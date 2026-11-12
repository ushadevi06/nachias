<?php

namespace App\Http\Controllers;

use App\Exports\FgStockTakingExport;
use App\Models\Brand;
use App\Models\ItemPrice;
use App\Models\StockTakingItem;
use App\Models\StockTakingScanLog;
use App\Models\StockTakingSession;
use App\Models\StoreLocation;
use App\Models\StoreType;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class FgStockTakingController extends Controller
{
    protected function authorizeAccess(): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Unauthorized');
        }
        if (
            auth()->id() != 1
            && !$user->can('view stock-entry-finished-goods')
            && !$user->can('view stock-entry-raw-materials')
        ) {
            abort(403, 'Unauthorized action.');
        }
    }

    public static function effectiveSkuSql(): string
    {
        return "TRIM(COALESCE(NULLIF(TRIM(sei.sku), ''), CONCAT('NOSKU-', COALESCE(NULLIF(TRIM(sei.art_no), ''), 'UNKNOWN'), '-', COALESCE(TRIM(sei.size), ''), '-', COALESCE(TRIM(sei.sleeve_type), ''))))";
    }

    /**
     * Parse plain SKU, pipe-delimited barcode, or JSON QR payload.
     */
    protected function parseCodePayload(string $rawCode): array
    {
        $raw = trim($rawCode);
        $code = $raw;
        $jsonArtNo = null;
        $jsonSize = null;

        if ($raw !== '' && str_starts_with($raw, '{')) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                foreach (['sku', 'SKU', 'barcode', 'code', 'finished_item_code'] as $key) {
                    if (!empty($decoded[$key]) && is_string($decoded[$key])) {
                        $code = trim($decoded[$key]);
                        break;
                    }
                }
                if (!empty($decoded['art_no'])) {
                    $jsonArtNo = trim((string) $decoded['art_no']);
                }
                if (!empty($decoded['size'])) {
                    $jsonSize = trim((string) $decoded['size']);
                }
            }
        } elseif (str_contains($raw, '|')) {
            $code = trim(explode('|', $raw)[0]);
        }

        return [
            'raw' => $raw,
            'code' => $code,
            'art_no' => $jsonArtNo,
            'size' => $jsonSize,
        ];
    }

    public static function baseFgQuery(?int $warehouseId = null, ?int $storeTypeId = null)
    {
        $query = DB::table('stock_entry_items as sei')
            ->leftJoin('stock_entries as se', 'se.id', '=', 'sei.stock_entry_id')
            ->where('sei.stock_type', 'finished_goods')
            ->whereNull('sei.deleted_at')
            ->where(function ($q) {
                $q->whereNull('sei.stock_entry_id')
                  ->orWhereNull('se.deleted_at');
            });

        if ($warehouseId || $storeTypeId) {
            $query->leftJoin('production_receipts as pr', 'pr.receipt_no', '=', 'se.reference_document');
        }

        if ($warehouseId) {
            $query->whereRaw('COALESCE(sei.warehouse_id, se.warehouse_id, pr.warehouse_id) = ?', [$warehouseId]);
        }

        if ($storeTypeId) {
            $query->whereRaw('COALESCE(sei.store_type_id, se.store_type_id, pr.store_type_id) = ?', [$storeTypeId]);
        }

        return $query;
    }

    protected function loadActivePriceMaps(): array
    {
        $prices = ItemPrice::whereNull('deleted_at')
            ->where('status', 'Active')
            ->orderByDesc('effective_from')
            ->orderByDesc('id')
            ->get(['art_no', 'size', 'selling_price', 'unit_price']);

        $byArtSize = [];
        $byArt = [];
        foreach ($prices as $p) {
            $val = floatval($p->selling_price ?: $p->unit_price ?: 0);
            if ($val <= 0 || !$p->art_no) {
                continue;
            }
            $artKey = strtoupper(trim((string) $p->art_no));
            $sizeKey = strtoupper(trim((string) $p->size));
            if ($sizeKey !== '' && !isset($byArtSize[$artKey . '|' . $sizeKey])) {
                $byArtSize[$artKey . '|' . $sizeKey] = $val;
            }
            if (!isset($byArt[$artKey])) {
                $byArt[$artKey] = $val;
            }
        }

        return [$byArtSize, $byArt];
    }

    protected function lookupRate(array $byArtSize, array $byArt, ?string $artNo, ?string $size, float $fallback = 0): float
    {
        $artKey = strtoupper(trim((string) $artNo));
        $sizeKey = strtoupper(trim((string) $size));
        if ($artKey !== '' && $sizeKey !== '' && isset($byArtSize[$artKey . '|' . $sizeKey])) {
            return $byArtSize[$artKey . '|' . $sizeKey];
        }
        if ($artKey !== '' && isset($byArt[$artKey])) {
            return $byArt[$artKey];
        }

        return $fallback > 0 ? $fallback : 0.0;
    }

    protected function resolveFgByCode(string $rawCode, ?int $warehouseId = null, ?int $brandFilterId = null, ?int $storeTypeId = null): array
    {
        $parsed = $this->parseCodePayload($rawCode);
        $code = $parsed['code'];
        if ($code === '') {
            return ['found' => false, 'parsed' => $parsed, 'matches' => []];
        }

        $effectiveSku = $this->effectiveSkuSql();
        $runMatchQuery = function (callable $filter) use ($warehouseId, $brandFilterId, $storeTypeId, $effectiveSku) {
            $q = $this->baseFgQuery($warehouseId, $storeTypeId)
                ->leftJoin('brands as b', 'b.id', '=', 'sei.brand_id')
                ->leftJoin('styles as st', 'st.id', '=', 'sei.style_id')
                ->leftJoin('warehouses as w', 'w.id', '=', DB::raw('COALESCE(sei.warehouse_id, se.warehouse_id)'))
                ->leftJoin('store_locations as sl', 'sl.id', '=', 'sei.store_location_id');

            if ($brandFilterId) {
                $q->where('sei.brand_id', $brandFilterId);
            }

            $filter($q);

            return $q->select(
                DB::raw("{$effectiveSku} as sku"),
                'sei.brand_id',
                DB::raw("COALESCE(b.brand_name, 'Unassigned') as brand_name"),
                'sei.style_id',
                DB::raw("COALESCE(st.style_name, '-') as style_name"),
                DB::raw("COALESCE(sei.art_no, '') as art_no"),
                DB::raw("COALESCE(sei.finished_item_code, '') as finished_item_code"),
                DB::raw("COALESCE(sei.size, '') as size"),
                DB::raw("COALESCE(sei.sleeve_type, '') as sleeve_type"),
                DB::raw("COALESCE(w.warehouse_name, 'Main / Unassigned') as warehouse_name"),
                DB::raw("COALESCE(sl.store_location, '-') as location_name"),
                DB::raw('COALESCE(NULLIF(sei.price, 0), 0) as entry_price'),
                DB::raw('(COALESCE(sei.qty_in, 0) - COALESCE(sei.qty_out, 0)) as net_qty')
            )->get();
        };

        $rows = $runMatchQuery(fn($q) => $q->where('sei.sku', $code));

        if ($rows->isEmpty()) {
            $rows = $runMatchQuery(fn($q) => $q->where('sei.barcode', $code));
        }

        if ($rows->isEmpty()) {
            $escaped = addcslashes($code, '%_\\');
            $rawEscaped = addcslashes($parsed['raw'], '%_\\');
            $rows = $runMatchQuery(function ($q) use ($parsed, $escaped, $rawEscaped) {
                $q->where(function ($sub) use ($parsed, $escaped, $rawEscaped) {
                    $sub->where('sei.qrcode', 'like', '%"sku":"' . $escaped . '"%')
                        ->orWhere('sei.qrcode', $parsed['raw'])
                        ->orWhere('sei.sku', $parsed['raw']);
                    if ($parsed['raw'] !== $parsed['code']) {
                        $sub->orWhere('sei.qrcode', 'like', '%' . $rawEscaped . '%');
                    }
                });
            });
        }

        if ($rows->isEmpty() && $parsed['art_no']) {
            $rows = $runMatchQuery(function ($q) use ($parsed) {
                $q->where('sei.art_no', $parsed['art_no']);
                if ($parsed['size']) {
                    $q->where('sei.size', $parsed['size']);
                }
            });
        }

        if ($rows->isEmpty()) {
            return ['found' => false, 'parsed' => $parsed, 'matches' => []];
        }

        [$byArtSize, $byArt] = $this->loadActivePriceMaps();

        $grouped = [];
        foreach ($rows as $row) {
            $key = ($row->brand_id ?? 0) . '||' . $row->sku;
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'sku' => $row->sku,
                    'brand_id' => $row->brand_id ? (int) $row->brand_id : null,
                    'brand_name' => $row->brand_name,
                    'style_id' => $row->style_id,
                    'style_name' => $row->style_name,
                    'art_no' => $row->art_no,
                    'finished_item_code' => $row->finished_item_code,
                    'size' => $row->size,
                    'sleeve_type' => $row->sleeve_type,
                    'rate' => $this->lookupRate($byArtSize, $byArt, $row->art_no, $row->size, floatval($row->entry_price)),
                    'total_qty' => 0.0,
                    'locations' => [],
                ];
            }
            $qty = floatval($row->net_qty);
            $grouped[$key]['total_qty'] += $qty;
            $locKey = $row->warehouse_name . '||' . $row->location_name;
            if (!isset($grouped[$key]['locations'][$locKey])) {
                $grouped[$key]['locations'][$locKey] = [
                    'warehouse' => $row->warehouse_name,
                    'location' => $row->location_name,
                    'qty' => 0.0,
                ];
            }
            $grouped[$key]['locations'][$locKey]['qty'] += $qty;
        }

        foreach ($grouped as &$g) {
            $g['total_qty'] = round($g['total_qty'], 2);
            $g['locations'] = array_values(array_filter(
                array_map(function ($loc) {
                    $loc['qty'] = round($loc['qty'], 2);
                    return $loc;
                }, $g['locations']),
                fn($loc) => abs($loc['qty']) > 0.0001
            ));
        }
        unset($g);

        $matches = array_values($grouped);
        usort($matches, fn($a, $b) => $b['total_qty'] <=> $a['total_qty']);

        return [
            'found' => true,
            'parsed' => $parsed,
            'matches' => $matches,
        ];
    }

    /**
     * Snapshot positive FG stock for a Brand (+ optional Warehouse & Store Type) into a new session.
     */
    protected function createBrandSessionSnapshot(int $brandId, ?int $warehouseId, ?int $storeTypeId = null): ?StockTakingSession
    {
        $effectiveSku = $this->effectiveSkuSql();
        $subQuery = $this->baseFgQuery($warehouseId, $storeTypeId)
            ->leftJoin('styles as st', 'st.id', '=', 'sei.style_id')
            ->where('sei.brand_id', $brandId)
            ->select(
                DB::raw("{$effectiveSku} as effective_sku"),
                'sei.style_id',
                DB::raw("COALESCE(st.style_name, '') as style_name"),
                DB::raw("COALESCE(sei.art_no, '') as art_no"),
                DB::raw("COALESCE(sei.finished_item_code, '') as finished_item_code"),
                DB::raw("COALESCE(sei.size, '') as size"),
                DB::raw("COALESCE(sei.sleeve_type, '') as sleeve_type"),
                'sei.store_location_id',
                DB::raw('NULLIF(sei.price, 0) as entry_price'),
                DB::raw('(COALESCE(sei.qty_in, 0) - COALESCE(sei.qty_out, 0)) as net_delta')
            );

        $rows = DB::query()
            ->fromSub($subQuery, 'fg')
            ->select(
                'fg.effective_sku',
                DB::raw('MAX(fg.style_id) as style_id'),
                DB::raw("COALESCE(MAX(fg.style_name), '') as style_name"),
                DB::raw("COALESCE(MAX(fg.art_no), '') as art_no"),
                DB::raw("COALESCE(MAX(fg.finished_item_code), '') as finished_item_code"),
                DB::raw("COALESCE(MAX(fg.size), '') as size"),
                DB::raw("COALESCE(MAX(fg.sleeve_type), '') as sleeve_type"),
                DB::raw('MAX(CASE WHEN fg.net_delta > 0 THEN fg.store_location_id END) as system_store_location_id'),
                DB::raw('MAX(fg.entry_price) as entry_price'),
                DB::raw('SUM(fg.net_delta) as net_qty')
            )
            ->groupBy('fg.effective_sku')
            ->havingRaw('SUM(fg.net_delta) > 0.0001')
            ->orderBy('style_name')
            ->orderBy('art_no')
            ->orderBy('size')
            ->get();

        if ($rows->isEmpty()) {
            return null;
        }

        [$byArtSize, $byArt] = $this->loadActivePriceMaps();

        return DB::transaction(function () use ($brandId, $warehouseId, $storeTypeId, $rows, $byArtSize, $byArt) {
            $todayPrefix = 'ST-' . date('Ymd') . '-';
            $maxSeq = (int) StockTakingSession::withTrashed()
                ->where('session_no', 'like', $todayPrefix . '%')
                ->lockForUpdate()
                ->pluck('session_no')
                ->map(function ($no) {
                    $parts = explode('-', (string) $no);
                    return (int) end($parts);
                })
                ->max();

            $nextSeq = $maxSeq + 1;
            $sessionNo = $todayPrefix . str_pad((string) $nextSeq, 3, '0', STR_PAD_LEFT);

            $totalQty = 0.0;
            foreach ($rows as $r) {
                $totalQty += round(floatval($r->net_qty), 2);
            }

            $session = StockTakingSession::create([
                'session_no' => $sessionNo,
                'brand_id' => $brandId,
                'warehouse_id' => $warehouseId,
                'store_type_id' => $storeTypeId,
                'is_blind' => false,
                'status' => 'In Progress',
                'total_system_skus' => $rows->count(),
                'total_system_qty' => round($totalQty, 2),
                'total_scanned_qty' => 0,
                'started_by' => auth()->id(),
                'started_at' => now(),
            ]);

            $now = now();
            $insertBatch = [];
            foreach ($rows as $r) {
                $rate = $this->lookupRate($byArtSize, $byArt, $r->art_no, $r->size, floatval($r->entry_price));
                $insertBatch[] = [
                    'session_id' => $session->id,
                    'sku' => $r->effective_sku,
                    'style_id' => $r->style_id ? (int) $r->style_id : null,
                    'style_name' => $r->style_name ?: null,
                    'art_no' => $r->art_no ?: null,
                    'finished_item_code' => $r->finished_item_code ?: null,
                    'size' => $r->size ?: null,
                    'sleeve_type' => $r->sleeve_type ?: null,
                    'system_store_location_id' => $r->system_store_location_id ? (int) $r->system_store_location_id : null,
                    'store_location_id' => null,
                    'system_qty' => round(floatval($r->net_qty), 2),
                    'scanned_qty' => 0,
                    'rate' => round($rate, 2),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            foreach (array_chunk($insertBatch, 500) as $chunk) {
                StockTakingItem::insert($chunk);
            }

            return $session;
        });
    }

    /**
     * Net live FG movements (qty_in - qty_out) since session snapshot started.
     */
    public static function getSessionMovementMap(StockTakingSession $session): array
    {
        if (!$session->started_at || !$session->brand_id) {
            return [];
        }

        $endAt = $session->completed_at ?? now();
        $effectiveSku = self::effectiveSkuSql();

        $subQuery = self::baseFgQuery(
            $session->warehouse_id ? (int) $session->warehouse_id : null,
            $session->store_type_id ? (int) $session->store_type_id : null
        )
            ->where('sei.brand_id', (int) $session->brand_id)
            ->where('sei.created_at', '>', $session->started_at)
            ->where('sei.created_at', '<=', $endAt)
            ->select(
                DB::raw("{$effectiveSku} as effective_sku"),
                DB::raw('(COALESCE(sei.qty_in, 0) - COALESCE(sei.qty_out, 0)) as net_delta')
            );

        $rows = DB::query()
            ->fromSub($subQuery, 'mv')
            ->select('mv.effective_sku', DB::raw('SUM(mv.net_delta) as net_move'))
            ->groupBy('mv.effective_sku')
            ->get();

        $map = [];
        foreach ($rows as $row) {
            $delta = round(floatval($row->net_move), 2);
            if (abs($delta) > 0.0001) {
                $map[(string) $row->effective_sku] = $delta;
            }
        }

        return $map;
    }

    /**
     * Classify a single stock-taking item after accounting for concurrent stock movements.
     */
    public static function classifyItemQuantities(float $snapshotSys, float $movementQty, float $scannedQty, float $rate = 0): array
    {
        $snapshotSys = round($snapshotSys, 2);
        $movementQty = round($movementQty, 2);
        $scannedQty = round($scannedQty, 2);
        $effectiveSys = max(0.0, round($snapshotSys + $movementQty, 2));
        $remaining = max(0.0, round($effectiveSys - $scannedQty, 2));
        $variance = round($scannedQty - $effectiveSys, 2);
        $matchedQty = min($scannedQty, $effectiveSys);

        if ($effectiveSys > 0.0001 && abs($variance) < 0.001) {
            $status = 'completed';
        } elseif ($variance > 0.001) {
            $status = 'excess';
        } elseif ($effectiveSys > 0.0001 && $scannedQty <= 0.0001) {
            $status = 'pending';
        } elseif ($effectiveSys > 0.0001 && $scannedQty < $effectiveSys - 0.001) {
            $status = 'in_progress';
        } else {
            $status = 'completed';
        }

        return [
            'snapshot_qty' => $snapshotSys,
            'movement_qty' => $movementQty,
            'system_qty' => $effectiveSys,
            'scanned_qty' => $scannedQty,
            'remaining_qty' => $remaining,
            'matched_qty' => round($matchedQty, 2),
            'variance' => $variance,
            'variance_value' => round($variance * $rate, 2),
            'status' => $status,
        ];
    }

    /**
     * Single shared state calculator for items, summary stats, remaining qty, and capped coverage %.
     */
    public static function computeSessionItemsState(StockTakingSession $session): array
    {
        $items = $session->relationLoaded('items')
            ? $session->items
            : StockTakingItem::where('session_id', $session->id)->get();

        $movementMap = self::getSessionMovementMap($session);

        $computed = [];
        $sumSnapshot = 0.0;
        $sumMovement = 0.0;
        $sumSys = 0.0;
        $sumScan = 0.0;
        $sumMatchedQty = 0.0;
        $sumRemaining = 0.0;
        $sumVarianceVal = 0.0;

        $matchedSkus = 0;
        $pendingSkus = 0;
        $inProgressSkus = 0;
        $shortageSkus = 0;
        $shortageQty = 0.0;
        $excessSkus = 0;
        $excessQty = 0.0;
        $touchedSkus = 0;

        foreach ($items as $it) {
            $mv = floatval($movementMap[$it->sku] ?? 0);
            $rate = floatval($it->rate);
            $q = self::classifyItemQuantities(floatval($it->system_qty), $mv, floatval($it->scanned_qty), $rate);

            $sumSnapshot += $q['snapshot_qty'];
            $sumMovement += $q['movement_qty'];
            $sumSys += $q['system_qty'];
            $sumScan += $q['scanned_qty'];
            $sumMatchedQty += $q['matched_qty'];
            $sumRemaining += $q['remaining_qty'];
            $sumVarianceVal += $q['variance_value'];

            if ($q['scanned_qty'] > 0.0001) {
                $touchedSkus++;
            }
            if ($q['status'] === 'completed') {
                $matchedSkus++;
            } elseif ($q['status'] === 'pending') {
                $pendingSkus++;
                $shortageSkus++;
                $shortageQty += $q['remaining_qty'];
            } elseif ($q['status'] === 'in_progress') {
                $inProgressSkus++;
                $shortageSkus++;
                $shortageQty += $q['remaining_qty'];
            } elseif ($q['status'] === 'excess') {
                $excessSkus++;
                $excessQty += $q['variance'];
            }

            $computed[] = [
                'model' => $it,
                'calc' => $q,
            ];
        }

        $pct = $sumSys > 0.0001 ? min(100.0, round(($sumMatchedQty / $sumSys) * 100, 1)) : 0.0;

        return [
            'rows' => $computed,
            'movement_map' => $movementMap,
            'stats' => [
                'total_skus' => count($computed),
                'touched_skus' => $touchedSkus,
                'total_snapshot_qty' => round($sumSnapshot, 2),
                'total_movement_qty' => round($sumMovement, 2),
                'total_system_qty' => round($sumSys, 2),
                'total_scanned_qty' => round($sumScan, 2),
                'total_matched_qty' => round($sumMatchedQty, 2),
                'remaining_qty' => round($sumRemaining, 2),
                'variance_qty' => round($sumScan - $sumSys, 2),
                'variance_value' => round($sumVarianceVal, 2),
                'matched_skus' => $matchedSkus,
                'pending_skus' => $pendingSkus,
                'in_progress_skus' => $inProgressSkus,
                'shortage_skus' => $shortageSkus,
                'shortage_qty' => round($shortageQty, 2),
                'excess_skus' => $excessSkus,
                'excess_qty' => round($excessQty, 2),
                'coverage_pct' => $pct,
            ],
        ];
    }

    protected function formatSessionMeta(StockTakingSession $session): array
    {
        $session->loadMissing(['brand', 'storeType', 'warehouse', 'starter', 'completer']);

        return [
            'session_id' => $session->id,
            'session_no' => $session->session_no,
            'brand_name' => $session->brand?->brand_name ?? '-',
            'store_type_name' => $session->storeType?->store_type_name ?? 'All Stores',
            'warehouse_name' => $session->warehouse?->warehouse_name ?? 'All Warehouses',
            'status' => $session->status,
            'is_finalized' => $session->status !== 'In Progress',
            'started_date' => $session->started_at ? $session->started_at->format('d-m-Y') : '-',
            'started_at_display' => $session->started_at ? $session->started_at->format('d-m-Y h:i A') : '-',
            'counted_by' => $session->starter?->name ?? '-',
            'completed_at_display' => $session->completed_at ? $session->completed_at->format('d-m-Y h:i A') : null,
            'completed_by_name' => $session->completer?->name ?? null,
            'resumed_from_previous_day' => (bool) ($session->started_at && $session->started_at->lt(today()) && $session->status === 'In Progress'),
        ];
    }

    protected function buildSummaryStats(StockTakingSession $session): array
    {
        $state = self::computeSessionItemsState($session);

        $logCounts = DB::table('stock_taking_scan_logs')
            ->where('session_id', $session->id)
            ->selectRaw('COUNT(*) as total_logs')
            ->selectRaw("SUM(CASE WHEN scan_status IN ('wrong_brand', 'zero_stock', 'not_found', 'duplicate') THEN 1 ELSE 0 END) as alert_logs")
            ->selectRaw("SUM(CASE WHEN scan_status = 'wrong_brand' THEN 1 ELSE 0 END) as wrong_brand_logs")
            ->selectRaw("SUM(CASE WHEN scan_status = 'duplicate' THEN 1 ELSE 0 END) as duplicate_logs")
            ->first();

        return array_merge($state['stats'], [
            'total_logs' => (int) ($logCounts->total_logs ?? 0),
            'alert_logs' => (int) ($logCounts->alert_logs ?? 0),
            'wrong_brand_logs' => (int) ($logCounts->wrong_brand_logs ?? 0),
            'duplicate_logs' => (int) ($logCounts->duplicate_logs ?? 0),
        ]);
    }

    public function index(Request $request)
    {
        $this->authorizeAccess();

        $effectiveSku = $this->effectiveSkuSql();
        $subQuery = DB::table('stock_entry_items as sei')
            ->leftJoin('stock_entries as se', 'se.id', '=', 'sei.stock_entry_id')
            ->where('sei.stock_type', 'finished_goods')
            ->whereNull('sei.deleted_at')
            ->whereNotNull('sei.brand_id')
            ->where(function ($q) {
                $q->whereNull('sei.stock_entry_id')
                  ->orWhereNull('se.deleted_at');
            })
            ->select(
                'sei.brand_id',
                DB::raw("{$effectiveSku} as effective_sku"),
                DB::raw('(COALESCE(sei.qty_in, 0) - COALESCE(sei.qty_out, 0)) as net_delta')
            );

        $brandStatsRows = DB::query()
            ->fromSub($subQuery, 'fg')
            ->select('fg.brand_id', 'fg.effective_sku', DB::raw('SUM(fg.net_delta) as net_qty'))
            ->groupBy('fg.brand_id', 'fg.effective_sku')
            ->havingRaw('SUM(fg.net_delta) > 0.0001')
            ->get();

        $brandSummary = [];
        foreach ($brandStatsRows as $r) {
            $bid = (int) $r->brand_id;
            if (!isset($brandSummary[$bid])) {
                $brandSummary[$bid] = ['skus' => 0, 'qty' => 0.0];
            }
            $brandSummary[$bid]['skus']++;
            $brandSummary[$bid]['qty'] += floatval($r->net_qty);
        }

        $selectedBrandId = $request->filled('brand_id') ? (int) $request->input('brand_id') : null;
        $selectedStoreTypeId = $request->filled('store_type_id') ? (int) $request->input('store_type_id') : null;
        $selectedWarehouseId = $request->filled('warehouse_id') ? (int) $request->input('warehouse_id') : null;
        $selectedStoreLocationId = $request->filled('store_location_id') ? (int) $request->input('store_location_id') : null;
        $selectedSessionId = $request->filled('session_id') ? (int) $request->input('session_id') : null;

        $allowedBrandIds = array_keys($brandSummary);
        if ($selectedSessionId) {
            $sessionBrandId = StockTakingSession::where('id', $selectedSessionId)->value('brand_id');
            if ($sessionBrandId) {
                $allowedBrandIds[] = (int) $sessionBrandId;
            }
        }
        if ($selectedBrandId) {
            $allowedBrandIds[] = $selectedBrandId;
        }
        $allowedBrandIds = array_values(array_unique(array_filter($allowedBrandIds)));

        $brands = Brand::whereNull('deleted_at')
            ->where('status', 'Active')
            ->whereIn('id', $allowedBrandIds ?: [0])
            ->orderBy('brand_name')
            ->get()
            ->map(function ($b) use ($brandSummary) {
                $stat = $brandSummary[$b->id] ?? ['skus' => 0, 'qty' => 0.0];
                $b->fg_sku_count = $stat['skus'];
                $b->fg_total_qty = round($stat['qty'], 2);
                return $b;
            });

        $storeTypes = StoreType::whereNull('deleted_at')
            ->where('status', 'Active')
            ->orderBy('store_type_name')
            ->get();

        $warehouses = Warehouse::whereNull('deleted_at')
            ->where('status', 'Active')
            ->orderBy('warehouse_name')
            ->get();

        $storeLocations = StoreLocation::whereNull('deleted_at')
            ->where('status', 'Active')
            ->orderBy('store_location')
            ->get();

        $logsCount = StockTakingSession::count();

        return view('stock_taking.index', compact(
            'brands',
            'storeTypes',
            'warehouses',
            'storeLocations',
            'selectedBrandId',
            'selectedStoreTypeId',
            'selectedWarehouseId',
            'selectedStoreLocationId',
            'selectedSessionId',
            'logsCount'
        ));
    }

    /**
     * Dedicated page showing previous and in-progress stock-taking session logs.
     */
    public function logs()
    {
        $this->authorizeAccess();

        return view('stock_taking.logs');
    }

    /**
     * Find latest unfinished ("In Progress") session across dates for Brand (+ optional Store & Warehouse), or initialize a new one.
     */
    public function loadBrandSession(Request $request)
    {
        $this->authorizeAccess();

        $brandId = (int) $request->input('brand_id');
        $storeTypeId = $request->filled('store_type_id') ? (int) $request->input('store_type_id') : null;
        $warehouseId = $request->filled('warehouse_id') ? (int) $request->input('warehouse_id') : null;
        $forceNew = $request->boolean('force_new', false);

        if (!$brandId) {
            return response()->json(['success' => false, 'message' => 'Please select a Brand.'], 422);
        }

        $session = null;
        if (!$forceNew) {
            $query = StockTakingSession::with(['brand', 'storeType', 'warehouse', 'starter', 'completer'])
                ->where('brand_id', $brandId)
                ->where('status', 'In Progress')
                ->orderByDesc('id');

            if ($storeTypeId) {
                $query->where('store_type_id', $storeTypeId);
            } else {
                $query->whereNull('store_type_id');
            }

            if ($warehouseId) {
                $query->where('warehouse_id', $warehouseId);
            } else {
                $query->whereNull('warehouse_id');
            }

            $session = $query->first();
        }

        $isNew = false;

        if (!$session) {
            $session = $this->createBrandSessionSnapshot($brandId, $warehouseId, $storeTypeId);
            if (!$session) {
                return response()->json([
                    'success' => false,
                    'empty_stock' => true,
                    'message' => 'Selected Brand / Store / Warehouse has no positive Finished Goods stock.',
                ], 404);
            }
            $isNew = true;
        }

        $meta = $this->formatSessionMeta($session);

        return response()->json([
            'success' => true,
            'is_new' => $isNew,
            'session_id' => $session->id,
            'session_no' => $session->session_no,
            'brand_name' => $meta['brand_name'],
            'store_type_name' => $meta['store_type_name'],
            'session' => $meta,
            'stats' => $this->buildSummaryStats($session),
        ]);
    }

    /**
     * Paginated list of previous and in-progress stock-taking sessions (supports both DataTables and JSON clients).
     */
    public function sessionsList(Request $request)
    {
        $this->authorizeAccess();

        $status = trim((string) $request->input('status', 'all'));
        $rawSearch = $request->input('search');
        $search = trim((string) (is_array($rawSearch) ? ($rawSearch['value'] ?? '') : $rawSearch));

        $baseQuery = StockTakingSession::with(['brand', 'storeType', 'warehouse', 'starter', 'completer']);
        $recordsTotal = StockTakingSession::count();

        if ($status === 'in_progress') {
            $baseQuery->where('status', 'In Progress');
        } elseif ($status === 'finalized' || $status === 'completed') {
            $baseQuery->where('status', '!=', 'In Progress');
        }

        if ($search !== '') {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('session_no', 'like', "%{$search}%")
                    ->orWhereHas('brand', fn($b) => $b->where('brand_name', 'like', "%{$search}%"))
                    ->orWhereHas('storeType', fn($st) => $st->where('store_type_name', 'like', "%{$search}%"))
                    ->orWhereHas('warehouse', fn($w) => $w->where('warehouse_name', 'like', "%{$search}%"))
                    ->orWhereHas('starter', fn($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('completer', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $formatRow = function ($s, int $rowIndex) {
            $sysQty = round(floatval($s->total_system_qty), 2);
            $scanQty = round(floatval($s->total_scanned_qty), 2);
            $varQty = round($scanQty - $sysQty, 2);
            $isFinalized = $s->status !== 'In Progress';
            $brandName = $s->brand?->brand_name ?? '-';
            $storeTypeName = $s->storeType?->store_type_name ?? 'All Stores';
            $warehouseName = $s->warehouse?->warehouse_name ?? 'All Warehouses';
            $countedBy = $s->starter?->name ?? '-';
            $finalizedBy = $s->completer?->name ?? '-';
            $completedAt = $s->completed_at ? $s->completed_at->format('d-m-Y h:i A') : '-';
            $exportUrl = url('fg_stock_taking/session/' . $s->id . '/export');
            $viewUrl = url('fg_stock_taking?session_id=' . $s->id);

            return [
                'DT_RowIndex' => $rowIndex,
                'id' => $s->id,
                'session_no' => $s->session_no,
                'brand_id' => $s->brand_id,
                'brand_name' => $brandName,
                'store_type_id' => $s->store_type_id,
                'store_type_name' => $storeTypeName,
                'warehouse_id' => $s->warehouse_id,
                'warehouse_name' => $warehouseName,
                'stock_taking_date' => $s->started_at ? $s->started_at->format('d-m-Y') : '-',
                'counted_by' => $countedBy,
                'finalized_by' => $finalizedBy,
                'completed_at' => $completedAt,
                'status' => $isFinalized ? 'Finalized' : 'In Progress',
                'raw_status' => $s->status,
                'total_system_skus' => (int) $s->total_system_skus,
                'total_system_qty' => $sysQty,
                'total_scanned_qty' => $scanQty,
                'variance_qty' => $varQty,
                'view_url' => $viewUrl,
                'export_url' => $exportUrl,
            ];
        };

        if ($request->has('draw')) {
            $recordsFiltered = (clone $baseQuery)->count();

            $sortableColumns = [
                1 => 'session_no',
                2 => 'started_at',
                7 => 'status',
            ];
            $orderColIdx = (int) data_get($request->input('order'), '0.column', -1);
            $orderDir = strtolower((string) data_get($request->input('order'), '0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            if (isset($sortableColumns[$orderColIdx])) {
                $baseQuery->orderBy($sortableColumns[$orderColIdx], $orderDir);
            } else {
                $baseQuery->orderByDesc('id');
            }

            $start = max(0, (int) $request->input('start', 0));
            $length = (int) $request->input('length', 10);
            if ($length <= 0) {
                $length = 1000;
            } else {
                $length = min(200, $length);
            }

            $items = $baseQuery->offset($start)->limit($length)->get();
            $data = $items->values()->map(fn($s, $idx) => $formatRow($s, $start + $idx + 1))->values();

            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);
        }

        $perPage = min(50, max(5, (int) $request->input('per_page', 10)));
        $page = max(1, (int) $request->input('page', 1));
        $paginator = $baseQuery->orderByDesc('id')->paginate($perPage, ['*'], 'page', $page);
        $offset = ($paginator->currentPage() - 1) * $paginator->perPage();
        $rows = $paginator->getCollection()->values()->map(fn($s, $idx) => $formatRow($s, $offset + $idx + 1))->values();

        return response()->json([
            'success' => true,
            'rows' => $rows,
            'pagination' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem() ?? 0,
                'to' => $paginator->lastItem() ?? 0,
            ],
        ]);
    }

    /**
     * Load any stock-taking session (In Progress or Finalized) by ID for viewing/exporting.
     */
    public function showSession($id)
    {
        $this->authorizeAccess();

        $session = StockTakingSession::with(['brand', 'storeType', 'warehouse', 'starter', 'completer'])->find($id);
        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Stock taking session not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'session_id' => $session->id,
            'session_no' => $session->session_no,
            'brand_id' => $session->brand_id,
            'store_type_id' => $session->store_type_id,
            'warehouse_id' => $session->warehouse_id,
            'session' => $this->formatSessionMeta($session),
            'stats' => $this->buildSummaryStats($session),
        ]);
    }

    /**
     * Finalize/complete a stock taking session so further barcode scans are prevented.
     */
    public function finalize(Request $request, $id)
    {
        $this->authorizeAccess();

        $res = DB::transaction(function () use ($id) {
            $session = StockTakingSession::with(['brand', 'storeType', 'warehouse', 'starter', 'completer'])->lockForUpdate()->find($id);
            if (!$session) {
                return ['missing' => true];
            }
            if ($session->status !== 'In Progress') {
                return ['already_finalized' => true, 'session' => $session];
            }

            $session->status = 'Completed';
            $session->completed_by = auth()->id();
            $session->completed_at = now();
            $session->save();
            $session->load(['brand', 'storeType', 'warehouse', 'starter', 'completer']);

            return ['success' => true, 'session' => $session];
        });

        if (!empty($res['missing'])) {
            return response()->json([
                'success' => false,
                'session_expired' => true,
                'message' => 'Stock taking session not found.',
            ], 404);
        }

        $session = $res['session'];
        $alreadyFinalized = !empty($res['already_finalized']);

        return response()->json([
            'success' => true,
            'already_finalized' => $alreadyFinalized,
            'message' => $alreadyFinalized
                ? "Stock taking session {$session->session_no} is already finalized."
                : "Stock taking session {$session->session_no} has been finalized. Scanning is now locked.",
            'session' => $this->formatSessionMeta($session),
            'stats' => $this->buildSummaryStats($session),
        ]);
    }

    public function resetSession(Request $request, $id)
    {
        $this->authorizeAccess();

        $old = StockTakingSession::findOrFail($id);
        $brandId = (int) $old->brand_id;
        $warehouseId = $old->warehouse_id ? (int) $old->warehouse_id : null;
        $storeTypeId = $old->store_type_id ? (int) $old->store_type_id : null;

        DB::transaction(function () use ($old) {
            StockTakingScanLog::where('session_id', $old->id)->delete();
            StockTakingItem::where('session_id', $old->id)->delete();
            $old->forceDelete();
        });

        $fresh = $this->createBrandSessionSnapshot($brandId, $warehouseId, $storeTypeId);
        if (!$fresh) {
            return response()->json(['success' => false, 'message' => 'No positive stock found for this brand.'], 404);
        }

        return response()->json([
            'success' => true,
            'session_id' => $fresh->id,
            'session_no' => $fresh->session_no,
            'stats' => $this->buildSummaryStats($fresh),
            'message' => 'Count cleared and reloaded with latest live FG stock.',
        ]);
    }

    public function searchItems(Request $request)
    {
        $this->authorizeAccess();

        $q = trim((string) $request->input('q', ''));
        if ($q === '') {
            return response()->json(['success' => true, 'items' => []]);
        }

        $escaped = addcslashes($q, '%_\\');
        $like = "%{$escaped}%";
        $prefix = "{$escaped}%";

        $sessionId = $request->filled('session_id') ? (int) $request->input('session_id') : null;
        $brandId = $request->filled('brand_id') ? (int) $request->input('brand_id') : null;
        $storeTypeId = $request->filled('store_type_id') ? (int) $request->input('store_type_id') : null;
        $warehouseId = $request->filled('warehouse_id') ? (int) $request->input('warehouse_id') : null;

        if ($sessionId) {
            $session = StockTakingSession::with('brand')->find($sessionId);
            if ($session) {
                $sessionItems = StockTakingItem::with('storeLocation')
                    ->where('session_id', $session->id)
                    ->where(function ($sub) use ($like) {
                        $sub->where('sku', 'like', $like)
                            ->orWhere('art_no', 'like', $like)
                            ->orWhere('style_name', 'like', $like)
                            ->orWhere('size', 'like', $like);
                    })
                    ->orderByRaw(
                        'CASE WHEN sku LIKE ? THEN 0 WHEN art_no LIKE ? THEN 1 ELSE 2 END',
                        [$prefix, $prefix]
                    )
                    ->orderBy('art_no')
                    ->orderBy('size')
                    ->orderBy('sku')
                    ->limit(15)
                    ->get();

                if ($sessionItems->isNotEmpty()) {
                    $movementMap = self::getSessionMovementMap($session);
                    $brandName = $session->brand?->brand_name ?? '-';
                    $items = $sessionItems->map(function ($it) use ($movementMap, $brandName) {
                        $mv = floatval($movementMap[$it->sku] ?? 0);
                        $calc = self::classifyItemQuantities(floatval($it->system_qty), $mv, floatval($it->scanned_qty), floatval($it->rate));
                        return [
                            'sku' => $it->sku,
                            'art_no' => $it->art_no ?: '-',
                            'style_name' => $it->style_name ?: '-',
                            'size' => $it->size ?: '-',
                            'sleeve_type' => $it->sleeve_type ?: '-',
                            'brand_name' => $brandName,
                            'system_qty' => $calc['system_qty'],
                            'scanned_qty' => $calc['scanned_qty'],
                            'remaining_qty' => $calc['remaining_qty'],
                            'status' => $calc['status'],
                            'store_location' => $it->storeLocation?->store_location,
                            'in_session' => true,
                        ];
                    })->values();

                    return response()->json(['success' => true, 'mode' => 'session', 'items' => $items]);
                }

                $brandId = $session->brand_id;
                $storeTypeId = $session->store_type_id;
                $warehouseId = $session->warehouse_id;
            }
        }

        $query = self::baseFgQuery($warehouseId, $storeTypeId)
            ->leftJoin('brands as b', 'b.id', '=', 'sei.brand_id')
            ->leftJoin('styles as st', 'st.id', '=', 'sei.style_id')
            ->whereNotNull('sei.sku')
            ->where('sei.sku', '!=', '')
            ->where(function ($sub) use ($like) {
                $sub->where('sei.sku', 'like', $like)
                    ->orWhere('sei.art_no', 'like', $like)
                    ->orWhere('sei.finished_item_code', 'like', $like)
                    ->orWhere('st.style_name', 'like', $like);
            });

        if ($brandId) {
            $query->where('sei.brand_id', $brandId);
        }

        $fgRows = $query->select(
                'sei.sku',
                DB::raw('MAX(sei.art_no) as art_no'),
                DB::raw('MAX(st.style_name) as style_name'),
                DB::raw('MAX(sei.size) as size'),
                DB::raw('MAX(sei.sleeve_type) as sleeve_type'),
                DB::raw('MAX(b.brand_name) as brand_name'),
                DB::raw('SUM(COALESCE(sei.qty_in, 0) - COALESCE(sei.qty_out, 0)) as net_qty')
            )
            ->groupBy('sei.sku')
            ->orderByRaw(
                'CASE WHEN sei.sku LIKE ? THEN 0 WHEN MAX(sei.art_no) LIKE ? THEN 1 ELSE 2 END',
                [$prefix, $prefix]
            )
            ->orderBy(DB::raw('MAX(sei.art_no)'))
            ->orderBy(DB::raw('MAX(sei.size)'))
            ->limit(15)
            ->get();

        $items = $fgRows->map(fn($r) => [
            'sku' => $r->sku,
            'art_no' => $r->art_no ?: '-',
            'style_name' => $r->style_name ?: '-',
            'size' => $r->size ?: '-',
            'sleeve_type' => $r->sleeve_type ?: '-',
            'brand_name' => $r->brand_name ?: '-',
            'system_qty' => round(max(0, floatval($r->net_qty)), 2),
            'scanned_qty' => 0.0,
            'remaining_qty' => round(max(0, floatval($r->net_qty)), 2),
            'status' => floatval($r->net_qty) > 0.0001 ? 'pending' : 'zero_stock',
            'store_location' => null,
            'in_session' => false,
        ])->values();

        return response()->json(['success' => true, 'mode' => 'catalog', 'items' => $items]);
    }

    public function quickCheck(Request $request)
    {
        $this->authorizeAccess();

        $rawCode = (string) $request->input('code', '');
        $brandId = $request->filled('brand_id') ? (int) $request->input('brand_id') : null;
        $storeTypeId = $request->filled('store_type_id') ? (int) $request->input('store_type_id') : null;
        $warehouseId = $request->filled('warehouse_id') ? (int) $request->input('warehouse_id') : null;

        if (trim($rawCode) === '') {
            return response()->json(['success' => false, 'status' => 'empty', 'message' => 'Please enter or scan a barcode / SKU.']);
        }

        $result = $this->resolveFgByCode($rawCode, $warehouseId, $brandId, $storeTypeId);
        $wrongBrandWarning = false;

        if (!$result['found'] && $brandId) {
            $fallback = $this->resolveFgByCode($rawCode, $warehouseId, null, $storeTypeId);
            if ($fallback['found']) {
                $result = $fallback;
                $wrongBrandWarning = true;
            }
        }

        if (!$result['found']) {
            return response()->json([
                'success' => false,
                'status' => 'not_found',
                'scanned_code' => $result['parsed']['code'],
                'message' => 'Barcode "' . $result['parsed']['code'] . '" not found in Finished Goods stock.',
            ]);
        }

        $primary = $result['matches'][0];
        $hasPositive = $primary['total_qty'] > 0.0001;

        return response()->json([
            'success' => true,
            'status' => $wrongBrandWarning ? 'wrong_brand' : ($hasPositive ? 'in_stock' : 'zero_stock'),
            'wrong_brand' => $wrongBrandWarning,
            'scanned_code' => $result['parsed']['code'],
            'item' => $primary,
            'other_matches' => array_slice($result['matches'], 1),
        ]);
    }

    public function scan(Request $request, $id)
    {
        $this->authorizeAccess();

        $rawBarcode = (string) $request->input('barcode', '');
        $qty = floatval($request->input('qty', 1));
        if ($qty <= 0) {
            $qty = 1;
        }
        $allowDuplicate = $request->boolean('allow_duplicate', false);
        $requestedLocationId = $request->filled('store_location_id') ? (int) $request->input('store_location_id') : null;
        $scannedLocation = $requestedLocationId
            ? StoreLocation::whereNull('deleted_at')->find($requestedLocationId)
            : null;
        $scannedLocationId = $scannedLocation?->id;
        $scannedLocationName = $scannedLocation?->store_location;

        if (trim($rawBarcode) === '') {
            return response()->json(['success' => false, 'scan_status' => 'empty', 'message' => 'Please enter or scan a barcode / SKU.'], 422);
        }

        $parsed = $this->parseCodePayload($rawBarcode);
        $candidateCode = $parsed['code'];

        $result = DB::transaction(function () use ($id, $parsed, $candidateCode, $rawBarcode, $qty, $allowDuplicate, $scannedLocationId, $scannedLocationName) {
            $session = StockTakingSession::with(['brand', 'warehouse', 'starter', 'completer'])->lockForUpdate()->find($id);
            if (!$session) {
                return ['session_expired' => true];
            }

            if ($session->status !== 'In Progress') {
                return [
                    'finalized' => true,
                    'session' => $session,
                ];
            }

            $sessionItem = StockTakingItem::where('session_id', $session->id)
                ->where('sku', $candidateCode)
                ->lockForUpdate()
                ->first();

            $resolvedInfo = null;
            if (!$sessionItem) {
                $resolvedInfo = $this->resolveFgByCode(
                    $rawBarcode,
                    $session->warehouse_id ? (int) $session->warehouse_id : null,
                    null,
                    $session->store_type_id ? (int) $session->store_type_id : null
                );
                if ($resolvedInfo['found']) {
                    foreach ($resolvedInfo['matches'] as $match) {
                        if ((int) $match['brand_id'] === (int) $session->brand_id) {
                            $tryItem = StockTakingItem::where('session_id', $session->id)
                                ->where('sku', $match['sku'])
                                ->lockForUpdate()
                                ->first();
                            if ($tryItem) {
                                $sessionItem = $tryItem;
                                break;
                            }
                        }
                    }
                }
            }

            $isZeroSystemStockItem = false;

            if (!$sessionItem) {
                if (!$resolvedInfo || !$resolvedInfo['found']) {
                    $resolvedInfo = $this->resolveFgByCode($rawBarcode, null);
                }

                if ($resolvedInfo['found']) {
                    $sameBrandMatch = null;
                    foreach ($resolvedInfo['matches'] as $m) {
                        if ((int) ($m['brand_id'] ?? 0) === (int) $session->brand_id) {
                            $sameBrandMatch = $m;
                            break;
                        }
                    }

                    if ($sameBrandMatch) {
                        // Zero-system-stock (or un-snapshotted same-brand FG) found physically: include in session items with system_qty = 0
                        $sessionItem = StockTakingItem::where('session_id', $session->id)
                            ->where('sku', $sameBrandMatch['sku'])
                            ->lockForUpdate()
                            ->first();

                        if (!$sessionItem) {
                            $sessionItem = StockTakingItem::create([
                                'session_id' => $session->id,
                                'sku' => $sameBrandMatch['sku'],
                                'style_id' => !empty($sameBrandMatch['style_id']) ? (int) $sameBrandMatch['style_id'] : null,
                                'style_name' => $sameBrandMatch['style_name'] ?: null,
                                'art_no' => $sameBrandMatch['art_no'] ?: null,
                                'finished_item_code' => $sameBrandMatch['finished_item_code'] ?: null,
                                'size' => $sameBrandMatch['size'] ?: null,
                                'sleeve_type' => $sameBrandMatch['sleeve_type'] ?: null,
                                'system_qty' => 0,
                                'scanned_qty' => 0,
                                'rate' => round(floatval($sameBrandMatch['rate'] ?? 0), 2),
                            ]);
                        }
                        $isZeroSystemStockItem = floatval($sessionItem->system_qty) <= 0.0001;
                    } else {
                        $match = $resolvedInfo['matches'][0];
                        $brandName = $match['brand_name'] ?? 'Another Brand';
                        $scanStatus = 'wrong_brand';
                        $message = "WRONG BRAND: {$match['sku']} belongs to {$brandName} (Filter: {$session->brand?->brand_name})";

                        $log = StockTakingScanLog::create([
                            'session_id' => $session->id,
                            'stock_taking_item_id' => null,
                            'store_location_id' => $scannedLocationId,
                            'raw_code' => $parsed['raw'],
                            'resolved_sku' => $match['sku'],
                            'detected_brand_id' => $match['brand_id'],
                            'detected_brand_name' => $match['brand_name'],
                            'art_no' => $match['art_no'],
                            'size' => $match['size'],
                            'sleeve_type' => $match['sleeve_type'],
                            'qty' => $qty,
                            'scan_status' => $scanStatus,
                            'message' => $message,
                            'scanned_by' => auth()->id(),
                        ]);

                        return [
                            'success' => false,
                            'scan_status' => $scanStatus,
                            'message' => $message,
                            'item' => $match,
                            'log' => $log,
                            'session' => $session,
                        ];
                    }
                }
            }

            if ($sessionItem) {
                $isZeroSystemStockItem = $isZeroSystemStockItem || floatval($sessionItem->system_qty) <= 0.0001;

                // Prevent accidental duplicate barcode scans within 3 seconds unless explicitly confirmed
                if (!$allowDuplicate) {
                    $recentDuplicate = StockTakingScanLog::where('session_id', $session->id)
                        ->where('resolved_sku', $sessionItem->sku)
                        ->whereIn('scan_status', ['matched', 'excess'])
                        ->where('created_at', '>=', now()->subSeconds(3))
                        ->orderByDesc('id')
                        ->first();

                    if ($recentDuplicate) {
                        $dupMessage = "DUPLICATE SCAN WARNING: {$sessionItem->sku} was just scanned. Quantity not increased.";
                        $log = StockTakingScanLog::create([
                            'session_id' => $session->id,
                            'stock_taking_item_id' => $sessionItem->id,
                            'store_location_id' => $scannedLocationId,
                            'raw_code' => $parsed['raw'],
                            'resolved_sku' => $sessionItem->sku,
                            'detected_brand_id' => $session->brand_id,
                            'detected_brand_name' => $session->brand?->brand_name,
                            'art_no' => $sessionItem->art_no,
                            'size' => $sessionItem->size,
                            'sleeve_type' => $sessionItem->sleeve_type,
                            'qty' => $qty,
                            'scan_status' => 'duplicate',
                            'message' => $dupMessage,
                            'scanned_by' => auth()->id(),
                        ]);

                        return [
                            'success' => false,
                            'duplicate' => true,
                            'can_override' => true,
                            'scan_status' => 'duplicate',
                            'raw_code' => $parsed['raw'],
                            'qty' => $qty,
                            'message' => $dupMessage,
                            'item' => [
                                'id' => $sessionItem->id,
                                'sku' => $sessionItem->sku,
                                'style_name' => $sessionItem->style_name,
                                'art_no' => $sessionItem->art_no,
                                'size' => $sessionItem->size,
                                'sleeve_type' => $sessionItem->sleeve_type,
                                'system_qty' => floatval($sessionItem->system_qty),
                                'scanned_qty' => floatval($sessionItem->scanned_qty),
                            ],
                            'log' => $log,
                            'session' => $session,
                        ];
                    }
                }

                $movementMap = self::getSessionMovementMap($session);
                $movementQty = floatval($movementMap[$sessionItem->sku] ?? 0);

                $newScanned = round(floatval($sessionItem->scanned_qty) + $qty, 2);
                $calc = self::classifyItemQuantities(floatval($sessionItem->system_qty), $movementQty, $newScanned, floatval($sessionItem->rate));
                $effectiveSys = $calc['system_qty'];
                $isExcess = $calc['status'] === 'excess';
                $scanStatus = $isExcess ? 'excess' : 'matched';

                $sessionItem->scanned_qty = $newScanned;
                if ($scannedLocationId) {
                    $sessionItem->store_location_id = $scannedLocationId;
                }
                $sessionItem->last_scanned_at = now();
                $sessionItem->save();
                $sessionItem->loadMissing(['systemStoreLocation', 'storeLocation']);

                $session->total_scanned_qty = round(floatval($session->total_scanned_qty) + $qty, 2);
                $session->save();

                $locSuffix = $scannedLocationName ? " [Loc: {$scannedLocationName}]" : '';
                if ($isZeroSystemStockItem && $isExcess) {
                    $message = "ZERO SYSTEM STOCK FOUND: {$sessionItem->sku} scanned ({$newScanned} / {$effectiveSys} sys) - Recorded as Excess{$locSuffix}";
                } elseif ($isExcess) {
                    $message = "EXCESS: {$sessionItem->sku} scanned ({$newScanned}) > system ({$effectiveSys}){$locSuffix}";
                } else {
                    $message = "MATCHED: {$sessionItem->sku} ({$newScanned} / {$effectiveSys}){$locSuffix}";
                }

                $log = StockTakingScanLog::create([
                    'session_id' => $session->id,
                    'stock_taking_item_id' => $sessionItem->id,
                    'store_location_id' => $scannedLocationId,
                    'raw_code' => $parsed['raw'],
                    'resolved_sku' => $sessionItem->sku,
                    'detected_brand_id' => $session->brand_id,
                    'detected_brand_name' => $session->brand?->brand_name,
                    'art_no' => $sessionItem->art_no,
                    'size' => $sessionItem->size,
                    'sleeve_type' => $sessionItem->sleeve_type,
                    'qty' => $qty,
                    'scan_status' => $scanStatus,
                    'message' => $message,
                    'scanned_by' => auth()->id(),
                ]);

                return [
                    'success' => true,
                    'scan_status' => $scanStatus,
                    'zero_stock_found' => $isZeroSystemStockItem,
                    'message' => $message,
                    'item' => [
                        'id' => $sessionItem->id,
                        'sku' => $sessionItem->sku,
                        'style_name' => $sessionItem->style_name,
                        'art_no' => $sessionItem->art_no,
                        'size' => $sessionItem->size,
                        'sleeve_type' => $sessionItem->sleeve_type,
                        'system_store_location_id' => $sessionItem->system_store_location_id,
                        'system_store_location' => $sessionItem->systemStoreLocation?->store_location,
                        'store_location_id' => $sessionItem->store_location_id,
                        'store_location' => $scannedLocationName ?? $sessionItem->storeLocation?->store_location,
                        'snapshot_qty' => $calc['snapshot_qty'],
                        'movement_qty' => $calc['movement_qty'],
                        'system_qty' => $effectiveSys,
                        'scanned_qty' => $newScanned,
                        'remaining_qty' => $calc['remaining_qty'],
                        'variance' => $calc['variance'],
                    ],
                    'log' => $log,
                    'session' => $session,
                ];
            }

            $message = "NOT FOUND: Code '{$candidateCode}' does not exist in Finished Goods stock.";
            $log = StockTakingScanLog::create([
                'session_id' => $session->id,
                'stock_taking_item_id' => null,
                'store_location_id' => $scannedLocationId,
                'raw_code' => $parsed['raw'],
                'resolved_sku' => $candidateCode,
                'qty' => $qty,
                'scan_status' => 'not_found',
                'message' => $message,
                'scanned_by' => auth()->id(),
            ]);

            return [
                'success' => false,
                'scan_status' => 'not_found',
                'message' => $message,
                'item' => null,
                'log' => $log,
                'session' => $session,
            ];
        });

        if (!empty($result['session_expired'])) {
            return response()->json([
                'success' => false,
                'session_expired' => true,
                'message' => 'Session expired or was reset. Reloading Brand stock...',
            ], 404);
        }

        if (!empty($result['finalized'])) {
            $sessionNo = $result['session']->session_no ?? '';
            return response()->json([
                'success' => false,
                'finalized' => true,
                'scan_status' => 'finalized',
                'message' => "Stock taking session {$sessionNo} is already finalized. Scanning is disabled.",
            ], 422);
        }

        if (!empty($result['duplicate'])) {
            return response()->json([
                'success' => false,
                'duplicate' => true,
                'can_override' => true,
                'scan_status' => 'duplicate',
                'raw_code' => $result['raw_code'],
                'qty' => $result['qty'],
                'message' => $result['message'],
                'item' => $result['item'],
                'log_id' => $result['log']->id,
                'stats' => $this->buildSummaryStats($result['session']),
            ], 409);
        }

        return response()->json([
            'success' => $result['success'],
            'scan_status' => $result['scan_status'],
            'zero_stock_found' => !empty($result['zero_stock_found']),
            'message' => $result['message'],
            'item' => $result['item'],
            'log_id' => $result['log']->id,
            'stats' => $this->buildSummaryStats($result['session']),
        ]);
    }

    public function undoScan(Request $request, $id)
    {
        $this->authorizeAccess();

        $logId = $request->input('log_id');

        $res = DB::transaction(function () use ($id, $logId) {
            $session = StockTakingSession::lockForUpdate()->find($id);
            if (!$session) {
                return ['error' => 'Session expired or was reset.', 'session_expired' => true, 'code' => 404];
            }
            if ($session->status !== 'In Progress') {
                return ['error' => 'Session is finalized and cannot be modified.', 'code' => 422];
            }

            $logQuery = StockTakingScanLog::where('session_id', $session->id)->orderByDesc('id');
            if ($logId) {
                $logQuery->where('id', (int) $logId);
            }
            $log = $logQuery->lockForUpdate()->first();

            if (!$log) {
                return ['error' => 'No scan entry found to undo.', 'code' => 404];
            }

            $revertedSku = $log->resolved_sku;
            $revertedQty = floatval($log->qty);

            $logIdToDelete = $log->id;
            $itemIdToUpdate = $log->stock_taking_item_id;
            $wasCountedScan = $log->scan_status !== 'duplicate';

            if ($itemIdToUpdate && $wasCountedScan) {
                $item = StockTakingItem::where('session_id', $session->id)
                    ->where('id', $itemIdToUpdate)
                    ->lockForUpdate()
                    ->first();
                if ($item) {
                    $actualDelta = min(floatval($item->scanned_qty), $revertedQty);
                    $item->scanned_qty = max(0, round(floatval($item->scanned_qty) - $actualDelta, 2));
                    if ($item->scanned_qty <= 0.0001) {
                        $item->store_location_id = null;
                    } else {
                        $prevLocationId = StockTakingScanLog::where('session_id', $session->id)
                            ->where('stock_taking_item_id', $item->id)
                            ->where('id', '!=', $logIdToDelete)
                            ->whereIn('scan_status', ['matched', 'excess'])
                            ->whereNotNull('store_location_id')
                            ->orderByDesc('id')
                            ->value('store_location_id');
                        $item->store_location_id = $prevLocationId ?: null;
                    }
                    $item->save();

                    $session->total_scanned_qty = max(0, round(floatval($session->total_scanned_qty) - $actualDelta, 2));
                    $session->save();
                }
            }

            $log->delete();

            return [
                'session' => $session,
                'message' => "Undid scan of {$revertedSku} (-{$revertedQty}).",
            ];
        });

        if (!empty($res['error'])) {
            return response()->json(['success' => false, 'message' => $res['error']], $res['code']);
        }

        return response()->json([
            'success' => true,
            'message' => $res['message'],
            'stats' => $this->buildSummaryStats($res['session']),
        ]);
    }

    public function itemsData(Request $request, $id)
    {
        $this->authorizeAccess();

        $session = StockTakingSession::find($id);
        if (!$session) {
            return response()->json([
                'success' => false,
                'session_expired' => true,
                'message' => 'Session expired or was reset.',
            ], 404);
        }
        $filter = (string) $request->input('filter', 'all');
        $search = trim((string) $request->input('search', ''));
        $perPage = min(250, max(10, (int) $request->input('per_page', 10)));
        $page = max(1, (int) $request->input('page', 1));

        if ($filter === 'alerts' || $filter === 'logs') {
            $logQuery = StockTakingScanLog::with(['scanner', 'storeLocation'])
                ->where('session_id', $session->id)
                ->orderByDesc('id');

            if ($filter === 'alerts') {
                $logQuery->whereIn('scan_status', ['wrong_brand', 'zero_stock', 'not_found', 'duplicate']);
            }
            if ($search !== '') {
                $logQuery->where(function ($q) use ($search) {
                    $q->where('resolved_sku', 'like', "%{$search}%")
                      ->orWhere('art_no', 'like', "%{$search}%")
                      ->orWhere('detected_brand_name', 'like', "%{$search}%")
                      ->orWhere('message', 'like', "%{$search}%")
                      ->orWhereHas('storeLocation', fn($sl) => $sl->where('store_location', 'like', "%{$search}%"));
                });
            }

            $paginator = $logQuery->paginate($perPage, ['*'], 'page', $page);
            $logs = $paginator->getCollection()->map(function ($l) {
                return [
                    'id' => $l->id,
                    'time' => $l->created_at?->format('d-m-Y h:i:s A'),
                    'raw_code' => $l->raw_code,
                    'resolved_sku' => $l->resolved_sku,
                    'detected_brand_name' => $l->detected_brand_name,
                    'art_no' => $l->art_no,
                    'size' => $l->size,
                    'sleeve_type' => $l->sleeve_type,
                    'store_location' => $l->storeLocation?->store_location ?? '-',
                    'qty' => floatval($l->qty),
                    'scan_status' => $l->scan_status,
                    'message' => $l->message,
                    'scanned_by' => $l->scanner?->name ?? '-',
                ];
            })->values();

            return response()->json([
                'mode' => 'logs',
                'rows' => $logs,
                'pagination' => [
                    'total' => $paginator->total(),
                    'per_page' => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'from' => $paginator->firstItem() ?? 0,
                    'to' => $paginator->lastItem() ?? 0,
                ],
            ]);
        }

        $query = StockTakingItem::with(['systemStoreLocation', 'storeLocation'])
            ->where('session_id', $session->id);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('art_no', 'like', "%{$search}%")
                  ->orWhere('style_name', 'like', "%{$search}%")
                  ->orWhere('size', 'like', "%{$search}%")
                  ->orWhereHas('storeLocation', fn($sl) => $sl->where('store_location', 'like', "%{$search}%"))
                  ->orWhereHas('systemStoreLocation', fn($sl) => $sl->where('store_location', 'like', "%{$search}%"));
            });
        }

        $allMatchingItems = $query
            ->orderByDesc('last_scanned_at')
            ->orderBy('style_name')
            ->orderBy('art_no')
            ->orderBy('size')
            ->get();

        $movementMap = self::getSessionMovementMap($session);

        $filtered = [];
        foreach ($allMatchingItems as $it) {
            $mv = floatval($movementMap[$it->sku] ?? 0);
            $calc = self::classifyItemQuantities(floatval($it->system_qty), $mv, floatval($it->scanned_qty), floatval($it->rate));
            $status = $calc['status'];

            $include = match ($filter) {
                'all' => true,
                'pending' => $status === 'pending',
                'in_progress' => $status === 'in_progress',
                'completed', 'matched' => $status === 'completed',
                'shortage' => in_array($status, ['pending', 'in_progress'], true),
                'excess' => $status === 'excess',
                'scanned' => $calc['scanned_qty'] > 0.0001,
                default => true,
            };

            if (!$include) {
                continue;
            }

            $filtered[] = [
                'id' => $it->id,
                'sku' => $it->sku,
                'style_name' => $it->style_name ?: '-',
                'art_no' => $it->art_no ?: '-',
                'size' => $it->size ?: '-',
                'sleeve_type' => $it->sleeve_type ?: '-',
                'rate' => floatval($it->rate),
                'snapshot_qty' => $calc['snapshot_qty'],
                'movement_qty' => $calc['movement_qty'],
                'system_qty' => $calc['system_qty'],
                'scanned_qty' => $calc['scanned_qty'],
                'remaining_qty' => $calc['remaining_qty'],
                'variance' => $calc['variance'],
                'status' => $status,
                'system_store_location_id' => $it->system_store_location_id,
                'system_store_location' => $it->systemStoreLocation?->store_location,
                'store_location_id' => $it->store_location_id,
                'store_location' => $it->storeLocation?->store_location,
                'last_scanned_at' => $it->last_scanned_at ? $it->last_scanned_at->format('h:i:s A') : '-',
            ];
        }

        $total = count($filtered);
        $lastPage = max(1, (int) ceil($total / $perPage));
        $currentPage = min($page, $lastPage);
        $offset = ($currentPage - 1) * $perPage;
        $pageRows = array_slice($filtered, $offset, $perPage);

        return response()->json([
            'mode' => 'items',
            'rows' => array_values($pageRows),
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $currentPage,
                'last_page' => $lastPage,
                'from' => $total > 0 ? ($offset + 1) : 0,
                'to' => $total > 0 ? ($offset + count($pageRows)) : 0,
            ],
        ]);
    }

    public function exportExcel($id)
    {
        $this->authorizeAccess();

        $session = StockTakingSession::with([
            'brand',
            'storeType',
            'warehouse',
            'storeLocation',
            'starter',
            'completer',
            'items.systemStoreLocation',
            'items.storeLocation',
            'scanLogs.scanner',
            'scanLogs.storeLocation',
        ])->findOrFail($id);

        $brandSlug = preg_replace('/[^A-Za-z0-9_-]+/', '_', (string) ($session->brand?->brand_name ?? 'BRAND'));
        $filename = $session->session_no . '_' . $brandSlug . '_StockTaking.xlsx';

        return Excel::download(new FgStockTakingExport($session), $filename);
    }
}


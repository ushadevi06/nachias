<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\BrandCategory;
use App\Models\GrnEntry;
use App\Models\GrnEntryItem;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\SalesOrder;
use App\Models\Customer;
use App\Models\StoreType;
use App\Models\StockEntryItem;
use App\Models\StoreLocation;
use App\Models\Warehouse;
use App\Models\WarehouseBrandCapacity;
use App\Models\Style;
use Illuminate\Support\Facades\DB;

class WarehouseReportController extends Controller
{
    public function brandwiseStockDetails($id)
    {
        $brand = Brand::find($id);
        return view('reports.warehouse_report.brandwise_stock_details', compact('id', 'brand'));
    }

    public function index(Request $request)
    {
        $brands = Brand::where('status', 'Active')->get();
        $stores = StoreType::where('status', 'Active')->get();
        $warehouses = Warehouse::where('status', 'Active')->orderBy('id', 'desc')->get();
        $customers = Customer::where('status', 'Active')->orderBy('id','desc')->get();
        $defaultWarehouseId = $warehouses->first()?->id;

        if ($request->ajax()) {
            $activeTab = $request->get('active_tab');

            if ($activeTab == 'warehouse-summary') {
                $selectedWarehouseId = $request->get('warehouse_id', 'all');

                if ($selectedWarehouseId == 'all' || empty($selectedWarehouseId)) {
                    $capacities = WarehouseBrandCapacity::where('status', 'Active')
                        ->groupBy('brand_id')
                        ->select('brand_id', DB::raw('SUM(capacity_pcs) as total_capacity'))
                        ->pluck('total_capacity', 'brand_id')
                        ->toArray();

                    $brandIdsWithStock = StockEntryItem::where('stock_type', 'finished_goods')
                        ->distinct()
                        ->pluck('brand_id')
                        ->toArray();
                } else {
                    $capacities = WarehouseBrandCapacity::where('warehouse_id', $selectedWarehouseId)
                        ->where('status', 'Active')
                        ->groupBy('brand_id')
                        ->select('brand_id', DB::raw('SUM(capacity_pcs) as total_capacity'))
                        ->pluck('total_capacity', 'brand_id')
                        ->toArray();

                    $brandIdsWithStock = StockEntryItem::where('stock_type', 'finished_goods')
                        ->where(function($q) use ($selectedWarehouseId) {
                            $q->where('warehouse_id', $selectedWarehouseId)
                              ->orWhereHas('stockEntry', function($se) use ($selectedWarehouseId) {
                                  $se->where('warehouse_id', $selectedWarehouseId);
                              });
                        })
                        ->distinct()
                        ->pluck('brand_id')
                        ->toArray();
                }

                $activeBrands = Brand::where('status', 'Active')->whereIn('id', $brandIdsWithStock)->orderBy('id','desc')->get();
                $reportData = [];

                $totalCapacity = 0;
                $totalSetwise = 0;
                $totalSingle = 0;
                $totalTotalStock = 0;
                $totalDamage = 0;
                $utilCapacity = 0;
                $utilStock = 0;

                foreach ($activeBrands as $brand) {
                    $capacityPcs = $capacities[$brand->id] ?? 0;

                    $stockItemsQuery = StockEntryItem::where('brand_id', $brand->id)
                        ->where('stock_type', 'finished_goods');

                    if ($selectedWarehouseId != 'all' && !empty($selectedWarehouseId)) {
                        $stockItemsQuery->where(function($q) use ($selectedWarehouseId) {
                            $q->where('warehouse_id', $selectedWarehouseId)
                              ->orWhereHas('stockEntry', function($se) use ($selectedWarehouseId) {
                                  $se->where('warehouse_id', $selectedWarehouseId);
                              });
                        });
                    }

                    $stockItems = $stockItemsQuery->with('stockEntry.productionReceipt')->get();

                    $setwiseStock = 0;
                    $singleStoreStock = 0;
                    $damageStock = 0;

                    foreach ($stockItems as $item) {
                        $netQty = (float)($item->qty_in - $item->qty_out);
                        $storeTypeId = $item->store_type_id ?? $item->stockEntry?->store_type_id ?? $item->stockEntry?->productionReceipt?->store_type_id;

                        if ($storeTypeId == 12) {
                            // Single Store Stock (Store Type ID 12)
                            $singleStoreStock += $netQty;
                        } elseif ($storeTypeId == 6) {
                            // Damage Store Stock (Store Type ID 6)
                            $damageStock += $netQty;
                        } else {
                            // Setwise Stock (Store Type ID 3 or Default Finished Goods)
                            $setwiseStock += $netQty;
                        }
                    }

                    $totalStock = $setwiseStock + $singleStoreStock;
                    $utilizationPct = $capacityPcs > 0 ? round(($totalStock / $capacityPcs) * 100, 2) : 0;

                    $totalCapacity += $capacityPcs;
                    $totalSetwise += $setwiseStock;
                    $totalSingle += $singleStoreStock;
                    $totalTotalStock += $totalStock;
                    $totalDamage += $damageStock;

                    // Only count brands with configured capacity towards utilization %
                    if ($capacityPcs > 0) {
                        $utilCapacity += $capacityPcs;
                        $utilStock    += $totalStock;
                    }

                    $reportData[] = [
                        'brand_id' => $brand->id,
                        'brand_name' => $brand->brand_name,
                        'capacity_pcs' => $capacityPcs,
                        'setwise_stock' => $setwiseStock,
                        'single_store_stock' => $singleStoreStock,
                        'total_stock' => $totalStock,
                        'utilization_pct' => $utilizationPct,
                        'damage_stock' => $damageStock,
                    ];
                }

                $totalUtilizationPct = $utilCapacity > 0 ? round(($utilStock / $utilCapacity) * 100, 2) : 0;

                $totals = [
                    'capacity_pcs' => $totalCapacity,
                    'setwise_stock' => $totalSetwise,
                    'single_store_stock' => $totalSingle,
                    'total_stock' => $totalTotalStock,
                    'utilization_pct' => $totalUtilizationPct,
                    'damage_stock' => $totalDamage,
                ];

                return response()->json([
                    'warehouse-summary' => view('reports.warehouse_report.warehouse_summary', compact('reportData', 'totals', 'selectedWarehouseId'))->render()
                ]);
            }

            $tabViews = [
                'brand-stock' => 'reports.warehouse_report.brandwise_stock',
                'brandwise-stock' => 'reports.warehouse_report.brandwise_stock',
                'brand-sales' => 'reports.warehouse_report.brandwise_sales',
                'brandwise-sales' => 'reports.warehouse_report.brandwise_sales',
                'assorted-stock' => 'reports.warehouse_report.assorted_stock',
                'order-dispatch' => 'reports.warehouse_report.order_vs_dispatch',
                'sales-return' => 'reports.warehouse_report.sales_return',
                'white-dhoti' => 'reports.warehouse_report.white_dhoti',
                'dispatch' => 'reports.warehouse_report.dispatch_report',
                'inward' => 'reports.warehouse_report.stock_inward',
                'discount' => 'reports.warehouse_report.regular_discount',
                'damage' => 'reports.warehouse_report.damage_sales_split',
                'urgent-orders' => 'reports.warehouse_report.urgent_orders',
                'brandwise-lost-sales' => 'reports.warehouse_report.brandwise_lost_sales',
                'stock-inward-sales' => 'reports.warehouse_report.stock_inward_sales',
            ];

            if ($activeTab && isset($tabViews[$activeTab])) {
                return response()->json([
                    $activeTab => view($tabViews[$activeTab])->render()
                ]);
            }

            $priorityStock = collect([]);
            if ($activeTab == 'priority' || !$activeTab) {
                $priorityStockQuery = StockEntryItem::query()
                    ->select(
                        'art_no',
                        DB::raw('SUM(qty_in - qty_out) as current_stock'),
                        DB::raw('DATEDIFF(NOW(), MIN(created_at)) as ageing_days'),
                        DB::raw('MAX(price) as latest_price'),
                        DB::raw('SUM((qty_in - qty_out) * price) as stock_value')
                    )
                    ->whereNull('deleted_at');

                if ($request->brand_id) {
                    $priorityStockQuery->where('brand_id', $request->brand_id);
                }
                if ($request->store_id) {
                    $priorityStockQuery->where('store_location_id', $request->store_id);
                }

                $priorityStock = $priorityStockQuery->groupBy('art_no')->having('current_stock', '>', 0)
                    ->having('ageing_days', '>=', 90)
                    ->orderBy('ageing_days', 'desc')
                    ->get();

                if ($activeTab == 'priority') {
                    return response()->json([
                        'priority' => view('reports.warehouse_report.priority_stock', compact('priorityStock'))->render()
                    ]);
                }
            }

            return response()->json([
                'sales-return' => view('reports.warehouse_report.sales_return')->render(),
                'white-dhoti' => view('reports.warehouse_report.white_dhoti')->render(),
                'priority' => view('reports.warehouse_report.priority_stock', compact('priorityStock'))->render(),
                'damage' => view('reports.warehouse_report.damage_sales_split')->render(),
                'urgent-orders' => view('reports.warehouse_report.urgent_orders')->render(),
                'brandwise-lost-sales' => view('reports.warehouse_report.brandwise_lost_sales')->render(),
                'stock-inward-sales' => view('reports.warehouse_report.stock_inward_sales')->render(),
            ]);
        }

        $priorityStock = collect([]);

        return view('reports/warehouse_report', compact(
            'priorityStock',
            'brands',
            'stores',
            'warehouses',
            'customers',
            'defaultWarehouseId'
        ));
    }

    public function getBrandwiseStyles(Request $request)
    {
        $draw = intval($request->draw);
        $start = intval($request->start);
        $rawLen = intval($request->length); $length = ($rawLen > 0 || $rawLen === -1) ? $rawLen : 10;
        $searchVal = $request->search;
        $search = is_array($searchVal) ? ($searchVal['value'] ?? '') : (is_string($searchVal) ? $searchVal : '');

        $brandId = $request->brand_id;
        $agingFilter = $request->aging_filter;
        $warehouseId = ($request->warehouse_id && $request->warehouse_id !== 'all') ? $request->warehouse_id : null;
        
        $stockQuery = StockEntryItem::query()
            ->leftJoin('stock_entries', 'stock_entry_items.stock_entry_id', '=', 'stock_entries.id')
            ->leftJoin('styles', 'stock_entry_items.style_id', '=', 'styles.id')
            ->select(
                DB::raw('COALESCE(styles.id, 0) as style_id'),
                DB::raw("COALESCE(styles.style_name, '(No Style)') as style_name"),
                DB::raw('SUM(stock_entry_items.qty_in - stock_entry_items.qty_out) as total_qty'),
                DB::raw('SUM((stock_entry_items.qty_in - stock_entry_items.qty_out) * stock_entry_items.price) as stock_value'),
                DB::raw('ROUND(AVG(DATEDIFF(NOW(), COALESCE(stock_entries.stock_date, stock_entry_items.created_at)))) as avg_stock_days')
            )
            ->where('stock_entry_items.stock_type', 'finished_goods')
            ->whereNull('stock_entry_items.deleted_at');

        if ($agingFilter === '0-30') {
            $stockQuery->whereRaw('DATEDIFF(NOW(), COALESCE(stock_entries.stock_date, stock_entry_items.created_at)) <= 30');
        } elseif ($agingFilter === '31-60') {
            $stockQuery->whereRaw('DATEDIFF(NOW(), COALESCE(stock_entries.stock_date, stock_entry_items.created_at)) BETWEEN 31 AND 60');
        } elseif ($agingFilter === '61-90') {
            $stockQuery->whereRaw('DATEDIFF(NOW(), COALESCE(stock_entries.stock_date, stock_entry_items.created_at)) BETWEEN 61 AND 90');
        } elseif ($agingFilter === '91+') {
            $stockQuery->whereRaw('DATEDIFF(NOW(), COALESCE(stock_entries.stock_date, stock_entry_items.created_at)) > 90');
        }

        if ($warehouseId) {
            $stockQuery->where('stock_entry_items.warehouse_id', $warehouseId);
        }

        if ($brandId !== null && $brandId !== '' && $brandId != 0) {
            $stockQuery->where(function($q) use ($brandId) {
                $q->where('stock_entry_items.brand_id', $brandId)
                  ->orWhere(function($q2) use ($brandId) {
                      $q2->whereNull('stock_entry_items.brand_id')
                         ->whereExists(function($sub) use ($brandId) {
                             $sub->select(DB::raw(1))
                                 ->from('brands')
                                 ->where('brands.id', $brandId)
                                 ->whereRaw('stock_entry_items.art_no LIKE CONCAT(brands.code, \'%\')')
                                 ->whereRaw('NOT EXISTS (SELECT 1 FROM brands b2 WHERE stock_entry_items.art_no LIKE CONCAT(b2.code, \'%\') AND LENGTH(b2.code) > LENGTH(brands.code))');
                         });
                  });
            });
        } elseif ($brandId === 0 || $brandId === '0') {
            $stockQuery->whereNull('stock_entry_items.brand_id');
        }

        if ($request->store_id) {
            $stockQuery->where('stock_entry_items.store_location_id', $request->store_id);
        }

        if ($search) {
            $stockQuery->where('styles.style_name', 'like', "%{$search}%");
        }

        $stockQuery->groupBy(DB::raw('COALESCE(styles.id, 0)'), DB::raw("COALESCE(styles.style_name, '(No Style)')"));

        $recordsTotal = DB::query()->fromSub($stockQuery, 'sub')->count();
        $recordsFiltered = $recordsTotal;

        $totalsRow = DB::query()->fromSub($stockQuery, 'sub')->selectRaw('
            SUM(CASE WHEN total_qty > 0 THEN total_qty ELSE 0 END) as sum_qty, 
            SUM(CASE WHEN stock_value > 0 THEN stock_value ELSE 0 END) as sum_value
        ')->first();
        $totals = [
            'total_qty' => number_format($totalsRow->sum_qty ?? 0, 0),
            'stock_value' => '₹' . number_format($totalsRow->sum_value ?? 0, 2),
        ];

        if ($length != -1) {
            $stockQuery->skip($start)->take($length);
        }

        $styles = $stockQuery->get();

        if ($draw) {
            $data = [];
            $brandName = $request->brand_name ?? '';
            foreach ($styles as $item) {
                $sId = $item->style_id ?? 0;
                $sName = addslashes($item->style_name);
                $bId = $brandId ?? 0;
                $bNameEsc = addslashes($brandName);

                $avgDays = $item->avg_stock_days;
                if ($avgDays !== null && $item->total_qty > 0) {
                    $d = intval($avgDays);
                    $badgeClass = ($d <= 30) ? 'bg-label-success' : (($d <= 60) ? 'bg-label-info' : (($d <= 90) ? 'bg-label-warning' : 'bg-label-danger'));
                    $daysBadge = '<span class="badge ' . $badgeClass . ' fw-bold">' . $d . ' Days</span>';
                } else {
                    $daysBadge = '-';
                }

                $data[] = [
                    'DT_RowAttr' => [
                        'onclick' => "drillDownToArtNo({$bId}, '" . htmlspecialchars($bNameEsc, ENT_QUOTES) . "', {$sId}, '" . htmlspecialchars($sName, ENT_QUOTES) . "')",
                        'style' => 'cursor: pointer;'
                    ],
                    'brand' => '<strong class="text-uppercase">' . htmlspecialchars($item->style_name) . '</strong>',
                    'total_qty' => number_format($item->total_qty ?? 0, 0),
                    'low_stock' => '-',
                    'excess_stock' => '-',
                    'stock_days' => $daysBadge,
                    'stock_value' => '₹' . number_format($item->stock_value ?? 0, 2),
                ];
            }

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
                'totals' => $totals,
            ]);
        }

        return response()->json($styles);
    }

    public function getWarehouseSummaryStyles(Request $request)
    {
        $draw = intval($request->draw);
        $start = intval($request->start);
        $rawLen = intval($request->length); $length = ($rawLen > 0 || $rawLen === -1) ? $rawLen : 10;
        $searchVal = $request->search;
        $search = is_array($searchVal) ? ($searchVal['value'] ?? '') : (is_string($searchVal) ? $searchVal : '');

        $brandId = $request->brand_id;
        $warehouseId = $request->warehouse_id ?: 'all';

        if ($warehouseId == 'all') {
            $capacities = WarehouseBrandCapacity::where('brand_id', $brandId)
                ->where('status', 'Active')
                ->groupBy('style_id')
                ->select('style_id', DB::raw('SUM(capacity_pcs) as total_capacity'))
                ->pluck('total_capacity', 'style_id')
                ->toArray();

            // Fetch distinct styles present in stock for this brand
            $stockStyleIds = StockEntryItem::where('brand_id', $brandId)
                ->where('stock_type', 'finished_goods')
                ->whereNotNull('style_id')
                ->distinct()
                ->pluck('style_id')
                ->toArray();
        } else {
            $capacities = WarehouseBrandCapacity::where('warehouse_id', $warehouseId)
                ->where('brand_id', $brandId)
                ->where('status', 'Active')
                ->pluck('capacity_pcs', 'style_id')
                ->toArray();

            // Fetch distinct styles present in stock for this brand & warehouse
            $stockStyleIds = StockEntryItem::where('brand_id', $brandId)
                ->where('stock_type', 'finished_goods')
                ->where(function($q) use ($warehouseId) {
                    $q->where('warehouse_id', $warehouseId)
                      ->orWhereHas('stockEntry', function($se) use ($warehouseId) {
                          $se->where('warehouse_id', $warehouseId);
                      });
                })
                ->whereNotNull('style_id')
                ->distinct()
                ->pluck('style_id')
                ->toArray();
        }

        $allActiveStyles = Style::where('status', 'Active')->orderBy('style_name', 'asc')->get();

        $configuredStyleIds = array_keys($capacities);
        $targetStyles = $allActiveStyles->filter(function($s) use ($configuredStyleIds, $stockStyleIds) {
            return in_array($s->id, $configuredStyleIds) || in_array($s->id, $stockStyleIds);
        });

        if ($targetStyles->isEmpty()) {
            $targetStyles = $allActiveStyles;
        }

        $hasNullStyleCap = isset($capacities['']) || isset($capacities[null]) || isset($capacities[0]);

        $data = [];
        $totalCapacity = 0;
        $totalSetwise = 0;
        $totalSingle = 0;
        $totalTotalStock = 0;
        $totalDamage = 0;
        // For utilization %: only count styles with configured capacity
        $utilCapacity = 0;
        $utilStock = 0;

        foreach ($targetStyles as $style) {
            if ($search && stripos($style->style_name, $search) === false) {
                continue;
            }

            $capPcs = $capacities[$style->id] ?? 0;

            $stockItemsQuery = StockEntryItem::where('brand_id', $brandId)
                ->where('style_id', $style->id)
                ->where('stock_type', 'finished_goods');

            if ($warehouseId != 'all') {
                $stockItemsQuery->where(function($q) use ($warehouseId) {
                    $q->where('warehouse_id', $warehouseId)
                      ->orWhereHas('stockEntry', function($se) use ($warehouseId) {
                          $se->where('warehouse_id', $warehouseId);
                      });
                });
            }

            $stockItems = $stockItemsQuery->with('stockEntry.productionReceipt')->get();

            $setwiseStock = 0;
            $singleStoreStock = 0;
            $damageStock = 0;

            foreach ($stockItems as $item) {
                $netQty = (float)($item->qty_in - $item->qty_out);
                $storeTypeId = $item->store_type_id ?? $item->stockEntry?->store_type_id ?? $item->stockEntry?->productionReceipt?->store_type_id;

                if ($storeTypeId == 12) {
                    $singleStoreStock += $netQty;
                } elseif ($storeTypeId == 6) {
                    $damageStock += $netQty;
                } else {
                    $setwiseStock += $netQty;
                }
            }

            $totalStock = $setwiseStock + $singleStoreStock;
            $utilizationPct = $capPcs > 0 ? round(($totalStock / $capPcs) * 100, 2) : 0;
            $badgeClass = $utilizationPct > 95 ? 'bg-danger' : ($utilizationPct > 80 ? 'bg-warning text-dark' : 'bg-primary');

            $totalCapacity += $capPcs;
            $totalSetwise += $setwiseStock;
            $totalSingle += $singleStoreStock;
            $totalTotalStock += $totalStock;
            $totalDamage += $damageStock;

            if ($capPcs > 0) {
                $utilCapacity += $capPcs;
                $utilStock    += $totalStock;
            }

            $data[] = [
                'style_name' => '<strong class="text-uppercase text-dark">' . e($style->style_name) . '</strong>',
                'capacity_pcs' => number_format($capPcs),
                'setwise_stock' => '<span class="text-primary fw-semibold">' . number_format($setwiseStock) . '</span>',
                'single_store_stock' => '<span class="text-info fw-semibold">' . number_format($singleStoreStock) . '</span>',
                'total_stock' => '<span class="fw-bold">' . number_format($totalStock) . '</span>',
                'utilization' => '<span class="badge ' . $badgeClass . ' px-3 py-2 fs-6">' . $utilizationPct . '%</span>',
                'damage_stock' => '<span class="text-danger fw-semibold">' . number_format($damageStock) . '</span>',
            ];
        }

        if ($hasNullStyleCap) {
            $nullCapPcs = $capacities[''] ?? ($capacities[null] ?? ($capacities[0] ?? 0));
            if ($nullCapPcs > 0 && (!$search || stripos('General / Unassigned', $search) !== false)) {
                $stockItemsQuery = StockEntryItem::where('brand_id', $brandId)
                    ->whereNull('style_id')
                    ->where('stock_type', 'finished_goods');

                if ($warehouseId != 'all') {
                    $stockItemsQuery->where(function($q) use ($warehouseId) {
                        $q->where('warehouse_id', $warehouseId)
                          ->orWhereHas('stockEntry', function($se) use ($warehouseId) {
                              $se->where('warehouse_id', $warehouseId);
                          });
                    });
                }

                $stockItems = $stockItemsQuery->with('stockEntry.productionReceipt')->get();

                $setwiseStock = 0;
                $singleStoreStock = 0;
                $damageStock = 0;

                foreach ($stockItems as $item) {
                    $netQty = (float)($item->qty_in - $item->qty_out);
                    $storeTypeId = $item->store_type_id ?? $item->stockEntry?->store_type_id ?? $item->stockEntry?->productionReceipt?->store_type_id;

                    if ($storeTypeId == 12) {
                        $singleStoreStock += $netQty;
                    } elseif ($storeTypeId == 6) {
                        $damageStock += $netQty;
                    } else {
                        $setwiseStock += $netQty;
                    }
                }

                $totalStock = $setwiseStock + $singleStoreStock;
                $utilizationPct = $nullCapPcs > 0 ? round(($totalStock / $nullCapPcs) * 100, 2) : 0;
                $badgeClass = $utilizationPct > 95 ? 'bg-danger' : ($utilizationPct > 80 ? 'bg-warning text-dark' : 'bg-primary');

                $totalCapacity += $nullCapPcs;
                $totalSetwise += $setwiseStock;
                $totalSingle += $singleStoreStock;
                $totalTotalStock += $totalStock;
                $totalDamage += $damageStock;

                // nullCapPcs > 0 is already guaranteed by the outer if; include in utilization
                $utilCapacity += $nullCapPcs;
                $utilStock    += $totalStock;

                $data[] = [
                    'style_name' => '<strong class="text-muted fst-italic">General / Unassigned</strong>',
                    'capacity_pcs' => number_format($nullCapPcs),
                    'setwise_stock' => '<span class="text-primary fw-semibold">' . number_format($setwiseStock) . '</span>',
                    'single_store_stock' => '<span class="text-info fw-semibold">' . number_format($singleStoreStock) . '</span>',
                    'total_stock' => '<span class="fw-bold">' . number_format($totalStock) . '</span>',
                    'utilization' => '<span class="badge ' . $badgeClass . ' px-3 py-2 fs-6">' . $utilizationPct . '%</span>',
                    'damage_stock' => '<span class="text-danger fw-semibold">' . number_format($damageStock) . '</span>',
                ];
            }
        }

        // Utilization % only for styles with configured capacity
        $totalUtilPct = $utilCapacity > 0 ? round(($utilStock / $utilCapacity) * 100, 2) : 0;
        $totBadgeClass = $totalUtilPct > 95 ? 'bg-danger' : ($totalUtilPct > 80 ? 'bg-warning text-dark' : 'bg-primary');

        $totals = [
            'capacity_pcs' => number_format($totalCapacity),
            'setwise_stock' => number_format($totalSetwise),
            'single_store_stock' => number_format($totalSingle),
            'total_stock' => number_format($totalTotalStock),
            'utilization' => '<span class="badge ' . $totBadgeClass . ' px-3 py-2 fs-6">' . $totalUtilPct . '%</span>',
            'utilization_text' => $totalUtilPct . '%',
            'damage_stock' => number_format($totalDamage),
        ];

        $recordsTotal = count($data);
        $recordsFiltered = $recordsTotal;

        if ($length != -1) {
            $pagedData = array_slice($data, $start, $length);
        } else {
            $pagedData = $data;
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $pagedData,
            'totals' => $totals
        ]);
    }

    public function getBrandwiseArtNos(Request $request)
    {
        $draw = intval($request->draw);
        $start = intval($request->start);
        $rawLen = intval($request->length); $length = ($rawLen > 0 || $rawLen === -1) ? $rawLen : 10;
        $searchVal = $request->search;
        $search = is_array($searchVal) ? ($searchVal['value'] ?? '') : (is_string($searchVal) ? $searchVal : '');

        $brandId = $request->brand_id;
        $styleId = $request->style_id;
        $agingFilter = $request->aging_filter;
        $warehouseId = ($request->warehouse_id && $request->warehouse_id !== 'all') ? $request->warehouse_id : null;
        
        $stockQuery = StockEntryItem::query()
            ->leftJoin('stock_entries', 'stock_entry_items.stock_entry_id', '=', 'stock_entries.id')
            ->leftJoin('fg_min_stocks', function($join) {
                $join->on('stock_entry_items.id', '=', 'fg_min_stocks.stock_entry_item_id')
                     ->where('fg_min_stocks.status', '=', 'Active')
                     ->whereNull('fg_min_stocks.deleted_at');
            })
            ->select(
                'stock_entry_items.art_no',
                'stock_entry_items.sleeve_type',
                'stock_entry_items.size',
                DB::raw('SUM(stock_entry_items.qty_in - stock_entry_items.qty_out) as total_qty'),
                DB::raw('SUM((stock_entry_items.qty_in - stock_entry_items.qty_out) * stock_entry_items.price) as stock_value'),
                DB::raw('SUM(IFNULL(fg_min_stocks.min_stock, 0)) as total_min_stock'),
                DB::raw('SUM(IFNULL(fg_min_stocks.max_stock, 0)) as total_max_stock'),
                DB::raw('MIN(CASE WHEN (stock_entry_items.qty_in - stock_entry_items.qty_out) > 0 THEN COALESCE(stock_entries.stock_date, stock_entry_items.created_at) ELSE NULL END) as oldest_stock_date')
            )
            ->where('stock_entry_items.stock_type', 'finished_goods')
            ->whereNull('stock_entry_items.deleted_at');

        if ($agingFilter === '0-30') {
            $stockQuery->whereRaw('DATEDIFF(NOW(), COALESCE(stock_entries.stock_date, stock_entry_items.created_at)) <= 30');
        } elseif ($agingFilter === '31-60') {
            $stockQuery->whereRaw('DATEDIFF(NOW(), COALESCE(stock_entries.stock_date, stock_entry_items.created_at)) BETWEEN 31 AND 60');
        } elseif ($agingFilter === '61-90') {
            $stockQuery->whereRaw('DATEDIFF(NOW(), COALESCE(stock_entries.stock_date, stock_entry_items.created_at)) BETWEEN 61 AND 90');
        } elseif ($agingFilter === '91+') {
            $stockQuery->whereRaw('DATEDIFF(NOW(), COALESCE(stock_entries.stock_date, stock_entry_items.created_at)) > 90');
        }

        if ($warehouseId) {
            $stockQuery->where('stock_entry_items.warehouse_id', $warehouseId);
        }

        if ($styleId !== null && $styleId !== '') {
            if ($styleId === 0 || $styleId === '0') {
                $stockQuery->where(function($q) {
                    $q->whereNull('stock_entry_items.style_id')
                      ->orWhere('stock_entry_items.style_id', 0);
                });
            } else {
                $stockQuery->where('stock_entry_items.style_id', $styleId);
            }
        }

        if ($brandId !== null && $brandId !== '' && $brandId != 0) {
            $stockQuery->where(function($q) use ($brandId) {
                $q->where('stock_entry_items.brand_id', $brandId)
                  ->orWhere(function($q2) use ($brandId) {
                      $q2->whereNull('stock_entry_items.brand_id')
                         ->whereExists(function($sub) use ($brandId) {
                            $sub->select(DB::raw(1))
                                ->from('brands')
                                ->where('brands.id', $brandId)
                                ->whereRaw('stock_entry_items.art_no LIKE CONCAT(brands.code, \'%\')')
                                ->whereRaw('NOT EXISTS (SELECT 1 FROM brands b2 WHERE stock_entry_items.art_no LIKE CONCAT(b2.code, \'%\') AND LENGTH(b2.code) > LENGTH(brands.code))');
                         });
                  });
            });
        } elseif ($brandId === 0 || $brandId === '0') {
            $stockQuery->whereNull('stock_entry_items.brand_id');
        }

        if ($request->store_id) {
            $stockQuery->where('stock_entry_items.store_location_id', $request->store_id);
        }

        if ($search) {
            $stockQuery->where(function($q) use ($search) {
                $q->where('stock_entry_items.art_no', 'like', "%{$search}%")
                  ->orWhere('stock_entry_items.sleeve_type', 'like', "%{$search}%")
                  ->orWhere('stock_entry_items.size', 'like', "%{$search}%")
                  ->orWhere('stock_entry_items.price', 'like', "%{$search}%")
                  ->orWhere('stock_entry_items.qty_in', 'like', "%{$search}%");
            });
        }

        $stockQuery->groupBy('stock_entry_items.art_no', 'stock_entry_items.sleeve_type', 'stock_entry_items.size');

        if ($request->has('order')) {
            $orderColIdx = intval($request->input('order.0.column'));
            $orderDir = $request->input('order.0.dir') === 'desc' ? 'desc' : 'asc';
            $columnsMap = [
                0 => 'stock_entry_items.art_no',
                1 => 'stock_entry_items.sleeve_type',
                2 => 'stock_entry_items.size',
                3 => 'total_qty',
                6 => 'oldest_stock_date',
                7 => 'stock_value',
            ];
            if (isset($columnsMap[$orderColIdx])) {
                $stockQuery->orderBy($columnsMap[$orderColIdx], $orderDir);
            }
        } else {
            $stockQuery->orderBy('stock_entry_items.art_no', 'asc')->orderBy('stock_entry_items.sleeve_type', 'asc')->orderBy('stock_entry_items.size', 'asc');
        }

        $recordsTotal = DB::query()->fromSub($stockQuery, 'sub')->count();
        $recordsFiltered = $recordsTotal;

        $totalsRow = DB::query()->fromSub($stockQuery, 'sub')->selectRaw('
            SUM(CASE WHEN total_qty > 0 THEN total_qty ELSE 0 END) as sum_qty, 
            SUM(CASE WHEN stock_value > 0 THEN stock_value ELSE 0 END) as sum_value,
            SUM(CASE WHEN total_min_stock > 0 AND total_qty < total_min_stock THEN (total_min_stock - total_qty) ELSE 0 END) as sum_low_stock,
            SUM(CASE WHEN total_max_stock > 0 AND total_qty > total_max_stock THEN (total_qty - total_max_stock) ELSE 0 END) as sum_excess_stock
        ')->first();
        $totals = [
            'total_qty' => number_format($totalsRow->sum_qty ?? 0, 0),
            'low_stock' => number_format($totalsRow->sum_low_stock ?? 0, 0),
            'excess_stock' => number_format($totalsRow->sum_excess_stock ?? 0, 0),
            'stock_value' => '₹' . number_format($totalsRow->sum_value ?? 0, 2),
        ];

        if ($length != -1) {
            $stockQuery->skip($start)->take($length);
        }

        $artNos = $stockQuery->get();

        if ($draw) {
            $data = [];
            foreach ($artNos as $artNo) {
                $displayQty = max(0, floatval($artNo->total_qty ?? 0));
                $totalMinStock = floatval($artNo->total_min_stock ?? 0);
                $totalMaxStock = floatval($artNo->total_max_stock ?? 0);

                $lowStock = ($totalMinStock > 0 && $displayQty < $totalMinStock) ? ($totalMinStock - $displayQty) : 0;
                $excessStock = ($totalMaxStock > 0 && $displayQty > $totalMaxStock) ? ($displayQty - $totalMaxStock) : 0;
                $stockValue = max(0, floatval($artNo->stock_value ?? 0));

                $stockDays = '-';
                if ($artNo->oldest_stock_date && $displayQty > 0) {
                    $d = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($artNo->oldest_stock_date));
                    $badgeClass = ($d <= 30) ? 'bg-label-success' : (($d <= 60) ? 'bg-label-info' : (($d <= 90) ? 'bg-label-warning' : 'bg-label-danger'));
                    $stockDays = '<span class="badge ' . $badgeClass . ' fw-bold">' . $d . ' Days</span>';
                }

                $lowStockDisplay = $lowStock > 0 ? '<span class="text-danger fw-bold">' . number_format($lowStock, 0) . '</span>' : '-';
                $excessStockDisplay = $excessStock > 0 ? '<span class="text-success fw-bold">' . number_format($excessStock, 0) . '</span>' : '-';

                $rawSleeve = trim((string)($artNo->sleeve_type ?? ''));
                $sleeveUpper = strtoupper($rawSleeve);
                if (in_array($sleeveUpper, ['FS', 'F/S', 'FULL', 'FULL SLEEVE'])) {
                    $sleeveFormatted = 'Full';
                } elseif (in_array($sleeveUpper, ['HS', 'H/S', 'HALF', 'HALF SLEEVE'])) {
                    $sleeveFormatted = 'Half';
                } else {
                    $sleeveFormatted = $rawSleeve !== '' ? htmlspecialchars($rawSleeve) : '-';
                }

                $sizeFormatted = !empty($artNo->size) ? htmlspecialchars($artNo->size) : '-';

                $data[] = [
                    'brand' => '<strong class="text-uppercase">' . htmlspecialchars($artNo->art_no ?? '-') . '</strong>',
                    'sleeve' => $sleeveFormatted,
                    'size' => $sizeFormatted,
                    'total_qty' => number_format($displayQty, 0),
                    'low_stock' => $lowStockDisplay,
                    'excess_stock' => $excessStockDisplay,
                    'stock_days' => $stockDays,
                    'stock_value' => '₹' . number_format($stockValue, 2),
                ];
            }

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
                'totals' => $totals,
            ]);
        }

        return response()->json($artNos);
    }

    public function getAssortedStyles(Request $request)
    {
        $draw = intval($request->draw);
        $start = intval($request->start);
        $rawLen = intval($request->length); $length = ($rawLen > 0 || $rawLen === -1) ? $rawLen : 10;
        $searchVal = $request->search;
        $search = is_array($searchVal) ? ($searchVal['value'] ?? '') : (is_string($searchVal) ? $searchVal : '');

        $brandId = $request->brand_id;
        $storeIdFilter = $request->store_id;
        $warehouseId = ($request->warehouse_id && $request->warehouse_id !== 'all') ? $request->warehouse_id : null;

        $subQuery = StockEntryItem::query()
            ->select(
                'stock_entry_items.brand_id',
                'stock_entry_items.style_id',
                'stock_entry_items.art_no',
                'stock_entry_items.sleeve_type',
                DB::raw("SUM(CASE WHEN stock_entry_items.size = '38' THEN stock_entry_items.qty_in - stock_entry_items.qty_out ELSE 0 END) as q38"),
                DB::raw("SUM(CASE WHEN stock_entry_items.size = '40' THEN stock_entry_items.qty_in - stock_entry_items.qty_out ELSE 0 END) as q40"),
                DB::raw("SUM(stock_entry_items.qty_in - stock_entry_items.qty_out) as total_qty")
            )
            ->where('stock_entry_items.stock_type', 'finished_goods')
            ->whereNull('stock_entry_items.deleted_at');

        if ($brandId) {
            $subQuery->where('stock_entry_items.brand_id', $brandId);
        }
        if ($warehouseId) {
            $subQuery->where('stock_entry_items.warehouse_id', $warehouseId);
        }
        if ($storeIdFilter) {
            $subQuery->where('stock_entry_items.store_location_id', $storeIdFilter);
        }

        $subQuery->groupBy(
            'stock_entry_items.brand_id',
            'stock_entry_items.style_id',
            'stock_entry_items.art_no',
            'stock_entry_items.sleeve_type'
        )->havingRaw('total_qty > 0 AND NOT (q38 > 0 AND q40 > 0)');

        $query = DB::query()->fromSub($subQuery, 'assorted')
            ->leftJoin('styles', 'assorted.style_id', '=', 'styles.id')
            ->select(
                DB::raw('COALESCE(styles.id, 0) as style_id'),
                DB::raw("COALESCE(styles.style_name, '(No Style)') as style_name"),
                DB::raw('COUNT(DISTINCT assorted.art_no) as art_count'),
                DB::raw('SUM(assorted.total_qty) as assorted_qty')
            );

        if ($search) {
            $query->where('styles.style_name', 'like', "%{$search}%");
        }

        $query->groupBy(DB::raw('COALESCE(styles.id, 0)'), DB::raw("COALESCE(styles.style_name, '(No Style)')"));

        $recordsTotal = DB::query()->fromSub($query, 'sub')->count();
        $recordsFiltered = $recordsTotal;
        $totalQtySum = DB::query()->fromSub($query, 'sub')->sum('assorted_qty');

        $query->orderBy('assorted_qty', 'desc');

        if ($length != -1) {
            $query->skip($start)->take($length);
        }

        $stylesList = $query->get();
        $data = [];
        $brandName = $request->brand_name ?? '';

        foreach ($stylesList as $item) {
            $sId = $item->style_id;
            $sName = addslashes($item->style_name);
            $bId = $brandId ?? 0;
            $bNameEsc = addslashes($brandName);

            $data[] = [
                'DT_RowAttr' => [
                    'onclick' => "drillDownToAssortedArtNo({$bId}, '" . htmlspecialchars($bNameEsc, ENT_QUOTES) . "', {$sId}, '" . htmlspecialchars($sName, ENT_QUOTES) . "')",
                    'style' => 'cursor: pointer;'
                ],
                'style' => '<strong class="text-uppercase text-primary">' . htmlspecialchars($item->style_name) . '</strong>',
                'art_count' => '<span class="badge bg-label-info">' . number_format($item->art_count ?? 0, 0) . ' Art Nos</span>',
                'stock_qty' => '<span class="badge bg-label-primary fs-6 fw-bold">' . number_format($item->assorted_qty ?? 0, 0) . '</span>',
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
            'totals' => [
                'stock_qty' => number_format($totalQtySum ?? 0, 0)
            ]
        ]);
    }

    public function getAssortedArtNos(Request $request)
    {
        $draw = intval($request->draw);
        $start = intval($request->start);
        $rawLen = intval($request->length); $length = ($rawLen > 0 || $rawLen === -1) ? $rawLen : 10;
        $searchVal = $request->search;
        $search = is_array($searchVal) ? ($searchVal['value'] ?? '') : (is_string($searchVal) ? $searchVal : '');

        $brandId = $request->brand_id;
        $styleId = $request->style_id;
        $storeIdFilter = $request->store_id;
        $warehouseId = ($request->warehouse_id && $request->warehouse_id !== 'all') ? $request->warehouse_id : null;

        $query = StockEntryItem::query()
            ->select(
                'stock_entry_items.art_no',
                'stock_entry_items.sleeve_type',
                DB::raw("SUM(CASE WHEN stock_entry_items.size = '36' THEN stock_entry_items.qty_in - stock_entry_items.qty_out ELSE 0 END) as q36"),
                DB::raw("SUM(CASE WHEN stock_entry_items.size = '38' THEN stock_entry_items.qty_in - stock_entry_items.qty_out ELSE 0 END) as q38"),
                DB::raw("SUM(CASE WHEN stock_entry_items.size = '40' THEN stock_entry_items.qty_in - stock_entry_items.qty_out ELSE 0 END) as q40"),
                DB::raw("SUM(CASE WHEN stock_entry_items.size = '42' THEN stock_entry_items.qty_in - stock_entry_items.qty_out ELSE 0 END) as q42"),
                DB::raw("SUM(CASE WHEN stock_entry_items.size = '44' THEN stock_entry_items.qty_in - stock_entry_items.qty_out ELSE 0 END) as q44"),
                DB::raw("SUM(CASE WHEN stock_entry_items.size = '46' THEN stock_entry_items.qty_in - stock_entry_items.qty_out ELSE 0 END) as q46"),
                DB::raw("SUM(CASE WHEN stock_entry_items.size = '48' THEN stock_entry_items.qty_in - stock_entry_items.qty_out ELSE 0 END) as q48"),
                DB::raw("SUM(CASE WHEN stock_entry_items.size = '50' THEN stock_entry_items.qty_in - stock_entry_items.qty_out ELSE 0 END) as q50"),
                DB::raw("SUM(stock_entry_items.qty_in - stock_entry_items.qty_out) as total_qty")
            )
            ->where('stock_entry_items.stock_type', 'finished_goods')
            ->whereNull('stock_entry_items.deleted_at');

        if ($brandId) {
            $query->where('stock_entry_items.brand_id', $brandId);
        }
        if ($styleId !== null && $styleId !== '') {
            if ($styleId === 0 || $styleId === '0') {
                $query->where(function($q) {
                    $q->whereNull('stock_entry_items.style_id')->orWhere('stock_entry_items.style_id', 0);
                });
            } else {
                $query->where('stock_entry_items.style_id', $styleId);
            }
        }
        if ($warehouseId) {
            $query->where('stock_entry_items.warehouse_id', $warehouseId);
        }
        if ($storeIdFilter) {
            $query->where('stock_entry_items.store_location_id', $storeIdFilter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('stock_entry_items.art_no', 'like', "%{$search}%")
                  ->orWhere('stock_entry_items.sleeve_type', 'like', "%{$search}%");
            });
        }

        $query->groupBy('stock_entry_items.art_no', 'stock_entry_items.sleeve_type')
              ->havingRaw('total_qty > 0 AND NOT (q38 > 0 AND q40 > 0)');

        $recordsTotal = DB::query()->fromSub($query, 'sub')->count();
        $recordsFiltered = $recordsTotal;

        $totalsRow = DB::query()->fromSub($query, 'sub')->selectRaw('
            SUM(q36) as sum_36,
            SUM(q38) as sum_38,
            SUM(q40) as sum_40,
            SUM(q42) as sum_42,
            SUM(q44) as sum_44,
            SUM(q46) as sum_46,
            SUM(q48) as sum_48,
            SUM(q50) as sum_50,
            SUM(total_qty) as sum_total
        ')->first();

        $query->orderBy('stock_entry_items.art_no', 'asc');

        if ($length != -1) {
            $query->skip($start)->take($length);
        }

        $artNosList = $query->get();
        $data = [];

        foreach ($artNosList as $row) {
            $rawSleeve = trim((string)($row->sleeve_type ?? ''));
            $sleeveUpper = strtoupper($rawSleeve);
            if (in_array($sleeveUpper, ['FS', 'F/S', 'FULL', 'FULL SLEEVE'])) {
                $sleeveFormatted = 'Full';
            } elseif (in_array($sleeveUpper, ['HS', 'H/S', 'HALF', 'HALF SLEEVE'])) {
                $sleeveFormatted = 'Half';
            } else {
                $sleeveFormatted = $rawSleeve !== '' ? htmlspecialchars($rawSleeve) : '-';
            }

            $fmtCell = function($val) {
                return $val > 0 
                    ? '<span class="fw-bold text-dark">' . number_format($val, 0) . '</span>' 
                    : '<span class="text-muted" style="opacity: 0.4;">-</span>';
            };

            $data[] = [
                'art_no' => '<strong class="text-dark">' . htmlspecialchars($row->art_no ?? '-') . '</strong>',
                'sleeve' => '<span class="badge bg-label-secondary fw-semibold">' . $sleeveFormatted . '</span>',
                'size_36' => $fmtCell($row->q36),
                'size_38' => $fmtCell($row->q38),
                'size_40' => $fmtCell($row->q40),
                'size_42' => $fmtCell($row->q42),
                'size_44' => $fmtCell($row->q44),
                'size_46' => $fmtCell($row->q46),
                'size_48' => $fmtCell($row->q48),
                'size_50' => $fmtCell($row->q50),
                'stock_qty' => '<span class="badge bg-label-primary fs-6 fw-bold">' . number_format($row->total_qty ?? 0, 0) . '</span>',
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
            'totals' => [
                'sum_36' => number_format($totalsRow->sum_36 ?? 0, 0),
                'sum_38' => number_format($totalsRow->sum_38 ?? 0, 0),
                'sum_40' => number_format($totalsRow->sum_40 ?? 0, 0),
                'sum_42' => number_format($totalsRow->sum_42 ?? 0, 0),
                'sum_44' => number_format($totalsRow->sum_44 ?? 0, 0),
                'sum_46' => number_format($totalsRow->sum_46 ?? 0, 0),
                'sum_48' => number_format($totalsRow->sum_48 ?? 0, 0),
                'sum_50' => number_format($totalsRow->sum_50 ?? 0, 0),
                'stock_qty' => number_format($totalsRow->sum_total ?? 0, 0),
            ]
        ]);
    }


    public function ajaxReportData(Request $request, $type)
    {
        $draw = intval($request->draw);
        $start = intval($request->start);
        $length = intval($request->length ? $request->length : 10);
        $searchVal = $request->search;
        $search = is_array($searchVal) ? ($searchVal['value'] ?? '') : (is_string($searchVal) ? $searchVal : '');

        switch ($type) {
            case 'stock-inward-sales':
                $fromDate = $request->from_date ? date('Y-m-d', strtotime($request->from_date)) : date('Y-m-d');
                $toDate = $request->to_date ? date('Y-m-d', strtotime($request->to_date)) : date('Y-m-d');
                $brandIdFilter = $request->brand_id;
                $storeIdFilter = $request->store_id;
                $warehouseIdFilter = ($request->warehouse_id && $request->warehouse_id !== 'all') ? $request->warehouse_id : null;

                $brandsQuery = Brand::where('status', 'Active');
                if ($brandIdFilter) {
                    $brandsQuery->where('id', $brandIdFilter);
                }
                if ($search) {
                    $brandsQuery->where('brand_name', 'like', "%{$search}%");
                }
                $brands = $brandsQuery->get();

                $inwardQuery = DB::table('stock_entry_items')
                    ->leftJoin('stock_entries', 'stock_entry_items.stock_entry_id', '=', 'stock_entries.id')
                    ->leftJoin('brands', function($join) {
                        $join->on(function($query) {
                            $query->on('stock_entry_items.brand_id', '=', 'brands.id')
                                ->orOn(function($sub) {
                                    $sub->whereNull('stock_entry_items.brand_id')
                                        ->on('stock_entry_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                                        ->whereRaw("NOT EXISTS (
                                            SELECT 1 FROM brands b2 
                                            WHERE stock_entry_items.art_no LIKE CONCAT(b2.code, '%') 
                                            AND LENGTH(b2.code) > LENGTH(brands.code)
                                        )");
                                });
                        });
                    })
                    ->where('stock_entry_items.stock_type', 'finished_goods')
                    ->where('stock_entry_items.qty_in', '>', 0)
                    ->whereNull('stock_entry_items.deleted_at')
                    ->whereBetween(DB::raw('DATE(COALESCE(stock_entries.stock_date, stock_entry_items.created_at))'), [$fromDate, $toDate]);

                if ($brandIdFilter) {
                    $inwardQuery->where('brands.id', $brandIdFilter);
                }
                if ($storeIdFilter) {
                    $inwardQuery->where('stock_entry_items.store_location_id', $storeIdFilter);
                }
                if ($warehouseIdFilter) {
                    $inwardQuery->where('stock_entry_items.warehouse_id', $warehouseIdFilter);
                }

                $inwardPerBrand = $inwardQuery->select(
                    'brands.id as brand_id',
                    DB::raw('SUM(stock_entry_items.qty_in) as inward_qty')
                )->groupBy('brands.id')->pluck('inward_qty', 'brand_id');

                // Sales Qty in Date Range
                $salesQuery = DB::table('sales_invoice_items')
                    ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
                    ->leftJoin('stock_entry_items', 'sales_invoice_items.stock_entry_item_id', '=', 'stock_entry_items.id')
                    ->leftJoin('brands', function($join) {
                        $join->on(function($query) {
                            $query->on('stock_entry_items.brand_id', '=', 'brands.id')
                                ->orOn(function($sub) {
                                    $sub->whereNull('stock_entry_items.brand_id')
                                        ->on('sales_invoice_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                                        ->whereRaw("NOT EXISTS (
                                            SELECT 1 FROM brands b2 
                                            WHERE sales_invoice_items.art_no LIKE CONCAT(b2.code, '%') 
                                            AND LENGTH(b2.code) > LENGTH(brands.code)
                                        )");
                                });
                        });
                    })
                    ->whereNull('sales_invoices.deleted_at')
                    ->whereNull('sales_invoice_items.deleted_at')
                    ->whereBetween('sales_invoices.inv_date', [$fromDate, $toDate]);

                if ($brandIdFilter) {
                    $salesQuery->where('brands.id', $brandIdFilter);
                }
                if ($storeIdFilter) {
                    $salesQuery->where('sales_invoices.store_location_id', $storeIdFilter);
                }
                if ($warehouseIdFilter) {
                    $salesQuery->where('stock_entry_items.warehouse_id', $warehouseIdFilter);
                }

                $salesPerBrand = $salesQuery->select(
                    'brands.id as brand_id',
                    DB::raw('SUM(sales_invoice_items.quantity) as sales_qty')
                )->groupBy('brands.id')->pluck('sales_qty', 'brand_id');

                // Sales Return Qty in Date Range
                $returnQuery = DB::table('credit_note_items')
                    ->join('credit_notes', 'credit_note_items.credit_note_id', '=', 'credit_notes.id')
                    ->leftJoin('sales_invoice_items', 'credit_note_items.sales_invoice_item_id', '=', 'sales_invoice_items.id')
                    ->leftJoin('stock_entry_items', 'sales_invoice_items.stock_entry_item_id', '=', 'stock_entry_items.id')
                    ->leftJoin('brands', function($join) {
                        $join->on(function($query) {
                            $query->on('stock_entry_items.brand_id', '=', 'brands.id')
                                ->orOn(function($sub) {
                                    $sub->whereNull('stock_entry_items.brand_id')
                                        ->on('sales_invoice_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                                        ->whereRaw("NOT EXISTS (
                                            SELECT 1 FROM brands b2 
                                            WHERE sales_invoice_items.art_no LIKE CONCAT(b2.code, '%') 
                                            AND LENGTH(b2.code) > LENGTH(brands.code)
                                        )");
                                });
                        });
                    })
                    ->whereNull('credit_notes.deleted_at')
                    ->whereNull('credit_note_items.deleted_at')
                    ->whereBetween('credit_notes.note_date', [$fromDate, $toDate]);

                if ($brandIdFilter) {
                    $returnQuery->where('brands.id', $brandIdFilter);
                }
                if ($warehouseIdFilter) {
                    $returnQuery->where('stock_entry_items.warehouse_id', $warehouseIdFilter);
                }

                $returnPerBrand = $returnQuery->select(
                    'brands.id as brand_id',
                    DB::raw('SUM(credit_note_items.quantity) as return_qty')
                )->groupBy('brands.id')->pluck('return_qty', 'brand_id');

                // Current Live Stock per Brand
                $currentStockQuery = DB::table('stock_entry_items')
                    ->leftJoin('brands', function($join) {
                        $join->on(function($query) {
                            $query->on('stock_entry_items.brand_id', '=', 'brands.id')
                                ->orOn(function($sub) {
                                    $sub->whereNull('stock_entry_items.brand_id')
                                        ->on('stock_entry_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                                        ->whereRaw("NOT EXISTS (
                                            SELECT 1 FROM brands b2 
                                            WHERE stock_entry_items.art_no LIKE CONCAT(b2.code, '%') 
                                            AND LENGTH(b2.code) > LENGTH(brands.code)
                                        )");
                                });
                        });
                    })
                    ->where('stock_entry_items.stock_type', 'finished_goods')
                    ->whereNull('stock_entry_items.deleted_at');

                if ($brandIdFilter) {
                    $currentStockQuery->where('brands.id', $brandIdFilter);
                }
                if ($storeIdFilter) {
                    $currentStockQuery->where('stock_entry_items.store_location_id', $storeIdFilter);
                }
                if ($warehouseIdFilter) {
                    $currentStockQuery->where('stock_entry_items.warehouse_id', $warehouseIdFilter);
                }

                $currentStockPerBrand = $currentStockQuery->select(
                    'brands.id as brand_id',
                    DB::raw('SUM(stock_entry_items.qty_in - stock_entry_items.qty_out) as total_qty')
                )->groupBy('brands.id')->pluck('total_qty', 'brand_id');

                // Inward from fromDate till today
                $inwardFromDateTillNowQuery = DB::table('stock_entry_items')
                    ->leftJoin('stock_entries', 'stock_entry_items.stock_entry_id', '=', 'stock_entries.id')
                    ->leftJoin('brands', function($join) {
                        $join->on(function($query) {
                            $query->on('stock_entry_items.brand_id', '=', 'brands.id')
                                ->orOn(function($sub) {
                                    $sub->whereNull('stock_entry_items.brand_id')
                                        ->on('stock_entry_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                                        ->whereRaw("NOT EXISTS (
                                            SELECT 1 FROM brands b2 
                                            WHERE stock_entry_items.art_no LIKE CONCAT(b2.code, '%') 
                                            AND LENGTH(b2.code) > LENGTH(brands.code)
                                        )");
                                });
                        });
                    })
                    ->where('stock_entry_items.stock_type', 'finished_goods')
                    ->where('stock_entry_items.qty_in', '>', 0)
                    ->whereNull('stock_entry_items.deleted_at')
                    ->where(DB::raw('DATE(COALESCE(stock_entries.stock_date, stock_entry_items.created_at))'), '>=', $fromDate);

                if ($brandIdFilter) {
                    $inwardFromDateTillNowQuery->where('brands.id', $brandIdFilter);
                }
                if ($storeIdFilter) {
                    $inwardFromDateTillNowQuery->where('stock_entry_items.store_location_id', $storeIdFilter);
                }
                if ($warehouseIdFilter) {
                    $inwardFromDateTillNowQuery->where('stock_entry_items.warehouse_id', $warehouseIdFilter);
                }
                $inwardTillNowPerBrand = $inwardFromDateTillNowQuery->select(
                    'brands.id as brand_id',
                    DB::raw('SUM(stock_entry_items.qty_in) as inward_qty')
                )->groupBy('brands.id')->pluck('inward_qty', 'brand_id');

                // Sales from fromDate till today
                $salesFromDateTillNowQuery = DB::table('sales_invoice_items')
                    ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
                    ->leftJoin('stock_entry_items', 'sales_invoice_items.stock_entry_item_id', '=', 'stock_entry_items.id')
                    ->leftJoin('brands', function($join) {
                        $join->on(function($query) {
                            $query->on('stock_entry_items.brand_id', '=', 'brands.id')
                                ->orOn(function($sub) {
                                    $sub->whereNull('stock_entry_items.brand_id')
                                        ->on('sales_invoice_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                                        ->whereRaw("NOT EXISTS (
                                            SELECT 1 FROM brands b2 
                                            WHERE sales_invoice_items.art_no LIKE CONCAT(b2.code, '%') 
                                            AND LENGTH(b2.code) > LENGTH(brands.code)
                                        )");
                                });
                        });
                    })
                    ->whereNull('sales_invoices.deleted_at')
                    ->whereNull('sales_invoice_items.deleted_at')
                    ->where('sales_invoices.inv_date', '>=', $fromDate);

                if ($brandIdFilter) {
                    $salesFromDateTillNowQuery->where('brands.id', $brandIdFilter);
                }
                if ($storeIdFilter) {
                    $salesFromDateTillNowQuery->where('sales_invoices.store_location_id', $storeIdFilter);
                }
                if ($warehouseIdFilter) {
                    $salesFromDateTillNowQuery->where('stock_entry_items.warehouse_id', $warehouseIdFilter);
                }
                $salesTillNowPerBrand = $salesFromDateTillNowQuery->select(
                    'brands.id as brand_id',
                    DB::raw('SUM(sales_invoice_items.quantity) as sales_qty')
                )->groupBy('brands.id')->pluck('sales_qty', 'brand_id');

                $returnsFromDateTillNowQuery = DB::table('credit_note_items')
                    ->join('credit_notes', 'credit_note_items.credit_note_id', '=', 'credit_notes.id')
                    ->leftJoin('sales_invoice_items', 'credit_note_items.sales_invoice_item_id', '=', 'sales_invoice_items.id')
                    ->leftJoin('stock_entry_items', 'sales_invoice_items.stock_entry_item_id', '=', 'stock_entry_items.id')
                    ->leftJoin('brands', function($join) {
                        $join->on(function($query) {
                            $query->on('stock_entry_items.brand_id', '=', 'brands.id')
                                ->orOn(function($sub) {
                                    $sub->whereNull('stock_entry_items.brand_id')
                                        ->on('sales_invoice_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                                        ->whereRaw("NOT EXISTS (
                                            SELECT 1 FROM brands b2 
                                            WHERE sales_invoice_items.art_no LIKE CONCAT(b2.code, '%') 
                                            AND LENGTH(b2.code) > LENGTH(brands.code)
                                        )");
                                });
                        });
                    })
                    ->whereNull('credit_notes.deleted_at')
                    ->whereNull('credit_note_items.deleted_at')
                    ->where('credit_notes.note_date', '>=', $fromDate);

                if ($brandIdFilter) {
                    $returnsFromDateTillNowQuery->where('brands.id', $brandIdFilter);
                }
                if ($warehouseIdFilter) {
                    $returnsFromDateTillNowQuery->where('stock_entry_items.warehouse_id', $warehouseIdFilter);
                }
                $returnsTillNowPerBrand = $returnsFromDateTillNowQuery->select(
                    'brands.id as brand_id',
                    DB::raw('SUM(credit_note_items.quantity) as return_qty')
                )->groupBy('brands.id')->pluck('return_qty', 'brand_id');

                $data = [];
                $totOpStock = 0;
                $totInward = 0;
                $totSales = 0;
                $totClosing = 0;
                $totSalesReturn = 0;
                $totNetClosing = 0;

                foreach ($brands as $brand) {
                    $bId = $brand->id;

                    $currStock = floatval($currentStockPerBrand[$bId] ?? 0);
                    $inwardTillNow = floatval($inwardTillNowPerBrand[$bId] ?? 0);
                    $salesTillNow = floatval($salesTillNowPerBrand[$bId] ?? 0);
                    $returnsTillNow = floatval($returnsTillNowPerBrand[$bId] ?? 0);

                    $opStock = max(0, $currStock - $inwardTillNow + $salesTillNow - $returnsTillNow);

                    $inward = floatval($inwardPerBrand[$bId] ?? 0);
                    $sales = floatval($salesPerBrand[$bId] ?? 0);
                    $closing = $opStock + $inward - $sales;
                    $salesReturn = floatval($returnPerBrand[$bId] ?? 0);
                    $netClosing = $closing + $salesReturn;

                    if ($opStock == 0 && $inward == 0 && $sales == 0 && $closing == 0 && $salesReturn == 0 && $netClosing == 0) {
                        continue;
                    }

                    $totOpStock += $opStock;
                    $totInward += $inward;
                    $totSales += $sales;
                    $totClosing += $closing;
                    $totSalesReturn += $salesReturn;
                    $totNetClosing += $netClosing;

                    $bNameSafe = addslashes($brand->brand_name);
                    $salesLink = $sales > 0 
                        ? '<a href="javascript:void(0)" onclick="drillDownToStockInwardSalesInvoices(event, ' . $bId . ', \'' . $bNameSafe . '\', \'\')" class="text-primary fw-bold text-decoration-underline" title="View Sales Invoices">' . number_format($sales, 0) . '</a>'
                        : '';

                    $data[] = [
                        'DT_RowAttr' => [
                            'onclick' => "drillDownToStockInwardSalesDetails({$bId}, '{$bNameSafe}')",
                            'style' => 'cursor: pointer;',
                            'data-brand-id' => $bId,
                            'data-brand-name' => $brand->brand_name
                        ],
                        'brand' => '<strong>' . htmlspecialchars($brand->brand_name) . '</strong>',
                        'op_stock' => number_format($opStock, 0),
                        'inward' => $inward > 0 ? number_format($inward, 0) : '',
                        'sales' => $salesLink,
                        'closing' => number_format($closing, 0),
                        'sales_return' => $salesReturn > 0 ? number_format($salesReturn, 0) : '',
                        'net_closing' => number_format($netClosing, 0),
                    ];
                }

                $recordsTotal = count($data);
                $recordsFiltered = $recordsTotal;

                if ($length != -1) {
                    $data = array_slice($data, $start, $length);
                }

                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                    'totals' => [
                        'op_stock' => number_format($totOpStock, 0),
                        'inward' => number_format($totInward, 0),
                        'sales' => number_format($totSales, 0),
                        'closing' => number_format($totClosing, 0),
                        'sales_return' => number_format($totSalesReturn, 0),
                        'net_closing' => number_format($totNetClosing, 0),
                    ]
                ]);

            case 'brandwise-sales':
                $fromDate = $request->from_date ? date('Y-m-d', strtotime(str_replace('/', '-', $request->from_date))) : date('Y-m-d', strtotime('-30 days'));
                $toDate = $request->to_date ? date('Y-m-d', strtotime(str_replace('/', '-', $request->to_date))) : date('Y-m-d');

                $fgBrandIds = StockEntryItem::where('stock_type', 'finished_goods')->whereNotNull('brand_id')->distinct()->pluck('brand_id')->toArray();

                $query = SalesInvoiceItem::query()
                    ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
                    ->leftJoin('stock_entry_items', 'sales_invoice_items.stock_entry_item_id', '=', 'stock_entry_items.id')
                    ->leftJoin('brands', function($join) {
                        $join->on(function($query) {
                            $query->on('stock_entry_items.brand_id', '=', 'brands.id')
                                ->orOn(function($sub) {
                                    $sub->whereNull('stock_entry_items.brand_id')
                                        ->on('sales_invoice_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                                        ->whereRaw("NOT EXISTS (
                                            SELECT 1 FROM brands b2 
                                            WHERE sales_invoice_items.art_no LIKE CONCAT(b2.code, '%') 
                                            AND LENGTH(b2.code) > LENGTH(brands.code)
                                        ) ");
                                });
                        });
                    })
                    ->whereNull('sales_invoices.deleted_at')
                    ->whereNull('sales_invoice_items.deleted_at')
                    ->whereBetween('sales_invoices.inv_date', [$fromDate, $toDate])
                    ->whereIn('brands.id', $fgBrandIds);

                if ($request->brand_id) {
                    $query->where('brands.id', $request->brand_id);
                }
                if ($request->store_id) {
                    $query->where('sales_invoices.store_location_id', $request->store_id);
                }
                if ($request->customer_id) {
                    $query->where('sales_invoices.customer_id', $request->customer_id);
                }
                if ($search) {
                    $query->where('brands.brand_name', 'like', "%{$search}%");
                }

                $query->select(
                    'brands.brand_name as brand',
                    'brands.id as brand_id',
                    DB::raw('SUM(sales_invoice_items.quantity) as sold_qty'),
                    DB::raw('SUM(sales_invoice_items.amount) as without_tax_amount'),
                    DB::raw('SUM(CASE WHEN sales_invoices.sub_total > 0 THEN sales_invoice_items.amount * (sales_invoices.total / sales_invoices.sub_total) ELSE sales_invoice_items.amount END) as taxable_amount'),
                    DB::raw('SUM(CASE WHEN sales_invoices.sub_total > 0 THEN sales_invoice_items.amount * (sales_invoices.tax_amount / sales_invoices.sub_total) ELSE 0 END) as tax_amount'),
                    DB::raw('SUM(CASE WHEN sales_invoices.sub_total > 0 THEN sales_invoice_items.amount * ((sales_invoices.grand_total - sales_invoices.total - sales_invoices.tax_amount) / sales_invoices.sub_total) ELSE 0 END) as round_off_amount'),
                    DB::raw('SUM(CASE WHEN sales_invoices.sub_total > 0 THEN sales_invoice_items.amount * (sales_invoices.grand_total / sales_invoices.sub_total) ELSE sales_invoice_items.amount END) as total_amount')
                )->groupBy('brands.brand_name', 'brands.id');

                $recordsTotal = DB::query()->fromSub($query, 'sub')->count();
                $recordsFiltered = $recordsTotal;

                $salesTotalsRow = DB::query()->fromSub($query, 'sub')->selectRaw('
                    SUM(sold_qty) as sum_qty,
                    SUM(without_tax_amount) as sum_without_tax,
                    SUM(taxable_amount) as sum_taxable,
                    SUM(tax_amount) as sum_tax,
                    SUM(round_off_amount) as sum_round_off,
                    SUM(total_amount) as sum_total
                ')->first();

                $sumRoundOff = (float)($salesTotalsRow->sum_round_off ?? 0);
                $sumRoundOffFormatted = ($sumRoundOff < 0 ? '-₹' . number_format(abs($sumRoundOff), 2) : '₹' . number_format($sumRoundOff, 2));

                $totals = [
                    'sold_qty' => number_format($salesTotalsRow->sum_qty ?? 0, 0),
                    'without_tax_amount' => '₹' . number_format($salesTotalsRow->sum_without_tax ?? 0, 2),
                    'taxable_amount' => '₹' . number_format($salesTotalsRow->sum_taxable ?? 0, 2),
                    'tax_amount' => '₹' . number_format($salesTotalsRow->sum_tax ?? 0, 2),
                    'round_off_amount' => $sumRoundOffFormatted,
                    'total_amount' => '₹' . number_format($salesTotalsRow->sum_total ?? 0, 2),
                    'sales_value' => '₹' . number_format($salesTotalsRow->sum_without_tax ?? 0, 2),
                ];

                if ($length != -1) {
                    $query->skip($start)->take($length);
                }

                $brandwiseSales = $query->get();

                $totalAmountSum = $salesTotalsRow->sum_total ?? 0;

                $data = [];
                foreach ($brandwiseSales as $sale) {
                    $trend = $totalAmountSum > 0 ? round(($sale->total_amount / $totalAmountSum) * 100, 1) : 0;

                    $trendHtml = '';
                    if ($trend > 0) {
                        $trendHtml = '<span class="text-success fw-bold">' . number_format($trend, 1) . '%</span>';
                    } else {
                        $trendHtml = '<span class="text-muted">0.0%</span>';
                    }

                    $roundOffVal = (float)($sale->round_off_amount ?? 0);
                    $roundOffFormatted = ($roundOffVal < 0 ? '-₹' . number_format(abs($roundOffVal), 2) : '₹' . number_format($roundOffVal, 2));

                    $data[] = [
                        'brand' => '<strong>' . ($sale->brand ?: 'Unassigned') . '</strong>',
                        'sold_qty' => number_format($sale->sold_qty ?? 0, 0),
                        'without_tax_amount' => '₹' . number_format($sale->without_tax_amount ?? 0, 2),
                        'taxable_amount' => '₹' . number_format($sale->taxable_amount ?? 0, 2),
                        'tax_amount' => '₹' . number_format($sale->tax_amount ?? 0, 2),
                        'round_off_amount' => $roundOffFormatted,
                        'total_amount' => '₹' . number_format($sale->total_amount ?? 0, 2),
                        'sales_value' => '₹' . number_format($sale->without_tax_amount ?? 0, 2),
                        'trend' => $trendHtml,
                    ];
                }

                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                    'totals' => $totals,
                ]);

            case 'brandwise-stock':
                $agingFilter = $request->aging_filter;
                $warehouseId = ($request->warehouse_id && $request->warehouse_id !== 'all') ? $request->warehouse_id : null;

                $query = StockEntryItem::query()
                    ->leftJoin('stock_entries', 'stock_entry_items.stock_entry_id', '=', 'stock_entries.id')
                    ->leftJoin('brands', function($join) {
                        $join->on(function($query) {
                            $query->on('stock_entry_items.brand_id', '=', 'brands.id')
                                ->orOn(function($sub) {
                                    $sub->whereNull('stock_entry_items.brand_id')
                                        ->on('stock_entry_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                                        ->whereRaw("NOT EXISTS (
                                            SELECT 1 FROM brands b2 
                                            WHERE stock_entry_items.art_no LIKE CONCAT(b2.code, '%') 
                                            AND LENGTH(b2.code) > LENGTH(brands.code)
                                        )");
                                });
                        });
                    })
                    ->leftJoin('fg_min_stocks', function($join) {
                        $join->on('stock_entry_items.id', '=', 'fg_min_stocks.stock_entry_item_id')
                            ->where('fg_min_stocks.status', '=', 'Active')
                            ->whereNull('fg_min_stocks.deleted_at');
                    })
                    ->select(
                        'brands.id as brand_id',
                        'brands.brand_name as brand',
                        DB::raw('SUM(stock_entry_items.qty_in - stock_entry_items.qty_out) as total_qty'),
                        DB::raw('SUM((stock_entry_items.qty_in - stock_entry_items.qty_out) * stock_entry_items.price) as stock_value'),
                        DB::raw('SUM(IFNULL(fg_min_stocks.min_stock, 0)) as total_min_stock'),
                        DB::raw('ROUND(AVG(DATEDIFF(NOW(), COALESCE(stock_entries.stock_date, stock_entry_items.created_at)))) as avg_stock_days')
                    )
                    ->where('stock_entry_items.stock_type', 'finished_goods')
                    ->whereNull('stock_entry_items.deleted_at')
                    ->whereNotNull('brands.id');

                if ($agingFilter === '0-30') {
                    $query->whereRaw('DATEDIFF(NOW(), COALESCE(stock_entries.stock_date, stock_entry_items.created_at)) <= 30');
                } elseif ($agingFilter === '31-60') {
                    $query->whereRaw('DATEDIFF(NOW(), COALESCE(stock_entries.stock_date, stock_entry_items.created_at)) BETWEEN 31 AND 60');
                } elseif ($agingFilter === '61-90') {
                    $query->whereRaw('DATEDIFF(NOW(), COALESCE(stock_entries.stock_date, stock_entry_items.created_at)) BETWEEN 61 AND 90');
                } elseif ($agingFilter === '91+') {
                    $query->whereRaw('DATEDIFF(NOW(), COALESCE(stock_entries.stock_date, stock_entry_items.created_at)) > 90');
                }

                if ($warehouseId) {
                    $query->where('stock_entry_items.warehouse_id', $warehouseId);
                }

                if ($request->brand_id) {
                    $query->where('brands.id', $request->brand_id);
                }
                if ($request->store_id) {
                    $query->where('stock_entry_items.store_location_id', $request->store_id);
                }
                
                if ($search) {
                    $query->where('brands.brand_name', 'like', "%{$search}%");
                }

                $query->groupBy('brands.id', 'brands.brand_name');

                $recordsTotal = DB::query()->fromSub($query, 'sub')->count();
                $recordsFiltered = $recordsTotal;

                $totalsRow = DB::query()->fromSub($query, 'sub')->selectRaw('
                    SUM(CASE WHEN total_qty > 0 THEN total_qty ELSE 0 END) as sum_qty, 
                    SUM(CASE WHEN stock_value > 0 THEN stock_value ELSE 0 END) as sum_value
                ')->first();
                $totals = [
                    'total_qty' => number_format($totalsRow->sum_qty ?? 0, 0),
                    'stock_value' => '₹' . number_format($totalsRow->sum_value ?? 0, 2),
                ];

                if ($length != -1) {
                    $query->skip($start)->take($length);
                }

                $brandwiseStock = $query->get();
                $data = [];
                
                foreach ($brandwiseStock as $stock) {
                    $low_stock = max(0, ($stock->total_min_stock ?? 0) - ($stock->total_qty ?? 0));
                    $excess_stock = max(0, ($stock->total_qty ?? 0) - (($stock->total_min_stock ?? 0) * 2));
                    
                    $brandId = $stock->brand_id ?? 0;
                    $brandName = addslashes($stock->brand ?? '-');

                    $avgDays = $stock->avg_stock_days;
                    if ($avgDays !== null && $stock->total_qty > 0) {
                        $d = intval($avgDays);
                        $badgeClass = ($d <= 30) ? 'bg-label-success' : (($d <= 60) ? 'bg-label-info' : (($d <= 90) ? 'bg-label-warning' : 'bg-label-danger'));
                        $daysBadge = '<span class="badge ' . $badgeClass . ' fw-bold">' . $d . ' Days</span>';
                    } else {
                        $daysBadge = '-';
                    }

                    $data[] = [
                        'DT_RowAttr' => [
                            'onclick' => "drillDownToStyle({$brandId}, '{$brandName}')",
                            'style' => 'cursor: pointer;',
                            'data-brand-id' => $brandId,
                            'data-brand-name' => $stock->brand ?? '-'
                        ],
                        'brand' => '<strong class="text-uppercase">' . ($stock->brand ?? '-') . '</strong>',
                        'total_qty' => number_format($stock->total_qty ?? 0, 0),
                        'low_stock' => '-',
                        'excess_stock' => '-',
                        'stock_days' => $daysBadge,
                        'stock_value' => '₹' . number_format($stock->stock_value ?? 0, 2),
                    ];
                }

                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                    'totals' => $totals,
                ]);

            case 'assorted-stock':
                $brandIdFilter = $request->brand_id;
                $storeIdFilter = $request->store_id;
                $warehouseId = ($request->warehouse_id && $request->warehouse_id !== 'all') ? $request->warehouse_id : null;

                $subQuery = StockEntryItem::query()
                    ->select(
                        'stock_entry_items.brand_id',
                        'stock_entry_items.style_id',
                        'stock_entry_items.art_no',
                        'stock_entry_items.sleeve_type',
                        DB::raw("SUM(CASE WHEN stock_entry_items.size = '38' THEN stock_entry_items.qty_in - stock_entry_items.qty_out ELSE 0 END) as q38"),
                        DB::raw("SUM(CASE WHEN stock_entry_items.size = '40' THEN stock_entry_items.qty_in - stock_entry_items.qty_out ELSE 0 END) as q40"),
                        DB::raw("SUM(stock_entry_items.qty_in - stock_entry_items.qty_out) as total_qty")
                    )
                    ->where('stock_entry_items.stock_type', 'finished_goods')
                    ->whereNull('stock_entry_items.deleted_at');

                if ($warehouseId) {
                    $subQuery->where('stock_entry_items.warehouse_id', $warehouseId);
                }
                if ($storeIdFilter) {
                    $subQuery->where('stock_entry_items.store_location_id', $storeIdFilter);
                }
                if ($brandIdFilter) {
                    $subQuery->where('stock_entry_items.brand_id', $brandIdFilter);
                }

                $subQuery->groupBy(
                    'stock_entry_items.brand_id',
                    'stock_entry_items.style_id',
                    'stock_entry_items.art_no',
                    'stock_entry_items.sleeve_type'
                )->havingRaw('total_qty > 0 AND NOT (q38 > 0 AND q40 > 0)');

                $query = DB::query()->fromSub($subQuery, 'assorted')
                    ->join('brands', 'assorted.brand_id', '=', 'brands.id')
                    ->select(
                        'brands.id as brand_id',
                        'brands.brand_name',
                        DB::raw('COUNT(DISTINCT assorted.style_id) as style_count'),
                        DB::raw('COUNT(DISTINCT assorted.art_no) as art_count'),
                        DB::raw('SUM(assorted.total_qty) as assorted_qty')
                    );

                if ($search) {
                    $query->where('brands.brand_name', 'like', "%{$search}%");
                }

                $query->groupBy('brands.id', 'brands.brand_name');

                $recordsTotal = DB::query()->fromSub($query, 'sub')->count();
                $recordsFiltered = $recordsTotal;

                $totalQtySum = DB::query()->fromSub($query, 'sub')->sum('assorted_qty');

                $query->orderBy('assorted_qty', 'desc');

                if ($length != -1) {
                    $query->skip($start)->take($length);
                }

                $brandsList = $query->get();
                $data = [];

                foreach ($brandsList as $item) {
                    $bId = $item->brand_id;
                    $bName = addslashes($item->brand_name);
                    $data[] = [
                        'DT_RowAttr' => [
                            'onclick' => "drillDownToAssortedStyle({$bId}, '" . htmlspecialchars($bName, ENT_QUOTES) . "')",
                            'style' => 'cursor: pointer;'
                        ],
                        'brand' => '<strong class="text-uppercase text-primary">' . htmlspecialchars($item->brand_name) . '</strong>',
                        'style_count' => '<span class="badge bg-label-info">' . number_format($item->style_count ?? 0, 0) . ' Styles</span>',
                        'art_count' => '<span class="badge bg-label-secondary">' . number_format($item->art_count ?? 0, 0) . ' Art Nos</span>',
                        'stock_qty' => '<span class="badge bg-label-primary fs-6 fw-bold">' . number_format($item->assorted_qty ?? 0, 0) . '</span>',
                    ];
                }

                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                    'totals' => [
                        'stock_qty' => number_format($totalQtySum ?? 0, 0)
                    ]
                ]);

            case 'order-dispatch':
                $query = SalesOrder::with(['customer'])
                    ->leftJoin('sales_invoices', 'sales_orders.id', '=', 'sales_invoices.so_id')
                    ->leftJoin('sales_invoice_items', function($join) {
                        $join->on('sales_invoices.id', '=', 'sales_invoice_items.sales_invoice_id')
                             ->whereNull('sales_invoice_items.deleted_at');
                    })
                    ->select(
                        'sales_orders.id',
                        'sales_orders.customer_id',
                        'sales_orders.so_no',
                        'sales_orders.so_date',
                        'sales_orders.total_qty as ordered_qty',
                        DB::raw('COALESCE(SUM(CASE WHEN sales_invoice_items.is_extra = 1 THEN sales_invoice_items.quantity ELSE 0 END), 0) as open_order_qty'),
                        DB::raw('COALESCE(SUM(sales_invoice_items.quantity), 0) as dispatched_qty')
                    )
                    ->whereNull('sales_orders.deleted_at')
                    ->whereNull('sales_invoices.deleted_at');

                if ($request->from_date) {
                    $query->where('sales_orders.so_date', '>=', date('Y-m-d', strtotime($request->from_date)));
                }
                if ($request->to_date) {
                    $query->where('sales_orders.so_date', '<=', date('Y-m-d', strtotime($request->to_date)));
                }
                if ($request->brand_id) {
                    $query->whereExists(function ($q) use ($request) {
                        $q->select(DB::raw(1))
                            ->from('sales_order_items')
                            ->leftJoin('brands', function($join) {
                                $join->on('sales_order_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                                    ->whereRaw("NOT EXISTS (
                                    SELECT 1 FROM brands b2 
                                    WHERE sales_order_items.art_no LIKE CONCAT(b2.code, '%') 
                                    AND LENGTH(b2.code) > LENGTH(brands.code)
                                    )");
                            })
                            ->whereColumn('sales_order_items.sale_order_id', 'sales_orders.id')
                            ->where('brands.id', $request->brand_id);
                    });
                }
                if ($request->customer_id) {
                    $query->where('sales_orders.customer_id', $request->customer_id);
                }
                
                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('sales_orders.so_no', 'like', "%{$search}%")
                          ->orWhereHas('customer', function($q2) use ($search) {
                              $q2->where('name', 'like', "%{$search}%");
                          });
                    });
                }

                $query->groupBy('sales_orders.id', 'sales_orders.customer_id', 'sales_orders.so_no', 'sales_orders.so_date', 'sales_orders.total_qty')->orderBy('id','desc');

                $recordsTotal = DB::query()->fromSub($query, 'sub')->count();
                $recordsFiltered = $recordsTotal;

                if ($length != -1) {
                    $query->skip($start)->take($length);
                }

                $orderVsDispatch = $query->get();
                $data = [];
                
                foreach ($orderVsDispatch as $so) {
                    $pending_qty = max(0, $so->ordered_qty - $so->dispatched_qty);
                    $fulfillment_pc = $so->ordered_qty > 0 ? min(100, ($so->dispatched_qty / $so->ordered_qty) * 100) : 0;
                    
                    $pcClass = 'bg-danger';
                    if($fulfillment_pc >= 90) $pcClass = 'bg-success';
                    elseif($fulfillment_pc >= 50) $pcClass = 'bg-warning';

                    $fulfillmentHtml = '
                        <div class="d-flex align-items-center justify-content-center">
                            <span class="me-2 text-muted" style="font-size: 0.75rem;">' . number_format($fulfillment_pc, 1) . '%</span>
                            <div class="progress" style="height: 6px; width: 60px;">
                                <div class="progress-bar ' . $pcClass . '" role="progressbar" style="width: ' . $fulfillment_pc . '%" aria-valuenow="' . $fulfillment_pc . '" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>';

                    $openOrderQty = (int) $so->open_order_qty;

                    $data[] = [
                        'customer' => $so->customer->name ?? '-',
                        'so_no' => '<strong>' . $so->so_no . '</strong><br><span class="text-muted" style="font-size: 0.75rem;">' . date('d M Y', strtotime($so->so_date)) . '</span>',
                        'ordered_qty' => number_format($so->ordered_qty, 0),
                        'open_order_qty' => $openOrderQty > 0
                            ? '<span class="text-info fw-bold">' . number_format($openOrderQty, 0) . '</span>'
                            : '<span class="text-muted">0</span>',
                        'dispatched_qty' => '<span class="text-primary fw-bold">' . number_format($so->dispatched_qty, 0) . '</span>',
                        'pending_qty' => '<span class="' . ($pending_qty > 0 ? 'text-danger' : 'text-success') . ' fw-bold">' . number_format($pending_qty, 0) . '</span>',
                        'fulfillment' => $fulfillmentHtml
                    ];
                }

                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                ]);

            case 'dispatch-report':
                $query = SalesOrder::with(['customer.city', 'salesInvoices.items'])
                    ->where(function ($q) {
                        $q->where('status', 'Dispatched')
                          ->orWhereHas('salesInvoices');
                    })
                    ->whereNull('deleted_at');

                if ($request->from_date) {
                    $fromDate = date('Y-m-d', strtotime($request->from_date));
                    $query->where(function ($q) use ($fromDate) {
                        $q->whereHas('salesInvoices', function ($iq) use ($fromDate) {
                            $iq->where('inv_date', '>=', $fromDate);
                        })->orWhere('so_date', '>=', $fromDate);
                    });
                }
                if ($request->to_date) {
                    $toDate = date('Y-m-d', strtotime($request->to_date));
                    $query->where(function ($q) use ($toDate) {
                        $q->whereHas('salesInvoices', function ($iq) use ($toDate) {
                            $iq->where('inv_date', '<=', $toDate);
                        })->orWhere('so_date', '<=', $toDate);
                    });
                }
                if ($request->brand_id) {
                    $query->whereExists(function ($q) use ($request) {
                        $q->select(DB::raw(1))
                            ->from('sales_order_items')
                            ->leftJoin('brands', function($join) {
                                $join->on('sales_order_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                                    ->whereRaw("NOT EXISTS (
                                    SELECT 1 FROM brands b2 
                                    WHERE sales_order_items.art_no LIKE CONCAT(b2.code, '%') 
                                    AND LENGTH(b2.code) > LENGTH(brands.code)
                                    )");
                            })
                            ->whereColumn('sales_order_items.sale_order_id', 'sales_orders.id')
                            ->where('brands.id', $request->brand_id);
                    });
                }
                if ($request->customer_id) {
                    $query->where('sales_orders.customer_id', $request->customer_id);
                }
                
                if ($search) {
                    $rawSearch = $search;
                    $dbInvNo = SalesInvoice::formatDbInvNo($rawSearch);
                    $query->where(function ($q) use ($rawSearch, $dbInvNo) {
                        $q->where('so_no', 'like', "%{$rawSearch}%")
                          ->orWhereHas('customer', function($q2) use ($rawSearch) {
                              $q2->where('name', 'like', "%{$rawSearch}%");
                          })
                          ->orWhereHas('salesInvoices', function($iq) use ($rawSearch, $dbInvNo) {
                              $iq->where('inv_no', 'like', "%{$rawSearch}%")
                                ->orWhere('inv_no', 'like', "%{$dbInvNo}%");
                          });
                    });
                }

                $recordsTotal = $query->count();
                $recordsFiltered = $recordsTotal;

                if ($start >= $recordsTotal && $recordsTotal > 0) {
                    $start = 0;
                }

                if ($length != -1) {
                    $query->skip($start)->take($length);
                }

                $dispatchReport = $query->orderBy('id', 'desc')->get();
                $data = [];
                foreach ($dispatchReport as $so) {
                    $invoicesData = $so->salesInvoices->map(function($inv) {
                        return [
                            'id' => $inv->id,
                            'no' => SalesInvoice::formatDisplayInvNo($inv->inv_no),
                        ];
                    })->filter(function($item) {
                        return !empty($item['no']);
                    })->values();

                    if ($invoicesData->isNotEmpty()) {
                        $allInvoicesStr = $invoicesData->pluck('no')->implode(', ');
                        $chunks = $invoicesData->chunk(3);
                        $rowsHtml = [];
                        foreach ($chunks as $chunk) {
                            $rowBadges = [];
                            foreach ($chunk as $inv) {
                                $url = url('sales_invoices/view/' . $inv['id']);
                                $rowBadges[] = '<a href="' . $url . '" target="_blank" class="badge bg-label-info text-nowrap px-2 py-1 fw-semibold text-decoration-none" style="font-size: 0.72rem;" title="View Invoice">' . htmlspecialchars($inv['no']) . '</a>';
                            }
                            $rowsHtml[] = '<div class="d-flex flex-wrap gap-1 justify-content-center">' . implode('', $rowBadges) . '</div>';
                        }
                        $invoicesHtml = '<div class="d-flex flex-column gap-1 py-1" data-export="' . htmlspecialchars($allInvoicesStr) . '" style="max-height: 180px; overflow-y: auto; min-width: 260px; margin: 0 auto; padding: 4px 2px;">' . implode('', $rowsHtml) . '</div>';
                    } else {
                        $invoicesHtml = '-';
                    }

                    $latestInv = $so->salesInvoices->sortByDesc('inv_date')->first();
                    $displayDate = $latestInv && $latestInv->inv_date ? date('d M Y', strtotime($latestInv->inv_date)) : date('d M Y', strtotime($so->so_date));

                    $invoicedQty = $so->salesInvoices->sum(function($inv) {
                        return $inv->items->sum('quantity');
                    });
                    $dispatchedQty = $invoicedQty > 0 ? $invoicedQty : (float)$so->total_qty;

                    $data[] = [
                        'date' => $displayDate,
                        'so_no' => '<strong>' . $so->so_no . '</strong>',
                        'customer' => $so->customer->name ?? '-',
                        'destination' => $so->customer->city->city_name ?? '-',
                        'qty' => number_format($dispatchedQty, 0),
                        'invoices' => $invoicesHtml,
                        'status' => '<span class="badge bg-label-success"><i class="ri ri-check-double-line align-middle me-1"></i> Dispatched</span>'
                    ];
                }

                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                ]);

            case 'stock-inward':
                $fgStoreIds = StoreType::whereRaw("UPPER(TRIM(store_type_name)) = 'FINISHED GOODS'")->pluck('id')->toArray();
                if (empty($fgStoreIds)) {
                    $fgStoreIds = [3];
                }

                $query = DB::table('production_receipts')
                    ->leftJoin('job_card_entries', 'production_receipts.job_card_id', '=', 'job_card_entries.id')
                    ->leftJoin('service_providers', 'job_card_entries.service_provider_id', '=', 'service_providers.id')
                    ->leftJoin('warehouses', 'production_receipts.warehouse_id', '=', 'warehouses.id')
                    ->leftJoin('users', 'production_receipts.employee_id', '=', 'users.id')
                    ->leftJoin('production_receipt_items', 'production_receipts.id', '=', 'production_receipt_items.production_receipt_id')
                    ->whereIn('production_receipts.store_type_id', $fgStoreIds)
                    ->whereNull('job_card_entries.deleted_at')
                    ->select(
                        'production_receipts.id',
                        'production_receipts.receipt_no',
                        'production_receipts.receipt_date',
                        'job_card_entries.job_card_no',
                        'service_providers.name as plant_name',
                        'warehouses.warehouse_name',
                        'users.name as employee_name',
                        'users.emp_id',
                        DB::raw('COALESCE(SUM(production_receipt_items.qty_to_receive), 0) as total_qty')
                    );

                if ($request->from_date) {
                    $query->where('production_receipts.receipt_date', '>=', date('Y-m-d', strtotime($request->from_date)));
                }
                if ($request->to_date) {
                    $query->where('production_receipts.receipt_date', '<=', date('Y-m-d', strtotime($request->to_date)));
                }
                if ($request->brand_id) {
                    $query->where('job_card_entries.brand_id', $request->brand_id);
                }
                if ($request->warehouse_id && $request->warehouse_id !== 'all') {
                    $query->where('production_receipts.warehouse_id', $request->warehouse_id);
                }

                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('job_card_entries.job_card_no', 'like', "%{$search}%")
                          ->orWhere('service_providers.name', 'like', "%{$search}%")
                          ->orWhere('warehouses.warehouse_name', 'like', "%{$search}%")
                          ->orWhere('users.name', 'like', "%{$search}%")
                          ->orWhere('users.emp_id', 'like', "%{$search}%")
                          ->orWhere('production_receipts.receipt_no', 'like', "%{$search}%");
                    });
                }

                $query->groupBy(
                    'production_receipts.id',
                    'production_receipts.receipt_no',
                    'production_receipts.receipt_date',
                    'job_card_entries.job_card_no',
                    'service_providers.name',
                    'warehouses.warehouse_name',
                    'users.name',
                    'users.emp_id'
                );

                $recordsTotal = DB::query()->fromSub($query, 'sub')->count();
                $recordsFiltered = $recordsTotal;
                $overallTotalQty = DB::query()->fromSub($query, 'sub')->sum('total_qty');

                if ($length != -1) {
                    $query->skip($start)->take($length);
                }

                $stockInward = $query->orderBy('production_receipts.receipt_date', 'desc')
                                     ->orderBy('production_receipts.id', 'desc')
                                     ->get();
                $data = [];

                foreach ($stockInward as $row) {
                    $empText = $row->employee_name ?? '-';
                    if (!empty($row->employee_name) && !empty($row->emp_id)) {
                        $empText = $row->employee_name . ' <span class="text-muted">(' . $row->emp_id . ')</span>';
                    }

                    $data[] = [
                        'cutting_no' => '<strong>' . ($row->job_card_no ?? '-') . '</strong>',
                        'inward_date' => $row->receipt_date ? date('d-m-Y', strtotime($row->receipt_date)) : '-',
                        'unit_plant' => $row->plant_name ?? '-',
                        'warehouse' => $row->warehouse_name ?? '-',
                        'responsible_person' => $empText,
                        'total_qty' => number_format($row->total_qty, 0),
                    ];
                }

                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'total_qty' => $overallTotalQty,
                    'data' => $data,
                ]);

            case 'regular-discount':
                $query = SalesInvoice::query()
                    ->select(
                        DB::raw("DATE_FORMAT(inv_date, '%b %Y') as period"),
                        DB::raw("YEAR(inv_date) as yr"),
                        DB::raw("MONTH(inv_date) as mn"),
                        DB::raw("SUM(sub_total) as total_sales"),
                        DB::raw("SUM(CASE WHEN discount = 0 THEN sub_total ELSE 0 END) as regular_sales"),
                        DB::raw("SUM(CASE WHEN discount > 0 THEN sub_total ELSE 0 END) as discount_sales_raw"),
                        DB::raw("SUM(discount) as discount_amount"),
                        DB::raw("SUM(sub_total - discount) as net_revenue")
                    )
                    ->whereNull('deleted_at');

                if ($request->from_date) {
                    $query->where('inv_date', '>=', date('Y-m-d', strtotime($request->from_date)));
                }
                if ($request->to_date) {
                    $query->where('inv_date', '<=', date('Y-m-d', strtotime($request->to_date)));
                }
                if ($request->store_id) {
                    $query->where('store_id', $request->store_id);
                }
                if ($request->customer_id) {
                    $query->where('customer_id', $request->customer_id);
                }
                if ($request->brand_id) {
                    $query->whereExists(function ($q) use ($request) {
                        $q->select(DB::raw(1))
                            ->from('sales_invoice_items')
                            ->leftJoin('brands', function($join) {
                                $join->on('sales_invoice_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                                    ->whereRaw("NOT EXISTS (
                                    SELECT 1 FROM brands b2 
                                    WHERE sales_invoice_items.art_no LIKE CONCAT(b2.code, '%') 
                                    AND LENGTH(b2.code) > LENGTH(brands.code)
                                    )");
                            })
                            ->whereColumn('sales_invoice_items.sales_invoice_id', 'sales_invoices.id')
                            ->where('brands.id', $request->brand_id);
                    });
                }
                
                if ($search) {
                    $query->where(DB::raw("DATE_FORMAT(inv_date, '%b %Y')"), 'like', "%{$search}%");
                }

                $query->groupBy(DB::raw("DATE_FORMAT(inv_date, '%b %Y')"), DB::raw("YEAR(inv_date)"), DB::raw("MONTH(inv_date)"));

                $recordsTotal = DB::query()->fromSub($query, 'sub')->count();
                $recordsFiltered = $recordsTotal;

                if ($length != -1) {
                    $query->skip($start)->take($length);
                }

                $regularDiscount = $query->orderBy('yr', 'desc')->orderBy('mn', 'desc')->get();
                $data = [];
                
                foreach ($regularDiscount as $item) {
                    $discount_pc = $item->discount_sales_raw > 0 ? ($item->discount_amount / $item->discount_sales_raw) * 100 : 0;
                    
                    $data[] = [
                        'period' => '<strong>' . $item->period . '</strong>',
                        'total_sales' => '₹' . number_format($item->total_sales ?? 0, 2),
                        'regular_sales' => '₹' . number_format($item->regular_sales ?? 0, 2),
                        'discount_amount' => '₹' . number_format($item->discount_amount ?? 0, 2),
                        'discount_pc' => '<span class="text-danger">' . number_format($discount_pc, 1) . '%</span>',
                        'net_revenue' => '<span class="text-success fw-bold">₹' . number_format($item->net_revenue ?? 0, 2) . '</span>'
                    ];
                }

                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                ]);

            case 'urgent-orders':
                $query = SalesOrder::with(['customer'])
                    ->leftJoin('sales_invoices', 'sales_orders.id', '=', 'sales_invoices.so_id')
                    ->leftJoin('sales_invoice_items', 'sales_invoices.id', '=', 'sales_invoice_items.sales_invoice_id')
                    ->select(
                        'sales_orders.id',
                        'sales_orders.customer_id',
                        'sales_orders.so_no',
                        'sales_orders.so_date',
                        'sales_orders.request_date',
                        'sales_orders.delivery_date',
                        'sales_orders.orderaxe_id',
                        'sales_orders.orderaxe_ref_id',
                        'sales_orders.order_no',
                        'sales_orders.status',
                        'sales_orders.total_qty as ordered_qty',
                        DB::raw('COALESCE(SUM(sales_invoice_items.quantity), 0) as dispatched_qty')
                    )
                    ->where('sales_orders.status', 'Pending')
                    ->whereNull('sales_orders.deleted_at')
                    ->whereNull('sales_invoices.deleted_at')
                    ->whereNull('sales_invoice_items.deleted_at');

                if ($request->from_date) {
                    $query->where(DB::raw('COALESCE(NULLIF(sales_orders.request_date, "0000-00-00"), sales_orders.so_date)'), '>=', date('Y-m-d', strtotime($request->from_date)));
                }
                if ($request->to_date) {
                    $query->where(DB::raw('COALESCE(NULLIF(sales_orders.request_date, "0000-00-00"), sales_orders.so_date)'), '<=', date('Y-m-d', strtotime($request->to_date)));
                }
                if ($request->store_id) {
                    $query->where('sales_orders.store_id', $request->store_id);
                }
                if ($request->customer_id) {
                    $query->where('sales_orders.customer_id', $request->customer_id);
                }
                if ($request->brand_id) {
                    $query->whereExists(function ($q) use ($request) {
                        $q->select(DB::raw(1))
                            ->from('sales_order_items')
                            ->leftJoin('brands', function($join) {
                                $join->on('sales_order_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                                    ->whereRaw("NOT EXISTS (
                                    SELECT 1 FROM brands b2 
                                    WHERE sales_order_items.art_no LIKE CONCAT(b2.code, '%') 
                                    AND LENGTH(b2.code) > LENGTH(brands.code)
                                    )");
                            })
                            ->whereColumn('sales_order_items.sale_order_id', 'sales_orders.id')
                            ->where('brands.id', $request->brand_id);
                    });
                }

                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('sales_orders.so_no', 'like', "%{$search}%")
                          ->orWhere('sales_orders.orderaxe_ref_id', 'like', "%{$search}%")
                          ->orWhere('sales_orders.order_no', 'like', "%{$search}%")
                          ->orWhereHas('customer', function($q2) use ($search) {
                              $q2->where('name', 'like', "%{$search}%");
                          });
                    });
                }

                $query->groupBy(
                    'sales_orders.id',
                    'sales_orders.customer_id',
                    'sales_orders.so_no',
                    'sales_orders.so_date',
                    'sales_orders.request_date',
                    'sales_orders.delivery_date',
                    'sales_orders.orderaxe_id',
                    'sales_orders.orderaxe_ref_id',
                    'sales_orders.order_no',
                    'sales_orders.status',
                    'sales_orders.total_qty'
                );

                $recordsTotal = DB::query()->fromSub($query, 'sub')->count();
                $recordsFiltered = $recordsTotal;

                if ($length != -1) {
                    $query->skip($start)->take($length);
                }

                $urgentOrders = $query->orderBy('sales_orders.id', 'desc')->get();
                $soIds = $urgentOrders->pluck('id')->toArray();

                $allBrands = Brand::orderByRaw('LENGTH(code) DESC')->orderByRaw('LENGTH(brand_name) DESC')->get();

                $soItemsList = DB::table('sales_order_items')
                    ->leftJoin('stock_entry_items', 'sales_order_items.stock_entry_item_id', '=', 'stock_entry_items.id')
                    ->leftJoin('brands as b_stock', 'stock_entry_items.brand_id', '=', 'b_stock.id')
                    ->leftJoin('brand_categories', 'sales_order_items.brand_cat_id', '=', 'brand_categories.id')
                    ->select(
                        'sales_order_items.sale_order_id',
                        'sales_order_items.art_no',
                        'sales_order_items.sku',
                        'sales_order_items.item_name',
                        'b_stock.brand_name as stock_brand_name',
                        'brand_categories.name as cat_brand_name'
                    )
                    ->whereIn('sales_order_items.sale_order_id', $soIds)
                    ->whereNull('sales_order_items.deleted_at')
                    ->get();

                $itemsWithBrands = [];
                foreach ($soItemsList as $soItem) {
                    $foundBrand = null;
                    if (!empty($soItem->stock_brand_name)) {
                        $foundBrand = $soItem->stock_brand_name;
                    } elseif (!empty($soItem->cat_brand_name)) {
                        $foundBrand = $soItem->cat_brand_name;
                    } else {
                        $artNo = trim($soItem->art_no ?? '');
                        $sku = trim($soItem->sku ?? '');
                        $itemName = trim($soItem->item_name ?? '');

                        foreach ($allBrands as $b) {
                            $bCode = trim($b->code);
                            $bName = trim($b->brand_name);

                            if (($bCode && $artNo && stripos($artNo, $bCode) === 0) ||
                                ($bCode && $sku && stripos($sku, $bCode) === 0) ||
                                ($bName && $artNo && stripos($artNo, $bName) !== false) ||
                                ($bName && $sku && stripos($sku, $bName) !== false) ||
                                ($bName && $itemName && stripos($itemName, $bName) !== false)) {
                                $foundBrand = $b->brand_name;
                                break;
                            }
                        }
                    }

                    if ($foundBrand) {
                        $itemsWithBrands[$soItem->sale_order_id][$foundBrand] = true;
                    }
                }

                $stockMap = DB::table('stock_entry_items')->select('art_no', DB::raw('SUM(qty_in - qty_out) as current_stock'))->where('stock_type', 'finished_goods')->whereNull('deleted_at')->groupBy('art_no')->pluck('current_stock', 'art_no');

                $soItems = DB::table('sales_order_items')->select('sale_order_id', 'art_no', 'qty')->whereIn('sale_order_id', $soIds)->whereNull('deleted_at')->get()->groupBy('sale_order_id');

                $data = [];
                $today = \Carbon\Carbon::today();

                foreach ($urgentOrders as $so) {
                    $pending_qty = max(0, $so->ordered_qty - $so->dispatched_qty);

                    $soNoHtml = '<a href="' . url('sales_orders/view/' . $so->id) . '" target="_blank" class="fw-bold text-primary" data-bs-toggle="tooltip" title="Click to view full Sales Order">' . $so->so_no . '</a>';
                    if (!empty($so->orderaxe_id) || !empty($so->orderaxe_ref_id) || !empty($so->order_no)) {
                        $soNoHtml .= ' <span class="badge bg-label-info ms-1" style="font-size:10px;">Orderaxe</span>';
                    }
                    if (!empty($so->order_no)) {
                        $soNoHtml .= '<br><span class="text-muted small" style="font-size: 0.75rem;">Ref: ' . $so->order_no . '</span>';
                    } elseif (!empty($so->orderaxe_ref_id)) {
                        $soNoHtml .= '<br><span class="text-muted small" style="font-size: 0.75rem;">Ref: ' . $so->orderaxe_ref_id . '</span>';
                    }

                    $soDateFormatted = !empty($so->so_date) ? date('d M Y', strtotime($so->so_date)) : '-';
                    $effectiveOrderDate = (!empty($so->request_date) && $so->request_date != '0000-00-00') ? $so->request_date : $so->so_date;
                    $orderDateFormatted = !empty($effectiveOrderDate) ? date('d M Y', strtotime($effectiveOrderDate)) : '-';
                    $daysPending = $today->diffInDays(\Carbon\Carbon::parse($effectiveOrderDate));
                    $daysPendingHtml = '<span class="badge bg-label-warning text-dark fw-bold px-2 py-1" style="font-size: 11px;">' . $daysPending . ' ' . ($daysPending == 1 ? 'Day' : 'Days') . '</span>';

                    $deliveryHtml = '-';
                    if (!empty($so->delivery_date)) {
                        $delDate = \Carbon\Carbon::parse($so->delivery_date);
                        $diffDays = $today->diffInDays($delDate, false);
                        if ($diffDays < 0) {
                            $deliveryHtml = '<span class="text-danger fw-bold">' . date('d M Y', strtotime($so->delivery_date)) . '</span><br><span class="badge bg-label-danger" style="font-size:10px;">' . abs($diffDays) . ' Days Overdue</span>';
                        } elseif ($diffDays == 0) {
                            $deliveryHtml = '<span class="text-warning fw-bold">' . date('d M Y', strtotime($so->delivery_date)) . '</span><br><span class="badge bg-label-warning" style="font-size:10px;">Due Today</span>';
                        } else {
                            $deliveryHtml = '<span class="text-dark">' . date('d M Y', strtotime($so->delivery_date)) . '</span><br><span class="badge bg-label-success" style="font-size:10px;">Due in ' . $diffDays . ' Days</span>';
                        }
                    }

                    $brandList = isset($itemsWithBrands[$so->id]) ? array_keys($itemsWithBrands[$so->id]) : [];
                    $brandBadges = [];
                    foreach ($brandList as $bName) {
                        $brandBadges[] = '<span class="badge bg-label-primary mb-1 me-1">' . htmlspecialchars($bName) . '</span>';
                    }
                    $brandHtml = !empty($brandBadges) ? implode('', $brandBadges) : '<span class="text-muted">-</span>';

                    $actionHtml = '<a href="' . url('sales_orders/view/' . $so->id) . '" target="_blank" class="btn btn-sm btn-primary rounded-pill"><i class="ri ri-eye-line me-1"></i>View Order</a>';

                    $data[] = [
                        'so_no' => $soNoHtml,
                        'customer' => $so->customer->name ?? '-',
                        'so_date' => $soDateFormatted,
                        'order_date' => $orderDateFormatted,
                        'days_pending' => $daysPendingHtml,
                        'delivery_date' => $deliveryHtml,
                        'brands' => $brandHtml,
                        'ordered_qty' => number_format($so->ordered_qty, 0),
                        'action' => $actionHtml
                    ];
                }

                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                ]);

            case 'brandwise-lost-sales':
                $fromDate = $request->from_date ? date('Y-m-d', strtotime(str_replace('/', '-', $request->from_date))) : '2026-04-01';
                $toDate = $request->to_date ? date('Y-m-d', strtotime(str_replace('/', '-', $request->to_date))) : date('Y-m-d');

                $allBrands = Brand::where('status', 'Active')->orderByRaw('LENGTH(code) DESC')->orderByRaw('LENGTH(brand_name) DESC')->get();

                $orderQuery = DB::table('sales_order_items')
                    ->join('sales_orders', 'sales_order_items.sale_order_id', '=', 'sales_orders.id')
                    ->leftJoin('stock_entry_items', 'sales_order_items.stock_entry_item_id', '=', 'stock_entry_items.id')
                    ->leftJoin('brands as b_stock', 'stock_entry_items.brand_id', '=', 'b_stock.id')
                    ->leftJoin('brand_categories', 'sales_order_items.brand_cat_id', '=', 'brand_categories.id')
                    ->select(
                        'sales_orders.id as so_id',
                        'sales_order_items.art_no',
                        'sales_order_items.sku',
                        'sales_order_items.item_name',
                        'sales_order_items.category_name',
                        'sales_order_items.categories_path_val',
                        'sales_order_items.qty',
                        'sales_order_items.amount',
                        'b_stock.brand_name as stock_brand_name',
                        'b_stock.id as stock_brand_id',
                        'brand_categories.name as cat_brand_name'
                    )
                    ->whereNull('sales_orders.deleted_at')
                    ->whereNull('sales_order_items.deleted_at');

                if ($fromDate) {
                    $orderQuery->where('sales_orders.so_date', '>=', $fromDate);
                }
                if ($toDate) {
                    $orderQuery->where('sales_orders.so_date', '<=', $toDate);
                }
                if ($request->store_id) {
                    $orderQuery->where('sales_orders.store_id', $request->store_id);
                }
                if ($request->customer_id) {
                    $orderQuery->where('sales_orders.customer_id', $request->customer_id);
                }

                $orderItems = $orderQuery->get();

                // Pre-build brand map — resolve each unique art_no combo only once
                $brandMap = $this->buildArtNoBrandMap($orderItems, $allBrands);

                $brandSoTotals = [];
                $soIdsList = [];

                foreach ($orderItems as $item) {
                    $resolved = $brandMap[spl_object_id($item)] ?? null;

                    if (!$resolved || empty($resolved['name']) || strtolower($resolved['name']) === 'uncategorized') {
                        continue; 
                    }

                    $brandName = $resolved['name'];
                    $brandId = $resolved['id'];

                    if ($request->brand_id && $brandId != $request->brand_id) {
                        continue;
                    }

                    $soId = $item->so_id;
                    $soIdsList[] = $soId;

                    if (!isset($brandSoTotals[$brandName][$soId])) {
                        $brandSoTotals[$brandName][$soId] = ['ordered_qty' => 0, 'ordered_value' => 0];
                    }
                    $brandSoTotals[$brandName][$soId]['ordered_qty'] += floatval($item->qty ?? 0);
                    $brandSoTotals[$brandName][$soId]['ordered_value'] += floatval($item->amount ?? 0);
                }

                $soInvoiceMap = [];
                if (!empty($soIdsList)) {
                    $invoices = DB::table('sales_invoice_items')
                        ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
                        ->whereIn('sales_invoices.so_id', array_unique($soIdsList))
                        ->whereNull('sales_invoices.deleted_at')
                        ->whereNull('sales_invoice_items.deleted_at')
                        ->select(
                            'sales_invoices.so_id',
                            DB::raw('SUM(sales_invoice_items.quantity) as invoiced_qty'),
                            DB::raw('SUM(sales_invoice_items.amount) as invoiced_value')
                        )
                        ->groupBy('sales_invoices.so_id')
                        ->get();

                    foreach ($invoices as $inv) {
                        $soInvoiceMap[$inv->so_id] = [
                            'invoiced_qty' => floatval($inv->invoiced_qty ?? 0),
                            'invoiced_value' => floatval($inv->invoiced_value ?? 0),
                        ];
                    }
                }

                $data = [];
                foreach ($brandSoTotals as $bName => $soList) {
                    if ($search && stripos($bName, $search) === false) {
                        continue;
                    }

                    $missedOrdersCount = 0;
                    $totalLostValue = 0;

                    foreach ($soList as $soId => $totals) {
                        $ordQty = $totals['ordered_qty'];
                        $ordVal = $totals['ordered_value'];
                        $invQty = floatval($soInvoiceMap[$soId]['invoiced_qty'] ?? 0);
                        $invVal = floatval($soInvoiceMap[$soId]['invoiced_value'] ?? 0);

                        $lostQty = max(0, $ordQty - $invQty);
                        $lostVal = max(0, $ordVal - $invVal);

                        if ($lostQty > 0 || $ordQty > $invQty) {
                            $missedOrdersCount++;
                            $totalLostValue += $lostVal;
                        }
                    }

                    if ($missedOrdersCount > 0 || $totalLostValue > 0) {
                        $data[] = [
                            'DT_RowAttr' => [
                                'onclick' => "drillDownToLostSalesBrand('" . addslashes($bName) . "')",
                                'style' => 'cursor: pointer;',
                                'class' => 'clickable-brand-row'
                            ],
                            'brand' => '<strong class="text-uppercase text-primary">' . htmlspecialchars($bName) . ' <i class="ri ri-arrow-right-s-line ms-1 small"></i></strong>',
                            'missed_orders' => '<span class="badge bg-label-danger px-3 py-1 fs-6 fw-bold">' . number_format($missedOrdersCount, 0) . '</span>',
                            'lost_value' => '<span class="text-danger fw-bold fs-6">₹' . number_format($totalLostValue, 2) . '</span>'
                        ];
                    }
                }

                $recordsTotal = count($data);
                $recordsFiltered = $recordsTotal;

                if ($length != -1) {
                    $data = array_slice($data, $start, $length);
                }

                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                ]);

           
            case 'order-processing-time':
            case 'order-processing-summary':
                $fromDate = $request->from_date ? date('Y-m-d', strtotime($request->from_date)) : null;
                $toDate = $request->to_date ? date('Y-m-d', strtotime($request->to_date)) : null;
                $brandIdFilter = $request->brand_id;
                $storeIdFilter = $request->store_id;
                $searchValue = $request->search['value'] ?? null;

                $allBrands = Brand::orderByRaw('LENGTH(code) DESC')->orderByRaw('LENGTH(brand_name) DESC')->get();

                $query = DB::table('sales_orders')
                    ->leftJoin('customers', 'sales_orders.customer_id', '=', 'customers.id')
                    ->leftJoin('places', 'customers.place_id', '=', 'places.id')
                    ->leftJoin('cities', 'customers.city_id', '=', 'cities.id')
                    ->select(
                        'sales_orders.id as so_id',
                        'sales_orders.so_no',
                        'sales_orders.so_date',
                        'sales_orders.request_date',
                        'sales_orders.delivery_date',
                        'sales_orders.order_type',
                        'sales_orders.order_no',
                        'sales_orders.reason_for_delay',
                        'sales_orders.internal_remarks',
                        'customers.name as customer_name',
                        'places.place_name as place_name',
                        'cities.city_name as city_name'
                    )
                    ->whereNull('sales_orders.deleted_at');

                if ($fromDate) {
                    $query->where('sales_orders.so_date', '>=', $fromDate);
                }
                if ($toDate) {
                    $query->where('sales_orders.so_date', '<=', $toDate);
                }
                if ($storeIdFilter) {
                    $query->where('sales_orders.store_id', $storeIdFilter);
                }
                if ($request->customer_id) {
                    $query->where('sales_orders.customer_id', $request->customer_id);
                }
                if ($brandIdFilter) {
                    $query->whereExists(function ($q) use ($brandIdFilter) {
                        $q->select(DB::raw(1))
                            ->from('sales_order_items')
                            ->leftJoin('brands', function($join) {
                                $join->on('sales_order_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                                    ->whereRaw("NOT EXISTS (
                                    SELECT 1 FROM brands b2 
                                    WHERE sales_order_items.art_no LIKE CONCAT(b2.code, '%') 
                                    AND LENGTH(b2.code) > LENGTH(brands.code)
                                    )");
                            })
                            ->whereColumn('sales_order_items.sale_order_id', 'sales_orders.id')
                            ->where('brands.id', $brandIdFilter);
                    });
                }
                if ($searchValue) {
                    $query->where(function($q) use ($searchValue) {
                        $q->where('sales_orders.so_no', 'LIKE', "%{$searchValue}%")
                          ->orWhere('customers.name', 'LIKE', "%{$searchValue}%")
                          ->orWhere('places.place_name', 'LIKE', "%{$searchValue}%")
                          ->orWhere('cities.city_name', 'LIKE', "%{$searchValue}%");
                    });
                }

                $recordsTotal = (clone $query)->count();
                $recordsFiltered = $recordsTotal;

                $salesOrders = $query->orderBy('sales_orders.so_date', 'desc')->orderBy('sales_orders.id', 'desc')->skip($start)->take($length)->get();

                $soIds = $salesOrders->pluck('so_id')->toArray();

                $soItemsList = [];
                if (!empty($soIds)) {
                    $soItemsList = DB::table('sales_order_items')
                        ->leftJoin('stock_entry_items', 'sales_order_items.stock_entry_item_id', '=', 'stock_entry_items.id')
                        ->leftJoin('brands as b_stock', 'stock_entry_items.brand_id', '=', 'b_stock.id')
                        ->leftJoin('brand_categories', 'sales_order_items.brand_cat_id', '=', 'brand_categories.id')
                        ->select(
                            'sales_order_items.sale_order_id',
                            'sales_order_items.art_no',
                            'sales_order_items.sku',
                            'sales_order_items.item_name',
                            'sales_order_items.category_name',
                            'sales_order_items.qty',
                            'b_stock.brand_name as stock_brand_name',
                            'b_stock.id as stock_brand_id',
                            'brand_categories.name as cat_brand_name'
                        )
                        ->whereIn('sales_order_items.sale_order_id', $soIds)
                        ->whereNull('sales_order_items.deleted_at')
                        ->get();
                }

                $soArtNoInvoicedMap = [];
                if (!empty($soIds)) {
                    $invoices = DB::table('sales_invoice_items')
                        ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
                        ->whereIn('sales_invoices.so_id', $soIds)
                        ->whereNull('sales_invoices.deleted_at')
                        ->whereNull('sales_invoice_items.deleted_at')
                        ->select(
                            'sales_invoices.so_id',
                            'sales_invoice_items.art_no',
                            DB::raw('SUM(sales_invoice_items.quantity) as inv_qty')
                        )
                        ->groupBy('sales_invoices.so_id', 'sales_invoice_items.art_no')
                        ->get();

                    foreach ($invoices as $inv) {
                        $soId = $inv->so_id;
                        $qty = floatval($inv->inv_qty ?? 0);
                        if ($inv->art_no) {
                            $soArtNoInvoicedMap[$soId][$inv->art_no] = ($soArtNoInvoicedMap[$soId][$inv->art_no] ?? 0) + $qty;
                        }
                    }
                }

                $data = [];
                $snoCounter = $start + 1;

                foreach ($salesOrders as $so) {
                    $itemsForSo = array_filter($soItemsList->toArray(), function($item) use ($so) {
                        return $item->sale_order_id == $so->so_id;
                    });

                    $brands = [];
                    $totalOrderedQty = 0;
                    $totalInvoicedQty = 0;
                    $pendingArtNos = [];

                    foreach ($itemsForSo as $itm) {
                        $bName = null;
                        if (!empty($itm->stock_brand_name)) {
                            $bName = $itm->stock_brand_name;
                        } elseif (!empty($itm->cat_brand_name)) {
                            $bName = $itm->cat_brand_name;
                        } else {
                            $artNo = trim($itm->art_no ?? '');
                            $sku = trim($itm->sku ?? '');
                            $itemName = trim($itm->item_name ?? '');
                            $catName = trim($itm->category_name ?? '');
                            foreach ($allBrands as $b) {
                                $bCode = trim($b->code ?? '');
                                $bNameCandidate = trim($b->brand_name ?? '');
                                $isMatched = false;

                                if ($bCode) {
                                    if (($artNo && stripos($artNo, $bCode) === 0) ||
                                        ($sku && stripos($sku, $bCode) === 0) ||
                                        ($itemName && (stripos($itemName, $bCode) === 0 || stripos($itemName, $bCode . '-') !== false || stripos($itemName, $bCode . ' ') !== false || stripos($itemName, $bCode . '_') !== false)) ||
                                        ($catName && (stripos($catName, $bCode) === 0 || stripos($catName, $bCode . '-') !== false || stripos($catName, $bCode . ' ') !== false || stripos($catName, $bCode . '_') !== false))) {
                                        $isMatched = true;
                                    }
                                }
                                if (!$isMatched && $bNameCandidate) {
                                    if (($artNo && stripos($artNo, $bNameCandidate) !== false) ||
                                        ($sku && stripos($sku, $bNameCandidate) !== false) ||
                                        ($itemName && stripos($itemName, $bNameCandidate) !== false) ||
                                        ($catName && stripos($catName, $bNameCandidate) !== false)) {
                                        $isMatched = true;
                                    }
                                }

                                if ($isMatched) {
                                    $bName = $b->brand_name;
                                    break;
                                }
                            }
                        }
                        if (!$bName) {
                            $bName = 'Uncategorized';
                        }
                        $brands[$bName] = true;

                        $ordQ = floatval($itm->qty ?? 0);
                        $invQ = floatval($soArtNoInvoicedMap[$so->so_id][$itm->art_no] ?? 0);
                        $totalOrderedQty += $ordQ;
                        $totalInvoicedQty += min($ordQ, $invQ);

                        if ($ordQ > $invQ && !empty($itm->art_no)) {
                            $pendingArtNos[] = $itm->art_no;
                        }
                    }

                    $pendingQty = max(0, $totalOrderedQty - $totalInvoicedQty);
                    $completionPct = $totalOrderedQty > 0 ? min(100, round(($totalInvoicedQty / $totalOrderedQty) * 100, 2)) : 0;

                    $brandBadges = [];
                    foreach (array_keys($brands) as $bName) {
                        $brandBadges[] = '<span class="badge bg-label-primary mb-1 me-1" style="font-size:10px;">' . htmlspecialchars($bName) . '</span>';
                    }
                    $brandHtml = !empty($brandBadges) ? implode('', $brandBadges) : '<span class="text-muted">-</span>';

                    $priority = strtoupper($so->order_type ?: 'REGULAR');
                    $priorityClass = ($priority == 'URGENT') ? 'bg-label-danger' : (($priority == 'SAMPLE') ? 'bg-label-info' : 'bg-label-secondary');
                    $priorityHtml = '<span class="badge ' . $priorityClass . ' font-monospace" style="font-size:11px;">' . $priority . '</span>';

                    $pcBadgeStyle = 'background-color: #fef9c3; color: #854d0e;';
                    if ($completionPct < 80) {
                        $pcBadgeStyle = 'background-color: #f8d7da; color: #842029;';
                    } elseif ($completionPct >= 90) {
                        $pcBadgeStyle = 'background-color: #d1e7dd; color: #0f5132;';
                    }
                    $completionHtml = '<span class="badge px-2 py-1 fw-bold" style="' . $pcBadgeStyle . ' font-size: 11px;">' . $completionPct . '%</span>';

                    if ($completionPct >= 100) {
                        $statusHtml = '<span class="badge bg-success text-white fw-bold px-2 py-1" style="font-size:10px;">COMPLETED</span>';
                    } elseif ($totalInvoicedQty > 0) {
                        $statusHtml = '<span class="badge bg-danger text-white fw-bold px-2 py-1" style="background-color:#e63946 !important; font-size:10px;">PARTIAL DELIVERY</span>';
                    } else {
                        $statusHtml = '<span class="badge bg-danger text-white fw-bold px-2 py-1" style="background-color:#e63946 !important; font-size:10px;">PENDING</span>';
                    }

                    $soNoHtml = '<a href="' . url('sales_orders/view/' . $so->so_id) . '" target="_blank" class="fw-bold text-primary">' . htmlspecialchars($so->so_no) . '</a>';

                    $uniquePendingArtNos = array_unique($pendingArtNos);
                    if (!empty($uniquePendingArtNos)) {
                        $countArtNos = count($uniquePendingArtNos);
                        $awaitingHtml = '<button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 text-nowrap view-awaiting-art-nos" data-so-id="' . $so->so_id . '" data-so-no="' . htmlspecialchars($so->so_no) . '" data-count="' . $countArtNos . '" data-bs-toggle="tooltip" title="Click to view pending Art Nos hierarchy"><i class="ri ri-eye-line me-1"></i>View Art No (' . $countArtNos . ')</button>';
                    } else {
                        $awaitingHtml = '<span class="text-success small fw-bold"><i class="ri ri-check-double-line me-1"></i>None / Closed</span>';
                    }

                    $place = $so->place_name ?: ($so->city_name ?: '-');

                    $data[] = [
                        'date' => date('d-m-Y', strtotime($so->so_date)),
                        'sno' => $snoCounter++,
                        'request_date' => !empty($so->request_date) ? date('d-m-Y', strtotime($so->request_date)) : (!empty($so->so_date) ? date('d-m-Y', strtotime($so->so_date)) : '-'),
                        'so_no' => $soNoHtml,
                        'orderaxe_order_no' => htmlspecialchars($so->order_no ?: '-'),
                        'delivery_date' => !empty($so->delivery_date) ? date('d-m-Y', strtotime($so->delivery_date)) : '-',
                        'priority' => $priorityHtml,
                        'customer' => htmlspecialchars($so->customer_name ?? '-'),
                        'place' => htmlspecialchars($place),
                        'brand' => $brandHtml,
                        'ordered_qty' => number_format($totalOrderedQty, 0),
                        'invoiced_qty' => number_format($totalInvoicedQty, 0),
                        'pending_qty' => '<span class="' . ($pendingQty > 0 ? 'text-danger fw-bold' : 'text-success fw-bold') . '">' . number_format($pendingQty, 0) . '</span>',
                        'completion' => $completionHtml,
                        'status' => $statusHtml,
                        'awaiting_art_nos' => $awaitingHtml,
                        'wip' => '-',
                        'action_required' => htmlspecialchars($so->reason_for_delay ?: '-')
                    ];
                }

                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                ]);

            case 'brandwise-lost-sales':
                $fromDate = $request->from_date ? date('Y-m-d', strtotime($request->from_date)) : null;
                $toDate = $request->to_date ? date('Y-m-d', strtotime($request->to_date)) : null;
                $storeId = $request->store_id;
                $brandIdFilter = $request->brand_id;

                $allBrands = DB::table('brands')->get();

                $orderQuery = DB::table('sales_order_items')
                    ->join('sales_orders', 'sales_order_items.sale_order_id', '=', 'sales_orders.id')
                    ->leftJoin('stock_entry_items', 'sales_order_items.stock_entry_item_id', '=', 'stock_entry_items.id')
                    ->leftJoin('brands', 'stock_entry_items.brand_id', '=', 'brands.id')
                    ->leftJoin('brand_categories', 'sales_order_items.brand_cat_id', '=', 'brand_categories.id')
                    ->whereNull('sales_orders.deleted_at')
                    ->whereNull('sales_order_items.deleted_at')
                    ->select(
                        'sales_order_items.sale_order_id',
                        'sales_order_items.art_no',
                        'sales_order_items.sku',
                        'sales_order_items.item_name',
                        'sales_order_items.qty',
                        'sales_order_items.amount',
                        'brands.id as stock_brand_id',
                        'brands.brand_name as stock_brand_name',
                        'brand_categories.name as cat_brand_name'
                    );

                if ($fromDate && $toDate) {
                    $orderQuery->whereBetween('sales_orders.so_date', [$fromDate, $toDate]);
                }
                if ($storeId) {
                    $orderQuery->where('sales_orders.store_id', $storeId);
                }

                $orderItems = $orderQuery->get();

                $brandMap = [];
                foreach ($orderItems as $item) {
                    $brandName = null;
                    $brandId = null;

                    if (!empty($item->stock_brand_name)) {
                        $brandName = $item->stock_brand_name;
                        $brandId = $item->stock_brand_id;
                    } elseif (!empty($item->cat_brand_name)) {
                        $brandName = $item->cat_brand_name;
                    } else {
                        $artNo = trim($item->art_no ?? '');
                        $sku = trim($item->sku ?? '');
                        $itemName = trim($item->item_name ?? '');

                        foreach ($allBrands as $b) {
                            $bCode = trim($b->code ?? '');
                            $bNameCandidate = trim($b->brand_name ?? '');
                            $isMatched = false;

                            if ($bCode) {
                                if (($artNo && stripos($artNo, $bCode) === 0) ||
                                    ($sku && stripos($sku, $bCode) === 0) ||
                                    ($itemName && (stripos($itemName, $bCode) === 0 || stripos($itemName, $bCode . '-') !== false || stripos($itemName, $bCode . ' ') !== false || stripos($itemName, $bCode . '_') !== false))) {
                                    $isMatched = true;
                                }
                            }
                            if (!$isMatched && $bNameCandidate) {
                                if (($artNo && stripos($artNo, $bNameCandidate) !== false) ||
                                    ($sku && stripos($sku, $bNameCandidate) !== false) ||
                                    ($itemName && stripos($itemName, $bNameCandidate) !== false)) {
                                    $isMatched = true;
                                }
                            }

                            if ($isMatched) {
                                $brandName = $b->brand_name;
                                $brandId = $b->id;
                                break;
                            }
                        }
                    }

                    if (!$brandName || $brandName === 'Unassigned Brand') {
                        continue;
                    }

                    if ($brandIdFilter && $brandId != $brandIdFilter) {
                        continue;
                    }

                    if (!isset($brandMap[$brandName])) {
                        $brandMap[$brandName] = [
                            'brand_id' => $brandId,
                            'brand_name' => $brandName,
                            'so_ids' => [],
                            'items' => []
                        ];
                    }

                    $soId = $item->sale_order_id;
                    $brandMap[$brandName]['so_ids'][$soId] = true;
                    $brandMap[$brandName]['items'][] = $item;
                }

                $data = [];
                foreach ($brandMap as $bName => $bData) {
                    $soIds = array_keys($bData['so_ids']);
                    $invoicedArtNos = [];

                    if (!empty($soIds)) {
                        $invoicedArtNos = DB::table('sales_invoice_items')
                            ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
                            ->whereIn('sales_invoices.so_id', $soIds)
                            ->whereNull('sales_invoices.deleted_at')
                            ->whereNull('sales_invoice_items.deleted_at')
                            ->select(
                                DB::raw('COALESCE(sales_invoice_items.art_no, "") as art_no'),
                                DB::raw('SUM(sales_invoice_items.quantity) as inv_qty')
                            )
                            ->groupBy('art_no')
                            ->pluck('inv_qty', 'art_no')
                            ->toArray();
                    }

                    $missedOrdersSet = [];
                    $totalLostValue = 0;

                    foreach ($bData['items'] as $item) {
                        $artNo = $item->art_no;
                        $ordQty = floatval($item->qty ?? 0);
                        $ordAmt = floatval($item->amount ?? 0);
                        $rate = $ordQty > 0 ? ($ordAmt / $ordQty) : 0;
                        $invQty = floatval($invoicedArtNos[$artNo] ?? 0);

                        $itemPending = max(0, $ordQty - $invQty);
                        if ($itemPending > 0) {
                            $missedOrdersSet[$item->sale_order_id] = true;
                            $totalLostValue += ($itemPending * $rate);
                        }
                    }

                    $missedOrdersCount = count($missedOrdersSet);
                    if ($missedOrdersCount <= 0) {
                        continue;
                    }

                    if ($search && stripos($bName, $search) === false) {
                        continue;
                    }

                    $brandLink = '<a href="javascript:void(0)" class="fw-bold text-primary" onclick="drillDownToLostSalesBrand(\'' . htmlspecialchars($bName, ENT_QUOTES) . '\')">' . htmlspecialchars($bName) . '</a>';

                    $data[] = [
                        'brand' => $brandLink,
                        'missed_orders' => '<span class="badge bg-danger fw-bold px-2 py-1">' . number_format($missedOrdersCount, 0) . '</span>',
                        'lost_value' => '<span class="text-danger fw-bold">₹' . number_format($totalLostValue, 2) . '</span>',
                    ];
                }

                $recordsTotal = count($data);
                $recordsFiltered = $recordsTotal;

                if ($length != -1) {
                    $data = array_slice($data, $start, $length);
                }

                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                ]);

            case 'brandwise-completion':
                $fromDate = $request->from_date ? date('Y-m-d', strtotime($request->from_date)) : null;
                $toDate = $request->to_date ? date('Y-m-d', strtotime($request->to_date)) : null;
                $storeId = $request->store_id;
                $brandIdFilter = $request->brand_id;

                $allBrands = DB::table('brands')->get();

                $orderQuery = DB::table('sales_order_items')
                    ->join('sales_orders', 'sales_order_items.sale_order_id', '=', 'sales_orders.id')
                    ->leftJoin('stock_entry_items', 'sales_order_items.stock_entry_item_id', '=', 'stock_entry_items.id')
                    ->leftJoin('brands', 'stock_entry_items.brand_id', '=', 'brands.id')
                    ->leftJoin('brand_categories', 'sales_order_items.brand_cat_id', '=', 'brand_categories.id')
                    ->whereNull('sales_orders.deleted_at')
                    ->whereNull('sales_order_items.deleted_at')
                    ->select(
                        'sales_order_items.sale_order_id',
                        'sales_order_items.art_no',
                        'sales_order_items.sku',
                        'sales_order_items.item_name',
                        'sales_order_items.category_name',
                        'sales_order_items.qty',
                        'brands.id as stock_brand_id',
                        'brands.brand_name as stock_brand_name',
                        'brand_categories.name as cat_brand_name',
                        'sales_orders.so_no',
                        'sales_orders.so_date',
                        'sales_orders.reason_for_delay'
                    );

                if ($fromDate && $toDate) {
                    $orderQuery->whereBetween('sales_orders.so_date', [$fromDate, $toDate]);
                }
                if ($storeId) {
                    $orderQuery->where('sales_orders.store_id', $storeId);
                }
                if ($request->customer_id) {
                    $orderQuery->where('sales_orders.customer_id', $request->customer_id);
                }

                $orderItems = $orderQuery->get();

                $brandMap = [];

                foreach ($orderItems as $item) {
                    $brandName = null;
                    $brandId = null;

                    if (!empty($item->stock_brand_name)) {
                        $brandName = $item->stock_brand_name;
                        $brandId = $item->stock_brand_id;
                    } elseif (!empty($item->cat_brand_name)) {
                        $brandName = $item->cat_brand_name;
                    } else {
                        $artNo = trim($item->art_no ?? '');
                        $sku = trim($item->sku ?? '');
                        $itemName = trim($item->item_name ?? '');
                        $catName = trim($item->category_name ?? '');

                        foreach ($allBrands as $b) {
                            $bCode = trim($b->code ?? '');
                            $bNameCandidate = trim($b->brand_name ?? '');
                            $isMatched = false;

                            if ($bCode) {
                                if (($artNo && stripos($artNo, $bCode) === 0) ||
                                    ($sku && stripos($sku, $bCode) === 0) ||
                                    ($itemName && (stripos($itemName, $bCode) === 0 || stripos($itemName, $bCode . '-') !== false || stripos($itemName, $bCode . ' ') !== false || stripos($itemName, $bCode . '_') !== false)) ||
                                    ($catName && (stripos($catName, $bCode) === 0 || stripos($catName, $bCode . '-') !== false || stripos($catName, $bCode . ' ') !== false || stripos($catName, $bCode . '_') !== false))) {
                                    $isMatched = true;
                                }
                            }
                            if (!$isMatched && $bNameCandidate) {
                                if (($artNo && stripos($artNo, $bNameCandidate) !== false) ||
                                    ($sku && stripos($sku, $bNameCandidate) !== false) ||
                                    ($itemName && stripos($itemName, $bNameCandidate) !== false) ||
                                    ($catName && stripos($catName, $bNameCandidate) !== false)) {
                                    $isMatched = true;
                                }
                            }

                            if ($isMatched) {
                                $brandName = $b->brand_name;
                                $brandId = $b->id;
                                break;
                            }
                        }
                    }

                    if (!$brandName || $brandName === 'Unassigned Brand' || empty($brandId)) {
                        continue;
                    }

                    if ($brandIdFilter && $brandId != $brandIdFilter) {
                        continue;
                    }

                    if (!isset($brandMap[$brandName])) {
                        $brandMap[$brandName] = [
                            'brand_id' => $brandId,
                            'brand_name' => $brandName,
                            'so_ids' => [],
                            'total_qty' => 0,
                            'items' => []
                        ];
                    }

                    $soId = $item->sale_order_id;
                    $brandMap[$brandName]['so_ids'][$soId] = true;
                    $brandMap[$brandName]['total_qty'] += floatval($item->qty ?? 0);
                    $brandMap[$brandName]['items'][] = $item;
                }

                $data = [];
                $snoCounter = 1;

                foreach ($brandMap as $bName => $bData) {
                    $soIds = array_keys($bData['so_ids']);
                    $ordersCount = count($soIds);
                    $totalQty = $bData['total_qty'];
                    $invoicedQty = 0;
                    $pendingArtNosList = [];
                    $reasonsList = [];

                    if (!empty($soIds)) {
                        $invoicedArtNos = DB::table('sales_invoice_items')
                            ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
                            ->whereIn('sales_invoices.so_id', $soIds)
                            ->whereNull('sales_invoices.deleted_at')
                            ->whereNull('sales_invoice_items.deleted_at')
                            ->select(
                                DB::raw('COALESCE(sales_invoice_items.art_no, "") as art_no'),
                                DB::raw('SUM(sales_invoice_items.quantity) as inv_qty')
                            )
                            ->groupBy('art_no')
                            ->pluck('inv_qty', 'art_no');

                        foreach ($bData['items'] as $item) {
                            $artNo = $item->art_no;
                            $itemOrdQty = floatval($item->qty ?? 0);
                            $itemInvQty = floatval($invoicedArtNos[$artNo] ?? 0);
                            $invoicedQty += min($itemOrdQty, $itemInvQty);

                            $itemPending = max(0, $itemOrdQty - $itemInvQty);
                            if ($itemPending > 0 && !empty($artNo)) {
                                $pendingArtNosList[] = $artNo;
                            }
                        }

                        $delays = DB::table('sales_orders')->whereIn('id', $soIds)->whereNotNull('reason_for_delay')->where('reason_for_delay', '!=', '')->pluck('reason_for_delay')->toArray();

                        $reasonsList = array_unique($delays);
                    }

                    $pendingQty = max(0, $totalQty - $invoicedQty);
                    $completionPct = $totalQty > 0 ? min(100, ($invoicedQty / $totalQty) * 100) : 0;

                    $pctBadgeClass = $completionPct >= 100 ? 'bg-success' : ($completionPct >= 50 ? 'bg-warning text-dark' : 'bg-danger');

                    if ($completionPct >= 100 || $pendingQty <= 0) {
                        $statusHtml = '<span class="badge bg-success">Completed</span>';
                    } elseif ($invoicedQty > 0) {
                        $statusHtml = '<span class="badge bg-warning text-dark">Partially Completed</span>';
                    } else {
                        $statusHtml = '<span class="badge bg-danger">Pending</span>';
                    }

                    $uniquePendingArtNos = array_unique($pendingArtNosList);
                    $awaitingStr = !empty($uniquePendingArtNos) ? implode(', ', array_slice($uniquePendingArtNos, 0, 5)) . (count($uniquePendingArtNos) > 5 ? '...' : '') : 'None / Closed';
                    $reasonsStr = !empty($reasonsList) ? implode('; ', array_slice($reasonsList, 0, 3)) : '-';

                    $brandLink = '<a href="javascript:void(0)" class="fw-bold text-primary view-brand-orders-link" data-brand-id="' . $bData['brand_id'] . '" data-brand-name="' . htmlspecialchars($bName) . '">' . htmlspecialchars($bName) . '</a>';

                    $data[] = [
                        'sno' => $snoCounter++,
                        'brand' => $brandLink,
                        'orders_count' => number_format($ordersCount, 0),
                        'total_qty' => number_format($totalQty, 0),
                        'invoiced_qty' => number_format($invoicedQty, 0),
                        'completion_pct' => '<span class="badge ' . $pctBadgeClass . ' fw-bold px-2 py-1">' . number_format($completionPct, 2) . '%</span>',
                        'pending_qty' => '<span class="fw-bold text-danger">' . number_format($pendingQty, 0) . '</span>',
                        'status' => $statusHtml,
                        'awaiting_art_nos' => htmlspecialchars($awaitingStr),
                        'wip' => '-',
                        'action_required' => htmlspecialchars($reasonsStr)
                    ];
                }

                $recordsTotal = count($data);
                $recordsFiltered = $recordsTotal;

                if ($length != -1) {
                    $data = array_slice($data, $start, $length);
                }

                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => $recordsTotal,
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                ]);

            default:
                return response()->json([
                    'draw' => $draw,
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                ]);
        }
    }

    public function getBrandwiseCompletionOrders(Request $request)
    {
        $brandId = $request->brand_id;
        $brandNameParam = $request->brand_name;
        $fromDate = $request->from_date ? date('Y-m-d', strtotime($request->from_date)) : null;
        $toDate = $request->to_date ? date('Y-m-d', strtotime($request->to_date)) : null;
        $storeId = $request->store_id;

        $draw = intval($request->draw ?? 1);
        $start = intval($request->start ?? 0);
        $length = intval($request->length ?? 10);
        $searchValue = $request->search['value'] ?? null;

        $allBrands = DB::table('brands')->get();

        $query = DB::table('sales_order_items')
            ->join('sales_orders', 'sales_order_items.sale_order_id', '=', 'sales_orders.id')
            ->leftJoin('stock_entry_items', 'sales_order_items.stock_entry_item_id', '=', 'stock_entry_items.id')
            ->leftJoin('brands', 'stock_entry_items.brand_id', '=', 'brands.id')
            ->leftJoin('brand_categories', 'sales_order_items.brand_cat_id', '=', 'brand_categories.id')
            ->leftJoin('customers', 'sales_orders.customer_id', '=', 'customers.id')
            ->whereNull('sales_orders.deleted_at')
            ->whereNull('sales_order_items.deleted_at')
            ->select(
                'sales_orders.id as so_id',
                'sales_order_items.art_no',
                'sales_order_items.sku',
                'sales_order_items.item_name',
                'sales_order_items.category_name',
                'brands.id as stock_brand_id',
                'brands.brand_name as stock_brand_name',
                'brand_categories.name as cat_brand_name'
            );

        if ($storeId) {
            $query->where('sales_orders.store_id', $storeId);
        }
        if ($request->customer_id) {
            $query->where('sales_orders.customer_id', $request->customer_id);
        }
        if ($fromDate && $toDate) {
            $query->whereBetween('sales_orders.so_date', [$fromDate, $toDate]);
        }

        $items = $query->get();

        $matchedSoIds = [];
        foreach ($items as $item) {
            $bName = null;
            $bId = null;

            if (!empty($item->stock_brand_name)) {
                $bName = $item->stock_brand_name;
                $bId = $item->stock_brand_id;
            } elseif (!empty($item->cat_brand_name)) {
                $bName = $item->cat_brand_name;
            } else {
                $artNo = trim($item->art_no ?? '');
                $sku = trim($item->sku ?? '');
                $itemName = trim($item->item_name ?? '');
                $catName = trim($item->category_name ?? '');

                foreach ($allBrands as $b) {
                    $bCode = trim($b->code ?? '');
                    $bBrandName = trim($b->brand_name ?? '');
                    $isMatched = false;

                    if ($bCode) {
                        if (($artNo && stripos($artNo, $bCode) === 0) ||
                            ($sku && stripos($sku, $bCode) === 0) ||
                            ($itemName && (stripos($itemName, $bCode) === 0 || stripos($itemName, $bCode . '-') !== false || stripos($itemName, $bCode . ' ') !== false || stripos($itemName, $bCode . '_') !== false)) ||
                            ($catName && (stripos($catName, $bCode) === 0 || stripos($catName, $bCode . '-') !== false || stripos($catName, $bCode . ' ') !== false || stripos($catName, $bCode . '_') !== false))) {
                            $isMatched = true;
                        }
                    }
                    if (!$isMatched && $bBrandName) {
                        if (($artNo && stripos($artNo, $bBrandName) !== false) ||
                            ($sku && stripos($sku, $bBrandName) !== false) ||
                            ($itemName && stripos($itemName, $bBrandName) !== false) ||
                            ($catName && stripos($catName, $bBrandName) !== false)) {
                            $isMatched = true;
                        }
                    }

                    if ($isMatched) {
                        $bName = $b->brand_name;
                        $bId = $b->id;
                        break;
                    }
                }
            }

            if (($brandId && $bId == $brandId) || ($brandNameParam && strcasecmp($bName, $brandNameParam) === 0)) {
                $matchedSoIds[$item->so_id] = true;
            }
        }

        $soIds = array_keys($matchedSoIds);

        $soQuery = DB::table('sales_orders')
            ->leftJoin('customers', 'sales_orders.customer_id', '=', 'customers.id')
            ->whereIn('sales_orders.id', $soIds)
            ->whereNull('sales_orders.deleted_at')
            ->select(
                'sales_orders.id as so_id',
                'sales_orders.so_no',
                'sales_orders.order_no as orderaxe_order_no',
                'sales_orders.order_type',
                'sales_orders.request_date',
                'sales_orders.so_date',
                'sales_orders.delivery_date',
                'customers.name as customer_name',
                'sales_orders.reason_for_delay',
                'sales_orders.status'
            );

        if (!empty($searchValue)) {
            $soQuery->where(function($q) use ($searchValue) {
                $q->where('sales_orders.so_no', 'like', "%{$searchValue}%")
                  ->orWhere('sales_orders.order_no', 'like', "%{$searchValue}%")
                  ->orWhere('sales_orders.order_type', 'like', "%{$searchValue}%")
                  ->orWhere('customers.name', 'like', "%{$searchValue}%")
                  ->orWhere('sales_orders.reason_for_delay', 'like', "%{$searchValue}%");
            });
        }

        $recordsTotal = count($soIds);
        $recordsFiltered = $soQuery->count();

        $soQuery->orderBy('sales_orders.id', 'desc');

        if ($length != -1) {
            $soQuery->skip($start)->take($length);
        }

        $orders = $soQuery->get();

        $data = [];
        $sno = $start + 1;
        foreach ($orders as $so) {
            $soItems = DB::table('sales_order_items')->where('sale_order_id', $so->so_id)->whereNull('deleted_at')->get();

            $orderedQty = $soItems->sum('qty');

            $invoicedArtNos = DB::table('sales_invoice_items')
                ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
                ->where('sales_invoices.so_id', $so->so_id)
                ->whereNull('sales_invoices.deleted_at')
                ->whereNull('sales_invoice_items.deleted_at')
                ->select(
                    DB::raw('COALESCE(sales_invoice_items.art_no, "") as art_no'),
                    DB::raw('SUM(sales_invoice_items.quantity) as inv_qty')
                )
                ->groupBy('art_no')
                ->pluck('inv_qty', 'art_no');

            $invoicedQty = 0;
            $pendingArtNos = [];

            foreach ($soItems as $item) {
                $artNo = $item->art_no;
                $itemOrdQty = floatval($item->qty ?? 0);
                $itemInvQty = floatval($invoicedArtNos[$artNo] ?? 0);
                $invoicedQty += min($itemOrdQty, $itemInvQty);

                $itemPending = max(0, $itemOrdQty - $itemInvQty);
                if ($itemPending > 0 && !empty($artNo)) {
                    $pendingArtNos[] = $artNo;
                }
            }

            $pendingQty = max(0, $orderedQty - $invoicedQty);
            $uniquePendingArtNos = array_unique($pendingArtNos);
            $countArtNos = count($uniquePendingArtNos);

            $completionPct = $orderedQty > 0 ? min(100, round(($invoicedQty / $orderedQty) * 100, 1)) : 0;
            $pctBadgeClass = ($completionPct >= 100) ? 'bg-label-success' : (($completionPct > 0) ? 'bg-label-warning' : 'bg-label-secondary');
            $completionHtml = '<span class="badge ' . $pctBadgeClass . ' fw-bold px-2 py-1">' . number_format($completionPct, 1) . '%</span>';

            if ($countArtNos > 0) {
                $awaitingHtml = '<button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 text-nowrap view-awaiting-art-nos" data-so-id="' . $so->so_id . '" data-so-no="' . htmlspecialchars($so->so_no) . '" data-count="' . $countArtNos . '"><i class="ri ri-eye-line me-1"></i>View Art No (' . $countArtNos . ')</button>';
            } else {
                $awaitingHtml = '<span class="text-success small fw-bold"><i class="ri ri-check-double-line me-1"></i>None / Closed</span>';
            }

            $statusHtml = ($pendingQty <= 0) 
                ? '<span class="badge bg-success">COMPLETED</span>' 
                : '<span class="badge bg-danger">PENDING</span>';

            $orderTypeBadge = '-';
            if (!empty($so->order_type)) {
                $typeClass = ($so->order_type === 'Regular') ? 'bg-label-primary' : 'bg-label-warning';
                $orderTypeBadge = '<span class="badge ' . $typeClass . '">' . htmlspecialchars($so->order_type) . '</span>';
            }

            $orderaxeNoHtml = !empty($so->orderaxe_order_no) ? '<span class="fw-semibold text-dark">' . htmlspecialchars($so->orderaxe_order_no) . '</span>' : '<span class="text-muted">-</span>';

            $orderDateStr = !empty($so->request_date) ? date('d-m-Y', strtotime($so->request_date)) : (!empty($so->so_date) ? date('d-m-Y', strtotime($so->so_date)) : '-');
            $soDateStr = !empty($so->so_date) ? date('d-m-Y', strtotime($so->so_date)) : '-';
            $deliveryDateStr = !empty($so->delivery_date) ? date('d-m-Y', strtotime($so->delivery_date)) : '-';

            $data[] = [
                'sno' => $sno++,
                'order_date' => $orderDateStr,
                'so_date' => $soDateStr,
                'delivery_date' => $deliveryDateStr,
                'so_no' => '<a href="' . url('sales_orders/view/' . $so->so_id) . '" target="_blank" class="fw-bold text-primary">' . htmlspecialchars($so->so_no) . '</a>',
                'orderaxe_order_no' => $orderaxeNoHtml,
                'order_type' => $orderTypeBadge,
                'customer' => htmlspecialchars($so->customer_name ?: '-'),
                'total_qty' => number_format($orderedQty, 0),
                'invoiced_qty' => number_format($invoicedQty, 0),
                'pending_qty' => '<span class="fw-bold text-danger">' . number_format($pendingQty, 0) . '</span>',
                'order_completed' => $completionHtml,
                'status' => $statusHtml,
                'awaiting_art_nos' => $awaitingHtml,
                'action_required' => htmlspecialchars($so->reason_for_delay ?: '-')
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'status' => true,
            'data' => $data
        ]);
    }

    public function getOrderProcessingTimeArtNos(Request $request)
    {
        $soId = $request->so_id;

        $items = DB::table('sales_order_items')
            ->leftJoin('stock_entry_items', 'sales_order_items.stock_entry_item_id', '=', 'stock_entry_items.id')
            ->select(
                'sales_order_items.art_no',
                'sales_order_items.sku',
                'sales_order_items.item_name',
                'sales_order_items.category_name',
                'sales_order_items.qty',
                'sales_order_items.size_id',
                'sales_order_items.sleeve',
                'stock_entry_items.size as stock_size',
                'stock_entry_items.sleeve_type as stock_sleeve'
            )
            ->where('sales_order_items.sale_order_id', $soId)
            ->whereNull('sales_order_items.deleted_at')
            ->get();

        $invoicedArtNos = DB::table('sales_invoice_items')
            ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
            ->where('sales_invoices.so_id', $soId)
            ->whereNull('sales_invoices.deleted_at')
            ->whereNull('sales_invoice_items.deleted_at')
            ->select(
                DB::raw('COALESCE(sales_invoice_items.art_no, "") as art_no'),
                DB::raw('SUM(sales_invoice_items.quantity) as inv_qty')
            )
            ->groupBy('art_no')
            ->pluck('inv_qty', 'art_no');

        $artNosData = [];
        $sno = 1;
        foreach ($items as $item) {
            $artNo = $item->art_no;
            $ordQty = floatval($item->qty ?? 0);
            $invQty = floatval($invoicedArtNos[$artNo] ?? 0);
            $pendingQty = max(0, $ordQty - $invQty);

            if ($pendingQty <= 0) {
                continue;
            }

            if ($invQty > 0) {
                $status = '<span class="badge bg-warning text-dark">Partial</span>';
            } else {
                $status = '<span class="badge bg-danger">Pending</span>';
            }

            $rawSleeve = $item->sleeve ?: $item->stock_sleeve;
            if (!empty($rawSleeve) && is_string($rawSleeve) && str_starts_with(trim($rawSleeve), '[')) {
                $decoded = json_decode($rawSleeve, true);
                if (is_array($decoded) && !empty($decoded)) {
                    $rawSleeve = $decoded[0];
                }
            }
            $sleeveUpper = strtoupper(trim((string)$rawSleeve));
            if ($sleeveUpper === 'FS' || $sleeveUpper === 'F/S' || $sleeveUpper === 'FULL' || $sleeveUpper === 'FULL SLEEVE') {
                $sleeveFormatted = 'Full';
            } elseif ($sleeveUpper === 'HS' || $sleeveUpper === 'H/S' || $sleeveUpper === 'HALF' || $sleeveUpper === 'HALF SLEEVE') {
                $sleeveFormatted = 'Half';
            } else {
                $sleeveFormatted = $rawSleeve ? htmlspecialchars($rawSleeve) : '-';
            }

            $sizeVal = $item->size_id ?: ($item->stock_size ?: '-');

            $formattedCategory = null;
            if (!empty($item->category_name)) {
                $catStr = str_replace([' ', '_'], '-', trim($item->category_name));
                $parts = array_filter(explode('-', $catStr));
                $formattedParts = array_map(function($p) {
                    $pUpper = strtoupper($p);
                    if ($pUpper === 'FS' || $pUpper === 'F/S') return 'F/S';
                    if ($pUpper === 'HS' || $pUpper === 'H/S') return 'H/S';
                    return ucfirst(strtolower($p));
                }, $parts);
                $formattedCategory = implode('-', $formattedParts);
            }

            $itemNameDisplay = $formattedCategory ? htmlspecialchars($formattedCategory) : htmlspecialchars($item->item_name ?: ($item->sku ?: '-'));

            $artNosData[] = [
                'sno' => $sno++,
                'art_no' => '<span class="fw-bold text-dark">' . htmlspecialchars($artNo ?: '-') . '</span>',
                'item_name' => $itemNameDisplay,
                'sleeve' => '<span class="badge bg-label-info">' . $sleeveFormatted . '</span>',
                'size' => '<span class="badge bg-label-secondary">' . htmlspecialchars($sizeVal) . '</span>',
                'ordered_qty' => number_format($ordQty, 0),
                'invoiced_qty' => number_format($invQty, 0),
                'pending_qty' => '<span class="fw-bold text-danger">' . number_format($pendingQty, 0) . '</span>',
                'status' => $status
            ];
        }

        $start = intval($request->start ?? 0);
        $length = intval($request->length ?? 10);
        $draw = intval($request->draw ?? 1);

        $recordsTotal = count($artNosData);
        $searchValue = $request->input('search.value');
        if (!empty($searchValue)) {
            $searchValueLower = strtolower($searchValue);
            $artNosData = array_values(array_filter($artNosData, function($row) use ($searchValueLower) {
                return (
                    str_contains(strtolower(strip_tags($row['art_no'])), $searchValueLower) ||
                    str_contains(strtolower(strip_tags($row['item_name'])), $searchValueLower) ||
                    str_contains(strtolower(strip_tags($row['sleeve'])), $searchValueLower) ||
                    str_contains(strtolower(strip_tags($row['size'])), $searchValueLower)
                );
            }));
        }

        $recordsFiltered = count($artNosData);

        $pagedData = $length > 0 ? array_slice($artNosData, $start, $length) : $artNosData;

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'status' => true,
            'data' => $pagedData,
            'items' => $pagedData
        ]);
    }

    public function getBrandwiseCompletionArtNos(Request $request)
    {
        return $this->getOrderProcessingTimeArtNos($request);
    }

    protected function resolveBrandFromOrderItem($item, $allBrands)
    {
        if (!empty($item->stock_brand_name)) {
            return ['name' => trim($item->stock_brand_name), 'id' => $item->stock_brand_id ?? null];
        }
        if (!empty($item->cat_brand_name)) {
            return ['name' => trim($item->cat_brand_name), 'id' => null];
        }

        $artNo = trim($item->art_no ?? '');
        $sku = trim($item->sku ?? '');
        $itemName = trim($item->item_name ?? '');
        $catName = trim($item->category_name ?? '');
        $catPath = trim($item->categories_path_val ?? '');

        if (stripos($artNo, 'BC') === 0 || stripos($sku, 'BC') === 0 || stripos($itemName, 'BC-') === 0 || stripos($catName, 'BC-') === 0) {
            $cbc = $allBrands->firstWhere('code', 'CBC');
            if ($cbc) {
                return ['name' => $cbc->brand_name, 'id' => $cbc->id];
            }
        }

        foreach ($allBrands as $b) {
            $bCode = trim($b->code ?? '');
            $bName = trim($b->brand_name ?? '');

            if (empty($bCode) && empty($bName)) {
                continue;
            }

            if ($bCode) {
                if (($artNo && (stripos($artNo, $bCode) === 0 || stripos($artNo, $bCode . '-') === 0)) ||
                    ($sku && (stripos($sku, $bCode) === 0 || stripos($sku, $bCode . '-') === 0)) ||
                    ($itemName && (stripos($itemName, $bCode) === 0 || stripos($itemName, $bCode . '-') === 0 || stripos($itemName, $bCode . ' ') === 0)) ||
                    ($catName && (stripos($catName, $bCode) === 0 || stripos($catName, $bCode . '-') === 0 || stripos($catName, $bCode . ' ') === 0)) ||
                    ($catPath && (stripos($catPath, $bCode) === 0 || stripos($catPath, $bCode . '-') === 0))) {
                    return ['name' => $b->brand_name, 'id' => $b->id];
                }
            }

            if ($bName) {
                if (($artNo && stripos($artNo, $bName) !== false) ||
                    ($sku && stripos($sku, $bName) !== false) ||
                    ($itemName && stripos($itemName, $bName) !== false) ||
                    ($catName && stripos($catName, $bName) !== false) ||
                    ($catPath && stripos($catPath, $bName) !== false)) {
                    return ['name' => $b->brand_name, 'id' => $b->id];
                }
            }
        }

        return null;
    }

    protected function buildArtNoBrandMap($orderItems, $allBrands)
    {
        $map = []; 
        $artNoMap = [];

        foreach ($orderItems as $item) {
            if (!empty($item->stock_brand_name)) {
                $artNoMap[spl_object_id($item)] = ['name' => trim($item->stock_brand_name), 'id' => $item->stock_brand_id ?? null];
                continue;
            }
            if (!empty($item->cat_brand_name)) {
                $artNoMap[spl_object_id($item)] = ['name' => trim($item->cat_brand_name), 'id' => null];
                continue;
            }

            $cacheKey = ($item->art_no ?? '') . '|||' . ($item->sku ?? '') . '|||' . ($item->item_name ?? '') . '|||' . ($item->category_name ?? '');
            if (!array_key_exists($cacheKey, $map)) {
                $map[$cacheKey] = $this->resolveBrandFromOrderItem($item, $allBrands);
            }
            $artNoMap[spl_object_id($item)] = $map[$cacheKey];
        }

        return $artNoMap;
    }

    public function getBrandwiseLostSalesOrders(Request $request)
    {
        $draw = intval($request->draw);
        $start = intval($request->start);
        $rawLen = intval($request->length); $length = ($rawLen > 0 || $rawLen === -1) ? $rawLen : 10;
        $search = $request->search['value'] ?? '';

        $brandName = $request->brand_name;
        $storeId = $request->store_id;
        $fromDate = $request->from_date ? date('Y-m-d', strtotime(str_replace('/', '-', $request->from_date))) : '2026-04-01';
        $toDate = $request->to_date ? date('Y-m-d', strtotime(str_replace('/', '-', $request->to_date))) : date('Y-m-d');

        $allBrands = Brand::where('status', 'Active')->orderByRaw('LENGTH(code) DESC')->orderByRaw('LENGTH(brand_name) DESC')->get();

        $orderQuery = DB::table('sales_order_items')
            ->join('sales_orders', 'sales_order_items.sale_order_id', '=', 'sales_orders.id')
            ->leftJoin('customers', 'sales_orders.customer_id', '=', 'customers.id')
            ->leftJoin('stock_entry_items', 'sales_order_items.stock_entry_item_id', '=', 'stock_entry_items.id')
            ->leftJoin('brands as b_stock', 'stock_entry_items.brand_id', '=', 'b_stock.id')
            ->leftJoin('brand_categories', 'sales_order_items.brand_cat_id', '=', 'brand_categories.id')
            ->select(
                'sales_orders.id as so_id',
                'sales_orders.so_no',
                'sales_orders.orderaxe_id',
                'sales_orders.orderaxe_ref_id',
                'sales_orders.order_no',
                'sales_orders.so_date',
                'sales_orders.reason_for_delay',
                'sales_orders.internal_remarks',
                'customers.name as customer_name',
                'sales_order_items.art_no',
                'sales_order_items.sku',
                'sales_order_items.item_name',
                'sales_order_items.category_name',
                'sales_order_items.categories_path_val',
                'sales_order_items.qty',
                'sales_order_items.amount',
                'b_stock.brand_name as stock_brand_name',
                'brand_categories.name as cat_brand_name'
            )
            ->whereNull('sales_orders.deleted_at')
            ->whereNull('sales_order_items.deleted_at');

        if ($fromDate) {
            $orderQuery->where('sales_orders.so_date', '>=', $fromDate);
        }
        if ($toDate) {
            $orderQuery->where('sales_orders.so_date', '<=', $toDate);
        }
        if ($storeId) {
            $orderQuery->where('sales_orders.store_id', $storeId);
        }
        if ($search) {
            $orderQuery->where(function($q) use ($search) {
                $q->where('sales_orders.so_no', 'like', "%{$search}%")
                  ->orWhere('customers.name', 'like', "%{$search}%")
                  ->orWhere('sales_order_items.art_no', 'like', "%{$search}%");
            });
        }

        $orderItems = $orderQuery->get();

        // Pre-build brand map — resolve each unique art_no combo only once
        $brandMap = $this->buildArtNoBrandMap($orderItems, $allBrands);

        $soMap = [];
        $matchedSoIds = [];

        foreach ($orderItems as $item) {
            $resolved = $brandMap[spl_object_id($item)] ?? null;

            if (!$resolved || empty($resolved['name']) || strtolower($resolved['name']) === 'uncategorized') {
                continue;
            }

            $resolvedBrand = $resolved['name'];

            if ($brandName && strtolower($resolvedBrand) !== strtolower($brandName)) {
                continue;
            }

            $soId    = $item->so_id;
            $artNo   = trim($item->art_no ?? '');
            $ordQ    = floatval($item->qty ?? 0);
            $ordAmt  = floatval($item->amount ?? 0);
            $normArtNo = strtoupper(preg_replace('/\s+/', '', $artNo));

            if (!isset($soMap[$soId])) {
                $matchedSoIds[] = $soId;
                $reasonText = !empty($item->reason_for_delay) ? trim($item->reason_for_delay) : '-';
                $soMap[$soId] = [
                    'so_id'           => $soId,
                    'so_no'           => $item->so_no,
                    'orderaxe_ref_id' => $item->orderaxe_ref_id ?? $item->order_no,
                    'is_orderaxe'     => !empty($item->orderaxe_id) || !empty($item->orderaxe_ref_id),
                    'customer'        => $item->customer_name ?? '-',
                    'so_date'         => date('d M Y', strtotime($item->so_date)),
                    'reason'          => $reasonText,
                    'ordered_qty'     => 0,
                    'ordered_value'   => 0,
                    'so_art_nos'      => [],   // normalized art_no => total qty
                    'art_rates'       => [],   // normalized art_no => unit rate
                ];
            }

            $soMap[$soId]['ordered_qty']   += $ordQ;
            $soMap[$soId]['ordered_value'] += $ordAmt;

            if (!empty($normArtNo)) {
                $soMap[$soId]['so_art_nos'][$normArtNo] = ($soMap[$soId]['so_art_nos'][$normArtNo] ?? 0) + $ordQ;
                // store rate for lost value calc (last rate wins per art_no, acceptable)
                if ($ordQ > 0) {
                    $soMap[$soId]['art_rates'][$normArtNo] = $ordAmt / $ordQ;
                }
            }
        }

        $invoicedArtNosMap = [];
        if (!empty($matchedSoIds)) {
            $invoices = DB::table('sales_invoice_items')
                ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
                ->whereIn('sales_invoices.so_id', array_unique($matchedSoIds))
                ->whereNull('sales_invoices.deleted_at')
                ->whereNull('sales_invoice_items.deleted_at')
                ->select(
                    'sales_invoices.so_id',
                    DB::raw('COALESCE(sales_invoice_items.art_no, "") as art_no'),
                    DB::raw('SUM(sales_invoice_items.quantity) as invoiced_qty')
                )
                ->groupBy('sales_invoices.so_id', 'art_no')
                ->get();

            foreach ($invoices as $inv) {
                $normalizedArtNo = strtoupper(preg_replace('/\s+/', '', trim($inv->art_no)));
                $invoicedArtNosMap[$inv->so_id][$normalizedArtNo] = ($invoicedArtNosMap[$inv->so_id][$normalizedArtNo] ?? 0) + floatval($inv->invoiced_qty ?? 0);
            }
        }

        $allOrders = [];
        $orderSno = 1;
        foreach ($soMap as $soId => $data) {
            $soInvQty  = 0;
            $lostValue = 0;
            $soArtNos  = $data['so_art_nos'];   // already normalized
            $artRates  = $data['art_rates'];

            // Calc invoiced qty (only for art_nos in SO)
            foreach ($soArtNos as $normArt => $ordQ) {
                $invQ = floatval($invoicedArtNosMap[$soId][$normArt] ?? 0);
                $effectiveInvQ = min($ordQ, $invQ);
                $soInvQty += $effectiveInvQ;

                $itemPending = max(0, $ordQ - $invQ);
                $rate = $artRates[$normArt] ?? 0;
                $lostValue += ($itemPending * $rate);
            }

            // Open Order Qty: invoice items NOT in SO, or excess over SO qty
            $openOrderQty = 0;
            if (!empty($invoicedArtNosMap[$soId])) {
                foreach ($invoicedArtNosMap[$soId] as $invNormArtNo => $invQ) {
                    if (!isset($soArtNos[$invNormArtNo])) {
                        $openOrderQty += $invQ;
                    } elseif ($invQ > $soArtNos[$invNormArtNo]) {
                        $openOrderQty += ($invQ - $soArtNos[$invNormArtNo]);
                    }
                }
            }

            $totalOrdQty = $data['ordered_qty'];
            $pendingQty  = max(0, $totalOrdQty - $soInvQty);

            if ($pendingQty <= 0) {
                continue;
            }

            $soNoHtml = '<a href="' . url('sales_orders/view/' . $soId) . '" target="_blank" class="fw-bold text-primary">' . htmlspecialchars($data['so_no']) . '</a>';
            if ($data['is_orderaxe']) {
                $soNoHtml .= ' <span class="badge bg-label-info ms-1" style="font-size:10px;">Orderaxe</span>';
            }

            $reasonHtml = $data['reason'] !== '-' ? htmlspecialchars($data['reason']) : '<span class="text-muted">-</span>';

            $allOrders[] = [
                'sno' => $orderSno++,
                'so_id' => $soId,
                'so_no_plain' => $data['so_no'],
                'so_no' => $soNoHtml,
                'customer' => htmlspecialchars($data['customer']),
                'so_date' => $data['so_date'],
                'ordered_qty' => number_format($totalOrdQty, 0),
                'invoiced_qty' => '<span class="text-primary fw-bold">' . number_format($soInvQty, 0) . '</span>',
                'open_order_qty' => $openOrderQty > 0
                    ? '<span class="text-info fw-bold">' . number_format($openOrderQty, 0) . '</span>'
                    : '<span class="text-muted">0</span>',
                'lost_qty' => '<span class="text-danger fw-bold">' . number_format($pendingQty, 0) . '</span>',
                'lost_value' => '<span class="text-danger fw-bold">₹' . number_format($lostValue, 2) . '</span>',
                'reason' => $reasonHtml,
            ];
        }

        $recordsTotal = count($allOrders);
        $recordsFiltered = $recordsTotal;

        $pagedOrders = $length != -1 ? array_slice($allOrders, $start, $length) : $allOrders;

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $pagedOrders,
        ]);
    }

    public function getLostItemsDetail(Request $request, $so_id = null)
    {
        $soId = $so_id ?: $request->so_id;
        $so = DB::table('sales_orders')
            ->leftJoin('customers', 'sales_orders.customer_id', '=', 'customers.id')
            ->where('sales_orders.id', $soId)
            ->whereNull('sales_orders.deleted_at')
            ->first([
                'sales_orders.id',
                'sales_orders.so_no',
                'sales_orders.so_date',
                'customers.name as customer_name'
            ]);

        if (!$so) {
            return response()->json([
                'draw' => intval($request->draw ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'status' => false,
                'message' => 'Sales Order not found'
            ], 404);
        }

        $soItems = DB::table('sales_order_items')
            ->leftJoin('stock_entry_items', 'sales_order_items.stock_entry_item_id', '=', 'stock_entry_items.id')
            ->leftJoin('colors', 'sales_order_items.color_id', '=', 'colors.id')
            ->where('sales_order_items.sale_order_id', $soId)
            ->whereNull('sales_order_items.deleted_at')
            ->select(
                'sales_order_items.art_no',
                'sales_order_items.item_name',
                'sales_order_items.sku',
                'sales_order_items.sleeve',
                'sales_order_items.qty',
                'sales_order_items.api_color',
                'sales_order_items.size_id',
                'sales_order_items.category_name',
                'colors.color_name',
                'stock_entry_items.size as stock_size',
                'stock_entry_items.sleeve_type as stock_sleeve',
                'stock_entry_items.barcode'
            )
            ->get();

        $soArtNos = [];
        foreach ($soItems as $item) {
            $norm = strtoupper(preg_replace('/\s+/', '', trim($item->art_no ?? '')));
            if (!empty($norm)) {
                $soArtNos[$norm] = ($soArtNos[$norm] ?? 0) + floatval($item->qty);
            }
        }

        $invRows = DB::table('sales_invoice_items')
            ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
            ->where('sales_invoices.so_id', $soId)
            ->whereNull('sales_invoices.deleted_at')
            ->whereNull('sales_invoice_items.deleted_at')
            ->select(
                'sales_invoice_items.sku',
                'sales_invoice_items.art_no',
                'sales_invoice_items.quantity'
            )
            ->get();

        $invSkuPool = [];
        $invArtNoPool = [];

        foreach ($invRows as $row) {
            $sku = trim($row->sku ?? '');
            $q = floatval($row->quantity ?? 0);
            if (!empty($sku)) {
                $invSkuPool[$sku] = ($invSkuPool[$sku] ?? 0) + $q;
            }
            $norm = strtoupper(preg_replace('/\s+/', '', trim($row->art_no ?? '')));
            if (!empty($norm)) {
                $invArtNoPool[$norm] = ($invArtNoPool[$norm] ?? 0) + $q;
            }
        }

        $lostItems = [];
        $itemSno = 1;
        $totalLostQty = 0;

        foreach ($soItems as $item) {
            $sku = trim($item->barcode ?: ($item->sku ?: ''));
            $norm = strtoupper(preg_replace('/\s+/', '', trim($item->art_no ?? '')));
            $soQ = floatval($item->qty ?? 0);

            // 1. Deduct by exact SKU (barcode) if available in invoice
            $usedFromSku = 0;
            if (!empty($sku) && isset($invSkuPool[$sku]) && $invSkuPool[$sku] > 0) {
                $usedFromSku = min($soQ, $invSkuPool[$sku]);
                $invSkuPool[$sku] -= $usedFromSku;
                if (!empty($norm) && isset($invArtNoPool[$norm])) {
                    $invArtNoPool[$norm] = max(0, $invArtNoPool[$norm] - $usedFromSku);
                }
            }

            $remQ = $soQ - $usedFromSku;

            // 2. If any remaining, deduct from normalized Art No pool
            if ($remQ > 0 && !empty($norm) && isset($invArtNoPool[$norm]) && $invArtNoPool[$norm] > 0) {
                $usedFromArt = min($remQ, $invArtNoPool[$norm]);
                $invArtNoPool[$norm] -= $usedFromArt;
                $remQ -= $usedFromArt;
            }

            if ($remQ > 0) {
                $row = $this->buildLostItemRow($item, $remQ, $itemSno++);
                $lostItems[] = $row;
                $totalLostQty += $remQ;
            }
        }

        $search = $request->search['value'] ?? '';
        $filteredItems = $lostItems;
        if (!empty($search)) {
            $searchLower = strtolower($search);
            $filteredItems = array_values(array_filter($lostItems, function($it) use ($searchLower) {
                return (str_contains(strtolower($it['art_no_raw'] ?? ''), $searchLower))
                    || (str_contains(strtolower($it['description'] ?? ''), $searchLower))
                    || (str_contains(strtolower($it['barcode_raw'] ?? ''), $searchLower))
                    || (str_contains(strtolower($it['color'] ?? ''), $searchLower))
                    || (str_contains(strtolower($it['size'] ?? ''), $searchLower))
                    || (str_contains(strtolower($it['sleeve'] ?? ''), $searchLower));
            }));
        }

        $draw = intval($request->draw ?? 1);
        $start = intval($request->start ?? 0);
        $rawLen = intval($request->length ?? 10);
        $length = ($rawLen > 0 || $rawLen === -1) ? $rawLen : 10;
        $pagedItems = $length != -1 ? array_slice($filteredItems, $start, $length) : $filteredItems;

        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => count($lostItems),
            'recordsFiltered' => count($filteredItems),
            'data'            => $pagedItems,
            'total_lost_qty'  => number_format($totalLostQty, 0),
            'so_no'           => $so->so_no,
            'customer'        => $so->customer_name ?? '-',
            'so_date'         => $so->so_date ? date('d-m-Y', strtotime($so->so_date)) : '-',
            'status'          => true,
            'items'           => $lostItems,
        ]);
    }

    private function buildLostItemRow($item, $qty, $sno = 1)
    {
        $rawSleeve = $item->sleeve ?: ($item->stock_sleeve ?? '');
        if (!empty($rawSleeve) && is_string($rawSleeve) && str_starts_with(trim($rawSleeve), '[')) {
            $decoded = json_decode($rawSleeve, true);
            $rawSleeve = is_array($decoded) && !empty($decoded) ? $decoded[0] : $rawSleeve;
        }
        $sleeveUpper = strtoupper(trim((string)$rawSleeve));
        if ($sleeveUpper === 'FS' || $sleeveUpper === 'F/S' || $sleeveUpper === 'FULL' || $sleeveUpper === 'FULL SLEEVE') {
            $sleeveFormatted = 'Full';
        } elseif ($sleeveUpper === 'HS' || $sleeveUpper === 'H/S' || $sleeveUpper === 'HALF' || $sleeveUpper === 'HALF SLEEVE') {
            $sleeveFormatted = 'Half';
        } else {
            $sleeveFormatted = $rawSleeve ?: '-';
        }

        $color = !empty($item->color_name) ? $item->color_name : (!empty($item->api_color) ? $item->api_color : '-');
        $size = !empty($item->size_id) ? $item->size_id : (!empty($item->stock_size) ? $item->stock_size : '-');
        $barcode = !empty($item->barcode) ? $item->barcode : (!empty($item->sku) ? $item->sku : '-');
        $artNo = !empty($item->art_no) ? $item->art_no : '-';

        $formattedCategory = null;
        if (!empty($item->category_name)) {
            $catStr = str_replace([' ', '_'], '-', trim($item->category_name));
            $parts = array_filter(explode('-', $catStr));
            $formattedParts = array_map(function($p) {
                $pUpper = strtoupper($p);
                if ($pUpper === 'FS' || $pUpper === 'F/S') return 'F/S';
                if ($pUpper === 'HS' || $pUpper === 'H/S') return 'H/S';
                return ucfirst(strtolower($p));
            }, $parts);
            $formattedCategory = implode('-', $formattedParts);
        }
        $desc = $formattedCategory ?: ($item->item_name ?: ($item->sku ?: '-'));

        return [
            'sno'         => $sno,
            'art_no'      => '<span class="fw-bold text-primary">' . htmlspecialchars($artNo) . '</span>',
            'art_no_raw'  => $artNo,
            'description' => htmlspecialchars($desc),
            'barcode'     => '<span class="badge bg-label-secondary font-monospace">' . htmlspecialchars($barcode) . '</span>',
            'barcode_raw' => $barcode,
            'color'       => htmlspecialchars($color),
            'size'        => '<span class="badge bg-label-info">' . htmlspecialchars($size) . '</span>',
            'sleeve'      => '<span class="badge bg-label-secondary">' . htmlspecialchars($sleeveFormatted) . '</span>',
            'qty'         => '<span class="text-danger fw-bold">' . number_format($qty, 0) . '</span>',
            'qty_raw'     => $qty,
        ];
    }

    public function getUrgentOrderDetails($id)
    {

        $so = SalesOrder::with(['customer', 'items.stockEntryItem', 'items.size', 'items.color'])->find($id);

        if (!$so) {
            return response()->json(['status' => false, 'message' => 'Sales Order not found'], 404);
        }

        $dispatchedItemMap = DB::table('sales_invoice_items')->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')->where('sales_invoices.so_id', $id)->whereNull('sales_invoices.deleted_at')->whereNull('sales_invoice_items.deleted_at')->select('sales_invoice_items.art_no', DB::raw('SUM(sales_invoice_items.quantity) as total_dispatched'))->groupBy('sales_invoice_items.art_no')->pluck('total_dispatched', 'art_no');
        $stockMap = DB::table('stock_entry_items')->select('art_no', DB::raw('SUM(qty_in - qty_out) as current_stock'))->where('stock_type', 'finished_goods')->whereNull('deleted_at')->groupBy('art_no')->pluck('current_stock', 'art_no');

        $allBrands = Brand::orderByRaw('LENGTH(code) DESC')->orderByRaw('LENGTH(brand_name) DESC')->get();

        $items = [];
        foreach ($so->items as $item) {
            $artNo = trim($item->art_no ?? '');
            $sku = trim($item->sku ?? '');
            $itemName = trim($item->item_name ?? '');
            $ordered = floatval($item->qty);
            $dispatched = floatval($dispatchedItemMap[$artNo] ?? 0);
            $pending = max(0, $ordered - $dispatched);
            $whStock = floatval($stockMap[$artNo] ?? 0);

            $brandName = '-';
            if ($item->stockEntryItem && $item->stockEntryItem->brand) {
                $brandName = $item->stockEntryItem->brand->brand_name;
            } elseif ($item->brandCategory) {
                $brandName = $item->brandCategory->name;
            } else {
                foreach ($allBrands as $b) {
                    $bCode = trim($b->code);
                    $bName = trim($b->brand_name);
                    if (($bCode && $artNo && stripos($artNo, $bCode) === 0) ||
                        ($bCode && $sku && stripos($sku, $bCode) === 0) ||
                        ($bName && $artNo && stripos($artNo, $bName) !== false) ||
                        ($bName && $sku && stripos($sku, $bName) !== false) ||
                        ($bName && $itemName && stripos($itemName, $bName) !== false)) {
                        $brandName = $b->brand_name;
                        break;
                    }
                }
            }

            $items[] = [
                'art_no' => $artNo ?: '-',
                'item_name' => $item->item_name ?? '-',
                'brand_name' => $brandName,
                'size' => $item->size->size_name ?? ($item->size_id ?? '-'),
                'color' => $item->color->color_name ?? ($item->color_id ?? '-'),
                'ordered_qty' => number_format($ordered, 0),
                'dispatched_qty' => number_format($dispatched, 0),
                'pending_qty' => number_format($pending, 0),
                'wh_stock' => number_format($whStock, 0),
                'stock_status' => $whStock >= $pending ? '<span class="badge bg-label-success">Available</span>' : ($whStock > 0 ? '<span class="badge bg-label-warning">Partial</span>' : '<span class="badge bg-label-danger">Out of Stock</span>')
            ];
        }

        return response()->json([
            'status' => true,
            'so_no' => $so->so_no,
            'orderaxe_ref_id' => $so->orderaxe_ref_id ?? $so->order_no,
            'customer_name' => $so->customer->name ?? '-',
            'so_date' => date('d M Y', strtotime((!empty($so->request_date) && $so->request_date != '0000-00-00') ? $so->request_date : $so->so_date)),
            'delivery_date' => $so->delivery_date ? date('d M Y', strtotime($so->delivery_date)) : '-',
            'status_name' => $so->status,
            'items' => $items
        ]);
    }

    public function getStockInwardSalesDetails(Request $request)
    {
        $draw = intval($request->draw);
        $start = intval($request->start);
        $rawLen = intval($request->length); $length = ($rawLen > 0 || $rawLen === -1) ? $rawLen : 10;
        $search = $request->search['value'] ?? '';

        $brandId = $request->brand_id;
        $brandName = $request->brand_name;
        $brandCode = null;
        if ($brandId) {
            $brandObj = DB::table('brands')->where('id', $brandId)->first();
            if ($brandObj) {
                if (!$brandName) $brandName = $brandObj->brand_name;
                $brandCode = $brandObj->code;
            }
        }
        $fromDate = $request->from_date ? date('Y-m-d', strtotime($request->from_date)) : date('Y-m-d');
        $toDate = $request->to_date ? date('Y-m-d', strtotime($request->to_date)) : date('Y-m-d');
        $storeId = $request->store_id;
        $warehouseId = ($request->warehouse_id && $request->warehouse_id !== 'all') ? $request->warehouse_id : null;

        $makeKey = function($artNo, $sleeve, $size) {
            return strtolower(trim($artNo ?? '')) . '_' . strtolower(trim($sleeve ?? '')) . '_' . strtolower(trim($size ?? ''));
        };

        $makeSimpleKey = function($artNo, $size) {
            return strtolower(trim($artNo ?? '')) . '_' . strtolower(trim($size ?? ''));
        };

        $stockItemsQuery = DB::table('stock_entry_items')
            ->leftJoin('stock_entries', 'stock_entry_items.stock_entry_id', '=', 'stock_entries.id')
            ->leftJoin('styles', 'stock_entry_items.style_id', '=', 'styles.id')
            ->select(
                'stock_entry_items.art_no',
                DB::raw('MAX(stock_entry_items.finished_item_code) as finished_item_code'),
                'stock_entry_items.size',
                'stock_entry_items.sleeve_type',
                DB::raw('MAX(styles.style_name) as style_name'),
                DB::raw('SUM(stock_entry_items.qty_in - stock_entry_items.qty_out) as current_stock')
            )
            ->where('stock_entry_items.stock_type', 'finished_goods')
            ->whereNull('stock_entry_items.deleted_at');

        if ($brandId) {
            $stockItemsQuery->where(function($q) use ($brandId, $brandCode) {
                $q->where('stock_entry_items.brand_id', $brandId);
                if ($brandCode) {
                    $q->orWhere(function($sub) use ($brandCode) {
                        $sub->whereNull('stock_entry_items.brand_id')
                            ->where('stock_entry_items.art_no', 'like', $brandCode . '%');
                    });
                }
            });
        }
        if ($storeId) {
            $stockItemsQuery->where('stock_entry_items.store_location_id', $storeId);
        }
        if ($warehouseId) {
            $stockItemsQuery->where('stock_entry_items.warehouse_id', $warehouseId);
        }
        if ($search) {
            $stockItemsQuery->where(function($q) use ($search) {
                $q->where('stock_entry_items.art_no', 'like', "%{$search}%")
                  ->orWhere('stock_entry_items.finished_item_code', 'like', "%{$search}%")
                  ->orWhere('stock_entry_items.size', 'like', "%{$search}%")
                  ->orWhere('stock_entry_items.sleeve_type', 'like', "%{$search}%")
                  ->orWhere('styles.style_name', 'like', "%{$search}%");
            });
        }

        $stockItems = $stockItemsQuery->groupBy(
            'stock_entry_items.art_no',
            'stock_entry_items.size',
            'stock_entry_items.sleeve_type'
        )->get();

        $inwardQuery = DB::table('stock_entry_items')
            ->leftJoin('stock_entries', 'stock_entry_items.stock_entry_id', '=', 'stock_entries.id')
            ->select(
                'stock_entry_items.art_no',
                'stock_entry_items.sleeve_type',
                'stock_entry_items.size',
                DB::raw('SUM(stock_entry_items.qty_in) as inward_qty')
            )
            ->where('stock_entry_items.stock_type', 'finished_goods')
            ->where('stock_entry_items.qty_in', '>', 0)
            ->whereNull('stock_entry_items.deleted_at')
            ->whereBetween(DB::raw('DATE(COALESCE(stock_entries.stock_date, stock_entry_items.created_at))'), [$fromDate, $toDate]);

        if ($brandId) {
            $inwardQuery->where(function($q) use ($brandId, $brandCode) {
                $q->where('stock_entry_items.brand_id', $brandId);
                if ($brandCode) {
                    $q->orWhere(function($sub) use ($brandCode) {
                        $sub->whereNull('stock_entry_items.brand_id')
                            ->where('stock_entry_items.art_no', 'like', $brandCode . '%');
                    });
                }
            });
        }
        if ($storeId) {
            $inwardQuery->where('stock_entry_items.store_location_id', $storeId);
        }
        if ($warehouseId) {
            $inwardQuery->where('stock_entry_items.warehouse_id', $warehouseId);
        }

        $inwardMap = $inwardQuery->groupBy('stock_entry_items.art_no', 'stock_entry_items.sleeve_type', 'stock_entry_items.size')
            ->get()
            ->keyBy(function($item) use ($makeKey) {
                return $makeKey($item->art_no, $item->sleeve_type, $item->size);
            });

        $inwardTillNowQuery = DB::table('stock_entry_items')
            ->leftJoin('stock_entries', 'stock_entry_items.stock_entry_id', '=', 'stock_entries.id')
            ->select(
                'stock_entry_items.art_no',
                'stock_entry_items.sleeve_type',
                'stock_entry_items.size',
                DB::raw('SUM(stock_entry_items.qty_in) as inward_qty')
            )
            ->where('stock_entry_items.stock_type', 'finished_goods')
            ->where('stock_entry_items.qty_in', '>', 0)
            ->whereNull('stock_entry_items.deleted_at')
            ->where(DB::raw('DATE(COALESCE(stock_entries.stock_date, stock_entry_items.created_at))'), '>=', $fromDate);

        if ($brandId) {
            $inwardTillNowQuery->where(function($q) use ($brandId, $brandCode) {
                $q->where('stock_entry_items.brand_id', $brandId);
                if ($brandCode) {
                    $q->orWhere(function($sub) use ($brandCode) {
                        $sub->whereNull('stock_entry_items.brand_id')
                            ->where('stock_entry_items.art_no', 'like', $brandCode . '%');
                    });
                }
            });
        }
        if ($storeId) {
            $inwardTillNowQuery->where('stock_entry_items.store_location_id', $storeId);
        }
        if ($warehouseId) {
            $inwardTillNowQuery->where('stock_entry_items.warehouse_id', $warehouseId);
        }

        $inwardTillNowMap = $inwardTillNowQuery->groupBy('stock_entry_items.art_no', 'stock_entry_items.sleeve_type', 'stock_entry_items.size')
            ->get()
            ->keyBy(function($item) use ($makeKey) {
                return $makeKey($item->art_no, $item->sleeve_type, $item->size);
            });

        $salesQuery = DB::table('sales_invoice_items')
            ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
            ->leftJoin('stock_entry_items', 'sales_invoice_items.stock_entry_item_id', '=', 'stock_entry_items.id')
            ->select(
                'sales_invoice_items.art_no',
                DB::raw('COALESCE(stock_entry_items.sleeve_type, sales_invoice_items.sleeve_type, "") as sleeve_type'),
                'sales_invoice_items.size',
                DB::raw('SUM(sales_invoice_items.quantity) as sales_qty')
            )
            ->whereNull('sales_invoices.deleted_at')
            ->whereNull('sales_invoice_items.deleted_at')
            ->whereBetween('sales_invoices.inv_date', [$fromDate, $toDate]);

        if ($brandId) {
            $salesQuery->where(function($q) use ($brandId, $brandCode) {
                $q->where('stock_entry_items.brand_id', $brandId);
                if ($brandCode) {
                    $q->orWhere(function($sub) use ($brandCode) {
                        $sub->whereNull('stock_entry_items.brand_id')
                            ->where('sales_invoice_items.art_no', 'like', $brandCode . '%');
                    });
                }
            });
        }
        if ($storeId) {
            $salesQuery->where('sales_invoices.store_id', $storeId);
        }
        if ($warehouseId) {
            $salesQuery->where('stock_entry_items.warehouse_id', $warehouseId);
        }

        $salesMap = $salesQuery->groupBy(
            'sales_invoice_items.art_no',
            DB::raw('COALESCE(stock_entry_items.sleeve_type, sales_invoice_items.sleeve_type, "")'),
            'sales_invoice_items.size'
        )->get()->keyBy(function($item) use ($makeKey) {
            return $makeKey($item->art_no, $item->sleeve_type, $item->size);
        });

        $salesTillNowQuery = DB::table('sales_invoice_items')
            ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
            ->leftJoin('stock_entry_items', 'sales_invoice_items.stock_entry_item_id', '=', 'stock_entry_items.id')
            ->select(
                'sales_invoice_items.art_no',
                DB::raw('COALESCE(stock_entry_items.sleeve_type, sales_invoice_items.sleeve_type, "") as sleeve_type'),
                'sales_invoice_items.size',
                DB::raw('SUM(sales_invoice_items.quantity) as sales_qty')
            )
            ->whereNull('sales_invoices.deleted_at')
            ->whereNull('sales_invoice_items.deleted_at')
            ->where('sales_invoices.inv_date', '>=', $fromDate);

        if ($brandId) {
            $salesTillNowQuery->where(function($q) use ($brandId, $brandCode) {
                $q->where('stock_entry_items.brand_id', $brandId);
                if ($brandCode) {
                    $q->orWhere(function($sub) use ($brandCode) {
                        $sub->whereNull('stock_entry_items.brand_id')
                            ->where('sales_invoice_items.art_no', 'like', $brandCode . '%');
                    });
                }
            });
        }
        if ($storeId) {
            $salesTillNowQuery->where('sales_invoices.store_id', $storeId);
        }
        if ($warehouseId) {
            $salesTillNowQuery->where('stock_entry_items.warehouse_id', $warehouseId);
        }

        $salesTillNowMap = $salesTillNowQuery->groupBy(
            'sales_invoice_items.art_no',
            DB::raw('COALESCE(stock_entry_items.sleeve_type, sales_invoice_items.sleeve_type, "")'),
            'sales_invoice_items.size'
        )->get()->keyBy(function($item) use ($makeKey) {
            return $makeKey($item->art_no, $item->sleeve_type, $item->size);
        });

        // Credit Notes / Sales Return in Date Range
        $returnsQuery = DB::table('credit_note_items')
            ->join('credit_notes', 'credit_note_items.credit_note_id', '=', 'credit_notes.id')
            ->leftJoin('sales_invoice_items', 'credit_note_items.sales_invoice_item_id', '=', 'sales_invoice_items.id')
            ->leftJoin('stock_entry_items', function($join) {
                $join->on('credit_note_items.stock_entry_item_id', '=', 'stock_entry_items.id')
                     ->orOn('sales_invoice_items.stock_entry_item_id', '=', 'stock_entry_items.id');
            })
            ->select(
                DB::raw('COALESCE(credit_note_items.art_no, sales_invoice_items.art_no) as art_no'),
                DB::raw('COALESCE(credit_note_items.sleeve_type, stock_entry_items.sleeve_type, sales_invoice_items.sleeve_type, "") as sleeve_type'),
                DB::raw('COALESCE(credit_note_items.size, sales_invoice_items.size) as size'),
                DB::raw('SUM(credit_note_items.quantity) as return_qty')
            )
            ->whereNull('credit_notes.deleted_at')
            ->whereNull('credit_note_items.deleted_at')
            ->whereBetween('credit_notes.note_date', [$fromDate, $toDate]);

        if ($brandId) {
            $returnsQuery->where(function($q) use ($brandId, $brandCode) {
                $q->where('stock_entry_items.brand_id', $brandId);
                if ($brandCode) {
                    $q->orWhere(function($sub) use ($brandCode) {
                        $sub->whereNull('stock_entry_items.brand_id')
                            ->where(DB::raw('COALESCE(credit_note_items.art_no, sales_invoice_items.art_no)'), 'like', $brandCode . '%');
                    });
                }
            });
        }
        if ($warehouseId) {
            $returnsQuery->where('stock_entry_items.warehouse_id', $warehouseId);
        }

        $returnsMap = $returnsQuery->groupBy(
            DB::raw('COALESCE(credit_note_items.art_no, sales_invoice_items.art_no)'),
            DB::raw('COALESCE(credit_note_items.sleeve_type, stock_entry_items.sleeve_type, sales_invoice_items.sleeve_type, "")'),
            DB::raw('COALESCE(credit_note_items.size, sales_invoice_items.size)')
        )->get()->keyBy(function($item) use ($makeKey) {
            return $makeKey($item->art_no, $item->sleeve_type, $item->size);
        });

        $returnsTillNowQuery = DB::table('credit_note_items')
            ->join('credit_notes', 'credit_note_items.credit_note_id', '=', 'credit_notes.id')
            ->leftJoin('sales_invoice_items', 'credit_note_items.sales_invoice_item_id', '=', 'sales_invoice_items.id')
            ->leftJoin('stock_entry_items', function($join) {
                $join->on('credit_note_items.stock_entry_item_id', '=', 'stock_entry_items.id')
                     ->orOn('sales_invoice_items.stock_entry_item_id', '=', 'stock_entry_items.id');
            })
            ->select(
                DB::raw('COALESCE(credit_note_items.art_no, sales_invoice_items.art_no) as art_no'),
                DB::raw('COALESCE(credit_note_items.sleeve_type, stock_entry_items.sleeve_type, sales_invoice_items.sleeve_type, "") as sleeve_type'),
                DB::raw('COALESCE(credit_note_items.size, sales_invoice_items.size) as size'),
                DB::raw('SUM(credit_note_items.quantity) as return_qty')
            )
            ->whereNull('credit_notes.deleted_at')
            ->whereNull('credit_note_items.deleted_at')
            ->where('credit_notes.note_date', '>=', $fromDate);

        if ($brandId) {
            $returnsTillNowQuery->where(function($q) use ($brandId, $brandCode) {
                $q->where('stock_entry_items.brand_id', $brandId);
                if ($brandCode) {
                    $q->orWhere(function($sub) use ($brandCode) {
                        $sub->whereNull('stock_entry_items.brand_id')
                            ->where(DB::raw('COALESCE(credit_note_items.art_no, sales_invoice_items.art_no)'), 'like', $brandCode . '%');
                    });
                }
            });
        }
        if ($warehouseId) {
            $returnsTillNowQuery->where('stock_entry_items.warehouse_id', $warehouseId);
        }

        $returnsTillNowMap = $returnsTillNowQuery->groupBy(
            DB::raw('COALESCE(credit_note_items.art_no, sales_invoice_items.art_no)'),
            DB::raw('COALESCE(credit_note_items.sleeve_type, stock_entry_items.sleeve_type, sales_invoice_items.sleeve_type, "")'),
            DB::raw('COALESCE(credit_note_items.size, sales_invoice_items.size)')
        )->get()->keyBy(function($item) use ($makeKey) {
            return $makeKey($item->art_no, $item->sleeve_type, $item->size);
        });

        $totOpStock = 0;
        $totInward = 0;
        $totSales = 0;
        $totClosing = 0;
        $totSalesReturn = 0;
        $totNetClosing = 0;

        $allItems = [];
        foreach ($stockItems as $item) {
            $key = $makeKey($item->art_no, $item->sleeve_type, $item->size);
            $simpleKey = $makeSimpleKey($item->art_no, $item->size);

            $currStock = floatval($item->current_stock ?? 0);
            $inwardTillNow = floatval($inwardTillNowMap[$key]->inward_qty ?? ($inwardTillNowMap[$simpleKey]->inward_qty ?? 0));
            $salesTillNow = floatval($salesTillNowMap[$key]->sales_qty ?? ($salesTillNowMap[$simpleKey]->sales_qty ?? 0));
            $returnsTillNow = floatval($returnsTillNowMap[$key]->return_qty ?? ($returnsTillNowMap[$simpleKey]->return_qty ?? 0));

            $opStock = max(0, $currStock - $inwardTillNow + $salesTillNow - $returnsTillNow);
            $inward = floatval($inwardMap[$key]->inward_qty ?? ($inwardMap[$simpleKey]->inward_qty ?? 0));
            $sales = floatval($salesMap[$key]->sales_qty ?? ($salesMap[$simpleKey]->sales_qty ?? 0));
            $closing = $opStock + $inward - $sales;
            $salesReturn = floatval($returnsMap[$key]->return_qty ?? ($returnsMap[$simpleKey]->return_qty ?? 0));
            $netClosing = $closing + $salesReturn;

            if ($opStock == 0 && $inward == 0 && $sales == 0 && $closing == 0 && $salesReturn == 0 && $netClosing == 0) {
                continue;
            }

            $totOpStock += $opStock;
            $totInward += $inward;
            $totSales += $sales;
            $totClosing += $closing;
            $totSalesReturn += $salesReturn;
            $totNetClosing += $netClosing;

            $artNoClean = (string)($item->art_no ?? '');
            $artNoSafe = addslashes($artNoClean);
            $bNameSafe = addslashes($brandName ?? '');
            $salesLink = $sales > 0 
                ? '<a href="javascript:void(0)" onclick="drillDownToStockInwardSalesInvoices(event, ' . ($brandId ?: 'null') . ', \'' . $bNameSafe . '\', \'' . $artNoSafe . '\')" class="text-primary fw-bold text-decoration-underline" title="View Sales Invoices">' . number_format($sales, 0) . '</a>'
                : '';

            $allItems[] = [
                'art_no' => '<strong>' . htmlspecialchars($item->art_no ?: '-') . '</strong>',
                'item_name' => htmlspecialchars($item->finished_item_code ?: ($item->style_name ?: '-')),
                'sleeve_type' => htmlspecialchars($item->sleeve_type ?: '-'),
                'size' => htmlspecialchars($item->size ?: '-'),
                'op_stock' => $opStock > 0 ? number_format($opStock, 0) : '',
                'inward' => $inward > 0 ? number_format($inward, 0) : '',
                'sales' => $salesLink,
                'closing' => $closing > 0 ? number_format($closing, 0) : '',
                'sales_return' => $salesReturn > 0 ? number_format($salesReturn, 0) : '',
                'net_closing' => '<strong class="text-primary">' . number_format($netClosing, 0) . '</strong>',
            ];
        }

        $recordsTotal = count($allItems);
        $recordsFiltered = $recordsTotal;

        $pagedData = $length != -1 ? array_slice($allItems, $start, $length) : $allItems;

        if ($brandId) {
            $brandCurrStockQuery = DB::table('stock_entry_items')
                ->leftJoin('brands', function($join) {
                    $join->on(function($query) {
                        $query->on('stock_entry_items.brand_id', '=', 'brands.id')
                            ->orOn(function($sub) {
                                $sub->whereNull('stock_entry_items.brand_id')
                                    ->on('stock_entry_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                                    ->whereRaw("NOT EXISTS (
                                        SELECT 1 FROM brands b2 
                                        WHERE stock_entry_items.art_no LIKE CONCAT(b2.code, '%') 
                                        AND LENGTH(b2.code) > LENGTH(brands.code)
                                    )");
                            });
                    });
                })
                ->where('stock_entry_items.stock_type', 'finished_goods')
                ->whereNull('stock_entry_items.deleted_at')
                ->where('brands.id', $brandId);
            if ($storeId) $brandCurrStockQuery->where('stock_entry_items.store_location_id', $storeId);
            if ($warehouseId) $brandCurrStockQuery->where('stock_entry_items.warehouse_id', $warehouseId);
            $bCurrStock = floatval($brandCurrStockQuery->sum(DB::raw('stock_entry_items.qty_in - stock_entry_items.qty_out')));

            $inwQuery = DB::table('stock_entry_items')
                ->leftJoin('stock_entries', 'stock_entry_items.stock_entry_id', '=', 'stock_entries.id')
                ->leftJoin('brands', function($join) {
                    $join->on(function($query) {
                        $query->on('stock_entry_items.brand_id', '=', 'brands.id')
                            ->orOn(function($sub) {
                                $sub->whereNull('stock_entry_items.brand_id')
                                    ->on('stock_entry_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                                    ->whereRaw("NOT EXISTS (
                                        SELECT 1 FROM brands b2 
                                        WHERE stock_entry_items.art_no LIKE CONCAT(b2.code, '%') 
                                        AND LENGTH(b2.code) > LENGTH(brands.code)
                                    )");
                            });
                    });
                })
                ->where('stock_entry_items.stock_type', 'finished_goods')
                ->where('stock_entry_items.qty_in', '>', 0)
                ->whereNull('stock_entry_items.deleted_at')
                ->whereBetween(DB::raw('DATE(COALESCE(stock_entries.stock_date, stock_entry_items.created_at))'), [$fromDate, $toDate])
                ->where('brands.id', $brandId);
            if ($storeId) $inwQuery->where('stock_entry_items.store_location_id', $storeId);
            if ($warehouseId) $inwQuery->where('stock_entry_items.warehouse_id', $warehouseId);
            $brandInward = floatval($inwQuery->sum('stock_entry_items.qty_in'));

            $inwTillNowQuery = DB::table('stock_entry_items')
                ->leftJoin('stock_entries', 'stock_entry_items.stock_entry_id', '=', 'stock_entries.id')
                ->leftJoin('brands', function($join) {
                    $join->on(function($query) {
                        $query->on('stock_entry_items.brand_id', '=', 'brands.id')
                            ->orOn(function($sub) {
                                $sub->whereNull('stock_entry_items.brand_id')
                                    ->on('stock_entry_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                                    ->whereRaw("NOT EXISTS (
                                        SELECT 1 FROM brands b2 
                                        WHERE stock_entry_items.art_no LIKE CONCAT(b2.code, '%') 
                                        AND LENGTH(b2.code) > LENGTH(brands.code)
                                    )");
                            });
                    });
                })
                ->where('stock_entry_items.stock_type', 'finished_goods')
                ->where('stock_entry_items.qty_in', '>', 0)
                ->whereNull('stock_entry_items.deleted_at')
                ->where(DB::raw('DATE(COALESCE(stock_entries.stock_date, stock_entry_items.created_at))'), '>=', $fromDate)
                ->where('brands.id', $brandId);
            if ($storeId) $inwTillNowQuery->where('stock_entry_items.store_location_id', $storeId);
            if ($warehouseId) $inwTillNowQuery->where('stock_entry_items.warehouse_id', $warehouseId);
            $bInwTillNow = floatval($inwTillNowQuery->sum('stock_entry_items.qty_in'));

            $salesQuery = DB::table('sales_invoice_items')
                ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
                ->leftJoin('stock_entry_items', 'sales_invoice_items.stock_entry_item_id', '=', 'stock_entry_items.id')
                ->leftJoin('brands', function($join) {
                    $join->on(function($query) {
                        $query->on('stock_entry_items.brand_id', '=', 'brands.id')
                            ->orOn(function($sub) {
                                $sub->whereNull('stock_entry_items.brand_id')
                                    ->on('sales_invoice_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                                    ->whereRaw("NOT EXISTS (
                                        SELECT 1 FROM brands b2 
                                        WHERE sales_invoice_items.art_no LIKE CONCAT(b2.code, '%') 
                                        AND LENGTH(b2.code) > LENGTH(brands.code)
                                    )");
                            });
                    });
                })
                ->whereNull('sales_invoices.deleted_at')
                ->whereNull('sales_invoice_items.deleted_at')
                ->whereBetween('sales_invoices.inv_date', [$fromDate, $toDate])
                ->where('brands.id', $brandId);
            if ($storeId) $salesQuery->where('sales_invoices.store_location_id', $storeId);
            if ($warehouseId) $salesQuery->where('stock_entry_items.warehouse_id', $warehouseId);
            $brandSales = floatval($salesQuery->sum('sales_invoice_items.quantity'));

            $salesTillNowQuery = DB::table('sales_invoice_items')
                ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
                ->leftJoin('stock_entry_items', 'sales_invoice_items.stock_entry_item_id', '=', 'stock_entry_items.id')
                ->leftJoin('brands', function($join) {
                    $join->on(function($query) {
                        $query->on('stock_entry_items.brand_id', '=', 'brands.id')
                            ->orOn(function($sub) {
                                $sub->whereNull('stock_entry_items.brand_id')
                                    ->on('sales_invoice_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                                    ->whereRaw("NOT EXISTS (
                                        SELECT 1 FROM brands b2 
                                        WHERE sales_invoice_items.art_no LIKE CONCAT(b2.code, '%') 
                                        AND LENGTH(b2.code) > LENGTH(brands.code)
                                    )");
                            });
                    });
                })
                ->whereNull('sales_invoices.deleted_at')
                ->whereNull('sales_invoice_items.deleted_at')
                ->where('sales_invoices.inv_date', '>=', $fromDate)
                ->where('brands.id', $brandId);
            if ($storeId) $salesTillNowQuery->where('sales_invoices.store_location_id', $storeId);
            if ($warehouseId) $salesTillNowQuery->where('stock_entry_items.warehouse_id', $warehouseId);
            $bSalTillNow = floatval($salesTillNowQuery->sum('sales_invoice_items.quantity'));

            $retQuery = DB::table('credit_note_items')
                ->join('credit_notes', 'credit_note_items.credit_note_id', '=', 'credit_notes.id')
                ->leftJoin('sales_invoice_items', 'credit_note_items.sales_invoice_item_id', '=', 'sales_invoice_items.id')
                ->leftJoin('stock_entry_items', 'sales_invoice_items.stock_entry_item_id', '=', 'stock_entry_items.id')
                ->leftJoin('brands', function($join) {
                    $join->on(function($query) {
                        $query->on('stock_entry_items.brand_id', '=', 'brands.id')
                            ->orOn(function($sub) {
                                $sub->whereNull('stock_entry_items.brand_id')
                                    ->on('sales_invoice_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                                    ->whereRaw("NOT EXISTS (
                                        SELECT 1 FROM brands b2 
                                        WHERE sales_invoice_items.art_no LIKE CONCAT(b2.code, '%') 
                                        AND LENGTH(b2.code) > LENGTH(brands.code)
                                    )");
                            });
                    });
                })
                ->whereNull('credit_notes.deleted_at')
                ->whereNull('credit_note_items.deleted_at')
                ->whereBetween('credit_notes.note_date', [$fromDate, $toDate])
                ->where('brands.id', $brandId);
            if ($warehouseId) $retQuery->where('stock_entry_items.warehouse_id', $warehouseId);
            $brandReturns = floatval($retQuery->sum('credit_note_items.quantity'));

            $retTillNowQuery = DB::table('credit_note_items')
                ->join('credit_notes', 'credit_note_items.credit_note_id', '=', 'credit_notes.id')
                ->leftJoin('sales_invoice_items', 'credit_note_items.sales_invoice_item_id', '=', 'sales_invoice_items.id')
                ->leftJoin('stock_entry_items', 'sales_invoice_items.stock_entry_item_id', '=', 'stock_entry_items.id')
                ->leftJoin('brands', function($join) {
                    $join->on(function($query) {
                        $query->on('stock_entry_items.brand_id', '=', 'brands.id')
                            ->orOn(function($sub) {
                                $sub->whereNull('stock_entry_items.brand_id')
                                    ->on('sales_invoice_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                                    ->whereRaw("NOT EXISTS (
                                        SELECT 1 FROM brands b2 
                                        WHERE sales_invoice_items.art_no LIKE CONCAT(b2.code, '%') 
                                        AND LENGTH(b2.code) > LENGTH(brands.code)
                                    )");
                            });
                    });
                })
                ->whereNull('credit_notes.deleted_at')
                ->whereNull('credit_note_items.deleted_at')
                ->where('credit_notes.note_date', '>=', $fromDate)
                ->where('brands.id', $brandId);
            if ($warehouseId) $retTillNowQuery->where('stock_entry_items.warehouse_id', $warehouseId);
            $bRetTillNow = floatval($retTillNowQuery->sum('credit_note_items.quantity'));

            $brandOpStock = max(0, $bCurrStock - $bInwTillNow + $bSalTillNow - $bRetTillNow);
            $brandClosing = $brandOpStock + $brandInward - $brandSales;
            $brandNetClosing = $brandClosing + $brandReturns;

            $brandTotals = [
                'op_stock' => number_format($brandOpStock, 0),
                'inward' => number_format($brandInward, 0),
                'sales' => number_format($brandSales, 0),
                'closing' => number_format($brandClosing, 0),
                'sales_return' => number_format($brandReturns, 0),
                'net_closing' => number_format($brandNetClosing, 0),
            ];
        } else {
            $brandTotals = [
                'op_stock' => number_format($totOpStock, 0),
                'inward' => number_format($totInward, 0),
                'sales' => number_format($totSales, 0),
                'closing' => number_format($totClosing, 0),
                'sales_return' => number_format($totSalesReturn, 0),
                'net_closing' => number_format($totNetClosing, 0),
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $pagedData,
            'totals' => $brandTotals
        ]);
    }

    public function getStockInwardSalesInvoices(Request $request)
    {
        $draw = intval($request->draw);
        $start = intval($request->start);
        $rawLen = intval($request->length); $length = ($rawLen > 0 || $rawLen === -1) ? $rawLen : 10;
        $search = $request->search['value'] ?? '';

        $brandId = $request->brand_id;
        $artNo = $request->art_no;
        $fromDate = $request->from_date ? date('Y-m-d', strtotime($request->from_date)) : date('Y-m-d');
        $toDate = $request->to_date ? date('Y-m-d', strtotime($request->to_date)) : date('Y-m-d');
        $storeId = $request->store_id;
        $warehouseId = ($request->warehouse_id && $request->warehouse_id !== 'all') ? $request->warehouse_id : null;
        $customerId = $request->customer_id;

        $query = DB::table('sales_invoice_items')
            ->join('sales_invoices', 'sales_invoice_items.sales_invoice_id', '=', 'sales_invoices.id')
            ->leftJoin('customers', 'sales_invoices.customer_id', '=', 'customers.id')
            ->leftJoin('sales_orders', 'sales_invoices.so_id', '=', 'sales_orders.id')
            ->leftJoin('stock_entry_items', 'sales_invoice_items.stock_entry_item_id', '=', 'stock_entry_items.id')
            ->leftJoin('brands', function($join) {
                $join->on(function($query) {
                    $query->on('stock_entry_items.brand_id', '=', 'brands.id')
                        ->orOn(function($sub) {
                            $sub->whereNull('stock_entry_items.brand_id')
                                ->on('sales_invoice_items.art_no', 'LIKE', DB::raw("CONCAT(brands.code, '%')"))
                                ->whereRaw("NOT EXISTS (
                                    SELECT 1 FROM brands b2 
                                    WHERE sales_invoice_items.art_no LIKE CONCAT(b2.code, '%') 
                                    AND LENGTH(b2.code) > LENGTH(brands.code)
                                )");
                        });
                });
            })
            ->whereNull('sales_invoices.deleted_at')
            ->whereNull('sales_invoice_items.deleted_at')
            ->whereBetween('sales_invoices.inv_date', [$fromDate, $toDate]);

        if ($brandId) {
            $query->where('brands.id', $brandId);
        }
        if ($artNo) {
            $query->where('sales_invoice_items.art_no', $artNo);
        }
        if ($warehouseId) {
            $query->where('stock_entry_items.warehouse_id', $warehouseId);
        }
        if ($customerId) {
            $query->where('sales_invoices.customer_id', $customerId);
        }
        if ($storeId) {
            $query->where('sales_invoices.store_id', $storeId);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('sales_invoices.inv_no', 'like', "%{$search}%");

                if (preg_match('/^CD\/(\d+)/i', $search, $sm)) {
                    $sNum = (int)$sm[1];
                    if ($sNum > SalesInvoice::CDW_CD_OFFSET) {
                        $dbNum = ($sNum >= 316) ? ($sNum - SalesInvoice::CDW_CD_OFFSET + 1) : ($sNum - SalesInvoice::CDW_CD_OFFSET);
                        $q->orWhere('sales_invoices.inv_no', 'like', "%CDW/{$dbNum}%");
                    }
                } elseif (is_numeric(trim($search))) {
                    $numVal = (int)trim($search);
                    if ($numVal > SalesInvoice::CDW_CD_OFFSET) {
                        $cdwNum = ($numVal >= 316) ? ($numVal - SalesInvoice::CDW_CD_OFFSET + 1) : ($numVal - SalesInvoice::CDW_CD_OFFSET);
                        $q->orWhere('sales_invoices.inv_no', 'like', "%CDW/{$cdwNum}/%");
                    }
                } elseif (stripos($search, 'CD') !== false && stripos($search, 'CDW') === false) {
                    $q->orWhere('sales_invoices.inv_no', 'like', "%CDW/%");
                }

                $q->orWhere('sales_orders.so_no', 'like', "%{$search}%")
                  ->orWhere('customers.name', 'like', "%{$search}%")
                  ->orWhere('sales_invoice_items.art_no', 'like', "%{$search}%");
            });
        }

        $query->select(
            'sales_invoices.id as invoice_id',
            'sales_invoices.inv_no',
            'sales_invoices.inv_date',
            'sales_invoices.invoice_status',
            'sales_invoices.grand_total',
            'sales_orders.so_no',
            'customers.name as customer_name',
            DB::raw('GROUP_CONCAT(DISTINCT sales_invoice_items.art_no SEPARATOR ", ") as art_nos'),
            DB::raw('SUM(sales_invoice_items.quantity) as sold_qty'),
            DB::raw('SUM(sales_invoice_items.amount) as items_amount')
        )->groupBy(
            'sales_invoices.id',
            'sales_invoices.inv_no',
            'sales_invoices.inv_date',
            'sales_invoices.invoice_status',
            'sales_invoices.grand_total',
            'sales_orders.so_no',
            'customers.name'
        );

        $recordsTotal = DB::query()->fromSub($query, 'sub')->count();
        $recordsFiltered = $recordsTotal;

        $query->orderBy('sales_invoices.id', 'desc');

        if ($length != -1) {
            $query->skip($start)->take($length);
        }

        $rows = $query->get();

        $data = [];
        $sno = $start + 1;
        $totSoldQty = 0;
        $totAmount = 0;

        foreach ($rows as $row) {
            $soldQty = floatval($row->sold_qty ?? 0);
            $amount = floatval($row->items_amount ?? 0);
            $totSoldQty += $soldQty;
            $totAmount += $amount;

            $invUrl = url('sales_invoices/view/' . $row->invoice_id);
            $displayInvNo = SalesInvoice::formatDisplayInvNo($row->inv_no);
            $invBadge = '<a href="' . $invUrl . '" target="_blank" class="fw-bold text-primary text-decoration-underline">' . htmlspecialchars($displayInvNo) . '</a>';

            $statusBadge = '<span class="badge bg-label-success">Active</span>';
            if ($row->invoice_status == 1 || strtolower($row->invoice_status) === 'paid') {
                $statusBadge = '<span class="badge bg-label-success">Paid</span>';
            } elseif ($row->invoice_status == 2 || strtolower($row->invoice_status) === 'partially paid') {
                $statusBadge = '<span class="badge bg-label-warning">Partial</span>';
            } elseif (strtolower($row->invoice_status) === 'cancelled') {
                $statusBadge = '<span class="badge bg-label-danger">Cancelled</span>';
            }

            $actionBtn = '<a href="' . $invUrl . '" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2"><i class="ri ri-eye-line me-1"></i>View</a>';

            $data[] = [
                'sno' => $sno++,
                'inv_no' => $invBadge,
                'inv_date' => $row->inv_date ? date('d-m-Y', strtotime($row->inv_date)) : '-',
                'so_no' => htmlspecialchars($row->so_no ?: '-'),
                'customer' => htmlspecialchars($row->customer_name ?: '-'),
                'art_nos' => htmlspecialchars($row->art_nos ?: '-'),
                'sold_qty' => number_format($soldQty, 0),
                'amount' => '₹' . number_format($amount, 2),
                'status' => $statusBadge,
                'action' => $actionBtn
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
            'totals' => [
                'sold_qty' => number_format($totSoldQty, 0),
                'amount' => '₹' . number_format($totAmount, 2),
            ]
        ]);
    }
}

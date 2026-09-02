<?php

namespace App\Http\Controllers;


use App\Models\ProductionReceipt;
use App\Models\ProductionReceiptItem;
use App\Models\JobCardEntry;
use App\Models\StoreType;
use App\Models\StockEntry;
use App\Models\StockEntryItem;
use App\Models\Task;
use App\Models\StoreLocation;
use App\Models\Item;
use App\Models\Warehouse;
use App\Models\WarehouseBrandCapacity;
use App\Models\Brand;
use App\Models\Style;
use App\Models\User;
use App\Models\JobCardMatrixQuantity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exports\ProductionReceiptExport;
use Maatwebsite\Excel\Facades\Excel;

class ProductionReceiptController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view production-receipts')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $query = ProductionReceipt::with(['jobCard', 'storeType', 'storeLocation', 'warehouse'])->orderBy('id', 'desc');

            $totalRecords = $query->count();

            if ($request->filled('warehouse_id')) {
                $query->where('warehouse_id', $request->warehouse_id);
            }

            if ($request->filled('store_type_id')) {
                $query->where('store_type_id', $request->store_type_id);
            }

            if ($request->filled('date_range')) {
                try {
                    $rangeStr = $request->date_range;
                    if (str_contains($rangeStr, ' to ')) {
                        $dates = explode(' to ', $rangeStr);
                        $fromDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[0]))->format('Y-m-d');
                        $toDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[1]))->format('Y-m-d');
                        $query->whereDate('receipt_date', '>=', $fromDate)
                              ->whereDate('receipt_date', '<=', $toDate);
                    } else {
                        $fromDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($rangeStr))->format('Y-m-d');
                        $query->whereDate('receipt_date', '=', $fromDate);
                    }
                } catch (\Exception $e) {}
            } elseif ($request->filled('from_date') || $request->filled('to_date')) {
                if ($request->filled('from_date')) {
                    try {
                        $fromDate = \Carbon\Carbon::createFromFormat('d-m-Y', $request->from_date)->format('Y-m-d');
                        $query->whereDate('receipt_date', '>=', $fromDate);
                    } catch (\Exception $e) {}
                }
                if ($request->filled('to_date')) {
                    try {
                        $toDate = \Carbon\Carbon::createFromFormat('d-m-Y', $request->to_date)->format('Y-m-d');
                        $query->whereDate('receipt_date', '<=', $toDate);
                    } catch (\Exception $e) {}
                }
            }

            if ($request->has('search') && !empty($request->input('search')['value'])) {
                $search = $request->input('search')['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('receipt_no', 'like', "%{$search}%")
                        ->orWhereHas('jobCard', function ($q2) use ($search) {
                            $q2->where('job_card_no', 'like', "%{$search}%");
                        })
                        ->orWhereHas('storeType', function ($q3) use ($search) {
                            $q3->where('store_type_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('storeLocation', function ($q4) use ($search) {
                            $q4->where('store_location', 'like', "%{$search}%");
                        })
                        ->orWhereHas('warehouse', function ($q5) use ($search) {
                            $q5->where('warehouse_name', 'like', "%{$search}%");
                        });
                });
            }

            $filteredRecords = $query->count();

            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            if ($length != -1) {
                $query->skip($start)->take($length);
            }

            $receipts = $query->get();
            $data = [];
            $i = $start + 1;

            foreach ($receipts as $row) {
                $action = '<div class="button-box">';
                if ((auth()->id() == 1 || auth()->user()->can('edit production-receipts')) && $row->status !== 'Posted') {
                    $action .= '<a href="' . url('production_receipts/add/' . $row->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }
                if (auth()->id() == 1 || auth()->user()->can('view_details production-receipts')) {
                    $action .= '<a href="' . url('production_receipts/view/' . $row->id) . '" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>';
                }
                $action .= '</div>';

                $statusBadge = $row->status == 'Posted'
                    ? '<span class="badge bg-label-success">Posted</span>'
                    : '<span class="badge bg-label-warning">Draft</span>';

                $data[] = [
                    'DT_RowIndex' => $i++,
                    'receipt_no' => $row->receipt_no ?? ('RCPT-' . str_pad($row->id, 4, '0', STR_PAD_LEFT)),
                    'job_card_no' => $row->jobCard ? $row->jobCard->job_card_no : '-',
                    'receipt_date' => $row->receipt_date ? date('d-m-Y', strtotime($row->receipt_date)) : '-',
                    'warehouse' => $row->warehouse ? $row->warehouse->warehouse_name : '-',
                    'store' => $row->storeType ? $row->storeType->store_type_name : '-',
                    'status' => $statusBadge,
                    'store_location' => $row->storeLocation ? $row->storeLocation->store_location : '-',
                    'action' => $action,
                ];
            }

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        }

        $warehouses = Warehouse::where('status', 'Active')->orderBy('id','desc')->get();
        $storeTypes = \App\Models\StoreType::where('status', 'Active')->orderBy('id','desc')->get();

        return view('production_receipts.view', compact('warehouses', 'storeTypes'));
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit production-receipts')) {
                return unauthorizedRedirect();
            }
        }
        else {
            if (auth()->id() != 1 && !auth()->user()->can('create production-receipts')) {
                return unauthorizedRedirect();
            }
        }

        $receipt = $id ? ProductionReceipt::with(['items'])->findOrFail($id) : null;

        if ($receipt && $receipt->status === 'Posted') {
            return redirect('production_receipts')->with('error', 'Cannot edit a Posted Production Receipt.');
        }

        $fullyReceivedJobCardIds = $this->getFullyReceivedJobCardIds($id);

        if ($id) {
            $currentReceipt = ProductionReceipt::find($id);
            $jobCards = JobCardEntry::with('serviceProvider')
                ->where(function ($query) use ($currentReceipt, $fullyReceivedJobCardIds) {
                $query->whereNotIn('id', $fullyReceivedJobCardIds);
                if ($currentReceipt && $currentReceipt->job_card_id) {
                    $query->orWhere('id', $currentReceipt->job_card_id);
                }
            })->orderBy('id', 'desc')->get();
        }
        else {
            $jobCards = JobCardEntry::with('serviceProvider')
                ->whereNotIn('id', $fullyReceivedJobCardIds)
                ->orderBy('id', 'desc')->get();
        }
        $storeTypes = StoreType::where('status', 'Active')->orderBy('id','desc')->get();
        $storeLocations = StoreLocation::where('status', 'Active')->orderBy('id','desc')->get();
        $employees = User::where('status', 'Active')->where('id', '!=', 1)->orderBy('id','desc')->get();

        if ($request->isMethod('post')) {
            $rules = [
                'job_card_id' => 'required|exists:job_card_entries,id',
                'receipt_date' => 'required|date_format:d-m-Y',
                'doc_date' => 'required|date_format:d-m-Y',
                'warehouse_id' => 'required|exists:warehouses,id',
                'store_type_id' => 'required|exists:store_types,id',
                'store_location_id' => 'required|exists:store_locations,id',
                'status' => 'required|in:Draft,Posted',
                'items' => 'required|array|min:1',
                'remarks' => 'nullable|min:5|max:255',
            ];

            $messages = [
                'required' => 'This field is required.',
                'min' => 'This field must be at least :min characters.',
                'max' => 'This field should not be more than :max characters.',
                'unique' => 'This field already exists.',
            ];

            $request->validate($rules, $messages);

            $capacityError = $this->validateWarehouseBrandStyleCapacity($request, $id);
            if ($capacityError) {
                return redirect()->back()->withInput()->withErrors([
                    'warehouse_id' => $capacityError
                ]);
            }

            DB::beginTransaction();
            try {

                $orderDueDate = $request->order_due_date ? date('Y-m-d', strtotime($request->order_due_date)) : null;
                if (!$orderDueDate && $request->job_card_id) {
                    $selectedJc = JobCardEntry::find($request->job_card_id);
                    if ($selectedJc && $selectedJc->delivery_date) {
                        $orderDueDate = date('Y-m-d', strtotime($selectedJc->delivery_date));
                    }
                }

                $data = [

                    'job_card_id' => $request->job_card_id,
                    'employee_id' => $request->employee_id,
                    'order_due_date' => $orderDueDate,
                    'receipt_no' => $request->receipt_no ?: ($receipt->receipt_no ?? ('RCPT-' . date('Y') . '-' . str_pad(ProductionReceipt::count() + 1, 4, '0', STR_PAD_LEFT))),
                    'receipt_date' => date('Y-m-d', strtotime($request->receipt_date)),
                    'doc_no' => $request->doc_no,
                    'doc_date' => $request->doc_date ? date('Y-m-d', strtotime($request->doc_date)) : null,
                    'store_type_id' => $request->store_type_id,
                    'store_location_id' => $request->store_location_id,
                    'warehouse_id' => $request->warehouse_id ?? null,
                    'status' => $request->status,
                    'remarks' => $request->remarks,
                ];

                if ($receipt) {
                    $oldData = $receipt->load('items')->toArray();
                    $data['updated_by'] = Auth::id();
                    $receipt->update($data);
                    $receipt->items()->delete();
                }
                else {
                    $data['created_by'] = Auth::id();
                    $receipt = ProductionReceipt::create($data);
                }

                $jobCardForArtResolution = null;
                if ($request->job_card_id) {
                    $jobCardForArtResolution = JobCardEntry::with(['fabricDetails.quantities'])->find($request->job_card_id);
                }

                $hasQty = false;
                if ($request->has('items') && is_array($request->items)) {
                    foreach ($request->items as $itemData) {
                        if (floatval($itemData['scan_qty'] ?? 0) > 0) {
                            $hasQty = true;
                            break;
                        }
                    }
                }

                if (!$hasQty) {
                    throw new \Exception('Please enter Qty To Receive for at least one item.');
                }
                if ($request->has('items') && is_array($request->items)) {
                    foreach ($request->items as $itemData) {
                        $scanQty = floatval($itemData['scan_qty'] ?? 0);

                        if ($scanQty > 0) {
                            $completedQty = floatval($itemData['completed_qty'] ?? 0);
                            $alreadyReceived = floatval($itemData['qty_already_received'] ?? 0);
                            $qtyToReceive = $scanQty;
                            $maxAllowed = $completedQty - $alreadyReceived;

                            if ($qtyToReceive > ($maxAllowed + 0.0001)) {
                                throw new \Exception('Qty To Receive (' . $qtyToReceive . ') cannot exceed available quantity (' . $maxAllowed . ') for item: ' . ($itemData['item_name'] ?? ''));
                            }

                            $balanceQty = $maxAllowed - $qtyToReceive;
                            $unitPrice = floatval($itemData['unit_price'] ?? 0);
                            $resolvedArtNo = $this->resolveReceiptItemArtNo($jobCardForArtResolution, $itemData);
                            ProductionReceiptItem::create([
                                'production_receipt_id' => $receipt->id,
                                'item_id' => $itemData['item_id'] ?? null,
                                'item_code' => $itemData['item_code'] ?? '',
                                'item_name' => $itemData['item_name'] ?? '',
                                'art_no' => $resolvedArtNo,
                                'size' => $itemData['size'] ?? null,
                                'color_id' => $itemData['color_id'] ?? null,
                                'description' => $itemData['description'] ?? '',
                                'size_variant' => $itemData['size_variant'] ?? '',
                                'unit_price' => $unitPrice,
                                'mrp' => floatval($itemData['mrp'] ?? 0), 
                                'total_value' => $qtyToReceive * $unitPrice,
                                'uom_id' => $itemData['uom_id'] ?? null,
                                'uom_code' => $itemData['uom_code'] ?? '',
                                'ordered_qty' => floatval($itemData['ordered_qty'] ?? 0),
                                'completed_qty' => $completedQty,
                                'qty_already_received' => $alreadyReceived,
                                'scan_qty' => floatval($itemData['scan_qty'] ?? 0),
                                'damage_qty' => floatval($itemData['damage_qty'] ?? 0),
                                'qty_to_receive' => $qtyToReceive,
                                'balance_qty' => $balanceQty,
                            ]);
                        }
                    }
                }

                $newData = $receipt->fresh(['items'])->toArray();
                if ($id) {
                    addLog('update', 'Production Receipt', 'production_receipts', $receipt->id, $oldData, $newData);
                }
                else {
                    addLog('create', 'Production Receipt', 'production_receipts', $receipt->id, null, $newData);
                }

                if ($newData['status'] == 'Posted') {
                    $this->createStockEntry($receipt, $request->store_location_id);
                    $this->updateActualConsumables($receipt);
                }
                else {
                    if (isset($oldData) && $oldData['status'] == 'Posted') {
                        $this->revertActualConsumables($receipt, $oldData);
                    }
                }

                $capacityWarning = null;
                if ($receipt->warehouse_id && $receipt->job_card_id) {
                    $jobCard = JobCardEntry::find($receipt->job_card_id);
                    if ($jobCard && $jobCard->brand_id) {
                        $capObj = \App\Models\WarehouseBrandCapacity::where('warehouse_id', $receipt->warehouse_id)
                            ->where('brand_id', $jobCard->brand_id)
                            ->where('status', 'Active')
                            ->first();
                        if ($capObj && $capObj->capacity_pcs > 0) {
                            $currentStock = (float) \App\Models\StockEntryItem::where('brand_id', $jobCard->brand_id)
                                ->where(function($q) use ($receipt) {
                                    $q->where('warehouse_id', $receipt->warehouse_id)
                                      ->orWhereHas('stockEntry', function($se) use ($receipt) {
                                          $se->where('warehouse_id', $receipt->warehouse_id);
                                      });
                                })
                                ->sum(DB::raw('qty_in - qty_out'));

                            $utilPct = round(($currentStock / $capObj->capacity_pcs) * 100, 1);
                            if ($utilPct > 100) {
                                $whName = $receipt->warehouse?->warehouse_name ?? 'Warehouse';
                                $brandName = $jobCard->brand?->brand_name ?? 'Brand';
                                $capacityWarning = "Notice: {$whName} capacity for {$brandName} is currently at {$utilPct}% ({$currentStock} / {$capObj->capacity_pcs} pcs).";
                            }
                        }
                    }
                }

                DB::commit();
                return redirect('production_receipts')->with('success', 'Production receipt saved successfully.');
            }
            catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
            }
        }

        $warehouses = \App\Models\Warehouse::where('status', 'Active')->orderBy('warehouse_name')->get();

        return view('production_receipts.add', compact('receipt', 'jobCards', 'storeTypes', 'storeLocations', 'employees', 'warehouses'));
    }

    private function resolveReceiptItemArtNo($jobCard, array $itemData): ?string
    {
        $existingArtNo = trim((string) ($itemData['art_no'] ?? ''));
        if ($existingArtNo !== '') {
            return $existingArtNo;
        }

        if (!$jobCard) {
            return null;
        }

        $size = trim((string) ($itemData['size'] ?? ''));
        $sizeVariant = trim((string) ($itemData['size_variant'] ?? ''));
        $colorId = $itemData['color_id'] ?? null;
        $sleeveCode = null;

        if ($sizeVariant !== '') {
            $parts = array_map('trim', explode(' - ', $sizeVariant));
            if ($size === '' && isset($parts[0])) {
                $size = $parts[0];
            }
            if (isset($parts[1])) {
                $sleeveCode = strtoupper($parts[1]);
            }
        }

        $matches = collect();

        foreach ($jobCard->fabricDetails as $fabricDetail) {
            foreach ($fabricDetail->quantities as $quantity) {
                if ($size !== '' && trim((string) $quantity->size) !== $size) {
                    continue;
                }

                if (!is_null($colorId) && !is_null($quantity->color_id) && (string) $quantity->color_id !== (string) $colorId) {
                    continue;
                }

                if ($sleeveCode === 'F/S' && floatval($quantity->qty_fs) <= 0) {
                    continue;
                }

                if ($sleeveCode === 'H/S' && floatval($quantity->qty_hs) <= 0) {
                    continue;
                }

                $matches->push(trim((string) $fabricDetail->art_no));
            }
        }

        $matches = $matches->filter()->unique()->values();

        if ($matches->count() === 1) {
            return $matches->first();
        }

        if ($jobCard->fabricDetails->count() === 1) {
            return trim((string) $jobCard->fabricDetails->first()->art_no) ?: null;
        }

        return null;
    }

    private function resolveItemStyleId($jobCard, array $itemData): ?int
    {
        $artNo = trim((string) ($itemData['art_no'] ?? ''));
        $size = trim((string) ($itemData['size'] ?? ''));
        $sizeVariant = trim((string) ($itemData['size_variant'] ?? ''));

        $sleeveTypeShort = null;
        if (str_contains($sizeVariant, ' - F/S')) {
            $sleeveTypeShort = 'F/S';
        } elseif (str_contains($sizeVariant, ' - H/S')) {
            $sleeveTypeShort = 'H/S';
        }

        if ($artNo !== '') {
            $bm = \App\Models\BarcodeMaster::where('art_no', $artNo)
                ->when($size !== '', function ($q) use ($size) { $q->where('size', $size); })
                ->when($sleeveTypeShort, function ($q) use ($sleeveTypeShort) { $q->where('sleeve_type', $sleeveTypeShort); })
                ->whereNotNull('style_id')
                ->first();
            if ($bm && !empty($bm->style_id)) {
                return (int) $bm->style_id;
            }

            $bm2 = \App\Models\BarcodeMaster::where('art_no', $artNo)
                ->whereNotNull('style_id')
                ->first();
            if ($bm2 && !empty($bm2->style_id)) {
                return (int) $bm2->style_id;
            }
        }

        if ($jobCard) {
            $fallbackBm = \App\Models\BarcodeMaster::where('job_card_entry_id', $jobCard->id)
                ->whereNotNull('style_id')
                ->first();
            if ($fallbackBm && !empty($fallbackBm->style_id)) {
                return (int) $fallbackBm->style_id;
            }
            if ($jobCard->item && !empty($jobCard->item->style_id)) {
                return (int) $jobCard->item->style_id;
            }
        }

        if (!empty($itemData['item_id'])) {
            $it = \App\Models\Item::find($itemData['item_id']);
            if ($it && !empty($it->style_id)) {
                return (int) $it->style_id;
            }
        }

        return null;
    }

    private function validateWarehouseBrandStyleCapacity($request, $receiptId = null): ?string
    {
        $warehouseId = $request->warehouse_id;
        $jobCardId = $request->job_card_id;
        $status = $request->status ?? 'Draft';

        if (!$warehouseId || !$jobCardId) {
            return null;
        }

        $jobCard = JobCardEntry::with(['brand', 'item', 'fabricDetails.quantities'])->find($jobCardId);
        if (!$jobCard || !$jobCard->brand_id) {
            return null;
        }

        $brandId = $jobCard->brand_id;
        $brandName = $jobCard->brand?->brand_name ?? 'this Brand';
        $warehouse = Warehouse::find($warehouseId);
        $whName = $warehouse ? $warehouse->warehouse_name : 'Selected Warehouse';

        // Group incoming scan quantities by style_id
        $styleIncoming = [];
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $itemData) {
                $qty = floatval($itemData['scan_qty'] ?? 0);
                if ($qty <= 0) {
                    continue;
                }

                $resolvedArtNo = $this->resolveReceiptItemArtNo($jobCard, $itemData);
                if ($resolvedArtNo) {
                    $itemData['art_no'] = $resolvedArtNo;
                }

                $styleId = $this->resolveItemStyleId($jobCard, $itemData);
                $styleKey = $styleId ?: 0;

                if (!isset($styleIncoming[$styleKey])) {
                    $styleIncoming[$styleKey] = [
                        'style_id' => $styleId,
                        'qty' => 0
                    ];
                }
                $styleIncoming[$styleKey]['qty'] += $qty;
            }
        }

        if (empty($styleIncoming)) {
            return null;
        }

        foreach ($styleIncoming as $entry) {
            $sId = $entry['style_id'];
            $incomingQty = $entry['qty'];

            $styleName = 'General / All Styles';
            if ($sId) {
                $styleObj = Style::find($sId);
                $styleName = $styleObj ? $styleObj->style_name : "Style #{$sId}";
            }

            // Check configured capacity in warehouse_brand_capacities
            $capObj = WarehouseBrandCapacity::where('warehouse_id', $warehouseId)
                ->where('brand_id', $brandId)
                ->where('status', 'Active')
                ->when($sId, function ($q) use ($sId) {
                    $q->where(function ($sub) use ($sId) {
                        $sub->where('style_id', $sId)
                            ->orWhereNull('style_id');
                    });
                }, function ($q) {
                    $q->whereNull('style_id');
                })
                ->orderByRaw('style_id IS NOT NULL DESC')
                ->first();

            // Check 1: Existence of capacity configuration
            if (!$capObj || $capObj->capacity_pcs <= 0) {
                return "Warehouse {$whName} has no storage capacity configured for Brand: {$brandName} - Style: {$styleName}. Please configure this capacity in Master > Warehouse.";
            }

            // Check 2: Numerical capacity limit check on 'Posted'
            if ($status === 'Posted') {
                $capPcs = (float) $capObj->capacity_pcs;

                // Current stock for this brand & style in this warehouse
                $currentStockQuery = StockEntryItem::where('brand_id', $brandId)
                    ->when($sId, function ($q) use ($sId) {
                        $q->where('style_id', $sId);
                    })
                    ->where(function ($q) use ($warehouseId) {
                        $q->where('warehouse_id', $warehouseId)
                          ->orWhereHas('stockEntry', function ($se) use ($warehouseId) {
                              $se->where('warehouse_id', $warehouseId);
                          });
                    });

                // If updating an existing receipt, exclude items already in stock from this receipt
                if ($receiptId) {
                    $currentReceipt = ProductionReceipt::find($receiptId);
                    if ($currentReceipt && $currentReceipt->receipt_no) {
                        $currentStockQuery->whereDoesntHave('stockEntry', function ($se) use ($currentReceipt) {
                            $se->where('reference_document', $currentReceipt->receipt_no);
                        });
                    }
                }

                $currentStock = (float) $currentStockQuery->sum(DB::raw('qty_in - qty_out'));
                $availablePcs = max(0, $capPcs - $currentStock);

                if (($currentStock + $incomingQty) > $capPcs) {
                    $exceededBy = ($currentStock + $incomingQty) - $capPcs;
                    return "Capacity limit exceeded for {$brandName} - Style: {$styleName} in {$whName}! " .
                           "Configured Capacity: " . number_format($capPcs) . " Pcs, " .
                           "Current Stock: " . number_format($currentStock) . " Pcs, " .
                           "Available Capacity: " . number_format($availablePcs) . " Pcs, " .
                           "Incoming: " . number_format($incomingQty) . " Pcs (Exceeds by " . number_format($exceededBy) . " Pcs).";
                }
            }
        }

        return null;
    }

    private function createStockEntry($receipt, $storeLocationId)
    {
        $stockEntry = StockEntry::where('reference_document', $receipt->receipt_no)->where('entry_type', 'Finished Goods')->first();

        if ($stockEntry) {
            $hasIssuedStock = StockEntryItem::where('stock_entry_id', $stockEntry->id)->where('qty_out', '>', 0)->exists();

            if ($hasIssuedStock) {
                throw new \Exception('Cannot update Stock Entry because some items have already been issued/sold from this stock.');
            }

            $stockEntry->update([
                'stock_date' => $receipt->receipt_date,
                'to_store_location_id' => $storeLocationId,
                'warehouse_id' => $receipt->warehouse_id ?? null,
                'remarks' => $receipt->remarks,
                'price' => $receipt->items->sum('total_value'),
                'updated_by' => Auth::id(),
            ]);

            StockEntryItem::where('stock_entry_id', $stockEntry->id)->forceDelete();
        } else {
            $lastEntry = StockEntry::where('stock_entry_no', 'REGEXP', '^SE[0-9]+$')
                                   ->orderByRaw('CAST(SUBSTRING(stock_entry_no, 3) AS UNSIGNED) DESC')
                                   ->first();
            $nextNumber = $lastEntry ? (int)substr($lastEntry->stock_entry_no, 2) + 1 : 1;
            $stockEntryNo = 'SE' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            $stockEntry = StockEntry::create([
                'stock_entry_no' => $stockEntryNo,
                'stock_date' => $receipt->receipt_date,
                'entry_type' => 'Finished Goods',
                'to_store_location_id' => $storeLocationId,
                'warehouse_id' => $receipt->warehouse_id ?? null,
                'remarks' => $receipt->remarks,
                'reference_document' => $receipt->receipt_no,
                'status' => 'Posted',
                'created_by' => Auth::id(),
                'price' => $receipt->items->sum('total_value'),
            ]);
        }

        $fitId = null;
        $brandId = null;
        if ($receipt->job_card_id) {
            $jobCard = JobCardEntry::find($receipt->job_card_id);
            if ($jobCard) {
                $fitId = $jobCard->fit_id;
                $brandId = $jobCard->brand_id;
            }
            if (!$brandId) {
                throw new \Exception('Brand is required in the Job Card to post the Production Receipt.');
            }
        }

        foreach ($receipt->items as $item) {
            if ($item->qty_to_receive > 0) {
                $itemCode = $item->item_code;
                $sleeve = 'Full';
                if (str_contains($item->size_variant, ' - ')) {
                    $parts = explode(' - ', $item->size_variant);
                    $sleevePart = trim($parts[1]);
                    $sleeve = ($sleevePart == 'F/S') ? 'Full' : (($sleevePart == 'H/S') ? 'Half' : $sleevePart);
                }

                $sleeveTypeShort = ($sleeve == 'Full') ? 'F/S' : (($sleeve == 'Half') ? 'H/S' : $sleeve);
                $barcodeMaster = \App\Models\BarcodeMaster::where('art_no', $item->art_no)->where('size', $item->size)->where('sleeve_type', $sleeveTypeShort)->first();

                if ($barcodeMaster) {
                    $sku = $barcodeMaster->barcode_no;
                    $itemCode = str_replace('/', '', $barcodeMaster->item_code ?: $itemCode);
                    $barcodeMasterId = $barcodeMaster->id;
                } else {
                    $formattedSize = str_pad(trim((string)$item->size), 2, '0', STR_PAD_LEFT);
                    $sleeveCode = '00';
                    if (isset($sleevePart)) {
                        if ($sleevePart == 'F/S') $sleeveCode = '01';
                        elseif ($sleevePart == 'H/S') $sleeveCode = '02';
                    } else {
                        if ($sleeve == 'Full') $sleeveCode = '01';
                        elseif ($sleeve == 'Half') $sleeveCode = '02';
                    }

                    $hasAlpha = preg_match('/[a-zA-Z]/', $item->art_no);
                    $matchesExistingPattern = preg_match('/^([a-zA-Z]*)(\d+)(?:-(\d+))?$/', $item->art_no);
                    
                    if ($hasAlpha && !$matchesExistingPattern) {
                        $cleanedArtNo = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $item->art_no));
                        $cleanSize = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $item->size));
                        $sku = 'BC' . $cleanedArtNo . $cleanSize . $sleeveCode;
                    } else {
                        preg_match('/([a-zA-Z]*)(\d+)(?:-(\d+))?/', $item->art_no, $matches);
                        $numericBase = $matches[2] ?? '';
                        $suffix = $matches[3] ?? '1';
                        $formattedSuffix = str_pad($suffix, 2, '0', STR_PAD_LEFT);
                        $sku = 'BC' . $numericBase . $formattedSuffix . $formattedSize . $sleeveCode;
                    }
                    $barcodeMasterId = null;
                }

                $styleId = null;
                if ($barcodeMaster && !empty($barcodeMaster->style_id)) {
                    $styleId = $barcodeMaster->style_id;
                } else {
                    $fallbackBm = \App\Models\BarcodeMaster::where('job_card_entry_id', $receipt->job_card_id)->whereNotNull('style_id')->first();
                    if ($fallbackBm) {
                        $styleId = $fallbackBm->style_id;
                    } else {
                        $fallbackBm2 = \App\Models\BarcodeMaster::where('art_no', $item->art_no)->whereNotNull('style_id')->first();
                        if ($fallbackBm2) {
                            $styleId = $fallbackBm2->style_id;
                        }
                    }
                }

                $itemModel = Item::with('fabricType')->find($item->item_id);

                $fabric = 'Polyester';
                if ($itemModel && $itemModel->fabricType) {
                    $fabric = $itemModel->fabricType->fabric_type;
                }

                $design = $itemModel ? $itemModel->design_art_no : $item->art_no;
                $mrp = $itemModel ? $itemModel->mrp : $item->unit_price;

                $colorName = '-';
                if ($item->color_id) {
                    $c = \App\Models\Color::find($item->color_id);
                    $colorName = $c ? $c->color_name : '-';
                }

                $qrData = [
                    'sku' => $sku,
                    'design' => $design,
                    'fabric' => $fabric,
                    'size' => $item->size,
                    'color' => $colorName,
                    'sleeve' => $sleeve,
                    'price' => number_format($mrp, 2),
                    'quantity' => number_format($item->qty_to_receive, 2)
                ];

                StockEntryItem::create([
                    'stock_entry_id' => $stockEntry->id,
                    'stock_type' => 'finished_goods',
                    'finished_item_code' => $itemCode,
                    'item_id' => $item->item_id,
                    'art_no' => $item->art_no,
                    'size' => $item->size,
                    'color_id' => $item->color_id,
                    'store_location_id' => $storeLocationId,
                    'warehouse_id' => $receipt->warehouse_id ?? null,
                    'uom_id' => $item->uom_id,
                    'qty_in' => $item->qty_to_receive,
                    'qty_out' => 0,
                    'price' => $item->unit_price,
                    'sku' => $sku,
                    'qrcode' => json_encode($qrData),
                    'sleeve_type' => $sleeve,
                    'fit_id' => $fitId,
                    'brand_id' => $brandId,
                    'style_id' => $styleId,
                    'barcode_master_id' => $barcodeMasterId ?? null,
                ]);
            }
        }

        return $stockEntry;
    }

    private function getFullyReceivedJobCardIds($excludeReceiptId = null)
    {
        $usedJobCardIds = ProductionReceipt::whereNotNull('job_card_id')
            ->when($excludeReceiptId, function ($q) use ($excludeReceiptId) {
                return $q->where('id', '!=', $excludeReceiptId);
            })
            ->pluck('job_card_id')
            ->unique()
            ->filter()
            ->toArray();

        if (empty($usedJobCardIds)) {
            return [];
        }

        $jobCards = JobCardEntry::whereIn('id', $usedJobCardIds)->get();
        $fullyReceivedIds = [];

        foreach ($jobCards as $jc) {
            $totalOrdered = \DB::table('job_card_matrix_quantities as jmq')
                ->join('job_card_fabric_details as jfd', 'jmq.job_card_fabric_detail_id', '=', 'jfd.id')
                ->where('jfd.job_card_entry_id', $jc->id)
                ->sum(\DB::raw('jmq.qty_fs + jmq.qty_hs'));

            if ($totalOrdered <= 0) {
                $totalOrdered = ($jc->total_qty_fs ?? 0) + ($jc->total_qty_hs ?? 0);
                if ($totalOrdered <= 0) {
                    $totalOrdered = $jc->grand_total_qty ?? 0;
                }
            }

            if ($totalOrdered <= 0) {
                continue;
            }

            $totalReceived = \DB::table('production_receipt_items as pri')
                ->join('production_receipts as pr', 'pri.production_receipt_id', '=', 'pr.id')
                ->where('pr.job_card_id', $jc->id)
                ->when($excludeReceiptId, function ($q) use ($excludeReceiptId) {
                    return $q->where('pr.id', '!=', $excludeReceiptId);
                })
                ->sum('pri.qty_to_receive');

            if ($totalReceived >= $totalOrdered) {
                $fullyReceivedIds[] = $jc->id;
            }
        }

        return $fullyReceivedIds;
    }

    public function getJobCardDetails(Request $request, $id)
    {
        $jobCard = JobCardEntry::with([
            'serviceProvider',
            'brand',
            'purchaseOrder.supplier',
            'purchaseOrder.items.style',
            'item',
            'item.uom',
            'item.brand',
            'item.style',
            'fabricDetails',
            'sleeveMeters',
            'cuttingSizeRatios',
            'processGroup'
        ])->find($id);

        if (!$jobCard) {
            return response()->json(['success' => false, 'message' => 'Job Card not found'], 404);
        }

        $issueItemsCount = \App\Models\JobCardIssueItem::where('job_card_entry_id', $jobCard->id)->count();
        if ($issueItemsCount == 0) {
            return response()->json([
                'success' => false,
                'message' => 'The selected Job Card has not been issued yet. Please issue the Job Card before creating a Production Receipt.'
            ]);
        }

        $getStoreCategoryIdForArtNo = function ($artNo) {
            $normalizedArtNo = trim($artNo ?? '');
            if ($normalizedArtNo === '') {
                return null;
            }

            $stockItem = \App\Models\StockEntryItem::where('art_no', $normalizedArtNo)->orderByDesc('id')->first();
            if ($stockItem && $stockItem->store_category_id) {
                return $stockItem->store_category_id;
            }

            $row = \DB::table('grn_entry_items')->join('purchase_invoice_items', 'grn_entry_items.purchase_invoice_item_id', '=', 'purchase_invoice_items.id')->join('raw_materials', 'purchase_invoice_items.raw_material_id', '=', 'raw_materials.id')->where('grn_entry_items.art_no', $normalizedArtNo)->orderByDesc('grn_entry_items.id')->select('raw_materials.store_category_id')->first();

            if ($row && $row->store_category_id) {
                return $row->store_category_id;
            }

            $rawMaterial = \App\Models\RawMaterial::where('code', $normalizedArtNo)->orWhere('name', $normalizedArtNo)->first();
            if ($rawMaterial) {
                return $rawMaterial->store_category_id;
            }

            return null;
        };


        $isCanvas = false;
        if ($jobCard->brand && in_array(strtoupper(trim($jobCard->brand->brand_name)), ['CANVAS ACCESSORIES', 'CANVAS ACCESSORIES (CAS)'])) {
            $isCanvas = true;
        }

        $allMaterials = $jobCard->fabricDetails->values();
        $fabricDetails = $allMaterials->filter(function ($fd) use ($getStoreCategoryIdForArtNo, $isCanvas) {
            $cat = $getStoreCategoryIdForArtNo($fd->art_no);
            $hasMatrix = \App\Models\JobCardMatrixQuantity::where('job_card_fabric_detail_id', $fd->id)->exists();
            return $isCanvas || $cat === null || (int)$cat === 1 || $hasMatrix;
        })->values();
        
        $articlePrices = [];
        foreach ($allMaterials as $fabricDetail) {
            $artNo = trim($fabricDetail->art_no);
            $avgPrice = 0;

            $stockItems = \DB::table('stock_entry_items')->where('art_no', $artNo)->whereNull('deleted_at')->whereRaw('(qty_in - qty_out) > 0')->select('price', \DB::raw('(qty_in - qty_out) as available_qty'))->get();

            if ($stockItems->count() == 0) {
                $stockItems = \DB::table('stock_entry_items')->where('art_no', $artNo)->whereNull('deleted_at')->where('qty_in', '>', 0)->select('price', \DB::raw('qty_in as available_qty'))->get();
            }

            if ($stockItems->count() == 0) {
                $rawMaterial = \App\Models\RawMaterial::where('name', $artNo)->orWhere('code', $artNo)->first();
                if ($rawMaterial) {
                    $stockItems = \DB::table('stock_entry_items')->where('raw_material_id', $rawMaterial->id)->whereNull('deleted_at')->whereRaw('(qty_in - qty_out) > 0')->select('price', \DB::raw('(qty_in - qty_out) as available_qty'))->get();
                }
            }

            if ($stockItems->count() == 0 && isset($rawMaterial)) {
                $stockItems = \DB::table('stock_entry_items')->where('raw_material_id', $rawMaterial->id)->whereNull('deleted_at')->where('qty_in', '>', 0)->select('price', \DB::raw('qty_in as available_qty'))->get();
            }

            if ($stockItems->count() > 0) {
                $totalVal = 0;
                $totalQty = 0;
                foreach ($stockItems as $si) {
                    $totalVal += ($si->available_qty * $si->price);
                    $totalQty += $si->available_qty;
                }
                if ($totalQty > 0) {
                    $avgPrice = $totalVal / $totalQty;
                }
            }

            if ($avgPrice <= 0) {
                $latestGrnItem = \DB::table('grn_entry_items')->where('art_no', $artNo)->orderBy('id', 'desc')->first();
                if ($latestGrnItem) {
                    $avgPrice = $latestGrnItem->rate;
                }
            }

            if ($avgPrice <= 0) {
                $rawMaterial = \App\Models\RawMaterial::where('name', $artNo)->orWhere('code', $artNo)->first();
                if ($rawMaterial) {
                    $avgPrice = $rawMaterial->purchase_price ?: $rawMaterial->latest_price ?: 0;
                }
            }
            $articlePrices[$fabricDetail->id] = $avgPrice;
        }

        $excludeReceiptId = $request->input('exclude_receipt_id');
        $existingReceiptIds = ProductionReceipt::where('job_card_id', $id)
            ->when($excludeReceiptId, function ($q) use ($excludeReceiptId) {
            return $q->where('id', '!=', $excludeReceiptId);
        })->pluck('id');
        
        $existingReceiptsItems = ProductionReceiptItem::whereIn('production_receipt_id', $existingReceiptIds)
            ->get()
            ->groupBy(function ($item) {
            return ($item->item_id ?? '0') . '|' . trim($item->art_no ?? '') . '|' . ($item->size_variant ?? '') . '|' . ($item->color_id ?? $item->color ?? '');
        })->map(function ($group) {
            return $group->sum('qty_to_receive');
        });

        $allPOItems = $jobCard->purchaseOrder?->items;
        if (!$allPOItems) {
            $firstArtNo = $fabricDetails->first()?->art_no;
            if ($firstArtNo) {
                $grnItem = \App\Models\GrnEntryItem::where('art_no', $firstArtNo)->whereHas('purchaseInvoiceItem.purchaseOrderItem')->first();
                $allPOItems = $grnItem?->purchaseInvoiceItem?->purchaseOrderItem?->purchaseOrder?->items ?? null;
            }
        }
        
        $fallbackStyleCode = '';
        $fallbackStyleName = '';
        if ($allPOItems) {
            $fabricPoItem = $allPOItems->where('store_category_id', 1)->whereNotNull('style_id')->first() ?: $allPOItems->whereNotNull('style_id')->first();
            $fallbackStyleCode = $fabricPoItem && $fabricPoItem->style ? $fabricPoItem->style->code : '';
            $fallbackStyleName = $fabricPoItem && $fabricPoItem->style ? $fabricPoItem->style->style_name : '';
        }

        $items = [];
        $tempGrouped = [];
        $serviceName = $jobCard->processGroup ? $jobCard->processGroup->name : 'Production Service';
        $artColorMap = [];

        foreach ($fabricDetails as $fabDetail) {
            $normalizedArtNo = trim($fabDetail->art_no ?? '');
            if ($normalizedArtNo === '' || isset($artColorMap[$normalizedArtNo])) {
                continue;
            }

            $grnColor = DB::table('grn_entry_items')
                ->leftJoin('colors', 'grn_entry_items.color_id', '=', 'colors.id')
                ->where('grn_entry_items.art_no', $normalizedArtNo)
                ->whereNotNull('grn_entry_items.color_id')
                ->orderByDesc('grn_entry_items.id')
                ->select('grn_entry_items.color_id', 'colors.color_name')
                ->first();

            if (!$grnColor) {
                $grnColor = DB::table('grn_entry_items')
                    ->join('purchase_invoice_items', 'grn_entry_items.purchase_invoice_item_id', '=', 'purchase_invoice_items.id')
                    ->join('purchase_order_items', 'purchase_invoice_items.purchase_order_item_id', '=', 'purchase_order_items.id')
                    ->leftJoin('colors', 'purchase_order_items.color_id', '=', 'colors.id')
                    ->where('grn_entry_items.art_no', $normalizedArtNo)
                    ->whereNotNull('purchase_order_items.color_id')
                    ->orderByDesc('grn_entry_items.id')
                    ->select('purchase_order_items.color_id', 'colors.color_name')
                    ->first();
            }
 
            if ($grnColor) {
                $artColorMap[$normalizedArtNo] = [
                    'color_id' => $grnColor->color_id,
                    'color' => $grnColor->color_name,
                ];
            }
        }

        $calculateItemUnitPrice = function ($artNo, $size, $sleeve) use ($allMaterials, $articlePrices, $getStoreCategoryIdForArtNo, $jobCard, $isCanvas) {
            $totalCost = 0;
            $consumptionDetails = [];
            $normalizedArtNo = trim($artNo ?? '');

            foreach ($allMaterials as $fd) {
                $fdCategoryId = $getStoreCategoryIdForArtNo($fd->art_no);
                
                if ($isCanvas) {
                    if ($normalizedArtNo !== '' && trim($fd->art_no ?? '') !== $normalizedArtNo) {
                        continue;
                    }
                } else {
                    if (($fdCategoryId === null || (int)$fdCategoryId === 1) && $normalizedArtNo !== '' && trim($fd->art_no ?? '') !== $normalizedArtNo) {
                        continue;
                    }
                }
                $rate = 0;
                $avgPrice = $articlePrices[$fd->id] ?? 0;

                $consumption = \App\Models\JobCardFabricConsumption::where('job_card_fabric_detail_id', $fd->id)->where('size', $size)->first();
                $formula = '';
                $source = 'Manual Entry';
                $layMarks = $fd->layMarks; 
                $marksForThisSize = [];
                $consInMarks = [];

                if ($layMarks && $layMarks->count() > 0) {
                    foreach ($layMarks as $lm) {
                        $lmSizes = is_array($lm->sizes) ? $lm->sizes : json_decode($lm->sizes, true);
                        $lmSleeve = ($lm->sleeve_type == 'H/S') ? 'H/S' : 'F/S';
                        
                        if ($lmSizes && in_array($size, $lmSizes) && $lmSleeve == $sleeve) {
                            $n = count($lmSizes);
                            if ($n > 0) {
                                $ci = floatval($lm->lay_mark_meter) / $n;
                                $marksForThisSize[] = $lm->lay_mark_meter . "m/" . $n;
                                $consInMarks[] = $ci;
                            }
                        }
                    }
                }

                if (!empty($consInMarks)) {
                    $rate = array_sum($consInMarks) / count($consInMarks);
                    $formula = (count($consInMarks) > 1) ? "(" . implode(" + ", $marksForThisSize) . ") / " . count($consInMarks) : $marksForThisSize[0];
                    $source = "Lay Marks";
                } elseif ($consumption && (($sleeve == 'F/S' && $consumption->fs_cons > 0) || ($sleeve == 'H/S' && $consumption->hs_cons > 0))) {
                    $rate = ($sleeve == 'F/S') ? $consumption->fs_cons : $consumption->hs_cons;
                    $formula = $rate . "m";
                }
                elseif ($fd->fs_qty > 0 || $fd->hs_qty > 0) {
                    $rate = ($sleeve == 'F/S') ? $fd->fs_qty : $fd->hs_qty;
                }
                else {
                    $globalSleeve = $jobCard->sleeveMeters->where('sleeve_type', ($sleeve == 'F/S' ? 'Full Sleeve' : 'Half Sleeve'))->first();
                    $rate = $globalSleeve ? $globalSleeve->meter : 0;
                }

                $rawMaterial = null;
                $stockItem = \DB::table('stock_entry_items')->where('art_no', $fd->art_no)->select('raw_material_id')->first();

                if ($stockItem) {
                    $rawMaterial = \App\Models\RawMaterial::with(['storeCategory', 'uom'])->find($stockItem->raw_material_id);
                }
                else {
                    $rawMaterial = \App\Models\RawMaterial::with(['storeCategory', 'uom'])->where('code', $fd->art_no)->orWhere('name', $fd->art_no)->first();
                }

                $materialName = '';
                if ($rawMaterial) {
                    $materialName = ($rawMaterial->storeCategory ? $rawMaterial->storeCategory->category_name . ' - ' : '') . $rawMaterial->name;
                }
                else {
                    $materialName = $fd->art_no;
                }
                $uomName = $rawMaterial && $rawMaterial->uom ? $rawMaterial->uom->uom_code : 'MTR';

                $cost = ($rate * $avgPrice);
                $totalCost += $cost;
                $consumptionDetails[] = [
                    'art_no' => $fd->art_no,
                    'material_name' => $materialName,
                    'uom' => $uomName,
                    'rate' => floatval($rate),
                    'price' => floatval($avgPrice),
                    'total_cost' => floatval($cost),
                    'formula' => $formula,
                    'source' => $source
                ];
            }

            return [
            'total_cost' => $totalCost,
            'consumption_details' => $consumptionDetails
            ];
        };
        $missingItemPrice = false;
        
        $processQty = function ($artNo, $sleeve, $size, $qty, $color = null, $colorId = null) use (&$tempGrouped, $jobCard, $serviceName, $calculateItemUnitPrice, $fallbackStyleCode, $fallbackStyleName, $artColorMap, $isCanvas, &$missingItemPrice) {
            if ($qty > 0) {
                $sizeVariant = $sleeve ? $size . ' - ' . $sleeve : $size;
                $itemKey = $jobCard->item_id ?? '0';
                $normalizedArtNo = trim($artNo ?? '');
                $resolvedColorId = $artColorMap[$normalizedArtNo]['color_id'] ?? $colorId;
                $resolvedColor = $artColorMap[$normalizedArtNo]['color'] ?? $color;
                $key = $itemKey . '|' . $normalizedArtNo . '|' . $sizeVariant . '|' . ($resolvedColorId ?? $resolvedColor ?? '');

                if (!isset($tempGrouped[$key])) {
                    $pricing = $calculateItemUnitPrice($normalizedArtNo, $size, $sleeve);   
                    $brandCode = $jobCard->brand ? $jobCard->brand->code : '';
                    $brandName = $jobCard->brand ? $jobCard->brand->brand_name : '';
                    $stockStyle = \DB::table('stock_entry_items')
                        ->join('styles', 'stock_entry_items.style_id', '=', 'styles.id')
                        ->where('stock_entry_items.art_no', $normalizedArtNo)
                        ->select(
                            'styles.code as style_code',
                            'styles.style_name'
                        )
                        ->first();
                    $barcodeMaster = \App\Models\BarcodeMaster::where('job_card_entry_id', $jobCard->id)->where('size', $size)->where('sleeve_type', $sleeve)->first();
                    if (!$barcodeMaster) {
                        $barcodeMaster = \App\Models\BarcodeMaster::where('job_card_entry_id', $jobCard->id)->where('sleeve_type', $sleeve)->first();
                    }
                    if (!$barcodeMaster) {
                        $barcodeMaster = \App\Models\BarcodeMaster::where('job_card_entry_id', $jobCard->id)->first();
                    }

                    $styleCode = $fallbackStyleCode;
                    $styleName = $fallbackStyleName;
                    
                    if (!$styleCode && isset($stockStyle) && $stockStyle) {
                        $styleCode = $stockStyle->style_code;
                        $styleName = $stockStyle->style_name;
                    }

                    if ($barcodeMaster && $barcodeMaster->style_id) {
                        $style = \App\Models\Style::find($barcodeMaster->style_id);
                        if ($style) {
                            $styleCode = $style->code;
                            $styleName = $style->style_name;
                        }
                    }
                    $sleeveCode = str_replace('/', '', $sleeve);
                    $fallbackCode = implode('-', array_filter([trim($brandCode), trim($styleCode), $sleeveCode], function($v) { return $v !== ''; }));
                    $itemCode = str_replace('/', '', $barcodeMaster && $barcodeMaster->item_code ? $barcodeMaster->item_code : $fallbackCode);
                    $itemName = $brandName . ' ' . $styleName . ' ' . $sleeve;

                    if ($isCanvas) {
                        $itemCode = $normalizedArtNo;
                        $itemName = count($pricing['consumption_details']) > 0 ? $pricing['consumption_details'][0]['material_name'] : $normalizedArtNo;
                    }

                    $itemPrice = \App\Models\ItemPrice::where('status', 'Active')
                        ->where('finished_item_code', $itemCode)
                        ->where('art_no', $normalizedArtNo)
                        ->where(function($q) use ($size) {
                            $q->where('size', $size)->orWhereNull('size')->orWhere('size', '');
                        })
                        ->whereDate('effective_from', '<=', now())
                        ->orderBy('effective_from', 'desc')
                        ->orderBy('id', 'desc')
                        ->first();

                    if (!$itemPrice) {
                        $missingItemPrice = true;
                    }

                    $unitPrice = $itemPrice ? $itemPrice->unit_price : $pricing['total_cost'];
                    $mrp = $itemPrice ? $itemPrice->selling_price : $pricing['total_cost'];

                    if ($isCanvas && !$itemPrice) {
                        $issueItem = \App\Models\JobCardIssueItem::where('job_card_entry_id', $jobCard->id)
                            ->whereHas('fabricDetail', function($q) use ($normalizedArtNo) {
                                $q->where('art_no', $normalizedArtNo);
                            })->first();
                        
                        if (!$issueItem) {
                            $issueItem = \App\Models\JobCardIssueItem::where('job_card_entry_id', $jobCard->id)
                                ->whereHas('rawMaterial', function($q) use ($normalizedArtNo) {
                                    $q->where('code', $normalizedArtNo)->orWhere('name', $normalizedArtNo);
                                })->first();
                        }
                        if ($issueItem) {
                            $qtyPerPc = $issueItem->produced_qty > 0 ? ($issueItem->qty_used / $issueItem->produced_qty) : 0;
                            $calculatedCost = $qtyPerPc > 0 ? ($qtyPerPc * $issueItem->unit_price) : 0;
                            
                            $cost = $calculatedCost > 0 ? $calculatedCost : ($issueItem->cost_per_pc > 0 ? $issueItem->cost_per_pc : $issueItem->unit_price);
                            if ($cost > 0) {
                                $unitPrice = $cost;
                                $mrp = $cost;
                            }
                        }
                    }

                    $tempGrouped[$key] = [
                        'item_id' => $jobCard->item_id ?? null,
                        'item_code' => $isCanvas ? $itemCode : ($barcodeMaster && $barcodeMaster->item_code ? str_replace('/', '', $barcodeMaster->item_code) : $itemCode),
                        'service_name' => $serviceName,
                        'sleeve' => $sleeve,
                        'size' => $size,
                        'item_name' => $itemName,
                        'art_no' => $normalizedArtNo ?: null,
                        'description' => $isCanvas ? $itemName : trim($barcodeMaster && $barcodeMaster->item_name ? $barcodeMaster->item_name : $itemName),
                        'size_variant' => $sizeVariant,
                        'unit_price' => floatval($unitPrice),
                        'mrp' => floatval($mrp),
                        'uom_id' => $jobCard->item ? $jobCard->item->uom_id : null,
                        'uom_code' => 'PCS',
                        'ordered_qty' => 0,
                        'completed_qty' => 0,
                        'color' => $resolvedColor,
                        'color_id' => $resolvedColorId,
                        'consumption_details' => $pricing['consumption_details'],
                    ];
                }
                $tempGrouped[$key]['ordered_qty'] += $qty;
                $tempGrouped[$key]['completed_qty'] += $qty;
            }
        };

        foreach ($fabricDetails as $fabDetail) {
            $matrixQtys = \App\Models\JobCardMatrixQuantity::with('colorRel')->where('job_card_fabric_detail_id', $fabDetail->id)->get();
            foreach ($matrixQtys as $mq) {
                $colorToUse = $mq->color ?: ($mq->colorRel ? $mq->colorRel->color_name : null);
                if ($isCanvas) {
                    $qty = ($mq->qty_fs > 0) ? $mq->qty_fs : $mq->qty_hs;
                    $processQty($fabDetail->art_no, '', $mq->size, $qty, $colorToUse, $mq->color_id);
                } else {
                    $processQty($fabDetail->art_no, 'F/S', $mq->size, $mq->qty_fs, $colorToUse, $mq->color_id);
                    $processQty($fabDetail->art_no, 'H/S', $mq->size, $mq->qty_hs, $colorToUse, $mq->color_id);
                }
            }
        }

        foreach ($tempGrouped as $key => $itemData) {
            $alreadyRec = $existingReceiptsItems->get($key) ?? 0;
            $balance = $itemData['completed_qty'] - $alreadyRec;

            $currentReceiptItemExistsQuery = ProductionReceiptItem::where('production_receipt_id', $excludeReceiptId)
                ->where('item_id', $itemData['item_id'])
                ->where('size_variant', $itemData['size_variant'])
                ->where('art_no', $itemData['art_no']);

            if (!is_null($itemData['color_id'])) {
                $currentReceiptItemExistsQuery->where('color_id', $itemData['color_id']);
            } else {
                $currentReceiptItemExistsQuery->whereNull('color_id');
            }

            if ($balance > 0 || ($excludeReceiptId && $currentReceiptItemExistsQuery->exists())) {
                $currentReceiptItemQuery = ProductionReceiptItem::where('production_receipt_id', $excludeReceiptId)
                    ->where('item_id', $itemData['item_id'])
                    ->where('size_variant', $itemData['size_variant'])
                    ->where('art_no', $itemData['art_no']);

                if (!is_null($itemData['color_id'])) {
                    $currentReceiptItemQuery->where('color_id', $itemData['color_id']);
                } else {
                    $currentReceiptItemQuery->whereNull('color_id');
                }

                $scanQty = 0;
                if ($excludeReceiptId) {
                    $currentReceiptItem = $currentReceiptItemQuery->first();
                    if ($currentReceiptItem) {
                        $scanQty = $currentReceiptItem->qty_to_receive;
                    }
                }

                $itemData['qty_already_received'] = $alreadyRec;
                $itemData['scan_qty'] = $scanQty;
                $itemData['damage_qty'] = 0;
                $itemData['qty_to_receive'] = $scanQty;
                $itemData['balance_qty'] = $balance;
                $itemData['total_value'] = $scanQty * $itemData['unit_price'];

                $items[] = $itemData;
            }
        }

        if ($missingItemPrice) {
            return response()->json([
                'success' => false,
                'message' => 'One or more items from the selected Job Card do not exist in the Item Price Master. Please configure the Item Price before creating the Production Receipt.'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'job_card_no' => $jobCard->job_card_no,
                'employee_id' => null,
                'purchase_order_id' => $jobCard->purchase_order_id,
                'po_number' => $jobCard->purchaseOrder ? $jobCard->purchaseOrder->po_number : '',
                'plant_id' => $jobCard->service_provider_id,
                'plant_name' => $jobCard->serviceProvider ? $jobCard->serviceProvider->name : '',
                'order_due_date' => $jobCard->delivery_date ? date('d-m-Y', strtotime($jobCard->delivery_date)) : (($jobCard->purchaseOrder && $jobCard->purchaseOrder->due_date) ? date('d-m-Y', strtotime($jobCard->purchaseOrder->due_date)) : ''),
                'job_card_date' => $jobCard->job_card_date ? date('d-m-Y', strtotime($jobCard->job_card_date)) : '',
                'doc_no' => $jobCard->job_card_no,
                'items' => $items,
            ]
        ]);
    }

    public function getWarehouseCapacity(Request $request)
    {
        $warehouseId = $request->get('warehouse_id');
        $jobCardId = $request->get('job_card_id');

        if (!$warehouseId || !$jobCardId) {
            return response()->json(['has_capacity' => false]);
        }

        $jobCard = JobCardEntry::find($jobCardId);
        if (!$jobCard || !$jobCard->brand_id) {
            return response()->json(['has_capacity' => false]);
        }

        $brand = Brand::find($jobCard->brand_id);
        $warehouse = Warehouse::find($warehouseId);

        $capSum = WarehouseBrandCapacity::where('warehouse_id', $warehouseId)
            ->where('brand_id', $jobCard->brand_id)
            ->where('status', 'Active')
            ->sum('capacity_pcs');

        if ($capSum <= 0) {
            return response()->json([
                'has_capacity' => false,
                'warehouse_name' => $warehouse?->warehouse_name,
                'brand_name' => $brand?->brand_name,
                'message' => "{$warehouse?->warehouse_name} has NO storage capacity configured for {$brand?->brand_name}."
            ]);
        }

        $currentStock = (float) StockEntryItem::where('brand_id', $jobCard->brand_id)
            ->where(function($q) use ($warehouseId) {
                $q->where('warehouse_id', $warehouseId)
                  ->orWhereHas('stockEntry', function($se) use ($warehouseId) {
                      $se->where('warehouse_id', $warehouseId);
                  });
            })
            ->sum(DB::raw('qty_in - qty_out'));

        $utilPct = round(($currentStock / $capSum) * 100, 1);
        $isOverCapacity = $utilPct > 100;

        return response()->json([
            'has_capacity' => true,
            'warehouse_name' => $warehouse?->warehouse_name,
            'brand_name' => $brand?->brand_name,
            'capacity_pcs' => $capSum,
            'current_stock' => $currentStock,
            'utilization_pct' => $utilPct,
            'is_over_capacity' => $isOverCapacity,
            'message' => "{$warehouse?->warehouse_name} Capacity for {$brand?->brand_name}: {$currentStock} / {$capSum} Pcs ({$utilPct}%)"
        ]);
    }

    private function updateActualConsumables($receipt)
    {
        $receipt->load('items');
        foreach ($receipt->items as $item) {
            $qty = $item->qty_to_receive;
            if ($qty <= 0)
                continue;

            $sleeveType = 'All';
            if (str_contains($item->size_variant, ' - F/S')) {
                $sleeveType = 'F/S';
            }
            elseif (str_contains($item->size_variant, ' - H/S')) {
                $sleeveType = 'H/S';
            }

            $consumables = \App\Models\ProductionStageConsumable::where('job_card_id', $receipt->job_card_id)
                ->where(function ($q) use ($sleeveType) {
                $q->where('sleeve_type', $sleeveType)->orWhere('sleeve_type', 'All');
            })
                ->get();

            foreach ($consumables as $con) {
                $con->actual_qty += ($qty * $con->quantity_per_unit);
                $con->save();
            }
        }
    }

    private function revertActualConsumables($receipt, $oldData)
    {
        if (!isset($oldData['items']))
            return;

        foreach ($oldData['items'] as $item) {
            $qty = $item['qty_to_receive'] ?? 0;
            if ($qty <= 0)
                continue;

            $sleeveType = 'All';
            if (isset($item['size_variant'])) {
                if (str_contains($item['size_variant'], ' - F/S')) {
                    $sleeveType = 'F/S';
                }
                elseif (str_contains($item['size_variant'], ' - H/S')) {
                    $sleeveType = 'H/S';
                }
            }

            $consumables = \App\Models\ProductionStageConsumable::where('job_card_id', $receipt->job_card_id)
                ->where(function ($q) use ($sleeveType) {
                $q->where('sleeve_type', $sleeveType)->orWhere('sleeve_type', 'All');
            })
                ->get();

            foreach ($consumables as $con) {
                $con->actual_qty = max(0, $con->actual_qty - ($qty * $con->quantity_per_unit));
                $con->save();
            }
        }
    }

    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view_details production-receipts')) {
            return unauthorizedRedirect();
        }

        $receipt = ProductionReceipt::with([
            'jobCard.brand',
            'jobCard.fabricDetails.quantities',
            'jobCard.season',
            'jobCard.serviceProvider',
            'storeType',
            'storeLocation',
            'warehouse',
            'employee',
            'items.uom'
        ])->findOrFail($id);

        foreach ($receipt->items as $item) {
            $item->resolved_art_no = $this->resolveReceiptItemArtNo($receipt->jobCard, $item->toArray());
        }

        $capacityWarning = null;
        if ($receipt->warehouse_id && $receipt->jobCard && $receipt->jobCard->brand_id) {
            $capObj = \App\Models\WarehouseBrandCapacity::where('warehouse_id', $receipt->warehouse_id)
                ->where('brand_id', $receipt->jobCard->brand_id)
                ->where('status', 'Active')
                ->first();
            if ($capObj && $capObj->capacity_pcs > 0) {
                $currentStock = (float) \App\Models\StockEntryItem::where('brand_id', $receipt->jobCard->brand_id)
                    ->where(function($q) use ($receipt) {
                        $q->where('warehouse_id', $receipt->warehouse_id)
                          ->orWhereHas('stockEntry', function($se) use ($receipt) {
                              $se->where('warehouse_id', $receipt->warehouse_id);
                          });
                    })
                    ->sum(DB::raw('qty_in - qty_out'));

                $utilPct = round(($currentStock / $capObj->capacity_pcs) * 100, 1);
                if ($utilPct > 100) {
                    $whName = $receipt->warehouse?->warehouse_name ?? 'Warehouse';
                    $brandName = $receipt->jobCard->brand?->brand_name ?? 'Brand';
                    $capacityWarning = "{$whName} capacity for {$brandName} is currently at {$utilPct}% ({$currentStock} / {$capObj->capacity_pcs} pcs).";
                }
            }
        }
        
        return view('production_receipts.view_details', compact('receipt', 'capacityWarning'));
    }

    public function print($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view production-receipts')) {
            return unauthorizedRedirect();
        }

        $receipt = ProductionReceipt::with([
            'jobCard.brand',
            'jobCard.fabricDetails.quantities',
            'jobCard.season',
            'jobCard.serviceProvider',
            'storeType',
            'storeLocation',
            'warehouse',
            'employee',
            'items.uom'
        ])->findOrFail($id);

        foreach ($receipt->items as $item) {
            $item->resolved_art_no = $this->resolveReceiptItemArtNo($receipt->jobCard, $item->toArray());
        }

        $is_print = true;
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('production_receipts.pdf', compact('receipt', 'is_print'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('production_receipt.pdf');
    }

    public function downloadPdf($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view production-receipts')) {
            return unauthorizedRedirect();
        }

        $receipt = ProductionReceipt::with([
            'jobCard.brand',
            'jobCard.fabricDetails.quantities',
            'jobCard.season',
            'jobCard.serviceProvider',
            'storeType',
            'storeLocation',
            'warehouse',
            'employee',
            'items.uom'
        ])->findOrFail($id);

        foreach ($receipt->items as $item) {
            $item->resolved_art_no = $this->resolveReceiptItemArtNo($receipt->jobCard, $item->toArray());
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('production_receipts.pdf', compact('receipt'));
        $pdf->setPaper('A4', 'portrait');

        $filename = 'Production_Receipt_' . str_replace(['/', '\\'], '_', $receipt->receipt_no) . '.pdf';
        return $pdf->stream($filename);
    }

    public function exportExcel()
    {
        if (auth()->id() != 1 && !auth()->user()->can('view production-receipts')) {
            return unauthorizedRedirect();
        }
        return Excel::download(new ProductionReceiptExport, 'production_receipts_' . date('Ymd_His') . '.xlsx');
    }

    public function report(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view production-receipt-report')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $query = \App\Models\ProductionReceiptItem::with('productionReceipt')->orderBy('id', 'desc');

            if (!empty($request->date_range)) {
                $dates = explode(' to ', $request->date_range);
                $startDate = isset($dates[0]) ? \Carbon\Carbon::parse($dates[0])->format('Y-m-d') : null;
                $endDate = isset($dates[1]) ? \Carbon\Carbon::parse($dates[1])->format('Y-m-d') : $startDate;

                if ($startDate && $endDate) {
                    $query->whereHas('productionReceipt', function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('receipt_date', [$startDate, $endDate]);
                    });
                } elseif ($startDate) {
                    $query->whereHas('productionReceipt', function ($q) use ($startDate) {
                        $q->whereDate('receipt_date', $startDate);
                    });
                }
            }

            $totalRecords = $query->count();
            $filteredRecords = $totalRecords;

            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            if ($length != -1) {
                $query->skip($start)->take($length);
            }

            $items = $query->get();
            $data = [];
            $count = $start + 1;

            $sizeMappings = [
                '36' => 'S',
                '38' => 'M',
                '40' => 'L',
                '42' => 'XL',
                '44' => 'XXL',
                '46' => '3XL',
                '48' => '4XL',
                '50' => '5XL',
            ];

            foreach ($items as $item) {
                $date = '';
                if ($item->productionReceipt && $item->productionReceipt->receipt_date) {
                    $date = \Carbon\Carbon::parse($item->productionReceipt->receipt_date)->format('d-m-Y');
                }

                $rawSize = $item->size ?? '';
                $sizeChar = $sizeMappings[$rawSize] ?? '';
                $mappedSize = $rawSize . ($sizeChar ? ' ' . $sizeChar : '');
                
                $sleeveType = '';
                if (preg_match('/(F\/S|H\/S|Fs|Hs)/i', $item->item_name, $matches)) {
                    $sleeveType = str_ireplace(['Fs', 'Hs', 'F/s', 'H/s'], ['F/S', 'H/S', 'F/S', 'H/S'], $matches[1]);
                }
        
                $nameOfItem = trim(($item->art_no ?? '') . ' ' . $mappedSize . ' ' . $sleeveType);

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'date' => $date,
                    'item' => $nameOfItem,
                    'quantity' => $item->qty_to_receive ?? 0,
                    'price' => '₹' . number_format($item->unit_price ?? 0, 2),
                    'amount' => '₹' . number_format($item->total_value ?? 0, 2),
                ];
            }

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        }

        return view('production_receipts.report');
    }

    public function exportReport(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('export production-receipt-report')) {
            return unauthorizedRedirect();
        }
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ProductionReceiptReportExport($request), 'production_receipt_report_' . date('Ymd_His') . '.xlsx');
    }
}

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
use App\Models\User;
use App\Models\JobCardMatrixQuantity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductionReceiptController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view production')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $query = ProductionReceipt::with(['jobCard', 'storeType', 'storeLocation'])->orderBy('id', 'desc');

            $totalRecords = $query->count();

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
                if (auth()->id() == 1 || auth()->user()->can('view production')) {
                    $action .= '<a href="' . url('production_receipts/add/' . $row->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                    $action .= '<a href="' . url('production_receipts/view/' . $row->id) . '" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>';
                }
                /* if (auth()->id() == 1 || auth()->user()->can('delete production')) {
                 $action .= '<a href="' . url('production_receipts/delete/' . $row->id) . '" class="btn btn-delete ps-2" onclick="return confirm(\'Are you sure you want to delete this receipt?\')"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                 } */
                $action .= '</div>';

                $statusBadge = $row->status == 'Posted'
                    ? '<span class="badge bg-label-success">Posted</span>'
                    : '<span class="badge bg-label-warning">Draft</span>';

                $data[] = [
                    'DT_RowIndex' => $i++,
                    'receipt_no' => $row->receipt_no ?? ('RCPT-' . str_pad($row->id, 4, '0', STR_PAD_LEFT)),
                    'job_card_no' => $row->jobCard ? $row->jobCard->job_card_no : '-',
                    'receipt_date' => $row->receipt_date ? date('d-m-Y', strtotime($row->receipt_date)) : '-',
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

        return view('production_receipts.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('view production')) {
                return unauthorizedRedirect();
            }
        }
        else {
            if (auth()->id() != 1 && !auth()->user()->can('view production')) {
                return unauthorizedRedirect();
            }
        }

        $receipt = $id ?ProductionReceipt::with(['items'])->findOrFail($id) : null;
        if ($id) {
            $currentReceipt = ProductionReceipt::find($id);
            $usedJobCardIds = ProductionReceipt::where('id', '!=', $id)->whereNotNull('job_card_id')->pluck('job_card_id')->toArray();

            $jobCards = JobCardEntry::with('serviceProvider')
                ->where(function ($query) use ($currentReceipt, $usedJobCardIds) {
                $query->whereNotIn('id', $usedJobCardIds);
                if ($currentReceipt && $currentReceipt->job_card_id) {
                    $query->orWhere('id', $currentReceipt->job_card_id);
                }
            })->orderBy('id', 'desc')->get();
        }
        else {
            $usedJobCardIds = ProductionReceipt::whereNotNull('job_card_id')->pluck('job_card_id')->toArray();
            $jobCards = JobCardEntry::with('serviceProvider')->whereNotIn('id', $usedJobCardIds)->orderBy('id', 'desc')->get();
        }
        $storeTypes = StoreType::where('status', 'Active')->orderBy('store_type_name')->get();
        $storeLocations = StoreLocation::where('status', 'Active')->get();
        $employees = User::where('status', 'Active')->where('id', '!=', 1)->get();

        if ($request->isMethod('post')) {
            $rules = [
                'job_card_id' => 'required|exists:job_card_entries,id',
                'receipt_date' => 'required|date_format:d-m-Y',
                'doc_date' => 'required|date_format:d-m-Y',
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

            DB::beginTransaction();
            try {
                $data = [

                    'job_card_id' => $request->job_card_id,
                    'employee_id' => $request->employee_id,
                    'order_due_date' => $request->order_due_date ? date('Y-m-d', strtotime($request->order_due_date)) : null,
                    'receipt_no' => $request->receipt_no ?: ('RCPT-' . date('Y') . '-' . str_pad(ProductionReceipt::count() + 1, 4, '0', STR_PAD_LEFT)),
                    'receipt_date' => date('Y-m-d', strtotime($request->receipt_date)),
                    'doc_no' => $request->doc_no,
                    'doc_date' => $request->doc_date ? date('Y-m-d', strtotime($request->doc_date)) : null,
                    'store_type_id' => $request->store_type_id,
                    'store_location_id' => $request->store_location_id,
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

                if ($id) {
                    $existingEntry = StockEntry::withTrashed()
                        ->where('reference_document', $receipt->receipt_no)
                        ->where('entry_type', 'Finished Goods')
                        ->first();
                    if ($existingEntry) {
                        StockEntryItem::withTrashed()->where('stock_entry_id', $existingEntry->id)->forceDelete();
                        $existingEntry->forceDelete();
                    }
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

                DB::commit();
                return redirect('production_receipts')->with('success', 'Production receipt saved successfully');
            }
            catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
            }
        }

        return view('production_receipts.add', compact('receipt', 'jobCards', 'storeTypes', 'storeLocations', 'employees'));
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

    private function createStockEntry($receipt, $storeLocationId)
    {
        $lastEntry = StockEntry::latest('id')->first();
        $nextNumber = $lastEntry ? (int)substr($lastEntry->stock_entry_no, 2) + 1 : 1;
        $stockEntryNo = 'SE' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        $stockEntry = StockEntry::create([
            'stock_entry_no' => $stockEntryNo,
            'stock_date' => $receipt->receipt_date,
            'entry_type' => 'Finished Goods',
            'to_store_location_id' => $storeLocationId,
            'remarks' => $receipt->remarks,
            'reference_document' => $receipt->receipt_no,
            'status' => 'Posted',
            'color' => $receipt->color,
            'created_by' => Auth::id(),
            'price' => $receipt->items->sum('total_value'),
        ]);


        foreach ($receipt->items as $item) {
            if ($item->qty_to_receive > 0) {
                $itemCode = $item->item_code;
                $sleeve = 'Full';
                if (str_contains($item->size_variant, ' - ')) {
                    $parts = explode(' - ', $item->size_variant);
                    $sleevePart = trim($parts[1]);
                    $sleeve = ($sleevePart == 'F/S') ? 'Full' : (($sleevePart == 'H/S') ? 'Half' : $sleevePart);
                    if (!str_contains($itemCode, $sleevePart)) {
                        $itemCode = trim($parts[0]) . ' - ' . $sleevePart;
                    }
                }

                $year = date('y');
                $prefix = 'BC' . $year;
                $lastSku = StockEntryItem::where('sku', 'like', $prefix . '%')->orderBy('sku', 'desc')->first();
                $nextNum = 1;
                if ($lastSku) {
                    $nextNum = (int)substr($lastSku->sku, 4) + 1;
                }
                $sku = $prefix . str_pad($nextNum, 5, '0', STR_PAD_LEFT);

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
                    'name' => $item->item_name,
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
                    'uom_id' => $item->uom_id,
                    'qty_in' => $item->qty_to_receive,
                    'qty_out' => 0,
                    'price' => $item->unit_price,
                    'sku' => $sku,
                    'qrcode' => json_encode($qrData),
                    'sleeve_type' => $sleeve,
                ]);
            }
        }

        return $stockEntry;
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

        $getStoreCategoryIdForArtNo = function ($artNo) {
            $normalizedArtNo = trim($artNo ?? '');
            if ($normalizedArtNo === '') {
                return null;
            }

            $rawMaterial = \App\Models\RawMaterial::where('code', $normalizedArtNo)
                ->orWhere('name', $normalizedArtNo)
                ->first();
            if ($rawMaterial) {
                return $rawMaterial->store_category_id;
            }

            $row = \DB::table('grn_entry_items')
                ->join('purchase_invoice_items', 'grn_entry_items.purchase_invoice_item_id', '=', 'purchase_invoice_items.id')
                ->join('raw_materials', 'purchase_invoice_items.raw_material_id', '=', 'raw_materials.id')
                ->where('grn_entry_items.art_no', $normalizedArtNo)
                ->orderByDesc('grn_entry_items.id')
                ->select('raw_materials.store_category_id')
                ->first();

            return $row?->store_category_id ?? null;
        };

        // Production receipts should only be generated against Fabric (store_category_id = 1)
        $fabricDetails = $jobCard->fabricDetails
            ->filter(function ($fd) use ($getStoreCategoryIdForArtNo) {
                return $getStoreCategoryIdForArtNo($fd->art_no) === 1;
            })
            ->values();

        $articlePrices = [];
        foreach ($fabricDetails as $fabricDetail) {
            $artNo = trim($fabricDetail->art_no);
            $avgPrice = 0;

            $stockItems = \DB::table('stock_entry_items')->join('grn_entry_items', 'stock_entry_items.grn_entry_item_id', '=', 'grn_entry_items.id')->where('grn_entry_items.art_no', $artNo)->whereNull('stock_entry_items.deleted_at')->whereRaw('(stock_entry_items.qty_in - stock_entry_items.qty_out) > 0')->select('stock_entry_items.price', \DB::raw('(stock_entry_items.qty_in - stock_entry_items.qty_out) as available_qty'))->get();

            if ($stockItems->count() == 0) {
                $rawMaterial = \App\Models\RawMaterial::where('name', $artNo)->orWhere('code', $artNo)->first();
                if ($rawMaterial) {
                    $stockItems = \DB::table('stock_entry_items')->where('raw_material_id', $rawMaterial->id)->whereNull('deleted_at')->whereRaw('(qty_in - qty_out) > 0')->select('price', \DB::raw('(qty_in - qty_out) as available_qty'))->get();
                }
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
        
        $fallbackStyleName = '';
        if ($allPOItems) {
            $fabricPoItem = $allPOItems->where('store_category_id', 1)->whereNotNull('style_id')->first() 
                            ?: $allPOItems->whereNotNull('style_id')->first();
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

        $calculateItemUnitPrice = function ($artNo, $size, $sleeve) use ($fabricDetails, $articlePrices) {
            $totalCost = 0;
            $consumptionDetails = [];
            $normalizedArtNo = trim($artNo ?? '');

            foreach ($fabricDetails as $fd) {
                if ($normalizedArtNo !== '' && trim($fd->art_no ?? '') !== $normalizedArtNo) {
                    continue;
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
                $stockItem = \DB::table('stock_entry_items')->join('grn_entry_items', 'stock_entry_items.grn_entry_item_id', '=', 'grn_entry_items.id')->where('grn_entry_items.art_no', $fd->art_no)->select('stock_entry_items.raw_material_id')->first();

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

        $processQty = function ($artNo, $sleeve, $size, $qty, $color = null, $colorId = null) use (&$tempGrouped, $jobCard, $serviceName, $calculateItemUnitPrice, $fallbackStyleName, $artColorMap) {
            if ($qty > 0) {
                $sizeVariant = $size . ' - ' . $sleeve;
                $itemKey = $jobCard->item_id ?? '0';
                $normalizedArtNo = trim($artNo ?? '');
                $resolvedColorId = $artColorMap[$normalizedArtNo]['color_id'] ?? $colorId;
                $resolvedColor = $artColorMap[$normalizedArtNo]['color'] ?? $color;
                $key = $itemKey . '|' . $normalizedArtNo . '|' . $sizeVariant . '|' . ($resolvedColorId ?? $resolvedColor ?? '');

                if (!isset($tempGrouped[$key])) {
                    $pricing = $calculateItemUnitPrice($normalizedArtNo, $size, $sleeve);
                    $unitPrice = $pricing['total_cost'];
                    
                    $brandCode = ($jobCard->brand ? $jobCard->brand->code : ($jobCard->item && $jobCard->item->brand ? $jobCard->item->brand->code : ''));
                    $brandName = ($jobCard->brand ? $jobCard->brand->brand_name : ($jobCard->item && $jobCard->item->brand ? $jobCard->item->brand->brand_name : ''));
                    
                    $styleName = ($jobCard->item && $jobCard->item->style ? $jobCard->item->style->style_name : $fallbackStyleName);

                    $tempGrouped[$key] = [
                        'item_id' => $jobCard->item_id ?? null,
                        'item_code' => trim($brandCode . ' - ' . $styleName . ' - ' . $sleeve, ' - '),
                        'service_name' => $serviceName,
                        'sleeve' => $sleeve,
                        'size' => $size,
                        'item_name' => $serviceName,
                        'art_no' => $normalizedArtNo ?: null,
                        'description' => trim($brandName . ' ' . $styleName . ' ' . $sleeve),
                        'size_variant' => $sizeVariant,
                        'unit_price' => floatval($unitPrice),
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
                $processQty($fabDetail->art_no, 'F/S', $mq->size, $mq->qty_fs, $colorToUse, $mq->color_id);
                $processQty($fabDetail->art_no, 'H/S', $mq->size, $mq->qty_hs, $colorToUse, $mq->color_id);
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

        return response()->json([
            'success' => true,
            'data' => [
                'job_card_no' => $jobCard->job_card_no,
                'employee_id' => null, // Left empty because employee shouldn't be auto-fetched from job card, it's manually selected
                'purchase_order_id' => $jobCard->purchase_order_id,
                'po_number' => $jobCard->purchaseOrder ? $jobCard->purchaseOrder->po_number : '',
                'plant_id' => $jobCard->service_provider_id,
                'plant_name' => $jobCard->serviceProvider ? $jobCard->serviceProvider->name : '',
                'order_due_date' => ($jobCard->purchaseOrder && $jobCard->purchaseOrder->due_date) ? date('d-m-Y', strtotime($jobCard->purchaseOrder->due_date)) : '',
                'job_card_date' => $jobCard->job_card_date ? date('d-m-Y', strtotime($jobCard->job_card_date)) : '',
                'doc_no' => $jobCard->job_card_no,
                'items' => $items,
            ]
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
        if (auth()->id() != 1 && !auth()->user()->can('view production')) {
            return unauthorizedRedirect();
        }

        $receipt = ProductionReceipt::with([
            'jobCard.brand',
            'jobCard.fabricDetails.quantities',
            'jobCard.season',
            'jobCard.serviceProvider',
            'storeType',
            'storeLocation',
            'employee',
            'items.uom'
        ])->findOrFail($id);

        foreach ($receipt->items as $item) {
            $item->resolved_art_no = $this->resolveReceiptItemArtNo($receipt->jobCard, $item->toArray());
        }

        return view('production_receipts.view_details', compact('receipt'));
    }

    public function print($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view production')) {
            return unauthorizedRedirect();
        }

        $receipt = ProductionReceipt::with([
            'jobCard.brand',
            'jobCard.fabricDetails.quantities',
            'jobCard.season',
            'jobCard.serviceProvider',
            'storeType',
            'storeLocation',
            'employee',
            'items.uom'
        ])->findOrFail($id);

        foreach ($receipt->items as $item) {
            $item->resolved_art_no = $this->resolveReceiptItemArtNo($receipt->jobCard, $item->toArray());
        }

        $is_print = true;
        return view('production_receipts.pdf', compact('receipt', 'is_print'));
    }

    public function downloadPdf($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view production')) {
            return unauthorizedRedirect();
        }

        $receipt = ProductionReceipt::with([
            'jobCard.brand',
            'jobCard.fabricDetails.quantities',
            'jobCard.season',
            'jobCard.serviceProvider',
            'storeType',
            'storeLocation',
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
}

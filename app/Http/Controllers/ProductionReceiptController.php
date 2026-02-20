<?php

namespace App\Http\Controllers;

use App\Models\Production;
use App\Models\ProductionReceipt;
use App\Models\ProductionReceiptItem;
use App\Models\JobCardEntry;
use App\Models\StoreType;
use App\Models\StockEntry;
use App\Models\StockEntryItem;
use App\Models\Task;
use App\Models\StoreLocation;
use App\Models\Item;
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
            $receipts = ProductionReceipt::with(['jobCard', 'storeType', 'storeLocation'])->orderBy('id', 'desc')->get();
            $data = [];
            $i = 1;

            foreach ($receipts as $row) {
                $action = '<div class="button-box">';
                if (auth()->id() == 1 || auth()->user()->can('view production')) {
                    $action .= '<a href="' . url('production_receipts/add/' . $row->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }
                /* if (auth()->id() == 1 || auth()->user()->can('delete production')) {
                    $action .= '<a href="' . url('production_receipts/delete/' . $row->id) . '" class="btn btn-delete ps-2" onclick="return confirm(\'Are you sure you want to delete this receipt?\')"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                } */
                $action .= '</div>';

                $statusBadge = $row->status == 'Posted' 
                    ? '<span class="badge bg-label-success">Posted</span>'
                    : '<span class="badge bg-label-warning">Draft</span>';

                $data[] = [
                    'DT_RowIndex'   => $i++,
                    'receipt_no'    => $row->receipt_no ?? ('RCPT-' . str_pad($row->id, 4, '0', STR_PAD_LEFT)),
                    'job_card_no'   => $row->jobCard ? $row->jobCard->job_card_no : '-',
                    'receipt_date'  => $row->receipt_date ? date('d-m-Y', strtotime($row->receipt_date)) : '-',
                    'store'          => $row->storeType ? $row->storeType->store_type_name : '-',
                    'status'         => $statusBadge,
                    'store_location' => $row->storeLocation ? $row->storeLocation->store_location : '-',
                    'action'         => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('production_receipts.view');
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('view production')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('view production')) {
                return unauthorizedRedirect();
            }
        }

        $receipt = $id ? ProductionReceipt::with(['production.plant', 'items'])->findOrFail($id) : null;

        if ($id) {
            $currentReceipt = ProductionReceipt::find($id);
            $usedJobCardIds = ProductionReceipt::where('id', '!=', $id)->whereNotNull('job_card_id')->pluck('job_card_id')->toArray();
            
            $jobCards = JobCardEntry::with('serviceProvider')
                ->where(function($query) use ($currentReceipt, $usedJobCardIds) {
                   $query->where('status', 'Production Completed')->whereNotIn('id', $usedJobCardIds);
                    if ($currentReceipt && $currentReceipt->job_card_id) {
                       $query->orWhere('id', $currentReceipt->job_card_id);
                    }
                })
                ->orderBy('id', 'desc')->get();
        } else {
            $usedJobCardIds = ProductionReceipt::whereNotNull('job_card_id')->pluck('job_card_id')->toArray();
            $jobCards = JobCardEntry::with('serviceProvider')->where('status', 'Production Completed')->whereNotIn('id', $usedJobCardIds)->orderBy('id', 'desc')->get();
        }
        $storeTypes = StoreType::where('status', 'Active')->orderBy('store_type_name')->get();
        $storeLocations = StoreLocation::where('status', 'Active')->get();

        if ($request->isMethod('post')) {
            $rules = [
                'job_card_id'   => 'required|exists:job_card_entries,id',
                'receipt_no'    => 'required|min:3|max:50|unique:production_receipts,receipt_no,' . ($id ?? 'NULL'),
                'receipt_date'  => 'required|date_format:d-m-Y',
                'doc_date'      => 'required|date_format:d-m-Y',
                'store_type_id' => 'required|exists:store_types,id',
                'store_location_id' => 'required|exists:store_locations,id',
                'status'        => 'required|in:Draft,Posted',
                'items'         => 'required|array|min:1',
                'remarks'       => 'nullable|min:5|max:255',
            ];

            $messages = [
                'required' => 'This field is required.',
                'min'      => 'This field must be at least :min characters.',
                'max'      => 'This field should not be more than :max characters.',
                'unique'   => 'This field already exists.',
            ];

            $request->validate($rules, $messages);

            $production = Production::where('job_card_entry_id', $request->job_card_id)->first();
            if (!$production) {
                return back()->with('error', 'Production not found for this Job Card')->withInput();
            }

            DB::beginTransaction();
            try {
                $data = [
                    'production_id' => $production->id,
                    'job_card_id'   => $request->job_card_id,
                    'customer_name' => $request->customer_name,
                    'order_due_date'=> $request->order_due_date ? date('Y-m-d', strtotime($request->order_due_date)) : null,
                    'receipt_no'    => $request->receipt_no ?: ('RCPT-' . date('Y') . '-' . str_pad(ProductionReceipt::count() + 1, 4, '0', STR_PAD_LEFT)),
                    'receipt_date'  => date('Y-m-d', strtotime($request->receipt_date)),
                    'doc_no'        => $request->doc_no,
                    'doc_date'      => $request->doc_date ? date('Y-m-d', strtotime($request->doc_date)) : null,
                    'store_type_id' => $request->store_type_id,
                    'store_location_id' => $request->store_location_id,
                    'status'        => $request->status,
                    'remarks'       => $request->remarks,
                ];

                if ($receipt) {
                    $oldData = $receipt->load('items')->toArray();
                    $data['updated_by'] = Auth::id();
                    $receipt->update($data);
                    $receipt->items()->delete();
                } else {
                    $data['created_by'] = Auth::id();
                    $receipt = ProductionReceipt::create($data);
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
                            
                            ProductionReceiptItem::create([
                                'production_receipt_id' => $receipt->id,
                                'item_id' => $itemData['item_id'] ?? null,
                                'item_code' => $itemData['item_code'] ?? '',
                                'item_name' => $itemData['item_name'] ?? '',
                                'art_no' => $itemData['art_no'] ?? null,
                                'size' => $itemData['size'] ?? null,
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
                } else {
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
                } else {
                    if (isset($oldData) && $oldData['status'] == 'Posted') {
                        $this->revertActualConsumables($receipt, $oldData);
                    }
                }

                DB::commit();
                return redirect('production_receipts')->with('success', 'Production receipt saved successfully');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
            }
        }

        return view('production_receipts.add', compact('receipt', 'jobCards', 'storeTypes', 'storeLocations'));
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
            'created_by' => Auth::id(),
            'price' => $receipt->items->sum('total_value'),
        ]);

        foreach ($receipt->items as $item) {
            if ($item->qty_to_receive > 0) {
                $itemCode = $item->item_code;
                if (str_contains($item->size_variant, ' - ')) {
                    $sleevePart = trim(explode(' - ', $item->size_variant)[1]);
                    if (!str_contains($itemCode, $sleevePart)) {
                        $itemCode = trim(explode(' - ', $itemCode)[0]) . ' - ' . $sleevePart;
                    }
                }

                StockEntryItem::create([
                    'stock_entry_id' => $stockEntry->id,
                    'stock_type' => 'finished_goods',
                    'finished_item_code' => $itemCode,
                    'art_no' => $item->art_no,
                    'size' => $item->size,
                    'store_location_id' => $storeLocationId,
                    'uom_id' => $item->uom_id,
                    'qty_in' => $item->qty_to_receive,
                    'qty_out' => 0,
                    'price' => $item->unit_price,
                ]);
            }
        }

        return $stockEntry;
    }

    public function getJobCardDetails(Request $request, $id)
    {
        $jobCard = JobCardEntry::with([
            'serviceProvider', 
            'purchaseOrder.supplier', 
            'item', 
            'item.uom', 
            'fabricDetails',             
            'sleeveMeters',             
            'cuttingSizeRatios', 
            'processGroup'
        ])->find($id);

        if (!$jobCard) {
            return response()->json(['success' => false, 'message' => 'Job Card not found'], 404);
        }

        $articlePrices = [];
        foreach ($jobCard->fabricDetails as $fabricDetail) {
            $artNo = trim($fabricDetail->art_no);
            $avgPrice = 0;

            $stockItems = \DB::table('stock_entry_items')
                ->join('grn_entry_items', 'stock_entry_items.grn_entry_item_id', '=', 'grn_entry_items.id')
                ->where('grn_entry_items.art_no', $artNo)
                ->whereNull('stock_entry_items.deleted_at')
                ->whereRaw('(stock_entry_items.qty_in - stock_entry_items.qty_out) > 0')
                ->select('stock_entry_items.price', \DB::raw('(stock_entry_items.qty_in - stock_entry_items.qty_out) as available_qty'))
                ->get();

            if ($stockItems->count() == 0) {
                $rawMaterial = \App\Models\RawMaterial::where('name', $artNo)->orWhere('code', $artNo)->first();
                if ($rawMaterial) {
                    $stockItems = \DB::table('stock_entry_items')
                        ->where('raw_material_id', $rawMaterial->id)
                        ->whereNull('deleted_at')
                        ->whereRaw('(qty_in - qty_out) > 0')
                        ->select('price', \DB::raw('(qty_in - qty_out) as available_qty'))
                        ->get();
                }
            }

            if ($stockItems->count() > 0) {
                $totalVal = 0;
                $totalQty = 0;
                foreach($stockItems as $si) {
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
            ->when($excludeReceiptId, function($q) use ($excludeReceiptId) {
                return $q->where('id', '!=', $excludeReceiptId);
            })->pluck('id');
        
        $existingReceiptsItems = ProductionReceiptItem::whereIn('production_receipt_id', $existingReceiptIds)
            ->get()
            ->groupBy(function($item) {
                return ($item->item_id ?? '0') . '|' . ($item->size_variant ?? '');
            })->map(function($group) {
                return $group->sum('qty_to_receive');
            });

        $items = [];
        $tempGrouped = [];
        $serviceName = $jobCard->processGroup ? $jobCard->processGroup->name : 'Production Service';

        $calculateItemUnitPrice = function($size, $sleeve) use ($jobCard, $articlePrices) {
            $totalCost = 0;
            $consumptionDetails = [];
            
            foreach ($jobCard->fabricDetails as $fd) {
                $rate = 0;
                $avgPrice = $articlePrices[$fd->id] ?? 0;

                $consumption = \App\Models\JobCardFabricConsumption::where('job_card_fabric_detail_id', $fd->id)->where('size', $size)->first();
                
                if ($consumption && (($sleeve == 'F/S' && $consumption->fs_cons > 0) || ($sleeve == 'H/S' && $consumption->hs_cons > 0))) {
                    $rate = ($sleeve == 'F/S') ? $consumption->fs_cons : $consumption->hs_cons;
                } elseif ($fd->fs_qty > 0 || $fd->hs_qty > 0) {
                    $rate = ($sleeve == 'F/S') ? $fd->fs_qty : $fd->hs_qty;
                } else {
                    $globalSleeve = $jobCard->sleeveMeters
                        ->where('sleeve_type', ($sleeve == 'F/S' ? 'Full Sleeve' : 'Half Sleeve'))
                        ->first();
                    $rate = $globalSleeve ? $globalSleeve->meter : 0;
                }
                
                $rawMaterial = null;
                $stockItem = \DB::table('stock_entry_items')
                    ->join('grn_entry_items', 'stock_entry_items.grn_entry_item_id', '=', 'grn_entry_items.id')
                    ->where('grn_entry_items.art_no', $fd->art_no)
                    ->select('stock_entry_items.raw_material_id')
                    ->first();
                
                if ($stockItem) {
                    $rawMaterial = \App\Models\RawMaterial::with(['storeCategory', 'uom'])->find($stockItem->raw_material_id);
                } else {
                    $rawMaterial = \App\Models\RawMaterial::with(['storeCategory', 'uom'])->where('code', $fd->art_no)->orWhere('name', $fd->art_no)->first();
                }
                
                $materialName = '';
                if ($rawMaterial) {
                    $materialName = ($rawMaterial->storeCategory ? $rawMaterial->storeCategory->category_name . ' - ' : '') . $rawMaterial->name;
                } else {
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
                    'total_cost' => floatval($cost)
                ];
            }

            // 2. Issue Items (Buttons, Trims, etc.)
            $issueItems = \App\Models\JobCardIssueItem::where('job_card_entry_id', $jobCard->id)->get();
            foreach ($issueItems as $issueItem) {
                $rate = floatval($issueItem->average ?? 0);
                if ($rate <= 0) continue;

                $avgPrice = 0; 
                // Fetch price from stock or grn
                if ($issueItem->stock_entry_item_id) {
                    $stockInfo = \DB::table('stock_entry_items')->where('id', $issueItem->stock_entry_item_id)->first();
                    $avgPrice = $stockInfo ? $stockInfo->price : 0;
                }

                $artNo = '';
                $materialName = 'Consumable';
                $uomName = 'PCS';

                $stockItem = \DB::table('stock_entry_items')
                    ->join('grn_entry_items', 'stock_entry_items.grn_entry_item_id', '=', 'grn_entry_items.id')
                    ->where('stock_entry_items.id', $issueItem->stock_entry_item_id)
                    ->select('grn_entry_items.art_no', 'stock_entry_items.raw_material_id', 'stock_entry_items.uom_id')
                    ->first();

                if ($stockItem) {
                    $artNo = $stockItem->art_no;
                    $rm = \App\Models\RawMaterial::with(['uom', 'storeCategory'])->find($stockItem->raw_material_id);
                    if ($rm) {
                        $materialName = ($rm->storeCategory ? $rm->storeCategory->category_name . ' - ' : '') . $rm->name;
                        $uomName = $rm->uom ? $rm->uom->uom_code : 'PCS';
                    }
                }

                $cost = ($rate * $avgPrice);
                $totalCost += $cost;
                $consumptionDetails[] = [
                    'art_no' => $artNo,
                    'material_name' => $materialName,
                    'uom' => $uomName,
                    'rate' => $rate,
                    'price' => floatval($avgPrice),
                    'total_cost' => floatval($cost)
                ];
            }

            return [
                'total_cost' => $totalCost,
                'consumption_details' => $consumptionDetails
            ];
        };

        $processQty = function($sleeve, $size, $qty) use (&$tempGrouped, $jobCard, $serviceName, $calculateItemUnitPrice) {
             if ($qty > 0) {
                $sizeVariant = $size . ' - ' . $sleeve;
                $itemKey = $jobCard->item_id ?? '0';
                $key = $itemKey . '|' . $sizeVariant;
                
                if (!isset($tempGrouped[$key])) {
                    $pricing = $calculateItemUnitPrice($size, $sleeve);
                    $unitPrice = $pricing['total_cost'];
                    $tempGrouped[$key] = [
                        'item_id' => $jobCard->item_id ?? null,
                        'item_code' => ($jobCard->item ? $jobCard->item->code : '') . ' - ' . $sleeve,
                        'service_name' => $serviceName,
                        'sleeve' => $sleeve,
                        'size' => $size,
                        'item_name' => $serviceName,
                        'art_no' => null,
                        'description' => ($jobCard->item && $jobCard->item->name) ? $jobCard->item->name : '',
                        'size_variant' => $sizeVariant,
                        'unit_price' => floatval($unitPrice),
                        'uom_id' => $jobCard->item ? $jobCard->item->uom_id : null,
                        'uom_code' => 'PCS',
                        'ordered_qty' => 0,
                        'completed_qty' => 0,
                        'consumption_details' => $pricing['consumption_details'],
                    ];
                }
                $tempGrouped[$key]['ordered_qty'] += $qty;
                $tempGrouped[$key]['completed_qty'] += $qty;
             }
        };

        $fabDetail = $jobCard->fabricDetails->first();
        if ($fabDetail) {
            $matrixQtys = \App\Models\JobCardMatrixQuantity::where('job_card_fabric_detail_id', $fabDetail->id)->get();
            foreach ($matrixQtys as $mq) {
                $processQty('F/S', $mq->size, $mq->qty_fs);
                $processQty('H/S', $mq->size, $mq->qty_hs);
            }
        }

        foreach ($tempGrouped as $key => $itemData) {
            $alreadyRec = $existingReceiptsItems->get($key) ?? 0;
            $balance = $itemData['completed_qty'] - $alreadyRec;

            if ($balance > 0 || ($excludeReceiptId && ProductionReceiptItem::where('production_receipt_id', $excludeReceiptId)->where('item_id', $itemData['item_id'])->where('size_variant', $itemData['size_variant'])->exists())) {
                
                $scanQty = 0; 
                if ($excludeReceiptId) {
                    $currentReceiptItem = ProductionReceiptItem::where('production_receipt_id', $excludeReceiptId)
                        ->where('item_id', $itemData['item_id'])
                        ->where('size_variant', $itemData['size_variant'])
                        ->first();
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
                'customer_name' => ($jobCard->purchaseOrder && $jobCard->purchaseOrder->supplier) ? $jobCard->purchaseOrder->supplier->name : '-',
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
            if ($qty <= 0) continue;

            $sleeveType = 'All';
            if (str_contains($item->size_variant, ' - F/S')) {
                $sleeveType = 'F/S';
            } elseif (str_contains($item->size_variant, ' - H/S')) {
                $sleeveType = 'H/S';
            }

            $consumables = \App\Models\ProductionStageConsumable::where('production_id', $receipt->production_id)
                ->where(function($q) use ($sleeveType) {
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
        if (!isset($oldData['items'])) return;

        foreach ($oldData['items'] as $item) {
            $qty = $item['qty_to_receive'] ?? 0;
            if ($qty <= 0) continue;

            $sleeveType = 'All';
            if (isset($item['size_variant'])) {
                if (str_contains($item['size_variant'], ' - F/S')) {
                    $sleeveType = 'F/S';
                } elseif (str_contains($item['size_variant'], ' - H/S')) {
                    $sleeveType = 'H/S';
                }
            }

            $consumables = \App\Models\ProductionStageConsumable::where('production_id', $receipt->production_id)
                ->where(function($q) use ($sleeveType) {
                    $q->where('sleeve_type', $sleeveType)->orWhere('sleeve_type', 'All');
                })
                ->get();

            foreach ($consumables as $con) {
                $con->actual_qty = max(0, $con->actual_qty - ($qty * $con->quantity_per_unit));
                $con->save();
            }
        }
    }
}


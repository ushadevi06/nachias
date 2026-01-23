<?php

namespace App\Http\Controllers;

use App\Models\Production;
use App\Models\ProductionReceipt;
use App\Models\ProductionReceiptItem;
use App\Models\JobCardEntry;
use App\Models\StoreType;
use App\Models\Task;
use App\Models\TaskReceive;
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
            $receipts = ProductionReceipt::with(['production', 'jobCard', 'storeType'])->latest()->get();
            $data = [];
            $i = 1;

            foreach ($receipts as $row) {
                $action = '<div class="button-box">';
                if (auth()->id() == 1 || auth()->user()->can('view production')) {
                    $action .= '<a href="' . url('production_receipts/add/' . $row->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }
                if (auth()->id() == 1 || auth()->user()->can('delete production')) {
                    $action .= '<a href="' . url('production_receipts/delete/' . $row->id) . '" class="btn btn-delete ps-2" onclick="return confirm(\'Are you sure you want to delete this receipt?\')"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                }
                $action .= '</div>';

                $statusBadge = $row->status == 'Posted' 
                    ? '<span class="badge bg-label-success">Posted</span>'
                    : '<span class="badge bg-label-warning">Draft</span>';

                $data[] = [
                    'DT_RowIndex'   => $i++,
                    'receipt_no'    => $row->receipt_no ?? ('RCPT-' . str_pad($row->id, 4, '0', STR_PAD_LEFT)),
                    'job_card_no'   => $row->jobCard ? $row->jobCard->job_card_no : '-',
                    'receipt_date'  => $row->receipt_date ? date('d-m-Y', strtotime($row->receipt_date)) : '-',
                    'store'         => $row->storeType ? $row->storeType->store_type_name : '-',
                    'status'        => $statusBadge,
                    'action'        => $action,
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
        $jobCards = JobCardEntry::with('serviceProvider')->orderBy('id', 'desc')->get();
        $storeTypes = StoreType::where('status', 'Active')->orderBy('store_type_name')->get();

        if ($request->isMethod('post')) {
            $rules = [
                'job_card_id'   => 'required|exists:job_card_entries,id',
                'receipt_no'    => 'required|unique:production_receipts,receipt_no,' . ($id ?? 'NULL'),
                'receipt_date'  => 'required|date_format:d-m-Y',
                'doc_date'      => 'required|date_format:d-m-Y',
                'store_type_id' => 'required|exists:store_types,id',
                'status'        => 'required|in:Draft,Posted',
                'items'         => 'required|array|min:1',
                // 'items.*.scan_qty' => 'nullable|numeric|min:0.01',
            ];

            $messages = [
                'required' => 'This field is required.',
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

                DB::commit();
                return redirect('production_receipts')->with('success', 'Production receipt saved successfully');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
            }
        }

        return view('production_receipts.add', compact('receipt', 'jobCards', 'storeTypes'));
    }

    public function getJobCardDetails(Request $request, $id)
    {
        $jobCard = JobCardEntry::with(['serviceProvider', 'purchaseOrder.supplier', 'item', 'item.uom', 'fabricDetails.quantities', 'cuttingSizeRatios', 'processGroup'])->find($id);
        if (!$jobCard) {
            return response()->json(['success' => false, 'message' => 'Job Card not found'], 404);
        }

        $production = Production::where('job_card_entry_id', $id)->first();
        $tasks = Task::where('job_card_entry_id', $id)->get();
        $taskIds = $tasks->pluck('id');
        
        $completedQtys = TaskReceive::whereIn('task_id', $taskIds)->selectRaw('task_id, SUM(good_qty) as total_completed')->groupBy('task_id')->pluck('total_completed', 'task_id')->toArray();
        $totalCompletedQty = array_sum($completedQtys);
        
        $excludeReceiptId = $request->input('exclude_receipt_id');
        $existingReceiptIds = ProductionReceipt::where('job_card_id', $id)
            ->when($excludeReceiptId, function($q) use ($excludeReceiptId) {
                return $q->where('id', '!=', $excludeReceiptId);
            })->pluck('id');
        
        $alreadyReceivedQty = ProductionReceiptItem::whereIn('production_receipt_id', $existingReceiptIds)->sum('qty_to_receive');
        
        $items = [];
        
        $existingReceiptsItems = ProductionReceiptItem::whereIn('production_receipt_id', $existingReceiptIds)
            ->get()
            ->groupBy(function($item) {
                return ($item->item_id ?? '0') . '|' . ($item->size_variant ?? '');
            })->map(function($group) {
                return $group->sum('qty_to_receive');
            });

        $tempGrouped = [];

        if ($jobCard->item_id && $jobCard->item && $jobCard->fabricDetails->count() == 0) {
            $item = $jobCard->item;
            $itemKey = $item->id;
            $serviceName = $jobCard->processGroup ? $jobCard->processGroup->name : 'Production Service';
            
            if ($jobCard->cuttingSizeRatios->count() > 0) {
                foreach ($jobCard->cuttingSizeRatios as $ratio) {
                    $size = $ratio->size ?? '';
                    if ($ratio->qty_fs > 0) {
                        $sleeve = 'F/S';
                        $sizeVariant = $size . ' - ' . $sleeve;
                        $key = $itemKey . '|' . $sizeVariant;
                        if (!isset($tempGrouped[$key])) {
                            $tempGrouped[$key] = [
                                'item_id' => $item->id,
                                'item_code' => $item->code ?? '',
                                'service_name' => $serviceName,
                                'sleeve' => $sleeve,
                                'size' => $size,
                                'item_name' => $serviceName,
                                'art_no' => null,
                                'description' => $item->name ?? '',
                                'size_variant' => $sizeVariant,
                                'unit_price' => floatval($jobCard->price_fs ?? 0),
                                'uom_id' => $item->uom_id,
                                'uom_code' => $item->uom ? $item->uom->uom_code : 'PCS',
                                'ordered_qty' => 0,
                                'completed_qty' => 0,
                            ];
                        }
                        $tempGrouped[$key]['ordered_qty'] += $ratio->qty_fs;
                        $tempGrouped[$key]['completed_qty'] += $ratio->qty_fs;
                    }
                    if ($ratio->qty_hs > 0) {
                        $sleeve = 'H/S';
                        $sizeVariant = $size . ' - ' . $sleeve;
                        $key = $itemKey . '|' . $sizeVariant;
                        if (!isset($tempGrouped[$key])) {
                            $tempGrouped[$key] = [
                                'item_id' => $item->id,
                                'item_code' => $item->code ?? '',
                                'service_name' => $serviceName,
                                'sleeve' => $sleeve,
                                'size' => $size,
                                'item_name' => $serviceName,
                                'art_no' => null,
                                'description' => $item->name ?? '',
                                'size_variant' => $sizeVariant,
                                'unit_price' => floatval($jobCard->price_hs ?? 0),
                                'uom_id' => $item->uom_id,
                                'uom_code' => $item->uom ? $item->uom->uom_code : 'PCS',
                                'ordered_qty' => 0,
                                'completed_qty' => 0,
                            ];
                        }
                        $tempGrouped[$key]['ordered_qty'] += $ratio->qty_hs;
                        $tempGrouped[$key]['completed_qty'] += $ratio->qty_hs;
                    }
                }
            }
        } else {
            $serviceName = $jobCard->processGroup ? $jobCard->processGroup->name : 'Production Service';
            foreach ($jobCard->fabricDetails as $fabricDetail) {
                foreach ($fabricDetail->quantities as $qtyEntry) {
                    $size = $qtyEntry->size ?? '';
                    $itemKey = $jobCard->item_id ?? '0';

                    if ($qtyEntry->qty_fs > 0) {
                        $sleeve = 'F/S';
                        $sizeVariant = $size . ' - ' . $sleeve;
                        $key = $itemKey . '|' . $sizeVariant;
                        if (!isset($tempGrouped[$key])) {
                            $tempGrouped[$key] = [
                                'item_id' => $jobCard->item_id ?? null,
                                'item_code' => $jobCard->item ? $jobCard->item->code : '',
                                'service_name' => $serviceName,
                                'sleeve' => $sleeve,
                                'size' => $size,
                                'item_name' => $serviceName,
                                'art_no' => null,
                                'description' => ($jobCard->item && $jobCard->item->name) ? $jobCard->item->name : '',
                                'size_variant' => $sizeVariant,
                                'unit_price' => floatval($jobCard->price_fs ?? 0),
                                'uom_id' => $jobCard->item ? $jobCard->item->uom_id : null,
                                'uom_code' => 'PCS',
                                'ordered_qty' => 0,
                                'completed_qty' => 0,
                            ];
                        }
                        $tempGrouped[$key]['ordered_qty'] += $qtyEntry->qty_fs;
                        $tempGrouped[$key]['completed_qty'] += $qtyEntry->qty_fs;
                    }

                    if ($qtyEntry->qty_hs > 0) {
                        $sleeve = 'H/S';
                        $sizeVariant = $size . ' - ' . $sleeve;
                        $key = $itemKey . '|' . $sizeVariant;
                        if (!isset($tempGrouped[$key])) {
                            $tempGrouped[$key] = [
                                'item_id' => $jobCard->item_id ?? null,
                                'item_code' => $jobCard->item ? $jobCard->item->code : '',
                                'service_name' => $serviceName,
                                'sleeve' => $sleeve,
                                'size' => $size,
                                'item_name' => $serviceName,
                                'art_no' => null,
                                'description' => ($jobCard->item && $jobCard->item->name) ? $jobCard->item->name : '',
                                'size_variant' => $sizeVariant,
                                'unit_price' => floatval($jobCard->price_hs ?? 0),
                                'uom_id' => $jobCard->item ? $jobCard->item->uom_id : null,
                                'uom_code' => 'PCS',
                                'ordered_qty' => 0,
                                'completed_qty' => 0,
                            ];
                        }
                        $tempGrouped[$key]['ordered_qty'] += $qtyEntry->qty_hs;
                        $tempGrouped[$key]['completed_qty'] += $qtyEntry->qty_hs;
                    }
                }
            }
        }



        foreach ($tempGrouped as $key => $itemData) {
            $alreadyRec = $existingReceiptsItems->get($key) ?? 0;
            $balance = $itemData['completed_qty'] - $alreadyRec;

            if ($balance > 0 || ($excludeReceiptId && ProductionReceiptItem::where('production_receipt_id', $excludeReceiptId)->where('item_id', $itemData['item_id'])->where('size_variant', $itemData['size_variant'])->exists())) {
                
                $currentReceiptItem = null;
                if ($excludeReceiptId) {
                    $currentReceiptItem = ProductionReceiptItem::where('production_receipt_id', $excludeReceiptId)
                        ->where('item_id', $itemData['item_id'])
                        ->where('size_variant', $itemData['size_variant'])
                        ->first();
                }

                $scanQty = $currentReceiptItem ? $currentReceiptItem->qty_to_receive : 0;
                
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
                'plant_id' => $jobCard->service_provider_id,
                'plant_name' => $jobCard->serviceProvider ? $jobCard->serviceProvider->name : '-',
                'job_card_date' => $jobCard->job_card_date ? date('d-m-Y', strtotime($jobCard->job_card_date)) : '',
                'purchase_order_no' => $jobCard->purchaseOrder ? $jobCard->purchaseOrder->po_number : '-',
                'customer_name' => ($jobCard->purchaseOrder && $jobCard->purchaseOrder->supplier) ? $jobCard->purchaseOrder->supplier->name : '-',
                'order_due_date' => ($jobCard->purchaseOrder && $jobCard->purchaseOrder->due_date) ? date('d-m-Y', strtotime($jobCard->purchaseOrder->due_date)) : '',
                'items' => $items,
            ]
        ]);
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete production')) {
            return unauthorizedRedirect();
        }

        try {
            $receipt = ProductionReceipt::with('items')->findOrFail($id);
            $oldData = $receipt->toArray();
            $receipt->items()->delete();
            $receipt->delete();

            addLog('delete', 'Production Receipt', 'production_receipts', $id, $oldData, null);

            return redirect('production_receipts')->with('success', 'Production Receipt deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting receipt: ' . $e->getMessage());
        }
    }
}


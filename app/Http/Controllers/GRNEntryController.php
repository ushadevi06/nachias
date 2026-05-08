<?php

namespace App\Http\Controllers;

use App\Models\GrnEntry;
use App\Models\GrnEntryItem;
use App\Models\GrnEntryItemVariant;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Models\FabricType;
use App\Models\StoreLocation;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class GrnEntryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = GrnEntry::with(['purchaseInvoice', 'supplier', 'grnEntryItems']);

            if ($request->supplier_id) {
                $query->where('supplier_id', $request->supplier_id);
            }

            if ($request->status) {
                $query->whereHas('grnEntryItems', function ($q) use ($request) {
                    $q->where('quality_check_status', $request->status);
                });
            }

            $totalRecords = $query->count();

            if ($request->has('search') && !empty($request->input('search')['value'])) {
                $search = $request->input('search')['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('grn_number', 'like', "%{$search}%")
                        ->orWhereHas('purchaseInvoice', function ($q2) use ($search) {
                            $q2->where('invoice_no', 'like', "%{$search}%")
                               ->orWhere('po_reference', 'like', "%{$search}%")
                               ->orWhereHas('purchaseOrder', function ($q3) use ($search) {
                                   $q3->where('po_number', 'like', "%{$search}%");
                               });
                        })
                        ->orWhereHas('supplier', function ($q4) use ($search) {
                            $q4->where('name', 'like', "%{$search}%")
                               ->orWhere('code', 'like', "%{$search}%");
                        });
                });
            }

            $filteredRecords = $query->count();

            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            if ($length != -1) {
                $query->skip($start)->take($length);
            }

            $grnEntries = $query->with(['purchaseInvoice.purchaseOrder', 'supplier', 'grnEntryItems'])->latest()->get();
            $data = [];
            $count = $start + 1;

            foreach ($grnEntries as $grn) {
                $statusLabel = $grn->status ?? 'Draft';
                $statusBadgeClass = 'bg-secondary';
                if ($statusLabel == 'Received')
                    $statusBadgeClass = 'bg-success';
                if ($statusLabel == 'Invoiced')
                    $statusBadgeClass = 'bg-info';
                if ($statusLabel == 'Closed')
                    $statusBadgeClass = 'bg-dark';

                $statusDisplay = '<span class="badge ' . $statusBadgeClass . '">' . $statusLabel . '</span>';

                $action = '<div class="button-box">';
                if (auth()->id() == 1 || auth()->user()->can('view grn-entry')) {
                    $action .= '<a href="' . url('grn_entries/view/' . $grn->id) . '" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>';
                }
                if (auth()->id() == 1 || auth()->user()->can('edit grn-entry')) {
                    $action .= '<a href="' . url('grn_entries/add/' . $grn->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }
                // if (auth()->id() == 1 || auth()->user()->can('delete grn-entry')) {
                //     $action .= '<button class="btn btn-delete" onclick="delete_data(`' . url('grn_entries/delete/' . $grn->id) . '`)"><i class="icon-base ri ri-delete-bin-line"></i></button>';
                // }
                $action .= '</div>';

                $qcStatuses = $grn->grnEntryItems->pluck('quality_check_status')->unique();
                $finalQcStatus = 'Pass';
                $badgeClass = 'bg-success';

                if ($qcStatuses->contains('Fail')) {
                    $finalQcStatus = 'Fail';
                    $badgeClass = 'bg-danger';
                } elseif ($qcStatuses->contains('Hold')) {
                    $finalQcStatus = 'Hold';
                    $badgeClass = 'bg-warning';
                }

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'grn_number' => $grn->grn_number,
                    'grn_date' => $grn->grn_date->format('d-m-Y'),
                    'po_invoice_no' => ($grn->purchaseInvoice->invoice_no ?? 'N/A') . (isset($grn->purchaseInvoice->purchaseOrder) ? ' <br><small class="text-primary fw-bold">PO: ' . $grn->purchaseInvoice->purchaseOrder->po_number . '</small>' : ''),
                    'supplier_name' => ($grn->supplier->name ?? 'N/A') . ' <span class="mini-title">(' . ($grn->supplier->code ?? '') . ')</span>',
                    'supplier_invoice_no' => $grn->purchaseInvoice->po_reference ?? 'N/A',
                    'total_items' => $grn->grnEntryItems->count(),
                    'amount' => '₹' . number_format($grn->grnEntryItems->sum('amount'), 2),
                    'qc_status' => '<span class="badge ' . $badgeClass . '">' . $finalQcStatus . '</span>',
                    'status' => $statusDisplay,
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
        $suppliers = \App\Models\Supplier::where('status', 'Active')->orderBy('name')->get();
        return view('grn_entry.view', compact('suppliers'));
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit grn-entry')) {
                return unauthorizedRedirect();
            }
            $grn = GrnEntry::with(['grnEntryItems.purchaseInvoiceItem.rawMaterial', 'grnEntryItems.purchaseInvoiceItem.purchaseOrderItem.color', 'grnEntryItems.variants.color', 'grnEntryItems.fabricType', 'grnEntryItems.storeLocation', 'grnEntryItems.color', 'purchaseInvoice.purchaseOrder'])->findOrFail($id);
            $purchaseInvoices = PurchaseInvoice::with('purchaseOrder')->whereHas('items', function ($query) use ($id) {
                $query->whereRaw('quantity > (SELECT IFNULL(SUM(qty_received), 0) FROM grn_entry_items WHERE grn_entry_items.purchase_invoice_item_id = purchase_invoice_items.id AND grn_entry_items.grn_entry_id != ? AND grn_entry_items.deleted_at IS NULL)', [$id]);
            })->orWhere('id', $grn->purchase_invoice_id)->orderBy('invoice_no')->get();
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create grn-entry')) {
                return unauthorizedRedirect();
            }
            $purchaseInvoices = PurchaseInvoice::with('purchaseOrder.storeType')->whereHas('items', function ($query) {
                $query->whereRaw('quantity > (SELECT IFNULL(SUM(qty_received), 0) FROM grn_entry_items WHERE grn_entry_items.purchase_invoice_item_id = purchase_invoice_items.id AND grn_entry_items.deleted_at IS NULL)');
            })->orderBy('invoice_no')->get();
        }
        $grn = $grn ?? null;

        $fabricTypes = FabricType::where('status', 'Active')->orderBy('fabric_type')->get();
        $storeLocations = StoreLocation::where('status', 'Active')->orderBy('store_location')->get();
        $colors = Color::where('status', 'Active')->orderBy('color_name')->get();

        if ($request->isMethod('post')) {
            if ($request->has('items')) {
                $items = $request->input('items');
                foreach ($items as $idx => $item) {
                    if (isset($item['color_id']) && $item['color_id'] === '') {
                        $items[$idx]['color_id'] = null;
                    }
                    if (isset($item['fabric_type_id']) && $item['fabric_type_id'] === '') {
                        $items[$idx]['fabric_type_id'] = null;
                    }
                }
                $request->merge(['items' => $items]);
            }

            $headerRules = [
                'grn_date' => 'required|date_format:d-m-Y',
                'purchase_invoice_id' => 'required|exists:purchase_invoices,id',
                'status' => 'required|in:Draft,Received,Partially Received,Invoiced,Cancelled',
            ];

            if ($request->purchase_invoice_id) {
                $headerRules['supplier_invoice_date'] = 'required|date_format:d-m-Y';
            }

            $messages = [
                '*.required' => 'This field is required',
                '*.numeric' => 'This field must be a number',
                '*.min' => 'This field must be at least :min',
                '*.max' => 'This field should not be more than :max characters.',
                '*.exists' => 'This field is invalid',
            ];

            $request->validate($headerRules, $messages);

            $selectedItems = collect($request->items)->filter(function ($item) {
                return ($item['row_selected'] ?? 0) == 1;
            });

            if ($selectedItems->isEmpty()) {
                return back()->withInput()->withErrors(['error' => 'Please select at least one item to proceed.']);
            }

            $rules = [
                'items' => 'required|array',
            ];

            $groupTotals = [];
            foreach ($request->items as $index => $item) {
                if (($item['row_selected'] ?? 0) == 1) {
                    $piItemId = $item['purchase_invoice_item_id'];
                    if (!isset($groupTotals[$piItemId])) {
                        $pi_item = PurchaseInvoiceItem::find($piItemId);
                        $alreadyReceived = GrnEntryItem::where('purchase_invoice_item_id', $piItemId)->where('grn_entry_id', '!=', $id ?? 0)->sum('qty_received');
                        $groupTotals[$piItemId] = [
                            'total_request' => 0,
                            'balance' => ($pi_item->quantity ?? 0) - $alreadyReceived,
                            'indices' => []
                        ];
                    }
                    $groupTotals[$piItemId]['total_request'] += $item['qty_received'] ?? 0;
                    $groupTotals[$piItemId]['indices'][] = $index;

                    // $rules["items.$index.art_no"] = [
                    //     'required',
                    //     function ($attribute, $value, $fail) use ($request) {
                    //         $allArtNos = collect($request->items)
                    //             ->filter(fn($item) => ($item['row_selected'] ?? 0) == 1 && !empty($item['art_no']))
                    //             ->pluck('art_no')
                    //             ->map(fn($art) => (string) $art)
                    //             ->toArray();
                                
                    //         $counts = array_count_values($allArtNos);
                    //         if (isset($counts[$value]) && $counts[$value] > 1) {
                    //             $fail('This article number is duplicated within this entry.');
                    //         }
                    //     }
                    // ];

                    $rules["items.$index.art_no"] = [
                        'required',
                        function ($attribute, $value, $fail) use ($request, $item, $id) {
                            $normalizedValue = trim((string) $value);
                            if ($normalizedValue === '') {
                                return;
                            }

                            $allArtNos = collect($request->items)
                                ->filter(fn($item) => ($item['row_selected'] ?? 0) == 1 && !empty($item['art_no']))
                                ->pluck('art_no')
                                ->map(fn($art) => trim((string) $art))
                                ->toArray();

                            $counts = array_count_values($allArtNos);
                            if (isset($counts[$normalizedValue]) && $counts[$normalizedValue] > 1) {
                                $fail('This article number is duplicated within this entry.');
                                return;
                            }

                            $existingArtNoQuery = GrnEntryItem::whereRaw('TRIM(art_no) = ?', [$normalizedValue])
                                ->whereNull('deleted_at');

                            $currentRowId = $item['id'] ?? null;
                            if ($currentRowId) {
                                $existingArtNoQuery->where('id', '!=', $currentRowId);
                            } elseif ($id) {
                                $existingArtNoQuery->where('grn_entry_id', '!=', $id);
                            }

                            if ($existingArtNoQuery->exists()) {
                                $fail('Art No already used.');
                            }
                        }
                    ];
                    $rules["items.$index.qty_received"] = 'required|numeric|gt:0';
                    $rules["items.$index.qty_accepted"] = 'required|numeric|min:0';
                    $rules["items.$index.quality_check_status"] = 'required|in:Pass,Fail,Hold';
                    $rules["items.$index.store_location_id"] = 'required|exists:store_locations,id';
                    $rules["items.$index.fabric_type_id"] = 'nullable|exists:fabric_types,id';
                    $rules["items.$index.color_id"] = 'nullable|exists:colors,id';
                    $rules["items.$index.item_image"] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
                }
            }

            foreach ($groupTotals as $piId => $data) {
                if ($data['total_request'] > $data['balance']) {
                    $bal = $data['balance'];
                    foreach ($data['indices'] as $idx) {
                        $rules["items.$idx.qty_received"] = "required|numeric|lte:$bal";
                    }
                }
            }

            $messages = array_merge($messages, [
                'items.*.art_no.required' => 'This field is required',
                'items.*.qty_received.required' => 'This field is required',
                'items.*.qty_received.gt' => 'This field is required',
                'items.*.qty_received.min' => 'This field is required',
                'items.*.qty_accepted.required' => 'This field is required',
                'items.*.quality_check_status.required' => 'This field is required',
                'items.*.store_location_id.required' => 'This field is required',
                'items.*.item_image.image' => 'File must be an image.',
                'items.*.item_image.mimes' => 'Upload a valid file (e.g., .jpg, .png, .jpeg, .webp).',
                'items.*.item_image.max' => 'Uploaded file cannot exceed 2MB.',
            ]);

            $request->validate($rules, $messages);

            DB::beginTransaction();
            try {
                $invoice = PurchaseInvoice::findOrFail($request->purchase_invoice_id);
                $headerData = [
                    'grn_date' => Carbon::createFromFormat('d-m-Y', $request->grn_date)->format('Y-m-d'),
                    'purchase_invoice_id' => $request->purchase_invoice_id,
                    'supplier_id' => $invoice->supplier_id,
                    'supplier_invoice_date' => Carbon::createFromFormat('d-m-Y', $request->supplier_invoice_date)->format('Y-m-d'),
                    'status' => $request->status,
                ];

                if ($id) {
                    $oldData = GrnEntry::find($id)->toArray();
                    $grn = GrnEntry::findOrFail($id);
                    $headerData['updated_by'] = auth()->id();
                    $grn->update($headerData);
                    $newData = $grn->fresh()->toArray();
                    addLog('update', 'GRN Entry', 'grn_entries', $id, $oldData, $newData);
                } else {
                    $lastGrn = GrnEntry::latest('id')->first();
                    $nextNumber = $lastGrn ? (int) substr($lastGrn->grn_number, 3) + 1 : 1;
                    $headerData['grn_number'] = 'GRN' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                    $headerData['created_by'] = auth()->id();
                    $grn = GrnEntry::create($headerData);
                    $newData = $grn->toArray();
                    addLog('create', 'GRN Entry', 'grn_entries', $grn->id, null, $newData);
                }
                $processedItemIds = [];
                foreach ($request->items as $idx => $itemData) {
                    if (($itemData['row_selected'] ?? 0) == 0)
                        continue;

                    $imagePath = $itemData['old_image'] ?? null;
                    if ($request->hasFile("items.$idx.item_image")) {
                        $file = $request->file("items.$idx.item_image");
                        $filename = 'grn_item_' . time() . '_' . $idx . '.' . $file->getClientOriginalExtension();
                        $uploadPath = public_path('uploads/grn_items');
                        if (!file_exists($uploadPath)) {
                            mkdir($uploadPath, 0755, true);
                        }
                        $file->move($uploadPath, $filename);
                        $imagePath = $filename;
                    }

                    $piItemId = $itemData['purchase_invoice_item_id'] ?? null;
                    $rowId = $itemData['id'] ?? null;

                    $item = null;
                    if ($rowId) {
                        $item = GrnEntryItem::where('grn_entry_id', $grn->id)->where('id', $rowId)->first();
                    }

                    if ($item) {
                        $updateData = [
                            'art_no' => $itemData['art_no'] ?? null,
                            'fabric_type_id' => !empty($itemData['fabric_type_id']) ? $itemData['fabric_type_id'] : null,
                            'color_id' => !empty($itemData['color_id']) ? $itemData['color_id'] : null,
                            'qty_ordered' => $itemData['qty_ordered'] ?? 0,
                            'qty_received' => $itemData['qty_received'] ?? 0,
                            'qty_accepted' => $itemData['qty_accepted'] ?? 0,
                            'qty_rejected' => $itemData['qty_rejected'] ?? 0,
                            'qty_balanced' => $itemData['qty_balanced'] ?? 0,
                            'rate' => $itemData['rate'] ?? 0,
                            'amount' => $itemData['amount'] ?? 0,
                            'quality_check_status' => $itemData['quality_check_status'] ?? null,
                            'store_location_id' => $itemData['store_location_id'] ?? null,
                        ];
                        if ($imagePath !== null) {
                            $updateData['image'] = $imagePath;
                        }
                        $item->update($updateData);
                    } else {
                        $item = GrnEntryItem::create([
                            'grn_entry_id' => $grn->id,
                            'purchase_invoice_item_id' => $piItemId,
                            'art_no' => $itemData['art_no'] ?? null,
                            'fabric_type_id' => !empty($itemData['fabric_type_id']) ? $itemData['fabric_type_id'] : null,
                            'color_id' => !empty($itemData['color_id']) ? $itemData['color_id'] : null,
                            'qty_ordered' => $itemData['qty_ordered'] ?? 0,
                            'qty_received' => $itemData['qty_received'] ?? 0,
                            'qty_accepted' => $itemData['qty_accepted'] ?? 0,
                            'qty_rejected' => $itemData['qty_rejected'] ?? 0,
                            'qty_balanced' => $itemData['qty_balanced'] ?? 0,
                            'rate' => $itemData['rate'] ?? 0,
                            'amount' => $itemData['amount'] ?? 0,
                            'quality_check_status' => $itemData['quality_check_status'] ?? null,
                            'store_location_id' => $itemData['store_location_id'] ?? null,
                            'image' => $imagePath,
                        ]);
                    }

                    $processedItemIds[] = $item->id;

                    if (isset($itemData['variants']) && is_array($itemData['variants'])) {
                        $item->variants()->delete();
                        foreach ($itemData['variants'] as $v) {
                            if (($v['qty'] ?? 0) > 0) {
                                GrnEntryItemVariant::create([
                                    'grn_entry_item_id' => $item->id,
                                    'color_id' => $v['color_id'],
                                    'qty_received' => $v['qty'],
                                ]);
                            }
                        }
                    }
                }

                if ($id) {
                    GrnEntryItem::where('grn_entry_id', $grn->id)->whereNotIn('id', $processedItemIds)->delete();
                }

                DB::commit();

                if (isset($invoice->purchase_order_id)) {
                    $poId = $invoice->purchase_order_id;
                    $po = PurchaseOrder::with('items')->find($poId);
                    if ($po) {
                        $isFullyReceived = true;
                        foreach ($po->items as $poItem) {
                            $totalReceived = GrnEntryItem::whereHas('purchaseInvoiceItem', function ($q) use ($poItem) {
                                $q->where('purchase_order_item_id', $poItem->id);
                            })->sum('qty_received');

                            if ($totalReceived < $poItem->quantity) {
                                $isFullyReceived = false;
                                break;
                            }
                        }

                        if ($isFullyReceived && $po->status != 'Received') {
                            $po->update(['status' => 'Received']);
                            addLog('update', 'Purchase Order Status (Auto)', 'purchase_orders', $po->id, ['status' => 'Approved'], ['status' => 'Received']);
                        }
                    }
                }

                $message = $id ? 'GRN Entry updated successfully' : 'GRN Entry saved successfully';
                return redirect('grn_entries')->with('success', $message);
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->withErrors(['error' => $e->getMessage()]);
            }
        }

        $nextGrnNo = $id ? $grn->grn_number : 'GRN' . str_pad((GrnEntry::latest('id')->first()->id ?? 0) + 1, 3, '0', STR_PAD_LEFT);

        return view('grn_entry.add', compact('grn', 'purchaseInvoices', 'fabricTypes', 'storeLocations', 'colors', 'nextGrnNo'));
    }

    public function getInvoiceDetails($id)
    {
        $invoice = PurchaseInvoice::with(['purchaseOrder.storeType', 'supplier', 'items.rawMaterial', 'items.uom', 'items.purchaseOrderItem.brand', 'items.purchaseOrderItem.color'])->findOrFail($id);

        $brandSequences = [];

        $items = $invoice->items()->get()->map(function ($item) use (&$brandSequences) {
            $already_received = GrnEntryItem::where('purchase_invoice_item_id', $item->id)->sum('qty_received');

            $art_no = $item->purchaseOrderItem->art_no ?? '';
            $store_category_id = $item->rawMaterial->store_category_id ?? 0;

            if ($store_category_id == 2) {
                $brand = $item->purchaseOrderItem->brand->code ?? 'ACC';
                if (!isset($brandSequences[$brand])) {
                    $latestItem = GrnEntryItem::where('art_no', 'like', $brand . '-%')->whereNull('deleted_at')->orderByRaw('CAST(SUBSTRING_INDEX(art_no, "-", -1) AS UNSIGNED) DESC')->first();

                    if ($latestItem && preg_match('/-(\d+)$/', $latestItem->art_no, $matches)) {
                        $brandSequences[$brand] = intval($matches[1]) + 1;
                    } else {
                        $brandSequences[$brand] = 1001;
                    }
                }

                if (empty($art_no)) {
                    $art_no = $brand . '-' . $brandSequences[$brand];
                    $brandSequences[$brand]++;
                }
            }

            if ($store_category_id == 1 && $item->purchaseOrderItem && strval($item->purchaseOrderItem->supplier_design_name) !== '') {
                $design_name = $item->purchaseOrderItem->supplier_design_name;
            } else {
                $design_name = ($item->rawMaterial->name ?? '') . '(' . ($item->rawMaterial->code ?? '') . ')';
            }

            return [
                'id' => $item->id,
                'design_name' => $design_name,
                'art_no' => $art_no,
                'width' => $item->purchaseOrderItem->fabricWidth->width ?? '-',
                'uom' => $item->uom->uom_code ?? 'MTR',
                'qty_ordered' => $item->quantity,
                'qty_already_received' => $already_received,
                'rate' => $item->rate,
                'amount' => $item->amount,
                'store_category_id' => $store_category_id,
                'fabric_type_id' => $item->purchaseOrderItem->fabric_type_id ?? '',
                'color_id' => $item->purchaseOrderItem->color_id ?? null,
                'color_name' => $item->purchaseOrderItem->color->color_name ?? '',
            ];
        })->filter(function ($item) {
            return ($item['qty_ordered'] - $item['qty_already_received']) > 0;
        })->values();

        return response()->json([
            'success' => true,
            'supplier_id' => $invoice->supplier_id,
            'supplier_name' => ($invoice->supplier->name ?? 'N/A') . ($invoice->supplier->code ? ' (' . $invoice->supplier->code . ')' : ''),
            'invoice_date' => $invoice->invoice_date->format('d-m-Y'),
            'store_type_name' => $invoice->purchaseOrder->storeType->store_type_name ?? '',
            'po_number' => $invoice->purchaseOrder->po_number ?? '',
            'items' => $items
        ]);
    }

    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view grn-entry')) {
            return unauthorizedRedirect();
        }
        $grn = GrnEntry::with([
            'purchaseInvoice',
            'supplier',
            'grnEntryItems.purchaseInvoiceItem.rawMaterial',
            'grnEntryItems.purchaseInvoiceItem.uom',
            'grnEntryItems.fabricType',
            'grnEntryItems.storeLocation',
            'grnEntryItems.color',
            'grnEntryItems.variants.color'
        ])->findOrFail($id);

        return view('grn_entry.view_details', compact('grn'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete grn-entry')) {
            return unauthorizedRedirect();
        }
        $grn = GrnEntry::findOrFail($id);
        $oldData = $grn->toArray();
        $grn->grnEntryItems()->each(function ($item) {
            $item->variants()->delete();
            $item->delete();
        });
        $grn->delete();
        addLog('delete', 'GRN Entry', 'grn_entries', $id, $oldData, null);

        return redirect('grn_entries')->with('success', 'GRN Entry deleted successfully');
    }

    public function downloadPdf($id)
    {
        $grn = GrnEntry::with([
            'purchaseInvoice.purchaseOrder',
            'supplier.city',
            'grnEntryItems.purchaseInvoiceItem.rawMaterial',
            'grnEntryItems.purchaseInvoiceItem.uom',
            'grnEntryItems.fabricType',
            'grnEntryItems.storeLocation',
            'grnEntryItems.color',
            'grnEntryItems.variants.color',
            'grnEntryItems.purchaseInvoiceItem.purchaseOrderItem.brand',
            'grnEntryItems.purchaseInvoiceItem.purchaseOrderItem.style',
            'grnEntryItems.purchaseInvoiceItem.purchaseOrderItem.color',
            'grnEntryItems.purchaseInvoiceItem.purchaseOrderItem.fabricWidth'
        ])->findOrFail($id);

        $setting = \App\Models\Setting::with(['state', 'city'])->first();
        $pdf = Pdf::loadView('grn_entry.grn_pdf', compact('grn', 'setting'));
        $pdf->setPaper('A4', 'landscape');
        $safeGrnNumber = str_replace(['/', '\\'], '_', $grn->grn_number);
        return $pdf->stream('GRN_' . $safeGrnNumber . '.pdf');
    }

    public function print($id)
    {
        $grn = GrnEntry::with([
            'purchaseInvoice.purchaseOrder',
            'supplier.city',
            'grnEntryItems.purchaseInvoiceItem.rawMaterial',
            'grnEntryItems.purchaseInvoiceItem.uom',
            'grnEntryItems.fabricType',
            'grnEntryItems.storeLocation',
            'grnEntryItems.color',
            'grnEntryItems.variants.color',
            'grnEntryItems.purchaseInvoiceItem.purchaseOrderItem.brand',
            'grnEntryItems.purchaseInvoiceItem.purchaseOrderItem.style',
            'grnEntryItems.purchaseInvoiceItem.purchaseOrderItem.color',
            'grnEntryItems.purchaseInvoiceItem.purchaseOrderItem.fabricWidth'
        ])->findOrFail($id);

        $setting = \App\Models\Setting::with(['state', 'city'])->first();
        $is_print = true;
        return view('grn_entry.grn_pdf', compact('grn', 'is_print', 'setting'));
    }
}

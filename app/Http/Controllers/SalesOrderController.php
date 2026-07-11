<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\SalesOrderCharge;
use App\Models\StockEntryItem;
use App\Models\Season;
use App\Models\Customer;
use App\Models\StoreType;
use App\Models\SalesAgent;
use App\Models\ShippingMethod;
use App\Models\TransportMode;
use App\Models\ServiceProvider;
use App\Models\BrandCategory;
use App\Models\Item;
use App\Models\Color;
use App\Models\Uom;
use App\Models\SizeRatio;
use App\Models\Setting;
use App\Models\Zone;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\OrderaxeService;

class SalesOrderController extends Controller
{
    public function fixOldItems(Request $request)
    {
        $items = \App\Models\SalesOrderItem::all();
        $changes = [];

        foreach ($items as $item) {
            $oldItemName = $item->item_name;
            $oldArtNo = $item->art_no;
            
            $newItemName = $oldItemName;
            $newArtNo = $oldArtNo;

            $barcode = $item->sku;
            $size = $item->size_id;
            
            $stockEntryItemQuery = \App\Models\StockEntryItem::where(function ($q) use ($barcode) {
                $q->where('sku', $barcode)
                  ->orWhere('barcode', $barcode);
            })->where('stock_type', 'finished_goods');

            if ($size) {
                $stockEntryItemQuery->where('size', $size);
            }
            
            $stockEntryItem = $stockEntryItemQuery->first();

            if ($stockEntryItem) {
                $newArtNo = $stockEntryItem->art_no;
            } else {
                if ($oldItemName) {
                    $newArtNo = strtoupper(trim(str_ireplace('Aero Cut', '', $oldItemName)));
                } else {
                    $newArtNo = null;
                }
            }

            if ($oldItemName !== $newItemName || $oldArtNo !== $newArtNo) {
                $changes[] = [
                    'order_id' => $item->sale_order_id,
                    'sku' => $item->sku,
                    'old_item_name' => $oldItemName,
                    'new_item_name' => $newItemName,
                    'old_art_no' => $oldArtNo,
                    'new_art_no' => $newArtNo,
                    'item_model' => $item
                ];
            }
        }

        if ($request->has('confirm')) {
            \Illuminate\Support\Facades\DB::beginTransaction();
            try {
                foreach ($changes as $change) {
                    $item = $change['item_model'];
                    $item->item_name = $change['new_item_name'];
                    $item->art_no = $change['new_art_no'];
                    $item->save();
                }
                \Illuminate\Support\Facades\DB::commit();
                return response()->json(['success' => true, 'message' => count($changes) . ' items updated successfully!']);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\DB::rollBack();
                return response()->json(['success' => false, 'error' => $e->getMessage()]);
            }
        }

        // Remove the eloquent model from response so it displays nicely
        $preview = array_map(function($c) {
            unset($c['item_model']);
            return $c;
        }, $changes);

        return response()->json([
            'message' => 'This is a preview. ' . count($changes) . ' items will be updated. Append ?confirm=1 to the URL to apply these changes.',
            'preview_changes' => $preview
        ]);
    }

    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view sales-order')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $query = SalesOrder::with(['customer', 'salesAgent'])->orderBy('id', 'desc');

            if (!empty($request->customer_id)) {
                $query->where('customer_id', $request->customer_id);
            }

            if (!empty($request->status)) {
                $query->where('status', $request->status);
            }

            if (!empty($request->so_date_range)) {
                $dates = explode(' to ', $request->so_date_range);
                if (count($dates) == 2) {
                    $startDate = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                    $query->whereBetween('so_date', [$startDate, $endDate]);
                }
                elseif (count($dates) == 1 && trim($dates[0]) !== '') {
                    $startDate = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $query->whereDate('so_date', $startDate);
                }
            }

            $salesOrders = $query->get();
            $data = [];
            $count = 1;

            foreach ($salesOrders as $so) {
                $statusOptions = '';
                $allStatuses = ['Draft', 'Pending', 'Approved', 'Rejected', 'Dispatched', 'Cancelled'];
                foreach ($allStatuses as $status) {
                    $selected = $so->status === $status ? 'selected' : '';
                    $disabled = '';

                    if ($so->status === 'Draft') {
                        if (!in_array($status, ['Draft', 'Pending', 'Approved', 'Rejected'])) $disabled = 'disabled';
                    } elseif ($so->status === 'Pending') {
                        if (!in_array($status, ['Pending', 'Approved', 'Rejected', 'Draft'])) $disabled = 'disabled';
                    } elseif ($so->status === 'Approved') {
                        if (!in_array($status, ['Approved', 'Cancelled', 'Dispatched'])) $disabled = 'disabled';
                    } elseif ($so->status === 'Rejected') {
                        if (!in_array($status, ['Rejected', 'Draft'])) $disabled = 'disabled';
                    } elseif ($so->status === 'Cancelled' || $so->status === 'Dispatched') {
                        if ($status !== $so->status) $disabled = 'disabled';
                    }

                    $statusOptions .= "<option value=\"{$status}\" {$selected} {$disabled}>{$status}</option>";
                }

                $statusDropdown = '
                <div class="form-floating form-floating-outline">
                    <select class="form-select so-status-change" data-id="' . $so->id . '" ' . ($so->status === 'Cancelled' ? 'disabled' : '') . '>
                        ' . $statusOptions . '
                    </select>
                </div>
                <div class="status_msg_' . $so->id . ' mt-1"></div>';

                $action = '<div class="button-box">';
                if (auth()->id() == 1 || auth()->user()->can('view_details sales-order')) {
                    $action .= '<a href="' . url('sales_orders/view/' . $so->id) . '" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>';
                }
                if ($so->status == 'Draft' || $so->status == 'Pending') {
                    if (auth()->id() == 1 || auth()->user()->can('edit sales-order')) {
                        $action .= '<a href="' . url('sales_orders/add/' . $so->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                    }
                }
                // if (auth()->id() == 1 || auth()->user()->can('delete sales-order')) {
                //     $action .= '<a href="' . url('sales_orders/delete/' . $so->id) . '" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                // }
                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'so_no' => $so->so_no . ($so->order_no ? '<br><span class="badge bg-label-info mt-1" style="font-size:10px;">' . $so->order_no . '</span>' : '') . ($so->orderaxe_id
                        ? '<br><span class="badge bg-label-warning mt-1" style="font-size:10px;"><i class="ri ri-refresh-line me-1"></i>Orderaxe</span>'
                        : ''),
                    'so_date' => $so->so_date ? $so->so_date->format('d-m-Y') : '-',
                    'customer_name' => $so->customer ? ($so->customer->name . ' <span class="mini-title">(' . $so->customer->code . ')</span>') : '-',
                    'customer_po_ref' => $so->customer_po_ref ?? '-',
                    'total_qty' => number_format($so->total_qty, 2),
                    'sales_agent' => $so->salesAgent ? $so->salesAgent->name : '-',
                    'status' => $statusDropdown,
                    'status_text' => $so->status,
                    'total_amount' => '₹' . number_format($so->total_amount, 2),
                    'action' => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('sales_order.view');
    }

    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit sales-order')) {
                return unauthorizedRedirect();
            }
        }
        else {
            if (auth()->id() != 1 && !auth()->user()->can('create sales-order')) {
                return unauthorizedRedirect();
            }
        }

        $salesOrder = null;
        $charges = collect();
        if ($id) {
            $salesOrder = SalesOrder::with(['items', 'charges'])->findOrFail($id);
            if ($salesOrder->status !== 'Draft' && $salesOrder->status !== 'Pending') {
                return redirect('sales_orders')->with('error', 'Only Draft or Pending Sales Orders can be edited.');
            }
            $charges = $salesOrder->charges;
        }

        if (request()->isMethod('post')) {
            $request = request();

            $rules = [
                'so_no' => 'required|string|min:3|max:50|unique:sales_orders,so_no,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'so_date' => 'required|date',
                'request_date' => 'nullable|date',
                'delivery_date' => 'nullable|date',
                'order_type' => 'required|string|max:50',
                'season_id' => 'nullable|exists:seasons,id',
                'customer_id' => 'required|exists:customers,id',
                'customer_po_ref' => 'nullable|string|min:3|max:50',
                'store_id' => 'required|exists:store_types,id',
                'agent_id' => 'nullable|exists:sales_agents,id',
                'zone_id' => 'nullable|exists:zones,id',
                'shipping_method_id' => 'nullable|exists:shipping_methods,id',
                'transport_mode_id' => 'nullable|exists:transport_modes,id',
                'dispatch_from_id' => 'nullable|exists:service_providers,id',
                'status' => 'required|in:Draft,Approved,Rejected,Pending,Dispatched,Cancelled',
                'items' => 'required|array|min:1',
                'items.*.color_id' => 'nullable|exists:colors,id',
                'items.*.art_no' => 'nullable|string|max:50',
                'items.*.uom_id' => 'required|string',
                'items.*.size_id' => 'required',
                'items.*.qty' => 'required|numeric|min:0.01',
                'items.*.rate' => 'nullable|numeric|min:0',
                'items.*.mrp' => 'required|numeric|min:0',
                'commission_percent' => 'nullable|numeric|min:0',
                'commission_amount' => 'nullable|numeric|min:0',
                'sales_discount_percent' => 'nullable|numeric|min:0|max:100',
                'box_discount_amount' => 'nullable|numeric|min:0',
                'round_off_type' => 'nullable|in:Add,Less',
                'round_off' => 'nullable|numeric|min:0',
                'apply_box_discount' => 'nullable|boolean',
                'attachment' => 'nullable|array',
                'attachment.*' => 'nullable|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:2048',
                'existing_attachments' => 'nullable|array',
                'billing_address' => 'nullable|string|regex:/^[^<>]*$/',
                'shipping_address' => 'nullable|string|regex:/^[^<>]*$/',
                'payment_terms' => 'nullable|string|max:255|regex:/^[^<>]*$/',
                'transporter_name' => 'nullable|string|max:50',
                //'freight_type' => 'nullable|in:Paid,To Pay',
                //'freight_amount' => 'nullable|numeric|min:0',
                'terms_conditions' => 'nullable|string|regex:/^[^<>]*$/',
            ];

            $messages = [
                '*.required' => 'This field is required.',
                '*.exists' => 'Selected value is invalid.',
                '*.unique' => 'SO Number already exists.',
                '*.date' => 'Please enter a valid date.',
                '*.numeric' => 'This field must be a number.',
                '*.regex' => 'This field is an invalid format.',
                'items.required' => 'At least one item is required.',
                'items.*.brand_cat_id.required' => 'This field is required.',
                'items.*.item_id.required' => 'This field is required.',
                'items.*.uom_id.required' => 'This field is required.',
                'items.*.size_id.required' => 'This field is required.',
                'items.*.qty.required' => 'This field is required.',
                'items.*.rate.nullable' => 'This field is optional.',
                'items.*.art_no.required' => 'This field is required.',
                'items.*.mrp.required' => 'This field is required.',
                'attachment.*.mimes' => 'Upload a valid file (e.g., .pdf, .doc, .docx, .jpg, .png, .jpeg, .webp).',
                'attachment.*.max' => 'Uploaded file cannot exceed 2MB.',
            ];

            $request->validate($rules, $messages);

            if ($id && $salesOrder->status !== $request->status) {
                $oldStatus = $salesOrder->status;
                $newStatus = $request->status;
                $valid = true;
                if ($oldStatus === 'Draft') {
                    if (!in_array($newStatus, ['Pending', 'Approved', 'Rejected', 'Draft'])) $valid = false;
                } elseif ($oldStatus === 'Pending') {
                    if (!in_array($newStatus, ['Approved', 'Rejected', 'Draft', 'Pending'])) $valid = false;
                } elseif ($oldStatus === 'Approved') {
                    if (!in_array($newStatus, ['Cancelled', 'Dispatched', 'Approved'])) $valid = false;
                } elseif ($oldStatus === 'Rejected') {
                    if (!in_array($newStatus, ['Draft', 'Rejected'])) $valid = false;
                } elseif (in_array($oldStatus, ['Cancelled', 'Dispatched'])) {
                    if ($newStatus !== $oldStatus) $valid = false;
                }

                if (!$valid) {
                    return back()->with('error', 'Invalid status transition from ' . $oldStatus . ' to ' . $newStatus)->withInput();
                }
            }

            DB::beginTransaction();
            try {
                $taxableAmount = $request->taxable_amount ?? 0;
                $taxAmount = $request->tax_amount ?? 0;
                $otherCharges = $request->other_charges ?? 0;
                $roundOffAmount = $request->round_off ?? 0;
                $roundOffType = $request->round_off_type ?? 'Add';
                $finalTotal = $taxableAmount + $taxAmount + $otherCharges;
                if ($roundOffType === 'Add') {
                    $finalTotal += $roundOffAmount;
                }
                else {
                    $finalTotal -= $roundOffAmount;
                }

                $agentCommValue = 0;
                if ($request->agent_id && $request->order_type !== 'Discount Order') {
                    $agent = \App\Models\SalesAgent::find($request->agent_id);
                    if ($agent) {
                        $agentCommValue = (float) ($agent->commission_value ?? 0);
                    }
                }
                $totalQty = $request->total_qty ?? 0;
                $commAmount = $totalQty * $agentCommValue;

                $soData = [
                    'so_no' => $request->so_no,
                    'so_date' => Carbon::createFromFormat('d-m-Y', $request->so_date)->format('Y-m-d'),
                    'request_date' => $request->request_date ?Carbon::createFromFormat('d-m-Y', $request->request_date)->format('Y-m-d') : null,
                    'delivery_date' => $request->delivery_date ?Carbon::createFromFormat('d-m-Y', $request->delivery_date)->format('Y-m-d') : null,
                    'order_type' => $request->order_type,
                    'season_id' => $request->season_id,
                    'customer_id' => $request->customer_id,
                    'customer_po_ref' => $request->customer_po_ref,
                    'store_id' => $request->store_id,
                    'agent_id' => $request->agent_id,
                    'zone_id' => $request->zone_id,
                    'shipping_method_id' => $request->shipping_method_id,
                    'transport_mode_id' => $request->transport_mode_id,
                    'dispatch_from_id' => $request->dispatch_from_id,
                    'status' => $request->status,
                    'total_qty' => $totalQty,
                    'sub_total_qty' => $request->sub_total_qty ?? 0,
                    'commission_percent' => $agentCommValue,
                    'commission_amount' => $commAmount,
                    'sales_discount_percent' => $request->sales_discount_percent ?? 0,
                    'box_discount_amount' => $request->box_discount_amount ?? 0,
                    'discount_amount' => $request->discount_amount ?? 0,
                    'taxable_amount' => $taxableAmount,
                    'other_state' => $request->other_state === 'yes',
                    'igst_percent' => $request->igst_percent ?? 0,
                    'cgst_percent' => $request->cgst_percent ?? 0,
                    'sgst_percent' => $request->sgst_percent ?? 0,
                    'tax_amount' => $taxAmount,
                    'other_charges' => $request->other_charges ?? 0,
                    'round_off_type' => $roundOffType,
                    'round_off' => $roundOffAmount,
                    'total_amount' => $finalTotal,
                    'internal_remarks' => $request->internal_remarks,
                    'billing_address' => $request->billing_address,
                    'shipping_address' => $request->shipping_address,
                    'payment_terms' => $request->payment_terms,
                    'transporter_name' => $request->transporter_name,
                    //'freight_type' => $request->freight_type,
                    //'freight_amount' => $request->freight_amount ?? 0,
                    'terms_conditions' => $request->terms_conditions,
                    'apply_box_discount' => $request->apply_box_discount == '1',
                ];

                $isNewApproval = false;
                if ($request->status === 'Approved') {
                    if (!$id || SalesOrder::find($id)->status !== 'Approved') {
                        $soData['approved_by'] = auth()->id();
                        $soData['approved_date'] = now();
                        $isNewApproval = true;
                    }
                }
                else {
                    $soData['approved_by'] = null;
                    $soData['approved_date'] = null;
                }

                if ($id) {
                    $salesOrder = SalesOrder::findOrFail($id);
                    $soData['updated_by'] = auth()->id();
                    $salesOrder->update($soData);
                    SalesOrderItem::where('sale_order_id', $id)->forceDelete();
                    SalesOrderCharge::where('sales_order_id', $id)->forceDelete();
                    $message = 'Sale Order updated successfully';
                }
                else {
                    $soData['created_by'] = auth()->id();
                    $salesOrder = SalesOrder::create($soData);
                    $message = 'Sale Order created successfully';
                }

                $attachments = $request->existing_attachments ?? [];

                if ($request->hasFile('attachment')) {
                    $uploadPath = public_path('uploads/so/' . $salesOrder->id);
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }

                    foreach ($request->file('attachment') as $file) {
                        $fileName = 'attach_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move($uploadPath, $fileName);
                        $attachments[] = $fileName;
                    }
                }

                if ($id) {
                    $oldAttachments = !empty($salesOrder->attachment) ? explode(',', $salesOrder->attachment) : [];
                    foreach ($oldAttachments as $oldFile) {
                        if (!in_array($oldFile, $attachments)) {
                            $filePath = public_path('uploads/so/' . $salesOrder->id . '/' . $oldFile);
                            if (file_exists($filePath)) {
                                @unlink($filePath);
                            }
                        }
                    }
                }

                $salesOrder->update(['attachment' => !empty($attachments) ? implode(',', $attachments) : null]);



                foreach ($request->items as $item) {
                    $itemName = null;
                    if (!empty($item['stock_entry_item_id'])) {
                        $seItem = \App\Models\StockEntryItem::find($item['stock_entry_item_id']);
                        if ($seItem) {
                            $itemName = $seItem->finished_item_code;
                        }
                    }
                    if (empty($itemName) && !empty($item['sku'])) {
                        $seItem = \App\Models\StockEntryItem::where('sku', $item['sku'])
                            ->orWhere('barcode', $item['sku'])
                            ->first();
                        if ($seItem) {
                            $itemName = $seItem->finished_item_code;
                        }
                    }
                    if (empty($itemName)) {
                        $itemName = $item['stock_item_key'] ?? $item['sku'] ?? null;
                        if ($itemName && strpos($itemName, ' - ') !== false) {
                            $itemName = explode(' - ', $itemName)[0];
                        }
                    }

                    $seItem = null;
                    if (!empty($item['stock_entry_item_id'])) {
                        $seItem = \App\Models\StockEntryItem::find($item['stock_entry_item_id']);
                    }
                    if (!$seItem && !empty($item['sku'])) {
                        $seItem = \App\Models\StockEntryItem::where('sku', $item['sku'])->orWhere('barcode', $item['sku'])->first();
                    }
                    $styleId = $seItem ? $seItem->style_id : null;
            
                    $categoryName = null;
                    $categoriesPathVal = null;

                    if ($styleId) {
                        $styleModel = \App\Models\Style::find($styleId);
                        if ($styleModel) {
                            $baseCategory = $styleModel->style_name;
                            $sleeveSuffix = '';
                            $itemNameUpper = strtoupper($itemName);
                            if (str_ends_with($itemNameUpper, 'F/S') || str_ends_with($itemNameUpper, ' FS') || str_ends_with($itemNameUpper, ' FULL')) {
                                $sleeveSuffix = ' F/s';
                            } elseif (str_ends_with($itemNameUpper, 'H/S') || str_ends_with($itemNameUpper, ' HS') || str_ends_with($itemNameUpper, ' HALF')) {
                                $sleeveSuffix = ' H/s';
                            } else {
                                $sleeveVal = null;
                                if (isset($item['sleeve'])) {
                                    $sleeveVal = is_array($item['sleeve']) ? ($item['sleeve'][0] ?? null) : $item['sleeve'];
                                }
                                if (empty($sleeveVal) && $seItem && !empty($seItem->sleeve_type)) {
                                    $sleeveVal = $seItem->sleeve_type;
                                }
                                if (!empty($sleeveVal)) {
                                    $sleeveUpper = strtoupper(trim($sleeveVal));
                                    if ($sleeveUpper === 'FULL' || $sleeveUpper === 'F/S' || $sleeveUpper === 'FS') {
                                        $sleeveSuffix = ' F/s';
                                    } elseif ($sleeveUpper === 'HALF' || $sleeveUpper === 'H/S' || $sleeveUpper === 'HS') {
                                        $sleeveSuffix = ' H/s';
                                    }
                                }
                            }

                            $categoryName = trim($baseCategory . $sleeveSuffix);
                            $categoriesPathVal = "Core > " . $categoryName;
                        }
                    }

                    $apiColor = $item['api_color'] ?? null;
                    if (empty($apiColor)) {
                        $artNo = $item['art_no'] ?? '';
                        if (!empty($artNo) && strpos($artNo, '-') !== false) {
                            $parts = explode('-', $artNo);
                            $lastPart = trim(end($parts));
                            if (is_numeric($lastPart)) {
                                $apiColor = $lastPart;
                            }
                        }
                    }
                    if (empty($apiColor)) {
                        if (!empty($itemName) && strpos($itemName, '-') !== false) {
                            $parts = explode('-', $itemName);
                            $lastPart = trim(end($parts));
                            if (is_numeric($lastPart)) {
                                $apiColor = $lastPart;
                            }
                        }
                    }
                    SalesOrderItem::create([
                        'sale_order_id' => $salesOrder->id,
                        'brand_cat_id' => $item['brand_cat_id'] ?? null,
                        'category_name' => $categoryName,
                        'categories_path_val' => $categoriesPathVal,
                        'item_id' => $item['item_id'],
                        'color_id' => $item['color_id'] ?? null,
                        'api_color' => $apiColor,
                        'art_no' => $item['art_no'] ?? null,
                        'item_name' => $itemName,
                        'uom_id' => $item['uom_id'],
                        'size_id' => $item['size_id'] ?? null,
                        'qty' => $item['qty'],
                        'rate' => $item['rate'] ?? 0,
                        'mrp' => $item['mrp'] ?? 0,
                        'commission_percent' => $agentCommValue,
                        'commission_amount'  => ($item['qty'] * $agentCommValue),
                        'amount' => $item['qty'] * ($item['rate'] ?? $item['mrp'] ?? 0),
                        'sleeve' => isset($item['sleeve']) ? (is_array($item['sleeve']) ? $item['sleeve'] : [$item['sleeve']]) : null,
                        'stock_entry_item_id' => !empty($item['stock_entry_item_id']) ? (int)$item['stock_entry_item_id'] : null,
                        'sku' => $item['sku'] ?? null,
                    ]);
                }

                if ($request->has('charges') && isset($request->charges['charge_id'])) {
                    $chargeIds = $request->charges['charge_id'];
                    $chargeNames = $request->charges['name'];
                    $chargeAmounts = $request->charges['amount'];
                    $taxTypes = $request->charges['tax_type'] ?? [];

                    foreach ($chargeIds as $index => $chargeId) {
                        SalesOrderCharge::create([
                            'sales_order_id' => $salesOrder->id,
                            'charge_id' => $chargeId,
                            'charge_name' => $chargeNames[$index],
                            'charge_amount' => $chargeAmounts[$index],
                            'tax_type' => $taxTypes[$index] ?? 'Post-GST',
                        ]);
                    }
                }

                DB::commit();


                addLog($id ? 'update' : 'create', 'Sale Order', 'sales_orders', $salesOrder->id, null, $salesOrder->toArray());
                return redirect('sales_orders')->with('success', $message);

            }
            catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->withErrors(['error' => 'Failed to save sale order: ' . $e->getMessage()]);
            }
        }

        $seasons = Season::where('status', 'Active')->orderBy('id', 'desc')->get();
        $customers = Customer::where('status', 'Active')->orderBy('id', 'desc')->get();
        $stores = StoreType::where('status', 'Active')->orderBy('id', 'desc')->get();
        $sales_agent = SalesAgent::where('status', 'Active')->orderBy('id', 'desc')->get();

        $availableStockItems = DB::table('stock_entry_items')
            ->leftJoin('items', 'stock_entry_items.item_id', '=', 'items.id')
            ->leftJoin('brands', 'items.brand_id', '=', 'brands.id')
            ->leftJoin('styles', 'items.style_id', '=', 'styles.id')
            ->leftJoin('brand_categories', 'items.brand_category_id', '=', 'brand_categories.id')
            ->where('stock_entry_items.stock_type', 'finished_goods')
            ->whereNull('stock_entry_items.deleted_at')
            ->select(
                'stock_entry_items.finished_item_code', 
                'brands.brand_name', 
                'brand_categories.name as category_name',
                'styles.style_name',
                'items.name as item_name',
                'stock_entry_items.sleeve_type',
                DB::raw('SUM(stock_entry_items.qty_in - stock_entry_items.qty_out) as balance')
            )
            ->groupBy('stock_entry_items.finished_item_code', 'brands.brand_name', 'brand_categories.name', 'styles.style_name', 'items.name', 'stock_entry_items.sleeve_type')
            ->having('balance', '>', 0)
            ->get();

        $stockItems = [];
        foreach ($availableStockItems as $si) {
            $stockItems[] = [
                'finished_item_code' => $si->finished_item_code,
                'display_name' => $si->finished_item_code,
                'balance' => $si->balance
            ];
        }

        if ($id && $salesOrder) {
            foreach ($salesOrder->items as $soItem) {
                $finishedCode = $soItem->stockEntryItem ? $soItem->stockEntryItem->finished_item_code : ($soItem->item->code ?? '');
                $found = false;
                foreach ($stockItems as $si) {
                    if ($si['finished_item_code'] == $finishedCode) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $stockItems[] = [
                        'finished_item_code' => $finishedCode,
                        'balance' => 0
                    ];
                }
            }
        }

        $colors = Color::where('status', 'Active')->orderBy('id', 'desc')->get();
        $uoms = Uom::where('status', 'Active')->orderBy('id', 'desc')->get();
        $sizes = SizeRatio::where('status', 'Active')->orderBy('id', 'desc')->get();
        $dynamicSizes = DB::table('stock_entry_items')->join('stock_entries', 'stock_entry_items.stock_entry_id', '=', 'stock_entries.id')->where('stock_entries.entry_type', 'Finished Goods')->whereNotNull('stock_entry_items.size')->where('stock_entry_items.size', '!=', '')->whereNull('stock_entry_items.deleted_at')->distinct()->pluck('stock_entry_items.size')->toArray();

        $nextSoNumber = '';
        if (!$id) {
            $nextSoNumber = SalesOrder::generateSoNo();
        }

        $zones = Zone::where('status', 'Active')->get();
        $shippingMethods = ShippingMethod::where('status', 'Active')->get();
        $transportModes = TransportMode::where('status', 'Active')->get();
        $serviceProviders = ServiceProvider::where('status', 'Active')->get();

        return view('sales_order.add', compact('salesOrder', 'seasons', 'customers', 'stores', 'sales_agent', 'stockItems', 'colors', 'uoms', 'sizes', 'dynamicSizes', 'nextSoNumber', 'zones', 'shippingMethods', 'transportModes', 'serviceProviders', 'charges'));

    }

    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view_details sales-order')) {
            return unauthorizedRedirect();
        }
        $salesOrder = SalesOrder::with(['customer', 'salesAgent', 'store', 'season', 'items.brandCategory', 'items.item.brand', 'items.item.style', 'items.color', 'items.uom', 'shippingMethod', 'transportMode', 'dispatchFrom', 'items.stockEntryItem'])->findOrFail($id);
        return view('sales_order.view_details', compact('salesOrder'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete sales-order')) {
            return unauthorizedRedirect();
        }
        $salesOrder = SalesOrder::findOrFail($id);
        if ($salesOrder->attachment) {
            $filePath = public_path('uploads/so/' . $id . '/' . $salesOrder->attachment);
            if (file_exists($filePath))
                unlink($filePath);
        }
        $salesOrder->delete();
        addLog('delete', 'Sale Order', 'sales_orders', $id, $salesOrder->toArray(), null);
        return redirect('sales_orders')->with('success', 'Sale Order deleted successfully');
    }
    public function updateDelayReason(Request $request, $id)
    {
        $salesOrder = SalesOrder::findOrFail($id);
        $oldData = $salesOrder->toArray();
        $salesOrder->reason_for_delay = $request->reason_for_delay;
        $salesOrder->save();
        addLog('update', 'Sale Order Delay Reason', 'sales_orders', $id, $oldData, $salesOrder->toArray());
        return response()->json(['success' => true, 'message' => 'Delay reason updated successfully']);
    }

    public function updateStatus(Request $request, $id)
    {
        $salesOrder = SalesOrder::findOrFail($id);
        $oldStatus = $salesOrder->status;
        $newStatus = $request->status;
        $valid = true;
        if ($oldStatus === 'Draft') {
            if (!in_array($newStatus, ['Pending', 'Approved', 'Rejected', 'Draft'])) $valid = false;
        } elseif ($oldStatus === 'Pending') {
            if (!in_array($newStatus, ['Approved', 'Rejected', 'Draft', 'Pending'])) $valid = false;
        } elseif ($oldStatus === 'Approved') {
            if (!in_array($newStatus, ['Cancelled', 'Dispatched', 'Approved'])) $valid = false;
        } elseif ($oldStatus === 'Rejected') {
            if (!in_array($newStatus, ['Draft', 'Rejected'])) $valid = false;
        } elseif (in_array($oldStatus, ['Cancelled', 'Dispatched'])) {
            if ($newStatus !== $oldStatus) $valid = false;
        }

        if (!$valid) {
            return response()->json(['success' => false, 'message' => 'Invalid status transition from ' . $oldStatus . ' to ' . $newStatus], 422);
        }

        DB::beginTransaction();
        try {
            $salesOrder->status = $newStatus;
            
            if ($newStatus === 'Approved' && $oldStatus !== 'Approved') {
                $salesOrder->approved_by = auth()->id();
                $salesOrder->approved_date = now();
            }

            $salesOrder->save();
            DB::commit();
            addLog('update_status', 'Sale Order Status', 'sales_orders', $id, ['old_status' => $oldStatus], $salesOrder->toArray());
            return response()->json(['success' => true, 'message' => 'Status updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function downloadPdf($id)
    {
        $salesOrder = SalesOrder::with([
            'customer.state',
            'customer.city',
            'salesAgent',
            'items.brandCategory',
            'items.item',
            'items.uom',
            'items.size',
            'items.color'
        ])->findOrFail($id);

        $setting = Setting::with(['state', 'city'])->first();

        $taxSummary = [];
        foreach ($salesOrder->items as $item) {
            $hsn = $item->hsn_sac ?: 'N/A';
            if (!isset($taxSummary[$hsn])) {
                $taxSummary[$hsn] = [
                    'hsn' => $hsn,
                    'taxable_value' => 0,
                    'cgst_rate' => $salesOrder->cgst_percent,
                    'cgst_amount' => 0,
                    'sgst_rate' => $salesOrder->sgst_percent,
                    'sgst_amount' => 0,
                    'igst_rate' => $salesOrder->igst_percent,
                    'igst_amount' => 0,
                ];
            }
            $taxSummary[$hsn]['taxable_value'] += $item->amount;
        }

        foreach ($taxSummary as &$summary) {
            $summary['cgst_amount'] = ($summary['taxable_value'] * $summary['cgst_rate']) / 100;
            $summary['sgst_amount'] = ($summary['taxable_value'] * $summary['sgst_rate']) / 100;
            $summary['igst_amount'] = ($summary['taxable_value'] * $summary['igst_rate']) / 100;
        }

        $totalInWords = numberToWords($salesOrder->total_amount);
        $totalTaxInWords = numberToWords($salesOrder->tax_amount);

        $pdf = Pdf::loadView('sales_order.pdf', compact('salesOrder', 'setting', 'taxSummary', 'totalInWords', 'totalTaxInWords'));
        $pdf->setPaper('A4', 'portrait');

        $safeSoNo = str_replace(['/', '\\'], '_', $salesOrder->so_no);
        // return view('sales_order.pdf', compact('salesOrder', 'setting', 'taxSummary', 'totalInWords', 'totalTaxInWords'));
        return $pdf->stream('Sales_Order_' . $safeSoNo . '.pdf');
    }

    public function print($id)
    {
        $salesOrder = SalesOrder::with([
            'customer.state',
            'customer.city',
            'salesAgent',
            'items.brandCategory',
            'items.item',
            'items.uom',
            'items.size',
            'items.color'
        ])->findOrFail($id);

        $setting = Setting::with(['state', 'city'])->first();

        $taxSummary = [];
        foreach ($salesOrder->items as $item) {
            $hsn = $item->hsn_sac ?: 'N/A';
            if (!isset($taxSummary[$hsn])) {
                $taxSummary[$hsn] = [
                    'hsn' => $hsn,
                    'taxable_value' => 0,
                    'cgst_rate' => $salesOrder->cgst_percent,
                    'cgst_amount' => 0,
                    'sgst_rate' => $salesOrder->sgst_percent,
                    'sgst_amount' => 0,
                    'igst_rate' => $salesOrder->igst_percent,
                    'igst_amount' => 0,
                ];
            }
            $taxSummary[$hsn]['taxable_value'] += $item->amount;
        }

        foreach ($taxSummary as &$summary) {
            $summary['cgst_amount'] = ($summary['taxable_value'] * $summary['cgst_rate']) / 100;
            $summary['sgst_amount'] = ($summary['taxable_value'] * $summary['sgst_rate']) / 100;
            $summary['igst_amount'] = ($summary['taxable_value'] * $summary['igst_rate']) / 100;
        }

        $totalInWords = numberToWords($salesOrder->total_amount);
        $totalTaxInWords = numberToWords($salesOrder->tax_amount);

        $is_print = true;
        return view('sales_order.pdf', compact('salesOrder', 'setting', 'taxSummary', 'totalInWords', 'totalTaxInWords', 'is_print'));
    }

    public function getFinishedItemDetails($code, $color_id)
    {
        $stockItem = DB::table('stock_entry_items')
            ->where('finished_item_code', $code)
            ->where('color_id', $color_id)
            ->where('stock_type', 'finished_goods')
            ->whereNull('deleted_at')
            ->select('item_id', 'art_no', 'size', 'price', 'uom_id', DB::raw('SUM(qty_in - qty_out) as balance'))
            ->groupBy('item_id', 'art_no', 'size', 'price', 'uom_id')
            ->first();

        if ($stockItem) {
            $item = Item::find($stockItem->item_id);
            $color = Color::find($color_id);
            return response()->json([
                'success' => true,
                'data' => [
                    'art_no' => $stockItem->art_no,
                    'size' => $stockItem->size,
                    'mrp' => $stockItem->price,
                    'uom_id' => $stockItem->uom_id,
                    'balance' => $stockItem->balance,
                    'color_name' => $color ? $color->color_name : 'No Color',
                    'sleeve' => str_contains($code, 'F/S') || str_contains($code, 'Full') ? 'Full' : (str_contains($code, 'H/S') || str_contains($code, 'Half') ? 'Half' : 'Full')
                ]
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Item not found in stock']);
    }

    public function getFinishedItemStock(Request $request)
    {
        $code = $request->code;
        $soId = $request->so_id;
        $exactMatch = DB::table('stock_entry_items')->where('sku', $code)->where('stock_type', 'finished_goods')->whereNull('deleted_at')->first();

        $query = DB::table('stock_entry_items')->where('stock_type', 'finished_goods')->whereNull('deleted_at');

        if ($exactMatch) {
            $query->where('sku', $code);
        } else {
            $query->where(function($q) use ($code) {
                $q->where('finished_item_code', $code)
                  ->orWhere(DB::raw("REPLACE(finished_item_code, ' ', '')"), str_replace(' ', '', $code));
            });
        }

        $items = $query->select('finished_item_code', 'color_id', 'item_id', 'art_no', 'size', 'price', 'uom_id', 'sleeve_type', 'sku', DB::raw('MAX(id) as stock_entry_item_id'), DB::raw('SUM(qty_in - qty_out) as balance'))
            ->groupBy('finished_item_code', 'color_id', 'item_id', 'art_no', 'size', 'price', 'uom_id', 'sleeve_type', 'sku')
            ->get();

        if ($items->isNotEmpty()) {
            $first = $items->first();
            $target = $exactMatch ? ($items->firstWhere('sku', $code) ?: $first) : $first;
            $item = Item::find($target->item_id);
            $sizeStock = [];
            foreach ($items as $si) {
                $sizeStock[$si->size] = ($sizeStock[$si->size] ?? 0) + (float)$si->balance;
            }
            $sizePrices = [];
            foreach (['36', '38', '40', '42', '44', '46', '48', '50'] as $sz) {
                $priceRec = DB::table('item_prices')
                    ->where('finished_item_code', $target->finished_item_code)
                    ->where('art_no', $target->art_no)
                    ->where('size', $sz)
                    ->where('status', 'Active')
                    ->whereNull('deleted_at')
                    ->whereDate('effective_from', '<=', now())
                    ->orderBy('effective_from', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();
                
                if (!$priceRec) {
                    $priceRec = DB::table('item_prices')
                        ->where('finished_item_code', $target->finished_item_code)
                        ->where('art_no', $target->art_no)
                        ->whereNull('size')
                        ->where('status', 'Active')
                        ->whereNull('deleted_at')
                        ->whereDate('effective_from', '<=', now())
                        ->orderBy('effective_from', 'desc')
                        ->orderBy('id', 'desc')
                        ->first();
                }

                $sizePrices[$sz] = [
                    'mrp' => $priceRec ? $priceRec->selling_price : ($item ? $item->mrp : $target->price),
                    'price' => $priceRec ? $priceRec->unit_price : $target->price,
                ];
            }

            $itemPrice = DB::table('item_prices')
                ->where('finished_item_code', $target->finished_item_code)
                ->where('art_no', $target->art_no)
                ->where('size', $target->size)
                ->where('status', 'Active')
                ->whereNull('deleted_at')
                ->whereDate('effective_from', '<=', now())
                ->orderBy('effective_from', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            if (!$itemPrice) {
                $itemPrice = DB::table('item_prices')
                    ->where('finished_item_code', $target->finished_item_code)
                    ->where('art_no', $target->art_no)
                    ->whereNull('size')
                    ->where('status', 'Active')
                    ->whereNull('deleted_at')
                    ->whereDate('effective_from', '<=', now())
                    ->orderBy('effective_from', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();
            }

            $finalMrp = $itemPrice ? $itemPrice->selling_price : ($item ? $item->mrp : $target->price);
            $finalPrice = $itemPrice ? $itemPrice->unit_price : $target->price;

            return response()->json([
                'success' => true,
                'finished_item_code' => $target->finished_item_code,
                'item_id' => $target->item_id,
                'item_name' => $item ? $item->name : '',
                'brand_name' => ($item && $item->brand) ? $item->brand->brand_name : '',
                'brand_cat_id' => $item ? $item->brand_category_id : null,
                'stock_entry_item_id' => $target->stock_entry_item_id ?? $target->id,
                'color_id' => $target->color_id,
                'size' => $target->size,
                'art_no' => $target->art_no,
                'price' => $finalPrice,
                'mrp' => $finalMrp,
                'size_prices' => $sizePrices,
                'uom_id' => $target->uom_id,
                'sleeve_type' => $target->sleeve_type,
                'sku' => $target->sku,
                'balance' => $target->balance ?? 0,
                'size_stock' => $sizeStock
            ]);
        }

        return response()->json(['success' => false]);
    }

    public function searchStockItems(Request $request)
    {
        $term = $request->term;
        $results = DB::table('stock_entry_items')
            ->leftJoin('items', 'stock_entry_items.item_id', '=', 'items.id')
            ->leftJoin('brands', 'items.brand_id', '=', 'brands.id')
            ->leftJoin(DB::raw('(SELECT finished_item_code, MAX(unit_price) as unit_price, MAX(selling_price) as selling_price FROM item_prices WHERE status = "Active" AND deleted_at IS NULL GROUP BY finished_item_code) as ip'), 'stock_entry_items.finished_item_code', '=', 'ip.finished_item_code')
            ->where('stock_entry_items.stock_type', 'finished_goods')
            ->whereNull('stock_entry_items.deleted_at')
            ->where(function ($query) use ($term) {
                $query->where('stock_entry_items.finished_item_code', 'LIKE', "%{$term}%")
                      ->orWhere('stock_entry_items.sku', 'LIKE', "%{$term}%")
                      ->orWhere('stock_entry_items.barcode', 'LIKE', "%{$term}%")
                      ->orWhere('stock_entry_items.art_no', 'LIKE', "%{$term}%");
            })
            ->select(
                'stock_entry_items.finished_item_code',
                'stock_entry_items.sku',
                'items.name as item_name',
                'brands.brand_name',
                'stock_entry_items.art_no',
                'stock_entry_items.size',
                'stock_entry_items.price as fallback_price',
                'ip.unit_price',
                'ip.selling_price as retail_mrp',
                'stock_entry_items.sleeve_type',
                DB::raw('SUM(stock_entry_items.qty_in - stock_entry_items.qty_out) as balance')
            )->groupBy('stock_entry_items.finished_item_code', 'stock_entry_items.sku', 'items.name', 'brands.brand_name', 'stock_entry_items.art_no', 'stock_entry_items.size', 'stock_entry_items.price', 'ip.unit_price', 'ip.selling_price', 'stock_entry_items.sleeve_type')->having('balance', '>', 0)->limit(20)->get();

        $formattedResults = [];
        foreach ($results as $item) {
            $label = $item->finished_item_code . ' - ' . $item->item_name;
            if ($item->sleeve_type) {
                $label .= ' (' . $item->sleeve_type . ')';
            }
            if ($item->sku) {
                $label .= ' | SKU: ' . $item->sku;
            }
            if ($item->size) {
                $label .= ' | Size: ' . $item->size;
            }
            
            $finalPrice = $item->unit_price ?? $item->fallback_price;
            $finalMrp = $item->retail_mrp ?? $item->fallback_price;

            $formattedResults[] = [
                'id' => $item->finished_item_code,
                'label' => $label,
                'value' => $item->sku ?: $item->finished_item_code,
                'sku' => $item->sku,
                'item_name' => $item->item_name,
                'brand_name' => $item->brand_name,
                'art_no' => $item->art_no,
                'size' => $item->size,
                'price' => $finalPrice,
                'mrp' => $finalMrp,
                'balance' => $item->balance,
                'sleeve_type' => $item->sleeve_type,
            ];
        }

        return response()->json($formattedResults);
    }

    public function syncFromOrderaxe(Request $request, OrderaxeService $orderaxeService)
    {
        if (auth()->id() != 1 && !auth()->user()->can('create sales-order')) {
            return unauthorizedRedirect();
        }

        $limit = $request->query('limit');
        $count = $orderaxeService->syncOrders($limit);


        if ($count > 0) {
            return redirect('sales_orders')->with('success', "Successfully synced $count orders from Orderaxe.");
        }

        return redirect('sales_orders')->with('error', "No new orders found to sync.");
    }

    public function deleteCharge($id)
    {
        try {
            $charge = SalesOrderCharge::findOrFail($id);
            $charge->delete();

            return response()->json([
                'success' => true,
                'message' => 'Charge deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete charge: ' . $e->getMessage()
            ], 500);

        }
    }
    public function syncOrderaxe(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('create sales-order')) {
            return unauthorizedRedirect();
        }

        try {
            $service = new \App\Services\OrderaxeService();
            $limit = $request->query('limit', 100);
            $syncCount = $service->syncOrders((int)$limit);
            if ($syncCount > 0) {
                return redirect('sales_orders')->with('success', $syncCount . ' orders synced successfully from Orderaxe');
            } else {
                return redirect('sales_orders')->with('info', 'No new orders found to sync from Orderaxe');
            }
        } catch (\Exception $e) {
            return redirect('sales_orders')->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }



}

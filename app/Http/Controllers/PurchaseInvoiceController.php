<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use App\Models\Setting;
use App\Models\PurchaseInvoiceItem;
use App\Models\PurchaseInvoiceCharge;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Charge;
use App\Models\PurchaseInvoicePayment;
use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\FabricSize;
use App\Models\FabricType;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseInvoiceController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view purchase-invoice')) {
            return unauthorizedRedirect();
        }
        if ($request->ajax()) {
            $query = PurchaseInvoice::with(['supplier', 'purchaseOrder'])->withCount('grnEntries')->orderBy('id', 'desc');

            if (!empty($request->supplier_id)) {
                $query->where('supplier_id', $request->supplier_id);
            }

            if (!empty($request->invoice_status)) {
                $query->where('invoice_status', $request->invoice_status);
            }

            $totalRecords = $query->count();

            if ($request->has('search') && !empty($request->input('search')['value'])) {
                $search = $request->input('search')['value'];
                $numericSearch = preg_replace('/[₹,\s]/', '', $search);
                $query->where(function ($q) use ($search, $numericSearch) {
                    $q->where('invoice_no', 'like', "%{$search}%")
                        ->orWhere('po_reference', 'like', "%{$search}%")
                        ->orWhere('destination', 'like', "%{$search}%")
                        ->orWhere('invoice_status', 'like', "%{$search}%")
                        ->orWhereDate('invoice_date', $search)
                        ->orWhereRaw("DATE_FORMAT(invoice_date, '%d-%m-%Y') LIKE ?", ["%{$search}%"])
                        ->orWhereRaw("CAST(grand_total AS CHAR) LIKE ?", ["%{$numericSearch}%"])
                        ->orWhereHas('supplier', function ($q2) use ($search) {
                            $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                        })
                        ->orWhereHas('purchaseOrder', function ($q2) use ($search) {
                            $q2->where('po_number', 'like', "%{$search}%");
                        });
                });
            }

            $filteredRecords = $query->count();

            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            if ($length != -1) {
                $query->skip($start)->take($length);
            }

            $invoices = $query->get();
            $data = [];
            $count = $start + 1;

            foreach ($invoices as $invoice) {
                $statusOptions = '';

                $allStatuses = ['Draft', 'Unpaid/Credit', 'Partially Paid', 'Paid'];
                $currentStatus = $invoice->invoice_status;

                foreach ($allStatuses as $status) {
                    $selected = ($currentStatus === $status) ? 'selected' : '';
                    $disabled = '';
                    if ($currentStatus === 'Draft') {
                        $disabled = '';
                    } elseif ($currentStatus === 'Unpaid/Credit' && $status === 'Draft') {
                        $disabled = 'disabled';
                    } elseif ($currentStatus === 'Partially Paid' && $status !== 'Paid') {
                        $disabled = 'disabled';
                    } elseif ($currentStatus === 'Paid' && $status !== 'Paid') {
                        $disabled = 'disabled';
                    }
                    $statusOptions .= "<option value=\"{$status}\" {$selected} {$disabled}>{$status}</option>";
                }

                $statusDropdown = '
                <div class="form-floating form-floating-outline">
                    <select class="form-select invoice-status-change" data-id="' . $invoice->id . '">
                        ' . $statusOptions . '
                    </select>
                </div>
                <div class="status_msg_' . $invoice->id . ' mt-1"></div>';

                $action = '<div class="button-box">';
                if (auth()->id() == 1 || auth()->user()->can('view_details purchase-invoice')) {
                    $action .= '<a href="' . url('purchase_invoices/view/' . $invoice->id) . '" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>';
                }
                $hasGrn = \App\Models\GrnEntry::where('purchase_invoice_id', $invoice->id)->whereNull('deleted_at')->exists();
                if ((auth()->id() == 1 || auth()->user()->can('edit purchase-invoice')) && $invoice->invoice_status !== 'Paid' && !$hasGrn) {
                    $action .= '<a href="' . url('purchase_invoices/add/' . $invoice->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }
                $action .= '</div>';
            
                $totalQty = $invoice->items ? $invoice->items->sum('quantity') : 0;
                $data[] = [
                    'DT_RowIndex' => $count++,
                    'invoice_no' => $invoice->invoice_no,
                    'po_number' => $invoice->purchaseOrder ? $invoice->purchaseOrder->po_number : '-',
                    'invoice_date' => $invoice->invoice_date->format('d-m-Y'),
                    'supplier_name' => $invoice->supplier ? $invoice->supplier->name . ' <a href="' . url('suppliers/view_details/' . $invoice->supplier->id) . '" target="_blank"><span class="mini-title">(' . $invoice->supplier->code . ')</span></a>' : '-',
                    'destination' => $invoice->destination ?? '-',
                    'total_qty' => number_format($totalQty, 2),
                    'total_amount' => '₹' . number_format($invoice->grand_total, 2),
                    'status' => $statusDropdown,
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

        $suppliers = Supplier::where('status', 'Active')->orderBy('id','desc')->get();
        return view('purchase_invoice.view', compact('suppliers'));
    }


    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit purchase-invoice')) {
                return unauthorizedRedirect();
            }
            
            $hasGrn = \App\Models\GrnEntry::where('purchase_invoice_id', $id)->exists();
            if ($hasGrn) {
                return redirect('purchase_invoices')->with('error', 'Cannot edit this Purchase Invoice because a GRN has already been created for it.');
            }
            $invoice = PurchaseInvoice::with([
                'items.rawMaterial.storeCategory', 
                'items.uom', 
                'items.brand', 
                'items.fabricWidth', 
                'items.purchaseOrderItem.storeCategory', 
                'items.purchaseOrderItem.brand', 
                'items.purchaseOrderItem.fabricWidth', 
                'charges', 
                'purchaseCommissionAgent'
            ])->findOrFail($id);
            $charges = $invoice->charges;
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create purchase-invoice')) {
                return unauthorizedRedirect();
            }
        }
        $invoice = $invoice ?? null;
        $charges = $charges ?? collect();

        if (request()->isMethod('post')) {
            $request = request();
            $oldInvoice = $id ? PurchaseInvoice::findOrFail($id) : null;

            $rules = [
                'invoice_no' => [($id ? 'nullable' : 'required'), 'string', 'min:3', 'max:50', 'not_regex:/^0+$/', 'unique:purchase_invoices,invoice_no,' . ($id ?? 'NULL') . ',id,deleted_at,NULL'],
                'invoice_date' => 'required|date_format:d-m-Y',
                'purchase_order_id' => 'required|array|min:1',
                'purchase_order_id.*' => 'exists:purchase_orders,id',
                'supplier_id' => 'required|exists:suppliers,id',
                'po_reference' => 'nullable|string|min:3|max:50',
                'transport' => 'nullable|string|max:100',
                'destination' => 'nullable|string|max:100',
                'lr_no' => ['nullable', 'string', 'max:100', 'not_regex:/^0+$/'],
                'lr_date' => 'nullable|date_format:d-m-Y',
                'eway_billno' => ['nullable', 'string', 'max:100', 'not_regex:/^0+$/'],
                'indent_no' => ['nullable', 'string', 'max:100', 'not_regex:/^0+$/'],
                'indent_date' => 'nullable|date_format:d-m-Y',
                'invoice_status' => 'required|in:Draft,Unpaid/Credit,Paid,Partially Paid',
                'items' => 'required|array|min:1',
                'items.*.raw_material_id' => 'required|exists:raw_materials,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.rate' => 'required|numeric|min:0',
                'items.*.hsn_code' => [
                    'nullable',
                    'digits_between:4,8'
                ],
                'other_state' => ($id ? 'nullable' : 'required') . '|in:Y,N',
                'igst_percent' => 'nullable|numeric|min:0|max:100',
                'cgst_percent' => 'nullable|numeric|min:0|max:100',
                'sgst_percent' => 'nullable|numeric|min:0|max:100',
                'other_charges' => 'nullable|numeric|min:0',
                'round_off' => 'nullable|numeric|min:0|max:99.99',
                'round_off_type' => 'required|in:Add,Less',
                'grand_total' => 'required|numeric',
                'received_amount' => 'nullable|numeric|min:0',
                'charges_select' => 'nullable',
                'charge_amount' => 'nullable|numeric|min:0',
                'charges.amount.*' => 'nullable|numeric|min:0',
                'transaction_id' => 'nullable|max:100',
                'auth_sign' => 'nullable|mimes:jpeg,jpg,png,webp,pdf,doc,docx|max:2048',
                'attachments' => 'nullable|mimes:jpeg,jpg,png,webp,pdf,doc,docx|max:2048',
                'discount_percent' => 'nullable|numeric|min:0|max:100',
                'commission' => 'nullable|numeric|min:0|max:100',
            ];

            $messages = [
                '*.required' => 'This field is required.',
                '*.exists' => 'Selected value is invalid.',
                '*.unique' => 'This field already exists.',
                'regex' => 'This field is an invalid format',
                'not_regex' => 'This field is an invalid format',
                '*.date' => 'Please enter a valid date.',
                'items.required' => 'Please add at least one item.',
                'items.*.quantity' => 'Quantity is required.',
                'items.*.hsn_code.digits_between' => 'HSN Code must be between 4 and 8 digits.',
                'other_state.required' => 'Please select if it is an other state transaction.',
                'igst_percent.numeric' => 'IGST % must be a number.',
                'igst_percent.min' => 'IGST cannot be negative. Please enter 0 or a positive value.',
                'igst_percent.max' => 'IGST percentage cannot exceed 100%.',
                'cgst_percent.numeric' => 'CGST % must be a number.',
                'cgst_percent.min' => 'CGST cannot be negative. Please enter 0 or a positive value.',
                'cgst_percent.max' => 'CGST percentage cannot exceed 100%.',
                'sgst_percent.numeric' => 'SGST % must be a number.',
                'sgst_percent.min' => 'SGST cannot be negative. Please enter 0 or a positive value.',
                'sgst_percent.max' => 'SGST percentage cannot exceed 100%.',
                'other_charges.numeric' => 'Other Charges must be a number.',
                'grand_total.required' => 'Grand Total is required.',
                'grand_total.min' => 'Grand Total cannot be negative.',
                'received_amount.numeric' => 'Received Amount must be a number.',
                'charges_select.required' => 'Please select a charge.',
                'charge_amount.numeric' => 'Charge amount must be a number.',
                'charges.amount.*.numeric' => 'Charge amount must be a number.',
                'round_off.min' => 'Round off cannot be negative. Please enter 0 or a positive value.',
                'round_off.max' => 'Round off amount cannot exceed 99.99.',
                'min' => 'This field must be at least :min characters.',
                'max' => 'This field should not be more than :max characters.',
                'auth_sign.mimes' => 'Upload a valid file (e.g., .jpg, .png, .jpeg, .webp, .pdf, .doc, .docx).',
                'auth_sign.max' => 'Uploaded file cannot exceed 2MB.',
                'attachments.mimes' => 'Upload a valid file (e.g., .jpg, .png, .jpeg, .webp, .pdf, .doc, .docx).',
                'attachments.max' => 'Uploaded file cannot exceed 2MB.',
                'discount_percent.min' => 'Discount cannot be negative. Please enter 0 or a positive value.',
                'discount_percent.max' => 'Discount percentage cannot exceed 100%.',
                'commission.min' => 'Commission cannot be negative. Please enter 0 or a positive value.',
                'commission.max' => 'Commission percentage cannot exceed 100%.',
            ];

            $validated = $request->validate($rules, $messages);
            $purchaseOrders = PurchaseOrder::with('items')->whereIn('id', $request->purchase_order_id)->get();

            $errors = [];
            $hasSelectedItems = false;

            foreach ($request->items as $index => $item) {
                if (!isset($item['selected'])) {
                    continue;
                }
                $hasSelectedItems = true;
                if (empty($item['quantity']) || $item['quantity'] <= 0) {
                    $errors["items.$index.quantity"] = 'This field is required.';
                    continue;
                }
               $poItem = \App\Models\PurchaseOrderItem::whereIn('purchase_order_id', $request->purchase_order_id)->where('id', $item['purchase_order_item_id'])->first();

                if ($poItem) {
                    $alreadyInvoiced = PurchaseInvoiceItem::where('purchase_order_item_id', $poItem->id)->sum('quantity');

                    if ($id) {
                        $currentInvoiceItem = PurchaseInvoiceItem::where('purchase_invoice_id', $id)->where('purchase_order_item_id', $poItem->id)->first();
                        if ($currentInvoiceItem) {
                            $alreadyInvoiced -= $currentInvoiceItem->quantity;
                        }
                    }

                    $maxAllowed = ($poItem->quantity * 1.5) - $alreadyInvoiced;

                    if ($item['quantity'] > $maxAllowed) {
                        $maxAllowedFormatted = number_format($maxAllowed, 2);
                        $errors["items.$index.quantity"] = "Received quantity cannot exceed max allowed ({$maxAllowedFormatted}) because of previously invoiced items.";
                    }
                }
            }

            if (!$hasSelectedItems && $request->has('purchase_order_id')) {
                return back()->withInput()->withErrors([
                    'items' => 'Please select at least one item from the Item Details section.'
                ]);
            }

            // Recalculate amounts to validate against negative values
            $subTotal = 0;
            if ($request->has('items')) {
                foreach ($request->items as $item) {
                    if (isset($item['selected']) && $item['selected'] == '1') {
                        $subTotal += floatval($item['quantity'] ?? 0) * floatval($item['rate'] ?? 0);
                    }
                }
            }

            $discountPercent = floatval($request->discount_percent ?? 0);
            if ($discountPercent < 0) {
                $errors['discount_percent'] = 'Discount percentage cannot be negative.';
            }
            $discountAmount = ($subTotal * $discountPercent) / 100;

            $preGstCharges = 0;
            $postGstCharges = 0;
            $brokerageAmount = 0;
            if ($request->has('charges') && isset($request->charges['charge_id'])) {
                $chargeAmounts = $request->charges['amount'] ?? [];
                $chargeNames = $request->charges['name'] ?? [];
                $taxTypes = $request->charges['tax_type'] ?? [];

                foreach ($chargeAmounts as $idx => $amount) {
                    $cName = strtoupper(trim($chargeNames[$idx] ?? ''));
                    $amt = floatval($amount);
                    if ($cName === 'BROKERAGE') {
                        $brokerageAmount += $amt;
                    } else {
                        $type = $taxTypes[$idx] ?? 'Post-GST';
                        if ($type === 'Pre-GST') {
                            $preGstCharges += $amt;
                        } else {
                            $postGstCharges += $amt;
                        }
                    }
                }
            }

            $commissionPercent = floatval($request->commission ?? 0);
            if ($commissionPercent < 0) {
                $errors['commission'] = 'Commission percentage cannot be negative.';
            }
            $baseCommissionAmount = 0;
            if ($commissionPercent > 0) {
                $baseCommissionAmount = ($subTotal * $commissionPercent) / 100;
            } else {
                $baseCommissionAmount = floatval($request->commission_amount ?? 0) - $brokerageAmount;
                if ($baseCommissionAmount < 0) {
                    $baseCommissionAmount = 0;
                }
            }
            $totalCommission = $baseCommissionAmount + $brokerageAmount;

            $itemTotal = $subTotal - $discountAmount - $totalCommission;
            $taxableAmount = $itemTotal + $preGstCharges;

            if ($taxableAmount < 0) {
                if ($discountPercent > 0) {
                    $errors['discount_percent'] = 'Discount cannot exceed the subtotal.';
                }
                $errors['taxable_amount'] = 'Taxable Total cannot be negative. Please check the discount, commission, and Pre-GST charges.';
            }

            $taxAmount = 0;
            $igstAmount = 0;
            $cgstAmount = 0;
            $sgstAmount = 0;

            $displayTaxableAmount = $taxableAmount >= 0 ? $taxableAmount : 0;
            $otherStateVal = $request->has('other_state') ? $request->other_state : ($oldInvoice && $oldInvoice->other_state ? 'Y' : 'N');

            if ($otherStateVal === 'Y') {
                $igstPercent = floatval($request->igst_percent ?? 0);
                $igstAmount = ($displayTaxableAmount * $igstPercent) / 100;
                if ($igstAmount < 0) $igstAmount = 0;
                $taxAmount = $igstAmount;
            } else {
                $cgstPercent = floatval($request->cgst_percent ?? 0);
                $sgstPercent = floatval($request->sgst_percent ?? 0);
                $cgstAmount = ($displayTaxableAmount * $cgstPercent) / 100;
                $sgstAmount = ($displayTaxableAmount * $sgstPercent) / 100;
                if ($cgstAmount < 0) $cgstAmount = 0;
                if ($sgstAmount < 0) $sgstAmount = 0;
                $taxAmount = $cgstAmount + $sgstAmount;
            }

            $totalBeforeRoundOff = round($taxableAmount + $taxAmount + $postGstCharges, 2);
            $roundOffAmount = floatval($request->round_off ?? 0);
            $roundOffType = $request->round_off_type ?? 'Add';
            $finalTotal = 0;

            if ($roundOffType === 'Add') {
                $finalTotal = $totalBeforeRoundOff + $roundOffAmount;
            } else {
                $finalTotal = $totalBeforeRoundOff - $roundOffAmount;
            }

            if ($finalTotal < 0) {
                $errors['grand_total'] = 'Grand Total cannot be negative. Please check the round off and other charges.';
            }

            if (!empty($errors)) {
                return back()->withInput()->withErrors($errors);
            }

            DB::beginTransaction();
            try {
                $firstPoId = is_array($request->purchase_order_id) && count($request->purchase_order_id) > 0 ? $request->purchase_order_id[0] : null;

                $invoiceData = [
                    'invoice_no' => $request->invoice_no,
                    'invoice_date' => Carbon::createFromFormat('d-m-Y', $request->invoice_date)->format('Y-m-d'),
                    'purchase_order_id' => $firstPoId,
                    'purchase_order_no' => $request->purchase_order_no,
                    'supplier_id' => $request->supplier_id,
                    'po_reference' => $request->po_reference,
                    'transport' => $request->transport,
                    'destination' => $request->destination,
                    'lr_no' => $request->lr_no,
                    'eway_billno' => $request->eway_billno,
                    'lr_date' => $request->lr_date ? Carbon::createFromFormat('d-m-Y', $request->lr_date)->format('Y-m-d') : null,
                    'indent_no' => $request->indent_no,
                    'indent_date' => $request->indent_date ? Carbon::createFromFormat('d-m-Y', $request->indent_date)->format('Y-m-d') : null,
                    'sub_total' => $request->sub_total ?? 0,
                    'discount_percent' => $request->discount_percent ?? 0,
                    'discount_amount' => $request->discount_amount ?? 0,
                    'taxable_amount' => $request->taxable_amount ?? 0,
                    'other_state' => $request->has('other_state') ? ($request->other_state === 'Y') : ($oldInvoice ? $oldInvoice->other_state : false),
                    'igst_percent' => $request->igst_percent ?? 0,
                    'igst_amount' => $request->igst_amount ?? 0,
                    'cgst_percent' => $request->cgst_percent ?? 0,
                    'cgst_amount' => $request->cgst_amount ?? 0,
                    'sgst_percent' => $request->sgst_percent ?? 0,
                    'sgst_amount' => $request->sgst_amount ?? 0,
                    'tax_amount' => $request->tax_amount ?? 0,
                    'other_charges' => $request->other_charges ?? 0,
                    'round_off' => $request->round_off ?? 0,
                    'round_off_type' => $request->round_off_type ?? 'Add',
                    'grand_total' => $request->grand_total ?? 0,
                    'received_amount' => $request->received_amount ?? 0,
                    'due_amount' => $request->due_amount ?? 0,
                    'invoice_status' => $request->invoice_status,
                    'payment_mode' => $request->payment_mode,
                    'due_date' => $request->due_date ? Carbon::createFromFormat('d-m-Y', $request->due_date)->format('Y-m-d') : null,
                    'notes' => $request->notes,
                    'transaction_id' => $request->transaction_id,
                    'purchase_commission_agent_id' => $request->purchase_commission_agent_id,
                    'commission' => $request->commission ?? 0,
                    'commission_amount' => $request->commission_amount ?? 0,
                ];

                $uploadPath = public_path('uploads/purchase_invoices');

                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                if ($request->hasFile('auth_sign')) {
                    if ($id && !empty($invoice->auth_signature)) {
                        $oldFilePath = public_path('uploads/purchase_invoices/' . $invoice->auth_signature);
                        if (file_exists($oldFilePath)) {
                            @unlink($oldFilePath);
                        }
                    }

                    $file = $request->file('auth_sign');
                    $fileName = time() . '_auth_' . $file->getClientOriginalName();
                    $file->move($uploadPath, $fileName);
                    $invoiceData['auth_signature'] = $fileName;
                }

                if ($request->hasFile('attachments')) {
                    if ($id && !empty($invoice->attachments)) {
                        $oldFilePath = public_path('uploads/purchase_invoices/' . $invoice->attachments);
                        if (file_exists($oldFilePath)) {
                            @unlink($oldFilePath);
                        }
                    }
                    $file = $request->file('attachments');
                    $fileName = time() . '_attach_' . $file->getClientOriginalName();
                    $file->move($uploadPath, $fileName);
                    $invoiceData['attachments'] = $fileName;
                }


                if ($id) {
                    $oldData = $oldInvoice->toArray();
                    $oldReceived = $oldInvoice->received_amount ?? 0;

                    $invoiceData['updated_by'] = auth()->id();
                    $oldInvoice->update($invoiceData);
                    $invoice = $oldInvoice;
                    PurchaseInvoiceCharge::where('purchase_invoice_id', $id)->forceDelete();

                    $newData = $invoice->fresh()->toArray();
                    addLog('update', 'Purchase Invoice', 'purchase_invoices', $id, $oldData, $newData);
                    $message = 'Purchase Invoice updated successfully';

                    $newPayment = $request->received_amount ?? 0;
                    if ($newPayment > 0) {
                        PurchaseInvoicePayment::create([
                            'purchase_invoice_id' => $invoice->id,
                            'amount' => $newPayment,
                            'payment_date' => now(),
                            'payment_mode' => $request->payment_mode ?? 'Cash',
                            'transaction_id' => $request->transaction_id,
                            'notes' => 'Additional payment from invoice edit',
                            'created_by' => auth()->id(),
                        ]);
                    }

                    $totalReceived = PurchaseInvoicePayment::where('purchase_invoice_id', $invoice->id)->sum('amount');
                    $invoice->update([
                        'received_amount' => $totalReceived,
                        'due_amount' => ($request->grand_total ?? $invoice->grand_total) - $totalReceived
                    ]);
                } else {
                    $invoiceData['created_by'] = auth()->id();
                    $invoice = PurchaseInvoice::create($invoiceData);

                    if ($request->received_amount > 0) {
                        PurchaseInvoicePayment::create([
                            'purchase_invoice_id' => $invoice->id,
                            'amount' => $request->received_amount,
                            'payment_date' => now(),
                            'payment_mode' => $request->payment_mode ?? 'Cash',
                            'transaction_id' => $request->transaction_id,
                            'notes' => 'Initial payment from invoice creation',
                            'created_by' => auth()->id(),
                        ]);
                    }

                    $newData = $invoice->toArray();
                    addLog('create', 'Purchase Invoice', 'purchase_invoices', $invoice->id, null, $newData);
                    $message = 'Purchase Invoice created successfully';
                }


                if ($request->has('items')) {
                    if ($id) {
                        $selectedPoItemIds = [];
                        foreach ($request->items as $item) {
                            if (isset($item['selected']) && $item['selected'] == '1') {
                                if (isset($item['purchase_order_item_id'])) {
                                    $selectedPoItemIds[] = $item['purchase_order_item_id'];
                                }
                            }
                        }
                        PurchaseInvoiceItem::where('purchase_invoice_id', $id)
                            ->whereNotIn('purchase_order_item_id', $selectedPoItemIds)
                            ->delete();
                    }
                    foreach ($request->items as $item) {
                        if (isset($item['selected']) && $item['selected'] == '1') {
                            if (isset($item['fabric_type_id']) && !empty($item['fabric_type_id']) && !empty($item['purchase_order_item_id'])) {
                                \App\Models\PurchaseOrderItem::where('id', $item['purchase_order_item_id'])->update([
                                    'fabric_type_id' => $item['fabric_type_id']
                                ]);
                            }

                            if ($id) {
                                $existingItem = PurchaseInvoiceItem::where('purchase_invoice_id', $id)->where('purchase_order_item_id', $item['purchase_order_item_id'] ?? null)->first();
                                if ($existingItem) {
                                    $oldRate = floatval($existingItem->rate);
                                    $newRate = floatval($item['rate']);

                                    $existingItem->update([
                                        'brand_id' => $item['brand_id'] ?? $existingItem->brand_id,
                                        'fabric_width_id' => $item['fabric_width_id'] ?? $existingItem->fabric_width_id,
                                        'hsn_code' => $item['hsn_code'] ?? $existingItem->hsn_code,
                                        'rate' => $newRate,
                                        'quantity' => $item['quantity'],
                                        'amount' => $item['quantity'] * $newRate,
                                        'qty_received' => $item['quantity'],
                                        'qty_invoiced' => $item['quantity'],
                                    ]);

                                    if ($oldRate !== $newRate) {
                                        $grnItems = \DB::table('grn_entry_items')->where('purchase_invoice_item_id', $existingItem->id)->get();
                                        foreach ($grnItems as $grnItem) {
                                            \DB::table('grn_entry_items')->where('id', $grnItem->id)->update([
                                                'rate' => $newRate,
                                                'amount' => $grnItem->qty_received * $newRate
                                            ]);
                                            
                                            \DB::table('stock_entry_items')->where('grn_entry_item_id', $grnItem->id)->update([
                                                'price' => $newRate
                                            ]);
                                        }
                                    }
                                }
                            } else {
                                PurchaseInvoiceItem::create([
                                    'purchase_invoice_id' => $invoice->id,
                                    'purchase_order_item_id' => $item['purchase_order_item_id'] ?? null,
                                    'brand_id' => $item['brand_id'] ?? null,
                                    'fabric_width_id' => $item['fabric_width_id'] ?? null,
                                    'raw_material_id' => $item['raw_material_id'],
                                    'hsn_code' => $item['hsn_code'],
                                    'quantity' => $item['quantity'],
                                    'uom_id' => $item['uom_id'],
                                    'rate' => $item['rate'],
                                    'amount' => $item['quantity'] * $item['rate'],
                                    'qty_ordered' => $item['qty_ordered'] ?? 0,
                                    'qty_received' => $item['quantity'],
                                    'qty_invoiced' => $item['quantity'],
                                ]);
                            }
                        }
                    }
                }

                if ($request->has('charges') && isset($request->charges['charge_id'])) {
                    $chargeIds = $request->charges['charge_id'];
                    $chargeNames = $request->charges['name'];
                    $chargeAmounts = $request->charges['amount'];
                    $taxTypes = $request->charges['tax_type'] ?? [];

                    foreach ($chargeIds as $index => $chargeId) {
                        PurchaseInvoiceCharge::create([
                            'purchase_invoice_id' => $invoice->id,
                            'charge_id' => $chargeId,
                            'charge_name' => $chargeNames[$index],
                            'charge_amount' => $chargeAmounts[$index],
                            'tax_type' => $taxTypes[$index] ?? 'Post-GST',
                        ]);
                    }
                }

                DB::commit();
                return redirect('purchase_invoices')->with('success', $message);
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->withErrors(['error' => 'Failed to save invoice: ' . $e->getMessage()]);
            }
        }

        $purchaseOrders = collect();
        if ($invoice) {
            $purchaseOrders = PurchaseOrder::with('supplier')
                ->where('supplier_id', $invoice->supplier_id)
                ->where(function ($query) use ($invoice) {
                    $query->where(function ($sub) {
                        $sub->where('purchase_orders.status', 'Approved')
                            ->where('purchase_orders.is_self_closed', 0)
                            ->whereIn(
                                'purchase_orders.id',
                                function ($q) {
                                    $q->select('purchase_order_items.purchase_order_id')
                                        ->from('purchase_order_items')
                                        ->leftJoin(
                                            'purchase_invoice_items',
                                            'purchase_invoice_items.purchase_order_item_id',
                                            '=',
                                            'purchase_order_items.id'
                                        )
                                        ->groupBy(
                                            'purchase_order_items.id',
                                            'purchase_order_items.quantity',
                                            'purchase_order_items.purchase_order_id'
                                        )
                                        ->havingRaw('ROUND(SUM(COALESCE(purchase_invoice_items.qty_invoiced,0)), 3) < ROUND(purchase_order_items.quantity, 3)');
                                }
                            );
                    });

                    if ($invoice && $invoice->po_reference) {
                        $poNumbers = explode(', ', $invoice->po_reference);
                        $query->orWhereIn('purchase_orders.po_number', $poNumbers);
                    } elseif ($invoice && $invoice->purchase_order_id) {
                        $query->orWhere('purchase_orders.id', $invoice->purchase_order_id);
                    }
                })
                ->orderBy('purchase_orders.id', 'asc')
                ->get();
        }
        $brands = Brand::active()->orderBy('brand_name')->get();
        $fabricSizes = FabricSize::active()->orderBy('width')->get();
        $fabricTypes = FabricType::active()->orderBy('fabric_type')->get();
        $suppliers = Supplier::where('status', 'Active')->get();
        $paid_so_far = $invoice ? $invoice->payments()->sum('amount') : 0;
        $nextInvoiceNumber = '';

        return view('purchase_invoice.add', compact('invoice', 'purchaseOrders', 'suppliers', 'charges', 'paid_so_far', 'nextInvoiceNumber', 'brands', 'fabricSizes', 'fabricTypes'));
    }

    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view_details purchase-invoice')) {
            return unauthorizedRedirect();
        }
        $invoice = PurchaseInvoice::with([
            'supplier',
            'items.rawMaterial',
            'items.uom',
            'items.brand',
            'items.fabricWidth',
            'items.purchaseOrderItem.brand',
            'items.purchaseOrderItem.fabricWidth',
            'items.purchaseOrderItem.storeCategory',
            'charges',
            'purchaseCommissionAgent'
        ])->findOrFail($id);
        return view('purchase_invoice.view_details', compact('invoice'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete purchase-invoice')) {
            return unauthorizedRedirect();
        }
        $invoice = PurchaseInvoice::findOrFail($id);

        if ($invoice->auth_signature) {
            $filePath = public_path('uploads/purchase_invoices/' . $invoice->auth_signature);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        if ($invoice->attachments) {
            $filePath = public_path('uploads/purchase_invoices/' . $invoice->attachments);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $invoice->delete();
        return redirect('purchase_invoices')->with('success', 'Purchase Invoice deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $invoice = PurchaseInvoice::findOrFail($id);
        $oldData = $invoice->toArray();
        $invoice->invoice_status = $request->status;
        $invoice->save();
        $newData = $invoice->toArray();
        addLog('update_status', 'Purchase Invoice Status', 'purchase_invoices', $id, $oldData, $newData);
        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    public function getPurchaseOrderDetails($id)
    {
        $purchaseOrder = PurchaseOrder::with([
            'supplier',
            'items.rawMaterial',
            'items.uom',
            'items.storeCategory',
            'items.brand',
            'items.fabricWidth',
            'items.fabricType'
        ])->findOrFail($id);

        $items = $purchaseOrder->items->map(function ($item) {
            $alreadyInvoicedQty = PurchaseInvoiceItem::where(
                'purchase_order_item_id',
                $item->id
            )->sum('quantity');

            $balanceQty = round($item->quantity - $alreadyInvoicedQty, 3);

            if ($balanceQty <= 0) {
                return null;
            }

            return [
                'id' => $item->id,
                'store_category_id' => $item->store_category_id,
                'store_category_name' => $item->storeCategory->category_name ?? '-',
                'raw_material_id' => $item->raw_material_id,
                'raw_material_name' => $item->rawMaterial->name,
                'art_no' => $item->supplier_design_name,
                'hsn_code' => $item->rawMaterial->hsn_code ?? '',
                'brand_id' => $item->brand_id,
                'brand_name' => $item->brand->brand_name ?? '-',
                'fabric_width_id' => $item->fabric_width_id,
                'fabric_width' => $item->fabricWidth->width ?? '-',
                'fabric_type_id' => $item->fabric_type_id,
                'fabric_type_name' => $item->fabricType->fabric_type ?? '-',
                'quantity' => $balanceQty,
                'qty_ordered' => $item->quantity,
                'qty_invoiced' => $alreadyInvoicedQty,
                'balance_qty' => $balanceQty,
                'uom_id' => $item->uom_id,
                'uom_code' => $item->uom->uom_code,
                'rate' => $item->rate,
                'amount' => $item->amount,
            ];
        })->filter()->values();

        return response()->json([
            'success' => true,
            'po_number' => $purchaseOrder->po_number,
            'supplier_id' => $purchaseOrder->supplier_id,
            'supplier_state_id' => $purchaseOrder->supplier->state_id ?? null,
            'supplier_name' => $purchaseOrder->supplier->name . ($purchaseOrder->supplier->code ? ' - ' . $purchaseOrder->supplier->code : ''),
            'discount_percent' => $purchaseOrder->discount_percent,
            'commission' => $purchaseOrder->commission ?? 0,
            'purchase_commission_agent_id' => $purchaseOrder->purchase_commission_agent_id,
            'purchase_commission_agent_name' => $purchaseOrder->purchaseCommissionAgent->name ?? '',
            'agent_commission_percentage' => $purchaseOrder->supplier->commission_percentage ?? 0,
            'round_off' => $purchaseOrder->round_off,
            'round_off_type' => $purchaseOrder->round_off_type,
            'igst_percent' => $purchaseOrder->igst_percent,
            'cgst_percent' => $purchaseOrder->cgst_percent,
            'sgst_percent' => $purchaseOrder->sgst_percent,
            'all_brands' => Brand::orderBy('brand_name')->get()->map(function($b) {
                return ['id' => $b->id, 'name' => $b->brand_name];
            }),
            'all_fabric_widths' => FabricSize::orderBy('width')->get()->map(function($f) {
                return ['id' => $f->id, 'name' => $f->width];
            }),
            'all_fabric_types' => FabricType::orderBy('fabric_type')->get()->map(function($f) {
                return ['id' => $f->id, 'name' => $f->fabric_type];
            }),
            'items' => $items,
        ]);
    }
    public function getPurchaseOrdersBySupplier($supplier_id)
    {
        $purchaseOrders = PurchaseOrder::where('status', 'Approved')
            ->where('supplier_id', $supplier_id)
            ->where(function ($query) {
                $query->whereDoesntHave('items', function ($query) {
                    $query->join('purchase_invoice_items', 'purchase_invoice_items.purchase_order_item_id', '=', 'purchase_order_items.id');
                })
                ->orWhereHas('items', function ($query) {
                    $query->select('purchase_order_items.id', 'purchase_order_items.quantity', 'purchase_order_items.purchase_order_id')
                        ->join('purchase_invoice_items', 'purchase_invoice_items.purchase_order_item_id', '=', 'purchase_order_items.id')
                        ->groupBy('purchase_order_items.id', 'purchase_order_items.quantity', 'purchase_order_items.purchase_order_id')
                        ->havingRaw('ROUND(SUM(COALESCE(purchase_invoice_items.qty_invoiced,0)), 3) < ROUND(purchase_order_items.quantity, 3)');
                });
            })
            ->orderBy('id', 'desc')
            ->get(['id', 'po_number']);

        return response()->json([
            'success' => true,
            'purchase_orders' => $purchaseOrders
        ]);
    }
 public function getPurchaseOrderDetailsMulti(Request $request)
    {
        $poIds = $request->po_ids ?? [];
        if (empty($poIds)) {
            return response()->json(['success' => false, 'message' => 'No Purchase Orders provided.']);
        }

        $purchaseOrders = PurchaseOrder::with([
            'supplier',
            'items.rawMaterial',
            'items.uom',
            'items.storeCategory',
            'items.brand',
            'items.fabricWidth',
            'items.fabricType',
            'purchaseCommissionAgent'
        ])->whereIn('id', $poIds)->get();

        if ($purchaseOrders->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Purchase Orders not found.']);
        }

        $firstPo = $purchaseOrders->first();
        $allItems = collect();
        $poNumbers = [];

        foreach ($purchaseOrders as $purchaseOrder) {
            $poNumbers[] = $purchaseOrder->po_number;
            $items = $purchaseOrder->items->map(function ($item) use ($purchaseOrder) {
                $alreadyInvoicedQty = PurchaseInvoiceItem::where('purchase_order_item_id', $item->id)->sum('quantity');
                $balanceQty = round($item->quantity - $alreadyInvoicedQty, 3);
                if ($balanceQty <= 0) return null;

                return [
                    'id' => $item->id,
                    'store_category_id' => $item->store_category_id,
                    'store_category_name' => $item->storeCategory->category_name ?? '-',
                    'raw_material_id' => $item->raw_material_id,
                    'raw_material_name' => $item->rawMaterial->name,
                    'art_no' => $item->supplier_design_name,
                    'hsn_code' => $item->rawMaterial->hsn_code ?? '',
                    'brand_id' => $item->brand_id,
                    'brand_name' => $item->brand->brand_name ?? '-',
                    'fabric_width_id' => $item->fabric_width_id,
                    'fabric_width' => $item->fabricWidth->width ?? '-',
                    'fabric_type_id' => $item->fabric_type_id,
                    'fabric_type_name' => $item->fabricType->fabric_type ?? '-',
                    'quantity' => $balanceQty,
                    'qty_ordered' => $item->quantity,
                    'qty_invoiced' => $alreadyInvoicedQty,
                    'balance_qty' => $balanceQty,
                    'uom_id' => $item->uom_id,
                    'uom_code' => $item->uom->uom_code,
                    'rate' => $item->rate,
                    'amount' => $item->amount,
                    'po_number' => $purchaseOrder->po_number // add PO number for display
                ];
            })->filter();

            $allItems = $allItems->concat($items);
        }

        return response()->json([
            'success' => true,
            'po_numbers' => implode(', ', $poNumbers),
            'supplier_id' => $firstPo->supplier_id,
            'supplier_state_id' => $firstPo->supplier->state_id ?? null,
            'supplier_name' => $firstPo->supplier->name . ($firstPo->supplier->code ? ' - ' . $firstPo->supplier->code : ''),
            'discount_percent' => $firstPo->discount_percent,
            'commission' => $firstPo->commission ?? 0,
            'purchase_commission_agent_id' => $firstPo->purchase_commission_agent_id,
            'purchase_commission_agent_name' => $firstPo->purchaseCommissionAgent->name ?? '',
            'agent_commission_percentage' => $firstPo->supplier->commission_percentage ?? 0,
            'round_off' => $firstPo->round_off,
            'round_off_type' => $firstPo->round_off_type,
            'igst_percent' => $firstPo->igst_percent,
            'cgst_percent' => $firstPo->cgst_percent,
            'sgst_percent' => $firstPo->sgst_percent,
            'all_brands' => Brand::orderBy('brand_name')->get()->map(function($b) { return ['id' => $b->id, 'name' => $b->brand_name]; }),
            'all_fabric_widths' => FabricSize::orderBy('width')->get()->map(function($f) { return ['id' => $f->id, 'name' => $f->width]; }),
            'all_fabric_types' => FabricType::orderBy('fabric_type')->get()->map(function($f) { return ['id' => $f->id, 'name' => $f->fabric_type]; }),
            'items' => $allItems->values(),
        ]);
    }

    public function downloadPdf($id)
    {
        $invoice = PurchaseInvoice::with([
            'supplier.state',
            'supplier.city',
            'items.rawMaterial',
            'items.uom',
            'charges'
        ])->findOrFail($id);
        $setting = Setting::with(['city', 'state'])->first();
        $totalInWords = numberToWords($invoice->grand_total);
        $pdf = Pdf::loadView('purchase_invoice.purchase_invoice_pdf', compact('invoice', 'setting', 'totalInWords'));
        $pdf->setPaper('A4', 'portrait');
        $safeInvoiceNo = str_replace(['/', '\\'], '_', $invoice->invoice_no);
        return $pdf->stream('Purchase_Invoice_' . $safeInvoiceNo . '.pdf');
    }

    public function print($id)
    {
        $invoice = PurchaseInvoice::with([
            'supplier.state',
            'supplier.city',
            'items.rawMaterial',
            'items.uom',
            'charges'
        ])->findOrFail($id);
        $setting = Setting::with(['city', 'state'])->first();
        $totalInWords = numberToWords($invoice->grand_total);
        $is_print = true;
        return view('purchase_invoice.purchase_invoice_pdf', compact('invoice', 'setting', 'totalInWords', 'is_print'));
    }
    public function deleteCharge($id)
    {
        try {
            $charge = PurchaseInvoiceCharge::findOrFail($id);
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

    public function getPaymentHistory($id)
    {
        $payments = PurchaseInvoicePayment::where('purchase_invoice_id', $id)->orderBy('payment_date', 'desc')->get();
        return response()->json([
            'success' => true,
            'payments' => $payments
        ]);
    }
}

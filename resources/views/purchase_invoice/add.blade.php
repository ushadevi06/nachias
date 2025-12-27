@extends('layouts.common')
@section('title', ($invoice ? 'Edit' : 'Add') . ' Purchase Invoice - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="{{ $invoice ? url('purchase_invoices/add/' . $invoice->id) : url('purchase_invoices/add') }}" method="POST" enctype="multipart/form-data" class="common-form">
                @csrf
                <input type="hidden" id="isEditMode" value="{{ isset($invoice) ? 1 : 0 }}">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>{{ $invoice ? 'Edit' : 'Add' }} Purchase Invoice</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('invoice_no') is-invalid @enderror"
                                        id="invoice_no" placeholder="Enter Invoice No" name="invoice_no"
                                        value="{{ old('invoice_no', $invoice->invoice_no ?? '') }}" {{ isset($invoice) ? 'readonly' : '' }}>
                                    <label for="invoice_no">Invoice No. <span class="text-danger">*</span></label>
                                </div>
                                @error('invoice_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control invoice_date @error('invoice_date') is-invalid @enderror"
                                        placeholder="Enter Invoice Date" name="invoice_date"
                                        autocomplete="off"
                                        value="{{ old('invoice_date', $invoice ? $invoice->invoice_date->format('d-m-Y') : '') }}" {{ isset($invoice) ? 'readonly' : '' }} />
                                    <label for="invoice_date">Invoice Date <span class="text-danger">*</span></label>
                                </div>
                                @error('invoice_date')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="purchase_order_id" id="purchase_order" class="form-select select2 @error('purchase_order_id') is-invalid @enderror" data-placeholder="Select Purchase Order" {{ isset($invoice) ? 'disabled' : '' }}>
                                        <option value="">Select Purchase Order</option>
                                        @foreach($purchaseOrders as $po)
                                        <option value="{{ $po->id }}"
                                            {{ old('purchase_order_id', $invoice->purchase_order_id ?? '') == $po->id ? 'selected' : '' }}>
                                            {{ $po->po_number }} - {{ $po->supplier->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @if(isset($invoice))
                                        <input type="hidden" name="purchase_order_id" value="{{ $invoice->purchase_order_id }}">
                                    @endif
                                    <label for="purchase_order">Purchase Order No <span class="text-danger">*</span></label>
                                </div>
                                @error('purchase_order_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="purchase_order_no"
                                        name="purchase_order_no" readonly
                                        value="{{ old('purchase_order_no', $invoice->purchase_order_no ?? '') }}">
                                    <label for="purchase_order_no">PO Number</label>
                                </div>
                            </div> --}}
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text"
                                        class="form-control @error('supplier_name') is-invalid @enderror"
                                        id="supplier_name"
                                        readonly
                                        value="{{ old('supplier_name', $invoice->supplier->name ?? '') }}">

                                    {{-- Hidden field to store supplier_name for old() --}}
                                    <input type="hidden"
                                        name="supplier_name"
                                        id="supplier_name_hidden"
                                        value="{{ old('supplier_name', $invoice->supplier->name ?? '') }}">

                                    <input type="hidden"
                                        name="supplier_id"
                                        id="supplier_id"
                                        value="{{ old('supplier_id', $invoice->supplier_id ?? '') }}">

                                    <label for="supplier_name">Supplier <span class="text-danger">*</span></label>
                                </div>
                                @error('supplier_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('po_reference') is-invalid @enderror" id="po_reference"
                                        placeholder="Enter PO Reference" name="po_reference"
                                        value="{{ old('po_reference', $invoice->po_reference ?? '') }}">
                                    <label for="po_reference">PO Reference</label>
                                </div>
                                @error('po_reference')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Item Details</h4>
                        </div>
                        @error('items')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                        <div id="item-rows" class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th width="50px">
                                            <input type="checkbox" id="select_all_items" class="form-check-input">
                                        </th>
                                        <th>Material</th>
                                        <th>HSN Code</th>
                                        <th>Ordered Qty</th>
                                        <th>Invoiced Qty</th>
                                        <th>Balanced Qty</th>
                                        <th>Received Qty <span class="text-danger">*</span></th>
                                        <th>UOM</th>
                                        <th>Rate</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>

                                <tbody id="items_tbody">
                                    @if(old('items'))
                                    @foreach(old('items') as $index => $item)
                                    <tr class="item-row">
                                        <td>
                                            <input type="checkbox"
                                                name="items[{{ $index }}][selected]"
                                                value="1"
                                                class="form-check-input item-checkbox"
                                                {{ isset($item['selected']) ? 'checked' : '' }}>

                                            <input type="hidden" name="items[{{ $index }}][purchase_order_item_id]" value="{{ $item['purchase_order_item_id'] ?? '' }}">
                                            <input type="hidden" name="items[{{ $index }}][raw_material_id]" value="{{ $item['raw_material_id'] ?? '' }}">
                                            <input type="hidden" name="items[{{ $index }}][raw_material_name]" value="{{ $item['raw_material_name'] ?? '' }}">
                                            <input type="hidden" name="items[{{ $index }}][uom_id]" value="{{ $item['uom_id'] ?? '' }}">
                                            <input type="hidden" name="items[{{ $index }}][uom_code]" value="{{ $item['uom_code'] ?? '' }}">
                                            <input type="hidden" name="items[{{ $index }}][rate]" value="{{ $item['rate'] ?? 0 }}" class="item-rate">
                                            <input type="hidden" name="items[{{ $index }}][qty_ordered]" value="{{ $item['qty_ordered'] ?? 0 }}" class="qty-ordered-val">
                                            <input type="hidden" name="items[{{ $index }}][qty_invoiced]" value="{{ $item['qty_invoiced'] ?? 0 }}" class="qty-invoiced-val">
                                        </td>

                                        <td>{{ $item['raw_material_name'] ?? '-' }}</td>

                                        <td>
                                            <input type="text"
                                                name="items[{{ $index }}][hsn_code]"
                                                class="form-control form-control-sm item-hsn @error('items.'.$index.'.hsn_code') is-invalid @enderror"
                                                value="{{ $item['hsn_code'] ?? '' }}"
                                                {{ isset($item['selected']) ? '' : 'readonly' }}>
                                            @error('items.'.$index.'.hsn_code')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </td>

                                        <td class="qty-ordered-display">{{ $item['qty_ordered'] ?? 0 }}</td>
                                        <td class="qty-invoiced-display">{{ $item['qty_invoiced'] ?? 0 }}</td>
                                        <td class="balanced-qty-display">
                                            {{ ($item['qty_ordered'] ?? 0) - ($item['qty_invoiced'] ?? 0) }}
                                        </td>

                                        <td>
                                            <input type="number"
                                                name="items[{{ $index }}][quantity]"
                                                class="form-control form-control-sm item-quantity received-qty-input @error('items.'.$index.'.quantity') is-invalid @enderror"
                                                value="{{ $item['quantity'] ?? '' }}"
                                                step="0.01"
                                                data-max-qty="{{ ($item['qty_ordered'] ?? 0) - ($item['qty_invoiced'] ?? 0) }}"
                                                {{ isset($item['selected']) ? '' : 'readonly' }}>

                                            @error("items.$index.quantity")
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </td>

                                        <td>{{ $item['uom_code'] ?? '-' }}</td>
                                        <td class="rate-display">{{ number_format($item['rate'] ?? 0, 2) }}</td>
                                        <td class="item-amount">
                                            {{ number_format(($item['quantity'] ?? 0) * ($item['rate'] ?? 0), 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                    @elseif(isset($invoice) && $invoice && $invoice->items->count())
                                    @foreach($invoice->items as $index => $invItem)
                                    @php
                                        $balancedQty = $invItem->qty_ordered - $invItem->qty_invoiced;
                                    @endphp
                                    <tr class="item-row">
                                        <td>
                                            <input type="checkbox"
                                                name="items[{{ $index }}][selected]"
                                                value="1"
                                                class="form-check-input item-checkbox"
                                                checked>

                                            <input type="hidden" name="items[{{ $index }}][purchase_order_item_id]" value="{{ $invItem->purchase_order_item_id }}">
                                            <input type="hidden" name="items[{{ $index }}][raw_material_id]" value="{{ $invItem->raw_material_id }}">
                                            <input type="hidden" name="items[{{ $index }}][raw_material_name]" value="{{ $invItem->rawMaterial->name ?? '' }}">
                                            <input type="hidden" name="items[{{ $index }}][uom_id]" value="{{ $invItem->uom_id }}">
                                            <input type="hidden" name="items[{{ $index }}][uom_code]" value="{{ $invItem->uom->uom_code ?? '' }}">
                                            <input type="hidden" name="items[{{ $index }}][rate]" value="{{ $invItem->rate }}" class="item-rate">
                                            <input type="hidden" name="items[{{ $index }}][qty_ordered]" value="{{ $invItem->qty_ordered }}" class="qty-ordered-val">
                                            <input type="hidden" name="items[{{ $index }}][qty_invoiced]" value="{{ $invItem->qty_invoiced }}" class="qty-invoiced-val">
                                        </td>

                                        <td>{{ $invItem->rawMaterial->name ?? '-' }}</td>

                                        <td>
                                            <input type="text"
                                                name="items[{{ $index }}][hsn_code]"
                                                class="form-control form-control-sm item-hsn @error('items.'.$index.'.hsn_code') is-invalid @enderror"
                                                value="{{ $invItem->hsn_code }}">
                                            @error('items.'.$index.'.hsn_code')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </td>

                                        <td class="qty-ordered-display">{{ $invItem->qty_ordered }}</td>
                                        <td class="qty-invoiced-display">{{ $invItem->qty_invoiced }}</td>
                                        <td class="balanced-qty-display">{{ $balancedQty }}</td>

                                        <td>
                                            <input type="number"
                                                name="items[{{ $index }}][quantity]"
                                                class="form-control form-control-sm received-qty-input @error('items.'.$index.'.quantity') is-invalid @enderror"
                                                value="{{ $invItem->quantity }}"
                                                step="0.01"
                                                data-max-qty="{{ $balancedQty }}">
                                            @error('items.'.$index.'.quantity')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </td>

                                        <td>{{ $invItem->uom->uom_code ?? '-' }}</td>
                                        <td class="rate-display">{{ number_format($invItem->rate, 2) }}</td>
                                        <td class="item-amount">
                                            {{ number_format($invItem->quantity * $invItem->rate, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach



                                    {{-- 3️⃣ EMPTY --}}
                                    @else
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">
                                            Please select a Purchase Order to load items
                                        </td>
                                    </tr>
                                    @endif

                                </tbody>


                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Tax & Charges</h4>
                        </div>

                        <div class="row g-4">

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="charges_select" class="select2 form-select @error('charges_select') is-invalid @enderror" data-placeholder="Select Charge">
                                        <option value="">Loading charges...</option>
                                    </select>
                                    <label>Charges <span class="text-danger">*</span></label>
                                </div>
                                @error('charges_select')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" min="0" step="0.01" class="form-control @error('charge_amount') is-invalid @enderror" id="charge_amount"
                                        placeholder="Charge Amount">
                                    <label>Amount</label>
                                </div>
                                @error('charge_amount')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 col-xl-4 d-flex align-items-center">
                                <button type="button" id="add_charge_btn" class="btn btn-primary">
                                    Add Charge
                                </button>
                            </div>

                        </div>
                        <div class="table-responsive mt-4 {{ $charges->count() ? '' : 'd-none' }}" id="charges_table">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Charge Name</th>
                                        <th>Amount</th>
                                        <th width="80px">Action</th>
                                    </tr>
                                </thead>

                                <tbody id="added_charges_list">
                                    @foreach($charges as $charge)
                                    @php
                                    $chargeId = is_array($charge) ? ($charge['charge_id'] ?? '') : $charge->charge_id;
                                    $chargeName = is_array($charge) ? $charge['name'] : $charge->charge_name;
                                    $chargeAmount = is_array($charge) ? $charge['amount'] : $charge->charge_amount;
                                    $invoiceChargeId = is_array($charge) ? ($charge['id'] ?? null) : $charge->id;
                                    @endphp

                                    <tr class="charge-row"
                                        data-charge-id="{{ $chargeId }}"
                                        data-invoice-charge-id="{{ $invoiceChargeId }}">
                                        <td>
                                            {{ $chargeName }}
                                            <input type="hidden" name="charges[charge_id][]" value="{{ $chargeId }}">
                                            <input type="hidden" name="charges[name][]" value="{{ $chargeName }}">
                                        </td>
                                        <td>
                                            {{ number_format($chargeAmount, 2) }}
                                            <input type="hidden" name="charges[amount][]" value="{{ $chargeAmount }}">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-charge {{ isset($invoice) ? 'disabled' : '' }}">X</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row g-4 align-items-start">
                            <div class="col-lg-6">
                                <div class="card p-3 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="mb-3 fw-semibold">Invoice Details</h5>
                                        <div class="form-floating form-floating-outline mb-3">
                                            @php
                                            $currentStatus = old('invoice_status', $invoice->invoice_status ?? '');
                                            @endphp
                                            <select id="invoice_status" name="invoice_status" class="select2 form-select @error('invoice_status') is-invalid @enderror" data-placeholder="Select Invoice Status">
                                                <option value="">Select Invoice Status</option>
                                                @foreach(['Draft', 'Unpaid/Credit', 'Paid', 'Partially Paid'] as $status)

                                                @php
                                                $disabled = false;
                                                $selected = false;

                                                if ($currentStatus === 'Paid') {
                                                $disabled = true;
                                                $selected = ($status === 'Paid');
                                                } elseif ($currentStatus === 'Partially Paid') {
                                                $disabled = true;
                                                $selected = ($status === 'Unpaid/Credit');
                                                } elseif ($currentStatus === 'Unpaid/Credit') {
                                                $disabled = ($status !== 'Unpaid/Credit');
                                                $selected = ($status === 'Unpaid/Credit');
                                                } elseif ($currentStatus === 'Draft') {
                                                $disabled = ($status !== 'Draft');
                                                $selected = ($status === 'Draft');
                                                }
                                                @endphp

                                                <option value="{{ $status }}" {{ $selected ? 'selected' : '' }} {{ $disabled ? 'disabled' : '' }}>
                                                    {{ $status }}
                                                </option>
                                                @endforeach
                                                @if(isset($invoice))
                                                <input type="hidden" name="invoice_status" value="{{ $currentStatus }}">
                                                @endif

                                            </select>
                                            <label for="invoice_status">Invoice Status <span class="text-danger">*</span></label>
                                        </div>
                                        @error('invoice_status')
                                        <div class="text-danger mb-3">{{ $message }}</div>
                                        @enderror

                                        <div class="form-floating form-floating-outline mb-3">
                                            <select id="payment_mode" name="payment_mode" class="select2 form-select @error('payment_mode') is-invalid @enderror" data-placeholder="Select Payment Mode">
                                                <option value="">Select Payment Mode</option>
                                                <option value="Bank Transfer" {{ old('payment_mode', $invoice->payment_mode ?? '') == 'Bank Transfer' ? 'selected' : '' }}>
                                                    Bank Transfer
                                                </option>
                                                <option value="Cheque" {{ old('payment_mode', $invoice->payment_mode ?? '') == 'Cheque' ? 'selected' : '' }}>
                                                    Cheque
                                                </option>
                                                <option value="UPI" {{ old('payment_mode', $invoice->payment_mode ?? '') == 'UPI' ? 'selected' : '' }}>
                                                    UPI
                                                </option>
                                                <option value="Cash" {{ old('payment_mode', $invoice->payment_mode ?? '') == 'Cash' ? 'selected' : '' }}>
                                                    Cash
                                                </option>
                                            </select>
                                            <label for="payment_mode">Payment Mode</label>
                                        </div>
                                        @error('payment_mode')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                        
                                        <div class="form-floating form-floating-outline mb-3 d-none" id="transaction_id_div">
                                            <input type="text" name="transaction_id" id="transaction_id" class="form-control @error('transaction_id') is-invalid @enderror" placeholder="Enter details" value="{{ old('transaction_id', $invoice->transaction_id ?? '') }}">
                                            <label for="transaction_id" id="transaction_id_label">Transaction Details</label>
                                        </div>
                                        @error('transaction_id')
                                        <div class="text-danger mt-1 mb-3">{{ $message }}</div>
                                        @enderror

                                        <div class="form-floating form-floating-outline mb-3">
                                            <input type="text" class="form-control due_date @error('due_date') is-invalid @enderror" placeholder="Enter Due Date" name="due_date" autocomplete="off" value="{{ old('due_date', $invoice && $invoice->due_date ? $invoice->due_date->format('d-m-Y') : '') }}" />
                                            <label for="due_date">Due Date</label>
                                        </div>
                                        @error('due_date')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror

                                        <div class="form-floating form-floating-outline mb-3">
                                            <textarea name="notes" id="notes" class="form-control h-px-100 @error('notes') is-invalid @enderror" placeholder="Enter Additional Notes">{{ old('notes', $invoice->notes ?? '') }}</textarea>
                                            <label for="notes">Additional Notes</label>
                                        </div>
                                        @error('notes')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror

                                        <div class="mb-3">
                                            <div class="form-floating form-floating-outline mb-3">
                                                <input type="file"
                                                    class="form-control @error('auth_sign') is-invalid @enderror"
                                                    id="auth_sign"
                                                    name="auth_sign"
                                                    accept="image/*">
                                                <label for="auth_sign">Authorized Signature / Stamp Upload</label>
                                            </div>

                                            @if(!empty($invoice->auth_sign))
                                            <a href="javascript:void(0)"
                                                class="view-image mt-1 d-inline-block"
                                                data-image="{{ url('uploads/purchase_invoices/' . $invoice->auth_sign) }}">
                                                <i class="ri ri-image-line"></i> View
                                            </a>
                                            @endif

                                            @error('auth_sign')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-floating form-floating-outline mb-3">
                                            <input type="file"
                                                class="form-control @error('attachments') is-invalid @enderror"
                                                id="attachments"
                                                name="attachments">
                                            <label for="attachments">Attachments</label>
                                        </div>

                                        @if(!empty($invoice->attachments))
                                        <a href="{{ url('uploads/purchase_invoices/' . $invoice->attachments) }}"
                                            target="_blank"
                                            class="mt-1 d-inline-block">
                                            <i class="ri ri-attachment-2"></i> View
                                        </a>
                                        @endif

                                        @error('attachments')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            @php
                                $subTotal = old('sub_total', $invoice->sub_total ?? 0);
                                $discountPercent = old('discount_percent', $invoice->discount_percent ?? 0);
                                $discountAmount = old('discount_amount', $invoice->discount_amount ?? 0);
                                $taxableAmount = old('taxable_amount', $invoice->taxable_amount ?? 0);

                                $otherState = old('other_state', isset($invoice) && $invoice->other_state ? 'Y' : 'N');

                                $igstPercent = old('igst_percent', $invoice->igst_percent ?? $web_settings->igst);
                                $igstAmount = old('igst_amount', $invoice->igst_amount ?? 0);

                                $cgstPercent = old('cgst_percent', $invoice->cgst_percent ?? $web_settings->cgst);
                                $cgstAmount = old('cgst_amount', $invoice->cgst_amount ?? 0);

                                $sgstPercent = old('sgst_percent', $invoice->sgst_percent ?? $web_settings->sgst);
                                $sgstAmount = old('sgst_amount', $invoice->sgst_amount ?? 0);

                                $taxAmount = old('tax_amount', $invoice->tax_amount ?? 0);
                                $otherCharges = old('other_charges', $invoice->other_charges ?? 0);
                                $grandTotal = old('grand_total', $invoice->grand_total ?? 0);
                                $receivedAmt = old('received_amount', $invoice->received_amount ?? 0);
                                $dueAmount = old('due_amount', $invoice->due_amount ?? 0);
                            @endphp

                            <div class="col-lg-6">
                                <div class="p-3">
                                    <h5 class="fw-semibold mb-3">Invoice Summary</h5>

                                    {{-- Sub Total --}}
                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <span>Sub total:</span>
                                        <strong id="subtotal">{{ number_format($subTotal, 2) }}</strong>
                                        <input type="hidden" name="sub_total" id="sub_total_input" value="{{ $subTotal }}">
                                    </div>

                                    {{-- Discount --}}
                                    <div class="d-flex justify-content-between py-2 border-bottom align-items-center">
                                        <span>Discount:</span>
                                        <div class="d-flex gap-2 align-items-center">
                                            <div class="input-group input-group-sm" style="width:120px;">
                                                <input type="number" name="discount_percent" id="discount_input" class="form-control text-end @error('discount_percent') is-invalid @enderror" value="{{ $discountPercent }}" min="0" max="100" step="0.01" {{ isset($invoice) ? 'readonly' : '' }}>
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <strong id="discount_value">{{ number_format($discountAmount, 2) }}</strong>
                                            <input type="hidden" name="discount_amount" id="discount_amount_input" value="{{ $discountAmount }}">
                                        </div>
                                    </div>
                                    @error('discount_percent')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror

                                    {{-- Taxable --}}
                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <span>Total:</span>
                                        <strong id="total">{{ number_format($taxableAmount, 2) }}</strong>
                                        <input type="hidden" name="taxable_amount" id="taxable_amount_input" value="{{ $taxableAmount }}">
                                    </div>

                                    {{-- Other State --}}
                                    <div class="py-3 border-bottom">
                                        <label class="fw-semibold mb-2 d-block">Other State?</label>
                                        <div class="d-flex gap-4">
                                            <div class="form-check">
                                                <input class="form-check-input @error('other_state') is-invalid @enderror" type="radio" name="other_state" value="Y" {{ $otherState === 'Y' ? 'checked' : '' }} {{ isset($invoice) ? 'disabled' : '' }}>
                                                <label class="form-check-label">Yes</label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input @error('other_state') is-invalid @enderror" type="radio" name="other_state" value="N" {{ $otherState === 'N' ? 'checked' : '' }} {{ isset($invoice) ? 'disabled' : '' }}>
                                                <label class="form-check-label">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    @error('other_state')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror

                                    {{-- IGST --}}
                                    <div id="igst_div" class="py-2 border-bottom" style="{{ $otherState === 'Y' ? '' : 'display:none;' }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>IGST</span>
                                            <div class="d-flex gap-2 align-items-center">
                                                <input type="number" name="igst_percent" id="igst_percent" value="{{ $igstPercent }}" class="form-control form-control-sm text-end @error('igst_percent') is-invalid @enderror" style="width:80px;">
                                                <span>%</span>
                                                <strong id="igst_amt">{{ number_format($igstAmount, 2) }}</strong>
                                                <input type="hidden" name="igst_amount" id="igst_amount_input" value="{{ $igstAmount }}">
                                            </div>
                                        </div>
                                        @error('igst_percent')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- CGST / SGST --}}
                                    <div id="cgst_sgst_div" class="py-2 border-bottom" style="{{ $otherState === 'N' ? '' : 'display:none;' }}">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>CGST</span>
                                            <div class="d-flex gap-2 align-items-center">
                                                <input type="number" name="cgst_percent" id="cgst_percent" value="{{ $cgstPercent }}" class="form-control form-control-sm text-end @error('cgst_percent') is-invalid @enderror" style="width:80px;" readonly>
                                                <span>%</span>
                                                <strong id="cgst_amt">{{ number_format($cgstAmount, 2) }}</strong>
                                                <input type="hidden" name="cgst_amount" id="cgst_amount_input" value="{{ $cgstAmount }}">
                                            </div>
                                        </div>
                                        @error('cgst_percent')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror

                                        <div class="d-flex justify-content-between">
                                            <span>SGST</span>
                                            <div class="d-flex gap-2 align-items-center">
                                                <input type="number" name="sgst_percent" id="sgst_percent" value="{{ $sgstPercent }}" class="form-control form-control-sm text-end @error('sgst_percent') is-invalid @enderror" style="width:80px;" readonly>
                                                <span>%</span>
                                                <strong id="sgst_amt">{{ number_format($sgstAmount, 2) }}</strong>
                                                <input type="hidden" name="sgst_amount" id="sgst_amount_input" value="{{ $sgstAmount }}">

                                            </div>
                                        </div>
                                        @error('sgst_percent')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Tax Amount --}}
                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <span>Tax Amount:</span>
                                        <strong id="tax_amount">{{ number_format($taxAmount, 2) }}</strong>
                                        <input type="hidden" name="tax_amount" id="tax_amount_input" value="{{ $taxAmount }}">
                                    </div>

                                    {{-- Other Charges --}}
                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <span>Other Charges:</span>
                                        <strong id="other_charges">{{ number_format($otherCharges, 2) }}</strong>
                                        <input type="hidden" name="other_charges" id="other_charges_input" value="{{ $otherCharges }}">
                                    </div>
                                    @error('other_charges')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror

                                    {{-- Grand Total --}}
                                    <div class="d-flex justify-content-between py-2 border-top fw-semibold">
                                        <span>Grand Total:</span>
                                        <strong id="grand_total">{{ number_format($grandTotal, 2) }}</strong>
                                        <input type="hidden" name="grand_total" id="grand_total_input" value="{{ $grandTotal }}">
                                    </div>
                                    @error('grand_total')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror

                                    @if(isset($invoice))
                                    {{-- Paid So Far --}}
                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <span>Paid So Far:</span>
                                        <div class="d-flex align-items-center">
                                            <strong id="paid_so_far_display">₹{{ number_format($paid_so_far, 2) }}</strong>
                                            <button type="button" class="btn btn-link p-0 ms-1 text-info" id="view_history_btn" title="View Payment History"><i class="ri ri-history-line" style="font-size: 1.1rem;"></i></button>
                                        </div>
                                        <input type="hidden" id="paid_so_far_input" value="{{ $paid_so_far }}">
                                    </div>
                                    @else
                                    <input type="hidden" id="paid_so_far_input" value="0">
                                    @endif

                                    {{-- Received --}}
                                    <div class="d-flex justify-content-between py-2 border-bottom align-items-center">
                                        <span>{{ isset($invoice) ? 'Add New Payment:' : 'Initial Payment:' }}</span>
                                        <div class="d-flex flex-column align-items-end">
                                            <input type="number" name="received_amount" id="received_amount_input" class="form-control form-control-sm text-end @error('received_amount') is-invalid @enderror" style="width:120px;" value="{{ isset($invoice) ? 0 : ($receivedAmt ?? 0) }}" step="0.01">
                                        </div>
                                    </div>
                                    @error('received_amount')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror

                                    {{-- Due --}}
                                    <div class="d-flex justify-content-between py-2 fw-semibold text-danger">
                                        <span>Due Amount:</span>
                                        <strong id="due_amount">{{ number_format($dueAmount, 2) }}</strong>
                                        <input type="hidden" name="due_amount" id="due_amount_input" value="{{ $dueAmount }}">
                                    </div>

                                    <div class="text-end mt-4">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a href="{{ url('purchase_invoices') }}" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Payment History Modal --}}
@if(isset($invoice))
<div class="modal fade" id="paymentHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Transaction History - {{ $invoice->invoice_no }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="payment_history_body">
                            {{-- Content loaded via AJAX --}}
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold bg-light">
                                <td colspan="1" class="text-end">Total Received:</td>
                                <td class="text-end" id="history_total_paid">₹0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            dropdownParent: $('body')
        });
        $('.invoice_date').flatpickr({
            dateFormat: 'd-m-Y',
            allowInput: true
        });

        function toggleTransactionId() {
            const mode = $('#payment_mode').val();
            const $div = $('#transaction_id_div');
            const $label = $('#transaction_id_label');
            const $input = $('#transaction_id');

            if (mode === 'Cheque') {
                $div.removeClass('d-none');
                $label.text('Cheque Number');
                $input.attr('placeholder', 'Enter Cheque Number');
            } else if (mode === 'UPI') {
                $div.removeClass('d-none');
                $label.text('UPI ID / Transaction ID');
                $input.attr('placeholder', 'Enter UPI ID / Transaction ID');
            } else {
                $div.addClass('d-none');
            }
        }

        $('#payment_mode').on('change', function() {
            toggleTransactionId();
        });

        // Initial call
        toggleTransactionId();
        $('#purchase_order').on('change', function() {
            let poId = $(this).val();
            if (poId) {
                $.ajax({
                    url: "{{ url('purchase_invoices/get-po-details') }}/" + poId,
                    type: "GET",
                    success: function(response) {
                        if (response.success) {
                            $('#purchase_order_no').val(response.po_number);
                            $('#supplier_id').val(response.supplier_id);
                            $('#supplier_name').val(response.supplier_name);
                            $('#supplier_name_hidden').val(response.supplier_name);

                            let itemsHtml = "";
                            response.items.forEach(function(item, index) {
                                // Calculate balanced qty
                                const balancedQty = item.qty_ordered - item.qty_invoiced;

                                itemsHtml += `
                        <tr class="item-row">
                            <td>
                                <input type="checkbox" name="items[${index}][selected]" 
                                    class="form-check-input item-checkbox" value="1">

                                <input type="hidden" name="items[${index}][purchase_order_item_id]" value="${item.id}">
                                <input type="hidden" name="items[${index}][raw_material_id]" value="${item.raw_material_id}">
                                <input type="hidden" name="items[${index}][raw_material_name]" value="${item.raw_material_name}">
                                <input type="hidden" name="items[${index}][uom_id]" value="${item.uom_id}">
                                <input type="hidden" name="items[${index}][uom_code]" value="${item.uom_code}">
                                <input type="hidden" name="items[${index}][rate]" value="${item.rate}" class="item-rate">
                                <input type="hidden" name="items[${index}][qty_ordered]" value="${item.qty_ordered}" class="qty-ordered-val">
                                <input type="hidden" name="items[${index}][qty_invoiced]" value="${item.qty_invoiced}" class="qty-invoiced-val">
                            </td>
                            <td>${item.raw_material_name}</td>
                            <td>
                                <input type="text" 
                                    name="items[${index}][hsn_code]"
                                    class="form-control form-control-sm item-hsn" 
                                    value="${item.hsn_code || ''}"
                                    placeholder="Enter HSN"
                                    readonly>
                            </td>
                            
                            <!-- Ordered Qty -->
                            <td class="qty-ordered-display">${item.qty_ordered}</td>
                            
                            <!-- Invoiced Qty -->
                            <td class="qty-invoiced-display">${item.qty_invoiced}</td>
                            
                            <!-- Balanced Qty -->
                            <td class="balanced-qty-display">${balancedQty.toFixed(2)}</td>

                            <!-- Received Qty (Input Field) -->
                            <td>
                                <input type="number" 
                                    name="items[${index}][quantity]"
                                    class="form-control form-control-sm item-quantity received-qty-input" 
                                    step="0.01"
                                    value="${balancedQty}"
                                    readonly
                                    placeholder="0.00"
                                    data-max-qty="${balancedQty}"
                                    data-ordered-qty="${item.qty_ordered}">
                            </td>

                            <td>${item.uom_code}</td>
                            <td class="rate-display">${parseFloat(item.rate).toFixed(2)}</td>
                            <td class="item-amount">0.00</td>

                        </tr>`;
                            });

                            $('#items_tbody').html(itemsHtml);
                            $('.item-row').each(function() {
                                let $row = $(this);
                                let qty = parseFloat($row.find('.item-quantity').val()) || 0;
                                let rate = parseFloat($row.find('.item-rate').val()) || 0;
                                let amount = qty * rate;
                                $row.find('.item-amount').text(amount.toFixed(2));
                            });

                            $('#select_all_items').prop('checked', false);

                            $('#subtotal').text('0.00');
                            $('#sub_total_input').val('0');
                            $('#discount_value').text('0.00');
                            $('#discount_amount_input').val('0');
                            $('#total').text('0.00');
                            $('#taxable_amount_input').val('0');
                            $('#tax_amount').text('0.00');
                            $('#tax_amount_input').val('0');
                            $('#other_charges').text('0.00');
                            $('#other_charges_input').val('0');
                            $('#grand_total').text('0.00');
                            $('#grand_total_input').val('0');
                            $('#due_amount').text('0.00');
                            $('#due_amount_input').val('0');
                            $('#cgst_amt').text('0.00');
                            $('#sgst_amt').text('0.00');
                            $('#igst_amt').text('0.00');

                            setTimeout(() => {
                                $('.item-row').each(function() {
                                    let $row = $(this);
                                    let checkbox = $row.find('.item-checkbox');

                                    toggleItemFields(checkbox);

                                    if (!checkbox.is(':checked')) {
                                        $row.find('.item-amount').text('0.00');
                                    }
                                });

                                calculateTotals();
                            }, 200);
                        }
                    },
                    error: function() {
                        alert("Failed to load purchase order details");
                    }
                });
            } else {
                $('#items_tbody').html('<tr><td colspan="10" class="text-center text-muted">Please select a Purchase Order to load items</td></tr>');
                $('#purchase_order_no').val('');
                $('#supplier_name').val('');
                $('#supplier_name_hidden').val('');
                $('#supplier_id').val('');
                $('#select_all_items').prop('checked', false);
                calculateTotals();
            }
        });

        // Add this event handler to auto-calculate balanced qty when received qty changes
        $(document).on('input', '.received-qty-input', function() {
            const $row = $(this).closest('tr');
            const receivedQty = parseFloat($(this).val()) || 0;
            const orderedQty = parseFloat($row.find('.qty-ordered-val').val()) || 0;
            const invoicedQty = parseFloat($row.find('.qty-invoiced-val').val()) || 0;
            const rate = parseFloat($row.find('.item-rate').val()) || 0;

            // Calculate balanced qty: Ordered - Invoiced - Received
            const balancedQty = orderedQty - invoicedQty - receivedQty;
            $row.find('.balanced-qty-display').text(balancedQty.toFixed(2));

            // Validate received qty doesn't exceed available balance
            const maxQty = orderedQty - invoicedQty;
            if (receivedQty > maxQty) {
                $(this).addClass('is-invalid');
                if (!$(this).next('.invalid-feedback').length) {
                    $(this).after('<div class="invalid-feedback d-block">Received quantity cannot exceed ' + maxQty.toFixed(2) + '</div>');
                }
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }

            // Update amount column
            const amount = receivedQty * rate;
            $row.find('.item-amount').text(amount.toFixed(2));

            // Recalculate totals if function exists
            if (typeof calculateTotals === 'function') {
                calculateTotals();
            }
        });


        $('#select_all_items').on('change', function() {
            $('.item-checkbox').prop('checked', $(this).is(':checked'));
            $('.item-checkbox').each(function() {
                toggleItemFields($(this));
            });
            calculateTotals();
        });

        function toggleItemFields($checkbox) {
            let $row = $checkbox.closest('tr');
            let $hsnInput = $row.find('.item-hsn');
            let $qtyInput = $row.find('.item-quantity');
            let isEditMode = $('#isEditMode').val() == '1';

            if ($checkbox.is(':checked')) {
                $hsnInput.prop('readonly', false);
                $qtyInput.prop('readonly', false);
            } else {
                $hsnInput.prop('readonly', true);
                $qtyInput.prop('readonly', true);
                $hsnInput.removeClass('is-invalid');
                $qtyInput.removeClass('is-invalid');
                $hsnInput.next('.invalid-feedback').remove();
                $qtyInput.next('.invalid-feedback').remove();
            }
        }


        $(document).on('input', '.item-quantity', function() {
            let $input = $(this);
            let row = $input.closest('tr');
            let qty = parseFloat($input.val()) || 0;
            let orderedQty = parseFloat(row.find('input[name*="[qty_ordered]"]').val()) || 0;
            let rate = parseFloat(row.find('.item-rate').val()) || 0;
            let checkbox = row.find('.item-checkbox');

            $input.removeClass('is-invalid');
            $input.next('.invalid-feedback').remove();

            if (!checkbox.is(':checked')) {
                row.find('.item-amount').text('0.00');
                calculateTotals();
                return;
            }

            if (qty > orderedQty) {
                $input.addClass('is-invalid');
                $input.after(`<div class="invalid-feedback d-block">Received quantity cannot exceed ordered quantity (${orderedQty})</div>`);
                row.find('.item-amount').text('0.00');
                calculateTotals();
                return;
            }

            let amount = qty * rate;
            row.find('.item-amount').text(amount.toFixed(2));

            calculateTotals();
        });


        $(document).on('change', '.item-checkbox', function() {
            toggleItemFields($(this));
            calculateTotals();
        });

        $('#select_all_charges').on('change', function() {
            $('.charge-checkbox').prop('checked', $(this).is(':checked'));
            calculateTotals();
        });

        $(document).on('input change', '.charge-checkbox, .charge-amount', function() {
            calculateTotals();
        });

        $('#discount_input').on('input', function() {
            calculateSummaryOnly();
        });

        $('input[name="other_state"]').on('change', function() {
            if ($(this).val() === 'Y') {
                $('#igst_div').show();
                $('#cgst_sgst_div').hide();
            } else {
                $('#igst_div').hide();
                $('#cgst_sgst_div').show();
            }

            calculateTaxOnly(); // ✅ IMPORTANT
        });


        $('#igst_percent, #cgst_percent, #sgst_percent').on('input', function() {
            calculateTotals();
        });

        $('#received_amount_input').on('input', function() {
            // Only recalculate due amount
            let grandTotal = parseFloat($('#grand_total_input').val()) || 0;
            let paidSoFar = parseFloat($('#paid_so_far_input').val()) || 0;
            let newPayment = parseFloat($(this).val()) || 0;
            let dueAmount = grandTotal - paidSoFar - newPayment;

            $('#due_amount').text(dueAmount.toFixed(2));
            $('#due_amount_input').val(dueAmount.toFixed(2));
        });


        function calculateTotals() {
            let subTotal = 0;
            $('.item-row').each(function() {
                let $row = $(this);
                let isChecked = $row.find('.item-checkbox').is(':checked');

                if (!isChecked) return;

                let qty = parseFloat($row.find('.item-quantity').val()) || 0;
                let rate = parseFloat($row.find('.item-rate').val()) || 0;

                let amount = qty * rate;
                $row.find('.item-amount').text(amount.toFixed(2));
                subTotal += amount;
            });

            $('#subtotal').text(subTotal.toFixed(2));
            $('#sub_total_input').val(subTotal.toFixed(2));

            let discountPercent = parseFloat($('#discount_input').val()) || 0;
            let discountAmount = (subTotal * discountPercent) / 100;

            $('#discount_value').text(discountAmount.toFixed(2));
            $('#discount_amount_input').val(discountAmount.toFixed(2));

            let taxableAmount = subTotal - discountAmount;
            $('#total').text(taxableAmount.toFixed(2));
            $('#taxable_amount_input').val(taxableAmount.toFixed(2));

            let taxAmount = 0;

            if ($('input[name="other_state"]:checked').val() === 'Y') {
                let igstPercent = parseFloat($('#igst_percent').val()) || 0;
                let igstAmount = (taxableAmount * igstPercent) / 100;

                $('#igst_amt').text(igstAmount.toFixed(2));
                $('#igst_amount_input').val(igstAmount.toFixed(2));

                $('#cgst_amt').text("0.00");
                $('#sgst_amt').text("0.00");

                taxAmount = igstAmount;

            } else {
                let cgstPercent = parseFloat($('#cgst_percent').val()) || 0;
                let sgstPercent = parseFloat($('#sgst_percent').val()) || 0;

                let cgstAmount = (taxableAmount * cgstPercent) / 100;
                let sgstAmount = (taxableAmount * sgstPercent) / 100;

                $('#cgst_amt').text(cgstAmount.toFixed(2));
                $('#sgst_amt').text(sgstAmount.toFixed(2));

                $('#cgst_amount_input').val(cgstAmount.toFixed(2));
                $('#sgst_amount_input').val(sgstAmount.toFixed(2));

                $('#igst_amt').text("0.00");

                taxAmount = cgstAmount + sgstAmount;
            }

            $('#tax_amount').text(taxAmount.toFixed(2));
            $('#tax_amount_input').val(taxAmount.toFixed(2));

            // ✅ FIX: Calculate other charges from the table
            let otherCharges = 0;
            $('#added_charges_list tr').each(function() {
                let amount = parseFloat($(this).find('input[name="charges[amount][]"]').val()) || 0;
                otherCharges += amount;
            });

            $('#other_charges').text(otherCharges.toFixed(2));
            $('#other_charges_input').val(otherCharges.toFixed(2));

            // ✅ Use the calculated otherCharges value
            let grandTotal = taxableAmount + taxAmount + otherCharges;
            $('#grand_total').text(grandTotal.toFixed(2));
            $('#grand_total_input').val(grandTotal.toFixed(2));

            let receivedAmount = parseFloat($('#received_amount_input').val()) || 0;
            let dueAmount = grandTotal - receivedAmount;

            $('#due_amount').text(dueAmount.toFixed(2));
            $('#due_amount_input').val(dueAmount.toFixed(2));
        }

        function refreshChargeDropdownState() {
            let selectedChargeIds = [];
            $('#added_charges_list tr').each(function() {
                let id = $(this).data('charge-id');
                if (id) selectedChargeIds.push(id.toString());
            });

            $('#charges_select option').each(function() {
                let optionId = $(this).val();
                if (optionId) {
                    if (selectedChargeIds.includes(optionId.toString())) {
                        $(this).prop('disabled', true);
                    } else {
                        $(this).prop('disabled', false);
                    }
                }
            });

            // Trigger change to refresh Select2 UI
            $('#charges_select').trigger('change.select2');
        }

        function loadCharges() {
            $.ajax({
                url: "{{ url('get_charges') }}",
                type: "GET",
                success: function(data) {
                    let select = $('#charges_select');
                    select.empty();
                    select.append('<option value="">Select Charge</option>');

                    data.forEach(function(charge) {
                        select.append(`<option value="${charge.id}">${charge.charge_name}</option>`);
                    });
                    refreshChargeDropdownState();
                }
            });
        }
        loadCharges();
        $('#add_charge_btn').click(function() {
            let chargeId = $('#charges_select').val();
            let chargeText = $('#charges_select option:selected').text();
            let amount = parseFloat($('#charge_amount').val());

            if (!chargeId) {
                alert("Please select a charge");
                return;
            }

            if (!amount || amount <= 0) {
                alert("Please enter a valid amount");
                return;
            }

            $('#charges_table').removeClass('d-none');

            let row = `
                <tr class="charge-row" data-charge-id="${chargeId}">
                    <td>
                        ${chargeText}
                        <input type="hidden" name="charges[charge_id][]" value="${chargeId}">
                        <input type="hidden" name="charges[name][]" value="${chargeText}">
                    </td>
                    <td>
                        ${amount.toFixed(2)}
                        <input type="hidden" name="charges[amount][]" value="${amount.toFixed(2)}">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-charge">X</button>
                    </td>
                </tr>
            `;

            $('#added_charges_list').append(row);

            // Clear inputs
            $('#charges_select').val('').trigger('change');
            $('#charge_amount').val('');

            // ✅ ONLY update other charges + grand total
            calculateChargesOnly();
            refreshChargeDropdownState();
        });


        $(document).on("click", ".remove-charge", function() {
            let $row = $(this).closest('tr');
            let chargeId = $row.data('charge-id');
            let invoiceChargeId = $row.data('invoice-charge-id');

            if (invoiceChargeId) {
                Swal.fire({
                    title: "Are you sure?",
                    text: "Do you really want to delete this charge?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#8c57ff",
                    cancelButtonColor: "#ff4c51",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Deleting...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{{ url('purchase_invoices/delete-charge') }}/" + invoiceChargeId,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: 'Charge has been deleted.',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });

                                    $row.remove();

                                    if ($('#added_charges_list tr').length === 0) {
                                        $('#charges_table').addClass('d-none');
                                    }

                                    calculateTotals();
                                    refreshChargeDropdownState();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: response.message || 'Failed to delete charge'
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Failed to delete charge: ' + (xhr.responseJSON?.message || 'Unknown error')
                                });
                            }
                        });
                    }
                });
            } else {
                $('#charges_select').find('option[value="' + chargeId + '"]').prop('disabled', false);
                $('#charges_select').select2('destroy').select2({
                    width: '100%',
                    dropdownParent: $('body')
                });

                $row.remove();

                if ($('#added_charges_list tr').length === 0) {
                    $('#charges_table').addClass('d-none');
                }

                calculateTotals();
                refreshChargeDropdownState();
            }
        });

        function updateOtherCharges() {
            let total = 0;

            $('#added_charges_list tr').each(function() {
                let amt = parseFloat($(this).find('input[name="charges[amount][]"]').val()) || 0;
                total += amt;
            });

            $('#other_charges').text(total.toFixed(2));
            $('#other_charges_input').val(total.toFixed(2));
        }

        function calculateTaxOnly() {

            let taxableAmount = parseFloat($('#taxable_amount_input').val()) || 0;
            let taxAmount = 0;

            if ($('input[name="other_state"]:checked').val() === 'Y') {

                let igstPercent = parseFloat($('#igst_percent').val()) || 0;
                let igstAmount = (taxableAmount * igstPercent) / 100;

                $('#igst_amt').text(igstAmount.toFixed(2));
                $('#igst_amount_input').val(igstAmount.toFixed(2));

                $('#cgst_amt').text('0.00');
                $('#sgst_amt').text('0.00');

                taxAmount = igstAmount;

            } else {
                let cgstPercent = parseFloat($('#cgst_percent').val()) || 0;
                let sgstPercent = parseFloat($('#sgst_percent').val()) || 0;
                let cgstAmount = (taxableAmount * cgstPercent) / 100;
                let sgstAmount = (taxableAmount * sgstPercent) / 100;
                $('#cgst_amt').text(cgstAmount.toFixed(2));
                $('#sgst_amt').text(sgstAmount.toFixed(2));
                $('#cgst_amount_input').val(cgstAmount.toFixed(2));
                $('#sgst_amount_input').val(sgstAmount.toFixed(2));
                $('#igst_amt').text('0.00');
                taxAmount = cgstAmount + sgstAmount;
            }

            $('#tax_amount').text(taxAmount.toFixed(2));
            $('#tax_amount_input').val(taxAmount.toFixed(2));

            let otherCharges = parseFloat($('#other_charges_input').val()) || 0;
            let grandTotal = taxableAmount + taxAmount + otherCharges;

            $('#grand_total').text(grandTotal.toFixed(2));
            $('#grand_total_input').val(grandTotal.toFixed(2));

            let receivedAmount = parseFloat($('#received_amount_input').val()) || 0;
            let dueAmount = grandTotal - receivedAmount;

            $('#due_amount').text(dueAmount.toFixed(2));
            $('#due_amount_input').val(dueAmount.toFixed(2));
        }

        function updateGrandTotalOnly() {
            let taxableAmount = parseFloat($('#taxable_amount_input').val()) || 0;
            let taxAmount = parseFloat($('#tax_amount_input').val()) || 0;
            let otherCharges = parseFloat($('#other_charges_input').val()) || 0;

            let grandTotal = taxableAmount + taxAmount + otherCharges;
            $('#grand_total').text(grandTotal.toFixed(2));
            $('#grand_total_input').val(grandTotal.toFixed(2));

            let receivedAmount = parseFloat($('#received_amount_input').val()) || 0;
            let dueAmount = grandTotal - receivedAmount;

            $('#due_amount').text(dueAmount.toFixed(2));
            $('#due_amount_input').val(dueAmount.toFixed(2));
        }

        function calculateChargesOnly() {
            let otherCharges = 0;

            $('#added_charges_list tr').each(function() {
                let amt = parseFloat(
                    $(this).find('input[name="charges[amount][]"]').val()
                ) || 0;

                otherCharges += amt;
            });

            // Update Other Charges
            $('#other_charges').text(otherCharges.toFixed(2));
            $('#other_charges_input').val(otherCharges.toFixed(2));

            // Get existing values (DO NOT recalc items)
            let taxableAmount = parseFloat($('#taxable_amount_input').val()) || 0;
            let taxAmount = parseFloat($('#tax_amount_input').val()) || 0;
            let receivedAmount = parseFloat($('#received_amount_input').val()) || 0;

            // Grand total
            let grandTotal = taxableAmount + taxAmount + otherCharges;

            $('#grand_total').text(grandTotal.toFixed(2));
            $('#grand_total_input').val(grandTotal.toFixed(2));

            // Due
            let dueAmount = grandTotal - receivedAmount;
            $('#due_amount').text(dueAmount.toFixed(2));
            $('#due_amount_input').val(dueAmount.toFixed(2));
        }

        function calculateItemSubtotal() {
            let subTotal = 0;

            $('.item-row').each(function() {
                let $row = $(this);
                let isChecked = $row.find('.item-checkbox').is(':checked');

                if (!isChecked) return;

                let qty = parseFloat($row.find('.item-quantity').val()) || 0;
                let rate = parseFloat($row.find('.item-rate').val()) || 0;

                let amount = qty * rate;
                $row.find('.item-amount').text(amount.toFixed(2));

                subTotal += amount;
            });

            $('#subtotal').text(subTotal.toFixed(2));
            $('#sub_total_input').val(subTotal.toFixed(2));
        }

        function calculateSummaryOnly() {
            let subTotal = parseFloat($('#sub_total_input').val()) || 0;

            let discountPercent = parseFloat($('#discount_input').val()) || 0;
            let discountAmount = (subTotal * discountPercent) / 100;

            $('#discount_value').text(discountAmount.toFixed(2));
            $('#discount_amount_input').val(discountAmount.toFixed(2));

            let taxableAmount = subTotal - discountAmount;
            $('#total').text(taxableAmount.toFixed(2));
            $('#taxable_amount_input').val(taxableAmount.toFixed(2));

            let taxAmount = 0;

            if ($('input[name="other_state"]:checked').val() === 'Y') {
                let igstPercent = parseFloat($('#igst_percent').val()) || 0;
                taxAmount = (taxableAmount * igstPercent) / 100;
                $('#igst_amt').text(taxAmount.toFixed(2));
            } else {
                let cgst = (taxableAmount * (parseFloat($('#cgst_percent').val()) || 0)) / 100;
                let sgst = (taxableAmount * (parseFloat($('#sgst_percent').val()) || 0)) / 100;
                taxAmount = cgst + sgst;
                $('#cgst_amt').text(cgst.toFixed(2));
                $('#sgst_amt').text(sgst.toFixed(2));
            }

            $('#tax_amount').text(taxAmount.toFixed(2));
            $('#tax_amount_input').val(taxAmount.toFixed(2));

            let otherCharges = parseFloat($('#other_charges_input').val()) || 0;
            let grandTotal = taxableAmount + taxAmount + otherCharges;

            $('#grand_total').text(grandTotal.toFixed(2));
            $('#grand_total_input').val(grandTotal.toFixed(2));

            let paidSoFar = parseFloat($('#paid_so_far_input').val()) || 0;
            let newPayment = parseFloat($('#received_amount_input').val()) || 0;
            let due = grandTotal - paidSoFar - newPayment;

            $('#due_amount').text(due.toFixed(2));
            $('#due_amount_input').val(due.toFixed(2));
        }

        @if(isset($invoice))
        $('#view_history_btn').click(function(e) {
            e.preventDefault();
            let invoiceId = "{{ $invoice->id }}";
            $('#payment_history_body').html('<tr><td colspan="2" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading history...</td></tr>');
            $('#paymentHistoryModal').modal('show');

            $.ajax({
                url: "{{ url('purchase_invoices/payment-history') }}/" + invoiceId,
                type: "GET",
                success: function(response) {
                    if (response.success) {
                        let html = "";
                        let total = 0;
                        response.payments.forEach(function(payment) {
                            let date = new Date(payment.payment_date);
                            let formattedDate = date.toLocaleString('en-IN', { 
                                day: '2-digit', month: '2-digit', year: 'numeric',
                                hour: '2-digit', minute: '2-digit', second: '2-digit',
                                hour12: true 
                            });
                            html += `
                                <tr>
                                    <td>${formattedDate}</td>
                                    <td class="text-end fw-bold">₹${parseFloat(payment.amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                                </tr>
                            `;
                            total += parseFloat(payment.amount);
                        });

                        if (response.payments.length === 0) {
                            html = '<tr><td colspan="2" class="text-center py-3 text-muted">No transaction logs found</td></tr>';
                        }

                        $('#payment_history_body').html(html);
                        $('#history_total_paid').text('₹' + total.toLocaleString('en-IN', {minimumFractionDigits: 2}));
                    }
                },
                error: function() {
                    $('#payment_history_body').html('<tr><td colspan="2" class="text-center text-danger py-3">Error loading history</td></tr>');
                }
            });
        });
        @endif

    });
</script>


@endsection
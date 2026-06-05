@extends('layouts.common')
@section('title', (isset($creditNote) ? 'Edit' : 'Add') . ' Credit Note - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            @include('flash_messages')
            
            @if($errors->has('error'))
                <div class="alert alert-danger">{{ $errors->first('error') }}</div>
            @endif
            
            <form action="{{ url('credit_notes/add' . (isset($creditNote) ? '/' . $creditNote->id : '')) }}" method="POST" class="common-form" enctype="multipart/form-data" id="creditNoteForm">
                @csrf
                
                <!-- Credit Note Details -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>{{ isset($creditNote) ? 'Edit' : 'Add' }} Credit Note</h4>
                        </div>
                        <div class="row g-4">
                            <!-- 1. Credit Note No (Auto Generated) -->
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="note_no" placeholder="Enter Credit Note No." name="note_no" value="{{ old('note_no', $creditNote->note_no ?? $nextNoteNo) }}" readonly>
                                    <label for="note_no">Credit Note No. *</label>
                                    @error('note_no') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            
                            <!-- 2. Date -->
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control flatpickr" id="note_date" name="note_date" placeholder="Enter Date" value="{{ old('note_date', isset($creditNote) ? $creditNote->note_date->format('Y-m-d') : date('Y-m-d')) }}">
                                    <label for="note_date">Date *</label>
                                    @error('note_date') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            
                            <!-- 3. Customer -->
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" name="customer_id" id="customer_id" data-placeholder="Select Customer/Buyer" {{ isset($creditNote) ? 'disabled' : '' }}>
                                        <option value="">Select Customer/Buyer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" data-state-id="{{ $customer->state_id }}" data-zone-id="{{ $customer->zone_id }}" {{ (old('customer_id', $creditNote->customer_id ?? '') == $customer->id) ? 'selected' : '' }}>{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                    @if(isset($creditNote))
                                        <input type="hidden" name="customer_id" value="{{ $creditNote->customer_id }}">
                                    @endif
                                    <label for="customer_id">Customer / Buyer *</label>
                                    @error('customer_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            
                            <!-- 4. Invoice No (Multi Select) -->
                            <div class="col-lg-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    @php
                                        $selectedInvoices = old('sales_invoice_ids', $creditNote->sales_invoice_ids ?? []);
                                    @endphp
                                    <select class="select2 form-select" name="sales_invoice_ids[]" id="sales_invoice_ids" 
                                            data-placeholder="Select Invoices" multiple="multiple" style="width: 100%;">
                                        @if(!empty($salesInvoices))
                                            @foreach($salesInvoices as $invoice)
                                                <option value="{{ $invoice->id }}" {{ in_array($invoice->id, $selectedInvoices) ? 'selected' : '' }}>
                                                    {{ $invoice->inv_no }} ({{ $invoice->inv_date->format('d-m-Y') }})
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <label for="sales_invoice_ids">Invoice No (Multi Select) *</label>
                                    @error('sales_invoice_ids') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            
                            <!-- 5. Reason -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" name="reason" id="reason" data-placeholder="Select Reason">
                                        <option value="">Select Reason</option>
                                        <option value="Return" {{ old('reason', $creditNote->reason ?? '') == 'Return' ? 'selected' : '' }}>Return</option>
                                        <option value="Excess Billing" {{ old('reason', $creditNote->reason ?? '') == 'Excess Billing' ? 'selected' : '' }}>Excess Billing</option>
                                        <option value="Short Supply" {{ old('reason', $creditNote->reason ?? '') == 'Short Supply' ? 'selected' : '' }}>Short Supply</option>
                                        <option value="Rate Correction" {{ old('reason', $creditNote->reason ?? '') == 'Rate Correction' ? 'selected' : '' }}>Rate Correction</option>
                                    </select>
                                    <label for="reason">Reason *</label>
                                    @error('reason') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <!-- Zone -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" name="zone_id" id="zone_id" data-placeholder="Select Zone">
                                        <option value="">Select Zone</option>
                                        @foreach($zones as $zone)
                                            <option value="{{ $zone->id }}" {{ (old('zone_id', $creditNote->zone_id ?? '') == $zone->id) ? 'selected' : '' }}>{{ $zone->zone_name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="zone_id">Zone</label>
                                    @error('zone_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            <!-- Sales Agent -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" name="agent_id" id="agent_id" data-placeholder="Select Sales Executive">
                                        <option value="">Select Sales Executive</option>
                                        @foreach($sales_agent as $agent)
                                            <option value="{{ $agent->id }}" {{ (old('agent_id', $creditNote->agent_id ?? '') == $agent->id) ? 'selected' : '' }}>{{ $agent->name }} ({{ $agent->code }})</option>
                                        @endforeach
                                    </select>
                                    <label for="agent_id">Sales Executive</label>
                                    @error('agent_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            <input type="hidden" name="status" value="{{ old('status', $creditNote->status ?? 'Draft') }}">
                            <div class="col-lg-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control" name="reason_detail" id="reason_detail" placeholder="Enter reason details" style="height: 100px;">{{ old('reason_detail', $creditNote->reason_detail ?? '') }}</textarea>
                                    <label for="reason_detail">Reason Detail</label>
                                    @error('reason_detail') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Item Details Section -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box border-bottom pb-2 mb-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <h5 class="mb-0">Item details</h5>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="barcode_scanner" class="form-control border-primary" placeholder="Scan Barcode" autocomplete="off" style="border-width: 2px;" autofocus>
                                    <label for="barcode_scanner" class="text-primary fw-bold">SCAN BARCODE</label>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                                <small class="text-muted"><i class="ri-information-line me-1"></i> Tip: Scan a barcode or type item code to quickly add it to the order.</small>
                            </div>
                        </div>
                        <div class="table-responsive p-1">
                            <table class="table table-bordered align-middle" id="itemTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">S.No</th>
                                        <th style="width: 120px;">INVOICE NO</th>
                                        <th style="width: 220px;">ITEM NAME</th>
                                        <th style="width: 100px;">COLOR</th>
                                        <th style="width: 130px;">ART NO</th>
                                        <th style="width: 70px;">UOM</th>
                                        <th style="width: 90px;" class="text-center">SIZE</th>
                                        <th style="width: 90px;" class="text-end">INV QTY</th>
                                        <th style="width: 90px;" class="text-end">RET QTY</th>
                                        <th style="width: 90px;" class="text-end">BAL QTY</th>
                                        <th style="width: 100px;" class="text-center">RETURN QTY</th>
                                        <th style="width: 100px;" class="text-end">MRP</th>
                                        <th style="width: 100px;" class="text-end">PRICE</th>
                                        <th style="width: 100px;" class="text-end">AMOUNT</th>
                                        <th style="width: 70px;" class="text-center">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody id="item-rows">
                                    <tr class="empty-row text-center">
                                        <td colspan="15">Select Customer and then Invoices to load items</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-lg-12">
                        <!-- Additional Charges Panel -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="card-header-box border-bottom pb-2 mb-3">
                                    <h5 class="mb-0">Additional Charges</h5>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6 col-xl-3">
                                        <div class="form-floating form-floating-outline">
                                            <select id="charges_select" class="select2 form-select" data-placeholder="Select Charge">
                                                <option value="">Select Charge</option>
                                                @foreach($charges as $c)
                                                    <option value="{{ $c->id }}">{{ $c->charge_name }}</option>
                                                @endforeach
                                            </select>
                                            <label for="charges_select">Select Charge</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <div class="form-floating form-floating-outline">
                                            <select id="charge_tax_type" class="form-select select2">
                                                <option value="Pre-GST">Pre-GST</option>
                                                <option value="Post-GST" selected>Post-GST</option>
                                            </select>
                                            <label for="charge_tax_type">Tax Type</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-2">
                                        <div class="form-floating form-floating-outline">
                                            <input type="number" step="0.01" class="form-control" id="charge_amount" placeholder="0.00">
                                            <label for="charge_amount">Amount</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-2 d-flex align-items-center">
                                        <button type="button" id="add_charge_btn" class="btn btn-primary w-100">Add Charge</button>
                                    </div>
                                </div>

                                <div class="table-responsive mt-3 {{ (isset($creditNoteCharges) && $creditNoteCharges->count() > 0) ? '' : 'd-none' }}" id="charges_table">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Charge Name</th>
                                                <th>Tax Type</th>
                                                <th>Amount</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="added_charges_list">
                                            @if(isset($creditNoteCharges))
                                                @foreach($creditNoteCharges as $chg)
                                                    <tr class="charge-row" data-charge-id="{{ $chg->charge_id }}" data-tax-type="{{ $chg->tax_type }}">
                                                        <td>
                                                            {{ $chg->charge_name }}
                                                            <input type="hidden" name="charges[charge_id][]" value="{{ $chg->charge_id }}">
                                                            <input type="hidden" name="charges[name][]" value="{{ $chg->charge_name }}">
                                                        </td>
                                                        <td>
                                                            {{ $chg->tax_type }}
                                                            <input type="hidden" name="charges[tax_type][]" value="{{ $chg->tax_type }}">
                                                        </td>
                                                        <td>
                                                            {{ number_format($chg->charge_amount, 2, '.', '') }}
                                                            <input type="hidden" name="charges[amount][]" value="{{ number_format($chg->charge_amount, 2, '.', '') }}">
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-sm btn-outline-danger remove-charge"><i class="ri-delete-bin-line"></i></button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-lg-7">
                        <!-- PDF Visibility Checklist -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="card-header-box mb-3 border-bottom pb-2">
                                    <h5 class="mb-0">Show in Credit Note PDF</h5>
                                </div>
                                <div class="row g-3">
                                    @php
                                        $selected_fields = old('show_fields', $creditNote->show_fields ?? ['amount', 'discount', 'tax', 'subtotal', 'grandtotal','mrp','price','sales_agent']);
                                    @endphp
                                    <div class="col-md-6 col-lg-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="show_amount" name="show_fields[]" value="amount" {{ in_array('amount', $selected_fields) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="show_amount">Show Amount</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="show_discount" name="show_fields[]" value="discount" {{ in_array('discount', $selected_fields) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="show_discount">Show Discount</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="show_tax" name="show_fields[]" value="tax" {{ in_array('tax', $selected_fields) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="show_tax">Show Tax (GST/IGST)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="show_subtotal" name="show_fields[]" value="subtotal" {{ in_array('subtotal', $selected_fields) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="show_subtotal">Show Sub Total</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="show_grandtotal" name="show_fields[]" value="grandtotal" {{ in_array('grandtotal', $selected_fields) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="show_grandtotal">Show Grand Total</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="show_mrp" name="show_fields[]" value="mrp" {{ in_array('mrp', $selected_fields) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="show_mrp">Show MRP</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="show_price" name="show_fields[]" value="price" {{ in_array('price', $selected_fields) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="show_price">Show Price</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="show_sales_agent" name="show_fields[]" value="sales_agent" {{ in_array('sales_agent', $selected_fields) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="show_sales_agent">Show Sales Executive</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Remarks Card -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="card-header-box mb-3">
                                    <h5 class="mb-0">Additional Information</h5>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-floating form-floating-outline">
                                            <textarea class="form-control" id="remarks" name="remarks" placeholder="Remarks" style="height: 100px;">{{ old('remarks', $creditNote->remarks ?? '') }}</textarea>
                                            <label for="remarks">Remarks</label>
                                            @error('remarks') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating form-floating-outline">
                                            <input class="form-control" type="file" id="reference_document" name="reference_document">
                                            <label for="reference_document">Reference Document (Attachment)</label>
                                            <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">Max file size: 2MB. Supported formats: JPG, PNG, JPEG, WEBP, PDF, DOC, DOCX</small>
                                            @error('reference_document') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                            @if(isset($creditNote) && !empty($creditNote->reference_doc))
                                            <div class="mt-2 preview-container">
                                                @php
                                                    $attachment = $creditNote->reference_doc;
                                                    $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                                    $url = url('uploads/credit_notes/' . $attachment);
                                                @endphp

                                                <div class="attachment-thumb border rounded p-1 bg-white shadow-sm position-relative" style="width: 100px; height: 100px;" title="{{ $attachment }}">
                                                    @if($isImage)
                                                        <img src="{{ $url }}" class="w-100 h-100 object-fit-cover rounded cursor-pointer view-image" data-image="{{ $url }}" alt="Reference">
                                                    @else
                                                        <a href="{{ $url }}" target="_blank" class="w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-light rounded text-decoration-none shadow-none text-primary">
                                                            <i class="ri ri-file-text-line fs-2"></i>
                                                            <span class="badge bg-primary text-white mt-1" style="font-size: 10px;">{{ strtoupper($extension) }}</span>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                            @else
                                            <div class="mt-2 preview-container"></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tax Summary Section -->
                    <div class="col-lg-5">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="card-header-box mb-3">
                                    <h5 class="mb-0">Tax & Bill Summary</h5>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="d-flex gap-3 align-items-center">
                                        <label class="fw-bold small mb-0">Other State?</label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check m-0">
                                                <input class="form-check-input" type="radio" name="is_other_state" id="state_yes" value="yes" {{ (old('is_other_state', ($creditNote->other_state ?? false) == true) ? 'checked' : '') }} onclick="return false;">
                                                <label class="form-check-label small" for="state_yes">Yes</label>
                                            </div>
                                            <div class="form-check m-0">
                                                <input class="form-check-input" type="radio" name="is_other_state" id="state_no" value="no" {{ (old('is_other_state', ($creditNote->other_state ?? false) == false) ? 'checked' : '') }} onclick="return false;">
                                                <label class="form-check-label small" for="state_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-3">
                                    <label class="fw-bold text-muted">Sub total:</label>
                                    <div class="text-end">
                                        <input type="hidden" name="sub_total" id="sub_total" value="{{ old('sub_total', $creditNote->sub_total ?? 0) }}">
                                        <span id="sub_total_text" class="fw-bold">₹{{ number_format(old('sub_total', $creditNote->sub_total ?? 0), 2) }}</span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-3 mt-3">
                                    <label class="fw-bold text-muted">Discount:</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="number" name="discount_percent" id="discount_percent" class="form-control form-control-sm text-end" style="width: 80px;" value="{{ old('discount_percent', $creditNote->discount_percent ?? 0) }}" step="0.01" min="0" max="100">
                                        <span class="small">%</span>
                                        <span class="ms-2">₹<span id="discount_text">{{ number_format(old('discount', $creditNote->discount ?? 0), 2) }}</span></span>
                                        <input type="hidden" name="discount" id="discount" value="{{ old('discount', $creditNote->discount ?? 0) }}">
                                    </div>
                                </div>

                                <div id="cgst_row" class="{{ (old('is_other_state', ($creditNote->other_state ?? false) == true) ? 'd-none' : '') }}">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="fw-bold text-muted">CGST:</label>
                                        <div class="d-flex gap-2 align-items-center">
                                            <input type="number" name="cgst_percent" id="cgst_percent" class="form-control form-control-sm text-end" style="width: 85px; border: 1px solid #e5e6e8;" value="{{ old('cgst_percent', $creditNote->cgst_percent ?? 9) }}" step="0.01">
                                            <span class="small">%</span>
                                            <span class="ms-2">₹<span id="cgst_amt_text">{{ number_format(old('cgst', $creditNote->cgst ?? 0), 2) }}</span></span>
                                            <input type="hidden" name="cgst_amt" id="cgst_amt" value="{{ old('cgst', $creditNote->cgst ?? 0) }}">
                                        </div>
                                    </div>
                                </div>

                                <div id="sgst_row" class="{{ (old('is_other_state', ($creditNote->other_state ?? false) == true) ? 'd-none' : '') }}">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="fw-bold text-muted">SGST:</label>
                                        <div class="d-flex gap-2 align-items-center">
                                            <input type="number" name="sgst_percent" id="sgst_percent" class="form-control form-control-sm text-end" style="width: 85px; border: 1px solid #e5e6e8;" value="{{ old('sgst_percent', $creditNote->sgst_percent ?? 9) }}" step="0.01">
                                            <span class="small">%</span>
                                            <span class="ms-2">₹<span id="sgst_amt_text">{{ number_format(old('sgst', $creditNote->sgst ?? 0), 2) }}</span></span>
                                            <input type="hidden" name="sgst_amt" id="sgst_amt" value="{{ old('sgst', $creditNote->sgst ?? 0) }}">
                                        </div>
                                    </div>
                                </div>

                                <div id="igst_row" class="{{ (old('is_other_state', ($creditNote->other_state ?? false) == false) ? 'd-none' : '') }}">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="fw-bold text-muted">IGST:</label>
                                        <div class="d-flex gap-2 align-items-center">
                                            <input type="number" name="igst_percent" id="igst_percent" class="form-control form-control-sm text-end" style="width: 85px; border: 1px solid #e5e6e8;" value="{{ old('igst_percent', $creditNote->igst_percent ?? 18) }}" step="0.01">
                                            <span class="small">%</span>
                                            <span class="ms-2">₹<span id="igst_amt_text">{{ number_format(old('igst', $creditNote->igst ?? 0), 2) }}</span></span>
                                            <input type="hidden" name="igst_amt" id="igst_amt" value="{{ old('igst', $creditNote->igst ?? 0) }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-3 mt-4">
                                    <label class="fw-bold text-muted">Tax Amount:</label>
                                    <div class="text-end">
                                        <input type="hidden" name="tax_amount" id="tax_amount" value="{{ old('tax_amount', $creditNote->tax_amount ?? 0) }}">
                                        <span id="tax_amount_text" class="fw-bold">₹{{ number_format(old('tax_amount', $creditNote->tax_amount ?? 0), 2) }}</span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-3">
                                    <label class="fw-bold text-muted">Other Charges:</label>
                                    <div class="text-end">
                                        <input type="hidden" name="other_charges" id="other_charges" value="{{ old('other_charges', $creditNote->other_charges ?? 0) }}">
                                        <span id="other_charges_text" class="fw-bold">₹{{ number_format(old('other_charges', $creditNote->other_charges ?? 0), 2) }}</span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between py-2">
                                    <label class="fw-bold text-muted">Round Off:</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="form-check form-check-inline m-0">
                                            <input class="form-check-input round-off-type-radio" type="radio" name="round_off_type" id="round_off_add" value="Add" {{ old('round_off_type', $creditNote->round_off_type ?? 'Add') == 'Add' ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="round_off_add">Add</label>
                                        </div>
                                        <div class="form-check form-check-inline m-0">
                                            <input class="form-check-input round-off-type-radio" type="radio" name="round_off_type" id="round_off_less" value="Less" {{ old('round_off_type', $creditNote->round_off_type ?? 'Add') == 'Less' ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="round_off_less">Less</label>
                                        </div>
                                        <input type="number" class="form-control form-control-sm text-end" style="width: 80px;" id="round_off" name="round_off" value="{{ old('round_off', $creditNote->round_off ?? 0) }}" step="0.01" min="0">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="fw-bold" style="font-size: 1.1rem;">Grand Total:</label>
                                    <div class="text-end">
                                        <input type="hidden" name="grand_total" id="grand_total" value="{{ old('grand_total', $creditNote->grand_total ?? 0) }}">
                                        <span id="grand_total_text" class="fw-bold" style="font-size: 1.5rem; color: #28a745;">₹{{ number_format(old('grand_total', $creditNote->grand_total ?? 0), 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 text-end mt-3 mb-4">
                        <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
                        <a href="{{ url('credit_notes') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize components
    $(".select2").select2();
    $('.flatpickr').flatpickr({
        dateFormat: "Y-m-d",
        allowInput: true
    });

    let creditNoteId = "{{ $creditNote->id ?? '' }}";
    let isEditing = creditNoteId !== "";

    // 1. Customer Selection Mandatory check before Invoice
    $('#sales_invoice_ids').on('select2:opening', function(e) {
        let customerId = $('#customer_id').val();
        if (!customerId) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Customer Mandatory',
                text: 'Please select a Customer first before selecting Sales Invoices!'
            });
        }
    });

    // 2. Load Customer's Invoices dynamically via AJAX
    $('#customer_id').on('change', function() {
        let customerId = $(this).val();
        if (!customerId) {
            $('#sales_invoice_ids').val([]).trigger('change').html('');
            $('#item-rows').html('<tr class="empty-row text-center"><td colspan="15">Select Customer and then Invoices to load items</td></tr>');
            $('#zone_id').val('').trigger('change');
            return;
        }

        // Fetch customer specific details
        let customerStateId = $(this).find(':selected').data('state-id');
        let companyStateId = "{{ $web_settings->state_id ?? '' }}";

        if (customerStateId && companyStateId) {
            if (customerStateId == companyStateId) {
                $('#state_no').prop('checked', true).trigger('change');
                $('#cgst_percent').val("{{ $web_settings->cgst ?? 9 }}");
                $('#sgst_percent').val("{{ $web_settings->sgst ?? 9 }}");
                $('#igst_percent').val(0);
            } else {
                $('#state_yes').prop('checked', true).trigger('change');
                $('#igst_percent').val("{{ $web_settings->igst ?? 18 }}");
                $('#cgst_percent').val(0);
                $('#sgst_percent').val(0);
            }
        }

        // Autofill Zone
        let zoneId = $(this).find(':selected').data('zone-id');
        if (zoneId) {
            $('#zone_id').val(zoneId).trigger('change');
        } else {
            // Fallback AJAX if data-zone-id is missing
            $.ajax({
                url: "{{ url('get-customer-details') }}/" + customerId,
                type: 'GET',
                success: function(res) {
                    if (res.success && res.customer && res.customer.zone_id) {
                        $('#zone_id').val(res.customer.zone_id).trigger('change');
                    }
                }
            });
        }

        if (!isEditing) {
            $.ajax({
                url: "{{ url('credit_notes/get-customer-invoices') }}/" + customerId,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        let options = '';
                        response.invoices.forEach(inv => {
                            options += `<option value="${inv.id}">${inv.inv_no} (${moment(inv.inv_date).format('d-m-Y')})</option>`;
                        });
                        $('#sales_invoice_ids').html(options).val([]).trigger('change');
                        $('#item-rows').html('<tr class="empty-row text-center"><td colspan="15">Invoices loaded. Select invoice(s) to fetch items.</td></tr>');
                    }
                }
            });
        }
    });

    // 3. Fetch Items from Selected Multi-Select Invoices
    $('#sales_invoice_ids').on('change', function() {
        let selectedInvoiceIds = $(this).val();
        if (!selectedInvoiceIds || selectedInvoiceIds.length === 0) {
            $('#item-rows').html('<tr class="empty-row text-center"><td colspan="15">Select Invoice(s) to load items</td></tr>');
            calculateTotal();
            return;
        }

        let url = "{{ url('credit_notes/get-invoice-details') }}/" + selectedInvoiceIds.join(',');
        if (isEditing) {
            url += "?credit_note_id=" + creditNoteId;
        }

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    // Store available items in global variable
                    window.availableInvoiceItems = response.items || [];
                    
                    // Clear the rows first
                    $('#item-rows').html('');
                    
                    // Keep old values if old items are stored
                    let oldItemsMap = {};
                    @if(old('items'))
                        @foreach(old('items') as $idx => $oldItm)
                            oldItemsMap["{{ $oldItm['sales_invoice_item_id'] }}"] = {
                                qty: "{{ $oldItm['quantity'] }}"
                            };
                        @endforeach
                    @elseif(isset($creditNote))
                        @foreach($creditNote->items as $noteItem)
                            oldItemsMap["{{ $noteItem->sales_invoice_item_id }}"] = {
                                qty: "{{ $noteItem->quantity }}"
                            };
                        @endforeach
                    @endif

                    let hasPrepopulated = false;
                    response.items.forEach((item) => {
                        if (oldItemsMap[item.id]) {
                            let returnQty = oldItemsMap[item.id].qty;
                            addInvoiceItem(item, returnQty);
                            hasPrepopulated = true;
                        }
                    });

                    if (!hasPrepopulated) {
                        $('#item-rows').html('<tr class="empty-row text-center"><td colspan="15">No items added. Use the search box above to scan or search items from selected invoices.</td></tr>');
                    }
                    
                    // Populate tax rates
                    $('#igst_percent').val(response.igst_percent);
                    $('#cgst_percent').val(response.cgst_percent);
                    $('#sgst_percent').val(response.sgst_percent);
                    
                    calculateTotal();
                }
            }
        });
    });

    // Helper to add item to table
    window.addInvoiceItem = function(item, initialQty = 1) {
        // Check if item is already in the table
        let existingRow = $(`.item-row[data-item-id="${item.id}"]`);
        if (existingRow.length > 0) {
            let qtyInput = existingRow.find('.qty');
            let currentVal = parseFloat(qtyInput.val()) || 0;
            let balanceVal = parseFloat(existingRow.find('.balance-qty-val').text()) || 0;
            if (currentVal + 1 <= balanceVal) {
                qtyInput.val((currentVal + 1).toFixed(2)).trigger('input');
            } else {
                qtyInput.val(balanceVal.toFixed(2)).trigger('input');
            }
            qtyInput.focus().select();
            return;
        }

        // Remove empty row message if it exists
        $('#item-rows .empty-row').remove();

        let index = $('#item-rows .item-row').length;
        let disabledStr = item.balance_qty <= 0 ? 'disabled' : '';
        let returnQty = initialQty;

        let rowHtml = `
            <tr class="item-row" data-item-id="${item.id}" data-barcode="${item.product_barcode || ''}" data-name="${item.item_name.toLowerCase()}" data-code="${item.item_code.toLowerCase()}">
                <td class="text-center s-no">${index + 1}</td>
                <td>
                    <span class="fw-semibold text-primary">${item.invoice_no}</span>
                </td>
                <td>
                    <input type="hidden" name="items[${index}][brand_category_id]" value="${item.brand_category_id || ''}">
                    <input type="hidden" name="items[${index}][sales_invoice_item_id]" value="${item.id}">
                    <input type="hidden" name="items[${index}][item_id]" value="${item.item_id}">
                    <input type="hidden" name="items[${index}][sleeve_type]" value="${item.sleeve_type || ''}">
                    <span class="d-block fw-bold" style="font-size: 13px;">${item.item_name}</span>
                    <small class="text-muted">
                        Code: 
                        ${item.product_barcode ? ' | <i class="ri-barcode-line"></i> ' + item.product_barcode : ''}
                    </small>
                </td>
                <td>
                    <input type="hidden" name="items[${index}][color_id]" value="${item.color_id || ''}">
                    <span>${item.color_name || '-'}</span>
                </td>
                <td>
                    <input type="hidden" name="items[${index}][art_no]" value="${item.art_no || ''}">
                    <span>${item.art_no || '-'}</span>
                </td>
                <td>
                    <input type="hidden" name="items[${index}][uom_id]" value="${item.uom_id || ''}">
                    <span>${item.uom_code || '-'}</span>
                </td>
                <td class="text-center">
                    <input type="text" class="form-control form-control-sm text-center" name="items[${index}][size]" value="${item.size_name || item.size || ''}" readonly style="background-color: #f8f9fa;">
                </td>
                <td class="text-end fw-semibold">${item.invoice_qty}</td>
                <td class="text-end text-warning fw-semibold">${item.returned_qty}</td>
                <td class="text-end text-success fw-bold balance-qty-val">${item.balance_qty}</td>
                <td>
                    <input type="number" class="form-control form-control-sm text-center qty" 
                        name="items[${index}][quantity]" 
                        value="${returnQty}" 
                        step="0.01" min="0" 
                        max="${item.balance_qty}" 
                        ${item.balance_qty <= 0 ? 'disabled' : ''}>
                </td>
                <td class="text-end">
                    <input type="number" class="form-control form-control-sm text-end mrp" 
                        name="items[${index}][mrp]" 
                        value="${item.mrp || 0}" 
                        step="0.01" readonly 
                        style="background-color: #f8f9fa; min-width: 110px;">
                </td>
                <td class="text-end">
                    <input type="number" class="form-control form-control-sm text-end rate" 
                        name="items[${index}][rate]" 
                        value="${item.rate || 0}" 
                        step="0.01" readonly 
                        style="background-color: #f8f9fa; min-width: 110px;">
                </td>
                <td>
                    <div class="input-group input-group-merge input-group-sm" style="min-width: 130px;">
                        <span class="input-group-text">₹</span>
                        <input type="text" class="form-control form-control-sm text-end line_total" 
                            value="${(returnQty * (item.rate || 0)).toFixed(2)}" 
                            readonly 
                            name="items[${index}][amount]" 
                            style="background-color: #f8f9fa;">
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger remove-item">
                        <i class="ri ri-delete-bin-line"></i>
                    </button>
                </td>
            </tr>`;

        $('#item-rows').append(rowHtml);
        updateSerialNumbers();
        calculateTotal();
    };

    window.updateSerialNumbers = function() {
        $('#item-rows .item-row').each(function(index) {
            $(this).find('.s-no').text(index + 1);
            
            // Re-index all inputs
            $(this).find('input').each(function() {
                let name = $(this).attr('name');
                if (name) {
                    let updatedName = name.replace(/items\[\d+\]/, `items[${index}]`);
                    $(this).attr('name', updatedName);
                }
            });
        });
    };

    // Remove item handler
    $(document).on('click', '.remove-item', function() {
        $(this).closest('tr').remove();
        updateSerialNumbers();
        if ($('#item-rows .item-row').length === 0) {
            $('#item-rows').html('<tr class="empty-row text-center"><td colspan="15">No items added. Use the search box above to scan or search items from selected invoices.</td></tr>');
        }
        calculateTotal();
    });

    // Run trigger on page load if editing or has old values
    if (isEditing || $('#sales_invoice_ids').val()) {
        $('#sales_invoice_ids').trigger('change');
    }

    // 4. Autocomplete and Barcode scanner for adding items
    $('#barcode_scanner').autocomplete({
        source: function (request, response) {
            if (!window.availableInvoiceItems || window.availableInvoiceItems.length === 0) {
                response([]);
                return;
            }
            var term = request.term.toLowerCase();
            var matches = window.availableInvoiceItems.filter(function (item) {
                return (item.product_barcode && String(item.product_barcode).toLowerCase().includes(term)) ||
                    (item.item_code && String(item.item_code).toLowerCase().includes(term)) ||
                    (item.item_name && String(item.item_name).toLowerCase().includes(term));
            });

            var formatted = matches.map(function (item) {
                var label = (item.item_name || '');
                if (item.invoice_no) label += ' [Inv: ' + item.invoice_no + ']';
                if (item.product_barcode) label += ' | Barcode: ' + item.product_barcode;
                if (item.size) label += ' | Size: ' + item.size;

                return {
                    label: label,
                    value: item.product_barcode || item.item_code || '',
                    itemData: item
                };
            });
            formatted = formatted.slice(0, 20);

            if (request.term && formatted.length === 0) {
                response([{
                    label: 'Item not found in selected Invoices',
                    value: '',
                    noResult: true
                }]);
                return;
            }

            response(formatted);
        },
        minLength: 1,
        select: function (event, ui) {
            if (ui.item && ui.item.noResult) {
                event.preventDefault();
                return false;
            }

            addInvoiceItem(ui.item.itemData, 1);
            setTimeout(() => { $(this).val(''); }, 10);
            return false;
        }
    }).autocomplete("instance")._renderItem = function (ul, item) {
        if (item.noResult) {
            return $("<li>")
                .append(`<div class="ui-menu-item-wrapper text-danger fw-bold">Item not found in selected Invoices</div>`)
                .appendTo(ul);
        }

        var it = item.itemData;
        var barcodeInfo = it.product_barcode ? ` | Barcode: ${it.product_barcode}` : '';
        var sizeInfo = it.size ? ` | Size: ${it.size}` : '';
        return $("<li>")
            .append(`<div class="ui-menu-item-wrapper">
                <span class="search-item-title">${item.label}</span>
                <span class="search-item-balance">Bal Qty: ${parseFloat(it.balance_qty).toFixed(2)}</span>
                <div class="search-item-info">
                    Code: ${it.item_code || '-'} ${barcodeInfo} ${sizeInfo} | Rate: ₹${parseFloat(it.rate || it.mrp || 0).toFixed(2)}
                </div>
            </div>`)
            .appendTo(ul);
    };

    // Support barcode scan with Enter key
    $('#barcode_scanner').on('keypress', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            var barcode = $(this).val().trim();
            if (!barcode) return;

            if (!window.availableInvoiceItems || window.availableInvoiceItems.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Items Available',
                    text: 'Please select Sales Invoices first, or no items exist in selected invoices.',
                });
                $(this).val('');
                return;
            }

            var matchedItem = window.availableInvoiceItems.find(function(item) {
                return (item.product_barcode && String(item.product_barcode).toLowerCase() === barcode.toLowerCase()) || 
                        (item.item_code && String(item.item_code).toLowerCase() === barcode.toLowerCase());
            });

            if (matchedItem) {
                addInvoiceItem(matchedItem, 1);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Item added via Scan',
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Item Not Found',
                    text: 'Item not found in selected Sales Invoices',
                    timer: 2000,
                    showConfirmButton: false
                });
            }

            $(this).val('');
            $(this).focus();
            if ($(this).data('ui-autocomplete')) {
                $(this).autocomplete('close');
            }
        }
    });

    // Clear search
    $('#clear_search_btn').on('click', function() {
        $('#barcode_scanner').val('');
    });

    // 5. Quantity validations & totals auto calculation
    $(document).on('input', '.qty', function() {
        let row = $(this).closest('.item-row');
        let qty = parseFloat($(this).val()) || 0;
        let balanceQty = parseFloat(row.find('.balance-qty-val').text()) || 0;

        if (qty < 0) {
            $(this).val(0);
            qty = 0;
        }

        if (qty > balanceQty) {
            Swal.fire({
                icon: 'error',
                title: 'Quantity Exceeded',
                text: `Return quantity cannot exceed remaining balance quantity of ${balanceQty}!`
            });
            $(this).val(balanceQty);
            qty = balanceQty;
        }

        let rate = parseFloat(row.find('.rate').val()) || 0;
        let amount = qty * rate;
        row.find('.line_total').val(amount.toFixed(2));
        
        calculateTotal();
    });

    $(document).on('input', '#cgst_percent, #sgst_percent, #igst_percent, #round_off', calculateTotal);
    $(document).on('change', '.round-off-type-radio', calculateTotal);
    
    $('input[name="is_other_state"]').on('change', function() {
        if ($('#state_yes').is(':checked')) {
            $('#igst_row').removeClass('d-none');
            $('#cgst_row, #sgst_row').addClass('d-none');
        } else {
            $('#igst_row').addClass('d-none');
            $('#cgst_row, #sgst_row').removeClass('d-none');
        }
        calculateTotal();
    });

    function calculateTotal() {
        let subTotal = 0;
        $('.item-row').each(function() {
            let qty = parseFloat($(this).find('.qty').val()) || 0;
            if (qty > 0) {
                subTotal += parseFloat($(this).find('.line_total').val()) || 0;
            }
        });

        // Calculate Discount
        let discountPercent = parseFloat($('#discount_percent').val()) || 0;
        let discountAmount = (subTotal * discountPercent) / 100;
        $('#discount').val(discountAmount.toFixed(2));
        $('#discount_text').text(discountAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));

        // Calculate Pre-GST and Post-GST charges
        let preGstCharges = 0;
        let postGstCharges = 0;
        $('.charge-row').each(function () {
            let amount = parseFloat($(this).find('input[name="charges[amount][]"]').val()) || 0;
            let taxType = $(this).attr('data-tax-type') || $(this).data('tax-type') || 'Post-GST';
            if (taxType === 'Pre-GST') {
                preGstCharges += amount;
            } else {
                postGstCharges += amount;
            }
        });

        let totalCharges = preGstCharges + postGstCharges;
        $('#other_charges').val(totalCharges.toFixed(2));
        $('#other_charges_text').text('₹' + totalCharges.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));

        // Taxable Amount = Subtotal - Discount + Pre-GST charges
        let taxableAmount = subTotal - discountAmount + preGstCharges;

        let cgst = 0, sgst = 0, igst = 0;
        let taxAmt = 0;

        if ($('#state_yes').is(':checked')) {
            let igstPercent = parseFloat($('#igst_percent').val()) || 0;
            igst = taxableAmount * (igstPercent / 100);
            taxAmt = igst;
            $('#igst_amt_text').text(igst.toFixed(2));
            $('#igst_amt').val(igst.toFixed(2));
        } else {
            let cgstPercent = parseFloat($('#cgst_percent').val()) || 0;
            let sgstPercent = parseFloat($('#sgst_percent').val()) || 0;
            cgst = taxableAmount * (cgstPercent / 100);
            sgst = taxableAmount * (sgstPercent / 100);
            taxAmt = cgst + sgst;
            $('#cgst_amt_text').text(cgst.toFixed(2));
            $('#cgst_amt').val(cgst.toFixed(2));
            $('#sgst_amt_text').text(sgst.toFixed(2));
            $('#sgst_amt').val(sgst.toFixed(2));
        }

        let roundOff = parseFloat($('#round_off').val()) || 0;
        let roundOffType = $('input[name="round_off_type"]:checked').val();

        let grandTotal = taxableAmount + taxAmt + postGstCharges;
        if (roundOffType === 'Less') {
            grandTotal -= roundOff;
        } else {
            grandTotal += roundOff;
        }

        $('#sub_total_text').text(subTotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#sub_total').val(subTotal.toFixed(2));
        $('#tax_amount_text').text(taxAmt.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#tax_amount').val(taxAmt.toFixed(2));
        $('#grand_total_text').text(grandTotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#grand_total').val(grandTotal.toFixed(2));
    }

    // Add event listener to discount_percent to recalculate total
    $('#discount_percent').on('input', calculateTotal);

    // --- Additional Charges Logic ---
    function refreshChargeDropdownState() {
        let selectedChargeIds = [];
        $('#added_charges_list tr').each(function () {
            let id = $(this).data('charge-id');
            if (id) selectedChargeIds.push(id.toString());
        });

        $('#charges_select option').each(function () {
            let optionId = $(this).val();
            if (optionId) {
                if (selectedChargeIds.includes(optionId.toString())) {
                    $(this).prop('disabled', true);
                } else {
                    $(this).prop('disabled', false);
                }
            }
        });

        $('#charges_select').select2('destroy').select2({
            dropdownParent: $('#charges_select').closest('.card-body')
        });
    }

    $('#add_charge_btn').click(function () {
        let chargeId = $('#charges_select').val();
        let chargeText = $('#charges_select option:selected').text();
        let amount = parseFloat($('#charge_amount').val());
        let taxType = $('#charge_tax_type').val();

        if (!chargeId) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: 'Please select a charge'
            });
            return;
        }

        if (!amount || amount <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: 'Please enter a valid amount'
            });
            return;
        }

        $('#charges_table').removeClass('d-none');

        let row = `
            <tr class="charge-row" data-charge-id="${chargeId}" data-tax-type="${taxType}">
                <td>
                    ${chargeText}
                    <input type="hidden" name="charges[charge_id][]" value="${chargeId}">
                    <input type="hidden" name="charges[name][]" value="${chargeText}">
                </td>
                <td>
                    ${taxType}
                    <input type="hidden" name="charges[tax_type][]" value="${taxType}">
                </td>
                <td>
                    ${amount.toFixed(2)}
                    <input type="hidden" name="charges[amount][]" value="${amount.toFixed(2)}">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-charge" title="Delete Charge">
                        <i class="ri ri-delete-bin-line"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#added_charges_list').append(row);

        $('#charges_select').val('').trigger('change');
        $('#charge_amount').val('');

        calculateTotal();
        refreshChargeDropdownState();
    });

    $(document).on("click", ".remove-charge", function () {
        let $row = $(this).closest('tr');
        $row.remove();

        if ($('#added_charges_list tr').length === 0) {
            $('#charges_table').addClass('d-none');
        }
        calculateTotal();
        refreshChargeDropdownState();
    });

    // Run on initial load to set select state for editing
    refreshChargeDropdownState();

    // --- Zone and Sales Executive (Agent) Logic ---
    $('#zone_id').on('change', function() {
        const zoneId = $(this).val();
        const agentSelect = $('#agent_id');
        
        let selectedAgentId = agentSelect.val();
        
        agentSelect.html('<option value="">Loading...</option>').trigger('change');
        
        if (zoneId) {
            $.ajax({
                url: `{{ url('get-agents-by-zone') }}/${zoneId}`,
                type: 'GET',
                success: function(data) {
                    let opts = '<option value="">Select Sales Executive</option>';
                    data.forEach(agent => {
                        let selectedAttr = (agent.id == selectedAgentId) ? 'selected' : '';
                        opts += `<option value="${agent.id}" ${selectedAttr}>${agent.name} (${agent.code})</option>`;
                    });
                    agentSelect.html(opts).trigger('change');
                }
            });
        } else {
             $.ajax({
                url: `{{ url('get-agents-by-zone') }}/0`, 
                type: 'GET',
                success: function(data) {
                    let opts = '<option value="">Select Sales Executive</option>';
                    data.forEach(agent => {
                        let selectedAttr = (agent.id == selectedAgentId) ? 'selected' : '';
                        opts += `<option value="${agent.id}" ${selectedAttr}>${agent.name}</option>`;
                    });
                    agentSelect.html(opts).trigger('change');
                }
            });
        }
    });

    // Form submit validation
    $('#creditNoteForm').on('submit', function(e) {
        let hasSelected = false;
        $('.qty').each(function() {
            let qty = parseFloat($(this).val()) || 0;
            if (qty > 0) {
                hasSelected = true;
            }
        });

        if (!hasSelected) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'At least one item must have a quantity greater than zero!'
            });
        }
    });
});
</script>
@endsection

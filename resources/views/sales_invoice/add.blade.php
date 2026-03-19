@extends('layouts.common')
@section('title', ($invoice ? 'Edit' : 'Add') . ' Sales Invoice - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="{{ isset($invoice) ? url('sales_invoices/add/'.$invoice->id) : url('sales_invoices/add') }}" method="POST" class="common-form" enctype="multipart/form-data">
                @csrf
                {{-- @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif --}}

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>{{ $invoice ? 'Edit' : 'Add' }} Sales Invoice</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('inv_no') is-invalid @enderror" id="inv_no" placeholder="Enter Invoice No" name="inv_no" value="{{ old('inv_no', isset($invoice) ? $invoice->inv_no : $nextInvNumber) }}">
                                    <label for="inv_no">Invoice No. * </label>
                                    @error('inv_no')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control form-control inv_date @error('inv_date') is-invalid @enderror" name="inv_date" placeholder="Enter Invoice Date" value="{{ old('inv_date', isset($invoice) ? $invoice->inv_date->format('d-m-Y') : date('d-m-Y')) }}" />
                                    <label for="inv_date">Invoice Date * </label>
                                    @error('inv_date')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="so_id" name="so_id" class="select2 form-select @error('so_id') is-invalid @enderror" data-placeholder="Select Sales Order">
                                        <option value="">Select Sales Order</option>
                                        @foreach($saleOrders as $so)
                                            <option value="{{ $so->id }}" {{ (old('so_id', isset($invoice) ? $invoice->so_id : '') == $so->id) ? 'selected' : '' }}>{{ $so->so_no }}</option>
                                        @endforeach
                                    </select>
                                    <label for="so_id">Sales Order *</label>
                                    @error('so_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="customer_id" name="customer_id" class="select2 form-select @error('customer_id') is-invalid @enderror" data-placeholder="Select Customer/Buyer">
                                        <option value="">Select Customer/Buyer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" data-state-id="{{ $customer->state_id }}" {{ (old('customer_id', isset($invoice) ? $invoice->customer_id : '') == $customer->id) ? 'selected' : '' }}>{{ $customer->name }} ({{ $customer->code }})</option>
                                        @endforeach
                                    </select>
                                    <label for="customer_id">Customer / Buyer *</label>
                                    @error('customer_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control @error('delivery_address') is-invalid @enderror" id="address" name="delivery_address" placeholder="Enter Delivery Address">{{ old('delivery_address', isset($invoice) ? $invoice->delivery_address : '') }}</textarea>
                                    <label for="address">Delivery Address *</label>
                                    @error('delivery_address')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control" id="remarks" name="remarks" placeholder="Enter Remarks">{{ old('remarks', isset($invoice) ? $invoice->remarks : '') }}</textarea>
                                    <label for="remarks">Remarks</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h5>Item Details *</h5>
                            @error('items')
                                <div class="text-danger small mb-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 12%;">Brand Category</th>
                                        <th style="width: 18%;">Item with Sleeve Type</th>
                                        <th style="width: 8%;">Size</th>
                                        <th style="width: 10%;">Art No</th>

                                        <th style="width: 8%;">UOM</th>
                                        <th style="width: 8%;">Qty *</th>
                                        <th style="width: 10%;">Rate *</th>
                                        <th style="width: 10%;">MRP</th>
                                        <th style="width: 12%;">Amount *</th>
                                    </tr>
                                </thead>
                                <tbody id="item-rows">
                                    @php
                                        $items = old('items');
                                        if (!$items && isset($invoice)) {
                                            $items = $invoice->items->map(function($item) {
                                                return [
                                                    'brand_id' => $item->brand_id,
                                                    'brand_name' => $item->brandCategory ? $item->brandCategory->name : '',
                                                    'item_id' => $item->item_id,
                                                    'item_name' => $item->item ? $item->item->name : '',
                                                    'sleeve_type' => $item->sleeve_type,
                                                    'size' => $item->size,
                                                    'size_name' => $item->sizeRatio ? $item->sizeRatio->size : $item->size,
                                                    'art_no' => $item->art_no,
                                                    'hsn_sac' => $item->hsn_sac,
                                                    'uom_id' => $item->uom_id,
                                                    'uom_code' => $item->uom ? $item->uom->uom_code : '',
                                                    'quantity' => $item->quantity,
                                                    'rate' => $item->rate,
                                                    'mrp' => $item->mrp,
                                                    'amount' => $item->amount,
                                                    'stock_entry_item_id' => $item->stock_entry_item_id,
                                                ];
                                            })->toArray();
                                        }
                                    @endphp
                                    @if($items)
                                        @foreach($items as $index => $row)
                                        @php $row = (object) $row; @endphp
                                        <tr class="item-row">
                                            <td>
                                                <span class="brand-text">{{ $row->brand_name ?? '' }}</span>
                                                <input type="hidden" name="items[{{ $index }}][brand_id]" class="brand-id" value="{{ $row->brand_id }}">
                                                <input type="hidden" name="items[{{ $index }}][brand_name]" class="brand-name" value="{{ $row->brand_name ?? '' }}">
                                                <input type="hidden" name="items[{{ $index }}][stock_entry_item_id]" class="stock-entry-item-id" value="{{ $row->stock_entry_item_id ?? '' }}">
                                            </td>
                                            <td>
                                                <span class="item-text">{{ $row->item_name ?? '' }} ({{ $row->sleeve_type ?? '' }})</span>
                                                <input type="hidden" name="items[{{ $index }}][item_id]" class="item-id" value="{{ $row->item_id }}">
                                                <input type="hidden" name="items[{{ $index }}][item_name]" class="item-name" value="{{ $row->item_name ?? '' }}">
                                                <input type="hidden" name="items[{{ $index }}][sleeve_type]" class="sleeve-type" value="{{ $row->sleeve_type ?? '' }}">
                                            </td>
                                            <td>
                                                <span class="size-text">{{ $row->size_name ?? '' }}</span>
                                                <input type="hidden" name="items[{{ $index }}][size]" class="size-id" value="{{ $row->size }}">
                                                <input type="hidden" name="items[{{ $index }}][size_name]" class="size-name" value="{{ $row->size_name ?? '' }}">
                                            </td>
                                            <td>
                                                <span class="art-no-text">{{ $row->art_no ?? '' }}</span>
                                                <input type="hidden" name="items[{{ $index }}][art_no]" class="art-no" value="{{ $row->art_no ?? '' }}">
                                            </td>

                                            <td>
                                                <span class="uom-text">{{ $row->uom_code ?? '' }}</span>
                                                <input type="hidden" name="items[{{ $index }}][uom_id]" class="uom-id" value="{{ $row->uom_id }}">
                                                <input type="hidden" name="items[{{ $index }}][uom_code]" class="uom-code" value="{{ $row->uom_code ?? '' }}">
                                            </td>
                                            <td>
                                                <div class="form-floating form-floating-outline">
                                                    <input type="number" step="any" class="form-control qty" name="items[{{ $index }}][quantity]" value="{{ $row->quantity ?? '' }}" placeholder="Qty">
                                                    <label>Qty *</label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-floating form-floating-outline">
                                                    <input type="number" step="any" class="form-control rate" name="items[{{ $index }}][rate]" value="{{ $row->rate ?? '' }}" placeholder="Rate">
                                                    <label>Rate *</label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-floating form-floating-outline">
                                                    <input type="number" step="any" class="form-control mrp" name="items[{{ $index }}][mrp]" value="{{ $row->mrp ?? '' }}" placeholder="MRP">
                                                    <label>MRP</label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" class="form-control amount" name="items[{{ $index }}][amount]" value="{{ $row->amount ?? '' }}" placeholder="Amount" readonly>
                                                    <label>Amount *</label>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr class="item-row">
                                            <td colspan="8" class="text-center">No items found</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row g-4">
                    <!-- Invoice Details Card -->
                    <div class="col-md-12 col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-header-box">
                                    <h5>Invoice Details</h5>
                                </div>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <select name="invoice_status" id="invoice_status" class="form-select select2 @error('invoice_status') is-invalid @enderror" data-placeholder="Select Invoice Status">
                                                <option value="">Select Invoice Status</option>
                                                <option value="Draft" {{ old('invoice_status', isset($invoice) ? $invoice->invoice_status : '') == 'Draft' ? 'selected' : '' }}>Draft</option>
                                                <option value="Unpaid/Credit" {{ old('invoice_status', isset($invoice) ? $invoice->invoice_status : '') == 'Unpaid/Credit' ? 'selected' : '' }}>Unpaid/Credit</option>
                                                <option value="Paid" {{ old('invoice_status', isset($invoice) ? $invoice->invoice_status : '') == 'Paid' ? 'selected' : '' }}>Paid</option>
                                                <option value="Partially Paid" {{ old('invoice_status', isset($invoice) ? $invoice->invoice_status : '') == 'Partially Paid' ? 'selected' : '' }}>Partially Paid</option>
                                            </select>
                                            <label for="invoice_status">Invoice Status *</label>
                                            @error('invoice_status')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <select name="payment_mode" id="payment_mode" class="form-select select2 @error('payment_mode') is-invalid @enderror">
                                                <option value="">Select Payment Mode</option>
                                                <option value="Cash" {{ old('payment_mode', isset($invoice) ? $invoice->payment_mode : '') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                                <option value="Bank (Cheque)" {{ old('payment_mode', isset($invoice) ? $invoice->payment_mode : '') == 'Bank (Cheque)' ? 'selected' : '' }}>Bank (Cheque)</option>
                                                <option value="Online (UPI)" {{ old('payment_mode', isset($invoice) ? $invoice->payment_mode : '') == 'Online (UPI)' ? 'selected' : '' }}>Online (UPI)</option>
                                            </select>
                                            <label for="payment_mode">Payment Mode</label>
                                            @error('payment_mode')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Cheque / UPI field -->
                                    <div class="col-md-12" id="extra_field" style="display:none;">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" name="extra_input" id="extra_input" placeholder="Enter Cheque / UPI No" value="{{ old('extra_input', isset($invoice) ? $invoice->extra_input : '') }}">
                                            <label id="extra_label">Cheque / UPI No</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control due_date" id="due_date" name="due_date" value="{{ old('due_date', isset($invoice) ? ($invoice->due_date ? $invoice->due_date->format('d-m-Y') : '') : date('d-m-Y')) }}">
                                            <label for="due_date">Due Date</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <textarea class="form-control" id="notes" name="notes" placeholder="Additional Notes">{{ old('notes', isset($invoice) ? $invoice->notes : '') }}</textarea>
                                            <label for="notes">Additional Notes</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <input type="file" class="form-control" id="signature_file" name="signature_file">
                                            <label for="signature_file">Authorized Signature / Stamp Upload</label>
                                            @if(isset($invoice) && $invoice->signature_file)
                                                <div class="mt-1">
                                                    <a href="{{ asset($invoice->signature_file) }}" target="_blank" class="small text-primary"><i class="ri ri-file-line"></i> View</a>
                                                </div>
                                            @endif
                                            <small class="text-muted d-block mt-1">Max file size: 2MB. Supported formats: JPG, PNG, JPEG, WEBP, PDF, DOC, DOCX</small>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <input type="file" class="form-control" id="attachment_file" name="attachment_file">
                                            <label for="attachment_file">Attachments</label>
                                            @if(isset($invoice) && $invoice->attachment_file)
                                                <div class="mt-1">
                                                    <a href="{{ asset($invoice->attachment_file) }}" target="_blank" class="small text-primary"><i class="ri ri-file-line"></i> View</a>
                                                </div>
                                            @endif
                                            <small class="text-muted d-block mt-1">Max file size: 2MB. Supported formats: JPG, PNG, JPEG, WEBP, PDF, DOC, DOCX</small>
                                        </div>
                                    </div>
                                    <!-- Show Fields in Customer Invoice PDF -->
                                    <div class="border-top pt-5 mt-5">
                                        <h6 class="fw-bold mb-2">Show in Customer Invoice PDF</h6>
                                        <div class="row">
                                            @php
                                                $selected_fields = old('show_fields', isset($invoice->show_fields) ? $invoice->show_fields : ['amount', 'discount', 'tax', 'subtotal', 'grandtotal']);
                                            @endphp
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="show_amount" name="show_fields[]" value="amount" {{ in_array('amount', $selected_fields) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="show_amount">Show Amount</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="show_discount" name="show_fields[]" value="discount" {{ in_array('discount', $selected_fields) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="show_discount">Show Discount</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="show_tax" name="show_fields[]" value="tax" {{ in_array('tax', $selected_fields) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="show_tax">Show Tax (GST/IGST)</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4 mt-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="show_subtotal" name="show_fields[]" value="subtotal" {{ in_array('subtotal', $selected_fields) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="show_subtotal">Show Sub Total</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4 mt-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="show_grandtotal" name="show_fields[]" value="grandtotal" {{ in_array('grandtotal', $selected_fields) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="show_grandtotal">Show Grand Total</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4 mt-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="show_due" name="show_fields[]" value="due" {{ in_array('due', $selected_fields) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="show_due">Show Due Amount</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="card-header-box mb-4">
                                    <h5 class="mb-0">Invoice Summary</h5>
                                </div>
                                <div class="summary-box px-2">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="text-secondary fw-medium">Sub total:</span>
                                        <span class="fw-bold h5 mb-0" id="sub_total_val">{{ old('sub_total', isset($invoice) ? number_format($invoice->sub_total, 2, '.', '') : '0.00') }}</span>
                                        <input type="hidden" name="sub_total" id="sub_total" value="{{ old('sub_total', isset($invoice) ? number_format($invoice->sub_total, 2, '.', '') : '0.00') }}">
                                    </div>
                                    <div class="row g-2 align-items-center mb-3 pb-3 border-bottom">
                                        <div class="col-4">
                                            <span class="text-secondary fw-medium">Discount:</span>
                                        </div>
                                        <div class="col-8 d-flex align-items-center">
                                            <div class="input-group input-group-sm ms-auto" style="width: 140px;">
                                                <input type="number" step="any" name="discount_percent" id="discount_percent" class="form-control text-end" value="{{ old('discount_percent', isset($invoice) ? number_format($invoice->discount_percent, 2, '.', '') : '0.00') }}">
                                                <span class="input-group-text bg-white">%</span>
                                            </div>
                                            <span class="fw-bold ms-3" style="text-align: right;" id="discount_val">{{ old('discount', isset($invoice) ? number_format($invoice->discount, 2, '.', '') : '0.00') }}</span>
                                            <input type="hidden" name="discount" id="discount" value="{{ old('discount', isset($invoice) ? number_format($invoice->discount, 2, '.', '') : '0.00') }}">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <span class="text-secondary fw-medium">Total:</span>
                                        <span class="fw-bold h5 mb-0" id="total_val">{{ old('total', isset($invoice) ? number_format($invoice->total, 2, '.', '') : '0.00') }}</span>
                                        <input type="hidden" name="total" id="total" value="{{ old('total', isset($invoice) ? number_format($invoice->total, 2, '.', '') : '0.00') }}">
                                    </div>
                                    <div class="mb-4 pt-2 border-top">
                                        <label class="text-secondary fw-medium mb-2 d-block">Other State?</label>
                                        <div class="d-flex gap-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="other_state" id="other_state_yes" value="yes" {{ old('other_state', isset($invoice) && $invoice->other_state ? 'yes' : 'no') == 'yes' ? 'checked' : '' }} onclick="return false;">
                                                <label class="form-check-label" for="other_state_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="other_state" id="other_state_no" value="no" {{ old('other_state', isset($invoice) && $invoice->other_state ? 'yes' : 'no') == 'no' ? 'checked' : '' }} onclick="return false;">
                                                <label class="form-check-label" for="other_state_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="igst_section" style="display:none;">
                                        <div class="row g-2 align-items-center mb-3">
                                            <div class="col-4"><span class="text-secondary fw-medium">IGST</span></div>
                                            <div class="col-8 d-flex align-items-center">
                                                <div class="input-group input-group-sm ms-auto" style="width: 140px;">
                                                    <input type="number" step="any" name="igst_percent" id="igst_percent" class="form-control text-end" value="{{ old('igst_percent', isset($invoice) ? number_format($invoice->igst_percent, 2, '.', '') : '18.00') }}">
                                                    <span class="input-group-text bg-white">%</span>
                                                </div>
                                                <span class="fw-bold ms-3" style="text-align: right;" id="igst_val">{{ old('igst', isset($invoice) ? number_format($invoice->igst, 2, '.', '') : '0.00') }}</span>
                                                <input type="hidden" name="igst" id="igst" value="{{ old('igst', isset($invoice) ? number_format($invoice->igst, 2, '.', '') : '0.00') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div id="cgst_sgst_section">
                                        <div class="row g-2 align-items-center mb-2">
                                            <div class="col-4"><span class="text-secondary fw-medium">CGST</span></div>
                                            <div class="col-8 d-flex align-items-center">
                                                <div class="input-group input-group-sm ms-auto" style="width: 140px;">
                                                    <input type="number" step="any" name="cgst_percent" id="cgst_percent" class="form-control text-end" value="{{ old('cgst_percent', isset($invoice) ? number_format($invoice->cgst_percent, 2, '.', '') : '9.00') }}">
                                                    <span class="input-group-text bg-white">%</span>
                                                </div>
                                                <span class="fw-bold ms-3" style="text-align: right;" id="cgst_val">{{ old('cgst', isset($invoice) ? number_format($invoice->cgst, 2, '.', '') : '0.00') }}</span>
                                                <input type="hidden" name="cgst" id="cgst" value="{{ old('cgst', isset($invoice) ? number_format($invoice->cgst, 2, '.', '') : '0.00') }}">
                                            </div>
                                        </div>
                                        <div class="row g-2 align-items-center mb-3">
                                            <div class="col-4"><span class="text-secondary fw-medium">SGST</span></div>
                                            <div class="col-8 d-flex align-items-center">
                                                <div class="input-group input-group-sm ms-auto" style="width: 140px;">
                                                    <input type="number" step="any" name="sgst_percent" id="sgst_percent" class="form-control text-end" value="{{ old('sgst_percent', isset($invoice) ? number_format($invoice->sgst_percent, 2, '.', '') : '9.00') }}">
                                                    <span class="input-group-text bg-white">%</span>
                                                </div>
                                                <span class="fw-bold ms-3" style="text-align: right;" id="sgst_val">{{ old('sgst', isset($invoice) ? number_format($invoice->sgst, 2, '.', '') : '0.00') }}</span>
                                                <input type="hidden" name="sgst" id="sgst" value="{{ old('sgst', isset($invoice) ? number_format($invoice->sgst, 2, '.', '') : '0.00') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-2 border-top border-bottom mb-3">
                                        <span class="text-secondary fw-medium">Tax Amount:</span>
                                        <span class="fw-bold" id="tax_amount_val">{{ old('tax_amount', isset($invoice) ? number_format($invoice->tax_amount, 2, '.', '') : '0.00') }}</span>
                                        <input type="hidden" name="tax_amount" id="tax_amount" value="{{ old('tax_amount', isset($invoice) ? number_format($invoice->tax_amount, 2, '.', '') : '0.00') }}">
                                    </div>
                                    {{-- <div class="row g-2 align-items-center mb-3">
                                        <div class="col-4"><span class="text-secondary fw-medium">Other Charges:</span></div>
                                        <div class="col-8">
                                            <input type="number" step="any" name="other_charges" id="other_charges" class="form-control form-control-sm text-end ms-auto" style="width: 140px;" value="{{ old('other_charges', '0.00') }}">
                                        </div>
                                    </div> --}}
                                    <div class="row g-2 align-items-center mb-3 pb-3 border-bottom">
                                        <div class="col-4"><span class="text-secondary fw-medium">Round Off:</span></div>
                                        <div class="col-8 d-flex align-items-center justify-content-end">
                                            <div class="d-flex gap-3 me-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="round_off_type" id="round_off_add" value="Add" {{ old('round_off_type', isset($invoice) ? $invoice->round_off_type : 'Add') == 'Add' ? 'checked' : '' }}>
                                                    <label class="form-check-label small fw-medium" for="round_off_add">Add</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="round_off_type" id="round_off_less" value="Less" {{ old('round_off_type', isset($invoice) ? $invoice->round_off_type : 'Add') == 'Less' ? 'checked' : '' }}>
                                                    <label class="form-check-label small fw-medium" for="round_off_less">Less</label>
                                                </div>
                                            </div>
                                            <input type="number" step="any" name="round_off" id="round_off" class="form-control form-control-sm text-end" style="width: 100px;" value="{{ old('round_off', isset($invoice) ? number_format($invoice->round_off, 2, '.', '') : '0.00') }}">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <span class="fw-bold mb-0">Grand Total:</span>
                                        <span class="fw-bold mb-0 text-primary" id="grand_total_val">{{ old('grand_total', isset($invoice) ? number_format($invoice->grand_total, 2, '.', '') : '0.00') }}</span>
                                        <input type="hidden" name="grand_total" id="grand_total" value="{{ old('grand_total', isset($invoice) ? number_format($invoice->grand_total, 2, '.', '') : '0.00') }}">
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-secondary small fw-medium">Paid So Far:</span>
                                        <span class="fw-bold text-dark" id="paid_so_far_val">₹{{ number_format(isset($invoice) ? $invoice->received_amount : 0, 2) }} <i class="mdi mdi-history text-info ms-1 cursor-pointer" title="View Payment History"></i></span>
                                    </div>
                                    <div class="d-flex justify-content-between py-2 border-bottom align-items-center">
                                        <span class="small fw-bold">Add New Payment:</span>
                                        <div class="d-flex flex-column align-items-end">
                                            <input type="number" step="any" name="received_amount" id="received_amount" class="form-control form-control-sm text-end" value="{{ old('received_amount', '0.00') }}">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center pt-2 text-danger">
                                        <span class="fw-bold mb-0">Due Amount:</span>
                                        <span class="fw-bold mb-0" id="due_amount_val">{{ old('due_amount', isset($invoice) ? number_format($invoice->due_amount, 2, '.', '') : '0.00') }}</span>
                                        <input type="hidden" name="due_amount" id="due_amount" value="{{ old('due_amount', isset($invoice) ? number_format($invoice->due_amount, 2, '.', '') : '0.00') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ url('sales_invoices') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            dropdownParent: $('body')
        });
        
        if ($('input[name="other_state"]:checked').val() == 'yes') {
            $('#igst_section').show();
            $('#cgst_sgst_section').hide();
        } else {
            $('#igst_section').hide();
            $('#cgst_sgst_section').show();
        }

        if ({{ isset($invoice) || old('items') ? 'true' : 'false' }}) {
            calculateTotals();
        }

        $('#so_id').on('change', function() {
            var soId = $(this).val();
            if (soId) {
                $.ajax({
                    url: "{{ url('sales_invoices/get-sale-order-details') }}/" + soId,
                    type: "GET",
                    success: function(data) {
                        if (data.success) {
                            $('#customer_id').val(data.customer_id).trigger('change');
                            $('#address').val(data.shipping_address);
                            if (data.other_state == 'yes') {
                                $('#other_state_yes').prop('checked', true);
                                $('#igst_section').show();
                                $('#cgst_sgst_section').hide();
                            } else {
                                $('#other_state_no').prop('checked', true);
                                $('#igst_section').hide();
                                $('#cgst_sgst_section').show();
                            }
                            
                            $('#discount_percent').val(data.discount_percent || 0);
                            $('#igst_percent').val(data.igst_percent || 18);
                            $('#cgst_percent').val(data.cgst_percent || 9);
                            $('#sgst_percent').val(data.sgst_percent || 9);

                            $('#item-rows').empty();
                            $.each(data.items, function(index, item) {
                                var html = `
                                 <tr class="item-row">
                                    <td>
                                        <span class="brand-text">${item.brand_name}</span>
                                        <input type="hidden" name="items[${index}][brand_id]" class="brand-id" value="${item.brand_id}">
                                        <input type="hidden" name="items[${index}][brand_name]" class="brand-name" value="${item.brand_name}">
                                        <input type="hidden" name="items[${index}][stock_entry_item_id]" class="stock-entry-item-id" value="${item.stock_entry_item_id || ''}">
                                    </td>
                                    <td>
                                        <span class="item-text">${item.item_name} (${item.sleeve})</span>
                                        <input type="hidden" name="items[${index}][item_id]" class="item-id" value="${item.item_id}">
                                        <input type="hidden" name="items[${index}][item_name]" class="item-name" value="${item.item_name}">
                                        <input type="hidden" name="items[${index}][sleeve_type]" class="sleeve-type" value="${item.sleeve}">
                                    </td>
                                    <td>
                                        <span class="size-text">${item.size_name || item.size_id || ''}</span>
                                        <input type="hidden" name="items[${index}][size]" class="size-id" value="${item.size_id}">
                                        <input type="hidden" name="items[${index}][size_name]" class="size-name" value="${item.size_name || item.size_id || ''}">
                                    </td>
                                    <td>
                                        <span class="art-no-text">${item.art_no || ''}</span>
                                        <input type="hidden" name="items[${index}][art_no]" class="art-no" value="${item.art_no}">
                                    </td>

                                    <td>
                                        <span class="uom-text">${item.uom_code}</span>
                                        <input type="hidden" name="items[${index}][uom_id]" class="uom-id" value="${item.uom_id}">
                                        <input type="hidden" name="items[${index}][uom_code]" class="uom-code" value="${item.uom_code}">
                                    </td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <input type="number" step="any" class="form-control qty" name="items[${index}][quantity]" value="${item.qty}">
                                            <label>Qty *</label>
                                        </div>
                                    </td>
                                    <td>
                                         <div class="form-floating form-floating-outline">
                                            <input type="number" step="any" class="form-control rate" name="items[${index}][rate]" value="${item.rate}">
                                            <label>Rate *</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <input type="number" step="any" class="form-control mrp" name="items[${index}][mrp]" value="${item.mrp || 0}">
                                            <label>MRP</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control amount" name="items[${index}][amount]" value="${item.amount}" readonly>
                                            <label>Amount *</label>
                                        </div>
                                    </td>
                                </tr>`;
                                $('#item-rows').append(html);
                            });
                            calculateTotals();
                        }
                    }
                });
            }
        });

        $('#customer_id').on('change', function() {
            let customerId = $(this).val();
            if (customerId) {
                let customerStateId = $(this).find(':selected').data('state-id');
                let companyStateId = "{{ $web_settings->state_id ?? '' }}";

                if (customerStateId && companyStateId) {
                    if (customerStateId == companyStateId) {
                        $('#other_state_no').prop('checked', true).trigger('change');
                        $('#cgst_percent').val("{{ $web_settings->cgst ?? 9 }}");
                        $('#sgst_percent').val("{{ $web_settings->sgst ?? 9 }}");
                        $('#igst_percent').val(0);
                    } else {
                        $('#other_state_yes').prop('checked', true).trigger('change');
                        $('#igst_percent').val("{{ $web_settings->igst ?? 18 }}");
                        $('#cgst_percent').val(0);
                        $('#sgst_percent').val(0);
                    }
                }
                calculateTotals();
            }
        });



        function calculateTotals() {
            var subTotal = 0;
            $('.amount').each(function() {
                subTotal += parseFloat($(this).val()) || 0;
            });
            $('#sub_total_val').text(subTotal.toFixed(2));
            $('#sub_total').val(subTotal.toFixed(2));

            var discPercent = parseFloat($('#discount_percent').val()) || 0;
            var discount = (subTotal * discPercent) / 100;
            $('#discount_val').text(discount.toFixed(2));
            $('#discount').val(discount.toFixed(2));

            var total = subTotal - discount;
            $('#total_val').text(total.toFixed(2));
            $('#total').val(total.toFixed(2));

            var otherState = $('input[name="other_state"]:checked').val();
            var taxAmount = 0;
            var igst = 0, cgst = 0, sgst = 0;

            if (otherState == 'yes') {
                var igstPercent = parseFloat($('#igst_percent').val()) || 0;
                igst = (total * igstPercent) / 100;
                taxAmount = igst;
                $('#igst_val').text(igst.toFixed(2));
                $('#igst').val(igst.toFixed(2));
                $('#igst_section').show();
                $('#cgst_sgst_section').hide();
            } else {
                var cgstP = parseFloat($('#cgst_percent').val()) || 0;
                var sgstP = parseFloat($('#sgst_percent').val()) || 0;
                cgst = (total * cgstP) / 100;
                sgst = (total * sgstP) / 100;
                taxAmount = cgst + sgst;
                $('#cgst_val').text(cgst.toFixed(2));
                $('#cgst').val(cgst.toFixed(2));
                $('#sgst_val').text(sgst.toFixed(2));
                $('#sgst').val(sgst.toFixed(2));
                $('#igst_section').hide();
                $('#cgst_sgst_section').show();
            }

            $('#tax_amount_val').text(taxAmount.toFixed(2));
            $('#tax_amount').val(taxAmount.toFixed(2));

            var otherCharges = parseFloat($('#other_charges').val()) || 0;
            
            var totalBeforeRoundOff = total + taxAmount + otherCharges;
            var nearestWhole = Math.round(totalBeforeRoundOff);
            var roundOff = nearestWhole - totalBeforeRoundOff;
            
            var roundOffType = $('input[name="round_off_type"]:checked').val();
            var roundOffAmount = parseFloat($('#round_off').val()) || 0;

            var grandTotal = totalBeforeRoundOff;
            if (roundOffType == 'Add') {
                grandTotal += roundOffAmount;
            } else {
                grandTotal -= roundOffAmount;
            }

            $('#grand_total_val').text(grandTotal.toFixed(2));
            $('#grand_total').val(grandTotal.toFixed(2));

            var paidSoFarText = $('#paid_so_far_val').text().replace(/[^\d.]/g, '');
            var paidSoFar = parseFloat(paidSoFarText) || 0;
            var receivedNow = parseFloat($('#received_amount').val()) || 0;
            
            var due = grandTotal - (paidSoFar + receivedNow);
            $('#due_amount_val').text(due.toFixed(2));
            $('#due_amount').val(due.toFixed(2));
        }

        $(document).on('input', '#discount_percent, #igst_percent, #cgst_percent, #sgst_percent, #other_charges, #round_off, #received_amount', function() {
            calculateTotals();
        });

        $(document).on('change', 'input[name="other_state"], input[name="round_off_type"]', function() {
            calculateTotals();
        });

        $('#payment_mode').on('change', function() {
            var mode = $(this).val();
            if (mode == 'Bank (Cheque)') {
                $('#extra_field').show();
                $('#extra_label').text('Cheque Number');
                $('#extra_input').attr('placeholder', 'Enter Cheque Number');
            } else if (mode == 'Online (UPI)') {
                $('#extra_field').show();
                $('#extra_label').text('UPI ID');
                $('#extra_input').attr('placeholder', 'Enter UPI ID');
            } else {
                $('#extra_field').hide();
                $('#extra_input').val('');
            }
        });

        if ($('#payment_mode').val()) {
            $('#payment_mode').trigger('change');
        }

        $('input[name="other_state"]').on('change', function() {
            if ($(this).val() == 'yes') {
                $('#igst_section').show();
                $('#cgst_sgst_section').hide();
                igstPercent = 18; 
                cgstPercent = 0;
                sgstPercent = 0;
            } else {
                $('#igst_section').hide();
                $('#cgst_sgst_section').show();
                igstPercent = 0;
                cgstPercent = 9;
                sgstPercent = 9;
            }
            calculateTotals();
        });

        $('#received_amount').on('input', function() {
            calculateTotals();
        });

        $('#item-rows').on('input', '.qty, .rate', function() {
            var row = $(this).closest('.item-row');
            var qty = parseFloat(row.find('.qty').val()) || 0;
            var rate = parseFloat(row.find('.rate').val()) || 0;
            row.find('.amount').val((qty * rate).toFixed(2));
            calculateTotals();
        });

        // $('form.common-form').on('submit', function(e) {
        //     if ($('#item-rows .item-row').length === 0 || $('#item-rows .item-row td').hasClass('text-center')) {
        //         e.preventDefault();
        //         Swal.fire({
        //             icon: 'error',
        //             title: 'No Items',
        //             text: 'Please select a Sales Order to fetch items before submitting.'
        //         });
        //         return false;
        //     }
        // });
    });
</script>
@endsection
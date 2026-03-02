@extends('layouts.common')
@section('title', 'View Sale Invoice - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Sales Invoice</h4>
                <div class="d-flex gap-2">
                    <a href="{{ url('sales_invoices/download-pdf/'.$invoice->id) }}" class="btn btn-primary">
                    <i class="ri ri-arrow-down-line back-arrow"></i>Download
                    </a>
                    <a href="{{ url('sales_invoices') }}" class="btn btn-secondary">
                        <i class="ri ri-arrow-left-line back-arrow"></i>Back
                    </a>
                </div>
            </div>

            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Order Details -->
                        <div class="col-lg-12">
                            <h6>Order Details:</h6>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Invoice No:</label>
                            <div class="text-muted">{{ $invoice->inv_no }}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Invoice Date:</label>
                            <div class="text-muted">{{ $invoice->inv_date->format('d-M-Y') }}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Customer / Buyer Name:</label>
                            <div class="text-muted">{{ $invoice->customer ? $invoice->customer->name : 'N/A' }} ({{ $invoice->customer ? $invoice->customer->code : '' }})</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Linked SO No:</label>
                            <div class="text-muted">{{ $invoice->saleOrder ? $invoice->saleOrder->so_no : 'N/A' }}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Delivery Address:</label>
                            <div class="text-muted">{{ $invoice->delivery_address ?? 'N/A' }}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Remarks:</label>
                            <div class="text-muted">{{ $invoice->remarks ?? 'N/A' }}</div>
                        </div>

                        <div class="col-lg-12">
                            <hr>
                        </div>

                        <!-- Item Details -->
                        <div class="col-lg-12">
                            <h6>Item Details:</h6>
                        </div>
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>S.No</th>
                                            <th>Brand</th>
                                            <th>Item (Code)</th>
                                            <th>Art No</th>
                                            <th>HSN/SAC</th>
                                            <th>Size</th>
                                            <th>Sleeve</th>
                                            <th>UOM</th>
                                            <th class="text-end">Quantity</th>
                                            <th class="text-end">Rate</th>
                                            <th class="text-end">MRP</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoice->items as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->brandCategory ? $item->brandCategory->name : 'N/A' }}</td>
                                            <td>{{ $item->item ? $item->item->name : 'N/A' }} <span class="mini-title">({{ $item->item ? $item->item->code : '' }})</span></td>
                                            <td>{{ $item->art_no ?? '-' }}</td>
                                            <td>{{ $item->hsn_sac ?? '-' }}</td>
                                            <td>{{ $item->size ?? '-' }}</td>
                                            <td>{{ $item->sleeve_type ?? '-' }}</td>
                                            <td>{{ $item->uom ? $item->uom->uom_code : ($item->item && $item->item->uom ? $item->item->uom->uom_code : '-') }}</td>
                                            <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                                            <td class="text-end">₹{{ number_format($item->rate, 2) }}</td>
                                            <td class="text-end">₹{{ number_format($item->mrp ?? 0, 2) }}</td>
                                            <td class="text-end">₹{{ number_format($item->amount, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <hr>
                        </div>
                        
                        <!-- Show in Customer Invoice PDF Section -->
                        <div class="col-lg-12 mt-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="mb-3 fw-bold">Show in Customer Invoice PDF</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="showAmount" {{ is_array($invoice->show_fields) && in_array('amount', $invoice->show_fields) ? 'checked' : '' }} disabled>
                                                <label class="form-check-label" for="showAmount">Show Amount</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="showSubTotal" {{ is_array($invoice->show_fields) && in_array('subtotal', $invoice->show_fields) ? 'checked' : '' }} disabled>
                                                <label class="form-check-label" for="showSubTotal">Show Sub Total</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="showDiscount" {{ is_array($invoice->show_fields) && in_array('discount', $invoice->show_fields) ? 'checked' : '' }} disabled>
                                                <label class="form-check-label" for="showDiscount">Show Discount</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="showGrandTotal" {{ is_array($invoice->show_fields) && in_array('grandtotal', $invoice->show_fields) ? 'checked' : '' }} disabled>
                                                <label class="form-check-label" for="showGrandTotal">Show Grand Total</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="showTax" {{ is_array($invoice->show_fields) && in_array('tax', $invoice->show_fields) ? 'checked' : '' }} disabled>
                                                <label class="form-check-label" for="showTax">Show Tax (GST/IGST)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="showDue" {{ is_array($invoice->show_fields) && in_array('due', $invoice->show_fields) ? 'checked' : '' }} disabled>
                                                <label class="form-check-label" for="showDue">Show Due Amount</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <h6>Invoice Summary:</h6>
                        </div>
                        <div class="col-lg-12">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="summary-left border rounded p-3 h-100">
                                        <div class="d-flex justify-content-between mb-2">
                                            <label class="detail-title">Sub Total:</label>
                                            <div class="text-muted fw-bold">₹{{ number_format($invoice->sub_total, 2) }}</div>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <label class="detail-title">Discount ({{ number_format($invoice->discount_percent, 2) }}%):</label>
                                            <div class="text-muted">₹{{ number_format($invoice->discount, 2) }}</div>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                            <label class="detail-title">Total:</label>
                                            <div class="text-muted fw-bold">₹{{ number_format($invoice->total, 2) }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="detail-title">Other State:</label>
                                            <div class="text-muted">{{ $invoice->other_state ? 'Yes' : 'No' }}</div>
                                        </div>
                                        @if($invoice->other_state)
                                        <div class="d-flex justify-content-between mb-2">
                                            <label class="detail-title">IGST ({{ number_format($invoice->igst_percent, 2) }}%):</label>
                                            <div class="text-muted">₹{{ number_format($invoice->igst, 2) }}</div>
                                        </div>
                                        @else
                                        <div class="d-flex justify-content-between mb-2">
                                            <label class="detail-title">CGST ({{ number_format($invoice->cgst_percent, 2) }}%):</label>
                                            <div class="text-muted">₹{{ number_format($invoice->cgst, 2) }}</div>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <label class="detail-title">SGST ({{ number_format($invoice->sgst_percent, 2) }}%):</label>
                                            <div class="text-muted">₹{{ number_format($invoice->sgst, 2) }}</div>
                                        </div>
                                        @endif
                                        <div class="d-flex justify-content-between mb-2 pt-2 border-top">
                                            <label class="detail-title">Tax Amount:</label>
                                            <div class="text-muted fw-bold">₹{{ number_format($invoice->tax_amount, 2) }}</div>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <label class="detail-title">Other Charges:</label>
                                            <div class="text-muted">₹{{ number_format($invoice->other_charges, 2) }}</div>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <label class="detail-title">Round Off ({{ $invoice->round_off_type }}):</label>
                                            <div class="text-muted">₹{{ number_format($invoice->round_off, 2) }}</div>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2 pt-3 border-top">
                                            <label class="detail-title h5 mb-0">Grand Total:</label>
                                            <div class="fw-bold h5 mb-0 text-success">₹{{ number_format($invoice->grand_total, 2) }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="summary-right border rounded p-3 h-100">
                                        <div class="d-flex justify-content-between mb-2">
                                            <label class="detail-title">Received Amount:</label>
                                            <div class="text-muted fw-bold">₹{{ number_format($invoice->received_amount, 2) }}</div>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2 text-danger">
                                            <label class="detail-title">Due Amount:</label>
                                            <div class="fw-bold h5">₹{{ number_format($invoice->due_amount, 2) }}</div>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <label class="detail-title">Invoice Status:</label>
                                            <div class="text-right"><span class="badge bg-primary">{{ $invoice->invoice_status }}</span></div>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <label class="detail-title">Payment Mode:</label>
                                            <div class="text-muted">{{ $invoice->payment_mode ?? 'N/A' }}</div>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <label class="detail-title">Cheque / UPI Ref:</label>
                                            <div class="text-muted">{{ $invoice->extra_input ?? 'N/A' }}</div>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <label class="detail-title">Due Date:</label>
                                            <div class="text-muted">{{ $invoice->due_date ? $invoice->due_date->format('d-M-Y') : 'N/A' }}</div>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <label class="detail-title">Additional Notes:</label>
                                            <div class="text-muted">{{ $invoice->notes ?? 'N/A' }}</div>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2 border-top pt-2">
                                            <label class="detail-title">Authorized Signature:</label>
                                            <div class="text-muted">
                                                @if($invoice->signature_file)
                                                    <a href="{{ asset($invoice->signature_file) }}" target="_blank">
                                                        <i class="ri ri-file-line"></i> View
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <label class="detail-title">Attachments:</label>
                                            <div class="text-muted">
                                                @if($invoice->attachment_file)
                                                    <a href="{{ asset($invoice->attachment_file) }}" target="_blank">
                                                        <i class="ri ri-file-line"></i> View
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

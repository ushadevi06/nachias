@extends('layouts.common')
@section('title', 'View Purchase Invoice - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Purchase Invoice</h4>
                <div>
                    <a href="{{ url('purchase_invoices') }}" class="btn btn-secondary me-2">
                        <i class="ri ri-arrow-left-line"></i> Back
                    </a>
                    {{-- <a href="{{ url('purchase_invoices/download-pdf/' . $invoice->id) }}" class="btn btn-primary" target="_blank">
                        <i class="ri ri-printer-line"></i> Download PDF
                    </a> --}}
                </div>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <h6>Order Details:</h6>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="detail-title">Invoice No: </label>
                            <div class="text-muted">{{ $invoice->invoice_no }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Invoice Date:</label>
                            <div class="text-muted">{{ $invoice->invoice_date->format('d-m-Y') }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Supplier:</label>
                            <div class="text-muted">
                                {{ $invoice->supplier->name ?? 'N/A' }}
                                @if($invoice->supplier && $invoice->supplier->supplier_code)
                                ({{ $invoice->supplier->supplier_code }})
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">PO Reference No:</label>
                            <div class="text-muted">{{ $invoice->po_reference ?? '-' }}</div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Item Details:</h6>
                        </div>
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Item</th>
                                            <th>HSN Code</th>
                                            <th>Quantity</th>
                                            <th>Rate</th>
                                            <th>Amount</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($invoice->items as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                {{ $item->rawMaterial->name ?? 'N/A' }}
                                                @if($item->rawMaterial && $item->rawMaterial->material_code)
                                                <span class="mini-title">({{ $item->rawMaterial->material_code }})</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->hsn_code ?? '-' }}</td>
                                            <td>{{ number_format($item->quantity, 0) }}</td>
                                            <td>₹{{ number_format($item->rate, 2) }}</td>
                                            <td>₹{{ number_format($item->quantity * $item->rate, 2) }}</td>
                                            <td>{{ $item->remarks ?? '-' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No items found</td>
                                        </tr>
                                        @endforelse
                                        <tr>
                                            <td colspan="5" class="text-end"><strong>Subtotal</strong></td>
                                            <td colspan="2"><strong>₹{{ number_format($invoice->sub_total, 2) }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <h6>Tax & Charges:</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Discount ({{ number_format($invoice->discount_percent, 2) }}%):</label>
                            <div class="text-muted">₹{{ number_format($invoice->discount_amount, 2) }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Taxable Amount:</label>
                            <div class="text-muted">₹{{ number_format($invoice->taxable_amount, 2) }}</div>
                        </div>

                        @if($invoice->other_state == 'Y')
                        <div class="col-md-3">
                            <label class="detail-title">IGST ({{ number_format($invoice->igst_percent, 2) }}%):</label>
                            <div class="text-muted">₹{{ number_format($invoice->igst_amount, 2) }}</div>
                        </div>
                        @else
                        <div class="col-md-3">
                            <label class="detail-title">CGST ({{ number_format($invoice->cgst_percent, 2) }}%):</label>
                            <div class="text-muted">₹{{ number_format($invoice->cgst_amount, 2) }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">SGST ({{ number_format($invoice->sgst_percent, 2) }}%):</label>
                            <div class="text-muted">₹{{ number_format($invoice->sgst_amount, 2) }}</div>
                        </div>
                        @endif

                        @if($invoice->charges && $invoice->charges->count() > 0)
                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Other Charges:</h6>
                        </div>
                        @foreach($invoice->charges as $charge)
                        <div class="col-md-3">
                            <label class="detail-title">{{ $charge->charge_name }}:</label>
                            <div class="text-muted">₹{{ number_format($charge->charge_amount, 2) }}</div>
                        </div>
                        @endforeach
                        <div class="col-md-3">
                            <label class="detail-title">Total Other Charges:</label>
                            <div class="text-muted">₹{{ number_format($invoice->other_charges, 2) }}</div>
                        </div>
                        @endif

                        <div class="col-md-3">
                            <label class="detail-title">Round Off:</label>
                            <div class="text-muted">
                                {{ $invoice->round_off_type }} 
                                ₹{{ number_format($invoice->round_off, 2) }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Total Invoice Amount:</label>
                            <div class="fw-bold text-success">₹{{ number_format($invoice->grand_total, 2) }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Receive Amount:</label>
                            <div class="fw-bold">
                                ₹{{ number_format($invoice->receive_amount, 2) }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Due Amount:</label>
                            <div class="fw-bold text-danger">
                                ₹{{ number_format($invoice->due_amount, 2) }}
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Invoice Details:</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Invoice Status:</label>
                            <div>
                                @php
                                $statusClass = match($invoice->invoice_status) {
                                'Paid' => 'bg-label-success',
                                'Unpaid/Credit' => 'bg-label-warning',
                                'Partially Paid' => 'bg-label-info',
                                'Draft' => 'bg-label-secondary',
                                default => 'bg-label-secondary'
                                };
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $invoice->invoice_status }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Payment Mode:</label>
                            <div class="text-muted">{{ $invoice->payment_mode ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Due Date:</label>
                            <div class="text-muted">
                                {{ $invoice->due_date ? $invoice->due_date->format('d-M-Y') : '-' }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Attachments:</label>
                            <div class="text-muted">
                                @if($invoice->attachments != '')
                                <img src="{{ url('uploads/purchase_invoices/' . $invoice->attachments) }}" alt="document" class="detail-img">
                                @else
                                -
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Authorized Signature:</label>
                            <div class="text-muted">
                                @if($invoice->auth_sign != '')
                                <img src="{{ url('uploads/purchase_invoices/' . $invoice->auth_sign) }}" alt="signature" class="detail-img">
                                @else
                                -
                                @endif
                            </div>
                        </div>

                        @if($invoice->notes)
                        <div class="col-md-12">
                            <label class="detail-title">Additional Notes:</label>
                            <div class="text-muted">{{ $invoice->notes }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
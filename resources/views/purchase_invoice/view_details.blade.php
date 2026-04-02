@extends('layouts.common')
@section('title', 'View Purchase Invoice - ' . env('WEBSITE_NAME'))
@section('content')
    <div class="container-xxl section-padding">
        <div class="row">
            <div class="col-lg-12">
                <!-- Header Section -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold text-primary mb-1">Purchase Invoice Details</h3>
                    </div>
                    <div class="d-flex gap-2">
                        {{--<a href="{{ url('purchase_invoices/download-pdf/' . $invoice->id) }}" target="_blank"
                            class="btn btn-primary d-flex align-items-center">
                            <i class="ri ri-download-line me-1"></i> Download
                        </a>
                        <a href="{{ url('purchase_invoices/print/'.$invoice->id) }}" target="_blank"
                            class="btn btn-primary d-flex align-items-center">
                            <i class="ri ri-printer-line me-1"></i> Print
                        </a> --}}
                        <a href="{{ url('purchase_invoices') }}"
                            class="btn btn-outline-secondary d-flex align-items-center">
                            <i class="ri ri-arrow-left-line me-1"></i> Back
                        </a>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-light py-3" style="border-radius: 12px 12px 0 0;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="text-dark mb-0 fw-bold">General Information</h5>
                            <span class="badge bg-white text-primary px-3 py-2">INV: #{{ $invoice->invoice_no }}</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4 text-break">
                            <div class="col-md-3">
                                <div class="mb-1 text-muted small fw-bold">Supplier</div>
                                <div class="fw-bold text-dark">
                                    {{ $invoice->supplier->name ?? 'N/A' }}
                                    @if($invoice->supplier && $invoice->supplier->supplier_code)
                                        <span class="text-primary small">({{ $invoice->supplier->supplier_code }})</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-1 text-muted text-uppercase small fw-bold">PO Reference</div>
                                <div class="fw-bold text-dark">{{ $invoice->po_reference ?? '-' }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-1 text-muted text-uppercase small fw-bold">Invoice Date</div>
                                <div class="fw-bold text-dark">{{ $invoice->invoice_date->format('d M, Y') }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-1 text-muted text-uppercase small fw-bold">Status</div>
                                <div>
                                    @php
                                        $statusClass = match ($invoice->invoice_status) {
                                            'Paid' => 'bg-success',
                                            'Unpaid/Credit' => 'bg-warning',
                                            'Partially Paid' => 'bg-info',
                                            'Draft' => 'bg-secondary',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }} px-3 py-2">{{ $invoice->invoice_status }}</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-1 text-muted text-uppercase small fw-bold">Commission Agent</div>
                                <div class="fw-bold text-dark">{{ $invoice->purchaseCommissionAgent->name ?? '-' }}</div>
                            </div>

                            <!-- Transport Fields -->
                            <div class="col-md-3 mt-3">
                                <div class="mb-1 text-muted text-uppercase small fw-bold">Transport Name</div>
                                <div class="fw-bold text-dark">{{ $invoice->transport ?? '-' }}</div>
                            </div>
                            <div class="col-md-3 mt-3">
                                <div class="mb-1 text-muted text-uppercase small fw-bold">Destination</div>
                                <div class="fw-bold text-dark">{{ $invoice->destination ?? '-' }}</div>
                            </div>
                            <div class="col-md-3 mt-3">
                                <div class="mb-1 text-muted text-uppercase small fw-bold">LR No. & Date</div>
                                <div class="fw-bold text-dark">
                                    {{ $invoice->lr_no ?? '-' }}
                                    @if($invoice->lr_date)
                                        <span class="text-primary small">({{ $invoice->lr_date->format('d/m/Y') }})</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3 mt-3">
                                <div class="mb-1 text-muted text-uppercase small fw-bold">Indent No. & Date</div>
                                <div class="fw-bold text-dark">
                                    {{ $invoice->indent_no ?? '-' }}
                                    @if($invoice->indent_date)
                                        <span class="text-primary small">({{ $invoice->indent_date->format('d/m/Y') }})</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3 mt-3">
                                <div class="mb-1 text-muted text-uppercase small fw-bold">Eway Bill No.</div>
                                <div class="fw-bold text-dark">{{ $invoice->eway_billno ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Item Details Table -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-light py-3"
                        style="border-radius: 12px 12px 0 0; border-bottom: 1px solid #f0f0f0;">
                        <h5 class="mb-0 fw-bold text-dark">Item Details</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3 text-muted text-uppercase small fw-bold" width="80">S.No</th>
                                        <th class="py-3 text-muted text-uppercase small fw-bold">Raw Material</th>
                                        <th class="py-3 text-muted text-uppercase small fw-bold text-center">HSN</th>
                                        <th class="py-3 text-muted text-uppercase small fw-bold text-center">Quantity</th>
                                        <th class="py-3 text-muted text-uppercase small fw-bold text-end">Rate</th>
                                        <th class="py-3 text-muted text-uppercase small fw-bold text-end pe-4">Total Amount
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($invoice->items->count() > 0)
                                        @foreach($invoice->items as $index => $item)
                                            <tr>
                                                <td class="ps-4 fw-bold">{{ sprintf('%02d', $index + 1) }}</td>
                                                <td>
                                                    <div class="fw-bold text-dark">{{ $item->rawMaterial->name ?? 'N/A' }}</div>
                                                    @if($item->rawMaterial && $item->rawMaterial->code)
                                                        <small class="text-primary fw-medium">({{ $item->rawMaterial->code }})</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $item->hsn_code ?? '-' }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-light text-dark px-3 py-2 fw-medium">
                                                        {{ number_format($item->quantity, 0) }}
                                                    </span>
                                                </td>
                                                <td class="text-end">₹{{ number_format($item->rate, 2) }}</td>
                                                <td class="text-end fw-bold text-dark pe-4">
                                                    ₹{{ number_format($item->quantity * $item->rate, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">No items found</td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot class="bg-light fw-bold border-top">
                                    <tr>
                                        <td colspan="5" class="text-end py-3 ps-4">Subtotal (Items)</td>
                                        <td class="text-end pe-4 py-3">₹{{ number_format($invoice->sub_total, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tax & Charges Section -->
                <div class="row g-4">
                    <div class="col-lg-7 text-break">
                        <!-- Pre-GST Charges -->
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                            <div class="card-header bg-light py-3"
                                style="border-radius: 12px 12px 0 0; border-bottom: 1px solid #f0f0f0;">
                                <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing: 0.5px;">
                                    Pre-GST Charges & Info</h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    @php $preGstCharges = $invoice->charges->where('tax_type', 'Pre-GST'); @endphp
                                    @if($preGstCharges->count() > 0)
                                        @foreach($preGstCharges as $charge)
                                            <div
                                                class="col-12 d-flex justify-content-between align-items-center border-bottom pb-3 mb-1">
                                                <span
                                                    class="text-muted small fw-bold text-uppercase">{{ $charge->charge_name }}</span>
                                                <span
                                                    class="fw-bold text-dark fs-6">₹{{ number_format($charge->charge_amount, 2) }}</span>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="col-12 text-muted small fst-italic py-2">No pre-GST charges applied.</div>
                                    @endif
                                    <div class="col-12 mt-3">
                                        <div class="d-flex justify-content-between align-items-center p-3 rounded-3"
                                            style="background-color: #eceef1; border: 1px solid #dee2e6;">
                                            <span class="fw-bold text-dark fs-6">Discount
                                                ({{ number_format($invoice->discount_percent, 2) }}%)</span>
                                            <span
                                                class="fw-bold fs-6">-₹{{ number_format($invoice->discount_amount, 2) }}</span>
                                        </div>
                                    </div>
                                    @if($invoice->notes)
                                        <div class="col-12 mt-2">
                                            <div class="text-muted text-uppercase small fw-bold mb-2">Additional Notes</div>
                                            <p class="small text-dark border p-3 rounded-3 bg-white shadow-sm mb-0"
                                                style="white-space: pre-line; border-color: #f0f0f0 !important;">
                                                {{ $invoice->notes }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- Post-GST Charges -->
                        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                            <div class="card-header bg-light py-3"
                                style="border-radius: 12px 12px 0 0; border-bottom: 1px solid #f0f0f0;">
                                <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing: 0.5px;">
                                    Post-GST Charges & Attachments</h6>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    @php $postGstCharges = $invoice->charges->where('tax_type', 'Post-GST'); @endphp
                                    @if($postGstCharges->count() > 0)
                                        @foreach($postGstCharges as $charge)
                                            <div
                                                class="col-12 d-flex justify-content-between align-items-center border-bottom pb-3 mb-1">
                                                <span
                                                    class="text-muted small fw-bold text-uppercase">{{ $charge->charge_name }}</span>
                                                <span
                                                    class="fw-bold text-dark fs-6">₹{{ number_format($charge->charge_amount, 2) }}</span>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="col-12 text-muted small fst-italic py-2 border-bottom pb-3 mb-1">No post-GST
                                            charges applied.</div>
                                    @endif
                                </div>

                                <div class="row g-4 mt-1">
                                    <!-- Attachments -->
                                    <div class="col-md-6 border-end" style="border-color: #f0f0f0 !important;">
                                        <div class="text-muted text-uppercase small fw-bold mb-2">Invoice Attachment</div>
                                        @if($invoice->attachments != '')
                                            @php
                                                $attachment = $invoice->attachments;
                                                $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                                                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                                $url = url('uploads/purchase_invoices/' . $attachment);
                                            @endphp
                                            <div class="mt-2 text-start">
                                                <div class="attachment-thumb border rounded p-1 bg-white shadow-sm position-relative"
                                                    style="width: 100px; height: 100px;" title="{{ $attachment }}">
                                                    @if($isImage)
                                                        <img src="{{ $url }}"
                                                            class="w-100 h-100 object-fit-cover rounded cursor-pointer view-image"
                                                            data-image="{{ $url }}" alt="Attachment">
                                                    @else
                                                        <a href="{{ $url }}" target="_blank"
                                                            class="w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-light rounded text-decoration-none shadow-none text-primary">
                                                            <i class="ri ri-file-text-line fs-2"></i>
                                                            <span class="badge bg-primary text-white mt-1"
                                                                style="font-size: 10px;">{{ strtoupper($extension) }}</span>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted small fst-italic">No attachment</span>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-muted text-uppercase small fw-bold mb-2">Authorized Signature</div>
                                        @if($invoice->auth_signature != '')
                                            @php
                                                $attachment = $invoice->auth_signature;
                                                $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                                                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                                $url = url('uploads/purchase_invoices/' . $attachment);
                                            @endphp
                                            <div class="mt-2 text-start">
                                                <div class="attachment-thumb border rounded p-1 bg-white shadow-sm position-relative"
                                                    style="width: 100px; height: 100px;" title="{{ $attachment }}">
                                                    @if($isImage)
                                                        <img src="{{ $url }}"
                                                            class="w-100 h-100 object-fit-cover rounded cursor-pointer view-image"
                                                            data-image="{{ $url }}" alt="Signature">
                                                    @else
                                                        <a href="{{ $url }}" target="_blank"
                                                            class="w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-light rounded text-decoration-none shadow-none text-primary">
                                                            <i class="ri ri-file-text-line fs-2"></i>
                                                            <span class="badge bg-primary text-white mt-1"
                                                                style="font-size: 10px;">{{ strtoupper($extension) }}</span>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-muted small fst-italic">No signature</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Final Summary -->
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm sticky-top" style="border-radius: 12px;">
                            <div class="card-header bg-light py-3">
                                <h5 class="fw-bold text-dark mb-4">Billing Summary</h5>
                            </div>
                            <div class="card-body p-4">

                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted small fw-bold">Total Quantity</span>
                                    <span
                                        class="fw-bold text-dark">{{ number_format($invoice->items->sum('quantity'), 2) }}</span>
                                </div>

                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted small fw-bold">Items Total</span>
                                    <span class="fw-bold text-dark">₹{{ number_format($invoice->sub_total, 2) }}</span>
                                </div>

                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted small fw-bold">Discount
                                        ({{ number_format($invoice->discount_percent, 2) }}%)</span>
                                    <span
                                        class="fw-bold text-dark">-₹{{ number_format($invoice->discount_amount, 2) }}</span>
                                </div>

                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted small fw-bold">Pre-GST Charges</span>
                                    <span
                                        class="fw-bold text-dark">+₹{{ number_format($preGstCharges->sum('charge_amount'), 2) }}</span>
                                </div>

                                @if($invoice->commission_amount > 0)
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="text-muted small fw-bold">Commission ({{ number_format($invoice->commission, 2) }}%)</span>
                                        <span class="fw-bold text-dark">₹{{ number_format($invoice->commission_amount, 2) }}</span>
                                    </div>
                                @endif

                                <div class="d-flex justify-content-between mb-3 pt-3 border-top">
                                    <span class="text-dark fw-bold small">Taxable Amount</span>
                                    <span class="fw-bold text-dark">₹{{ number_format($invoice->taxable_amount, 2) }}</span>
                                </div>

                                @if($invoice->other_state)
                                    <div class="d-flex justify-content-between mb-3 text-muted small">
                                        <span class="fw-bold">IGST ({{ number_format($invoice->igst_percent, 2) }}%)</span>
                                        <span class="fw-bold text-dark">₹{{ number_format($invoice->igst_amount, 2) }}</span>
                                    </div>
                                @else
                                    <div class="d-flex justify-content-between mb-2 text-muted small">
                                        <span class="fw-bold">CGST ({{ number_format($invoice->cgst_percent, 2) }}%)</span>
                                        <span class="fw-bold text-dark">₹{{ number_format($invoice->cgst_amount, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3 text-muted small">
                                        <span class="fw-bold">SGST ({{ number_format($invoice->sgst_percent, 2) }}%)</span>
                                        <span class="fw-bold text-dark">₹{{ number_format($invoice->sgst_amount, 2) }}</span>
                                    </div>
                                @endif

                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted small fw-bold">Post-GST Charges</span>
                                    <span
                                        class="fw-bold text-dark">+₹{{ number_format($postGstCharges->sum('charge_amount'), 2) }}</span>
                                </div>

                                @if($invoice->round_off > 0)
                                    <div class="d-flex justify-content-between mb-3 text-muted italic small">
                                        <span class="fw-bold">Round Off ({{ $invoice->round_off_type }})</span>
                                        <span
                                            class="fw-bold text-dark">{{ $invoice->round_off_type == 'Less' ? '-' : '+' }}₹{{ number_format($invoice->round_off, 2) }}</span>
                                    </div>
                                @endif

                                <div class="bg-primary-soft p-3 rounded-3 mt-4 border-start border-primary border-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-primary small uppercase">Grand Total</span>
                                        <span
                                            class="fs-5 fw-bold text-primary">₹{{ number_format($invoice->grand_total, 2) }}</span>
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
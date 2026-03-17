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
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ url('purchase_invoices') }}">Purchase Invoices</a></li>
                            <li class="breadcrumb-item active" aria-current="page">View #{{ $invoice->invoice_no }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ url('purchase_invoices') }}" class="btn btn-outline-secondary d-flex align-items-center">
                        <i class="ri ri-arrow-left-line me-1"></i> Back
                    </a>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-primary py-3" style="border-radius: 12px 12px 0 0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-white mb-0 fw-bold">General Information</h5>
                        <span class="badge bg-white text-primary px-3 py-2">INV: #{{ $invoice->invoice_no }}</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4 text-break">
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Supplier</div>
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
                                $statusClass = match($invoice->invoice_status) {
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
                    </div>
                </div>
            </div>

            <!-- Item Details Table -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-light py-3" style="border-radius: 12px 12px 0 0; border-bottom: 1px solid #f0f0f0;">
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
                                    <th class="py-3 text-muted text-uppercase small fw-bold text-end pe-4">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($invoice->items->count() > 0)
                                    @foreach($invoice->items as $index => $item)
                                    <tr>
                                        <td class="ps-4 fw-bold">{{ sprintf('%02d', $index + 1) }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $item->rawMaterial->name ?? 'N/A' }}</div>
                                            @if($item->rawMaterial && $item->rawMaterial->material_code)
                                                <small class="text-primary fw-medium">{{ $item->rawMaterial->material_code }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $item->hsn_code ?? '-' }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark px-3 py-2 fw-medium">
                                                {{ number_format($item->quantity, 0) }}
                                            </span>
                                        </td>
                                        <td class="text-end">₹{{ number_format($item->rate, 2) }}</td>
                                        <td class="text-end fw-bold text-dark pe-4">₹{{ number_format($item->quantity * $item->rate, 2) }}</td>
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
                        <div class="card-header bg-light py-3" style="border-radius: 12px 12px 0 0; border-bottom: 1px solid #f0f0f0;">
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small">Pre-GST Charges & Info</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                @php $preGstCharges = $invoice->charges->where('tax_type', 'Pre-GST'); @endphp
                                @if($preGstCharges->count() > 0)
                                    @foreach($preGstCharges as $charge)
                                        <div class="col-md-6 d-flex justify-content-between align-items-center border-bottom pb-2">
                                            <span class="text-muted small fw-bold text-uppercase">{{ $charge->charge_name }}</span>
                                            <span class="fw-bold text-dark">₹{{ number_format($charge->charge_amount, 2) }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12 text-muted small italic">No pre-GST charges applied.</div>
                                @endif
                                <div class="col-12 mt-4">
                                    <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded">
                                        <span class="fw-bold text-dark">Discount ({{ number_format($invoice->discount_percent, 2) }}%)</span>
                                        <span class="text-danger fw-bold">-₹{{ number_format($invoice->discount_amount, 2) }}</span>
                                    </div>
                                </div>
                                @if($invoice->notes)
                                    <div class="col-12 mt-2">
                                        <div class="text-muted text-uppercase small fw-bold mb-1">Additional Notes</div>
                                        <p class="small text-dark border p-3 rounded bg-white shadow-sm mb-0" style="white-space: pre-line;">{{ $invoice->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Post-GST Charges -->
                    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-header bg-light py-3" style="border-radius: 12px 12px 0 0; border-bottom: 1px solid #f0f0f0;">
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small">Post-GST Charges & Attachments</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                @php $postGstCharges = $invoice->charges->where('tax_type', 'Post-GST'); @endphp
                                @if($postGstCharges->count() > 0)
                                    @foreach($postGstCharges as $charge)
                                        <div class="col-md-6 d-flex justify-content-between align-items-center border-bottom pb-2">
                                            <span class="text-muted small fw-bold text-uppercase">{{ $charge->charge_name }}</span>
                                            <span class="fw-bold text-dark">₹{{ number_format($charge->charge_amount, 2) }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12 text-muted small italic">No post-GST charges applied.</div>
                                @endif

                                <!-- Attachments -->
                                <div class="col-md-6 mt-4">
                                    <div class="text-muted text-uppercase small fw-bold mb-1">Invoice Attachment</div>
                                    @if($invoice->attachments != '')
                                        @php
                                            $attachment = $invoice->attachments;
                                            $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                            $url = url('uploads/purchase_invoices/' . $attachment);
                                        @endphp
                                        @if($isImage)
                                            <button class="btn btn-sm btn-outline-primary view-image" data-image="{{ $url }}"><i class="ri ri-image-line"></i> View Image</button>
                                        @else
                                            <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ri ri-file-text-line"></i> View File</a>
                                        @endif
                                    @else
                                        <span class="text-muted small">No attachment</span>
                                    @endif
                                </div>
                                <div class="col-md-6 mt-4">
                                    <div class="text-muted text-uppercase small fw-bold mb-1">Authorized Signature</div>
                                    @if($invoice->auth_signature != '')
                                        @php
                                            $attachment = $invoice->auth_signature;
                                            $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                            $url = url('uploads/purchase_invoices/' . $attachment);
                                        @endphp
                                        @if($isImage)
                                            <button class="btn btn-sm btn-outline-primary view-image" data-image="{{ $url }}"><i class="ri ri-image-line"></i> View Image</button>
                                        @else
                                            <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ri ri-file-text-line"></i> View File</a>
                                        @endif
                                    @else
                                        <span class="text-muted small">No signature</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Final Summary -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm sticky-top" style="border-radius: 12px; top: 1rem;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom">Billing Summary</h5>
                            
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted fw-medium">Items Total</span>
                                <span class="fw-bold">₹{{ number_format($invoice->sub_total, 2) }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-3 text-danger">
                                <span class="fw-medium">Discount ({{ number_format($invoice->discount_percent, 2) }}%)</span>
                                <span class="fw-bold">-₹{{ number_format($invoice->discount_amount, 2) }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-3 text-primary">
                                <span class="fw-medium">Pre-GST Charges</span>
                                <span class="fw-bold">+₹{{ number_format($preGstCharges->sum('charge_amount'), 2) }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-3 pt-3 border-top">
                                <span class="text-dark fw-bold">Taxable Amount</span>
                                <span class="fw-bold text-dark">₹{{ number_format($invoice->taxable_amount, 2) }}</span>
                            </div>

                            @if($invoice->other_state)
                                <div class="d-flex justify-content-between mb-3 text-muted small">
                                    <span>IGST ({{ number_format($invoice->igst_percent, 2) }}%)</span>
                                    <span>₹{{ number_format($invoice->igst_amount, 2) }}</span>
                                </div>
                            @else
                                <div class="d-flex justify-content-between mb-3 text-muted small">
                                    <span>CGST ({{ number_format($invoice->cgst_percent, 2) }}%)</span>
                                    <span>₹{{ number_format($invoice->cgst_amount, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 text-muted small">
                                    <span>SGST ({{ number_format($invoice->sgst_percent, 2) }}%)</span>
                                    <span>₹{{ number_format($invoice->sgst_amount, 2) }}</span>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between mb-3 text-info">
                                <span class="fw-medium">Post-GST Charges</span>
                                <span class="fw-bold">+₹{{ number_format($postGstCharges->sum('charge_amount'), 2) }}</span>
                            </div>

                            @if($invoice->round_off > 0)
                            <div class="d-flex justify-content-between mb-3 text-muted italic small">
                                <span>Round Off ({{ $invoice->round_off_type }})</span>
                                <span>{{ $invoice->round_off_type == 'Less' ? '-' : '+' }}₹{{ number_format($invoice->round_off, 2) }}</span>
                            </div>
                            @endif

                            <div class="d-flex justify-content-between align-items-center mt-4 p-4 bg-primary bg-opacity-10 rounded-3">
                                <h4 class="mb-0 fw-bold text-primary">Grand Total</h4>
                                <h3 class="mb-0 fw-bold text-primary">₹{{ number_format($invoice->grand_total, 2) }}</h3>
                            </div>

                            <div class="mt-4 pt-3 border-top">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Received Amount</span>
                                    <span class="fw-bold text-success">₹{{ number_format($invoice->receive_amount, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">Due Amount</span>
                                    <span class="fw-bold text-danger">₹{{ number_format($invoice->due_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">Document Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <img id="modalImage" src="" class="img-fluid rounded shadow-sm" alt="Preview">
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $(document).on('click', '.view-image', function() {
            let imageUrl = $(this).data('image');
            $('#modalImage').attr('src', imageUrl);
            $('#imageModal').modal('show');
        });
    });
</script>
@endsection
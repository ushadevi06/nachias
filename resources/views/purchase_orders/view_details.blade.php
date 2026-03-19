@extends('layouts.common')
@section('title', 'View Purchase Order - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-primary mb-1">Purchase Order Details</h3>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ url('purchase_orders/download-pdf/'.$purchaseOrder->id) }}" target="_blank" class="btn btn-primary d-flex align-items-center shadow-sm">
                        <i class="ri ri-download-line me-1"></i> Download
                    </a>
                    <a href="{{ url('purchase_orders/print/'.$purchaseOrder->id) }}" target="_blank" class="btn btn-primary d-flex align-items-center shadow-sm">
                        <i class="ri ri-printer-line me-1"></i> Print
                    </a>
                    <a href="{{ url('purchase_orders') }}" class="btn btn-outline-secondary d-flex align-items-center shadow-sm">
                        <i class="ri ri-arrow-left-line me-1"></i> Back
                    </a>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header py-3 bg-light" style="border-radius: 12px 12px 0 0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark text-uppercase small">General Information</h5>
                        <span class="badge bg-white text-primary px-3 py-2">PO: #{{ $purchaseOrder->po_number }}</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4 text-break">
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Supplier</div>
                            <div class="fw-bold text-dark">
                                {{ $purchaseOrder->supplier->name ?? 'N/A' }}
                                @if($purchaseOrder->supplier && $purchaseOrder->supplier->code)
                                    <span class="text-primary small">({{ $purchaseOrder->supplier->code }})</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">PO Date</div>
                            <div class="fw-bold text-dark">{{ $purchaseOrder->po_date->format('d M, Y') }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Due Date</div>
                            <div class="fw-bold text-dark">{{ $purchaseOrder->due_date->format('d M, Y') }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Status</div>
                            <div>
                                @php
                                $statusColors = [
                                    'Draft' => 'bg-secondary', 'Approved' => 'bg-success',
                                    'Dispatched' => 'bg-info', 'Received' => 'bg-primary',
                                ];
                                @endphp
                                <span class="badge {{ $statusColors[$purchaseOrder->status] ?? 'bg-secondary' }} px-3 py-2 text-uppercase small">{{ $purchaseOrder->status }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Agent</div>
                            <div class="fw-bold text-dark">{{ $purchaseOrder->purchaseCommissionAgent->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Store / Unit</div>
                            <div class="fw-bold text-dark">{{ $purchaseOrder->storeType->store_type_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Reference No</div>
                            <div class="fw-bold text-dark text-break">{{ $purchaseOrder->reference_no ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Payment Terms</div>
                            <div class="fw-bold text-dark text-break">{{ $purchaseOrder->payment_terms ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Item Details Card -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-light py-3" style="border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0 fw-bold text-dark text-uppercase small">Order Item Details</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 text-uppercase small fw-bold">Sr.#</th>
                                    <th class="text-uppercase small fw-bold" style="min-width: 150px;">Material</th>
                                    <th class="text-uppercase small fw-bold">Brand</th>
                                    <th class="text-uppercase small fw-bold">Color</th>
                                    <th class="text-uppercase small fw-bold">Style</th>
                                    <th class="text-uppercase small fw-bold">Width</th>
                                    <th class="text-center text-uppercase small fw-bold">UOM</th>
                                    <th class="text-center text-uppercase small fw-bold">Qty</th>
                                    <th class="text-end text-uppercase small fw-bold">Rate</th>
                                    <th class="text-end pe-4 text-uppercase small fw-bold">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseOrder->items as $index => $item)
                                <tr>
                                    <td class="ps-4 fw-bold text-muted small">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">
                                            {{ $item->rawMaterial->name ?? '-' }}
                                            @if($item->rawMaterial && $item->rawMaterial->code)
                                                <span class="text-primary small">({{ $item->rawMaterial->code }})</span>
                                            @endif
                                        </div>
                                        <div class="small text-muted">
                                            {{ $item->storeCategory->category_name ?? '-' }}
                                            @if($item->storeCategory && $item->storeCategory->code)
                                                <span class="text-primary small">({{ $item->storeCategory->code }})</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="small fw-bold text-dark">
                                        {{ $item->brand->brand_name ?? '-' }}
                                        @if($item->brand && $item->brand->code)
                                            <span class="text-primary small">({{ $item->brand->code }})</span>
                                        @endif
                                    </td>
                                    <td class="small text-dark">{{ $item->color->color_name ?? '-' }}</td>
                                    <td class="small text-dark">{{ $item->style->style_name ?? '-' }}</td>
                                    <td class="small text-dark">{{ $item->fabricWidth->size ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark">{{ $item->uom->uom_code ?? '-' }}</span>
                                    </td>
                                    <td class="text-center fw-bold">{{ number_format($item->quantity, 2) }}</td>
                                    <td class="text-end fw-semibold text-dark">{{ number_format($item->rate, 2) }}</td>
                                    <td class="text-end pe-4 fw-bold text-primary">₹{{ number_format($item->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    @if($purchaseOrder->remarks)
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-header bg-light py-3">
                            <h5 class="mb-0 fw-bold text-dark text-uppercase small">Remarks / Terms</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="text-dark small" style="white-space: pre-line;">{{ $purchaseOrder->remarks }}</div>
                        </div>
                    </div>
                    @endif

                    @if($purchaseOrder->additional_attachments)
                    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-header bg-light py-3">
                            <h5 class="mb-0 fw-bold text-dark text-uppercase small">Attachments</h5>
                        </div>
                        <div class="card-body p-4">
                            @php
                                $attachment = $purchaseOrder->additional_attachments;
                                $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                $url = url('uploads/purchase_orders/' . $attachment);
                            @endphp
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-light p-3 rounded-3">
                                    <i class="ri ri-file-{{ $isImage ? 'image' : 'text' }}-line fs-3 text-primary"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark small text-break">{{ $attachment }}</div>
                                    <div class="mt-2 text-break">
                                        @if($isImage)
                                            <a href="javascript:void(0)" class="view-image btn btn-soft-primary btn-sm px-3" data-image="{{ $url }}">
                                                <i class="ri ri-eye-line me-1"></i> View
                                            </a>
                                        @else
                                            <a href="{{ $url }}" target="_blank" class="btn btn-soft-secondary btn-sm px-3">
                                                <i class="ri ri-download-line me-1"></i> Download
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-header bg-light py-3">
                            <h5 class="mb-0 fw-bold text-dark text-uppercase small">Billing Summary</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Total Quantity</span>
                                <span class="fw-bold text-dark">{{ number_format($purchaseOrder->items->sum('quantity'), 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Sub Total</span>
                                <span class="fw-bold text-dark">₹{{ number_format($purchaseOrder->sub_total, 2) }}</span>
                            </div>
                            @if($purchaseOrder->discount_amount > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Discount ({{ number_format($purchaseOrder->discount_percent, 2) }}%)</span>
                                <span class="fw-bold text-danger">- ₹{{ number_format($purchaseOrder->discount_amount, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Taxable Amount</span>
                                <span class="fw-bold text-dark">₹{{ number_format($purchaseOrder->taxable_amount, 2) }}</span>
                            </div>
                            @endif
                            
                            @if($purchaseOrder->other_state)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">IGST ({{ number_format($purchaseOrder->igst_percent, 2) }}%)</span>
                                <span class="fw-bold text-dark">₹{{ number_format($purchaseOrder->tax_amount, 2) }}</span>
                            </div>
                            @else
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">CGST ({{ number_format($purchaseOrder->cgst_percent, 2) }}%)</span>
                                <span class="fw-bold text-dark">₹{{ number_format($purchaseOrder->tax_amount/2, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">SGST ({{ number_format($purchaseOrder->sgst_percent, 2) }}%)</span>
                                <span class="fw-bold text-dark">₹{{ number_format($purchaseOrder->tax_amount/2, 2) }}</span>
                            </div>
                            @endif

                            @if($purchaseOrder->round_off != 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Round Off ({{ $purchaseOrder->round_off_type }})</span>
                                <span class="fw-bold text-dark">
                                    {{ $purchaseOrder->round_off_type == 'Less' ? '-' : '+' }}₹{{ number_format($purchaseOrder->round_off, 2) }}
                                </span>
                            </div>
                            @endif

                            <div class="p-3 rounded-3 mt-4 border-start border-primary border-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-primary small uppercase">Grand Total</span>
                                    <span class="fs-5 fw-bold text-primary">₹{{ number_format($purchaseOrder->total_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-soft { background-color: #f0f7ff; }
    .btn-soft-primary { background-color: #f0f7ff; color: #007bff; border: none; }
    .btn-soft-primary:hover { background-color: #007bff; color: white; }
    .btn-soft-secondary { background-color: #f8f9fa; color: #6c757d; border: none; }
    .btn-soft-secondary:hover { background-color: #6c757d; color: white; }
    .text-break { word-wrap: break-word !important; word-break: break-word !important; }
</style>

<!-- Image Preview Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Attachment Preview</h6>
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
$(document).ready(function () {
    $(document).on('click', '.view-image', function () {
        let imageSrc = $(this).data('image');
        $('#modalImage').attr('src', imageSrc);
        $('#imageModal').modal('show');
    });
});
</script>
@endsection
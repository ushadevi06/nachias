@extends('layouts.common')
@section('title', 'View Purchase Order - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <!-- Header Section -->
    <div class="d-flex align-items-center justify-content-between mb-4 mt-n2">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('purchase_orders') }}" class="text-muted">Purchase Orders</a></li>
                    <li class="breadcrumb-item active">PO Details</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center">
                <h4 class="fw-bold mb-0 me-3 text-primary">{{ $purchaseOrder->po_number }}</h4>
                @php
                    $statusClass = [
                        'Draft' => 'bg-soft-secondary text-secondary border-secondary',
                        'Approved' => 'bg-soft-success text-success border-success',
                        'Dispatched' => 'bg-soft-info text-info border-info',
                        'Received' => 'bg-soft-primary text-primary border-primary'
                    ];
                    $badgeStyle = $statusClass[$purchaseOrder->status] ?? 'bg-soft-secondary text-secondary border-secondary';
                @endphp
                <span class="badge {{ $badgeStyle }} border px-3 py-2">{{ $purchaseOrder->status }}</span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary btn-sm px-3" onclick="window.print()">
                <i class="ri ri-printer-line me-1"></i> Print
            </button>
            <a href="{{ url('purchase_orders') }}" class="btn btn-primary btn-sm px-4">
                <i class="ri ri-arrow-left-line me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Summary KPI Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 border-start border-primary border-4 h-100">
                <div class="d-flex align-items-center">
                    <div class="kpi-icon-sm bg-light-primary me-3 text-primary">
                        <i class="ri ri-calendar-event-line"></i>
                    </div>
                    <div>
                        <p class="text-muted small fw-bold mb-0">PO Date</p>
                        <h6 class="mb-0 fw-bold">{{ $purchaseOrder->po_date->format('d-M-Y') }}</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 border-start border-info border-4 h-100">
                <div class="d-flex align-items-center">
                    <div class="kpi-icon-sm bg-light-info me-3 text-info">
                        <i class="ri ri-user-star-line"></i>
                    </div>
                    <div>
                        <p class="text-muted small fw-bold mb-0">Supplier</p>
                        <h6 class="mb-0 fw-bold">{{ Str::limit($purchaseOrder->supplier->name, 20) }}</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 border-start border-warning border-4 h-100">
                <div class="d-flex align-items-center">
                    <div class="kpi-icon-sm bg-light-warning me-3 text-warning">
                        <i class="ri ri-time-line"></i>
                    </div>
                    <div>
                        <p class="text-muted small fw-bold mb-0">Due Date</p>
                        <h6 class="mb-0 fw-bold">{{ $purchaseOrder->due_date->format('d-M-Y') }}</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 border-start border-success border-4 h-100">
                <div class="d-flex align-items-center">
                    <div class="kpi-icon-sm bg-light-success me-3 text-success">
                        <i class="ri ri-money-rupee-circle-line"></i>
                    </div>
                    <div>
                        <p class="text-muted small fw-bold mb-0">Total Amount</p>
                        <h6 class="mb-0 fw-bold text-success">₹{{ number_format($purchaseOrder->total_amount, 2) }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- 1. General Information Card -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                    <h6 class="mb-0 fw-bold text-primary"><i class="ri ri-information-line me-2"></i>General Information</h6>
                </div>
                <div class="card-body p-5">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold d-block mb-1">Commission Agent</label>
                            <span class="text-dark fw-semibold">
                                @if($purchaseOrder->purchaseCommissionAgent)
                                    {{ $purchaseOrder->purchaseCommissionAgent->name }} <small class="text-muted">({{ $purchaseOrder->purchaseCommissionAgent->code }})</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </span>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small fw-bold d-block mb-1">Store / Unit</label>
                            <span class="text-dark fw-semibold">{{ $purchaseOrder->storeType->store_type_name ?? '-' }}</span>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small fw-bold d-block mb-1">Reference No</label>
                            <span class="text-dark fw-semibold">{{ $purchaseOrder->reference_no ?? '-' }}</span>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small fw-bold d-block mb-1">Ref / Order Date</label>
                            <span class="text-dark fw-semibold">{{ $purchaseOrder->reference_date ? $purchaseOrder->reference_date->format('d-M-Y') : '-' }}</span>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small fw-bold d-block mb-1">Payment Terms</label>
                            <span class="text-dark fw-semibold">{{ $purchaseOrder->payment_terms ?? '-' }}</span>
                        </div>
                        @if($purchaseOrder->additional_attachments)
                        <div class="col-md-12">
                            <hr class="my-2 border-dashed">
                            <label class="text-muted small fw-bold d-block mb-1">Additional Attachments</label>
                            @php
                                $attachment = $purchaseOrder->additional_attachments;
                                $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                $url = url('uploads/purchase_orders/' . $attachment);
                            @endphp
                            @if($isImage)
                                <a href="javascript:void(0)" class="view-image btn btn-soft-primary btn-xs py-1 px-2" data-image="{{ $url }}">
                                    <i class="ri ri-image-line me-1"></i> View
                                </a>
                            @else
                                <a href="{{ $url }}" target="_blank" class="btn btn-soft-secondary btn-xs py-1 px-2">
                                    <i class="ri ri-file-text-line me-1"></i> View File
                                </a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Financial Summary Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 bg-primary-soft">
                <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center">
                    <h6 class="mb-0 fw-bold text-primary"><i class="ri ri-bank-card-line me-2"></i>Financial Summary</h6>
                </div>
                <div class="card-body p-5">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted fw-bold">Sub Total</span>
                        <span class="fw-bold fs-6 text-dark text-end">₹{{ number_format($purchaseOrder->sub_total, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted fw-bold">Discount ({{ number_format($purchaseOrder->discount_percent, 2) }}%)</span>
                        <span class="text-danger fw-bold">- ₹{{ number_format($purchaseOrder->discount_amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="small text-muted fw-bold">Taxable Amount</span>
                        <span class="fw-bold text-dark">₹{{ number_format($purchaseOrder->taxable_amount, 2) }}</span>
                    </div>
                    <hr class="my-2 border-dashed">
                    @if($purchaseOrder->other_state)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted fw-bold">IGST ({{ number_format($purchaseOrder->igst_percent, 2) }}%)</span>
                        <span class="text-dark">₹{{ number_format(($purchaseOrder->taxable_amount * $purchaseOrder->igst_percent) / 100, 2) }}</span>
                    </div>
                    @else
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted fw-bold">CGST ({{ number_format($purchaseOrder->cgst_percent, 2) }}%)</span>
                        <span class="text-dark">₹{{ number_format(($purchaseOrder->taxable_amount * $purchaseOrder->cgst_percent) / 100, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted fw-bold">SGST ({{ number_format($purchaseOrder->sgst_percent, 2) }}%)</span>
                        <span class="text-dark">₹{{ number_format(($purchaseOrder->taxable_amount * $purchaseOrder->sgst_percent) / 100, 2) }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted fw-bold">Round Off</span>
                        <span class="text-dark">@if($purchaseOrder->round_off_type == 'Less') - @endif ₹{{ number_format($purchaseOrder->round_off, 2) }}</span>
                    </div>
                    <div class="mt-3 p-2 bg-white rounded border-start border-primary border-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small fw-bold text-primary">Grand Total</span>
                            <span class="h5 mb-0 fw-bold text-primary">₹{{ number_format($purchaseOrder->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Item Details Table -->
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold text-primary"><i class="ri ri-checkbox-line me-2"></i>Order Item Details</h6>
                    <span class="badge bg-light-primary text-primary px-3 fw-bold">{{ $purchaseOrder->items->count() }} Items</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-premium align-middle mb-0">
                            <thead class="bg-light text-muted uppercase small">
                                <tr>
                                    <th class="ps-4">Item #</th>
                                    <th>Material / Style</th>
                                    <th>Brand / Category</th>
                                    <th class="text-center">UOM</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Rate</th>
                                    <th class="text-end pe-4">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseOrder->items as $index => $item)
                                <tr>
                                    <td class="ps-4 text-muted small fw-bold">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item->rawMaterial->name ?? '-' }}</div>
                                        <div class="small text-muted mt-1">
                                            <span class="badge bg-light-secondary me-1 py-1 px-2">{{ $item->style->style_name ?? '-' }}</span>
                                            @if($item->fabricWidth) {{ $item->fabricWidth->size }}@endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small fw-bold">{{ $item->brand->brand_name ?? '-' }}</div>
                                        <div class="x-small text-muted">{{ $item->storeCategory->category_name ?? '-' }}</div>
                                    </td>
                                    <td class="text-center"><span class="badge bg-soft-info text-info border border-info px-2">{{ $item->uom->uom_code ?? '-' }}</span></td>
                                    <td class="text-center fw-bold">{{ number_format($item->quantity, 2) }}</td>
                                    <td class="text-end fw-semibold">₹{{ number_format($item->rate, 2) }}</td>
                                    <td class="text-end pe-4 fw-bold text-dark">₹{{ number_format($item->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .kpi-icon-sm {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 1.2rem;
    }
    .bg-light-primary { background-color: #f1f3ff; }
    .bg-light-info { background-color: #ecfeff; }
    .bg-light-warning { background-color: #fffbeb; }
    .bg-light-success { background-color: #f0fdf4; }
    .bg-primary-soft { background-color: #f8fafc; }
    .bg-soft-success { background-color: #f0fdf4 !important; }
    .bg-soft-info { background-color: #ecfeff !important; }
    .bg-soft-primary { background-color: #f1f3ff !important; }
    .bg-soft-secondary { background-color: #f8fafc !important; }
    .border-dashed { border-style: dashed !important; }

    .table-premium thead th {
        font-weight: 700;
        letter-spacing: 0.05em;
        border-bottom: none;
        padding-top: 15px;
        padding-bottom: 15px;
    }
    .table-premium tbody tr {
        transition: background-color 0.2s;
    }
    .table-premium tbody tr:hover {
        background-color: #f8fafc;
    }
    .table-premium td {
        padding-top: 15px;
        padding-bottom: 15px;
        border-color: #f1f5f9;
    }
    .btn-soft-primary {
        background-color: #f1f3ff;
        color: #6a1b9a;
        border: none;
    }
    .btn-soft-primary:hover {
        background-color: #6a1b9a;
        color: #fff;
    }
    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
</style>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="Preview">
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    $(document).on('click', '.view-image', function () {
        let imagePath = $(this).data('image');

        let imageSrc = imagePath.startsWith('http') || imagePath.startsWith('data:')
            ? imagePath
            : APP_URL + imagePath;

        $('#modalImage').attr('src', imageSrc);
        $('#imageModal').modal('show');
    });
});

</script>
@endsection
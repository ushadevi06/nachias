@extends('layouts.common')
@section('title', 'View GRN Entry #' . $grn->grn_number . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-primary mb-1">GRN Entry Details</h3>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ url('grn_entries/download-pdf/' . $grn->id) }}" class="btn btn-primary d-flex align-items-center" target="_blank">
                        <i class="ri ri-download-line me-1"></i> Download
                    </a>
                    <a href="{{ url('grn_entries/print/' . $grn->id) }}" target="_blank" class="btn btn-primary d-flex align-items-center">
                        <i class="ri ri-printer-line me-1"></i> Print
                    </a>
                    <a href="{{ url('grn_entries') }}" class="btn btn-outline-secondary d-flex align-items-center">
                        <i class="ri ri-arrow-left-line me-1"></i> Back
                    </a>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-light py-3" style="border-radius: 12px 12px 0 0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">General Information</h5>
                        <span class="badge bg-white text-primary px-3 py-2">#{{ $grn->grn_number }}</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4 text-break">
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Supplier</div>
                            <div class="fw-bold text-dark">
                                {{ $grn->supplier->name ?? 'N/A' }}
                                @if($grn->supplier && $grn->supplier->code)
                                    <span class="text-primary small">({{ $grn->supplier->code }})</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">PO Invoice No</div>
                            <div class="fw-bold text-dark">{{ $grn->purchaseInvoice->invoice_no ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">GRN Date</div>
                            <div class="fw-bold text-dark">{{ $grn->grn_date->format('d M, Y') }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Status</div>
                            <div>
                                @php
                                    $statusBadgeClass = match($grn->status) {
                                        'Received' => 'bg-success',
                                        'Invoiced' => 'bg-info',
                                        'Closed' => 'bg-dark',
                                        'Cancelled' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $statusBadgeClass }} px-3 py-2">{{ $grn->status }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Supplier Invoice No</div>
                            <div class="fw-bold text-dark">{{ $grn->purchaseInvoice->po_reference ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Supplier Invoice Date</div>
                            <div class="fw-bold text-dark">{{ $grn->supplier_invoice_date->format('d M, Y') }}</div>
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
                                    <th class="ps-4 py-3 text-muted text-uppercase small fw-bold" width="60">S.No</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold">Raw Material</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold">Grn Image</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold text-center">Style</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold text-center">Color</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold text-center">Width</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold text-center">Supplier Design Name</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold text-center">Art No</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold text-center">Location</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold text-center">UOM</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold text-end">Quantity</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold text-end">Rate</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold text-end pe-4">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($grn->grnEntryItems->isEmpty())
                                    <tr>
                                        <td colspan="13" class="text-center py-5 text-muted">No items found</td>
                                    </tr>
                                @else
                                    @foreach($grn->grnEntryItems as $index => $item)
                                    <tr>
                                        <td class="ps-4 fw-bold">{{ sprintf('%02d', $index + 1) }}</td>
                                        @php
                                            $catId = $item->purchaseInvoiceItem?->rawMaterial?->store_category_id ?? 0;
                                            $purchaseOrderItem = $item->purchaseInvoiceItem?->purchaseOrderItem;
                                            $rawMaterial = $item->purchaseInvoiceItem?->rawMaterial;

                                            if ($catId == 1 && $purchaseOrderItem?->supplier_design_name) {
                                                $rawMaterialName = $purchaseOrderItem->supplier_design_name;
                                                $rawMaterialCode = '';
                                            } else {
                                                $rawMaterialName = $rawMaterial->name ?? '-';
                                                $rawMaterialCode = $rawMaterial->code ?? '';
                                            }

                                            $brandName = $purchaseOrderItem?->brand?->brand_name ?? '-';
                                            $styleName = $purchaseOrderItem?->style?->style_name ?? '-';

                                            if (!empty($item->color?->color_name)) {
                                                $colorName = $item->color->color_name;
                                            } elseif (($item->variants->count() ?? 0) === 1) {
                                                $colorName = $item->variants->first()->color->color_name ?? '-';
                                            } elseif (($item->variants->count() ?? 0) > 1) {
                                                $colorName = $item->variants
                                                    ->map(fn($variant) => $variant->color->color_name ?? null)
                                                    ->filter()
                                                    ->implode(', ');
                                                $colorName = $colorName !== '' ? $colorName : '-';
                                            } else {
                                                $colorName = $purchaseOrderItem?->color?->color_name ?? '-';
                                            }

                                            $widthVal = $purchaseOrderItem?->fabricWidth?->width ?? '-';
                                            $supplierDesignName = $purchaseOrderItem?->supplier_design_name ?? '-';
                                            $locationName = $item->storeLocation?->store_location ?? '-';
                                            $uomCode = $item->purchaseInvoiceItem?->uom?->uom_code ?? 'MTR';

                                            $qcBadgeClass = match($item->quality_check_status) {
                                                'Pass' => 'bg-success',
                                                'Fail' => 'bg-danger',
                                                'Hold' => 'bg-warning',
                                                default => 'bg-secondary'
                                            };
                                        @endphp

                                        <td>
                                            <div class="fw-bold text-dark">{{ $rawMaterialName }}</div>
                                            @if($rawMaterialCode !== '')
                                                <small class="text-primary fw-medium">({{ $rawMaterialCode }})</small>
                                            @endif
                                            <div class="small text-muted mt-1">[{{ $brandName }}]</div>
                                            <div class="small text-muted">{{ $item->fabricType->fabric_type ?? '' }}</div>
                                            <span class="badge {{ $qcBadgeClass }} x-small" style="font-size: 10px;">{{ $item->quality_check_status ?? 'N/A' }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($item->image)
                                                <img src="{{ url('uploads/grn_items/' . $item->image) }}" alt="GRN Item Image" class="img-fluid border rounded mt-1 border rounded cursor-pointer view-image" style="max-width: 70px; max-height: 70px;" data-image="{{ url('uploads/grn_items/'.$item->image) }}">
                                            @else
                                                <span class="text-muted small">No Image</span>
                                            @endif
                                        </td>
                                        <td class="text-center small fw-bold">{{ $styleName }}</td>
                                        <td class="text-center">
                                            <div class="small fw-bold">{{ $colorName }}</div>
                                            @if($item->variants->count() > 0)
                                                <button type="button" class="btn btn-link btn-sm p-0 text-info text-decoration-none mt-1" data-bs-toggle="modal" data-bs-target="#variantModal{{ $item->id }}" style="font-size: 11px;">
                                                    <i class="ri-list-check me-1"></i>View Variants
                                                </button>
                                            @endif
                                        </td>
                                        <td class="text-center small fw-bold">{{ $widthVal }}</td>
                                        <td class="text-center small fw-bold">{{ $supplierDesignName }}</td>
                                        <td class="text-center small fw-bold">{{ $item->art_no ?? '-' }}</td>
                                        <td class="text-center small">{{ $locationName }}</td>
                                        <td class="text-center small fw-bold">{{ $uomCode }}</td>
                                        <td class="text-end fw-bold text-dark pe-4">{{ number_format($item->qty_received, 2) }}</td>
                                        <td class="text-end small">₹{{ number_format($item->rate, 2) }}</td>
                                        <td class="text-end fw-bold text-dark pe-4">₹{{ number_format($item->amount, 2) }}</td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row g-4 justify-content-end">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-header bg-light py-3" style="border-radius: 12px 12px 0 0; border-bottom: 1px solid #f0f0f0;">
                            <h5 class="mb-0 fw-bold text-dark">Summary</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted fw-medium">Total Items</span>
                                <span class="fw-bold">{{ $grn->grnEntryItems->count() }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted fw-medium">Ordered Quantity</span>
                                <span class="fw-bold">{{ number_format($grn->grnEntryItems->sum('qty_ordered'), 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted fw-medium">Received Quantity</span>
                                <span class="fw-bold">{{ number_format($grn->grnEntryItems->sum('qty_received'), 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 border-top pt-3">
                                <span class="text-dark fw-bold">Accepted Quantity</span>
                                <span class="fw-bold text-success">{{ number_format($grn->grnEntryItems->sum('qty_accepted'), 2) }}</span>
                            </div>
                            <div class="bg-primary-soft p-3 rounded-3 mt-4 border-start border-primary border-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-primary small uppercase">Total Amount</span>
                                    <span class="fs-5 fw-bold text-primary">&#8377;{{ number_format($grn->grnEntryItems->sum('amount'), 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Variants Modals -->
    @foreach($grn->grnEntryItems as $item)
        @if($item->variants->count() > 0)
            <div class="modal fade" id="variantModal{{ $item->id }}" tabindex="-1" aria-labelledby="variantModalLabel{{ $item->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title text-white" id="variantModalLabel{{ $item->id }}">
                                Variants for 
                                @php
                                    $vCatId = $item->purchaseInvoiceItem?->rawMaterial?->store_category_id ?? 0;
                                    $vDesignName = null;
                                    if ($vCatId == 1 && $item->purchaseInvoiceItem?->purchaseOrderItem?->supplier_design_name) {
                                        $vDesignName = $item->purchaseInvoiceItem->purchaseOrderItem->supplier_design_name;
                                    }
                                @endphp
                                @if($vDesignName)
                                    {{ $vDesignName }}
                                @elseif($item->purchaseInvoiceItem && $item->purchaseInvoiceItem->rawMaterial)
                                    {{ $item->purchaseInvoiceItem->rawMaterial->name }}
                                @else
                                    Item {{ $loop->iteration }}
                                @endif
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-center align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Color</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($item->variants as $variant)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $variant->color->color_name ?? 'N/A' }}</td>
                                                <td>{{ number_format($variant->qty_received, 2) }}</td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-light fw-bold">
                                            <td colspan="2" class="text-end">Total:</td>
                                            <td>{{ number_format($item->variants->sum('qty_received'), 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>
@endsection

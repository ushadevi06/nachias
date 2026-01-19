@extends('layouts.common')
@section('title', 'View GRN Entry #' . $grn->grn_number . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box d-flex justify-content-between align-items-center">
                <h4>View GRN Entry</h4>
                <a href="{{ url('grn_entries') }}" class="btn btn-primary">
                    <i class="ri ri-arrow-left-line back-arrow"></i> Back
                </a>
            </div>

            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="detail-title">GRN No:</label>
                            <div class="text-muted">{{ $grn->grn_number }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">GRN Date:</label>
                            <div class="text-muted">{{ $grn->grn_date->format('d-m-Y') }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">PO Invoice Number:</label>
                            <div class="text-muted">{{ $grn->purchaseInvoice->invoice_no ?? 'N/A' }}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Supplier:</label>
                            <div class="text-muted">
                                {{ $grn->supplier->name ?? 'N/A' }} 
                                @if($grn->supplier)
                                    <span class="mini-title">({{ $grn->supplier->code }})</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Supplier Invoice No:</label>
                            <div class="text-muted">{{ $grn->purchaseInvoice->po_reference ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Supplier Invoice Date:</label>
                            <div class="text-muted">{{ $grn->supplier_invoice_date->format('d-m-Y') }}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Status:</label>
                            <div>
                                @php
                                    $statusBadgeClass = 'bg-secondary';
                                    if ($grn->status == 'Received') $statusBadgeClass = 'bg-success';
                                    if ($grn->status == 'Invoiced') $statusBadgeClass = 'bg-info';
                                    if ($grn->status == 'Closed') $statusBadgeClass = 'bg-dark';
                                    if ($grn->status == 'Cancelled') $statusBadgeClass = 'bg-danger';
                                @endphp
                                <span class="badge {{ $statusBadgeClass }}">{{ $grn->status }}</span>
                            </div>
                        </div>

                        <!-- Item Details Table -->
                        <div class="col-lg-12 mt-4">
                            <h6 class="fw-bold">Item Details</h6>
                            <div class="table-responsive" style="overflow-x:auto; white-space:nowrap;">
                                <table class="table table-bordered align-middle text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Supplier Design Name (Code)</th>
                                            <th>Item Image</th>
                                            <th>Art No.</th>
                                            <th>UOM</th>
                                            <th>Fabric Type</th>
                                            <th>Quantity Ordered</th>
                                            <th>Quantity Received</th>
                                            <th>Quantity Accepted</th>
                                            <th>Quantity Rejected</th>
                                            <th>Quantity Balanced</th>
                                            <th>Rate</th>
                                            <th>Amount</th>
                                            <th>Quality Check Status</th>
                                            <th>Store Location</th>
                                            <th>Variants</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($grn->grnEntryItems as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    @if($item->purchaseInvoiceItem && $item->purchaseInvoiceItem->rawMaterial)
                                                        {{ $item->purchaseInvoiceItem->rawMaterial->name }}
                                                        <span class="mini-title">({{ $item->purchaseInvoiceItem->rawMaterial->code }})</span>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($item->image)
                                                        <a href="{{ url('uploads/grn_items/' . $item->image) }}" target="_blank">
                                                            <img src="{{ url('uploads/grn_items/' . $item->image) }}" width="50" class="border rounded">
                                                        </a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $item->art_no ?? '-' }}</td>
                                                <td>
                                                    @if($item->purchaseInvoiceItem && $item->purchaseInvoiceItem->uom)
                                                        {{ $item->purchaseInvoiceItem->uom->uom_code }}
                                                    @else
                                                        MTR
                                                    @endif
                                                </td>
                                                <td>{{ $item->fabricType->fabric_type ?? '-' }}</td>
                                                <td>{{ number_format($item->qty_ordered, 2) }}</td>
                                                <td>{{ number_format($item->qty_received, 2) }}</td>
                                                <td>{{ number_format($item->qty_accepted, 2) }}</td>
                                                <td>{{ number_format($item->qty_rejected, 2) }}</td>
                                                <td>{{ number_format($item->qty_balanced, 2) }}</td>
                                                <td>₹{{ number_format($item->rate, 2) }}</td>
                                                <td>₹{{ number_format($item->amount, 2) }}</td>
                                                <td>
                                                    @php
                                                        $qcBadgeClass = 'bg-secondary';
                                                        if ($item->quality_check_status == 'Pass') $qcBadgeClass = 'bg-success';
                                                        if ($item->quality_check_status == 'Fail') $qcBadgeClass = 'bg-danger';
                                                        if ($item->quality_check_status == 'Hold') $qcBadgeClass = 'bg-warning';
                                                    @endphp
                                                    <span class="badge {{ $qcBadgeClass }}">{{ $item->quality_check_status ?? 'N/A' }}</span>
                                                </td>
                                                <td>{{ $item->storeLocation->store_location ?? '-' }}</td>
                                                <td>
                                                    @if($item->variants->count() > 0)
                                                        <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#variantModal{{ $item->id }}">
                                                            View Variants
                                                        </button>
                                                    @else
                                                        <span class="text-muted">No Variants</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="16" class="text-center">No items found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
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
                                @if($item->purchaseInvoiceItem && $item->purchaseInvoiceItem->rawMaterial)
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
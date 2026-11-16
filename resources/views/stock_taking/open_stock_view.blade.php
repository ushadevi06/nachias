@extends('layouts.common')
@section('title', 'Open Stock Entry ' . $entryNo . ' - ' . env('WEBSITE_NAME'))

@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            @include('flash_messages')
        </div>
        <div class="col-lg-12">
            <!-- Header Section -->
            <div class="table-header-box d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h4 class="mb-1 fw-bold text-primary">Open Stock Entry Details</h4>
                    <p class="text-muted small mb-0 font-monospace">Entry No: <strong>{{ $entryNo }}</strong></p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ url('fg_stock_taking/open-stock-entry/edit/' . $firstItem->id) }}" class="btn btn-primary">
                        <i class="ri ri-edit-line me-1"></i> Edit Entry
                    </a>
                    <a href="{{ url('fg_stock_taking/open-stock-logs') }}" class="btn btn-secondary">
                        <i class="ri ri-arrow-left-line back-arrow"></i> Back to Logs
                    </a>
                </div>
            </div>

            <!-- General Information Card -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0 fw-bold">
                        <i class="ri ri-information-line me-1 text-primary"></i> General Information
                    </h5>
                    <span class="badge bg-primary fs-6">{{ $entryNo }}</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-3 col-sm-6">
                            <div class="text-muted small fw-bold text-uppercase">Stock Date</div>
                            <div class="fs-6 fw-bold text-dark mt-1">
                                {{ $firstItem->entry_date ? \Carbon\Carbon::parse($firstItem->entry_date)->format('d M, Y') : '-' }}
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="text-muted small fw-bold text-uppercase">Warehouse</div>
                            <div class="fs-6 fw-bold text-dark mt-1">
                                {{ $firstItem->warehouse?->warehouse_name ?? '-' }}
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="text-muted small fw-bold text-uppercase">Store Type</div>
                            <div class="fs-6 fw-bold text-dark mt-1">
                                {{ $firstItem->storeType?->store_type_name ?? '-' }}
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="text-muted small fw-bold text-uppercase">Brand(s)</div>
                            <div class="fs-6 fw-bold text-dark mt-1">
                                {{ $brandDisplay ?: '-' }}
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="text-muted small fw-bold text-uppercase">Store Location</div>
                            <div class="fs-6 fw-bold text-dark mt-1">
                                {{ $locationDisplay ?: '-' }}
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="text-muted small fw-bold text-uppercase">Total Items</div>
                            <div class="fs-6 fw-bold text-info mt-1">
                                {{ number_format($totalItems) }} items
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="text-muted small fw-bold text-uppercase">Total Quantity</div>
                            <div class="fs-6 fw-bold text-primary mt-1">
                                {{ number_format($totalQty) }} pcs
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <div class="text-muted small fw-bold text-uppercase">Total Value</div>
                            <div class="fs-5 fw-bold text-success mt-1">
                                ₹{{ number_format($totalAmount, 2) }}
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12">
                            <div class="text-muted small fw-bold text-uppercase">Logged By</div>
                            <div class="text-dark mt-1">
                                <strong>{{ $firstItem->creator?->name ?? 'Admin' }}</strong>
                                <span class="text-muted small">({{ $firstItem->created_at ? \Carbon\Carbon::parse($firstItem->created_at)->format('d M, Y h:i A') : '-' }})</span>
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12">
                            <div class="text-muted small fw-bold text-uppercase">Remarks</div>
                            <div class="text-dark mt-1">
                                {{ $firstItem->header_remarks ?: '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Items Table Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0 fw-bold">
                        <i class="ri ri-list-check me-1 text-primary"></i> Stock Items Breakdown
                    </h5>
                    <span class="badge bg-label-info">{{ number_format($totalItems) }} items / {{ number_format($totalQty) }} pcs</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small text-uppercase">
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>Product Name</th>
                                    <th>Art No</th>
                                    <th>Style</th>
                                    <th>Size</th>
                                    <th>Sleeve</th>
                                    <th class="text-end">Qty In</th>
                                    <th class="text-end">Price (₹)</th>
                                    <th class="text-end">Total Value (₹)</th>
                                    <th>Store Location</th>
                                    <th>SKU / Barcode</th>
                                    <th>Item Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $idx => $it)
                                    @php
                                        $rowTotal = round((float) $it->qty * ((float) ($it->price ?? 0)), 2);
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $idx + 1 }}</td>
                                        <td><strong>{{ $it->product_name }}</strong></td>
                                        <td>{{ $it->art_no }}</td>
                                        <td>{{ $it->style?->style_name ?? ($it->style?->name ?? '-') }}</td>
                                        <td><span class="badge bg-label-dark">{{ $it->size }}</span></td>
                                        <td>{{ $it->sleeve_type ?: '-' }}</td>
                                        <td class="text-end"><strong>{{ number_format($it->qty) }}</strong></td>
                                        <td class="text-end">₹{{ number_format((float) ($it->price ?? 0), 2) }}</td>
                                        <td class="text-end"><strong>₹{{ number_format($rowTotal, 2) }}</strong></td>
                                        <td>{{ $it->storeLocation?->store_location ?? ($it->storeLocation?->store_location_name ?? '-') }}</td>
                                        <td><code class="text-primary">{{ $it->sku }}</code></td>
                                        <td class="small text-muted">{{ $it->remarks ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td colspan="6" class="text-end text-uppercase">Total:</td>
                                    <td class="text-end text-primary fs-6">{{ number_format($totalQty) }}</td>
                                    <td class="text-end">-</td>
                                    <td class="text-end text-success fs-6">₹{{ number_format($totalAmount, 2) }}</td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light d-flex justify-content-between align-items-center py-3">
                    <a href="{{ url('fg_stock_taking/open-stock-logs') }}" class="btn btn-secondary">
                        <i class="ri ri-arrow-left-line back-arrow"></i> Back to Logs
                    </a>
                    <a href="{{ url('fg_stock_taking/open-stock-entry/edit/' . $firstItem->id) }}" class="btn btn-primary">
                        <i class="ri ri-edit-line me-1"></i> Edit Entry
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

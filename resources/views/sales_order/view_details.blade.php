@extends('layouts.common')
@section('title', 'View Sale Order - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-primary mb-1">Sale Order Details</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ url('sales_orders') }}">Sales Orders</a></li>
                            <li class="breadcrumb-item active" aria-current="page">View #{{ $salesOrder->so_no }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ url('sales_orders/download-pdf/'.$salesOrder->id) }}" target="_blank" class="btn btn-primary d-flex align-items-center shadow-sm">
                        <i class="ri ri-download-line me-1"></i> Download
                    </a>
                    <a href="{{ url('sales_orders/print/'.$salesOrder->id) }}" target="_blank" class="btn btn-primary d-flex align-items-center shadow-sm">
                        <i class="ri ri-printer-line me-1"></i> Print
                    </a>
                    <a href="{{ url('sales_orders') }}" class="btn btn-outline-secondary d-flex align-items-center shadow-sm">
                        <i class="ri ri-arrow-left-line me-1"></i> Back
                    </a>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header py-3 bg-light" style="border-radius: 12px 12px 0 0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark text-uppercase small">General Information</h5>
                        <span class="badge bg-white text-primary px-3 py-2">SO: #{{ $salesOrder->so_no }}</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4 text-break">
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Customer / Retailer</div>
                            <div class="fw-bold text-dark">
                                @if($salesOrder->customer)
                                    {{ $salesOrder->customer->name }}
                                    <span class="text-primary small">({{ $salesOrder->customer->code }})</span>
                                @elseif($salesOrder->retailer)
                                    {{ $salesOrder->retailer->name }}
                                    <span class="text-primary small">({{ $salesOrder->retailer->code }})</span>
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">SO Date</div>
                            <div class="fw-bold text-dark">{{ $salesOrder->so_date->format('d M, Y') }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Order Type</div>
                            <div class="fw-bold text-dark">{{ $salesOrder->order_type }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Status</div>
                            <div>
                                @php
                                $statusColors = [
                                    'Draft' => 'bg-secondary', 'Approved' => 'bg-success',
                                    'Pending' => 'bg-warning', 'In Production' => 'bg-info',
                                    'Dispatched' => 'bg-primary', 'Cancelled' => 'bg-danger',
                                ];
                                @endphp
                                <span class="badge {{ $statusColors[$salesOrder->status] ?? 'bg-secondary' }} px-3 py-2">{{ $salesOrder->status }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Season</div>
                            <div class="fw-bold text-dark">{{ $salesOrder->season->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Sales Agent</div>
                            <div class="fw-bold text-dark">
                                {{ $salesOrder->salesAgent->name ?? '-' }}
                                @if($salesOrder->salesAgent && $salesOrder->salesAgent->code)
                                    <span class="text-primary small">({{ $salesOrder->salesAgent->code }})</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Zone</div>
                            <div class="fw-bold text-dark">{{ $salesOrder->zone->zone_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Store</div>
                            <div class="fw-bold text-dark">{{ $salesOrder->store->store_type_name ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logistics & Shipping Section -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-light py-3" style="border-radius: 12px 12px 0 0; border-bottom: 1px solid #f0f0f0;">
                    <h5 class="mb-0 fw-bold text-dark text-uppercase small">Logistics & Shipping Details</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4 text-break">
                        <div class="col-md-4">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Billing Address</div>
                            <div class="text-dark small" style="white-space: pre-line;">{{ $salesOrder->billing_address ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Shipping Address</div>
                            <div class="text-dark small" style="white-space: pre-line;">{{ $salesOrder->shipping_address ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Payment Terms</div>
                            <div class="text-dark small">{{ $salesOrder->payment_terms ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Shipping Method</div>
                            <div class="fw-bold text-dark">{{ $salesOrder->shippingMethod->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Transport Mode</div>
                            <div class="fw-bold text-dark">{{ $salesOrder->transportMode->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Dispatch From</div>
                            <div class="fw-bold text-dark">{{ $salesOrder->dispatchFrom->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Transporter</div>
                            <div class="fw-bold text-dark">{{ $salesOrder->transporter_name ?? '-' }}</div>
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
                                    <th class="py-3 text-muted text-uppercase small fw-bold">Item Description</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold text-center">Color/Size</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold text-center">Qty/UOM</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold text-end">MRP</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold text-end pe-4">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($salesOrder->items->count() > 0)
                                    @foreach($salesOrder->items as $idx => $item)
                                    <tr>
                                        <td class="ps-4 fw-bold">{{ sprintf('%02d', $idx + 1) }}</td>
                                        <td>
                                            @php
                                                $brand = $item->item->brand->name ?? $item->brandCategory->name ?? '-';
                                                $style = $item->item->style->name ?? $item->item->name ?? '';
                                                $sleeve = is_array($item->sleeve) ? implode(', ', $item->sleeve) : $item->sleeve;
                                                
                                                if ($brand == '-' && $item->stockEntryItem) {
                                                    $brand = $item->stockEntryItem->finished_item_code;
                                                }
                                                
                                                $displayName = array_filter([$brand, $style]);
                                                $displayName = implode(' - ', $displayName);

                                                // if (($displayName == '-' || empty($displayName) || $item->stockEntryItem) && $item->art_no) {
                                                //     $displayName = $item->art_no;
                                                // }
                                                // {{ dd($displayName) }}
                                            @endphp
                                            <div class="fw-medium text-dark">{{ $displayName }}</div>
                                            @if($sleeve)
                                                <small class="text-primary fw-medium">{{ $sleeve }} Sleeve</small>
                                            @endif
                                            @if($item->art_no)
                                                <div class="text-muted" style="font-size: 11px;">Art No: {{ $item->art_no }}</div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="small fw-bold">{{ $item->color->color_name ?? '-' }}</div>
                                            <div class="badge bg-light text-dark border">{{ $item->size->name ?? $item->size_id ?? '-' }}</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark px-3 py-2 fw-medium border">
                                                {{ number_format($item->qty, 2) }} {{ $item->uom_id ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div>₹{{ number_format($item->mrp, 2) }}</div>
                                        </td>
                                        <td class="text-end fw-bold text-dark pe-4">₹{{ number_format($item->amount, 2) }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="6" class="text-center py-5 text-muted">No items found</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="row g-4">
                <div class="col-lg-7">
                    <!-- Additional Info -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-header bg-light py-3" style="border-radius: 12px 12px 0 0; border-bottom: 1px solid #f0f0f0;">
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small">Additional Information</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3 text-break">
                                <div class="col-12 mt-3">
                                    <div class="text-muted text-uppercase small fw-bold mb-1">Terms & Conditions</div>
                                    <p class="small text-dark border p-3 rounded bg-white shadow-sm mb-0 text-break" style="white-space: pre-line;">{{ $salesOrder->terms_conditions ?? '-' }}</p>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="text-muted text-uppercase small fw-bold mb-1">Internal Remarks</div>
                                    <p class="small text-dark border p-3 rounded bg-white shadow-sm mb-0 text-break" style="white-space: pre-line;">{{ $salesOrder->internal_remarks ?? '-' }}</p>
                                </div>
                                @if($salesOrder->attachment)
                                <div class="col-12 mt-3">
                                    <div class="text-muted text-uppercase small fw-bold mb-2">Attachments</div>
                                    <div class="d-flex flex-wrap gap-3 mt-1">
                                        @foreach(explode(',', $salesOrder->attachment) as $file)
                                            @php
                                                $ext = pathinfo($file, PATHINFO_EXTENSION);
                                                $isImg = in_array(strtolower($ext), ['jpg','jpeg','png','webp','gif']);
                                                $url = url('uploads/so/' . $salesOrder->id . '/' . $file);
                                            @endphp
                                            <div class="p-2 border rounded bg-white shadow-sm d-flex align-items-center mb-2" style="min-width: 120px;">
                                                @if($isImg)
                                                    <img src="{{ $url }}" class="rounded cursor-pointer view-image border" data-image="{{ $url }}" width="60" height="60" style="object-fit: cover;" alt="Attachment">
                                                    <div class="ms-3">
                                                        <div class="fw-bold text-dark small text-uppercase">Image</div>
                                                        <div class="text-muted small" style="font-size: 10px;">Click to zoom</div>
                                                    </div>
                                                @else
                                                    <a href="{{ $url }}" target="_blank" class="text-decoration-none d-flex align-items-center">
                                                        @if(strtolower($ext) == 'pdf')
                                                            <i class="ri-file-pdf-fill text-danger ri-3x"></i>
                                                        @else
                                                            <i class="ri-file-text-fill text-primary ri-3x"></i>
                                                        @endif
                                                        <div class="ms-2">
                                                            <div class="fw-bold text-dark small text-uppercase">{{ $ext }} Document</div>
                                                            <div class="text-muted small" style="font-size: 10px;">Click to view</div>
                                                        </div>
                                                    </a>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Billing Summary -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm sticky-top" style="border-radius: 12px; top: 1rem;">
                        <div class="card-header bg-light py-3" style="border-radius: 12px 12px 0 0; border-bottom: 1px solid #f0f0f0;">
                            <h5 class="mb-0 fw-bold text-dark">Billing Summary</h5>
                        </div>
                        <div class="card-body p-4">
                            
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted fw-medium">Total Quantity</span>
                                <span class="fw-bold">{{ number_format($salesOrder->total_qty, 2) }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted fw-medium">Sub Total</span>
                                <span class="fw-bold">₹{{ number_format($salesOrder->sub_total_qty, 2) }}</span>
                            </div>

                            @if($salesOrder->discount_amount > 0)
                            <div class="d-flex justify-content-between mb-3 text-danger">
                                <span class="fw-medium">{{ $salesOrder->apply_box_discount ? 'Box Discount' : 'Discount' }} ({{ number_format($salesOrder->discount_percent, 2) }}%)</span>
                                <span class="fw-bold">-₹{{ number_format($salesOrder->discount_amount, 2) }}</span>
                            </div>
                            @endif

                            <div class="d-flex justify-content-between mb-3 pt-3 border-top">
                                <span class="text-dark fw-bold">Taxable Amount</span>
                                <span class="fw-bold text-dark">₹{{ number_format($salesOrder->taxable_amount, 2) }}</span>
                            </div>

                            @if($salesOrder->other_state)
                                <div class="d-flex justify-content-between mb-3 text-muted small">
                                    <span>IGST ({{ number_format($salesOrder->igst_percent, 2) }}%)</span>
                                    <span>₹{{ number_format($salesOrder->tax_amount, 2) }}</span>
                                </div>
                            @else
                                <div class="d-flex justify-content-between mb-3 text-muted small">
                                    <span>CGST ({{ number_format($salesOrder->cgst_percent, 2) }}%)</span>
                                    <span>₹{{ number_format($salesOrder->taxable_amount * ($salesOrder->cgst_percent / 100), 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 text-muted small">
                                    <span>SGST ({{ number_format($salesOrder->sgst_percent, 2) }}%)</span>
                                    <span>₹{{ number_format($salesOrder->taxable_amount * ($salesOrder->sgst_percent / 100), 2) }}</span>
                                </div>
                            @endif

                            @if($salesOrder->round_off > 0)
                            <div class="d-flex justify-content-between mb-3 text-muted italic small">
                                <span>Round Off ({{ $salesOrder->round_off_type }})</span>
                                <span class="fw-bold">{{ $salesOrder->round_off_type == 'Less' ? '-' : '+' }}₹{{ number_format($salesOrder->round_off, 2) }}</span>
                            </div>
                            @endif

                            <div class="bg-primary-soft p-3 rounded-3 mt-4 border-start border-primary border-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-primary small uppercase">Grand Total</span>
                                    <span class="fs-5 fw-bold text-primary">₹{{ number_format($salesOrder->total_amount, 2) }}</span>
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

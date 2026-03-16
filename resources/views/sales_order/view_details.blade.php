@extends('layouts.common')
@section('title', 'View Sale Order - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Sale Order</h4>
                <div class="d-flex gap-2">
                    <a href="{{ url('sales_orders') }}" class="btn btn-primary">
                        <i class="ri ri-arrow-left-line back-arrow"></i> Back
                    </a>
                </div>
            </div>

            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        {{-- ===== ORDER DETAILS ===== --}}
                        <div class="col-lg-12">
                            <h6>Order Details:</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">SO Number:</label>
                            <div class="text-muted">{{ $salesOrder->so_no }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">SO Date:</label>
                            <div class="text-muted">{{ $salesOrder->so_date->format('d-M-Y') }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Order Type:</label>
                            <div class="text-muted">{{ $salesOrder->order_type }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Request Date:</label>
                            <div class="text-muted">{{ $salesOrder->request_date ? $salesOrder->request_date->format('d-M-Y') : '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Season:</label>
                            <div class="text-muted">{{ $salesOrder->season->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Customer Name:</label>
                            <div class="text-muted">{{ $salesOrder->customer ? $salesOrder->customer->name . ' (' . $salesOrder->customer->code . ')' : '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Customer PO Ref No:</label>
                            <div class="text-muted">{{ $salesOrder->customer_po_ref ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Store:</label>
                            <div class="text-muted">{{ $salesOrder->store->store_type_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Sales Agent:</label>
                            <div class="text-muted">{{ $salesOrder->salesAgent->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Zone:</label>
                            <div class="text-muted">{{ $salesOrder->zone->zone_name ?? '-' }}</div>
                        </div>

                        <div class="col-lg-12"><hr></div>

                        {{-- ===== BILLING & SHIPPING ===== --}}
                        <div class="col-lg-12">
                            <h6>Billing &amp; Shipping Details:</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Billing Address:</label>
                            <div class="text-muted">{{ $salesOrder->billing_address ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Shipping Address:</label>
                            <div class="text-muted">{{ $salesOrder->shipping_address ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Payment Terms:</label>
                            <div class="text-muted">{{ $salesOrder->payment_terms ?? '-' }}</div>
                        </div>

                        <div class="col-lg-12"><hr></div>

                        {{-- ===== LOGISTICS ===== --}}
                        <div class="col-lg-12">
                            <h6>Logistics &amp; Destination:</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Shipping Method:</label>
                            <div class="text-muted">{{ $salesOrder->shippingMethod->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Transport Mode:</label>
                            <div class="text-muted">{{ $salesOrder->transportMode->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Dispatch From:</label>
                            <div class="text-muted">{{ $salesOrder->dispatchFrom->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Transporter Name:</label>
                            <div class="text-muted">{{ $salesOrder->transporter_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Freight Type:</label>
                            <div class="text-muted">{{ $salesOrder->freight_type ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Freight Amount:</label>
                            <div class="text-muted">₹{{ number_format($salesOrder->freight_amount, 2) }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Transport GST No:</label>
                            <div class="text-muted">{{ $salesOrder->transport_gst_no ?? '-' }}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Dispatch Through:</label>
                            <div class="text-muted">{{ $salesOrder->dispatch_through ?? '-' }}</div>
                        </div>

                        <div class="col-lg-12"><hr></div>

                        {{-- ===== ITEM DETAILS ===== --}}
                        <div class="col-lg-12">
                            <h6>Item Details:</h6>
                        </div>
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>S.No</th>
                                            <th>Brand Category</th>
                                            <th>Item</th>
                                            <th>Color</th>
                                            <th>Art No</th>
                                            <th>UOM</th>
                                            <th>Size</th>
                                            <th>Qty</th>
                                            <th>Rate</th>
                                            <th>MRP</th>
                                            <th>Amount</th>
                                            <th>Sleeve Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($salesOrder->items->count() > 0)
                                            @foreach($salesOrder->items as $idx => $item)
                                            <tr>
                                                <td>{{ $idx + 1 }}</td>
                                                <td>{{ $item->brandCategory->name ?? '-' }} ({{ $item->brandCategory->code ?? '-' }})</td>
                                                <td>{{ $item->item->item_name ?? $item->item->name ?? '-' }} ({{ $item->item->code ?? '-' }})</td>
                                                <td>{{ $item->color->color_name ?? '-' }}</td>
                                                <td>{{ $item->art_no ?? '-' }}</td>
                                                <td>{{ $item->uom->uom_code ?? '-' }}</td>
                                                <td>{{ $item->size_id ?? '-' }}</td>
                                                <td>{{ number_format($item->qty, 2) }}</td>
                                                <td>₹{{ number_format($item->rate, 2) }}</td>
                                                <td>₹{{ number_format($item->mrp ?? 0, 2) }}</td>
                                                <td>₹{{ number_format($item->amount, 2) }}</td>
                                                <td>{{ $item->sleeve ? implode(', ', $item->sleeve) : '-' }}</td>
                                            </tr>
                                            @endforeach
                                        @else
                                        <tr><td colspan="12" class="text-center text-muted">No items found</td></tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-12"><hr></div>

                        {{-- ===== ADDITIONAL INFO + TAX SUMMARY ===== --}}
                        <div class="row g-4 mt-2">
                            <div class="col-lg-6">
                                <div class="card h-100 border shadow-none">
                                    <div class="card-body">
                                        <div class="card-header-box mb-4">
                                            <h6 class="mb-0">Additional Information</h6>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="detail-title d-block">Status:</label>
                                                <div>
                                                    @php
                                                    $statusColors = [
                                                        'Draft' => 'secondary', 'Approved' => 'success',
                                                        'Pending' => 'warning', 'In Production' => 'info',
                                                        'Dispatched' => 'primary', 'Cancelled' => 'danger',
                                                    ];
                                                    @endphp
                                                    <span class="badge bg-label-{{ $statusColors[$salesOrder->status] ?? 'secondary' }}">{{ $salesOrder->status }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="detail-title d-block">Approved By:</label>
                                                <div class="text-muted">{{ $salesOrder->approvedBy->name ?? '-' }}</div>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="detail-title d-block">Approved Date:</label>
                                                <div class="text-muted">{{ $salesOrder->approved_date ? $salesOrder->approved_date->format('d-M-Y H:i A') : '-' }}</div>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="detail-title d-block">Terms & Conditions:</label>
                                                <div class="text-muted border rounded p-2">{!! nl2br(e($salesOrder->terms_conditions ?? '-')) !!}</div>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="detail-title d-block">Internal Notes:</label>
                                                <div class="text-muted border rounded p-2">{!! nl2br(e($salesOrder->internal_remarks ?? '-')) !!}</div>
                                            </div>
                                            @if($salesOrder->attachment)
                                            <div class="col-md-12">
                                                <label class="detail-title d-block">Attachments:</label>
                                                <div class="mt-2 d-flex flex-wrap gap-3">
                                                    @foreach(explode(',', $salesOrder->attachment) as $file)
                                                        @php
                                                        $ext = pathinfo($file, PATHINFO_EXTENSION);
                                                        $isImg = in_array(strtolower($ext), ['jpg','jpeg','png','webp','gif']);
                                                        $url = url('uploads/so/' . $salesOrder->id . '/' . $file);
                                                        @endphp
                                                        <div class="attachment-box">
                                                            @if($isImg)
                                                            <a href="{{ $url }}" target="_blank">
                                                                <img src="{{ $url }}" alt="Attachment" class="img-fluid rounded border shadow-sm" style="height:80px; width:80px; object-fit:cover;">
                                                            </a>
                                                            @else
                                                            <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-secondary h-100 d-flex align-items-center">
                                                                <i class="ri ri-file-text-line me-1"></i> {{ mb_strimwidth($file, 0, 15, '...') }}
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

                            <div class="col-lg-6">
                                <div class="card h-100 border shadow-none">
                                    <div class="card-body">
                                        <div class="card-header-box mb-4">
                                            <h6 class="mb-0">Tax Summary</h6>
                                        </div>
                                        <div class="d-flex flex-column gap-3">
                                            <div class="d-flex justify-content-between">
                                                <span class="fw-semibold">Total Qty:</span>
                                                <span class="fw-bold">{{ number_format($salesOrder->total_qty, 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="fw-semibold">Sub Total:</span>
                                                <span class="fw-bold">₹{{ number_format($salesOrder->sub_total_qty, 2) }}</span>
                                            </div>
                                            <div class="row align-items-center g-2">
                                                <div class="col-6"><span class="fw-semibold">Box Discount:</span></div>
                                                <div class="col-6 text-end">
                                                    <span class="badge bg-label-{{ $salesOrder->apply_box_discount ? 'danger' : 'secondary' }}">
                                                        {{ $salesOrder->apply_box_discount ? 'Applied' : 'Not Applied' }}
                                                    </span>
                                                    <span class="ms-1 small text-muted">({{ $salesOrder->discount_percent }}%)</span>
                                                    <div class="fw-bold mt-1">₹{{ number_format($salesOrder->discount_amount, 2) }}</div>
                                                </div>
                                            </div>
                                            <hr class="my-1">
                                            <div class="d-flex justify-content-between">
                                                <span class="fw-semibold">Net Amount (Before Tax):</span>
                                                <span class="fw-bold">₹{{ number_format($salesOrder->taxable_amount, 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="fw-semibold">Other State:</span>
                                                <span class="badge bg-label-{{ $salesOrder->other_state ? 'warning' : 'secondary' }}">{{ $salesOrder->other_state ? 'Yes' : 'No' }}</span>
                                            </div>
                                            @if($salesOrder->other_state)
                                            <div class="row align-items-center g-2">
                                                <div class="col-6"><span class="fw-semibold">IGST:</span></div>
                                                <div class="col-6 text-end">
                                                    <span class="small text-muted">({{ $salesOrder->igst_percent }}%)</span>
                                                    <span class="fw-bold ms-1">₹{{ number_format($salesOrder->tax_amount, 2) }}</span>
                                                </div>
                                            </div>
                                            @else
                                            <div class="row align-items-center g-2">
                                                <div class="col-6"><span class="fw-semibold">CGST:</span></div>
                                                <div class="col-6 text-end">
                                                    <span class="small text-muted">({{ $salesOrder->cgst_percent }}%)</span>
                                                    <span class="fw-bold ms-1">₹{{ number_format($salesOrder->taxable_amount * $salesOrder->cgst_percent / 100, 2) }}</span>
                                                </div>
                                            </div>
                                            <div class="row align-items-center g-2">
                                                <div class="col-6"><span class="fw-semibold">SGST:</span></div>
                                                <div class="col-6 text-end">
                                                    <span class="small text-muted">({{ $salesOrder->sgst_percent }}%)</span>
                                                    <span class="fw-bold ms-1">₹{{ number_format($salesOrder->taxable_amount * $salesOrder->sgst_percent / 100, 2) }}</span>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="d-flex justify-content-between">
                                                <span class="fw-semibold">Tax Amount:</span>
                                                <span class="fw-bold">₹{{ number_format($salesOrder->tax_amount, 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="fw-semibold">Round Off ({{ $salesOrder->round_off_type }}):</span>
                                                <span class="fw-bold">₹{{ number_format($salesOrder->round_off, 2) }}</span>
                                            </div>
                                            <hr class="my-1">
                                            <div class="d-flex justify-content-between align-items-center text-primary">
                                                <h5 class="m-0 fw-bold">Total Amount:</h5>
                                                <h5 class="m-0 fw-bold">₹{{ number_format($salesOrder->total_amount, 2) }}</h5>
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

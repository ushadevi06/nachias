@extends('layouts.common')
@section('title', 'View Purchase Order - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Purchase Order</h4>
                <a href="{{ url('purchase_orders') }}" class="btn btn-primary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <h6>Order Details:</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">PO Number: </label>
                            <div class="text-muted">{{ $purchaseOrder->po_number }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">PO Date:</label>
                            <div class="text-muted">{{ $purchaseOrder->po_date->format('d-M-Y') }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Broker / Sales Agent:</label>
                            <div class="text-muted">
                                @if($purchaseOrder->salesAgent)
                                {{ $purchaseOrder->salesAgent->name }}({{ $purchaseOrder->salesAgent->code }})
                                @else
                                -
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Supplier:</label>
                            <div class="text-muted">{{ $purchaseOrder->supplier->name }} ({{ $purchaseOrder->supplier->code }})</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Reference No:</label>
                            <div class="text-muted">{{ $purchaseOrder->reference_no ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Reference Date:</label>
                            <div class="text-muted">{{ $purchaseOrder->reference_date ? $purchaseOrder->reference_date->format('d-M-Y') : '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Due Date:</label>
                            <div class="text-muted">{{ $purchaseOrder->due_date->format('d-M-Y') }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Delivery Location:</label>
                            <div class="text-muted">{{ $purchaseOrder->storeType->store_type_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Order Date:</label>
                            <div class="text-muted">{{ $purchaseOrder->order_date->format('d-M-Y') }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Payment Terms:</label>
                            <div class="text-muted">{{ $purchaseOrder->payment_terms ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Commission (%):</label>
                            <div class="text-muted">{{ $purchaseOrder->commission ? number_format($purchaseOrder->commission, 2) . '%' : '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Status:</label>
                            <div>
                                @php
                                    $statusClass = [
                                    'Draft' => 'bg-secondary',
                                    'Approved' => 'bg-success',
                                    'Dispatched' => 'bg-info',
                                    'Received' => 'bg-primary'
                                    ];
                                @endphp
                                <span class="badge {{ $statusClass[$purchaseOrder->status] ?? 'bg-secondary' }}">{{ $purchaseOrder->status }}</span>
                            </div>
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
                                            <th>Category</th>
                                            <th>Material (Code)</th>
                                            <th>Art No</th>
                                            <th>Quantity</th>
                                            <th>UOM</th>
                                            <th>Rate</th>
                                            <th>Amount</th>
                                            <th>Remarks</th>
                                            <th>File</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($purchaseOrder->items as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->storeCategory->category_name ?? '-' }}</td>
                                            <td>{{ $item->rawMaterial->name ?? '-' }} <span class="mini-title">({{ $item->rawMaterial->code ?? '-' }})</span></td>
                                            <td>{{ $item->art_no ?? '-' }}</td>
                                            <td>{{ number_format($item->quantity, 2) }}</td>
                                            <td>{{ $item->uom->uom_code ?? '-' }}</td>
                                            <td>₹{{ number_format($item->rate, 2) }}</td>
                                            <td>₹{{ number_format($item->amount, 2) }}</td>
                                            <td>{{ $item->remarks ?? '-' }}</td>
                                            <td>
                                                @if($item->attached_file)
                                                <a href="javascript:void(0)" class="view-image" data-image="{{ asset('uploads/purchase_orders/' . $item->attached_file) }}">
                                                    <i class="ri ri-image-line"></i> View
                                                </a>
                                                @else
                                                -
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="9" class="text-end"><strong>Subtotal</strong></td>
                                            <td><strong>₹{{ number_format($purchaseOrder->sub_total, 2) }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <h6>Tax & Charges:</h6>
                        </div>

                        <div class="col-md-3 col-xl-2">
                            <label class="detail-title">Total Qty:</label>
                            <div class="text-muted">{{ number_format($purchaseOrder->total_qty, 2) }}</div>
                        </div>

                        <div class="col-md-3 col-xl-2">
                            <label class="detail-title">Sub Total:</label>
                            <div class="text-muted">₹{{ number_format($purchaseOrder->sub_total, 2) }}</div>
                        </div>

                        <div class="col-md-3 col-xl-2">
                            <label class="detail-title">Discount %:</label>
                            <div class="text-muted">{{ number_format($purchaseOrder->discount_percent, 2) }}%</div>
                        </div>

                        <div class="col-md-3 col-xl-2">
                            <label class="detail-title">Discount Amount:</label>
                            <div class="text-muted">₹{{ number_format($purchaseOrder->discount_amount, 2) }}</div>
                        </div>

                        <div class="col-md-3 col-xl-2">
                            <label class="detail-title">Taxable Amount:</label>
                            <div class="text-muted">₹{{ number_format($purchaseOrder->taxable_amount, 2) }}</div>
                        </div>

                        <div class="col-md-3 col-xl-2">
                            <label class="detail-title">Other State:</label>
                            <div class="text-muted">{{ $purchaseOrder->other_state ? 'Yes' : 'No' }}</div>
                        </div>

                        @if($purchaseOrder->other_state)
                        <div class="col-md-3 col-xl-2">
                            <label class="detail-title">IGST ({{ number_format($purchaseOrder->igst_percent, 2) }}%):</label>
                            <div class="text-muted">₹{{ number_format(($purchaseOrder->taxable_amount * $purchaseOrder->igst_percent) / 100, 2) }}</div>
                        </div>
                        @else
                        <div class="col-md-3 col-xl-2">
                            <label class="detail-title">CGST ({{ number_format($purchaseOrder->cgst_percent, 2) }}%):</label>
                            <div class="text-muted">₹{{ number_format(($purchaseOrder->taxable_amount * $purchaseOrder->cgst_percent) / 100, 2) }}</div>
                        </div>

                        <div class="col-md-3 col-xl-2">
                            <label class="detail-title">SGST ({{ number_format($purchaseOrder->sgst_percent, 2) }}%):</label>
                            <div class="text-muted">₹{{ number_format(($purchaseOrder->taxable_amount * $purchaseOrder->sgst_percent) / 100, 2) }}</div>
                        </div>
                        @endif

                        <div class="col-md-3 col-xl-2">
                            <label class="detail-title">Tax Amount:</label>
                            <div class="text-muted">₹{{ number_format($purchaseOrder->tax_amount, 2) }}</div>
                        </div>

                        <div class="col-md-3 col-xl-2">
                            <label class="detail-title">Total Amount:</label>
                            <div class="text-muted text-success fw-bold">₹{{ number_format($purchaseOrder->total_amount, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    $(document).ready(function() {
        $(document).on('click', '.view-image', function() {
            let imageSrc = $(this).data('image');
            $('#modalImage').attr('src', imageSrc);
            $('#imageModal').modal('show');
        });
    });
</script>
@endsection
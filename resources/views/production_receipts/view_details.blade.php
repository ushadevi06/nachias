@extends('layouts.common')
@section('title', 'Production Receipt Details - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row g-4">
        <div class="col-lg-12 d-flex justify-content-between align-items-center">
            <h4>Production Receipt</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('production_receipts.download_pdf', $receipt->id) }}" class="btn btn-primary" target="_blank">
                    <i class="ri ri-download-line me-1"></i> Download
                </a>
                <a href="{{ route('production_receipts.print', $receipt->id) }}" class="btn btn-primary" target="_blank">
                    <i class="ri ri-printer-line me-1"></i> Print
                </a>
                <a href="{{ url('production_receipts') }}" class="btn btn-outline-secondary">
                    <i class="ri ri-arrow-left-line me-1"></i> Back
                </a>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Job Card No</label>
                            <p>{{ $receipt->jobCard->job_card_no ?? '-' }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Receipt Date</label>
                            <p>{{ date('d-m-Y', strtotime($receipt->receipt_date)) }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Brand</label>
                            <p>{{ $receipt->jobCard->brand->brand_name ?? '-' }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Doc Date</label>
                            <p>{{ date('d-m-Y', strtotime($receipt->doc_date)) }}</p>
                        </div> 
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Responsible Person(Employee)</label>
                            <p>{{ $receipt->employee->employee_name ?? '-' }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Plant</label>
                            <p>{{ $receipt->plant->plant_name ?? '-' }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Order Due Date</label>
                            <p>{{ $receipt->order_due_date ?? '-' }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Store</label>
                            <p>{{ $receipt->storeType->store_type_name ?? '-' }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Location</label>
                            <p>{{ $receipt->storeLocation->store_location ?? '-' }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Status</label>
                            <p>
                                @if($receipt->status == 'Posted')
                                    <span class="badge bg-label-success">Posted</span>
                                @else
                                    <span class="badge bg-label-warning">Draft</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Remarks</label>
                            <p>{{ $receipt->remark ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Item Details</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item Name</th>
                                    <th>Art No</th>
                                    <th>Size / Variant</th>
                                    <th class="text-center">Ordered Qty</th>
                                    <th class="text-center">Scan Qty</th>
                                    <th class="text-center">Received Qty</th>
                                    <th class="text-center">UOM</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Total Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($receipt->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <span class="fw-bold">{{ $item->item_name }}</span><br>
                                            <small class="text-muted">{{ $item->item_code }}</small>
                                        </td>
                                        <td>{{ $item->resolved_art_no ?? $item->art_no ?? '-' }}</td>
                                        <td>{{ $item->size_variant }}</td>
                                        <td class="text-center">{{ number_format($item->ordered_qty, 2) }}</td>
                                        <td class="text-center">{{ number_format($item->scan_qty, 2) }}</td>
                                        <td class="text-center fw-bold">{{ number_format($item->qty_to_receive, 2) }}</td>
                                        <td class="text-center">{{ $item->uom_code }}</td>
                                        <td class="text-end">₹{{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-end fw-bold">₹{{ number_format($item->total_value, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr class="fw-bold">
                                    <td colspan="4" class="text-end">Total Qty:</td>
                                    <td class="text-center">{{ number_format($receipt->items->sum('ordered_qty'), 2) }}</td>
                                    <td class="text-center">{{ number_format($receipt->items->sum('scan_qty'), 2) }}</td>
                                    <td class="text-center">{{ number_format($receipt->items->sum('qty_to_receive'), 2) }}</td>
                                    <td></td>
                                    <td class="text-end">Grand Total:</td>
                                    <td class="text-end text-primary">₹{{ number_format($receipt->items->sum('total_value'), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            @if($receipt->remarks)
                <div class="card mt-4">
                    <div class="card-body">
                        <label class="form-label fw-bold">Remarks</label>
                        <p class="mb-0">{{ $receipt->remarks }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@extends('layouts.common')
@section('title', 'Credit Note Details - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="card-header-box d-flex justify-content-between align-items-center  pb-2 mb-4">
                        <h4 class="mb-0">Credit Note Details <strong class="text-primary">[{{ $creditNote->note_no }}]</strong></h4>
                        <div class="header-actions">
                            <a href="{{ url('credit_notes/print/' . $creditNote->id) }}" target="_blank" class="btn btn-primary me-2">
                                <i class="ri ri-printer-line me-1"></i> PRINT
                            </a>
                            <a href="{{ url('credit_notes/download/' . $creditNote->id) }}" class="btn btn-primary me-2">
                                <i class="ri ri-download-line me-1"></i> DOWNLOAD
                            </a>
                            <a href="{{ url('credit_notes') }}" class="btn btn-secondary"><i class="ri ri-arrow-left-line me-1"></i>Back</a>
                        </div>
                    </div>
                    
                    <div class="row g-4 mb-5">
                        <div class="col-md-3">
                            <label class="text-muted d-block small uppercase mb-1">Note Number</label>
                            <h6 class="mb-0">{{ $creditNote->note_no }}</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted d-block small uppercase mb-1">Date</label>
                            <h6 class="mb-0">{{ $creditNote->note_date->format('d-M-Y') }}</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted d-block small uppercase mb-1">Sales Invoice</label>
                            <h6 class="mb-0">{{ $creditNote->salesInvoice ? $creditNote->salesInvoice->inv_no : '-' }}</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted d-block small uppercase mb-1">Customer</label>
                            <h6 class="mb-0">{{ $creditNote->customer ? $creditNote->customer->name : '-' }}</h6>
                        </div>
                    </div>

                    <div class="row g-4 mb-5">
                        <div class="col-md-3">
                            <label class="text-muted d-block small uppercase mb-1">Status</label>
                            <span class="badge bg-{{ $creditNote->status == 'Approved' ? 'success' : ($creditNote->status == 'Cancelled' ? 'danger' : 'warning') }}">
                                {{ $creditNote->status }}
                            </span>
                        </div>
                        <div class="col-md-9">
                            <label class="text-muted d-block small uppercase mb-1">Reason</label>
                            <p class="mb-0">{{ $creditNote->reason ?: '-' }}</p>
                        </div>
                    </div>

                    <div class="table-responsive mb-5">
                        <table class="table table-bordered align-middle" style="min-width: 1000px;">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Brand Category</th>
                                    <th>Item Name</th>
                                    <th>Size</th>
                                    <th>UOM</th>
                                    <th class="text-end">Quantity</th>
                                    <th class="text-end">Rate</th>
                                    <th class="text-end">MRP</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($creditNote->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        @php
                                            $sleeveShort = '';
                                            if ($item->sleeve_type) {
                                                $sleeveShort = ' - ' . (strtolower($item->sleeve_type) == 'full' ? 'F/S' : 'H/S');
                                            }
                                        @endphp
                                        <td>{{ $item->brandCategory ? $item->brandCategory->name : '-' }}</td>
                                        <td>{{ ($item->item ? $item->item->name : '-') . ($item->item ? ' (' . $item->item->code . ')' : '') . $sleeveShort }}</td>
                                        <td>{{ $item->size ?: '-' }}</td>
                                        <td>{{ $item->uom ? $item->uom->uom_code : '-' }}</td>
                                        <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                                        <td class="text-end">₹{{ number_format($item->rate, 2) }}</td>
                                        <td class="text-end">₹{{ number_format($item->mrp, 2) }}</td>
                                        <td class="text-end">₹{{ number_format($item->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row justify-content-end">
                        <div class="col-md-5">
                            <div class="card border">
                                <div class="card-body">
                                    <h5 class="mb-4">Tax Summary</h5>
                                    <table class="table table-borderless mb-0">
                                        <tbody>
                                            <tr>
                                                <th class="ps-0">Sub total:</th>
                                                <td class="text-end pe-0">₹{{ number_format($creditNote->sub_total, 2) }}</td>
                                            </tr>
                                            @if(!$creditNote->other_state)
                                                <tr>
                                                    <th class="ps-0">CGST ({{ $creditNote->cgst_percent }}%)</th>
                                                    <td class="text-end pe-0">₹{{ number_format($creditNote->cgst, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <th class="ps-0">SGST ({{ $creditNote->sgst_percent }}%)</th>
                                                    <td class="text-end pe-0">₹{{ number_format($creditNote->sgst, 2) }}</td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <th class="ps-0">IGST ({{ $creditNote->igst_percent }}%)</th>
                                                    <td class="text-end pe-0">₹{{ number_format($creditNote->igst, 2) }}</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <th class="ps-0">Tax Amount:</th>
                                                <td class="text-end pe-0">₹{{ number_format($creditNote->tax_amount, 2) }}</td>
                                            </tr>
                                            @if($creditNote->round_off > 0)
                                            <tr>
                                                <th class="ps-0">Round Off ({{ $creditNote->round_off_type }}):</th>
                                                <td class="text-end pe-0"><strong>{{ $creditNote->round_off_type == 'Less' ? '-' : '+' }}₹{{ number_format($creditNote->round_off, 2) }}</strong></td>
                                            </tr>
                                            @endif
                                            <tr class="border-top fw-bold">
                                                <th class="ps-0 h5 mb-0">Grand Total:</th>
                                                <td class="text-end pe-0 h5 mb-0 text-success">₹{{ number_format($creditNote->grand_total, 2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
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

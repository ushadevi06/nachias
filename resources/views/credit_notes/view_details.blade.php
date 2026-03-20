@extends('layouts.common')
@section('title', 'Credit Note Details - ' . env('WEBSITE_NAME'))
@section('content')

<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-primary mb-1">Credit Note Details</h3>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ url('credit_notes/download/' . $creditNote->id) }}" class="btn btn-primary d-flex align-items-center">
                        <i class="ri ri-download-line me-1"></i> Download
                    </a>
                    <a href="{{ url('credit_notes/print/' . $creditNote->id) }}" target="_blank" class="btn btn-primary d-flex align-items-center">
                        <i class="ri ri-printer-line me-1"></i> Print
                    </a>
                    <a href="{{ url('credit_notes') }}" class="btn btn-outline-secondary d-flex align-items-center">
                        <i class="ri ri-arrow-left-line me-1"></i> Back
                    </a>
                </div>
            </div>

            <!-- General Information -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-light py-3" style="border-radius: 12px 12px 0 0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">General Information</h5>
                        <span class="badge bg-white text-primary px-3 py-2">#{{ $creditNote->note_no }}</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Customer</div>
                            <div class="fw-bold text-dark">
                                {{ $creditNote->customer ? $creditNote->customer->name : 'N/A' }}
                                @if($creditNote->customer && $creditNote->customer->code)
                                    <span class="text-primary small">({{ $creditNote->customer->code }})</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Sales Invoice</div>
                            <div class="fw-bold text-dark">{{ $creditNote->salesInvoice ? $creditNote->salesInvoice->inv_no : '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Issue Date</div>
                            <div class="fw-bold text-dark">{{ $creditNote->note_date->format('d M, Y') }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Reason</div>
                            <div class="fw-bold text-dark">{{ $creditNote->reason ?? '-' }}</div>
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
                                    <th class="py-3 text-muted text-uppercase small fw-bold text-center">Quantity</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold text-end">Rate</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold text-end pe-4">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($creditNote->items->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="ri-information-line fs-3 d-block mb-2"></i>
                                            No items found for this credit note
                                        </td>
                                    </tr>
                                @else
                                    @foreach($creditNote->items as $index => $item)
                                    @php
                                        $sleeveShort = '';
                                        if ($item->sleeve_type) {
                                            $sleeveShort = ' - ' . (strtolower($item->sleeve_type) == 'full' ? 'F/S' : 'H/S');
                                        }
                                    @endphp
                                    <tr>
                                        <td class="ps-4 fw-bold">{{ sprintf('%02d', $index + 1) }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">
                                                {{ $item->brandCategory ? $item->brandCategory->name : '' }}
                                                {{ $item->item ? $item->item->name : 'N/A' }}{{ $sleeveShort }}
                                            </div>
                                            @if($item->item && $item->item->code)
                                                <small class="text-primary fw-medium">{{ $item->item->code }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark px-3 py-2 fw-medium">
                                                {{ number_format($item->quantity, 2) }} {{ $item->uom ? $item->uom->uom_code : '' }}
                                            </span>
                                        </td>
                                        <td class="text-end">₹{{ number_format($item->rate, 2) }}</td>
                                        <td class="text-end fw-bold text-dark pe-4">₹{{ number_format($item->amount, 2) }}</td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="row g-4 justify-content-between">
                <div class="col-lg-6" style="max-height:200px;">
                    @if($creditNote->remarks || $creditNote->reference_doc)
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background-color: #fcfcfc;">
                        <div class="card-body p-4">
                            @if($creditNote->remarks)
                                <h6 class="text-uppercase small fw-bold text-muted mb-3 border-bottom pb-2">Internal Remarks</h6>
                                <p class="mb-3 text-dark small" style="white-space: pre-line; line-height: 1.6;">{{ $creditNote->remarks }}</p>
                            @endif

                            @if($creditNote->reference_doc)
                                <h6 class="text-uppercase small fw-bold text-muted mb-3 {{ $creditNote->remarks ? 'mt-4 border-top pt-3' : '' }} border-bottom pb-2">Reference Document</h6>
                                @php
                                    $attachment = $creditNote->reference_doc;
                                    $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                    $url = url('uploads/credit_notes/' . $attachment);
                                @endphp

                                <div class="attachment-thumb border rounded p-1 bg-white shadow-sm position-relative" style="width: 100px; height: 100px;" title="{{ $attachment }}">
                                    @if($isImage)
                                        <img src="{{ $url }}" class="w-100 h-100 object-fit-cover rounded cursor-pointer view-image" data-image="{{ $url }}" alt="Reference">
                                    @else
                                        <a href="{{ $url }}" target="_blank" class="w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-light rounded text-decoration-none shadow-none text-primary">
                                            <i class="ri ri-file-text-line fs-2"></i>
                                            <span class="badge bg-primary text-white mt-1" style="font-size: 10px;">{{ strtoupper($extension) }}</span>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-header bg-light py-3" style="border-radius: 12px 12px 0 0; border-bottom: 1px solid #f0f0f0;">
                            <h5 class="mb-0 fw-bold text-dark">Billing Summary</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted fw-medium">Total Quantity</span>
                                <span class="fw-bold">{{ number_format($creditNote->items->sum('quantity'), 2) }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted fw-medium">Sub Total</span>
                                <span class="fw-bold">₹{{ number_format($creditNote->sub_total, 2) }}</span>
                            </div>

                            @if($creditNote->other_state)
                                <div class="d-flex justify-content-between mb-3 text-muted small">
                                    <span>IGST ({{ $creditNote->igst_percent }}%)</span>
                                    <span>₹{{ number_format($creditNote->igst, 2) }}</span>
                                </div>
                            @else
                                <div class="d-flex justify-content-between mb-3 text-muted small">
                                    <span>CGST ({{ $creditNote->cgst_percent }}%)</span>
                                    <span>₹{{ number_format($creditNote->cgst, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 text-muted small">
                                    <span>SGST ({{ $creditNote->sgst_percent }}%)</span>
                                    <span>₹{{ number_format($creditNote->sgst, 2) }}</span>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between mb-3 pt-3 border-top">
                                <span class="text-dark fw-bold">Total Tax</span>
                                <span class="fw-bold">₹{{ number_format($creditNote->tax_amount, 2) }}</span>
                            </div>

                            @if($creditNote->round_off > 0)
                            <div class="d-flex justify-content-between mb-3 text-muted italic">
                                <span>Round Off ({{ $creditNote->round_off_type }})</span>
                                <span>{{ $creditNote->round_off_type == 'Less' ? '-' : '+' }}₹{{ number_format($creditNote->round_off, 2) }}</span>
                            </div>
                            @endif

                            <div class="bg-primary-soft p-3 rounded-3 mt-4 border-start border-primary border-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-primary small uppercase">Grand Total</span>
                                    <span class="fs-5 fw-bold text-primary">₹{{ number_format($creditNote->grand_total, 2) }}</span>
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

@extends('layouts.common')
@section('title', 'View Debit Note - ' . env('WEBSITE_NAME'))
@section('content')

<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-primary mb-1">Debit Note Details</h3>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ url('debit_notes') }}">Debit Notes</a></li>
                            <li class="breadcrumb-item active" aria-current="page">View #{{ $debitNote->debit_note_no }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ url('debit_notes') }}" class="btn btn-outline-secondary d-flex align-items-center">
                        <i class="ri ri-arrow-left-line me-1"></i> Back
                    </a>
                    @if($debitNote->reference_document)
                        <a href="{{ url('uploads/debit_notes/' . $debitNote->reference_document) }}" target="_blank" class="btn btn-primary d-flex align-items-center">
                            <i class="ri ri-file-download-line me-1"></i> Document
                        </a>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-primary py-3" style="border-radius: 12px 12px 0 0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-white mb-0 fw-bold">General Information</h5>
                        <span class="badge bg-white text-primary px-3 py-2">#{{ $debitNote->debit_note_no }}</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Supplier</div>
                            <div class="fw-bold text-dark">
                                {{ $debitNote->supplier->name ?? 'N/A' }}
                                @if($debitNote->supplier && $debitNote->supplier->supplier_code)
                                    <span class="text-primary small">({{ $debitNote->supplier->supplier_code }})</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Purchase Invoice</div>
                            <div class="fw-bold text-dark">{{ $debitNote->purchaseInvoice->invoice_no ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Issue Date</div>
                            <div class="fw-bold text-dark">{{ $debitNote->debit_note_date->format('d M, Y') }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Reason</div>
                            <div class="fw-bold text-dark">{{ $debitNote->reason ?? '-' }}</div>
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
                                @if($debitNote->items->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="ri-information-line fs-3 d-block mb-2"></i>
                                            No items found for this debit note
                                        </td>
                                    </tr>
                                @else
                                    @foreach($debitNote->items as $index => $item)
                                    <tr>
                                        <td class="ps-4 fw-bold">{{ sprintf('%02d', $index + 1) }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $item->rawMaterial->name ?? 'N/A' }}</div>
                                            @if($item->rawMaterial && $item->rawMaterial->material_code)
                                                <small class="text-primary fw-medium">{{ $item->rawMaterial->material_code }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark px-3 py-2 fw-medium">
                                                {{ number_format($item->quantity, 2) }} {{ $item->uom->uom_code ?? '' }}
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
                <div class="col-lg-6">
                    @if($debitNote->remarks)
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background-color: #fcfcfc;">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase small fw-bold text-muted mb-3 border-bottom pb-2">Internal Remarks</h6>
                            <p class="mb-0 text-dark small" style="white-space: pre-line; line-height: 1.6;">{{ $debitNote->remarks }}</p>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted fw-medium">Sub Total</span>
                                <span class="fw-bold">₹{{ number_format($debitNote->sub_total, 2) }}</span>
                            </div>

                            @if($debitNote->other_state == 'Y')
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted fw-medium">IGST ({{ $debitNote->igst_percent }}%)</span>
                                    <span class="fw-bold">₹{{ number_format($debitNote->sub_total * ($debitNote->igst_percent / 100), 2) }}</span>
                                </div>
                            @else
                                <div class="d-flex justify-content-between mb-3 text-muted small">
                                    <span>CGST ({{ $debitNote->cgst_percent }}%)</span>
                                    <span>₹{{ number_format($debitNote->sub_total * ($debitNote->cgst_percent / 100), 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 text-muted small">
                                    <span>SGST ({{ $debitNote->sgst_percent }}%)</span>
                                    <span>₹{{ number_format($debitNote->sub_total * ($debitNote->sgst_percent / 100), 2) }}</span>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between mb-3 pt-3 border-top">
                                <span class="text-dark fw-bold">Total Tax</span>
                                <span class="fw-bold">₹{{ number_format($debitNote->tax_amount, 2) }}</span>
                            </div>

                            @if($debitNote->round_off > 0)
                            <div class="d-flex justify-content-between mb-3 text-muted italic">
                                <span>Round Off ({{ $debitNote->round_off_type }})</span>
                                <span>{{ $debitNote->round_off_type == 'Less' ? '-' : '+' }}₹{{ number_format($debitNote->round_off, 2) }}</span>
                            </div>
                            @endif

                            <div class="d-flex justify-content-between align-items-center mt-4 p-3 bg-primary bg-opacity-10 rounded">
                                <h4 class="mb-0 fw-bold text-primary">Grand Total</h4>
                                <h3 class="mb-0 fw-bold text-primary">₹{{ number_format($debitNote->grand_total, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

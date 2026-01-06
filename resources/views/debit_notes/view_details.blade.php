@extends('layouts.common')
@section('title', 'View Debit Note - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Debit Note</h4>
                <div>
                    <a href="{{ url('debit_notes') }}" class="btn btn-secondary me-2">
                        <i class="ri ri-arrow-left-line"></i> Back
                    </a>
                </div>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <h6>Debit Note Details:</h6>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="detail-title">Debit Note No: </label>
                            <div class="text-muted">{{ $debitNote->debit_note_no }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Debit Note Date:</label>
                            <div class="text-muted">{{ $debitNote->debit_note_date->format('d-m-Y') }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Purchase Invoice No:</label>
                            <div class="text-muted">{{ $debitNote->purchaseInvoice->invoice_no ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Supplier:</label>
                            <div class="text-muted">
                                {{ $debitNote->supplier->name ?? 'N/A' }}
                                @if($debitNote->supplier && $debitNote->supplier->supplier_code)
                                ({{ $debitNote->supplier->supplier_code }})
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Reason:</label>
                            <div class="text-muted">{{ $debitNote->reason ?? '-' }}</div>
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
                                            <th>Item</th>
                                            <th>Quantity</th>
                                            <th>Rate</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($debitNote->items as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                {{ $item->rawMaterial->name ?? 'N/A' }}
                                                @if($item->rawMaterial && $item->rawMaterial->material_code)
                                                <span class="mini-title">({{ $item->rawMaterial->material_code }})</span>
                                                @endif
                                            </td>
                                            <td>{{ number_format($item->quantity, 2) }} {{ $item->uom->uom_code ?? '' }}</td>
                                            <td>₹{{ number_format($item->rate, 2) }}</td>
                                            <td>₹{{ number_format($item->amount, 2) }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No items found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-12 d-flex justify-content-end">
                            <div class="summary-details p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Sub Total:</span>
                                    <strong>₹{{ number_format($debitNote->sub_total, 2) }}</strong>
                                </div>
                                @if($debitNote->other_state == 'Y')
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>IGST ({{ $debitNote->igst_percent }}%):</span>
                                        <strong>₹{{ number_format($debitNote->sub_total * ($debitNote->igst_percent / 100), 2) }}</strong>
                                    </div>
                                @else
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>CGST ({{ $debitNote->cgst_percent }}%):</span>
                                        <strong>₹{{ number_format($debitNote->sub_total * ($debitNote->cgst_percent / 100), 2) }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>SGST ({{ $debitNote->sgst_percent }}%):</span>
                                        <strong>₹{{ number_format($debitNote->sub_total * ($debitNote->sgst_percent / 100), 2) }}</strong>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between mb-2 border-top pt-2">
                                    <span>Tax Amount:</span>
                                    <strong>₹{{ number_format($debitNote->tax_amount, 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between border-top pt-2 mt-2">
                                    <span class="h5 mb-0">Grand Total:</span>
                                    <span class="h5 mb-0 text-primary">₹{{ number_format($debitNote->grand_total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        @if($debitNote->remarks)
                        <div class="col-md-12">
                            <label class="detail-title">Remarks:</label>
                            <div class="text-muted">{{ $debitNote->remarks }}</div>
                        </div>
                        @endif
                        @if($debitNote->reference_document)
                        <div class="col-md-4">
                            <label class="detail-title">Reference Document:</label>
                            <div class="text-muted">
                                <a href="{{ asset($debitNote->reference_document) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ri-eye-line me-1"></i> View Attachment</a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

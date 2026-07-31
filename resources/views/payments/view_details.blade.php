@extends('layouts.common')
@section('title', 'Payment Detail - ' . $payment->payment_no . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">Payment Details</h4>
                    <p class="text-muted mb-0">Record of transaction #{{ $payment->payment_no }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ url('payments/print/' . $payment->id) }}" target="_blank" class="btn btn-primary">
                        <i class="ri ri-printer-line me-1"></i> Print
                    </a>
                    <a href="{{ url('payments/download/' . $payment->id) }}" class="btn btn-primary" target="_blank">
                        <i class="ri ri-download-line me-1"></i> Download
                    </a>
                    <a href="{{ url('payments') }}" class="btn btn-outline-secondary">
                        <i class="ri ri-arrow-left-line me-1"></i> Back
                    </a>
                </div>
            </div>

            <div class="row g-4">
                <!-- Main Info Card -->
                <div class="col-xl-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase text-primary small font-bold tracking-wider mb-4">Core Information</h6>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded-3">
                                        <label class="text-muted small d-block mb-1">Payment Type</label>
                                        <h5 class="mb-0 text-dark">{{ $payment->payment_type }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded-3">
                                        <label class="text-muted small d-block mb-1">Payment Date</label>
                                        <h5 class="mb-0 text-dark">{{ date('d-m-Y', strtotime($payment->payment_date)) }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small d-block mb-1">Reference Document</label>
                                    <p class="font-medium text-dark h6 mb-0">{{ $payment->reference_type }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small d-block mb-1">Reference No</label>
                                    <p class="font-medium text-dark h6 mb-0 text-primary">{{ $payment->reference_no ?: 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method & Tracking -->
                    @if($payment->payment_mode != 'Cash')
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase text-primary small font-bold tracking-wider mb-4">Billing & Tracking Details</h6>
                            <div class="row g-4">
                                <div class="col-md-3">
                                    <label class="text-muted small d-block mb-1">Payment Mode</label>
                                    <p class="font-medium text-dark mb-0">{{ $payment->payment_mode }}</p>
                                </div>
                                @if($payment->bank_name)
                                <div class="col-md-3">
                                    <label class="text-muted small d-block mb-1">Bank Name</label>
                                    <p class="font-medium text-dark mb-0">{{ $payment->bank_name }}</p>
                                </div>
                                @endif
                                
                                @if($payment->transaction_no)
                                <div class="col-md-3">
                                    <label class="text-muted small d-block mb-1">Transaction / UTR No</label>
                                    <code class="text-primary font-bold fs-6">{{ $payment->transaction_no }}</code>
                                </div>
                                @endif

                                @if($payment->cheque_no)
                                <div class="col-md-3">
                                    <label class="text-muted small d-block mb-1">Cheque No</label>
                                    <p class="font-medium text-dark mb-0">{{ $payment->cheque_no }}</p>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-muted small d-block mb-1">Cheque Date</label>
                                    <p class="font-medium text-dark mb-0">{{ $payment->cheque_date ? date('d-m-Y', strtotime($payment->cheque_date)) : 'N/A' }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Remarks & Attachments Section -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-{{ $payment->attachment ? '8' : '12' }}">
                                    <h6 class="text-uppercase text-primary small font-bold tracking-wider mb-3">Notes & Remarks</h6>
                                    <div class="p-3 border rounded border-dashed text-muted italic">
                                        {{ $payment->remarks ?: 'No additional notes provided for this transaction.' }}
                                    </div>
                                </div>
                                @if($payment->attachment)
                                <div class="col-md-4">
                                    <h6 class="text-uppercase text-primary small font-bold tracking-wider mb-3">Attachment</h6>
                                    @php
                                        $attachment = $payment->attachment;
                                        $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                        $url = asset($attachment);
                                    @endphp

                                    <div class="attachment-thumb border rounded p-1 bg-white shadow-sm position-relative" style="width: 100px; height: 100px;" title="{{ basename($attachment) }}">
                                        @if($isImage)
                                            <img src="{{ $url }}" class="w-100 h-100 object-fit-cover rounded cursor-pointer view-image" data-image="{{ $url }}" alt="Attachment">
                                        @else
                                            <a href="{{ $url }}" target="_blank" class="w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-light rounded text-decoration-none shadow-none text-primary">
                                                <i class="ri ri-file-text-line fs-2"></i>
                                                <span class="badge bg-primary text-white mt-1" style="font-size: 10px;">{{ strtoupper($extension) }}</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Snapshot Widget -->
                <div class="col-xl-4">
                    <div class="card border-0 bg-primary text-white shadow-lg sticky-top" style="top: 2rem; z-index: 10;">
                        <div class="card-body p-4 text-center">
                            <div class="mb-4">
                                <div class="bg-opacity-10 d-inline-flex p-3 rounded-circle mb-3">
                                    <i class="ri ri-wallet-3-line ri-2x text-white"></i>
                                </div>
                                <h6 class="text-white text-uppercase small tracking-widest">Total Amount</h6>
                                <h2 class="text-white font-bold mb-0">₹{{ number_format($payment->amount, 2) }}</h2>
                            </div>
                            <hr class="border-white opacity-10">
                            <div class="text-start">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-white-50 small">Voucher ID</span>
                                    <span class="font-medium">{{ $payment->payment_no }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-white-50 small">Status</span>
                                    <span class="badge bg-success bg-opacity-25 text-white rounded-pill">Confirmed</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($payment->created_at)
                    <div class="mt-4 p-4 text-center">
                        <p class="small text-muted mb-0">Recorded on {{ $payment->created_at->format('M d, Y') }} at {{ $payment->created_at->format('h:i A') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .font-bold { font-weight: 700; }
    .font-medium { font-weight: 500; }
    .border-dashed { border-style: dashed !important; }
    .bg-light { background-color: #f8f9fa !important; }
    .tracking-wider { letter-spacing: 0.05em; }
    .tracking-widest { letter-spacing: 0.1em; }
</style>
@endsection
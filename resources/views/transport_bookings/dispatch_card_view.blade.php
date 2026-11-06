@extends('layouts.common')
@section('title', 'Customer Dispatch Card - ' . $invoice_no)

@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Action Bar -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                    <div>
                        <a href="{{ route('transport_bookings.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="ri ri-arrow-left-line me-1"></i> Back to Register
                        </a>
                        <span class="badge bg-label-primary ms-2 fs-6">Invoice #{{ $invoice_no }}</span>
                        @if($booking->customer_notified)
                            <span class="badge bg-success ms-1"><i class="ri ri-checkbox-circle-line me-1"></i> Notified via Email</span>
                        @endif
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('transport_bookings.dispatch_card_pdf', $booking->id) }}" target="_blank" class="btn btn-primary btn-sm">
                            <i class="ri ri-printer-line me-1"></i> Print / Download PDF
                        </a>
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#emailModal">
                            <i class="ri ri-mail-send-line me-1"></i> Send / Resend Email
                        </button>
                    </div>
                </div>
            </div>

            @include('flash_messages')

            <!-- Visual Dispatch Card Display -->
            <div class="card shadow border-0 overflow-hidden" style="border-radius: 20px;">
                <div class="card-body p-0" style="background-color: #f1f5f9;">
                    @include('emails.dispatch_card')
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Send Email Modal -->
<div class="modal fade" id="emailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('transport_bookings.send_dispatch_email', $booking->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Send Customer Dispatch Notification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">
                        This will send the Casino dispatch card HTML email along with the PDF attachment to the customer.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Customer Name</label>
                        <input type="text" class="form-control" value="{{ $customer_name }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Customer Email Address</label>
                        <input type="email" name="customer_email" class="form-control" value="{{ $customer_email }}" placeholder="customer@example.com" required>
                        <div class="form-text">Pre-filled from Customer Master. You can edit if needed.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ri ri-mail-send-line me-1"></i> Send Notification Email Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

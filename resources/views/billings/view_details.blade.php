@extends('layouts.common')
@section('title', 'View Billing - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-primary mb-1">Billing Details</h3>
                    <p class="text-muted small mb-0">View detailed information for bill #{{ $billing->bill_no }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ url('billing/download/'.$billing->id) }}" class="btn btn-primary d-flex align-items-center" target="_blank">
                        <i class="ri ri-download-line me-1"></i> Download
                    </a>
                    <a href="{{ url('billing/print/'.$billing->id) }}" class="btn btn-primary d-flex align-items-center" target="_blank">
                        <i class="ri ri-printer-line me-1"></i> Print
                    </a>
                    <a href="{{ url('billing') }}" class="btn btn-outline-secondary d-flex align-items-center">
                        <i class="ri ri-arrow-left-line me-1"></i> Back
                    </a>
                </div>
            </div>

            <!-- Main Billing Card -->
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-light py-3" style="border-radius: 12px 12px 0 0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-dark mb-0 fw-bold">Billing Information</h5>
                        <span class="badge bg-white text-dark px-3 py-2 border border-{{ 
                            $billing->status == 'Paid' ? 'success' : 
                            ($billing->status == 'Pending' ? 'warning' : 
                            ($billing->status == 'Cancelled' ? 'danger' : 'info')) 
                        }}">
                            <i class="ri-checkbox-blank-circle-fill me-1 small text-{{ 
                                $billing->status == 'Paid' ? 'success' : 
                                ($billing->status == 'Pending' ? 'warning' : 
                                ($billing->status == 'Cancelled' ? 'danger' : 'info')) 
                            }}"></i>
                            STATUS: {{ $billing->status }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-5">
                        <div class="col-md-4">
                            <div class="mb-1 text-muted small fw-bold text-uppercase ls-1">Billing Name</div>
                            <div class="h5 fw-bold text-dark mb-0">{{ $billing->billing_name }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-1 text-muted small fw-bold text-uppercase ls-1">Bill Number</div>
                            <div class="h5 fw-bold text-dark mb-0">{{ $billing->bill_no }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-1 text-muted small fw-bold text-uppercase ls-1">Bill Type</div>
                            <div class="h5 fw-bold text-primary mb-0">{{ $billing->billing_type }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-1 text-muted small fw-bold text-uppercase ls-1">Bill Date</div>
                            <div class="h5 fw-bold text-dark mb-0">{{ $billing->bill_date->format('d M, Y') }}</div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-1 text-muted small fw-bold text-uppercase ls-1">Total Amount</div>
                            <div class="h3 fw-bold text-success mb-0">₹{{ number_format($billing->amount, 2) }}</div>
                        </div>

                        <div class="col-lg-8 border-start ps-lg-5">
                            <div class="mb-1 text-muted small fw-bold text-uppercase ls-1">Reason / Description</div>
                            <div class="text-dark bg-light p-3 rounded" style="min-height: 100px; white-space: pre-line; border-left: 4px solid #dee2e6;">
                                {{ $billing->reason ?? 'No description provided.' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .ls-1 { letter-spacing: 0.5px; }
    .badge { font-weight: 600; letter-spacing: 0.3px; }
</style>
@endsection

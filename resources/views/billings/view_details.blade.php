@extends('layouts.common')
@section('title', 'View Billing - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Billing</h4>
                <div class="d-flex gap-2">
                    <a href="{{ url('billing/print/'.$billing->id) }}" class="btn btn-primary" target="_blank">
                        <i class="ri ri-printer-line back-arrow"></i>Print
                    </a>
                    <a href="{{ url('billing/download/'.$billing->id) }}" class="btn btn-primary" target="_blank">
                        <i class="ri ri-download-line back-arrow"></i>Download
                    </a>
                    <a href="{{ url('billing') }}" class="btn btn-secondary">
                        <i class="ri ri-arrow-left-line back-arrow"></i>Back
                    </a>
                </div>
            </div>
            <div class="card detail-card mt-3">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="detail-title">Bill Number:</label>
                            <div class="text-muted">{{ $billing->bill_no }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Bill Type:</label>
                            <div class="text-muted">{{ $billing->billing_type }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Bill Date:</label>
                            <div class="text-muted">{{ $billing->bill_date->format('d-m-Y') }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Amount:</label>
                            <div class="text-muted">₹{{ number_format($billing->amount, 2) }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Status:</label>
                            <div>
                                <span class="badge bg-{{ 
                                    $billing->status == 'Paid' ? 'success' : 
                                    ($billing->status == 'Pending' ? 'warning' : 
                                    ($billing->status == 'Cancelled' ? 'danger' : 'info')) 
                                }}">
                                    {{ $billing->status }}
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <label class="detail-title">Reason:</label>
                            <div class="text-muted">{{ $billing->reason ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
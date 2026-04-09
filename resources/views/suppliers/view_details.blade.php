@extends('layouts.common')
@section('title', 'Supplier Details - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row g-4">
        <!-- Action Header -->
        <div class="col-12">
            <div class="table-header-box d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Supplier Profile</h4>
                <div class="d-flex gap-2">
                    <a href="{{ url('suppliers') }}" class="btn btn-secondary">
                        <i class="ri ri-arrow-left-line me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <!-- Left Sidebar: Profile & Primary Info -->
        <div class="col-xl-4 col-lg-5 col-md-5">
            <!-- Profile Card -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body pt-5">
                    <div class="user-avatar-section mb-4">
                        <div class="d-flex align-items-center flex-column">
                            <div class="avatar avatar-xl bg-label-primary rounded-circle mb-3 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 40px; background-color: #ede4ff; color: #8c57ff;">
                                {{ substr($supplier->name, 0, 1) }}
                            </div>
                            <div class="user-info text-center">
                                <h4 class="mb-2">{{ $supplier->name }}</h4>
                                <div class="d-flex flex-wrap gap-2 justify-content-center">
                                    <span class="badge bg-label-info">{{ $supplier->code }}</span>
                                    <span class="badge {{ $supplier->status == 'Active' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $supplier->status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h6 class="pb-2 border-bottom mb-3 text-uppercase fw-semibold small text-muted">Business Summary</h6>
                    <div class="info-container">
                        <ul class="list-unstyled">
                            <li class="mb-3 d-flex align-items-center">
                                <i class="ri ri-mail-line me-2 text-primary"></i>
                                <span class="text-dark">{{ $supplier->email ?? '-' }}</span>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i class="ri ri-smartphone-line me-2 text-primary"></i>
                                <span class="text-dark">{{ $supplier->mobile_no ?? '-' }}</span>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i class="ri ri-global-line me-2 text-primary"></i>
                                <span class="text-dark">@if($supplier->website_url) <a href="{{ $supplier->website_url }}" target="_blank">Website</a> @else - @endif</span>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i class="ri ri-store-2-line me-2 text-primary"></i>
                                <span class="text-dark">{{ $supplier->storeType->store_type_name ?? '-' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Contact Person Card -->
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom bg-transparent py-3">
                    <h5 class="card-title mb-0 fs-6"><i class="ri ri-contacts-line me-2 text-primary"></i>Primary Contact</h5>
                </div>
                <div class="card-body pt-4">
                    <div class="d-flex mb-4">
                        <div class="avatar avatar-md bg-label-success rounded me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: #e4f7d6; color: #56ca00;">
                            <i class="ri ri-user-star-line"></i>
                        </div>
                        <div>
                            <span class="fw-bold d-block text-muted small text-uppercase">Full Name</span>
                            <span class="text-dark fw-semibold">{{ $supplier->contact_person_name ?? '-' }}</span>
                            <small class="d-block text-muted small">{{ $supplier->designation ?? '-' }}</small>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2 small">
                            <span class="text-muted">Mobile:</span> <span class="fw-medium text-dark ms-1">{{ $supplier->contact_mobile_no ?? '-' }}</span>
                        </li>
                        <li class="small">
                            <span class="text-muted">Email:</span> <span class="fw-medium text-dark ms-1">{{ $supplier->contact_email ?? '-' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Right Side: Detailed Details -->
        <div class="col-xl-8 col-lg-7 col-md-7">
            <!-- Location Details -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header border-bottom bg-transparent py-3">
                    <h5 class="card-title mb-0"><i class="ri ri-map-pin-line me-2 text-primary"></i>Location & Address</h5>
                </div>
                <div class="card-body pt-4">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <span class="fw-bold d-block text-muted small text-uppercase mb-2">Office Address</span>
                            <p class="text-dark mb-1 h6 fw-normal">{{ $supplier->address_line_1 }}</p>
                            @if($supplier->address_line_2) <p class="text-dark mb-1 h6 fw-normal">{{ $supplier->address_line_2 }}</p> @endif
                            @if($supplier->address_line_3) <p class="text-dark mb-3 h6 fw-normal">{{ $supplier->address_line_3 }}</p> @endif
                            <div class="mt-2 d-flex gap-4">
                                <div><small class="text-muted d-block">City</small><strong>{{ $supplier->city->city_name ?? '-' }}</strong></div>
                                <div><small class="text-muted d-block">State</small><strong>{{ $supplier->state->state_name ?? '-' }}</strong></div>
                                <div><small class="text-muted d-block">Zip</small><strong>{{ $supplier->zip_code ?? '-' }}</strong></div>
                            </div>
                        </div>
                        <div class="col-md-5 border-start ps-md-4">
                            <span class="fw-bold d-block text-muted small text-uppercase mb-2">Logistics</span>
                            <div class="mb-3">
                                <small class="text-muted d-block">Transport Name</small>
                                <span class="text-dark fw-medium">{{ $supplier->transport_name ?? '-' }}</span>
                            </div>
                            <div>
                                <small class="text-muted d-block">Booking Area</small>
                                <span class="text-dark fw-medium">{{ $supplier->booking_area ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial & Compliance -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header border-bottom bg-transparent py-3">
                    <h5 class="card-title mb-0"><i class="ri ri-shield-check-line me-2 text-primary"></i>Financial & Compliance</h5>
                </div>
                <div class="card-body pt-4">
                    <div class="row g-4">
                        <!-- Identifiers -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light bg-opacity-50 mb-3">
                                <span class="fw-bold d-block text-muted small text-uppercase mb-1">GST Number</span>
                                <span class="text-dark fw-bold h6">{{ $supplier->gst_no ?? '-' }}</span>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-4">
                                    <div class="p-2 border rounded bg-light bg-opacity-50 text-center">
                                        <small class="fw-bold d-block text-muted text-uppercase mb-0" style="font-size: 0.6rem;">IGST %</small>
                                        <span class="text-dark fw-bold small">{{ $supplier->igst_percent ?? '0' }}%</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 border rounded bg-light bg-opacity-50 text-center">
                                        <small class="fw-bold d-block text-muted text-uppercase mb-0" style="font-size: 0.6rem;">CGST %</small>
                                        <span class="text-dark fw-bold small">{{ $supplier->cgst_percent ?? '0' }}%</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 border rounded bg-light bg-opacity-50 text-center">
                                        <small class="fw-bold d-block text-muted text-uppercase mb-0" style="font-size: 0.6rem;">SGST %</small>
                                        <span class="text-dark fw-bold small">{{ $supplier->sgst_percent ?? '0' }}%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-3 border rounded bg-light bg-opacity-50">
                                <span class="fw-bold d-block text-muted small text-uppercase mb-1">PAN Number</span>
                                <span class="text-dark fw-bold h6">{{ $supplier->pan_no ?? '-' }}</span>
                            </div>
                        </div>
                        <!-- Terms & Limits -->
                        <div class="col-md-6 border-start ps-md-4">
                            <div class="mb-3 d-flex justify-content-between pb-2 border-bottom">
                                <span class="text-muted">Tax Type</span>
                                <span class="badge bg-label-primary">@if($supplier->taxType) {{ $supplier->taxType->item_name }} ({{ $supplier->taxType->tax_rate }}%) @else - @endif</span>
                            </div>
                            <div class="mb-3 d-flex justify-content-between pb-2 border-bottom">
                                <span class="text-muted">Credit Limit</span>
                                <span class="fw-bold text-danger">₹ {{ number_format($supplier->credit_limit, 2) }}</span>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted d-block">Payment Terms</small>
                                <span class="text-success fw-bold">{{ $supplier->payment_terms ?? '-' }}</span>
                            </div>
                        </div>
                        <!-- Commission -->
                        <div class="col-12 mt-4 pt-3 border-top">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Commission Agent</small>
                                    <h6 class="mb-0">{{ $supplier->purchaseCommissionAgent->name ?? '-' }} <span class="text-muted small">({{ $supplier->purchaseCommissionAgent->code ?? '-' }})</span></h6>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">Commission %</small>
                                    <span class="badge bg-label-success">{{ $supplier->commission_percentage ? $supplier->commission_percentage . '%' : '0%' }}</span>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted d-block">ECC No.</small>
                                    <span class="text-dark">{{ $supplier->ecc_no ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bank Details -->
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom bg-transparent py-3">
                    <h5 class="card-title mb-0"><i class="ri ri-bank-card-line me-2 text-primary"></i>Banking Information</h5>
                </div>
                <div class="card-body pt-4">
                    <div class="row g-4 d-flex align-items-center">
                        <div class="col-md-6">
                            <div class="p-3 border-start border-primary border-4 bg-light rounded">
                                <small class="text-muted d-block text-uppercase small fw-bold mb-1">Account Connection</small>
                                <h5 class="mb-1 fw-bold">{{ $supplier->bank_name ?? '-' }}</h5>
                                <small class="text-muted">{{ $supplier->branch ?? '-' }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-7">
                                    <small class="text-muted d-block mb-1">Account Number</small>
                                    <code class="text-primary fs-5 fw-bold">{{ $supplier->account_number ?? '-' }}</code>
                                </div>
                                <div class="col-5">
                                    <small class="text-muted d-block mb-1">IFSC Code</small>
                                    <span class="badge bg-label-info px-3">{{ $supplier->ifsc_code ?? '-' }}</span>
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

@extends('layouts.common')
@section('title', 'Customer Details - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box mb-4">
                <h4 class="mb-0">Customer Details</h4>
                <a href="{{ url('customers') }}" class="btn btn-secondary">
                    <i class="ri ri ri-arrow-left-line"></i> Back
                </a>
            </div>

            <!-- Profile Header Card -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center flex-column flex-sm-row gap-2">
                        <div class="text-center text-sm-start">
                            <h3 class="mb-1 text-primary">{{ $customer->name }}</h3>
                            <p class="text-muted mb-2 fw-medium">
                                <i class="ri ri-hashtag"></i> {{ $customer->code }} | 
                                <span class="badge bg-label-info">{{ $customer->category }}</span>
                            </p>
                            <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-sm-start align-items-center">
                                <span class="badge {{ $customer->status == 'Active' ? 'bg-success' : 'bg-danger' }} d-flex align-items-center">
                                    <i class="ri ri-checkbox-circle-line me-1"></i> {{ $customer->status }}
                                </span>
                                <span class="badge bg-label-secondary d-flex align-items-center">
                                    <i class="ri ri-map-pin-line me-1"></i> {{ $customer->city->city_name ?? '-' }}, {{ $customer->state->state_name ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Business Details -->
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header border-bottom bg-transparent py-3">
                            <h5 class="card-title mb-0"><i class="ri ri-building-line me-2 text-primary"></i>Business Info</h5>
                        </div>
                        <div class="card-body pt-4">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase mb-1">Email Address</span>
                                    <span class="text-dark">{{ $customer->email ?? '-' }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase mb-1">Mobile Number</span>
                                    <span class="text-dark">{{ $customer->mobile_no ?? '-' }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase mb-1">Website URL</span>
                                    <span class="text-dark">@if($customer->website_url) <a href="{{ $customer->website_url }}" target="_blank">{{ $customer->website_url }}</a> @else - @endif</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase mb-1">Zone</span>
                                    <span class="text-dark">{{ $customer->zone->zone_name ?? '-' }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase mb-1">Store / Outlet</span>
                                    <span class="text-dark">{{ $customer->storeType->store_type_name ?? '-' }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase mb-1">Transport Name</span>
                                    <span class="text-dark">{{ $customer->transport_name ?? '-' }}</span>
                                </li>
                                <li>
                                    <span class="fw-bold d-block text-muted small text-uppercase mb-1">Booking Office</span>
                                    <span class="text-dark">{{ $customer->booking_office ?? '-' }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Location Information -->
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header border-bottom bg-transparent py-3">
                            <h5 class="card-title mb-0"><i class="ri ri-map-pin-user-line me-2 text-primary"></i>Location & Address</h5>
                        </div>
                        <div class="card-body pt-4">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase mb-1">State</span>
                                    <span class="text-dark">{{ $customer->state->state_name ?? '-' }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase mb-1">City</span>
                                    <span class="text-dark">{{ $customer->city->city_name ?? '-' }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase mb-1">Place</span>
                                    <span class="text-dark">{{ $customer->place->place_name ?? '-' }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase mb-1">Address</span>
                                    <span class="text-dark">
                                        {{ $customer->address_line_1 }}<br>
                                        @if($customer->address_line_2) {{ $customer->address_line_2 }} <br> @endif
                                        @if($customer->address_line_3) {{ $customer->address_line_3 }} <br> @endif
                                    </span>
                                </li>
                                <li>
                                    <span class="fw-bold d-block text-muted small text-uppercase mb-1">Zip Code</span>
                                    <span class="text-dark fw-bold">{{ $customer->zip_code ?? '-' }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Contact Person Information -->
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header border-bottom bg-transparent py-3">
                            <h5 class="card-title mb-0"><i class="ri ri-contacts-line me-2 text-primary"></i>Contact Person</h5>
                        </div>
                        <div class="card-body pt-4">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-4 d-flex align-items-center">
                                    <div class="avatar avatar-md bg-label-info rounded me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                        <i class="ri ri-user-received-2-line"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold d-block text-muted small text-uppercase">Full Name</span>
                                        <span class="text-dark fw-semibold">{{ $customer->contact_person_name ?? '-' }}</span>
                                    </div>
                                </li>
                                <li class="mb-4 d-flex align-items-center">
                                    <div class="avatar avatar-md bg-label-warning rounded me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                        <i class="ri ri-medal-line"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold d-block text-muted small text-uppercase">Designation</span>
                                        <span class="text-dark fw-semibold">{{ $customer->designation ?? '-' }}</span>
                                    </div>
                                </li>
                                <li class="mb-4 d-flex align-items-center">
                                    <div class="avatar avatar-md bg-label-success rounded me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                        <i class="ri ri-smartphone-line"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold d-block text-muted small text-uppercase">Mobile Number</span>
                                        <span class="text-dark fw-semibold">{{ $customer->contact_mobile_no ?? '-' }}</span>
                                    </div>
                                </li>
                                <li class="d-flex align-items-center">
                                    <div class="avatar avatar-md bg-label-danger rounded me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                        <i class="ri ri-mail-send-line"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold d-block text-muted small text-uppercase">Email Address</span>
                                        <span class="text-dark fw-semibold">{{ $customer->contact_email ?? '-' }}</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Financial & Compliance -->
                <div class="col-md-8 col-xl-8">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header border-bottom bg-transparent py-3">
                            <h5 class="card-title mb-0"><i class="ri ri-shield-check-line me-2 text-primary"></i>Financial & Compliance</h5>
                        </div>
                        <div class="card-body pt-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-light bg-opacity-50">
                                        <span class="fw-bold d-block text-muted small text-uppercase mb-1">GST Number</span>
                                        <span class="text-dark fw-bold h6">{{ $customer->gst_no ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-light bg-opacity-50">
                                        <span class="fw-bold d-block text-muted small text-uppercase mb-1">PAN Number</span>
                                        <span class="text-dark fw-bold h6">{{ $customer->pan_no ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <span class="fw-bold d-block text-muted small text-uppercase mb-1">Tax Type</span>
                                    <span class="text-dark">
                                        @if($customer->tax)
                                            <span class="badge bg-label-primary">{{ $customer->tax->item_name }} ({{ $customer->tax->tax_rate }}%)</span>
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                                <div class="col-md-4">
                                    <span class="fw-bold d-block text-muted small text-uppercase mb-1">Credit Limit</span>
                                    <span class="text-dark fw-bold">₹ {{ number_format($customer->credit_limit, 2) }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="fw-bold d-block text-muted small text-uppercase mb-1">Payment Terms</span>
                                    <span class="text-success fw-bold">{{ $customer->payment_terms ?? '-' }}</span>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                                        <span class="text-muted">Sales Discount</span>
                                        <span class="fw-bold text-primary">{{ $customer->sales_discount ? $customer->sales_discount . '%' : '0%' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                                        <p class="mb-0 text-muted">Box Discount Amount (Per PCS)</p>
                                        <h6 class="mb-0">
                                            <span class="fw-bold text-primary">₹ {{ $customer->box_discount_amount ?: '0.00' }}</span>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank Information -->
                <div class="col-md-4 col-xl-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header border-bottom bg-transparent py-3">
                            <h5 class="card-title mb-0"><i class="ri ri-bank-card-line me-2 text-primary"></i>Bank Details</h5>
                        </div>
                        <div class="card-body pt-4">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase mb-1">Bank Name</span>
                                    <span class="text-dark fw-semibold h6 mb-0">{{ $customer->bank_name ?? '-' }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase mb-1">Branch</span>
                                    <span class="text-dark fw-semibold">{{ $customer->branch ?? '-' }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase mb-1">Account Number</span>
                                    <code class="text-primary fw-bold" style="font-size: 1.1rem;">{{ $customer->account_number ?? '-' }}</code>
                                </li>
                                <li>
                                    <span class="fw-bold d-block text-muted small text-uppercase mb-1">IFSC Code</span>
                                    <span class="badge bg-white text-primary border border-primary fw-bold px-3">{{ $customer->ifsc_code ?? '-' }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@extends('layouts.common')
@section('title', 'Purchase Commission Agent Details - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box mb-4">
                <h4 class="mb-0">Purchase Commission Agent Details</h4>
                <a href="{{ url('purchase_commission_agent') }}" class="btn btn-secondary">
                    <i class="ri ri-arrow-left-line"></i> Back
                </a>
            </div>

            <!-- Profile Header Card -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center flex-column flex-sm-row gap-2">
                        <div class="text-center text-sm-start">
                            <h3 class="mb-1 text-primary">{{ $agent->name }}</h3>
                            <p class="text-muted mb-2 fw-medium">
                                <i class="ri ri-hashtag"></i> {{ $agent->code }} | 
                                <span class="badge bg-label-info">Purchase Commission Agent</span>
                            </p>
                            <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-sm-start">
                                <span class="badge {{ $agent->status == 'Active' ? 'bg-success' : 'bg-danger' }}">
                                    <i class="ri ri-checkbox-circle-line me-1"></i> {{ $agent->status }}
                                </span>
                                <span class="badge bg-label-secondary">
                                    <i class="ri ri-map-pin-line me-1"></i> {{ $agent->city->city_name ?? '-' }}, {{ $agent->state->state_name ?? '-' }}
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
                                    <span class="text-dark">{{ $agent->email ?? '-' }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase mb-1">Mobile Number</span>
                                    <span class="text-dark">{{ $agent->mobile_no ?? '-' }}</span>
                                </li>
                                <li>
                                    <span class="fw-bold d-block text-muted small text-uppercase mb-1">Status</span>
                                    <span class="badge {{ $agent->status == 'Active' ? 'bg-label-success' : 'bg-label-danger' }}">{{ $agent->status }}</span>
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
                                    <span class="text-dark">{{ $agent->state->state_name ?? '-' }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase mb-1">City</span>
                                    <span class="text-dark">{{ $agent->city->city_name ?? '-' }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase mb-1">Place</span>
                                    <span class="text-dark">{{ $agent->servicePoint->place_name ?? '-' }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold d-block text-muted small text-uppercase mb-1">Address</span>
                                    <span class="text-dark">
                                        {{ $agent->address_line_1 }}<br>
                                        @if($agent->address_line_2) {{ $agent->address_line_2 }} <br> @endif
                                    </span>
                                </li>
                                <li>
                                    <span class="fw-bold d-block text-muted small text-uppercase mb-1">Zip Code</span>
                                    <span class="text-dark fw-bold">{{ $agent->zipcode ?? '-' }}</span>
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
                                        <span class="text-dark fw-semibold">{{ $agent->contact_person_name ?? '-' }}</span>
                                    </div>
                                </li>
                                <li class="mb-4 d-flex align-items-center">
                                    <div class="avatar avatar-md bg-label-warning rounded me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                        <i class="ri ri-medal-line"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold d-block text-muted small text-uppercase">Designation</span>
                                        <span class="text-dark fw-semibold">{{ $agent->designation ?? '-' }}</span>
                                    </div>
                                </li>
                                <li class="mb-4 d-flex align-items-center">
                                    <div class="avatar avatar-md bg-label-success rounded me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                        <i class="ri ri-smartphone-line"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold d-block text-muted small text-uppercase">Phone Number</span>
                                        <span class="text-dark fw-semibold">{{ $agent->phone_number ?? '-' }}</span>
                                    </div>
                                </li>
                                <li class="d-flex align-items-center">
                                    <div class="avatar avatar-md bg-label-danger rounded me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                        <i class="ri ri-mail-send-line"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold d-block text-muted small text-uppercase">Email Address</span>
                                        <span class="text-dark fw-semibold">{{ $agent->contact_email ?? '-' }}</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Financial & Compliance -->
                <div class="col-md-12 col-xl-12">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header border-bottom bg-transparent py-3">
                            <h5 class="card-title mb-0"><i class="ri ri-shield-check-line me-2 text-primary"></i>Financial & Compliance</h5>
                        </div>
                        <div class="card-body pt-4">
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <div class="p-3 border rounded bg-light bg-opacity-50">
                                        <span class="fw-bold d-block text-muted small text-uppercase mb-1">GST Number</span>
                                        <span class="text-dark fw-bold h6">{{ $agent->gst_no ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 border rounded bg-light bg-opacity-50">
                                        <span class="fw-bold d-block text-muted small text-uppercase mb-1">PAN Number</span>
                                        <span class="text-dark fw-bold h6">{{ $agent->pan_no ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 border rounded bg-light bg-opacity-50">
                                        <span class="fw-bold d-block text-muted small text-uppercase mb-1">Remarks</span>
                                        <span class="text-dark">{{ $agent->remarks ?? '-' }}</span>
                                    </div>
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
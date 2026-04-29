@extends('layouts.common')
@section('title', 'Retailer Details - ' . env('WEBSITE_NAME'))
@section('content')
    <div class="container-xxl section-padding">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-5">
                        <div class="d-flex align-items-center justify-content-between mb-5 border-bottom pb-4">
                            <h4 class="fw-bold mb-0 text-primary">Retailer Details: {{ $retailer->name }}</h4>
                            <div class="d-flex gap-2">
                                <a href="{{ url('retailers/add/' . $retailer->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    <i class="ri ri-edit-line me-1"></i> Edit
                                </a>
                                <a href="{{ url('retailers') }}" class="btn btn-primary btn-sm rounded-pill px-3">
                                    <i class="ri ri-arrow-left-line me-1"></i> Back to List
                                </a>
                            </div>
                        </div>

                        <div class="row g-5">
                            <!-- Basic Information -->
                            <div class="col-md-6 col-lg-4">
                                <div class="detail-group">
                                    <label class="detail-title small fw-bold text-muted text-uppercase mb-2 d-block">Basic Information</label>
                                    <div class="detail-content bg-light rounded-3 p-4">
                                        <div class="mb-3">
                                            <label class="text-muted small d-block">Name</label>
                                            <span class="fw-bold text-dark">{{ $retailer->name }}</span>
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-muted small d-block">Code</label>
                                            <span class="fw-bold text-dark">{{ $retailer->code }}</span>
                                        </div>
                                        <div>
                                            <label class="text-muted small d-block">Status</label>
                                            <span class="badge {{ $retailer->status == 'Active' ? 'bg-label-success' : 'bg-label-danger' }} rounded-pill">
                                                {{ $retailer->status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="col-md-6 col-lg-4">
                                <div class="detail-group">
                                    <label class="detail-title small fw-bold text-muted text-uppercase mb-2 d-block">Contact Details</label>
                                    <div class="detail-content bg-light rounded-3 p-4">
                                        <div class="mb-3">
                                            <label class="text-muted small d-block">Mobile Number</label>
                                            <span class="fw-bold text-dark">{{ $retailer->mobile_number ?? '-' }}</span>
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-muted small d-block">Email Address</label>
                                            <span class="fw-bold text-dark">{{ $retailer->email ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Location Information -->
                            <div class="col-md-12 col-lg-4">
                                <div class="detail-group">
                                    <label class="detail-title small fw-bold text-muted text-uppercase mb-2 d-block">Location Details</label>
                                    <div class="detail-content bg-light rounded-3 p-4">
                                        <div class="row">
                                            <div class="col-6 mb-3">
                                                <label class="text-muted small d-block">State</label>
                                                <span class="fw-bold text-dark">{{ $retailer->state->state_name ?? '-' }}</span>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label class="text-muted small d-block">City</label>
                                                <span class="fw-bold text-dark">{{ $retailer->city->city_name ?? '-' }}</span>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label class="text-muted small d-block">Place</label>
                                                <span class="fw-bold text-dark">{{ $retailer->place->place_name ?? '-' }}</span>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label class="text-muted small d-block">Zone</label>
                                                <span class="fw-bold text-dark">{{ $retailer->zone->zone_name ?? '-' }}</span>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="text-muted small d-block">Address</label>
                                                <span class="fw-bold text-dark d-block">{{ $retailer->address_line_1 }}</span>
                                                <span class="text-muted d-block">{{ $retailer->address_line_2 }}</span>
                                            </div>
                                            <div class="col-6">
                                                <label class="text-muted small d-block">Zip Code</label>
                                                <span class="fw-bold text-dark">{{ $retailer->zipcode ?? '-' }}</span>
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
    </div>
@endsection

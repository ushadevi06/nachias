@extends('layouts.common')
@section('title', 'View Sales Agent - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Sales Agent - {{ $salesAgent->name }} ({{ $salesAgent->code }})</h4>
                <a href="{{ url('sales_agents') }}" class="btn btn-primary">
                    <i class="ri ri-arrow-left-line back-arrow"></i> Back
                </a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <h6>Identification & Contact:</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Sales Agent Type: </label>
                            <div class="text-muted">{{ $salesAgent->agent_type }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Name:</label>
                            <div class="text-muted">{{ $salesAgent->name }} ({{ $salesAgent->code }})</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Email:</label>
                            <div class="text-muted">{{ $salesAgent->email ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Mobile Number:</label>
                            <div class="text-muted">{{ $salesAgent->mobile_no ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Status:</label>
                            <div class="{{ $salesAgent->status == 'Active' ? 'text-success' : 'text-danger' }}">
                                {{ $salesAgent->status }}
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Location Details:</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">State:</label>
                            <div class="text-muted">{{ $salesAgent->state->state_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">City:</label>
                            <div class="text-muted">{{ $salesAgent->city->city_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Service Point:</label>
                            <div class="text-muted">{{ $salesAgent->place->place_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Address Line 1:</label>
                            <div class="text-muted">{{ $salesAgent->address_line_1 ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Address Line 2:</label>
                            <div class="text-muted">{{ $salesAgent->address_line_2 ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Zip Code:</label>
                            <div class="text-muted">{{ $salesAgent->zip_code ?? '-' }}</div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Contact Information:</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Contact Person Name:</label>
                            <div class="text-muted">{{ $salesAgent->contact_person_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Designation:</label>
                            <div class="text-muted">{{ $salesAgent->designation ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Contact Phone Number:</label>
                            <div class="text-muted">{{ $salesAgent->contact_phone_number ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Contact Email:</label>
                            <div class="text-muted">{{ $salesAgent->contact_email ?? '-' }}</div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Tax & Compliance:</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">PAN No:</label>
                            <div class="text-muted">{{ $salesAgent->pan_no ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">GST No:</label>
                            <div class="text-muted">{{ $salesAgent->gst_no ?? '-' }}</div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Performance Targets:</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Commission Value:</label>
                            <div class="text-muted">
                                {{ $salesAgent->commission_value ? $salesAgent->commission_value . '%' : '-' }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Sales Target:</label>
                            <div class="text-muted">
                                {{ $salesAgent->sales_target ? '₹' . number_format($salesAgent->sales_target, 2) : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
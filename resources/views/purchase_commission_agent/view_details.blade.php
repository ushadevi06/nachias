@extends('layouts.common')
@section('title', 'View Purchase Commission Agent - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Purchase Commission Agent</h4>
                <a href="{{ url('purchase_commission_agent') }}" class="btn btn-primary"><i
                        class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <h6>Identification & Contact:</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Sales Agent Type: </label>
                            <div class="text-muted">Commission Agent</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Name:</label>
                            <div class="text-muted">{{ $agent->name }} <span
                                    class="mini-title">({{ $agent->code }})</span></div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Email:</label>
                            <div class="text-muted">{{ $agent->email ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Mobile Number:</label>
                            <div class="text-muted">{{ $agent->mobile_no ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Status:</label>
                            <div class="{{ $agent->status == 'Active' ? 'text-success' : 'text-muted' }}">
                                {{ $agent->status }}
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
                            <div class="text-muted">{{ $agent->state->state_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">City:</label>
                            <div class="text-muted">{{ $agent->city->city_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Place:</label>
                            <div class="text-muted">{{ $agent->servicePoint->place_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Zip Code:</label>
                            <div class="text-muted">{{ $agent->zipcode ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="detail-title">Address Line 1:</label>
                            <div class="text-muted">{{ $agent->address_line_1 ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="detail-title">Address Line 2:</label>
                            <div class="text-muted">{{ $agent->address_line_2 ?? '-' }}</div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Contact Information:</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Contact Person Name:</label>
                            <div class="text-muted">{{ $agent->contact_person_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Designation:</label>
                            <div class="text-muted">{{ $agent->designation ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Phone Number:</label>
                            <div class="text-muted">{{ $agent->phone_number ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Email:</label>
                            <div class="text-muted">{{ $agent->contact_email ?? '-' }}</div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Tax & Compliance:</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">PAN NO:</label>
                            <div class="text-muted">{{ $agent->pan_no ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">GST No:</label>
                            <div class="text-muted">{{ $agent->gst_no ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="detail-title">Remarks:</label>
                            <div class="text-muted">{{ $agent->remarks ?? '-' }}</div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
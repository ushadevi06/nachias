@extends('layouts.common')
@section('title', 'View Customer - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Customer</h4>
                <a href="{{ url('customers') }}" class="btn btn-primary"><i
                        class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <h6>Identification & Contact:</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Category: </label>
                            <div class="text-muted">{{ $customer->category }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Customer Name:</label>
                            <div class="text-muted">{{ $customer->name }} <strong>({{ $customer->code }})</strong></div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Mobile Number:</label>
                            <div class="text-muted">{{ $customer->mobile_no }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Email:</label>
                            <div class="text-muted">{{ $customer->email ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Website URL:</label>
                            <div class="text-muted">{{ $customer->website_url ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Transport Name:</label>
                            <div class="text-muted">{{ $customer->transport_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Booking Office:</label>
                            <div class="text-muted">{{ $customer->booking_office ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Zone:</label>
                            <div class="text-muted">{{ $customer->zone->zone_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Stores:</label>
                            <div class="text-muted">{{ $customer->stores ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Status:</label>
                            <div class="{{ $customer->status === 'Active' ? 'text-success' : 'text-danger' }}">
                                {{ $customer->status }}</div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Location Information:</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">State:</label>
                            <div class="text-muted">{{ $customer->state->state_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">City:</label>
                            <div class="text-muted">{{ $customer->city->city_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Place:</label>
                            <div class="text-muted">{{ $customer->place->place_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Address Line 1:</label>
                            <div class="text-muted">{{ $customer->address_line_1 }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Address Line 2:</label>
                            <div class="text-muted">{{ $customer->address_line_2 ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Address Line 3:</label>
                            <div class="text-muted">{{ $customer->address_line_3 ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Zip Code:</label>
                            <div class="text-muted">{{ $customer->zip_code ?? '-' }}</div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Contact Information:</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Contact Person Name:</label>
                            <div class="text-muted">{{ $customer->contact_person_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Designation:</label>
                            <div class="text-muted">{{ $customer->designation ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Mobile Number:</label>
                            <div class="text-muted">{{ $customer->contact_mobile_no ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Email:</label>
                            <div class="text-muted">{{ $customer->contact_email ?? '-' }}</div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Tax & Compliance:</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">GST No:</label>
                            <div class="text-muted">{{ $customer->gst_no ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Tax Type:</label>
                            <div class="text-muted">
                                @if($customer->taxType)
                                {{ $customer->taxType->item_name }} ({{ $customer->taxType->tax_rate }}%)
                                @else
                                -
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">PAN NO:</label>
                            <div class="text-muted">{{ $customer->pan_no ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Credit Limit:</label>
                            <div class="text-muted">₹{{ number_format($customer->credit_limit, 2) }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Payment Terms:</label>
                            <div class="text-muted text-success">{{ $customer->payment_terms ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Sales Discount (%):</label>
                            <div class="text-muted">
                                {{ $customer->sales_discount ? $customer->sales_discount . '%' : '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Box Discount (%):</label>
                            <div class="text-muted">{{ $customer->box_discount ? $customer->box_discount . '%' : '-' }}
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Bank Information:</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Bank Name:</label>
                            <div class="text-muted">{{ $customer->bank_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Branch:</label>
                            <div class="text-muted">{{ $customer->branch ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Account Number:</label>
                            <div class="text-muted">{{ $customer->account_number ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">IFSC Code:</label>
                            <div class="text-muted">{{ $customer->ifsc_code ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
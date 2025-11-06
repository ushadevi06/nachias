@extends('layouts.common')
@section('title', 'View Service Provider - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Service Provider</h4>
                <a href="{{ url('service_providers') }}" class="btn btn-primary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <h6>Identification & Contact:</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Service Type: </label>
                            <div class="text-muted">Transport</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Name:</label>
                            <div class="text-muted">Udaan Road Ways Pvt Ltd(SP001)</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Email:</label>
                            <div class="text-muted">udanroad@gmail.com</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Mobile Number:</label>
                            <div class="text-muted">9876543210</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Telephone:</label>
                            <div class="text-muted">048-343-3244</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Zip Code:</label>
                            <div class="text-muted">625011</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Website URL:</label>
                            <div class="text-muted">-</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Service Rate:</label>
                            <div class="text-muted">Per Agent</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Status:</label>
                            <div class="text-success">Active</div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Location Details:</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Country:</label>
                            <div class="text-muted">India</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">State:</label>
                            <div class="text-muted">Tamil Nadu</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">City:</label>
                            <div class="text-muted">Madurai</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Place:</label>
                            <div class="text-muted">Arapalayam</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Address Line 1:</label>
                            <div class="text-muted">No. 12, South Masi Street</div>
                        </div> 
                        <div class="col-md-3">
                            <label class="detail-title">Address Line 2:</label>
                            <div class="text-muted">Near Meenakshi Temple</div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Contact Information:</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Contact Person Name:</label>
                            <div class="text-muted">Raj</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Designation:</label>
                            <div class="text-muted">Store Manager</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Alternate Phone Number:</label>
                            <div class="text-muted">7410258963</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Alternate Email:</label>
                            <div class="text-muted">-</div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Tax & Compliance:</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">PAN NO:</label>
                            <div class="text-muted">AAAPH1234C</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">GST No:</label>
                            <div class="text-muted">33AAAPH1234C1Z5</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">ECC No:</label>
                            <div class="text-muted">MAD1234567</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Remarks:</label>
                            <div class="text-muted text-success">-</div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Financial / Payment:</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Payment Terms:</label>
                            <div class="text-muted">-</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Bank Name:</label>
                            <div class="text-muted">Indian Bank</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Bank Account No:</label>
                            <div class="text-muted">MAD1234567</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">IFSC Code:</label>
                            <div class="text-muted text-success">IB3974DSD3</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
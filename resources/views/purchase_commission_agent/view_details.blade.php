@extends('layouts.common')
@section('title', 'View Purchase Commission Agent - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Purchase Commission Agent</h4>
                <a href="{{ url('purchase_commission_agent') }}" class="btn btn-primary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
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
                            <div class="text-muted">Amit Kumar(PCA101)</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Email:</label>
                            <div class="text-muted">amit@gmail.com</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Mobile Number:</label>
                            <div class="text-muted">9876543210</div>
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
                            <div class="text-muted">75, Murugan Street</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Address Line 1:</label>
                            <div class="text-muted">Madurai, Tamil Nadu.</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Zip Code:</label>
                            <div class="text-muted">625001</div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Contact Information:</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Contact Person Name:</label>
                            <div class="text-muted">Riya</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Designation:</label>
                            <div class="text-muted">Officer</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Phone Number:</label>
                            <div class="text-muted">7410258963</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Email:</label>
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
                            <label class="detail-title">Remarks:</label>
                            <div class="text-muted text-success">-</div>
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

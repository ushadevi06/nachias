@extends('layouts.common')
@section('title', 'View Suppliers - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Suppliers</h4>
                <a href="{{ url('suppliers') }}" class="btn btn-primary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <h6>Identification & Contact:</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Supplier Name:</label>
                            <div class="text-muted">Krishna Fabrics <strong>(SUP001)</strong></div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Mobile Number:</label>
                            <div class="text-muted">9870123489</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Email:</label>
                            <div class="text-muted">krishnafabrics12@gmail.com</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Website URL:</label>
                            <div class="text-muted">https://www.krishnafabrics.net/</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Transport Name:</label>
                            <div class="text-muted">Nachias Van(12)</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Booking Area:</label>
                            <div class="text-muted">North Masi Street</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Supplier Type:</label>
                            <div class="text-muted">Fabric</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Status:</label>
                            <div class="text-success">Active</div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
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
                            <div class="text-muted">68/1 East Perumal Maistry Street</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Address Line 2:</label>
                            <div class="text-muted">Mahal Area, Madurai – 625001, Tamil Nadu, India</div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Contact Information:</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Contact Person Name:</label>
                            <div class="text-muted">Rio</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Designation:</label>
                            <div class="text-muted">Store Manager</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Phone Number:</label>
                            <div class="text-muted">7410258963</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Email:</label>
                            <div class="text-muted">rio90@gmail.com</div>
                        </div>
                        <div class="col-lg-12">
                            <h6>Commission Details:</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Purchase Commission Agent:</label>
                            <div class="text-muted">Rayan (PC001)</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Commission (%):</label>
                            <div class="text-muted">12.36%</div>
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
                            <div class="text-muted">33AXFPK5008A1Z6</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">ECC No:</label>
                            <div class="text-muted">MAD1234567</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Credit Limit:</label>
                            <div class="text-muted text-success">-</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Payment Terms:</label>
                            <div class="text-muted">Net 30 Days</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
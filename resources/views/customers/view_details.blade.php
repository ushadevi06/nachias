@extends('layouts.common')
@section('title', 'View Customer - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Customer</h4>
                <a href="{{ url('suppliers') }}" class="btn btn-primary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <h6>Identification & Contact:</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Category: </label>
                            <div class="text-muted">Retailer</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Customer Name:</label>
                            <div class="text-muted">Arul Textiles <strong>(CUS001)</strong></div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Mobile Number:</label>
                            <div class="text-muted">9870123489</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Email:</label>
                            <div class="text-muted">abinaya.1900@gmail.com</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Website URL:</label>
                            <div class="text-muted">https://www.abinaya.com/</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Transport Name:</label>
                            <div class="text-muted">MSS Logistics</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Booking Office:</label>
                            <div class="text-muted">Hosur</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Zone:</label>
                            <div class="text-muted">South Zone</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Stores:</label>
                            <div class="text-muted">Finished Goods</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Status:</label>
                            <div class="text-success">Active</div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Location Information:</h6>
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
                            <div class="text-muted">Keelavasal</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Address Line 1:</label>
                            <div class="text-muted">No. 250, Bazzar Street</div>
                        </div> 
                        <div class="col-md-3">
                            <label class="detail-title">Address Line 2:</label>
                            <div class="text-muted">Bangalore Main Road</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Address Line 3:</label>
                            <div class="text-muted"></div>
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
                            <label class="detail-title">Mobile Number:</label>
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
                            <h6>Commission Information:</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Sales Agent:</label>
                            <div class="text-muted">Amit Kumar(SA101)</div>
                        </div>
                        <div class="col-md-3">
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
                            <label class="detail-title">GST No:</label>
                            <div class="text-muted">33ARULTX1234Z1Z </div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Tax Type:</label>
                            <div class="text-muted">IGST8%</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">PAN NO:</label>
                            <div class="text-muted">AAAPH1234C</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Credit Limit:</label>
                            <div class="text-muted">₹50,000</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Payment Terms:</label>
                            <div class="text-muted text-success"> Net 30 Days</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Sales Discount (%):</label>
                            <div class="text-muted"> 12 %</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Box Discount (%):</label>
                            <div class="text-muted"> 3.2 %</div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>  
                        <div class="col-lg-12">
                            <h6>Bank Information:</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Bank Name:</label>
                            <div class="text-muted">Indian Bank</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Branch:</label>
                            <div class="text-muted">Anna Nagar</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Account Number:</label>
                            <div class="text-muted">MAD1234567</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">IFSC Code:</label>
                            <div class="text-muted">IDIB000M004</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
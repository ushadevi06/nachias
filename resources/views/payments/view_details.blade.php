@extends('layouts.common')
@section('title', 'View Payment - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Payment</h4>
                <a href="{{ url('payments') }}" class="btn btn-primary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="detail-title">Payment Type: </label>
                            <div class="text-muted">Supplier Payment</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Reference Document:</label>
                            <div class="text-muted">PO(PO-2025-001)</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Party:</label>
                            <div class="text-muted">Supplier</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Party Name:</label>
                            <div class="text-muted">Krishna Fabrics(SUP001)</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Payment Mode:</label>
                            <div class="text-muted">Online</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Amount Paid:</label>
                            <div class="text-muted">₹20,000</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Balance Outstanding:</label>
                            <div class="text-muted">₹6,900</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Payment Date :</label>
                            <div class="text-muted">25-09-2025</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Status:</label>
                            <div class="text-muted"><span class="badge bg-success">Cleared</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
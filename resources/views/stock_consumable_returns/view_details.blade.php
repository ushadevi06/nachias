@extends('layouts.common')
@section('title', 'View Stock Consumables & Return Management - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Stock Consumables & Return Management</h4>
                <a href="{{ url('stock_consumables_returns') }}" class="btn btn-primary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <label class="detail-title">Issue No: </label>
                            <div class="text-muted">ISSUE001</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Issue Date:</label>
                            <div class="text-muted">22-09-2025</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Issue Type:</label>
                            <div class="text-muted">Consumable Issue</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Production:</label>
                            <div class="text-muted">Stiching</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Material Category:</label>
                            <div class="text-muted">Fabric</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Material:</label>
                            <div class="text-muted">Cotton Poplin 60 GSM</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">UOM:</label>
                            <div class="text-muted">KG</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Issued Quantity:</label>
                            <div class="text-muted">50</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Returned Quantity:</label>
                            <div class="text-muted">48</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Net Assumption:</label>
                            <div class="text-muted">2</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Reason:</label>
                            <div class="text-muted">Quality Issue</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Remarks:</label>
                            <div class="text-muted">-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
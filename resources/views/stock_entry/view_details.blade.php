@extends('layouts.common')
@section('title', 'View Stock Entry- ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Stock Entry</h4>
                <a href="{{ url('stock_entries') }}" class="btn btn-primary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="detail-title">Stock Entry No: </label>
                            <div class="text-muted">STOCK001</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Stock Date:</label>
                            <div class="text-muted">22-09-2025</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Entry Type:</label>
                            <div class="text-muted">Inward</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Material Category:</label>
                            <div class="text-muted">Fabric(MC001)</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Material:</label>
                            <div class="text-muted">Cotton Poplin 60 GSM(M001)</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">UOM:</label>
                            <div class="text-muted">250M</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Quantity In:</label>
                            <div class="text-muted">180M</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Quantity Out:</label>
                            <div class="text-muted">-</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Price:</label>
                            <div class="text-muted">₹925</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Location / Store:</label>
                            <div class="text-muted">Warehouse A</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Remarks:</label>
                            <div class="text-muted">Materials Purchased</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Reference Document:</label>
                            <div class="text-muted">-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
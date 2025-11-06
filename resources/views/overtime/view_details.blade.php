@extends('layouts.common')
@section('title', 'View Overtime / Bonus - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Overtime / Bonus</h4>
                <a href="{{ url('overtime') }}" class="btn btn-primary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="detail-title">Month/Year:</label>
                            <div class="text-muted">Sep 2025</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Employee:</label>
                            <div class="text-muted">Ramesh Kumar(EMP001)</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Overtime Hours:</label>
                            <div class="text-muted">10</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Overtime Rate:</label>
                            <div class="text-muted">₹300</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Overtime Amount:</label>
                            <div class="text-muted">₹3,000</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Bonus Type:</label>
                            <div class="text-muted">Festival</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Bonus Amount:</label>
                            <div class="text-muted">₹2,000</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.common')
@section('title', 'View Leave - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Leave</h4>
                <a href="{{ url('leave') }}" class="btn btn-primary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="detail-title">Leave ID: </label>
                            <div class="text-muted">LV001</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Employee:</label>
                            <div class="text-muted">Ramesh Kumar(EMP001)</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Leave Type:</label>
                            <div class="text-muted">Casual</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Start Date:</label>
                            <div class="text-muted">29-09-2025</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">End Date:</label>
                            <div class="text-muted">30-09-2025</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Approval Status:</label>
                            <div class="text-muted"><span class="badge bg-warning">Pending</span></div>
                        </div>
                        <div class="col-md-12">
                            <label class="detail-title">Reason:</label>
                            <div class="text-muted">Personal work or errands that require a short absence from office.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
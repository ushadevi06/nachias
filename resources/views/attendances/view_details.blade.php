@extends('layouts.common')
@section('title', 'View Attendance - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row mb-4">
        <div class="col-lg-12 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h4 class="mb-1">Attendance Details</h4>
                <p class="text-muted mb-0">Review the selected employee's biometric attendance entry.</p>
            </div>
            <a href="{{ url('attendances') }}" class="btn btn-outline-secondary">
                <i class="ri ri-arrow-left-line me-1"></i>Back 
            </a>
        </div>
    </div>

    <div class="row gy-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="avatar rounded-circle bg-primary text-white mx-auto mb-3" style="width:64px;height:64px;display:grid;place-items:center;">
                        RK
                    </div>
                    <h5 class="mb-1">Ramesh Kumar</h5>
                    <p class="text-muted mb-3">Employee Code: <strong>EMP001</strong></p>
                    <span class="badge bg-success py-2 px-3">Present</span>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="border rounded p-3 h-100 bg-light">
                                <div class="text-muted small mb-1">Attendance Date</div>
                                <div class="fw-semibold">27-09-2025</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="border rounded p-3 h-100 bg-light">
                                <div class="text-muted small mb-1">Device</div>
                                <div class="fw-semibold">192.168.203</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="border rounded p-3 h-100 bg-light">
                                <div class="text-muted small mb-1">In Time</div>
                                <div class="fw-semibold">09:06 AM</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="border rounded p-3 h-100 bg-light">
                                <div class="text-muted small mb-1">Out Time</div>
                                <div class="fw-semibold">06:30 PM</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="border rounded p-3 h-100 bg-light">
                                <div class="text-muted small mb-1">Total Hours</div>
                                <div class="fw-semibold">9.0 hrs</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="border rounded p-3 h-100 bg-light">
                                <div class="text-muted small mb-1">Status</div>
                                <div><span class="badge bg-success">Present</span></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded p-3 bg-white">
                                <div class="text-muted small mb-1">Notes</div>
                                <p class="mb-0">Attendance record synced from biometric device. Use this page to verify the employee punch details and status.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
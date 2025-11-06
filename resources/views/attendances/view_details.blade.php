@extends('layouts.common')
@section('title', 'View Attendance - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Attendance</h4>
                <a href="{{ url('attendances') }}" class="btn btn-primary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="detail-title">Employee: </label>
                            <div class="text-muted">Ramesh Kumar(EMP001)</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Date:</label>
                            <div class="text-muted">Cutting</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">In Time:</label>
                            <div class="text-muted">09:06 AM</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Out Time:</label>
                            <div class="text-muted">06:30 PM</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Hours:</label>
                            <div class="text-muted">9</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Status:</label>
                            <div class="text-muted"><span class="badge bg-success">Present</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
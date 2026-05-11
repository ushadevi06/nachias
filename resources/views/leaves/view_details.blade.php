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
                            <label class="detail-title">Employee:</label>
                            <div class="text-muted">{{ $leave->employee->name }} ({{ $leave->employee->emp_id }})</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Leave Type:</label>
                            <div class="text-muted">{{ $leave->leave_type }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Leave Date:</label>
                            <div class="text-muted">{{ date('d-m-Y', strtotime($leave->leave_date)) }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Approval Status:</label>
                            <div class="text-muted">
                                @if($leave->status == 'Approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($leave->status == 'Rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="detail-title">Reason:</label>
                            <div class="text-muted">{{ $leave->reason }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.common')
@section('title', 'View Leave - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Leave</h4>
                <a href="{{ url('leaves') }}" class="btn btn-primary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="detail-title">Employee:</label>
                            <div class="text-muted">{{ $leave->employee ? $leave->employee->name . ' (' . $leave->employee->emp_id . ')' : 'N/A' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Leave Type:</label>
                            <div class="text-muted">{{ $leave->leave_type }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Leave Date / Duration:</label>
                            <div class="text-muted">
                                @php
                                    $sDate = $leave->start_date ?? $leave->leave_date;
                                    $eDate = $leave->end_date ?? $leave->leave_date;
                                    $daysVal = floatval($leave->no_of_days ?? 1);
                                    $daysText = ($daysVal == 1) ? '1 Day' : (rtrim(rtrim(number_format($daysVal, 1), '0'), '.') . ' Days');
                                @endphp
                                @if($sDate && $eDate && $sDate != $eDate)
                                    {{ date('d-m-Y', strtotime($sDate)) }} to {{ date('d-m-Y', strtotime($eDate)) }}
                                    <span class="badge bg-label-info ms-1">({{ $daysText }})</span>
                                @else
                                    {{ date('d-m-Y', strtotime($sDate)) }}
                                    <span class="badge bg-label-info ms-1">({{ $daysText }})</span>
                                @endif
                            </div>
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
                        <div class="col-md-4">
                            <label class="detail-title">Attachment:</label>
                            <div class="text-muted">
                                @if(!empty($leave->attachment))
                                    <a href="{{ asset('uploads/leaves/' . $leave->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="ri ri-attachment-line me-1"></i>View Attachment
                                    </a>
                                @else
                                    <span class="text-muted fst-italic">No Attachment</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
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

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
                    <div class="avatar rounded-circle bg-primary text-white mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:64px;height:64px;">
                        @php 
                            $profileImagePath = $attendance->profile_image
                                ? public_path('uploads/employee/' . $attendance->user_id . '/' . $attendance->profile_image)
                                : null;
                            $profileImageUrl = ($attendance->profile_image && file_exists($profileImagePath))
                                ? url('uploads/employee/' . $attendance->user_id . '/' . $attendance->profile_image)
                                : url('assets/images/user.jpg'); 
                        @endphp
                        <img src="{{ $profileImageUrl }}" 
                            alt="alt" 
                            class="rounded-circle"
                            style="width:60px;height:60px;object-fit:cover;">
                    </div>
                    <h5 class="mb-1">{{ $attendance->name }}</h5>
                    <p class="text-muted mb-3">Employee Code: <strong>{{ $attendance->emp_id }}</strong></p>
                    <span class="badge 
                        @if($attendance->status === 'Present') bg-success
                        @elseif($attendance->status === 'Late') bg-danger
                        @elseif($attendance->status === 'Overtime') bg-warning text-dark
                        @elseif($attendance->status === 'Absent') bg-danger
                        @elseif($attendance->status === 'Punch Out Missing') bg-danger
                        @elseif($attendance->status === 'Holiday') bg-primary
                        @elseif($attendance->status === 'Week Off') bg-secondary
                        @else bg-secondary
                        @endif py-2 px-3">
                        {{ $attendance->status }}
                    </span>
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
                                <div class="fw-semibold">{{ date('d-m-Y', strtotime($attendance->date)) }}</div>
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
                                <div class="fw-semibold">{{ $attendance->in_time != null ? date('h:i A', strtotime($attendance->in_time)) : '-' }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="border rounded p-3 h-100 bg-light">
                                <div class="text-muted small mb-1">Out Time</div>
                                <div class="fw-semibold">{{ $attendance->out_time!= null ? date('h:i A', strtotime($attendance->out_time)) : '-' }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="border rounded p-3 h-100 bg-light">
                                <div class="text-muted small mb-1">Total Hours</div>
                                <div class="fw-semibold">{{ $attendance->work_hours }} hrs</div>
                            </div>
                        </div>
                        {{-- <div class="col-sm-6">
                            <div class="border rounded p-3 h-100 bg-light">
                                <div class="text-muted small mb-1">Status</div>
                                <div><span class="badge 
                                    @if($attendance->status === 'Present') bg-success
                                    @elseif($attendance->status === 'Late') bg-danger
                                    @elseif($attendance->status === 'Overtime') bg-warning text-dark
                                    @elseif($attendance->status === 'Absent') bg-danger
                                    @elseif($attendance->status === 'Punch Out Missing') bg-danger
                                    @elseif($attendance->status === 'Holiday') bg-primary
                                    @elseif($attendance->status === 'Week Off') bg-secondary
                                    @else bg-secondary
                                    @endif">
                                    {{ $attendance->status }}
                                </span></div>
                            </div>
                        </div> --}}
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

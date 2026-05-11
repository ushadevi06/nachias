@php
$user = auth()->user();
$isSuper = $user->id == 1;
@endphp
@extends('layouts.common')
@section('title', 'Add Leave - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            @include('flash_messages')
        </div>
        {{-- @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif --}}
        <div class="col-lg-12">
            <form action="{{ $leaveEntry ? url('leaves/add/' . $leaveEntry->id) : url('leaves/add') }}" method="POST" class="common-form" autocomplete="off">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Add Leave</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    @php
                                        $empValue = old('emp_code', $leaveEntry->emp_code ?? '');
                                    @endphp
                                    @if($isSuper)
                                    <select class="select2 form-select" id="emp_code_display" {{ $leaveEntry ? 'disabled' : '' }} data-placeholder="Select Employee">
                                        <option value="">Select Employee</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->emp_id }}"
                                                 {{ $empValue == $employee->emp_id ? 'selected' : '' }}>
                                                {{ $employee->name }} ({{ $employee->emp_id }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="emp_code" id="emp_code" value="{{ $empValue }}">
                                    <label for="leave_type">Employee * </label>
                                    @else
                                    <input type="text" class="form-control @error('emp_code') is-invalid @enderror"
                                        value="{{ auth()->user()->name }} ({{ auth()->user()->emp_id }})" readonly>
                                    <input type="hidden" name="emp_code" value="{{ auth()->user()->emp_id }}">
                                    @endif
                                </div>
                                @error('emp_code')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    @php
                                        $leaveType = old('leave_type', $leaveEntry->leave_type ?? '');
                                    @endphp
                                    <select class="select2 form-select" id="leave_type_display" {{ $leaveEntry ? 'disabled' : '' }} data-placeholder="Select Leave Type">
                                        <option value="">Select Leave Type</option>
                                        <option value="Casual" {{ $leaveType == 'Casual' ? 'selected' : '' }}>Casual</option>
                                        <option value="Sick" {{ $leaveType == 'Sick' ? 'selected' : '' }}>Sick</option>
                                        <option value="Paid" {{ $leaveType == 'Paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="Maternity" {{ $leaveType == 'Maternity' ? 'selected' : '' }}>Maternity</option>
                                    </select>
                                    <input type="hidden" name="leave_type" id="leave_type" value="{{ $leaveType }}">
                                    <label for="leave_type">Leave Type * </label>
                                </div>
                                @error('leave_type')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="leave_days_display" class="form-control @error('leave_days') is-invalid @enderror" placeholder="Enter Leave Days" value="{{ old('leave_days', $leaveEntry ? date('d-m-Y', strtotime($leaveEntry->leave_date)) : '') }}" {{ $leaveEntry ? 'disabled' : '' }}>
                                    <input type="hidden" name="leave_days" id="leave_days" value="{{ old('leave_days', isset($leaveEntry) ? date('d-m-Y', strtotime($leaveEntry->leave_date)) : '') }}">
                                    <label for="leave_days">Leave Day(s) * </label>
                                </div>
                                @error('leave_days')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100 @error('reason') is-invalid @enderror" name="reason" id="reason" placeholder="Enter Reason" {{ $leaveEntry ? 'readonly' : '' }}>{{ old('reason', $leaveEntry ? $leaveEntry->reason : '') }}</textarea>
                                    <label for="reason">Reason * </label>
                                </div>
                                @error('reason')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    @php
                                        $statusValue = old('status', $leaveEntry->status ?? 'Pending');
                                        $isFinal = isset($leaveEntry) && in_array($leaveEntry->status, ['Approved', 'Rejected']);
                                    @endphp
                                    <input type="hidden" name="status" id="status" value="{{ $statusValue }}">
                                    <select id="status_display" class="select2 form-select @error('status') is-invalid @enderror" {{ $isFinal ? 'disabled' : '' }}>
                                        @if(isset($leaveEntry))
                                            @if($isFinal)
                                                <option value="{{ $statusValue }}" selected>{{ $statusValue }}</option>
                                            @else
                                                <option value="Pending" {{ $statusValue == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="Approved" {{ $statusValue == 'Approved' ? 'selected' : '' }}>Approved</option>
                                                <option value="Rejected" {{ $statusValue == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                            @endif
                                        @else
                                            <option value="Pending" selected>Pending</option>
                                        @endif
                                    </select>
                                    <label for="approval_status">Approval Status * </label>
                                </div>
                                @error('status')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('leave') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    $('#leave_days_display').flatpickr({
        mode: 'range',
        dateFormat: 'd-m-Y',
        allowInput: true,
        minDate: "today"
    });
    $(document).on('change', '#status_display', function () {
        $('#status').val($(this).val());
    });
    $(document).on('change', '#emp_code_display', function () {
        $('#emp_code').val($(this).val());
    });
    $(document).on('change', '#leave_type_display', function () {
        $('#leave_type').val($(this).val());
    });
    $(document).on('change', '#leave_days_display', function () {
        $('#leave_days').val($(this).val());
    });
</script>
@endsection

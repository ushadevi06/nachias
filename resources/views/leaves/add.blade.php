@extends('layouts.common')
@section('title', 'Add Leave - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="" method="POST" class="common-form">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Add Leave</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="" class="select2 form-select" data-placeholder="Select Employee">
                                        <option value="">Select Employee</option>
                                        <option value="Ramesh Kumar(EMP001)">Ramesh Kumar(EMP001)</option>
                                        <option value="Karthick(EMP002)">Karthick(EMP002)</option>
                                        <option value="Akash Mehta (EMP003)	">Akash Mehta (EMP003) </option>
                                    </select>
                                    <label for="leave_type">Employee * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="" class="select2 form-select" data-placeholder="Select Leave Type">
                                        <option value="">Select Leave Type</option>
                                        <option value="Casual">Casual</option>
                                        <option value="Sick">Sick</option>
                                        <option value="Paid">Paid</option>
                                        <option value="Maternity">Maternity</option>
                                    </select>
                                    <label for="leave_type">Leave Type * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control start_date" id="start_date" placeholder="Enter Start Date" name="start_date">
                                    <label for="start_date">Start Date * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control end_date" id="end_date" placeholder="Enter End Date" name="end_date">
                                    <label for="end_date">End Date * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100" id="address" placeholder="Enter Reason"></textarea>
                                    <label for="reason">Reason * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" data-placeholder="Select Approval Status">
                                        <option value="">Select Approval Status</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Approved">Approved</option>
                                        <option value="Rejected">Rejected</option>
                                    </select>
                                    <label for="approval_status">Approval Status * </label>
                                </div>
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
<script>
    $(document).ready(function() {
        $('.start_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            minDate: 'today',
            allowInput: true
        });
        $('.end_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            minDate: 'today',
            allowInput: true
        });
    });
</script>
@endsection
@extends('layouts.common')
@section('title', 'Add Shift - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>Add Shift</h4>
                    </div>
                    <form action="" method="POST" class="common-form">
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="shift_name" placeholder="Enter Shift Name" name="shift_name">
                                    <label for="shift_name">Shift Name <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="time" class="form-control" id="start_time" name="start_time">
                                    <label for="start_time">Start Time <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="time" class="form-control" id="end_time" name="end_time">
                                    <label for="end_time">End Time <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select select2" id="status" name="status" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('shifts') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

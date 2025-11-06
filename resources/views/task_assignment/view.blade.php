@extends('layouts.common')
@section('title', 'Task Assignment - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Task Assignment</h4>
                <a class="btn btn-primary" href="{{ url('add_task_assignment') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="filter-box">
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <h5>Filter</h5>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <div class="form-floating form-floating-outline">
                                    <select name="assignment_type" id="assignment_type" class="form-select select2" data-placeholder="Select Assignment Type">
                                        <option value="">Select Assignment Type</option>
                                        <option value="Instant">Instant</option>
                                        <option value="Scheduled">Scheduled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <div class="form-floating form-floating-outline">
                                    <select name="permission" id="permission" class="form-select select2" data-placeholder="Select Permission">
                                        <option value="">Select Permission</option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary">Filter</button>
                                <button type="button" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable">
                        <table class="datatables-products table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Task ID</th>
                                    <th>Job Card No</th>
                                    <th>Quantity</th>
                                    <th>Assigned By</th>
                                    <th>Assigned To</th>
                                    <th>Assignment Type</th>
                                    <th>Permission</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>TASK-2025-001</td>
                                    <td>JC20250924-001-K</td>
                                    <td>2</td>
                                    <td>Admin</td>
                                    <td>Ramesh <span class="mini-title">(EMP001)</span></td>
                                    <td>Direct</td>
                                    <td>Yes</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_task_assignment') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_task_assignment') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>TASK-2025-001</td>
                                    <td>JC20250924-001-K</td>
                                    <td>2</td>
                                    <td>Admin</td>
                                    <td>Karthick <span class="mini-title">(EMP002)</span></td>
                                    <td>Direct</td>
                                    <td>Yes</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_task_assignment') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_task_assignment') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>TASK-2025-003</td>
                                    <td>JC20250924-002-K</td>
                                    <td>2</td>
                                    <td>Admin</td>
                                    <td>Akash Mehta <span class="mini-title">(EMP003)</span></td>
                                    <td>Self-assigned</td>
                                    <td>No</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_task_assignment') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_task_assignment') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
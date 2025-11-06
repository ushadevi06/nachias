@extends('layouts.common')
@section('title', 'Task Creation - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Task Creation</h4>
                <a class="btn btn-primary" href="{{ url('add_task_creation') }}">
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
                                    <select name="task_type" id="task_type" class="form-select select2" data-placeholder="Select Task Type">
                                        <option value="">Select Task Type</option>
                                        <option value="Instant">Instant</option>
                                        <option value="Scheduled">Scheduled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <select class="select2 form-select" data-placeholder="Select Assigned Team">
                                    <option value="">Select Assigned Team</option>
                                    <option value="Cutting Department">Cutting Department</option>
                                    <option value="Quality Department">Quality Department</option>
                                    <option value="Quality Department">Stitching Department</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <select class="select2 form-select" data-placeholder="Select Priority">
                                    <option value="">Select Priority</option>
                                    <option value="High">High</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Low">Low</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <select class="select2 form-select" data-placeholder="Select Status">
                                    <option value="">Select Status</option>
                                    <option value="Not Started">Not Started</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Overdue">Overdue</option>
                                </select>
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
                                    <th>Job Card No.</th>
                                    <th>Task Title</th>
                                    <th>Task Type</th>
                                    <th>Assigned Team</th>
                                    <th>Priority</th>
                                    <th>Deadline Date</th>
                                    <th>Related Module</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>TASK-2025-001</td>
                                    <td>JC20250924-001-K</td>
                                    <td>Cutting for Order #SO-1001</td>
                                    <td>Scheduled</td>
                                    <td>Cutting Department</td>
                                    <td>High</td>
                                    <td>30-09-2025</td>
                                    <td>Production</td>
                                    <td>
                                        <select class="select2 form-select" data-placeholder="Select Status">
                                            <option value="">Select Status</option>
                                            <option value="Not Started" selected>Not Started</option>
                                            <option value="In Progress">In Progress</option>
                                            <option value="Completed">Completed</option>
                                            <option value="Overdue">Overdue</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_task_creation') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_task_creation') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>TASK-2025-002</td>
                                    <td>JC20250924-001-K</td>
                                    <td>Fabric Quality Check</td>
                                    <td>Instant</td>
                                    <td>Quality Department</td>
                                    <td>Medium</td>
                                    <td>26-09-2025</td>
                                    <td>Store</td>
                                    <td>
                                        <select class="select2 form-select" data-placeholder="Select Status">
                                            <option value="">Select Status</option>
                                            <option value="Not Started">Not Started</option>
                                            <option value="In Progress" selected>In Progress</option>
                                            <option value="Completed">Completed</option>
                                            <option value="Overdue">Overdue</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_task_creation') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_task_creation') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>TASK-2025-003</td>
                                    <td>JC20250924-002-K</td>
                                    <td>Stitching Order #SO-1002</td>
                                    <td>Scheduled</td>
                                    <td>Stitching Department</td>
                                    <td>High</td>
                                    <td>03-10-2025</td>
                                    <td>Production</td>
                                    <td>
                                        <select class="select2 form-select" data-placeholder="Select Status">
                                            <option value="">Select Status</option>
                                            <option value="Not Started">Not Started</option>
                                            <option value="In Progress">In Progress</option>
                                            <option value="Completed" selected>Completed</option>
                                            <option value="Overdue">Overdue</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_task_creation') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_task_creation') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
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
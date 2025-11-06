@extends('layouts.common')
@section('title', 'Task Tracking & Monitoring - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Task Tracking & Monitoring</h4>
                <a class="btn btn-primary" href="{{ url('add_task_tracking_monitoring') }}">
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
                                    <select name="task_status" id="task_status" class="form-select select2" data-placeholder="Select Task Status">
                                        <option value="">Select Task Status</option>
                                        <option value="Not Started">Not Started</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Overdue">Overdue</option>
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
                                    <th>Job Card No.</th>
                                    <th>Task Title</th>
                                    <th>Assigned By</th>
                                    <th>Assigned To</th>
                                    <th>Task Status</th>
                                    <th>Qty Ordered</th>
                                    <th>Qty Completed</th>
                                    <th>Qty Pending</th>
                                    <th>Deadline Date</th>
                                    <th>Alerts</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>TASK-2025-001</td>
                                    <td>JC20250924-001-K</td>
                                    <td>Cutting for Order #SO-1001</td>
                                    <td>Admin</td>
                                    <td>Ramesh <span class="mini-title">(EMP001)</span></td>
                                    <td><span class="badge bg-warning">In Progress</span></td>
                                    <td>25</td>
                                    <td>20</td>
                                    <td>5</td>
                                    <td>29-09-2025</td>
                                    <td>
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" value="1" name="remember" id="alerts" />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_task_tracking_monitoring') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_task_tracking_monitoring') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>TASK-2025-002</td>
                                    <td>JC20250924-001-K</td>
                                    <td>Fabric Quality Check</td>
                                    <td>Admin</td>
                                    <td>Karthick <span class="mini-title">(EMP002)</span></td>
                                    <td><span class="badge bg-danger">Not Progress</span></td>
                                    <td>10</td>
                                    <td>0</td>
                                    <td>10</td>
                                    <td>26-09-2025</td>
                                    <td>
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" value="1" name="remember" id="alerts" checked />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_task_tracking_monitoring') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_task_tracking_monitoring') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>TASK-2025-003</td>
                                    <td>JC20250924-002-K</td>
                                    <td>Stitching Order #SO-1002</td>
                                    <td>Admin</td>
                                    <td>Akash Mehta <span class="mini-title">(EMP003)</span></td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                    <td>0</td>
                                    <td>10</td>
                                    <td>0</td>
                                    <td>03-10-2025</td>
                                    <td>
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" value="1" name="remember" id="alerts" checked />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_task_tracking_monitoring') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_task_tracking_monitoring') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
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
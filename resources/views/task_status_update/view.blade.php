@extends('layouts.common')
@section('title', 'Task Status Updates - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Task Status Updates</h4>
                <a class="btn btn-primary" href="{{ url('add_task_status_update') }}">
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
                                    <select name="task_status" class="form-select select2" data-placeholder="Select Task Status">
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
                                    <th>Job Card No</th>
                                    <th>Employee</th>
                                    <th>Quantity</th>
                                    <th>Task Status</th>
                                    <th>Comments / Notes</th>
                                    <th>Files</th>
                                    <th>Updated By</th>
                                    <th>Updated On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>TASK-2025-001</td>
                                    <td>JC20250924-001-K</td>
                                    <td>Ramesh<span class="mini-title">(EMP001)</span></td>
                                    <td>25</td>
                                    <td>
                                        <select name="task_status" class="form-select select2" data-placeholder="Select Task Status">
                                            <option value="">Select Task Status</option>
                                            <option value="Not Started">Not Started</option>
                                            <option value="In Progress" selected>In Progress</option>
                                            <option value="Completed">Completed</option>
                                            <option value="Overdue">Overdue</option>
                                        </select>
                                    </td>
                                    <td>Fabric cutting 50% complete, awaiting next batch.</td>
                                    <td>
                                        <a href="{{ url('assets/images/pdf/report.pdf') }}" target="_blank"><img src="{{ url('assets/images/pdf_image.jpg') }}" alt="pdf" class="table-img"></a>
                                    </td>
                                    <td>Ramesh(EMP001) - Cutting Team</td>
                                    <td>25-09-2025 03:15 PM</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_task_status_update') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_task_status_update') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>TASK-2025-001</td>
                                    <td>JC20250924-002-K</td>
                                    <td>Karthick <span class="mini-title">(EMP002)</span></td>
                                    <td>10</td>
                                    <td>
                                        <select name="task_status" class="form-select select2" data-placeholder="Select Task Status">
                                            <option value="">Select Task Status</option>
                                            <option value="Not Started">Not Started</option>
                                            <option value="In Progress">In Progress</option>
                                            <option value="Completed" selected>Completed</option>
                                            <option value="Overdue">Overdue</option>
                                        </select>
                                    </td>
                                    <td>Cutting completed successfully.</td>
                                    <td><a href="{{ url('assets/images/sample-excel.xlsx') }}"><img src="{{ url('assets/images/excel.png') }}" alt="pdf" class="table-img"></a></td>
                                    <td>Ramesh(EMP001) - Cutting Team</td>
                                    <td>26-09-2025 11:30 AM</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_task_status_update') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_task_status_update') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>TASK-2025-002</td>
                                    <td>JC20250924-002-K</td>
                                    <td>Akash Mehta <span class="mini-title">(EMP003)</span></td>
                                    <td>10</td>
                                    <td>
                                        <select name="task_status" class="form-select select2" data-placeholder="Select Task Status">
                                            <option value="">Select Task Status</option>
                                            <option value="Not Started">Not Started</option>
                                            <option value="In Progress" selected>In Progress</option>
                                            <option value="Completed">Completed</option>
                                            <option value="Overdue">Overdue</option>
                                        </select>
                                    </td>
                                    <td>Stitching started for 200PCS</td>
                                    <td><a href=""><img src="{{ url('assets/images/pdf_image.jpg') }}" alt="pdf" class="table-img"></a></td>
                                    <td>Karthick(EMP002) - Stitching Team</td>
                                    <td>26-09-2025 02:45 PM</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_task_status_update') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_task_status_update') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
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
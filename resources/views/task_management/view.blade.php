@extends('layouts.common')
@section('title', 'Task Management List - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">Task Management List</h4>
                        <a href="{{ url('add_task_management') }}" class="btn btn-primary waves-effect waves-light">
                            <i class="ri-add-line me-1"></i> Add Task
                        </a>
                    </div>
                    <div class="card-datatable table-responsive">
                        <table class="datatables-products table table-hover">
                            <thead>
                                <tr>
                                    <th>Task ID</th>
                                    <th>Assigned To</th>
                                    <th>Stage / Dept</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-bold">TASK-2025-001</td>
                                    <td>Ramesh</td>
                                    <td>Cutting</td>
                                    <td>
                                        <div class="d-flex align-items-center" style="min-width: 150px;">
                                            <div class="progress w-100 me-2" style="height: 6px;">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span class="small fw-bold">80%</span>
                                        </div>
                                    </td>
                                    <td><span class="badge rounded-pill bg-label-info">In Progress</span></td>
                                    <td class="text-center">
                                        <div class="button-box">
                                            <a href="{{ url('add_task_management') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_task_management') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete delete-btn" onclick="delete_data('{{ url('delete_task_management/1') }}')"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">TASK-2025-002</td>
                                    <td>Karthick</td>
                                    <td>Stitching</td>
                                    <td>
                                        <div class="d-flex align-items-center" style="min-width: 150px;">
                                            <div class="progress w-100 me-2" style="height: 6px;">
                                                <div class="progress-bar bg-secondary" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span class="small fw-bold">0%</span>
                                        </div>
                                    </td>
                                    <td><span class="badge rounded-pill bg-label-secondary">Not Started</span></td>
                                    <td class="text-center">
                                        <div class="button-box">
                                            <a href="{{ url('add_task_management') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_task_management') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete delete-btn" onclick="delete_data('{{ url('delete_task_management/2') }}')"><i class="icon-base ri ri-delete-bin-line"></i></a>
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

@extends('layouts.common')
@section('title', 'Manage Leaves - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="table-header-box">
        <h4>Manage Leaves</h4>
        <a class="btn btn-primary" href="{{ url('add_leave') }}">
            <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
        </a>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="card-datatable">
                <table class="datatables-products table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Leave ID</th>
                            <th>Employee</th>
                            <th>Leave Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Approval Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>LV001</td>
                            <td>Ramesh Kumar <span class="mini-title">(EMP001)</span></td>
                            <td>Casual</td>
                            <td>29-09-2025</td>
                            <td>30-09-2025</td>
                            <td><span class="badge bg-warning">Pending</span></td>
                            <td>
                                <div class="button-box">
                                    <a href="{{ url('view_leave') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                    <a href="{{ url('add_leave') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                    <a href="javascript:;" class="btn btn-edit"><i class="icon-base ri ri-checkbox-circle-line"></i></a>
                                    <a href="javascript:;" class="btn btn-delete"><i class="icon-base ri ri-close-circle-line"></i></a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>LV002</td>
                            <td>Karthick <span class="mini-title">(EMP002)</span></td>
                            <td>Sick</td>
                            <td>26-09-2025</td>
                            <td>26-09-2025</td>
                            <td><span class="badge bg-success">Approved</span></td>
                            <td>
                                <div class="button-box">
                                    <a href="{{ url('view_leave') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                    <a href="{{ url('add_leave') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>LV003</td>
                            <td>Akash Mehta <span class="mini-title">(EMP003)</span></td>
                            <td>Paid</td>
                            <td>01-10-2025</td>
                            <td>03-10-2025</td>
                            <td><span class="badge bg-danger">Rejected</span></td>
                            <td>
                                <div class="button-box">
                                    <a href="{{ url('view_leave') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                    <a href="{{ url('add_leave') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
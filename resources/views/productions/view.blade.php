@extends('layouts.common')
@section('title', 'Productions - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Productions</h4>
                <a class="btn btn-primary" href="{{ url('add_production') }}">
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
                                    <th>Production Date</th>
                                    <th>Job Card No</th>
                                    <th>Production Stage</th>
                                    <th>Service Provider</th>
                                    <th>Shift</th>
                                    <th>Planned Qty</th>
                                    <th>Completed Qty</th>
                                    <th>WIP</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>16-12-2023</td>
                                    <td class="fw-bold">JC20250924-001-K</td>
                                    <td>Cutting</td>
                                    <td>In-House Cutting</td>
                                    <td>Day</td>
                                    <td>100</td>
                                    <td>80</td>
                                    <td>15</td>
                                    <td>
                                        <select class="form-select form-select-sm" data-placeholder="Select Status">
                                            <option value="">Select Status</option>
                                            <option value="Not Started">Not Started</option>
                                            <option value="In Progress" selected>In Progress</option>
                                            <option value="Completed">Completed</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_production') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_production') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>17-12-2023</td>
                                    <td class="fw-bold">JC20250924-001-K</td>
                                    <td>Stitching</td>
                                    <td>Vendor A Stitching</td>
                                    <td>Night</td>
                                    <td>100</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>
                                        <select class="form-select form-select-sm" data-placeholder="Select Status">
                                            <option value="">Select Status</option>
                                            <option value="Not Started" selected>Not Started</option>
                                            <option value="In Progress">In Progress</option>
                                            <option value="Completed">Completed</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_production') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_production') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>18-12-2023</td>
                                    <td class="fw-bold">JC20250924-002-K</td>
                                    <td>Cutting</td>
                                    <td>In-House Cutting</td>
                                    <td>Day</td>
                                    <td>150</td>
                                    <td>150</td>
                                    <td>0</td>
                                    <td>
                                        <select class="form-select form-select-sm" data-placeholder="Select Status">
                                            <option value="">Select Status</option>
                                            <option value="Not Started">Not Started</option>
                                            <option value="In Progress">In Progress</option>
                                            <option value="Completed" selected>Completed</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_production') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_production') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
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
@extends('layouts.common')
@section('title', 'Employees - ' . env('WEBSITE_NAME'))
@section('content')
<!-- / Menu -->
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4 class="mb-0">Employees</h4>
                <a class="btn btn-primary" href="{{ url('add_employee') }}">
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
                                    <th>Name</th>
                                    <th>Image</th>
                                    <th>Role</th>
                                    <th>Department</th>
                                    <th>Mobile Number</th>
                                    <th>Status</th>
                                    <th>Created On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Ramesh<span class="mini-title">(EMP001)</span></td>
                                    <td><img src="{{ url('assets/images/user.jpg') }}" alt="alt" class="rounded-circle" width="50"></td>
                                    <td>Manager</td>
                                    <td>Cutting</td>
                                    <td>7894561230</td>
                                    <td>
                                        <label class="switch switch-success switch-lg">
                                            <input type="checkbox" class="switch-input" checked>
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                        </label>
                                    </td>
                                    <td>04-11-2025</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('add_employee') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" title="Delete" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Karthick <span class="mini-title">(EMP002)</span></td>
                                    <td><img src="{{ url('assets/images/user.jpg') }}" alt="alt" class="rounded-circle" width="50"></td>
                                    <td>Supervisior</td>
                                    <td>Stitching</td>
                                    <td>7410258963</td>
                                    <td>
                                        <label class="switch switch-success switch-lg">
                                            <input type="checkbox" class="switch-input" checked>
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                        </label>
                                    </td>
                                    <td>03-11-2025</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('add_employee') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" title="Delete" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Akash Mehta <span class="mini-title">(EMP003)</span></td>
                                    <td><img src="{{ url('assets/images/user.jpg') }}" alt="alt" class="rounded-circle" width="50"></td>
                                    <td>Store Keeper</td>
                                    <td>Ironing</td>
                                    <td>9652308741</td>
                                    <td>
                                        <label class="switch switch-success switch-lg">
                                            <input type="checkbox" class="switch-input">
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                        </label>
                                    </td>
                                    <td>03-11-2025</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('add_employee') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" title="Delete" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Rithivika <span class="mini-title">(EMP004)</span></td>
                                    <td><img src="{{ url('assets/images/user.jpg') }}" alt="alt" class="rounded-circle" width="50"></td>
                                    <td>Production Head</td>
                                    <td>Packing</td>
                                    <td>8458986810</td>
                                    <td>
                                        <label class="switch switch-success switch-lg">
                                            <input type="checkbox" class="switch-input" checked>
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                        </label>
                                    </td>
                                    <td>01-11-2025</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('add_employee') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" title="Delete" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>Kishore Kumar <span class="mini-title">(EMP005)</span></td>
                                    <td><img src="{{ url('assets/images/user.jpg') }}" alt="alt" class="rounded-circle" width="50"></td>
                                    <td>Accountant</td>
                                    <td>Cutting</td>
                                    <td>8458986810</td>
                                    <td>
                                        <label class="switch switch-success switch-lg">
                                            <input type="checkbox" class="switch-input">
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                        </label>
                                    </td>
                                    <td>01-11-2025</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('add_employee') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" title="Delete" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
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
@extends('layouts.common')
@section('title', 'UOM - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>UOM</h4>
                <a class="btn btn-primary" href="{{ url('add_uom') }}">
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
                                    <th>UOM Code</th>
                                    <th>UOM Name</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>MTR</td>
                                    <td>Meter</td>
                                    <td>
                                        <label class="switch switch-success switch-lg">
                                            <input type="checkbox" class="switch-input" checked>
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('add_uom') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" title="Delete" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>PCS</td>
                                    <td>Piece</td>
                                    <td>
                                        <label class="switch switch-success switch-lg">
                                            <input type="checkbox" class="switch-input" checked>
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('add_uom') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" title="Delete" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>ROLL</td>
                                    <td>Roll</td>
                                    <td>
                                        <label class="switch switch-success switch-lg">
                                            <input type="checkbox" class="switch-input" checked>
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('add_uom') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
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
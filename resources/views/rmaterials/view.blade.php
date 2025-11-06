@extends('layouts.common')
@section('title', 'Raw Materials - ' . env('WEBSITE_NAME'))
@section('content')
<!-- / Menu -->
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Raw Materials </h4>
                <a class="btn btn-primary" href="{{ url('add_rmaterial') }}">
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
                                    <th>Category</th>
                                    <th>Name</th>
                                    <th>UOM</th>
                                    <th>Min Stock</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Fabric <span class="mini-title">(MC001)</span></td>
                                    <td>Cotton Poplin 60 GSM <span class="mini-title">(M001)</span></td>
                                    <td>MTR</td>
                                    <td>150</td>
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
                                            <a href="{{ url('add_rmaterial') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Accessories <span class="mini-title">(MC002)</span></td>
                                    <td>Zipper <span class="mini-title">(M002)</span></td>
                                    <td>PCS</td>
                                    <td>100</td>
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
                                            <a href="{{ url('add_rmaterial') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Trims <span class="mini-title">(MC003)</span></td>
                                    <td>Lace <span class="mini-title">(M003)</span></td>
                                    <td>MTR</td>
                                    <td>100</td>
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
                                            <a href="{{ url('add_rmaterial') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Thread <span class="mini-title">(MC004)</span></td>
                                    <td>Polyester Thread <span class="mini-title">(M004)</span></td>
                                    <td>KG</td>
                                    <td>100</td>
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
                                            <a href="{{ url('add_rmaterial') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>Buttons <span class="mini-title">(MC005)</span></td>
                                    <td>Metal Buttons <span class="mini-title">(M005)</span></td>
                                    <td>KG</td>
                                    <td>250</td>
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
                                            <a href="{{ url('add_rmaterial') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
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
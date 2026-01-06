@extends('layouts.common')
@section('title', 'Fit - ' . env('WEBSITE_NAME'))

@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">

            <div class="table-header-box">
                <h4>Fit</h4>
                <a class="btn btn-primary" href="{{ url('fit/add') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <table class="table" id="tableMaster">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                             <tr>
                                <td>1</td>
                                <td>Slim Fit</td>
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
                                        <a href="#" class="btn btn-edit">
                                            <i class="icon-base ri ri-edit-box-line"></i>
                                        </a>
                                        <button class="btn btn-delete">
                                            <i class="icon-base ri ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Regular Fit</td>
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
                                        <a href="#" class="btn btn-edit">
                                            <i class="icon-base ri ri-edit-box-line"></i>
                                        </a>
                                        <button class="btn btn-delete">
                                            <i class="icon-base ri ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Skinny Fit</td>
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
                                        <a href="#" class="btn btn-edit">
                                            <i class="icon-base ri ri-edit-box-line"></i>
                                        </a>
                                        <button class="btn btn-delete">
                                            <i class="icon-base ri ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>Tailored Fit</td>
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
                                        <a href="#" class="btn btn-edit">
                                            <i class="icon-base ri ri-edit-box-line"></i>
                                        </a>
                                        <button class="btn btn-delete">
                                            <i class="icon-base ri ri-delete-bin-line"></i>
                                        </button>
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
@endsection

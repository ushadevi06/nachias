@extends('layouts.common')
@section('title', 'Places - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Places (Service Points)</h4>
                <a class="btn btn-primary" href="{{ url('add_place') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="filter-box">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <h5>Filter</h5>
                            </div>
                            <div class="col-md-4 col-lg-3 country">
                                <div class="form-floating form-floating-outline">
                                    <select name="country" id="country" class="form-select select2" data-placeholder="Select Country">
                                        <option value="">Select Country</option>
                                        <option value="IN">IN</option>
                                        <option value="US">US</option>
                                        <option value="UK">UK</option>
                                        <option value="CN">CN</option>
                                        <option value="AUS">AUS</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3 state">
                                <select name="state" id="state" class="form-select select2" data-placeholder="Select State">
                                    <option value="">Select State</option>
                                    <option value="AP">AP</option>
                                    <option value="TN">TN</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3 status">
                                <select name="status" id="status" class="form-select select2" data-placeholder="Select City">
                                    <option value="">Select City</option>
                                    <option value="Madurai">Madurai</option>
                                    <option value="Chennai">Chennai</option>
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
                                    <th>Country</th>
                                    <th>State</th>
                                    <th>City</th>
                                    <th>Place (Service Point)</th>
                                    <th>Place Type</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>IN</td>
                                    <td>TN</td>
                                    <td>Madurai</td>
                                    <td>Vilangudi</td>
                                    <td>Residential</td>
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
                                            <a href="{{ url('add_place') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" title="Delete" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>IN</td>
                                    <td>TN</td>
                                    <td>Madurai</td>
                                    <td>Jaihindpuram</td>
                                    <td>Commercial</td>
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
                                            <a href="{{ url('add_place') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" title="Delete" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>IN</td>
                                    <td>TN</td>
                                    <td>Chennai</td>
                                    <td>Tambaram</td>
                                    <td>Project Site</td>
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
                                            <a href="{{ url('add_place') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
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
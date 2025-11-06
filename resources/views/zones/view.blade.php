@extends('layouts.common')
@section('title', 'Zone - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Zones</h4>
                <a class="btn btn-primary" href="{{ url('add_zone') }}">
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
                                <select name="country" id="country" class="form-select select2" data-placeholder="Select Country">
                                    <option value="">Select Country</option>
                                    <option value="IN">IN</option>
                                    <option value="US">US</option>
                                    <option value="UK">UK</option>
                                    <option value="CA">CA</option>
                                    <option value="AUS">AUS</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3 state">
                                <select name="state" id="state" class="form-select select2" data-placeholder="Select State">
                                    <option value="">Select State</option>
                                    <option value="TN">TN</option>
                                    <option value="AP">AP</option>
                                    <option value="MH">MH</option>
                                </select>
                            </div>
                            <div class="col-md-4">
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
                                    <th>Zone</th>
                                    <th>Cities</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>South Zone</td>
                                    <td>Chennai, Madurai, Coimbatore, Trichy </td>
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
                                            <a href="{{ url('add_zone') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" title="Delete" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>North Zone</td>
                                    <td>New Delhi, Chandigarh, Lucknow, Jaipur</td>
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
                                            <a href="{{ url('add_zone') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" title="Delete" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>West Zone</td>
                                    <td>Mumbai, Pune, Ahmedabad, Surat</td>
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
                                            <a href="{{ url('add_zone') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" title="Delete" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>East Zone</td>
                                    <td>Kolkata, Bhubaneswar, Patna, Guwahati</td>
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
                                            <a href="{{ url('add_zone') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
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

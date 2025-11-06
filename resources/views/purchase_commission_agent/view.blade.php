@extends('layouts.common')
@section('title', 'Purchase Commission Agent - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="table-header-box">
        <h4>Purchase Commission Agent</h4>
        <a class="btn btn-primary" href="{{ url('add_purchase_commission_agent') }}">
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
                    <div class="col-md-4 col-lg-3">
                        <select name="agent_type" id="agent_type" class="form-select select2" data-placeholder="Select Agent Type">
                            <option value="">Select Agent Type</option>
                            <option value="Direct Sales Agent">Direct Sales Agent</option>
                            <option value="Commission Agent">Commission Agent</option>
                            <option value="Export Agent">Export Agent</option>
                        </select>
                    </div>
                    <div class="col-md-4 col-lg-3">
                        <select name="commision_type" id="commision_type" class="form-select select2" data-placeholder="Select Commission Type">
                            <option value="">Select Commission Type</option>
                            <option value="Percentage on Sales">Percentage on Sales</option>
                            <option value="Fixed per Order">Fixed per Order</option>
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
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Commission Type</th>
                            <th>Commission Value</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Amit Kumar
                                <span class="mini-title">(PCA101)</span>
                            </td>
                            <td>amit@gmail.com</td>
                            <td>9876543210</td>
                            <td>Percentage on Sales</td>
                            <td>25%</td>
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
                                    <a href="{{ url('view_purchase_commission_agent') }}" title="View" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                    <a href="{{ url('add_purchase_commission_agent') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                    <a href="javascript:;" title="Delete" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Neha Sharma<span class="mini-title">(PCA102)</span></td>
                            <td>neha@gmail.com</td>
                            <td>9123456789</td>
                            <td>Fixed per Order</td>
                            <td>₹1,000</td>
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
                                    <a href="{{ url('view_purchase_commission_agent') }}" title="View" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                    <a href="{{ url('add_purchase_commission_agent') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                    <a href="javascript:;" title="Delete" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Ravi Singh<span class="mini-title">(PCA103)</span></td>
                            <td>ravi@gmail.com</td>
                            <td>9988776655</td>
                            <td>Fixed per Order</td>
                            <td>₹2,900</td>
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
                                    <a href="{{ url('view_purchase_commission_agent') }}" title="View" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                    <a href="{{ url('add_purchase_commission_agent') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                    <a href="javascript:;" title="Delete" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
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

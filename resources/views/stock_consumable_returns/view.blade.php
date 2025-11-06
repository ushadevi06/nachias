@extends('layouts.common')
@section('title', 'Stock Consumables & Return Management - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Stock Consumables & Return Management</h4>
                <a class="btn btn-primary" href="{{ url('add_stock_consumables_return') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="filter-box">
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <h5>Filter</h5>
                            </div>
                            <div class="col-md-4 col-lg-3 state">
                                <select name="production" id="production" class="form-select select2" data-placeholder="Select Production">
                                    <option value="">Select Production</option>
                                    <option value="Cutting">Cutting</option>
                                    <option value="Stitching">Stitching</option>
                                    <option value="Stitching">Printing</option>
                                    <option value="Ironing">Ironing</option>
                                    <option value="Packing">Packing</option>
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
                                    <th>Issue No.</th>
                                    <th>Issue Date</th>
                                    <th>Production</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>ISSUE001</td>
                                    <td>22-09-2025</td>
                                    <td>Stitching</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_stock_consumables_return') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_stock_consumables_return') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>ISSUE002</td>
                                    <td>22-09-2025</td>
                                    <td>Cutting</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_stock_consumables_return') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_stock_consumables_return') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>ISSUE003</td>
                                    <td>22-09-2025</td>
                                    <td>Packing</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_stock_consumables_return') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_stock_consumables_return') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
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
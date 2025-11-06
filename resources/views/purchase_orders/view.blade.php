@extends('layouts.common')
@section('title', 'Purchase Order - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Purchase Order</h4>
                <a class="btn btn-primary" href="{{ url('add_purchase_order') }}">
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
                            <div class="col-md-4 col-lg-3 status">
                                <select name="status" id="status" class="form-select select2" data-placeholder="Select Status">
                                    <option value="">Select Status</option>
                                    <option value="Draft">Draft</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Dispatched">Dispatched</option>
                                    <option value="Received">Received</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary">Filter</button>
                                <button type="button" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable">
                        <table class="datatables-products table nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>PO Number</th>
                                    <th>PO Date</th>
                                    <th>Supplier Name</th>
                                    <th>Reference No. </th>
                                    <th>Delivery Date </th>
                                    <th>Delivery Location </th>
                                    <th>Total Order Meters</th>
                                    <th>Order Date</th>
                                    <th>Status</th>
                                    <th>Total Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>PO-2025-001</td>
                                    <td>17-09-2025</td>
                                    <td>Krishna Fabrics <span class="mini-title">(SUP001)</span></td>
                                    <td>REF-987232</td>
                                    <td>24-09-2025</td>
                                    <td>Mumbai Warehouse</td>
                                    <td>9261.60 MTR</td>
                                    <td>31-10-2025</td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <select id="" class="form-select" data-placeholder="">
                                                <option value="Draft">Draft</option>
                                                <option value="Approved" selected>Approved</option>
                                                <option value="Dispatched">Dispatched</option>
                                                <option value="Received">Received</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>₹24,000</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_purchase_order') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_purchase_order') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>PO-2025-002</td>
                                    <td>17-09-2025</td>
                                    <td>Sri Meena Traders <span class="mini-title">(SUP002)</span></td>
                                    <td>REF-98723</td>
                                    <td>21-09-2025</td>
                                    <td>Delhi Distribution</td>
                                    <td>2351.60 MTR</td>
                                    <td>31-10-2025</td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <select id="" class="form-select" data-placeholder="">
                                                <option value="Draft">Draft</option>
                                                <option value="Approved">Approved</option>
                                                <option value="Dispatched" selected>Dispatched</option>
                                                <option value="Received">Received</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>₹24,000</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_purchase_order') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_purchase_order') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>PO-2025-003</td>
                                    <td>17-09-2025</td>
                                    <td>Vasanth Garments <span class="mini-title">(SUP003)</span></td>
                                    <td>REF-98765</td>
                                    <td>27-09-2025</td>
                                    <td>Chennai Warehouse</td>
                                    <td>8.692 MTR</td>
                                    <td>31-10-2025</td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <select id="" class="form-select" data-placeholder="" disabled>
                                                <option value="Draft">Draft</option>
                                                <option value="Approved">Approved</option>
                                                <option value="Dispatched">Dispatched</option>
                                                <option value="Received" selected>Received</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>₹42,750</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_purchase_order') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_purchase_order') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
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
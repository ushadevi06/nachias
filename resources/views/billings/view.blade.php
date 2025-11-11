@extends('layouts.common')
@section('title', 'Billing - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Billing</h4>
                <a class="btn btn-primary" href="{{ url('add_billing') }}">
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
                            <div class="col-md-4 col-lg-3 country">
                                <div class="form-floating form-floating-outline">
                                    <select name="bill_type" id="bill_type" class="form-select select2" data-placeholder="Select Bill Type">
                                        <option value="">Select Bill Type</option>
                                        <option value="Purchase">Purchase</option>
                                        <option value="Sales">Sales</option>
                                        <option value="Service">Service</option>
                                    </select>
                                </div>
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
                                    <th>Bill Number</th>
                                    <th>Bill Type</th>
                                    <th>Customer</th>
                                    <th>Supplier</th>
                                    <th>Bill Date</th>
                                    <th>Total Quantity</th>
                                    <th>Gross Amount</th>
                                    <th>Tax Amount</th>
                                    <th>Total Amount</th>
                                    <th>Discount</th>
                                    <th><strong>Net Pay</strong></th>
                                    <th>Remarks</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>BILL-2025-001</td>
                                    <td>Purchase</td>
                                    <td>-</td>
                                    <td>Krishna Fabrics <span class="mini-title">(SUP001)</span></td>
                                    <td>25-09-2025</td>
                                    <td>10</td>
                                    <td>₹15,000.00</td>
                                    <td>₹2,700.00</td>
                                    <td>₹17,700.00</td>
                                    <td>₹200.00</td>
                                    <td><strong>₹17,500.50</strong></td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="Enter remarks" value="Awaiting payment">
                                    </td>
                                    <td>
                                        <select class="form-select">
                                            <option value="">Select Status</option>
                                            <option value="Pending" selected>Pending</option>
                                            <option value="Dispatch">Dispatch</option>
                                            <option value="Partially Delivery">Partially Delivery</option>
                                            <option value="Cancelled">Cancelled</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_billing') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_billing') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>BILL-2025-002</td>
                                    <td>Sales</td>
                                    <td>Hero Mens Wear <span class="mini-title">(CUS001)</span></td>
                                    <td>-</td>
                                    <td>28-09-2025</td>
                                    <td>8</td>
                                    <td>₹12,000.00</td>
                                    <td>₹2,160.00</td>
                                    <td>₹14,160.00</td>
                                    <td>₹0.00</td>
                                    <td><strong>₹14,159.50</strong></td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="Enter remarks" value="Invoice cleared">
                                    </td>
                                    <td>
                                        <select class="form-select">
                                            <option value="">Select Status</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Dispatch" selected>Dispatch</option>
                                            <option value="Partially Delivery">Partially Delivery</option>
                                            <option value="Cancelled">Cancelled</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_billing') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_billing') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>BILL-2025-003</td>
                                    <td>Purchase</td>
                                    <td>-</td>
                                    <td>Shree Textiles <span class="mini-title">(SUP002)</span></td>
                                    <td>30-09-2025</td>
                                    <td>15</td>
                                    <td>₹25,000.00</td>
                                    <td>₹4,500.00</td>
                                    <td>₹29,500.00</td>
                                    <td>₹500.00</td>
                                    <td><strong>₹28,999.75</strong></td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="Enter remarks" value="Part payment received">
                                    </td>
                                    <td>
                                        <select class="form-select">
                                            <option value="">Select Status</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Dispatch">Dispatch</option>
                                            <option value="Partially Delivery" selected>Partially Delivery</option>
                                            <option value="Cancelled">Cancelled</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_billing') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_billing') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
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
@extends('layouts.common')
@section('title', 'Sales Order - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Sales Order</h4>
                <a class="btn btn-primary" href="{{ url('add_sale_order') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="filter-box">
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <h5>Fiter</h5>
                            </div>
                            <div class="col-md-4 col-lg-3 country">
                                <div class="form-floating form-floating-outline">
                                    <select name="country" id="country" class="form-select select2" data-placeholder="Select Customer/Buyer">
                                        <option value="">Select Customer/Buyer</option>
                                        <option value="Hero Mens Wear(CUS001)">Hero Mens Wear(CUS001)</option>
                                        <option value="Unlimited Fashion Store(CUS002)">Unlimited Fashion Store(CUS002)</option>
                                        <option value="Nikhil Jain(CUS003)">Nikhil Jain(CUS003)</option>
                                        <option value="Elite Garments Exporters(CUS004)">Elite Garments Exporters(CUS004)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3 status">
                                <select name="status" id="status" class="form-select select2" data-placeholder="Select Status">
                                    <option value="">Select Status</option>
                                    <option value="Pending">Pending</option>
                                    <option value="In Production">In Production</option>
                                    <option value="Dispatched">Dispatched</option>
                                    <option value="Cancelled">Cancelled</option>
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
                                    <th>SO Number</th>
                                    <th>SO Date</th>
                                    <th>Customer Name </th>
                                    <th>Customer PO Ref</th>
                                    <th>Total Items</th>
                                    <th>Expected Delivery Date </th>
                                    <th>Sales Agent</th>
                                    <th>Status</th>
                                    <th>Total Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>SO-1001</td>
                                    <td>20-09-2025</td>
                                    <td>Hero Mens Wear <span class="mini-title">(CUS001)</span></td>
                                    <td>PO-458</td>
                                    <td>2</td>
                                    <td>30-09-2025</td>
                                    <td>Neha Sharma <span class="mini-title">(SA102)</span></td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <select id="" class="select2 form-select" data-placeholder="">
                                                <option value="Pending">Pending</option>
                                                <option value="In Production">In Production</option>
                                                <option value="Dispatched">Dispatched</option>
                                                <option value="Cancelled">Cancelled</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>₹78,400</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_sale_order') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_sale_order') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>SO-1002</td>
                                    <td>17-09-2025</td>
                                    <td>Unlimited Fashion Store <span class="mini-title">(CUS002)</span></td>
                                    <td>PO-879</td>
                                    <td>1</td>
                                    <td>30-09-2025</td>
                                    <td>Neha Sharma <span class="mini-title">(SA102)</span></td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <select id="" class="select2 form-select" data-placeholder="">
                                                <option value="Pending">Pending</option>
                                                <option value="In Production">In Production</option>
                                                <option value="Dispatched">Dispatched</option>
                                                <option value="Cancelled">Cancelled</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>₹24,000</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_sale_order') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_sale_order') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
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
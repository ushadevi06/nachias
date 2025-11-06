@extends('layouts.common')
@section('title', 'Sales Invoices - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Sales Invoices</h4>
                <a class="btn btn-primary" href="{{ url('add_sale_invoice') }}">
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
                                    <option value="Draft">Draft</option>
                                    <option value="Finalized">Finalized</option>
                                    <option value="Paid">Paid</option>
                                    <option value="Partially Paid">Partially Paid</option>
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
                                    <th>Invoice No</th>
                                    <th>Invoice Date</th>
                                    <th>Customer Name / Buyer </th>
                                    <th>Linked SO No.</th>
                                    <th>Total Items</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>SINV-1001</td>
                                    <td>17-09-2025</td>
                                    <td>Hero Mens Wear <span class="mini-title">(CUS001)</span></td>
                                    <td>SO-1001</td>
                                    <td>2</td>
                                    <td>₹186.75</td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <select id="" class="select2 form-select" data-placeholder="">
                                                <option value="Draft">Draft</option>
                                                <option value="Finalized" selected>Finalized</option>
                                                <option value="Paid">Paid</option>
                                                <option value="Partially Paid">Partially Paid</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_sale_invoice') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_sale_invoice') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>SINV-1002</td>
                                    <td>17-09-2025</td>
                                    <td>Unlimited Fashion Store <span class="mini-title">(CUS002)</span></td>
                                    <td>SO-1002</td>
                                    <td>2</td>
                                    <td>₹110.36</td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <select id="" class="select2 form-select" data-placeholder="">
                                                <option value="Draft">Draft</option>
                                                <option value="Finalized">Finalized</option>
                                                <option value="Paid" selected>Paid</option>
                                                <option value="Partially Paid">Partially Paid</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_sale_invoice') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_sale_invoice') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
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
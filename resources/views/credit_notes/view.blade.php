@extends('layouts.common')
@section('title', 'Credit Notes - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Credit Notes</h4>
                <a class="btn btn-primary" href="{{ url('add_credit_note') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
            </div>

            <div class="card">
                <div class="card-body">

                    <!-- Filter Section -->
                    <div class="filter-box mb-4">
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <h5>Filter</h5>
                            </div>

                            <div class="col-md-4 col-lg-3">
                                <div class="form-floating form-floating-outline">
                                    <select name="customer" id="customer" class="form-select select2" data-placeholder="Select Customer/Buyer">
                                        <option value="">Select Customer/Buyer</option>
                                        <option value="Hero Mens Wear(CUS001)">Hero Mens Wear (CUS001)</option>
                                        <option value="Unlimited Fashion Store(CUS002)">Unlimited Fashion Store (CUS002)</option>
                                        <option value="Nikhil Jain(CUS003)">Nikhil Jain (CUS003)</option>
                                        <option value="Elite Garments Exporters(CUS004)">Elite Garments Exporters (CUS004)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4 col-lg-3">
                                <div class="form-floating form-floating-outline">
                                    <select name="reason" id="reason" class="form-select select2" data-placeholder="Select Reason">
                                        <option value="">Select Reason</option>
                                        <option value="Return">Return</option>
                                        <option value="Excess Billing">Excess Billing</option>
                                        <option value="Short Supply">Short Supply</option>
                                        <option value="Undercharge">Undercharge</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3 align-self-end">
                                <button type="button" class="btn btn-primary me-2">Filter</button>
                                <button type="button" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </div>

                    <!-- Table Section -->
                    <div class="card-datatable">
                        <table class="datatables-products table align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Note No.</th>
                                    <th>Date</th>
                                    <th>Linked Invoice No.</th>
                                    <th>Customer Name (Code)</th>
                                    <th>Reason</th>
                                    <th>Total Items</th>
                                    <th>Discount (₹)</th>
                                    <th>Tax (₹)</th>
                                    <th>Total Amount (₹)</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>NOTE-1001</td>
                                    <td>20-09-2025</td>
                                    <td>SINV-1001</td>
                                    <td>Hero Mens Wear <span class="mini-title">(CUS001)</span></td>
                                    <td><span class="badge text-bg-warning">Excess Quantity</span></td>
                                    <td>2</td>
                                    <td>₹100.00</td>
                                    <td>₹240.00</td>
                                    <td>₹1,440.00</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_credit_note') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_credit_debit_note') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>2</td>
                                    <td>NOTE-1002</td>
                                    <td>17-09-2025</td>
                                    <td>SINV-1002</td>
                                    <td>Unlimited Fashion Store <span class="mini-title">(CUS002)</span></td>
                                    <td><span class="badge text-bg-secondary">Price Adjustment</span></td>
                                    <td>2</td>
                                    <td>₹50.00</td>
                                    <td>₹45.25</td>
                                    <td>₹250.25</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_credit_note') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_credit_debit_note') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
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

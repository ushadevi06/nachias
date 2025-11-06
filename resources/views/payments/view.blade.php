@extends('layouts.common')
@section('title', 'Payments - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Payments</h4>
                <a class="btn btn-primary" href="{{ url('add_payment') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="filter-box mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-12">
                                <h5>Filter</h5>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <div class="form-floating form-floating-outline">
                                    <select name="payment_type" id="payment_type" class="form-select select2" data-placeholder="Select Payment Type">
                                        <option value="">Select Payment Type</option>
                                        <option value="Customer Collection">Customer Collection</option>
                                        <option value="Supplier Payment">Supplier Payment</option>
                                        <option value="Agent Commission">Agent Commission</option>
                                    </select>
                                    <label for="payment_type">Payment Type</label>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <div class="form-floating form-floating-outline">
                                    <select name="zone" id="zone" class="form-select select2" data-placeholder="Select Zone">
                                        <option value="">Select Zone</option>
                                        <option value="South Zone">South Zone</option>
                                        <option value="North Zone">North Zone</option>
                                        <option value="West Zone">West Zone</option>
                                    </select>
                                    <label for="zone">Zone</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary">Filter</button>
                                <button type="button" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </div>

                    <ul class="nav nav-tabs" id="paymentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">
                                Pending Payments
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab" aria-controls="completed" aria-selected="false">
                                Completed Payments
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content pt-3" id="paymentTabsContent">
                        <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                            <div class="card-datatable">
                                <table class="datatables-products table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Payment Type</th>
                                            <th>Reference Document</th>
                                            <th>Customer</th>
                                            <th>Supplier</th>
                                            <th>Payment Mode</th>
                                            <th>Amount Paid</th>
                                            <th>Balance Outstanding</th>
                                            <th>Payment Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Customer Collection</td>
                                            <td>SO(SO-1001)</td>
                                            <td>-</td>
                                            <td>Hero Mens Wear <span class="mini-title">(CUS001)</span></td>
                                            <td>Cash</td>
                                            <td>₹10,000</td>
                                            <td>₹7,900</td>
                                            <td>25-09-2025</td>
                                            <td>
                                                <select class="select2 form-select">
                                                    <option value="">Select Status</option>
                                                    <option value="Cleared">Cleared</option>
                                                    <option value="Pending" selected>Pending</option>
                                                    <option value="Partial">Partial</option>
                                                </select>
                                            </td>
                                            <td>
                                                <div class="button-box">
                                                    <a href="{{ url('view_payment') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                                    <a href="{{ url('add_payment') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                                    <a href="javascript:;" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                            <div class="card-datatable">
                                <table class="datatables-products table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Payment Type</th>
                                            <th>Reference Document</th>
                                            <th>Customer</th>
                                            <th>Supplier</th>
                                            <th>Payment Mode</th>
                                            <th>Amount Paid</th>
                                            <th>Balance Outstanding</th>
                                            <th>Payment Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Supplier Payment</td>
                                            <td>PO(PO-2025-001)</td>
                                            <td>Krishna Fabrics <span class="mini-title">(SUP001)</span></td>
                                            <td>-</td>
                                            <td>Online</td>
                                            <td>₹20,000</td>
                                            <td>₹6,900</td>
                                            <td>25-09-2025</td>
                                            <td>
                                                <select class="select2 form-select">
                                                    <option value="">Select Status</option>
                                                    <option value="Cleared" selected>Cleared</option>
                                                    <option value="Pending">Pending</option>
                                                    <option value="Partial">Partial</option>
                                                </select>
                                            </td>
                                            <td>
                                                <div class="button-box">
                                                    <a href="{{ url('view_payment') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                                    <a href="{{ url('add_payment') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
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
    </div>
</div>
@endsection
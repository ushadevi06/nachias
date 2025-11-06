@extends('layouts.common')
@section('title', 'Debit Notes - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Debit Notes</h4>
                <a class="btn btn-primary" href="{{ url('add_debit_note') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="filter-box">
                        <div class="row g-3">
                            <div class="col-md-4 col-lg-3 status">
                                <select name="status" id="status" class="form-select select2" data-placeholder="Select Status">
                                    <option value="">Select Status</option>
                                    <option value="Draft">Draft</option>
                                    <option value="Approved">Approved</option>
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
                        <table class="datatables-products table nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Debit Note No.</th>
                                    <th>Date</th>
                                    <th>Supplier Name</th>
                                    <th>Reference No.</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Total Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>DN-2025-001</td>
                                    <td>31-10-2025</td>
                                    <td>Krishna Fabrics <span class="mini-title">(SUP001)</span></td>
                                    <td>PO-2025-001</td>
                                    <td>Rate Difference</td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2 form-select" data-placeholder="">
                                                <option value="Draft">Draft</option>
                                                <option value="Approved" selected>Approved</option>
                                                <option value="Cancelled">Cancelled</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>₹5,250</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_debit_note') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_debit_note') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>DN-2025-002</td>
                                    <td>29-10-2025</td>
                                    <td>Vasanth Garments <span class="mini-title">(SUP003)</span></td>
                                    <td>PO-2025-003</td>
                                    <td>Short Supply</td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2 form-select">
                                                <option value="Draft" selected>Draft</option>
                                                <option value="Approved">Approved</option>
                                                <option value="Cancelled">Cancelled</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>₹2,700</td>
                                    <td>
                                        <div class="button-box">
                                            <a href="{{ url('view_debit_note') }}" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_debit_note') }}" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
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

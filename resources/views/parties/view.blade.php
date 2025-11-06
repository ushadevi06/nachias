@extends('layouts.common')
@section('title', 'Party - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Parties</h4>
        <a class="btn btn-primary" href="{{ url('add_party') }}">
            <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
        </a>
    </div>
    <div class="card p-5">
        <div class="card-header mb-4 d-flex justify-content-between align-items-center row pt-4">
            <div class="col-md-4 party_type">
                <select name="party_type" id="party_type" class="form-select">
                    <option value="">Select Party Type</option>
                    <option value="Supplier">Supplier</option>
                    <option value="Customer">Customer</option>
                </select>
            </div>

            <div class="col-md-4 status">
                <select name="status" id="status" class="form-select">
                    <option value="">Select Status</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-primary">Filter</button>
                <button type="button" class="btn btn-secondary">Reset</button>
            </div>
        </div>
        <div class="card-datatable">
            <table class="datatables-products table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Party Type</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Country</th>
                        <th>State</th>
                        <th>city</th>
                        <th>Service Point</th>
                        <th>Tax Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Supplier</td>
                        <td>1001</td>
                        <td>4 Star Packing(SUP001)</td>
                        <td>India</td>
                        <td>TamilNadu</td>
                        <td>Chennai</td>
                        <td>Tambaram</td>
                        <td>CGST 9</td>
                        <td><span class="badge rounded-pill bg-label-danger" text-capitalized="">Inactive</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <a href="{{ url('add_party') }}" class="btn btn-icon btn-text-secondary rounded-pill"><i class="icon-base ri ri-edit-box-fill"></i></a>
                                <a href="javascript:;" class="btn btn-sm btn-icon btn-text-secondary rounded-pill"><i class="icon-base ri ri-delete-bin-7-line icon-22px"></i></a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Customer</td>
                        <td>6001</td>
                        <td>Hero Mens Wear(CUS001)</td>
                        <td>India</td>
                        <td>TamilNadu</td>
                        <td>Madurai</td>
                        <td>Jaihindpuram</td>
                        <td>CGST 9</td>
                        <td><span class="badge rounded-pill bg-label-success" text-capitalized="">Active</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <a href="{{ url('add_party') }}" class="btn btn-icon btn-text-secondary rounded-pill"><i class="icon-base ri ri-edit-box-fill"></i></a>
                                <a href="javascript:;" class="btn btn-sm btn-icon btn-text-secondary rounded-pill"><i class="icon-base ri ri-delete-bin-7-line delete-btn icon-22px"></i></a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Customer</td>
                        <td>6003</td>
                        <td>Unlimited Fashion Store </td>
                        <td>India</td>
                        <td>TamilNadu</td>
                        <td>Madurai</td>
                        <td>Arapalayam</td>
                        <td>CGST 9</td>
                        <td><span class="badge rounded-pill bg-label-success" text-capitalized="">Active</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <a href="{{ url('add_party') }}" class="btn btn-icon btn-text-secondary rounded-pill"><i class="icon-base ri ri-edit-box-fill"></i></a>
                                <a href="javascript:;" class="btn btn-sm btn-icon btn-text-secondary rounded-pill"><i class="icon-base ri ri-delete-bin-7-line delete-btn icon-22px"></i></a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
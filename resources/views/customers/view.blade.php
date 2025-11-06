@extends('layouts.common')
@section('title', 'Customers - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Customers</h4>
                <a class="btn btn-primary" href="{{ url('add_customer') }}">
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
                            <div class="col-md-4 col-lg-3 party_type">
                                <select name="category" id="category" class="form-select select2" data-placeholder="Select Category">
                                    <option value="">Select Category</option>
                                    <option value="Retailer">Retailer</option>
                                    <option value="Wholesaler">Wholesaler</option>
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
                                    <th>Contact Info</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr>
                                    <td>1</td>
                                    <td>Hero Mens Wear <span class="mini-title">(CUS001)</span></td>
                                    <td>
                                        <div class="contact-info">
                                            <div><i class="ri ri-mail-line icon-email"></i> heromenswear@gmail.com</div>
                                            <div><i class="ri ri-phone-line icon-phone"></i> 9870123489</div>
                                        </div>
                                    </td>
                                    <td>Retailer</td>
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
                                            <a href="{{ url('view_customer') }}" title="View" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_customer') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" title="Delete" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>2</td>
                                    <td>Unlimited Fashion Store <span class="mini-title">(CUS002)</span></td>
                                    <td>
                                        <div class="contact-info">
                                            <div><i class="ri ri-mail-line icon-email"></i> unlimitedfashionstore@gmail.com</div>
                                            <div><i class="ri ri-phone-line icon-phone"></i> 8520369741</div>
                                        </div>
                                    </td>
                                    <td>Retailer</td>
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
                                            <a href="{{ url('view_customer') }}" title="View" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_customer') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" title="Delete" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>3</td>
                                    <td>Nikhil Jain <span class="mini-title">(CUS003)</span></td>
                                    <td>
                                        <div class="contact-info">
                                            <div><i class="ri ri-mail-line icon-email"></i> nikhil.jain@trendzapparel.com</div>
                                            <div><i class="ri ri-phone-line icon-phone"></i> 9988223344</div>
                                        </div>
                                    </td>
                                    <td>Wholesaler</td>
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
                                            <a href="{{ url('view_customer') }}" title="View" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_customer') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" title="Delete" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>4</td>
                                    <td>Elite Garments Exporters <span class="mini-title">(CUS004)</span></td>
                                    <td>
                                        <div class="contact-info">
                                            <div><i class="ri ri-mail-line icon-email"></i> rohit.malhotra@elitegarments.com</div>
                                            <div><i class="ri ri-phone-line icon-phone"></i> 9810098765</div>
                                        </div>
                                    </td>
                                    <td>Wholesaler</td>
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
                                            <a href="{{ url('view_customer') }}" title="View" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_customer') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
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
    </div>
</div>
@endsection

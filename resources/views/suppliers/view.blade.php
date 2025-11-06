@extends('layouts.common')
@section('title', 'Suppliers - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Suppliers</h4>
                <a class="btn btn-primary" href="{{ url('add_supplier') }}">
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
                                <select name="supplier_type" id="supplier_type" class="form-select select2" data-placeholder="Select Supplier Type">
                                    <option value="">Select Supplier Type</option>
                                    <option value="Fabrics">Fabrics</option>
                                    <option value="Accessories">Accessories</option>
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
                                    <th>Supplier Type</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>

                                <!-- 1 -->
                                <tr>
                                    <td>1</td>
                                    <td>Krishna Fabrics <span class="mini-title">(SUP001)</span></td>
                                    <td>
                                        <div class="contact-info">
                                            <div><i class="ri ri-mail-line icon-email"></i> krishnafabrics12@gmail.com</div>
                                            <div><i class="ri ri-phone-line icon-phone"></i> 9080706050</div>
                                        </div>
                                    </td>
                                    <td>Fabrics</td>
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
                                            <a href="{{ url('view_supplier') }}" title="View" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_supplier') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" title="Delete" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>

                                <!-- 2 -->
                                <tr>
                                    <td>2</td>
                                    <td>Vasanth Garments <span class="mini-title">(SUP002)</span></td>
                                    <td>
                                        <div class="contact-info">
                                            <div><i class="ri ri-mail-line icon-email"></i> vasanthgarments21@gmail.com</div>
                                            <div><i class="ri ri-phone-line icon-phone"></i> 9870123489</div>
                                        </div>
                                    </td>
                                    <td>Fabrics</td>
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
                                            <a href="{{ url('view_supplier') }}" title="View" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_supplier') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" title="Delete" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>

                                <!-- 3 -->
                                <tr>
                                    <td>3</td>
                                    <td>Jaya Fabrics <span class="mini-title">(SUP003)</span></td>
                                    <td>
                                        <div class="contact-info">
                                            <div><i class="ri ri-mail-line icon-email"></i> jayafabrics1998@gmail.com</div>
                                            <div><i class="ri ri-phone-line icon-phone"></i> 8520369741</div>
                                        </div>
                                    </td>
                                    <td>Fabrics</td>
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
                                            <a href="{{ url('view_supplier') }}" title="View" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_supplier') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" title="Delete" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>

                                <!-- 4 -->
                                <tr>
                                    <td>4</td>
                                    <td>Sri Meena Traders <span class="mini-title">(SUP004)</span></td>
                                    <td>
                                        <div class="contact-info">
                                            <div><i class="ri ri-mail-line icon-email"></i> grasim.secretarial@adityabirla.com</div>
                                            <div><i class="ri ri-phone-line icon-phone"></i> 9630230230</div>
                                        </div>
                                    </td>
                                    <td>Accessories</td>
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
                                            <a href="{{ url('view_supplier') }}" title="View" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_supplier') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" title="Delete" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>

                                <!-- 5 -->
                                <tr>
                                    <td>5</td>
                                    <td>Royal Stitch Wear <span class="mini-title">(SUP005)</span></td>
                                    <td>
                                        <div class="contact-info">
                                            <div><i class="ri ri-mail-line icon-email"></i> royalstitchwear23@gmail.com</div>
                                            <div><i class="ri ri-phone-line icon-phone"></i> 9843011122</div>
                                        </div>
                                    </td>
                                    <td>Accessories</td>
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
                                            <a href="{{ url('view_supplier') }}" title="View" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_supplier') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
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

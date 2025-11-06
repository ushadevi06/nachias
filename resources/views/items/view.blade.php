@extends('layouts.common')
@section('title', 'Items - ' . env('WEBSITE_NAME'))
@section('content')
<!-- / Menu -->
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Items </h4>
                <a class="btn btn-primary" href="{{ url('add_item') }}">
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
                            <div class="col-md-4 col-lg-3">
                                <select id="item_category" class="select2 form-select" data-placeholder="Select Item Category">
                                    <option value="">Select Item Category</option>
                                    <option value="	Formal Shirts(IC001)">Formal Shirts(IC001)</option>
                                    <option value="Casual Shirts(IC002)">Casual Shirts(IC002)</option>
                                    <option value="Uniform Shirts(IC003)">Uniform Shirts(IC003)</option>
                                    <option value="Kids Shirts(IC004)">Kids Shirts(IC004)</option>
                                    <option value="Premium Shirts(IC004)">Premium Shirts(IC005)</option>
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
                                    <th>Item Category</th>
                                    <th>Name</th>
                                    <th>Brand</th>
                                    <th>Color</th>
                                    <th>UOM</th>
                                    <th>Barcode</th>
                                    <th>Pricing(Wholesale / Retail / Export)</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Formal Shirts <span class="mini-title">(IC001)</span></td>
                                    <td>Men’s Formal Cotton Shirt <span class="mini-title">(ITEM001)</span></td>
                                    <td>Hero Mens Wear</td>
                                    <td>White, Blue</td>
                                    <td>PCS</td>
                                    <td>890100000001</td>
                                    <td>450 / 550 / 600</td>
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
                                            <a href="{{ url('view_item') }}" title="View" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_item') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" title="Delete" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Casual Shirts <span class="mini-title">(IC002)</span></td>
                                    <td>Men’s Casual Denim Shirt <span class="mini-title">(ITEM002)</span></td>
                                    <td>Unlimited Fashion</td>
                                    <td>Red, Blue, Black</td>
                                    <td>PCS</td>
                                    <td>890100000002</td>
                                    <td>400 / 500 / 550</td>
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
                                            <a href="{{ url('view_item') }}" title="View" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_item') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" title="Delete" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Uniform Shirts <span class="mini-title">(IC003)</span></td>
                                    <td>School Uniform Shirt <span class="mini-title">(ITEM003)</span></td>
                                    <td>EduWear</td>
                                    <td>White</td>
                                    <td>PCS</td>
                                    <td>890100000003</td>
                                    <td>350 / 450 / 500</td>
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
                                            <a href="{{ url('view_item') }}" title="View" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_item') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" title="Delete" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Kids Shirts <span class="mini-title">(IC004)</span></td>
                                    <td>Kids Polo Shirt <span class="mini-title">(ITEM004)</span></td>
                                    <td>TinyWear</td>
                                    <td>Yellow, Green</td>
                                    <td>PCS</td>
                                    <td>890100000004</td>
                                    <td>250 / 300 / 350</td>
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
                                            <a href="{{ url('view_item') }}" title="View" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_item') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                                            <a href="javascript:;" title="Delete" class="btn btn-delete"><i class="icon-base ri ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>Premium Shirts <span class="mini-title">(IC005)</span></td>
                                    <td>Premium Linen Shirt <span class="mini-title">(ITEM005)</span></td>
                                    <td>LuxeWear</td>
                                    <td>White, Navy</td>
                                    <td>PCS</td>
                                    <td>890100000005</td>
                                    <td>900 / 1100 / 1200</td>
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
                                            <a href="{{ url('view_item') }}" title="View" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                                            <a href="{{ url('add_item') }}" title="Edit" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
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
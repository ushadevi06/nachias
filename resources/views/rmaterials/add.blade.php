@extends('layouts.common')
@section('title', 'Add Raw Material - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>Add Raw Material</h4>
                    </div>
                    <form action="" method="POST" class="common-form">
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="code" placeholder="Enter Code" name="country_code" value="M001">
                                    <label for="code">Code *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="store_category" class="select2 form-select" data-placeholder="Select Store Category">
                                        <option value="">Select Store Category</option>
                                        <option value="Fabric(MC001)">Fabric(MC001)</option>
                                        <option value="Accessories(MC002)">Accessories(MC002)</option>
                                        <option value="Trims(MC003)">Trims(MC003)</option>
                                        <option value="Thread(MC004)">Thread(MC004)</option>
                                        <option value="Buttons(MC005)">Buttons(MC005)</option>
                                    </select>
                                    <label for="store_category">Store Category </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="name" placeholder="Enter Name" name="name">
                                    <label for="name">Name *</label>
                                </div>
                            </div>
                             <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="supplier_design_name" placeholder="Enter Supplier Design Name" name="name">
                                    <label for="name">Supplier Design Name *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="color" class="select2 form-select" data-placeholder="Select Color">
                                        <option value="">Select Color</option>
                                        <option value="Blue">Blue</option>
                                        <option value="White">White</option>
                                        <option value="Red">Red</option>
                                    </select>
                                    <label for="color">Color</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="code" placeholder="Enter Size / Width" name="size">
                                    <label for="code">Size / Width</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="UOM" class="select2 form-select" data-placeholder="Select UOM">
                                        <option value="">Select Raw Material Category</option>
                                        <option value="MTR">MTR</option>
                                        <option value="PCS">PCS</option>
                                        <option value="ROLL">ROLL</option>
                                    </select>
                                    <label for="UOM">UOM</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="fabric_type" class="select2 form-select" data-placeholder="Select Fabric Type">
                                        <option value="">Select Fabric Type</option>
                                        <option value="Polyester">Polyester</option>
                                        <option value="Polycotton">Polycotton</option>
                                    </select>
                                    <label for="fabric_type">Fabric Type</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="file" id="formFile" multiple>
                                    <label for="formFile" class="form-label">Reference Images </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100" id="specifications" placeholder="Enter Specifications / Quality Notes"></textarea>
                                    <label for="address">Specifications / Quality Notes</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="code" placeholder="Enter Minimum Stock Level" name="min_stock">
                                    <label for="code">Minimum Stock Level</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="status" class="select2 form-select" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                    <label for="status">Status</label>
                                </div>
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('rmaterials') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
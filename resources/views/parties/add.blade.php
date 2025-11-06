@extends('layouts.common')
@section('title', 'Add Party - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card mb-6">
                <div class="card-header">
                    <h5 class="card-title mb-0">Add Party</h5>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <h6 class="mb-5">Identification & Contact:</h6>
                        <div class="row mb-4">
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="" class="select2 form-select" data-placeholder="Select Party Type">
                                        <option value="">Select Party Type</option>
                                        <option value="Supplier">Supplier</option>
                                        <option value="Customer">Customer</option>
                                    </select>
                                    <label for="select2Basic">Party Type * <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="name" placeholder="Enter Name" name="name">
                                    <label for="name">Name * </label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="code" placeholder="Enter Code" name="name">
                                    <label for="code">Code * </label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline mb-6">
                                    <textarea class="form-control h-px-100" id="address" placeholder="Enter Address"></textarea>
                                    <label for="address">Address *</label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="code" placeholder="Enter ZipCode" name="name">
                                    <label for="code">Zip Code </label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="code" placeholder="Enter Telephone" name="name">
                                    <label for="code">Telephone </label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="code" placeholder="Enter Mobile Number" name="name">
                                    <label for="code">Mobile Number * </label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="code" placeholder="Enter Email" name="name">
                                    <label for="code">Email * </label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="code" placeholder="Enter Website URL" name="name">
                                    <label for="code">Website URL * </label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h6 class="mb-5">Location Details:</h6>
                        <div class="row mb-4">
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="" class="select2 form-select" data-placeholder="Select Country">
                                        <option value="">Select Country</option>
                                        <option value="India">India</option>
                                    </select>
                                    <label for="select2Basic">Country</label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="" class="select2 form-select" data-placeholder="Select State">
                                        <option value="">Select State</option>
                                        <option value="Andhra Pradesh">Andhra Pradesh</option>
                                        <option value="Tamil Nadu">Tamil Nadu</option>
                                        <option value="Kerala">Kerala</option>
                                    </select>
                                    <label for="select2Basic">State</label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="" class="select2 form-select" data-placeholder="Select City">
                                        <option value="">Select City</option>
                                        <option value="Chennai">Chennai</option>
                                        <option value="Madurai">Madurai</option>
                                        <option value="Erode">Erode</option>
                                        <option value="Trichy">Trichy</option>
                                    </select>
                                    <label for="select2Basic">City</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="" class="select2 form-select" data-placeholder="Select Service Point">
                                        <option value="">Select Service Point</option>
                                        <option value="Vilangudi">Vilangudi</option>
                                        <option value="Jaihindpuram">Jaihindpuram</option>
                                        <option value="Arapalayam">Arapalayam</option>
                                    </select>
                                    <label for="select2Basic">Service Point</label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h6>Contact Information: </h6>
                        <div class="row mb-4 mt-4">
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="Contact Person Name" placeholder="Enter Contact Person Name" name="phn_no">
                                    <label for="Contact Person Name">Contact Person Name</label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="designation" placeholder="Enter Designation" name="designation">
                                    <label for="designation">Designation</label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="phn_no" placeholder="Enter Alternate Phone Number" name="phn_no">
                                    <label for="phn_no">Alternate Phone Number</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="email" placeholder="Enter Alternate Email" name="email">
                                    <label for="email">Alternate Email</label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="Tax Type" placeholder="Enter Tax & Compliance" name="tax_type">
                                    <label for="name">Tax & Compliance</label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="gst_no" placeholder="Enter PAN No" name="gst_no">
                                    <label for="name">PAN No</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="gst_no" placeholder="Enter GST No" name="gst_no">
                                    <label for="name">GST No</label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="Zipcode" placeholder="Enter ECC No" name="phn_no">
                                    <label for="name">ECC No</label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline mb-6">
                                    <textarea class="form-control h-px-100" id="address" placeholder="Enter Remarks"></textarea>
                                    <label for="address">Remarks </label>
                                </div>
                            </div>
                        </div>


                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ url('parties') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
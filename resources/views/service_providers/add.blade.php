@extends('layouts.common')
@section('title', 'Add Service Provider - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>Add Service Provider</h4>
                    </div>
                    <form action="" method="POST" class="common-form">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <h6>Identification & Contact:</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="" class="select2 form-select" data-placeholder="Select Service Type">
                                        <option value="">Select Service Type</option>
                                        <option value="Dyeing">Dyeing</option>
                                        <option value="Printing">Printing</option>
                                        <option value="Embroidery">Embroidery</option>
                                        <option value="Washing">Washing</option>
                                        <option value="Transport">Transport</option>
                                        <option value="Logistics">Logistics</option>
                                    </select>
                                    <label for="select2Basic">Service Type * <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="name" placeholder="Enter Name" name="name">
                                    <label for="name">Name * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="code" placeholder="Enter Code" name="code">
                                    <label for="code">Code * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="email" placeholder="Enter Email" name="email">
                                    <label for="email">Email  </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="mobile_no" placeholder="Enter Mobile Number" name="mobile_no">
                                    <label for="code">Mobile Number * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="zip_code" placeholder="Enter ZipCode" name="zip_code">
                                    <label for="code">Zip Code </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="website_url" placeholder="Enter Website URL" name="website_url">
                                    <label for="website_url">Website URL </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="" class="select2 form-select" data-placeholder="Select Service Rate">
                                        <option value="">Select Service Rate</option>
                                        <option value="Per Agent">Per Agent</option>
                                        <option value="Job Type">Job Type</option>
                                    </select>
                                    <label for="select2Basic">Service Rate * <span class="text-danger">*</span></label>
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
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h6>Location Details:</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="" class="select2 form-select" data-placeholder="Select Country">
                                        <option value="">Select Country</option>
                                        <option value="India">India</option>
                                    </select>
                                    <label for="select2Basic">Country</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
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
                            <div class="col-md-6 col-xl-4">
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
                            <div class="col-md-6 col-xl-4">
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
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline mb-6">
                                    <input type="text" class="form-control" id="address" placeholder="Enter Address Line 1">
                                    <label for="address">Address Line 1 *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="address" placeholder="Enter Address Line 2">
                                    <label for="address">Address Line 2</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="code" placeholder="Enter ZipCode" name="name">
                                    <label for="code">Zip Code </label>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h6>Contact Information: </h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="Contact Person Name" placeholder="Enter Contact Person Name" name="phn_no">
                                    <label for="Contact Person Name">Contact Person Name</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="designation" placeholder="Enter Designation" name="designation">
                                    <label for="designation">Designation</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="phn_no" placeholder="Enter Phone Number" name="phn_no">
                                    <label for="phn_no">Phone Number</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="email" placeholder="Enter Email" name="email">
                                    <label for="email">Email</label>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h6>Tax & Compliance: </h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="gst_no" placeholder="Enter PAN No" name="gst_no">
                                    <label for="name">PAN No</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="gst_no" placeholder="Enter GST No" name="gst_no">
                                    <label for="name">GST No</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100" id="address" placeholder="Enter Remarks"></textarea>
                                    <label for="address">Remarks </label>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h6>Financial / Payment:</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="bank_name" placeholder="Enter Bank Name" name="bank_name">
                                    <label for="bank_name">Bank Name </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="bank_acc_no" placeholder="Enter Bank Account No" name="bank_acc_no">
                                    <label for="bank_acc_no">Bank Account No </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="ifsc_code" placeholder="Enter IFSC Code" name="ifsc_code">
                                    <label for="ifsc_code">IFSC Code </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100" id="payment_terms" placeholder="Enter Payment Terms"></textarea>
                                    <label for="address">Payment Terms </label>
                                </div>
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('service_providers') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
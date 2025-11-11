@extends('layouts.common')
@section('title', 'Add Customer - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>Add Customer</h4>
                    </div>
                    <form action="" method="POST" class="common-form">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <h6>Identification & Contact:</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <label class="form-label d-block mb-2">Category *</label>
                                <div class="d-flex align-items-center gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="category" id="category_retailer" value="Retailer" checked>
                                        <label class="form-check-label" for="category_retailer">
                                            Retailer
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="category" id="category_wholesaler" value="Wholesaler">
                                        <label class="form-check-label" for="category_wholesaler">
                                            Wholesaler
                                        </label>
                                    </div>
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
                                    <input type="text" class="form-control" id="code" placeholder="Enter Code" name="name">
                                    <label for="code">Code * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="mobile_no" placeholder="Enter Mobile Number" name="name">
                                    <label for="code">Mobile Number * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="email" placeholder="Enter Email" name="name">
                                    <label for="code">Email </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="website_url" placeholder="Enter Website URL" name="name">
                                    <label for="code">Website URL </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="transport_name" placeholder="Enter Transport Name" name="name">
                                    <label for="transport_name">Transport Name </label>
                                </div>
                            </div> 
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="booking_office" placeholder="Enter Booking Office" name="booking_office">
                                    <label for="booking_office">Booking Office </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="" class="select2 form-select" data-placeholder="Select Zone">
                                        <option value="">Select Zone</option>
                                        <option value="South Zone">South Zone</option>
                                        <option value="North Zone">North Zone</option>
                                        <option value="West Zone">West Zone</option>
                                        <option value="Central Zone">Central Zone</option>
                                    </select>
                                    <label for="zone_id">Zone * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="" class="select2 form-select" data-placeholder="Select Stores">
                                        <option value="">Select Stores</option>
                                        <option value="Fabric">Fabric</option>
                                        <option value="Finished Goods">Finished Goods</option>
                                    </select>
                                    <label for="stores">Stores </label>
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
                                        <option value="United States">United States</option>
                                        <option value="United Kingdom">United Kingdom</option>
                                        <option value="Canada">Canada</option>
                                        <option value="Australia">Australia</option>
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
                                    <select id="" class="select2 form-select" data-placeholder="Select Place">
                                        <option value="">Select Place</option>
                                        <option value="Vilangudi">Vilangudi</option>
                                        <option value="Jaihindpuram">Jaihindpuram</option>
                                        <option value="Arapalayam">Arapalayam</option>
                                    </select>
                                    <label for="select2Basic">Place</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline mb-6">
                                    <input type="text" class="form-control" id="address_line_1" placeholder="Enter Address Line 1">
                                    <label for="address">Address Line 1 *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="address_line_2" placeholder="Enter Address Line 2">
                                    <label for="address">Address Line 2</label>
                                </div>
                            </div> 
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="address_line_3" placeholder="Enter Address Line 3">
                                    <label for="address">Address Line 3</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="zip_code" placeholder="Enter ZipCode" name="name">
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
                                    <input type="text" class="form-control" id="phn_no" placeholder="Enter Mobile Number" name="phn_no">
                                    <label for="phn_no">Mobile Number</label>
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
                                <h6>Commission Information: </h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="" class="select2 form-select" data-placeholder="Select Sales Agent">
                                        <option value="">Select Sales Agent</option>
                                        <option value="Amit Kumar(SA101)">Amit Kumar(SA101)</option>
                                        <option value="Neha Sharma(SA102)">Neha Sharma(SA102)</option>
                                    </select>
                                    <label for="select2Basic">Sales Agent</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="commission" placeholder="Enter Commission (%)" name="commission">
                                    <label for="name">Commission (%)</label>
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
                                    <input type="text" class="form-control" id="gst_no" placeholder="Enter GST No" name="gst_no">
                                    <label for="name">GST No</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="" class="select2 form-select" data-placeholder="Select Tax Types">
                                        <option value="">Select Tax Types</option>
                                        <option value="Button (5%)">Button(5%)</option>
                                        <option value="Collar (12%)">Collar (12%)</option>
                                        <option value="Polyester (2.5%)">CGST2.5</option>
                                        <option value="Zippers (2.5%)">SGST2.5</option>
                                        <option value="Thread (6%)">Thread (6%)</option>
                                        <option value="Cuffs (6%)">Cuffs (6%)</option>
                                    </select>
                                    <label for="name">Tax Types</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="pan_no" placeholder="Enter PAN No" name="pan_no">
                                    <label for="name">PAN No</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline mb-6">
                                    <textarea class="form-control h-px-100" id="payment_terms" placeholder="Enter Payment Terms"></textarea>
                                    <label for="payment_terms">Payment Terms </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="credit_limit" placeholder="Enter Credit Limit" name="credit_limit">
                                    <label for="name">Credit Limit</label>
                                </div>
                            </div> 
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="sales_discount" placeholder="Enter Sales Discount" name="sales_discount">
                                    <label for="sales_discount">Sales Discount (%)</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="box_discount" placeholder="Enter Box Discount" name="box_discount">
                                    <label for="box_discount">Box Discount (%)</label>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h6>Bank Information: </h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="bank_name" placeholder="Enter Bank Name" name="bank_name">
                                    <label for="bank_name">Bank Name</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="branch" placeholder="Enter Branch" name="branch">
                                    <label for="branch">Branch</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="acc_no" placeholder="Enter Account Number" name="acc_no">
                                    <label for="acc_no">Account Number</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="ifsc_code" placeholder="Enter IFSC Code" name="ifsc_code">
                                    <label for="ifsc_code">IFSC Code</label>
                                </div>
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('customers') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
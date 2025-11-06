@extends('layouts.common')
@section('title', 'Add Employee - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>Add Employee</h4>
                    </div>
                    <form action="" method="POST" class="common-form">
                        <div class="row g-4 mb-4">
                            <div class="col-lg-12">
                                <h6>Employee Details:</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class=" form-floating form-floating-outline">
                                    <select id="department" class="form-select select2" data-placeholder="Select Department" required>
                                        <option value="">Select Department</option>
                                        <option value="Cutting">Cutting</option>
                                        <option value="Stiching">Stiching</option>
                                        <option value="Ironing">Ironing</option>
                                        <option value="Quality Control">Quality Control</option>
                                        <option value="Packing">Packing</option>
                                    </select>
                                    <label for="department">Department *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class=" form-floating form-floating-outline">
                                    <select id="role" class="form-select select2" data-placeholder="Select Role" required>
                                        <option value="">Select Role</option>
                                        <option value="Manager">Manager</option>
                                        <option value="Supervisior">Supervisior</option>
                                        <option value="Store Keeper">Store Keeper</option>
                                        <option value="Production Head">Production Head</option>
                                        <option value="Accountant">Accountant</option>
                                    </select>
                                    <label for="role">Role *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class=" form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="name" placeholder="Enter Employee ID" name="emp_id" value="EMP001" readonly>
                                    <label for="emp_id">Employee ID * </label>
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
                                    <input type="text" class="form-control" id="email" placeholder="Enter Email" name="email">
                                    <label for="email">Email  </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="phn_no" placeholder="Enter Phone Number" name="phn_no">
                                    <label for="name">Phone Number *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="doj" name="doj" placeholder="Date of Joining">
                                    <label for="doj">Date of Joining</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="blood_group" class="form-select select2" data-placeholder="Select Blood Group" required>
                                        <option value="">Select Blood Group</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                    </select>
                                    <label for="blood_grp">Blood Group</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="father_name" name="father_name" placeholder="Enter Father's Name">
                                    <label for="father_name">Father's Name</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="father_phn_no" name="father_phn_no" placeholder="Enter Father's Phone Number">
                                    <label for="father_phn_no">Father's Phone Number</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                 <div class="form-floating form-floating-outline">
                                    <input type="file" class="form-control" id="image" name="image">
                                    <label for="formFile" class="form-label">Profile Picture</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="password" class="form-control" id="phn_no" placeholder="Enter Password" name="password">
                                    <label for="name">Password *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="" class="select2 form-select" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                    <label for="select2Basic">Status</label>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h6>Address Information:</h6>
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
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control" id="address_line1" name="address_line1" placeholder="Enter Address Line 1">
                                    <label for="address_line1">Address Line 1 *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="address_line2" name="address_line2" placeholder="Enter Address Line 2">
                                    <label for="address_line2">Address Line 2</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="zipcode" placeholder="Enter Zip code" name="zipcode">
                                    <label for="basic_salary">Zipcode</label>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h6>Contact Information:</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="contact_person_name" placeholder="Enter Contact Person Name" name="contact_person_name">
                                    <label for="contact_person_name">Contact Person Name</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="contact_person_phn_no" placeholder="Enter Contact Person Phone Number" name="contact_person_phn_no">
                                    <label for="contact_person_phn_no">Contact Person Phone Number</label>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h6>Salary Structure:</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="basic_salary" placeholder="Enter Basic Salary" name="basic_salary">
                                    <label for="basic_salary">Basic Salary</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="hra" placeholder="Enter HRA" name="hra">
                                    <label for="hra">HRA</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="allowances" placeholder="Enter Allowances" name="allowances">
                                    <label for="allowances">Allowances</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="deductions" placeholder="Enter Deductions" name="deductions">
                                    <label for="deductions">Deductions</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="gross_salary" placeholder="Enter Gross Salary" name="gross_salary">
                                    <label for="gross_salary">Gross Salary</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="net_salary" placeholder="Enter Net Salary" name="net_salary">
                                    <label for="net_salary">Net Salary</label>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h6>Tax & Compilance:</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                 <div class="form-floating form-floating-outline">
                                    <input type="file" class="form-control" id="esi" name="esi">
                                    <label for="formFile" class="form-label">ESI Document</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                 <div class="form-floating form-floating-outline">
                                    <input type="file" class="form-control" id="pf" name="pf">
                                    <label for="formFile" class="form-label">PF Document</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                 <div class="form-floating form-floating-outline">
                                    <input type="file" class="form-control" id="aadhaar" name="aadhaar">
                                    <label for="formFile" class="form-label">Aadhaar Card</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                 <div class="form-floating form-floating-outline">
                                    <input type="file" class="form-control" id="pan" name="pan">
                                    <label for="formFile" class="form-label">Pan Card</label>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h6>Bank Details:</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="ac_no" placeholder="Enter A/C No." name="ac_no">
                                    <label for="ac_no">A/C No.</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="bank_name" placeholder="Enter Bank Name" name="bank_name">
                                    <label for="bank_name">Bank Name</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="ifsc" placeholder="Enter IFSC Code" name="ifsc">
                                    <label for="ifsc">IFSC Code</label>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="button-box">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <a href="{{ url('employees') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
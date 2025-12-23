@extends('layouts.common')
@section('title', ($employee ? 'Edit' : 'Add') . ' Employee - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $employee ? 'Edit' : 'Add' }} Employee</h4>
                    </div>
                    <form action="{{ url('employees/add' . ($employee ? '/' . $employee->id : '')) }}" method="POST" class="common-form" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-4 mb-4">
                            <div class="col-lg-12">
                                <h6>Employee Details:</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="department_id" name="department_id" class="form-select select2 @error('department_id') is-invalid @enderror" data-placeholder="Select Department">
                                        <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id ?? '') == $department->id ? 'selected' : '' }}>{{ $department->department }}</option>
                                        @endforeach
                                    </select>
                                    <label for="department_id">Department <span class="text-danger">*</span></label>
                                </div>
                                @error('department_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="role_id" name="role_id" class="form-select select2 @error('role_id') is-invalid @enderror" data-placeholder="Select Role">
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id', $employee->role_id ?? '') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="role_id">Role <span class="text-danger">*</span></label>
                                </div>
                                @error('role_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('emp_id') is-invalid @enderror" id="emp_id" placeholder="Employee ID" name="emp_id" value="{{ old('emp_id', $employee->emp_id ?? '') }}">
                                    <label for="emp_id">Employee ID <span class="text-danger">*</span></label>
                                </div>
                                @error('emp_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter Name" name="name" value="{{ old('name', $employee->name ?? '') }}">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                </div>
                                @error('name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Enter Email" name="email" value="{{ old('email', $employee->email ?? '') }}">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                </div>
                                @error('email')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" placeholder="Enter Phone Number" name="phone" value="{{ old('phone', $employee->phone ?? '') }}">
                                    <label for="phone">Phone Number <span class="text-danger">*</span></label>
                                </div>
                                @error('phone')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="date" class="form-control" id="date_of_joining" name="date_of_joining" placeholder="Date of Joining" value="{{ old('date_of_joining', \Carbon\Carbon::parse($employee?->date_of_joining)->format('Y-m-d')) }}">
                                    <label for="date_of_joining">Date of Joining</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="blood_group_id" name="blood_group_id" class="form-select select2" data-placeholder="Select Blood Group">
                                        <option value="">Select Blood Group</option>
                                        @foreach($bloodGroups as $bloodGroup)
                                        <option value="{{ $bloodGroup->id }}" {{ old('blood_group_id', $employee->blood_group_id ?? '') == $bloodGroup->id ? 'selected' : '' }}>{{ $bloodGroup->blood_grp_name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="blood_group_id">Blood Group</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="father_name" name="father_name" placeholder="Enter Father's Name" value="{{ old('father_name', $employee->father_name ?? '') }}">
                                    <label for="father_name">Father's Name</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="father_phone" name="father_phone" placeholder="Enter Father's Phone Number" value="{{ old('father_phone', $employee->father_phone ?? '') }}">
                                    <label for="father_phone">Father's Phone Number</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="file" class="form-control" id="image" name="image" accept="image/jpeg,image/png,image/webp">
                                    <label for="image">Picture</label>
                                </div>
                                @if($employee && $employee->profile_image)
                                <div class="file-preview">
                                    <a href="{{ asset('uploads/employee/' . $employee->id . '/' . $employee->profile_image) }}" target="_blank"><i class="ri-file-line"></i> View Profile Image</a>
                                </div>
                                @endif
                                @error('image')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Enter Password" name="password">
                                    <label for="password">Password {{ !$employee ? '*' : '' }}</label>
                                </div>
                                @if($employee)
                                <small class="text-muted">Leave blank to keep current password</small>
                                @endif
                                @error('password')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active" {{ old('status', $employee->status ?? 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status', $employee->status ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <label for="status">Status</label>
                                </div>
                                @error('status')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h6>Address Information:</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="state_id" name="state_id" class="select2 form-select @error('state_id') is-invalid @enderror" data-placeholder="Select State">
                                        <option value="">Select State </option>
                                        @foreach($states as $state)
                                        <option value="{{ $state->id }}" {{ old('state_id', $employee->state_id ?? '') == $state->id ? 'selected' : '' }}>{{ $state->state_name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="state_id">State <span class="text-danger">*</span></label>
                                </div>
                                @error('state_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="city_id" name="city_id" class="select2 form-select @error('city_id') is-invalid @enderror" data-placeholder="Select City">
                                        <option value="">Select City</option>
                                        @foreach($cities as $city)
                                        <option value="{{ $city->id }}" {{ old('city_id', $employee->city_id ?? '') == $city->id ? 'selected' : '' }}>{{ $city->city_name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="city_id">City <span class="text-danger">*</span></label>
                                </div>
                                @error('city_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control @error('address_line1') is-invalid @enderror" id="address_line1" name="address_line1" placeholder="Enter Address Line 1" value="{{ old('address_line1', $employee->address_line1 ?? '') }}">
                                    <label for="address_line1">Address Line 1 <span class="text-danger">*</span></label>
                                </div>
                                @error('address_line1')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('address_line2') is-invalid @enderror" id="address_line2" name="address_line2" placeholder="Enter Address Line 2" value="{{ old('address_line2', $employee->address_line2 ?? '') }}">
                                    <label for="address_line2">Address Line 2</label>
                                </div>
                                @error('address_line2')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('zipcode') is-invalid @enderror" id="zipcode" placeholder="Enter Zip code" name="zipcode" value="{{ old('zipcode', $employee->zipcode ?? '') }}">
                                    <label for="zipcode">Zipcode</label>
                                </div>
                                @error('zipcode')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h6>Contact Information:</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('contact_person_name') is-invalid @enderror" id="contact_person_name" placeholder="Enter Contact Person Name" name="contact_person_name" value="{{ old('contact_person_name', $employee->contact_person_name ?? '') }}">
                                    <label for="contact_person_name">Contact Person Name</label>
                                </div>
                                @error('contact_person_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('contact_person_phone') is-invalid @enderror" id="contact_person_phone" placeholder="Enter Contact Person Phone Number" name="contact_person_phone" value="{{ old('contact_person_phone', $employee->contact_person_phone ?? '') }}">
                                    <label for="contact_person_phone">Contact Person Phone Number</label>
                                </div>
                                @error('contact_person_phone')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('contact_person_email') is-invalid @enderror" id="contact_person_email" placeholder="Enter Contact Person Email" name="contact_person_email" value="{{ old('contact_person_email', $employee->contact_person_email ?? '') }}">
                                    <label for="contact_person_email">Contact Person Email</label>
                                </div>
                                @error('contact_person_email')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h6>Salary Structure:</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" class="form-control @error('basic_salary') is-invalid @enderror" id="basic_salary" placeholder="Enter Basic Salary" name="basic_salary" value="{{ old('basic_salary', $employee->basic_salary ?? '') }}">
                                    <label for="basic_salary">Basic Salary</label>
                                </div>
                                @error('basic_salary')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" class="form-control @error('hra') is-invalid @enderror" id="hra" placeholder="Enter HRA" name="hra" value="{{ old('hra', $employee->hra ?? '') }}">
                                    <label for="hra">HRA</label>
                                </div>
                                @error('hra')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" class="form-control @error('allowances') is-invalid @enderror" id="allowances" placeholder="Enter Allowances" name="allowances" value="{{ old('allowances', $employee->allowances ?? '') }}">
                                    <label for="allowances">Allowances</label>
                                </div>
                                @error('allowances')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" class="form-control @error('deductions') is-invalid @enderror" id="deductions" placeholder="Enter Deductions" name="deductions" value="{{ old('deductions', $employee->deductions ?? '') }}">
                                    <label for="deductions">Deductions</label>
                                </div>
                                @error('deductions')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" class="form-control @error('gross_salary') is-invalid @enderror" id="gross_salary" placeholder="Enter Gross Salary" name="gross_salary" value="{{ old('gross_salary', $employee->gross_salary ?? '') }}">
                                    <label for="gross_salary">Gross Salary</label>
                                </div>
                                @error('gross_salary')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" class="form-control @error('net_salary') is-invalid @enderror" id="net_salary" placeholder="Enter Net Salary" name="net_salary" value="{{ old('net_salary', $employee->net_salary ?? '') }}">
                                    <label for="net_salary">Net Salary</label>
                                </div>
                                @error('net_salary')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h6>Tax & Compliance:</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="file" class="form-control @error('esi') is-invalid @enderror" id="esi" name="esi" accept="image/jpeg,image/png,image/webp,application/pdf">
                                    <label for="esi">ESI Document</label>
                                </div>
                                @error('esi')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                                @if($employee && $employee->esi_document)
                                <div class="file-preview">
                                    <a href="{{ asset('uploads/employee/' . $employee->id . '/' . $employee->esi_document) }}" target="_blank"><i class="ri-file-line"></i> View ESI Document</a>
                                </div>
                                @endif
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="file" class="form-control @error('pf') is-invalid @enderror" id="pf" name="pf" accept="image/jpeg,image/png,image/webp,application/pdf">
                                    <label for="pf">PF Document</label>
                                </div>
                                @error('pf')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                                @if($employee && $employee->pf_document)
                                <div class="file-preview">
                                    <a href="{{ asset('uploads/employee/' . $employee->id . '/' . $employee->pf_document) }}" target="_blank"><i class="ri-file-line"></i> View PF Document</a>
                                </div>
                                @endif
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="file" class="form-control @error('aadhaar') is-invalid @enderror" id="aadhaar" name="aadhaar" accept="image/jpeg,image/png,image/webp,application/pdf">
                                    <label for="aadhaar">Aadhaar Card</label>
                                </div>
                                @error('aadhaar')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                                @if($employee && $employee->aadhaar_document)
                                <div class="file-preview">
                                    <a href="{{ asset('uploads/employee/' . $employee->id . '/' . $employee->aadhaar_document) }}" target="_blank"><i class="ri-file-line"></i> View Aadhaar</a>
                                </div>
                                @endif
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="file" class="form-control @error('pan') is-invalid @enderror" id="pan" name="pan" accept="image/jpeg,image/png,image/webp,application/pdf">
                                    <label for="pan">Pan Card</label>
                                </div>
                                @error('pan')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                                @if($employee && $employee->pan_document)
                                <div class="file-preview">
                                    <a href="{{ asset('uploads/employee/' . $employee->id . '/' . $employee->pan_document) }}" target="_blank"><i class="ri-file-line"></i> View PAN</a>
                                </div>
                                @endif
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h6>Bank Details:</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('account_number') is-invalid @enderror" id="account_number" placeholder="Enter A/C No." name="account_number" value="{{ old('account_number', $employee->account_number ?? '') }}">
                                    <label for="account_number">A/C No.</label>
                                </div>
                                @error('account_number')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('bank_name') is-invalid @enderror" id="bank_name" placeholder="Enter Bank Name" name="bank_name" value="{{ old('bank_name', $employee->bank_name ?? '') }}">
                                    <label for="bank_name">Bank Name</label>
                                </div>
                                @error('bank_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('ifsc_code') is-invalid @enderror" id="ifsc_code" placeholder="Enter IFSC Code" name="ifsc_code" value="{{ old('ifsc_code', $employee->ifsc_code ?? '') }}">
                                    <label for="ifsc_code">IFSC Code</label>
                                </div>
                                @error('ifsc_code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
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
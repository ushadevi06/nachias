@extends('layouts.common')
@section('title', ($serviceProvider ? 'Edit' : 'Add') . ' Service Provider - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $serviceProvider ? 'Edit' : 'Add' }} Service Provider</h4>
                    </div>
                    <form
                        action="{{ url('service_providers/add' . ($serviceProvider ? '/' . $serviceProvider->id : '')) }}"
                        method="POST" class="common-form">
                        @csrf
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <h6>Identification & Contact:</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="service_type_id" id="service_type_id" class="select2 form-select @error('service_type_id') is-invalid @enderror"
                                        data-placeholder="Select Service Type">
                                        <option value="">Select Service Type</option>
                                        @foreach($service_types as $service_type)
                                        <option value="{{ $service_type->id }}"
                                            {{ old('service_type_id', $serviceProvider->service_type_id ?? '') == $service_type->id ? 'selected' : '' }}>
                                            {{ $service_type->service_type_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="service_type_id">Service Type <span class="text-danger">*</span></label>
                                </div>
                                @error('service_type_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter Name"
                                        name="name" value="{{ old('name', $serviceProvider->name ?? '') }}">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                </div>
                                @error('name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" placeholder="Enter Code"
                                        name="code" value="{{ old('code', $serviceProvider->code ?? '') }}">
                                    <label for="code">Code <span class="text-danger">*</span></label>
                                </div>
                                @error('code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Enter Email"
                                        name="email" value="{{ old('email', $serviceProvider->email ?? '') }}">
                                    <label for="email">Email</label>
                                </div>
                                @error('email')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('mobile_no') is-invalid @enderror" id="mobile_no"
                                        placeholder="Enter Mobile Number" name="mobile_no"
                                        value="{{ old('mobile_no', $serviceProvider->mobile_no ?? '') }}">
                                    <label for="mobile_no">Mobile Number <span class="text-danger">*</span></label>
                                </div>
                                @error('mobile_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('zip_code') is-invalid @enderror" id="zip_code" placeholder="Enter ZipCode"
                                        name="zip_code" value="{{ old('zip_code', $serviceProvider->zip_code ?? '') }}">
                                    <label for="zip_code">Zip Code</label>
                                </div>
                                @error('zip_code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('website_url') is-invalid @enderror" id="website_url"
                                        placeholder="Enter Website URL" name="website_url"
                                        value="{{ old('website_url', $serviceProvider->website_url ?? '') }}">
                                    <label for="website_url">Website URL</label>
                                </div>
                                @error('website_url')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="service_rate" id="service_rate" class="select2 form-select @error('service_rate') is-invalid @enderror"
                                        data-placeholder="Select Service Rate">
                                        <option value="">Select Service Rate</option>
                                        <option value="Per Agent"
                                            {{ old('service_rate', $serviceProvider->service_rate ?? '') == 'Per Agent' ? 'selected' : '' }}>
                                            Per Agent</option>
                                        <option value="Job Type"
                                            {{ old('service_rate', $serviceProvider->service_rate ?? '') == 'Job Type' ? 'selected' : '' }}>
                                            Job Type</option>
                                    </select>
                                    <label for="service_rate">Service Rate <span class="text-danger">*</span></label>
                                </div>
                                @error('service_rate')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror"
                                        data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active"
                                            {{ old('status', $serviceProvider->status ?? '') == 'Active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="Inactive"
                                            {{ old('status', $serviceProvider->status ?? '') == 'Inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                </div>
                                @error('status')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h6>Location Details:</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="state_id" id="state_id" class="select2 form-select @error('state_id') is-invalid @enderror"
                                        data-placeholder="Select State">
                                        <option value="">Select State</option>
                                        @foreach($states as $state)
                                        <option value="{{ $state->id }}"
                                            {{ old('state_id', $serviceProvider->state_id ?? '') == $state->id ? 'selected' : '' }}>
                                            {{ $state->state_name }}
                                        </option>
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
                                    <select name="city_id" id="city_id" class="select2 form-select @error('city_id') is-invalid @enderror"
                                        data-placeholder="Select City">
                                        <option value="">Select City</option>
                                        @foreach($cities as $city)
                                        <option value="{{ $city->id }}"
                                            {{ old('city_id', $serviceProvider->city_id ?? '') == $city->id ? 'selected' : '' }}>
                                            {{ $city->city_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="city_id">City <span class="text-danger">*</span></label>
                                </div>
                                @error('city_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="place_id" id="place_id" class="select2 form-select @error('place_id') is-invalid @enderror"
                                        data-placeholder="Select Place">
                                        <option value="">Select Place</option>
                                        @foreach($places as $place)
                                        <option value="{{ $place->id }}"
                                            {{ old('place_id', $serviceProvider->place_id ?? '') == $place->id ? 'selected' : '' }}>
                                            {{ $place->place_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="place_id">Place <span class="text-danger">*</span></label>
                                </div>
                                @error('place_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline mb-6">
                                    <input type="text" class="form-control @error('address_line_1') is-invalid @enderror" id="address_line_1"
                                        placeholder="Enter Address Line 1" name="address_line_1"
                                        value="{{ old('address_line_1', $serviceProvider->address_line_1 ?? '') }}">
                                    <label for="address_line_1">Address Line 1 <span class="text-danger">*</span></label>
                                </div>
                                @error('address_line_1')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('address_line_2') is-invalid @enderror" id="address_line_2"
                                        placeholder="Enter Address Line 2" name="address_line_2"
                                        value="{{ old('address_line_2', $serviceProvider->address_line_2 ?? '') }}">
                                    <label for="address_line_2">Address Line 2</label>
                                </div>
                                @error('address_line_2')
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
                                    <input type="text" class="form-control @error('contact_person_name') is-invalid @enderror" id="contact_person_name"
                                        placeholder="Enter Contact Person Name" name="contact_person_name"
                                        value="{{ old('contact_person_name', $serviceProvider->contact_person_name ?? '') }}">
                                    <label for="contact_person_name">Contact Person Name</label>
                                </div>
                                @error('contact_person_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('designation') is-invalid @enderror" id="designation"
                                        placeholder="Enter Designation" name="designation"
                                        value="{{ old('designation', $serviceProvider->designation ?? '') }}">
                                    <label for="designation">Designation</label>
                                </div>
                                @error('designation')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number"
                                        placeholder="Enter Phone Number" name="phone_number"
                                        value="{{ old('phone_number', $serviceProvider->phone_number ?? '') }}">
                                    <label for="phone_number">Phone Number</label>
                                </div>
                                @error('phone_number')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('contact_email') is-invalid @enderror" id="contact_email" placeholder="Enter Email"
                                        name="contact_email"
                                        value="{{ old('contact_email', $serviceProvider->contact_email ?? '') }}">
                                    <label for="contact_email">Email</label>
                                </div>
                                @error('contact_email')
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
                                    <input type="text" class="form-control @error('pan_no') is-invalid @enderror" id="pan_no" placeholder="Enter PAN No"
                                        name="pan_no" value="{{ old('pan_no', $serviceProvider->pan_no ?? '') }}">
                                    <label for="pan_no">PAN No</label>
                                </div>
                                @error('pan_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('gst_no') is-invalid @enderror" id="gst_no" placeholder="Enter GST No"
                                        name="gst_no" value="{{ old('gst_no', $serviceProvider->gst_no ?? '') }}">
                                    <label for="gst_no">GST No</label>
                                </div>
                                @error('gst_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100 @error('remarks') is-invalid @enderror" id="remarks" placeholder="Enter Remarks"
                                        name="remarks">{{ old('remarks', $serviceProvider->remarks ?? '') }}</textarea>
                                    <label for="remarks">Remarks</label>
                                </div>
                                @error('remarks')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h6>Financial / Payment:</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('bank_name') is-invalid @enderror" id="bank_name" placeholder="Enter Bank Name"
                                        name="bank_name"
                                        value="{{ old('bank_name', $serviceProvider->bank_name ?? '') }}">
                                    <label for="bank_name">Bank Name</label>
                                </div>
                                @error('bank_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('bank_acc_no') is-invalid @enderror" id="bank_acc_no"
                                        placeholder="Enter Bank Account No" name="bank_acc_no"
                                        value="{{ old('bank_acc_no', $serviceProvider->bank_acc_no ?? '') }}">
                                    <label for="bank_acc_no">Bank Account No</label>
                                </div>
                                @error('bank_acc_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('ifsc_code') is-invalid @enderror" id="ifsc_code" placeholder="Enter IFSC Code"
                                        name="ifsc_code"
                                        value="{{ old('ifsc_code', $serviceProvider->ifsc_code ?? '') }}">
                                    <label for="ifsc_code">IFSC Code</label>
                                </div>
                                @error('ifsc_code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100 @error('payment_terms') is-invalid @enderror" id="payment_terms"
                                        placeholder="Enter Payment Terms"
                                        name="payment_terms">{{ old('payment_terms', $serviceProvider->payment_terms ?? '') }}</textarea>
                                    <label for="payment_terms">Payment Terms</label>
                                </div>
                                @error('payment_terms')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
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
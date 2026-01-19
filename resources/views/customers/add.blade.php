@extends('layouts.common')
@section('title', 'Add Customer - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $customer ? 'Edit' : 'Add' }} Customer</h4>
                    </div>

                    <form action="{{ url('customers/add' . ($customer ? '/' . $customer->id : '')) }}" method="POST"
                        class="common-form">
                        @csrf
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <h6>Identification & Contact:</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <label class="form-label d-block mb-2">Category <span class="text-danger">*</span></label>
                                <div class="d-flex align-items-center gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input @error('category') is-invalid @enderror" type="radio" name="category"
                                            id="category_retailer" value="Retailer"
                                            {{ old('category', $customer->category ?? '') == 'Retailer' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="category_retailer">
                                            Retailer
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input @error('category') is-invalid @enderror" type="radio" name="category"
                                            id="category_wholesaler" value="Wholesaler"
                                            {{ old('category', $customer->category ?? '') == 'Wholesaler' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="category_wholesaler">
                                            Wholesaler
                                        </label>
                                    </div>
                                </div>
                                @error('category')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter Name"
                                        name="name" value="{{ old('name', $customer->name ?? '') }}">
                                    <label for="name">Name <span class="text-danger">*</span> </label>
                                </div>
                                @error('name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" placeholder="Enter Code"
                                        name="code" value="{{ old('code', $customer->code ?? '') }}">
                                    <label for="code">Code <span class="text-danger">*</span> </label>
                                </div>
                                @error('code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('mobile_no') is-invalid @enderror" id="mobile_no"
                                        placeholder="Enter Mobile Number" name="mobile_no"
                                        value="{{ old('mobile_no', $customer->mobile_no ?? '') }}">
                                    <label for="mobile_no">Mobile Number <span class="text-danger">*</span> </label>
                                </div>
                                @error('mobile_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Enter Email"
                                        name="email" value="{{ old('email', $customer->email ?? '') }}">
                                    <label for="email">Email </label>
                                </div>
                                @error('email')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="url" class="form-control @error('website_url') is-invalid @enderror" id="website_url"
                                        placeholder="Enter Website URL" name="website_url"
                                        value="{{ old('website_url', $customer->website_url ?? '') }}">
                                    <label for="website_url">Website URL </label>
                                </div>
                                @error('website_url')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('transport_name') is-invalid @enderror" id="transport_name"
                                        placeholder="Enter Transport Name" name="transport_name"
                                        value="{{ old('transport_name', $customer->transport_name ?? '') }}">
                                    <label for="transport_name">Transport Name </label>
                                </div>
                                @error('transport_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('booking_office') is-invalid @enderror" id="booking_office"
                                        placeholder="Enter Booking Office" name="booking_office"
                                        value="{{ old('booking_office', $customer->booking_office ?? '') }}">
                                    <label for="booking_office">Booking Office </label>
                                </div>
                                @error('booking_office')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="zone_id" id="zone_id" class="select2 form-select @error('zone_id') is-invalid @enderror"
                                        data-placeholder="Select Zone">
                                        <option value="">Select Zone</option>
                                        @foreach($zones as $zone)
                                        <option value="{{ $zone->id }}"
                                            {{ old('zone_id', $customer->zone_id ?? '') == $zone->id ? 'selected' : '' }}>
                                            {{ $zone->zone_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="zone_id">Zone <span class="text-danger">*</span> </label>
                                </div>
                                @error('zone_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="stores" id="stores" class="select2 form-select @error('stores') is-invalid @enderror"
                                        data-placeholder="Select Stores">
                                        <option value="">Select Stores</option>
                                        @foreach($store_types as $st)
                                        <option value="{{ $st->store_type_name }}" {{ old('stores', $customer->stores ?? '') == $st->store_type_name ? 'selected' : '' }}>{{ $st->store_type_name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="stores">Stores </label>
                                </div>
                                @error('stores')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror"
                                        data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active"
                                            {{ old('status', $customer->status ?? '') == 'Active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="Inactive"
                                            {{ old('status', $customer->status ?? '') == 'Inactive' ? 'selected' : '' }}>
                                            Inactive</option>
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
                                <h6>Location Details:</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="state_id" id="state_id" class="select2 form-select @error('state_id') is-invalid @enderror"
                                        data-placeholder="Select State">
                                        <option value="">Select State</option>
                                        @foreach($states as $state)
                                        <option value="{{ $state->id }}"
                                            {{ old('state_id', $customer->state_id ?? '') == $state->id ? 'selected' : '' }}>
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
                                            {{ old('city_id', $customer->city_id ?? '') == $city->id ? 'selected' : '' }}>
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
                                            {{ old('place_id', $customer->place_id ?? '') == $place->id ? 'selected' : '' }}>
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
                                        value="{{ old('address_line_1', $customer->address_line_1 ?? '') }}">
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
                                        value="{{ old('address_line_2', $customer->address_line_2 ?? '') }}">
                                    <label for="address_line_2">Address Line 2</label>
                                </div>
                                @error('address_line_2')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('address_line_3') is-invalid @enderror" id="address_line_3"
                                        placeholder="Enter Address Line 3" name="address_line_3"
                                        value="{{ old('address_line_3', $customer->address_line_3 ?? '') }}">
                                    <label for="address_line_3">Address Line 3</label>
                                </div>
                                @error('address_line_3')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('address_line_3') is-invalid @enderror" id="zip_code" placeholder="Enter ZipCode"
                                        name="zip_code" value="{{ old('zip_code', $customer->zip_code ?? '') }}">
                                    <label for="zip_code">Zip Code </label>
                                </div>
                                @error('zip_code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h6>Contact Information: </h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('contact_person_name') is-invalid @enderror" id="contact_person_name"
                                        placeholder="Enter Contact Person Name" name="contact_person_name"
                                        value="{{ old('contact_person_name', $customer->contact_person_name ?? '') }}">
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
                                        value="{{ old('designation', $customer->designation ?? '') }}">
                                    <label for="designation">Designation</label>
                                </div>
                                @error('designation')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('contact_mobile_no') is-invalid @enderror" id="contact_mobile_no"
                                        placeholder="Enter Mobile Number" name="contact_mobile_no"
                                        value="{{ old('contact_mobile_no', $customer->contact_mobile_no ?? '') }}">
                                    <label for="contact_mobile_no">Mobile Number</label>
                                </div>
                                @error('contact_mobile_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="email" class="form-control @error('contact_email') is-invalid @enderror" id="contact_email"
                                        placeholder="Enter Email" name="contact_email"
                                        value="{{ old('contact_email', $customer->contact_email ?? '') }}">
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
                                <h6>Tax & Compliance: </h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('gst_no') is-invalid @enderror" id="gst_no" placeholder="Enter GST No"
                                        name="gst_no" value="{{ old('gst_no', $customer->gst_no ?? '') }}">
                                    <label for="gst_no">GST No</label>
                                </div>
                                @error('gst_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="tax_type_id" id="tax_type_id" class="select2 form-select @error('tax_type_id') is-invalid @enderror"
                                        data-placeholder="Select Tax Types">
                                        <option value="">Select Tax Types</option>
                                        @foreach($taxes as $tax)
                                        <option value="{{ $tax->id }}"
                                            {{ old('tax_type_id', $customer->tax_type_id ?? '') == $tax->id ? 'selected' : '' }}>
                                            {{ $tax->item_name }} ({{ $tax->tax_rate }}%)
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="tax_type_id">Tax Types</label>
                                </div>
                                @error('tax_type_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('pan_no') is-invalid @enderror" id="pan_no" placeholder="Enter PAN No"
                                        name="pan_no" value="{{ old('pan_no', $customer->pan_no ?? '') }}">
                                    <label for="pan_no">PAN No</label>
                                </div>
                                @error('pan_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline mb-6">
                                    <textarea class="form-control h-px-100 @error('payment_terms') is-invalid @enderror" id="payment_terms"
                                        placeholder="Enter Payment Terms"
                                        name="payment_terms">{{ old('payment_terms', $customer->payment_terms ?? '') }}</textarea>
                                    <label for="payment_terms">Payment Terms </label>
                                </div>
                                @error('payment_terms')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" class="form-control @error('credit_limit') is-invalid @enderror" id="credit_limit"
                                        placeholder="Enter Credit Limit" name="credit_limit"
                                        value="{{ old('credit_limit', $customer->credit_limit ?? '') }}">
                                    <label for="credit_limit">Credit Limit</label>
                                </div>
                                @error('credit_limit')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" class="form-control @error('sales_discount') is-invalid @enderror" id="sales_discount"
                                        placeholder="Enter Sales Discount" name="sales_discount"
                                        value="{{ old('sales_discount', $customer->sales_discount ?? '') }}">
                                    <label for="sales_discount">Sales Discount (%)</label>
                                </div>
                                @error('sales_discount')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" class="form-control @error('box_discount') is-invalid @enderror" id="box_discount"
                                        placeholder="Enter Box Discount" name="box_discount"
                                        value="{{ old('box_discount', $customer->box_discount ?? '') }}">
                                    <label for="box_discount">Box Discount (%)</label>
                                </div>
                                @error('box_discount')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h6>Bank Information: </h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('bank_name') is-invalid @enderror" id="bank_name" placeholder="Enter Bank Name"
                                        name="bank_name" value="{{ old('bank_name', $customer->bank_name ?? '') }}">
                                    <label for="bank_name">Bank Name</label>
                                </div>
                                @error('bank_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('branch') is-invalid @enderror" id="branch" placeholder="Enter Branch"
                                        name="branch" value="{{ old('branch', $customer->branch ?? '') }}">
                                    <label for="branch">Branch</label>
                                </div>
                                @error('branch')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('account_number') is-invalid @enderror" id="account_number"
                                        placeholder="Enter Account Number" name="account_number"
                                        value="{{ old('account_number', $customer->account_number ?? '') }}">
                                    <label for="account_number">Account Number</label>
                                </div>
                                @error('account_number')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('ifsc_code') is-invalid @enderror" id="ifsc_code" placeholder="Enter IFSC Code"
                                        name="ifsc_code" value="{{ old('ifsc_code', $customer->ifsc_code ?? '') }}">
                                    <label for="ifsc_code">IFSC Code</label>
                                </div>
                                @error('ifsc_code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
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
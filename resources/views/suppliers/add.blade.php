@extends('layouts.common')
@section('title', ($supplier ? 'Edit' : 'Add') . ' Supplier - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $supplier ? 'Edit' : 'Add' }} Supplier</h4>
                    </div>
                    <form action="{{ url('suppliers/add' . ($supplier ? '/' . $supplier->id : '')) }}" method="POST"
                        class="common-form">
                        @csrf
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <h6>Identification & Contact:</h6>
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter Name"
                                        name="name" value="{{ old('name', $supplier->name ?? '') }}">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                </div>
                                @error('name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" placeholder="Enter Code"
                                        name="code" value="{{ old('code', $supplier->code ?? '') }}">
                                    <label for="code">Code <span class="text-danger">*</span></label>
                                </div>
                                @error('code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('mobile_no') is-invalid @enderror" id="mobile_no"
                                        placeholder="Enter Mobile Number" name="mobile_no"
                                        value="{{ old('mobile_no', $supplier->mobile_no ?? '') }}">
                                    <label for="mobile_no">Mobile Number <span class="text-danger">*</span></label>
                                </div>
                                @error('mobile_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Enter Email"
                                        name="email" value="{{ old('email', $supplier->email ?? '') }}">
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
                                        value="{{ old('website_url', $supplier->website_url ?? '') }}">
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
                                        value="{{ old('transport_name', $supplier->transport_name ?? '') }}">
                                    <label for="transport_name">Transport Name </label>
                                </div>
                                @error('transport_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('booking_area') is-invalid @enderror" id="booking_area"
                                        placeholder="Enter Booking Area" name="booking_area"
                                        value="{{ old('booking_area', $supplier->booking_area ?? '') }}">
                                    <label for="booking_area">Booking Area </label>
                                </div>
                                @error('booking_area')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="stores" id="stores" class="select2 form-select @error('stores') is-invalid @enderror"
                                        data-placeholder="Select Stores">
                                        <option value="">Select Stores</option>
                                        <option value="Fabric"
                                            {{ old('stores', $supplier->stores ?? '') == 'Fabric' ? 'selected' : '' }}>
                                            Fabric</option>
                                        <option value="Finished Goods"
                                            {{ old('stores', $supplier->stores ?? '') == 'Finished Goods' ? 'selected' : '' }}>
                                            Finished Goods</option>
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
                                            {{ old('status', $supplier->status ?? '') == 'Active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="Inactive"
                                            {{ old('status', $supplier->status ?? '') == 'Inactive' ? 'selected' : '' }}>
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
                                <h6>Location Information:</h6>
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="state_id" id="state_id" class="select2 form-select @error('state_id') is-invalid @enderror"
                                        data-placeholder="Select State">
                                        <option value="">Select State</option>
                                        @foreach($states as $state)
                                        <option value="{{ $state->id }}"
                                            {{ old('state_id', $supplier->state_id ?? '') == $state->id ? 'selected' : '' }}>
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
                                        @foreach($cities as $c)
                                        <option value="{{ $c->id }}"
                                            {{ (string)old('city_id', optional($supplier)->city_id ?? '') === (string)$c->id ? 'selected' : '' }}>
                                            {{ $c->city_name }}
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
                                        @foreach($places as $sp)
                                        <option value="{{ $sp->id }}"
                                            {{ (string)old('place_id', optional($supplier)->place_id ?? '') === (string)$sp->id ? 'selected' : '' }}>
                                            {{ $sp->place_name }}
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
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('address_line_1') is-invalid @enderror" id="address_line_1"
                                        placeholder="Enter Address Line 1" name="address_line_1"
                                        value="{{ old('address_line_1', $supplier->address_line_1 ?? '') }}">
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
                                        value="{{ old('address_line_2', $supplier->address_line_2 ?? '') }}">
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
                                        value="{{ old('address_line_3', $supplier->address_line_3 ?? '') }}">
                                    <label for="address_line_3">Address Line 3</label>
                                </div>
                                @error('address_line_3')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('zip_code') is-invalid @enderror" id="zip_code" placeholder="Enter ZipCode"
                                        name="zip_code" value="{{ old('zip_code', $supplier->zip_code ?? '') }}">
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
                                        value="{{ old('contact_person_name', $supplier->contact_person_name ?? '') }}">
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
                                        value="{{ old('designation', $supplier->designation ?? '') }}">
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
                                        value="{{ old('contact_mobile_no', $supplier->contact_mobile_no ?? '') }}">
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
                                        value="{{ old('contact_email', $supplier->contact_email ?? '') }}">
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
                                <h6>Commission Information: </h6>
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="purchase_commission_agent_id" id="purchase_commission_agent_id"
                                        class="select2 form-select @error('purchase_commission_agent_id') is-invalid @enderror" data-placeholder="Select Purchase Commission Agent">

                                        <option value="">Select Purchase Commission Agent</option>

                                        @foreach($purchase_commission_agents as $agent)
                                        <option value="{{ $agent->id }}"
                                            {{ old('purchase_commission_agent_id', $supplier->purchase_commission_agent_id ?? '') == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->name }}
                                        </option>
                                        @endforeach

                                    </select>
                                    <label for="purchase_commission_agent_id">Purchase Commission Agent</label>
                                </div>

                                @error('purchase_commission_agent_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>


                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" class="form-control @error('commission_percentage') is-invalid @enderror" id="commission_percentage"
                                        placeholder="Enter Commission (%)" name="commission_percentage"
                                        value="{{ old('commission_percentage', $supplier->commission_percentage ?? '') }}">
                                    <label for="commission_percentage">Commission (%)</label>
                                </div>
                                @error('commission_percentage')
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
                                        name="gst_no" value="{{ old('gst_no', $supplier->gst_no ?? '') }}">
                                    <label for="gst_no">GST No</label>
                                </div>
                                @error('gst_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="tax_id" id="tax_id" class="select2 form-select @error('tax_id') is-invalid @enderror"
                                        data-placeholder="Select Tax Types">
                                        <option value="">Select Tax Types</option>
                                        @foreach($taxes as $tax)
                                        <option value="{{ $tax->id }}"
                                            {{ old('tax_id', $supplier->tax_id ?? '') == $tax->id ? 'selected' : '' }}>
                                            {{ $tax->item_name }} ({{ $tax->tax_rate }}%)
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="tax_id">Tax Types</label>
                                </div>
                                @error('tax_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('pan_no') is-invalid @enderror" id="pan_no" placeholder="Enter PAN No"
                                        name="pan_no" value="{{ old('pan_no', $supplier->pan_no ?? '') }}">
                                    <label for="pan_no">PAN No</label>
                                </div>
                                @error('pan_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('ecc_no') is-invalid @enderror" id="ecc_no" placeholder="Enter ECC No"
                                        name="ecc_no" value="{{ old('ecc_no', $supplier->ecc_no ?? '') }}">
                                    <label for="ecc_no">ECC No</label>
                                </div>
                                @error('ecc_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" class="form-control @error('credit_limit') is-invalid @enderror" id="credit_limit"
                                        placeholder="Enter Credit Limit" name="credit_limit"
                                        value="{{ old('credit_limit', $supplier->credit_limit ?? '') }}">
                                    <label for="credit_limit">Credit Limit</label>
                                </div>
                                @error('credit_limit')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100 @error('payment_terms') is-invalid @enderror" id="payment_terms"
                                        placeholder="Enter Payment Terms"
                                        name="payment_terms">{{ old('payment_terms', $supplier->payment_terms ?? '') }}</textarea>
                                    <label for="payment_terms">Payment Terms </label>
                                </div>
                                @error('payment_terms')
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
                                        name="bank_name" value="{{ old('bank_name', $supplier->bank_name ?? '') }}">
                                    <label for="bank_name">Bank Name</label>
                                </div>
                                @error('bank_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('branch') is-invalid @enderror" id="branch" placeholder="Enter Branch"
                                        name="branch" value="{{ old('branch', $supplier->branch ?? '') }}">
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
                                        value="{{ old('account_number', $supplier->account_number ?? '') }}">
                                    <label for="account_number">Account Number</label>
                                </div>
                                @error('account_number')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('ifsc_code') is-invalid @enderror" id="ifsc_code" placeholder="Enter IFSC Code"
                                        name="ifsc_code" value="{{ old('ifsc_code', $supplier->ifsc_code ?? '') }}">
                                    <label for="ifsc_code">IFSC Code</label>
                                </div>
                                @error('ifsc_code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('suppliers') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
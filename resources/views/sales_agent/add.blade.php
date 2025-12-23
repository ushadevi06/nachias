@extends('layouts.common')
@section('title', ($salesAgent ? 'Edit' : 'Add') . ' Sales Agent - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">

                    <div class="card-header-box">
                        <h4>{{ $salesAgent ? 'Edit' : 'Add' }} Sales Agent</h4>
                    </div>
                    <form action="{{ url('sales_agents/add' . ($salesAgent ? '/' . $salesAgent->id : '')) }}"
                        method="POST" class="common-form">
                        @csrf
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <h6>Identification & Contact:</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="agent_type" id="agent_type" class="select2 form-select @error('agent_type') is-invalid @enderror"
                                        data-placeholder="Select Agent Type">
                                        <option value="">Select Agent Type</option>
                                        <option value="Direct Sales Agent"
                                            {{ old('agent_type', $salesAgent->agent_type ?? '') == 'Direct Sales Agent' ? 'selected' : '' }}>
                                            Direct Sales Agent</option>
                                        <option value="Commission Agent"
                                            {{ old('agent_type', $salesAgent->agent_type ?? '') == 'Commission Agent' ? 'selected' : '' }}>
                                            Commission Agent</option>
                                        <option value="Export Agent"
                                            {{ old('agent_type', $salesAgent->agent_type ?? '') == 'Export Agent' ? 'selected' : '' }}>
                                            Export Agent</option>
                                    </select>
                                    <label for="agent_type">Agent Type <span class="text-danger">*</span></label>
                                </div>
                                @error('agent_type')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter Name"
                                        name="name" value="{{ old('name', $salesAgent->name ?? '') }}">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                </div>
                                @error('name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" placeholder="Enter Code"
                                        name="code" value="{{ old('code', $salesAgent->code ?? '') }}">
                                    <label for="code">Code <span class="text-danger">*</span></label>
                                </div>
                                @error('code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Enter Email"
                                        name="email" value="{{ old('email', $salesAgent->email ?? '') }}">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                </div>
                                @error('email')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('mobile_no') is-invalid @enderror" id="mobile_no"
                                        placeholder="Enter Mobile Number" name="mobile_no"
                                        value="{{ old('mobile_no', $salesAgent->mobile_no ?? '') }}">
                                    <label for="mobile_no">Mobile Number <span class="text-danger">*</span></label>
                                </div>
                                @error('mobile_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror"
                                        data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active"
                                            {{ old('status', $salesAgent->status ?? '') == 'Active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="Inactive"
                                            {{ old('status', $salesAgent->status ?? '') == 'Inactive' ? 'selected' : '' }}>
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
                                            {{ old('state_id', $salesAgent->state_id ?? '') == $state->id ? 'selected' : '' }}>
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
                                            {{ old('city_id', $salesAgent->city_id ?? '') == $city->id ? 'selected' : '' }}>
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
                                            {{ old('place_id', $salesAgent->place_id ?? '') == $place->id ? 'selected' : '' }}>
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
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('address_line_1') is-invalid @enderror" id="address_line_1"
                                        placeholder="Enter Address Line 1" name="address_line_1"
                                        value="{{ old('address_line_1', $salesAgent->address_line_1 ?? '') }}">
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
                                        value="{{ old('address_line_2', $salesAgent->address_line_2 ?? '') }}">
                                    <label for="address_line_2">Address Line 2</label>
                                </div>
                                @error('address_line_2')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('zip_code') is-invalid @enderror" id="zip_code" placeholder="Enter Zip Code"
                                        name="zip_code" value="{{ old('zip_code', $salesAgent->zip_code ?? '') }}">
                                    <label for="zip_code">Zip Code <span class="text-danger">*</span></label>
                                </div>
                                @error('zip_code')
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
                                        value="{{ old('contact_person_name', $salesAgent->contact_person_name ?? '') }}">
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
                                        value="{{ old('designation', $salesAgent->designation ?? '') }}">
                                    <label for="designation">Designation</label>
                                </div>
                                @error('designation')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('contact_phone_number') is-invalid @enderror" id="contact_phone_number"
                                        placeholder="Enter Contact Phone Number" name="contact_phone_number"
                                        value="{{ old('contact_phone_number', $salesAgent->contact_phone_number ?? '') }}">
                                    <label for="contact_phone_number">Contact Phone Number</label>
                                </div>
                                @error('contact_phone_number')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('contact_email') is-invalid @enderror" id="contact_email"
                                        placeholder="Enter Contact Email" name="contact_email"
                                        value="{{ old('contact_email', $salesAgent->contact_email ?? '') }}">
                                    <label for="contact_email">Contact Email</label>
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
                                        name="pan_no" value="{{ old('pan_no', $salesAgent->pan_no ?? '') }}">
                                    <label for="pan_no">PAN No</label>
                                </div>
                                @error('pan_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('gst_no') is-invalid @enderror" id="gst_no" placeholder="Enter GST No"
                                        name="gst_no" value="{{ old('gst_no', $salesAgent->gst_no ?? '') }}">
                                    <label for="gst_no">GST No</label>
                                </div>
                                @error('gst_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h6>Performance Targets:</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" class="form-control @error('commission_value') is-invalid @enderror" id="commission_value"
                                        placeholder="Enter Commission Value" name="commission_value"
                                        value="{{ old('commission_value', $salesAgent->commission_value ?? '') }}">
                                    <label for="commission_value">Commission Value</label>
                                </div>
                                @error('commission_value')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" class="form-control @error('sales_target') is-invalid @enderror" id="sales_target"
                                        placeholder="Enter Sales Target" name="sales_target"
                                        value="{{ old('sales_target', $salesAgent->sales_target ?? '') }}">
                                    <label for="sales_target">Sales Target</label>
                                </div>
                                @error('sales_target')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('sales_agents') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
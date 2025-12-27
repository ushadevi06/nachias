@extends('layouts.common')
@section('title', ($agent && $agent->id ? 'Edit' : 'Add') . ' Purchase Commission Agent - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $agent && $agent->id ? 'Edit' : 'Add' }} Purchase Commission Agent</h4>
                    </div>
                    <form
                        action="{{ url('purchase_commission_agent/add' . ($agent && $agent->id ? '/' . $agent->id : '')) }}"
                        method="POST" class="common-form">
                        @csrf
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <h6>Identification & Contact:</h6>
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" placeholder="Enter Name" name="name"
                                        value="{{ old('name', $agent->name ?? '') }}">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                </div>
                                @error('name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('code') is-invalid @enderror"
                                        id="code" placeholder="Enter Code" name="code"
                                        value="{{ old('code', $agent->code ?? '') }}">
                                    <label for="code">Code <span class="text-danger">*</span></label>
                                </div>
                                @error('code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('email') is-invalid @enderror"
                                        id="email" placeholder="Enter Email" name="email"
                                        value="{{ old('email', $agent->email ?? '') }}">
                                    <label for="email">Email</label>
                                </div>
                                @error('email')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('mobile_no') is-invalid @enderror"
                                        id="mobile_no" placeholder="Enter Mobile Number" name="mobile_no"
                                        value="{{ old('mobile_no', $agent->mobile_no ?? '') }}">
                                    <label for="mobile_no">Mobile Number <span class="text-danger">*</span></label>
                                </div>
                                @error('mobile_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="status" name="status"
                                        class="select2 form-select @error('status') is-invalid @enderror"
                                        data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active"
                                            {{ old('status', $agent->status ?? '') == 'Active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="Inactive"
                                            {{ old('status', $agent->status ?? '') == 'Inactive' ? 'selected' : '' }}>
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
                                    <select id="state_id" name="state_id"
                                        class="select2 form-select @error('state_id') is-invalid @enderror"
                                        data-placeholder="Select State">
                                        <option value="">Select State</option>
                                        @foreach($states as $s)
                                        <option value="{{ $s->id }}"
                                            {{ (string)old('state_id', optional($agent)->state_id ?? '') === (string)$s->id ? 'selected' : '' }}>
                                            {{ $s->state_name }}
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
                                    <select id="city_id" name="city_id"
                                        class="select2 form-select @error('city_id') is-invalid @enderror"
                                        data-placeholder="Select City">
                                        <option value="">Select City</option>
                                        @foreach($cities as $c)
                                        <option value="{{ $c->id }}"
                                            {{ (string)old('city_id', optional($agent)->city_id ?? '') === (string)$c->id ? 'selected' : '' }}>
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
                                    <select id="place_id" name="place_id"
                                        class="select2 form-select @error('place_id') is-invalid @enderror"
                                        data-placeholder="Select Place">
                                        <option value="">Select Place</option>
                                        @foreach($servicePoints as $sp)
                                        <option value="{{ $sp->id }}"
                                            {{ (string)old('place_id', optional($agent)->place_id ?? '') === (string)$sp->id ? 'selected' : '' }}>
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
                                <div class="form-floating form-floating-outline mb-6">
                                    <input type="text"
                                        class="form-control @error('address_line_1') is-invalid @enderror"
                                        id="address_line_1" placeholder="Enter Address Line 1" name="address_line_1"
                                        value="{{ old('address_line_1', $agent->address_line_1 ?? '') }}">
                                    <label for="address_line_1">Address Line 1</label>
                                </div>
                                @error('address_line_1')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text"
                                        class="form-control @error('address_line_2') is-invalid @enderror"
                                        id="address_line_2" placeholder="Enter Address Line 2" name="address_line_2"
                                        value="{{ old('address_line_2', $agent->address_line_2 ?? '') }}">
                                    <label for="address_line_2">Address Line 2</label>
                                </div>
                                @error('address_line_2')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('zipcode') is-invalid @enderror"
                                        id="zipcode" placeholder="Enter ZipCode" name="zipcode"
                                        value="{{ old('zipcode', $agent->zipcode ?? '') }}">
                                    <label for="zipcode">Zip Code</label>
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
                                    <input type="text"
                                        class="form-control @error('contact_person_name') is-invalid @enderror"
                                        id="contact_person_name" placeholder="Enter Contact Person Name"
                                        name="contact_person_name"
                                        value="{{ old('contact_person_name', $agent->contact_person_name ?? '') }}">
                                    <label for="contact_person_name">Contact Person Name</label>
                                </div>
                                @error('contact_person_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('designation') is-invalid @enderror"
                                        id="designation" placeholder="Enter Designation" name="designation"
                                        value="{{ old('designation', $agent->designation ?? '') }}">
                                    <label for="designation">Designation</label>
                                </div>
                                @error('designation')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                        id="phone_number" placeholder="Enter Phone Number" name="phone_number"
                                        value="{{ old('phone_number', $agent->phone_number ?? '') }}">
                                    <label for="phone_number">Phone Number</label>
                                </div>
                                @error('phone_number')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('contact_email') is-invalid @enderror"
                                        id="contact_email" placeholder="Enter Email" name="contact_email"
                                        value="{{ old('contact_email', $agent->contact_email ?? '') }}">
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
                                    <input type="text" class="form-control @error('pan_no') is-invalid @enderror"
                                        id="pan_no" placeholder="Enter PAN No" name="pan_no"
                                        value="{{ old('pan_no', $agent->pan_no ?? '') }}">
                                    <label for="pan_no">PAN No</label>
                                </div>
                                @error('pan_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('gst_no') is-invalid @enderror"
                                        id="gst_no" placeholder="Enter GST No" name="gst_no"
                                        value="{{ old('gst_no', $agent->gst_no ?? '') }}">
                                    <label for="gst_no">GST No</label>
                                </div>
                                @error('gst_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control @error('remarks') is-invalid @enderror"
                                        id="remarks" placeholder="Enter Remarks"
                                        name="remarks">{{ old('remarks', $agent->remarks ?? '') }}</textarea>
                                    <label for="remarks">Remarks</label>
                                </div>
                                @error('remarks')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('purchase_commission_agent') }}" class="btn btn-secondary">Cancel</a>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@endpush

@endsection
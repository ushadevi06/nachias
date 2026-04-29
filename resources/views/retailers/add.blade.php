@extends('layouts.common')
@section('title', ($retailer ? 'Edit Retailer' : 'Add Retailer') . ' - ' . env('WEBSITE_NAME'))
@section('content')
        <div class="container-xxl section-padding">
            <div class="row">
                <div class="col-lg-12">
                    @include('flash_messages')
                </div>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-header-box">
                                <h4>{{ $retailer ? 'Edit' : 'Add' }} Retailer</h4>
                            </div>
                            <form action="{{ url('retailers/add' . ($retailer ? '/' . $retailer->id : '')) }}" method="POST"
                                class="common-form" autocomplete="off">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-lg-12">
                                        <h6>Identification & Contact:</h6>
                                    </div>

                                    <div class="col-md-6 col-xl-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" placeholder="Enter Name" name="name"
                                                value="{{ old('name', $retailer->name ?? '') }}">
                                            <label for="name">Name <span class="text-danger">*</span> </label>
                                        </div>
                                        @error('name')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control @error('code') is-invalid @enderror"
                                                id="code" placeholder="Enter Code" name="code"
                                                value="{{ old('code', $retailer->code ?? '') }}">
                                            <label for="code">Code <span class="text-danger">*</span> </label>
                                        </div>
                                        @error('code')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control @error('mobile_number') is-invalid @enderror"
                                                id="mobile_number" placeholder="Enter Mobile Number" name="mobile_number"
                                                value="{{ old('mobile_number', $retailer->mobile_number ?? '') }}">
                                            <label for="mobile_number">Mobile Number </label>
                                        </div>
                                        @error('mobile_number')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                id="email" placeholder="Enter Email" name="email"
                                                value="{{ old('email', $retailer->email ?? '') }}">
                                            <label for="email">Email </label>
                                        </div>
                                        @error('email')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 col-xl-4">
                                        <div class="form-floating form-floating-outline">
                                            <select name="status" id="status"
                                                class="select2 form-select @error('status') is-invalid @enderror"
                                                data-placeholder="Select Status">
                                                <option value="Active" {{ old('status', $retailer->status ?? 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                                                <option value="Inactive" {{ old('status', $retailer->status ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
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
                                            <select name="state_id" id="state_id"
                                                class="select2 form-select @error('state_id') is-invalid @enderror"
                                                data-placeholder="Select State">
                                                <option value="">Select State</option>
                                                @foreach($states as $state)
                                                    <option value="{{ $state->id }}" {{ old('state_id', $retailer->state_id ?? '') == $state->id ? 'selected' : '' }}>{{ $state->state_name }}</option>
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
                                            <select name="city_id" id="city_id"
                                                class="select2 form-select @error('city_id') is-invalid @enderror"
                                                data-placeholder="Select City">
                                                <option value="">Select City</option>
                                                @foreach($cities as $city)
                                                    <option value="{{ $city->id }}" {{ old('city_id', $retailer->city_id ?? '') == $city->id ? 'selected' : '' }}>{{ $city->city_name }}</option>
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
                                            <select name="place_id" id="place_id"
                                                class="select2 form-select @error('place_id') is-invalid @enderror"
                                                data-placeholder="Select Place">
                                                <option value="">Select Place</option>
                                                @foreach($places as $place)
                                                    <option value="{{ $place->id }}" {{ old('place_id', $retailer->place_id ?? '') == $place->id ? 'selected' : '' }}>{{ $place->place_name }}</option>
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
                                            <select name="zone_id" id="zone_id" class="select2 form-select @error('zone_id') is-invalid @enderror"
                                                data-placeholder="Select Zone">
                                                <option value="">Select Zone</option>
                                                @foreach($zones as $zone)
                                                    <option value="{{ $zone->id }}" {{ old('zone_id', $retailer->zone_id ?? '') == $zone->id ? 'selected' : '' }}>
                                                        {{ $zone->zone_name }}</option>
                                                @endforeach
                                            </select>
                                            <label for="zone_id">Zone <span class="text-danger">*</span></label>
                                        </div>
                                        @error('zone_id')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text"
                                                class="form-control @error('address_line_1') is-invalid @enderror"
                                                id="address_line_1" placeholder="Enter Address Line 1" name="address_line_1"
                                                value="{{ old('address_line_1', $retailer->address_line_1 ?? '') }}">
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
                                                value="{{ old('address_line_2', $retailer->address_line_2 ?? '') }}">
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
                                                value="{{ old('zipcode', $retailer->zipcode ?? '') }}">
                                            <label for="zipcode">Zip Code </label>
                                        </div>
                                        @error('zipcode')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-lg-12 text-end">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a href="{{ url('retailers') }}" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('scripts')
<script>
    $(function () {
        // Fetch Zones when City changes
        $('#city_id').on('change', function () {
            var city_id = $(this).val();
            $('#zone_id').empty().append('<option value="">Loading...</option>').prop('disabled', true).trigger('change.select2');
            
            if (city_id) {
                $.ajax({
                    url: APP_URL + '/get-zones-by-city/' + city_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#zone_id').empty().append('<option value="">-- Select Zone --</option>');
                        $.each(data, function (key, zone) {
                            $('#zone_id').append('<option value="' + zone.id + '">' + zone.zone_name + '</option>');
                        });
                        $('#zone_id').prop('disabled', false).trigger('change.select2');
                    }
                });
            } else {
                $('#zone_id').empty().append('<option value="">Select Zone</option>').prop('disabled', false).trigger('change.select2');
            }
        });
    });
</script>
@endsection

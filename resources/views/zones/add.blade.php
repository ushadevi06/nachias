@extends('layouts.common')
@section('title', ($zone ? 'Edit Zone' : 'Add Zone') . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $zone ? 'Edit' : 'Add' }} Zone</h4>
                    </div>

                    <form action="{{ url('zones/add' . ($zone ? '/' . $zone->id : '')) }}" method="POST"
                        class="common-form">
                        @csrf
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select name="state_id" id="zone_state_id" class="select2 form-select @error('state_id') is-invalid @enderror"
                                        data-placeholder="Select State">
                                        <option value="">Select State</option>
                                        @foreach($states as $state)
                                        <option value="{{ $state->id }}"
                                            {{ old('state_id', $zone->state_id ?? '') == $state->id ? 'selected' : '' }}>
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
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select name="city_ids[]" id="city_ids" class="select2 form-select @error('city_ids') is-invalid @enderror"
                                        data-placeholder="Select City" multiple>
                                        <option value="">Select City</option>
                                        @php
                                        $selectedCities = old('city_ids', $zone ? explode(',', $zone->city_ids) : []);
                                        @endphp

                                        @foreach($cities as $city)
                                        <option value="{{ $city->id }}"
                                            {{ in_array($city->id, $selectedCities) ? 'selected' : '' }}>
                                            {{ $city->city_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="city_ids">City <span class="text-danger">*</span></label>
                                </div>
                                @error('city_ids')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('zone_name') is-invalid @enderror" id="zone-name" placeholder="Enter Zone Name"
                                        name="zone_name" value="{{ old('zone_name', $zone->zone_name ?? '') }}">
                                    <label for="zone-name">Zone name <span class="text-danger">*</span></label>
                                </div>
                                @error('zone_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror"
                                        data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active"
                                            {{ old('status', $zone->status ?? '') == 'Active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="Inactive"
                                            {{ old('status', $zone->status ?? '') == 'Inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                </div>
                                @error('status')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('zones') }}" class="btn btn-secondary">Cancel</a>
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
    $(document).ready(function() {
        $('#zone_state_id').on('change', function() {
            var stateId = $(this).val();
            $('#city_ids').html('<option value="">Select City</option>');

            if (stateId) {
                  $.ajax({
                    url: APP_URL + '/get-cities/' + stateId,
                    type: 'GET',
                    success: function(data) {
                        $('#city_ids').html('<option value="">Select City</option>');
                        $.each(data, function(index, city) {
                            $('#city_ids').append('<option value="' + city.id + '">' + city.city_name + '</option>');
                        });
                        $('#city_ids').trigger('change');
                    }
                });
            }
        });
    });
</script>
@endsection
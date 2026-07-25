@extends('layouts.common')
@section('title', ($zone ? 'Edit Zone' : 'Add Zone') . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $zone ? 'Edit' : 'Add' }} Zone</h4>
                    </div>

                    <form action="{{ url('zones/add' . ($zone ? '/' . $zone->id : '')) }}" method="POST" class="common-form" autocomplete="off">
                        @csrf
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select name="state_ids[]" id="zone_state_id" class="select2 form-select @error('state_ids') is-invalid @enderror" data-placeholder="Select State" multiple>
                                        <option value="">Select State</option>
                                        @foreach($states as $state)
                                        <option value="{{ $state->id }}" {{ in_array($state->id, $stateIds ?? []) ? 'selected' : '' }}>{{ $state->state_name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="zone_state_id">State <span class="text-danger">*</span></label>
                                </div>
                                @error('state_ids')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select name="city_ids[]" id="city_ids" class="select2 form-select @error('city_ids') is-invalid @enderror" data-placeholder="Select City" multiple>
                                        @php
                                        $selectedCities = old('city_ids', $zone ? explode(',', $zone->city_ids) : []);
                                        @endphp

                                        @foreach($cities as $city)
                                        <option value="{{ $city->id }}" data-state-id="{{ $city->state_id }}"
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
                                    <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active" {{ old('status', $zone->status ?? '') == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status', $zone->status ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
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
    var cityAjaxRequest = null;
    var previousStateIds = [];

    $(document).ready(function() {
        var initialStates = $('#zone_state_id').val() || [];
        previousStateIds = initialStates.map(String);

        $('#zone_state_id').on('change', function() {
            var rawStateIds = $(this).val() || [];
            var stateIds = rawStateIds.map(String);
            
            // Find added and removed states using strict string comparison
            var addedStates = stateIds.filter(x => !previousStateIds.includes(x));
            var removedStates = previousStateIds.filter(x => !stateIds.includes(x));
            
            // Capture selected cities BEFORE doing anything
            var rawSelectedCities = $('#city_ids').val() || [];
            var selectedCities = rawSelectedCities.map(String);
            
            // Remove cities belonging to removed states
            if (removedStates.length > 0) {
                var removedAny = false;
                removedStates.forEach(function(stateId) {
                    var optionsToRemove = $('#city_ids option[data-state-id="' + stateId + '"]');
                    if(optionsToRemove.length > 0) {
                        optionsToRemove.remove();
                        removedAny = true;
                    }
                });
                
                if(removedAny) {
                    // Update selectedCities to remove the ones that were just deleted from DOM
                    selectedCities = selectedCities.filter(function(cityId) {
                        return $('#city_ids option[value="' + cityId + '"]').length > 0;
                    });
                    // Simply trigger change so Select2 syncs with the DOM
                    $('#city_ids').trigger('change');
                }
            }

            // Fetch and add cities for newly added states
            if (addedStates.length > 0) {
                if (cityAjaxRequest) {
                    cityAjaxRequest.abort();
                }

                cityAjaxRequest = $.ajax({
                    url: APP_URL + '/get-cities/' + addedStates.join(','),
                    type: 'GET',
                    success: function(data) {
                        $.each(data, function(index, city) {
                            var cityIdStr = String(city.id);
                            // Prevent duplicates
                            if ($('#city_ids option[value="' + cityIdStr + '"]').length === 0) {
                                $('#city_ids').append('<option value="' + cityIdStr + '" data-state-id="' + city.state_id + '">' + city.city_name + '</option>');
                            }
                        });
                        
                        // Maintain the selections explicitly on the DOM elements
                        selectedCities.forEach(function(cityId) {
                            $('#city_ids option[value="' + cityId + '"]').prop('selected', true);
                        });
                        $('#city_ids').trigger('change');
                    }
                });
            }

            previousStateIds = stateIds;
        });
    });
</script>
@endsection
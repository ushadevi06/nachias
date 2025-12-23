@extends('layouts.common')
@section('title', ($place ? 'Edit Place' : 'Add Place') . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $place ? 'Edit' : 'Add' }} Place</h4>
                    </div>

                    <form action="{{ url('places/add' . ($place ? '/' . $place->id : '')) }}" method="POST"
                        class="common-form">
                        @csrf
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select name="state_id" id="state_id" class="select2 form-select @error('state_id') is-invalid @enderror"
                                        data-placeholder="Select State">
                                        <option value="">Select State</option>
                                        @foreach($states as $state)
                                        <option value="{{ $state->id }}"
                                            {{ old('state_id', $place->state_id ?? '') == $state->id ? 'selected' : '' }}>
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
                                    <select name="city_id" id="city_id" class="select2 form-select @error('city_id') is-invalid @enderror"
                                        data-placeholder="Select City">
                                        <option value="">Select City</option>
                                        @foreach($cities as $city)
                                        <option value="{{ $city->id }}"
                                            {{ old('city_id', $place->city_id ?? '') == $city->id ? 'selected' : '' }}>
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
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('place_name') is-invalid @enderror" id="place_name"
                                        placeholder="Enter Place Name" name="place_name"
                                        value="{{ old('place_name', $place->place_name ?? '') }}">
                                    <label for="place_name">Place Name <span class="text-danger">*</span></label>
                                </div>
                                @error('place_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select name="place_type" id="place_type" class="select2 form-select @error('place_type') is-invalid @enderror"
                                        data-placeholder="Select Place Type">
                                        <option value="">Select Place Type</option>
                                        <option value="Residential"
                                            {{ old('place_type', $place->place_type ?? '') == 'Residential' ? 'selected' : '' }}>
                                            Residential</option>
                                        <option value="Commercial"
                                            {{ old('place_type', $place->place_type ?? '') == 'Commercial' ? 'selected' : '' }}>
                                            Commercial</option>
                                        <option value="Project Site"
                                            {{ old('place_type', $place->place_type ?? '') == 'Project Site' ? 'selected' : '' }}>
                                            Project Site</option>
                                    </select>
                                    <label for="place_type">Place Type <span class="text-danger">*</span></label>
                                </div>
                                @error('place_type')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('latitude') is-invalid @enderror" id="latitude" placeholder="Enter Latitude"
                                        name="latitude" value="{{ old('latitude', $place->latitude ?? '') }}">
                                    <label for="latitude">Latitude</label>
                                </div>
                                @error('latitude')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('longitude') is-invalid @enderror" id="longitude" placeholder="Enter Longitude"
                                        name="longitude" value="{{ old('longitude', $place->longitude ?? '') }}">
                                    <label for="longitude">Longitude</label>
                                </div>
                                @error('longitude')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror" data-placeholder="Select Status">
                                        <option value="">Select Status </option>
                                        <option value="Active"
                                            {{ old('status', $place->status ?? '') == 'Active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="Inactive"
                                            {{ old('status', $place->status ?? '') == 'Inactive' ? 'selected' : '' }}>
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
                                <a href="{{ url('places')}}" class="btn btn-secondary">Cancel</a>
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

    });
</script>
@endsection
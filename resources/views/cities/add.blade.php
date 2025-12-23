@extends('layouts.common')
@section('title', ($id ? 'Edit City' : 'Add City') . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $id ? 'Edit City' : 'Add City' }}</h4>
                    </div>

                    <form action="{{ url('cities/add' . ($id ? '/' . $id : '')) }}" method="POST" class="common-form">
                        @csrf

                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select name="state_id" class="select2 form-select @error('state_id') is-invalid @enderror" data-placeholder="Select State">
                                        <option value="">Select State</option>
                                        @foreach ($states as $state)
                                        <option value="{{ $state->id }}"
                                            {{ old('state_id', $city->state_id ?? '') == $state->id ? 'selected' : '' }}>
                                            {{ $state->state_name }} ({{ $state->state_code }})
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="select2Basic">State <span class="text-danger">*</span></label>
                                </div>
                                @error('state_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('city_name') is-invalid @enderror" placeholder="Enter City" name="city_name"
                                        value="{{ old('city_name', $city->city_name ?? '') }}">
                                    <label>City <span class="text-danger">*</span></label>
                                </div>
                                @error('city_name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('city_code') is-invalid @enderror" placeholder="Enter Code" name="city_code"
                                        value="{{ old('city_code', $city->city_code ?? '') }}">
                                    <label>City Code</label>
                                </div>
                                @error('city_code') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" class="select2 form-select @error('status') is-invalid @enderror" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active"
                                            {{ old('status', $city->status ?? '') == 'Active' ? 'selected' : '' }}>
                                            Active
                                        </option>
                                        <option value="Inactive"
                                            {{ old('status', $city->status ?? '') == 'Inactive' ? 'selected' : '' }}>
                                            Inactive
                                        </option>

                                    </select>
                                    <label>Status <span class="text-danger">*</span></label>
                                </div>
                                @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('cities') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
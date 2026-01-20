@extends('layouts.common')
@section('title', ($resource ? 'Edit Resource' : 'Add Resource') . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $resource ? 'Edit' : 'Add' }} Resource</h4>
                    </div>
                    <form action="{{ url('resources/add' . ($resource ? '/' . $resource->id : '')) }}" method="POST" class="common-form">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('resource_code') is-invalid @enderror" id="resource_code" 
                                        placeholder="Enter Resource Code" name="resource_code"
                                        value="{{ old('resource_code', $resource->resource_code ?? '') }}">
                                    <label for="resource_code">Resource Code <span class="text-danger">*</span></label>
                                </div>
                                @error('resource_code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('resource_name') is-invalid @enderror" id="resource_name" 
                                        placeholder="Enter Resource Name" name="resource_name"
                                        value="{{ old('resource_name', $resource->resource_name ?? '') }}">
                                    <label for="resource_name">Resource Name <span class="text-danger">*</span></label>
                                </div>
                                @error('resource_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select name="service_provider_id" id="service_provider_id" 
                                        class="select2 form-select @error('service_provider_id') is-invalid @enderror"
                                        data-placeholder="Select Service Provider (Plant)">
                                        <option value="">Select Service Provider (Plant)</option>
                                        @foreach($serviceProviders as $provider)
                                            <option value="{{ $provider->id }}" 
                                                {{ old('service_provider_id', $resource->service_provider_id ?? '') == $provider->id ? 'selected' : '' }}>
                                                {{ $provider->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="service_provider_id">Service Provider (Plant) <span class="text-danger">*</span></label>
                                </div>
                                @error('service_provider_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" 
                                        class="select2 form-select @error('status') is-invalid @enderror"
                                        data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active" {{ old('status', $resource->status ?? '') == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status', $resource->status ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                </div>
                                @error('status')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('resources') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

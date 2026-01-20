@extends('layouts.common')
@section('title', ($service ? 'Edit Service' : 'Add Service') . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $service ? 'Edit' : 'Add' }} Production Service</h4>
                    </div>
                    <form action="{{ url('production_services/add' . ($service ? '/' . $service->id : '')) }}"
                        method="POST" class="common-form">
                        @csrf
                        <div class="row g-4 justify-content-center">
                             <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('service_code') is-invalid @enderror" id="service_code"
                                        placeholder="Enter Service Code" name="service_code"
                                        value="{{ old('service_code', $service->service_code ?? '') }}">
                                    <label for="service_code">Service Code <span class="text-danger">*</span></label>
                                </div>
                                @error('service_code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('service_name') is-invalid @enderror" id="service_name"
                                        placeholder="Enter Service Name" name="service_name"
                                        value="{{ old('service_name', $service->service_name ?? '') }}">
                                    <label for="service_name">Service Name <span class="text-danger">*</span></label>
                                </div>
                                @error('service_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select name="operation_stage_id" id="operation_stage_id" class="select2 form-select @error('operation_stage_id') is-invalid @enderror"
                                        data-placeholder="Select Production Stage">
                                        <option value="">Select Production Stage</option>
                                        @foreach($operationStages as $stage)
                                            <option value="{{ $stage->id }}"
                                                {{ old('operation_stage_id', $service->operation_stage_id ?? '') == $stage->id ? 'selected' : '' }}>
                                                {{ $stage->operation_stage_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="operation_stage_id">Production Stage <span class="text-danger">*</span></label>
                                </div>
                                @error('operation_stage_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                           
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror"
                                        data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active"
                                            {{ old('status', $service->status ?? '') == 'Active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="Inactive"
                                            {{ old('status', $service->status ?? '') == 'Inactive' ? 'selected' : '' }}>
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
                                <a href="{{ url('production_services') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

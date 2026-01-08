@extends('layouts.common')
@section('title', ($collarType ? 'Edit Collar Type' : 'Add Collar Type') . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $collarType ? 'Edit' : 'Add' }} Collar Type</h4>
                    </div>
                    <form action="{{ url('collar_types/add' . ($collarType ? '/' . $collarType->id : '')) }}"
                        method="POST" class="common-form">
                        @csrf
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('collar_type_name') is-invalid @enderror" id="collar_type_name"
                                        placeholder="Enter Collar Type Name" name="collar_type_name"
                                        value="{{ old('collar_type_name', $collarType->collar_type_name ?? '') }}">
                                    <label for="collar_type_name">Collar Type Name <span class="text-danger">*</span></label>
                                </div>
                                @error('collar_type_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror"
                                        data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active"
                                            {{ old('status', $collarType->status ?? '') == 'Active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="Inactive"
                                            {{ old('status', $collarType->status ?? '') == 'Inactive' ? 'selected' : '' }}>
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
                                <a href="{{ url('collar_types') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

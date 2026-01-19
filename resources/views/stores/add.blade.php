@extends('layouts.common')
@section('title', ($storeType ? 'Edit Store' : 'Add Store') . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $storeType ? 'Edit' : 'Add' }} Store</h4>
                    </div>
                    <form action="{{ url('stores/add' . ($storeType ? '/' . $storeType->id : '')) }}" 
                        method="POST" class="common-form">
                        @csrf
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('store_type_name') is-invalid @enderror" id="store_type_name" 
                                        placeholder="Enter Store Name" name="store_type_name" 
                                        value="{{ old('store_type_name', $storeType->store_type_name ?? '') }}">
                                    <label for="store_type_name">Store Name <span class="text-danger">*</span></label>
                                </div>
                                @error('store_type_name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select select2 @error('status') is-invalid @enderror" id="status" name="status" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active" {{ old('status', $storeType->status ?? '') == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status', $storeType->status ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                </div>
                                @error('status')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('stores') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

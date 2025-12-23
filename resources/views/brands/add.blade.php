@extends('layouts.common')
@section('title', ($brand ? 'Edit' : 'Add') . ' Brand - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $brand ? 'Edit' : 'Add' }} Brand</h4>
                    </div>
                    <form action="{{ url('brands/add' . ($brand ? '/' . $brand->id : '')) }}" method="POST"
                        class="common-form">
                        @csrf
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('brand_name') is-invalid @enderror" id="brand_name" placeholder="Enter Brand"
                                        name="brand_name" value="{{ old('brand_name', $brand->brand_name ?? '') }}">
                                    <label for="brand_name">Brand <span class="text-danger">*</span></label>
                                </div>
                                @error('brand_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror"
                                        data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active"
                                            {{ old('status', $brand->status ?? '') == 'Active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="Inactive"
                                            {{ old('status', $brand->status ?? '') == 'Inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                </div>
                                @error('status')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit"
                                    class="btn btn-primary">{{ $brand ? 'Update' : 'Submit' }}</button>
                                <a href="{{ url('brands') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
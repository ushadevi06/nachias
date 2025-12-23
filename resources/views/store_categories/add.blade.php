@extends('layouts.common')
@section('title', ($storeCategory ? 'Edit' : 'Add') . ' Store Category - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $storeCategory ? 'Edit' : 'Add' }} Store Category</h4>
                    </div>
                    <form action="{{ url('store_categories/add' . ($storeCategory ? '/' . $storeCategory->id : '')) }}"
                        method="POST" class="common-form">
                        @csrf
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" placeholder="Enter Code"
                                        name="code" value="{{ old('code', $storeCategory->code ?? '') }}">
                                    <label for="code">Code <span class="text-danger">*</span></label>
                                </div>
                                @error('code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('category_name') is-invalid @enderror" id="category_name"
                                        placeholder="Enter Category" name="category_name"
                                        value="{{ old('category_name', $storeCategory->category_name ?? '') }}">
                                    <label for="category_name">Category <span class="text-danger">*</span></label>
                                </div>
                                @error('category_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100 @error('description') is-invalid @enderror" id="description"
                                        placeholder="Enter Description"
                                        name="description">{{ old('description', $storeCategory->description ?? '') }}</textarea>
                                    <label for="description">Description</label>
                                </div>
                                @error('description')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror"
                                        data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active"
                                            {{ old('status', $storeCategory->status ?? '') == 'Active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="Inactive"
                                            {{ old('status', $storeCategory->status ?? '') == 'Inactive' ? 'selected' : '' }}>
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
                                <a href="{{ url('store_categories') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.common')
@section('title', 'Add UOM - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $uom ? 'Edit' : 'Add' }} UOM</h4>
                    </div>

                    <form action="{{ url('uoms/add' . ($uom ? '/' . $uom->id : '')) }}" method="POST"
                        class="common-form">
                        @csrf
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('uom_code') is-invalid @enderror" id="uom-code" name="uom_code"
                                        placeholder="Enter UOM Code"
                                        value="{{ old('uom_code', $uom->uom_code ?? '') }}">
                                    <label for="uom-code">UOM Code <span class="text-danger">*</span></label>
                                </div>
                                @error('uom_code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('uom_name') is-invalid @enderror" id="uom-name" name="uom_name"
                                        placeholder="Enter UOM Name"
                                        value="{{ old('uom_name', $uom->uom_name ?? '') }}">
                                    <label for="uom-name">UOM Name <span class="text-danger">*</span></label>
                                </div>
                                @error('uom_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline mb-6">
                                    <textarea class="form-control h-px-100 @error('description') is-invalid @enderror" id="description" name="description"
                                        placeholder="Enter Description">{{ old('description', $uom->description ?? '') }}</textarea>
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
                                            {{ old('status', $uom->status ?? '') == 'Active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="Inactive"
                                            {{ old('status', $uom->status ?? '') == 'Inactive' ? 'selected' : '' }}>
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
                                <a href="{{ url('uoms') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
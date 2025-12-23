@extends('layouts.common')
@section('title', ($sizeRatio ? 'Edit Size/Ratio' : 'Add Size/Ratio') . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box mb-4">
                        <h4 class="mb-0">{{ $sizeRatio ? 'Edit' : 'Add' }} Size/Ratio</h4>
                    </div>
                    <form action="{{ url('size_ratio/add' . ($sizeRatio ? '/' . $sizeRatio->id : '')) }}" method="POST"
                        class="common-form">
                        @csrf
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('size') is-invalid @enderror" id="size" name="size"
                                        placeholder="Enter Size (comma separated)"
                                        value="{{ old('size', $sizeRatio->size ?? '') }}">
                                    <label for="size">Size * (comma separated e.g., 38,40,42,44)</label>
                                </div>
                                @error('size')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('ratio') is-invalid @enderror" id="ratio" name="ratio"
                                        placeholder="Enter Ratio (comma separated)"
                                        value="{{ old('ratio', $sizeRatio->ratio ?? '') }}">
                                    <label for="ratio">Ratio * (comma separated e.g., 1,2,3,1)</label>
                                </div>
                                @error('ratio')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                                @error('ratio_custom')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror"
                                        data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active"
                                            {{ old('status', $sizeRatio->status ?? '') == 'Active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="Inactive"
                                            {{ old('status', $sizeRatio->status ?? '') == 'Inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                    <label for="status">Status *</label>
                                </div>
                                @error('status')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary me-2">Submit</button>
                                <a href="{{ url('size_ratio') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
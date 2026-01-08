@extends('layouts.common')
@section('title', ($cuffType ? 'Edit Cuff Type' : 'Add Cuff Type') . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $cuffType ? 'Edit' : 'Add' }} Cuff Type</h4>
                    </div>
                    <form action="{{ url('cuff_types/add' . ($cuffType ? '/' . $cuffType->id : '')) }}"
                        method="POST" class="common-form">
                        @csrf
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('cuff_type_name') is-invalid @enderror" id="cuff_type_name"
                                        placeholder="Enter Cuff Type Name" name="cuff_type_name"
                                        value="{{ old('cuff_type_name', $cuffType->cuff_type_name ?? '') }}">
                                    <label for="cuff_type_name">Cuff Type Name <span class="text-danger">*</span></label>
                                </div>
                                @error('cuff_type_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror"
                                        data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active"
                                            {{ old('status', $cuffType->status ?? '') == 'Active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="Inactive"
                                            {{ old('status', $cuffType->status ?? '') == 'Inactive' ? 'selected' : '' }}>
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
                                <a href="{{ url('cuff_types') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

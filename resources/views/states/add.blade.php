@extends('layouts.common')
@section('title', ($id ? 'Edit' : 'Add') . ' State - ' . env('WEBSITE_NAME', 'States Management'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $id ? 'Edit' : 'Add' }} State</h4>
                    </div>
                    <form action="{{ url('states/add' . ($id ? '/' . $id : '')) }}" method="POST" autocomplete="off">
                        @csrf
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('state_name') is-invalid @enderror"
                                        id="state-name" name="state_name" placeholder="Enter State Name"
                                        value="{{ old('state_name', $state->state_name ?? '') }}">
                                    <label for="state-name">State Name *</label>
                                    @error('state_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('state_code') is-invalid @enderror"
                                        id="state-code" name="state_code" placeholder="Enter State Code"
                                        value="{{ old('state_code', $state->state_code ?? '') }}">
                                    <label for="state-code">State Code *</label>
                                    @error('state_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select id="status" name="status"
                                        class="select2 form-select @error('status') is-invalid @enderror"
                                        data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active"
                                            {{ old('status', $state->status ?? '') == 'Active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="Inactive"
                                            {{ old('status', $state->status ?? '') == 'Inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                    <label for="status">Status *</label>
                                    @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('states') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
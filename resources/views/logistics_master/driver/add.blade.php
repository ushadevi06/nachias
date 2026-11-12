@extends('layouts.common')
@section('title', ($driver ? 'Edit Driver' : 'Add Driver') . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $driver ? 'Edit' : 'Add' }} Driver</h4>
                    </div>
                    <form action="{{ url('drivers/add' . ($driver ? '/' . $driver->id : '')) }}" method="POST" class="common-form" autocomplete="off">
                        @csrf
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('driver_name') is-invalid @enderror" id="driver_name" placeholder="Enter Driver Name" name="driver_name" value="{{ old('driver_name', $driver->driver_name ?? '') }}">
                                    <label for="driver_name">Driver Name <span class="text-danger">*</span></label>
                                </div>
                                @error('driver_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" min="0" class="form-control @error('loading_unloading_amount') is-invalid @enderror" id="loading_unloading_amount" placeholder="Enter Loading / Unloading Amount" name="loading_unloading_amount" value="{{ old('loading_unloading_amount', isset($driver) ? number_format((float) $driver->loading_unloading_amount, 2, '.', '') : '0.00') }}">
                                    <label for="loading_unloading_amount">Loading / Unloading Amount (₹) <span class="text-danger">*</span></label>
                                </div>
                                @error('loading_unloading_amount')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active" {{ old('status', $driver->status ?? 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status', $driver->status ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                </div>
                                @error('status')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('drivers') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


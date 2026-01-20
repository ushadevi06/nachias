@extends('layouts.common')
@section('title', ($shift ? 'Edit Shift' : 'Add Shift') . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $shift ? 'Edit' : 'Add' }} Shift</h4>
                    </div>
                    <form action="{{ url('shifts/add' . ($shift ? '/' . $shift->id : '')) }}" method="POST" class="common-form">
                        @csrf
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('shift_name') is-invalid @enderror" id="shift_name" 
                                        placeholder="Enter Shift Name" name="shift_name"
                                        value="{{ old('shift_name', $shift->shift_name ?? '') }}">
                                    <label for="shift_name">Shift Name <span class="text-danger">*</span></label>
                                </div>
                                @error('shift_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" 
                                        name="start_time"
                                        value="{{ old('start_time', $shift->start_time ?? '') }}">
                                    <label for="start_time">Start Time <span class="text-danger">*</span></label>
                                </div>
                                @error('start_time')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" 
                                        name="end_time"
                                        value="{{ old('end_time', $shift->end_time ?? '') }}">
                                    <label for="end_time">End Time <span class="text-danger">*</span></label>
                                </div>
                                @error('end_time')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select select2 @error('status') is-invalid @enderror" id="status" name="status" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active" {{ old('status', $shift->status ?? '') == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status', $shift->status ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                </div>
                                @error('status')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('shifts') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

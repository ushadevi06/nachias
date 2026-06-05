@extends('layouts.common')
@section('title', ($device ? 'Edit Device' : 'Add Device') . ' - ' . env('WEBSITE_NAME'))
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
                        <h4>{{ $device ? 'Edit' : 'Add' }} Device</h4>
                    </div>
                    <form action="{{ url('devices/add' . ($device ? '/' . $device->id : '')) }}" method="POST" class="common-form" autocomplete="off">
                        @csrf
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('device_name') is-invalid @enderror" id="device_name" placeholder="Enter Device Name" name="device_name" value="{{ old('device_name', $device->device_name ?? '') }}">
                                    <label for="device_name">Device Name <span class="text-danger">*</span></label>
                                </div>
                                @error('device_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('serial_number') is-invalid @enderror" id="serial_number" placeholder="Enter Serial Number" name="serial_number" value="{{ old('serial_number', $device->serial_number ?? '') }}">
                                    <label for="serial_number">Serial Number <span class="text-danger">*</span></label>
                                </div>
                                @error('serial_number')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>                            
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('devices') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

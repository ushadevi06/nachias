@extends('layouts.common')
@section('title', ($shippingMethod ? 'Edit Shipping Method' : 'Add Shipping Method') . ' - ' . env('WEBSITE_NAME'))
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
                        <h4>{{ $shippingMethod ? 'Edit' : 'Add' }} Shipping Method</h4>
                    </div>
                    <form action="{{ url('shipping_methods/add' . ($shippingMethod ? '/' . $shippingMethod->id : '')) }}" method="POST" class="common-form" autocomplete="off">
                        @csrf
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter Method Name" name="name" value="{{ old('name', $shippingMethod->name ?? '') }}">
                                    <label for="name">Method Name <span class="text-danger">*</span></label>
                                </div>
                                @error('name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active" {{ old('status', $shippingMethod->status ?? '') == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status', $shippingMethod->status ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                </div>
                                @error('status')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('shipping_methods') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

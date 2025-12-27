@extends('layouts.common')
@section('title', ($tax ? 'Edit Taxes' : 'Add Taxes') . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $tax ? 'Edit' : 'Add' }} Taxes</h4>
                    </div>
                    <form action="{{ url('taxes/add' . ($tax ? '/' . $tax->id : '')) }}" method="POST"
                        class="common-form">
                        @csrf
                        <div class="row justify-content-center g-4">
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('item_name') is-invalid @enderror" id="item_name" placeholder="Enter Item Name"
                                        name="item_name" value="{{ old('item_name', $tax->item_name ?? '') }}">
                                    <label for="item_name">Item Name <span class="text-danger">*</span></label>
                                </div>
                                @error('item_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" class="form-control @error('tax_rate') is-invalid @enderror" id="tax_rate"
                                        placeholder="Enter Tax Rate %" name="tax_rate"
                                        value="{{ old('tax_rate', $tax->tax_rate ?? '') }}">
                                    <label for="tax_rate">Tax Rate % <span class="text-danger">*</span></label>
                                </div>
                                @error('tax_rate')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror"
                                        data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active"
                                            {{ old('status', $tax->status ?? '') == 'Active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="Inactive"
                                            {{ old('status', $tax->status ?? '') == 'Inactive' ? 'selected' : '' }}>
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
                                <a href="{{ url('taxes') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
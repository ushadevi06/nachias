@extends('layouts.common')
@section('title', 'Add Billing - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <form action="{{ url('billing/add/' . ($billing->id ?? '')) }}" method="POST" class="common-form" autocomplete="off" novalidate>
                @csrf
                <div class="card mb-6">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>{{ isset($billing) ? 'Edit Billing' : 'Add Billing' }}</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="billing_name" id="billing_name"
                                        placeholder="Enter Billing Name"
                                        value="{{ old('billing_name', $billing->billing_name ?? '') }}">
                                    <label for="billing_name">Billing Name <span class="text-danger">*</span></label>
                                </div>
                                @error('billing_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="bill_no" id="bill_no"
                                        placeholder="Enter Bill Number"
                                        value="{{ old('bill_no', $billing->bill_no ?? '') }}">
                                    <label for="bill_no">Bill Number <span class="text-danger">*</span></label>
                                </div>
                                @error('bill_no')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-6">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" id="billing_type" name="billing_type" data-placeholder="Select Billing Type">
                                        <option value="">Select Billing Type</option>
                                        @foreach(['Purchase','Sales','Service','Job Work','Transport'] as $type)
                                            <option value="{{ $type }}" {{ old('billing_type', $billing->billing_type ?? '') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                    <label for="billing_type">Billing Type</label>
                                </div>
                                @error('billing_type')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control bill_date" id="bill_date" placeholder="Enter Bill Date" name="bill_date" value="{{ old('bill_date', isset($billing) ? $billing->bill_date->format('d-m-Y') : '') }}">
                                    <label for="bill_date">Bill Date <span class="text-danger">*</span></label>
                                </div>
                                @error('bill_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-6">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="form-select select2" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        @foreach(['Pending','Partially Paid','Paid','Cancelled'] as $st)
                                            @php
                                                $optionDisabled = '';
                                                if (isset($billing)) {
                                                    if ($billing->status === 'Paid' && $st !== 'Paid') {
                                                        $optionDisabled = 'disabled';
                                                    } elseif ($billing->status === 'Cancelled' && !in_array($st, ['Cancelled', 'Paid'])) {
                                                        $optionDisabled = 'disabled';
                                                    }
                                                }
                                            @endphp
                                            <option value="{{ $st }}" {{ old('status', $billing->status ?? '') == $st ? 'selected' : '' }} {{ $optionDisabled }}>{{ $st }}</option>
                                        @endforeach
                                    </select>
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                </div>
                                @error('status')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control" name="amount" id="amount" placeholder="Enter Amount" step="0.01" min="0.01" value="{{ old('amount', $billing->amount ?? '') }}">
                                    <label for="amount">Amount <span class="text-danger">*</span></label>
                                </div>
                                @error('amount')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-6">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control" name="reason" id="reason" placeholder="Enter Reason">{{ old('reason', $billing->reason ?? '') }}</textarea>
                                    <label for="reason">Reason</label>
                                </div>
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('billing') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@extends('layouts.common')
@section('title', 'Add State - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>Add State</h4>
                    </div>
                    <form action="" method="POST" class="common-form">
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6 col-xl-12">
                                <div class=" form-floating form-floating-outline">
                                    <select id="country" class="form-select select2" data-placeholder="Select Country" required>
                                        <option value="">Select Country</option>
                                        <option value="India">India</option>
                                        <option value="United States">United States</option>
                                        <option value="United Kingdom">United Kingdom</option>
                                        <option value="Canada">Canada</option>
                                        <option value="Australia">Australia</option>
                                    </select>
                                    <label for="country">Country *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="state-code" name="code" placeholder="Enter State Code" required>
                                    <label for="state-code">State Code *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="state-name" name="name" placeholder="Enter State Name" required>
                                    <label for="state-name">State Name *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select id="status" class="select2 form-select" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                    <label for="status">Status</label>
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
@extends('layouts.common')
@section('title', 'Add Size/Ratio - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box mb-4">
                        <h4 class="mb-0">Add Size/Ratio</h4>
                    </div>

                    <form action="" method="POST" class="common-form">
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="size" name="size" placeholder="Enter Size">
                                    <label for="size">Size *</label>
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="ratio" name="ratio" placeholder="Enter Ratio">
                                    <label for="ratio">Ratio *</label>
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
                            <div class="text-end">
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

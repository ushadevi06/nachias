@extends('layouts.common')
@section('title', 'Add Document Repository - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="" method="POST" class="common-form">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Add Document Repository</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="document_name" placeholder="Enter Document Name" name="document_name">
                                    <label for="document_name">Document Name * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" data-placeholder="Select Document Type">
                                        <option value="">Select Document Type</option>
                                        <option value="Certification">Certification</option>
                                        <option value="HR">HR</option>
                                        <option value="Compliance">Compliance</option>
                                        <option value="Policy">Policy</option>
                                    </select>
                                    <label for="wastage_reason">Document Type * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" data-placeholder="Select Department">
                                        <option value="">Select Department</option>
                                        <option value="HR">HR</option>
                                        <option value="Production">Production</option>
                                        <option value="Quality">Quality</option>
                                        <option value="Finance">Finance</option>
                                    </select>
                                    <label for="wastage_reason">Department * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control validity_date" id="validity_date" placeholder="Enter Validity Date" name="validity_date">
                                    <label for="validity_date">Validity Date * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="file" id="formFile">
                                    <label for="formFile" class="form-label">File Upload</label>
                                </div>
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('document_repository') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.validity_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            minDate: 'today',
            allowInput: true
        });

    });
</script>
@endsection
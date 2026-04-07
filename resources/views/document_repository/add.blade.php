@extends('layouts.common')
@section('title', ($document ? 'Edit' : 'Add') . ' Document Repository - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $document ? 'Edit' : 'Add' }} Document Repository</h4>
                    </div>
                    <form action="{{ url('document_repository/add' . ($document ? '/' . $document->id : '') ) }}" method="POST" class="common-form" enctype="multipart/form-data" autocomplete="off">
                        @csrf
                    <div class="row g-4">
                        <div class="col-md-6 col-xl-6">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="document_name" placeholder="Enter Document Name" name="document_name" value="{{ old('document_name',$document->document_name ?? '') }}">
                                <label for="document_name">Document Name * </label>
                            </div>
                            @error('document_name')
                            <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 col-xl-6">
                            <div class="form-floating form-floating-outline">
                                <select class="select2 form-select" name="document_type" data-placeholder="Select Document Type">
                                    <option value="">Select Document Type</option>
                                    <option value="Certification" {{ old('document_type',$document->document_type ?? '') == 'Certification' ? 'selected' : '' }}>Certification</option>
                                    <option value="HR" {{ old('document_type',$document->document_type ?? '') == 'HR' ? 'selected' : '' }}>HR</option>
                                    <option value="Compliance" {{ old('document_type',$document->document_type ?? '') == 'Compliance' ? 'selected' : '' }}>Compliance</option>
                                    <option value="Policy" {{ old('document_type',$document->document_type ?? '') == 'Policy' ? 'selected' : '' }}>Policy</option>
                                </select>
                                <label for="document_type">Document Type * </label>
                            </div>
                            @error('document_type')
                            <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 col-xl-6">
                            <div class="form-floating form-floating-outline">
                                <select class="select2 form-select" name="department_id" data-placeholder="Select Department">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id',$document->department_id ?? '') == $department->id ? 'selected' : '' }}>{{ $department->department }}</option>
                                    @endforeach
                                </select>
                                <label for="deparment">Department * </label>
                            </div>
                            @error('department_id')
                            <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 col-xl-6">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control validity_date" id="validity_date" placeholder="Enter Validity Date" name="validity_date" value="{{ old('validity_date', isset($document->validity_date) ? date('d-m-Y', strtotime($document->validity_date)) : '') }}">
                                <label for="validity_date">Validity Date</label>
                            </div>
                            @error('validity_date')
                            <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 col-xl-6">
                            <div class="form-floating form-floating-outline">
                                <textarea name="remarks" id="remarks" class="form-control" placeholder="Remarks">{{ old('remarks',$document->remarks ?? '') }}</textarea>
                                <label for="remarks">Remarks</label>
                            </div>
                            @error('remarks')
                            <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 col-xl-6">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="file" id="formFile" name="file">
                                <label for="formFile" class="form-label">File Upload {{ $document ? '' : '*' }}</label>
                            </div>
                            <small class="text-muted d-block mt-2">Max file size: 2MB per file. Supported: JPG, PNG, WEBP, PDF, DOC, DOCX</small>
                            @error('file')
                            <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                            @if(isset($document->file) && $document->file != '')
                                <a href="{{ url('uploads/documents/'.$document->file) }}" target="_blank"><i class="ri ri-image-line"></i> View</a>
                            @endif
                        </div>
                        <div class="col-lg-12 text-end">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ url('document_repository') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {

    });
</script>
@endsection
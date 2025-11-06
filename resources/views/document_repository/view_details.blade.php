@extends('layouts.common')
@section('title', 'View Document Repository - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Document Repository</h4>
                <a href="{{ url('document_repository') }}" class="btn btn-primary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="detail-title">Document Name: </label>
                            <div class="text-muted">ISO Certification 2024</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Document Type:</label>
                            <div class="text-muted">Certification</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Department:</label>
                            <div class="text-muted">Quality</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Validity Date:</label>
                            <div class="text-muted">31-12-2025</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">File:</label>
                            <div class="text-muted">
                                <a href="{{ url('assets/images/pdf/report.pdf') }}" target="_blank"><img src="{{ url('assets/images/pdf_image.jpg') }}" alt="" width="30"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
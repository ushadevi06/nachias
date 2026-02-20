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
                            <div class="text-muted">{{ $document->document_name }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Document Type:</label>
                            <div class="text-muted">{{ $document->document_type }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Department:</label>
                            <div class="text-muted">{{ $document->department->department ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Validity Date:</label>
                            <div class="text-muted">{{ $document->validity_date ? date('d-m-Y', strtotime($document->validity_date)) : '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Status:</label>
                            <div class="text-muted">
                                @if($document->validity_date)
                                    @if(Carbon::parse($document->validity_date)->isPast() && !Carbon::parse($document->validity_date)->isToday())
                                        <span class="badge bg-danger">Expired</span>
                                    @else
                                        <span class="badge bg-success">Active</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">N/A</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">File:</label>
                            <div class="text-muted">
                                @if($document->file)
                                    @php
                                        $ext = pathinfo($document->file, PATHINFO_EXTENSION);
                                        $img = ($ext == 'pdf') ? 'pdf_image.jpg' : 'word_image.png';
                                    @endphp
                                    <a href="{{ url('uploads/documents/' . $document->file) }}" target="_blank"><img src="{{ url('assets/images/' . $img) }}" alt="" width="30"></a>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        @if($document->remarks)
                        <div class="col-md-12">
                            <label class="detail-title">Remarks:</label>
                            <div class="text-muted">{{ $document->remarks }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
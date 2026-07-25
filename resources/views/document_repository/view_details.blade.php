@extends('layouts.common')
@section('title', 'View Document Repository - ' . env('WEBSITE_NAME'))@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">Document Details</h4>
                    <p class="text-muted mb-0">{{ $document->document_name }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ url('document_repository') }}" class="btn btn-outline-secondary d-flex align-items-center">
                        <i class="icon-base ri ri-arrow-left-line me-1"></i> Back
                    </a>
                </div>
            </div>

            <div class="row g-4">
                <!-- Main Info Card -->
                <div class="col-xl-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase text-primary small font-bold tracking-wider mb-4">Core Information</h6>
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded-3">
                                        <label class="text-muted small d-block mb-1">Reference No.</label>
                                        <h5 class="mb-0 text-dark">{{ $document->reference_no ?: 'N/A' }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded-3">
                                        <label class="text-muted small d-block mb-1">Document Type</label>
                                        <h5 class="mb-0 text-dark">{{ $document->document_type }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded-3">
                                        <label class="text-muted small d-block mb-1">Department</label>
                                        <h5 class="mb-0 text-dark">{{ $document->department->department ?? 'N/A' }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small d-block mb-1">Validity Date</label>
                                    <p class="font-medium text-dark h6 mb-0">{{ $document->validity_date ? date('d-m-Y', strtotime($document->validity_date)) : 'Lifetime' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small d-block mb-1">Status</label>
                                    @if($document->status === 'Archived')
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">Archived</span>
                                    @elseif($document->status === 'Expired')
                                        <span class="badge bg-danger rounded-pill px-3 py-2">Expired</span>
                                    @else
                                        @if($document->validity_date && \Carbon\Carbon::parse($document->validity_date)->isPast() && !\Carbon\Carbon::parse($document->validity_date)->isToday())
                                            <span class="badge bg-danger rounded-pill px-3 py-2">Expired</span>
                                        @else
                                            <span class="badge bg-success rounded-pill px-3 py-2">Active</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Remarks & Attachments Section -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-{{ $document->file ? '8' : '12' }}">
                                    <h6 class="text-uppercase text-primary small font-bold tracking-wider mb-3">Remarks</h6>
                                    <div class="p-3 border rounded border-dashed text-muted italic" style="min-height: 100px;">
                                        {{ $document->remarks ?: 'No additional remarks provided for this document.' }}
                                    </div>
                                </div>
                                @if($document->file)
                                <div class="col-md-4">
                                    <h6 class="text-uppercase text-primary small font-bold tracking-wider mb-3">Attachment File</h6>
                                    @php
                                        $attachment = $document->file;
                                        $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                        $url = url('uploads/documents/' . $attachment);
                                    @endphp

                                    <div class="attachment-thumb border rounded p-1 bg-white shadow-sm position-relative d-flex justify-content-center align-items-center mb-2" style="width: 100%; height: 100px;" title="{{ basename($attachment) }}">
                                        @if($isImage)
                                            <img src="{{ $url }}" class="w-100 h-100 object-fit-cover rounded cursor-pointer view-image" data-image="{{ $url }}" alt="Document">
                                        @else
                                            <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-light rounded text-decoration-none shadow-none text-primary">
                                                <i class="ri ri-file-text-line fs-2"></i>
                                                <span class="badge bg-primary text-white mt-1" style="font-size: 10px;">{{ strtoupper($extension) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-primary flex-fill">
                                            <i class="ri ri-eye-line me-1"></i> Preview
                                        </a>
                                        <a href="{{ $url }}" download class="btn btn-sm btn-primary flex-fill">
                                            <i class="ri ri-download-line me-1"></i> Download
                                        </a>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Snapshot Widget -->
                <div class="col-xl-4">
                    <div class="card border-0 bg-primary text-white shadow-lg sticky-top" style="top: 2rem; z-index: 10;">
                        <div class="card-body p-4 text-center">
                            <div class="mb-4">
                                <div class="bg-opacity-10 d-inline-flex p-3 rounded-circle mb-3">
                                    <i class="ri ri-folder-shield-2-line ri-2x text-white"></i>
                                </div>
                                <h6 class="text-white text-uppercase small tracking-widest">Document Registry</h6>
                                <h4 class="text-white font-bold mb-0 text-truncate" title="{{ $document->document_name }}">{{ $document->document_name }}</h4>
                            </div>
                            <hr class="border-white opacity-10">
                            <div class="text-start">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-white-50 small">Type</span>
                                    <span class="font-medium text-end">{{ $document->document_type }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($document->created_at)
                    <div class="mt-4 p-4 text-center">
                        <p class="small text-muted mb-0">Registered on {{ $document->created_at->format('M d, Y') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .font-bold { font-weight: 700; }
    .font-medium { font-weight: 500; }
    .border-dashed { border-style: dashed !important; }
    .bg-light { background-color: #f8f9fa !important; }
    .tracking-wider { letter-spacing: 0.05em; }
    .tracking-widest { letter-spacing: 0.1em; }
</style>
@endsection

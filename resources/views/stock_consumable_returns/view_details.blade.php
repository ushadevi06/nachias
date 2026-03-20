@extends('layouts.common')
@section('title', 'View Stock Consumable Details - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Stock Consumable Details</h4>
                <a href="{{ url('stock_consumables_returns') }}" class="btn btn-outline-secondary">
                    <i class="ri ri-arrow-left-line me-1"></i> Back
                </a>
            </div>

            <div class="row g-4">
                <!-- Main Details Card -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h5 class="mb-0 fw-bold text-primary"><i class="ri ri-information-line me-2"></i>Material Information</h5>
                        </div>
                        <div class="card-body py-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar avatar-sm bg-label-primary me-3 p-2 rounded">
                                            <i class="ri ri-flask-line fs-4"></i>
                                        </div>
                                        <div>
                                            <label class="text-muted small fw-medium text-uppercase mb-1 d-block">Material Name</label>
                                            <h6 class="mb-0 fw-bold">{{ $consumable->rawMaterial ? $consumable->rawMaterial->name : '-' }}</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar avatar-sm bg-label-info me-3 p-2 rounded">
                                            <i class="ri ri-ruler-2-line fs-4"></i>
                                        </div>
                                        <div>
                                            <label class="text-muted small fw-medium text-uppercase mb-1 d-block">UOM</label>
                                            <h6 class="mb-0 fw-bold">{{ $consumable->uom ? $consumable->uom->uom_code : '-' }}</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 text-truncate">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar avatar-sm bg-label-success me-3 p-2 rounded">
                                            <i class="ri ri-hashtag fs-4"></i>
                                        </div>
                                        <div>
                                            <label class="text-muted small fw-medium text-uppercase mb-1 d-block">Art No</label>
                                            <h6 class="mb-0 fw-bold">{{ $consumable->art_no }}</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar avatar-sm bg-label-warning me-3 p-2 rounded">
                                            <i class="ri ri-calendar-line fs-4"></i>
                                        </div>
                                        <div>
                                            <label class="text-muted small fw-medium text-uppercase mb-1 d-block">Issued Date</label>
                                            <h6 class="mb-0 fw-bold">{{ $consumable->created_at->format('d-m-Y') }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4 opacity-25">

                            <div class="row g-4">
                                <div class="col-md-4">
                                    <div class="bg-light p-3 rounded text-center">
                                        <label class="text-muted small fw-medium text-uppercase mb-1 d-block">F/S Qty</label>
                                        <h5 class="mb-0 fw-bold">{{ number_format($consumable->fs_qty, 3) }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="bg-light p-3 rounded text-center">
                                        <label class="text-muted small fw-medium text-uppercase mb-1 d-block">H/S Qty</label>
                                        <h5 class="mb-0 fw-bold">{{ number_format($consumable->hs_qty, 3) }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="bg-primary bg-opacity-10 p-3 rounded text-center border border-primary border-opacity-25">
                                        <label class="text-primary small fw-medium text-uppercase mb-1 d-block">Total Issue Qty</label>
                                        <h5 class="mb-0 fw-bold text-primary">{{ number_format($consumable->actual_qty, 3) }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Source Details Card -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h5 class="mb-0 fw-bold text-secondary"><i class="ri ri-links-line me-2"></i>Source Reference</h5>
                        </div>
                        <div class="card-body py-4">
                            <div class="mb-4">
                                <label class="text-muted small fw-medium text-uppercase mb-1 d-block">Job Card No</label>
                                <h6 class="mb-0 fw-bold text-dark">{{ $consumable->jobCard ? $consumable->jobCard->job_card_no : '-' }}</h6>
                            </div>
                            <div class="mb-0">
                                <label class="text-muted small fw-medium text-uppercase mb-1 d-block">Production Stage</label>
                                <span class="badge bg-label-secondary">{{ $consumable->stage }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-sm { width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; }
    .bg-label-primary { background-color: rgba(105, 108, 255, 0.1) !important; color: #696cff !important; }
    .bg-label-info { background-color: rgba(3, 195, 236, 0.1) !important; color: #03c3ec !important; }
    .bg-label-success { background-color: rgba(113, 221, 55, 0.1) !important; color: #71dd37 !important; }
    .bg-label-warning { background-color: rgba(255, 171, 0, 0.1) !important; color: #ffab00 !important; }
    .bg-label-secondary { background-color: rgba(133, 146, 163, 0.1) !important; color: #8592a3 !important; }
</style>
@endsection
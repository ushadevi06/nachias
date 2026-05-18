@extends('layouts.common')
@section('title', 'Production Report - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <!-- Header Section -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-primary">Production Report</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Reports</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button id="btn-excel" class="btn btn-outline-primary btn-sm rounded-pill"><i class="ri ri-file-excel-line me-1"></i> Excel</button>
            <button id="btn-pdf" class="btn btn-outline-danger btn-sm rounded-pill"><i class="ri ri-file-pdf-line me-1"></i> PDF</button>
            <button id="btn-print" class="btn btn-primary btn-sm rounded-pill px-3"><i class="ri ri-printer-line me-1"></i> Print</button>
        </div>
    </div>

    <!-- Global Filter Card -->
    <div class="card shadow-sm border-0 mb-4 premium-filter-card">
        <div class="card-body py-4">
            <form id="productionReportForm" action="{{ url('production_reports') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">From Date</label>
                    <input type="text" class="form-control start_date" name="from_date" placeholder="DD-MM-YYYY" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">To Date</label>
                    <input type="text" class="form-control end_date" name="to_date" placeholder="DD-MM-YYYY" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Unit</label>
                    <select class="form-select select2" name="unit_id" data-placeholder="Select Unit">
                        <option value=""></option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Process</label>
                    <select class="form-select select2" data-placeholder="Select Process">
                        <option value=""></option>
                        <option value="1">Fabric Spread</option>
                        <option value="2">Cuff Stitching</option>
                        <option value="3">Button Fixing</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label small fw-bold text-muted">Jobcard</label>
                    <input type="text" class="form-control" placeholder="No.">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">
                        <i class="ri ri-search-line me-1"></i> Search
                    </button>
                    <a href="{{ url('production_reports') }}" class="btn btn-outline-light w-100 rounded-pill border">
                        <i class="ri ri-refresh-line me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabs Interface -->
    <div class="card shadow-sm border-0 premium-content-card">
        <div class="card-header bg-white border-bottom-0 p-0">
            <ul class="nav nav-tabs nav-fill premium-nav-tabs" id="productionTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#production-wip" type="button" role="tab">Production WIP Unit Wise</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#performance-report" type="button" role="tab">Performance Individual</button>
                </li>
                {{-- <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#incentive-report" type="button" role="tab">Incentive Report</button>
                </li> --}}
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#process-wise" type="button" role="tab">Production Report Section Wise</button>
                </li>
                {{-- <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#production-cost" type="button" role="tab">Production Cost (Section Wise & Unit Wise)</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#alteration-report" type="button" role="tab">Alteration Quantity</button>
                </li> --}}
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#completion-report" type="button" role="tab">Job Card Completed Date</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#brand-production" type="button" role="tab">Brand Wise Unit Production</button>
                </li>
            </ul>
        </div>
        <div class="card-body py-4">
            <div class="tab-content">
                <!-- 1. Production WIP Unit Wise -->
                <div class="tab-pane fade show active" id="production-wip" role="tabpanel">
                    @include('reports.production_report.production_wip')
                </div>

                <!-- 2. Performance Individual Report -->
                <div class="tab-pane fade" id="performance-report" role="tabpanel">
                    @include('reports.production_report.performance_individual')
                </div>

                <!-- 3. Incentive Report -->
                <div class="tab-pane fade" id="incentive-report" role="tabpanel">
                    @include('reports.production_report.incentive_report')
                </div>

                <!-- 4. Production Report Section Wise -->
                <div class="tab-pane fade" id="process-wise" role="tabpanel">
                    @include('reports.production_report.section_wise_production')
                </div>

                <!-- 5. Production Cost (Section Wise & Unit Wise) -->
                <div class="tab-pane fade" id="production-cost" role="tabpanel">
                    @include('reports.production_report.production_cost')
                </div>

                <!-- 6. Alteration Quantity (Job Card Wise & Unit Wise) -->
                <div class="tab-pane fade" id="alteration-report" role="tabpanel">
                    @include('reports.production_report.alteration_quantity')
                </div>

                <!-- 7. Job Card Completed Date (Unit Wise & Quantity & During Days) -->
                <div class="tab-pane fade" id="completion-report" role="tabpanel">
                    @include('reports.production_report.job_card_completion')
                </div>

                <!-- 8. Brand Wise Unit Production (Sleeve & Style Wise) -->
                <div class="tab-pane fade" id="brand-production" role="tabpanel">
                    @include('reports.production_report.brand_wise_production')
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .premium-filter-card {
        border-radius: 12px;
        background: #fff;
    }

    .premium-content-card {
        border-radius: 12px;
        overflow: hidden;
    }

    .premium-nav-tabs {
        border: none;
        background: #f8fafc;
    }

    .premium-nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        padding: 1.25rem 0.5rem;
        color: #64748b;
        font-weight: 600;
        font-size: 0.85rem;
        border-radius: 0;
        transition: all 0.3s ease;
    }

    .premium-nav-tabs .nav-link:hover {
        background: #f1f5f9;
        color: #1e293b;
    }

    .premium-nav-tabs .nav-link.active {
        color: var(--bs-primary);
        background: #fff;
        border-bottom-color: var(--bs-primary);
    }

    .table thead th {
        border-top: none;
        border-bottom: 2px solid #e2e8f0;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: #475569;
        padding: 1.2rem 0.75rem;
    }

    .table tbody td {
        padding: 1.1rem 0.75rem;
        vertical-align: middle;
        font-size: 0.88rem;
        border-bottom: 1px solid #f1f5f9;
    }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#productionReportForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalBtnHtml = submitBtn.html();

        submitBtn.html('<span class="spinner-border spinner-border-sm me-1"></span> Searching...').prop('disabled', true);
        $('.tab-content').css('opacity', '0.6');

        $.ajax({
            url: form.attr('action'),
            method: 'GET',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                $.each(response, function(tabId, html) {
                    const targetTab = $('#' + tabId);
                    if (targetTab.length) {
                        targetTab.html(html);
                    }
                });
                
                if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                    tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl)
                    });
                }
            },
            error: function() {
                alert('An error occurred while fetching the report data. Please try again.');
            },
            complete: function() {
                submitBtn.html(originalBtnHtml).prop('disabled', false);
                $('.tab-content').css('opacity', '1');
            }
        });
    });

    $('#btn-excel').on('click', function() {
        $('.tab-pane.active .datatables-products').DataTable().button('.buttons-excel').trigger();
    });
    $('#btn-pdf').on('click', function() {
        $('.tab-pane.active .datatables-products').DataTable().button('.buttons-pdf').trigger();
    });
    $('#btn-print').on('click', function() {
        $('.tab-pane.active .datatables-products').DataTable().button('.buttons-print').trigger();
    });
});
</script>
@endsection

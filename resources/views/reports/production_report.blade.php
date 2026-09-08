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
                <button id="btn-excel" class="btn btn-outline-primary btn-sm rounded-pill"><i
                        class="ri ri-file-excel-line me-1"></i> Excel</button>
                <button id="btn-pdf" class="btn btn-outline-danger btn-sm rounded-pill"><i
                        class="ri ri-file-pdf-line me-1"></i> PDF</button>
                <button id="btn-print" class="btn btn-primary btn-sm rounded-pill px-3"><i
                        class="ri ri-printer-line me-1"></i> Print</button>
            </div>
        </div>

        <!-- Global Filter Card -->
        <div class="card shadow-sm border-0 mb-4 premium-filter-card">
            <div class="card-body py-4">
                <form id="productionReportForm" class="row g-3 align-items-end" onsubmit="return false;">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-primary"><i class="ri-file-chart-line me-1"></i>Select
                            Report Type</label>
                        <select class="form-select select2" id="report_type_select" name="report_type">
                            <option value="production-wip" selected>🏭 Production WIP Unit Wise</option>
                            <option value="department-efficiency">📊 Department Wise Efficiency Report</option>
                            <option value="performance-report">👤 Performance Individual</option>
                            <option value="process-wise">⚙️ Production Report Section Wise</option>
                            <option value="completion-report">📅 Job Card Completed Date</option>
                            <option value="brand-production">🏷️ Brand Wise Unit Production</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">From Date</label>
                        <input type="text" class="form-control start_date" name="from_date"
                            value="{{ request('from_date') }}" placeholder="DD-MM-YYYY">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">To Date</label>
                        <input type="text" class="form-control end_date" name="to_date" value="{{ request('to_date') }}"
                            placeholder="DD-MM-YYYY">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Unit</label>
                        <select class="form-select select2" name="unit_id" id="unit_id_filter"
                            data-placeholder="Select Unit">
                            <option value=""></option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill p-2" title="Search">
                            <i class="ri ri-search-line me-1"></i> Search
                        </button>
                        <button type="button" id="btn-reset-report" class="btn btn-outline-light rounded-pill border p-2"
                            title="Reset">
                            <i class="ri ri-refresh-line"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Content Card -->
        <div class="card shadow-sm border-0 premium-content-card">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold text-primary" id="active_report_title">
                    🏭 Production WIP Unit Wise
                </h5>
            </div>
            <div class="card-body py-4">
                <div class="tab-content" id="reportTabsContent">
                    <!-- 1. Production WIP Unit Wise -->
                    <div class="tab-pane fade show active" id="production-wip" role="tabpanel">
                        <div class="card-datatable table-responsive">
                            <table class="datatables-products table table-hover" id="productionWipTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Jobcard No</th>
                                        <th>Stage</th>
                                        <th class="text-center">Opening</th>
                                        <th class="text-center">Inward</th>
                                        <th class="text-center">Outward</th>
                                        <th class="text-center">Current WIP</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 2. Performance Individual Report -->
                    <div class="tab-pane fade" id="performance-report" role="tabpanel">
                        <div class="card-datatable table-responsive">
                            <table class="datatables-products table table-hover" id="performanceReportTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Job Card No</th>
                                        <th>Service Name</th>
                                        <th>Employee</th>
                                        <th>Stage</th>
                                        <th class="text-center">Assigned Qty</th>
                                        <th class="text-center">Completed Qty</th>
                                        <th class="text-center">Pending Qty</th>
                                        <th class="text-center">Efficiency</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 3. Production Report Section Wise -->
                    <div class="tab-pane fade" id="process-wise" role="tabpanel">
                        <div class="card-datatable table-responsive">
                            <table class="datatables-products table table-hover" id="processWiseTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Job Card No</th>
                                        <th>Service Name</th>
                                        <th>Process Name</th>
                                        <th class="text-center text-primary">Task Plan</th>
                                        <th class="text-center text-warning">Inprocess</th>
                                        <th class="text-center text-success">Completed</th>
                                        <th class="text-center text-danger">Hold</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 4. Job Card Completed Date -->
                    <div class="tab-pane fade" id="completion-report" role="tabpanel">
                        <div class="card-datatable table-responsive">
                            <table class="datatables-products table table-hover" id="completionReportTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Jobcard No</th>
                                        <th>Unit</th>
                                        <th class="text-center">Quantity</th>
                                        <th>Target Date</th>
                                        <th>Completed Date</th>
                                        <th class="text-center">Days Taken</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 5. Brand Wise Unit Production -->
                    <div class="tab-pane fade" id="brand-production" role="tabpanel">
                        <div class="card-datatable table-responsive">
                            <table class="datatables-products table table-hover" id="brandProductionTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Brand</th>
                                        <th>Style Name</th>
                                        <th>Sleeve Type</th>
                                        <th class="text-center">Produced Qty</th>
                                        <th>Unit</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 6. Department Wise Efficiency Report -->
                    <div class="tab-pane fade" id="department-efficiency" role="tabpanel">
                        <!-- MAIN VIEW: Summary Widget & Department Table -->
                        <div id="deptEfficiencyMainView">
                            <!-- Department Efficiency Summary Widget (Top of datatable as per Task 1) -->
                            <div class="dept-efficiency-summary-card mb-4 p-4 rounded-3 border bg-white shadow-sm">
                                <div class="row align-items-center">
                                    <div class="col-md-5 mb-3 mb-md-0">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <h5 class="mb-0 fw-bold text-dark">Department Efficiency</h5>
                                            <div class="text-warning fs-5">
                                                <i class="ri-star-fill"></i>
                                                <i class="ri-star-fill"></i>
                                                <i class="ri-star-fill"></i>
                                                <i class="ri-star-fill"></i>
                                                <i class="ri-star-fill"></i>
                                            </div>
                                        </div>
                                        <p class="text-muted small mb-0">Daily morning meeting performance score calculated
                                            from active department operations based on planned vs completed actual output.
                                        </p>
                                    </div>
                                    <div class="col-md-7">
                                        <div
                                            class="p-3 bg-light rounded-3 border d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="dept-eff-badge-display px-3 py-2 rounded-3 fw-bold fs-3 text-primary bg-white shadow-sm border"
                                                    id="deptOverallEffVal">
                                                    0%
                                                </div>
                                                <div class="flex-grow-1" style="min-width: 140px;">
                                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                                        <span class="fw-semibold">Overall Status</span>
                                                        <span id="deptEffProgressLabel">0%</span>
                                                    </div>
                                                    <div class="progress" style="height: 10px; border-radius: 6px;">
                                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                                            id="deptEffProgressBar" role="progressbar" style="width: 0%">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2 text-center text-nowrap">
                                                <div class="px-2 py-1 bg-white rounded border">
                                                    <span class="d-block extra-small text-muted text-uppercase fw-bold"
                                                        style="font-size: 0.68rem;">Target</span>
                                                    <span class="fw-bold text-dark small" id="deptSummaryTarget">-</span>
                                                </div>
                                                <div class="px-2 py-1 bg-white rounded border">
                                                    <span class="d-block extra-small text-muted text-uppercase fw-bold"
                                                        style="font-size: 0.68rem;">Plan</span>
                                                    <span class="fw-bold text-primary small" id="deptSummaryPlan">0
                                                        Pcs</span>
                                                </div>
                                                <div class="px-2 py-1 bg-white rounded border">
                                                    <span class="d-block extra-small text-muted text-uppercase fw-bold"
                                                        style="font-size: 0.68rem;">Actual</span>
                                                    <span class="fw-bold text-success small" id="deptSummaryActual">0
                                                        Pcs</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Department Efficiency DataTable -->
                            <div class="card-datatable table-responsive">
                                <table class="datatables-products table table-hover" id="departmentEfficiencyTable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Operation / Department</th>
                                            <th class="text-center">Target</th>
                                            <th class="text-center">Plan</th>
                                            <th class="text-center">Actual</th>
                                            <th class="text-center">Efficiency</th>
                                            <th class="text-center">Working Hours</th>
                                            <th class="text-center no-export">Delay Breakdown & Tasks</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- DETAIL VIEW: In-Page Department Breakdown (Replaces Modal as per Task 3) -->
                        <div id="deptEfficiencyDetailView" class="d-none">
                            <!-- Top Action Bar with Back Button -->
                            <div
                                class="d-flex flex-wrap align-items-center justify-content-between p-3 mb-4 bg-light rounded-3 border gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <button type="button"
                                        class="btn btn-primary btn-sm rounded-pill px-3 btn-back-to-dept-report">
                                        <i class="ri-arrow-left-line me-1"></i> Back to Report
                                    </button>
                                    <div>
                                        <h5 class="mb-0 fw-bold text-primary d-flex align-items-center"
                                            id="detailStageName">
                                            Department Breakdown
                                        </h5>
                                        <small class="text-muted" id="detailStageSubheading">Job Cards, Tasks & Delay
                                            Reasons</small>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="badge bg-label-secondary rounded-pill px-3 py-2"
                                        id="detailFilterDateRange">All Dates</span>
                                    <span class="badge bg-label-secondary rounded-pill px-3 py-2" id="detailFilterUnit">All
                                        Units</span>
                                </div>
                            </div>

                            <!-- Summary KPI Chips for Selected Department -->
                            <div class="row g-3 mb-4" id="detailSummaryCards">
                                <div class="col-6 col-md-3">
                                    <div
                                        class="card border border-light-subtle shadow-none bg-light p-3 text-center rounded-3">
                                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Total
                                            Tasks</span>
                                        <h4 class="mb-0 fw-bold text-dark" id="detailTotalTasks">0</h4>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div
                                        class="card border border-light-subtle shadow-none bg-light p-3 text-center rounded-3">
                                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Plan
                                            Qty</span>
                                        <h4 class="mb-0 fw-bold text-primary" id="detailTotalPlan">0</h4>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div
                                        class="card border border-light-subtle shadow-none bg-light p-3 text-center rounded-3">
                                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Actual
                                            Qty</span>
                                        <h4 class="mb-0 fw-bold text-success" id="detailTotalActual">0</h4>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div
                                        class="card border border-light-subtle shadow-none bg-light p-3 text-center rounded-3">
                                        <span
                                            class="text-muted small fw-semibold text-uppercase d-block mb-1">Efficiency</span>
                                        <h4 class="mb-0 fw-bold text-info" id="detailStageEfficiency">0%</h4>
                                    </div>
                                </div>
                            </div>

                            <!-- Table of Tasks & Delay Reasons -->
                            <div class="card border shadow-sm rounded-3 overflow-hidden mb-4">
                                <div
                                    class="card-header bg-white border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                                    <h6 class="mb-0 fw-bold text-dark"><i class="ri-file-list-3-line me-1 text-primary"></i>
                                        Linked Job Cards & Tasks</h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" id="btn-detail-excel" class="btn btn-outline-primary btn-sm rounded-pill"><i
                                                class="ri ri-file-excel-line me-1"></i> Excel</button>
                                        <button type="button" id="btn-detail-pdf" class="btn btn-outline-danger btn-sm rounded-pill"><i
                                                class="ri ri-file-pdf-line me-1"></i> PDF</button>
                                        <button type="button" id="btn-detail-print" class="btn btn-primary btn-sm rounded-pill px-3"><i
                                                class="ri ri-printer-line me-1"></i> Print</button>
                                        <button type="button"
                                            class="btn btn-outline-secondary btn-sm rounded-pill btn-back-to-dept-report ms-1">
                                            <i class="ri-arrow-left-line me-1"></i> Back to Report
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body py-4">
                                    <div class="card-datatable table-responsive">
                                        <table class="table table-hover align-middle mb-0" id="detailTasksTable" style="width: 100%;">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Job Card No</th>
                                                    <th>Task No</th>
                                                    <th>Unit</th>
                                                    <th>Issue Date</th>
                                                    <th>Due Date</th>
                                                    <th class="text-center">Plan</th>
                                                    <th class="text-center">Actual</th>
                                                    <th class="text-center">Efficiency</th>
                                                    <th class="text-center">Status</th>
                                                    <th>Delay Reason</th>
                                                </tr>
                                            </thead>
                                            <tbody id="detailTasksTableBody">
                                                <!-- Populated dynamically via AJAX DataTable -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom Back to Report Button -->
                            <div class="d-flex justify-content-start mb-3">
                                <button type="button"
                                    class="btn btn-outline-primary rounded-pill px-4 btn-back-to-dept-report">
                                    <i class="ri-arrow-left-line me-1"></i> Back to Department Efficiency Report
                                </button>
                            </div>
                        </div>
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

        .table thead th {
            border-top: none;
            border-bottom: 2px solid #e2e8f0;
            text-transform: uppercase;
            font-size: 0.72rem;
            letter-spacing: 0.05em;
            font-weight: 700;
            color: #475569;
            padding: 1rem 0.75rem;
        }

        .table tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            font-size: 0.85rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .badge.bg-label-primary {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge.bg-label-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge.bg-label-info {
            background: #e0f2fe;
            color: #0369a1;
        }

        .badge.bg-label-warning {
            background: #fef9c3;
            color: #854d0e;
        }

        .badge.bg-label-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .dept-efficiency-summary-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #e2e8f0 !important;
            border-radius: 14px;
            transition: all 0.2s ease-in-out;
        }

        .dept-efficiency-summary-card:hover {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
        }

        .dept-eff-badge-display {
            min-width: 90px;
            text-align: center;
            letter-spacing: -0.5px;
        }

        .view-dept-tasks {
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .view-dept-tasks:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            const tableConfigs = {
                'production-wip': {
                    tableId: '#productionWipTable',
                    type: 'production-wip',
                    columns: [
                        { data: 'job_card_no', name: 'job_card_no' },
                        { data: 'process', name: 'process' },
                        { data: 'opening', name: 'opening', className: 'text-center' },
                        { data: 'inward', name: 'inward', className: 'text-center' },
                        { data: 'outward', name: 'outward', className: 'text-center' },
                        { data: 'current_wip', name: 'current_wip', className: 'text-center fw-bold' }
                    ]
                },
                'performance-report': {
                    tableId: '#performanceReportTable',
                    type: 'performance-report',
                    columns: [
                        { data: 'job_card_no', name: 'job_card_no' },
                        { data: 'service', name: 'service' },
                        { data: 'employee', name: 'employee' },
                        { data: 'stage', name: 'stage' },
                        { data: 'assigned_qty', name: 'assigned_qty', className: 'text-center' },
                        { data: 'completed_qty', name: 'completed_qty', className: 'text-center' },
                        { data: 'pending_qty', name: 'pending_qty', className: 'text-center' },
                        { data: 'efficiency', name: 'efficiency', className: 'text-center' }
                    ]
                },
                'process-wise': {
                    tableId: '#processWiseTable',
                    type: 'process-wise',
                    columns: [
                        { data: 'job_card_no', name: 'job_card_no' },
                        { data: 'service_name', name: 'service_name' },
                        { data: 'process_name', name: 'process_name' },
                        { data: 'task_plan', name: 'task_plan', className: 'text-center' },
                        { data: 'inprocess', name: 'inprocess', className: 'text-center' },
                        { data: 'completed', name: 'completed', className: 'text-center' },
                        { data: 'hold', name: 'hold', className: 'text-center' }
                    ]
                },
                'completion-report': {
                    tableId: '#completionReportTable',
                    type: 'completion-report',
                    columns: [
                        { data: 'job_card_no', name: 'job_card_no' },
                        { data: 'unit', name: 'unit' },
                        { data: 'quantity', name: 'quantity', className: 'text-center' },
                        { data: 'target_date', name: 'target_date' },
                        { data: 'completed_date', name: 'completed_date' },
                        { data: 'days_taken', name: 'days_taken', className: 'text-center' }
                    ]
                },
                'brand-production': {
                    tableId: '#brandProductionTable',
                    type: 'brand-production',
                    columns: [
                        { data: 'brand', name: 'brand' },
                        { data: 'style', name: 'style' },
                        { data: 'sleeve', name: 'sleeve' },
                        { data: 'qty', name: 'qty', className: 'text-center fw-bold' },
                        { data: 'unit', name: 'unit' }
                    ]
                },
                'department-efficiency': {
                    tableId: '#departmentEfficiencyTable',
                    type: 'department-efficiency',
                    columns: [
                        { data: 'operation', name: 'operation' },
                        { data: 'target', name: 'target', className: 'text-center' },
                        { data: 'plan', name: 'plan', className: 'text-center' },
                        { data: 'actual', name: 'actual', className: 'text-center' },
                        { data: 'efficiency', name: 'efficiency', className: 'text-center' },
                        { data: 'working_hours', name: 'working_hours', className: 'text-center' },
                        { data: 'delay_details', name: 'delay_details', className: 'text-center no-export', orderable: false, searchable: false }
                    ]
                }
            };

            $.fn.dataTable.ext.errMode = 'none';

            let activeDetailStageId = null;
            let activeDetailStageName = '';

            function showReportLoading(isLoading) {
                let loader = $('#report_loader');
                if (isLoading) {
                    if (!loader.length) {
                        $('#active_report_title').append(' <div class="spinner-border spinner-border-sm text-primary ms-2" id="report_loader" role="status"></div>');
                    }
                    $('#reportTabsContent').css('opacity', '0.6');
                } else {
                    $('#report_loader').remove();
                    $('#reportTabsContent').css('opacity', '1');
                }
            }

            function loadActiveTabTable(tabPaneId) {
                const config = tableConfigs[tabPaneId];
                if (!config) return;

                const tableElem = $(config.tableId);
                if (!tableElem.length) return;

                showReportLoading(true);

                if ($.fn.DataTable.isDataTable(config.tableId)) {
                    const dt = tableElem.DataTable();
                    if (dt && dt.ajax && typeof dt.ajax.url === 'function' && dt.ajax.url()) {
                        try {
                            dt.ajax.reload(function () { showReportLoading(false); }, false);
                            return;
                        } catch (err) {
                            dt.destroy();
                        }
                    } else {
                        dt.destroy();
                    }
                }

                tableElem.DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    destroy: true,
                    language: {
                        processing: '<div class="d-flex align-items-center justify-content-center py-4 text-primary fw-bold"><div class="spinner-border spinner-border-sm me-2" role="status"></div> Loading report data...</div>',
                        emptyTable: '<div class="text-center py-4 text-muted"><i class="ri-inbox-line ri-2x mb-2 d-block text-secondary"></i>No records found</div>'
                    },
                    ajax: {
                        url: "{{ url('production_reports/ajax') }}/" + config.type,
                        type: "GET",
                        data: function (d) {
                            d.from_date = $('.start_date').val();
                            d.to_date = $('.end_date').val();
                            d.unit_id = $('select[name="unit_id"]').val();
                        }
                    },
                    drawCallback: function (settings) {
                        showReportLoading(false);
                        if (config.type === 'department-efficiency') {
                            const json = settings.json;
                            if (json && json.meta) {
                                $('#deptOverallEffVal').text(json.meta.overall_efficiency || '0%');
                                $('#deptEffProgressLabel').text(json.meta.overall_efficiency || '0%');
                                const effNum = Math.min(100, Math.max(0, parseFloat(json.meta.efficiency_val || 0)));
                                $('#deptEffProgressBar').css('width', effNum + '%');
                                if (effNum >= 95) {
                                    $('#deptEffProgressBar').removeClass('bg-warning bg-danger').addClass('bg-success');
                                } else if (effNum >= 75) {
                                    $('#deptEffProgressBar').removeClass('bg-success bg-danger').addClass('bg-warning');
                                } else {
                                    $('#deptEffProgressBar').removeClass('bg-success bg-warning').addClass('bg-danger');
                                }
                                $('#deptSummaryTarget').text(json.meta.total_target || '-');
                                $('#deptSummaryPlan').text(json.meta.total_plan || '0 Pcs');
                                $('#deptSummaryActual').text(json.meta.total_actual || '0 Pcs');
                            }
                        }
                    },
                    columns: config.columns,
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    buttons: [
                        {
                            extend: 'excel',
                            className: 'buttons-excel d-none',
                            title: function () {
                                return $('#active_report_title').text().trim() || 'Production Report';
                            },
                            exportOptions: {
                                columns: ':not(.no-export)'
                            }
                        },
                        {
                            extend: 'pdf',
                            className: 'buttons-pdf d-none',
                            title: function () {
                                return $('#active_report_title').text().trim() || 'Production Report';
                            },
                            exportOptions: {
                                columns: ':not(.no-export)'
                            }
                        },
                        {
                            extend: 'print',
                            className: 'buttons-print d-none',
                            title: function () {
                                return $('#active_report_title').text().trim() || 'Production Report';
                            },
                            exportOptions: {
                                columns: ':not(.no-export)'
                            }
                        }
                    ],
                    lengthMenu: [10, 25, 50, 100],
                    pageLength: 10
                });
            }

            // Universal Server-side Export Action to export all records
            function serverSideExportAction(e, dt, button, config) {
                var self = this;
                var oldStart = dt.settings()[0]._iDisplayStart;
                
                showReportLoading(true);

                dt.one('preXhr', function (e, s, data) {
                    // Load ALL data for export
                    data.start = 0;
                    data.length = -1;
                    data.export = 1;

                    dt.one('preDraw', function (e, settings) {
                        var btnType = config.extend || '';
                        if (btnType === 'excel' || button.hasClass('buttons-excel') || button.hasClass('detail-buttons-excel')) {
                            if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
                                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
                            } else if ($.fn.dataTable.ext.buttons.excelFlash && $.fn.dataTable.ext.buttons.excelFlash.available(dt, config)) {
                                $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
                            }
                        } else if (btnType === 'pdf' || button.hasClass('buttons-pdf') || button.hasClass('detail-buttons-pdf')) {
                            if ($.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config)) {
                                $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config);
                            } else if ($.fn.dataTable.ext.buttons.pdfFlash && $.fn.dataTable.ext.buttons.pdfFlash.available(dt, config)) {
                                $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
                            }
                        } else if (btnType === 'print' || button.hasClass('buttons-print') || button.hasClass('detail-buttons-print')) {
                            $.fn.dataTable.ext.buttons.print.action.call(self, e, dt, button, config);
                        }

                        // Restore original start and clear export flag
                        dt.one('preXhr', function (e, s, data) {
                            settings._iDisplayStart = oldStart;
                            data.start = oldStart;
                            delete data.export;
                        });

                        // Reload the current page in background
                        setTimeout(function () {
                            dt.ajax.reload(function () {
                                showReportLoading(false);
                            }, false);
                        }, 0);

                        return false; // Prevent rendering all rows into the HTML DOM
                    });
                });

                dt.ajax.reload();
            }

            let detailTasksDataTable = null;

            function initDetailTasksDataTable(stageId) {
                if ($.fn.DataTable.isDataTable('#detailTasksTable')) {
                    var dt = $('#detailTasksTable').DataTable();
                    dt.ajax.reload(null, true);
                    return;
                }

                detailTasksDataTable = $('#detailTasksTable').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    destroy: true,
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    language: {
                        processing: '<div class="d-flex align-items-center justify-content-center py-4 text-primary fw-bold"><div class="spinner-border spinner-border-sm me-2" role="status"></div> Loading task & delay details...</div>',
                        emptyTable: '<div class="text-center py-4 text-muted"><i class="ri-inbox-line ri-2x mb-2 d-block text-secondary"></i>No tasks found for this department with the selected filters.</div>',
                        zeroRecords: '<div class="text-center py-3 text-muted">No matching records found</div>'
                    },
                    ajax: {
                        url: "{{ url('production_reports/ajax/department-tasks') }}",
                        type: "GET",
                        data: function (d) {
                            d.stage_id = activeDetailStageId;
                            d.from_date = $('.start_date').val();
                            d.to_date = $('.end_date').val();
                            d.unit_id = $('select[name="unit_id"]').val();
                        },
                        dataSrc: function (json) {
                            if (json && json.summary) {
                                $('#detailTotalTasks').text(json.summary.total_tasks != null ? json.summary.total_tasks : 0);
                                $('#detailTotalPlan').text(json.summary.total_plan || '0 Pcs');
                                $('#detailTotalActual').text(json.summary.total_actual || '0 Pcs');
                                $('#detailStageEfficiency').text(json.summary.efficiency || '0%');
                            }
                            return json.data || [];
                        }
                    },
                    columns: [
                        {
                            data: 'job_card_no',
                            name: 'job_card_no',
                            render: function (data, type, row) {
                                if (type === 'display') {
                                    return '<strong class="text-dark">' + (data || 'N/A') + '</strong>';
                                }
                                return data || 'N/A';
                            }
                        },
                        {
                            data: 'task_no',
                            name: 'task_no',
                            render: function (data, type, row) {
                                if (type === 'display') {
                                    return '<span class="badge bg-label-secondary font-monospace">' + (data || 'N/A') + '</span>';
                                }
                                return data || 'N/A';
                            }
                        },
                        {
                            data: 'unit',
                            name: 'unit',
                            render: function (data, type, row) {
                                if (type === 'display') {
                                    return '<small class="text-muted">' + (data || 'N/A') + '</small>';
                                }
                                return data || 'N/A';
                            }
                        },
                        {
                            data: 'issue_date',
                            name: 'issue_date',
                            render: function (data, type, row) {
                                if (type === 'display') {
                                    return '<small>' + (data || '-') + '</small>';
                                }
                                return data || '-';
                            }
                        },
                        {
                            data: 'due_date',
                            name: 'due_date',
                            render: function (data, type, row) {
                                if (type === 'display') {
                                    var cls = (row.delay_badge === 'danger') ? 'text-danger fw-bold' : '';
                                    return '<small class="' + cls + '">' + (data || '-') + '</small>';
                                }
                                return data || '-';
                            }
                        },
                        {
                            data: 'plan',
                            name: 'plan',
                            className: 'text-center',
                            render: function (data, type, row) {
                                if (type === 'display') {
                                    return '<span class="text-primary fw-semibold">' + (data || '0 Pcs') + '</span>';
                                }
                                return data || '0 Pcs';
                            }
                        },
                        {
                            data: 'actual',
                            name: 'actual',
                            className: 'text-center',
                            render: function (data, type, row) {
                                if (type === 'display') {
                                    return '<span class="text-success fw-semibold">' + (data || '0 Pcs') + '</span>';
                                }
                                return data || '0 Pcs';
                            }
                        },
                        {
                            data: 'efficiency',
                            name: 'efficiency',
                            className: 'text-center',
                            render: function (data, type, row) {
                                if (type === 'display') {
                                    return '<span class="badge bg-label-info rounded-pill">' + (data || '0%') + '</span>';
                                }
                                return data || '0%';
                            }
                        },
                        {
                            data: 'status',
                            name: 'status',
                            className: 'text-center',
                            render: function (data, type, row) {
                                if (type === 'display') {
                                    var badge = row.status_badge || 'secondary';
                                    return '<span class="badge bg-label-' + badge + ' rounded-pill">' + (data || 'Planned') + '</span>';
                                }
                                return data || 'Planned';
                            }
                        },
                        {
                            data: 'delay_reason',
                            name: 'delay_reason',
                            render: function (data, type, row) {
                                if (type === 'display') {
                                    var badge = row.delay_badge || 'secondary';
                                    return '<span class="badge bg-label-' + badge + ' rounded-pill">' + (data || '-') + '</span>';
                                }
                                return data || '-';
                            }
                        }
                    ],
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    buttons: [
                        {
                            extend: 'excel',
                            className: 'detail-buttons-excel d-none',
                            title: function () {
                                return (activeDetailStageName ? activeDetailStageName + ' - Task & Delay Details' : 'Department Tasks Report');
                            },
                            exportOptions: {
                                columns: ':visible'
                            },
                            action: serverSideExportAction
                        },
                        {
                            extend: 'pdf',
                            className: 'detail-buttons-pdf d-none',
                            title: function () {
                                return (activeDetailStageName ? activeDetailStageName + ' - Task & Delay Details' : 'Department Tasks Report');
                            },
                            orientation: 'landscape',
                            pageSize: 'A4',
                            exportOptions: {
                                columns: ':visible'
                            },
                            action: serverSideExportAction
                        },
                        {
                            extend: 'print',
                            className: 'detail-buttons-print d-none',
                            title: function () {
                                return (activeDetailStageName ? activeDetailStageName + ' - Task & Delay Details' : 'Department Tasks Report');
                            },
                            exportOptions: {
                                columns: ':visible'
                            },
                            action: serverSideExportAction
                        }
                    ]
                });
            }

            // In-Page Department Detail View Switcher
            function openDeptDetailView(stageId, stageName, pushHistory) {
                activeDetailStageId = stageId;
                activeDetailStageName = stageName || 'Department';

                // Update Heading & Breadcrumb
                $('#detailStageName').html('<i class="ri-building-line me-2"></i> ' + activeDetailStageName + ' Breakdown');
                $('#detailStageSubheading').text('Tasks, Job Cards & Delay Reasons for ' + activeDetailStageName);
                $('#active_report_title').html('<span class="text-muted">Department Efficiency &gt;</span> ' + activeDetailStageName);

                // Filter Labels
                const fromDateVal = $('.start_date').val() || '';
                const toDateVal = $('.end_date').val() || '';
                const unitText = $('select[name="unit_id"] option:selected').text();
                $('#detailFilterDateRange').text(fromDateVal || toDateVal ? 'Dates: ' + (fromDateVal || 'Start') + ' to ' + (toDateVal || 'Now') : 'All Dates');
                $('#detailFilterUnit').text(unitText && unitText.trim() ? 'Unit: ' + unitText.trim() : 'All Units');

                // Reset summary figures while loading
                $('#detailTotalTasks').text('-');
                $('#detailTotalPlan').text('-');
                $('#detailTotalActual').text('-');
                $('#detailStageEfficiency').text('-');

                // Switch views in-page
                $('#deptEfficiencyMainView').addClass('d-none');
                $('#deptEfficiencyDetailView').removeClass('d-none');

                // Push browser history state for client back button
                if (pushHistory !== false) {
                    history.pushState({ view: 'dept-detail', stageId: stageId, stageName: activeDetailStageName }, '', '#stage-' + stageId);
                }

                // Smooth scroll to top of table card
                $('html, body').animate({ scrollTop: $('#deptEfficiencyDetailView').offset().top - 80 }, 200);

                // Initialize/load DataTables with AJAX pagination and export
                initDetailTasksDataTable(stageId);
            }

            // Return to Normal Department Report View
            function returnToDeptMainView() {
                activeDetailStageId = null;
                activeDetailStageName = '';

                $('#deptEfficiencyDetailView').addClass('d-none');
                $('#deptEfficiencyMainView').removeClass('d-none');

                const origTitle = $('#report_type_select option:selected').text() || 'Department Wise Efficiency Report';
                $('#active_report_title').html(origTitle);

                // Clean up hash if needed
                if (window.location.hash && window.location.hash.indexOf('#stage-') !== -1) {
                    history.replaceState(null, '', window.location.pathname + window.location.search);
                }

                if ($.fn.DataTable.isDataTable('#departmentEfficiencyTable')) {
                    $('#departmentEfficiencyTable').DataTable().columns.adjust();
                }
            }

            // Department Tasks & Delay Reasons Click Handler (Open in Same Page)
            $(document).on('click', '.view-dept-tasks', function (e) {
                e.preventDefault();
                const stageId = $(this).data('stage-id');
                const stageName = $(this).data('stage-name') || 'Department';
                openDeptDetailView(stageId, stageName, true);
            });

            // Back to Report Buttons Handler
            $(document).on('click', '.btn-back-to-dept-report', function (e) {
                e.preventDefault();
                if (window.location.hash && window.location.hash.indexOf('#stage-') !== -1) {
                    history.back();
                } else {
                    returnToDeptMainView();
                }
            });

            // Browser Client Back / Forward Button Popstate Handler
            window.addEventListener('popstate', function (e) {
                if (e.state && e.state.view === 'dept-detail' && e.state.stageId) {
                    openDeptDetailView(e.state.stageId, e.state.stageName, false);
                } else {
                    if (!$('#deptEfficiencyDetailView').hasClass('d-none')) {
                        returnToDeptMainView();
                    }
                }
            });

            // Select Report Type Change Listener
            $('#report_type_select').on('change', function () {
                const selectedType = $(this).val();
                const selectedText = $(this).find('option:selected').text();
                $('#active_report_title').html(selectedText);

                // Reset detail view if switching tabs
                returnToDeptMainView();

                $('.tab-pane').removeClass('show active');
                $('#' + selectedType).addClass('show active');

                loadActiveTabTable(selectedType);
            });

            // Initialize Active Report on Page Load
            const initialReportType = $('#report_type_select').val() || 'production-wip';
            const initialText = $('#report_type_select option:selected').text();
            if (initialText) {
                $('#active_report_title').html(initialText);
            }
            $('.tab-pane').removeClass('show active');
            $('#' + initialReportType).addClass('show active');
            loadActiveTabTable(initialReportType);

            // Form Filter Submit listener
            $('#productionReportForm').on('submit', function (e) {
                e.preventDefault();
                const activeTabId = $('#report_type_select').val() || 'production-wip';

                // If currently viewing department detail, refresh detail view with new filters
                if (activeTabId === 'department-efficiency' && activeDetailStageId && !$('#deptEfficiencyDetailView').hasClass('d-none')) {
                    if (detailTasksDataTable) {
                        detailTasksDataTable.ajax.reload(null, false);
                    } else {
                        initDetailTasksDataTable(activeDetailStageId);
                    }
                    return;
                }

                const config = tableConfigs[activeTabId];
                if (config && $.fn.DataTable.isDataTable(config.tableId)) {
                    const dt = $(config.tableId).DataTable();
                    if (dt && dt.ajax && typeof dt.ajax.url === 'function' && dt.ajax.url()) {
                        try {
                            showReportLoading(true);
                            dt.ajax.reload(function () { showReportLoading(false); });
                            return;
                        } catch (err) {
                            dt.destroy();
                        }
                    }
                }
                loadActiveTabTable(activeTabId);
            });

            // Reset Button Handler
            $(document).on('click', '#btn-reset-report', function (e) {
                e.preventDefault();
                $('.start_date').val('');
                $('.end_date').val('');
                $('select[name="unit_id"]').val('').trigger('change');
                returnToDeptMainView();
                $('#productionReportForm').trigger('submit');
            });

            // Detail Card Export Buttons Handlers
            $(document).on('click', '#btn-detail-excel', function (e) {
                e.preventDefault();
                if (detailTasksDataTable) {
                    detailTasksDataTable.button('.detail-buttons-excel').trigger();
                }
            });
            $(document).on('click', '#btn-detail-pdf', function (e) {
                e.preventDefault();
                if (detailTasksDataTable) {
                    detailTasksDataTable.button('.detail-buttons-pdf').trigger();
                }
            });
            $(document).on('click', '#btn-detail-print', function (e) {
                e.preventDefault();
                if (detailTasksDataTable) {
                    detailTasksDataTable.button('.detail-buttons-print').trigger();
                }
            });

            // Top Header Export Handlers
            $('#btn-excel').on('click', function () {
                if ($('#report_type_select').val() === 'department-efficiency' && !$('#deptEfficiencyDetailView').hasClass('d-none')) {
                    if (detailTasksDataTable) {
                        detailTasksDataTable.button('.detail-buttons-excel').trigger();
                        return;
                    }
                }
                $('.tab-pane.active .datatables-products').DataTable().button('.buttons-excel').trigger();
            });
            $('#btn-pdf').on('click', function () {
                if ($('#report_type_select').val() === 'department-efficiency' && !$('#deptEfficiencyDetailView').hasClass('d-none')) {
                    if (detailTasksDataTable) {
                        detailTasksDataTable.button('.detail-buttons-pdf').trigger();
                        return;
                    }
                }
                $('.tab-pane.active .datatables-products').DataTable().button('.buttons-pdf').trigger();
            });
            $('#btn-print').on('click', function () {
                if ($('#report_type_select').val() === 'department-efficiency' && !$('#deptEfficiencyDetailView').hasClass('d-none')) {
                    if (detailTasksDataTable) {
                        detailTasksDataTable.button('.detail-buttons-print').trigger();
                        return;
                    }
                }
                $('.tab-pane.active .datatables-products').DataTable().button('.buttons-print').trigger();
            });
        });
    </script>
@endsection
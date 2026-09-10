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
                            <option value="casino-cutting-wip">✂️ Casino Cutting WIP Report</option>
                            <option value="department-efficiency">📊 Department Wise Efficiency Report</option>
                            <option value="employee-efficiency">👥 Employee Wise Efficiency Report</option>
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

                    <!-- Casino Cutting WIP Report -->
                    <div class="tab-pane fade" id="casino-cutting-wip" role="tabpanel">
                        <div class="card-datatable table-responsive">
                            <table class="datatables-products table table-hover text-nowrap align-middle" id="casinoCuttingWipTable" style="width: 100%;">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Job Card</th>
                                        <th>Issue Date</th>
                                        <th>Delivery Date</th>
                                        <th class="text-center">Age (Days)</th>
                                        <th>Brand</th>
                                        <th>Season</th>
                                        <th>Pattern</th>
                                        <th class="text-center">Fabric</th>
                                        <th class="text-center">Issue Mtrs</th>
                                        <th class="text-center">Est. Qty</th>
                                        <th class="text-center">Cut Qty</th>
                                        <th class="text-center">Bundled</th>
                                        <th class="text-center">Balance to Bundle</th>
                                        <th class="text-center">Full</th>
                                        <th class="text-center">Half</th>
                                        <th>Unit Assigned</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Priority</th>
                                        <th>Remarks</th>
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
                                        <button type="button"
                                            class="btn btn-outline-secondary btn-sm rounded-pill btn-back-to-dept-report">
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

                    <!-- 7. Employee Wise Efficiency Report -->
                    <div class="tab-pane fade" id="employee-efficiency" role="tabpanel">
                        <!-- MAIN VIEW: Summary Widget & Employee Table -->
                        <div id="employeeEfficiencyMainView">
                            <!-- Employee Efficiency Summary Widget -->
                            <div class="dept-efficiency-summary-card mb-4 p-4 rounded-3 border bg-white shadow-sm">
                                <div class="row align-items-center">
                                    <div class="col-md-5 mb-3 mb-md-0">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <h5 class="mb-0 fw-bold text-dark">Employee Efficiency</h5>
                                            <div class="text-warning fs-5">
                                                <i class="ri-user-star-fill"></i>
                                                <i class="ri-user-star-fill"></i>
                                                <i class="ri-user-star-fill"></i>
                                                <i class="ri-user-star-fill"></i>
                                                <i class="ri-user-star-fill"></i>
                                            </div>
                                        </div>
                                        <p class="text-muted small mb-0">Daily individual employee productivity & performance overview calculated from assigned tasks vs completed quantity.
                                        </p>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="p-3 bg-light rounded-3 border d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="dept-eff-badge-display px-3 py-2 rounded-3 fw-bold fs-3 text-primary bg-white shadow-sm border"
                                                    id="empOverallEffVal">
                                                    0%
                                                </div>
                                                <div class="flex-grow-1" style="min-width: 140px;">
                                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                                        <span class="fw-semibold">Overall Efficiency</span>
                                                        <span id="empEffProgressLabel">0%</span>
                                                    </div>
                                                    <div class="progress" style="height: 10px; border-radius: 6px;">
                                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                                            id="empEffProgressBar" role="progressbar" style="width: 0%">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2 text-center text-nowrap">
                                                <div class="px-2 py-1 bg-white rounded border">
                                                    <span class="d-block extra-small text-muted text-uppercase fw-bold"
                                                        style="font-size: 0.68rem;">Employees</span>
                                                    <span class="fw-bold text-dark small" id="empSummaryCount">0</span>
                                                </div>
                                                <div class="px-2 py-1 bg-white rounded border">
                                                    <span class="d-block extra-small text-muted text-uppercase fw-bold"
                                                        style="font-size: 0.68rem;">Hours</span>
                                                    <span class="fw-bold text-dark small" id="empSummaryHours">0 Hrs</span>
                                                </div>
                                                <div class="px-2 py-1 bg-white rounded border">
                                                    <span class="d-block extra-small text-muted text-uppercase fw-bold"
                                                        style="font-size: 0.68rem;">Target</span>
                                                    <span class="fw-bold text-primary small" id="empSummaryTarget">0 Pcs</span>
                                                </div>
                                                <div class="px-2 py-1 bg-white rounded border">
                                                    <span class="d-block extra-small text-muted text-uppercase fw-bold"
                                                        style="font-size: 0.68rem;">Completed</span>
                                                    <span class="fw-bold text-success small" id="empSummaryActual">0 Pcs</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Employee Efficiency DataTable -->
                            <div class="card-datatable table-responsive">
                                <table class="datatables-products table table-hover align-middle text-nowrap" id="employeeEfficiencyTable" style="width: 100%;">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Emp ID</th>
                                            <th>Employee Name</th>
                                            <th>Designation</th>
                                            <th>Task</th>
                                            <th class="text-center">Hours Working</th>
                                            <th class="text-center">Target Qty</th>
                                            <th class="text-center">Completed</th>
                                            <th class="text-center">Pending</th>
                                            <th class="text-center">Efficiency</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- DETAIL VIEW: In-Page Employee Task & Job Breakdown (Points 1 & 2) -->
                        <div id="employeeDetailView" class="d-none">
                            <!-- Top Action Bar with Back Button & View Switcher -->
                            <div class="d-flex flex-wrap align-items-center justify-content-between p-3 mb-4 bg-light rounded-3 border gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <button type="button"
                                        class="btn btn-primary btn-sm rounded-pill px-3 btn-back-to-emp-report">
                                        <i class="ri-arrow-left-line me-1"></i> Back to Report
                                    </button>
                                    <div>
                                        <h5 class="mb-0 fw-bold text-primary d-flex align-items-center"
                                            id="empDetailTitle">
                                            Employee Breakdown
                                        </h5>
                                        <small class="text-muted" id="empDetailSubtitle">Tasks, Job Cards & Performance</small>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <!-- View Mode Switcher Toggle: Task-Wise vs Job-Wise -->
                                    <div class="btn-group rounded-pill p-1 bg-white border shadow-sm" role="group">
                                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 active" id="btnShowEmpTasks">
                                            <i class="ri-task-line me-1"></i> Task-Wise Breakdown
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="btnShowEmpJobs">
                                            <i class="ri-file-list-3-line me-1"></i> Job-Wise Summary
                                        </button>
                                    </div>
                                    <span class="badge bg-label-secondary rounded-pill px-3 py-2"
                                        id="empDetailFilterDateRange">All Dates</span>
                                    <span class="badge bg-label-secondary rounded-pill px-3 py-2" id="empDetailFilterUnit">All
                                        Units</span>
                                </div>
                            </div>

                            <!-- Summary KPI Chips for Selected Employee -->
                            <div class="row g-3 mb-4" id="empDetailSummaryCards">
                                <div class="col-6 col-md-2">
                                    <div class="card border border-light-subtle shadow-none bg-light p-3 text-center rounded-3">
                                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1" id="empDetailCountLabel">Total Tasks</span>
                                        <h4 class="mb-0 fw-bold text-dark" id="empDetailTotalCount">0</h4>
                                    </div>
                                </div>
                                <div class="col-6 col-md-2">
                                    <div class="card border border-light-subtle shadow-none bg-light p-3 text-center rounded-3">
                                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Hours Worked</span>
                                        <h4 class="mb-0 fw-bold text-dark" id="empDetailTotalHours">0 Hrs</h4>
                                    </div>
                                </div>
                                <div class="col-6 col-md-2">
                                    <div class="card border border-light-subtle shadow-none bg-light p-3 text-center rounded-3">
                                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Target Qty</span>
                                        <h4 class="mb-0 fw-bold text-primary" id="empDetailTotalTarget">0 Pcs</h4>
                                    </div>
                                </div>
                                <div class="col-6 col-md-2">
                                    <div class="card border border-light-subtle shadow-none bg-light p-3 text-center rounded-3">
                                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Completed</span>
                                        <h4 class="mb-0 fw-bold text-success" id="empDetailTotalCompleted">0 Pcs</h4>
                                    </div>
                                </div>
                                <div class="col-6 col-md-2">
                                    <div class="card border border-light-subtle shadow-none bg-light p-3 text-center rounded-3">
                                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Pending</span>
                                        <h4 class="mb-0 fw-bold text-danger" id="empDetailTotalPending">0 Pcs</h4>
                                    </div>
                                </div>
                                <div class="col-6 col-md-2">
                                    <div class="card border border-light-subtle shadow-none bg-light p-3 text-center rounded-3">
                                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Efficiency</span>
                                        <h4 class="mb-0 fw-bold text-info" id="empDetailEfficiency">0%</h4>
                                    </div>
                                </div>
                            </div>

                            <!-- SUB-VIEW 1: Task Wise Breakdown (Point 1) -->
                            <div id="empTaskSubView">
                                <div class="card border shadow-sm rounded-3 overflow-hidden mb-4">
                                    <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                                        <h6 class="mb-0 fw-bold text-dark"><i class="ri-task-line me-1 text-primary"></i>
                                            Task-Wise Performance Breakdown</h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <button type="button"
                                                class="btn btn-outline-secondary btn-sm rounded-pill btn-back-to-emp-report">
                                                <i class="ri-arrow-left-line me-1"></i> Back to Report
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body py-4">
                                        <div class="card-datatable table-responsive">
                                            <table class="table table-hover align-middle mb-0 text-nowrap" id="employeeTasksTable" style="width: 100%;">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>Task No</th>
                                                        <th>Job Card No</th>
                                                        <th>Service / Process</th>
                                                        <th>Stage</th>
                                                        <th class="text-center">Hours Worked</th>
                                                        <th class="text-center">Target Qty</th>
                                                        <th class="text-center">Completed</th>
                                                        <th class="text-center">Pending</th>
                                                        <th class="text-center">Efficiency</th>
                                                        <th class="text-center">Status</th>
                                                        <th>Remarks</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SUB-VIEW 2: Job Card Wise Summary (Point 2) -->
                            <div id="empJobSubView" class="d-none">
                                <div class="card border shadow-sm rounded-3 overflow-hidden mb-4">
                                    <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                                        <h6 class="mb-0 fw-bold text-dark"><i class="ri-file-list-3-line me-1 text-primary"></i>
                                            Job Card Wise Summary</h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <button type="button"
                                                class="btn btn-outline-secondary btn-sm rounded-pill btn-back-to-emp-report">
                                                <i class="ri-arrow-left-line me-1"></i> Back to Report
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body py-4">
                                        <div class="card-datatable table-responsive">
                                            <table class="table table-hover align-middle mb-0 text-nowrap" id="employeeJobsTable" style="width: 100%;">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>Job Card No</th>
                                                        <th>Unit / Plant</th>
                                                        <th>Tasks / Services</th>
                                                        <th class="text-center">Target Qty</th>
                                                        <th class="text-center">Completed</th>
                                                        <th class="text-center">Pending</th>
                                                        <th class="text-center">Efficiency</th>
                                                        <th class="text-center">Status</th>
                                                        <th>Remarks</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom Back to Report Button -->
                            <div class="d-flex justify-content-start mb-3">
                                <button type="button"
                                    class="btn btn-outline-primary rounded-pill px-4 btn-back-to-emp-report">
                                    <i class="ri-arrow-left-line me-1"></i> Back to Employee Efficiency Report
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
                'casino-cutting-wip': {
                    tableId: '#casinoCuttingWipTable',
                    type: 'casino-cutting-wip',
                    columns: [
                        { data: 'job_card_no', name: 'job_card_no' },
                        { data: 'issue_date', name: 'issue_date' },
                        { data: 'delivery_date', name: 'delivery_date' },
                        { data: 'age_days', name: 'age_days', className: 'text-center' },
                        { data: 'brand', name: 'brand' },
                        { data: 'season', name: 'season' },
                        { data: 'pattern', name: 'pattern' },
                        { data: 'fabric', name: 'fabric', className: 'text-center' },
                        { data: 'issue_mts', name: 'issue_mts', className: 'text-center' },
                        { data: 'estimate_qty', name: 'estimate_qty', className: 'text-center' },
                        { data: 'cut_qty', name: 'cut_qty', className: 'text-center' },
                        { data: 'bundle', name: 'bundle', className: 'text-center' },
                        { data: 'balance_bundle', name: 'balance_bundle', className: 'text-center' },
                        { data: 'full_sleeve', name: 'full_sleeve', className: 'text-center' },
                        { data: 'half_sleeve', name: 'half_sleeve', className: 'text-center' },
                        { data: 'unit_assigned', name: 'unit_assigned' },
                        { data: 'status', name: 'status', className: 'text-center' },
                        { data: 'priority', name: 'priority', className: 'text-center' },
                        { data: 'remarks', name: 'remarks' }
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
                },
                'employee-efficiency': {
                    tableId: '#employeeEfficiencyTable',
                    type: 'employee-efficiency',
                    columns: [
                        { data: 'emp_id', name: 'emp_id_raw' },
                        { data: 'employee_name', name: 'employee_name' },
                        { data: 'designation', name: 'designation' },
                        { data: 'task', name: 'task' },
                        { data: 'hours_working', name: 'hours_working', className: 'text-center' },
                        { data: 'target_qty', name: 'target_qty', className: 'text-center' },
                        { data: 'completed', name: 'completed', className: 'text-center' },
                        { data: 'pending', name: 'pending', className: 'text-center' },
                        { data: 'efficiency', name: 'efficiency', className: 'text-center' },
                        { data: 'remark', name: 'remark' }
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
                        if (btnType === 'excel' || button.hasClass('buttons-excel') || button.hasClass('detail-buttons-excel') || button.hasClass('emp-detail-buttons-excel')) {
                            if ($.fn.dataTable.ext.buttons.excelHtml5 && $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
                                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
                            } else if ($.fn.dataTable.ext.buttons.excelFlash && $.fn.dataTable.ext.buttons.excelFlash.available(dt, config)) {
                                $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
                            }
                        } else if (btnType === 'pdf' || button.hasClass('buttons-pdf') || button.hasClass('detail-buttons-pdf') || button.hasClass('emp-detail-buttons-pdf')) {
                            if ($.fn.dataTable.ext.buttons.pdfHtml5 && $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config)) {
                                $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config);
                            } else if ($.fn.dataTable.ext.buttons.pdfFlash && $.fn.dataTable.ext.buttons.pdfFlash.available(dt, config)) {
                                $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
                            }
                        } else if (btnType === 'print' || button.hasClass('buttons-print') || button.hasClass('detail-buttons-print') || button.hasClass('emp-detail-buttons-print')) {
                            $.fn.dataTable.ext.buttons.print.action.call(self, e, dt, button, config);
                        }

                        // Restore original start and clear export flag
                        dt.one('preXhr', function (e, s, data) {
                            settings._iDisplayStart = oldStart;
                            data.start = oldStart;
                            data.length = dt.page.len();
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
                        if (config.type === 'employee-efficiency') {
                            const json = settings.json;
                            if (json && json.meta) {
                                $('#empOverallEffVal').text(json.meta.overall_efficiency || '0%');
                                $('#empEffProgressLabel').text(json.meta.overall_efficiency || '0%');
                                const effNum = Math.min(100, Math.max(0, parseFloat(json.meta.efficiency_val || 0)));
                                $('#empEffProgressBar').css('width', effNum + '%');
                                if (effNum >= 95) {
                                    $('#empEffProgressBar').removeClass('bg-warning bg-danger').addClass('bg-success');
                                } else if (effNum >= 75) {
                                    $('#empEffProgressBar').removeClass('bg-success bg-danger').addClass('bg-warning');
                                } else {
                                    $('#empEffProgressBar').removeClass('bg-success bg-warning').addClass('bg-danger');
                                }
                                $('#empSummaryCount').text(json.meta.total_employees || '0');
                                $('#empSummaryHours').text(json.meta.total_hours || '0 Hrs');
                                $('#empSummaryTarget').text(json.meta.total_target || '0 Pcs');
                                $('#empSummaryActual').text(json.meta.total_completed || '0 Pcs');
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
                                columns: ':not(.no-export)',
                                format: {
                                    body: function (data, row, column, node) {
                                        if (typeof data === 'string') {
                                            return data.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim();
                                        }
                                        return data;
                                    }
                                }
                            },
                            action: serverSideExportAction
                        },
                        {
                            extend: 'pdf',
                            className: 'buttons-pdf d-none',
                            title: function () {
                                return $('#active_report_title').text().trim() || 'Production Report';
                            },
                            orientation: 'landscape',
                            pageSize: (config.columns && config.columns.length > 12) ? 'A3' : 'A4',
                            exportOptions: {
                                columns: ':not(.no-export)',
                                format: {
                                    body: function (data, row, column, node) {
                                        if (typeof data === 'string') {
                                            return data.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim();
                                        }
                                        return data;
                                    }
                                }
                            },
                            customize: function (doc) {
                                var colCount = (config.columns ? config.columns.length : 0);
                                var isVeryWide = colCount > 12;

                                doc.pageOrientation = 'landscape';
                                if (isVeryWide) {
                                    doc.pageSize = 'A3';
                                    doc.defaultStyle.fontSize = 6.5;
                                    doc.styles.tableHeader.fontSize = 7;
                                    doc.pageMargins = [10, 15, 10, 15];
                                } else if (colCount >= 10) {
                                    doc.pageSize = 'A4';
                                    doc.defaultStyle.fontSize = 7;
                                    doc.styles.tableHeader.fontSize = 7.5;
                                    doc.pageMargins = [12, 15, 12, 15];
                                } else {
                                    doc.pageSize = 'A4';
                                    doc.defaultStyle.fontSize = 8;
                                    doc.styles.tableHeader.fontSize = 8.5;
                                    doc.pageMargins = [15, 15, 15, 15];
                                }

                                if (doc.content) {
                                    for (var i = 0; i < doc.content.length; i++) {
                                        if (doc.content[i].table) {
                                            var actualCols = doc.content[i].table.body[0].length;
                                            if (config.type === 'employee-efficiency' && actualCols === 10) {
                                                doc.content[i].table.widths = ['6%', '13%', '12%', '14%', '7%', '8%', '8%', '7%', '8%', '17%'];
                                            } else {
                                                doc.content[i].table.widths = Array(actualCols).fill('*');
                                            }
                                            break;
                                        }
                                    }
                                }
                            },
                            action: serverSideExportAction
                        },
                        {
                            extend: 'print',
                            className: 'buttons-print d-none',
                            title: function () {
                                return $('#active_report_title').text().trim() || 'Production Report';
                            },
                            exportOptions: {
                                columns: ':not(.no-export)',
                                format: {
                                    body: function (data, row, column, node) {
                                        if (typeof data === 'string') {
                                            return data.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim();
                                        }
                                        return data;
                                    }
                                }
                            },
                            autoPrint: false,
                            customize: function (win) {
                                var colCount = (config.columns ? config.columns.length : 0);
                                var isVeryWide = colCount > 12;
                                var isWide = colCount >= 7;

                                var $winBody = $(win.document.body);
                                $winBody.css('padding', '15px');
                                $winBody.find('h1').css({
                                    'font-size': '18px',
                                    'margin-bottom': '12px'
                                });

                                var $tbl = $winBody.find('table');
                                $tbl.removeClass('text-nowrap')
                                    .addClass('table table-bordered')
                                    .css({
                                        'font-size': (isVeryWide ? '7.5px' : (colCount >= 10 ? '8.5px' : (isWide ? '9.5px' : '11px'))),
                                        'width': '100%',
                                        'table-layout': (colCount >= 8 ? 'fixed' : 'auto'),
                                        'border-collapse': 'collapse'
                                    });

                                if (config.type === 'employee-efficiency') {
                                    var empColWidths = ['6%', '13%', '12%', '14%', '7%', '8%', '8%', '7%', '8%', '17%'];
                                    $tbl.find('thead th').each(function (idx) {
                                        if (empColWidths[idx]) {
                                            $(this).css('width', empColWidths[idx]);
                                        }
                                    });
                                }

                                var pageOrientation = isWide ? 'landscape' : 'portrait';
                                var printStyle = '<style>' +
                                    '@page { size: ' + pageOrientation + '; margin: 8mm; } ' +
                                    'body { -webkit-print-color-adjust: exact; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; } ' +
                                    'table { width: 100% !important; border-collapse: collapse !important; } ' +
                                    'table th, table td { padding: 4px 5px !important; white-space: normal !important; word-wrap: break-word !important; word-break: break-word !important; vertical-align: middle !important; } ' +
                                    'table th { background-color: #f8f9fa !important; font-weight: 700 !important; color: #212529 !important; } ' +
                                    '.badge, .btn, span, a { font-size: inherit !important; padding: 0 !important; background: transparent !important; color: inherit !important; text-decoration: none !important; } ' +
                                    'i { display: none !important; } ' +
                                    '</style>';
                                $(win.document.head).append(printStyle);

                                setTimeout(function () {
                                    win.focus();
                                    win.print();
                                }, 600);
                            },
                            action: serverSideExportAction
                        }
                    ],
                    lengthMenu: [10, 25, 50, 100],
                    pageLength: 10
                });
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
                            customize: function (doc) {
                                doc.defaultStyle.fontSize = 8;
                                doc.styles.tableHeader.fontSize = 8.5;
                                doc.pageMargins = [15, 15, 15, 15];
                                if (doc.content) {
                                    for (var i = 0; i < doc.content.length; i++) {
                                        if (doc.content[i].table) {
                                            var colCount = doc.content[i].table.body[0].length;
                                            doc.content[i].table.widths = Array(colCount).fill('*');
                                            break;
                                        }
                                    }
                                }
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
                            autoPrint: false,
                            customize: function (win) {
                                $(win.document.body).css('padding', '20px');
                                $(win.document.body).find('h1').css({
                                    'font-size': '20px',
                                    'margin-bottom': '15px'
                                });
                                $(win.document.body).find('table')
                                    .addClass('table table-bordered')
                                    .css({
                                        'font-size': '11px',
                                        'width': '100%',
                                        'border-collapse': 'collapse'
                                    });
                                var printStyle = '<style>@page { size: landscape; margin: 10mm; } body { -webkit-print-color-adjust: exact; } table th, table td { padding: 4px 6px !important; }</style>';
                                $(win.document.head).append(printStyle);

                                setTimeout(function () {
                                    win.focus();
                                    win.print();
                                }, 600);
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

            // ==========================================
            // EMPLOYEE WISE EFFICIENCY REPORT DETAIL LOGIC
            // ==========================================
            let activeEmpId = null;
            let activeEmpName = '';
            let activeEmpCode = '';
            let activeEmpDesignation = '';
            let activeEmpSubView = 'tasks';
            let employeeTasksDataTable = null;
            let employeeJobsDataTable = null;

            function initEmployeeTasksDataTable(empId) {
                if ($.fn.DataTable.isDataTable('#employeeTasksTable')) {
                    var dt = $('#employeeTasksTable').DataTable();
                    dt.ajax.reload(null, true);
                    return;
                }

                employeeTasksDataTable = $('#employeeTasksTable').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    destroy: true,
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    language: {
                        processing: '<div class="d-flex align-items-center justify-content-center py-4 text-primary fw-bold"><div class="spinner-border spinner-border-sm me-2" role="status"></div> Loading task details...</div>',
                        emptyTable: '<div class="text-center py-4 text-muted"><i class="ri-inbox-line ri-2x mb-2 d-block text-secondary"></i>No tasks found for this employee with the selected filters.</div>',
                        zeroRecords: '<div class="text-center py-3 text-muted">No matching records found</div>'
                    },
                    ajax: {
                        url: "{{ url('production_reports/ajax/employee-tasks') }}",
                        type: "GET",
                        data: function (d) {
                            d.emp_id = activeEmpId;
                            d.from_date = $('.start_date').val();
                            d.to_date = $('.end_date').val();
                            d.unit_id = $('select[name="unit_id"]').val();
                        },
                        dataSrc: function (json) {
                            if (json && json.summary) {
                                $('#empDetailTotalCount').text(json.summary.total_tasks != null ? json.summary.total_tasks : 0);
                                $('#empDetailTotalHours').text(json.summary.total_hours || '0 Hrs');
                                $('#empDetailTotalTarget').text(json.summary.total_target || '0 Pcs');
                                $('#empDetailTotalCompleted').text(json.summary.total_completed || '0 Pcs');
                                $('#empDetailTotalPending').text(json.summary.total_pending || '0 Pcs');
                                $('#empDetailEfficiency').text(json.summary.efficiency || '0%');
                            }
                            if (json && json.employee) {
                                if (json.employee.designation && json.employee.designation !== '-') {
                                    activeEmpDesignation = json.employee.designation;
                                    $('#empDetailSubtitle').text('Emp ID: ' + (json.employee.emp_id || '-') + ' | Designation: ' + activeEmpDesignation);
                                }
                            }
                            return json.data || [];
                        }
                    },
                    columns: [
                        { data: 'task_no', name: 'task_no' },
                        { data: 'job_card_no', name: 'job_card_no' },
                        { data: 'service', name: 'service' },
                        { data: 'stage', name: 'stage' },
                        { data: 'hours_worked', name: 'hours_worked', className: 'text-center' },
                        { data: 'target_qty', name: 'target_qty', className: 'text-center' },
                        { data: 'completed_qty', name: 'completed_qty', className: 'text-center' },
                        { data: 'pending_qty', name: 'pending_qty', className: 'text-center' },
                        { data: 'efficiency', name: 'efficiency', className: 'text-center' },
                        { data: 'status', name: 'status', className: 'text-center' },
                        { data: 'remarks', name: 'remarks' }
                    ],
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    buttons: [
                        {
                            extend: 'excel',
                            className: 'emp-detail-buttons-excel d-none',
                            title: function () {
                                return (activeEmpName ? activeEmpName + ' - Task-Wise Efficiency Report' : 'Employee Tasks Report');
                            },
                            exportOptions: {
                                columns: ':visible',
                                format: {
                                    body: function (data, row, column, node) {
                                        if (typeof data === 'string') {
                                            return data.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim();
                                        }
                                        return data;
                                    }
                                }
                            },
                            action: serverSideExportAction
                        },
                        {
                            extend: 'pdf',
                            className: 'emp-detail-buttons-pdf d-none',
                            title: function () {
                                return (activeEmpName ? activeEmpName + ' - Task-Wise Efficiency Report' : 'Employee Tasks Report');
                            },
                            orientation: 'landscape',
                            pageSize: 'A4',
                            exportOptions: {
                                columns: ':visible',
                                format: {
                                    body: function (data, row, column, node) {
                                        if (typeof data === 'string') {
                                            return data.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim();
                                        }
                                        return data;
                                    }
                                }
                            },
                            customize: function (doc) {
                                doc.pageOrientation = 'landscape';
                                doc.pageSize = 'A4';
                                doc.defaultStyle.fontSize = 7;
                                doc.styles.tableHeader.fontSize = 7.5;
                                doc.pageMargins = [10, 12, 10, 12];
                                if (doc.content) {
                                    for (var i = 0; i < doc.content.length; i++) {
                                        if (doc.content[i].table) {
                                            var colCount = doc.content[i].table.body[0].length;
                                            if (colCount === 11) {
                                                doc.content[i].table.widths = ['9%', '9%', '11%', '10%', '7%', '8%', '8%', '7%', '8%', '8%', '15%'];
                                            } else {
                                                doc.content[i].table.widths = Array(colCount).fill('*');
                                            }
                                            break;
                                        }
                                    }
                                }
                            },
                            action: serverSideExportAction
                        },
                        {
                            extend: 'print',
                            className: 'emp-detail-buttons-print d-none',
                            title: function () {
                                return (activeEmpName ? activeEmpName + ' - Task-Wise Efficiency Report' : 'Employee Tasks Report');
                            },
                            exportOptions: {
                                columns: ':visible',
                                format: {
                                    body: function (data, row, column, node) {
                                        if (typeof data === 'string') {
                                            return data.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim();
                                        }
                                        return data;
                                    }
                                }
                            },
                            autoPrint: false,
                            customize: function (win) {
                                var $winBody = $(win.document.body);
                                $winBody.css('padding', '15px');
                                $winBody.find('h1').css({
                                    'font-size': '18px',
                                    'margin-bottom': '12px'
                                });

                                var $tbl = $winBody.find('table');
                                $tbl.removeClass('text-nowrap')
                                    .addClass('table table-bordered')
                                    .css({
                                        'font-size': '8px',
                                        'width': '100%',
                                        'table-layout': 'fixed',
                                        'border-collapse': 'collapse'
                                    });

                                var taskColWidths = ['9%', '9%', '11%', '10%', '7%', '8%', '8%', '7%', '8%', '8%', '15%'];
                                $tbl.find('thead th').each(function (idx) {
                                    if (taskColWidths[idx]) {
                                        $(this).css('width', taskColWidths[idx]);
                                    }
                                });

                                var printStyle = '<style>' +
                                    '@page { size: landscape; margin: 8mm; } ' +
                                    'body { -webkit-print-color-adjust: exact; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; } ' +
                                    'table { width: 100% !important; border-collapse: collapse !important; } ' +
                                    'table th, table td { padding: 4px 5px !important; white-space: normal !important; word-wrap: break-word !important; word-break: break-word !important; vertical-align: middle !important; } ' +
                                    'table th { background-color: #f8f9fa !important; font-weight: 700 !important; color: #212529 !important; } ' +
                                    '.badge, .btn, span, a { font-size: inherit !important; padding: 0 !important; background: transparent !important; color: inherit !important; text-decoration: none !important; } ' +
                                    'i { display: none !important; } ' +
                                    '</style>';
                                $(win.document.head).append(printStyle);

                                setTimeout(function () {
                                    win.focus();
                                    win.print();
                                }, 600);
                            },
                            action: serverSideExportAction
                        }
                    ]
                });
            }

            function initEmployeeJobsDataTable(empId) {
                if ($.fn.DataTable.isDataTable('#employeeJobsTable')) {
                    var dt = $('#employeeJobsTable').DataTable();
                    dt.ajax.reload(null, true);
                    return;
                }

                employeeJobsDataTable = $('#employeeJobsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    destroy: true,
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    language: {
                        processing: '<div class="d-flex align-items-center justify-content-center py-4 text-primary fw-bold"><div class="spinner-border spinner-border-sm me-2" role="status"></div> Loading job card summary...</div>',
                        emptyTable: '<div class="text-center py-4 text-muted"><i class="ri-inbox-line ri-2x mb-2 d-block text-secondary"></i>No job cards found for this employee with the selected filters.</div>',
                        zeroRecords: '<div class="text-center py-3 text-muted">No matching records found</div>'
                    },
                    ajax: {
                        url: "{{ url('production_reports/ajax/employee-jobs') }}",
                        type: "GET",
                        data: function (d) {
                            d.emp_id = activeEmpId;
                            d.from_date = $('.start_date').val();
                            d.to_date = $('.end_date').val();
                            d.unit_id = $('select[name="unit_id"]').val();
                        },
                        dataSrc: function (json) {
                            if (json && json.summary) {
                                $('#empDetailTotalCount').text(json.summary.total_jobs != null ? json.summary.total_jobs : 0);
                                $('#empDetailTotalTarget').text(json.summary.total_target || '0 Pcs');
                                $('#empDetailTotalCompleted').text(json.summary.total_completed || '0 Pcs');
                                $('#empDetailTotalPending').text(json.summary.total_pending || '0 Pcs');
                                $('#empDetailEfficiency').text(json.summary.efficiency || '0%');
                            }
                            if (json && json.employee) {
                                if (json.employee.designation && json.employee.designation !== '-') {
                                    activeEmpDesignation = json.employee.designation;
                                    $('#empDetailSubtitle').text('Emp ID: ' + (json.employee.emp_id || '-') + ' | Designation: ' + activeEmpDesignation);
                                }
                            }
                            return json.data || [];
                        }
                    },
                    columns: [
                        { data: 'job_card_no', name: 'job_card_no' },
                        { data: 'unit', name: 'unit' },
                        { data: 'tasks', name: 'tasks' },
                        { data: 'target_qty', name: 'target_qty', className: 'text-center' },
                        { data: 'completed_qty', name: 'completed_qty', className: 'text-center' },
                        { data: 'pending_qty', name: 'pending_qty', className: 'text-center' },
                        { data: 'efficiency', name: 'efficiency', className: 'text-center' },
                        { data: 'status', name: 'status', className: 'text-center' },
                        { data: 'remarks', name: 'remarks' }
                    ],
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    buttons: [
                        {
                            extend: 'excel',
                            className: 'emp-detail-buttons-excel d-none',
                            title: function () {
                                return (activeEmpName ? activeEmpName + ' - Job Card Summary' : 'Employee Job Summary');
                            },
                            exportOptions: {
                                columns: ':visible',
                                format: {
                                    body: function (data, row, column, node) {
                                        if (typeof data === 'string') {
                                            return data.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim();
                                        }
                                        return data;
                                    }
                                }
                            },
                            action: serverSideExportAction
                        },
                        {
                            extend: 'pdf',
                            className: 'emp-detail-buttons-pdf d-none',
                            title: function () {
                                return (activeEmpName ? activeEmpName + ' - Job Card Summary' : 'Employee Job Summary');
                            },
                            orientation: 'landscape',
                            pageSize: 'A4',
                            exportOptions: {
                                columns: ':visible',
                                format: {
                                    body: function (data, row, column, node) {
                                        if (typeof data === 'string') {
                                            return data.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim();
                                        }
                                        return data;
                                    }
                                }
                            },
                            customize: function (doc) {
                                doc.pageOrientation = 'landscape';
                                doc.pageSize = 'A4';
                                doc.defaultStyle.fontSize = 7.5;
                                doc.styles.tableHeader.fontSize = 8;
                                doc.pageMargins = [10, 12, 10, 12];
                                if (doc.content) {
                                    for (var i = 0; i < doc.content.length; i++) {
                                        if (doc.content[i].table) {
                                            var colCount = doc.content[i].table.body[0].length;
                                            if (colCount === 9) {
                                                doc.content[i].table.widths = ['12%', '12%', '16%', '9%', '9%', '8%', '9%', '9%', '16%'];
                                            } else {
                                                doc.content[i].table.widths = Array(colCount).fill('*');
                                            }
                                            break;
                                        }
                                    }
                                }
                            },
                            action: serverSideExportAction
                        },
                        {
                            extend: 'print',
                            className: 'emp-detail-buttons-print d-none',
                            title: function () {
                                return (activeEmpName ? activeEmpName + ' - Job Card Summary' : 'Employee Job Summary');
                            },
                            exportOptions: {
                                columns: ':visible',
                                format: {
                                    body: function (data, row, column, node) {
                                        if (typeof data === 'string') {
                                            return data.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim();
                                        }
                                        return data;
                                    }
                                }
                            },
                            autoPrint: false,
                            customize: function (win) {
                                var $winBody = $(win.document.body);
                                $winBody.css('padding', '15px');
                                $winBody.find('h1').css({
                                    'font-size': '18px',
                                    'margin-bottom': '12px'
                                });

                                var $tbl = $winBody.find('table');
                                $tbl.removeClass('text-nowrap')
                                    .addClass('table table-bordered')
                                    .css({
                                        'font-size': '8.5px',
                                        'width': '100%',
                                        'table-layout': 'fixed',
                                        'border-collapse': 'collapse'
                                    });

                                var jobColWidths = ['12%', '12%', '16%', '9%', '9%', '8%', '9%', '9%', '16%'];
                                $tbl.find('thead th').each(function (idx) {
                                    if (jobColWidths[idx]) {
                                        $(this).css('width', jobColWidths[idx]);
                                    }
                                });

                                var printStyle = '<style>' +
                                    '@page { size: landscape; margin: 8mm; } ' +
                                    'body { -webkit-print-color-adjust: exact; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; } ' +
                                    'table { width: 100% !important; border-collapse: collapse !important; } ' +
                                    'table th, table td { padding: 4px 5px !important; white-space: normal !important; word-wrap: break-word !important; word-break: break-word !important; vertical-align: middle !important; } ' +
                                    'table th { background-color: #f8f9fa !important; font-weight: 700 !important; color: #212529 !important; } ' +
                                    '.badge, .btn, span, a { font-size: inherit !important; padding: 0 !important; background: transparent !important; color: inherit !important; text-decoration: none !important; } ' +
                                    'i { display: none !important; } ' +
                                    '</style>';
                                $(win.document.head).append(printStyle);

                                setTimeout(function () {
                                    win.focus();
                                    win.print();
                                }, 600);
                            },
                            action: serverSideExportAction
                        }
                    ]
                });
            }

            function switchEmpSubView(viewType) {
                activeEmpSubView = viewType;
                if (viewType === 'tasks') {
                    $('#btnShowEmpTasks').addClass('btn-primary active').removeClass('btn-outline-primary');
                    $('#btnShowEmpJobs').removeClass('btn-primary active').addClass('btn-outline-primary');
                    $('#empTaskSubView').removeClass('d-none');
                    $('#empJobSubView').addClass('d-none');
                    $('#empDetailCountLabel').text('Total Tasks');
                    initEmployeeTasksDataTable(activeEmpId);
                } else {
                    $('#btnShowEmpJobs').addClass('btn-primary active').removeClass('btn-outline-primary');
                    $('#btnShowEmpTasks').removeClass('btn-primary active').addClass('btn-outline-primary');
                    $('#empJobSubView').removeClass('d-none');
                    $('#empTaskSubView').addClass('d-none');
                    $('#empDetailCountLabel').text('Total Job Cards');
                    initEmployeeJobsDataTable(activeEmpId);
                }
            }

            function openEmpDetailView(empId, empName, empCode, designation, initialView, pushHistory) {
                activeEmpId = empId;
                activeEmpName = empName || 'Employee';
                activeEmpCode = empCode || '';
                activeEmpDesignation = designation || '-';
                activeEmpSubView = initialView || 'tasks';

                $('#empDetailTitle').html('<i class="ri-user-line me-2"></i> ' + activeEmpName);
                $('#empDetailSubtitle').text('Emp ID: ' + (activeEmpCode || '-') + ' | Designation: ' + (activeEmpDesignation || '-'));
                $('#active_report_title').html('<span class="text-muted">Employee Wise Efficiency &gt;</span> ' + activeEmpName);

                const fromDateVal = $('.start_date').val() || '';
                const toDateVal = $('.end_date').val() || '';
                const unitText = $('select[name="unit_id"] option:selected').text();
                $('#empDetailFilterDateRange').text(fromDateVal || toDateVal ? 'Dates: ' + (fromDateVal || 'Start') + ' to ' + (toDateVal || 'Now') : 'All Dates');
                $('#empDetailFilterUnit').text(unitText && unitText.trim() ? 'Unit: ' + unitText.trim() : 'All Units');

                $('#empDetailTotalCount').text('-');
                $('#empDetailTotalHours').text('-');
                $('#empDetailTotalTarget').text('-');
                $('#empDetailTotalCompleted').text('-');
                $('#empDetailTotalPending').text('-');
                $('#empDetailEfficiency').text('-');

                $('#employeeEfficiencyMainView').addClass('d-none');
                $('#employeeDetailView').removeClass('d-none');

                if (pushHistory !== false) {
                    history.pushState({ view: 'emp-detail', empId: empId, empName: activeEmpName, empCode: activeEmpCode, designation: activeEmpDesignation, subView: activeEmpSubView }, '', '#emp-' + empId + '-' + activeEmpSubView);
                }

                $('html, body').animate({ scrollTop: $('#employeeDetailView').offset().top - 80 }, 200);

                switchEmpSubView(activeEmpSubView);
            }

            function returnToEmpMainView() {
                activeEmpId = null;
                activeEmpName = '';
                activeEmpCode = '';
                activeEmpDesignation = '';

                $('#employeeDetailView').addClass('d-none');
                $('#employeeEfficiencyMainView').removeClass('d-none');

                const origTitle = $('#report_type_select option:selected').text() || 'Employee Wise Efficiency Report';
                $('#active_report_title').html(origTitle);

                if (window.location.hash && window.location.hash.indexOf('#emp-') !== -1) {
                    history.replaceState(null, '', window.location.pathname + window.location.search);
                }

                if ($.fn.DataTable.isDataTable('#employeeEfficiencyTable')) {
                    $('#employeeEfficiencyTable').DataTable().columns.adjust();
                }
            }

            // Employee Task-Wise Breakdown Click Handler (Point 1: clicking Employee Name)
            $(document).on('click', '.view-emp-tasks', function (e) {
                e.preventDefault();
                const empId = $(this).data('emp-id');
                const empName = $(this).data('emp-name') || 'Employee';
                const empCode = $(this).data('emp-code') || '';
                const designation = $(this).data('designation') || '-';
                openEmpDetailView(empId, empName, empCode, designation, 'tasks', true);
            });

            // Employee Job-Wise Summary Click Handler (Point 2: clicking Target Qty)
            $(document).on('click', '.view-emp-jobs', function (e) {
                e.preventDefault();
                const empId = $(this).data('emp-id');
                const empName = $(this).data('emp-name') || 'Employee';
                const empCode = $(this).data('emp-code') || '';
                const designation = $(this).data('designation') || '-';
                openEmpDetailView(empId, empName, empCode, designation, 'jobs', true);
            });

            // Switch to Task Wise Sub-view
            $(document).on('click', '#btnShowEmpTasks', function (e) {
                e.preventDefault();
                switchEmpSubView('tasks');
            });

            // Switch to Job Wise Sub-view
            $(document).on('click', '#btnShowEmpJobs', function (e) {
                e.preventDefault();
                switchEmpSubView('jobs');
            });

            // Back to Employee Report Buttons Handler
            $(document).on('click', '.btn-back-to-emp-report', function (e) {
                e.preventDefault();
                if (window.location.hash && window.location.hash.indexOf('#emp-') !== -1) {
                    history.back();
                } else {
                    returnToEmpMainView();
                }
            });

            // Browser Client Back / Forward Button Popstate Handler
            window.addEventListener('popstate', function (e) {
                if (e.state && e.state.view === 'dept-detail' && e.state.stageId) {
                    openDeptDetailView(e.state.stageId, e.state.stageName, false);
                } else if (e.state && e.state.view === 'emp-detail' && e.state.empId) {
                    openEmpDetailView(e.state.empId, e.state.empName, e.state.empCode, e.state.designation, e.state.subView || 'tasks', false);
                } else {
                    if (!$('#deptEfficiencyDetailView').hasClass('d-none')) {
                        returnToDeptMainView();
                    }
                    if (!$('#employeeDetailView').hasClass('d-none')) {
                        returnToEmpMainView();
                    }
                }
            });

            // Select Report Type Change Listener
            $('#report_type_select').on('change', function () {
                const selectedType = $(this).val();
                const selectedText = $(this).find('option:selected').text();
                $('#active_report_title').html(selectedText);

                // Reset detail views if switching tabs
                returnToDeptMainView();
                returnToEmpMainView();

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

                // If currently viewing employee detail, refresh active detail subview
                if (activeTabId === 'employee-efficiency' && activeEmpId && !$('#employeeDetailView').hasClass('d-none')) {
                    if (activeEmpSubView === 'jobs' && employeeJobsDataTable) {
                        employeeJobsDataTable.ajax.reload(null, false);
                    } else if (employeeTasksDataTable) {
                        employeeTasksDataTable.ajax.reload(null, false);
                    } else {
                        switchEmpSubView(activeEmpSubView);
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
                returnToEmpMainView();
                $('#productionReportForm').trigger('submit');
            });

            // Top Header Export Handlers
            $('#btn-excel').on('click', function () {
                if ($('#report_type_select').val() === 'department-efficiency' && !$('#deptEfficiencyDetailView').hasClass('d-none')) {
                    if (detailTasksDataTable) {
                        detailTasksDataTable.button('.detail-buttons-excel').trigger();
                        return;
                    }
                }
                if ($('#report_type_select').val() === 'employee-efficiency' && !$('#employeeDetailView').hasClass('d-none')) {
                    if (activeEmpSubView === 'jobs' && employeeJobsDataTable) {
                        employeeJobsDataTable.button('.emp-detail-buttons-excel').trigger();
                        return;
                    } else if (employeeTasksDataTable) {
                        employeeTasksDataTable.button('.emp-detail-buttons-excel').trigger();
                        return;
                    }
                }
                var activeTable = $('.tab-pane.active .datatables-products');
                if (activeTable.length && $.fn.DataTable.isDataTable(activeTable)) {
                    activeTable.DataTable().button('.buttons-excel').trigger();
                }
            });
            $('#btn-pdf').on('click', function () {
                if ($('#report_type_select').val() === 'department-efficiency' && !$('#deptEfficiencyDetailView').hasClass('d-none')) {
                    if (detailTasksDataTable) {
                        detailTasksDataTable.button('.detail-buttons-pdf').trigger();
                        return;
                    }
                }
                if ($('#report_type_select').val() === 'employee-efficiency' && !$('#employeeDetailView').hasClass('d-none')) {
                    if (activeEmpSubView === 'jobs' && employeeJobsDataTable) {
                        employeeJobsDataTable.button('.emp-detail-buttons-pdf').trigger();
                        return;
                    } else if (employeeTasksDataTable) {
                        employeeTasksDataTable.button('.emp-detail-buttons-pdf').trigger();
                        return;
                    }
                }
                var activeTable = $('.tab-pane.active .datatables-products');
                if (activeTable.length && $.fn.DataTable.isDataTable(activeTable)) {
                    activeTable.DataTable().button('.buttons-pdf').trigger();
                }
            });
            $('#btn-print').on('click', function () {
                if ($('#report_type_select').val() === 'department-efficiency' && !$('#deptEfficiencyDetailView').hasClass('d-none')) {
                    if (detailTasksDataTable) {
                        detailTasksDataTable.button('.detail-buttons-print').trigger();
                        return;
                    }
                }
                if ($('#report_type_select').val() === 'employee-efficiency' && !$('#employeeDetailView').hasClass('d-none')) {
                    if (activeEmpSubView === 'jobs' && employeeJobsDataTable) {
                        employeeJobsDataTable.button('.emp-detail-buttons-print').trigger();
                        return;
                    } else if (employeeTasksDataTable) {
                        employeeTasksDataTable.button('.emp-detail-buttons-print').trigger();
                        return;
                    }
                }
                var activeTable = $('.tab-pane.active .datatables-products');
                if (activeTable.length && $.fn.DataTable.isDataTable(activeTable)) {
                    activeTable.DataTable().button('.buttons-print').trigger();
                }
            });
        });
    </script>
@endsection
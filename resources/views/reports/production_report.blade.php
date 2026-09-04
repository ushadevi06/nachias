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
            <form id="productionReportForm" class="row g-3 align-items-end" onsubmit="return false;">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-primary"><i class="ri-file-chart-line me-1"></i>Select Report Type</label>
                    <select class="form-select select2" id="report_type_select" name="report_type">
                        <option value="production-wip" selected>🏭 Production WIP Unit Wise</option>
                        <option value="performance-report">👤 Performance Individual</option>
                        <option value="process-wise">⚙️ Production Report Section Wise</option>
                        <option value="completion-report">📅 Job Card Completed Date</option>
                        <option value="brand-production">🏷️ Brand Wise Unit Production</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">From Date</label>
                    <input type="text" class="form-control start_date" name="from_date" value="{{ request('from_date') }}" placeholder="DD-MM-YYYY">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">To Date</label>
                    <input type="text" class="form-control end_date" name="to_date" value="{{ request('to_date') }}" placeholder="DD-MM-YYYY">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Unit</label>
                    <select class="form-select select2" name="unit_id" id="unit_id_filter" data-placeholder="Select Unit">
                        <option value=""></option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill p-2" title="Search">
                        <i class="ri ri-search-line me-1"></i> Search
                    </button>
                    <button type="button" id="btn-reset-report" class="btn btn-outline-light rounded-pill border p-2" title="Reset">
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

    .badge.bg-label-primary { background: #dbeafe; color: #1e40af; }
    .badge.bg-label-success { background: #dcfce7; color: #166534; }
    .badge.bg-label-info { background: #e0f2fe; color: #0369a1; }
    .badge.bg-label-warning { background: #fef9c3; color: #854d0e; }
    .badge.bg-label-danger { background: #fee2e2; color: #991b1b; }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
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
        }
    };

    $.fn.dataTable.ext.errMode = 'none';

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
                    dt.ajax.reload(function() { showReportLoading(false); }, false);
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
                data: function(d) {
                    d.from_date = $('.start_date').val();
                    d.to_date = $('.end_date').val();
                    d.unit_id = $('select[name="unit_id"]').val();
                }
            },
            drawCallback: function() {
                showReportLoading(false);
            },
            columns: config.columns,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            buttons: [
                { extend: 'excel', className: 'buttons-excel d-none' },
                { extend: 'pdf', className: 'buttons-pdf d-none' },
                { extend: 'print', className: 'buttons-print d-none' }
            ],
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10
        });
    }

    // Select Report Type Change Listener
    $('#report_type_select').on('change', function() {
        const selectedType = $(this).val();
        const selectedText = $(this).find('option:selected').text();
        $('#active_report_title').html(selectedText);

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
    $('#productionReportForm').on('submit', function(e) {
        e.preventDefault();
        const activeTabId = $('#report_type_select').val() || 'production-wip';
        const config = tableConfigs[activeTabId];
        if (config && $.fn.DataTable.isDataTable(config.tableId)) {
            const dt = $(config.tableId).DataTable();
            if (dt && dt.ajax && typeof dt.ajax.url === 'function' && dt.ajax.url()) {
                try {
                    showReportLoading(true);
                    dt.ajax.reload(function() { showReportLoading(false); });
                    return;
                } catch (err) {
                    dt.destroy();
                }
            }
        }
        loadActiveTabTable(activeTabId);
    });

    // Reset Button Handler
    $(document).on('click', '#btn-reset-report', function(e) {
        e.preventDefault();
        $('.start_date').val('');
        $('.end_date').val('');
        $('select[name="unit_id"]').val('').trigger('change');
        $('#productionReportForm').trigger('submit');
    });

    // Export Handlers
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

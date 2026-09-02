@extends('layouts.common')
@section('title', 'Fabric Store Report - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <!-- Header Section -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-primary">Fabric Store Report</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Reports</li>
                    <li class="breadcrumb-item active">Purchase Reports</li>
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
            <form id="fabricReportForm" class="row g-3 align-items-end" method="GET" action="{{ url('purchase_reports/fabric') }}">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-primary"><i class="ri-file-chart-line me-1"></i>Select Report Type</label>
                    <select class="form-select select2" id="report_type_select" name="report_type">
                        <option value="po-report" selected>📋 PO Supplier Wise</option>
                        <option value="stock-report">📦 Stock Report</option>
                        <option value="ageing-report">⏳ Stock Ageing</option>
                        <option value="consumption-report">📊 Average Consumption</option>
                        <option value="minstock-report">⚠️ Minimum Stock</option>
                        <option value="return-report">🔄 Return Goods</option>
                        <option value="performance-report">⭐ Supplier Performance</option>
                        <option value="casino-po-report">🏷️ Casino Purchase Order Report</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">From Date</label>
                    <input type="text" class="form-control start_date" name="from_date" placeholder="DD-MM-YYYY">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">To Date</label>
                    <input type="text" class="form-control end_date" name="to_date" placeholder="DD-MM-YYYY">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Supplier</label>
                    <select class="form-select select2" name="supplier_id" id="supplier_id" data-placeholder="Select Supplier">
                        <option value=""></option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">
                        <i class="ri ri-search-line me-1"></i> Search
                    </button>
                    <button type="reset" class="btn btn-outline-light w-100 rounded-pill border text-dark" onclick="window.location.reload();">
                        Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Content Card -->
    <div class="card shadow-sm border-0 premium-content-card">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-dark" id="active_report_title">📋 PO Supplier Wise</h5>
        </div>
        <div class="card-body py-4">
            <div class="tab-content" id="reportTabsContent">
                <!-- 1. PO Supplier Wise -->
                <div class="tab-pane fade show active" id="po-report" role="tabpanel">
                    @include('reports.purchase_reports._po_supplier_wise', ['qtyLabel' => 'Meters'])
                </div>

                <!-- 2. Stock Report -->
                <div class="tab-pane fade" id="stock-report" role="tabpanel">
                    @include('reports.purchase_reports._stock_report', ['isFabric' => true])
                </div>

                <!-- 3. Stock Ageing -->
                <div class="tab-pane fade" id="ageing-report" role="tabpanel">
                    @include('reports.purchase_reports._ageing_report', ['isFabric' => true])
                </div>
                
                <div class="tab-pane fade" id="consumption-report" role="tabpanel">
                    @include('reports.purchase_reports._consumption_report')
                </div>
                <div class="tab-pane fade" id="minstock-report" role="tabpanel">
                    @include('reports.purchase_reports._minstock_report', ['isFabric' => true])
                </div>
                <div class="tab-pane fade" id="return-report" role="tabpanel">
                    @include('reports.purchase_reports._return_report', ['isFabric' => true])
                </div>
                <div class="tab-pane fade" id="performance-report" role="tabpanel">
                    @include('reports.purchase_reports._supplier_performance')
                </div>
                <div class="tab-pane fade" id="casino-po-report" role="tabpanel">
                    @include('reports.purchase_reports._casino_po_report')
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

    .premium-nav-tabs .nav-item {
        margin-bottom: 0;
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
        background-color: #f8fafc;
        border-top: none;
        border-bottom: 2px solid #e2e8f0;
        text-transform: uppercase;
        font-size: 0.7rem;
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

    .premium-table tr:hover {
        background-color: #f8faff;
    }

    .badge.bg-label-primary { background: #dbeafe; color: #1e40af; }
    .badge.bg-label-success { background: #dcfce7; color: #166534; }
    .badge.bg-label-info { background: #e0f2fe; color: #0369a1; }
    .badge.bg-label-warning { background: #fef9c3; color: #854d0e; }
    .badge.bg-label-danger { background: #fee2e2; color: #991b1b; }
</style>

<script>
$(document).ready(function() {
    function fetchReport() {
        const form = $('#fabricReportForm');
        const submitBtn = form.find('button[type="submit"]');
        const originalBtnHtml = submitBtn.html();

        submitBtn.html('<span class="spinner-border spinner-border-sm me-1"></span> Searching...').prop('disabled', true);
        $('.tab-content').css('opacity', '0.6');

        $.ajax({
            url: form.attr('action'),
            method: 'GET',
            data: form.serialize() + '&fetch_report=1',
            dataType: 'json',
            success: function(response) {
                $.each(response, function(tabId, html) {
                    const targetTab = $('#' + tabId);
                    if (targetTab.length) {
                        targetTab.html(html);
                    }
                });
            },
            error: function() {
                alert('An error occurred while fetching the report data. Please try again.');
            },
            complete: function() {
                submitBtn.html(originalBtnHtml).prop('disabled', false);
                $('.tab-content').css('opacity', '1');
            }
        });
    }

    $('#report_type_select').on('change', function() {
        let targetTabId = $(this).val();
        let selectedText = $(this).find('option:selected').text();
        $('#active_report_title').html('<i class="ri-file-chart-line text-primary me-2"></i>' + selectedText);

        $('.tab-pane').removeClass('show active');
        $('#' + targetTabId).addClass('show active');

        let activeTable = $('#' + targetTabId).find('table');
        if (activeTable.length && $.fn.DataTable.isDataTable(activeTable[0])) {
            let dt = activeTable.DataTable();
            if (dt.ajax && typeof dt.ajax.reload === 'function' && dt.ajax.url()) {
                dt.ajax.reload();
            }
        }
    });

    $('#fabricReportForm').on('submit', function(e) {
        e.preventDefault();
        let currentTabId = $('#report_type_select').val();
        let activeTable = $('#' + currentTabId).find('table');
        if (activeTable.length && $.fn.DataTable.isDataTable(activeTable[0])) {
            let dt = activeTable.DataTable();
            if (dt.ajax && typeof dt.ajax.reload === 'function' && dt.ajax.url()) {
                dt.ajax.reload();
                return;
            }
        }
    });

    // Export Handlers
    $('#btn-excel').on('click', function() {
        $('.tab-pane.active table').DataTable().button('.buttons-excel').trigger();
    });
    $('#btn-pdf').on('click', function() {
        $('.tab-pane.active table').DataTable().button('.buttons-pdf').trigger();
    });
    $('#btn-print').on('click', function() {
        $('.tab-pane.active table').DataTable().button('.buttons-print').trigger();
    });
});
</script>
@endsection

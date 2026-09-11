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
                        <option value="minstock-report">⚠️ Fabric Minimum Stock</option>
                        <option value="brandwise-minstock-report">🏷️ Brandwise Minimum Stock</option>
                        <option value="return-report">🔄 Return Goods</option>
                        <option value="performance-report">⭐ Supplier Performance</option>
                        <option value="casino-po-report">🏷️ Casino Purchase Order Report</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Brand</label>
                    <select class="form-select select2" name="brand_id" id="brand_id" data-placeholder="Select Brand">
                        <option value=""></option>
                        @if(isset($brands))
                            @foreach($brands as $brand)
                                @php
                                    $isSelected = false;
                                    if (request()->has('brand_id') && request('brand_id') == $brand->id) {
                                        $isSelected = true;
                                    } elseif (request('report_type') === 'brandwise-minstock-report' && !request()->has('brand_id')) {
                                        if (stripos($brand->brand_name, 'CASINO DHOTI SHIRTS') !== false || stripos($brand->brand_name, 'CASINO') !== false) {
                                            $isSelected = true;
                                        }
                                    }
                                @endphp
                                <option value="{{ $brand->id }}" {{ $isSelected ? 'selected' : '' }}>{{ $brand->brand_name }}</option>
                            @endforeach
                        @endif
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
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Supplier</label>
                    <select class="form-select select2" name="supplier_id" id="supplier_id" data-placeholder="Select Supplier">
                        <option value=""></option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill w-50 px-2" style="min-height: 38px;">
                        <i class="ri ri-search-line me-1"></i> Search
                    </button>
                    <button type="button" id="btn-reset-filters" class="btn btn-outline-secondary btn-sm rounded-pill w-50 px-2" style="min-height: 38px;">
                        <i class="ri ri-refresh-line me-1"></i> Reset
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
                <div class="tab-pane fade" id="brandwise-minstock-report" role="tabpanel">
                    @include('reports.purchase_reports._brandwise_minstock_report')
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

        if (targetTabId === 'brandwise-minstock-report') {
            if (typeof window.loadBrandwiseMinStockTable === 'function') {
                window.loadBrandwiseMinStockTable();
            }
            return;
        }

        if (typeof renderStockLevel1 === 'function') {
            renderStockLevel1();
        }

        let activeTable = $('#' + targetTabId).find('table').first();
        if (activeTable.length && $.fn.DataTable.isDataTable(activeTable[0])) {
            let dt = activeTable.DataTable();
            if (dt && dt.ajax && typeof dt.ajax.reload === 'function' && dt.ajax.url()) {
                dt.ajax.reload();
            }
        }
    });

    $('#fabricReportForm').on('submit', function(e) {
        e.preventDefault();
        let currentTabId = $('#report_type_select').val();
        if (currentTabId === 'brandwise-minstock-report') {
            if (typeof window.loadBrandwiseMinStockTable === 'function') {
                window.loadBrandwiseMinStockTable();
            }
            return;
        }

        if (typeof renderStockLevel1 === 'function') {
            renderStockLevel1();
        }

        let activeTable = $('#' + currentTabId).find('table').first();
        if (activeTable.length && $.fn.DataTable.isDataTable(activeTable[0])) {
            let dt = activeTable.DataTable();
            if (dt && dt.ajax && typeof dt.ajax.reload === 'function' && dt.ajax.url()) {
                dt.ajax.reload();
                return;
            }
        }
    });

    $('#btn-reset-filters').on('click', function() {
        $('.start_date, .end_date').val('');
        $('#supplier_id, #brand_id').val('').trigger('change.select2');
        $('#brandwise_search_input').val('');
        $('#fabricReportForm').trigger('submit');
    });

    // Export Handlers
    $('#btn-excel').on('click', function() {
        let currentTabId = $('#report_type_select').val() || $('.tab-pane.active').attr('id');
        if (currentTabId === 'brandwise-minstock-report') {
            let brandId = $('select[name="brand_id"]').val() || '';
            let fromDate = $('.start_date').val() || '';
            let toDate = $('.end_date').val() || '';
            let supplierId = $('select[name="supplier_id"]').val() || '';
            let search = $('#brandwise_search_input').val() || '';

            let exportUrl = "{{ url('purchase_reports/fabric') }}?export=brandwise-minstock-excel"
                + "&brand_id=" + encodeURIComponent(brandId)
                + "&from_date=" + encodeURIComponent(fromDate)
                + "&to_date=" + encodeURIComponent(toDate)
                + "&supplier_id=" + encodeURIComponent(supplierId)
                + "&search=" + encodeURIComponent(search);

            window.location.href = exportUrl;
            return;
        }

        let activeTable = $('.tab-pane.active table');
        if (activeTable.length && $.fn.DataTable.isDataTable(activeTable[0])) {
            let dt = activeTable.DataTable();
            let btn = dt.button('.buttons-excel');
            if (btn && btn.length) {
                btn.trigger();
                return;
            }
        }
    });

    function printBrandwiseReportWindow() {
        let printContents = document.querySelector('.brandwise-table-container');
        if (!printContents) {
            window.print();
            return;
        }
        let brandText = $('select[name="brand_id"] option:selected').text().trim() || 'Brandwise';
        let printWin = window.open('', '_blank', 'width=1200,height=800');
        if (!printWin) {
            window.print();
            return;
        }
        printWin.document.write('<!DOCTYPE html><html><head><title>Brandwise Minimum Stock Report - ' + brandText + '</title>');
        printWin.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">');
        printWin.document.write('<style>@page { size: landscape; margin: 8mm; } body { font-family: sans-serif; font-size: 9px; padding: 10px; color: #1e293b; } table { width: 100%; border-collapse: collapse; font-size: 8.5px; } th, td { border: 1px solid #999 !important; padding: 3px 4px !important; text-align: center; vertical-align: middle; } th { background-color: #f1f5f9 !important; font-weight: bold; } .bg-fs-header { background-color: #cbd5e1 !important; } .bg-hs-header { background-color: #e2e8f0 !important; } .bg-gross-header { background-color: #94a3b8 !important; color: #fff !important; } .bg-tl-header { background-color: #cbd5e1 !important; } .text-start { text-align: left !important; } .text-end { text-align: right !important; } .badge.bg-danger { background: #dc3545 !important; color: #fff !important; padding: 1px 3px; border-radius: 3px; font-weight: bold; }</style>');
        printWin.document.write('</head><body>');
        printWin.document.write('<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">');
        printWin.document.write('<div><h4 style="margin:0;font-weight:bold;">Brandwise Minimum Stock Report</h4><div style="font-weight:600;color:#0d6efd;">Brand: ' + brandText + '</div></div>');
        printWin.document.write('<div style="font-size:10px;color:#666;">Generated on: ' + (new Date()).toLocaleString() + '</div>');
        printWin.document.write('</div>');
        printWin.document.write(printContents.innerHTML);
        printWin.document.write('</body></html>');
        printWin.document.close();
        printWin.focus();
        setTimeout(function() {
            printWin.print();
        }, 500);
    }

    $('#btn-pdf, #btn-print').on('click', function() {
        let currentTabId = $('#report_type_select').val() || $('.tab-pane.active').attr('id');
        if (currentTabId === 'brandwise-minstock-report') {
            printBrandwiseReportWindow();
            return;
        }

        let isPdf = $(this).attr('id') === 'btn-pdf';
        let buttonClass = isPdf ? '.buttons-pdf' : '.buttons-print';
        let activeTable = $('.tab-pane.active table');
        if (activeTable.length && $.fn.DataTable.isDataTable(activeTable[0])) {
            let dt = activeTable.DataTable();
            let btn = dt.button(buttonClass);
            if (btn && btn.length) {
                btn.trigger();
                return;
            }
        }
        window.print();
    });
});
</script>
@endsection

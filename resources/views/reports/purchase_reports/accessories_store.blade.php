@extends('layouts.common')
@section('title', 'Accessories Store Report - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <!-- Header Section -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-primary">Accessories Store Report</h4>
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
            <form id="accessoriesReportForm" class="row g-3 align-items-end" method="GET" action="{{ url('purchase_reports/accessories') }}">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-primary"><i class="ri ri-file-chart-line me-1"></i>Select Report Type</label>
                    <select class="form-select select2" id="report_type_select" name="report_type">
                        <option value="po-report" selected>📋 PO Supplier Wise</option>
                        <option value="stock-report">📦 Stock Report</option>
                        <option value="ageing-report">⏳ Stock Ageing</option>
                        <option value="cost-report">💰 Average Cost</option>
                        <option value="minstock-report">⚠️ Minimum Stock</option>
                        <option value="return-report">🔄 Return Goods</option>
                        <option value="performance-report">⭐ Supplier Performance</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Brand</label>
                    <select class="form-select select2" name="brand_id" id="brand_id" data-placeholder="Select Brand">
                        <option value=""></option>
                        @if(isset($brands))
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->brand_name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Date Range</label>
                    <input type="text" class="form-control report_date_range" id="accessories_store_date_range" placeholder="DD-MM-YYYY to DD-MM-YYYY" value="{{ (request('from_date') && request('to_date')) ? (request('from_date') == request('to_date') ? request('from_date') : request('from_date') . ' to ' . request('to_date')) : (request('from_date') ?? '') }}">
                    <input type="hidden" class="start_date" name="from_date" value="{{ request('from_date') }}">
                    <input type="hidden" class="end_date" name="to_date" value="{{ request('to_date') }}">
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
                <div class="col-12 col-md-1 col-xl-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill w-50 px-2" style="min-height: 38px;" title="Search">
                        <i class="ri ri-search-line me-1"></i>
                    </button>
                    <button type="button" id="btn-reset-filters" class="btn btn-light btn-sm rounded-pill w-50 px-2 border" style="min-height: 38px;" title="Reset">
                        <i class="ri ri-refresh-line me-1"></i>
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
                    @include('reports.purchase_reports._po_supplier_wise', ['qtyLabel' => 'Qty'])
                </div>

                <!-- 2. Stock Report -->
                <div class="tab-pane fade" id="stock-report" role="tabpanel">
                    @include('reports.purchase_reports._stock_report', ['isFabric' => false])
                </div>

                <!-- 3. Stock Ageing -->
                <div class="tab-pane fade" id="ageing-report" role="tabpanel">
                    @include('reports.purchase_reports._ageing_report', ['isFabric' => false])
                </div>
                
                <div class="tab-pane fade" id="cost-report" role="tabpanel">
                    @include('reports.purchase_reports._cost_report')
                </div>
                <div class="tab-pane fade" id="minstock-report" role="tabpanel">
                    @include('reports.purchase_reports._minstock_report', ['isFabric' => false])
                </div>
                <div class="tab-pane fade" id="return-report" role="tabpanel">
                    @include('reports.purchase_reports._return_report', ['isFabric' => false])
                </div>
                <div class="tab-pane fade" id="performance-report" role="tabpanel">
                    @include('reports.purchase_reports._supplier_performance')
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
@endsection

@section('scripts')
<script>
var seenFooterNodes = [];
var footerColspans = [];

function getExcelColumnLetter(colIdx) {
    var str = '';
    while (colIdx >= 0) {
        str = String.fromCharCode((colIdx % 26) + 65) + str;
        colIdx = Math.floor(colIdx / 26) - 1;
    }
    return str;
}

function formatReportBodyCell(data) {
    if (typeof data === 'string') {
        var temp = $('<div>').html(data);
        temp.find('.no-export, .d-none, button, i, script').remove();
        return temp.text().trim();
    }
    return data;
}

function formatReportFooterCell(data, columnIdx, node) {
    if (columnIdx === 0) {
        seenFooterNodes = [];
        footerColspans = [];
    }
    if (node) {
        var colspan = parseInt($(node).attr('colspan') || 1, 10);
        if (seenFooterNodes.indexOf(node) !== -1) {
            return '';
        }
        seenFooterNodes.push(node);
        if (colspan > 1) {
            footerColspans.push({
                startCol: columnIdx,
                endCol: columnIdx + colspan - 1
            });
        }
    }
    if (typeof data === 'string') {
        var temp = $('<div>').html(data);
        temp.find('.no-export, .d-none, button, i, script').remove();
        return temp.text().trim();
    }
    return data;
}

function customizeReportExcel(xlsx) {
    var sheet = xlsx.xl.worksheets['sheet1.xml'];
    var $lastRow = $('row:last', sheet);
    var rowNum = $lastRow.attr('r');

    if (rowNum && footerColspans && footerColspans.length > 0) {
        var $mergeCells = $('mergeCells', sheet);
        if (!$mergeCells.length) {
            $('sheetData', sheet).after('<mergeCells count="0"/>');
            $mergeCells = $('mergeCells', sheet);
        }
        footerColspans.forEach(function (span) {
            var startRef = getExcelColumnLetter(span.startCol) + rowNum;
            var endRef = getExcelColumnLetter(span.endCol) + rowNum;
            var mergeEl = xlsx.xl.worksheets['sheet1.xml'].createElement('mergeCell');
            mergeEl.setAttribute('ref', startRef + ':' + endRef);
            $mergeCells[0].appendChild(mergeEl);
        });
        $mergeCells.attr('count', $mergeCells.find('mergeCell').length);
    }
}

$.extend(true, $.fn.dataTable.defaults, {
    processing: true,
    buttons: [
        {
            extend: 'excel',
            className: 'buttons-excel d-none',
            footer: true,
            title: function () {
                var title = $('#active_report_title').text().trim() || 'Accessories Store Report';
                return title.replace(/[^\w\s\-_]/gi, '').trim();
            },
            exportOptions: {
                columns: ':visible:not(.no-export)',
                format: {
                    body: formatReportBodyCell,
                    footer: formatReportFooterCell
                }
            },
            customize: customizeReportExcel
        },
        {
            extend: 'pdf',
            className: 'buttons-pdf d-none',
            footer: true,
            title: function () {
                var title = $('#active_report_title').text().trim() || 'Accessories Store Report';
                return title.replace(/[^\w\s\-_]/gi, '').trim();
            },
            orientation: 'landscape',
            pageSize: 'A4',
            exportOptions: {
                columns: ':visible:not(.no-export)'
            }
        },
        {
            extend: 'print',
            className: 'buttons-print d-none',
            footer: true,
            title: function () {
                var title = $('#active_report_title').text().trim() || 'Accessories Store Report';
                return title.replace(/[^\w\s\-_]/gi, '').trim();
            },
            exportOptions: {
                columns: ':visible:not(.no-export)'
            }
        }
    ]
});

$(document).ready(function() {
    function fetchReport() {
        const form = $('#accessoriesReportForm');
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
        $('#active_report_title').html(selectedText);

        $('.tab-pane').removeClass('show active');
        $('#' + targetTabId).addClass('show active');

        let activeTable = $('#' + targetTabId).find('table').first();
        if (activeTable.length && $.fn.DataTable.isDataTable(activeTable[0])) {
            let dt = activeTable.DataTable();
            if (dt && dt.ajax && typeof dt.ajax.reload === 'function' && dt.ajax.url()) {
                dt.ajax.reload();
            }
        }
    });

    $('#accessoriesReportForm').on('submit', function(e) {
        e.preventDefault();
        let currentTabId = $('#report_type_select').val() || $('.tab-pane.active').attr('id');

        const submitBtn = $(this).find('button[type="submit"]');
        const origHtml = submitBtn.html();
        submitBtn.html('<span class="spinner-border spinner-border-sm"></span>').prop('disabled', true);

        if (typeof renderPoSupplierLevel === 'function') {
            renderPoSupplierLevel();
        }
        if (typeof renderStockLevel1 === 'function') {
            renderStockLevel1();
        }

        let activeTable = $('#' + currentTabId).find('table').first();
        if (activeTable.length && $.fn.DataTable.isDataTable(activeTable[0])) {
            let dt = activeTable.DataTable();
            if (dt && dt.ajax && typeof dt.ajax.reload === 'function' && dt.ajax.url()) {
                dt.ajax.reload(function() {
                    submitBtn.html(origHtml).prop('disabled', false);
                }, false);
                return;
            }
        }
        submitBtn.html(origHtml).prop('disabled', false);
    });

    $('#btn-reset-filters').on('click', function(e) {
        e.preventDefault();
        $('#accessories_store_date_range').val('');
        if ($('#accessories_store_date_range')[0] && $('#accessories_store_date_range')[0]._flatpickr) {
            $('#accessories_store_date_range')[0]._flatpickr.clear();
        }
        $('.start_date, .end_date').val('');
        $('#supplier_id, select[name="supplier_id"]').val('').trigger('change.select2');
        $('#brand_id, select[name="brand_id"]').val('').trigger('change.select2');

        let currentTabId = $('#report_type_select').val() || $('.tab-pane.active').attr('id');
        let activeTab = $('#' + currentTabId);

        // Clear quick search box inside DataTable if typed
        activeTab.find('.dataTables_filter input').val('');

        let activeTable = activeTab.find('table').first();
        if (activeTable.length && $.fn.DataTable.isDataTable(activeTable[0])) {
            let dt = activeTable.DataTable();
            dt.search('');
        }

        $('#accessoriesReportForm').trigger('submit');
    });

    // Unified Export Handler for Excel, PDF and Print
    function triggerAccessoriesExport(buttonClass, $triggerBtn) {
        var $activeTab = $('.tab-pane.active');
        if (!$activeTab.length) {
            $activeTab = $('#' + $('#report_type_select').val());
        }

        var targetDt = null;
        var tables = $activeTab.find('table:visible');
        if (!tables.length) {
            tables = $activeTab.find('.table:visible, table');
        }

        tables.each(function() {
            if ($.fn.DataTable.isDataTable(this)) {
                var dt = $(this).DataTable();
                if (dt.button && dt.button(buttonClass).length) {
                    targetDt = dt;
                    return false;
                }
            }
        });

        if (!targetDt) {
            tables.each(function() {
                if ($.fn.DataTable.isDataTable(this)) {
                    targetDt = $(this).DataTable();
                    return false;
                }
            });
        }

        if (!targetDt) {
            var $anyTbl = $activeTab.find('table').first();
            if ($anyTbl.length && $.fn.DataTable.isDataTable($anyTbl[0])) {
                targetDt = $anyTbl.DataTable();
            }
        }

        if (!targetDt) return;

        // Ensure buttons exist on the target instance (fallback)
        if (!targetDt.button || !targetDt.button(buttonClass).length) {
            new $.fn.dataTable.Buttons(targetDt, {
                buttons: [
                    {
                        extend: 'excel',
                        className: 'buttons-excel d-none',
                        footer: true,
                        title: function () {
                            var title = $('#active_report_title').text().trim() || 'Accessories Store Report';
                            return title.replace(/[^\w\s\-_]/gi, '').trim();
                        },
                        exportOptions: {
                            columns: ':visible:not(.no-export)',
                            format: {
                                body: formatReportBodyCell,
                                footer: formatReportFooterCell
                            }
                        },
                        customize: customizeReportExcel
                    },
                    {
                        extend: 'pdf',
                        className: 'buttons-pdf d-none',
                        footer: true,
                        title: function () {
                            var title = $('#active_report_title').text().trim() || 'Accessories Store Report';
                            return title.replace(/[^\w\s\-_]/gi, '').trim();
                        },
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: { columns: ':visible:not(.no-export)' }
                    },
                    {
                        extend: 'print',
                        className: 'buttons-print d-none',
                        footer: true,
                        title: function () {
                            var title = $('#active_report_title').text().trim() || 'Accessories Store Report';
                            return title.replace(/[^\w\s\-_]/gi, '').trim();
                        },
                        exportOptions: { columns: ':visible:not(.no-export)' }
                    }
                ]
            });
        }

        var isServerSide = (targetDt.settings()[0] && targetDt.settings()[0].oFeatures && targetDt.settings()[0].oFeatures.bServerSide);

        if (isServerSide) {
            var origLen = targetDt.page.len();
            var origBtnHtml = $triggerBtn ? $triggerBtn.html() : '';
            if ($triggerBtn) {
                $triggerBtn.html('<span class="spinner-border spinner-border-sm me-1"></span> Exporting...').prop('disabled', true);
            }

            targetDt.one('draw', function() {
                if ($triggerBtn) {
                    $triggerBtn.html(origBtnHtml).prop('disabled', false);
                }
                if (targetDt.button && targetDt.button(buttonClass).length) {
                    targetDt.button(buttonClass).trigger();
                }
                setTimeout(function() {
                    targetDt.page.len(origLen).draw();
                }, 300);
            });
            targetDt.page.len(-1).draw();
        } else {
            if (targetDt.button && targetDt.button(buttonClass).length) {
                targetDt.button(buttonClass).trigger();
            }
        }
    }

    // Export Handlers
    $('#btn-excel').on('click', function(e) {
        e.preventDefault();
        triggerAccessoriesExport('.buttons-excel', $(this));
    });
    $('#btn-pdf').on('click', function(e) {
        e.preventDefault();
        triggerAccessoriesExport('.buttons-pdf', $(this));
    });
    $('#btn-print').on('click', function(e) {
        e.preventDefault();
        triggerAccessoriesExport('.buttons-print', $(this));
    });
});
</script>
@endsection

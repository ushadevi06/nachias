@extends('layouts.common')
@section('title', 'Warehouse Report - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <!-- Header Section -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-primary">Warehouse Report</h4>
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
            <form id="warehouseReportForm" class="row g-3 align-items-end" method="GET" action="{{ url('warehouse_reports') }}" onsubmit="return false;">
                <div class="col-12 col-md-4 col-xl-2">
                    <label class="form-label small fw-bold text-primary"><i class="ri-file-chart-line me-1"></i>Select Report Type</label>
                    <select class="form-select select2" id="report_type_select" name="report_type">
                        <option value="warehouse-summary" selected>🏬 Warehouse Summary</option>
                        <option value="brand-sales">📊 Brandwise Sales</option>
                        <option value="brand-stock">📦 Brandwise Stock</option>
                        <option value="assorted-stock">🏷️ Assorted Stock</option>
                        <option value="order-dispatch">🔄 Order vs Dispatch</option>
                        <option value="urgent-orders">🔥 Urgent Orders</option>
                        <option value="dispatch">🚚 Dispatch</option>
                        <option value="inward">📥 Stock Inward</option>
                        <option value="discount">🏷️ Regular / Discount</option>
                        <option value="brandwise-lost-sales">❌ Brandwise Lost Sales</option>
                        <option value="order-processing-time">⏱️ Order Processing Time</option>
                        <option value="stock-inward-sales">📊 Stock Inward & Sales</option>
                        <option value="brandwise-completion">📈 Brandwise Completion</option>
                    </select>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <label class="form-label small fw-bold text-muted">From Date</label>
                    <input type="text" class="form-control start_date" name="from_date" value="{{ request('from_date') }}" placeholder="DD-MM-YYYY">
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <label class="form-label small fw-bold text-muted">To Date</label>
                    <input type="text" class="form-control end_date" name="to_date" value="{{ request('to_date') }}" placeholder="DD-MM-YYYY">
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <label class="form-label small fw-bold text-muted">Brand</label>
                    <select class="form-select select2" name="brand_id" data-placeholder="Select Brand">
                        <option value=""></option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->brand_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <label class="form-label small fw-bold text-muted">Customer</label>
                    <select class="form-select select2" name="customer_id" id="customer_id_select" data-placeholder="Select Customer">
                        <option value=""></option>
                        @foreach($customers ?? [] as $customer)
                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-4 col-xl-2" id="warehouse_filter_col" style="{{ request('report_type') === 'stock-inward-sales' ? '' : 'display: none;' }}">
                    <label class="form-label small fw-bold text-muted">Warehouse</label>
                    <select class="form-select select2" name="warehouse_id" id="filter_warehouse_id">
                        <option value="all" {{ (request('warehouse_id') == 'all' || empty(request('warehouse_id'))) ? 'selected' : '' }}>All</option>
                        @foreach($warehouses ?? [] as $wh)
                            <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->warehouse_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4 col-xl-2 d-flex gap-1 align-items-end">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">
                        <i class="ri ri-search-line me-1"></i> Search
                    </button>
                    <button type="button" id="btn-reset-report" class="btn btn-outline-light rounded-pill border">
                        <i class="ri ri-refresh-line"></i>
                    </button>
                </div>
                <div class="d-none">
                    <select class="form-select" name="store_id">
                        <option value=""></option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Content Card -->
    <div class="card shadow-sm border-0 premium-content-card">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-bold text-primary" id="active_report_title">
                🏬 Warehouse Summary Report
            </h5>
            <div class="d-flex align-items-center gap-2">
                <div id="top_warehouse_wrapper">
                    <select id="top_warehouse_select" class="form-select form-select-sm border-primary text-primary fw-bold" style="width: 240px;">
                        @foreach($warehouses ?? [] as $wh)
                            <option value="{{ $wh->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $wh->warehouse_name }}</option>
                        @endforeach
                    </select>
                </div>
                <span class="badge bg-label-primary px-3 py-2 rounded-pill" id="active_report_badge">Warehouse Analytics</span>
            </div>
        </div>
        <div class="card-body py-4">
            <style>
                .card-datatable, div.dataTables_wrapper {
                    position: relative !important;
                    min-height: 200px !important;
                }
            </style>
            <div class="tab-content">
                <!-- 0. Warehouse Summary Report -->
                <div class="tab-pane fade show active" id="warehouse-summary" role="tabpanel">
                    @include('reports.warehouse_report.warehouse_summary', ['reportData' => [], 'totals' => []])
                </div>

                <!-- 1. Brandwise Sales Report -->
                <div class="tab-pane fade" id="brand-sales" role="tabpanel">
                    @include('reports.warehouse_report.brandwise_sales')
                </div>

                <!-- 2. Brandwise Stock Report (Setwise only) -->
                <div class="tab-pane fade" id="brand-stock" role="tabpanel">
                    @include('reports.warehouse_report.brandwise_stock')
                </div>

                <!-- 3. Assorted Stock Report (Single Store) -->
                <div class="tab-pane fade" id="assorted-stock" role="tabpanel">
                    @include('reports.warehouse_report.assorted_stock')
                </div>

                <!-- 4. Order (vs) Dispatch Report -->
                <div class="tab-pane fade" id="order-dispatch" role="tabpanel">
                    @include('reports.warehouse_report.order_vs_dispatch')
                </div>

                <!-- Urgent Orders Monitor -->
                <div class="tab-pane fade" id="urgent-orders" role="tabpanel">
                    @include('reports.warehouse_report.urgent_orders')
                </div>

                <!-- 5. Sales Return Report -->
                <div class="tab-pane fade" id="sales-return" role="tabpanel">
                    @include('reports.warehouse_report.sales_return')
                </div>

                <!-- 6. White & Dhoti Itemwise Sales Report -->
                <div class="tab-pane fade" id="white-dhoti" role="tabpanel">
                    @include('reports.warehouse_report.white_dhoti')
                </div>

                <!-- 7. Dispatch Report -->
                <div class="tab-pane fade" id="dispatch" role="tabpanel">
                    @include('reports.warehouse_report.dispatch_report')
                </div>

                <!-- 8. Stock Inward Report -->
                <div class="tab-pane fade" id="inward" role="tabpanel">
                    @include('reports.warehouse_report.stock_inward')
                </div>

                <!-- 9. Regular Sales & Discount Sales Report -->
                <div class="tab-pane fade" id="discount" role="tabpanel">
                    @include('reports.warehouse_report.regular_discount')
                </div>

                <!-- 12. Brandwise Lost Sales Report -->
                <div class="tab-pane fade" id="brandwise-lost-sales" role="tabpanel">
                    @include('reports.warehouse_report.brandwise_lost_sales')
                </div>

                <!-- 13. Order Processing Time Report -->
                <div class="tab-pane fade" id="order-processing-time" role="tabpanel">
                    @include('reports.warehouse_report.order_processing_time')
                </div>

                <!-- Stock Inward & Sales Report -->
                <div class="tab-pane fade" id="stock-inward-sales" role="tabpanel">
                    @include('reports.warehouse_report.stock_inward_sales')
                </div>

                <!-- Brandwise Completion Report -->
                <div class="tab-pane fade" id="brandwise-completion" role="tabpanel">
                    @include('reports.warehouse_report.brandwise_completion')
                </div>

                <!-- 10. Priority Stock Report (Above 90 Days) -->
                <div class="tab-pane fade" id="priority" role="tabpanel">
                    @include('reports.warehouse_report.priority_stock')
                </div>

                <!-- 11. Damage Sales Report -->
                <div class="tab-pane fade" id="damage" role="tabpanel">
                    @include('reports.warehouse_report.damage_sales_split')
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
        font-size: 0.82rem;
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

    .badge.bg-label-success { background: #dcfce7; color: #166534; }
    .badge.bg-label-warning { background: #fef9c3; color: #854d0e; }
    .badge.bg-label-danger { background: #fee2e2; color: #991b1b; }
    .badge.bg-label-info { background: #e0f2fe; color: #0369a1; }

</style>
@endsection

@section('scripts')
<script>
$.extend(true, $.fn.dataTable.defaults, {
    processing: true,
});

$(document).ready(function() {
    function syncReportFilterVisibility() {
        let targetTabId = $('#report_type_select').val();

        if (targetTabId === 'warehouse-summary') {
            $('#top_warehouse_wrapper').show();
        } else {
            $('#top_warehouse_wrapper').hide();
        }

        if (targetTabId === 'stock-inward-sales') {
            $('#warehouse_filter_col').show();
        } else {
            $('#warehouse_filter_col').hide();
        }
    }

    $('#report_type_select').on('change', function() {
        let targetTabId = $(this).val();
        let selectedText = $(this).find('option:selected').text();
        
        $('#active_report_title').html(selectedText + ' Report');

        syncReportFilterVisibility();

        $('.tab-pane').removeClass('show active');
        $('#' + targetTabId).addClass('show active');

        $('#warehouseReportForm').trigger('submit');
    });

    syncReportFilterVisibility();

    $('#top_warehouse_select').on('change', function() {
        $('#warehouseReportForm').trigger('submit');
    });

    $.fn.dataTable.ext.errMode = 'none';

    window.showWarehouseReportLoading = function(isLoading) {
        let loader = $('#report_loader');
        if (isLoading) {
            if (!loader.length) {
                $('#active_report_title').append(' <div class="spinner-border spinner-border-sm text-primary ms-2" id="report_loader" role="status"></div>');
            }
            $('.tab-content').css({ 'opacity': '0.5', 'transition': 'opacity 0.2s ease-in-out' });
        } else {
            loader.remove();
            $('.tab-content').css({ 'opacity': '1.0' });
        }
    };
    function showWarehouseReportLoading(isLoading) {
        window.showWarehouseReportLoading(isLoading);
    }

    $('#warehouseReportForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        if (!submitBtn.data('original-html')) {
            submitBtn.data('original-html', submitBtn.html());
        }
        const originalBtnHtml = submitBtn.data('original-html');

        submitBtn.html('<span class="spinner-border spinner-border-sm me-1"></span> Searching...').prop('disabled', true);
        showWarehouseReportLoading(true);

        let activeTabId = $('.tab-pane.active').attr('id') || $('#report_type_select').val() || 'warehouse-summary';

        if (activeTabId === 'warehouse-summary') {
            let formData = form.serializeArray();
            formData.push({ name: 'active_tab', value: activeTabId });
            formData.push({ name: 'warehouse_id', value: $('#top_warehouse_select').val() });

            $.ajax({
                url: form.attr('action'),
                method: 'GET',
                data: $.param(formData),
                dataType: 'json',
                success: function(response) {
                    if (response && response['warehouse-summary']) {
                        $('#warehouse-summary').html(response['warehouse-summary']);
                    }
                    if (typeof initWarehouseSummaryTable === 'function') initWarehouseSummaryTable();
                },
                error: function() {
                    alert('An error occurred while fetching the summary.');
                },
                complete: function() {
                    submitBtn.html(originalBtnHtml).prop('disabled', false);
                    showWarehouseReportLoading(false);
                }
            });
        } else {
            var activeTable = $('#' + activeTabId).find('.datatables-products:visible');
            var isRealAjaxTable = false;

            if (activeTable.length && $.fn.DataTable.isDataTable(activeTable[0])) {
                var dtInstance = activeTable.DataTable();
                if (dtInstance.ajax && typeof dtInstance.ajax.reload === 'function' && dtInstance.ajax.url()) {
                    isRealAjaxTable = true;
                }
            }

            if (isRealAjaxTable) {
                activeTable.DataTable().ajax.reload(function() {
                    submitBtn.html(originalBtnHtml).prop('disabled', false);
                    showWarehouseReportLoading(false);
                }, false);
            } else {
                handleTabActivation('#' + activeTabId);
                setTimeout(function() {
                    submitBtn.html(originalBtnHtml).prop('disabled', false);
                    showWarehouseReportLoading(false);
                }, 400);
            }
        }
    });

    // Auto-trigger initial search on load
    $('#warehouseReportForm').trigger('submit');

    $('#btn-excel').on('click', function() {
        $('.tab-pane.active .datatables-products').DataTable().button('.buttons-excel').trigger();
    });
    $('#btn-pdf').on('click', function() {
        $('.tab-pane.active .datatables-products').DataTable().button('.buttons-pdf').trigger();
    });
    $('#btn-print').on('click', function() {
        $('.tab-pane.active .datatables-products').DataTable().button('.buttons-print').trigger();
    });

    $(document).on('click', '#btn-reset-report', function(e) {
        e.preventDefault();

        $('.start_date').val('');
        $('.end_date').val('');
        $('select[name="brand_id"]').val('').trigger('change');
        $('select[name="customer_id"]').val('').trigger('change');
        $('select[name="store_id"]').val('').trigger('change');
        $('select[name="warehouse_id"]').val('all').trigger('change');

        let activeTabId = $('.tab-pane.active').attr('id') || $('#report_type_select').val() || 'brand-sales';

        if (activeTabId === 'stock-inward-sales' && typeof renderStockInwardSalesBrandLevel === 'function') {
            renderStockInwardSalesBrandLevel();
        } else if (activeTabId === 'brandwise-lost-sales' && typeof renderLostSalesBrandLevel === 'function') {
            renderLostSalesBrandLevel();
        } else if (activeTabId === 'order-processing-time' && typeof renderOrderProcessingTimeRootLevel === 'function') {
            renderOrderProcessingTimeRootLevel();
        } else if (activeTabId === 'brandwise-completion' && typeof renderCompletionRootLevel === 'function') {
            renderCompletionRootLevel();
        } else if ((activeTabId === 'brand-stock' || activeTabId === 'brandwise-stock') && typeof renderBrandLevel === 'function') {
            renderBrandLevel();
        }

        $('#warehouseReportForm').trigger('submit');
    });

    function handleTabActivation(targetId) {
        if (!targetId) return;
        var activeTabId = targetId.replace('#', '');
        
        switch (activeTabId) {
            case 'brand-sales':
            case 'brandwise-sales':
                if (typeof window.initBrandwiseSalesTable === 'function') window.initBrandwiseSalesTable();
                else if (typeof initBrandwiseSalesTable === 'function') initBrandwiseSalesTable();
                break;
            case 'brand-stock':
            case 'brandwise-stock':
                if (typeof window.initBrandwiseStockTable === 'function') window.initBrandwiseStockTable();
                else if (typeof initBrandwiseStockTable === 'function') initBrandwiseStockTable();
                break;
            case 'assorted-stock':
                if (typeof window.initAssortedStockTable === 'function') window.initAssortedStockTable();
                else if (typeof initAssortedStockTable === 'function') initAssortedStockTable();
                break;
            case 'order-dispatch':
            case 'order-vs-dispatch':
                if (typeof window.initOrderVsDispatchTable === 'function') window.initOrderVsDispatchTable();
                else if (typeof initOrderVsDispatchTable === 'function') initOrderVsDispatchTable();
                break;
            case 'urgent-orders':
                if (typeof window.initUrgentOrdersTable === 'function') window.initUrgentOrdersTable();
                else if (typeof initUrgentOrdersTable === 'function') initUrgentOrdersTable();
                break;
            case 'dispatch':
            case 'dispatch-report':
                if (typeof window.initDispatchReportTable === 'function') window.initDispatchReportTable();
                else if (typeof initDispatchReportTable === 'function') initDispatchReportTable();
                break;
            case 'inward':
            case 'stock-inward':
                if (typeof window.initStockInwardTable === 'function') window.initStockInwardTable();
                else if (typeof initStockInwardTable === 'function') initStockInwardTable();
                break;
            case 'discount':
            case 'regular-discount':
                if (typeof window.initRegularDiscountTable === 'function') window.initRegularDiscountTable();
                else if (typeof initRegularDiscountTable === 'function') initRegularDiscountTable();
                break;
            case 'brandwise-lost-sales':
                if (typeof window.initBrandwiseLostSalesTable === 'function') window.initBrandwiseLostSalesTable();
                else if (typeof initBrandwiseLostSalesTable === 'function') initBrandwiseLostSalesTable();
                break;
            case 'order-processing-time':
                if (typeof window.initOrderProcessingTime === 'function') window.initOrderProcessingTime();
                else if (typeof initOrderProcessingTime === 'function') initOrderProcessingTime();
                break;
            case 'stock-inward-sales':
                if (typeof window.initStockInwardSalesTable === 'function') window.initStockInwardSalesTable();
                else if (typeof initStockInwardSalesTable === 'function') initStockInwardSalesTable();
                break;
            case 'brandwise-completion':
                if (typeof window.initBrandwiseCompletionTable === 'function') window.initBrandwiseCompletionTable();
                else if (typeof initBrandwiseCompletionTable === 'function') initBrandwiseCompletionTable();
                break;
        }

        var $table = $(targetId).find('.datatables-products');
        if ($table.length > 0) {
            $table.each(function() {
                if ($.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable().columns.adjust().responsive.recalc();
                }
            });
        }
    }

    $(document).on('shown.bs.tab', '[data-bs-toggle="tab"]', function (e) {
        var $btn = $(e.target).closest('[data-bs-toggle="tab"]');
        var targetId = $btn.attr('data-bs-target') || $btn.attr('href');
        handleTabActivation(targetId);
    });

    $(document).on('click', '[data-bs-toggle="tab"]', function (e) {
        var $btn = $(this).closest('[data-bs-toggle="tab"]');
        var targetId = $btn.attr('data-bs-target') || $btn.attr('href');
        setTimeout(function() {
            handleTabActivation(targetId);
        }, 50);
    });
});
</script>
@endsection

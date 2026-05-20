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
            <form id="warehouseReportForm" class="row g-3 align-items-end" method="GET" action="{{ url('warehouse_reports') }}">
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">From Date</label>
                    <input type="text" class="form-control start_date" name="from_date" value="{{ request('from_date') }}" placeholder="DD-MM-YYYY">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">To Date</label>
                    <input type="text" class="form-control end_date" name="to_date" value="{{ request('to_date') }}" placeholder="DD-MM-YYYY">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Brand</label>
                    <select class="form-select select2" name="brand_id" data-placeholder="Select Brand">
                        <option value=""></option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->brand_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Store</label>
                    <select class="form-select select2" name="store_id" data-placeholder="Select Store">
                        <option value=""></option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>{{ $store->store_location }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Item</label>
                    <select class="form-select select2" name="item_id" data-placeholder="Select Item">
                        <option value=""></option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">
                        <i class="ri ri-search-line me-1"></i> Search
                    </button>
                    <a href="{{ url('warehouse_reports') }}" class="btn btn-outline-light w-100 rounded-pill border">
                        <i class="ri ri-refresh-line me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabs Interface -->
    <div class="card shadow-sm border-0 premium-content-card">
        <div class="card-header bg-white border-bottom-0 p-0">
            <ul class="nav nav-tabs nav-fill premium-nav-tabs" id="warehouseTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#brand-sales" type="button" role="tab">Brandwise Sales</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#brand-stock" type="button" role="tab">Brandwise Stock</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#assorted-stock" type="button" role="tab">Assorted Stock</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#order-dispatch" type="button" role="tab">Order vs Dispatch</button>
                </li>
                {{-- <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#sales-return" type="button" role="tab">Sales Return</button>
                </li>
                <li class="nav-item d-none d-xl-block" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#white-dhoti" type="button" role="tab">White & Dhoti</button>
                </li> --}}
                <li class="nav-item d-none d-xl-block" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#dispatch" type="button" role="tab">Dispatch Report</button>
                </li>
                <li class="nav-item d-none d-xl-block" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#inward" type="button" role="tab">Stock Inward</button>
                </li>
                <li class="nav-item d-none d-xl-block" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#discount" type="button" role="tab">Regular/Discount</button>
                </li>
                {{-- <li class="nav-item d-none d-xl-block" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#priority" type="button" role="tab">Priority Stock</button>
                </li>
                <li class="nav-item d-none d-xl-block" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#damage" type="button" role="tab">Damage Sales</button>
                </li> --}}
                <li class="nav-item dropdown d-xl-none">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button">More</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#white-dhoti" data-bs-toggle="tab">White & Dhoti</a></li>
                        <li><a class="dropdown-item" href="#dispatch" data-bs-toggle="tab">Dispatch Report</a></li>
                        <li><a class="dropdown-item" href="#inward" data-bs-toggle="tab">Stock Inward</a></li>
                        <li><a class="dropdown-item" href="#discount" data-bs-toggle="tab">Regular/Discount</a></li>
                        <li><a class="dropdown-item" href="#priority" data-bs-toggle="tab">Priority Stock</a></li>
                        <li><a class="dropdown-item" href="#damage" data-bs-toggle="tab">Damage Sales</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="card-body py-4">
            <div class="tab-content">
                <!-- 1. Brandwise Sales Report -->
                <div class="tab-pane fade show active" id="brand-sales" role="tabpanel">
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

    /* Badge Customization */
    .badge.bg-label-success { background: #dcfce7; color: #166534; }
    .badge.bg-label-warning { background: #fef9c3; color: #854d0e; }
    .badge.bg-label-danger { background: #fee2e2; color: #991b1b; }
    .badge.bg-label-info { background: #e0f2fe; color: #0369a1; }

</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#warehouseReportForm').on('submit', function(e) {
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

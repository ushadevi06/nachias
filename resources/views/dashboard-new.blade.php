@extends('layouts.common')
@section('title', 'Dashboard - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-primary">ERP Dashboard</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item active">Dashboard Overview</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- MODERN SEGMENTED DASHBOARD TABS -->
    <div class="mb-4">
        <ul class="nav nav-pills dashboard-nav-pills p-2 bg-white rounded-3 shadow-sm border flex-nowrap overflow-auto" id="dashboardTabs" role="tablist" style="scrollbar-width: thin; gap: 8px;">
            <!-- Tab 1: Executive Overview -->
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold px-3 py-2 text-nowrap" id="tab-executive-tab" data-bs-toggle="pill" data-bs-target="#tab-executive" type="button" role="tab" aria-controls="tab-executive" aria-selected="true">
                    <i class="ri ri-dashboard-line me-1 text-primary"></i> Executive Overview
                </button>
            </li>

            <!-- Tab 2: Fabric & Stores -->
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-3 py-2 text-nowrap" id="tab-fabric-stores-tab" data-bs-toggle="pill" data-bs-target="#tab-fabric-stores" type="button" role="tab" aria-controls="tab-fabric-stores" aria-selected="false">
                    <i class="ri ri-store-2-line me-1 text-info"></i> Fabric & Stores
                    @if(isset($fabric_shortage_count) && $fabric_shortage_count > 0)
                        <span class="badge bg-warning text-dark ms-1">{{ $fabric_shortage_count }} Short</span>
                    @endif
                </button>
            </li>

            <!-- Tab 3: Core Material Planner -->
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-3 py-2 text-nowrap" id="tab-core-planner-tab" data-bs-toggle="pill" data-bs-target="#tab-core-planner" type="button" role="tab" aria-controls="tab-core-planner" aria-selected="false">
                    <i class="ri ri-stack-line me-1 text-warning"></i> Material Planner
                </button>
            </li>

            <!-- Tab 4: Supplier Performance -->
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-3 py-2 text-nowrap" id="tab-suppliers-tab" data-bs-toggle="pill" data-bs-target="#tab-suppliers" type="button" role="tab" aria-controls="tab-suppliers" aria-selected="false">
                    <i class="ri ri-truck-line me-1 text-success"></i> Supplier Reliability
                </button>
            </li>

            <!-- Tab 5: Production WIP -->
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-3 py-2 text-nowrap" id="tab-production-tab" data-bs-toggle="pill" data-bs-target="#tab-production" type="button" role="tab" aria-controls="tab-production" aria-selected="false">
                    <i class="ri ri-loader-line me-1 text-secondary"></i> Production WIP
                </button>
            </li>

            <!-- Tab 6: Maintenance & Compliance -->
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-3 py-2 text-nowrap" id="tab-maintenance-tab" data-bs-toggle="pill" data-bs-target="#tab-maintenance" type="button" role="tab" aria-controls="tab-maintenance" aria-selected="false">
                    <i class="ri ri-tools-line me-1 text-danger"></i> Maintenance & Compliance
                </button>
            </li>
        </ul>
    </div>

    <!-- TAB CONTENT CONTAINER -->
    <div class="tab-content" id="dashboardTabsContent">

        <!-- ==================== TAB 1: EXECUTIVE OVERVIEW ==================== -->
        <div class="tab-pane fade show active" id="tab-executive" role="tabpanel" aria-labelledby="tab-executive-tab">
            <!-- SECTION 1: SALES & ORDER DASHBOARD -->
            @if(auth()->id() == 1 || auth()->user()->can('view-sales-order dashboard'))
            <div class="mb-5">
        <div class="d-flex align-items-center mb-3">
            <div class="section-indicator bg-primary me-2"></div>
            <h5 class="fw-bold mb-0">Sales & Order Dashboard</h5>
        </div>

        
        <div class="row g-3">
            <div class="col-md-3 col-lg-3">
                <div class="card kpi-widget border-0 shadow-sm border-start border-primary border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">Sales (Today)</p>
                                <h4 class="mb-0 fw-bold">₹{{ number_format($sales_today, 2) }}</h4>
                                <span class="text-muted small">{{ $sales_count_today }} Invoices</span>
                            </div>
                            <div class="kpi-icon bg-light-primary">
                                <i class="ri ri-money-rupee-circle-line text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="card kpi-widget border-0 shadow-sm border-start border-info border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">Sales (Month)</p>
                                <h4 class="mb-0 fw-bold">₹{{ number_format($sales_month, 2) }}</h4>
                                <span class="text-muted small">{{ $sales_count_month }} Invoices</span>
                            </div>
                            <div class="kpi-icon bg-light-info">
                                <i class="ri ri-calendar-check-line text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="card kpi-widget border-0 shadow-sm border-start border-success border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">Sales (Year)</p>
                                <h4 class="mb-0 fw-bold">₹{{ number_format($sales_year, 2) }}</h4>
                                <span class="text-muted small">{{ $sales_count_year }} Invoices</span>
                            </div>
                            <div class="kpi-icon bg-light-success">
                                <i class="ri ri-line-chart-line text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="card kpi-widget border-0 shadow-sm border-start border-warning border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">Orders (Today)</p>
                                <h4 class="mb-0 fw-bold">{{ $orders_today }}</h4>
                                <span class="text-muted small">New Bookings</span>
                            </div>
                            <div class="kpi-icon bg-light-warning">
                                <i class="ri ri-shopping-bag-3-line text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="card kpi-widget border-0 shadow-sm border-start border-secondary border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">Orders (Month)</p>
                                <h4 class="mb-0 fw-bold">{{ number_format($orders_month) }}</h4>
                                <span class="text-muted small">Confirmed</span>
                            </div>
                            <div class="kpi-icon bg-light-secondary">
                                <i class="ri ri-archive-line text-secondary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Stock & Urgent -->
            <div class="col-md-3 col-lg-3">
                <div class="card kpi-widget border-0 shadow-sm border-start border-dark border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">Finished Stock</p>
                                <h4 class="mb-0 fw-bold">{{ number_format($total_stock) }}</h4>
                                <span class="text-muted small">Items in Hand</span>
                            </div>
                            <div class="kpi-icon bg-light-dark">
                                <i class="ri ri-stack-line text-dark"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="card kpi-widget border-0 shadow-sm border-start border-danger border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">Urgent Orders</p>
                                <h4 class="mb-0 fw-bold text-danger">{{ $urgent_orders }}</h4>
                                <span class="text-danger small fw-bold">Action Required</span>
                            </div>
                            <div class="kpi-icon bg-light-danger">
                                <i class="ri ri-error-warning-line text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-lg-3">
                <div class="card kpi-widget border-0 shadow-sm border-start border-warning border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">Missed Order Revenue</p>
                                <h4 class="mb-0 fw-bold text-warning">₹{{ number_format($year_missed_value, 2) }}</h4>
                                <span class="text-muted small">{{ number_format($year_missed_qty) }} Items Pending</span>
                            </div>
                            <div class="kpi-icon bg-light-warning">
                                <i class="ri ri-money-dollar-circle-line text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('reports.sales_summary_kpi')
    @endif

	<!-- SECTION 2: ATTENDANCE DASHBOARD -->
    @if(auth()->id() == 1 || auth()->user()->can('view-attendance dashboard'))
    <div class="row g-3 mb-4 mt-3">
        <div class="d-flex align-items-center mb-2">
            <div class="section-indicator bg-secondary me-2"></div>
            <h5 class="fw-bold mb-0">Employee's Attendance Dashboard</h5>
        </div>
        
        <div class="col-xl col-md-4 col-sm-6">
            <div class="card attendance-card border-0 shadow-sm" style="border-left: 4px solid #0d6efd;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="small text-muted mb-1">Total Employees</p>
                        <h2 class="fw-bold text-primary mb-0">
                            {{ $total_emp ?? 0 }}
                        </h2>
                    </div>
                    <div class="kpi-icon bg-light-primary">
                        <i class="ri ri-team-line text-primary"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl col-md-4 col-sm-6">
            <div class="card attendance-card present-card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="small text-muted mb-1">Present Today</p>
                        <h2 class="fw-bold text-success mb-0">
                            {{ $present_emp_today }}
                        </h2>
                    </div>

                    <div class="kpi-icon bg-light-success">
                        <i class="ri ri-user-smile-line text-success"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl col-md-4 col-sm-6">
            <div class="card attendance-card absent-card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="small text-muted mb-1">Absent Today</p>
                        <h2 class="fw-bold text-danger mb-0">
                            {{ $absent_emp_today }}
                        </h2>
                    </div>

                    <div class="kpi-icon bg-light-danger">
                        <i class="ri ri-user-unfollow-line text-danger"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl col-md-4 col-sm-6">
            <div class="card attendance-card late-card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="small text-muted mb-1">Late Employees</p>
                        <h2 class="fw-bold text-warning mb-0">
                            {{ $late_emp_today }}
                        </h2>
                    </div>

                    <div class="kpi-icon bg-light-warning">
                        <i class="ri ri-time-line text-warning"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl col-md-4 col-sm-6">
            <div class="card attendance-card overtime-card border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="small text-muted mb-1">Overtime</p>
                        <h2 class="fw-bold text-primary mb-0">
                            {{ $overtime_today }}
                        </h2>
                    </div>

                    <div class="kpi-icon bg-light-info">
                        <i class="ri ri-alarm-warning-line text-info"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>
    
    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-lg-6 col-md-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark"><i class="ri-pie-chart-2-fill text-danger me-2"></i>My Attendance Summary</h6>
                    <select id="attendanceDeviceFilter" class="form-select form-select-sm w-auto shadow-none">
                        <option value="All">All</option>
                        @foreach($dbDevices as $device)
                            <option value="{{ $device->device_name ?: $device->serial_number }}">{{ $device->device_name ?: $device->serial_number }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="card-body">
                    <div style="height: 300px; position: relative;">
                        <canvas id="attendanceSummaryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <!-- SECTION 3: ACCOUNTS DASHBOARD -->
    @if(auth()->id() == 1 || auth()->user()->can('view-accounts-financial dashboard'))
    <div class="mb-5">
        <div class="d-flex align-items-center mb-3">
            <div class="section-indicator bg-success me-2"></div>
            <h5 class="fw-bold mb-0">Accounts & Financial Dashboard</h5>
        </div>
        
        <!-- Financial KPI Grid -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card financial-stat border-0 shadow-sm p-3 border-top-primary">
                    <p class="text-muted small mb-1">Total Sales Val.</p>
                    <h5 class="mb-0 fw-bold">₹{{ formatIndianCurrency($total_sales_value) }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card financial-stat border-0 shadow-sm p-3 border-top-danger">
                    <p class="text-muted small mb-1">Total Sales Return</p>
                    <h5 class="mb-0 fw-bold text-danger">₹{{ formatIndianCurrency($sales_return) }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card financial-stat border-0 shadow-sm p-3 border-top-primary">
                    <p class="text-muted small mb-1">Total Debtors</p>
                    <h5 class="mb-0 fw-bold text-primary">₹{{ formatIndianCurrency($total_debtors) }}</h5>
                </div>
            </div>
             <div class="col-md-3">
                <div class="card financial-stat border-0 shadow-sm p-3 border-top-primary">
                    <p class="text-muted small mb-1">Bill Disc. ({{ $bill_discount_percent }}%)</p>
                    <h5 class="mb-0 fw-bold">₹{{ formatIndianCurrency($bill_discount) }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card financial-stat border-0 shadow-sm p-3 border-top-primary">
                    <p class="text-muted small mb-1">Cash Disc. ({{ $cash_discount_percent }}%)</p>
                    <h5 class="mb-0 fw-bold">₹{{ formatIndianCurrency($cash_discount) }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card financial-stat border-0 shadow-sm p-3 border-top-dark">
                    <p class="text-muted small mb-1">Total Purchase</p>
                    <h5 class="mb-0 fw-bold text-navy">₹{{ formatIndianCurrency($total_purchase) }}</h5>
                </div>
            </div>
             <div class="col-md-3">
                <div class="card financial-stat border-0 shadow-sm p-3 border-top-danger">
                    <p class="text-muted small mb-1">Purchase Return</p>
                    <h5 class="mb-0 fw-bold text-danger">₹{{ formatIndianCurrency($purchase_return) }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card financial-stat border-0 shadow-sm p-3 border-top-danger">
                    <p class="text-muted small mb-1">Total Creditors</p>
                    <h5 class="mb-0 fw-bold text-danger">₹{{ formatIndianCurrency($total_creditors) }}</h5>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Debtors Outstanding & Aging -->
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-primary"><i class="ri ri-team-line me-2"></i>Debtors Outstanding & Aging Report</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="small">Zone</th>
                                        <th class="small">Total Outstanding (₹)</th>
                                        <th class="small">0-30 Days</th>
                                        <th class="small">31-60 Days</th>
                                        <th class="small">61-90 Days</th>
                                        <th class="small">Above 90 Days</th>
                                    </tr>
                                </thead>
                                <tbody class="small">
                                    @if($debtors_aging->count() > 0)
                                        @foreach ($debtors_aging as $row)
                                        <tr>
                                            <td><strong>{{ $row->zone_name }}</strong></td>
                                            <td>{{ formatIndianCurrency($row->total_due) }}</td>
                                            <td class="text-success">{{ formatIndianCurrency($row->bucket_30) }}</td>
                                            <td>{{ formatIndianCurrency($row->bucket_60) }}</td>
                                            <td class="text-warning">{{ formatIndianCurrency($row->bucket_90) }}</td>
                                            <td class="text-danger fw-bold">{{ formatIndianCurrency($row->bucket_above_90) }}</td>
                                        </tr>
                                        @endforeach
                                    @else
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No pending debtors found for any zone.</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Creditors Outstanding & Aging -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-danger"><i class="ri ri-store-line me-2"></i>Creditors Outstanding & Aging Report</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="small">Supplier</th>
                                        <th class="small">Total Payable (₹)</th>
                                        <th class="small">0-30 Days</th>
                                        <th class="small">31-60 Days</th>
                                        <th class="small">61-90 Days</th>
                                        <th class="small">Above 90 Days</th>
                                    </tr>
                                </thead>
                                <tbody class="small">
                                    @if($creditors_aging->count() > 0)
                                        @foreach ($creditors_aging as $row)
                                        <tr>
                                            <td><strong>{{ $row->supplier_name }}</strong></td>
                                            <td>{{ formatIndianCurrency($row->total_due) }}</td>
                                            <td class="text-success">{{ formatIndianCurrency($row->bucket_30) }}</td>
                                            <td>{{ formatIndianCurrency($row->bucket_60) }}</td>
                                            <td class="text-warning">{{ formatIndianCurrency($row->bucket_90) }}</td>
                                            <td class="text-danger fw-bold">{{ formatIndianCurrency($row->bucket_above_90) }}</td>
                                        </tr>
                                        @endforeach
                                    @else
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No pending creditors found.</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Itemwise Stock Value -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="ri ri-pie-chart-line me-2"></i>Item-wise Stock Value</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 border-0">
                                <div><i class="ri ri-checkbox-blank-circle-fill text-primary me-2"></i>Fabric</div>
                                <span class="fw-bold">₹{{ formatIndianCurrency($fabric_value) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 border-0">
                                <div><i class="ri ri-checkbox-blank-circle-fill text-info me-2"></i>Accessories</div>
                                <span class="fw-bold">₹{{ formatIndianCurrency($accessories_value) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 border-0">
                                <div><i class="ri ri-checkbox-blank-circle-fill text-warning me-2"></i>WIP</div>
                                <span class="fw-bold">₹{{ formatIndianCurrency($wip_value) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 border-0">
                                <div><i class="ri ri-checkbox-blank-circle-fill text-success me-2"></i>Finished Goods</div>
                                <span class="fw-bold">₹{{ formatIndianCurrency($finished_goods_value) }}</span>
                            </li>
                        </ul>
                        <hr>
                        <div style="height: 200px;">
                            <canvas id="stockDistributionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comparison Widget -->
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 border-end">
                                <h6 class="fw-bold mb-3">Sales vs Collection (Month-wise)</h6>
                                <div style="height: 250px;">
                                    <canvas id="salesCollectionChart"></canvas>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3 ms-md-3">Purchase vs Payment (Month-wise)</h6>
                                <div style="height: 250px;" class="ms-md-3">
                                    <canvas id="purchasePaymentChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    </div> <!-- /#tab-executive -->

    <!-- ==================== TAB 5: PRODUCTION WIP ==================== -->
    <div class="tab-pane fade" id="tab-production" role="tabpanel" aria-labelledby="tab-production-tab">
        <!-- SECTION 3: PRODUCTION DASHBOARD -->
        @if(auth()->id() == 1 || auth()->user()->can('view-production dashboard'))
        <div class="mb-5">
        <div class="d-flex align-items-center mb-3">
            <div class="section-indicator bg-warning me-2"></div>
            <h5 class="fw-bold mb-0">Production Dashboard</h5>
        </div>
        
        <div class="row g-4">
            <!-- 1. Production WIP -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="ri ri-loader-line me-2"></i>Production WIP (Unit Wise)</h6>
                        <div class="search-box">
                            <input type="text" id="wipSearchInput" class="form-control form-control-sm" placeholder="Search Process...">
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="small">Process</th>
                                        <th class="small text-center">Opening</th>
                                        <th class="small text-center">Inward</th>
                                        <th class="small text-center">Outward</th>
                                        <th class="small text-center">WIP</th>
                                    </tr>
                                </thead>
                                <tbody class="small">
                                    @if($production_wip->count() > 0)
                                        @foreach($production_wip as $wip)
                                        <tr class="wip-row">
                                            <td class="wip-process-name"><strong>{{ $wip->operation_stage_name }}</strong></td>
                                            <td class="text-center">{{ number_format($wip->opening ?: 0, 2) }}</td>
                                            <td class="text-center text-success">{{ number_format($wip->inward ?: 0, 2) }}</td>
                                            <td class="text-center text-primary">{{ number_format($wip->outward ?: 0, 2) }}</td>
                                            <td class="text-center fw-bold">{{ number_format($wip->wip ?: 0, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    @else
                                     <tr><td colspan="5" class="text-center text-muted py-4">No WIP data available</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-0 py-2">
                        <p class="mb-0 x-small text-muted">
                            <strong>Note:</strong> 
                            1. Stages show if work is in progress or a task is assigned. 
                            2. Completed stages are hidden to keep the overview clean.
                        </p>
                    </div>
                </div>
            </div>

            <!-- 2. Production Cost -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="ri ri-money-rupee-circle-line me-2"></i>Production Cost (Unit Wise)</h6>
                        <div class="search-box">
                            <input type="text" id="costSearchInput" class="form-control form-control-sm" placeholder="Search Job Card...">
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <p class="text-muted small fw-bold mb-1">Total WIP Material Cost</p>
                        <h2 class="mb-3 fw-bold text-primary">₹{{ number_format($wip_value, 2) }}</h2>
                        <div class="table-responsive w-100 mt-2" style="max-height: 250px; overflow-y: auto;">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="x-small">Job Card</th>
                                        <th class="x-small text-end">Cost</th>
                                    </tr>
                                </thead>
                                <tbody class="x-small">
                                    @if($wip_cost_breakdown->count() > 0)
                                    @foreach($wip_cost_breakdown as $cost)
                                    <tr class="cost-row">
                                        <td class="cost-jc-no"><strong>{{ $cost->job_card_no }}</strong></td>
                                        <td class="text-end">₹{{ number_format($cost->total_cost, 2) }}</td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">No material costs recorded yet.</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Production Target Unit Wise -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="ri ri-focus-3-line me-2"></i>Production Target Status</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4 text-center">
                            <h3 class="fw-bold mb-1 text-primary">{{ $production_efficiency }}%</h3>
                            <p class="text-muted small uppercase fw-bold mb-0">Total Efficiency</p>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">Plan vs Achieved</span>
                                <span class="small fw-bold">{{ $production_achieved_qty ?: 0 }} / {{ $production_plan_qty ?: 0 }}</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $production_efficiency }}%"></div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <div class="bg-light-warning p-2 rounded w-100 text-center">
                                <p class="text-muted x-small mb-1">Pending Qty</p>
                                <h5 class="mb-0 fw-bold">{{ max(0, $production_plan_qty - ($production_achieved_qty ?: 0)) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Production Delivery Days Overdue -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100 border-top border-danger border-3">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-danger"><i class="ri ri-time-line me-2"></i>Delivery Days Overdue</h6>
                    </div>
                    <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                        <div class="list-group list-group-flush">
                            @if($delivery_overdue->count() > 0)
                                @foreach($delivery_overdue as $od)
                                <div class="list-group-item py-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small fw-bold">{{ $od->job_card_no }}</span>
                                        <span class="badge bg-soft-danger text-danger">{{ $od->overdue_days }} Days Overdue</span>
                                    </div>
                                    <p class="text-muted x-small mb-0">Target: {{ date('d-M-y', strtotime($od->delivery_date)) }} | Qty: {{ $od->grand_total_qty }}</p>
                                </div>
                                @endforeach
                            @else
                                <div class="list-group-item py-4 text-center text-muted">
                                    <i class="ri ri-checkbox-circle-line fs-2 text-success"></i><br>
                                    No Overdue Job Cards
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Process Wise Status -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="ri ri-flow-chart me-2"></i>Process Wise Status</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0 align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="small">Process</th>
                                        <th class="small text-center">Done</th>
                                        <th class="small text-center">IP</th>
                                        <th class="small text-center">Planned</th>
                                    </tr>
                                </thead>
                                <tbody class="small">
                                    @if($process_wise_status->count() > 0)
                                        @foreach($process_wise_status as $ps)
                                        <tr>
                                            <td>{{ $ps->operation_stage_name }}</td>
                                            <td class="text-center text-success fw-bold">{{ $ps->completed ?: 0 }}</td>
                                            <td class="text-center text-warning">{{ $ps->in_progress ?: 0 }}</td>
                                            <td class="text-center text-danger">{{ $ps->planned ?: 0 }}</td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr><td colspan="4" class="text-center text-muted py-2">No data</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    </div> <!-- /#tab-production -->

    <!-- ==================== TAB 2: FABRIC & STORES ==================== -->
    <div class="tab-pane fade" id="tab-fabric-stores" role="tabpanel" aria-labelledby="tab-fabric-stores-tab">
        <!-- SECTION: FABRIC & STORE STOCK DASHBOARD -->
        @if(auth()->id() == 1 || auth()->user()->can('view purchase-report') || auth()->user()->can('view warehouse-report') || auth()->user()->can('view stock-entry-raw-materials'))
        <div class="mb-5">
        <div class="d-flex align-items-center mb-3">
            <div class="section-indicator bg-info me-2"></div>
            <h5 class="fw-bold mb-0">Fabric & Store Stock Dashboard</h5>
        </div>

        <!-- KPI Grid -->
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="card kpi-widget border-0 shadow-sm border-start border-primary border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">Total Fabric Stock</p>
                                <h4 class="mb-0 fw-bold">{{ number_format($total_fabric_stock_qty, 2) }}</h4>
                                <span class="text-muted small">Meters in Store</span>
                            </div>
                            <div class="kpi-icon bg-light-primary">
                                <i class="ri ri-stack-line text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card kpi-widget border-0 shadow-sm border-start border-success border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">Total Stock Value</p>
                                <h4 class="mb-0 fw-bold">₹{{ formatIndianCurrency($total_fabric_stock_val) }}</h4>
                                <span class="text-muted small">Fabric Inventory Valuation</span>
                            </div>
                            <div class="kpi-icon bg-light-success">
                                <i class="ri ri-money-rupee-circle-line text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card kpi-widget border-0 shadow-sm border-start border-warning border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">Shortage Brands</p>
                                <h4 class="mb-0 fw-bold text-warning">{{ $fabric_shortage_count }}</h4>
                                <span class="text-muted small">{{ $fabric_shortage_count > 0 ? 'Below Min Requirement' : 'All Stocks Healthy' }}</span>
                            </div>
                            <div class="kpi-icon bg-light-warning">
                                <i class="ri ri-alert-line text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card kpi-widget border-0 shadow-sm border-start border-danger border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">Excess Brands</p>
                                <h4 class="mb-0 fw-bold text-danger">{{ $fabric_excess_count }}</h4>
                                <span class="text-muted small">Above Min Stock Limit</span>
                            </div>
                            <div class="kpi-icon bg-light-danger">
                                <i class="ri ri-arrow-up-circle-line text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Brand-wise Stock & Min Stock Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="ri ri-store-2-line me-2 text-info"></i>Fabric Inventory Dashboard
                    </h6>
                    <small class="text-muted">Consolidated overview of fabric stock, stock valuation, warehouse ageing, and shortage/excess</small>
                </div>
                <div class="search-box">
                    <input type="text" id="fabricStockSearchInput" class="form-control form-control-sm" placeholder="Search Brand..." style="width: 220px;">
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0" id="fabricStockTable">
                        <thead class="bg-light sticky-top" style="z-index: 2;">
                            <tr>
                                <th style="width: 45px;">#</th>
                                <th>BRAND</th>
                                <th class="text-end">STOCK</th>
                                <th class="text-end">STOCK VALUE</th>
                                <th class="text-center">DAYS IN WAREHOUSE</th>
                                <th class="text-center">MIN STOCK REQ.</th>
                                <th class="text-center">SHORTAGE</th>
                                <th class="text-center">EXCESS</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @php
                                $totStock = 0;
                                $totVal = 0;
                                $totMin = 0;
                                $totShort = 0;
                                $totExc = 0;
                            @endphp
                            @if(!empty($fabric_stock_summary) && count($fabric_stock_summary) > 0)
                                @foreach($fabric_stock_summary as $idx => $row)
                                @php
                                    $totStock += $row['stock'];
                                    $totVal += $row['stock_value'];
                                    $totMin += $row['min_stock'];
                                    $totShort += $row['shortage'];
                                    $totExc += $row['excess'];
                                @endphp
                                <tr class="fabric-stock-row">
                                    <td class="text-muted fw-bold">{{ $idx + 1 }}</td>
                                    <td>
                                        <span class="fw-bold text-dark fabric-brand-name">{{ $row['brand_name'] }}</span>
                                    </td>
                                    <td class="text-end fw-bold text-dark">{{ number_format($row['stock'], 2) }}</td>
                                    <td class="text-end fw-bold text-success">₹{{ number_format($row['stock_value'], 2) }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-2 py-1">
                                            {{ $row['days_in_warehouse'] }} Days
                                        </span>
                                    </td>
                                    <td class="text-center fw-bold">{{ number_format($row['min_stock'], 2) }}</td>
                                    <td class="text-center">
                                        @if($row['shortage'] > 0)
                                            <span class="badge bg-warning text-dark fw-bold px-3 py-2">
                                                {{ number_format($row['shortage'], 2) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($row['excess'] > 0)
                                            <span class="badge bg-danger text-white fw-bold px-3 py-2">
                                                {{ number_format($row['excess'], 2) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">No fabric stock data available.</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot class="bg-light fw-bold" style="position: sticky; bottom: 0; z-index: 2;">
                            <tr>
                                <td colspan="2" class="text-end">TOTAL:</td>
                                <td class="text-end text-dark">{{ number_format($totStock, 2) }}</td>
                                <td class="text-end text-success">₹{{ number_format($totVal, 2) }}</td>
                                <td class="text-center">-</td>
                                <td class="text-center">{{ number_format($totMin, 2) }}</td>
                                <td class="text-center text-warning">
                                    @if($totShort > 0)
                                        <span class="badge bg-warning text-dark fw-bold px-2 py-1">{{ number_format($totShort, 2) }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center text-danger">
                                    @if($totExc > 0)
                                        <span class="badge bg-danger text-white fw-bold px-2 py-1">{{ number_format($totExc, 2) }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('fabricStockSearchInput');
            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    const term = this.value.toLowerCase().trim();
                    const rows = document.querySelectorAll('#fabricStockTable tbody tr.fabric-stock-row');
                    rows.forEach(function(r) {
                        const brandText = r.querySelector('.fabric-brand-name')?.textContent.toLowerCase() || '';
                        r.style.display = brandText.includes(term) ? '' : 'none';
                    });
                });
            }
        });
    </script>
    @endif

    <!-- SECTION: FABRIC UTILISATION DASHBOARD -->
    @if(auth()->id() == 1 || auth()->user()->can('view purchase-report') || auth()->user()->can('view warehouse-report') || auth()->user()->can('view stock-entry-raw-materials'))
    <div class="mb-5" id="fabricUtilisationSection">
        <div class="d-flex align-items-center mb-3">
            <div class="section-indicator bg-success me-2"></div>
            <h5 class="fw-bold mb-0">Fabric Utilisation Dashboard</h5>
        </div>

        <!-- KPI Grid -->
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="card kpi-widget border-0 shadow-sm border-start border-primary border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">Total Fabric Issued</p>
                                <h4 class="mb-0 fw-bold">{{ number_format($total_util_issued, 2) }}</h4>
                                <span class="text-muted small">Meters Issued to Plants</span>
                            </div>
                            <div class="kpi-icon bg-light-primary">
                                <i class="ri ri-scissors-cut-line text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card kpi-widget border-0 shadow-sm border-start border-success border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">Total Fabric Consumed</p>
                                <h4 class="mb-0 fw-bold">{{ number_format($total_util_consumed, 2) }}</h4>
                                <span class="text-muted small">Meters Used in Production</span>
                            </div>
                            <div class="kpi-icon bg-light-success">
                                <i class="ri ri-checkbox-circle-line text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card kpi-widget border-0 shadow-sm border-start border-danger border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">Total Wastage</p>
                                <h4 class="mb-0 fw-bold text-danger">{{ number_format($total_util_wastage, 2) }}</h4>
                                <span class="text-muted small">Cut Waste / Remnants (M)</span>
                            </div>
                            <div class="kpi-icon bg-light-danger">
                                <i class="ri ri-delete-bin-line text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card kpi-widget border-0 shadow-sm border-start border-info border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">Overall Utilisation</p>
                                <h4 class="mb-0 fw-bold {{ $total_util_pct >= 95 ? 'text-success' : ($total_util_pct >= 90 ? 'text-warning' : 'text-danger') }}">{{ $total_util_pct }}%</h4>
                                <span class="text-muted small">Fabric Cutting Efficiency</span>
                            </div>
                            <div class="kpi-icon bg-light-info">
                                <i class="ri ri-pie-chart-2-line text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Breadcrumb Navigation for Level 2 Drilldown -->
        <div class="mb-3 d-flex align-items-center" id="utilBreadcrumbs" style="display: none !important;">
            <button class="btn btn-sm btn-outline-secondary me-2" onclick="showUtilBrandLevel()" id="btnBackToUtilBrands">
                <i class="ri ri-arrow-left-line me-1"></i> Back to All Brands
            </button>
            <span class="text-muted me-1">All Brands &gt;</span>
            <span class="text-dark fw-bold" id="utilBreadcrumbBrandName">CASINO FORMAL</span>
        </div>

        <!-- Level 1: Brand Summary Container -->
        <div id="utilBrandContainer" class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="ri ri-line-chart-line me-2 text-success"></i>Brand-wise Fabric Utilisation
                    </h6>
                    <small class="text-muted">Click any brand row to drill down into its job cards, style, plants, and remarks</small>
                </div>
                <div class="search-box">
                    <input type="text" id="utilBrandSearchInput" class="form-control form-control-sm" placeholder="Search Brand..." style="width: 220px;">
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0" id="utilBrandTable">
                        <thead class="bg-light sticky-top" style="z-index: 2;">
                            <tr>
                                <th style="width: 45px;">#</th>
                                <th>BRAND</th>
                                <th>STYLE</th>
                                <th>SERVICE PROVIDER (PLANT)</th>
                                <th class="text-center">JOB CARDS</th>
                                <th class="text-end">FABRIC ISSUED (M)</th>
                                <th class="text-end">FABRIC CONSUMED (M)</th>
                                <th class="text-end">WASTAGE (M)</th>
                                <th class="text-center">UTILISATION %</th>
                                <th class="text-center" style="width: 90px;">ACTION</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @php
                                $totU_Issued = 0;
                                $totU_Consumed = 0;
                                $totU_Wastage = 0;
                                $totU_JCs = 0;
                            @endphp
                            @if(!empty($fabric_utilisation_summary) && count($fabric_utilisation_summary) > 0)
                                @foreach($fabric_utilisation_summary as $uIdx => $uRow)
                                @php
                                    $totU_Issued += $uRow['fabric_issued'];
                                    $totU_Consumed += $uRow['fabric_consumed'];
                                    $totU_Wastage += $uRow['wastage'];
                                    $totU_JCs += $uRow['job_cards_count'];
                                    $uPct = $uRow['utilisation'];
                                @endphp
                                <tr class="util-brand-row" style="cursor: pointer;" onclick="drillDownToUtilBrandByIndex({{ $uIdx }})">
                                    <td class="text-muted fw-bold">{{ $uIdx + 1 }}</td>
                                    <td>
                                        <span class="fw-bold text-dark util-brand-name">{{ $uRow['brand_name'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-secondary fw-bold util-style-name">{{ $uRow['style'] }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-secondary util-sp-name">{{ $uRow['service_provider'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-info fw-bold">{{ $uRow['job_cards_count'] }} JCs</span>
                                    </td>
                                    <td class="text-end fw-bold text-dark">{{ number_format($uRow['fabric_issued'], 2) }}</td>
                                    <td class="text-end fw-bold text-success">{{ number_format($uRow['fabric_consumed'], 2) }}</td>
                                    <td class="text-end fw-bold {{ $uRow['wastage'] > 0 ? 'text-danger' : 'text-muted' }}">
                                        {{ number_format($uRow['wastage'], 2) }}
                                    </td>
                                    <td class="text-center">
                                        @if($uPct >= 95)
                                            <span class="badge bg-success text-white fw-bold px-3 py-2">{{ $uPct }}%</span>
                                        @elseif($uPct >= 90)
                                            <span class="badge bg-warning text-dark fw-bold px-3 py-2">{{ $uPct }}%</span>
                                        @else
                                            <span class="badge bg-danger text-white fw-bold px-3 py-2">{{ $uPct }}%</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-xs btn-outline-primary py-1 px-2">
                                            View <i class="ri ri-arrow-right-s-line"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">No fabric utilisation data available.</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot class="bg-light fw-bold" style="position: sticky; bottom: 0; z-index: 2;">
                            <tr>
                                <td colspan="4" class="text-end">TOTAL:</td>
                                <td class="text-center" id="utilFootJCs">{{ number_format($totU_JCs) }} JCs</td>
                                <td class="text-end text-dark" id="utilFootIssued">{{ number_format($totU_Issued, 2) }}</td>
                                <td class="text-end text-success" id="utilFootConsumed">{{ number_format($totU_Consumed, 2) }}</td>
                                <td class="text-end text-danger" id="utilFootWastage">{{ number_format($totU_Wastage, 2) }}</td>
                                <td class="text-center text-primary" id="utilFootUtil">
                                    <span class="badge bg-label-primary px-3 py-2 fw-bold">
                                        {{ $totU_Issued > 0 ? round(($totU_Consumed / $totU_Issued) * 100, 1) : 0 }}%
                                    </span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- Card Footer: AJAX Pagination Controls -->
            <div class="card-footer bg-white py-2 d-flex flex-wrap justify-content-between align-items-center border-top">
                <div class="small text-muted mb-2 mb-sm-0">
                    Showing <span id="utilPageFrom" class="fw-bold">1</span> to <span id="utilPageTo" class="fw-bold">10</span> of <span id="utilPageTotal" class="fw-bold">{{ count($fabric_utilisation_summary) }}</span> entries
                </div>
                <div class="d-flex align-items-center">
                    <nav aria-label="Fabric Utilisation pagination">
                        <ul class="pagination pagination-sm mb-0" id="utilPaginationList">
                            <li class="page-item" id="utilPrevItem">
                                <button class="page-link" id="btnUtilPrev" onclick="changeUtilPage(currentUtilPage - 1)">
                                    <i class="ri ri-arrow-left-s-line"></i> Prev
                                </button>
                            </li>
                            <li id="utilPageNumbersContainer" class="d-flex"></li>
                            <li class="page-item" id="utilNextItem">
                                <button class="page-link" id="btnUtilNext" onclick="changeUtilPage(currentUtilPage + 1)">
                                    Next <i class="ri ri-arrow-right-s-line"></i>
                                </button>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Level 2: Detailed Job Cards Container -->
        <div id="utilJobCardsContainer" class="card border-0 shadow-sm" style="display: none;">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="ri ri-file-list-3-line me-2 text-primary"></i><span id="utilActiveBrandHeaderTitle">Job Cards</span>
                    </h6>
                    <small class="text-muted">Showing plant service provider, style, issued meters, consumed, wastage, and remarks</small>
                </div>
                <div class="search-box">
                    <input type="text" id="utilJobCardSearchInput" class="form-control form-control-sm" placeholder="Search Job Card / Plant..." style="width: 250px;">
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 520px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0" id="utilJobCardsTable">
                        <thead class="bg-light sticky-top" style="z-index: 2;">
                            <tr>
                                <th style="width: 45px;">#</th>
                                <th>JOB CARD NO</th>
                                <th>DATE</th>
                                <th>SERVICE PROVIDER (PLANT)</th>
                                <th>STYLE</th>
                                <th class="text-end">FABRIC ISSUED (M)</th>
                                <th class="text-end">FABRIC CONSUMED (M)</th>
                                <th class="text-end">WASTAGE (M)</th>
                                <th class="text-center">UTILISATION %</th>
                                <th>REMARKS</th>
                                <th class="text-center">STATUS</th>
                            </tr>
                        </thead>
                        <tbody class="small"></tbody>
                        <tfoot class="bg-light fw-bold" style="position: sticky; bottom: 0; z-index: 2;">
                            <tr>
                                <td colspan="5" class="text-end">TOTAL:</td>
                                <td id="utilJcFootIssued" class="text-end text-dark">0.00</td>
                                <td id="utilJcFootConsumed" class="text-end text-success">0.00</td>
                                <td id="utilJcFootWastage" class="text-end text-danger">0.00</td>
                                <td id="utilJcFootUtil" class="text-center text-primary">0%</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // AJAX Pagination & Drilldown Logic for Fabric Utilisation Dashboard
        let currentUtilPage = 1;
        let lastUtilPage = 1;
        let utilSearchTerm = '';
        let currentUtilData = [];
        let utilSearchTimer = null;

        function loadFabricUtilisation(page, search) {
            if (page < 1) page = 1;
            currentUtilPage = page;
            if (search !== undefined) {
                utilSearchTerm = search;
            }

            const tbody = document.querySelector('#utilBrandTable tbody');
            if (tbody) {
                tbody.style.opacity = '0.4';
            }

            $.ajax({
                url: '{{ url("/dashboard/fabric-utilisation") }}',
                type: 'GET',
                data: {
                    page: currentUtilPage,
                    per_page: 10,
                    search: utilSearchTerm
                },
                success: function(res) {
                    if (tbody) tbody.style.opacity = '1';
                    currentUtilData = res.data || [];
                    currentUtilPage = res.current_page || 1;
                    lastUtilPage = res.last_page || 1;

                    // Render Rows
                    tbody.innerHTML = '';
                    if (currentUtilData.length > 0) {
                        currentUtilData.forEach(function(row, idx) {
                            const u = parseFloat(row.utilisation || 0);
                            let badgeClass = 'bg-danger text-white';
                            if (u >= 95) badgeClass = 'bg-success text-white';
                            else if (u >= 90) badgeClass = 'bg-warning text-dark';

                            const sNo = res.from + idx;
                            const tr = document.createElement('tr');
                            tr.className = 'util-brand-row';
                            tr.style.cursor = 'pointer';
                            tr.onclick = function() {
                                const title = `${row.brand_name} - ${row.style} (${row.service_provider})`;
                                drillDownToUtilBrand(title, row.job_cards);
                            };

                            tr.innerHTML = `
                                <td class="text-muted fw-bold">${sNo}</td>
                                <td><span class="fw-bold text-dark util-brand-name">${row.brand_name}</span></td>
                                <td><span class="badge bg-label-secondary fw-bold util-style-name">${row.style}</span></td>
                                <td><span class="fw-bold text-secondary util-sp-name">${row.service_provider}</span></td>
                                <td class="text-center"><span class="badge bg-label-info fw-bold">${row.job_cards_count} JCs</span></td>
                                <td class="text-end fw-bold text-dark">${parseFloat(row.fabric_issued || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                <td class="text-end fw-bold text-success">${parseFloat(row.fabric_consumed || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                <td class="text-end fw-bold ${parseFloat(row.wastage || 0) > 0 ? 'text-danger' : 'text-muted'}">${parseFloat(row.wastage || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                <td class="text-center"><span class="badge ${badgeClass} fw-bold px-3 py-2">${u}%</span></td>
                                <td class="text-center"><button class="btn btn-xs btn-outline-primary py-1 px-2">View <i class="ri ri-arrow-right-s-line"></i></button></td>
                            `;
                            tbody.appendChild(tr);
                        });
                    } else {
                        tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted py-4">No matching fabric utilisation records found.</td></tr>';
                    }

                    // Update Totals Footer
                    if (res.totals) {
                        const elJCs = document.getElementById('utilFootJCs');
                        const elIssued = document.getElementById('utilFootIssued');
                        const elConsumed = document.getElementById('utilFootConsumed');
                        const elWastage = document.getElementById('utilFootWastage');
                        const elUtil = document.getElementById('utilFootUtil');

                        if (elJCs) elJCs.textContent = res.totals.total_jcs;
                        if (elIssued) elIssued.textContent = res.totals.total_issued;
                        if (elConsumed) elConsumed.textContent = res.totals.total_consumed;
                        if (elWastage) elWastage.textContent = res.totals.total_wastage;
                        if (elUtil) elUtil.innerHTML = `<span class="badge bg-label-primary px-3 py-2 fw-bold">${res.totals.total_utilisation}</span>`;
                    }

                    // Update Pagination Info
                    const elFrom = document.getElementById('utilPageFrom');
                    const elTo = document.getElementById('utilPageTo');
                    const elTotal = document.getElementById('utilPageTotal');
                    if (elFrom) elFrom.textContent = res.from || 0;
                    if (elTo) elTo.textContent = res.to || 0;
                    if (elTotal) elTotal.textContent = res.total_records || 0;

                    // Update Prev / Next buttons
                    const prevItem = document.getElementById('utilPrevItem');
                    const nextItem = document.getElementById('utilNextItem');
                    if (prevItem) {
                        if (currentUtilPage <= 1) prevItem.classList.add('disabled');
                        else prevItem.classList.remove('disabled');
                    }
                    if (nextItem) {
                        if (currentUtilPage >= lastUtilPage) nextItem.classList.add('disabled');
                        else nextItem.classList.remove('disabled');
                    }

                    // Render Page Numbers
                    const numContainer = document.getElementById('utilPageNumbersContainer');
                    if (numContainer) {
                        numContainer.innerHTML = '';
                        for (let i = 1; i <= lastUtilPage; i++) {
                            const li = document.createElement('li');
                            li.className = 'page-item' + (i === currentUtilPage ? ' active' : '');
                            li.innerHTML = `<button class="page-link" onclick="changeUtilPage(${i})">${i}</button>`;
                            numContainer.appendChild(li);
                        }
                    }
                },
                error: function(xhr) {
                    if (tbody) tbody.style.opacity = '1';
                    console.error('Failed to load fabric utilisation via AJAX:', xhr);
                }
            });
        }

        function changeUtilPage(page) {
            if (page < 1 || page > lastUtilPage || page === currentUtilPage) return;
            loadFabricUtilisation(page);
        }

        function drillDownToUtilBrand(brandName, jobCards) {
            document.getElementById('utilBrandContainer').style.display = 'none';
            document.getElementById('utilBreadcrumbs').style.setProperty('display', 'flex', 'important');
            document.getElementById('utilBreadcrumbBrandName').textContent = brandName;
            document.getElementById('utilActiveBrandHeaderTitle').textContent = brandName + ' - Job Cards';
            document.getElementById('utilJobCardsContainer').style.display = 'block';

            const tbody = document.querySelector('#utilJobCardsTable tbody');
            tbody.innerHTML = '';

            let totIssued = 0;
            let totConsumed = 0;
            let totWastage = 0;

            if (jobCards && jobCards.length > 0) {
                jobCards.forEach(function(jc, index) {
                    totIssued += parseFloat(jc.fabric_issued || 0);
                    totConsumed += parseFloat(jc.fabric_consumed || 0);
                    totWastage += parseFloat(jc.wastage || 0);

                    const u = parseFloat(jc.utilisation || 0);
                    let badgeClass = 'bg-danger text-white';
                    if (u >= 95) badgeClass = 'bg-success text-white';
                    else if (u >= 90) badgeClass = 'bg-warning text-dark';

                    const row = document.createElement('tr');
                    row.className = 'util-jc-row';
                    row.innerHTML = `
                        <td class="text-muted fw-bold">${index + 1}</td>
                        <td class="fw-bold text-primary jc-no">${jc.job_card_no}</td>
                        <td class="text-muted">${jc.date}</td>
                        <td class="fw-bold jc-plant">${jc.service_provider}</td>
                        <td><span class="badge bg-label-secondary">${jc.style}</span></td>
                        <td class="text-end fw-bold text-dark">${parseFloat(jc.fabric_issued || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                        <td class="text-end fw-bold text-success">${parseFloat(jc.fabric_consumed || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                        <td class="text-end fw-bold ${parseFloat(jc.wastage || 0) > 0 ? 'text-danger' : 'text-muted'}">${parseFloat(jc.wastage || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                        <td class="text-center"><span class="badge ${badgeClass} fw-bold px-2 py-1">${u}%</span></td>
                        <td class="text-muted small">${jc.remarks}</td>
                        <td class="text-center"><span class="badge bg-label-info rounded-pill">${jc.status}</span></td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="11" class="text-center text-muted py-4">No job cards found for ${brandName}.</td></tr>`;
            }

            const overallPct = totIssued > 0 ? ((totConsumed / totIssued) * 100).toFixed(1) : 0;
            document.getElementById('utilJcFootIssued').textContent = totIssued.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('utilJcFootConsumed').textContent = totConsumed.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('utilJcFootWastage').textContent = totWastage.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('utilJcFootUtil').textContent = overallPct + '%';

            // Reset job cards search input
            const jcSearch = document.getElementById('utilJobCardSearchInput');
            if (jcSearch) jcSearch.value = '';
        }

        function showUtilBrandLevel() {
            document.getElementById('utilBreadcrumbs').style.setProperty('display', 'none', 'important');
            document.getElementById('utilJobCardsContainer').style.display = 'none';
            document.getElementById('utilBrandContainer').style.display = 'block';
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Load initial Page 1 via AJAX
            loadFabricUtilisation(1);

            // Debounced AJAX search for brand/style/plant
            const brandSearch = document.getElementById('utilBrandSearchInput');
            if (brandSearch) {
                brandSearch.addEventListener('input', function() {
                    clearTimeout(utilSearchTimer);
                    const term = this.value.trim();
                    utilSearchTimer = setTimeout(function() {
                        loadFabricUtilisation(1, term);
                    }, 300);
                });
            }

            // Job cards table search
            const jcSearch = document.getElementById('utilJobCardSearchInput');
            if (jcSearch) {
                jcSearch.addEventListener('keyup', function() {
                    const term = this.value.toLowerCase().trim();
                    const rows = document.querySelectorAll('#utilJobCardsTable tbody tr.util-jc-row');
                    rows.forEach(function(r) {
                        const jcNo = r.querySelector('.jc-no')?.textContent.toLowerCase() || '';
                        const plant = r.querySelector('.jc-plant')?.textContent.toLowerCase() || '';
                        const text = r.textContent.toLowerCase();
                        r.style.display = text.includes(term) ? '' : 'none';
                    });
                });
            }
        });
    </script>
    @endif
    </div> <!-- /#tab-fabric-stores -->

    <!-- ==================== TAB 3: CORE MATERIAL PLANNER ==================== -->
    <div class="tab-pane fade" id="tab-core-planner" role="tabpanel" aria-labelledby="tab-core-planner-tab">
        <!-- SECTION: CORE MATERIAL PLANNER DASHBOARD -->
        @if(auth()->id() == 1 || auth()->user()->can('view purchase-report') || auth()->user()->can('view warehouse-report') || auth()->user()->can('view stock-entry-raw-materials'))
        <div class="mb-5" id="coreMaterialPlannerSection">
        <div class="d-flex align-items-center mb-3">
            <div class="section-indicator bg-warning me-2"></div>
            <h5 class="fw-bold mb-0">Core Material Planner</h5>
        </div>

        <!-- KPI Grid -->
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="card kpi-widget border-0 shadow-sm border-start border-primary border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">Total Fabric Stock</p>
                                <h4 class="mb-0 fw-bold">{{ number_format($core_total_stock, 2) }}</h4>
                                <span class="text-muted small">Warehouse Available (M)</span>
                            </div>
                            <div class="kpi-icon bg-light-primary">
                                <i class="ri ri-store-2-line text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card kpi-widget border-0 shadow-sm border-start border-warning border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">WIP in Production</p>
                                <h4 class="mb-0 fw-bold text-warning">{{ number_format($core_total_wip, 2) }}</h4>
                                <span class="text-muted small">Active Job Cards (M)</span>
                            </div>
                            <div class="kpi-icon bg-light-warning">
                                <i class="ri ri-time-line text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card kpi-widget border-0 shadow-sm border-start border-success border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">Finished Goods (FG)</p>
                                <h4 class="mb-0 fw-bold text-success">{{ number_format($core_total_fg) }}</h4>
                                <span class="text-muted small">Garments Ready in Store (PCS)</span>
                            </div>
                            <div class="kpi-icon bg-light-success">
                                <i class="ri ri-shirt-line text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card kpi-widget border-0 shadow-sm border-start border-info border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">Total Pipeline</p>
                                <h4 class="mb-0 fw-bold text-info">{{ number_format($core_total_pipeline, 2) }}</h4>
                                <span class="text-muted small">Stock + WIP (Meters)</span>
                            </div>
                            <div class="kpi-icon bg-light-info">
                                <i class="ri ri-equalizer-line text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div id="corePlannerContainer" class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="ri ri-stack-line me-2 text-warning"></i>Core Material Stock & Production Pipeline
                </h6>
                <small class="text-muted">Showing fabric art numbers, current warehouse stock, active WIP, and finished goods inventory</small>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 w-100" id="corePlannerTable">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 45px;">#</th>
                                <th>ART NO</th>
                                <th>ITEM / MATERIAL</th>
                                <th>BRAND</th>
                                <th class="text-end">STOCK (M)</th>
                                <th class="text-end">WIP (M)</th>
                                <th class="text-end">FG (PCS)</th>
                                <th class="text-end">TOTAL PIPELINE (M)</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <!-- Populated via DataTables AJAX -->
                        </tbody>
                        <tfoot class="bg-light fw-bold">
                            <tr>
                                <td colspan="4" class="text-end">TOTAL:</td>
                                <td class="text-end text-dark" id="coreFootStock">{{ number_format($core_total_stock, 2) }}</td>
                                <td class="text-end text-warning" id="coreFootWip">{{ number_format($core_total_wip, 2) }}</td>
                                <td class="text-end text-success" id="coreFootFg">{{ number_format($core_total_fg) }} pcs</td>
                                <td class="text-end text-info" id="coreFootPipeline">{{ number_format($core_total_pipeline, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            if ($('#corePlannerTable').length) {
                $('#corePlannerTable').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    ajax: {
                        url: "{{ url('/dashboard/core-material-planner') }}",
                        type: "GET"
                    },
                    columns: [
                        { data: 'DT_RowIndex', orderable: false, searchable: false, width: '45px' },
                        { data: 'art_no' },
                        { data: 'item_name' },
                        { data: 'brand_name' },
                        { data: 'stock', className: 'text-end' },
                        { data: 'wip', className: 'text-end' },
                        { data: 'fg', className: 'text-end' },
                        { data: 'pipeline', className: 'text-end' }
                    ],
                    dom: '<"row align-items-center mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row align-items-center mt-3"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"p>>',
                    language: {
                        search: "",
                        searchPlaceholder: "Search Art No / Item / Brand...",
                        processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
                    },
                    drawCallback: function(settings) {
                        var json = settings.json;
                        if (json && json.totals) {
                            $('#coreFootStock').text(json.totals.total_stock);
                            $('#coreFootWip').text(json.totals.total_wip);
                            $('#coreFootFg').text(json.totals.total_fg + ' pcs');
                            $('#coreFootPipeline').text(json.totals.total_pipeline);
                        }
                    }
                });
            }
        });
    </script>
    @endif
    </div> <!-- /#tab-core-planner -->

    <!-- ==================== TAB 4: SUPPLIER PERFORMANCE ==================== -->
    <div class="tab-pane fade" id="tab-suppliers" role="tabpanel" aria-labelledby="tab-suppliers-tab">
        <!-- SECTION: SUPPLIER PERFORMANCE DASHBOARD -->
        @if(auth()->id() == 1 || auth()->user()->can('view purchase-report') || auth()->user()->can('view suppliers'))
        <div class="mb-5" id="supplierPerformanceSection">
        <div class="d-flex align-items-center mb-3">
            <div class="section-indicator bg-info me-2"></div>
            <h5 class="fw-bold mb-0">Supplier Performance</h5>
        </div>

        <!-- KPI Grid -->
        <div class="row g-3 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="card kpi-widget border-0 shadow-sm border-start border-primary border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">Active Suppliers</p>
                                <h4 class="mb-0 fw-bold">{{ $supplier_perf_count }}</h4>
                                <span class="text-muted small">With Purchase Orders</span>
                            </div>
                            <div class="kpi-icon bg-light-primary">
                                <i class="ri ri-store-3-line text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card kpi-widget border-0 shadow-sm border-start border-success border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">Total Purchase Spend</p>
                                <h4 class="mb-0 fw-bold text-success">₹{{ number_format($supplier_perf_spend, 2) }}</h4>
                                <span class="text-muted small">Cumulative Order Spend</span>
                            </div>
                            <div class="kpi-icon bg-light-success">
                                <i class="ri ri-money-dollar-circle-line text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card kpi-widget border-0 shadow-sm border-start border-warning border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">Fleet On-Time Rate</p>
                                <h4 class="mb-0 fw-bold text-warning">{{ $supplier_perf_ontime }}%</h4>
                                <span class="text-muted small">Delivered By Due Date</span>
                            </div>
                            <div class="kpi-icon bg-light-warning">
                                <i class="ri ri-time-line text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card kpi-widget border-0 shadow-sm border-start border-danger border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">Returns (Debit Notes)</p>
                                <h4 class="mb-0 fw-bold text-danger">{{ $supplier_perf_returns }}</h4>
                                <span class="text-muted small">Quality / Shade Rejections</span>
                            </div>
                            <div class="kpi-icon bg-light-danger">
                                <i class="ri ri-refund-2-line text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div id="supplierPerformanceContainer" class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="ri ri-truck-line me-2 text-info"></i>Supplier Reliability & Performance Tracker
                </h6>
                <small class="text-muted">Instead of measuring only purchase value, measure supplier reliability, delivery speed, and return rates.</small>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 w-100" id="supplierPerformanceTable">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 45px;">#</th>
                                <th>SUPPLIER</th>
                                <th class="text-end">PURCHASE VALUE</th>
                                <th class="text-center">ORDERS</th>
                                <th class="text-center">ON-TIME %</th>
                                <th class="text-center">AVG DELAY</th>
                                <th class="text-center">RETURNS (DEBIT NOTES)</th>
                                <th class="text-center">OVERALL RATING</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <!-- Populated via DataTables AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            if ($('#supplierPerformanceTable').length) {
                $('#supplierPerformanceTable').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    ajax: {
                        url: "{{ url('/dashboard/supplier-performance') }}",
                        type: "GET"
                    },
                    columns: [
                        { data: 'DT_RowIndex', orderable: false, searchable: false, width: '45px' },
                        { data: 'supplier', name: 'supplier' },
                        { data: 'purchase_value', name: 'purchase_value', className: 'text-end' },
                        { data: 'orders', name: 'orders', className: 'text-center' },
                        { data: 'on_time_pct', name: 'on_time_pct', className: 'text-center' },
                        { data: 'avg_delay', name: 'avg_delay', className: 'text-center' },
                        { data: 'returns', name: 'returns', className: 'text-center' },
                        { data: 'overall_rating', name: 'overall_rating', className: 'text-center' }
                    ],
                    dom: '<"row align-items-center mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row align-items-center mt-3"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"p>>',
                    language: {
                        search: "",
                        searchPlaceholder: "Search Supplier Name...",
                        processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
                    }
                });
            }
        });
    </script>
    @endif
    </div> <!-- /#tab-suppliers -->

    <!-- ==================== TAB 6: MAINTENANCE & COMPLIANCE ==================== -->
    <div class="tab-pane fade" id="tab-maintenance" role="tabpanel" aria-labelledby="tab-maintenance-tab">
        <!-- SECTION 4: MAINTENANCE -->
        @if(auth()->id() == 1 || auth()->user()->can('view-maintenance dashboard'))
        <div class="mb-4">
        <div class="d-flex align-items-center mb-3">
            <div class="section-indicator bg-danger me-2"></div>
            <h5 class="fw-bold mb-0">Maintenance</h5>
        </div>
        <div class="row g-4">
            <!-- Renewals & Compliance Table -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-navy"><i class="ri ri-shield-check-line me-2"></i>Renewals & Compliance</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="small font-weight-bold">Renewal Item</th>
                                        <th class="small font-weight-bold">Due Date</th>
                                        <th class="small font-weight-bold text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="small">
                                    @if($expiring_documents->count() > 0)
                                    @foreach($expiring_documents as $doc)
                                    <tr>
                                        <td><strong>{{ $doc->document_name }}</strong></td>
                                        <td>{{ date('d-M-y', strtotime($doc->validity_date)) }}</td>
                                        <td class="text-center">
                                            @php
                                                $daysLeft = \Carbon\Carbon::parse($doc->validity_date)->diffInDays(\Carbon\Carbon::today());
                                                $isExpired = \Carbon\Carbon::parse($doc->validity_date)->isPast();
                                            @endphp
                                            @if($isExpired)
                                                <span class="badge bg-soft-danger text-danger border border-danger px-2 py-1">Expired</span>
                                            @else
                                                <span class="badge bg-soft-warning text-warning border border-warning px-2 py-1">{{ $daysLeft }} Days Left</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-muted small">No upcoming renewals in the next 30 days</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Machinery & Other Service Due (Unit Wise) -->
            <!--
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100 text-navy">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-navy"><i class="ri ri-settings-5-line me-2"></i>Machinery & Other Service Due (Unit Wise)</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small fw-bold">Unit I (Cutting)</span>
                                <span class="small text-danger">02 Due</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 65%"></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small fw-bold">Unit II (Stitching)</span>
                                <span class="small text-success">Healthy</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small fw-bold">Unit III (Finishing)</span>
                                <span class="small text-warning">01 Due</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            -->

            <!-- Maintenance Requirements Log -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-navy"><i class="ri ri-tools-line me-2"></i>Maintenance Requirements</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush list-group-sm">
                            <div class="list-group-item border-0 py-3 bg-light-danger-soft">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small fw-bold">Requirements Raised</span>
                                    <span class="badge bg-danger rounded-pill">{{ $maintenance_raised }}</span>
                                </div>
                            </div>
                            <div class="list-group-item border-0 py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small fw-bold">Requirements Attended</span>
                                    <span class="badge bg-success rounded-pill">{{ $maintenance_attended }}</span>
                                </div>
                            </div>
                            <div class="list-group-item border-0 py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small fw-bold text-danger">Requirements Pending</span>
                                    <span class="badge bg-danger rounded-pill">{{ $maintenance_pending }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    </div> <!-- /#tab-maintenance -->

    </div> <!-- /#dashboardTabsContent -->
</div> <!-- /.container-xxl -->

<style>
    /* Segmented Dashboard Nav Pills */
    .dashboard-nav-pills {
        background: #ffffff;
        border: 1px solid #e2e8f0;
    }
    .dashboard-nav-pills .nav-link {
        color: #475569;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.88rem;
        transition: all 0.2s ease-in-out;
    }
    .dashboard-nav-pills .nav-link:hover {
        background: #e2e8f0;
        color: #0f172a;
        transform: translateY(-1px);
    }
    .dashboard-nav-pills .nav-link.active {
        background: #1e3a8a !important;
        color: #ffffff !important;
        border-color: #1e3a8a !important;
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.25);
    }
    .dashboard-nav-pills .nav-link.active i {
        color: #ffffff !important;
    }

    .section-indicator {
        width: 12px;
        height: 24px;
        border-radius: 4px;
    }

    .kpi-widget {
        transition: transform 0.3s ease;
    }
    .kpi-widget:hover {
        transform: translateY(-5px);
    }
    .kpi-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.5rem;
    }

    /* Color Variances */
    .bg-light-primary { background: #eef2ff; }
    .bg-light-info { background: #ecfeff; }
    .bg-light-success { background: #f0fdf4; }
    .bg-light-warning { background: #fffbeb; }
    .bg-light-danger { background: #fef2f2; }
    .bg-light-secondary { background: #f8fafc; }
    .bg-light-dark { background: #f1f5f9; }

    /* Financial Stat Cards */
    .financial-stat {
        background: #fff;
        border-top: 3px solid #e2e8f0 !important;
    }

    /* Table Styles */
    .table thead th {
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.03em;
        color: #64748b;
        border-top: none;
    }

    /* Alert Styling */
    .alert-soft-danger {
        background-color: #fef2f2;
        border: 1px solid #fee2e2;
        color: #991b1b;
    }
    .alert-soft-warning {
        background-color: #fffbeb;
        border: 1px solid #fef3c7;
        color: #92400e;
    }

    .x-small { font-size: 0.65rem; }
    .badge.bg-label-success { background: #dcfce7; color: #166534; }
    .badge.bg-label-primary { background: #dbeafe; color: #1e40af; }
    .badge.bg-label-warning { background: #fef9c3; color: #854d0e; }

    .bg-soft-danger { background-color: #fef2f2; }
    .bg-soft-warning { background-color: #fffbeb; }
    .bg-soft-info { background-color: #eff6ff; }
    .bg-light-danger-soft { background-color: #fff5f5; }

    .border-dashed { border-style: dashed !important; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const wipSearchInput = document.getElementById('wipSearchInput');
        if (wipSearchInput) {
            wipSearchInput.addEventListener('keyup', function() {
                const value = this.value.toLowerCase();
                const rows = document.querySelectorAll('.wip-row');
                
                rows.forEach(row => {
                    const text = row.querySelector('.wip-process-name').textContent.toLowerCase();
                    row.style.display = text.includes(value) ? '' : 'none';
                });
            });
        }

        const costSearchInput = document.getElementById('costSearchInput');
        if (costSearchInput) {
            costSearchInput.addEventListener('keyup', function() {
                const value = this.value.toLowerCase();
                const rows = document.querySelectorAll('.cost-row');
                
                rows.forEach(row => {
                    const text = row.querySelector('.cost-jc-no').textContent.toLowerCase();
                    row.style.display = text.includes(value) ? '' : 'none';
                });
            });
        }

        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#64748b';

        new Chart(document.getElementById('stockDistributionChart'), {
            type: 'doughnut',
            data: {
                labels: ['Fabric', 'Accessories', 'WIP', 'Finished Goods'],
                datasets: [{
                    data: [{{ $fabric_value }}, {{ $accessories_value }}, {{ $wip_value }}, {{ $finished_goods_value }}],
                    backgroundColor: ['#1e3a8a', '#06b6d4', '#f59e0b', '#10b981'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '70%'
            }
        });

        new Chart(document.getElementById('salesCollectionChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($months_labels) !!},
                datasets: [{
                    label: 'Sales',
                    data: {!! json_encode($sales_chart_data) !!},
                    borderColor: '#1e3a8a',
                    backgroundColor: 'rgba(30, 58, 138, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3
                }, {
                    label: 'Collection',
                    data: {!! json_encode($collection_chart_data) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'transparent',
                    tension: 0.4,
                    borderWidth: 2,
                    borderDash: [5, 5]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', align: 'end', labels: { boxWidth: 8, usePointStyle: true, padding: 20 } }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, border: { display: false } },
                    x: { grid: { display: false } }
                }
            }
        });

        new Chart(document.getElementById('purchasePaymentChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($months_labels) !!},
                datasets: [{
                    label: 'Purchase',
                    data: {!! json_encode($purchase_chart_data) !!},
                    backgroundColor: '#1e3a8a',
                    borderRadius: 4
                }, {
                    label: 'Payment',
                    data: {!! json_encode($payment_chart_data) !!},
                    backgroundColor: '#94a3b8',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', align: 'end', labels: { boxWidth: 8, usePointStyle: true, padding: 20 } }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, border: { display: false } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Attendance Summary Chart
        const attendanceData = {!! json_encode($attendance_chart_data ?? []) !!};
        const ctxAttendance = document.getElementById('attendanceSummaryChart');
        let attendanceChart = null;

        if (ctxAttendance) {
            const initAttendanceChart = (deviceKey) => {
                const data = attendanceData[deviceKey] || { Present: 0, Absent: 0, Late: 0 };
                
                if (attendanceChart) {
                    attendanceChart.destroy();
                }

                attendanceChart = new Chart(ctxAttendance, {
                    type: 'pie',
                    data: {
                        labels: ['Present', 'Absent', 'Late'],
                        datasets: [{
                            data: [data.Present || 0, data.Absent || 0, data.Late || 0],
                            backgroundColor: ['#28a745', '#dc3545', '#ffc107'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 40,
                                    usePointStyle: false,
                                    padding: 20,
                                    // Show percentage next to label
                                    generateLabels: function(chart) {
                                        const dataset = chart.data.datasets[0];
                                        const total = dataset.data.reduce((a, b) => a + b, 0);
                                        return chart.data.labels.map((label, i) => {
                                            const value = dataset.data[i];
                                            const percentage = total > 0
                                                ? ((value / total) * 100).toFixed(1)
                                                : 0;
                                            return {
                                                text: `${label}: ${percentage}% (${value})`,
                                                fillStyle: dataset.backgroundColor[i],
                                                strokeStyle: dataset.backgroundColor[i],
                                                index: i
                                            };
                                        });
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const dataset = context.dataset;
                                        const total = dataset.data.reduce((a, b) => a + b, 0);
                                        const value = dataset.data[context.dataIndex];
                                        const percentage = total > 0
                                            ? ((value / total) * 100).toFixed(1)
                                            : 0;
                                        return ` ${context.label}: ${value} (${percentage}%)`;
                                    }
                                }
                            },
                            // Show percentage directly on pie slices
                            datalabels: {
                                display: true
                            }
                        }
                    }
                });
            };

            initAttendanceChart('All');

            document.getElementById('attendanceDeviceFilter').addEventListener('change', function() {
                initAttendanceChart(this.value);
            });
        }

        // Dashboard Tab Persistence & DataTables auto-resizing
        $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
            const target = $(e.target).data('bs-target');
            if (target) {
                localStorage.setItem('activeDashboardTab', target);
                if (window.history.replaceState) {
                    window.history.replaceState(null, null, target);
                }
            }
            // Auto-adjust any DataTables on newly visible tab
            setTimeout(function() {
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust().responsive.recalc();
            }, 50);
        });

        // Restore active tab from URL hash or localStorage
        const activeTabHash = window.location.hash || localStorage.getItem('activeDashboardTab');
        if (activeTabHash && $(activeTabHash + '-tab').length) {
            const triggerEl = document.querySelector(activeTabHash + '-tab');
            if (triggerEl) {
                const tabTrigger = new bootstrap.Tab(triggerEl);
                tabTrigger.show();
            }
        }
    });
</script>
@endsection

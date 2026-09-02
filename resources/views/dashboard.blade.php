@extends('layouts.common')
@section('title', 'Dashboard - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <!-- TOP HEADER WITH BREADCRUMB & DATE CONTROLS -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
        <div>
            <h4 class="fw-bold mb-0 text-primary d-flex align-items-center">
                <i class="ri ri-dashboard-3-line me-2"></i> ERP Manufacturing Dashboard
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" id="breadcrumbActiveTab">Sales & Orders</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="badge bg-white text-dark border px-3 py-2 shadow-sm d-flex align-items-center">
                <i class="ri ri-calendar-check-line text-primary me-2 fs-6"></i>
                <span class="small fw-semibold">{{ date('d M, Y') }} | FY {{ date('m') >= 4 ? date('Y').'-'.(date('y')+1) : (date('Y')-1).'-'.date('y') }}</span>
            </div>
            <button type="button" class="btn btn-sm btn-white border shadow-sm" onclick="window.location.reload();" title="Refresh Dashboard">
                <i class="ri ri-refresh-line text-secondary"></i>
            </button>
        </div>
    </div>

    <!-- 5-TAB SEGMENTED NAVIGATION BAR (EXACTLY 5 TABS) -->
    <div class="mb-4">
        <ul class="nav nav-pills dashboard-nav-pills p-2 bg-white rounded-3 shadow-sm border flex-nowrap overflow-auto" id="dashboardTabs" role="tablist" style="scrollbar-width: thin; gap: 8px;">
            <!-- Tab 1: Sales & Orders -->
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold px-3 py-2 text-nowrap" id="tab-sales-orders-tab" data-bs-toggle="tab" data-bs-target="#tab-sales-orders" type="button" role="tab" aria-controls="tab-sales-orders" aria-selected="true">
                    <i class="ri ri-shopping-bag-3-line me-1 text-primary"></i> 1. Sales & Orders
                </button>
            </li>

            <!-- Tab 2: Finance & HR -->
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-3 py-2 text-nowrap" id="tab-finance-hr-tab" data-bs-toggle="tab" data-bs-target="#tab-finance-hr" type="button" role="tab" aria-controls="tab-finance-hr" aria-selected="false">
                    <i class="ri ri-bank-card-line me-1 text-success"></i> 2. Finance & HR
                </button>
            </li>

            <!-- Tab 3: Production -->
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-3 py-2 text-nowrap" id="tab-production-tab" data-bs-toggle="tab" data-bs-target="#tab-production" type="button" role="tab" aria-controls="tab-production" aria-selected="false">
                    <i class="ri ri-loader-line me-1 text-warning"></i> 3. Production
                </button>
            </li>

            <!-- Tab 4: Stock & Material -->
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-3 py-2 text-nowrap" id="tab-stock-material-tab" data-bs-toggle="tab" data-bs-target="#tab-stock-material" type="button" role="tab" aria-controls="tab-stock-material" aria-selected="false">
                    <i class="ri ri-stack-line me-1 text-info"></i> 4. Stock & Material
                    @if(isset($fabric_shortage_count) && $fabric_shortage_count > 0)
                        <span class="badge bg-warning text-dark ms-1">{{ $fabric_shortage_count }} Low</span>
                    @endif
                </button>
            </li>

            <!-- Tab 5: Suppliers & Maintenance -->
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold px-3 py-2 text-nowrap" id="tab-suppliers-maintenance-tab" data-bs-toggle="tab" data-bs-target="#tab-suppliers-maintenance" type="button" role="tab" aria-controls="tab-suppliers-maintenance" aria-selected="false">
                    <i class="ri ri-truck-line me-1 text-danger"></i> 5. Suppliers & Maintenance
                </button>
            </li>
        </ul>
    </div>

    <!-- 5-TAB CONTENT CONTAINER -->
    <div class="tab-content" id="erpDashboardTabsContent">

        <!-- ========================================================================================= -->
        <!-- TAB 1: SALES & ORDERS                                                                     -->
        <!-- ========================================================================================= -->
        <div class="tab-pane fade show active" id="tab-sales-orders" role="tabpanel" aria-labelledby="tab-sales-orders-tab">
            @if(auth()->id() == 1 || auth()->user()->can('view-sales-order dashboard'))
            <!-- Section 1: Sales & Order Dashboard KPIs -->
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="section-indicator bg-primary me-2"></div>
                    <h5 class="fw-bold mb-0">Sales & Order Overview</h5>
                </div>

                <div class="row g-3">
                    <div class="col-xl-3 col-md-6">
                        <div class="card kpi-widget border-0 shadow-sm border-start border-primary border-4 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted small fw-bold mb-1">Sales (Today)</p>
                                        <h4 class="mb-0 fw-bold text-dark">₹{{ number_format($sales_today, 2) }}</h4>
                                        <span class="text-muted small">{{ $sales_count_today }} Invoices</span>
                                    </div>
                                    <div class="kpi-icon bg-light-primary">
                                        <i class="ri ri-money-rupee-circle-line text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card kpi-widget border-0 shadow-sm border-start border-info border-4 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted small fw-bold mb-1">Sales (Month)</p>
                                        <h4 class="mb-0 fw-bold text-dark">₹{{ number_format($sales_month, 2) }}</h4>
                                        <span class="text-muted small">{{ $sales_count_month }} Invoices</span>
                                    </div>
                                    <div class="kpi-icon bg-light-info">
                                        <i class="ri ri-calendar-check-line text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card kpi-widget border-0 shadow-sm border-start border-success border-4 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted small fw-bold mb-1">Sales (Year)</p>
                                        <h4 class="mb-0 fw-bold text-dark">₹{{ number_format($sales_year, 2) }}</h4>
                                        <span class="text-muted small">{{ $sales_count_year }} Invoices</span>
                                    </div>
                                    <div class="kpi-icon bg-light-success">
                                        <i class="ri ri-line-chart-line text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card kpi-widget border-0 shadow-sm border-start border-warning border-4 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted small fw-bold mb-1">Orders (Today)</p>
                                        <h4 class="mb-0 fw-bold text-dark">{{ $orders_today }}</h4>
                                        <span class="text-muted small">New Bookings</span>
                                    </div>
                                    <div class="kpi-icon bg-light-warning">
                                        <i class="ri ri-shopping-bag-3-line text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card kpi-widget border-0 shadow-sm border-start border-secondary border-4 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted small fw-bold mb-1">Orders (Month)</p>
                                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($orders_month) }}</h4>
                                        <span class="text-muted small">Confirmed</span>
                                    </div>
                                    <div class="kpi-icon bg-light-secondary">
                                        <i class="ri ri-archive-line text-secondary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card kpi-widget border-0 shadow-sm border-start border-dark border-4 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted small fw-bold mb-1">Finished Stock</p>
                                        <h4 class="mb-0 fw-bold text-dark">{{ number_format($total_stock) }}</h4>
                                        <span class="text-muted small">Items in Hand</span>
                                    </div>
                                    <div class="kpi-icon bg-light-dark">
                                        <i class="ri ri-stack-line text-dark"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card kpi-widget border-0 shadow-sm border-start border-danger border-4 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted small fw-bold mb-1">Urgent Orders</p>
                                        <h4 class="mb-0 fw-bold text-danger">{{ $urgent_orders }}</h4>
                                        <span class="text-danger small fw-bold"><i class="ri-alert-line"></i> Action Required</span>
                                    </div>
                                    <div class="kpi-icon bg-light-danger">
                                        <i class="ri ri-error-warning-line text-danger"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card kpi-widget border-0 shadow-sm border-start border-warning border-4 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted small fw-bold mb-1">Missed Order Revenue</p>
                                        <h4 class="mb-0 fw-bold text-warning">₹{{ number_format($year_missed_value, 2) }}</h4>
                                        <span class="text-muted small">{{ number_format($year_missed_qty) }} Items Pending</span>
                                    </div>
                                    <div class="kpi-icon bg-light-warning">
                                        <i class="ri ri-money-rupee-circle-line text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Executive Operations & Fulfillment KPI Summary -->
            @include('dashboard.kpi_summary_card')

            <!-- Section 2: Sales, Returns & Net Sales Summary Matrix -->
            @include('reports.sales_summary_kpi')

            <!-- Section 3: Month-wise Comparison Trends -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="ri ri-line-chart-line me-2 text-primary"></i>Sales, Collection, Purchase & Payment Trends
                    </h6>
                    <span class="badge bg-light text-muted border px-2 py-1 small">Past 12 Months</span>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-6 border-end">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0 text-dark">Sales vs Collection (Month-wise)</h6>
                                <span class="badge bg-label-success">Collection Perf: {{ $collection_performance }}%</span>
                            </div>
                            <div style="height: 280px;">
                                <canvas id="salesCollectionChart"></canvas>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="d-flex justify-content-between align-items-center mb-2 ps-lg-2">
                                <h6 class="fw-bold mb-0 text-dark">Purchase vs Payment (Month-wise)</h6>
                                <span class="badge bg-label-primary">Total: ₹{{ formatIndianCurrency($total_purchase) }}</span>
                            </div>
                            <div style="height: 280px;" class="ps-lg-2">
                                <canvas id="purchasePaymentChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-soft-warning">You do not have permission to view the Sales & Order Dashboard.</div>
            @endif
        </div> <!-- /#tab-sales-orders -->

        <!-- ========================================================================================= -->
        <!-- TAB 2: FINANCE & HR                                                                       -->
        <!-- ========================================================================================= -->
        <div class="tab-pane fade" id="tab-finance-hr" role="tabpanel" aria-labelledby="tab-finance-hr-tab">
            <!-- PART A: ACCOUNTS & FINANCIAL DASHBOARD -->
            @if(auth()->id() == 1 || auth()->user()->can('view-accounts-financial dashboard'))
            <div class="mb-5">
                <div class="d-flex align-items-center mb-3">
                    <div class="section-indicator bg-success me-2"></div>
                    <h5 class="fw-bold mb-0">Accounts & Financial Dashboard</h5>
                </div>

                <!-- Financial KPI Grid -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card financial-stat border-0 shadow-sm p-3 border-top-primary h-100">
                            <p class="text-muted small mb-1 fw-bold">Total Sales Val.</p>
                            <h5 class="mb-0 fw-bold text-dark">₹{{ formatIndianCurrency($total_sales_value) }}</h5>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card financial-stat border-0 shadow-sm p-3 border-top-danger h-100">
                            <p class="text-muted small mb-1 fw-bold">Total Sales Return</p>
                            <h5 class="mb-0 fw-bold text-danger">₹{{ formatIndianCurrency($sales_return) }}</h5>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card financial-stat border-0 shadow-sm p-3 border-top-primary h-100">
                            <p class="text-muted small mb-1 fw-bold">Total Debtors (Receivable)</p>
                            <h5 class="mb-0 fw-bold text-primary">₹{{ formatIndianCurrency($total_debtors) }}</h5>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card financial-stat border-0 shadow-sm p-3 border-top-primary h-100">
                            <p class="text-muted small mb-1 fw-bold">Bill Disc. ({{ $bill_discount_percent }}%)</p>
                            <h5 class="mb-0 fw-bold text-dark">₹{{ formatIndianCurrency($bill_discount) }}</h5>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card financial-stat border-0 shadow-sm p-3 border-top-primary h-100">
                            <p class="text-muted small mb-1 fw-bold">Cash Disc. ({{ $cash_discount_percent }}%)</p>
                            <h5 class="mb-0 fw-bold text-dark">₹{{ formatIndianCurrency($cash_discount) }}</h5>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card financial-stat border-0 shadow-sm p-3 border-top-dark h-100">
                            <p class="text-muted small mb-1 fw-bold">Total Purchase</p>
                            <h5 class="mb-0 fw-bold text-navy">₹{{ formatIndianCurrency($total_purchase) }}</h5>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card financial-stat border-0 shadow-sm p-3 border-top-danger h-100">
                            <p class="text-muted small mb-1 fw-bold">Purchase Return</p>
                            <h5 class="mb-0 fw-bold text-danger">₹{{ formatIndianCurrency($purchase_return) }}</h5>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card financial-stat border-0 shadow-sm p-3 border-top-danger h-100">
                            <p class="text-muted small mb-1 fw-bold">Total Creditors (Payable)</p>
                            <h5 class="mb-0 fw-bold text-danger">₹{{ formatIndianCurrency($total_creditors) }}</h5>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <!-- Debtors Outstanding & Aging -->
                    <div class="col-lg-12">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold text-primary"><i class="ri ri-team-line me-2"></i>Debtors Outstanding & Aging Report</h6>
                                <span class="badge bg-light-primary text-primary">Receivables Bucket</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light sticky-top">
                                            <tr>
                                                <th class="small">Zone</th>
                                                <th class="small">Total Outstanding (₹)</th>
                                                <th class="small text-success">0-30 Days</th>
                                                <th class="small">31-60 Days</th>
                                                <th class="small text-warning">61-90 Days</th>
                                                <th class="small text-danger">Above 90 Days</th>
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
                                                <td colspan="6" class="text-center text-muted py-3">No pending debtors found for any zone.</td>
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
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold text-danger"><i class="ri ri-store-line me-2"></i>Creditors Outstanding & Aging Report</h6>
                                <span class="badge bg-light-danger text-danger">Payables Bucket</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light sticky-top">
                                            <tr>
                                                <th class="small">Supplier</th>
                                                <th class="small">Total Payable (₹)</th>
                                                <th class="small text-success">0-30 Days</th>
                                                <th class="small">31-60 Days</th>
                                                <th class="small text-warning">61-90 Days</th>
                                                <th class="small text-danger">Above 90 Days</th>
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
                                                <td colspan="6" class="text-center text-muted py-3">No pending creditors found.</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Itemwise Stock Value Breakdown -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-3">
                                <h6 class="mb-0 fw-bold"><i class="ri ri-pie-chart-line me-2"></i>Item-wise Stock Value</h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush mb-3">
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 border-0">
                                        <div><i class="ri ri-checkbox-blank-circle-fill text-primary me-2"></i>Fabric</div>
                                        <span class="fw-bold">₹{{ formatIndianCurrency($fabric_value) }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 border-0">
                                        <div><i class="ri ri-checkbox-blank-circle-fill text-info me-2"></i>Accessories</div>
                                        <span class="fw-bold">₹{{ formatIndianCurrency($accessories_value) }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 border-0">
                                        <div><i class="ri ri-checkbox-blank-circle-fill text-warning me-2"></i>WIP</div>
                                        <span class="fw-bold">₹{{ formatIndianCurrency($wip_value) }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 border-0">
                                        <div><i class="ri ri-checkbox-blank-circle-fill text-success me-2"></i>Finished Goods</div>
                                        <span class="fw-bold">₹{{ formatIndianCurrency($finished_goods_value) }}</span>
                                    </li>
                                </ul>
                                <div style="height: 190px;">
                                    <canvas id="stockDistributionChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- PART B: EMPLOYEE'S ATTENDANCE DASHBOARD -->
            @if(auth()->id() == 1 || auth()->user()->can('view-attendance dashboard'))
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="section-indicator bg-secondary me-2"></div>
                    <h5 class="fw-bold mb-0">Employee's Attendance & Workforce</h5>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-xl col-md-4 col-sm-6">
                        <div class="card attendance-card border-0 shadow-sm h-100" style="border-left: 4px solid #0d6efd !important;">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="small text-muted mb-1 fw-bold">Total Active Staff</p>
                                    <h2 class="fw-bold text-primary mb-0">{{ $total_emp ?? 0 }}</h2>
                                </div>
                                <div class="kpi-icon bg-light-primary">
                                    <i class="ri ri-team-line text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl col-md-4 col-sm-6">
                        <div class="card attendance-card border-0 shadow-sm h-100" style="border-left: 4px solid #28a745 !important;">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="small text-muted mb-1 fw-bold">Present Today</p>
                                    <h2 class="fw-bold text-success mb-0">{{ $present_emp_today }}</h2>
                                </div>
                                <div class="kpi-icon bg-light-success">
                                    <i class="ri ri-user-smile-line text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl col-md-4 col-sm-6">
                        <div class="card attendance-card border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545 !important;">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="small text-muted mb-1 fw-bold">Absent Today</p>
                                    <h2 class="fw-bold text-danger mb-0">{{ $absent_emp_today }}</h2>
                                </div>
                                <div class="kpi-icon bg-light-danger">
                                    <i class="ri ri-user-unfollow-line text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl col-md-4 col-sm-6">
                        <div class="card attendance-card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important;">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="small text-muted mb-1 fw-bold">Late Employees</p>
                                    <h2 class="fw-bold text-warning mb-0">{{ $late_emp_today }}</h2>
                                </div>
                                <div class="kpi-icon bg-light-warning">
                                    <i class="ri ri-time-line text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl col-md-4 col-sm-6">
                        <div class="card attendance-card border-0 shadow-sm h-100" style="border-left: 4px solid #0dcaf0 !important;">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="small text-muted mb-1 fw-bold">Overtime Staff</p>
                                    <h2 class="fw-bold text-info mb-0">{{ $overtime_today }}</h2>
                                </div>
                                <div class="kpi-icon bg-light-info">
                                    <i class="ri ri-alarm-warning-line text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-xl-6 col-lg-8 col-md-12">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold text-dark"><i class="ri-pie-chart-2-fill text-danger me-2"></i>Attendance Summary & Device Filter</h6>
                                <select id="attendanceDeviceFilter" class="form-select form-select-sm w-auto shadow-none">
                                    <option value="All">All Devices</option>
                                    @foreach($dbDevices as $device)
                                        <option value="{{ $device->device_name ?: $device->serial_number }}">{{ $device->device_name ?: $device->serial_number }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="card-body">
                                <div style="height: 280px; position: relative;">
                                    <canvas id="attendanceSummaryChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div> <!-- /#tab-finance-hr -->

        <!-- ========================================================================================= -->
        <!-- TAB 3: PRODUCTION                                                                         -->
        <!-- ========================================================================================= -->
        <div class="tab-pane fade" id="tab-production" role="tabpanel" aria-labelledby="tab-production-tab">
            @if(auth()->id() == 1 || auth()->user()->can('view-production dashboard'))
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="section-indicator bg-warning me-2"></div>
                    <h5 class="fw-bold mb-0">Production Operations & Job Card Tracking</h5>
                </div>

                <!-- Production KPI / Summary Row -->
                <div class="row g-4 mb-4">
                    <!-- 1. Production Target vs Actual & Efficiency -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold"><i class="ri ri-focus-3-line me-2 text-primary"></i>Production Target Status</h6>
                                <span class="badge {{ $production_efficiency >= 80 ? 'bg-success' : ($production_efficiency >= 50 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                    {{ $production_efficiency }}% Efficiency
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="mb-3 text-center">
                                    <h2 class="fw-bold mb-0 text-primary">{{ $production_efficiency }}%</h2>
                                    <p class="text-muted small text-uppercase fw-bold mb-0">Monthly Efficiency</p>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small fw-semibold">Plan vs Achieved</span>
                                        <span class="small fw-bold">{{ number_format($production_achieved_qty ?: 0) }} / {{ number_format($production_plan_qty ?: 0) }}</span>
                                    </div>
                                    <div class="progress" style="height: 12px;">
                                        <div class="progress-bar {{ $production_efficiency >= 80 ? 'bg-success' : ($production_efficiency >= 50 ? 'bg-warning' : 'bg-danger') }}" role="progressbar" style="width: {{ min(100, $production_efficiency) }}%"></div>
                                    </div>
                                </div>
                                <div class="p-3 bg-light rounded text-center border">
                                    <p class="text-muted small mb-1 fw-semibold">Pending Production Qty</p>
                                    <h4 class="mb-0 fw-bold text-dark">{{ number_format(max(0, $production_plan_qty - ($production_achieved_qty ?: 0))) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Production Delivery Days Overdue -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm h-100 border-top border-danger border-3">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold text-danger"><i class="ri ri-time-line me-2"></i>Delivery Days Overdue</h6>
                                <span class="badge bg-soft-danger text-danger">{{ $delivery_overdue->count() }} Overdue</span>
                            </div>
                            <div class="card-body p-0" style="max-height: 280px; overflow-y: auto;">
                                <div class="list-group list-group-flush">
                                    @if($delivery_overdue->count() > 0)
                                        @foreach($delivery_overdue as $od)
                                        <div class="list-group-item py-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="small fw-bold text-primary">{{ $od->job_card_no }}</span>
                                                <span class="badge bg-danger text-white">{{ $od->overdue_days }} Days Overdue</span>
                                            </div>
                                            <p class="text-muted x-small mb-0">Target: {{ date('d-M-y', strtotime($od->delivery_date)) }} | Qty: {{ number_format($od->grand_total_qty) }}</p>
                                        </div>
                                        @endforeach
                                    @else
                                        <div class="list-group-item py-4 text-center text-muted">
                                            <i class="ri ri-checkbox-circle-line fs-2 text-success"></i><br>
                                            <span class="text-success fw-semibold">No Overdue Job Cards</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Process Wise Status -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-3">
                                <h6 class="mb-0 fw-bold"><i class="ri ri-flow-chart me-2 text-info"></i>Process Wise Status</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                                    <table class="table table-sm table-hover mb-0 align-middle">
                                        <thead class="bg-light sticky-top">
                                            <tr>
                                                <th class="small">Process</th>
                                                <th class="small text-center text-success">Done</th>
                                                <th class="small text-center text-warning">IP</th>
                                                <th class="small text-center text-danger">Planned</th>
                                            </tr>
                                        </thead>
                                        <tbody class="small">
                                            @if($process_wise_status->count() > 0)
                                                @foreach($process_wise_status as $ps)
                                                <tr>
                                                    <td><strong>{{ $ps->operation_stage_name }}</strong></td>
                                                    <td class="text-center text-success fw-bold">{{ $ps->completed ?: 0 }}</td>
                                                    <td class="text-center text-warning fw-bold">{{ $ps->in_progress ?: 0 }}</td>
                                                    <td class="text-center text-danger fw-bold">{{ $ps->planned ?: 0 }}</td>
                                                </tr>
                                                @endforeach
                                            @else
                                                <tr><td colspan="4" class="text-center text-muted py-3">No process data recorded</td></tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- WIP & Production Cost Row -->
                <div class="row g-4">
                    <!-- 1. Production WIP (Unit Wise) -->
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold"><i class="ri ri-loader-line me-2 text-primary"></i>Production WIP (Unit Wise)</h6>
                                <div class="search-box">
                                    <input type="text" id="wipSearchInput" class="form-control form-control-sm" placeholder="Search Process...">
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light sticky-top">
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
                                    <strong>Note:</strong> Stages show if work is in progress or a task is assigned. Completed stages are hidden to keep overview clean.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Production Cost (Unit Wise) -->
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold"><i class="ri ri-money-rupee-circle-line me-2 text-success"></i>Production Cost (Unit Wise)</h6>
                                <div class="search-box">
                                    <input type="text" id="costSearchInput" class="form-control form-control-sm" placeholder="Search Job Card...">
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <p class="text-muted small fw-bold mb-1">Total WIP Material Cost</p>
                                <h3 class="mb-3 fw-bold text-primary">₹{{ number_format($wip_value, 2) }}</h3>
                                <div class="table-responsive w-100 mt-2" style="max-height: 280px; overflow-y: auto;">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="bg-light sticky-top">
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
                                                <td class="text-end fw-bold text-dark">₹{{ number_format($cost->total_cost, 2) }}</td>
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
                </div>
            </div>
            @else
            <div class="alert alert-soft-warning">You do not have permission to view the Production Dashboard.</div>
            @endif
        </div> <!-- /#tab-production -->

        <!-- ========================================================================================= -->
        <!-- TAB 4: STOCK & MATERIAL                                                                   -->
        <!-- ========================================================================================= -->
        <div class="tab-pane fade" id="tab-stock-material" role="tabpanel" aria-labelledby="tab-stock-material-tab">
            @if(auth()->id() == 1 || auth()->user()->can('view purchase-report') || auth()->user()->can('view warehouse-report') || auth()->user()->can('view stock-entry-raw-materials'))
            <!-- Section 1: Fabric & Store Stock Dashboard -->
            <div class="mb-5">
                <div class="d-flex align-items-center mb-3">
                    <div class="section-indicator bg-info me-2"></div>
                    <h5 class="fw-bold mb-0">Fabric & Store Stock Overview</h5>
                </div>

                <!-- KPI Grid -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-md-6">
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

                    <div class="col-xl-3 col-md-6">
                        <div class="card kpi-widget border-0 shadow-sm border-start border-success border-4 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted small fw-bold mb-1">Total Stock Value</p>
                                        <h4 class="mb-0 fw-bold">₹{{ formatIndianCurrency($total_fabric_stock_val) }}</h4>
                                        <span class="text-muted small">Fabric Valuation</span>
                                    </div>
                                    <div class="kpi-icon bg-light-success">
                                        <i class="ri ri-money-rupee-circle-line text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card kpi-widget border-0 shadow-sm border-start border-warning border-4 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted small fw-bold mb-1">Shortage Brands</p>
                                        <h4 class="mb-0 fw-bold text-warning">{{ $fabric_shortage_count }}</h4>
                                        <span class="text-muted small">{{ $fabric_shortage_count > 0 ? 'Below Min Req (Reorder)' : 'All Stocks Healthy' }}</span>
                                    </div>
                                    <div class="kpi-icon bg-light-warning">
                                        <i class="ri ri-alert-line text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
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
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">
                                <i class="ri ri-store-2-line me-2 text-info"></i>Fabric Inventory Dashboard
                            </h6>
                            <small class="text-muted">Consolidated overview of fabric stock, valuation, warehouse ageing, and shortage/excess</small>
                        </div>
                        <div class="search-box">
                            <input type="text" id="fabricStockSearchInput" class="form-control form-control-sm" placeholder="Search Brand..." style="width: 220px;">
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 440px; overflow-y: auto;">
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
                                        <th class="text-center">STATUS</th>
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
                                                    <span class="badge bg-warning text-dark fw-bold px-2 py-1">
                                                        {{ number_format($row['shortage'], 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($row['excess'] > 0)
                                                    <span class="badge bg-danger text-white fw-bold px-2 py-1">
                                                        {{ number_format($row['excess'], 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($row['shortage'] > 0)
                                                    <span class="badge bg-warning text-dark"><i class="ri-alert-line"></i> Reorder</span>
                                                @elseif($row['excess'] > 0)
                                                    <span class="badge bg-danger text-white">Excess</span>
                                                @else
                                                    <span class="badge bg-success text-white">Optimal</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">No fabric stock data available.</td>
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
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Core Material Planner Dashboard -->
            <div class="mb-5" id="coreMaterialPlannerSection">
                <div class="d-flex align-items-center mb-3">
                    <div class="section-indicator bg-warning me-2"></div>
                    <h5 class="fw-bold mb-0">Core Material Planner & Pipeline</h5>
                </div>

                <!-- KPI Grid -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-md-6">
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

                    <div class="col-xl-3 col-md-6">
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

                    <div class="col-xl-3 col-md-6">
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

                    <div class="col-xl-3 col-md-6">
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
                <div id="corePlannerContainer" class="card border-0 shadow-sm mb-5">
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
                                        <th colspan="4" class="text-end">TOTAL:</th>
                                        <th id="coreFootStock" class="text-end text-dark">0.00</th>
                                        <th id="coreFootWip" class="text-end text-warning">0.00</th>
                                        <th id="coreFootFg" class="text-end text-success">0 pcs</th>
                                        <th id="coreFootPipeline" class="text-end text-primary">0.00</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Fabric Utilisation Dashboard -->
            <div class="mb-4" id="fabricUtilisationSection">
                <div class="d-flex align-items-center mb-3">
                    <div class="section-indicator bg-success me-2"></div>
                    <h5 class="fw-bold mb-0">Fabric Utilisation Dashboard</h5>
                </div>

                <!-- KPI Grid -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-md-6">
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

                    <div class="col-xl-3 col-md-6">
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

                    <div class="col-xl-3 col-md-6">
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

                    <div class="col-xl-3 col-md-6">
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
                        <div class="table-responsive" style="max-height: 440px; overflow-y: auto;">
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
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
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
            @else
            <div class="alert alert-soft-warning">You do not have permission to view the Stock & Material Dashboard.</div>
            @endif
        </div> <!-- /#tab-stock-material -->

        <!-- ========================================================================================= -->
        <!-- TAB 5: SUPPLIERS & MAINTENANCE                                                            -->
        <!-- ========================================================================================= -->
        <div class="tab-pane fade" id="tab-suppliers-maintenance" role="tabpanel" aria-labelledby="tab-suppliers-maintenance-tab">
            <!-- SECTION 1: SUPPLIER PERFORMANCE DASHBOARD -->
            @if(auth()->id() == 1 || auth()->user()->can('view purchase-report') || auth()->user()->can('view suppliers'))
            <div class="mb-5" id="supplierPerformanceSection">
                <div class="d-flex align-items-center mb-3">
                    <div class="section-indicator bg-info me-2"></div>
                    <h5 class="fw-bold mb-0">Supplier Reliability & Delivery Performance</h5>
                </div>

                <!-- KPI Grid -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card kpi-widget border-0 shadow-sm border-start border-primary border-4 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted small fw-bold mb-1">Active Suppliers</p>
                                        <h4 class="mb-0 fw-bold text-dark">{{ $supplier_perf_count }}</h4>
                                        <span class="text-muted small">With Purchase Orders</span>
                                    </div>
                                    <div class="kpi-icon bg-light-primary">
                                        <i class="ri ri-store-3-line text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card kpi-widget border-0 shadow-sm border-start border-success border-4 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted small fw-bold mb-1">Total Purchase Spend</p>
                                        <h4 class="mb-0 fw-bold text-success">₹{{ number_format($supplier_perf_spend, 2) }}</h4>
                                        <span class="text-muted small">Cumulative Order Spend</span>
                                    </div>
                                    <div class="kpi-icon bg-light-success">
                                        <i class="ri ri-money-rupee-circle-line text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card kpi-widget border-0 shadow-sm border-start {{ $supplier_perf_ontime >= 90 ? 'border-success' : ($supplier_perf_ontime >= 75 ? 'border-warning' : 'border-danger') }} border-4 h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted small fw-bold mb-1">Fleet On-Time Rate</p>
                                        <h4 class="mb-0 fw-bold {{ $supplier_perf_ontime >= 90 ? 'text-success' : ($supplier_perf_ontime >= 75 ? 'text-warning' : 'text-danger') }}">{{ $supplier_perf_ontime }}%</h4>
                                        <span class="text-muted small">Delivered By Due Date</span>
                                    </div>
                                    <div class="kpi-icon bg-light-warning">
                                        <i class="ri ri-time-line text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
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
                <div id="supplierPerformanceContainer" class="card border-0 shadow-sm mb-5">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="ri ri-truck-line me-2 text-info"></i>Supplier Reliability & Performance Tracker
                        </h6>
                        <small class="text-muted">Supplier reliability, delivery delay analysis, and return rates</small>
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
            @endif

            <!-- SECTION 2: MAINTENANCE & COMPLIANCE DASHBOARD -->
            @if(auth()->id() == 1 || auth()->user()->can('view-maintenance dashboard'))
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="section-indicator bg-danger me-2"></div>
                    <h5 class="fw-bold mb-0">Maintenance, Service & Statutory Compliance</h5>
                </div>
                <div class="row g-4">
                    <!-- Renewals & Compliance Table -->
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold text-navy"><i class="ri ri-shield-check-line me-2 text-primary"></i>Renewals & Statutory Compliance</h6>
                                <span class="badge bg-light text-muted border">Next 90 Days</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light sticky-top">
                                            <tr>
                                                <th class="small font-weight-bold">Renewal Item / Document</th>
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
                                                        <span class="badge bg-danger text-white border px-2 py-1"><i class="ri-close-circle-line me-1"></i> Expired</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark border px-2 py-1"><i class="ri-time-line me-1"></i> {{ $daysLeft }} Days Left</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr>
                                                <td colspan="3" class="text-center py-4 text-muted small">
                                                    <i class="ri-checkbox-circle-line text-success fs-3"></i><br>
                                                    All renewals up to date. No upcoming expirations in the next 90 days.
                                                </td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Maintenance Requirements Log -->
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-3">
                                <h6 class="mb-0 fw-bold text-navy"><i class="ri ri-tools-line me-2 text-danger"></i>Maintenance Ticket Status</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush list-group-sm">
                                    <div class="list-group-item border-0 py-3 bg-light-danger-soft">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="small fw-bold text-dark d-block">Requirements Raised</span>
                                                <small class="text-muted">Total maintenance tickets registered</small>
                                            </div>
                                            <span class="badge bg-primary rounded-pill fs-6 px-3 py-2">{{ $maintenance_raised }}</span>
                                        </div>
                                    </div>
                                    <div class="list-group-item border-0 py-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="small fw-bold text-dark d-block">Requirements Attended</span>
                                                <small class="text-muted">Repairs completed & resolved</small>
                                            </div>
                                            <span class="badge bg-success rounded-pill fs-6 px-3 py-2">{{ $maintenance_attended }}</span>
                                        </div>
                                    </div>
                                    <div class="list-group-item border-0 py-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="small fw-bold text-danger d-block">Requirements Pending</span>
                                                <small class="text-danger">Action required by maintenance team</small>
                                            </div>
                                            <span class="badge bg-danger rounded-pill fs-6 px-3 py-2">{{ $maintenance_pending }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div> <!-- /#tab-suppliers-maintenance -->

    </div> <!-- /#erpDashboardTabsContent -->
</div> <!-- /.container-xxl -->

<style>
    /* 5-Tab Segmented Navigation Styling */
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
        cursor: pointer;
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

    /* Tab Pane Visibility: Strict Isolation */
    .tab-content > .tab-pane {
        display: none;
    }
    .tab-content > .tab-pane.active {
        display: block !important;
    }

    .section-indicator {
        width: 12px;
        height: 24px;
        border-radius: 4px;
    }

    .kpi-widget {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .kpi-widget:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
    }
    .kpi-icon {
        width: 46px;
        height: 46px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.45rem;
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
    .badge.bg-label-info { background: #e0f2fe; color: #0369a1; }
    .badge.bg-label-secondary { background: #f1f5f9; color: #475569; }

    .bg-soft-danger { background-color: #fef2f2; }
    .bg-soft-warning { background-color: #fffbeb; }
    .bg-soft-info { background-color: #eff6ff; }
    .bg-light-danger-soft { background-color: #fff5f5; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Robust Tab Controller & Auto-Resizing
    function activateErpTab(targetId) {
        if (!targetId) return;
        if (!targetId.startsWith('#')) targetId = '#' + targetId;
        
        var $targetPane = $(targetId);
        if (!$targetPane.length) return;
        
        // 1. Update tab buttons
        $('#dashboardTabs .nav-link').removeClass('active').attr('aria-selected', 'false');
        var $activeBtn = $('[data-bs-target="' + targetId + '"]');
        if ($activeBtn.length) {
            $activeBtn.addClass('active').attr('aria-selected', 'true');
            var tabName = $activeBtn.text().trim();
            $('#breadcrumbActiveTab').text(tabName);
        }
        
        // 2. Switch tab panes with display isolation
        $('#erpDashboardTabsContent > .tab-pane').removeClass('show active').css('display', 'none');
        $targetPane.addClass('show active').css('display', 'block');
        
        // 3. Store in localStorage & history
        try {
            localStorage.setItem('activeErpTab', targetId);
            if (window.history && window.history.replaceState) {
                window.history.replaceState(null, null, targetId);
            }
        } catch (e) {}
        
        // 4. Trigger resize for Chart.js & DataTables
        setTimeout(function() {
            window.dispatchEvent(new Event('resize'));
            if ($.fn.DataTable) {
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust().responsive.recalc();
            }
        }, 60);
    }

    $(document).ready(function() {
        // Tab Click Delegation (supports native BS5 + jQuery fallback)
        $(document).on('click', '#dashboardTabs button, #dashboardTabs a', function(e) {
            e.preventDefault();
            var target = $(this).attr('data-bs-target') || $(this).attr('href');
            activateErpTab(target);
        });

        // Initialize active tab from URL hash or localStorage
        var initialTab = window.location.hash || localStorage.getItem('activeErpTab') || '#tab-sales-orders';
        if (!$(initialTab).length) initialTab = '#tab-sales-orders';
        activateErpTab(initialTab);

        // WIP table search
        const wipSearchInput = document.getElementById('wipSearchInput');
        if (wipSearchInput) {
            wipSearchInput.addEventListener('keyup', function() {
                const value = this.value.toLowerCase();
                const rows = document.querySelectorAll('.wip-row');
                rows.forEach(row => {
                    const text = row.querySelector('.wip-process-name')?.textContent.toLowerCase() || '';
                    row.style.display = text.includes(value) ? '' : 'none';
                });
            });
        }

        // Cost table search
        const costSearchInput = document.getElementById('costSearchInput');
        if (costSearchInput) {
            costSearchInput.addEventListener('keyup', function() {
                const value = this.value.toLowerCase();
                const rows = document.querySelectorAll('.cost-row');
                rows.forEach(row => {
                    const text = row.querySelector('.cost-jc-no')?.textContent.toLowerCase() || '';
                    row.style.display = text.includes(value) ? '' : 'none';
                });
            });
        }

        // Fabric stock search
        const fabricSearchInput = document.getElementById('fabricStockSearchInput');
        if (fabricSearchInput) {
            fabricSearchInput.addEventListener('keyup', function() {
                const term = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('#fabricStockTable tbody tr.fabric-stock-row');
                rows.forEach(function(r) {
                    const brandText = r.querySelector('.fabric-brand-name')?.textContent.toLowerCase() || '';
                    r.style.display = brandText.includes(term) ? '' : 'none';
                });
            });
        }

        // Chart defaults
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#64748b';

        // 1. Stock Distribution Chart (Finance & HR Tab)
        const ctxStock = document.getElementById('stockDistributionChart');
        if (ctxStock) {
            new Chart(ctxStock, {
                type: 'doughnut',
                data: {
                    labels: ['Fabric', 'Accessories', 'WIP', 'Finished Goods'],
                    datasets: [{
                        data: [{{ $fabric_value }}, {{ $accessories_value }}, {{ $wip_value }}, {{ $finished_goods_value }}],
                        backgroundColor: ['#1e3a8a', '#06b6d4', '#f59e0b', '#10b981'],
                        borderWidth: 0,
                        hoverOffset: 8
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
        }

        // 2. Sales vs Collection Chart (Sales & Orders Tab)
        const ctxSales = document.getElementById('salesCollectionChart');
        if (ctxSales) {
            new Chart(ctxSales, {
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
                        legend: { position: 'top', align: 'end', labels: { boxWidth: 8, usePointStyle: true, padding: 15 } },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let val = context.parsed.y || 0;
                                    return ' ' + context.dataset.label + ': ₹' + Number(val).toLocaleString('en-IN');
                                }
                            }
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { color: '#f1f5f9' }, 
                            border: { display: false },
                            ticks: {
                                callback: function(value) {
                                    if (value >= 10000000) return '₹' + (value / 10000000).toFixed(1) + ' Cr';
                                    if (value >= 100000) return '₹' + (value / 100000).toFixed(1) + ' L';
                                    if (value >= 1000) return '₹' + (value / 1000).toFixed(0) + ' k';
                                    return '₹' + value;
                                }
                            }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // 3. Purchase vs Payment Chart (Sales & Orders Tab)
        const ctxPurchase = document.getElementById('purchasePaymentChart');
        if (ctxPurchase) {
            new Chart(ctxPurchase, {
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
                        legend: { position: 'top', align: 'end', labels: { boxWidth: 8, usePointStyle: true, padding: 15 } },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let val = context.parsed.y || 0;
                                    return ' ' + context.dataset.label + ': ₹' + Number(val).toLocaleString('en-IN');
                                }
                            }
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { color: '#f1f5f9' }, 
                            border: { display: false },
                            ticks: {
                                callback: function(value) {
                                    if (value >= 10000000) return '₹' + (value / 10000000).toFixed(1) + ' Cr';
                                    if (value >= 100000) return '₹' + (value / 100000).toFixed(1) + ' L';
                                    if (value >= 1000) return '₹' + (value / 1000).toFixed(0) + ' k';
                                    return '₹' + value;
                                }
                            }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // 4. Attendance Summary Chart (Finance & HR Tab)
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
                                    boxWidth: 20,
                                    padding: 15,
                                    generateLabels: function(chart) {
                                        const dataset = chart.data.datasets[0];
                                        const total = dataset.data.reduce((a, b) => a + b, 0);
                                        return chart.data.labels.map((label, i) => {
                                            const value = dataset.data[i];
                                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
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
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return ` ${context.label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            };

            initAttendanceChart('All');

            const deviceFilter = document.getElementById('attendanceDeviceFilter');
            if (deviceFilter) {
                deviceFilter.addEventListener('change', function() {
                    initAttendanceChart(this.value);
                });
            }
        }

        // 5. Core Material Planner DataTable (Stock & Material Tab)
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
                    { data: 'DT_RowIndex', orderable: false, searchable: false, width: '45px', defaultContent: '' },
                    { data: 'art_no', name: 'art_no', defaultContent: '' },
                    { data: 'item_name', name: 'item_name', defaultContent: '' },
                    { data: 'brand_name', name: 'brand_name', defaultContent: '' },
                    { data: 'stock', name: 'stock', className: 'text-end', defaultContent: '0.00' },
                    { data: 'wip', name: 'wip', className: 'text-end', defaultContent: '0.00' },
                    { data: 'fg', name: 'fg', className: 'text-end', defaultContent: '0 pcs' },
                    { 
                        data: 'total_pipeline', 
                        name: 'total_pipeline', 
                        className: 'text-end', 
                        defaultContent: '0.00',
                        render: function(data, type, row) {
                            return data || row.pipeline || '0.00';
                        }
                    }
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

        // 6. Supplier Performance DataTable (Suppliers & Maintenance Tab)
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
                    { data: 'DT_RowIndex', orderable: false, searchable: false, width: '45px', defaultContent: '' },
                    { data: 'supplier', name: 'supplier', defaultContent: '' },
                    { data: 'purchase_value', name: 'purchase_value', className: 'text-end', defaultContent: '0.00' },
                    { data: 'orders', name: 'orders', className: 'text-center', defaultContent: '0' },
                    { data: 'on_time_pct', name: 'on_time_pct', className: 'text-center', defaultContent: '0%' },
                    { data: 'avg_delay', name: 'avg_delay', className: 'text-center', defaultContent: '0 Days' },
                    { data: 'returns', name: 'returns', className: 'text-center', defaultContent: '0' },
                    { data: 'overall_rating', name: 'overall_rating', className: 'text-center', defaultContent: '-' }
                ],
                dom: '<"row align-items-center mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row align-items-center mt-3"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"p>>',
                language: {
                    search: "",
                    searchPlaceholder: "Search Supplier Name...",
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
                }
            });
        }

        // 7. Fabric Utilisation Initial Load & Search
        loadFabricUtilisation(1);

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

    // AJAX Pagination & Drilldown Logic for Fabric Utilisation
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

                const elFrom = document.getElementById('utilPageFrom');
                const elTo = document.getElementById('utilPageTo');
                const elTotal = document.getElementById('utilPageTotal');
                if (elFrom) elFrom.textContent = res.from || 0;
                if (elTo) elTo.textContent = res.to || 0;
                if (elTotal) elTotal.textContent = res.total_records || 0;

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
                    <td class="text-muted small">${jc.remarks || '-'}</td>
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

        const jcSearch = document.getElementById('utilJobCardSearchInput');
        if (jcSearch) jcSearch.value = '';
    }

    function showUtilBrandLevel() {
        document.getElementById('utilBreadcrumbs').style.setProperty('display', 'none', 'important');
        document.getElementById('utilJobCardsContainer').style.display = 'none';
        document.getElementById('utilBrandContainer').style.display = 'block';
    }
</script>
@endsection

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
                                <p class="text-muted small fw-bold mb-1">Total Stock</p>
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
        </div>
    </div>
    @endif

    <!-- SECTION 2: ACCOUNTS DASHBOARD -->
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
                    <h5 class="mb-0 fw-bold">₹{{ number_format($total_sales_value, 2) }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card financial-stat border-0 shadow-sm p-3 border-top-danger">
                    <p class="text-muted small mb-1">Total Sales Return</p>
                    <h5 class="mb-0 fw-bold text-danger">₹{{ number_format($sales_return, 2) }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card financial-stat border-0 shadow-sm p-3 border-top-primary">
                    <p class="text-muted small mb-1">Total Debtors</p>
                    <h5 class="mb-0 fw-bold text-primary">₹{{ number_format($total_debtors, 2) }}</h5>
                </div>
            </div>
             <div class="col-md-3">
                <div class="card financial-stat border-0 shadow-sm p-3 border-top-primary">
                    <p class="text-muted small mb-1">Bill Disc. ({{ $bill_discount_percent }}%)</p>
                    <h5 class="mb-0 fw-bold">₹{{ number_format($bill_discount, 2) }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card financial-stat border-0 shadow-sm p-3 border-top-primary">
                    <p class="text-muted small mb-1">Cash Disc. ({{ $cash_discount_percent }}%)</p>
                    <h5 class="mb-0 fw-bold">₹{{ number_format($cash_discount, 2) }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card financial-stat border-0 shadow-sm p-3 border-top-dark">
                    <p class="text-muted small mb-1">Total Purchase</p>
                    <h5 class="mb-0 fw-bold text-navy">₹{{ number_format($total_purchase, 2) }}</h5>
                </div>
            </div>
             <div class="col-md-3">
                <div class="card financial-stat border-0 shadow-sm p-3 border-top-danger">
                    <p class="text-muted small mb-1">Purchase Return</p>
                    <h5 class="mb-0 fw-bold text-danger">₹{{ number_format($purchase_return, 2) }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card financial-stat border-0 shadow-sm p-3 border-top-danger">
                    <p class="text-muted small mb-1">Total Creditors</p>
                    <h5 class="mb-0 fw-bold text-danger">₹{{ number_format($total_creditors, 2) }}</h5>
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
                        <div class="table-responsive">
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
                                            <td>{{ number_format($row->total_due, 2) }}</td>
                                            <td class="text-success">{{ number_format($row->bucket_30, 2) }}</td>
                                            <td>{{ number_format($row->bucket_60, 2) }}</td>
                                            <td class="text-warning">{{ number_format($row->bucket_90, 2) }}</td>
                                            <td class="text-danger fw-bold">{{ number_format($row->bucket_above_90, 2) }}</td>
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
                        <div class="table-responsive">
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
                                            <td>{{ number_format($row->total_due, 2) }}</td>
                                            <td class="text-success">{{ number_format($row->bucket_30, 2) }}</td>
                                            <td>{{ number_format($row->bucket_60, 2) }}</td>
                                            <td class="text-warning">{{ number_format($row->bucket_90, 2) }}</td>
                                            <td class="text-danger fw-bold">{{ number_format($row->bucket_above_90, 2) }}</td>
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
                                <span class="fw-bold">₹{{ number_format($fabric_value, 2) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 border-0">
                                <div><i class="ri ri-checkbox-blank-circle-fill text-info me-2"></i>Accessories</div>
                                <span class="fw-bold">₹{{ number_format($accessories_value, 2) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 border-0">
                                <div><i class="ri ri-checkbox-blank-circle-fill text-warning me-2"></i>WIP</div>
                                <span class="fw-bold">₹{{ number_format($wip_value, 2) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 border-0">
                                <div><i class="ri ri-checkbox-blank-circle-fill text-success me-2"></i>Finished Goods</div>
                                <span class="fw-bold">₹{{ number_format($finished_goods_value, 2) }}</span>
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
                    <div class="card-body p-0">
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
</div>

<style>
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
    });
</script>
@endsection

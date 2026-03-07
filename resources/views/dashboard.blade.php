@extends('layouts.common')
@section('title', 'ERP Dashboard - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    
    <!-- Page Header -->
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
    <div class="mb-5">
        <div class="d-flex align-items-center mb-3">
            <div class="section-indicator bg-primary me-2"></div>
            <h5 class="fw-bold mb-0">Sales & Order Dashboard</h5>
        </div>
        <div class="row g-3">
            <!-- Sales KPI Cards -->
            <div class="col-md-3 col-lg-3">
                <div class="card kpi-widget border-0 shadow-sm border-start border-primary border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">Sales (Today)</p>
                                <h4 class="mb-0 fw-bold">₹1,24,500</h4>
                                <span class="text-success small"><i class="ri ri-arrow-up-s-line"></i> 8.5%</span>
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
                                <h4 class="mb-0 fw-bold">₹42,80,000</h4>
                                <span class="text-success small"><i class="ri ri-arrow-up-s-line"></i> 12.2%</span>
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
                                <h4 class="mb-0 fw-bold">₹5.24 Cr</h4>
                                <span class="text-success small"><i class="ri ri-arrow-up-s-line"></i> 15%</span>
                            </div>
                            <div class="kpi-icon bg-light-success">
                                <i class="ri ri-line-chart-line text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Order KPI Cards -->
            <div class="col-md-3 col-lg-3">
                <div class="card kpi-widget border-0 shadow-sm border-start border-warning border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small fw-bold mb-1">Orders (Today)</p>
                                <h4 class="mb-0 fw-bold">42</h4>
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
                                <h4 class="mb-0 fw-bold">1,120</h4>
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
                                <h4 class="mb-0 fw-bold">14,500</h4>
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
                                <h4 class="mb-0 fw-bold text-danger">85</h4>
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

    <!-- SECTION 2: ACCOUNTS DASHBOARD -->
    <div class="mb-5">
        <div class="d-flex align-items-center mb-3">
            <div class="section-indicator bg-success me-2"></div>
            <h5 class="fw-bold mb-0">Accounts & Financial Dashboard</h5>
        </div>
        
        <!-- Financial KPI Grid -->
        <div class="row g-3 mb-4">
            <div class="col-md-2">
                <div class="card financial-stat border-0 shadow-sm p-3">
                    <p class="text-muted small mb-1">Total Sales Val.</p>
                    <h5 class="mb-0 fw-bold">₹12.55 Cr</h5>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card financial-stat border-0 shadow-sm p-3">
                    <p class="text-muted small mb-1">Sales Return</p>
                    <h5 class="mb-0 fw-bold text-danger">₹4.20 L</h5>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card financial-stat border-0 shadow-sm p-3">
                    <p class="text-muted small mb-1">Bill Disc. (2%)</p>
                    <h5 class="mb-0 fw-bold">₹2.45 L</h5>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card financial-stat border-0 shadow-sm p-3">
                    <p class="text-muted small mb-1">Total Debtors</p>
                    <h5 class="mb-0 fw-bold text-primary">₹85.40 L</h5>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card financial-stat border-0 shadow-sm p-3">
                    <p class="text-muted small mb-1">Total Purchase</p>
                    <h5 class="mb-0 fw-bold">₹8.12 Cr</h5>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card financial-stat border-0 shadow-sm p-3">
                    <p class="text-muted small mb-1">Total Creditors</p>
                    <h5 class="mb-0 fw-bold text-danger">₹42.15 L</h5>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Debtors Outstanding & Aging -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="ri ri-team-line me-2"></i>Debtors Outstanding & Aging</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="small">Zone</th>
                                        <th class="small">Total (₹)</th>
                                        <th class="small">0-30 Days</th>
                                        <th class="small">31-60 Days</th>
                                        <th class="small">61-90 Days</th>
                                        <th class="small">Above 90</th>
                                    </tr>
                                </thead>
                                <tbody class="small">
                                    <tr>
                                        <td><strong>South Zone</strong></td>
                                        <td>24,50,000</td>
                                        <td class="text-success">15,00,000</td>
                                        <td>6,00,000</td>
                                        <td class="text-warning">2,50,000</td>
                                        <td class="text-danger fw-bold">1,00,000</td>
                                    </tr>
                                    <tr>
                                        <td><strong>North Zone</strong></td>
                                        <td>18,20,000</td>
                                        <td class="text-success">10,20,000</td>
                                        <td>5,00,000</td>
                                        <td class="text-warning">2,00,000</td>
                                        <td class="text-danger fw-bold">1,00,000</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Central Zone</strong></td>
                                        <td>12,40,000</td>
                                        <td class="text-success">8,40,000</td>
                                        <td>2,00,000</td>
                                        <td class="text-warning">1,50,000</td>
                                        <td class="text-danger fw-bold">50,000</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0 py-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="small text-muted">Debtors Collection Performance</span>
                            <div class="progress w-50" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 75%">75%</div>
                            </div>
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
                                <span class="fw-bold">₹42.50 L</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 border-0">
                                <div><i class="ri ri-checkbox-blank-circle-fill text-info me-2"></i>Accessories</div>
                                <span class="fw-bold">₹8.20 L</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 border-0">
                                <div><i class="ri ri-checkbox-blank-circle-fill text-warning me-2"></i>WIP</div>
                                <span class="fw-bold">₹15.30 L</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 border-0">
                                <div><i class="ri ri-checkbox-blank-circle-fill text-success me-2"></i>Finished Goods</div>
                                <span class="fw-bold">₹28.40 L</span>
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

    <!-- SECTION 3: PRODUCTION DASHBOARD -->
    <div class="mb-5">
        <div class="d-flex align-items-center mb-3">
            <div class="section-indicator bg-warning me-2"></div>
            <h5 class="fw-bold mb-0">Production Dashboard</h5>
        </div>
        
        <div class="row g-4">
            <!-- 1. Production WIP -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="ri ri-loader-line me-2"></i>Production WIP (Unit Wise)</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
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
                                    <tr>
                                        <td><strong>Stitching</strong></td>
                                        <td class="text-center">500</td>
                                        <td class="text-center text-success">250</td>
                                        <td class="text-center text-primary">400</td>
                                        <td class="text-center fw-bold">350</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Finishing</strong></td>
                                        <td class="text-center">300</td>
                                        <td class="text-center text-success">150</td>
                                        <td class="text-center text-primary">100</td>
                                        <td class="text-center fw-bold">350</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Production Cost (Unit Wise) -->
            <div class="col-lg-5">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="card border-0 shadow-sm p-3 border-start border-primary border-4">
                            <p class="text-muted small fw-bold mb-1">Unit I Cost</p>
                            <h5 class="mb-0 fw-bold">₹1.25 L</h5>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card border-0 shadow-sm p-3 border-start border-success border-4">
                            <p class="text-muted small fw-bold mb-1">Unit II Cost</p>
                            <h5 class="mb-0 fw-bold">₹2.80 L</h5>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-3">
                                <h6 class="mb-0 fw-bold"><i class="ri ri-money-rupee-circle-line me-2"></i>Production Cost Summary</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small">Material Cost</span>
                                    <span class="small fw-bold">₹1.50 L</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small">Labor Cost</span>
                                    <span class="small fw-bold">₹0.85 L</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 65%"></div>
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 35%"></div>
                                </div>
                            </div>
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
                            <h3 class="fw-bold mb-1 text-primary">85%</h3>
                            <p class="text-muted small uppercase fw-bold mb-0">Total Efficiency</p>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">Plan vs Achieved</span>
                                <span class="small fw-bold">850 / 1000</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 85%"></div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <div class="bg-light-warning p-2 rounded w-100 text-center">
                                <p class="text-muted x-small mb-1">Pending</p>
                                <h5 class="mb-0 fw-bold">150</h5>
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
                            <div class="list-group-item py-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-bold">JC/2026/001</span>
                                    <span class="badge bg-soft-danger text-danger">5 Days Overdue</span>
                                </div>
                                <p class="text-muted x-small mb-0">Target: 01-Mar | Qty: 500</p>
                            </div>
                            <div class="list-group-item py-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-bold">JC/2026/008</span>
                                    <span class="badge bg-soft-warning text-warning">2 Days Overdue</span>
                                </div>
                                <p class="text-muted x-small mb-0">Target: 05-Mar | Qty: 200</p>
                            </div>
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
                                        <th class="small text-center">Hold</th>
                                    </tr>
                                </thead>
                                <tbody class="small">
                                    <tr>
                                        <td>Fabric Spread</td>
                                        <td class="text-center text-success fw-bold">450</td>
                                        <td class="text-center text-danger">12</td>
                                    </tr>
                                    <tr>
                                        <td>Cuff Stitching</td>
                                        <td class="text-center text-success fw-bold">380</td>
                                        <td class="text-center text-danger">05</td>
                                    </tr>
                                    <tr>
                                        <td>Button Fixing</td>
                                        <td class="text-center text-success fw-bold">290</td>
                                        <td class="text-center text-danger">18</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 4: MAINTENANCE -->
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
                                    <tr>
                                        <td><strong>Vehicle & Other Insurance</strong></td>
                                        <td>15-Mar-26</td>
                                        <td class="text-center"><span class="badge bg-soft-danger text-danger border border-danger px-2 py-1">Due</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>AMC Renewal</strong></td>
                                        <td>22-Mar-26</td>
                                        <td class="text-center"><span class="badge bg-soft-warning text-warning border border-warning px-2 py-1">Due</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Other Licenses Renewal</strong></td>
                                        <td>12-May-26</td>
                                        <td class="text-center"><span class="badge bg-soft-info text-info border border-info px-2 py-1">Active</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Machinery & Other Service Due (Unit Wise) -->
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
                                    <span class="badge bg-danger rounded-pill">12</span>
                                </div>
                            </div>
                            <div class="list-group-item border-0 py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small fw-bold">Requirements Attended</span>
                                    <span class="badge bg-success rounded-pill">09</span>
                                </div>
                            </div>
                            <div class="list-group-item border-0 py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small fw-bold text-danger">Requirements Pending</span>
                                    <span class="badge bg-danger rounded-pill">03</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    
    .section-indicator {
        width: 12px;
        height: 24px;
        border-radius: 4px;
    }

    /* KPI Widget Styles */
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

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Helper for Chart defaults
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#64748b';

        // 1. Stock Distribution Chart (Doughnut)
        new Chart(document.getElementById('stockDistributionChart'), {
            type: 'doughnut',
            data: {
                labels: ['Fabric', 'Accessories', 'WIP', 'Finished Goods'],
                datasets: [{
                    data: [42.5, 8.2, 15.3, 28.4],
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

        // 2. Sales vs Collection Chart (Line)
        new Chart(document.getElementById('salesCollectionChart'), {
            type: 'line',
            data: {
                labels: ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
                datasets: [{
                    label: 'Sales',
                    data: [65, 59, 80, 81, 56, 55, 40, 70, 90, 110, 105, 120],
                    borderColor: '#1e3a8a',
                    backgroundColor: 'rgba(30, 58, 138, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3
                }, {
                    label: 'Collection',
                    data: [45, 48, 60, 70, 46, 45, 30, 60, 85, 95, 100, 110],
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

        // 3. Purchase vs Payment Chart (Bar)
        new Chart(document.getElementById('purchasePaymentChart'), {
            type: 'bar',
            data: {
                labels: ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
                datasets: [{
                    label: 'Purchase',
                    data: [40, 45, 50, 60, 40, 35, 30, 50, 70, 80, 75, 85],
                    backgroundColor: '#1e3a8a',
                    borderRadius: 4
                }, {
                    label: 'Payment',
                    data: [35, 40, 45, 55, 38, 30, 25, 45, 65, 75, 70, 80],
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

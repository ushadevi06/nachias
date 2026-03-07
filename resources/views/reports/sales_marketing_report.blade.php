@extends('layouts.common')
@section('title', 'Sales & Marketing Report - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <!-- Header Section -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-primary">Sales & Marketing Report</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Reports</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary btn-sm rounded-pill"><i class="ri ri-file-excel-line me-1"></i> Excel</button>
            <button class="btn btn-outline-danger btn-sm rounded-pill"><i class="ri ri-file-pdf-line me-1"></i> PDF</button>
            <button class="btn btn-primary btn-sm rounded-pill px-3"><i class="ri ri-printer-line me-1"></i> Print</button>
        </div>
    </div>

    <!-- Global Filter Card -->
    <div class="card shadow-sm border-0 mb-4 premium-filter-card">
        <div class="card-body py-4">
            <form class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">From Date</label>
                    <input type="text" class="form-control start_date" name="from_date" placeholder="DD-MM-YYYY">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">To Date</label>
                    <input type="text" class="form-control end_date" name="to_date" placeholder="DD-MM-YYYY">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Customer</label>
                    <select class="form-select select2" data-placeholder="Select Customer">
                        <option value=""></option>
                        <option value="1">Hero Mens Wear (CUS001)</option>
                        <option value="2">Unlimited Fashion Store (CUS002)</option>
                        <option value="3">Fashion Hub (CUS003)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Sales Executive</label>
                    <select class="form-select select2" data-placeholder="Select Executive">
                        <option value=""></option>
                        <option value="1">John Doe (EXE01)</option>
                        <option value="2">Jane Smith (EXE02)</option>
                        <option value="3">Michael Brown (EXE03)</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">
                        <i class="ri ri-search-line me-1"></i> Search
                    </button>
                    <button type="reset" class="btn btn-light w-100 rounded-pill border">
                        <i class="ri ri-refresh-line me-1"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabs Interface -->
    <div class="card shadow-sm border-0 premium-content-card">
        <div class="card-header bg-white border-bottom-0 p-0">
            <ul class="nav nav-tabs nav-fill premium-nav-tabs" id="reportTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="order-tab" data-bs-toggle="tab" data-bs-target="#order-report" type="button" role="tab">Order Report</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-report" type="button" role="tab">Pending Orders</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="incentive-tab" data-bs-toggle="tab" data-bs-target="#incentive-report" type="button" role="tab">Zone Wise Incentive</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="comparison-tab" data-bs-toggle="tab" data-bs-target="#comparison-report" type="button" role="tab">Sales Comparison</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="outstanding-tab" data-bs-toggle="tab" data-bs-target="#outstanding-report" type="button" role="tab">Zone Wise Outstanding</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-none d-xl-block" id="tracker-tab" data-bs-toggle="tab" data-bs-target="#tracker-report" type="button" role="tab">Tracker (Customer Feedback)</button>
                </li>   
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-none d-xl-block" id="location-tab" data-bs-toggle="tab" data-bs-target="#location-report" type="button" role="tab">Location Tracking</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-none d-xl-block" id="trip-tab" data-bs-toggle="tab" data-bs-target="#trip-report" type="button" role="tab">Trip Sheet</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-none d-xl-block" id="expense-tab" data-bs-toggle="tab" data-bs-target="#expense-report" type="button" role="tab">Expenses Cost Sheet</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-none d-xl-block" id="swatch-tab" data-bs-toggle="tab" data-bs-target="#swatch-report" type="button" role="tab">Swatch Card</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-none d-xl-block" id="complaint-tab" data-bs-toggle="tab" data-bs-target="#complaint-report" type="button" role="tab">Complaints</button>
                </li>
                <li class="nav-item dropdown d-xl-none">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">More</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#tracker-report" data-bs-toggle="tab">Feedback</a></li>
                        <li><a class="dropdown-item" href="#location-report" data-bs-toggle="tab">Location</a></li>
                        <li><a class="dropdown-item" href="#trip-report" data-bs-toggle="tab">Trip Sheet</a></li>
                        <li><a class="dropdown-item" href="#expense-report" data-bs-toggle="tab">Expenses</a></li>
                        <li><a class="dropdown-item" href="#swatch-report" data-bs-toggle="tab">Swatch Card</a></li>
                        <li><a class="dropdown-item" href="#complaint-report" data-bs-toggle="tab">Complaints</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="card-body py-4">
            <div class="tab-content" id="reportTabsContent">
                <!-- 1. Order Report -->
                <div class="tab-pane fade show active" id="order-report" role="tabpanel">
                    <div class="card-datatable">
                        <table class="datatables-products table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Order No</th>
                                    <th>Order Date</th>
                                    <th>Customer</th>
                                    <th>Item</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>SO-2024-001</strong></td>
                                    <td>05-Mar-2026</td>
                                    <td>Hero Mens Wear</td>
                                    <td>Slim Fit Formal Shirt - White</td>
                                    <td class="text-center">150</td>
                                    <td class="text-center"><span class="badge bg-label-info rounded-pill">Processing</span></td>
                                </tr>
                                <tr>
                                    <td><strong>SO-2024-002</strong></td>
                                    <td>04-Mar-2026</td>
                                    <td>Unlimited Fashion Store</td>
                                    <td>Casual Denim Shirt - Blue</td>
                                    <td class="text-center">85</td>
                                    <td class="text-center"><span class="badge bg-label-success rounded-pill">Completed</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. Pending Order Report -->
                <div class="tab-pane fade" id="pending-report" role="tabpanel">
                    <div class="card-datatable">
                        <table class="datatables-products table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Customer</th>
                                    <th>Item</th>
                                    <th class="text-center">Completion %</th>
                                    <th class="text-center">Ord. Qty</th>
                                    <th class="text-center">Bal. Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Fashion Hub (CUS003)</td>
                                    <td>Checked Flannel Shirt - Red</td>
                                    <td style="width: 250px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress w-100" style="height: 8px;">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: 60%"></div>
                                            </div>
                                            <span class="small fw-bold text-primary">60%</span>
                                        </div>
                                    </td>
                                    <td class="text-center fw-medium">200</td>
                                    <td class="text-center text-danger fw-bold">80</td>
                                </tr>
                                <tr>
                                    <td>Style Studio (CUS005)</td>
                                    <td>Linen Short Sleeve - Olive</td>
                                    <td style="width: 250px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress w-100" style="height: 8px;">
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: 35%"></div>
                                            </div>
                                            <span class="small fw-bold text-warning">35%</span>
                                        </div>
                                    </td>
                                    <td class="text-center fw-medium">500</td>
                                    <td class="text-center text-danger fw-bold">325</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. Zonewise Incentive Report -->
                <div class="tab-pane fade" id="incentive-report" role="tabpanel">
                    <div class="card-datatable">
                        <table class="datatables-products table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Zone</th>
                                    <th>Sales Executive</th>
                                    <th class="text-end">Total Sales</th>
                                    <th class="text-center">Incentive %</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>South Zone</td>
                                    <td>John Doe</td>
                                    <td class="text-end fw-bold text-primary">₹12,45,000</td>
                                    <td class="text-center"><span class="badge bg-label-primary rounded-pill px-3">2.5%</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 4. Sales Comparison -->
                <div class="tab-pane fade" id="comparison-report" role="tabpanel">
                    <div class="card-datatable">
                        <table class="datatables-products table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Month</th>
                                    <th class="text-end">Sales Year 2024</th>
                                    <th class="text-end">Sales Year 2025</th>
                                    <th class="text-center">Growth %</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>February</td>
                                    <td class="text-end">₹4,50,000</td>
                                    <td class="text-end">₹5,20,000</td>
                                    <td class="text-center text-success fw-bold"><i class="ri ri-arrow-up-s-line me-1"></i>15.5%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 5. Zonewise Outstanding Report -->
                <div class="tab-pane fade" id="outstanding-report" role="tabpanel">
                    <div class="card-datatable">
                        <table class="datatables-products table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Zone</th>
                                    <th>Customer</th>
                                    <th>Invoice No</th>
                                    <th class="text-end">Outstanding Amount</th>
                                    <th>Due Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>North Zone</td>
                                    <td>Style Studio</td>
                                    <td>INV/24/552</td>
                                    <td class="text-end fw-bold text-danger">₹28,500</td>
                                    <td><span class="badge bg-label-danger rounded-pill px-2">15-Mar-2026</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 6. Sales Executive Tracker -->
                <div class="tab-pane fade" id="tracker-report" role="tabpanel">
                    <div class="card-datatable">
                        <table class="datatables-products table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Executive</th>
                                    <th>Customer</th>
                                    <th>Visit Date</th>
                                    <th>Feedback</th>
                                    <th>Next Followup</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Jane Smith</td>
                                    <td>Retail King</td>
                                    <td>04-Mar-2026</td>
                                    <td>Ordered 500 units for new season. Samples approved.</td>
                                    <td><span class="text-primary fw-medium">10-Mar-2026</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 7. Sales Executive Location Tracking -->
                <div class="tab-pane fade" id="location-report" role="tabpanel">
                    <div class="card-datatable">
                        <table class="datatables-products table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Executive</th>
                                    <th>Date</th>
                                    <th>Location</th>
                                    <th class="text-center">Check In</th>
                                    <th class="text-center">Check Out</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Michael Brown</td>
                                    <td>05-Mar-2026</td>
                                    <td>Main Market St, Bangalore</td>
                                    <td class="text-center text-success fw-medium"><i class="ri ri-map-pin-2-line me-1"></i>09:30 AM</td>
                                    <td class="text-center text-danger fw-medium">11:15 AM</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 8. Sales Executive Trip Sheet -->
                <div class="tab-pane fade" id="trip-report" role="tabpanel">
                    <div class="card-datatable">
                        <table class="datatables-products table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Executive</th>
                                    <th>Date</th>
                                    <th>From Location</th>
                                    <th>To Location</th>
                                    <th>Purpose</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Jane Smith</td>
                                    <td>05-Mar-2026</td>
                                    <td>Head Office</td>
                                    <td>Salem Depot</td>
                                    <td>Inventory Check & Dealer Meeting</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 9. Sales Executive Expenses Cost Sheet -->
                <div class="tab-pane fade" id="expense-report" role="tabpanel">
                    <div class="card-datatable">
                        <table class="datatables-products table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Executive</th>
                                    <th class="text-end">Travel</th>
                                    <th class="text-end">Food</th>
                                    <th class="text-end">Hotel</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>04-Mar-2026</td>
                                    <td>John Doe</td>
                                    <td class="text-end">₹450</td>
                                    <td class="text-end">₹220</td>
                                    <td class="text-end">₹0</td>
                                    <td class="text-end fw-bold text-success">₹670</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 10. Swatch Card In & Out -->
                <div class="tab-pane fade" id="swatch-report" role="tabpanel">
                    <div class="card-datatable">
                        <table class="datatables-products table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Swatch Card No</th>
                                    <th>Given To</th>
                                    <th class="text-center">Date Out</th>
                                    <th class="text-center">Date In</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>SWT-2024-55</strong></td>
                                    <td>Michael (Exec)</td>
                                    <td class="text-center">28-Feb-2026</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center"><span class="badge bg-label-warning rounded-pill px-3">With Executive</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 11. Sales Complaint Report -->
                <div class="tab-pane fade" id="complaint-report" role="tabpanel">
                    <div class="card-datatable">
                        <table class="datatables-products table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Complaint No</th>
                                    <th>Customer</th>
                                    <th>Item</th>
                                    <th>Complaint</th>
                                    <th class="text-center">Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>CMP-004</strong></td>
                                    <td>Hero Mens Wear</td>
                                    <td>Linen Shirt - Ivory</td>
                                    <td>Color bleeding issue reported by end customer.</td>
                                    <td class="text-center"><span class="badge bg-label-danger rounded-pill px-3">Open</span></td>
                                    <td>06-Mar-2026</td>
                                </tr>
                            </tbody>
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

    .kpi-card {
        border-radius: 12px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
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

    /* Progress Bar */
    .progress {
        background-color: #f1f5f9;
        border-radius: 5px;
        overflow: hidden;
    }

    /* Badge Customization */
    .badge.bg-label-primary { background: #dbeafe; color: #1e40af; }
    .badge.bg-label-success { background: #dcfce7; color: #166534; }
    .badge.bg-label-info { background: #e0f2fe; color: #0369a1; }
    .badge.bg-label-warning { background: #fef9c3; color: #854d0e; }
    .badge.bg-label-danger { background: #fee2e2; color: #991b1b; }

</style>
@endsection

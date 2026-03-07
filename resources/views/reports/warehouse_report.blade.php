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
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Brand</label>
                    <select class="form-select select2" data-placeholder="Select Brand">
                        <option value=""></option>
                        <option value="1">Nachias</option>
                        <option value="2">Arrow</option>
                        <option value="3">Peter England</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Store</label>
                    <select class="form-select select2" data-placeholder="Select Store">
                        <option value=""></option>
                        <option value="1">Main Warehouse</option>
                        <option value="2">Retail Store A</option>
                        <option value="3">Showroom B</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Item</label>
                    <select class="form-select select2" data-placeholder="Select Item">
                        <option value=""></option>
                        <option value="1">Slim Fit Formal Shirt</option>
                        <option value="2">Casual Denim Shirt</option>
                        <option value="3">Cotton Dhoti</option>
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
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#order-despatch" type="button" role="tab">Order vs Despatch</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#sales-return" type="button" role="tab">Sales Return</button>
                </li>
                <li class="nav-item d-none d-xl-block" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#white-dhoti" type="button" role="tab">White & Dhoti</button>
                </li>
                <li class="nav-item d-none d-xl-block" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#despatch" type="button" role="tab">Despatch Report</button>
                </li>
                <li class="nav-item d-none d-xl-block" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#inward" type="button" role="tab">Stock Inward</button>
                </li>
                <li class="nav-item d-none d-xl-block" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#discount" type="button" role="tab">Regular/Discount</button>
                </li>
                <li class="nav-item d-none d-xl-block" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#priority" type="button" role="tab">Priority Stock</button>
                </li>
                <li class="nav-item d-none d-xl-block" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#damage" type="button" role="tab">Damage Sales</button>
                </li>
                <li class="nav-item dropdown d-xl-none">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button">More</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#white-dhoti" data-bs-toggle="tab">White & Dhoti</a></li>
                        <li><a class="dropdown-item" href="#despatch" data-bs-toggle="tab">Despatch</a></li>
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
                    <div class="card-datatable">
                        <table class="datatables-products table table-hover">
                            <thead>
                                <tr>
                                    <th>Brand</th>
                                    <th>Category</th>
                                    <th class="text-center">Sold Qty</th>
                                    <th class="text-end">Sales Value</th>
                                    <th class="text-center">Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Nachias</strong></td>
                                    <td>Formal Shirts</td>
                                    <td class="text-center">1,250</td>
                                    <td class="text-end fw-bold">₹15,00,000</td>
                                    <td class="text-center text-success"><i class="ri ri-arrow-up-line me-1"></i>12%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. Brandwise Stock Report (Setwise only) -->
                <div class="tab-pane fade" id="brand-stock" role="tabpanel">
                    <div class="card-datatable">
                        <table class="datatables-products table table-hover">
                            <thead>
                                <tr>
                                    <th>Brand</th>
                                    <th>Article No</th>
                                    <th class="text-center">Sets Available</th>
                                    <th class="text-center">Items per Set</th>
                                    <th class="text-center">Total Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Arrow</strong></td>
                                    <td>ART-992</td>
                                    <td class="text-center">45</td>
                                    <td class="text-center">6</td>
                                    <td class="text-center fw-bold">270</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. Assorted Stock Report (Single Store) -->
                <div class="tab-pane fade" id="assorted-stock" role="tabpanel">
                    <div class="card-datatable">
                        <table class="datatables-products table table-hover">
                            <thead>
                                <tr>
                                    <th>Store</th>
                                    <th>Item Name</th>
                                    <th>Color</th>
                                    <th>Size</th>
                                    <th class="text-center">Stock Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Main Warehouse</td>
                                    <td>Linen Formal Shirt</td>
                                    <td>Pure White</td>
                                    <td>42 (XL)</td>
                                    <td class="text-center fw-bold text-primary">85</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 4. Order (vs) Despatch Report -->
                <div class="tab-pane fade" id="order-despatch" role="tabpanel">
                    <div class="card-datatable">
                        <table class="datatables-products table table-hover">
                            <thead>
                                <tr>
                                    <th>Order No</th>
                                    <th>Customer</th>
                                    <th class="text-center">Ordered Qty</th>
                                    <th class="text-center">Despatched Qty</th>
                                    <th class="text-center">Pending Qty</th>
                                    <th class="text-center">Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>ORD-2024-551</strong></td>
                                    <td>Hero Mens Wear</td>
                                    <td class="text-center">500</td>
                                    <td class="text-center">350</td>
                                    <td class="text-center text-danger">150</td>
                                    <td>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-success" style="width: 70%"></div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 5. Sales Return Report -->
                <div class="tab-pane fade" id="sales-return" role="tabpanel">
                    <div class="card-datatable">
                        <table class="datatables-products table table-hover">
                            <thead>
                                <tr>
                                    <th>Return ID</th>
                                    <th>Customer</th>
                                    <th>Item</th>
                                    <th>Reason</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>SR-990</strong></td>
                                    <td>Style Studio</td>
                                    <td>Denim Shirt - Blue</td>
                                    <td>Size Fitting Issue</td>
                                    <td class="text-center">12</td>
                                    <td class="text-center"><span class="badge bg-label-warning rounded-pill">Inspecting</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 6. White & Dhoti Itemwise Sales Report -->
                <div class="tab-pane fade" id="white-dhoti" role="tabpanel">
                    <div class="card-datatable">
                        <table class="datatables-products table table-hover">
                            <thead>
                                <tr>
                                    <th>Item ID</th>
                                    <th>Item Name</th>
                                    <th>Type</th>
                                    <th class="text-center">Sold Qty</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>DHO-002</td>
                                    <td>Premium Silk Dhoti</td>
                                    <td>White Gold Border</td>
                                    <td class="text-center">240</td>
                                    <td class="text-end">₹4,80,000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 7. Despatch Report -->
                <div class="tab-pane fade" id="despatch" role="tabpanel">
                    <div class="card-datatable">
                        <table class="datatables-products table table-hover">
                            <thead>
                                <tr>
                                    <th>Despatch ID</th>
                                    <th>Vehicle No</th>
                                    <th>Driver</th>
                                    <th>Destination</th>
                                    <th class="text-center">Boxes</th>
                                    <th class="text-center">Time Out</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>DSP-112</td>
                                    <td>TN-33-AX-4455</td>
                                    <td>Ravi Kumar</td>
                                    <td>Chennai North Hub</td>
                                    <td class="text-center">45</td>
                                    <td class="text-center">10:30 AM</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 8. Stock Inward Report -->
                <div class="tab-pane fade" id="inward" role="tabpanel">
                    <div class="card-datatable">
                        <table class="datatables-products table table-hover">
                            <thead>
                                <tr>
                                    <th>GRN No</th>
                                    <th>Supplier</th>
                                    <th>Inward Date</th>
                                    <th class="text-center">Total Items</th>
                                    <th class="text-center">QC Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>GRN/442</td>
                                    <td>Vardhaman Textiles</td>
                                    <td>05-Mar-2026</td>
                                    <td class="text-center">2,000</td>
                                    <td class="text-center"><span class="badge bg-label-success rounded-pill">Passed</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 9. Regular Sales & Discount Sales Report -->
                <div class="tab-pane fade" id="discount" role="tabpanel">
                    <div class="card-datatable">
                        <table class="datatables-products table table-hover">
                            <thead>
                                <tr>
                                    <th>Period</th>
                                    <th class="text-end">Regular Sales</th>
                                    <th class="text-end">Discount Sales</th>
                                    <th class="text-center">Discount %</th>
                                    <th class="text-end">Net Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Feb 2026</td>
                                    <td class="text-end">₹55,00,000</td>
                                    <td class="text-end">₹12,00,000</td>
                                    <td class="text-center text-danger">15%</td>
                                    <td class="text-end fw-bold">₹67,00,000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 10. Priority Stock Report (Above 90 Days) -->
                <div class="tab-pane fade" id="priority" role="tabpanel">
                    <div class="card-datatable">
                        <table class="datatables-products table table-hover">
                            <thead>
                                <tr>
                                    <th>Article No</th>
                                    <th>Ageing (Days)</th>
                                    <th class="text-center">Current Stock</th>
                                    <th class="text-end">Value</th>
                                    <th class="text-center">Priority</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>W-SHIRT-77</td>
                                    <td class="text-danger">112 Days</td>
                                    <td class="text-center">180</td>
                                    <td class="text-end">₹2,16,000</td>
                                    <td class="text-center"><span class="badge bg-danger rounded-pill">High</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 11. Damage Sales Report -->
                <div class="tab-pane fade" id="damage" role="tabpanel">
                    <div class="card-datatable">
                        <table class="datatables-products table table-hover">
                            <thead>
                                <tr>
                                    <th>Damage ID</th>
                                    <th>Description</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Damage Type</th>
                                    <th class="text-center">Action Taken</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>DMG-102</td>
                                    <td>Fabric Stain on collar</td>
                                    <td class="text-center">5</td>
                                    <td class="text-center">Minor</td>
                                    <td class="text-center"><span class="badge bg-label-info rounded-pill">Discounted Sale</span></td>
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

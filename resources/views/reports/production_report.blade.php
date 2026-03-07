@extends('layouts.common')
@section('title', 'Production Report - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <!-- Header Section -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-primary">Production Report</h4>
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
                    <label class="form-label small fw-bold text-muted">Unit</label>
                    <select class="form-select select2" data-placeholder="Select Unit">
                        <option value=""></option>
                        <option value="1">Unit I (Cutting)</option>
                        <option value="2">Unit II (Stitching)</option>
                        <option value="3">Unit III (Finishing)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Process</label>
                    <select class="form-select select2" data-placeholder="Select Process">
                        <option value=""></option>
                        <option value="1">Fabric Spread</option>
                        <option value="2">Cuff Stitching</option>
                        <option value="3">Button Fixing</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label small fw-bold text-muted">Jobcard</label>
                    <input type="text" class="form-control" placeholder="No.">
                </div>
                <div class="col-md-1">
                    <label class="form-label small fw-bold text-muted">Item</label>
                    <select class="form-select select2" data-placeholder="Item">
                        <option value=""></option>
                        <option value="1">Linen Shirt</option>
                        <option value="2">Cotton Shirt</option>
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
            <ul class="nav nav-tabs nav-fill premium-nav-tabs" id="productionTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#production-wip" type="button" role="tab">Production WIP Unit Wise</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#performance-report" type="button" role="tab">Performance Individual</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#incentive-report" type="button" role="tab">Incentive Report</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#process-wise" type="button" role="tab">Production Report Section Wise</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#production-cost" type="button" role="tab">Production Cost (Section Wise & Unit Wise)</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#alteration-report" type="button" role="tab">Alteration Quantity</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#completion-report" type="button" role="tab">Job Card Completed Date</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#brand-production" type="button" role="tab">Brand Wise Unit Production</button>
                </li>
            </ul>
        </div>
        <div class="card-body py-4">
            <div class="tab-content">
                <!-- 1. Production WIP Unit Wise -->
                <div class="tab-pane fade show active" id="production-wip" role="tabpanel">
                    <div class="card-datatable table-responsive">
                        <table class="datatables-products table table-hover">
                            <thead>
                                <tr>
                                    <th>Jobcard No</th>
                                    <th>Process</th>
                                    <th class="text-center">Opening</th>
                                    <th class="text-center">Inward</th>
                                    <th class="text-center">Outward</th>
                                    <th class="text-center">Current WIP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>JC/2026/001</strong></td>
                                    <td>Stitching</td>
                                    <td class="text-center text-muted">500</td>
                                    <td class="text-center text-success">250</td>
                                    <td class="text-center text-primary">400</td>
                                    <td class="text-center fw-bold">350</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. Performance Individual Report -->
                <div class="tab-pane fade" id="performance-report" role="tabpanel">
                    <div class="card-datatable table-responsive">
                        <table class="datatables-products table table-hover">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Role</th>
                                    <th class="text-center">Assigned Qty</th>
                                    <th class="text-center">Completed Qty</th>
                                    <th class="text-center">Pending Qty</th>
                                    <th class="text-center">Efficiency</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Ramesh</strong></td>
                                    <td>Tailor</td>
                                    <td class="text-center">100</td>
                                    <td class="text-center text-success">85</td>
                                    <td class="text-center text-danger">15</td>
                                    <td class="text-center"><span class="badge bg-label-success">85%</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. Incentive Report -->
                <div class="tab-pane fade" id="incentive-report" role="tabpanel">
                    <div class="card-datatable table-responsive">
                        <table class="datatables-products table table-hover">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th class="text-center">Total Production</th>
                                    <th class="text-center">Incentive Rate</th>
                                    <th class="text-end">Total Incentive</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Karthick</strong></td>
                                    <td class="text-center">1,200</td>
                                    <td class="text-center">₹2.50</td>
                                    <td class="text-end fw-bold text-primary">₹3,000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 4. Production Report Section Wise -->
                <div class="tab-pane fade" id="process-wise" role="tabpanel">
                    <div class="card-datatable table-responsive">
                        <table class="datatables-products table table-hover">
                            <thead>
                                <tr>
                                    <th>Process Name</th>
                                    <th class="text-center text-primary">Task Plan</th>
                                    <th class="text-center text-warning">Inprocess</th>
                                    <th class="text-center text-success">Completed</th>
                                    <th class="text-center text-danger">Hold</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Collar Preparation</td>
                                    <td class="text-center">500</td>
                                    <td class="text-center">120</td>
                                    <td class="text-center">350</td>
                                    <td class="text-center">30</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 5. Production Cost (Section Wise & Unit Wise) -->
                <div class="tab-pane fade" id="production-cost" role="tabpanel">
                    <div class="card-datatable table-responsive">
                        <table class="datatables-products table table-hover">
                            <thead>
                                <tr>
                                    <th>Unit</th>
                                    <th>Process</th>
                                    <th class="text-end">Material Cost</th>
                                    <th class="text-end">Labor Cost</th>
                                    <th class="text-end">Overheads</th>
                                    <th class="text-end">Total Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Unit II (Stitching)</td>
                                    <td>Cuff Attachment</td>
                                    <td class="text-end">₹45,000</td>
                                    <td class="text-end">₹12,000</td>
                                    <td class="text-end">₹5,000</td>
                                    <td class="text-end fw-bold text-danger">₹62,000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 6. Alteration Quantity (Job Card Wise & Unit Wise) -->
                <div class="tab-pane fade" id="alteration-report" role="tabpanel">
                    <div class="card-datatable table-responsive">
                        <table class="datatables-products table table-hover">
                            <thead>
                                <tr>
                                    <th>Jobcard No</th>
                                    <th>Unit</th>
                                    <th class="text-center">Total Produced</th>
                                    <th class="text-center text-danger">Alteration Qty</th>
                                    <th class="text-center">Alteration %</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>JC/2026/005</strong></td>
                                    <td>Unit II</td>
                                    <td class="text-center">500</td>
                                    <td class="text-center text-danger">15</td>
                                    <td class="text-center">3%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 7. Job Card Completed Date (Unit Wise & Quantity & During Days) -->
                <div class="tab-pane fade" id="completion-report" role="tabpanel">
                    <div class="card-datatable table-responsive">
                        <table class="datatables-products table table-hover">
                            <thead>
                                <tr>
                                    <th>Jobcard No</th>
                                    <th>Unit</th>
                                    <th class="text-center">Quantity</th>
                                    <th>Target Date</th>
                                    <th>Completed Date</th>
                                    <th class="text-center">Days Taken</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>JC/2026/012</strong></td>
                                    <td>Unit III</td>
                                    <td class="text-center">250</td>
                                    <td>10-Mar-2026</td>
                                    <td>08-Mar-2026</td>
                                    <td class="text-center"><span class="badge bg-label-success">2 Days Early</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 8. Brand Wise Unit Production (Sleeve & Style Wise) -->
                <div class="tab-pane fade" id="brand-production" role="tabpanel">
                    <div class="card-datatable table-responsive">
                        <table class="datatables-products table table-hover">
                            <thead>
                                <tr>
                                    <th>Brand</th>
                                    <th>Style Name</th>
                                    <th>Sleeve Type</th>
                                    <th class="text-center">Produced Qty</th>
                                    <th>Unit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Nachias</strong></td>
                                    <td>Premium Slim Fit</td>
                                    <td>Full Sleeve</td>
                                    <td class="text-center fw-bold">1,500</td>
                                    <td>Unit II</td>
                                </tr>
                                <tr>
                                    <td><strong>Nachias</strong></td>
                                    <td>Casual Cotton</td>
                                    <td>Half Sleeve</td>
                                    <td class="text-center fw-bold">800</td>
                                    <td>Unit II</td>
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
</style>
@endsection

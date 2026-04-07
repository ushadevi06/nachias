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
            <button id="btn-excel" class="btn btn-outline-primary btn-sm rounded-pill"><i class="ri ri-file-excel-line me-1"></i> Excel</button>
            <button id="btn-pdf" class="btn btn-outline-danger btn-sm rounded-pill"><i class="ri ri-file-pdf-line me-1"></i> PDF</button>
            <button id="btn-print" class="btn btn-primary btn-sm rounded-pill px-3"><i class="ri ri-printer-line me-1"></i> Print</button>
        </div>
    </div>

    <!-- Global Filter Card -->
    <div class="card shadow-sm border-0 mb-4 premium-filter-card">
        <div class="card-body py-4">
            <form id="salesMarketingReportForm" class="row g-3 align-items-end" method="GET" action="{{ url('sales_marketing_reports') }}">
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">From Date</label>
                    <input type="text" class="form-control start_date" name="from_date" value="{{ request('from_date') }}" placeholder="DD-MM-YYYY">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">To Date</label>
                    <input type="text" class="form-control end_date" name="to_date" value="{{ request('to_date') }}" placeholder="DD-MM-YYYY">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Zone</label>
                    <select class="form-select select2" name="zone_id" id="zone_id_filter" data-placeholder="Select Zone">
                        <option value=""></option>
                        @foreach($zones as $zone)
                        <option value="{{ $zone->id }}" {{ request('zone_id') == $zone->id ? 'selected' : '' }}>
                            {{ $zone->zone_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Customer</label>
                    <select class="form-select select2" name="customer_id" data-placeholder="Select Customer">
                        <option value=""></option>
                        @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }} ({{ $customer->code }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Sales Executive</label>
                    <select class="form-select select2" name="agent_id" id="agent_id_filter" data-placeholder="Select Executive">
                        <option value=""></option>
                        @foreach($executives as $executive)
                        <option value="{{ $executive->id }}" {{ request('agent_id') == $executive->id ? 'selected' : '' }}>
                            {{ $executive->name }} ({{ $executive->code }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">
                        <i class="ri ri-search-line me-1"></i> Search
                    </button>
                    <a href="{{ url('sales_marketing_reports') }}" class="btn btn-outline-light w-100 rounded-pill border">
                        <i class="ri ri-refresh-line me-1"></i> Reset
                    </a>
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
                    @include('reports.sales_marketing_reports._order_report')
                </div>

                <!-- 2. Pending Order Report -->
                <div class="tab-pane fade" id="pending-report" role="tabpanel">
                    @include('reports.sales_marketing_reports._pending_report')
                </div>

                <!-- 3. Zonewise Incentive Report -->
                <div class="tab-pane fade" id="incentive-report" role="tabpanel">
                    @include('reports.sales_marketing_reports._incentive_report')
                </div>

                <!-- 4. Sales Comparison -->
                <div class="tab-pane fade" id="comparison-report" role="tabpanel">
                    @include('reports.sales_marketing_reports._comparison_report')
                </div>

                <!-- 5. Zonewise Outstanding Report -->
                <div class="tab-pane fade" id="outstanding-report" role="tabpanel">
                    @include('reports.sales_marketing_reports._outstanding_report')
                </div>

                <!-- 6. Sales Executive Tracker -->
                <div class="tab-pane fade" id="tracker-report" role="tabpanel">
                    @include('reports.sales_marketing_reports._tracker_report')
                </div>

                <!-- 7. Sales Executive Location Tracking -->
                <div class="tab-pane fade" id="location-report" role="tabpanel">
                    @include('reports.sales_marketing_reports._location_report')
                </div>

                <!-- 8. Sales Executive Trip Sheet -->
                <div class="tab-pane fade" id="trip-report" role="tabpanel">
                    @include('reports.sales_marketing_reports._trip_report')
                </div>

                <!-- 9. Sales Executive Expenses Cost Sheet -->
                <div class="tab-pane fade" id="expense-report" role="tabpanel">
                    @include('reports.sales_marketing_reports._expense_report')
                </div>

                <!-- 10. Swatch Card In & Out -->
                <div class="tab-pane fade" id="swatch-report" role="tabpanel">
                    @include('reports.sales_marketing_reports._swatch_report')
                </div>

                <!-- 11. Sales Complaint Report -->
                <div class="tab-pane fade" id="complaint-report" role="tabpanel">
                    @include('reports.sales_marketing_reports._complaint_report')
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Items Modal -->
<div class="modal fade" id="orderItemsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold text-white mb-0">
                    <i class="ri ri-shopping-bag-line me-2"></i>Order Items: <span id="modalOrderNo" class="text-warning"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-uppercase small fw-bold">Item Name</th>
                                <th class="text-center py-3 text-uppercase small fw-bold">Size</th>
                                <th class="text-center py-3 text-uppercase small fw-bold">Sleeve</th>
                                <th class="text-center pe-4 py-3 text-uppercase small fw-bold">Qty</th>
                            </tr>
                        </thead>
                        <tbody id="modalItemsBody">
                            <!-- Items will be injected here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light py-2">
                <button type="button" class="btn btn-label-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const orderItemsModal = document.getElementById('orderItemsModal');
    if (orderItemsModal) {
        orderItemsModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const orderNo = button.getAttribute('data-order-no');
            const items = JSON.parse(button.getAttribute('data-items'));

            const modalOrderNo = document.getElementById('modalOrderNo');
            const modalItemsBody = document.getElementById('modalItemsBody');

            modalOrderNo.textContent = orderNo;
            modalItemsBody.innerHTML = '';

            items.forEach(item => {
                const row = `
                    <tr>
                        <td class="ps-4 py-3 fw-medium text-dark">${item.name}</td>
                        <td class="text-center py-3">
                            <span class="badge bg-label-secondary rounded-pill">${item.size}</span>
                        </td>
                        <td class="text-center py-3">
                            <span class="badge bg-label-info rounded-pill">${item.sleeve}</span>
                        </td>
                        <td class="text-center pe-4 py-3">
                            <span class="badge bg-label-primary rounded-pill px-3">${item.qty}</span>
                        </td>
                    </tr>
                `;
                modalItemsBody.insertAdjacentHTML('beforeend', row);
            });
        });
    }

    // Zone-based Agent Filtering
    $('#zone_id_filter').on('change', function() {
        const zoneId = $(this).val();
        const agentSelect = $('#agent_id_filter');
        
        agentSelect.empty().append('<option value="">Select Executive</option>');
        
        if (zoneId) {
            $.ajax({
                url: `${APP_URL}/get-agents-by-zone/${zoneId}`,
                type: 'GET',
                success: function(data) {
                    data.forEach(agent => {
                        agentSelect.append(`<option value="${agent.id}">${agent.name}</option>`);
                    });
                    agentSelect.trigger('change');
                }
            });
        } else {
            // If no zone selected, re-fill with all active executives (optional, or just leave empty)
            // For now, let's keep it empty or as is if no zone is selected.
        }
    });
});
</script>

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

@section('scripts')
<script>
$(document).ready(function() {
    $('#salesMarketingReportForm').on('submit', function(e) {
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

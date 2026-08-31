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
            <form id="salesMarketingReportForm" class="row g-3 align-items-end" onsubmit="return false;">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-primary"><i class="ri-file-chart-line me-1"></i>Select Report Type</label>
                    <select class="form-select select2" id="report_type_select" name="report_type">
                        <option value="order-report" selected>📦 Order Report</option>
                        <option value="pending-report">⏳ Pending Orders</option>
                        <option value="incentive-report">🗺️ Zone Wise Incentive</option>
                        <option value="comparison-report">📊 Sales Comparison</option>
                        <option value="credit-note-report">📝 Credit Note Report</option>
                        <option value="despatch-report">🚚 Despatch Tracking</option>
                        <option value="outstanding-report">💰 Zone Wise Outstanding</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">From Date</label>
                    <input type="text" class="form-control start_date" name="from_date" value="{{ request('from_date') }}" placeholder="DD-MM-YYYY">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">To Date</label>
                    <input type="text" class="form-control end_date" name="to_date" value="{{ request('to_date') }}" placeholder="DD-MM-YYYY">
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
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill p-2" title="Search">
                        <i class="ri ri-search-line"></i>
                    </button>
                    <button type="button" id="btn-reset-report" class="btn btn-outline-light rounded-pill border p-2" title="Reset">
                        <i class="ri ri-refresh-line"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Content Card -->
    <div class="card shadow-sm border-0 premium-content-card">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-bold text-primary" id="active_report_title">
                📦 Order Report
            </h5>
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

                <!-- 12. Credit Note Report -->
                <div class="tab-pane fade" id="credit-note-report" role="tabpanel">
                    @include('reports.sales_marketing_reports._credit_note_report')
                </div>

                <!-- 13. Despatch Tracking Report -->
                <div class="tab-pane fade" id="despatch-report" role="tabpanel">
                    @include('reports.sales_marketing_reports._despatch_tracking_report')
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
    const tableConfigs = {
        'order-report': {
            tableId: '#orderReportTable',
            type: 'order-report',
            columns: [
                { data: 'so_no', name: 'so_no', className: 'fw-bold' },
                { data: 'so_date', name: 'so_date', className: 'text-nowrap' },
                { data: 'customer', name: 'customer' },
                { data: 'qty', name: 'qty', className: 'text-center fw-bold' },
                { data: 'status', name: 'status', className: 'text-center' },
                { data: 'action', name: 'action', className: 'text-center', orderable: false }
            ]
        },
        'pending-report': {
            tableId: '#pendingReportTable',
            type: 'pending-report',
            columns: [
                { data: 'so_no', name: 'so_no', className: 'fw-bold text-primary' },
                { data: 'customer', name: 'customer' },
                { data: 'ord_qty', name: 'ord_qty', className: 'text-center' },
                { data: 'bal_qty', name: 'bal_qty', className: 'text-center fw-bold text-danger' }
            ]
        },
        'incentive-report': {
            tableId: '#incentiveReportTable',
            type: 'incentive-report',
            columns: [
                { data: 'zone', name: 'zone' },
                { data: 'agent', name: 'agent' },
                { data: 'total_sales', name: 'total_sales', className: 'text-end fw-bold' },
                { data: 'incentive_pc', name: 'incentive_pc', className: 'text-center' },
                { data: 'incentive_amt', name: 'incentive_amt', className: 'text-end fw-bold text-success' }
            ]
        },
        'credit-note-report': {
            tableId: '#creditNoteReportTable',
            type: 'credit-note-report',
            columns: [
                { data: 'note_no', name: 'note_no', className: 'fw-bold' },
                { data: 'note_date', name: 'note_date', className: 'text-nowrap' },
                { data: 'customer', name: 'customer' },
                { data: 'zone', name: 'zone' },
                { data: 'agent', name: 'agent' },
                { data: 'reason', name: 'reason' },
                { data: 'sub_total', name: 'sub_total', className: 'text-end' },
                { data: 'discount', name: 'discount', className: 'text-end text-danger' },
                { data: 'tax_amount', name: 'tax_amount', className: 'text-end' },
                { data: 'other_charges', name: 'other_charges', className: 'text-end' },
                { data: 'grand_total', name: 'grand_total', className: 'text-end fw-bold text-success' },
                { data: 'status', name: 'status', className: 'text-center' }
            ]
        },
        'despatch-report': {
            tableId: '#despatchReportTable',
            type: 'despatch-report',
            columns: [
                { data: 'sno', name: 'sno', className: 'text-center font-monospace' },
                { data: 'so_no', name: 'so_no', className: 'fw-bold text-primary' },
                { data: 'order_no', name: 'order_no' },
                { data: 'order_type', name: 'order_type' },
                { data: 'so_date', name: 'so_date', className: 'text-nowrap' },
                { data: 'agent', name: 'agent' },
                { data: 'customer', name: 'customer' },
                { data: 'place', name: 'place' },
                { data: 'zone', name: 'zone' },
                { data: 'dhoti_qty', name: 'dhoti_qty', className: 'text-center' },
                { data: 'white_qty', name: 'white_qty', className: 'text-center' },
                { data: 'core_qty', name: 'core_qty', className: 'text-center' },
                { data: 'bravo_qty', name: 'bravo_qty', className: 'text-center' },
                { data: 'deal_qty', name: 'deal_qty', className: 'text-center' },
                { data: 'formal_qty', name: 'formal_qty', className: 'text-center' },
                { data: 'total_qty', name: 'total_qty', className: 'text-center fw-bold' },
                { data: 'delivery_date', name: 'delivery_date', className: 'text-nowrap' },
                { data: 'status', name: 'status', className: 'text-center' },
                { data: 'delivered_qty', name: 'delivered_qty', className: 'text-center' },
                { data: 'pending_qty', name: 'pending_qty', className: 'text-center' },
                { data: 'partial_d_date', name: 'partial_d_date', className: 'text-nowrap' },
                { data: 'despatch_complete_date', name: 'despatch_complete_date', className: 'text-nowrap' },
                { data: 'reason', name: 'reason' }
            ]
        },
        'comparison-report': {
            tableId: '#comparisonReportTable',
            type: 'comparison-report',
            columns: [
                { data: 'month_name', name: 'month_name' },
                { data: 'prev_year_sales', name: 'prev_year_sales', className: 'text-end' },
                { data: 'curr_year_sales', name: 'curr_year_sales', className: 'text-end' },
                { data: 'growth_pc', name: 'growth_pc', className: 'text-center' }
            ]
        },
        'outstanding-report': {
            tableId: '#outstandingReportTable',
            type: 'outstanding-report',
            columns: [
                { data: 'zone', name: 'zone' },
                { data: 'customer', name: 'customer' },
                { data: 'bills_count', name: 'bills_count', className: 'text-center' },
                { data: 'total_sales', name: 'total_sales', className: 'text-end' },
                { data: 'received', name: 'received', className: 'text-end' },
                { data: 'outstanding', name: 'outstanding', className: 'text-end' }
            ]
        }
    };

    $.fn.dataTable.ext.errMode = 'none';

    function showReportLoading(isLoading) {
        let loader = $('#report_loader');
        if (isLoading) {
            if (!loader.length) {
                $('#active_report_title').append(' <div class="spinner-border spinner-border-sm text-primary ms-2" id="report_loader" role="status"></div>');
            }
            $('#reportTabsContent').css('opacity', '0.6');
        } else {
            $('#report_loader').remove();
            $('#reportTabsContent').css('opacity', '1');
        }
    }

    function loadActiveTabTable(tabPaneId) {
        const config = tableConfigs[tabPaneId];
        if (!config) return;

        const tableElem = $(config.tableId);
        if (!tableElem.length) return;

        showReportLoading(true);

        if ($.fn.DataTable.isDataTable(config.tableId)) {
            const dt = tableElem.DataTable();
            if (dt && dt.ajax && typeof dt.ajax.url === 'function' && dt.ajax.url()) {
                try {
                    dt.ajax.reload(function() { showReportLoading(false); }, false);
                    return;
                } catch (err) {
                    dt.destroy();
                }
            } else {
                dt.destroy();
            }
        }

        tableElem.DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            destroy: true,
            language: {
                processing: '<div class="d-flex align-items-center justify-content-center py-4 text-primary fw-bold"><div class="spinner-border spinner-border-sm me-2" role="status"></div> Loading report data...</div>',
                emptyTable: '<div class="text-center py-4 text-muted"><i class="ri-inbox-line ri-2x mb-2 d-block text-secondary"></i>No records found</div>'
            },
            ajax: {
                url: "{{ url('sales_marketing_reports/ajax') }}/" + config.type,
                type: "GET",
                data: function(d) {
                    d.from_date = $('.start_date').val();
                    d.to_date = $('.end_date').val();
                    d.customer_id = $('select[name="customer_id"]').val();
                    d.agent_id = $('select[name="agent_id"]').val();
                }
            },
            drawCallback: function() {
                showReportLoading(false);
            },
            columns: config.columns,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10
        });
    }

    // Select Report Type Change Listener
    $('#report_type_select').on('change', function() {
        const selectedType = $(this).val();
        const selectedText = $(this).find('option:selected').text();
        $('#active_report_title').html(selectedText);

        $('.tab-pane').removeClass('show active');
        $('#' + selectedType).addClass('show active');

        loadActiveTabTable(selectedType);
    });

    // Initialize Active Report on Page Load
    const initialReportType = $('#report_type_select').val() || 'order-report';
    const initialText = $('#report_type_select option:selected').text();
    if (initialText) {
        $('#active_report_title').html(initialText);
    }
    $('.tab-pane').removeClass('show active');
    $('#' + initialReportType).addClass('show active');
    loadActiveTabTable(initialReportType);

    // Form Filter Submit listener
    $('#salesMarketingReportForm').on('submit', function(e) {
        e.preventDefault();
        const activeTabId = $('#report_type_select').val() || 'order-report';
        const config = tableConfigs[activeTabId];
        if (config && $.fn.DataTable.isDataTable(config.tableId)) {
            const dt = $(config.tableId).DataTable();
            if (dt && dt.ajax && typeof dt.ajax.url === 'function' && dt.ajax.url()) {
                try {
                    showReportLoading(true);
                    dt.ajax.reload(function() { showReportLoading(false); });
                    return;
                } catch (err) {
                    dt.destroy();
                }
            }
        }
        loadActiveTabTable(activeTabId);
    });

    var isOrderRootLevel = true;

    window.renderOrderLevel = function() {
        isOrderRootLevel = true;
        $('#orderBreadcrumbs').hide();
        $('#selectedOrderInfoHeader').hide();

        if ($.fn.DataTable.isDataTable('#orderReportTable')) {
            $('#orderReportTable').DataTable().clear().destroy();
        }

        $('#orderReportTheadTr').html(`
            <th class="fw-bold">ORDER NO</th>
            <th class="text-nowrap fw-bold">ORDER DATE</th>
            <th class="fw-bold">CUSTOMER</th>
            <th class="text-center fw-bold">QTY</th>
            <th class="text-center fw-bold">STATUS</th>
        `);
        $('#orderReportTbody').empty();

        loadActiveTabTable('order-report');
    };

    function renderOrderItemLevel(soNo, customer, soDate, statusHtml, itemsData) {
        isOrderRootLevel = false;

        $('#orderBreadcrumbText').text('Orders > ' + soNo + ' (' + customer + ')');
        $('#orderBreadcrumbs').css('display', 'flex');

        $('#level2SoNo').text(soNo);
        $('#level2Customer').text(customer);
        $('#level2SoDate').text(soDate);
        $('#level2Status').html(statusHtml);
        $('#selectedOrderInfoHeader').show();

        if ($.fn.DataTable.isDataTable('#orderReportTable')) {
            $('#orderReportTable').DataTable().clear().destroy();
        }

        $('#orderReportTheadTr').html(`
            <th class="text-center" style="width: 40px;">#</th>
            <th class="fw-bold">ITEM NAME</th>
            <th class="text-center fw-bold">SIZE</th>
            <th class="text-center fw-bold">SLEEVE</th>
            <th class="text-end fw-bold pe-3">QUANTITY</th>
        `);

        let rowsHtml = '';
        if (itemsData && itemsData.length > 0) {
            itemsData.forEach((item, idx) => {
                rowsHtml += `
                    <tr>
                        <td class="text-center text-muted fw-bold">${idx + 1}</td>
                        <td class="fw-medium text-dark">${item.name}</td>
                        <td class="text-center"><span class="badge bg-label-secondary rounded-pill">${item.size}</span></td>
                        <td class="text-center"><span class="badge bg-label-info rounded-pill">${item.sleeve}</span></td>
                        <td class="text-end fw-bold pe-3">${item.qty}</td>
                    </tr>
                `;
            });
        } else {
            rowsHtml = '<tr><td colspan="5" class="text-center py-4 text-muted"><i class="ri-inbox-line ri-2x d-block mb-1"></i>No items recorded for this order.</td></tr>';
        }

        $('#orderReportTbody').html(rowsHtml);

        $('#orderReportTable').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            pageLength: 10,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
        });
    }

    // Handle Order Row Click to Drilldown to Items Hierarchy
    $(document).on('click', '#orderReportTable tbody tr', function(e) {
        if (!isOrderRootLevel) return;
        if ($(e.target).is('a, button, input, select')) return;

        const dt = $('#orderReportTable').DataTable();
        const rowData = dt.row(this).data();

        if (rowData && rowData.items_data) {
            renderOrderItemLevel(
                rowData.so_no_raw,
                rowData.customer_raw,
                rowData.so_date_raw,
                rowData.status_html,
                rowData.items_data
            );
        }
    });

    var isPendingOrderRootLevel = true;

    window.renderPendingOrderLevel = function() {
        isPendingOrderRootLevel = true;
        $('#pendingBreadcrumbs').hide();
        $('#selectedPendingOrderInfoHeader').hide();

        if ($.fn.DataTable.isDataTable('#pendingReportTable')) {
            $('#pendingReportTable').DataTable().clear().destroy();
        }

        $('#pendingReportTheadTr').html(`
            <th class="fw-bold">ORDER NO</th>
            <th class="fw-bold">CUSTOMER</th>
            <th class="text-center fw-bold">ORD. QTY</th>
            <th class="text-center fw-bold">BAL. QTY</th>
        `);
        $('#pendingReportTbody').empty();

        loadActiveTabTable('pending-report');
    };

    function renderPendingOrderItemLevel(soNo, customer, ordQty, balQty, itemsData) {
        isPendingOrderRootLevel = false;

        $('#pendingBreadcrumbText').text('Pending Orders > ' + soNo + ' (' + customer + ')');
        $('#pendingBreadcrumbs').css('display', 'flex');

        $('#level2PendingSoNo').text(soNo);
        $('#level2PendingCustomer').text(customer);
        $('#level2PendingOrdQty').text(ordQty);
        $('#level2PendingBalQty').text(balQty);
        $('#selectedPendingOrderInfoHeader').show();

        if ($.fn.DataTable.isDataTable('#pendingReportTable')) {
            $('#pendingReportTable').DataTable().clear().destroy();
        }

        $('#pendingReportTheadTr').html(`
            <th class="text-center" style="width: 40px;">#</th>
            <th class="fw-bold">ITEM NAME</th>
            <th class="text-center fw-bold">SIZE</th>
            <th class="text-center fw-bold">SLEEVE</th>
            <th class="text-end fw-bold pe-3">QUANTITY</th>
        `);

        let rowsHtml = '';
        if (itemsData && itemsData.length > 0) {
            itemsData.forEach((item, idx) => {
                rowsHtml += `
                    <tr>
                        <td class="text-center text-muted fw-bold">${idx + 1}</td>
                        <td class="fw-medium text-dark">${item.name}</td>
                        <td class="text-center"><span class="badge bg-label-secondary rounded-pill">${item.size}</span></td>
                        <td class="text-center"><span class="badge bg-label-info rounded-pill">${item.sleeve}</span></td>
                        <td class="text-end fw-bold pe-3">${item.qty}</td>
                    </tr>
                `;
            });
        } else {
            rowsHtml = '<tr><td colspan="5" class="text-center py-4 text-muted"><i class="ri-inbox-line ri-2x d-block mb-1"></i>No items recorded for this order.</td></tr>';
        }

        $('#pendingReportTbody').html(rowsHtml);

        $('#pendingReportTable').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            pageLength: 10,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
        });
    }

    // Handle Pending Order Row Click to Drilldown to Items Hierarchy
    $(document).on('click', '#pendingReportTable tbody tr', function(e) {
        if (!isPendingOrderRootLevel) return;
        if ($(e.target).is('a, button, input, select')) return;

        const dt = $('#pendingReportTable').DataTable();
        const rowData = dt.row(this).data();

        if (rowData && rowData.items_data) {
            renderPendingOrderItemLevel(
                rowData.so_no_raw,
                rowData.customer_raw,
                rowData.ord_qty_raw,
                rowData.bal_qty_raw,
                rowData.items_data
            );
        }
    });

    // Reset Button Handler (matches Warehouse Report pattern)
    $(document).on('click', '#btn-reset-report', function(e) {
        e.preventDefault();
        $('.start_date').val('');
        $('.end_date').val('');
        $('select[name="customer_id"]').val('').trigger('change');
        $('select[name="agent_id"]').val('').trigger('change');
        $('#salesMarketingReportForm').trigger('submit');
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

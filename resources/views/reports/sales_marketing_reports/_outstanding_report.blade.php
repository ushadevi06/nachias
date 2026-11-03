<!-- Sub-Tabs: 1. Party Wise | 2. Aging Wise -->
<div class="row mb-3" id="outstandingSubTabsWrapper">
    <div class="col-12">
        <ul class="nav nav-pills p-1 bg-lighter rounded-3 d-inline-flex border" id="outstandingSubTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link active px-4 py-2 fw-semibold" id="outstanding-party-wise-tab" data-bs-toggle="pill" data-bs-target="#outstanding-party-wise-pane" role="tab" aria-controls="outstanding-party-wise-pane" aria-selected="true">
                    <i class="ri ri-user-star-line me-1"></i> Party Wise
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link px-4 py-2 fw-semibold" id="outstanding-aging-wise-tab" data-bs-toggle="pill" data-bs-target="#outstanding-aging-wise-pane" role="tab" aria-controls="outstanding-aging-wise-pane" aria-selected="false">
                    <i class="ri ri-time-line me-1"></i> Aging Wise
                </button>
            </li>
        </ul>
    </div>
</div>

<div class="tab-content p-0" id="outstandingTabContent">
    <!-- Tab 1: Party Wise -->
    <div class="tab-pane fade show active" id="outstanding-party-wise-pane" role="tabpanel" aria-labelledby="outstanding-party-wise-tab">
        <!-- Action Toolbar for Outstanding Report -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 p-3 bg-light rounded-3 border">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill">
                    <i class="ri ri-bank-card-line me-1"></i> Party Wise Outstanding
                </span>
                <span class="text-muted small d-none d-md-inline">
                    <i class="ri ri-information-line text-info me-1"></i> Includes Opening Outstanding from Tally & Current Portal Invoices
                </span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ url('sales_marketing_reports/download_tally_template') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3" title="Download Excel template for Tally opening balance">
                    <i class="ri ri-download-2-line me-1"></i> Download Template
                </a>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#importTallyModal">
                    <i class="ri ri-upload-cloud-2-line me-1"></i> Import Tally Outstanding
                </button>
            </div>
        </div>

        <div class="card-datatable table-responsive">
            <table class="datatables-products table table-hover" id="outstandingReportTable">
                <thead class="bg-light">
                    <tr>
                        <th>Zone</th>
                        <th>Customer</th>
                        <th class="text-center">Pending Bills</th>
                        <th class="text-end text-primary fw-bold">Open Outstanding</th>
                        <th class="text-end">Total Sales</th>
                        <th class="text-end">Received</th>
                        <th class="text-end text-danger fw-bold">Total Outstanding</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot class="bg-light fw-bold">
                    <tr>
                        <th colspan="2" class="text-end text-uppercase">Total:</th>
                        <th class="text-center text-primary fw-bold" id="outstanding_report_bills_count">0</th>
                        <th class="text-end text-primary fw-bold" id="outstanding_report_open_outstanding">₹0.00</th>
                        <th class="text-end text-primary fw-bold" id="outstanding_report_total_sales">₹0.00</th>
                        <th class="text-end text-success fw-bold" id="outstanding_report_received">₹0.00</th>
                        <th class="text-end text-danger fw-bold" id="outstanding_report_outstanding">₹0.00</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Tab 2: Aging Wise -->
    <div class="tab-pane fade" id="outstanding-aging-wise-pane" role="tabpanel" aria-labelledby="outstanding-aging-wise-tab">
        <!-- Level 1: Zone Aging Summary View -->
        <div id="zoneAgingSummaryWrapper">
            <div class="card-datatable table-responsive">
                <table class="datatables-products table table-hover align-middle" id="zoneAgingSummaryTable" style="width: 100%;">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-bold">ZONE</th>
                            <th class="text-end fw-bold">TOTAL OUTSTANDING (₹)</th>
                            <th class="text-end fw-bold text-success">0-30 DAYS</th>
                            <th class="text-end fw-bold text-dark">31-60 DAYS</th>
                            <th class="text-end fw-bold text-warning">61-90 DAYS</th>
                            <th class="text-end fw-bold text-danger">ABOVE 90 DAYS</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot class="table-light fw-bold border-top">
                        <tr>
                            <th class="text-uppercase text-dark">Total:</th>
                            <th class="text-end text-dark fs-6" id="aging_report_outstanding">₹0.00</th>
                            <th class="text-end text-success fs-6" id="aging_report_b0_30">₹0.00</th>
                            <th class="text-end text-dark fs-6" id="aging_report_b31_60">₹0.00</th>
                            <th class="text-end text-warning fs-6" id="aging_report_b61_90">₹0.00</th>
                            <th class="text-end text-danger fs-6" id="aging_report_above_90">₹0.00</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Level 2: In-Page Zone Customers Drilldown View -->
        <div id="zoneAgingDrilldownWrapper" style="display: none;">
            <!-- Breadcrumb & Back Navigation -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 p-3 bg-light rounded-3 border">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" onclick="backToZoneAgingSummary()">
                        <i class="ri ri-arrow-left-line me-1"></i> Back to Aging Wise
                    </button>
                    <span class="text-muted fw-semibold">
                        Zone Wise Outstanding &nbsp;<i class="ri ri-arrow-right-s-line"></i>&nbsp;
                        <span class="text-dark fw-bold" id="drilldownZoneBreadcrumb">Zone</span>
                    </span>
                </div>
                <div>
                    <span class="badge bg-danger rounded-pill px-3 py-2 fs-7" id="drilldownZoneTotalBadge">Total Outstanding: ₹0.00</span>
                </div>
            </div>

            <!-- Customer Details Table for this Zone -->
            <div class="card-datatable table-responsive">
                <table class="datatables-products table table-hover" id="zoneDrilldownCustomerTable" style="width: 100%;">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">S.No</th>
                            <th>Customer</th>
                            <th class="text-center">Pending Bills</th>
                            <th class="text-end text-primary fw-bold">Open Outstanding</th>
                            <th class="text-end">Total Sales</th>
                            <th class="text-end">Received</th>
                            <th class="text-end text-danger fw-bold">Total Outstanding</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot class="bg-light fw-bold border-top">
                        <tr>
                            <th colspan="2" class="text-end text-uppercase">Zone Total:</th>
                            <th class="text-center text-primary fw-bold" id="zone_drill_bills_count">0</th>
                            <th class="text-end text-primary fw-bold" id="zone_drill_open_outstanding">₹0.00</th>
                            <th class="text-end text-primary fw-bold" id="zone_drill_total_sales">₹0.00</th>
                            <th class="text-end text-success fw-bold" id="zone_drill_received">₹0.00</th>
                            <th class="text-end text-danger fw-bold" id="zone_drill_outstanding">₹0.00</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Import Tally Opening Outstanding -->
<div class="modal fade" id="importTallyModal" tabindex="-1" aria-labelledby="importTallyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title text-white d-flex align-items-center gap-2" id="importTallyModalLabel">
                    <i class="ri ri-upload-cloud-2-line fs-4"></i> Import Tally Opening Outstanding
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="importTallyForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Select Excel / CSV File <span class="text-danger">*</span></label>
                        <input type="file" class="control form-control" name="import_file" id="tallyImportFileInput" accept=".xlsx,.xls,.csv,.txt" required>
                        <small class="text-muted d-block mt-1">Supported formats: .xlsx, .xls, .csv (Max: 10 MB)</small>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="small text-muted">Need standard format?</span>
                        <a href="{{ url('sales_marketing_reports/download_tally_template') }}" class="small text-primary fw-semibold text-decoration-underline">
                            <i class="ri ri-file-excel-2-line me-1"></i> Download Sample CSV
                        </a>
                    </div>

                    <div id="importTallyAlert" class="mt-3" style="display: none;"></div>
                </div>
                <div class="modal-footer bg-light py-2 px-4">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4" id="importTallySubmitBtn">
                        <span id="importTallyBtnText"><i class="ri ri-upload-2-line me-1"></i> Upload & Import</span>
                        <span id="importTallyBtnSpinner" style="display: none;">
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Processing...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Outstanding Sub-Tabs Segmented Capsule */
    #outstandingSubTabs .nav-link {
        color: #566a7f;
        transition: all 0.2s ease-in-out;
    }
    #outstandingSubTabs .nav-link.active {
        background-color: #696cff !important;
        color: #fff !important;
        box-shadow: 0 2px 4px 0 rgba(105, 108, 255, 0.4);
    }
    #zoneAgingSummaryTable tbody tr {
        transition: background-color 0.15s ease-in-out;
        cursor: pointer;
    }
    #zoneAgingSummaryTable tbody tr:hover td {
        background-color: rgba(105, 108, 255, 0.08) !important;
    }
</style>

<script>
window.currentAgingZoneId = null;

window.drillDownZoneAging = function(zoneId, zoneName) {
    window.currentAgingZoneId = zoneId;
    $('#zoneAgingSummaryWrapper').hide();
    $('#zoneAgingDrilldownWrapper').fadeIn(150);
    $('#drilldownZoneBreadcrumb').text(zoneName || ('Zone #' + zoneId));
    $('#drilldownZoneTotalBadge').text('Loading...');

    if ($.fn.DataTable.isDataTable('#zoneDrilldownCustomerTable')) {
        try {
            $('#zoneDrilldownCustomerTable').DataTable().destroy();
        } catch(e) {}
    }

    var fromVal = $('.start_date').val();
    var toVal = $('.end_date').val();
    if (!fromVal || !toVal) {
        var rangeText = $('#sales_marketing_date_range').val() || '';
        if (rangeText.indexOf(' to ') !== -1) {
            var parts = rangeText.split(' to ');
            fromVal = parts[0].trim();
            toVal = parts[1].trim();
        } else if (rangeText.trim()) {
            fromVal = rangeText.trim();
            toVal = rangeText.trim();
        }
    }

    $('#zoneDrilldownCustomerTable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        destroy: true,
        searchDelay: 500,
        language: {
            emptyTable: '<div class="text-center py-4 text-muted"><i class="ri ri-inbox-line ri-2x mb-2 d-block text-secondary"></i>No customers with outstanding in this zone.</div>'
        },
        ajax: {
            url: "{{ url('sales_marketing_reports/ajax/outstanding-report') }}",
            type: "GET",
            data: function(d) {
                d.zone_id = zoneId;
                d.from_date = fromVal;
                d.to_date = toVal;
                d.customer_id = $('select[name="customer_id"]').val();
            }
        },
        columns: [
            { data: 'sno', name: 'sno', className: 'text-center fw-semibold text-muted' },
            { data: 'customer', name: 'customer' },
            { data: 'bills_count', name: 'bills_count', className: 'text-center' },
            { data: 'open_outstanding', name: 'open_outstanding', className: 'text-end' },
            { data: 'total_sales', name: 'total_sales', className: 'text-end' },
            { data: 'received', name: 'received', className: 'text-end' },
            { data: 'outstanding', name: 'outstanding', className: 'text-end' }
        ],
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
            var json = api.ajax.json();
            if (json && json.totals) {
                $('#zone_drill_bills_count').html(json.totals.bills_count);
                $('#zone_drill_open_outstanding').html(json.totals.open_outstanding);
                $('#zone_drill_total_sales').html(json.totals.total_sales);
                $('#zone_drill_received').html(json.totals.received);
                $('#zone_drill_outstanding').html(json.totals.outstanding);
                $('#drilldownZoneTotalBadge').text('Total Outstanding: ' + json.totals.outstanding);
            }
        }
    });
};

window.backToZoneAgingSummary = function() {
    $('#zoneAgingDrilldownWrapper').hide();
    $('#zoneAgingSummaryWrapper').fadeIn(150);
    window.currentAgingZoneId = null;
    if ($.fn.DataTable.isDataTable('#zoneAgingSummaryTable')) {
        $('#zoneAgingSummaryTable').DataTable().columns.adjust();
    }
};

$(document).on('click', '#zoneAgingSummaryTable tbody tr', function(e) {
    if ($(e.target).closest('a, button').length) {
        return;
    }
    var zoneId = $(this).attr('data-zone-id');
    var zoneName = $(this).attr('data-zone-name');
    if (!zoneId && $.fn.DataTable.isDataTable('#zoneAgingSummaryTable')) {
        var dt = $('#zoneAgingSummaryTable').DataTable();
        var rowData = dt.row(this).data();
        if (rowData) {
            zoneId = rowData.zone_id;
            zoneName = rowData.zone_name;
        }
    }
    if (zoneId && typeof window.drillDownZoneAging === 'function') {
        window.drillDownZoneAging(zoneId, zoneName);
    }
});

$(document).ready(function() {
    $('#importTallyForm').on('submit', function(e) {
        e.preventDefault();

        var form = this;
        var formData = new FormData(form);
        var submitBtn = $('#importTallySubmitBtn');
        var btnText = $('#importTallyBtnText');
        var btnSpinner = $('#importTallyBtnSpinner');
        var alertBox = $('#importTallyAlert');

        alertBox.hide().empty();
        submitBtn.prop('disabled', true);
        btnText.hide();
        btnSpinner.show();

        $.ajax({
            url: "{{ url('sales_marketing_reports/import_tally_outstanding') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                submitBtn.prop('disabled', false);
                btnText.show();
                btnSpinner.hide();

                if (res.status) {
                    alertBox.html('<div class="alert alert-success py-2 px-3 small mb-0"><i class="ri ri-checkbox-circle-line me-1"></i> ' + res.message + '</div>').fadeIn();

                    setTimeout(function() {
                        var modalEl = document.getElementById('importTallyModal');
                        var modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) { modal.hide(); }
                        form.reset();
                        alertBox.hide();

                        if ($.fn.DataTable.isDataTable('#outstandingReportTable')) {
                            $('#outstandingReportTable').DataTable().ajax.reload(null, false);
                        }
                        if ($.fn.DataTable.isDataTable('#zoneAgingSummaryTable')) {
                            $('#zoneAgingSummaryTable').DataTable().ajax.reload(null, false);
                        }
                    }, 1200);
                } else {
                    alertBox.html('<div class="alert alert-danger py-2 px-3 small mb-0"><i class="ri ri-error-warning-line me-1"></i> ' + (res.message || 'Import failed.') + '</div>').fadeIn();
                }
            },
            error: function(xhr) {
                submitBtn.prop('disabled', false);
                btnText.show();
                btnSpinner.hide();

                var msg = 'An error occurred during file upload. Please verify file format and try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                alertBox.html('<div class="alert alert-danger py-2 px-3 small mb-0"><i class="ri ri-error-warning-line me-1"></i> ' + msg + '</div>').fadeIn();
            }
        });
    });
});
</script>

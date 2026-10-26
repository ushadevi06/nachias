<!-- Level 1: Main Despatch Tracking Table -->
<div id="despatchMainTableWrapper">
    <div class="card-datatable table-responsive">
        <table class="datatables-products table premium-table table-hover text-nowrap" id="despatchReportTable">
            <thead class="bg-light">
                <tr>
                    <th class="text-center">S.No</th>
                    <th>Sales Order No</th>
                    <th>OrderAxe Order No</th>
                    <th>Order Type</th>
                    <th>Sale Order Date</th>
                    <th>Sales Executive</th>
                    <th>Customer Name</th>
                    <th>Place</th>
                    <th>Zone</th>
                    <th class="text-center">Dhoti Shirts</th>
                    <th class="text-center">White</th>
                    <th class="text-center">Core</th>
                    <th class="text-center">Bravo</th>
                    <th class="text-center">Deal</th>
                    <th class="text-center">Formal</th>
                    <th class="text-center fw-bold text-dark">Total Qty</th>
                    <th>Requested Delivery Date</th>
                    <th>Status</th>
                    <th class="text-center text-success fw-bold">Despatch Qty</th>
                    <th class="text-center text-danger fw-bold">Pending Qty</th>
                    <th class="text-center text-primary fw-bold">Pending Arts Nos</th>
                    <th>Partial D.Date</th>
                    <th>Despatch Complete Date</th>
                    <th>Reason for Delayed Delivery</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot class="bg-light fw-bold">
                <tr>
                    <th colspan="9" class="text-end text-uppercase">Total:</th>
                    <th class="text-center text-primary fw-bold" id="despatch_report_dhoti_qty">0</th>
                    <th class="text-center text-primary fw-bold" id="despatch_report_white_qty">0</th>
                    <th class="text-center text-primary fw-bold" id="despatch_report_core_qty">0</th>
                    <th class="text-center text-primary fw-bold" id="despatch_report_bravo_qty">0</th>
                    <th class="text-center text-primary fw-bold" id="despatch_report_deal_qty">0</th>
                    <th class="text-center text-primary fw-bold" id="despatch_report_formal_qty">0</th>
                    <th class="text-center fw-bold text-dark" id="despatch_report_total_qty">0</th>
                    <th colspan="2"></th>
                    <th class="text-center text-success fw-bold" id="despatch_report_delivered_qty">0</th>
                    <th class="text-center text-danger fw-bold" id="despatch_report_pending_qty">0</th>
                    <th class="text-center text-primary fw-bold" id="despatch_report_pending_arts_qty">-</th>
                    <th colspan="3"></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Level 2: In-Page Drilldown Container -->
<div id="despatchDrilldownWrapper" style="display: none;">
    <!-- Breadcrumb & Back Navigation -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 p-3 bg-light rounded-3 border">
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" onclick="backToDespatchMain()">
                <i class="ri ri-arrow-left-line me-1"></i> Back to Despatch Tracking
            </button>
            <span class="text-muted fw-semibold">
                Despatch Tracking &nbsp;<i class="ri ri-arrow-right-s-line"></i>&nbsp;
                <span class="text-dark fw-bold" id="despatchDrilldownBreadcrumb">Pending Items</span>
            </span>
        </div>
        <div>
            <span class="badge bg-danger rounded-pill px-3 py-2 fs-7" id="despatchDrilldownTotalBadge">Total Pending: 0</span>
        </div>
    </div>

    <!-- Order Info Header Card -->
    <div class="row g-3 mb-3 p-3 bg-white rounded-3 border shadow-xs" id="despatchDrilldownInfoCard">
        <div class="col-12 col-md-2">
            <span class="text-muted small d-block">Sales Order No:</span>
            <strong class="text-primary fs-6" id="drilldownSoNo">-</strong>
        </div>
        <div class="col-12 col-md-3">
            <span class="text-muted small d-block">Customer:</span>
            <strong class="text-dark" id="drilldownCustomer">-</strong>
        </div>
        <div class="col-12 col-md-2">
            <span class="text-muted small d-block">SO Date:</span>
            <strong class="text-dark" id="drilldownSoDate">-</strong>
        </div>
        <div class="col-12 col-md-2">
            <span class="text-muted small d-block">Order / Despatch Qty:</span>
            <strong class="text-dark fs-6"><span id="drilldownOrderQty">0</span> <span class="text-muted fw-normal">/</span> <span id="drilldownBilledQty" class="text-success">0</span></strong>
        </div>
        <div class="col-12 col-md-3">
            <span class="text-muted small d-block">Total Pending Qty:</span>
            <strong class="text-danger fs-5" id="drilldownPendingQty">0</strong>
        </div>
    </div>

    <!-- Drilldown Table Container -->
    <div class="card-datatable table-responsive position-relative" style="min-height: 220px;">
        <div id="despatchDrilldownLoader" class="text-center py-5" style="display: none;">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="text-muted small mt-2">Loading pending items...</p>
        </div>
        <table class="table table-bordered table-hover align-middle text-nowrap mb-0" id="despatchDrilldownTable" style="width: 100%;">
            <thead class="bg-light">
                <tr>
                    <th class="text-center" style="width: 50px;">S.No</th>
                    <th>Art No</th>
                    <th>Item Name</th>
                    <th class="text-center">Size</th>
                    <th class="text-center">Sleeve</th>
                    <th class="text-center text-danger fw-bold">Pending Qty</th>
                </tr>
            </thead>
            <tbody id="despatchDrilldownTbody">
            </tbody>
            <tfoot class="bg-light fw-bold border-top">
                <tr>
                    <th colspan="5" class="text-end text-uppercase">Total Pending Qty:</th>
                    <th class="text-center text-danger fs-6" id="despatchDrilldownTfootTotal">0</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<style>
    /* Sticky Columns specific styles */
    #despatch-report .table-responsive {
        overflow-x: auto;
    }
    
    .sticky-col-sno {
        position: sticky !important;
        left: 0;
        min-width: 60px;
        max-width: 60px;
        background-color: #fff !important;
        z-index: 1;
        box-shadow: inset -1px 0 0 #e2e8f0;
    }
    
    .sticky-col-so {
        position: sticky !important;
        left: 60px;
        min-width: 140px;
        max-width: 140px;
        background-color: #fff !important;
        z-index: 1;
        box-shadow: inset -1px 0 0 #e2e8f0;
    }
    
    .sticky-col-axe {
        position: sticky !important;
        left: 200px;
        min-width: 160px;
        max-width: 160px;
        background-color: #fff !important;
        z-index: 1;
        box-shadow: inset -1px 0 0 #e2e8f0;
    }

    #despatch-report thead .sticky-col-sno,
    #despatch-report thead .sticky-col-so,
    #despatch-report thead .sticky-col-axe {
        background-color: #f8f9fa !important;
        z-index: 3 !important;
    }

    #despatch-report tbody tr:hover .sticky-col-sno,
    #despatch-report tbody tr:hover .sticky-col-so,
    #despatch-report tbody tr:hover .sticky-col-axe {
        background-color: #f8faff !important;
    }
    
    /* Ensure DataTables doesn't hide sticky columns */
    #despatch-report .datatables-products th.sticky-col-sno,
    #despatch-report .datatables-products td.sticky-col-sno,
    #despatch-report .datatables-products th.sticky-col-so,
    #despatch-report .datatables-products td.sticky-col-so,
    #despatch-report .datatables-products th.sticky-col-axe,
    #despatch-report .datatables-products td.sticky-col-axe {
        display: table-cell !important;
    }
</style>

<script>
window.drillDownToDespatchPending = function(soId, soNo) {
    $('#despatchMainTableWrapper').hide();
    $('#despatchDrilldownWrapper').fadeIn(150);

    $('#despatchDrilldownBreadcrumb').text('Order No: ' + (soNo || ('#' + soId)));
    $('#drilldownSoNo').text(soNo || ('#' + soId));
    $('#drilldownCustomer').text('Loading...');
    $('#drilldownSoDate').text('-');
    $('#drilldownPendingQty').text('-');
    $('#despatchDrilldownTotalBadge').text('Loading...');

    if ($.fn.DataTable.isDataTable('#despatchDrilldownTable')) {
        try {
            $('#despatchDrilldownTable').DataTable().destroy();
        } catch(e) {}
    }

    $('#despatchDrilldownTbody').empty();
    $('#despatchDrilldownLoader').show();
    $('#despatchDrilldownTable').hide();

    $.ajax({
        url: "{{ url('sales_marketing_reports/lost_items_detail') }}/" + soId,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            $('#despatchDrilldownLoader').hide();
            $('#despatchDrilldownTable').show();

            $('#drilldownCustomer').text(res.customer || '-');
            $('#drilldownSoDate').text(res.so_date || '-');
            var totalPending = res.total_lost_qty || '0';

            $('#drilldownOrderQty').text(res.order_qty || '0');
            $('#drilldownBilledQty').text(res.delivered_qty || '0');
            $('#drilldownPendingQty').text(totalPending);

            $('#despatchDrilldownTotalBadge').text('Total Pending: ' + totalPending + ' pcs');
            $('#despatchDrilldownTfootTotal').text(totalPending);

            var items = res.items || res.data || [];
            var tbody = $('#despatchDrilldownTbody');
            tbody.empty();

            if (!items.length) {
                tbody.html('<tr><td colspan="6" class="text-center py-4 text-muted"><i class="ri-checkbox-circle-line ri-2x text-success d-block mb-1"></i>No pending items for this order. All items delivered!</td></tr>');
                return;
            }

            items.forEach(function(item, idx) {
                var artNo = item.art_no_raw || item.art_no || '-';
                var name = item.description || item.item_name || item.sku || '-';
                var size = item.size || '-';
                var sleeve = item.sleeve || '-';
                var qty = item.qty_raw !== undefined ? item.qty_raw : (parseFloat(item.qty) || 0);

                var row = '<tr>' +
                    '<td class="text-center fw-semibold text-muted">' + (idx + 1) + '</td>' +
                    '<td class="fw-bold text-primary">' + artNo + '</td>' +
                    '<td class="fw-medium text-dark">' + name + '</td>' +
                    '<td class="text-center">' + size + '</td>' +
                    '<td class="text-center">' + sleeve + '</td>' +
                    '<td class="text-center text-danger fw-bold">' + Number(qty).toLocaleString('en-IN') + '</td>' +
                '</tr>';
                tbody.append(row);
            });

            $('#despatchDrilldownTable').DataTable({
                processing: true,
                paging: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                dom: '<"row mb-2 align-items-center"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>rt<"row mt-2 align-items-center"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6 d-flex justify-content-md-end"p>>',
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search pending items...",
                    lengthMenu: "Show _MENU_ entries"
                }
            });
        },
        error: function(err) {
            $('#despatchDrilldownLoader').hide();
            $('#despatchDrilldownTable').show();
            $('#despatchDrilldownTbody').html('<tr><td colspan="6" class="text-center py-4 text-danger"><i class="ri-error-warning-line ri-2x d-block mb-1"></i>Failed to load pending items. Please try again.</td></tr>');
        }
    });
};

window.showDespatchPendingItems = window.drillDownToDespatchPending;

window.backToDespatchMain = function() {
    $('#despatchDrilldownWrapper').hide();
    $('#despatchMainTableWrapper').fadeIn(150);
    if ($.fn.DataTable.isDataTable('#despatchReportTable')) {
        $('#despatchReportTable').DataTable().columns.adjust();
    }
};
</script>

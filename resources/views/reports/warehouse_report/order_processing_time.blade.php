<div class="mb-3 d-flex align-items-center" id="orderProcessingBreadcrumbs" style="display: none !important;">
    <button class="btn btn-sm btn-outline-secondary me-2" onclick="renderOrderProcessingTimeMainLevel()" id="btnBackToOrders">
        <i class="ri ri-arrow-left-line me-1"></i> Back to Orders
    </button>
    <span class="text-muted fw-bold" id="orderProcessingBreadcrumbText">All Orders</span>
</div>

<div class="card-datatable table-responsive">
    <table class="datatables-products table table-hover border-top align-middle" id="orderProcessingTimeTable" style="font-size: 0.82rem;">
        <thead class="table-light text-center">
            <tr>
                <th class="text-nowrap">DATE</th>
                <th class="text-nowrap">S.NO</th>
                <th class="text-nowrap">ORDER DATE</th>
                <th class="text-nowrap">ORDER NO</th>
                <th class="text-nowrap">DELIVERY DATE</th>
                <th class="text-nowrap">PRIORITY</th>
                <th class="text-nowrap">CUSTOMER</th>
                <th class="text-nowrap">PLACE</th>
                <th class="text-nowrap">BRAND</th>
                <th class="text-nowrap">ORDER QTY</th>
                <th class="text-nowrap">INVOICED QTY</th>
                <th class="text-nowrap">PENDING QTY</th>
                <th class="text-nowrap">ORDER COMPLETED</th>
                <th class="text-nowrap">STATUS</th>
                <th class="text-nowrap">AWAITING ART NO</th>
                <th class="text-nowrap">WIP</th>
                <th class="text-nowrap">ACTION REQUIRED</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<script>
var isOrderProcessingRootLevel = true;

function reinitOrderProcessingTable() {
    if ($.fn.DataTable.isDataTable('#orderProcessingTimeTable')) {
        $('#orderProcessingTimeTable').DataTable().clear().destroy();
    }
    $('#orderProcessingTimeTable').empty();
}

function renderOrderProcessingTimeMainLevel() {
    isOrderProcessingRootLevel = true;
    $('#orderProcessingBreadcrumbs').attr('style', 'display: none !important;');

    reinitOrderProcessingTable();

    $('#orderProcessingTimeTable').html(`
        <thead class="table-light text-center">
            <tr>
                <th class="text-nowrap">DATE</th>
                <th class="text-nowrap">S.NO</th>
                <th class="text-nowrap">ORDER DATE</th>
                <th class="text-nowrap">ORDER NO</th>
                <th class="text-nowrap">DELIVERY DATE</th>
                <th class="text-nowrap">PRIORITY</th>
                <th class="text-nowrap">CUSTOMER</th>
                <th class="text-nowrap">PLACE</th>
                <th class="text-nowrap">BRAND</th>
                <th class="text-nowrap">ORDER QTY</th>
                <th class="text-nowrap">INVOICED QTY</th>
                <th class="text-nowrap">PENDING QTY</th>
                <th class="text-nowrap">ORDER COMPLETED</th>
                <th class="text-nowrap">STATUS</th>
                <th class="text-nowrap">AWAITING ART NO</th>
                <th class="text-nowrap">WIP</th>
                <th class="text-nowrap">ACTION REQUIRED</th>
            </tr>
        </thead>
        <tbody></tbody>
    `);

    $('#orderProcessingTimeTable').DataTable({
        processing: true,
        autoWidth: false,
        serverSide: true,
        ajax: {
            url: "{{ url('warehouse_reports/ajax/order-processing-time') }}",
            data: function (d) {
                d.from_date = $('.start_date').val();
                d.to_date = $('.end_date').val();
                d.brand_id = $('select[name="brand_id"]').val();
                d.store_id = $('select[name="store_id"]').val();
            }
        },
        columns: [
            { data: 'date', name: 'date', className: 'text-center text-nowrap' },
            { data: 'sno', name: 'sno', className: 'text-center', orderable: false },
            { data: 'order_date', name: 'order_date', className: 'text-center text-nowrap' },
            { data: 'so_no', name: 'so_no', className: 'text-center fw-bold text-nowrap' },
            { data: 'delivery_date', name: 'delivery_date', className: 'text-center text-nowrap' },
            { data: 'priority', name: 'priority', className: 'text-center' },
            { data: 'customer', name: 'customer' },
            { data: 'place', name: 'place' },
            { data: 'brand', name: 'brand' },
            { data: 'ordered_qty', name: 'ordered_qty', className: 'text-center' },
            { data: 'invoiced_qty', name: 'invoiced_qty', className: 'text-center' },
            { data: 'pending_qty', name: 'pending_qty', className: 'text-center' },
            { data: 'completion', name: 'completion', className: 'text-center' },
            { data: 'status', name: 'status', className: 'text-center' },
            { data: 'awaiting_art_nos', name: 'awaiting_art_nos', className: 'text-center', orderable: false },
            { data: 'wip', name: 'wip', className: 'text-center', orderable: false },
            { data: 'action_required', name: 'action_required', orderable: false }
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10,
        drawCallback: function (settings) {
            var api = this.api();
            var rows = api.rows({ page: 'current' }).nodes();
            var lastDate = null;

            api.column(0, { page: 'current' }).data().each(function (groupDate, i) {
                if (lastDate !== groupDate) {
                    $(rows).eq(i).before(
                        '<tr class="table-light date-group-header-row"><td colspan="17" class="py-2 px-3 fw-bold text-primary text-uppercase border-top border-bottom" style="background-color: #f1f5f9 !important; font-size: 0.88rem; letter-spacing: 0.5px;"><i class="ri-calendar-event-line me-2 text-primary"></i>DATE : ' + groupDate + '</td></tr>'
                    );
                    lastDate = groupDate;
                }
            });
        }
    });
}

function drillDownToOrderPendingArtNos(soId, soNo) {
    isOrderProcessingRootLevel = false;

    $('#orderProcessingBreadcrumbs').attr('style', 'display: flex !important;');
    $('#orderProcessingBreadcrumbText').html(`All Orders &nbsp; <i class="ri-arrow-right-s-line"></i> &nbsp; Order No: <span class="text-danger fw-bold">${soNo}</span> &nbsp; <i class="ri-arrow-right-s-line"></i> &nbsp; <span class="text-primary fw-bold">Awaiting Art Numbers Breakdown</span>`);

    reinitOrderProcessingTable();

    $('#orderProcessingTimeTable').html(`
        <thead class="text-center">
            <tr>
                <th style="width: 40px;">#</th>
                <th>ART NO</th>
                <th>ITEM NAME</th>
                <th class="text-center">SLEEVE</th>
                <th class="text-center">SIZE</th>
                <th class="text-center">ORDERED QTY</th>
                <th class="text-center">INVOICED QTY</th>
                <th class="text-center">PENDING QTY</th>
                <th class="text-center">STATUS</th>
            </tr>
        </thead>
        <tbody></tbody>
    `);

    $('#orderProcessingTimeTable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax: {
            url: "{{ url('warehouse_reports/order_processing_time_art_nos') }}",
            data: function (d) {
                d.so_id = soId;
            }
        },
        columns: [
            { data: 'sno', name: 'sno', className: 'text-center font-monospace', width: '5%' },
            { data: 'art_no', name: 'art_no', width: '15%' },
            { data: 'item_name', name: 'item_name', width: '30%' },
            { data: 'sleeve', name: 'sleeve', className: 'text-center', width: '10%' },
            { data: 'size', name: 'size', className: 'text-center', width: '10%' },
            { data: 'ordered_qty', name: 'ordered_qty', className: 'text-center fw-bold', width: '10%' },
            { data: 'invoiced_qty', name: 'invoiced_qty', className: 'text-center', width: '10%' },
            { data: 'pending_qty', name: 'pending_qty', className: 'text-center fw-bold text-danger', width: '10%' },
            { data: 'status', name: 'status', className: 'text-center', width: '10%' }
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10,
        language: {
            emptyTable: "No pending art numbers found for this order",
            zeroRecords: "No matching pending art numbers found",
            infoEmpty: "Showing 0 to 0 entries"
        }
    });
}

window.initOrderProcessingTimeTable = function() {
    renderOrderProcessingTimeMainLevel();
};
function initOrderProcessingTimeTable() {
    window.initOrderProcessingTimeTable();
}

window.initOrderProcessingTime = function() {
    window.initOrderProcessingTimeTable();
};
function initOrderProcessingTime() {
    window.initOrderProcessingTimeTable();
}

window.renderOrderProcessingTimeRootLevel = function() {
    renderOrderProcessingTimeMainLevel();
};
function renderOrderProcessingTimeRootLevel() {
    renderOrderProcessingTimeMainLevel();
}

window.renderCompletionRootLevel = function() {
    renderOrderProcessingTimeMainLevel();
};
function renderCompletionRootLevel() {
    renderOrderProcessingTimeMainLevel();
}

if ($('#order-processing-time').hasClass('active') || $('#order-completion').hasClass('active')) {
    window.initOrderProcessingTimeTable();
}

$(document).on('click', '.view-awaiting-art-nos', function(e) {
    e.preventDefault();
    const btn = $(this);
    const soId = btn.data('so-id');
    const soNo = btn.data('so-no');
    drillDownToOrderPendingArtNos(soId, soNo);
});
</script>

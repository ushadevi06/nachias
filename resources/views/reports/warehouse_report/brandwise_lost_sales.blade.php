<!-- Lost Sales Breadcrumb Navigation -->
<div class="mb-3 d-flex align-items-center" id="lostSalesBreadcrumbs" style="display: none !important;">
    <button class="btn btn-sm btn-outline-secondary me-2" onclick="lostSalesGoBack()" id="btnBackToLostSales">
        <i class="ri ri-arrow-left-line me-1"></i> <span id="lostSalesBackBtnText">Back to Brands</span>
    </button>
    <span class="text-dark fw-bold" id="lostSalesBreadcrumbText">All Brands</span>
</div>

<!-- Level 1: Brand Summary Table -->
<div id="lostSalesBrandsContainer" class="card-datatable table-responsive">
    <table class="datatables-products table table-hover" id="brandwiseLostSalesBrandsTable">
        <thead class="table-light">
            <tr>
                <th>Brand</th>
                <th class="text-center">Missed Orders</th>
                <th class="text-center">Lost Revenue</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot class="table-light border-top">
            <tr class="bg-light fw-bold">
                <th class="fw-bold text-start">TOTAL</th>
                <th class="text-center fw-bold text-primary" id="lostSalesFootOrders">0</th>
                <th class="text-center fw-bold text-danger" id="lostSalesFootRevenue">₹0.00</th>
            </tr>
        </tfoot>
    </table>
</div>

<!-- Level 2: Brand Orders Drilldown Table -->
<div id="lostSalesOrdersContainer" class="card-datatable table-responsive" style="display: none;">
    <table class="datatables-products table table-hover" id="brandwiseLostSalesOrdersTable">
        <thead class="table-light">
            <tr>
                <th>SO No</th>
                <th>Customer</th>
                <th class="text-center">Order Date</th>
                <th class="text-center">Ordered Qty</th>
                <th class="text-center">Ordered Invoiced Qty</th>
                <th class="text-center">Open Order Qty</th>
                <th class="text-center">Total Qty</th>
                <th class="text-center">Lost Qty</th>
                <th class="text-center">Lost Revenue</th>
                <th>Reason for Delay</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot class="table-light border-top">
            <tr class="bg-light fw-bold">
                <th colspan="3" class="text-end fw-bold">TOTAL</th>
                <th class="text-center fw-bold text-primary" id="lostOrdersFootOrdered">0</th>
                <th class="text-center fw-bold text-primary" id="lostOrdersFootInvoiced">0</th>
                <th class="text-center fw-bold text-info" id="lostOrdersFootOpenOrder">0</th>
                <th class="text-center fw-bold text-dark" id="lostOrdersFootTotalQty">0</th>
                <th class="text-center fw-bold text-warning" id="lostOrdersFootLostQty">0</th>
                <th class="text-center fw-bold text-danger" id="lostOrdersFootLostVal">₹0.00</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>

<!-- Level 3: Lost Items Drilldown Sub Page -->
<div id="lostSalesItemsContainer" style="display: none;">
    <!-- Order Summary Bar -->
    <div class="card bg-label-light border shadow-none mb-3">
        <div class="card-body py-2 px-3">
            <div class="row align-items-center g-2">
                <div class="col-md-3">
                    <small class="text-muted d-block">Sales Order</small>
                    <span class="fw-bold text-dark fs-6" id="lostItemsSoNoHeader">-</span>
                </div>
                <div class="col-md-4">
                    <small class="text-muted d-block">Customer</small>
                    <span class="fw-bold text-dark" id="lostItemsCustomerHeader">-</span>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Order Date</small>
                    <span class="fw-bold text-dark" id="lostItemsDateHeader">-</span>
                </div>
                <div class="col-md-2 text-md-end">
                    <small class="text-muted d-block">Total Lost Qty</small>
                    <span class="badge bg-danger fs-6 fw-bold" id="lostItemsTotalQtyBadge">0</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="card-datatable table-responsive">
        <table class="datatables-products table table-hover border-top align-middle" id="brandwiseLostItemsTable">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width: 45px;">#</th>
                    <th>Art No</th>
                    <th>Item Description</th>
                    <th>Barcode</th>
                    <th class="text-center">Color</th>
                    <th class="text-center">Size</th>
                    <th class="text-center">Sleeve</th>
                    <th class="text-center">Lost Qty</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot class="table-light border-top">
                <tr class="bg-light fw-bold">
                    <th colspan="7" class="text-end fw-bold">TOTAL LOST QTY</th>
                    <th class="text-center fw-bold text-danger" id="lostItemsFootTotalQty">0</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<style>
    #brandwiseLostSalesBrandsTable tbody tr.clickable-brand-row:hover td,
    #brandwiseLostSalesBrandsTable tbody tr:hover td {
        background-color: rgba(105, 108, 255, 0.08) !important;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }
    .lost-qty-drill-link {
        cursor: pointer;
        text-decoration: underline;
        transition: opacity 0.2s;
    }
    .lost-qty-drill-link:hover {
        opacity: 0.8;
    }
    .cursor-pointer {
        cursor: pointer;
    }
    .cursor-pointer:hover {
        text-decoration: underline;
    }
</style>

<script>
var lostSalesLevel = 1; // 1: Brands, 2: Orders, 3: Lost Items
var currentLostSalesBrand = '';
var currentLostSalesSoId = null;
var currentLostSalesSoNo = '';
var currentLostSalesCustomer = '';
var currentLostSalesSoDate = '';

function lostSalesGoBack() {
    if (lostSalesLevel === 3) {
        drillDownToLostSalesBrand(currentLostSalesBrand);
    } else if (lostSalesLevel === 2) {
        renderLostSalesBrandLevel();
    }
}

function renderLostSalesBrandLevel() {
    lostSalesLevel = 1;
    currentLostSalesBrand = '';
    currentLostSalesSoId = null;

    $('#lostSalesBreadcrumbs').attr('style', 'display: none !important;');
    $('#lostSalesOrdersContainer').hide();
    $('#lostSalesItemsContainer').hide();
    $('#lostSalesBrandsContainer').show();

    if ($.fn.DataTable.isDataTable('#brandwiseLostSalesBrandsTable')) {
        var dt = $('#brandwiseLostSalesBrandsTable').DataTable();
        if (dt.ajax && typeof dt.ajax.reload === 'function' && dt.ajax.url()) {
            dt.ajax.reload(null, false);
            return;
        }
        dt.clear().destroy();
        $('#brandwiseLostSalesBrandsTable tbody').empty();
    }

    if (typeof window.showWarehouseReportLoading === 'function') {
        window.showWarehouseReportLoading(true);
    }

    $('#brandwiseLostSalesBrandsTable').DataTable({
        processing: true,
        autoWidth: false,
        serverSide: true,
        ajax: {
            url: "{{ url('warehouse_reports/ajax/brandwise-lost-sales') }}",
            data: function (d) {
                d.from_date = $('.start_date').val();
                d.to_date = $('.end_date').val();
                d.brand_id = $('select[name="brand_id"]').val();
                d.customer_id = $('select[name="customer_id"]').val();
                d.store_id = $('select[name="store_id"]').val();
            }
        },
        columns: [
            {
                data: 'brand',
                name: 'brand',
                render: function(data, type, row) {
                    return '<a href="javascript:void(0);" class="fw-bold text-primary view-lost-brand-orders">' + data + '</a>';
                }
            },
            { data: 'missed_orders', name: 'missed_orders', className: 'text-center' },
            { data: 'lost_value', name: 'lost_value', className: 'text-center' }
        ],
        drawCallback: function() {
            if (typeof window.showWarehouseReportLoading === 'function') {
                window.showWarehouseReportLoading(false);
            }
            let submitBtn = $('#warehouseReportForm').find('button[type="submit"]');
            if (submitBtn.length && submitBtn.data('original-html')) {
                submitBtn.html(submitBtn.data('original-html')).prop('disabled', false);
            }
            var api = this.api();
            var json = (this.api().ajax && this.api().ajax.json) ? this.api().ajax.json() : null;
            var fmt = function (num) { return parseFloat(num || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 }); };
            var cur = function (num) { return '₹' + parseFloat(num || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); };

            if (json && json.totals) {
                $('#lostSalesFootOrders').html(fmt(json.totals.missed_orders));
                $('#lostSalesFootRevenue').html(cur(json.totals.lost_revenue));
            } else {
                var intVal = function (i) {
                    if (typeof i === 'number') return i;
                    if (typeof i === 'string') {
                        var val = parseFloat(i.replace(/<[^>]*>/g, '').replace(/[^\d.-]/g, ''));
                        return isNaN(val) ? 0 : val;
                    }
                    return 0;
                };
                $('#lostSalesFootOrders').html(fmt(api.column(1, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
                $('#lostSalesFootRevenue').html(cur(api.column(2, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
            }
        },
        initComplete: function() {
            if (typeof window.showWarehouseReportLoading === 'function') {
                window.showWarehouseReportLoading(false);
            }
        },
        language: {
            emptyTable: "No lost sales found matching your filters",
            zeroRecords: "No matching records found"
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10
    });
}

function drillDownToLostSalesBrand(brandName) {
    lostSalesLevel = 2;
    currentLostSalesBrand = brandName;
    currentLostSalesSoId = null;

    $('#lostSalesBreadcrumbs').removeAttr('style').attr('style', 'display: flex !important;');
    $('#lostSalesBackBtnText').text('Back to Brands');
    $('#lostSalesBreadcrumbText').html('All Brands &nbsp; <i class="ri ri-arrow-right-s-line"></i> &nbsp; <span class="text-primary text-uppercase">' + brandName + '</span> (Missed Sales Orders)');

    $('#lostSalesBrandsContainer').hide();
    $('#lostSalesItemsContainer').hide();
    $('#lostSalesOrdersContainer').show();

    if ($.fn.DataTable.isDataTable('#brandwiseLostSalesOrdersTable')) {
        $('#brandwiseLostSalesOrdersTable').DataTable().clear().destroy();
    }

    $('#brandwiseLostSalesOrdersTable tbody').empty();

    if (typeof window.showWarehouseReportLoading === 'function') {
        window.showWarehouseReportLoading(true);
    }

    $('#brandwiseLostSalesOrdersTable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax: {
            url: "{{ url('warehouse_reports/brandwise_lost_sales_orders') }}",
            type: "GET",
            data: function (d) {
                d.brand_name = currentLostSalesBrand;
                d.from_date = $('.start_date').val();
                d.to_date = $('.end_date').val();
                d.customer_id = $('select[name="customer_id"]').val();
                d.store_id = $('select[name="store_id"]').val();
            }
        },
        columns: [
            { data: 'so_no', name: 'so_no' },
            { data: 'customer', name: 'customer' },
            { data: 'so_date', name: 'so_date', className: 'text-center' },
            { data: 'ordered_qty', name: 'ordered_qty', className: 'text-center' },
            { data: 'invoiced_qty', name: 'invoiced_qty', className: 'text-center' },
            { data: 'open_order_qty', name: 'open_order_qty', className: 'text-center' },
            { data: 'total_qty', name: 'total_qty', className: 'text-center' },
            {
                data: 'lost_qty', name: 'lost_qty', className: 'text-center',
                render: function(data, type, row) {
                    if (type === 'display') {
                        var rawQty = $(data).text().trim();
                        if (!rawQty || rawQty === '0') return data;
                        return '<a href="javascript:void(0);" class="text-danger fw-bold lost-qty-drill-link" data-so-id="' + row.so_id + '" data-so-no="' + (row.so_no_plain || '') + '" data-customer="' + (row.customer || '') + '" data-so-date="' + (row.so_date || '') + '" title="View Lost Items Detail">' + rawQty + ' <i class="ri ri-arrow-right-s-line" style="font-size:13px;"></i></a>';
                    }
                    return data;
                }
            },
            { data: 'lost_value', name: 'lost_value', className: 'text-center' },
            { data: 'reason', name: 'reason' }
        ],
        drawCallback: function() {
            if (typeof window.showWarehouseReportLoading === 'function') {
                window.showWarehouseReportLoading(false);
            }
            let submitBtn = $('#warehouseReportForm').find('button[type="submit"]');
            if (submitBtn.length && submitBtn.data('original-html')) {
                submitBtn.html(submitBtn.data('original-html')).prop('disabled', false);
            }
            var api = this.api();
            var fmt = function (num) { return parseFloat(num || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 }); };
            var cur = function (num) { return '₹' + parseFloat(num || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); };
            var json = api.ajax.json();

            if (json && json.totals) {
                $('#lostOrdersFootOrdered').html(fmt(json.totals.ordered_qty));
                $('#lostOrdersFootInvoiced').html(fmt(json.totals.invoiced_qty));
                $('#lostOrdersFootOpenOrder').html(fmt(json.totals.open_order_qty));
                $('#lostOrdersFootTotalQty').html(fmt(json.totals.total_qty));
                $('#lostOrdersFootLostQty').html(fmt(json.totals.lost_qty));
                $('#lostOrdersFootLostVal').html(cur(json.totals.lost_value));
            } else {
                var intVal = function (i) {
                    if (typeof i === 'number') return i;
                    if (typeof i === 'string') {
                        var val = parseFloat(i.replace(/<[^>]*>/g, '').replace(/[^\d.-]/g, ''));
                        return isNaN(val) ? 0 : val;
                    }
                    return 0;
                };
                $('#lostOrdersFootOrdered').html(fmt(api.column(3, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
                $('#lostOrdersFootInvoiced').html(fmt(api.column(4, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
                $('#lostOrdersFootOpenOrder').html(fmt(api.column(5, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
                $('#lostOrdersFootTotalQty').html(fmt(api.column(6, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
                $('#lostOrdersFootLostQty').html(fmt(api.column(7, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
                $('#lostOrdersFootLostVal').html(cur(api.column(8, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
            }
        },
        initComplete: function() {
            if (typeof window.showWarehouseReportLoading === 'function') {
                window.showWarehouseReportLoading(false);
            }
        },
        language: {
            emptyTable: "No missed sales orders found for this brand",
            zeroRecords: "No matching sales orders found"
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10
    });
}

function drillDownToLostSalesItems(soId, soNo, customer, soDate) {
    lostSalesLevel = 3;
    currentLostSalesSoId = soId;
    currentLostSalesSoNo = soNo || ('SO #' + soId);
    currentLostSalesCustomer = customer || '-';
    currentLostSalesSoDate = soDate || '-';

    $('#lostSalesBreadcrumbs').removeAttr('style').attr('style', 'display: flex !important;');
    $('#lostSalesBackBtnText').text('Back to Orders');
    $('#lostSalesBreadcrumbText').html(
        'All Brands &nbsp; <i class="ri ri-arrow-right-s-line"></i> &nbsp; ' +
        '<span class="text-secondary cursor-pointer" onclick="drillDownToLostSalesBrand(\'' + currentLostSalesBrand.replace(/'/g, "\\'") + '\')">' + currentLostSalesBrand + '</span> &nbsp; <i class="ri ri-arrow-right-s-line"></i> &nbsp; ' +
        'Order No: <span class="text-primary fw-bold">' + currentLostSalesSoNo + '</span> &nbsp; <i class="ri ri-arrow-right-s-line"></i> &nbsp; ' +
        '<span class="text-danger fw-bold">Lost Items Detail</span>'
    );

    // Update header bar
    $('#lostItemsSoNoHeader').text(currentLostSalesSoNo);
    $('#lostItemsCustomerHeader').text(currentLostSalesCustomer);
    $('#lostItemsDateHeader').text(currentLostSalesSoDate);
    $('#lostItemsTotalQtyBadge').text('Loading...');
    $('#lostItemsFootTotalQty').text('0');

    $('#lostSalesBrandsContainer').hide();
    $('#lostSalesOrdersContainer').hide();
    $('#lostSalesItemsContainer').show();

    if ($.fn.DataTable.isDataTable('#brandwiseLostItemsTable')) {
        $('#brandwiseLostItemsTable').DataTable().clear().destroy();
    }
    $('#brandwiseLostItemsTable tbody').empty();

    if (typeof window.showWarehouseReportLoading === 'function') {
        window.showWarehouseReportLoading(true);
    }

    $('#brandwiseLostItemsTable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax: {
            url: "{{ url('warehouse_reports/brandwise_lost_sales_items') }}",
            type: "GET",
            data: function (d) {
                d.so_id = currentLostSalesSoId;
            }
        },
        columns: [
            { data: 'sno', name: 'sno', className: 'text-center text-muted font-monospace' },
            { data: 'art_no', name: 'art_no' },
            { data: 'description', name: 'description' },
            { data: 'barcode', name: 'barcode' },
            { data: 'color', name: 'color', className: 'text-center' },
            { data: 'size', name: 'size', className: 'text-center' },
            { data: 'sleeve', name: 'sleeve', className: 'text-center' },
            { data: 'qty', name: 'qty', className: 'text-center' }
        ],
        drawCallback: function(settings) {
            if (typeof window.showWarehouseReportLoading === 'function') {
                window.showWarehouseReportLoading(false);
            }
            var json = settings.json;
            if (json) {
                if (json.total_lost_qty !== undefined) {
                    $('#lostItemsTotalQtyBadge').text(json.total_lost_qty);
                    $('#lostItemsFootTotalQty').text(json.total_lost_qty);
                }
                if (json.customer && json.customer !== '-') {
                    $('#lostItemsCustomerHeader').text(json.customer);
                }
                if (json.so_date && json.so_date !== '-') {
                    $('#lostItemsDateHeader').text(json.so_date);
                }
            } else {
                var api = this.api();
                var intVal = function (i) {
                    if (typeof i === 'number') return i;
                    if (typeof i === 'string') {
                        var val = parseFloat(i.replace(/<[^>]*>/g, '').replace(/[^\d.-]/g, ''));
                        return isNaN(val) ? 0 : val;
                    }
                    return 0;
                };
                var total = api.column(7, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                var formattedTotal = total.toLocaleString('en-IN');
                $('#lostItemsTotalQtyBadge').text(formattedTotal);
                $('#lostItemsFootTotalQty').text(formattedTotal);
            }
        },
        initComplete: function() {
            if (typeof window.showWarehouseReportLoading === 'function') {
                window.showWarehouseReportLoading(false);
            }
        },
        language: {
            emptyTable: "No lost items found for this order",
            zeroRecords: "No matching items found"
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10
    });
}

window.initBrandwiseLostSalesTable = function() {
    if (lostSalesLevel === 1) {
        renderLostSalesBrandLevel();
    } else if (lostSalesLevel === 2) {
        if ($.fn.DataTable.isDataTable('#brandwiseLostSalesOrdersTable')) {
            var dtOrders = $('#brandwiseLostSalesOrdersTable').DataTable();
            if (dtOrders.ajax && typeof dtOrders.ajax.reload === 'function' && dtOrders.ajax.url()) {
                dtOrders.ajax.reload(null, false);
                return;
            }
        }
        drillDownToLostSalesBrand(currentLostSalesBrand);
    } else if (lostSalesLevel === 3) {
        if ($.fn.DataTable.isDataTable('#brandwiseLostItemsTable')) {
            var dtItems = $('#brandwiseLostItemsTable').DataTable();
            if (dtItems.ajax && typeof dtItems.ajax.reload === 'function' && dtItems.ajax.url()) {
                dtItems.ajax.reload(null, false);
                return;
            }
        }
        drillDownToLostSalesItems(currentLostSalesSoId, currentLostSalesSoNo, currentLostSalesCustomer, currentLostSalesSoDate);
    }
};

function initBrandwiseLostSalesTable() {
    window.initBrandwiseLostSalesTable();
}

// Brand click handler in Level 1 Table
$(document).on('click', '#brandwiseLostSalesBrandsTable tbody tr', function(e) {
    if ($(e.target).is('a') || $(e.target).is('button') || $(e.target).closest('a').length > 0 || $(e.target).closest('button').length > 0) {
        return;
    }
    var brandName = $(this).find('td:first').text().trim();
    if (brandName && brandName !== 'No lost sales found matching your filters' && brandName !== 'No matching records found') {
        drillDownToLostSalesBrand(brandName);
    }
});

$(document).on('click', '.view-lost-brand-orders', function(e) {
    e.preventDefault();
    var brandName = $(this).text().trim();
    if (brandName) {
        drillDownToLostSalesBrand(brandName);
    }
});

// Lost Qty click handler in Level 2 Table (Drill down to Level 3)
$(document).on('click', '.lost-qty-drill-link', function(e) {
    e.preventDefault();
    var soId     = $(this).data('so-id');
    var soNo     = $(this).data('so-no') || ('SO #' + soId);
    var customer = $(this).data('customer') || '-';
    var soDate   = $(this).data('so-date') || '-';
    if (!soId) return;

    drillDownToLostSalesItems(soId, soNo, customer, soDate);
});

// Auto-init if tab is active
if ($('#brandwise-lost-sales').hasClass('active') || $('#brand-lost-sales').hasClass('active')) {
    initBrandwiseLostSalesTable();
}
</script>

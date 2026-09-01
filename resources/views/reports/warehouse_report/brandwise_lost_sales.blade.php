<div class="mb-3 d-flex align-items-center" id="lostSalesBreadcrumbs" style="display: none !important;">
    <button class="btn btn-sm btn-outline-secondary me-2" onclick="renderLostSalesBrandLevel()" id="btnBackToLostSalesBrands">
        <i class="ri ri-arrow-left-line me-1"></i> Back to Brands
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
                <th class="text-center">Invoiced Qty</th>
                <th class="text-center">Lost Qty</th>
                <th class="text-center">Lost Revenue</th>
                <th>Reason for Delay</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<style>
    #brandwiseLostSalesBrandsTable tbody tr.clickable-brand-row:hover td {
        background-color: rgba(105, 108, 255, 0.08) !important;
        transition: all 0.2s ease-in-out;
    }
    .dataTables_processing {
        background: rgba(255, 255, 255, 0.92) !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
        z-index: 1050 !important;
        padding: 15px !important;
    }
</style>

<script>
var lostSalesIsRoot = true;
var currentLostSalesBrand = '';

function renderLostSalesBrandLevel() {
    lostSalesIsRoot = true;
    currentLostSalesBrand = '';
    $('#lostSalesBreadcrumbs').attr('style', 'display: none !important;');
    $('#lostSalesOrdersContainer').hide();
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
                d.store_id = $('select[name="store_id"]').val();
            }
        },
        columns: [
            { data: 'brand', name: 'brand' },
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
        },
        initComplete: function() {
            if (typeof window.showWarehouseReportLoading === 'function') {
                window.showWarehouseReportLoading(false);
            }
        },
        language: {
            processing: '<div class="py-4"><div class="spinner-border text-primary me-2" role="status"></div> <span class="fw-bold text-primary align-middle">Loading Brandwise Lost Sales...</span></div>',
            emptyTable: "No lost sales found matching your filters",
            zeroRecords: "No matching records found"
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10
    });
}

function drillDownToLostSalesBrand(brandName) {
    lostSalesIsRoot = false;
    currentLostSalesBrand = brandName;
    $('#lostSalesBreadcrumbs').removeAttr('style');
    $('#lostSalesBreadcrumbText').html('All Brands &gt; <span class="text-primary text-uppercase">' + brandName + '</span> (Missed Sales Orders)');

    $('#lostSalesBrandsContainer').hide();
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
                d.store_id = $('select[name="store_id"]').val();
            }
        },
        columns: [
            { data: 'so_no', name: 'so_no' },
            { data: 'customer', name: 'customer' },
            { data: 'so_date', name: 'so_date', className: 'text-center' },
            { data: 'ordered_qty', name: 'ordered_qty', className: 'text-center' },
            { data: 'invoiced_qty', name: 'invoiced_qty', className: 'text-center' },
            { data: 'lost_qty', name: 'lost_qty', className: 'text-center' },
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
        },
        initComplete: function() {
            if (typeof window.showWarehouseReportLoading === 'function') {
                window.showWarehouseReportLoading(false);
            }
        },
        language: {
            processing: '<div class="py-4"><div class="spinner-border text-primary me-2" role="status"></div> <span class="fw-bold text-primary align-middle">Loading Sales Orders...</span></div>',
            emptyTable: "No missed sales orders found for this brand",
            zeroRecords: "No matching sales orders found"
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10
    });
}

window.initBrandwiseLostSalesTable = function() {
    if (lostSalesIsRoot) {
        renderLostSalesBrandLevel();
    } else {
        if ($.fn.DataTable.isDataTable('#brandwiseLostSalesOrdersTable')) {
            var dtOrders = $('#brandwiseLostSalesOrdersTable').DataTable();
            if (dtOrders.ajax && typeof dtOrders.ajax.reload === 'function' && dtOrders.ajax.url()) {
                dtOrders.ajax.reload(null, false);
                return;
            }
        }
        drillDownToLostSalesBrand(currentLostSalesBrand);
    }
};

function initBrandwiseLostSalesTable() {
    window.initBrandwiseLostSalesTable();
}
</script>

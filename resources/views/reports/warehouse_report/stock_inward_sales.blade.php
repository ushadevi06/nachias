<div class="mb-3 align-items-center" id="stockInwardSalesBreadcrumb" style="display: none !important;">
    <button class="btn btn-sm btn-outline-secondary me-2 rounded-pill" onclick="renderStockInwardSalesBrandLevel()">
        <i class="ri ri-arrow-left-line"></i> Back to Brands
    </button>
    <span class="text-muted fw-bold" id="stockInwardSalesBreadcrumbText">All Brands</span>
</div>

<div class="card-datatable table-responsive">
    <table class="datatables-products table table-hover" id="stockInwardSalesTable">
        <thead class="table-light" id="stockInwardSalesThead">
            <tr>
                <th>Brand</th>
                <th class="text-center">Op.Stock</th>
                <th class="text-center">Inward</th>
                <th class="text-center">Sales</th>
                <th class="text-center">Closing</th>
                <th class="text-center">Sales Return</th>
                <th class="text-center">Net Closing</th>
            </tr>
        </thead>
        <tbody id="stockInwardSalesTbody">
        </tbody>
        <tfoot class="table-light fw-bold" id="stockInwardSalesTfoot">
            <tr>
                <th>Total</th>
                <th class="text-center" id="total_op_stock">0</th>
                <th class="text-center" id="total_inward">0</th>
                <th class="text-center" id="total_sales">0</th>
                <th class="text-center" id="total_closing">0</th>
                <th class="text-center" id="total_sales_return">0</th>
                <th class="text-center" id="total_net_closing">0</th>
            </tr>
        </tfoot>
    </table>
</div>

<style>
    #stockInwardSalesTable tbody tr:hover td {
        background-color: rgba(105, 108, 255, 0.08) !important;
        transition: all 0.2s ease-in-out;
    }
</style>

<script>
var isStockInwardSalesRoot = true;

function renderStockInwardSalesBrandLevel() {
    isStockInwardSalesRoot = true;
    $('#stockInwardSalesBreadcrumb').attr('style', 'display: none !important;');
    var brandFootHtml = `<tr class="fw-bold bg-light">
        <th class="fw-bold">Total</th>
        <th class="text-center fw-bold text-primary" id="total_op_stock">0</th>
        <th class="text-center fw-bold text-primary" id="total_inward">0</th>
        <th class="text-center fw-bold text-primary" id="total_sales">0</th>
        <th class="text-center fw-bold text-primary" id="total_closing">0</th>
        <th class="text-center fw-bold text-primary" id="total_sales_return">0</th>
        <th class="text-center fw-bold text-primary" id="total_net_closing">0</th>
    </tr>`;
    $('#stockInwardSalesTfoot').html(brandFootHtml).show();

    var headerHtml = `<tr>
        <th>Brand</th>
        <th class="text-center">Op.Stock</th>
        <th class="text-center">Inward</th>
        <th class="text-center">Sales</th>
        <th class="text-center">Closing</th>
        <th class="text-center">Sales Return</th>
        <th class="text-center">Net Closing</th>
    </tr>`;
    $('#stockInwardSalesThead').html(headerHtml);

    if ($.fn.DataTable.isDataTable('#stockInwardSalesTable')) {
        $('#stockInwardSalesTable').DataTable().destroy();
    }
    $('#stockInwardSalesTbody').empty();

    $('#stockInwardSalesTable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax: {
            url: "{{ url('warehouse_reports/ajax/stock-inward-sales') }}",
            data: function (d) {
                d.from_date = $('.start_date').val();
                d.to_date = $('.end_date').val();
                d.brand_id = $('select[name="brand_id"]').val();
                d.store_id = $('select[name="store_id"]').val();
            }
        },
        columns: [
            { data: 'brand', name: 'brand', width: '22%' },
            { data: 'op_stock', name: 'op_stock', className: 'text-center', width: '13%' },
            { data: 'inward', name: 'inward', className: 'text-center', width: '13%' },
            { data: 'sales', name: 'sales', className: 'text-center', width: '13%' },
            { data: 'closing', name: 'closing', className: 'text-center', width: '13%' },
            { data: 'sales_return', name: 'sales_return', className: 'text-center', width: '13%' },
            { data: 'net_closing', name: 'net_closing', className: 'text-center', width: '13%' }
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10,
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
            var json = api.ajax.json();
            if (json && json.totals) {
                $('#total_op_stock').html(json.totals.op_stock);
                $('#total_inward').html(json.totals.inward);
                $('#total_sales').html(json.totals.sales);
                $('#total_closing').html(json.totals.closing);
                $('#total_sales_return').html(json.totals.sales_return);
                $('#total_net_closing').html(json.totals.net_closing);
            }
        }
    });
}

function drillDownToStockInwardSalesDetails(brandId, brandName) {
    isStockInwardSalesRoot = false;

    $('#stockInwardSalesBreadcrumb').attr('style', 'display: flex !important;');
    $('#stockInwardSalesBreadcrumbText').html(`All Brands &nbsp; <i class="ri-arrow-right-s-line"></i> &nbsp; <span class="text-primary">${brandName}</span>`);
    
    var subFootHtml = `<tr class="fw-bold bg-light">
        <th colspan="4" class="text-end fw-bold">TOTAL</th>
        <th class="text-center fw-bold text-primary" id="sub_total_op_stock">0</th>
        <th class="text-center fw-bold text-primary" id="sub_total_inward">0</th>
        <th class="text-center fw-bold text-primary" id="sub_total_sales">0</th>
        <th class="text-center fw-bold text-primary" id="sub_total_closing">0</th>
        <th class="text-center fw-bold text-primary" id="sub_total_sales_return">0</th>
        <th class="text-center fw-bold text-primary" id="sub_total_net_closing">0</th>
    </tr>`;
    $('#stockInwardSalesTfoot').html(subFootHtml).show();

    if ($.fn.DataTable.isDataTable('#stockInwardSalesTable')) {
        $('#stockInwardSalesTable').DataTable().destroy();
    }
    $('#stockInwardSalesTbody').empty();

    var subHeaderHtml = `<tr>
        <th>Art No</th>
        <th>Item Name</th>
        <th class="text-center">Sleeve</th>
        <th class="text-center">Size</th>
        <th class="text-center">Op.Stock</th>
        <th class="text-center">Inward</th>
        <th class="text-center">Sales</th>
        <th class="text-center">Closing</th>
        <th class="text-center">Sales Return</th>
        <th class="text-center">Net Closing</th>
    </tr>`;
    $('#stockInwardSalesThead').html(subHeaderHtml);

    $('#stockInwardSalesTable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax: {
            url: "{{ url('warehouse_reports/stock_inward_sales_details') }}",
            type: "GET",
            data: function (d) {
                d.brand_id = brandId;
                d.brand_name = brandName;
                d.from_date = $('.start_date').val();
                d.to_date = $('.end_date').val();
                d.store_id = $('select[name="store_id"]').val();
            }
        },
        columns: [
            { data: 'art_no', name: 'art_no', width: '15%' },
            { data: 'item_name', name: 'item_name', width: '20%' },
            { data: 'sleeve_type', name: 'sleeve_type', className: 'text-center', width: '10%' },
            { data: 'size', name: 'size', className: 'text-center', width: '8%' },
            { data: 'op_stock', name: 'op_stock', className: 'text-center', width: '9%' },
            { data: 'inward', name: 'inward', className: 'text-center', width: '9%' },
            { data: 'sales', name: 'sales', className: 'text-center', width: '9%' },
            { data: 'closing', name: 'closing', className: 'text-center', width: '9%' },
            { data: 'sales_return', name: 'sales_return', className: 'text-center', width: '9%' },
            { data: 'net_closing', name: 'net_closing', className: 'text-center', width: '12%' }
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10,
        drawCallback: function (settings) {
            var api = this.api();
            var intVal = function (i) {
                return typeof i === 'string' ? i.replace(/[\₹,]/g, '').trim() * 1 : typeof i === 'number' ? i : 0;
            };
            var fmt = function (num) { return parseFloat(num || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 }); };
            $('#sub_total_op_stock').html(fmt(api.column(4, { page: 'current' }).data().reduce(function(a,b){ return intVal(a)+intVal(b); }, 0)));
            $('#sub_total_inward').html(fmt(api.column(5, { page: 'current' }).data().reduce(function(a,b){ return intVal(a)+intVal(b); }, 0)));
            $('#sub_total_sales').html(fmt(api.column(6, { page: 'current' }).data().reduce(function(a,b){ return intVal(a)+intVal(b); }, 0)));
            $('#sub_total_closing').html(fmt(api.column(7, { page: 'current' }).data().reduce(function(a,b){ return intVal(a)+intVal(b); }, 0)));
            $('#sub_total_sales_return').html(fmt(api.column(8, { page: 'current' }).data().reduce(function(a,b){ return intVal(a)+intVal(b); }, 0)));
            $('#sub_total_net_closing').html(fmt(api.column(9, { page: 'current' }).data().reduce(function(a,b){ return intVal(a)+intVal(b); }, 0)));
        }
    });
}

window.initStockInwardSalesTable = function() {
    renderStockInwardSalesBrandLevel();
};
function initStockInwardSalesTable() {
    window.initStockInwardSalesTable();
}

if ($('#stock-inward-sales').hasClass('active')) {
    window.initStockInwardSalesTable();
}
</script>

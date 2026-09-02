<div class="mb-3 d-flex align-items-center" id="brandwiseBreadcrumbs" style="display: none !important;">
    <button class="btn btn-sm btn-outline-secondary me-2" onclick="renderBrandLevel()" id="btnBackToBrands">
        <i class="ri ri-arrow-left-line"></i> Back to Brands
    </button>
    <span class="text-muted fw-bold" id="breadcrumbText">All Brands</span>
</div>

<div class="card-datatable table-responsive">
    <table id="brandwiseStockTable" class="datatables-products table table-hover">
        <thead class="table-light">
            <tr id="brandwiseStockTheadTr">
                <th class="fw-bold" id="brandwiseStockCol1">BRAND</th>
                <th class="text-center fw-bold">STOCK</th>
                <th class="text-end fw-bold">STOCK VALUE</th>
            </tr>
        </thead>
        <tbody id="brandwiseStockTbody">
        </tbody>
        <tfoot class="table-light border-top" id="brandwiseStockTfoot">
            <tr id="brandwiseStockTfootTr" class="bg-light fw-bold">
                <th class="fw-bold text-start">TOTAL</th>
                <th class="text-center fw-bold text-primary" id="brandwiseStockFootQty">-</th>
                <th class="text-end fw-bold text-primary" id="brandwiseStockFootValue">-</th>
            </tr>
        </tfoot>
    </table>
</div>

<style>
    /* Styling for hover effect to indicate it's clickable */
    #brandwiseStockTable tbody tr:hover td {
        background-color: rgba(105, 108, 255, 0.08) !important;
        transition: all 0.2s ease-in-out;
    }
    #brandwiseStockTfoot th {
        font-size: 0.95rem;
        padding-top: 12px;
        padding-bottom: 12px;
    }
</style>

<script>
    var currentBrandId = null;
    var currentBrandName = '';
    var dtInstance = null;
    var isRootLevel = true;

    function formatNumber(num) {
        return parseFloat(num || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 });
    }

    function formatCurrency(num) {
        return '₹' + parseFloat(num || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function reinitDataTable() {
        if ($.fn.DataTable.isDataTable('#brandwiseStockTable')) {
            $('#brandwiseStockTable').DataTable().clear().destroy();
        }
        $('#brandwiseStockTbody').empty();
    }
    
    function applyServerSideDataTable() {
        isRootLevel = true;
        $('#brandwiseStockTheadTr').html(`
            <th class="fw-bold" id="brandwiseStockCol1">BRAND</th>
            <th class="text-center fw-bold">STOCK</th>
            <th class="text-end fw-bold">STOCK VALUE</th>
        `);
        $('#brandwiseStockTfootTr').html(`
            <th class="fw-bold text-start">TOTAL</th>
            <th class="text-center fw-bold text-primary" id="brandwiseStockFootQty">-</th>
            <th class="text-end fw-bold text-primary" id="brandwiseStockFootValue">-</th>
        `);

        dtInstance = $('#brandwiseStockTable').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: "{{ url('warehouse_reports/ajax/brandwise-stock') }}",
                data: function (d) {
                    d.from_date = $('.start_date').val();
                    d.to_date = $('.end_date').val();
                    d.brand_id = $('select[name="brand_id"]').val();
                    d.store_id = $('select[name="store_id"]').val();
                }
            },
            columns: [
                { data: 'brand', name: 'brand', width: '55%' },
                { data: 'total_qty', name: 'total_qty', className: 'text-center', width: '20%' },
                { data: 'stock_value', name: 'stock_value', className: 'text-end fw-bold', width: '25%' }
            ],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10,
            language: {
                emptyTable: "No data found matching your filters",
                zeroRecords: "No matching records found",
                infoEmpty: "Showing 0 to 0 entries",
            },
            drawCallback: function (settings) {
                var json = settings.json;
                if (json && json.totals) {
                    $('#brandwiseStockFootQty').html(json.totals.total_qty);
                    $('#brandwiseStockFootValue').html(json.totals.stock_value);
                } else {
                    var api = this.api();
                    var intVal = function (i) {
                        return typeof i === 'string' ? i.replace(/[\₹,]/g, '').trim() * 1 : typeof i === 'number' ? i : 0;
                    };
                    var pQty = api.column(1, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    var pVal = api.column(2, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    $('#brandwiseStockFootQty').html(formatNumber(pQty));
                    $('#brandwiseStockFootValue').html(formatCurrency(pVal));
                }
            }
        });
    }

    function renderBrandLevel() {
        $('#brandwiseBreadcrumbs').attr('style', 'display: none !important;');
        
        reinitDataTable();
        applyServerSideDataTable();
    }
    
    window.initBrandwiseStockTable = function() {
        renderBrandLevel();
    };
    function initBrandwiseStockTable() {
        window.initBrandwiseStockTable();
    }

    function drillDownToStyle(brandId, brandName) {
        isRootLevel = false;
        currentBrandId = brandId;
        currentBrandName = brandName;

        $('#brandwiseBreadcrumbs').attr('style', 'display: flex !important;');
        $('#breadcrumbText').html(`All Brands &nbsp; <i class="ri-arrow-right-s-line"></i> &nbsp; <span class="text-primary">${brandName}</span>`);
        $('#btnBackToBrands').attr('onclick', 'renderBrandLevel()');

        reinitDataTable();

        $('#brandwiseStockTheadTr').html(`
            <th class="fw-bold" id="brandwiseStockCol1">STYLE</th>
            <th class="text-center fw-bold">STOCK</th>
            <th class="text-end fw-bold">STOCK VALUE</th>
        `);
        $('#brandwiseStockTfootTr').html(`
            <th class="fw-bold text-start">TOTAL</th>
            <th class="text-center fw-bold text-primary" id="brandwiseStockFootQty">-</th>
            <th class="text-end fw-bold text-primary" id="brandwiseStockFootValue">-</th>
        `);

        dtInstance = $('#brandwiseStockTable').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: "{{ url('warehouse_reports/brandwise_styles') }}",
                data: function (d) {
                    d.brand_id = brandId;
                    d.brand_name = brandName;
                    d.store_id = $('select[name="store_id"]').val();
                    d.from_date = $('.start_date').val();
                    d.to_date = $('.end_date').val();
                }
            },
            columns: [
                { data: 'brand', name: 'styles.style_name', width: '55%' },
                { data: 'total_qty', name: 'total_qty', className: 'text-center', width: '20%' },
                { data: 'stock_value', name: 'stock_value', className: 'text-end fw-bold', width: '25%' }
            ],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10,
            language: {
                emptyTable: "No styles found matching your filters",
                zeroRecords: "No matching records found",
                infoEmpty: "Showing 0 to 0 entries",
            },
            drawCallback: function (settings) {
                var json = settings.json;
                if (json && json.totals) {
                    $('#brandwiseStockFootQty').html(json.totals.total_qty);
                    $('#brandwiseStockFootValue').html(json.totals.stock_value);
                } else {
                    var api = this.api();
                    var intVal = function (i) {
                        return typeof i === 'string' ? i.replace(/[\₹,]/g, '').trim() * 1 : typeof i === 'number' ? i : 0;
                    };
                    var pQty = api.column(1, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    var pVal = api.column(2, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    $('#brandwiseStockFootQty').html(formatNumber(pQty));
                    $('#brandwiseStockFootValue').html(formatCurrency(pVal));
                }
            }
        });
    }

    function drillDownToArtNo(brandId, brandName, styleId, styleName) {
        $('#breadcrumbText').html(`All Brands &nbsp; <i class="ri-arrow-right-s-line"></i> &nbsp; <span class="text-secondary cursor-pointer" onclick="drillDownToStyle(${brandId}, '${brandName.replace(/'/g, "\\'")}')">${brandName}</span> &nbsp; <i class="ri-arrow-right-s-line"></i> &nbsp; <span class="text-primary">${styleName}</span>`);
        $('#btnBackToBrands').attr('onclick', `drillDownToStyle(${brandId}, '${brandName.replace(/'/g, "\\'")}')`);

        reinitDataTable();

        $('#brandwiseStockTheadTr').html(`
            <th class="fw-bold" id="brandwiseStockCol1">ART NO</th>
            <th class="text-center fw-bold">SLEEVE</th>
            <th class="text-center fw-bold">SIZE</th>
            <th class="text-center fw-bold">STOCK</th>
            <th class="text-center fw-bold">LOW STOCK QTY</th>
            <th class="text-center fw-bold">EXCESS STOCK QTY</th>
            <th class="text-center fw-bold">STOCK DAYS</th>
            <th class="text-end fw-bold">STOCK VALUE</th>
        `);
        $('#brandwiseStockTfootTr').html(`
            <th class="fw-bold text-start">TOTAL</th>
            <th class="text-center fw-bold">-</th>
            <th class="text-center fw-bold">-</th>
            <th class="text-center fw-bold text-primary" id="brandwiseStockFootQty">-</th>
            <th class="text-center fw-bold text-danger" id="brandwiseStockFootLowStock">-</th>
            <th class="text-center fw-bold text-success" id="brandwiseStockFootExcessStock">-</th>
            <th class="text-center fw-bold">-</th>
            <th class="text-end fw-bold text-primary" id="brandwiseStockFootValue">-</th>
        `);

        dtInstance = $('#brandwiseStockTable').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: "{{ url('warehouse_reports/brandwise_art_nos') }}",
                data: function (d) {
                    d.brand_id = brandId;
                    d.style_id = styleId;
                    d.store_id = $('select[name="store_id"]').val();
                    d.from_date = $('.start_date').val();
                    d.to_date = $('.end_date').val();
                }
            },
            columns: [
                { data: 'brand', name: 'stock_entry_items.art_no', width: '22%' },
                { data: 'sleeve', name: 'stock_entry_items.sleeve_type', className: 'text-center', width: '10%' },
                { data: 'size', name: 'stock_entry_items.size', className: 'text-center', width: '10%' },
                { data: 'total_qty', name: 'total_qty', className: 'text-center', width: '11%' },
                { data: 'low_stock', name: 'low_stock', className: 'text-center', orderable: false, width: '12%' },
                { data: 'excess_stock', name: 'excess_stock', className: 'text-center', orderable: false, width: '12%' },
                { data: 'stock_days', name: 'stock_days', className: 'text-center', orderable: false, width: '9%' },
                { data: 'stock_value', name: 'stock_value', className: 'text-end fw-bold', width: '14%' }
            ],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10,
            language: {
                emptyTable: "No Art Nos found matching your filters",
                zeroRecords: "No matching records found",
                infoEmpty: "Showing 0 to 0 entries",
            },
            drawCallback: function (settings) {
                var json = settings.json;
                if (json && json.totals) {
                    $('#brandwiseStockFootQty').html(json.totals.total_qty);
                    $('#brandwiseStockFootLowStock').html(json.totals.low_stock != '0' ? json.totals.low_stock : '-');
                    $('#brandwiseStockFootExcessStock').html(json.totals.excess_stock != '0' ? json.totals.excess_stock : '-');
                    $('#brandwiseStockFootValue').html(json.totals.stock_value);
                } else {
                    var api = this.api();
                    var intVal = function (i) {
                        return typeof i === 'string' ? i.replace(/[\₹,]/g, '').trim() * 1 : typeof i === 'number' ? i : 0;
                    };
                    var pQty = api.column(3, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    var pLow = api.column(4, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    var pExcess = api.column(5, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    var pVal = api.column(7, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    $('#brandwiseStockFootQty').html(formatNumber(pQty));
                    $('#brandwiseStockFootLowStock').html(pLow > 0 ? formatNumber(pLow) : '-');
                    $('#brandwiseStockFootExcessStock').html(pExcess > 0 ? formatNumber(pExcess) : '-');
                    $('#brandwiseStockFootValue').html(formatCurrency(pVal));
                }
            }
        });
    }

    if ($('#brand-stock').hasClass('active') || $('#brandwise-stock').hasClass('active')) {
        initBrandwiseStockTable();
    }
</script>

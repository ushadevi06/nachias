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
                <th class="text-center fw-bold">AVG STOCK DAYS</th>
                <th class="text-end fw-bold">STOCK VALUE</th>
            </tr>
        </thead>
        <tbody id="brandwiseStockTbody">
        </tbody>
        <tfoot class="table-light border-top" id="brandwiseStockTfoot">
            <tr id="brandwiseStockTfootTr" class="bg-light fw-bold">
                <th class="fw-bold text-start">TOTAL</th>
                <th class="text-center fw-bold text-primary" id="brandwiseStockFootQty">-</th>
                <th class="text-center fw-bold text-muted">-</th>
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
    .cursor-pointer {
        cursor: pointer;
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
            <th class="text-center fw-bold">AVG STOCK DAYS</th>
            <th class="text-end fw-bold">STOCK VALUE</th>
        `);
        $('#brandwiseStockTfootTr').html(`
            <th class="fw-bold text-start">TOTAL</th>
            <th class="text-center fw-bold text-primary" id="brandwiseStockFootQty">-</th>
            <th class="text-center fw-bold text-muted">-</th>
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
                    d.warehouse_id = $('#filter_warehouse_id').val() || $('#top_warehouse_select').val();
                    d.aging_filter = $('#aging_filter').val();
                }
            },
            columns: [
                { data: 'brand', name: 'brands.brand_name', width: '45%' },
                { data: 'total_qty', name: 'total_qty', className: 'text-center', width: '18%' },
                { data: 'stock_days', name: 'stock_days', className: 'text-center', orderable: false, width: '17%' },
                { data: 'stock_value', name: 'stock_value', className: 'text-end fw-bold', width: '20%' }
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
                    var pVal = api.column(3, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
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
        $('#breadcrumbText').html(`All Brands &nbsp; <i class="ri ri-arrow-right-s-line"></i> &nbsp; <span class="text-primary">${brandName}</span>`);
        $('#btnBackToBrands').attr('onclick', 'renderBrandLevel()');

        reinitDataTable();

        $('#brandwiseStockTheadTr').html(`
            <th class="fw-bold" id="brandwiseStockCol1">STYLE</th>
            <th class="text-center fw-bold">STOCK</th>
            <th class="text-center fw-bold">AVG STOCK DAYS</th>
            <th class="text-end fw-bold">STOCK VALUE</th>
        `);
        $('#brandwiseStockTfootTr').html(`
            <th class="fw-bold text-start">TOTAL</th>
            <th class="text-center fw-bold text-primary" id="brandwiseStockStyleFootQty">-</th>
            <th class="text-center fw-bold text-muted">-</th>
            <th class="text-end fw-bold text-primary" id="brandwiseStockStyleFootValue">-</th>
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
                    d.warehouse_id = $('#filter_warehouse_id').val() || $('#top_warehouse_select').val();
                    d.aging_filter = $('#aging_filter').val();
                    d.from_date = $('.start_date').val();
                    d.to_date = $('.end_date').val();
                }
            },
            columns: [
                { data: 'brand', name: 'styles.style_name', width: '45%' },
                { data: 'total_qty', name: 'total_qty', className: 'text-center', width: '18%' },
                { data: 'stock_days', name: 'stock_days', className: 'text-center', orderable: false, width: '17%' },
                { data: 'stock_value', name: 'stock_value', className: 'text-end fw-bold', width: '20%' }
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
                    $('#brandwiseStockStyleFootQty').html(json.totals.total_qty);
                    $('#brandwiseStockStyleFootValue').html(json.totals.stock_value);
                } else {
                    var api = this.api();
                    var intVal = function (i) {
                        return typeof i === 'string' ? i.replace(/[\₹,]/g, '').trim() * 1 : typeof i === 'number' ? i : 0;
                    };
                    var pQty = api.column(1, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    var pVal = api.column(3, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    $('#brandwiseStockStyleFootQty').html(formatNumber(pQty));
                    $('#brandwiseStockStyleFootValue').html(formatCurrency(pVal));
                }
            }
        });
    }

    function drillDownToArtNo(brandId, brandName, styleId, styleName) {
        $('#breadcrumbText').html(`All Brands &nbsp; <i class="ri ri-arrow-right-s-line"></i> &nbsp; <span class="text-secondary cursor-pointer" onclick="drillDownToStyle(${brandId}, '${brandName.replace(/'/g, "\\'")}')">${brandName}</span> &nbsp; <i class="ri ri-arrow-right-s-line"></i> &nbsp; <span class="text-primary">${styleName}</span>`);
        $('#btnBackToBrands').attr('onclick', `drillDownToStyle(${brandId}, '${brandName.replace(/'/g, "\\'")}')`);

        reinitDataTable();

        $('#brandwiseStockTheadTr').html(`
            <th class="fw-bold" id="brandwiseStockCol1">ART NO</th>
            <th class="text-center fw-bold">SLEEVE</th>
            <th class="text-center fw-bold">36</th>
            <th class="text-center fw-bold">38</th>
            <th class="text-center fw-bold">40</th>
            <th class="text-center fw-bold">42</th>
            <th class="text-center fw-bold">44</th>
            <th class="text-center fw-bold">46</th>
            <th class="text-center fw-bold">48</th>
            <th class="text-center fw-bold">50</th>
            <th class="text-center fw-bold">TOTAL STOCK</th>
            <th class="text-center fw-bold">STOCK DAYS</th>
            <th class="text-end fw-bold">STOCK VALUE</th>
        `);
        $('#brandwiseStockTfootTr').html(`
            <th class="fw-bold text-start">TOTAL</th>
            <th class="text-center fw-bold">-</th>
            <th class="text-center fw-bold text-dark" id="brandwiseStockArtFoot36">-</th>
            <th class="text-center fw-bold text-dark" id="brandwiseStockArtFoot38">-</th>
            <th class="text-center fw-bold text-dark" id="brandwiseStockArtFoot40">-</th>
            <th class="text-center fw-bold text-dark" id="brandwiseStockArtFoot42">-</th>
            <th class="text-center fw-bold text-dark" id="brandwiseStockArtFoot44">-</th>
            <th class="text-center fw-bold text-dark" id="brandwiseStockArtFoot46">-</th>
            <th class="text-center fw-bold text-dark" id="brandwiseStockArtFoot48">-</th>
            <th class="text-center fw-bold text-dark" id="brandwiseStockArtFoot50">-</th>
            <th class="text-center fw-bold text-primary" id="brandwiseStockArtFootQty">-</th>
            <th class="text-center fw-bold text-muted">-</th>
            <th class="text-end fw-bold text-primary" id="brandwiseStockArtFootValue">-</th>
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
                    d.warehouse_id = $('#filter_warehouse_id').val() || $('#top_warehouse_select').val();
                    d.aging_filter = $('#aging_filter').val();
                    d.from_date = $('.start_date').val();
                    d.to_date = $('.end_date').val();
                }
            },
            columns: [
                { data: 'art_no', name: 'stock_entry_items.art_no', width: '18%' },
                { data: 'sleeve', name: 'sleeve_type', className: 'text-center', width: '8%' },
                { data: 'size_36', name: 'q36', className: 'text-center', width: '5%' },
                { data: 'size_38', name: 'q38', className: 'text-center', width: '5%' },
                { data: 'size_40', name: 'q40', className: 'text-center', width: '5%' },
                { data: 'size_42', name: 'q42', className: 'text-center', width: '5%' },
                { data: 'size_44', name: 'q44', className: 'text-center', width: '5%' },
                { data: 'size_46', name: 'q46', className: 'text-center', width: '5%' },
                { data: 'size_48', name: 'q48', className: 'text-center', width: '5%' },
                { data: 'size_50', name: 'q50', className: 'text-center', width: '5%' },
                { data: 'total_qty', name: 'total_qty', className: 'text-center', width: '10%' },
                { data: 'stock_days', name: 'stock_days', className: 'text-center', orderable: false, width: '11%' },
                { data: 'stock_value', name: 'stock_value', className: 'text-end fw-bold', width: '18%' }
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
                    $('#brandwiseStockArtFoot36').html(json.totals.sum_36 || '-');
                    $('#brandwiseStockArtFoot38').html(json.totals.sum_38 || '-');
                    $('#brandwiseStockArtFoot40').html(json.totals.sum_40 || '-');
                    $('#brandwiseStockArtFoot42').html(json.totals.sum_42 || '-');
                    $('#brandwiseStockArtFoot44').html(json.totals.sum_44 || '-');
                    $('#brandwiseStockArtFoot46').html(json.totals.sum_46 || '-');
                    $('#brandwiseStockArtFoot48').html(json.totals.sum_48 || '-');
                    $('#brandwiseStockArtFoot50').html(json.totals.sum_50 || '-');
                    $('#brandwiseStockArtFootQty').html(json.totals.total_qty || '0');
                    $('#brandwiseStockArtFootValue').html(json.totals.stock_value || '₹0.00');
                } else {
                    var api = this.api();
                    var intVal = function (i) {
                        return typeof i === 'string' ? i.replace(/[\₹,]/g, '').trim() * 1 : typeof i === 'number' ? i : 0;
                    };
                    var pQty = api.column(10, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    var pVal = api.column(12, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    $('#brandwiseStockArtFootQty').html(formatNumber(pQty));
                    $('#brandwiseStockArtFootValue').html(formatCurrency(pVal));
                }
            }
        });
    }

    if ($('#brand-stock').hasClass('active') || $('#brandwise-stock').hasClass('active')) {
        initBrandwiseStockTable();
    }
</script>
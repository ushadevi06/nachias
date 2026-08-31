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
    </table>
</div>

<style>
    /* Styling for hover effect to indicate it's clickable */
    #brandwiseStockTable tbody tr:hover td {
        background-color: rgba(105, 108, 255, 0.08) !important;
        transition: all 0.2s ease-in-out;
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
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10,
            language: {
                emptyTable: "No data found matching your filters",
                zeroRecords: "No matching records found",
                infoEmpty: "Showing 0 to 0 entries",
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
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10,
            language: {
                emptyTable: "No styles found matching your filters",
                zeroRecords: "No matching records found",
                infoEmpty: "Showing 0 to 0 entries",
            }
        });
    }

    function drillDownToArtNo(brandId, brandName, styleId, styleName) {
        $('#breadcrumbText').html(`All Brands &nbsp; <i class="ri-arrow-right-s-line"></i> &nbsp; <span class="text-secondary cursor-pointer" onclick="drillDownToStyle(${brandId}, '${brandName.replace(/'/g, "\\'")}')">${brandName}</span> &nbsp; <i class="ri-arrow-right-s-line"></i> &nbsp; <span class="text-primary">${styleName}</span>`);
        $('#btnBackToBrands').attr('onclick', `drillDownToStyle(${brandId}, '${brandName.replace(/'/g, "\\'")}')`);

        reinitDataTable();

        $('#brandwiseStockTheadTr').html(`
            <th class="fw-bold" id="brandwiseStockCol1">ART NO</th>
            <th class="text-center fw-bold">STOCK</th>
            <th class="text-center fw-bold">LOW STOCK QTY</th>
            <th class="text-center fw-bold">EXCESS STOCK QTY</th>
            <th class="text-center fw-bold">STOCK DAYS</th>
            <th class="text-end fw-bold">STOCK VALUE</th>
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
                { data: 'brand', name: 'stock_entry_items.art_no', width: '30%' },
                { data: 'total_qty', name: 'total_qty', className: 'text-center', width: '15%' },
                { data: 'low_stock', name: 'low_stock', className: 'text-center', orderable: false, width: '15%' },
                { data: 'excess_stock', name: 'excess_stock', className: 'text-center', orderable: false, width: '15%' },
                { data: 'stock_days', name: 'stock_days', className: 'text-center', orderable: false, width: '10%' },
                { data: 'stock_value', name: 'stock_value', className: 'text-end fw-bold', width: '15%' }
            ],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10,
            language: {
                emptyTable: "No Art Nos found matching your filters",
                zeroRecords: "No matching records found",
                infoEmpty: "Showing 0 to 0 entries",
            }
        });
    }

    if ($('#brand-stock').hasClass('active') || $('#brandwise-stock').hasClass('active')) {
        initBrandwiseStockTable();
    }
</script>

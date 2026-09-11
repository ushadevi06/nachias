<!-- Breadcrumb Bar for Drilldown -->
<div class="mb-3 d-flex align-items-center" id="stockDrilldownBreadcrumbs" style="display: none !important;">
    <button class="btn btn-sm btn-outline-secondary me-2" onclick="renderStockLevel1()" id="btnBackToStockReport">
        <i class="ri ri-arrow-left-line me-1"></i> Back
    </button>
    <span class="text-dark fw-bold" id="stockBreadcrumbText">Stock Report</span>
</div>

<!-- Level 1: Stock Summary Table -->
<div id="stockLevel1Container" class="table-responsive">
    <table class="table premium-table mb-0 datatables-stock">
        @if($isFabric)
        <thead>
            <tr>
                <th>#</th>
                <th class="text-start ps-3">PRODUCT GROUP</th>
                <th>WIDTH</th>
                <th>OPENING METERS</th>
                <th>INWARD METERS</th>
                <th>OUTWARD METERS</th>
                <th>CLOSING METERS</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="fw-bold" style="background: #f1f5f9;">
                <td colspan="3" class="text-end">TOTAL</td>
                <td id="footer-stock-opening">0.00</td>
                <td id="footer-stock-inward" class="text-success">0.00</td>
                <td id="footer-stock-outward" class="text-danger">0.00</td>
                <td id="footer-stock-closing">0.00</td>
            </tr>
        </tfoot>
        @else
        <thead>
            <tr>
                <th>#</th>
                <th>ITEM NAME</th>
                <th>OPENING QTY</th>
                <th>INWARD QTY</th>
                <th>OUTWARD QTY</th>
                <th>CLOSING QTY</th>
                <th>CLOSING COST (₹)</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="fw-bold" style="background: #f1f5f9;">
                <td colspan="2" class="text-end">TOTAL</td>
                <td id="footer-acc-opening">0.00</td>
                <td id="footer-acc-inward" class="text-success">0.00</td>
                <td id="footer-acc-outward" class="text-danger">0.00</td>
                <td id="footer-acc-closing">0.00</td>
                <td id="footer-acc-cost" class="text-primary">₹ 0.00</td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

<!-- Level 2: Style-wise Stock Report Table (Clean UI matching Image 2) -->
<div id="stockLevel2Container" style="display: none;">
    <div class="card shadow-sm border mb-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="stockStylesTable" class="table table-hover mb-0" style="font-size: 0.88rem; width: 100%;">
                    <thead class="table-light">
                        <tr style="background-color: #f1f5f9;">
                            <th class="fw-bold ps-4" style="width: 70%;">STYLE</th>
                            <th class="text-center fw-bold pe-4" style="width: 30%;">STOCK</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot class="table-light border-top">
                        <tr class="bg-light fw-bold" style="background-color: #f8fafc;">
                            <td class="fw-bold ps-4 text-start">TOTAL</td>
                            <td class="text-center fw-bold text-primary pe-4" id="footer-styles-qty">-</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Level 3: Item Stock Transactions Drilldown Table -->
<div id="stockLevel3Container" style="display: none;">
    <div class="card shadow-sm border mb-3">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="stockDrilldownTable" class="table premium-table table-hover mb-0" style="font-size: 0.82rem; width: 100%;">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 45px;">#</th>
                            <th style="width: 70px;">WIDTH</th>
                            <th style="width: 100px;">LOCATION</th>
                            <th>ART</th>
                            <th style="width: 70px;">UOM</th>
                            <th style="width: 110px;">INWARD DATE</th>
                            <th>DOC TYPE</th>
                            <th>DOC NO</th>
                            <th class="text-end">OPENING</th>
                            <th class="text-end">OPENING VALUE</th>
                            <th class="text-end">INWARD</th>
                            <th class="text-end">INWARD VALUE</th>
                            <th>OUTWARD DOC TYPE</th>
                            <th>OUTWARD DOC NO</th>
                            <th style="width: 110px;">OUTWARD DATE</th>
                            <th class="text-end">OUTWARD</th>
                            <th class="text-end">OUTWARD VALUE</th>
                            <th class="text-end">CLOSING BAL</th>
                            <th class="text-end">CLOSING VALUE</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr class="fw-bold" style="background: #f1f5f9;">
                            <td colspan="8" class="text-end">TOTAL</td>
                            <td id="footer-drill-opening-qty" class="text-end">0.00</td>
                            <td id="footer-drill-opening-val" class="text-end">₹ 0.00</td>
                            <td id="footer-drill-inward-qty" class="text-end text-success">0.00</td>
                            <td id="footer-drill-inward-val" class="text-end text-success">₹ 0.00</td>
                            <td colspan="3"></td>
                            <td id="footer-drill-outward-qty" class="text-end text-danger">0.00</td>
                            <td id="footer-drill-outward-val" class="text-end text-danger">₹ 0.00</td>
                            <td id="footer-drill-closing-qty" class="text-end fw-bold">0.00</td>
                            <td id="footer-drill-closing-val" class="text-end fw-bold text-primary">₹ 0.00</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Level 4: Accessories Store Stock Details Drilldown Table (Matching Images 2 & 3 Excel Layout) -->
<div id="accDrilldownContainer" style="display: none;">
    <div class="card shadow-sm border mb-3">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="accDrilldownTable" class="table premium-table table-hover mb-0" style="font-size: 0.80rem; width: 100%;">
                    <thead class="table-light">
                        <tr style="background-color: #f1f5f9;">
                            <th class="text-center" style="width: 40px;">#</th>
                            <th style="width: 220px;">DESCRIPTION</th>
                            <th class="text-center" style="width: 60px;">UOM</th>
                            <th class="text-end" style="width: 100px;">PER PCS RATE</th>
                            <th class="text-end" style="width: 100px;">OPENING QTY</th>
                            <th class="text-end" style="width: 110px;">OPENING VALUE</th>
                            <th class="text-center" style="width: 110px;">DOC NO</th>
                            <th class="text-center" style="width: 100px;">INWARD DATE</th>
                            <th class="text-end" style="width: 100px;">INWARD QTY</th>
                            <th class="text-end" style="width: 110px;">INWARD VALUE</th>
                            <th class="text-center" style="width: 100px;">DOC TYPE</th>
                            <th class="text-center" style="width: 110px;">DOC NO</th>
                            <th class="text-center" style="width: 100px;">OUTWARD DATE</th>
                            <th class="text-end" style="width: 100px;">OUTWARD QTY</th>
                            <th class="text-end" style="width: 110px;">OUTWARD VALUE</th>
                            <th class="text-end" style="width: 110px;">CLOSING STOCK</th>
                            <th class="text-end" style="width: 120px;">TOTAL VALUE</th>
                            <th class="text-center" style="width: 100px;">DELIVERY DAYS</th>
                            <th class="text-end" style="width: 100px;">MIN STOCK</th>
                            <th class="text-center" style="width: 90px;">DIFF</th>
                            <th class="text-end" style="width: 100px;">REORDER</th>
                            <th style="width: 150px;">SUPPLIER NAME</th>
                            <th style="width: 160px;">ORDER NO & DATE</th>
                            <th class="text-center" style="width: 110px;">DELIVERY DATE</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr class="fw-bold" style="background: #f1f5f9;">
                            <td colspan="4" class="text-end">TOTAL</td>
                            <td id="acc-drill-opening-qty" class="text-end">0.00</td>
                            <td id="acc-drill-opening-val" class="text-end">₹ 0.00</td>
                            <td colspan="2"></td>
                            <td id="acc-drill-inward-qty" class="text-end text-success">0.00</td>
                            <td id="acc-drill-inward-val" class="text-end text-success">₹ 0.00</td>
                            <td colspan="3"></td>
                            <td id="acc-drill-outward-qty" class="text-end text-danger">0.00</td>
                            <td id="acc-drill-outward-val" class="text-end text-danger">₹ 0.00</td>
                            <td id="acc-drill-closing-stock" class="text-end fw-bold">0.00</td>
                            <td id="acc-drill-total-val" class="text-end fw-bold text-primary">₹ 0.00</td>
                            <td colspan="7"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    #stockLevel1Container tbody tr,
    #stockStylesTable tbody tr {
        cursor: pointer;
        transition: background-color 0.15s ease-in-out;
    }
    #stockLevel1Container tbody tr:hover,
    #stockStylesTable tbody tr:hover {
        background-color: rgba(105, 108, 255, 0.08) !important;
    }

    #stockLevel2Container,
    #stockLevel3Container,
    #stockLevel2Container table,
    #stockLevel3Container table,
    #stockLevel2Container .dataTables_wrapper *,
    #stockLevel3Container .dataTables_wrapper * {
        font-family: inherit !important;
    }

    #stockDrilldownTable_wrapper .dataTables_scrollBody {
        border-bottom: 1px solid #e2e8f0;
    }
</style>

<script>
    var currentBrandName = '';
    var currentBrandId = '';
    var currentWidthName = '';
    var currentFabricWidthId = '';

    function renderStockLevel1() {
        $('#stockDrilldownBreadcrumbs').attr('style', 'display: none !important;');
        $('#stockLevel2Container, #stockLevel3Container, #accDrilldownContainer').hide();
        $('#stockLevel1Container').show();

        if ($.fn.DataTable.isDataTable('.datatables-stock')) {
            $('.datatables-stock').DataTable().columns.adjust();
        }
    }

    function renderStockLevel2() {
        $('#stockDrilldownBreadcrumbs').attr('style', 'display: flex !important;');
        $('#btnBackToStockReport').attr('onclick', 'renderStockLevel1()');
        $('#stockBreadcrumbText').html(`All Brands &nbsp; <i class="ri-arrow-right-s-line"></i> &nbsp; <span class="text-primary fw-bold">${currentBrandName}</span>`);
        
        $('#stockLevel1Container, #stockLevel3Container').hide();
        $('#stockLevel2Container').show();

        if ($.fn.DataTable.isDataTable('#stockStylesTable')) {
            $('#stockStylesTable').DataTable().columns.adjust();
        }
    }

    // Level 2: Style Breakdown view matching Image 2
    function drilldownToStockStyles(brandName, width, brandId, fabricWidthId) {
        currentBrandName = brandName || 'All Brands';
        currentBrandId = brandId || '';
        currentWidthName = width || '-';
        currentFabricWidthId = fabricWidthId || '';

        $('#stockDrilldownBreadcrumbs').attr('style', 'display: flex !important;');
        $('#btnBackToStockReport').attr('onclick', 'renderStockLevel1()');
        $('#stockBreadcrumbText').html(`All Brands &nbsp; <i class="ri-arrow-right-s-line"></i> &nbsp; <span class="text-primary fw-bold">${currentBrandName}</span>`);

        $('#stockLevel1Container, #stockLevel3Container').hide();
        $('#stockLevel2Container').show();

        if ($.fn.DataTable.isDataTable('#stockStylesTable')) {
            $('#stockStylesTable').DataTable().clear().destroy();
        }
        $('#stockStylesTable tbody').html('<tr><td colspan="2" class="text-center py-4 text-primary fw-semibold"><span class="spinner-border spinner-border-sm me-2"></span>Loading styles...</td></tr>');

        $.ajax({
            url: window.location.pathname,
            method: 'GET',
            data: {
                report_type: 'stock-report-styles',
                brand_id: currentBrandId,
                brand: currentBrandName,
                fabric_width_id: currentFabricWidthId,
                width: currentWidthName,
                from_date: $('.start_date').val(),
                to_date: $('.end_date').val(),
                supplier_id: $('select[name="supplier_id"]').val()
            },
            dataType: 'json',
            success: function(response) {
                let items = response.data || [];
                if (response.totals) {
                    $('#footer-styles-qty').text(response.totals.total_qty);
                }

                const dtStyles = $('#stockStylesTable').DataTable({
                    data: items,
                    autoWidth: false,
                    columns: [
                        { data: 'style', className: 'fw-bold ps-4 text-dark', width: '70%' },
                        { data: 'total_qty', className: 'text-center fw-semibold pe-4', width: '30%' }
                    ],
                    dom: '<"row p-3 align-items-center"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>t<"row p-3 align-items-center"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6 d-flex justify-content-end"p>>',
                    pageLength: 10,
                    bLengthChange: true,
                    bFilter: true,
                    bInfo: true,
                    language: {
                        emptyTable: 'No style data found for this brand.'
                    }
                });

                $('#stockStylesTable tbody').off('click', 'tr').on('click', 'tr', function(e) {
                    if ($(e.target).is('a, button, input')) return;
                    var tr = $(this);
                    var row = dtStyles.row(tr);
                    if (!row || !row.data()) return;

                    var data = row.data();
                    drilldownToStockItemDetails(data.style_id, data.style);
                });
            }
        });
    }

    // Level 3: Item Transaction Details view
    function drilldownToStockItemDetails(styleId, styleName) {
        styleName = styleName || 'All Styles';

        $('#stockDrilldownBreadcrumbs').attr('style', 'display: flex !important;');
        $('#btnBackToStockReport').attr('onclick', 'renderStockLevel2()');
        $('#stockBreadcrumbText').html(`All Brands &nbsp; <i class="ri-arrow-right-s-line"></i> &nbsp; <span class="text-secondary cursor-pointer" onclick="renderStockLevel2()">${currentBrandName}</span> &nbsp; <i class="ri-arrow-right-s-line"></i> &nbsp; <span class="text-primary fw-bold">${styleName}</span>`);

        $('#stockLevel1Container, #stockLevel2Container').hide();
        $('#stockLevel3Container').show();

        if ($.fn.DataTable.isDataTable('#stockDrilldownTable')) {
            $('#stockDrilldownTable').DataTable().clear().destroy();
        }
        $('#stockDrilldownTable tbody').html('<tr><td colspan="19" class="text-center py-4 text-primary fw-semibold"><span class="spinner-border spinner-border-sm me-2"></span>Loading detailed transactions...</td></tr>');

        $.ajax({
            url: window.location.pathname,
            method: 'GET',
            data: {
                report_type: 'stock-report-drilldown',
                brand_id: currentBrandId,
                brand: currentBrandName,
                fabric_width_id: currentFabricWidthId,
                width: currentWidthName,
                style_id: styleId || '',
                style_name: styleName,
                from_date: $('.start_date').val(),
                to_date: $('.end_date').val(),
                supplier_id: $('select[name="supplier_id"]').val()
            },
            dataType: 'json',
            success: function(response) {
                let items = response.data || [];
                if (response.totals) {
                    $('#footer-drill-opening-qty').text(response.totals.opening_qty);
                    $('#footer-drill-opening-val').text(response.totals.opening_value);
                    $('#footer-drill-inward-qty').text(response.totals.inward_qty);
                    $('#footer-drill-inward-val').text(response.totals.inward_value);
                    $('#footer-drill-outward-qty').text(response.totals.outward_qty);
                    $('#footer-drill-outward-val').text(response.totals.outward_value);
                    $('#footer-drill-closing-qty').text(response.totals.closing_qty);
                    $('#footer-drill-closing-val').text(response.totals.closing_value);
                }

                const dt3 = $('#stockDrilldownTable').DataTable({
                    data: items,
                    autoWidth: false,
                    scrollX: true,
                    scrollCollapse: true,
                    columns: [
                        { data: 'sno', className: 'text-center' },
                        { data: 'width', className: 'text-center fw-semibold' },
                        { data: 'location', className: 'text-center fw-bold text-primary' },
                        { data: 'art_no', className: 'fw-bold text-dark' },
                        { data: 'uom', className: 'text-center' },
                        { data: 'inward_date', className: 'text-center' },
                        { data: 'doc_type' },
                        { data: 'doc_number', className: 'fw-semibold' },
                        { data: 'opening_qty', className: 'text-end' },
                        { data: 'opening_value', className: 'text-end' },
                        { data: 'inward_qty', className: 'text-end text-success fw-semibold' },
                        { data: 'inward_value', className: 'text-end text-success fw-semibold' },
                        { data: 'outward_doc_type' },
                        { data: 'outward_doc_number', className: 'fw-semibold text-danger' },
                        { data: 'outward_date', className: 'text-center' },
                        { data: 'outward_qty', className: 'text-end text-danger fw-semibold' },
                        { data: 'outward_value', className: 'text-end text-danger fw-semibold' },
                        { data: 'closing_qty', className: 'text-end fw-bold' },
                        { data: 'closing_value', className: 'text-end fw-bold text-primary' }
                    ],
                    dom: '<"row mb-3 align-items-center"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>t<"row mt-3 align-items-center"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6 d-flex justify-content-end"p>>',
                    pageLength: 10,
                    bLengthChange: true,
                    bFilter: true,
                    bInfo: true,
                    language: {
                        emptyTable: 'No detailed stock transactions found for this style.'
                    }
                });

                setTimeout(function() {
                    dt3.columns.adjust().draw(false);
                }, 150);
            }
        });
    }

    // Accessories Store Drilldown View matching Excel layout (Images 2 & 3)
    function drilldownToAccessoriesStock(itemName, rawMaterialId, itemId) {
        currentBrandName = itemName || 'All Accessories';

        $('#stockDrilldownBreadcrumbs').attr('style', 'display: flex !important;');
        $('#btnBackToStockReport').attr('onclick', 'renderStockLevel1()');
        $('#stockBreadcrumbText').html(`Accessories Store &nbsp; <i class="ri-arrow-right-s-line"></i> &nbsp; <span class="text-primary fw-bold">${currentBrandName}</span>`);

        $('#stockLevel1Container, #stockLevel2Container, #stockLevel3Container').hide();
        $('#accDrilldownContainer').show();

        if ($.fn.DataTable.isDataTable('#accDrilldownTable')) {
            $('#accDrilldownTable').DataTable().clear().destroy();
        }
        $('#accDrilldownTable tbody').html('<tr><td colspan="24" class="text-center py-4 text-primary fw-semibold"><span class="spinner-border spinner-border-sm me-2"></span>Loading detailed stock records...</td></tr>');

        $.ajax({
            url: window.location.pathname,
            type: 'GET',
            data: {
                report_type: 'stock-report-drilldown',
                store_category_id: 2,
                item_name: itemName,
                raw_material_id: rawMaterialId || '',
                item_id: itemId || '',
                from_date: $('.start_date').val(),
                to_date: $('.end_date').val(),
                supplier_id: $('select[name="supplier_id"]').val()
            },
            dataType: 'json',
            success: function(response) {
                let items = response.data || [];
                if (response.totals) {
                    $('#acc-drill-opening-qty').text(response.totals.opening_qty);
                    $('#acc-drill-opening-val').text(response.totals.opening_value);
                    $('#acc-drill-inward-qty').text(response.totals.inward_qty);
                    $('#acc-drill-inward-val').text(response.totals.inward_value);
                    $('#acc-drill-outward-qty').text(response.totals.outward_qty);
                    $('#acc-drill-outward-val').text(response.totals.outward_value);
                    $('#acc-drill-closing-stock').text(response.totals.closing_stock);
                    $('#acc-drill-total-val').text(response.totals.total_value);
                }

                const dtAcc = $('#accDrilldownTable').DataTable({
                    data: items,
                    autoWidth: false,
                    scrollX: true,
                    scrollCollapse: true,
                    columns: [
                        { data: 'sno', className: 'text-center' },
                        { data: 'description', className: 'fw-bold text-dark' },
                        { data: 'uom', className: 'text-center' },
                        { data: 'rate', className: 'text-end' },
                        { data: 'opening_qty', className: 'text-end' },
                        { data: 'opening_value', className: 'text-end' },
                        { data: 'doc_number', className: 'text-center fw-semibold' },
                        { data: 'inward_date', className: 'text-center' },
                        { data: 'inward_qty', className: 'text-end text-success fw-semibold' },
                        { data: 'inward_value', className: 'text-end text-success fw-semibold' },
                        { data: 'outward_doc_type', className: 'text-center' },
                        { data: 'outward_doc_number', className: 'text-center fw-semibold text-danger' },
                        { data: 'outward_date', className: 'text-center' },
                        { data: 'outward_qty', className: 'text-end text-danger fw-semibold' },
                        { data: 'outward_value', className: 'text-end text-danger fw-semibold' },
                        { data: 'closing_stock', className: 'text-end fw-bold' },
                        { data: 'total_value', className: 'text-end fw-bold text-primary' },
                        { data: 'delivery_days', className: 'text-center' },
                        { data: 'min_stock', className: 'text-end' },
                        { data: 'diff', className: 'text-center' },
                        { data: 'reorder', className: 'text-end' },
                        { data: 'supplier_name', className: 'fw-semibold' },
                        { data: 'order_info' },
                        { data: 'delivery_date', className: 'text-center' }
                    ],
                    dom: '<"row mb-3 align-items-center"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>t<"row mt-3 align-items-center"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6 d-flex justify-content-end"p>>',
                    pageLength: 10,
                    bLengthChange: true,
                    bFilter: true,
                    bInfo: true,
                    language: {
                        emptyTable: 'No detailed stock records found for this item.'
                    }
                });

                setTimeout(function() {
                    dtAcc.columns.adjust().draw(false);
                }, 150);
            },
            error: function() {
                alert('Failed to fetch accessories stock details.');
            }
        });
    }

    $(document).ready(function() {
        const $table = $('.datatables-stock');
        if (!$table.length || !$.fn.DataTable) return;

        if ($.fn.DataTable.isDataTable($table)) {
            $table.DataTable().clear().destroy();
        }

        const isFabric = {{ $isFabric ? 'true' : 'false' }};
        const columns = isFabric ? [
            { data: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'brand', className: 'text-start ps-3 fw-bold' },
            { data: 'width', className: 'text-center' },
            { data: 'opening', className: 'text-end' },
            { data: 'inward', className: 'text-end text-success fw-semibold' },
            { data: 'outward', className: 'text-end text-danger fw-semibold' },
            { data: 'closing', className: 'text-end fw-bold' }
        ] : [
            { data: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'item_name' },
            { data: 'opening', className: 'text-end' },
            { data: 'inward', className: 'text-end text-success fw-semibold' },
            { data: 'outward', className: 'text-end text-danger fw-semibold' },
            { data: 'closing', className: 'text-end fw-bold' },
            { data: 'closing_cost', className: 'text-end fw-bold text-primary' }
        ];

        const dt = $table.DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            pageLength: 10,
            bLengthChange: true,
            bFilter: true,
            bInfo: true,
            bAutoWidth: false,
            ajax: {
                url: window.location.pathname,
                data: function(d) {
                    d.report_type = 'stock-report';
                    d.from_date = $('.start_date').val();
                    d.to_date = $('.end_date').val();
                    d.supplier_id = $('select[name="supplier_id"]').val();
                }
            },
            columns: columns,
            drawCallback: function(settings) {
                var json = settings.json;
                if (json && json.totals) {
                    if (isFabric) {
                        $('#footer-stock-opening').html(json.totals.opening);
                        $('#footer-stock-inward').html(json.totals.inward);
                        $('#footer-stock-outward').html(json.totals.outward);
                        $('#footer-stock-closing').html(json.totals.closing);
                    } else {
                        $('#footer-acc-opening').html(json.totals.opening);
                        $('#footer-acc-inward').html(json.totals.inward);
                        $('#footer-acc-outward').html(json.totals.outward);
                        $('#footer-acc-closing').html(json.totals.closing);
                        $('#footer-acc-cost').html(json.totals.closing_cost);
                    }
                }
            },
            language: {
                emptyTable: 'No stock data found.'
            }
        });

        if (isFabric) {
            $table.find('tbody').off('click', 'tr').on('click', 'tr', function(e) {
                if ($(e.target).is('a, button, input')) return;

                var tr = $(this);
                var row = dt.row(tr);
                if (!row || !row.data()) return;

                var data = row.data();
                drilldownToStockStyles(data.brand, data.width, data.brand_id, data.fabric_width_id);
            });
        } else {
            $table.find('tbody').off('click', 'tr').on('click', 'tr', function(e) {
                if ($(e.target).is('a, button, input')) return;

                var tr = $(this);
                var row = dt.row(tr);
                if (!row || !row.data()) return;

                var data = row.data();
                drilldownToAccessoriesStock(data.item_name, data.raw_material_id, data.item_id);
            });
        }
    });
</script>
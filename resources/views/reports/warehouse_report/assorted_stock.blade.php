<div class="mb-3 d-flex align-items-center" id="assortedBreadcrumbs" style="display: none !important;">
    <button class="btn btn-sm btn-outline-secondary me-2" onclick="renderAssortedBrandLevel()" id="btnBackToAssorted">
        <i class="ri ri-arrow-left-line"></i> Back to Brands
    </button>
    <span class="text-muted fw-bold" id="assortedBreadcrumbText">All Brands</span>
</div>

<div class="card-datatable table-responsive">
    <table class="datatables-products table table-hover" id="assortedStockTable">
        <thead class="table-light">
            <tr id="assortedStockTheadTr">
                <th class="fw-bold">BRAND</th>
                <th class="text-center fw-bold">STYLES</th>
                <th class="text-center fw-bold">ART NOS</th>
                <th class="text-center fw-bold">TOTAL ASSORTED STOCK</th>
            </tr>
        </thead>
        <tbody id="assortedStockTbody">
        </tbody>
        <tfoot class="table-light border-top" id="assortedStockTfoot">
            <tr id="assortedStockTfootTr" class="bg-light fw-bold">
                <th class="fw-bold text-start">TOTAL</th>
                <th class="text-center fw-bold">-</th>
                <th class="text-center fw-bold">-</th>
                <th class="text-center fw-bold text-primary" id="assortedFootQty">0</th>
            </tr>
        </tfoot>
    </table>
</div>

<style>
    #assortedStockTable tbody tr:hover td {
        background-color: rgba(105, 108, 255, 0.08) !important;
        transition: all 0.2s ease-in-out;
    }
    #assortedStockTfoot th {
        font-size: 0.92rem;
        padding-top: 10px;
        padding-bottom: 10px;
    }
    .cursor-pointer {
        cursor: pointer;
    }
</style>

<script>
    var currentAssortedBrandId = null;
    var currentAssortedBrandName = '';
    var currentAssortedStyleId = null;
    var currentAssortedStyleName = '';
    var currentAssortedSleeve = '';
    var assortedDtInstance = null;

    function formatNumber(num) {
        return parseFloat(num || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 });
    }

    function reinitAssortedDataTable() {
        if ($.fn.DataTable.isDataTable('#assortedStockTable')) {
            $('#assortedStockTable').DataTable().clear().destroy();
        }
        $('#assortedStockTbody').empty();
    }

    function renderAssortedBrandLevel() {
        $('#assortedBreadcrumbs').attr('style', 'display: none !important;');
        reinitAssortedDataTable();

        $('#assortedStockTheadTr').html(`
            <th class="fw-bold">BRAND</th>
            <th class="text-center fw-bold">STYLES</th>
            <th class="text-center fw-bold">ART NOS</th>
            <th class="text-center fw-bold">TOTAL ASSORTED STOCK</th>
        `);

        $('#assortedStockTfootTr').html(`
            <th class="fw-bold text-start">TOTAL</th>
            <th class="text-center fw-bold">-</th>
            <th class="text-center fw-bold">-</th>
            <th class="text-center fw-bold text-primary" id="assortedFootQty">0</th>
        `);

        assortedDtInstance = $('#assortedStockTable').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: "{{ url('warehouse_reports/ajax/assorted-stock') }}",
                data: function (d) {
                    d.from_date = $('.start_date').val();
                    d.to_date = $('.end_date').val();
                    d.brand_id = $('select[name="brand_id"]').val();
                    d.store_id = $('select[name="store_id"]').val();
                    d.warehouse_id = $('#filter_warehouse_id').val() || $('#top_warehouse_select').val();
                }
            },
            columns: [
                { data: 'brand', name: 'brand', width: '40%' },
                { data: 'style_count', name: 'style_count', className: 'text-center', orderable: false, width: '20%' },
                { data: 'art_count', name: 'art_count', className: 'text-center', orderable: false, width: '20%' },
                { data: 'stock_qty', name: 'stock_qty', className: 'text-center', width: '20%' }
            ],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10,
            language: {
                emptyTable: "No assorted stock found matching your filters",
                zeroRecords: "No matching records found",
                infoEmpty: "Showing 0 to 0 entries",
            },
            drawCallback: function (settings) {
                var json = settings.json;
                if (json && json.totals && json.totals.stock_qty !== undefined) {
                    $('#assortedFootQty').html(json.totals.stock_qty);
                } else {
                    var api = this.api();
                    var intVal = function (i) {
                        return typeof i === 'string' ? i.replace(/[\₹,<span.*?>|<\/span>]/g, '').trim() * 1 : typeof i === 'number' ? i : 0;
                    };
                    var pQty = api.column(3, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    $('#assortedFootQty').html(formatNumber(pQty));
                }
            }
        });
    }

    function drillDownToAssortedStyle(brandId, brandName) {
        currentAssortedBrandId = brandId;
        currentAssortedBrandName = brandName;

        $('#assortedBreadcrumbs').attr('style', 'display: flex !important;');
        $('#assortedBreadcrumbText').html(`All Brands &nbsp; <i class="ri ri-arrow-right-s-line"></i> &nbsp; <span class="text-primary">${brandName}</span>`);
        $('#btnBackToAssorted').attr('onclick', 'renderAssortedBrandLevel()').html('<i class="ri ri-arrow-left-line"></i> Back to Brands');

        reinitAssortedDataTable();

        $('#assortedStockTheadTr').html(`
            <th class="fw-bold">STYLE</th>
            <th class="text-center fw-bold">ART NOS</th>
            <th class="text-center fw-bold">SLEEVE</th>
            <th class="text-center fw-bold">36</th>
            <th class="text-center fw-bold">38</th>
            <th class="text-center fw-bold">40</th>
            <th class="text-center fw-bold">42</th>
            <th class="text-center fw-bold">44</th>
            <th class="text-center fw-bold">46</th>
            <th class="text-center fw-bold">48</th>
            <th class="text-center fw-bold">50</th>
            <th class="text-center fw-bold">TOTAL QTY</th>
        `);

        $('#assortedStockTfootTr').html(`
            <th class="fw-bold text-start">TOTAL</th>
            <th class="text-center fw-bold text-info" id="assortedStyleFootArtCount">-</th>
            <th class="text-center fw-bold">-</th>
            <th class="text-center fw-bold text-dark" id="assortedStyleFoot36">-</th>
            <th class="text-center fw-bold text-dark" id="assortedStyleFoot38">-</th>
            <th class="text-center fw-bold text-dark" id="assortedStyleFoot40">-</th>
            <th class="text-center fw-bold text-dark" id="assortedStyleFoot42">-</th>
            <th class="text-center fw-bold text-dark" id="assortedStyleFoot44">-</th>
            <th class="text-center fw-bold text-dark" id="assortedStyleFoot46">-</th>
            <th class="text-center fw-bold text-dark" id="assortedStyleFoot48">-</th>
            <th class="text-center fw-bold text-dark" id="assortedStyleFoot50">-</th>
            <th class="text-center fw-bold text-primary" id="assortedStyleFootQty">0</th>
        `);

        assortedDtInstance = $('#assortedStockTable').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: "{{ url('warehouse_reports/assorted_styles') }}",
                data: function (d) {
                    d.from_date = $('.start_date').val();
                    d.to_date = $('.end_date').val();
                    d.brand_id = brandId;
                    d.brand_name = brandName;
                    d.store_id = $('select[name="store_id"]').val();
                    d.warehouse_id = $('#filter_warehouse_id').val() || $('#top_warehouse_select').val();
                }
            },
            columns: [
                { data: 'style', name: 'styles.style_name' },
                { data: 'art_count', name: 'art_count', className: 'text-center' },
                { data: 'sleeve', name: 'assorted.sleeve_type', className: 'text-center' },
                { data: 'size_36', name: 'size_36', className: 'text-center', orderable: false },
                { data: 'size_38', name: 'size_38', className: 'text-center', orderable: false },
                { data: 'size_40', name: 'size_40', className: 'text-center', orderable: false },
                { data: 'size_42', name: 'size_42', className: 'text-center', orderable: false },
                { data: 'size_44', name: 'size_44', className: 'text-center', orderable: false },
                { data: 'size_46', name: 'size_46', className: 'text-center', orderable: false },
                { data: 'size_48', name: 'size_48', className: 'text-center', orderable: false },
                { data: 'size_50', name: 'size_50', className: 'text-center', orderable: false },
                { data: 'stock_qty', name: 'assorted_qty', className: 'text-center' }
            ],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10,
            language: {
                emptyTable: "No styles found with assorted stock",
                zeroRecords: "No matching records found",
                infoEmpty: "Showing 0 to 0 entries",
            },
            drawCallback: function (settings) {
                var json = settings.json;
                if (json && json.totals) {
                    if (json.totals.art_count !== undefined) $('#assortedStyleFootArtCount').html(json.totals.art_count);
                    if (json.totals.sum_36 !== undefined) $('#assortedStyleFoot36').html(json.totals.sum_36);
                    if (json.totals.sum_38 !== undefined) $('#assortedStyleFoot38').html(json.totals.sum_38);
                    if (json.totals.sum_40 !== undefined) $('#assortedStyleFoot40').html(json.totals.sum_40);
                    if (json.totals.sum_42 !== undefined) $('#assortedStyleFoot42').html(json.totals.sum_42);
                    if (json.totals.sum_44 !== undefined) $('#assortedStyleFoot44').html(json.totals.sum_44);
                    if (json.totals.sum_46 !== undefined) $('#assortedStyleFoot46').html(json.totals.sum_46);
                    if (json.totals.sum_48 !== undefined) $('#assortedStyleFoot48').html(json.totals.sum_48);
                    if (json.totals.sum_50 !== undefined) $('#assortedStyleFoot50').html(json.totals.sum_50);
                    if (json.totals.stock_qty !== undefined) $('#assortedStyleFootQty').html(json.totals.stock_qty);
                }
            }
        });
    }

    function drillDownToAssortedArtNo(brandId, brandName, styleId, styleName, sleeve) {
        currentAssortedBrandId = brandId;
        currentAssortedBrandName = brandName;
        currentAssortedStyleId = styleId;
        currentAssortedStyleName = styleName;
        currentAssortedSleeve = sleeve || '';

        var sleeveTitle = currentAssortedSleeve && currentAssortedSleeve !== '-' ? ` (${currentAssortedSleeve})` : '';
        $('#assortedBreadcrumbs').attr('style', 'display: flex !important;');
        $('#assortedBreadcrumbText').html(`All Brands &nbsp; <i class="ri ri-arrow-right-s-line"></i> &nbsp; <span class="text-secondary cursor-pointer" onclick="drillDownToAssortedStyle(${brandId}, '${brandName.replace(/'/g, "\\'")}')">${brandName}</span> &nbsp; <i class="ri ri-arrow-right-s-line"></i> &nbsp; <span class="text-primary">${styleName}${sleeveTitle}</span>`);
        $('#btnBackToAssorted').attr('onclick', `drillDownToAssortedStyle(${brandId}, '${brandName.replace(/'/g, "\\'")}')`).html('<i class="ri ri-arrow-left-line"></i> Back to Styles');

        reinitAssortedDataTable();

        $('#assortedStockTheadTr').html(`
            <th class="fw-bold">ART NO</th>
            <th class="text-center fw-bold">SLEEVE</th>
            <th class="text-center fw-bold">36</th>
            <th class="text-center fw-bold">38</th>
            <th class="text-center fw-bold">40</th>
            <th class="text-center fw-bold">42</th>
            <th class="text-center fw-bold">44</th>
            <th class="text-center fw-bold">46</th>
            <th class="text-center fw-bold">48</th>
            <th class="text-center fw-bold">50</th>
            <th class="text-center fw-bold">TOTAL QTY</th>
        `);

        $('#assortedStockTfootTr').html(`
            <th class="fw-bold text-start">TOTAL</th>
            <th class="text-center fw-bold">-</th>
            <th class="text-center fw-bold text-dark" id="assortedFoot36">-</th>
            <th class="text-center fw-bold text-dark" id="assortedFoot38">-</th>
            <th class="text-center fw-bold text-dark" id="assortedFoot40">-</th>
            <th class="text-center fw-bold text-dark" id="assortedFoot42">-</th>
            <th class="text-center fw-bold text-dark" id="assortedFoot44">-</th>
            <th class="text-center fw-bold text-dark" id="assortedFoot46">-</th>
            <th class="text-center fw-bold text-dark" id="assortedFoot48">-</th>
            <th class="text-center fw-bold text-dark" id="assortedFoot50">-</th>
            <th class="text-center fw-bold text-primary" id="assortedFootQty">0</th>
        `);

        assortedDtInstance = $('#assortedStockTable').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: "{{ url('warehouse_reports/assorted_art_nos') }}",
                data: function (d) {
                    d.from_date = $('.start_date').val();
                    d.to_date = $('.end_date').val();
                    d.brand_id = brandId;
                    d.style_id = styleId;
                    d.sleeve = currentAssortedSleeve;
                    d.store_id = $('select[name="store_id"]').val();
                    d.warehouse_id = $('#filter_warehouse_id').val() || $('#top_warehouse_select').val();
                }
            },
            columns: [
                { data: 'art_no', name: 'stock_entry_items.art_no' },
                { data: 'sleeve', name: 'stock_entry_items.sleeve_type', className: 'text-center' },
                { data: 'size_36', name: 'size_36', className: 'text-center', orderable: false },
                { data: 'size_38', name: 'size_38', className: 'text-center', orderable: false },
                { data: 'size_40', name: 'size_40', className: 'text-center', orderable: false },
                { data: 'size_42', name: 'size_42', className: 'text-center', orderable: false },
                { data: 'size_44', name: 'size_44', className: 'text-center', orderable: false },
                { data: 'size_46', name: 'size_46', className: 'text-center', orderable: false },
                { data: 'size_48', name: 'size_48', className: 'text-center', orderable: false },
                { data: 'size_50', name: 'size_50', className: 'text-center', orderable: false },
                { data: 'stock_qty', name: 'stock_qty', className: 'text-center' }
            ],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10,
            language: {
                emptyTable: "No assorted art nos found matching criteria (Art Nos with both size 38 and 40 are excluded)",
                zeroRecords: "No matching records found",
                infoEmpty: "Showing 0 to 0 entries",
            },
            drawCallback: function (settings) {
                var json = settings.json;
                if (json && json.totals) {
                    $('#assortedFoot36').html(json.totals.sum_36 || '-');
                    $('#assortedFoot38').html(json.totals.sum_38 || '-');
                    $('#assortedFoot40').html(json.totals.sum_40 || '-');
                    $('#assortedFoot42').html(json.totals.sum_42 || '-');
                    $('#assortedFoot44').html(json.totals.sum_44 || '-');
                    $('#assortedFoot46').html(json.totals.sum_46 || '-');
                    $('#assortedFoot48').html(json.totals.sum_48 || '-');
                    $('#assortedFoot50').html(json.totals.sum_50 || '-');
                    $('#assortedFootQty').html(json.totals.stock_qty || '0');
                }
            }
        });
    }

    window.initAssortedStockTable = function() {
        renderAssortedBrandLevel();
    };

    function initAssortedStockTable() {
        window.initAssortedStockTable();
    }

    if ($('#assorted-stock').hasClass('active')) {
        window.initAssortedStockTable();
    }
</script>
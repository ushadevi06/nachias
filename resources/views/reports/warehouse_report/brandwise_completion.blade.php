<div class="mb-3 d-flex align-items-center" id="brandwiseCompletionBreadcrumbs" style="display: none !important;">
    <button class="btn btn-sm btn-outline-secondary me-2" onclick="renderBrandwiseCompletionBrandsLevel()" id="btnBackToCompletionBrands">
        <i class="ri ri-arrow-left-line me-1"></i> Back to Brands
    </button>
    <span class="text-muted fw-bold" id="brandwiseCompletionBreadcrumbText">All Brands</span>
</div>

<div class="card-datatable table-responsive">
    <table class="datatables-products table table-hover border-top align-middle" id="brandwiseCompletionTable" style="font-size: 0.82rem;">
        <thead class="table-light text-center">
            <tr>
                <th class="text-nowrap">S.NO</th>
                <th class="text-nowrap">BRAND</th>
                <th class="text-nowrap">ORDER RECEIVED</th>
                <th class="text-nowrap">TOTAL QTY</th>
                <th class="text-nowrap">TOTAL QTY INVOICED</th>
                <th class="text-nowrap">COMPLETION %</th>
                <th class="text-nowrap">PENDING QTY</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<style>
    #brandwiseCompletionTable tbody tr:hover td {
        background-color: rgba(105, 108, 255, 0.08) !important;
        transition: all 0.2s ease-in-out;
    }
</style>

<script>
var currentCompletionBrandId = null;
var currentCompletionBrandName = '';
var currentCompletionSoId = null;
var currentCompletionSoNo = '';

function reinitBrandwiseCompletionTable() {
    if ($.fn.DataTable.isDataTable('#brandwiseCompletionTable')) {
        $('#brandwiseCompletionTable').DataTable().clear().destroy();
    }
    $('#brandwiseCompletionTable').empty();
}

function renderBrandwiseCompletionBrandsLevel() {
    $('#brandwiseCompletionBreadcrumbs').attr('style', 'display: none !important;');
    reinitBrandwiseCompletionTable();

    $('#brandwiseCompletionTable').html(`
        <thead class="table-light text-center">
            <tr>
                <th class="text-nowrap">S.NO</th>
                <th class="text-nowrap">BRAND</th>
                <th class="text-nowrap">ORDER RECEIVED</th>
                <th class="text-nowrap">TOTAL QTY</th>
                <th class="text-nowrap">TOTAL QTY INVOICED</th>
                <th class="text-nowrap">COMPLETION %</th>
                <th class="text-nowrap">PENDING QTY</th>
            </tr>
        </thead>
        <tbody></tbody>
    `);

    $('#brandwiseCompletionTable').DataTable({
        processing: true,
        autoWidth: false,
        serverSide: true,
        ajax: {
            url: "{{ url('warehouse_reports/ajax/brandwise-completion') }}",
            data: function (d) {
                d.from_date = $('.start_date').val();
                d.to_date = $('.end_date').val();
                d.brand_id = $('select[name="brand_id"]').val();
                d.store_id = $('select[name="store_id"]').val();
            }
        },
        columns: [
            { data: 'sno', name: 'sno', className: 'text-center', orderable: false },
            { data: 'brand', name: 'brand' },
            { data: 'orders_count', name: 'orders_count', className: 'text-center fw-bold' },
            { data: 'total_qty', name: 'total_qty', className: 'text-center fw-bold' },
            { data: 'invoiced_qty', name: 'invoiced_qty', className: 'text-center' },
            { data: 'completion_pct', name: 'completion_pct', className: 'text-center' },
            { data: 'pending_qty', name: 'pending_qty', className: 'text-center' }
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10
    });
}

function drillDownToBrandOrders(brandId, brandName) {
    currentCompletionBrandId = brandId;
    currentCompletionBrandName = brandName;

    $('#brandwiseCompletionBreadcrumbs').attr('style', 'display: flex !important;');
    $('#brandwiseCompletionBreadcrumbText').html(`All Brands &nbsp; <i class="ri-arrow-right-s-line"></i> &nbsp; Brand: <span class="text-primary fw-bold">${brandName}</span>`);
    $('#btnBackToCompletionBrands').attr('onclick', 'renderBrandwiseCompletionBrandsLevel()');

    reinitBrandwiseCompletionTable();

    $('#brandwiseCompletionTable').html(`
        <thead class="table-light text-center">
            <tr>
                <th class="text-nowrap">S.NO</th>
                <th class="text-nowrap">ORDER DATE</th>
                <th class="text-nowrap">ORDER NO</th>
                <th class="text-nowrap">CUSTOMER</th>
                <th class="text-nowrap">TOTAL QTY</th>
                <th class="text-nowrap">INVOICED QTY</th>
                <th class="text-nowrap">PENDING QTY</th>
                <th class="text-nowrap">STATUS</th>
                <th class="text-nowrap">AWAITING ART NO</th>
                <th class="text-nowrap">ACTION REQUIRED</th>
            </tr>
        </thead>
        <tbody></tbody>
    `);

    $('#brandwiseCompletionTable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax: {
            url: "{{ url('warehouse_reports/brandwise_completion_orders') }}",
            data: function (d) {
                d.brand_id = brandId;
                d.brand_name = brandName;
                d.from_date = $('.start_date').val();
                d.to_date = $('.end_date').val();
                d.store_id = $('select[name="store_id"]').val();
            }
        },
        columns: [
            { data: 'sno', name: 'sno', className: 'text-center font-monospace' },
            { data: 'order_date', name: 'order_date', className: 'text-center text-nowrap' },
            { data: 'so_no', name: 'so_no', className: 'text-center fw-bold text-nowrap' },
            { data: 'customer', name: 'customer' },
            { data: 'total_qty', name: 'total_qty', className: 'text-center fw-bold' },
            { data: 'invoiced_qty', name: 'invoiced_qty', className: 'text-center' },
            { data: 'pending_qty', name: 'pending_qty', className: 'text-center' },
            { data: 'status', name: 'status', className: 'text-center' },
            { data: 'awaiting_art_nos', name: 'awaiting_art_nos', className: 'text-center', orderable: false },
            { data: 'action_required', name: 'action_required', orderable: false }
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10
    });
}

function drillDownToBrandOrderArtNos(soId, soNo) {
    currentCompletionSoId = soId;
    currentCompletionSoNo = soNo;

    $('#brandwiseCompletionBreadcrumbs').attr('style', 'display: flex !important;');
    $('#brandwiseCompletionBreadcrumbText').html(`All Brands &nbsp; <i class="ri-arrow-right-s-line"></i> &nbsp; Brand: <span class="text-secondary cursor-pointer" onclick="drillDownToBrandOrders(${currentCompletionBrandId}, '${currentCompletionBrandName.replace(/'/g, "\\'")}')">${currentCompletionBrandName}</span> &nbsp; <i class="ri-arrow-right-s-line"></i> &nbsp; Order No: <span class="text-danger fw-bold">${soNo}</span> &nbsp; <i class="ri-arrow-right-s-line"></i> &nbsp; <span class="text-primary fw-bold">Awaiting Art Numbers</span>`);
    $('#btnBackToCompletionBrands').attr('onclick', `drillDownToBrandOrders(${currentCompletionBrandId}, '${currentCompletionBrandName.replace(/'/g, "\\'")}')`);

    reinitBrandwiseCompletionTable();

    $('#brandwiseCompletionTable').html(`
        <thead class="text-center">
            <tr>
                <th style="width: 40px;">#</th>
                <th>ART NO</th>
                <th>ITEM NAME</th>
                <th class="text-center">SLEEVE</th>
                <th class="text-center">SIZE</th>
                <th class="text-center">ORDERED QTY</th>
                <th class="text-center">STATUS</th>
            </tr>
        </thead>
        <tbody></tbody>
    `);

    $('#brandwiseCompletionTable').DataTable({
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
            { data: 'art_no', name: 'art_no', width: '20%' },
            { data: 'item_name', name: 'item_name', width: '35%' },
            { data: 'sleeve', name: 'sleeve', className: 'text-center', width: '10%' },
            { data: 'size', name: 'size', className: 'text-center', width: '10%' },
            { data: 'ordered_qty', name: 'ordered_qty', className: 'text-center fw-bold', width: '10%' },
            { data: 'status', name: 'status', className: 'text-center', width: '10%' }
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10
    });
}

window.initBrandwiseCompletionTable = function() {
    renderBrandwiseCompletionBrandsLevel();
};
function initBrandwiseCompletionTable() {
    window.initBrandwiseCompletionTable();
}

if ($('#brandwise-completion').hasClass('active')) {
    window.initBrandwiseCompletionTable();
}

$(document).on('click', '#brandwise-completion .view-brand-orders-link', function(e) {
    e.preventDefault();
    const brandId = $(this).data('brand-id');
    const brandName = $(this).data('brand-name');
    drillDownToBrandOrders(brandId, brandName);
});

$(document).on('click', '#brandwiseCompletionTable tbody tr', function(e) {
    if ($(e.target).is('a') || $(e.target).is('button') || $(e.target).closest('a').length > 0 || $(e.target).closest('button').length > 0) {
        return;
    }
    const link = $(this).find('.view-brand-orders-link');
    if (link.length > 0) {
        const brandId = link.data('brand-id');
        const brandName = link.data('brand-name');
        drillDownToBrandOrders(brandId, brandName);
    }
});

$(document).on('click', '#brandwise-completion .view-awaiting-art-nos', function(e) {
    e.preventDefault();
    const soId = $(this).data('so-id');
    const soNo = $(this).data('so-no');
    drillDownToBrandOrderArtNos(soId, soNo);
});
</script>

<div class="mb-3 d-flex align-items-center" id="completionBreadcrumbs" style="display: none !important;">
    <button class="btn btn-sm btn-outline-secondary me-2" onclick="renderCompletionRootLevel()" id="btnBackToCompletionOrders">
        <i class="ri-arrow-left-line me-1"></i> Back to Completion Orders
    </button>
    <span class="text-dark fw-bold" id="completionBreadcrumbText">All Orders</span>
</div>

<div class="card-datatable table-responsive">
    <table class="datatables-products table table-hover table-bordered align-middle" id="brandwiseCompletionTable" style="font-size: 0.85rem;">
        <thead class="table-light" id="brandwiseCompletionThead">
            <tr>
                <th style="width: 40px;">S.No</th>
                <th class="text-center" style="min-width: 110px; white-space: nowrap;">Order Date</th>
                <th style="min-width: 95px; white-space: nowrap;">Order No</th>
                <th class="text-center" style="min-width: 110px; white-space: nowrap;">Delivery Date</th>
                <th class="text-center" style="min-width: 100px;">Priority</th>
                <th style="min-width: 190px;">Customer</th>
                <th style="min-width: 130px;">Place</th>
                <th style="min-width: 140px;">Brand</th>
                <th class="text-center">Order Qty</th>
                <th class="text-center">Invoiced Qty</th>
                <th class="text-center">Pending Qty</th>
                <th class="text-center" style="min-width: 110px;">Order Compltd %</th>
                <th class="text-center" style="min-width: 130px;">Status</th>
                <th class="text-center" style="min-width: 130px; white-space: nowrap;">Awaiting Art No</th>
                <th style="min-width: 140px;">Action Required</th>
            </tr>
        </thead>
        <tbody id="brandwiseCompletionTbody">
            <tr>
                <td colspan="15" class="text-center py-5">
                    <div class="spinner-border text-primary me-2" role="status"></div>
                    <span class="fw-bold text-primary align-middle">Loading Brand Wise Completion Report...</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<style>
    #brandwiseCompletionTable th,
    #brandwiseCompletionTable td {
        vertical-align: middle !important;
    }
</style>

<script>
let completionIsRoot = true;

function renderCompletionRootLevel() {
    completionIsRoot = true;
    $('#completionBreadcrumbs').attr('style', 'display: none !important;');

    if ($.fn.DataTable.isDataTable('#brandwiseCompletionTable')) {
        $('#brandwiseCompletionTable').DataTable().destroy();
    }

    $('#brandwiseCompletionThead').html(`
        <tr>
            <th style="width: 40px;">S.No</th>
            <th class="text-center" style="min-width: 110px; white-space: nowrap;">Order Date</th>
            <th style="min-width: 95px; white-space: nowrap;">Order No</th>
            <th class="text-center" style="min-width: 110px; white-space: nowrap;">Delivery Date</th>
            <th class="text-center" style="min-width: 100px;">Priority</th>
            <th style="min-width: 190px;">Customer</th>
            <th style="min-width: 130px;">Place</th>
            <th style="min-width: 140px;">Brand</th>
            <th class="text-center">Order Qty</th>
            <th class="text-center">Invoiced Qty</th>
            <th class="text-center">Pending Qty</th>
            <th class="text-center" style="min-width: 110px;">Order Compltd %</th>
            <th class="text-center" style="min-width: 130px;">Status</th>
            <th class="text-center" style="min-width: 130px; white-space: nowrap;">Awaiting Art No</th>
            <th style="min-width: 140px;">Action Required</th>
        </tr>
    `);

    $('#brandwiseCompletionTbody').html(`
        <tr>
            <td colspan="15" class="text-center py-5">
                <div class="spinner-border text-primary me-2" role="status"></div>
                <span class="fw-bold text-primary align-middle">Loading Brand Wise Completion Report...</span>
            </td>
        </tr>
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
            { data: 'sno', name: 'sno', className: 'text-center' },
            { data: 'order_date', name: 'order_date', className: 'text-center' },
            { data: 'order_no', name: 'order_no', className: 'fw-bold' },
            { data: 'delivery_date', name: 'delivery_date', className: 'text-center' },
            { data: 'priority', name: 'priority', className: 'text-center' },
            { data: 'customer', name: 'customer' },
            { data: 'place', name: 'place' },
            { data: 'brand', name: 'brand' },
            { data: 'order_qty', name: 'order_qty', className: 'text-center' },
            { data: 'invoiced_qty', name: 'invoiced_qty', className: 'text-center' },
            { data: 'pending_qty', name: 'pending_qty', className: 'text-center fw-bold text-danger' },
            { data: 'compltd_percent', name: 'compltd_percent', className: 'text-center' },
            { data: 'status', name: 'status', className: 'text-center' },
            { data: 'awaiting_art_no', name: 'awaiting_art_no', className: 'text-center' },
            { data: 'action_required', name: 'action_required' }
        ],
        language: {
            processing: '<div class="py-4"><div class="spinner-border text-primary me-2" role="status"></div> <span class="fw-bold text-primary align-middle">Loading Completion Data...</span></div>',
            emptyTable: "No completion records found matching your filters",
            zeroRecords: "No matching records found"
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10
    });
}

function drillDownToCompletionArtNos(soId, soNo) {
    completionIsRoot = false;
    $('#completionBreadcrumbs').removeAttr('style');
    $('#completionBreadcrumbText').html('All Orders &gt; <span class="text-primary text-uppercase">ORDER #' + soNo + '</span> (Art No Breakdown)');

    if ($.fn.DataTable.isDataTable('#brandwiseCompletionTable')) {
        $('#brandwiseCompletionTable').DataTable().destroy();
    }

    $('#brandwiseCompletionThead').html(`
        <tr>
            <th>Art No</th>
            <th>Item Name / SKU</th>
            <th class="text-center">Ordered Qty</th>
            <th class="text-center">Invoiced Qty</th>
            <th class="text-center">Pending Qty</th>
            <th class="text-center">Status</th>
        </tr>
    `);

    $('#brandwiseCompletionTbody').html(`
        <tr>
            <td colspan="6" class="text-center py-5">
                <div class="spinner-border text-primary me-2" role="status"></div>
                <span class="fw-bold text-primary align-middle">Loading Art Nos for Order #${soNo}...</span>
            </td>
        </tr>
    `);

    $.ajax({
        url: "{{ url('warehouse_reports/brandwise_completion_art_nos') }}",
        type: "GET",
        data: { so_id: soId },
        dataType: "json",
        success: function(response) {
            if (response.status) {
                let rowsHtml = '';
                if (response.items && response.items.length > 0) {
                    $.each(response.items, function(idx, item) {
                        rowsHtml += `<tr>
                            <td class="fw-bold text-primary">${item.art_no}</td>
                            <td>${item.item_name}</td>
                            <td class="text-center">${item.ordered_qty}</td>
                            <td class="text-center">${item.invoiced_qty}</td>
                            <td class="text-center fw-bold text-danger">${item.pending_qty}</td>
                            <td class="text-center">${item.status}</td>
                        </tr>`;
                    });
                } else {
                    rowsHtml = '<tr><td colspan="6" class="text-center py-4 text-muted">No line-item Art Nos found for this order.</td></tr>';
                }

                $('#brandwiseCompletionTbody').html(rowsHtml);

                $('#brandwiseCompletionTable').DataTable({
                    autoWidth: false,
                    paging: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    lengthMenu: [10, 25, 50, 100],
                    pageLength: 10,
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
                });
            }
        },
        error: function() {
            $('#brandwiseCompletionTbody').html('<tr><td colspan="6" class="text-center text-danger py-4">Failed to load Art No breakdown. Please try again.</td></tr>');
        }
    });
}

function openArtNoModal(soId, soNo) {
    drillDownToCompletionArtNos(soId, soNo);
}

$(document).ready(function() {
    renderCompletionRootLevel();
});
</script>

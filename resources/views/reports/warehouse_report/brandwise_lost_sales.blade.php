<div class="mb-3 d-flex align-items-center" id="lostSalesBreadcrumbs" style="display: none !important;">
    <button class="btn btn-sm btn-outline-secondary me-2" onclick="renderLostSalesBrandLevel()" id="btnBackToLostSalesBrands">
        <i class="ri ri-arrow-left-line me-1"></i> Back to Brands
    </button>
    <span class="text-dark fw-bold" id="lostSalesBreadcrumbText">All Brands</span>
</div>

<div class="card-datatable table-responsive">
    <table class="datatables-products table table-hover" id="brandwiseLostSalesTable">
        <thead class="table-light" id="brandwiseLostSalesThead">
            <tr>
                <th>Brand</th>
                <th class="text-center">Missed Orders</th>
                <th class="text-center">Lost Revenue</th>
            </tr>
        </thead>
        <tbody id="brandwiseLostSalesTbody">
            <tr>
                <td colspan="3" class="text-center py-5">
                    <div class="spinner-border text-primary me-2" role="status"></div>
                    <span class="fw-bold text-primary align-middle">Loading Brandwise Lost Sales...</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<style>
    #brandwiseLostSalesTable tbody tr.clickable-brand-row:hover td {
        background-color: rgba(105, 108, 255, 0.08) !important;
        transition: all 0.2s ease-in-out;
    }
</style>

<script>
let lostSalesIsRoot = true;

function renderLostSalesBrandLevel() {
    lostSalesIsRoot = true;
    $('#lostSalesBreadcrumbs').attr('style', 'display: none !important;');
    
    if ($.fn.DataTable.isDataTable('#brandwiseLostSalesTable')) {
        $('#brandwiseLostSalesTable').DataTable().destroy();
    }
    
    $('#brandwiseLostSalesThead').html(`
        <tr>
            <th>Brand</th>
            <th class="text-center">Missed Orders</th>
            <th class="text-center">Lost Revenue</th>
        </tr>
    `);

    $('#brandwiseLostSalesTbody').html(`
        <tr>
            <td colspan="3" class="text-center py-5">
                <div class="spinner-border text-primary me-2" role="status"></div>
                <span class="fw-bold text-primary align-middle">Loading Brandwise Lost Sales...</span>
            </td>
        </tr>
    `);

    $('#brandwiseLostSalesTable').DataTable({
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
    $('#lostSalesBreadcrumbs').removeAttr('style');
    $('#lostSalesBreadcrumbText').html('All Brands &gt; <span class="text-primary text-uppercase">' + brandName + '</span> (Missed Sales Orders)');

    if ($.fn.DataTable.isDataTable('#brandwiseLostSalesTable')) {
        $('#brandwiseLostSalesTable').DataTable().destroy();
    }

    $('#brandwiseLostSalesThead').html(`
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
    `);

    $('#brandwiseLostSalesTbody').html(`
        <tr>
            <td colspan="8" class="text-center py-5">
                <div class="spinner-border text-primary me-2" role="status"></div>
                <span class="fw-bold text-primary align-middle">Loading Missed Orders for ${brandName}...</span>
            </td>
        </tr>
    `);

    $.ajax({
        url: "{{ url('warehouse_reports/brandwise_lost_sales_orders') }}",
        type: "GET",
        data: {
            brand_name: brandName,
            from_date: $('.start_date').val(),
            to_date: $('.end_date').val(),
            store_id: $('select[name="store_id"]').val()
        },
        dataType: "json",
        success: function(response) {
            if (response.status) {
                let rowsHtml = '';
                if (response.orders && response.orders.length > 0) {
                    $.each(response.orders, function(idx, order) {
                        rowsHtml += `<tr>
                            <td>${order.so_no}</td>
                            <td>${order.customer}</td>
                            <td class="text-center">${order.so_date}</td>
                            <td class="text-center">${order.ordered_qty}</td>
                            <td class="text-center">${order.invoiced_qty}</td>
                            <td class="text-center">${order.lost_qty}</td>
                            <td class="text-center">${order.lost_value}</td>
                            <td>${order.reason}</td>
                        </tr>`;
                    });
                } else {
                    rowsHtml = '<tr><td colspan="8" class="text-center py-4 text-muted">No missed sales orders found for this brand.</td></tr>';
                }

                $('#brandwiseLostSalesTbody').html(rowsHtml);

                $('#brandwiseLostSalesTable').DataTable({
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
            $('#brandwiseLostSalesTbody').html('<tr><td colspan="8" class="text-center text-danger py-4">Failed to load order breakdown. Please try again.</td></tr>');
        }
    });
}

$(document).ready(function() {
    renderLostSalesBrandLevel();
});
</script>

<div class="card-datatable table-responsive">
    <table class="datatables-products table table-hover" id="urgentOrdersTable">
        <thead>
            <tr>
                <th>SO No</th>
                <th>Customer</th>
                <th class="text-center">Order Date</th>
                <th class="text-center">Days Pending</th>
                <th class="text-center">Delivery Date</th>
                <th>Brand(s)</th>
                <th class="text-center">Ordered Qty</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<!-- Modal for Viewing Urgent Order Details -->
<div class="modal fade" id="urgentOrderDetailsModal" tabindex="-1" aria-labelledby="urgentOrderModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-3">
                <div>
                    <h5 class="modal-title text-white fw-bold mb-0" id="urgentOrderModalTitle">
                        <i class="ri-shopping-bag-line me-2"></i>Sales Order Items Details
                    </h5>
                    <small class="text-white-50" id="modalSoSubTitle">Order Details & Warehouse Stock Availability</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Order Overview Card -->
                <div class="row g-3 mb-4 p-3 bg-light rounded-3 border">
                    <div class="col-md-3">
                        <span class="text-muted small d-block">SO Number:</span>
                        <strong class="text-primary fs-6" id="modalSoNo">-</strong>
                        <div id="modalOrderaxeRef"></div>
                    </div>
                    <div class="col-md-3">
                        <span class="text-muted small d-block">Customer:</span>
                        <strong class="text-dark" id="modalCustomer">-</strong>
                    </div>
                    <div class="col-md-3">
                        <span class="text-muted small d-block">Order Date:</span>
                        <strong class="text-dark" id="modalSoDate">-</strong>
                    </div>
                    <div class="col-md-3">
                        <span class="text-muted small d-block">Delivery Date:</span>
                        <strong class="text-dark" id="modalDeliveryDate">-</strong>
                    </div>
                </div>

                <!-- Line Items Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0" id="urgentOrderItemsTable">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Art No / SKU</th>
                                <th>Item Name</th>
                                <th>Brand</th>
                                <th>Size</th>
                                <th>Color</th>
                                <th class="text-center">Ordered Qty</th>
                                <th class="text-center">Dispatched Qty</th>
                                <th class="text-center">Pending Qty</th>
                                <th class="text-center">WH Stock</th>
                                <th class="text-center">Stock Status</th>
                            </tr>
                        </thead>
                        <tbody id="urgentOrderItemsBody">
                            <tr>
                                <td colspan="11" class="text-center py-3 text-muted">Loading order items...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 justify-content-between">
                <a id="modalFullOrderLink" href="#" target="_blank" class="btn btn-primary rounded-pill"><i class="ri ri-external-link-line me-1"></i>Open Full Sales Order Page</a>
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#urgentOrdersTable')) {
        $('#urgentOrdersTable').DataTable().destroy();
    }

    var urgentTable = $('#urgentOrdersTable').DataTable({
        processing: true,
        autoWidth: false,
        serverSide: true,
        ajax: {
            url: "{{ url('warehouse_reports/ajax/urgent-orders') }}",
            data: function (d) {
                d.from_date = $('.start_date').val();
                d.to_date = $('.end_date').val();
                d.brand_id = $('select[name="brand_id"]').val();
                d.store_id = $('select[name="store_id"]').val();
            }
        },
        columns: [
            { data: 'so_no', name: 'so_no' },
            { data: 'customer', name: 'customer' },
            { data: 'order_date', name: 'order_date', className: 'text-center' },
            { data: 'days_pending', name: 'days_pending', className: 'text-center' },
            { data: 'delivery_date', name: 'delivery_date', className: 'text-center' },
            { data: 'brands', name: 'brands' },
            { data: 'ordered_qty', name: 'ordered_qty', className: 'text-center' },
            { data: 'action', name: 'action', className: 'text-center', orderable: false, searchable: false }
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10
    });

    // Handle View Items Click
    $(document).on('click', '.view-urgent-details', function() {
        var soId = $(this).data('id');
        var $btn = $(this);
        var originalHtml = $btn.html();

        $btn.html('<span class="spinner-border spinner-border-sm me-1"></span> Loading...').prop('disabled', true);

        $.ajax({
            url: "{{ url('warehouse_reports/urgent_order_details') }}/" + soId,
            type: "GET",
            dataType: "json",
            success: function(response) {
                if (response.status) {
                    $('#modalSoNo').text(response.so_no);
                    $('#modalFullOrderLink').attr('href', "{{ url('sales_orders/view') }}/" + soId);
                    if (response.orderaxe_ref_id) {
                        $('#modalOrderaxeRef').html('<span class="badge bg-label-info mt-1">Ref: ' + response.orderaxe_ref_id + '</span>');
                    } else {
                        $('#modalOrderaxeRef').empty();
                    }
                    $('#modalCustomer').text(response.customer_name);
                    $('#modalSoDate').text(response.so_date);
                    $('#modalDeliveryDate').text(response.delivery_date);

                    var rowsHtml = '';
                    if (response.items && response.items.length > 0) {
                        $.each(response.items, function(idx, item) {
                            rowsHtml += '<tr>' +
                                '<td>' + (idx + 1) + '</td>' +
                                '<td><strong>' + item.art_no + '</strong></td>' +
                                '<td>' + item.item_name + '</td>' +
                                '<td><span class="badge bg-label-primary">' + item.brand_name + '</span></td>' +
                                '<td>' + item.size + '</td>' +
                                '<td>' + item.color + '</td>' +
                                '<td class="text-center fw-bold">' + item.ordered_qty + '</td>' +
                                '<td class="text-center text-primary fw-bold">' + item.dispatched_qty + '</td>' +
                                '<td class="text-center text-danger fw-bold">' + item.pending_qty + '</td>' +
                                '<td class="text-center fw-bold">' + item.wh_stock + '</td>' +
                                '<td class="text-center">' + item.stock_status + '</td>' +
                                '</tr>';
                        });
                    } else {
                        rowsHtml = '<tr><td colspan="11" class="text-center py-3 text-muted">No items found for this order.</td></tr>';
                    }

                    $('#urgentOrderItemsBody').html(rowsHtml);
                    var modalEl = document.getElementById('urgentOrderDetailsModal');
                    var modal = new bootstrap.Modal(modalEl);
                    modal.show();
                } else {
                    alert(response.message || 'Failed to load details.');
                }
            },
            error: function() {
                alert('An error occurred while loading order details.');
            },
            complete: function() {
                $btn.html(originalHtml).prop('disabled', false);
            }
        });
    });
});
</script>

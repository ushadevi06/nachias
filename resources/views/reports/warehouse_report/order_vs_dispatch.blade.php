<div class="card-datatable table-responsive">
    <table class="datatables-products table table-hover" id="orderVsDispatchTable">
        <thead>
            <tr>
                <th>ORDER DATE</th>
                <th>ORDER NO</th>
                <th>DELIVERY DATE</th>
                <th class="text-center">PRIORITY</th>
                <th>CUSTOMER</th>
                <th>PLACE</th>
                <th class="text-center">ORDERED QTY</th>
                <th class="text-center">ORDERED INVOICED QTY</th>
                <th class="text-center">OPEN ORDER QTY</th>
                <th class="text-center">DISPATCHED QTY</th>
                <th class="text-center">PENDING QTY</th>
                <th class="text-center">ORDER COMPLETED</th>
                <th class="text-center">STATUS</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot class="table-light border-top" id="orderVsDispatchTfoot">
            <tr class="bg-light fw-bold">
                <th colspan="6" class="text-end fw-bold">TOTAL</th>
                <th class="text-center fw-bold text-dark" id="ovdFootOrdered">0</th>
                <th class="text-center fw-bold text-success" id="ovdFootOrderedInvoiced">0</th>
                <th class="text-center fw-bold text-info" id="ovdFootOpenOrder">0</th>
                <th class="text-center fw-bold text-primary" id="ovdFootDispatched">0</th>
                <th class="text-center fw-bold text-danger" id="ovdFootPending">0</th>
                <th class="text-center">-</th>
                <th class="text-center">-</th>
            </tr>
        </tfoot>
    </table>
</div>

<script>
window.initOrderVsDispatchTable = function() {
    if ($.fn.DataTable.isDataTable('#orderVsDispatchTable')) {
        $('#orderVsDispatchTable').DataTable().clear().destroy();
    }
    $('#orderVsDispatchTable').DataTable({
        processing: true,
        autoWidth: false,
        serverSide: true,
        ajax: {
            url: "{{ url('warehouse_reports/ajax/order-dispatch') }}",
            data: function (d) {
                d.from_date = $('.start_date').val();
                d.to_date = $('.end_date').val();
                d.brand_id = $('select[name="brand_id"]').val();
                d.customer_id = $('select[name="customer_id"]').val();
                d.store_id = $('select[name="store_id"]').val();
            }
        },
        columns: [
            { data: 'order_date', name: 'sales_orders.so_date' },
            { data: 'order_no', name: 'sales_orders.order_no' },
            { data: 'delivery_date', name: 'sales_orders.delivery_date' },
            { data: 'priority', name: 'sales_orders.order_type', className: 'text-center' },
            { data: 'customer', name: 'customers.name' },
            { data: 'place', name: 'place_name' },
            { data: 'ordered_qty', name: 'ordered_qty', className: 'text-center' },
            { data: 'ordered_invoiced_qty', name: 'ordered_invoiced_qty', className: 'text-center' },
            { data: 'open_order_qty', name: 'open_order_qty', className: 'text-center' },
            { data: 'dispatched_qty', name: 'dispatched_qty', className: 'text-center' },
            { data: 'pending_qty', name: 'pending_qty', className: 'text-center' },
            { data: 'order_completed', name: 'order_completed', className: 'text-center', orderable: false },
            { data: 'status', name: 'sales_orders.status', className: 'text-center' }
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10,
        drawCallback: function (settings) {
            var json = settings.json;
            if (json && json.totals) {
                $('#ovdFootOrdered').text(json.totals.ordered_qty || '0');
                $('#ovdFootOrderedInvoiced').text(json.totals.ordered_invoiced_qty || '0');
                $('#ovdFootOpenOrder').text(json.totals.open_order_qty || '0');
                $('#ovdFootDispatched').text(json.totals.dispatched_qty || '0');
                $('#ovdFootPending').text(json.totals.pending_qty || '0');
            } else {
                var api = this.api();
                var intVal = function (i) {
                    return typeof i === 'string' ? i.replace(/<[^>]*>?/gm, '').replace(/[\₹,]/g, '').trim() * 1 : typeof i === 'number' ? i : 0;
                };
                var fmt = function (num) { return parseFloat(num || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 }); };
                $('#ovdFootOrdered').html(fmt(api.column(6, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
                $('#ovdFootOrderedInvoiced').html(fmt(api.column(7, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
                $('#ovdFootOpenOrder').html(fmt(api.column(8, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
                $('#ovdFootDispatched').html(fmt(api.column(9, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
                $('#ovdFootPending').html(fmt(api.column(10, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
            }
        }
    });
};
function initOrderVsDispatchTable() {
    window.initOrderVsDispatchTable();
}

if ($('#order-dispatch').hasClass('active') || $('#order-vs-dispatch').hasClass('active')) {
    window.initOrderVsDispatchTable();
}
</script>
<div class="card-datatable">
    <table class="datatables-products table table-hover" id="orderVsDispatchTable">
        <thead>
            <tr>
                <th>Order No</th>
                <th>Customer</th>
                <th class="text-center">Ordered Qty</th>
                <th class="text-center">Dispatched Qty</th>
                <th class="text-center">Pending Qty</th>
                <th class="text-center">Progress</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot class="table-light border-top" id="orderVsDispatchTfoot">
            <tr class="bg-light fw-bold">
                <th colspan="2" class="text-end fw-bold">TOTAL</th>
                <th class="text-center fw-bold text-primary" id="ovdFootOrdered">0</th>
                <th class="text-center fw-bold text-success" id="ovdFootDispatched">0</th>
                <th class="text-center fw-bold text-danger" id="ovdFootPending">0</th>
                <th></th>
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
            { data: 'so_no', name: 'so_no' },
            { data: 'customer', name: 'customer' },
            { data: 'ordered_qty', name: 'ordered_qty', className: 'text-center' },
            { data: 'dispatched_qty', name: 'dispatched_qty', className: 'text-center' },
            { data: 'pending_qty', name: 'pending_qty', className: 'text-center' },
            { data: 'fulfillment', name: 'fulfillment', className: 'text-center', orderable: false }
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10,
        drawCallback: function (settings) {
            var api = this.api();
            var intVal = function (i) {
                return typeof i === 'string' ? i.replace(/[\₹,]/g, '').trim() * 1 : typeof i === 'number' ? i : 0;
            };
            var fmt = function (num) { return parseFloat(num || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 }); };
            $('#ovdFootOrdered').html(fmt(api.column(2, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
            $('#ovdFootDispatched').html(fmt(api.column(3, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
            $('#ovdFootPending').html(fmt(api.column(4, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
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
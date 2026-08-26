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
    </table>
</div>

<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#orderVsDispatchTable')) {
        $('#orderVsDispatchTable').DataTable().destroy();
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
                d.store_id = $('select[name="store_id"]').val();
            }
        },
        columns: [
            { data: 'customer', name: 'customer' },
            { data: 'so_no', name: 'so_no', className: 'text-center' },
            { data: 'ordered_qty', name: 'ordered_qty', className: 'text-center' },
            { data: 'dispatched_qty', name: 'dispatched_qty', className: 'text-center' },
            { data: 'pending_qty', name: 'pending_qty', className: 'text-center' },
            { data: 'fulfillment', name: 'fulfillment', className: 'text-center', orderable: false }
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10
    });
});
</script>
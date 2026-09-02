<div class="card-datatable">
    <table class="datatables-products table table-hover" id="assortedStockTable">
        <thead>
            <tr>
                <th>Store</th>
                <th>Item Name</th>
                <th>Size</th>
                <th class="text-center">Stock Qty</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot class="table-light border-top" id="assortedStockTfoot">
            <tr class="bg-light fw-bold">
                <th colspan="3" class="text-end fw-bold">TOTAL</th>
                <th class="text-center fw-bold text-primary" id="assortedFootQty">0</th>
            </tr>
        </tfoot>
    </table>
</div>

<script>
window.initAssortedStockTable = function() {
    if ($.fn.DataTable.isDataTable('#assortedStockTable')) {
        $('#assortedStockTable').DataTable().clear().destroy();
    }
    $('#assortedStockTable').DataTable({
        processing: true,
        autoWidth: false,
        serverSide: true,
        ajax: {
            url: "{{ url('warehouse_reports/ajax/assorted-stock') }}",
            data: function (d) {
                d.from_date = $('.start_date').val();
                d.to_date = $('.end_date').val();
                d.brand_id = $('select[name="brand_id"]').val();
                d.store_id = $('select[name="store_id"]').val();
            }
        },
        columns: [
            { data: 'store', name: 'store' },
            { data: 'item_name', name: 'item_name' },
            { data: 'size', name: 'size', className: 'text-center' },
            { data: 'stock_qty', name: 'stock_qty', className: 'text-center' }
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
            $('#assortedFootQty').html(fmt(api.column(3, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
        }
    });
};
function initAssortedStockTable() {
    window.initAssortedStockTable();
}

if ($('#assorted-stock').hasClass('active')) {
    window.initAssortedStockTable();
}
</script>
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
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10
    });
};
function initAssortedStockTable() {
    window.initAssortedStockTable();
}

if ($('#assorted-stock').hasClass('active')) {
    window.initAssortedStockTable();
}
</script>
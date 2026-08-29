<div class="card-datatable">
    <table class="datatables-products table table-hover" id="stockInwardTable">
        <thead>
            <tr>
                <th>GRN No</th>
                <th>Supplier</th>
                <th>Inward Date</th>
                <th class="text-center">Total Items</th>
                <th class="text-center">QC Status</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<script>
window.initStockInwardTable = function() {
    if ($.fn.DataTable.isDataTable('#stockInwardTable')) {
        $('#stockInwardTable').DataTable().clear().destroy();
    }
    $('#stockInwardTable').DataTable({
        processing: true,
        autoWidth: false,
        serverSide: true,
        ajax: {
            url: "{{ url('warehouse_reports/ajax/stock-inward') }}",
            data: function (d) {
                d.from_date = $('.start_date').val();
                d.to_date = $('.end_date').val();
                d.brand_id = $('select[name="brand_id"]').val();
                d.store_id = $('select[name="store_id"]').val();
            }
        },
        columns: [
            { data: 'date', name: 'date', className: 'text-center' },
            { data: 'grn_no', name: 'grn_no', className: 'text-center' },
            { data: 'supplier', name: 'supplier' },
            { data: 'qty', name: 'qty', className: 'text-center' },
            { data: 'status', name: 'status', className: 'text-center', orderable: false }
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10
    });
};
function initStockInwardTable() {
    window.initStockInwardTable();
}

if ($('#inward').hasClass('active') || $('#stock-inward').hasClass('active')) {
    window.initStockInwardTable();
}
</script>
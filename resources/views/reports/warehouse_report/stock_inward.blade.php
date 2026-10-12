<div class="card-datatable">
    <table class="datatables-products table table-hover" id="stockInwardTable">
        <thead>
            <tr>
                <th class="text-center">Cutting No</th>
                <th class="text-center">Inward Date</th>
                <th>Unit/Plant</th>
                <th>Warehouse</th>
                <th>Responsible Person</th>
                <th class="text-center">Total Qty</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot class="table-light border-top" id="stockInwardTfoot">
            <tr class="bg-light fw-bold">
                <th colspan="5" class="text-end fw-bold">TOTAL</th>
                <th class="text-center fw-bold text-primary" id="inwardFootQty">0</th>
            </tr>
        </tfoot>
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
                d.warehouse_id = $('#filter_warehouse_id').val() || $('#top_warehouse_select').val();
            }
        },
        columns: [
            { data: 'cutting_no', name: 'cutting_no', className: 'text-center' },
            { data: 'inward_date', name: 'inward_date', className: 'text-center' },
            { data: 'unit_plant', name: 'unit_plant' },
            { data: 'warehouse', name: 'warehouse' },
            { data: 'responsible_person', name: 'responsible_person' },
            { data: 'total_qty', name: 'total_qty', className: 'text-center fw-bold text-primary' }
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
            var json = settings.json;
            var total = (json && json.total_qty !== undefined)
                ? json.total_qty
                : api.column(5, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            $('#inwardFootQty').html(fmt(total));
        }
    });
};
function initStockInwardTable() {
    window.initStockInwardTable();
}

if ($('#inward').hasClass('active') || $('#stock-inward').hasClass('active')) {
    window.initStockInwardTable();
}
</script>
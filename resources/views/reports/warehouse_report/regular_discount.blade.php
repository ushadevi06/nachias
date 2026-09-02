<div class="card-datatable">
    <table class="datatables-products table table-hover" id="regularDiscountTable">
        <thead>
            <tr>
                <th>Period</th>
                <th class="text-end">Regular Sales</th>
                <th class="text-end">Discount Sales</th>
                <th class="text-center">Discount %</th>
                <th class="text-end">Net Revenue</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot class="table-light border-top" id="regularDiscountTfoot">
            <tr class="bg-light fw-bold">
                <th class="fw-bold text-start">TOTAL</th>
                <th class="text-end fw-bold text-primary" id="regFootSales">₹0.00</th>
                <th class="text-end fw-bold text-warning" id="regFootDiscount">₹0.00</th>
                <th></th>
                <th class="text-end fw-bold text-success" id="regFootNet">₹0.00</th>
            </tr>
        </tfoot>
    </table>
</div>

<script>
window.initRegularDiscountTable = function() {
    if ($.fn.DataTable.isDataTable('#regularDiscountTable')) {
        $('#regularDiscountTable').DataTable().clear().destroy();
    }
    $('#regularDiscountTable').DataTable({
        processing: true,
        autoWidth: false,
        serverSide: true,
        ajax: {
            url: "{{ url('warehouse_reports/ajax/regular-discount') }}",
            data: function (d) {
                d.from_date = $('.start_date').val();
                d.to_date = $('.end_date').val();
                d.brand_id = $('select[name="brand_id"]').val();
                d.customer_id = $('select[name="customer_id"]').val();
                d.store_id = $('select[name="store_id"]').val();
            }
        },
        columns: [
            { data: 'period', name: 'period', className: 'text-center' },
            { data: 'regular_sales', name: 'regular_sales', className: 'text-end' },
            { data: 'discount_sales', name: 'discount_sales', className: 'text-end' },
            { data: 'discount_pc', name: 'discount_pc', className: 'text-center', orderable: false },
            { data: 'net_revenue', name: 'net_revenue', className: 'text-end' }
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10,
        drawCallback: function (settings) {
            var api = this.api();
            var intVal = function (i) {
                return typeof i === 'string' ? i.replace(/[\₹,]/g, '').trim() * 1 : typeof i === 'number' ? i : 0;
            };
            var cur = function (num) { return '₹' + parseFloat(num || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); };
            $('#regFootSales').html(cur(api.column(1, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
            $('#regFootDiscount').html(cur(api.column(2, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
            $('#regFootNet').html(cur(api.column(4, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
        }
    });
};
function initRegularDiscountTable() {
    window.initRegularDiscountTable();
}

if ($('#discount').hasClass('active') || $('#regular-discount').hasClass('active')) {
    window.initRegularDiscountTable();
}
</script>
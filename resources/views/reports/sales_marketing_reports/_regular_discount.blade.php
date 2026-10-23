<div class="card-datatable table-responsive">
    <table class="datatables-products table table-hover" id="regularDiscountTable">
        <thead class="table-light">
            <tr>
                <th class="text-center fw-bold">Period</th>
                <th class="text-end fw-bold">Total Sales</th>
                <th class="text-end fw-bold">Regular Sales</th>
                <th class="text-end fw-bold">Discount Sales</th>
                <th class="text-end fw-bold">Discount Amount</th>
                <th class="text-center fw-bold">Discount %</th>
                <th class="text-end fw-bold">Net Revenue</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot class="table-light border-top" id="regularDiscountTfoot">
            <tr class="bg-light fw-bold">
                <th class="fw-bold text-center">TOTAL</th>
                <th class="text-end fw-bold text-dark" id="regFootTotalSales">₹0.00</th>
                <th class="text-end fw-bold text-primary" id="regFootSales">₹0.00</th>
                <th class="text-end fw-bold text-warning" id="regFootDiscountSales">₹0.00</th>
                <th class="text-end fw-bold text-danger" id="regFootDiscount">₹0.00</th>
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
            url: "{{ url('sales_marketing_reports/ajax/regular-discount') }}",
            data: function (d) {
                d.from_date = $('.start_date').val();
                d.to_date = $('.end_date').val();
                d.brand_id = $('select[name="brand_id"]').val();
                d.customer_id = $('select[name="customer_id"]').val();
                d.agent_id = $('select[name="agent_id"]').val();
            }
        },
        columns: [
            { data: 'period', name: 'period', className: 'text-center' },
            { data: 'total_sales', name: 'total_sales', className: 'text-end fw-semibold' },
            { data: 'regular_sales', name: 'regular_sales', className: 'text-end' },
            { data: 'discount_sales', name: 'discount_sales', className: 'text-end' },
            { data: 'discount_amount', name: 'discount_amount', className: 'text-end text-danger' },
            { data: 'discount_pc', name: 'discount_pc', className: 'text-center', orderable: false },
            { data: 'net_revenue', name: 'net_revenue', className: 'text-end' }
        ],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10,
        drawCallback: function (settings) {
            var api = this.api();
            var intVal = function (i) {
                if (typeof i === 'number') return i;
                if (typeof i === 'string') {
                    var val = parseFloat(i.replace(/<[^>]*>/g, '').replace(/[^\d.-]/g, ''));
                    return isNaN(val) ? 0 : val;
                }
                return 0;
            };
            var cur = function (num) { return '₹' + parseFloat(num || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); };
            $('#regFootTotalSales').html(cur(api.column(1, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
            $('#regFootSales').html(cur(api.column(2, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
            $('#regFootDiscountSales').html(cur(api.column(3, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
            $('#regFootDiscount').html(cur(api.column(4, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
            $('#regFootNet').html(cur(api.column(6, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0)));
        }
    });
};
function initRegularDiscountTable() {
    window.initRegularDiscountTable();
}

if ($('#regular-discount').hasClass('active') || $('#discount').hasClass('active')) {
    window.initRegularDiscountTable();
}
</script>

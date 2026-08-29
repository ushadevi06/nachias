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
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10
    });
};
function initRegularDiscountTable() {
    window.initRegularDiscountTable();
}

if ($('#discount').hasClass('active') || $('#regular-discount').hasClass('active')) {
    window.initRegularDiscountTable();
}
</script>
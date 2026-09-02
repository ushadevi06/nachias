<div class="card-datatable">
    <table class="datatables-products table table-hover" id="brandwiseSalesTable">
        <thead>
            <tr>
                <th>Brand</th>
                <!-- <th>Category</th> -->
                <th class="text-center">Sold Qty</th>
                <th class="text-end">Sales Value</th>
                <th class="text-center">Trend</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot class="table-light border-top" id="brandwiseSalesTfoot">
            <tr id="brandwiseSalesTfootTr" class="bg-light fw-bold">
                <th class="fw-bold text-start">TOTAL</th>
                <th class="text-center fw-bold text-primary" id="brandwiseSalesFootSoldQty">-</th>
                <th class="text-end fw-bold text-primary" id="brandwiseSalesFootSalesValue">-</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>
<script>
window.initBrandwiseSalesTable = function() {
    if (!$.fn.DataTable.isDataTable('#brandwiseSalesTable')) {
        $('#brandwiseSalesTable').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: "{{ url('warehouse_reports/ajax/brandwise-sales') }}",
                data: function (d) {
                    d.from_date = $('.start_date').val();
                    d.to_date = $('.end_date').val();
                    d.brand_id = $('select[name="brand_id"]').val();
                    d.customer_id = $('select[name="customer_id"]').val();
                    d.store_id = $('select[name="store_id"]').val();
                }
            },
            columns: [
                { data: 'brand', name: 'brand', width: '45%' },
                { data: 'sold_qty', name: 'sold_qty', className: 'text-center', width: '20%' },
                { data: 'sales_value', name: 'sales_value', className: 'text-end', width: '20%' },
                { data: 'trend', name: 'trend', className: 'text-center', orderable: false, width: '15%' }
            ],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10,
            drawCallback: function (settings) {
                var json = settings.json;
                if (json && json.totals) {
                    $('#brandwiseSalesFootSoldQty').html(json.totals.sold_qty);
                    $('#brandwiseSalesFootSalesValue').html(json.totals.sales_value);
                } else {
                    var api = this.api();
                    var intVal = function (i) {
                        return typeof i === 'string' ? i.replace(/[\₹,]/g, '').trim() * 1 : typeof i === 'number' ? i : 0;
                    };
                    var pQty = api.column(1, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    var pVal = api.column(2, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    $('#brandwiseSalesFootSoldQty').html(parseFloat(pQty || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 }));
                    $('#brandwiseSalesFootSalesValue').html('₹' + parseFloat(pVal || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                }
            }
        });
    } else {
        $('#brandwiseSalesTable').DataTable().ajax.reload(null, false);
    }
};
function initBrandwiseSalesTable() {
    window.initBrandwiseSalesTable();
}

$(document).ready(function() {
    window.initBrandwiseSalesTable();
});
</script>

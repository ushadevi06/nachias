<div class="card-datatable table-responsive">
    <table class="datatables-products table table-hover align-middle" id="brandwiseSalesTable" style="width: 100%;">
        <thead class="table-light">
            <tr>
                <th class="text-start">BRAND</th>
                <th class="text-center">SOLD QTY</th>
                <th class="text-end">WITHOUT TAX (₹)</th>
                <th class="text-end">TAXABLE AMOUNT (₹)</th>
                <th class="text-end">TAX AMOUNT (₹)</th>
                <th class="text-end">ROUND OFF (₹)</th>
                <th class="text-end">TOTAL AMOUNT (₹)</th>
                <th class="text-center">TREND</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot class="table-light border-top" id="brandwiseSalesTfoot">
            <tr id="brandwiseSalesTfootTr" class="table-secondary fw-bold">
                <th class="fw-bold text-start">TOTAL</th>
                <th class="text-center fw-bold text-primary" id="brandwiseSalesFootSoldQty">-</th>
                <th class="text-end fw-bold text-dark" id="brandwiseSalesFootWithoutTax">-</th>
                <th class="text-end fw-bold text-info" id="brandwiseSalesFootTaxable">-</th>
                <th class="text-end fw-bold text-danger" id="brandwiseSalesFootTax">-</th>
                <th class="text-end fw-bold text-muted" id="brandwiseSalesFootRoundOff">-</th>
                <th class="text-end fw-bold text-primary" id="brandwiseSalesFootTotal">-</th>
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
                { data: 'brand', name: 'brand', className: 'text-start', width: '22%' },
                { data: 'sold_qty', name: 'sold_qty', className: 'text-center', width: '9%' },
                { data: 'without_tax_amount', name: 'without_tax_amount', className: 'text-end', width: '12%' },
                { data: 'taxable_amount', name: 'taxable_amount', className: 'text-end text-info fw-semibold', width: '12%' },
                { data: 'tax_amount', name: 'tax_amount', className: 'text-end text-danger fw-semibold', width: '12%' },
                { data: 'round_off_amount', name: 'round_off_amount', className: 'text-end text-muted fw-semibold', width: '10%' },
                { data: 'total_amount', name: 'total_amount', className: 'text-end fw-bold text-primary', width: '13%' },
                { data: 'trend', name: 'trend', className: 'text-center', orderable: false, width: '10%' }
            ],
            dom: '<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>rt<"row mt-3"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10,
            drawCallback: function (settings) {
                var json = settings.json;
                if (json && json.totals) {
                    $('#brandwiseSalesFootSoldQty').html(json.totals.sold_qty);
                    $('#brandwiseSalesFootWithoutTax').html(json.totals.without_tax_amount);
                    $('#brandwiseSalesFootTaxable').html(json.totals.taxable_amount);
                    $('#brandwiseSalesFootTax').html(json.totals.tax_amount);
                    $('#brandwiseSalesFootRoundOff').html(json.totals.round_off_amount);
                    $('#brandwiseSalesFootTotal').html(json.totals.total_amount);
                } else {
                    var api = this.api();
                    var intVal = function (i) {
                        return typeof i === 'string' ? i.replace(/[\₹,]/g, '').trim() * 1 : typeof i === 'number' ? i : 0;
                    };
                    var pQty = api.column(1, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    var pWithoutTax = api.column(2, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    var pTaxable = api.column(3, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    var pTax = api.column(4, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    var pRoundOff = api.column(5, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                    var pTotal = api.column(6, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);

                    $('#brandwiseSalesFootSoldQty').html(parseFloat(pQty || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 }));
                    $('#brandwiseSalesFootWithoutTax').html('₹' + parseFloat(pWithoutTax || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    $('#brandwiseSalesFootTaxable').html('₹' + parseFloat(pTaxable || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    $('#brandwiseSalesFootTax').html('₹' + parseFloat(pTax || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    $('#brandwiseSalesFootRoundOff').html('₹' + parseFloat(pRoundOff || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    $('#brandwiseSalesFootTotal').html('₹' + parseFloat(pTotal || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
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

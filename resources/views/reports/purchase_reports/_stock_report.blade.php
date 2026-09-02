<div class="table-responsive">
    <table class="table premium-table mb-0 datatables-stock">
        @if($isFabric)
        <thead>
            <tr>
                <th>#</th>
                <th class="text-start ps-3">PRODUCT GROUP</th>
                <th>WIDTH</th>
                <th>PLAIN METERS</th>
                <th>PRINT METERS</th>
                <th>CHECKED METERS</th>
                <th>OPENING METERS</th>
                <th>INWARD METERS</th>
                <th>OUTWARD METERS</th>
                <th>CLOSING METERS</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="fw-bold" style="background: #f1f5f9;">
                <td colspan="6" class="text-end">TOTAL</td>
                <td id="footer-stock-opening">0.00</td>
                <td id="footer-stock-inward" class="text-success">0.00</td>
                <td id="footer-stock-outward" class="text-danger">0.00</td>
                <td id="footer-stock-closing">0.00</td>
            </tr>
        </tfoot>
        @else
        <thead>
            <tr>
                <th>#</th>
                <th>ITEM NAME</th>
                <th>OPENING QTY</th>
                <th>INWARD QTY</th>
                <th>OUTWARD QTY</th>
                <th>CLOSING QTY</th>
                <th>CLOSING COST (₹)</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="fw-bold" style="background: #f1f5f9;">
                <td colspan="2" class="text-end">TOTAL</td>
                <td id="footer-acc-opening">0.00</td>
                <td id="footer-acc-inward" class="text-success">0.00</td>
                <td id="footer-acc-outward" class="text-danger">0.00</td>
                <td id="footer-acc-closing">0.00</td>
                <td id="footer-acc-cost" class="text-primary">₹ 0.00</td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

<script>
    $(document).ready(function() {
        const $table = $('.datatables-stock');
        if (!$table.length || !$.fn.DataTable) return;

        if ($.fn.DataTable.isDataTable($table)) {
            $table.DataTable().clear().destroy();
        }

        const isFabric = {{ $isFabric ? 'true' : 'false' }};
        const columns = isFabric ? [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'brand', className: 'text-start ps-3 fw-bold' },
            { data: 'width' },
            { data: 'plain' },
            { data: 'print' },
            { data: 'checked' },
            { data: 'opening' },
            { data: 'inward', className: 'text-success fw-semibold' },
            { data: 'outward', className: 'text-danger fw-semibold' },
            { data: 'closing', className: 'fw-bold' }
        ] : [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'item_name' },
            { data: 'opening' },
            { data: 'inward', className: 'text-success fw-semibold' },
            { data: 'outward', className: 'text-danger fw-semibold' },
            { data: 'closing', className: 'fw-bold' },
            { data: 'closing_cost', className: 'fw-bold text-primary' }
        ];

        $table.DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            pageLength: 10,
            bLengthChange: true,
            bFilter: true,
            bInfo: true,
            bAutoWidth: false,
            ajax: {
                url: window.location.pathname,
                data: function(d) {
                    d.report_type = 'stock-report';
                    d.from_date = $('.start_date').val();
                    d.to_date = $('.end_date').val();
                    d.supplier_id = $('select[name="supplier_id"]').val();
                }
            },
            columns: columns,
            drawCallback: function(settings) {
                var json = settings.json;
                if (json && json.totals) {
                    if (isFabric) {
                        $('#footer-stock-opening').html(json.totals.opening);
                        $('#footer-stock-inward').html(json.totals.inward);
                        $('#footer-stock-outward').html(json.totals.outward);
                        $('#footer-stock-closing').html(json.totals.closing);
                    } else {
                        $('#footer-acc-opening').html(json.totals.opening);
                        $('#footer-acc-inward').html(json.totals.inward);
                        $('#footer-acc-outward').html(json.totals.outward);
                        $('#footer-acc-closing').html(json.totals.closing);
                        $('#footer-acc-cost').html(json.totals.closing_cost);
                    }
                }
            },
            language: {
                emptyTable: 'No stock data found.'
            }
        });
    });
</script>
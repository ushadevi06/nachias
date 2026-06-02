<div class="table-responsive">
    <table class="table premium-table datatables-casino-po mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th class="text-start ps-3">PRODUCT GROUP</th>
                <th>WIDTH</th>
                <th>PLAIN METERS</th>
                <th>PRINT METERS</th>
                <th>CHECKED METERS</th>
                <th>STRIPED METERS</th>
                <th>TOTAL METERS</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="fw-bold" style="background: #f1f5f9;">
                <td colspan="3" class="text-end">TOTAL</td>
                <td id="footer-casino-plain">0.00</td>
                <td id="footer-casino-print">0.00</td>
                <td id="footer-casino-checked">0.00</td>
                <td id="footer-casino-striped">0.00</td>
                <td id="footer-casino-total">0.00</td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
    (function() {
        const $table = $('.datatables-casino-po');
        if (!$table.length || !$.fn.DataTable) return;

        if ($.fn.DataTable.isDataTable($table)) {
            $table.DataTable().clear().destroy();
        }

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
                    d.report_type = 'casino-po-report';
                    d.from_date = $('.start_date').val();
                    d.to_date = $('.end_date').val();
                    d.supplier_id = $('select[name="supplier_id"]').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'brand_name', className: 'text-start ps-3 fw-medium text-dark' },
                { data: 'width' },
                { data: 'plain' },
                { data: 'print' },
                { data: 'checked' },
                { data: 'striped' },
                { data: 'total', className: 'fw-bold' }
            ],
            drawCallback: function(settings) {
                var json = settings.json;
                if (json && json.totals) {
                    $('#footer-casino-plain').html(json.totals.plain);
                    $('#footer-casino-print').html(json.totals.print);
                    $('#footer-casino-checked').html(json.totals.checked);
                    $('#footer-casino-striped').html(json.totals.striped);
                    $('#footer-casino-total').html(json.totals.total);
                }
            },
            language: {
                emptyTable: 'No casino purchase orders found.'
            }
        });
    })();
</script>

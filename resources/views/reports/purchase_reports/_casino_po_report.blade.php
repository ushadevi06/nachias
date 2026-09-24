<div class="table-responsive">
    <table class="table premium-table datatables-casino-po mb-0">
        <thead>
            <tr>
                <th style="width: 45px;" class="text-center">#</th>
                <th class="text-start ps-3">PRODUCT GROUP</th>
                <th class="text-center">WIDTH</th>
                <th class="text-end">PLAIN METERS</th>
                <th class="text-end">WHITE METERS</th>
                <th class="text-end">PRINT METERS</th>
                <th class="text-end">CHECKED METERS</th>
                <th class="text-end">STRIPED METERS</th>
                <th class="text-end">TOTAL METERS</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="fw-bold" style="background: #f1f5f9;">
                <td colspan="3" class="text-end">TOTAL</td>
                <td id="footer-casino-plain" class="text-end">0.00</td>
                <td id="footer-casino-white" class="text-end">0.00</td>
                <td id="footer-casino-print" class="text-end">0.00</td>
                <td id="footer-casino-checked" class="text-end">0.00</td>
                <td id="footer-casino-striped" class="text-end">0.00</td>
                <td id="footer-casino-total" class="text-end">0.00</td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
    $(document).ready(function() {
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
                    d.brand_id = $('select[name="brand_id"]').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'brand_name', className: 'text-start ps-3 fw-medium text-dark' },
                { data: 'width', className: 'text-center' },
                { data: 'plain', className: 'text-end' },
                { data: 'white', className: 'text-end' },
                { data: 'print', className: 'text-end' },
                { data: 'checked', className: 'text-end' },
                { data: 'striped', className: 'text-end' },
                { data: 'total', className: 'text-end fw-bold text-dark' }
            ],
            drawCallback: function(settings) {
                var json = settings.json;
                if (json && json.totals) {
                    $('#footer-casino-plain').html(json.totals.plain);
                    $('#footer-casino-white').html(json.totals.white);
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
    });
</script>

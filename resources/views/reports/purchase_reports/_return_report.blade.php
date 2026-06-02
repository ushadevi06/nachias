<div class="table-responsive">
    <table class="table premium-table mb-0 datatables-return" style="width:100%">
        <thead>
            <tr>
                <th>#</th>
                <th>RETURN DATE</th>
                <th>DEBIT NOTE NO</th>
                <th>SUPPLIER NAME</th>
                <th>ITEM NAME</th>
                <th>RETURNED QTY</th>
                <th>RATE</th>
                <th>TOTAL AMOUNT</th>
                <th>REASON</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="fw-bold" style="background: #f1f5f9;">
                <td colspan="5" class="text-end">TOTAL</td>
                <td id="footer-return-qty">0.00</td>
                <td></td>
                <td id="footer-return-amount">0.00</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
    (function() {
        const $table = $('.datatables-return');
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
                    d.report_type = 'return-report';
                    d.from_date = $('.start_date').val();
                    d.to_date = $('.end_date').val();
                    d.supplier_id = $('select[name="supplier_id"]').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'return_date' },
                { data: 'return_no' },
                { data: 'supplier_name' },
                { data: 'item_name' },
                { data: 'quantity' },
                { data: 'rate' },
                { data: 'amount' },
                { data: 'reason' }
            ],
            drawCallback: function(settings) {
                var json = settings.json;
                if (json && json.totals) {
                    $('#footer-return-qty').html(json.totals.quantity);
                    $('#footer-return-amount').html(json.totals.amount);
                }
            },
            language: {
                emptyTable: 'No return goods found.'
            }
        });
    })();
</script>

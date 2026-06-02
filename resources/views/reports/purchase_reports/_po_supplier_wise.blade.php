<div class="table-responsive">
    <table class="table premium-table datatables-po-supplier mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>PO NO</th>
                <th>PO DATE</th>
                <th>SUPPLIER</th>
                <th>ORDER {{ strtoupper($qtyLabel ?? 'QTY') }}</th>
                <th>RECEIVED {{ strtoupper($qtyLabel ?? 'QTY') }}</th>
                <th>PENDING {{ strtoupper($qtyLabel ?? 'QTY') }}</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="fw-bold" style="background: #f1f5f9;">
                <td colspan="4" class="text-end">TOTAL</td>
                <td id="footer-po-ordered">0.00</td>
                <td id="footer-po-received">0.00</td>
                <td id="footer-po-pending">0.00</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
    (function() {
        const $table = $('.datatables-po-supplier');
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
                    d.report_type = 'po-report';
                    d.from_date = $('.start_date').val();
                    d.to_date = $('.end_date').val();
                    d.supplier_id = $('select[name="supplier_id"]').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'po_number' },
                { data: 'po_date' },
                { data: 'supplier_name' },
                { data: 'total_ordered', className: 'text-end' },
                { data: 'total_received', className: 'text-end' },
                { data: 'total_pending', className: 'text-end' },
                { data: 'status', orderable: false, searchable: false }
            ],
            drawCallback: function(settings) {
                var json = settings.json;
                if (json && json.totals) {
                    $('#footer-po-ordered').html(json.totals.total_ordered);
                    $('#footer-po-received').html(json.totals.total_received);
                    $('#footer-po-pending').html(json.totals.total_pending);
                }
            },
            language: {
                emptyTable: 'No purchase orders found.'
            }
        });
    })();
</script>

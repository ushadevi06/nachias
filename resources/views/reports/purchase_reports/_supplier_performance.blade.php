<div class="table-responsive">
    <table class="table premium-table mb-0 datatables-performance" style="width:100%">
        <thead>
            <tr>
                <th>#</th>
                <th>SUPPLIER NAME</th>
                <th>PO COUNT</th>
                <th>TOTAL PO VALUE</th>
                <th>DEBIT NOTE COUNT</th>
                <th>RETURN RATE (%)</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="fw-bold" style="background: #f1f5f9;">
                <td colspan="2" class="text-end">TOTAL</td>
                <td id="footer-perf-po-count" class="text-center">0</td>
                <td id="footer-perf-po-val" class="text-end text-primary">₹ 0.00</td>
                <td id="footer-perf-dn-count" class="text-center">0</td>
                <td id="footer-perf-return-rate" class="text-center text-danger">0.00%</td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
    $(document).ready(function() {
        const $table = $('.datatables-performance');
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
                    d.report_type = 'performance-report';
                    d.from_date = $('.start_date').val();
                    d.to_date = $('.end_date').val();
                    d.supplier_id = $('select[name="supplier_id"]').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'supplier_name' },
                { data: 'po_count', className: 'text-center' },
                { data: 'total_po_value', className: 'text-end fw-semibold' },
                { data: 'dn_count', className: 'text-center' },
                { data: 'return_rate', className: 'text-center', orderable: false, searchable: false }
            ],
            drawCallback: function(settings) {
                var json = settings.json;
                if (json && json.totals) {
                    $('#footer-perf-po-count').text(json.totals.po_count || '0');
                    $('#footer-perf-po-val').text(json.totals.total_po_value || '₹ 0.00');
                    $('#footer-perf-dn-count').text(json.totals.dn_count || '0');
                    $('#footer-perf-return-rate').text(json.totals.return_rate || '0.00%');
                }
            },
            language: {
                emptyTable: 'No supplier performance data found.'
            }
        });
    });
</script>

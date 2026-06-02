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
    </table>
</div>

<script>
    (function() {
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
            language: {
                emptyTable: 'No supplier performance data found.'
            }
        });
    })();
</script>

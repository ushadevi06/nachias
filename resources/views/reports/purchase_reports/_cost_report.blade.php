<div class="table-responsive">
    <table class="table premium-table mb-0 datatables-cost">
        <thead>
            <tr>
                <th>#</th>
                <th>ITEM NAME</th>
                <th class="text-center">TOTAL PURCHASED QTY</th>
                <th class="text-end">TOTAL AMOUNT (₹)</th>
                <th class="text-end text-primary">AVERAGE COST (₹)</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        const $table = $('.datatables-cost');
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
                    d.report_type = 'cost-report';
                    d.from_date = $('.start_date').val();
                    d.to_date = $('.end_date').val();
                    d.supplier_id = $('select[name="supplier_id"]').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'item_name' },
                { data: 'total_qty', className: 'text-center' },
                { data: 'total_amount', className: 'text-end' },
                { data: 'average_cost', className: 'text-end text-primary fw-bold' }
            ],
            language: {
                emptyTable: 'No data available in table'
            }
        });
    });
</script>

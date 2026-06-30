<div class="table-responsive">
    <table class="table premium-table mb-0 datatables-consumption">
        <thead>
            <tr>
                <th>#</th>
                <th>DATE</th>
                <th>JOB CARD NO</th>
                <th>BRAND</th>
                <th class="text-center">TOTAL GARMENTS (QTY)</th>
                <th class="text-center">FABRIC USED (MTR)</th>
                <th class="text-center">AVG CONSUMPTION (MTR/PC)</th>
                <th class="text-center">STATUS</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="bg-light fw-bold">
                <td colspan="4" class="text-end">TOTAL</td>
                <td id="footer-consumption-garments" class="text-center text-primary">0.00</td>
                <td id="footer-consumption-fabric" class="text-center text-primary">0.00</td>
                <td id="footer-consumption-avg" class="text-center text-primary">0.000</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
    (function() {
        const $table = $('.datatables-consumption');
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
                    d.report_type = 'consumption-report';
                    d.from_date = $('.start_date').val();
                    d.to_date = $('.end_date').val();
                    d.supplier_id = $('select[name="supplier_id"]').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'date' },
                { data: 'job_card_no', className: 'fw-bold text-primary' },
                { data: 'brand' },
                { data: 'total_garments', className: 'text-center' },
                { data: 'total_fabric', className: 'text-center' },
                { data: 'average', className: 'text-center fw-bold' },
                { data: 'status', className: 'text-center', orderable: false, searchable: false }
            ],
            drawCallback: function(settings) {
                var json = settings.json;
                if (json && json.totals) {
                    var garments = parseFloat(json.totals.total_garments.replace(/,/g, ''));
                    var fabric = parseFloat(json.totals.total_fabric.replace(/,/g, ''));
                    var avg = garments > 0 ? (fabric / garments).toFixed(3) : '0.000';

                    $('#footer-consumption-garments').html(json.totals.total_garments);
                    $('#footer-consumption-fabric').html(json.totals.total_fabric);
                    $('#footer-consumption-avg').html(avg);
                }
            },
            language: {
                emptyTable: 'No data available in table'
            }
        });
    })();
</script>

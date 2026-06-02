<div class="table-responsive">
    <table class="table premium-table mb-0 datatables-ageing">
        @if($isFabric)
        <thead>
            <tr>
                <th>#</th>
                <th>MATERIAL NAME</th>
                <th>BRAND</th>
                <th>STYLE</th>
                <th>COLOR</th>
                <th>FABRIC TYPE</th>
                <th>WIDTH</th>
                <th>0-30 DAYS</th>
                <th>31-60 DAYS</th>
                <th>61-90 DAYS</th>
                <th>91+ DAYS</th>
                <th>TOTAL STOCK</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="bg-light fw-bold">
                <td colspan="7" class="text-end">TOTAL</td>
                <td id="footer-ageing-0-30" class="text-center">0.00</td>
                <td id="footer-ageing-31-60" class="text-center">0.00</td>
                <td id="footer-ageing-61-90" class="text-center">0.00</td>
                <td id="footer-ageing-91-plus" class="text-center">0.00</td>
                <td id="footer-ageing-total" class="text-center text-primary">0.00</td>
            </tr>
        </tfoot>
        @else
        <thead>
            <tr>
                <th>#</th>
                <th>ITEM NAME</th>
                <th>0-30 DAYS</th>
                <th>31-60 DAYS</th>
                <th>61-90 DAYS</th>
                <th>91+ DAYS</th>
                <th>TOTAL STOCK</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="bg-light fw-bold">
                <td colspan="2" class="text-end">TOTAL</td>
                <td id="footer-ageing-acc-0-30" class="text-center">0.00</td>
                <td id="footer-ageing-acc-31-60" class="text-center">0.00</td>
                <td id="footer-ageing-acc-61-90" class="text-center">0.00</td>
                <td id="footer-ageing-acc-91-plus" class="text-center">0.00</td>
                <td id="footer-ageing-acc-total" class="text-center text-primary">0.00</td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

<script>
    (function() {
        const $table = $('.datatables-ageing');
        if (!$table.length || !$.fn.DataTable) return;

        if ($.fn.DataTable.isDataTable($table)) {
            $table.DataTable().clear().destroy();
        }

        const isFabric = {{ $isFabric ? 'true' : 'false' }};
        const columns = isFabric ? [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'item_name' },
            { data: 'brand' },
            { data: 'style' },
            { data: 'color' },
            { data: 'fabric_type' },
            { data: 'width' },
            { data: 'age_0_30', className: 'text-center' },
            { data: 'age_31_60', className: 'text-center' },
            { data: 'age_61_90', className: 'text-center' },
            { data: 'age_91_plus', className: 'text-center' },
            { data: 'total', className: 'text-center fw-bold text-primary' }
        ] : [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'item_name' },
            { data: 'age_0_30', className: 'text-center' },
            { data: 'age_31_60', className: 'text-center' },
            { data: 'age_61_90', className: 'text-center' },
            { data: 'age_91_plus', className: 'text-center' },
            { data: 'total', className: 'text-center fw-bold text-primary' }
        ];

        $table.DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            pageLength: 25,
            bLengthChange: true,
            bFilter: true,
            bInfo: true,
            bAutoWidth: false,
            ajax: {
                url: window.location.pathname,
                data: function(d) {
                    d.report_type = 'ageing-report';
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
                        $('#footer-ageing-0-30').html(json.totals['0_30']);
                        $('#footer-ageing-31-60').html(json.totals['31_60']);
                        $('#footer-ageing-61-90').html(json.totals['61_90']);
                        $('#footer-ageing-91-plus').html(json.totals['91_plus']);
                        $('#footer-ageing-total').html(json.totals.total);
                    } else {
                        $('#footer-ageing-acc-0-30').html(json.totals['0_30']);
                        $('#footer-ageing-acc-31-60').html(json.totals['31_60']);
                        $('#footer-ageing-acc-61-90').html(json.totals['61_90']);
                        $('#footer-ageing-acc-91-plus').html(json.totals['91_plus']);
                        $('#footer-ageing-acc-total').html(json.totals.total);
                    }
                }
            },
            language: {
                emptyTable: 'No data available in table'
            }
        });
    })();
</script>

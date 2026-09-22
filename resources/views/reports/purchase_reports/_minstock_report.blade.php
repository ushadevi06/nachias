<div class="table-responsive">
    <table class="table premium-table mb-0 datatables-minstock">
        @if($isFabric)
        <thead>
            <tr>
                <th>#</th>
                <th>ART NO</th>
                <th>MATERIAL NAME</th>
                <th>BRAND</th>
                <th>STYLE</th>
                <th>COLOR</th>
                <th>FABRIC TYPE</th>
                <th>WIDTH</th>
                <th class="text-center">MIN STOCK REQ.</th>
                <th class="text-center">CURRENT STOCK</th>
                <th class="text-center">SHORTAGE</th>
                <th class="text-center">STATUS</th>
            </tr>
        </thead>
        @else
        <thead>
            <tr>
                <th>#</th>
                <th>ITEM NAME</th>
                <th class="text-center">MIN STOCK REQ.</th>
                <th class="text-center">CURRENT STOCK</th>
                <th class="text-center">SHORTAGE</th>
                <th class="text-center">STATUS</th>
            </tr>
        </thead>
        @endif
        @if($isFabric)
        <tfoot>
            <tr class="fw-bold" style="background: #f1f5f9;">
                <td colspan="8" class="text-end">TOTAL</td>
                <td id="footer-minstock-min" class="text-center fw-bold">0.00</td>
                <td id="footer-minstock-current" class="text-center fw-bold">0.00</td>
                <td id="footer-minstock-shortage" class="text-center fw-bold text-danger">0.00</td>
                <td></td>
            </tr>
        </tfoot>
        @else
        <tfoot>
            <tr class="fw-bold" style="background: #f1f5f9;">
                <td colspan="2" class="text-end">TOTAL</td>
                <td id="footer-minstock-acc-min" class="text-center fw-bold">0.00</td>
                <td id="footer-minstock-acc-current" class="text-center fw-bold">0.00</td>
                <td id="footer-minstock-acc-shortage" class="text-center fw-bold text-danger">0.00</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
        <tbody></tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        const $table = $('.datatables-minstock');
        if (!$table.length || !$.fn.DataTable) return;

        if ($.fn.DataTable.isDataTable($table)) {
            $table.DataTable().clear().destroy();
        }

        const isFabric = {{ $isFabric ? 'true' : 'false' }};
        const columns = isFabric ? [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'art_no' },
            { data: 'item_name' },
            { data: 'brand' },
            { data: 'style' },
            { data: 'color' },
            { data: 'fabric_type' },
            { data: 'width' },
            { data: 'min_stock', className: 'text-center fw-bold text-dark' },
            { data: 'closing', className: 'text-center fw-bold' },
            { data: 'shortage', className: 'text-center fw-bold text-danger' },
            { data: 'status', className: 'text-center', orderable: false, searchable: false }
        ] : [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'item_name' },
            { data: 'min_stock', className: 'text-center fw-bold text-dark' },
            { data: 'closing', className: 'text-center fw-bold' },
            { data: 'shortage', className: 'text-center fw-bold text-danger' },
            { data: 'status', className: 'text-center', orderable: false, searchable: false }
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
                    d.report_type = 'minstock-report';
                    d.from_date = $('.start_date').val();
                    d.to_date = $('.end_date').val();
                    d.supplier_id = $('select[name="supplier_id"]').val();
                    d.art_no = $('select[name="art_no"]').val();
                    d.brand_id = $('select[name="brand_id"]').val();
                }
            },
            columns: columns,
            drawCallback: function(settings) {
                var json = settings.json;
                if (json && json.totals) {
                    if (isFabric) {
                        $('#footer-minstock-min').text(json.totals.min_stock || '0.00');
                        $('#footer-minstock-current').text(json.totals.closing || '0.00');
                        $('#footer-minstock-shortage').text(json.totals.shortage || '0.00');
                    } else {
                        $('#footer-minstock-acc-min').text(json.totals.min_stock || '0.00');
                        $('#footer-minstock-acc-current').text(json.totals.closing || '0.00');
                        $('#footer-minstock-acc-shortage').text(json.totals.shortage || '0.00');
                    }
                }
            },
            language: {
                emptyTable: 'No data available in table'
            }
        });
    });
</script>

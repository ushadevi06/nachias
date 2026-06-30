<div class="table-responsive">
    <table class="table premium-table mb-0 datatables-minstock">
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
        <tbody></tbody>
    </table>
</div>

<script>
    (function() {
        const $table = $('.datatables-minstock');
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
                }
            },
            columns: columns,
            language: {
                emptyTable: 'No data available in table'
            }
        });
    })();
</script>

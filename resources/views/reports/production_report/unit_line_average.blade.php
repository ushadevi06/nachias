<div class="unit-line-average-wrapper">
    <!-- Top Title Banner -->
    <div class="unit-report-banner mb-3 p-3 rounded-3 text-center border bg-light">
        <h5 class="mb-1 fw-bold text-dark text-uppercase" id="unitLineReportHeaderTitle" style="letter-spacing: 0.5px;">
            UNIT AVERAGE REPORT
        </h5>
        <div class="small fw-semibold text-muted">
            TARGET: <span id="unitLineTargetBadge" class="fw-bold text-dark">-</span>
        </div>
    </div>

    <!-- Main Unit Line Table -->
    <div class="card-datatable table-responsive">
        <table class="datatables-products table table-hover align-middle text-nowrap" id="unitLineAverageTable" style="width: 100%;">
            <thead class="table-light text-center align-middle">
                <tr>
                    <th class="fw-bold text-dark" style="width: 8%;">DATE</th>
                    <th class="fw-bold text-dark" style="width: 7%;">N.PATTI</th>
                    <th class="fw-bold text-dark" style="width: 8%;">BACK SHOULDER</th>
                    <th class="fw-bold text-dark" style="width: 7%;">SLEEVE</th>
                    <th class="fw-bold text-dark" style="width: 7%;">COLLAR</th>
                    <th class="fw-bold text-dark" style="width: 7%;">CUFF</th>
                    <th class="fw-bold text-dark" style="width: 8%;">ASSEMBLE</th>
                    <th class="fw-bold text-dark" style="width: 6%;">KAJA</th>
                    <th class="fw-bold text-dark" style="width: 6%;">BUTTON</th>
                    <th class="fw-bold text-dark" style="width: 7%;">TRIMMING</th>
                    <th class="fw-bold text-dark" style="width: 7%;">CHECKING</th>
                    <th class="fw-bold text-dark" style="width: 8%;">H.O DELIVER</th>
                    <th class="fw-bold text-dark" style="width: 7%;">EFFICIENCY</th>
                    <th class="fw-bold text-dark" style="width: 7%;">1 HR OT</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot class="table-light fw-bold align-middle">
                <!-- Store Stock Row -->
                <tr class="table-light border-top" id="unitLineStoreStockRow">
                    <th colspan="11" class="text-center fw-bold text-dark">Store Stock</th>
                    <th class="text-center" id="store_stock_deliver">-</th>
                    <th class="text-center" id="store_stock_efficiency">-</th>
                    <th class="text-center">-</th>
                </tr>

                <!-- TOTAL Row -->
                <tr class="table-light" id="unitLineFooterTotalRow">
                    <th class="text-center fw-bold text-dark">Total</th>
                    <th class="text-center" id="foot_line_n_patti">-</th>
                    <th class="text-center" id="foot_line_back_shoulder">-</th>
                    <th class="text-center" id="foot_line_sleeve">-</th>
                    <th class="text-center" id="foot_line_collar">-</th>
                    <th class="text-center" id="foot_line_cuff">-</th>
                    <th class="text-center" id="foot_line_assemble">-</th>
                    <th class="text-center" id="foot_line_kaja">-</th>
                    <th class="text-center" id="foot_line_button">-</th>
                    <th class="text-center" id="foot_line_trimming">-</th>
                    <th class="text-center" id="foot_line_checking">-</th>
                    <th class="text-center" id="foot_line_ho_deliver">-</th>
                    <th class="text-center" id="foot_line_efficiency">-</th>
                    <th class="text-center">-</th>
                </tr>

                <!-- AVERAGE Row -->
                <tr class="table-light" id="unitLineFooterAvgRow">
                    <th class="text-center fw-bold text-dark">AVG</th>
                    <th class="text-center" id="avg_line_n_patti">-</th>
                    <th class="text-center" id="avg_line_back_shoulder">-</th>
                    <th class="text-center" id="avg_line_sleeve">-</th>
                    <th class="text-center" id="avg_line_collar">-</th>
                    <th class="text-center" id="avg_line_cuff">-</th>
                    <th class="text-center" id="avg_line_assemble">-</th>
                    <th class="text-center" id="avg_line_kaja">-</th>
                    <th class="text-center" id="avg_line_button">-</th>
                    <th class="text-center" id="avg_line_trimming">-</th>
                    <th class="text-center" id="avg_line_checking">-</th>
                    <th class="text-center" id="avg_line_ho_deliver">-</th>
                    <th class="text-center">-</th>
                    <th class="text-center">-</th>
                </tr>

                <!-- TARGETTED AVERAGE Row -->
                <tr class="table-light" id="unitLineTargetedAvgRow">
                    <th colspan="10" class="text-center fw-bold text-dark">TARGETTED AVERAGE BASED ON OT (9 TO 7)</th>
                    <th class="text-center" colspan="2" id="bottom_targeted_avg">-</th>
                    <th class="text-center" colspan="2">-</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<style>
#unitLineAverageTable {
    table-layout: fixed !important;
    width: 100% !important;
}
#unitLineAverageTable thead th {
    font-size: 0.8rem;
    letter-spacing: 0.2px;
    vertical-align: middle !important;
}
#unitLineAverageTable tfoot th {
    font-size: 0.84rem;
}
</style>

<script>
window.unitLineAverageDt = null;

window.initUnitLineAverageTable = function() {
    if ($.fn.DataTable.isDataTable('#unitLineAverageTable')) {
        $('#unitLineAverageTable').DataTable().destroy();
    }

    window.unitLineAverageDt = $('#unitLineAverageTable').DataTable({
        autoWidth: false,
        processing: true,
        serverSide: true,
        paging: true,
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50, 100, -1], [10, 15, 25, 50, 100, "All"]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>B',
        buttons: [
            {
                extend: 'excelHtml5',
                className: 'buttons-excel d-none',
                title: function() {
                    return $('#unitLineReportHeaderTitle').text().trim();
                },
                footer: true,
                exportOptions: {
                    columns: ':visible'
                },
                action: serverSideExportAction
            },
            {
                extend: 'pdfHtml5',
                className: 'buttons-pdf d-none',
                orientation: 'landscape',
                pageSize: 'A4',
                title: function() {
                    return $('#unitLineReportHeaderTitle').text().trim();
                },
                footer: true,
                exportOptions: {
                    columns: ':visible'
                },
                action: serverSideExportAction
            },
            {
                extend: 'print',
                className: 'buttons-print d-none',
                title: function() {
                    return '<h3 style="text-align:center; margin-bottom:10px;">' + $('#unitLineReportHeaderTitle').text().trim() + '</h3>';
                },
                footer: true,
                exportOptions: {
                    columns: ':visible'
                },
                action: serverSideExportAction
            }
        ],
        ajax: {
            url: "{{ url('production_reports/ajax/unit-line-average') }}",
            type: 'GET',
            data: function(d) {
                d.from_date = $('.start_date').val();
                d.to_date = $('.end_date').val();
                d.unit_id = $('select[name="unit_id"]').val();
                d.brand_id = $('select[name="brand_id"]').val();
            }
        },
        columns: [
            { data: 'date', className: 'text-center text-nowrap fw-semibold', width: '8%' },
            { data: 'n_patti', className: 'text-center', width: '7%' },
            { data: 'back_shoulder', className: 'text-center', width: '8%' },
            { data: 'sleeve', className: 'text-center', width: '7%' },
            { data: 'collar', className: 'text-center', width: '7%' },
            { data: 'cuff', className: 'text-center', width: '7%' },
            { data: 'assemble', className: 'text-center', width: '8%' },
            { data: 'kaja', className: 'text-center', width: '6%' },
            { data: 'button', className: 'text-center', width: '6%' },
            { data: 'trimming', className: 'text-center', width: '7%' },
            { data: 'checking', className: 'text-center', width: '7%' },
            { data: 'ho_deliver', className: 'text-center', width: '8%' },
            { data: 'efficiency', className: 'text-center', width: '7%' },
            { data: 'ot', className: 'text-center', width: '7%' }
        ],
        drawCallback: function(settings) {
            var json = settings.json;
            if (json && json.meta) {
                var m = json.meta;
                if (m.report_title) {
                    $('#unitLineReportHeaderTitle').text(m.report_title);
                    $('#active_report_title').text('🏭 ' + m.report_title);
                }
                if (m.target_qty) {
                    $('#unitLineTargetBadge').text(m.target_qty + ' PCS');
                    $('#bottom_targeted_avg').text(m.target_qty);
                }

                if (m.store_stock_row) {
                    var ss = m.store_stock_row;
                    $('#store_stock_deliver').text(ss.ho_deliver);
                    $('#store_stock_efficiency').text(ss.efficiency);
                }

                if (m.total_row) {
                    var t = m.total_row;
                    $('#foot_line_n_patti').text(t.n_patti);
                    $('#foot_line_back_shoulder').text(t.back_shoulder);
                    $('#foot_line_sleeve').text(t.sleeve);
                    $('#foot_line_collar').text(t.collar);
                    $('#foot_line_cuff').text(t.cuff);
                    $('#foot_line_assemble').text(t.assemble);
                    $('#foot_line_kaja').text(t.kaja);
                    $('#foot_line_button').text(t.button);
                    $('#foot_line_trimming').text(t.trimming);
                    $('#foot_line_checking').text(t.checking);
                    $('#foot_line_ho_deliver').text(t.ho_deliver);
                    $('#foot_line_efficiency').text(t.efficiency);
                }

                if (m.avg_row) {
                    var a = m.avg_row;
                    $('#avg_line_n_patti').text(a.n_patti);
                    $('#avg_line_back_shoulder').text(a.back_shoulder);
                    $('#avg_line_sleeve').text(a.sleeve);
                    $('#avg_line_collar').text(a.collar);
                    $('#avg_line_cuff').text(a.cuff);
                    $('#avg_line_assemble').text(a.assemble);
                    $('#avg_line_kaja').text(a.kaja);
                    $('#avg_line_button').text(a.button);
                    $('#avg_line_trimming').text(a.trimming);
                    $('#avg_line_checking').text(a.checking);
                    $('#avg_line_ho_deliver').text(a.ho_deliver);
                }
            }
            if (typeof showReportLoading === 'function') {
                showReportLoading(false);
            }
        },
        language: {
            processing: '<div class="d-flex align-items-center justify-content-center py-4 text-primary fw-bold"><div class="spinner-border spinner-border-sm me-2" role="status"></div> Loading report data...</div>',
            emptyTable: '<div class="text-center py-4 text-muted"><i class="ri ri-inbox-line ri-2x mb-2 d-block text-secondary"></i>No unit line production records found for this period</div>'
        }
    });
};
</script>

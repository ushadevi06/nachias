<div class="final-finishing-average-wrapper">
    <!-- Top Title Banner -->
    <div class="finishing-report-banner mb-3 p-3 rounded-3 text-center border bg-light">
        <h5 class="mb-0 fw-bold text-dark text-uppercase" id="finishingReportHeaderTitle" style="letter-spacing: 0.5px;">
            FINAL FINISHING AVERAGE REPORT
        </h5>
    </div>

    <!-- Main Finishing Table -->
    <div class="card-datatable table-responsive">
        <table class="datatables-products table table-hover align-middle text-nowrap" id="finalFinishingAverageTable" style="width: 100%;">
            <thead class="table-light text-center align-middle">
                <tr>
                    <th class="fw-bold text-dark" style="width: 14%;">DATE</th>
                    <th class="fw-bold text-dark" style="width: 14%;">IRONING</th>
                    <th class="fw-bold text-dark" style="width: 14%;">DESPATCH</th>
                    <th class="fw-bold text-dark" style="width: 14%;">DELIVERY</th>
                    <th class="fw-bold text-dark" style="width: 14%;">EFFICIENCY</th>
                    <th class="fw-bold text-dark" style="width: 16%;">TARGET PER DAY</th>
                    <th class="fw-bold text-dark" style="width: 14%;">COUNT</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot class="table-light fw-bold align-middle">
                <!-- TOTAL Row -->
                <tr class="table-light border-top" id="finishingFooterTotalRow">
                    <th class="text-center fw-bold text-dark">TOTAL</th>
                    <th class="text-center" id="foot_finish_ironing">-</th>
                    <th class="text-center" id="foot_finish_despatch">-</th>
                    <th class="text-center" id="foot_finish_delivery">-</th>
                    <th class="text-center" id="foot_finish_efficiency">-</th>
                    <th class="text-center">-</th>
                    <th class="text-center">-</th>
                </tr>
                <!-- AVERAGE Row -->
                <tr class="table-light" id="finishingFooterAvgRow">
                    <th class="text-center fw-bold text-dark">AVERAGE</th>
                    <th class="text-center" id="avg_finish_ironing">-</th>
                    <th class="text-center" id="avg_finish_despatch">-</th>
                    <th class="text-center" id="avg_finish_delivery">-</th>
                    <th class="text-center">-</th>
                    <th class="text-center">-</th>
                    <th class="text-center">-</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<style>
#finalFinishingAverageTable {
    table-layout: fixed !important;
    width: 100% !important;
}
#finalFinishingAverageTable thead th {
    font-size: 0.8rem;
    letter-spacing: 0.2px;
    vertical-align: middle !important;
}
#finalFinishingAverageTable tfoot th {
    font-size: 0.84rem;
}
</style>

<script>
window.finalFinishingAverageDt = null;

window.initFinalFinishingAverageTable = function() {
    var fromDate = $('.start_date').val();
    var toDate = $('.end_date').val();
    var unitId = $('select[name="unit_id"]').val();
    var brandId = $('select[name="brand_id"]').val();

    if ($.fn.DataTable.isDataTable('#finalFinishingAverageTable')) {
        $('#finalFinishingAverageTable').DataTable().destroy();
    }

    window.finalFinishingAverageDt = $('#finalFinishingAverageTable').DataTable({
        autoWidth: false,
        processing: true,
        serverSide: true,
        paging: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>B',
        buttons: [
            {
                extend: 'excelHtml5',
                className: 'buttons-excel d-none',
                title: function() {
                    return $('#finishingReportHeaderTitle').text().trim();
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
                    return $('#finishingReportHeaderTitle').text().trim();
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
                    return '<h3 style="text-align:center; margin-bottom:10px;">' + $('#finishingReportHeaderTitle').text().trim() + '</h3>';
                },
                footer: true,
                exportOptions: {
                    columns: ':visible'
                },
                action: serverSideExportAction
            }
        ],
        ajax: {
            url: "{{ url('production_reports/ajax/final-finishing-average') }}",
            type: 'GET',
            data: function(d) {
                d.from_date = $('.start_date').val();
                d.to_date = $('.end_date').val();
                d.unit_id = $('select[name="unit_id"]').val();
                d.brand_id = $('select[name="brand_id"]').val();
            }
        },
        columns: [
            { data: 'date', className: 'text-center text-nowrap fw-semibold', width: '14%' },
            { data: 'ironing', className: 'text-center', width: '14%' },
            { data: 'despatch', className: 'text-center', width: '14%' },
            { data: 'delivery', className: 'text-center', width: '14%' },
            { data: 'efficiency', className: 'text-center', width: '14%' },
            { data: 'target_per_day', className: 'text-center', width: '16%' },
            { data: 'count', className: 'text-center text-muted', width: '14%' }
        ],
        drawCallback: function(settings) {
            var json = settings.json;
            if (json && json.meta) {
                var m = json.meta;
                if (m.report_title) {
                    $('#finishingReportHeaderTitle').text(m.report_title);
                    $('#active_report_title').text('✨ ' + m.report_title);
                }
                if (m.total_row) {
                    var t = m.total_row;
                    $('#foot_finish_ironing').text(t.ironing);
                    $('#foot_finish_despatch').text(t.despatch);
                    $('#foot_finish_delivery').text(t.delivery);
                    $('#foot_finish_efficiency').text(t.efficiency);
                }
                if (m.average_row) {
                    var a = m.average_row;
                    $('#avg_finish_ironing').text(a.ironing);
                    $('#avg_finish_despatch').text(a.despatch);
                    $('#avg_finish_delivery').text(a.delivery);
                }
            }
            if (typeof showReportLoading === 'function') {
                showReportLoading(false);
            }
        },
        language: {
            processing: '<div class="d-flex align-items-center justify-content-center py-4 text-primary fw-bold"><div class="spinner-border spinner-border-sm me-2" role="status"></div> Loading report data...</div>',
            emptyTable: '<div class="text-center py-4 text-muted"><i class="ri ri-inbox-line ri-2x mb-2 d-block text-secondary"></i>No finishing records found for this period</div>'
        }
    });
};
</script>

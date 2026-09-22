<div class="unit-line-average-wrapper">
    <!-- ==========================================
         MAIN VIEW: Unit Line Average Table
         ========================================== -->
    <div id="unitLineMainContainer">
        <!-- Top Title Banner -->
        <div class="unit-report-banner mb-3 p-3 rounded-3 text-center border bg-light shadow-sm">
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

    <!-- ==========================================
         SUBPAGE VIEW: In-Page Drilldown Breakdown
         ========================================== -->
    <div id="unitLineDrilldownContainer" style="display: none;">
        <!-- Breadcrumb Bar matching design: [ ← BACK TO REPORT ] Unit Line Report > Date > OP -->
        <div class="mb-3 d-flex align-items-center flex-wrap" id="unitLineBreadcrumbs">
            <button type="button" class="btn btn-sm btn-outline-secondary me-3 px-3 fw-semibold text-uppercase btn-back-to-unit-line" style="border-color: #8592a3; color: #566a7f; border-radius: 5px; font-size: 0.78rem; letter-spacing: 0.3px; padding-top: 5px; padding-bottom: 5px;">
                <i class="ri ri-arrow-left-line me-1"></i> Back to Report
            </button>
            <div class="d-flex align-items-center flex-wrap fw-bold" style="font-size: 0.88rem;">
                <span class="text-muted" id="breadcrumbUnitName">Unit Line Report</span>
                <span class="text-muted mx-2 fw-normal" style="font-size: 0.95rem;">&rsaquo;</span>
                <span class="text-muted" id="drilldownBreadcrumbDate">01-09-2026</span>
                <span class="text-muted mx-2 fw-normal" style="font-size: 0.95rem;">&rsaquo;</span>
                <span class="fw-bold text-uppercase" style="color: #6f42c1; letter-spacing: 0.3px;" id="drilldownBreadcrumbOp">N.PATTI</span>
            </div>
        </div>

        <!-- Summary KPI Chips -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card border border-light-subtle shadow-none bg-light p-3 text-center rounded-3">
                    <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Total Quantity</span>
                    <h4 class="mb-0 fw-bold text-success" id="drillTotalQty">0 Pcs</h4>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border border-light-subtle shadow-none bg-light p-3 text-center rounded-3">
                    <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Total Tasks / Entries</span>
                    <h4 class="mb-0 fw-bold text-dark" id="drillTotalTasks">0</h4>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border border-light-subtle shadow-none bg-light p-3 text-center rounded-3">
                    <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Assigned Employees</span>
                    <h4 class="mb-0 fw-bold text-primary" id="drillTotalEmps">0</h4>
                </div>
            </div>
        </div>

        <!-- Drilldown DataTable Card -->
        <div class="card border shadow-sm rounded-3 overflow-hidden mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="ri ri-file-list-3-line me-1 text-primary"></i> Job Card & Task Entries
                </h6>
            </div>
            <div class="card-body py-3">
                <div class="card-datatable table-responsive">
                    <table class="table table-hover align-middle mb-0 text-nowrap" id="unitLineDrilldownTable" style="width: 100%;">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 5%;">#</th>
                                <th style="width: 16%;">Job Card No</th>
                                <th style="width: 14%;">Task No</th>
                                <th style="width: 25%;">Service Name</th>
                                <th style="width: 18%;">Assigned Employee</th>
                                <th class="text-center" style="width: 11%;">Target Qty</th>
                                <th class="text-center" style="width: 11%;">Completed</th>
                                <th class="text-center" style="width: 10%;">Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
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
.unit-line-drilldown-link {
    display: inline-block;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
    border-radius: 6px;
    padding: 2px 6px;
}
.unit-line-drilldown-link:hover {
    background-color: #0d6efd !important;
    color: #ffffff !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(13, 110, 253, 0.25);
}
</style>

<script>
window.unitLineAverageDt = null;
window.unitLineDrilldownDt = null;

// Helper to wrap numbers into clickable drilldown badge links
function renderUnitDrillCell(opKey, opTitle) {
    return function(data, type, row) {
        if (type === 'display' && data && data !== '-' && data !== '0') {
            var rawDate = row.raw_date || row.date;
            return '<span class="unit-line-drilldown-link badge bg-light text-primary border" ' +
                   'data-date="' + rawDate + '" ' +
                   'data-date-display="' + row.date + '" ' +
                   'data-op="' + opKey + '" ' +
                   'data-title="' + opTitle + '" ' +
                   'title="Click to view ' + opTitle + ' breakdown">' + data + '</span>';
        }
        return data;
    };
}

window.initUnitLineAverageTable = function() {
    $('#unitLineDrilldownContainer').hide();
    $('#unitLineMainContainer').show();

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
            { data: 'n_patti', className: 'text-center', width: '7%', render: renderUnitDrillCell('n_patti', 'N.PATTI') },
            { data: 'back_shoulder', className: 'text-center', width: '8%', render: renderUnitDrillCell('back_shoulder', 'BACK SHOULDER') },
            { data: 'sleeve', className: 'text-center', width: '7%', render: renderUnitDrillCell('sleeve', 'SLEEVE') },
            { data: 'collar', className: 'text-center', width: '7%', render: renderUnitDrillCell('collar', 'COLLAR') },
            { data: 'cuff', className: 'text-center', width: '7%', render: renderUnitDrillCell('cuff', 'CUFF') },
            { data: 'assemble', className: 'text-center', width: '8%', render: renderUnitDrillCell('assemble', 'ASSEMBLE') },
            { data: 'kaja', className: 'text-center', width: '6%', render: renderUnitDrillCell('kaja', 'KAJA') },
            { data: 'button', className: 'text-center', width: '6%', render: renderUnitDrillCell('button', 'BUTTON') },
            { data: 'trimming', className: 'text-center', width: '7%', render: renderUnitDrillCell('trimming', 'TRIMMING') },
            { data: 'checking', className: 'text-center', width: '7%', render: renderUnitDrillCell('checking', 'CHECKING') },
            { data: 'ho_deliver', className: 'text-center', width: '8%', render: renderUnitDrillCell('ho_deliver', 'H.O DELIVER') },
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

// Open Subpage Drilldown Function
window.openUnitLineDrilldown = function(date, dateDisplay, op, title) {
    var unitId = $('select[name="unit_id"]').val();
    var brandId = $('select[name="brand_id"]').val();

    // 1. Switch View
    $('#unitLineMainContainer').hide();
    $('#unitLineDrilldownContainer').fadeIn(200);

    // 2. Set Breadcrumb
    var uName = $('select[name="unit_id"] option:selected').text().trim();
    var reportLabel = (uName && uName !== 'Select Unit' && uName !== '') ? uName : 'Unit Line Report';
    $('#breadcrumbUnitName').text(reportLabel);
    $('#drilldownBreadcrumbDate').text(dateDisplay || date);
    $('#drilldownBreadcrumbOp').text(title);

    // Reset KPI Chips
    $('#drillTotalQty').text('Loading...');
    $('#drillTotalTasks').text('Loading...');
    $('#drillTotalEmps').text('Loading...');

    // 3. Clear existing table
    if ($.fn.DataTable.isDataTable('#unitLineDrilldownTable')) {
        $('#unitLineDrilldownTable').DataTable().clear().destroy();
    }
    $('#unitLineDrilldownTable tbody').html('<tr><td colspan="8" class="text-center py-4 text-primary fw-semibold"><div class="spinner-border spinner-border-sm me-2" role="status"></div> Fetching drilldown records...</td></tr>');

    // 4. AJAX Call
    $.ajax({
        url: "{{ url('production_reports/ajax/unit-line-drilldown') }}",
        type: 'GET',
        data: {
            date: date,
            op: op,
            unit_id: unitId,
            brand_id: brandId
        },
        success: function(res) {
            if (res && res.success) {
                // Update KPI Chips
                $('#drillTotalQty').text((res.summary.total_qty || 0) + ' Pcs');
                $('#drillTotalTasks').text(res.summary.total_tasks || 0);
                $('#drillTotalEmps').text(res.summary.total_employees || 0);

                // Build Table Rows
                var tbodyHtml = '';
                if (res.rows && res.rows.length > 0) {
                    res.rows.forEach(function(r) {
                        tbodyHtml += '<tr>' +
                            '<td class="text-center text-muted small">' + r.sno + '</td>' +
                            '<td><span class="badge bg-light text-dark border fw-semibold">' + r.job_card_no + '</span></td>' +
                            '<td class="fw-semibold text-primary">' + r.task_no + '</td>' +
                            '<td class="fw-semibold">' + r.service_name + '</td>' +
                            '<td><i class="ri ri-user-3-line text-muted me-1"></i>' + r.employee_name + '</td>' +
                            '<td class="text-center">' + r.issue_qty + '</td>' +
                            '<td class="text-center fw-bold text-success">' + r.completed_qty + '</td>' +
                            '<td class="text-center"><span class="badge bg-label-success">' + (r.status || 'ACTIVE') + '</span></td>' +
                        '</tr>';
                    });
                }
                $('#unitLineDrilldownTable tbody').html(tbodyHtml);

                // Initialize DataTable
                window.unitLineDrilldownDt = $('#unitLineDrilldownTable').DataTable({
                    autoWidth: false,
                    paging: true,
                    pageLength: 25,
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    language: {
                        emptyTable: '<div class="text-center py-4 text-muted">No breakdown entries found for this operation</div>'
                    }
                });
            } else {
                $('#unitLineDrilldownTable tbody').html('<tr><td colspan="8" class="text-center py-4 text-danger">Failed to load breakdown records.</td></tr>');
            }
        },
        error: function(err) {
            $('#unitLineDrilldownTable tbody').html('<tr><td colspan="8" class="text-center py-4 text-danger">An error occurred while loading breakdown records.</td></tr>');
        }
    });
};

// Event Delegations
$(document).ready(function() {
    // Click on any operation quantity in main table
    $(document).on('click', '.unit-line-drilldown-link', function(e) {
        e.preventDefault();
        var date = $(this).data('date');
        var dateDisplay = $(this).data('date-display');
        var op = $(this).data('op');
        var title = $(this).data('title');
        window.openUnitLineDrilldown(date, dateDisplay, op, title);
    });

    // Click on Back to Report
    $(document).on('click', '.btn-back-to-unit-line', function(e) {
        e.preventDefault();
        $('#unitLineDrilldownContainer').hide();
        $('#unitLineMainContainer').fadeIn(200, function() {
            if (window.unitLineAverageDt) {
                window.unitLineAverageDt.columns.adjust().draw(false);
            }
        });
    });
});
</script>

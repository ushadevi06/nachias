<div class="production-planning-wrapper">
    <!-- Top Stage & Title Bar Card -->
    <div class="card shadow-sm border mb-3">
        <div class="card-header bg-white py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <h5 class="mb-0 fw-bold text-dark text-uppercase" id="prodPlanningHeaderTitle" style="letter-spacing: 0.5px; font-size: 1.05rem;">
                    📋 CUTTING SUPPORTERS REPORT
                </h5>
                <span class="badge bg-primary text-white rounded-pill px-3 py-1 fw-bold" id="prodPlanningDateBadge">{{ date('d-m-Y') }}</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <label class="small text-dark mb-0 fw-bold">Stage:</label>
                <select id="prod_planning_stage_select" class="form-select form-select-sm fw-bold border-secondary" style="width: 200px;">
                    @foreach($operationStages ?? [] as $stg)
                        <option value="{{ $stg->id }}" {{ strtolower($stg->operation_stage_name) === 'cutting' || $stg->id == 1 ? 'selected' : '' }}>
                            {{ $stg->operation_stage_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- KPI Summary Cards with Sharp High-Contrast Styling -->
        <div class="card-body p-3 bg-light border-bottom">
            <div class="row g-2">
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="p-2 border rounded-3 text-center bg-white shadow-sm" style="border-top: 3px solid #3b82f6 !important;">
                        <span class="small fw-bold d-block text-uppercase" style="font-size: 0.72rem; color: #475569;">Total Employees</span>
                        <h5 class="mb-0 fw-bolder mt-1" style="font-size: 1.35rem; color: #0f172a;" id="ppSummaryEmployees">0</h5>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="p-2 border rounded-3 text-center bg-white shadow-sm" style="border-top: 3px solid #6366f1 !important;">
                        <span class="small fw-bold d-block text-uppercase" style="font-size: 0.72rem; color: #475569;">Working Hours</span>
                        <h5 class="mb-0 fw-bolder mt-1" style="font-size: 1.35rem; color: #4338ca;" id="ppSummaryHours">0.0</h5>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="p-2 border rounded-3 text-center bg-white shadow-sm" style="border-top: 3px solid #0284c7 !important;">
                        <span class="small fw-bold d-block text-uppercase" style="font-size: 0.72rem; color: #475569;">Plan Qty</span>
                        <h5 class="mb-0 fw-bolder mt-1" style="font-size: 1.35rem; color: #0284c7;" id="ppSummaryPlan">0</h5>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="p-2 border rounded-3 text-center bg-white shadow-sm" style="border-top: 3px solid #475569 !important;">
                        <span class="small fw-bold d-block text-uppercase" style="font-size: 0.72rem; color: #475569;">Issue Qty</span>
                        <h5 class="mb-0 fw-bolder mt-1" style="font-size: 1.35rem; color: #0f172a;" id="ppSummaryIssue">0</h5>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="p-2 border rounded-3 text-center bg-white shadow-sm" style="border-top: 3px solid #16a34a !important;">
                        <span class="small fw-bold d-block text-uppercase" style="font-size: 0.72rem; color: #475569;">Finish Qty</span>
                        <h5 class="mb-0 fw-bolder mt-1" style="font-size: 1.35rem; color: #16a34a;" id="ppSummaryFinish">0</h5>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="p-2 border rounded-3 text-center bg-white shadow-sm" style="border-top: 3px solid #dc2626 !important;">
                        <span class="small fw-bold d-block text-uppercase" style="font-size: 0.72rem; color: #475569;">Pending Qty</span>
                        <h5 class="mb-0 fw-bolder mt-1" style="font-size: 1.35rem; color: #dc2626;" id="ppSummaryPending">0</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTable Container -->
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0" id="productionPlanningTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 50px;">S.NO</th>
                            <th style="min-width: 170px;">NAME</th>
                            <th style="width: 110px;">WORKING HOURS</th>
                            <th style="min-width: 150px;">WORK</th>
                            <th style="width: 130px;">CUT NO</th>
                            <th style="width: 90px;" class="text-end">PLAN</th>
                            <th style="width: 90px;" class="text-end">ISSUE</th>
                            <th style="width: 90px;" class="text-end">FINISH</th>
                            <th style="width: 90px;" class="text-end">PENDING</th>
                            <th style="min-width: 120px;">REMARKS</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-center">TOTAL</th>
                            <th class="text-center" style="color: #4338ca !important;" id="ppFootHours">0.0</th>
                            <th class="text-center">-</th>
                            <th class="text-center">-</th>
                            <th class="text-end" style="color: #0284c7 !important;" id="ppFootPlan">0</th>
                            <th class="text-end" style="color: #0f172a !important;" id="ppFootIssue">0</th>
                            <th class="text-end" style="color: #16a34a !important;" id="ppFootFinish">0</th>
                            <th class="text-end" style="color: #dc2626 !important;" id="ppFootPending">0</th>
                            <th class="text-center">-</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
/* High-Contrast Production Planning Table Styling */
#productionPlanningTable {
    border: 1px solid #cbd5e1 !important;
    font-size: 0.88rem !important;
}

#productionPlanningTable thead th {
    background-color: #e2e8f0 !important;
    color: #0f172a !important;
    font-weight: 800 !important;
    border: 1px solid #cbd5e1 !important;
    padding: 8px 10px !important;
    vertical-align: middle !important;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    font-size: 0.82rem !important;
}

#productionPlanningTable tbody td {
    color: #0f172a !important;
    border: 1px solid #e2e8f0 !important;
    padding: 7px 10px !important;
    vertical-align: middle !important;
    font-weight: 600 !important;
}

#productionPlanningTable tbody tr:hover td {
    background-color: #f1f5f9 !important;
}

#productionPlanningTable tfoot th {
    background-color: #e2e8f0 !important;
    color: #0f172a !important;
    font-weight: 800 !important;
    border: 1px solid #cbd5e1 !important;
    padding: 9px 10px !important;
    font-size: 0.88rem !important;
}

/* Crisp Text Colors for columns */
.pp-dark-text {
    color: #0f172a !important;
    font-weight: 700 !important;
}
.pp-plan-text {
    color: #0284c7 !important;
    font-weight: 800 !important;
}
.pp-issue-text {
    color: #0f172a !important;
    font-weight: 800 !important;
}
.pp-finish-text {
    color: #16a34a !important;
    font-weight: 800 !important;
}
.pp-pending-text {
    color: #dc2626 !important;
    font-weight: 800 !important;
}
</style>

<script>
window.productionPlanningDt = null;

window.initProductionPlanningTable = function() {
    var stageId = $('#prod_planning_stage_select').val() || 1;
    var stageText = $('#prod_planning_stage_select option:selected').text().trim() || 'CUTTING';
    var fullTitle = stageText.toUpperCase() + ' SUPPORTERS REPORT';
    
    $('#prodPlanningHeaderTitle').text('📋 ' + fullTitle);
    $('#active_report_title').text('📋 ' + fullTitle);

    var fromDateVal = $('.start_date').val();
    var toDateVal = $('.end_date').val();
    if (!fromDateVal && !toDateVal) {
        var todayFormatted = moment().format('DD-MM-YYYY');
        $('.start_date').val(todayFormatted);
        $('.end_date').val(todayFormatted);
        fromDateVal = todayFormatted;
        toDateVal = todayFormatted;
    }
    $('#prodPlanningDateBadge').text(fromDateVal === toDateVal ? fromDateVal : (fromDateVal + ' to ' + toDateVal));

    if ($.fn.DataTable.isDataTable('#productionPlanningTable')) {
        $('#productionPlanningTable').DataTable().destroy();
    }

    window.productionPlanningDt = $('#productionPlanningTable').DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        dom: '<"row align-items-center mb-2"<"col-sm-6"l><"col-sm-6 text-end"f>>rt<"row align-items-center mt-2"<"col-sm-5"i><"col-sm-7"p>>B',
        buttons: [
            {
                extend: 'excelHtml5',
                className: 'buttons-excel d-none',
                title: function() {
                    return ($('#prodPlanningHeaderTitle').text().replace('📋', '').trim()) + ' (' + $('#prodPlanningDateBadge').text() + ')';
                },
                footer: true,
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function(data, row, column, node) {
                            var div = document.createElement('div');
                            div.innerHTML = data;
                            return div.textContent || div.innerText || '';
                        }
                    }
                }
            },
            {
                extend: 'pdfHtml5',
                className: 'buttons-pdf d-none',
                orientation: 'landscape',
                pageSize: 'A4',
                title: function() {
                    return ($('#prodPlanningHeaderTitle').text().replace('📋', '').trim()) + ' (' + $('#prodPlanningDateBadge').text() + ')';
                },
                footer: true,
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function(data, row, column, node) {
                            var div = document.createElement('div');
                            div.innerHTML = data;
                            return div.textContent || div.innerText || '';
                        }
                    }
                },
                customize: function(doc) {
                    doc.styles.tableHeader.fontSize = 8;
                    doc.styles.tableFooter = { fontSize: 8, bold: true };
                    doc.defaultStyle.fontSize = 7.5;
                    doc.content[1].table.widths = ['5%', '18%', '9%', '16%', '12%', '8%', '8%', '8%', '8%', '8%'];
                }
            },
            {
                extend: 'print',
                className: 'buttons-print d-none',
                title: function() {
                    return '<h3 style="text-align:center; margin-bottom:10px;">' + ($('#prodPlanningHeaderTitle').text().replace('📋', '').trim()) + '</h3><div style="text-align:center; font-size:12px; margin-bottom:15px; color:#555;">Date: ' + $('#prodPlanningDateBadge').text() + '</div>';
                },
                footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }
        ],
        ajax: {
            url: "{{ url('production_reports/ajax/production-planning') }}",
            type: 'GET',
            data: function(d) {
                d.stage_id = $('#prod_planning_stage_select').val() || 1;
                d.from_date = $('.start_date').val();
                d.to_date = $('.end_date').val();
                d.brand_id = $('select[name="brand_id"]').val();
                d.unit_id = $('select[name="unit_id"]').val();
            }
        },
        columns: [
            { data: 's_no', name: 's_no', className: 'text-center pp-dark-text' },
            { data: 'name', name: 'name', className: 'pp-dark-text' },
            { data: 'working_hours', name: 'working_hours', className: 'text-center pp-dark-text' },
            { data: 'work', name: 'work', className: 'text-center pp-dark-text' },
            { data: 'cut_no', name: 'cut_no', className: 'text-center pp-dark-text' },
            { data: 'plan_qty', name: 'plan_qty', className: 'text-end pp-plan-text' },
            { data: 'issue_qty', name: 'issue_qty', className: 'text-end pp-issue-text' },
            { data: 'completed_qty', name: 'completed_qty', className: 'text-end pp-finish-text' },
            { data: 'pending_qty', name: 'pending_qty', className: 'text-end pp-pending-text' },
            { data: 'remarks', name: 'remarks', className: 'text-center pp-dark-text' }
        ],
        drawCallback: function(settings) {
            var json = settings.json;
            if (json && json.meta) {
                var m = json.meta;
                $('#ppSummaryEmployees').text(m.total_employees || 0);
                $('#ppSummaryHours').text(m.total_hours || '0.0');
                $('#ppSummaryPlan').text(m.total_plan || 0);
                $('#ppSummaryIssue').text(m.total_issue || 0);
                $('#ppSummaryFinish').text(m.total_finish || 0);
                $('#ppSummaryPending').text(m.total_pending || 0);

                $('#ppFootHours').text(m.total_hours || '0.0');
                $('#ppFootPlan').text(m.total_plan || 0);
                $('#ppFootIssue').text(m.total_issue || 0);
                $('#ppFootFinish').text(m.total_finish || 0);
                $('#ppFootPending').text(m.total_pending || 0);

                if (m.from_date && m.to_date) {
                    $('#prodPlanningDateBadge').text(m.from_date === m.to_date ? m.from_date : (m.from_date + ' to ' + m.to_date));
                }
            }
            if (typeof hideReportLoading === 'function') {
                hideReportLoading();
            }
        },
        language: {
            emptyTable: "No planning / supporter records found for this stage and date",
            processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading Supporters Report...'
        }
    });
};

$(document).on('change', '#prod_planning_stage_select', function() {
    if (typeof window.initProductionPlanningTable === 'function') {
        window.initProductionPlanningTable();
    }
});
</script>

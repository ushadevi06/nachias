<div class="production-planning-wrapper">
    <!-- Filter Toolbar -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-label-primary px-3 py-2 fw-semibold" id="prodPlanningDateBadge">{{ date('d-m-Y') }}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <label class="small text-muted mb-0 fw-semibold">Stage:</label>
            <select id="prod_planning_stage_select" class="form-select form-select-sm" style="width: 200px;">
                @foreach($operationStages ?? [] as $stg)
                    <option value="{{ $stg->id }}" {{ strtolower($stg->operation_stage_name) === 'cutting' || $stg->id == 1 ? 'selected' : '' }}>
                        {{ $stg->operation_stage_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-lg-2 col-md-4 col-6">
            <div class="p-3 border rounded-3 text-center bg-white">
                <span class="small fw-semibold d-block text-muted text-uppercase" style="font-size: 0.72rem;">Total Employees</span>
                <h5 class="mb-0 fw-bold mt-1 text-primary" id="ppSummaryEmployees">0</h5>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="p-3 border rounded-3 text-center bg-white">
                <span class="small fw-semibold d-block text-muted text-uppercase" style="font-size: 0.72rem;">Working Hours</span>
                <h5 class="mb-0 fw-bold mt-1 text-info" id="ppSummaryHours">0.0</h5>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="p-3 border rounded-3 text-center bg-white">
                <span class="small fw-semibold d-block text-muted text-uppercase" style="font-size: 0.72rem;">Plan Qty</span>
                <h5 class="mb-0 fw-bold mt-1 text-dark" id="ppSummaryPlan">0</h5>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="p-3 border rounded-3 text-center bg-white">
                <span class="small fw-semibold d-block text-muted text-uppercase" style="font-size: 0.72rem;">Issue Qty</span>
                <h5 class="mb-0 fw-bold mt-1 text-secondary" id="ppSummaryIssue">0</h5>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="p-3 border rounded-3 text-center bg-white">
                <span class="small fw-semibold d-block text-muted text-uppercase" style="font-size: 0.72rem;">Finish Qty</span>
                <h5 class="mb-0 fw-bold mt-1 text-success" id="ppSummaryFinish">0</h5>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="p-3 border rounded-3 text-center bg-white">
                <span class="small fw-semibold d-block text-muted text-uppercase" style="font-size: 0.72rem;">Pending Qty</span>
                <h5 class="mb-0 fw-bold mt-1 text-danger" id="ppSummaryPending">0</h5>
            </div>
        </div>
    </div>

    <!-- Standard Normal Report DataTable -->
    <div class="card-datatable table-responsive">
        <table class="datatables-products table table-hover align-middle" id="productionPlanningTable" style="width: 100%;">
            <thead class="bg-light">
                <tr>
                    <th style="width: 50px;">S.NO</th>
                    <th>NAME</th>
                    <th class="text-center">WORKING HOURS</th>
                    <th>WORK</th>
                    <th class="text-center">CUT NO</th>
                    <th class="text-end">PLAN</th>
                    <th class="text-end">ISSUE</th>
                    <th class="text-end">FINISH</th>
                    <th class="text-end">PENDING</th>
                    <th>REMARKS</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot class="bg-light fw-bold">
                <tr>
                    <th colspan="2" class="text-center">TOTAL</th>
                    <th class="text-center text-info" id="ppFootHours">0.0</th>
                    <th class="text-center">-</th>
                    <th class="text-center">-</th>
                    <th class="text-end" id="ppFootPlan">0</th>
                    <th class="text-end" id="ppFootIssue">0</th>
                    <th class="text-end text-success" id="ppFootFinish">0</th>
                    <th class="text-end text-danger" id="ppFootPending">0</th>
                    <th class="text-center">-</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script>
window.productionPlanningDt = null;

window.initProductionPlanningTable = function() {
    var stageId = $('#prod_planning_stage_select').val() || 1;
    var stageText = $('#prod_planning_stage_select option:selected').text().trim() || 'CUTTING';
    var fullTitle = stageText.toUpperCase() + ' SUPPORTERS REPORT';

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
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>B',
        buttons: [
            {
                extend: 'excelHtml5',
                className: 'buttons-excel d-none',
                title: function() {
                    return ($('#active_report_title').text().replace('📋', '').trim()) + ' (' + $('#prodPlanningDateBadge').text() + ')';
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
                    return ($('#active_report_title').text().replace('📋', '').trim()) + ' (' + $('#prodPlanningDateBadge').text() + ')';
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
                    return '<h3 style="text-align:center; margin-bottom:10px;">' + ($('#active_report_title').text().replace('📋', '').trim()) + '</h3><div style="text-align:center; font-size:12px; margin-bottom:15px; color:#555;">Date: ' + $('#prodPlanningDateBadge').text() + '</div>';
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
            { data: 's_no', name: 's_no', className: 'text-center' },
            { data: 'name', name: 'name' },
            { data: 'working_hours', name: 'working_hours', className: 'text-center' },
            { data: 'work', name: 'work', className: 'text-center' },
            { data: 'cut_no', name: 'cut_no', className: 'text-center' },
            { data: 'plan_qty', name: 'plan_qty', className: 'text-end' },
            { data: 'issue_qty', name: 'issue_qty', className: 'text-end' },
            { data: 'completed_qty', name: 'completed_qty', className: 'text-end text-success fw-semibold' },
            { data: 'pending_qty', name: 'pending_qty', className: 'text-end text-danger fw-semibold' },
            { data: 'remarks', name: 'remarks', className: 'text-center' }
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
            if (typeof showReportLoading === 'function') {
                showReportLoading(false);
            }
        }
    });
};

$(document).on('change', '#prod_planning_stage_select', function() {
    if (typeof window.initProductionPlanningTable === 'function') {
        window.initProductionPlanningTable();
    }
});
</script>

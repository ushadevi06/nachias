<div class="stage-wise-wip-wrapper">
    <!-- Main Dynamic DataTable Container -->
    <div class="card shadow-sm border mb-4">
        <div class="card-header bg-light py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-dark text-uppercase" id="stageWipHeaderTitle" style="letter-spacing: 0.5px;">
                HO CUTTING SECTION OPENING STOCK & W.I.P REPORT
            </h5>
            <div class="d-flex align-items-center gap-2">
                <label class="small text-muted mb-0 fw-semibold">Stage:</label>
                <select id="stage_wip_stage_select" class="form-select form-select-sm" style="width: 180px; font-weight: 600;">
                    @foreach($operationStages ?? [] as $stg)
                        <option value="{{ $stg->id }}" {{ strtolower($stg->operation_stage_name) === 'cutting' || $stg->id == 1 ? 'selected' : '' }}>
                            {{ $stg->operation_stage_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="card-body p-2">
            <!-- Service Column Toggle Toolbar -->
            <div id="serviceColumnFilterBox" class="p-2 mb-2 rounded-2 border bg-light d-none">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2 pb-1 border-bottom">
                    <span class="small fw-bold text-dark text-uppercase" style="font-size: 0.78rem;">
                        <i class="ri ri-checkbox-multiple-line me-1 text-primary"></i> Show / Hide Services:
                    </span>
                    <div class="d-flex align-items-center gap-1">
                        <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 rounded-pill" id="btnSelectAllServices" style="font-size: 0.72rem;">
                            <i class="ri ri-checkbox-line me-1"></i> Select All
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 rounded-pill" id="btnDeselectAllServices" style="font-size: 0.72rem;">
                            <i class="ri ri-checkbox-blank-line me-1"></i> Deselect All
                        </button>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center" id="serviceColumnCheckboxes">
                    <!-- Dynamic service checkboxes -->
                </div>
            </div>

            <div class="table-responsive" style="max-height: 550px; overflow-y: auto;">
                <table class="table table-bordered table-hover text-nowrap align-middle" id="stageWiseWipTable" style="width: 100%; font-size: 0.84rem;">
                    <thead id="stageWiseWipThead" class="align-middle text-center" style="position: sticky; top: 0; z-index: 10; background-color: #f8fafc;">
                        <!-- Injected dynamically based on stage services -->
                    </thead>
                    <tbody id="stageWiseWipTbody"></tbody>
                    <tfoot id="stageWiseWipTfoot" class="fw-bold align-middle bg-light" style="position: sticky; bottom: 0; z-index: 9;">
                        <!-- Injected dynamically -->
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Abstract Summary Matrix: Brand wise x Style wise WIP -->
    <div class="card shadow-sm border mb-3" id="stageWipAbstractMatrixCard">
        <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background-color: #f1f5f9;">
            <h6 class="mb-0 fw-bold text-dark text-uppercase" style="letter-spacing: 0.5px;">
                📊 Brand wise, Style wise WIP as on {{ date('d.m.Y') }}
            </h6>
            <span class="badge bg-primary px-3 py-1 rounded-pill">Abstract Summary</span>
        </div>
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-nowrap text-center align-middle mb-0" id="stageWipMatrixTable" style="font-size: 0.84rem;">
                    <thead id="stageWipMatrixThead" style="background-color: #e2e8f0;">
                        <!-- Injected dynamically -->
                    </thead>
                    <tbody id="stageWipMatrixTbody">
                        <!-- Injected dynamically -->
                    </tbody>
                    <tfoot id="stageWipMatrixTfoot" class="fw-bold" style="background-color: #fef08a;">
                        <!-- Injected dynamically -->
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
#stageWiseWipTable th, #stageWiseWipTable td {
    padding: 6px 10px !important;
    vertical-align: middle;
}
#stageWipMatrixTable th, #stageWipMatrixTable td {
    padding: 6px 12px !important;
}
div.dataTables_processing {
    position: absolute !important;
    top: 50% !important;
    left: 50% !important;
    transform: translate(-50%, -50%) !important;
    margin: 0 !important;
    padding: 0 !important;
    background: transparent !important;
    border: none !important;
    z-index: 1100 !important;
}
</style>

<script>
window.allStageServices = @json($allStageServices ?? []);
window.currentWipServices = [];
window.currentWipAbstractMatrix = null;
window.stageWiseWipDt = null;

window.initStageWiseWipTable = function() {
    var stageId = $('#stage_wip_stage_select').val() || 1;
    var stageText = $('#stage_wip_stage_select option:selected').text().trim() || 'CUTTING';
    $('#stageWipHeaderTitle').text('HO ' + stageText + ' SECTION OPENING STOCK & W.I.P REPORT');

    var services = (window.allStageServices && window.allStageServices[stageId]) ? window.allStageServices[stageId] : null;

    if (services && services.length) {
        window.currentWipServices = services;
        buildStageWipHeaders(services, stageText);
        initStageWipDataTable(services, stageId);
    } else {
        var fromDate = $('.start_date').val() || '';
        var toDate = $('.end_date').val() || '';
        var brandId = $('select[name="brand_id"]').val() || '';
        var unitId = $('#unit_id_filter').val() || '';

        $('#stageWiseWipTbody').html('<tr><td colspan="20" class="text-center py-5 text-primary fw-bold"><div class="spinner-border spinner-border-sm me-2" role="status"></div> Loading Stage WIP records...</td></tr>');

        $.ajax({
            url: "{{ url('production_reports/ajax/stage-wise-wip') }}",
            type: 'GET',
            data: {
                operation_stage_id: stageId,
                from_date: fromDate,
                to_date: toDate,
                brand_id: brandId,
                unit_id: unitId,
                length: 1
            },
            success: function(resp) {
                if (!resp.meta) return;
                var meta = resp.meta;
                window.currentWipServices = meta.services || [];
                buildStageWipHeaders(meta.services, meta.stage_name);
                if (meta.abstract_matrix) {
                    buildStageWipMatrix(meta.abstract_matrix);
                }
                initStageWipDataTable(meta.services, stageId);
            }
        });
    }
};

function buildStageWipHeaders(services, stageName) {
    var theadHtml = '<tr>';
    theadHtml += '<th class="text-center" style="background-color: #e0f2fe; width: 45px;">S. No</th>';
    theadHtml += '<th class="text-center" style="background-color: #e0f2fe; min-width: 85px;">Date</th>';
    theadHtml += '<th class="text-center" style="background-color: #e0f2fe; min-width: 95px;">CUT NO</th>';
    theadHtml += '<th class="text-center" style="background-color: #e0f2fe; min-width: 80px;">UOM / MTRS</th>';
    theadHtml += '<th class="text-center" style="background-color: #e0f2fe; min-width: 110px;">STYLE</th>';
    theadHtml += '<th class="text-center" style="background-color: #e0f2fe; min-width: 75px;">SLEEVE (Full)</th>';
    theadHtml += '<th class="text-center" style="background-color: #e0f2fe; min-width: 75px;">SLEEVE (Half)</th>';
    theadHtml += '<th class="text-center" style="background-color: #bae6fd; min-width: 90px;">TOTAL ' + (stageName || 'CUTTING') + ' QTY</th>';
    theadHtml += '<th class="text-center" style="background-color: #e0f2fe; min-width: 85px;">Delivery Date</th>';

    // Dynamic Production Services
    $.each(services, function(idx, s) {
        var code = s.service_code || s.code || s.service_name || s.name || ('SVC ' + s.id);
        var name = s.service_name || s.name || code;
        theadHtml += '<th class="text-center text-nowrap" style="background-color: #fef08a; font-size: 0.78rem; min-width: 75px;" title="' + name + '">' + code + '</th>';
    });

    // Timeline & Completion
    theadHtml += '<th class="text-center" style="background-color: #fed7aa; min-width: 75px;">Days in WIP</th>';
    theadHtml += '<th class="text-center" style="background-color: #fed7aa; min-width: 95px;">' + (stageName || 'Cutting') + ' sent Date</th>';
    theadHtml += '<th class="text-center" style="background-color: #fed7aa; min-width: 75px;">Days taken</th>';
    theadHtml += '<th class="text-center" style="background-color: #bbf7d0; min-width: 85px;">Store Stock</th>';
    theadHtml += '</tr>';

    $('#stageWiseWipThead').html(theadHtml);

    // Build 1-to-1 matching tfoot for robust DataTables visibility toggle
    var tfootHtml = '<tr class="bg-light fw-bold text-center">';
    tfootHtml += '<th class="text-end">TOTAL</th>';
    tfootHtml += '<th>-</th>';
    tfootHtml += '<th>-</th>';
    tfootHtml += '<th id="foot_mtrs" class="text-center text-primary">-</th>';
    tfootHtml += '<th>-</th>';
    tfootHtml += '<th id="foot_fs" class="text-center">-</th>';
    tfootHtml += '<th id="foot_hs" class="text-center">-</th>';
    tfootHtml += '<th id="foot_total_cut" class="text-center text-primary fs-6">-</th>';
    tfootHtml += '<th>-</th>';

    $.each(services, function(idx, s) {
        tfootHtml += '<th id="foot_svc_' + s.id + '" class="text-center">-</th>';
    });

    tfootHtml += '<th>-</th>';
    tfootHtml += '<th>-</th>';
    tfootHtml += '<th>-</th>';
    tfootHtml += '<th id="foot_store_stock" class="text-center text-success fs-6">-</th>';
    tfootHtml += '</tr>';

    $('#stageWiseWipTfoot').html(tfootHtml);

    // Build Checkboxes for Show / Hide service columns
    buildServiceCheckboxes(services);
}

function buildServiceCheckboxes(services) {
    if (!services || !services.length) {
        $('#serviceColumnFilterBox').addClass('d-none');
        return;
    }
    $('#serviceColumnFilterBox').removeClass('d-none');
    var chkHtml = '';
    $.each(services, function(idx, s) {
        var label = s.service_code || s.code || s.service_name || s.name || ('SVC ' + s.id);
        var title = s.service_name || s.name || label;
        chkHtml += '<div class="form-check form-check-inline me-2 mb-1" style="font-size: 0.8rem;">';
        chkHtml += '<input class="form-check-input service-col-toggle" type="checkbox" id="chk_svc_' + s.id + '" data-svc-name="svc_' + s.id + '" checked>';
        chkHtml += '<label class="form-check-label fw-semibold text-dark cursor-pointer" for="chk_svc_' + s.id + '" title="' + title + '">' + label + '</label>';
        chkHtml += '</div>';
    });
    $('#serviceColumnCheckboxes').html(chkHtml);
}

function initStageWipDataTable(services, stageId) {
    if ($.fn.DataTable.isDataTable('#stageWiseWipTable')) {
        $('#stageWiseWipTable').DataTable().clear().destroy();
        $('#stageWiseWipTbody').empty();
    }

    var columns = [
        { data: 's_no', className: 'text-center' },
        { data: 'date', className: 'text-center text-nowrap' },
        { data: 'cut_no', className: 'text-center' },
        { data: 'mtrs', className: 'text-center' },
        { data: 'style', className: 'text-center' },
        { data: 'fs_qty', className: 'text-center' },
        { data: 'hs_qty', className: 'text-center' },
        { data: 'total_cutting_qty', className: 'text-center text-primary fw-bold' },
        { data: 'delivery_date', className: 'text-center text-nowrap' }
    ];

    $.each(services, function(idx, s) {
        columns.push({
            data: 'svc_' + s.id,
            name: 'svc_' + s.id,
            className: 'text-center',
            orderable: false,
            defaultContent: '-'
        });
    });

    columns.push({
        data: 'days_in_wip',
        className: 'text-center',
        render: function(data, type, row) {
            return '<span class="fw-semibold text-dark">' + (data !== null && data !== undefined ? data : '-') + '</span>';
        }
    });
    columns.push({ data: 'cutting_sent_date', className: 'text-center text-nowrap' });
    columns.push({ data: 'days_taken', className: 'text-center' });
    columns.push({ data: 'store_stock', className: 'text-center fw-bold text-success' });

    window.stageWiseWipDt = $('#stageWiseWipTable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        language: {
            processing: '<div class="d-flex align-items-center justify-content-center p-3 bg-white border border-primary rounded shadow text-primary fw-bold" style="min-width: 250px;"><div class="spinner-border spinner-border-sm me-2 text-primary" role="status"></div> Loading Stage WIP records...</div>',
            emptyTable: '<div class="text-center py-4 text-muted"><i class="ri-inbox-line ri-2x mb-2 d-block text-secondary"></i>No records found</div>',
            zeroRecords: '<div class="text-center py-3 text-muted">No matching records found</div>'
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        buttons: [
            {
                extend: 'excel',
                className: 'buttons-excel d-none',
                title: function () {
                    var stageText = $('#stage_wip_stage_select option:selected').text().trim() || 'Cutting';
                    return 'HO_' + stageText + '_Section_Stage_Wise_WIP_Report_' + (new Date().toISOString().slice(0, 10));
                },
                exportOptions: {
                    columns: ':visible',
                    footer: true,
                    format: {
                        header: function (data, columnIdx) {
                            return (typeof data === 'string') ? data.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim() : data;
                        },
                        body: function (data, row, column, node) {
                            return (typeof data === 'string') ? data.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim() : data;
                        },
                        footer: function (data, row, column, node) {
                            return (typeof data === 'string') ? data.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim() : data;
                        }
                    }
                },
                customize: function (xlsx) {
                    var matrixData = window.currentWipAbstractMatrix;
                    if (!matrixData || !matrixData.columns || !matrixData.rows) return;

                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    var sheetDataNode = sheet.getElementsByTagName('sheetData')[0];
                    if (!sheetDataNode) return;

                    var lastRowR = 0;
                    var rowElements = sheetDataNode.getElementsByTagName('row');
                    for (var i = 0; i < rowElements.length; i++) {
                        var r = parseInt(rowElements[i].getAttribute('r'), 10);
                        if (r > lastRowR) lastRowR = r;
                    }

                    function getExcelColLetter(n) {
                        var s = "";
                        while (n >= 0) {
                            s = String.fromCharCode((n % 26) + 65) + s;
                            n = Math.floor(n / 26) - 1;
                        }
                        return s;
                    }

                    function escapeXml(str) {
                        return String(str || '')
                            .replace(/&/g, '&amp;')
                            .replace(/</g, '&lt;')
                            .replace(/>/g, '&gt;')
                            .replace(/"/g, '&quot;')
                            .replace(/'/g, '&apos;');
                    }

                    var matrixCols = matrixData.columns || [];
                    var matrixRows = matrixData.rows || {};
                    var curR = lastRowR + 2;
                    var appendXml = '';

                    // Title row
                    appendXml += '<row r="' + curR + '"><c r="A' + curR + '" t="inlineStr" s="2"><is><t>BRAND WISE, STYLE WISE WIP ABSTRACT SUMMARY</t></is></c></row>';
                    curR++;

                    // Header row
                    appendXml += '<row r="' + curR + '">';
                    appendXml += '<c r="' + getExcelColLetter(0) + curR + '" t="inlineStr" s="2"><is><t>Brand</t></is></c>';
                    for (var cIdx = 0; cIdx < matrixCols.length; cIdx++) {
                        appendXml += '<c r="' + getExcelColLetter(cIdx + 1) + curR + '" t="inlineStr" s="2"><is><t>' + escapeXml(matrixCols[cIdx]) + '</t></is></c>';
                    }
                    appendXml += '<c r="' + getExcelColLetter(matrixCols.length + 1) + curR + '" t="inlineStr" s="2"><is><t>Total</t></is></c>';
                    appendXml += '</row>';
                    curR++;

                    // Brand rows
                    for (var brandName in matrixRows) {
                        if (brandName === 'TOTAL') continue;
                        var colVals = matrixRows[brandName] || {};
                        appendXml += '<row r="' + curR + '">';
                        appendXml += '<c r="' + getExcelColLetter(0) + curR + '" t="inlineStr"><is><t>' + escapeXml(brandName) + '</t></is></c>';
                        for (var cIdx = 0; cIdx < matrixCols.length; cIdx++) {
                            var val = colVals[matrixCols[cIdx]] || 0;
                            appendXml += '<c r="' + getExcelColLetter(cIdx + 1) + curR + '"><v>' + (val || 0) + '</v></c>';
                        }
                        var bTotal = colVals['TOTAL'] || 0;
                        appendXml += '<c r="' + getExcelColLetter(matrixCols.length + 1) + curR + '" s="2"><v>' + (bTotal || 0) + '</v></c>';
                        appendXml += '</row>';
                        curR++;
                    }

                    // Summary Total row
                    if (matrixRows['TOTAL']) {
                        var totalVals = matrixRows['TOTAL'];
                        appendXml += '<row r="' + curR + '">';
                        appendXml += '<c r="' + getExcelColLetter(0) + curR + '" t="inlineStr" s="2"><is><t>Total</t></is></c>';
                        for (var cIdx = 0; cIdx < matrixCols.length; cIdx++) {
                            var tVal = totalVals[matrixCols[cIdx]] || 0;
                            appendXml += '<c r="' + getExcelColLetter(cIdx + 1) + curR + '" s="2"><v>' + (tVal || 0) + '</v></c>';
                        }
                        var grandTotal = totalVals['TOTAL'] || 0;
                        appendXml += '<c r="' + getExcelColLetter(matrixCols.length + 1) + curR + '" s="2"><v>' + (grandTotal || 0) + '</v></c>';
                        appendXml += '</row>';
                    }

                    try {
                        var parser = new DOMParser();
                        var xmlDoc = parser.parseFromString('<root xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' + appendXml + '</root>', 'application/xml');
                        var children = xmlDoc.documentElement.childNodes;
                        while (children.length > 0) {
                            sheetDataNode.appendChild(children[0]);
                        }
                        var dimensionNode = sheet.getElementsByTagName('dimension')[0];
                        if (dimensionNode) {
                            var maxCol = getExcelColLetter(Math.max(16, matrixCols.length + 1));
                            dimensionNode.setAttribute('ref', 'A1:' + maxCol + curR);
                        }
                    } catch (e) {
                        console.error('Error appending Abstract Matrix to Excel sheet:', e);
                    }
                },
                action: function(e, dt, button, config) {
                    if (typeof window.serverSideExportAction === 'function') {
                        window.serverSideExportAction.call(this, e, dt, button, config);
                    } else {
                        $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
                    }
                }
            },
            {
                extend: 'pdf',
                className: 'buttons-pdf d-none',
                orientation: 'landscape',
                pageSize: 'A3',
                title: function () {
                    var stageText = $('#stage_wip_stage_select option:selected').text().trim() || 'Cutting';
                    return 'HO ' + stageText + ' Section Stage Wise WIP Report';
                },
                exportOptions: {
                    columns: ':visible',
                    footer: true,
                    format: {
                        header: function (data, columnIdx) {
                            return (typeof data === 'string') ? data.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim() : data;
                        },
                        body: function (data, row, column, node) {
                            return (typeof data === 'string') ? data.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim() : data;
                        },
                        footer: function (data, row, column, node) {
                            return (typeof data === 'string') ? data.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim() : data;
                        }
                    }
                },
                customize: function (doc) {
                    var matrixData = window.currentWipAbstractMatrix;
                    if (!matrixData || !matrixData.columns || !matrixData.rows) return;

                    var matrixCols = matrixData.columns || [];
                    var matrixRows = matrixData.rows || {};

                    doc.content.push({
                        text: 'BRAND WISE, STYLE WISE WIP ABSTRACT SUMMARY',
                        fontSize: 10,
                        bold: true,
                        margin: [0, 20, 0, 8]
                    });

                    var pdfTableBody = [];
                    // Header row
                    var pdfHeaderRow = [{ text: 'Brand', bold: true, fillColor: '#cbd5e1', alignment: 'left' }];
                    matrixCols.forEach(function(c) {
                        pdfHeaderRow.push({ text: c, bold: true, fillColor: '#cbd5e1', alignment: 'center' });
                    });
                    pdfHeaderRow.push({ text: 'Total', bold: true, fillColor: '#cbd5e1', alignment: 'center' });
                    pdfTableBody.push(pdfHeaderRow);

                    // Body rows
                    for (var brandName in matrixRows) {
                        if (brandName === 'TOTAL') continue;
                        var colVals = matrixRows[brandName] || {};
                        var row = [{ text: brandName, bold: true, alignment: 'left' }];
                        matrixCols.forEach(function(c) {
                            var val = colVals[c] || 0;
                            row.push({ text: val > 0 ? Number(val).toLocaleString('en-IN') : '0', alignment: 'center' });
                        });
                        var bTotal = colVals['TOTAL'] || 0;
                        row.push({ text: bTotal > 0 ? Number(bTotal).toLocaleString('en-IN') : '0', bold: true, alignment: 'center' });
                        pdfTableBody.push(row);
                    }

                    // Total row
                    if (matrixRows['TOTAL']) {
                        var totRow = [{ text: 'Total', bold: true, fillColor: '#fef08a', alignment: 'left' }];
                        matrixCols.forEach(function(c) {
                            var val = matrixRows['TOTAL'][c] || 0;
                            totRow.push({ text: val > 0 ? Number(val).toLocaleString('en-IN') : '0', bold: true, fillColor: '#fef08a', alignment: 'center' });
                        });
                        var gTotal = matrixRows['TOTAL']['TOTAL'] || 0;
                        totRow.push({ text: gTotal > 0 ? Number(gTotal).toLocaleString('en-IN') : '0', bold: true, fillColor: '#fef08a', alignment: 'center' });
                        pdfTableBody.push(totRow);
                    }

                    doc.content.push({
                        table: {
                            headerRows: 1,
                            widths: Array(matrixCols.length + 2).fill('*'),
                            body: pdfTableBody
                        },
                        layout: {
                            hLineWidth: function () { return 0.5; },
                            vLineWidth: function () { return 0.5; },
                            hLineColor: function () { return '#bbb'; },
                            vLineColor: function () { return '#bbb'; }
                        },
                        margin: [0, 0, 0, 15]
                    });
                },
                action: function(e, dt, button, config) {
                    if (typeof window.serverSideExportAction === 'function') {
                        window.serverSideExportAction.call(this, e, dt, button, config);
                    } else {
                        $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                    }
                }
            },
            {
                extend: 'print',
                className: 'buttons-print d-none',
                title: function () {
                    var stageText = $('#stage_wip_stage_select option:selected').text().trim() || 'Cutting';
                    return 'HO ' + stageText + ' Section Stage Wise WIP Report';
                },
                exportOptions: {
                    columns: ':visible',
                    footer: true
                },
                customize: function (win) {
                    var $matrixCard = $('#stageWipAbstractMatrixCard');
                    if ($matrixCard.length) {
                        var cloneHtml = $matrixCard.clone();
                        cloneHtml.css('margin-top', '25px');
                        cloneHtml.find('table').addClass('table table-bordered').css({
                            'border-collapse': 'collapse',
                            'width': '100%',
                            'font-size': '11px'
                        });
                        $(win.document.body).append(cloneHtml);
                    }
                },
                action: function(e, dt, button, config) {
                    if (typeof window.serverSideExportAction === 'function') {
                        window.serverSideExportAction.call(this, e, dt, button, config);
                    } else {
                        $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                    }
                }
            }
        ],
        ajax: {
            url: "{{ url('production_reports/ajax/stage-wise-wip') }}",
            type: 'GET',
            data: function(d) {
                d.operation_stage_id = $('#stage_wip_stage_select').val() || stageId;
                d.from_date = $('.start_date').val();
                d.to_date = $('.end_date').val();
                d.brand_id = $('select[name="brand_id"]').val();
                d.unit_id = $('#unit_id_filter').val();
            }
        },
        columns: columns,
        drawCallback: function(settings) {
            var json = settings.json;
            if (json && json.meta) {
                if (json.meta.abstract_matrix) {
                    window.currentWipAbstractMatrix = json.meta.abstract_matrix;
                    buildStageWipMatrix(json.meta.abstract_matrix);
                }
                updateStageWipFooters(json.meta.totals, services);
            }
            if (typeof window.showReportLoading === 'function') {
                window.showReportLoading(false);
            }
            $('#report_loader').remove();
            $('#reportTabsContent').css('opacity', '1');
        }
    });
}

function updateStageWipFooters(totals, services) {
    if (!totals) return;
    $('#foot_mtrs').text(totals.mtrs || '0');
    $('#foot_fs').text(totals.fs_qty || '0');
    $('#foot_hs').text(totals.hs_qty || '0');
    $('#foot_total_cut').text(totals.total_cutting_qty || '0');
    $.each(services, function(idx, s) {
        var svcTot = totals.services && totals.services[s.id] ? totals.services[s.id] : '-';
        $('#foot_svc_' + s.id).text(svcTot);
    });
    $('#foot_store_stock').text(totals.store_stock || '0');
}

// Toggle Service Columns on Checkbox click
$(document).on('change', '.service-col-toggle', function() {
    if (!window.stageWiseWipDt) return;
    var svcName = $(this).data('svc-name');
    var isChecked = $(this).is(':checked');
    var col = window.stageWiseWipDt.column(svcName + ':name');
    if (col && col.length) {
        col.visible(isChecked);
    }
});

// Select All Services
$(document).on('click', '#btnSelectAllServices', function() {
    if (!window.stageWiseWipDt) return;
    $('.service-col-toggle').prop('checked', true);
    $.each(window.currentWipServices || [], function(idx, s) {
        var col = window.stageWiseWipDt.column('svc_' + s.id + ':name');
        if (col && col.length) {
            col.visible(true);
        }
    });
});

// Deselect All Services
$(document).on('click', '#btnDeselectAllServices', function() {
    if (!window.stageWiseWipDt) return;
    $('.service-col-toggle').prop('checked', false);
    $.each(window.currentWipServices || [], function(idx, s) {
        var col = window.stageWiseWipDt.column('svc_' + s.id + ':name');
        if (col && col.length) {
            col.visible(false);
        }
    });
});

function buildStageWipMatrix(matrixData) {
    if (!matrixData || !matrixData.columns || !matrixData.rows) return;

    var cols = matrixData.columns; 
    var rows = matrixData.rows;

    // Build thead
    var thead = '<tr>';
    thead += '<th style="width: 120px; background-color: #cbd5e1;">Brand</th>';
    $.each(cols, function(i, c) {
        thead += '<th>' + c + '</th>';
    });
    thead += '<th style="background-color: #cbd5e1;">Total</th>';
    thead += '</tr>';
    $('#stageWipMatrixThead').html(thead);

    // Build tbody
    var tbody = '';
    var totalRow = rows['TOTAL'] || null;

    $.each(rows, function(brandName, colVals) {
        if (brandName === 'TOTAL') return;

        tbody += '<tr>';
        tbody += '<td class="fw-bold text-dark text-start ps-3" style="background-color: #f8fafc;">' + brandName + '</td>';
        $.each(cols, function(i, c) {
            var val = colVals[c] || 0;
            tbody += '<td>' + (val > 0 ? Number(val).toLocaleString('en-IN') : '0') + '</td>';
        });
        var bTotal = colVals['TOTAL'] || 0;
        tbody += '<td class="fw-bold text-primary" style="background-color: #f1f5f9;">' + (bTotal > 0 ? Number(bTotal).toLocaleString('en-IN') : '0') + '</td>';
        tbody += '</tr>';
    });
    $('#stageWipMatrixTbody').html(tbody);

    // Build tfoot
    if (totalRow) {
        var tfoot = '<tr class="fw-bold" style="background-color: #fef08a;">';
        tfoot += '<td class="text-start ps-3">Total</td>';
        $.each(cols, function(i, c) {
            var val = totalRow[c] || 0;
            tfoot += '<td>' + (val > 0 ? Number(val).toLocaleString('en-IN') : '0') + '</td>';
        });
        tfoot += '<td class="text-danger fs-6">' + (totalRow['TOTAL'] > 0 ? Number(totalRow['TOTAL']).toLocaleString('en-IN') : '0') + '</td>';
        tfoot += '</tr>';
        $('#stageWipMatrixTfoot').html(tfoot);
    }
}

// Re-fetch whenever stage changes inside the tab
$(document).on('change', '#stage_wip_stage_select', function() {
    if (typeof window.showReportLoading === 'function') {
        window.showReportLoading(true);
    }
    window.initStageWiseWipTable();
});
</script>

<div class="card shadow-none border-0 mb-0">
    <!-- Credit Note Comparison Matrix Subheader / Summary Stats -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 p-3 bg-light rounded-3 mb-3 border">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-warning rounded-pill p-2"><i class="ri-refund-2-line fs-6"></i></span>
            <div>
                <h6 class="mb-0 fw-bold text-dark">Brand-wise Monthly Credit Note Comparison</h6>
                <small class="text-muted">Comparing total credit note return quantities across all finished goods brands</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2" id="cn_comparison_badges_container">
            <span class="badge bg-label-secondary px-3 py-2 fw-semibold" id="cn_badge_prev_year">Previous Year: -</span>
            <span class="badge bg-label-primary px-3 py-2 fw-bold" id="cn_badge_curr_year">Current Year: -</span>
            <span class="badge bg-label-info px-3 py-2 fw-semibold" id="cn_badge_total_brands">Brands: -</span>
        </div>
    </div>

    <!-- Dynamic Table Container -->
    <div class="card-datatable table-responsive position-relative" id="cnComparisonTableWrapper" style="min-height: 220px;">
        <table class="table table-bordered table-hover align-middle text-nowrap mb-0" id="creditNoteComparisonReportTable" style="width: 100%;">
            <thead></thead>
            <tbody></tbody>
            <tfoot></tfoot>
        </table>
    </div>
</div>

<script>
window.initCreditNoteComparisonReportTable = function() {
    $('#cnComparisonTableWrapper').find('#creditNoteComparisonReportTable_processing').remove();
    $('#cnComparisonTableWrapper').append('<div class="dataTables_processing card" id="creditNoteComparisonReportTable_processing" style="display: block;"><div><div></div><div></div><div></div><div></div></div></div>');
    if (typeof showReportLoading === 'function') {
        showReportLoading(true);
    }

    var selectedYear = $('#comparison_year_filter').val() || new Date().getFullYear();
    var customerId = $('select[name="customer_id"]').val();
    var agentId = $('select[name="agent_id"]').val();
    var brandId = $('select[name="brand_id"]').val();
    var fromDate = $('input[name="from_date"]').val();
    var toDate = $('input[name="to_date"]').val();

    $.ajax({
        url: "{{ url('sales_marketing_reports/ajax/credit-note-comparison-report') }}",
        type: 'GET',
        dataType: 'json',
        data: {
            comparison_year: selectedYear,
            from_date: fromDate,
            to_date: toDate,
            customer_id: customerId,
            agent_id: agentId,
            brand_id: brandId
        },
        success: function(res) {
            $('#creditNoteComparisonReportTable_processing').remove();
            if (typeof showReportLoading === 'function') {
                showReportLoading(false);
            }
            renderCnComparisonMatrix(res);
        },
        error: function(err) {
            $('#creditNoteComparisonReportTable_processing').remove();
            if (typeof showReportLoading === 'function') {
                showReportLoading(false);
            }
            console.error('Credit Note Comparison Report fetch error:', err);
            $('#creditNoteComparisonReportTable tbody').html('<tr><td class="text-center py-4 text-danger"><i class="ri-error-warning-line ri-2x d-block mb-1"></i>Failed to load credit note comparison data. Please try again.</td></tr>');
        }
    });
};

function formatCnComparisonNumber(num) {
    if (num === null || num === undefined || num === 0 || num === '0') {
        return '<span class="text-muted opacity-50">-</span>';
    }
    var val = parseFloat(num);
    if (isNaN(val) || val === 0) {
        return '<span class="text-muted opacity-50">-</span>';
    }
    return Number(val.toFixed(0)).toLocaleString('en-IN');
}

function formatCnComparisonValue(curr, prev) {
    var c = parseFloat(curr) || 0;
    var p = parseFloat(prev) || 0;

    if (c === 0 && p === 0) {
        return '<span class="text-muted opacity-50">-</span>';
    }

    var formatted = Number(c.toFixed(0)).toLocaleString('en-IN');
    if (c === 0) {
        formatted = '0';
    }

    if (c > p) {
        return '<span class="text-success fw-bold">' + formatted + '</span>';
    } else if (c < p) {
        return '<span class="text-danger fw-bold">' + formatted + '</span>';
    } else {
        return '<span class="text-dark fw-semibold">' + formatted + '</span>';
    }
}

function renderCnComparisonMatrix(data) {
    if ($.fn.DataTable.isDataTable('#creditNoteComparisonReportTable')) {
        try {
            $('#creditNoteComparisonReportTable').DataTable().destroy();
        } catch (e) {
            console.warn('DataTable destroy bypassed:', e);
        }
    }
    $('#cnComparisonTableWrapper').html('<table class="table table-bordered table-hover align-middle text-nowrap mb-0" id="creditNoteComparisonReportTable" style="width: 100%;"></table>');
    var table = $('#creditNoteComparisonReportTable');

    var prevYear = data.prev_year;
    var currYear = data.curr_year;
    var brands = data.brands || [];
    var months = data.months || [];
    var brandTotals = data.brand_totals || {};
    var grandTotal = data.grand_total || { prev: 0, curr: 0, growth: 0 };

    $('#cn_badge_prev_year').text('Prev Year: ' + prevYear);
    $('#cn_badge_curr_year').text('Selected Year: ' + currYear);
    $('#cn_badge_total_brands').text('Brands: ' + brands.length);
    if (data.date_range) {
        if ($('#cn_badge_date_range').length === 0) {
            $('#cn_comparison_badges_container').append('<span class="badge bg-label-warning px-3 py-2 fw-semibold" id="cn_badge_date_range"></span>');
        }
        $('#cn_badge_date_range').text('Range: ' + data.date_range).show();
    } else {
        $('#cn_badge_date_range').remove();
    }

    var thead = $('<thead></thead>');
    
    // Row 1: Header groupings
    var tr1 = $('<tr class="bg-light text-center border-bottom"></tr>');
    tr1.append('<th rowspan="2" class="align-middle text-start fw-bold px-3 bg-white" style="min-width: 130px; position: sticky; left: 0; z-index: 2; box-shadow: 2px 0 5px rgba(0,0,0,0.05);">Month</th>');

    brands.forEach(function(b) {
        tr1.append('<th colspan="2" class="text-center fw-bold py-2 px-3 border-start border-end" style="background: #fdf6ec; color: #7c2d12; font-size: 0.8rem; letter-spacing: 0.03em;">' + (b.name || 'Brand') + '</th>');
    });

    tr1.append('<th colspan="3" class="text-center fw-bold py-2 px-3 bg-warning text-dark border-start" style="font-size: 0.85rem; letter-spacing: 0.05em;">TOTAL RETURN QTY</th>');
    thead.append(tr1);

    // Row 2: Sub-columns for each brand and total
    var tr2 = $('<tr class="bg-white text-center border-bottom"></tr>');
    brands.forEach(function(b) {
        tr2.append('<th class="text-end small py-2 px-2 text-muted fw-semibold border-start" style="font-size: 0.75rem;">' + prevYear + ' Qty</th>');
        tr2.append('<th class="text-end small py-2 px-2 text-primary fw-bold border-end" style="font-size: 0.75rem;">' + currYear + ' Qty</th>');
    });

    tr2.append('<th class="text-end small py-2 px-2 fw-semibold bg-light" style="font-size: 0.75rem;">' + prevYear + ' Qty</th>');
    tr2.append('<th class="text-end small py-2 px-2 text-primary fw-bold bg-light" style="font-size: 0.75rem;">' + currYear + ' Qty</th>');
    tr2.append('<th class="text-center small py-2 px-2 fw-bold bg-light" style="font-size: 0.75rem;">Change %</th>');
    thead.append(tr2);

    table.append(thead);

    // 2. Build TBODY
    var tbody = $('<tbody></tbody>');
    if (!months.length) {
        var totalColspan = 1 + (brands.length * 2) + 3;
        tbody.append('<tr><td colspan="' + totalColspan + '" class="text-center py-5 text-muted"><i class="ri-inbox-line ri-2x d-block mb-2 text-secondary"></i>No credit note comparison records found for ' + prevYear + ' vs ' + currYear + '</td></tr>');
    } else {
        months.forEach(function(m) {
            var row = $('<tr></tr>');
            row.append('<td class="fw-bold px-3 bg-white text-dark" style="position: sticky; left: 0; z-index: 1; box-shadow: 2px 0 5px rgba(0,0,0,0.05);">' + m.month_name + '</td>');

            brands.forEach(function(b) {
                var bData = (m.brands && m.brands[b.id]) ? m.brands[b.id] : { prev_qty: 0, curr_qty: 0 };
                row.append('<td class="text-end px-2 border-start">' + formatCnComparisonNumber(bData.prev_qty) + '</td>');
                row.append('<td class="text-end px-2 border-end">' + formatCnComparisonValue(bData.curr_qty, bData.prev_qty) + '</td>');
            });

            row.append('<td class="text-end px-2 fw-semibold bg-light">' + formatCnComparisonNumber(m.total_prev) + '</td>');
            row.append('<td class="text-end px-2 bg-light">' + formatCnComparisonValue(m.total_curr, m.total_prev) + '</td>');

            var growthHtml = '-';
            var gVal = parseFloat(m.growth);
            if (m.total_prev === 0 && m.total_curr === 0) {
                growthHtml = '<span class="text-muted small">-</span>';
            } else if (gVal > 0) {
                growthHtml = '<span class="badge bg-label-success rounded-pill fw-bold"><i class="ri ri-arrow-up-s-line"></i> +' + gVal.toFixed(1) + '%</span>';
            } else if (gVal < 0) {
                growthHtml = '<span class="badge bg-label-danger rounded-pill fw-bold"><i class="ri ri-arrow-down-s-line"></i> ' + gVal.toFixed(1) + '%</span>';
            } else {
                growthHtml = '<span class="badge bg-label-secondary rounded-pill small">0%</span>';
            }
            row.append('<td class="text-center px-2 bg-light">' + growthHtml + '</td>');

            tbody.append(row);
        });
    }
    table.append(tbody);

    // 3. Build TFOOT
    var tfoot = $('<tfoot class="border-top border-2"></tfoot>');
    var trFoot = $('<tr class="bg-light fw-bold"></tr>');
    trFoot.append('<th class="text-uppercase fw-bold px-3 text-dark bg-light" style="position: sticky; left: 0; z-index: 2; box-shadow: 2px 0 5px rgba(0,0,0,0.05);">ANNUAL TOTAL</th>');

    brands.forEach(function(b) {
        var bt = brandTotals[b.id] || { prev: 0, curr: 0 };
        trFoot.append('<th class="text-end px-2 border-start text-dark fw-bold">' + formatCnComparisonNumber(bt.prev) + '</th>');
        trFoot.append('<th class="text-end px-2 border-end">' + formatCnComparisonValue(bt.curr, bt.prev) + '</th>');
    });

    trFoot.append('<th class="text-end px-2 fw-bold text-dark bg-light">' + formatCnComparisonNumber(grandTotal.prev) + '</th>');
    trFoot.append('<th class="text-end px-2 bg-light fs-6">' + formatCnComparisonValue(grandTotal.curr, grandTotal.prev) + '</th>');

    var grandGVal = parseFloat(grandTotal.growth);
    var grandGrowthHtml = '-';
    if (grandTotal.prev === 0 && grandTotal.curr === 0) {
        grandGrowthHtml = '-';
    } else if (grandGVal > 0) {
        grandGrowthHtml = '<span class="badge bg-success rounded-pill px-2 py-1 fw-bold fs-7"><i class="ri ri-arrow-up-s-line"></i> +' + grandGVal.toFixed(1) + '%</span>';
    } else if (grandGVal < 0) {
        grandGrowthHtml = '<span class="badge bg-danger rounded-pill px-2 py-1 fw-bold fs-7"><i class="ri ri-arrow-down-s-line"></i> ' + grandGVal.toFixed(1) + '%</span>';
    } else {
        grandGrowthHtml = '<span class="badge bg-secondary rounded-pill px-2 py-1 small">0%</span>';
    }
    trFoot.append('<th class="text-center px-2 bg-light">' + grandGrowthHtml + '</th>');
    tfoot.append(trFoot);
    table.append(tfoot);

    try {
        table.DataTable({
            processing: true,
            paging: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            searching: true,
            ordering: false,
            info: true,
            autoWidth: false,
            scrollX: true,
            dom: '<"row mb-2 align-items-center"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>rt<"row mt-2 align-items-center"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6 d-flex justify-content-md-end"p>>',
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search matrix...",
                lengthMenu: "Show _MENU_ entries"
            },
            buttons: [
                {
                    extend: 'excel',
                    className: 'buttons-excel d-none',
                    footer: true,
                    title: function () {
                        var currY = $('#comparison_year_filter').val() || new Date().getFullYear();
                        return 'Credit Note Comparison (' + currY + ' vs ' + (currY - 1) + ')';
                    },
                    exportOptions: {
                        columns: ':not(.no-export)',
                        format: {
                            body: function (data) {
                                if (typeof formatReportExportCell === 'function') {
                                    return formatReportExportCell(data);
                                }
                                if (typeof data === 'string') {
                                    var temp = $('<div>').html(data);
                                    temp.find('.no-export, .d-none, button, i, script').remove();
                                    return temp.text().trim();
                                }
                                return data;
                            },
                            footer: function (data) {
                                if (typeof data === 'string') {
                                    var temp = $('<div>').html(data);
                                    temp.find('.no-export, .d-none, button, i, script').remove();
                                    return temp.text().trim();
                                }
                                return data;
                            }
                        }
                    },
                    customize: function (xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];

                        var rows = $('row', sheet).toArray().reverse();
                        rows.forEach(function (rowEl) {
                            var r = parseInt($(rowEl).attr('r'), 10);
                            if (r >= 2) {
                                var newR = r + 1;
                                $(rowEl).attr('r', newR);
                                $(rowEl).find('c').each(function () {
                                    var cellRef = $(this).attr('r');
                                    if (cellRef) {
                                        var colLetter = cellRef.replace(/[0-9]/g, '');
                                        $(this).attr('r', colLetter + newR);
                                    }
                                });
                            }
                        });

                        function getColLetter(colIdx) {
                            var colName = '';
                            var dividend = colIdx + 1;
                            while (dividend > 0) {
                                var modulo = (dividend - 1) % 26;
                                colName = String.fromCharCode(65 + modulo) + colName;
                                dividend = Math.floor((dividend - modulo) / 26);
                            }
                            return colName;
                        }

                        var brandHeadersRow = '<row r="2">';
                        brandHeadersRow += '<c r="A2" t="inlineStr"><is><t>MONTH</t></is></c>';

                        var curColIdx = 1;
                        brands.forEach(function (b) {
                            var bName = b.name || 'Brand';
                            var col1 = getColLetter(curColIdx);
                            var col2 = getColLetter(curColIdx + 1);
                            brandHeadersRow += '<c r="' + col1 + '2" t="inlineStr"><is><t>' + bName + '</t></is></c>';
                            brandHeadersRow += '<c r="' + col2 + '2" t="inlineStr"><is><t></t></is></c>';
                            curColIdx += 2;
                        });

                        var totCol1 = getColLetter(curColIdx);
                        brandHeadersRow += '<c r="' + totCol1 + '2" t="inlineStr"><is><t>TOTAL RETURN QTY</t></is></c>';
                        brandHeadersRow += '</row>';

                        $('sheetData row[r="1"]', sheet).after(brandHeadersRow);

                        var mergeCells = $('mergeCells', sheet);
                        if (!mergeCells.length) {
                            mergeCells = $('<mergeCells></mergeCells>');
                            $('sheetData', sheet).after(mergeCells);
                        }

                        curColIdx = 1;
                        brands.forEach(function () {
                            var c1 = getColLetter(curColIdx);
                            var c2 = getColLetter(curColIdx + 1);
                            mergeCells.append($('<mergeCell ref="' + c1 + '2:' + c2 + '2"/>'));
                            curColIdx += 2;
                        });

                        var t1 = getColLetter(curColIdx);
                        var t3 = getColLetter(curColIdx + 2);
                        mergeCells.append($('<mergeCell ref="' + t1 + '2:' + t3 + '2"/>'));
                        mergeCells.attr('count', $('mergeCell', mergeCells).length);
                    }
                },
                {
                    extend: 'pdfHtml5',
                    className: 'buttons-pdf d-none',
                    footer: true,
                    orientation: 'landscape',
                    pageSize: 'A3',
                    title: function () {
                        var currY = $('#comparison_year_filter').val() || new Date().getFullYear();
                        return 'Credit Note Comparison (' + currY + ' vs ' + (currY - 1) + ')';
                    },
                    exportOptions: {
                        columns: ':not(.no-export)',
                        format: {
                            body: function (data) {
                                if (typeof formatReportExportCell === 'function') {
                                    return formatReportExportCell(data);
                                }
                                if (typeof data === 'string') {
                                    var temp = $('<div>').html(data);
                                    temp.find('.no-export, .d-none, button, i, script').remove();
                                    return temp.text().trim();
                                }
                                return data;
                            },
                            footer: function (data) {
                                if (typeof data === 'string') {
                                    var temp = $('<div>').html(data);
                                    temp.find('.no-export, .d-none, button, i, script').remove();
                                    return temp.text().trim();
                                }
                                return data;
                            }
                        }
                    },
                    customize: function (doc) {
                        doc.defaultStyle.fontSize = 7;
                        doc.styles.tableHeader.fontSize = 7.5;
                        doc.styles.tableFooter.fontSize = 7.5;
                        doc.pageMargins = [10, 15, 10, 15];
                    }
                },
                {
                    extend: 'print',
                    className: 'buttons-print d-none',
                    footer: true,
                    title: function () {
                        var currY = $('#comparison_year_filter').val() || new Date().getFullYear();
                        return 'Credit Note Comparison (' + currY + ' vs ' + (currY - 1) + ')';
                    },
                    exportOptions: {
                        columns: ':not(.no-export)',
                        format: {
                            body: function (data) {
                                return typeof formatReportExportCell === 'function' ? formatReportExportCell(data) : data;
                            },
                            footer: function (data) {
                                return typeof formatReportExportCell === 'function' ? formatReportExportCell(data) : data;
                            }
                        }
                    }
                }
            ]
        });
    } catch (e) {
        console.warn('DataTable init notice:', e);
    }
}

$(document).on('change', '#comparison_year_filter', function() {
    if ($('#report_type_select').val() === 'credit-note-comparison-report') {
        window.initCreditNoteComparisonReportTable();
    }
});
</script>


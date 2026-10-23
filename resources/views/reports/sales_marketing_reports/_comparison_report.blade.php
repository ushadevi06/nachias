<div class="card shadow-none border-0 mb-0">
    <!-- Comparison Matrix Subheader / Summary Stats -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 p-3 bg-light rounded-3 mb-3 border">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary rounded-pill p-2"><i class="ri-bar-chart-grouped-line fs-6"></i></span>
            <div>
                <h6 class="mb-0 fw-bold text-dark">Brand-wise Monthly Sales Comparison</h6>
                <small class="text-muted">Comparing total invoice quantities across all finished goods brands</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2" id="comparison_badges_container">
            <span class="badge bg-label-secondary px-3 py-2 fw-semibold" id="badge_prev_year">Previous Year: -</span>
            <span class="badge bg-label-primary px-3 py-2 fw-bold" id="badge_curr_year">Current Year: -</span>
            <span class="badge bg-label-info px-3 py-2 fw-semibold" id="badge_total_brands">Brands: -</span>
        </div>
    </div>

    <!-- Dynamic Table Container -->
    <div class="card-datatable table-responsive">
        <table class="datatables-products table table-bordered table-hover align-middle text-nowrap mb-0" id="comparisonReportTable" style="width: 100%;">
            <thead></thead>
            <tbody></tbody>
            <tfoot></tfoot>
        </table>
    </div>
</div>

<script>
window.initComparisonReportTable = function() {
    if (typeof showReportLoading === 'function') {
        showReportLoading(true);
    }

    var selectedYear = $('#comparison_year_filter').val() || new Date().getFullYear();
    var customerId = $('select[name="customer_id"]').val();
    var agentId = $('select[name="agent_id"]').val();
    var brandId = $('select[name="brand_id"]').val();

    $.ajax({
        url: "{{ url('sales_marketing_reports/ajax/comparison-report') }}",
        type: 'GET',
        dataType: 'json',
        data: {
            comparison_year: selectedYear,
            customer_id: customerId,
            agent_id: agentId,
            brand_id: brandId
        },
        success: function(res) {
            if (typeof showReportLoading === 'function') {
                showReportLoading(false);
            }
            renderComparisonMatrix(res);
        },
        error: function(err) {
            if (typeof showReportLoading === 'function') {
                showReportLoading(false);
            }
            console.error('Comparison Report fetch error:', err);
            $('#comparisonReportTable tbody').html('<tr><td class="text-center py-4 text-danger"><i class="ri-error-warning-line ri-2x d-block mb-1"></i>Failed to load sales comparison data. Please try again.</td></tr>');
        }
    });
};

function formatComparisonNumber(num) {
    if (num === null || num === undefined || num === 0 || num === '0') {
        return '<span class="text-muted opacity-50">-</span>';
    }
    var val = parseFloat(num);
    if (isNaN(val) || val === 0) {
        return '<span class="text-muted opacity-50">-</span>';
    }
    // Format with commas, 0 decimals for integer pcs
    return Number(val.toFixed(0)).toLocaleString('en-IN');
}

function renderComparisonMatrix(data) {
    var table = $('#comparisonReportTable');
    if ($.fn.DataTable.isDataTable('#comparisonReportTable')) {
        table.DataTable().clear().destroy();
    }
    table.empty();

    var prevYear = data.prev_year;
    var currYear = data.curr_year;
    var brands = data.brands || [];
    var months = data.months || [];
    var brandTotals = data.brand_totals || {};
    var grandTotal = data.grand_total || { prev: 0, curr: 0, growth: 0 };

    // Update summary badges
    $('#badge_prev_year').text('Prev Year: ' + prevYear);
    $('#badge_curr_year').text('Selected Year: ' + currYear);
    $('#badge_total_brands').text('Brands: ' + brands.length);

    // 1. Build THEAD
    var thead = $('<thead></thead>');
    
    // Header Row 1: Brand Groups & Total Group
    var tr1 = $('<tr class="bg-light text-center border-bottom"></tr>');
    tr1.append('<th rowspan="2" class="align-middle text-start fw-bold px-3 bg-white" style="min-width: 130px; position: sticky; left: 0; z-index: 2; box-shadow: 2px 0 5px rgba(0,0,0,0.05);">Month</th>');

    brands.forEach(function(b) {
        tr1.append('<th colspan="2" class="text-center fw-bold py-2 px-3 border-start border-end" style="background: #f1f5f9; color: #1e293b; font-size: 0.8rem; letter-spacing: 0.03em;">' + (b.name || 'Brand') + '</th>');
    });

    tr1.append('<th colspan="3" class="text-center fw-bold py-2 px-3 bg-primary text-white border-start" style="font-size: 0.85rem; letter-spacing: 0.05em;">TOTAL QTY</th>');
    thead.append(tr1);

    // Header Row 2: Prev Year Qty & Curr Year Qty subheaders
    var tr2 = $('<tr class="bg-white text-center border-bottom"></tr>');
    brands.forEach(function(b) {
        tr2.append('<th class="text-end small py-2 px-2 text-muted fw-semibold border-start" style="font-size: 0.75rem;">' + prevYear + ' Qty</th>');
        tr2.append('<th class="text-end small py-2 px-2 text-primary fw-bold border-end" style="font-size: 0.75rem;">' + currYear + ' Qty</th>');
    });

    tr2.append('<th class="text-end small py-2 px-2 fw-semibold bg-light" style="font-size: 0.75rem;">' + prevYear + ' Qty</th>');
    tr2.append('<th class="text-end small py-2 px-2 text-primary fw-bold bg-light" style="font-size: 0.75rem;">' + currYear + ' Qty</th>');
    tr2.append('<th class="text-center small py-2 px-2 fw-bold bg-light" style="font-size: 0.75rem;">Growth %</th>');
    thead.append(tr2);

    table.append(thead);

    // 2. Build TBODY
    var tbody = $('<tbody></tbody>');
    if (!months.length) {
        var totalColspan = 1 + (brands.length * 2) + 3;
        tbody.append('<tr><td colspan="' + totalColspan + '" class="text-center py-5 text-muted"><i class="ri-inbox-line ri-2x d-block mb-2 text-secondary"></i>No sales comparison records found for ' + prevYear + ' vs ' + currYear + '</td></tr>');
    } else {
        months.forEach(function(m) {
            var row = $('<tr></tr>');
            row.append('<td class="fw-bold px-3 bg-white text-dark" style="position: sticky; left: 0; z-index: 1; box-shadow: 2px 0 5px rgba(0,0,0,0.05);">' + m.month_name + '</td>');

            brands.forEach(function(b) {
                var bData = (m.brands && m.brands[b.id]) ? m.brands[b.id] : { prev_qty: 0, curr_qty: 0 };
                row.append('<td class="text-end px-2 border-start">' + formatComparisonNumber(bData.prev_qty) + '</td>');
                row.append('<td class="text-end px-2 border-end fw-semibold text-primary">' + formatComparisonNumber(bData.curr_qty) + '</td>');
            });

            // Month Totals
            row.append('<td class="text-end px-2 fw-semibold bg-light">' + formatComparisonNumber(m.total_prev) + '</td>');
            row.append('<td class="text-end px-2 fw-bold text-primary bg-light">' + formatComparisonNumber(m.total_curr) + '</td>');

            // Growth %
            var growthHtml = '-';
            var gVal = parseFloat(m.growth);
            if (m.total_prev === 0 && m.total_curr === 0) {
                growthHtml = '<span class="text-muted small">-</span>';
            } else if (gVal > 0) {
                growthHtml = '<span class="badge bg-label-success rounded-pill fw-bold"><i class="ri-arrow-up-s-line"></i> +' + gVal.toFixed(1) + '%</span>';
            } else if (gVal < 0) {
                growthHtml = '<span class="badge bg-label-danger rounded-pill fw-bold"><i class="ri-arrow-down-s-line"></i> ' + gVal.toFixed(1) + '%</span>';
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
        trFoot.append('<th class="text-end px-2 border-start text-dark fw-bold">' + formatComparisonNumber(bt.prev) + '</th>');
        trFoot.append('<th class="text-end px-2 border-end text-primary fw-bold">' + formatComparisonNumber(bt.curr) + '</th>');
    });

    trFoot.append('<th class="text-end px-2 fw-bold text-dark bg-light">' + formatComparisonNumber(grandTotal.prev) + '</th>');
    trFoot.append('<th class="text-end px-2 fw-bold text-primary bg-light fs-6">' + formatComparisonNumber(grandTotal.curr) + '</th>');

    var grandGVal = parseFloat(grandTotal.growth);
    var grandGrowthHtml = '-';
    if (grandTotal.prev === 0 && grandTotal.curr === 0) {
        grandGrowthHtml = '-';
    } else if (grandGVal > 0) {
        grandGrowthHtml = '<span class="badge bg-success rounded-pill px-2 py-1 fw-bold fs-7"><i class="ri-arrow-up-s-line"></i> +' + grandGVal.toFixed(1) + '%</span>';
    } else if (grandGVal < 0) {
        grandGrowthHtml = '<span class="badge bg-danger rounded-pill px-2 py-1 fw-bold fs-7"><i class="ri-arrow-down-s-line"></i> ' + grandGVal.toFixed(1) + '%</span>';
    } else {
        grandGrowthHtml = '<span class="badge bg-secondary rounded-pill px-2 py-1 small">0%</span>';
    }
    trFoot.append('<th class="text-center px-2 bg-light">' + grandGrowthHtml + '</th>');
    tfoot.append(trFoot);
    table.append(tfoot);

    // Initialize DataTable on the rendered table
    table.DataTable({
        paging: false,
        searching: true,
        ordering: false,
        info: false,
        autoWidth: false,
        scrollX: true,
        dom: '<"row mb-2 align-items-center"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6 d-flex justify-content-md-end"f>>rt',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search matrix..."
        }
    });
}
</script>

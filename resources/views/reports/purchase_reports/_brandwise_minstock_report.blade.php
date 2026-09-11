<style>
    .brandwise-table-container {
        max-height: 72vh;
        overflow: auto;
        position: relative;
        border-radius: 8px;
    }

    /* Sticky Headers */
    .brandwise-table-container table thead tr:nth-child(1) th {
        position: sticky;
        top: 0;
        z-index: 15;
        background-color: #f1f5f9;
        color: #1e293b;
        border-bottom: 1px solid #cbd5e1;
    }

    .brandwise-table-container table thead tr:nth-child(2) th {
        position: sticky;
        top: 38px;
        z-index: 14;
        background-color: #f8fafc;
        color: #334155;
        border-bottom: 2px solid #cbd5e1;
    }

    /* Light Grey Header Backgrounds */
    .brandwise-table-container table thead tr:nth-child(1) th.bg-fs-header {
        background-color: #cbd5e1 !important;
        color: #0f172a !important;
    }
    .brandwise-table-container table thead tr:nth-child(1) th.bg-hs-header {
        background-color: #e2e8f0 !important;
        color: #0f172a !important;
    }
    .brandwise-table-container table thead tr:nth-child(1) th.bg-gross-header {
        background-color: #94a3b8 !important;
        color: #ffffff !important;
    }
    .brandwise-table-container table thead tr:nth-child(2) th.bg-tl-header {
        background-color: #cbd5e1 !important;
        color: #0f172a !important;
    }

    /* Sticky Left Columns (S.NO & PRODUCT) */
    .sticky-col-sno {
        position: sticky;
        left: 0;
        width: 50px;
        min-width: 50px;
        max-width: 50px;
        box-shadow: 2px 0 4px -1px rgba(0,0,0,0.12);
    }

    .sticky-col-product {
        position: sticky;
        left: 50px;
        width: 140px;
        min-width: 140px;
        max-width: 140px;
        box-shadow: 3px 0 5px -1px rgba(0,0,0,0.15);
    }

    /* Top Left Corner Intersection Z-Index */
    .brandwise-table-container table thead tr:nth-child(1) th.sticky-col-sno {
        top: 0 !important;
        left: 0 !important;
        z-index: 30 !important;
        background-color: #f1f5f9 !important;
    }

    .brandwise-table-container table thead tr:nth-child(1) th.sticky-col-product {
        top: 0 !important;
        left: 50px !important;
        z-index: 30 !important;
        background-color: #f1f5f9 !important;
    }

    /* Sticky Body Cells */
    .brandwise-table-container table tbody td.sticky-col-sno,
    .brandwise-table-container table tbody td.sticky-col-product {
        z-index: 8;
        background-color: #ffffff !important;
    }
</style>

<div class="d-flex align-items-center justify-content-end mb-3 px-1">
    <div class="dataTables_filter">
        <label class="d-inline-flex align-items-center gap-2 small fw-semibold text-muted">
            Search:
            <input type="search" id="brandwise_search_input" class="form-control form-control-sm" placeholder="" style="width: 220px;">
        </label>
    </div>
</div>

<div class="table-responsive border rounded-3 shadow-sm mb-3 brandwise-table-container">
    <table class="table table-bordered align-middle text-center mb-0 datatables-brandwise-minstock" style="min-width: 1900px; font-size: 0.82rem;">
        <thead class="table-light">
            <tr>
                <th rowspan="2" class="align-middle sticky-col-sno" style="width: 50px;">S.NO</th>
                <th rowspan="2" class="align-middle text-start sticky-col-product" style="min-width: 140px;">PRODUCT</th>
                <th rowspan="2" class="align-middle text-end" style="min-width: 100px;">ORDER FABRIC</th>
                <th rowspan="2" class="align-middle text-end" style="min-width: 100px;">FABRIC STOCK</th>
                <th rowspan="2" class="align-middle text-end dhoti-col" style="min-width: 100px;">DHOTI STOCK</th>
                <th rowspan="2" class="align-middle text-end dhoti-col" style="min-width: 100px;">DHOTI REORDER</th>
                <th rowspan="2" class="align-middle text-end dhoti-col" style="min-width: 100px;">DHOTI PENDING</th>
                <th rowspan="2" class="align-middle" style="min-width: 65px;">STOCK</th>
                <th colspan="9" class="text-center bg-fs-header py-2 fw-bold">F/S</th>
                <th colspan="9" class="text-center bg-hs-header py-2 fw-bold">H/S</th>
                <th rowspan="2" class="align-middle bg-gross-header fw-bold" style="min-width: 90px;">GROSS TOT</th>
                <th rowspan="2" class="align-middle" style="min-width: 110px;">UNIT</th>
                <th rowspan="2" class="align-middle" style="min-width: 100px;">C.NO</th>
                <th rowspan="2" class="align-middle text-start" style="min-width: 140px;">REMARKS</th>
            </tr>
            <tr class="bg-light text-dark">
                <!-- F/S Sizes -->
                <th style="min-width: 42px;">36</th>
                <th style="min-width: 42px;">38</th>
                <th style="min-width: 42px;">40</th>
                <th style="min-width: 42px;">42</th>
                <th style="min-width: 42px;">44</th>
                <th style="min-width: 42px;">46</th>
                <th style="min-width: 42px;">48</th>
                <th style="min-width: 42px;">50</th>
                <th style="min-width: 50px;" class="bg-tl-header fw-bold">T/L</th>
                <!-- H/S Sizes -->
                <th style="min-width: 42px;">36</th>
                <th style="min-width: 42px;">38</th>
                <th style="min-width: 42px;">40</th>
                <th style="min-width: 42px;">42</th>
                <th style="min-width: 42px;">44</th>
                <th style="min-width: 42px;">46</th>
                <th style="min-width: 42px;">48</th>
                <th style="min-width: 42px;">50</th>
                <th style="min-width: 50px;" class="bg-tl-header fw-bold">T/L</th>
            </tr>
        </thead>
        <tbody id="brandwise-minstock-tbody">
            <tr>
                <td colspan="28" class="text-center text-muted py-4">Select a Brand to view Brandwise Minimum Stock report</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Dynamic AJAX Pagination Controls -->
<div class="d-flex align-items-center justify-content-between mt-3 px-2" id="brandwise-minstock-pagination-container">
    <div class="small text-muted fw-semibold" id="brandwise-minstock-info">
        Showing 0 to 0 of 0 entries
    </div>
    <nav aria-label="Brandwise Report Pagination">
        <ul class="pagination pagination-sm mb-0" id="brandwise-minstock-pagination">
        </ul>
    </nav>
</div>

<script>
    $(document).ready(function() {
        let currentStart = 0;
        const pageLength = 10;

        window.loadBrandwiseMinStockTable = function(start = 0, length = pageLength) {
            currentStart = start;
            const $table = $('.datatables-brandwise-minstock');
            if (!$table.length) return;

            // Auto-select CASINO DHOTI SHIRTS if no brand is chosen
            let brandSelect = $('select[name="brand_id"]');
            if (!brandSelect.val()) {
                let casinoOption = brandSelect.find('option').filter(function() {
                    return $(this).text().toUpperCase().indexOf('CASINO') !== -1;
                }).first();

                if (casinoOption.length) {
                    brandSelect.val(casinoOption.val()).trigger('change.select2');
                }
            }

            const brandId = brandSelect.val();
            const selectedBrandText = (brandSelect.find('option:selected').text() || '').toUpperCase();
            const isDhotiBrand = selectedBrandText.indexOf('CASINO DHOTI SHIRTS') !== -1;

            if (isDhotiBrand) {
                $('th.dhoti-col').show();
            } else {
                $('th.dhoti-col').hide();
            }

            const fromDate = $('.start_date').val();
            const toDate = $('.end_date').val();
            const supplierId = $('select[name="supplier_id"]').val();
            const searchQuery = $('#brandwise_search_input').val();

            const colSpanVal = isDhotiBrand ? 28 : 25;

            $.ajax({
                url: window.location.pathname,
                type: 'GET',
                data: {
                    report_type: 'brandwise-minstock-report',
                    brand_id: brandId,
                    from_date: fromDate,
                    to_date: toDate,
                    supplier_id: supplierId,
                    search: searchQuery,
                    start: start,
                    length: length
                },
                beforeSend: function() {
                    $('#brandwise-minstock-tbody').html(`<tr><td colspan="${colSpanVal}" class="text-center py-4"><div class="spinner-border text-secondary spinner-border-sm me-2" role="status"></div><span class="text-muted">Loading Brandwise Minimum Stock Matrix...</span></td></tr>`);
                    $('#brandwise-minstock-pagination').empty();
                    $('#brandwise-minstock-info').text('Loading...');
                },
                success: function(response) {
                    if (!response || !response.data || response.data.length === 0) {
                        $('#brandwise-minstock-tbody').html(`<tr><td colspan="${colSpanVal}" class="text-center text-muted py-4"><i class="ri ri-inbox-line fs-4 d-block mb-1"></i>No matching data found for the selected brand/search filter.</td></tr>`);
                        $('#brandwise-minstock-info').text('Showing 0 to 0 of 0 entries');
                        return;
                    }

                    const totalRecords = response.recordsFiltered || response.data.length;
                    const end = Math.min(start + length, totalRecords);

                    $('#brandwise-minstock-info').text(`Showing ${start + 1} to ${end} of ${totalRecords} entries`);

                    let html = '';
                    response.data.forEach(function(item, index) {
                        const rowNumber = start + index + 1;
                        const min = item.matrix.min;
                        const fg = item.matrix.fg;
                        const wips = (item.matrix.wips && item.matrix.wips.length > 0) ? item.matrix.wips : [{
                            label: 'WIP-1',
                            unit: '-',
                            c_no: '-',
                            remarks: '-',
                            fs: {},
                            hs: {},
                            fs_tl: 0,
                            hs_tl: 0,
                            gross_total: 0
                        }];
                        const total = item.matrix.total;
                        const totalRowspan = 2 + wips.length + 1; // 1(MIN) + 1(FG) + wips.length + 1(TOTAL)

                        function renderCell(val, minVal, isTotalRow = false) {
                            val = parseInt(val) || 0;
                            minVal = parseInt(minVal) || 0;
                            if (isTotalRow && val < minVal && minVal > 0) {
                                return `<span class="badge bg-danger text-white px-2 py-1 fw-bold">${val}</span>`;
                            }
                            if (val > 0) {
                                return val;
                            }
                            return isTotalRow ? '0' : '';
                        }

                        // Row 1: MIN
                        html += `
                        <tr class="border-top border-secondary">
                            <td rowspan="${totalRowspan}" class="align-middle fw-bold sticky-col-sno">${rowNumber}</td>
                            <td rowspan="${totalRowspan}" class="align-middle fw-bold text-dark text-start sticky-col-product">${item.art_no}</td>
                            <td rowspan="${totalRowspan}" class="align-middle fw-bold text-end bg-white">${item.order_fabric}</td>
                            <td rowspan="${totalRowspan}" class="align-middle fw-bold text-end bg-white">${item.fabric_stock}</td>
                            <td rowspan="${totalRowspan}" class="align-middle fw-bold text-end bg-white dhoti-col">${item.dhoti_stock}</td>
                            <td rowspan="${totalRowspan}" class="align-middle fw-bold text-end bg-white dhoti-col">${item.dhoti_reorder}</td>
                            <td rowspan="${totalRowspan}" class="align-middle fw-bold text-end bg-white dhoti-col">${item.dhoti_pending}</td>
                            <td class="fw-bold bg-light text-dark">MIN</td>
                            <td>${renderCell(min.fs[36])}</td>
                            <td>${renderCell(min.fs[38])}</td>
                            <td>${renderCell(min.fs[40])}</td>
                            <td>${renderCell(min.fs[42])}</td>
                            <td>${renderCell(min.fs[44])}</td>
                            <td>${renderCell(min.fs[46])}</td>
                            <td>${renderCell(min.fs[48])}</td>
                            <td>${renderCell(min.fs[50])}</td>
                            <td class="fw-bold bg-light text-dark">${renderCell(min.fs_tl)}</td>
                            <td>${renderCell(min.hs[36])}</td>
                            <td>${renderCell(min.hs[38])}</td>
                            <td>${renderCell(min.hs[40])}</td>
                            <td>${renderCell(min.hs[42])}</td>
                            <td>${renderCell(min.hs[44])}</td>
                            <td>${renderCell(min.hs[46])}</td>
                            <td>${renderCell(min.hs[48])}</td>
                            <td>${renderCell(min.hs[50])}</td>
                            <td class="fw-bold bg-light text-dark">${renderCell(min.hs_tl)}</td>
                            <td class="fw-bold bg-light text-dark">${renderCell(min.gross_total)}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        `;

                        // Row 2: FG
                        html += `
                        <tr>
                            <td class="fw-bold bg-light text-dark">FG</td>
                            <td>${renderCell(fg.fs[36])}</td>
                            <td>${renderCell(fg.fs[38])}</td>
                            <td>${renderCell(fg.fs[40])}</td>
                            <td>${renderCell(fg.fs[42])}</td>
                            <td>${renderCell(fg.fs[44])}</td>
                            <td>${renderCell(fg.fs[46])}</td>
                            <td>${renderCell(fg.fs[48])}</td>
                            <td>${renderCell(fg.fs[50])}</td>
                            <td class="fw-bold bg-light text-dark">${renderCell(fg.fs_tl)}</td>
                            <td>${renderCell(fg.hs[36])}</td>
                            <td>${renderCell(fg.hs[38])}</td>
                            <td>${renderCell(fg.hs[40])}</td>
                            <td>${renderCell(fg.hs[42])}</td>
                            <td>${renderCell(fg.hs[44])}</td>
                            <td>${renderCell(fg.hs[46])}</td>
                            <td>${renderCell(fg.hs[48])}</td>
                            <td>${renderCell(fg.hs[50])}</td>
                            <td class="fw-bold bg-light text-dark">${renderCell(fg.hs_tl)}</td>
                            <td class="fw-bold bg-light text-dark">${renderCell(fg.gross_total)}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        `;

                        // Row 3+: WIP Rows (WIP-1, WIP-2, ...)
                        wips.forEach(function(wip) {
                            html += `
                            <tr>
                                <td class="fw-bold bg-light text-dark text-nowrap">${wip.label}</td>
                                <td>${renderCell(wip.fs[36])}</td>
                                <td>${renderCell(wip.fs[38])}</td>
                                <td>${renderCell(wip.fs[40])}</td>
                                <td>${renderCell(wip.fs[42])}</td>
                                <td>${renderCell(wip.fs[44])}</td>
                                <td>${renderCell(wip.fs[46])}</td>
                                <td>${renderCell(wip.fs[48])}</td>
                                <td>${renderCell(wip.fs[50])}</td>
                                <td class="fw-bold bg-light text-dark">${renderCell(wip.fs_tl)}</td>
                                <td>${renderCell(wip.hs[36])}</td>
                                <td>${renderCell(wip.hs[38])}</td>
                                <td>${renderCell(wip.hs[40])}</td>
                                <td>${renderCell(wip.hs[42])}</td>
                                <td>${renderCell(wip.hs[44])}</td>
                                <td>${renderCell(wip.hs[46])}</td>
                                <td>${renderCell(wip.hs[48])}</td>
                                <td>${renderCell(wip.hs[50])}</td>
                                <td class="fw-bold bg-light text-dark">${renderCell(wip.hs_tl)}</td>
                                <td class="fw-bold bg-light text-dark">${renderCell(wip.gross_total)}</td>
                                <td class="text-nowrap fw-semibold bg-white">${wip.unit || '-'}</td>
                                <td class="text-nowrap fw-semibold bg-white">${wip.c_no || '-'}</td>
                                <td class="text-start small text-break bg-white">${wip.remarks || '-'}</td>
                            </tr>
                            `;
                        });

                        // Row 4: TOTAL
                        html += `
                        <tr class="bg-light fw-bold">
                            <td class="fw-bold text-dark">TOTAL</td>
                            <td>${renderCell(total.fs[36], min.fs[36], true)}</td>
                            <td>${renderCell(total.fs[38], min.fs[38], true)}</td>
                            <td>${renderCell(total.fs[40], min.fs[40], true)}</td>
                            <td>${renderCell(total.fs[42], min.fs[42], true)}</td>
                            <td>${renderCell(total.fs[44], min.fs[44], true)}</td>
                            <td>${renderCell(total.fs[46], min.fs[46], true)}</td>
                            <td>${renderCell(total.fs[48], min.fs[48], true)}</td>
                            <td>${renderCell(total.fs[50], min.fs[50], true)}</td>
                            <td class="fw-bold bg-light text-dark">${renderCell(total.fs_tl, min.fs_tl, true)}</td>
                            <td>${renderCell(total.hs[36], min.hs[36], true)}</td>
                            <td>${renderCell(total.hs[38], min.hs[38], true)}</td>
                            <td>${renderCell(total.hs[40], min.hs[40], true)}</td>
                            <td>${renderCell(total.hs[42], min.hs[42], true)}</td>
                            <td>${renderCell(total.hs[44], min.hs[44], true)}</td>
                            <td>${renderCell(total.hs[46], min.hs[46], true)}</td>
                            <td>${renderCell(total.hs[48], min.hs[48], true)}</td>
                            <td>${renderCell(total.hs[50], min.hs[50], true)}</td>
                            <td class="fw-bold bg-light text-dark">${renderCell(total.hs_tl, min.hs_tl, true)}</td>
                            <td class="fw-bold bg-light text-dark">${renderCell(total.gross_total, min.gross_total, true)}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        `;
                    });

                    $('#brandwise-minstock-tbody').html(html);

                    if (isDhotiBrand) {
                        $('.dhoti-col').show();
                    } else {
                        $('.dhoti-col').hide();
                    }

                    // Render Truncated Pagination Controls (Max 5 visible pages)
                    const totalPages = Math.ceil(totalRecords / length);
                    const currentPage = Math.floor(start / length) + 1;
                    let pagHtml = '';

                    // Previous Button
                    pagHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="javascript:;" onclick="window.loadBrandwiseMinStockTable(${Math.max(0, start - length)}, ${length})">Previous</a>
                    </li>`;

                    const maxVisible = 5;
                    let startPage = Math.max(1, currentPage - 2);
                    let endPage = Math.min(totalPages, startPage + maxVisible - 1);
                    if (endPage - startPage < maxVisible - 1) {
                        startPage = Math.max(1, endPage - maxVisible + 1);
                    }

                    if (startPage > 1) {
                        pagHtml += `<li class="page-item"><a class="page-link" href="javascript:;" onclick="window.loadBrandwiseMinStockTable(0, ${length})">1</a></li>`;
                        if (startPage > 2) {
                            pagHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                        }
                    }

                    for (let p = startPage; p <= endPage; p++) {
                        const pageStart = (p - 1) * length;
                        pagHtml += `<li class="page-item ${p === currentPage ? 'active' : ''}">
                            <a class="page-link" href="javascript:;" onclick="window.loadBrandwiseMinStockTable(${pageStart}, ${length})">${p}</a>
                        </li>`;
                    }

                    if (endPage < totalPages) {
                        if (endPage < totalPages - 1) {
                            pagHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                        }
                        const lastStart = (totalPages - 1) * length;
                        pagHtml += `<li class="page-item"><a class="page-link" href="javascript:;" onclick="window.loadBrandwiseMinStockTable(${lastStart}, ${length})">${totalPages}</a></li>`;
                    }

                    // Next Button
                    pagHtml += `<li class="page-item ${currentPage >= totalPages ? 'disabled' : ''}">
                        <a class="page-link" href="javascript:;" onclick="window.loadBrandwiseMinStockTable(${start + length}, ${length})">Next</a>
                    </li>`;

                    $('#brandwise-minstock-pagination').html(pagHtml);
                },
                error: function() {
                    $('#brandwise-minstock-tbody').html('<tr><td colspan="27" class="text-center text-danger py-4">Error loading report data. Please try again.</td></tr>');
                    $('#brandwise-minstock-info').text('Error loading data');
                }
            });
        };

        $(document).on('keyup input', '#brandwise_search_input', function() {
            clearTimeout(window.brandwiseSearchTimer);
            window.brandwiseSearchTimer = setTimeout(function() {
                window.loadBrandwiseMinStockTable(0, pageLength);
            }, 300);
        });

        if ($('#report_type_select').val() === 'brandwise-minstock-report') {
            window.loadBrandwiseMinStockTable(0, pageLength);
        }
    });
</script>

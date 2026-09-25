<!-- Breadcrumb Bar for Casino PO Drilldown -->
<div class="mb-3 d-flex align-items-center" id="casinoDrilldownBreadcrumbs" style="display: none !important;">
    <button class="btn btn-sm btn-outline-secondary me-2 rounded-pill px-3" onclick="renderCasinoPoLevel()" id="btnBackToCasinoPo">
        <i class="ri ri-arrow-left-line me-1"></i> Back to Casino PO Report
    </button>
    <span class="text-dark fw-bold" id="casinoDrilldownBreadcrumbText">All Casino Purchase Orders</span>
</div>

<!-- Level 1: Main Casino PO Summary Table -->
<div id="casinoPoMainContainer" class="table-responsive">
    <table class="table premium-table datatables-casino-po mb-0">
        <thead>
            <tr>
                <th style="width: 45px;" class="text-center">#</th>
                <th class="text-start ps-3">PRODUCT GROUP</th>
                <th class="text-center">WIDTH</th>
                <th class="text-end">PLAIN METERS</th>
                <th class="text-end">PRINT METERS</th>
                <th class="text-end">CHECKED METERS</th>
                <th class="text-end">STRIPED METERS</th>
                <th class="text-end">TOTAL METERS</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="fw-bold" style="background: #f1f5f9;">
                <td colspan="3" class="text-end">TOTAL</td>
                <td id="footer-casino-plain" class="text-end">0.00</td>
                <td id="footer-casino-print" class="text-end">0.00</td>
                <td id="footer-casino-checked" class="text-end">0.00</td>
                <td id="footer-casino-striped" class="text-end">0.00</td>
                <td id="footer-casino-total" class="text-end">0.00</td>
            </tr>
        </tfoot>
    </table>
</div>

<!-- Level 2: Casino PO Drilldown Table (PR Range Breakdown) -->
<div id="casinoDrilldownContainer" class="table-responsive" style="display: none;">
    <div class="card shadow-sm border mb-3">
        <div class="card-body p-0">
            <table id="casinoDrilldownTable" class="table premium-table table-hover mb-0" style="width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 60px;">#</th>
                        <th>PR RANGE</th>
                        <th class="text-center" style="width: 180px;">DESIGN COUNT</th>
                        <th class="text-end" style="width: 220px;">METERS</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr class="fw-bold" style="background: #f1f5f9;">
                        <td colspan="2" class="text-end">TOTAL</td>
                        <td id="footer-casino-drilldown-designs" class="text-center">0</td>
                        <td id="footer-casino-drilldown-meters" class="text-end">0.00</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
    function renderCasinoPoLevel() {
        $('#casinoDrilldownBreadcrumbs').attr('style', 'display: none !important;');
        $('#casinoDrilldownContainer').hide();
        $('#casinoPoMainContainer').show();

        if ($.fn.DataTable.isDataTable('.datatables-casino-po')) {
            $('.datatables-casino-po').DataTable().columns.adjust();
        }
    }

    function getCasinoFilterData(extraParams) {
        let fromDate = $('.start_date').val();
        let toDate = $('.end_date').val();
        let rangeVal = $('#fabric_store_date_range').val();
        if ((!fromDate || !toDate) && rangeVal) {
            rangeVal = rangeVal.trim();
            if (rangeVal.indexOf(' to ') !== -1) {
                let parts = rangeVal.split(' to ');
                fromDate = parts[0].trim();
                toDate = parts[1].trim();
            } else if (rangeVal) {
                fromDate = rangeVal;
                toDate = rangeVal;
            }
        }
        let data = {
            from_date: fromDate || '',
            to_date: toDate || '',
            supplier_id: $('select[name="supplier_id"]').val() || '',
            brand_id: $('select[name="brand_id"]').val() || ''
        };
        return $.extend(data, extraParams);
    }

    function drillDownToCasinoStyle(brand, width, style) {
        $('#casinoDrilldownBreadcrumbs').attr('style', 'display: flex !important;');
        let displayWidth = (width && width !== '-' && width !== 'N/A') ? `&nbsp; <span class="badge bg-label-info">${width}"</span>` : '';
        $('#casinoDrilldownBreadcrumbText').html(`Casino PO Report &nbsp; <i class="ri-arrow-right-s-line"></i> &nbsp; <span class="text-primary fw-bold">${brand}</span>${displayWidth} &nbsp; <span class="badge bg-primary text-white">${style}</span>`);

        $('#casinoPoMainContainer').hide();
        $('#casinoDrilldownContainer').show();

        if ($.fn.DataTable.isDataTable('#casinoDrilldownTable')) {
            $('#casinoDrilldownTable').DataTable().clear().destroy();
        }
        $('#casinoDrilldownTable tbody').empty();

        $('#casinoDrilldownTable').DataTable({
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
                    return getCasinoFilterData({
                        report_type: 'casino-po-drilldown',
                        brand_name: brand,
                        width: width,
                        style_name: style,
                        draw: d.draw,
                        start: d.start,
                        length: d.length,
                        search: d.search ? d.search.value : ''
                    });
                }
            },
            columns: [
                { data: 'sno', className: 'text-center' },
                { data: 'pr_range', className: 'fw-semibold text-dark' },
                { data: 'design_count', className: 'text-center' },
                { data: 'meters', className: 'text-end fw-bold text-primary' }
            ],
            drawCallback: function(settings) {
                var json = settings.json;
                if (json && json.totals) {
                    $('#footer-casino-drilldown-designs').text(json.totals.design_count);
                    $('#footer-casino-drilldown-meters').text(json.totals.meters);
                }
            },
            language: {
                emptyTable: 'No PR range slab records found for this selection.'
            }
        });
    }

    $(document).ready(function() {
        const $table = $('.datatables-casino-po');
        if (!$table.length || !$.fn.DataTable) return;

        if ($.fn.DataTable.isDataTable($table)) {
            $table.DataTable().clear().destroy();
        }

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
                    return getCasinoFilterData({
                        report_type: 'casino-po-report',
                        draw: d.draw,
                        start: d.start,
                        length: d.length,
                        search: d.search ? d.search.value : ''
                    });
                }
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'brand_name', className: 'text-start ps-3 fw-medium text-dark' },
                { data: 'width', className: 'text-center' },
                { data: 'plain', className: 'text-end' },
                { data: 'print', className: 'text-end' },
                { data: 'checked', className: 'text-end' },
                { data: 'striped', className: 'text-end' },
                { data: 'total', className: 'text-end fw-bold text-dark' }
            ],
            drawCallback: function(settings) {
                var json = settings.json;
                if (json && json.totals) {
                    $('#footer-casino-plain').html(json.totals.plain);
                    $('#footer-casino-print').html(json.totals.print);
                    $('#footer-casino-checked').html(json.totals.checked);
                    $('#footer-casino-striped').html(json.totals.striped);
                    $('#footer-casino-total').html(json.totals.total);
                }
            },
            language: {
                emptyTable: 'No casino purchase orders found.'
            }
        });

        $(document).off('click', '.casino-drilldown-cell').on('click', '.casino-drilldown-cell', function(e) {
            e.preventDefault();
            const brand = $(this).data('brand') || '';
            const width = $(this).data('width') || '';
            const style = $(this).data('style') || '';

            drillDownToCasinoStyle(brand, width, style);
        });
    });
</script>

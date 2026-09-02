<div class="mb-3 d-flex align-items-center" id="warehouseSummaryBreadcrumbs" style="display: none !important;">
    <button class="btn btn-sm btn-outline-secondary me-2" onclick="renderWarehouseSummaryBrandLevel()" id="btnBackToWarehouseBrands">
        <i class="ri ri-arrow-left-line"></i> Back to Brands
    </button>
    <span class="text-muted fw-bold" id="warehouseSummaryBreadcrumbText">All Brands</span>
</div>

<!-- Brand Level Container -->
<div class="card-datatable table-responsive" id="warehouseSummaryBrandContainer">
    <table class="datatables-products table table-hover align-middle" id="warehouseSummaryTable" style="width: 100%;">
        <thead class="text-center table-light">
            <tr>
                <th class="text-start">BRAND</th>
                <th class="text-center">WAREHOUSE CAPACITY (PCS)</th>
                <th class="text-center">SETWISE STOCK</th>
                <th class="text-center">SINGLE STORE STOCK</th>
                <th class="text-center">TOTAL</th>
                <th class="text-center">WAREHOUSE UTILIZATION</th>
                <th class="text-center">DAMAGE STORE STOCK</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $row)
                <tr onclick="drillDownToWarehouseSummaryStyle({{ $row['brand_id'] }}, '{{ addslashes($row['brand_name']) }}')" class="clickable-brand-row" data-brand-id="{{ $row['brand_id'] }}" data-brand-name="{{ $row['brand_name'] }}">
                    <td class="fw-bold text-start">
                        <span class="text-primary fw-bold text-decoration-none view-brand-style-link">
                            {{ $row['brand_name'] }} <i class="ri-arrow-right-s-line small"></i>
                        </span>
                    </td>
                    <td class="text-center fw-semibold">{{ number_format($row['capacity_pcs']) }}</td>
                    <td class="text-center text-primary fw-semibold">{{ number_format($row['setwise_stock']) }}</td>
                    <td class="text-center text-info fw-semibold">{{ number_format($row['single_store_stock']) }}</td>
                    <td class="text-center fw-bold">{{ number_format($row['total_stock']) }}</td>
                    <td class="text-center">
                        @php
                            $pct = $row['utilization_pct'];
                            $badgeClass = $pct > 95 ? 'bg-danger' : ($pct > 80 ? 'bg-warning text-dark' : 'bg-primary');
                        @endphp
                        <span class="badge {{ $badgeClass }} px-3 py-2 fs-6">{{ $pct }}%</span>
                    </td>
                    <td class="text-center text-danger fw-semibold">{{ number_format($row['damage_stock']) }}</td>
                </tr>
            @empty
            @endforelse
        </tbody>
        @if(!empty($totals))
            <tfoot class="table-light fw-bold border-top">
                <tr class="table-secondary">
                    <td class="text-start fw-bold fs-6">TOTAL</td>
                    <td class="text-center fw-bold fs-6">{{ number_format($totals['capacity_pcs']) }}</td>
                    <td class="text-center text-primary fw-bold fs-6">{{ number_format($totals['setwise_stock']) }}</td>
                    <td class="text-center text-info fw-bold fs-6">{{ number_format($totals['single_store_stock']) }}</td>
                    <td class="text-center fw-bold fs-6">{{ number_format($totals['total_stock']) }}</td>
                    <td class="text-center">
                        @php
                            $totPct = $totals['utilization_pct'];
                            $totBadgeClass = $totPct > 95 ? 'bg-danger' : ($totPct > 80 ? 'bg-warning text-dark' : 'bg-primary');
                        @endphp
                        <span class="badge {{ $totBadgeClass }} px-3 py-2 fs-6">{{ $totPct }}%</span>
                    </td>
                    <td class="text-center text-danger fw-bold fs-6">{{ number_format($totals['damage_stock']) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>
</div>

<!-- Style Level Container -->
<div class="card-datatable table-responsive" id="warehouseSummaryStyleContainer" style="display: none;">
    <table class="datatables-products table table-hover align-middle" id="warehouseSummaryStyleTable" style="width: 100%;">
        <thead class="text-center table-light">
            <tr>
                <th class="text-start">STYLE</th>
                <th class="text-center">STYLE CAPACITY (PCS)</th>
                <th class="text-center">SETWISE STOCK</th>
                <th class="text-center">SINGLE STORE STOCK</th>
                <th class="text-center">TOTAL</th>
                <th class="text-center">WAREHOUSE UTILIZATION</th>
                <th class="text-center">DAMAGE STORE STOCK</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot class="table-light fw-bold border-top">
            <tr class="table-secondary">
                <td class="text-start fw-bold fs-6">TOTAL</td>
                <td class="text-center fw-bold fs-6" id="whSumStyleFootCapacity">-</td>
                <td class="text-center text-primary fw-bold fs-6" id="whSumStyleFootSetwise">-</td>
                <td class="text-center text-info fw-bold fs-6" id="whSumStyleFootSingle">-</td>
                <td class="text-center fw-bold fs-6" id="whSumStyleFootTotal">-</td>
                <td class="text-center" id="whSumStyleFootUtil">-</td>
                <td class="text-center text-danger fw-bold fs-6" id="whSumStyleFootDamage">-</td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
var currentWhSumBrandId = null;
var currentWhSumBrandName = '';
var isWhSumStyleLevel = false;

window.initWarehouseSummaryTable = function() {
    if (isWhSumStyleLevel && currentWhSumBrandId) {
        loadWarehouseSummaryStylesTable(currentWhSumBrandId, currentWhSumBrandName);
        return;
    }

    if ($.fn.DataTable.isDataTable('#warehouseSummaryTable')) {
        $('#warehouseSummaryTable').DataTable().destroy();
    }
    $('#warehouseSummaryTable').DataTable({
        responsive: true,
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10,
        columnDefs: [
            { targets: 0, className: 'text-start', width: '25%' },
            { targets: 1, className: 'text-center', width: '15%' },
            { targets: 2, className: 'text-center', width: '12%' },
            { targets: 3, className: 'text-center', width: '12%' },
            { targets: 4, className: 'text-center', width: '10%' },
            { targets: 5, className: 'text-center', width: '14%' },
            { targets: 6, className: 'text-center', width: '12%' }
        ],
        dom: '<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>rt<"row mt-3"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search...",
            lengthMenu: "Show _MENU_ entries",
            zeroRecords: "No matching records found for the selected warehouse."
        }
    });
};

function initWarehouseSummaryTable() {
    window.initWarehouseSummaryTable();
}

function drillDownToWarehouseSummaryStyle(brandId, brandName) {
    isWhSumStyleLevel = true;
    currentWhSumBrandId = brandId;
    currentWhSumBrandName = brandName;

    $('#warehouseSummaryBreadcrumbs').attr('style', 'display: flex !important;');
    $('#warehouseSummaryBreadcrumbText').html(`All Brands &nbsp; <i class="ri-arrow-right-s-line"></i> &nbsp; <span class="text-primary">${brandName}</span>`);

    $('#warehouseSummaryBrandContainer').hide();
    $('#warehouseSummaryStyleContainer').show();

    loadWarehouseSummaryStylesTable(brandId, brandName);
}

function renderWarehouseSummaryBrandLevel() {
    isWhSumStyleLevel = false;
    currentWhSumBrandId = null;
    currentWhSumBrandName = '';

    $('#warehouseSummaryBreadcrumbs').attr('style', 'display: none !important;');
    $('#warehouseSummaryStyleContainer').hide();
    $('#warehouseSummaryBrandContainer').show();

    if ($.fn.DataTable.isDataTable('#warehouseSummaryTable')) {
        $('#warehouseSummaryTable').DataTable().columns.adjust().draw();
    }
}

function loadWarehouseSummaryStylesTable(brandId, brandName) {
    if ($.fn.DataTable.isDataTable('#warehouseSummaryStyleTable')) {
        $('#warehouseSummaryStyleTable').DataTable().clear().destroy();
    }
    $('#warehouseSummaryStyleTable tbody').empty();

    let selectedWhId = $('#top_warehouse_select').val() || '{{ $selectedWarehouseId ?? '' }}';

    $('#warehouseSummaryStyleTable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        ajax: {
            url: "{{ url('warehouse_reports/warehouse_summary_styles') }}",
            data: function (d) {
                d.brand_id = brandId;
                d.warehouse_id = selectedWhId;
                d.from_date = $('.start_date').val();
                d.to_date = $('.end_date').val();
            }
        },
        columns: [
            { data: 'style_name', name: 'style_name', className: 'text-start' },
            { data: 'capacity_pcs', name: 'capacity_pcs', className: 'text-center fw-semibold' },
            { data: 'setwise_stock', name: 'setwise_stock', className: 'text-center' },
            { data: 'single_store_stock', name: 'single_store_stock', className: 'text-center' },
            { data: 'total_stock', name: 'total_stock', className: 'text-center' },
            { data: 'utilization', name: 'utilization', className: 'text-center', orderable: false },
            { data: 'damage_stock', name: 'damage_stock', className: 'text-center' }
        ],
        dom: '<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>rt<"row mt-3"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search styles...",
            lengthMenu: "Show _MENU_ entries",
            zeroRecords: "No styles found for this brand."
        },
        drawCallback: function (settings) {
            let json = settings.json;
            if (json && json.totals) {
                $('#whSumStyleFootCapacity').text(json.totals.capacity_pcs || '0');
                $('#whSumStyleFootSetwise').text(json.totals.setwise_stock || '0');
                $('#whSumStyleFootSingle').text(json.totals.single_store_stock || '0');
                $('#whSumStyleFootTotal').text(json.totals.total_stock || '0');
                $('#whSumStyleFootUtil').html(json.totals.utilization || '<span class="badge bg-primary px-3 py-2 fs-6">0%</span>');
                $('#whSumStyleFootDamage').text(json.totals.damage_stock || '0');
            }
        }
    });
}

$(document).ready(function() {
    window.initWarehouseSummaryTable();
});
</script>

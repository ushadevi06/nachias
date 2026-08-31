<div class="card-datatable table-responsive">
    <table class="datatables-products table table-hover align-middle" id="warehouseSummaryTable" style="width: 100%;">
        <thead class="text-center">
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
                <tr>
                    <td class="fw-bold text-start">{{ $row['brand_name'] }}</td>
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
            <tfoot class="table-light fw-bold">
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

<script>
window.initWarehouseSummaryTable = function() {
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
        dom: '<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row mt-3"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
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

$(document).ready(function() {
    window.initWarehouseSummaryTable();
});
</script>

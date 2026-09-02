@php
    $pending = $kpiCardData['pending_orders'] ?? ['display' => '0 Orders (0 Due Today)'];
    $leadTime = $kpiCardData['average_lead_time'] ?? ['display' => '0.0 Days'];
    $util = $kpiCardData['warehouse_utilisation'] ?? ['display' => '0%'];
    $accuracy = $kpiCardData['dispatch_accuracy'] ?? ['display' => '100% (0 Errors)'];
@endphp

<div class="card border-0 shadow-sm mb-4 kpi-dashboard-card" style="border-radius: 12px; overflow: hidden; background: #ffffff;">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
            <i class="ri ri-pie-chart-2-line text-primary me-2 fs-5"></i>
            Order Fulfillment & Warehouse Operations KPIs
        </h6>
        <span class="badge bg-label-primary px-3 py-1 rounded-pill small fw-semibold">Today / Live</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0 kpi-summary-table">
                <thead>
                    <tr class="text-muted" style="font-size: 0.85rem; font-weight: 700; border-bottom: 2px solid #edf2f7; letter-spacing: 0.03em; background: #fbfcfe;">
                        <th class="ps-4 py-3 text-uppercase" style="width: 45%;">KPI</th>
                        <th class="pe-4 py-3 text-uppercase" style="width: 55%;">Today</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.92rem;">
                    <!-- 1. Pending Orders -->
                    <tr class="kpi-table-row">
                        <td class="ps-4 py-3 fw-bold text-dark text-nowrap">
                            <span class="kpi-emoji me-2">📦</span>
                            <span class="align-middle">Pending Orders</span>
                        </td>
                        <td class="pe-4 py-3 fw-semibold text-secondary">
                            {{ $pending['display'] }}
                        </td>
                    </tr>

                    <!-- 2. Average Lead Time -->
                    <tr class="kpi-table-row">
                        <td class="ps-4 py-3 fw-bold text-dark text-nowrap">
                            <span class="kpi-emoji me-2">🚚</span>
                            <span class="align-middle">Average Lead Time</span>
                        </td>
                        <td class="pe-4 py-3 fw-semibold text-secondary">
                            {{ $leadTime['display'] }}
                        </td>
                    </tr>

                    <!-- 3. Warehouse Utilisation -->
                    <tr class="kpi-table-row">
                        <td class="ps-4 py-3 fw-bold text-dark text-nowrap">
                            <span class="kpi-emoji me-2">🏬</span>
                            <span class="align-middle">Warehouse Utilisation</span>
                        </td>
                        <td class="pe-4 py-3 fw-semibold text-secondary">
                            {{ $util['display'] }}
                        </td>
                    </tr>

                    <!-- 4. Dispatch Accuracy -->
                    <tr class="kpi-table-row">
                        <td class="ps-4 py-3 fw-bold text-dark text-nowrap">
                            <span class="kpi-emoji me-2">❌</span>
                            <span class="align-middle">Dispatch Accuracy</span>
                        </td>
                        <td class="pe-4 py-3 fw-semibold text-secondary">
                            {{ $accuracy['display'] }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.kpi-dashboard-card {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04) !important;
    border: 1px solid #edf2f7 !important;
}

.kpi-summary-table thead th {
    border-top: none;
    color: #475569;
}

.kpi-table-row {
    transition: background-color 0.18s ease;
    border-bottom: 1px solid #f8fafc;
}

.kpi-table-row:hover {
    background-color: #f8fafc;
}

.kpi-emoji {
    font-size: 1.15rem;
    display: inline-block;
    vertical-align: middle;
}
</style>

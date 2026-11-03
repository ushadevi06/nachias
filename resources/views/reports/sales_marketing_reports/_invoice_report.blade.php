<style>
    .dispatch-kpi-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 16px 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        height: 100%;
    }
    .dispatch-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }
    .dispatch-kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }
    .dispatch-tile {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 8px;
        transition: all 0.15s ease;
        margin: 0 auto;
    }
    .dispatch-tile:hover {
        transform: scale(1.08);
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    #invoiceReportTable th {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        color: #475569;
        vertical-align: middle;
        padding: 12px 6px;
        background-color: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }
    #invoiceReportTable td {
        vertical-align: middle;
        padding: 10px 6px;
        border-bottom: 1px solid #f1f5f9;
    }
    #invoiceReportTable tbody tr:hover {
        background-color: #f8fafc;
    }
</style>

<!-- 4 KPI Summary Cards -->
<div class="mb-4">
    <div class="row g-3 mb-2">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dispatch-kpi-card">
                <div class="dispatch-kpi-icon" style="background: #e0f2fe; color: #0284c7;">
                    <i class="ri ri-file-text-line"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Invoices Listed</div>
                    <div class="fs-4 fw-bold text-dark mt-1" id="kpi_dispatch_total_invoices">-</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dispatch-kpi-card">
                <div class="dispatch-kpi-icon" style="background: #dcfce7; color: #16a34a;">
                    <i class="ri ri-box-3-line"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Ready for Dispatch</div>
                    <div class="fs-4 fw-bold text-dark mt-1" id="kpi_dispatch_ready">-</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dispatch-kpi-card">
                <div class="dispatch-kpi-icon" style="background: #fef3c7; color: #d97706;">
                    <i class="ri ri-time-line"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">In Progress</div>
                    <div class="fs-4 fw-bold text-dark mt-1" id="kpi_dispatch_in_progress">-</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dispatch-kpi-card">
                <div class="dispatch-kpi-icon" style="background: #fee2e2; color: #dc2626;">
                    <i class="ri ri-error-warning-line"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold text-uppercase" style="letter-spacing: 0.5px; font-size: 11px;">Pending LR / Accounts</div>
                    <div class="fs-4 fw-bold text-dark mt-1" id="kpi_dispatch_pending_lr">-</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover datatables-products w-100" id="invoiceReportTable">
        <thead>
            <tr>
                <th class="text-nowrap">DATE</th>
                <th>INVOICE NO</th>
                <th>CUSTOMER</th>
                <th class="text-center">QTY</th>
                <th class="text-center">PICKING</th>
                <th class="text-center">CHECKING (ERP)</th>
                <th class="text-center">E-INVOICE</th>
                <th class="text-center">E-WAY BILL</th>
                <th class="text-center">KACHATHU</th>
                <th class="text-center">READY FOR DISPATCH</th>
                <th class="text-center">LR UPDATED</th>
                <th class="text-center">SENT TO ACCOUNTS</th>
                <th class="text-center">STATUS</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be loaded via AJAX -->
        </tbody>
    </table>
</div>

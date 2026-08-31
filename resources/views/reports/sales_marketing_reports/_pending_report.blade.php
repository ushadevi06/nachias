<!-- Breadcrumbs / Back to Pending Orders Button (Level 2 Navigation) -->
<div class="mb-3 align-items-center" id="pendingBreadcrumbs" style="display: none;">
    <button class="btn btn-sm btn-primary rounded-pill me-2" onclick="renderPendingOrderLevel()" id="btnBackToPendingOrders">
        <i class="ri ri-arrow-left-line me-1"></i> Back to Pending Orders
    </button>
    <span class="text-muted fw-bold" id="pendingBreadcrumbText">All Pending Orders</span>
</div>

<!-- Selected Pending Order Info Header (shows on Level 2) -->
<div class="row g-3 mb-4 p-3 bg-light rounded-3 border shadow-xs" id="selectedPendingOrderInfoHeader" style="display: none;">
    <div class="col-md-3">
        <span class="text-muted small d-block">SO Number:</span>
        <strong class="text-primary fs-6" id="level2PendingSoNo">-</strong>
    </div>
    <div class="col-md-3">
        <span class="text-muted small d-block">Customer:</span>
        <strong class="text-dark" id="level2PendingCustomer">-</strong>
    </div>
    <div class="col-md-3">
        <span class="text-muted small d-block">Total Ordered Qty:</span>
        <strong class="text-dark" id="level2PendingOrdQty">-</strong>
    </div>
    <div class="col-md-3">
        <span class="text-muted small d-block">Balance Qty:</span>
        <strong class="text-danger fs-6" id="level2PendingBalQty">-</strong>
    </div>
</div>

<!-- Main DataTables Table -->
<div class="card-datatable table-responsive">
    <table class="datatables-products table table-hover" id="pendingReportTable">
        <thead class="table-light">
            <tr id="pendingReportTheadTr">
                <th class="fw-bold">ORDER NO</th>
                <th class="fw-bold">CUSTOMER</th>
                <th class="text-center fw-bold">ORD. QTY</th>
                <th class="text-center fw-bold">BAL. QTY</th>
            </tr>
        </thead>
        <tbody id="pendingReportTbody">
        </tbody>
    </table>
</div>

<style>
    #pendingReportTable tbody tr {
        cursor: pointer;
        transition: background-color 0.15s ease-in-out;
    }
    #pendingReportTable tbody tr:hover td {
        background-color: rgba(105, 108, 255, 0.08) !important;
    }
</style>

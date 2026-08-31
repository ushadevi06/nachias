<!-- Breadcrumbs / Back to Orders Button (Level 2 Navigation) -->
<div class="mb-3 align-items-center" id="orderBreadcrumbs" style="display: none;">
    <button class="btn btn-sm btn-primary rounded-pill me-2" onclick="renderOrderLevel()" id="btnBackToOrders">
        <i class="ri ri-arrow-left-line me-1"></i> Back to Orders
    </button>
    <span class="text-muted fw-bold" id="orderBreadcrumbText">All Orders</span>
</div>

<!-- Selected Order Info Header (shows on Level 2) -->
<div class="row g-3 mb-4 p-3 bg-light rounded-3 border shadow-xs" id="selectedOrderInfoHeader" style="display: none;">
    <div class="col-md-3">
        <span class="text-muted small d-block">SO Number:</span>
        <strong class="text-primary fs-6" id="level2SoNo">-</strong>
    </div>
    <div class="col-md-3">
        <span class="text-muted small d-block">Customer:</span>
        <strong class="text-dark" id="level2Customer">-</strong>
    </div>
    <div class="col-md-3">
        <span class="text-muted small d-block">Order Date:</span>
        <strong class="text-dark" id="level2SoDate">-</strong>
    </div>
    <div class="col-md-3">
        <span class="text-muted small d-block">Status:</span>
        <div id="level2Status">-</div>
    </div>
</div>

<!-- Main DataTables Table -->
<div class="card-datatable table-responsive">
    <table class="datatables-products table table-hover" id="orderReportTable">
        <thead class="table-light">
            <tr id="orderReportTheadTr">
                <th class="fw-bold">ORDER NO</th>
                <th class="text-nowrap fw-bold">ORDER DATE</th>
                <th class="fw-bold">CUSTOMER</th>
                <th class="text-center fw-bold">QTY</th>
                <th class="text-center fw-bold">STATUS</th>
            </tr>
        </thead>
        <tbody id="orderReportTbody">
        </tbody>
    </table>
</div>

<style>
    #orderReportTable tbody tr {
        cursor: pointer;
        transition: background-color 0.15s ease-in-out;
    }
    #orderReportTable tbody tr:hover td {
        background-color: rgba(105, 108, 255, 0.08) !important;
    }
</style>

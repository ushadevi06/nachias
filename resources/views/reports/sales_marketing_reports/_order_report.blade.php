<!-- Order Report 3 Sub-Tabs (Order List, Product Wise, Party Wise) -->
<div class="row mb-3">
    <div class="col-12">
        <ul class="nav nav-pills p-1 bg-lighter rounded-3 d-inline-flex border" id="orderReportSubTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link active px-4 py-2 fw-semibold" id="order-list-tab" data-bs-toggle="pill" data-bs-target="#order-list-pane" role="tab" aria-controls="order-list-pane" aria-selected="true">
                    <i class="ri ri-list-check-2 me-1"></i> Order List
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link px-4 py-2 fw-semibold" id="order-product-wise-tab" data-bs-toggle="pill" data-bs-target="#order-product-wise-pane" role="tab" aria-controls="order-product-wise-pane" aria-selected="false">
                    <i class="ri ri-shirt-line me-1"></i> Product Wise
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link px-4 py-2 fw-semibold" id="order-party-wise-tab" data-bs-toggle="pill" data-bs-target="#order-party-wise-pane" role="tab" aria-controls="order-party-wise-pane" aria-selected="false">
                    <i class="ri ri-user-star-line me-1"></i> Party Wise
                </button>
            </li>
        </ul>
    </div>
</div>

<div class="tab-content p-0" id="orderReportTabContent">
    <!-- Sub-Tab 1: Order List (With Level 1 & Level 2 Drilldown) -->
    <div class="tab-pane fade show active" id="order-list-pane" role="tabpanel" aria-labelledby="order-list-tab">
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
                <tfoot class="table-light fw-bold">
                    <tr>
                        <th colspan="3" class="text-end text-uppercase">Total:</th>
                        <th class="text-center text-primary fw-bold" id="order_report_total_qty">0</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Sub-Tab 2: Product Wise -->
    <div class="tab-pane fade" id="order-product-wise-pane" role="tabpanel" aria-labelledby="order-product-wise-tab">
        <div class="card-datatable table-responsive">
            <table class="datatables-products table table-hover" id="orderProductWiseTable">
                <thead class="table-light">
                    <tr>
                        <th class="text-center fw-bold" style="width: 50px;">S.NO</th>
                        <th class="fw-bold">PRODUCT NAME</th>
                        <th class="fw-bold text-nowrap">ART NO</th>
                        <th class="fw-bold">COLOR</th>
                        <th class="text-center fw-bold">SLEEVE</th>
                        <th class="text-center fw-bold">36</th>
                        <th class="text-center fw-bold">38</th>
                        <th class="text-center fw-bold">40</th>
                        <th class="text-center fw-bold">42</th>
                        <th class="text-center fw-bold">44</th>
                        <th class="text-center fw-bold">46</th>
                        <th class="text-center fw-bold">48</th>
                        <th class="text-center fw-bold">50</th>
                        <th class="text-center fw-bold text-danger text-nowrap">TOTAL QTY</th>
                    </tr>
                </thead>
                <tbody id="orderProductWiseTbody">
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <th colspan="5" class="text-end text-uppercase">Total:</th>
                        <th class="text-center text-primary" id="order_pw_total_36">0</th>
                        <th class="text-center text-primary" id="order_pw_total_38">0</th>
                        <th class="text-center text-primary" id="order_pw_total_40">0</th>
                        <th class="text-center text-primary" id="order_pw_total_42">0</th>
                        <th class="text-center text-primary" id="order_pw_total_44">0</th>
                        <th class="text-center text-primary" id="order_pw_total_46">0</th>
                        <th class="text-center text-primary" id="order_pw_total_48">0</th>
                        <th class="text-center text-primary" id="order_pw_total_50">0</th>
                        <th class="text-center text-danger fw-bold fs-6" id="order_pw_total_qty">0</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Sub-Tab 3: Party Wise -->
    <div class="tab-pane fade" id="order-party-wise-pane" role="tabpanel" aria-labelledby="order-party-wise-tab">
        <div class="card-datatable table-responsive">
            <table class="datatables-products table table-hover" id="orderPartyWiseTable">
                <thead class="table-light">
                    <tr>
                        <th class="text-center fw-bold" style="width: 50px;">S.NO</th>
                        <th class="fw-bold text-nowrap">ORDER NO</th>
                        <th class="fw-bold text-nowrap">ORDER DATE</th>
                        <th class="fw-bold">CUSTOMER NAME</th>
                        <th class="fw-bold">PLACE</th>
                        <th class="fw-bold text-nowrap">SALES EXECUTIVE</th>
                        <th class="fw-bold">ZONE</th>
                        <th class="fw-bold">PRODUCT NAME</th>
                        <th class="fw-bold text-nowrap">ART NO</th>
                        <th class="fw-bold">COLOR</th>
                        <th class="text-center fw-bold">SLEEVE</th>
                        <th class="text-center fw-bold">36</th>
                        <th class="text-center fw-bold">38</th>
                        <th class="text-center fw-bold">40</th>
                        <th class="text-center fw-bold">42</th>
                        <th class="text-center fw-bold">44</th>
                        <th class="text-center fw-bold">46</th>
                        <th class="text-center fw-bold">48</th>
                        <th class="text-center fw-bold">50</th>
                        <th class="text-center fw-bold text-danger text-nowrap">TOTAL QTY</th>
                    </tr>
                </thead>
                <tbody id="orderPartyWiseTbody">
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <th colspan="11" class="text-end text-uppercase">Total:</th>
                        <th class="text-center text-primary" id="order_party_total_36">0</th>
                        <th class="text-center text-primary" id="order_party_total_38">0</th>
                        <th class="text-center text-primary" id="order_party_total_40">0</th>
                        <th class="text-center text-primary" id="order_party_total_42">0</th>
                        <th class="text-center text-primary" id="order_party_total_44">0</th>
                        <th class="text-center text-primary" id="order_party_total_46">0</th>
                        <th class="text-center text-primary" id="order_party_total_48">0</th>
                        <th class="text-center text-primary" id="order_party_total_50">0</th>
                        <th class="text-center text-danger fw-bold fs-6" id="order_party_total_qty">0</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
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

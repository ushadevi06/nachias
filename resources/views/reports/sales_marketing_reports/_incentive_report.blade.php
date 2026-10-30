<div class="d-flex justify-content-between align-items-center mb-2 px-1 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
        <div id="incentivePeriodWrapper" style="display: none;">
            <span class="badge bg-label-primary px-3 py-2 fs-7">
                <i class="ri ri-calendar-event-line me-1"></i> Period: <strong id="incentivePeriodBadge"></strong>
            </span>
        </div>
        <div id="incentiveBreadcrumbs" style="display: none;" class="align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" id="btnBackToIncentiveLevel">
                <i class="ri ri-arrow-left-line me-1"></i> Back to All Executives
            </button>
            <span class="badge bg-label-info px-3 py-2 fs-7">
                <i class="ri ri-user-line me-1"></i> <strong id="incentiveDrilldownExecutiveName"></strong>
            </span>
        </div>
    </div>
</div>

<div id="incentiveMainLevelContainer">
    <div class="card-datatable table-responsive">
        <table class="datatables-products table table-hover" id="incentiveReportTable" style="cursor: pointer;">
            <thead class="bg-light">
                <tr>
                    <th>Zone</th>
                    <th>Sales Executive</th>
                    <th class="text-center">Target</th>
                    <th class="text-center">Achieved</th>
                    <th class="text-center">Discount Qty</th>
                    <th class="text-center">Return Qty</th>
                    <th class="text-center">Incentive Qty</th>
                    <th class="text-end">Rate / PCS</th>
                    <th class="text-end">Incentive Amount</th>
                    <th class="text-end">Refinishing Amount</th>
                    <th class="text-end">Net Incentive</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot class="bg-light fw-bold">
                <tr>
                    <th colspan="2" class="text-end text-uppercase">Total:</th>
                    <th class="text-center text-primary fw-bold" id="incentive_report_total_target">0</th>
                    <th class="text-center text-primary fw-bold" id="incentive_report_total_achieved">0</th>
                    <th class="text-center text-secondary fw-bold" id="incentive_report_total_discount">0</th>
                    <th class="text-center text-danger fw-bold" id="incentive_report_total_return">0</th>
                    <th class="text-center text-info fw-bold" id="incentive_report_total_incentive_qty">0</th>
                    <th></th>
                    <th class="text-end text-primary fw-bold" id="incentive_report_total_gross_incentive">₹0.00</th>
                    <th class="text-end text-danger fw-bold" id="incentive_report_total_refinishing">₹0.00</th>
                    <th class="text-end text-success fw-bold fs-6" id="incentive_report_total_net_incentive">₹0.00</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div id="incentiveBrandLevelContainer" style="display: none;">
    <div class="card-datatable table-responsive">
        <table class="datatables-products table table-hover w-100" id="incentiveBrandReportTable">
            <thead class="bg-light">
                <tr>
                    <th>Brand</th>
                    <th class="text-center">Achieved</th>
                    <th class="text-center">Discount Qty</th>
                    <th class="text-center">Return Qty</th>
                    <th class="text-center">Incentive Qty</th>
                    <th class="text-end">Rate / PCS</th>
                    <th class="text-end">Incentive Amount</th>
                    <th class="text-end">Refinishing Amount</th>
                    <th class="text-end">Net Incentive</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot class="bg-light fw-bold">
                <tr>
                    <th class="text-end text-uppercase">Total:</th>
                    <th class="text-center text-primary fw-bold" id="incentive_brand_total_achieved">0</th>
                    <th class="text-center text-secondary fw-bold" id="incentive_brand_total_discount">0</th>
                    <th class="text-center text-danger fw-bold" id="incentive_brand_total_return">0</th>
                    <th class="text-center text-info fw-bold" id="incentive_brand_total_incentive_qty">0</th>
                    <th></th>
                    <th class="text-end text-primary fw-bold" id="incentive_brand_total_gross_incentive">₹0.00</th>
                    <th class="text-end text-danger fw-bold" id="incentive_brand_total_refinishing">₹0.00</th>
                    <th class="text-end text-success fw-bold fs-6" id="incentive_brand_total_net_incentive">₹0.00</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

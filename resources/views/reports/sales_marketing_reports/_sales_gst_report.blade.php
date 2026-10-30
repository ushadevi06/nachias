<div class="d-flex justify-content-between align-items-center mb-2 px-1 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-sm btn-success" id="btnMarkGstFiled" disabled>
            <i class="ri ri-checkbox-circle-line me-1"></i> Mark as GST Filed (<span id="gstSelectedCount">0</span>)
        </button>
    </div>
    <div id="gstSelectionNotice" class="text-muted small" style="display: none;">
        <span class="badge bg-label-primary" id="gstSelectionBadge">0 selected</span>
    </div>
</div>

<div id="gstSelectAllPagesBanner" class="alert alert-primary py-2 px-3 mb-2 d-none d-flex align-items-center justify-content-between flex-wrap gap-2" style="font-size: 13px;">
    <div id="gstBannerContent">
        <i class="ri ri-information-line me-1"></i>
        <span id="gstBannerPageText">All <strong id="gstPageCountText">0</strong> invoices on this page are selected.</span>
        <a href="javascript:void(0);" id="btnSelectAllMatching" class="fw-bold text-decoration-underline ms-1 text-primary">
            Select all <span id="gstTotalFilteredPendingCount">0</span> pending invoices matching current filters
        </a>
    </div>
    <div id="gstBannerAllSelectedContent" class="d-none">
        <i class="ri ri-checkbox-circle-fill text-primary me-1"></i>
        <span>All <strong id="gstAllSelectedPendingCount">0</strong> pending invoices matching current filters across all pages are selected.</span>
    </div>
    <a href="javascript:void(0);" id="btnClearAllSelection" class="text-danger small fw-semibold text-decoration-underline ms-auto">Clear selection</a>
</div>

<div class="table-responsive">
    <table class="table table-hover datatables-products w-100" id="salesGstReportTable">
        <thead class="table-light">
            <tr>
                <th class="text-center no-export" style="width: 35px;"><input type="checkbox" class="form-check-input" id="gstSelectAll" title="Select All"></th>
                <th class="text-center fw-bold" style="width: 40px;">S.NO</th>
                <th class="fw-bold">INVOICE NO</th>
                <th class="text-nowrap fw-bold">INVOICE DATE</th>
                <th class="fw-bold">CUSTOMER NAME</th>
                <th class="fw-bold">CUSTOMER GST NO</th>
                <th class="fw-bold">CUSTOMER PLACE</th>
                <th class="text-center fw-bold">QTY</th>
                <th class="text-end fw-bold">SUBTOTAL</th>
                <th class="text-center fw-bold">DISCOUNT %</th>
                <th class="text-end fw-bold">DISCOUNT</th>
                <th class="text-end fw-bold">TAXABLE VALUE</th>
                <th class="text-center fw-bold">CGST %</th>
                <th class="text-end fw-bold">CGST AMOUNT</th>
                <th class="text-center fw-bold">SGST %</th>
                <th class="text-end fw-bold">SGST AMOUNT</th>
                <th class="text-center fw-bold">IGST %</th>
                <th class="text-end fw-bold">IGST AMOUNT</th>
                <th class="text-end fw-bold">ROUND OFF</th>
                <th class="text-end fw-bold">TOTAL AMOUNT</th>
                <th class="text-center fw-bold">E-INVOICE STATUS</th>
                <th class="text-center fw-bold">GST FILING</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be loaded via AJAX -->
        </tbody>
        <tfoot>
            <tr class="table-light fw-bold">
                <th colspan="7" class="text-end text-uppercase">Total:</th>
                <th class="text-center text-primary fw-bold" id="sales_gst_report_total_qty">0</th>
                <th class="text-end text-primary fw-bold" id="sales_gst_report_total_subtotal">₹0.00</th>
                <th></th>
                <th class="text-end text-danger fw-bold" id="sales_gst_report_total_discount">₹0.00</th>
                <th class="text-end text-primary fw-bold" id="sales_gst_report_total_taxable">₹0.00</th>
                <th></th>
                <th class="text-end text-primary fw-bold" id="sales_gst_report_total_cgst">₹0.00</th>
                <th></th>
                <th class="text-end text-primary fw-bold" id="sales_gst_report_total_sgst">₹0.00</th>
                <th></th>
                <th class="text-end text-primary fw-bold" id="sales_gst_report_total_igst">₹0.00</th>
                <th class="text-end text-secondary fw-bold" id="sales_gst_report_total_round_off">₹0.00</th>
                <th class="text-end text-success fw-bold" id="sales_gst_report_total_amount">₹0.00</th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>


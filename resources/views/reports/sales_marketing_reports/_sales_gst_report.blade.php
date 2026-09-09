<div class="table-responsive">
    <table class="table table-hover datatables-products w-100" id="salesGstReportTable">
        <thead class="table-light">
            <tr>
                <th class="text-center fw-bold" style="width: 40px;">S.NO</th>
                <th class="fw-bold">INVOICE NO</th>
                <th class="text-nowrap fw-bold">INVOICE DATE</th>
                <th class="fw-bold">CUSTOMER NAME</th>
                <th class="fw-bold">CUSTOMER GST NO</th>
                <th class="fw-bold">CUSTOMER PLACE</th>
                <th class="text-center fw-bold">QTY</th>
                <th class="text-end fw-bold">SUBTOTAL</th>
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
            </tr>
        </thead>
        <tbody>
            <!-- Data will be loaded via AJAX -->
        </tbody>
        <tfoot>
            <tr class="table-light fw-bold">
                <th colspan="6" class="text-end text-uppercase">Total:</th>
                <th class="text-center text-primary fw-bold" id="sales_gst_report_total_qty">0</th>
                <th class="text-end text-primary fw-bold" id="sales_gst_report_total_subtotal">₹0.00</th>
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
            </tr>
        </tfoot>
    </table>
</div>


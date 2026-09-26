<div class="card-datatable table-responsive">
    <table class="datatables-products table table-hover" id="creditNoteReportTable">
        <thead class="bg-light">
            <tr>
                <th>CN No</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Zone</th>
                <th>Sales Executive</th>
                <th>Reason</th>
                <th class="text-center">Total Qty</th>
                <th class="text-end">Sub Total</th>
                <th class="text-end">Discount</th>
                <th class="text-end">Tax Amt</th>
                <th class="text-end">Other Chg</th>
                <th class="text-end">Grand Total</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot class="bg-light fw-bold">
            <tr>
                <th colspan="6" class="text-end text-uppercase">Total:</th>
                <th class="text-center text-primary fw-bold" id="cn_report_total_qty">0</th>
                <th class="text-end text-primary fw-bold" id="cn_report_sub_total">₹0.00</th>
                <th class="text-end text-danger fw-bold" id="cn_report_discount">₹0.00</th>
                <th class="text-end text-primary fw-bold" id="cn_report_tax_amount">₹0.00</th>
                <th class="text-end text-primary fw-bold" id="cn_report_other_charges">₹0.00</th>
                <th class="text-end text-success fw-bold" id="cn_report_grand_total">₹0.00</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>

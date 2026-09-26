<div class="card-datatable table-responsive">
    <table class="datatables-products table premium-table table-hover text-nowrap" id="despatchReportTable">
        <thead class="bg-light">
            <tr>
                <th class="text-center">S.No</th>
                <th>Sales Order No</th>
                <th>OrderAxe Order No</th>
                <th>Order Type</th>
                <th>Sale Order Date</th>
                <th>Sales Executive</th>
                <th>Customer Name</th>
                <th>Place</th>
                <th>Zone</th>
                <th class="text-center">Dhoti Shirts</th>
                <th class="text-center">White</th>
                <th class="text-center">Core</th>
                <th class="text-center">Bravo</th>
                <th class="text-center">Deal</th>
                <th class="text-center">Formal</th>
                <th class="text-center fw-bold text-dark">Total Qty</th>
                <th>Requested Delivery Date</th>
                <th>Status</th>
                <th class="text-center text-success fw-bold">Despatch Qty</th>
                <th class="text-center text-danger fw-bold">Pending Qty</th>
                <th>Partial D.Date</th>
                <th>Despatch Complete Date</th>
                <th>Reason for Delayed Delivery</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot class="bg-light fw-bold">
            <tr>
                <th colspan="9" class="text-end text-uppercase">Total:</th>
                <th class="text-center text-primary fw-bold" id="despatch_report_dhoti_qty">0</th>
                <th class="text-center text-primary fw-bold" id="despatch_report_white_qty">0</th>
                <th class="text-center text-primary fw-bold" id="despatch_report_core_qty">0</th>
                <th class="text-center text-primary fw-bold" id="despatch_report_bravo_qty">0</th>
                <th class="text-center text-primary fw-bold" id="despatch_report_deal_qty">0</th>
                <th class="text-center text-primary fw-bold" id="despatch_report_formal_qty">0</th>
                <th class="text-center fw-bold text-dark" id="despatch_report_total_qty">0</th>
                <th colspan="2"></th>
                <th class="text-center text-success fw-bold" id="despatch_report_delivered_qty">0</th>
                <th class="text-center text-danger fw-bold" id="despatch_report_pending_qty">0</th>
                <th colspan="3"></th>
            </tr>
        </tfoot>
    </table>
</div>

<style>
    /* Sticky Columns specific styles */
    #despatch-report .table-responsive {
        overflow-x: auto;
    }
    
    .sticky-col-sno {
        position: sticky !important;
        left: 0;
        min-width: 60px;
        max-width: 60px;
        background-color: #fff !important;
        z-index: 1;
        box-shadow: inset -1px 0 0 #e2e8f0;
    }
    
    .sticky-col-so {
        position: sticky !important;
        left: 60px;
        min-width: 140px;
        max-width: 140px;
        background-color: #fff !important;
        z-index: 1;
        box-shadow: inset -1px 0 0 #e2e8f0;
    }
    
    .sticky-col-axe {
        position: sticky !important;
        left: 200px;
        min-width: 160px;
        max-width: 160px;
        background-color: #fff !important;
        z-index: 1;
        box-shadow: inset -1px 0 0 #e2e8f0;
    }

    #despatch-report thead .sticky-col-sno,
    #despatch-report thead .sticky-col-so,
    #despatch-report thead .sticky-col-axe {
        background-color: #f8f9fa !important;
        z-index: 3 !important;
    }

    #despatch-report tbody tr:hover .sticky-col-sno,
    #despatch-report tbody tr:hover .sticky-col-so,
    #despatch-report tbody tr:hover .sticky-col-axe {
        background-color: #f8faff !important;
    }
    
    /* Ensure DataTables doesn't hide sticky columns */
    #despatch-report .datatables-products th.sticky-col-sno,
    #despatch-report .datatables-products td.sticky-col-sno,
    #despatch-report .datatables-products th.sticky-col-so,
    #despatch-report .datatables-products td.sticky-col-so,
    #despatch-report .datatables-products th.sticky-col-axe,
    #despatch-report .datatables-products td.sticky-col-axe {
        display: table-cell !important;
    }
</style>

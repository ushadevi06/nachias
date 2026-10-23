<!-- Dispatch Orders Dual Tabs (Product Wise & Party Wise) -->
<div class="row mb-3">
    <div class="col-12">
        <ul class="nav nav-pills p-1 bg-lighter rounded-3 d-inline-flex border" id="dispatchOrdersSubTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link active px-4 py-2 fw-semibold" id="dispatch-product-wise-tab" data-bs-toggle="pill" data-bs-target="#dispatch-product-wise-pane" role="tab" aria-controls="dispatch-product-wise-pane" aria-selected="true">
                    <i class="ri ri-shirt-line me-1"></i> Product Wise
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button type="button" class="nav-link px-4 py-2 fw-semibold" id="dispatch-party-wise-tab" data-bs-toggle="pill" data-bs-target="#dispatch-party-wise-pane" role="tab" aria-controls="dispatch-party-wise-pane" aria-selected="false">
                    <i class="ri ri-user-star-line me-1"></i> Party Wise
                </button>
            </li>
        </ul>
    </div>
</div>

<div class="tab-content p-0" id="dispatchOrdersTabContent">
    <!-- Tab 1: Product Wise -->
    <div class="tab-pane fade show active" id="dispatch-product-wise-pane" role="tabpanel" aria-labelledby="dispatch-product-wise-tab">
        <div class="card-datatable table-responsive">
            <table class="datatables-products table table-hover" id="dispatchProductWiseTable">
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
                <tbody id="dispatchProductWiseTbody">
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <th colspan="5" class="text-end text-uppercase">Total:</th>
                        <th class="text-center text-primary" id="dispatch_pw_total_36">0</th>
                        <th class="text-center text-primary" id="dispatch_pw_total_38">0</th>
                        <th class="text-center text-primary" id="dispatch_pw_total_40">0</th>
                        <th class="text-center text-primary" id="dispatch_pw_total_42">0</th>
                        <th class="text-center text-primary" id="dispatch_pw_total_44">0</th>
                        <th class="text-center text-primary" id="dispatch_pw_total_46">0</th>
                        <th class="text-center text-primary" id="dispatch_pw_total_48">0</th>
                        <th class="text-center text-primary" id="dispatch_pw_total_50">0</th>
                        <th class="text-center text-danger fw-bold fs-6" id="dispatch_pw_total_qty">0</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Tab 2: Party Wise -->
    <div class="tab-pane fade" id="dispatch-party-wise-pane" role="tabpanel" aria-labelledby="dispatch-party-wise-tab">
        <div class="card-datatable table-responsive">
            <table class="datatables-products table table-hover" id="dispatchPartyWiseTable">
                <thead class="table-light">
                    <tr>
                        <th class="text-center fw-bold" style="width: 50px;">S.NO</th>
                        <th class="fw-bold text-nowrap">INVOICE NO</th>
                        <th class="fw-bold text-nowrap">INVOICE DATE</th>
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
                <tbody id="dispatchPartyWiseTbody">
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <th colspan="11" class="text-end text-uppercase">Total:</th>
                        <th class="text-center text-primary" id="dispatch_party_total_36">0</th>
                        <th class="text-center text-primary" id="dispatch_party_total_38">0</th>
                        <th class="text-center text-primary" id="dispatch_party_total_40">0</th>
                        <th class="text-center text-primary" id="dispatch_party_total_42">0</th>
                        <th class="text-center text-primary" id="dispatch_party_total_44">0</th>
                        <th class="text-center text-primary" id="dispatch_party_total_46">0</th>
                        <th class="text-center text-primary" id="dispatch_party_total_48">0</th>
                        <th class="text-center text-primary" id="dispatch_party_total_50">0</th>
                        <th class="text-center text-danger fw-bold fs-6" id="dispatch_party_total_qty">0</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<style>
    #dispatchProductWiseTable th, #dispatchPartyWiseTable th {
        font-size: 0.82rem;
        letter-spacing: 0.3px;
        vertical-align: middle;
    }
    #dispatchProductWiseTable td, #dispatchPartyWiseTable td {
        vertical-align: middle;
    }
    #dispatchOrdersSubTabs .nav-link {
        color: #566a7f;
        border-radius: 0.375rem;
        transition: all 0.2s ease-in-out;
    }
    #dispatchOrdersSubTabs .nav-link.active {
        background-color: #696cff;
        color: #fff !important;
        box-shadow: 0 2px 4px rgba(105, 108, 255, 0.4);
    }
</style>

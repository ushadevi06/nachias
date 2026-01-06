@extends('layouts.common')
@section('title', 'Add Job Card Entry - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="" method="POST" class="common-form">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Add Job Card Entry</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="job_card_no" placeholder="Enter Job Card Number" name="job_card_no" value="JC20250924-001-K">
                                    <label for="job_card_no">Job Card Number * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="cutting_no" placeholder="Enter Cutting No" name="cutting_no" value="CF0156/23">
                                    <label for="cutting_no">Cutting No * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control form-control issue_date" placeholder="Enter Issue Date" />
                                    <label for="issue_date">Issue Date * </label>
                                </div>
                            </div>
                            {{-- <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="sales_order" name="sales_order" class="form-select select2" data-placeholder="Select Sales Order">
                                        <option value="">Select Sales Order</option>
                                        <option value="SO-1001">SO-1001</option>
                                        <option value="SO-1002">SO-1002</option>
                                        <option value="SO-1003">SO-1003</option>
                                    </select>
                                    <label for="sales_order">Sales Order </label>
                                </div>
                            </div> --}}
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control form-control delivery_date" placeholder="Enter Delivery Date" />
                                    <label for="delivery_date">Delivery Date * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <label class="mb-2">Washing</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="washing" id="washing_yes" value="Yes">
                                        <label class="form-check-label" for="washing_yes">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="washing" id="washing_no" value="No" checked>
                                        <label class="form-check-label" for="washing_no">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="width" placeholder="Enter Width" name="width">
                                    <label for="width">Width</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="mrp" placeholder="Enter MRP" name="mrp">
                                    <label for="mrp">MRP</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="fs" placeholder="Enter F/S" name="fs">
                                    <label for="fs">F/S</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="hs" placeholder="Enter H/S" name="hs">
                                    <label for="hs">H/S</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="season" name="season" class="form-select select2" data-placeholder="Select Season">
                                        <option value="">Select Season</option>
                                        <option value="Pongal">Pongal Season</option>
                                        <option value="Navaratri / Dussehra">Navaratri / Dussehra Season</option>
                                        <option value="Diwali">Diwali Season</option>
                                        <option value="Christmas / New Year">Christmas / New Year Season</option>
                                    </select>
                                    <label for="season">Season </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="brand" name="brand" class="form-select select2" data-placeholder="Select Brand">
                                        <option value="">Select Brand</option>
                                        <option value="Casino Deal(CD)">Casino Deal(CD)</option>
                                        <option value="Casino Gold(CG)">Casino Gold(CS)</option>
                                        <option value="Casino Formal(CF)">Casino Formal(CF)</option>
                                        <option value="Casino Premium(CP)">Casino Premium(CP)</option>
                                    </select>
                                    <label for="brand">Brand </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="receipt_store" name="" class="form-select select2" data-placeholder="Select Receipt Store">
                                        <option value="">Select Receipt Store</option>
                                        <option value="Finished Goods">Finished Goods</option>
                                    </select>
                                    <label for="receipt_store">Receipt Store </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="input-group">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="process_group" name="process_group" class="form-control" placeholder="Select Process Group" readonly>
                                        <label for="process_group">Process Group</label>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#processGroupModal">
                                        <i class="ri ri-search-line"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="status" name="status" class="form-select select2" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Urgent">Urgent</option>
                                        <option value="Normal">Normal</option>
                                    </select>
                                    <label for="status">Status </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea id="remarks" name="remarks" class="form-control" placeholder="Enter Remarks" style="height: 100px;"></textarea>
                                    <label for="remarks">Remarks</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Tailoring Specification</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="fit" name="fit" class="form-select select2" data-placeholder="Select Fit">
                                        <option value="">Select Fit</option>
                                        <option value="Slim Fit">Slim Fit</option>
                                        <option value="Regular Fit">Regular Fit</option>
                                        <option value="Skinny Fit">Skinny Fit</option>
                                        <option value="Relaxed Fit">Relaxed Fit</option>
                                    </select>
                                    <label for="fit">Fit</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="patti_type" name="patti_type" class="form-select select2" data-placeholder="Select Patti Type">
                                        <option value="">Select Patti Type</option>
                                        <option value="Regular Patti">Regular Patti</option>
                                        <option value="Box Patti">Box Patti</option>
                                        <option value="Concealed Patti">Concealed Patti</option>
                                        <option value="French Patti">French Patti</option>
                                    </select>
                                    <label for="patti_type">Patti Type</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="collar_type" name="collar_type" class="form-select select2" data-placeholder="Select Collar Type">
                                        <option value="">Select Collar Type</option>
                                        <option value="Regular Collar">Regular Collar</option>
                                        <option value="Spread Double Canvas">Spread Double Canvas</option>
                                        <option value="Spread Mandarin Collar">Spread Mandarin Collar</option>
                                        <option value="Button-Down Double Canvas">Button-Down Double Canvas</option>
                                    </select>
                                    <label for="collar_type">Collar Type</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="cuff_type" name="cuff_type" class="form-select select2" data-placeholder="Select Cuff Type">
                                        <option value="">Select Cuff Type</option>
                                        <option value="Cross">Cross</option>
                                        <option value="French Cuff">French Cuff</option>
                                        <option value="Barrel Cuff">Barrel Cuff</option>
                                        <option value="Convertible Cuff">Convertible Cuff</option>
                                    </select>
                                    <label for="cuff_type">Cuff Type</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="pocket_type" name="pocket_type" class="form-select select2" data-placeholder="Select Pocket Type">
                                        <option value="">Select Pocket Type</option>
                                        <option value="Cross">Cross</option>
                                        <option value="Patch Pocket">Patch Pocket</option>
                                        <option value="Flap Pocket">Flap Pocket</option>
                                        <option value="Welt Pocket">Welt Pocket</option>
                                    </select>
                                    <label for="pocket_type">Pocket Type</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="bottom_cut" name="bottom_cut" class="form-select select2" data-placeholder="Select Bottom Cut">
                                        <option value="">Select Bottom Cut</option>
                                        <option value="Aero Cut">Aero Cut</option>
                                        <option value="Curved Cut">Curved Cut</option>
                                        <option value="Deep Curve Cut">Deep Curve Cut</option>
                                        <option value="Gusset Cut">Gusset Cut</option>
                                    </select>
                                    <label for="bottom_cut">Bottom Cut</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="cutting_master" name="cutting_master" class="form-select select2" data-placeholder="Select Cutting Master">
                                        <option value="">Select Cutting Master</option>
                                        <option value="Rajkumar">Rajkumar</option>
                                    </select>
                                    <label for="cutting_master">Cutting Master</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control cutting_date" id="cutting_date" name="cutting_date" placeholder="Select Cutting Date">
                                    <label for="cutting_date">Cutting Date</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="cutting_issue_unit" name="cutting_issue_unit" class="form-select select2" data-placeholder="Select Cutting Issue Unit">
                                        <option value="">Select Cutting Issue Unit</option>
                                        <option value="Nachias Fashion Private Limited">Nachias Fashion Private Limited</option>
                                        <option value="Samayanallur">Samayanallur</option>
                                        <option value="Kalavasal">Kalavasal</option>
                                    </select>
                                    <label for="cutting_issue_unit">Cutting Issue Unit</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Cutting Size Ratio</h4>
                        </div>
                        <div class="row g-4 mb-3">
                             <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="size_ratio" name="size_ratio" class="form-select select2" data-placeholder="Select Size Ratio">
                                        <option value="">Select Size Ratio</option>
                                        <option value="1">(36,38,40,42,44) - (1,2,3,4,1)</option>
                                    </select>
                                    <label for="size_ratio">Select Size Ratio</label>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="align-middle">SIZE</th>
                                        <th colspan="5">CUTTING SIZE RATIO</th>
                                        <th colspan="2"></th>
                                        <th colspan="2">CUTTING MARK AND LAY</th>
                                    </tr>
                                    <tr>
                                        <th>36</th>
                                        <th>38</th>
                                        <th>40</th>
                                        <th>42</th>
                                        <th>44</th>
                                        <th></th>
                                        <th></th>
                                        <th>SIZE</th>
                                        <th>MARK</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>QTY - F/S</strong></td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="8"></td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="8"></td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="8"></td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="-"></td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="-"></td>
                                        <td></td>
                                        <td></td>
                                        <td><input type="text" class="form-control form-control-sm"></td>
                                        <td><input type="text" class="form-control form-control-sm"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>QTY - F/S</strong></td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="-"></td>
                                        <td colspan="3"><input type="text" class="form-control form-control-sm text-center" value="(38,40,42)"></td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="-"></td>
                                        <td></td>
                                        <td></td>
                                        <td><input type="text" class="form-control form-control-sm"></td>
                                        <td><input type="text" class="form-control form-control-sm"></td>
                                    </tr>
                                     <tr>
                                        <td><strong>QTY - H/S</strong></td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="-"></td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="8"></td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="8"></td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="8"></td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="-"></td>
                                        <td></td>
                                        <td></td>
                                        <td><input type="text" class="form-control form-control-sm"></td>
                                        <td><input type="text" class="form-control form-control-sm"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Fabric Details</h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle">
                                <thead>
                                    <tr>
                                        <!-- Placeholders for images or color relation -->
                                        <th colspan="2" style="height: 50px; background-color: #f0f0f0;"></th>
                                        <th colspan="2" style="height: 50px; background-color: #e0e0e0;"></th>
                                        <th colspan="2" style="height: 50px; background-color: #d0d0d0;"></th>
                                        <th colspan="2" style="height: 50px; background-color: #c0c0c0;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="fw-bold">ART NO</td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="CF20077-1"></td>
                                        <td class="fw-bold">ART NO</td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="CF20077-2"></td>
                                        <td class="fw-bold">ART NO</td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="CF20078-1"></td>
                                        <td class="fw-bold">ART NO</td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="CF20078-2"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">WIDTH</td>
                                        <td><input type="text" class="form-control form-control-sm text-center"></td>
                                        <td class="fw-bold">WIDTH</td>
                                        <td><input type="text" class="form-control form-control-sm text-center"></td>
                                        <td class="fw-bold">WIDTH</td>
                                        <td><input type="text" class="form-control form-control-sm text-center"></td>
                                        <td class="fw-bold">WIDTH</td>
                                        <td><input type="text" class="form-control form-control-sm text-center"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Mtr/B.M</td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="103.5"></td>
                                        <td class="fw-bold">Mtr/B.M</td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="91"></td>
                                        <td class="fw-bold">Mtr/B.M</td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="112"></td>
                                        <td class="fw-bold">Mtr/B.M</td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="109"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">IN / OUT</td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="YES"></td>
                                        <td class="fw-bold">IN / OUT</td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="YES"></td>
                                        <td class="fw-bold">IN / OUT</td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="YES"></td>
                                        <td class="fw-bold">IN / OUT</td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="YES"></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">N.PATTI</td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="WHITE"></td>
                                        <td class="fw-bold">N.PATTI</td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="WHITE"></td>
                                        <td class="fw-bold">N.PATTI</td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="WHITE"></td>
                                        <td class="fw-bold">N.PATTI</td>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="WHITE"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Article Quantity Matrix</h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle" id="article-qty-matrix">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="align-middle" style="min-width: 120px;">ART NO</th>
                                        <th colspan="6">F/S</th>
                                        <th colspan="4">H/S</th>
                                        <th colspan="2">EX</th>
                                        <th rowspan="2" class="align-middle">TOTAL</th>
                                    </tr>
                                    <tr class="size-headers">
                                        <!-- F/S Sizes -->
                                        <th>36</th><th>38</th><th>40</th><th>42</th><th>44</th><th>46</th>
                                        <!-- H/S Sizes -->
                                        <th>38</th><th>40</th><th>42</th><th>44</th>
                                        <!-- EX -->
                                        <th>40 H/S</th><th>38 F/S</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $articles = ['CF20077-1', 'CF20077-2', 'CF20078-1', 'CF20078-2', 'CF20079-1', 'CF20079-2'];
                                    @endphp
                                    @foreach ($articles as $art)
                                    <tr>
                                        <td><input type="text" class="form-control form-control-sm text-center" value="{{ $art }}"></td>
                                        <!-- F/S Inputs -->
                                        <td><input type="number" class="form-control form-control-sm qty-input text-center" data-col="fs-36"></td>
                                        <td><input type="number" class="form-control form-control-sm qty-input text-center" data-col="fs-38"></td>
                                        <td><input type="number" class="form-control form-control-sm qty-input text-center" data-col="fs-40"></td>
                                        <td><input type="number" class="form-control form-control-sm qty-input text-center" data-col="fs-42"></td>
                                        <td><input type="number" class="form-control form-control-sm qty-input text-center" data-col="fs-44"></td>
                                        <td><input type="number" class="form-control form-control-sm qty-input text-center" data-col="fs-46"></td>
                                        <!-- H/S Inputs -->
                                        <td><input type="number" class="form-control form-control-sm qty-input text-center" data-col="hs-38"></td>
                                        <td><input type="number" class="form-control form-control-sm qty-input text-center" data-col="hs-40"></td>
                                        <td><input type="number" class="form-control form-control-sm qty-input text-center" data-col="hs-42"></td>
                                        <td><input type="number" class="form-control form-control-sm qty-input text-center" data-col="hs-44"></td>
                                        <!-- EX Inputs -->
                                        <td><input type="number" class="form-control form-control-sm qty-input text-center" data-col="ex-1"></td>
                                        <td><input type="number" class="form-control form-control-sm qty-input text-center" data-col="ex-2"></td>
                                        <!-- Row Total -->
                                        <td><input type="text" class="form-control form-control-sm row-total text-center fw-bold" readonly tabindex="-1"></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="fw-bold">TOTAL</td>
                                        <!-- F/S Totals -->
                                        <td><input type="text" class="form-control form-control-sm col-total text-center fw-bold" data-col="fs-36" readonly tabindex="-1"></td>
                                        <td><input type="text" class="form-control form-control-sm col-total text-center fw-bold" data-col="fs-38" readonly tabindex="-1"></td>
                                        <td><input type="text" class="form-control form-control-sm col-total text-center fw-bold" data-col="fs-40" readonly tabindex="-1"></td>
                                        <td><input type="text" class="form-control form-control-sm col-total text-center fw-bold" data-col="fs-42" readonly tabindex="-1"></td>
                                        <td><input type="text" class="form-control form-control-sm col-total text-center fw-bold" data-col="fs-44" readonly tabindex="-1"></td>
                                        <td><input type="text" class="form-control form-control-sm col-total text-center fw-bold" data-col="fs-46" readonly tabindex="-1"></td>
                                        <!-- H/S Totals -->
                                        <td><input type="text" class="form-control form-control-sm col-total text-center fw-bold" data-col="hs-38" readonly tabindex="-1"></td>
                                        <td><input type="text" class="form-control form-control-sm col-total text-center fw-bold" data-col="hs-40" readonly tabindex="-1"></td>
                                        <td><input type="text" class="form-control form-control-sm col-total text-center fw-bold" data-col="hs-42" readonly tabindex="-1"></td>
                                        <td><input type="text" class="form-control form-control-sm col-total text-center fw-bold" data-col="hs-44" readonly tabindex="-1"></td>
                                        <!-- EX Totals -->
                                        <td><input type="text" class="form-control form-control-sm col-total text-center fw-bold" data-col="ex-1" readonly tabindex="-1"></td>
                                        <td><input type="text" class="form-control form-control-sm col-total text-center fw-bold" data-col="ex-2" readonly tabindex="-1"></td>
                                        <!-- Grand Total -->
                                        <td><input type="text" id="grand-total" class="form-control form-control-sm text-center fw-bold bg-light" readonly tabindex="-1"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>


                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Production Stages</h4>
                        </div>
                        
                        <!-- Stage Selection -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <div class="form-floating form-floating-outline">
                                    <select id="production_stage_select" class="form-select select2" data-placeholder="Select Production Stage">
                                        <option value="">Select Production Stage</option>
                                        <option value="Fabric Incharge">Fabric Incharge</option>
                                        <option value="Fabric Issued By">Fabric Issued By</option>
                                        <option value="Cutting Supervisor">Cutting Supervisor</option>
                                        <option value="Cutting Send By">Cutting Send By</option>
                                        <option value="Cutting Received By">Cutting Received By</option>
                                        <option value="Unit Incharge">Unit Incharge</option>
                                        <option value="Ready Section">Ready Section</option>
                                        <option value="Ready Store">Ready Store</option>
                                        <option value="Assemble">Assemble</option>
                                        <option value="Production Unit Send By">Production Unit Send By</option>
                                        <option value="H.O Received By">H.O Received By</option>
                                        <option value="Kaja & Button">Kaja & Button</option>
                                        <option value="Trimming & Checking">Trimming & Checking</option>
                                        <option value="Ironing">Ironing</option>
                                        <option value="Packing & Delivery">Packing & Delivery</option>
                                        <option value="F.G Store">F.G Store</option>
                                    </select>
                                    <label for="production_stage_select">Select Production Stage</label>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" id="production_stages_table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 20%;">Stage Name</th>
                                        <th style="width: 20%;">Issue Date</th>
                                        <th style="width: 25%;">Employee</th>
                                        <th style="width: 25%;">Received By</th>
                                        <th style="width: 10%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="production_stages_body">
                                    <!-- Dynamic rows will be added here -->
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-12 text-end mt-5">
                            <button type="button" class="btn btn-primary">Submit</button>
                            <a href="{{ url('job_card_entries') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </div>

                {{-- <div class="card">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Item Details </h4>
                        </div>
                        <div id="item-rows">
                            <div class="item-block mb-4">
                                <div class="row g-4 item-row">
                                    <div class="col-md-3 col-lg-2">
                                        <div class="form-floating form-floating-outline">
                                            <select id="item" class="select2 form-select" data-placeholder="Select Item">
                                                <option value="">Select Item</option>
                                                <option value="Men’s Casual Denim Shirt(ITEM001)">Men’s Casual Denim Shirt(ITEM001)</option>
                                                <option value="Men’s Formal Cotton Shirt(ITEM002)">Men’s Formal Cotton Shirt(ITEM002)</option>
                                                <option value="School Uniform Shirt(ITEM003)">School Uniform Shirt(ITEM003) </option>
                                                <option value="Kids Polo Shirt(ITEM004)">Kids Polo Shirt(ITEM004)</option>
                                                <option value="Premium Linen Shirt(ITEM005)">Premium Linen Shirt(ITEM005)</option>
                                            </select>
                                            <label for="item">Item </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-lg-1">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="quantity" placeholder="Enter Quatity" name="quantity">
                                            <label for="quantity">Quantity * </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-2">
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2 form-select uom" id="uom" data-placeholder="Select UOM">
                                                <option value="">Select UOM</option>
                                                <option value="PCS">PCS</option>
                                                <option value="MTR">MTR</option>
                                                <option value="ROLL">ROLL</option>
                                                <option value="KG">KG</option>
                                                <option value="SET">SET</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-2">
                                        <div class="form-floating form-floating-outline">
                                            <select id="size" class="select2 form-select" data-placeholder="Select Size">
                                                <option value="">Select Size</option>
                                                <option value="38,40,42,44 (1,2,3,7)">38,40,42,44 (1,2,3,7)</option>
                                                <option value="38,40 (5,2)">38,40 (5,2)</option>
                                                <option value="42,44 (5,7)">42,44 (5,7)</option>
                                                <option value="38,40,42 (1,3,2)">38,40,42 (1,3,2)</option>
                                                <option value="38,40,42 (1,3,1)">38,40,42 (1,3,1)</option>
                                            </select>
                                            <label for="size">Size * </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-2">
                                        <div class="form-floating form-floating-outline">
                                            <select id="sleeve" class="select2 form-select" data-placeholder="Select Sleeve">
                                                <option value="">Select Sleeve</option>
                                                <option value="Full Sleeve">Full Sleeve</option>
                                                <option value="Half Sleeve">Sleeve</option>
                                                <option value="Full-half Sleeve">Full-half Sleeve</option>
                                            </select>
                                            <label for="sleeve">Sleeve * </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-2">
                                        <div class="form-floating form-floating-outline">
                                            <select id="color" class="select2 form-select" data-placeholder="Select Color">
                                                <option value="">Select Color</option>
                                                <option value="White">White</option>
                                                <option value="Blue">Blue</option>
                                                <option value="Red">Red</option>
                                                <option value="Black">Black</option>
                                            </select>
                                            <label for="color">Color * </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-1 text-end">
                                        <button type="button" class="btn btn-primary add_item"><i class="icon-base ri ri-add-line"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="table table-bordered d-none mb-4" id="material_bom_table">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Material</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <div class="row g-4 production-row mt-4">
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" data-placeholder="Select Production Stages">
                                        <option value="">Select Production Stages</option>
                                        <option value="Cutting">Cutting</option>
                                        <option value="Stitching">Stitching</option>
                                        <option value="Printing">Printing</option>
                                        <option value="Ironing">Ironing</option>
                                        <option value="Packing">Packing</option>
                                    </select>
                                    <label for="production_stages">Production Stages </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control plan_start" id="planned_start" placeholder="Enter Planned Start Date" name="planned_start">
                                    <label for="planned_start">Planned Start Date * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control plan_end" id="planned_end" placeholder="Enter Planned End Date" name="planned_end">
                                    <label for="planned_end">Planned End Date * </label>
                                </div>
                            </div>
                            <div class="col-lg-1 text-end">
                                <button type="button" class="btn btn-primary add_stage"><i class="icon-base ri ri-add-line"></i></button>
                            </div>
                        </div>
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ url('job_card_entries') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </div> --}}
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="processGroupModal" tabindex="-1" aria-labelledby="processGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="processGroupModalLabel">Select Process Group</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered align-middle text-center" id="processGroupTable">
                    <thead class="table-light">
                        <tr>
                            <th>Select</th>
                            <th>Code</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="radio" name="process_option" value="CKD-F/S|Checked Full Sleeve"></td>
                            <td>CKD-F/S</td>
                            <td>Checked Full Sleeve</td>
                        </tr>
                        <tr>
                            <td><input type="radio" name="process_option" value="CKD-FS/HS|Checked Full & Half Sleeve"></td>
                            <td>CKD-FS/HS</td>
                            <td>Checked Full & Half Sleeve</td>
                        </tr>
                        <tr>
                            <td><input type="radio" name="process_option" value="CKD-H/S|Checked Half Sleeve"></td>
                            <td>CKD-H/S</td>
                            <td>Checked Half Sleeve</td>
                        </tr>
                        <tr>
                            <td><input type="radio" name="process_option" value="OTH-F/S|Others Full Sleeve"></td>
                            <td>OTH-F/S</td>
                            <td>Others Full Sleeve</td>
                        </tr>
                        <tr>
                            <td><input type="radio" name="process_option" value="OTH-FS/HS|Others Full & Half Sleeve"></td>
                            <td>OTH-FS/HS</td>
                            <td>Others Full & Half Sleeve</td>
                        </tr>
                        <tr>
                            <td><input type="radio" name="process_option" value="OTH-H/S|Others Half Sleeve"></td>
                            <td>OTH-H/S</td>
                            <td>Others Half Sleeve</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmProcessGroup">Select</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#item-rows').on('click', '.add_item', function() {
            var html = `
        <div class="item-block mb-4 mt-3">
            <div class="row g-4">
                <div class="col-md-3 col-lg-2">
                    <div class="form-floating form-floating-outline">
                        <select class="select2 form-select item" data-placeholder="Select Item">
                            <option value="">Select Item</option>
                            <option value="Men’s Casual Denim Shirt(ITEM001)">Men’s Casual Denim Shirt(ITEM001)</option>
                            <option value="Men’s Formal Cotton Shirt(ITEM002)">Men’s Formal Cotton Shirt(ITEM002)</option>
                            <option value="School Uniform Shirt(ITEM003)">School Uniform Shirt(ITEM003)	</option>
                            <option value="Kids Polo Shirt(ITEM004)">Kids Polo Shirt(ITEM004)</option>
                            <option value="Premium Linen Shirt(ITEM005)">Premium Linen Shirt(ITEM005)</option>
                        </select>
                        <label>Item</label>
                    </div>
                </div>
                <div class="col-md-2 col-lg-1">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control quantity" placeholder="Enter Quantity">
                        <label>Quantity *</label>
                    </div>
                </div>
                <div class="col-md-3 col-lg-2">
                    <div class="form-floating form-floating-outline">
                        <select class="select2 form-select uom"  data-placeholder="Select UOM">
                            <option value="">Select UOM</option>
                            <option value="PCS">PCS</option>
                            <option value="MTR">MTR</option>
                            <option value="ROLL">ROLL</option>
                            <option value="KG">KG</option>
                            <option value="SET">SET</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 col-lg-2">
                    <div class="form-floating form-floating-outline">
                        <select class="select2 form-select" data-placeholder="Select Size">
                            <option value="">Select Size</option>
                            <option value="38,40,42,44 (1,2,3,7)">38,40,42,44 (1,2,3,7)</option>
                            <option value="38,40 (5,2)">38,40 (5,2)</option>
                            <option value="42,44 (5,7)">42,44 (5,7)</option>
                            <option value="38,40,42 (1,3,2)">38,40,42 (1,3,2)</option>
                            <option value="38,40,42 (1,3,1)">38,40,42 (1,3,1)</option>
                        </select>
                        <label for="size">Size * </label>
                    </div>
                </div>
                <div class="col-md-3 col-lg-2">
                    <div class="form-floating form-floating-outline">
                        <select class="select2 form-select" data-placeholder="Select Sleeve">
                            <option value="">Select Sleeve</option>
                            <option value="Full Sleeve">Full Sleeve</option>
                            <option value="Half Sleeve">Sleeve</option>
                            <option value="Full-half Sleeve">Full-half Sleeve</option>
                        </select>
                        <label for="sleeve">Sleeve * </label>
                    </div>
                </div>
                <div class="col-md-3 col-lg-2">
                    <div class="form-floating form-floating-outline">
                        <select class="select2 form-select" data-placeholder="Select Color">
                            <option value="">Select Color</option>
                            <option value="White">White</option>
                            <option value="Blue">Blue</option>
                            <option value="Red">Red</option>
                            <option value="Black">Black</option>
                        </select>
                        <label for="color">Color * </label>
                    </div>
                </div>
                <div class="col-lg-1 text-end">
                    <button type="button" class="btn btn-danger delete_item"><i class="ri ri-delete-bin-line icon-base"></i> </button>
                </div>
            </div>
        </div>
        `;
            $('#item-rows').append(html);
            $(".select2").select2();
        });
        $('#item-rows').on('click', '.delete_item', function() {
            $(this).closest('.item-block').remove();
        });
        $(document).on("click", ".add_stage", function() {
            var production_row =
                `<div class="stage-block mt-3">
                    <div class="col-lg-12">
                        <div class="row g-4">
                            <div class="col-md-6 col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" data-placeholder="Select Production Stages">
                                        <option value="">Select Production Stages</option>
                                        <option value="Cutting">Cutting</option>
                                        <option value="Stitching">Stitching</option>
                                        <option value="Printing">Printing</option>
                                        <option value="Ironing">Ironing</option>
                                        <option value="Packing">Packing</option>
                                    </select>
                                    <label for="production_stages">Production Stages </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control plan_start" id="planned_start" placeholder="Enter Planned Start Date" name="planned_start">
                                    <label for="planned_start">Planned Start Date * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control plan_end" id="planned_end" placeholder="Enter Planned End Date" name="planned_end">
                                    <label for="planned_end">Planned End Date * </label>
                                </div>
                            </div>
                            <div class="col-lg-1 text-end">
                                <button type="button" class="btn btn-danger delete_production"><i class="ri ri-delete-bin-line icon-base"></i> </button>
                            </div>
                        </div>
                    </div>
                </div>`;
            $('.production-row').append(production_row);
            $(".select2").select2();
            $('.plan_start').flatpickr({
                dateFormat: 'd-m-Y',
                defaultDate: 'today',
                minDate: 'today',
                allowInput: true
            });

            $('.plan_end').flatpickr({
                dateFormat: 'd-m-Y',
                defaultDate: 'today',
                minDate: 'today',
                allowInput: true
            });
        });
        $(document).on("click", ".delete_production", function() {
            $(this).closest('.stage-block').remove();
        });
        $('.job_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            minDate: 'today',
            allowInput: true
        });
        $('.delivery_date').flatpickr({
            dateFormat: 'd-m-Y',
            minDate: 'today',
            allowInput: true
        });
        $('.cutting_date').flatpickr({
            dateFormat: 'd-m-Y',
            allowInput: true
        });
        $('.stage-date').flatpickr({
            dateFormat: 'd-m-Y',
            allowInput: true
        });
        $('.issue_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            allowInput: true
        });
        $('.plan_start').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            minDate: 'today',
            allowInput: true
        });
        $('.plan_end').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            minDate: 'today',
            allowInput: true
        });
        $('#item').change(function() {
            var item = $(this).val();
            if (item) {
                $('#material_bom_table').removeClass('d-none');
                var newRow = `<tr>
                <td>1</td>
                <td>Cotton Poplin 60 GSM(M001)</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Plastic Button 18L(M002)</td>
            </tr>`;
                $('#material_bom_table tbody').append(newRow);
            } else {
                $('#material_bom_table').addClass('d-none');
            }
        });
        
        let addedStages = [];
        
        $('#production_stage_select').on('change', function() {
            const stageName = $(this).val();
            
            if (!stageName) {
                return;
            }
            
            if (addedStages.includes(stageName)) {
                alert('This stage has already been added.');
                $(this).val('').trigger('change');
                return;
            }
            
            addedStages.push(stageName);
            
            const rowHtml = `
                <tr data-stage="${stageName}">
                    <td>
                        <input type="text" class="form-control form-control-sm" value="${stageName}" readonly>
                        <input type="hidden" name="stages[${addedStages.length - 1}][name]" value="${stageName}">
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm dynamic-stage-date" name="stages[${addedStages.length - 1}][issue_date]" placeholder="Select Date">
                    </td>
                    <td>
                        <select class="form-select form-select-sm select2-dynamic" name="stages[${addedStages.length - 1}][employee]" data-placeholder="Select Employee">
                            <option value="">Select Employee</option>
                            <option value="Rajkumar">Rajkumar</option>
                            <option value="Suresh">Suresh</option>
                            <option value="Ganesh">Ganesh</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm" name="stages[${addedStages.length - 1}][received_by]" placeholder="Received By">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-stage">
                            <i class="ri ri-delete-bin-line"></i>
                        </button>
                    </td>
                </tr>
            `;
            
            $('#production_stages_body').append(rowHtml);
            
            // Initialize Select2 for the new row
            $('.select2-dynamic').last().select2();
            
            // Initialize Flatpickr for the new date field
            $('.dynamic-stage-date').last().flatpickr({
                dateFormat: 'd-m-Y',
                allowInput: true
            });
            
            // Reset dropdown
            $(this).val('').trigger('change');
        });
        
        // Remove stage row
        $('#production_stages_body').on('click', '.remove-stage', function() {
            const row = $(this).closest('tr');
            const stageName = row.data('stage');
            
            // Remove from addedStages array
            addedStages = addedStages.filter(s => s !== stageName);
            
            // Remove the row
            row.remove();
        });
        
        $('#confirmProcessGroup').click(function() {
            var selectedValue = $('input[name="process_option"]:checked').val();
            if (selectedValue) {
                var parts = selectedValue.split('|');
                var code = parts[0];
                var desc = parts[1];
                $('#process_group').val(code + ' - ' + desc);
                $('#processGroupModal').modal('hide');
            } else {
                alert('Please select a Process Group first.');
            }
            });

            $('#processGroupTable tbody tr').on('click', function() {
            $('#processGroupTable tbody tr').removeClass('table-active');
            $(this).addClass('table-active');
            $(this).find('input[type="radio"]').prop('checked', true);
        });


        // Article Quantity Matrix Calculations
        const $matrix = $('#article-qty-matrix');

        function calculateMatrix() {
            let grandTotal = 0;

            // 1. Calculate Row Totals
            $matrix.find('tbody tr').each(function() {
                let rowTotal = 0;
                $(this).find('.qty-input').each(function() {
                    const val = parseFloat($(this).val()) || 0;
                    rowTotal += val;
                });
                $(this).find('.row-total').val(rowTotal > 0 ? rowTotal : '');
                grandTotal += rowTotal;
            });

            // 2. Calculate Column Totals
            $matrix.find('tfoot .col-total').each(function() {
                const colKey = $(this).data('col');
                let colSum = 0;
                
                $matrix.find(`tbody .qty-input[data-col="${colKey}"]`).each(function() {
                    colSum += parseFloat($(this).val()) || 0;
                });

                $(this).val(colSum > 0 ? colSum : '');
            });

            // 3. Update Grand Total
            $('#grand-total').val(grandTotal > 0 ? grandTotal : '');
        }

        // Attach event listener
        $matrix.on('input', '.qty-input', function() {
            calculateMatrix();
        });

    });
</script>
@endsection
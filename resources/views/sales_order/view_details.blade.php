@extends('layouts.common')
@section('title', 'View Sale Order - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Sale Order</h4>
                <a href="{{ url('sales_order') }}" class="btn btn-primary">
                    <i class="ri ri-arrow-left-line back-arrow"></i>Back
                </a>
            </div>

            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">

                        <!-- ========== ORDER DETAILS ========== -->
                        <div class="col-lg-12">
                            <h6>Order Details:</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">SO Number: </label>
                            <div class="text-muted">S0-1001</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">SO Date:</label>
                            <div class="text-muted">19-Sep-2025</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Customer Name:</label>
                            <div class="text-muted">Hero Mens Wear (CUS001)</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Customer PO Ref No:</label>
                            <div class="text-muted">PO-458</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Payment Terms:</label>
                            <div class="text-muted">30 Days</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Broker / Sales Agent:</label>
                            <div class="text-muted">Neha Sharma (SA102)</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Expected Delivery Date:</label>
                            <div class="text-muted">15-Sep-2025</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Zone:</label>
                            <div class="text-muted">North Zone</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Address Line 1:</label>
                            <div class="text-muted">12, Main Street</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Address Line 1:</label>
                            <div class="text-muted">Delhi</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Zipcode:</label>
                            <div class="text-muted">625011</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Status:</label>
                            <div class="text-muted"><span class="badge bg-warning">Pending</span></div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Remarks:</label>
                            <div class="text-muted">Urgent delivery requested</div>
                        </div>

                        <div class="col-lg-12">
                            <hr>
                        </div>

                        <!-- ========== ITEM DETAILS ========== -->
                        <div class="col-lg-12">
                            <h6>Item Details:</h6>
                        </div>
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Brand</th>
                                            <th>Item (Code)</th>
                                            <th>Quantity Order</th>
                                            <th>Rate Per Unit</th>
                                            <th>UOM</th>
                                            <th>Size</th>
                                            <th>Sleeve Type</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>UrbanStitch</td>
                                            <td>Men’s Casual Denim Shirt <span class="mini-title">(ITEM001)</span></td>
                                            <td>3</td>
                                            <td>₹30</td>
                                            <td>PCS</td>
                                            <td>38, 40, 42</td>
                                            <td>Checked Full Sleeve</td>
                                            <td>₹90</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>EliteThreads</td>
                                            <td>Men’s Formal Cotton Shirt <span class="mini-title">(ITEM002)</span></td>
                                            <td>1</td>
                                            <td>₹50</td>
                                            <td>PCS</td>
                                            <td>40, 42</td>
                                            <td>Others Half Sleeve</td>
                                            <td>₹50</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <hr>
                        </div>

                        <!-- ========== TAX & CHARGES SECTION ========== -->
                        <div class="col-lg-12">
                            <h6>Tax & Charges:</h6>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="row g-3 align-items-center">

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Sub Total:</label>
                                        <input type="text" class="form-control text-end" readonly value="₹140.00">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Discount:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-end" readonly value="2%">
                                            <span class="input-group-text">%</span>
                                            <input type="text" class="form-control text-end" readonly value="₹2.80">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Total After Discount:</label>
                                        <input type="text" class="form-control text-end" readonly value="₹137.20">
                                    </div>

                                    <div class="col-md-12 mt-3">
                                        <label class="form-label fw-bold">Other State?</label><br>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="other_state" id="other_state_yes" value="yes">
                                            <label class="form-check-label" for="other_state_yes">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="other_state" id="other_state_no" value="no" checked>
                                            <label class="form-check-label" for="other_state_no">No</label>
                                        </div>
                                    </div>

                                    <!-- GST Sections -->
                                    <div class="col-md-4" id="cgst_section">
                                        <label class="form-label fw-bold">CGST (9%):</label>
                                        <input type="text" class="form-control text-end" readonly value="₹12.35">
                                    </div>
                                    <div class="col-md-4" id="sgst_section">
                                        <label class="form-label fw-bold">SGST (9%):</label>
                                        <input type="text" class="form-control text-end" readonly value="₹12.35">
                                    </div>
                                    <div class="col-md-4 d-none" id="igst_section">
                                        <label class="form-label fw-bold">IGST (18%):</label>
                                        <input type="text" class="form-control text-end" readonly value="₹24.70">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Grand Total:</label>
                                        <input type="text" class="form-control text-end fw-bold text-success" readonly value="₹162.00">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Received Amount:</label>
                                        <input type="text" class="form-control text-end" readonly value="₹100.00">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-danger">Due Amount:</label>
                                        <input type="text" class="form-control text-end text-danger fw-bold" readonly value="₹62.00">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div> <!-- row end -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

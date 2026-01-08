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
                            <label class="detail-title">Address Line 2:</label>
                            <div class="text-muted">Delhi</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Zipcode:</label>
                            <div class="text-muted">625011</div>
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
                        <div class="row g-4 mt-2">
                            <!-- Additional Information Card -->
                            <div class="col-lg-6">
                                <div class="card h-100 border shadow-none">
                                    <div class="card-body">
                                        <div class="card-header-box mb-4">
                                            <h6 class="mb-0">Additional Information</h6>
                                        </div>
                                        <div class="row g-4">
                                            <div class="col-md-12">
                                                <label class="detail-title d-block">Status:</label>
                                                <div class="text-muted"><span class="badge bg-label-warning">Pending</span></div>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="detail-title d-block">Payment Terms:</label>
                                                <div class="text-muted h-px-100 border rounded p-2">30 Days</div>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="detail-title d-block">Remarks:</label>
                                                <div class="text-muted border rounded p-2">Urgent delivery requested</div>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="detail-title d-block">Additional Attachments:</label>
                                                <div class="mt-2">
                                                    <img src="{{ asset('assets/img/illustrations/page-pricing-basic.png') }}" alt="Attachment Preview" class="img-fluid rounded border shadow-sm" style="max-height: 200px; cursor: pointer;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tax Summary Card -->
                            <div class="col-lg-6">
                                <div class="card h-100 border shadow-none">
                                    <div class="card-body">
                                        <div class="card-header-box mb-4">
                                            <h6 class="mb-0">Tax Summary</h6>
                                        </div>
                                        <div class="d-flex flex-column gap-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-semibold">Total Qty:</span>
                                                <span class="fw-bold">4.00</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-semibold">Sub Total:</span>
                                                <span class="fw-bold">₹140.00</span>
                                            </div>
                                            <div class="row align-items-center g-2">
                                                <div class="col-6">
                                                    <span class="fw-semibold">Discount:</span>
                                                </div>
                                                <div class="col-6 text-end">
                                                    <span class="badge bg-label-danger">2%</span>
                                                    <div class="fw-bold mt-1">₹2.80</div>
                                                </div>
                                            </div>
                                            <hr class="my-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-semibold">Net Amount (Before Tax):</span>
                                                <span class="fw-bold">₹137.20</span>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <span class="fw-semibold">Other State:</span>
                                                <span class="badge bg-label-secondary">No</span>
                                            </div>

                                            <!-- CGST Row -->
                                            <div class="row align-items-center g-2">
                                                <div class="col-6">
                                                    <span class="fw-semibold">CGST :</span>
                                                </div>
                                                <div class="col-6 text-end">
                                                    <span class="small text-muted me-2">(9%)</span>
                                                    <span class="fw-bold">₹12.35</span>
                                                </div>
                                            </div>

                                            <!-- SGST Row -->
                                            <div class="row align-items-center g-2">
                                                <div class="col-6">
                                                    <span class="fw-semibold">SGST :</span>
                                                </div>
                                                <div class="col-6 text-end">
                                                    <span class="small text-muted me-2">(9%)</span>
                                                    <span class="fw-bold">₹12.35</span>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-semibold">Tax Amount:</span>
                                                <span class="fw-bold">₹24.70</span>
                                            </div>

                                            <hr class="my-1">

                                            <div class="d-flex justify-content-between align-items-center text-primary">
                                                <h5 class="m-0 fw-bold">Total Amount:</h5>
                                                <h5 class="m-0 fw-bold">₹162.00</h5>
                                            </div>
                                        </div>
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

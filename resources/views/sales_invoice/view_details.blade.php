@extends('layouts.common')
@section('title', 'View Sale Invoice - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Sales Invoice</h4>
                <a href="{{ url('sales_invoice') }}" class="btn btn-primary">
                    <i class="ri ri-arrow-left-line back-arrow"></i>Back
                </a>
            </div>

            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Order Details -->
                        <div class="col-lg-12">
                            <h6>Order Details:</h6>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Invoice No:</label>
                            <div class="text-muted">SINV-1001</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Invoice Date:</label>
                            <div class="text-muted">19-Sep-2025</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Customer / Buyer Name:</label>
                            <div class="text-muted">Hero Mens Wear (CUS001)</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Linked SO No:</label>
                            <div class="text-muted">SO-1001</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Delivery Address:</label>
                            <div class="text-muted">12, KG Street, Nagercoil - 98.</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Remarks:</label>
                            <div class="text-muted">Payment is due within 30 days after receipt of the invoice.</div>
                        </div>

                        <div class="col-lg-12">
                            <hr>
                        </div>

                        <!-- Item Details -->
                        <div class="col-lg-12">
                            <h6>Item Details:</h6>
                        </div>
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>S.No</th>
                                            <th>Brand</th>
                                            <th>Item (Code)</th>
                                            <th>UOM</th>
                                            <th>Quantity</th>
                                            <th>Rate</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>UrbanStitch</td>
                                            <td>Men’s Casual Denim Shirt <span class="mini-title">(ITEM001)</span></td>
                                            <td>KG</td>
                                            <td>3</td>
                                            <td>₹50</td>
                                            <td>₹150</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>EliteThreads</td>
                                            <td>Men’s Formal Cotton Shirt <span class="mini-title">(ITEM002)</span></td>
                                            <td>KG</td>
                                            <td>1</td>
                                            <td>₹25</td>
                                            <td>₹25</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <hr>
                        </div>
                        
                        <!-- Show in Customer Invoice PDF Section -->
                        <div class="col-lg-12 mt-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="mb-3 fw-bold">Show in Customer Invoice PDF</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="showAmount" checked disabled>
                                                <label class="form-check-label" for="showAmount">Show Amount</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="showSubTotal" checked disabled>
                                                <label class="form-check-label" for="showSubTotal">Show Sub Total</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="showDiscount" checked disabled>
                                                <label class="form-check-label" for="showDiscount">Show Discount</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="showGrandTotal" checked disabled>
                                                <label class="form-check-label" for="showGrandTotal">Show Grand Total</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="showTax" checked disabled>
                                                <label class="form-check-label" for="showTax">Show Tax (GST/IGST)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="showDue" disabled>
                                                <label class="form-check-label" for="showDue">Show Due Amount</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Summary -->
                        <div class="col-lg-12">
                            <h6>Invoice Summary:</h6>
                        </div>

                        <div class="col-lg-12">
                            <div class="row g-4">
                                <!-- LEFT SIDE -->
                                <div class="col-md-6">
                                    <div class="summary-left border rounded p-3 h-100">
                                        <div class="mb-2">
                                            <label class="detail-title">Sub Total:</label>
                                            <div class="text-muted">₹175.00</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="detail-title">Discount (2%):</label>
                                            <div class="text-muted">₹3.50</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="detail-title">Other State:</label>
                                            <div class="text-muted">No</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="detail-title">CGST (9%):</label>
                                            <div class="text-muted">₹15.75</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="detail-title">SGST (9%):</label>
                                            <div class="text-muted">₹15.75</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="detail-title">Freight Charges:</label>
                                            <div class="text-muted">₹100.00</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="detail-title">Grand Total:</label>
                                            <div class="fw-bold text-success">₹303.00</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- RIGHT SIDE -->
                                <div class="col-md-6">
                                    <div class="summary-right border rounded p-3 h-100">
                                        <div class="mb-2">
                                            <label class="detail-title">Received Amount:</label>
                                            <div class="text-muted">₹200.00</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="detail-title">Due Amount:</label>
                                            <div class="fw-bold text-danger">₹103.00</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="detail-title">Invoice Status:</label>
                                            <div><span class="badge bg-warning">Draft</span></div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="detail-title">Payment Mode:</label>
                                            <div class="text-muted">Online (UPI)</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="detail-title">Cheque / UPI Ref:</label>
                                            <div class="text-muted">TXN12345UPI</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="detail-title">Due Date:</label>
                                            <div class="text-muted">25-Sep-2025</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="detail-title">Additional Notes:</label>
                                            <div class="text-muted">Please include PO number in payment reference.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

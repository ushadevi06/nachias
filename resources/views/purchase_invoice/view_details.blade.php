@extends('layouts.common')
@section('title', 'View Purchase Invoice - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Purchase Invoice</h4>
                <div>
                    <a href="{{ url('purchase_invoices') }}" class="btn btn-secondary me-2">
                        <i class="ri ri-arrow-left-line"></i> Back
                    </a>
                    <a href="{{ url('download_purchase_invoice') }}" class="btn btn-primary">
                        <i class="ri ri-printer-line"></i> Print / Download PDF
                    </a>
                </div>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <h6>Order Details:</h6>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="detail-title">Invoice No: </label>
                            <div class="text-muted">PO-2025-001</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Invoice Date:</label>
                            <div class="text-muted">19-09-2025</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Supplier:</label>
                            <div class="text-muted">Krishna Fabrics(SUP001)</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Consignee Location:</label>
                            <div class="text-muted">Delhi Warehouse</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Dispatch Location:</label>
                            <div class="text-muted">Gurugram Factory</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Service Provider:</label>
                            <div class="text-muted">Udaan Road Ways Pvt Ltd</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Destination:</label>
                            <div class="text-muted">Chennai</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">PO Reference No:</label>
                            <div class="text-muted">PO-2025-001</div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Item Details:</h6>
                        </div>
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>S.No</th>
                                        <th>Item</th>
                                        <th>HSN Code</th>
                                        <th>Quantity</th>
                                        <th>Rate</th>
                                        <th>Amount</th>
                                        <th>Remarks</th>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>Men’s Casual Denim Shirt <span class="mini-title">(ITEM001)</span></td>
                                        <td>D45SD/72-23</td>
                                        <td>3</td>
                                        <td>₹50</td>
                                        <td>₹150</td>
                                        <td>-</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Men’s Formal Cotton Shirt <span class="mini-title">(ITEM002)</span></td>
                                        <td>Q9KD34-23</td>
                                        <td>1</td>
                                        <td>₹25</td>
                                        <td>₹25</td>
                                        <td>-</td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="text-end"><strong>Subtotal</strong></td>
                                        <td colspan="2"><strong>₹175.00</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <h6>Tax & Charges:</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Discount (2%):</label>
                            <div class="text-muted">₹3.50</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Taxable Amount:</label>
                            <div class="text-muted">₹171.50</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">GST (3%):</label>
                            <div class="text-muted">₹5.15</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Freight Charges:</label>
                            <div class="text-muted">₹100.00</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Total Invoice Amount:</label>
                            <div class="fw-bold text-success">₹276.65</div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Bank Details:</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Bank A/C No:</label>
                            <div class="text-muted">1325467890</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">IFSC No:</label>
                            <div class="text-muted">IB009712122</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Branch:</label>
                            <div class="text-muted">Indian Bank</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Invoice Status:</label>
                            <div><span class="badge bg-label-success">Finalized</span></div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Payment Mode:</label>
                            <div class="text-muted">UPI</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Due Date:</label>
                            <div class="text-muted">19-Sep-2025</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Attachments:</label>
                            <div class="text-muted"><img src="{{ url('assets/images/documents.jpg') }}" alt="document" class="detail-img"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Authorized Signature:</label>
                            <div class="text-muted"><img src="{{ url('assets/images/signature_images.jpg') }}" alt="signature" class="detail-img"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
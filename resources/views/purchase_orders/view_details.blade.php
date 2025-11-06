@extends('layouts.common')
@section('title', 'View Purchase Order - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Purchase Order</h4>
                <a href="{{ url('purchase_orders') }}" class="btn btn-primary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <h6>Order Details:</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">PO Number: </label>
                            <div class="text-muted">PO-2025-001</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">PO Date:</label>
                            <div class="text-muted">19-Sep-2025</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Broker / Sales Agent:</label>
                            <div class="text-muted">Neha Sharma(SA102)</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Supplier:</label>
                            <div class="text-muted">Bhagwan Agency</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Reference No:</label>
                            <div class="text-muted">REF-98765</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Delivery Date:</label>
                            <div class="text-muted">15-09-2025</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Delivery Location:</label>
                            <div class="text-muted">Warehouse A, Tiruppur</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Payment Terms:</label>
                            <div class="text-muted">Net 30 Days</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Status:</label>
                            <div><span class="badge bg-success">Approved</span></div>
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
                                        <th>Item(Code)</th>
                                        <th>Quality Code</th>
                                        <th>Quantity</th>
                                        <th>UOM</th>
                                        <th>Rate</th>
                                        <th>Amount</th>
                                        <th>Remarks</th>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>Men’s Casual Denim Shirt <span class="mini-title">(ITEM001)</span></td>
                                        <td>Q972-23</td>
                                        <td>3</td>
                                        <td>MTR</td>
                                        <td>₹50</td>
                                        <td>₹150</td>
                                        <td>-</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Men’s Formal Cotton Shirt <span class="mini-title">(ITEM002)</span></td>
                                        <td>Q934-23</td>
                                        <td>1</td>
                                        <td>MTR</td>
                                        <td>₹25</td>
                                        <td>₹25</td>
                                        <td>-</td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-end"><strong>Subtotal</strong></td>
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
                            <label class="detail-title">Total Amount:</label>
                            <div class="text-muted text-success">₹176.75</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Additional Notes:</label>
                            <div class="text-muted">Goods are received</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Attachments:</label>
                            <div class="text-muted"><img src="{{ url('assets/images/documents.jpg') }}" alt="" class="detail-img"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Authorized Signature:</label>
                            <div class="text-muted"><img src="{{ url('assets/images/signature_images.jpg') }}" alt="" class="detail-img"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
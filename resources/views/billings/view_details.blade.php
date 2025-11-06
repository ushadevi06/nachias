@extends('layouts.common')
@section('title', 'View Billing - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Billing</h4>
                <a href="{{ url('billing') }}" class="btn btn-primary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="detail-title">Bill Number: </label>
                            <div class="text-muted">BILL-1001</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Bill Type:</label>
                            <div class="text-muted">Purchase</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Reference Document:</label>
                            <div class="text-muted">PO-2025-001</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Party:</label>
                            <div class="text-muted">Supplier</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Party Name:</label>
                            <div class="text-muted">Krishna Fabrics(SUP001)</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Bill Date:</label>
                            <div class="text-muted">25-09-2025</div>
                        </div>
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-3">
                                    <tr>
                                        <th>S.No</th>
                                        <th>Item(Code)</th>
                                        <th>Quantity</th>
                                        <th>UOM</th>
                                        <th>Rate (₹)</th>
                                        <th>Discount (%)</th>
                                        <th>GST (%)</th>
                                        <th>Amount</th>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>Men’s Casual Denim Shirt <span class="mini-title">(ITEM001)</span></td>
                                        <td>5</td>
                                        <td>MTR</td>
                                        <td>₹150</td>
                                        <td>5%</td>
                                        <td>18%</td>
                                        <td>₹840.75</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Men’s Formal Cotton Shirt <span class="mini-title">(ITEM002)</span></td>
                                        <td>3</td>
                                        <td>MTR</td>
                                        <td>₹200</td>
                                        <td>5%</td>
                                        <td>18%</td>
                                        <td>₹667.20</td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="text-end"><strong>Subtotal</strong></td>
                                        <td colspan="2"><strong>₹1,507.95</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <h6>Additional Charges</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Freight Charge:</label>
                            <div class="text-muted">₹66</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Packing Charge:</label>
                            <div class="text-muted">₹85</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Total Amount:</label>
                            <div class="text-muted">₹1,658.95</div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Recurring Billing & Status</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Recurring Bill:</label>
                            <div class="text-muted">Yes</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Frequency:</label>
                            <div class="text-muted">Daily</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Status:</label>
                            <div><span class="badge bg-warning">Pending</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.common')
@section('title', 'View Credit Note - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Credit Note</h4>
                <a href="{{ url('credit_notes') }}" class="btn btn-primary">
                    <i class="ri ri-arrow-left-line back-arrow"></i> Back
                </a>
            </div>

            <div class="card detail-card">
                <div class="card-body">
                    <!-- Credit Note Info -->
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="detail-title">Credit Note No:</label>
                            <div class="text-muted">CN-1001</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Date:</label>
                            <div class="text-muted">19-09-2025</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Linked Invoice No:</label>
                            <div class="text-muted">SINV-1001</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Customer / Buyer:</label>
                            <div class="text-muted">Hero Mens Wear (CUS001)</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Reason:</label>
                            <div><span class="badge text-bg-secondary">Rate Correction</span></div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Other State:</label>
                            <div class="text-muted">No</div>
                        </div>

                        <div class="col-lg-12"><hr></div>

                        <!-- Item Details -->
                        <div class="col-lg-12">
                            <h6>Item Details</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>S.No</th>
                                            <th>Item (Code)</th>
                                            <th>Description</th>
                                            <th>Qty</th>
                                            <th>UOM</th>
                                            <th>Rate</th>
                                            <th>Value</th>
                                            <th>Tax</th>
                                            <th>Line Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Men’s Casual Denim Shirt (ITEM001)</td>
                                            <td>Returned due to defect</td>
                                            <td>10</td>
                                            <td>PCS</td>
                                            <td>₹500.00</td>
                                            <td>₹5,000.00</td>
                                            <td>₹900.00</td>
                                            <td>₹5,900.00</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Men’s Formal Cotton Shirt (ITEM002)</td>
                                            <td>Rate adjustment</td>
                                            <td>5</td>
                                            <td>PCS</td>
                                            <td>₹600.00</td>
                                            <td>₹3,000.00</td>
                                            <td>₹540.00</td>
                                            <td>₹3,540.00</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-12"><hr></div>

                        <!-- Totals & Taxes Section -->
                        <div class="col-lg-12">
                            <h6>Tax & Charges</h6>
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <th style="width:50%;">Sub Total:</th>
                                        <td class="text-end text-muted">₹8,000.00</td>
                                    </tr>
                                    <tr>
                                        <th>Discount (5%):</th>
                                        <td class="text-end text-muted">₹400.00</td>
                                    </tr>
                                    <tr>
                                        <th>Total After Discount:</th>
                                        <td class="text-end text-muted">₹7,600.00</td>
                                    </tr>
                                    <tr>
                                        <th>CGST (9%):</th>
                                        <td class="text-end text-muted">₹684.00</td>
                                    </tr>
                                    <tr>
                                        <th>SGST (9%):</th>
                                        <td class="text-end text-muted">₹684.00</td>
                                    </tr>
                                    <!-- If Other State = Yes, replace above with IGST -->
                                    <!--
                                    <tr>
                                        <th>IGST (18%):</th>
                                        <td class="text-end text-muted">₹1,368.00</td>
                                    </tr>
                                    -->
                                    <tr class="fw-bold border-top">
                                        <th>Grand Total:</th>
                                        <td class="text-end text-success">₹8,968.00</td>
                                    </tr>
                                    <tr>
                                        <th>Received Amount:</th>
                                        <td class="text-end text-muted">₹7,000.00</td>
                                    </tr>
                                    <tr>
                                        <th class="text-danger">Due Amount:</th>
                                        <td class="text-end text-danger fw-bold">₹1,968.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div> 
                </div> 
            </div> 
        </div>
    </div>
</div>
@endsection

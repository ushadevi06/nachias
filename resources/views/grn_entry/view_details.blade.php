@extends('layouts.common')
@section('title', 'View GRN Entry - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box d-flex justify-content-between align-items-center">
                <h4>View GRN Entry</h4>
                <a href="{{ url('grn_entries') }}" class="btn btn-primary">
                    <i class="ri ri-arrow-left-line back-arrow"></i> Back
                </a>
            </div>

            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="detail-title">GRN No:</label>
                            <div class="text-muted">GRN001</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">GRN Date:</label>
                            <div class="text-muted">22-09-2025</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">PO Invoice Number:</label>
                            <div class="text-muted">PINV-1001</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Supplier:</label>
                            <div class="text-muted">Krishna Fabrics (SUP001)</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Supplier Invoice No:</label>
                            <div class="text-muted">SUPINV101</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Supplier Invoice Date:</label>
                            <div class="text-muted">22-09-2025</div>
                        </div>

                        <!-- ✅ Table for Item Details -->
                        <div class="col-lg-12 mt-4">
                            <h6 class="fw-bold">Item Details</h6>
                            <div class="table-responsive" style="overflow-x:auto; white-space:nowrap;">
                                <table class="table table-bordered align-middle text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Supplier Design Name (Code)</th>
                                            <th>Art No.</th>
                                            <th>UOM</th>
                                            <th>Fabric Type</th>
                                            <th>Quantity Ordered</th>
                                            <th>Quantity Received</th>
                                            <th>Quantity Accepted</th>
                                            <th>Quantity Rejected</th>
                                            <th>Quantity Balanced</th>
                                            <th>Rate</th>
                                            <th>Amount</th>
                                            <th>Quality Check Status</th>
                                            <th>Store Location</th>
                                            <th>Variants</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Men’s Casual Denim Shirt <span class="mini-title">(ITEM001)</span></td>
                                            <td>ART1001</td>
                                            <td>MTR</td>
                                            <td>Polyester</td>
                                            <td>500</td>
                                            <td>500</td>
                                            <td>480</td>
                                            <td>20</td>
                                            <td>0</td>
                                            <td>₹200</td>
                                            <td>₹96,000</td>
                                            <td><span class="badge bg-label-success">Pass</span></td>
                                            <td>Warehouse 1</td>
                                            <td>
                                                <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#variantModal1">
                                                    View Variants
                                                </button>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>2</td>
                                            <td>Men’s Formal Cotton Shirt <span class="mini-title">(ITEM002)</span></td>
                                            <td>ART1002</td>
                                            <td>MTR</td>
                                            <td>Polycotton</td>
                                            <td>25</td>
                                            <td>25</td>
                                            <td>25</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>₹10</td>
                                            <td>₹250</td>
                                            <td><span class="badge bg-label-success">Pass</span></td>
                                            <td>Warehouse 2</td>
                                            <td>
                                                <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#variantModal2">
                                                    View Variants
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ✅ Tax & Charges Section -->
                        <div class="col-lg-12 mt-4">
                            <h6 class="fw-bold">Tax & Charges</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Sub Total:</label>
                            <div class="text-muted">₹96,250</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Taxes (3%):</label>
                            <div class="text-muted">₹2,887.50</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Discount (2%):</label>
                            <div class="text-muted">₹1,925.00</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Total Amount:</label>
                            <div class="text-success fw-semibold">₹97,212.50</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Variants Modal 1 -->
    <div class="modal fade" id="variantModal1" tabindex="-1" aria-labelledby="variantModalLabel1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="variantModalLabel1">Variants for Men’s Casual Denim Shirt</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Color</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>Blue</td><td>150</td></tr>
                                <tr><td>Black</td><td>200</td></tr>
                                <tr><td>White</td><td>150</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Variants Modal 2 -->
    <div class="modal fade" id="variantModal2" tabindex="-1" aria-labelledby="variantModalLabel2" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="variantModalLabel2">Variants for Men’s Formal Cotton Shirt</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Color</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>White</td><td>10</td></tr>
                                <tr><td>Grey</td><td>15</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

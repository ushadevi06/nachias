@extends('layouts.common')
@section('title', 'View Debit Note - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">

            <div class="card mb-4">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>Debit Note Details</h4>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <p><strong>Debit Note No:</strong> DN-00045</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Debit Note Date:</strong> 31-10-2025</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Reference Invoice No:</strong> INV-0245</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Reference Invoice Date:</strong> 28-10-2025</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Supplier:</strong> Madurai Shirts Pvt Ltd</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Reason:</strong> Goods Return</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>Item Details</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Item Description</th>
                                    <th>HSN Code</th>
                                    <th>Quantity</th>
                                    <th>UOM</th>
                                    <th>Unit Price</th>
                                    <th>Line Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Men’s Casual Denim Shirt (ITEM001)</td>
                                    <td>6205</td>
                                    <td>10</td>
                                    <td>MTR</td>
                                    <td>500.00</td>
                                    <td>5000.00</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Premium Linen Shirt (ITEM005)</td>
                                    <td>6205</td>
                                    <td>5</td>
                                    <td>MTR</td>
                                    <td>800.00</td>
                                    <td>4000.00</td>
                                </tr>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="5" class="text-end">Subtotal</th>
                                    <th>9000.00</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Left: Additional Information -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="card-header-box">
                                <h4>Additional Information</h4>
                            </div>
                            <p><strong>Notes:</strong></p>
                            <p>Returned goods were found defective during inspection. Adjusting against Invoice INV-0245.</p>

                            <p class="mt-3"><strong>Supporting Documents:</strong></p>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-file-earmark-text"></i> View Document
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right: Tax Summary -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="card-header-box">
                                <h4>Tax Summary</h4>
                            </div>

                            <div class="row g-3 align-items-center mb-2">
                                <div class="col-md-6"><label class="fw-bold">Sub total:</label></div>
                                <div class="col-md-6 text-end">9,000.00</div>
                            </div>

                            <div class="row g-3 align-items-center mb-2">
                                <div class="col-md-6"><label class="fw-bold">Other State?</label></div>
                                <div class="col-md-6 text-end">No</div>
                            </div>

                            <div class="row g-3 align-items-center mb-2">
                                <div class="col-md-6"><label class="fw-bold">CGST (9%):</label></div>
                                <div class="col-md-6 text-end">810.00</div>
                            </div>
                            <div class="row g-3 align-items-center mb-2">
                                <div class="col-md-6"><label class="fw-bold">SGST (9%):</label></div>
                                <div class="col-md-6 text-end">810.00</div>
                            </div>

                            <hr>

                            <div class="row g-3 align-items-center">
                                <div class="col-md-6"><label class="fw-bold">Grand Total:</label></div>
                                <div class="col-md-6 text-end fw-bold fs-5">10,620.00</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ url('debit_notes') }}" class="btn btn-secondary">Back</a>
                <button class="btn btn-primary">Print</button>
            </div>

        </div>
    </div>
</div>
@endsection

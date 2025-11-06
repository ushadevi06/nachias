@extends('layouts.common')
@section('title', 'View Job Card Entry - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Job Card Entry</h4>
                <a href="{{ url('job_card_entries') }}" class="btn btn-primary">
                    <i class="ri ri-arrow-left-line back-arrow"></i>Back
                </a>
            </div>

            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">

                        <!-- Job Card Section -->
                        <div class="col-lg-12">
                            <h6>Job Card Details:</h6>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Job Card Number:</label>
                            <div class="text-muted">JC20250924-001-K</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Issue Date:</label>
                            <div class="text-muted">25-09-2025</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Delivery Date:</label>
                            <div class="text-muted">15-10-2025</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Season:</label>
                            <div class="text-muted">Diwali Season</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Brand:</label>
                            <div class="text-muted">Casino Gold (CG)</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Assign Unit:</label>
                            <div class="text-muted">Nachias Fashion Private Limited</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Receipt Store:</label>
                            <div class="text-muted">Finished Goods</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Process Group:</label>
                            <div class="text-muted">CKD-F/S - Checked Full Sleeve</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Status:</label>
                            <div class="text-muted">Urgent</div>
                        </div>

                        <div class="col-lg-12"><hr></div>

                        <!-- Item Details Section -->
                        <div class="col-lg-12">
                            <h6>Item Details:</h6>
                        </div>
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>S.No</th>
                                            <th>Item (Code)</th>
                                            <th>Quantity</th>
                                            <th>UOM</th>
                                            <th>Size</th>
                                            <th>Sleeve</th>
                                            <th>Color</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Men’s Casual Denim Shirt <span class="mini-title">(ITEM001)</span></td>
                                            <td>3</td>
                                            <td>PCS</td>
                                            <td>38,40,42,44 (1,2,3,4)</td>
                                            <td>Full Sleeve</td>
                                            <td>White</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Men’s Casual Denim Shirt <span class="mini-title">(ITEM001)</span></td>
                                            <td>2</td>
                                            <td>PCS</td>
                                            <td>38,40,42,44 (1,2,3,4)</td>
                                            <td>Half Sleeve</td>
                                            <td>Blue</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-12"><hr></div>

                        <!-- Material BOM Section -->
                        <div class="col-lg-12">
                            <h6>Material BOM Details:</h6>
                        </div>
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>S.No</th>
                                            <th>Material</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Cotton Poplin 60 GSM (M001)</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Plastic Button 18L (M002)</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-12"><hr></div>

                        <!-- Production Details Section -->
                        <div class="col-lg-12">
                            <h6>Production Details:</h6>
                        </div>
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>S.No</th>
                                            <th>Production Stages</th>
                                            <th>Planned Start Date</th>
                                            <th>Planned End Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Cutting</td>
                                            <td>29-09-2025</td>
                                            <td>30-09-2025</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Stitching</td>
                                            <td>30-09-2025</td>
                                            <td>01-10-2025</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div> <!-- row end -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

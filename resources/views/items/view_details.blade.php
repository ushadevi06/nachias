@extends('layouts.common')
@section('title', 'View Item - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Item</h4>
                <a href="{{ url('items') }}" class="btn btn-primary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="detail-title">Brand: </label>
                            <div class="text-muted">Hero Mens Wear</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Brand Category:</label>
                            <div class="text-muted">Formal Shirt(IC001) </div>
                        </div> 
                        <div class="col-md-4">
                            <label class="detail-title">Item Name:</label>
                            <div class="text-muted">Men’s Formal Cotton Shirt(ITEM001) </div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Style:</label>
                            <div class="text-muted">Plain</div>
                        </div> 
                        <div class="col-md-4">
                            <label class="detail-title">Fabric Type:</label>
                            <div class="text-muted">Polyester</div>
                        </div> 
                        <div class="col-md-4">
                            <label class="detail-title">Design Art Number:</label>
                            <div class="text-muted">ARTF001</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">UOM:</label>
                            <div class="text-muted">PCS</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Size / Ratio:</label>
                            <div class="text-muted">38, 40, 42(1, 2, 1)</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Color:</label>
                            <div class="text-muted">Blue,Red</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Product Barcode:</label>
                            <div class="text-muted">890100000005</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Standard Costing:</label>
                            <div class="text-muted">₹400</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Status:</label>
                            <div class="text-success">Active</div>
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
                                        <th>Raw Material Category</th>
                                        <th>Material</th>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>Fabric <span class="mini-title">(MC001)</span></td>
                                        <td>Cotton Poplin 60 GSM <span class="mini-title">(M001)</span></td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Fabric <span class="mini-title">(MC001)</span></td>
                                        <td>Cotton Poplin 60 GSM <span class="mini-title">(M001)</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <h6>Operation Stages:</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Cutting:</label>
                            <div class="text-muted">In-House Cutting(SP003)</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Stitching:</label>
                            <div class="text-muted">Vendor A Stitching(SP004)</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Printing:</label>
                            <div class="text-muted">Fast Print Works(SP002)</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Ironing:</label>
                            <div class="text-muted">Vendor B Ironing(SP006)</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Packing:</label>
                            <div class="text-muted">In-House Packing(SP005)</div>
                        </div>
                        <div class="col-lg-12">
                            <h6>Pricing:</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">WholeSale Price:</label>
                            <div>₹450</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Retail Price:</label>
                            <div>₹550</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Export Price:</label>
                            <div>₹600</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
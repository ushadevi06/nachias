@extends('layouts.common')
@section('title', 'View Job Card Item - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row g-4">
        <!-- Left Card -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-header-box mb-3">
                        <h4>Job Card Details</h4>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-12">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="job_card_no" value="JC20250924-001-K" readonly>
                                <label for="job_card_no">Job Card Number *</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="qty_pkg" value="1" readonly>
                                <label for="qty_pkg">Qty/Pkg *</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control jc_date" id="jc_date" value="05-11-2025" readonly>
                                <label for="jc_date">Job Card Date *</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-floating form-floating-outline">
                                <select id="store" name="store" class="form-select select2" disabled>
                                    <option selected>Fabric Store</option>
                                </select>
                                <label for="store">Store *</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Card -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-header-box mb-3">
                        <h4>Item Details</h4>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-12">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="item_name" value="Men’s Casual Denim Shirt(ITEM001)" readonly>
                                <label for="item_name">Item</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="size" value="38,40,42,44(1,2,3,1)" readonly>
                                <label for="size">Size</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="qty" value="7" readonly>
                                <label for="qty">Qty</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="uom" value="PCS" readonly>
                                <label for="uom">UOM</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="qty_to_issue" value="99.00" readonly>
                                <label for="qty_to_issue">Qty To Issue</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="qty_wastage" value="1.17" readonly>
                                <label for="qty_wastage">Qty Wastage</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="qty_used" value="97.83" readonly>
                                <label for="qty_used">Qty Used</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ url('job_card_entries') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </div>
</div>
@endsection

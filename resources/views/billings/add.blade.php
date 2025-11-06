@extends('layouts.common')
@section('title', 'Add Billing - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="" method="POST" class="common-form">
                <div class="card mb-6">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Add Billing</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="bill_no" placeholder="Enter Bill Number">
                                    <label for="bill_no">Bill Number * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" id="billing_type" data-placeholder="Select Billing Type">
                                        <option value="">Select Billing Type</option>
                                        <option value="Purchase">Purchase</option>
                                        <option value="Sales">Sales</option>
                                        <option value="Service">Service</option>
                                    </select>
                                    <label for="billing_type">Billing Type </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline" id="reference_document_div">
                                    <select name="reference_document" id="reference_document" class="form-select select2" data-placeholder="Select Reference Document">
                                        <option value="">Select Reference Document</option>
                                        <option value="PO-2025-001">PO-2025-001</option>
                                        <option value="PO-2025-002">PO-2025-002</option>
                                        <option value="PO-2025-003">PO-2025-003</option>
                                    </select>
                                    <label for="Reference Document">Reference Document *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="party" id="party" class="form-select select2" data-placeholder="Select Party">
                                        <option value="">Select Party</option>
                                        <option value="Supplier">Supplier</option>
                                        <option value="Customer">Customer</option>
                                        <option value="Service Provider">Service Provider</option>
                                    </select>
                                    <label for="party">Party *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4 d-none" id="supplier_div">
                                <div class="form-floating form-floating-outline">
                                    <select name="party_name" id="party_name" class="form-select select2" data-placeholder="Select Supplier">
                                        <option value="">Select Supplier</option>
                                        <option value="Krishna Fabrics(SUP001)">Krishna Fabrics(SUP001)</option>
                                        <option value="Vasanth Garments(SUP002)">Vasanth Garments(SUP002)</option>
                                        <option value="Jaya Fabrics(SUP003)">Jaya Fabrics(SUP003)</option>
                                        <option value="Sri Meena Traders(SUP004)">Sri Meena Traders(SUP004)</option>
                                    </select>
                                    <label for="party_name">Party Name*</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4 d-none" id="customer_div">
                                <div class="form-floating form-floating-outline">
                                    <select name="customer" id="" class="form-select select2" data-placeholder="Select Customer/Buyer">
                                        <option value="">Select Customer/Buyer</option>
                                        <option value="Hero Mens Wear(CUS001)">Hero Mens Wear(CUS001)</option>
                                        <option value="Unlimited Fashion Store(CUS002)">Unlimited Fashion Store(CUS002)</option>
                                        <option value="Nikhil Jain(CUS003)">Nikhil Jain(CUS003)</option>
                                        <option value="Elite Garments Exporters(CUS004)">Elite Garments Exporters(CUS004)</option>
                                    </select>
                                    <label for="party_name">Customer *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4 d-none" id="service_provider_div">
                                <div class="form-floating form-floating-outline">
                                    <select name="customer" id="" class="form-select select2" data-placeholder="Select Service Provider">
                                        <option value="">Select Service Provider</option>
                                        <option value="Fast Print Works(SP002)">Fast Print Works(SP002)</option>
                                        <option value="In-House Cutting(SP003)">In-House Cutting(SP003)</option>
                                        <option value="Vendor A Stitching(SP004)">Vendor A Stitching(SP004)</option>
                                        <option value="In-House Packing(SP005)">In-House Packing(SP005)</option>
                                    </select>
                                    <label for="party_name">Service Provider *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control bill_date" id="bill_date" placeholder="Enter Bill Date" name="bill_date">
                                    <label for="bill_date">Bill Date * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100" id="address" placeholder="Enter Remarks"></textarea>
                                    <label for="address">Remarks </label>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered d-none" id="itemTable">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Item (Code)</th>
                                                <th>Quantity</th>
                                                <th>UOM</th>
                                                <th>Rate (₹)</th>
                                                <th>Discount (%)</th>
                                                <th>GST (%)</th>
                                                <th>Amount (₹)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>Men’s Casual Denim Shirt (ITEM001)</td>
                                                <td><input type="number" class="form-control qty" value="5" min="0"></td>
                                                <td>MTR</td>
                                                <td><input type="number" class="form-control rate" value="150" min="0"></td>
                                                <td><input type="number" class="form-control discount" value="5" min="0"></td>
                                                <td><input type="number" class="form-control gst" value="18" min="0"></td>
                                                <td class="amount">₹840.75</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>Casual Checked Shirt (ITEM002)</td>
                                                <td><input type="number" class="form-control qty" value="3" min="0"></td>
                                                <td>MTR</td>
                                                <td><input type="number" class="form-control rate" value="200" min="0"></td>
                                                <td><input type="number" class="form-control discount" value="5" min="0"></td>
                                                <td><input type="number" class="form-control gst" value="18" min="0"></td>
                                                <td class="amount">₹667.20</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="7" style="text-align:right">Sub Total</th>
                                                <th id="netAmount">₹1,507.95</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h6>Additional Charges</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="freight_charges" placeholder="Enter Freight Charge" name="freight_charges">
                                    <label for="freight_charges">Freight Charge </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="packing_charges" placeholder="Enter Packing Charge" name="packing_charges">
                                    <label for="packing_charges">Packing Charge </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="net_amount" placeholder="Enter Total Amount" name="net_amount">
                                    <label for="net_amount">Total Amount </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="form-select select2" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Paid">Paid</option>
                                        <option value="Partially Paid">Partially Paid</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                    <label for="status">Status *</label>
                                </div>
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('billing') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.bill_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            minDate: 'today',
            allowInput: true
        });
        $('#recuring_billing').change(function() {
            var recuring_bill = $(this).val();
            if (recuring_bill == 'Yes') {
                $('#frequency_div').removeClass('d-none');
            } else {
                $('#frequency_div').addClass('d-none');
            }
        });
        $('#billing_type').change(function() {
            var billing_type = $(this).val();
            if (billing_type == 'Purchase') {
                $('#itemTable').removeClass('d-none');
            } else {
                $('#itemTable').addClass('d-none');
            }
        });
        $('#party').change(function() {
            var party = $(this).val();
            console.log(party);
            if (party == 'Supplier') {
                $('#supplier_div').removeClass('d-none');
                $('#customer_div').addClass('d-none');
                $('#service_provider_div').addClass('d-none');
            } else if (party == 'Customer') {
                $('#customer_div').removeClass('d-none');
                $('#service_provider_div').addClass('d-none');
                $('#supplier_div').addClass('d-none');
            } else if (party == 'Service Provider') {
                $('#service_provider_div').removeClass('d-none');
                $('#supplier_div').addClass('d-none');
                $('#customer_div').addClass('d-none');
            }
        });
    });
</script>
@endsection
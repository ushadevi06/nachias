@extends('layouts.common')
@section('title', 'Add Purchase Invoice - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="" method="POST" class="common-form">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Add Purchase Invoice</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="inv_no" placeholder="Enter Invoice No" name="inv_no">
                                    <label for="inv_no">Invoice No. * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control form-control inv_date" placeholder="Enter Invoice Date" />
                                    <label for="inv_date">Invoice Date * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="purchase_order" id="purchase_order" class="form-select select2" data-placeholder="Select Purchase Order">
                                        <option value="">Select Purchase Order</option>
                                        <option value="PO-2025-001">PO-2025-001</option>
                                        <option value="PO-2025-002">PO-2025-002</option>
                                        <option value="PO-2025-003">PO-2025-003</option>
                                    </select>
                                    <label for="purchase_order">Purchase Order No *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="supplier" id="supplier" class="form-select select2" data-placeholder="Select Supplier">
                                        <option value="">Select Supplier</option>
                                        <option value="Krishna Fabrics(SUP001)">Krishna Fabrics(SUP001)</option>
                                        <option value="Vasanth Garments(SUP002)">Vasanth Garments(SUP002)</option>
                                        <option value="Jaya Fabrics(SUP003)">Jaya Fabrics(SUP003)</option>
                                        <option value="Sri Meena Traders(SUP004)">Sri Meena Traders(SUP004)</option>
                                    </select>
                                    <label for="supplier">Supplier *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="consignee_location" placeholder="Enter Consignee Location" name="consignee_location">
                                    <label for="consignee_location">Consignee Location</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="dispatch_location" placeholder="Enter Dispatch Location" name="dispatch_location">
                                    <label for="dispatch_location">Dispatch Location</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="" class="select2 form-select" data-placeholder="Select Service Provider">
                                        <option value="">Select Service Provider</option>
                                        <option value="Udaan Road Ways Pvt Ltd(SP001)">Udaan Road Ways Pvt Ltd(SP001)</option>
                                        <option value="TVLS Transports Pvt Ltd(SP002)">TVLS Transports Pvt Ltd(SP002)</option>
                                    </select>
                                    <label for="country">Service Provider *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="destination" placeholder="Enter Destination" name="destination">
                                    <label for="destination">Destination * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="po_no" placeholder="Enter PO Reference" name="po_no">
                                    <label for="po_no">PO Reference </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Item Details </h4>
                        </div>
                        <div id="item-rows">
                            <div class="item-block">
                                <div class="row g-4 item-row">
                                    <div class="col-md-3 col-xl-2">
                                        <div class="form-floating form-floating-outline">
                                            <select id="item" class="select2 form-select" data-placeholder="Select Item">
                                                <option value="">Select Item</option>
                                                <option value="Men’s Casual Denim Shirt(ITEM001)">Men’s Casual Denim Shirt(ITEM001)</option>
                                                <option value="Men’s Formal Cotton Shirt(ITEM002)">Men’s Formal Cotton Shirt(ITEM002)</option>
                                                <option value="School Uniform Shirt(ITEM003)">School Uniform Shirt(ITEM003) </option>
                                                <option value="Kids Polo Shirt(ITEM004)">Kids Polo Shirt(ITEM004)</option>
                                                <option value="Premium Linen Shirt(ITEM005)">Premium Linen Shirt(ITEM005)</option>
                                            </select>
                                            <label for="item_code">Item * </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-xl-2">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="hsn_code" placeholder="Enter HSN Code" name="hsn_code">
                                            <label for="hsn_code">HSN Code</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-xl-1">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="quantity" placeholder="Enter Quality" name="quantity">
                                            <label for="quantity">Quantity * </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-xl-2">
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2 form-select uom" data-placeholder="Select UOM">
                                                <option value="">Select UOM</option>
                                                <option value="MTR">MTR</option>
                                                <option value="PCS">PCS</option>
                                                <option value="ROLL">ROLL</option>
                                            </select>
                                            <label>UOM</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-xl-2">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="rate" placeholder="Enter rate per unit" name="rate">
                                            <label for="rate">Rate per Unit * </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-xl-2">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="amount" placeholder="Enter amount" name="amount">
                                            <label for="amount">Amount * </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-xl-1">
                                        <button type="button" class="btn btn-primary add_item"><i class="icon-base ri ri-add-line"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Tax & Charges </h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="charges" class="select2 form-select" data-placeholder="Select Charges">
                                        <option value="">Select Charges</option>
                                        <option value="Freight Charge">Freight Charge</option>
                                        <option value="Insurance Charge">Insurance Charge</option>
                                        <option value="TCS Charge">TCS Charge</option>
                                        <option value="Labour Charges">Labour Charges</option>
                                        <option value="Moulding / Tooling Charges">Moulding / Tooling Charges</option>
                                    </select>
                                    <label for="charges">Charges *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="charge_amt" placeholder="Enter Charge Amount" name="charge_amt">
                                    <label for="charge_amt">Charge Amount *</label>
                                </div>
                            </div>
                            <div class="col-md-3 col-xl-1">
                                <button type="button" class="btn btn-primary add_charges"><i class="icon-base ri ri-add-line"></i></button>
                            </div>
                        </div>
                        <div class="tax-charges-container"></div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="row g-4 align-items-start">
                            <div class="col-lg-6">
                                <div class="card p-3 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="mb-3 fw-semibold">Invoice Details</h5>
                                        <div class="form-floating form-floating-outline mb-3">
                                            <select id="invoice_status" class="select2 form-select" data-placeholder="Select Invoice Status">
                                                <option value="">Select Invoice Status</option>
                                                <option value="Draft">Draft</option>
                                                <option value="Unpaid/Credit">Unpaid/Credit</option>
                                                <option value="Paid">Paid</option>
                                                <option value="Partially Paid">Partially Paid</option>
                                            </select>
                                            <label for="invoice_status">Invoice Status *</label>
                                        </div>

                                        <div class="form-floating form-floating-outline mb-3">
                                            <select id="payment_mode" class="select2 form-select" data-placeholder="Select Payment Mode">
                                                <option value="">Select Payment Mode</option>
                                                <option value="Bank Transfer">Bank Transfer</option>
                                                <option value="Cheque">Cheque</option>
                                                <option value="UPI">UPI</option>
                                            </select>
                                            <label for="payment_mode">Payment Mode</label>
                                        </div>

                                        <div class="form-floating form-floating-outline mb-3">
                                            <input type="text" class="form-control due_date" placeholder="Enter Due Date" />
                                            <label for="due_date">Due Date</label>
                                        </div>

                                        <div class="form-floating form-floating-outline mb-3">
                                            <textarea name="notes" id="notes" class="form-control h-px-100" placeholder="Enter Additional Notes"></textarea>
                                            <label for="notes">Additional Notes</label>
                                        </div>

                                        <div class="form-floating form-floating-outline mb-3">
                                            <input type="file" class="form-control" id="auth_sign" name="auth_sign">
                                            <label for="auth_sign">Authorized Signature / Stamp Upload</label>
                                        </div>

                                        <div class="form-floating form-floating-outline mb-3">
                                            <input type="file" class="form-control" id="attachments" name="attachments">
                                            <label for="attachments">Attachments</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="p-3">
                                    <h5 class="fw-semibold mb-3">Invoice Summary</h5>

                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <span>Sub total:</span><strong id="subtotal">0.00</strong>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                        <span>Discount:</span>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="input-group input-group-sm" style="width:120px;">
                                                <input type="number" id="discount_input" class="form-control text-end" placeholder="0.00" min="0" max="100">
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <strong id="discount_value">0.00</strong>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <span>Total:</span><strong id="total">0.00</strong>
                                    </div>

                                    <!-- ✅ Tax Type Radio -->
                                    <div class="py-3 border-bottom">
                                        <label class="fw-semibold mb-2 d-block">Other State?</label>
                                        <div class="d-flex gap-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="other_state" id="state_yes" value="yes">
                                                <label class="form-check-label" for="state_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="other_state" id="state_no" value="no" checked>
                                                <label class="form-check-label" for="state_no">No</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ✅ IGST Section (hidden initially) -->
                                    <div id="igst_div" class="py-2 border-bottom" style="display:none;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>IGST (18%)</span>
                                            <input type="text" class="form-control form-control-sm text-end" id="igst_amt" placeholder="0.00" style="width:120px;">
                                        </div>
                                    </div>

                                    <!-- ✅ CGST + SGST Section -->
                                    <div id="cgst_sgst_div" class="py-2 border-bottom">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span>CGST (9%)</span>
                                            <input type="text" class="form-control form-control-sm text-end" id="cgst_amt" placeholder="0.00" style="width:120px;">
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>SGST (9%)</span>
                                            <input type="text" class="form-control form-control-sm text-end" id="sgst_amt" placeholder="0.00" style="width:120px;">
                                        </div>
                                    </div>

                                    <!-- Totals -->
                                    <div class="d-flex justify-content-between py-2 border-top fw-semibold">
                                        <span>Grand total:</span><strong id="grand_total">0.00</strong>
                                    </div>

                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <span>Received amount:</span><strong id="received_amount">0.00</strong>
                                    </div>

                                    <div class="d-flex justify-content-between py-2 fw-semibold text-danger">
                                        <span>Due amount:</span><strong id="due_amount">0.00</strong>
                                    </div>

                                    <div class="text-end mt-4">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a href="{{ url('purchase_invoices') }}" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
<script>
    $(document).ready(function() {
        // Add Item functionality
        $('#item-rows').on('click', '.add_item', function() {
            var html = `
            <div class="item-block mb-4 mt-4">
                <div class="row g-4 item-row">
                    <div class="col-md-3 col-xl-2">
                        <div class="form-floating form-floating-outline">
                            <select class="select2 form-select" data-placeholder="Select Item">
                                <option value="">Select Item</option>
                                <option value="Men’s Casual Denim Shirt(ITEM001)">Men’s Casual Denim Shirt(ITEM001)</option>
                                <option value="Men’s Formal Cotton Shirt(ITEM002)">Men’s Formal Cotton Shirt(ITEM002)</option>
                                <option value="School Uniform Shirt(ITEM003)">School Uniform Shirt(ITEM003)</option>
                                <option value="Kids Polo Shirt(ITEM004)">Kids Polo Shirt(ITEM004)</option>
                                <option value="Premium Linen Shirt(ITEM005)">Premium Linen Shirt(ITEM005)</option>
                            </select>
                            <label for="item_code">Item * </label>
                        </div>
                    </div>
                    <div class="col-md-3 col-xl-2">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" placeholder="Enter HSN Code" name="hsn_code">
                            <label>HSN Code</label>
                        </div>
                    </div>
                    <div class="col-md-3 col-xl-1">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" placeholder="Enter Quantity" name="quantity">
                            <label>Quantity * </label>
                        </div>
                    </div>
                    <div class="col-md-2 col-xl-2">
                        <div class="form-floating form-floating-outline">
                            <select class="select2 form-select uom" data-placeholder="Select UOM">
                                <option value="">Select UOM</option>
                                <option value="MTR">MTR</option>
                                <option value="PCS">PCS</option>
                                <option value="ROLL">ROLL</option>
                            </select>
                            <label>UOM</label>
                        </div>
                    </div>
                    <div class="col-md-3 col-xl-2">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" placeholder="Enter rate per unit" name="rate">
                            <label>Rate per Unit * </label>
                        </div>
                    </div>
                    <div class="col-md-3 col-xl-2">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" placeholder="Enter amount" name="amount">
                            <label>Amount * </label>
                        </div>
                    </div> 
                    <div class="col-md-3 col-xl-1">
                        <button type="button" class="btn btn-danger delete_item"><i class="ri ri-delete-bin-line icon-base"></i></button>
                    </div>
                </div>
            </div>`;
            $('#item-rows').append(html);
            
            $('.select2').select2({
                width: '100%',
                dropdownParent: $('body')
            });
        });

        $('#item-rows').on('click', '.delete_item', function() {
            $(this).closest('.item-block').remove();
        });
        
        $('.add_charges').on('click', function() {
            var html = `
                <div class="row g-4 mt-4 single-charge">
                    <div class="col-md-6 col-xl-4">
                        <div class="form-floating form-floating-outline">
                            <select class="select2 form-select" data-placeholder="Select Charges">
                                <option value="">Select Charges</option>
                                <option value="Freight Charge">Freight Charge</option>
                                <option value="Insurance Charge">Insurance Charge</option>
                                <option value="TCS Charge">TCS Charge</option>
                                <option value="Labour Charges">Labour Charges</option>
                                <option value="Moulding / Tooling Charges">Moulding / Tooling Charges</option>
                            </select>
                            <label>Charges *</label>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" placeholder="Enter Charge Amount" name="charge_amt">
                            <label>Charge Amount *</label>
                        </div>
                    </div>
                    <div class="col-md-3 col-xl-1">
                        <button type="button" class="btn btn-danger delete_charge">
                            <i class="ri ri-delete-bin-line icon-base"></i>
                        </button>
                    </div>
                </div>`;
            
            $('.tax-charges-container').append(html);

            $('.select2').select2({
                width: '100%',
                dropdownParent: $('body')
            });
        });

        $('.tax-charges-container').on('click', '.delete_charge', function() {
            $(this).closest('.single-charge').remove();
        });

        function togglePaymentMode() {
            var status = $('#invoice_status').val();
            if (status === 'Draft' || status === 'Unpaid/Credit') {
                $('.payment-mode-div').hide();
            } else if (status === 'Paid' || status === 'Partially Paid') {
                $('.payment-mode-div').show();
            } else {
                $('.payment-mode-div').hide();
            }
        }
        
        togglePaymentMode();
        $('#invoice_status').on('change', function() {
            togglePaymentMode();
        });
        
        $('.select2').select2({
            width: '100%',
            dropdownParent: $('body')
        });

        $('#discount_input').on('input', function () {
            let discountPercent = parseFloat($(this).val()) || 0;
            let subtotal = 1000; // Example value
            let discountAmt = subtotal * (discountPercent / 100);
            let total = subtotal - discountAmt;

            $('#discount_value').text(discountAmt.toFixed(2));
            $('#total').text(total.toFixed(2));
            $('#grand_total').text(total.toFixed(2));
        });

        // Toggle Tax Type
        $('input[name="other_state"]').on('change', function () {
            if ($(this).val() === 'yes') {
                // Show IGST only
                $('#igst_div').show();
                $('#cgst_sgst_div').hide();
            } else {
                // Show CGST and SGST only
                $('#igst_div').hide();
                $('#cgst_sgst_div').show();
            }
        });
    });
</script>
@endsection

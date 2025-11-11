@extends('layouts.common')
@section('title', 'Add Sales Invoice - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="" method="POST" class="common-form">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Add Sales Invoice</h4>
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
                                    <select id="so_inv_no" class="select2 form-select" data-placeholder="Select Sales Order Invoice">
                                        <option value="">Select Sales Order Invoice</option>
                                        <option value="SINV-1001">SINV-1001</option>
                                        <option value="SINV-1001SINV-1002">SINV-1002</option>
                                        <option value="SINV-1003">SINV-1003</option>
                                    </select>
                                    <label for="so_inv_no">Sales Order Invoice</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="" class="select2 form-select" data-placeholder="Select Customer/Buyer">
                                        <option value="">Select Customer/Buyer</option>
                                        <option value="Hero Mens Wear(CUS001)">Hero Mens Wear(CUS001)</option>
                                        <option value="Unlimited Fashion Store(CUS002)">Unlimited Fashion Store(CUS002)</option>
                                        <option value="Nikhil Jain(CUS003)">Nikhil Jain(CUS003)</option>
                                        <option value="Elite Garments Exporters(CUS004)">Elite Garments Exporters(CUS004)</option>
                                    </select>
                                    <label for="country">Customer / Buyer *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100" id="address" placeholder="Enter Delivery Address"></textarea>
                                    <label for="address">Delivery Address</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100" id="remarks" placeholder="Enter Remarks"></textarea>
                                    <label for="remarks">Remarks</label>
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
                            <div class="item-block mb-4">
                                <div class="row g-4 item-row">
                                    <div class="col-md-4 col-lg-2">
                                        <div class="form-floating form-floating-outline">
                                            <select id="brand" class="select2 form-select" data-placeholder="Select ">
                                                <option value="">Select Brand</option>
                                                <option value="UrbanStitch">UrbanStitch</option>
                                                <option value="EliteThreads">EliteThreads</option>
                                                <option value="CraftTailor">CraftTailor</option>
                                                <option value="BlueOak">BlueOak</option>
                                            </select>
                                            <label for="brand">Brand </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-3">
                                        <div class="form-floating form-floating-outline">
                                            <select id="item" class="select2 form-select" data-placeholder="Select Item">
                                                <option value="">Select Item</option>
                                                <option value="Men’s Casual Denim Shirt(ITEM001)">Men’s Casual Denim Shirt(ITEM001)</option>
                                                <option value="Men’s Formal Cotton Shirt(ITEM002)">Men’s Formal Cotton Shirt(ITEM002)</option>
                                                <option value="School Uniform Shirt(ITEM003)">School Uniform Shirt(ITEM003) </option>
                                                <option value="Kids Polo Shirt(ITEM004)">Kids Polo Shirt(ITEM004)</option>
                                                <option value="Premium Linen Shirt(ITEM005)">Premium Linen Shirt(ITEM005)</option>
                                            </select>
                                            <label for="item_code">Item (Code) * </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-1">
                                        <div class="form-floating form-floating-outline">
                                            <select id="uom" class="select2 form-select" data-placeholder="Select UOM">
                                                <option value="">Select UOM</option>
                                                <option value="MTR">MTR</option>
                                                <option value="PCS">PCS</option>
                                                <option value="ROLL">ROLL</option>
                                            </select>
                                            <label for="uom">UOM </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-lg-1">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="quantity" placeholder="Enter Quality" name="quantity">
                                            <label for="quantity">Quantity * </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-2">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="rate" placeholder="Enter rate" name="rate">
                                            <label for="rate">Rate * </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-2">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="amount" placeholder="Enter Amount" name="amount">
                                            <label for="rate">Amount * </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-1">
                                        <button type="button" class="btn btn-primary add_item"><i class="icon-base ri ri-add-line"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-4">
                    <!-- Invoice Details Card -->
                    <div class="col-md-12 col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-header-box">
                                    <h5>Invoice Details</h5>
                                </div>

                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <select name="invoice_status" id="invoice_status" class="form-select select2" data-placeholder="Select Invoice Status">
                                                <option value="">Select Invoice Status</option>
                                                <option value="Draft">Draft</option>
                                                <option value="Finalized">Finalized</option>
                                                <option value="Paid">Paid</option>
                                                <option value="Partially Paid">Partially Paid</option>
                                            </select>
                                            <label for="invoice_status">Invoice Status *</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <select name="payment_mode" id="payment_mode" class="form-select select2">
                                                <option value="">Select Payment Mode</option>
                                                <option value="Cash">Cash</option>
                                                <option value="Bank (Cheque)">Bank (Cheque)</option>
                                                <option value="Online (UPI)">Online (UPI)</option>
                                            </select>
                                            <label for="payment_mode">Payment Mode *</label>
                                        </div>
                                    </div>

                                    <!-- Cheque / UPI field -->
                                    <div class="col-md-12" id="extra_field" style="display:none;">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="extra_input" placeholder="Enter Cheque / UPI No">
                                            <label id="extra_label">Cheque / UPI No</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="date" class="form-control" id="due_date" name="due_date" value="{{ date('Y-m-d') }}">
                                            <label for="due_date">Due Date</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <textarea class="form-control" id="notes" name="notes" placeholder="Additional Notes"></textarea>
                                            <label for="notes">Additional Notes</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <input type="file" class="form-control" id="signature_file" name="signature_file">
                                            <label for="signature_file">Authorized Signature / Stamp Upload</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <input type="file" class="form-control" id="attachment_file" name="attachment_file">
                                            <label for="attachment_file">Attachments</label>
                                        </div>
                                    </div>
                                    <!-- Show Fields in Customer Invoice PDF -->
                                    <div class="border-top pt-5 mt-5">
                                        <h6 class="fw-bold mb-2">Show in Customer Invoice PDF</h6>
                                        <div class="row">
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="show_amount" name="show_fields[]" value="amount" checked>
                                                    <label class="form-check-label" for="show_amount">Show Amount</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="show_discount" name="show_fields[]" value="discount" checked>
                                                    <label class="form-check-label" for="show_discount">Show Discount</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="show_tax" name="show_fields[]" value="tax" checked>
                                                    <label class="form-check-label" for="show_tax">Show Tax (GST/IGST)</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4 mt-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="show_subtotal" name="show_fields[]" value="subtotal" checked>
                                                    <label class="form-check-label" for="show_subtotal">Show Sub Total</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4 mt-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="show_grandtotal" name="show_fields[]" value="grandtotal" checked>
                                                    <label class="form-check-label" for="show_grandtotal">Show Grand Total</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4 mt-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="show_due" name="show_fields[]" value="due">
                                                    <label class="form-check-label" for="show_due">Show Due Amount</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Summary Card -->
                    <div class="col-md-12 col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-header-box">
                                    <h5>Invoice Summary</h5>
                                </div>

                                <div class="summary-box">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Sub Total:</span><span id="sub_total_val">0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Discount:</span><span id="discount_val">0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total:</span><span id="total_val">0.00</span>
                                    </div>

                                    <div class="mb-3 mt-3">
                                        <label class="form-label">Other State?</label><br>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="other_state" id="other_state_yes" value="yes">
                                            <label class="form-check-label" for="other_state_yes">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="other_state" id="other_state_no" value="no" checked>
                                            <label class="form-check-label" for="other_state_no">No</label>
                                        </div>
                                    </div>

                                    <!-- GST/IGST toggle -->
                                    <div id="igst_section" style="display:none;">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>IGST (18%):</span><span id="igst_val">0.00</span>
                                        </div>
                                    </div>
                                    <div id="cgst_sgst_section">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>CGST (9%):</span><span id="cgst_val">0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>SGST (9%):</span><span id="sgst_val">0.00</span>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between mb-2 fw-bold border-top pt-2">
                                        <span>Grand Total:</span><span id="grand_total_val">0.00</span>
                                    </div>

                                    <div class="form-floating form-floating-outline mt-3">
                                        <input type="text" class="form-control" id="received_amount" placeholder="Enter Received Amount">
                                        <label for="received_amount">Received Amount</label>
                                    </div>

                                    <div class="d-flex justify-content-between mt-2 text-danger fw-bold">
                                        <span>Due Amount:</span><span id="due_amount_val">0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Submit Buttons -->
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ url('   ') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#item-rows').on('click', '.add_item', function() {
            var html = `
         <div class="item-block mb-4">
            <div class="row g-4 item-row">
                <div class="col-md-4 col-lg-2">
                    <div class="form-floating form-floating-outline">
                        <select id="" class="select2 form-select" data-placeholder="Select ">
                            <option value="">Select Brand</option>
                            <option value="UrbanStitch">UrbanStitch</option>
                            <option value="EliteThreads">EliteThreads</option>
                            <option value="CraftTailor">CraftTailor</option>
                            <option value="BlueOak">BlueOak</option>
                        </select>
                        <label for="brand">Brand </label>
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <div class="form-floating form-floating-outline">
                        <select class="select2 form-select" data-placeholder="Select Item">
                            <option value="">Select Item</option>
                            <option value="Men’s Casual Denim Shirt(ITEM001)">Men’s Casual Denim Shirt(ITEM001)</option>
                            <option value="Men’s Formal Cotton Shirt(ITEM002)">Men’s Formal Cotton Shirt(ITEM002)</option>
                            <option value="School Uniform Shirt(ITEM003)">School Uniform Shirt(ITEM003)	</option>
                            <option value="Kids Polo Shirt(ITEM004)">Kids Polo Shirt(ITEM004)</option>
                            <option value="Premium Linen Shirt(ITEM005)">Premium Linen Shirt(ITEM005)</option>
                        </select>
                        <label for="item_code">Item * </label>
                    </div>
                </div>
                <div class="col-md-3 col-lg-1">
                    <div class="form-floating form-floating-outline">
                        <select class="select2 form-select" data-placeholder="Select UOM">
                            <option value="">Select UOM</option>
                            <option value="MTR">MTR</option>
                            <option value="PCS">PCS</option>
                            <option value="ROLL">ROLL</option>
                        </select>
                        <label for="uom">UOM </label>
                    </div>
                </div>
                <div class="col-md-3 col-lg-1">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control" id="quantity" placeholder="Enter Quality" name="quantity">
                        <label for="quantity">Quantity * </label>
                    </div>
                </div>
                <div class="col-md-6 col-lg-2">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control" id="rate" placeholder="Enter rate" name="rate">
                        <label for="rate">Rate* </label>
                    </div>
                </div>
                <div class="col-md-6 col-lg-2">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control" id="amount" placeholder="Enter amount" name="amount">
                        <label for="amount">Amount * </label>
                    </div>
                </div> 
                <div class="col-lg-1">
                    <button type="button" class="btn btn-danger delete_item"><i class="ri ri-delete-bin-line icon-base"></i> </button>
                </div>
            </div>
        </div>
        `;
            $('#item-rows').append(html);
            $(".select2").select2();
        });
        $('#item-rows').on('click', '.delete_item', function() {
            $(this).closest('.item-block').remove();
        });
    });
</script>
@endsection
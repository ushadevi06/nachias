@extends('layouts.common')
@section('title', 'Add Sale Order - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="" method="POST" class="common-form">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Add Sale Order</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="so_no" placeholder="Enter SO Number" name="so_no" value="SO-1001">
                                    <label for="so_no">SO Number * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control form-control so_date" placeholder="Enter SO Date" />
                                    <label for="code">SO Date * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="" class="select2 form-select" data-placeholder="Select Customer">
                                        <option value="">Select Customer</option>
                                        <option value="Hero Mens Wear(CUS001)">Hero Mens Wear (CUS001)</option>
                                        <option value="Unlimited Fashion Store(CUS002)">Unlimited Fashion Store (CUS002)</option>
                                        <option value="Nikhil Jain(CUS003)">Nikhil Jain (CUS003)</option>
                                        <option value="Elite Garments Exporters(CUS004)">Elite Garments Exporters (CUS004)</option>
                                    </select>
                                    <label for="country">Customer *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="customer_po_ref" placeholder="Enter Customer PO Ref No" name="customer_po_ref">
                                    <label for="customer_po_ref">Customer PO Ref No</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100" id="payment_terms" placeholder="Enter Payment Terms"></textarea>
                                    <label for="payment_terms">Payment Terms </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="" class="select2 form-select" data-placeholder="Select Broker/Sales Agent">
                                        <option value="">Select Broker/Sales Agent</option>
                                        <option value="Amit Kumar(SA101)">Amit Kumar(SA101)</option>
                                        <option value="Neha Sharma(SA102)">Neha Sharma(SA102)</option>
                                    </select>
                                    <label for="country">Broker/Sales Agent *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control form-control delivery_date" placeholder="Enter Expected Delivery Date" />
                                    <label for="delivery_date">Expected Delivery Date * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="" class="select2 form-select" data-placeholder="Select Zone">
                                        <option value="">Select Zone</option>
                                        <option value="South Zone">South Zone</option>
                                        <option value="North Zone">North Zone</option>
                                        <option value="West Zone">West Zone</option>
                                        <option value="Central Zone">Central Zone</option>
                                    </select>
                                    <label for="zone_id">Zone * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" id="address_line_1" placeholder="Enter Address Line 1">
                                    <label for="address_line_1">Address Line 1 *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" id="address_line_2" placeholder="Enter Address Line 2">
                                    <label for="address_line_1">Address Line 2</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" id="zipcode" placeholder="Enter Zipcode">
                                    <label for="zipcode">Zipcode</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="status" class="select2 form-select" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Pending">Pending</option>
                                        <option value="In Production">In Production</option>
                                        <option value="Dispatched">Dispatched</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                    <label for="status">Status </label>
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
                                <div class="row item-row g-4">
                                    <div class="col-md-3 col-xl-2">
                                        <div class="form-floating form-floating-outline">
                                            <select id="" class="select2 form-select" data-placeholder="Select Brand Category">
                                                <option value="">Select Brand Category</option>
                                                <option value="BlueBay">BlueBay</option>
                                                <option value="Ethnic Edge">Ethnic Edge</option>
                                                <option value="Royal Attire">Royal Attire</option>
                                                <option value="WorkPro">WorkPro</option>
                                            </select>
                                            <label for="country">Brand *</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-2">
                                        <div class="form-floating form-floating-outline">
                                            <select id="item" class="select2 form-select" data-placeholder="Select Item">
                                                <option value="">Select Item</option>
                                                <option value="Men’s Casual Denim Shirt(ITEM001)">Men’s Casual Denim Shirt(ITEM001)</option>
                                                <option value="Men’s Formal Cotton Shirt(ITEM002)">Men’s Formal Cotton Shirt(ITEM002)</option>
                                                <option value="School Uniform Shirt(ITEM003)">School Uniform Shirt(ITEM003) </option>
                                                <option value="Kids Polo Shirt(ITEM004)">Kids Polo Shirt(ITEM004)</option>
                                                <option value="Premium Linen Shirt(ITEM005)">Premium Linen Shirt(ITEM005)</option>
                                            </select>
                                            <label for="item">Item </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-1">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="quantity_order" placeholder="Enter Quantity" name="quantity_order">
                                            <label for="quantity_order">Quantity* </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-1">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="rate" placeholder="Enter rate per unit" name="rate">
                                            <label for="rate">Rate per Unit * </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-1">
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
                                    <div class="col-md-6 col-lg-1">
                                        <div class="form-floating form-floating-outline">
                                            <select id="size" class="select2 form-select" data-placeholder="Select Size">
                                                <option value="">Select Size</option>
                                                <option value="38,40,42,44 (1,2,3,7)">38,40,42,44 (1,2,3,7)</option>
                                                <option value="38,40 (5,2)">38,40 (5,2)</option>
                                                <option value="42,44 (5,7)">42,44 (5,7)</option>
                                                <option value="38,40,42 (1,3,2)">38,40,42 (1,3,2)</option>
                                                <option value="38,40,42 (1,3,1)">38,40,42 (1,3,1)</option>
                                            </select>
                                            <label for="size">Size * </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-2">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="amount" placeholder="Enter amount" name="amount">
                                            <label for="amount">Amount * </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-1">
                                        <div class="border rounded p-2" style="max-height: 180px; overflow-y: auto;">
                                            <label class="fw-semibold d-block mb-2">Sleeve Type</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="sleeve1" name="sleeve_type[]" value="Checked Full Sleeve">
                                                <label class="form-check-label" for="sleeve1">Checked Full Sleeve</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="sleeve2" name="sleeve_type[]" value="Checked Full & Half Sleeve">
                                                <label class="form-check-label" for="sleeve2">Checked Full & Half Sleeve</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="sleeve3" name="sleeve_type[]" value="Checked Half Sleeve">
                                                <label class="form-check-label" for="sleeve3">Checked Half Sleeve</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="sleeve4" name="sleeve_type[]" value="Others Full Sleeve">
                                                <label class="form-check-label" for="sleeve4">Others Full Sleeve</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="sleeve5" name="sleeve_type[]" value="Others Full & Half Sleeve">
                                                <label class="form-check-label" for="sleeve5">Others Full & Half Sleeve</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="sleeve6" name="sleeve_type[]" value="Others Half Sleeve">
                                                <label class="form-check-label" for="sleeve6">Others Half Sleeve</label>
                                            </div>
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
                    <!-- Additional Information -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-header-box">
                                    <h4>Additional Information</h4>
                                </div>

                                <div class="row g-4">
                                    <div class="col-12">
                                        <div class="form-floating form-floating-outline">
                                            <select id="status" name="status" class="select2 form-select">
                                                <option value="">Select Status</option>
                                                <option value="Draft" selected>Draft</option>
                                                <option value="Approved">Approved</option>
                                                <option value="Dispatched">Dispatched</option>
                                                <option value="Received">Received</option>
                                            </select>
                                            <label for="status">Status <span class="text-danger">*</span></label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating form-floating-outline">
                                            <textarea class="form-control h-px-100"
                                                id="payment_terms"
                                                name="payment_terms"
                                                placeholder="Enter Payment Terms"></textarea>
                                            <label for="payment_terms">Payment Terms</label>
                                        </div>
                                    </div>

                                    <div class="col-12 file-container">
                                        <div class="form-floating form-floating-outline text-black">
                                            <input type="file" class="form-control file-input"
                                                id="additional_attachments"
                                                name="additional_attachments">
                                            <label for="additional_attachments">Additional Attachments</label>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Tax Summary -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-header-box">
                                    <h4>Tax Summary</h4>
                                </div>

                                <div class="row g-4">
                                    <div class="col-12">

                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="fw-medium">Total Qty:</label>
                                            <input type="text" class="form-control-plaintext text-end w-50 fw-bold"
                                                id="total_qty" name="total_qty" value="0" readonly>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="fw-medium">Sub Total:</label>
                                            <input type="text" class="form-control-plaintext text-end w-50 fw-bold"
                                                id="sub_total" name="sub_total" value="0.00" readonly>
                                        </div>

                                        <!-- Discount -->
                                        <div class="mb-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label class="fw-medium">Discount:</label>
                                                <div class="input-group input-group-sm" style="width:120px;">
                                                    <input type="number" class="form-control form-control-sm text-end"
                                                        id="discount_percent" name="discount_percent"
                                                        step="0.01" min="0" max="100" value="0">
                                                    <span class="input-group-text px-1">%</span>
                                                </div>
                                            </div>
                                            <div class="text-end mt-1">
                                                <input type="text"
                                                    class="form-control-plaintext form-control-sm text-end py-0"
                                                    id="discount_amount" name="discount_amount"
                                                    value="0.00" readonly>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center border-top pt-2">
                                            <label class="fw-medium">Net Amount (Before Tax):</label>
                                            <input type="text"
                                                class="form-control-plaintext text-end w-50 fw-bold"
                                                id="taxable_amount" name="taxable_amount"
                                                value="0.00" readonly>
                                        </div>

                                        <!-- Other State -->
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="fw-medium">Other State:</label>
                                            <div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                        name="other_state" value="no" checked>
                                                    <label class="form-check-label">No</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio"
                                                        name="other_state" value="yes">
                                                    <label class="form-check-label">Yes</label>
                                                </div>
                                            </div>
                                        </div>

                                        <input type="hidden" name="round_off_type" value="Add">

                                        <!-- IGST -->
                                        <div class="igst-field d-none">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label class="fw-medium">IGST :</label>
                                                <div class="input-group input-group-sm" style="width:120px;">
                                                    <input type="number"
                                                        class="form-control form-control-sm text-end"
                                                        id="igst_percent" name="igst_percent"
                                                        value="0">
                                                    <span class="input-group-text px-1">%</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- CGST -->
                                        <div class="cgst-field">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label class="fw-medium">CGST :</label>
                                                <div class="input-group input-group-sm" style="width:120px;">
                                                    <input type="number"
                                                        class="form-control form-control-sm text-end"
                                                        id="cgst_percent" name="cgst_percent"
                                                        value="9">
                                                    <span class="input-group-text px-1">%</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- SGST -->
                                        <div class="sgst-field mt-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label class="fw-medium">SGST :</label>
                                                <div class="input-group input-group-sm" style="width:120px;">
                                                    <input type="number"
                                                        class="form-control form-control-sm text-end"
                                                        id="sgst_percent" name="sgst_percent"
                                                        value="9">
                                                    <span class="input-group-text px-1">%</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="fw-medium">Tax Amount:</label>
                                            <input type="text"
                                                class="form-control-plaintext text-end w-50"
                                                id="tax_amount" name="tax_amount"
                                                value="0.00" readonly>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                                            <label class="fw-bold fs-5">Total Amount:</label>
                                            <input type="text"
                                                class="form-control-plaintext text-end w-50 fw-bold fs-5 text-primary"
                                                id="total_amount" name="total_amount"
                                                value="0.00" readonly>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


                <!-- Submit Buttons -->
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ url('sales_order') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
$(document).ready(function () {
  $(".select2").select2();

  // Add New Item Row (full structure)
  $('#item-rows').on('click', '.add_item', function () {
    let html = `
    <div class="item-block mb-4">
      <div class="row item-row g-4">
        <div class="col-md-3 col-xl-2">
          <div class="form-floating form-floating-outline">
            <select class="select2 form-select" data-placeholder="Select Brand">
              <option value="">Select Brand</option>
              <option value="BlueBay">BlueBay</option>
              <option value="Ethnic Edge">Ethnic Edge</option>
              <option value="Royal Attire">Royal Attire</option>
              <option value="WorkPro">WorkPro</option>
            </select>
            <label>Brand *</label>
          </div>
        </div>

        <div class="col-md-6 col-lg-2">
          <div class="form-floating form-floating-outline">
            <select class="select2 form-select" data-placeholder="Select Item">
              <option value="">Select Item</option>
              <option value="Men’s Casual Denim Shirt(ITEM001)">Men’s Casual Denim Shirt(ITEM001)</option>
              <option value="Men’s Formal Cotton Shirt(ITEM002)">Men’s Formal Cotton Shirt(ITEM002)</option>
              <option value="School Uniform Shirt(ITEM003)">School Uniform Shirt(ITEM003)</option>
              <option value="Kids Polo Shirt(ITEM004)">Kids Polo Shirt(ITEM004)</option>
              <option value="Premium Linen Shirt(ITEM005)">Premium Linen Shirt(ITEM005)</option>
            </select>
            <label>Item *</label>
          </div>
        </div>

        <div class="col-md-6 col-lg-1">
          <div class="form-floating form-floating-outline">
            <input type="text" class="form-control quantity_order" placeholder="Enter Quantity">
            <label>Quantity *</label>
          </div>
        </div>

        <div class="col-md-6 col-lg-1">
          <div class="form-floating form-floating-outline">
            <input type="text" class="form-control rate" placeholder="Enter rate per unit">
            <label>Rate per Unit *</label>
          </div>
        </div>

        <div class="col-md-6 col-lg-1">
          <div class="form-floating form-floating-outline">
            <select class="select2 form-select" data-placeholder="Select UOM">
              <option value="">Select UOM</option>
              <option value="MTR">MTR</option>
              <option value="PCS">PCS</option>
              <option value="ROLL">ROLL</option>
            </select>
            <label>UOM</label>
          </div>
        </div>

        <div class="col-md-6 col-lg-1">
          <div class="form-floating form-floating-outline">
            <select class="select2 form-select" data-placeholder="Select Size">
              <option value="">Select Size</option>
              <option value="38,40,42,44 (1,2,3,7)">38,40,42,44 (1,2,3,7)</option>
              <option value="38,40 (5,2)">38,40 (5,2)</option>
              <option value="42,44 (5,7)">42,44 (5,7)</option>
              <option value="38,40,42 (1,3,2)">38,40,42 (1,3,2)</option>
              <option value="38,40,42 (1,3,1)">38,40,42 (1,3,1)</option>
            </select>
            <label>Size *</label>
          </div>
        </div>

        <div class="col-md-6 col-lg-2">
          <div class="form-floating form-floating-outline">
            <input type="text" class="form-control amount" placeholder="Enter amount">
            <label>Amount *</label>
          </div>
        </div>

        <div class="col-md-6 col-lg-1">
          <label class="fw-semibold d-block mb-2">Sleeve Type</label>
          <div class="border rounded p-2" style="max-height: 180px; overflow-y: auto;">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="sleeve_type[]" value="Checked Full Sleeve">
              <label class="form-check-label">Checked Full Sleeve</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="sleeve_type[]" value="Checked Full & Half Sleeve">
              <label class="form-check-label">Checked Full & Half Sleeve</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="sleeve_type[]" value="Checked Half Sleeve">
              <label class="form-check-label">Checked Half Sleeve</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="sleeve_type[]" value="Others Full Sleeve">
              <label class="form-check-label">Others Full Sleeve</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="sleeve_type[]" value="Others Full & Half Sleeve">
              <label class="form-check-label">Others Full & Half Sleeve</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="sleeve_type[]" value="Others Half Sleeve">
              <label class="form-check-label">Others Half Sleeve</label>
            </div>
          </div>
        </div>

        <div class="col-lg-1">
          <button type="button" class="btn btn-danger delete_item"><i class="ri ri-delete-bin-line"></i></button>
        </div>
      </div>
    </div>`;

    $('#item-rows').append(html);
    $(".select2").select2();
  });

  // Delete item row
  $('#item-rows').on('click', '.delete_item', function () {
    $(this).closest('.item-block').remove();
    calculateTotals();
  });

  // Auto calculate amount
  $('#item-rows').on('input', '.quantity_order, .rate', function () {
    let row = $(this).closest('.item-block');
    let qty = parseFloat(row.find('.quantity_order').val()) || 0;
    let rate = parseFloat(row.find('.rate').val()) || 0;
    row.find('.amount').val((qty * rate).toFixed(2));
    calculateTotals();
  });

  // Totals
  $('#discount_percent, #received_amount, input[name="other_state"]').on('input change', function () {
    calculateTotals();
  });

  function calculateTotals() {
    let subTotal = 0;
    $('.amount').each(function () {
      subTotal += parseFloat($(this).val()) || 0;
    });

    $('#sub_total').val(subTotal.toFixed(2));

    let discountPercent = parseFloat($('#discount_percent').val()) || 0;
    let discountAmount = (subTotal * discountPercent) / 100;
    $('#discount_amount').val(discountAmount.toFixed(2));

    let totalAfterDiscount = subTotal - discountAmount;
    $('#total_after_discount').val(totalAfterDiscount.toFixed(2));

    let isOtherState = $('input[name="other_state"]:checked').val() === 'yes';
    let cgst = 0, sgst = 0, igst = 0;

    if (isOtherState) {
      $('#cgst_sgst_section, #sgst_section').addClass('d-none');
      $('#igst_section').removeClass('d-none');
      igst = totalAfterDiscount * 0.18;
      $('#igst').val(igst.toFixed(2));
    } else {
      $('#cgst_sgst_section, #sgst_section').removeClass('d-none');
      $('#igst_section').addClass('d-none');
      cgst = totalAfterDiscount * 0.09;
      sgst = totalAfterDiscount * 0.09;
      $('#cgst').val(cgst.toFixed(2));
      $('#sgst').val(sgst.toFixed(2));
    }

    let grandTotal = totalAfterDiscount + cgst + sgst + igst;
    $('#grand_total').val(grandTotal.toFixed(2));

    let received = parseFloat($('#received_amount').val()) || 0;
    let due = grandTotal - received;
    $('#due_amount').val(due.toFixed(2));
  }
});
</script>
@endsection
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
                                            <select id="" class="select2 form-select" data-placeholder="Select Brand">
                                                <option value="">Select Brand</option>
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
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="total_quantity" placeholder="Enter Total Quantity" readonly value="">
                                    <label for="total_quantity">Total Quantity</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="sub_total" placeholder="Enter Sub Total" readonly value="0.00">
                                    <label for="sub_total">Sub Total</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-floating form-floating-outline">Discount:</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="discount_percent" placeholder="0.00">
                                    <span class="input-group-text">%</span>
                                <input type="text" class="form-control text-end" id="discount_amount" readonly value="0.00">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control text-end" id="total_after_discount" readonly value="0.00">
                                    <label for="total_after_discount" class="fw-bold">Total After Discount</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100" id="additional_notes" placeholder="Enter Additional Notes"></textarea>
                                    <label for="additional_notes">Additional Notes</label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Other State?</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="other_state" id="other_state_yes" value="yes">
                                    <label class="form-check-label" for="other_state_yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="other_state" id="other_state_no" value="no" checked>
                                    <label class="form-check-label" for="other_state_no">No</label>
                                </div>
                            </div>

                            <div class="col-md-6" id="cgst_sgst_section">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control text-end" id="cgst" readonly value="0.00">
                                    <label for="cgst" class="fw-bold">CGST (9%)</label>
                                </div>
                            </div>

                            <div class="col-md-6" id="sgst_section">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control text-end" id="sgst" readonly value="0.00">
                                    <label for="sgst" class="fw-bold">SGST (9%)</label>
                                </div>
                            </div>

                            <div class="col-md-6 d-none" id="igst_section">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control text-end" id="igst" readonly value="0.00">
                                    <label for="igst" class="fw-bold">IGST (18%)</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control text-end fw-bold" id="grand_total" readonly value="0.00">
                                    <label for="grand_total" class="fw-bold">Grand Total</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control text-end" id="received_amount" placeholder="0.00">
                                    <label for="received_amount" class="fw-bold">Received Amount</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control text-end text-danger fw-bold" id="due_amount" readonly value="0.00">
                                    <label for="due_amount" class="fw-bold text-danger">Due Amount</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Submit Buttons -->
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ url('sales_invoice') }}" class="btn btn-secondary">Cancel</a>
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
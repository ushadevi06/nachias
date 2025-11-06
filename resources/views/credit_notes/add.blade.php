@extends('layouts.common')
@section('title', 'Add Credit Note - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="" method="POST" class="common-form">
                <!-- Credit Note Details -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Add Credit Note</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="note_no" placeholder="Enter Credit Note No." name="note_no" value="CN-1001">
                                    <label for="note_no">Credit Note No. *</label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="date" class="form-control note_date" id="note_date" name="note_date">
                                    <label for="note_date">Date *</label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" data-placeholder="Select Invoice No">
                                        <option value="">Select Invoice No</option>
                                        <option value="PO-2025-001">PO-2025-001</option>
                                        <option value="PO-2025-002">PO-2025-002</option>
                                        <option value="PO-2025-003">PO-2025-003</option>
                                    </select>
                                    <label for="invoice_no">Invoice No</label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" data-placeholder="Select Customer/Buyer">
                                        <option value="">Select Customer/Buyer</option>
                                        <option value="Hero Mens Wear(CUS001)">Hero Mens Wear(CUS001)</option>
                                        <option value="Unlimited Fashion Store(CUS002)">Unlimited Fashion Store(CUS002)</option>
                                        <option value="Nikhil Jain(CUS003)">Nikhil Jain(CUS003)</option>
                                        <option value="Elite Garments Exporters(CUS004)">Elite Garments Exporters(CUS004)</option>
                                    </select>
                                    <label for="customer">Customer / Buyer *</label>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" data-placeholder="Select Reason">
                                        <option value="">Select Reason</option>
                                        <option value="Return">Return</option>
                                        <option value="Excess Billing">Excess Billing</option>
                                        <option value="Short Supply">Short Supply</option>
                                        <option value="Rate Correction">Rate Correction</option>
                                    </select>
                                    <label for="reason">Reason *</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Item Details Section -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h5>Item Details</h5>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 30%;">Item</th>
                                        <th style="width: 10%;">Qty</th>
                                        <th style="width: 10%;">UOM</th>
                                        <th style="width: 15%;">Rate</th>
                                        <th style="width: 15%;">Line Total</th>
                                        <th style="width: 5%;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="item-rows">
                                    <tr class="item-row">
                                        <td>
                                            <select class="select2 form-select" data-placeholder="Select Item">
                                                <option value="">Select Item</option>
                                                <option value="Men’s Casual Denim Shirt(ITEM001)">Men’s Casual Denim Shirt(ITEM001)</option>
                                                <option value="Men’s Formal Cotton Shirt(ITEM002)">Men’s Formal Cotton Shirt(ITEM002)</option>
                                                <option value="School Uniform Shirt(ITEM003)">School Uniform Shirt(ITEM003)</option>
                                                <option value="Kids Polo Shirt(ITEM004)">Kids Polo Shirt(ITEM004)</option>
                                                <option value="Premium Linen Shirt(ITEM005)">Premium Linen Shirt(ITEM005)</option>
                                            </select>
                                        </td>
                                        <td><input type="number" class="form-control qty" placeholder="Qty" min="0"></td>
                                        <td>
                                            <select class="select2 form-select" data-placeholder="Select UOM">
                                                <option value="">Select UOM</option>
                                                <option value="PCS">PCS</option>
                                                <option value="MTR">MTR</option>
                                                <option value="ROLL">ROLL</option>
                                            </select>
                                        </td>
                                        <td><input type="number" class="form-control rate" placeholder="Rate" min="0" step="0.01"></td>
                                        <td><input type="text" class="form-control line_total" placeholder="Line Total" readonly></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-primary btn-sm add_item"><i class="ri ri-add-line"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Totals Section -->
                <div class="card">
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <th style="width: 50%;">Sub total:</th>
                                            <td class="text-end">
                                                <span id="sub_total_text">0.00</span>
                                                <input type="hidden" id="sub_total" name="sub_total" value="0.00">
                                            </td>
                                        </tr>

                                        <tr>
                                        <th>Discount:</th>
                                            <td class="text-end d-flex justify-content-end align-items-center gap-2">
                                                <input type="number" id="discount" class="form-control w-auto text-end" style="width: 80px;" placeholder="0.00">
                                                <span>%</span>
                                                <span id="discount_amount">0.00</span>
                                            </td>
                                        </tr>

                                        <tr>
                                        <th>Total:</th>
                                            <td class="text-end"><span id="total">0.00</span></td>
                                        </tr>

                                        <tr>
                                        <th>Other State?</th>
                                            <td class="text-end">
                                                <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="is_other_state" id="state_yes" value="yes">
                                                <label class="form-check-label" for="state_yes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="is_other_state" id="state_no" value="no" checked>
                                                <label class="form-check-label" for="state_no">No</label>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr id="cgst_row">
                                            <th>CGST (9%):</th>
                                            <td class="text-end"><span id="cgst_amt">0.00</span></td>
                                        </tr>

                                        <tr id="sgst_row">
                                            <th>SGST (9%):</th>
                                            <td class="text-end"><span id="sgst_amt">0.00</span></td>
                                        </tr>

                                        <tr id="igst_row" class="d-none">
                                            <th>IGST (18%):</th>
                                            <td class="text-end"><span id="igst_amt">0.00</span></td>
                                        </tr>

                                        <tr class="fw-bold border-top">
                                            <th>Grand total:</th>
                                            <td class="text-end"><span id="grand_total">0.00</span></td>
                                        </tr>

                                        <tr>
                                            <th>Received amount:</th>
                                            <td class="text-end">
                                                <input type="number" id="received_amt" class="form-control text-end w-auto d-inline-block" style="width: 120px;" placeholder="0.00">
                                            </td>
                                        </tr>

                                        <tr>
                                            <th class="text-danger">Due amount:</th>
                                            <td class="text-end text-danger fw-bold"><span id="due_amt">0.00</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-lg-12 text-end mt-3">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('credit_notes') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Script -->
<script>
$(document).ready(function() {
    // Initialize Select2 and Date Picker
    $(".select2").select2();
    $(".note_date").flatpickr({
        dateFormat: "d-m-Y",
        allowInput: true,
        defaultDate: "today"
    });

    // Add new item row
    $(".add_item").on("click", function () {
        let row = `
        <tr class="item-row">
            <td>
                <select class="select2 form-select" data-placeholder="Select Item">
                    <option value="">Select Item</option>
                    <option>Men’s Casual Denim Shirt(ITEM001)</option>
                    <option>Men’s Formal Cotton Shirt(ITEM002)</option>
                    <option>School Uniform Shirt(ITEM003)</option>
                    <option>Kids Polo Shirt(ITEM004)</option>
                    <option>Premium Linen Shirt(ITEM005)</option>
                </select>
            </td>
            <td><input type="number" class="form-control qty" placeholder="Qty" min="0"></td>
            <td>
                <select class="select2 form-select" data-placeholder="Select UOM">
                    <option value="">Select UOM</option>
                    <option>PCS</option>
                    <option>MTR</option>
                    <option>ROLL</option>
                </select>
            </td>
            <td><input type="number" class="form-control rate" placeholder="Rate" step="0.01"></td>
            <td><input type="text" class="form-control line_total" placeholder="0.00" readonly></td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm delete_item"><i class="ri ri-delete-bin-line"></i></button>
            </td>
        </tr>`;
        $("#item-rows").append(row);
        $(".select2").select2();
    });

    // Delete item row
    $('#item-rows').on('click', '.delete_item', function() {
        $(this).closest('.item-row').remove();
        calculateTotal();
    });

    // Calculate line total when qty or rate changes
    $(document).on('input', '.qty, .rate', function() {
        let row = $(this).closest('.item-row');
        let qty = parseFloat(row.find('.qty').val()) || 0;
        let rate = parseFloat(row.find('.rate').val()) || 0;
        let lineTotal = qty * rate;
        row.find('.line_total').val(lineTotal.toFixed(2));
        calculateTotal();
    });

    // Recalculate totals on discount or received change
    $(document).on('input', '#discount, #received_amt', calculateTotal);

    // Show/Hide IGST vs CGST-SGST when user clicks Yes/No
    $('input[name="is_other_state"]').on('change', function() {
        if ($('#state_yes').is(':checked')) {
            $('#igst_row').removeClass('d-none');
            $('#cgst_row, #sgst_row').hide();
        } else {
            $('#igst_row').addClass('d-none');
            $('#cgst_row, #sgst_row').show();
        }
        calculateTotal();
    });

    // Total calculation
    function calculateTotal() {
        let subTotal = 0;
        $('.line_total').each(function() {
            subTotal += parseFloat($(this).val()) || 0;
        });

        // Discount
        let discountPercent = parseFloat($('#discount').val()) || 0;
        let discountAmount = (subTotal * discountPercent) / 100;
        let totalAfterDiscount = subTotal - discountAmount;

        // Taxes
        let cgst = 0, sgst = 0, igst = 0;
        if ($('#state_yes').is(':checked')) {
            igst = totalAfterDiscount * 0.18;
        } else {
            cgst = totalAfterDiscount * 0.09;
            sgst = totalAfterDiscount * 0.09;
        }

        // Grand total
        let grandTotal = totalAfterDiscount + cgst + sgst + igst;

        // Received & due
        let received = parseFloat($('#received_amt').val()) || 0;
        let due = grandTotal - received;

        // Update UI
        $('#sub_total_text').text(subTotal.toFixed(2));
        $('#discount_amount').text(discountAmount.toFixed(2));
        $('#total').text(totalAfterDiscount.toFixed(2));
        $('#cgst_amt').text(cgst.toFixed(2));
        $('#sgst_amt').text(sgst.toFixed(2));
        $('#igst_amt').text(igst.toFixed(2));
        $('#grand_total').text(grandTotal.toFixed(2));
        $('#due_amt').text(due.toFixed(2));
        $('#sub_total').val(subTotal.toFixed(2));
    }
});
</script>

@endsection

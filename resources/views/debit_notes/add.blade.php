@extends('layouts.common')
@section('title', 'Add Debit Note - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="" method="POST" class="common-form">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Add Debit Note</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="debit_note_no" placeholder="Debit Note No">
                                    <label for="debit_note_no">Debit Note No *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control debit_note_date" placeholder="Debit Note Date">
                                    <label for="debit_note_date">Debit Note Date *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select select2" id="reference_invoice_no" data-placeholder="Select Reference Invoice No">
                                        <option value="">Select Reference Invoice No</option>
                                        <option value="PINV-1001">PINV-1001</option>
                                        <option value="PINV-1002">PINV-1002</option>
                                    </select>
                                    <label for="reference_invoice_no">Reference Invoice No *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control ref_invoice_date" placeholder="Reference Invoice Date">
                                    <label for="ref_invoice_date">Reference Invoice Date *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select select2" id="supplier" data-placeholder="Select Supplier">
                                        <option value="">Select Supplier</option>
                                        <option value="Krishna Fabrics (SUP001)">Krishna Fabrics (SUP001)</option>
                                        <option value="Vasanth Garments (SUP002)">Vasanth Garments (SUP002)</option>
                                        <option value="Jaya Fabrics (SUP003)">Jaya Fabrics (SUP003)</option>
                                    </select>
                                    <label for="supplier">Supplier *</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select select2" id="debit_reason" data-placeholder="Select Reason">
                                        <option value="">Select Reason</option>
                                        <option value="return">Goods Return</option>
                                        <option value="damage">Damaged Goods</option>
                                        <option value="price_discrepancy">Price Discrepancy</option>
                                        <option value="quantity_discrepancy">Quantity Discrepancy</option>
                                        <option value="tax_correction">Tax Correction</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <label for="debit_reason">Reason for Debit Note *</label>
                                </div>
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
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Item Description</th>
                                        <th>HSN Code</th>
                                        <th>Quantity</th>
                                        <th>UOM</th>
                                        <th>Unit Price</th>
                                        <th>Line Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="debit_items">
                                    <tr>
                                        <td>
                                            <select id="item" class="select2 form-select" data-placeholder="Select Item">
                                                <option value="">Select Item</option>
                                                <option value="Men’s Casual Denim Shirt(ITEM001)">Men’s Casual Denim Shirt(ITEM001)</option>
                                                <option value="Men’s Formal Cotton Shirt(ITEM002)">Men’s Formal Cotton Shirt(ITEM002)</option>
                                                <option value="School Uniform Shirt(ITEM003)">School Uniform Shirt(ITEM003) </option>
                                                <option value="Kids Polo Shirt(ITEM004)">Kids Polo Shirt(ITEM004)</option>
                                                <option value="Premium Linen Shirt(ITEM005)">Premium Linen Shirt(ITEM005)</option>
                                            </select>
                                        </td>
                                        <td><input type="text" class="form-control hsn_code" placeholder="Enter HSN Code" readonly></td>
                                        <td><input type="number" class="form-control quantity" placeholder="Enter Unit Price" step="0.01"></td>
                                        <td>
                                            <select class="select2 form-select uom" data-placeholder="Select UOM">
                                                <option value="">Select UOM</option>
                                                <option value="PCS">PCS</option>
                                                <option value="MTR">MTR</option>
                                                <option value="ROLL">ROLL</option>
                                                <option value="KG">KG</option>
                                                <option value="SET">SET</option>
                                            </select>
                                        </td>
                                        <td><input type="number" class="form-control unit_price" placeholder="Enter Unit Price" step="0.01"></td>
                                        <td><input type="number" class="form-control total_amount" placeholder="Enter Line Total" step="0.01" readonly></td>
                                        <td><button type="button" class="btn btn-primary add_item">+</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Left Side: Additional Information -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="card-header-box">
                                    <h4>Additional Information</h4>
                                </div>
                                <div class="row g-4">
                                    <div class="col-12">
                                        <div class="form-floating form-floating-outline">
                                            <textarea class="form-control" id="additional_notes" placeholder="Additional Notes" style="height: 120px;"></textarea>
                                            <label for="additional_notes">Additional Notes</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating form-floating-outline">
                                            <input type="file" class="form-control" id="supporting_docs">
                                            <label for="supporting_docs">Supporting Documents</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="card-header-box">
                                    <h4>Tax Summary</h4>
                                </div>

                                <div class="row g-3 align-items-center mb-3">
                                    <div class="col-md-6"><label class="fw-bold">Sub total:</label></div>
                                    <div class="col-md-6 text-end"><span id="subtotal_display">0.00</span></div>
                                </div>

                                <div class="row g-3 align-items-center mb-3">
                                    <div class="col-md-6"><label class="fw-bold">Total:</label></div>
                                    <div class="col-md-6 text-end"><span id="total_display">0.00</span></div>
                                </div>

                                <div class="row g-3 align-items-center mb-3">
                                    <div class="col-md-6"><label class="fw-bold">Other State?</label></div>
                                    <div class="col-md-6 text-end">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="other_state" id="other_state_yes" value="yes">
                                            <label class="form-check-label" for="other_state_yes">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="other_state" id="other_state_no" value="no" checked>
                                            <label class="form-check-label" for="other_state_no">No</label>
                                        </div>
                                    </div>
                                </div>

                                <div id="igst_section" class="row g-3 align-items-center mb-3" style="display: none;">
                                    <div class="col-md-6"><label class="fw-bold">IGST (18%):</label></div>
                                    <div class="col-md-6 text-end">
                                        <input type="text" id="igst_amount" class="form-control form-control-sm text-end d-inline-block" style="width:120px;" readonly value="0.00">
                                    </div>
                                </div>

                                <div id="cgst_sgst_section">
                                    <div class="row g-3 align-items-center mb-3">
                                        <div class="col-md-6"><label class="fw-bold">CGST (9%):</label></div>
                                        <div class="col-md-6 text-end">
                                            <input type="text" id="cgst_amount" class="form-control form-control-sm text-end d-inline-block" style="width:120px;" readonly value="0.00">
                                        </div>
                                    </div>
                                    <div class="row g-3 align-items-center mb-3">
                                        <div class="col-md-6"><label class="fw-bold">SGST (9%):</label></div>
                                        <div class="col-md-6 text-end">
                                            <input type="text" id="sgst_amount" class="form-control form-control-sm text-end d-inline-block" style="width:120px;" readonly value="0.00">
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="row g-3 align-items-center">
                                    <div class="col-md-6"><label class="fw-bold">Grand Total:</label></div>
                                    <div class="col-md-6 text-end">
                                        <span id="grand_total_display" class="fw-bold fs-5">0.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end mb-5 me-4">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <a href="{{ url('debit_notes') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
$(document).ready(function () {

    toggleTaxSections();
    $('input[name="other_state"]').change(function() {
        toggleTaxSections();
    });

    function toggleTaxSections() {
        if ($('input[name="other_state"]:checked').val() === 'yes') {
            // Other State = Yes → Show IGST, Hide CGST/SGST
            $('#igst_section').show();
            $('#cgst_sgst_section').hide();
        } else {
            // Other State = No → Show CGST/SGST, Hide IGST
            $('#igst_section').hide();
            $('#cgst_sgst_section').show();
        }
    }
    $('.debit_note_date, .ref_invoice_date').flatpickr({
        dateFormat: 'd-m-Y',
        allowInput: true
    });
    function calculateTotals() {
        let subtotal = 0;
        $('#debit_items tr').each(function () {
            let qty = parseFloat($(this).find('.quantity').val()) || 0;
            let rate = parseFloat($(this).find('.unit_price').val()) || 0;
            let total = qty * rate;
            $(this).find('.total_amount').val(total.toFixed(2));
            subtotal += total;
        });

        // Example tax calculation (9% CGST + 9% SGST)
        let cgst = subtotal * 0.09;
        let sgst = subtotal * 0.09;
        let totalTax = cgst + sgst;
        let grandTotal = subtotal + totalTax;

        $('#subtotal').val(subtotal.toFixed(2));
        $('#cgst_amount').val(cgst.toFixed(2));
        $('#sgst_amount').val(sgst.toFixed(2));
        $('#total_tax').val(totalTax.toFixed(2));
        $('#grand_total').val(grandTotal.toFixed(2));
    }

    // Add new item row
    $(document).on('click', '.add_item', function () {
        let newRow = `
            <tr>
                <td>
                    <select id="" class="select2 form-select" data-placeholder="Select Item">
                        <option value="">Select Item</option>
                        <option value="Men’s Casual Denim Shirt(ITEM001)">Men’s Casual Denim Shirt(ITEM001)</option>
                        <option value="Men’s Formal Cotton Shirt(ITEM002)">Men’s Formal Cotton Shirt(ITEM002)</option>
                        <option value="School Uniform Shirt(ITEM003)">School Uniform Shirt(ITEM003) </option>
                        <option value="Kids Polo Shirt(ITEM004)">Kids Polo Shirt(ITEM004)</option>
                        <option value="Premium Linen Shirt(ITEM005)">Premium Linen Shirt(ITEM005)</option>
                    </select>
                </td>
                <td><input type="text" class="form-control hsn_code" placeholder="Enter HSN Code" readonly></td>
                <td><input type="number" class="form-control quantity" placeholder="Enter Quantity" step="0.01"></td>
                <td>
                    <select class="select2 form-select uom" data-placeholder="Select UOM">
                        <option value="">Select UOM</option>
                        <option value="PCS">PCS</option>
                        <option value="MTR">MTR</option>
                        <option value="ROLL">ROLL</option>
                        <option value="KG">KG</option>
                        <option value="SET">SET</option>
                    </select>
                </td>
                <td><input type="number" class="form-control unit_price" placeholder="Enter Unit Price" step="0.01"></td>
                <td><input type="number" class="form-control total_amount" placeholder="Enter Line Total" step="0.01" readonly></td>
                <td><button type="button" class="btn btn-danger remove_item">-</button></td>
            </tr>
        `;
        $('#debit_items').append(newRow);
        $('.select2').select2();
    });

    $(document).on('click', '.remove_item', function () {
        $(this).closest('tr').remove();
        calculateTotals();
    });

    // Auto calculate total when quantity or rate changes
    $(document).on('input', '.quantity, .unit_price', function () {
        calculateTotals();
    });

});
</script>

@endsection

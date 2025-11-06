@extends('layouts.common')
@section('title', 'Add Purchase Order - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="" method="POST" class="common-form">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Add Purchase Order</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="po_no" placeholder="Enter PO Number" name="po_no">
                                    <label for="po_no">PO Number * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control form-control po_date" placeholder="Enter PO Date" />
                                    <label for="code">PO Date * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="" class="select2 form-select" data-placeholder="Select Broker/Sales Agent">
                                        <option value="">Select Broker/Sales Agent</option>
                                        <option value="Amit Kumar(SA101)">Amit Kumar(SA101)</option>
                                        <option value="Neha Sharma(SA102)">Neha Sharma(SA102)</option>
                                        <option value="Ravi Singh(SA103)">Ravi Singh(SA103)</option>
                                    </select>
                                    <label for="country">Broker/Sales Agent *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="permission" placeholder="Enter Permission (%)" name="permission">
                                    <label for="country">Permission (%) *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="" class="select2 form-select" data-placeholder="Select Supplier">
                                        <option value="">Select Supplier</option>
                                        <option value="Krishna Fabrics(SUP001)">Krishna Fabrics(SUP001)</option>
                                        <option value="Vasanth Garments(SUP002)">Vasanth Garments(SUP002)</option>
                                        <option value="Jaya Fabrics(SUP003)">Jaya Fabrics(SUP003)</option>
                                        <option value="Sri Meena Traders(SUP004)">Sri Meena Traders(SUP004)</option>
                                    </select>
                                    <label for="country">Supplier *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="reference_no" placeholder="Enter Reference No" name="name">
                                    <label for="reference_no">Reference No </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="reference_date" placeholder="Enter Reference Date" name="reference_date">
                                    <label for="reference_no">Reference Date </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control delivery_date" placeholder="Enter Due Date" />
                                    <label for="ref_date">Due Date * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="store_type" class="select2 form-select" data-placeholder="Select Store Type">
                                        <option value="">Select Store Type</option>
                                        <option value="Fabric Store">Fabric Store</option>
                                        <option value="Accessories Store">Accessories Store</option>
                                    </select>
                                    <label for="store_type">Store Type * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control form-control order_date" id="order_date" placeholder="Enter Order Date" />
                                    <label for="code">Order Date * </label>
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
                                    <select id="status" class="select2 form-select" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Draft">Draft</option>
                                        <option value="Approved">Approved</option>
                                        <option value="Dispatched">Dispatched</option>
                                        <option value="Received">Received</option>
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
                            <h4>Item Details</h4>
                        </div>

                        <div id="item-rows">
                            <div class="item-block mb-4">
                                <div class="row align-items-end g-3 item-row">
                                    <!-- Material -->
                                    <div class="col-xl-2 col-md-3">
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2 form-select material" data-placeholder="Select Material">
                                                <option value="">Select Material</option>
                                                <option value="Cotton Poplin 60 GSM(M001)">Cotton Poplin 60 GSM(M001)</option>
                                                <option value="Zipper(M002)">Zipper(M002)</option>
                                                <option value="Lace(M003)">Lace(M003)</option>
                                                <option value="Polyester Thread(M004)">Polyester Thread(M004)</option>
                                                <option value="Metal Buttons(M005)">Metal Buttons(M005)</option>
                                            </select>
                                            <label>Material *</label>
                                        </div>
                                    </div>

                                    <!-- UOM -->
                                    <div class="col-xl-1 col-md-2">
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

                                    <!-- Quantity -->
                                    <div class="col-xl-1 col-md-2">
                                        <div class="form-floating form-floating-outline">
                                            <input type="number" class="form-control quantity" placeholder="Enter Quantity">
                                            <label>Qty *</label>
                                        </div>
                                    </div>

                                    <!-- Rate -->
                                    <div class="col-xl-2 col-md-2">
                                        <div class="form-floating form-floating-outline">
                                            <input type="number" class="form-control rate" placeholder="Enter rate per unit">
                                            <label>Rate *</label>
                                        </div>
                                    </div>

                                    <!-- Amount -->
                                    <div class="col-xl-2 col-md-2">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control amount" placeholder="Enter Amount" readonly>
                                            <label>Amount *</label>
                                        </div>
                                    </div>

                                    <!-- Remarks -->
                                    <div class="col-xl-3 col-md-4">
                                        <div class="form-floating form-floating-outline">
                                            <textarea class="form-control remarks" placeholder="Enter Remarks" style="height: 58px;"></textarea>
                                            <label>Remarks</label>
                                        </div>
                                    </div>

                                    <!-- Add Button -->
                                    <div class="col-auto text-end">
                                        <button type="button" class="btn btn-primary add_item mt-2">
                                            <i class="ri ri-add-line icon-base"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== TOTAL & TAX SECTION ===== -->
                <div class="card">
                    <div class="card-body">
                        <div class="row g-4">

                            <div class="col-md-3 col-xl-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="total_qty" placeholder="Total Qty" readonly>
                                    <label for="total_qty">Total Qty</label>
                                </div>
                            </div>

                            <div class="col-md-3 col-xl-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="sub_total" placeholder="Sub Total" readonly>
                                    <label for="sub_total">Sub Total</label>
                                </div>
                            </div>

                            <div class="col-md-4 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <label class="form-label d-block mb-1">Discount:</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control text-end" id="discount_percent" placeholder="0.00" step="0.01">
                                        <span class="input-group-text">%</span>
                                        <input type="text" class="form-control text-end" id="discount_amount" placeholder="0.00" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="taxable_amount" class="form-control" readonly>
                                    <label>Net Amount (Before Tax)</label>
                                </div>
                            </div>
                            <!-- Other State Radio -->
                            <div class="col-md-6 col-xl-4">
                                <label class="">Other State</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="other_state" id="other_state_yes" value="yes">
                                    <label class="form-check-label" for="other_state_yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="other_state" id="other_state_no" value="no" checked>
                                    <label class="form-check-label" for="other_state_no">No</label>
                                </div>
                            </div>

                            <!-- IGST Field -->
                            <div class="col-md-3 col-xl-2 igst-field d-none">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control" id="igst_percent" placeholder="Enter IGST %" value="18">
                                    <label for="igst_percent">IGST %</label>
                                </div>
                            </div>

                            <!-- CGST Field -->
                            <div class="col-md-3 col-xl-2 cgst-field">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control" id="cgst_percent" placeholder="Enter CGST %" value="9">
                                    <label for="cgst_percent">CGST %</label>
                                </div>
                            </div>

                            <!-- SGST Field -->
                            <div class="col-md-3 col-xl-2 sgst-field">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control" id="sgst_percent" placeholder="Enter SGST %" value="9">
                                    <label for="sgst_percent">SGST %</label>
                                </div>
                            </div>

                            <!-- Missing taxes field -->
                            <div class="col-md-3 col-xl-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="taxes" placeholder="Tax Amount" readonly>
                                    <label for="taxes">Tax Amount</label>
                                </div>
                            </div>

                            <div class="col-md-3 col-xl-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="total_amount" placeholder="Total Amount" readonly>
                                    <label for="total_amount">Total Amount</label>
                                </div>
                            </div>

                        </div>
                        <div class="col-lg-12 text-end mt-5">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ url('purchase_orders') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {

        // Add new item row
        $('#item-rows').on('click', '.add_item', function () {
            var html = `
            <div class="item-block mb-3">
                <div class="row align-items-end g-3">
                    <div class="col-xl-2 col-md-3">
                        <div class="form-floating form-floating-outline">
                            <select class="select2 form-select material">
                                <option value="">Select Material</option>
                                <option value="Cotton Poplin 60 GSM(M001)">Cotton Poplin 60 GSM(M001)</option>
                                <option value="Zipper(M002)">Zipper(M002)</option>
                                <option value="Lace(M003)">Lace(M003)</option>
                                <option value="Polyester Thread(M004)">Polyester Thread(M004)</option>
                                <option value="Metal Buttons(M005)">Metal Buttons(M005)</option>
                            </select>
                            <label>Material *</label>
                        </div>
                    </div>

                    <div class="col-xl-1 col-md-2">
                        <div class="form-floating form-floating-outline">
                            <select class="select2 form-select uom">
                                <option value="">Select UOM</option>
                                <option value="MTR">MTR</option>
                                <option value="PCS">PCS</option>
                                <option value="ROLL">ROLL</option>
                            </select>
                            <label>UOM</label>
                        </div>
                    </div>

                    <div class="col-xl-1 col-md-2">
                        <div class="form-floating form-floating-outline">
                            <input type="number" class="form-control quantity" placeholder="Enter Quantity">
                            <label>Qty *</label>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-2">
                        <div class="form-floating form-floating-outline">
                            <input type="number" class="form-control rate" placeholder="Enter rate per unit">
                            <label>Rate *</label>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-2">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control amount" placeholder="Enter Amount" readonly>
                            <label>Amount *</label>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-4">
                        <div class="form-floating form-floating-outline">
                            <textarea class="form-control remarks" placeholder="Enter Remarks" style="height: 58px;"></textarea>
                            <label>Remarks</label>
                        </div>
                    </div>

                    <div class="col-auto text-end">
                        <button type="button" class="btn btn-danger delete_item mt-2">
                            <i class="ri ri-delete-bin-line icon-base"></i>
                        </button>
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

        // Qty or Rate input change
        $('#item-rows').on('input', '.quantity, .rate', function () {
            const row = $(this).closest('.item-block');
            const qty = parseFloat(row.find('.quantity').val()) || 0;
            const rate = parseFloat(row.find('.rate').val()) || 0;
            row.find('.amount').val((qty * rate).toFixed(2));
            calculateTotals();
        });

        // Discount or Tax input change
        $('#discount_percent, #igst_percent, #cgst_percent, #sgst_percent').on('input', calculateTotals);
        $('input[name="other_state"]').on('change', function () {
            toggleTaxFields();
            calculateTotals();
        });

        function calculateTotals() {
            let totalQty = 0, subTotal = 0;
            $('.item-block').each(function () {
                const qty = parseFloat($(this).find('.quantity').val()) || 0;
                const amt = parseFloat($(this).find('.amount').val()) || 0;
                totalQty += qty;
                subTotal += amt;
            });

            $('#total_qty').val(totalQty.toFixed(2));
            $('#sub_total').val(subTotal.toFixed(2));

            const discountPercent = parseFloat($('#discount_percent').val()) || 0;
            const discountAmount = (subTotal * discountPercent) / 100;
            $('#discount_amount').val(discountAmount.toFixed(2));

            const taxable = subTotal - discountAmount;
            $('#taxable_amount').val(taxable.toFixed(2));

            let taxAmount = 0;
            if ($('#other_state_yes').is(':checked')) {
                const igst = parseFloat($('#igst_percent').val()) || 0;
                taxAmount = (taxable * igst) / 100;
            } else {
                const cgst = parseFloat($('#cgst_percent').val()) || 0;
                const sgst = parseFloat($('#sgst_percent').val()) || 0;
                taxAmount = (taxable * (cgst + sgst)) / 100;
            }
            $('#taxes').val(taxAmount.toFixed(2));

            const total = taxable + taxAmount;
            $('#total_amount').val(total.toFixed(2));
        }

        function toggleTaxFields() {
            if ($('#other_state_yes').is(':checked')) {
                $('.igst-field').removeClass('d-none');
                $('.cgst-field, .sgst-field').addClass('d-none');
            } else {
                $('.igst-field').addClass('d-none');
                $('.cgst-field, .sgst-field').removeClass('d-none');
            }
        }

        toggleTaxFields();
    });
</script>
@endsection
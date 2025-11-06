@extends('layouts.common')
@section('title', 'Add Payment - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="" method="POST" class="common-form">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-box mb-4">
                            <h4>Add Payment</h4>
                        </div>

                        <div class="row g-4">

                            <!-- Payment Type -->
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" id="payment_type" name="payment_type" data-placeholder="Select Payment Type">
                                        <option value="">Select Payment Type</option>
                                        <option value="Customer Collection">Customer Collection</option>
                                        <option value="Supplier Payment">Supplier Payment</option>
                                        <option value="Agent Commission">Agent Commission</option>
                                    </select>
                                    <label for="payment_type">Payment Type</label>
                                </div>
                            </div>

                            <!-- Reference Document -->
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select name="reference_document" id="reference_document" class="form-select select2" data-placeholder="Select Reference Document">
                                        <option value="">Select Reference Document</option>
                                        <option value="po-invoice">PO Invoice</option>
                                        <option value="so-invoice">SO Invoice</option>
                                    </select>
                                    <label for="reference_document">Reference Document *</label>
                                </div>
                            </div>

                            <!-- Reference PO -->
                            <div class="col-md-6 d-none" id="reference_po">
                                <div class="form-floating form-floating-outline">
                                    <select name="reference_po" class="form-select select2" data-placeholder="Select Reference PO">
                                        <option value="">Select Reference</option>
                                        <option value="PO-2025-001">PO-2025-001</option>
                                        <option value="PO-2025-002">PO-2025-002</option>
                                        <option value="PO-2025-003">PO-2025-003</option>
                                    </select>
                                    <label for="reference_po">Reference PO *</label>
                                </div>
                            </div>

                            <!-- Reference SO -->
                            <div class="col-md-6 d-none" id="reference_so">
                                <div class="form-floating form-floating-outline">
                                    <select name="reference_so" class="form-select select2" data-placeholder="Select Reference SO">
                                        <option value="">Select Reference SO</option>
                                        <option value="SO-1001">SO-1001</option>
                                        <option value="SO-1002">SO-1002</option>
                                        <option value="SO-1003">SO-1003</option>
                                    </select>
                                    <label for="reference_so">Reference SO *</label>
                                </div>
                            </div>

                            <!-- Payment Mode -->
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select name="payment_mode" id="payment_mode" class="form-select select2" data-placeholder="Select Payment Mode">
                                        <option value="">Select Payment Mode</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Bank (Cheque)">Bank (Cheque)</option>
                                        <option value="Online (UPI)">Online (UPI)</option>
                                    </select>
                                    <label for="payment_mode">Payment Mode *</label>
                                </div>
                            </div>

                            <!-- Cheque / UPI -->
                            <div class="col-md-6 d-none" id="cheque_div">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="cheque_no" name="cheque_no" placeholder="Enter Cheque No">
                                    <label for="cheque_no">Cheque No *</label>
                                </div>
                            </div>

                            <div class="col-md-6 d-none" id="upi_div">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="upi_no" name="upi_no" placeholder="Enter UPI No">
                                    <label for="upi_no">UPI No *</label>
                                </div>
                            </div>

                            <!-- Invoice Amount -->
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="invoice_amt" name="invoice_amt" value="1000" readonly>
                                    <label for="invoice_amt">Invoice Amount *</label>
                                </div>
                            </div>

                            <!-- Amount Paid -->
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="amt_paid" placeholder="Enter Amount Paid" name="amt_paid">
                                    <label for="amt_paid">Amount Paid</label>
                                </div>
                            </div>

                            <!-- Balance -->
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="balance" placeholder="Enter Balance Outstanding" name="balance" readonly>
                                    <label for="balance">Balance Outstanding</label>
                                </div>
                            </div>

                            <!-- Payment Date -->
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control payment_date" id="payment_date" placeholder="Enter Payment Date" name="payment_date">
                                    <label for="payment_date">Payment Date *</label>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" id="status" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Cleared">Cleared</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Partial">Partial</option>
                                    </select>
                                    <label for="status">Status *</label>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('payments') }}" class="btn btn-secondary">Cancel</a>
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
        $('#payment_type').on('change', function () {
            var paymentType = $(this).val();

            $('#supplier_div').addClass('d-none');
            $('#customer_div').addClass('d-none');
            $('#sales_agent_div').addClass('d-none');

            if (paymentType === 'Customer Collection') {
                $('#customer_div').removeClass('d-none');
            } 
            else if (paymentType === 'Supplier Payment') {
                $('#supplier_div').removeClass('d-none');
            } 
            else if (paymentType === 'Agent Commission') {
                $('#sales_agent_div').removeClass('d-none');
            }
        });
        $('#reference_document').change(function() {
            var reference_document = $(this).val();
            if (reference_document === 'po-invoice') {
                $('#reference_po').removeClass('d-none');
                $('#reference_so').addClass('d-none');
            } else if (reference_document === 'so-invoice') {
                $('#reference_po').addClass('d-none');
                $('#reference_so').removeClass('d-none');
            }
        });
        $('#payment_mode').on('change', function() {
            let mode = $(this).val();
            if (mode === 'Bank (Cheque)') {
                $('#cheque_div').removeClass('d-none');
                $('#upi_div').addClass('d-none');
            } 
            else if (mode === 'Online (UPI)') {
                $('#cheque_div').addClass('d-none');
                $('#upi_div').removeClass('d-none');
            } else {
                $('#cheque_div').addClass('d-none');
                $('#upi_div').addClass('d-none');
            }
        });

        $('.payment_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            minDate: 'today',
            allowInput: true
        });
        $('#amt_paid').on('input', function() {
            let invoiceAmt = parseFloat($('#invoice_amt').val()) || 0;
            let amtPaid = parseFloat($(this).val()) || 0;
            if (amtPaid > invoiceAmt) {
                alert('Amount Paid cannot be greater than Invoice Amount (₹' + invoiceAmt + ')');
                $(this).val(''); 
                $('#balance').val(invoiceAmt.toFixed(2));
                return;
            }
            let balance = invoiceAmt - amtPaid;
            $('#balance').val(balance.toFixed(2));
        });

    });
</script>
@endsection
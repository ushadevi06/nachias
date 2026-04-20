@extends('layouts.common')
@section('title', 'Add Payment - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            @include('flash_messages')
        </div>
        <div class="col-lg-12">
            <form action="{{ url('payments/add') }}" method="POST" class="common-form" enctype="multipart/form-data" autocomplete="off">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-box mb-4">
                            <h4>Add Payment</h4>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select @error('payment_type') is-invalid @enderror" id="payment_type" name="payment_type" data-placeholder="Select Payment Type">
                                        <option value="">Select Payment Type</option>
                                        <option value="Customer Collection" {{ old('payment_type') == 'Customer Collection' ? 'selected' : '' }}>Customer Collection</option>
                                        <option value="Supplier Payment" {{ old('payment_type') == 'Supplier Payment' ? 'selected' : '' }}>Supplier Payment</option>
                                        <option value="Agent Commission" {{ old('payment_type') == 'Agent Commission' ? 'selected' : '' }}>Agent Commission</option>
                                    </select>
                                    <label for="payment_type">Payment Type *</label>
                                </div>
                                @error('payment_type')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select name="reference_document" id="reference_document" class="form-select select2 @error('reference_document') is-invalid @enderror" data-placeholder="Select Reference Document">
                                        <option value="">Select Reference Document</option>
                                    </select>
                                    <label for="reference_document">Reference Document *</label>
                                </div>
                                @error('reference_document')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select name="reference_no" id="reference_no" class="form-select select2 @error('reference_no') is-invalid @enderror" data-placeholder="Select Reference No">
                                        <option value="">Select Reference</option>
                                    </select>
                                    <label for="reference_no">Reference No *</label>
                                </div>
                                @error('reference_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 d-none" id="agent_info_div">
                                <div class="form-floating form-floating-outline mb-4">
                                    <input type="text" class="form-control" id="agent_name" readonly>
                                    <label for="agent_name">Agent Name</label>
                                </div>
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="commission_percent" readonly>
                                    <label for="commission_percent">Commission (%)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select name="payment_mode" id="payment_mode" class="form-select select2 @error('payment_mode') is-invalid @enderror" data-placeholder="Select Payment Mode">
                                        <option value="">Select Payment Mode</option>
                                        <option value="Cash" {{ old('payment_mode') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="Bank (Cheque)" {{ old('payment_mode') == 'Bank (Cheque)' ? 'selected' : '' }}>Bank (Cheque)</option>
                                        <option value="Online (UPI)" {{ old('payment_mode') == 'Online (UPI)' ? 'selected' : '' }}>Online (UPI)</option>
                                        <option value="NEFT/RTGS" {{ old('payment_mode') == 'NEFT/RTGS' ? 'selected' : '' }}>NEFT/RTGS</option>
                                    </select>
                                    <label for="payment_mode">Payment Mode *</label>
                                </div>
                                @error('payment_mode')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 {{ old('payment_mode') && in_array(old('payment_mode'), ['Bank (Cheque)', 'Online (UPI)', 'NEFT/RTGS']) ? '' : 'd-none' }}" id="bank_div">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('bank_name') is-invalid @enderror" id="bank_name" name="bank_name" placeholder="Enter Bank Name" value="{{ old('bank_name') }}">
                                    <label for="bank_name">Bank Name</label>
                                </div>
                                @error('bank_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 {{ old('payment_mode') == 'Bank (Cheque)' ? '' : 'd-none' }}" id="cheque_div">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('cheque_no') is-invalid @enderror" id="cheque_no" name="cheque_no" placeholder="Enter Cheque No" value="{{ old('cheque_no') }}">
                                    <label for="cheque_no">Cheque No</label>
                                </div>
                                @error('cheque_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 {{ old('payment_mode') == 'Bank (Cheque)' ? '' : 'd-none' }}" id="cheque_date_div">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control flatpickr @error('cheque_date') is-invalid @enderror" id="cheque_date" name="cheque_date" placeholder="Select Cheque Date" value="{{ old('cheque_date') }}">
                                    <label for="cheque_date">Cheque Date</label>
                                </div>
                                @error('cheque_date')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 {{ old('payment_mode') && in_array(old('payment_mode'), ['Online (UPI)', 'NEFT/RTGS']) ? '' : 'd-none' }}" id="upi_div">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('transaction_no') is-invalid @enderror" id="transaction_no" name="transaction_no" placeholder="Enter Transaction/UTR No" value="{{ old('transaction_no') }}">
                                    <label for="transaction_no">Transaction/UTR No</label>
                                </div>
                                @error('transaction_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="invoice_amt" value="0.00" readonly>
                                    <label for="invoice_amt">Invoice Total</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="paid_till_date" value="0.00" readonly>
                                    <label for="paid_till_date">Paid Till Date</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control text-danger font-bold" id="balance" value="0.00" readonly>
                                    <label for="balance">Outstanding Balance</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control font-bold @error('amount_paid') is-invalid @enderror" id="amount_paid" name="amount_paid" placeholder="Enter Amount Paid" value="{{ old('amount_paid') }}">
                                    <label for="amount_paid">Amount Paid *</label>
                                </div>
                                @error('amount_paid')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control flatpickr @error('payment_date') is-invalid @enderror" id="payment_date" name="payment_date" value="{{ old('payment_date', date('d-m-Y')) }}">
                                    <label for="payment_date">Payment Date *</label>
                                </div>
                                @error('payment_date')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating form-floating-outline">
                                    <textarea name="remarks" id="remarks" class="form-control" style="height: 100px" placeholder="Enter Remarks">{{ old('remarks') }}</textarea>
                                    <label for="remarks">Remarks</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="file" name="attachment" id="attachment" class="form-control">
                                    <label for="attachment">Attachment</label>
                                    <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">Max file size: 2MB. Supported formats: JPG, PNG, JPEG, WEBP, PDF, DOC, DOCX</small>
                                    @error('attachment') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    
                                    @if(isset($payment) && !empty($payment->attachment))
                                    <div class="mt-2 preview-container">
                                        @php
                                            $attachment = $payment->attachment;
                                            $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                            $url = asset($attachment);
                                        @endphp

                                        <div class="attachment-thumb border rounded p-1 bg-white shadow-sm position-relative" style="width: 100px; height: 100px;" title="{{ basename($attachment) }}">
                                            @if($isImage)
                                                <img src="{{ $url }}" class="w-100 h-100 object-fit-cover rounded cursor-pointer view-image" data-image="{{ $url }}" alt="Attachment">
                                            @else
                                                <a href="{{ $url }}" target="_blank" class="w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-light rounded text-decoration-none shadow-none text-primary">
                                                    <i class="ri ri-file-text-line fs-2"></i>
                                                    <span class="badge bg-primary text-white mt-1" style="font-size: 10px;">{{ strtoupper($extension) }}</span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    @else
                                    <div class="mt-2 preview-container"></div>
                                    @endif
                                </div>
                            </div>

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

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $(".flatpickr").flatpickr({
            dateFormat: "d-m-Y"
        });

        $('#payment_type').on('change', function () {
            var paymentType = $(this).val();
            var docSelect = $('#reference_document');
            var oldDoc = "{{ old('reference_document') }}";
            
            docSelect.html('<option value="">Select Reference Document</option>');
            if (paymentType === 'Customer Collection') {
                docSelect.append('<option value="so-invoice" ' + (oldDoc == "so-invoice" ? "selected" : "") + '>SO Invoice</option><option value="credit-note" ' + (oldDoc == "credit-note" ? "selected" : "") + '>Credit Note</option>');
            } else if (paymentType === 'Supplier Payment') {
                docSelect.append('<option value="po-invoice" ' + (oldDoc == "po-invoice" ? "selected" : "") + '>PO Invoice</option><option value="debit-note" ' + (oldDoc == "debit-note" ? "selected" : "") + '>Debit Note</option>');
            } else if (paymentType === 'Agent Commission') {
                docSelect.append('<option value="po-invoice" ' + (oldDoc == "po-invoice" ? "selected" : "") + '>PO Invoice</option><option value="so-invoice" ' + (oldDoc == "so-invoice" ? "selected" : "") + '>SO Invoice</option>');
            }
            
            if (paymentType === 'Agent Commission') {
                $('#agent_info_div').removeClass('d-none');
            } else {
                $('#agent_info_div').addClass('d-none');
                $('#agent_name, #commission_percent').val('');
            }
            
            docSelect.trigger('change');
        });

        $('#reference_document').change(function() {
            var docType = $(this).val();
            var paymentType = $('#payment_type').val();
            var refSelect = $('#reference_no');
            var oldRef = "{{ old('reference_no') }}";

            refSelect.html('<option value="">Select Reference</option>');
            $('#invoice_amt, #paid_till_date, #balance').val('0.00');

            if (docType && paymentType) {
                $.ajax({
                    url: "{{ url('get_references') }}",
                    method: 'GET',
                    data: { payment_type: paymentType, doc_type: docType },
                    success: function(data) {
                        data.forEach(function(item) {
                            var selected = (oldRef == item.id) ? "selected" : "";
                            refSelect.append('<option value="' + item.id + '" ' + selected + '>' + item.text + '</option>');
                        });
                        refSelect.trigger('change');
                    }
                });
            }
        });

        $('#reference_no').on('change', function() {
            var id = $(this).val();
            var docType = $('#reference_document').val();
            var paymentType = $('#payment_type').val();

            if (id && docType) {
                $.ajax({
                    url: "{{ url('get_reference_details') }}",
                    method: 'GET',
                    data: { id: id, doc_type: docType, payment_type: paymentType },
                    success: function(data) {
                        $('#invoice_amt').val(parseFloat(data.total).toFixed(2));
                        $('#paid_till_date').val(parseFloat(data.paid).toFixed(2));
                        $('#balance').val(parseFloat(data.outstanding).toFixed(2));

                        if (paymentType === 'Agent Commission') {
                            $('label[for="invoice_amt"]').text('Commission Amount');
                            $('#agent_name').val(data.agent_name);
                            $('#commission_percent').val(data.commission_percent);
                            $('#amount_paid').val(parseFloat(data.outstanding).toFixed(2));
                        } else {
                            $('label[for="invoice_amt"]').text('Invoice Total');
                        }
                    }
                });
            }
        });

        $('#payment_mode').on('change', function() {
            let mode = $(this).val();
            $('#cheque_div, #cheque_date_div, #bank_div, #upi_div').addClass('d-none');
            
            if (mode === 'Bank (Cheque)') {
                $('#cheque_div, #cheque_date_div, #bank_div').removeClass('d-none');
            } else if (mode === 'Online (UPI)' || mode === 'NEFT/RTGS') {
                $('#upi_div, #bank_div').removeClass('d-none');
            }
        });

        if ($('#payment_type').val()) {
            $('#payment_type').trigger('change');
        }
    });
</script>
@endsection
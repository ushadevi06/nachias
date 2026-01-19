@extends('layouts.common')
@section('title', 'Add Debit Note - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            
            <form action="{{ url('debit_notes/add/' . ($debitNote->id ?? '')) }}" method="POST" class="common-form" enctype="multipart/form-data">
                @csrf
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Add Debit Note</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="debit_note_no" name="debit_note_no" class="form-control" placeholder="Enter Debit Note No" value="{{ old('debit_note_no', $debitNote->debit_note_no ?? $nextDebitNoteNo) }}" required {{ isset($debitNote) ? 'readonly' : '' }}>
                                    <label for="debit_note_no">Debit Note No <span class="text-danger">*</span></label>
                                </div>
                                @error('debit_note_no') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="date" id="debit_note_date" name="debit_note_date" class="form-control" value="{{ old('debit_note_date', isset($debitNote) ? $debitNote->debit_note_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                                    <label for="debit_note_date">Debit Note Date <span class="text-danger">*</span></label>
                                </div>
                                @error('debit_note_date') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="purchase_invoice_id" name="purchase_invoice_id" class="form-select select2" data-placeholder="Select Invoice" required>
                                        <option value="">Select Invoice</option>
                                        @foreach($purchaseInvoices as $invoice)
                                            <option value="{{ $invoice->id }}" {{ (old('purchase_invoice_id', $debitNote->purchase_invoice_id ?? '') == $invoice->id) ? 'selected' : '' }}>{{ $invoice->invoice_no }}</option>
                                        @endforeach
                                    </select>
                                    <label for="purchase_invoice_id">Select Purchase Invoice <span class="text-danger">*</span></label>
                                </div>
                                @error('purchase_invoice_id') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="hidden" id="supplier_id_hidden" name="supplier_id" value="{{ old('supplier_id', $debitNote->supplier_id ?? '') }}">
                                    <input type="text" id="supplier_name" class="form-control" value="{{ $debitNote->supplier->name ?? '' }}" readonly placeholder="Supplier">
                                    <label for="supplier_name">Supplier <span class="text-danger">*</span></label>
                                </div>
                                @error('supplier_id') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="reason" name="reason" class="form-control" placeholder="Enter Reason" value="{{ old('reason', $debitNote->reason ?? '') }}">
                                    <label for="reason">Reason for Debit Note</label>
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
                                    <tr class="item-row">
                                        <th>Select</th>
                                        <th>Item Name</th>
                                        <th>UOM</th>
                                        <th>Quantity</th>
                                        <th>Rate</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="items_tbody">
                                    @if(isset($debitNote))
                                        @foreach($debitNote->items as $index => $item)
                                            <tr class="item-row">
                                                <td>
                                                    <input type="checkbox" name="items[{{ $index }}][selected]" value="1" class="form-check-input item-checkbox" checked>
                                                    <input type="hidden" name="items[{{ $index }}][purchase_invoice_item_id]" value="{{ $item->purchase_invoice_item_id }}">
                                                    <input type="hidden" name="items[{{ $index }}][raw_material_id]" value="{{ $item->raw_material_id }}">
                                                </td>
                                                <td>{{ $item->rawMaterial->name ?? '-' }}</td>
                                                <td>
                                                    <input type="hidden" name="items[{{ $index }}][uom_id]" value="{{ $item->uom_id }}">
                                                    {{ $item->uom->uom_code ?? '-' }}
                                                </td>
                                                <td>
                                                    <input type="number" name="items[{{ $index }}][quantity]" class="form-control item-qty" value="{{ $item->quantity }}" step="0.01">
                                                </td>
                                                <td>
                                                    <input type="number" name="items[{{ $index }}][rate]" class="form-control item-rate" value="{{ $item->rate }}" step="0.01" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[{{ $index }}][amount]" class="form-control item-amount" value="{{ $item->amount }}" step="0.01" readonly>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
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
                                            <textarea class="form-control" id="remarks" name="remarks" placeholder="Remarks" style="height: 120px;">{{ old('remarks', $debitNote->remarks ?? '') }}</textarea>
                                            <label for="remarks">Remarks</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group mb-3">
                                            <label for="reference_document" class="form-label">Reference Document (Attachment)</label>
                                            <input type="file" id="reference_document" name="reference_document" class="form-control">
                                            @if(isset($debitNote) && $debitNote->reference_document)
                                                <div class="mt-1">
                                                    <a href="{{ url('uploads/debit_notes/' . $debitNote->reference_document) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ri-eye-line me-1"></i> View Attachment</a>
                                                </div>
                                            @endif
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
                                    <div class="col-md-6"><label class="fw-bold">Other State?</label></div>
                                    <div class="col-md-6 text-end">
                                        <div class="d-flex gap-4 justify-content-end">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="other_state" id="other_state_yes" value="Y" {{ (old('other_state', $debitNote->other_state ?? 'N') == 'Y') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="other_state_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="other_state" id="other_state_no" value="N" {{ (old('other_state', $debitNote->other_state ?? 'N') == 'N') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="other_state_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 align-items-center mb-3">
                                    <div class="col-md-6"><label class="fw-bold">Sub total:</label></div>
                                    <div class="col-md-6 text-end">
                                        <input type="hidden" id="sub_total" name="sub_total" value="{{ old('sub_total', $debitNote->sub_total ?? '0.00') }}">
                                        <span id="sub_total_display" class="fw-bold">₹{{ number_format(old('sub_total', $debitNote->sub_total ?? 0), 2) }}</span>
                                    </div>
                                </div>

                                <div id="igst_div" style="display: none;">
                                    <div class="row g-3 align-items-center mb-3">
                                        <div class="col-md-6"><label class="fw-bold">IGST:</label></div>
                                        <div class="col-md-6 text-end">
                                            <div class="d-flex gap-2 align-items-center justify-content-end">
                                                <input type="number" name="igst_percent" id="igst_percent" value="{{ old('igst_percent', $debitNote->igst_percent ?? $debitNote->purchaseInvoice->igst_percent ?? 0) }}" class="form-control form-control-sm text-end" style="width:80px;">
                                                <span>%</span>
                                                <strong id="igst_amt">₹0.00</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="cgst_sgst_div" style="display: none;">
                                    <div class="row g-3 align-items-center mb-3">
                                        <div class="col-md-6"><label class="fw-bold">CGST:</label></div>
                                        <div class="col-md-6 text-end">
                                            <div class="d-flex gap-2 align-items-center justify-content-end">
                                                <input type="number" name="cgst_percent" id="cgst_percent" value="{{ old('cgst_percent', $debitNote->cgst_percent ?? $debitNote->purchaseInvoice->cgst_percent ?? 0) }}" class="form-control form-control-sm text-end" style="width:80px;">
                                                <span>%</span>
                                                <strong id="cgst_amt">₹0.00</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-items-center mb-3">
                                        <div class="col-md-6"><label class="fw-bold">SGST:</label></div>
                                        <div class="col-md-6 text-end">
                                            <div class="d-flex gap-2 align-items-center justify-content-end">
                                                <input type="number" name="sgst_percent" id="sgst_percent" value="{{ old('sgst_percent', $debitNote->sgst_percent ?? $debitNote->purchaseInvoice->sgst_percent ?? 0) }}" class="form-control form-control-sm text-end" style="width:80px;">
                                                <span>%</span>
                                                <strong id="sgst_amt">₹0.00</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 align-items-center mb-3">
                                    <div class="col-md-6"><label class="fw-bold">Tax Amount:</label></div>
                                    <div class="col-md-6 text-end">
                                        <input type="hidden" id="tax_amount" name="tax_amount" value="{{ old('tax_amount', $debitNote->tax_amount ?? '0.00') }}">
                                        <span id="tax_amount_display" class="fw-bold">₹{{ number_format(old('tax_amount', $debitNote->tax_amount ?? 0), 2) }}</span>
                                    </div>
                                </div>

                                <hr>

                                <div class="row g-3 align-items-center">
                                    <div class="col-md-6"><label class="fw-bold">Grand Total:</label></div>
                                    <div class="col-md-6 text-end">
                                        <input type="hidden" id="grand_total" name="grand_total" value="{{ old('grand_total', $debitNote->grand_total ?? '0.00') }}">
                                        <span id="grand_total_display" class="fw-bold fs-5 text-success">₹{{ number_format(old('grand_total', $debitNote->grand_total ?? 0), 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mb-5 me-4 mt-5">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ url('debit_notes') }}" class="btn btn-secondary">Cancel</a>
                </div>  
            </form>
        </div>
    </div>
</div>
<script>
    let taxInfo = {
        other_state: 'N',
        igst_percent: 0,
        cgst_percent: 0,
        sgst_percent: 0
    };

    $(document).ready(function() {
        $('#purchase_invoice_id').on('change', function() {
            let invoiceId = $(this).val();
            if (invoiceId) {
                $.get("{{ url('debit_notes/get-invoice-details') }}/" + invoiceId, function(res) {
                    if (res.success) {
                        $('#supplier_name').val(res.supplier_name);
                        $('#supplier_id_hidden').val(res.supplier_id);
                        
                        $('input[name="other_state"][value="' + res.other_state + '"]').prop('checked', true);
                        $('#igst_percent').val(res.igst_percent);
                        $('#cgst_percent').val(res.cgst_percent);
                        $('#sgst_percent').val(res.sgst_percent);
                        
                        toggleTaxDivs();

                        let tbody = $('#items_tbody');
                        tbody.empty();
                        res.items.forEach((item, index) => {
                            tbody.append(`
                                <tr class="item-row">
                                    <td>
                                        <input type="checkbox" name="items[${index}][selected]" value="1" class="form-check-input item-checkbox" checked>
                                        <input type="hidden" name="items[${index}][purchase_invoice_item_id]" value="${item.id}">
                                        <input type="hidden" name="items[${index}][raw_material_id]" value="${item.raw_material_id}">
                                    </td>
                                    <td>${item.raw_material_name}</td>
                                    <td>
                                        <input type="hidden" name="items[${index}][uom_id]" value="${item.uom_id}">
                                        ${item.uom_code}
                                    </td>
                                    <td>
                                        <input type="number" name="items[${index}][quantity]" class="form-control item-qty" value="${item.quantity}" step="0.01" max="${item.quantity}">
                                    </td>
                                    <td>
                                        <input type="number" name="items[${index}][rate]" class="form-control item-rate" value="${item.rate}" step="0.01" readonly>
                                    </td>
                                    <td>
                                        <input type="number" name="items[${index}][amount]" class="form-control item-amount" value="${item.amount}" step="0.01" readonly>
                                    </td>
                                </tr>
                            `);
                        });
                        calculateTotals();
                    }
                });
            } else {
                $('#supplier_name').val('');
                $('#supplier_id_hidden').val('');
                $('#items_tbody').empty();
                calculateTotals();
            }
        });

        $('input[name="other_state"]').on('change', function() {
            toggleTaxDivs();
            calculateTotals();
        });

        function toggleTaxDivs() {
            let otherState = $('input[name="other_state"]:checked').val();
            if (otherState === 'Y') {
                $('#igst_div').show();
                $('#cgst_sgst_div').hide();
            } else {
                $('#igst_div').hide();
                $('#cgst_sgst_div').show();
            }
        }

        $(document).on('input', '.item-qty, #igst_percent, #cgst_percent, #sgst_percent', function() {
            let row = $(this).closest('tr');
            if (row.hasClass('item-row')) {
                let qty = parseFloat(row.find('.item-qty').val()) || 0;
                let rate = parseFloat(row.find('.item-rate').val()) || 0;
                row.find('.item-amount').val((qty * rate).toFixed(2));
            }
            calculateTotals();
        });

        $(document).on('change', '.item-checkbox', function() {
            calculateTotals();
        });

        function calculateTotals() {
            let subTotal = 0;
            $('.item-row').each(function() {
                if ($(this).find('.item-checkbox').is(':checked')) {
                    subTotal += parseFloat($(this).find('.item-amount').val()) || 0;
                }
            });

            $('#sub_total').val(subTotal.toFixed(2));
            $('#sub_total_display').text('₹' + subTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));

            let otherState = $('input[name="other_state"]:checked').val();
            let taxAmount = 0;

            if (otherState === 'Y') {
                let igstPercent = parseFloat($('#igst_percent').val()) || 0;
                let igstAmt = subTotal * (igstPercent / 100);
                $('#igst_amt').text('₹' + igstAmt.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                taxAmount = igstAmt;
            } else {
                let cgstPercent = parseFloat($('#cgst_percent').val()) || 0;
                let sgstPercent = parseFloat($('#sgst_percent').val()) || 0;
                let cgstAmt = subTotal * (cgstPercent / 100);
                let sgstAmt = subTotal * (sgstPercent / 100);
                $('#cgst_amt').text('₹' + cgstAmt.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('#sgst_amt').text('₹' + sgstAmt.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                taxAmount = cgstAmt + sgstAmt;
            }

            $('#tax_amount').val(taxAmount.toFixed(2));
            $('#tax_amount_display').text('₹' + taxAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));

            // Automated Round Off Logic
            let totalBeforeRoundOff = subTotal + taxAmount;
            let grandTotal = Math.round(totalBeforeRoundOff);
            let roundOff = grandTotal - totalBeforeRoundOff;
            let roundOffAmt = Math.abs(roundOff);
            let roundOffType = roundOff >= 0 ? 'Add' : 'Less';

            $('#round_off').val(roundOffAmt.toFixed(2));
            $('#round_off_display').text('₹' + roundOffAmt.toFixed(2));
            $('#round_off_type').val(roundOffType);
            
            let roundOffBadge = $('#round_off_type_display');
            roundOffBadge.text(roundOffType);
            if (roundOffType === 'Add') {
                roundOffBadge.removeClass('bg-label-danger').addClass('bg-label-primary');
            } else {
                roundOffBadge.removeClass('bg-label-primary').addClass('bg-label-danger');
            }

            $('#grand_total').val(grandTotal.toFixed(2));
            $('#grand_total_display').text('₹' + grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        }

        @if(isset($debitNote))
             $('input[name="other_state"][value="{{ $debitNote->other_state }}"]').prop('checked', true);
             toggleTaxDivs();
             calculateTotals();
        @endif
    });
</script>

@endsection

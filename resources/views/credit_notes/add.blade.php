@extends('layouts.common')
@section('title', (isset($creditNote) ? 'Edit' : 'Add') . ' Credit Note - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="{{ url('credit_notes/add' . (isset($creditNote) ? '/' . $creditNote->id : '')) }}" method="POST" class="common-form" enctype="multipart/form-data">
                @csrf
                <!-- Credit Note Details -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>{{ isset($creditNote) ? 'Edit' : 'Add' }} Credit Note</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="note_no" placeholder="Enter Credit Note No." name="note_no" value="{{ old('note_no', $creditNote->note_no ?? $nextNoteNo) }}">
                                    <label for="note_no">Credit Note No. *</label>
                                    @error('note_no') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control note_date" id="note_date" name="note_date" placeholder="Enter Date" value="{{ old('note_date', isset($creditNote) ? $creditNote->note_date->format('Y-m-d') : date('Y-m-d')) }}">
                                    <label for="note_date">Date *</label>
                                    @error('note_date') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" name="sales_invoice_id" id="sales_invoice_id" data-placeholder="Select Invoice No">
                                        <option value="">Select Invoice No</option>
                                        @foreach($salesInvoices as $invoice)
                                            <option value="{{ $invoice->id }}" {{ (old('sales_invoice_id', $creditNote->sales_invoice_id ?? '') == $invoice->id) ? 'selected' : '' }}>{{ $invoice->inv_no }}</option>
                                        @endforeach
                                    </select>
                                    <label for="sales_invoice_id">Invoice No *</label>
                                    @error('sales_invoice_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" name="customer_id" id="customer_id" data-placeholder="Select Customer/Buyer">
                                        <option value="">Select Customer/Buyer</option>
                                        @foreach(\App\Models\Customer::all() as $customer)
                                            <option value="{{ $customer->id }}" {{ (old('customer_id', $creditNote->customer_id ?? '') == $customer->id) ? 'selected' : '' }}>{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="customer_id">Customer / Buyer *</label>
                                    @error('customer_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" name="reason" data-placeholder="Select Reason">
                                        <option value="">Select Reason</option>
                                        <option value="Return" {{ old('reason', $creditNote->reason ?? '') == 'Return' ? 'selected' : '' }}>Return</option>
                                        <option value="Excess Billing" {{ old('reason', $creditNote->reason ?? '') == 'Excess Billing' ? 'selected' : '' }}>Excess Billing</option>
                                        <option value="Short Supply" {{ old('reason', $creditNote->reason ?? '') == 'Short Supply' ? 'selected' : '' }}>Short Supply</option>
                                        <option value="Rate Correction" {{ old('reason', $creditNote->reason ?? '') == 'Rate Correction' ? 'selected' : '' }}>Rate Correction</option>
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
                        <div class="card-header-box border-bottom pb-2 mb-3">
                            <h5 class="mb-0">Item Details</h5>
                        </div>

                        <div class="table-responsive p-1">
                            <table class="table table-bordered align-middle" id="itemTable" style="min-width: 1300px;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">SELECT</th>
                                        <th style="width: 180px;">BRAND CATEGORY</th>
                                        <th style="width: 280px;">ITEM NAME</th>
                                        <th style="width: 100px;">SIZE</th>
                                        <th style="width: 100px;">UOM</th>
                                        <th style="width: 120px;">QUANTITY</th>
                                        <th style="width: 120px;">RATE</th>
                                        <th style="width: 150px;">MRP</th>
                                        <th style="width: 180px;">AMOUNT</th>
                                    </tr>
                                </thead>
                                <tbody id="item-rows">
                                    @php
                                        $displayItems = [];
                                        if (old('items')) {
                                            foreach (old('items') as $oldItem) {
                                                $itemModel = \App\Models\Item::find($oldItem['item_id']);
                                                $uomModel = \App\Models\Uom::find($oldItem['uom_id'] ?? null);
                                                $displayItems[] = (object)[
                                                    'selected' => isset($oldItem['selected']),
                                                    'sales_invoice_item_id' => $oldItem['sales_invoice_item_id'],
                                                    'item_id' => $oldItem['item_id'],
                                                    'item_name' => $itemModel ? $itemModel->name : '-',
                                                    'item_code' => $itemModel ? $itemModel->code : '-',
                                                    'brand_category_id' => $oldItem['brand_category_id'] ?? null,
                                                    'brand_category_name' => \App\Models\BrandCategory::find($oldItem['brand_category_id'] ?? null)->name ?? '-',
                                                    'size' => $oldItem['size'],
                                                    'uom_id' => $oldItem['uom_id'] ?? null,
                                                    'uom_code' => $uomModel ? $uomModel->uom_code : '-',
                                                    'quantity' => $oldItem['quantity'],
                                                    'rate' => $oldItem['rate'],
                                                    'mrp' => $oldItem['mrp'],
                                                    'amount' => $oldItem['amount'],
                                                    'sleeve_type' => $oldItem['sleeve_type']
                                                ];
                                            }
                                        } elseif (isset($creditNote)) {
                                            foreach ($creditNote->items as $noteItem) {
                                                $displayItems[] = (object)[
                                                    'selected' => true,
                                                    'sales_invoice_item_id' => $noteItem->sales_invoice_item_id,
                                                    'item_id' => $noteItem->item_id,
                                                    'item_name' => $noteItem->item ? $noteItem->item->name : '-',
                                                    'item_code' => $noteItem->item ? $noteItem->item->code : '-',
                                                    'brand_category_id' => $noteItem->brand_category_id,
                                                    'brand_category_name' => $noteItem->brandCategory ? $noteItem->brandCategory->name : '-',
                                                    'size' => $noteItem->size,
                                                    'uom_id' => $noteItem->uom_id,
                                                    'uom_code' => $noteItem->uom ? $noteItem->uom->uom_code : '-',
                                                    'quantity' => $noteItem->quantity,
                                                    'rate' => $noteItem->rate,
                                                    'mrp' => $noteItem->mrp,
                                                    'amount' => $noteItem->amount,
                                                    'sleeve_type' => $noteItem->sleeve_type,
                                                ];
                                            }
                                        }
                                    @endphp

                                    @if(count($displayItems) > 0)
                                        @foreach($displayItems as $index => $item)
                                            <tr class="item-row">
                                                <td class="text-center">
                                                    <div class="form-check d-flex justify-content-center">
                                                        <input class="form-check-input row-select" type="checkbox" name="items[{{$index}}][selected]" value="1" {{ $item->selected ? 'checked' : '' }}>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="hidden" name="items[{{$index}}][brand_category_id]" value="{{ $item->brand_category_id }}">
                                                    <input type="text" class="form-control" value="{{ $item->brand_category_name }}" readonly>
                                                </td>
                                                <td>
                                                    <input type="hidden" name="items[{{$index}}][sales_invoice_item_id]" value="{{ $item->sales_invoice_item_id }}">
                                                    <input type="hidden" name="items[{{$index}}][item_id]" value="{{ $item->item_id }}">
                                                    @php
                                                        $sleeveShort = '';
                                                        if ($item->sleeve_type) {
                                                            $sleeveShort = ' - ' . (strtolower($item->sleeve_type) == 'full' ? 'F/S' : 'H/S');
                                                        }
                                                    @endphp
                                                    <input type="text" class="form-control" value="{{ $item->item_name . ' (' . $item->item_code . ')' . $sleeveShort }}" readonly title="{{ $item->item_name }}">
                                                    <input type="hidden" name="items[{{$index}}][sleeve_type]" value="{{ $item->sleeve_type }}">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="items[{{$index}}][size]" value="{{ $item->size }}" readonly>
                                                </td>
                                                <td>
                                                    <input type="hidden" name="items[{{$index}}][uom_id]" value="{{ $item->uom_id }}">
                                                    <input type="text" class="form-control" value="{{ $item->uom_code }}" readonly>
                                                </td>
                                                <td><input type="number" class="form-control qty" name="items[{{$index}}][quantity]" value="{{ $item->quantity }}" step="0.01"></td>
                                                <td><input type="number" class="form-control rate" name="items[{{$index}}][rate]" value="{{ $item->rate }}" step="0.01"></td>
                                                <td>
                                                    <input type="number" class="form-control mrp" name="items[{{$index}}][mrp]" value="{{ $item->mrp }}" step="0.01" readonly>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-merge">
                                                        <span class="input-group-text">₹</span>
                                                        <input type="text" class="form-control line_total" value="{{ number_format($item->amount, 2, '.', '') }}" readonly name="items[{{$index}}][amount]">
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="empty-row text-center">
                                            <td colspan="10">Select an Invoice to load items</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Additional Information Section -->
                    <div class="col-lg-7">
                        <div class="card mb-4" style="height: calc(100% - 1.5rem);">
                            <div class="card-body">
                                <div class="card-header-box mb-3">
                                    <h5 class="mb-0">Additional Information</h5>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-group mb-3">
                                            <textarea class="form-control" id="remarks" name="remarks" placeholder="Remarks">{{ old('remarks', $creditNote->remarks ?? '') }}</textarea>
                                            @error('remarks') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="reference_document" class="form-label fw-bold small text-muted">Reference Document (Attachment)</label>
                                            <input class="form-control" type="file" id="reference_document" name="reference_document">
                                            <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">Max file size: 2MB. Supported formats: JPG, PNG, JPEG, WEBP, PDF, DOC, DOCX</small>
                                            @error('reference_document') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                            @if(isset($creditNote) && $creditNote->reference_doc)
                                                <div class="mb-2">
                                                    <a href="{{ asset('uploads/credit_notes/' . $creditNote->reference_doc) }}" target="_blank" class="mt-1 d-block">
                                                        <i class="ri ri-image-line"></i> View
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tax Summary Section -->
                    <div class="col-lg-5">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="card-header-box mb-3">
                                    <h5 class="mb-0">Tax Summary</h5>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="d-flex gap-3 align-items-center">
                                        <label class="fw-bold small mb-0">Other State?</label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check m-0">
                                                <input class="form-check-input" type="radio" name="is_other_state" id="state_yes" value="yes" {{ (old('is_other_state', ($creditNote->other_state ?? false) == true) ? 'checked' : '') }}>
                                                <label class="form-check-label small" for="state_yes">Yes</label>
                                            </div>
                                            <div class="form-check m-0">
                                                <input class="form-check-input" type="radio" name="is_other_state" id="state_no" value="no" {{ (old('is_other_state', ($creditNote->other_state ?? false) == false) ? 'checked' : '') }}>
                                                <label class="form-check-label small" for="state_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-3">
                                    <label class="fw-bold text-muted">Sub total:</label>
                                    <div class="text-end">
                                        <input type="hidden" name="sub_total" id="sub_total" value="{{ old('sub_total', $creditNote->sub_total ?? 0) }}">
                                        <span id="sub_total_text" class="fw-bold">₹{{ number_format(old('sub_total', $creditNote->sub_total ?? 0), 2) }}</span>
                                    </div>
                                </div>

                                <div id="cgst_row" class="{{ (old('is_other_state', ($creditNote->other_state ?? false) == true) ? 'd-none' : '') }}">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="fw-bold text-muted">CGST:</label>
                                        <div class="d-flex gap-2 align-items-center">
                                            <input type="number" name="cgst_percent" id="cgst_percent" class="form-control form-control-sm text-end" style="width: 85px; border: 1px solid #e5e6e8;" value="{{ old('cgst_percent', $creditNote->cgst_percent ?? 9) }}" step="0.01">
                                            <span class="small">%</span>
                                            <span class="ms-2">₹<span id="cgst_amt_text">{{ number_format(old('cgst', $creditNote->cgst ?? 0), 2) }}</span></span>
                                            <input type="hidden" name="cgst_amt" id="cgst_amt" value="{{ old('cgst', $creditNote->cgst ?? 0) }}">
                                        </div>
                                    </div>
                                </div>

                                <div id="sgst_row" class="{{ (old('is_other_state', ($creditNote->other_state ?? false) == true) ? 'd-none' : '') }}">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="fw-bold text-muted">SGST:</label>
                                        <div class="d-flex gap-2 align-items-center">
                                            <input type="number" name="sgst_percent" id="sgst_percent" class="form-control form-control-sm text-end" style="width: 85px; border: 1px solid #e5e6e8;" value="{{ old('sgst_percent', $creditNote->sgst_percent ?? 9) }}" step="0.01">
                                            <span class="small">%</span>
                                            <span class="ms-2">₹<span id="sgst_amt_text">{{ number_format(old('sgst', $creditNote->sgst ?? 0), 2) }}</span></span>
                                            <input type="hidden" name="sgst_amt" id="sgst_amt" value="{{ old('sgst', $creditNote->sgst ?? 0) }}">
                                        </div>
                                    </div>
                                </div>

                                <div id="igst_row" class="{{ (old('is_other_state', ($creditNote->other_state ?? false) == false) ? 'd-none' : '') }}">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="fw-bold text-muted">IGST:</label>
                                        <div class="d-flex gap-2 align-items-center">
                                            <input type="number" name="igst_percent" id="igst_percent" class="form-control form-control-sm text-end" style="width: 85px; border: 1px solid #e5e6e8;" value="{{ old('igst_percent', $creditNote->igst_percent ?? 18) }}" step="0.01">
                                            <span class="small">%</span>
                                            <span class="ms-2">₹<span id="igst_amt_text">{{ number_format(old('igst', $creditNote->igst ?? 0), 2) }}</span></span>
                                            <input type="hidden" name="igst_amt" id="igst_amt" value="{{ old('igst', $creditNote->igst ?? 0) }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-3 mt-4">
                                    <label class="fw-bold text-muted">Tax Amount:</label>
                                    <div class="text-end">
                                        <input type="hidden" name="tax_amount" id="tax_amount" value="{{ old('tax_amount', $creditNote->tax_amount ?? 0) }}">
                                        <span id="tax_amount_text" class="fw-bold">₹{{ number_format(old('tax_amount', $creditNote->tax_amount ?? 0), 2) }}</span>
                                    </div>
                                </div>

                                <hr class="my-3">

                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="fw-bold" style="font-size: 1.1rem;">Grand Total:</label>
                                    <div class="text-end">
                                        <input type="hidden" name="grand_total" id="grand_total" value="{{ old('grand_total', $creditNote->grand_total ?? 0) }}">
                                        <span id="grand_total_text" class="fw-bold" style="font-size: 1.5rem; color: #28a745;">₹{{ number_format(old('grand_total', $creditNote->grand_total ?? 0), 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 text-end mt-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="{{ url('credit_notes') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $(".select2").select2();
    $('#sales_invoice_id').on('change', function() {
        let id = $(this).val();
        if (!id) {
            $('#item-rows').html('<tr class="empty-row text-center"><td colspan="10">Select an Invoice to load items</td></tr>');
            return;
        }

        $.ajax({
            url: "{{ url('credit_notes/get-invoice-details') }}/" + id,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#customer_id').val(response.customer_id).trigger('change');
                    if (response.other_state == 'yes') {
                        $('#state_yes').prop('checked', true).trigger('change');
                    } else {
                        $('#state_no').prop('checked', true).trigger('change');
                    }
                    let rows = '';
                    response.items.forEach((item, index) => {
                        rows += `
                        <tr class="item-row">
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input row-select" type="checkbox" name="items[${index}][selected]" value="1" checked>
                                </div>
                            </td>
                            <td>
                                <input type="hidden" name="items[${index}][brand_category_id]" value="${item.brand_category_id || ''}">
                                <input type="text" class="form-control" value="${item.brand_category_name || '-'}" readonly>
                            </td>
                            <td>
                                <input type="hidden" name="items[${index}][sales_invoice_item_id]" value="${item.id}">
                                <input type="hidden" name="items[${index}][item_id]" value="${item.item_id}">
                                <input type="text" class="form-control" value="${item.item_name} (${item.item_code})${item.sleeve_type ? ' - ' + (item.sleeve_type.toLowerCase() == 'full' ? 'F/S' : 'H/S') : ''}" readonly title="${item.item_name}">
                                <input type="hidden" name="items[${index}][sleeve_type]" value="${item.sleeve_type || ''}">
                            </td>
                            <td>
                                <input type="text" class="form-control" name="items[${index}][size]" value="${item.size || ''}" readonly>
                            </td>
                            <td>
                                <input type="hidden" name="items[${index}][uom_id]" value="${item.uom_id}">
                                <input type="text" class="form-control" value="${item.uom_code}" readonly>
                            </td>
                            <td><input type="number" class="form-control qty" name="items[${index}][quantity]" value="${item.quantity}" step="0.01"></td>
                            <td><input type="number" class="form-control rate" name="items[${index}][rate]" value="${item.rate}" step="0.01"></td>
                            <td>
                                <input type="number" class="form-control" name="items[${index}][mrp]" value="${item.mrp || 0}" step="0.01" readonly>
                            </td>
                            <td>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text">₹</span>
                                    <input type="text" class="form-control line_total" value="${item.amount}" readonly name="items[${index}][amount]">
                                </div>
                            </td>
                        </tr>`;
                    });
                    $('#item-rows').html(rows);
                    $('#igst_percent').val(response.igst_percent);
                    $('#cgst_percent').val(response.cgst_percent);
                    $('#sgst_percent').val(response.sgst_percent);
                    calculateTotal();
                }
            }
        });
    });

    $(document).on('input', '.qty, .rate', function() {
        let row = $(this).closest('.item-row');
        let qty = parseFloat(row.find('.qty').val()) || 0;
        let rate = parseFloat(row.find('.rate').val()) || 0;
        let lineTotal = qty * rate;
        row.find('.line_total').val(lineTotal.toFixed(2));
        calculateTotal();
    });

    $(document).on('change', '.row-select', calculateTotal);
    $(document).on('input', '#cgst_percent, #sgst_percent, #igst_percent', calculateTotal);
    $('input[name="is_other_state"]').on('change', function() {
        if ($('#state_yes').is(':checked')) {
            $('#igst_row').removeClass('d-none');
            $('#cgst_row, #sgst_row').addClass('d-none');
        } else {
            $('#igst_row').addClass('d-none');
            $('#cgst_row, #sgst_row').removeClass('d-none');
        }
        calculateTotal();
    });

    function calculateTotal() {
        let subTotal = 0;
        $('.item-row').each(function() {
            if ($(this).find('.row-select').is(':checked')) {
                subTotal += parseFloat($(this).find('.line_total').val()) || 0;
            }
        });

        let cgst = 0, sgst = 0, igst = 0;
        let taxAmt = 0;
        
        if ($('#state_yes').is(':checked')) {
            let igstPercent = parseFloat($('#igst_percent').val()) || 0;
            igst = subTotal * (igstPercent / 100);
            taxAmt = igst;
            $('#igst_amt_text').text(igst.toFixed(2));
            $('#igst_amt').val(igst.toFixed(2));
        } else {
            let cgstPercent = parseFloat($('#cgst_percent').val()) || 0;
            let sgstPercent = parseFloat($('#sgst_percent').val()) || 0;
            cgst = subTotal * (cgstPercent / 100);
            sgst = subTotal * (sgstPercent / 100);
            taxAmt = cgst + sgst;
            $('#cgst_amt_text').text(cgst.toFixed(2));
            $('#cgst_amt').val(cgst.toFixed(2));
            $('#sgst_amt_text').text(sgst.toFixed(2));
            $('#sgst_amt').val(sgst.toFixed(2));
        }

        let grandTotal = subTotal + taxAmt;

        $('#sub_total_text').text(subTotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#sub_total').val(subTotal.toFixed(2));
        $('#tax_amount_text').text(taxAmt.toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#tax_amount').val(taxAmt.toFixed(2));
        $('#grand_total_text').text(grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#grand_total').val(grandTotal.toFixed(2));
    }
});
</script>
@endsection

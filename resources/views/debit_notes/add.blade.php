@extends('layouts.common')
@section('title', ($debitNote ? 'Edit Debit Note' : 'Add Debit Note') . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="{{ url('debit_notes/add/' . ($debitNote->id ?? '')) }}" method="POST" class="common-form"
                enctype="multipart/form-data" autocomplete="off">
                @csrf
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>{{ $debitNote ? 'Edit' : 'Add' }} Debit Note</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="debit_note_no" name="debit_note_no" class="form-control" placeholder="Enter Debit Note No" value="{{ old('debit_note_no', $debitNote->debit_note_no ?? $nextDebitNoteNo) }}" {{ isset($debitNote) ? 'readonly' : '' }}>
                                    <label for="debit_note_no">Debit Note No <span class="text-danger">*</span></label>
                                </div>
                                @error('debit_note_no') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="debit_note_date" name="debit_note_date" class="form-control date-picker" value="{{ old('debit_note_date', isset($debitNote) ? $debitNote->debit_note_date->format('d-m-Y') : date('d-m-Y')) }}">
                                    <label for="debit_note_date">Debit Note Date <span class="text-danger">*</span></label>
                                </div>
                                @error('debit_note_date') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="purchase_invoice_id" name="purchase_invoice_id" class="form-select select2" data-placeholder="Select Invoice" {{ isset($debitNote) ? 'disabled' : '' }}>
                                        <option value="">Select Invoice</option>
                                        @foreach($purchaseInvoices as $invoice)
                                            <option value="{{ $invoice->id }}" {{ (old('purchase_invoice_id', $debitNote->purchase_invoice_id ?? '') == $invoice->id) ? 'selected' : '' }}>{{ $invoice->invoice_no }}</option>
                                        @endforeach
                                    </select>
                                    @if(isset($debitNote))
                                        <input type="hidden" name="purchase_invoice_id" value="{{ $debitNote->purchase_invoice_id }}">
                                    @endif
                                    <label for="purchase_invoice_id">Select Purchase Invoice <span class="text-danger">*</span></label>
                                </div>
                                @error('purchase_invoice_id') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="hidden" id="supplier_id_hidden" name="supplier_id" value="{{ old('supplier_id', $debitNote->supplier_id ?? '') }}">
                                    <input type="text" id="supplier_name" class="form-control" value="{{ old('supplier_id') ? \App\Models\Supplier::find(old('supplier_id'))?->name : ($debitNote->supplier->name ?? '') }}" readonly>
                                    <label for="supplier_name">Supplier <span class="text-danger">*</span></label>
                                </div>
                                @error('supplier_id') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="reason" name="reason" class="form-control" placeholder="Enter Reason" value="{{ old('reason', $debitNote->reason ?? '') }}">
                                    <label for="reason">Reason for Debit Note</label>
                                </div>
                                @error('reason') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="status" name="status" class="form-select">
                                        <option value="Draft" {{ (old('status', $debitNote->status ?? 'Draft') == 'Draft') ? 'selected' : '' }} {{ (isset($debitNote) && $debitNote->status != 'Draft') ? 'disabled' : '' }}>Draft</option>
                                        <option value="Approved" {{ (old('status', $debitNote->status ?? '') == 'Approved') ? 'selected' : '' }}>Approved</option>
                                        <option value="Cancelled" {{ (old('status', $debitNote->status ?? '') == 'Cancelled') ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                </div>
                                @error('status') <div class="text-danger">{{ $message }}</div> @enderror
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
                                    @if(old('items'))
                                        @foreach(old('items') as $index => $item)
                                            @php
                                            $invItemId = $item['purchase_invoice_item_id'] ?? 0;
                                            $rejectedQty = \App\Models\GrnEntryItem::where('purchase_invoice_item_id', $invItemId)->sum('qty_rejected');
                                            $alreadyDebited = \App\Models\DebitNoteItem::where('purchase_invoice_item_id', $invItemId)
                                                ->when(isset($debitNote), function ($q) use ($debitNote) {
                                                    $q->where('debit_note_id', '!=', $debitNote->id);
                                                })->sum('quantity');
                                            $maxQty = $rejectedQty - $alreadyDebited;
                                            @endphp
                                            <tr class="item-row">
                                                <td>
                                                    <input type="checkbox" name="items[{{ $index }}][selected]" value="1" class="form-check-input item-checkbox" {{ isset($item['selected']) ? 'checked' : '' }}>
                                                    <input type="hidden" name="items[{{ $index }}][purchase_invoice_item_id]" value="{{ $item['purchase_invoice_item_id'] ?? '' }}">
                                                    <input type="hidden" name="items[{{ $index }}][raw_material_id]" value="{{ $item['raw_material_id'] ?? '' }}">
                                                </td>
                                                <td>
                                                    {{ \App\Models\RawMaterial::find($item['raw_material_id'])?->name ?? '-' }}
                                                </td>
                                                <td>
                                                    <input type="hidden" name="items[{{ $index }}][uom_id]" value="{{ $item['uom_id'] ?? '' }}">
                                                    {{ \App\Models\Uom::find($item['uom_id'])?->uom_code ?? '-' }}
                                                </td>
                                                <td>
                                                    <input type="number" name="items[{{ $index }}][quantity]" class="form-control item-qty" value="{{ $item['quantity'] ?? 0 }}" step="0.01" data-max="{{ $maxQty }}">
                                                    @error("items.$index.quantity")
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                    <div class="text-danger small qty-error-msg mt-1" style="display:none;"></div>
                                                </td>

                                                <td>
                                                    <input type="number" name="items[{{ $index }}][rate]" class="form-control item-rate" value="{{ $item['rate'] ?? 0 }}" step="0.01" readonly>
                                                </td>

                                                <td>
                                                    <input type="number" name="items[{{ $index }}][amount]" class="form-control item-amount" value="{{ $item['amount'] ?? 0 }}" step="0.01" readonly>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @elseif(isset($debitNote))
                                        @foreach($debitNote->items as $index => $item)
                                            @php
                                                $rejectedQty = \App\Models\GrnEntryItem::where('purchase_invoice_item_id', $item->purchase_invoice_item_id)->sum('qty_rejected');
                                                $alreadyDebited = \App\Models\DebitNoteItem::where('purchase_invoice_item_id', $item->purchase_invoice_item_id)->where('debit_note_id', '!=', $debitNote->id)->sum('quantity');
                                                $maxQty = $rejectedQty - $alreadyDebited;
                                            @endphp
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
                                                    <input type="number" name="items[{{ $index }}][quantity]" class="form-control item-qty" value="{{ $item->quantity }}" step="0.01" data-max="{{ $maxQty }}">
                                                    @error("items.$index.quantity")
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                    <div class="text-danger small qty-error-msg mt-1" style="display:none;"></div>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[{{ $index }}][rate]" class="form-control item-rate" value="{{ $item->rate }}" step="0.01" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" name="items[{{ $index }}][amount]" class="form-control item-amount" value="{{ $item->amount }}" step="0.01" readonly>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center">No items added yet.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card mb-4" id="tax_charges_card">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Tax & Charges</h4>
                        </div>
                        <div class="row g-4 mb-3">
                            <div class="col-md-6 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <select id="charges_select" class="select2 form-select @error('charges_select') is-invalid @enderror" data-placeholder="Select Charge">
                                        <option value="">Loading charges...</option>
                                    </select>
                                    <label>Charges</label>
                                </div>
                                @error('charges_select')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" min="0" step="0.01" class="form-control @error('charge_amount') is-invalid @enderror" id="charge_amount" placeholder="Charge Amount">
                                    <label>Amount</label>
                                </div>
                                @error('charge_amount')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <select id="charge_tax_type" class="form-select select2">
                                        <option value="Pre-GST">Pre-GST (Taxable)</option>
                                        <option value="Post-GST" selected>Post-GST (Non-Taxable)</option>
                                    </select>
                                    <label>Tax Type</label>
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-2 d-flex align-items-center">
                                <button type="button" id="add_charge_btn" class="btn btn-primary w-100">Add Charge</button>
                            </div>
                        </div>

                        <div class="table-responsive mt-4 {{ (isset($charges) && $charges->count() || (old('charges') && isset(old('charges')['charge_id']))) ? '' : 'd-none' }}"
                            id="charges_table">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Charge Name</th>
                                        <th>Tax Type</th>
                                        <th>Amount</th>
                                        <th width="80px">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="added_charges_list">
                                    @php
                                        $oldCharges = old('charges');
                                        $chargesToLoop = [];

                                        if ($oldCharges && isset($oldCharges['charge_id'])) {
                                            foreach ($oldCharges['charge_id'] as $index => $id) {
                                                $chargesToLoop[] = (object) [
                                                        'charge_id' => $id,
                                                        'charge_name' => $oldCharges['name'][$index] ?? '',
                                                        'charge_amount' => $oldCharges['amount'][$index] ?? 0,
                                                        'tax_type' => $oldCharges['tax_type'][$index] ?? 'Post-GST',
                                                        'id' => null
                                                    ];
                                                }
                                            } else {
                                                $chargesToLoop = $charges ?? [];
                                            }

                                            $preGstTotal = 0;
                                            $postGstTotal = 0;
                                    @endphp

                                    @foreach($chargesToLoop as $charge)
                                        @php
                                            $chargeId = is_array($charge) ? ($charge['charge_id'] ?? '') : $charge->charge_id;
                                            $chargeName = is_array($charge) ? ($charge['name'] ?? '') : ($charge->charge_name ?? $charge->name ?? '');
                                            $chargeAmount = is_array($charge) ? ($charge['amount'] ?? 0) : ($charge->charge_amount ?? $charge->amount ?? 0);
                                            $taxType = is_array($charge) ? ($charge['tax_type'] ?? 'Post-GST') : ($charge->tax_type ?? 'Post-GST');
                                            if ($taxType === 'Pre-GST')
                                                $preGstTotal += $chargeAmount;
                                            else
                                                $postGstTotal += $chargeAmount;
                                        @endphp

                                        <tr class="charge-row" data-charge-id="{{ $chargeId }}" data-tax-type="{{ $taxType }}">
                                            <td>
                                                {{ $chargeName }}
                                                <input type="hidden" name="charges[charge_id][]" value="{{ $chargeId }}">
                                                <input type="hidden" name="charges[name][]" value="{{ $chargeName }}">
                                            </td>
                                            <td>
                                                {{ $taxType }}
                                                <input type="hidden" name="charges[tax_type][]" value="{{ $taxType }}">
                                            </td>
                                            <td>
                                                {{ number_format($chargeAmount, 2) }}
                                                <input type="hidden" name="charges[amount][]" value="{{ $chargeAmount }}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove-charge">X</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="card-header-box mb-3">
                                    <h5 class="mb-0">Additional Information</h5>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-group mb-3">
                                            <textarea class="form-control" id="remarks" name="remarks" placeholder="Remarks">{{ old('remarks', $debitNote->remarks ?? '') }}</textarea>
                                        </div>
                                        @error('remarks') <div class="text-danger small">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating form-floating-outline text-black">
                                            <input type="file" id="reference_document" name="reference_document" class="form-control @error('reference_document') is-invalid @enderror" accept="*">
                                            <label for="reference_document" class="form-label small text-muted">Reference Document / Attachment</label>
                                            <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">Max file size: 2MB. Supported formats: JPG, PNG, JPEG, WEBP, PDF, DOC, DOCX</small>
                                            @error('reference_document') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                            @if(isset($debitNote) && !empty($debitNote->reference_document))
                                                <div class="mt-2 preview-container">
                                                    @php
                                                        $attachment = $debitNote->reference_document;
                                                        $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                                        $url = url('uploads/debit_notes/' . $attachment);
                                                    @endphp

                                                    <div class="attachment-thumb border rounded p-1 bg-white shadow-sm position-relative" style="width: 100px; height: 100px;" title="{{ $attachment }}">
                                                        @if($isImage)
                                                            <img src="{{ $url }}" class="w-100 h-100 object-fit-cover rounded cursor-pointer view-image" data-image="{{ $url }}" alt="Reference">
                                                        @else
                                                            <a href="{{ $url }}" target="_blank" class="w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-light rounded text-decoration-none shadow-none text-primary"><i class="ri ri-file-text-line fs-2"></i><span class="badge bg-primary text-white mt-1" style="font-size: 10px;">{{ strtoupper($extension) }}</span></a>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <div class="mt-2 preview-container"></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: Tax Summary -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0">Tax Summary</h5>
                                    <div class="d-flex gap-3 align-items-center">
                                        <label class="small mb-0">Other State?</label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check m-0">
                                                <input class="form-check-input" type="radio" name="other_state" id="other_state_yes" value="Y" {{ (old('other_state', $debitNote->other_state ?? '') == 'Y') ? 'checked' : '' }} onclick="return false;">
                                                <label class="form-check-label small" for="other_state_yes">Yes</label>
                                            </div>
                                            <div class="form-check m-0">
                                                <input class="form-check-input" type="radio" name="other_state" id="other_state_no" value="N" {{ (old('other_state', $debitNote->other_state ?? 'N') == 'N') ? 'checked' : '' }} onclick="return false;">
                                                <label class="form-check-label small" for="other_state_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-3">
                                    <label class="text-muted">Sub total:</label>
                                    <div class="text-end">
                                        <input type="hidden" id="sub_total" name="sub_total" value="{{ old('sub_total', $debitNote->sub_total ?? '0.00') }}">
                                        <span id="sub_total_display" class="fw-bold">₹{{ number_format(old('sub_total', $debitNote->sub_total ?? 0), 2) }}</span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-3" id="pre_gst_charges_div" style="{{ $preGstTotal > 0 ? '' : 'display: none;' }}">
                                    <label class="text-muted">Pre-GST Charges:</label>
                                    <div class="text-end">
                                        <input type="hidden" id="pre_gst_total" name="pre_gst_total" value="{{ $preGstTotal }}">
                                        <span id="pre_gst_total_display" class="fw-bold">₹{{ number_format($preGstTotal, 2) }}</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <label class="text-muted">Taxable Total:</label>
                                    <div class="text-end">
                                        @php $taxableAmt = old('sub_total', $debitNote->sub_total ?? 0) + $preGstTotal; @endphp
                                        <input type="hidden" id="taxable_amount" name="taxable_amount" value="{{ $taxableAmt }}">
                                        <span id="taxable_amount_display" class="fw-bold">₹{{ number_format($taxableAmt, 2) }}</span>
                                    </div>
                                </div>

                                <div id="igst_div" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="text-muted">IGST:</label>
                                        <div class="d-flex gap-2 align-items-center">
                                            <input type="number" name="igst_percent" id="igst_percent" value="{{ old('igst_percent', $debitNote->igst_percent ?? $debitNote->purchaseInvoice->igst_percent ?? $web_settings->igst ?? 0) }}" class="form-control form-control-sm text-end" style="width: 85px;" step="0.01">
                                            <span class="small">%</span>
                                            <strong id="igst_amt" class="ms-2">₹0.00</strong>
                                        </div>
                                    </div>
                                </div>

                                <div id="cgst_sgst_div" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="text-muted">CGST:</label>
                                        <div class="d-flex gap-2 align-items-center">
                                            <input type="number" name="cgst_percent" id="cgst_percent" value="{{ old('cgst_percent', $debitNote->cgst_percent ?? $debitNote->purchaseInvoice->cgst_percent ?? $web_settings->cgst ?? 0) }}" class="form-control form-control-sm text-end" style="width: 85px;" step="0.01">
                                            <span class="small">%</span>
                                            <strong id="cgst_amt" class="ms-2">₹0.00</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="text-muted">SGST:</label>
                                        <div class="d-flex gap-2 align-items-center">
                                            <input type="number" name="sgst_percent" id="sgst_percent" value="{{ old('sgst_percent', $debitNote->sgst_percent ?? $debitNote->purchaseInvoice->sgst_percent ?? $web_settings->sgst ?? 0) }}" class="form-control form-control-sm text-end" style="width: 85px;" step="0.01">
                                            <span class="small">%</span>
                                            <strong id="sgst_amt" class="ms-2">₹0.00</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-3 mt-4">
                                    <label class="text-muted">Tax Amount:</label>
                                    <div class="text-end">
                                        <input type="hidden" id="tax_amount" name="tax_amount" value="{{ old('tax_amount', $debitNote->tax_amount ?? '0.00') }}">
                                        <span id="tax_amount_display" class="fw-bold">₹{{ number_format(old('tax_amount', $debitNote->tax_amount ?? 0), 2) }}</span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-3" id="post_gst_charges_div" style="{{ $postGstTotal > 0 ? '' : 'display: none;' }}">
                                    <label class="text-muted">Post-GST Charges:</label>
                                    <div class="text-end">
                                        <input type="hidden" id="post_gst_total" name="post_gst_total" value="{{ $postGstTotal }}">
                                        <span id="post_gst_total_display" class="fw-bold">₹{{ number_format($postGstTotal, 2) }}</span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="text-muted">Round Off:</label>
                                    <div class="d-flex align-items-center">
                                        <div class="form-check form-check-inline me-2 m-0 mt-1">
                                            <input class="form-check-input" type="radio" name="round_off_type" id="round_off_add" value="Add" {{ old('round_off_type', $debitNote->round_off_type ?? 'Add') == 'Add' ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="round_off_add">Add</label>
                                        </div>
                                        <div class="form-check form-check-inline me-2 m-0 mt-1">
                                            <input class="form-check-input" type="radio" name="round_off_type" id="round_off_less" value="Less" {{ old('round_off_type', $debitNote->round_off_type ?? 'Add') == 'Less' ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="round_off_less">Less</label>
                                        </div>
                                        <input type="number" class="form-control form-control-sm text-end" style="width: 100px;" id="round_off" name="round_off" step="0.01" min="0" value="{{ old('round_off', $debitNote->round_off ?? '0.00') }}" autocomplete="off">
                                    </div>
                                </div>

                                <hr class="my-3">

                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="fw-bold" style="font-size: 1.1rem;">Grand Total:</label>
                                    <div class="text-end">
                                        <input type="hidden" id="grand_total" name="grand_total" value="{{ old('grand_total', $debitNote->grand_total ?? '0.00') }}">
                                        <span id="grand_total_display" class="fw-bold" style="font-size: 1.5rem; color: #28a745;">₹{{ number_format(old('grand_total', $debitNote->grand_total ?? 0), 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mb-5 me-4 mt-5">
                    <button type="submit" class="btn btn-primary">Submit</button>
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

    $(document).ready(function () {
        $.get('{{ url('get_charges') }}', function (data) {
            let select = $('#charges_select');
            select.empty();
            select.append('<option value="">Select Charge</option>');
            $.each(data, function (key, value) {
                select.append('<option value="' + value.id + '">' + value.charge_name + '</option>');
            });
        });

        $('#add_charge_btn').click(function () {
            let chargeId = $('#charges_select').val();
            let chargeName = $('#charges_select option:selected').text();
            let chargeAmount = parseFloat($('#charge_amount').val());
            let taxType = $('#charge_tax_type').val();

            if (!chargeId || isNaN(chargeAmount) || chargeAmount <= 0) {
                alert('Please select a charge and enter a valid amount greater than 0.');
                return;
            }

            let existingRow = $('#added_charges_list').find('tr[data-charge-id="' + chargeId + '"]');
            if (existingRow.length > 0) {
                alert('This charge is already added.');
                return;
            }

            let newRow = `
                <tr class="charge-row" data-charge-id="${chargeId}" data-tax-type="${taxType}">
                    <td>
                        ${chargeName}
                        <input type="hidden" name="charges[charge_id][]" value="${chargeId}">
                        <input type="hidden" name="charges[name][]" value="${chargeName}">
                    </td>
                    <td>
                        ${taxType}
                        <input type="hidden" name="charges[tax_type][]" value="${taxType}">
                    </td>
                    <td>
                        ${chargeAmount.toFixed(2)}
                        <input type="hidden" name="charges[amount][]" value="${chargeAmount.toFixed(2)}">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-charge">X</button>
                    </td>
                </tr>
            `;

            $('#added_charges_list').append(newRow);
            $('#charges_table').removeClass('d-none');

            $('#charges_select').val('').trigger('change');
            $('#charge_amount').val('');

            calculateTotals();
        });

        $(document).on('click', '.remove-charge', function () {
            $(this).closest('tr').remove();
            if ($('#added_charges_list tr').length === 0) {
                $('#charges_table').addClass('d-none');
            }
            calculateTotals();
        });
        $('#purchase_invoice_id').on('change', function () {
            let invoiceId = $(this).val();
            if (invoiceId) {
                $.get("{{ url('debit_notes/get-invoice-details') }}/" + invoiceId, function (res) {
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
                                        <input type="number" name="items[${index}][quantity]" class="form-control item-qty" value="${item.quantity}" step="0.01" data-max="${item.max_quantity}">
                                        <div class="text-danger small qty-error-msg mt-1" style="display:none;"></div>
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

        $('input[name="other_state"]').on('change', function () {
            if ($(this).val() === 'Y') {
                if (parseFloat($('#igst_percent').val()) == 0) {
                    $('#igst_percent').val("{{ $web_settings->igst }}");
                }
            } else {
                if (parseFloat($('#cgst_percent').val()) == 0) {
                    $('#cgst_percent').val("{{ $web_settings->cgst }}");
                }
                if (parseFloat($('#sgst_percent').val()) == 0) {
                    $('#sgst_percent').val("{{ $web_settings->sgst }}");
                }
            }
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

        $(document).on('input', '.item-qty, #igst_percent, #cgst_percent, #sgst_percent', function () {
            let row = $(this).closest('tr');
            if (row.hasClass('item-row')) {
                let qty = parseFloat(row.find('.item-qty').val()) || 0;
                let rate = parseFloat(row.find('.item-rate').val()) || 0;
                row.find('.item-amount').val((qty * rate).toFixed(2));
            }
            calculateTotals();
        });

        $(document).on('change', '.item-checkbox, input[name="round_off_type"]', function () {
            calculateTotals();
        });

        $(document).on('input', '#round_off', function () {
            calculateTotals();
        });

        function calculateTotals() {
            let subTotal = 0;
            $('.item-row').each(function () {
                if ($(this).find('.item-checkbox').is(':checked')) {
                    subTotal += parseFloat($(this).find('.item-amount').val()) || 0;
                }
            });

            $('#sub_total').val(subTotal.toFixed(2));
            $('#sub_total_display').text('₹' + subTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

            let preGstTotal = 0;
            let postGstTotal = 0;
            $('#added_charges_list tr').each(function () {
                let amount = parseFloat($(this).find('input[name="charges[amount][]"]').val()) || 0;
                let taxType = $(this).find('input[name="charges[tax_type][]"]').val();
                if (taxType === 'Pre-GST') {
                    preGstTotal += amount;
                } else {
                    postGstTotal += amount;
                }
            });

            $('#pre_gst_total').val(preGstTotal.toFixed(2));
            $('#pre_gst_total_display').text('₹' + preGstTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            if (preGstTotal > 0) {
                $('#pre_gst_charges_div').show();
            } else {
                $('#pre_gst_charges_div').hide();
            }

            let taxableAmount = subTotal + preGstTotal;
            $('#taxable_amount').val(taxableAmount.toFixed(2));
            $('#taxable_amount_display').text('₹' + taxableAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

            $('#post_gst_total').val(postGstTotal.toFixed(2));
            $('#post_gst_total_display').text('₹' + postGstTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            if (postGstTotal > 0) {
                $('#post_gst_charges_div').show();
            } else {
                $('#post_gst_charges_div').hide();
            }

            let otherState = $('input[name="other_state"]:checked').val();
            let taxAmount = 0;

            if (otherState === 'Y') {
                let igstPercent = parseFloat($('#igst_percent').val()) || 0;
                let igstAmt = taxableAmount * (igstPercent / 100);
                $('#igst_amt').text('₹' + igstAmt.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                taxAmount = igstAmt;
            } else {
                let cgstPercent = parseFloat($('#cgst_percent').val()) || 0;
                let sgstPercent = parseFloat($('#sgst_percent').val()) || 0;
                let cgstAmt = taxableAmount * (cgstPercent / 100);
                let sgstAmt = taxableAmount * (sgstPercent / 100);
                $('#cgst_amt').text('₹' + cgstAmt.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                $('#sgst_amt').text('₹' + sgstAmt.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                taxAmount = cgstAmt + sgstAmt;
            }

            $('#tax_amount').val(taxAmount.toFixed(2));
            $('#tax_amount_display').text('₹' + taxAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

            let totalBeforeRoundOff = taxableAmount + taxAmount + postGstTotal;

            let roundOffAmount = parseFloat($('#round_off').val()) || 0;
            let roundOffType = $('input[name="round_off_type"]:checked').val() || 'Add';
            let grandTotal = 0;

            if (roundOffType === 'Add') {
                grandTotal = totalBeforeRoundOff + roundOffAmount;
            } else {
                grandTotal = totalBeforeRoundOff - roundOffAmount;
            }

            if ($('#hidden_other_charges').length === 0) {
                $('#tax_charges_card').append('<input type="hidden" id="hidden_other_charges" name="other_charges" value="0">');
            }
            $('#hidden_other_charges').val((preGstTotal + postGstTotal).toFixed(2));

            $('#grand_total').val(grandTotal.toFixed(2));
            $('#grand_total_display').text('₹' + grandTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        }

        @if(isset($debitNote) || old('other_state'))
            toggleTaxDivs();
            calculateTotals();
        @endif

        if (!$('input[name="other_state"]:checked').length) {
            $('#other_state_no').prop('checked', true);
        }
        $(document).on('input', '.item-qty', function () {
            let qty = parseFloat($(this).val()) || 0;
            let max = parseFloat($(this).attr('data-max')) || 0;
            let errorMsg = $(this).closest('td').find('.qty-error-msg');
            let parentRow = $(this).closest('tr');

            if (qty > max) {
                errorMsg.text('Quantity exceeds available rejected quantity (' + max + ')').show();
                $(this).addClass('is-invalid');
            } else {
                errorMsg.hide();
                $(this).removeClass('is-invalid');
            }
            calculateTotals();
        });

        toggleTaxDivs();
    });
</script>

@endsection
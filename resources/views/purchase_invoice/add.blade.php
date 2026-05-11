@extends('layouts.common')
@section('title', ($invoice ? 'Edit' : 'Add') . ' Purchase Invoice - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            @include('flash_messages')
        </div>
        <div class="col-lg-12">
            <form action="{{ $invoice ? url('purchase_invoices/add/' . $invoice->id) : url('purchase_invoices/add') }}"
                method="POST" enctype="multipart/form-data" class="common-form" autocomplete="off">
                @csrf
                <input type="hidden" id="isEditMode" value="{{ isset($invoice) ? 1 : 0 }}">
                <input type="hidden" name="purchase_commission_agent_id" id="purchase_commission_agent_id"
                    value="{{ old('purchase_commission_agent_id', $invoice->purchase_commission_agent_id ?? '') }}">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>{{ $invoice ? 'Edit' : 'Add' }} Purchase Invoice</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('invoice_no') is-invalid @enderror" id="invoice_no" placeholder="Enter Invoice No" name="invoice_no" value="{{ old('invoice_no', $invoice->invoice_no ?? $nextInvoiceNumber ?? '') }}" {{ isset($invoice) ? 'readonly' : '' }}>
                                    <label for="invoice_no">Invoice No. <span class="text-danger">*</span></label>
                                </div>
                                @error('invoice_no')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control invoice_date @error('invoice_date') is-invalid @enderror" placeholder="Enter Invoice Date" name="invoice_date" autocomplete="off" value="{{ old('invoice_date', $invoice ? $invoice->invoice_date->format('d-m-Y') : '') }}" {{ isset($invoice) ? 'readonly' : '' }} />
                                    <label for="invoice_date">Invoice Date <span class="text-danger">*</span></label>
                                </div>
                                @error('invoice_date')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="purchase_order_id" id="purchase_order" class="form-select select2 @error('purchase_order_id') is-invalid @enderror" data-placeholder="Select Purchase Order" {{ isset($invoice) ? 'disabled' : '' }}>
                                        <option value="">Select Purchase Order</option>
                                        @foreach($purchaseOrders as $po)
                                            <option value="{{ $po->id }}" {{ old('purchase_order_id', $invoice->purchase_order_id ?? '') == $po->id ? 'selected' : '' }}>{{ $po->po_number }} - {{ $po->supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if(isset($invoice))
                                        <input type="hidden" name="purchase_order_id" value="{{ $invoice->purchase_order_id }}">
                                    @endif
                                    <label for="purchase_order">Purchase Order No <span
                                            class="text-danger">*</span></label>
                                </div>
                                @error('purchase_order_id')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('supplier_name') is-invalid @enderror" id="supplier_name" readonly value="{{ old('supplier_name', $invoice->supplier->name ?? '') }}">
                                    <input type="hidden" name="supplier_name" id="supplier_name_hidden" value="{{ old('supplier_name', $invoice->supplier->name ?? '') }}">
                                    <input type="hidden" name="supplier_id" id="supplier_id" value="{{ old('supplier_id', $invoice->supplier_id ?? '') }}">
                                    <label for="supplier_name">Supplier <span class="text-danger">*</span></label>
                                </div>
                                @error('supplier_id')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('po_reference') is-invalid @enderror" id="po_reference" placeholder="Enter PO Reference" name="po_reference" value="{{ old('po_reference', $invoice->po_reference ?? '') }}">
                                    <label for="po_reference">PO Reference</label>
                                </div>
                                @error('po_reference')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="purchase_commission_agent_name" placeholder="Commission Agent" readonly value="{{ old('purchase_commission_agent_name', $invoice->purchaseCommissionAgent->name ?? '') }}">
                                    <label for="purchase_commission_agent_name">Purchase Commission Agent</label>
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('transport') is-invalid @enderror" id="transport" placeholder="Enter Transport" name="transport" value="{{ old('transport', $invoice->transport ?? '') }}">
                                    <label for="transport">Transport</label>
                                </div>
                                @error('transport')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('destination') is-invalid @enderror" id="destination" placeholder="Enter Destination" name="destination" value="{{ old('destination', $invoice->destination ?? '') }}">
                                    <label for="destination">Destination</label>
                                </div>
                                @error('destination')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('lr_no') is-invalid @enderror" id="lr_no" placeholder="Enter LR No" name="lr_no" value="{{ old('lr_no', $invoice->lr_no ?? '') }}">
                                    <label for="lr_no">LR No</label>
                                </div>
                                @error('lr_no')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control date-picker @error('lr_date') is-invalid @enderror" placeholder="Enter LR Date" name="lr_date" autocomplete="off" value="{{ old('lr_date', $invoice && $invoice->lr_date ? $invoice->lr_date->format('d-m-Y') : '') }}" />
                                    <label for="lr_date">LR Date</label>
                                </div>
                                @error('lr_date')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('eway_billno') is-invalid @enderror" id="eway_billno" placeholder="Enter Eway Bill No" name="eway_billno" value="{{ old('eway_billno', $invoice->eway_billno ?? '') }}">
                                    <label for="eway_billno">Eway Bill No</label>
                                </div>
                                @error('eway_billno')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('indent_no') is-invalid @enderror" id="indent_no" placeholder="Enter Indent No" name="indent_no" value="{{ old('indent_no', $invoice->indent_no ?? '') }}">
                                    <label for="indent_no">Indent No</label>
                                </div>
                                @error('indent_no')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control date-picker @error('indent_date') is-invalid @enderror" placeholder="Enter Indent Date" name="indent_date" autocomplete="off" value="{{ old('indent_date', $invoice && $invoice->indent_date ? $invoice->indent_date->format('d-m-Y') : '') }}" />
                                    <label for="indent_date">Indent Date</label>
                                </div>
                                @error('indent_date')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Item Details</h4>
                        </div>
                        @error('items')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                        <div id="item-rows" class="table-responsive purchase-items-scroll">
                            <table class="table table-bordered align-middle purchase-items-table">
                                <thead>
                                    <tr>
                                        <th width="50px">
                                            <input type="checkbox" id="select_all_items" class="form-check-input">
                                        </th>
                                        <th>Store Category</th>
                                        <th>Raw Material</th>
                                        <th>Supplier Design Name</th>
                                        <th>Brand</th>
                                        <th class="hsn-column">HSN Code</th>
                                        <th class="col-fabric">Fabric Width</th>
                                        <th class="col-fabric">Fabric Type</th>
                                        <th>Ordered Qty</th>
                                        <th>Balanced Qty</th>
                                        <th class="invoice-qty-column">Invoiced Qty <span class="text-danger">*</span></th>
                                        <th>UOM</th>
                                        <th>Rate</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>

                                <tbody id="items_tbody">
                                    @if(old('items'))
                                        @foreach(old('items') as $index => $item)
                                            <tr class="item-row">
                                                <td>
                                                    <input type="checkbox" name="items[{{ $index }}][selected]" value="1" class="form-check-input item-checkbox" {{ isset($item['selected']) ? 'checked' : '' }}>
                                                    <input type="hidden" name="items[{{ $index }}][purchase_order_item_id]" value="{{ $item['purchase_order_item_id'] ?? '' }}">
                                                    <input type="hidden" name="items[{{ $index }}][raw_material_id]" value="{{ $item['raw_material_id'] ?? '' }}">
                                                    <input type="hidden" name="items[{{ $index }}][raw_material_name]" value="{{ $item['raw_material_name'] ?? '' }}">
                                                    <input type="hidden" name="items[{{ $index }}][uom_id]" value="{{ $item['uom_id'] ?? '' }}">
                                                    <input type="hidden" name="items[{{ $index }}][uom_code]" value="{{ $item['uom_code'] ?? '' }}">
                                                    <input type="hidden" name="items[{{ $index }}][rate]" value="{{ $item['rate'] ?? 0 }}" class="item-rate">
                                                    <input type="hidden" name="items[{{ $index }}][qty_ordered]" value="{{ $item['qty_ordered'] ?? 0 }}" class="qty-ordered-val">
                                                    <input type="hidden" name="items[{{ $index }}][qty_invoiced]" value="{{ $item['qty_invoiced'] ?? 0 }}" class="qty-invoiced-val">
                                                    <input type="hidden" name="items[{{ $index }}][fabric_type_name]" value="{{ $item['fabric_type_name'] ?? '-' }}">
                                                    <input type="hidden" name="items[{{ $index }}][store_category_id]" value="{{ $item['store_category_id'] ?? 0 }}">
                                                </td>
                                                <td>{{ $item['store_category_name'] ?? '-' }}</td>
                                                <td>{{ $item['raw_material_name'] ?? '-' }}</td>
                                                <td>{{ $item['art_no'] ?? '-' }}</td>
                                                <td>
                                                    <select name="items[{{ $index }}][brand_id]" class="select2 form-select form-select-sm">
                                                        <option value="">Select Brand</option>
                                                        @foreach($brands as $brand)
                                                            <option value="{{ $brand->id }}" {{ ($item['brand_id'] ?? '') == $brand->id ? 'selected' : '' }}>
                                                                {{ $brand->brand_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="items[{{ $index }}][hsn_code]" class="form-control form-control-sm item-hsn @error('items.' . $index . '.hsn_code') is-invalid @enderror" value="{{ $item['hsn_code'] ?? '' }}" {{ isset($item['selected']) ? '' : 'readonly' }}>
                                                    @error('items.' . $index . '.hsn_code')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td class="fabric-only-cell">
                                                    <select name="items[{{ $index }}][fabric_width_id]" class="select2 form-select form-select-sm">
                                                        <option value="">Select Width</option>
                                                        @foreach($fabricSizes as $size)
                                                            <option value="{{ $size->id }}" {{ ($item['fabric_width_id'] ?? '') == $size->id ? 'selected' : '' }}>
                                                                {{ $size->width }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="fabric-only-cell">{{ $item['fabric_type_name'] ?? '-' }}</td>
                                                <td class="qty-ordered-display">{{ $item['qty_ordered'] ?? 0 }}</td>
                                                <td class="balanced-qty-display">
                                                    {{ ($item['qty_ordered'] ?? 0) - ($item['qty_invoiced'] ?? 0) }}
                                                </td>
                                                
                                                <td class="invoice-qty-column">
                                                    <input type="number" name="items[{{ $index }}][quantity]"
                                                        class="form-control form-control-sm item-quantity received-qty-input @error('items.' . $index . '.quantity') is-invalid @enderror"
                                                        value="{{ $item['quantity'] ?? '' }}" step="0.01"
                                                        data-max-qty="{{ ($item['qty_ordered'] ?? 0) - ($item['qty_invoiced'] ?? 0) }}"
                                                        {{ isset($item['selected']) ? '' : 'readonly' }}>

                                                    <small class="text-secondary">
                                                        Note: Invoiced quantity should not exceed 50% of ordered quantity.
                                                    </small>

                                                    @error("items.$index.quantity")
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>{{ $item['uom_code'] ?? '-' }}</td>
                                                <td class="rate-display">{{ number_format($item['rate'] ?? 0, 2) }}</td>
                                                <td class="item-amount">
                                                    {{ number_format(($item['quantity'] ?? 0) * ($item['rate'] ?? 0), 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @elseif(isset($invoice) && $invoice && $invoice->items->count())
                                        @foreach($invoice->items as $index => $invItem)
                                            @php
                                                $balancedQty = $invItem->qty_ordered - $invItem->qty_invoiced;
                                            @endphp
                                            <tr class="item-row">
                                                <td>
                                                    <input type="checkbox" name="items[{{ $index }}][selected]" value="1" class="form-check-input item-checkbox" checked>
                                                    <input type="hidden" name="items[{{ $index }}][purchase_order_item_id]" value="{{ $invItem->purchase_order_item_id }}">
                                                    <input type="hidden" name="items[{{ $index }}][raw_material_id]" value="{{ $invItem->raw_material_id }}">
                                                    <input type="hidden" name="items[{{ $index }}][raw_material_name]" value="{{ $invItem->rawMaterial->name ?? '' }}">
                                                    <input type="hidden" name="items[{{ $index }}][uom_id]" value="{{ $invItem->uom_id }}">
                                                    <input type="hidden" name="items[{{ $index }}][uom_code]" value="{{ $invItem->uom->uom_code ?? '' }}">
                                                    <input type="hidden" name="items[{{ $index }}][rate]" value="{{ $invItem->rate }}" class="item-rate">
                                                    <input type="hidden" name="items[{{ $index }}][qty_ordered]" value="{{ $invItem->qty_ordered }}" class="qty-ordered-val">
                                                    <input type="hidden" name="items[{{ $index }}][qty_invoiced]" value="{{ $invItem->qty_invoiced }}" class="qty-invoiced-val">
                                                    <input type="hidden" name="items[{{ $index }}][store_category_name]" value="{{ $invItem->purchaseOrderItem->storeCategory->category_name ?? '-' }}">
                                                    <input type="hidden" name="items[{{ $index }}][brand_name]" value="{{ $invItem->brand->brand_name ?? $invItem->purchaseOrderItem->brand->brand_name ?? '-' }}">
                                                    <input type="hidden" name="items[{{ $index }}][fabric_width]" value="{{ $invItem->fabricWidth->width ?? $invItem->purchaseOrderItem->fabricWidth->width ?? '-' }}">
                                                    <input type="hidden" name="items[{{ $index }}][fabric_type_name]" value="{{ $invItem->purchaseOrderItem->fabricType->fabric_type ?? '-' }}">
                                                    <input type="hidden" name="items[{{ $index }}][store_category_id]" value="{{ $invItem->purchaseOrderItem->store_category_id ?? 0 }}">
                                                </td>

                                                <td>{{ $invItem->purchaseOrderItem->storeCategory->category_name ?? '-' }}</td>
                                                <td>{{ $invItem->rawMaterial->name ?? '-' }}</td>
                                                <td>{{ $invItem->purchaseOrderItem->supplier_design_name ?? '-' }}</td>
                                                <td>
                                                    <select name="items[{{ $index }}][brand_id]" class="select2 form-select form-select-sm">
                                                        <option value="">Select Brand</option>
                                                        @foreach($brands as $brand)
                                                            @php
        $selectedBrandId = $invItem->brand_id ?? $invItem->purchaseOrderItem->brand_id ?? null;
                                                            @endphp
                                                            <option value="{{ $brand->id }}" {{ $selectedBrandId == $brand->id ? 'selected' : '' }}>
                                                                {{ $brand->brand_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>

                                                <td>
                                                    <input type="text" name="items[{{ $index }}][hsn_code]" class="form-control form-control-sm item-hsn @error('items.' . $index . '.hsn_code') is-invalid @enderror" value="{{ $invItem->hsn_code }}">
                                                    @error('items.' . $index . '.hsn_code')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </td>

                                                <td class="fabric-only-cell">
                                                    <select name="items[{{ $index }}][fabric_width_id]" class="select2 form-select form-select-sm">
                                                        <option value="">Select Width</option>
                                                        @foreach($fabricSizes as $size)
                                                            @php
                                                                $selectedWidthId = $invItem->fabric_width_id ?? $invItem->purchaseOrderItem->fabric_width_id ?? null;
                                                            @endphp
                                                            <option value="{{ $size->id }}" {{ $selectedWidthId == $size->id ? 'selected' : '' }}>
                                                                {{ $size->width }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>

                                                <td class="fabric-only-cell">{{ $invItem->purchaseOrderItem->fabricType->fabric_type ?? '-' }}</td>

                                                <td class="qty-ordered-display">{{ $invItem->qty_ordered }}</td>
                                                <td class="balanced-qty-display">{{ $balancedQty }}</td>

                                                <td class="invoice-qty-column">
                                                    <input type="number" name="items[{{ $index }}][quantity]" class="form-control form-control-sm item-quantity received-qty-input @error('items.' . $index . '.quantity') is-invalid @enderror" value="{{ $invItem->quantity }}" step="0.01" data-max-qty="{{ $balancedQty }}">
                                                    <small class="text-secondary">
                                                        Note: Invoiced quantity can exceed ordered quantity by up to 50% (Max:
                                                        {{ number_format($invItem->qty_ordered * 1.5, 2) }}).
                                                    </small>
                                                    @error('items.' . $index . '.quantity')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </td>

                                                <td>{{ $invItem->uom->uom_code ?? '-' }}</td>
                                                <td class="rate-display">{{ number_format($invItem->rate, 2) }}</td>
                                                <td class="item-amount">
                                                    {{ number_format($invItem->quantity * $invItem->rate, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="13" class="text-center text-muted">
                                                Please select a Purchase Order to load items
                                            </td>
                                        </tr>
                                    @endif

                                </tbody>


                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Tax & Charges</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <select id="charges_select" class="select2 form-select @error('charges_select') is-invalid @enderror" data-placeholder="Select Charge">
                                        <option value="">Loading charges...</option>
                                    </select>
                                    <label>Charges <span class="text-danger">*</span></label>
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
                        <div class="table-responsive mt-4 {{ ($charges->count() || (old('charges') && isset(old('charges')['charge_id']))) ? '' : 'd-none' }}"
                            id="charges_table">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Charge Name</th>
                                        <th>Tax Type</th>
                                        <th>Amount</th>
                                        <th width="120px">Action</th>
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
        'id' => null
    ];
}
} else {
$chargesToLoop = $charges;
}
                                    @endphp

                                    @foreach($chargesToLoop as $charge)
                                        @php
$chargeId = is_array($charge) ? ($charge['charge_id'] ?? '') : $charge->charge_id;
$chargeName = is_array($charge) ? ($charge['name'] ?? '') : ($charge->charge_name ?? $charge->name ?? '');
$chargeAmount = is_array($charge) ? ($charge['amount'] ?? 0) : ($charge->charge_amount ?? $charge->amount ?? 0);
$taxType = is_array($charge) ? ($charge['tax_type'] ?? 'Post-GST') : ($charge->tax_type ?? 'Post-GST');
$invoiceChargeId = is_array($charge) ? ($charge['id'] ?? null) : ($charge->id ?? null);
                                        @endphp

                                        <tr class="charge-row" data-charge-id="{{ $chargeId }}"
                                            data-invoice-charge-id="{{ $invoiceChargeId }}" data-tax-type="{{ $taxType }}">
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
                                            <td class="d-flex align-items-center">
                                                <button type="button" class="btn btn-outline-success btn-sm edit-charge me-1" title="Edit Charge">
                                                    <i class="ri ri-pencil-line"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-charge" title="Delete Charge" {{ isset($invoice) ? 'disabled' : '' }}>
                                                    <i class="ri ri-delete-bin-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row g-4 align-items-start">
                            <div class="col-lg-6">
                                <div class="card p-3 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="mb-3 fw-semibold">Invoice Details</h5>
                                        <div class="form-floating form-floating-outline mb-2">
                                            @php
$currentStatus = old('invoice_status', $invoice->invoice_status ?? '');
                                            @endphp

                                            <select id="invoice_status" name="invoice_status"
                                                class="select2 form-select @error('invoice_status') is-invalid @enderror"
                                                data-placeholder="Select Invoice Status">
                                                <option value="">Select Invoice Status</option>
                                                @foreach (['Draft', 'Unpaid/Credit', 'Partially Paid', 'Paid'] as $status)
                                                    @php
$disabled = false;
if ($currentStatus === 'Unpaid/Credit') {
    $disabled = ($status === 'Draft');
}
if ($currentStatus === 'Partially Paid') {
    $disabled = in_array($status, ['Draft', 'Unpaid/Credit']);
}
if ($currentStatus === 'Paid') {
    $disabled = ($status !== 'Paid');
}
                                                    @endphp
                                                    <option value="{{ $status }}" {{ $currentStatus === $status ? 'selected' : '' }} {{ $disabled ? 'disabled' : '' }}>
                                                        {{ $status }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label for="invoice_status">Invoice Status <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        @error('invoice_status')
                                            <div class="text-danger mb-3">{{ $message }}</div>
                                        @enderror

                                        <div class="form-floating form-floating-outline mb-3">
                                            <select id="payment_mode" name="payment_mode" class="select2 form-select @error('payment_mode') is-invalid @enderror" data-placeholder="Select Payment Mode">
                                                <option value="">Select Payment Mode</option>
                                                <option value="Bank Transfer" {{ old('payment_mode', $invoice->payment_mode ?? '') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                                <option value="Cheque" {{ old('payment_mode', $invoice->payment_mode ?? '') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                                <option value="UPI" {{ old('payment_mode', $invoice->payment_mode ?? '') == 'UPI' ? 'selected' : '' }}>UPI</option>
                                                <option value="Cash" {{ old('payment_mode', $invoice->payment_mode ?? '') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                            </select>
                                            <label for="payment_mode">Payment Mode</label>
                                        </div>
                                        @error('payment_mode')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror

                                        <div class="form-floating form-floating-outline mb-3 d-none" id="transaction_id_div">
                                            <input type="text" name="transaction_id" id="transaction_id" class="form-control @error('transaction_id') is-invalid @enderror" placeholder="Enter details" value="{{ old('transaction_id', $invoice->transaction_id ?? '') }}">
                                            <label for="transaction_id" id="transaction_id_label">Transaction Details</label>
                                        </div>
                                        @error('transaction_id')
                                            <div class="text-danger mt-1 mb-3">{{ $message }}</div>
                                        @enderror

                                        <div class="form-floating form-floating-outline mb-3">
                                            <input type="text" class="form-control due_date @error('due_date') is-invalid @enderror" placeholder="Enter Due Date" name="due_date" autocomplete="off" value="{{ old('due_date', $invoice && $invoice->due_date ? $invoice->due_date->format('d-m-Y') : '') }}" />
                                            <label for="due_date">Due Date</label>
                                        </div>
                                        @error('due_date')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror

                                        <div class="form-floating form-floating-outline mb-3">
                                            <textarea name="notes" id="notes" class="form-control h-px-100 @error('notes') is-invalid @enderror" placeholder="Enter Additional Notes">{{ old('notes', $invoice->notes ?? '') }}</textarea>
                                            <label for="notes">Additional Notes</label>
                                        </div>
                                        @error('notes')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror

                                        <div class="mb-3">
                                            <div class="form-floating form-floating-outline text-black">
                                                <input type="file" class="form-control @error('auth_sign') is-invalid @enderror" id="auth_sign" name="auth_sign" accept="*">
                                                <label for="auth_sign">Authorized Signature / Stamp Upload</label>
                                                @error('auth_sign')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted d-block mt-1">Max file size: 2MB. Supported
                                                    formats: JPG, PNG, JPEG, WEBP, PDF, DOC, DOCX</small>
                                                @if(!empty($invoice->auth_signature))
                                                    <div class="mt-2 preview-container">
                                                        @php
$attachment = $invoice->auth_signature;
$extension = pathinfo($attachment, PATHINFO_EXTENSION);
$isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
$url = url('uploads/purchase_invoices/' . $invoice->auth_signature);
                                                        @endphp

                                                        <div class="attachment-thumb border rounded p-1 bg-white shadow-sm position-relative"
                                                            style="width: 100px; height: 100px;" title="{{ $attachment }}">
                                                            @if($isImage)
                                                                <img src="{{ $url }}" class="w-100 h-100 object-fit-cover rounded cursor-pointer view-image" data-image="{{ $url }}" alt="Signature">
                                                            @else
                                                                <a href="{{ $url }}" target="_blank" class="w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-light rounded text-decoration-none shadow-none">
                                                                    <i class="ri ri-file-text-line fs-2 text-primary"></i>
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

                                        <div class="mb-3 mt-5">
                                            <div class="form-floating form-floating-outline text-black">
                                                <input type="file"
                                                    class="form-control @error('attachments') is-invalid @enderror"
                                                    id="attachments" name="attachments">
                                                <label for="attachments">Attachments</label>
                                                @if(!empty($invoice->attachments))
                                                    <div class="mt-2 preview-container">
                                                        @php
                                                        $attachment = $invoice->attachments;
                                                        $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                                        $url = url('uploads/purchase_invoices/' . $invoice->attachments);
                                                        @endphp

                                                        @error('attachments')
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @enderror
                                                        <small class="text-muted d-block mt-1">Max file size: 2MB. Supported
                                                            formats: JPG, PNG, JPEG, WEBP, PDF, DOC, DOCX</small>
                                                        <div class="attachment-thumb border rounded p-1 bg-white shadow-sm position-relative"
                                                            style="width: 100px; height: 100px;" title="{{ $attachment }}">
                                                            @if($isImage)
                                                                <img src="{{ $url }}" class="w-100 h-100 object-fit-cover rounded cursor-pointer view-image" data-image="{{ $url }}" alt="Attachment">
                                                            @else
                                                                <a href="{{ $url }}" target="_blank" class="w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-light rounded text-decoration-none shadow-none">
                                                                    <i class="ri ri-file-text-line fs-2 text-primary"></i>
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
                                    </div>
                                </div>
                            </div>
                            @php
                                $subTotal = old('sub_total', $invoice->sub_total ?? 0);
                                $discountPercent = old('discount_percent', $invoice->discount_percent ?? 0);
                                $discountAmount = old('discount_amount', $invoice->discount_amount ?? 0);
                                $taxableAmount = old('taxable_amount', $invoice->taxable_amount ?? 0);
                                $otherState = old('other_state', isset($invoice) && $invoice->other_state ? 'Y' : 'N');
                                $igstPercent = old('igst_percent', $invoice->igst_percent ?? $web_settings->igst);
                                $igstAmount = old('igst_amount', $invoice->igst_amount ?? 0);
                                $cgstPercent = old('cgst_percent', $invoice->cgst_percent ?? $web_settings->cgst);
                                $cgstAmount = old('cgst_amount', $invoice->cgst_amount ?? 0);
                                $sgstPercent = old('sgst_percent', $invoice->sgst_percent ?? $web_settings->sgst);
                                $sgstAmount = old('sgst_amount', $invoice->sgst_amount ?? 0);
                                $taxAmount = old('tax_amount', $invoice->tax_amount ?? 0);
                                $otherCharges = old('other_charges', $invoice->other_charges ?? 0);
                                $roundOff = old('round_off', $invoice->round_off ?? 0);
                                $roundOffType = old('round_off_type', $invoice->round_off_type ?? 'Add');
                                $grandTotal = old('grand_total', $invoice->grand_total ?? 0);
                                $receivedAmt = old('received_amount', $invoice->received_amount ?? 0);
                                $dueAmount = old('due_amount', $invoice->due_amount ?? 0);

                                $preGstTotal = 0;
                                $postGstTotal = 0;
                                foreach ($chargesToLoop as $c) {
                                $amt = is_array($c) ? ($c['amount'] ?? 0) : ($c->charge_amount ?? 0);
                                $type = is_array($c) ? ($c['tax_type'] ?? 'Post-GST') : ($c->tax_type ?? 'Post-GST');
                                if ($type === 'Pre-GST')
                                    $preGstTotal += $amt;
                                else
                                    $postGstTotal += $amt;
                                }
                            @endphp

                            <div class="col-lg-6">
                                <div class="p-3">
                                    <h5 class="fw-semibold mb-3">Invoice Summary</h5>
                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <span>Sub total:</span>
                                        <strong id="subtotal">{{ number_format($subTotal, 2) }}</strong>
                                        <input type="hidden" name="sub_total" id="sub_total_input" value="{{ $subTotal }}">
                                    </div>
                                    <div class="d-flex justify-content-between py-2 border-bottom align-items-center">
                                        <span>Commission:</span>
                                        <div class="d-flex gap-2 align-items-center">
                                            <div class="d-flex align-items-center gap-1">
                                                <strong id="commission_percent_display">{{ number_format(old('commission', $invoice->commission ?? 0), 2) }}</strong>
                                                <span>%</span>
                                            </div>
                                            <input type="hidden" name="commission" id="commission_input" value="{{ old('commission', $invoice->commission ?? 0) }}">
                                            <strong id="commission_value">{{ number_format(old('commission_amount', $invoice->commission_amount ?? 0), 2) }}</strong>
                                            <input type="hidden" name="commission_amount" id="commission_amount_input" value="{{ old('commission_amount', $invoice->commission_amount ?? 0) }}">
                                        </div>
                                    </div>
                                    @error('commission')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                    <div class="d-flex justify-content-between py-2 border-bottom align-items-center">
                                        <span>Discount:</span>
                                        <div class="d-flex gap-2 align-items-center">
                                            <div class="input-group input-group-sm" style="width:120px;">
                                                <input type="number" name="discount_percent" id="discount_input" class="form-control text-end @error('discount_percent') is-invalid @enderror" value="{{ $discountPercent }}" step="0.01" {{ isset($invoice) ? 'readonly' : '' }}>
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <strong id="discount_value">{{ number_format($discountAmount, 2) }}</strong>
                                            <input type="hidden" name="discount_amount" id="discount_amount_input" value="{{ $discountAmount }}">
                                        </div>
                                    </div>
                                    @error('discount_percent')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <span>Pre-GST Charges:</span>
                                        <strong id="pre_gst_total_display">{{ number_format($preGstTotal, 2) }}</strong>
                                        <input type="hidden" id="pre_gst_total_input" value="{{ $preGstTotal }}">
                                    </div>
                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <span>Taxable Total:</span>
                                        <strong id="total">{{ number_format($taxableAmount, 2) }}</strong>
                                        <input type="hidden" name="taxable_amount" id="taxable_amount_input" value="{{ $taxableAmount }}">
                                    </div>
                                    <div class="py-3 border-bottom">
                                        <label class="fw-semibold mb-2 d-block">Other State?</label>
                                        <div class="d-flex gap-4">
                                            <div class="form-check">
                                                <input class="form-check-input @error('other_state') is-invalid @enderror" type="radio" name="other_state" value="Y" {{ $otherState === 'Y' ? 'checked' : '' }} {{ isset($invoice) ? 'disabled' : '' }} onclick="return false;">
                                                <label class="form-check-label">Yes</label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input @error('other_state') is-invalid @enderror" type="radio" name="other_state" value="N" {{ $otherState === 'N' ? 'checked' : '' }} {{ isset($invoice) ? 'disabled' : '' }} onclick="return false;">
                                                <label class="form-check-label">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    @error('other_state')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                    <div id="igst_div" class="py-2 border-bottom"
                                        style="{{ $otherState === 'Y' ? '' : 'display:none;' }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>IGST</span>
                                            <div class="d-flex gap-2 align-items-center">
                                                <input type="number" name="igst_percent" id="igst_percent" value="{{ $igstPercent }}" class="form-control form-control-sm text-end @error('igst_percent') is-invalid @enderror" style="width:80px;">
                                                <span>%</span>
                                                <strong id="igst_amt">{{ number_format($igstAmount, 2) }}</strong>
                                                <input type="hidden" name="igst_amount" id="igst_amount_input" value="{{ $igstAmount }}">
                                            </div>
                                        </div>
                                        @error('igst_percent')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div id="cgst_sgst_div" class="py-2 border-bottom"
                                        style="{{ $otherState === 'N' ? '' : 'display:none;' }}">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>CGST</span>
                                            <div class="d-flex gap-2 align-items-center">
                                                <input type="number" name="cgst_percent" id="cgst_percent" value="{{ $cgstPercent }}" class="form-control form-control-sm text-end @error('cgst_percent') is-invalid @enderror" style="width:80px;" readonly>
                                                <span>%</span>
                                                <strong id="cgst_amt">{{ number_format($cgstAmount, 2) }}</strong>
                                                <input type="hidden" name="cgst_amount" id="cgst_amount_input" value="{{ $cgstAmount }}">
                                            </div>
                                        </div>
                                        @error('cgst_percent')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror

                                        <div class="d-flex justify-content-between">
                                            <span>SGST</span>
                                            <div class="d-flex gap-2 align-items-center">
                                                <input type="number" name="sgst_percent" id="sgst_percent" value="{{ $sgstPercent }}" class="form-control form-control-sm text-end @error('sgst_percent') is-invalid @enderror" style="width:80px;" readonly>
                                                <span>%</span>
                                                <strong id="sgst_amt">{{ number_format($sgstAmount, 2) }}</strong>
                                                <input type="hidden" name="sgst_amount" id="sgst_amount_input" value="{{ $sgstAmount }}">
                                            </div>
                                        </div>
                                        @error('sgst_percent')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <span>Tax Amount:</span>
                                        <strong id="tax_amount">{{ number_format($taxAmount, 2) }}</strong>
                                        <input type="hidden" name="tax_amount" id="tax_amount_input" value="{{ $taxAmount }}">
                                    </div>
                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <span>Post-GST Charges:</span>
                                        <strong id="post_gst_total_display">{{ number_format($postGstTotal, 2) }}</strong>
                                        <input type="hidden" name="other_charges" id="other_charges_input"
                                            value="{{ $postGstTotal }}">
                                    </div>
                                    @error('other_charges')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <label class="fw-semibold">Round Off:</label>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="form-check form-check-inline m-0">
                                                <input class="form-check-input round-off-type-radio" type="radio" name="round_off_type" id="round_off_add" value="Add" {{ $roundOffType == 'Add' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="round_off_add">Add</label>
                                            </div>
                                            <div class="form-check form-check-inline m-0">
                                                <input class="form-check-input round-off-type-radio" type="radio" name="round_off_type" id="round_off_less" value="Less" {{ $roundOffType == 'Less' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="round_off_less">Less</label>
                                            </div>
                                            <input type="number" class="form-control form-control-sm text-end" style="width: 80px;" id="round_off_input" name="round_off" value="{{ $roundOff }}" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between py-2 border-top fw-semibold">
                                        <span>Grand Total:</span>
                                        <strong id="grand_total">{{ number_format($grandTotal, 2) }}</strong>
                                        <input type="hidden" name="grand_total" id="grand_total_input" value="{{ $grandTotal }}">
                                    </div>
                                    @error('grand_total')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror

                                    <input type="hidden" id="paid_so_far_input" value="{{ $paid_so_far ?? 0 }}">

                                    <input type="hidden" name="received_amount" id="received_amount_input" value="{{ isset($invoice) ? 0 : ($receivedAmt ?? 0) }}">

                                    <input type="hidden" name="due_amount" id="due_amount_input" value="{{ $dueAmount }}">

                                    <div class="text-end mt-4">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a href="{{ url('purchase_invoices') }}" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Payment History Modal --}}
@if(isset($invoice))
    <div class="modal fade" id="paymentHistoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payment Transaction History - {{ $invoice->invoice_no }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody id="payment_history_body">
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold bg-light">
                                    <td colspan="1" class="text-end">Total Received:</td>
                                    <td class="text-end" id="history_total_paid">₹0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection
@section('scripts')
    <style>
    .purchase-items-scroll {
        overflow-x: auto;
        overflow-y: visible;
        -webkit-overflow-scrolling: touch;
    }

    .purchase-items-table {
        min-width: 1600px;
        table-layout: auto;
    }

    .purchase-items-table .hsn-column {
        min-width: 170px;
        width: 170px;
    }

    .purchase-items-table .item-hsn {
        min-width: 140px;
    }
.purchase-items-table .invoice-qty-column {
    min-width: 230px;
    width: 230px;
}

.purchase-items-table .invoice-qty-column .item-quantity {
    max-width: 160px;
}
</style>
    <script>
        @php
            // Determine if any existing item (edit/old mode) is from Fabric Store (id=1)
            $hasFabricItems = false;
            if (isset($invoice) && $invoice && $invoice->items->count()) {
                $hasFabricItems = $invoice->items->contains(function($invItem) {
                    return ($invItem->purchaseOrderItem->store_category_id ?? 0) == 1;
                });
            } elseif (old('items')) {
                foreach (old('items') as $oldItem) {
                    if (($oldItem['store_category_id'] ?? 0) == 1) {
                        $hasFabricItems = true;
                        break;
                    }
                }
            }
        @endphp

        function toggleFabricColumns(hasFabric) {
            if (hasFabric) {
                $('.col-fabric').show();
                $('.fabric-only-cell').show();
            } else {
                $('.col-fabric').hide();
                $('.fabric-only-cell').hide();
            }
        }

        $(document).ready(function () {
            toggleFabricColumns({{ $hasFabricItems ? 'true' : 'false' }});
            $('.select2').select2({
                width: '100%',
                dropdownParent: $('body')
            });

            function toggleTransactionId() {
                const mode = $('#payment_mode').val();
                const $div = $('#transaction_id_div');
                const $label = $('#transaction_id_label');
                const $input = $('#transaction_id');

                if (mode === 'Cheque') {
                    $div.removeClass('d-none');
                    $label.text('Cheque Number');
                    $input.attr('placeholder', 'Enter Cheque Number');
                } else if (mode === 'UPI') {
                    $div.removeClass('d-none');
                    $label.text('UPI ID / Transaction ID');
                    $input.attr('placeholder', 'Enter UPI ID / Transaction ID');
                } else {
                    $div.addClass('d-none');
                }
            }

            $('#payment_mode').on('change', function () {
                toggleTransactionId();
            });

            toggleTransactionId();

            function updateSelectAllState() {
                let allChecked = $('.item-checkbox').length > 0 && $('.item-checkbox:not(:checked)').length === 0;
                $('#select_all_items').prop('checked', allChecked);
            }

            $('.item-checkbox').each(function () {
                toggleItemFields($(this));
            });
            updateSelectAllState();
            $('#purchase_order').on('change', function () {
                let poId = $(this).val();
                if (poId) {
                    $.ajax({
                        url: "{{ url('purchase_invoices/get-po-details') }}/" + poId,
                        type: "GET",
                        success: function (response) {
                            if (response.success) {
                                $('#purchase_order_no').val(response.po_number);
                                $('#po_reference').val(response.po_number);
                                $('#supplier_id').val(response.supplier_id);
                                $('#supplier_name').val(response.supplier_name);
                                $('#supplier_name_hidden').val(response.supplier_name);

                                $('#discount_input').val(response.discount_percent);
                                $('#commission_input').val(response.commission);
                                $('#commission_percent_display').text(parseFloat(response.commission || 0).toFixed(2));
                                $('#purchase_commission_agent_id').val(response.purchase_commission_agent_id);
                                $('#purchase_commission_agent_name').val(response.purchase_commission_agent_name);

                                if (response.round_off) {
                                    $('#round_off_input').val(parseFloat(response.round_off).toFixed(2)).trigger('change');
                                }
                                if (response.round_off_type) {
                                    $(`input[name="round_off_type"][value="${response.round_off_type}"]`).prop('checked', true).trigger('change');
                                }

                                let companyStateId = "{{ $web_settings->state_id }}";
                                if (response.supplier_state_id) {
                                    if (response.supplier_state_id == companyStateId) {
                                        $('input[name="other_state"][value="N"]').prop('checked', true).trigger('change');
                                        $('#cgst_percent').val(response.cgst_percent);
                                        $('#sgst_percent').val(response.sgst_percent);
                                        $('#igst_percent').val(0);
                                    } else {
                                        $('input[name="other_state"][value="Y"]').prop('checked', true).trigger('change');
                                        $('#igst_percent').val(response.igst_percent);
                                        $('#cgst_percent').val(0);
                                        $('#sgst_percent').val(0);
                                    }
                                }

                                let itemsHtml = "";

                                let brandOptions = '<option value="">Select Brand</option>';
                                if (response.all_brands) {
                                    response.all_brands.forEach(function(b) {
                                        brandOptions += `<option value="${b.id}">${b.name}</option>`;
                                    });
                                }

                                let widthOptions = '<option value="">Select Width</option>';
                                if (response.all_fabric_widths) {
                                    response.all_fabric_widths.forEach(function(f) {
                                        widthOptions += `<option value="${f.id}">${f.name}</option>`;
                                    });
                                }

                                response.items.forEach(function (item, index) {
                                    const balancedQty = item.qty_ordered - item.qty_invoiced;
                                    
                                    let itemBrandSelect = `<select name="items[${index}][brand_id]" class="select2 form-select form-select-sm">${brandOptions}</select>`;
                                    let itemWidthSelect = `<select name="items[${index}][fabric_width_id]" class="select2 form-select form-select-sm">${widthOptions}</select>`;

                                    let brandSelectObj = $(itemBrandSelect);
                                    brandSelectObj.find(`option[value="${item.brand_id}"]`).attr('selected', 'selected');
                                    itemBrandSelect = brandSelectObj.prop('outerHTML');

                                    let widthSelectObj = $(itemWidthSelect);
                                    widthSelectObj.find(`option[value="${item.fabric_width_id}"]`).attr('selected', 'selected');
                                    itemWidthSelect = widthSelectObj.prop('outerHTML');

                                    itemsHtml += `
                                        <tr class="item-row">
                                            <td>
                                                <input type="checkbox" name="items[${index}][selected]" 
                                                    class="form-check-input item-checkbox" value="1">

                                                <input type="hidden" name="items[${index}][purchase_order_item_id]" value="${item.id}">
                                                <input type="hidden" name="items[${index}][raw_material_id]" value="${item.raw_material_id}">
                                                <input type="hidden" name="items[${index}][raw_material_name]" value="${item.raw_material_name}">
                                                <input type="hidden" name="items[${index}][uom_id]" value="${item.uom_id}">
                                                <input type="hidden" name="items[${index}][uom_code]" value="${item.uom_code}">
                                                <input type="hidden" name="items[${index}][rate]" value="${item.rate}" class="item-rate">
                                                <input type="hidden" name="items[${index}][qty_ordered]" value="${item.qty_ordered}" class="qty-ordered-val">
                                                <input type="hidden" name="items[${index}][qty_invoiced]" value="${item.qty_invoiced}" class="qty-invoiced-val">
                                                <input type="hidden" name="items[${index}][store_category_name]" value="${item.store_category_name}">
                                                <input type="hidden" name="items[${index}][store_category_id]" value="${item.store_category_id || 0}">
                                                <input type="hidden" name="items[${index}][brand_name]" value="${item.brand_name}">
                                                <input type="hidden" name="items[${index}][fabric_width]" value="${item.fabric_width}">
                                                <input type="hidden" name="items[${index}][fabric_type_name]" value="${item.fabric_type_name || '-'}">
                                            </td>
                                            <td>${item.store_category_name}</td>
                                            <td>${item.raw_material_name}</td>
                                            <td>${item.art_no || '-'}</td>
                                            <td>${itemBrandSelect}</td>
                                            <td class="hsn-column">
                                                <input type="text" 
                                                    name="items[${index}][hsn_code]"
                                                    class="form-control form-control-sm item-hsn" 
                                                    value="${item.hsn_code || ''}"
                                                    placeholder="Enter HSN"
                                                    readonly>
                                            </td>
                                            <td class="fabric-only-cell">${itemWidthSelect}</td>
                                            <td class="fabric-only-cell">${item.fabric_type_name || '-'}</td>

                                            <!-- Ordered Qty -->
                                            <td class="qty-ordered-display">${item.qty_ordered}</td>

                                            <!-- Balanced Qty -->
                                            <td class="balanced-qty-display">${balancedQty.toFixed(2)}</td>

                                            <!-- Invoiced Qty (Input Field) -->
                                            <td class="invoice-qty-column">
                                                <input type="number" 
                                                    name="items[${index}][quantity]"
                                                    class="form-control form-control-sm item-quantity received-qty-input" 
                                                    step="0.01"
                                                    value="${balancedQty}"
                                                    readonly
                                                    placeholder="0.00"
                                                    data-max-qty="${balancedQty}"
                                                    data-ordered-qty="${item.qty_ordered}">
                                                    <small class="text-secondary">
                                                    Note: Invoiced quantity can exceed ordered quantity by up to 50% (Max: ${(item.qty_ordered * 1.5).toFixed(2)}).
                                                </small>
                                            </td>

                                            <td>${item.uom_code}</td>
                                            <td class="rate-display">${parseFloat(item.rate).toFixed(2)}</td>
                                            <td class="item-amount">0.00</td>

                                        </tr>`;
                                });

                                $('#items_tbody').html(itemsHtml);
                                $('#items_tbody .select2').select2({
                                    width: '100%'
                                });
                                // Show/hide fabric columns based on store category
                                let hasFabric = response.items.some(i => parseInt(i.store_category_id) === 1);
                                toggleFabricColumns(hasFabric);
                                $('.item-row').each(function () {
                                    let $row = $(this);
                                    let qty = parseFloat($row.find('.item-quantity').val()) || 0;
                                    let rate = parseFloat($row.find('.item-rate').val()) || 0;
                                    let amount = qty * rate;
                                    $row.find('.item-amount').text(amount.toFixed(2));
                                });

                                $('#select_all_items').prop('checked', false);

                                $('#subtotal').text('0.00');
                                $('#sub_total_input').val('0');
                                $('#discount_value').text('0.00');
                                $('#discount_amount_input').val('0');
                                $('#total').text('0.00');
                                $('#taxable_amount_input').val('0');
                                $('#tax_amount').text('0.00');
                                $('#tax_amount_input').val('0');
                                $('#other_charges').text('0.00');
                                $('#other_charges_input').val('0');
                                $('#grand_total').text('0.00');
                                $('#grand_total_input').val('0');
                                $('#due_amount').text('0.00');
                                $('#due_amount_input').val('0');
                                $('#cgst_amt').text('0.00');
                                $('#sgst_amt').text('0.00');
                                $('#igst_amt').text('0.00');

                                setTimeout(() => {
                                    $('.item-row').each(function () {
                                        let $row = $(this);
                                        let checkbox = $row.find('.item-checkbox');

                                        toggleItemFields(checkbox);

                                        if (!checkbox.is(':checked')) {
                                            $row.find('.item-amount').text('0.00');
                                        }
                                    });

                                    calculateTotals();
                                }, 200);
                            }
                        },
                        error: function () {
                            alert("Failed to load purchase order details");
                        }
                    });
                } else {
                    toggleFabricColumns(false);
                    $('#items_tbody').html('<tr><td colspan="13" class="text-center text-muted">Please select a Purchase Order to load items</td></tr>');
                    $('#purchase_order_no').val('');
                    $('#supplier_id').val('');
                    $('#purchase_commission_agent_id').val('');
                    $('#purchase_commission_agent_name').val('');
                    $('#select_all_items').prop('checked', false);
                    calculateTotals();
                }
            });

            $(document).on('input', '.received-qty-input', function () {
                const $row = $(this).closest('tr');
                const receivedQty = parseFloat($(this).val()) || 0;
                const orderedQty = parseFloat($row.find('.qty-ordered-val').val()) || 0;
                const invoicedQty = parseFloat($row.find('.qty-invoiced-val').val()) || 0;
                const rate = parseFloat($row.find('.item-rate').val()) || 0;

                const balancedQty = Math.max(0, orderedQty - invoicedQty - receivedQty);
                $row.find('.balanced-qty-display').text(balancedQty.toFixed(2));

                const maxTotalQty = orderedQty * 1.5;
                const maxQty = maxTotalQty - invoicedQty;

                if (receivedQty > maxQty) {
                    $(this).addClass('is-invalid');
                    if (!$(this).next('.invalid-feedback').length) {
                        $(this).after('<div class="invalid-feedback d-block">Received quantity cannot exceed ' + maxQty.toFixed(2) + ' (Order + 50% Tolerance)</div>');
                    }
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').remove();
                }

                const amount = receivedQty * rate;
                $row.find('.item-amount').text(amount.toFixed(2));

                if (typeof calculateTotals === 'function') {
                    calculateTotals();
                }
            });

            $('#select_all_items').on('change', function () {
                $('.item-checkbox').prop('checked', $(this).is(':checked'));
                $('.item-checkbox').each(function () {
                    toggleItemFields($(this));
                });
                calculateTotals();
            });

            function toggleItemFields($checkbox) {
                let $row = $checkbox.closest('tr');
                let $hsnInput = $row.find('.item-hsn');
                let $qtyInput = $row.find('.item-quantity');
                let isEditMode = $('#isEditMode').val() == '1';

                if ($checkbox.is(':checked')) {
                    $hsnInput.prop('readonly', false);
                    $qtyInput.prop('readonly', false);
                } else {
                    $hsnInput.prop('readonly', true);
                    $qtyInput.prop('readonly', true);
                    $hsnInput.removeClass('is-invalid');
                    $qtyInput.removeClass('is-invalid');
                    $hsnInput.next('.invalid-feedback').remove();
                    $qtyInput.next('.invalid-feedback').remove();
                }
            }


            $(document).on('input', '.item-quantity', function () {
                let $input = $(this);
                let row = $input.closest('tr');
                let qty = parseFloat($input.val()) || 0;
                let orderedQty = parseFloat(row.find('.qty-ordered-val').val()) || 0;
                let invoicedQty = parseFloat(row.find('.qty-invoiced-val').val()) || 0;
                let rate = parseFloat(row.find('.item-rate').val()) || 0;
                let checkbox = row.find('.item-checkbox');

                $input.removeClass('is-invalid');
                $input.next('.invalid-feedback').remove();

                if (!checkbox.is(':checked')) {
                    row.find('.item-amount').text('0.00');
                    calculateTotals();
                    return;
                }

                const maxTotalQty = orderedQty * 1.5;
                let oldQty = 0;
                let isEditMode = $('#isEditMode').val() == '1';
                if (isEditMode) {
                }
                let maxAllowed = maxTotalQty - invoicedQty;
                if (isEditMode) {
                    maxAllowed = maxTotalQty;
                }

                if (qty > maxAllowed && !isEditMode) {
                    $input.addClass('is-invalid');
                    $input.after(`<div class="invalid-feedback d-block">Received quantity cannot exceed ${maxAllowed.toFixed(2)} (Order + 50% Tolerance minus already invoiced)</div>`);
                    row.find('.item-amount').text('0.00');
                    calculateTotals();
                    return;
                } else if (qty > maxTotalQty) {
                    $input.addClass('is-invalid');
                    $input.after(`<div class="invalid-feedback d-block">Received quantity cannot exceed ${(maxTotalQty).toFixed(2)} (Total Order + 50% Tolerance)</div>`);
                    row.find('.item-amount').text('0.00');
                    calculateTotals();
                    return;
                }

                let amount = qty * rate;
                row.find('.item-amount').text(amount.toFixed(2));

                calculateTotals();
            });


            $(document).on('change', '.item-checkbox', function () {
                toggleItemFields($(this));
                updateSelectAllState();
                calculateTotals();
            });

            $('#select_all_charges').on('change', function () {
                $('.charge-checkbox').prop('checked', $(this).is(':checked'));
                calculateTotals();
            });

            $(document).on('input change', '.charge-checkbox, .charge-amount', function () {
                calculateTotals();
            });

            $('#discount_input').on('input', function () {
                calculateSummaryOnly();
            });

            $('#commission_input').on('input', function () {
                calculateSummaryOnly();
            });

            $('input[name="other_state"]').on('change', function () {
                if ($(this).val() === 'Y') {
                    $('#igst_div').show();
                    $('#cgst_sgst_div').hide();
                    if (parseFloat($('#igst_percent').val()) == 0) {
                        $('#igst_percent').val("{{ $web_settings->igst }}");
                    }
                } else {
                    $('#igst_div').hide();
                    $('#cgst_sgst_div').show();
                    if (parseFloat($('#cgst_percent').val()) == 0) {
                        $('#cgst_percent').val("{{ $web_settings->cgst }}");
                    }
                    if (parseFloat($('#sgst_percent').val()) == 0) {
                        $('#sgst_percent').val("{{ $web_settings->sgst }}");
                    }
                }

                calculateTaxOnly();
            });


            $('#igst_percent, #cgst_percent, #sgst_percent').on('input', function () {
                calculateTotals();
            });

            $('#received_amount_input, #round_off_input, .round-off-type-radio').on('input change', function () {
                if (this.id === 'round_off_input') {
                    let val = parseFloat($(this).val());
                    if (val < 0) $(this).val(Math.abs(val));
                }
                calculateSummaryOnly();
            });

            function calculateGrandTotalOnly() {
                let grandTotal = parseFloat($('#grand_total_input').val()) || 0;

                let paidSoFar = parseFloat($('#paid_so_far_input').val()) || 0;
                let newPayment = parseFloat($('#received_amount_input').val()) || 0;
                let dueAmount = grandTotal - paidSoFar - newPayment;

                $('#due_amount').text(dueAmount.toFixed(2));
                $('#due_amount_input').val(dueAmount.toFixed(2));
            }


            function calculateTotals() {
                let subTotal = 0;
                $('.item-row').each(function () {
                    let $row = $(this);
                    let isChecked = $row.find('.item-checkbox').is(':checked');

                    if (!isChecked) return;

                    let qty = parseFloat($row.find('.item-quantity').val()) || 0;
                    let rate = parseFloat($row.find('.item-rate').val()) || 0;

                    let amount = qty * rate;
                    $row.find('.item-amount').text(amount.toFixed(2));
                    subTotal += amount;
                });

                $('#subtotal').text(subTotal.toFixed(2));
                $('#sub_total_input').val(subTotal.toFixed(2));

                let discountPercent = parseFloat($('#discount_input').val()) || 0;
                let discountAmount = (subTotal * discountPercent) / 100;

                $('#discount_value').text(discountAmount.toFixed(2));
                $('#discount_amount_input').val(discountAmount.toFixed(2));

                let commissionPercent = parseFloat($('#commission_input').val()) || 0;
                let commissionAmount = (subTotal * commissionPercent) / 100;
                $('#commission_value').text(commissionAmount.toFixed(2));
                $('#commission_amount_input').val(commissionAmount.toFixed(2));

                let itemTotal = subTotal - discountAmount - commissionAmount;

                let preGstCharges = 0;
                let postGstCharges = 0;
                $('.charge-row').each(function () {
                    let amount = parseFloat($(this).find('input[name="charges[amount][]"]').val()) || 0;
                    let taxType = $(this).attr('data-tax-type') || $(this).data('tax-type') || 'Post-GST';
                    if (taxType === 'Pre-GST') {
                        preGstCharges += amount;
                    } else {
                        postGstCharges += amount;
                    }
                });

                let taxableAmount = itemTotal + preGstCharges;
                $('#total').text(taxableAmount.toFixed(2));
                $('#taxable_amount_input').val(taxableAmount.toFixed(2));

                let taxAmount = 0;

                if ($('input[name="other_state"]:checked').val() === 'Y') {
                    let igstPercent = parseFloat($('#igst_percent').val()) || 0;
                    let igstAmount = (taxableAmount * igstPercent) / 100;

                    $('#igst_amt').text(igstAmount.toFixed(2));
                    $('#igst_amount_input').val(igstAmount.toFixed(2));

                    $('#cgst_amt').text("0.00");
                    $('#sgst_amt').text("0.00");

                    taxAmount = igstAmount;

                } else {
                    let cgstPercent = parseFloat($('#cgst_percent').val()) || 0;
                    let sgstPercent = parseFloat($('#sgst_percent').val()) || 0;

                    let cgstAmount = (taxableAmount * cgstPercent) / 100;
                    let sgstAmount = (taxableAmount * sgstPercent) / 100;

                    $('#cgst_amt').text(cgstAmount.toFixed(2));
                    $('#sgst_amt').text(sgstAmount.toFixed(2));

                    $('#cgst_amount_input').val(cgstAmount.toFixed(2));
                    $('#sgst_amount_input').val(sgstAmount.toFixed(2));

                    $('#igst_amt').text("0.00");

                    taxAmount = cgstAmount + sgstAmount;
                }

                $('#tax_amount').text(taxAmount.toFixed(2));
                $('#tax_amount_input').val(taxAmount.toFixed(2));

                let totalOtherCharges = preGstCharges + postGstCharges;

                $('#pre_gst_total_display').text(preGstCharges.toFixed(2));
                $('#pre_gst_total_input').val(preGstCharges.toFixed(2));

                $('#post_gst_total_display').text(postGstCharges.toFixed(2));
                $('#other_charges_input').val(postGstCharges.toFixed(2));

                let totalBeforeRoundOff = parseFloat((taxableAmount + taxAmount + postGstCharges).toFixed(2));

                let roundOffAmount = parseFloat($('#round_off_input').val()) || 0;
                let roundOffType = $('input[name="round_off_type"]:checked').val();
                let finalTotal = 0;

                if (roundOffType === 'Add') {
                    finalTotal = totalBeforeRoundOff + roundOffAmount;
                } else {
                    finalTotal = totalBeforeRoundOff - roundOffAmount;
                }

                $('#grand_total').text(finalTotal.toFixed(2));
                $('#grand_total_input').val(finalTotal.toFixed(2));

                let receivedAmount = parseFloat($('#received_amount_input').val()) || 0;
                let dueAmount = finalTotal - receivedAmount;

                $('#due_amount').text(dueAmount.toFixed(2));
                $('#due_amount_input').val(dueAmount.toFixed(2));
            }

            function refreshChargeDropdownState() {
                let selectedChargeIds = [];
                $('#added_charges_list tr').each(function () {
                    let id = $(this).data('charge-id');
                    if (id) selectedChargeIds.push(id.toString());
                });

                $('#charges_select option').each(function () {
                    let optionId = $(this).val();
                    if (optionId) {
                        if (selectedChargeIds.includes(optionId.toString())) {
                            $(this).prop('disabled', true);
                        } else {
                            $(this).prop('disabled', false);
                        }
                    }
                });

                $('#charges_select').select2('destroy').select2({
                    width: '100%',
                    dropdownParent: $('body')
                });
            }

            function loadCharges() {
                $.ajax({
                    url: "{{ url('get_charges') }}",
                    type: "GET",
                    success: function (data) {
                        let select = $('#charges_select');
                        select.empty();
                        select.append('<option value="">Select Charge</option>');

                        data.forEach(function (charge) {
                            select.append(`<option value="${charge.id}">${charge.charge_name}</option>`);
                        });
                        refreshChargeDropdownState();
                    }
                });
            }
            loadCharges();
            $('#add_charge_btn').click(function () {
                let chargeId = $('#charges_select').val();
                let chargeText = $('#charges_select option:selected').text();
                let amount = parseFloat($('#charge_amount').val());
                let taxType = $('#charge_tax_type').val();

                if (!chargeId) {
                    alert("Please select a charge");
                    return;
                }

                if (!amount || amount <= 0) {
                    alert("Please enter a valid amount");
                    return;
                }

                $('#charges_table').removeClass('d-none');

                let row = `
                            <tr class="charge-row" data-charge-id="${chargeId}" data-tax-type="${taxType}">
                                <td>
                                    ${chargeText}
                                    <input type="hidden" name="charges[charge_id][]" value="${chargeId}">
                                    <input type="hidden" name="charges[name][]" value="${chargeText}">
                                </td>
                                <td>
                                    ${taxType}
                                    <input type="hidden" name="charges[tax_type][]" value="${taxType}">
                                </td>
                                <td>
                                    ${amount.toFixed(2)}
                                    <input type="hidden" name="charges[amount][]" value="${amount.toFixed(2)}">
                                </td>
                                <td class="d-flex align-items-center">
                                    <button type="button" class="btn btn-outline-success btn-sm edit-charge me-1" title="Edit Charge">
                                        <i class="ri ri-pencil-line"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-charge" title="Delete Charge">
                                        <i class="ri ri-delete-bin-line"></i>
                                    </button>
                                </td>
                            </tr>
                        `;

                $('#added_charges_list').append(row);

                $('#charges_select').val('').trigger('change');
                $('#charge_amount').val('');

                calculateChargesOnly();
                refreshChargeDropdownState();
            });

            $(document).on('click', '.edit-charge', function () {
                let $row = $(this).closest('tr');
                let chargeId = $row.data('charge-id');
                let amount = parseFloat($row.find('input[name="charges[amount][]"]').val()) || 0;
                let taxType = $row.attr('data-tax-type') || $row.data('tax-type') || 'Post-GST';

                // Populate inputs
                $('#charges_select').val(chargeId).trigger('change');
                $('#charge_amount').val(amount.toFixed(2));
                $('#charge_tax_type').val(taxType).trigger('change');

                // Remove the row
                $row.remove();

                // Recalculate and refresh
                if ($('#added_charges_list tr').length === 0) {
                    $('#charges_table').addClass('d-none');
                }
                calculateChargesOnly();
                refreshChargeDropdownState();
            });


            $(document).on("click", ".remove-charge", function () {
                let $row = $(this).closest('tr');
                let chargeId = $row.data('charge-id');
                let invoiceChargeId = $row.data('invoice-charge-id');

                if (invoiceChargeId) {
                    Swal.fire({
                        title: "Are you sure?",
                        text: "Do you really want to delete this charge?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#8c57ff",
                        cancelButtonColor: "#ff4c51",
                        confirmButtonText: "Yes, delete it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Deleting...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            $.ajax({
                                url: "{{ url('purchase_invoices/delete-charge') }}/" + invoiceChargeId,
                                type: "DELETE",
                                data: {
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function (response) {
                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Deleted!',
                                            text: 'Charge has been deleted.',
                                            timer: 1500,
                                            showConfirmButton: false
                                        });

                                        $row.remove();

                                        if ($('#added_charges_list tr').length === 0) {
                                            $('#charges_table').addClass('d-none');
                                        }

                                        calculateTotals();
                                        refreshChargeDropdownState();
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error!',
                                            text: response.message || 'Failed to delete charge'
                                        });
                                    }
                                },
                                error: function (xhr) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: 'Failed to delete charge: ' + (xhr.responseJSON?.message || 'Unknown error')
                                    });
                                }
                            });
                        }
                    });
                } else {
                    $('#charges_select').find('option[value="' + chargeId + '"]').prop('disabled', false);
                    $('#charges_select').select2('destroy').select2({
                        width: '100%',
                        dropdownParent: $('body')
                    });

                    $row.remove();

                    if ($('#added_charges_list tr').length === 0) {
                        $('#charges_table').addClass('d-none');
                    }

                    calculateTotals();
                    refreshChargeDropdownState();
                }
            });

            function updateOtherCharges() {
                let total = 0;

                $('#added_charges_list tr').each(function () {
                    let amt = parseFloat($(this).find('input[name="charges[amount][]"]').val()) || 0;
                    total += amt;
                });

                $('#other_charges').text(total.toFixed(2));
                $('#other_charges_input').val(total.toFixed(2));
            }

            function calculateTaxOnly() {

                let itemTotal = (parseFloat($('#sub_total_input').val()) || 0) - (parseFloat($('#discount_amount_input').val()) || 0);

                let preGstCharges = 0;
                let postGstCharges = 0;
                $('.charge-row').each(function () {
                    let amount = parseFloat($(this).find('input[name="charges[amount][]"]').val()) || 0;
                    let taxType = $(this).attr('data-tax-type') || $(this).data('tax-type') || 'Post-GST';
                    if (taxType === 'Pre-GST') {
                        preGstCharges += amount;
                    } else {
                        postGstCharges += amount;
                    }
                });

                let taxableAmount = itemTotal + preGstCharges;
                $('#total').text(taxableAmount.toFixed(2));
                $('#taxable_amount_input').val(taxableAmount.toFixed(2));

                let taxAmount = 0;

                if ($('input[name="other_state"]:checked').val() === 'Y') {

                    let igstPercent = parseFloat($('#igst_percent').val()) || 0;
                    let igstAmount = (taxableAmount * igstPercent) / 100;

                    $('#igst_amt').text(igstAmount.toFixed(2));
                    $('#igst_amount_input').val(igstAmount.toFixed(2));

                    $('#cgst_amt').text('0.00');
                    $('#sgst_amt').text('0.00');

                    taxAmount = igstAmount;

                } else {

                    let cgstPercent = parseFloat($('#cgst_percent').val()) || 0;
                    let sgstPercent = parseFloat($('#sgst_percent').val()) || 0;
                    let cgstAmount = (taxableAmount * cgstPercent) / 100;
                    let sgstAmount = (taxableAmount * sgstPercent) / 100;
                    $('#cgst_amt').text(cgstAmount.toFixed(2));
                    $('#sgst_amt').text(sgstAmount.toFixed(2));
                    $('#cgst_amount_input').val(cgstAmount.toFixed(2));
                    $('#sgst_amount_input').val(sgstAmount.toFixed(2));
                    $('#igst_amt').text('0.00');
                    taxAmount = cgstAmount + sgstAmount;
                }

                let totalOtherCharges = preGstCharges + postGstCharges;

                $('#pre_gst_total_display').text(preGstCharges.toFixed(2));
                $('#pre_gst_total_input').val(preGstCharges.toFixed(2));

                $('#post_gst_total_display').text(postGstCharges.toFixed(2));
                $('#other_charges_input').val(postGstCharges.toFixed(2));

                let totalBeforeRoundOff = parseFloat((taxableAmount + taxAmount + postGstCharges).toFixed(2));

                let roundOffAmount = parseFloat($('#round_off_input').val()) || 0;
                let roundOffType = $('input[name="round_off_type"]:checked').val();
                let finalTotal = 0;

                if (roundOffType === 'Add') {
                    finalTotal = totalBeforeRoundOff + roundOffAmount;
                } else {
                    finalTotal = totalBeforeRoundOff - roundOffAmount;
                }

                $('#grand_total').text(finalTotal.toFixed(2));
                $('#grand_total_input').val(finalTotal.toFixed(2));

                let receivedAmount = parseFloat($('#received_amount_input').val()) || 0;
                let dueAmount = finalTotal - receivedAmount;

                $('#due_amount').text(dueAmount.toFixed(2));
                $('#due_amount_input').val(dueAmount.toFixed(2));
            }


            function calculateChargesOnly() {
                calculateTotals();
            }

            function calculateItemSubtotal() {
                let subTotal = 0;

                $('.item-row').each(function () {
                    let $row = $(this);
                    let isChecked = $row.find('.item-checkbox').is(':checked');

                    if (!isChecked) return;

                    let qty = parseFloat($row.find('.item-quantity').val()) || 0;
                    let rate = parseFloat($row.find('.item-rate').val()) || 0;

                    let amount = qty * rate;
                    $row.find('.item-amount').text(amount.toFixed(2));

                    subTotal += amount;
                });

                $('#subtotal').text(subTotal.toFixed(2));
                $('#sub_total_input').val(subTotal.toFixed(2));
            }

            function calculateSummaryOnly() {
                let subTotal = parseFloat($('#sub_total_input').val()) || 0;

                let discountPercent = parseFloat($('#discount_input').val()) || 0;
                let discountAmount = (subTotal * discountPercent) / 100;

                $('#discount_value').text(discountAmount.toFixed(2));
                $('#discount_amount_input').val(discountAmount.toFixed(2));

                let commissionPercent = parseFloat($('#commission_input').val()) || 0;
                let commissionAmount = (subTotal * commissionPercent) / 100;
                $('#commission_value').text(commissionAmount.toFixed(2));
                $('#commission_amount_input').val(commissionAmount.toFixed(2));

                let itemTotal = subTotal - discountAmount - commissionAmount;

                let preGstCharges = 0;
                let postGstCharges = 0;
                $('.charge-row').each(function () {
                    let amount = parseFloat($(this).find('input[name="charges[amount][]"]').val()) || 0;
                    let taxType = $(this).attr('data-tax-type') || $(this).data('tax-type') || 'Post-GST';
                    if (taxType === 'Pre-GST') {
                        preGstCharges += amount;
                    } else {
                        postGstCharges += amount;
                    }
                });

                let taxableAmount = itemTotal + preGstCharges;
                $('#total').text(taxableAmount.toFixed(2));
                $('#taxable_amount_input').val(taxableAmount.toFixed(2));

                let taxAmount = 0;

                if ($('input[name="other_state"]:checked').val() === 'Y') {
                    let igstPercent = parseFloat($('#igst_percent').val()) || 0;
                    taxAmount = (taxableAmount * igstPercent) / 100;
                    $('#igst_amt').text(taxAmount.toFixed(2));
                } else {
                    let cgst = (taxableAmount * (parseFloat($('#cgst_percent').val()) || 0)) / 100;
                    let sgst = (taxableAmount * (parseFloat($('#sgst_percent').val()) || 0)) / 100;
                    taxAmount = cgst + sgst;
                    $('#cgst_amt').text(cgst.toFixed(2));
                    $('#sgst_amt').text(sgst.toFixed(2));
                }

                $('#tax_amount').text(taxAmount.toFixed(2));
                $('#tax_amount_input').val(taxAmount.toFixed(2));

                let totalOtherCharges = preGstCharges + postGstCharges;

                $('#pre_gst_total_display').text(preGstCharges.toFixed(2));
                $('#pre_gst_total_input').val(preGstCharges.toFixed(2));

                $('#post_gst_total_display').text(postGstCharges.toFixed(2));
                $('#other_charges_input').val(postGstCharges.toFixed(2));

                let totalBeforeRoundOff = parseFloat((taxableAmount + taxAmount + postGstCharges).toFixed(2));

                let roundOffAmount = parseFloat($('#round_off_input').val()) || 0;
                let roundOffType = $('input[name="round_off_type"]:checked').val();
                let finalTotal = 0;

                if (roundOffType === 'Add') {
                    finalTotal = totalBeforeRoundOff + roundOffAmount;
                } else {
                    finalTotal = totalBeforeRoundOff - roundOffAmount;
                }

                $('#grand_total').text(finalTotal.toFixed(2));
                $('#grand_total_input').val(finalTotal.toFixed(2));

                let paidSoFar = parseFloat($('#paid_so_far_input').val()) || 0;
                let newPayment = parseFloat($('#received_amount_input').val()) || 0;
                let due = finalTotal - paidSoFar - newPayment;

                $('#due_amount').text(due.toFixed(2));
                $('#due_amount_input').val(due.toFixed(2));
            }

            @if(isset($invoice))
                $('#view_history_btn').click(function (e) {
                    e.preventDefault();
                    let invoiceId = "{{ $invoice->id }}";
                    $('#payment_history_body').html('<tr><td colspan="2" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading history...</td></tr>');
                    $('#paymentHistoryModal').modal('show');

                    $.ajax({
                        url: "{{ url('purchase_invoices/payment-history') }}/" + invoiceId,
                        type: "GET",
                        success: function (response) {
                            if (response.success) {
                                let html = "";
                                let total = 0;
                                response.payments.forEach(function (payment) {
                                    let date = new Date(payment.payment_date);
                                    let formattedDate = date.toLocaleString('en-IN', {
                                        day: '2-digit', month: '2-digit', year: 'numeric',
                                        hour: '2-digit', minute: '2-digit', second: '2-digit',
                                        hour12: true
                                    });
                                    html += `
                                            <tr>
                                                <td>${formattedDate}</td>
                                                <td class="text-end fw-bold">₹${parseFloat(payment.amount).toLocaleString('en-IN', { minimumFractionDigits: 2 })}</td>
                                            </tr>`;
                                    total += parseFloat(payment.amount);
                                });

                                if (response.payments.length === 0) {
                                    html = '<tr><td colspan="2" class="text-center py-3 text-muted">No transaction logs found</td></tr>';
                                }

                                $('#payment_history_body').html(html);
                                $('#history_total_paid').text('₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2 }));
                            }
                        },
                        error: function () {
                            $('#payment_history_body').html('<tr><td colspan="2" class="text-center text-danger py-3">Error loading history</td></tr>');
                        }
                    });
                });
            @endif

            $(document).on('change', 'input[name="round_off_type"]', function () {
                calculateSummaryOnly();
            });

            $('form.common-form').on('submit', function (e) {
                let hasSelectedItems = $('.item-checkbox:checked').length > 0;
                if (!hasSelectedItems && $('#purchase_order').val()) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please select at least one item from the Item Details section before submitting.',
                        confirmButtonColor: '#8c57ff'
                    });
                    return false;
                }
            });
        });
    </script>
@endsection
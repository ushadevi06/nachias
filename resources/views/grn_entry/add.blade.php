@extends('layouts.common')
@section('title', ($grn ? 'Edit' : 'Add') . ' GRN Entry - ' . env('WEBSITE_NAME'))
@section('content')
    <div class="container-xxl section-padding">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>{{ $grn ? 'Edit' : 'Add' }} GRN Entry</h4>
                        </div>
                        <form action="{{ url('grn_entries/add' . ($grn ? '/' . $grn->id : '')) }}" method="POST" id="grn-form" class="common-form" enctype="multipart/form-data" autocomplete="off">
                            @csrf
                            <div class="col-lg-12">
                                @include('flash_messages')
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" name="grn_no" class="form-control" value="{{ $grn->grn_number ?? $nextGrnNo }}" readonly />
                                        <label>GRN No * </label>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" name="grn_date" autocomplete="off" class="form-control grn_date @error('grn_date') is-invalid @enderror" value="{{ old('grn_date', $grn ? $grn->grn_date->format('d-m-Y') : date('d-m-Y')) }}" />
                                        <label>GRN Date * </label>
                                    </div>
                                    @error('grn_date')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <select name="purchase_invoice_id" id="po_no" class="select2 form-select @error('purchase_invoice_id') is-invalid @enderror" data-placeholder="Select PO Invoice Number">
                                            <option value="">Select PO Invoice Number</option>
                                            @foreach($purchaseInvoices as $inv)
                                                <option value="{{ $inv->id }}" {{ old('purchase_invoice_id', $grn->purchase_invoice_id ?? '') == $inv->id ? 'selected' : '' }}>
                                                    {{ $inv->invoice_no }} {{ isset($inv->purchaseOrder) ? '(PO: ' . $inv->purchaseOrder->po_number . ')' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label>PO Invoice Number *</label>
                                    </div>
                                    @error('purchase_invoice_id')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- ===================== FIX: Full-width item details section ===================== --}}
                                <div id="show_item_det" class="{{ old('purchase_invoice_id', $grn->purchase_invoice_id ?? '') ? '' : 'd-none' }} col-12" style="min-width:0; width:100%;">
                                    <style>
                                        .split-row {
                                            border-left: 4px solid #00cfe8 !important;
                                        }
                                        .item-row.split-row td {
                                            padding-top: 10px;
                                            padding-bottom: 10px;
                                        }
                                        .row-it-count {
                                            font-weight: bold;
                                            color: #5d596c;
                                        }
                                        .grn-table-wrapper {
                                            width: 100%;
                                            max-width: 100%;
                                            overflow-x: auto;
                                            -webkit-overflow-scrolling: touch;
                                            border: 1px solid #dee2e6;
                                            border-radius: 0.375rem;
                                            background: #fff;
                                        }
                                        #grn-items-table {
                                            width: 100%;
                                            min-width: 1720px;
                                            margin-bottom: 0;
                                            table-layout: fixed;
                                            background: #fff;
                                        }
                                        #grn-items-table th,
                                        #grn-items-table td {
                                            min-width: 0;
                                            padding: 1rem 0.75rem;
                                            vertical-align: top;
                                        }
                                        #grn-items-table th {
                                            white-space: normal;
                                            text-align: center;
                                            font-size: 0.95rem;
                                            font-weight: 700;
                                            color: #1f1f1f;
                                            letter-spacing: 0.02em;
                                            line-height: 1.45;
                                        }
                                        #grn-items-table td {
                                            word-wrap: break-word;
                                            text-align: center;
                                        }
                                        #grn-items-table input.form-control,
                                        #grn-items-table select.form-control,
                                        #grn-items-table .select2-container {
                                            width: 100% !important;
                                        }
                                        #grn-items-table .form-control,
                                        #grn-items-table .select2-container .select2-selection {
                                            min-height: 46px;
                                        }
                                        #grn-items-table .form-check-input {
                                            width: 1.2rem;
                                            height: 1.2rem;
                                            margin-top: 0.2rem;
                                        }
                                        #grn-items-table td:nth-child(1),
                                        #grn-items-table td:nth-child(2),
                                        #grn-items-table td:nth-child(8) {
                                            vertical-align: middle;
                                            text-align: center;
                                        }
                                        #grn-items-table td:nth-child(6),
                                        #grn-items-table td:nth-child(7) {
                                            vertical-align: middle !important;
                                            text-align: center !important;
                                        }
                                        #grn-items-table .btn-variants,
                                        #grn-items-table .add-split-row,
                                        #grn-items-table .remove-split-row {
                                            white-space: nowrap;
                                        }
                                        #grn-items-table .btn-variants {
                                            margin-top: 0.75rem;
                                            display: inline-block;
                                        }
                                        #grn-items-table .item-design-cell .btn-variants {
                                            margin-bottom: 0;
                                        }
                                        #grn-items-table .qty-block,
                                        #grn-items-table .status-block,
                                        #grn-items-table .rate-block {
                                            display: flex;
                                            flex-direction: column;
                                            gap: 0.75rem;
                                        }
                                        #grn-items-table .field-group {
                                            text-align: left;
                                        }
                                        #grn-items-table .field-group label {
                                            display: block;
                                            margin-bottom: 0.35rem;
                                            font-size: 0.78rem;
                                            font-weight: 700;
                                            color: #2f3349;
                                            line-height: 1.35;
                                        }
                                        #grn-items-table .item-design-cell {
                                            min-width: 220px;
                                            vertical-align: middle;
                                            text-align: center;
                                        }
                                        #grn-items-table .item-image-cell {
                                            min-width: 130px;
                                            vertical-align: middle;
                                            text-align: center;
                                        }
                                        #grn-items-table .art-no-cell {
                                            min-width: 140px;
                                            vertical-align: middle;
                                            text-align: center;
                                        }
                                        #grn-items-table .item-image-cell .form-control {
                                            margin-bottom: 0.75rem;
                                        }
                                        #grn-items-table .art-no-cell .form-control,
                                        #grn-items-table .item-image-cell .form-control {
                                            text-align: center;
                                        }
                                        #grn-items-table .fabric-type-cell {
                                            min-width: 260px;
                                            vertical-align: middle !important;
                                            text-align: center;
                                        }
                                        #grn-items-table .qty-cell {
                                            min-width: 190px;
                                        }
                                        #grn-items-table .rate-cell {
                                            min-width: 190px;
                                        }
                                        #grn-items-table .status-cell {
                                            min-width: 210px;
                                        }
                                        #grn-items-table th:nth-child(1),
                                        #grn-items-table td:nth-child(1) {
                                            width: 90px;
                                        }
                                        #grn-items-table th:nth-child(1) {
                                            white-space: nowrap;
                                        }
                                        #grn-items-table th:nth-child(2),
                                        #grn-items-table td:nth-child(2) {
                                            width: 95px;
                                        }
                                        #grn-items-table th:nth-child(3),
                                        #grn-items-table td:nth-child(3) {
                                            width: 220px;
                                        }
                                        #grn-items-table th:nth-child(4),
                                        #grn-items-table td:nth-child(4) {
                                            width: 135px;
                                        }
                                        #grn-items-table th:nth-child(5),
                                        #grn-items-table td:nth-child(5) {
                                            width: 140px;
                                        }
                                        #grn-items-table th:nth-child(6),
                                        #grn-items-table td:nth-child(6) {
                                            width: 95px;
                                        }
                                        #grn-items-table th:nth-child(7),
                                        #grn-items-table td:nth-child(7) {
                                            width: 85px;
                                        }
                                        #grn-items-table th:nth-child(8),
                                        #grn-items-table td:nth-child(8) {
                                            width: 260px;
                                        }
                                        #grn-items-table th:nth-child(9),
                                        #grn-items-table td:nth-child(9) {
                                            width: 230px;
                                        }
                                        #grn-items-table th:nth-child(10),
                                        #grn-items-table td:nth-child(10) {
                                            width: 230px;
                                        }
                                        #grn-items-table th:nth-child(11),
                                        #grn-items-table td:nth-child(11) {
                                            width: 190px;
                                        }
                                        #grn-items-table th:nth-child(12),
                                        #grn-items-table td:nth-child(12) {
                                            width: 210px;
                                        }
                                        @media (max-width: 1399.98px) {
                                            #grn-items-table {
                                                min-width: 1800px;
                                            }
                                        }
                                    </style>

                                    <div class="row g-4">
                                        <div class="col-md-6 col-xl-4">
                                            <div class="form-floating form-floating-outline">
                                                <input type="hidden" name="supplier_name" id="supplier_name_hidden" value="{{ old('supplier_name', ($grn && $grn->supplier) ? ($grn->supplier->name . ($grn->supplier->code ? ' (' . $grn->supplier->code . ')' : '')) : '') }}">
                                                <input type="text" class="form-control" id="supplier_display" value="{{ old('supplier_name', ($grn && $grn->supplier) ? ($grn->supplier->name . ($grn->supplier->code ? ' (' . $grn->supplier->code . ')' : '')) : '') }}" readonly>
                                                <label>Supplier</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl-4">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" name="supplier_invoice_date" id="sup_inv_date" autocomplete="off" class="form-control sup_inv_date @error('supplier_invoice_date') is-invalid @enderror" value="{{ old('supplier_invoice_date', $grn ? $grn->supplier_invoice_date->format('d-m-Y') : '') }}" />
                                                <label>Supplier Invoice Date * </label>
                                            </div>
                                            @error('supplier_invoice_date')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 col-xl-4">
                                            <div class="form-floating form-floating-outline">
                                                <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror" data-placeholder="Select Status">
                                                    <option value="">Select Status</option>
                                                    <option value="Draft" {{ old('status', $grn->status ?? 'Received') == 'Draft' ? 'selected' : '' }}>Draft</option>
                                                    <option value="Received" {{ old('status', $grn->status ?? 'Received') == 'Received' ? 'selected' : '' }}>Received</option>
                                                    <option value="Partially Received" {{ old('status', $grn->status ?? 'Received') == 'Partially Received' ? 'selected' : '' }}>Partially Received</option>
                                                    <option value="Invoiced" {{ old('status', $grn->status ?? 'Received') == 'Invoiced' ? 'selected' : '' }}>Invoiced</option>
                                                    <option value="Cancelled" {{ old('status', $grn->status ?? 'Received') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                </select>
                                                <label>Status *</label>
                                            </div>
                                            @error('status')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- ===================== FIX: Table wrapped in constrained div ===================== --}}
                                        <div class="col-12 mt-5">
                                            <div class="grn-table-wrapper">
                                                <table class="table table-bordered align-middle text-center mb-0" id="grn-items-table">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th rowspan="2">Select</th>
                                                            <th rowspan="2">S.No.</th>
                                                            <th rowspan="2">Supplier Design Name(Code)</th>
                                                            <th rowspan="2">Item Image</th>
                                                            <th rowspan="2">Art No. *</th>
                                                            <th rowspan="2">Width</th>
                                                            <th rowspan="2">UOM</th>
                                                            <th rowspan="2">Fabric Type</th>
                                                            <th colspan="2">QUANTITY</th>
                                                            <th rowspan="2">RATE & AMOUNT</th>
                                                            <th rowspan="2">STATUS & LOCATION *</th>
                                                        </tr>
                                                        <tr>
                                                            <th>Ordered / Inv<br>/ Rec</th>
                                                            <th>Acc / Rej<br>/ Bal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $oldItems = old('items');
                                                            $itemsToLoop = $oldItems ?: ($grn ? $grn->grnEntryItems : []);
                                                            $itCount = 1;
                                                        @endphp
                                                        @foreach($itemsToLoop as $idx => $item)
                                                            @php
                                                                $itemObj = is_array($item) ? (object) $item : $item;
                                                                if (is_array($item)) {
                                                                    $dbItem = \App\Models\PurchaseInvoiceItem::with(['uom', 'rawMaterial', 'purchaseOrderItem'])->find($item['purchase_invoice_item_id'] ?? 0);
                                                                    if ($dbItem && $dbItem->rawMaterial && $dbItem->rawMaterial->store_category_id == 1 && $dbItem->purchaseOrderItem && strval($dbItem->purchaseOrderItem->supplier_design_name) !== '') {
                                                                        $designName = $dbItem->purchaseOrderItem->supplier_design_name;
                                                                    } else {
                                                                        $designName = ($dbItem && $dbItem->rawMaterial) ? ($dbItem->rawMaterial->name . ' (' . $dbItem->rawMaterial->code . ')') : 'Item ' . ($idx + 1);
                                                                    }
                                                                    $uomName = ($dbItem && $dbItem->uom) ? $dbItem->uom->uom_code : 'MTR';
                                                                    $alreadyReceived = \App\Models\GrnEntryItem::where('purchase_invoice_item_id', $item['purchase_invoice_item_id'] ?? 0)->where('grn_entry_id', '!=', $grn->id ?? 0)->sum('qty_received');
                                                                    $qtyOrdered = ($dbItem->quantity ?? 0);
                                                                    $initialBalance = ($qtyOrdered - $alreadyReceived - ($item['qty_received'] ?? 0));
                                                                } else {
                                                                    if ($item->purchaseInvoiceItem && $item->purchaseInvoiceItem->rawMaterial && $item->purchaseInvoiceItem->rawMaterial->store_category_id == 1 && $item->purchaseInvoiceItem->purchaseOrderItem && strval($item->purchaseInvoiceItem->purchaseOrderItem->supplier_design_name) !== '') {
                                                                        $designName = $item->purchaseInvoiceItem->purchaseOrderItem->supplier_design_name;
                                                                    } else {
                                                                        $designName = ($item->purchaseInvoiceItem && $item->purchaseInvoiceItem->rawMaterial) ? ($item->purchaseInvoiceItem->rawMaterial->name . ' (' . $item->purchaseInvoiceItem->rawMaterial->code . ')') : 'Unknown';
                                                                    }
                                                                    $uomName = ($item->purchaseInvoiceItem && $item->purchaseInvoiceItem->uom) ? $item->purchaseInvoiceItem->uom->uom_code : 'MTR';
                                                                    $alreadyReceived = \App\Models\GrnEntryItem::where('purchase_invoice_item_id', $item->purchase_invoice_item_id)->where('grn_entry_id', '!=', $grn->id ?? 0)->sum('qty_received');
                                                                    $qtyOrdered = ($item->purchaseInvoiceItem->quantity ?? 0);
                                                                    $initialBalance = ($qtyOrdered - $alreadyReceived - ($item->qty_received ?? 0));
                                                                }
                                                            @endphp

                                                            @php
                                                                $selectedColorId = is_array($item)
                                                                    ? ($item['color_id'] ?? null)
                                                                    : ($item->color_id
                                                                        ?? $item->purchaseInvoiceItem?->purchaseOrderItem?->color_id
                                                                        ?? null);
                                                                $selectedColorName = '-';
                                                                if (!empty($selectedColorId)) {
                                                                    $selectedColor = $colors->firstWhere('id', (int) $selectedColorId);
                                                                    $selectedColorName = $selectedColor->color_name ?? 'N/A';
                                                                } elseif (!is_array($item) && ($item->variants->count() ?? 0) === 1) {
                                                                    $selectedColorName = $item->variants->first()->color->color_name ?? '-';
                                                                }

                                                                $piItemId = is_array($item) ? ($item['purchase_invoice_item_id'] ?? 0) : ($item->purchase_invoice_item_id ?? 0);
                                                                $isSplit = false;
                                                                if ($piItemId > 0) {
                                                                    $isSplit = collect($itemsToLoop)->where(function ($it) use ($piItemId) {
                                                                        return (is_array($it) ? ($it['purchase_invoice_item_id'] ?? 0) : ($it->purchase_invoice_item_id ?? 0)) == $piItemId;
                                                                    })->count() > 1;
                                                                }
                                                            @endphp
                                                            <tr class="item-row {{ $isSplit ? 'split-row' : '' }}" data-index="{{ $idx }}">
                                                                <td>
                                                                    <input type="checkbox" class="row-select form-check-input" {{ (is_array($item) ? ($item['row_selected'] ?? false) : true) ? 'checked' : '' }}>
                                                                    <input type="hidden" name="items[{{$idx}}][row_selected]" value="{{ (is_array($item) ? ($item['row_selected'] ?? 0) : 1) }}" class="row-selected-input">
                                                                </td>
                                                                <td class="text-nowrap">
                                                                    <span class="row-it-count">{{ $itCount++ }}</span>
                                                                    <button type="button" class="btn btn-sm btn-outline-info ms-1 add-split-row" data-bs-toggle="tooltip" title="Add split row for this item"><i class="ri ri-add-line"></i></button>
                                                                    <input type="hidden" name="items[{{$idx}}][id]" value="{{ $itemObj->id ?? '' }}">
                                                                </td>
                                                                <td class="item-design-cell">
                                                                    {{ $designName }}
                                                                    <button type="button" class="btn btn-warning btn-sm btn-variants" data-index="{{ $idx }}" data-ordered="{{ $itemObj->qty_ordered }}" {{ ((is_array($item) ? ($item['row_selected'] ?? false) : true) && ($itemObj->qty_received ?? 0) > 0) ? '' : 'disabled' }}>Add Variants</button>
                                                                    <div class="variants-data-container">
                                                                        @php $variants = is_array($item) ? ($item['variants'] ?? []) : $item->variants; @endphp
                                                                        @foreach($variants as $vIdx => $v)
                                                                            @php $vObj = is_array($v) ? (object) $v : $v; @endphp
                                                                            <input type="hidden" name="items[{{$idx}}][variants][{{$vIdx}}][color_id]" value="{{ $vObj->color_id }}">
                                                                            <input type="hidden" name="items[{{$idx}}][variants][{{$vIdx}}][qty]" value="{{ $vObj->qty ?? ($vObj->qty_received ?? 0) }}">
                                                                        @endforeach
                                                                    </div>
                                                                    @error("items.$idx.variants") <div class="text-danger small">{{ $message }}</div> @enderror
                                                                </td>
                                                                <td class="item-image-cell">
                                                                    <input type="file" name="items[{{$idx}}][item_image]" class="form-control" accept="image/jpeg,image/jpg,image/png" {{ (is_array($item) ? ($item['row_selected'] ?? false) : true) ? '' : 'disabled' }}>
                                                                    @if(isset($itemObj->image) && $itemObj->image)
                                                                        <input type="hidden" name="items[{{$idx}}][old_image]" value="{{ $itemObj->image }}">
                                                                        <img src="{{ url('uploads/grn_items/' . $itemObj->image) }}" width="40" class="mt-1 border rounded cursor-pointer view-image" data-image="{{ url('uploads/grn_items/' . $itemObj->image) }}" alt="Item">
                                                                    @endif
                                                                    @error("items.$idx.item_image") <div class="text-danger small">{{ $message }}</div> @enderror
                                                                </td>
                                                                <td class="art-no-cell">
                                                                    <input type="hidden" name="items[{{$idx}}][purchase_invoice_item_id]" value="{{ $itemObj->purchase_invoice_item_id }}">
                                                                    <input type="hidden" name="items[{{$idx}}][store_category_id]" value="{{ is_array($item) ? ($item['store_category_id'] ?? 0) : ($item->purchaseInvoiceItem->rawMaterial->store_category_id ?? 0) }}">
                                                                    <input type="text" name="items[{{$idx}}][art_no]" value="{{ $itemObj->art_no }}"
                                                                        class="form-control art-no-input @error('items.' . $idx . '.art_no') is-invalid @enderror"
                                                                        {{ (isset($itemObj->purchaseInvoiceItem->rawMaterial->store_category_id) && $itemObj->purchaseInvoiceItem->rawMaterial->store_category_id == 2) ? 'readonly style=background-color:#e9ecef;' : '' }}>
                                                                    @error("items.$idx.art_no") <div class="text-danger small">{{ $message }}</div> @enderror
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        $width = '-';
                                                                        if (is_array($item)) {
                                                                            $width = $dbItem->purchaseOrderItem->fabricWidth->width ?? '-';
                                                                        } else {
                                                                            $width = $item->purchaseInvoiceItem->purchaseOrderItem->fabricWidth->width ?? '-';
                                                                        }
                                                                    @endphp
                                                                    {{ $width }}
                                                                </td>
                                                                <td>{{ $uomName }}</td>
                                                                <td class="fabric-type-cell" style="vertical-align:middle; text-align:center;">
                                                                    <input type="hidden" name="items[{{$idx}}][fabric_type_id]" value="{{ $itemObj->fabric_type_id ?? '' }}">
                                                                    <input type="hidden" name="items[{{$idx}}][color_id]" value="{{ $selectedColorId }}" class="row-color-id">
                                                                    <span class="d-none row-color-name">{{ $selectedColorName }}</span>
                                                                    <div style="display:flex; align-items:center; justify-content:center; width:100%;">
                                                                        <select class="form-control select2" disabled style="width:100%; text-align:center;">
                                                                            <option value="">Select Fabric Type</option>
                                                                            @foreach($fabricTypes as $ft)
                                                                            <option value="{{ $ft->id }}" {{ ($itemObj->fabric_type_id ?? '') == $ft->id ? 'selected' : '' }}>{{ $ft->fabric_type }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    @error("items.$idx.fabric_type_id") <div class="text-danger small">{{ $message }}</div> @enderror
                                                                </td>
                                                                <td class="qty-cell">
                                                                    <div class="qty-block">
                                                                    <div class="field-group">
                                                                        <label>Ordered:</label>
                                                                        <input type="number" step="0.01" name="items[{{$idx}}][qty_ordered]" value="{{ $qtyOrdered }}" class="qty-ordered form-control" readonly>
                                                                        <div class="split-summary-area" id="split-summary-{{$idx}}"></div>
                                                                    </div>
                                                                    <div class="field-group">
                                                                        <label>Invoiced:</label>
                                                                        <input type="number" step="0.01" value="{{ $alreadyReceived }}" class="qty-already-received form-control" readonly disabled>
                                                                    </div>
                                                                    <div class="field-group">
                                                                        <label>Received *:</label>
                                                                        <input type="number" step="0.01" name="items[{{$idx}}][qty_received]" value="{{ $itemObj->qty_received }}" class="qty-received form-control @error('items.' . $idx . '.qty_received') is-invalid @enderror" {{ (is_array($item) ? ($item['row_selected'] ?? false) : true) ? '' : 'readonly' }}>
                                                                        <div class="qty-error text-danger small" style="display:none;">Cannot exceed ordered qty</div>
                                                                        @error("items.$idx.qty_received") <div class="text-danger small">{{ $message }}</div> @enderror
                                                                    </div>
                                                                    </div>
                                                                </td>
                                                                <td class="qty-cell">
                                                                    <div class="qty-block">
                                                                    <div class="field-group">
                                                                        <label>Accepted *:</label>
                                                                        <input type="number" step="0.01" name="items[{{$idx}}][qty_accepted]" value="{{ $itemObj->qty_accepted }}" class="qty-accepted form-control @error('items.' . $idx . '.qty_accepted') is-invalid @enderror" {{ (is_array($item) ? ($item['row_selected'] ?? false) : true) ? '' : 'readonly' }}>
                                                                        <div class="qty-acc-error text-danger small" style="display:none;">Cannot exceed received qty</div>
                                                                        @error("items.$idx.qty_accepted") <div class="text-danger small">{{ $message }}</div> @enderror
                                                                    </div>
                                                                    <div class="field-group">
                                                                        <label>Rejected:</label>
                                                                        <input type="number" step="0.01" name="items[{{$idx}}][qty_rejected]" value="{{ $itemObj->qty_rejected }}" class="qty-rejected form-control">
                                                                    </div>
                                                                    <div class="field-group">
                                                                        <label>Balanced:</label>
                                                                        <input type="number" step="0.01" name="items[{{$idx}}][qty_balanced]" value="{{ $initialBalance }}" class="qty-balanced form-control" readonly>
                                                                    </div>
                                                                    </div>
                                                                </td>
                                                                <td class="rate-cell">
                                                                    <div class="rate-block">
                                                                    <div class="field-group">
                                                                        <label>Rate:</label>
                                                                        <input type="number" step="0.01" name="items[{{$idx}}][rate]" value="{{ $itemObj->rate }}" class="rate-input form-control @error('items.' . $idx . '.rate') is-invalid @enderror" readonly>
                                                                    </div>
                                                                    <div class="field-group">
                                                                        <label>Amount:</label>
                                                                        <input type="number" step="0.01" name="items[{{$idx}}][amount]" value="{{ $itemObj->amount }}" class="amount-input form-control" readonly>
                                                                    </div>
                                                                    </div>
                                                                </td>
                                                                <td class="status-cell">
                                                                    <div class="status-block">
                                                                    <div class="field-group">
                                                                        <label>QC Status:</label>
                                                                        <select class="form-control select2 @error('items.' . $idx . '.quality_check_status') is-invalid @enderror" name="items[{{$idx}}][quality_check_status]" {{ (is_array($item) ? ($item['row_selected'] ?? false) : true) ? '' : 'disabled' }}>
                                                                            <option value="">Select Status</option>
                                                                            <option value="Pass" {{ ($itemObj->quality_check_status ?? '') == 'Pass' ? 'selected' : '' }}>Pass</option>
                                                                            <option value="Fail" {{ ($itemObj->quality_check_status ?? '') == 'Fail' ? 'selected' : '' }}>Fail</option>
                                                                            <option value="Hold" {{ ($itemObj->quality_check_status ?? '') == 'Hold' ? 'selected' : '' }}>Hold</option>
                                                                        </select>
                                                                        @error("items.$idx.quality_check_status") <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                                                    </div>
                                                                    <div class="field-group">
                                                                        <label>Store Location:</label>
                                                                        <select class="form-control select2 @error('items.' . $idx . '.store_location_id') is-invalid @enderror" name="items[{{$idx}}][store_location_id]" {{ (is_array($item) ? ($item['row_selected'] ?? false) : true) ? '' : 'disabled' }}>
                                                                            <option value="">Select Store Location</option>
                                                                            @foreach($storeLocations as $loc)
                                                                                <option value="{{ $loc->id }}" {{ ($itemObj->store_location_id ?? '') == $loc->id ? 'selected' : '' }}>{{ $loc->store_location }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error("items.$idx.store_location_id") <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                                                    </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>{{-- end grn-table-wrapper --}}
                                        </div>
                                    </div>
                                </div>{{-- end show_item_det --}}
                            </div>

                            <div class="text-end col-lg-12 mt-4">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('grn_entries') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Variant Modal -->
    <div class="modal fade" id="variantModal" tabindex="-1" aria-labelledby="variantModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary d-flex justify-content-between align-items-center">
                    <h5 class="modal-title mb-0 text-white" id="variantModalLabel">Add Variants by Color</h5>
                    <h5 class="mb-0 text-white" id="modal-qty-summary">Ordered: 0.00 | Received: 0.00</h5>
                    <button type="button" class="btn-close ms-2 btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4" id="variant-color-picker-wrap">
                        <label class="form-label fw-semibold">Select Colors</label>
                        <select id="variantColors" class="form-control select2" data-placeholder="Select Color">
                            <option value="">Select Color</option>
                            @foreach($colors as $col)
                                <option value="{{ $col->id }}" data-name="{{ $col->color_name }}">{{ $col->color_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle" id="variantQtyTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Color</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div id="variant-error" class="text-danger mt-2 fw-bold" style="display:none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="save-variants">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let maxIndex = 0;
            let lockedVariantColor = false;
            let activeRowIndex = null;

            $('.item-row').each(function() {
                let idx = parseInt($(this).data('index'));
                if (idx > maxIndex) maxIndex = idx;
            });

            function renderVariantRows(colors, existingData = {}) {
                let tbody = $('#variantQtyTable tbody');
                tbody.empty();
                colors.forEach(function(color) {
                    let colorId = String(color.id);
                    let existingQty = existingData[colorId] || existingData[color.id] || '';
                    tbody.append(`<tr data-color-id="${colorId}"><td>${color.name}</td><td><input type="number" step="0.01" class="form-control var-qty" value="${existingQty}" min="0"></td></tr>`);
                });
            }

            function setVariantModalMode(selectedColor, existingData = {}, isLocked = false) {
                lockedVariantColor = isLocked;
                $('#variantColors').prop('disabled', isLocked);
                if (selectedColor && selectedColor.id) {
                    $('#variantColors').val(String(selectedColor.id)).trigger('change.select2');
                    renderVariantRows([selectedColor], existingData);
                } else {
                    $('#variantColors').val('').trigger('change.select2');
                    renderVariantRows([], existingData);
                }
            }

            $(document).on('click', '.add-split-row', function() {
                let originalRow = $(this).closest('.item-row');
                let selectedFabricTypeId = originalRow.find('input[name*="[fabric_type_id]"]').val() || originalRow.find('.fabric-type-cell select').val() || '';
                $('.item-row').each(function() {
                    let idx = parseInt($(this).attr('data-index'));
                    if (idx > maxIndex) maxIndex = idx;
                });
                maxIndex++;
                let newIndex = maxIndex;
                let newRow = originalRow.clone();
                newRow.attr('data-index', newIndex);
                newRow.addClass('split-row');

                newRow.find('input, select').each(function() {
                    let name = $(this).attr('name');
                    if (name) $(this).attr('name', name.replace(/items\[\d+\]/, 'items[' + newIndex + ']'));
                    let id = $(this).attr('id');
                    if (id) $(this).attr('id', id.replace(/-\d+$/, '-' + newIndex));

                    let nameAttr = $(this).attr('name') || '';
                    if ($(this).hasClass('row-select')) {
                        $(this).prop('checked', true);
                    } else if ($(this).hasClass('row-selected-input')) {
                        $(this).val(1);
                    } else if (nameAttr.includes('[id]')) {
                        $(this).val('');
                    } else if ($(this).hasClass('qty-received') || $(this).hasClass('qty-accepted') || $(this).hasClass('qty-rejected') || $(this).hasClass('amount-input')) {
                        $(this).val(0);
                    } else if ($(this).hasClass('art-no-input') && !$(this).prop('readonly')) {
                        $(this).val('');
                    } else if ($(this).attr('type') === 'file') {
                        $(this).val('');
                    }
                });

                newRow.find('.select2-container').remove();
                newRow.find('.select2').removeClass('select2-hidden-accessible').removeAttr('data-select2-id').find('option').removeAttr('data-select2-id');
                newRow.find('.variants-data-container').empty();
                newRow.find('input[name*="[fabric_type_id]"]').val(selectedFabricTypeId);
                newRow.find('.fabric-type-cell select').val(selectedFabricTypeId).attr('data-val', selectedFabricTypeId);

                newRow.find('.btn-variants').prop('disabled', true);
                newRow.find('.split-summary-area').empty();
                newRow.find('.add-split-row').removeClass('btn-outline-info add-split-row').addClass('btn-outline-danger remove-split-row').attr('title', 'Remove this split row').html('<i class="ri ri-subtract-line"></i>');

                let piId = originalRow.find('input[name*="[purchase_invoice_item_id]"]').val();
                let storeCategoryId = originalRow.find('input[name*="[store_category_id]"]').val();
                let lastRowInGroup = originalRow;
                let groupRows = [];

                $('.item-row').each(function() {
                    if ($(this).find('input[name*="[purchase_invoice_item_id]"]').val() === piId) {
                        lastRowInGroup = $(this);
                        groupRows.push($(this));
                    }
                });

                if (storeCategoryId != 1) {
                    let currentArtNo = originalRow.find('.art-no-input').val();
                    let baseArtNo = currentArtNo.replace(/-[0-9]+$/, '');
                    let maxSuffix = 0;
                    groupRows.forEach(row => {
                        let rowArtNo = row.find('.art-no-input').val();
                        let match = rowArtNo.match(/-([0-9]+)$/);
                        if (match) {
                            let suffix = parseInt(match[1]);
                            if (suffix > maxSuffix) maxSuffix = suffix;
                        }
                    });
                    newRow.find('.art-no-input').val(baseArtNo + '-' + (maxSuffix + 1));
                }

                newRow.insertAfter(lastRowInGroup);
                initSelect2();
                newRow.find('.fabric-type-cell select').val(selectedFabricTypeId).trigger('change.select2');
                updateGroupBalances(piId);
                validateForm();
                updateSerialNumbers();
            });

            $(document).on('click', '.remove-split-row', function() {
                let row = $(this).closest('.item-row');
                let piId = row.find('input[name*="[purchase_invoice_item_id]"]').val();
                row.remove();
                updateGroupBalances(piId);
                validateForm();
                updateSerialNumbers();
            });

            function updateSerialNumbers() {
                $('.row-it-count').each(function(index) {
                    $(this).text(index + 1);
                });
            }

            function initSelect2() {
                $('.select2').each(function() {
                    let parent = $(this).closest('.modal-content').length ? $(this).closest('.modal-content') : null;
                    $(this).select2({ dropdownParent: parent });
                });
            }
            initSelect2();

            let fabrics_options = '';
            @foreach($fabricTypes as $ft)
                fabrics_options += `<option value="{{ $ft->id }}">{{ addslashes($ft->fabric_type) }}</option>`;
            @endforeach

            let locations_options = '';
            @foreach($storeLocations as $loc)
                locations_options += `<option value="{{ $loc->id }}">{{ addslashes($loc->store_location) }}</option>`;
            @endforeach

            $('#po_no').on('change', function() {
                let po_id = $(this).val();
                if (po_id) {
                    $('#show_item_det').removeClass('d-none');
                    $('#supplier_display').val('Loading...');
                    $('#grn-items-table tbody').empty().append('<tr><td colspan="12" class="text-center">Loading items...</td></tr>');

                    $.get("{{ url('grn_entries/get-invoice-details') }}/" + po_id, function(res) {
                        $('#supplier_display').val(res.supplier_name);
                        $('#supplier_name_hidden').val(res.supplier_name);
                        $('#sup_inv_date').val(res.invoice_date);

                        let tbody = $('#grn-items-table tbody').empty();
                        res.items.forEach((item, idx) => {
                            tbody.append(`
                                <tr class="item-row" data-index="${idx}">
                                    <td>
                                        <input type="checkbox" class="row-select form-check-input" checked>
                                        <input type="hidden" name="items[${idx}][row_selected]" value="1" class="row-selected-input">
                                    </td>
                                    <td class="text-nowrap">
                                        <span class="row-it-count">${idx + 1}</span>
                                        <button type="button" class="btn btn-sm btn-outline-info ms-1 add-split-row" data-bs-toggle="tooltip" title="Add split row for this item"><i class="ri ri-add-line"></i></button>
                                    </td>
                                    <td class="item-design-cell">
                                        ${item.design_name}
                                        <button type="button" class="btn btn-warning btn-sm btn-variants" data-index="${idx}" data-ordered="${item.qty_ordered}" disabled>Add Variants</button>
                                        <div class="variants-data-container"></div>
                                    </td>
                                    <td class="item-image-cell">
                                        <input type="file" name="items[${idx}][item_image]" class="form-control" accept="image/jpeg,image/jpg,image/png">
                                    </td>
                                    <td class="art-no-cell">
                                        <input type="hidden" name="items[${idx}][purchase_invoice_item_id]" value="${item.id}">
                                        <input type="hidden" name="items[${idx}][store_category_id]" value="${item.store_category_id}">
                                        <input type="text" name="items[${idx}][art_no]" value="${item.art_no}"
                                            class="form-control art-no-input" ${item.store_category_id == 2 ? 'readonly style="background-color:#e9ecef;"' : ''}>
                                    </td>
                                    <td>${item.width}</td>
                                    <td>${item.uom}</td>
                                    <td class="fabric-type-cell" style="vertical-align:middle; text-align:center;">
                                        <input type="hidden" name="items[${idx}][fabric_type_id]" value="${item.fabric_type_id || ''}">
                                        <input type="hidden" name="items[${idx}][color_id]" value="${item.color_id || ''}" class="row-color-id">
                                        <span class="d-none row-color-name">${item.color_name || '-'}</span>
                                        <div style="display:flex; align-items:center; justify-content:center; width:100%;">
                                            <select class="form-control select2" data-val="${item.fabric_type_id || ''}" disabled style="width:100%; text-align:center;"><option value="">Select Fabric</option>${fabrics_options}</select>
                                        </div>
                                    </td>
                                    <td class="qty-cell">
                                        <div class="qty-block">
                                        <div class="field-group">
                                            <label>Ordered:</label>
                                            <input type="number" step="0.01" name="items[${idx}][qty_ordered]" value="${item.qty_ordered}" class="qty-ordered form-control" readonly>
                                            <input type="hidden" name="items[${idx}][id]" value="">
                                            <div class="split-summary-area" id="split-summary-${idx}"></div>
                                        </div>
                                        <div class="field-group">
                                            <label>Invoiced:</label>
                                            <input type="number" step="0.01" value="${item.qty_already_received}" class="qty-already-received form-control" readonly disabled>
                                        </div>
                                        <div class="field-group">
                                            <label>Received *:</label>
                                            <input type="number" step="0.01" name="items[${idx}][qty_received]" value="0" class="qty-received form-control">
                                            <div class="qty-error text-danger small" style="display:none;">Cannot exceed ordered qty</div>
                                        </div>
                                        </div>
                                    </td>
                                    <td class="qty-cell">
                                        <div class="qty-block">
                                        <div class="field-group">
                                            <label>Accepted *:</label>
                                            <input type="number" step="0.01" name="items[${idx}][qty_accepted]" value="0" class="qty-accepted form-control">
                                            <div class="qty-acc-error text-danger small" style="display:none;">Cannot exceed received qty</div>
                                        </div>
                                        <div class="field-group">
                                            <label>Rejected:</label>
                                            <input type="number" step="0.01" name="items[${idx}][qty_rejected]" value="0" class="qty-rejected form-control">
                                        </div>
                                        <div class="field-group">
                                            <label>Balanced:</label>
                                            <input type="number" step="0.01" name="items[${idx}][qty_balanced]" value="${(item.qty_ordered - item.qty_already_received).toFixed(2)}" class="qty-balanced form-control" readonly>
                                        </div>
                                        </div>
                                    </td>
                                    <td class="rate-cell">
                                        <div class="rate-block">
                                        <div class="field-group">
                                            <label>Rate:</label>
                                            <input type="number" step="0.01" name="items[${idx}][rate]" value="${item.rate}" class="rate-input form-control" readonly>
                                        </div>
                                        <div class="field-group">
                                            <label>Amount:</label>
                                            <input type="number" step="0.01" name="items[${idx}][amount]" value="0" class="amount-input form-control" readonly>
                                        </div>
                                        </div>
                                    </td>
                                    <td class="status-cell">
                                        <div class="status-block">
                                        <div class="field-group">
                                            <label>QC Status:</label>
                                            <select class="form-control select2" name="items[${idx}][quality_check_status]"><option value="">Select</option><option value="Pass">Pass</option><option value="Fail">Fail</option><option value="Hold">Hold</option></select>
                                        </div>
                                        <div class="field-group">
                                            <label>Store Location:</label>
                                            <select class="form-control select2" name="items[${idx}][store_location_id]"><option value="">Select Location</option>${locations_options}</select>
                                        </div>
                                        </div>
                                    </td>
                                </tr>
                            `);
                        });

                        $('#grn-items-table tbody select[data-val]').each(function() {
                            let v = $(this).data('val');
                            if (v) $(this).val(v);
                        });

                        initSelect2();
                        updateSerialNumbers();

                        $('.item-row').each(function() {
                            let idx = parseInt($(this).attr('data-index'));
                            if (idx > maxIndex) maxIndex = idx;
                        });
                    });
                } else {
                    $('#show_item_det').addClass('d-none');
                    $('#supplier_display').val('');
                    $('#sup_inv_date').val('');
                    $('#grn-items-table tbody').empty();
                }
            });

            $(document).on('click', '.btn-variants', function() {
                let row = $(this).closest('.item-row');
                activeRowIndex = row.data('index');
                let ordered = parseFloat($(this).data('ordered')) || 0;
                let currentReceived = parseFloat(row.find('.qty-received').val()) || 0;

                $('#modal-qty-summary').data('ordered', ordered);
                updateModalSummary(currentReceived, ordered);
                $('#variant-error').hide();

                let container = $(this).closest('td').find('.variants-data-container');
                let existingData = {};
                let fixedColorId = row.find('.row-color-id').val();
                let fixedColorName = $.trim(row.find('.row-color-name').text());
                let selectedColorId = '';

                container.find('input[name$="[color_id]"]').each(function() {
                    let color_id = $(this).val();
                    let qty = $(this).next().val();
                    existingData[String(color_id)] = qty;
                    if (!selectedColorId) selectedColorId = String(color_id);
                });

                if (fixedColorId && fixedColorName && fixedColorName !== '-') {
                    let presetQty = existingData[String(fixedColorId)] || currentReceived || '';
                    setVariantModalMode({ id: String(fixedColorId), name: fixedColorName }, { [String(fixedColorId)]: presetQty }, false);
                } else {
                    let colorIdToUse = selectedColorId || '';
                    let colorNameToUse = colorIdToUse ? ($(`#variantColors option[value="${colorIdToUse}"]`).data('name') || 'Unknown') : '';
                    setVariantModalMode(colorIdToUse ? { id: colorIdToUse, name: colorNameToUse } : null, existingData, false);
                    if (!colorIdToUse && currentReceived > 0) $('#variantQtyTable tbody').empty();
                }

                validateVariantTotal();
                $('#variantModal').modal('show');
            });

            $('#variantColors').on('change', function() {
                if (lockedVariantColor) return;
                let colorId = $(this).val();
                let existingData = {};
                $('#variantQtyTable tbody').find('tr').each(function() {
                    existingData[$(this).data('color-id')] = $(this).find('input').val();
                });
                if (!colorId) { renderVariantRows([], existingData); return; }
                renderVariantRows([{ id: String(colorId), name: $(`#variantColors option[value="${colorId}"]`).data('name') || 'Unknown' }], existingData);
            });

            $(document).on('input', '.var-qty', function() { validateVariantTotal(); });

            function validateVariantTotal() {
                let total = 0;
                $('.var-qty').each(function() { total += parseFloat($(this).val()) || 0; });
                let row = $(`.item-row[data-index="${activeRowIndex}"]`);
                let received = parseFloat(row.find('.qty-received').val()) || 0;
                let ordered = parseFloat($('#modal-qty-summary').data('ordered')) || 0;
                updateModalSummary(total, received);
                if (total > received) {
                    $('#variant-error').text(`Total variant quantity (${total.toFixed(2)}) cannot exceed received quantity (${received.toFixed(2)})`).show();
                    $('#save-variants').prop('disabled', true);
                } else {
                    $('#variant-error').hide();
                    $('#save-variants').prop('disabled', false);
                }
                return total;
            }

            function updateModalSummary(totalVariants = 0, received = null) {
                let ordered = parseFloat($('#modal-qty-summary').data('ordered')) || 0;
                if (received === null && activeRowIndex !== null) {
                    received = parseFloat($(`.item-row[data-index="${activeRowIndex}"]`).find('.qty-received').val()) || 0;
                }
                $('#modal-qty-summary').text(`Ordered: ${ordered.toFixed(2)} | Received Total: ${received.toFixed(2)} | Variants Sum: ${totalVariants.toFixed(2)}`);
            }

            $('#save-variants').on('click', function() {
                let row = $(`.item-row[data-index="${activeRowIndex}"]`);
                let receivedLimit = parseFloat(row.find('.qty-received').val()) || 0;
                let totalVariants = validateVariantTotal();
                if (totalVariants > receivedLimit) return;

                let container = row.find('.variants-data-container').empty();
                let firstColorId = '', firstColorName = '';

                $('#variantQtyTable tbody tr').each(function(i) {
                    let color_id = $(this).data('color-id');
                    let color_name = $(this).find('td:first').text();
                    let qty = $(this).find('.var-qty').val() || 0;
                    if (parseFloat(qty) > 0) {
                        if (!firstColorId) { firstColorId = color_id; firstColorName = color_name; }
                        container.append(`<input type="hidden" name="items[${activeRowIndex}][variants][${i}][color_id]" value="${color_id}">`);
                        container.append(`<input type="hidden" name="items[${activeRowIndex}][variants][${i}][qty]" value="${qty}">`);
                    }
                });

                if (firstColorId) {
                    row.find('.row-color-id').val(firstColorId);
                    row.find('.row-color-name').text(firstColorName);
                }

                row.find('.qty-received').prop('readonly', totalVariants > 0);
                updateRowCalculations(row);
                $('#variantModal').modal('hide');
            });

            $(document).on('input', '.qty-received, .qty-accepted, .qty-rejected, .rate-input', function() {
                let row = $(this).closest('.item-row');
                if ($(this).hasClass('qty-received')) {
                    let received = parseFloat($(this).val()) || 0;
                    row.find('.qty-accepted').val(received);
                }
                updateRowCalculations(row);
                validateForm();
                let received = parseFloat(row.find('.qty-received').val()) || 0;
                row.find('.btn-variants').prop('disabled', received <= 0);
            });

            function validateForm() {
                let hasError = false;
                let groupTotals = {};
                $('.item-row').each(function() {
                    let isChecked = $(this).find('.row-select').is(':checked');
                    if (!isChecked) return;
                    let piId = $(this).find('input[name*="[purchase_invoice_item_id]"]').val();
                    let ordered = parseFloat($(this).find('.qty-ordered').val()) || 0;
                    let received = parseFloat($(this).find('.qty-received').val()) || 0;
                    let accepted = parseFloat($(this).find('.qty-accepted').val()) || 0;
                    let alreadyReceived = parseFloat($(this).find('.qty-already-received').val()) || 0;

                    if (!groupTotals[piId]) groupTotals[piId] = { totalReceived: 0, ordered, alreadyReceived, rows: [] };
                    groupTotals[piId].totalReceived += received;
                    groupTotals[piId].rows.push($(this));

                    if (accepted > received) {
                        $(this).find('.qty-accepted').addClass('is-invalid');
                        $(this).find('.qty-acc-error').show();
                        hasError = true;
                    } else {
                        $(this).find('.qty-accepted').removeClass('is-invalid');
                        $(this).find('.qty-acc-error').hide();
                    }
                });

                for (let piId in groupTotals) {
                    let group = groupTotals[piId];
                    let balance = group.ordered - group.alreadyReceived;
                    let groupError = group.totalReceived > balance;
                    group.rows.forEach(row => {
                        if (groupError) {
                            row.find('.qty-received').addClass('is-invalid');
                            row.find('.qty-error').text(`Group total (${group.totalReceived.toFixed(2)}) exceeds balance (${balance.toFixed(2)})`).show();
                            hasError = true;
                        } else {
                            row.find('.qty-received').removeClass('is-invalid');
                            row.find('.qty-error').hide();
                        }
                    });
                }
                $('button[type="submit"]').prop('disabled', hasError);
            }

            function updateGroupBalances(piId) {
                let groupRows = [], totalReceivedInGroup = 0, ordered = 0, alreadyReceived = 0;
                $('.item-row').each(function() {
                    if ($(this).find('input[name*="[purchase_invoice_item_id]"]').val() === piId) {
                        groupRows.push($(this));
                        if ($(this).find('.row-select').is(':checked')) totalReceivedInGroup += parseFloat($(this).find('.qty-received').val()) || 0;
                        ordered = parseFloat($(this).find('.qty-ordered').val()) || 0;
                        alreadyReceived = parseFloat($(this).find('.qty-already-received').val()) || 0;
                    }
                });
                let currentBalance = ordered - alreadyReceived - totalReceivedInGroup;
                groupRows.forEach(row => row.find('.qty-balanced').val(currentBalance.toFixed(2)));
            }

            function updateRowCalculations(row) {
                let received = parseFloat(row.find('.qty-received').val()) || 0;
                let accepted = parseFloat(row.find('.qty-accepted').val()) || 0;
                let rate = parseFloat(row.find('.rate-input').val()) || 0;
                let piId = row.find('input[name*="[purchase_invoice_item_id]"]').val();
                row.find('.qty-rejected').val((received - accepted).toFixed(2));
                row.find('.amount-input').val((accepted * rate).toFixed(2));
                updateGroupBalances(piId);
            }

            $('.item-row').each(function() {
                let row = $(this);
                updateRowCalculations(row);
                let received = parseFloat(row.find('.qty-received').val()) || 0;
                row.find('.btn-variants').prop('disabled', received <= 0);
            });
            updateSerialNumbers();

            $(document).on('change', '.row-select', function() {
                let row = $(this).closest('.item-row');
                let isChecked = $(this).is(':checked');
                row.find('.row-selected-input').val(isChecked ? 1 : 0);
                row.find('.qty-received').prop('readonly', !isChecked || row.find('.variants-data-container input').length > 0);
                row.find('.qty-accepted').prop('readonly', !isChecked);
                row.find('select').prop('disabled', !isChecked);
                row.find('input[type="file"]').prop('disabled', !isChecked);
                let received = parseFloat(row.find('.qty-received').val()) || 0;
                row.find('.btn-variants').prop('disabled', !isChecked || received <= 0);

                if (!isChecked) {
                    row.find('.qty-received').val(0).prop('readonly', true);
                    row.find('.qty-accepted').val(0).prop('readonly', true);
                    row.find('.qty-rejected').val(0);
                    row.find('.qty-balanced').val((parseFloat(row.find('.qty-ordered').val()) - parseFloat(row.find('.qty-already-received').val())).toFixed(2));
                    row.find('.amount-input').val(0);
                    row.find('select').val('').trigger('change').prop('disabled', true);
                    row.find('input[type="file"]').val('').prop('disabled', true);
                    row.find('.variants-data-container').empty();
                    row.find('.btn-variants').prop('disabled', true);
                    row.find('input, select').removeClass('is-invalid');
                    row.find('.text-danger').hide();
                }
                updateRowCalculations(row);
                validateForm();
            });

            $('.row-select').each(function() {
                let row = $(this).closest('.item-row');
                let isChecked = $(this).is(':checked');
                row.find('.qty-received').prop('readonly', !isChecked || row.find('.variants-data-container input').length > 0);
                row.find('.qty-accepted').prop('readonly', !isChecked);
                row.find('select').prop('disabled', !isChecked);
                row.find('input[type="file"]').prop('disabled', !isChecked);
                let received = parseFloat(row.find('.qty-received').val()) || 0;
                row.find('.btn-variants').prop('disabled', !isChecked || received <= 0);
            });

            let targetedErrors = $('.item-row').find('select[name*="quality_check_status"].is-invalid, select[name*="store_location_id"].is-invalid');
            if (targetedErrors.length > 0) {
                setTimeout(function() {
                    let tableContainer = $('.grn-table-wrapper');
                    if (tableContainer.length > 0) tableContainer.animate({ scrollLeft: tableContainer[0].scrollWidth }, 800);
                }, 500);
            }
        });
    </script>
@endsection

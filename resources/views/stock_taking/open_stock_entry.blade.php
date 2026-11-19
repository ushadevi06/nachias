@extends('layouts.common')
@section('title', (!empty($isEdit) ? 'Edit Open Stock Entry - ' . ($editEntryNo ?? '') : 'Add Open Stock Entry') . ' - ' . env('WEBSITE_NAME'))

@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            @include('flash_messages')
        </div>
        <div class="col-lg-12">
            <form id="form_open_stock_entry" action="{{ url('fg_stock_taking/open-stock-entry') }}" method="POST" class="common-form" autocomplete="off" novalidate>
                @csrf
                @if(!empty($isEdit) && !empty($editEntryNo))
                    <input type="hidden" name="existing_entry_no" value="{{ $editEntryNo }}">
                @endif
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                            <h4 class="mb-0">
                                @if(!empty($isEdit))
                                    Edit Open Stock Entry <span class="badge bg-primary text-white fs-6 ms-2">{{ $editEntryNo }}</span>
                                @else
                                    Add Open Stock Entry
                                @endif
                            </h4>
                            <div class="d-flex gap-2">
                                <a href="{{ url('fg_stock_taking/open-stock-logs') }}" class="btn btn-outline-info">
                                    <i class="ri ri-file-list-3-line me-1"></i> Open Stock Logs
                                </a>
                                <a href="{{ url('fg_stock_taking') }}" class="btn btn-secondary">
                                    <i class="ri ri-arrow-left-line back-arrow"></i> Back to Stock Taking
                                </a>
                            </div>
                        </div>

                        <div class="alert alert-info py-2 px-3 mb-4 d-flex align-items-center gap-2 small">
                            <i class="ri ri-information-line fs-5 text-info"></i>
                            <div><strong>Audit-Only Note:</strong> Items entered here are recorded directly into <strong>Open Stock Entry Logs</strong> for audit tracking and physical count verification. They do not convert to live Finished Goods inventory or modify stock ledger balances.</div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6 col-lg-3">
                                <div class="form-floating form-floating-outline">
                                    <select name="warehouse_id" id="ose_warehouse_id" class="select2 form-select @error('warehouse_id') is-invalid @enderror" data-placeholder="Select Warehouse">
                                        <option value=""></option>
                                        @foreach($warehouses as $wh)
                                            <option value="{{ $wh->id }}" {{ old('warehouse_id', $selectedWarehouseId ?? '') == $wh->id ? 'selected' : '' }}>{{ trim($wh->warehouse_name) }}</option>
                                        @endforeach
                                    </select>
                                    <label for="ose_warehouse_id">Warehouse <span class="text-danger">*</span></label>
                                </div>
                                @error('warehouse_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <div class="form-floating form-floating-outline">
                                    <select name="store_type_id" id="ose_store_type_id" class="select2 form-select @error('store_type_id') is-invalid @enderror" data-placeholder="Select Store Type">
                                        <option value=""></option>
                                        @foreach($storeTypes as $st)
                                            @php
                                                $isDefault = old('store_type_id', $selectedStoreTypeId ?? '') == $st->id || (!old('store_type_id') && empty($selectedStoreTypeId) && stripos($st->store_type_name, 'FINISHED GOODS') !== false);
                                            @endphp
                                            <option value="{{ $st->id }}" {{ $isDefault ? 'selected' : '' }}>{{ trim($st->store_type_name) }}</option>
                                        @endforeach
                                    </select>
                                    <label for="ose_store_type_id">Store Type <span class="text-danger">*</span></label>
                                </div>
                                @error('store_type_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <div class="form-floating form-floating-outline">
                                    <select name="default_store_location_id" id="ose_default_location_id" class="select2 form-select @error('default_store_location_id') is-invalid @enderror" data-placeholder="Select Store Location">
                                        <option value=""></option>
                                        @foreach($storeLocations as $loc)
                                            <option value="{{ $loc->id }}" {{ old('default_store_location_id', $selectedStoreLocationId ?? '') == $loc->id ? 'selected' : '' }}>{{ trim($loc->store_location) }}</option>
                                        @endforeach
                                    </select>
                                    <label for="ose_default_location_id">Default Store Location</label>
                                </div>
                                @error('default_store_location_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="entry_date" id="ose_entry_date" class="form-control flatpickr @error('entry_date') is-invalid @enderror" value="{{ old('entry_date', $entryDate ?? date('d-m-Y')) }}" placeholder="DD-MM-YYYY">
                                    <label for="ose_entry_date">Stock Date <span class="text-danger">*</span></label>
                                </div>
                                @error('entry_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="remarks" id="ose_remarks" class="form-control @error('remarks') is-invalid @enderror" value="{{ old('remarks', $headerRemarks ?? '') }}" placeholder="Enter Remarks">
                                    <label for="ose_remarks">Remarks</label>
                                </div>
                                @error('remarks')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card-header-box border-bottom pb-2 mb-3 mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="mb-0">Stock Items</h5>
                                <span class="badge bg-primary text-white" id="ose_items_count_badge">1 item</span>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-secondary text-uppercase" id="btn_ose_duplicate_last" title="Copy last row to quickly add next size">
                                    <i class="ri ri-file-copy-line me-1"></i> Duplicate Last Row
                                </button>
                                <button type="button" class="btn btn-primary text-uppercase" id="btn_ose_add_row">
                                    <i class="ri ri-add-line me-1"></i> Add More
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive mb-3">
                            <table class="table align-middle" id="ose_items_table">
                                <thead class="table">
                                    <tr class="text-uppercase small">
                                        <th style="width: 50px;" class="text-center">S.No</th>
                                        <th style="min-width: 200px;">Product Name <span class="text-danger">*</span></th>
                                        <th style="min-width: 160px;">Art No <span class="text-danger">*</span></th>
                                        <th style="min-width: 220px;">Style <span class="text-danger">*</span></th>
                                        <th style="min-width: 150px;">Size <span class="text-danger">*</span></th>
                                        <th style="min-width: 160px;">Sleeve</th>
                                        <th style="width: 120px; min-width: 120px;">Qty In <span class="text-danger">*</span></th>
                                        <th style="width: 160px; min-width: 160px;">MRP (₹)</th>
                                        <th style="width: 160px; min-width: 160px;">WSP (₹)</th>
                                        <th style="min-width: 220px;">Location</th>
                                        <th style="min-width: 180px;">SKU / Barcode <span class="text-danger">*</span></th>
                                        <th style="min-width: 150px;">UOM</th>
                                        <th style="min-width: 180px;">Remarks</th>
                                        <th style="width: 70px;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="ose_items_tbody">
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end align-items-center p-3 rounded mb-4 flex-wrap gap-4">
                            <div>Total Qty: <strong class="text-primary fs-5" id="ose_total_qty_display">0</strong> pcs</div>
                            <div>Total Amount: <strong class="text-success fs-5">₹<span id="ose_total_amount_display">0.00</span></strong></div>
                        </div>

                        <div class="button-box mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary text-uppercase px-4" id="btn_ose_submit">{{ !empty($isEdit) ? 'Update Entry' : 'Submit' }}</button>
                            <a href="{{ url('fg_stock_taking/open-stock-logs') }}" class="btn btn-secondary text-uppercase">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="ose_autocomplete_dropdown" class="ose-autocomplete-menu"></div>

<style>
    #ose_autocomplete_dropdown {
        position: absolute;
        z-index: 99999;
        background: #ffffff;
        border: 1px solid #d9dee3;
        border-radius: 0.375rem;
        box-shadow: 0 4px 18px rgba(50, 50, 93, 0.16), 0 2px 6px rgba(0, 0, 0, 0.08);
        max-height: 250px;
        overflow-y: auto;
        display: none;
        padding: 4px 0;
    }
    .ose-autocomplete-item {
        padding: 8px 14px;
        cursor: pointer;
        font-size: 0.875rem;
        color: #566a7f;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background-color 0.12s ease;
    }
    .ose-autocomplete-item:hover,
    .ose-autocomplete-item.active {
        background-color: #f2f3ff;
        color: #696cff;
        font-weight: 600;
    }
    .ose-autocomplete-item mark {
        background-color: transparent;
        color: #696cff;
        font-weight: 700;
        padding: 0;
        text-decoration: underline;
    }
    #ose_items_table .ose-product-name {
        text-transform: uppercase;
    }
    #ose_items_table input.form-control,
    #ose_items_table select.form-select {
        height: 48px;
        min-height: 48px;
        font-size: 0.9375rem;
        padding: 0.5rem 0.875rem;
        border: 1px solid #d9dee3;
        border-radius: 0.375rem;
    }
    #ose_items_table th {
        font-size: 0.8rem;
        letter-spacing: 0.3px;
        background-color: #f8f9fa;
        white-space: nowrap;
        vertical-align: middle;
    }
    #ose_items_table .select2-container {
        width: 100% !important;
    }
    #ose_items_table .select2-container--default .select2-selection--single {
        height: 48px !important;
        min-height: 48px !important;
        border: 1px solid #d9dee3 !important;
        border-radius: 0.375rem !important;
        font-size: 0.9375rem !important;
        background-color: #fff !important;
        display: flex !important;
        align-items: center !important;
    }
    #ose_items_table .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 46px !important;
        padding-left: 0.875rem !important;
        padding-right: 2rem !important;
        color: #566a7f !important;
        font-size: 0.9375rem !important;
        width: 100% !important;
    }
    #ose_items_table .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
        right: 8px !important;
    }
    #ose_items_table .btn-remove-ose-row {
        height: 48px;
        width: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .select2-container.is-invalid .select2-selection {
        border-color: #ff3e1d !important;
    }
    .form-floating-outline select.is-invalid + label,
    .form-floating-outline input.is-invalid + label {
        color: #ff3e1d !important;
    }
    .ose-err {
        font-size: 0.74rem;
        line-height: 1.2;
        margin-top: 3px;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    const csrfToken = '{{ csrf_token() }}';
    const baseUrl = '{{ url('fg_stock_taking') }}';

    function initHeaderSelect2() {
        if (!$.fn.select2) return;
        $('#ose_warehouse_id, #ose_store_type_id, #ose_default_location_id').each(function() {
            const $el = $(this);
            if ($el.hasClass('select2-hidden-accessible')) {
                $el.select2('destroy');
            }
            $el.select2({
                placeholder: $el.data('placeholder') || 'Select',
                width: '100%',
                allowClear: true,
                dropdownParent: $('body')
            });
        });
    }

    function initSelect2($container) {
        if (!$.fn.select2) return;
        const $target = $container ? $container.find('.select2') : $('#ose_items_table .select2');
        $target.each(function() {
            const $this = $(this);
            if ($this.hasClass('select2-hidden-accessible')) {
                $this.select2('destroy');
            }
            $this.select2({
                width: '100%',
                placeholder: $this.data('placeholder') || 'Select',
                dropdownParent: $('body')
            });
        });
    }

    initHeaderSelect2();

    if ($.fn.flatpickr) {
        $('.flatpickr').flatpickr({
            dateFormat: 'd-m-Y',
            allowInput: true
        });
    }

@php
    $brandsData = ($allBrands ?? collect())->map(function($b) {
        return [
            'id' => (int) $b->id,
            'name' => trim($b->brand_name ?? ''),
            'code' => trim((string)($b->code ?? '')),
        ];
    })->values()->toArray();

    $stylesData = ($styles ?? collect())->map(function($s) {
        return [
            'id' => (int) $s->id,
            'style_name' => trim($s->style_name ?? ''),
            'code' => trim((string)($s->code ?? '')),
        ];
    })->values()->toArray();
@endphp

    const OSE_BRANDS = @json($brandsData);
    OSE_BRANDS.sort(function(a, b) {
        return (b.code ? b.code.length : 0) - (a.code ? a.code.length : 0);
    });

    const OSE_STYLES = @json($stylesData);
    const OSE_COLORS = @json($colors ?? []);
    const OSE_UOMS = @json($uoms ?? []);
    const OSE_LOCATIONS = @json($storeLocations ?? []);
    const OSE_SIZES = ['36', '38', '40', '42', '44', '46', '48', '50'];
    const OSE_PRODUCT_SUGGESTIONS = @json($productSuggestions ?? []);
    const INITIAL_ITEMS = @json(old('items', $existingItems ?? []));
    const SERVER_ERRORS = @json(isset($errors) ? $errors->toArray() : []);
    let oseRowCounter = 0;

    function stripInvisible(str) {
        if (!str) return '';
        return String(str).replace(/[\u00A0\u200B\u200E\u200F\uFEFF]/g, ' ').trim();
    }
    function stripSkuSpaces(str) {
        if (!str) return '';
        return String(str).replace(/[\s\u00A0\u200B\u200E\u200F\uFEFF]+/g, '');
    }

    function addOseRow(prefill = null, forcedIdx = null) {
        const idx = (forcedIdx !== null && forcedIdx !== undefined) ? forcedIdx : oseRowCounter++;
        if (idx >= oseRowCounter) {
            oseRowCounter = idx + 1;
        }
        const defaultLoc = $('#ose_default_location_id').val() || '';

        const productName = prefill ? (prefill.product_name || '') : '';
        const artNo = prefill ? (prefill.art_no || '') : '';
        const styleId = prefill ? (prefill.style_id || '') : (OSE_STYLES[0]?.id || '');
        const size = prefill ? (prefill.size || '38') : '38';
        const colorId = prefill ? (prefill.color_id || '') : '';
        const sleeveRaw = prefill ? String(prefill.sleeve_type || 'FULL').trim().toUpperCase() : 'FULL';
        const sleeve = (sleeveRaw === 'HALF' || sleeveRaw === 'HS' || sleeveRaw === '02') ? 'HALF' : 'FULL';
        const qtyIn = prefill ? (prefill.qty_in !== undefined ? prefill.qty_in : 1) : 1;
        const mrp = prefill ? (prefill.mrp !== undefined ? prefill.mrp : '0.00') : '0.00';
        const price = prefill ? (prefill.price !== undefined ? prefill.price : '0.00') : '0.00';
        const locId = prefill ? (prefill.store_location_id || defaultLoc) : defaultLoc;
        const sku = prefill ? (prefill.sku || '') : '';
        const uomId = prefill ? (prefill.uom_id || '') : '';
        const remarks = prefill ? (prefill.remarks || '') : '';

        const errProd = SERVER_ERRORS[`items.${idx}.product_name`]?.[0] || '';
        const errArt = SERVER_ERRORS[`items.${idx}.art_no`]?.[0] || '';
        const errStyle = SERVER_ERRORS[`items.${idx}.style_id`]?.[0] || '';
        const errSize = SERVER_ERRORS[`items.${idx}.size`]?.[0] || '';
        const errSleeve = SERVER_ERRORS[`items.${idx}.sleeve_type`]?.[0] || '';
        const errQty = SERVER_ERRORS[`items.${idx}.qty_in`]?.[0] || '';
        const errSku = SERVER_ERRORS[`items.${idx}.sku`]?.[0] || '';

        const styleOptionsHtml = '<option value="">Select Style</option>' + OSE_STYLES.map(s =>
            `<option value="${s.id}" ${styleId == s.id ? 'selected' : ''}>${escapeHtml(s.style_name)}</option>`
        ).join('');

        const colorOptionsHtml = '<option value="">Select Color</option>' + OSE_COLORS.map(c =>
            `<option value="${c.id}" ${colorId == c.id ? 'selected' : ''}>${escapeHtml(c.color_name)}</option>`
        ).join('');

        const locOptionsHtml = '<option value="">Select Location</option>' + OSE_LOCATIONS.map(l =>
            `<option value="${l.id}" ${locId == l.id ? 'selected' : ''}>${escapeHtml(l.store_location)}</option>`
        ).join('');

        const uomOptionsHtml = OSE_UOMS.map(u => {
            const isPcs = (u.uom_code === 'PCS' || u.uom_name === 'PIECES');
            const isSel = uomId ? (uomId == u.id) : isPcs;
            return `<option value="${u.id}" ${isSel ? 'selected' : ''}>${escapeHtml(u.uom_code || u.uom_name)}</option>`;
        }).join('');

        let effectiveSizes = [...OSE_SIZES];
        if (size && !effectiveSizes.includes(String(size))) {
            effectiveSizes.push(String(size));
        }
        const sizeOptionsHtml = effectiveSizes.map(s =>
            `<option value="${s}" ${String(size) === String(s) ? 'selected' : ''}>${s}</option>`
        ).join('');

        const rowHtml = `
            <tr class="ose-item-row" data-index="${idx}">
                <td class="text-center small text-muted fw-bold ose-row-num">1</td>
                <td>
                    <input type="text" name="items[${idx}][product_name]" class="form-control ose-product-name text-uppercase ${errProd ? 'is-invalid' : ''}" value="${escapeHtml(productName)}" placeholder="e.g. CF-PLN-FS" autocomplete="off">
                    <div class="text-danger small ose-err ose-err-product-name">${escapeHtml(errProd)}</div>
                </td>
                <td>
                    <input type="text" name="items[${idx}][art_no]" class="form-control ose-art-no ${errArt ? 'is-invalid' : ''}" value="${escapeHtml(artNo)}" placeholder="e.g. CBC8001">
                    <div class="text-danger small ose-err ose-err-art-no">${escapeHtml(errArt)}</div>
                </td>
                <td>
                    <select name="items[${idx}][style_id]" class="form-select select2 ose-style ${errStyle ? 'is-invalid' : ''}" data-placeholder="Select Style">
                        ${styleOptionsHtml}
                    </select>
                    <div class="text-danger small ose-err ose-err-style">${escapeHtml(errStyle)}</div>
                </td>
                <td>
                    <select name="items[${idx}][size]" class="form-select select2 ose-size ${errSize ? 'is-invalid' : ''}" data-placeholder="Size">
                        ${sizeOptionsHtml}
                    </select>
                    <div class="text-danger small ose-err ose-err-size">${escapeHtml(errSize)}</div>
                </td>
                <td>
                    <select name="items[${idx}][sleeve_type]" class="form-select select2 ose-sleeve-type ${errSleeve ? 'is-invalid' : ''}" data-placeholder="Sleeve">
                        <option value="FULL" ${sleeve === 'FULL' ? 'selected' : ''}>FULL</option>
                        <option value="HALF" ${sleeve === 'HALF' ? 'selected' : ''}>HALF</option>
                    </select>
                    <div class="text-danger small ose-err ose-err-sleeve-type">${escapeHtml(errSleeve)}</div>
                </td>
                <td>
                    <input type="number" name="items[${idx}][qty_in]" class="form-control ose-qty text-end ${errQty ? 'is-invalid' : ''}" min="0.01" step="any" value="${qtyIn}">
                    <div class="text-danger small ose-err ose-err-qty">${escapeHtml(errQty)}</div>
                </td>
                <td>
                    <input type="number" name="items[${idx}][mrp]" class="form-control ose-mrp text-end" min="0" step="0.01" value="${mrp}" placeholder="0.00">
                </td>
                <td>
                    <input type="number" name="items[${idx}][price]" class="form-control ose-price text-end" min="0" step="0.01" value="${price}" placeholder="0.00">
                </td>
                <td>
                    <select name="items[${idx}][store_location_id]" class="form-select select2 ose-location" data-placeholder="Select Location">
                        ${locOptionsHtml}
                    </select>
                </td>
                <td>
                    <input type="text" name="items[${idx}][sku]" class="form-control ose-sku ${errSku ? 'is-invalid' : ''}" value="${escapeHtml(sku)}" placeholder="Scan / Enter SKU">
                    <div class="text-danger small ose-err ose-err-sku">${escapeHtml(errSku)}</div>
                </td>
                <td>
                    <select name="items[${idx}][uom_id]" class="form-select select2 ose-uom" data-placeholder="Select UOM">
                        ${uomOptionsHtml}
                    </select>
                </td>
                <td>
                    <input type="text" name="items[${idx}][remarks]" class="form-control ose-remarks" value="${escapeHtml(remarks)}" placeholder="Remarks">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-ose-row p-1" title="Remove row">
                        <i class="ri ri-delete-bin-line"></i>
                    </button>
                </td>
            </tr>
        `;

        const $newRow = $(rowHtml);
        $('#ose_items_tbody').append($newRow);
        initSelect2($newRow);
        if (errStyle) {
            $newRow.find('.ose-style').next('.select2-container').addClass('is-invalid');
        }
        if (errSize) {
            $newRow.find('.ose-size').next('.select2-container').addClass('is-invalid');
        }
        if (errSleeve) {
            $newRow.find('.ose-sleeve-type').next('.select2-container').addClass('is-invalid');
        }
        renumberOseRows();
        calculateOseSummary();
    }

    function renumberOseRows() {
        $('#ose_items_tbody tr').each(function(i) {
            $(this).find('.ose-row-num').text(i + 1);
        });
        const count = $('#ose_items_tbody tr').length;
        $('#ose_items_count_badge').text(count + (count === 1 ? ' item' : ' items'));
    }

    function calculateOseSummary() {
        let totalQty = 0;
        let totalAmount = 0;
        $('#ose_items_tbody tr').each(function() {
            const qty = parseFloat($(this).find('.ose-qty').val()) || 0;
            const price = parseFloat($(this).find('.ose-price').val()) || 0;
            totalQty += qty;
            totalAmount += (qty * price);
        });
        $('#ose_total_qty_display').text(totalQty.toLocaleString());
        $('#ose_total_amount_display').text(totalAmount.toFixed(2));
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function checkSleeveConsistency($row) {
        const prod = $.trim($row.find('.ose-product-name').val()).toUpperCase();
        const $sleeve = $row.find('.ose-sleeve-type');
        const sleeveVal = ($sleeve.val() || 'FULL').toUpperCase();
        const $errDiv = $row.find('.ose-err-sleeve-type');

        const isFs = /(^|[-_\s])FS($|[-_\s])/i.test(prod) || prod.endsWith('-FS') || prod.endsWith(' FS');
        const isHs = /(^|[-_\s])HS($|[-_\s])/i.test(prod) || prod.endsWith('-HS') || prod.endsWith(' HS');

        if (isFs && sleeveVal === 'HALF') {
            $sleeve.addClass('is-invalid');
            $sleeve.next('.select2-container').addClass('is-invalid');
            $errDiv.text('Product Name is FS, sleeve must be FULL.');
            return false;
        } else if (isHs && sleeveVal === 'FULL') {
            $sleeve.addClass('is-invalid');
            $sleeve.next('.select2-container').addClass('is-invalid');
            $errDiv.text('Product Name is HS, sleeve must be HALF.');
            return false;
        } else {
            $sleeve.removeClass('is-invalid');
            $sleeve.next('.select2-container').removeClass('is-invalid');
            $errDiv.text('');
            return true;
        }
    }

    // ==========================================
    // Custom JS Autocomplete for Product Name
    // ==========================================
    let $activeAutocompleteInput = null;
    let activeItemIndex = -1;

    function highlightMatch(text, query) {
        if (!query) return escapeHtml(text);
        const idx = text.toUpperCase().indexOf(query.toUpperCase());
        if (idx === -1) return escapeHtml(text);
        const before = escapeHtml(text.substring(0, idx));
        const match = escapeHtml(text.substring(idx, idx + query.length));
        const after = escapeHtml(text.substring(idx + query.length));
        return `${before}<mark>${match}</mark>${after}`;
    }

    function positionAutocomplete($input) {
        if (!$input || !$input.length) return;
        const offset = $input.offset();
        const height = $input.outerHeight();
        const width = $input.outerWidth();
        $('#ose_autocomplete_dropdown').css({
            top: (offset.top + height + 2) + 'px',
            left: offset.left + 'px',
            minWidth: Math.max(width, 240) + 'px',
            width: Math.max(width, 240) + 'px'
        });
    }

    function showAutocomplete($input) {
        $activeAutocompleteInput = $input;
        const query = ($input.val() || '').trim().toUpperCase();

        let matches = [];
        if (!query) {
            matches = OSE_PRODUCT_SUGGESTIONS.slice(0, 15);
        } else {
            const startsWith = [];
            const contains = [];
            for (let i = 0; i < OSE_PRODUCT_SUGGESTIONS.length; i++) {
                const code = OSE_PRODUCT_SUGGESTIONS[i];
                const upper = code.toUpperCase();
                if (upper.startsWith(query)) {
                    startsWith.push(code);
                } else if (upper.includes(query)) {
                    contains.push(code);
                }
            }
            matches = startsWith.concat(contains).slice(0, 20);
        }

        if (!matches.length) {
            $('#ose_autocomplete_dropdown').hide();
            return;
        }

        let html = '';
        matches.forEach((code, i) => {
            const parts = code.split('-');
            let hint = '';
            if (parts.length >= 2) {
                hint = parts[1];
                if (parts.length >= 3) hint += ' · ' + (parts[2] === 'FS' ? 'Full' : 'Half');
            }
            html += `
                <div class="ose-autocomplete-item ${i === 0 ? 'active' : ''}" data-code="${escapeHtml(code)}">
                    <span>${highlightMatch(code, query)}</span>
                    ${hint ? `<span class="badge bg-label-primary rounded-pill small ms-2">${escapeHtml(hint)}</span>` : ''}
                </div>
            `;
        });

        activeItemIndex = 0;
        $('#ose_autocomplete_dropdown').html(html);
        positionAutocomplete($input);
        $('#ose_autocomplete_dropdown').show();
    }

    function selectAutocompleteItem($item) {
        if (!$activeAutocompleteInput || !$item.length) return;
        const code = $item.data('code');
        $activeAutocompleteInput.val(code);
        $activeAutocompleteInput.trigger('change');
        $('#ose_autocomplete_dropdown').hide();
        $activeAutocompleteInput.focus();
    }

    $(document).on('focus', '.ose-product-name', function() {
        showAutocomplete($(this));
    });

    $(document).on('input', '.ose-product-name', function() {
        showAutocomplete($(this));
    });

    $(document).on('keydown', '.ose-product-name', function(e) {
        const $dropdown = $('#ose_autocomplete_dropdown');
        if (!$dropdown.is(':visible')) {
            if (e.key === 'ArrowDown') {
                showAutocomplete($(this));
                e.preventDefault();
            }
            return;
        }

        const $items = $dropdown.find('.ose-autocomplete-item');
        if (!$items.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeItemIndex = (activeItemIndex + 1) % $items.length;
            $items.removeClass('active').eq(activeItemIndex).addClass('active');
            const itemEl = $items.get(activeItemIndex);
            if (itemEl) itemEl.scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeItemIndex = (activeItemIndex - 1 + $items.length) % $items.length;
            $items.removeClass('active').eq(activeItemIndex).addClass('active');
            const itemEl = $items.get(activeItemIndex);
            if (itemEl) itemEl.scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'Enter') {
            e.preventDefault();
            const $active = $items.eq(activeItemIndex);
            if ($active.length) {
                selectAutocompleteItem($active);
            }
        } else if (e.key === 'Escape') {
            $dropdown.hide();
        }
    });

    $(document).on('mousedown', '.ose-autocomplete-item', function(e) {
        e.preventDefault();
        selectAutocompleteItem($(this));
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#ose_autocomplete_dropdown, .ose-product-name').length) {
            $('#ose_autocomplete_dropdown').hide();
        }
    });

    $(window).on('resize scroll', function() {
        if ($activeAutocompleteInput && $('#ose_autocomplete_dropdown').is(':visible')) {
            positionAutocomplete($activeAutocompleteInput);
        }
    });

    $(document).on('input blur change', '.ose-product-name', function(e) {
        const $row = $(this).closest('tr');
        let rawVal = $(this).val();
        let cleaned = stripInvisible(rawVal);
        if ((e.type === 'blur' || e.type === 'change') && rawVal !== cleaned.toUpperCase()) {
            $(this).val(cleaned.toUpperCase());
        }
        const prod = cleaned.toUpperCase();
        const $errProd = $row.find('.ose-err-product-name');
        const $styleSelect = $row.find('.ose-style');
        const $sleeveSelect = $row.find('.ose-sleeve-type');

        // Check slashes
        if (prod.includes('/')) {
            $(this).addClass('is-invalid');
            $errProd.text("Product Name cannot contain slashes ('/'). Please use 'FS' or 'HS' instead of 'F/S' or 'H/S'.");
            return;
        }

        const parts = prod.split('-');
        if (parts.length >= 2) {
            const brandPart = parts[0].trim();
            const stylePart = parts[1].trim();

            // Brand lookup
            const brandObj = OSE_BRANDS.find(b => b.code.toUpperCase() === brandPart || b.name.toUpperCase() === brandPart);

            if (!brandObj) {
                $(this).addClass('is-invalid');
                $errProd.text(`Brand prefix '${brandPart}' does not exist in Brands master.`);
            } else {
                $(this).removeClass('is-invalid');
                $errProd.text('');

                // Cross-check with Art No brand if already entered
                const artVal = ($row.find('.ose-art-no').val() || '').trim().toUpperCase();
                if (artVal && !artVal.startsWith('CRYSTAL')) {
                    let artBrand = null;
                    for (let i = 0; i < OSE_BRANDS.length; i++) {
                        const b = OSE_BRANDS[i];
                        if (b.code && artVal.startsWith(b.code.toUpperCase())) {
                            artBrand = b;
                            break;
                        }
                    }
                    if (artBrand && artBrand.id !== brandObj.id) {
                        $(this).addClass('is-invalid');
                        $errProd.text(`Art No belongs to Brand '${artBrand.name}' (${artBrand.code}), but Product Name specifies Brand '${brandObj.name}' (${brandObj.code}).`);
                    }
                }
            }

            // Style lookup and auto-select
            const styleObj = OSE_STYLES.find(s => s.code.toUpperCase() === stylePart || s.style_name.toUpperCase() === stylePart);
            const $errStyle = $row.find('.ose-err-style');
            if (styleObj) {
                if ($styleSelect.val() != styleObj.id) {
                    $styleSelect.val(styleObj.id).trigger('change');
                }
                $styleSelect.removeClass('is-invalid');
                $styleSelect.next('.select2-container').removeClass('is-invalid');
                $errStyle.text('');
            } else if (stylePart) {
                $(this).addClass('is-invalid');
                $errProd.text(`Style '${stylePart}' from Product Name does not exist in Styles master.`);
            }
        } else {
            $(this).removeClass('is-invalid');
            $errProd.text('');
        }

        // Sleeve auto-select & check
        const isFs = /(^|[-_\s])FS($|[-_\s])/i.test(prod) || prod.endsWith('-FS') || prod.endsWith(' FS');
        const isHs = /(^|[-_\s])HS($|[-_\s])/i.test(prod) || prod.endsWith('-HS') || prod.endsWith(' HS');

        if (isFs && $sleeveSelect.val() !== 'FULL') {
            $sleeveSelect.val('FULL').trigger('change');
        } else if (isHs && $sleeveSelect.val() !== 'HALF') {
            $sleeveSelect.val('HALF').trigger('change');
        } else {
            checkSleeveConsistency($row);
        }

        // Auto fetch selling price
        fetchItemSellingPrice($row);
    });

    $(document).on('input blur', '.ose-art-no', function(e) {
        const $row = $(this).closest('tr');
        let rawVal = $(this).val();
        let cleaned = stripInvisible(rawVal);
        if (e.type === 'blur' && rawVal !== cleaned) {
            $(this).val(cleaned);
        }
        const art = cleaned.toUpperCase();
        const $errArt = $row.find('.ose-err-art-no');
        const $styleSelect = $row.find('.ose-style');

        // CRYSTAL condition -> Style WHITE
        if (art === 'CRYSTAL' || art.startsWith('CRYSTAL')) {
            const whiteStyle = OSE_STYLES.find(s => s.style_name.toUpperCase() === 'WHITE' || s.code.toUpperCase() === 'WHITE' || s.code.toUpperCase() === 'WHT');
            if (whiteStyle && $styleSelect.val() != whiteStyle.id) {
                $styleSelect.val(whiteStyle.id).trigger('change');
            }
            $(this).removeClass('is-invalid');
            $errArt.text('');
            return;
        }

        let brandMatch = null;
        for (let i = 0; i < OSE_BRANDS.length; i++) {
            const b = OSE_BRANDS[i];
            if (b.code && art.startsWith(b.code.toUpperCase())) {
                brandMatch = b;
                break;
            }
        }

        // Cross-check with Product Name brand if already entered
        const prodVal = ($row.find('.ose-product-name').val() || '').trim().toUpperCase();
        let prodBrand = null;
        const prodParts = prodVal.split('-');
        if (prodParts.length >= 2) {
            const bp = prodParts[0].trim();
            prodBrand = OSE_BRANDS.find(b => b.code.toUpperCase() === bp || b.name.toUpperCase() === bp);
        }

        if (brandMatch && prodBrand && brandMatch.id !== prodBrand.id) {
            $(this).addClass('is-invalid');
            $errArt.text(`Art No belongs to Brand '${brandMatch.name}' (${brandMatch.code}), but Product Name specifies Brand '${prodBrand.name}' (${prodBrand.code}).`);
        } else {
            $(this).removeClass('is-invalid');
            $errArt.text('');
        }

        // Auto generate barcode
        autoGenerateBarcode($row);

        // Auto fetch selling price
        fetchItemSellingPrice($row);
    });

    let priceFetchTimers = {};

    function fetchItemSellingPrice($row) {
        if (!$row || !$row.length) return;

        const art = $.trim($row.find('.ose-art-no').val());
        const prod = $.trim($row.find('.ose-product-name').val());
        const size = $.trim($row.find('.ose-size').val());
        const sleeve = $.trim($row.find('.ose-sleeve-type').val());

        if (!art) return;

        const rowKey = $row.attr('data-index') || $row.index();
        if (priceFetchTimers[rowKey]) {
            clearTimeout(priceFetchTimers[rowKey]);
        }

        priceFetchTimers[rowKey] = setTimeout(function() {
            $.ajax({
                url: "{{ url('fg_stock_taking/get-item-price') }}",
                type: 'GET',
                data: {
                    art_no: art,
                    product_name: prod,
                    size: size,
                    sleeve: sleeve
                },
                dataType: 'json',
                success: function(res) {
                    if (res && res.success && res.found) {
                        if (res.mrp !== undefined && res.mrp !== null) {
                            $row.find('.ose-mrp').val(res.mrp);
                        }
                        const wspVal = res.wsp || res.selling_price || res.unit_price;
                        if (wspVal) {
                            $row.find('.ose-price').val(wspVal).trigger('input');
                        }
                    }
                }
            });
        }, 150);
    }

    function computeBarcode(rawArt, sizeVal, sleeveVal) {
        if (!rawArt) return '';
        const art = String(rawArt).trim().toUpperCase();
        if (!art) return '';

        // Strip leading brand letters if followed by digits (e.g. CF12345-1 -> 12345-1, CB3119-2 -> 3119-2)
        let cleanArt = art.replace(/^[A-Za-z]+[-_]?(?=\d)/, '');
        // Convert hyphens into '0' (e.g. 12345-1 -> 1234501)
        cleanArt = cleanArt.replace(/-/g, '0').replace(/[^A-Za-z0-9]/g, '');

        const sizeCode = String(sizeVal || '').trim().replace(/[^A-Za-z0-9]/g, '');
        const slv = String(sleeveVal || '').trim().toUpperCase();
        const sleeveCode = (slv === 'HALF' || slv === 'HS' || slv === '02') ? '02' : '01';

        if (cleanArt && sizeCode) {
            return 'BC' + cleanArt + sizeCode + sleeveCode;
        }
        return '';
    }

    function autoGenerateBarcode($row) {
        const art = $row.find('.ose-art-no').val();
        const size = $row.find('.ose-size').val();
        const sleeve = $row.find('.ose-sleeve-type').val();
        const barcode = computeBarcode(art, size, sleeve);
        if (barcode) {
            $row.find('.ose-sku').val(barcode).trigger('input');
        }
    }

    $(document).on('input blur', '.ose-sku', function() {
        let raw = $(this).val();
        let cleaned = stripSkuSpaces(raw);
        if (raw !== cleaned) {
            $(this).val(cleaned);
        }
        checkDuplicateSkusInTable();
    });

    function checkDuplicateSkusInTable() {
        const seen = {};
        $('#ose_items_tbody tr').each(function() {
            const $row = $(this);
            const sku = stripSkuSpaces($row.find('.ose-sku').val());
            const size = ($row.find('.ose-size').val() || '').trim();
            const $errSku = $row.find('.ose-err-sku');
            const rowNum = $row.find('.ose-row-num').text();

            if (!sku || !size) return;

            if (seen[sku]) {
                if (seen[sku].size.toUpperCase() !== size.toUpperCase()) {
                    $row.find('.ose-sku').addClass('is-invalid');
                    $errSku.text(`Duplicate Barcode with different Size '${size}' (Row #${seen[sku].rowNum} has '${seen[sku].size}').`);
                }
            } else {
                seen[sku] = { size: size, rowNum: rowNum };
            }
        });
    }

    $(document).on('change', '.ose-sleeve-type', function() {
        const $row = $(this).closest('tr');
        const sleeveVal = ($(this).val() || '').toUpperCase();
        const $prodInput = $row.find('.ose-product-name');
        let prod = $.trim($prodInput.val());
        if (prod) {
            if (sleeveVal === 'HALF' && prod.toUpperCase().endsWith('-FS')) {
                $prodInput.val(prod.substring(0, prod.length - 3) + '-HS');
            } else if (sleeveVal === 'FULL' && prod.toUpperCase().endsWith('-HS')) {
                $prodInput.val(prod.substring(0, prod.length - 3) + '-FS');
            }
        }
        checkSleeveConsistency($row);
        autoGenerateBarcode($row);
        fetchItemSellingPrice($row);
    });

    $(document).on('change', '.ose-size', function() {
        const $row = $(this).closest('tr');
        autoGenerateBarcode($row);
        fetchItemSellingPrice($row);
        checkDuplicateSkusInTable();
    });

    $(document).on('input change select2:select', 'select, input', function() {
        $(this).removeClass('is-invalid');
        $(this).next('.select2-container').removeClass('is-invalid');
        $(this).closest('td').find('.ose-err').text('');
        $(this).closest('.col-md-6, .col-lg-3, .col-12').find('.text-danger').text('').hide();
    });

    $('#ose_warehouse_id, #ose_store_type_id, #ose_default_location_id').on('change select2:select', function() {
        $(this).removeClass('is-invalid');
        $(this).next('.select2-container').removeClass('is-invalid');
        $(this).closest('.col-md-6, .col-lg-3').find('.text-danger').text('').hide();
    });

    if (Array.isArray(INITIAL_ITEMS) && INITIAL_ITEMS.length > 0) {
        INITIAL_ITEMS.forEach(function(item, idx) {
            addOseRow(item, idx);
        });
        // Auto-generate new format barcode and fetch latest price for existing/old rows
        $('#ose_items_tbody tr').each(function() {
            const $row = $(this);
            autoGenerateBarcode($row);
            fetchItemSellingPrice($row);
        });
    } else {
        addOseRow();
    }

    $('#btn_ose_add_row').on('click', function() {
        addOseRow();
    });

    $('#btn_ose_duplicate_last').on('click', function() {
        const $lastRow = $('#ose_items_tbody tr').last();
        if ($lastRow.length === 0) {
            addOseRow();
            return;
        }

        const lastSize = $lastRow.find('.ose-size').val();
        const artVal = $lastRow.find('.ose-art-no').val();
        const sleeveVal = $lastRow.find('.ose-sleeve-type').val();
        const nextSku = computeBarcode(artVal, lastSize, sleeveVal);

        const prefill = {
            product_name: $lastRow.find('.ose-product-name').val(),
            art_no: artVal,
            style_id: $lastRow.find('.ose-style').val(),
            size: lastSize,
            sleeve_type: sleeveVal,
            qty_in: $lastRow.find('.ose-qty').val(),
            mrp: $lastRow.find('.ose-mrp').val(),
            price: $lastRow.find('.ose-price').val(),
            store_location_id: $lastRow.find('.ose-location').val(),
            uom_id: $lastRow.find('.ose-uom').val(),
            sku: nextSku,
            remarks: $lastRow.find('.ose-remarks').val()
        };

        addOseRow(prefill);
        fetchItemSellingPrice($('#ose_items_tbody tr').last());
    });

    $(document).on('click', '.btn-remove-ose-row', function() {
        const $tbody = $('#ose_items_tbody');
        if ($tbody.find('tr').length <= 1) {
            $tbody.find('input').val('');
            $tbody.find('.ose-qty').val('1');
            $tbody.find('.ose-mrp').val('0.00');
            $tbody.find('.ose-price').val('0.00');
            $tbody.find('.ose-size').val('38').trigger('change');
            $tbody.find('.ose-sleeve-type').val('FULL').trigger('change');
            $tbody.find('.ose-style').val(OSE_STYLES[0]?.id || '').trigger('change');
            $tbody.find('.ose-location').val('').trigger('change');
            $tbody.find('.ose-uom').val('').trigger('change');
        } else {
            const $row = $(this).closest('tr');
            $row.find('.select2').each(function() {
                if ($(this).data('select2')) {
                    $(this).select2('destroy');
                }
            });
            $row.remove();
            renumberOseRows();
        }
        calculateOseSummary();
    });

    $(document).on('input', '.ose-mrp', function() {
        const $row = $(this).closest('tr');
        const mrp = parseFloat($(this).val()) || 0;
        if (mrp > 0) {
            const wsp = Math.round(mrp / 1.5);
            $row.find('.ose-price').val(wsp.toFixed(2)).trigger('change');
        }
    });

    $(document).on('input change', '.ose-qty, .ose-price, .ose-mrp', function() {
        calculateOseSummary();
    });

    $('#ose_default_location_id').on('change', function() {
        const newLoc = $(this).val();
        if (!newLoc) return;
        $('#ose_items_tbody tr').each(function() {
            const $loc = $(this).find('.ose-location');
            if (!$loc.val()) {
                $loc.val(newLoc).trigger('change');
            }
        });
    });
});
</script>
@endsection

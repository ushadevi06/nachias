@extends('layouts.common')
@section('title', 'Add Stock Entry - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <form action="{{ url('stock_entries/add' . ($stockEntry ? '/' . $stockEntry->id : '')) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card mb-6">
                    <div class="card-header">
                        <h4>{{ $stockEntry ? 'Edit' : 'Add' }} Stock Entry</h5>
                    </div>
                    <div class="card-body">
                        
                        <div class="row mb-4">
                            <div class="col-lg-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="stock_entry_no" class="form-control" placeholder="Enter Stock Entry No" value="{{ $stockEntry->stock_entry_no ?? $nextStockNo }}" readonly required />
                                    <label for="code">Stock Entry No <span class="text-danger">*</span></label>
                                    @error('stock_entry_no')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="stock_date" class="form-control stock_date" placeholder="Enter Stock Date" value="{{ old('stock_date', $stockEntry ? $stockEntry->stock_date->format('d-m-Y') : date('d-m-Y')) }}" {{ $stockEntry ? 'readonly' : '' }} required />
                                    <label for="stock_date">Stock Date <span class="text-danger">*</span></label>
                                    @error('stock_date')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="entry_type" name="entry_type" class="select2 form-select" data-placeholder="Select Entry Type" {{ $stockEntry ? 'disabled' : '' }} required>
                                        <option value="">Select Entry Type</option>
                                        <option value="Purchase Receipt" {{ old('entry_type', $stockEntry->entry_type ?? '') == 'Purchase Receipt' ? 'selected' : '' }}>Purchase Receipt</option>
                                        <option value="Transfer Receipt" {{ old('entry_type', $stockEntry->entry_type ?? '') == 'Transfer Receipt' ? 'selected' : '' }}>Transfer Receipt</option>
                                        <option value="Transfer Issue" {{ old('entry_type', $stockEntry->entry_type ?? '') == 'Transfer Issue' ? 'selected' : '' }}>Transfer Issue</option>
                                        <option value="Production Issue" {{ old('entry_type', $stockEntry->entry_type ?? '') == 'Production Issue' ? 'selected' : '' }}>Production Issue</option>
                                        <option value="Production Receipt" {{ old('entry_type', $stockEntry->entry_type ?? '') == 'Production Receipt' ? 'selected' : '' }}>Production Receipt</option>
                                        <option value="Stock Adjustment" {{ old('entry_type', $stockEntry->entry_type ?? '') == 'Stock Adjustment' ? 'selected' : '' }}>Stock Adjustment</option>
                                        <option value="Stock Conversion" {{ old('entry_type', $stockEntry->entry_type ?? '') == 'Stock Conversion' ? 'selected' : '' }}>Stock Conversion</option>
                                    </select>
                                    <label for="entry_type">Entry Type <span class="text-danger">*</span></label>
                                    @if($stockEntry)
                                        <input type="hidden" name="entry_type" id="entry_type_hidden" value="{{ $stockEntry->entry_type }}">
                                    @endif
                                    @error('entry_type')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- GRN Entry No - NEW FIELD (optional) -->
                            <div class="col-lg-4 mb-4" id="grn_section" style="{{ old('entry_type', $stockEntry->entry_type ?? '') == 'Purchase Receipt' ? '' : 'display:none;' }}">
                                <div class="form-floating form-floating-outline">
                                    <select id="grn_entry_id" name="grn_entry_id" class="select2 form-select" data-placeholder="Select GRN Entry" {{ $stockEntry ? 'disabled' : '' }}>
                                        <option value="">Select GRN Entry No</option>
                                        @foreach($grnEntries as $grn)
                                            <option value="{{ $grn->id }}" {{ old('grn_entry_id', $stockEntry->grn_entry_id ?? '') == $grn->id ? 'selected' : '' }}>{{ $grn->grn_number }}</option>
                                        @endforeach
                                    </select>
                                    <label for="grn_entry_id">GRN Entry No</label>
                                    @if($stockEntry)
                                        <input type="hidden" name="grn_entry_id" value="{{ $stockEntry->grn_entry_id }}">
                                    @endif
                                    @error('grn_entry_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- GRN Item Selection - Added for one-by-one -->
                            <div class="col-lg-4 mb-4" id="grn_item_selection_section" style="display:none;">
                                <div class="form-floating form-floating-outline">
                                    <select id="grn_entry_item_id" name="grn_entry_item_id" class="select2 form-select" data-placeholder="Select GRN Item">
                                        <option value="">Select GRN Item</option>
                                    </select>
                                    <label for="grn_entry_item_id">Select GRN Item</label>
                                    @error('grn_entry_item_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-4 mb-4" id="select_type_container">
                                <label class="form-label d-block">Select Type <span class="text-danger">*</span></label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="entry_type_radio" id="raw_material" value="raw_material" {{ old('entry_type_radio', ($stockEntry && $stockEntry->stockEntryItems->first() ? $stockEntry->stockEntryItems->first()->stock_type : '')) == 'raw_material' ? 'checked' : '' }} {{ $stockEntry ? 'disabled' : '' }} required>
                                    <label class="form-check-label" for="raw_material">Raw Material</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="entry_type_radio" id="finished_goods" value="finished_goods" {{ old('entry_type_radio', ($stockEntry && $stockEntry->stockEntryItems->first() ? $stockEntry->stockEntryItems->first()->stock_type : '')) == 'finished_goods' ? 'checked' : '' }} {{ $stockEntry ? 'disabled' : '' }}>
                                    <label class="form-check-label" for="finished_goods">Finished Goods</label>
                                </div>
                                @if($stockEntry)
                                    <input type="hidden" name="entry_type_radio" value="{{ ($stockEntry && $stockEntry->stockEntryItems->first()) ? $stockEntry->stockEntryItems->first()->stock_type : 'raw_material' }}">
                                @endif
                                @error('entry_type_radio')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- GRN Items Table (Removed for one-by-one selection) -->
                        {{-- <div class="row mb-4" id="grn_items_section" style="display:none;"> ... </div> --}}

                        <div class="row mb-4">

                        <!-- RAW MATERIAL SECTION -->
                        <div class="row mb-4" id="raw_material_section" style="display:none;">
                            <div class="col-lg-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="store_category" name="store_category_id" class="select2 form-select" data-placeholder="Select Store Category">
                                        <option value="">Select Store Category</option>
                                        @foreach($storeCategories as $cat)
                                            <option value="{{ $cat->id }}" data-code="{{ $cat->code }}" {{ old('store_category_id', ($stockEntry && $stockEntry->stockEntryItems->first()) ? $stockEntry->stockEntryItems->first()->store_category_id : '') == $cat->id ? 'selected' : '' }}>{{ $cat->category_name }}({{ $cat->code }})</option>
                                        @endforeach
                                    </select>
                                    <label for="store_category">Store Category <span class="text-danger">*</span></label>
                                    @error('store_category_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                    <input type="hidden" id="store_category_hidden" name="store_category_id" disabled>
                                </div>
                            </div>

                            <div class="col-lg-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="material" name="raw_material_id" class="select2 form-select" data-placeholder="Select Material">
                                        <option value="">Select Material</option>
                                        @foreach($rawMaterials as $mat)
                                            <option value="{{ $mat->id }}" data-uom="{{ $mat->uom_id }}" data-category="{{ $mat->store_category_id }}" {{ old('raw_material_id', ($stockEntry && $stockEntry->stockEntryItems->first()) ? $stockEntry->stockEntryItems->first()->raw_material_id : '') == $mat->id ? 'selected' : '' }}>{{ $mat->name }}({{ $mat->code }})</option>
                                        @endforeach
                                    </select>
                                    <label for="material">Material <span class="text-danger">*</span></label>
                                    @error('raw_material_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                    <input type="hidden" id="material_hidden" name="raw_material_id" disabled>
                                </div>
                            </div>
                        </div>

                        <!-- FINISHED GOODS SECTION -->
                        <div class="row mb-4" id="finished_goods_section" style="display:none;">
                            <div class="col-lg-6 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="finished_item" name="finished_item_code" class="select2 form-select" data-placeholder="Select Finished Item">
                                        <option value="">Select Finished Item</option>
                                        <option value="ITEM001" {{ old('finished_item_code', ($stockEntry && $stockEntry->stockEntryItems->first()) ? $stockEntry->stockEntryItems->first()->finished_item_code : '') == 'ITEM001' ? 'selected' : '' }}>Men's Formal Cotton Shirt(ITEM001)</option>
                                        <option value="ITEM002" {{ old('finished_item_code', ($stockEntry && $stockEntry->stockEntryItems->first()) ? $stockEntry->stockEntryItems->first()->finished_item_code : '') == 'ITEM002' ? 'selected' : '' }}>Men's Casual Denim Shirt(ITEM002)</option>
                                        <option value="ITEM003" {{ old('finished_item_code', ($stockEntry && $stockEntry->stockEntryItems->first()) ? $stockEntry->stockEntryItems->first()->finished_item_code : '') == 'ITEM003' ? 'selected' : '' }}>School Uniform Shirt(ITEM003)</option>
                                        <option value="ITEM004" {{ old('finished_item_code', ($stockEntry && $stockEntry->stockEntryItems->first()) ? $stockEntry->stockEntryItems->first()->finished_item_code : '') == 'ITEM004' ? 'selected' : '' }}>Kids Polo Shirt(ITEM004)</option>
                                        <option value="ITEM005" {{ old('finished_item_code', ($stockEntry && $stockEntry->stockEntryItems->first()) ? $stockEntry->stockEntryItems->first()->finished_item_code : '') == 'ITEM005' ? 'selected' : '' }}>Premium Linen Shirt(ITEM005)</option>
                                    </select>
                                    <label for="finished_item">Finished Goods Item <span class="text-danger">*</span></label>
                                    @error('finished_item_code')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- RAW MATERIAL DETAILS FOR FINISHED GOODS -->
                        <div class="row mb-4" id="finished_raw_materials" style="display:none;">
                            <div class="col-lg-12">
                                <div class="card border shadow-sm">
                                    <div class="card-header bg-light">
                                        <strong>Raw Material Requirements</strong>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 10%">S.No</th>
                                                        <th>Raw Material</th>
                                                        <th>UOM</th>
                                                        <th>Required Qty</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="raw_material_table_body">
                                                    <!-- JS will populate rows dynamically -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SINGLE ITEM FIELDS (Hidden when GRN Items Table is shown) -->
                        <div class="row mb-4" id="single_item_fields">
                            <div class="col-lg-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="uom" name="uom_id" class="select2 form-select" data-placeholder="Select UOM">
                                        <option value="">Select UOM</option>
                                        @foreach($uoms as $uom)
                                            <option value="{{ $uom->id }}" {{ old('uom_id', ($stockEntry && $stockEntry->stockEntryItems->first()) ? $stockEntry->stockEntryItems->first()->uom_id : '') == $uom->id ? 'selected' : '' }}>{{ $uom->uom_code }}</option>
                                        @endforeach
                                    </select>
                                    <label for="uom">UOM </label>
                                    @error('uom_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" name="qty_in" id="qty_in" class="form-control" placeholder="Enter Quantity In" value="{{ old('qty_in', ($stockEntry && $stockEntry->stockEntryItems->first()) ? $stockEntry->stockEntryItems->first()->qty_in : 0) }}" step="0.01" />
                                    <label for="qty_in">Quantity In <span class="text-danger">*</span></label>
                                    @error('qty_in')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" name="qty_out" id="qty_out" class="form-control" placeholder="Enter Quantity Out" value="{{ old('qty_out', ($stockEntry && $stockEntry->stockEntryItems->first()) ? $stockEntry->stockEntryItems->first()->qty_out : 0) }}" step="0.01" />
                                    <label for="qty_out">Quantity Out <span class="text-danger">*</span></label>
                                    @error('qty_out')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="store_location" name="store_location_id" class="select2 form-select" data-placeholder="Select Location/Store">
                                        <option value="">Select Location/Store</option>
                                        @foreach($storeLocations as $loc)
                                            <option value="{{ $loc->id }}" {{ old('store_location_id', ($stockEntry && $stockEntry->stockEntryItems->first()) ? $stockEntry->stockEntryItems->first()->store_location_id : '') == $loc->id ? 'selected' : '' }}>{{ $loc->store_location }}</option>
                                        @endforeach
                                    </select>
                                    <label for="store_location">Location/Store <span class="text-danger">*</span></label>
                                    @error('store_location_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- REMARKS & REFERENCE -->
                        <div class="row mb-4">
                            <div class="col-md-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea name="remarks" id="remarks" class="form-control" placeholder="Enter Remarks">{{ old('remarks', $stockEntry->remarks ?? '') }}</textarea>
                                    <label for="remarks">Remarks</label>
                                    @error('remarks')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-4 mb-4">
                                <label for="formFile" class="form-label">Reference Document</label>
                                <input type="file" class="form-control" id="formFile" name="reference_document">
                                @if($stockEntry && $stockEntry->reference_document)
                                    <a href="{{ asset($stockEntry->reference_document) }}" target="_blank" class="small">View current document</a>
                                @endif
                                @error('reference_document')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <input type="hidden" name="status" value="Draft">
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ url('stock_entries') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        // Initialize date picker
        $('.stock_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            allowInput: true
        });

        const QTY_IN_TYPES = ['Purchase Receipt', 'Transfer Receipt', 'Production Receipt', 'Stock Adjustment', 'Stock Conversion'];
        const QTY_OUT_TYPES = ['Transfer Issue', 'Production Issue', 'Stock Adjustment', 'Stock Conversion'];

        // Entry Type change - show/hide GRN section and control qty fields
        $('#entry_type').on('change', function() {
            let entryType = $(this).val();
            
            // Show GRN section for Purchase Receipt
            if (entryType === 'Purchase Receipt') {
                $('#grn_section').show();
            } else {
                $('#grn_section').hide();
                $('#grn_entry_id').val('').trigger('change');
                toggleManualRequired(true); // Enable manual requirements
            }

            // Control qty_in / qty_out based on entry type
            let allowIn = QTY_IN_TYPES.includes(entryType);
            let allowOut = QTY_OUT_TYPES.includes(entryType);

            if (allowIn && !allowOut) {
                $('#qty_in').prop('readonly', false);
                $('#qty_out').prop('readonly', true).val(0);
            } else if (allowOut && !allowIn) {
                $('#qty_in').prop('readonly', true).val(0);
                $('#qty_out').prop('readonly', false);
            } else {
                $('#qty_in, #qty_out').prop('readonly', false);
            }
        });

    
        // Initial state for Edit Mode
        const stockEntry = @json($stockEntry);
        if (stockEntry && stockEntry.grn_entry_id) {
            const grnItemId = stockEntry.stock_entry_items[0]?.grn_entry_item_id;
            
            // Trigger GRN change logic manually to fetch items and populate dropdown
            if (stockEntry.grn_entry_id) {
                $.get("{{ url('stock_entries/get-grn-items') }}/" + stockEntry.grn_entry_id + "?stock_entry_id=" + stockEntry.id, function(res) {
                    if (res.success && res.items.length > 0) {
                        grnItemsData = res.items;
                        $('#grn_item_selection_section').show();
                        
                        // Show fields but they will be locked
                        $('#select_type_container').hide();
                        $('#raw_material_section').show();
                        $('#finished_goods_section').hide();

                        let itemSelect = $('#grn_entry_item_id');
                        itemSelect.empty();
                        itemSelect.append('<option value="">Select GRN Item</option>');
                        res.items.forEach((item) => {
                            let selected = (item.id == grnItemId) ? 'selected' : '';
                            itemSelect.append(`<option value="${item.id}" ${selected}>${item.raw_material_name} (Qty: ${item.qty_accepted})</option>`);
                        });
                        
                        // If we have a selected item, lock Category/Material
                        if (grnItemId) {
                            setTimeout(() => {
                                $('#store_category option').not(':selected').prop('disabled', true);
                                $('#material option').not(':selected').prop('disabled', true);
                                $('.select2').trigger('change.select2');
                            }, 500); // Give plenty of time for auto-fill logic to settle
                        }

                        // Ensure single item fields stay visible and populated
                        $('#single_item_fields').show();
                        toggleManualRequired(true);
                    }
                });
            }
        }

        // GRN Entry change - populate GRN Item dropdown
        let grnItemsData = [];
        $('#grn_entry_id').on('change', function() {
            let grnId = $(this).val();
            let entryType = $('#entry_type').val();

            if (grnId) {
                $.get("{{ url('stock_entries/get-grn-items') }}/" + grnId + "?stock_entry_id=" + (stockEntry ? stockEntry.id : ''), function(res) {
                    if (res.success && res.items.length > 0) {
                        grnItemsData = res.items;
                        $('#grn_item_selection_section').show();
                        
                        // Show Category/Material but they will be locked when item is selected
                        $('#select_type_container').hide();
                        $('#raw_material_section').show();
                        $('#finished_goods_section').hide();
                        
                        let itemSelect = $('#grn_entry_item_id');
                        itemSelect.empty();
                        itemSelect.append('<option value="">Select GRN Item</option>');
                        res.items.forEach((item) => {
                            itemSelect.append(`<option value="${item.id}">${item.raw_material_name} (Qty: ${item.qty_accepted})</option>`);
                        });

                        // Ensure we are in raw material mode
                        $('#raw_material').prop('checked', true).trigger('change');
                        
                        // Show single item fields
                        $('#single_item_fields').show();
                        toggleManualRequired(true);
                    }
                });
            } else {
                $('#grn_item_selection_section').hide();
                $('#grn_entry_item_id').empty().append('<option value="">Select GRN Item</option>');
                grnItemsData = [];
                
                // Show fields again when no GRN selected
                $('#select_type_container').show();
                $('input[name="entry_type_radio"]:checked').trigger('change');
                
                // Re-enable Category and Material dropdowns
                $('#store_category, #material').prop('disabled', false).find('option').prop('disabled', false);
                $('#store_category_hidden, #material_hidden').prop('disabled', true);
                $('.select2').trigger('change.select2');

                // Clear fields if GRN is removed
                $('#store_category, #material, #uom, #qty_in, #qty_out, #store_location').val('').trigger('change');
            }
        });

        // When a GRN Item is selected - Auto populate fields and DISABLE others
        $('#grn_entry_item_id').on('change', function() {
            let itemId = $(this).val();
            
            // Always re-enable all options first
            $('#store_category, #material').find('option').prop('disabled', false);
            
            if (itemId) {
                let item = grnItemsData.find(i => i.id == itemId);
                if (item) {
                    // Update Category
                    $('#store_category').val(item.store_category_id).trigger('change');
                    $('#store_category option').not(':selected').prop('disabled', true);
                    
                    // Trigger material update with a slight delay to ensure category selection finished
                    setTimeout(() => {
                        $('#material').val(item.raw_material_id).trigger('change');
                        $('#material option').not(':selected').prop('disabled', true);
                        
                        $('.select2').trigger('change.select2');
                    }, 100);

                    // Handle submit values for disabled fields if needed (though we disabled options not the select)
                    // If we want to be safe, we can disable the select and use hidden fields
                    /*
                    $('#store_category, #material').prop('disabled', true);
                    $('#store_category_hidden').val(item.store_category_id).prop('disabled', false);
                    $('#material_hidden').val(item.raw_material_id).prop('disabled', false);
                    */

                    $('#uom').val(item.uom_id).trigger('change');
                    
                    let entryType = $('#entry_type').val();
                    if (QTY_IN_TYPES.includes(entryType)) {
                        $('#qty_in').val(item.qty_accepted);
                        $('#qty_out').val(0);
                    }
                    $('#store_location').val(item.store_location_id).trigger('change');
                }
            } else {
                // Re-enable options if GRN item is cleared
                $('#store_category, #material').find('option').prop('disabled', false);
                $('.select2').trigger('change.select2');
            }
        });

        // Function to update qty columns visibility based on entry type
        function updateQtyColumns() {
            let entryType = $('#entry_type').val();
            let allowIn = QTY_IN_TYPES.includes(entryType);
            let allowOut = QTY_OUT_TYPES.includes(entryType);

            if (allowIn && !allowOut) {
                $('.qty-in-col').show();
                $('.qty-out-col').hide();
            } else if (allowOut && !allowIn) {
                $('.qty-in-col').hide();
                $('.qty-out-col').show();
            } else {
                $('.qty-in-col, .qty-out-col').show();
            }
        }

        // Dependent Dropdown for Store Category -> Material
        let $materialSelect = $('#material');
        let allMaterials = $materialSelect.find('option').clone();

        $('#store_category').on('change', function() {
            let categoryId = $(this).val();
            let currentMaterial = $materialSelect.val();

            $materialSelect.empty();
            $materialSelect.append('<option value="">Select Material</option>');

            allMaterials.each(function() {
                let optionCat = $(this).data('category');
                if ($(this).val() !== '') {
                    if (!categoryId || optionCat == categoryId) {
                        $materialSelect.append($(this));
                    }
                }
            });

            // Restore selection if valid
            $materialSelect.val(currentMaterial).trigger('change');
        });

        // Trigger on load if category is selected
        if ($('#store_category').val()) {
            $('#store_category').trigger('change');
        }

        // Toggle Raw Material / Finished Goods sections
        $('input[name="entry_type_radio"]').on('change', function() {
            // In one-by-one mode, we always want requirements if we are not in legacy table mode
            // Since legacy table is removed, we check if we are in manual/one-by-one entry
            let isRequired = true; 

            if ($(this).val() === 'raw_material') {
                $('#raw_material_section').show();
                $('#finished_goods_section, #finished_raw_materials').hide();
                if(isRequired) {
                    $('#store_category, #material').prop('required', true);
                    $('#finished_item').prop('required', false);
                }
            } else if ($(this).val() === 'finished_goods') {
                $('#raw_material_section').hide();
                $('#finished_goods_section').show();
                $('#finished_raw_materials').hide();
                if(isRequired) {
                    $('#store_category, #material').prop('required', false);
                    $('#finished_item').prop('required', true);
                }
            }
        });

        // Helper to toggle manual fields requirements
        function toggleManualRequired(enable) {
            $('#uom, #qty_in, #qty_out, #store_location').prop('required', enable);
            if (enable) {
                $('input[name="entry_type_radio"]:checked').trigger('change');
            } else {
                $('#store_category, #material, #finished_item').prop('required', false);
            }
        }

        // Trigger on page load if editing
        if (savedItems.length === 0) {
            $('input[name="entry_type_radio"]:checked').trigger('change');
        }
        $('#entry_type').trigger('change');

        // Raw material data for each finished good (static)
        const finishedGoodsMaterials = {
            'ITEM001': [{
                    sno: 1,
                    name: 'Cotton Fabric 60 GSM',
                    uom: 'MTR',
                    qty: 2.5
                },
                {
                    sno: 2,
                    name: 'Buttons - White 12mm',
                    uom: 'PCS',
                    qty: 6
                },
                {
                    sno: 3,
                    name: 'Thread - Polyester',
                    uom: 'ROLL',
                    qty: 0.2
                },
            ],
            'ITEM002': [{
                    sno: 1,
                    name: 'Denim Fabric 200 GSM',
                    uom: 'MTR',
                    qty: 2.8
                },
                {
                    sno: 2,
                    name: 'Metal Buttons',
                    uom: 'PCS',
                    qty: 6
                },
                {
                    sno: 3,
                    name: 'Thread - Blue',
                    uom: 'ROLL',
                    qty: 0.15
                },
            ],
            'ITEM003': [{
                    sno: 1,
                    name: 'Cotton Blend Fabric',
                    uom: 'MTR',
                    qty: 2.3
                },
                {
                    sno: 2,
                    name: 'Buttons',
                    uom: 'PCS',
                    qty: 5
                },
                {
                    sno: 3,
                    name: 'Thread - White',
                    uom: 'ROLL',
                    qty: 0.1
                },
            ],
            'ITEM004': [{
                    sno: 1,
                    name: 'Pique Fabric',
                    uom: 'MTR',
                    qty: 1.5
                },
                {
                    sno: 2,
                    name: 'Buttons - Plastic',
                    uom: 'PCS',
                    qty: 3
                },
                {
                    sno: 3,
                    name: 'Thread - Red',
                    uom: 'ROLL',
                    qty: 0.05
                },
            ],
            'ITEM005': [{
                    sno: 1,
                    name: 'Linen Fabric Premium',
                    uom: 'MTR',
                    qty: 2.4
                },
                {
                    sno: 2,
                    name: 'Buttons - Pearl',
                    uom: 'PCS',
                    qty: 6
                },
                {
                    sno: 3,
                    name: 'Thread - Beige',
                    uom: 'ROLL',
                    qty: 0.2
                },
            ],
        };

        // When Finished Goods selected → show related raw materials
        $('#finished_item').on('change', function() {
            const selectedItem = $(this).val();
            const tableBody = $('#raw_material_table_body');
            tableBody.empty();

            if (selectedItem && finishedGoodsMaterials[selectedItem]) {
                finishedGoodsMaterials[selectedItem].forEach(row => {
                    tableBody.append(`
                    <tr>
                        <td>${row.sno}</td>
                        <td>${row.name}</td>
                        <td>${row.uom}</td>
                        <td>${row.qty}</td>
                    </tr>
                `);
                });
                $('#finished_raw_materials').show();
            } else {
                $('#finished_raw_materials').hide();
            }
        });
    });
</script>


@endsection
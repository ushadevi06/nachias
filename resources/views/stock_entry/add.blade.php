@extends('layouts.common')
@section('title', ($stockEntry ? 'Edit' : 'Add') . ' Stock Entry - ' . env('WEBSITE_NAME'))
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
                                    <input type="text" name="stock_entry_no" class="form-control" placeholder="Enter Stock Entry No" value="{{ $stockEntry->stock_entry_no ?? $nextStockNo }}" readonly />
                                    <label for="code">Stock Entry No <span class="text-danger">*</span></label>
                                    @error('stock_entry_no')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="stock_date" class="form-control stock_date" placeholder="Enter Stock Date" value="{{ old('stock_date', $stockEntry ? $stockEntry->stock_date->format('d-m-Y') : date('d-m-Y')) }}" {{ $stockEntry ? 'readonly' : '' }} />
                                    <label for="stock_date">Stock Date <span class="text-danger">*</span></label>
                                    @error('stock_date')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <input type="hidden" name="entry_type" value="Purchase Receipt">
                            <div class="col-lg-4 mb-4" id="grn_section">
                                <div class="form-floating form-floating-outline">
                                    <select id="grn_entry_id" name="grn_entry_id" class="select2 form-select" data-placeholder="Select GRN Entry" {{ $stockEntry ? 'disabled' : '' }}>
                                        <option value="">Select GRN Entry No</option>
                                        @foreach($grnEntries as $grn)
                                            <option value="{{ $grn->id }}" {{ old('grn_entry_id', $stockEntry->grn_entry_id ?? '') == $grn->id ? 'selected' : '' }}>{{ $grn->grn_number }}</option>
                                        @endforeach
                                    </select>
                                    <label for="grn_entry_id">GRN Entry No <span class="text-danger">*</span></label>
                                    @if($stockEntry)
                                        <input type="hidden" name="grn_entry_id" value="{{ $stockEntry->grn_entry_id }}">
                                    @endif
                                    @error('grn_entry_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="grn_entry_item_id" name="grn_entry_item_id" class="select2 form-select" data-placeholder="Select GRN Item">
                                        <option value="">Select GRN Item</option>
                                    </select>
                                    <label for="grn_entry_item_id">Select GRN Item <span class="text-danger">*</span></label>
                                    @error('grn_entry_item_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <input type="hidden" name="entry_type_radio" value="raw_material">
                        </div>
                        <!-- GRN Items Table (Removed for one-by-one selection) -->
                        {{-- <div class="row mb-4" id="grn_items_section" style="display:none;"> ... </div> --}}

                        <div class="row mb-4">

                        <!-- RAW MATERIAL SECTION -->
                        <div class="row mb-4" id="raw_material_section" style="display:none;">
                            <div class="col-lg-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="store_category" name="store_category_id" class="select2 form-select" data-placeholder="Select Store Category" disabled>
                                        <option value="">Select Store Category</option>
                                        @foreach($storeCategories as $cat)
                                            <option value="{{ $cat->id }}" data-code="{{ $cat->code }}" {{ old('store_category_id', ($stockEntry && $stockEntry->stockEntryItems->first()) ? $stockEntry->stockEntryItems->first()->store_category_id : '') == $cat->id ? 'selected' : '' }}>{{ $cat->category_name }}({{ $cat->code }})</option>
                                        @endforeach
                                    </select>
                                    <label for="store_category">Store Category <span class="text-danger">*</span></label>
                                    <input type="hidden" id="store_category_val" name="store_category_id" value="{{ old('store_category_id', ($stockEntry && $stockEntry->stockEntryItems->first()) ? $stockEntry->stockEntryItems->first()->store_category_id : '') }}">
                                </div>
                            </div>

                            <div class="col-lg-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="material" name="raw_material_id" class="select2 form-select" data-placeholder="Select Material" disabled>
                                        <option value="">Select Material</option>
                                        @foreach($rawMaterials as $mat)
                                            <option value="{{ $mat->id }}" data-uom="{{ $mat->uom_id }}" data-category="{{ $mat->store_category_id }}" {{ old('raw_material_id', ($stockEntry && $stockEntry->stockEntryItems->first()) ? $stockEntry->stockEntryItems->first()->raw_material_id : '') == $mat->id ? 'selected' : '' }}>{{ $mat->name }}({{ $mat->code }})</option>
                                        @endforeach
                                    </select>
                                    <label for="material">Material <span class="text-danger">*</span></label>
                                    <input type="hidden" id="material_val" name="raw_material_id" value="{{ old('raw_material_id', ($stockEntry && $stockEntry->stockEntryItems->first()) ? $stockEntry->stockEntryItems->first()->raw_material_id : '') }}">
                                </div>
                            </div>
                        </div>

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
                                    <input type="number" name="qty_in" id="qty_in" class="form-control" placeholder="Enter Quantity In" value="{{ old('qty_in', $stockEntry ? $stockEntry->stockEntryItems->count() : 1) }}" step="1" min="1" />
                                    <label for="qty_in">Quantity In <span class="text-danger">*</span></label>
                                    @error('qty_in')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="text-muted small">Total quantity available from GRN item will be split into individual entries.</div>
                            </div>
                            <div class="col-lg-4 mb-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" name="price" id="price" class="form-control" placeholder="Enter Unit Price" value="{{ old('price', ($stockEntry && $stockEntry->stockEntryItems->first()) ? $stockEntry->stockEntryItems->first()->price : '') }}" step="0.01" min="0" />
                                    <label for="price">Unit Price</label>
                                    @error('price')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <input type="hidden" name="qty_out" value="0">

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
                                    <a href="{{ url('uploads/stock_entries/' . $stockEntry->reference_document) }}" target="_blank" class="small">View current document</a>
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

        $('.stock_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            allowInput: true
        });

        const savedItems = @json($savedItems);
        const stockEntry = @json($stockEntry);

        let grnItemsData = [];
        $('#grn_entry_id').on('change', function() {
            let grnId = $(this).val();

            if (grnId) {
                $.get("{{ url('stock_entries/get-grn-items') }}/" + grnId + "?stock_entry_id=" + (stockEntry ? stockEntry.id : ''), function(res) {
                    if (res.success && res.items.length > 0) {
                        grnItemsData = res.items;
                        
                        $('#raw_material_section').show();
                        
                        let itemSelect = $('#grn_entry_item_id');
                        itemSelect.empty();
                        itemSelect.append('<option value="">Select GRN Item</option>');
                        
                        let hasSelection = false;
                        res.items.forEach((item) => {
                            let isSelected = '';
                            if (savedItems && savedItems.length > 0 && savedItems[0].grn_entry_item_id == item.id) {
                                isSelected = 'selected';
                                hasSelection = true;
                            } else if ((!savedItems || savedItems.length === 0) && res.items.length === 1) {
                                isSelected = 'selected';
                                hasSelection = true;
                            }
                            itemSelect.append(`<option value="${item.id}" ${isSelected}>${item.raw_material_name} (Qty Available: ${item.qty_accepted})</option>`);
                        });

                        $('#single_item_fields').show();
                        toggleManualRequired(true);

                        if (hasSelection) {
                            setTimeout(() => {
                                itemSelect.trigger('change');
                            }, 100);
                        }
                    }
                });
            } else {
                $('#grn_entry_item_id').empty().append('<option value="">Select GRN Item</option>');
                grnItemsData = [];
                
                $('#store_category, #material').prop('disabled', false).find('option').prop('disabled', false);
                $('.select2').trigger('change.select2');

                $('#store_category, #material, #uom, #qty_in, #store_location, #price').val('').trigger('change');
            }
        });

        $('#grn_entry_item_id').on('change', function() {
            let itemId = $(this).val();
            
            if (itemId) {
                let item = grnItemsData.find(i => i.id == itemId);
                if (item) {
                    $('#store_category').val(item.store_category_id).trigger('change');
                    $('#store_category_val').val(item.store_category_id);
                    
                    setTimeout(() => {
                        $('#material').val(item.raw_material_id).trigger('change');
                        $('#material_val').val(item.raw_material_id);
                        $('.select2').trigger('change.select2');
                    }, 100);

                    $('#uom').val(item.uom_id).trigger('change');
                    
                    if (!$('#qty_in').val() || !stockEntry) {
                        $('#qty_in').val(item.qty_accepted);
                    }
                    $('#qty_in').attr('max', item.qty_accepted);

                    $('#price').val(item.rate);
                    $('#store_location').val(item.store_location_id).trigger('change');
                }
            }
        });

        $('#qty_in').on('input', function() {
            let max = parseFloat($(this).attr('max'));
            let current = parseFloat($(this).val());
            if (current > max) {
                $(this).val(max);
                alert('Quantity cannot be greater than available quantity (' + max + ')');
            }
        });
        
        function toggleManualRequired(enable) {
            $('#uom, #qty_in, #store_location, #price').prop('required', enable);
        }

        if (stockEntry) {
            $('#grn_entry_id').trigger('change');
        }
    });
</script>


@endsection
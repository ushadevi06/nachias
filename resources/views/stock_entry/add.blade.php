@extends('layouts.common')
@section('title', ($stockEntry ? 'Edit' : 'Add') . ' Stock Entry - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <form action="{{ url('stock_entries/add' . ($stockEntry ? '/' . $stockEntry->id : '')) }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                @csrf
                <div class="col-lg-12">
                    @include('flash_messages')
                </div>
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
                            <input type="hidden" name="entry_type_radio" value="raw_material">
                        </div>

                        <div class="row mb-4" id="grn_items_section" style="display:none;">
                            <div class="col-12">
                                <h5>GRN Items</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Material Category</th>
                                                <th>Material</th>
                                                <th style="width: 150px;">UOM</th>
                                                <th style="width: 250px;">Qty In</th>
                                                <th style="width: 150px;">Price</th>
                                                <th style="width: 250px;">Location</th>
                                            </tr>
                                        </thead>
                                        <tbody id="grn_items_tbody">
                                        </tbody>
                                    </table>
                                </div>
                                @error('items')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
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
                                <div class="form-floating form-floating-outline text-black">
                                    <input type="file" class="form-control" id="formFile" name="reference_document" accept="*">
                                    @if($stockEntry && $stockEntry->reference_document)
                                        @php
                                            $ext = pathinfo($stockEntry->reference_document, PATHINFO_EXTENSION);
                                            $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                            $fileUrl = url('uploads/stock_entries/' . $stockEntry->reference_document);
                                        @endphp
                                        <div class="mt-2 p-2 border rounded d-inline-flex align-items-center">
                                            @if($isImage)
                                                <img src="{{ $fileUrl }}" class="rounded cursor-pointer view-image" data-image="{{ $fileUrl }}" width="40" height="40" style="object-fit: cover;" alt="Reference">
                                            @else
                                                <a href="{{ $fileUrl }}" target="_blank" class="text-decoration-none d-flex align-items-center">
                                                    @if(strtolower($ext) == 'pdf')
                                                        <i class="ri ri-file-pdf-fill text-danger fs-3"></i>
                                                    @else
                                                        <i class="ri ri-file-text-fill text-primary fs-3"></i>
                                                    @endif
                                                    <span class="ms-1 small text-dark fw-bold text-uppercase">{{ $ext }}</span>
                                                </a>
                                            @endif
                                            <span class="ms-2 small text-muted">Current File</span>
                                        </div>
                                    @endif
                                    <label for="formFile" class="form-label">Reference Document</label>
                                    <small class="text-muted d-block mt-1">Max file size: 2MB. Supported formats: JPG, PNG, JPEG, WEBP, PDF, DOC, DOCX</small>
                                    @error('reference_document')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
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
        const savedItems = @json($savedItems);
        const stockEntry = @json($stockEntry);
        const oldItems = @json(old('items', []));
        const validationErrors = @json($errors->toArray());
        let grnItemsData = [];
        let storeLocationsOptions = `<option value="">Select Location</option>`;
        @foreach($storeLocations as $loc)
            storeLocationsOptions += `<option value="{{ $loc->id }}">{{ $loc->store_location }}</option>`;
        @endforeach


        $('#grn_entry_id').on('change', function() {
            let grnId = $(this).val();
            if (grnId) {
                $.get("{{ url('stock_entries/get-grn-items') }}/" + grnId + "?stock_entry_id=" + (stockEntry ? stockEntry.id : ''), function(res) {
                    if (res.success && res.items.length > 0) {
                        grnItemsData = res.items;                    
                        $('#grn_items_section').show();
                        let tbody = $('#grn_items_tbody');
                        tbody.empty();
                        

                        res.items.forEach((item, index) => {
                            let oldItem = null;
                            if (oldItems && Object.keys(oldItems).length > 0) {
                                let oldItemsArray = Array.isArray(oldItems) ? oldItems : Object.values(oldItems);
                                oldItem = oldItemsArray.find(o => o.grn_entry_item_id == item.id);
                            }
                            
                            let savedItem = null;
                            if (savedItems && savedItems.length > 0) {
                                savedItem = savedItems.find(s => s.grn_entry_item_id == item.id);
                            }

                            let qtyIn = (oldItem && oldItem.qty_in !== null && oldItem.qty_in !== '') ? oldItem.qty_in : (savedItem ? savedItem.qty_in : item.qty_accepted);
                            let price = (oldItem && oldItem.price !== null && oldItem.price !== '') ? oldItem.price : (savedItem ? savedItem.price : item.rate);
                            let locId = (oldItem && oldItem.store_location_id) ? oldItem.store_location_id : (savedItem ? savedItem.store_location_id : (item.store_location_id ? item.store_location_id : ''));

                            let locError = validationErrors['items.' + index + '.store_location_id'] ? validationErrors['items.' + index + '.store_location_id'][0] : '';
                            let qtyError = validationErrors['items.' + index + '.qty_in'] ? validationErrors['items.' + index + '.qty_in'][0] : '';
                            let priceError = validationErrors['items.' + index + '.price'] ? validationErrors['items.' + index + '.price'][0] : '';

                            let row = `
                                <tr>
                                    <td>
                                        <input type="hidden" name="items[${index}][selected]" value="1">
                                        <input type="hidden" name="items[${index}][grn_entry_item_id]" value="${item.id}">
                                        <input type="hidden" name="items[${index}][raw_material_id]" value="${item.raw_material_id}">
                                        <input type="hidden" name="items[${index}][store_category_id]" value="${item.store_category_id}">
                                        <input type="hidden" name="items[${index}][uom_id]" value="${item.uom_id}">
                                        <input type="hidden" name="items[${index}][fabric_type_id]" value="${item.fabric_type_id || ''}">
                                        ${item.store_category_name}
                                    </td>
                                    <td>${item.raw_material_name}</td>
                                    <td>${item.uom_name}</td>
                                    <td>
                                        <input type="number" name="items[${index}][qty_in]" class="form-control form-control-sm item-qty" value="${qtyIn}" step="0.01" placeholder="Max: ${item.qty_accepted}">
                                        <div class="text-danger small qty-error" style="${qtyError ? '' : 'display:none;'}">${qtyError || 'Quantity is required'}</div>
                                    </td>
                                    <td>
                                        <input type="number" name="items[${index}][price]" class="form-control form-control-sm item-price" value="${price}" step="0.01">
                                        <div class="text-danger small price-error" style="${priceError ? '' : 'display:none;'}">${priceError || 'Price is required'}</div>
                                    </td>
                                    <td>
                                        <select name="items[${index}][store_location_id]" class="form-select select2 form-select-sm item-location">
                                            ${storeLocationsOptions}
                                        </select>
                                        <div class="text-danger small loc-error" style="${locError ? '' : 'display:none;'}">${locError || 'Location is required'}</div>
                                    </td>
                                </tr>
                            `;
                            tbody.append(row);
                            
                            let locSelect = tbody.find(`select[name="items[${index}][store_location_id]"]`);
                            if (locId) {
                                locSelect.val(locId);
                            }
                            $('.select2').each(function() {
                                $(this).select2({
                                    dropdownParent: $(this).parent(),
                                    placeholder: $(this).data('placeholder'),
                                    width: '100%'
                                });
                            });
                        });
                    }
                });
            } else {
                $('#grn_items_section').hide();
                $('#grn_items_tbody').empty();
                grnItemsData = [];
            }
        });

        $(document).on('keypress', '.item-qty, .item-price', function (e) {
            if (e.which === 45 || e.which === 43) {
                e.preventDefault();
            }
        });

        if (stockEntry || $('#grn_entry_id').val()) {
            $('#grn_entry_id').trigger('change');
        }
    });
</script>
@endsection
@extends('layouts.common')
@section('title', ($item ? 'Edit' : 'Add') . ' Item - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $item ? 'Edit' : 'Add' }} Item</h4>
                    </div>
                    <form action="{{ url('items/add' . ($item ? '/' . $item->id : '')) }}" method="POST">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="brand_id" id="brand" class="select2 form-select @error('brand_id') is-invalid @enderror"
                                        data-placeholder="Select Brand">
                                        <option value="">Select Brand</option>
                                        @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}"
                                            {{ old('brand_id', $item->brand_id ?? '') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->brand_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="brand">Brand <span class="text-danger">*</span></label>
                                </div>
                                @error('brand_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="brand_category_id" id="brand_category" class="select2 form-select @error('brand_category_id') is-invalid @enderror"
                                        data-placeholder="Select Brand Category">
                                        <option value="">Select Brand Category</option>
                                        @foreach($brandCategories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('brand_category_id', $item->brand_category_id ?? '') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}({{ $category->code }})
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="brand_category">Brand Category <span
                                            class="text-danger">*</span></label>
                                </div>
                                @error('brand_category_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- <div class="col-lg-6 col-xl-4 mb-4">
                                <label class="form-label d-block">Select Type <span class="text-danger">*</span></label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('entry_type') is-invalid @enderror" type="radio" name="entry_type" id="raw_material" value="raw_material" {{ old('entry_type', $item->entry_type ?? '') == 'raw_material' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="raw_material">Raw Material</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input @error('entry_type') is-invalid @enderror" type="radio" name="entry_type" id="items" value="items" {{ old('entry_type', $item->entry_type ?? 'items') == 'items' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="items">Items</label>
                                    </div>
                                    @error('entry_type')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                            </div> --}}
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter Name" name="name"
                                        value="{{ old('name', $item->name ?? '') }}">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                </div>
                                @error('name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" placeholder="Enter Code" name="code"
                                        value="{{ old('code', $item->code ?? '') }}">
                                    <label for="code">Code <span class="text-danger">*</span></label>
                                </div>
                                @error('code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="style" id="style" class="select2 form-select @error('style') is-invalid @enderror" data-placeholder="Select Style">
                                        <option value="">Select Style</option>
                                        <option value="Plain" {{ old('style', $item->style ?? '') == 'Plain' ? 'selected' : '' }}>
                                            Plain
                                        </option>
                                        <option value="Print" {{ old('style', $item->style ?? '') == 'Print' ? 'selected' : '' }}>
                                            Print
                                        </option>
                                        <option value="Checked"
                                            {{ old('style', $item->style ?? '') == 'Checked' ? 'selected' : '' }}>
                                            Checked</option>
                                    </select>
                                    <label for="style">Style</label>
                                </div>
                                @error('style')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="fabric_type_id" id="fabric_type" class="select2 form-select @error('fabric_type_id') is-invalid @enderror"
                                        data-placeholder="Select Fabric Type">
                                        <option value="">Select Fabric Type</option>
                                        @foreach($fabricTypes as $fabricType)
                                        <option value="{{ $fabricType->id }}"
                                            {{ old('fabric_type_id', $item->fabric_type_id ?? '') == $fabricType->id ? 'selected' : '' }}>
                                            {{ $fabricType->fabric_type }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="fabric_type">Fabric Type</label>
                                </div>
                                @error('fabric_type_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('design_art_no') is-invalid @enderror" id="design_art_no" placeholder="Enter Design Art Number"
                                        name="design_art_no" value="{{ old('design_art_no', $item->design_art_no ?? '') }}">
                                    <label for="design_art_no">Design Art Number</label>
                                </div>
                                @error('design_art_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="uom_id" id="uom" class="select2 form-select @error('uom_id') is-invalid @enderror" data-placeholder="Select UOM">
                                        <option value="">Select UOM</option>
                                        @foreach($uoms as $uom)
                                        <option value="{{ $uom->id }}"
                                            {{ old('uom_id', $item->uom_id ?? '') == $uom->id ? 'selected' : '' }}>
                                            {{ $uom->uom_code }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="uom">UOM <span class="text-danger">*</span></label>
                                </div>
                                @error('uom_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline position-relative">
                                    @php
                                        $sizeRatioDisplay = '';
                                        $sizeRatioIdValue = old('size_ratio_id', $item->size_ratio_id ?? '');
                                        if ($sizeRatioIdValue) {
                                            $selectedSizeRatio = \App\Models\SizeRatio::find($sizeRatioIdValue);
                                            if ($selectedSizeRatio) {
                                                $sizeRatioDisplay = $selectedSizeRatio->size . '(' . $selectedSizeRatio->ratio .
                                                ')';
                                            }
                                    }
                                    @endphp
                                    <input type="text" id="selected_size_ratio" class="form-control pe-5 @error('size_ratio_id') is-invalid @enderror" placeholder="Select Size/Ratio" readonly value="{{ $sizeRatioDisplay }}">
                                    <label for="selected_size_ratio">Size / Ratio</label>
                                    <button type="button" class="btn btn-outline-primary position-absolute end-0 top-0 h-100 rounded-0" data-bs-toggle="modal" data-bs-target="#sizeRatioModal" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">Select</button>
                                </div>
                                <input type="hidden" name="size_ratio_id" id="size_ratio_id" value="{{ old('size_ratio_id', $item->size_ratio_id ?? '') }}">
                                @error('size_ratio_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    @php
                                        $selectedColors = [];
                                        $colorIdValue = old('color_id', $item->color_id ?? '');
                                        if (!empty($colorIdValue)) {
                                            if (is_string($colorIdValue) && str_contains($colorIdValue, ',')) {
                                                $selectedColors = explode(',', $colorIdValue);
                                            } elseif (is_string($colorIdValue)) {
                                                $selectedColors = json_decode($item->color_id, true) ?? [];
                                            } elseif (is_array($item->color_id)) {
                                                $selectedColors = $item->color_id;
                                            }
                                        }
                                        $selectedColors = array_map('intval', $selectedColors);
                                    @endphp

                                    <select name="color_id[]" class="select2 form-select @error('color_id') is-invalid @enderror" multiple data-placeholder="Select Color">
                                        @foreach($colors as $color)
                                        <option value="{{ $color->id }}" {{ in_array($color->id, old('color_id', $selectedColors)) ? 'selected' : '' }}>{{ $color->color_name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="color">Color</label>
                                </div>
                                @error('color_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="product_barcode" placeholder="Product Barcode" name="product_barcode" value="{{ $item->product_barcode ?? 'Auto Generated' }}" disabled>
                                    <label for="product_barcode">Product Barcode</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" class="form-control @error('standard_costing') is-invalid @enderror" id="standard_costing" placeholder="Enter Standard Costing" name="standard_costing" value="{{ old('standard_costing', $item->standard_costing ?? '') }}">
                                    <label for="standard_costing">Standard Costing</label>
                                </div>
                                @error('standard_costing')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active" {{ old('status', $item->status ?? 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status', $item->status ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                </div>
                                @error('status')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h6>Material Consumption and Cost (BOM)</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="store_category_id" class="select2 form-select @error('store_category_id') is-invalid @enderror" data-placeholder="Select Store Category">
                                        <option value="">Select Store Category</option>
                                        @foreach($storeCategories as $storeCategory)
                                        <option value="{{ $storeCategory->id }}" {{ old('store_category_id', $item->store_category_id ?? '') == $storeCategory->id ? 'selected' : '' }}>{{ $storeCategory->category_name }}({{ $storeCategory->code }})</option>
                                        @endforeach
                                    </select>
                                    <label for="store_category_id">Store Category</label>
                                </div>
                                @error('store_category_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="material_select" class="select2 form-select @error('material_select') is-invalid @enderror" data-placeholder="Select Material">
                                        <option value="">Select Material</option>
                                        @foreach($materials as $material)
                                        <option value="{{ $material->id }}" {{ old('material_select', $item->material_select ?? '') == $material->id ? 'selected' : '' }} data-name="{{ $material->name }}" data-code="{{ $material->code }}">{{ $material->name }}({{ $material->code }})</option>
                                        @endforeach
                                    </select>
                                    <label for="material_select">Material</label>
                                </div>
                                @error('material_select')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12">
                                <table class="table table-bordered" id="bom_table">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Store Category</th>
                                            <th>Material</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $relatedMaterials = old('related_materials', $item->related_materials ?? []);
                                        @endphp
                                        @if(is_array($relatedMaterials) && count($relatedMaterials) > 0)
                                        @foreach($relatedMaterials as $index => $material)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $material['category_name'] ?? '' }}</td>
                                            <td>{{ $material['material_name'] ?? '' }}</td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove_row">
                                                    <i class="ri ri-delete-bin-line"></i>
                                                </button>
                                                <input type="hidden" name="related_materials[{{ $loop->iteration }}][category_id]"
                                                    value="{{ $material['category_id'] ?? '' }}">
                                                <input type="hidden" name="related_materials[{{ $loop->iteration }}][category_name]"
                                                    value="{{ $material['category_name'] ?? '' }}">
                                                <input type="hidden" name="related_materials[{{ $loop->iteration }}][material_id]"
                                                    value="{{ $material['material_id'] ?? '' }}">
                                                <input type="hidden" name="related_materials[{{ $loop->iteration }}][material_name]"
                                                    value="{{ $material['material_name'] ?? '' }}">
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <label class="d-block mb-4">Operation Stages</label>
                                <div class="row g-4">
                                    @php
                                        // Get old values from session (after validation error)
                                        $oldOperationStages = old('operation_stages', []);
                                        $oldServiceProviders = old('service_providers', []);

                                        // Get current item values (on edit)
                                        $itemStages = (isset($item) && $item && is_array($item->operation_stages)) ? $item->operation_stages : [];
                                        $itemProviders = (isset($item) && $item && is_array($item->service_providers)) ? $item->service_providers : [];

                                        // Use old values if present (validation error case), otherwise use item values
                                        $stagesToCheck = !empty($oldOperationStages) ? $oldOperationStages : $itemStages;
                                        $providersToUse = !empty($oldServiceProviders) ? $oldServiceProviders : $itemProviders;
                                    @endphp

                                    @foreach($operationStages as $stage)
                                    @php
                                        $stageKey = strtolower($stage->operation_stage_name);
                                        $stageName = $stage->operation_stage_name;

                                        // Check if this stage is in the old/item values
                                        $isChecked = in_array($stageName, $stagesToCheck);

                                        // Get service provider ID
                                        $providerId = $providersToUse[$stageKey] ?? '';
                                    @endphp

                                    <div class="col-md-6 d-flex align-items-center">
                                        <div class="form-check me-3">
                                            <input class="form-check-input @error('operation_stages') is-invalid @enderror" type="checkbox" name="operation_stages[]" value="{{ $stageName }}" id="stage_{{ $stage->id }}" {{ $isChecked ? 'checked' : '' }}>
                                            <label class="form-check-label" for="stage_{{ $stage->id }}">
                                                {{ $stageName }}
                                            </label>
                                        </div>

                                        <select name="service_providers[{{ $stageKey }}]" class="select2 form-select flex-grow-1 @error('service_providers.' . $stageKey) is-invalid @enderror" data-placeholder="Select Service Provider">
                                            <option value="">Select Service Provider</option>
                                            @foreach($serviceProviders as $provider)
                                            <option value="{{ $provider->id }}" {{ $providerId == $provider->id ? 'selected' : '' }}>
                                                {{ $provider->name }}({{ $provider->code }})
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endforeach
                                </div>
                                @error('operation_stages')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12">
                                <h6>Pricing</h6>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" class="form-control @error('wholesale_price') is-invalid @enderror" id="wholesale_price" placeholder="Enter Wholesale Price" name="wholesale_price" value="{{ old('wholesale_price', $item->wholesale_price ?? '') }}">
                                    <label for="wholesale_price">Wholesale Price</label>
                                </div>
                                @error('wholesale_price')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" class="form-control @error('retail_price') is-invalid @enderror" id="retail_price" placeholder="Enter Retail Price" name="retail_price" value="{{ old('retail_price', $item->retail_price ?? '') }}">
                                    <label for="retail_price">Retail Price</label>
                                </div>
                                @error('retail_price')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" class="form-control @error('export_price') is-invalid @enderror" id="export_price" placeholder="Enter Export Price" name="export_price" value="{{ old('export_price', $item->export_price ?? '') }}">
                                    <label for="export_price">Export Price</label>
                                </div>
                                @error('export_price')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('items') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="sizeRatioModal" tabindex="-1" aria-labelledby="sizeRatioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="sizeRatioModalLabel">Select Size / Ratio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table align-middle mb-0" id="sizeRatioTable">
                    <thead class="table-light">
                        <tr>
                            <th>Select</th>
                            <th>Sizes</th>
                            <th>Ratios</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sizeRatios as $sizeRatio)
                        <tr>
                            <td>
                                <input type="radio" name="size_ratio_option" value="{{ $sizeRatio->id }}"
                                    data-sizes="{{ $sizeRatio->size }}" data-ratios="{{ $sizeRatio->ratio }}"
                                    {{ old('size_ratio_id', $item->size_ratio_id ?? '') == $sizeRatio->id ? 'checked' : '' }}>
                            </td>
                            <td>{{ $sizeRatio->size }}</td>
                            <td>{{ $sizeRatio->ratio }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="confirmSizeRatio">Confirm</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#store_category_id, #material_select').select2({
            placeholder: "Select option",
            width: '100%'
        });

        function updateDisabledMaterials() {
            let selectedMaterialIds = [];
            $('#bom_table tbody tr').each(function() {
                let materialId = $(this).find('input[name$="[material_id]"]').val();
                if (materialId) {
                    selectedMaterialIds.push(materialId.toString());
                }
            });

            $('#material_select option').each(function() {
                let optionId = $(this).val();
                if (optionId && selectedMaterialIds.includes(optionId.toString())) {
                    $(this).attr('disabled', 'disabled');
                } else {
                    $(this).removeAttr('disabled');
                }
            });

            $('#material_select').select2({
                placeholder: "Select option",
                width: '100%'
            });
        }

        // Call on page load to handle edit mode
        updateDisabledMaterials();

        $('#store_category_id').on('change', function() {
            let category_id = $(this).val();

            if (!category_id) {
                $('#material_select').html('<option value="">Select Material</option>').trigger('change');
                return;
            }

            $.ajax({
                url: APP_URL + '/get-materials-by-category/' + category_id,
                type: 'GET',
                data: {},
                success: function(response) {
                    let materialsHtml = '<option value="">Select Material</option>';

                    if (response.materials && response.materials.length > 0) {
                        response.materials.forEach(function(material) {
                            materialsHtml += `<option value="${material.id}" data-name="${material.name}" data-code="${material.code}">
                            ${material.name}(${material.code})
                        </option>`;
                        });
                    } else {
                        materialsHtml += '<option value="">No materials found</option>';
                    }

                    $('#material_select').html(materialsHtml).trigger('change');
                    updateDisabledMaterials();
                },
                error: function(xhr) {
                    console.error('Error fetching materials:', xhr);
                    alert('Error loading materials. Please try again.');
                    $('#material_select').html('<option value="">Select Material</option>').trigger('change');
                }
            });
        });

        $('#material_select').on('change', function() {

            let category_id = $('#store_category_id').val();
            let category_text = $('#store_category_id option:selected').text();

            let material_id = $(this).val();
            let material_text = $('#material_select option:selected').text();
            if (!category_id) {
                alert("Please select a Store Category first!");
                $('#material_select').val('').trigger('change');
                return;
            }
            if (!material_id) return;
            let rowCount = $('#bom_table tbody tr').length + 1;
            let row = `
            <tr>
                <td>${rowCount}</td>
                <td>${category_text}</td>
                <td>${material_text}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove_row">
                        <i class="ri ri-delete-bin-line"></i>
                    </button>

                    <input type="hidden" name="related_materials[${rowCount}][category_id]" value="${category_id}">
                    <input type="hidden" name="related_materials[${rowCount}][category_name]" value="${category_text}">
                    <input type="hidden" name="related_materials[${rowCount}][material_id]" value="${material_id}">
                    <input type="hidden" name="related_materials[${rowCount}][material_name]" value="${material_text}">
                </td>
            </tr>
        `;
            $('#bom_table tbody').append(row);
            $('#material_select').val('').trigger('change');
            updateDisabledMaterials();
        });
        $(document).on('click', '.remove_row', function() {
            $(this).closest('tr').remove();
            $('#bom_table tbody tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
            updateDisabledMaterials();
        });
        $('#confirmSizeRatio').on('click', function() {
            let selectedRadio = $('input[name="size_ratio_option"]:checked');

            if (selectedRadio.length === 0) {
                alert('Please select a Size/Ratio');
                return;
            }
            let sizeRatioId = selectedRadio.val();
            let sizes = selectedRadio.data('sizes');
            let ratios = selectedRadio.data('ratios');
            $('#size_ratio_id').val(sizeRatioId);
            $('#selected_size_ratio').val(sizes + '(' + ratios + ')');
            $('#sizeRatioModal').modal('hide');
        });
    });
</script>

@endsection
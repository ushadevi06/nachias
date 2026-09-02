@extends('layouts.common')
@section('title', ($warehouse ? 'Edit Warehouse' : 'Add Warehouse') . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $warehouse ? 'Edit' : 'Add' }} Warehouse</h4>
                    </div>

                    <form action="{{ url('warehouses/add' . ($warehouse ? '/' . $warehouse->id : '')) }}" method="POST" class="common-form" autocomplete="off" id="warehouseForm">
                        @csrf
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('warehouse_name') is-invalid @enderror" id="warehouse_name" placeholder="Enter Warehouse Name" name="warehouse_name" value="{{ old('warehouse_name', $warehouse->warehouse_name ?? '') }}">
                                    <label for="warehouse_name">Warehouse Name <span class="text-danger">*</span></label>
                                </div>
                                @error('warehouse_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active" {{ old('status', $warehouse->status ?? '') == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status', $warehouse->status ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                </div>
                                @error('status')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Brand-wise Capacity Matrix -->
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div>
                                        <h5 class="fw-bold mb-0">Brand Storage Capacities</h5>
                                        <small class="text-muted">Configure style-wise storage capacity for each brand</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddBrandBlock">
                                        <i class="ri ri-add-line me-1"></i> Add More Brand
                                    </button>
                                </div>

                                @if($errors->has('brand_blocks'))
                                    <div class="alert alert-danger py-2 small fw-bold mb-3">{{ $errors->first('brand_blocks') }}</div>
                                @endif

                                <div id="brandCapacitiesContainer">
                                    @php
                                        $blocks = old('brand_blocks', []);
                                        if (empty($blocks) && !empty($existingBrandBlocks)) {
                                            $blocks = $existingBrandBlocks;
                                        }
                                        if (empty($blocks)) {
                                            $blocks = [
                                                [
                                                    'brand_id' => '',
                                                    'styles' => [
                                                        ['style_id' => '', 'capacity_pcs' => 0]
                                                    ]
                                                ]
                                            ];
                                        }
                                    @endphp

                                    @foreach($blocks as $bIdx => $block)
                                        @php
                                            $bId = $block['brand_id'] ?? '';
                                            $stylesList = $block['styles'] ?? [];
                                            if (empty($stylesList)) {
                                                $stylesList = [['style_id' => '', 'capacity_pcs' => 0]];
                                            }
                                        @endphp
                                        <div class="card border mb-3 shadow-none brand-block" data-brand-index="{{ $bIdx }}">
                                            <div class="card-header bg-light py-2 px-3 d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom">
                                                <div class="d-flex align-items-center gap-2 flex-grow-1" style="min-width: 260px; max-width: 480px;">
                                                    <span class="badge bg-primary rounded-circle brand-num-badge" style="width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; font-size: 13px;">{{ $loop->iteration }}</span>
                                                    <div class="flex-grow-1">
                                                        <select name="brand_blocks[{{ $bIdx }}][brand_id]" class="form-select brand-select select2 @error('brand_blocks.'.$bIdx.'.brand_id') is-invalid @enderror" data-placeholder="Select Brand">
                                                            <option value="">Select Brand</option>
                                                            @foreach($brands as $b)
                                                                <option value="{{ $b->id }}" {{ $bId == $b->id ? 'selected' : '' }}>{{ $b->brand_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('brand_blocks.'.$bIdx.'.brand_id')
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="text-muted small">Brand Total: <strong class="text-primary brand-subtotal-pcs">0 Pcs</strong></span>
                                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-brand" title="Delete Brand">
                                                        <i class="ri ri-delete-bin-line"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body p-3">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm align-middle mb-2 style-table">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th style="width: 5%;" class="text-center">#</th>
                                                                <th style="width: 55%;">Style Name <span class="text-danger">*</span></th>
                                                                <th style="width: 30%;">Capacity (Pcs) <span class="text-danger">*</span></th>
                                                                <th style="width: 10%;" class="text-center">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="style-rows-container">
                                                            @foreach($stylesList as $sIdx => $sData)
                                                                @php
                                                                    $sId = $sData['style_id'] ?? '';
                                                                    $sCap = $sData['capacity_pcs'] ?? 0;
                                                                @endphp
                                                                <tr class="style-row">
                                                                    <td class="text-center style-num">{{ $loop->iteration }}</td>
                                                                    <td>
                                                                        <select name="brand_blocks[{{ $bIdx }}][styles][{{ $sIdx }}][style_id]" class="form-select style-select select2 @error('brand_blocks.'.$bIdx.'.styles.'.$sIdx.'.style_id') is-invalid @enderror" data-placeholder="Select Style">
                                                                            <option value="">Select Style</option>
                                                                            @foreach($styles as $st)
                                                                                <option value="{{ $st->id }}" {{ $sId == $st->id ? 'selected' : '' }}>{{ $st->style_name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error('brand_blocks.'.$bIdx.'.styles.'.$sIdx.'.style_id')
                                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                                        @enderror
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" min="0" step="1" name="brand_blocks[{{ $bIdx }}][styles][{{ $sIdx }}][capacity_pcs]" class="form-control capacity-input @error('brand_blocks.'.$bIdx.'.styles.'.$sIdx.'.capacity_pcs') is-invalid @enderror" value="{{ $sCap }}" placeholder="Enter capacity in Pcs">
                                                                        @error('brand_blocks.'.$bIdx.'.styles.'.$sIdx.'.capacity_pcs')
                                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                                        @enderror
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-style" title="Delete Style">
                                                                            <i class="ri ri-delete-bin-line"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <button type="button" class="btn btn-xs btn-outline-secondary btn-add-style" style="font-size: 12px; padding: 3px 10px;">
                                                    <i class="ri ri-add-line me-1"></i> Add Style
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Total Warehouse Capacity Summary -->
                                <div class="card bg-light border p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold fs-6">Total Warehouse Capacity:</span>
                                        <span id="lblTotalCapacity" class="fw-bold fs-5 text-primary">0 Pcs</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('warehouses') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function() {
        const brandOptionsHtml = `
            <option value="">Select Brand</option>
            @foreach($brands as $b)
                <option value="{{ $b->id }}">{{ addslashes($b->brand_name) }}</option>
            @endforeach
        `;

        const styleOptionsHtml = `
            <option value="">Select Style</option>
            @foreach($styles as $st)
                <option value="{{ $st->id }}">{{ addslashes($st->style_name) }}</option>
            @endforeach
        `;

        function initSelect2(element, placeholderText) {
            if ($.fn.select2) {
                $(element).select2({
                    placeholder: placeholderText || 'Select Option',
                    allowClear: true,
                    width: '100%'
                });
            }
        }

        function updateDisabledBrands() {
            let selectedBrandIds = [];
            $('.brand-select').each(function() {
                let val = $(this).val();
                if (val) {
                    selectedBrandIds.push(val.toString());
                }
            });

            $('.brand-select').each(function() {
                let currentVal = $(this).val() ? $(this).val().toString() : '';
                $(this).find('option').each(function() {
                    let optVal = $(this).val() ? $(this).val().toString() : '';
                    if (optVal && optVal !== currentVal && selectedBrandIds.includes(optVal)) {
                        $(this).prop('disabled', true);
                    } else {
                        $(this).prop('disabled', false);
                    }
                });
                if ($(this).data('select2')) {
                    $(this).trigger('change.select2');
                }
            });
        }

        function updateDisabledStyles() {
            $('.brand-block').each(function() {
                let $block = $(this);
                let selectedStyles = [];
                $block.find('.style-select').each(function() {
                    let val = $(this).val();
                    if (val) {
                        selectedStyles.push(val.toString());
                    }
                });

                $block.find('.style-select').each(function() {
                    let currentVal = $(this).val() ? $(this).val().toString() : '';
                    $(this).find('option').each(function() {
                        let optVal = $(this).val() ? $(this).val().toString() : '';
                        if (optVal && optVal !== currentVal && selectedStyles.includes(optVal)) {
                            $(this).prop('disabled', true);
                        } else {
                            $(this).prop('disabled', false);
                        }
                    });
                    if ($(this).data('select2')) {
                        $(this).trigger('change.select2');
                    }
                });
            });
        }

        function updateRowNumbersAndTotals() {
            let grandTotal = 0;

            $('.brand-block').each(function(bIdx) {
                $(this).find('.brand-num-badge').text(bIdx + 1);

                let brandSubtotal = 0;
                $(this).find('.style-row').each(function(sIdx) {
                    $(this).find('.style-num').text(sIdx + 1);
                    let cap = parseInt($(this).find('.capacity-input').val()) || 0;
                    brandSubtotal += cap;
                });

                $(this).find('.brand-subtotal-pcs').text(brandSubtotal.toLocaleString('en-IN') + ' Pcs');
                grandTotal += brandSubtotal;
            });

            $('#lblTotalCapacity').text(grandTotal.toLocaleString('en-IN') + ' Pcs');
        }

        // Initialize Select2 on page load
        $('.brand-select').each(function() {
            initSelect2(this, 'Select Brand');
        });

        $('.style-select').each(function() {
            initSelect2(this, 'Select Style');
        });

        // Add Style row under specific brand
        $(document).on('click', '.btn-add-style', function() {
            let $block = $(this).closest('.brand-block');
            let bIdx = $block.data('brand-index');
            let sIdx = Date.now() + Math.floor(Math.random() * 1000);

            let newRowHtml = `
                <tr class="style-row">
                    <td class="text-center style-num"></td>
                    <td>
                        <select name="brand_blocks[${bIdx}][styles][${sIdx}][style_id]" class="form-select style-select" data-placeholder="Select Style">
                            ${styleOptionsHtml}
                        </select>
                    </td>
                    <td>
                        <input type="number" min="0" step="1" name="brand_blocks[${bIdx}][styles][${sIdx}][capacity_pcs]" class="form-control capacity-input" value="0" placeholder="Enter capacity in Pcs">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-style" title="Delete Style">
                            <i class="ri ri-delete-bin-line"></i>
                        </button>
                    </td>
                </tr>
            `;
            let $newRow = $(newRowHtml);
            $block.find('.style-rows-container').append($newRow);
            initSelect2($newRow.find('.style-select'), 'Select Style');
            updateDisabledStyles();
            updateRowNumbersAndTotals();
        });

        // Remove Style row
        $(document).on('click', '.btn-remove-style', function() {
            let $tbody = $(this).closest('.style-rows-container');
            if ($tbody.find('.style-row').length > 1) {
                let $row = $(this).closest('tr');
                let $select = $row.find('.style-select');
                if ($select.data('select2')) {
                    $select.select2('destroy');
                }
                $row.remove();
                updateDisabledStyles();
                updateRowNumbersAndTotals();
            } else {
                alert('At least one style is required for this brand.');
            }
        });

        // Add Brand block
        $('#btnAddBrandBlock').on('click', function() {
            let bIdx = Date.now() + Math.floor(Math.random() * 1000);
            let sIdx = bIdx + 1;

            let newBlockHtml = `
                <div class="card border mb-3 shadow-none brand-block" data-brand-index="${bIdx}">
                    <div class="card-header bg-light py-2 px-3 d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom">
                        <div class="d-flex align-items-center gap-2 flex-grow-1" style="min-width: 260px; max-width: 480px;">
                            <span class="badge bg-primary rounded-circle brand-num-badge" style="width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; font-size: 13px;"></span>
                            <div class="flex-grow-1">
                                <select name="brand_blocks[${bIdx}][brand_id]" class="form-select brand-select" data-placeholder="Select Brand">
                                    ${brandOptionsHtml}
                                </select>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted small">Brand Total: <strong class="text-primary brand-subtotal-pcs">0 Pcs</strong></span>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-brand" title="Delete Brand">
                                <i class="ri ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle mb-2 style-table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%;" class="text-center">#</th>
                                        <th style="width: 55%;">Style Name <span class="text-danger">*</span></th>
                                        <th style="width: 30%;">Capacity (Pcs) <span class="text-danger">*</span></th>
                                        <th style="width: 10%;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="style-rows-container">
                                    <tr class="style-row">
                                        <td class="text-center style-num">1</td>
                                        <td>
                                            <select name="brand_blocks[${bIdx}][styles][${sIdx}][style_id]" class="form-select style-select" data-placeholder="Select Style">
                                                ${styleOptionsHtml}
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" min="0" step="1" name="brand_blocks[${bIdx}][styles][${sIdx}][capacity_pcs]" class="form-control capacity-input" value="0" placeholder="Enter capacity in Pcs">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-style" title="Delete Style">
                                                <i class="ri ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-xs btn-outline-secondary btn-add-style" style="font-size: 12px; padding: 3px 10px;">
                            <i class="ri ri-add-line me-1"></i> Add Style
                        </button>
                    </div>
                </div>
            `;

            let $newBlock = $(newBlockHtml);
            $('#brandCapacitiesContainer').append($newBlock);
            initSelect2($newBlock.find('.brand-select'), 'Select Brand');
            initSelect2($newBlock.find('.style-select'), 'Select Style');
            updateDisabledBrands();
            updateDisabledStyles();
            updateRowNumbersAndTotals();
        });

        // Remove Brand block
        $(document).on('click', '.btn-remove-brand', function() {
            if ($('.brand-block').length > 1) {
                let $block = $(this).closest('.brand-block');
                $block.find('select').each(function() {
                    if ($(this).data('select2')) {
                        $(this).select2('destroy');
                    }
                });
                $block.remove();
                updateDisabledBrands();
                updateDisabledStyles();
                updateRowNumbersAndTotals();
            } else {
                alert('At least one brand is required.');
            }
        });

        $(document).on('change', '.brand-select', function() {
            updateDisabledBrands();
        });

        $(document).on('change', '.style-select', function() {
            updateDisabledStyles();
        });

        $(document).on('input change', '.capacity-input', function() {
            updateRowNumbersAndTotals();
        });

        updateDisabledBrands();
        updateDisabledStyles();
        updateRowNumbersAndTotals();
    });
</script>
@endsection

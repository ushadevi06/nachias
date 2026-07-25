@extends('layouts.common')
@section('title', ($price ? 'Edit Item Price' : 'Add Item Price') . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $price ? 'Edit' : 'Add' }} Item Price</h4>
                    </div>
                    <form action="{{ url('item_prices/add' . ($price ? '/' . $price->id : '')) }}" method="POST" class="common-form" autocomplete="off">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="item_search" class="form-control @error('finished_item_code') is-invalid @enderror" id="item_search" placeholder="Search Item Code or Name" value="{{ old('item_search', $price->finished_item_code ?? '') }}">
                                    <input type="hidden" name="finished_item_code" id="finished_item_code" value="{{ old('finished_item_code', $price->finished_item_code ?? '') }}">
                                    <label for="item_search">Item Name <span class="text-danger">*</span></label>
                                </div>
                                @error('finished_item_code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select name="art_no" id="art_no" class="select2 form-select @error('art_no') is-invalid @enderror" data-placeholder="Select Art No" disabled>
                                        <option value="">Select Art No</option>
                                        @if($price && $price->art_no)
                                            <option value="{{ $price->art_no }}" selected>{{ $price->art_no }}</option>
                                        @endif
                                    </select>
                                    <label for="art_no">Art No</label>
                                </div>
                                @error('art_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" class="form-control @error('selling_price') is-invalid @enderror" id="selling_price" placeholder="Enter Selling Price" name="selling_price" value="{{ old('selling_price', $price->selling_price ?? '') }}">
                                    <label for="selling_price">MRP <span class="text-danger">*</span></label>
                                </div>
                                @error('selling_price')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="unit_price" placeholder="Unit Price" name="unit_price" value="{{ old('unit_price', $price->unit_price ?? '') }}" readonly tabindex="-1" style="pointer-events: none; background-color: #f0f0f0;">
                                    <label for="unit_price">Selling Price</label>
                                </div>
                            </div>

                            <input type="hidden" id="price_id_val" value="{{ $price->id ?? '' }}">

                            <div class="col-12">
                                <label class="form-label fw-bold">Select Sizes for Pricing:</label>
                                <div class="d-flex flex-wrap gap-3 mb-2">
                                     @foreach(['36', '38', '40', '42', '44', '46', '48', '50'] as $sz)
                                         <div class="form-check">
                                             <input type="checkbox" name="sizes[{{ $sz }}]" id="size_{{ $sz }}" value="{{ $sz }}" class="form-check-input size-checkbox"
                                                    {{ (old("sizes.$sz") || (isset($allPrices) && isset($allPrices[$sz]))) ? 'checked' : '' }}>
                                             <label class="form-check-label" for="size_{{ $sz }}">{{ $sz }}</label>
                                         </div>
                                     @endforeach
                                </div>
                            </div>

                            <div class="col-12 d-none" id="size-pricing-container">
                                <label class="form-label fw-bold mb-2">Size-wise Pricing Grid:</label>
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Size</th>
                                            <th>MRP <span class="text-danger">*</span></th>
                                            <th>Selling Price (Auto)</th>
                                            <th class="text-center" style="width: 80px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="size-pricing-tbody">
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control flatpickr-date @error('effective_from') is-invalid @enderror" id="effective_from" placeholder="DD-MM-YYYY" name="effective_from" value="{{ old('effective_from', $price ? $price->effective_from->format('d-m-Y') : date('d-m-Y')) }}">
                                    <label for="effective_from">Effective From <span class="text-danger">*</span></label>
                                </div>
                                @error('effective_from')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror" data-placeholder="Select Status">
                                        <option value="Active" {{ old('status', $price->status ?? 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status', $price->status ?? 'Active') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                </div>
                                @error('status')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('item_prices') }}" class="btn btn-secondary">Cancel</a>
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
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<style>
    .ui-autocomplete {
        z-index: 1050;
        max-height: 300px;
        overflow-y: auto;
        overflow-x: hidden;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .ui-menu-item {
        padding: 8px 12px;
        cursor: pointer;
        list-style: none;
    }

    .ui-menu-item:hover {
        background-color: #f8f9fa;
    }

    .ui-state-active {
        background-color: #e9ecef !important;
        border: none !important;
        color: #333 !important;
    }
</style>
<script>
    $(function() {
        $('#item_search').on('keydown', function(e) {
            if (e.keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });

        if ($(".flatpickr-date").length > 0) {
            $(".flatpickr-date").flatpickr({
                dateFormat: "d-m-Y",
                allowInput: true
            });
        }

        $('#selling_price').on('input', function() {
            let sellingPrice = parseFloat($(this).val());
            if (!isNaN(sellingPrice)) {
                let unitPrice = sellingPrice / 1.5;
                $('#unit_price').val(unitPrice.toFixed(2));
            } else {
                $('#unit_price').val('');
            }
            syncStandardSizes();
        });

        const standardSizes = ['36', '38', '40', '42', '44'];
        const specialSizes = ['46', '48', '50'];
        const dbPrices = @json($allPrices ?? (object)[]);
        const isEditMode = {{ $price ? 'true' : 'false' }};
        let editingRows = {};

        function renderGrid() {
            let hasChecked = false;
            let tbody = $('#size-pricing-tbody');
            
            let currentValues = {};
            let currentUnitPrices = {};
            let unitPriceReadonly = {};
            tbody.find('.size-price-input').each(function() {
                let sz = $(this).data('size');
                currentValues[sz] = $(this).val();
            });
            tbody.find('.size-unit-price').each(function() {
                let sz = $(this).attr('id').replace('unit_price_', '');
                currentUnitPrices[sz] = $(this).val();
                unitPriceReadonly[sz] = $(this).prop('readonly');
            });

            tbody.empty();

            $('.size-checkbox:checked').each(function() {
                hasChecked = true;
                let sz = $(this).val();
                
                let val = '';
                if (currentValues[sz] !== undefined) {
                    val = currentValues[sz];
                } else if (dbPrices && dbPrices[sz] !== undefined) {
                    val = dbPrices[sz].selling_price;
                } else if (standardSizes.includes(sz)) {
                    val = $('#selling_price').val();
                }

                let unitVal = '';
                let isReadonly = true;
                if (currentUnitPrices[sz] !== undefined) {
                    unitVal = currentUnitPrices[sz];
                    isReadonly = unitPriceReadonly[sz];
                } else if (dbPrices && dbPrices[sz] !== undefined && dbPrices[sz].unit_price !== undefined) {
                    unitVal = dbPrices[sz].unit_price;
                } else if (val) {
                    let baseUnit = $('#unit_price').val();
                    unitVal = baseUnit ? baseUnit : (parseFloat(val) / 1.5).toFixed(2);
                }

                let actionTd = `
                    <div class="text-center d-flex justify-content-center gap-1">
                        <button type="button" class="btn btn-sm btn-outline-primary edit-row-btn" data-size="${sz}" title="Edit Unit Price for size ${sz}">
                            <i class="ri ri-edit-box-line me-1"></i> Edit
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-size-row" data-size="${sz}" title="Remove size ${sz}">
                            <i class="ri ri-delete-bin-line"></i>
                        </button>
                    </div>
                `;

                tbody.append(`
                    <tr id="row_size_${sz}">
                        <td><strong>Size ${sz}</strong></td>
                        <td>
                            <input type="number" step="0.01" name="size_prices[${sz}][selling_price]" class="form-control size-price-input" data-size="${sz}" value="${val}" placeholder="Enter Price for Size ${sz}" required>
                        </td>
                        <td>
                            <input type="number" step="0.01" name="size_prices[${sz}][unit_price]" class="form-control size-unit-price" id="unit_price_${sz}" value="${unitVal}" placeholder="Enter Unit Price" required ${isReadonly ? 'readonly tabindex="-1" style="background-color: #f0f0f0;"' : 'style="background-color: #fff;"'}>
                        </td>
                        <td>
                            ${actionTd}
                        </td>
                    </tr>
                `);
            });

            if (hasChecked) {
                $('#size-pricing-container').removeClass('d-none');
            } else {
                $('#size-pricing-container').addClass('d-none');
            }
        }

        $(document).on('click', '.remove-size-row', function() {
            let sz = $(this).data('size');
            $(`#size_${sz}`).prop('checked', false).trigger('change');
        });

        function syncStandardSizes() {
            let basePrice = $('#selling_price').val();
            $('.size-price-input').each(function() {
                let sz = $(this).data('size');
                if (standardSizes.includes(sz.toString())) {
                    $(this).val(basePrice);
                    let unitInput = $(`#unit_price_${sz}`);
                    if (unitInput.prop('readonly')) {
                        if (basePrice) {
                            let unitPrice = (parseFloat(basePrice) / 1.5).toFixed(2);
                            unitInput.val(unitPrice);
                        } else {
                            unitInput.val('');
                        }
                    }
                }
            });
        }

        $('.size-checkbox').on('change', function() {
            renderGrid();
        });

        $(document).on('input', '.size-price-input', function() {
            let sz = $(this).data('size');
            let unitInput = $(`#unit_price_${sz}`);
            if (unitInput.prop('readonly')) {
                let val = parseFloat($(this).val());
                if (!isNaN(val)) {
                    unitInput.val((val / 1.5).toFixed(2));
                } else {
                    unitInput.val('');
                }
            }
        });

        $(document).on('click', '.edit-row-btn', function() {
            let sz = $(this).data('size');
            let row = $(`#row_size_${sz}`);
            let unitInput = row.find('.size-unit-price');
            unitInput.prop('readonly', false).removeAttr('tabindex').css('background-color', '#fff').focus();
        });

        var itemAutocomplete = $('#item_search').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{ url('item_prices/search_items') }}",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        if (data.length === 0) {
                            response([{ label: 'No records found', value: '', id: '', no_record: true }]);
                        } else {
                            response($.map(data, function(item) {
                                return {
                                    label: item.text,
                                    value: item.text,
                                    id: item.id,
                                    code: item.code,
                                    name: item.name
                                };
                            }));
                        }
                    }
                });
            },
            select: function(event, ui) {
                if (ui.item.no_record) {
                    event.preventDefault();
                    $('#item_search').val('');
                    return false;
                }
                $('#item_id').val(ui.item.id);
                $('#finished_item_code').val(ui.item.code);
                $('#item_search').val(ui.item.code);

                loadArtNos(ui.item.code);
                return false;
            },
            minLength: 1
        });

        if (itemAutocomplete.data("ui-autocomplete")) {
            itemAutocomplete.data("ui-autocomplete")._renderItem = function(ul, item) {
                if (item.no_record) {
                    return $("<li>")
                        .append(`
                            <div class="p-2 text-danger" style="font-size: 13px;">${item.label}</div>
                        `).appendTo(ul);
                }
                let displayName = item.name ? item.name : item.code;
                return $("<li>")
                    .append(`
                        <div class="d-flex justify-content-between align-items-center p-2 border-bottom autocomplete-custom-item" style="cursor: pointer;">
                            <div>
                                <div class="fw-bold text-primary" style="font-size: 13px;">${displayName}</div>
                            </div>
                        </div>
                    `).appendTo(ul);
            };
        }

        $('#item_search').on('input blur', function(e) {
            if ($(this).val() === "" || (e.type === 'blur' && $('#finished_item_code').val() === "")) {
                if (e.type === 'blur') {
                    $(this).val("");
                }
                $('#item_id').val("");
                $('#finished_item_code').val("");
                $('#art_no').empty().append('<option value="">Select Art No</option>').prop('disabled', true);
            }
        });

        function loadArtNos(itemCode) {
            let currentArtNo = "{{ old('art_no', $price->art_no ?? '') }}";
            let artNoSelect = $('#art_no');

            if (itemCode) {
                artNoSelect.prop('disabled', true);
                $.ajax({
                    url: "{{ url('item_prices/get_art_nos') }}",
                    type: "GET",
                    data: {
                        item_code: itemCode
                    },
                    success: function(response) {
                        artNoSelect.empty();
                        artNoSelect.append('<option value="">Select Art No</option>');

                        if (currentArtNo) {
                            artNoSelect.append('<option value="' + currentArtNo + '" selected>' + currentArtNo + '</option>');
                        }

                        if (response.art_nos && response.art_nos.length > 0) {
                            response.art_nos.forEach(function(artNo) {
                                if (artNo != currentArtNo) {
                                    artNoSelect.append('<option value="' + artNo + '">' + artNo + '</option>');
                                }
                            });
                        }
                        artNoSelect.prop('disabled', false).trigger('change');
                    }
                });
            } else {
                artNoSelect.empty().append('<option value="">Select Art No</option>').prop('disabled', true).trigger('change');
            }
        }
        if ($('#finished_item_code').val()) {
            loadArtNos($('#finished_item_code').val());
        }
        if (!isEditMode && $('#selling_price').val()) {
            $('#selling_price').trigger('input');
        }
        renderGrid();
    });
</script>
@endsection

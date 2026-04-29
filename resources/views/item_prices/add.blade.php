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
                                    <input type="text" name="item_search" class="form-control @error('finished_item_code') is-invalid @enderror" id="item_search" placeholder="Search Item Code or Name" value="{{ old('item_search', ($price && $price->finished_item_code) ? $price->finished_item_code . ' - ' . ($price->item->name ?? '') : '') }}">
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
                                    <label for="selling_price">Selling Price <span class="text-danger">*</span></label>
                                </div>
                                @error('selling_price')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="unit_price" placeholder="Unit Price" name="unit_price" value="{{ old('unit_price', $price->unit_price ?? '') }}" readonly tabindex="-1" style="pointer-events: none; background-color: #f0f0f0;">
                                    <label for="unit_price">Unit Price</label>
                                </div>
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
                });
            },
            select: function(event, ui) {
                $('#item_id').val(ui.item.id);
                $('#finished_item_code').val(ui.item.code);
                $('#item_search').val(ui.item.label);

                loadArtNos(ui.item.code);
                return false;
            },
            minLength: 1
        });

        if (itemAutocomplete.data("ui-autocomplete")) {
            itemAutocomplete.data("ui-autocomplete")._renderItem = function(ul, item) {
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
        $('#item_search').on('input', function() {
            if ($(this).val() === "") {
                $('#item_id').val("");
                $('#finished_item_code').val("");
                $('#art_no').empty().append('<option value="">Select Art No</option>').prop('disabled', true);
            }
        });

        function loadArtNos(itemCode) {
            let currentArtNo = "{{ $price->art_no ?? '' }}";
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

                        if (response.art_nos && response.art_nos.length > 0) {
                            response.art_nos.forEach(function(artNo) {
                                let selected = (artNo == currentArtNo) ? 'selected' : '';
                                artNoSelect.append('<option value="' + artNo + '" ' + selected + '>' + artNo + '</option>');
                            });
                            // If only one Art No and nothing selected, select it automatically
                            if (response.art_nos.length === 1 && !artNoSelect.val()) {
                                artNoSelect.val(response.art_nos[0]).trigger('change');
                            }
                        }
                        artNoSelect.prop('disabled', false);
                    }
                });
            } else {
                artNoSelect.empty().append('<option value="">Select Art No</option>').prop('disabled', true);
            }
        }

        // Trigger on load if editing
        if ($('#finished_item_code').val()) {
            loadArtNos($('#finished_item_code').val());
        }
        if ($('#selling_price').val()) {
            $('#selling_price').trigger('input');
        }
    });
</script>
@endsection

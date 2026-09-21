@extends('layouts.common')
@section('title', ($style ? 'Edit Style' : 'Add Style') . ' - ' . env('WEBSITE_NAME'))
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
                        <h4>{{ $style ? 'Edit' : 'Add' }} Style</h4>
                    </div>
                    <form action="{{ url('styles/add' . ($style ? '/' . $style->id : '')) }}"
                        method="POST" class="common-form" autocomplete="off">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('style_name') is-invalid @enderror" id="style_name"
                                        placeholder="Enter Style Name" name="style_name"
                                        value="{{ old('style_name', $style->style_name ?? '') }}">
                                    <label for="style_name">Style Name <span class="text-danger">*</span></label>
                                </div>
                                @error('style_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code"
                                        placeholder="Enter Style Code" name="code"
                                        value="{{ old('code', $style->code ?? '') }}">
                                    <label for="code">Style Code <span class="text-danger">*</span></label>
                                </div>
                                @error('code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mt-3">
                                <div class="card border shadow-none">
                                    <div class="card-header d-flex justify-content-between align-items-center py-2 bg-light">
                                        <h5 class="mb-0 fw-semibold text-dark">Brand Wise Average Consumption</h5>
                                        <button type="button" class="btn btn-sm btn-primary" id="add_brand_row">
                                            <i class="ri ri-add-line me-1"></i> Add Brand
                                        </button>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-bordered align-middle mb-0" id="brand_consumption_table">
                                                <thead>
                                                    <tr class="bg-light">
                                                        <th>Brand <span class="text-danger">*</span></th>
                                                        <th>Average Quantity / Consumption <span class="text-danger">*</span></th>
                                                        <th style="width: 80px;" class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="brand_consumption_tbody">
                                                    @php
                                                        $existingConsumptions = old('brands');
                                                        if (is_null($existingConsumptions) && isset($style) && $style->brandConsumptions->isNotEmpty()) {
                                                            $existingConsumptions = [];
                                                            foreach ($style->brandConsumptions as $bc) {
                                                                $existingConsumptions[] = [
                                                                    'brand_id' => $bc->brand_id,
                                                                    'average_consumption' => $bc->average_consumption,
                                                                ];
                                                            }
                                                        }
                                                        if (empty($existingConsumptions)) {
                                                            $existingConsumptions = [
                                                                ['brand_id' => '', 'average_consumption' => $style->average_consumption ?? '']
                                                            ];
                                                        }
                                                    @endphp

                                                    @foreach($existingConsumptions as $idx => $bCons)
                                                        <tr class="brand-row">
                                                            <td>
                                                                <select name="brands[{{ $idx }}][brand_id]" class="form-select select2-brand @error('brands.'.$idx.'.brand_id') is-invalid @enderror" data-placeholder="Select Brand">
                                                                    <option></option>
                                                                    @foreach($brands as $b)
                                                                        <option value="{{ $b->id }}" {{ (isset($bCons['brand_id']) && $bCons['brand_id'] == $b->id) ? 'selected' : '' }}>
                                                                            {{ $b->brand_name }}{{ $b->code ? ' ('.$b->code.')' : '' }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                @error('brands.'.$idx.'.brand_id')
                                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                                @enderror
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" min="1" name="brands[{{ $idx }}][average_consumption]" class="form-control @error('brands.'.$idx.'.average_consumption') is-invalid @enderror" placeholder="Enter Average Quantity" value="{{ $bCons['average_consumption'] ?? '' }}">
                                                                @error('brands.'.$idx.'.average_consumption')
                                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                                @enderror
                                                            </td>
                                                            <td class="text-center">
                                                                <button type="button" class="btn btn-sm btn-outline-danger remove-brand-row">
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
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror"
                                        data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active"
                                            {{ old('status', $style->status ?? '') == 'Active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="Inactive"
                                            {{ old('status', $style->status ?? '') == 'Inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                </div>
                                @error('status')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                           <div class="col-lg-12 text-end mt-4">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('styles') }}" class="btn btn-secondary">Cancel</a>
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
    $(document).ready(function () {
        let brandOptionsHtml = `<option></option>`;
        @foreach($brands as $b)
            brandOptionsHtml += `<option value="{{ $b->id }}">{{ addslashes($b->brand_name) }}{{ $b->code ? ' ('.$b->code.')' : '' }}</option>`;
        @endforeach

        let rowIndex = $('#brand_consumption_tbody tr').length;
        let totalBrands = {{ count($brands) }};

        function initSelect2($element) {
            if ($.fn.select2) {
                $element.select2({
                    placeholder: "Select Brand",
                    dropdownParent: $('#brand_consumption_table')
                });
            }
        }

        function updateBrandOptions() {
            let selectedBrands = [];
            $('.select2-brand').each(function () {
                let val = $(this).val();
                if (val) {
                    selectedBrands.push(val.toString());
                }
            });

            $('.select2-brand').each(function () {
                let $select = $(this);
                let currentVal = $select.val() ? $select.val().toString() : '';

                $select.find('option').each(function () {
                    let optVal = $(this).val() ? $(this).val().toString() : '';
                    if (optVal !== '') {
                        if (selectedBrands.includes(optVal) && optVal !== currentVal) {
                            $(this).prop('disabled', true).prop('hidden', true).addClass('d-none');
                        } else {
                            $(this).prop('disabled', false).prop('hidden', false).removeClass('d-none');
                        }
                    }
                });

                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }
                $select.select2({
                    placeholder: "Select Brand",
                    dropdownParent: $('#brand_consumption_table')
                });
            });

            let count = $('#brand_consumption_tbody tr').length;
            if (count >= totalBrands) {
                $('#add_brand_row').prop('disabled', true);
            } else {
                $('#add_brand_row').prop('disabled', false);
            }
        }

        function addBrandRow(brandId = '', avgCons = '') {
            let html = `
                <tr class="brand-row">
                    <td>
                        <select name="brands[${rowIndex}][brand_id]" class="form-select select2-brand">
                            ${brandOptionsHtml}
                        </select>
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0" name="brands[${rowIndex}][average_consumption]" class="form-control" placeholder="Enter Average Quantity" value="${avgCons}">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-brand-row">
                            <i class="ri ri-delete-bin-line"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#brand_consumption_tbody').append(html);
            let $newSelect = $('#brand_consumption_tbody tr:last .select2-brand');
            if (brandId) {
                $newSelect.val(brandId);
            }
            initSelect2($newSelect);
            rowIndex++;
            updateRemoveButtons();
            updateBrandOptions();
        }

        $(document).on('click', '#add_brand_row', function() {
            addBrandRow();
        });

        $(document).on('click', '.remove-brand-row', function() {
            $(this).closest('tr').remove();
            updateRemoveButtons();
            updateBrandOptions();
        });

        $(document).on('change', '.select2-brand', function() {
            updateBrandOptions();
        });

        function updateRemoveButtons() {
            let count = $('#brand_consumption_tbody tr').length;
            if (count <= 1) {
                $('.remove-brand-row').prop('disabled', true);
            } else {
                $('.remove-brand-row').prop('disabled', false);
            }
        }

        $('.select2-brand').each(function() {
            initSelect2($(this));
        });

        updateRemoveButtons();
        updateBrandOptions();
    });
</script>
@endsection


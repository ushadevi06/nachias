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
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h5 class="fw-bold mb-0">Brand Storage Capacities</h5>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddBrandRow">
                                        <i class="ri ri-add-line me-1"></i> Add More Brand
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle" id="brandCapacityTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 5%;" class="text-center">#</th>
                                                <th style="width: 55%;">Brand Name <span class="text-danger">*</span></th>
                                                <th style="width: 30%;">Capacity (Pcs) <span class="text-danger">*</span></th>
                                                <th style="width: 10%;" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="brandCapacityRows">
                                            @php
                                                $rows = old('brand_ids', []);
                                                if (empty($rows) && !empty($existingCapacities)) {
                                                    $rows = $existingCapacities;
                                                }
                                            @endphp

                                            @if($errors->has('brand_ids'))
                                                <div class="text-danger mb-2 small fw-bold">{{ $errors->first('brand_ids') }}</div>
                                            @endif

                                            @if(!empty($rows) && count($rows) > 0)
                                                @foreach($rows as $idx => $rData)
                                                    @php
                                                        $bId = is_array($rData) ? ($rData['brand_id'] ?? '') : $rData;
                                                        $cVal = is_array($rData) ? ($rData['capacity_pcs'] ?? 0) : (old('capacities')[$idx] ?? 0);
                                                    @endphp
                                                    <tr class="capacity-row">
                                                        <td class="text-center row-num">{{ $loop->iteration }}</td>
                                                        <td>
                                                            <select name="brand_ids[]" class="form-select brand-select select2 @error('brand_ids.'.$idx) is-invalid @enderror" data-placeholder="Select Brand">
                                                                <option value="">Select Brand</option>
                                                                @foreach($brands as $b)
                                                                    <option value="{{ $b->id }}" {{ $bId == $b->id ? 'selected' : '' }}>{{ $b->brand_name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('brand_ids.'.$idx)
                                                                <div class="text-danger mt-1 small">{{ $message }}</div>
                                                            @enderror
                                                        </td>
                                                        <td>
                                                            <input type="number" min="0" step="1" name="capacities[]" class="form-control capacity-input @error('capacities.'.$idx) is-invalid @enderror" value="{{ $cVal }}" placeholder="Enter capacity in Pcs">
                                                            @error('capacities.'.$idx)
                                                                <div class="text-danger mt-1 small">{{ $message }}</div>
                                                            @enderror
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" title="Remove Row">
                                                                <i class="ri ri-delete-bin-line"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr class="capacity-row">
                                                    <td class="text-center row-num">1</td>
                                                    <td>
                                                        <select name="brand_ids[]" class="form-select brand-select select2 @error('brand_ids.0') is-invalid @enderror" data-placeholder="Select Brand">
                                                            <option value="">Select Brand</option>
                                                            @foreach($brands as $b)
                                                                <option value="{{ $b->id }}">{{ $b->brand_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('brand_ids.0')
                                                            <div class="text-danger mt-1 small">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <input type="number" min="0" step="1" name="capacities[]" class="form-control capacity-input @error('capacities.0') is-invalid @enderror" value="0" placeholder="Enter capacity in Pcs">
                                                        @error('capacities.0')
                                                            <div class="text-danger mt-1 small">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" title="Remove Row">
                                                            <i class="ri ri-delete-bin-line"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-light fw-bold">
                                                <td colspan="2" class="text-end">Total Capacity:</td>
                                                <td colspan="2"><span id="lblTotalCapacity" class="text-primary fs-6">0 Pcs</span></td>
                                            </tr>
                                        </tfoot>
                                    </table>
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

        function initSelect2(element) {
            if ($.fn.select2) {
                $(element).select2({
                    placeholder: 'Select Brand',
                    allowClear: true
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
                
                // Re-initialize or trigger update on select2
                if ($(this).data('select2')) {
                    $(this).trigger('change.select2');
                }
            });
        }

        function updateRowNumbersAndTotals() {
            let totalCap = 0;
            $('#brandCapacityRows tr.capacity-row').each(function(idx) {
                $(this).find('.row-num').text(idx + 1);
                let capVal = parseInt($(this).find('.capacity-input').val()) || 0;
                totalCap += capVal;
            });
            $('#lblTotalCapacity').text(totalCap.toLocaleString('en-IN') + ' Pcs');
        }

        // Initialize select2 on existing brand rows
        $('.brand-select').each(function() {
            initSelect2(this);
        });

        $('#btnAddBrandRow').on('click', function() {
            let newRowHtml = `
                <tr class="capacity-row">
                    <td class="text-center row-num"></td>
                    <td>
                        <select name="brand_ids[]" class="form-select brand-select" data-placeholder="Select Brand">
                            ${brandOptionsHtml}
                        </select>
                    </td>
                    <td>
                        <input type="number" min="0" step="1" name="capacities[]" class="form-control capacity-input" value="0" placeholder="Enter capacity in Pcs">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" title="Remove Row">
                            <i class="ri ri-delete-bin-line"></i>
                        </button>
                    </td>
                </tr>
            `;
            let $newRow = $(newRowHtml);
            $('#brandCapacityRows').append($newRow);
            
            let $newSelect = $newRow.find('.brand-select');
            initSelect2($newSelect);
            updateDisabledBrands();
            updateRowNumbersAndTotals();
        });

        $(document).on('change', '.brand-select', function() {
            updateDisabledBrands();
        });

        $(document).on('click', '.btn-remove-row', function() {
            if ($('#brandCapacityRows tr.capacity-row').length > 1) {
                let $row = $(this).closest('tr');
                let $select = $row.find('.brand-select');
                if ($select.data('select2')) {
                    $select.select2('destroy');
                }
                $row.remove();
                updateDisabledBrands();
                updateRowNumbersAndTotals();
            } else {
                alert('At least one brand capacity row is required.');
            }
        });

        $(document).on('input change', '.capacity-input', function() {
            updateRowNumbersAndTotals();
        });

        updateDisabledBrands();
        updateRowNumbersAndTotals();
    });
</script>
@endsection

@extends('layouts.common')
@section('title', ($operationStage ? 'Edit Operation Stage' : 'Add Operation Stage') . ' - ' . env('WEBSITE_NAME'))
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
                        <h4>{{ $operationStage ? 'Edit' : 'Add' }} Operation Stage</h4>
                    </div>
                    <form action="{{ url('operation_stages/add/' . ($operationStage ?  $operationStage->id : '')) }}" method="POST" class="common-form" autocomplete="off" id="operationStageForm">
                        @csrf
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6 col-xl-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('operation_stage_name') is-invalid @enderror" id="operation-stage-name" placeholder="Enter Operation Stage Name" name="operation_stage_name" value="{{ old('operation_stage_name', $operationStage->operation_stage_name ?? '') }}">
                                    <label for="operation-stage-name">Operation Stage Name <span class="text-danger">*</span></label>
                                </div>
                                @error('operation_stage_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" min="0" class="form-control @error('working_days') is-invalid @enderror" id="working-days" placeholder="Enter Working Days" name="working_days" value="{{ old('working_days', $operationStage->working_days ?? 0) }}">
                                    <label for="working-days">Working Days (Default Deadline Duration)</label>
                                </div>
                                @error('working_days')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" min="0" class="form-control @error('cost') is-invalid @enderror" id="cost" placeholder="Enter Cost" name="cost" value="{{ old('cost', $operationStage->cost ?? 0) }}">
                                    <label for="cost">Cost</label>
                                </div>
                                @error('cost')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" min="0" class="form-control @error('target') is-invalid @enderror" id="target" placeholder="Enter Default Target" name="target" value="{{ old('target', $operationStage->target ?? '') }}">
                                    <label for="target">Default Target Qty (PCS)</label>
                                </div>
                                @error('target')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active" {{ old('status', $operationStage->status ?? 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status', $operationStage->status ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                </div>
                                @error('status')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Unit Wise Target Quantities -->
                            <div class="col-xl-12">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div>
                                        <h5 class="fw-bold mb-0 text-dark">Unit Wise Target Quantities</h5>
                                        <small class="text-muted">Configure daily target quantity for each unit</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddUnitRow">
                                        <i class="ri ri-add-line me-1"></i> Add More Unit
                                    </button>
                                </div>

                                @php
                                    $targetsList = old('unit_targets', []);
                                    if (empty($targetsList) && isset($stageTargets) && count($stageTargets) > 0) {
                                        $targetsList = [];
                                        foreach ($stageTargets as $st) {
                                            $targetsList[] = [
                                                'service_provider_id' => $st->service_provider_id,
                                                'target_qty' => $st->target_qty
                                            ];
                                        }
                                    }
                                    if (empty($targetsList)) {
                                        $targetsList = [
                                            ['service_provider_id' => '', 'target_qty' => '']
                                        ];
                                    }
                                @endphp

                                <div class="card border shadow-none mb-3">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm align-middle mb-0" id="unitTargetsTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 6%;" class="text-center">#</th>
                                                    <th style="width: 54%;">Unit / Service Provider <span class="text-danger">*</span></th>
                                                    <th style="width: 30%;">Target Qty (PCS) <span class="text-danger">*</span></th>
                                                    <th style="width: 10%;" class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="unitTargetsContainer">
                                                <tr id="emptyUnitRow" style="{{ count($targetsList) > 0 ? 'display: none;' : '' }}">
                                                    <td colspan="4" class="text-center text-muted py-3">No unit targets added. Click <strong>+ Add Unit</strong> to configure.</td>
                                                </tr>
                                                @foreach($targetsList as $tIdx => $tRow)
                                                    <tr class="unit-target-row">
                                                        <td class="text-center row-num fw-semibold">{{ $loop->iteration }}</td>
                                                        <td>
                                                            <select name="unit_targets[{{ $tIdx }}][service_provider_id]" class="form-select unit-select select2 @error('unit_targets.'.$tIdx.'.service_provider_id') is-invalid @enderror" data-placeholder="Select Unit">
                                                                <option value="">Select Unit</option>
                                                                @foreach($serviceProviders as $sp)
                                                                    <option value="{{ $sp->id }}" {{ ($tRow['service_provider_id'] ?? '') == $sp->id ? 'selected' : '' }}>
                                                                        {{ $sp->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('unit_targets.'.$tIdx.'.service_provider_id')
                                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </td>
                                                        <td>
                                                            <input type="number" min="0" step="1" name="unit_targets[{{ $tIdx }}][target_qty]" class="form-control target-input @error('unit_targets.'.$tIdx.'.target_qty') is-invalid @enderror" value="{{ $tRow['target_qty'] ?? '' }}" placeholder="Enter target in Pcs">
                                                            @error('unit_targets.'.$tIdx.'.target_qty')
                                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                            @enderror
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-unit" title="Delete Unit">
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

                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('operation_stages') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.select2-container.is-invalid .select2-selection,
.is-invalid + .select2-container .select2-selection {
    border-color: #ff3e1d !important;
}
</style>
@endsection

@section('scripts')
<script>
    $(function() {
        const unitOptionsHtml = `
            <option value="">Select Unit</option>
            @foreach($serviceProviders as $sp)
                <option value="{{ $sp->id }}">{{ addslashes($sp->name) }}</option>
            @endforeach
        `;

        function initSelect2(element, placeholderText) {
            if ($.fn.select2) {
                $(element).select2({
                    placeholder: placeholderText || 'Select Unit',
                    allowClear: true,
                    width: '100%'
                });
            }
        }

        function updateDisabledUnits() {
            let selectedUnits = [];
            $('.unit-select').each(function() {
                let val = $(this).val();
                if (val) {
                    selectedUnits.push(val.toString());
                }
            });

            $('.unit-select').each(function() {
                let currentVal = $(this).val() ? $(this).val().toString() : '';
                $(this).find('option').each(function() {
                    let optVal = $(this).val() ? $(this).val().toString() : '';
                    if (optVal && optVal !== currentVal && selectedUnits.includes(optVal)) {
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

        function renumberRows() {
            $('#unitTargetsContainer tr.unit-target-row').each(function(index) {
                $(this).find('.row-num').text(index + 1);
                $(this).find('.unit-select').attr('name', `unit_targets[${index}][service_provider_id]`);
                $(this).find('.target-input').attr('name', `unit_targets[${index}][target_qty]`);
            });
        }

        function addUnitRow() {
            $('#emptyUnitRow').hide();
            let rowIndex = $('#unitTargetsContainer tr.unit-target-row').length;
            let newRowHtml = `
                <tr class="unit-target-row">
                    <td class="text-center row-num fw-semibold">${rowIndex + 1}</td>
                    <td>
                        <select name="unit_targets[${rowIndex}][service_provider_id]" class="form-select unit-select select2" data-placeholder="Select Unit">
                            ${unitOptionsHtml}
                        </select>
                    </td>
                    <td>
                        <input type="number" min="0" step="1" name="unit_targets[${rowIndex}][target_qty]" class="form-control target-input" placeholder="Enter target in Pcs">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-unit" title="Delete Unit">
                            <i class="ri ri-delete-bin-line"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#unitTargetsContainer').append(newRowHtml);
            let $newSelect = $('#unitTargetsContainer tr.unit-target-row:last .unit-select');
            initSelect2($newSelect, 'Select Unit');
            renumberRows();
            updateDisabledUnits();
        }

        $('#btnAddUnitRow, #btnAddUnitRowBottom').on('click', function(e) {
            e.preventDefault();
            addUnitRow();
        });

        $(document).on('click', '.btn-remove-unit', function(e) {
            e.preventDefault();
            let $row = $(this).closest('tr');
            let $select = $row.find('.unit-select');
            if ($select.data('select2')) {
                $select.select2('destroy');
            }
            $row.remove();

            if ($('#unitTargetsContainer tr.unit-target-row').length === 0) {
                $('#emptyUnitRow').show();
            }
            renumberRows();
            updateDisabledUnits();
        });

        $(document).on('change', '.unit-select', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.select2-container').removeClass('is-invalid');
            $(this).closest('td').find('.text-danger').remove();
            updateDisabledUnits();
        });

        $(document).on('input', '.target-input', function() {
            $(this).removeClass('is-invalid');
            $(this).closest('td').find('.text-danger').remove();
        });

        // Initialize Select2 on existing rows
        $('.unit-select').each(function() {
            initSelect2($(this), 'Select Unit');
        });
        updateDisabledUnits();

        // Client-side validation on form submit
        $('#operationStageForm').on('submit', function(e) {
            let isValid = true;
            $('.unit-target-row').each(function() {
                let $row = $(this);
                let $select = $row.find('.unit-select');
                let $input = $row.find('.target-input');
                let unitVal = $select.val();
                let targetVal = $input.val();

                // Clean previous client errors
                $row.find('.client-error-msg').remove();
                $select.removeClass('is-invalid');
                $select.next('.select2-container').removeClass('is-invalid');
                $input.removeClass('is-invalid');

                if (!unitVal) {
                    isValid = false;
                    $select.addClass('is-invalid');
                    $select.next('.select2-container').addClass('is-invalid');
                    if ($select.closest('td').find('.text-danger').length === 0) {
                        $select.closest('td').append('<div class="text-danger small mt-1 client-error-msg">Unit / Service Provider is required.</div>');
                    }
                }

                if (targetVal === '' || targetVal === null || isNaN(targetVal) || parseFloat(targetVal) < 0) {
                    isValid = false;
                    $input.addClass('is-invalid');
                    if ($input.closest('td').find('.text-danger').length === 0) {
                        $input.closest('td').append('<div class="text-danger small mt-1 client-error-msg">Target quantity is required.</div>');
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
@endsection
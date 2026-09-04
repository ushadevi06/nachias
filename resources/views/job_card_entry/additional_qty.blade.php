@extends('layouts.common')
@section('title', 'Additional Quantity - Job Card ' . $jobCard->job_card_no . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    @php
        $isCanvas = false;
        if ($jobCard->brand && in_array(strtoupper(trim($jobCard->brand->brand_name)), ['CANVAS ACCESSORIES', 'CANVAS ACCESSORIES (CAS)'])) {
            $isCanvas = true;
        }

        $allSizes = $sizes ?? ['36', '38', '40', '42', '44', '46'];
        $firstFabric = $jobCard->fabricDetails->first();
        $artNoVal = $firstFabric->art_no ?? ($jobCard->reference_no ?? '');
        $widthVal = $firstFabric->width ?? ($jobCard->width ?? '');
    @endphp

    <!-- Page Header & Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="mb-1 text-primary d-flex align-items-center gap-2">
                <i class="ri ri-add-circle-line"></i> Job Card Additional Quantity
            </h4>
            <div class="text-muted small">
                Job Card Number: <strong class="text-dark">{{ $jobCard->job_card_no }}</strong> &nbsp;|&nbsp; 
                Date: {{ $jobCard->job_card_date ? date('d-m-Y', strtotime($jobCard->job_card_date)) : '-' }} &nbsp;|&nbsp; 
                Plant: {{ $jobCard->serviceProvider->name ?? '-' }}
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('job_card_entries.view-item', $jobCard->id) }}" class="btn btn-outline-primary d-flex align-items-center gap-1">
                <i class="ri ri-list-check-2"></i> Issue Item
            </a>
            <a href="{{ url('job_card_entries/view/' . $jobCard->id) }}" class="btn btn-outline-secondary d-flex align-items-center gap-1">
                <i class="ri ri-eye-line"></i> View Job Card
            </a>
            <a href="{{ url('job_card_entries') }}" class="btn btn-secondary d-flex align-items-center gap-1">
                <i class="ri ri-arrow-left-line"></i> Back to List
            </a>
        </div>
    </div>

    @include('flash_messages')

    <!-- Common Total Summary Bar -->
    <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #f8f9fa 0%, #edf2f7 100%);">
        <div class="card-body py-3">
            <div class="row text-center align-items-center">
                <div class="col-md-4 border-end">
                    <span class="text-muted small d-block mb-1 fw-semibold text-uppercase">Planned Quantity</span>
                    <h3 class="mb-0 fw-bold text-dark">{{ number_format($jobCard->grand_total_qty, 0) }} <small class="fs-6 text-muted">pcs</small></h3>
                </div>
                <div class="col-md-4 border-end">
                    <span class="text-warning-emphasis small d-block mb-1 fw-bold text-uppercase">Additional Quantity</span>
                    <h3 class="mb-0 fw-bold text-warning" id="lblSummaryExtraQty">+0 <small class="fs-6 text-muted">pcs</small></h3>
                </div>
                <div class="col-md-4">
                    <span class="text-success small d-block mb-1 fw-bold text-uppercase">Common Total Quantity</span>
                    <h3 class="mb-0 fw-bold text-success" id="lblSummaryCommonTotal">{{ number_format($jobCard->grand_total_qty, 0) }} <small class="fs-6 text-muted">pcs</small></h3>
                </div>
            </div>
        </div>
    </div>

    <form id="formAdditionalQty" method="POST" action="{{ route('job_card_entries.add_additional_qty', $jobCard->id) }}" enctype="multipart/form-data" novalidate>
        @csrf

        <!-- 1. Fabric Details Section (Matches Image 2) -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">
                <div class="card-header-box mb-3 border-bottom pb-2">
                    <h4 class="mb-0">Fabric Details</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle" id="fabric-details-table">
                        <thead>
                            <tr>
                                <th colspan="2" class="bg-light p-3">
                                    <label class="small text-primary fw-bold text-uppercase d-block mb-2">IMAGE</label>
                                    <div class="d-flex align-items-center justify-content-center gap-3">
                                        <input type="file" class="form-control form-control-sm" name="fabric_image" accept="image/*" style="max-width: 300px;">
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold" style="width: 20%;">ART NO</td>
                                <td class="p-2">
                                    <input type="text" name="art_no" id="fabric_art_no" class="form-control form-control-sm text-center fw-bold" value="{{ $artNoVal }}">
                                    <input type="hidden" name="stock_entry_id" value="{{ $firstFabric->stock_entry_id ?? '' }}">
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">WIDTH</td>
                                <td class="p-2">
                                    <select name="width" id="fabric_width" class="form-select form-select-sm text-center">
                                        <option value="">Select Width</option>
                                        @foreach($fabricSizes as $fs)
                                            <option value="{{ $fs->id }}" {{ ((string)$widthVal === (string)$fs->id || (string)$widthVal === (string)$fs->width) ? 'selected' : '' }}>
                                                {{ $fs->width }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">ISSUED METERS</td>
                                <td class="p-2">
                                    <input type="number" step="0.01" min="0.01" name="total_fabric_meters" id="issued_meters_input" class="form-control form-control-sm text-center fw-bold" value="0.00" readonly>
                                    <div class="small text-muted mt-1" id="matrix_need_caption">Matrix Need: 0.00 MTR</div>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">IN/OUT</td>
                                <td class="p-2">
                                    <input type="text" name="in_out" class="form-control form-control-sm text-center" value="NO">
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">N.PATTI</td>
                                <td class="p-2">
                                    <input type="text" name="n_patti" class="form-control form-control-sm text-center" value="WHITE">
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-light" style="vertical-align: middle;">
                                    CONSUMPTION<br>
                                    <span class="badge bg-secondary">MTR</span>
                                </td>
                                <td class="p-0">
                                    <table class="table table-bordered table-sm mb-0 align-middle text-center" id="consumption-lay-table">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 8%;">MARK</th>
                                                <th style="width: 35%;">SIZE</th>
                                                <th style="width: 15%;">SLEEVE</th>
                                                <th style="width: 18%;">LAY MARK METER</th>
                                                <th style="width: 16%;">NO.OF LAY</th>
                                                <th style="width: 8%;"><i class="ri-settings-4-line"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody id="consumption-lay-tbody">
                                            <tr class="lay-row">
                                                <td class="fw-bold mark-number">1</td>
                                                <td>
                                                    <select class="form-select form-select-sm select2-multi-sizes select-lay-sizes" multiple="multiple" style="width: 100%;">
                                                        @foreach($allSizes as $sz)
                                                            <option value="{{ $sz }}">{{ $sz }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm select-lay-sleeve select2" name="lay_sleeve" data-placeholder="Select Sleeve">
                                                        <option value="F/S">F/S</option>
                                                        <option value="H/S">H/S</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm text-center input-lay-meter" name="lay_meter" placeholder="0.00" value="">
                                                </td>
                                                <td>
                                                    <input type="number" step="1" min="0" class="form-control form-control-sm text-center input-no-of-lay" name="plies" placeholder="0" value="">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-icon btn-danger btn-remove-lay-row"><i class="ri ri-delete-bin-line"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="6" class="text-center p-2 bg-light">
                                                    <button type="button" class="btn btn-sm btn-primary" id="btn-add-lay-row">
                                                        <i class="ri ri-add-line me-1"></i> ADD ROW
                                                    </button>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 2. Production Stages Section (Matches Image 2) -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">
                <div class="card-header-box d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <h4 class="mb-0">Production Stages</h4>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-stage-row">
                        <i class="ri ri-add-line me-1"></i> ADD STAGE
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle" id="production-stages-table">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width: 22%;">STAGE</th>
                                <th style="width: 22%;">ISSUE UNIT (PLANT)</th>
                                <th style="width: 10%;">RATE</th>
                                <th style="width: 14%;">ISSUE DATE</th>
                                <th style="width: 14%;">DEADLINE DATE</th>
                                <th style="width: 12%;">REMARKS</th>
                                <th style="width: 6%;">ACTION</th>
                            </tr>
                        </thead>
                        <tbody id="production-stages-tbody">
                            <tr class="stage-row">
                                <td>
                                    <select name="production_stages[0][stage_id]" class="form-select form-select-sm select2 stage-select" data-placeholder="Select Stage">
                                        <option value="">Select Stage</option>
                                        @foreach($operationStages as $os)
                                            <option value="{{ $os->id }}" data-cost="{{ $os->cost }}">{{ $os->operation_stage_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="production_stages[0][service_provider_id]" class="form-select form-select-sm select2 plant-select" data-placeholder="Select Unit">
                                        <option value="">Select Unit</option>
                                        @foreach($plants as $p)
                                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" name="production_stages[0][rate]" class="form-control form-control-sm text-center stage-rate bg-light" value="0.00" readonly>
                                </td>
                                <td>
                                    <input type="text" name="production_stages[0][issue_date]" class="form-control form-control-sm text-center flatpickr-date issue-date" placeholder="Enter Issue Date" value="">
                                </td>
                                <td>
                                    <input type="text" name="production_stages[0][deadline_date]" class="form-control form-control-sm text-center flatpickr-date deadline-date" placeholder="Enter Deadline Date" value="">
                                </td>
                                <td>
                                    <input type="text" name="production_stages[0][remarks]" class="form-control form-control-sm" placeholder="Enter Remarks" value="">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-icon btn-danger btn-remove-stage-row"><i class="ri ri-delete-bin-line"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 3. Cutting Size Ratio Section (Matches Image 3 Bottom) -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">
                <div class="card-header-box d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <h4 class="mb-0">Article Quantity Matrix</h4>
                    <span class="badge bg-primary fs-6 px-3 py-2" id="badgeExtraPiecesCount">Additional Qty: 0 pcs</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle" id="cutting-size-matrix-table">
                        <thead>
                            <tr class="table-light">
                                <th style="width: 15%;">SIZE</th>
                                @foreach($allSizes as $s)
                                    <th class="fw-bold">{{ $s }}</th>
                                @endforeach
                                <th style="width: 15%;">ROW TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!$isCanvas)
                            <!-- QTY - F/S Row -->
                            <tr>
                                <td class="fw-bold bg-light">QTY - F/S</td>
                                @foreach($allSizes as $idx => $s)
                                <td>
                                    <input type="number" min="0" step="1" name="sizes[{{ $idx }}][qty_fs]" class="form-control form-control-sm text-center fw-bold input-size-fs" data-size="{{ $s }}" placeholder="0" value="">
                                    <input type="hidden" name="sizes[{{ $idx }}][size]" value="{{ $s }}">
                                </td>
                                @endforeach
                                <td class="fw-bold text-primary fs-6" id="total_fs_row">0</td>
                            </tr>

                            <!-- QTY - H/S Row -->
                            <tr>
                                <td class="fw-bold bg-light">QTY - H/S</td>
                                @foreach($allSizes as $idx => $s)
                                <td>
                                    <input type="number" min="0" step="1" name="sizes[{{ $idx }}][qty_hs]" class="form-control form-control-sm text-center fw-bold input-size-hs" data-size="{{ $s }}" placeholder="0" value="">
                                </td>
                                @endforeach
                                <td class="fw-bold text-success fs-6" id="total_hs_row">0</td>
                            </tr>
                            @else
                            <!-- Canvas Single Qty Row -->
                            <tr>
                                <td class="fw-bold bg-light">EXTRA QTY</td>
                                @foreach($allSizes as $idx => $s)
                                <td>
                                    <input type="number" min="0" step="1" name="sizes[{{ $idx }}][qty_fs]" class="form-control form-control-sm text-center fw-bold input-size-fs" data-size="{{ $s }}" placeholder="0" value="">
                                    <input type="hidden" name="sizes[{{ $idx }}][size]" value="{{ $s }}">
                                    <input type="hidden" name="sizes[{{ $idx }}][qty_hs]" value="0">
                                </td>
                                @endforeach
                                <td class="fw-bold text-primary fs-6" id="total_fs_row">0</td>
                            </tr>
                            @endif
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td>TOTAL EXTRA</td>
                                @foreach($allSizes as $s)
                                    <td class="col-size-total text-primary" data-col-size="{{ $s }}">0</td>
                                @endforeach
                                <td class="text-primary fs-5" id="grand_extra_matrix_total">0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Submit Bar -->
        <div class="card shadow-sm border-0 mb-5" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
            <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <span class="text-muted fw-semibold">Common Grand Total:</span>
                    <strong class="text-success fs-3 ms-2" id="lblBottomCommonTotal">{{ number_format($jobCard->grand_total_qty, 0) }} pcs</strong>
                    <span class="text-muted small ms-2">(Planned: {{ number_format($jobCard->grand_total_qty, 0) }} + Additional: <span id="lblBottomExtra">0</span>)</span>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-5 fs-6" id="btnSubmitForm">Submit</button>
                    <a href="{{ url('job_card_entries') }}" class="btn btn-secondary px-4">Cancel</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const plannedTotal = {{ intval($jobCard->grand_total_qty) }};
    const availableSizes = @json($allSizes);
    const operationStagesList = @json($operationStages);
    const operationStagesData = @json($operationStages->keyBy('id'));
    const plantsData = @json($plants);

    // Initialize Select2 & Flatpickr
    $('.select2').select2({ width: '100%' });
    $('.select2-multi-sizes').select2({ width: '100%', placeholder: 'Select Sizes' });
    $('.flatpickr-date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });

    // Stage rate auto-fill & trigger deadline calculation
    $(document).on('change', '.stage-select', function() {
        const cost = $(this).find(':selected').data('cost') || 0;
        $(this).closest('tr').find('.stage-rate').val(cost ? parseFloat(cost).toFixed(2) : '0.00');
        $(this).closest('tr').find('.issue-date').trigger('change');
    });

    // Auto-calculate deadline date on issue date selection / stage selection
    $(document).on('change', '.issue-date', function() {
        let $row = $(this).closest('tr');
        let stageId = $row.find('.stage-select').val();
        let issueDateStr = $(this).val();
        let $deadlineInput = $row.find('.deadline-date');

        if (issueDateStr) {
            let parts = issueDateStr.split('-');
            if (parts.length === 3 && $deadlineInput.length && $deadlineInput[0]._flatpickr) {
                let issueDateObjForMin = new Date(parts[2], parts[1] - 1, parts[0]);
                if (!Number.isNaN(issueDateObjForMin.getTime())) {
                    $deadlineInput[0]._flatpickr.set('minDate', issueDateObjForMin);
                }
            }
        }

        if (stageId && issueDateStr) {
            let stageData = operationStagesData[stageId];
            if (stageData && stageData.working_days) {
                let parts = issueDateStr.split('-');
                if (parts.length === 3) {
                    let issueDateObj = new Date(parts[2], parts[1] - 1, parts[0]);
                    issueDateObj.setDate(issueDateObj.getDate() + parseInt(stageData.working_days));

                    let d = String(issueDateObj.getDate()).padStart(2, '0');
                    let m = String(issueDateObj.getMonth() + 1).padStart(2, '0');
                    let y = issueDateObj.getFullYear();

                    let deadlineDateStr = d + '-' + m + '-' + y;
                    $deadlineInput.val(deadlineDateStr);
                    if ($deadlineInput[0]._flatpickr) {
                        $deadlineInput[0]._flatpickr.setDate(deadlineDateStr, true);
                    }
                }
            }
        }
    });

    // Add Production Stage Row
    let stageIndex = 1;
    $('#btn-add-stage-row').on('click', function() {
        let stageOptions = '<option value="">Select Stage</option>';
        operationStagesList.forEach(os => {
            stageOptions += `<option value="${os.id}" data-cost="${os.cost}">${os.operation_stage_name}</option>`;
        });

        let plantOptions = '<option value="">Select Unit</option>';
        plantsData.forEach(p => {
            plantOptions += `<option value="${p.id}">${p.name}</option>`;
        });

        const newRow = `
            <tr class="stage-row">
                <td>
                    <select name="production_stages[${stageIndex}][stage_id]" class="form-select form-select-sm select2 stage-select">
                        ${stageOptions}
                    </select>
                </td>
                <td>
                    <select name="production_stages[${stageIndex}][service_provider_id]" class="form-select form-select-sm select2 plant-select">
                        ${plantOptions}
                    </select>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="production_stages[${stageIndex}][rate]" class="form-control form-control-sm text-center stage-rate bg-light" value="0.00" readonly>
                </td>
                <td>
                    <input type="text" name="production_stages[${stageIndex}][issue_date]" class="form-control form-control-sm text-center flatpickr-date issue-date" placeholder="DD-MM-YYYY" value="">
                </td>
                <td>
                    <input type="text" name="production_stages[${stageIndex}][deadline_date]" class="form-control form-control-sm text-center flatpickr-date deadline-date" placeholder="DD-MM-YYYY" value="">
                </td>
                <td>
                    <input type="text" name="production_stages[${stageIndex}][remarks]" class="form-control form-control-sm" placeholder="Enter Remarks">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-icon btn-danger btn-remove-stage-row"><i class="ri ri-delete-bin-line"></i></button>
                </td>
            </tr>
        `;
        $('#production-stages-tbody').append(newRow);
        $('#production-stages-tbody tr:last-child .select2').select2({ width: '100%' });
        $('#production-stages-tbody tr:last-child .flatpickr-date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });
        stageIndex++;
    });

    $(document).on('click', '.btn-remove-stage-row', function() {
        if ($('#production-stages-tbody tr').length > 1) {
            $(this).closest('tr').remove();
        } else {
            Swal.fire({ icon: 'info', text: 'At least one production stage is required.' });
        }
    });

    // Add Lay Mark Row
    $('#btn-add-lay-row').on('click', function() {
        const markNum = $('#consumption-lay-tbody tr').length + 1;
        let sizeOptions = '';
        availableSizes.forEach(s => {
            sizeOptions += `<option value="${s}">${s}</option>`;
        });

        const newLayRow = `
            <tr class="lay-row">
                <td class="fw-bold mark-number">${markNum}</td>
                <td>
                    <select class="form-select form-select-sm select2-multi-sizes select-lay-sizes" multiple="multiple" style="width: 100%;">
                        ${sizeOptions}
                    </select>
                </td>
                <td>
                    <select class="form-select form-select-sm select-lay-sleeve select2">
                        <option value="">Select Sleeve</option>
                        <option value="F/S">F/S</option>
                        <option value="H/S">H/S</option>
                    </select>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" class="form-control form-control-sm text-center input-lay-meter" placeholder="0.00" value="">
                </td>
                <td>
                    <input type="number" step="1" min="0" class="form-control form-control-sm text-center input-no-of-lay" placeholder="0" value="">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-icon btn-danger btn-remove-lay-row"><i class="ri ri-delete-bin-line"></i></button>
                </td>
            </tr>
        `;
        $('#consumption-lay-tbody').append(newLayRow);
        $('#consumption-lay-tbody tr:last-child .select2').select2({ width: '100%' });
        $('#consumption-lay-tbody tr:last-child .select2-multi-sizes').select2({ width: '100%', placeholder: 'Select Sizes' });
        syncCuttingRatioFromLayMarks();
    });

    $(document).on('click', '.btn-remove-lay-row', function() {
        if ($('#consumption-lay-tbody tr').length > 1) {
            $(this).closest('tr').remove();
            $('#consumption-lay-tbody tr').each(function(i) {
                $(this).find('.mark-number').text(i + 1);
            });
            recalcFabricMeters();
            syncCuttingRatioFromLayMarks();
        }
    });

    // Calculate Fabric Meters from Lay Mark Rows
    function recalcFabricMeters() {
        let totalMtrs = 0;
        $('#consumption-lay-tbody tr').each(function() {
            const meter = parseFloat($(this).find('.input-lay-meter').val()) || 0;
            const lays = parseFloat($(this).find('.input-no-of-lay').val()) || 0;
            if (meter > 0 && lays > 0) {
                totalMtrs += (meter * lays);
            }
        });
        if (totalMtrs > 0) {
            $('#issued_meters_input').val(totalMtrs.toFixed(2));
            $('#matrix_need_caption').text('Matrix Need: ' + totalMtrs.toFixed(2) + ' MTR');
        }
    }

    // Auto-Sync Cutting Size Ratio Matrix from Lay Marks (No. of Lay * Sizes)
    function syncCuttingRatioFromLayMarks() {
        const sizeCounts = {
            'fs': {},
            'hs': {}
        };

        availableSizes.forEach(s => {
            sizeCounts.fs[s] = 0;
            sizeCounts.hs[s] = 0;
        });

        let hasAnyLay = false;
        $('#consumption-lay-tbody tr.lay-row').each(function() {
            const selectedSizes = $(this).find('.select-lay-sizes').val() || [];
            const sleeve = ($(this).find('.select-lay-sleeve').val() || 'F/S').toUpperCase();
            const noOfLay = parseInt($(this).find('.input-no-of-lay').val()) || 0;

            if (noOfLay > 0 && selectedSizes.length > 0) {
                hasAnyLay = true;
                const sleeveKey = (sleeve === 'H/S') ? 'hs' : 'fs';
                selectedSizes.forEach(sz => {
                    const trimmedSz = String(sz).trim();
                    if (sizeCounts[sleeveKey].hasOwnProperty(trimmedSz)) {
                        sizeCounts[sleeveKey][trimmedSz] += noOfLay;
                    }
                });
            }
        });

        // Populate the Cutting Size Ratio inputs if lay marks were entered
        if (hasAnyLay) {
            availableSizes.forEach(s => {
                const fsQty = sizeCounts.fs[s] > 0 ? sizeCounts.fs[s] : '';
                const hsQty = sizeCounts.hs[s] > 0 ? sizeCounts.hs[s] : '';

                $(`.input-size-fs[data-size="${s}"]`).val(fsQty);
                $(`.input-size-hs[data-size="${s}"]`).val(hsQty);
            });
        }

        recalcMatrixTotals();
    }

    // Events that trigger Lay Mark calculation and Cutting Size Ratio auto-population
    $(document).on('input change', '.input-lay-meter', recalcFabricMeters);
    $(document).on('input change', '.input-no-of-lay', function() {
        recalcFabricMeters();
        syncCuttingRatioFromLayMarks();
    });
    $(document).on('change', '.select-lay-sizes, .select-lay-sleeve', function() {
        syncCuttingRatioFromLayMarks();
    });

    // Calculate Cutting Size Matrix & Common Totals
    function recalcMatrixTotals() {
        let grandFs = 0;
        let grandHs = 0;

        availableSizes.forEach(s => {
            const fsVal = parseInt($(`.input-size-fs[data-size="${s}"]`).val()) || 0;
            const hsVal = parseInt($(`.input-size-hs[data-size="${s}"]`).val()) || 0;
            const colTot = fsVal + hsVal;
            $(`.col-size-total[data-col-size="${s}"]`).text(colTot);
            grandFs += fsVal;
            grandHs += hsVal;
        });

        const totalExtra = grandFs + grandHs;
        $('#total_fs_row').text(grandFs);
        $('#total_hs_row').text(grandHs);
        $('#grand_extra_matrix_total').text(totalExtra);
        $('#badgeExtraPiecesCount').text('Additional Qty: ' + totalExtra + ' pcs');

        // Update Common Totals
        const commonTotal = plannedTotal + totalExtra;
        $('#lblSummaryExtraQty').html('+' + totalExtra + ' <small class="fs-6 text-muted">pcs</small>');
        $('#lblSummaryCommonTotal').html(commonTotal.toLocaleString() + ' <small class="fs-6 text-muted">pcs</small>');
        $('#lblBottomCommonTotal').text(commonTotal.toLocaleString() + ' pcs');
        $('#lblBottomExtra').text('+' + totalExtra);
    }
    $(document).on('input change', '.input-size-fs, .input-size-hs', recalcMatrixTotals);

    // Helper to display inline validation error message
    function showFieldError($elem, message = 'This field is required') {
        let $target = $elem;
        if ($elem.next('.select2-container').length) {
            $target = $elem.next('.select2-container');
        }
        $target.after(`<span class="text-danger small validation-error d-block mt-1">${message}</span>`);
    }

    // Dynamic error removal on user input/change
    $(document).on('input change select2:select select2:unselect', 'input, select', function() {
        $(this).closest('td').find('.validation-error').remove();
        $(this).next('.select2-container').next('.validation-error').remove();
        $('#matrix-validation-error').remove();
    });

    // Form Submit with Field-Level Inline Validation
    $('#formAdditionalQty').on('submit', function(e) {
        e.preventDefault();
        $('.validation-error').remove();
        let hasError = false;

        // 1. Validate Fabric Details - Lay Marks (Sizes, Sleeve, Meter, No of Lay)
        $('#consumption-lay-tbody tr.lay-row').each(function() {
            const $sizesSelect = $(this).find('.select-lay-sizes');
            const sizesVal = $sizesSelect.val();
            if (!sizesVal || sizesVal.length === 0) {
                showFieldError($sizesSelect, 'This field is required');
                hasError = true;
            }

            const $sleeveSelect = $(this).find('.select-lay-sleeve');
            const sleeveVal = $sleeveSelect.val();
            if (!sleeveVal) {
                showFieldError($sleeveSelect, 'This field is required');
                hasError = true;
            }

            const $meterInput = $(this).find('.input-lay-meter');
            const meterVal = parseFloat($meterInput.val());
            if (isNaN(meterVal) || meterVal <= 0) {
                showFieldError($meterInput, 'This field is required');
                hasError = true;
            }

            const $layInput = $(this).find('.input-no-of-lay');
            const layVal = parseInt($layInput.val());
            if (isNaN(layVal) || layVal <= 0) {
                showFieldError($layInput, 'This field is required');
                hasError = true;
            }
        });

        // 2. Validate Issued Meters
        const issuedMtr = parseFloat($('#issued_meters_input').val()) || 0;
        if (issuedMtr <= 0) {
            showFieldError($('#issued_meters_input'), 'This field is required');
            hasError = true;
        }

        // 3. Validate Production Stages (Stage, Unit, Issue Date, Delivery Date)
        $('#production-stages-tbody tr.stage-row').each(function() {
            const $stage = $(this).find('.stage-select');
            if (!$stage.val()) {
                showFieldError($stage, 'This field is required');
                hasError = true;
            }

            const $unit = $(this).find('.plant-select');
            if (!$unit.val()) {
                showFieldError($unit, 'This field is required');
                hasError = true;
            }

            const $issueDate = $(this).find('.issue-date');
            if (!$issueDate.val()) {
                showFieldError($issueDate, 'This field is required');
                hasError = true;
            }

            const $deadlineDate = $(this).find('.deadline-date');
            if (!$deadlineDate.val()) {
                showFieldError($deadlineDate, 'This field is required');
                hasError = true;
            }
        });

        // 4. Validate Cutting Size Ratio Matrix Total
        let totalExtra = 0;
        availableSizes.forEach(s => {
            const fsVal = parseInt($(`.input-size-fs[data-size="${s}"]`).val()) || 0;
            const hsVal = parseInt($(`.input-size-hs[data-size="${s}"]`).val()) || 0;
            totalExtra += (fsVal + hsVal);
        });

        if (totalExtra <= 0) {
            $('#cutting-size-matrix-table').after('<div id="matrix-validation-error" class="text-danger small validation-error fw-bold mt-2 text-center">This field is required (Please enter additional quantity in the matrix)</div>');
            hasError = true;
        }

        if (hasError) {
            const $firstErr = $('.validation-error').first();
            if ($firstErr.length) {
                $('html, body').animate({
                    scrollTop: $firstErr.offset().top - 140
                }, 300);
            }
            return;
        }

        const btn = $('#btnSubmitForm');
        const origText = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

        const formData = new FormData($('#formAdditionalQty')[0]);

        $.ajax({
            url: "{{ route('job_card_entries.add_additional_qty', $jobCard->id) }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}'
            },
            success: function(res) {
                if (res.success) {
                    window.location.href = res.redirect || "{{ url('job_card_entries') }}";
                } else {
                    btn.prop('disabled', false).html(origText);
                    alert(res.message || 'Failed to save additional quantity.');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(origText);
                let msg = 'Failed to save additional quantity.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                alert(msg);
            }
        });
    });
});
</script>
@endsection

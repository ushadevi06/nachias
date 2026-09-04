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
        
        if ($editingBatch) {
            $baseFabrics = (!empty($editingBatchGroup) && $editingBatchGroup->count() > 0) ? $editingBatchGroup : collect([$editingBatch]);
        } else {
            $baseFabrics = $jobCard->fabricDetails->where('is_additional', 0)->values();
            if ($baseFabrics->isEmpty()) {
                $baseFabrics = $jobCard->fabricDetails->values();
            }
        }
        $hasMultipleFabrics = $baseFabrics->count() > 1;

        $currentAdditional = intval($jobCard->additional_qty ?? 0);
        $initialPlanned = max(0, intval($jobCard->grand_total_qty) - $currentAdditional);
        $editBatchSumQty = ($editingBatch && !empty($editingBatchGroup) && $editingBatchGroup->count() > 0) ? $editingBatchGroup->sum('total_qty') : ($editingBatch ? intval($editingBatch->total_qty) : 0);
        $baseTotalForView = $editingBatch ? (intval($jobCard->grand_total_qty) - $editBatchSumQty) : intval($jobCard->grand_total_qty);
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
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('job_card_entries.additional_qty_history', $jobCard->id) }}" class="btn btn-dark d-flex align-items-center gap-1 shadow-sm">
                <i class="ri ri-history-line"></i> Addition History 
                @if(!empty($additionalBatches) && count($additionalBatches) > 0)
                    <span class="badge bg-warning text-dark rounded-pill ms-1">{{ count($additionalBatches) }}</span>
                @endif
            </a>
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

    @if($editingBatch)
        @php
            $editBatchTotalQty = (!empty($editingBatchGroup) && $editingBatchGroup->count() > 0) ? $editingBatchGroup->sum('total_qty') : $editingBatch->total_qty;
            $editBatchTotalMtr = (!empty($editingBatchGroup) && $editingBatchGroup->count() > 0) ? $editingBatchGroup->sum('mtr') : $editingBatch->mtr;
            $editBatchArtList = (!empty($editingBatchGroup) && $editingBatchGroup->count() > 0) ? $editingBatchGroup->pluck('art_no')->implode(', ') : $editingBatch->art_no;
        @endphp
        <!-- Edit Mode Banner -->
        <div class="alert alert-warning border-0 shadow-sm d-flex justify-content-between align-items-center mb-4 p-3 rounded-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-warning text-dark p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="ri ri-edit-2-line fs-5"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Editing Addition Batch #{{ $batchIndex }} ({{ $editBatchArtList }})</h5>
                    <div class="small text-muted">
                        Added on: <strong>{{ $editingBatch->created_at ? $editingBatch->created_at->format('d-m-Y h:i A') : '-' }}</strong> | 
                        Current Batch Qty: <strong class="text-primary">+{{ $editBatchTotalQty }} pcs</strong> ({{ number_format($editBatchTotalMtr, 2) }} Mtr)
                    </div>
                </div>
            </div>
            <a href="{{ url('job_card_entries/additional-qty/' . $jobCard->id) }}" class="btn btn-outline-dark btn-sm d-flex align-items-center gap-1">
                <i class="ri ri-close-circle-line"></i> Cancel Edit & Add New Batch
            </a>
        </div>
    @endif

    <!-- Common Total Summary Bar -->
    <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #f8f9fa 0%, #edf2f7 100%);">
        <div class="card-body py-3">
            <div class="row text-center align-items-center">
                <div class="col-md-4 border-end">
                    <span class="text-muted small d-block mb-1 fw-semibold text-uppercase">Planned Quantity</span>
                    <h3 class="mb-0 fw-bold text-dark">{{ number_format($initialPlanned, 0) }} <small class="fs-6 text-muted">pcs</small></h3>
                    @if(!$editingBatch && $currentAdditional > 0)
                        <div class="small text-muted">(+{{ $currentAdditional }} pcs already added)</div>
                    @endif
                </div>
                <div class="col-md-4 border-end">
                    <span class="text-warning-emphasis small d-block mb-1 fw-bold text-uppercase">{{ $editingBatch ? 'Batch #'.$batchIndex.' Qty' : 'This Addition' }}</span>
                    <h3 class="mb-0 fw-bold text-warning" id="lblSummaryExtraQty">+0 <small class="fs-6 text-muted">pcs</small></h3>
                </div>
                <div class="col-md-4">
                    <span class="text-success small d-block mb-1 fw-bold text-uppercase">New Grand Total</span>
                    <h3 class="mb-0 fw-bold text-success" id="lblSummaryCommonTotal">{{ number_format($jobCard->grand_total_qty, 0) }} <small class="fs-6 text-muted">pcs</small></h3>
                </div>
            </div>
        </div>
    </div>

    <form id="formAdditionalQty" method="POST" action="{{ $editingBatch ? route('job_card_entries.update_additional_batch', [$jobCard->id, $editingBatch->id]) : route('job_card_entries.add_additional_qty', $jobCard->id) }}" enctype="multipart/form-data" novalidate>
        @csrf

        <!-- 1. Fabric Details Section (Side-by-Side Columns for All Fabrics) -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">
                <div class="card-header-box mb-3 border-bottom pb-2 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Fabric Details</h4>
                    @if($hasMultipleFabrics)
                        <span class="badge bg-label-primary px-3 py-2 fs-6">
                            <i class="ri ri-layout-column-line me-1"></i> {{ $baseFabrics->count() }} Fabrics (Side-by-Side)
                        </span>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle" id="fabric-details-table" style="min-width: {{ count($baseFabrics) * 1260 }}px; width: max-content; table-layout: fixed;">
                        <thead>
                            <tr id="fabric-details-head">
                                @foreach($baseFabrics as $idx => $bf)
                                    <th colspan="2" class="bg-light p-3" style="width: 1260px; min-width: 1260px; max-width: 1260px;">
                                        <label class="small text-primary fw-bold text-uppercase d-block mb-2">IMAGE</label>
                                        <div class="d-flex align-items-center justify-content-center gap-3">
                                            <input type="file" class="form-control form-control-sm" name="fabrics[{{ $idx }}][fabric_image]" accept="image/*" style="max-width: 350px;">
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody id="fabric-details-body">
                            <!-- ART NO Row -->
                            <tr>
                                @foreach($baseFabrics as $idx => $bf)
                                    <td class="fw-bold bg-light" style="width: 160px; min-width: 160px; max-width: 160px;">ART NO</td>
                                    <td class="p-2" style="width: 1100px; min-width: 1100px; max-width: 1100px;">
                                        <input type="text" class="form-control form-control-sm text-center fw-bold" value="{{ $bf->art_no }}" readonly>
                                        <input type="hidden" name="fabrics[{{ $idx }}][art_no]" value="{{ $bf->art_no }}">
                                        <input type="hidden" name="fabrics[{{ $idx }}][stock_entry_id]" value="{{ $bf->stock_entry_id }}">
                                    </td>
                                @endforeach
                            </tr>

                            <!-- WIDTH Row -->
                            <tr>
                                @foreach($baseFabrics as $idx => $bf)
                                    @php
                                        $widthVal = $bf->width ?? ($jobCard->width ?? '');
                                    @endphp
                                    <td class="fw-bold bg-light" style="width: 160px; min-width: 160px; max-width: 160px;">WIDTH</td>
                                    <td class="p-2" style="width: 1100px; min-width: 1100px; max-width: 1100px;">
                                        <select name="fabrics[{{ $idx }}][width]" class="form-select form-select-sm text-center fabric-width-select" data-fabric-index="{{ $idx }}">
                                            <option value="">Select Width</option>
                                            @foreach($fabricSizes as $fs)
                                                <option value="{{ $fs->id }}" {{ ((string)$widthVal === (string)$fs->id || (string)$widthVal === (string)$fs->width) ? 'selected' : '' }}>
                                                    {{ $fs->width }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                @endforeach
                            </tr>

                            <!-- ISSUED METERS Row -->
                            <tr>
                                @foreach($baseFabrics as $idx => $bf)
                                    @php
                                        $mtrVal = $editingBatch ? ($bf->mtr ?? '0.00') : '0.00';
                                    @endphp
                                    <td class="fw-bold bg-light" style="width: 160px; min-width: 160px; max-width: 160px;">ISSUED METERS</td>
                                    <td class="p-2" style="width: 1100px; min-width: 1100px; max-width: 1100px;">
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            <input type="number" step="0.01" min="0" name="fabrics[{{ $idx }}][total_fabric_meters]" id="issued_meters_{{ $idx }}" class="form-control form-control-sm text-center fw-bold issued-meters-input" data-fabric-index="{{ $idx }}" value="{{ $mtrVal }}" placeholder="0.00" readonly>
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-recalc-fabric-meters" data-fabric-index="{{ $idx }}" title="Recalculate Fabric Meters"><i class="ri ri-refresh-line"></i></button>
                                        </div>
                                        <div class="small text-muted mt-1 matrix-need-caption" id="matrix_need_caption_{{ $idx }}">Matrix Need: {{ number_format(floatval($mtrVal), 2) }} MTR</div>
                                    </td>
                                @endforeach
                            </tr>

                            <!-- IN/OUT Row -->
                            <tr>
                                @foreach($baseFabrics as $idx => $bf)
                                    @php
                                        $inOutVal = $bf->in_out ?? 'NO';
                                    @endphp
                                    <td class="fw-bold bg-light" style="width: 160px; min-width: 160px; max-width: 160px;">IN/OUT</td>
                                    <td class="p-2" style="width: 1100px; min-width: 1100px; max-width: 1100px;">
                                        <input type="text" name="fabrics[{{ $idx }}][in_out]" class="form-control form-control-sm text-center in-out-input" value="{{ $inOutVal }}">
                                    </td>
                                @endforeach
                            </tr>

                            <!-- N.PATTI Row -->
                            <tr>
                                @foreach($baseFabrics as $idx => $bf)
                                    @php
                                        $nPattiVal = $bf->n_patti ?? 'WHITE';
                                    @endphp
                                    <td class="fw-bold bg-light" style="width: 160px; min-width: 160px; max-width: 160px;">N.PATTI</td>
                                    <td class="p-2" style="width: 1100px; min-width: 1100px; max-width: 1100px;">
                                        <input type="text" name="fabrics[{{ $idx }}][n_patti]" class="form-control form-control-sm text-center n-patti-input" value="{{ $nPattiVal }}">
                                    </td>
                                @endforeach
                            </tr>

                            <!-- CONSUMPTION MTR Row -->
                            <tr>
                                @foreach($baseFabrics as $idx => $bf)
                                    <td class="fw-bold bg-light" style="width: 160px; min-width: 160px; max-width: 160px; vertical-align: middle;">
                                        CONSUMPTION<br>
                                        <span class="badge bg-secondary">MTR</span>
                                    </td>
                                    <td class="p-0" style="width: 1100px; min-width: 1100px; max-width: 1100px;">
                                        <table class="table table-bordered table-sm mb-0 align-middle text-center lay-mark-table" id="lay-mark-table-art-{{ $idx }}" data-fabric-index="{{ $idx }}" data-art="{{ $bf->art_no }}">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 70px; min-width: 70px;">MARK</th>
                                                    <th style="width: 440px; min-width: 440px;">SIZE</th>
                                                    <th style="width: 140px; min-width: 140px;">SLEEVE</th>
                                                    <th style="width: 180px; min-width: 180px;">LAY MARK METER</th>
                                                    <th style="width: 180px; min-width: 180px;">NO.OF LAY</th>
                                                    <th style="width: 70px; min-width: 70px;"><i class="ri ri-settings-4-line"></i></th>
                                                </tr>
                                            </thead>
                                            <tbody class="consumption-lay-tbody" id="consumption-lay-tbody-{{ $idx }}">
                                                @php
                                                    $batchLayMarks = ($editingBatch && $bf->layMarks && $bf->layMarks->count() > 0) 
                                                        ? $bf->layMarks 
                                                        : collect();
                                                @endphp
                                                @if($batchLayMarks->count() > 0)
                                                    @foreach($batchLayMarks as $lmIdx => $lm)
                                                        @php
                                                            $selectedLmSizes = is_array($lm->sizes) ? $lm->sizes : (is_string($lm->sizes) ? json_decode($lm->sizes, true) ?? explode(',', $lm->sizes) : []);
                                                        @endphp
                                                        <tr class="lay-mark-row" data-fabric-index="{{ $idx }}">
                                                            <td class="fw-bold mark-no">{{ $lmIdx + 1 }}</td>
                                                            <td>
                                                                <select class="form-select form-select-sm select2-multi-sizes select-lay-sizes" name="fabrics[{{ $idx }}][lay_marks][{{ $lmIdx }}][sizes][]" multiple="multiple" style="width: 100%;">
                                                                    @foreach($allSizes as $sz)
                                                                        <option value="{{ $sz }}" {{ in_array((string)$sz, array_map('strval', $selectedLmSizes)) ? 'selected' : '' }}>{{ $sz }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-select form-select-sm select-lay-sleeve select2" name="fabrics[{{ $idx }}][lay_marks][{{ $lmIdx }}][sleeve]" data-placeholder="Select Sleeve">
                                                                    <option value="F/S" {{ ($lm->sleeve_type == 'F/S') ? 'selected' : '' }}>F/S</option>
                                                                    <option value="H/S" {{ ($lm->sleeve_type == 'H/S') ? 'selected' : '' }}>H/S</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" min="0" class="form-control form-control-sm text-center input-lay-meter" name="fabrics[{{ $idx }}][lay_marks][{{ $lmIdx }}][meter]" placeholder="0.00" value="{{ $lm->lay_mark_meter }}">
                                                            </td>
                                                            <td>
                                                                <input type="number" step="1" min="0" class="form-control form-control-sm text-center input-no-of-lay" name="fabrics[{{ $idx }}][lay_marks][{{ $lmIdx }}][no_of_lay]" placeholder="0" value="{{ $lm->no_of_lay }}">
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-icon btn-danger remove-lay-mark"><i class="ri ri-delete-bin-line"></i></button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr class="lay-mark-row" data-fabric-index="{{ $idx }}">
                                                        <td class="fw-bold mark-no">1</td>
                                                        <td>
                                                            <select class="form-select form-select-sm select2-multi-sizes select-lay-sizes" name="fabrics[{{ $idx }}][lay_marks][0][sizes][]" multiple="multiple" style="width: 100%;">
                                                                @foreach($allSizes as $sz)
                                                                    <option value="{{ $sz }}">{{ $sz }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-select form-select-sm select-lay-sleeve select2" name="fabrics[{{ $idx }}][lay_marks][0][sleeve]" data-placeholder="Select Sleeve">
                                                                <option value="F/S">F/S</option>
                                                                <option value="H/S">H/S</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.01" min="0" class="form-control form-control-sm text-center input-lay-meter" name="fabrics[{{ $idx }}][lay_marks][0][meter]" placeholder="0.00" value="">
                                                        </td>
                                                        <td>
                                                            <input type="number" step="1" min="0" class="form-control form-control-sm text-center input-no-of-lay" name="fabrics[{{ $idx }}][lay_marks][0][no_of_lay]" placeholder="0" value="">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-icon btn-danger remove-lay-mark"><i class="ri ri-delete-bin-line"></i></button>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="6" class="text-center p-2 bg-light">
                                                        <button type="button" class="btn btn-sm btn-primary add-lay-mark-btn" data-fabric-index="{{ $idx }}">
                                                            <i class="ri ri-add-line me-1"></i> ADD ROW
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 2. Production Stages Section -->
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
                                <th style="width: 20%;">STAGE *</th>
                                <th style="width: 20%;">ISSUE UNIT (PLANT) *</th>
                                <th style="width: 8%;">RATE *</th>
                                <th style="width: 13%;">ISSUE DATE *</th>
                                <th style="width: 13%;">DEADLINE DATE *</th>
                                <th style="width: 12%;">REMARKS</th>
                                <th style="width: 14%;">ACTION</th>
                            </tr>
                        </thead>
                        <tbody id="production-stages-tbody">
                            @php
                                $existingOps = ($editingBatch && $jobCard->operations->count() > 0) ? $jobCard->operations : collect();
                            @endphp
                            @if($existingOps->count() > 0)
                                @foreach($existingOps as $stIdx => $op)
                                    <tr class="stage-row">
                                        <td>
                                            <select name="production_stages[{{ $stIdx }}][stage_id]" class="form-select form-select-sm select2 stage-select" data-placeholder="Select Stage">
                                                <option value="">Select Stage</option>
                                                @foreach($operationStages as $os)
                                                    <option value="{{ $os->id }}" data-cost="{{ $os->cost }}" {{ ($op->operation_stage_id == $os->id) ? 'selected' : '' }}>
                                                        {{ $os->operation_stage_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="production_stages[{{ $stIdx }}][service_provider_id]" class="form-select form-select-sm select2 plant-select" data-placeholder="Select Unit">
                                                <option value="">Select Unit</option>
                                                @foreach($plants as $p)
                                                    <option value="{{ $p->id }}" {{ ($op->service_provider_id == $p->id) ? 'selected' : '' }}>
                                                        {{ $p->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" name="production_stages[{{ $stIdx }}][rate]" class="form-control form-control-sm text-center stage-rate bg-light" value="{{ number_format(floatval($op->rate ?? 0), 2, '.', '') }}" readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="production_stages[{{ $stIdx }}][issue_date]" class="form-control form-control-sm text-center flatpickr-date issue-date" placeholder="Enter Issue Date" value="{{ $op->assigned_date ? date('d-m-Y', strtotime($op->assigned_date)) : '' }}">
                                        </td>
                                        <td>
                                            <input type="text" name="production_stages[{{ $stIdx }}][deadline_date]" class="form-control form-control-sm text-center flatpickr-date deadline-date" placeholder="Enter Deadline Date" value="{{ $op->deadline_date ? date('d-m-Y', strtotime($op->deadline_date)) : '' }}">
                                        </td>
                                        <td>
                                            <input type="text" name="production_stages[{{ $stIdx }}][remarks]" class="form-control form-control-sm" placeholder="Enter Remarks" value="{{ $op->remarks ?? '' }}">
                                        </td>
                                        <td class="text-center text-nowrap">
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                @php
                                                    $currentStageId = $op->operation_stage_id ?? null;
                                                    $taskData = $stageTaskStatus[$currentStageId] ?? null;
                                                    $hasTask = !empty($taskData);
                                                @endphp
                                                @if(!$hasTask)
                                                    <button type="button" class="btn btn-sm btn-icon btn-danger btn-remove-stage-row"><i class="ri ri-delete-bin-line"></i></button>
                                                @endif
                                                @if($editingBatch && (auth()->id() == 1 || (auth()->check() && auth()->user()->can('assign-task job-card'))))
                                                @php
                                                    $taskStatus = $taskData['status'] ?? null;
                                                    $taskNo = $taskData['task_no'] ?? null;
                                                    $previousOp = $existingOps[$stIdx - 1] ?? null;
                                                    $previousStageId = $previousOp ? ($previousOp->operation_stage_id ?? null) : null;
                                                    if ($stIdx > 0) {
                                                        $previousTaskAssigned = !empty($stageTaskStatus[$previousStageId]);
                                                    } else {
                                                        $previousTaskAssigned = true;
                                                    }
                                                    $canAssignCurrentStage = ($hasIssuedItems ?? true) && $previousTaskAssigned && !$hasTask;
                                                    $buttonText = $hasTask ? 'Assigned (#' . $taskNo . ')' : 'Assign Task';

                                                    if (!($hasIssuedItems ?? true) && !$hasTask) {
                                                        $buttonTitle = 'Materials not yet issued';
                                                    } elseif (!$previousTaskAssigned) {
                                                        $buttonTitle = 'Previous stage task not assigned';
                                                    } elseif ($hasTask) {
                                                        $buttonTitle = "Task already assigned (Status: $taskStatus)";
                                                    } else {
                                                        $buttonTitle = 'Assign Task';
                                                    }
                                                @endphp
                                                <button type="button" class="btn btn-sm btn-outline-primary assign-task-btn" title="{{ $buttonTitle }}" {{ !$canAssignCurrentStage ? 'disabled' : '' }} data-stage-id="{{ $currentStageId }}">
                                                    <i class="ri ri-task-line"></i> {{ $buttonText }}
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="stage-row">
                                    <td>
                                        <select name="production_stages[0][stage_id]" class="form-select form-select-sm select2 stage-select" data-placeholder="Select Stage">
                                            <option value="">Select Stage</option>
                                            @foreach($operationStages as $innerOs)
                                                <option value="{{ $innerOs->id }}" data-cost="{{ $innerOs->cost }}">
                                                    {{ $innerOs->operation_stage_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="production_stages[0][service_provider_id]" class="form-select form-select-sm select2 plant-select" data-placeholder="Select Unit">
                                            <option value="">Select Unit</option>
                                            @foreach($plants as $p)
                                                <option value="{{ $p->id }}" {{ ($jobCard->service_provider_id == $p->id) ? 'selected' : '' }}>
                                                    {{ $p->name }}
                                                </option>
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
                                    <td class="text-center text-nowrap">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <button type="button" class="btn btn-sm btn-icon btn-danger btn-remove-stage-row"><i class="ri ri-delete-bin-line"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 3. Article Quantity Matrix Section -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">
                <div class="card-header-box d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <h4 class="mb-0">Article Quantity Matrix</h4>
                    <span class="badge bg-primary px-3 py-2 fs-6" id="badgeExtraPiecesCount">
                        Additional Qty: 0 pcs
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle mb-0" id="cutting-size-matrix-table">
                        <thead class="table-light">
                            @if(!$isCanvas)
                                <tr>
                                    <th rowspan="2" class="align-middle bg-light text-center" style="min-width: 170px; width: 170px;">ART NO / MATERIAL</th>
                                    <th colspan="{{ count($allSizes) }}" class="text-center bg-light fw-bold">F/S</th>
                                    <th colspan="{{ count($allSizes) }}" class="text-center bg-light fw-bold">H/S</th>
                                    <th rowspan="2" class="align-middle bg-light text-center" style="min-width: 110px; width: 110px;">TOTAL</th>
                                </tr>
                                <tr>
                                    @foreach($allSizes as $sz)
                                        <th class="text-center bg-light" style="min-width: 75px;">{{ $sz }}</th>
                                    @endforeach
                                    @foreach($allSizes as $sz)
                                        <th class="text-center bg-light" style="min-width: 75px;">{{ $sz }}</th>
                                    @endforeach
                                </tr>
                            @else
                                <tr>
                                    <th class="align-middle bg-light text-center" style="min-width: 170px; width: 170px;">ART NO / MATERIAL</th>
                                    @foreach($allSizes as $sz)
                                        <th class="text-center bg-light" style="min-width: 75px;">{{ $sz }}</th>
                                    @endforeach
                                    <th class="align-middle bg-light text-center" style="min-width: 110px; width: 110px;">TOTAL</th>
                                </tr>
                            @endif
                        </thead>
                        <tbody>
                            @foreach($baseFabrics as $idx => $bf)
                                @php
                                    $batchQuantities = ($editingBatch && $bf->quantities && $bf->quantities->count() > 0)
                                        ? $bf->quantities
                                        : collect();
                                    $matName = $jobCard->item->item_name ?? 'COTTON LINEN';
                                @endphp
                                <tr class="matrix-art-row" data-fabric-index="{{ $idx }}" data-art="{{ $bf->art_no }}">
                                    <td>
                                        <div class="border rounded p-1 mb-1 text-center fw-bold small bg-white text-primary">{{ $bf->art_no }}</div>
                                        <input type="hidden" name="fabrics[{{ $idx }}][art_no]" value="{{ $bf->art_no }}">
                                        <div class="small text-muted text-center text-uppercase" style="font-size: 10px; line-height: 1.1;">{{ $matName }}</div>
                                    </td>
                                    @if(!$isCanvas)
                                        <!-- F/S Inputs -->
                                        @foreach($allSizes as $sz)
                                            @php
                                                $qObj = $batchQuantities->firstWhere('size', (string)$sz);
                                                $fsQty = ($qObj && $qObj->qty_fs > 0) ? $qObj->qty_fs : '';
                                            @endphp
                                            <td>
                                                <input type="number" min="0" step="1" name="fabrics[{{ $idx }}][sizes][{{ $sz }}][qty_fs]" class="form-control form-control-sm text-center input-size-qty input-size-fs" data-fabric-index="{{ $idx }}" data-col="fs-{{ $sz }}" data-size="{{ $sz }}" value="{{ $fsQty }}" placeholder="0">
                                                <input type="hidden" name="fabrics[{{ $idx }}][sizes][{{ $sz }}][size]" value="{{ $sz }}">
                                            </td>
                                        @endforeach
                                        <!-- H/S Inputs -->
                                        @foreach($allSizes as $sz)
                                            @php
                                                $qObj = $batchQuantities->firstWhere('size', (string)$sz);
                                                $hsQty = ($qObj && $qObj->qty_hs > 0) ? $qObj->qty_hs : '';
                                            @endphp
                                            <td>
                                                <input type="number" min="0" step="1" name="fabrics[{{ $idx }}][sizes][{{ $sz }}][qty_hs]" class="form-control form-control-sm text-center input-size-qty input-size-hs" data-fabric-index="{{ $idx }}" data-col="hs-{{ $sz }}" data-size="{{ $sz }}" value="{{ $hsQty }}" placeholder="0">
                                            </td>
                                        @endforeach
                                    @else
                                        <!-- Canvas Single Qty Inputs -->
                                        @foreach($allSizes as $sz)
                                            @php
                                                $qObj = $batchQuantities->firstWhere('size', (string)$sz);
                                                $fsQty = ($qObj && $qObj->total_qty > 0) ? $qObj->total_qty : '';
                                            @endphp
                                            <td>
                                                <input type="number" min="0" step="1" name="fabrics[{{ $idx }}][sizes][{{ $sz }}][qty_fs]" class="form-control form-control-sm text-center input-size-qty input-size-fs" data-fabric-index="{{ $idx }}" data-col="fs-{{ $sz }}" data-size="{{ $sz }}" value="{{ $fsQty }}" placeholder="0">
                                                <input type="hidden" name="fabrics[{{ $idx }}][sizes][{{ $sz }}][size]" value="{{ $sz }}">
                                            </td>
                                        @endforeach
                                    @endif
                                    <td>
                                        <input type="text" class="form-control form-control-sm row-total text-center fw-bold bg-light" id="row_total_{{ $idx }}" value="0" readonly tabindex="-1">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td class="fw-bold text-center small">CUTTING TOTAL (PCS)</td>
                                @if(!$isCanvas)
                                    @foreach($allSizes as $sz)
                                        <td><div class="col-total col-size-total-fs text-center fw-bold small py-1 border rounded bg-white" data-col="fs-{{ $sz }}" style="min-height: 30px; display: flex; align-items: center; justify-content: center;">0</div></td>
                                    @endforeach
                                    @foreach($allSizes as $sz)
                                        <td><div class="col-total col-size-total-hs text-center fw-bold small py-1 border rounded bg-white" data-col="hs-{{ $sz }}" style="min-height: 30px; display: flex; align-items: center; justify-content: center;">0</div></td>
                                    @endforeach
                                @else
                                    @foreach($allSizes as $sz)
                                        <td><div class="col-total col-size-total-fs text-center fw-bold small py-1 border rounded bg-white" data-col="fs-{{ $sz }}" style="min-height: 30px; display: flex; align-items: center; justify-content: center;">0</div></td>
                                    @endforeach
                                @endif
                                <td><div id="grand_extra_matrix_total" class="grand-total text-center fw-bold py-1 border rounded small text-primary bg-white" style="min-height: 30px; display: flex; align-items: center; justify-content: center;">0</div></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- 4. Save & Summary Actions Bar -->
        <div class="card shadow-sm border-0 sticky-bottom-card mb-4">
            <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <strong class="text-success fs-3 ms-2" id="lblBottomCommonTotal">{{ number_format($editingBatch ? ($baseTotalForView + $editBatchSumQty) : $jobCard->grand_total_qty, 0) }} pcs</strong>
                    <span class="text-muted small ms-2">({{ $editingBatch ? 'Base: '.number_format($baseTotalForView, 0).' + Batch #'.$batchIndex : 'Planned: '.number_format($initialPlanned, 0).' + This Addition' }}: <span id="lblBottomExtra">0</span>)</span>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn {{ $editingBatch ? 'btn-warning text-dark' : 'btn-primary' }} px-5 fs-6 fw-bold shadow" id="btnSubmitForm">
                        <i class="ri ri-save-line me-1"></i> {{ $editingBatch ? 'Update Addition Batch #' . $batchIndex : 'Submit Additional Quantity' }}
                    </button>
                    @if($editingBatch)
                        <a href="{{ url('job_card_entries/additional-qty/' . $jobCard->id) }}" class="btn btn-outline-dark px-4">Cancel Edit</a>
                    @else
                        <a href="{{ url('job_card_entries') }}" class="btn btn-secondary px-4">Cancel</a>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const baseTotal = {{ intval($baseTotalForView) }};
    const availableSizes = @json($allSizes);
    const operationStagesList = @json($operationStages);
    const operationStagesData = @json($operationStages->keyBy('id'));
    const plantsData = @json($plants);
    const fabricCount = {{ $baseFabrics->count() }};
    let isSyncing = false;

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

    function renumberStageRows() {
        $('#production-stages-tbody tr.stage-row').each(function(idx) {
            $(this).find('.stage-select').attr('name', `production_stages[${idx}][stage_id]`);
            $(this).find('.plant-select').attr('name', `production_stages[${idx}][service_provider_id]`);
            $(this).find('.stage-rate').attr('name', `production_stages[${idx}][rate]`);
            $(this).find('.issue-date').attr('name', `production_stages[${idx}][issue_date]`);
            $(this).find('.deadline-date').attr('name', `production_stages[${idx}][deadline_date]`);
            $(this).find('input[placeholder="Enter Remarks"]').attr('name', `production_stages[${idx}][remarks]`);
        });
    }

    // Add Production Stage Row
    $('#btn-add-stage-row').on('click', function() {
        const stageIndex = $('#production-stages-tbody tr.stage-row').length;
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
                    <select name="production_stages[${stageIndex}][stage_id]" class="form-select form-select-sm select2 stage-select" data-placeholder="Select Stage">
                        ${stageOptions}
                    </select>
                </td>
                <td>
                    <select name="production_stages[${stageIndex}][service_provider_id]" class="form-select form-select-sm select2 plant-select" data-placeholder="Select Unit">
                        ${plantOptions}
                    </select>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="production_stages[${stageIndex}][rate]" class="form-control form-control-sm text-center stage-rate bg-light" value="0.00" readonly>
                </td>
                <td>
                    <input type="text" name="production_stages[${stageIndex}][issue_date]" class="form-control form-control-sm text-center flatpickr-date issue-date" placeholder="Enter Issue Date" value="">
                </td>
                <td>
                    <input type="text" name="production_stages[${stageIndex}][deadline_date]" class="form-control form-control-sm text-center flatpickr-date deadline-date" placeholder="Enter Deadline Date" value="">
                </td>
                <td>
                    <input type="text" name="production_stages[${stageIndex}][remarks]" class="form-control form-control-sm" placeholder="Enter Remarks" value="">
                </td>
                <td class="text-center text-nowrap">
                    <div class="d-flex align-items-center justify-content-center gap-1">
                        <button type="button" class="btn btn-sm btn-icon btn-danger btn-remove-stage-row"><i class="ri ri-delete-bin-line"></i></button>
                    </div>
                </td>
            </tr>
        `;
        $('#production-stages-tbody').append(newRow);
        $('#production-stages-tbody tr:last-child .select2').select2({ width: '100%' });
        $('#production-stages-tbody tr:last-child .flatpickr-date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });
        renumberStageRows();
    });

    $(document).on('click', '.btn-remove-stage-row', function() {
        if ($('#production-stages-tbody tr').length > 1) {
            $(this).closest('tr').remove();
            renumberStageRows();
        } else {
            Swal.fire({ icon: 'info', text: 'At least one production stage is required.' });
        }
    });

    // Handle Assign Task button click
    $(document).on('click', '.assign-task-btn', function(e) {
        e.preventDefault();
        let $row = $(this).closest('tr');
        let stageId = $(this).data('stage-id') || $row.find('.stage-select').val();
        let jobCardId = '{{ $jobCard ? $jobCard->id : "" }}';

        if (!stageId) {
            Swal.fire({ icon: 'warning', text: 'Please select a Stage before assigning.' });
            return;
        }

        let baseUrl = '{{ route("task_management.add") }}';
        let params = new URLSearchParams({
            job_card_id: jobCardId,
            stage_id: stageId,
            is_additional: 1,
            batch_id: '{{ $editingBatch ? $editingBatch->id : "" }}'
        });
        window.open(baseUrl + '?' + params.toString(), '_blank');
    });

    function updateLayMarkRowNumbers(fIdx) {
        $(`#consumption-lay-tbody-${fIdx} tr.lay-mark-row`).each(function(i) {
            $(this).find('.mark-no').text(i + 1);
            $(this).find('.select-lay-sizes').attr('name', `fabrics[${fIdx}][lay_marks][${i}][sizes][]`);
            $(this).find('.select-lay-sleeve').attr('name', `fabrics[${fIdx}][lay_marks][${i}][sleeve]`);
            $(this).find('.input-lay-meter').attr('name', `fabrics[${fIdx}][lay_marks][${i}][meter]`);
            $(this).find('.input-no-of-lay').attr('name', `fabrics[${fIdx}][lay_marks][${i}][no_of_lay]`);
        });
    }

    function addLayMarkRowToFabric(fIdx) {
        const $tbody = $(`#consumption-lay-tbody-${fIdx}`);
        const markNum = $tbody.find('tr.lay-mark-row').length + 1;
        const markIdx = markNum - 1;

        let copiedSizes = [];
        let copiedSleeve = 'F/S';
        let copiedMeter = '';
        let copiedNoOfLay = '';
        if (fIdx !== 0) {
            const $row0 = $('#consumption-lay-tbody-0 tr.lay-mark-row').eq(markIdx);
            if ($row0.length) {
                copiedSizes = $row0.find('.select-lay-sizes').val() || [];
                copiedSleeve = $row0.find('.select-lay-sleeve').val() || 'F/S';
                copiedMeter = $row0.find('.input-lay-meter').val() || '';
                copiedNoOfLay = $row0.find('.input-no-of-lay').val() || '';
            }
        }

        let sizeOptionsHtml = '';
        availableSizes.forEach(s => {
            const isSelected = copiedSizes.includes(String(s)) ? 'selected' : '';
            sizeOptionsHtml += `<option value="${s}" ${isSelected}>${s}</option>`;
        });

        const newLayRow = `
            <tr class="lay-mark-row" data-fabric-index="${fIdx}">
                <td class="fw-bold mark-no">${markNum}</td>
                <td>
                    <select class="form-select form-select-sm select2-multi-sizes select-lay-sizes" name="fabrics[${fIdx}][lay_marks][${markIdx}][sizes][]" multiple="multiple" style="width: 100%;">
                        ${sizeOptionsHtml}
                    </select>
                </td>
                <td>
                    <select class="form-select form-select-sm select-lay-sleeve select2" name="fabrics[${fIdx}][lay_marks][${markIdx}][sleeve]">
                        <option value="F/S" ${copiedSleeve === 'F/S' ? 'selected' : ''}>F/S</option>
                        <option value="H/S" ${copiedSleeve === 'H/S' ? 'selected' : ''}>H/S</option>
                    </select>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" class="form-control form-control-sm text-center input-lay-meter" name="fabrics[${fIdx}][lay_marks][${markIdx}][meter]" placeholder="0.00" value="${copiedMeter}">
                </td>
                <td>
                    <input type="number" step="1" min="0" class="form-control form-control-sm text-center input-no-of-lay" name="fabrics[${fIdx}][lay_marks][${markIdx}][no_of_lay]" placeholder="0" value="${copiedNoOfLay}">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-icon btn-danger remove-lay-mark"><i class="ri ri-delete-bin-line"></i></button>
                </td>
            </tr>
        `;
        $tbody.append(newLayRow);
        $tbody.find('tr:last-child .select2').select2({ width: '100%' });
        $tbody.find('tr:last-child .select2-multi-sizes').select2({ width: '100%', placeholder: 'Select Sizes' });
        updateLayMarkRowNumbers(fIdx);
        recalcFabricMeters(fIdx);
        syncCuttingRatioFromLayMarks(fIdx);
    }

    // Add Lay Mark Row for clicked fabric
    $(document).on('click', '.add-lay-mark-btn', function() {
        const fIdx = $(this).data('fabric-index');
        addLayMarkRowToFabric(fIdx);
    });

    // Remove Lay Mark Row for clicked fabric
    $(document).on('click', '.remove-lay-mark', function() {
        const $row = $(this).closest('tr');
        const $containerTable = $(this).closest('.lay-mark-table');
        const fabricIndex = $containerTable.data('fabric-index');
        const $tbody = $row.closest('tbody');

        if ($tbody.find('tr.lay-mark-row').length > 1) {
            $row.remove();
            updateLayMarkRowNumbers(fabricIndex);
            recalcFabricMeters(fabricIndex);
            syncCuttingRatioFromLayMarks(fabricIndex);
        }
    });

    // Auto-Sync from Art 0 (First Fabric) to All Other Fabrics
    $(document).on('input change', '.lay-mark-table[id$="-art-0"] input, .lay-mark-table[id$="-art-0"] select', function() {
        if (isSyncing) return;
        isSyncing = true;
        try {
            const $el = $(this);
            const $row = $el.closest('tr');
            const rowIndex = $row.index();
            const name = $el.attr('name');
            if (!name) return;

            const suffix = name.replace(/^fabrics\[0\]\[lay_marks\]\[\d+\]/, '');

            $('.lay-mark-table').each(function() {
                const fabricIndex = $(this).data('fabric-index');
                if (fabricIndex === 0) return;

                const targetName = `fabrics[${fabricIndex}][lay_marks][${rowIndex}]${suffix}`;
                const $target = $(`[name="${targetName}"]`);

                if ($target.length) {
                    if ($el.is('select[multiple]')) {
                        const val = $el.val();
                        $target.val(val).trigger('change.select2');
                    } else if ($el.is('select')) {
                        const val = $el.val();
                        $target.val(val).trigger('change.select2');
                    } else {
                        $target.val($el.val());
                    }
                }

                recalcFabricMeters(fabricIndex);
                syncCuttingRatioFromLayMarks(fabricIndex);
            });
        } finally {
            isSyncing = false;
        }
    });

    // Calculate Fabric Meters from Lay Mark Rows for a specific Fabric
    function recalcFabricMeters(fIdx) {
        let totalMtrs = 0;
        $(`#consumption-lay-tbody-${fIdx} tr.lay-mark-row`).each(function() {
            const meter = parseFloat($(this).find('.input-lay-meter').val()) || 0;
            const lays = parseFloat($(this).find('.input-no-of-lay').val()) || 0;
            if (meter > 0 && lays > 0) {
                totalMtrs += (meter * lays);
            }
        });
        $(`#issued_meters_${fIdx}`).val(totalMtrs.toFixed(2));
        $(`#matrix_need_caption_${fIdx}`).text('Matrix Need: ' + totalMtrs.toFixed(2) + ' MTR');
    }

    $(document).on('click', '.btn-recalc-fabric-meters', function() {
        const fIdx = $(this).data('fabric-index');
        recalcFabricMeters(fIdx);
    });

    // Auto-Sync Cutting Size Ratio Matrix from Lay Marks for a specific Fabric
    function syncCuttingRatioFromLayMarks(fIdx) {
        const sizeCounts = {
            'fs': {},
            'hs': {}
        };

        availableSizes.forEach(s => {
            sizeCounts.fs[s] = 0;
            sizeCounts.hs[s] = 0;
        });

        let hasAnyLay = false;
        $(`#consumption-lay-tbody-${fIdx} tr.lay-mark-row`).each(function() {
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

        // Populate this fabric's row in the Article Quantity Matrix
        if (hasAnyLay) {
            availableSizes.forEach(s => {
                const fsQty = sizeCounts.fs[s] > 0 ? sizeCounts.fs[s] : '';
                const hsQty = sizeCounts.hs[s] > 0 ? sizeCounts.hs[s] : '';

                $(`.input-size-fs[data-fabric-index="${fIdx}"][data-size="${s}"]`).val(fsQty);
                $(`.input-size-hs[data-fabric-index="${fIdx}"][data-size="${s}"]`).val(hsQty);
            });
        }

        recalcMatrixTotals();
    }

    // Events that trigger Lay Mark calculation per fabric
    $(document).on('input change', '.input-lay-meter', function() {
        const fIdx = $(this).closest('.lay-mark-table').data('fabric-index');
        if (fIdx !== undefined) recalcFabricMeters(fIdx);
    });
    $(document).on('input change', '.input-no-of-lay', function() {
        const fIdx = $(this).closest('.lay-mark-table').data('fabric-index');
        if (fIdx !== undefined) {
            recalcFabricMeters(fIdx);
            syncCuttingRatioFromLayMarks(fIdx);
        }
    });
    $(document).on('change', '.select-lay-sizes, .select-lay-sleeve', function() {
        const fIdx = $(this).closest('.lay-mark-table').data('fabric-index');
        if (fIdx !== undefined) {
            syncCuttingRatioFromLayMarks(fIdx);
        }
    });

    // Calculate Matrix Totals across all Fabrics & Sizes
    function recalcMatrixTotals() {
        let grandTotalExtra = 0;
        const colTotalsFs = {};
        const colTotalsHs = {};

        availableSizes.forEach(s => {
            colTotalsFs[s] = 0;
            colTotalsHs[s] = 0;
        });

        // Loop over each fabric row in the matrix
        $('.matrix-art-row').each(function() {
            const fIdx = $(this).data('fabric-index');
            let rowTotal = 0;

            availableSizes.forEach(s => {
                const fsVal = parseInt($(`.input-size-fs[data-fabric-index="${fIdx}"][data-size="${s}"]`).val()) || 0;
                const hsVal = parseInt($(`.input-size-hs[data-fabric-index="${fIdx}"][data-size="${s}"]`).val()) || 0;
                const cellTot = fsVal + hsVal;
                rowTotal += cellTot;

                colTotalsFs[s] += fsVal;
                colTotalsHs[s] += hsVal;
            });

            $(`#row_total_${fIdx}`).val(rowTotal);
            grandTotalExtra += rowTotal;
        });

        // Update column totals
        availableSizes.forEach(s => {
            $(`.col-size-total-fs[data-col="fs-${s}"]`).text(colTotalsFs[s]);
            $(`.col-size-total-hs[data-col="hs-${s}"]`).text(colTotalsHs[s]);
        });

        // Update Grand Totals
        $('#grand_extra_matrix_total').text(grandTotalExtra);
        $('#badgeExtraPiecesCount').text('Additional Qty: ' + grandTotalExtra + ' pcs');

        const commonTotal = baseTotal + grandTotalExtra;
        $('#lblSummaryExtraQty').html('+' + grandTotalExtra + ' <small class="fs-6 text-muted">pcs</small>');
        $('#lblSummaryCommonTotal').html(commonTotal.toLocaleString() + ' <small class="fs-6 text-muted">pcs</small>');
        $('#lblBottomCommonTotal').text(commonTotal.toLocaleString() + ' pcs');
        $('#lblBottomExtra').text('+' + grandTotalExtra);
    }

    $(document).on('input change', '.input-size-fs, .input-size-hs', recalcMatrixTotals);

    // Initial calculation on page load
    recalcMatrixTotals();

    // Helper to display inline validation error message
    function showFieldError($elem, message = 'This field is required') {
        $elem.addClass('is-invalid');
        const $parent = $elem.closest('td, .form-floating, .form-group');
        $parent.find('.validation-error').remove();
        $parent.append(`<div class="text-danger small validation-error fw-bold mt-1">${message}</div>`);
    }

    // Clear validation errors on interaction
    $(document).on('input change', 'input, select', function() {
        $(this).removeClass('is-invalid');
        $(this).closest('td, .form-floating, .form-group').find('.validation-error').remove();
        $('#matrix-validation-error').remove();
    });

    // Form Submission via AJAX
    $('#formAdditionalQty').on('submit', function(e) {
        e.preventDefault();
        $('.validation-error').remove();
        $('.is-invalid').removeClass('is-invalid');

        let hasError = false;

        // 1. Validate Production Stages
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

        // 2. Validate Cutting Size Ratio Matrix Total
        let totalExtra = 0;
        $('.matrix-art-row').each(function() {
            availableSizes.forEach(s => {
                const fsVal = parseInt($(this).find(`.input-size-fs[data-size="${s}"]`).val()) || 0;
                const hsVal = parseInt($(this).find(`.input-size-hs[data-size="${s}"]`).val()) || 0;
                totalExtra += (fsVal + hsVal);
            });
        });

        let totalMtrsEntered = 0;
        $('.issued-meters-input').each(function() {
            totalMtrsEntered += (parseFloat($(this).val()) || 0);
        });

        if (totalExtra <= 0 && totalMtrsEntered <= 0) {
            $('#cutting-size-matrix-table').after('<div id="matrix-validation-error" class="text-danger small validation-error fw-bold mt-2 text-center">Please enter additional pieces in the Article Quantity Matrix or Fabric Lay Marks</div>');
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
            url: $('#formAdditionalQty').attr('action'),
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
                    window.location.href = res.redirect || '{{ url("job_card_entries") }}';
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: res.message || 'Unable to update additional quantity.',
                        confirmButtonColor: '#d33'
                    });
                    btn.prop('disabled', false).html(origText);
                }
            },
            error: function(xhr) {
                let msg = 'An unexpected error occurred.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    msg = xhr.responseText;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: msg,
                    confirmButtonColor: '#d33'
                });
                btn.prop('disabled', false).html(origText);
            }
        });
    });
});
</script>
@endsection

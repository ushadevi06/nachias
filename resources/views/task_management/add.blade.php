@extends('layouts.common')
@section('title', ($task ? 'Edit' : 'Add') . ' Task Management - ' . env('WEBSITE_NAME'))
@section('content')
    <div class="container-xxl section-padding">   
        <div class="row mb-4">
            <div class="col-12">
                <div class="col-lg-12">
                    @include('flash_messages')
                </div>
                <div class="card border-0 shadow-sm erp-header-card">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <h5 class="mb-0 fw-bold text-primary">{{ isset($task) ? 'Edit Task: ' . $task->task_no : 'New Task Issue' }}</h5>
                                <h3 class="fw-bold mb-1 text-primary d-none">{{ isset($task) ? $task->task_no : $nextTaskNo }}</h3>
                                <p class="text-muted mb-0">
                                    <span class="badge bg-label-secondary px-2 py-1 me-2">Task Execution</span>
                                    <i class="ri ri-arrow-right-s-line"></i>
                                    <span class="ms-2">Job Card: <span class="fw-bold text-dark">{{ $jobCard->job_card_no ?? ($task->job_card_no ?? 'N/A') }}</span></span>
                                    @if(isset($jobCard->job_card_type))
                                        <span class="ms-2">| Type: <span class="badge bg-label-warning fw-bold">{{ strtoupper($jobCard->job_card_type) }}</span></span>
                                    @endif
                                    @if(isset($jobCard->grand_total_qty))
                                        <span class="ms-2">| Total Qty: <span class="badge bg-label-primary fs-6 fw-bold">{{ (int) $jobCard->grand_total_qty }} PCS</span></span>
                                    @endif
                                    @if(isset($stageRemainingPending) && isset($stageTotalPlanned) && $stageTotalPlanned > 0 && $stageRemainingPending > 0)
                                        <span class="ms-2">| Unassigned: <span class="badge bg-label-warning fs-6 fw-bold">{{ (int) $stageRemainingPending }} / {{ (int) $stageTotalPlanned }} PCS</span></span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                                <div class="d-flex flex-wrap justify-content-md-end gap-2">
                                    @if(isset($task))
                                    <button type="button" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-bold" id="btn-view-history" data-task-id="{{ $task->id }}"><i class="ri ri-time-line me-1"></i> History</button>
                                    @if($task->job_card_entry_id && $task->stage_id && isset($stageRemainingPending) && $stageRemainingPending > 0)
                                    <a href="{{ route('task_management.add', ['job_card_id' => $task->job_card_entry_id, 'stage_id' => $task->stage_id]) }}" class="btn btn-success rounded-pill px-3 py-2 fw-bold shadow-sm" target="_blank">
                                        <i class="ri ri-add-circle-line me-1"></i> + Add Next Task ({{ (int)$stageRemainingPending }} PCS)
                                    </a>
                                    @endif
                                    @endif
                                    <span class="badge bg-label-info rounded-pill px-3 py-2 fw-bold">
                                        <i class="ri ri-loader-4-line me-1"></i> {{ $task ? $task->status : '' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Horizontal Tabs Navigation (1 Row, 3 Equal Columns) -->
        <div class="row g-3 mb-4" id="v-pills-tab" role="tablist">
            <div class="col-md-4">
                <button class="nav-link custom-tab-btn active text-start" id="tab-issue" data-bs-toggle="pill" data-bs-target="#content-issue" type="button" role="tab">
                    <div class="section-icon bg-label-primary text-primary me-3 flex-shrink-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="ri ri-task-line fs-4"></i>
                    </div>
                    <div class="overflow-hidden">
                        <span class="d-block fw-bold" style="font-size: 0.95rem;">Task Issue</span>
                        <small class="text-muted font-small d-block text-truncate">Issue materials/tasks</small>
                    </div>
                </button>
            </div>
            <div class="col-md-4">
                <button class="nav-link custom-tab-btn text-start" id="tab-receive" data-bs-toggle="pill" data-bs-target="#content-receive" type="button" role="tab">
                    <div class="section-icon bg-label-success text-success me-3 flex-shrink-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="ri ri-refresh-line fs-4"></i>
                    </div>
                    <div class="overflow-hidden">
                        <span class="d-block fw-bold" style="font-size: 0.95rem;">Status Update</span>
                        <small class="text-muted font-small d-block text-truncate">Employee Progress</small>
                    </div>
                </button>
            </div>
            <div class="col-md-4">
                <button class="nav-link custom-tab-btn text-start" id="tab-adjustment" data-bs-toggle="pill" data-bs-target="#content-adjustment" type="button" role="tab">
                    <div class="section-icon bg-label-warning text-warning me-3 flex-shrink-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="ri ri-equalizer-line fs-4"></i>
                    </div>
                    <div class="overflow-hidden">
                        <span class="d-block fw-bold" style="font-size: 0.95rem;">Task Adjustment</span>
                        <small class="text-muted font-small d-block text-truncate">Corrections & Wastage</small>
                    </div>
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="tab-content sdk-tab-content p-0" id="v-pills-tabContent">
                    <div class="tab-pane fade show active" id="content-issue" role="tabpanel">
                        <form action="{{ $task ? route('task_management.edit', ['id' => $task->id]) : route('task_management.add') }}" method="POST" class="common-form" autocomplete="off">
                            {{-- @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                                @endif
                                @if(session('error'))
                                <div class="alert alert-warning alert-dismissible fade show">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                                @endif 
                                @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                                @endif  --}}
                            @csrf
                            <input type="hidden" name="production_id" value="{{ $production->id ?? ($task->production_id ?? '') }}">
                            <input type="hidden" name="production_no" value="{{ $production->production_no ?? ($task->production_no ?? '') }}">
                            <input type="hidden" name="job_card_entry_id" value="{{ $jobCard->id ?? ($task->job_card_entry_id ?? '') }}">
                            <input type="hidden" name="job_card_no" value="{{ $jobCard->job_card_no ?? ($task->job_card_no ?? '') }}">
                            <input type="hidden" name="is_additional" value="{{ ($isAdditional ?? false) ? 1 : 0 }}">
                            <input type="hidden" name="job_card_fabric_detail_id" value="{{ $batchId ?? ($task->job_card_fabric_detail_id ?? '') }}">
                            <div class="card border-0 shadow-sm section-card">
                                <div class="card-header border-bottom py-3 bg-label-primary bg-opacity-10 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="section-icon bg-primary text-white me-3"><i class="ri ri-upload-2-line"></i></div>
                                        <h5 class="mb-0 fw-bold text-primary d-flex align-items-center gap-2">
                                            {{ isset($task) ? 'Edit Task' : 'New Task Issue' }}
                                            @if($isAdditional ?? false)
                                                <span class="badge bg-warning text-dark fs-6 px-3 py-1">
                                                    <i class="ri ri-add-circle-line me-1"></i> Additional Qty {{ $additionalBatch ? ('(+'.intval($additionalBatch->batch_total_qty ?? $additionalBatch->total_qty).' pcs)') : '' }}
                                                </span>
                                            @endif
                                        </h5>
                                    </div>
                                </div>
                                <div class="card-body pt-4">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" class="form-control" name="task_no" value="{{ $nextTaskNo }}" readonly>
                                                <label>Task Issue ID (Auto)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" class="form-control" value="{{ $jobCard->job_card_no ?? ($task->job_card_no ?? 'N/A') }}" readonly>
                                                <label>Job Card No</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating form-floating-outline">
                                                @php
                                                    $selectedStage = null;
                                                    $reqId = request('stage_id');
                                                    $taskId = isset($task) ? $task->stage_id : null;
                                                    $targetStageId = $reqId ?: $taskId;
                                                    if ($targetStageId && isset($stages)) {
                                                        $selectedStage = $stages->firstWhere('id', $targetStageId) ?? $stages->firstWhere('operation_stage_id', $targetStageId);
                                                    }
                                                    if (!$selectedStage && isset($task) && $task->stage) {
                                                        $selectedStage = $task->stage;
                                                    }
                                                @endphp
                                                @if($selectedStage)
                                                    <input type="text" class="form-control" value="{{ $selectedStage->operationStage->operation_stage_name ?? ($selectedStage->stage ?? 'No Name') }}" readonly>
                                                     <input type="hidden" name="stage_id" id="stage_select" value="{{ $selectedStage->id }}" data-start-date="{{ $selectedStage->start_date ? \Carbon\Carbon::parse($selectedStage->start_date)->format('Y-m-d') : '' }}" data-qty="{{ isset($stageRemainingPending) ? $stageRemainingPending : ($selectedStage->planned_qty ?? 0) }}" data-total-planned-qty="{{ $selectedStage->planned_qty ?? 0 }}" data-already-assigned-qty="{{ $stageAlreadyAssigned ?? 0 }}" data-service-provider-id="{{ $selectedStage->scheduled_to ?? '' }}" data-job-card-service-provider-id="{{ $jobCard->service_provider_id ?? '' }}" data-due-date="{{ $selectedStage->due_date ? \Carbon\Carbon::parse($selectedStage->due_date)->format('Y-m-d') : '' }}" data-operation-stage-id="{{ $selectedStage->operation_stage_id }}" data-services='@json($services)'>
                                                @else
                                                    <input type="text" class="form-control" value="No Stage Selected" readonly>
                                                @endif
                                                <label>Stage *</label>
                                            </div>
                                            @error('stage_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>

                                        @if($selectedStage && ($selectedStage->start_date || $selectedStage->due_date))
                                        <div class="col-md-6">
                                            <div class="card border-0 bg-light">
                                                <div class="card-body p-3">
                                                    <div class="row g-2">
                                                        @if($selectedStage->start_date)
                                                        <div class="col-6">
                                                            <small class="text-muted d-block mb-1"><i class="ri ri-calendar-event-line me-1"></i>START</small>
                                                            <span class="badge bg-label-primary fs-6 fw-bold">{{ \Carbon\Carbon::parse($selectedStage->start_date)->format('d-m-Y') }}</span>
                                                        </div>
                                                        @endif
                                                        @if($selectedStage->due_date)
                                                        <div class="col-6">
                                                            <small class="text-muted d-block mb-1"><i class="ri ri-calendar-check-line me-1"></i>END</small>
                                                            <span class="badge bg-label-success fs-6 fw-bold">{{ \Carbon\Carbon::parse($selectedStage->due_date)->format('d-m-Y') }}</span>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        @php
                                            $stageStartFormatted = ($selectedStage && $selectedStage->start_date) ? \Carbon\Carbon::parse($selectedStage->start_date)->format('d-m-Y') : null;
                                            $stageDueFormatted = ($selectedStage && $selectedStage->due_date) ? \Carbon\Carbon::parse($selectedStage->due_date)->format('d-m-Y') : null;
                                            $defaultPlanDate = isset($task->issue_date) ? \Carbon\Carbon::parse($task->issue_date)->format('d-m-Y') : (old('plan_date') ?? '');
                                        @endphp
                                        <div class="col-md-6">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" class="form-control flatpickr-plan-date" name="plan_date" id="plan_date" value="{{ $defaultPlanDate }}" data-start-date="{{ $stageStartFormatted ?? '' }}" data-due-date="{{ $stageDueFormatted ?? '' }}" placeholder="DD-MM-YYYY" autocomplete="off">
                                                <label>Plan Date (Task Date) *</label>
                                            </div>
                                            @error('plan_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <div class="card border-0 bg-light h-100">
                                                <div class="card-body p-3">
                                                    <div class="row g-2 text-center text-sm-start align-items-center">
                                                        @php 
                                                            $totPlanned = (int)($stageTotalPlanned ?? ($selectedStage->planned_qty ?? ($jobCard->grand_total_qty ?? 0)));
                                                            $achievedVal = (int)($stageTotalAchieved ?? 0);
                                                            $pendingToComplete = max(0, $totPlanned - $achievedVal);
                                                        @endphp
                                                        <div class="col-4">
                                                            <small class="text-muted d-block mb-1 text-truncate"><i class="ri ri-focus-2-line me-1"></i>STAGE TARGET</small>
                                                            <span class="badge bg-label-secondary fs-6 fw-bold">{{ $totPlanned }} PCS</span>
                                                        </div>
                                                        <div class="col-4">
                                                            <small class="text-muted d-block mb-1 text-truncate"><i class="ri ri-checkbox-circle-line me-1"></i>STAGE ACHIEVED</small>
                                                            <span class="badge bg-label-{{ $achievedVal >= $totPlanned && $totPlanned > 0 ? 'success' : ($achievedVal > 0 ? 'info' : 'secondary') }} fs-6 fw-bold">{{ $achievedVal }} PCS</span>
                                                        </div>
                                                        <div class="col-4">
                                                            <small class="text-muted d-block mb-1 text-truncate"><i class="ri ri-time-line me-1"></i>STAGE PENDING</small>
                                                            <span class="badge bg-label-{{ $pendingToComplete > 0 ? 'warning' : 'success' }} fs-6 fw-bold">{{ $pendingToComplete }} PCS</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="table-responsive border rounded bg-white mt-3 shadow-sm">
                                                <table class="table table-bordered table-hover align-middle mb-0" id="assignments-table">
                                                    <thead class="table-light text-nowrap">
                                                        <tr class="text-secondary small fw-bold">
                                                            <th style="width: 45px;" class="text-center">#</th>
                                                            <th style="min-width: 170px;">Service <span class="text-danger">*</span></th>
                                                            <th style="min-width: 200px;">Issued To <span class="text-danger">*</span></th>
                                                            <th style="min-width: 140px;">Plan / Issue Date <span class="text-danger">*</span></th>
                                                            <th style="min-width: 95px; width: 95px;" class="text-center">Hrs</th>
                                                            <th style="min-width: 110px; width: 110px;" class="text-center">Qty <span class="text-danger">*</span></th>
                                                            <th style="min-width: 140px;">Status</th>
                                                            <th style="min-width: 160px;">Remarks</th>
                                                            <th style="width: 90px; min-width: 90px;" class="text-center">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="assignment-cards-container">
                                                        @php 
                                                            $assignmentsArr = [];
                                                            if (old('assignments')) {
                                                                $assignmentsArr = old('assignments');
                                                            } elseif (isset($task) && $task->assignments->count() > 0) {
                                                                $assignmentsArr = $task->assignments;
                                                            } elseif (!isset($task) && isset($services) && count($services) > 0) {
                                                                $isStitchingAssemble = ($selectedStage->operationStage->operation_stage_name ?? '') === 'STITCHING ASSEMBLE';
                                                                $multiplier = $isStitchingAssemble ? 2 : 1;
                                                                foreach ($services as $svc) {
                                                                    if ((float)($svc['qty'] ?? 0) <= 0) {
                                                                        continue;
                                                                    }
                                                                    for ($i = 0; $i < $multiplier; $i++) {
                                                                        $assignmentsArr[] = [
                                                                            'service_id' => $svc['id'],
                                                                            'issue_qty' => $i === 0 ? $svc['qty'] : '',
                                                                            'status' => 'Open'
                                                                        ];
                                                                    }
                                                                }
                                                                if (empty($assignmentsArr)) {
                                                                    $assignmentsArr = [[]];
                                                                }
                                                            } elseif (!isset($task)) {
                                                                $assignmentsArr = [[]];
                                                            }
                                                        @endphp

                                                        @foreach($assignmentsArr as $index => $assign)
                                                            @php 
                                                                $assign = (object) $assign;
                                                                $emp_id_val = $assign->issued_to ?? '';
                                                                $employee_name = '';
                                                                $employee_emp_id = '';

                                                                if (isset($assign->assignee)) {
                                                                    $employee_name = $assign->assignee->name;
                                                                    $employee_emp_id = $assign->assignee->emp_id ?? '';
                                                                } elseif ($emp_id_val) {
                                                                    $oldUser = \App\Models\User::find($emp_id_val);
                                                                    if ($oldUser) {
                                                                        $employee_name = $oldUser->name;
                                                                        $employee_emp_id = $oldUser->emp_id ?? '';
                                                                    }
                                                                }

                                                                $employee_display = $employee_name ?: 'Select Employee';
                                                                if ($employee_emp_id) {
                                                                    $employee_display .= ' (' . $employee_emp_id . ')';
                                                                }
                                                                $service_id = $assign->service_id ?? '';
                                                                $issue_date = isset($assign->issue_date) ? \Carbon\Carbon::parse($assign->issue_date)->format('d-m-Y') : '';
                                                                $due_date = isset($assign->due_date) ? \Carbon\Carbon::parse($assign->due_date)->format('d-m-Y') : '';
                                                                $status = $assign->status ?? 'Open';
                                                                if ($status === 'Completed' || (isset($assign->issue_qty) && (float)$assign->issue_qty > 0 && ((float)($assign->completed_qty ?? 0) + (float)($assign->wastage_qty ?? 0)) >= (float)$assign->issue_qty)) {
                                                                    $status = 'Completed';
                                                                } elseif (((float)($assign->completed_qty ?? 0) + (float)($assign->wastage_qty ?? 0)) > 0) {
                                                                    $status = 'In Progress';
                                                                }

                                                            @endphp
                                                            <tr class="assignment-row">
                                                                <td class="text-center fw-bold card-badge">#{{ $index + 1 }}</td>
                                                                <td>
                                                                    <input type="hidden" name="assignments[{{ $index }}][id]" value="{{ $assign->id ?? '' }}">
                                                                    <select class="form-select select2 services-select form-select-sm" name="assignments[{{ $index }}][service_id]" data-placeholder="Select Service" data-selected="{{ $service_id }}">
                                                                        <option value="">Select Service</option>
                                                                        @if(isset($services) && count($services) > 0)
                                                                            @foreach($services as $svc)
                                                                                @php
                                                                                    $sTarget = (int)($svc['total_planned_qty'] ?? $svc['qty'] ?? 0);
                                                                                    $sAchieved = (int)($svc['achieved_qty'] ?? 0);
                                                                                    $sPending = (int)($svc['stage_pending_qty'] ?? max(0, $sTarget - $sAchieved));
                                                                                @endphp
                                                                                <option value="{{ $svc['id'] }}" data-qty="{{ $svc['qty'] }}" data-target="{{ $sTarget }}" data-achieved="{{ $sAchieved }}" data-pending="{{ $sPending }}" {{ $service_id == $svc['id'] ? 'selected' : '' }}>{{ $svc['name'] }}</option>
                                                                            @endforeach
                                                                        @elseif($service_id)
                                                                            @php
                                                                                $selectedService = \App\Models\ProductionService::find($service_id);
                                                                            @endphp
                                                                            <option value="{{ $service_id }}" selected>
                                                                                {{ $selectedService ? $selectedService->service_name : 'Service ID: ' . $service_id }}
                                                                            </option>
                                                                        @endif
                                                                    </select>
                                                                    @php
                                                                        $matchedSvc = isset($services) ? collect($services)->firstWhere('id', (int)$service_id) : null;
                                                                        $mTarget = $matchedSvc ? (int)($matchedSvc['total_planned_qty'] ?? $matchedSvc['qty'] ?? 0) : 0;
                                                                        $mAchieved = $matchedSvc ? (int)($matchedSvc['achieved_qty'] ?? 0) : 0;
                                                                        $mPending = $matchedSvc ? (int)($matchedSvc['stage_pending_qty'] ?? max(0, $mTarget - $mAchieved)) : 0;
                                                                    @endphp
                                                                    <div class="service-metrics-badge mt-1 d-flex flex-wrap gap-1" style="font-size: 10px; {{ $matchedSvc ? 'display: flex !important;' : 'display: none !important;' }}">
                                                                        <span class="badge bg-label-secondary py-0 px-1 border" style="font-size: 10px;" title="Stage Planned Target">Target: <b class="badge-target">{{ $mTarget }}</b></span>
                                                                        <span class="badge bg-label-success py-0 px-1 border" style="font-size: 10px;" title="Already Completed/Assigned in Stage">Achieved: <b class="badge-achieved">{{ $mAchieved }}</b></span>
                                                                        <span class="badge bg-label-warning py-0 px-1 border" style="font-size: 10px;" title="Remaining Pending Quantity">Pending: <b class="badge-pending">{{ $mPending }}</b></span>
                                                                    </div>
                                                                    @error("assignments.$index.service_id") <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                                                                </td>
                                                                <td>
                                                                    <select class="form-select select2 employee-select form-select-sm" name="assignments[{{ $index }}][issued_to]" data-placeholder="Select Employee">
                                                                        <option value="">Select Employee</option>
                                                                        @if(isset($users) && count($users) > 0)
                                                                            @foreach($users as $user)
                                                                                <option value="{{ $user->id }}" data-emp-id="{{ $user->emp_id ?? '' }}" {{ $emp_id_val == $user->id ? 'selected' : '' }}>
                                                                                    {{ $user->name }}{{ $user->emp_id ? ' (' . $user->emp_id . ')' : '' }}
                                                                                </option>
                                                                            @endforeach
                                                                        @elseif($emp_id_val)
                                                                            <option value="{{ $emp_id_val }}" selected>{{ $employee_display }}</option>
                                                                        @endif
                                                                    </select>
                                                                    <input type="hidden" name="assignments[{{ $index }}][emp_id]" class="employee-id-input" value="{{ $employee_emp_id }}">
                                                                    @error("assignments.$index.issued_to") <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm flatpickr-assignment issue-date {{ $status == 'Completed' ? 'bg-light' : '' }}" name="assignments[{{ $index }}][issue_date]" value="{{ $issue_date }}" placeholder="DD-MM-YYYY" autocomplete="off" {{ $status == 'Completed' ? 'readonly style=pointer-events:none;background-color:#f8f9fa;' : '' }}>
                                                                    @error("assignments.$index.issue_date") <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                                                                </td>
                                                                <td>
                                                                    <input type="number" step="0.01" min="0" class="form-control form-control-sm text-center total-hrs" name="assignments[{{ $index }}][total_hrs]" value="{{ $assign->total_hrs ?? '' }}" placeholder="0.00">
                                                                </td>
                                                                <td>
                                                                    <input type="number" step="1" min="0" class="form-control form-control-sm text-center qty-input" name="assignments[{{ $index }}][issue_qty]" value="{{ $assign->issue_qty ?? '' }}" placeholder="Qty">
                                                                    @error("assignments.$index.issue_qty") <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                                                                </td>
                                                                <td>
                                                                    <select class="form-select select2 status-select-row form-select-sm" name="assignments[{{ $index }}][status_display]" disabled>
                                                                        <option value="Pending Assignment" {{ $status == 'Pending Assignment' ? 'selected' : '' }}>Pending Assignment</option>
                                                                        <option value="Open" {{ $status == 'Open' ? 'selected' : '' }}>Open</option>
                                                                        <option value="In Progress" {{ $status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                                                        <option value="Completed" {{ $status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                                                    </select>
                                                                    <input type="hidden" name="assignments[{{ $index }}][status]" value="{{ $status }}">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm" name="assignments[{{ $index }}][remarks]" value="{{ $assign->remarks ?? '' }}" placeholder="Remarks">
                                                                </td>
                                                                <td class="text-center text-nowrap">
                                                                    @php
                                                                        $matchedSvc = isset($services) ? collect($services)->firstWhere('id', (int)$service_id) : null;
                                                                        $sTarget = $matchedSvc ? (float)($matchedSvc['total_planned_qty'] ?? $matchedSvc['qty'] ?? 0) : 0;
                                                                        $sAchieved = $matchedSvc ? (float)($matchedSvc['achieved_qty'] ?? 0) : 0;
                                                                        $sPending = $matchedSvc ? (float)($matchedSvc['stage_pending_qty'] ?? max(0, $sTarget - $sAchieved)) : 0;
                                                                        $showAddBtn = $service_id && ($sPending > 0);
                                                                    @endphp
                                                                    <button type="button" class="btn btn-icon btn-outline-primary btn-sm add-row-for-service me-1" title="Add Another Row for this Service" style="{{ $showAddBtn ? '' : 'display: none;' }}">
                                                                        <i class="ri ri-add-line"></i>
                                                                    </button>
                                                                    @if($status == 'Completed')
                                                                        <button type="button" class="btn btn-icon btn-sm btn-outline-secondary disabled" disabled style="opacity: 0.35; cursor: not-allowed; pointer-events: none;" title="Completed batch cannot be deleted">
                                                                            <i class="ri ri-delete-bin-line"></i>
                                                                        </button>
                                                                    @else
                                                                        <button type="button" class="btn btn-icon btn-outline-danger btn-sm remove-assignment-row" title="Delete Row">
                                                                            <i class="ri ri-delete-bin-line"></i>
                                                                        </button>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="mt-3">
                                                <button type="button" class="btn btn-outline-primary btn-sm fw-bold px-3 py-2" id="add-assignment-row">
                                                    <i class="ri ri-add-line fs-5 align-middle me-1"></i> Add Another Row
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating form-floating-outline">
                                                <select class="select2 form-select" name="issued_by" id="issued_by" data-placeholder="Select Supervisor">
                                                    <option value="">Select Supervisor</option>
                                                    @if(isset($supervisors) && $supervisors->count() > 0)
                                                        @foreach($supervisors as $supervisor)
                                                            <option value="{{ $supervisor->id }}" {{ old('issued_by', $task->issued_by ?? '') == $supervisor->id ? 'selected' : '' }}>
                                                                {{ $supervisor->name }} {{ $supervisor->emp_id ? '('.$supervisor->emp_id.')' : '' }}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        <option value="" disabled>No Supervisors Available.</option>
                                                    @endif
                                                </select>
                                                <label>Issued By *</label>
                                            </div>
                                            @error('issued_by') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating form-floating-outline">
                                                <select class="select2 form-select status-select @error('status') is-invalid @enderror" name="status" id="status" data-placeholder="Select or enter status">
                                                    @foreach($allStatuses as $statusOption)
                                                        <option value="{{ $statusOption }}" {{ old('status', $task->status ?? 'Planned') == $statusOption ? 'selected' : '' }}>{{ $statusOption }}</option>
                                                    @endforeach
                                                </select>
                                                <label>Status *</label>
                                            </div>
                                            @error('status') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-floating form-floating-outline">
                                                <textarea class="form-control" name="remarks" style="height: 80px;">{{ old('remarks', $task->remarks ?? '') }}</textarea>
                                                <label>Remarks</label>
                                            </div>
                                        </div>
                                        <div class="col-12 text-end">
                                            <button type="submit" class="btn btn-primary fw-bold px-4">
                                                <i class="ri ri-save-line me-1"></i> Save Issue
                                            </button>
                                            <a href="{{ url('task_management') }}" class="btn btn-secondary">Cancel</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="content-receive" role="tabpanel">
                        <form action="{{ route('task_management.update_progress') }}" method="POST" class="common-form" autocomplete="off" novalidate>
                            @csrf
                            <input type="hidden" name="task_id" value="{{ $task->id ?? '' }}">
                            <div class="card border-0 shadow-sm section-card">
                                <div class="card-header border-bottom py-3 bg-label-success bg-opacity-10">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="section-icon bg-success text-white me-3"><i class="ri ri-timer-flash-line"></i></div>
                                            <h5 class="mb-0 fw-bold text-success">Task Status Update</h5>
                                        </div>
                                        {{-- <div class="text-end">
                                            <small class="text-muted d-block">Deadline Date</small>
                                            <span class="fw-bold fs-5 {{ ($task->due_date && \Carbon\Carbon::parse($task->due_date)->isPast() && $task->status != 'Completed') ? 'text-danger' : 'text-primary' }}">
                                                {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d-m-Y') : '' }}
                                            </span>
                                        </div> --}}
                                    </div>
                                </div>
                                <div class="card-body pt-4">
                                    @if($task)
                                        @php
                                            $hasOverdueAssignments = false;
                                            if ($task->assignments && $task->assignments->count() > 0) {
                                                foreach ($task->assignments as $assign) {
                                                    if ($assign->status != 'Completed' && $assign->due_date) {
                                                        if (\Carbon\Carbon::parse($assign->due_date)->isBefore(\Carbon\Carbon::today())) {
                                                            $hasOverdueAssignments = true;
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                            $isOverdue = $hasOverdueAssignments;
                                        @endphp
                                        @if($isOverdue)
                                            <div class="alert alert-danger d-flex align-items-center mb-3" role="alert">
                                                <i class="ri ri-alarm-warning-line me-2 fs-5"></i>
                                                <div>
                                                    <strong>Overdue!</strong> Some assignments have passed their due date and are not yet completed.
                                                </div>
                                            </div>
                                        @endif
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0 fw-bold">Employee Assignments</h6>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#bulkUpdateModal">
                                                <i class="ri ri-stack-line me-1"></i> Bulk Update Qty
                                            </button>
                                        </div>

                                        <div class="accordion custom-accordion" id="taskAssignmentsAccordion">
                                            @if($task->assignments && $task->assignments->count() > 0)
                                                @foreach($task->assignments as $index => $assign)
                                                    @php
                                                        $serviceName = $assign->service ? $assign->service->service_name : 'N/A';
                                                        $employeeName = $assign->assignee ? $assign->assignee->name : 'Unknown';
                                                        $progressPercent = ($assign->issue_qty > 0) ? min(100, round(($assign->completed_qty / $assign->issue_qty) * 100)) : 0;

                                                        $dueDate = $assign->due_date ? \Carbon\Carbon::parse($assign->due_date) : null;
                                                        $isOverdue = false;
                                                        if ($dueDate && $assign->status != 'Completed' && $dueDate->lt(\Carbon\Carbon::today())) {
                                                            $isOverdue = true;
                                                        }

                                                        $displayStatus = $assign->status;
                                                        if ((float)$assign->issue_qty > 0 && ((float)$assign->completed_qty + (float)$assign->wastage_qty) >= (float)$assign->issue_qty) {
                                                            $displayStatus = 'Completed';
                                                        } elseif (((float)$assign->completed_qty + (float)$assign->wastage_qty) > 0) {
                                                            $displayStatus = 'In Progress';
                                                        }

                                                        $statusColor = 'secondary';
                                                        if ($displayStatus == 'Completed')
                                                            $statusColor = 'success';
                                                        if ($displayStatus == 'In Progress')
                                                            $statusColor = 'info';

                                                        $qcStatusColor = 'secondary';
                                                        if ($assign->qc_status == 'QC Completed')
                                                            $qcStatusColor = 'success';
                                                        if ($assign->qc_status == 'In QC')
                                                            $qcStatusColor = 'info';
                                                    @endphp
                                                    <div class="accordion-item border rounded mb-3 status-update-row {{ $isOverdue ? 'border-danger' : '' }} {{ $assign->qc_status == 'QC Completed' ? 'border-success' : '' }}" data-service-id="{{ $assign->service_id }}">
                                                        <h2 class="accordion-header" id="heading-{{ $index }}">
                                                            <button class="accordion-button collapsed p-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $index }}">
                                                                <div class="d-flex align-items-center justify-content-between w-100 me-3">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="avatar avatar-sm bg-label-primary rounded me-3">
                                                                            <i class="ri ri-user-settings-line fs-4"></i>
                                                                        </div>
                                                                        <div>
                                                                            <h6 class="mb-0 fw-bold">{{ $employeeName }}</h6>
                                                                            <small class="text-muted">{{ $serviceName }}</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        @if($assign->due_date)
                                                                        <span class="badge {{ $isOverdue ? 'bg-label-danger' : 'bg-label-primary' }} rounded-pill font-small">
                                                                            <i class="ri ri-calendar-todo-line me-1"></i>{{ $assign->due_date ? \Carbon\Carbon::parse($assign->due_date)->format('d-m-Y') : 'N/A' }}
                                                                        </span>
                                                                        @endif
                                                                        <span class="badge bg-label-{{ $statusColor }} border-{{ $statusColor }} fw-bold row-status-badge font-small">{{ $displayStatus }}</span>
                                                                        @if(auth()->user()->hasRole('QUALITY CHECKER') || auth()->user()->id == 1)
                                                                            <span class="badge bg-label-{{ $qcStatusColor }} border-{{ $qcStatusColor }} fw-bold row-qc-status-badge font-small">{{ $assign->qc_status ?? 'Pending' }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapse-{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $index }}" data-bs-parent="#taskAssignmentsAccordion">
                                                            <div class="accordion-body bg-light-subtle border-top p-4">
                                                                <input type="hidden" name="assignments[{{ $index }}][id]" value="{{ $assign->id }}">
                                                                <div class="row g-4">
                                                                    <div class="col-md-6 border-end">
                                                                        <div class="d-flex align-items-center mb-3">
                                                                            <div class="section-icon bg-success bg-opacity-10 text-success me-2">
                                                                                <i class="ri ri-settings-3-line"></i>
                                                                            </div>
                                                                            <h6 class="mb-0 fw-bold text-uppercase">Production Tracking</h6>
                                                                        </div>
                                                                        <div class="row g-3">
                                                                            <div class="col-6">
                                                                                <label class="form-label small fw-bold text-muted">Assigned Qty</label>
                                                                                <div class="form-control form-control-sm bg-light fw-bold row-assigned-qty" data-raw-qty="{{ $assign->issue_qty }}">{{ number_format($assign->issue_qty, 2) }}</div>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <label class="form-label small fw-bold text-muted text-success">Completed Qty</label>
                                                                                <input type="number" step="0.01" min="0" class="form-control form-control-sm border-success fw-bold row-completed-qty" name="assignments[{{ $index }}][completed_qty]" value="{{ $assign->completed_qty }}">
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <label class="form-label small fw-bold text-muted text-danger">Wastage Qty</label>
                                                                                <input type="number" step="0.01" min="0"  class="form-control form-control-sm border-danger fw-bold row-wastage-qty" name="assignments[{{ $index }}][wastage_qty]" value="{{ $assign->wastage_qty }}">
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <label class="form-label small fw-bold text-muted text-warning">In Progress</label>
                                                                                <input type="number" step="0.01" min="0" class="form-control form-control-sm border-warning bg-light fw-bold row-inprogress-qty" name="assignments[{{ $index }}][inprogress_qty]" value="{{ $assign->inprogress_qty }}" readonly tabindex="-1">
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                                    <span class="small fw-bold">Overall Progress</span>
                                                                                    <span class="small fw-bold text-success progress-bar-text">{{ $progressPercent }}%</span>
                                                                                </div>
                                                                                <div class="progress" style="height: 10px; border-radius: 5px;">
                                                                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progressPercent }}%" aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <div class="bg-label-info p-2 rounded d-flex justify-content-between">
                                                                                    <span class="fw-bold small">Current Balance Qty:</span>
                                                                                    <span class="fw-bold balance-display fs-6">{{ (int) max(0, $assign->issue_qty - $assign->completed_qty - $assign->wastage_qty) }}</span>
                                                                                </div>
                                                                                <input type="hidden" class="row-status" name="assignments[{{ $index }}][status]" value="{{ $assign->status }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-6">
                                                                        @if(auth()->user()->hasRole('QUALITY CHECKER') || auth()->user()->id == 1)
                                                                            <div class="d-flex align-items-center mb-3">
                                                                                <div class="section-icon bg-info bg-opacity-10 text-info me-2">
                                                                                    <i class="ri ri-shield-check-line"></i>
                                                                                </div>
                                                                                <h6 class="mb-0 fw-bold text-uppercase">QC Management</h6>
                                                                            </div>

                                                                            <div class="row g-3">
                                                                                <div class="col-6">
                                                                                    <label class="form-label small fw-bold text-muted">QC Checked</label>
                                                                                    <input type="number" step="1" min="0" class="form-control form-control-sm border-info fw-bold row-qc-checked" name="assignments[{{ $index }}][qc_checked_qty]" value="{{ (int) ($assign->qc_checked_qty ?? 0) }}">
                                                                                </div>
                                                                                <div class="col-6">
                                                                                    <label class="form-label small fw-bold text-muted">QC Passed</label>
                                                                                    <input type="number" step="1" min="0" class="form-control form-control-sm border-info fw-bold row-qc-passed" name="assignments[{{ $index }}][qc_passed_qty]" value="{{ (int) ($assign->qc_passed_qty ?? 0) }}">
                                                                                </div>
                                                                                <div class="col-6">
                                                                                    <label class="form-label small fw-bold text-muted">QC Rejected</label>
                                                                                    <input type="number" step="1" min="0" class="form-control form-control-sm border-info fw-bold row-qc-rejected bg-light" name="assignments[{{ $index }}][qc_rejected_qty]" value="{{ (int) ($assign->qc_rejected_qty ?? 0) }}" readonly>
                                                                                </div>
                                                                                <div class="col-6">
                                                                                    <label class="form-label small fw-bold text-muted">Current QC Status</label>
                                                                                    <div class="badge-status-container bg-light w-100 p-2 rounded text-center" style="min-height: 31px;">
                                                                                        <span class="badge bg-label-{{ $qcStatusColor }} border-{{ $qcStatusColor }} fw-bold row-qc-status-badge w-100 py-2">{{ $assign->qc_status ?? 'Pending' }}</span>
                                                                                    </div>
                                                                                    <input type="hidden" class="row-qc-status" name="assignments[{{ $index }}][qc_status]" value="{{ $assign->qc_status ?? 'Pending' }}">
                                                                                </div>
                                                                            </div>
                                                                        @else
                                                                            <div class="h-100 d-flex flex-column justify-content-center align-items-center text-muted border rounded bg-white p-4">
                                                                                <i class="ri ri-lock-2-line fs-1 mb-2"></i>
                                                                                <p class="mb-0 fs-6">QC fields are restricted</p>
                                                                                <small>Contact administrator for access</small>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="alert alert-warning text-center">
                                                    <i class="ri ri-error-warning-line me-2"></i> No employees assigned to this task yet.
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-12 text-end mt-4">
                                            <button type="submit" class="btn btn-primary fw-bold px-4">
                                                <i class="ri ri-save-3-line me-1"></i> Update Status
                                            </button>
                                            <a href="{{ url('task_management') }}" class="btn btn-secondary">Cancel</a>

                                        </div>
                                    @else
                                        <div class="alert alert-info">Please create the task first before updating status.</div>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="content-adjustment" role="tabpanel">
                        <form action="{{ $taskAdjustment ? route('task_adjustments.add', $taskAdjustment->id) : route('task_adjustments.add') }}" method="POST" class="common-form" autocomplete="off">
                            @csrf
                            <div class="card border-0 shadow-sm section-card">
                                <div class="card-header border-bottom py-3 bg-label-warning bg-opacity-10">
                                    <div class="d-flex align-items-center">
                                        <div class="section-icon bg-warning text-white me-3"><i class="ri ri-equalizer-line"></i></div>
                                        <h5 class="mb-0 fw-bold text-warning">Inventory Adjustment</h5>
                                    </div>
                                </div>
                                <div class="card-body pt-4">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" class="form-control" name="adjustment_no" value="{{ $nextAdjNo }}" readonly>
                                                <label>Adjustment ID (Auto)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating form-floating-outline">
                                                <input type="hidden" name="task_id" value="{{ $task->id ?? '' }}">
                                                <input type="text" class="form-control" value="{{ $task->task_no ?? '' }} {{ isset($task->stage->operationStage) ? '(' . $task->stage->operationStage->operation_stage_name . ')' : '' }}" readonly>
                                                <label>Link Task Issue ID *</label>
                                            </div>
                                            @error('task_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating form-floating-outline">
                                                <input type="hidden" name="job_card_id" value="{{ $task->production->jobCard->id ?? ($task->job_card_entry_id ?? ($jobCard->id ?? '')) }}">
                                                <input type="text" class="form-control" id="adj_jobcard_ref" value="{{ $task->production->jobCard->job_card_no ?? ($task->job_card_no ?? '') }}" readonly>
                                                <label>Job Card Reference</label>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-6">
                                            <div class="form-floating form-floating-outline">
                                                @php
                                                    $effStageName = $task->stage->operationStage->operation_stage_name ?? $task->stage->stage ?? '';
                                                @endphp
                                                <input type="text" class="form-control" name="affected_stage" id="adj_stage_ref" value="{{ $effStageName ?? '' }}" readonly>
                                                <label>Affected Stage</label>
                                            </div>
                                        </div> --}}
                                        <div class="col-md-6">
                                            <div class="form-floating form-floating-outline">
                                                <select class="select2 form-select @error('approved_by') is-invalid @enderror" name="approved_by" id="approved_by" data-placeholder="Select Supervisor">
                                                    <option value="">Select Supervisor</option>
                                                    @if(isset($supervisors) && $supervisors->count() > 0)
                                                        @foreach($supervisors as $supervisor)
                                                            <option value="{{ $supervisor->id }}" {{ old('approved_by', $taskAdjustment->approved_by ?? '') == $supervisor->id || old('approved_by', $taskAdjustment->approved_by ?? '') == $supervisor->name ? 'selected' : '' }}>
                                                                {{ $supervisor->name }} {{ $supervisor->emp_id ? '('.$supervisor->emp_id.')' : '' }}
                                                            </option>
                                                        @endforeach
                                                    @else
                                                        <option value="" disabled>No Supervisors Available.</option>
                                                    @endif
                                                </select>
                                                <label>Approved By *</label>
                                            </div>
                                            @error('approved_by') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-floating form-floating-outline">
                                                <textarea class="form-control @error('overall_reason') is-invalid @enderror" name="overall_reason" style="height: 60px;" placeholder="Overall reason for this adjustment">{{ old('overall_reason', $taskAdjustment->overall_reason ?? ($taskAdjustment->reason ?? '')) }}</textarea>
                                                <label>Overall Reason *</label>
                                            </div>
                                            @error('overall_reason') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-12">
                                            <div class="table-responsive border rounded">
                                                <table class="table table-sm table-hover" id="adjustment_items_table">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th style="width: 25%;">Service</th>
                                                            <th style="width: 25%;">Material *</th>
                                                            <th style="width: 20%;">Type *</th>
                                                            <th style="width: 12%;">Qty *</th>
                                                            <th style="width: 18%;">Remarks</th>
                                                            <th style="width: 50px; text-align: center;">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                        $adjItems = old('items');
                                                        if (!$adjItems && isset($taskAdjustment) && $taskAdjustment->items) {
                                                            $adjItems = [];
                                                            foreach ($taskAdjustment->items as $idx => $it) {
                                                                $adjItems[$idx] = [
                                                                    'raw_material_id' => $it->raw_material_id,
                                                                    'service_id' => $it->service_id,
                                                                    'adjustment_type' => $it->adjustment_type,
                                                                    'qty' => $it->qty,
                                                                    'remarks' => $it->remarks,
                                                                    'art_no' => $it->art_no,
                                                                    'grn_no' => $it->grn_no,
                                                                    'id' => $it->id
                                                                ];
                                                            }
                                                        }
                                                        if (!$adjItems)
                                                            $adjItems = [['raw_material_id' => '', 'adjustment_type' => 'Loss', 'qty' => '', 'remarks' => '']];
                                                        @endphp
                                                        @foreach($adjItems as $index => $item)
                                                            <tr class="item-row">
                                                                <td>
                                                                    <select class="select2 form-select service-select" name="items[{{ $index }}][service_id]" data-placeholder="Select Service">
                                                                        <option value="">Select Service</option>
                                                                        @if($task && is_array($task->services))
                                                                            @foreach($task->services as $svcId)
                                                                                @php $svc = \App\Models\ProductionService::find($svcId); @endphp
                                                                                @if($svc)
                                                                                    <option value="{{ $svc->id }}" {{ ($item['service_id'] ?? '') == $svc->id ? 'selected' : '' }}>{{ $svc->service_name }}</option>
                                                                                @endif
                                                                            @endforeach
                                                                        @endif
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input type="hidden" name="items[{{ $index }}][art_no]" value="{{ $item['art_no'] ?? '' }}">
                                                                    <input type="hidden" name="items[{{ $index }}][grn_no]" value="{{ $item['grn_no'] ?? '' }}">
                                                                    <select class="select2 form-select material-select" name="items[{{ $index }}][raw_material_id]" data-placeholder="Select Material">
                                                                        <option value="">Select Material</option>
                                                                        @if(isset($item['raw_material_id']) && $item['raw_material_id'])
                                                                            @php $rm = \App\Models\RawMaterial::find($item['raw_material_id']); @endphp
                                                                            @if($rm)
                                                                                <option value="{{ $rm->id }}" selected>{{ $rm->name }} ({{ $rm->code }})</option>
                                                                            @endif
                                                                        @endif
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <select class="form-select select2" name="items[{{ $index }}][adjustment_type]">
                                                                        <option value="Loss" {{ ($item['adjustment_type'] ?? '') == 'Loss' ? 'selected' : '' }}>Loss / Damage (-)</option>
                                                                        <option value="Rework" {{ ($item['adjustment_type'] ?? '') == 'Rework' ? 'selected' : '' }}>Rework (Consum.) (-)</option>
                                                                        <option value="Excess" {{ ($item['adjustment_type'] ?? '') == 'Excess' ? 'selected' : '' }}>Excess Found (+)</option>
                                                                        <option value="Material Return" {{ ($item['adjustment_type'] ?? '') == 'Material Return' ? 'selected' : '' }}>Return to Store (+)</option>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input type="number" step="0.01" min="0" class="form-control" name="items[{{ $index }}][qty]" placeholder="0.00" value="{{ $item['qty'] ?? '' }}">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control" name="items[{{ $index }}][remarks]" placeholder="Remarks" value="{{ $item['remarks'] ?? '' }}">
                                                                </td>
                                                                 <td class="text-center">
                                                                    <div class="d-flex justify-content-center align-items-center gap-1">
                                                                        @if(isset($item['id']))
                                                                            <a href="{{ url('stock_entries') }}?art_no={{ $item['art_no'] ?? '' }}&grn_no={{ $item['grn_no'] ?? '' }}&material={{ $item['raw_material_id'] ?? '' }}" class="btn btn-sm btn-outline-info" title="Stock Adjustment">
                                                                                Stock Adjustment
                                                                            </a>
                                                                        @endif
                                                                        <button type="button" class="btn btn-icon btn-outline-danger btn-sm remove-row"><i class="ri ri-delete-bin-line"></i></button>
                                                                    </div>
                                                                 </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="5">
                                                                <button type="button" class="btn btn-sm btn-outline-primary" id="add_adjustment_row"><i class="ri ri-add-line"></i> Add Material</button>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                            @error('reason') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-12 text-end">
                                            <button type="submit" class="btn btn-warning fw-bold px-4 text-white mt-4">
                                                <i class="ri ri-alert-line me-1"></i> Post Adjustment
                                            </button>
                                            <a href="{{ url('task_management') }}" class="btn btn-secondary mt-4">Cancel</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        {{-- 🛠 ADJUSTMENT HISTORY LOG
                        @if(isset($task) && isset($taskAdjustments) && $taskAdjustments->count() > 0)
                            <div class="card border-0 shadow-sm mt-4 section-card">
                                <div class="card-header border-bottom py-3 bg-label-warning bg-opacity-10 d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 fw-bold text-warning"><i class="ri ri-settings-5-line me-2"></i>Task Adjustment History</h5>
                                    <span class="badge bg-warning">{{ $taskAdjustments->count() }} Adjustments</span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="bg-light extra-small fw-bold text-uppercase">
                                                <tr>
                                                    <th class="ps-3">Adj No / Date</th>
                                                    <th>Material Adjustments</th>
                                                    <th class="text-center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($taskAdjustments as $adj)
                                                    <tr class="border-bottom">
                                                        <td class="ps-3 py-3" style="width: 180px;">
                                                            <div class="fw-bold text-dark">{{ $adj->adjustment_no }}</div>
                                                            <small class="text-muted"><i class="ri ri-calendar-line me-1"></i>{{ $adj->created_at->format('d-m-Y') }}</small>
                                                        </td>
                                                        <td class="py-3">
                                                            <div class="d-flex flex-column gap-2">
                                                                @foreach($adj->items as $item)
                                                                    <div class="bg-light p-2 rounded-2 border-start border-warning border-3">
                                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                                            <span class="fw-bold small text-dark">
                                                                                @if($item->service)
                                                                                    <span class="text-primary">[{{ $item->service->service_name }}]</span> 
                                                                                @endif
                                                                                {{ $item->rawMaterial->name ?? 'N/A' }}
                                                                            </span>
                                                                            <span class="badge bg-label-{{ $item->adjustment_type == 'Excess' ? 'success' : 'danger' }} small">{{ $item->adjustment_type }}</span>
                                                                        </div>
                                                                        <div class="d-flex gap-3 small">
                                                                            <span>Qty: <b>{{ $item->qty }} {{ $item->uom->uom_code ?? '' }}</b></span>
                                                                            @if($item->remarks)
                                                                                <span class="text-muted italic"><i class="ri ri-chat-1-line me-1"></i>{{ $item->remarks }}</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            @if($adj->overall_reason)
                                                                <div class="mt-2 small text-muted italic p-2 bg-light rounded">
                                                                    <i class="ri ri-question-line me-1"></i> Reason: {{ $adj->overall_reason }}
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td class="text-center py-3">
                                                            <span class="badge bg-label-primary rounded-pill">{{ $adj->status }}</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif  --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            var stageMinDate = "{{ $stageStartFormatted ?? '' }}" || null;
            var stageMaxDate = "{{ $stageDueFormatted ?? '' }}" || null;

            var planDefaultDate = "{{ $defaultPlanDate }}";
            const planDatePicker = $("#plan_date").flatpickr({
                dateFormat: "d-m-Y",
                allowInput: true,
                closeOnSelect: true,
                minDate: stageMinDate,
                maxDate: stageMaxDate,
                defaultDate: planDefaultDate ? planDefaultDate : null,
                onChange: function(selectedDates, dateStr, instance) {
                    if (dateStr) {
                        $('#assignment-cards-container .assignment-row').each(function() {
                            var $row = $(this);
                            var rowStatus = $row.find('input[name*="[status]"]').val() || $row.find('.status-select-row').val();
                            if (rowStatus === 'Completed') {
                                return; 
                            }
                            var $rowDate = $row.find('.flatpickr-assignment.issue-date');
                            var fp = $rowDate[0] ? $rowDate[0]._flatpickr : null;
                            if (fp) {
                                fp.setDate(dateStr, true);
                            } else {
                                $rowDate.val(dateStr);
                            }
                        });
                        instance.close();
                    }
                }
            });
            let assignmentIndex = {{ count($assignmentsArr) }};
            let rowIndex = {{ count($adjItems ?? [0]) }};
            let availableMaterials = [];

            function addAssignmentRow(data = {}, $insertAfter = null) {
                var firstRow = $('.assignment-row').first();
                if (!data.issue_date) {
                    data.issue_date = (firstRow.length ? firstRow.find('.flatpickr-assignment.issue-date').val() : '') || $('#plan_date').val() || '';
                }
                if (!data.total_hrs && firstRow.length) {
                    data.total_hrs = firstRow.find('.total-hrs').val();
                }

                let rowHtml = `
                <tr class="assignment-row">
                    <td class="text-center fw-bold card-badge">#${assignmentIndex + 1}</td>
                    <td>
                        <select class="form-select select2 services-select form-select-sm" name="assignments[${assignmentIndex}][service_id]" data-placeholder="Select Service">
                            <option value="">Select Service</option>
                            @if(isset($services) && count($services) > 0)
                                @foreach($services as $svc)
                                    @php
                                        $sTarget = (int)($svc['total_planned_qty'] ?? $svc['qty'] ?? 0);
                                        $sAchieved = (int)($svc['achieved_qty'] ?? 0);
                                        $sPending = (int)($svc['stage_pending_qty'] ?? max(0, $sTarget - $sAchieved));
                                    @endphp
                                    <option value="{{ $svc['id'] }}" data-qty="{{ $svc['qty'] }}" data-target="{{ $sTarget }}" data-achieved="{{ $sAchieved }}" data-pending="{{ $sPending }}" ${data.service_id == {{ $svc['id'] }} ? 'selected' : ''}>{{ $svc['name'] }}</option>
                                @endforeach
                            @endif
                        </select>
                        <div class="service-metrics-badge mt-1 d-flex flex-wrap gap-1" style="font-size: 10px; display: none;">
                            <span class="badge bg-label-secondary py-0 px-1 border" style="font-size: 10px;" title="Stage Planned Target">Target: <b class="badge-target">0</b></span>
                            <span class="badge bg-label-success py-0 px-1 border" style="font-size: 10px;" title="Already Completed/Assigned in Stage">Achieved: <b class="badge-achieved">0</b></span>
                            <span class="badge bg-label-warning py-0 px-1 border" style="font-size: 10px;" title="Remaining Pending Quantity">Pending: <b class="badge-pending">0</b></span>
                        </div>
                    </td>
                    <td>
                        <select class="form-select select2 employee-select form-select-sm" name="assignments[${assignmentIndex}][issued_to]" data-placeholder="Select Employee">
                            <option value="">Select Employee</option>
                            @if(isset($users) && count($users) > 0)
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" data-emp-id="{{ $user->emp_id ?? '' }}" ${data.issued_to == {{ $user->id }} ? 'selected' : ''}>
                                        {{ addslashes($user->name) }}{{ $user->emp_id ? ' (' . addslashes($user->emp_id) . ')' : '' }}
                                    </option>
                                @endforeach
                            @endif
                            ${data.issued_to && !$('#assignment-cards-container .employee-select:first option[value="' + data.issued_to + '"]').length ? `<option value="${data.issued_to}" selected>${data.employee_name || 'Selected Employee'}</option>` : ''}
                        </select>
                        <input type="hidden" name="assignments[${assignmentIndex}][emp_id]" class="employee-id-input" value="${data.emp_id || ''}">
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm flatpickr-assignment issue-date" name="assignments[${assignmentIndex}][issue_date]" value="${data.issue_date || ''}" placeholder="DD-MM-YYYY" autocomplete="off">
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0" class="form-control form-control-sm text-center total-hrs" name="assignments[${assignmentIndex}][total_hrs]" value="${data.total_hrs || ''}" placeholder="0.00">
                    </td>
                    <td>
                        <input type="number" step="1" min="0" class="form-control form-control-sm text-center qty-input" name="assignments[${assignmentIndex}][issue_qty]" value="${data.issue_qty || ''}" placeholder="Qty">
                    </td>
                    <td>
                        <select class="form-select select2 status-select-row form-select-sm" name="assignments[${assignmentIndex}][status_display]" disabled>
                            <option value="Open" ${data.status === 'Open' || !data.status ? 'selected' : ''}>Open</option>
                            <option value="In Progress" ${data.status === 'In Progress' ? 'selected' : ''}>In Progress</option>
                            <option value="Completed" ${data.status === 'Completed' ? 'selected' : ''}>Completed</option>
                        </select>
                        <input type="hidden" name="assignments[${assignmentIndex}][status]" value="${data.status || 'Open'}">
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm" name="assignments[${assignmentIndex}][remarks]" value="${data.remarks || ''}" placeholder="Remarks">
                    </td>
                    <td class="text-center text-nowrap">
                        <button type="button" class="btn btn-icon btn-outline-primary btn-sm add-row-for-service me-1" title="Add Another Row for this Service" style="display: none;">
                            <i class="ri ri-add-line"></i>
                        </button>
                        ${data.status === 'Completed' ? `
                            <button type="button" class="btn btn-icon btn-sm btn-outline-secondary" disabled style="opacity: 0.35; cursor: not-allowed;" title="Completed batch cannot be deleted">
                                <i class="ri ri-delete-bin-line"></i>
                            </button>
                        ` : `
                            <button type="button" class="btn btn-icon btn-outline-danger btn-sm remove-assignment-row" title="Delete Row">
                                <i class="ri ri-delete-bin-line"></i>
                            </button>
                        `}
                    </td>
                </tr>`;

                if ($insertAfter && $insertAfter.length) {
                    $insertAfter.after(rowHtml);
                    let $row = $insertAfter.next('.assignment-row');
                    initRowControls($row, data);
                } else {
                    $('#assignment-cards-container').append(rowHtml);
                    let $row = $('#assignment-cards-container .assignment-row:last');
                    initRowControls($row, data);
                }
                assignmentIndex++;
                updateCardNumbers();
                validateTotalQty();
            }
            $(document).on('input change', '#assignment-cards-container input[name*="[issue_qty]"]', function() {
                var $currentRow = $(this).closest('.assignment-row');
                var serviceId = $currentRow.find('.services-select').val();
                
                if (serviceId) {
                    var $allRows = $('#assignment-cards-container .assignment-row').filter(function() {
                        return $(this).find('.services-select').val() == serviceId;
                    });
                    
                    if ($allRows.length > 1) {
                        var $lastRow = $allRows.last();
                        if (!$currentRow.is($lastRow)) {
                            var $selectedOption = $currentRow.find('.services-select option:selected');
                            var maxQty = parseFloat($selectedOption.data('qty')) || 0;
                            if (!maxQty) {
                                var $el = $('#stage_select');
                                var $selected = $el.is('select') ? $el.find(':selected') : $el;
                                maxQty = parseFloat($selected.data('qty')) || 0;
                            }
                            
                            var totalAssigned = 0;
                            $allRows.not($lastRow).each(function() {
                                totalAssigned += parseFloat($(this).find('input[name*="[issue_qty]"]').val()) || 0;
                            });
                            
                            var balance = maxQty - totalAssigned;
                            if (balance < 0) balance = 0;
                            $lastRow.find('input[name*="[issue_qty]"]').val(balance);
                        }
                    }
                }
                
                validateTotalQty();
            });

            $(document).on('change', '#assignment-cards-container .services-select', function(e, isInit) {
                var $row = $(this).closest('.assignment-row');
                updateRowServiceBadge($row);
                if (isInit) return; 
                var serviceId = $(this).val();
                
                if (serviceId) {
                    var $selectedOption = $(this).find('option:selected');
                    var maxQty = parseFloat($selectedOption.data('qty')) || 0;
                    if (!maxQty) {
                        var $el = $('#stage_select');
                        var $selected = $el.is('select') ? $el.find(':selected') : $el;
                        maxQty = parseFloat($selected.data('qty')) || 0;
                    }
                    
                    var totalAssigned = 0;
                    $('#assignment-cards-container .assignment-row').not($row).each(function() {
                        if ($(this).find('.services-select').val() == serviceId) {
                            totalAssigned += parseFloat($(this).find('input[name*="[issue_qty]"]').val()) || 0;
                        }
                    });
                    
                    var balance = maxQty - totalAssigned;
                    if (balance < 0) balance = 0;
                    
                    $row.find('input[name*="[issue_qty]"]').val(balance);
                }
                validateTotalQty();
            });

            $(document).on('input', 'input[name*="[issue_qty]"]', function() {
                validateTotalQty();
            });
            function updateCardNumbers() {
                $('#assignment-cards-container .assignment-row, #assignment-cards-container .assignment-card').each(function(index) {
                    $(this).find('.card-badge').text('#' + (index + 1));
                });
            }
            
            function validateTotalQty() {
                var $el = $('#stage_select');
                var $selected = $el.is('select') ? $el.find(':selected') : $el;
                var stageMaxQty = parseFloat($selected.data('qty')) || 0;

                $('#qty-exceed-error').remove();

                var isValid = true;
                var errorMessages = [];
                var serviceTotals = {};
                var serviceNames = {};
                var serviceMaxQties = {};

                $('#assignment-cards-container .assignment-row').each(function() {
                    var qty = parseFloat($(this).find('input[name*="[issue_qty]"]').val()) || 0;
                    var $serviceSelect = $(this).find('.services-select');
                    var serviceId = $serviceSelect.val();
                    var $selectedOption = $serviceSelect.find('option:selected');
                    var serviceName = $selectedOption.text();
                    
                    if (serviceId) {
                        var sMaxQty = parseFloat($selectedOption.data('qty')) || stageMaxQty;
                        
                        if (!serviceTotals[serviceId]) {
                            serviceTotals[serviceId] = 0;
                            serviceNames[serviceId] = serviceName;
                            serviceMaxQties[serviceId] = sMaxQty;
                        }
                        serviceTotals[serviceId] += qty;
                    }
                });

                for (var sId in serviceTotals) {
                    if (serviceTotals[sId] > serviceMaxQties[sId]) {
                        isValid = false;
                        var remaining = Math.max(0, serviceMaxQties[sId] - (serviceTotals[sId] - parseFloat($('#assignment-cards-container .assignment-row').find('.services-select').filter(function() { return $(this).val() == sId; }).closest('.assignment-row').find('input[name*="[issue_qty]"]').last().val() || 0)));
                        errorMessages.push(
                            `<b>${serviceNames[sId]}</b>: Total assigned qty (<b>${serviceTotals[sId]} PCS</b>) exceeds remaining pending quantity (<b>${serviceMaxQties[sId]} PCS</b>).`
                        );
                    }
                }

                var $submitBtn = $('#content-issue button[type="submit"]');
                if (!isValid) {
                    $submitBtn.attr('disabled', true);
                    var errorHtml = errorMessages.map(msg =>
                        `<div class="d-flex align-items-center gap-2 mb-1">
                            <i class="ri ri-error-warning-line fs-5"></i>
                            <div>${msg}</div>
                        </div>`
                    ).join('<hr class="my-1">');

                    $('#content-issue .card-body').first().prepend(
                        `<div id="qty-exceed-error" class="alert alert-danger mb-4">${errorHtml}</div>`
                    );
                } else {
                    $submitBtn.attr('disabled', false);
                }

                $('#assignment-cards-container .assignment-row').each(function() {
                    var $row = $(this);
                    var sId = $row.find('.services-select').val();
                    var $addBtn = $row.find('.add-row-for-service');

                    if (sId) {
                        var $selectedOption = $row.find('.services-select option:selected');
                        var sPending = parseFloat($selectedOption.attr('data-pending'));
                        if (isNaN(sPending)) {
                            var $badge = $row.find('.badge-pending');
                            sPending = $badge.length ? (parseFloat($badge.text()) || 0) : (parseFloat($selectedOption.data('qty')) || 0);
                        }

                        if (sPending > 0) {
                            $addBtn.show();
                        } else {
                            $addBtn.hide();
                        }
                    } else {
                        $addBtn.hide();
                    }
                });

                return isValid;
            }
            function initRowControls($row, data = {}) {
                var $el = $('#stage_select');
                var $selected = $el.is('select') ? $el.find(':selected') : $el;
                var stageStartDate = $selected.data('start-date');
                var curMinDate = stageMinDate || null;
                var curMaxDate = stageMaxDate || null;
                if (!curMinDate && stageStartDate) {
                    curMinDate = formatDateForPicker(stageStartDate);
                }
                if (!curMaxDate && stageDueDate) {
                    curMaxDate = formatDateForPicker(stageDueDate);
                }

                var isCompletedRow = ($row.find('input[name*="[status]"]').val() === 'Completed') || ($row.find('.status-select-row').val() === 'Completed');
                if (isCompletedRow) {
                    $row.find('.flatpickr-assignment').attr('readonly', true).css({'pointer-events': 'none', 'background-color': '#f8f9fa'});
                } else {
                    var $issueInput = $row.find('.flatpickr-assignment.issue-date');
                    if ($issueInput[0] && $issueInput[0]._flatpickr) {
                        $issueInput[0]._flatpickr.destroy();
                    }

                    $issueInput.flatpickr({
                        dateFormat: "d-m-Y",
                        allowInput: true,
                        closeOnSelect: true,
                        minDate: curMinDate,
                        maxDate: curMaxDate,
                        defaultDate: $issueInput.val() || null,
                        onChange: function(selectedDates, dateStr, instance) {
                            calculateRowHours($row);

                            var isFirstRow = $row.is($('.assignment-row').first());
                            if (isFirstRow && dateStr) {
                                $('.assignment-row').not($row).each(function() {
                                    var $r = $(this);
                                    var rStatus = $r.find('input[name*="[status]"]').val() || $r.find('.status-select-row').val();
                                    if (rStatus === 'Completed') return;
                                    var otherInput = $r.find('.flatpickr-assignment.issue-date')[0];
                                    if (otherInput && otherInput._flatpickr && !otherInput.value) {
                                        otherInput._flatpickr.setDate(dateStr, true);
                                    }
                                });
                            }
                            instance.close();
                        }
                    });
                }

                calculateRowHours($row);          
                $row.find('.services-select').each(function() {
                    if ($(this).data('select2')) $(this).select2('destroy');
                }).select2({ 
                    placeholder: "Select Service", 
                    allowClear: true, 
                    width: '100%', 
                    dropdownParent: $row.find('.services-select').parent()
                }).on('select2:select', function(e) {
                    $(this).trigger('change');
                });

                $row.find('.status-select-row').each(function() {
                    if ($(this).data('select2')) $(this).select2('destroy');
                }).select2({ 
                    placeholder: "Status", 
                    allowClear: false, 
                    width: '100%',
                    minimumResultsForSearch: Infinity
                });

                $row.find('.employee-select').each(function() {
                    if ($(this).data('select2')) $(this).select2('destroy');
                }).select2({
                    dropdownParent: $row.find('.employee-select').parent(),
                    placeholder: 'Select Employee',
                    allowClear: true,
                    width: '100%'
                }).on('select2:select', function(e) {
                    var $selected = $(this).find('option:selected');
                    var empId = $selected.attr('data-emp-id') || '';
                    $row.find('.employee-id-input').val(empId);
                }).on('select2:clear', function(e) {
                    $row.find('.employee-id-input').val('');
                });

                updateRowServiceBadge($row);
            }

            function updateRowServiceBadge($row) {
                var $select = $row.find('.services-select');
                var $badge = $row.find('.service-metrics-badge');
                if (!$badge.length) return;

                var $selected = $select.find('option:selected');
                var serviceId = $select.val();

                if (serviceId && $selected.length && $selected.attr('data-target') !== undefined) {
                    var target = $selected.attr('data-target') || 0;
                    var achieved = $selected.attr('data-achieved') || 0;
                    var pending = $selected.attr('data-pending') !== undefined ? $selected.attr('data-pending') : ($selected.attr('data-qty') || 0);

                    $badge.find('.badge-target').text(target);
                    $badge.find('.badge-achieved').text(achieved);
                    $badge.find('.badge-pending').text(pending);
                    $badge.attr('style', 'font-size: 10px; display: flex !important;');
                } else {
                    $badge.attr('style', 'font-size: 10px; display: none !important;');
                }
            }

            $(document).on('input', '.total-hrs', function() {
                var $row = $(this).closest('.assignment-row');
                var hrsVal = $(this).val();
                
                var isFirstRow = $row.is($('.assignment-row').first());
                if (isFirstRow && hrsVal !== '') {
                    $('.assignment-row').not($row).each(function() {
                        var $otherInput = $(this).find('.total-hrs');
                        if (!$otherInput.val()) {
                            $otherInput.val(hrsVal);
                        }
                    });
                }
            });

            function calculateRowHours($row) {
                // let issueDateStr = $row.find('.issue-date').val();
                // let dueDateStr = $row.find('.due-date').val();
                // if (issueDateStr && dueDateStr) {
                //     let d1 = moment(issueDateStr, "DD-MM-YYYY");
                //     let d2 = moment(dueDateStr, "DD-MM-YYYY");
                //     if (d1.isValid() && d2.isValid()) {
                //         let diffDays = d2.diff(d1, 'days');
                //         let diffHours = (diffDays + 1) * 24;
                //         if (diffHours < 0) diffHours = 0;
                //         $row.find('.total-hrs').val(diffHours);
                //     }
                // }
            }

            // function checkOverdueStatus($row) {
            //     let dueDateStr = $row.find('.due-date').val();
            //     let status = $row.find('.status-select-row').val();

            //     $row.removeClass('table-danger'); 
            //     $row.find('.due-date').removeClass('is-invalid');

            //     if (dueDateStr && status !== 'Completed') {
            //         let d2 = moment(dueDateStr, "DD-MM-YYYY");
            //         if (d2.isBefore(moment(), 'day')) {
            //             $row.addClass('table-danger');
            //             $row.find('.due-date').addClass('is-invalid');
            //         }
            //     }
            // }

            function getSelectedPlantId() {
                var $el = $('#stage_select');
                var $selected = $el.is('select') ? $el.find(':selected') : $el;
                var pId = $selected.attr('data-service-provider-id') || $selected.data('service-provider-id');
                if (!pId || pId === 'all' || pId === 'null' || pId === 'undefined') {
                    pId = $selected.attr('data-job-card-service-provider-id') || $selected.data('job-card-service-provider-id');
                }
                if (!pId || pId === 'null' || pId === 'undefined') {
                    pId = 'all';
                }
                return pId;
            }

            function getSelectedOperationStageId() {
                var $el = $('#stage_select');
                var $selected = $el.is('select') ? $el.find(':selected') : $el;
                return $selected.attr('data-operation-stage-id') || $selected.data('operation-stage-id') || 'null';
            }

            $('#add-assignment-row').on('click', function() {
                addAssignmentRow();
                validateTotalQty();
                if ($('#status').val() === 'Completed') {
                    $('#status').val('In Progress').trigger('change');
                }
            });

            $(document).on('click', '.add-row-for-service', function() {
                var $currentRow = $(this).closest('.assignment-row');
                var currentServiceId = $currentRow.find('.services-select').val();
                var currentIssueDate = $currentRow.find('.flatpickr-assignment.issue-date').val() || $('#plan_date').val() || '';
                var currentTotalHrs = $currentRow.find('.total-hrs').val() || '';

                var $selectedOption = $currentRow.find('.services-select option:selected');
                var sPending = parseFloat($selectedOption.attr('data-pending'));
                if (isNaN(sPending)) {
                    var $badge = $currentRow.find('.badge-pending');
                    sPending = $badge.length ? (parseFloat($badge.text()) || 0) : (parseFloat($selectedOption.data('qty')) || 0);
                }

                addAssignmentRow({
                    service_id: currentServiceId,
                    issue_qty: sPending > 0 ? sPending : '',
                    issue_date: currentIssueDate,
                    total_hrs: currentTotalHrs,
                    status: 'Open'
                }, $currentRow);

                validateTotalQty();
                if ($('#status').val() === 'Completed') {
                    $('#status').val('In Progress').trigger('change');
                }
            });

            $(document).on('click', '.remove-assignment-row', function() {
                var $row = $(this).closest('.assignment-row');
                var rowStatus = $row.find('input[name*="[status]"]').val() || $row.find('.status-select-row').val();
                if (rowStatus === 'Completed') {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Cannot Delete',
                            text: 'This batch is already Completed and cannot be deleted.',
                            icon: 'warning',
                            confirmButtonText: 'OK',
                            customClass: { confirmButton: 'btn btn-primary' },
                            buttonsStyling: false
                        });
                    } else {
                        alert('This batch is already Completed and cannot be deleted.');
                    }
                    return false;
                }
                if ($('#assignment-cards-container .assignment-row').length > 1) {
                    $row.remove();
                    updateCardNumbers();
                    validateTotalQty();
                } else {
                    alert('At least one assignment is required.');
                }
            });

            $('.select2').not('.services-select, .employee-select, .status-select-row, .material-select').each(function() {
                if ($(this).data('select2')) return;
                $(this).select2({
                    dropdownParent: $(this).parent(),
                    placeholder: $(this).data('placeholder'),
                    width: '100%'
                });
            });

            $('#stage_select').on('change', function(e, isInit) {
                var $el = $(this);
                var $selected = $el.is('select') ? $el.find(':selected') : $el;
                var sDate = $selected.data('start-date');
                var dDate = $selected.data('due-date');
                if (sDate) stageMinDate = formatDateForPicker(sDate);
                if (dDate) stageMaxDate = formatDateForPicker(dDate);

                if (typeof planDatePicker !== 'undefined' && planDatePicker) {
                    planDatePicker.set('minDate', stageMinDate);
                    planDatePicker.set('maxDate', stageMaxDate);
                }

                $('#assignment-cards-container .assignment-row').each(function() {
                    var $row = $(this);
                    calculateRowHours($row);
                    var issueFp = $row.find('.flatpickr-assignment.issue-date')[0] ? $row.find('.flatpickr-assignment.issue-date')[0]._flatpickr : null;
                    if (issueFp) {
                        issueFp.set('minDate', stageMinDate);
                        issueFp.set('maxDate', stageMaxDate);
                    }
                });

                $('.select2').trigger('change', [isInit]);
                if (typeof populateAllMaterialDropdowns === 'function') {
                    populateAllMaterialDropdowns();
                }
            });

            function formatDateForPicker(dateStr) {
                if (!dateStr || typeof dateStr !== 'string') return '';

                if (dateStr.includes('T')) {
                    dateStr = dateStr.split('T')[0];
                } else if (dateStr.includes(' ')) {
                    dateStr = dateStr.split(' ')[0];
                }

                if (dateStr.includes('-')) {
                    let parts = dateStr.split('-');
                    if (parts[0].length === 4) { 
                        if (parts[0] === '0001' || parts[0] === '1970') return ''; 
                        return parts[2] + '-' + parts[1] + '-' + parts[0]; 
                    }
                }
                return dateStr;
            }
            function initAllExistingRows() {
                var val = $('#stage_select').val();
                if(val) {
                    $('#stage_select').trigger('change', [true]);
                }

                $('.assignment-row').each(function() {
                    var $row = $(this);
                    initRowControls($row, {});
                });
                validateTotalQty();
            }
            initAllExistingRows();

            function initSelect2(selector) {
                $(selector).select2({
                    placeholder: "Select Material",
                    allowClear: true,
                    width: '100%'
                });
            }

            function populateAllMaterialDropdowns() {
                var selectedStage = $('#stage_select').val();
                var jobCardId = $('input[name="job_card_id"]').val();

                if (!selectedStage && !jobCardId) return;

                var requestUrl = "{{ url('task_management/get-stage-consumables') }}/" + (selectedStage || 0);
                if (jobCardId) {
                    requestUrl += "?job_card_id=" + jobCardId;
                }

                $.ajax({
                    url: requestUrl,
                    type: "GET",
                    success: function(response) {
                        if (response.success) {
                            availableMaterials = response.materials;
                            $('.material-select').each(function() {
                                var currentVal = $(this).val();
                                $(this).empty().append('<option value="">Select Material</option>');
                                var $select = $(this);
                                $.each(availableMaterials, function(i, mat) {
                                    $select.append(new Option(mat.text, mat.id, (mat.id == currentVal), (mat.id == currentVal)));
                                });
                                $(this).trigger('change');
                            });

                            $('.item-row').each(function() {
                                var $row = $(this);
                                var selectedId = $row.find('.material-select').val();
                                if (selectedId) {
                                    var mat = availableMaterials.find(function(m) { return m.id == selectedId; });
                                    if (mat) {
                                        $row.find('input[name*="[art_no]"]').val(mat.art_no || '');
                                        $row.find('input[name*="[grn_no]"]').val(mat.grn_no || '');
                                    }
                                }
                            });
                        }
                    }
                });
            }

            var availableServices = [
                @if($task && is_array($task->services))
                    @foreach($task->services as $svcId)
                        @php $svc = \App\Models\ProductionService::find($svcId); @endphp
                        @if($svc)
                            { id: '{{ $svc->id }}', text: '{{ $svc->service_name }}' },
                        @endif
                    @endforeach
                @endif
            ];

            $('#add_adjustment_row').on('click', function() {
                var serviceOptions = '<option value="">Select Service</option>';
                $.each(availableServices, function(i, svc) {
                    serviceOptions += `<option value="${svc.id}">${svc.text}</option>`;
                });

                var newRow = `
                    <tr class="item-row">
                        <td>
                            <select class="select2 form-select service-select" name="items[${rowIndex}][service_id]">
                                ${serviceOptions}
                            </select>
                        </td>
                        <td>
                            <input type="hidden" name="items[${rowIndex}][art_no]" value="">
                            <input type="hidden" name="items[${rowIndex}][grn_no]" value="">
                            <select class="select2 form-select material-select" name="items[${rowIndex}][raw_material_id]">
                                <option value="">Select Material</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-select select2" name="items[${rowIndex}][adjustment_type]">
                                <option value="Loss">Loss / Damage (-)</option>
                                <option value="Rework">Rework (Consum.) (-)</option>
                                <option value="Excess">Excess Found (+)</option>
                                <option value="Material Return">Return to Store (+)</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" step="0.01" min="0" class="form-control" name="items[${rowIndex}][qty]" placeholder="0.00">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="items[${rowIndex}][remarks]" placeholder="Remarks">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-icon btn-outline-danger btn-sm remove-row"><i class="ri ri-delete-bin-line"></i></button>
                        </td>
                    </tr>
                `;
                $('#adjustment_items_table tbody').append(newRow);
                var $row = $('#adjustment_items_table tbody tr:last');
                initSelect2($row.find('.select2'));

                var $select = $row.find('.material-select');
                $.each(availableMaterials, function(i, mat) {
                    $select.append(new Option(mat.text, mat.id, false, false));
                });
                $select.trigger('change');

                rowIndex++;
            });
            $(document).on('keydown keypress', '#assignment-cards-container input[type="number"], #adjustment_items_table input[type="number"], .status-update-row input[type="number"], .bulk-qty-input', function (e) {
                if (e.key === '-' || e.key === '+' || e.key === 'e' || e.key === 'E' || e.which === 45 || e.which === 43 || e.which === 189 || e.which === 109 || e.which === 107 || e.which === 187) {
                    e.preventDefault();
                    return false;
                }
                if (e.which === 40 || e.key === 'ArrowDown') {
                    var val = parseFloat($(this).val()) || 0;
                    if (val <= 0) {
                        $(this).val(0);
                        $(this).trigger('input');
                        e.preventDefault();
                        return false;
                    }
                }
            });
            $(document).on('input paste', '#assignment-cards-container input[type="number"], #adjustment_items_table input[type="number"], .status-update-row input[type="number"], .bulk-qty-input', function () {
                var el = this;
                setTimeout(function() {
                    var val = $(el).val();
                    if (val !== '' && (parseFloat(val) < 0 || String(val).indexOf('-') !== -1 || String(val).indexOf('+') !== -1)) {
                        var cleaned = String(val).replace(/[-+]/g, '');
                        $(el).val(cleaned ? (parseFloat(cleaned) >= 0 ? cleaned : '0') : '0');
                        $(el).trigger('input');
                    }
                }, 0);
            });
            $(document).on('click', '.remove-row', function() {
                if ($('#adjustment_items_table tbody tr').length > 1) {
                    $(this).closest('tr').remove();
                } else {
                    alert('At least one item is required.');
                }
            });

            $(document).on('change', '.material-select', function() {
                var selectedId = $(this).val();
                var $row = $(this).closest('tr');
                var mat = availableMaterials.find(function(m) { return m.id == selectedId; });
                if (mat) {
                    $row.find('input[name*="[art_no]"]').val(mat.art_no || '');
                    $row.find('input[name*="[grn_no]"]').val(mat.grn_no || '');
                } else {
                    $row.find('input[name*="[art_no]"]').val('');
                    $row.find('input[name*="[grn_no]"]').val('');
                }
            });

            $('#assignment-cards-container .assignment-row').each(function() {
                initRowControls($(this), {});
            });

            if ($('#stage_select').val()) {
                $('#stage_select').trigger('change', [true]);
            } else {
                 populateAllMaterialDropdowns();
            }

            $('.status-select').select2({
                dropdownParent: $(this).parent(),
                placeholder: $(this).data('placeholder'),
                width: '100%'
            });
            @php
                $activeTab = session('active_tab', 'issue');
                if ($errors->any() && $activeTab == 'issue') {
                    $receiveKeys = ['received_date', 'received_store', 'received_services'];
                    $adjKeys = ['adjustment_type', 'qty', 'reason', 'raw_material_id', 'approved_by'];
                    foreach ($errors->keys() as $key) {
                        if (in_array($key, $receiveKeys) || str_contains($key, 'received_services')) {
                            $activeTab = 'receive';
                            break;
                        }
                        if (in_array($key, $adjKeys)) {
                            $activeTab = 'adjustment';
                            break;
                        }
                    }
                }
            @endphp
            var triggerEl = document.querySelector('#tab-{{ $activeTab }}');
            if(triggerEl) bootstrap.Tab.getOrCreateInstance(triggerEl).show();
        });
    </script>

    <style>
        :root {
            --erp-primary: #696cff;
            --erp-bg: #f5f5f9;
            --erp-text: #435971;
            --erp-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12);
        }
        .text-primary { color: var(--erp-primary) !important; }
        .bg-primary { background-color: var(--erp-primary) !important; }
        .btn-primary { background-color: var(--erp-primary); border-color: var(--erp-primary); }

        .erp-header-card { border-radius: 0.75rem; border: none !important; box-shadow: var(--erp-shadow) !important; }
        .kpi-card { border-radius: 0.75rem; transition: 0.3s; border: none !important; box-shadow: var(--erp-shadow) !important; }
        .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 4px 12px 0 rgba(67, 89, 113, 0.18) !important; }

        .custom-tab-btn { 
            color: var(--erp-text); 
            border-radius: 0.65rem; 
            padding: 0.85rem 1.25rem; 
            transition: all 0.25s ease-in-out; 
            border: 1.5px solid #e7e7e8;
            background-color: #fff;
            width: 100%;
            display: flex;
            align-items: center;
            box-shadow: 0 1px 3px rgba(67, 89, 113, 0.05);
        }
        .custom-tab-btn:hover { 
            background-color: rgba(105, 108, 255, 0.04); 
            color: var(--erp-primary); 
            border-color: rgba(105, 108, 255, 0.35); 
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 89, 113, 0.1);
        }
        .custom-tab-btn.active { 
            background-color: rgba(105, 108, 255, 0.08) !important; 
            color: var(--erp-primary) !important; 
            border-color: var(--erp-primary) !important; 
            box-shadow: 0 3px 10px rgba(105, 108, 255, 0.18) !important;
        }
        .custom-tab-btn.active .section-icon.bg-label-primary {
            background-color: var(--erp-primary) !important;
            color: #fff !important;
        }
        .custom-tab-btn.active .section-icon.bg-label-success {
            background-color: #71dd37 !important;
            color: #fff !important;
        }
        .custom-tab-btn.active .section-icon.bg-label-warning {
            background-color: #ffab00 !important;
            color: #fff !important;
        }

        .font-small { font-size: 0.75rem !important; }
        .section-card { border-radius: 0.75rem; border: none !important; box-shadow: var(--erp-shadow) !important; }
        .section-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }

        .custom-accordion .accordion-button:not(.collapsed) {
            background-color: rgba(105, 108, 255, 0.05);
            color: var(--erp-primary);
            box-shadow: none;
        }
        .custom-accordion .accordion-button:focus { box-shadow: none; }
        .custom-accordion .accordion-item { overflow: hidden; }

        .bg-light-subtle { background-color: #f8f9fa !important; }

        .sticky-sidebar { position: sticky; top: 100px; }
        .avatar { width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; }

        /* Assignments Table Styles */
        #assignments-table {
            margin-bottom: 0;
            width: 100%;
        }
        #assignments-table thead th {
            background-color: #f5f5f9;
            color: #566a7f;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.75rem 0.6rem;
            border-bottom: 2px solid #e7e7e8;
        }
        #assignments-table tbody tr {
            transition: background 0.15s ease-in-out;
        }
        #assignments-table tbody tr:hover {
            background-color: rgba(105, 108, 255, 0.02);
        }
        #assignments-table td {
            vertical-align: middle;
            padding: 0.5rem 0.5rem;
        }
        #assignments-table .card-badge {
            background: rgba(105, 108, 255, 0.08);
            color: var(--erp-primary);
            padding: 0.35rem 0.6rem;
            border-radius: 0.375rem;
            display: inline-block;
            font-size: 0.8rem;
        }
        #assignments-table input[type="number"]::-webkit-inner-spin-button,
        #assignments-table input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        #assignments-table input[type="number"] {
            -moz-appearance: textfield;
        }
        #assignments-table .total-hrs {
            min-width: 85px;
            text-align: center;
            padding: 0.35rem 0.3rem !important;
            font-weight: 600;
        }
        #assignments-table .qty-input {
            min-width: 95px;
            text-align: center;
            padding: 0.35rem 0.3rem !important;
            font-weight: 600;
        }
        #assignments-table .form-control-sm,
        #assignments-table .form-select-sm {
            font-size: 0.8125rem;
            border-color: #d9dee3;
        }
        #assignments-table .select2-container .select2-selection--single {
            height: 34px !important;
            border-color: #d9dee3 !important;
            border-radius: 0.375rem !important;
        }
        #assignments-table .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 32px !important;
            font-size: 0.8125rem !important;
            padding-left: 0.6rem !important;
        }
        #assignments-table .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 32px !important;
        }
        .remove-assignment-row {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }
    </style>
    <!-- History Modal -->
    <div class="modal fade" id="historyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-light py-3">
                    <h5 class="modal-title fw-bold"><i class="ri ri-history-line me-2"></i>Activity Timeline</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="activity-timeline">
                        <div class="text-center py-4" id="history-loading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div id="history-content"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .activity-timeline {
        position: relative;
        padding-left: 20px;
    }
    .activity-timeline::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 2rem;
        padding-left: 1.5rem;
    }
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    .timeline-marker {
        position: absolute;
        left: -21px;
        top: 0;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #696cff;
        z-index: 1;
    }
    .timeline-header {
        margin-bottom: 0.25rem;
    }
    .timeline-user {
        font-weight: 600;
        color: #566a7f;
    }
    .timeline-time {
        font-size: 0.85rem;
        color: #a1acb8;
    }
    .timeline-body {
        background: #f8f9fa;
        padding: 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.9rem;
        color: #697a8d;
    }
    </style>

    <!-- Bulk Update Modal -->
    <div class="modal fade" id="bulkUpdateModal" tabindex="-1" aria-labelledby="bulkUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold text-primary d-flex align-items-center mb-0" id="bulkUpdateModalLabel">
                        <i class="ri ri-stack-line me-2"></i> Bulk Update Quantity
                    </h5>
                    <div class="d-flex align-items-center me-3">
                        @if(isset($jobCard->grand_total_qty))
                            <span class="badge bg-label-primary px-3 py-2 fs-6">Qty: {{ (int) $jobCard->grand_total_qty }} PCS</span>
                        @endif
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 px-3 mb-4 d-flex align-items-center">
                        <i class="ri ri-information-line me-2 fs-5"></i>
                        <small>Values entered here will be applied to all employees, <strong>except</strong> those you have manually modified.</small>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-success">Completed Qty</label>
                            <input type="number" step="0.01" min="0" class="form-control border-success fw-bold bulk-qty-input" id="bulk_completed_qty" placeholder="e.g. 10">
                            <div class="invalid-feedback fw-bold extra-small"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger">Wastage Qty</label>
                            <input type="number" step="0.01" min="0" class="form-control border-danger fw-bold bulk-qty-input" id="bulk_wastage_qty" placeholder="e.g. 0">
                            <div class="invalid-feedback fw-bold extra-small"></div>
                        </div>
                        @if(auth()->user()->hasRole('QUALITY CHECKER') || auth()->user()->id == 1)
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-info">QC Checked Qty</label>
                            <input type="number" step="1" min="0" class="form-control border-info fw-bold bulk-qty-input" id="bulk_qc_checked" placeholder="e.g. 10">
                            <div class="invalid-feedback fw-bold extra-small"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-info">QC Passed Qty</label>
                            <input type="number" step="1" min="0" class="form-control border-info fw-bold bulk-qty-input" id="bulk_qc_passed" placeholder="e.g. 10">
                            <div class="invalid-feedback fw-bold extra-small"></div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0 py-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary fw-bold px-4" id="btn-apply-bulk">
                        <i class="ri ri-check-line me-1"></i> Apply to All Employees
                    </button>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
    <script>
        $(document).ready(function() {
            function getAssignedQty(row) {
                var $el = row.find('.row-assigned-qty');
                var raw = $el.attr('data-raw-qty') || $el.data('raw-qty');
                if (raw !== undefined && raw !== null && raw !== '') {
                    return parseFloat(raw) || 0;
                }
                var txt = String($el.text()).replace(/,/g, '').trim();
                return parseFloat(txt) || 0;
            }

            function calculateRow(row) {
                var assignedQty = getAssignedQty(row);
                var completedQty = parseFloat(String(row.find('.row-completed-qty').val()).replace(/,/g, '')) || 0;
                var wastageQty = parseFloat(String(row.find('.row-wastage-qty').val()).replace(/,/g, '')) || 0;

                var inprogressInput = row.find('.row-inprogress-qty');
                var statusInput = row.find('.row-status');
                var balanceDisplay = row.find('.balance-display');
                var progressBar = row.find('.progress-bar');

                var totalUsed = completedQty + wastageQty;
                var remaining = assignedQty - totalUsed;

                if (totalUsed > assignedQty) {
                    row.addClass('border-danger').removeClass('border-success');
                } else {
                    row.removeClass('border-danger');
                }

                var inprogressQty = Math.max(0, remaining);
                inprogressInput.val(inprogressQty);
                balanceDisplay.text(inprogressQty); 

                var percentage = 0;
                if (assignedQty > 0) {
                    percentage = Math.min(100, Math.round((completedQty / assignedQty) * 100));
                }
                progressBar.css('width', percentage + '%').attr('aria-valuenow', percentage);
                row.find('.progress-bar-text').text(percentage + '%'); // Optional: update text if present

                var statusColor = 'secondary';
                var svcId = row.attr('data-service-id') || row.data('service-id');
                var svcTarget = parseFloat(row.attr('data-service-target') || row.data('service-target')) || 0;
                var totalSvcCompleted = 0;
                if (svcId) {
                    $('.status-update-row[data-service-id="' + svcId + '"]').each(function() {
                        var c = parseFloat(String($(this).find('.row-completed-qty').val()).replace(/,/g, '')) || 0;
                        var w = parseFloat(String($(this).find('.row-wastage-qty').val()).replace(/,/g, '')) || 0;
                        totalSvcCompleted += (c + w);
                    });
                }

                if (completedQty == 0 && wastageQty == 0) {
                    statusInput.val('Open');
                    statusColor = 'secondary';
                } else if (totalUsed < assignedQty) {
                    statusInput.val('In Progress');
                    statusColor = 'info';
                } else if (totalUsed >= assignedQty) {
                    statusInput.val('Completed');
                    statusColor = 'success';
                }

                var statusBadge = row.find('.row-status-badge');
                statusBadge.text(statusInput.val());
                statusBadge.removeClass('bg-label-secondary bg-label-info bg-label-success border-secondary border-info border-success');
                statusBadge.addClass('bg-label-' + statusColor + ' border-' + statusColor);

                // QC Calculations
                if (row.find('.row-qc-checked').length > 0) {
                    var qcChecked = parseFloat(row.find('.row-qc-checked').val()) || 0;
                    var qcPassed = parseFloat(row.find('.row-qc-passed').val()) || 0;
                    var qcRejected = Math.max(0, qcChecked - qcPassed);

                    row.find('.row-qc-rejected').val(qcRejected.toFixed(0));

                    var qcStatusInput = row.find('.row-qc-status');
                    var qcStatusColor = 'secondary';
                    var qcStatus = 'Pending';

                    if (qcChecked == 0) {
                        qcStatus = 'Pending';
                        qcStatusColor = 'secondary';
                    } else if (qcChecked < completedQty) {
                        qcStatus = 'In QC';
                        qcStatusColor = 'info';
                    } else if (qcChecked == completedQty) {
                        qcStatus = 'QC Completed';
                        qcStatusColor = 'success';
                        row.addClass('border-success').removeClass('border-danger');
                    } else {
                        row.removeClass('border-success');
                    }

                    qcStatusInput.val(qcStatus);
                    var qcStatusBadge = row.find('.row-qc-status-badge');
                    qcStatusBadge.text(qcStatus);
                    qcStatusBadge.removeClass('bg-label-secondary bg-label-info bg-label-success border-secondary border-info border-success');
                    qcStatusBadge.addClass('bg-label-' + qcStatusColor + ' border-' + qcStatusColor);
                }
            }

            $(document).on('paste', '.row-completed-qty, .row-wastage-qty, .row-qc-checked, .row-qc-passed, .bulk-qty-input', function(e) {
                var pastedData = (e.originalEvent || e).clipboardData ? (e.originalEvent || e).clipboardData.getData('text/plain') : '';
                if (pastedData && pastedData.indexOf(',') !== -1) {
                    e.preventDefault();
                    var cleanData = pastedData.replace(/,/g, '').trim();
                    document.execCommand('insertText', false, cleanData);
                    $(this).trigger('input');
                }
            });

            $(document).on('input', '.row-completed-qty, .row-wastage-qty, .row-qc-checked, .row-qc-passed', function(e) {
                if (e.originalEvent) {
                    $(this).attr('data-user-modified', 'true');
                }
                var row = $(this).closest('.status-update-row');
                calculateRow(row);
                validateForm();
            });

            function validateForm() {
                var isFormValid = true;
                var errorMessages = [];

                $('.status-update-row').each(function() {
                    var row = $(this);
                    row.find('.row-validation-error').remove(); // Clear previous error

                    var assignedQty = getAssignedQty(row);
                    var completedQty = parseFloat(String(row.find('.row-completed-qty').val()).replace(/,/g, '')) || 0;
                    var wastageQty = parseFloat(String(row.find('.row-wastage-qty').val()).replace(/,/g, '')) || 0;

                    var isValid = true;
                    var errorMsg = "";
                    var empName = row.find('h6').first().text().trim() || 'Employee';
                    var svcName = row.find('small.text-muted').first().text().trim() || '';
                    var titlePrefix = empName + (svcName ? ' (' + svcName + ')' : '') + ': ';

                    if ((completedQty + wastageQty) > assignedQty) {
                        isValid = false;
                        errorMsg = titlePrefix + "Completed (" + completedQty + ") + Wastage (" + wastageQty + ") cannot exceed Assigned quantity (" + assignedQty + ").";
                        errorMessages.push(errorMsg);
                    }

                    if (row.find('.row-qc-checked').length > 0 && isValid) {
                        var qcChecked = parseFloat(row.find('.row-qc-checked').val()) || 0;
                        var qcPassed = parseFloat(row.find('.row-qc-passed').val()) || 0;
                        var qcRejected = parseFloat(row.find('.row-qc-rejected').val()) || 0;

                        if (qcChecked > completedQty) {
                            isValid = false;
                            errorMsg = titlePrefix + "QC Checked (" + qcChecked + ") cannot exceed Completed quantity (" + completedQty + ").";
                            errorMessages.push(errorMsg);
                        }

                        if (Math.abs((qcPassed + qcRejected) - qcChecked) > 0.01 && isValid) {
                            isValid = false;
                            errorMsg = titlePrefix + "QC Passed (" + qcPassed + ") + Rejected (" + qcRejected + ") must equal QC Checked (" + qcChecked + ").";
                            errorMessages.push(errorMsg);
                        }
                    }

                    if (!isValid) {
                        isFormValid = false;
                        row.find('.row.g-4').first().prepend('<div class="col-12 row-validation-error"><div class="alert alert-danger p-2 mb-0 fw-bold small"><i class="ri ri-error-warning-line me-1"></i>' + errorMsg + '</div></div>');
                    }
                });

                var submitBtn = $('#content-receive button[type="submit"]');
                if (!isFormValid) {
                    submitBtn.attr('disabled', true);
                } else {
                    submitBtn.attr('disabled', false);
                    $('#common-validation-error').remove();
                }

                return { valid: isFormValid, message: errorMessages.join('<br>') };
            }

            $('.status-update-row').each(function() {
                calculateRow($(this));
            });
            validateForm();

            $('#btn-view-history').on('click', function() {
                var taskId = $(this).data('task-id');
                $('#historyModal').modal('show');
                $('#history-loading').show();
                $('#history-content').empty();

                $.ajax({
                    url: `{{ url('task_management/get_logs') }}/${taskId}`,
                    method: 'GET',
                    success: function(response) {
                        $('#history-loading').hide();
                        if(response.success && response.logs.length > 0) {
                            var html = '';
                            $.each(response.logs, function(index, log) {
                                html += `
                                    <div class="timeline-item">
                                        <div class="timeline-marker"></div>
                                        <div class="timeline-header d-flex justify-content-between align-items-center mb-1">
                                            <div class="timeline-user text-primary fw-bold">${log.action}</div>
                                            <div class="timeline-time small text-muted">${log.created_at}</div>
                                        </div>
                                        <div class="mb-1 small text-dark"><i class="ri-user-line me-1"></i> ${log.user_name}</div>
                                        <div class="timeline-body border bg-light">
                                            ${log.description}
                                        </div>
                                        <div class="timeline-time mt-1 small text-muted text-end">${log.time_ago}</div>
                                    </div>
                                `;
                            });
                            $('#history-content').html(html);
                        } else {
                            $('#history-content').html('<div class="text-center text-muted py-3">No activity logs found.</div>');
                        }
                    },
                    error: function() {
                        $('#history-loading').hide();
                        $('#history-content').html('<div class="text-center text-danger py-3">Failed to load logs.</div>');
                    }
                });
            });

            // Bulk Update Validation Logic
            var maxJobCardQty = {{ isset($jobCard->grand_total_qty) ? (int) $jobCard->grand_total_qty : 0 }};
            
            function validateBulkInputs() {
                var $btnApply = $('#btn-apply-bulk');
                var isValid = true;
                
                // Clear previous errors
                $('.bulk-qty-input').removeClass('is-invalid').next('.invalid-feedback').text('');
                
                var compQty = parseFloat($('#bulk_completed_qty').val()) || 0;
                var wasQty = parseFloat($('#bulk_wastage_qty').val()) || 0;
                var qcChecked = parseFloat($('#bulk_qc_checked').val()) || 0;
                var qcPassed = parseFloat($('#bulk_qc_passed').val()) || 0;
                
                // 1. Negative checks
                $('.bulk-qty-input').each(function() {
                    var val = parseFloat($(this).val());
                    if (val < 0) {
                        $(this).addClass('is-invalid').next('.invalid-feedback').text('Cannot be negative.');
                        isValid = false;
                    }
                });

                // 2. Individual Max Check
                if (isValid) {
                    $('.bulk-qty-input').each(function() {
                        var val = parseFloat($(this).val()) || 0;
                        if (maxJobCardQty > 0 && val > maxJobCardQty) {
                            $(this).addClass('is-invalid').next('.invalid-feedback').text('Exceeds Job Card Qty (' + maxJobCardQty + ')');
                            isValid = false;
                        }
                    });
                }
                
                // 3. Completed + Wastage Check
                if (isValid && maxJobCardQty > 0 && (compQty + wasQty) > maxJobCardQty) {
                    $('#bulk_completed_qty, #bulk_wastage_qty').addClass('is-invalid');
                    $('#bulk_wastage_qty').next('.invalid-feedback').text('Completed + Wastage > Job Card Qty');
                    isValid = false;
                }
                
                // 4. QC Checked <= Completed
                if (isValid && $('#bulk_qc_checked').length > 0 && $('#bulk_completed_qty').val() !== '') {
                    if (qcChecked > compQty) {
                        $('#bulk_qc_checked').addClass('is-invalid').next('.invalid-feedback').text('Cannot exceed Completed Qty');
                        isValid = false;
                    }
                }
                
                // 5. QC Passed <= QC Checked
                if (isValid && $('#bulk_qc_passed').length > 0 && $('#bulk_qc_checked').val() !== '') {
                    if (qcPassed > qcChecked) {
                        $('#bulk_qc_passed').addClass('is-invalid').next('.invalid-feedback').text('Cannot exceed QC Checked');
                        isValid = false;
                    }
                }
                
                $btnApply.prop('disabled', !isValid);
            }

            $('.bulk-qty-input').on('input', function() {
                validateBulkInputs();
            });

            $('#btn-apply-bulk').on('click', function() {
                var bulkCompleted = $('#bulk_completed_qty').val();
                var bulkWastage = $('#bulk_wastage_qty').val();
                var bulkQcChecked = $('#bulk_qc_checked').val();
                var bulkQcPassed = $('#bulk_qc_passed').val();

                $('.status-update-row').each(function() {
                    var row = $(this);
                    var changed = false;
                    var assignedQty = getAssignedQty(row);

                    // Completed Qty: Cap at assignedQty so employees with lower assigned qty (e.g. 38) do not exceed their limit
                    var completedInput = row.find('.row-completed-qty');
                    if (bulkCompleted !== '') {
                        var targetComp = parseFloat(bulkCompleted) || 0;
                        var finalComp = Math.min(targetComp, assignedQty);
                        completedInput.val(finalComp).removeAttr('data-user-modified');
                        changed = true;
                    }

                    // Wastage Qty: Cap so completed + wastage <= assignedQty
                    var wastageInput = row.find('.row-wastage-qty');
                    if (bulkWastage !== '') {
                        var currentComp = parseFloat(completedInput.val()) || 0;
                        var targetWastage = parseFloat(bulkWastage) || 0;
                        var maxAllowedWastage = Math.max(0, assignedQty - currentComp);
                        var finalWastage = Math.min(targetWastage, maxAllowedWastage);
                        wastageInput.val(finalWastage).removeAttr('data-user-modified');
                        changed = true;
                    }

                    // QC Checked: Cap at completedQty
                    var qcCheckedInput = row.find('.row-qc-checked');
                    if (qcCheckedInput.length > 0 && bulkQcChecked !== '') {
                        var currentComp = parseFloat(completedInput.val()) || 0;
                        var targetQcChecked = parseFloat(bulkQcChecked) || 0;
                        var finalQcChecked = Math.min(targetQcChecked, currentComp);
                        qcCheckedInput.val(finalQcChecked).removeAttr('data-user-modified');
                        changed = true;
                    }

                    // QC Passed: Cap at qcChecked
                    var qcPassedInput = row.find('.row-qc-passed');
                    if (qcPassedInput.length > 0 && bulkQcPassed !== '') {
                        var currentQcChecked = parseFloat(row.find('.row-qc-checked').val()) || 0;
                        var targetQcPassed = parseFloat(bulkQcPassed) || 0;
                        var finalQcPassed = Math.min(targetQcPassed, currentQcChecked);
                        qcPassedInput.val(finalQcPassed).removeAttr('data-user-modified');
                        changed = true;
                    }

                    if (changed) {
                        calculateRow(row);
                    }
                });

                var validation = validateForm();
                
                if (!validation.valid) {
                    var errorHtml = validation.message || 'Please check the affected rows.';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Validation Error',
                            html: '<div class="text-start small">' + errorHtml + '</div>',
                            icon: 'error',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    } else {
                        alert("Validation Error: \n" + errorHtml.replace(/<br>/g, '\n'));
                    }
                }

                $('#bulkUpdateModal').modal('hide');
                
                
                // Clear bulk inputs
                $('#bulk_completed_qty').val('').removeClass('is-invalid');
                $('#bulk_wastage_qty').val('').removeClass('is-invalid');
                $('#bulk_qc_checked').val('').removeClass('is-invalid');
                $('#bulk_qc_passed').val('').removeClass('is-invalid');
                $('.bulk-qty-input').next('.invalid-feedback').text('');
                $('#btn-apply-bulk').prop('disabled', false);
            });
            var assignmentProgress = {
                @if(isset($task) && $task->assignments)
                    @foreach($task->assignments as $assign)
                        "{{ $assign->id }}": {{ (float)$assign->completed_qty + (float)$assign->wastage_qty }},
                    @endforeach
                @endif
            };

            $('#content-issue form').on('submit', function(e) {
                var statusVal = $('#status').val();
                if (statusVal === 'Completed') {
                    var hasAssignments = false;
                    var totalIssueQty = 0;
                    var totalCompletedQty = 0;

                    $('#content-issue .assignment-row input[name*="[issue_qty]"]').each(function() {
                        hasAssignments = true;
                        totalIssueQty += parseFloat($(this).val()) || 0;
                    });
                    
                    for (var id in assignmentProgress) {
                        totalCompletedQty += assignmentProgress[id];
                    }

                    if (!hasAssignments || totalIssueQty <= 0 || totalCompletedQty < totalIssueQty) {
                        // Automatically switch to 'In Progress' instead of showing error and stopping!
                        $('#status').val('In Progress').trigger('change');
                    }
                }
            });
        });
    </script>
    @endsection
@endsection


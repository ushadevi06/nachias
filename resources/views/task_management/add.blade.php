@extends('layouts.common')
@section('title', ($task ? 'Edit' : 'Add') . ' Task Management - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif    
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm erp-header-card">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h3 class="fw-bold mb-1 text-primary">{{ $task ? $task->task_no : $nextTaskNo }}</h3>
                            <p class="text-muted mb-0">
                                <span class="badge bg-label-secondary px-2 py-1 me-2">Task Execution</span>
                                <i class="ri ri-arrow-right-s-line"></i>
                                <span class="ms-2">Job Card: <span class="fw-bold text-dark">{{ $jobCard->job_card_no }}</span></span>
                            </p>
                        </div>
                        <div class="col-md-5 text-md-end mt-3 mt-md-0">
                            <div class="d-flex flex-wrap justify-content-md-end gap-2">
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
    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="sticky-sidebar">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header border-bottom py-3">
                        <h6 class="mb-0 fw-bold">Transaction Type</h6>
                    </div>
                    <div class="card-body px-0 py-2">
                        <div class="nav flex-column nav-pills custom-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <button class="nav-link active text-start py-3 px-4 d-flex align-items-center" id="tab-issue" data-bs-toggle="pill" data-bs-target="#content-issue" type="button" role="tab">
                                <i class="ri ri-upload-2-line me-2 fs-5"></i>
                                <div>
                                    <span class="d-block fw-bold">Task Issue</span>
                                    <small class="text-muted font-small">Issue materials/tasks</small>
                                </div>
                            </button>
                            <button class="nav-link text-start py-3 px-4 d-flex align-items-center" id="tab-receive" data-bs-toggle="pill" data-bs-target="#content-receive" type="button" role="tab">
                                <i class="ri ri-download-2-line me-2 fs-5"></i>
                                <div>
                                    <span class="d-block fw-bold">Task Receive</span>
                                    <small class="text-muted font-small">Receive completed work</small>
                                </div>
                            </button>
                            <button class="nav-link text-start py-3 px-4 d-flex align-items-center" id="tab-adjustment" data-bs-toggle="pill" data-bs-target="#content-adjustment" type="button" role="tab">
                                <i class="ri ri-equalizer-line me-2 fs-5"></i>
                                <div>
                                    <span class="d-block fw-bold">Task Adjustment</span>
                                    <small class="text-muted font-small">Corrections & Wastage</small>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="tab-content sdk-tab-content p-0" id="v-pills-tabContent">
                <div class="tab-pane fade show active" id="content-issue" role="tabpanel">
                    <form action="{{ $task ? route('task_management.edit', ['id' => $task->id]) : route('task_management.add') }}" method="POST" class="common-form">
                        @csrf
                        <input type="hidden" name="production_id" value="{{ $production->id ?? ($task->production_id ?? '') }}">
                        <input type="hidden" name="production_no" value="{{ $production->production_no ?? ($task->production_no ?? '') }}">
                        <input type="hidden" name="job_card_entry_id" value="{{ $jobCard->id ?? ($task->job_card_entry_id ?? '') }}">
                        <input type="hidden" name="job_card_no" value="{{ $jobCard->job_card_no ?? ($task->job_card_no ?? '') }}"> 
                        <div class="card border-0 shadow-sm section-card">
                            <div class="card-header border-bottom py-3 bg-label-primary bg-opacity-10">
                                <div class="d-flex align-items-center">
                                    <div class="section-icon bg-primary text-white me-3"><i class="ri ri-upload-2-line"></i></div>
                                    <h5 class="mb-0 fw-bold text-primary">New Task Issue</h5>
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
                                            <input type="text" class="form-control" value="{{ $jobCard->job_card_no }}" readonly>
                                            <label>Job Card No</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2 form-select @error('stage_id') is-invalid @enderror" id="stage_select" name="stage_id" data-placeholder="Select Stage">
                                                <option value="">Select Stage</option>
                                                @if(isset($stages) && $stages->count() > 0) 
                                                    @foreach($stages as $schedule)
                                                        <option value="{{ $schedule->id }}" 
                                                                {{ ($task->stage_id ?? '') == $schedule->id ? 'selected' : '' }}
                                                                data-start-date="{{ $schedule->start_date ? \Carbon\Carbon::parse($schedule->start_date)->format('Y-m-d') : '' }}"
                                                                data-qty="{{ $schedule->planned_qty ?? 0 }}"
                                                                data-services="{{ $schedule->services->map(function($s){ return ['id' => $s->productionService->id ?? '', 'name' => ($s->productionService->service_name ?? '') . ' - ' . ($s->productionService->service_code ?? '')]; })->toJson() }}">
                                                            {{ $schedule->operationStage->operation_stage_name ?? $schedule->stage }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <label>Stage *</label>
                                        </div>
                                        @error('stage_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <!-- New Services Dropdown -->
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2 form-select @error('service_ids') is-invalid @enderror" id="services_select" name="service_ids[]" multiple data-placeholder="Select Services">
                                                <!-- Populated by JS -->
                                            </select>
                                            <label>Services *</label>
                                        </div>
                                        @error('service_ids') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control flatpickr @error('issue_date') is-invalid @enderror" id="issue_date" name="issue_date" value="{{ $task->issue_date ?? '' }}">
                                            <label>Issue Date *</label>
                                        </div>
                                        @error('issue_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control flatpickr" id="due_date" name="due_date" value="{{ $task->due_date ?? '' }}">
                                            <label>Due Date</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12"><hr class="my-2"></div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2 form-select @error('issued_to') is-invalid @enderror" name="issued_to" data-placeholder="Select Employee">
                                                <option value="">Select Employee</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}" {{ ($task->issued_to ?? '') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                            <label>Issued To *</label>
                                        </div>
                                        @error('issued_to') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2 form-select" name="issue_store" id="issue_store">
                                                <option value="">Select Store</option>
                                                @if(isset($stores))
                                                    @foreach($stores as $store)
                                                        <option value="{{ $store->id }}" {{ ($task->issue_store ?? '') == $store->id ? 'selected' : '' }}>{{ $store->store_type_name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <label>Issue Store *</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2 form-select status-select @error('status') is-invalid @enderror" name="status" id="status" data-placeholder="Select or enter status">
                                                @foreach($allStatuses as $statusOption)
                                                    <option value="{{ $statusOption }}" {{ ($task->status ?? 'Planned') == $statusOption ? 'selected' : '' }}>{{ $statusOption }}</option>
                                                @endforeach
                                            </select>
                                            <label>Status *</label>
                                        </div>
                                        @error('status') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <textarea class="form-control" name="remarks" style="height: 80px;">{{ $task->remarks ?? '' }}</textarea>
                                            <label>Remarks</label>
                                        </div>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary fw-bold px-4">
                                            <i class="ri ri-save-line me-1"></i> Save Issue
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="content-receive" role="tabpanel">
                    <form action="{{ $taskReceive ? route('task_receives.add', $taskReceive->id) : route('task_receives.add') }}" method="POST" class="common-form">
                        @csrf
                        <div class="card border-0 shadow-sm section-card">
                            <div class="card-header border-bottom py-3 bg-label-success bg-opacity-10">
                                <div class="d-flex align-items-center">
                                    <div class="section-icon bg-success text-white me-3"><i class="ri ri-download-2-line"></i></div>
                                    <h5 class="mb-0 fw-bold text-success">{{ $taskReceive ? 'Update Task Receipt' : 'New Task Receipt' }}</h5>
                                </div>
                            </div>
                            <div class="card-body pt-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" name="task_receive_no" value="{{ $nextTRNo }}" readonly>
                                            <label>Task Receive ID (Auto)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="hidden" name="task_id" value="{{ $task->id ?? '' }}">
                                            <input type="text" class="form-control" value="{{ $task->task_no ?? '' }} {{ isset($task->stage->operationStage) ? '('.$task->stage->operationStage->operation_stage_name.')' : '' }}" readonly>
                                            <label>Link Task Issue ID *</label>
                                        </div>
                                        @error('task_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control flatpickr @error('received_date') is-invalid @enderror" name="received_date" value="{{ $taskReceive ? $taskReceive->received_date : date('Y-m-d') }}">
                                            <label>Receive Date *</label>
                                        </div>
                                        @error('received_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2 form-select @error('received_store') is-invalid @enderror" name="received_store">
                                                <option value="">Select Store</option>
                                                @foreach($stores as $store)
                                                    <option value="{{ $store->store_type_name }}" {{ ($taskReceive && $taskReceive->received_store == $store->store_type_name) ? 'selected' : '' }}>{{ $store->store_type_name }}</option>
                                                @endforeach
                                            </select>
                                            <label>Receipt Store *</label>
                                        </div>
                                        @error('received_store') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2 form-select" name="shift_id" data-placeholder="Select Shift">
                                                <option value="">Select Shift</option>
                                                @foreach($shifts as $shift)
                                                    <option value="{{ $shift->id }}" {{ ($taskReceive && $taskReceive->shift_id == $shift->id) ? 'selected' : '' }}>{{ $shift->shift_name }} ({{ $shift->start_time }} - {{ $shift->end_time }})</option>
                                                @endforeach
                                            </select>
                                            <label>Shift</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12"><hr class="my-2"></div>
                                    <div class="col-md-12" id="task_details_receive" style="{{ ($task || $taskReceive) ? '' : 'display:none;' }}">
                                        <div class="alert alert-info py-3 mb-3">
                                            <div class="row align-items-center">
                                                <div class="col-md-4">
                                                    <small class="fw-bold d-block text-uppercase mb-1">Employee & Stage</small>
                                                    <span class="fw-bold text-dark">
                                                        <span id="span_issued_to">{{ $task->assignee->name ?? '' }}</span> 
                                                        (<span id="span_stage_name">{{ $task->stage->operationStage->operation_stage_name ?? ($task->stage->stage ?? '') }}</span>)
                                                    </span>
                                                </div>
                                                <div class="col-md-4">
                                                    <small class="fw-bold d-block text-uppercase mb-1">Plant / provider</small>
                                                    <span id="span_plant" class="fw-bold text-primary">{{ $task->stage->scheduled_to ?? '' }}</span>
                                                </div>
                                                <div class="col-md-4">
                                                    <small class="fw-bold d-block text-uppercase mb-1">Original Issue Info</small>
                                                    <span class="text-dark">Qty: <span id="span_issued_qty" class="fw-bold">{{ $task->issue_qty ?? '' }}</span> | Date: <span id="span_issue_date" class="fw-bold">{{ $task->issue_date ?? '' }}</span></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="table-responsive border rounded bg-white">
                                            <table class="table table-sm table-hover mb-0 align-middle">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th class="ps-3" style="width: 30%;">Service Name & Code</th>
                                                        <th class="text-center text-primary" style="width: 15%;">Service Qty</th>
                                                        <th class="text-center text-success" style="width: 15%;">Good Qty</th>
                                                        <th class="text-center text-warning" style="width: 15%;">Rework Qty</th>
                                                        <th class="text-center text-danger" style="width: 15%;">Wastage Qty</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="services_quantity_table_body">
                                                    @if($taskReceive && is_array($taskReceive->received_services))
                                                        @foreach($taskReceive->received_services as $svcId => $qty)
                                                            @php
                                                                $svc = \App\Models\ProductionService::find($svcId);
                                                                $scheduleService = \App\Models\ProcessScheduleService::where('process_schedule_id', $task->stage_id)
                                                                    ->where('service_id', $svcId)
                                                                    ->first();
                                                                $serviceQty = $scheduleService ? $scheduleService->calculated_qty : 0;
                                                            @endphp
                                                            <tr>
                                                                <td class="ps-3 fw-bold">{{ $svc->service_name ?? 'N/A' }} ({{ $svc->service_code ?? 'N/A' }})</td>
                                                                <td class="text-center fw-bold text-primary">{{ $serviceQty }}</td>
                                                                <td>
                                                                    <input type="number" step="0.01" class="form-control form-control-sm text-center border-success" data-placeholder="Enter Good Qty" name="received_services[{{ $svcId }}][good_qty]" value="{{ $qty['good_qty'] ?? 0 }}">
                                                                </td>
                                                                <td>
                                                                    <input type="number" step="0.01" class="form-control form-control-sm text-center border-warning" data-placeholder="Enter Rework Qty" name="received_services[{{ $svcId }}][rework_qty]" value="{{ $qty['rework_qty'] ?? 0 }}">
                                                                </td>
                                                                <td>
                                                                    <input type="number" step="0.01" class="form-control form-control-sm text-center border-danger" data-placeholder="Enter Wastage Qty" name="received_services[{{ $svcId }}][wastage_qty]" value="{{ $qty['wastage_qty'] ?? 0 }}">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @elseif($task)  
                                                        @if($task->services && is_array($task->services))
                                                            @foreach($task->services as $svcId)
                                                                @php
                                                                    $svc = \App\Models\ProductionService::find($svcId);
                                                                    $scheduleService = \App\Models\ProcessScheduleService::where('process_schedule_id', $task->stage_id)
                                                                        ->where('service_id', $svcId)
                                                                        ->first();
                                                                    $serviceQty = $scheduleService ? $scheduleService->calculated_qty : 0;
                                                                @endphp
                                                                <tr>
                                                                    <td class="ps-3 fw-bold">{{ $svc->service_name ?? 'N/A' }} ({{ $svc->service_code ?? 'N/A' }})</td>
                                                                    <td class="text-center fw-bold text-primary">{{ $serviceQty }}</td>
                                                                    <td>
                                                                        <input type="number" step="0.01" class="form-control form-control-sm text-center border-success" name="received_services[{{ $svcId }}][good_qty]" data-placeholder="Enter Good Qty" value="{{ $serviceQty }}">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" step="0.01" class="form-control form-control-sm text-center border-warning" name="received_services[{{ $svcId }}][rework_qty]" data-placeholder="Enter Rework Qty" value="0">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" step="0.01" class="form-control form-control-sm text-center border-danger" name="received_services[{{ $svcId }}][wastage_qty]" data-placeholder="Enter Wastage Qty" value="0">
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mt-3">
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2 form-select" name="qc_status" id="qc_status">
                                                <option value="Pending" {{ ($taskReceive && $taskReceive->qc_status == 'Pending') ? 'selected' : '' }}>Pending QC</option>
                                                <option value="Pass" {{ ($taskReceive && $taskReceive->qc_status == 'Pass') ? 'selected' : '' }}>QC Pass</option>
                                                <option value="Fail" {{ ($taskReceive && $taskReceive->qc_status == 'Fail') ? 'selected' : '' }}>QC Fail</option>
                                            </select>
                                            <label>Overall QC Status</label>
                                        </div>
                                    </div>
                                    <div class="col-md-8 mt-3">
                                        <div class="form-floating form-floating-outline">
                                            <textarea class="form-control" name="remarks" style="height: 50px;">{{ $taskReceive->remarks ?? '' }}</textarea>
                                            <label>Remarks</label>
                                        </div>
                                    </div>

                                    <div class="col-12 text-end mt-3">
                                        <button type="submit" class="btn btn-primary fw-bold px-4">
                                            <i class="ri ri-save-3-line me-1"></i> {{ $taskReceive ? 'Update Task Receipt' : 'Save Task Receipt' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="content-adjustment" role="tabpanel">
                    <form action="{{ $taskAdjustment ? route('task_adjustments.add', $taskAdjustment->id) : route('task_adjustments.add') }}" method="POST" class="common-form">
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
                                            <input type="text" class="form-control" value="{{ $task->task_no ?? '' }} {{ isset($task->stage->operationStage) ? '('.$task->stage->operationStage->operation_stage_name.')' : '' }}" readonly>
                                            <label>Link Task Issue ID *</label>
                                        </div>
                                        @error('task_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="adj_jobcard_ref" value="{{ $task->job_card_no ?? '' }}" readonly>
                                            <label>Job Card Reference</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="adj_stage_ref" value="{{ isset($task->stage->operationStage) ? $task->stage->operationStage->operation_stage_name : ($task->stage->stage ?? '') }}" readonly>
                                            <label>Affected Stage</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2 form-select" id="service_id_adjustment" name="service_id" data-placeholder="Select Service">
                                                <option value="">Select Service (Optional)</option>
                                                @if($task && is_array($task->services))
                                                    @foreach($task->services as $svcId)
                                                        @php $svc = \App\Models\ProductionService::find($svcId); @endphp
                                                        @if($svc)
                                                            <option value="{{ $svc->id }}" {{ ($taskAdjustment && $taskAdjustment->service_id == $svc->id) ? 'selected' : '' }}>
                                                                {{ $svc->service_name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>
                                            <label>Service (Optional)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <select class="select2 form-select @error('adjustment_type') is-invalid @enderror" name="adjustment_type">
                                                <option value="Rework" {{ ($taskAdjustment && $taskAdjustment->adjustment_type == 'Rework') ? 'selected' : '' }}>Rework (Material Return)</option>
                                                <option value="Loss" {{ ($taskAdjustment && $taskAdjustment->adjustment_type == 'Loss') ? 'selected' : '' }}>Loss / Damage (Reduce Stock)</option>
                                                <option value="Excess" {{ ($taskAdjustment && $taskAdjustment->adjustment_type == 'Excess') ? 'selected' : '' }}>Excess Found (Add Stock)</option>
                                            </select>
                                            <label>Adjustment Type *</label>
                                        </div>
                                        @error('adjustment_type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="number" step="0.01" class="form-control @error('qty') is-invalid @enderror" name="qty" placeholder="0" value="{{ $taskAdjustment->qty ?? '' }}">
                                            <label>Adjustment Quantity *</label>
                                        </div>
                                        @error('qty') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" name="approved_by" placeholder="Supervisor Name" value="{{ $taskAdjustment->approved_by ?? '' }}">
                                            <label>Approved By</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <textarea class="form-control @error('reason') is-invalid @enderror" name="reason" style="height: 100px;" placeholder="Mandatory reason...">{{ $taskAdjustment->reason ?? '' }}</textarea>
                                            <label>Reason for Adjustment *</label>
                                        </div>
                                        @error('reason') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-warning fw-bold px-4 text-white">
                                            <i class="ri ri-alert-line me-1"></i> Post Adjustment
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#issue_date").flatpickr({
            dateFormat: "Y-m-d",
            allowInput: true,
            defaultDate: "{{ $task->issue_date ?? date('Y-m-d') }}"
        });
        $("#due_date").flatpickr({
            dateFormat: "Y-m-d",
            allowInput: true,
            defaultDate: "{{ $task->due_date ?? '' }}"
        });

        $('.select2').each(function() {
            if (!$(this).data('select2')) {
                $(this).select2({ placeholder: $(this).data('placeholder') || "Select", allowClear: true, width: '100%' });
            }
        });
        var preSelectedServices = {!! json_encode($task->services ?? []) !!};
        $('#stage_select').on('change', function(e, isInit) {
            var selected = $(this).find(':selected');
            var date = selected.data('start-date');
            var services = selected.data('services');
            
            if(date) {
                $('#issue_date').val(date);
            }

            var servicesSelect = $('#services_select');
            servicesSelect.empty();
            
            if (services && Array.isArray(services) && services.length > 0) {
                $.each(services, function(index, service) {
                    if(service.id && service.name) {
                        var isSelected = false;
                        if (isInit && preSelectedServices.includes(String(service.id))) {
                            isSelected = true;
                        } else if (isInit && preSelectedServices.includes(parseInt(service.id))) {
                            isSelected = true;
                        }
                        var newOption = new Option(service.name, service.id, isSelected, isSelected);
                        console.log(newOption);
                        servicesSelect.append(newOption);
                    }
                });
            } else {
                console.log('No services found for this stage.');
            }
            servicesSelect.trigger('change'); 
        });

        if ($('#stage_select').val()) {
            $('#stage_select').trigger('change', [true]);
        }

        $('.status-select').select2({
            tags: true,
            placeholder: "Select or enter status",
            allowClear: true
        });
        @php
            $activeTab = session('active_tab', 'issue');
            if ($errors->any() && $activeTab == 'issue') {
                $receiveKeys = ['received_date', 'received_store', 'received_services'];
                $adjKeys = ['adjustment_type', 'qty', 'reason'];
                foreach($errors->keys() as $key) {
                    if(in_array($key, $receiveKeys) || str_contains($key, 'received_services')) {
                        $activeTab = 'receive'; break;
                    }
                    if(in_array($key, $adjKeys)) {
                        $activeTab = 'adjustment'; break;
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

    /* Custom Pills Sidebar */
    .custom-pills .nav-link { 
        color: var(--erp-text); 
        border-radius: 0.5rem; 
        margin-bottom: 0.5rem; 
        transition: 0.2s; 
        border: 1px solid transparent;
    }
    .custom-pills .nav-link:hover { background-color: rgba(105, 108, 255, 0.05); color: var(--erp-primary); }
    .custom-pills .nav-link.active { 
        background-color: rgba(105, 108, 255, 0.1); 
        color: var(--erp-primary); 
        border-color: rgba(105, 108, 255, 0.2); 
    }
    
    .font-small { font-size: 0.8rem; }
    .section-card { border-radius: 0.75rem; border: none !important; box-shadow: var(--erp-shadow) !important; }
    .section-icon { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }

    .sticky-sidebar { position: sticky; top: 100px; }
    .avatar { width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; }
</style>
@endsection

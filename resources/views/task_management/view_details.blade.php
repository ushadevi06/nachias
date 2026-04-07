@extends('layouts.common')
@section('title', 'Task Details - ' . env('WEBSITE_NAME'))
@section('content')

<div class="container-xxl section-padding container-p-y">
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ url('task_management') }}" class="btn btn-sm btn-label-secondary px-3 fw-bold">
            <i class="ri ri-arrow-left-line me-1"></i> Back
        </a>
    </div>
    <div class="row">
        <div class="col-lg-12">
            
            @php
                $statusColor = 'info';
                if($task->status == 'Completed') $statusColor = 'success';
                if($task->status == 'Hold') $statusColor = 'warning';
                if($task->status == 'Planned') $statusColor = 'secondary';
                
                // Simplified progress based on status since receives is removed
                $progress = 0;
                if($task->status == 'Completed') $progress = 100;
                else if($task->status == 'In Progress') $progress = 50;
                else if($task->status == 'Hold') $progress = 25;

                $totalAssigned = $task->assignments->sum('issue_qty');
                $totalCompleted = $task->assignments->sum('completed_qty');
                $totalWastage = $task->assignments->sum('wastage_qty');
                $totalReceived = $totalCompleted + $totalWastage;
            @endphp
            {{-- 🚀 TOP BAR: ERP HEADER CARD --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <div class="d-flex align-items-center mb-1">
                                <h3 class="fw-bold text-primary mb-0 me-3">{{ $task->task_no }}</h3>
                                <span class="badge bg-label-primary rounded-pill fw-bold">{{ $task->production_no }}</span>
                            </div>
                            <p class="text-muted mb-0">
                                <i class="ri-git-merge-line me-1 text-primary"></i> Linked Job Card: <span class="fw-bold text-dark">{{ $task->job_card_no }}</span>
                            </p>
                        </div>
                        <div class="col-md-5 text-md-end mt-3 mt-md-0">
                             <div class="d-flex flex-wrap justify-content-md-end gap-2 align-items-center">
                                <span class="badge bg-label-info rounded-pill px-3 py-2 fw-bold">
                                    <i class="ri-checkbox-circle-line me-1"></i> Received: {{ (float)$totalReceived }} / {{ (float)$totalAssigned }} PCS
                                </span>
                                @if($task->due_date)
                                <span class="badge bg-label-danger rounded-pill px-3 py-2 fw-bold">
                                    <i class="ri-calendar-event-line me-1"></i> Deadline: {{ \Carbon\Carbon::parse($task->due_date)->format('d-m-Y') }}
                                </span>
                                @endif
                                <span class="badge bg-label-{{ $statusColor }} rounded-pill px-3 py-2 fw-bold">
                                    <i class="ri-loader-4-line me-1"></i> {{ $task->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <small class="text-muted fw-bold text-uppercase d-block">Overall Progress</small>
                        </div>
                        <div class="col-md-10">
                            <div class="d-flex align-items-center">
                                <div class="progress w-100 me-3" style="height: 12px; border-radius: 10px;">
                                    <div class="progress-bar bg-{{ $statusColor }} progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="fw-bold fs-5 text-{{ $statusColor }}">{{ $progress }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- 📋 LEFT CONTENT --}}
                <div class="col-lg-8">
                    {{-- Basic configuration Section --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header border-bottom py-3">
                            <h5 class="mb-0 fw-bold text-dark"><i class="ri-settings-4-line me-2 text-primary"></i>Basic Configuration</h5>
                        </div>
                        <div class="card-body pt-4">
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="text-muted extra-small fw-bold text-uppercase mb-2">Assigned Employees & Services</div>
                                    @if($task->assignments->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered align-middle mb-0">
                                                <thead class="table-light extra-small fw-bold text-uppercase text-center">
                                                    <tr>
                                                        <th>#</th>
                                                        <th class="text-start">Employee</th>
                                                        <th>Service</th>
                                                        <th>Issue Date</th>
                                                        <th>Due Date</th>
                                                        <th>Status</th>
                                                        <th>Assigned</th>
                                                        <th>Completed</th>
                                                        <th>Wastage</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($task->assignments as $i => $asgn)
                                                    <tr>
                                                        <td>{{ $i + 1 }}</td>
                                                         <td>
                                                            <div class="fw-bold">{{ $asgn->assignee->name ?? 'N/A' }}</div>
                                                            @if($asgn->assignee && $asgn->assignee->emp_id)
                                                                <small class="text-muted">ID: {{ $asgn->assignee->emp_id }}</small>
                                                            @endif
                                                        </td>
                                                        <td>{{ $asgn->service->service_name ?? 'N/A' }}</td>
                                                        <td>{{ $asgn->issue_date ? $asgn->issue_date->format('d-m-Y') : '—' }}</td>
                                                        <td>{{ $asgn->due_date ? $asgn->due_date->format('d-m-Y') : '—' }}</td>
                                                        <td><span class="badge bg-label-primary">{{ $asgn->status }}</span></td>
                                                        <td class="fw-bold">{{ (float)$asgn->issue_qty }}</td>
                                                        <td class="text-success fw-bold">{{ (float)$asgn->completed_qty }}</td>
                                                        <td class="text-danger small">{{ (float)$asgn->wastage_qty }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <span class="text-muted">No assignments recorded.</span>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted extra-small fw-bold text-uppercase mb-1">Stage</div>
                                    <div class="fw-bold">
                                        @php
                                            $stageName = $task->stage->operationStage->operation_stage_name
                                                ?? $task->operationStage->operation_stage_name
                                                ?? $task->stage->stage
                                                ?? 'N/A';
                                        @endphp
                                        {{ $stageName }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted extra-small fw-bold text-uppercase mb-1">Issue Store</div>
                                    <div class="fw-bold text-dark">{{ \App\Models\StoreType::find($task->issue_store)->store_type_name ?? 'N/A' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted extra-small fw-bold text-uppercase mb-1">Status</div>
                                    <div class="fw-bold text-{{ $statusColor }}">{{ $task->status }}</div>
                                </div>
                                <div class="col-12">
                                    <hr class="my-3">
                                    <div class="text-muted extra-small fw-bold text-uppercase mb-1">Remarks</div>
                                    <p class="text-secondary leading-relaxed mb-0">
                                        {{ $task->remarks ?: 'No remarks available.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    {{-- Metrics Section --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header border-bottom py-3">
                            <h5 class="mb-0 fw-bold text-dark"><i class="ri ri-pie-chart-line me-2 text-primary"></i>Dates Tracking</h5>
                        </div>
                        <div class="card-body pt-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="text-muted extra-small fw-bold text-uppercase mb-1">Issue Date</div>
                                    <div class="h5 fw-bold text-primary mb-0">{{ $task->issue_date ? \Carbon\Carbon::parse($task->issue_date)->format('d-m-Y') : 'N/A' }}</div>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <div class="text-muted extra-small fw-bold text-uppercase mb-1">Due Date</div>
                                    <div class="h5 fw-bold text-danger mb-0">{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d-m-Y') : 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header border-bottom py-3 bg-label-warning bg-opacity-10 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-warning"><i class="ri ri-settings-5-line me-2"></i>Task Adjustment History</h5>
                        <span class="badge bg-warning">{{ $task->adjustments->count() }} Adjustments</span>
                    </div>
                    <div class="card-body p-0">
                        @if($task->adjustments->count() > 0)
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
                                        @foreach($task->adjustments as $adj)
                                            <tr class="border-bottom">
                                                <td class="ps-3 py-3" style="width: 180px;">
                                                    <div class="fw-bold text-dark">{{ $adj->adjustment_no }}</div>
                                                    <small class="text-muted"><i class="ri-calendar-line me-1"></i>{{ $adj->created_at->format('d-m-Y') }}</small>
                                                </td>
                                                <td class="py-3">
                                                    <div class="d-flex flex-column gap-2">
                                                        @foreach($adj->items as $item)
                                                            <div class="bg-light p-2 rounded-2 border-start border-warning border-3">
                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                    <span class="fw-bold small text-dark">{{ $item->rawMaterial->name ?? 'N/A' }}</span>
                                                                    <span class="badge bg-label-{{ $item->adjustment_type == 'Excess' ? 'success' : 'danger' }} small">{{ $item->adjustment_type }}</span>
                                                                </div>
                                                                <div class="d-flex gap-3 small">
                                                                    <span>Qty: <b>{{ $item->qty }} {{ $item->uom->uom_code ?? '' }}</b></span>
                                                                    @if($item->remarks)
                                                                        <span class="text-muted italic"><i class="ri-chat-1-line me-1"></i>{{ $item->remarks }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    @if($adj->overall_reason)
                                                        <div class="mt-2 small text-muted italic p-2 bg-light rounded">
                                                            <i class="ri-question-line me-1"></i> Reason: {{ $adj->overall_reason }}
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
                        @else
                            <div class="p-5 text-center">
                                <i class="ri-tools-line fs-1 text-muted d-block mb-3"></i>
                                <h6 class="text-muted">No adjustments recorded for this task.</h6>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

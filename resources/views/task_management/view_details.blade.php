@extends('layouts.common')
@section('title', 'Task Details - ' . env('WEBSITE_NAME'))
@section('content')

<div class="container-xxl section-padding container-p-y">
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ url('task_management') }}" class="btn btn-outline-secondary  shadow-sm">
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
                            <p class="text-muted mb-0 d-flex align-items-center flex-wrap gap-2">
                                <span><i class="ri ri-git-merge-line me-1 text-primary"></i> Linked Job Card: <span class="fw-bold text-dark">{{ $task->job_card_no }}</span></span>
                                @if($task->is_additional)
                                    <span class="badge bg-warning text-dark"><i class="ri ri-add-circle-line me-1"></i> Additional Qty Batch {{ $task->job_card_fabric_detail_id ? '#' . $task->job_card_fabric_detail_id : '' }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-5 text-md-end mt-3 mt-md-0">
                             <div class="d-flex flex-wrap justify-content-md-end gap-2 align-items-center">
                                <span class="badge bg-label-info rounded-pill px-3 py-2 fw-bold">
                                    <i class="ri-checkbox-circle-line me-1"></i> Received: {{ (float)$totalReceived }} / {{ (float)$totalAssigned }} PCS
                                </span>
                                @if($task->due_date)
                                <span class="badge bg-label-danger rounded-pill px-3 py-2 fw-bold">
                                    <i class="ri ri-calendar-event-line me-1"></i> Deadline: {{ \Carbon\Carbon::parse($task->due_date)->format('d-m-Y') }}
                                </span>
                                @endif
                                <span class="badge bg-label-{{ $statusColor }} rounded-pill px-3 py-2 fw-bold">
                                    <i class="ri ri-loader-4-line me-1"></i> {{ $task->status }}
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
                <div class="col-12">
                    {{-- Basic configuration Section --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header border-bottom py-3">
                            <h5 class="mb-0 fw-bold text-dark"><i class="ri ri-settings-4-line me-2 text-primary"></i>Basic Configuration</h5>
                        </div>
                        <div class="card-body pt-4">
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="text-muted extra-small fw-bold text-uppercase">Assigned Employees & Services</div>
                                        @if($task->assignments->count() > 0)
                                            <div class="input-group input-group-sm" style="width: 250px;">
                                                <span class="input-group-text bg-transparent border-end-0"><i class="ri ri-search-line"></i></span>
                                                <input type="text" id="employeeSearchInput" class="form-control border-start-0" placeholder="Search Employee...">
                                            </div>
                                        @endif
                                    </div>
                                    @if($task->assignments->count() > 0)
                                        <div class="table-responsive" style="max-height: 80vh; overflow-y: auto;">
                                            <table class="table table-sm table-bordered align-middle mb-0">
                                                <thead class="table-light extra-small text-center align-middle" style="position: sticky; top: 0; z-index: 10;">
                                                    <tr style="background-color: #f8f9fa;">
                                                        <th rowspan="2" class="text-start border-end shadow-sm" style="position: sticky; left: 0; top: 0; z-index: 11; background-color: #f8f9fa; min-width: 250px;">Employee & Service</th>
                                                        <th rowspan="2" class="border-end">Stage Start & End Date</th>
                                                        <th rowspan="2" class="border-end">Hrs</th>
                                                        <th rowspan="2" class="border-end">Status</th>
                                                        <th colspan="5" class="border-start border-end bg-primary bg-opacity-10 text-primary">Production Details</th>
                                                        <th colspan="4" class="border-end bg-info bg-opacity-10 text-info">QC Tracking</th>
                                                        <th colspan="3" class="bg-success bg-opacity-10 text-success">Financials & Progress</th>
                                                    </tr>
                                                    <tr class="fw-bold text-uppercase" style="background-color: #f8f9fa; position: sticky; top: 35px; z-index: 9;">
                                                        <!-- Production -->
                                                        <th class="border-start">Assign</th>
                                                        <th>Done</th>
                                                        <th>In-Prog</th>
                                                        <th>Pending</th>
                                                        <th class="border-end">Waste</th>
                                                        <!-- QC -->
                                                        <th>Check</th>
                                                        <th>Pass</th>
                                                        <th>Reject</th>
                                                        <th class="border-end">Status</th>
                                                        <!-- Financials -->
                                                        <th>Progress</th>
                                                        <th>Rate (₹)</th>
                                                        <th>Earn (₹)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($task->assignments as $i => $asgn)
                                                    <tr class="employee-row">
                                                        <td class="text-start border-end shadow-sm" style="position: sticky; left: 0; z-index: 1; background-color: #fff;">
                                                            <div class="d-flex align-items-center">
                                                                <div class="me-2 text-muted small">#{{ $i + 1 }}</div>
                                                                <div>
                                                                    <div class="fw-bold text-dark">{{ $asgn->assignee->name ?? 'N/A' }}</div>
                                                                    <div class="small text-muted">{{ $asgn->service->service_name ?? 'N/A' }} 
                                                                        @if($asgn->assignee && $asgn->assignee->emp_id) <span class="ms-1 border-start ps-1">(ID: {{ $asgn->assignee->emp_id }})</span> @endif
                                                                    </div>
                                                                    @if(!empty($asgn->remarks))
                                                                        <div class="small text-info mt-1" style="font-size: 0.75rem;"><i class="ri-message-2-line align-middle"></i> <strong>Remarks:</strong> {{ $asgn->remarks }}</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="border-end text-nowrap">
                                                            <div class="small text-success">Start: {{ $asgn->issue_date ? $asgn->issue_date->format('d-m-Y') : '—' }}</div>
                                                            <div class="small text-danger">End: {{ $asgn->due_date ? $asgn->due_date->format('d-m-Y') : '—' }}</div>
                                                        </td>
                                                        <td class="border-end text-center fw-bold">
                                                            {{ $asgn->total_hrs ?? '-' }}
                                                        </td>
                                                        <td class="border-end">
                                                            <span class="badge bg-label-primary">{{ $asgn->status }}</span>
                                                        </td>
                                                        @php
                                                            $assignedQty = (float)$asgn->issue_qty;
                                                            $completedQty = (float)($asgn->completed_qty ?? 0);
                                                            $wastageQty = (float)($asgn->wastage_qty ?? 0);
                                                            $inProgressQty = (float)($asgn->inprogress_qty ?? 0);
                                                            $pendingQty = max(0, $assignedQty - $completedQty - $wastageQty);
                                                            
                                                            $qcChecked = (float)($asgn->qc_checked_qty ?? 0);
                                                            $qcPassed = (float)($asgn->qc_passed_qty ?? 0);
                                                            $qcRejected = (float)($asgn->qc_rejected_qty ?? 0);
                                                            $qcStatus = $asgn->qc_status ?? 'N/A';
                                                            
                                                            $progressPercent = ($assignedQty > 0) ? min(100, round(($completedQty / $assignedQty) * 100)) : 0;
                                                        @endphp
                                                        <td class="fw-bold border-start">{{ $assignedQty ?: '0' }}</td>
                                                        <td class="text-success fw-bold">{{ $completedQty ?: '0' }}</td>
                                                        <td class="text-warning fw-bold">{{ $inProgressQty ?: '0' }}</td>
                                                        <td class="text-warning fw-bold">{{ $pendingQty ?: '0' }}</td>
                                                        <td class="text-danger fw-bold border-end">{{ $wastageQty ?: '0' }}</td>
                                                        <td class="text-info fw-bold">{{ $qcChecked ?: '0' }}</td>
                                                        <td class="text-success fw-bold">{{ $qcPassed ?: '0' }}</td>
                                                        <td class="text-danger fw-bold">{{ $qcRejected ?: '0' }}</td>
                                                        <td class="border-end text-nowrap">
                                                            <span class="badge {{ $qcStatus == 'QC Completed' ? 'bg-label-success' : ($qcStatus == 'In QC' ? 'bg-label-info' : 'bg-label-secondary') }}">
                                                                {{ $qcStatus }}
                                                            </span>
                                                        </td>
                                                        <td class="text-nowrap">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <div class="progress w-100" style="height: 6px; min-width: 60px;">
                                                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progressPercent }}%"></div>
                                                                </div>
                                                                <small class="text-success fw-bold">{{ $progressPercent }}%</small>
                                                            </div>
                                                        </td>
                                                        <td class="text-muted fw-bold">{{ number_format((float)$asgn->unit_rate, 2) }}</td>
                                                        <td class="fw-bold text-success">{{ number_format((float)$asgn->total_cost, 2) }}</td>
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
                                    <div class="text-muted extra-small fw-bold text-uppercase mb-1">Issued By</div>
                                    <div class="fw-bold text-dark">
                                        {{ $task->issuedBy ? $task->issuedBy->name . ($task->issuedBy->emp_id ? ' ('.$task->issuedBy->emp_id.')' : '') : 'N/A' }}
                                    </div>
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
                                                    <small class="text-muted"><i class="ri ri-calendar-line me-1"></i>{{ $adj->created_at->format('d-m-Y') }}</small>
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
                        @else
                            <div class="p-5 text-center">
                                <i class="ri ri-tools-line fs-1 text-muted d-block mb-3"></i>
                                <h6 class="text-muted">No adjustments recorded for this task.</h6>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('employeeSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('.employee-row');
            
            rows.forEach(row => {
                const employeeCell = row.querySelector('td:nth-child(1)');
                if (employeeCell) {
                    const textContent = employeeCell.textContent.toLowerCase();
                    if (textContent.includes(filter)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });
    }
});
</script>

@endsection

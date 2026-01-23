@extends('layouts.common')
@section('title', 'Task Details - ' . env('WEBSITE_NAME'))
@section('content')

<div class="container-xxl section-padding container-p-y">
    <div class="row">
        <div class="col-lg-12">
            
            @php
                $progress = ($task->status == 'Completed' ? 100 : ($task->status == 'In Progress' ? 50 : ($task->status == 'Hold' ? 25 : 0)));
                $statusColor = 'info';
                if($task->status == 'Completed') $statusColor = 'success';
                if($task->status == 'Hold') $statusColor = 'warning';
                if($task->status == 'Planned') $statusColor = 'secondary';
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
                            <div class="d-flex flex-wrap justify-content-md-end gap-2">
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

            @php
                $totalGood = $task->receives->sum('good_qty');
                $totalRework = $task->receives->sum('rework_qty');
                $totalWastage = $task->receives->sum('wastage_qty');
                $progress = $task->issue_qty > 0 ? min(100, round(($totalGood / $task->issue_qty) * 100)) : 0;
                
                $statusColor = 'info';
                if($task->status == 'Completed') $statusColor = 'success';
                if($task->status == 'Hold') $statusColor = 'warning';
                if($task->status == 'Planned') $statusColor = 'secondary';
            @endphp

            {{-- 📊 KPI TILES ROW --}}
            <div class="row mb-4 g-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <small class="text-muted fw-bold text-uppercase d-block mb-1">Issued Qty</small>
                            <h4 class="fw-bold mb-0">{{ number_format($task->issue_qty, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm border-start border-success border-3">
                        <div class="card-body">
                            <small class="text-success fw-bold text-uppercase d-block mb-1">Total Good</small>
                            <h4 class="fw-bold mb-0 text-success">{{ number_format($totalGood, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm border-start border-warning border-3">
                        <div class="card-body">
                            <small class="text-warning fw-bold text-uppercase d-block mb-1">Total Rework</small>
                            <h4 class="fw-bold mb-0 text-warning">{{ number_format($totalRework, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm border-start border-danger border-3">
                        <div class="card-body">
                            <small class="text-danger fw-bold text-uppercase d-block mb-1">Total Waste</small>
                            <h4 class="fw-bold mb-0 text-danger">{{ number_format($totalWastage, 2) }}</h4>
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
                                    <div class="text-muted extra-small fw-bold text-uppercase mb-1">Task Services</div>
                                    <div class="h5 fw-bold text-dark">
                                        @if($task->services)
                                            @php
                                                $serviceNames = \Illuminate\Support\Facades\DB::table('production_services')
                                                    ->whereIn('id', $task->services)
                                                    ->pluck('service_name')
                                                    ->implode(', ');
                                            @endphp
                                            {{ $serviceNames }}
                                        @else
                                            No services assigned
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted extra-small fw-bold text-uppercase mb-1">Stage</div>
                                    <div class="fw-bold">{{ $task->stage->operationStage->operation_stage_name ?? 'N/A' }}</div>
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

                    {{-- 📜 RECEIPT HISTORY SECTION --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header border-bottom py-3 bg-label-success bg-opacity-10 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold text-success"><i class="ri-history-line me-2"></i>Task Receipt History</h5>
                            <span class="badge bg-success">{{ $task->receives->count() }} Receipts</span>
                        </div>
                        <div class="card-body p-0">
                            @if($task->receives->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light extra-small fw-bold text-uppercase">
                                            <tr>
                                                <th class="ps-3">Receipt No/Date</th>
                                                <th>Service Detailed Entry</th>
                                                <th class="text-center">QC</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($task->receives as $receipt)
                                                <tr class="border-bottom">
                                                    <td class="ps-3 py-3" style="width: 180px;">
                                                        <div class="fw-bold text-dark">{{ $receipt->task_receive_no }}</div>
                                                        <small class="text-muted"><i class="ri-calendar-line me-1"></i>{{ \Carbon\Carbon::parse($receipt->received_date)->format('d-m-Y') }}</small>
                                                    </td>
                                                    <td class="py-3">
                                                        @if($receipt->received_services && is_array($receipt->received_services))
                                                            <div class="d-flex flex-column gap-2">
                                                                @foreach($receipt->received_services as $svcId => $qtyData)
                                                                    @php
                                                                        $svc = \App\Models\ProductionService::find($svcId);
                                                                    @endphp
                                                                    <div class="bg-light p-2 rounded-2 border-start border-primary border-3">
                                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                                            <span class="fw-bold small text-primary">{{ $svc->service_name ?? 'Service ID: '.$svcId }}</span>
                                                                        </div>
                                                                        <div class="d-flex gap-3 small">
                                                                            <span class="text-success"><i class="ri-checkbox-circle-line me-1"></i>Good: <b>{{ $qtyData['good_qty'] }}</b></span>
                                                                            <span class="text-warning"><i class="ri-tools-line me-1"></i>Rework: <b>{{ $qtyData['rework_qty'] }}</b></span>
                                                                            <span class="text-danger"><i class="ri-close-circle-line me-1"></i>Waste: <b>{{ $qtyData['wastage_qty'] }}</b></span>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div class="small">
                                                                <span class="text-success me-2">Good: {{ $receipt->good_qty }}</span>
                                                                <span class="text-warning me-2">Rework: {{ $receipt->rework_qty }}</span>
                                                                <span class="text-danger">Wastage: {{ $receipt->wastage_qty }}</span>
                                                            </div>
                                                        @endif
                                                        @if($receipt->remarks)
                                                            <div class="mt-2 small text-muted italic">
                                                                <i class="ri-chat-1-line me-1"></i> {{ $receipt->remarks }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="text-center py-3">
                                                        @php
                                                            $qcColor = 'secondary';
                                                            if($receipt->qc_status == 'Pass') $qcColor = 'success';
                                                            if($receipt->qc_status == 'Fail') $qcColor = 'danger';
                                                        @endphp
                                                        <span class="badge bg-label-{{ $qcColor }} rounded-pill">{{ $receipt->qc_status }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="p-5 text-center">
                                    <i class="ri-file-search-line fs-1 text-muted d-block mb-3"></i>
                                    <h6 class="text-muted">No receipts recorded for this task yet.</h6>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- 📋 RIGHT SIDEBAR --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header border-bottom py-3">
                            <h6 class="mb-0 fw-bold">Staff Responsibility</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                                <div class="avatar avatar-lg me-3">
                                    <span class="avatar-initial rounded-circle bg-label-primary fs-4">
                                        {{ strtoupper(substr($task->assignee->name ?? '', 0, 2)) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="text-muted extra-small fw-bold text-uppercase mb-0">Target Assignee</div>
                                    <div class="fw-bold text-dark fs-5">{{ $task->assignee->name ?? 'Unassigned' }}</div>
                                    <small class="text-muted">{{ $task->assignee->email ?? '' }}</small>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="text-muted extra-small fw-bold text-uppercase mb-1">Assigned By</div>
                                <div class="fw-bold text-dark">{{ \App\Models\User::find($task->created_by)->name ?? 'System' }}</div>
                            </div>
                            <div class="mb-4 text-muted small">
                                <div class="text-muted extra-small fw-bold text-uppercase mb-1">Last Updated</div>
                                <div class="fw-bold">{{ $task->updated_at->format('d-m-Y h:i A') }}</div>
                            </div>
                        </div>
                    </div>
                    <a href="{{ url('task_management') }}" class="btn btn-label-secondary w-100 py-2">
                        <i class="ri ri-arrow-left-line me-2"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

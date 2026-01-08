@extends('layouts.common')
@section('title', 'Task Details - ' . env('WEBSITE_NAME'))
@section('content')

<div class="container-xxl section-padding container-p-y">
    <div class="row">
        <div class="col-lg-12">
            
            {{-- 🚀 TOP BAR: ERP HEADER CARD --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <div class="d-flex align-items-center mb-1">
                                <h3 class="fw-bold text-primary mb-0 me-3">TASK-2025-001</h3>
                                <span class="badge bg-label-primary rounded-pill fw-bold">Production Module</span>
                            </div>
                            <p class="text-muted mb-0">
                                <i class="ri-git-merge-line me-1 text-primary"></i> Linked Job Card: <span class="fw-bold text-dark">JC20250924-001-K</span>
                            </p>
                        </div>
                        <div class="col-md-5 text-md-end mt-3 mt-md-0">
                            <div class="d-flex flex-wrap justify-content-md-end gap-2">
                                <span class="badge bg-label-danger rounded-pill px-3 py-2 fw-bold">
                                    <i class="ri-calendar-event-line me-1"></i> Deadline: 30-09-2025
                                </span>
                                <span class="badge bg-label-warning rounded-pill px-3 py-2 fw-bold">
                                    <i class="ri-error-warning-line me-1"></i> High Priority
                                </span>
                                <span class="badge bg-label-info rounded-pill px-3 py-2 fw-bold">
                                    <i class="ri-loader-4-line me-1"></i> In Progress
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 📊 KPI TILES ROW --}}
            <div class="row mb-4 g-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-muted fw-bold text-uppercase d-block mb-1">Total Quantity</small>
                                    <h4 class="fw-bold mb-0">1,000</h4>
                                </div>
                                <div class="avatar bg-label-primary p-2 rounded-3">
                                    <i class="ri-stack-line fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-muted fw-bold text-uppercase d-block mb-1">Completed</small>
                                    <h4 class="fw-bold mb-0 text-success">800</h4>
                                </div>
                                <div class="avatar bg-label-success p-2 rounded-3">
                                    <i class="ri-checkbox-circle-line fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-muted fw-bold text-uppercase d-block mb-1">Pending</small>
                                    <h4 class="fw-bold mb-0 text-warning">200</h4>
                                </div>
                                <div class="avatar bg-label-warning p-2 rounded-3">
                                    <i class="ri-timer-2-line fs-3"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <small class="text-muted fw-bold text-uppercase d-block mb-1">Efficiency</small>
                            <div class="d-flex align-items-center">
                                <div class="progress w-100 me-2" style="height: 10px; border-radius: 10px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="fw-bold text-primary">80%</span>
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
                                    <div class="text-muted extra-small fw-bold text-uppercase mb-1">Detailed Task Headline</div>
                                    <div class="h5 fw-bold text-dark">Cutting for Order #SO-1001 - Fabric Quality Setup</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted extra-small fw-bold text-uppercase mb-1">Execution Mode</div>
                                    <div class="fw-bold">Scheduled</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted extra-small fw-bold text-uppercase mb-1">Task Priority</div>
                                    <div class="fw-bold text-danger"><i class="ri-error-warning-fill me-1"></i>High Severity</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-muted extra-small fw-bold text-uppercase mb-1">Status</div>
                                    <div class="fw-bold text-info">In Progress</div>
                                </div>
                                <div class="col-12">
                                    <hr class="my-3">
                                    <div class="text-muted extra-small fw-bold text-uppercase mb-1">Task Description</div>
                                    <p class="text-secondary leading-relaxed mb-0">
                                        Inspect the incoming fabric for color consistency, weight, and defects. Ensure all rolls match the approved sample swatch before issuing to the cutting department.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Metric Tracking Section --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header border-bottom py-3">
                            <h5 class="mb-0 fw-bold text-dark"><i class="ri-pie-chart-line me-2 text-primary"></i>Metric Tracking</h5>
                        </div>
                        <div class="card-body pt-4">
                            <div class="row g-4">
                                <div class="col-md-3">
                                    <div class="text-muted extra-small fw-bold text-uppercase mb-1">Planned Hours</div>
                                    <div class="h5 fw-bold text-primary mb-0">8.0 hrs</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-muted extra-small fw-bold text-uppercase mb-1">Actual Hours</div>
                                    <div class="h5 fw-bold text-dark mb-0">9.5 hrs</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-muted extra-small fw-bold text-uppercase mb-1">Overtime</div>
                                    <div class="h5 fw-bold text-danger mb-0">+1.5 hrs</div>
                                </div>
                                <div class="col-md-3 text-md-end">
                                    <div class="text-muted extra-small fw-bold text-uppercase mb-1">Target Deadline</div>
                                    <div class="fw-bold">30th Sep 2025</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Logs & Documentation --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header border-bottom py-3">
                            <h5 class="mb-0 fw-bold text-dark"><i class="ri-history-line me-2 text-primary"></i>Logs & Documentation</h5>
                        </div>
                        <div class="card-body pt-4">
                            <div class="p-3 bg-label-secondary bg-opacity-10 rounded-3 border-start border-3 border-primary mb-4">
                                <div class="text-muted extra-small fw-bold text-uppercase mb-1">Internal Remarks / Shift Report</div>
                                <p class="mb-0 text-dark font-italic">"Inspected 8 rolls today. Shading variation found in Roll #4, reported to management. GSM testing is within tolerance limits (178-182)."</p>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center text-success">
                                    <i class="ri-shield-check-line me-2 fs-4"></i>
                                    <span class="fw-bold">Evidence Verified by SuperAdmin</span>
                                </div>
                                <button class="btn btn-sm btn-outline-primary"><i class="ri-attachment-line me-1"></i>View Proof</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 📋 RIGHT SIDEBAR --}}
                <div class="col-lg-4">
                    {{-- Staff Responsibility --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header border-bottom py-3">
                            <h6 class="mb-0 fw-bold">Staff Responsibility</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4 pb-2 border-bottom">
                                <div class="avatar avatar-lg me-3">
                                    <span class="avatar-initial rounded-circle bg-label-primary fs-4">RK</span>
                                </div>
                                <div>
                                    <div class="text-muted extra-small fw-bold text-uppercase mb-0">Target Assignee</div>
                                    <div class="fw-bold text-dark fs-5">Ramesh Kumar</div>
                                    <small class="text-muted">Senior QC Lead</small>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="text-muted extra-small fw-bold text-uppercase mb-1">Assigned By</div>
                                <div class="fw-bold text-dark">Admin</div>
                            </div>
                            <div class="mb-4">
                                <div class="text-muted extra-small fw-bold text-uppercase mb-1">Assignment Start Date</div>
                                <div class="fw-bold text-primary">31-12-2025</div>
                            </div>
                            <div class="p-3 bg-label-primary bg-opacity-10 rounded-3 border">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="ri-lock-unlock-line me-2 text-primary"></i>
                                    <span class="fw-bold text-primary">Delegation Enabled</span>
                                </div>
                                <small class="text-muted d-block">Scope: Update Progress, Upload Logs</small>
                            </div>
                        </div>
                    </div>

                    {{-- Process Flow --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header border-bottom py-3">
                            <h6 class="mb-0 fw-bold">Recent Updates</h6>
                        </div>
                        <div class="card-body p-4">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-4 d-flex align-items-start">
                                    <div class="avatar avatar-sm bg-label-success p-2 rounded-circle me-3">
                                        <i class="ri-check-line"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-bold text-dark">Quantity Updated</h6>
                                        <small class="text-muted d-block">10:15 AM &bull; By System</small>
                                    </div>
                                </li>
                                <li class="d-flex align-items-start">
                                    <div class="avatar avatar-sm bg-label-primary p-2 rounded-circle me-3">
                                        <i class="ri-add-line"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-bold text-dark">Task Created</h6>
                                        <small class="text-muted d-block">09:00 AM &bull; By Admin</small>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <a href="{{ url('task_management') }}" class="btn btn-label-secondary w-100 py-2">
                        <i class="ri-arrow-left-line me-2"></i> Back to
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

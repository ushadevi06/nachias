@extends('layouts.common')

@section('content')
<div class="container-xxl section-padding">
    
    {{-- 🚀 TOP BAR: ERP HEADER --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm erp-header-card">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h3 class="fw-bold mb-1 text-primary">TASK-2025-001</h3>
                            <p class="text-muted mb-0">
                                <span class="badge bg-label-secondary px-2 py-1 me-2">Production Module</span>
                                <i class="ri ri-arrow-right-s-line"></i>
                                <span class="ms-2">Job Card: <span class="fw-bold text-dark">JC20250924-001-K</span></span>
                            </p>
                        </div>
                        <div class="col-md-5 text-md-end mt-3 mt-md-0">
                            <div class="d-flex flex-wrap justify-content-md-end gap-2">
                                <span class="badge bg-label-danger rounded-pill px-3 py-2 fw-bold">
                                    <i class="ri ri-calendar-event-line me-1"></i> Deadline: 30-09-2025
                                </span>
                                <span class="badge bg-label-warning rounded-pill px-3 py-2 fw-bold">
                                    <i class="ri ri-error-warning-line me-1"></i> High Priority
                                </span>
                                <span class="badge bg-label-info rounded-pill px-3 py-2 fw-bold">
                                    <i class="ri ri-loader-4-line me-1"></i> In Progress
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 📊 KPI TILES ROW --}}
    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm kpi-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted fw-bold text-uppercase d-block mb-1">Total Quantity</small>
                            <h4 class="fw-bold mb-0" id="kpi_total">1,000</h4>
                        </div>
                        <div class="avatar bg-label-primary p-2 rounded-3">
                            <i class="ri ri-stack-line fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm kpi-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted fw-bold text-uppercase d-block mb-1">Completed</small>
                            <h4 class="fw-bold mb-0 text-success" id="kpi_completed">800</h4>
                        </div>
                        <div class="avatar bg-label-success p-2 rounded-3">
                            <i class="ri ri-checkbox-circle-line fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm kpi-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted fw-bold text-uppercase d-block mb-1">Pending</small>
                            <h4 class="fw-bold mb-0 text-warning" id="kpi_pending">200</h4>
                        </div>
                        <div class="avatar bg-label-warning p-2 rounded-3">
                            <i class="ri ri-timer-2-line fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm kpi-card">
                <div class="card-body d-flex align-items-center py-2">
                    <div class="progress-ring-container me-3">
                        <svg class="progress-ring" width="50" height="50">
                            <circle class="progress-ring__circle" stroke="#696cff" stroke-width="4" fill="transparent" r="20" cx="25" cy="25"/>
                        </svg>
                        <span class="progress-ring-text fw-bold" id="progress_percent_text">80%</span>
                    </div>
                    <div>
                        <small class="text-muted fw-bold text-uppercase d-block">Completion</small>
                        <small class="text-success extra-small fw-bold">Ahead of scale</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- 📋 LEFT SIDEBAR: STEPPER & TIMELINE --}}
        <div class="col-lg-4 col-xl-3 mb-4">
            <div class="sticky-sidebar">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header border-bottom py-3">
                        <h6 class="mb-0 fw-bold">Process Flow</h6>
                    </div>
                    <div class="card-body px-0 py-2">
                        <ul class="process-stepper">
                            <li class="active" data-section="basic-info">
                                <div class="step-indicator"><i class="ri ri-settings-4-line"></i></div>
                                <div class="step-label">Basic Configuration</div>
                            </li>
                            <li data-section="assignment">
                                <div class="step-indicator"><i class="ri ri-user-follow-line"></i></div>
                                <div class="step-label">Staff Assignment</div>
                            </li>
                            <li data-section="tracking">
                                <div class="step-indicator"><i class="ri ri-pie-chart-line"></i></div>
                                <div class="step-label">Production Tracking</div>
                            </li>
                            <li data-section="finalization">
                                <div class="step-indicator"><i class="ri ri-history-line"></i></div>
                                <div class="step-label">Status Update</div>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">Activity Timeline</h6>
                        <i class="ri ri-refresh-line text-muted extra-small"></i>
                    </div>
                    <div class="card-body py-4">
                        <div class="activity-timeline">
                            <div class="timeline-item">
                                <div class="timeline-icon bg-label-primary"><i class="ri ri-add-line"></i></div>
                                <div class="timeline-content">
                                    <p class="mb-1 fw-bold">Task Created</p>
                                    <small class="text-muted d-block">10:00 AM &bull; By Sarah Admin</small>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-icon bg-label-info"><i class="ri ri-user-shared-line"></i></div>
                                <div class="timeline-content">
                                    <p class="mb-1 fw-bold">Assigned to Mike Supervisor</p>
                                    <small class="text-muted d-block">10:15 AM &bull; By System</small>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-icon bg-label-success"><i class="ri ri-checkbox-circle-line"></i></div>
                                <div class="timeline-content">
                                    <p class="mb-1 fw-bold">Quantity Update: 800 Completed</p>
                                    <small class="text-muted d-block">02:30 PM &bull; By Mike.S</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 📝 MAIN CONTENT: FORM SECTIONS --}}
        <div class="col-lg-8 col-xl-9">
            <form id="taskForm">
                
                {{-- SECTION 1: BASIC INFO --}}
                <div class="card border-0 shadow-sm mb-4 section-card" id="basic-info">
                    <div class="card-header border-bottom py-3">
                        <div class="d-flex align-items-center">
                            <div class="section-icon bg-primary text-white me-3">1</div>
                            <h5 class="mb-0 fw-bold">Basic Configuration</h5>
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" value="TASK-2025-001" readonly />
                                    <label>Task Reference ID</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select">
                                        <option value="1">JC20250924-001-K</option>
                                        <option value="2">JC20250924-005-M</option>
                                    </select>
                                    <label>Linked Job Card *</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" placeholder="Enter headline" value="Cutting for Order #SO-1001" />
                                    <label>Detailed Task Headline</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" id="task_type">
                                        <option value="Scheduled" selected>Scheduled</option>
                                        <option value="Manual">Manual</option>
                                        <option value="Urgent">Urgent</option>
                                    </select>
                                    <label for="task_type">Execution Mode</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" id="priority">
                                        <option value="Low">Low</option>
                                        <option value="Medium">Medium</option>
                                        <option value="High" selected>High Severity</option>
                                    </select>
                                    <label for="priority">Task Priority</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: ASSIGNMENT --}}
                <div class="card border-0 shadow-sm mb-4 section-card" id="assignment">
                    <div class="card-header border-bottom py-3">
                        <div class="d-flex align-items-center">
                            <div class="section-icon bg-info text-white me-3">2</div>
                            <h5 class="mb-0 fw-bold">Staff Responsibility</h5>
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" value="Sarah Admin" readonly />
                                    <label>Assigned By</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select">
                                        <optgroup label="Supervisor">
                                            <option value="1" selected>Mike Supervisor</option>
                                            <option value="2">John Lead</option>
                                        </optgroup>
                                        <optgroup label="Operator">
                                            <option value="3">Davis Oper</option>
                                        </optgroup>
                                    </select>
                                    <label>Target Assignee *</label>
                                </div>
                            </div>
                            <div class="col-md-4" id="start_date_container">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control date_picker" id="start_date" placeholder="Start Date" value="31-12-2025" />
                                    <label for="start_date">Assignment Start Date *</label>
                                </div>
                            </div>
                            
                            <div class="col-12 mt-4">
                                <div class="p-4 bg-light rounded-3 border">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-white p-2 rounded-circle me-3 shadow-sm">
                                                <i class="ri ri-user-shared-2-line fs-4 text-primary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold">Enable Delegation</h6>
                                                <small class="text-muted">Allow the assignee to delegate specific permissions</small>
                                            </div>
                                        </div>
                                        <div class="form-check form-switch p-0 m-0">
                                            <input class="form-check-input ms-0" type="checkbox" id="enable_delegation" style="width: 3.5em; height: 1.75em;">
                                        </div>
                                    </div>
                                    
                                    <div id="delegation_options" style="display: none;">
                                        <hr class="my-4">
                                        <div class="row g-4 animate__animated animate__fadeIn">
                                            <div class="col-12">
                                                <small class="text-muted fw-bold text-uppercase d-block mb-3">Delegation Scope (Check all that apply)</small>
                                                <div class="d-flex flex-wrap gap-4">
                                                    <div class="form-check custom-option custom-option-basic">
                                                      <label class="form-check-label custom-option-content" for="scope1">
                                                        <input class="form-check-input" type="checkbox" id="scope1" checked disabled />
                                                        <span class="custom-option-header"><span class="h6 mb-0">View Only</span></span>
                                                      </label>
                                                    </div>
                                                    <div class="form-check custom-option custom-option-basic">
                                                      <label class="form-check-label custom-option-content" for="scope2">
                                                        <input class="form-check-input" type="checkbox" id="scope2" />
                                                        <span class="custom-option-header"><span class="h6 mb-0">Update Progress</span></span>
                                                      </label>
                                                    </div>
                                                    <div class="form-check custom-option custom-option-basic">
                                                      <label class="form-check-label custom-option-content" for="scope3">
                                                        <input class="form-check-input" type="checkbox" id="scope3" />
                                                        <span class="custom-option-header"><span class="h6 mb-0">Reassign Task</span></span>
                                                      </label>
                                                    </div>
                                                    <div class="form-check custom-option custom-option-basic">
                                                      <label class="form-check-label custom-option-content" for="scope4">
                                                        <input class="form-check-input" type="checkbox" id="scope4" />
                                                        <span class="custom-option-header"><span class="h6 mb-0">Upload Logs</span></span>
                                                      </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating form-floating-outline border-primary">
                                                    <input type="text" class="form-control date_picker" placeholder="Valid Till" />
                                                    <label>Delegation Valid Till</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 d-flex align-items-center">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="auto_revoke" checked>
                                                    <label class="form-check-label fw-bold" for="auto_revoke">Auto revoke permission after expiry</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: TRACKING --}}
                <div class="card border-0 shadow-sm mb-4 section-card" id="tracking">
                    <div class="card-header border-bottom py-3">
                        <div class="d-flex align-items-center">
                            <div class="section-icon bg-success text-white me-3">3</div>
                            <h5 class="mb-0 fw-bold">Metric Tracking</h5>
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control date_picker" value="30-09-2025" />
                                    <label>Target Deadline</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control" id="total_qty" value="1000" />
                                    <label>Total Quantity</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control" id="completed_qty" value="800" />
                                    <label>Quantity Completed</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control bg-light" id="pending_qty" value="200" readonly />
                                    <label>Quantity Pending</label>
                                </div>
                            </div>
                            
                            <div class="col-12 mt-4">
                                <div class="p-3 border rounded-3 bg-label-primary bg-opacity-10">
                                    <div class="d-flex justify-content-between align-items-end mb-2">
                                        <h6 class="mb-0 fw-bold text-primary">Efficiency Indicator</h6>
                                        <span class="fw-bold text-primary" id="eff_percent">80%</span>
                                    </div>
                                    <div class="progress" style="height: 12px;">
                                        <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" id="eff_bar" style="width: 80%"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 mt-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control" id="planned_hours" value="8" />
                                    <label>Planned Hours</label>
                                </div>
                            </div>
                            <div class="col-md-4 mt-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control" id="actual_hours" value="9" />
                                    <label>Actual Hours</label>
                                </div>
                            </div>
                            <div class="col-md-4 mt-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control bg-light" id="overtime_hours" value="1" readonly />
                                    <label>Overtime (Hours)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 4: LOGS & DOCUMENTATION --}}
                <div class="card border-0 shadow-sm mb-5 section-card" id="finalization">
                    <div class="card-header border-bottom py-3">
                        <div class="d-flex align-items-center">
                            <div class="section-icon bg-dark text-white me-3">4</div>
                            <h5 class="mb-0 fw-bold">Logs & Documentation</h5>
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" id="official_status">
                                        <option value="Not Started">Not Started</option>
                                        <option value="In Progress" selected>In Progress</option>
                                        <option value="Blocked">Blocked</option>
                                        <option value="On Hold">On Hold</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                    <label>Official Status *</label>
                                </div>
                            </div>
                            <div class="col-md-6" id="blocker_reason_container" style="display: none;">
                                <div class="form-floating form-floating-outline border-danger">
                                    <select class="select2 form-select">
                                        <option value="Machine Breakdown">Machine Breakdown</option>
                                        <option value="Material Shortage">Material Shortage</option>
                                        <option value="Manpower Issue">Manpower Issue</option>
                                        <option value="Power Issue">Power Issue</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    <label class="text-danger fw-bold">Blocker Reason *</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-3 border rounded-3 bg-light">
                                    <label class="mb-2 d-block fw-bold text-muted small">Supporting Proof (PDF / Image upload)</label>
                                    <input type="file" class="form-control" id="supportProof">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control" placeholder="Reporting info..." style="height: 120px"></textarea>
                                    <label>Internal Remarks / Shift Report</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 🏁 BOTTOM ACTIONS --}}
                <div class="d-flex justify-content-between align-items-center mb-5 sticky-bottom-actions py-3 bg-white border-top">
                    <a href="{{ url('task_management') }}" class="btn btn-outline-secondary px-4 py-2">
                        <i class="ri ri-close-line me-2"></i> Discard Task
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg px-5 py-2 fw-bold shadow" id="finalize_btn" disabled>
                        <i class="ri ri-check-double-line me-2"></i> Finalize Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Init Datepicker
        $('.date_picker').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });
        
        // Proper Select2 Init
        $('.select2').each(function() {
            $(this).select2({ 
                containerCssClass: 'select2--large',
                dropdownParent: $('body')
            });
        });

        // 🔄 Toggle Logic: Execution Mode -> Start Date
        function toggleStartDate() {
            if ($('#task_type').val() === 'Immediate') { // Note: requirements say Scheduled/Manual/Urgent
                // Keeping previous logic for Immediate or Manual
                $('#start_date_container').fadeOut();
            } else {
                $('#start_date_container').fadeIn();
            }
        }
        $('#task_type').on('change', toggleStartDate);
        toggleStartDate();

        // 🔄 Toggle Logic: Delegation Switch
        $('#enable_delegation').on('change', function() {
            if (this.checked) {
                $('#delegation_options').slideDown();
            } else {
                $('#delegation_options').slideUp();
            }
        });

        // 🔄 Toggle Logic: Status -> Blocker Reason
        $('#official_status').on('change', function() {
            if ($(this).val() === 'Blocked') {
                $('#blocker_reason_container').fadeIn();
            } else {
                $('#blocker_reason_container').fadeOut();
            }
            updateFinalizeBtn();
        });

        // 🧮 Auto Calculation
        function updateMetrics() {
            let total = parseInt($('#total_qty').val()) || 0;
            let completed = parseInt($('#completed_qty').val()) || 0;
            let pending = Math.max(0, total - completed);
            
            $('#pending_qty').val(pending);
            $('#kpi_pending').text(pending.toLocaleString());
            
            let percent = total > 0 ? Math.round((completed / total) * 100) : 0;
            $('#eff_percent, #progress_percent_text').text(percent + '%');
            $('#eff_bar').css('width', percent + '%');
            
            // Update Ring SVG
            let circum = 2 * Math.PI * 20; // 125.6
            $('.progress-ring__circle').css('stroke-dasharray', circum);
            $('.progress-ring__circle').css('stroke-dashoffset', circum - (circum * percent / 100));

            // Hours calc
            let planned = parseFloat($('#planned_hours').val()) || 0;
            let actual = parseFloat($('#actual_hours').val()) || 0;
            let overtime = Math.max(0, actual - planned);
            $('#overtime_hours').val(overtime.toFixed(1));

            updateFinalizeBtn();
        }

        $('#total_qty, #completed_qty, #planned_hours, #actual_hours').on('input', updateMetrics);
        updateMetrics();

        function updateFinalizeBtn() {
            let pending = parseInt($('#pending_qty').val()) || 0;
            let status = $('#official_status').val();
            
            if (status === 'Completed' || pending === 0) {
                $('#finalize_btn').prop('disabled', false);
            } else {
                $('#finalize_btn').prop('disabled', true);
            }
        }

        // 🔗 Sidebar Sync & Scroll
        $(window).scroll(function() {
            let scrollPos = $(document).scrollTop();
            $('.section-card').each(function() {
                let sectionTop = $(this).offset().top - 200;
                let id = $(this).attr('id');
                if (scrollPos >= sectionTop) {
                    $('.process-stepper li').removeClass('active');
                    $(`.process-stepper li[data-section="${id}"]`).addClass('active');
                }
            });
        });

        $('.process-stepper li').click(function() {
            let section = $(this).data('section');
            $('html, body').animate({
                scrollTop: $(`#${section}`).offset().top - 150
            }, 500);
        });
    });
</script>

<style>
    :root {
        --erp-primary: #696cff;
        --erp-primary-rgb: 105, 108, 255;
        --erp-bg: #f5f5f9;
        --erp-text: #435971;
        --erp-border: #d9dee3;
        --erp-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12);
    }

    body { background-color: var(--erp-bg); color: var(--erp-text); font-size: 0.9375rem; }
    
    .text-primary { color: var(--erp-primary) !important; }
    .bg-primary { background-color: var(--erp-primary) !important; }
    .btn-primary { background-color: var(--erp-primary); border-color: var(--erp-primary); }

    .erp-header-card { border-radius: 0.75rem; border: none !important; box-shadow: var(--erp-shadow) !important; }
    .kpi-card { border-radius: 0.75rem; transition: 0.3s; border: none !important; box-shadow: var(--erp-shadow) !important; }
    .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 4px 12px 0 rgba(67, 89, 113, 0.18) !important; }

    /* Progress Ring */
    .progress-ring-container { position: relative; display: flex; align-items: center; justify-content: center; }
    .progress-ring__circle { transition: stroke-dashoffset 0.5s ease-in-out; transform: rotate(-90deg); transform-origin: 50% 50%; }
    .progress-ring-text { position: absolute; font-size: 0.7rem; color: var(--erp-primary); }

    /* Sidebar Stepper */
    .sticky-sidebar { position: sticky; top: 100px; }
    .process-stepper { list-style: none; padding: 0.5rem 0; margin: 0; }
    .process-stepper li { padding: 1rem 1.5rem; display: flex; align-items: center; cursor: pointer; transition: 0.2s; border-left: 3px solid transparent; }
    .process-stepper li:hover { background: rgba(var(--erp-primary-rgb), 0.05); }
    .process-stepper li.active { background: rgba(var(--erp-primary-rgb), 0.08); border-left-color: var(--erp-primary); }
    .process-stepper li.active .step-indicator { background: var(--erp-primary); color: #fff; transform: scale(1.1); }
    .process-stepper li.active .step-label { color: var(--erp-primary); font-weight: 700; }
    
    .step-indicator { width: 34px; height: 34px; border-radius: 50%; background: #e7e7ff; color: var(--erp-primary); display: flex; align-items: center; justify-content: center; margin-right: 15px; font-size: 1rem; transition: 0.3s; }
    .step-label { font-size: 0.9rem; color: var(--erp-text); }

    /* Timeline */
    .activity-timeline { border-left: 2px solid var(--erp-border); margin-left: 12px; }
    .timeline-item { position: relative; padding-left: 35px; margin-bottom: 2rem; }
    .timeline-item:last-child { margin-bottom: 0; }
    .timeline-icon { position: absolute; left: -12px; top: 0; width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; border: 3px solid #fff; box-shadow: var(--erp-shadow); }
    .timeline-content p { font-size: 0.875rem; margin-bottom: 0.2rem; }

    /* Form Design */
    .section-card { border-radius: 0.75rem; border: none !important; box-shadow: var(--erp-shadow) !important; transition: 0.3s; }
    .section-card:hover { box-shadow: 0 4px 12px 0 rgba(67, 89, 113, 0.15) !important; }
    .section-icon { width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.9rem; }
    
    .form-floating-outline .form-control:focus { border-color: var(--erp-primary); box-shadow: 0 0 0 2px rgba(var(--erp-primary-rgb), 0.1); }
    
    .bg-label-primary { background-color: #e7e7ff !important; color: #696cff !important; }
    .bg-label-success { background-color: #e8fadf !important; color: #71dd37 !important; }
    .bg-label-info { background-color: #d7f5fc !important; color: #03c3ec !important; }
    .bg-label-warning { background-color: #fff2d6 !important; color: #ffab00 !important; }
    .bg-label-danger { background-color: #ffe0db !important; color: #ff3e1d !important; }
    .bg-label-secondary { background-color: #ebeef1 !important; color: #8592a3 !important; }

    .extra-small { font-size: 0.75rem; }
    .avatar { width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; }

    .sticky-bottom-actions { position: sticky; bottom: 0; z-index: 10; margin-left: -1rem; margin-right: -1rem; padding-left: 2rem; padding-right: 2rem; }

    /* Custom Input States */
    .form-control[readonly] { background-color: #fcfcfd !important; opacity: 0.85; }
    
    /* Responsive Fixes */
    @media (max-width: 991.98px) {
        .sticky-sidebar { position: relative; top: 0; }
        .sticky-bottom-actions { position: relative; padding-left: 0; padding-right: 0; margin: 0; }
    }
</style>
@endsection

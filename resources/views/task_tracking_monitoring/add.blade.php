@extends('layouts.common')
@section('title', 'Add Task Tracking & Monitoring - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="" method="POST" class="common-form">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Add Task Tracking & Monitoring</h4>
                        </div>
                        <div class="row g-4">
                            <!-- Job Card -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" id="job_card_no" data-placeholder="Select Job Card No">
                                        <option value="">Select Job Card No</option>
                                        <option value="JC20250924-001-K">JC20250924-001-K</option>
                                        <option value="JC20250924-002-K">JC20250924-002-K</option>
                                        <option value="JC20250924-003-K">JC20250924-003-K</option>
                                    </select>
                                    <label for="job_card_no">Job Card No</label>
                                </div>
                            </div>

                            <!-- Assigned By -->
                            <div class="col-md-6 col-xl-4 d-none" id="assigned_by_div">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="assigned_by" placeholder="Enter Assigned By" name="assigned_by" value="Admin" readonly>
                                    <label for="assigned_by">Assigned By</label>
                                </div>
                            </div>

                            <!-- Assigned To -->
                            <div class="col-md-6 col-xl-4 d-none" id="assigned_to_div">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" data-placeholder="Select Assigned To">
                                        <option value="">Select Assigned To</option>
                                        <option value="Ramesh(EMP001)" selected>Ramesh(EMP001)</option>
                                        <option value="Karthick(EMP002)">Karthick(EMP002)</option>
                                        <option value="Sudharsan(EMP003)">Sudharsan(EMP003)</option>
                                    </select>
                                    <label for="task_description">Assigned To </label>
                                </div>
                            </div>

                            <!-- Task Status -->
                            <div class="col-md-6 col-xl-4 d-none" id="task_status_div">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" data-placeholder="Select Task Status">
                                        <option value="">Select Task Status</option>
                                        <option value="Not Started">Not Started</option>
                                        <option value="In Progress" selected>In Progress</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Overdue">Overdue</option>
                                    </select>
                                    <label for="task_status">Task Status * </label>
                                </div>
                            </div>

                            <!-- Deadline Date -->
                            <div class="col-md-6 col-xl-4 d-none" id="deadline_date_div">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control deadline_date" id="deadline_date" placeholder="Enter Deadline Date" name="deadline_date">
                                    <label for="deadline_date">Deadline Date * </label>
                                </div>
                            </div>

                            <!-- Progress -->
                            <div class="col-md-6 col-xl-4 d-none" id="progress_div">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="progress" placeholder="Enter progress(%)" name="progress">
                                    <label for="progress">Progress(%) * </label>
                                </div>
                            </div>

                            <!-- Alerts -->
                            <div class="col-md-6 col-xl-4 d-none" id="alert_div">
                                <div class="d-flex justify-content-between flex-wrap py-2">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" value="1" name="remember" id="alerts" />
                                        <label class="form-check-label me-2" for="alerts">Alerts & Reminders </label>
                                    </div>
                                </div>
                            </div>

                            <!-- ✅ Total Quantity -->
                            <div class="col-md-6 col-xl-4 d-none" id="total_qty_div">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control" id="total_qty" name="total_qty" placeholder="Enter Total Quantity" value="25">
                                    <label for="total_qty">Total Quantity * </label>
                                </div>
                            </div>

                            <!-- ✅ Quantity Completed -->
                            <div class="col-md-6 col-xl-4 d-none" id="qty_completed_div">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control" id="qty_completed" name="qty_completed" placeholder="Enter Quantity Completed" value="20">
                                    <label for="qty_completed">Quantity Completed * </label>
                                </div>
                            </div>

                            <!-- ✅ Quantity Pending (Auto-calculated) -->
                            <div class="col-md-6 col-xl-4 d-none" id="qty_pending_div">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control" id="qty_pending" name="qty_pending" placeholder="Quantity Pending" value="5" readonly>
                                    <label for="qty_pending">Quantity Pending</label>
                                </div>
                            </div>

                            <!-- Task Table -->
                            <div class="col-lg-12">
                                <div class="table-responsive d-none" id="task_table">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Sales Order No</th>
                                            <th>Task Title</th>
                                            <th>Task Type</th>
                                            <th>Assigned Team</th>
                                            <th>Assigned By</th>
                                            <th>Assigned To</th>
                                            <th>Priority</th>
                                            <th>Deadline Date</th>
                                            <th>Related Module</th>
                                            <th>Status</th>
                                        </tr>
                                        <tr>
                                            <td>SO-1001</td>
                                            <td>Cutting for Order #SO-1001</td>
                                            <td>Scheduled</td>
                                            <td>Cutting Department</td>
                                            <td>Admin</td>
                                            <td>Ramesh(EMP001)</td>
                                            <td>High</td>
                                            <td>30-09-2025</td>
                                            <td>Production</td>
                                            <td><span class="badge bg-warning">In Progress</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('task_tracking_monitoring') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.deadline_date').flatpickr({
        dateFormat: 'd-m-Y',
        defaultDate: 'today',
        minDate: 'today',
        allowInput: true
    });

    // Show fields when Job Card selected
    $('#job_card_no').change(function() {
        var job_card_no = $(this).val();
        if (job_card_no) {
            $('#assigned_by_div, #assigned_to_div, #task_status_div, #deadline_date_div, #progress_div, #alert_div, #total_qty_div, #qty_completed_div, #qty_pending_div').removeClass('d-none');
            $('#task_table').removeClass('d-none');
        } else {
            $('#assigned_by_div, #assigned_to_div, #task_status_div, #deadline_date_div, #progress_div, #alert_div, #total_qty_div, #qty_completed_div, #qty_pending_div').addClass('d-none');
            $('#task_table').addClass('d-none');
        }
    });

    // Auto-calculate Quantity Pending
    $('#total_qty, #qty_completed').on('input', function() {
        var total = parseInt($('#total_qty').val()) || 0;
        var completed = parseInt($('#qty_completed').val()) || 0;
        var pending = Math.max(total - completed, 0);
        $('#qty_pending').val(pending);
    });
});
</script>
@endsection

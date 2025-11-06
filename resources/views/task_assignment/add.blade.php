@extends('layouts.common')
@section('title', 'Add Task Assignment - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="" method="POST" class="common-form">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Add Task Assignment</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-6">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" id="task_id" data-placeholder="Select Task ID">
                                        <option value="">Select Task ID</option>
                                        <option value="TASK-2025-001">TASK-2025-001</option>
                                        <option value="TASK-2025-002">TASK-2025-002</option>
                                        <option value="TASK-2025-003">TASK-2025-003</option>
                                    </select>
                                    <label for="task_id">Task ID</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="assigned_by" placeholder="Enter Assigned By" name="assigned_by" value="Admin" readonly>
                                    <label for="assigned_by">Assigned By</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-6">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" data-placeholder="Select Assigned To">
                                        <option value="">Select Assigned To</option>
                                        <option value="Ramesh(EMP001)">Ramesh(EMP001)</option>
                                        <option value="Karthick(EMP002)">Karthick(EMP002)</option>
                                        <option value="Sudharsan(EMP003)">Sudharsan(EMP003)</option>
                                    </select>
                                    <label for="task_description">Assigned To </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-6">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" data-placeholder="Select Assignment Type">
                                        <option value="">Select Assignment Type</option>
                                        <option value="Direct">Direct</option>
                                        <option value="Via Department Head">Via Department Head</option>
                                        <option value="Self-assigned">Self-assigned</option>
                                    </select>
                                    <label for="assignment_type">Assignment Type </label>
                                </div>
                            </div>
                             <div class="col-md-6 col-xl-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control form-control start_date" placeholder="Enter Start Date" />
                                    <label for="start_date">Start Date * </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="permission" class="form-label">Permission (Allow Delegation) *</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="permission" id="permissionYes" value="Yes" checked>
                                    <label class="form-check-label" for="permissionYes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="permission" id="permissionNo" value="No">
                                    <label class="form-check-label" for="permissionNo">No</label>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="table-responsive d-none" id="task_table">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Sales Order No</th>
                                            <th>Task Title</th>
                                            <th>Task Type</th>
                                            <th>Assigned Team</th>
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
                                            <td>High</td>
                                            <td>30-09-2025</td>
                                            <td>Production</td>
                                            <td><span class="badge bg-danger">Not Started</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="text-end col-lg-12">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('task_assignment') }}" class="btn btn-secondary">Cancel</a>
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
        $('#task_id').change(function() {
            var task_id = $(this).val();
            if (task_id) {
                $('#task_table').removeClass('d-none');
            } else {
                $('#task_table').addClass('d-none');
            }
        });
        $('.start_date').flatpickr({
            dateFormat: 'd-m-Y',
            defaultDate: 'today',
            minDate: 'today',
            allowInput: true
        });
    });
</script>
@endsection
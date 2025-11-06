@extends('layouts.common')
@section('title', 'Add Task Status Update - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="" method="POST" class="common-form">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Add Task Status Update</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
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
                            <div class="col-md-6 col-xl-4 d-none" id="task_status_div">
                                <div class="form-floating form-floating-outline">
                                    <select class="select2 form-select" data-placeholder="Select Task Status">
                                        <option value="">Select Task Status</option>
                                        <option value="Not Started" selected>Not Started</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Overdue">Overdue</option>
                                    </select>
                                    <label for="task_status">Task Status * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control" id="comments" name="comments" placeholder="Enter Comments / Notes"></textarea>
                                    <label for="comments">Comments / Notes *</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="file" id="formFile">
                                    <label for="formFile" class="form-label">File Upload</label>
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
                                            <td><span class="badge bg-danger">Not Started</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="text-end col-lg-12">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('task_status_updates') }}" class="btn btn-secondary">Cancel</a>
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
        $('#task_id').change(function() {
            var task_id = $(this).val();
            if (task_id) {
                $('#task_status_div').removeClass('d-none');
                $('#task_table').removeClass('d-none');
            } else {
                $('#task_status_div').addClass('d-none');
                $('#task_table').addClass('d-none');
            }
        });

    });
</script>
@endsection
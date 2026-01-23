@extends('layouts.common')
@section('title', 'Task Tracking & Monitoring - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Task Tracking & Monitoring</h4>
                <a class="btn btn-primary" href="{{ url('add_task_tracking_monitoring') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="filter-box">
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <h5>Filter</h5>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <div class="form-floating form-floating-outline">
                                    <select name="task_status" id="task_status" class="form-select select2" data-placeholder="Select Task Status">
                                        <option value="">Select Task Status</option>
                                        <option value="Planned">Planned</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Hold">Hold</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary" id="filter-btn">Filter</button>
                                <button type="button" class="btn btn-secondary" id="reset-btn">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable">
                        <table class="datatables-products table" id="task-tracking-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Task ID</th>
                                    <th>Job Card No.</th>
                                    <th>Task Title</th>
                                    <th>Assigned By</th>
                                    <th>Assigned To</th>
                                    <th>Task Status</th>
                                    <th>Qty Ordered</th>
                                    <th>Qty Completed</th>
                                    <th>Qty Pending</th>
                                    <th>Deadline Date</th>
                                    <th>Alerts</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var table = $('#task-tracking-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ url('task_tracking_monitoring') }}",
            type: 'GET',
            data: function(d) {
                d.task_status = $('#task_status').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'task_id', name: 'task_id' },
            { data: 'job_card_no', name: 'job_card_no' },
            { data: 'task_title', name: 'task_title' },
            { data: 'assigned_by', name: 'assigned_by' },
            { data: 'assigned_to', name: 'assigned_to' },
            { data: 'task_status', name: 'task_status' },
            { data: 'qty_ordered', name: 'qty_ordered' },
            { data: 'qty_completed', name: 'qty_completed' },
            { data: 'qty_pending', name: 'qty_pending' },
            { data: 'deadline_date', name: 'deadline_date' },
            { data: 'alerts', name: 'alerts', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[1, 'desc']],
        pageLength: 25
    });

    $('#filter-btn').on('click', function() {
        table.draw();
    });

    $('#reset-btn').on('click', function() {
        $('#task_status').val('').trigger('change');
        table.draw();
    });
});
</script>
@endsection

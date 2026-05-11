@extends('layouts.common')
@section('title', 'Manage Leaves - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="table-header-box">
        <h4>Manage Leaves</h4>
        <a class="btn btn-primary" href="{{ url('add_leave') }}">
            <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
        </a>
    </div>
    <div class="col-lg-12">
        @include('flash_messages')
    </div>
    <div class="card">
        <div class="card-body">
            <div class="card-datatable">
                <table class="table nowrap w-100" id="leavesTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            {{-- <th>Leave ID</th> --}}
                            <th>Employee</th>
                            <th>Leave Type</th>
                            <th>Date</th>
                            {{-- <th>End Date</th> --}}
                            <th>Approval Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
$(function () {
    let table = $('#leavesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ url('leave') }}",
            data: function (d) {
                d.status = $('#status').val();
                d.date_range = $('#date_range').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex' },
            // { data: 'leave_id' },
            { data: 'employee' },
            { data: 'leave_type' },
            { data: 'start_date' },
            // { data: 'end_date' },
            { data: 'status', orderable: false, searchable: false },
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    $('#filterBtn').click(function () {
        table.ajax.reload();
    });

    $('#resetBtn').click(function () {
        $('#status').val('');
        $('#date_range').val('');
        table.ajax.reload();
    });

    // Date picker
    $('#date_range').flatpickr({
        mode: 'range',
        dateFormat: 'd-m-Y'
    });
});
</script>
@endsection

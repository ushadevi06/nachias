@extends('layouts.common')
@section('title', 'Ticket Management - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Ticket Management</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create ticket-management'))
                    <a class="btn btn-primary" href="{{ url('ticket_management/add') }}">
                        <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                    </a>
                @endif
            </div>
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="filter-box">
                        <h6 class="card-title mb-3 fw-bold text-dark">Filter</h6>
                        <form id="filterForm" class="row gx-2 gy-2 align-items-center">
                            <div class="col-12 col-md-3">
                                <select class="form-select select2" id="filter_priority" name="filter_priority" data-placeholder="Select Priority">
                                    <option value="">Select Priority</option>
                                    <option value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                    <option value="Critical">Critical</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-3">
                                <select class="form-select select2" id="filter_assigned_to" name="filter_assigned_to" data-placeholder="Assigned To Employees">
                                    <option value="">Assigned To Employees</option>
                                    @foreach($assignees as $assignee)
                                        <option value="{{ $assignee->id }}">{{ $assignee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-3">
                                <input type="text" class="form-control" id="filter_date_range" name="filter_date_range" placeholder="Select Ticket Date Range">
                            </div>
                            <div class="col-12 col-md-3 d-flex gap-2">
                                <button type="button" id="btnFilter" class="btn btn-primary text-uppercase">Filter</button>
                                <button type="button" id="btnReset" class="btn btn-secondary text-uppercase">Reset</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-datatable">
                        <table class="table nowrap w-100" id="ticketTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ticket No</th>
                                    <th>Issue & Category</th>
                                    <th>Assigned To</th>
                                    <th>Priority</th>
                                    <th>Date</th>
                                    <th>Status</th>
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
    $(function() {
        $('#ticketTable').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('ticket_management') }}",
                data: function (d) {
                    d.filter_priority = $('#filter_priority').val();
                    d.filter_assigned_to = $('#filter_assigned_to').val();
                    d.filter_date_range = $('#filter_date_range').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex' },
                { data: 'ticket_no' },
                { data: 'subject' },
                { data: 'assigned_to' },
                { data: 'priority' },
                { data: 'created_at' },
                {
                    data: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $(document).on('change', '.ticket-status-toggle', function() {
            let id = $(this).data('id');
            let status = $(this).is(':checked') ? 'Active' : 'Inactive';

            $.ajax({
                url: "{{ url('ticket_management/status') }}/" + id,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    status: status
                },
                success: function() {
                    let msg = status === 'Active' ?
                        '<span class="text-success small">Activated</span>' :
                        '<span class="text-danger small">Deactivated</span>';

                    $('.status_msg_' + id).html(msg).fadeIn().delay(1200).fadeOut();
                }
            });
        });

        if ($('#filter_date_range').length) {
            $('#filter_date_range').flatpickr({
                mode: "range",
                dateFormat: "Y-m-d",
            });
        }

        $('#btnFilter').on('click', function() {
            $('#ticketTable').DataTable().ajax.reload();
        });

        $('#btnReset').on('click', function() {
            $('#filterForm')[0].reset();
            if ($('#filter_date_range').length && $('#filter_date_range')[0]._flatpickr) {
                $('#filter_date_range')[0]._flatpickr.clear();
            }
            $('#ticketTable').DataTable().ajax.reload();
        });
    });
</script>
@endsection

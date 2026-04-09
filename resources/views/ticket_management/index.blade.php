@extends('layouts.common')
@section('title', 'Ticket Management - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Ticket Management</h4>
                <a class="btn btn-primary" href="{{ url('ticket_management/add') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
            </div>
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="card-datatable">
                        <table class="table" id="ticketTable">
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
            ajax: "{{ url('ticket_management') }}",
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
    });
</script>
@endsection

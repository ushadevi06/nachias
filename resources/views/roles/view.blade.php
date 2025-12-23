@extends('layouts.common')
@section('title', 'Roles - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Roles</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create roles'))
                <a class="btn btn-primary" href="{{ url('roles/add') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
                @endif
            </div>
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-datatable">
                        <table class="table" id="rolesTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Role</th>
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
    $(document).ready(function() {
        $('#rolesTable').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            pageLength: 10,
            ajax: {
                url: "{{ url('roles') }}",
                type: "GET"
            },
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name'
                },
                {
                    data: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });
    });
    $(document).on('change', '.role-status-toggle', function() {

        let roleId = $(this).data('id');
        let status = $(this).is(':checked') ? 'Active' : 'Inactive';

        $.ajax({
            url: "{{ url('roles/status') }}/" + roleId,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: roleId,
                status: status
            },
            success: function(response) {

                if (response.success) {

                    let message = status === 'Active' ?
                        '<span class="text-success">Activated</span>' :
                        '<span class="text-danger">Inactivated</span>';

                    $('.status_msg_' + roleId)
                        .html(message)
                        .fadeIn()
                        .delay(1000)
                        .fadeOut();
                } else {
                    alert('Error updating status.');
                }
            }
        });
    });
</script>
@endsection
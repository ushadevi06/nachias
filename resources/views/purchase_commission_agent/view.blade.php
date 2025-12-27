@extends('layouts.common')
@section('title', 'Purchase Commission Agent - ' . env('WEBSITE_NAME'))

@section('content')
<div class="container-xxl section-padding">
    <div class="table-header-box">
        <h4>Purchase Commission Agent</h4>
        @if(auth()->id() == 1 || auth()->user()->can('create purchase-commission-agent'))
        <a class="btn btn-primary" href="{{ url('purchase_commission_agent/add') }}">
            <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="card-datatable">
                <table class="table" id="purchase-agent-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Contact Info</th>
                            <th>Location</th>
                            <th>Status</th>
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
    $(document).ready(function() {
        var table = $('#purchase-agent-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ url('purchase_commission_agent') }}",
                type: "GET",
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
                    data: 'code'
                },
                {
                    data: 'contact_info'
                },
                {
                    data: 'location'
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


        $('#filter-btn').on('click', function() {
            table.ajax.reload();
        });
        $('#reset-btn').on('click', function() {
            $('#agent_type').val('').trigger('change');
            $('#commission_type').val('').trigger('change');
            table.ajax.reload();
        });
        $(document).on('change', '.purchase-agent-status-toggle', function() {

            let id = $(this).data('id');
            let status = $(this).is(':checked') ? 'Active' : 'Inactive';

            $.ajax({
                url: "{{ url('purchase_commission_agent/status') }}/" + id,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    status: status
                },
                success: function() {

                    let msg = status === 'Active' ?
                        '<span class="text-success">Activated</span>' :
                        '<span class="text-danger">Deactivated</span>';

                    $('.status_msg_' + id).html(msg).fadeIn().delay(1200).fadeOut();
                }
            });
        });

    });
</script>
@endsection
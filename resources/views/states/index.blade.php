@extends('layouts.common')
@section('title', 'States - ' . env('WEBSITE_NAME'))
@section('content')
<!-- / Menu -->
<div class="container-xxl section-padding">
    <div class="row">
        
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>States</h4>
                @if(auth()->user()->id == 1 || auth()->user()->can('create states'))
                <a class="btn btn-primary" href="{{ url('states/add') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
            </div>
            @endif
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-datatable">
                        <table class="table" id="statesTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>State Code</th>
                                    <th>State</th>
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
    </div>
</div>
@endsection
@section('scripts')
<script>
    $(function() {
        let table = $('#statesTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{ url('states') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'state_code',
                    name: 'state_code'
                },
                {
                    data: 'state_name',
                    name: 'state_name'
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });
        $(document).on('change', '.state-status-toggle', function() {
            let id = $(this).data('id');
            let status = $(this).is(':checked') ? 'Active' : 'Inactive';
            $.ajax({
                url: "/state/status/" + id,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    status: status
                },
                success: function(res) {

                    let msg = status === 'Active' ?
                        '<span class="text-success">Activated</span>' :
                        '<span class="text-danger">Inactivated</span>';

                    $('.status_msg_' + id).html(msg).fadeIn().delay(3000).fadeOut();
                }
            });
        });
    });
</script>
@endsection
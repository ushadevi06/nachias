@extends('layouts.common')
@section('title', 'Charges - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Charges</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create charges'))
                <a class="btn btn-primary" href="{{ url('charges/add') }}">
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
                        <table class="table" id="chargeTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Charge Name</th>
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
        $('#chargeTable').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            processing: true,
            ajax: "{{ url('charges') }}",
            columns: [{
                    data: 'DT_RowIndex'
                },
                {
                    data: 'charge_name'
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
                },
            ]
        });
        $(document).on('change', '.charge-status-toggle', function() {
            let chargeId = $(this).data('id');
            let status = $(this).is(':checked') ? 'Active' : 'Inactive';
            $.ajax({
                url: "{{ url('charges/status') }}/" + chargeId,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    status: status
                },
                success: function(response) {

                    let msg = response.status === 'Active' ?
                        '<span class="text-success">Activated</span>' :
                        '<span class="text-danger">Deactivated</span>';

                    $('.status_msg_' + chargeId).html(msg).fadeIn().delay(1200).fadeOut();
                }
            });
        });
    });
</script>
@endsection
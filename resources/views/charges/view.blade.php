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

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

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
            processing: true,
            serverSide: false,
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
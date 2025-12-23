@extends('layouts.common')
@section('title', 'Fabric Type - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Fabric Type</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create fabric-type'))
                <a class="btn btn-primary" href="{{ url('fabric_type/add') }}">
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
                        <table class="table" id="fabricTypesTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fabric Type</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
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

        let table = $('#fabricTypesTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ url('fabric_type') }}"
            },
            columns: [{
                    data: 'DT_RowIndex'
                },
                {
                    data: 'fabric_type'
                },
                {
                    data: 'status'
                },
                {
                    data: 'action'
                }
            ]
        });

        $(document).on('change', '.fabric-type-status-toggle', function() {
            let fabricTypeId = $(this).data('id');
            let status = $(this).is(':checked') ? 'Active' : 'Inactive';

            $.ajax({
                url: "{{ url('fabric_type/status') }}/" + fabricTypeId,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    status: status
                },
                success: function(response) {
                    if (response.success) {

                        let msg = response.status === 'Active' ?
                            '<span class="text-success">Activated</span>' :
                            '<span class="text-danger">Deactivated</span>';

                        $('.status_msg_' + fabricTypeId).html(msg).fadeIn().delay(1200).fadeOut();

                    } else {
                        alert('Status update failed');
                    }
                },
                error: function() {
                    alert('Error updating status');
                }
            });
        });

    });
</script>
@endsection
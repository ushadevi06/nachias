@extends('layouts.common')
@section('title', 'Operation Stages - ' . env('WEBSITE_NAME'))

@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4 class="mb-0">Operation Stages</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create operation-stages'))
                <a class="btn btn-primary" href="{{ url('operation_stages/add') }}">
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
                        <table class="table" id="operationStagesTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Operation Stage Name</th>
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
        // Initialize DataTable
        let table = $('#operationStagesTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ url('operation_stages') }}",
                type: "GET"
            },
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'operation_stage_name'
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

        // Handle status toggle
        $(document).on('change', '.operation-stage-status-toggle', function() {
            let stageId = $(this).data('id');
            let status = $(this).is(':checked') ? 'Active' : 'Inactive';

            $.ajax({
                url: "{{ url('operation_stages/status') }}/" + stageId,
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

                        $('.status_msg_' + stageId).html(msg).fadeIn().delay(1200).fadeOut();
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
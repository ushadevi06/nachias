@extends('layouts.common')
@section('title', 'UOM - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>UOM</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create uoms'))
                <a class="btn btn-primary" href="{{ url('uoms/add') }}">
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
                        <table class="table" id="uomTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>UOM Code</th>
                                    <th>UOM Name</th>
                                    <th>Status</th>
                                    <th width="150">Actions</th>
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

        if ($.fn.DataTable.isDataTable('#uomTable')) {
            $('#uomTable').DataTable().destroy();
        }

        let table = $('#uomTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ url('uoms') }}"
            },
            columns: [{
                    data: 'DT_RowIndex'
                },
                {
                    data: 'uom_code'
                },
                {
                    data: 'uom_name'
                },
                {
                    data: 'status'
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // ✅ STATUS CHANGE
        $(document).on('change', '.uom-status-toggle', function() {

            let id = $(this).data('id');
            let status = $(this).is(':checked') ? 'Active' : 'Inactive';

            $.ajax({
                url: "/uoms/status/" + id,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    status: status
                },
                success: function(response) {

                    let msg = response.status === 'Active' ?
                        '<span class="text-success">Activated</span>' :
                        '<span class="text-danger">Inactivated</span>';

                    $('.status_msg_' + id).html(msg).fadeIn().delay(1000).fadeOut();
                }
            });
        });

    });
</script>
@endsection
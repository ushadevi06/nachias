@extends('layouts.common')
@section('title', 'Colors - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Colors</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create colors'))
                <a class="btn btn-primary" href="{{ url('colors/add') }}">
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
                        <table class="table" id="colorTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Color Name</th>
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

        if ($.fn.DataTable.isDataTable('#colorTable')) {
            $('#colorTable').DataTable().destroy();
        }

        let table = $('#colorTable').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            processing: true,
            ajax: {
                url: "{{ url('colors') }}"
            },
            columns: [{
                    data: 'DT_RowIndex'
                },
                {
                    data: 'color_name'
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

        $(document).on('change', '.color-status-toggle', function() {

            let id = $(this).data('id');
            let status = $(this).is(':checked') ? 'Active' : 'Inactive';

            $.ajax({
                url: "{{ url('colors/status') }}" + "/" + id,
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

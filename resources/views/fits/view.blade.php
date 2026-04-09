@extends('layouts.common')
@section('title', 'Fits - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Fits</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create fits'))
                <a class="btn btn-primary" href="{{ url('fits/add') }}">
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
                        <table class="table" id="fitTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fit Name</th>
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
        $('#fitTable').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            ajax: "{{ url('fits') }}",
            columns: [
                { data: 'DT_RowIndex' },
                { data: 'fit_name' },
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

        $(document).on('change', '.fit-status-toggle', function() {
            let id = $(this).data('id');
            let status = $(this).is(':checked') ? 'Active' : 'Inactive';

            $.ajax({
                url: "{{ url('fits/status') }}/" + id,
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

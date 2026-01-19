@extends('layouts.common')
@section('title', 'Styles - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Styles</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create styles'))
                <a class="btn btn-primary" href="{{ url('styles/add') }}">
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
                        <table class="table" id="styleTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Style Name</th>
                                    <th>Code</th>
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
        $('#styleTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{ url('styles') }}",
            columns: [
                { data: 'DT_RowIndex' },
                { data: 'style_name' },
                { data: 'code' },
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

        $(document).on('change', '.style-status-toggle', function() {
            let id = $(this).data('id');
            let status = $(this).is(':checked') ? 'Active' : 'Inactive';

            $.ajax({
                url: "{{ url('styles/status') }}/" + id,
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

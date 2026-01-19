@extends('layouts.common')
@section('title', 'Brands - ' . env('WEBSITE_NAME'))

@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">

            <div class="table-header-box">
                <h4>Brands</h4>

                @if(auth()->id() == 1 || auth()->user()->can('create brands'))
                <a class="btn btn-primary" href="{{ url('brands/add') }}">
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
                        <table class="table" id="brands-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Brand Name</th>
                                    <th>Code</th>
                                    <th>Created By</th>
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

        let table = $('#brands-table').DataTable({
            processing: true,
            ajax: "{{ url('brands') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false
                },
                {
                    data: 'brand_name'
                },
                {
                    data: 'code'
                },
                {
                    data: 'created_by'
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
        $(document).on('change', '.brand-status-toggle', function() {

            let id = $(this).data('id');
            let status = $(this).is(':checked') ? 'Active' : 'Inactive';

            $.ajax({
                url: "{{ url('brands/status') }}/" + id,
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
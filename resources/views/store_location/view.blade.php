@extends('layouts.common')
@section('title', 'Store Locations - ' . env('WEBSITE_NAME'))

@section('content')
<div class="container-xxl section-padding">
    <div class="row">

        <div class="col-lg-12">
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
        </div>

        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Store Locations</h4>

                @if(auth()->id() == 1 || auth()->user()->can('create store-location'))

                <a class="btn btn-primary" href="{{ url('store_location/add') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
                @endif
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="card-datatable">
                        <table class="table" id="storeLocationTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Store Location</th>
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

        $('#storeLocationTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{ url('store_location') }}",
            columns: [{
                    data: 'DT_RowIndex'
                },
                {
                    data: 'store_location'
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


        $(document).on('change', '.store-status-toggle', function() {

            let id = $(this).data('id');
            let status = $(this).is(':checked') ? 'Active' : 'Inactive';

            $.ajax({
                url: "{{ url('store_location/status') }}/" + id,
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


    // ✅ COMMON DELETE FUNCTION
    function delete_data(url) {
        if (confirm('Are you sure you want to delete this Store Location?')) {
            window.location.href = url;
        }
    }
</script>
@endsection
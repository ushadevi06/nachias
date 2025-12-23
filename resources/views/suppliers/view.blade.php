@extends('layouts.common')
@section('title', 'Suppliers - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Suppliers</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create suppliers'))

                <a href="{{ url('suppliers/add') }}" class="btn btn-primary">
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
                        <table class="datatables-suppliers table" id="suppliers-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Contact Info</th>
                                    <th>Location</th>
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
        var table = $('#suppliers-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ url('suppliers') }}",
                type: "GET",
            },
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name'
                },
                {
                    data: 'contact_info',
                    orderable: false
                },
                {
                    data: 'location',
                    orderable: false
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

        $('#filter-btn').on('click', function() {
            table.ajax.reload();
        });

        $('#reset-btn').on('click', function() {
            table.ajax.reload();
        });

        $(document).on('change', '.supplier-status-toggle', function() {

            let id = $(this).data('id');
            let status = $(this).is(':checked') ? 'Active' : 'Inactive';

            $.ajax({
                url: "{{ url('supplier/status') }}/" + id,
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
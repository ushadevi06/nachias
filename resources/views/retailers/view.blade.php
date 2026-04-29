@extends('layouts.common')
@section('title', 'Retailers - ' . env('WEBSITE_NAME'))

@section('content')
    <div class="container-xxl section-padding">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-header-box">
                    <h4>Retailers</h4>
                    <div class="d-flex gap-2">
                        <button id="btn-excel" class="btn btn-outline-primary btn-sm rounded-pill"><i class="ri ri-file-excel-line me-1"></i> Excel</button>
                        @if(auth()->id() == 1 || auth()->user()->can('create retailers'))
                            <a class="btn btn-primary" href="{{ url('retailers/add') }}">
                                <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                            </a>
                        @endif
                    </div>
                </div>
                <div class="col-lg-12">
                    @include('flash_messages')
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="card-datatable">
                            <table id="retailerTable" class="table">
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
        $(function () {
            let table = $('#retailerTable').DataTable({
                responsive: true,
                paging: true,
                autoWidth: false,
                searching: true,
                ordering: true,
                info: true,
                lengthChange: true,
                dom: '<"d-none"B>lfrtip', 
                buttons: [
                    'excel'
                ],
                ajax: {
                    url: "{{ url('retailers') }}"
                },
                columns: [{
                    data: 'DT_RowIndex'
                },
                {
                    data: 'name'
                },
                {
                    data: 'contact_info',
                    orderable: false,
                },
                {
                    data: 'location',
                    orderable: false,
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

            // Export Handlers
            $('#btn-excel').on('click', function() {
                window.location.href = "{{ url('retailers/export-excel') }}";
            });

            $(document).on('change', '.retailer-status-toggle', function () {

                let id = $(this).data('id');
                let status = $(this).is(':checked') ? 'Active' : 'Inactive';

                $.ajax({
                    url: "{{ url('retailers/status') }}/" + id,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        status: status
                    },
                    success: function () {
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

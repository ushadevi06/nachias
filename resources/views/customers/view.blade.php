@extends('layouts.common')
@section('title', 'Customers - ' . env('WEBSITE_NAME'))

@section('content')
    <div class="container-xxl section-padding">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-header-box">
                    <h4>Customers</h4>
                    @if(auth()->id() == 1 || auth()->user()->can('create customers'))
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal"
                                data-bs-target="#importModal">
                                <i class="menu-icon icon-base ri ri-upload-2-line"></i> Import
                            </button>
                            <a class="btn btn-outline-success" href="{{ url('customers/export-excel') }}">
                                <i class="menu-icon icon-base ri ri-file-excel-2-line"></i> Export
                            </a>
                            <a class="btn btn-primary" href="{{ url('customers/add') }}">
                                <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                            </a>
                        </div>
                    @endif
                </div>
                <div class="col-lg-12">
                    @include('flash_messages')
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="filter-box mb-3">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <select id="category" class="form-select select2" data-placeholder="Select Category">
                                        <option value="">Select Category</option>
                                        <option value="Retailer">Retailer</option>
                                        <option value="Wholesaler">Wholesaler</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" id="filterBtn" class="btn btn-primary">Filter</button>
                                    <button type="button" id="resetBtn" class="btn btn-secondary">Reset</button>
                                </div>
                            </div>
                        </div>

                        <div class="card-datatable">
                            <table id="customerTable" class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Category</th>
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

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Customers</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ url('customers/import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="import_file" class="form-label">Upload File (CSV, Excel)</label>
                            <input class="form-control" type="file" id="import_file" name="import_file"
                                accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                required>
                        </div>
                        <div class="mb-3">
                            <a href="{{ url('customers/download-sample') }}" class="btn btn-sm btn-outline-info">
                                <i class="ri ri-download-2-line"></i> Download Sample Format
                            </a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('scripts')

    <script>
        $(function () {
            let table = $('#customerTable').DataTable({
                responsive: true,
                paging: true,
                autoWidth: false,
                searching: true,
                ordering: true,
                info: true,
                lengthChange: true,
                ajax: {
                    url: "{{ url('customers') }}",
                    data: function (d) {
                        d.category = $('#category').val();
                    }
                },
                columns: [{
                    data: 'DT_RowIndex'
                },
                {
                    data: 'name'
                },
                {
                    data: 'category'
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

            $('#filterBtn').click(function () {
                table.ajax.reload();
            });

            $('#resetBtn').click(function () {
                $('#category').val('').trigger('change');
                table.ajax.reload();
            });

            $(document).on('change', '.customer-status-toggle', function () {

                let id = $(this).data('id');
                let status = $(this).is(':checked') ? 'Active' : 'Inactive';

                $.ajax({
                    url: "{{ url('customers/status') }}/" + id,
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
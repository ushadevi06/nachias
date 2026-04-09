@extends('layouts.common')
@section('title', 'Suppliers - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Suppliers</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create suppliers'))
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="menu-icon icon-base ri ri-upload-2-line"></i> Import
                    </button>
                    <a href="{{ url('suppliers/add') }}" class="btn btn-primary">
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

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Suppliers</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('suppliers/import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="import_file" class="form-label">Upload File (CSV, Excel)</label>
                        <input class="form-control" type="file" id="import_file" name="import_file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                    </div>
                    <div class="mb-3">
                        <a href="{{ url('suppliers/download-sample') }}" class="btn btn-sm btn-outline-info">
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
    $(document).ready(function() {
        var table = $('#suppliers-table').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
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
@extends('layouts.common')
@section('title', 'Store Locations - ' . env('WEBSITE_NAME'))

@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Store Locations</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create store-location'))
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="menu-icon icon-base ri ri-upload-2-line"></i> Import
                    </button>
                    <a class="btn btn-primary" href="{{ url('store_location/add') }}">
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

<!-- Import Modal -->
@if(auth()->id() == 1 || auth()->user()->can('create store-location'))
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Store Locations</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('store_location/import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="import_file" class="form-label">Upload File (CSV, Excel)</label>
                        <input class="form-control" type="file" id="import_file" name="import_file"
                            accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                            required>
                    </div>
                    <div class="mb-3">
                        <a href="{{ url('store_location/download-sample') }}" class="btn btn-sm btn-outline-info">
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
@endif
@endsection


@section('scripts')
<script>
    $(function() {
        $('#storeLocationTable').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            processing: true,
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

        $('#importModal').on('hidden.bs.modal', function () {
            $(this).find('form')[0].reset();
        });

    });
</script>
@endsection
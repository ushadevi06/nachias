@extends('layouts.common')
@section('title', 'Raw Materials - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Raw Materials</h4>
                <div class="d-flex gap-2">
                    @if(auth()->id() == 1 || auth()->user()->can('create raw-materials'))
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal"
                        data-bs-target="#importModal">
                        <i class="menu-icon icon-base ri ri-upload-2-line"></i> Import
                    </button>
                    @endif
                    @if(auth()->id() == 1 || auth()->user()->can('create raw-materials'))
                    <a class="btn btn-primary" href="{{ url('raw_materials/add') }}">
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
                    <div class="filter-box mb-3">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <select id="filter_category" class="form-select select2" data-placeholder="Select Store Category">
                                    <option value="">Select Store Category</option>
                                    @foreach(\App\Models\StoreCategory::where('status','Active')->get() as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary" id="filter-btn">Filter</button>
                                <button type="button" class="btn btn-secondary" id="reset-btn">Reset</button>
                            </div>

                        </div>
                    </div>

                    <div class="card-datatable">
                        <table class="table" id="raw-material-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Store Category</th>
                                    <th>Name</th>
                                    <th>UOM</th>
                                    <th>Created By</th>
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
    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Raw Materials</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ url('raw_materials/import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="import_file" class="form-label">Upload File (CSV, Excel)</label>
                            <input class="form-control" type="file" id="import_file" name="import_file"
                                accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                required>
                        </div>
                        <div class="mb-3">
                            <a href="{{ url('raw_materials/download-sample') }}" class="btn btn-sm btn-outline-info">
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
</div>
@endsection
@section('scripts')
<script>
    $(document).ready(function() {

        let table = $('#raw-material-table').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            ajax: {
                url: "{{ url('raw_materials') }}",
                data: function(d) {
                    d.category_id = $('#filter_category').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'category'
                },
                {
                    data: 'name'
                },
                {
                    data: 'uom'
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
                }
            ]
        });
        $('#filter-btn').click(function() {
            table.ajax.reload();
        });
        $('#reset-btn').click(function() {
            $('#filter_category').val('').trigger('change');
            $('#filter_status').val('').trigger('change');
            table.ajax.reload();
        });
        $(document).on('change', '.raw-material-status-toggle', function() {
            let id = $(this).data('id');
            let status = $(this).is(':checked') ? 'Active' : 'Inactive';

            $.ajax({
                url: "{{ url('raw_materials/status') }}/" + id,
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
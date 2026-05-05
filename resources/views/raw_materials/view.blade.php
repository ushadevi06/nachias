@extends('layouts.common')
@section('title', 'Raw Materials - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Raw Materials</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create raw-materials'))
                <a class="btn btn-primary" href="{{ url('raw_materials/add') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
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

    });
</script>
@endsection
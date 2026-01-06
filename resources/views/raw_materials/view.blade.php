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

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <!-- ✅ FILTER -->
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
                                <select id="filter_fabric_type" class="form-select select2" data-placeholder="Select Fabric Type">
                                    <option value="">Select Fabric Type</option>
                                    @foreach(\App\Models\FabricType::where('status','Active')->get() as $fab)
                                    <option value="{{ $fab->id }}">{{ $fab->fabric_type }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary" id="filter-btn">
                                    <i class="ri ri-filter-3-line"></i> Filter
                                </button>

                                <button type="button" class="btn btn-secondary" id="reset-btn">
                                    <i class="ri ri-refresh-line"></i> Reset
                                </button>
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
                                    <th>Fabric Type</th>
                                    <th>Width</th>
                                    <th>Min Stock</th>
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
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ url('raw_materials') }}",
                data: function(d) {
                    d.category_id = $('#filter_category').val();
                    d.fabric_type_id = $('#filter_fabric_type').val();
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
                    data: 'fabric_type'
                },
                {
                    data: 'size_width'
                },
                {
                    data: 'min_stock'
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

        // ✅ FILTER BUTTON
        $('#filter-btn').click(function() {
            table.ajax.reload();
        });

        // ✅ RESET BUTTON
        $('#reset-btn').click(function() {
            $('#filter_category').val('').trigger('change');
            $('#filter_fabric_type').val('').trigger('change');
            $('#filter_status').val('').trigger('change');
            table.ajax.reload();
        });

        // ✅ STATUS TOGGLE
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
@extends('layouts.common')
@section('title', 'Items - ' . env('WEBSITE_NAME'))

@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">

            <div class="table-header-box">
                <h4>Items</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create items'))
                <a class="btn btn-primary" href="{{ url('items/add') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
                @endif
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <div class="card mb-3">
                <div class="card-body">
                    <form id="filter-form">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <select id="filter_brand_category" class="form-select select2" data-placeholder="Select Brand Category">
                                    <option value="">Select Brand Category</option>
                                    @foreach($brandCategories as $category)
                                    <option value="{{ $category->id }}">
                                        {{ $category->name }} ({{ $category->code }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select id="filter_brand" class="form-select select2" data-placeholder="Select Brand">
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">
                                        {{ $brand->brand_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 d-flex gap-2">
                                <button type="button" id="filterBtn" class="btn btn-primary">Filter</button>
                                <button type="button" id="resetBtn" class="btn btn-secondary">Reset</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-datatable">
                        <table class="table" id="itemsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Brand Category</th>
                                    <th>Name</th>
                                    <th>Brand</th>
                                    <th>Color</th>
                                    <th>UOM</th>
                                    <th>Pricing (W / R / E)</th>
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

        let table = $('#itemsTable').DataTable({
            processing: true,
            responsive: true,
            serverSide: false,
            ajax: {
                url: "{{ url('items') }}",
                type: "GET",
                data: function(d) {
                    d.brand_category_id = $('#filter_brand_category').val();
                    d.brand_id = $('#filter_brand').val();
                },
                dataSrc: "data"
            },
            columns: [{
                    data: 'DT_RowIndex'
                },
                {
                    data: 'brand_category'
                },
                {
                    data: 'name'
                },
                {
                    data: 'brand'
                },
                {
                    data: 'color'
                },
                {
                    data: 'uom'
                },
                {
                    data: 'pricing'
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

        // ✅ FILTER
        $('#filterBtn').click(function() {
            table.ajax.reload();
        });

        // ✅ RESET
        $('#resetBtn').click(function() {
            $('#filter_brand_category').val('');
            $('#filter_brand').val('');
            table.ajax.reload();
        });

        // ✅ STATUS TOGGLE (AJAX)
        $(document).on('change', '.item-status-toggle', function() {

            let id = $(this).data('id');
            let status = $(this).is(':checked') ? 'Active' : 'Inactive';

            $.ajax({
                url: "{{ url('items/status') }}/" + id,
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
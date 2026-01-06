@extends('layouts.common')
@section('title', 'Stock Entry - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Stock Entry</h4>
                @if(auth()->id() == 1 || auth()->user()->can('add stock entries'))
                <a class="btn btn-primary" href="{{ url('stock_entries/add') }}">
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
                    <!-- Filter Section -->
                    <div class="filter-box">
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <h5>Filter</h5>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <div class="form-floating form-floating-outline">
                                    <select name="material_category" id="material_category" class="form-select select2" data-placeholder="Select Store Category">
                                        <option value="">Select Store Category</option>
                                        @foreach($storeCategories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->category_name }}({{ $cat->code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <select name="material" id="material" class="form-select select2" data-placeholder="Select Material">
                                    <option value="">Select Material</option>
                                    @foreach($rawMaterials as $mat)
                                        <option value="{{ $mat->id }}" data-category="{{ $mat->store_category_id }}">{{ $mat->name }}({{ $mat->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary" id="btn-filter">Filter</button>
                                <button type="button" class="btn btn-secondary" id="btn-reset">Reset</button>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="card-datatable">
                        <table class="stock-entry-table table">
                            <thead class="table">
                                <tr>
                                    <th>#</th>
                                    <th>Stock Entry No.</th>
                                    <th>Stock Date</th>
                                    <th>Store Category</th>
                                    <th>Material</th>
                                    <th>GRN No.</th>
                                    <th>Total Qty</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
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
        let table = $('.stock-entry-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ url('stock_entries') }}",
                data: function(d) {
                    d.material_category = $('#material_category').val();
                    d.material = $('#material').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'stock_entry_no', name: 'stock_entry_no'},
                {data: 'stock_date', name: 'stock_date'},
                {data: 'material_category', name: 'material_category'},
                {data: 'material', name: 'material'},
                {data: 'grn_no', name: 'grn_no'},

                {data: 'total_qty', name: 'total_qty'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });

        // Dependent Dropdown for Store Category -> Material
        let $materialSelect = $('#material');
        let allMaterials = $materialSelect.find('option').clone();

        $('#material_category').on('change', function() {
            let categoryId = $(this).val();
            let currentMaterial = $materialSelect.val();

            $materialSelect.empty();
            $materialSelect.append('<option value="">Select Material</option>');

            allMaterials.each(function() {
                let optionCat = $(this).data('category');
                if ($(this).val() !== '') {
                    if (!categoryId || optionCat == categoryId) {
                        $materialSelect.append($(this));
                    }
                }
            });

            // Restore selection if valid
            $materialSelect.val(currentMaterial).trigger('change');
        });

        $('#btn-filter').click(function() {
            table.ajax.reload();
        });

        $('#btn-reset').click(function() {
            $('#material_category').val('').trigger('change');
            $('#material').val('').trigger('change');
            table.ajax.reload();
        });
    });

    function delete_data(url) {
        if (confirm('Are you sure you want to delete this stock entry?')) {
            $.post(url, {
                _token: "{{ csrf_token() }}",
            }, function(res) {
                if (res.success) {
                    $('.datatables-products').DataTable().ajax.reload();
                }
            });
        }
    }
</script>
@endsection

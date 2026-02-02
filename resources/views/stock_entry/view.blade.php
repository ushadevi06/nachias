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

<!-- Modal Stock Adjustment -->
<div class="modal fade" id="modalAdjustment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Stock Adjustment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAdjustment">
                @csrf
                <input type="hidden" name="item_id" id="adj_item_id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="adj_grn_no" readonly>
                                <label>GRN No.</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="adj_material" readonly>
                                <label>Material</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="adj_current_qty" readonly>
                                <label>Current Qty (In)</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating form-floating-outline">
                                <input type="number" step="0.01" class="form-control" name="qty_to_add" id="adj_qty_to_add" placeholder="0.00" required>
                                <label>Add Quantity *</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" name="approved_by" id="adj_approved_by" placeholder="Supervisor/Manager Name" required>
                                <label>Approved By *</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating form-floating-outline">
                                <textarea class="form-control" name="reason" id="adj_reason" rows="3" placeholder="Reason for adjustment" required style="height: 80px;"></textarea>
                                <label>Reason for Adjustment *</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-adjustment">Adjust Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function() {
        let table = $('.stock-entry-table').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            pageLength: 10,
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

    // Handle Adjustment Button Click
    $(document).on('click', '.btn-adjust', function() {
        let btn = $(this);
        $('#adj_item_id').val(btn.data('item-id'));
        $('#adj_grn_no').val(btn.data('grn-no'));
        $('#adj_material').val(btn.data('material'));
        $('#adj_current_qty').val(btn.data('current-qty'));
        $('#adj_qty_to_add').val('');
        $('#adj_reason').val('');
        $('#adj_approved_by').val('');
        
        $('#modalAdjustment').modal('show');
    });

    // Handle Adjustment Form Submit
    $('#formAdjustment').on('submit', function(e) {
        e.preventDefault();
        let btn = $('#btn-save-adjustment');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adjusting...');

        $.ajax({
            url: "{{ route('stock_entries.quick_adjustment') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                if(res.success) {
                    $('#modalAdjustment').modal('hide');
                    alert(res.message);
                    table.ajax.reload();
                } else {
                    alert(res.message);
                }
            },
            error: function(xhr) {
                let msg = 'Error adjusting stock. Please check the console.';
                if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                alert(msg);
            },
            complete: function() {
                btn.prop('disabled', false).html('Adjust Stock');
            }
        });
    });

    function delete_data(url) {
        if (confirm('Are you sure you want to delete this stock entry?')) {
            $.post(url, {
                _token: "{{ csrf_token() }}",
            }, function(res) {
                if (res.success) {
                    table.ajax.reload();
                }
            });
        }
    }
</script>
@endsection

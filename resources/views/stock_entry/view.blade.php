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

                    <!-- Tabs Section -->
                    <!-- Tabs Section -->
                    <div class="d-flex justify-content-center mb-4">
                        <div class="nav nav-pills custom-segment-tabs p-1 rounded-pill bg-light" id="stockEntryTabs" role="tablist">
                            <button class="nav-link active rounded-pill px-4 fw-bold" id="raw-material-tab" type="button" data-entry-type="Raw Material">Raw Material</button>
                            <button class="nav-link rounded-pill px-4 fw-bold" id="finished-goods-tab" type="button" data-entry-type="Finished Goods">Finished Goods</button>
                        </div>
                    </div>

                    <style>
                        .custom-segment-tabs {
                            background-color: #f1f3f6 !important;
                            border: 1px solid #e0e0e0;
                            display: inline-flex;
                            min-width: 300px;
                        }
                        .custom-segment-tabs .nav-link {
                            color: #6c757d;
                            transition: all 0.3s ease;
                            flex: 1;
                            text-align: center;
                        }
                        .custom-segment-tabs .nav-link:hover {
                            color: #5d596c;
                        }
                        .custom-segment-tabs .nav-link.active {
                            background-color: #ffffff !important;
                            color: #696cff !important;
                            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08); 
                        }
                    </style>

                    <!-- Data Table -->
                    <div class="card-datatable">
                        <table class="stock-entry-table table">
                            <thead class="table">
                                <tr>
                                    <th>#</th>
                                    <th>Stock Entry No.</th>
                                    <th>Stock Date</th>
                                    <th>Store Category</th>
                                    <th>Art No</th>
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
            <form id="formAdjustment" autocomplete="off">
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
                            <div class="form-floating form-floating-outline shadow-sm">
                                <input type="text" class="form-control fw-bold text-primary" id="adj_art_no" readonly style="background-color: rgba(78, 103, 235, 0.05);">
                                <label class="fw-bold">Art No.</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="adj_material" readonly>
                                <label>Material Name</label>
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
    let table; // Declare table variable in global scope
    
    $(function() {
        table = $('.stock-entry-table').DataTable({
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
                    d.art_no = new URLSearchParams(window.location.search).get('art_no');
                    d.grn_no = new URLSearchParams(window.location.search).get('grn_no');
                    d.entry_type = $('#stockEntryTabs .nav-link.active').data('entry-type');
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'stock_entry_no', name: 'stock_entry_no'},
                {data: 'stock_date', name: 'stock_date'},
                {data: 'material_category', name: 'material_category'},
                {data: 'art_no', name: 'art_no', render: function(data) {
                    return `<span class="badge bg-label-primary fs-6 fw-bold border border-primary-subtle shadow-sm px-3">${data}</span>`;
                }},
                {data: 'material', name: 'material'},
                {data: 'grn_no', name: 'grn_no'},

                {data: 'total_qty', name: 'total_qty'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
        
        // Tab Click Handler
        $('#stockEntryTabs .nav-link').on('click', function() {
            $('#stockEntryTabs .nav-link').removeClass('active');
            $(this).addClass('active');
            
            // Show/Hide filters and columns based on tab
            if($(this).data('entry-type') === 'Finished Goods') {
                $('.filter-box').hide();
                table.column(1).visible(false); // Hide Stock Entry No
                table.column(3).visible(false); // Hide Store Category
                table.column(4).visible(false); // Hide Art No
                $(table.column(5).header()).text('Finished Goods');
                $(table.column(6).header()).text('Job Card No.');
            } else {
                $('.filter-box').show();
                table.column(1).visible(true); // Show Stock Entry No
                table.column(3).visible(true); // Show Store Category
                table.column(4).visible(true); // Show Art No
                $(table.column(5).header()).text('Material');
                $(table.column(6).header()).text('GRN No.');
            }
            
            table.ajax.reload();
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
            window.history.replaceState({}, document.title, window.location.pathname); // Clear URL params
            table.ajax.reload();
        });

        // Auto-Filter from URL
        const urlParams = new URLSearchParams(window.location.search);
        const catParam = urlParams.get('material_category');
        const matParam = urlParams.get('material');
        const searchParam = urlParams.get('search');

        if (catParam) {
            $('#material_category').val(catParam).trigger('change');
        }
        if (matParam) {
            setTimeout(() => {
                $('#material').val(matParam).trigger('change');
                table.ajax.reload();
            }, 300);
        }
        if (searchParam) {
            table.search(searchParam).draw();
        }
    });

    // Handle Adjustment Button Click
    $(document).on('click', '.btn-adjust', function() {
        let btn = $(this);
        $('#adj_item_id').val(btn.data('item-id'));
        $('#adj_grn_no').val(btn.data('grn-no'));
        $('#adj_art_no').val(btn.data('art-no'));
        $('#adj_material').val(btn.data('material'));
        $('#adj_current_qty').val(btn.data('current-qty'));
        $('#adj_qty_to_add').val('');
        $('#adj_reason').val('');
        $('#adj_approved_by').val('');
        
        $('#modalAdjustment').modal('show');
    });

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
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: res.message,
                        confirmButtonText: 'OK',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        if (table && typeof table.ajax !== 'undefined') {
                            table.ajax.reload(null, false); 
                        } else {
                            location.reload();  
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: res.message,
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                let msg = 'Error adjusting stock. Please check the console.';
                if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: msg,
                    confirmButtonText: 'OK'
                });
            },
            complete: function() {
                btn.prop('disabled', false).html('Adjust Stock');
            }
        });
    });
    
</script>
<style>
    .bg-label-primary {
        background-color: rgba(78, 103, 235, 0.1) !important;
        color: #4e67eb !important;
        padding: 5px 12px;
        border-radius: 6px;
        display: inline-block;
    }
    .stock-entry-table td {
        vertical-align: middle;
    }
    .mini-title {
        display: block;
        font-size: 11px;
        color: #888;
        font-weight: 500;
        margin-top: 2px;
    }
</style>
@endsection

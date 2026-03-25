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
             <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <div class="card">
                <div class="card-body">
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
                                    <th>GRN No.</th>
                                    <th>Sleeve Type</th>
                                    <th>Size</th>
                                    <th>SKU</th>
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
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Stock Adjustment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAdjustment" autocomplete="off">
                @csrf
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 40px;"><input type="checkbox" class="form-check-input" id="check-all"></th>
                                    <th>Category</th>
                                    <th>Material</th>
                                    <th>Art No</th>
                                    <th>Current Qty</th>
                                    <th style="width: 120px;">Add Qty *</th>
                                    <th>Approved By *</th>
                                    <th>Reason *</th>
                                </tr>
                            </thead>
                            <tbody id="adjustment-items-body">
                                <tr>
                                    <td colspan="8" class="text-center">Loading items...</td>
                                </tr>
                            </tbody>
                        </table>
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
                    d.art_no = new URLSearchParams(window.location.search).get('art_no');
                    d.grn_no = new URLSearchParams(window.location.search).get('grn_no');
                    d.entry_type = $('#stockEntryTabs .nav-link.active').data('entry-type');
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'stock_entry_no', name: 'stock_entry_no'},
                {data: 'stock_date', name: 'stock_date'},
                {data: 'grn_no', name: 'grn_no'},
                {data: 'sleeve_type', name: 'sleeve_type', visible: false},
                {data: 'size', name: 'size', visible: false},
                {data: 'sku', name: 'sku', visible: false},
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
                $(table.column(3).header()).text('Job Card No.');
                table.column(4).visible(true); // Sleeve Type
                table.column(5).visible(true); // Size
                table.column(6).visible(true); // SKU
            } else {
                $('.filter-box').show();
                table.column(1).visible(true); // Show Stock Entry No
                $(table.column(3).header()).text('GRN No.');
                table.column(4).visible(false); // Sleeve Type
                table.column(5).visible(false); // Size
                table.column(6).visible(false); // SKU
            }
            
            table.ajax.reload();
        });


        $('#btn-filter').click(function() {
            table.ajax.reload();
        });

        $('#btn-reset').click(function() {
            window.history.replaceState({}, document.title, window.location.pathname); // Clear URL params
            table.ajax.reload();
        });

        // Auto-Filter from URL
        const urlParams = new URLSearchParams(window.location.search);
        const searchParam = urlParams.get('search');
        if (searchParam) {
            table.search(searchParam).draw();
        }
    });

    // Handle Adjustment Button Click
    $(document).on('click', '.btn-adjust', function() {
        let btn = $(this);
        let entryId = btn.data('entry-id');
        let $body = $('#adjustment-items-body');
        $body.html('<tr><td colspan="8" class="text-center"><span class="spinner-border spinner-border-sm" role="status"></span> Loading items...</td></tr>');
        $('#check-all').prop('checked', false);
        $('#modalAdjustment').modal('show');

        $.get("{{ url('stock_entries/get-items') }}/" + entryId, function(res) {
            if(res.success) {
                let html = '';
                res.items.forEach((item, index) => {
                    html += `
                        <tr>
                            <td><input type="checkbox" class="form-check-input item-check" data-index="${index}"></td>
                            <td>${item.category}</td>
                            <td>${item.material}</td>
                            <td><span class="badge bg-label-primary px-2">${item.art_no}</span></td>
                            <td>${item.current_qty}</td>
                            <td>
                                <input type="hidden" name="adjustments[${index}][item_id]" value="${item.id}" disabled>
                                <input type="number" step="0.01" class="form-control form-control-sm" name="adjustments[${index}][qty_to_add]" placeholder="0.00" disabled required>
                            </td>
                            <td><input type="text" class="form-control form-control-sm" name="adjustments[${index}][approved_by]" placeholder="Approved By" disabled required></td>
                            <td><input type="text" class="form-control form-control-sm" name="adjustments[${index}][reason]" placeholder="Reason" disabled required></td>
                        </tr>
                    `;
                });
                $body.html(html);
            } else {
                $body.html('<tr><td colspan="8" class="text-center text-danger">Error loading items</td></tr>');
            }
        });
    });

    $(document).on('change', '#check-all', function() {
        $('.item-check').prop('checked', $(this).prop('checked')).trigger('change');
    });

    $(document).on('change', '.item-check', function() {
        let row = $(this).closest('tr');
        let checked = $(this).prop('checked');
        row.find('input:not(.item-check)').prop('disabled', !checked);
    });

    $('#formAdjustment').on('submit', function(e) {
        e.preventDefault();
        
        if ($('.item-check:checked').length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Items Selected',
                text: 'Please select at least one item to adjust.',
                confirmButtonText: 'OK'
            });
            return;
        }

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

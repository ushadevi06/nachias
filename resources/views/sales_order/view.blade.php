@extends('layouts.common')
@section('title', 'Sales Orders - ' . env('WEBSITE_NAME'))

@section('content')
    <div class="container-xxl section-padding">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-header-box">
                    <h4>Sales Orders</h4>
                    <div class="d-flex gap-2">
                        <!-- Bulk Action Dropdown Button (Shown only when at least 1 checkbox checked) -->
                        <div class="dropdown d-none" id="bulkActionDropdown">
                            <button class="btn btn-warning dropdown-toggle" type="button" id="bulkActionDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="icon-base ri ri-checkbox-multiple-line me-1"></i> Bulk Action (<span id="selectedCountBadge">0</span>)
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="bulkActionDropdownBtn">
                                <li>
                                    <a class="dropdown-item text-danger fw-semibold" href="javascript:void(0);" id="bulkCloseBtn">
                                        <i class="icon-base ri ri-close-circle-line me-2"></i> Closed
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <a class="btn btn-outline-info" href="{{ url('sales_orders/sync-orderaxe') }}">
                            <i class="icon-base ri ri-refresh-line"></i> Sync Orderaxe
                        </a>
                        <a class="btn btn-primary" href="{{ url('sales_orders/add') }}">
                            <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                        </a>
                    </div>
                </div>
                <div class="col-lg-12">
                    @include('flash_messages')
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="filter-box">
                            <div class="row g-3">
                                <div class="col-lg-12">
                                    <h5>Filter</h5>
                                </div>
                                <div class="col-md-4 col-lg-3">
                                    <select name="customer_id" id="filter_customer" class="form-select select2"
                                        data-placeholder="Select Customer">
                                        <option value="">Select Customer</option>
                                        @foreach(\App\Models\Customer::active()->orderBy('id', 'desc')->get() as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 col-lg-3">
                                    <select name="brand_id" id="filter_brand" class="form-select select2"
                                        data-placeholder="Select Brand">
                                        <option value="">Select Brand</option>
                                        @foreach(\App\Models\Brand::whereNull('deleted_at')->where('status', 'Active')->finishedGoods()->orderBy('brand_name', 'asc')->get() as $b)
                                            <option value="{{ $b->id }}">{{ $b->brand_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 col-lg-3">
                                    <select name="status" id="filter_status" class="form-select select2"
                                        data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        @foreach(['Draft', 'Approved', 'Pending', 'Dispatched', 'Cancelled', 'Closed'] as $s)
                                            <option value="{{ $s }}">{{ $s }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 col-lg-3">
                                    <input type="text" id="so_date_range" class="form-control"
                                        placeholder="Select SO Date Range">
                                </div>
                                <div class="col-md-3">
                                    <button type="button" id="filterBtn" class="btn btn-primary">Filter</button>
                                    <button type="button" id="resetBtn" class="btn btn-secondary">Reset</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-datatable">
                            <!-- Gmail Style Select All Matching Alert Banner -->
                            <div id="selectAllMatchingBanner" class="alert alert-primary py-2 px-3 mb-2 text-center d-none" style="border-radius: 8px; font-size: 14px;">
                                <span id="selectAllBannerText">All <b><span id="pageSelectedCount">10</span></b> orders on this page are selected.</span>
                                <a href="javascript:void(0);" id="btnSelectAllAcrossPages" class="alert-link fw-bold text-decoration-underline ms-2">
                                    Select all <span id="totalMatchingCount">0</span> orders matching this filter
                                </a>
                                <a href="javascript:void(0);" id="btnClearAllSelection" class="alert-link fw-bold text-decoration-underline ms-2 d-none">
                                    Clear selection
                                </a>
                            </div>

                            <table id="salesOrderTable" class="table nowrap w-100">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 35px;" data-orderable="false">
                                            <input type="checkbox" id="checkAllSO" class="form-check-input" title="Select All">
                                        </th>
                                        <th>#</th>
                                        <th>SO Number</th>
                                        <th>SO Date</th>
                                        <th>Customer Name</th>
                                        <th>Customer PO Ref</th>
                                        <th>Total Qty</th>
                                        <th>Sales Executive</th>
                                        <th>Status</th>
                                        <th>Total Amount</th>
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

    <style>
        #salesOrderTable tbody tr.selected-row {
            background-color: rgba(115, 103, 240, 0.08) !important;
        }
        .so-row-checkbox:disabled {
            opacity: 0.35;
            cursor: not-allowed;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(function () {
            let selectedSoIds = new Set();
            let isAllMatchingSelected = false;

            function updateBulkActionBar() {
                let recordsDisplay = 0;
                try {
                    recordsDisplay = table.page.info().recordsDisplay;
                } catch (e) {
                    recordsDisplay = 0;
                }

                if (isAllMatchingSelected) {
                    $('#selectedCountBadge').text(recordsDisplay.toLocaleString());
                    $('#bulkActionDropdown').removeClass('d-none');
                    $('#selectAllMatchingBanner').removeClass('d-none');
                    $('#selectAllBannerText').html(`All <b>${recordsDisplay.toLocaleString()}</b> orders matching this filter are selected.`);
                    $('#btnSelectAllAcrossPages').addClass('d-none');
                    $('#btnClearAllSelection').removeClass('d-none');
                    $('#checkAllSO').prop('checked', true).prop('indeterminate', false);
                    return;
                }

                let count = selectedSoIds.size;
                $('#selectedCountBadge').text(count.toLocaleString());
                if (count > 0) {
                    $('#bulkActionDropdown').removeClass('d-none');
                } else {
                    $('#bulkActionDropdown').addClass('d-none');
                }

                let totalVisible = $('.so-row-checkbox:not(:disabled)').length;
                let checkedVisible = $('.so-row-checkbox:not(:disabled):checked').length;

                if (totalVisible > 0 && totalVisible === checkedVisible) {
                    $('#checkAllSO').prop('checked', true).prop('indeterminate', false);

                    if (recordsDisplay > totalVisible) {
                        $('#pageSelectedCount').text(checkedVisible);
                        $('#totalMatchingCount').text(recordsDisplay.toLocaleString());
                        $('#selectAllBannerText').html(`All <b>${checkedVisible}</b> orders on this page are selected.`);
                        $('#btnSelectAllAcrossPages').removeClass('d-none');
                        $('#btnClearAllSelection').addClass('d-none');
                        $('#selectAllMatchingBanner').removeClass('d-none');
                    } else {
                        $('#selectAllMatchingBanner').addClass('d-none');
                    }
                } else if (checkedVisible > 0) {
                    $('#checkAllSO').prop('checked', false).prop('indeterminate', true);
                    $('#selectAllMatchingBanner').addClass('d-none');
                } else {
                    $('#checkAllSO').prop('checked', false).prop('indeterminate', false);
                    $('#selectAllMatchingBanner').addClass('d-none');
                }
            }

            let table = $('#salesOrderTable').DataTable({
                responsive: true,
                paging: true,
                autoWidth: false,
                searching: true,
                ordering: true,
                info: true,
                lengthChange: true,
                processing: true,
                serverSide: true,
                order: [[2, 'desc']],
                ajax: {
                    url: "{{ url('sales_orders') }}",
                    data: function (d) {
                        d.customer_id = $('#filter_customer').val();
                        d.brand_id = $('#filter_brand').val();
                        d.status = $('#filter_status').val();
                        d.so_date_range = $('#so_date_range').val();
                    }
                },
                columns: [
                    { data: 'checkbox', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'so_no', render: function (data) { return data; } },
                    { data: 'so_date' },
                    { data: 'customer_name' },
                    { data: 'customer_po_ref' },
                    { data: 'total_qty' },
                    { data: 'sales_agent' },
                    { 
                        data: 'status', 
                        orderable: false, 
                        searchable: true,
                        render: function (data, type, row) {
                            if (type === 'filter') {
                                return row.status_text;
                            }
                            return data;
                        }
                    },
                    { data: 'total_amount' },
                    { data: 'action', orderable: false, searchable: false },
                ],
                drawCallback: function () {
                    $('.so-row-checkbox').each(function () {
                        let id = $(this).val();
                        if (isAllMatchingSelected || selectedSoIds.has(id)) {
                            $(this).prop('checked', true);
                            $(this).closest('tr').addClass('selected-row');
                        } else {
                            $(this).closest('tr').removeClass('selected-row');
                        }
                    });
                    updateBulkActionBar();
                }
            });

            // Select all across all pages (Gmail Style)
            $(document).on('click', '#btnSelectAllAcrossPages', function () {
                isAllMatchingSelected = true;
                updateBulkActionBar();
            });

            // Clear all selection
            $(document).on('click', '#btnClearAllSelection', function () {
                isAllMatchingSelected = false;
                selectedSoIds.clear();
                $('.so-row-checkbox').prop('checked', false).closest('tr').removeClass('selected-row');
                $('#checkAllSO').prop('checked', false).prop('indeterminate', false);
                updateBulkActionBar();
            });

            // Check All change
            $(document).on('change', '#checkAllSO', function () {
                let isChecked = $(this).is(':checked');
                isAllMatchingSelected = false; // Reset cross-page state on toggle
                $('.so-row-checkbox:not(:disabled)').each(function () {
                    let id = $(this).val();
                    $(this).prop('checked', isChecked);
                    if (isChecked) {
                        selectedSoIds.add(id);
                        $(this).closest('tr').addClass('selected-row');
                    } else {
                        selectedSoIds.delete(id);
                        $(this).closest('tr').removeClass('selected-row');
                    }
                });
                updateBulkActionBar();
            });

            // Single Row Checkbox change
            $(document).on('change', '.so-row-checkbox', function () {
                let id = $(this).val();
                isAllMatchingSelected = false; // If individual row toggled, cross-page mode drops
                if ($(this).is(':checked')) {
                    selectedSoIds.add(id);
                    $(this).closest('tr').addClass('selected-row');
                } else {
                    selectedSoIds.delete(id);
                    $(this).closest('tr').removeClass('selected-row');
                }
                updateBulkActionBar();
            });

            // Bulk Close Button Click
            $(document).on('click', '#bulkCloseBtn', function () {
                let count = 0;
                try {
                    count = isAllMatchingSelected ? table.page.info().recordsDisplay : selectedSoIds.size;
                } catch (e) {
                    count = selectedSoIds.size;
                }

                if (count === 0) return;

                const messageHtml = isAllMatchingSelected
                    ? `<p class="mb-2">You are about to mark <b>ALL ${count.toLocaleString()}</b> orders matching the current filter as <b>Closed</b>.</p>`
                    : `<p class="mb-2">You are about to mark <b>${count}</b> selected Sales Order(s) as <b>Closed</b>.</p>`;

                Swal.fire({
                    icon: 'warning',
                    title: 'Close Selected Orders?',
                    html: `<div class="text-start">` +
                          messageHtml +
                          `<p class="text-danger small mb-0"><i class="ri-alert-line me-1"></i>Note: Closed orders cannot be processed for delivery or invoicing.</p>` +
                          `</div>`,
                    showCancelButton: true,
                    confirmButtonText: `<i class="ri-close-circle-line me-1"></i> Yes, Close ${count.toLocaleString()} Orders`,
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#ea5455',
                    cancelButtonColor: '#8592a3',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        let payload = {
                            _token: "{{ csrf_token() }}",
                            status: 'Closed'
                        };

                        if (isAllMatchingSelected) {
                            payload.select_all_matching = 1;
                            payload.customer_id = $('#filter_customer').val();
                            payload.status_filter = $('#filter_status').val();
                            payload.so_date_range = $('#so_date_range').val();
                            payload.search = table.search();
                        } else {
                            payload.so_ids = Array.from(selectedSoIds);
                        }

                        return $.ajax({
                            url: "{{ url('sales_orders/bulk-update-status') }}",
                            type: "POST",
                            data: payload
                        }).catch(error => {
                            let errorMsg = 'Failed to close orders';
                            if (error.responseJSON && error.responseJSON.message) {
                                errorMsg = error.responseJSON.message;
                            }
                            Swal.showValidationMessage(errorMsg);
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed && result.value && result.value.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Closed Successfully!',
                            text: result.value.message,
                            timer: 3000,
                            showConfirmButton: false
                        });
                        isAllMatchingSelected = false;
                        selectedSoIds.clear();
                        updateBulkActionBar();
                        table.ajax.reload(null, false);
                    }
                });
            });

            $('#so_date_range').flatpickr({
                mode: 'range',
                dateFormat: 'd-m-Y',
                allowInput: true,
                onClose: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 1) {
                        instance.setDate([selectedDates[0], selectedDates[0]], true);
                    }
                }
            });

            $('#filterBtn').click(function () {
                isAllMatchingSelected = false;
                selectedSoIds.clear();
                updateBulkActionBar();
                table.ajax.reload();
            });

            $('#resetBtn').click(function () {
                isAllMatchingSelected = false;
                selectedSoIds.clear();
                updateBulkActionBar();
                $('#filter_customer').val('').trigger('change');
                $('#filter_brand').val('').trigger('change');
                $('#filter_status').val('').trigger('change');
                $('#so_date_range').val('');
                table.ajax.reload();
            });

            $(document).on('change', '.so-status-change', function () {
                let id = $(this).data('id');
                let status = $(this).val();
                $.ajax({
                    url: "{{ url('sales_orders/status') }}/" + id,
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}", status: status },
                    success: function (response) {
                        let msg = '<span class="text-success">Status Changed</span>';
                        $('.status_msg_' + id).html(msg).fadeIn().delay(1200).fadeOut();
                        table.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        let message = 'Failed to update status';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Approval Blocked!',
                                html: '<div class="text-start p-2" style="font-size: 14px; line-height: 1.6;">' + message + '</div>',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn btn-primary rounded-pill px-4'
                                },
                                buttonsStyling: false
                            });
                        } else {
                            alert(message);
                        }
                        table.ajax.reload(null, false);
                    }
                });
            });
            $(document).on('click', '.delete-btn', function (e) {
                e.preventDefault();
                let url = $(this).attr('href');
                if (confirm('Are you sure you want to delete this sale order?')) {
                    window.location.href = url;
                }
            });
        });
    </script>
@endsection
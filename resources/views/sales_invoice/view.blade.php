@extends('layouts.common')
@section('title', 'Sales Invoices - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Sales Invoices</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create sales-invoice'))
                <a class="btn btn-primary" href="{{ url('sales_invoices/add') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
                @endif
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
                                <div class="form-floating form-floating-outline">
                                    <select name="customer_id" id="customer_id" class="form-select select2" data-placeholder="Select Customer">
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <select name="status" id="status" class="form-select select2" data-placeholder="Select Status">
                                    <option value="">Select Status</option>
                                    <option value="Draft">Draft</option>
                                    <option value="Unpaid/Credit">Unpaid/Credit</option>
                                    <option value="Paid">Paid</option>
                                    <option value="Partially Paid">Partially Paid</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <input type="text" id="inv_date_range" class="form-control" placeholder="Select Invoice Date Range">
                            </div>
                            <div class="col-md-3">
                                <button type="button" id="filterBtn" class="btn btn-primary">Filter</button>
                                <button type="button" id="resetBtn" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable">
                        <table id="salesInvoiceTable" class="table nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Invoice No</th>
                                    <th>Invoice Date</th>
                                    <th>Customer Name / Buyer </th>
                                    <th>Linked SO No.</th>
                                    <th>Total Items</th>
                                    <th>Total Amount</th>
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
    $(function() {
        let table = $('#salesInvoiceTable').DataTable({
            responsive: true,
            paging: true,
            autoWidth: false,
            searching: true,
            ordering: true,
            info: true,
            lengthChange: true,
            ajax: {
                url: "{{ url('sales_invoices') }}",
                data: function(d) {
                    d.status = $('#status').val();
                    d.customer_id = $('#customer_id').val();
                    d.inv_date_range = $('#inv_date_range').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex' },
                { data: 'inv_no' },
                { data: 'inv_date' },
                { data: 'customer_name' },
                { data: 'so_no' },
                { data: 'total_items' },
                { data: 'grand_total' },
                { data: 'status' },
                { data: 'action', orderable: false, searchable: false },
            ]
        });
        $('#inv_date_range').flatpickr({
            mode: 'range',
            dateFormat: 'd-m-Y',
            allowInput: true
        });

        $('#filterBtn').click(function() {
            table.ajax.reload();
        });

        $('#resetBtn').click(function() {
            $('#status').val('').trigger('change');
            $('#customer_id').val('').trigger('change');
            $('#inv_date_range').val('');
            table.ajax.reload();
        });

        $(document).on('change', '.inv-status-change', function() {
            let id = $(this).data('id');
            let status = $(this).val();
            let $select = $(this);

            $.ajax({
                url: "{{ url('sales_invoices/status') }}/" + id,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    status: status
                },
                success: function(response) {
                    let msg = '<span class="text-success">Status Changed</span>';
                    $('.status_msg_' + id).html(msg).fadeIn().delay(2000).fadeOut();
                    table.ajax.reload(null, false);
                },
                error: function(xhr) {
                    let message = 'Failed to update status';

                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.message) {
                        let full = xhr.responseJSON.message;
                        let balance = full.match(/Outstanding balance of ([\d,\.]+)/);
                        message = balance 
                            ? '⚠ Balance of ' + balance[1] + ' still remaining' 
                            : full;
                    }

                    let msg = '<span class="text-danger">' + message + '</span>';
                    $('.status_msg_' + id).html(msg).fadeIn().delay(2000).fadeOut();

                    table.ajax.reload(null, false);
                }
            });
        });

        $(document).on('click', '.einvoice-generate-btn', function() {
            let invoiceId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to generate an E-Invoice for this Sales Invoice?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, generate it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let btn = $(this);
                    let originalHtml = btn.html();
                    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                    
                    Swal.fire({
                        title: 'Generating...',
                        text: 'Please wait while we generate the E-Invoice.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    $.ajax({
                        url: "{{ url('sales_invoices/generate-einvoice') }}/" + invoiceId,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                let msg = response.message;
                                let isWarning = false;
                                if (response.eway_bill && !response.eway_bill.success) {
                                    msg += " However, E-Way Bill failed: " + response.eway_bill.message;
                                    isWarning = true;
                                }

                                if (isWarning) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Partial Success',
                                        text: msg,
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: msg,
                                        timer: 3000,
                                        showConfirmButton: false
                                    });
                                }
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                                btn.prop('disabled', false).html(originalHtml);
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
                            btn.prop('disabled', false).html(originalHtml);
                        }
                    });
                }
            });
        });

        $(document).on('click', '.einvoice-cancel-btn', function() {
            let invoiceId = $(this).data('id');
            Swal.fire({
                title: 'Cancel E-Invoice',
                html: `
                    <div class="alert alert-warning text-start p-2 mb-3" style="font-size: 13.5px; border: 1px solid #ffd8a8; background-color: #fff9db; color: #d9480f; border-radius: 4px; line-height: 1.4;">
                        <div class="fw-semibold mb-1" style="font-size: 14.5px; color: #d9480f;"><i class="ri-information-line me-1"></i> GST e-Invoicing Guidelines:</div>
                        <ul class="mb-0 ps-3">
                            <li><strong>24-Hour Window:</strong> IRN can only be cancelled within 24 hours of generation.</li>
                            <li><strong>E-Way Bill:</strong> If linked to an active E-Way Bill, the E-Way Bill will be automatically cancelled first.</li>
                            <li><strong>No Re-Use:</strong> Once cancelled, this invoice number cannot be reused to generate another IRN.</li>
                            <li><strong>Full Cancellation:</strong> Partial cancellations are not allowed.</li>
                        </ul>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="cancel_reason" class="form-label font-semibold text-dark">Cancellation Reason <span class="text-danger">*</span></label>
                        <select id="cancel_reason" class="form-select">
                            <option value="1">1 - Duplicate</option>
                            <option value="2" selected>2 - Data Entry Mistake</option>
                            <option value="3">3 - Order Cancelled</option>
                            <option value="4">4 - Others</option>
                        </select>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="cancel_remarks" class="form-label font-semibold text-dark">Remarks / Explanation <span class="text-danger">*</span></label>
                        <textarea id="cancel_remarks" class="form-control" rows="2" placeholder="Explain the reason for cancellation (min 3 chars)"></textarea>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, cancel it!',
                preConfirm: () => {
                    const reason = Swal.getPopup().querySelector('#cancel_reason').value;
                    const remarks = Swal.getPopup().querySelector('#cancel_remarks').value.trim();
                    if (!reason) {
                        Swal.showValidationMessage(`Please select a reason`);
                    } else if (remarks.length < 3) {
                        Swal.showValidationMessage(`Remarks must be at least 3 characters`);
                    }
                    return { cancel_reason: reason, cancel_remarks: remarks }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let btn = $(this);
                    let originalHtml = btn.html();
                    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                    
                    Swal.fire({
                        title: 'Canceling...',
                        text: 'Please wait while we cancel the E-Invoice.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    $.ajax({
                        url: "{{ url('sales_invoices/cancel-einvoice') }}/" + invoiceId,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            cancel_reason: result.value.cancel_reason,
                            cancel_remarks: result.value.cancel_remarks
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Canceled!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                                btn.prop('disabled', false).html(originalHtml);
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
                            btn.prop('disabled', false).html(originalHtml);
                        }
                    });
                }
            });
        });

        $(document).on('click', '.einvoice-expired-btn', function() {
            Swal.fire({
                title: 'Cancellation Expired',
                text: 'According to GST guidelines, an E-Invoice cannot be cancelled after 24 hours of generation. Please issue a Credit Note instead.',
                icon: 'error',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        });
    });
</script>
@endsection
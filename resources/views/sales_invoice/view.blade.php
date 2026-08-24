@extends('layouts.common')
@section('title', 'Sales Invoices - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Sales Invoices</h4>
                <div>
                    @if(auth()->id() == 1 || auth()->user()->can('view sales-invoice-report'))
                    <a class="btn btn-outline-success me-2" href="{{ url('sales_invoices/report') }}">
                        <i class="menu-icon icon-base ri ri-file-excel-2-line"></i> Report & Export
                    </a>
                    @endif
                    @if(auth()->id() == 1 || auth()->user()->can('create sales-invoice'))
                    <a class="btn btn-primary" href="{{ url('sales_invoices/add') }}">
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
                                    <th>Total Qty</th>
                                    <th>Sub Total</th>
                                    <th>Discount</th>
                                    <th>Taxable Value</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Delivery Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-end fw-bold">Total:</th>
                                    <th id="footer-total-qty" class="fw-bold">0</th>
                                    <th id="footer-sub-total" class="fw-bold">₹0.00</th>
                                    <th id="footer-discount" class="fw-bold">₹0.00</th>
                                    <th id="footer-taxable-value" class="fw-bold">₹0.00</th>
                                    <th id="footer-grand-total" class="fw-bold">₹0.00</th>
                                    <th colspan="3"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delivery Status Modal -->
<div class="modal fade" id="deliveryStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel2">Update Delivery Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-2 mt-2">
                        <input type="hidden" id="ds_invoice_id">
                        <div class="form-floating form-floating-outline mb-2">
                            <select id="ds_status" class="form-select">
                                <option value="Pending">Pending</option>
                                <option value="Dispatched">Dispatched</option>
                                <option value="Partially Delivered">Partially Delivered</option>
                                <option value="Delivered">Delivered</option>
                                <option value="Cancel">Cancel</option>
                            </select>
                            <label for="ds_status">Status</label>
                        </div>
                        <div id="ds_status_msg" class="small fw-bold"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveDeliveryStatusBtn">Save changes</button>
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
            serverSide: true,
            processing: true,
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
                { data: 'total_qty' },
                { data: 'sub_total' },
                { data: 'discount' },
                { data: 'taxable_value' },
                { data: 'grand_total' },
                { 
                    data: 'status',
                    searchable: true,
                    render: function (data, type, row) {
                        if (type === 'filter') {
                            return row.status_text;
                        }
                        return data;
                    }
                },
                {
                    data: 'delivery_status',
                    render: function(data, type, row) {
                        let badgeClass = 'bg-secondary';
                        let statusText = data || 'Pending';
                        if (statusText === 'Delivered') badgeClass = 'bg-success';
                        else if (statusText === 'Dispatched') badgeClass = 'bg-info';
                        else if (statusText === 'Partially Delivered') badgeClass = 'bg-warning';
                        else if (statusText === 'Pending') badgeClass = 'bg-secondary';
                        else if (statusText === 'Cancel') badgeClass = 'bg-danger';

                        let html = `<span class="badge ${badgeClass}">${statusText}</span>`;
                        @if(auth()->id() == 1)
                        html += ` <button class="btn btn-sm btn-icon btn-text-primary rounded-pill ms-1 edit-delivery-status" data-id="${row.id}" data-status="${statusText}" title="Update Delivery Status"><i class="ri ri-pencil-line"></i></button>`;
                        @endif
                        return html;
                    }
                },
                { data: 'action', orderable: false, searchable: false },
            ],
            footerCallback: function (row, data, start, end, display) {
                let api = this.api();

                let intVal = function (i) {
                    return typeof i === 'string' ? i.replace(/[\₹,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                };

                let json = api.ajax.json();
                
                if (json) {
                    let totalQty = json.overallTotalQty || 0;
                    let subTotal = json.overallSubTotal || 0;
                    let totalDiscount = json.overallDiscount || 0;
                    let totalTaxable = json.overallTaxable || 0;
                    let grandTotal = json.overallGrandTotal || 0;

                    $('#footer-total-qty').html(totalQty);
                    $('#footer-sub-total').html('₹' + subTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    $('#footer-discount').html('₹' + totalDiscount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    $('#footer-taxable-value').html('₹' + totalTaxable.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    $('#footer-grand-total').html('₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                }
            }
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
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    html: response.message
                                });
                                btn.prop('disabled', false).html(originalHtml);
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 419) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Session Expired',
                                    text: 'Your session has expired or the page was idle. Please reload the page to refresh your CSRF token.',
                                    confirmButtonText: 'Reload Page'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
                            }
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
                            <option value="">Select Reason</option>
                            <option value="1">1 - Duplicate</option>
                            <option value="2">2 - Data Entry Mistake</option>
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
                        error: function(xhr) {
                            if (xhr.status === 419) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Session Expired',
                                    text: 'Your session has expired or the page was idle. Please reload the page to refresh your CSRF token.',
                                    confirmButtonText: 'Reload Page'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
                            }
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

        $(document).on('click', '.edit-delivery-status', function() {
            let id = $(this).data('id');
            let status = $(this).data('status');
            $('#ds_invoice_id').val(id);
            $('#ds_status').val(status);
            $('#ds_status_msg').html('');
            
            $('#ds_status option').prop('disabled', false);

            if (status === 'Delivered') {
                $('#ds_status option[value="Pending"]').prop('disabled', true);
                $('#ds_status option[value="Dispatched"]').prop('disabled', true);
            } else if (status === 'Cancel') {
                $('#ds_status option:not([value="Cancel"])').prop('disabled', true);
            } else if (status === 'Pending') {
                $('#ds_status option[value="Delivered"]').prop('disabled', true);
            }
            
            $('#deliveryStatusModal').modal('show');
        });

        $('#saveDeliveryStatusBtn').click(function() {
            let id = $('#ds_invoice_id').val();
            let status = $('#ds_status').val();
            let btn = $(this);
            let originalHtml = btn.html();
            
            $('#ds_status_msg').html('');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');
            
            $.ajax({
                url: '{{ url("sales_invoices/update_delivery_status") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    invoice_id: id,
                    delivery_status: status
                },
                success: function(response) {
                    btn.prop('disabled', false).html(originalHtml);
                    if (response.success) {
                        $('#deliveryStatusModal').modal('hide');
                        table.ajax.reload(null, false);
                    } else {
                        $('#ds_status_msg').html('<span class="text-danger">' + response.message + '</span>');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalHtml);
                    let errorMsg = 'An error occurred while updating status.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    $('#ds_status_msg').html('<span class="text-danger">' + errorMsg + '</span>');
                }
            });
        });
    });
</script>
@endsection
@extends('layouts.common')
@section('title', 'Credit Notes - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Credit Notes</h4>
                @if(auth()->id() == 1 || auth()->user()->can('create credit-notes'))
                <a class="btn btn-primary" href="{{ url('credit_notes/add') }}">
                    <i class="menu-icon icon-base ri ri-add-circle-line"></i> Add
                </a>
                @endif
            </div>
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <div class="filter-box mb-4">
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
                                    <option value="Approved">Approved</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <input type="text" id="note_date_range" class="form-control" placeholder="Select Note Date Range">
                            </div>
                            <div class="col-md-3">
                                <button type="button" id="filterBtn" class="btn btn-primary">Filter</button>
                                <button type="button" id="resetBtn" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-datatable table-responsive">
                        <table class="table" id="creditNoteTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Note No.</th>
                                    <th>Date</th>
                                    <th>Invoice No.</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
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
        var table = $('#creditNoteTable').DataTable({
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
                url: "{{ url('credit_notes') }}",
                data: function(d) {
                    d.status = $('#status').val();
                    d.customer_id = $('#customer_id').val();
                    d.note_date_range = $('#note_date_range').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex' },
                { data: 'note_no' },
                { data: 'note_date' },
                { data: 'sales_invoice_no' },
                { data: 'customer_name' },
                { data: 'grand_total' },
                { data: 'status' },
                { data: 'action', orderable: false, searchable: false },
            ]
        });

        $('#note_date_range').flatpickr({
            mode: "range",
            dateFormat: "d-m-Y"
        });

        $('#filterBtn').click(function() {
            table.ajax.reload();
        });

        $('#resetBtn').click(function() {
            $('#customer_id').val('').trigger('change');
            $('#status').val('').trigger('change');
            $('#note_date_range').val('');
            table.ajax.reload();
        });

        $(document).on('change', '.status-dropdown', function() {
            var id = $(this).data('id');
            var status = $(this).val();
            var $msg = $('.status_msg_' + id);

            $.ajax({
                url: "{{ url('credit_notes/status') }}/" + id,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status
                },
                success: function(response) {
                    $msg.html('<span class="text-success small">Updated Successfully!</span>').fadeIn().delay(1000).fadeOut();
                    table.ajax.reload(null, false);
                }
            });
        });

        // E-Invoice Generation
        $(document).on('click', '.einvoice-generate-btn', function() {
            var id = $(this).data('id');
            var btn = $(this);
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to generate an E-Invoice for this Credit Note?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, generate it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');
                    
                    Swal.fire({
                        title: 'Generating...',
                        text: 'Please wait while we generate the E-Invoice.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: "{{ url('credit_notes/generate-einvoice') }}/" + id,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Success', response.message, 'success').then(() => {
                                    table.ajax.reload(null, false);
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error');
                                btn.prop('disabled', false).html('<i class="ri ri-receipt-line"></i>');
                            }
                        },
                        error: function(xhr) {
                            var errMsg = 'An error occurred';
                            if(xhr.responseJSON && xhr.responseJSON.message) {
                                errMsg = xhr.responseJSON.message;
                            }
                            Swal.fire('Error', errMsg, 'error');
                            btn.prop('disabled', false).html('<i class="ri ri-receipt-line"></i>');
                        }
                    });
                }
            });
        });

        // E-Invoice Cancellation
        $(document).on('click', '.einvoice-cancel-btn', function() {
            var id = $(this).data('id');
            var btn = $(this);
            Swal.fire({
                title: 'Cancel E-Invoice',
                html: `
                    <div class="text-start">
                        <p class="text-danger fw-bold mb-2">Warning: This action cannot be undone.</p>
                        <p class="mb-2">Are you sure you want to cancel the E-Invoice?</p>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i>');
                    
                    Swal.fire({
                        title: 'Cancelling...',
                        text: 'Please wait while we cancel the E-Invoice.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: "{{ url('credit_notes/cancel-einvoice') }}/" + id,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Success', response.message, 'success').then(() => {
                                    table.ajax.reload(null, false);
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error');
                                btn.prop('disabled', false).html('<i class="ri ri-close-circle-line"></i>');
                            }
                        },
                        error: function(xhr) {
                            var errMsg = 'An error occurred';
                            if(xhr.responseJSON && xhr.responseJSON.message) {
                                errMsg = xhr.responseJSON.message;
                            }
                            Swal.fire('Error', errMsg, 'error');
                            btn.prop('disabled', false).html('<i class="ri ri-close-circle-line"></i>');
                        }
                    });
                }
            });
        });
    });
</script>
@endsection

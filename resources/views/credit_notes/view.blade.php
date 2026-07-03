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
            ajax: "{{ url('credit_notes') }}",
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

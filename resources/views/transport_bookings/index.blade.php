@extends('layouts.common')
@section('title', 'Transport Handover / LR Register - ' . env('WEBSITE_NAME'))

@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Transport Handover / LR Register</h4>
                <div class="d-flex gap-2">
                    @if(auth()->id() == 1 || auth()->user()->can('clearance transport-handover'))
                    <a class="btn btn-outline-success" href="{{ route('transport_bookings.payment_clearance') }}">
                        <i class="menu-icon icon-base ri ri-money-dollar-circle-line"></i> Driver Weekly Clearance
                    </a>
                    @endif
                    <a class="btn btn-primary" target="_blank" href="{{ route('transport_bookings.print_sheet') }}" id="btnPrintSheet">
                        <i class="menu-icon icon-base ri ri-printer-line"></i> Print Handover Sheet (A4)
                    </a>
                </div>
            </div>

            <div class="col-lg-12">
                @include('flash_messages')
            </div>

            <div class="card">
                <div class="card-body">
                    <!-- 3 KPI Metric Summary Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card border shadow-none mb-0 bg-label-primary">
                                <div class="card-body py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="text-muted small text-uppercase fw-semibold">Total Dispatched Boxes</span>
                                            <h3 class="mb-0 fw-bold mt-1 text-primary" id="kpi-total-boxes">{{ $totalBoxesCount }}</h3>
                                        </div>
                                        <div class="avatar bg-white text-primary rounded p-2 shadow-xs d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                            <i class="menu-icon icon-base ri ri-archive-line fs-3"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border shadow-none mb-0 bg-label-success">
                                <div class="card-body py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="text-muted small text-uppercase fw-semibold">Confirmed Booked (LR Verified)</span>
                                            <h3 class="mb-0 fw-bold mt-1 text-success" id="kpi-booked-boxes">{{ $bookedBoxesCount }}</h3>
                                        </div>
                                        <div class="avatar bg-white text-success rounded p-2 shadow-xs d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                            <i class="menu-icon icon-base ri ri-checkbox-circle-line fs-3"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border shadow-none mb-0 bg-label-danger">
                                <div class="card-body py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="text-muted small text-uppercase fw-semibold">Pending LR (Payment Locked)</span>
                                            <h3 class="mb-0 fw-bold mt-1 text-danger" id="kpi-pending-lr">{{ $pendingLrCount }}</h3>
                                        </div>
                                        <div class="avatar bg-white text-danger rounded p-2 shadow-xs d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                            <i class="menu-icon icon-base ri ri-error-warning-line fs-3"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Policy Banner -->
                    <div class="alert alert-danger d-flex align-items-center mb-4 py-2" role="alert">
                        <i class="menu-icon icon-base ri ri-error-warning-fill me-2 fs-5"></i>
                        <div>
                            <strong>IMPORTANT POLICY: NO LR NUMBER = NO PAYMENT CLEARANCE.</strong> The system strictly blocks booking confirmation and driver payout clearance without a verified LR receipt number.
                        </div>
                    </div>

                    <!-- Filter Box (Matching sales_invoice/view) -->
                    <div class="filter-box mb-4">
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <h5>Filter</h5>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <select id="filter_customer_id" class="form-select select2" data-placeholder="Select Customer">
                                    <option value="">All Customers</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-2">
                                <select id="filter_driver_id" class="form-select select2" data-placeholder="Select Driver">
                                    <option value="">All Drivers</option>
                                    @foreach($drivers as $d)
                                        <option value="{{ $d['name'] }}">{{ $d['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-lg-2">
                                <select id="filter_lr_status" class="form-select select2" data-placeholder="LR Status">
                                    <option value="">All LR Status</option>
                                    <option value="pending">Pending LR (Unbooked)</option>
                                    <option value="booked">Booked (Has LR)</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-lg-2">
                                <select id="filter_transport_name" class="form-select select2" data-placeholder="Select Transporter">
                                    <option value="">All Transporters</option>
                                    @foreach($transporters as $t)
                                        <option value="{{ $t }}">{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <div class="d-flex gap-2">
                                    <input type="text" id="filter_date" class="form-control flatpickr-date" placeholder="Dispatched Date">
                                    <button type="button" id="filterBtn" class="btn btn-primary">Filter</button>
                                    <button type="button" id="resetBtn" class="btn btn-secondary">Reset</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DataTables Container -->
                    <div class="card-datatable table-responsive">
                        <table id="transportTable" class="table table-hover table-sm w-100">
                            <thead>
                                <tr>
                                    <th style="width: 35px;" class="text-center no-sort">
                                        <input type="checkbox" id="checkAllBookings" class="form-check-input" title="Select All Pending Handover Boxes">
                                    </th>
                                    <th style="width: 30px;">#</th>
                                    <th>Invoice No</th>
                                    <th>Invoice Date</th>
                                    <th>Customer / Place</th>
                                    <th>Box No</th>
                                    <th>Qty</th>
                                    <th>Transport Name</th>
                                    <th>Driver</th>
                                    <th>LR Number</th>
                                    <th>Freight (₹)</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
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

<!-- Update LR Details Modal (Matching deliveryStatusModal model) -->
<div class="modal fade" id="updateLrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Update Transport LR Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modal_booking_id">

                <div class="p-2 mb-3 bg-light rounded">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted d-block">Invoice / Box:</small>
                            <span class="fw-bold text-primary" id="modal_display_invoice">-</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Customer:</small>
                            <span class="fw-bold text-dark" id="modal_display_customer">-</span>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Assigned Driver (Outside Driver)</label>
                        <select id="modal_driver_id" class="form-select select2-modal-tags">
                            <option value="">Select or Type Outside Driver Name</option>
                            @foreach($drivers as $d)
                                <option value="{{ $d['name'] }}">{{ $d['name'] }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1">
                            <i class="ri-information-line me-1"></i>You can select an existing driver or type any outside driver name directly (e.g. Ramesh Auto, Mani Tempo).
                        </small>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Transport Name</label>
                        <input type="text" id="modal_transport_name" class="form-control" placeholder="e.g. AKR Transport, VRL, ABT">
                    </div>

                    <div class="col-md-7">
                        <label class="form-label fw-semibold text-danger">LR Number * (Mandatory)</label>
                        <input type="text" id="modal_lr_no" class="form-control font-monospace" placeholder="Enter Physical LR Receipt No">
                        <div class="invalid-feedback fw-semibold mt-1" id="lr_error_text" style="display: none;">
                            LR Number is required. (NO LR NUMBER = NO PAYMENT CLEARANCE)
                        </div>
                        <small class="text-muted d-block mt-1" id="lr_policy_hint">NO LR NUMBER = NO PAYMENT CLEARANCE</small>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Freight Price (₹)</label>
                        <input type="number" step="any" min="0" id="modal_lr_price" class="form-control text-end" placeholder="0.00">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Dispatched Date</label>
                        <input type="text" id="modal_booking_date" class="form-control flatpickr-date" value="{{ date('d-m-Y') }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Box Number</label>
                        <input type="text" id="modal_box_number" class="form-control font-monospace" placeholder="e.g. 1/3, 2/3">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" id="modal_customer_email" class="form-control" placeholder="Enter Email">
                    </div>

                    <div class="col-12" id="modal_error_msg"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnSaveLr">
                    <i class="ri ri-save-line me-1"></i> Save & Confirm LR
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function() {
    // Select2
    if ($.fn.select2) {
        $('.select2').select2({
            allowClear: true,
            placeholder: function() {
                return $(this).data('placeholder') || 'Select';
            }
        });

        $('#updateLrModal').on('shown.bs.modal', function () {
            $('.select2-modal-tags').select2({
                dropdownParent: $('#updateLrModal'),
                allowClear: true,
                tags: true,
                placeholder: 'Select or Type Outside Driver Name',
                createTag: function (params) {
                    var term = $.trim(params.term);
                    if (term === '') {
                        return null;
                    }
                    return {
                        id: term,
                        text: term + ' (New Outside Driver)',
                        newTag: true
                    };
                }
            });
        });
    }

    // Flatpickr
    if ($.fn.flatpickr) {
        $('.flatpickr-date').flatpickr({
            dateFormat: 'd-m-Y',
            allowInput: true
        });
    }

    // Server-side DataTables
    let table = $('#transportTable').DataTable({
        responsive: true,
        paging: true,
        autoWidth: false,
        searching: true,
        ordering: false,
        info: true,
        lengthChange: true,
        serverSide: true,
        processing: true,
        searchDelay: 500,
        ajax: {
            url: "{{ route('transport_bookings.index') }}",
            data: function(d) {
                d.customer_id = $('#filter_customer_id').val();
                d.driver_id = $('#filter_driver_id').val();
                d.lr_status = $('#filter_lr_status').val();
                d.transport_name = $('#filter_transport_name').val();
                d.date = $('#filter_date').val();
            }
        },
        columns: [
            { data: 'checkbox', orderable: false, searchable: false, className: 'text-center' },
            { data: 'DT_RowIndex', className: 'text-center' },
            { data: 'inv_no', className: 'text-nowrap' },
            { data: 'inv_date', className: 'text-nowrap' },
            { data: 'customer' },
            { data: 'box_number', className: 'text-nowrap' },
            { data: 'quantity', className: 'text-center' },
            { data: 'transport_name' },
            { data: 'driver' },
            { data: 'lr_no', className: 'text-nowrap' },
            { data: 'freight_price', className: 'text-end text-nowrap' },
            { data: 'status', className: 'text-nowrap' },
            { data: 'action', orderable: false, searchable: false, className: 'text-center text-nowrap' }
        ],
        drawCallback: function() {
            let rows = $('.row-booking-checkbox');
            if (rows.length === 0) {
                $('#checkAllBookings').prop('checked', false);
                return;
            }
            let allChecked = true;
            rows.each(function() {
                let id = $(this).val();
                if (selectedBookingIds.has(id)) {
                    $(this).prop('checked', true);
                } else {
                    $(this).prop('checked', false);
                    allChecked = false;
                }
            });
            $('#checkAllBookings').prop('checked', allChecked);
        }
    });

    // Checkbox selection tracking
    let selectedBookingIds = new Set();

    function updateSelectedCountAndPrintUrl() {
        let count = selectedBookingIds.size;
        let btnText = count > 0 
            ? `<i class="menu-icon icon-base ri ri-printer-line"></i> Print Handover Sheet (${count} Selected)`
            : `<i class="menu-icon icon-base ri ri-printer-line"></i> Print Handover Sheet (A4)`;
        $('#btnPrintSheet').html(btnText);
        updatePrintSheetUrl();
    }

    $(document).on('change', '.row-booking-checkbox', function() {
        let id = $(this).val();
        if ($(this).is(':checked')) {
            selectedBookingIds.add(id);
        } else {
            selectedBookingIds.delete(id);
            $('#checkAllBookings').prop('checked', false);
        }
        updateSelectedCountAndPrintUrl();
    });

    $('#checkAllBookings').on('change', function() {
        let isChecked = $(this).is(':checked');
        $('.row-booking-checkbox').each(function() {
            let id = $(this).val();
            $(this).prop('checked', isChecked);
            if (isChecked) {
                selectedBookingIds.add(id);
            } else {
                selectedBookingIds.delete(id);
            }
        });
        updateSelectedCountAndPrintUrl();
    });

    // Filter & Reset buttons
    $('#filterBtn').click(function() {
        table.ajax.reload();
        updatePrintSheetUrl();
    });

    $('#resetBtn').click(function() {
        $('#filter_customer_id').val('').trigger('change');
        $('#filter_driver_id').val('').trigger('change');
        $('#filter_lr_status').val('').trigger('change');
        $('#filter_transport_name').val('').trigger('change');
        $('#filter_date').val('');
        selectedBookingIds.clear();
        $('#checkAllBookings').prop('checked', false);
        updateSelectedCountAndPrintUrl();
        table.search('').draw();
    });

    function updatePrintSheetUrl() {
        let params = new URLSearchParams();

        if (selectedBookingIds.size > 0) {
            params.append('booking_ids', Array.from(selectedBookingIds).join(','));
        } else {
            let dateVal = $('#filter_date').val();
            let customerId = $('#filter_customer_id').val();
            let driverId = $('#filter_driver_id').val();
            let lrStatus = $('#filter_lr_status').val();
            let transportName = $('#filter_transport_name').val();

            if (dateVal) params.append('date', dateVal);
            if (customerId) params.append('customer_id', customerId);
            if (driverId) params.append('driver_id', driverId);
            if (lrStatus) params.append('lr_status', lrStatus);
            if (transportName) params.append('transport_name', transportName);
        }

        let baseUrl = "{{ route('transport_bookings.print_sheet') }}";
        let queryString = params.toString();
        $('#btnPrintSheet').attr('href', queryString ? baseUrl + '?' + queryString : baseUrl);
    }

    $('#filter_customer_id, #filter_driver_id, #filter_lr_status, #filter_transport_name').on('change', function() {
        updatePrintSheetUrl();
    });
    $('#filter_date').on('input change', updatePrintSheetUrl);

    // Open LR Modal
    $(document).on('click', '.btn-open-lr-modal', function() {
        let btn = $(this);
        let id = btn.data('id');
        let invNo = btn.data('invoice');
        let box = btn.data('box');
        let customer = btn.data('customer');
        let transport = btn.data('transport');
        let rate = btn.data('rate');
        let lr = btn.data('lr');
        let driver = btn.data('driver');
        let driverName = btn.data('driver-name');

        $('#modal_booking_id').val(id);
        $('#modal_display_invoice').text(invNo + ' (' + box + ')');
        $('#modal_display_customer').text(customer);
        let custEmail = btn.data('customer-email') || '';
        $('#modal_customer_email').val(custEmail);
        $('#modal_transport_name').val(transport || '');
        $('#modal_lr_price').val(rate || '');
        $('#modal_lr_no').val(lr || '').removeClass('is-invalid');
        $('#modal_box_number').val(box || '');
        $('#lr_error_text').hide();
        $('#lr_policy_hint').show();
        let bDate = btn.data('booking-date') || "{{ date('d-m-Y') }}";
        $('#modal_booking_date').val(bDate);
        $('#modal_error_msg').html('');

        let selectVal = driver || driverName || '';
        if (selectVal) {
            let exists = false;
            $('#modal_driver_id option').each(function() {
                if ($(this).val() == selectVal || $(this).text().trim() == driverName) {
                    exists = true;
                    selectVal = $(this).val();
                    return false;
                }
            });

            if (!exists && driverName) {
                let newOption = new Option(driverName, driverName, true, true);
                $('#modal_driver_id').append(newOption);
                selectVal = driverName;
            }

            $('#modal_driver_id').val(selectVal).trigger('change');
        } else {
            $('#modal_driver_id').val('').trigger('change');
        }

        $('#updateLrModal').modal('show');
    });

    $(document).on('input', '#modal_lr_no', function() {
        if ($(this).val().trim() !== '') {
            $(this).removeClass('is-invalid');
            $('#lr_error_text').hide();
            $('#lr_policy_hint').show();
        }
    });

    // Save LR Details
    $('#btnSaveLr').click(function() {
        let id = $('#modal_booking_id').val();
        let lrNo = $('#modal_lr_no').val().trim();
        let lrPrice = $('#modal_lr_price').val().trim();
        let transport = $('#modal_transport_name').val().trim();
        let rawDriver = $('#modal_driver_id').val();
        let driverId = rawDriver ? rawDriver.replace(/\s*\(New Outside Driver\)$/i, '').trim() : '';
        let bookingDate = $('#modal_booking_date').val();
        let boxNumber = $('#modal_box_number').val().trim();
        let customerEmail = $('#modal_customer_email').val().trim();

        if (!lrNo) {
            $('#modal_lr_no').addClass('is-invalid').focus();
            $('#lr_error_text').text('LR Number is required. (NO LR NUMBER = NO PAYMENT CLEARANCE)').show();
            $('#lr_policy_hint').hide();
            return;
        }

        let btn = $(this);
        let originalHtml = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');
        $('#modal_error_msg').html('');

        $.ajax({
            url: "{{ url('transport_bookings') }}/" + id + "/record_lr",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                lr_no: lrNo,
                lr_price: lrPrice,
                preferred_transport: transport,
                delivery_agent_id: driverId,
                booking_date: bookingDate,
                box_number: boxNumber,
                customer_email: customerEmail
            },
            success: function(response) {
                btn.prop('disabled', false).html(originalHtml);
                if (response.success) {
                    $('#updateLrModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'LR Confirmed!',
                        text: response.message,
                        timer: 3500,
                        showConfirmButton: false
                    });
                    table.ajax.reload(null, false);
                } else {
                    $('#modal_error_msg').html('<div class="alert alert-danger py-1 mt-2">' + response.message + '</div>');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalHtml);
                let errMsg = 'Failed to save LR details.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.lr_no) {
                    $('#modal_lr_no').addClass('is-invalid').focus();
                    $('#lr_error_text').text(xhr.responseJSON.errors.lr_no[0]).show();
                    $('#lr_policy_hint').hide();
                } else {
                    $('#modal_error_msg').html('<div class="alert alert-danger py-1 mt-2">' + errMsg + '</div>');
                }
            }
        });
    });
});
</script>
@endsection

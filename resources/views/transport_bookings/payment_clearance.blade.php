@extends('layouts.common')
@section('title', 'Driver Weekly Payment Clearance - ' . env('WEBSITE_NAME'))

@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Driver Weekly Payment Clearance</h4>
                <div class="d-flex gap-2">
                    <a class="btn btn-primary" target="_blank" href="{{ route('transport_bookings.print_clearance_pdf') }}" id="btnPrintClearanceSheet">
                        <i class="menu-icon icon-base ri ri-printer-line"></i> Print Clearance Sheet (A4)
                    </a>
                    <a class="btn btn-secondary" href="{{ route('transport_bookings.index') }}">
                        <i class="menu-icon icon-base ri ri-arrow-left-line"></i> Back to LR Register
                    </a>
                </div>
            </div>

            <div class="col-lg-12">
                @include('flash_messages')
            </div>

            <div class="card">
                <div class="card-body">
                    <!-- Policy Warning Banner -->
                    <div class="alert alert-warning d-flex align-items-center mb-4 py-2" role="alert">
                        <i class="menu-icon icon-base ri ri-shield-check-line me-2 fs-4 text-warning"></i>
                        <div>
                            <strong>PAYMENT CLEARANCE POLICY: NO LR NUMBER = NO PAYMENT CLEARANCE.</strong>
                            Payment is strictly disbursed only for boxes that have confirmed, physical/digital LR receipt numbers entered. Unbooked or missing LR boxes are automatically held.
                        </div>
                    </div>

                    <!-- Filter Box -->
                    <div class="filter-box mb-4">
                        <form id="filterForm">
                            <div class="row g-3">
                                <div class="col-lg-12">
                                    <h5>Filter Batches</h5>
                                </div>
                                <div class="col-md-3">
                                    <select name="driver_id" id="driver_id" class="form-select select2" data-placeholder="All Drivers">
                                        <option value="">All Drivers</option>
                                        @foreach($drivers as $d)
                                            <option value="{{ $d['id'] }}" {{ (string)$selectedDriverId === (string)$d['id'] ? 'selected' : '' }}>
                                                {{ $d['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="payout_status" id="payout_status" class="form-select select2" data-placeholder="Payment Status">
                                        <option value="">All (Cleared & Pending)</option>
                                        <option value="cleared">Only Cleared Invoices</option>
                                        <option value="pending">Only Pending Clearance</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="start_date" id="start_date" class="form-control flatpickr-date" value="{{ $startDate->format('d-m-Y') }}" placeholder="From Date">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="end_date" id="end_date" class="form-control flatpickr-date" value="{{ $endDate->format('d-m-Y') }}" placeholder="To Date">
                                </div>
                                <div class="col-md-2">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-primary flex-fill text-nowrap" id="btnFilterBatches">
                                            <i class="menu-icon icon-base ri ri-filter-line"></i> Load Batches
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" id="btnResetFilter" title="Reset Filters">
                                            <i class="ri ri-refresh-line"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- KPI Metric Cards (Clickable to switch tabs) -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="card border shadow-none bg-label-success mb-0" id="kpi_card_eligible" style="cursor: pointer;" title="Click to view Eligible Consignments">
                                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="d-flex align-items-center mb-1">
                                            <span class="small text-uppercase fw-semibold text-muted">Eligible Boxes for Payout</span>
                                            <span class="badge bg-success ms-2" style="font-size: 0.65rem;">Active View</span>
                                        </div>
                                        <h3 class="mb-0 fw-bold text-success" id="kpi_eligible_count">{{ $eligibleCount ?? $eligibleBookings->count() }} Boxes</h3>
                                        <small class="text-muted"><i class="ri ri-checkbox-circle-line text-success me-1"></i>Verified with confirmed LR numbers</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="small text-uppercase fw-semibold text-muted">Total Approved Payout</span>
                                        <h3 class="mb-0 fw-bold mt-1 text-success" id="kpi_eligible_amount">₹{{ number_format($eligibleAmount, 2) }}</h3>
                                        <small class="text-success fw-medium">Ready for Clearance &rarr;</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border shadow-none bg-label-danger mb-0" id="kpi_card_blocked" style="cursor: pointer;" title="Click to view Blocked Consignments">
                                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="d-flex align-items-center mb-1">
                                            <span class="small text-uppercase fw-semibold text-muted">Blocked / Pending LR Boxes</span>
                                            <span class="badge bg-danger ms-2" style="font-size: 0.65rem;">Action Needed</span>
                                        </div>
                                        <h3 class="mb-0 fw-bold text-danger" id="kpi_blocked_count">{{ $blockedCount ?? $blockedBookings->count() }} Boxes</h3>
                                        <small class="text-muted"><i class="ri ri-lock-line text-danger me-1"></i>Payment strictly locked (No LR receipt)</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="small text-uppercase fw-semibold text-muted">Held Amount</span>
                                        <h3 class="mb-0 fw-bold mt-1 text-danger">₹0.00</h3>
                                        <small class="text-danger fw-medium">View Held Parcels &rarr;</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form for Clearing Payment -->
                    <form method="POST" action="{{ route('transport_bookings.payment_clearance') }}" id="clearanceForm">
                        @csrf
                        <input type="hidden" name="action" value="clear_payment">
                        <input type="hidden" name="selected_booking_ids" id="selected_booking_ids" value="">
                        <input type="hidden" name="driver_id" id="post_driver_id" value="{{ $selectedDriverId }}">
                        <input type="hidden" name="start_date" id="post_start_date" value="{{ $startDate->format('d-m-Y') }}">
                        <input type="hidden" name="end_date" id="post_end_date" value="{{ $endDate->format('d-m-Y') }}">

                        <!-- Nav Tabs -->
                        <div class="nav-align-top mb-4">
                            <ul class="nav nav-pills p-1 bg-lighter rounded-3 d-inline-flex border mb-3" role="tablist">
                                <li class="nav-item">
                                    <button type="button" class="nav-link active px-4 py-2" role="tab" data-bs-toggle="tab" data-bs-target="#tab-eligible" aria-controls="tab-eligible" aria-selected="true" id="tab_btn_eligible">
                                        <i class="ri ri-checkbox-circle-fill text-success me-1 fs-5 align-middle"></i>
                                        <span class="fw-semibold">1. Eligible Consignments (LR Verified)</span>
                                        <span class="badge rounded-pill bg-success ms-2" id="tab_badge_eligible">{{ $eligibleCount ?? $eligibleBookings->count() }}</span>
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link px-4 py-2" role="tab" data-bs-toggle="tab" data-bs-target="#tab-blocked" aria-controls="tab-blocked" aria-selected="false" id="tab_btn_blocked">
                                        <i class="ri ri-close-circle-fill text-danger me-1 fs-5 align-middle"></i>
                                        <span class="fw-semibold">2. Blocked Consignments (Missing LR)</span>
                                        <span class="badge rounded-pill bg-danger ms-2" id="tab_badge_blocked">{{ $blockedCount ?? $blockedBookings->count() }}</span>
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content border-0 p-0">
                                <!-- Tab 1: Eligible Consignments -->
                                <div class="tab-pane fade show active" id="tab-eligible" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h5 class="mb-1 text-success fw-bold">
                                                <i class="ri ri-checkbox-circle-fill me-1"></i> Verified Consignments (Eligible for Weekly Payout)
                                            </h5>
                                            <small class="text-muted">Parcels verified with authentic transport LR numbers. Check boxes to selectively approve payment.</small>
                                        </div>
                                        <button type="button" class="btn btn-success" id="btnApproveClearance" style="{{ ($eligibleCount ?? 0) > 0 ? '' : 'display: none;' }}">
                                            <i class="menu-icon icon-base ri ri-check-double-line"></i> <span id="clearanceBtnText">Approve & Clear Selected (₹{{ number_format($eligibleAmount, 2) }})</span>
                                        </button>
                                    </div>

                                    <div class="card-datatable table-responsive">
                                        <table class="table table-hover align-middle mb-0 w-100" id="eligibleTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 35px;" class="text-center">
                                                        <input type="checkbox" class="form-check-input" id="checkAllEligible" title="Select All" checked>
                                                    </th>
                                                    <th style="width: 3%;">#</th>
                                                    <th>Customer / Buyer</th>
                                                    <th>Invoice No</th>
                                                    <th>Box</th>
                                                    <th>Qty</th>
                                                    <th>Transport</th>
                                                    <th>Driver</th>
                                                    <th>LR Number</th>
                                                    <th>Booking Date</th>
                                                    <th class="text-end">Loading / Unloading (₹)</th>
                                                    <th>Payment Status</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Tab 2: Blocked Consignments -->
                                <div class="tab-pane fade" id="tab-blocked" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <h5 class="mb-1 text-danger fw-bold">
                                                <i class="ri ri-close-circle-fill me-1"></i> Blocked Consignments (Payment Strictly Held)
                                            </h5>
                                            <small class="text-muted">These parcels cannot be cleared for payment until the physical LR receipt number is entered in the LR Register.</small>
                                        </div>
                                        <div>
                                            <span class="badge bg-label-danger py-2 px-3 fw-bold">
                                                <i class="ri ri-lock-2-line me-1"></i> PAYMENT LOCKED
                                            </span>
                                        </div>
                                    </div>

                                    <div class="alert alert-danger py-2 d-flex align-items-center mb-3" role="alert">
                                        <i class="ri ri-error-warning-fill fs-5 me-2"></i>
                                        <div>
                                            <strong>STRICT AUDIT POLICY: NO LR NUMBER = NO PAYMENT CLEARANCE.</strong>
                                            Once the driver submits the LR receipt, update it in <a href="{{ route('transport_bookings.index') }}" class="alert-link fw-bold text-decoration-underline">LR Register</a> to move it to the Eligible tab.
                                        </div>
                                    </div>

                                    <div class="card-datatable table-responsive">
                                        <table class="table table-hover align-middle mb-0 w-100" id="blockedTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 3%;">#</th>
                                                    <th>Customer / Buyer</th>
                                                    <th>Invoice No</th>
                                                    <th>Box</th>
                                                    <th>Qty</th>
                                                    <th>Transport</th>
                                                    <th>Driver</th>
                                                    <th>Status</th>
                                                    <th>LR Status</th>
                                                    <th class="text-end">Held Payout (₹)</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function() {
    let defaultStartDate = "{{ Carbon\Carbon::now()->subDays(7)->format('d-m-Y') }}";
    let defaultEndDate = "{{ Carbon\Carbon::now()->format('d-m-Y') }}";

    if ($.fn.select2) {
        $('.select2').select2({
            allowClear: true,
            placeholder: function() {
                return $(this).data('placeholder') || 'Select';
            }
        });
    }

    if ($.fn.flatpickr) {
        $('.flatpickr-date').flatpickr({
            dateFormat: 'd-m-Y',
            allowInput: true
        });
    }

    // 1. Eligible DataTable (Server-side AJAX)
    let eligibleTable = $('#eligibleTable').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        ajax: {
            url: "{{ route('transport_bookings.payment_clearance') }}",
            type: "GET",
            data: function(d) {
                d.table_type = 'eligible';
                d.driver_id = $('#driver_id').val();
                d.payout_status = $('#payout_status').val();
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            },
            dataSrc: function(json) {
                if (json.metrics) {
                    $('#kpi_eligible_count').text(json.metrics.eligible_count + ' Boxes');
                    $('#kpi_eligible_amount').text('₹' + json.metrics.eligible_amount);
                    $('#tab_badge_eligible').text(json.metrics.eligible_count);
                    $('#clearanceBtnText').text('Approve & Clear Payment (₹' + json.metrics.eligible_amount + ')');
                    if (json.metrics.eligible_count > 0) {
                        $('#btnApproveClearance').show();
                    } else {
                        $('#btnApproveClearance').hide();
                    }
                }
                return json.data;
            }
        },
        columns: [
            { data: 'checkbox', orderable: false, searchable: false, className: 'text-center' },
            { data: 'sno', className: 'text-center' },
            { data: 'customer' },
            { data: 'invoice_no' },
            { data: 'box', className: 'text-center' },
            { data: 'qty', className: 'text-center' },
            { data: 'transport' },
            { data: 'driver', className: 'text-center' },
            { data: 'lr_number', className: 'text-center' },
            { data: 'booking_date', className: 'text-center' },
            { data: 'approved_price' },
            { data: 'payment_status', className: 'text-center' }
        ],
        drawCallback: function() {
            let total = $('.row-checkbox-eligible:not(:disabled)').length;
            let checked = $('.row-checkbox-eligible:checked').length;
            $('#checkAllEligible').prop('checked', total > 0 && total === checked);
            updateSelectedSummary();
        },
        language: {
            emptyTable: "No eligible bookings with confirmed LR numbers found for this period."
        }
    });

    // 2. Blocked DataTable (Server-side AJAX)
    let blockedTable = $('#blockedTable').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        ajax: {
            url: "{{ route('transport_bookings.payment_clearance') }}",
            type: "GET",
            data: function(d) {
                d.table_type = 'blocked';
                d.driver_id = $('#driver_id').val();
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            },
            dataSrc: function(json) {
                if (json.metrics) {
                    $('#kpi_blocked_count').text(json.metrics.blocked_count + ' Boxes');
                    $('#tab_badge_blocked').text(json.metrics.blocked_count);
                }
                return json.data;
            }
        },
        columns: [
            { data: 'sno', className: 'text-center' },
            { data: 'customer' },
            { data: 'invoice_no' },
            { data: 'box', className: 'text-center' },
            { data: 'qty', className: 'text-center' },
            { data: 'transport' },
            { data: 'driver', className: 'text-center' },
            { data: 'status', className: 'text-center' },
            { data: 'lr_status', className: 'text-center' },
            { data: 'claim_amount' }
        ],
        language: {
            emptyTable: "Great! No blocked boxes. All dispatched boxes have confirmed LR receipts."
        }
    });

    // Tab switching: adjust column widths automatically
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust().responsive.recalc();
    });

    // KPI Cards: click to navigate between tabs
    $('#kpi_card_eligible').on('click', function() {
        let triggerEl = document.querySelector('#tab_btn_eligible');
        if (triggerEl) {
            bootstrap.Tab.getOrCreateInstance(triggerEl).show();
        }
    });

    $('#kpi_card_blocked').on('click', function() {
        let triggerEl = document.querySelector('#tab_btn_blocked');
        if (triggerEl) {
            bootstrap.Tab.getOrCreateInstance(triggerEl).show();
        }
    });

    // AJAX Filter Trigger
    function reloadTables() {
        // Sync hidden post values for clearance form
        $('#post_driver_id').val($('#driver_id').val());
        $('#post_start_date').val($('#start_date').val());
        $('#post_end_date').val($('#end_date').val());

        eligibleTable.ajax.reload();
        blockedTable.ajax.reload();
        updatePrintClearanceUrl();
    }

    $('#btnFilterBatches').on('click', function(e) {
        e.preventDefault();
        reloadTables();
    });

    // Reset Filter Button
    $('#btnResetFilter').on('click', function(e) {
        e.preventDefault();
        $('#driver_id').val('').trigger('change');
        $('#payout_status').val('').trigger('change');
        $('#start_date').val(defaultStartDate);
        $('#end_date').val(defaultEndDate);
        reloadTables();
    });

    // 3. Dynamic Selection & Payout Summary
    function updateSelectedSummary() {
        let selectedBoxes = 0;
        let selectedAmount = 0;
        let selectedIds = [];

        $('.row-checkbox-eligible:checked').each(function() {
            selectedBoxes++;
            selectedAmount += parseFloat($(this).data('price')) || 0;
            selectedIds.push($(this).val());
        });

        $('#selected_booking_ids').val(selectedIds.join(','));

        let formattedAmount = selectedAmount.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        if (selectedBoxes > 0) {
            $('#clearanceBtnText').text('Approve & Clear Selected (' + selectedBoxes + ' Boxes - ₹' + formattedAmount + ')');
            $('#btnApproveClearance').prop('disabled', false).removeClass('opacity-50').show();
        } else {
            $('#clearanceBtnText').text('Select Invoices to Clear Payment');
            $('#btnApproveClearance').prop('disabled', true).addClass('opacity-50');
        }

        updatePrintClearanceUrl();
    }

    function updatePrintClearanceUrl() {
        let baseUrl = "{{ route('transport_bookings.print_clearance_pdf') }}";
        let params = new URLSearchParams();

        let driver = $('#driver_id').val();
        let payoutStatus = $('#payout_status').val() || 'cleared';
        let startDate = $('#start_date').val();
        let endDate = $('#end_date').val();

        if (driver) params.append('driver_id', driver);
        if (payoutStatus) params.append('payment_status', payoutStatus);
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);

        let finalUrl = baseUrl + (params.toString() ? ('?' + params.toString()) : '');
        $('#btnPrintClearanceSheet').attr('href', finalUrl);
    }

    // Call on initial load
    updatePrintClearanceUrl();

    // Select All Checkbox Handler
    $(document).on('change', '#checkAllEligible', function() {
        let isChecked = $(this).is(':checked');
        $('.row-checkbox-eligible:not(:disabled)').prop('checked', isChecked);
        updateSelectedSummary();
    });

    // Row Checkbox Handler
    $(document).on('change', '.row-checkbox-eligible', function() {
        let total = $('.row-checkbox-eligible:not(:disabled)').length;
        let checked = $('.row-checkbox-eligible:checked').length;
        $('#checkAllEligible').prop('checked', total > 0 && total === checked);
        updateSelectedSummary();
    });

    // Approve Clearance
    $('#btnApproveClearance').on('click', function(e) {
        e.preventDefault();
        let checkedCount = $('.row-checkbox-eligible:checked').length;
        if (checkedCount === 0) {
            Swal.fire({
                title: 'No Invoices Selected',
                text: 'Please select at least one invoice using the checkboxes to clear payment.',
                icon: 'warning',
                confirmButtonColor: '#ff9f43'
            });
            return;
        }

        let amountText = $('#clearanceBtnText').text();

        Swal.fire({
            title: 'Confirm Payment Clearance',
            text: "Confirm clearing payout for: " + amountText + "?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28c76f',
            cancelButtonColor: '#82868b',
            confirmButtonText: 'Yes, Approve Payout'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#clearanceForm').submit();
            }
        });
    });
});
</script>
@endsection

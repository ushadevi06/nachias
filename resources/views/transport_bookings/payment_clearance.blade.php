@extends('layouts.common')
@section('title', 'Driver Weekly Payment Clearance - ' . env('WEBSITE_NAME'))

@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>Driver Weekly Payment Clearance</h4>
                <div class="d-flex gap-2">
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
                                <div class="col-md-4">
                                    <select name="driver_id" id="driver_id" class="form-select select2" data-placeholder="All Drivers">
                                        <option value="">All Drivers</option>
                                        @foreach($drivers as $d)
                                            <option value="{{ $d->id }}" {{ $selectedDriverId == $d->id ? 'selected' : '' }}>
                                                {{ $d->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="start_date" id="start_date" class="form-control flatpickr-date" value="{{ $startDate->format('d-m-Y') }}" placeholder="From Date">
                                </div>
                                <div class="col-md-3">
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

                    <!-- KPI Metric Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="card border shadow-none bg-label-success mb-0">
                                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="small text-uppercase fw-semibold text-muted">Eligible Boxes for Payout</span>
                                        <h3 class="mb-0 fw-bold mt-1 text-success" id="kpi_eligible_count">{{ $eligibleCount ?? $eligibleBookings->count() }} Boxes</h3>
                                        <small class="text-muted">Verified with confirmed LR numbers</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="small text-uppercase fw-semibold text-muted">Total Approved Payout</span>
                                        <h3 class="mb-0 fw-bold mt-1 text-success" id="kpi_eligible_amount">₹{{ number_format($eligibleAmount, 2) }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border shadow-none bg-label-danger mb-0">
                                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="small text-uppercase fw-semibold text-muted">Blocked / Pending LR Boxes</span>
                                        <h3 class="mb-0 fw-bold mt-1 text-danger" id="kpi_blocked_count">{{ $blockedCount ?? $blockedBookings->count() }} Boxes</h3>
                                        <small class="text-muted">Payment strictly locked (No LR receipt)</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="small text-uppercase fw-semibold text-muted">Held Amount</span>
                                        <h3 class="mb-0 fw-bold mt-1 text-danger">₹0.00</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form for Clearing Payment -->
                    <form method="POST" action="{{ route('transport_bookings.payment_clearance') }}" id="clearanceForm">
                        @csrf
                        <input type="hidden" name="action" value="clear_payment">
                        <input type="hidden" name="driver_id" id="post_driver_id" value="{{ $selectedDriverId }}">
                        <input type="hidden" name="start_date" id="post_start_date" value="{{ $startDate->format('d-m-Y') }}">
                        <input type="hidden" name="end_date" id="post_end_date" value="{{ $endDate->format('d-m-Y') }}">

                        <!-- Section 1: Eligible Table -->
                        <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                            <h5 class="mb-0 text-success fw-bold">
                                <i class="menu-icon icon-base ri ri-checkbox-circle-fill me-1"></i> 1. Eligible Consignments (LR Verified)
                            </h5>
                            <button type="button" class="btn btn-success" id="btnApproveClearance" style="{{ ($eligibleCount ?? 0) > 0 ? '' : 'display: none;' }}">
                                <i class="menu-icon icon-base ri ri-check-double-line"></i> <span id="clearanceBtnText">Approve & Clear Payment (₹{{ number_format($eligibleAmount, 2) }})</span>
                            </button>
                        </div>

                        <div class="card-datatable table-responsive mb-4">
                            <table class="table table-hover align-middle mb-0 w-100" id="eligibleTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 3%;">#</th>
                                        <th>Customer / Buyer</th>
                                        <th>Invoice No</th>
                                        <th>Box</th>
                                        <th>Qty</th>
                                        <th>Transport</th>
                                        <th>Driver</th>
                                        <th>LR Number</th>
                                        <th>Booking Date</th>
                                        <th class="text-end">Approved Price</th>
                                        <th>Payment Status</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <!-- Section 2: Blocked Table -->
                        <div class="mb-3 mt-4">
                            <h5 class="mb-0 text-danger fw-bold">
                                <i class="menu-icon icon-base ri ri-close-circle-fill me-1"></i> 2. Blocked Consignments (Missing LR - Payment Held)
                            </h5>
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
                                        <th class="text-end">Claim Amount</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
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
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            },
            dataSrc: function(json) {
                if (json.metrics) {
                    $('#kpi_eligible_count').text(json.metrics.eligible_count + ' Boxes');
                    $('#kpi_eligible_amount').text('₹' + json.metrics.eligible_amount);
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

    // AJAX Filter Trigger
    function reloadTables() {
        // Sync hidden post values for clearance form
        $('#post_driver_id').val($('#driver_id').val());
        $('#post_start_date').val($('#start_date').val());
        $('#post_end_date').val($('#end_date').val());

        eligibleTable.ajax.reload();
        blockedTable.ajax.reload();
    }

    $('#btnFilterBatches').on('click', function(e) {
        e.preventDefault();
        reloadTables();
    });

    // Reset Filter Button
    $('#btnResetFilter').on('click', function(e) {
        e.preventDefault();
        $('#driver_id').val('').trigger('change');
        $('#start_date').val(defaultStartDate);
        $('#end_date').val(defaultEndDate);
        reloadTables();
    });

    // Approve Clearance
    $('#btnApproveClearance').on('click', function(e) {
        e.preventDefault();
        let amountText = $('#kpi_eligible_amount').text();
        let countText = $('#kpi_eligible_count').text();

        Swal.fire({
            title: 'Confirm Payment Clearance',
            text: "Confirm clearing weekly payout of " + amountText + " for " + countText + "?",
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

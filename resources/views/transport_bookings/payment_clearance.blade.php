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
                        <form method="GET" action="{{ route('transport_bookings.payment_clearance') }}">
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
                                    <input type="text" name="start_date" class="form-control flatpickr-date" value="{{ $startDate->format('d-m-Y') }}" placeholder="From Date">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="end_date" class="form-control flatpickr-date" value="{{ $endDate->format('d-m-Y') }}" placeholder="To Date">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="menu-icon icon-base ri ri-filter-line"></i> Load Batches
                                    </button>
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
                                        <h3 class="mb-0 fw-bold mt-1 text-success">{{ $eligibleBookings->count() }} Boxes</h3>
                                        <small class="text-muted">Verified with confirmed LR numbers</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="small text-uppercase fw-semibold text-muted">Total Approved Payout</span>
                                        <h3 class="mb-0 fw-bold mt-1 text-success">₹{{ number_format($eligibleAmount, 2) }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border shadow-none bg-label-danger mb-0">
                                <div class="card-body py-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="small text-uppercase fw-semibold text-muted">Blocked / Pending LR Boxes</span>
                                        <h3 class="mb-0 fw-bold mt-1 text-danger">{{ $blockedBookings->count() }} Boxes</h3>
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
                        <input type="hidden" name="driver_id" value="{{ $selectedDriverId }}">
                        <input type="hidden" name="start_date" value="{{ $startDate->format('d-m-Y') }}">
                        <input type="hidden" name="end_date" value="{{ $endDate->format('d-m-Y') }}">

                        <!-- Section 1: Eligible Table -->
                        <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                            <h5 class="mb-0 text-success fw-bold">
                                <i class="menu-icon icon-base ri ri-checkbox-circle-fill me-1"></i> 1. Eligible Consignments (LR Verified)
                            </h5>
                            @if($eligibleBookings->count() > 0)
                                <button type="button" class="btn btn-success" id="btnApproveClearance">
                                    <i class="menu-icon icon-base ri ri-check-double-line"></i> Approve & Clear Payment (₹{{ number_format($eligibleAmount, 2) }})
                                </button>
                            @endif
                        </div>

                        <div class="card-datatable table-responsive mb-4">
                            <table class="table table-hover align-middle mb-0">
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
                                <tbody>
                                    @forelse($eligibleBookings as $idx => $b)
                                        <tr>
                                            <td>{{ $idx + 1 }}</td>
                                            <td>
                                                <strong class="d-block text-dark">{{ $b->customer->name ?? '-' }}</strong>
                                                <small class="text-muted">{{ $b->customer->code ?? '' }}</small>
                                            </td>
                                            <td>
                                                <a href="{{ url('sales_invoices/show/' . $b->sales_invoice_id) }}" class="fw-semibold text-primary">
                                                    {{ $b->salesInvoice->inv_no ?? '-' }}
                                                </a>
                                            </td>
                                            <td><span class="badge bg-dark fw-bold">{{ $b->box_number }}</span></td>
                                            <td>{{ (int)$b->quantity }}</td>
                                            <td>{{ $b->preferred_transport ?: '-' }}</td>
                                            <td><span class="badge bg-label-primary">{{ $b->deliveryAgent->name ?? ($b->delivery_agent_name ?: '-') }}</span></td>
                                            <td><span class="badge bg-label-success fw-bold">{{ $b->lr_no }}</span></td>
                                            <td>{{ $b->booking_date ? $b->booking_date->format('d-m-Y') : '-' }}</td>
                                            <td class="text-end fw-bold text-success">₹{{ number_format($b->lr_price, 2) }}</td>
                                            <td>
                                                @if($b->payment_status === 'Cleared')
                                                    <span class="badge bg-success">Cleared on {{ $b->payment_cleared_date ? $b->payment_cleared_date->format('d-m-Y') : '' }}</span>
                                                @else
                                                    <span class="badge bg-label-warning">Eligible (Pending Clearance)</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center py-4 text-muted">
                                                No eligible bookings with confirmed LR numbers found for this period.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Section 2: Blocked Table -->
                        <div class="mb-3 mt-4">
                            <h5 class="mb-0 text-danger fw-bold">
                                <i class="menu-icon icon-base ri ri-close-circle-fill me-1"></i> 2. Blocked Consignments (Missing LR - Payment Held)
                            </h5>
                        </div>

                        <div class="card-datatable table-responsive">
                            <table class="table table-hover align-middle mb-0">
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
                                <tbody>
                                    @forelse($blockedBookings as $idx => $b)
                                        <tr class="table-light text-muted">
                                            <td>{{ $idx + 1 }}</td>
                                            <td>{{ $b->customer->name ?? '-' }}</td>
                                            <td>{{ $b->salesInvoice->inv_no ?? '-' }}</td>
                                            <td><span class="badge bg-secondary">{{ $b->box_number }}</span></td>
                                            <td>{{ (int)$b->quantity }}</td>
                                            <td>{{ $b->preferred_transport ?: '-' }}</td>
                                            <td><span class="badge bg-label-secondary">{{ $b->deliveryAgent->name ?? ($b->delivery_agent_name ?: '-') }}</span></td>
                                            <td><span class="badge bg-label-secondary">{{ $b->status }}</span></td>
                                            <td>
                                                <span class="badge bg-danger">NO LR NUMBER</span>
                                            </td>
                                            <td class="text-end fw-bold text-danger">₹0.00 (LOCKED)</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-3 text-muted">
                                                Great! No blocked boxes. All dispatched boxes have confirmed LR receipts.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
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

    $('#btnApproveClearance').on('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Confirm Payment Clearance',
            text: "Confirm clearing weekly payout of ₹{{ number_format($eligibleAmount, 2) }} for {{ $eligibleBookings->count() }} boxes?",
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

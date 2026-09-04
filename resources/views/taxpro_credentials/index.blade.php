@extends('layouts.common')
@section('title', 'TaxPro Credentials - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-1 text-primary d-flex align-items-center gap-2">
                <i class="ri ri-shield-keyhole-line"></i> TaxPro API Credentials & Balance
            </h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if($credentials['is_sandbox'])
                <span class="badge bg-warning text-dark px-3 py-2 fs-6">
                    <i class="ri ri-test-tube-line me-1"></i> Sandbox Mode
                </span>
            @else
                <span class="badge bg-success px-3 py-2 fs-6">
                    <i class="ri ri-checkbox-circle-line me-1"></i> Production Mode
                </span>
            @endif
            <button type="button" id="btnRefreshBalance" class="btn btn-primary d-flex align-items-center gap-1">
                <i class="ri ri-refresh-line" id="refreshIcon"></i>
                <span id="refreshBtnText">Check Live Balance</span>
            </button>
        </div>
    </div>

    <!-- Balance & Helpline Summary Cards -->
    <div class="row g-4 mb-4">
        <!-- API Balance -->
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #f0f7ff 0%, #e6f0fa 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge bg-primary-subtle text-primary mb-2 fw-semibold">E-Way & E-Invoice</span>
                            <h6 class="card-title text-muted mb-1">API Balance (EWBApiBal)</h6>
                            <h2 class="mb-0 fw-bold text-primary" id="ewbApiBalDisplay">--</h2>
                        </div>
                        <div class="avatar avatar-md bg-primary text-white rounded p-2 d-flex align-items-center justify-content-center">
                            <i class="ri ri-coins-line fs-3"></i>
                        </div>
                    </div>
                    <div class="mt-3 small text-muted" id="balanceStatusText">
                        <span class="spinner-border spinner-border-sm text-primary"></span> Fetching live balance...
                    </div>
                </div>
            </div>
        </div>

        <!-- Expiry Date -->
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #fbf7f0 0%, #f6efe1 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge bg-warning-subtle text-warning-emphasis mb-2 fw-semibold">Subscription</span>
                            <h6 class="card-title text-muted mb-1">Balance Expiry (EWBApiBalExpDt)</h6>
                            <h3 class="mb-0 fw-bold text-dark" id="ewbApiBalExpDtDisplay">--</h3>
                        </div>
                        <div class="avatar avatar-md bg-warning text-white rounded p-2 d-flex align-items-center justify-content-center">
                            <i class="ri ri-calendar-event-line fs-3"></i>
                        </div>
                    </div>
                    <div class="mt-3 small text-muted" id="expiryStatusText">
                        <i class="ri ri-time-line"></i> Valid until subscription end
                    </div>
                </div>
            </div>
        </div>

        <!-- Gateway / Environment Status -->
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #f3f0ff 0%, #ebe5fc 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge bg-purple-subtle text-purple mb-2 fw-semibold" style="background-color: #ede9fe; color: #6d28d9;">GSP Gateway</span>
                            <h6 class="card-title text-muted mb-1">Environment & Mode</h6>
                            <h3 class="mb-0 fw-bold {{ $credentials['is_sandbox'] ? 'text-warning' : 'text-success' }}">
                                {{ $credentials['is_sandbox'] ? 'Sandbox' : 'Production' }}
                            </h3>
                        </div>
                        <div class="avatar avatar-md rounded p-2 d-flex align-items-center justify-content-center" style="background-color: #7c3aed; color: #ffffff;">
                            <i class="ri ri-shield-check-line fs-3"></i>
                        </div>
                    </div>
                    <div class="mt-3 small text-muted d-flex align-items-center gap-1">
                        <i class="ri ri-building-line text-secondary"></i>
                        <span>GSTIN: <strong>{{ $credentials['gstin'] }}</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($isAdmin)
    <!-- Main Important Details (Admin Only) -->
    <div class="row g-4">
        <!-- API Credentials Card -->
        <div class="col-lg-7">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom pb-3">
                    <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                        <i class="ri ri-key-2-line text-primary"></i> API Credentials
                    </h5>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-borderless table-striped align-middle mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted fw-semibold" style="width: 35%;">ASP ID</td>
                                    <td>
                                        <code class="fs-6 fw-bold text-primary">{{ $credentials['asp_id'] }}</code>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">ASP Password</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="password" readonly class="form-control form-control-sm bg-transparent border-0 p-0 fw-bold" style="width: 140px;" id="asp_pwd_field" value="{{ $credentials['asp_password'] }}">
                                            <button type="button" class="btn btn-sm btn-outline-secondary p-1 border-0" onclick="togglePassword('asp_pwd_field', this)">
                                                <i class="ri ri-eye-line"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Company GSTIN</td>
                                    <td>
                                        <span class="badge bg-dark fs-6">{{ $credentials['gstin'] }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">GSP Username</td>
                                    <td class="fw-bold">{{ $credentials['username'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">GSP Password</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="password" readonly class="form-control form-control-sm bg-transparent border-0 p-0 fw-bold" style="width: 140px;" id="gsp_pwd_field" value="{{ $credentials['password'] }}">
                                            <button type="button" class="btn btn-sm btn-outline-secondary p-1 border-0" onclick="togglePassword('gsp_pwd_field', this)">
                                                <i class="ri ri-eye-line"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted fw-semibold">Environment</td>
                                    <td>
                                        <span class="badge {{ $credentials['is_sandbox'] ? 'bg-warning text-dark' : 'bg-success' }}">
                                            {{ $credentials['environment'] }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Support & Recharge Info -->
        <div class="col-lg-5">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom pb-3">
                    <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                        <i class="ri ri-customer-service-2-line text-primary"></i> TaxPro Support
                    </h5>
                </div>
                <div class="card-body pt-3 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Central Helpline -->
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded mb-3">
                            <div class="avatar avatar-sm bg-primary text-white rounded p-2 d-flex align-items-center justify-content-center">
                                <i class="ri ri-phone-line fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Central Helpdesk (100 Lines)</div>
                                <a href="tel:07126638888" class="fw-bold fs-5 text-dark text-decoration-none">
                                    0712-663 8888
                                </a>
                            </div>
                        </div>

                        <!-- Important Emails -->
                        <ul class="list-group list-group-flush mb-3 small">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 bg-transparent">
                                <span class="text-muted fw-semibold">Technical Support:</span>
                                <a href="mailto:{{ $helpline['emails']['support'] }}" class="fw-bold text-primary">{{ $helpline['emails']['support'] }}</a>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 bg-transparent">
                                <span class="text-muted fw-semibold">Recharge / Sales:</span>
                                <a href="mailto:{{ $helpline['emails']['sales'] }}" class="fw-bold text-primary">{{ $helpline['emails']['sales'] }}</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Direct Portal Link -->
                    <div class="pt-3 border-top text-end">
                        <a href="{{ $helpline['websites']['portal'] }}" target="_blank" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                            <i class="ri ri-external-link-line"></i> TaxPro Official Portal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'ri ri-eye-off-line';
        } else {
            input.type = 'password';
            icon.className = 'ri ri-eye-line';
        }
    }

    function formatExpiryDate(rawDate) {
        if (!rawDate) return 'N/A';
        try {
            const d = new Date(rawDate);
            if (isNaN(d.getTime())) return rawDate;
            const options = { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true };
            return d.toLocaleDateString('en-GB', options);
        } catch (e) {
            return rawDate;
        }
    }

    $(document).ready(function() {
        function fetchApiBalance() {
            const btn = $('#btnRefreshBalance');
            const icon = $('#refreshIcon');
            const btnText = $('#refreshBtnText');

            btn.prop('disabled', true);
            icon.addClass('ri-spin');
            btnText.text('Fetching...');

            $('#balanceStatusText').html('<span class="spinner-border spinner-border-sm text-primary"></span> Fetching live balance...');

            $.ajax({
                url: "{{ route('taxpro.check_balance') }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    btn.prop('disabled', false);
                    icon.removeClass('ri-spin');
                    btnText.text('Check Live Balance');

                    if (response.success) {
                        const bal = response.ewb_api_bal !== null && response.ewb_api_bal !== undefined ? response.ewb_api_bal : '0';
                        const rawExpDt = response.ewb_api_bal_exp_dt || '';
                        const formattedExpDt = formatExpiryDate(rawExpDt);

                        $('#ewbApiBalDisplay').text(Number(bal).toLocaleString());
                        $('#ewbApiBalExpDtDisplay').text(formattedExpDt);

                        const numBal = parseInt(bal) || 0;
                        if (numBal <= 0) {
                            $('#balanceStatusText').html('<span class="text-danger fw-bold"><i class="ri ri-error-warning-fill"></i> Balance Exhausted! Please recharge.</span>');
                        } else if (numBal < 100) {
                            $('#balanceStatusText').html('<span class="text-warning fw-bold"><i class="ri ri-alert-fill"></i> Low Balance: ' + numBal + ' credits remaining</span>');
                        } else {
                            $('#balanceStatusText').html('<span class="text-success fw-bold"><i class="ri ri-checkbox-circle-fill"></i> Active & Healthy (' + numBal + ' credits)</span>');
                        }

                        $('#expiryStatusText').html('<span class="text-success fw-semibold"><i class="ri ri-check-line"></i> Valid until ' + formattedExpDt + '</span>');
                    } else {
                        $('#ewbApiBalDisplay').text('Error');
                        $('#ewbApiBalExpDtDisplay').text('--');
                        $('#balanceStatusText').html('<span class="text-danger fw-bold"><i class="ri ri-close-circle-fill"></i> ' + (response.message || 'Failed to fetch balance') + '</span>');
                    }
                },
                error: function(xhr, status, error) {
                    btn.prop('disabled', false);
                    icon.removeClass('ri-spin');
                    btnText.text('Check Live Balance');
                    $('#ewbApiBalDisplay').text('Error');
                    $('#balanceStatusText').html('<span class="text-danger fw-bold"><i class="ri ri-close-circle-fill"></i> Request failed</span>');
                }
            });
        }

        $('#btnRefreshBalance').on('click', function() {
            fetchApiBalance();
        });

        fetchApiBalance();
    });
</script>
@endsection

@extends('layouts.common')
@section('title', 'FG Stock Taking - ' . env('WEBSITE_NAME'))

@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <!-- Header matching Stock Entry / Sales Invoice Scan style -->
            <div class="table-header-box mb-3">
                <h4>FG Stock Taking</h4>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ url('stock_entries') }}" class="btn btn-secondary text-white">
                        <i class="ri ri-arrow-left-line back-arrow"></i> Back to Stock Entry
                    </a>
                    <a href="{{ url('fg_stock_taking/logs') }}" class="btn btn-outline-primary" id="stock_taking_logs_btn">
                        <i class="ri ri-history-line me-1"></i> Stock Taking Logs
                        @if(($logsCount ?? 0) > 0)
                            <span class="badge bg-primary text-white ms-1" id="history_count_badge">{{ number_format($logsCount) }}</span>
                        @endif
                    </a>
                    <button type="button" class="btn btn-outline-primary d-none" id="start_new_btn">
                        <i class="ri ri-add-circle-line me-1"></i> Start New Count
                    </button>
                    <button type="button" class="btn btn-primary" id="finalize_btn" disabled>
                        <i class="ri ri-lock-line me-1"></i> Finalize Stock Taking
                    </button>
                    <a href="javascript:void(0)" class="btn btn-success disabled" id="export_excel_btn" style="pointer-events: none; opacity: 0.5;">
                        <i class="ri ri-file-excel-line me-1"></i> Export Excel
                    </a>
                </div>
            </div>

            <!-- Main Card matching Sales Invoice Scan UI -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-3 mb-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-bold mb-1">Brand <span class="text-danger">*</span></label>
                            <select id="filter_brand_id" class="form-select select2" data-placeholder="Select Brand">
                                <option value="">-- Select Brand --</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ ($selectedBrandId ?? null) == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->brand_name }} ({{ number_format($brand->fg_sku_count) }} SKUs | {{ number_format($brand->fg_total_qty, 0) }} pcs)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold mb-1">Store</label>
                            <select id="filter_store_type_id" class="form-select select2" data-placeholder="All Stores">
                                <option value="">All Stores</option>
                                @foreach($storeTypes as $st)
                                    <option value="{{ $st->id }}" {{ ($selectedStoreTypeId ?? null) == $st->id ? 'selected' : '' }}>
                                        {{ $st->store_type_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold mb-1">Warehouse</label>
                            <select id="filter_warehouse_id" class="form-select select2" data-placeholder="All Warehouses">
                                <option value="">All Warehouses</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" {{ ($selectedWarehouseId ?? null) == $wh->id ? 'selected' : '' }}>
                                        {{ $wh->warehouse_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold mb-1">Store Location</label>
                            <select id="filter_store_location_id" class="form-select select2" data-placeholder="Select Store Location">
                                <option value="">-- Select Store Location --</option>
                                @foreach($storeLocations as $loc)
                                    <option value="{{ $loc->id }}" {{ ($selectedStoreLocationId ?? null) == $loc->id ? 'selected' : '' }}>
                                        {{ $loc->store_location }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Stock Taking Details Strip -->
                    <div id="session_details_bar" class="bg-light border rounded px-3 py-2 mb-4 d-none">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 small">
                            <div class="d-flex flex-wrap gap-3 align-items-center">
                                <span><strong class="text-muted">Ref No:</strong> <span id="detail_session_no" class="fw-bold text-primary">-</span></span>
                                <span><strong class="text-muted">Store / Warehouse:</strong> <span id="detail_scope" class="fw-semibold">-</span></span>
                                <span><strong class="text-muted">Stock Taking Date:</strong> <span id="detail_date" class="fw-semibold">-</span></span>
                                <span><strong class="text-muted">Counted By:</strong> <span id="detail_counted_by" class="fw-semibold">-</span></span>
                                <span id="detail_finalized_by_wrap" class="d-none"><strong class="text-muted">Finalized By:</strong> <span id="detail_finalized_by" class="fw-semibold">-</span></span>
                            </div>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <span id="detail_resumed_badge" class="badge bg-info text-dark d-none">Resumed Unfinished Session</span>
                                <span id="detail_movement_badge" class="badge bg-secondary d-none"></span>
                                <span id="detail_status_badge" class="badge bg-primary">In Progress</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-lg-2 col-md-4 col-6">
                            <div class="stat-card stat-card-filter" data-filter="all" title="Click to show all items">
                                <h6>System Stock</h6>
                                <h3 id="stat_system_qty">0</h3>
                                <div class="small text-muted mt-1" id="stat_total_skus">0 SKUs</div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <div class="stat-card border-success">
                                <h6 class="text-success">Scanned</h6>
                                <h3 id="stat_scanned" class="text-success">0</h3>
                                <div class="small text-muted mt-1" id="stat_touched_skus">0 SKUs</div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <div class="stat-card stat-card-filter" data-filter="pending" title="Click to filter pending items">
                                <h6>Remaining</h6>
                                <h3 id="stat_remaining">0</h3>
                                <div class="small text-muted mt-1">Pending Pcs</div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <div class="stat-card stat-card-filter" data-filter="completed" title="Click to filter completed SKUs">
                                <h6>Matched SKUs</h6>
                                <h3 id="stat_completed">0</h3>
                                <div class="small text-muted mt-1">Exact Match</div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <div class="stat-card border-danger stat-card-filter" data-filter="shortage" title="Click to filter Shortage items" style="background: #fff5f5;">
                                <h6 class="text-danger">Shortage</h6>
                                <h3 id="stat_shortage_qty" class="text-danger">0</h3>
                                <div class="small text-danger fw-semibold mt-1" id="stat_shortage_skus">0 SKUs</div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6">
                            <div class="stat-card border-warning stat-card-filter" data-filter="excess" title="Click to filter Excess items" style="background: #fffdf2;">
                                <h6 style="color: #b45309;">Excess</h6>
                                <h3 id="stat_excess_qty" style="color: #b45309;">0</h3>
                                <div class="small fw-semibold mt-1" id="stat_excess_skus" style="color: #b45309;">0 SKUs</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Scan Progress</span>
                            <span id="progress_text" class="fw-bold text-success">0%</span>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div id="progress_bar" class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <hr>

                    <div class="col-md-12 text-center">
                        <label class="form-label fw-bold">Scan Barcode</label>
                        <div id="barcode_search_wrap" class="position-relative mx-auto mb-2" style="max-width: 520px;">
                            <div class="input-group">
                                <input type="text" id="barcode_input" class="form-control border-primary" placeholder="Scan barcode or search SKU / Art No..." autofocus autocomplete="off">
                                <button class="btn btn-outline-primary" type="button" id="btn_camera_scan">
                                    <i class="ri ri-camera-line me-1"></i> Camera
                                </button>
                            </div>
                            <div id="barcode_suggestions" class="list-group shadow text-start d-none"></div>
                        </div>
                        <div id="finalized_notice" class="small text-danger fw-semibold mb-2 d-none">
                            <i class="ri ri-lock-fill me-1"></i> This stock taking session is finalized. Barcode scanning is locked.
                        </div>
                        <div id="reader" class="rounded overflow-hidden mb-3 mx-auto" style="display: none; width: 100%; max-width: 500px; border: 1px solid #00bcd4;"></div>

                        <div id="scan_alert" class="alert alert-danger mt-3 mx-auto" style="display: none; max-width: 560px;">
                            <span id="scan_msg"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table Card matching Sales Invoice Scan UI -->
            <div class="card">
                <div class="card-header bg-white fw-bold d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <span>Items Table</span>
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <select id="table_per_page" class="form-select form-select-sm" style="width: auto;">
                            <option value="10" selected>10 / page</option>
                            <option value="25">25 / page</option>
                            <option value="50">50 / page</option>
                            <option value="100">100 / page</option>
                            <option value="250">250 / page</option>
                        </select>
                        <select id="table_filter_select" class="form-select form-select-sm" style="width: auto;">
                            <option value="all">All Items</option>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="shortage">Shortage</option>
                            <option value="excess">Excess</option>
                        </select>
                        <input type="text" id="table_search_input" class="form-control form-control-sm" style="width: 220px;" placeholder="Search SKU / Art No / Size...">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0" id="items_table">
                            <thead class="table-light" id="table_head">
                                <tr>
                                    <th>Item Name</th>
                                    <th>Art No</th>
                                    <th>SKU / Barcode</th>
                                    <th>System Qty</th>
                                    <th>Scanned</th>
                                    <th>Remaining</th>
                                    <th>Variance</th>
                                    <th>Status</th>
                                    <th>Store Location</th>
                                </tr>
                            </thead>
                            <tbody id="table_body">
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        Please select a <strong>Brand</strong> above to load items and start scanning.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2 py-2 d-none" id="pagination_bar">
                    <div class="small text-muted" id="pagination_info"></div>
                    <nav aria-label="Items pagination">
                        <ul class="pagination pagination-sm mb-0" id="pagination_links"></ul>
                    </nav>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .stat-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        background: #f8f9fa;
    }
    .stat-card h6 {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 6px;
    }
    .stat-card h3 {
        margin: 0;
        font-weight: bold;
    }
    .stat-card-filter {
        cursor: pointer;
        transition: transform 0.12s ease, box-shadow 0.12s ease;
    }
    .stat-card-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    #barcode_suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1055;
        max-height: 290px;
        overflow-y: auto;
        background: #fff;
        border: 1px solid #d9dee3;
        border-top: none;
        border-radius: 0 0 0.375rem 0.375rem;
    }
    .barcode-suggestion-item {
        cursor: pointer;
        padding: 0.55rem 0.85rem;
        border-left: 3px solid transparent;
    }
    .barcode-suggestion-item:hover,
    .barcode-suggestion-item.active {
        background-color: #f3f0ff;
        color: inherit;
        border-left-color: #696cff;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
$(document).ready(function() {
    const csrfToken = '{{ csrf_token() }}';
    const baseUrl = '{{ url('fg_stock_taking') }}';
    let currentSessionId = null;
    let currentSessionFinalized = false;
    let currentPage = 1;
    let searchTimer = null;
    let activeBrandXhr = null;
    let activeTableXhr = null;
    let suggestTimer = null;
    let activeSuggestXhr = null;
    let suggestionItems = [];
    let highlightedSuggestIdx = -1;

    function hideBarcodeSuggestions() {
        clearTimeout(suggestTimer);
        if (activeSuggestXhr) {
            activeSuggestXhr.abort();
            activeSuggestXhr = null;
        }
        suggestionItems = [];
        highlightedSuggestIdx = -1;
        $('#barcode_suggestions').addClass('d-none').empty();
    }

    function updateSuggestionHighlight() {
        const $items = $('#barcode_suggestions .barcode-suggestion-item');
        $items.removeClass('active');
        if (highlightedSuggestIdx >= 0 && highlightedSuggestIdx < $items.length) {
            const $active = $items.eq(highlightedSuggestIdx).addClass('active');
            $active[0].scrollIntoView({ block: 'nearest' });
        }
    }

    function renderBarcodeSuggestions(items) {
        const $box = $('#barcode_suggestions');
        suggestionItems = Array.isArray(items) ? items : [];
        highlightedSuggestIdx = -1;

        if (!suggestionItems.length) {
            $box.html('<div class="list-group-item text-muted small py-2 text-center">No matching SKU or Art No found.</div>').removeClass('d-none');
            return;
        }

        let html = '';
        suggestionItems.forEach((it, idx) => {
            const sleeveText = it.sleeve_type && it.sleeve_type !== '-' ? ` (${escapeHtml(it.sleeve_type)})` : '';
            const qtyBadge = it.in_session
                ? `<span class="badge bg-light text-dark border">Sys: ${Number(it.system_qty).toFixed(0)} | Scan: ${Number(it.scanned_qty).toFixed(0)} | Rem: ${Number(it.remaining_qty).toFixed(0)}</span>`
                : `<span class="badge bg-light text-dark border">Stock: ${Number(it.system_qty).toFixed(0)}</span>`;

            html += `
                <button type="button" class="list-group-item list-group-item-action barcode-suggestion-item" data-index="${idx}" data-sku="${escapeHtml(it.sku)}">
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <div>
                            <strong class="text-primary">${escapeHtml(it.sku)}</strong>
                            <span class="badge bg-secondary-subtle text-dark border ms-1">Art: ${escapeHtml(it.art_no || '-')}</span>
                        </div>
                        ${qtyBadge}
                    </div>
                    <div class="small text-muted mt-1">
                        ${escapeHtml(it.style_name || '-')}${sleeveText} &bull; Size: <strong>${escapeHtml(it.size || '-')}</strong>${it.brand_name && it.brand_name !== '-' ? ' &bull; ' + escapeHtml(it.brand_name) : ''}
                    </div>
                </button>
            `;
        });
        $box.html(html).removeClass('d-none');
    }

    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s]));
    }

    function playBeep(freq, duration, type) {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = type || 'sine';
            osc.frequency.setValueAtTime(freq, ctx.currentTime);
            gain.gain.setValueAtTime(0.1, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + duration);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + duration);
        } catch (e) {}
    }

    function playStatusSound(status) {
        if (status === 'matched' || status === 'in_stock') {
            playBeep(960, 0.11, 'sine');
        } else if (status === 'excess') {
            playBeep(720, 0.10, 'triangle');
            setTimeout(() => playBeep(720, 0.14, 'triangle'), 140);
        } else {
            playBeep(210, 0.32, 'sawtooth');
        }
    }

    function refocusScanner() {
        if (!currentSessionFinalized && !$('#table_search_input').is(':focus')) {
            setTimeout(() => $('#barcode_input').focus(), 60);
        }
    }

    function applySessionMeta(meta) {
        if (!meta || !currentSessionId) {
            $('#session_details_bar').addClass('d-none');
            currentSessionFinalized = false;
            return;
        }

        currentSessionFinalized = !!meta.is_finalized;
        $('#detail_session_no').text(meta.session_no || '-');
        $('#detail_scope').text((meta.store_type_name || 'All Stores') + ' | ' + (meta.warehouse_name || 'All Warehouses'));
        $('#detail_date').text(meta.started_at_display || '-');
        $('#detail_counted_by').text(meta.counted_by || '-');

        if (meta.resumed_from_previous_day) {
            $('#detail_resumed_badge').removeClass('d-none');
        } else {
            $('#detail_resumed_badge').addClass('d-none');
        }

        if (currentSessionFinalized) {
            $('#detail_status_badge')
                .removeClass('bg-primary')
                .addClass('bg-dark')
                .text('Finalized' + (meta.completed_at_display ? ' (' + meta.completed_at_display + ')' : ''));
            if (meta.completed_by_name) {
                $('#detail_finalized_by').text(meta.completed_by_name);
                $('#detail_finalized_by_wrap').removeClass('d-none');
            } else {
                $('#detail_finalized_by_wrap').addClass('d-none');
            }
        } else {
            $('#detail_status_badge')
                .removeClass('bg-dark')
                .addClass('bg-primary')
                .text('In Progress');
            $('#detail_finalized_by_wrap').addClass('d-none');
        }

        $('#session_details_bar').removeClass('d-none');
    }

    function setSessionEnabled(enabled, isFinalized = false) {
        currentSessionFinalized = !!isFinalized;

        if (enabled && currentSessionId) {
            $('#export_excel_btn')
                .attr('href', baseUrl + '/session/' + currentSessionId + '/export')
                .removeClass('disabled')
                .css({ 'pointer-events': 'auto', 'opacity': '1' });

            if (isFinalized) {
                $('#finalize_btn').prop('disabled', true);
                $('#start_new_btn').removeClass('d-none');
                $('#barcode_input').prop('disabled', true).val('');
                hideBarcodeSuggestions();
                $('#btn_camera_scan').prop('disabled', true);
                $('#finalized_notice').removeClass('d-none');
                if (typeof stopCameraIfOpen === 'function') stopCameraIfOpen();
            } else {
                $('#finalize_btn').prop('disabled', false);
                $('#start_new_btn').addClass('d-none');
                $('#barcode_input').prop('disabled', false);
                $('#btn_camera_scan').prop('disabled', false);
                $('#finalized_notice').addClass('d-none');
            }
        } else {
            $('#export_excel_btn')
                .attr('href', 'javascript:void(0)')
                .addClass('disabled')
                .css({ 'pointer-events': 'none', 'opacity': '0.5' });
            $('#finalize_btn').prop('disabled', true);
            $('#start_new_btn').addClass('d-none');
            $('#barcode_input').prop('disabled', false);
            $('#btn_camera_scan').prop('disabled', false);
            $('#finalized_notice').addClass('d-none');
            applySessionMeta(null);
        }
    }

    function updateStats(st) {
        const sys = Number(st?.total_system_qty || 0);
        const totalSkus = Number(st?.total_skus || 0);
        const scan = Number(st?.total_scanned_qty || 0);
        const touchedSkus = Number(st?.touched_skus || 0);
        const rem = st?.remaining_qty !== undefined ? Number(st.remaining_qty) : Math.max(0, sys - scan);
        const matched = Number(st?.matched_skus || 0);
        const shortageSkus = Number(st?.shortage_skus || 0);
        const shortageQty = st?.shortage_qty !== undefined ? Number(st.shortage_qty) : rem;
        const excessSkus = Number(st?.excess_skus || 0);
        const excessQty = Number(st?.excess_qty || 0);
        const pct = st?.coverage_pct !== undefined
            ? Math.min(100, Math.max(0, Number(st.coverage_pct)))
            : (sys > 0 ? Math.min(100, Math.round((scan / sys) * 100)) : 0);
        const moveQty = Number(st?.total_movement_qty || 0);

        $('#stat_system_qty').text(sys.toLocaleString());
        $('#stat_total_skus').text(totalSkus.toLocaleString() + ' SKUs');
        $('#stat_scanned').text(scan.toLocaleString());
        $('#stat_touched_skus').text(touchedSkus.toLocaleString() + ' SKUs');
        $('#stat_remaining').text(rem.toLocaleString());
        $('#stat_completed').text(matched.toLocaleString());
        $('#stat_shortage_qty').text(Math.abs(shortageQty).toLocaleString() + ' pcs');
        $('#stat_shortage_skus').text(shortageSkus.toLocaleString() + ' SKUs');
        $('#stat_excess_qty').text(Math.abs(excessQty).toLocaleString() + ' pcs');
        $('#stat_excess_skus').text(excessSkus.toLocaleString() + ' SKUs');
        $('#progress_bar').css('width', pct + '%').attr('aria-valuenow', pct);
        $('#progress_text').text(pct + '%');

        if (Math.abs(moveQty) > 0.0001) {
            const sign = moveQty > 0 ? '+' : '';
            $('#detail_movement_badge')
                .removeClass('d-none')
                .text('Live Movement: ' + sign + moveQty.toLocaleString());
        } else {
            $('#detail_movement_badge').addClass('d-none').text('');
        }
    }

    function showScanAlert(typeClass, htmlMsg) {
        $('#scan_alert')
            .removeClass('alert-success alert-warning alert-danger alert-info')
            .addClass(typeClass)
            .show();
        $('#scan_msg').html(htmlMsg);
    }

    function loadBrandSession(onLoaded, forceNew = false) {
        const brandId = $('#filter_brand_id').val();
        const storeTypeId = $('#filter_store_type_id').val();
        const warehouseId = $('#filter_warehouse_id').val();
        $('#scan_alert').hide();

        if (activeBrandXhr) {
            activeBrandXhr.abort();
            activeBrandXhr = null;
        }
        if (activeTableXhr) {
            activeTableXhr.abort();
            activeTableXhr = null;
        }

        if (!brandId) {
            currentSessionId = null;
            currentPage = 1;
            setSessionEnabled(false, false);
            updateStats({ total_system_qty: 0, total_scanned_qty: 0, remaining_qty: 0, matched_skus: 0, coverage_pct: 0 });
            $('#pagination_bar').addClass('d-none');
            $('#table_body').html('<tr><td colspan="9" class="text-center py-4 text-muted">Please select a <strong>Brand</strong> above to load items and start scanning.</td></tr>');
            refocusScanner();
            return;
        }

        $('#table_body').html('<tr><td colspan="9" class="text-center py-4 text-muted">Loading items...</td></tr>');

        activeBrandXhr = $.ajax({
            url: baseUrl + '/load-brand',
            type: 'POST',
            data: {
                _token: csrfToken,
                brand_id: brandId,
                store_type_id: storeTypeId,
                warehouse_id: warehouseId,
                force_new: forceNew ? 1 : 0
            },
            success: function(res) {
                activeBrandXhr = null;
                currentSessionId = res.session_id;
                currentPage = 1;
                applySessionMeta(res.session);
                setSessionEnabled(true, res.session?.is_finalized);
                updateStats(res.stats);
                if (res.session?.resumed_from_previous_day) {
                    showScanAlert('alert-info', `<i class="ri ri-history-line me-1"></i> Resumed unfinished session <strong>${escapeHtml(res.session_no)}</strong> started on <strong>${escapeHtml(res.session.started_at_display)}</strong>.`);
                }
                loadTable(1);
                refocusScanner();
                if (typeof onLoaded === 'function') {
                    onLoaded();
                }
            },
            error: function(xhr, statusText) {
                activeBrandXhr = null;
                if (statusText === 'abort') return;
                if (activeTableXhr) {
                    activeTableXhr.abort();
                    activeTableXhr = null;
                }
                currentSessionId = null;
                setSessionEnabled(false, false);
                $('#pagination_bar').addClass('d-none');
                updateStats({ total_system_qty: 0, total_scanned_qty: 0, remaining_qty: 0, matched_skus: 0, coverage_pct: 0 });
                const msg = xhr.responseJSON?.message || 'No positive Finished Goods stock found for this Brand.';
                $('#table_body').html(`<tr><td colspan="9" class="text-center py-4 text-danger">${escapeHtml(msg)}</td></tr>`);
            }
        });
    }

    function processScannedBarcode(rawCode, allowRetry = true, allowDuplicate = false) {
        const code = String(rawCode || '').trim();
        hideBarcodeSuggestions();
        if (!code) return;
        $('#barcode_input').val('');

        if (currentSessionFinalized) {
            playBeep(210, 0.32, 'sawtooth');
            showScanAlert('alert-danger', '<i class="ri ri-lock-line me-1"></i> <strong>Session Finalized:</strong> Further scanning is blocked.');
            return;
        }

        if (!currentSessionId) {
            // Instant lookup if Brand not yet picked
            $.post(baseUrl + '/quick-check', {
                _token: csrfToken,
                code: code,
                store_type_id: $('#filter_store_type_id').val(),
                warehouse_id: $('#filter_warehouse_id').val()
            }, function(res) {
                playStatusSound(res.status);
                if (!res.success && res.status === 'not_found') {
                    showScanAlert('alert-danger', `<strong>Not Found:</strong> ${escapeHtml(res.message)}`);
                } else {
                    const it = res.item;
                    showScanAlert('alert-info', `<strong>${escapeHtml(it.brand_name)}</strong> | SKU: <strong>${escapeHtml(it.sku)}</strong> | Available Qty: <strong>${it.total_qty}</strong> <small>(Select Brand above to scan & count)</small>`);
                }
                refocusScanner();
            });
            return;
        }

        $.ajax({
            url: baseUrl + '/session/' + currentSessionId + '/scan',
            type: 'POST',
            data: {
                _token: csrfToken,
                barcode: code,
                qty: 1,
                allow_duplicate: allowDuplicate ? 1 : 0,
                store_location_id: $('#filter_store_location_id').val() || ''
            },
            success: function(res) {
                playStatusSound(res.scan_status);
                updateStats(res.stats);
                if (res.scan_status === 'matched') {
                    showScanAlert('alert-success', `<i class="ri ri-checkbox-circle-line me-1"></i> <strong>${escapeHtml(res.message)}</strong>`);
                } else if (res.scan_status === 'excess') {
                    showScanAlert('alert-warning', `<i class="ri ri-error-warning-line me-1"></i> <strong>${escapeHtml(res.message)}</strong>`);
                } else {
                    showScanAlert('alert-danger', `<i class="ri ri-close-circle-line me-1"></i> <strong>${escapeHtml(res.message)}</strong>`);
                }
                loadTable(1);
                refocusScanner();
            },
            error: function(xhr) {
                const json = xhr.responseJSON || {};
                if (xhr.status === 409 && json.scan_status === 'duplicate') {
                    playBeep(320, 0.24, 'triangle');
                    if (json.stats) updateStats(json.stats);
                    const encodedCode = encodeURIComponent(json.raw_code || code);
                    showScanAlert(
                        'alert-warning',
                        `<i class="ri ri-spam-2-line me-1"></i> <strong>${escapeHtml(json.message)}</strong>
                         <button type="button" class="btn btn-sm btn-warning ms-2 fw-bold btn-override-dup" data-code="${encodedCode}">Count +1 Anyway</button>`
                    );
                    loadTable(currentPage);
                    refocusScanner();
                    return;
                }
                if (xhr.status === 422 && json.finalized) {
                    setSessionEnabled(true, true);
                    playBeep(210, 0.32, 'sawtooth');
                    showScanAlert('alert-danger', `<i class="ri ri-lock-line me-1"></i> <strong>${escapeHtml(json.message)}</strong>`);
                    return;
                }
                if ((xhr.status === 404 || json.session_expired) && allowRetry && $('#filter_brand_id').val()) {
                    loadBrandSession(() => processScannedBarcode(code, false, allowDuplicate));
                    return;
                }
                playBeep(210, 0.32, 'sawtooth');
                showScanAlert('alert-danger', escapeHtml(json.message || 'Error processing scan.'));
                refocusScanner();
            }
        });
    }

    $(document).on('click', '.btn-override-dup', function() {
        const encoded = $(this).data('code') || '';
        const decoded = decodeURIComponent(String(encoded));
        if (decoded) {
            processScannedBarcode(decoded, true, true);
        }
    });

    $('#finalize_btn').on('click', function() {
        if (!currentSessionId || currentSessionFinalized) return;

        Swal.fire({
            title: 'Finalize Stock Taking?',
            text: 'Once finalized, barcode scanning will be locked for this session.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Finalize',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.post(baseUrl + '/session/' + currentSessionId + '/finalize', {
                _token: csrfToken
            }, function(res) {
                applySessionMeta(res.session);
                setSessionEnabled(true, true);
                updateStats(res.stats);
                loadTable(1);
                showScanAlert('alert-info', `<i class="ri ri-lock-fill me-1"></i> <strong>${escapeHtml(res.message)}</strong>`);
            }).fail(function(xhr) {
                showScanAlert('alert-danger', escapeHtml(xhr.responseJSON?.message || 'Unable to finalize session.'));
            });
        });
    });

    $('#start_new_btn').on('click', function() {
        if ($('#filter_brand_id').val()) {
            loadBrandSession(null, true);
        }
    });

    function renderPagination(pg) {
        if (!pg || pg.total <= 0) {
            $('#pagination_bar').addClass('d-none');
            return;
        }

        $('#pagination_bar').removeClass('d-none');
        $('#pagination_info').text(`Showing ${Number(pg.from).toLocaleString()} to ${Number(pg.to).toLocaleString()} of ${Number(pg.total).toLocaleString()} entries`);

        const cur = pg.current_page;
        const last = pg.last_page;
        let html = '';

        html += `<li class="page-item ${cur <= 1 ? 'disabled' : ''}">
            <a class="page-link ajax-page-link" href="javascript:void(0)" data-page="${cur - 1}">&laquo; Prev</a>
        </li>`;

        const pages = [];
        for (let p = 1; p <= last; p++) {
            if (p === 1 || p === last || (p >= cur - 2 && p <= cur + 2)) {
                pages.push(p);
            }
        }

        let prev = null;
        pages.forEach(p => {
            if (prev !== null && p - prev > 1) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            html += `<li class="page-item ${p === cur ? 'active' : ''}">
                <a class="page-link ajax-page-link" href="javascript:void(0)" data-page="${p}">${p}</a>
            </li>`;
            prev = p;
        });

        html += `<li class="page-item ${cur >= last ? 'disabled' : ''}">
            <a class="page-link ajax-page-link" href="javascript:void(0)" data-page="${cur + 1}">Next &raquo;</a>
        </li>`;

        $('#pagination_links').html(html);
    }

    function loadTable(page) {
        if (!currentSessionId) return;
        if (page !== undefined && page !== null) {
            currentPage = page;
        }
        if (activeTableXhr) {
            activeTableXhr.abort();
            activeTableXhr = null;
        }
        const reqSessionId = currentSessionId;
        const filter = $('#table_filter_select').val() || 'all';
        const search = $('#table_search_input').val() || '';
        const perPage = $('#table_per_page').val() || 10;

        activeTableXhr = $.get(baseUrl + '/session/' + reqSessionId + '/items', {
            filter: filter,
            search: search,
            per_page: perPage,
            page: currentPage
        }, function(res) {
            activeTableXhr = null;
            if (!currentSessionId || reqSessionId !== currentSessionId) {
                return;
            }
            const thead = $('#table_head');
            const tbody = $('#table_body');
            renderPagination(res.pagination);

            if (res.mode === 'logs') {
                thead.html(`
                    <tr>
                        <th>Time</th>
                        <th>SKU / Barcode</th>
                        <th>Detected Brand</th>
                        <th>Art No / Size</th>
                        <th>Store Location</th>
                        <th>Qty</th>
                        <th>Status & Message</th>
                    </tr>
                `);
                if (!res.rows.length) {
                    tbody.html('<tr><td colspan="7" class="text-center py-4 text-muted">No alerts recorded yet.</td></tr>');
                    return;
                }
                let html = '';
                res.rows.forEach(r => {
                    let badge = '<span class="badge bg-danger">Not Found</span>';
                    if (r.scan_status === 'wrong_brand') badge = '<span class="badge bg-danger">Wrong Brand</span>';
                    else if (r.scan_status === 'zero_stock') badge = '<span class="badge bg-secondary">Zero Stock</span>';
                    else if (r.scan_status === 'duplicate') badge = '<span class="badge bg-warning text-dark">Duplicate</span>';
                    html += `
                        <tr>
                            <td class="small">${escapeHtml(r.time)}</td>
                            <td><strong>${escapeHtml(r.resolved_sku || '-')}</strong></td>
                            <td>${escapeHtml(r.detected_brand_name || '-')}</td>
                            <td>${escapeHtml((r.art_no || '-') + ' / ' + (r.size || '-'))}</td>
                            <td>${escapeHtml(r.store_location || '-')}</td>
                            <td>${Number(r.qty).toFixed(0)}</td>
                            <td>${badge} <small class="ms-1">${escapeHtml(r.message)}</small></td>
                        </tr>
                    `;
                });
                tbody.html(html);
                return;
            }

            thead.html(`
                <tr>
                    <th>Item Name</th>
                    <th>Art No</th>
                    <th>SKU / Barcode</th>
                    <th>System Qty</th>
                    <th>Scanned</th>
                    <th>Remaining</th>
                    <th>Variance</th>
                    <th>Status</th>
                    <th>Store Location</th>
                </tr>
            `);

            if (!res.rows.length) {
                tbody.html('<tr><td colspan="9" class="text-center py-4 text-muted">No items found for this filter.</td></tr>');
                return;
            }

            let html = '';
            res.rows.forEach(r => {
                const sys = Number(r.system_qty || 0);
                const scan = Number(r.scanned_qty || 0);
                const rem = Number(r.remaining_qty !== undefined ? r.remaining_qty : Math.max(0, sys - scan));
                const diff = Number(r.variance !== undefined ? r.variance : (scan - sys));
                const mv = Number(r.movement_qty || 0);

                let badge = '<span class="badge bg-secondary">Pending</span>';
                let rowCls = '';
                if (r.status === 'completed') {
                    badge = '<span class="badge bg-success">Completed</span>';
                    rowCls = 'table-success';
                } else if (r.status === 'excess') {
                    badge = '<span class="badge bg-warning text-dark">Excess (+' + Math.abs(diff).toFixed(0) + ')</span>';
                    rowCls = 'table-warning';
                } else if (r.status === 'in_progress') {
                    badge = '<span class="badge bg-info text-dark">In Progress</span>';
                }

                let varHtml = '<span class="text-muted">0</span>';
                if (diff > 0.001) {
                    varHtml = '<span class="text-warning fw-bold">+' + diff.toFixed(0) + '</span>';
                } else if (diff < -0.001) {
                    varHtml = '<span class="text-danger fw-bold">' + diff.toFixed(0) + '</span>';
                } else {
                    varHtml = '<span class="text-success fw-bold">0</span>';
                }

                const mvHtml = Math.abs(mv) > 0.0001
                    ? `<br><small class="text-muted">(Snap: ${Number(r.snapshot_qty).toFixed(0)}, Move: ${mv > 0 ? '+' : ''}${mv.toFixed(0)})</small>`
                    : '';

                const locHtml = r.store_location
                    ? `<span class="badge bg-light text-dark border">${escapeHtml(r.store_location)}</span>`
                    : '<span class="text-muted">-</span>';

                html += `
                    <tr class="${rowCls}">
                        <td>
                            ${escapeHtml(r.style_name)} ${r.sleeve_type && r.sleeve_type !== '-' ? '(' + escapeHtml(r.sleeve_type) + ')' : ''}
                            <br>
                            <small class="text-muted">Size: ${escapeHtml(r.size)}</small>
                        </td>
                        <td>${escapeHtml(r.art_no)}</td>
                        <td class="fw-bold">${escapeHtml(r.sku)}</td>
                        <td>${sys.toFixed(0)}${mvHtml}</td>
                        <td class="fw-bold">${scan.toFixed(0)}</td>
                        <td>${rem.toFixed(0)}</td>
                        <td>${varHtml}</td>
                        <td>${badge}</td>
                        <td>${locHtml}</td>
                    </tr>
                `;
            });
            tbody.html(html);
        });
    }

    $('#filter_brand_id, #filter_store_type_id, #filter_warehouse_id').on('change', () => loadBrandSession(null, false));
    $('#filter_store_location_id').on('change', () => refocusScanner());
    $('#table_filter_select, #table_per_page').on('change', () => loadTable(1));
    $(document).on('click', '.stat-card-filter', function() {
        const targetFilter = $(this).data('filter');
        if (!currentSessionId || !targetFilter) return;
        $('#table_filter_select').val(targetFilter).trigger('change');
        $('html, body').animate({ scrollTop: $('#items_table').closest('.card').offset().top - 70 }, 200);
    });
    $('#table_search_input').on('keyup', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => loadTable(1), 250);
    });
    $(document).on('click', '.ajax-page-link', function(e) {
        e.preventDefault();
        const p = parseInt($(this).data('page'));
        if (p && p > 0) {
            loadTable(p);
        }
    });

    $('#barcode_input').on('input', function() {
        if (currentSessionFinalized) {
            hideBarcodeSuggestions();
            return;
        }
        const q = $(this).val().trim();
        clearTimeout(suggestTimer);
        if (q.length < 1) {
            hideBarcodeSuggestions();
            return;
        }
        suggestTimer = setTimeout(() => {
            if (activeSuggestXhr) {
                activeSuggestXhr.abort();
            }
            activeSuggestXhr = $.get(baseUrl + '/search-items', {
                q: q,
                session_id: currentSessionId || '',
                brand_id: $('#filter_brand_id').val() || '',
                store_type_id: $('#filter_store_type_id').val() || '',
                warehouse_id: $('#filter_warehouse_id').val() || ''
            }, function(res) {
                activeSuggestXhr = null;
                if ($('#barcode_input').val().trim() !== q) return;
                renderBarcodeSuggestions(res.items || []);
            }).fail(function(xhr, statusText) {
                if (statusText !== 'abort') {
                    activeSuggestXhr = null;
                }
            });
        }, 180);
    });

    $('#barcode_input').on('keydown', function(e) {
        if (e.key === 'ArrowDown' && suggestionItems.length && !$('#barcode_suggestions').hasClass('d-none')) {
            e.preventDefault();
            highlightedSuggestIdx = (highlightedSuggestIdx + 1) % suggestionItems.length;
            updateSuggestionHighlight();
            return;
        }
        if (e.key === 'ArrowUp' && suggestionItems.length && !$('#barcode_suggestions').hasClass('d-none')) {
            e.preventDefault();
            highlightedSuggestIdx = highlightedSuggestIdx <= 0 ? suggestionItems.length - 1 : highlightedSuggestIdx - 1;
            updateSuggestionHighlight();
            return;
        }
        if (e.key === 'Escape') {
            hideBarcodeSuggestions();
            return;
        }
        if (e.which === 13 || e.key === 'Enter') {
            e.preventDefault();
            if (highlightedSuggestIdx >= 0 && suggestionItems[highlightedSuggestIdx] && !$('#barcode_suggestions').hasClass('d-none')) {
                const selectedSku = suggestionItems[highlightedSuggestIdx].sku;
                hideBarcodeSuggestions();
                processScannedBarcode(selectedSku);
                return;
            }
            hideBarcodeSuggestions();
            processScannedBarcode($(this).val());
        }
    });

    $(document).on('mousedown', '.barcode-suggestion-item', function(e) {
        e.preventDefault();
        const sku = $(this).data('sku');
        hideBarcodeSuggestions();
        if (sku) {
            processScannedBarcode(sku);
        }
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#barcode_search_wrap').length) {
            hideBarcodeSuggestions();
        }
    });

    // Mobile Camera Scanner matching sales_invoice/scan_items.blade.php
    let html5QrCode = null;
    let isCameraOpen = false;
    let camLock = false;

    function stopCameraIfOpen() {
        if (isCameraOpen && html5QrCode) {
            html5QrCode.stop().then(() => {
                $('#reader').hide();
                isCameraOpen = false;
                $('#btn_camera_scan').html('<i class="ri ri-camera-line me-1"></i> Camera');
            }).catch(() => {});
        }
    }

    $('#btn_camera_scan').on('click', function() {
        if (currentSessionFinalized) return;
        const btn = $(this);
        if (isCameraOpen && html5QrCode) {
            stopCameraIfOpen();
            return;
        }
        $('#reader').show();
        html5QrCode = new Html5Qrcode('reader');
        html5QrCode.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 250, height: 150 } },
            (decodedText) => {
                if (camLock) return;
                camLock = true;
                processScannedBarcode(decodedText);
                setTimeout(() => { camLock = false; }, 1400);
            },
            () => {}
        ).then(() => {
            isCameraOpen = true;
            btn.html('<i class="ri ri-close-line me-1"></i> Close Camera');
        }).catch(() => {
            $('#reader').hide();
            showScanAlert('alert-danger', '<strong>Error:</strong> Could not start camera. Please ensure camera permissions are granted.');
        });
    });

    function openSessionById(sessionId) {
        if (!sessionId) return;
        $('#scan_alert').hide();
        if (activeBrandXhr) {
            activeBrandXhr.abort();
            activeBrandXhr = null;
        }
        if (activeTableXhr) {
            activeTableXhr.abort();
            activeTableXhr = null;
        }

        $.get(baseUrl + '/session/' + sessionId, function(res) {
            if (!res.success) return;

            $('#filter_brand_id').val(res.brand_id || '').trigger('change.select2');
            $('#filter_store_type_id').val(res.store_type_id || '').trigger('change.select2');
            $('#filter_warehouse_id').val(res.warehouse_id || '').trigger('change.select2');

            currentSessionId = res.session_id;
            currentPage = 1;
            applySessionMeta(res.session);
            setSessionEnabled(true, res.session?.is_finalized);
            updateStats(res.stats);
            loadTable(1);

            if (res.session?.is_finalized) {
                showScanAlert('alert-info', `<i class="ri ri-lock-fill me-1"></i> Viewing finalized record <strong>${escapeHtml(res.session_no)}</strong>. You can export Excel or click <strong>Start New Count</strong>.`);
            } else {
                showScanAlert('alert-info', `<i class="ri ri-play-circle-line me-1"></i> Loaded in-progress session <strong>${escapeHtml(res.session_no)}</strong>.`);
                refocusScanner();
            }

            $('html, body').animate({ scrollTop: 0 }, 200);
        }).fail(function(xhr) {
            showScanAlert('alert-danger', escapeHtml(xhr.responseJSON?.message || 'Unable to load selected session.'));
        });
    }

    const initialSessionId = @json($selectedSessionId ?? null);
    if (initialSessionId) {
        openSessionById(initialSessionId);
    } else if ($('#filter_brand_id').val()) {
        loadBrandSession(null, false);
    }
});
</script>
@endsection

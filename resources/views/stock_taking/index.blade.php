@extends('layouts.common')
@section('title', 'FG Stock Taking - ' . env('WEBSITE_NAME'))

@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box mb-3">
                <h4>FG Stock Taking</h4>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ url('fg_stock_taking/logs') }}" class="btn btn-outline-primary" id="stock_taking_logs_btn">
                        <i class="ri ri-history-line me-1"></i> Stock Taking Logs
                        @if(($logsCount ?? 0) > 0)
                            <span class="badge bg-primary text-white ms-1" id="history_count_badge">{{ number_format($logsCount) }}</span>
                        @endif
                    </a>
                    <a href="{{ url('fg_stock_taking/open-stock-logs') }}" class="btn btn-outline-info" id="btn_open_stock_logs">
                        <i class="ri ri-file-list-3-line me-1"></i> Open Stock Logs
                        @if(($openStockLogsCount ?? 0) > 0)
                            <span class="badge bg-info text-white ms-1" id="open_stock_logs_badge">{{ number_format($openStockLogsCount) }}</span>
                        @endif
                    </a>
                    <a href="{{ url('fg_stock_taking/open-stock-entry') }}" class="btn btn-outline-success" id="btn_open_stock_entry">
                        <i class="ri ri-inbox-archive-line me-1"></i> Open Stock Entry
                    </a>
                    <button type="button" class="btn btn-outline-primary d-none" id="start_new_btn">
                        <i class="ri ri-add-circle-line me-1"></i> Start New Count
                    </button>
                    <button type="button" class="btn btn-primary" id="finalize_btn" disabled>
                        <i class="ri ri-lock-line me-1"></i> Finalize Stock Taking
                    </button>
                    <div class="btn-group" id="export_dropdown_group">
                        <button type="button" class="btn btn-success dropdown-toggle disabled" id="export_dropdown_btn" data-bs-toggle="dropdown" aria-expanded="false" style="pointer-events: none; opacity: 0.5;">
                            <i class="ri ri-download-2-line me-1"></i> Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li>
                                <a class="dropdown-item d-flex align-items-center py-2" href="javascript:void(0)" id="export_excel_link">
                                    <i class="ri ri-file-excel-line text-success fs-5 me-2"></i>
                                    <div>
                                        <div class="fw-semibold">Excel Format</div>
                                        <div class="text-muted small" style="font-size: 11px;">Detailed items & scan logs (.xlsx)</div>
                                    </div>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center py-2" href="javascript:void(0)" id="export_summary_pdf_link" target="_blank">
                                    <i class="ri ri-file-chart-line text-info fs-5 me-2"></i>
                                    <div>
                                        <div class="fw-semibold">Audit Summary PDF</div>
                                        <div class="text-muted small" style="font-size: 11px;">1-Page Reconciliation Certificate (Signatures)</div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center py-2" href="javascript:void(0)" id="export_pdf_link" target="_blank">
                                    <i class="ri ri-file-pdf-line text-danger fs-5 me-2"></i>
                                    <div>
                                        <div class="fw-semibold">Detailed PDF Report</div>
                                        <div class="text-muted small" style="font-size: 11px;">Full SKU-by-SKU discrepancy breakdown</div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <a href="{{ url('stock_entries') }}" class="btn btn-secondary">
                        <i class="ri ri-arrow-left-line back-arrow"></i> Back to Stock Entry
                    </a>
                </div>
            </div>

            <!-- Main Card matching Sales Invoice Scan UI -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="filter-box mb-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-lg-12">
                                <h5 class="mb-0">Filter</h5>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3">
                                <select id="filter_brand_id" class="form-select select2" data-placeholder="Select Brand">
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ ($selectedBrandId ?? null) == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->brand_name }} ({{ number_format($brand->fg_sku_count) }} SKUs | {{ number_format($brand->fg_total_qty, 0) }} pcs)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3">
                                <select id="filter_store_type_id" class="form-select select2" data-placeholder="Select Store">
                                    @php
                                        $allBrandQty = (!empty($selectedBrandId) && !empty($brands->firstWhere('id', $selectedBrandId)?->fg_total_qty)) ? $brands->firstWhere('id', $selectedBrandId)->fg_total_qty : null;
                                    @endphp
                                    <option value="" data-base-name="All Stores">All Stores{{ $allBrandQty ? ' (' . number_format($allBrandQty, 0) . ' pcs)' : '' }}</option>
                                    @foreach($storeTypes as $st)
                                        @php
                                            $pcs = isset($storeCounts[$st->id]) ? $storeCounts[$st->id] : null;
                                            $pcsSuffix = ($pcs !== null && !empty($selectedBrandId)) ? ' (' . number_format($pcs, 0) . ' pcs)' : '';
                                        @endphp
                                        <option value="{{ $st->id }}"
                                                data-base-name="{{ $st->store_type_name }}"
                                                {{ ($selectedStoreTypeId ?? null) == $st->id ? 'selected' : '' }}>
                                            {{ $st->store_type_name }}{{ $pcsSuffix }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-2">
                                <select id="filter_warehouse_id" class="form-select select2" data-placeholder="Select Warehouse">
                                    <option value="">All Warehouses</option>
                                    @foreach($warehouses as $wh)
                                        <option value="{{ $wh->id }}" {{ ($selectedWarehouseId ?? null) == $wh->id ? 'selected' : '' }}>
                                            {{ $wh->warehouse_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-2">
                                <select id="filter_store_location_id" class="form-select select2" data-placeholder="Select Store Location">
                                    <option value="">Select Store Location</option>
                                    @foreach($storeLocations as $loc)
                                        <option value="{{ $loc->id }}" {{ ($selectedStoreLocationId ?? null) == $loc->id ? 'selected' : '' }}>
                                            {{ $loc->store_location }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-2 d-flex gap-2">
                                <button type="button" id="btn_apply_filter" class="btn btn-primary text-uppercase">Filter</button>
                                <button type="button" id="btn_reset_stock_taking" class="btn btn-secondary text-uppercase">Reset</button>
                            </div>
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

            <!-- Items Table Card with DataTables Integration -->
            <div class="card">
                <div class="card-header bg-white fw-bold">
                    <span class="fs-5">Items Table</span>
                </div>
                <div class="card-body">
                    <div class="filter-box mb-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-lg-12">
                                <h5 class="mb-0">Filter</h5>
                            </div>
                            <div class="col-md-3">
                                <select id="table_filter_select" class="form-select select2" data-placeholder="Select Status">
                                    <option value="all" selected>All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="shortage">Shortage</option>
                                    <option value="excess">Excess</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="button" id="btn_table_filter" class="btn btn-primary text-uppercase">Filter</button>
                                <button type="button" id="btn_table_reset" class="btn btn-secondary text-uppercase">Reset</button>
                            </div>
                        </div>
                    </div>

                    <div class="card-datatable table-responsive">
                        <table class="table table-bordered table-striped" id="items_table" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th>Item Name</th>
                                    <th>Art No</th>
                                    <th>SKU / Barcode</th>
                                    <th>System Qty</th>
                                    <th>Open Qty</th>
                                    <th>Scanned</th>
                                    <th>Shortage</th>
                                    <th>Excess</th>
                                    <th>Status</th>
                                    <th>Store Location</th>
                                    <th class="text-center" style="width: 105px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="11" class="text-center py-4 text-muted">
                                        Please select a <strong>Brand</strong> above to load items and start scanning.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Store Locations Modal -->
            <div class="modal fade" id="modalStoreLocations" tabindex="-1" aria-labelledby="modalStoreLocationsTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content shadow border-0">
                        <div class="modal-header border-bottom py-3">
                            <h5 class="modal-title d-flex align-items-center gap-2" id="modalStoreLocationsTitle">
                                <i class="ri ri-map-pin-2-fill text-primary fs-4"></i>
                                <span>All Store Locations</span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body py-3">
                            <div class="mb-3 d-flex flex-wrap gap-2 align-items-center justify-content-between p-2 bg-light rounded">
                                <div>
                                    <small class="text-muted d-block">Item / SKU</small>
                                    <strong id="modal_loc_sku_display" class="text-dark fs-6">-</strong>
                                </div>
                                <div>
                                    <span class="badge bg-primary text-white" id="modal_loc_total_badge">0 Locations</span>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2 p-1" id="modal_loc_badges_container" style="max-height: 350px; overflow-y: auto;">
                                <!-- badges rendered dynamically -->
                            </div>
                        </div>
                        <div class="modal-footer py-2 border-top">
                            <button type="button" class="btn btn-secondary text-uppercase btn-sm" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .select2-container--default .select2-results > .select2-results__options {
        max-height: 280px;
    }
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
    #ose_items_table input.form-control-sm,
    #ose_items_table select.form-select-sm {
        font-size: 0.8rem;
        padding: 0.25rem 0.4rem;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
$(document).ready(function() {
    if ($.fn.select2) {
        if ($('#table_filter_select').hasClass('select2-hidden-accessible')) {
            $('#table_filter_select').select2('destroy');
        }
        $('#table_filter_select').select2({
            minimumResultsForSearch: Infinity,
            width: '100%',
            dropdownParent: $('#table_filter_select').closest('.col-md-3')
        });
    }

    const csrfToken = '{{ csrf_token() }}';
    const baseUrl = '{{ url('fg_stock_taking') }}';
    let currentSessionId = null;
    let currentSessionFinalized = false;
    let lastScannedLogId = null;
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
                        ${escapeHtml(it.finished_item_code || it.item_name || it.style_name || '-')}${sleeveText} &bull; Size: <strong>${escapeHtml(it.size || '-')}</strong>${it.brand_name && it.brand_name !== '-' ? ' &bull; ' + escapeHtml(it.brand_name) : ''}
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
            $('#detail_status_badge').removeClass('bg-primary').addClass('bg-dark').text('Finalized' + (meta.completed_at_display ? ' (' + meta.completed_at_display + ')' : ''));
            if (meta.completed_by_name) {
                $('#detail_finalized_by').text(meta.completed_by_name);
                $('#detail_finalized_by_wrap').removeClass('d-none');
            } else {
                $('#detail_finalized_by_wrap').addClass('d-none');
            }
        } else {
            $('#detail_status_badge').removeClass('bg-dark').addClass('bg-primary').text('In Progress');
            $('#detail_finalized_by_wrap').addClass('d-none');
        }

        $('#session_details_bar').removeClass('d-none');
    }

    function setSessionEnabled(enabled, isFinalized = false) {
        currentSessionFinalized = !!isFinalized;

        if (enabled && currentSessionId) {
            $('#export_dropdown_btn').removeClass('disabled').css({ 'pointer-events': 'auto', 'opacity': '1' });
            $('#export_excel_link').attr('href', baseUrl + '/session/' + currentSessionId + '/export');
            $('#export_pdf_link').attr('href', baseUrl + '/session/' + currentSessionId + '/pdf');
            $('#export_summary_pdf_link').attr('href', baseUrl + '/session/' + currentSessionId + '/pdf?mode=summary');

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
            $('#export_dropdown_btn').addClass('disabled').css({ 'pointer-events': 'none', 'opacity': '0.5' });
            $('#export_excel_link').attr('href', 'javascript:void(0)');
            $('#export_pdf_link, #export_summary_pdf_link').attr('href', 'javascript:void(0)');
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
            $('#detail_movement_badge').removeClass('d-none').text('Live Movement: ' + sign + moveQty.toLocaleString());
        } else {
            $('#detail_movement_badge').addClass('d-none').text('');
        }
    }

    function showScanAlert(typeClass, htmlMsg) {
        $('#scan_alert').removeClass('alert-success alert-warning alert-danger alert-info').addClass(typeClass).show();
        $('#scan_msg').html(htmlMsg);
    }

    function updateStoreDropdownCounts(storeCounts, totalBrandQty) {
        const $storeSelect = $('#filter_store_type_id');
        const curVal = $storeSelect.val();

        $storeSelect.find('option').each(function() {
            const $opt = $(this);
            const val = $opt.val();
            let baseName = $opt.attr('data-base-name');
            if (!baseName) {
                baseName = $opt.text().replace(/\s*\([\d,.]+\s*pcs\)\s*$/i, '').trim();
                $opt.attr('data-base-name', baseName);
            }

            if (!val) {
                if (totalBrandQty !== undefined && totalBrandQty !== null && Number(totalBrandQty) > 0) {
                    $opt.text(baseName + ' (' + Number(totalBrandQty).toLocaleString() + ' pcs)');
                } else {
                    $opt.text(baseName);
                }
            } else {
                if (storeCounts && typeof storeCounts === 'object') {
                    const count = storeCounts[val] !== undefined ? Number(storeCounts[val]) : 0;
                    $opt.text(baseName + ' (' + count.toLocaleString() + ' pcs)');
                } else {
                    $opt.text(baseName);
                }
            }
        });

        if (typeof $storeSelect.select2 === 'function') {
            if ($storeSelect.data('select2')) {
                $storeSelect.select2('destroy');
            }
            $storeSelect.select2({
                placeholder: $storeSelect.data('placeholder') || 'All Stores',
                width: '100%',
                allowClear: true,
                dropdownParent: $('body')
            });
            if (curVal !== undefined && curVal !== null && curVal !== '') {
                $storeSelect.val(curVal);
            }
        }
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
            updateStoreDropdownCounts(null, null);
            currentSessionId = null;
            currentPage = 1;
            setSessionEnabled(false, false);
            updateStats({ total_system_qty: 0, total_scanned_qty: 0, remaining_qty: 0, matched_skus: 0, coverage_pct: 0 });
            if (itemsDataTable) {
                itemsDataTable.destroy();
                itemsDataTable = null;
            }
            $('#items_table tbody').html('<tr><td colspan="11" class="text-center py-4 text-muted">Please select a <strong>Brand</strong> above to load items and start scanning.</td></tr>');
            refocusScanner();
            return;
        }

        if (itemsDataTable) {
            itemsDataTable.destroy();
            itemsDataTable = null;
        }
        $('#items_table tbody').html('<tr><td colspan="11" class="text-center py-4 text-muted">Loading items...</td></tr>');

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
                if (res.store_counts !== undefined) {
                    updateStoreDropdownCounts(res.store_counts, res.total_brand_qty || res.stats?.total_system_qty);
                }
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
                $('#start_new_btn').prop('disabled', false).html('<i class="ri ri-add-circle-line me-1"></i> Start New Count');
                if (statusText === 'abort') return;
                if (activeTableXhr) {
                    activeTableXhr.abort();
                    activeTableXhr = null;
                }
                currentSessionId = null;
                setSessionEnabled(false, false);
                if (itemsDataTable) {
                    itemsDataTable.destroy();
                    itemsDataTable = null;
                }
                updateStats({ total_system_qty: 0, total_scanned_qty: 0, remaining_qty: 0, matched_skus: 0, coverage_pct: 0 });
                const msg = xhr.responseJSON?.message || 'No positive Finished Goods stock found for this Brand.';
                $('#items_table tbody').html(`<tr><td colspan="11" class="text-center py-4 text-danger">${escapeHtml(msg)}</td></tr>`);
            }
        });
    }

    let lastProcessedBarcode = '';
    let lastProcessedTimestamp = 0;

    function processScannedBarcode(rawCode, allowRetry = true, allowDuplicate = false) {
        const code = String(rawCode || '').trim();
        hideBarcodeSuggestions();
        if (!code) return;
        $('#barcode_input').val('');

        const now = Date.now();
        if (!allowDuplicate && code === lastProcessedBarcode && (now - lastProcessedTimestamp) < 350) {
            return;
        }
        lastProcessedBarcode = code;
        lastProcessedTimestamp = now;

        if (currentSessionFinalized) {
            playBeep(210, 0.32, 'sawtooth');
            showScanAlert('alert-danger', '<i class="ri ri-lock-line me-1"></i> <strong>Session Finalized:</strong> Further scanning is blocked.');
            return;
        }

        if (!currentSessionId) {
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
                lastScannedLogId = res.log_id || null;
                const undoBtn = (!currentSessionFinalized && res.log_id)
                    ? `<button type="button" class="btn btn-sm btn-dark ms-2 fw-bold btn-undo-last-scan" data-log-id="${res.log_id}" title="Undo this scan (or press Ctrl+Z)"><i class="ri ri-arrow-go-back-line me-1"></i>Undo (-1)</button>`
                    : '';
                if (res.scan_status === 'matched') {
                    showScanAlert('alert-success', `<i class="ri ri-checkbox-circle-line me-1"></i> <strong>${escapeHtml(res.message)}</strong> ${undoBtn}`);
                } else if (res.scan_status === 'excess') {
                    showScanAlert('alert-warning', `<i class="ri ri-error-warning-line me-1"></i> <strong>${escapeHtml(res.message)}</strong> ${undoBtn}`);
                } else {
                    showScanAlert('alert-danger', `<i class="ri ri-close-circle-line me-1"></i> <strong>${escapeHtml(res.message)}</strong>`);
                }
                loadTable(false);
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
                    loadTable(false);
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

            const $btn = $('#finalize_btn');
            const originalBtnHtml = $btn.html();
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Finalizing...');

            Swal.fire({
                title: 'Finalizing Stock Taking...',
                html: '<div class="text-muted small">Converting barcodes and updating inventory ledger.<br>Please wait...</div>',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.post(baseUrl + '/session/' + currentSessionId + '/finalize', {
                _token: csrfToken
            }, function(res) {
                applySessionMeta(res.session);
                setSessionEnabled(true, true);
                updateStats(res.stats);
                loadTable(1);
                showScanAlert('alert-info', `<i class="ri ri-lock-fill me-1"></i> <strong>${escapeHtml(res.message)}</strong>`);

                Swal.fire({
                    title: 'Stock Taking Finalized!',
                    html: `
                        <div class="text-start mb-3">
                            <p class="mb-2"><strong>${escapeHtml(res.message)}</strong></p>
                            <div class="alert alert-success py-2 px-3 small mb-0">
                                <i class="ri ri-checkbox-circle-line me-1"></i> Scanning has been locked and inventory ledger has been updated.
                            </div>
                        </div>
                        <div class="text-muted small mb-2 fw-semibold">Download Stock Taking Reports:</div>
                        <div class="d-flex justify-content-center gap-2 mt-2">
                            <a href="${baseUrl}/session/${currentSessionId}/export" class="btn btn-success">
                                <i class="ri ri-file-excel-line me-1"></i> Download Excel
                            </a>
                            <a href="${baseUrl}/session/${currentSessionId}/pdf" target="_blank" class="btn btn-danger">
                                <i class="ri ri-file-pdf-line me-1"></i> Download PDF
                            </a>
                        </div>
                    `,
                    icon: 'success',
                    showConfirmButton: true,
                    confirmButtonText: 'Done',
                    confirmButtonColor: '#696cff'
                });
            }).fail(function(xhr) {
                $btn.prop('disabled', false).html(originalBtnHtml);
                showScanAlert('alert-danger', escapeHtml(xhr.responseJSON?.message || 'Unable to finalize session.'));
                Swal.fire({
                    title: 'Finalization Failed',
                    text: xhr.responseJSON?.message || 'Unable to finalize session.',
                    icon: 'error'
                });
            });
        });
    });

    $('#start_new_btn').on('click', function() {
        const brandId = $('#filter_brand_id').val();
        if (!brandId) return;

        const brandText = $('#filter_brand_id option:selected').text().trim();

        Swal.fire({
            title: 'Start New Stock Taking?',
            html: `This will take a fresh system stock snapshot for <strong>${escapeHtml(brandText)}</strong> and start a new count session.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Start New Count',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#696cff'
        }).then((result) => {
            if (!result.isConfirmed) return;

            const $btn = $('#start_new_btn');
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Starting...');
            showScanAlert('alert-info', '<i class="ri ri-loader-4-line ri-spin me-1"></i> <strong>Taking stock snapshot...</strong> Please wait a moment while items are prepared.');

            loadBrandSession(() => {
                $btn.prop('disabled', false).html('<i class="ri ri-add-circle-line me-1"></i> Start New Count');
                showScanAlert('alert-success', '<i class="ri ri-checkbox-circle-line me-1"></i> <strong>New session started!</strong> Ready to scan barcodes.');
            }, true);
        });
    });

    let itemsDataTable = null;

    function initItemsDataTable() {
        if ($.fn.DataTable.isDataTable('#items_table')) {
            $('#items_table').DataTable().destroy();
            $('#items_table tbody').empty();
        }

        if (!currentSessionId) {
            $('#items_table tbody').html('<tr><td colspan="11" class="text-center py-4 text-muted">Please select a <strong>Brand</strong> above to load items and start scanning.</td></tr>');
            return;
        }

        itemsDataTable = $('#items_table').DataTable({
            processing: true,
            serverSide: true,
            searchDelay: 400,
            ordering: false,
            responsive: true,
            autoWidth: false,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, 250], [10, 25, 50, 100, 250]],
            ajax: {
                url: baseUrl + '/session/' + currentSessionId + '/items',
                type: 'GET',
                data: function (d) {
                    d.filter = $('#table_filter_select').val() || 'all';
                }
            },
            columns: [
                {
                    data: 'finished_item_code',
                    name: 'finished_item_code',
                    defaultContent: '-',
                    render: function (data, type, r) {
                        const displayName = r.finished_item_code || r.item_name || r.style_name || '-';
                        const sleeve = r.sleeve_type && r.sleeve_type !== '-' ? ' (' + escapeHtml(r.sleeve_type) + ')' : '';
                        return `${escapeHtml(displayName)}${sleeve}<br><small class="text-muted">Size: ${escapeHtml(r.size)}</small>`;
                    }
                },
                {
                    data: 'art_no',
                    name: 'art_no',
                    render: function (data) {
                        return escapeHtml(data || '-');
                    }
                },
                {
                    data: 'sku',
                    name: 'sku',
                    render: function (data, type, r) {
                        const master = r.master_barcode;
                        if (master && master !== data) {
                            return `<span class="fw-bold text-dark">${escapeHtml(master)}</span><br><small class="text-muted">${escapeHtml(data)}</small>`;
                        }
                        return `<span class="fw-bold">${escapeHtml(data || '-')}</span>`;
                    }
                },
                {
                    data: 'system_qty',
                    name: 'system_qty',
                    render: function (data, type, r) {
                        const sys = Number(data || 0);
                        const mv = Number(r.movement_qty || 0);
                        const mvHtml = Math.abs(mv) > 0.0001
                            ? `<br><small class="text-muted">(Snap: ${Number(r.snapshot_qty).toFixed(0)}, Move: ${mv > 0 ? '+' : ''}${mv.toFixed(0)})</small>`
                            : '';
                        return `${sys.toFixed(0)}${mvHtml}`;
                    }
                },
                {
                    data: 'open_qty',
                    name: 'open_qty',
                    render: function (data) {
                        const oq = Number(data || 0);
                        if (oq > 0.001) {
                            return `<span class="badge bg-label-info fw-bold fs-6">+${oq.toFixed(0)}</span>`;
                        }
                        return '<span class="text-muted">0</span>';
                    }
                },
                {
                    data: 'scanned_qty',
                    name: 'scanned_qty',
                    render: function (data, type, r) {
                        const totalScan = Number(data || 0);
                        const oq = Number(r.open_qty || 0);
                        const bScan = Number(r.barcode_scanned_qty !== undefined ? r.barcode_scanned_qty : (totalScan - oq));
                        if (oq > 0.001) {
                            return `<span class="fw-bold">${totalScan.toFixed(0)}</span><br><small class="text-muted">(Scan: ${bScan.toFixed(0)}, Open: +${oq.toFixed(0)})</small>`;
                        }
                        return `<span class="fw-bold">${totalScan.toFixed(0)}</span>`;
                    }
                },
                {
                    data: 'shortage_qty',
                    name: 'shortage_qty',
                    render: function (data) {
                        const sh = Number(data || 0);
                        if (sh > 0.001) {
                            return `<span class="badge bg-label-danger fw-bold fs-6">-${sh.toFixed(0)}</span>`;
                        }
                        return '<span class="text-muted">0</span>';
                    }
                },
                {
                    data: 'excess_qty',
                    name: 'excess_qty',
                    render: function (data) {
                        const ex = Number(data || 0);
                        if (ex > 0.001) {
                            return `<span class="badge bg-label-warning text-dark fw-bold fs-6">+${ex.toFixed(0)}</span>`;
                        }
                        return '<span class="text-muted">0</span>';
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function (data, type, r) {
                        const diff = Number(r.variance || 0);
                        if (data === 'completed') {
                            return '<span class="badge bg-success">Completed</span>';
                        } else if (data === 'excess') {
                            return '<span class="badge bg-warning text-dark">Excess (+' + Math.abs(diff).toFixed(0) + ')</span>';
                        } else if (data === 'in_progress') {
                            return '<span class="badge bg-info text-dark">In Progress</span>';
                        }
                        return '<span class="badge bg-secondary">Pending</span>';
                    }
                },
                {
                    data: 'store_location',
                    name: 'store_location',
                    render: function (data, type, r) {
                        if (data && data !== '-') {
                            const parts = String(data).split(',').map(s => s.trim()).filter(Boolean);
                            if (parts.length === 0) {
                                return '<span class="text-muted">-</span>';
                            }
                            if (parts.length <= 3) {
                                return parts.map(p => renderLocationBadge(p)).join('');
                            }
                            const firstThree = parts.slice(0, 3).map(p => renderLocationBadge(p)).join('');
                            const remainingCount = parts.length - 3;
                            const allLocsEscaped = escapeHtml(JSON.stringify(parts));
                            const skuLabel = escapeHtml((r.art_no ? r.art_no + ' (' + (r.sku || '') + ')' : (r.sku || '')));
                            const moreBtn = `<button type="button" class="btn btn-xs btn-outline-primary btn-show-more-locs px-2 py-0 align-middle mb-1" data-sku="${skuLabel}" data-locations="${allLocsEscaped}" title="Click to view all ${parts.length} locations" style="font-size: 0.72rem; line-height: 1.5; border-radius: 4px; font-weight: 600;">+${remainingCount} more</button>`;
                            return firstThree + moreBtn;
                        }
                        return '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: 'id',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function (data, type, r) {
                        const scan = Number(r.scanned_qty || 0);
                        if (!currentSessionFinalized && scan > 0) {
                            return `
                                <div class="d-inline-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-xs btn-outline-warning btn-item-decrement" data-id="${r.id}" data-sku="${escapeHtml(r.sku)}" title="Reduce scanned qty by 1 (-1)" style="padding: 1px 7px; font-weight: bold; font-size: 0.75rem;">
                                        <i class="ri ri-subtract-line"></i> 1
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-danger btn-item-reset" data-id="${r.id}" data-sku="${escapeHtml(r.sku)}" data-scan="${scan.toFixed(0)}" title="Reset scanned count to 0" style="padding: 1px 6px; font-size: 0.75rem;">
                                        <i class="ri ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            `;
                        }
                        return '<span class="text-muted small">-</span>';
                    }
                }
            ],
            rowCallback: function (row, data) {
                if (data.status === 'completed') {
                    $(row).addClass('table-success');
                } else if (data.status === 'excess') {
                    $(row).addClass('table-warning');
                }
            },
            language: {
                emptyTable: 'No items found for this filter.',
                processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div> Loading items...'
            }
        });
    }

    function loadTable(page) {
        if (!itemsDataTable) {
            initItemsDataTable();
        } else {
            const expectedUrl = baseUrl + '/session/' + currentSessionId + '/items';
            if (itemsDataTable.ajax.url() !== expectedUrl) {
                itemsDataTable.ajax.url(expectedUrl).load();
            } else {
                itemsDataTable.ajax.reload(null, page === 1);
            }
        }
    }

    $('#btn_apply_filter').on('click', function(e) {
        e.preventDefault();
        loadBrandSession(null, false);
    });
    $('#filter_brand_id, #filter_store_type_id, #filter_warehouse_id').on('change', () => loadBrandSession(null, false));
    $('#filter_store_location_id').on('change', () => refocusScanner());

    $('#btn_table_filter').on('click', function(e) {
        e.preventDefault();
        loadTable(1);
    });
    $('#btn_table_reset').on('click', function(e) {
        e.preventDefault();
        $('#table_filter_select').val('all').trigger('change');
        if (itemsDataTable) {
            itemsDataTable.search('').page(0).ajax.reload();
        } else {
            loadTable(1);
        }
    });
    $('#table_filter_select').on('change', () => loadTable(1));
    $(document).on('click', '.stat-card-filter', function() {
        const targetFilter = $(this).data('filter');
        if (!currentSessionId || !targetFilter) return;
        $('#table_filter_select').val(targetFilter).trigger('change');
        $('html, body').animate({ scrollTop: $('#items_table').closest('.card').offset().top - 70 }, 200);
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

    function performUndo(params) {
        if (!currentSessionId || currentSessionFinalized) return;

        const postData = Object.assign({ _token: csrfToken }, params);

        $.ajax({
            url: baseUrl + '/session/' + currentSessionId + '/undo',
            type: 'POST',
            data: postData,
            success: function(res) {
                playBeep(440, 0.15, 'sine');
                updateStats(res.stats);
                showScanAlert('alert-info', `<i class="ri ri-arrow-go-back-line me-1"></i> <strong>${escapeHtml(res.message)}</strong>`);
                lastScannedLogId = null;
                loadTable(currentPage);
                refocusScanner();
            },
            error: function(xhr) {
                playBeep(210, 0.3, 'sawtooth');
                const msg = xhr.responseJSON?.message || 'Unable to undo scan.';
                showScanAlert('alert-danger', `<i class="ri ri-error-warning-line me-1"></i> ${escapeHtml(msg)}`);
                refocusScanner();
            }
        });
    }

    $(document).on('click', '.btn-undo-last-scan', function() {
        const logId = $(this).data('log-id');
        performUndo({ log_id: logId });
    });

    $(document).on('click', '.btn-item-decrement', function() {
        const itemId = $(this).data('id');
        performUndo({ item_id: itemId, action: 'decrement' });
    });

    $(document).on('click', '.btn-item-reset', function() {
        const itemId = $(this).data('id');
        const sku = $(this).data('sku');
        const currentScan = $(this).data('scan');
        Swal.fire({
            title: 'Reset Scanned Qty?',
            html: `Are you sure you want to reset scanned count for <strong>${escapeHtml(sku)}</strong> (current: <strong>${currentScan}</strong>) back to <strong>0</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Reset to 0',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                performUndo({ item_id: itemId, action: 'reset' });
            }
        });
    });

    $('#btn_reset_stock_taking').on('click', function(e) {
        e.preventDefault();
        const currentScanned = parseInt($('#stat_scanned').text().replace(/,/g, '')) || 0;

        if (currentSessionId && currentScanned > 0 && !currentSessionFinalized) {
            Swal.fire({
                title: 'Reset Stock Taking',
                html: `This session has <strong>${currentScanned}</strong> scanned pieces in progress.<br><br>What would you like to reset?`,
                icon: 'question',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonColor: '#696cff',
                denyButtonColor: '#ff3e1d',
                cancelButtonColor: '#8592a3',
                confirmButtonText: '<i class="ri ri-refresh-line me-1"></i> Clear Screen (New Brand)',
                denyButtonText: '<i class="ri ri-delete-bin-line me-1"></i> Reset Scanned Counts to 0',
                cancelButtonText: 'Cancel'
            }).then((res) => {
                if (res.isConfirmed) {
                    window.location.href = "{{ url('fg_stock_taking') }}";
                } else if (res.isDenied) {
                    Swal.fire({
                        title: 'Confirm Reset Scanned Counts?',
                        text: 'This will reset all scanned quantities in this active session back to 0. Are you sure?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ff3e1d',
                        cancelButtonColor: '#8592a3',
                        confirmButtonText: 'Yes, Reset All to 0',
                        cancelButtonText: 'Cancel'
                    }).then((conf) => {
                        if (conf.isConfirmed) {
                            performUndo({ action: 'reset_all' });
                        }
                    });
                }
            });
        } else {
            window.location.href = "{{ url('fg_stock_taking') }}";

        }
    });

    $(document).on('click', '.btn-log-delete', function() {
        const logId = $(this).data('log-id');
        Swal.fire({
            title: 'Delete this scan entry?',
            text: 'This will remove this scan entry and adjust the counted stock.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                performUndo({ log_id: logId });
            }
        });
    });

    $(document).on('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === 'z' || e.key === 'Z') && !e.shiftKey) {
            const activeTag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
            if (activeTag === 'input' && document.activeElement.id !== 'barcode_input' && $(document.activeElement).val().length > 0) {
                return;
            }
            if (currentSessionId && !currentSessionFinalized) {
                e.preventDefault();
                performUndo(lastScannedLogId ? { log_id: lastScannedLogId } : {});
            }
        }
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

            if (res.store_counts !== undefined) {
                updateStoreDropdownCounts(res.store_counts, res.total_brand_qty || res.stats?.total_system_qty);
            }

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

    $('#btn_open_stock_entry').on('click', function(e) {
        e.preventDefault();
        const brandId = $('#filter_brand_id').val();
        const storeTypeId = $('#filter_store_type_id').val();
        const warehouseId = $('#filter_warehouse_id').val();
        const storeLocationId = $('#filter_store_location_id').val();
        const params = new URLSearchParams();
        if (brandId) params.append('brand_id', brandId);
        if (storeTypeId) params.append('store_type_id', storeTypeId);
        if (warehouseId) params.append('warehouse_id', warehouseId);
        if (storeLocationId) params.append('store_location_id', storeLocationId);
        const query = params.toString();
        window.location.href = '{{ url("fg_stock_taking/open-stock-entry") }}' + (query ? '?' + query : '');
    });

    function parseLocationAndQty(raw) {
        if (!raw) return { loc: '', qty: null };
        let str = String(raw).trim();
        let qty = null;
        let loc = str;

        // Matches "LOC (X pcs)" or "LOC (X)"
        let m = str.match(/^(.+?)\s*\(([\d.]+)\s*(?:pcs)?\)$/i);
        if (m) {
            loc = m[1].trim();
            qty = m[2];
        } else {
            // Matches "LOC - X" or "LOC: X"
            m = str.match(/^(.+?)\s+[-:]\s+([\d.]+)$/);
            if (m) {
                loc = m[1].trim();
                qty = m[2];
            }
        }
        return { loc: loc, qty: qty };
    }

    function renderLocationBadge(p) {
        const item = parseLocationAndQty(p);
        if (!item.loc) return '';
        if (item.qty !== null && item.qty !== undefined) {
            return `<span class="badge bg-light text-dark border me-1 mb-1 d-inline-flex align-items-center gap-1" style="font-weight: 500; font-size: 0.78rem;">
                <i class="ri ri-map-pin-line text-muted"></i>
                <span>${escapeHtml(item.loc)}</span>
                <span class="badge bg-primary text-white rounded-pill px-1" style="font-size: 0.68rem; font-weight: 600;">${escapeHtml(item.qty)} pcs</span>
            </span>`;
        }
        return `<span class="badge bg-light text-dark border me-1 mb-1" style="font-weight: 500; font-size: 0.78rem;"><i class="ri ri-map-pin-line text-muted me-1"></i>${escapeHtml(item.loc)}</span>`;
    }

    function renderModalLocationItem(p) {
        const item = parseLocationAndQty(p);
        if (!item.loc) return '';
        if (item.qty !== null && item.qty !== undefined) {
            return `<div class="d-flex align-items-center justify-content-between p-2 border rounded bg-white w-100 mb-2 shadow-sm">
                <div class="d-flex align-items-center gap-2">
                    <i class="ri ri-map-pin-2-fill text-primary fs-5"></i>
                    <span class="fw-semibold text-dark fs-6">${escapeHtml(item.loc)}</span>
                </div>
                <span class="badge bg-primary fs-6 px-3 py-1 rounded-pill">${escapeHtml(item.qty)} pcs</span>
            </div>`;
        }
        return `<div class="d-flex align-items-center justify-content-between p-2 border rounded bg-white w-100 mb-2 shadow-sm">
            <div class="d-flex align-items-center gap-2">
                <i class="ri ri-map-pin-2-fill text-primary fs-5"></i>
                <span class="fw-semibold text-dark fs-6">${escapeHtml(item.loc)}</span>
            </div>
        </div>`;
    }

    $(document).on('click', '.btn-show-more-locs', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const sku = $(this).data('sku') || '-';
        let locs = [];
        try {
            locs = JSON.parse($(this).attr('data-locations') || '[]');
        } catch (err) {
            locs = [];
        }

        $('#modal_loc_sku_display').text(sku);
        $('#modal_loc_total_badge').text(locs.length + (locs.length === 1 ? ' Location' : ' Locations'));

        const badgesHtml = locs.map(l => renderModalLocationItem(l)).join('');

        $('#modal_loc_badges_container').html(badgesHtml || '<span class="text-muted">No locations found</span>');

        const locModalEl = document.getElementById('modalStoreLocations');
        if (locModalEl) {
            const modalObj = bootstrap.Modal.getOrCreateInstance(locModalEl);
            modalObj.show();
        }
    });

    $('#modalStoreLocations').on('hidden.bs.modal', function () {
        refocusScanner();
    });
});
</script>
@endsection

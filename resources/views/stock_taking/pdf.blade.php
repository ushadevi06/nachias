<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ !empty($isSummaryOnly) ? 'Stock Audit Summary' : 'Stock Taking Report' }} - {{ $session->session_no }}</title>
    <style>
        @page {
            margin: {{ !empty($isSummaryOnly) ? '25px 30px 25px 30px' : '20px 20px 25px 20px' }};
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: {{ !empty($isSummaryOnly) ? '9.5px' : '8.5px' }};
            color: #1e293b;
            margin: 0;
            padding: 0;
            line-height: 1.3;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: {{ !empty($isSummaryOnly) ? '8px' : '6px' }};
            margin-bottom: {{ !empty($isSummaryOnly) ? '10px' : '8px' }};
        }
        .company-name {
            font-size: {{ !empty($isSummaryOnly) ? '18px' : '16px' }};
            font-weight: bold;
            color: #312e81;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .report-title {
            font-size: {{ !empty($isSummaryOnly) ? '15px' : '13px' }};
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            text-align: right;
        }
        .sub-text {
            font-size: {{ !empty($isSummaryOnly) ? '9px' : '8px' }};
            color: #64748b;
        }
        .meta-table {
            width: 100%;
            margin-bottom: {{ !empty($isSummaryOnly) ? '12px' : '8px' }};
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: {{ !empty($isSummaryOnly) ? '6px 10px' : '4px 6px' }};
            font-size: {{ !empty($isSummaryOnly) ? '10px' : '8.5px' }};
            border: 1px solid #e2e8f0;
        }
        .meta-label {
            font-weight: bold;
            color: #475569;
            width: 110px;
            background-color: #f1f5f9;
        }
        .meta-val {
            color: #0f172a;
            font-weight: 600;
        }
        .section-header {
            font-size: {{ !empty($isSummaryOnly) ? '13px' : '10.5px' }};
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin: {{ !empty($isSummaryOnly) ? '12px 0 8px 0' : '8px 0 4px 0' }};
            padding-bottom: 4px;
            border-bottom: 2px solid #4f46e5;
        }
        /* Audit Reconciliation Matrix Table */
        .audit-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: {{ !empty($isSummaryOnly) ? '14px' : '8px' }};
        }
        .audit-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: {{ !empty($isSummaryOnly) ? '11.5px' : '8px' }};
            padding: {{ !empty($isSummaryOnly) ? '8px 10px' : '5px 6px' }};
            text-align: left;
            border: 1px solid #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .audit-table td {
            border: 1px solid #cbd5e1;
            padding: {{ !empty($isSummaryOnly) ? '7.5px 10px' : '4px 6px' }};
            font-size: {{ !empty($isSummaryOnly) ? '12px' : '8.5px' }};
            color: #0f172a;
            line-height: 1.35;
        }
        .audit-table .sub-text {
            font-size: {{ !empty($isSummaryOnly) ? '10px' : '8px' }};
            color: #64748b;
        }
        .audit-table td strong {
            font-size: {{ !empty($isSummaryOnly) ? '12.5px' : '8.5px' }};
        }
        .audit-row-highlight {
            background-color: #f8fafc;
            font-weight: bold;
        }
        .audit-row-target {
            background-color: #eff6ff;
            font-weight: bold;
            color: #1e40af;
        }
        .audit-row-scanned {
            background-color: #f0fdf4;
            font-weight: bold;
            color: #166534;
        }
        .audit-row-deduct {
            background-color: #fef2f2;
            color: #991b1b;
            font-weight: bold;
        }
        .audit-row-add {
            background-color: #fffbeb;
            color: #854d0e;
            font-weight: bold;
        }
        .audit-row-final {
            background-color: #ecfdf5;
            color: #065f46;
            font-weight: bold;
            font-size: {{ !empty($isSummaryOnly) ? '13px' : '9.5px' }};
            border-top: 2px solid #059669 !important;
            border-bottom: 2px solid #059669 !important;
        }
        .audit-row-final td {
            font-size: {{ !empty($isSummaryOnly) ? '13px' : '9.5px' }} !important;
            padding: {{ !empty($isSummaryOnly) ? '9px 10px' : '5px 6px' }};
        }
        .kpi-table {
            width: 100%;
            margin-bottom: {{ !empty($isSummaryOnly) ? '14px' : '8px' }};
            border-collapse: collapse;
        }
        .kpi-card {
            border: 1px solid #cbd5e1;
            padding: {{ !empty($isSummaryOnly) ? '8px 6px' : '4px 6px' }};
            text-align: center;
            background-color: #ffffff;
            width: 16.66%;
        }
        .kpi-title {
            font-size: {{ !empty($isSummaryOnly) ? '9px' : '7.5px' }};
            text-transform: uppercase;
            font-weight: bold;
            color: #64748b;
            margin-bottom: 2px;
        }
        .kpi-num {
            font-size: {{ !empty($isSummaryOnly) ? '15px' : '12px' }};
            font-weight: bold;
            color: #0f172a;
        }
        .text-danger { color: #dc2626 !important; }
        .text-warning { color: #d97706 !important; }
        .text-success { color: #16a34a !important; }
        .text-primary { color: #4f46e5 !important; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        /* Detailed SKU Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .data-table th {
            background-color: #334155;
            color: #ffffff;
            font-weight: bold;
            font-size: 7.5px;
            padding: 4px 3px;
            text-align: center;
            border: 1px solid #1e293b;
            text-transform: uppercase;
        }
        .data-table td {
            border: 1px solid #cbd5e1;
            padding: 3px 4px;
            font-size: 7.5px;
            color: #1e293b;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .data-table tr.row-excess td {
            background-color: #fffbeb;
        }
        .data-table tr.row-shortage td {
            background-color: #fef2f2;
        }
        .total-row td {
            background-color: #e2e8f0 !important;
            font-weight: bold;
            font-size: 8px;
            border-top: 2px solid #64748b;
        }
        .badge {
            display: inline-block;
            padding: {{ !empty($isSummaryOnly) ? '3px 7px' : '1px 3px' }};
            font-size: {{ !empty($isSummaryOnly) ? '9.5px' : '7px' }};
            font-weight: bold;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .badge-completed { background-color: #dcfce7; color: #15803d; }
        .badge-excess { background-color: #fef3c7; color: #b45309; }
        .badge-shortage { background-color: #fee2e2; color: #b91c1c; }
        .cert-box {
            background-color: #f8fafc;
            border: 1px dashed #94a3b8;
            padding: {{ !empty($isSummaryOnly) ? '10px 14px' : '8px 10px' }};
            margin: {{ !empty($isSummaryOnly) ? '12px 0' : '8px 0' }};
            font-size: {{ !empty($isSummaryOnly) ? '10px' : '8px' }};
            color: #334155;
            line-height: 1.45;
        }
        .signatures-table {
            width: 100%;
            margin-top: {{ !empty($isSummaryOnly) ? '60px' : '25px' }};
            page-break-inside: avoid;
        }
        .signature-box {
            text-align: center;
            width: 30%;
            padding-top: {{ !empty($isSummaryOnly) ? '8px' : '6px' }};
            border-top: 1.5px dashed #64748b;
            font-size: {{ !empty($isSummaryOnly) ? '10.5px' : '8px' }};
            font-weight: bold;
            color: #1e293b;
            line-height: 1.4;
        }
    </style>
</head>
<body>
    @php
        $stats = $state['stats'] ?? [];
        $rows = $state['rows'] ?? [];

        $initialSysQty = floatval($stats['total_snapshot_qty'] ?? $stats['total_system_qty'] ?? 0);
        $initialSysVal = floatval($stats['total_snapshot_value'] ?? $stats['total_system_value'] ?? 0);
        $openQty = floatval($stats['total_open_qty'] ?? 0);
        $openVal = floatval($stats['total_open_value'] ?? 0);
        $openSkus = (int) ($stats['open_skus'] ?? 0);
        $targetSysQty = floatval($stats['total_system_qty'] ?? 0);
        $targetSysVal = floatval($stats['total_system_value'] ?? 0);
        $scannedQty = floatval($stats['total_scanned_qty'] ?? 0);
        $scannedVal = floatval($stats['total_scanned_value'] ?? 0);
        $shortageQty = floatval($stats['shortage_qty'] ?? 0);
        $shortageVal = floatval($stats['shortage_value'] ?? 0);
        $shortageSkus = (int) ($stats['shortage_skus'] ?? 0);
        $excessQty = floatval($stats['excess_qty'] ?? 0);
        $excessVal = floatval($stats['excess_value'] ?? 0);
        $excessSkus = (int) ($stats['excess_skus'] ?? 0);
        $matchedSkus = (int) ($stats['matched_skus'] ?? 0);
        $totalSkus = (int) ($stats['total_skus'] ?? count($rows));
        $touchedSkus = (int) ($stats['touched_skus'] ?? 0);
    @endphp

    <table class="header-table">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                <div class="company-name">{{ $setting->name ?? 'NACHIAS' }}</div>
                <div class="sub-text">{{ $setting->address ?? '' }} {{ $setting->city?->city_name ?? '' }}</div>
                <div class="sub-text">Phone: {{ $setting->phone ?? '-' }} | Email: {{ $setting->email ?? '-' }}</div>
            </td>
            <td style="width: 45%; vertical-align: top; text-align: right;">
                <div class="report-title">{{ !empty($isSummaryOnly) ? 'Stock Audit & Reconciliation Summary' : 'Finished Goods Stock Taking Report' }}</div>
                <div class="sub-text">Session Ref: <strong>{{ $session->session_no }}</strong></div>
                <div class="sub-text">Generated On: <strong>{{ date('d-m-Y h:i A') }}</strong></div>
            </td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Brand Name:</td>
            <td class="meta-val" style="width: 25%;">{{ $session->brand?->brand_name ?? '-' }}</td>
            <td class="meta-label">Warehouse:</td>
            <td class="meta-val" style="width: 25%;">{{ $session->warehouse?->warehouse_name ?? 'All Warehouses' }}</td>
            <td class="meta-label">Status:</td>
            <td class="meta-val" style="width: 25%;">
                @if($session->status === 'Completed')
                    <span style="color: #16a34a; font-weight: bold;">Finalized (Stock Updated)</span>
                @else
                    <span style="color: #4f46e5; font-weight: bold;">In Progress</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="meta-label">Store / Section:</td>
            <td class="meta-val">{{ $session->storeType?->store_type_name ?? 'All Stores' }}</td>
            <td class="meta-label">Count Date:</td>
            <td class="meta-val">{{ $session->started_at ? $session->started_at->format('d-m-Y h:i A') : '-' }}</td>
            <td class="meta-label">Counted By:</td>
            <td class="meta-val">{{ $session->starter?->name ?? '-' }}</td>
        </tr>
        @if($session->completed_at)
        <tr>
            <td class="meta-label">Finalized Date:</td>
            <td class="meta-val">{{ $session->completed_at->format('d-m-Y h:i A') }}</td>
            <td class="meta-label">Finalized By:</td>
            <td class="meta-val" colspan="3">{{ $session->completer?->name ?? '-' }}</td>
        </tr>
        @endif
    </table>

    <!-- Simple, Easy-to-Understand Stock Reconciliation & Adjustment Summary -->
    <div class="section-header">Stock Verification Summary & Final Adjustments</div>
    <table class="audit-table">
        <thead>
            <tr>
                <th style="width: 34%;">Particulars / Details</th>
                <th style="width: 13%; text-align: center;">Total Items</th>
                <th style="width: 16%; text-align: right;">Quantity (Pcs)</th>
                <th style="width: 17%; text-align: right;">Value (Rs.)</th>
                <th style="width: 20%;">Action / Remarks</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>1. System Stock (Before Count)</strong></td>
                <td class="text-center">{{ number_format($totalSkus) }} SKUs</td>
                <td class="text-end fw-bold">{{ number_format($initialSysQty, 0) }} pcs</td>
                <td class="text-end">Rs. {{ number_format($initialSysVal, 2) }}</td>
                <td><span style="color: #64748b; font-weight: 500;">Initial stock in computer</span></td>
            </tr>
            <tr class="audit-row-highlight">
                <td style="color: #2563eb;"><strong>2. (+) Open Stock Added</strong></td>
                <td class="text-center" style="color: #2563eb;">{{ number_format($openSkus) }} SKUs</td>
                <td class="text-end fw-bold" style="color: #2563eb;">+ {{ number_format($openQty, 0) }} pcs</td>
                <td class="text-end" style="color: #2563eb;">+ Rs. {{ number_format($openVal, 2) }}</td>
                <td><span style="color: #2563eb; font-weight: 600;">Added from Excel / White Stock</span></td>
            </tr>
            <tr class="audit-row-target">
                <td><strong>3. (=) Total Expected Stock</strong></td>
                <td class="text-center">{{ number_format($totalSkus) }} SKUs</td>
                <td class="text-end fw-bold">{{ number_format($targetSysQty, 0) }} pcs</td>
                <td class="text-end">Rs. {{ number_format($targetSysVal, 2) }}</td>
                <td><span style="font-weight: bold;">Expected stock to be in store</span></td>
            </tr>
            <tr class="audit-row-scanned">
                <td><strong>4. Physical Scanned Stock</strong></td>
                <td class="text-center">{{ number_format($touchedSkus) }} SKUs</td>
                <td class="text-end fw-bold">{{ number_format($scannedQty, 0) }} pcs</td>
                <td class="text-end">Rs. {{ number_format($scannedVal, 2) }}</td>
                <td><span style="font-weight: bold;">Items scanned on racks</span></td>
            </tr>
            <tr class="audit-row-deduct">
                <td><strong>5. (-) Shortage (Missing / Less Items)</strong></td>
                <td class="text-center">{{ number_format($shortageSkus) }} SKUs</td>
                <td class="text-end fw-bold">- {{ number_format($shortageQty, 0) }} pcs</td>
                <td class="text-end">- Rs. {{ number_format($shortageVal, 2) }}</td>
                <td><span class="badge badge-shortage">Deducted (Removed from stock)</span></td>
            </tr>
            <tr class="audit-row-add">
                <td><strong>6. (+) Excess (Extra Found Items)</strong></td>
                <td class="text-center">{{ number_format($excessSkus) }} SKUs</td>
                <td class="text-end fw-bold">+ {{ number_format($excessQty, 0) }} pcs</td>
                <td class="text-end">+ Rs. {{ number_format($excessVal, 2) }}</td>
                <td><span class="badge badge-excess">Added (Entered into stock)</span></td>
            </tr>
            <tr class="audit-row-final">
                <td><strong>7. (=) Final Shop / Store Stock</strong></td>
                <td class="text-center">{{ number_format($touchedSkus) }} SKUs</td>
                <td class="text-end fw-bold">{{ number_format($scannedQty, 0) }} pcs</td>
                <td class="text-end">Rs. {{ number_format($scannedVal, 2) }}</td>
                <td><span class="badge badge-completed">New Final Stock in System</span></td>
            </tr>
        </tbody>
    </table>

    <!-- 6 Simple KPI Cards -->
    <table class="kpi-table">
        <tr>
            <td class="kpi-card">
                <div class="kpi-title">Expected Stock</div>
                <div class="kpi-num">{{ number_format($targetSysQty, 0) }}</div>
                <div class="sub-text">{{ $totalSkus }} Total Items</div>
            </td>
            <td class="kpi-card">
                <div class="kpi-title">Total Scanned</div>
                <div class="kpi-num text-primary">{{ number_format($scannedQty, 0) }}</div>
                <div class="sub-text">{{ $touchedSkus }} Scanned Items</div>
            </td>
            <td class="kpi-card">
                <div class="kpi-title">Matched Items</div>
                <div class="kpi-num text-success">{{ $matchedSkus }}</div>
                <div class="sub-text">100% Match</div>
            </td>
            <td class="kpi-card">
                <div class="kpi-title">Shortage (Less)</div>
                <div class="kpi-num text-danger">{{ number_format($shortageQty, 0) }} pcs</div>
                <div class="sub-text">{{ $shortageSkus }} Items (-Rs.{{ number_format($shortageVal, 0) }})</div>
            </td>
            <td class="kpi-card">
                <div class="kpi-title">Excess (Extra)</div>
                <div class="kpi-num text-warning">{{ number_format($excessQty, 0) }} pcs</div>
                <div class="sub-text">{{ $excessSkus }} Items (+Rs.{{ number_format($excessVal, 0) }})</div>
            </td>
            <td class="kpi-card">
                <div class="kpi-title">Count Accuracy</div>
                <div class="kpi-num">{{ number_format($stats['coverage_pct'] ?? 0, 1) }}%</div>
                <div class="sub-text">Accuracy %</div>
            </td>
        </tr>
    </table>

    @if(!empty($isSummaryOnly))
        <!-- Simple, Clear Audit Verification Statement -->
        <div class="cert-box">
            <strong>STOCK VERIFICATION CERTIFICATE:</strong><br>
            This confirms that physical stock counting was done for Brand <strong>{{ $session->brand?->brand_name ?? 'Finished Goods' }}</strong> at <strong>{{ $session->warehouse?->warehouse_name ?? 'All Warehouses' }}</strong>.<br>
            &bull; <strong>Expected Stock:</strong> {{ number_format($targetSysQty, 0) }} pcs (System: {{ number_format($initialSysQty, 0) }} pcs + Open Stock: {{ number_format($openQty, 0) }} pcs)<br>
            &bull; <strong>Physically Scanned:</strong> {{ number_format($scannedQty, 0) }} pcs<br>
            &bull; <strong>Shortage Deducted:</strong> -{{ number_format($shortageQty, 0) }} pcs (Rs. {{ number_format($shortageVal, 2) }}) removed from system inventory.<br>
            &bull; <strong>Excess Added:</strong> +{{ number_format($excessQty, 0) }} pcs (Rs. {{ number_format($excessVal, 2) }}) added to system inventory.<br>
            &bull; <strong>Final Confirmed Stock:</strong> {{ number_format($scannedQty, 0) }} pcs (This is now the active stock balance in system).
        </div>
    @else
        @php
            $displayRows = $rows;
            $truncatedNotice = false;
            if (count($rows) > 100) {
                $displayRows = array_slice($rows, 0, 100);
                $truncatedNotice = true;
            }
        @endphp
        <!-- Full Detailed SKU Breakdown Table (Landscape) -->
        <div class="section-header">
            Detailed SKU-by-SKU Discrepancy Breakdown
            @if($truncatedNotice)
                <span style="font-size: 7.5px; font-weight: normal; color: #dc2626; text-transform: none; float: right;">
                    (Showing first 100 items. For all {{ number_format(count($rows)) }} items, download Excel Export)
                </span>
            @endif
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 20px;">#</th>
                    <th style="text-align: left;">Item Name / Style</th>
                    <th style="text-align: left; width: 65px;">Art No</th>
                    <th style="width: 85px;">SKU / Barcode</th>
                    <th style="width: 25px;">Size</th>
                    <th style="width: 30px;">Sleeve</th>
                    <th style="width: 40px; text-align: right;">Rate (Rs.)</th>
                    <th style="width: 35px; text-align: right;">System</th>
                    <th style="width: 35px; text-align: right;">Open</th>
                    <th style="width: 40px; text-align: right;">Scanned</th>
                    <th style="width: 35px; text-align: right;">Shortage</th>
                    <th style="width: 35px; text-align: right;">Excess</th>
                    <th style="width: 55px;">Status</th>
                    <th style="width: 80px;">Location</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $tSys = 0; $tOpen = 0; $tScan = 0; $tShort = 0; $tExc = 0;
                @endphp
                @forelse($displayRows as $idx => $entry)
                    @php
                        $item = $entry['model'];
                        $calc = $entry['calc'];
                        $sys = floatval($calc['system_qty'] ?? 0);
                        $open = floatval($calc['open_qty'] ?? 0);
                        $scan = floatval($calc['scanned_qty'] ?? 0);
                        $shortage = floatval($calc['shortage_qty'] ?? 0);
                        $excess = floatval($calc['excess_qty'] ?? 0);

                        $tSys += $sys;
                        $tOpen += $open;
                        $tScan += $scan;
                        $tShort += $shortage;
                        $tExc += $excess;

                        $locDisplay = '-';
                        if ($scan > 0.0001) {
                            $locDisplay = $scannedLocMap[$item->id] ?? ($item->storeLocation?->store_location ? ($item->storeLocation->store_location . ' (' . (int)$scan . ')') : '-');
                        }

                        $rowClass = '';
                        if ($excess > 0.001) {
                            $rowClass = 'row-excess';
                        } elseif ($shortage > 0.001) {
                            $rowClass = 'row-shortage';
                        }

                        $masterBarcode = \App\Http\Controllers\FgStockTakingController::resolveMasterBarcode($item->art_no, $item->size, $item->sleeve_type, (int) $session->brand_id) ?: $item->sku;
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ $item->finished_item_code ?: ($item->style_name ?: '-') }}</td>
                        <td><strong>{{ $item->art_no ?: '-' }}</strong></td>
                        <td class="text-center font-monospace">
                            <strong>{{ $masterBarcode }}</strong>
                        </td>
                        <td class="text-center">{{ $item->size ?: '-' }}</td>
                        <td class="text-center">{{ $item->sleeve_type ?: '-' }}</td>
                        <td class="text-end">{{ number_format(floatval($item->rate), 2) }}</td>
                        <td class="text-end fw-bold">{{ number_format($sys, 0) }}</td>
                        <td class="text-end text-primary">{{ $open > 0 ? ('+' . number_format($open, 0)) : '0' }}</td>
                        <td class="text-end fw-bold">{{ number_format($scan, 0) }}</td>
                        <td class="text-end text-danger fw-bold">{{ $shortage > 0 ? ('-' . number_format($shortage, 0)) : '0' }}</td>
                        <td class="text-end text-warning fw-bold">{{ $excess > 0 ? ('+' . number_format($excess, 0)) : '0' }}</td>
                        <td class="text-center">
                            @if($calc['status'] === 'completed')
                                <span class="badge badge-completed">Matched</span>
                            @elseif($calc['status'] === 'excess')
                                <span class="badge badge-excess">Excess</span>
                            @else
                                <span class="badge badge-shortage">Shortage</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $locDisplay }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" class="text-center" style="padding: 15px; color: #64748b;">No items recorded in this stock taking session.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="7" class="text-end">TOTAL ({{ count($rows) }} SKUs):</td>
                    <td class="text-end">{{ number_format($tSys, 0) }}</td>
                    <td class="text-end text-primary">{{ number_format($tOpen, 0) }}</td>
                    <td class="text-end">{{ number_format($tScan, 0) }}</td>
                    <td class="text-end text-danger">{{ number_format($tShort, 0) }}</td>
                    <td class="text-end text-warning">{{ number_format($tExc, 0) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    @endif

    <!-- Official Signatures Table -->
    <table class="signatures-table">
        <tr>
            <td style="width: 5%;"></td>
            <td class="signature-box">
                Counted By<br>
                <span style="font-weight: normal; color: #64748b;">{{ $session->starter?->name ?? 'Physical Counter' }}</span><br>
                <span style="font-size: {{ !empty($isSummaryOnly) ? '8.5px' : '7px' }}; color: #94a3b8;">Signature & Date</span>
            </td>
            <td style="width: 10%;"></td>
            <td class="signature-box">
                Verified By (Store In-Charge)<br>
                <span style="font-weight: normal; color: #64748b;">{{ $session->completer?->name ?? 'Store Head' }}</span><br>
                <span style="font-size: {{ !empty($isSummaryOnly) ? '8.5px' : '7px' }}; color: #94a3b8;">Signature & Date</span>
            </td>
            <td style="width: 10%;"></td>
            <td class="signature-box">
                Approved By<br>
                <span style="font-weight: normal; color: #64748b;">Managing Director / GM</span><br>
                <span style="font-size: {{ !empty($isSummaryOnly) ? '8.5px' : '7px' }}; color: #94a3b8;">Signature & Date</span>
            </td>
            <td style="width: 5%;"></td>
        </tr>
    </table>
</body>
</html>

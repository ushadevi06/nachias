<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Stock Taking Report - {{ $session->session_no }}</title>
    <style>
        @page {
            margin: 25px 25px 35px 25px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9px;
            color: #2b2f38;
            margin: 0;
            padding: 0;
            line-height: 1.25;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #5a5fee;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #383b8a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .report-title {
            font-size: 13px;
            font-weight: bold;
            color: #1f2937;
            text-transform: uppercase;
            text-align: right;
        }
        .sub-text {
            font-size: 8px;
            color: #6b7280;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 8px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 4px 6px;
            font-size: 8.5px;
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
        .kpi-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }
        .kpi-card {
            border: 1px solid #cbd5e1;
            padding: 5px;
            text-align: center;
            background-color: #ffffff;
            width: 16.66%;
        }
        .kpi-title {
            font-size: 8px;
            text-transform: uppercase;
            font-weight: bold;
            color: #64748b;
            margin-bottom: 2px;
        }
        .kpi-num {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
        }
        .text-danger { color: #dc2626 !important; }
        .text-warning { color: #d97706 !important; }
        .text-success { color: #16a34a !important; }
        .text-primary { color: #4f46e5 !important; }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .data-table th {
            background-color: #475569;
            color: #ffffff;
            font-weight: bold;
            font-size: 8px;
            padding: 4px 3px;
            text-align: center;
            border: 1px solid #334155;
            text-transform: uppercase;
        }
        .data-table td {
            border: 1px solid #cbd5e1;
            padding: 3px 4px;
            font-size: 8px;
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
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .total-row td {
            background-color: #e2e8f0 !important;
            font-weight: bold;
            font-size: 8.5px;
            border-top: 2px solid #64748b;
        }
        .badge {
            display: inline-block;
            padding: 1px 4px;
            font-size: 7.5px;
            font-weight: bold;
            border-radius: 2px;
            text-transform: uppercase;
        }
        .badge-completed { background-color: #dcfce7; color: #15803d; }
        .badge-excess { background-color: #fef3c7; color: #b45309; }
        .badge-shortage { background-color: #fee2e2; color: #b91c1c; }
        .signatures-table {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .signature-box {
            text-align: center;
            width: 33.33%;
            padding-top: 35px;
            border-top: 1px dashed #94a3b8;
            font-size: 8.5px;
            font-weight: bold;
            color: #334155;
        }
    </style>
</head>
<body>
    @php
        $stats = $state['stats'] ?? [];
        $rows = $state['rows'] ?? [];
        $totalSys = 0;
        $totalOpen = 0;
        $totalScan = 0;
        $totalShortage = 0;
        $totalExcess = 0;
    @endphp

    <table class="header-table">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                <div class="company-name">{{ $setting->name ?? 'NACHIAS' }}</div>
                <div class="sub-text">{{ $setting->address ?? '' }} {{ $setting->city?->city_name ?? '' }}</div>
                <div class="sub-text">Phone: {{ $setting->phone ?? '-' }} | Email: {{ $setting->email ?? '-' }}</div>
            </td>
            <td style="width: 45%; vertical-align: top; text-align: right;">
                <div class="report-title">Finished Goods Stock Taking Report</div>
                <div class="sub-text">Session: <strong>{{ $session->session_no }}</strong></div>
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
                    <span style="color: #16a34a; font-weight: bold;">Finalized (Completed)</span>
                @else
                    <span style="color: #4f46e5; font-weight: bold;">In Progress</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="meta-label">Store / Store Type:</td>
            <td class="meta-val">{{ $session->storeType?->store_type_name ?? 'All Stores' }}</td>
            <td class="meta-label">Stock Date:</td>
            <td class="meta-val">{{ $session->started_at ? $session->started_at->format('d-m-Y h:i A') : '-' }}</td>
            <td class="meta-label">Counted By:</td>
            <td class="meta-val">{{ $session->starter?->name ?? '-' }}</td>
        </tr>
        @if($session->completed_at)
        <tr>
            <td class="meta-label">Finalized At:</td>
            <td class="meta-val">{{ $session->completed_at->format('d-m-Y h:i A') }}</td>
            <td class="meta-label">Finalized By:</td>
            <td class="meta-val" colspan="3">{{ $session->completer?->name ?? '-' }}</td>
        </tr>
        @endif
    </table>

    <table class="kpi-table">
        <tr>
            <td class="kpi-card">
                <div class="kpi-title">System Stock</div>
                <div class="kpi-num">{{ number_format($stats['total_system_qty'] ?? 0, 0) }}</div>
                <div class="sub-text">{{ $stats['total_skus'] ?? 0 }} SKUs</div>
            </td>
            <td class="kpi-card">
                <div class="kpi-title">Total Counted</div>
                <div class="kpi-num text-primary">{{ number_format($stats['total_scanned_qty'] ?? 0, 0) }}</div>
                <div class="sub-text">{{ $stats['scanned_skus'] ?? 0 }} Counted SKUs</div>
            </td>
            <td class="kpi-card">
                <div class="kpi-title">Exact Matched</div>
                <div class="kpi-num text-success">{{ $stats['matched_skus'] ?? 0 }}</div>
                <div class="sub-text">{{ number_format($stats['matched_qty'] ?? 0, 0) }} pcs</div>
            </td>
            <td class="kpi-card">
                <div class="kpi-title">Shortage</div>
                <div class="kpi-num text-danger">{{ number_format($stats['shortage_qty'] ?? 0, 0) }} pcs</div>
                <div class="sub-text">{{ $stats['shortage_skus'] ?? 0 }} SKUs</div>
            </td>
            <td class="kpi-card">
                <div class="kpi-title">Excess</div>
                <div class="kpi-num text-warning">{{ number_format($stats['excess_qty'] ?? 0, 0) }} pcs</div>
                <div class="sub-text">{{ $stats['excess_skus'] ?? 0 }} SKUs</div>
            </td>
            <td class="kpi-card">
                <div class="kpi-title">Progress %</div>
                <div class="kpi-num">{{ number_format($stats['coverage_pct'] ?? 0, 1) }}%</div>
                <div class="sub-text">Count Accuracy</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">#</th>
                <th style="text-align: left;">Item Name / Style</th>
                <th style="text-align: left; width: 65px;">Art No</th>
                <th style="width: 85px;">SKU / Barcode</th>
                <th style="width: 30px;">Size</th>
                <th style="width: 35px;">Sleeve</th>
                <th style="width: 45px; text-align: right;">Rate (₹)</th>
                <th style="width: 40px; text-align: right;">System</th>
                <th style="width: 40px; text-align: right;">Open</th>
                <th style="width: 45px; text-align: right;">Scanned</th>
                <th style="width: 40px; text-align: right;">Shortage</th>
                <th style="width: 40px; text-align: right;">Excess</th>
                <th style="width: 60px;">Status</th>
                <th style="width: 100px;">Location</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $idx => $entry)
                @php
                    $item = $entry['model'];
                    $calc = $entry['calc'];
                    $sys = floatval($calc['system_qty'] ?? 0);
                    $open = floatval($calc['open_qty'] ?? 0);
                    $scan = floatval($calc['scanned_qty'] ?? 0);
                    $shortage = floatval($calc['shortage_qty'] ?? 0);
                    $excess = floatval($calc['excess_qty'] ?? 0);

                    $totalSys += $sys;
                    $totalOpen += $open;
                    $totalScan += $scan;
                    $totalShortage += $shortage;
                    $totalExcess += $excess;

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
                @endphp
                <tr class="{{ $rowClass }}">
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ $item->style_name ?: '-' }}</td>
                    <td><strong>{{ $item->art_no ?: '-' }}</strong></td>
                    <td class="text-center font-monospace">{{ $item->sku }}</td>
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
                            <span class="badge badge-completed">Completed</span>
                        @elseif($calc['status'] === 'excess')
                            <span class="badge badge-excess">Excess (+{{ (int)$excess }})</span>
                        @else
                            <span class="badge badge-shortage">Shortage (-{{ (int)$shortage }})</span>
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
                <td class="text-end">{{ number_format($totalSys, 0) }}</td>
                <td class="text-end text-primary">{{ number_format($totalOpen, 0) }}</td>
                <td class="text-end">{{ number_format($totalScan, 0) }}</td>
                <td class="text-end text-danger">{{ number_format($totalShortage, 0) }}</td>
                <td class="text-end text-warning">{{ number_format($totalExcess, 0) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <table class="signatures-table">
        <tr>
            <td style="width: 5%;"></td>
            <td class="signature-box" style="width: 25%;">
                Counted By<br>
                <span style="font-weight: normal; color: #64748b;">{{ $session->starter?->name ?? 'Stock Counter' }}</span>
            </td>
            <td style="width: 10%;"></td>
            <td class="signature-box" style="width: 25%;">
                Verified By (Store In-charge)<br>
                <span style="font-weight: normal; color: #64748b;">{{ $session->completer?->name ?? 'Verified Signature' }}</span>
            </td>
            <td style="width: 10%;"></td>
            <td class="signature-box" style="width: 25%;">
                Approved By<br>
                <span style="font-weight: normal; color: #64748b;">Managing Director / GM</span>
            </td>
            <td style="width: 5%;"></td>
        </tr>
    </table>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 10mm 15mm 10mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #222;
            line-height: 1.3;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            border-bottom: 2px solid #333;
            padding-bottom: 6px;
        }
        .company-title {
            font-size: 18px;
            font-weight: bold;
            color: #111;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .sheet-title {
            font-size: 13px;
            font-weight: bold;
            color: #0d6efd;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .meta-text {
            font-size: 9px;
            color: #555;
        }
        .policy-banner {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 4px 8px;
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 8px;
            text-align: center;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .data-table th {
            background-color: #f1f3f5;
            color: #333;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            border: 1px solid #bbb;
            padding: 5px 4px;
            text-align: left;
        }
        .data-table td {
            border: 1px solid #ccc;
            padding: 5px 4px;
            font-size: 9.5px;
            vertical-align: middle;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .sign-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .sign-box {
            width: 33.33%;
            border-top: 1px solid #444;
            padding-top: 5px;
            text-align: center;
            font-size: 9.5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <div class="company-title">{{ $setting->company_name ?? 'NACHIAS' }}</div>
                <div class="meta-text">{{ $setting->address ?? '' }}</div>
                <div class="sheet-title">{{ $title }}</div>
            </td>
            <td style="width: 40%; text-align: right; vertical-align: top;">
                <div class="meta-text"><strong>Date:</strong> {{ $date }}</div>
                <div class="meta-text"><strong>Total Boxes Listed:</strong> {{ $bookings->count() }}</div>
                <div class="meta-text"><strong>Printed At:</strong> {{ date('d-m-Y H:i A') }}</div>
            </td>
        </tr>
    </table>

    <div class="policy-banner">
        MANDATORY AUDIT RULE: NO LR NUMBER = NO PAYMENT CLEARANCE. Driver must submit physical/digital LR slips for all parcels to clear freight payout.
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%;" class="text-center">#</th>
                <th style="width: 10%;">Invoice No</th>
                <th style="width: 22%;">Customer / Buyer Name</th>
                <th style="width: 12%;">Destination Place</th>
                <th style="width: 13%;">Preferred Transport</th>
                <th style="width: 6%;" class="text-center">Box No</th>
                <th style="width: 6%;" class="text-center">Qty (Pcs)</th>
                <th style="width: 14%;">LR Slip No (Receipt)</th>
                <th style="width: 14%;">Driver Sign / Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $idx => $b)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="fw-bold">{{ $b->salesInvoice->inv_no ?? '-' }}</td>
                    <td>
                        <strong>{{ $b->customer->name ?? '-' }}</strong>
                        @if(!empty($b->customer->code))
                            <span style="font-size: 8.5px; color: #666;">({{ $b->customer->code }})</span>
                        @endif
                    </td>
                    <td>{{ $b->booking_office ?: ($b->customer->place->place_name ?? '-') }}</td>
                    <td class="fw-bold">{{ $b->preferred_transport ?: '-' }}</td>
                    <td class="text-center fw-bold">{{ $b->box_number }}</td>
                    <td class="text-center fw-bold">{{ (int)$b->quantity }}</td>
                    <td>{{ $b->lr_no ?: '____________________' }}</td>
                    <td>____________________</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 15px; color: #888;">
                        No transport bookings found for this selection.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="sign-table">
        <tr>
            <td class="sign-box">
                Prepared By (Warehouse Incharge)<br>
                <small style="font-weight: normal; color: #666;">Parcels verified & handed over</small>
            </td>
            <td style="width: 33.33%;"></td>
            <td class="sign-box">
                Received By (Driver Signature)<br>
                <small style="font-weight: normal; color: #666;">I acknowledge receipt of all above boxes</small>
            </td>
        </tr>
    </table>
</body>
</html>

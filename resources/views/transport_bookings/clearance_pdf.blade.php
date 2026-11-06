<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 10mm 12mm 10mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10.5px;
            color: #222;
            line-height: 1.35;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            border-bottom: 2px solid #198754;
            padding-bottom: 6px;
        }
        .company-title {
            font-size: 19px;
            font-weight: bold;
            color: #111;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .sheet-title {
            font-size: 13px;
            font-weight: bold;
            color: #198754;
            text-transform: uppercase;
            margin-top: 3px;
        }
        .meta-text {
            font-size: 9.5px;
            color: #444;
            line-height: 1.4;
        }
        .summary-card-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .summary-box {
            padding: 6px 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .box-green {
            background-color: #e8f5e9;
            border-color: #a5d6a7;
            color: #1b5e20;
        }
        .box-blue {
            background-color: #e3f2fd;
            border-color: #90caf9;
            color: #0d47a1;
        }
        .box-gray {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #333;
        }
        .policy-banner {
            background-color: #d1e7dd;
            border: 1px solid #badbcc;
            color: #0f5132;
            padding: 5px 10px;
            font-size: 9.5px;
            font-weight: bold;
            margin-bottom: 8px;
            text-align: center;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .data-table th {
            background-color: #e9ecef;
            color: #212529;
            font-weight: bold;
            font-size: 9.5px;
            text-transform: uppercase;
            border: 1px solid #bbb;
            padding: 6px 4px;
            text-align: left;
        }
        .data-table td {
            border: 1px solid #ccc;
            padding: 6px 4px;
            font-size: 10px;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) td {
            background-color: #fafbfc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .badge-lr {
            font-family: monospace;
            font-weight: bold;
            font-size: 10px;
            color: #0f5132;
            background: #d1e7dd;
            padding: 2px 5px;
            border-radius: 3px;
            display: inline-block;
        }
        .badge-cleared {
            color: #0f5132;
            font-weight: bold;
            font-size: 9.5px;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 58%;">
                <div class="company-title">{{ $setting->company_name ?? 'NACHIAS FASHION PRIVATE LIMITED' }}</div>
                <div class="meta-text">{{ $setting->address ?? '' }}</div>
                <div class="sheet-title">DRIVER FREIGHT &amp; PAYMENT CLEARANCE PAYOUT VOUCHER (CLEARED INVOICES)</div>
            </td>
            <td style="width: 42%; text-align: right; vertical-align: top;">
                <div class="meta-text"><strong>Driver / Agent:</strong> <span style="font-size: 11px; color: #0d6efd; font-weight: bold;">{{ $driverLabel }}</span></div>
                <div class="meta-text"><strong>Date Period:</strong> {{ $periodLabel }}</div>
                <div class="meta-text"><strong>Cleared Consignments:</strong> {{ $totalBoxes }} Boxes</div>
                <div class="meta-text"><strong>Printed At:</strong> {{ date('d-m-Y H:i A') }}</div>
            </td>
        </tr>
    </table>

    <table class="summary-card-table">
        <tr>
            <td style="width: 33%; padding-right: 4px;">
                <div class="summary-box box-blue">
                    <span style="font-size: 9px; text-transform: uppercase;">Assigned Driver</span><br>
                    <strong style="font-size: 12px;">{{ $driverLabel }}</strong>
                </div>
            </td>
            <td style="width: 33%; padding: 0 4px;">
                <div class="summary-box box-gray">
                    <span style="font-size: 9px; text-transform: uppercase;">Total Cleared Boxes</span><br>
                    <strong style="font-size: 12px;">{{ $totalBoxes }} Cleared Consignments</strong>
                </div>
            </td>
            <td style="width: 34%; padding-left: 4px;">
                <div class="summary-box box-green text-right">
                    <span style="font-size: 9px; text-transform: uppercase;">Total Cleared Payout</span><br>
                    <strong style="font-size: 15px; color: #1b5e20;">&#8377;{{ number_format($totalAmount, 2) }}</strong>
                </div>
            </td>
        </tr>
    </table>

    <div class="policy-banner">
        AUDIT VERIFIED: Showing exclusively CLEARED &amp; APPROVED freight payouts with authentic transport LR numbers.
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%;" class="text-center">#</th>
                <th style="width: 10%;">Invoice No</th>
                <th style="width: 22%;">Customer / Buyer Name</th>
                <th style="width: 12%;">Destination Town</th>
                <th style="width: 12%;">Transporter</th>
                <th style="width: 10%;">Driver</th>
                <th style="width: 11%;" class="text-center">Verified LR No</th>
                <th style="width: 6%;" class="text-center">Box</th>
                <th style="width: 4%;" class="text-center">Qty</th>
                <th style="width: 10%;" class="text-center">Cleared Date</th>
                <th style="width: 8%;" class="text-right">Freight (&#8377;)</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalQty = 0; 
            @endphp
            @forelse($bookings as $idx => $b)
                @php 
                    $totalQty += (int)$b->quantity; 
                    $destPlace = (!empty($b->booking_office) && $b->booking_office !== '-')
                        ? $b->booking_office
                        : ($b->customer && $b->customer->city ? $b->customer->city->city_name : ($b->customer && $b->customer->place ? $b->customer->place->place_name : '-'));
                    $driverName = $b->deliveryAgent ? $b->deliveryAgent->name : ($b->delivery_agent_name ?: '-');
                    $clearedDate = $b->payment_cleared_date 
                        ? \Carbon\Carbon::parse($b->payment_cleared_date)->format('d-m-Y') 
                        : ($b->updated_at ? \Carbon\Carbon::parse($b->updated_at)->format('d-m-Y') : '-');
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="fw-bold">
                        {{ $b->salesInvoice->inv_no ?? '-' }}
                        @if(!empty($b->salesInvoice?->inv_date))
                            <div style="font-size: 8.5px; font-weight: normal; color: #666; margin-top: 1px;">{{ \Carbon\Carbon::parse($b->salesInvoice->inv_date)->format('d-m-Y') }}</div>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $b->customer->name ?? '-' }}</strong>
                        @if(!empty($b->customer?->code))
                            <span style="font-size: 8.5px; color: #666;">({{ $b->customer->code }})</span>
                        @endif
                    </td>
                    <td>{{ $destPlace }}</td>
                    <td>{{ $b->preferred_transport ?: '-' }}</td>
                    <td>{{ $driverName }}</td>
                    <td class="text-center">
                        <span class="badge-lr">{{ $b->lr_no ?: '-' }}</span>
                        @if($b->booking_date)
                            <div style="font-size: 8px; color: #666; margin-top: 1px;">{{ \Carbon\Carbon::parse($b->booking_date)->format('d-m-Y') }}</div>
                        @endif
                    </td>
                    <td class="text-center fw-bold">{{ $b->box_number ?: '1/1' }}</td>
                    <td class="text-center">{{ (int)$b->quantity }}</td>
                    <td class="text-center">
                        <span class="badge-cleared">{{ $clearedDate }}</span>
                    </td>
                    <td class="text-right fw-bold" style="color: #198754;">&#8377;{{ number_format($b->lr_price, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 15px; color: #888;">No cleared invoices found for selected driver / date filter.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f3f5; font-weight: bold; border-top: 2px solid #333;">
                <td colspan="7" class="text-right">TOTAL SUMMARY:</td>
                <td class="text-center">{{ $totalBoxes }} Boxes</td>
                <td class="text-center">{{ $totalQty }} Pcs</td>
                <td></td>
                <td class="text-right" style="font-size: 11.5px; color: #198754;">&#8377;{{ number_format($totalAmount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 6px; font-size: 10px;">
        <strong>Total Cleared Amount in Words:</strong> 
        <span style="font-style: italic; text-transform: capitalize;">
            {{ function_exists('numberToWords') ? numberToWords($totalAmount) : '' }}
        </span>
    </div>

    <div style="margin-top: 55px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 22%; text-align: center; vertical-align: bottom;">
                    <div style="border-top: 1.5px solid #222; padding-top: 6px; font-weight: bold; font-size: 10px;">
                        Prepared By<br><span style="font-size: 8.5px; font-weight: normal; color: #555;">(Logistics Desk)</span>
                    </div>
                </td>
                <td style="width: 4%;"></td>
                <td style="width: 22%; text-align: center; vertical-align: bottom;">
                    <div style="border-top: 1.5px solid #222; padding-top: 6px; font-weight: bold; font-size: 10px;">
                        Verified By<br><span style="font-size: 8.5px; font-weight: normal; color: #555;">(Store In-charge)</span>
                    </div>
                </td>
                <td style="width: 4%;"></td>
                <td style="width: 22%; text-align: center; vertical-align: bottom;">
                    <div style="border-top: 1.5px solid #222; padding-top: 6px; font-weight: bold; font-size: 10px;">
                        Approved By<br><span style="font-size: 8.5px; font-weight: normal; color: #555;">(Accounts / Finance)</span>
                    </div>
                </td>
                <td style="width: 4%;"></td>
                <td style="width: 22%; text-align: center; vertical-align: bottom;">
                    <div style="border-top: 1.5px solid #222; padding-top: 6px; font-weight: bold; font-size: 10px;">
                        Payee Signature<br><span style="font-size: 8.5px; font-weight: normal; color: #555;">(Driver / Agent with Date)</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

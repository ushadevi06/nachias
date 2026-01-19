<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fabric Consumption - {{ $jobCard->job_card_no }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }
        .company-address {
            font-size: 12px;
            margin: 5px 0;
            line-height: 1.4;
        }
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            margin: 10px 0;
        }
        .metadata-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .metadata-table td {
            padding: 2px 0;
        }
        .label {
            font-weight: bold;
            width: 100px;
        }
        .value {
            border-bottom: none;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th {
            background-color: #f2f2f2;
            border: 1px solid #333;
            padding: 5px;
            text-align: center;
            font-weight: bold;
        }
        .data-table td {
            border: 1px solid #333;
            padding: 5px;
            text-align: center;
        }
        .text-end {
            text-align: right !important;
        }
        .footer-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .footer-row td {
            border-top: 2px solid #333 !important;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="company-name">{{ $web_settings->company_name }}</h1>
        <div class="company-address">
            272/2, Muthupandi Nagar (Sarathambal Kovil Backside),<br>
            Byepass Road, Madurai - 625016
        </div>
    </div>

    <div class="title">Fabric Consumption</div>

    <table class="metadata-table">
        <tr>
            <td class="label">Printed On:</td>
            <td>{{ now()->format('d/m/Y H:i') }}</td>
            <td style="text-align: right;" class="label">Page:</td>
            <td style="text-align: left; width: 50px;">1/1</td>
        </tr>
        <tr>
            <td class="label">Work Order No:</td>
            <td class="value">{{ $jobCard->job_card_no }}</td>
            <td class="label" style="text-align: right;">Item:</td>
            <td class="value" style="text-align: left;">{{ $jobCard->brand->brand_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Date:</td>
            <td class="value">{{ $jobCard->created_at->format('d/m/Y') }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Art No</th>
                <th>Qty Produced</th>
                <th>Qty Issued</th>
                <th>Qty Wastage</th>
                <th>Qty Used</th>
                <th>Qty Adjusted</th>
                <th>Qty Consumed</th>
                <th>Qty Balance</th>
                <th>Qty/PC</th>
                <th>Unit Price</th>
                <th>Cost</th>
                <th>Cost/PC</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalQtyProduced = 0;
                $totalQtyIssued = 0;
                $totalQtyWastage = 0;
                $totalQtyUsed = 0;
                $totalQtyAdjusted = 0;
                $totalQtyConsumed = 0;
                $totalQtyBalance = 0;
                $totalCost = 0;
            @endphp
            @foreach($issueItems as $index => $item)
                @php
                    $artNo = $item->art_no;
                    $qtyConsumed = $item->qty_used + $item->qty_adjusted + $item->qty_wastage;
                    $qtyPerPc = $item->produced_qty > 0 ? $qtyConsumed / $item->produced_qty : 0;
                    $costPerPc = $item->produced_qty > 0 ? $item->total_cost / $item->produced_qty : 0;
                    
                    $totalQtyProduced += $item->produced_qty;
                    $totalQtyIssued += $item->qty_issue;
                    $totalQtyWastage += $item->qty_wastage;
                    $totalQtyUsed += $item->qty_used;
                    $totalQtyAdjusted += $item->qty_adjusted;
                    $totalQtyConsumed += $qtyConsumed;
                    $totalQtyBalance += $item->balance;
                    $totalCost += $item->total_cost;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $artNo }}</td>
                    <td>{{ number_format($item->produced_qty, 0) }}</td>
                    <td class="text-end">{{ number_format($item->qty_issue, 2) }}</td>
                    <td class="text-end">{{ number_format($item->qty_wastage, 2) }}</td>
                    <td class="text-end">{{ number_format($item->qty_used, 2) }}</td>
                    <td class="text-end">{{ number_format($item->qty_adjusted, 2) }}</td>
                    <td class="text-end">{{ number_format($qtyConsumed, 2) }}</td>
                    <td class="text-end">{{ number_format($item->balance, 2) }}</td>
                    <td class="text-end">{{ number_format($qtyPerPc, 2) }}</td>
                    <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end">{{ number_format($item->total_cost, 2) }}</td>
                    <td class="text-end">{{ number_format($costPerPc, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="footer-row">
                <td colspan="2" class="text-end">Total</td>
                <td>{{ number_format($totalQtyProduced, 0) }}</td>
                <td class="text-end">{{ number_format($totalQtyIssued, 2) }}</td>
                <td class="text-end">{{ number_format($totalQtyWastage, 2) }}</td>
                <td class="text-end">{{ number_format($totalQtyUsed, 2) }}</td>
                <td class="text-end">{{ number_format($totalQtyAdjusted, 2) }}</td>
                <td class="text-end">{{ number_format($totalQtyConsumed, 2) }}</td>
                <td class="text-end">{{ number_format($totalQtyBalance, 2) }}</td>
                <td class="text-end">{{ $totalQtyProduced > 0 ? number_format($totalQtyConsumed / $totalQtyProduced, 2) : '-' }}</td>
                <td class="text-end">-</td>
                <td class="text-end">{{ number_format($totalCost, 2) }}</td>
                <td class="text-end">{{ $totalQtyProduced > 0 ? number_format($totalCost / $totalQtyProduced, 2) : '-' }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>

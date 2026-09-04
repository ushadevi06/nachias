<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Production Receipt - {{ $receipt->jobCard->job_card_no ?? '' }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .metadata-table {
            width: 100%;
            margin-bottom: 5px;
            border-collapse: collapse;
        }
        .metadata-table td {
            padding: 2px 0;
        }
        .label {
            font-weight: bold;
            width: 90px;
        }
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 5px 0;
        }
        .section-header {
            background-color: #72a1e3;
            color: #fff;
            padding: 5px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 10px;
            font-size: 10px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .data-table th {
            background-color: #f2f2f2;
            border: 1px solid #333;
            padding: 4px;
            text-align: center;
            font-weight: bold;
        }
        .data-table td {
            border: 1px solid #333;
            padding: 4px;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .footer-row {
            font-weight: bold;
            background-color: #d9e9ff;
        }
        .signature {
            border-top: 1px solid #333;
            width: 180px;
            text-align: center;
            padding-top: 4px;
            margin-top: 40px;
            font-size: 10px;
        }
        .art-value {
            font-weight: bold;
            color: #2f6fae;
        }
        .art-label {
            font-weight: bold;
            color: #555;
        }
    </style>
</head>
<body>

    {{-- Printed On --}}
    <table class="metadata-table">
        <tr>
            <td class="label">Printed On:</td>
            <td>{{ now()->format('d/m/Y H:i') }}</td>
        </tr>
    </table>

    {{-- Company name + doc title header --}}
    <table class="metadata-table" style="margin-bottom: 4px;">
        <tr>
            <td style="font-size: 13px; font-weight: bold;">
                {{ $receipt->jobCard->serviceProvider->name ?? env('WEBSITE_NAME', 'Nachias Fashion Private Limited') }}
            </td>
            <td style="text-align: right; font-size: 14px; font-weight: bold; color: #2f6fae;">
                PRODUCTION RECEIPT
            </td>
        </tr>
    </table>

    <div class="title">Production Receipt</div>

    {{-- Metadata row (4-column layout with clean alignment) --}}
    <table class="metadata-table">
        <tr>
            <td style="width: 18%; font-weight: bold;">Receipt Date</td>
            <td style="width: 32%;">: {{ date('d/m/Y', strtotime($receipt->receipt_date)) }}</td>
            <td style="width: 18%; font-weight: bold;">Brand</td>
            <td style="width: 32%;">: {{ $receipt->jobCard->brand->brand_name ?? '-' }}</td>
        </tr>
        <tr>
            <td style="width: 18%; font-weight: bold;">Job Card No #</td>
            <td style="width: 32%;">: {{ $receipt->jobCard->job_card_no ?? '-' }}{{ ($receipt->is_additional && $receipt->additionalBatch) ? ' [Additional: +' . intval($receipt->additionalBatch->batch_total_qty ?? $receipt->additionalBatch->total_qty) . ' pcs]' : (($receipt->is_additional && $receipt->jobCard && floatval($receipt->jobCard->additional_qty) > 0) ? ' [Additional: +' . intval($receipt->jobCard->additional_qty) . ' pcs]' : '') }}</td>
            <td style="width: 18%; font-weight: bold;">Warehouse</td>
            <td style="width: 32%;">: {{ $receipt->warehouse->warehouse_name ?? '-' }}</td>
        </tr>
        <tr>
            <td style="width: 18%; font-weight: bold;">Store</td>
            <td style="width: 32%;">: {{ $receipt->storeType->store_type_name ?? '-' }}</td>
            <td style="width: 18%; font-weight: bold;">Store Location</td>
            <td style="width: 32%;">: {{ $receipt->storeLocation->store_location ?? '-' }}</td>
        </tr>
    </table>

    {{-- Blue section header (matches Work Order style) --}}
    <div class="section-header">
        Production Receipt Details
    </div>

    {{-- Items table --}}
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Item Description</th>
                <th>Size / Variant</th>
                <th class="text-center">Qty</th>
                <th class="text-center">UOM</th>
                <th class="text-end">Unit Price</th>
                <th class="text-end">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($receipt->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->item_name }}</strong><br>
                        <small>{{ $item->item_code }}</small>
                        @if(!empty($item->art_no))
                            <small class="art-label">[ Art No: <span class="art-value">{{ $item->art_no }}</span> ]</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->size_variant }}</td>
                    <td class="text-center">{{ number_format($item->qty_to_receive, 2) }}</td>
                    <td class="text-center">{{ $item->uom_code }}</td>
                    <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end">{{ number_format($item->total_value, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="footer-row">
                <td colspan="3" class="text-end">Total Received Quantity:</td>
                <td class="text-center">{{ number_format($receipt->items->sum('qty_to_receive'), 2) }}</td>
                <td></td>
                <td class="text-end">Grand Total:</td>
                <td class="text-end" style="font-size: 11px;">
                    {{ number_format($receipt->items->sum('total_value'), 2) }}
                </td>
            </tr>
        </tfoot>
    </table>

    @if($receipt->remarks)
        <div style="margin-top: 14px;">
            <strong>Remarks:</strong>
            <p style="margin: 3px 0 0; font-size: 10px;">{{ $receipt->remarks }}</p>
        </div>
    @endif

    {{-- Signatures --}}
    <div style="margin-top: 40px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;">
                    <div class="signature">Authorized Signature</div>
                </td>
                <td style="width: 50%; text-align: right;">
                    <div class="signature" style="margin-left: auto;">Received By</div>
                </td>
            </tr>
        </table>
    </div>

    @if(isset($is_print) && $is_print)
        <script>
            window.onload = function() { window.print(); }
        </script>
    @endif

</body>
</html>
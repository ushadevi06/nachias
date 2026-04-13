<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Production Receipt</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .logo {
            width: 150px;
        }
        .title {
            font-size: 18pt;
            font-weight: bold;
            text-align: right;
            color: #2f6fae;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            width: 120px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .data-table th, .data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .data-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .footer {
            margin-top: 50px;
            width: 100%;
        }
        .signature {
            border-top: 1px solid #333;
            width: 200px;
            text-align: center;
            padding-top: 5px;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('assets/images/jc_logo.png');
        $logoSrc = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
    @endphp

    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;">
                    @if($logoSrc)
                        <img src="{{ $logoSrc }}" class="logo">
                    @else
                        <h2>{{ env('WEBSITE_NAME') }}</h2>
                    @endif
                </td>
                <td style="width: 50%;" class="title">PRODUCTION RECEIPT</td>
            </tr>
        </table>
    </div>

    <table class="info-table">
        <tr>
            <td style="width: 50%;">
                <table>
                    <tr><td class="label">Receipt Date:</td><td>{{ date('d-m-Y', strtotime($receipt->receipt_date)) }}</td></tr>
                    <tr><td class="label">Job Card No:</td><td>{{ $receipt->jobCard->job_card_no ?? '-' }}</td></tr>
                </table>
            </td>
            <td style="width: 50%;">
                <table>
                    <tr><td class="label">Brand:</td><td>{{ $receipt->jobCard->brand->brand_name ?? '-' }}</td></tr>
                    <tr><td class="label">Store:</td><td>{{ $receipt->storeType->store_type_name ?? '-' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

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
                    </td>
                    <td>{{ $item->size_variant }}</td>
                    <td class="text-center">{{ number_format($item->qty_to_receive, 2) }}</td>
                    <td class="text-center">{{ $item->uom_code }}</td>
                    <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end">{{ number_format($item->total_value, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #f9f9f9;">
                <td colspan="3" class="text-end">Total Received Quantity:</td>
                <td class="text-center">{{ number_format($receipt->items->sum('qty_to_receive'), 2) }}</td>
                <td></td>
                <td class="text-end">Grand Total:</td>
                <td class="text-end">{{ number_format($receipt->items->sum('total_value'), 2) }}</td>
            </tr>
        </tfoot>
    </table>

    @if($receipt->remarks)
        <div style="margin-top: 20px;">
            <strong>Remarks:</strong>
            <p>{{ $receipt->remarks }}</p>
        </div>
    @endif

    <div class="footer">
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
            window.onload = function() {
                window.print();
            }
        </script>
    @endif
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Square Sticker</title>
    <style>
        @font-face {
            font-family: 'Arial';
            src: url('{{ asset('assets/fonts/Arial Regular.ttf') }}') format('truetype');
            font-weight: normal;
        }
        @font-face {
            font-family: 'Arial';
            src: url('{{ asset('assets/fonts/Arial Bold.ttf') }}') format('truetype');
            font-weight: bold;
        }
        @page {
            size: {{ $width }}mm {{ $height }}mm;
            margin: 0;
        }
        
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        html, body {
            width: {{ $width }}mm;
            height: {{ $height }}mm;
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: white;
            color: black;
            overflow: hidden;
        }

        .page-container {
            width: {{ $width }}mm;
            height: {{ $height }}mm;
            position: relative;
            background-color: white;
            border: none;
            box-sizing: border-box;
            overflow: hidden;
            padding: 2mm;
        }

        .fw-bold { font-weight: bold; }
        .text-center { text-align: center; }
        
        /* 45x45 Specific Styles */
        .square-header {
            font-size: 6pt;
            font-weight: bold;
            margin-bottom: 4mm;
            line-height: 1.15;
        }

        .square-main {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .square-left {
            width: 29mm;
        }

        .square-right {
            width: 10mm;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .details-table {
            border-collapse: collapse;
            width: 100%;
        }

        .details-table td {
            font-size: 6pt;
            line-height: 1.15;
            padding-bottom: 5px;
        }

        .td-lbl { width: 8.5mm; white-space: nowrap; }
        .td-col { width: 1.5mm; text-align: center; font-weight: bold; }
        .td-val { font-weight: bold; padding-left: 0.5mm; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .square-qr {
            margin-top: 1mm;
            margin-bottom: 0mm;
        }

        .square-qr img, .square-qr svg {
            width: 8mm;
            height: 8mm;
        }

        .mrp-box {
            margin-top: 2mm;
        }
        
        .mrp-label {
            font-size: 5pt;
            font-weight: bold;
        }

        .mrp-val {
            font-size: 10pt;
            font-weight: bold;
        }

        .square-footer {
            font-size: 4.5pt;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 1mm;
            line-height: 1.1;
            font-weight: bold;
        }
    </style>
</head>
<body onload="window.print()">
    @php
        $labelCollection = isset($labels) ? $labels : [$labelData];
    @endphp

    @foreach($labelCollection as $labelData)
        @php
            $qrString = $labelData['sku'] ?? $labelData['barcode'] ?? ($labelData['design'] ?? '-');
            $sText = strtoupper($labelData['sleeve'] ?? 'F/S');
            if (strpos($sText, 'FULL') !== false) {
                $sText = 'F/S';
            } elseif (strpos($sText, 'HALF') !== false) {
                $sText = 'H/S';
            }
        @endphp

        <div class="page-container">
            
            <div class="square-header" style="display: flex;">
                <div style="width: 8.5mm; font-weight: normal;">Item</div>
                <div style="width: 1.5mm; text-align: center;">:</div>
                <div style="flex: 1; padding-left: 0.5mm;">{{ strtoupper($labelData['item_name_full'] ?? ($labelData['product_name'] ?? '-')) }}</div>
            </div>
            
            <div class="square-main">
                <div class="square-left">
                    <table class="details-table">
                        <tr><td class="td-lbl">Design</td><td class="td-col">:</td><td class="td-val">{{ $labelData['design'] ?? '-' }}</td></tr>
                        <tr><td class="td-lbl">Size</td><td class="td-col">:</td><td class="td-val">{{ $labelData['size'] ?? '-' }}</td></tr>
                        <tr><td class="td-lbl">Sleeve</td><td class="td-col">:</td><td class="td-val">{{ $sText }}</td></tr>
                        <tr><td class="td-lbl">MRP</td><td class="td-col">:</td><td class="td-val">₹ {{ number_format((float) ($labelData['raw_price'] ?? str_replace(',', '', $labelData['price'] ?? 0)), 2) }}</td></tr>
                        <tr><td colspan="3" class="td-lbl" style="padding-top: 1.5mm;"><strong>{{ $labelData['lot_no'] ?? '' }}</strong></td></tr>
                    </table>
                </div>

                <div class="square-right">
                    <div class="square-qr">
                        {!! QrCode::size(45)->generate($qrString) !!}
                    </div>
                    <div style="font-size: 2.5pt; text-align:center;">
                        {{ $labelData['sku'] ?? '' }}
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</body>
</html>

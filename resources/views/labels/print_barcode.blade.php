<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Label - {{ $labelData['sku'] }}</title>
    <style>
        @font-face {
            font-family: 'Roboto Condensed';
            font-style: normal;
            font-weight: 400;
            src: local('Roboto Condensed'), local('RobotoCondensed-Regular');
        }
        @page {
            size: 60mm 130mm;
            margin: 0;
        }
        body {
            font-family: 'Arial Narrow', 'Roboto Condensed', sans-serif;
            margin: 0;
            padding: 3mm;
            color: #000;
            line-height: 1.15;
            -webkit-print-color-adjust: exact;
        }
        .label-container {
            width: 54mm;
            padding: 2mm;
            margin: auto;
            position: relative;
        }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        .company-header {
            font-size: 8px;
            margin-bottom: 3mm;
            text-align: center;
        }
        .company-name {
            font-size: 11px;
            margin-bottom: 0.5mm;
            display: block;
        }
        
        .product-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 3mm;
        }
        .product-table td {
            padding: 0.8mm 0;
            vertical-align: top;
        }
        .label-col {
            width: 20mm;
            font-weight: normal;
        }
        .colon-col {
            width: 3mm;
            padding-right: 1mm !important;
        }
        .value-col {
            font-weight: bold;
        }
        .large-value {
            font-size: 14px;
        }
        
        .mrp-qr-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 2mm;
            min-height: 25mm;
        }
        .mrp-info {
            flex: 1;
        }
        .mrp-label {
            font-size: 16px;
            font-weight: bold;
        }
        .mrp-value {
            font-size: 18px;
            font-weight: bold;
        }
        .mrp-taxes {
            font-size: 7.5px;
            display: block;
            margin-top: -1mm;
        }
        
        .qr-area {
            width: 18mm;
            text-align: center;
        }
        .sku-under-qr {
            font-size: 11px;
            font-weight: bold;
            margin-top: 0.5mm;
            letter-spacing: 0.5px;
        }
        
        .mfg-lot-section {
            font-size: 10px;
            font-weight: bold;
            margin-top: 2mm;
        }
        
        .footer-compliance {
            margin-top: 3mm;
            font-size: 8px;
            line-height: 1.2;
            padding-top: 2mm;
        }
        .made-in-india {
            text-align: right;
            font-weight: bold;
            font-size: 8px;
            margin-top: -2mm;
        }

        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="padding: 10px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; font-weight: bold; background: #673ab7; color: #fff; border: none; border-radius: 4px;">Print Label</button>
    </div>

    <div class="label-container">
        <div class="company-header">
            <div class="bold" style="font-size: 10px; text-align:left; margin-bottom: 5px;">MANUFACTURED & MARKETED BY</div>
            <div class="company-name bold" style="font-size: 10px; text-align:left;">{{ $labelData['company_name'] }}</div>
            <div style="font-size: 10px; text-align:left;">272/2, SOMU NAGAR, SIRINGERI NAGAR,</div>
            <div style="font-size: 10px; text-align:left;">SARATHAMBAL KOVIL BACKSIDE BYE PASS ROAD</div>
            <div style="font-size: 10px; text-align:left;">MADURAI - 625016</div>
            <div style="font-size: 10px; text-align:left;">TOLL-FREE: 8489938071, 8489938073</div>
            <div style="font-size: 10px; text-align:left;">EMAIL: {{ $labelData['company_email'] }}</div>
            <div class="bold">GSTIN: {{ $labelData['company_gstin'] }}</div>
        </div>

        <table class="product-table">
            <tr>
                <td class="label-col uppercase">PRODUCT</td>
                <td class="colon-col">:</td>
                <td class="value-col large-value uppercase" style="font-size: 10px;">{{ $labelData['product_name'] }}</td>
            </tr>
            <tr>
                <td class="label-col uppercase">BRAND NAME</td>
                <td class="colon-col">:</td>
                <td class="value-col uppercase">{{ $labelData['brand_name'] }}</td>
            </tr>
            <tr>
                <td class="label-col uppercase">ART NO</td>
                <td class="colon-col">:</td>
                <td class="value-col uppercase">{{ $labelData['design'] }}</td>
            </tr>
            <tr>
                <td class="label-col uppercase">COLOUR</td>
                <td class="colon-col">:</td>
                <td class="value-col uppercase">{{ $labelData['color'] }}</td>
            </tr>
            <tr>
                <td class="label-col uppercase">FABRIC</td>
                <td class="colon-col">:</td>
                <td class="value-col uppercase">{{ $labelData['fabric'] }}</td>
            </tr>
            <tr>
                <td class="label-col uppercase">SIZE</td>
                <td class="colon-col">:</td>
                <td class="value-col uppercase">
                    <span style="font-size: 14px;">{{ $labelData['size'] }}</span>
                    &nbsp;&nbsp;&nbsp; 
                    <span style="font-weight: normal; font-size: 10px;">SLEEVE {{ $labelData['sleeve'] }}</span>
                </td>
            </tr>
        </table>

        <div class="mrp-qr-container">
            <div class="mrp-info">
                <div class="mrp-label">
                    MRP : <span class="mrp-value">₹ {{ $labelData['price'] }}</span>
                </div>
                <span class="mrp-taxes">(Inclusive of All Taxes)</span>
                
                <div class="mfg-lot-section">
                    <div>DATE OF MFG: {{ strtoupper($labelData['mfg_date']) }}</div>
                    <div style="margin-top: 1mm;">LOT # {{ $labelData['lot_no'] }}</div>
                </div>
            </div>

            <div class="qr-area">
                @php
                    $qrString = ($labelData['sku'] ?? '-') . " | " .
                        ($labelData['product_name'] ?? '-') . " | " .
                        ($labelData['fabric'] ?? '-') . " | " .
                        ($labelData['size'] ?? '-') . " | " .
                        ($labelData['color'] ?? '-') . " | " .
                        ($labelData['sleeve'] ?? '-') . " | Qty: " .
                        ($labelData['quantity'] ?? '-');
                @endphp
                {!! QrCode::size(65)->generate($qrString) !!}
                <div class="sku-under-qr">{{ $labelData['sku'] }}</div>
            </div>
        </div>

        <div class="footer-compliance">
            <div>FOR COMPLAINTS PLEASE CONTACT CUSTOMER CARE EXECUTIVE</div>
            <div>ADDRESS AS ABOVE</div>
            <div>{{ $labelData['company_name'] }}</div>
            <div class="bold">MOBILE: 8489938073 (10 AM TO 6 PM)</div>
            <div style="font-size: 7px;">MONDAY - SATURDAY</div>
            <div class="made-in-india uppercase">MADE IN INDIA</div>
        </div>
    </div>
</body>
</html>

    <script>
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>

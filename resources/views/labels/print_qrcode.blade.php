<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print QR Label - {{ $labelData['sku'] }}</title>
    <style>
        @page {
            size: 100mm 150mm; /* A6 size approximately, adjust as needed */
            margin: 0;
        }
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 10mm;
            color: #000;
        }
        .label-container {
            width: 80mm;
            border: 1px solid #eee;
            padding: 5mm;
            position: relative;
        }
        .header {
            font-size: 8px;
            text-transform: uppercase;
            margin-bottom: 5mm;
            line-height: 1.2;
        }
        .main-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .item-info {
            flex: 1;
        }
        .item-name {
            font-size: 18px;
            font-weight: 800;
            margin: 0 0 2mm 0;
            text-transform: uppercase;
        }
        .item-design {
            font-size: 14px;
            font-weight: 600;
            margin: 0 0 1mm 0;
        }
        .item-fabric {
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            margin: 0 0 2mm 0;
        }
        .item-variant {
            font-size: 14px;
            font-weight: 700;
            margin: 0 0 4mm 0;
            text-transform: uppercase;
        }
        .item-price {
            font-size: 20px;
            font-weight: 800;
            margin-top: 5mm;
        }
        .item-price span {
            font-size: 10px;
            font-weight: 400;
            display: block;
        }
        .qr-section {
            text-align: center;
            width: 30mm;
        }
        .qr-code {
            margin-bottom: 1mm;
        }
        .sku-code {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .footer {
            margin-top: 10mm;
            font-size: 8px;
            text-transform: uppercase;
            border-top: 1px dashed #ccc;
            padding-top: 2mm;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="padding: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Print Label</button>
    </div>

    <div class="label-container">
        <div class="header">
            nachias garments<br>
            bypass road, tirupur
        </div>

        <div class="main-content">
            <div class="item-info">
                <h1 class="item-name">{{ $labelData['name'] }}</h1>
                <div class="item-design">{{ $labelData['design'] }}</div>
                <div class="item-fabric">{{ $labelData['fabric'] }}</div>
                <div class="item-variant">{{ $labelData['size'] }} SLEEVE {{ $labelData['sleeve'] }}</div>
                
                <div class="item-price">
                    ₹{{ $labelData['price'] }}
                    <span>(Incl. of all taxes)</span>
                </div>
            </div>

            <div class="qr-section">
                <div class="qr-code">
                    {!! QrCode::size(80)->generate($labelData['sku']) !!}
                </div>
                <div class="sku-code">{{ $labelData['sku'] }}</div>
            </div>
        </div>

        <div class="footer">
            Customer Care Contact: +91 00000 00000<br>
            MADE IN INDIA
        </div>
    </div>

    <script>
        // Auto print window when loaded
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>

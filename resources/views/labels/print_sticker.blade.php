<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Barcode Label</title>
    <style>
        @font-face {
            font-family: 'Open Sans';
            src: url('{{ asset('assets/fonts/OpenSans-Regular.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'Open Sans';
            src: url('{{ asset('assets/fonts/OpenSans-Bold.ttf') }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }
        @font-face {
            font-family: 'Impact';
            src: url('{{ asset('assets/fonts/Impact Regular.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'BebasNeue';
            src: url('{{ asset('assets/fonts/BebasNeue-Regular.ttf') }}') format('truetype');
            font-weight: 400;
        }
        @font-face {
            font-family: 'Oswald';
            src: url('{{ asset('assets/fonts/Oswald-SemiBold.ttf') }}') format('truetype');
            font-weight: 600;
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
            font-family: 'Open Sans', Arial, Helvetica, sans-serif;
            font-weight: 500;
            background-color: white;
            color: black;
            overflow: hidden;
        }

        .page-container {
            width: {{ $width }}mm;
            height: {{ $height }}mm;
            position: relative;
            background-color: white;
            border: 1px solid #000;
            box-sizing: border-box;
            overflow: hidden;
        }

        /* Helpers */
        .fw-bold { font-weight: bold; }
        .fw-normal { font-weight: normal; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .f-5 { font-size: 5pt; }
        .f-6 { font-size: 6pt; }
        .f-7 { font-size: 7pt; }
        .f-8 { font-size: 8pt; }
        .f-9 { font-size: 9pt; }
        .f-10 { font-size: 10pt; }
        .f-12 { font-size: 12pt; }
        .f-14 { font-size: 14pt; }
        
        .font-impact {
            font-family: 'Impact', 'Open Sans', sans-serif;
            font-weight: normal; /* Impact is naturally bold */
        }
        .font-bebas {
            font-family: 'BebasNeue', sans-serif;
            font-weight: 400;
        }
        .font-oswald {
            font-family: 'Oswald', sans-serif;
            font-weight: 600;
        }
        /* ---------------------------------------------------
           PRICE STICKER STYLES (70 x 50 mm)
           --------------------------------------------------- */
        
        .sticker-container {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .main-content {
            display: flex;
            justify-content: space-between;
            padding: 2.5mm 1mm 1mm 4mm;
            height: 35.5mm;
        }
        
        .left-col {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .right-col {
            width: 20mm;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }
        
        .right-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        
        .details-table {
            border-collapse: collapse;
            width: 100%;
        }
        .details-table td {
            padding: 0.6mm 0;
            vertical-align: top;
            font-size: 4.7pt;
            line-height: 1.15;
            font-weight: 500;
        }
        .td-lbl { width: 11mm; }
        .td-col { width: 1.5mm; text-align: center; }
        .td-val { white-space: nowrap; }
        
        .art-no-box {
            margin-top: 0mm;
            margin-bottom: 2mm;
            display: flex;
            align-items: center;
        }
        
        .sticker-qr {
        }
        .sticker-qr img, .sticker-qr svg {
            width: 10mm;
            height: 10mm;
        }
        
        .size-wrapper {
            display: flex;
            height: 11mm;
        }
        
        .size-box {
            width: 16mm;
            height: 100%;
            display: flex;
            justify-content: space-around;
            align-items: center;
            color: white;
            padding: 0 1.5mm;
        }
        
        .lot-vertical {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            font-size: 3pt;
            margin-left: 0.5mm;
            height: 100%;
            text-align: center;
            color: #000;
        }
        
        .mrp-section {
            margin-top: 4mm;
            margin-bottom: 2mm;
            text-align: left;
            line-height: 1.5;
        }
        
        .footer-boxes {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            width: 100%;
            display: flex;
            flex-direction: column;
            height: 14.5mm;
        }
        
        .footer-row {
            padding: 1mm 1mm 1mm 2mm;
            border-bottom: 1px solid #000;
            font-size: 5pt;
            line-height: 1.2;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .footer-row:last-child {
            border-bottom: none;
        }

    </style>
</head>
<body onload="window.print()">
    @php
        $labelCollection = isset($labels) ? $labels : [$labelData];
        $isTag = ($format ?? 'tag') === 'tag';
        
        $sizeColors = [
            '36' => '#cba3cc', // Purple
            '38' => '#86bc8b', // Green
            '40' => '#c8af79', // Khaki
            '42' => '#75a0c9', // Blue
            '44' => '#bac2c6', // Grey
        ];
    @endphp

    @foreach($labelCollection as $labelData)
        @php
            $currentSize = preg_replace('/[^0-9]/', '', $labelData['size'] ?? '');
            $bgColor = $sizeColors[$currentSize] ?? '#888888';
            
            $qrString = ($labelData['sku'] ?? '-') . " | " . ($labelData['product_name'] ?? '-') . " | " . ($labelData['size'] ?? '-');
            
            $sText = $labelData['sleeve'] ?? 'Full';
            $sText = str_replace(['F/S', 'H/S'], ['Full', 'Half'], $sText);

            $brandText = ucwords(strtolower($labelData['brand_name'] ?? '-'));
            $companyName = ucwords(strtolower($labelData['company_name'] ?? 'Nachias Fashion Private Limited'));
            
            $emails = explode(',', $labelData['company_email'] ?? 'srinachias@yahoo.in');
            $firstEmail = trim($emails[0]);
        @endphp

        <div class="page-container" style="{{ !$loop->last ? 'page-break-after: always;' : '' }}">
            <!-- PRICE STICKER (50x70 mm) -> Landscape 70x50 -->
            <div class="sticker-container">
                <div class="main-content">
                    <div class="left-col">
                        <div style="display: flex; margin-bottom: auto;">
                            <table class="details-table" style="width: auto; margin-right: 2mm;">
                                <tr><td class="td-lbl">Brand</td><td class="td-col">:</td><td class="td-val">{{ $brandText }}</td></tr>
                                <tr><td class="td-lbl">Product</td><td class="td-col">:</td><td class="td-val">{{ ucwords(strtolower($labelData['product_name'] ?? '-')) }}</td></tr>
                                <tr><td class="td-lbl">Fit</td><td class="td-col">:</td><td class="td-val">{{ $labelData['fit'] ?? 'Tailor Fit' }}</td></tr>
                                <tr><td class="td-lbl">Sleeve</td><td class="td-col">:</td><td class="td-val">{{ $sText }}</td></tr>
                                <tr><td class="td-lbl">Colour</td><td class="td-col">:</td><td class="td-val">{{ ucwords(strtolower($labelData['color'] ?? '-')) }}</td></tr>
                                <tr><td class="td-lbl">Fabric</td><td class="td-col">:</td><td class="td-val">{{ ucwords(strtolower($labelData['fabric'] ?? 'Cotton')) }}</td></tr>
                                <tr><td class="td-lbl">Net Quantity</td><td class="td-col">:</td><td class="td-val">{{ $labelData['quantity'] ?? '1 Number' }}</td></tr>
                            </table>
                            <div class="sticker-qr" style="margin-top: 4mm;">
                                {!! QrCode::size(40)->generate($qrString) !!}
                            </div>
                        </div>
                        <div class="art-no-box">
                            <span class="f-5 fw-bold" style="margin-right: 1mm;">Art No :</span>
                            <span class="font-bebas" style="font-size: 14pt; letter-spacing: 1px;">{{ $labelData['design'] ?? '-' }}</span>
                        </div>
                    </div>
                    
                    <div class="right-col">
                        <div class="size-wrapper">
                            <div class="size-box" style="background-color: {{ $bgColor }};">
                                <span class="f-6 font-oswald">Size</span>
                                <span class="font-bebas" style="font-size: 16pt;">{{ $labelData['size'] ?? '-' }}</span>
                            </div>
                            <div class="lot-vertical">Lot, {{ $labelData['lot_no'] ?? '' }}</div>
                        </div>
                        
                        <div class="mrp-section" style="margin-top: auto;">
                            <div class="f-6 fw-bold">MRP <span class="fw-normal" style="font-size: 4pt;">(inclusive of all taxes)</span></div>
                            <div class="fw-bold" style="font-size: 12pt; line-height: 1;">₹ {{ number_format((float) ($labelData['price'] ?? 0), 2) }}</div>
                            <div class="f-5" style="color: #000; margin-top: 0.5mm; font-size:4pt;">MADE IN INDIA WITH PRIDE</div>
                        </div>
                    </div>
                </div>
                
                <div class="footer-boxes">
                    <div class="footer-row">
                        <div>
                            <span class="f-5" style="color: #333;">Manufactured & Marketed by:</span> <span class="fw-bold" style="font-size: 7pt;">{{ $companyName }}</span>
                        </div>
                        <div>
                            <span style="font-size: 5pt;">{{ $labelData['company_address'] ?? '272/2, Somu Nager, Sringeri Nagar, By Pass Road, Madurai - 625 016.' }}</span>
                        </div>
                    </div>
                    <div class="footer-row" style="flex-direction: row; justify-content: space-between; align-items: center;">
                        <div><span class="f-5" style="color: #333;">Complaints :</span> <span class="fw-bold" style="font-size: 5pt;">84899 38071 | 84899 38073</span></div>
                        <div><span class="f-5" style="color: #333;">Email:</span> <span style="font-size: 5pt;">{{ $firstEmail }}</span></div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</body>
</html>

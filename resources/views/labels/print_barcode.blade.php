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
            border: none;
            border-radius: 0;
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
        /* ---------------------------------------------------
           PRICE TAG STYLES (45 x 85 mm)
           --------------------------------------------------- */
        .tag-container {
            width: 100%;
            height: 100%;
            padding: 1mm 2mm;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        
        .tag-header-icons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 8mm;
            padding: 0 4mm 0 16mm;
        }
        .tag-logo {
            height: 6mm;
            width: auto;
        }
        .tag-hole {
            width: 4mm;
            height: 4mm;
            border-radius: 50%;
            border: 1px solid #000;
        }

        .size-banner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1mm 3mm;
            color: white;
            margin-bottom: 2mm;
            height: 6.5mm;
        }
        
        .tag-main-details {
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex: 1;
            position: relative;
        }
        
        .details-table {
            border-collapse: collapse;
            width: 100%;
        }
        .details-table td {
            padding: 0.3mm 0;
            vertical-align: top;
            font-size: 5.5pt;
            line-height: 1.25;
            font-weight: 600;
            color: #000000;
        }
        .tag-main-details .details-table td {
            padding: 0.8mm 0;
            vertical-align: middle;
            font-size: 7.5pt;
            line-height: 1.3;
            font-weight: 700;
        }
        .td-lbl { width: 14mm; }
        .td-col { width: 2mm; text-align: center; font-weight: bold; }
        .td-val { width: 26mm; }
        
        .tag-qr-section {
            position: absolute;
            right: 0mm;
            top: 2mm;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .tag-qr-section img, .tag-qr-section svg {
            width: 8mm;
            height: 8mm;
        }
        
        .lot-vertical {
            position: absolute;
            right: 1.5mm;
            bottom: 38mm;
            transform: rotate(-90deg);
            transform-origin: right bottom;
            font-size: 6pt;
            color: #000000;
            font-weight: 700;
            white-space: nowrap;
        }
        
        .tag-separator {
            border-top: 1px solid #221F1F;
            margin: 1.5mm 0;
        }
        
        .tag-footer-info {
            text-align: left;
            line-height: 1.1;
        }
        
        .complaints-table {
            border-collapse: collapse;
            width: 100%;
        }
        .complaints-table td {
            font-size: 4.5pt;
            line-height: 1.2;
            vertical-align: top;
        }
        .comp-lbl { width: 13mm; }
        .comp-col { width: 2mm; text-align: center; }
        .comp-val { }
        
        .mrp-section {
            text-align: center;
            margin-top: auto;
            margin-bottom: 1mm;
        }
        
        /* ---------------------------------------------------
           PRICE STICKER STYLES (70 x 50 mm)
           --------------------------------------------------- */
        .sticker-container {
            width: 100%;
            height: 100%;
            padding: 2mm 3mm;
            display: flex;
            flex-direction: column;
        }
        
        .sticker-top-row {
            display: flex;
            justify-content: space-between;
            flex: 1;
        }
        
        .sticker-left-details {
            width: 42mm;
        }
        
        .sticker-right-details {
            width: 20mm;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }
        
        .sticker-size-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            width: 20mm;
            height: 6mm;
            padding: 0 2mm;
            margin-bottom: 2mm;
        }
        
        .sticker-qr {
            margin-bottom: 2mm;
            text-align: right;
        }
        .sticker-qr img, .sticker-qr svg {
            width: 12mm;
            height: 12mm;
        }
        
        .sticker-mrp-section {
            text-align: right;
        }
        
        .sticker-footer-info {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 1mm 0;
            line-height: 1.1;
            margin-bottom: 0.5mm;
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
        @endphp

        <div class="page-container" style="{{ !$loop->last ? 'page-break-after: always;' : '' }}">
            
            @if($isTag)
                <!-- PRICE TAG (45x85 mm) -->
                <div class="tag-container">
                    <div class="tag-header-icons">
                        <img src="{{ url('assets/images/fav.jpeg') }}" class="tag-logo" alt="Logo">
                        <div class="tag-hole"></div>
                    </div>
                    
                    <div class="size-banner" style="background-color: {{ $bgColor }};">
                        <span class="f-8 fw-bold">Size</span>
                        <span class="f-14 font-bebas" style="letter-spacing: 1px;">{{ $labelData['size'] ?? '-' }}</span>
                    </div>
                    
                    <div class="tag-main-details">
                        <table class="details-table">
                            <tr><td class="td-lbl">Brand</td><td class="td-col">:</td><td class="td-val">{{ $brandText }}</td></tr>
                            <tr><td class="td-lbl">Product</td><td class="td-col">:</td><td class="td-val">{{ ucwords(strtolower($labelData['product_name'] ?? '-')) }}</td></tr>
                            <tr><td class="td-lbl">Fit</td><td class="td-col">:</td><td class="td-val">{{ $labelData['fit'] ?? 'Tailor Fit' }}</td></tr>
                            <tr><td class="td-lbl">Sleeve</td><td class="td-col">:</td><td class="td-val">{{ $sText }}</td></tr>
                            <tr><td class="td-lbl">Colour</td><td class="td-col">:</td><td class="td-val">{{ ucwords(strtolower($labelData['color'] ?? '-')) }}</td></tr>
                            <tr><td class="td-lbl">Fabric</td><td class="td-col">:</td><td class="td-val">{{ ucwords(strtolower($labelData['fabric'] ?? 'Cotton')) }}</td></tr>
                            <tr><td class="td-lbl">Net Quantity</td><td class="td-col">:</td><td class="td-val">{{ $labelData['quantity'] ?? '1 Number' }}</td></tr>
                            <tr><td class="td-lbl" style="padding-top: 1.5mm;">Art No</td><td class="td-col" style="padding-top: 1.5mm;">:</td><td class="td-val font-bebas" style="padding-top: 1.5mm; font-size: 16pt; letter-spacing: 1px;">{{ $labelData['design'] ?? '-' }}</td></tr>
                        </table>
                        <div class="tag-qr-section">
                            {!! QrCode::size(50)->generate($qrString) !!}
                        </div>
                    </div>
                    <div class="lot-vertical">Lot: {{ $labelData['lot_no'] ?? '' }}</div>
                    
                    <div class="tag-footer-info f-6">
                        <div class="f-5" style="color: #000000; margin-bottom: 0.5mm;">Manufactured & Marketed by:</div>
                        <div class="fw-bold" style="font-size: 5pt; margin-bottom: 0.5mm;">{{ $companyName }}</div>
                        <div style="font-size: 4pt; line-height: 1.2;">
                            {{ $labelData['company_address'] }}
                        </div>
                        
                        <div class="tag-separator"></div>
                        
                        <table class="complaints-table">
                            <tr>
                                <td class="comp-lbl">Complaints</td>
                                <td class="comp-col">:</td>
                                <td class="comp-val fw-bold">{{ $labelData['toll_free'] ?? '84899 38071 | 84899 38073' }}</td>
                            </tr>
                            <tr>
                                <td class="comp-lbl">Email</td>
                                <td class="comp-col">:</td>
                                <td class="comp-val">{{ $labelData['company_email'] ?? 'srinachias@yahoo.in' }}</td>
                            </tr>
                        </table>
                        <div class="tag-separator"></div>
                    </div>
                    
                    <div class="mrp-section">
                        <div class="f-6 fw-bold">MRP <span class="fw-normal" style="font-size: 5pt;">(inclusive of all taxes)</span></div>
                        <div class="f-12 fw-bold">₹ {{ number_format((float) ($labelData['price'] ?? 0), 2) }}</div>
                        <div class="f-5" style="color: #000; margin-top: 0.5mm;">MADE IN INDIA WITH PRIDE</div>
                    </div>
                </div>
            @else
                <!-- PRICE STICKER (50x70 mm) -> Landscape 70x50 -->
                <div class="sticker-container">
                    <div class="sticker-top-row">
                        <div class="sticker-left-details">
                            <table class="details-table">
                                <tr><td class="td-lbl">Brand</td><td class="td-col">:</td><td class="td-val">{{ $brandText }}</td></tr>
                                <tr><td class="td-lbl">Product</td><td class="td-col">:</td><td class="td-val">{{ ucwords(strtolower($labelData['product_name'] ?? '-')) }}</td></tr>
                                <tr><td class="td-lbl">Fit</td><td class="td-col">:</td><td class="td-val">{{ $labelData['fit'] ?? 'Tailor Fit' }}</td></tr>
                                <tr><td class="td-lbl">Sleeve</td><td class="td-col">:</td><td class="td-val">{{ $sText }}</td></tr>
                                <tr><td class="td-lbl">Colour</td><td class="td-col">:</td><td class="td-val">{{ ucwords(strtolower($labelData['color'] ?? '-')) }}</td></tr>
                                <tr><td class="td-lbl">Fabric</td><td class="td-col">:</td><td class="td-val">{{ ucwords(strtolower($labelData['fabric'] ?? 'Cotton')) }}</td></tr>
                                <tr><td class="td-lbl">Net Quantity</td><td class="td-col">:</td><td class="td-val">{{ $labelData['quantity'] ?? '1 Number' }}</td></tr>
                                <tr><td class="td-lbl" style="padding-top: 1mm;">Art No</td><td class="td-col" style="padding-top: 1mm;">:</td><td class="td-val fw-bold" style="padding-top: 1mm; font-size: 12pt;">{{ $labelData['design'] ?? '-' }}</td></tr>
                            </table>
                        </div>
                        <div class="sticker-right-details">
                            <div class="sticker-size-box" style="background-color: {{ $bgColor }};">
                                <span class="f-7 fw-bold">Size</span>
                                <span class="f-12 fw-bold font-impact">{{ $labelData['size'] ?? '-' }}</span>
                            </div>
                            <div class="sticker-qr">
                                {!! QrCode::size(45)->generate($qrString) !!}
                                <div class="fw-bold" style="color: #000000; text-align: right; margin-top: 1mm; font-size: 8pt;">Lot: {{ $labelData['lot_no'] ?? '' }}</div>
                            </div>
                            <div class="sticker-mrp-section">
                                <div class="fw-bold" style="font-size: 8pt; color: #000000;">MRP <span class="fw-bold" style="font-size: 6pt;">(inclusive of all taxes)</span></div>
                                <div class="fw-bold" style="font-size: 16pt; color: #000000; margin: 1mm 0;">₹ {{ number_format((float) ($labelData['price'] ?? 0), 2) }}</div>
                                <div class="fw-bold" style="font-size: 6.5pt; color: #000000;">MADE IN INDIA WITH PRIDE</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="sticker-footer-info mt-1" style="color: #000000;">
                        <span class="fw-bold" style="font-size: 6.5pt;">Mfr. & Mktd. by: {{ $companyName }}</span><br>
                        <span class="fw-bold" style="font-size: 5.5pt; line-height: 1.2;">{{ $labelData['company_address'] }}</span>
                    </div>
                    <table class="complaints-table" style="margin-bottom: 0; color: #000000;">
                        <tr>
                            <td class="comp-lbl fw-bold" style="font-size: 5.5pt;">Complaints</td>
                            <td class="comp-col fw-bold" style="font-size: 5.5pt;">:</td>
                            <td class="comp-val fw-bold" style="font-size: 5.5pt;">{{ $labelData['toll_free'] ?? '84899 38071 | 84899 38073' }}</td>
                            <td class="fw-bold" style="text-align:right; font-size: 5.5pt;">Email: {{ $labelData['company_email'] ?? 'srinachias@yahoo.in' }}</td>
                        </tr>
                    </table>
                </div>
            @endif

        </div>
    @endforeach
</body>
</html>

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
            margin-bottom: 2.5mm;
            height: 6.5mm;
        }
        
        .tag-main-details {
            height: 33.2mm;
            position: relative;
            padding-top: 2.3mm;
        }
        
        .details-table {
            border-collapse: collapse;
            width: 100%;
        }
        .details-table td {
            padding: 0.5mm 0;
            vertical-align: middle;
            font-size: 6.5pt;
            line-height: 1.1;
            font-weight: 700;
            color: #000000;
        }
        .td-lbl { width: 12.5mm; white-space: nowrap; }
        .td-col { width: 2mm; text-align: center; font-weight: bold; }
        .td-val { width: 24.5mm; }
        
        .tag-qr-section {
            position: absolute;
            right: 3.3mm;
            top: 6mm;
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
            right: -0.5mm;
            top: 2mm;
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            font-size: 6pt;
            color: #000000;
            font-weight: 700;
            white-space: nowrap;
        }
        
        .tag-separator {
            border-top: 1px solid #221F1F;
            margin: 1.5mm -2mm;
        }
        
        .tag-footer-info {
            text-align: left;
            line-height: 1.1;
            height: 13.4mm;
            overflow: hidden;
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
            position: absolute;
            bottom: 2mm;
            left: 0;
            right: 0;
            width: 100%;
        }
        
        .hide-for-print {
            visibility: hidden !important;
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
            
            $phones = str_replace(',', ' | ', $labelData['toll_free'] ?? '84899 38071 | 84899 38073');
        @endphp

        <div class="page-container" style="{{ !$loop->last ? 'page-break-after: always;' : '' }}">
            <!-- PRICE TAG (45x85 mm) -->
            <div class="tag-container">
                <div class="tag-header-icons hide-for-print">
                    <img src="{{ url('assets/images/fav.jpeg') }}" class="tag-logo" alt="Logo">
                    <div class="tag-hole"></div>
                </div>
                
                <div class="size-banner hide-for-print" style="background-color: {{ $bgColor }};">
                    <span class="f-6  font-oswald">Size</span>
                    <span class="f-14 font-bebas" style="letter-spacing: 1px;">{{ $labelData['size'] ?? '-' }}</span>
                </div>
                
                <div class="tag-main-details">
                    <table class="details-table">
                        <tr><td class="td-lbl">Brand</td><td class="td-col">:</td><td class="td-val">{{ $brandText }}</td></tr>
                        <tr><td class="td-lbl">Product</td><td class="td-col">:</td><td class="td-val">{{ ucwords(strtolower($labelData['product_name'] ?? '-')) }}</td></tr>
                        <tr><td class="td-lbl">Fit</td><td class="td-col">:</td><td class="td-val">{{ ucwords(strtolower($labelData['fit'] ?? 'Tailor Fit')) }}</td></tr>
                        <tr><td class="td-lbl">Sleeve</td><td class="td-col">:</td><td class="td-val">{{ $sText }}</td></tr>
                        <tr><td class="td-lbl">Colour</td><td class="td-col">:</td><td class="td-val">{{ ucwords(strtolower($labelData['color'] ?? '-')) }}</td></tr>
                        <tr><td class="td-lbl">Fabric</td><td class="td-col">:</td><td class="td-val">{{ ucwords(strtolower($labelData['fabric'] ?? 'Cotton')) }}</td></tr>
                        <tr><td class="td-lbl">Net Quantity</td><td class="td-col">:</td><td class="td-val">{{ $labelData['quantity'] ?? '1 Number' }}</td></tr>
                        <tr><td class="td-lbl" style="padding-top: 3mm;">Art No</td><td class="td-col" style="padding-top: 3mm;">:</td><td class="td-val font-bebas" style="padding-top: 3mm; font-size: 12pt; letter-spacing: 0.5px;">{{ $labelData['design'] ?? '-' }}</td></tr>
                    </table>
                    <div class="tag-qr-section">
                        {!! QrCode::size(50)->generate($qrString) !!}
                    </div>
                    <div class="lot-vertical">Lot: {{ $labelData['lot_no'] ?? '' }}</div>
                </div>
                
                <div class="tag-footer-info f-6 hide-for-print">
                    <div class="f-5" style="color: #000000; margin-bottom: 0.5mm;">Manufactured & Marketed by:</div>
                    <div class="fw-bold" style="font-size: 5pt; margin-bottom: 0.5mm;">{{ $companyName }}</div>
                    <div style="font-size: 4pt; line-height: 1.2;">
                        {{ $labelData['company_address'] ?? '272/2, Somu Nager, Sringeri Nagar, By Pass Road, Madurai - 625 016.' }}
                    </div>
                    
                    <div class="tag-separator"></div>
                    
                    <table class="complaints-table">
                        <tr>
                            <td class="comp-lbl">Complaints</td>
                            <td class="comp-col">:</td>
                            <td class="comp-val fw-bold">{{ $phones }}</td>
                        </tr>
                        <tr>
                            <td class="comp-lbl">Email</td>
                            <td class="comp-col">:</td>
                            <td class="comp-val">{{ $firstEmail }}</td>
                        </tr>
                    </table>
                    <div class="tag-separator"></div>
                </div>
                
                <div class="mrp-section">
                    <div class="fw-bold" style="font-size: 6.2pt; color: #000000;">MRP <span class="fw-bold" style="font-size: 6.5pt; color: #000000;">(inclusive of all taxes)</span></div>
                    <div class="fw-bold" style="font-size: 11pt; line-height: 1; color: #000000; margin: 0.8mm 0;">₹ {{ number_format((float) ($labelData['price'] ?? 0), 2) }}</div>
                    <div class="fw-bold" style="font-size: 4pt; color: #000000; margin-top: 0.5mm;">MADE IN INDIA WITH PRIDE</div>
                </div>
        </div>
    @endforeach
</body>
</html>

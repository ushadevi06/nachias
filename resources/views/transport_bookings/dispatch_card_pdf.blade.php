<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Casino Package Dispatch Notification - {{ $invoice_no }}</title>
    @php
        $truckPath = public_path('assets/images/dispatch_exact/truck_hd.jpg');
        $truckBase64 = file_exists($truckPath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($truckPath)) : '';

        $wavePath = public_path('assets/images/dispatch_exact/bottom_wave.png');
        $waveBase64 = file_exists($wavePath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($wavePath)) : '';

        // Exact Modern Geometric Sans-Serif Font Family (Poppins) matching client reference
        $fReg = str_replace('\\', '/', public_path('assets/fonts/Poppins-Regular.ttf'));
        $fMed = str_replace('\\', '/', public_path('assets/fonts/Poppins-Medium.ttf'));
        $fSemi = str_replace('\\', '/', public_path('assets/fonts/Poppins-SemiBold.ttf'));
        $fBold = str_replace('\\', '/', public_path('assets/fonts/Poppins-Bold.ttf'));
        $fExBold = str_replace('\\', '/', public_path('assets/fonts/Poppins-ExtraBold.ttf'));
        $fBlack = str_replace('\\', '/', public_path('assets/fonts/Poppins-Black.ttf')); 
        $opensans = str_replace('\\', '/', public_path('assets/fonts/OpenSans-Bold.ttf'));
        $roboto = str_replace('\\', '/', public_path('assets/fonts/Roboto-Bold.ttf'));
        $robotoregular = str_replace('\\', '/', public_path('assets/fonts/Roboto-Regular.ttf'));

        // Customer name smart font sizing
        $cName = $customer_name;
        $cLen = strlen($cName);
        if ($cLen > 42) {
            $custFs = '13px';
            $custLh = '1.14';
        } elseif ($cLen > 28) {
            $custFs = '15.5px';
            $custLh = '1.16';
        } elseif ($cLen > 18) {
            $custFs = '18px';
            $custLh = '1.18';
        } else {
            $custFs = '20.5px';
            $custLh = '1.22';
        }
    @endphp
    <style>
        /* Exact Modern Geometric Sans-Serif Font Family (Poppins) Embedded Directly for DomPDF */
        @font-face {
            font-family: 'GeometricSans';
            font-style: normal;
            font-weight: normal;
            src: url('{{ $fReg }}') format('truetype');
        }
        @font-face {
            font-family: 'GeometricSans';
            font-style: normal;
            font-weight: bold;
            src: url('{{ $fBold }}') format('truetype');
        }
        @font-face {
            font-family: 'OpenSansBold';
            font-style: normal;
            font-weight: bold;
            src: url('{{ $opensans }}') format('truetype');
        }
        @font-face {
            font-family: 'GeometricSansMedium';
            font-style: normal;
            font-weight: normal;
            src: url('{{ $fMed }}') format('truetype');
        }
        @font-face {
            font-family: 'GeometricSansMedium';
            font-style: normal;
            font-weight: bold;
            src: url('{{ $fMed }}') format('truetype');
        }
        @font-face {
            font-family: 'GeometricSansSemiBold';
            font-style: normal;
            font-weight: normal;
            src: url('{{ $fSemi }}') format('truetype');
        }
        @font-face {
            font-family: 'GeometricSansSemiBold';
            font-style: normal;
            font-weight: bold;
            src: url('{{ $fSemi }}') format('truetype');
        }
        @font-face {
            font-family: 'GeometricSansBold';
            font-style: normal;
            font-weight: normal;
            src: url('{{ $fBold }}') format('truetype');
        }
        @font-face {
            font-family: 'GeometricSansBold';
            font-style: normal;
            font-weight: bold;
            src: url('{{ $fBold }}') format('truetype');
        }
        @font-face {
            font-family: 'GeometricSansExtraBold';
            font-style: normal;
            font-weight: normal;
            src: url('{{ $fExBold }}') format('truetype');
        }
        @font-face {
            font-family: 'GeometricSansExtraBold';
            font-style: normal;
            font-weight: bold;
            src: url('{{ $fExBold }}') format('truetype');
        }
        @font-face {
            font-family: 'GeometricSansBlack';
            font-style: normal;
            font-weight: normal;
            src: url('{{ $fBlack }}') format('truetype');
        }
        @font-face {
            font-family: 'GeometricSansBlack';
            font-style: normal;
            font-weight: bold;
            src: url('{{ $fBlack }}') format('truetype');
        }
        @font-face {
            font-family: 'RobotoBold';
            font-style: normal;
            font-weight: bold;
            src: url('{{ $roboto }}') format('truetype');
        }
        @font-face {
            font-family: 'RobotoRegular';
            font-style: normal;
            font-weight: normal;
            src: url('{{ $robotoregular }}') format('truetype');
        }

        @page {
            size: A4 portrait;
            margin: 3mm 10mm 0mm 10mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 0;
            background: #ffffff;
            font-family: 'GeometricSans', 'Poppins', sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.15;
        }
        .header-table {
            width: 100%;
            margin-bottom: 2px;
            text-align: right;
        }

        .header-table img {
            width: 165px;
            height: auto;
            display: inline-block;
        }
        .truck-container {
            text-align: center;
            margin: 0 0 2px 0;
        }
        .truck-container img {
            width: 83%;
            height: auto;
            display: inline-block;
        }
        .headline-title {
            font-family: 'GeometricSansBlack', 'GeometricSansExtraBold', sans-serif;
            font-size: 52px;
            font-weight: bold;
            color: #0052cc;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 0 0 -2px 0;
            text-align: center;
            line-height: 0.92;
        }
        .headline-sub {
            font-family: 'OpenSansBold', sans-serif;
            font-size: 18.5px;
            color: #1e293b;
            margin: 2px 0 11px 0;
            text-align: center;
            line-height: 1.1;
            font-weight: bold;
        }

        /* 2-Row 3-Col Shipment Details Panel */
        .shipment-panel {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #bcd8fa;
            border-radius: 12px;
            background-color: #edf5ff;
            margin-bottom: 11px;
        }
        .shipment-cell {
            padding: 17px 11px;
            vertical-align: middle;
        }
        .cell-border-r {
            border-right: 1.5px solid #d0e2ff;
        }
        .cell-border-b {
            border-bottom: 1.5px solid #d0e2ff;
        }
        .inner-table {
            width: 100%;
            border-collapse: collapse;
        }
        .inner-icon-td {
            width: 48px;
            vertical-align: middle;
            text-align: left;
        }
        .inner-text-td {
            vertical-align: middle;
            padding-left: 8px;
        }
        .card-label {
            font-family: 'GeometricSansSemiBold', 'GeometricSansBold', sans-serif;
            font-size: 13.5px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 2px;
        }
        .card-value {
            font-family: 'RobotoBold', sans-serif;
            font-size: 22.5px;
            font-weight: bold;
            color: #0f172a;
            line-height: 1.18;
        }

        /* QR Code & Features Panel */
        .qr-panel {
            width: 100%;
            border-collapse: collapse;
            background-color: #edf5ff;
            border: 2px solid #bcd8fa;
            border-radius: 12px;
            margin-bottom: 10px;
        }
        .qr-left-td {
            width: 215px;
            padding: 10px 8px;
            border-right: 1.5px solid #bcd8fa;
            text-align: center;
            vertical-align: middle;
        }
        .qr-frame {
            display: inline-block;
            background: transparent;
            padding: 0;
            border: none;
        }
        .qr-right-td {
            padding: 10px 14px;
            vertical-align: middle;
        }
        .scan-title {
            font-family: 'GeometricSansBlack', 'GeometricSansExtraBold', sans-serif;
            font-size: 35px;
            font-weight: bold;
            color: #0058b3;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 0 0 2px 0;
        }
        .scan-subtitle {
            font-family: 'GeometricSansSemiBold', 'GeometricSansBold', sans-serif;
            font-size: 15.5px;
            font-weight: bold;
            color: #1e293b;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 0 0 8px 0;
        }
        .features-table {
            width: 100%;
            border-collapse: collapse;
        }
        .feature-cell {
            width: 25%;
            text-align: center;
            vertical-align: top;
            padding: 1px 2px;
        }
        .feature-label {
            font-family: 'GeometricSansSemiBold', 'GeometricSansBold', sans-serif;
            font-size: 12.5px;
            font-weight: bold;
            color: #132343;
            margin-top: 3px;
            line-height: 1.05;
        }

        /* Footer Panel */
        .footer-panel {
            width: 100%;
            border-collapse: collapse;
            background-color: #f0f7ff;
            border: 1.5px solid #bcd8fa;
            border-radius: 12px;
            margin-bottom: 7px;
        }
        .footer-icon-td {
            width: 76px;
            padding: 12px 12px;
            border-right: 1.5px solid #e2e8f0;
            text-align: center;
            vertical-align: middle;
        }
        .footer-content-td {
            padding: 10px 15px;
            vertical-align: middle;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-headline {
            font-family: 'RobotoBold', sans-serif;
            font-size: 16.5px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 2px;
        }
        .footer-desc {
            font-family: 'RobotoRegular', sans-serif;
            font-size: 14px;
            color: #454858;
            margin-bottom: 2px;
        }
        .footer-brand {
            font-family: 'RobotoRegular', sans-serif;
            font-size: 14.5px;
            font-weight: bold;
            color: #15529a;
        }
        .bottom-wave-container {
            width: 100%;
            text-align: center;
            line-height: 0;
            margin-top: 0px;
        }
        .bottom-wave-container img {
            width: 100%;
            height: auto;
            display: block;
        }
    </style>
</head>
<body>
    <!-- 1. Header (Top Right Casino) -->
    <div class="header-table">
        <img src="{{ $casino_logo }}" alt="">
    </div>

    <!-- Delivery Truck Hero Image (Enlarged 80% to Fill Upper Page & Remove Footer Gap) -->
    @if($truckBase64)
        <div class="truck-container">
            <img src="{{ $truckBase64 }}" alt="Delivery Truck">
        </div>
    @endif

    <!-- 2. Main Heading (Thick, Rounded, Extra-Bold Geometric Sans-Serif) -->
    <div class="headline-title">SEE YOU SOON!</div>
    <div class="headline-sub">Your package is out for delivery.</div>

    <!-- 3. Shipment Details Panel (Proportional 2-Row 3-Col Box with Dynamic Text Sizing) -->
    <table class="shipment-panel">
        <!-- Row 1: Customer, Invoice, Cartons -->
        <tr>
            <!-- Customer -->
            <td class="shipment-cell cell-border-r cell-border-b" style="width: 42%;">
                <table class="inner-table">
                    <tr>
                        <td class="inner-icon-td">
                            <img src="{{ $img_store }}" width="46" height="46" alt="Store">
                        </td>
                        <td class="inner-text-td">
                            <div class="card-label">CUSTOMER</div>
                            <div class="card-value" style="font-size: {{ $custFs }}; line-height: {{ $custLh }};">{{ $cName }}</div>
                        </td>
                    </tr>
                </table>
            </td>

            <!-- Invoice -->
            <td class="shipment-cell cell-border-r cell-border-b" style="width: 29%;">
                <table class="inner-table">
                    <tr>
                        <td class="inner-icon-td">
                            <img src="{{ $img_invoice }}" width="46" height="46" alt="Invoice">
                        </td>
                        <td class="inner-text-td">
                            <div class="card-label">INVOICE</div>
                            <div class="card-value" style="white-space: nowrap;">{{ $invoice_no }}</div>
                        </td>
                    </tr>
                </table>
            </td>

            <!-- Cartons -->
            <td class="shipment-cell cell-border-b" style="width: 29%;">
                <table class="inner-table">
                    <tr>
                        <td class="inner-icon-td">
                            <img src="{{ $img_cartons }}" width="48" height="46" alt="Cartons">
                        </td>
                        <td class="inner-text-td">
                            <div class="card-label">CARTONS</div>
                            <div class="card-value" style="white-space: nowrap;">{{ $cartons }}</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Row 2: Total Qty, Articles, LR Number -->
        <tr>
            <!-- Total Qty -->
            <td class="shipment-cell cell-border-r" style="width: 42%;">
                <table class="inner-table">
                    <tr>
                        <td class="inner-icon-td">
                            <img src="{{ $img_shirt }}" width="46" height="46" alt="Qty">
                        </td>
                        <td class="inner-text-td">
                            <div class="card-label">TOTAL QTY</div>
                            <div class="card-value" style="white-space: nowrap;">{{ (int)$total_qty }} pcs</div>
                        </td>
                    </tr>
                </table>
            </td>

            <!-- Articles -->
            <td class="shipment-cell cell-border-r" style="width: 29%;">
                <table class="inner-table">
                    <tr>
                        <td class="inner-icon-td">
                            <img src="{{ $img_articles }}" width="46" height="46" alt="Articles">
                        </td>
                        <td class="inner-text-td">
                            <div class="card-label">ARTICLES</div>
                            <div class="card-value">{{ $articles_count }}</div>
                        </td>
                    </tr>
                </table>
            </td>

            <!-- LR Number -->
            <td class="shipment-cell" style="width: 29%;">
                <table class="inner-table">
                    <tr>
                        <td class="inner-icon-td">
                            <img src="{{ $img_truck }}" width="48" height="46" alt="LR">
                        </td>
                        <td class="inner-text-td">
                            <div class="card-label">LR NUMBER</div>
                            <div class="card-value" style="color: #0052cc; white-space: nowrap;">{{ $lr_no }}</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 4. QR Code & Document Information Panel -->
    <table class="qr-panel">
        <tr>
            <!-- QR Code (Left) -->
            <td class="qr-left-td">
                <div class="qr-frame">
                    @if(!empty($qr_base64))
                        <img src="{{ $qr_base64 }}" width="155" height="155" alt="QR Code" style="display: block; margin: 0 auto;">
                    @else
                        <div style="width: 155px; height: 155px; background: transparent; line-height: 155px;">QR CODE</div>
                    @endif
                </div>
            </td>

            <!-- Content (Right) -->
            <td class="qr-right-td">
                <div class="scan-title">SCAN TO VIEW</div>
                <div class="scan-subtitle">INVOICE + FULL DELIVERY CHALLAN</div>

                <!-- 4 Document Feature Icons -->
                <table class="features-table">
                    <tr>
                        <td class="feature-cell">
                            <img src="{{ $img_qr_inv }}" width="38" height="38" alt="Invoice PDF" style="display: block; margin: 0 auto;">
                            <div class="feature-label">Invoice<br>PDF</div>
                        </td>
                        <td class="feature-cell">
                            <img src="{{ $img_qr_challan }}" width="38" height="38" alt="Delivery Challan" style="display: block; margin: 0 auto;">
                            <div class="feature-label">Delivery<br>Challan</div>
                        </td>
                        <td class="feature-cell">
                            <img src="{{ $img_qr_carton }}" width="38" height="38" alt="Carton Details" style="display: block; margin: 0 auto;">
                            <div class="feature-label">Carton<br>Details</div>
                        </td>
                        <td class="feature-cell">
                            <img src="{{ $img_qr_lr }}" width="38" height="38" alt="LR Details" style="display: block; margin: 0 auto;">
                            <div class="feature-label">LR Details</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 5. Footer Panel with Parcel Icon -->
    <table class="footer-panel">
        <tr>
            <!-- Left: Parcel Box Icon -->
            <td class="footer-icon-td">
                <img src="{{ $img_footer_box }}" width="70" height="70" alt="Shipment Icon" style="display: block; margin: 0 auto;">
            </td>

            <!-- Right: Content & Meta Details -->
            <td class="footer-content-td">
                <table class="footer-table">
                    <tr>
                        <td>
                            <div class="footer-headline">Your shipment is on its way.</div>
                            <div class="footer-desc">Please keep your receiving team ready for a smooth and quick process.</div>
                            <div class="footer-brand">Thank you for choosing Casino!</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 6. Bottom Decorative Wave (Sitting Flush Under Footer) -->
    @if($waveBase64)
        <div class="bottom-wave-container">
            <img src="{{ $waveBase64 }}" alt="">
        </div>
    @endif
</body>
</html>

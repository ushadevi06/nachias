<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transport Sticker - {{ $invoice->inv_no }}</title>
    <style>
        @page {
            size: 100mm 150mm;
            margin: 0 !important;
        }
        @font-face {
            font-family: 'Bebas Neue';
            src: url('{{ asset('assets/fonts/BebasNeue-Regular.ttf') }}') format('truetype');
            font-weight: 400;
        }
        @font-face {
            font-family: 'Montserrat';
            src: url('{{ asset('assets/fonts/Montserrat-Regular.ttf') }}') format('truetype');
            font-weight: 400;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        html {
            width: 100mm;
            height: 150mm;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Montserrat', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            width: 100mm;
            height: 150mm;
            padding: 2mm;
            background: #fff;
            margin: 0;
        }
        .sticker {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #ccc;
            background: #fff;
        }
        .ship-to-bar {
            background: #000;
            color: #fff;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
        }
        .ship-to-bar::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #fff;
            margin-left: 12px;
        }
        .address-section {
            padding: 16px;
        }
        .company-heading {
            font-size: 14px;
            font-weight: bold;
            color: #000;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .customer-name {
            font-size: 38px;
            font-weight: normal;
            font-family: 'Bebas Neue', 'Segoe UI', sans-serif;
            margin-bottom: 2px;
            text-transform: uppercase;
            color: #000;
            line-height: 1.1;
            letter-spacing: 1px;
        }
        .customer-address {
            font-size: 14px;
            color: #000;
            margin-bottom: 8px;
            line-height: 1.4;
        }
        .city-badge {
            background: #1a1a1a;
            color: #fff;
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .city-badge svg {
            width: 14px;
            height: 14px;
            fill: #fff;
            margin-right: 6px;
        }
        .stats-section {
            border-top: 2px dashed #000;
            border-bottom: 2px dashed #000;
            display: flex;
            padding: 8px 16px;
        }
        .stat-box {
            flex: 1;
        }
        .stat-label {
            font-size: 10px;
            color: #000;
            letter-spacing: 1px;
            margin-bottom: 4px;
            text-transform: uppercase;
            font-weight: 600;
        }
        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #1a1a1a;
        }
        .stat-value.orange {
            color: #000;
            font-size: 24px;
        }
        .disclaimer-section {
            padding: 8px 16px;
            display: flex;
            align-items: flex-start;
            border-bottom: 2px dashed #000;
            margin-bottom: 5px;
        }
        .disclaimer-section svg {
            width: 20px;
            height: 20px;
            fill: #000;
            margin-right: 10px;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .disclaimer-text {
            font-size: 10px;
            color: #000;
            line-height: 1.4;
            font-family: 'Montserrat', 'Segoe UI', sans-serif;
        }
        .qr-section {
            padding: 6px 16px;
            margin-bottom: auto;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .qr-text {
            font-size: 10px;
            color: #000;
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        .header {
            background-color: #1a1a1a;
            padding: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-left {
            display: flex;
            flex-direction: column;
        }
        .company-name {
            color: #fff;
            font-size: 24px;
            font-weight: normal;
            font-family: 'Bebas Neue', sans-serif;
            letter-spacing: 2px;
            margin-bottom: 4px;
        }
        .company-sub {
            color: #fff;
            font-size: 9px;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 600;
        }
        .logo-box {
            background-color: #fff;
            color: #1a1a1a;
            font-size: 16px;
            font-weight: 900;
            width: 32px;
            height: 32px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 8px;
        }
        .footer {
            background: #1a1a1a;
            padding: 10px 16px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .footer-address {
            color: #fff;
            font-size: 10px;
            line-height: 1.5;
        }
        .footer-phone {
            color: #fff;
            font-size: 11px;
            font-weight: bold;
            text-align: right;
            line-height: 1.5;
            max-width: 40%;
        }
        @media print {
            .sticker {
                border: none;
                border-radius: 0;
            }
        }
    </style>
</head>
<body onload="{{ isset($is_print) && $is_print ? 'window.print()' : '' }}">
    <div class="sticker">
        
        <!-- Header -->
        <!-- <div class="header">
            <div class="header-left">
                <div class="company-name">NACHIAS FASHION</div>
                <div class="company-sub">PVT LTD &bull; MADURAI</div>
            </div>
            <div class="header-right">
                <div class="logo-box">N</div>
            </div>
        </div> -->

        <!-- Ship To Bar -->
        <div class="ship-to-bar">SHIP TO</div>

        <!-- Address Section -->
        <div class="address-section">
            <div class="customer-name">{{ $invoice->customer->name ?? 'N/A' }}</div>
            <div class="customer-address">
                {{ implode(', ', array_filter([$invoice->customer->address_line_1 ?? '', $invoice->customer->address_line_2 ?? '', $invoice->customer->address_line_3 ?? ''])) }}
                <p>{{ $invoice->customer->mobile_no }}</p>
            </div>
            <div class="city-badge">
                <svg viewBox="0 0 24 24">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                </svg>
                {{ $invoice->customer->city->city_name ?? ($invoice->customer->place->place_name ?? 'N/A') }}
            </div>
        </div>

        <!-- Stats Section -->
        <div class="stats-section">
            <div class="stat-box" style="flex: 1.5;">
                <div class="stat-label">INVOICE NO.</div>
                <div class="stat-value">{{ strtoupper($invoice->inv_no) }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">PIECES</div>
                <div class="stat-value orange">{{ intval($totalPcs) }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">BOXES</div>
                <div class="stat-value orange">{{ $invoice->no_of_box ?: '-' }}</div>
            </div>
        </div>

        <!-- Disclaimer Section -->
        <div class="disclaimer-section">
            <svg viewBox="0 0 24 24">
                <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
            </svg>
            <div class="disclaimer-text">
                The original invoice for your purchase will be securely placed inside the carton in a Casino envelope.
            </div>
        </div>

        <!-- QR Code Section -->
        <div class="qr-section">
            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(70)->margin(0)->generate($invoice->qr_details ?? url('sales_invoices/view/' . $invoice->id)) !!}
            <div class="qr-text">Scan for Details</div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-address">
                <strong>NACHIAS FASHION PVT LTD</strong><br>
                272/2, Somunagar, Sringeri Nagar,<br>
                By-Pass Road, Madurai - 625 016.
            </div>
            <div class="footer-phone">
                {{ $setting->toll_free_no ?? '' }}
            </div>
        </div>

    </div>
</body>
</html>

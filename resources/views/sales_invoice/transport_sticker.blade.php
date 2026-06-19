<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transport Sticker - {{ $invoice->inv_no }}</title>
    <style>
        @page {
            size: 100mm 150mm;
            margin: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial Narrow', Arial, sans-serif;
            width: 100mm;
            height: 150mm;
            padding: 2mm;
            background: #fff;
            color: #000;
        }
        .sticker {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .section {
            width: 100%;
            border-bottom: 2px solid #000;
            padding: 8px 10px;
        }
        .section:last-child {
            border-bottom: none;
        }
        .ship-to-label {
            font-size: 22px;
            font-weight: 900;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }
        .ship-row {
            display: flex;
            align-items: flex-start;
            margin-bottom: 8px;
        }
        .ship-row:last-child {
            margin-bottom: 0;
        }
        .icon-box {
            width: 28px;
            height: 28px;
            background: #000;
            color: #fff;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
        }
        .icon-box svg {
            width: 18px;
            height: 18px;
            fill: currentColor;
        }
        .val-party {
            font-size: 26px;
            font-weight: bold;
            line-height: 1.1;
            text-transform: uppercase;
        }
        .val-address {
            font-size: 20px;
            font-weight: bold;
            line-height: 1.2;
            text-transform: uppercase;
        }
        .val-place {
            font-size: 34px;
            font-weight: 900;
            font-family: 'Arial Black', Impact, sans-serif;
            text-transform: uppercase;
            line-height: 1;
        }
        .invoice-section {
            display: flex;
            align-items: center;
            padding: 8px 10px;
        }
        .invoice-val {
            font-size: 24px;
            font-weight: 900;
            font-family: 'Arial Black', Impact, sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stats-section {
            display: flex;
            padding: 0;
            flex: 1;
        }
        .stats-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px 5px;
        }
        .stats-col.border-right {
            border-right: 2px solid #000;
        }
        .badge {
            background: #000;
            color: #fff;
            font-size: 18px;
            font-weight: bold;
            padding: 4px 10px;
            border-radius: 2px;
            text-align: center;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }
        .stat-value {
            font-size: 64px;
            font-weight: 900;
            font-family: 'Arial Black', Impact, sans-serif;
            line-height: 1;
        }
        .disclaimer-section {
            font-size: 13.5px;
            font-weight: bold;
            line-height: 1.35;
            padding: 8px 10px;
            text-align: left;
        }
        .footer-section {
            border-top: 2px solid #000;
            padding: 8px 10px;
            text-align: center;
            background: #fff;
        }
        .footer-title {
            font-size: 18px;
            font-weight: 900;
            font-family: 'Arial Black', Impact, sans-serif;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            white-space: nowrap;
        }
        .footer-address {
            font-size: 13px;
            font-weight: bold;
            line-height: 1.3;
            text-transform: uppercase;
        }
        @media print {
            body {
                width: 100mm;
                height: 150mm;
                padding: 2mm;
            }
            .sticker {
                border: none;
            }
        }
    </style>
</head>
<body onload="{{ isset($is_print) && $is_print ? 'window.print()' : '' }}">
    <div class="sticker">
        <!-- SHIP TO Section -->
        <div class="section">
            <div class="ship-to-label">SHIP TO:</div>
            
            <div class="ship-row">
                <div class="icon-box">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div>
                <div class="val-party">{{ strtoupper($invoice->customer->name ?? 'N/A') }}</div>
            </div>
            
            <div class="ship-row">
                <div class="icon-box">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                </div>
                <div class="val-address">{{ strtoupper(implode(', ', array_filter([$invoice->customer->address_line_1 ?? '', $invoice->customer->address_line_2 ?? '', $invoice->customer->address_line_3 ?? '']))) }}</div>
            </div>
            
            <div class="ship-row">
                <div class="icon-box">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                </div>
                <div class="val-place">{{ strtoupper($invoice->customer->city->city_name ?? ($invoice->customer->place->place_name ?? 'N/A')) }}</div>
            </div>
        </div>

        <!-- Invoice Info Section -->
        <div class="section invoice-section">
            <div class="icon-box" style="margin-bottom: 0;">
                <svg viewBox="0 0 24 24">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                </svg>
            </div>
            <div class="invoice-val">INVOICE NO: {{ strtoupper($invoice->inv_no) }}</div>
        </div>

        <!-- Stats Section (No of Pcs & Boxes) -->
        <div class="section stats-section">
            <div class="stats-col border-right">
                <div class="badge">NO OF PCS</div>
                <div class="stat-value">{{ intval($totalPcs) }}</div>
            </div>
            <div class="stats-col">
                <div class="badge">NO OF BOXES</div>
                <div class="stat-value">{{ $boxCount }}</div>
            </div>
        </div>

        <!-- Disclaimer Section -->
        <div class="section disclaimer-section">
            The original invoice for your purchase will be securely placed inside the carton in a CASINO envelope
        </div>

        <!-- Footer Section -->
        <div class="footer-section">
            <div class="footer-title">NACHIAS FASHION PVT LTD</div>
            <div class="footer-address">
                272/2, SOMUNAGAR, SRINGERI NAGAR,<br>
                BY-PASS ROAD, MADURAI - 625 016.<br>
                MOBILE: 8489938071 M:8489938073
            </div>
        </div>
    </div>
</body>
</html>

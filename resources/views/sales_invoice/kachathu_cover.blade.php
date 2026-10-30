<!DOCTYPE html>
<html lang="ta">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kachathu Cover - {{ $invoice->inv_no }}</title>
    <style>
        @page {
            size: 6.5in 4.1in; /* Exact Custom Envelope Cover Size (6.5 inches x 4.1 inches) */
            margin: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body {
            font-family: 'Mukta Malar', 'Latha', 'Segoe UI', Arial, sans-serif;
            background: #fff;
            color: #000;
            width: 6.5in;
            height: 4.1in;
            margin: 0 auto;
            padding: 0.12in;
            -webkit-font-smoothing: antialiased;
        }
        .cover-wrapper {
            width: 100%;
            height: 100%;
            border: 2px solid transparent;
            border-radius: 6px;
            padding: 8px 12px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: visible;
            background: transparent;
        }

        /* Top Header */
        .top-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid transparent;
            padding-bottom: 6px;
        }
        .brand-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .brand-logo-img {
            height: 32px;
            width: auto;
            object-fit: contain;
            visibility: hidden !important;
        }
        .company-logo-img {
            height: 22px;
            width: auto;
            object-fit: contain;
            vertical-align: middle;
            margin-right: 4px;
            visibility: hidden !important;
        }
        .brand-logo-text {
            font-size: 25px;
            font-weight: 900;
            letter-spacing: 2px;
            text-transform: uppercase;
            visibility: hidden !important;
        }
        .box-title-pill {
            padding: 4px 14px;
            border-radius: 14px;
            font-size: 15px;
            font-weight: bold;
            display: inline-block;
            visibility: hidden !important;
        }
        .box-count-box {
            display: inline-block;
            border: 2px solid transparent;
            width: 46px;
            height: 32px;
            line-height: 30px;
            text-align: center;
            font-family: Arial, 'Segoe UI', sans-serif !important;
            font-size: 24px !important;
            font-weight: 900 !important;
            margin-left: -76px;
            position: relative;
            top: 2px;
            vertical-align: middle;
            border-radius: 4px;
            color: #000 !important;
        }
        .payment-mode-section {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 24px;
            font-weight: 900;
            letter-spacing: 1px;
            visibility: hidden !important;
        }

        /* Main To Address Section */
        .to-section {
            border: 2px solid transparent;
            border-radius: 6px;
            padding: 0 14px;
            margin: 0;
            display: block;
        }
        .to-title {
            display: none !important;
        }
        .to-content {
            padding-left: 12px;
            color: #000;
            display: block;
        }
        .customer-name {
            font-size: 18px;
            font-weight: 900;
            color: #000;
            height: 31px;
            line-height: 30px;
            white-space: nowrap;
        }
        .address-line {
            font-size: 18px;
            font-weight: 900 !important;
            color: #000;
            height: 31px;
            line-height: 30px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .place-line {
            font-size: 18px;
            font-weight: 900 !important;
            color: #000;
            height: 31px;
            line-height: 30px;
            white-space: nowrap;
        }
        .phone-line {
            font-size: 19px;
            font-weight: 900;
            letter-spacing: 1.5px;
            color: #000;
            height: 31px;
            line-height: 30px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .phone-svg {
            display: inline-block;
            vertical-align: middle;
            fill: #000;
            width: 17px;
            height: 17px;
            flex-shrink: 0;
        }

        /* Bottom Footer Row */
        .bottom-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-top: 2px solid transparent;
            padding-top: 14px;
        }
        .from-section {
            width: 48%;
            visibility: hidden !important;
        }
        .from-title {
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }
        .company-name {
            font-size: 15px;
            font-weight: 900;
            margin: 2px 0 2px 0;
        }
        .company-address {
            font-size: 11px;
            line-height: 1.4;
            font-weight: 600;
        }

        .transport-section {
            width: 50%;
            font-size: 12px;
            font-weight: bold;
            padding-top: 48px;
        }
        .transport-row {
            margin-bottom: 2px;
            display: flex;
            align-items: center;
        }
        .transport-label {
            width: 74px;
            min-width: 74px;
            max-width: 74px;
            display: inline-block;
            font-size: 13px;
            visibility: hidden !important;
        }
        .transport-value {
            border-bottom: 1.5px solid transparent;
            flex-grow: 1;
            padding-left: 2px;
            font-size: 15px;
            font-weight: 900;
            min-height: 22px;
            line-height: 22px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #000;
        }

        /* Print Button / Controls (Screen only) */
        @media screen {
            body {
                background: #eef2f5;
                padding: 20px;
                display: flex;
                flex-direction: column;
                align-items: center;
                height: auto;
            }
            .cover-wrapper {
                background: #fff;
                width: 6.5in;
                height: 4.1in;
                box-shadow: 0 4px 20px rgba(0,0,0,0.08);
                border: 1px solid #cbd5e1;
            }
            .screen-controls {
                width: 6.5in;
                margin-bottom: 12px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .btn {
                padding: 8px 16px;
                font-size: 14px;
                font-weight: bold;
                border: none;
                border-radius: 6px;
                cursor: pointer;
            }
            .btn-primary {
                background: #8c57ff;
                color: #fff;
            }
            .btn-close {
                background: #6c757d;
                color: #fff;
            }
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
                width: 6.5in;
                height: 4.1in;
                background: transparent !important;
            }
            .cover-wrapper {
                width: 6.5in;
                height: 4.1in;
                border: none !important;
                box-shadow: none !important;
                background: transparent !important;
                page-break-inside: avoid;
            }
            .screen-controls {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="screen-controls">
        <h4 style="color: #333; font-family: sans-serif;">Kachathu / Parcel Cover Preview</h4>
        <div>
            <button type="button" class="btn btn-primary" onclick="window.print()">🖨️ Print Cover</button>
            <button class="btn btn-close" onclick="window.close()">Close</button>
        </div>
    </div>

    @php
        $customer = $invoice->customer;
        $details = $customDetails ?? [];
        
        $cName = !empty($details['tamil_name']) ? $details['tamil_name'] : ($customer->tamil_name ?? $customer->name ?? '-');
        $cAddr = !empty($details['tamil_address']) ? $details['tamil_address'] : ($customer->tamil_address ?? $customer->address_line_1 ?? '');
        $cPlace = !empty($details['tamil_place']) ? $details['tamil_place'] : ($customer->tamil_place ?? optional($customer->place)->place_name ?? '');
        $cZip = $customer->zip_code ?? '';
        $cMobile = !empty($details['mobile']) ? $details['mobile'] : ($customer->mobile_no ?? '-');
        
        $brandName = optional($invoice->brand)->brand_name ?? 'CASINO';
        $boxes = $details['no_of_box'] ?? ($invoice->no_of_box ?: 1);
        $payMode = strtoupper($invoice->payment_mode ?? 'TO PAY');
        if (empty($payMode) || $payMode === 'CREDIT') {
            $payMode = 'TO PAY';
        }
        
        $lorryOffice = $details['lorry_office'] ?? ($invoice->transporter_name ?: ($customer->transport_name ?? ''));
        $bookingOffice = $details['booking_office'] ?? ($customer->booking_office ?? '');
    @endphp

    <div class="cover-wrapper">
        <!-- Top Row -->
        <div class="top-row">
            <div class="brand-section">
                <img src="{{ asset('assets/images/casino_shield_clean.png') }}" alt="Casino Logo" class="brand-logo-img">
                <span class="brand-logo-text">CASINO</span>
                <span class="box-title-pill">ஜவுளி அட்டை பெட்டி</span>
                <span class="box-count-box">{{ $boxes }}</span>
            </div>
            <div class="payment-mode-section">
                <!-- SVG Wi-Fi / Wave icon exact like the cover -->
                <svg width="34" height="28" viewBox="0 0 24 24" fill="none" stroke="#0b2265" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12.55a11 11 0 0 1 14.08 0"></path>
                    <path d="M1.42 9a16 16 0 0 1 21.16 0"></path>
                    <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
                    <line x1="12" y1="20" x2="12.01" y2="20"></line>
                </svg>
                <span>{{ $payMode }}</span>
            </div>
        </div>

        <!-- To Address -->
        <div class="to-section">
            <div class="to-title">To :</div>
            <div class="to-content">
                <div class="customer-name">{{ $cName }}</div>
                <div class="address-line">{{ $cAddr }}</div>
                <div class="place-line">{{ $cPlace }} {{ $cZip }}</div>
                @if($cMobile && $cMobile !== '-')
                    <div class="phone-line">
                        <svg class="phone-svg" width="17" height="17" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M6.62 10.79a15.053 15.053 0 006.59 6.59l2.2-2.2a1 1 0 011.02-.24c1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                        </svg>
                        <span>{{ $cMobile }}</span>
                    </div>
                @endif
            </div>
        </div>
        <!-- Bottom Row -->
        <div class="bottom-row">
            <div class="from-section">
                <div class="from-title">From :</div>
                <div class="company-name">
                    @if(file_exists(public_path('assets/images/logo_2.png')))
                        <img src="{{ asset('assets/images/logo_2.png') }}" alt="Logo" class="company-logo-img">
                    @endif
                    {{ $setting->company_name ?? 'Nachias Fashion Pvt Ltd' }}
                </div>
                <div class="company-address">
                    @if(!empty($setting->address))
                        {!! nl2br(e($setting->address)) !!}
                        @if(!empty($setting->zip_code))
                            - {{ $setting->zip_code }}
                        @endif
                    @else
                        272/2, Somu Nagar, Sringeri Nagar,<br>
                        By Pass Road, Madurai - 625016.
                    @endif
                    @php
                        $phones = array_filter([$setting->phone_number ?? null, $setting->toll_free_no ?? null]);
                    @endphp
                    @if(!empty($phones))
                        <br>Contact : {{ $setting->toll_free_no  }}
                    @endif
                </div>
            </div>

            <div class="transport-section">
                <div class="transport-row">
                    <span class="transport-label">லாரி ஆபீஸ் :</span>
                    <span class="transport-value">{{ $lorryOffice }}</span>
                </div>
                <div class="transport-row">
                    <span class="transport-label">புக்கிங் ஆபீஸ் :</span>
                    <span class="transport-value">{{ $bookingOffice }}</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        @if(request()->get('autoprint'))
            window.onload = function() {
                window.print();
            };
        @endif
    </script>
</body>
</html>


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
            color: #0b2265; /* Deep Navy Blue matching the photo */
            width: 6.5in;
            height: 4.1in;
            margin: 0 auto;
            padding: 0.12in;
            -webkit-font-smoothing: antialiased;
        }
        .cover-wrapper {
            width: 100%;
            height: 100%;
            border: 2px solid #0b2265;
            border-radius: 6px;
            padding: 8px 12px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            background: #fff;
        }

        /* Top Header */
        .top-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #0b2265;
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
        }
        .company-logo-img {
            height: 22px;
            width: auto;
            object-fit: contain;
            vertical-align: middle;
            margin-right: 4px;
        }
        .brand-logo-text {
            font-size: 25px;
            font-weight: 900;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .box-title-pill {
            background-color: #0b2265;
            color: #fff;
            padding: 4px 14px;
            border-radius: 14px;
            font-size: 15px;
            font-weight: bold;
            display: inline-block;
        }
        .box-count-box {
            display: inline-block;
            border: 2px solid #0b2265;
            min-width: 40px;
            height: 32px;
            line-height: 28px;
            text-align: center;
            font-size: 20px;
            font-weight: 900;
            margin-left: 4px;
            vertical-align: middle;
            border-radius: 4px;
        }
        .payment-mode-section {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 24px;
            font-weight: 900;
            letter-spacing: 1px;
        }

        /* Main To Address Section */
        .to-section {
            border: 2px solid #0b2265;
            border-radius: 6px;
            padding: 8px 14px;
            margin: 6px 0;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }
        .to-title {
            font-size: 15px;
            font-weight: 900;
            margin-bottom: 4px;
        }
        .to-content {
            padding-left: 12px;
            font-size: 18px;
            line-height: 1.5;
            font-weight: bold;
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            flex-grow: 1;
        }
        .customer-name {
            font-size: 25px;
            font-weight: 900;
            color: #000;
            margin-bottom: 4px;
            line-height: 1.25;
        }
        .phone-line {
            font-size: 22px;
            font-weight: 900;
            letter-spacing: 1.5px;
            color: #000;
            margin-top: 4px;
        }

        /* Bottom Footer Row */
        .bottom-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-top: 2px solid #0b2265;
            padding-top: 6px;
        }
        .from-section {
            width: 55%;
        }
        .from-title {
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            color: #0b2265;
        }
        .company-name {
            font-size: 15px;
            font-weight: 900;
            margin: 2px 0 2px 0;
            color: #0b2265;
        }
        .company-address {
            font-size: 11px;
            line-height: 1.4;
            color: #0b2265;
            font-weight: 600;
        }

        .transport-section {
            width: 43%;
            font-size: 12px;
            font-weight: bold;
        }
        .transport-row {
            margin-bottom: 4px;
            display: flex;
            align-items: center;
        }
        .transport-label {
            min-width: 90px;
            font-size: 13px;
        }
        .transport-value {
            border-bottom: 1.5px solid #0b2265;
            flex-grow: 1;
            padding-left: 6px;
            font-size: 15px;
            font-weight: 900;
            min-height: 22px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
                box-shadow: 0 8px 30px rgba(0,0,0,0.15);
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
            }
            .cover-wrapper {
                width: 6.5in;
                height: 4.1in;
                border: 2px solid #0b2265;
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
                @if($cAddr)
                    <div>{{ $cAddr }}</div>
                @endif
                <div>{{ $cPlace }} {{ $cZip }}</div>
                @if($cMobile && $cMobile !== '-')
                    <div class="phone-line">{{ $cMobile }}</div>
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
        // Auto trigger print when page opens if requested
        @if(request()->get('autoprint'))
            window.onload = function() {
                window.print();
            };
        @endif
    </script>
</body>
</html>


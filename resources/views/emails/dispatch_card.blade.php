<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
    <meta name="x-apple-disable-message-reformatting">
    <title>See You Soon! Your Package is Out for Delivery [{{ $invoice_no ?? '' }}] - Casino</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800;900&family=Open+Sans:wght@600;700&display=swap" rel="stylesheet">
    <style>
        /* Base Resets */
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            background-color: #f1f5f9;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
            border-collapse: collapse !important;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            display: block;
        }
        * {
            box-sizing: border-box;
        }
        /* Mobile Responsiveness */
        @media only screen and (max-width: 620px) {
            .email-outer-wrapper {
                padding: 10px 6px 12px 6px !important;
            }
            .email-main-card {
                width: 100% !important;
                padding: 0 !important;
                border-radius: 12px !important;
            }
            .card-content-cell {
                padding-left: 14px !important;
                padding-right: 14px !important;
            }
            .headline-main {
                font-size: 27px !important;
            }
            .headline-sub {
                font-size: 14px !important;
                margin-bottom: 14px !important;
            }
            .truck-hero-img {
                max-width: 90% !important;
            }
            .details-cell {
                padding: 10px 6px !important;
            }
            .details-icon {
                width: 32px !important;
                height: 32px !important;
            }
            .details-label {
                font-size: 9.5px !important;
            }
            .details-value {
                font-size: 13px !important;
            }
            .qr-panel-left {
                width: 100% !important;
                display: block !important;
                border-right: none !important;
                border-bottom: 1.5px solid #bcd8fa !important;
                padding: 14px 10px !important;
            }
            .qr-panel-right {
                width: 100% !important;
                display: block !important;
                padding: 14px 10px !important;
            }
            .scan-title {
                font-size: 22px !important;
                text-align: center !important;
            }
            .scan-subtitle {
                font-size: 11.5px !important;
                text-align: center !important;
            }
            .feature-icon {
                width: 30px !important;
                height: 30px !important;
            }
            .feature-text {
                font-size: 9.5px !important;
            }
            .footer-panel-icon {
                width: 60px !important;
                padding: 10px 6px !important;
            }
            .footer-panel-box {
                width: 44px !important;
                height: 44px !important;
            }
            .footer-text-1 {
                font-size: 13px !important;
            }
            .footer-text-2 {
                font-size: 11px !important;
            }
            .footer-text-3 {
                font-size: 11.5px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b;">

@php
    // Determine the most reliable public asset host (use HTTPS production ERP domain for universally loading images)
    $assetHost = 'https://erp.nachias.com';
    if (!app()->runningInConsole() && request()->getHost() && !in_array(request()->getHost(), ['localhost', '127.0.0.1'])) {
        $assetHost = request()->getSchemeAndHttpHost();
    }

    // Dynamic customer name font sizing to keep cleanly on at most 2 lines
    $cName = $customer_name ?? 'Valued Customer';
    $cLen = strlen($cName);
    if ($cLen > 36) {
        $custFs = '13.5px';
        $custLh = '1.18';
    } elseif ($cLen > 22) {
        $custFs = '15px';
        $custLh = '1.2';
    } else {
        $custFs = '17px';
        $custLh = '1.22';
    }

    // LR Number dynamic sizing
    $lrVal = $lr_no ?? 'PENDING-LR';
    $lrFs = (strlen($lrVal) > 16) ? '13.5px' : '16px';
@endphp

<!-- Full Page Wrapper Table -->
<table class="email-outer-wrapper" role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f1f5f9; width: 100%; margin: 0; padding: 20px 12px 12px 12px;">
    <tr>
        <td align="center" valign="top">

            <!--[if mso]>
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="width: 600px;">
            <tr>
            <td align="center" valign="top">
            <![endif]-->

            <!-- Main White Card Container -->
            <table class="email-main-card" role="presentation" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 18px rgba(15, 23, 42, 0.06); padding: 0; margin: 0 auto; border-collapse: separate;">

                <!-- ================= 1. HEADER SECTION ================= -->
                <!-- Centered Hero Delivery Truck -->
                <tr>
                    <td class="card-content-cell" align="center" valign="middle" style="padding: 20px 24px 8px 24px;">
                        <img class="truck-hero-img" src="{{ $assetHost }}/assets/images/dispatch_exact/truck_hd.jpg?v=2" width="370" alt="Casino - No Compromise Delivery Truck" style="display: block; width: 370px; max-width: 86%; height: auto; margin: 0 auto; border: 0;">
                    </td>
                </tr>

                <!-- Bold Headline "SEE YOU SOON!" -->
                <tr>
                    <td class="card-content-cell" align="center" valign="middle" style="padding: 2px 24px 2px 24px;">
                        <div class="headline-main" style="font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 36px; font-weight: 900; color: #0052cc; letter-spacing: 0.5px; text-transform: uppercase; line-height: 1.08; margin: 0;">
                            SEE YOU SOON!
                        </div>
                    </td>
                </tr>

                <!-- Subtitle "Your package is out for delivery." -->
                <tr>
                    <td class="card-content-cell" align="center" valign="middle" style="padding: 2px 24px 18px 24px;">
                        <div class="headline-sub" style="font-family: 'Open Sans', 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 16px; font-weight: 700; color: #1e293b; line-height: 1.25; margin: 0;">
                            Your package is out for delivery.
                        </div>
                    </td>
                </tr>

                <!-- ================= 2. SHIPMENT DETAILS (3x2 GRID) ================= -->
                <tr>
                    <td class="card-content-cell" valign="top" style="padding: 0 24px 16px 24px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #edf5ff; border: 1.5px solid #bcd8fa; border-radius: 14px; overflow: hidden; border-collapse: separate;">

                            <!-- ROW 1: Customer, Invoice, Cartons -->
                            <tr>
                                <!-- 1. Customer (Width 42%) -->
                                <td class="details-cell" width="42%" valign="middle" style="padding: 16px 12px 16px 14px; border-right: 1.5px solid #d0e2ff; border-bottom: 1.5px solid #d0e2ff; vertical-align: middle;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td width="46" valign="middle" style="width: 46px; text-align: left; vertical-align: middle;">
                                                <img class="details-icon" src="{{ $assetHost }}/assets/images/dispatch_exact/icon_store.png" width="40" height="40" alt="Customer" style="display: block; width: 40px; height: 40px; border: 0;">
                                            </td>
                                            <td valign="middle" style="padding-left: 8px; vertical-align: middle;">
                                                <div class="details-label" style="font-family: 'Poppins', sans-serif; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 3px; line-height: 1;">
                                                    CUSTOMER
                                                </div>
                                                <div class="details-value" style="font-family: 'Poppins', sans-serif; font-size: {{ $custFs }}; font-weight: 700; color: #0f172a; line-height: {{ $custLh }}; word-break: break-word; max-height: 2.5em; overflow: hidden;">
                                                    {{ $cName }}
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>

                                <!-- 2. Invoice (Width 29%) -->
                                <td class="details-cell" width="29%" valign="middle" style="padding: 16px 10px 16px 12px; border-right: 1.5px solid #d0e2ff; border-bottom: 1.5px solid #d0e2ff; vertical-align: middle;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td width="44" valign="middle" style="width: 44px; text-align: left; vertical-align: middle;">
                                                <img class="details-icon" src="{{ $assetHost }}/assets/images/dispatch_exact/icon_invoice.png" width="38" height="38" alt="Invoice" style="display: block; width: 38px; height: 38px; border: 0;">
                                            </td>
                                            <td valign="middle" style="padding-left: 6px; vertical-align: middle;">
                                                <div class="details-label" style="font-family: 'Poppins', sans-serif; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 3px; line-height: 1;">
                                                    INVOICE
                                                </div>
                                                <div class="details-value" style="font-family: 'Poppins', sans-serif; font-size: 15px; font-weight: 700; color: #0f172a; line-height: 1.2; white-space: nowrap;">
                                                    {{ $invoice_no }}
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>

                                <!-- 3. Cartons (Width 29%) -->
                                <td class="details-cell" width="29%" valign="middle" style="padding: 16px 10px 16px 12px; border-bottom: 1.5px solid #d0e2ff; vertical-align: middle;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td width="44" valign="middle" style="width: 44px; text-align: left; vertical-align: middle;">
                                                <img class="details-icon" src="{{ $assetHost }}/assets/images/dispatch_exact/icon_cartons.png" width="40" height="40" alt="Cartons" style="display: block; width: 40px; height: 40px; border: 0;">
                                            </td>
                                            <td valign="middle" style="padding-left: 6px; vertical-align: middle;">
                                                <div class="details-label" style="font-family: 'Poppins', sans-serif; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 3px; line-height: 1;">
                                                    CARTONS
                                                </div>
                                                <div class="details-value" style="font-family: 'Poppins', sans-serif; font-size: 17px; font-weight: 700; color: #0f172a; line-height: 1.2; white-space: nowrap;">
                                                    {{ $cartons }}
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- ROW 2: Total Qty, Articles, LR Number -->
                            <tr>
                                <!-- 4. Total Qty (Width 42%) -->
                                <td class="details-cell" width="42%" valign="middle" style="padding: 16px 12px 16px 14px; border-right: 1.5px solid #d0e2ff; vertical-align: middle;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td width="46" valign="middle" style="width: 46px; text-align: left; vertical-align: middle;">
                                                <img class="details-icon" src="{{ $assetHost }}/assets/images/dispatch_exact/icon_shirt.png" width="40" height="40" alt="Quantity" style="display: block; width: 40px; height: 40px; border: 0;">
                                            </td>
                                            <td valign="middle" style="padding-left: 8px; vertical-align: middle;">
                                                <div class="details-label" style="font-family: 'Poppins', sans-serif; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 3px; line-height: 1;">
                                                    TOTAL QTY
                                                </div>
                                                <div class="details-value" style="font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 700; color: #0f172a; line-height: 1.2; white-space: nowrap;">
                                                    {{ (int)$total_qty }} pcs
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>

                                <!-- 5. Articles (Width 29%) -->
                                <td class="details-cell" width="29%" valign="middle" style="padding: 16px 10px 16px 12px; border-right: 1.5px solid #d0e2ff; vertical-align: middle;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td width="44" valign="middle" style="width: 44px; text-align: left; vertical-align: middle;">
                                                <img class="details-icon" src="{{ $assetHost }}/assets/images/dispatch_exact/icon_articles.png" width="38" height="38" alt="Articles" style="display: block; width: 38px; height: 38px; border: 0;">
                                            </td>
                                            <td valign="middle" style="padding-left: 6px; vertical-align: middle;">
                                                <div class="details-label" style="font-family: 'Poppins', sans-serif; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 3px; line-height: 1;">
                                                    ARTICLES
                                                </div>
                                                <div class="details-value" style="font-family: 'Poppins', sans-serif; font-size: 17px; font-weight: 700; color: #0f172a; line-height: 1.2;">
                                                    {{ $articles_count }}
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>

                                <!-- 6. LR Number (Width 29%) -->
                                <td class="details-cell" width="29%" valign="middle" style="padding: 16px 10px 16px 12px; vertical-align: middle;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td width="44" valign="middle" style="width: 44px; text-align: left; vertical-align: middle;">
                                                <img class="details-icon" src="{{ $assetHost }}/assets/images/dispatch_exact/icon_truck.png" width="40" height="40" alt="LR Number" style="display: block; width: 40px; height: 40px; border: 0;">
                                            </td>
                                            <td valign="middle" style="padding-left: 6px; vertical-align: middle;">
                                                <div class="details-label" style="font-family: 'Poppins', sans-serif; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 3px; line-height: 1;">
                                                    LR NUMBER
                                                </div>
                                                <div class="details-value" style="font-family: 'Poppins', sans-serif; font-size: {{ $lrFs }}; font-weight: 800; color: #0052cc; line-height: 1.2; white-space: nowrap;">
                                                    {{ $lrVal }}
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>

                <!-- ================= 3. QR CODE & FEATURE DOCUMENTS ================= -->
                <tr>
                    <td class="card-content-cell" valign="top" style="padding: 0 24px 16px 24px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #edf5ff; border: 1.5px solid #bcd8fa; border-radius: 14px; overflow: hidden; border-collapse: separate;">
                            <tr>
                                <!-- Left Column: QR Code (Width 38%) -->
                                <td class="qr-panel-left" width="38%" align="center" valign="middle" style="width: 38%; padding: 14px 10px; border-right: 1.5px solid #bcd8fa; text-align: center; vertical-align: middle;">
                                    <a href="{{ $doc_url }}" target="_blank" style="text-decoration: none; display: inline-block;">
                                        @if(!empty($qr_base64))
                                            <img src="{{ $qr_base64 }}" width="152" height="152" alt="Scan QR Code to View Full Documents" style="display: block; margin: 0 auto; width: 152px; height: 152px; max-width: 100%; border: 0;">
                                        @else
                                            <div style="width: 150px; height: 150px; line-height: 150px; text-align: center; background: #ffffff; border-radius: 10px; font-size: 13px; font-weight: bold; color: #0052cc;">
                                                SCAN QR
                                            </div>
                                        @endif
                                    </a>
                                </td>

                                <!-- Right Column: "SCAN TO VIEW" + 4 Feature Cards (Width 62%) -->
                                <td class="qr-panel-right" width="62%" valign="middle" style="width: 62%; padding: 14px 14px 14px 18px; vertical-align: middle;">
                                    <!-- Scan Titles -->
                                    <div class="scan-title" style="font-family: 'Poppins', sans-serif; font-size: 26px; font-weight: 900; color: #0058b3; letter-spacing: 0.5px; text-transform: uppercase; line-height: 1.1; margin: 0 0 2px 0;">
                                        SCAN TO VIEW
                                    </div>
                                    <div class="scan-subtitle" style="font-family: 'Poppins', sans-serif; font-size: 12.5px; font-weight: 700; color: #1e293b; letter-spacing: 0.3px; text-transform: uppercase; line-height: 1.25; margin: 0 0 14px 0;">
                                        INVOICE + FULL DELIVERY CHALLAN
                                    </div>

                                    <!-- 4 Document Feature Columns -->
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                        <tr>
                                            <!-- 1. Invoice PDF -->
                                            <td width="25%" align="center" valign="top" style="width: 25%; text-align: center; vertical-align: top; padding: 0 2px;">
                                                <a href="{{ $doc_url }}" target="_blank" style="text-decoration: none; display: block;">
                                                    <img class="feature-icon" src="{{ $assetHost }}/assets/images/dispatch_exact/qr_icon_invoice.png" width="36" height="36" alt="Invoice PDF" style="display: block; margin: 0 auto 5px auto; width: 36px; height: 36px; border: 0;">
                                                    <div class="feature-text" style="font-family: 'Poppins', sans-serif; font-size: 10.5px; font-weight: 700; color: #0f172a; line-height: 1.15; text-align: center;">
                                                        Invoice<br>PDF
                                                    </div>
                                                </a>
                                            </td>

                                            <!-- 2. Delivery Challan -->
                                            <td width="25%" align="center" valign="top" style="width: 25%; text-align: center; vertical-align: top; padding: 0 2px;">
                                                <a href="{{ $doc_url }}" target="_blank" style="text-decoration: none; display: block;">
                                                    <img class="feature-icon" src="{{ $assetHost }}/assets/images/dispatch_exact/qr_icon_challan.png" width="36" height="36" alt="Delivery Challan" style="display: block; margin: 0 auto 5px auto; width: 36px; height: 36px; border: 0;">
                                                    <div class="feature-text" style="font-family: 'Poppins', sans-serif; font-size: 10.5px; font-weight: 700; color: #0f172a; line-height: 1.15; text-align: center;">
                                                        Delivery<br>Challan
                                                    </div>
                                                </a>
                                            </td>

                                            <!-- 3. Carton Details -->
                                            <td width="25%" align="center" valign="top" style="width: 25%; text-align: center; vertical-align: top; padding: 0 2px;">
                                                <a href="{{ $doc_url }}" target="_blank" style="text-decoration: none; display: block;">
                                                    <img class="feature-icon" src="{{ $assetHost }}/assets/images/dispatch_exact/qr_icon_carton.png" width="36" height="36" alt="Carton Details" style="display: block; margin: 0 auto 5px auto; width: 36px; height: 36px; border: 0;">
                                                    <div class="feature-text" style="font-family: 'Poppins', sans-serif; font-size: 10.5px; font-weight: 700; color: #0f172a; line-height: 1.15; text-align: center;">
                                                        Carton<br>Details
                                                    </div>
                                                </a>
                                            </td>

                                            <!-- 4. LR Details -->
                                            <td width="25%" align="center" valign="top" style="width: 25%; text-align: center; vertical-align: top; padding: 0 2px;">
                                                <a href="{{ $doc_url }}" target="_blank" style="text-decoration: none; display: block;">
                                                    <img class="feature-icon" src="{{ $assetHost }}/assets/images/dispatch_exact/qr_icon_lr.png" width="36" height="36" alt="LR Details" style="display: block; margin: 0 auto 5px auto; width: 36px; height: 36px; border: 0;">
                                                    <div class="feature-text" style="font-family: 'Poppins', sans-serif; font-size: 10.5px; font-weight: 700; color: #0f172a; line-height: 1.15; text-align: center;">
                                                        LR<br>Details
                                                    </div>
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- ================= 4. FOOTER NOTIFICATION PANEL ================= -->
                <tr>
                    <td class="card-content-cell" valign="top" style="padding: 0 24px 4px 24px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #f0f7ff; border: 1.5px solid #bcd8fa; border-radius: 14px; overflow: hidden; border-collapse: separate;">
                            <tr>
                                <!-- Left: Parcel Box Icon with Speed Lines -->
                                <td class="footer-panel-icon" width="80" align="center" valign="middle" style="width: 80px; padding: 12px 10px; border-right: 1.5px solid #d0e2ff; text-align: center; vertical-align: middle;">
                                    <img class="footer-panel-box" src="{{ $assetHost }}/assets/images/dispatch_exact/icon_footer_box.png" width="56" height="56" alt="Express Delivery" style="display: block; margin: 0 auto; width: 56px; height: 56px; border: 0;">
                                </td>

                                <!-- Right: Text Notification -->
                                <td valign="middle" style="padding: 12px 16px; vertical-align: middle; text-align: left;">
                                    <div class="footer-text-1" style="font-family: 'Poppins', sans-serif; font-size: 15px; font-weight: 700; color: #0f172a; line-height: 1.25; margin-bottom: 2px;">
                                        Your shipment is on its way.
                                    </div>
                                    <div class="footer-text-2" style="font-family: 'Poppins', sans-serif; font-size: 12.5px; font-weight: 400; color: #475569; line-height: 1.35; margin-bottom: 4px;">
                                        Please keep your receiving team ready for a smooth and quick process.
                                    </div>
                                    <div class="footer-text-3" style="font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 700; color: #0052cc; line-height: 1.25;">
                                        Thank you for choosing Casino!
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- ================= 5. BOTTOM DECORATIVE WAVE ================= -->
                <tr>
                    <td align="center" valign="bottom" style="padding: 0; margin: 0; line-height: 0; font-size: 0; vertical-align: bottom;">
                        <img src="{{ $assetHost }}/assets/images/dispatch_exact/bottom_wave.png" width="600" alt="" style="display: block; width: 100%; max-width: 600px; height: auto; border: 0; margin: 0 auto; line-height: 0; vertical-align: bottom; border-radius: 0 0 16px 16px;">
                    </td>
                </tr>

            </table>
            <!-- End Main White Card Container -->

            <!--[if mso]>
            </td>
            </tr>
            </table>
            <![endif]-->

        </td>
    </tr>
</table>
<!-- End Full Page Wrapper Table -->

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invoice->inv_no }} - Tax Invoice & Delivery Order | Casino</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Roboto+Condensed:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0052cc;
            --primary-dark: #003e99;
            --navy: #0b2c64;
            --success: #059669;
            --bg: #f1f5f9;
            --card-bg: #ffffff;
            --border: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg);
            color: var(--text-dark);
            line-height: 1.45;
            padding-bottom: 90px;
        }

        /* Top Sticky Header */
        .top-navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: #ffffff;
            border-bottom: 1px solid var(--border);
            padding: 12px 18px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        .nav-container {
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .brand-badge {
            font-size: 20px;
            font-weight: 800;
            color: var(--navy);
            letter-spacing: 1px;
            text-transform: uppercase;
            font-family: 'Roboto Condensed', sans-serif;
        }
        .brand-badge span {
            color: var(--primary);
        }
        .inv-badge {
            display: inline-block;
            background-color: #eff6ff;
            color: var(--primary);
            font-size: 13px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            border: 1px solid #bfdbfe;
        }
        .nav-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .btn-download {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background-color: var(--primary);
            color: #ffffff !important;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            padding: 8px 14px;
            border-radius: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(0, 82, 204, 0.2);
        }
        .btn-download:hover {
            background-color: var(--primary-dark);
        }
        .btn-download.secondary {
            background-color: #0284c7;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.2);
        }

        /* Main Container */
        .main-wrapper {
            max-width: 1000px;
            margin: 18px auto;
            padding: 0 14px;
        }

        /* Shipment Overview Card */
        .overview-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 18px 20px;
            margin-bottom: 18px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.03);
        }
        .overview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 14px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background-color: #ecfdf5;
            color: var(--success);
            font-size: 12px;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 20px;
            border: 1px solid #a7f3d0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-dot {
            width: 7px;
            height: 7px;
            background-color: var(--success);
            border-radius: 50%;
        }
        .overview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 12px;
        }
        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 14px;
        }
        .info-box-label {
            font-size: 10.5px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }
        .info-box-val {
            font-size: 14.5px;
            font-weight: 800;
            color: var(--text-dark);
            word-break: break-word;
        }

        /* Tab Switcher */
        .tab-switcher {
            display: flex;
            background-color: #e2e8f0;
            border-radius: 10px;
            padding: 4px;
            gap: 4px;
            margin-bottom: 18px;
        }
        .tab-btn {
            flex: 1;
            background: transparent;
            border: none;
            padding: 11px 16px;
            font-size: 13.5px;
            font-weight: 700;
            color: #475569;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            transition: all 0.15s ease;
        }
        .tab-btn.active {
            background-color: #ffffff;
            color: var(--primary);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        /* Document Paper Card */
        .doc-paper {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            margin-bottom: 24px;
        }
        .doc-section {
            display: none;
        }
        .doc-section.active {
            display: block;
        }

        /* Document Content Formatting */
        .doc-header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            padding-bottom: 14px;
            border-bottom: 2px solid #0052cc;
        }
        .doc-company-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--navy);
            font-family: 'Roboto Condensed', sans-serif;
            text-transform: uppercase;
        }
        .doc-company-sub {
            font-size: 11px;
            color: #475569;
            margin-top: 2px;
            line-height: 1.35;
        }
        .doc-title-badge {
            display: inline-block;
            background-color: #0052cc;
            color: #ffffff;
            font-size: 13px;
            font-weight: 800;
            padding: 6px 16px;
            border-radius: 6px;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        /* Addresses grid */
        .address-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 14px;
            margin-bottom: 16px;
        }
        .address-box {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 12px;
            background-color: #f8fafc;
        }
        .address-box-title {
            font-weight: 800;
            color: #0f172a;
            font-size: 12.5px;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        /* Responsive Table Container */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 16px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
            white-space: nowrap;
        }
        .data-table th, .data-table td {
            border: 1px solid #e2e8f0;
            padding: 7px 8px;
            text-align: left;
        }
        .data-table th {
            background-color: #f1f5f9;
            font-weight: 800;
            color: #1e293b;
            text-transform: uppercase;
            font-size: 11px;
        }
        .data-table td.text-center, .data-table th.text-center {
            text-align: center;
        }
        .data-table td.text-right, .data-table th.text-right {
            text-align: right;
        }
        .data-table tr.total-row {
            background-color: #e0f2fe;
            font-weight: 800;
            color: #0369a1;
        }

        /* MRP Summary Matrix */
        .mrp-table-box {
            border: 1.5px solid #0052cc;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 14px;
            margin-bottom: 14px;
        }
        .mrp-header-bar {
            background-color: #01316f;
            color: #ffffff;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Sticky Bottom Bar on Mobile */
        .mobile-bottom-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #ffffff;
            border-top: 1px solid var(--border);
            padding: 10px 14px;
            display: flex;
            gap: 10px;
            z-index: 100;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.06);
        }
        .mobile-bottom-bar a {
            flex: 1;
            text-align: center;
            justify-content: center;
        }

        @media (min-width: 768px) {
            .mobile-bottom-bar {
                display: none;
            }
            body {
                padding-bottom: 30px;
            }
        }
    </style>
</head>
<body>

<!-- 1. Sticky Navigation Header -->
<header class="top-navbar">
    <div class="nav-container">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div class="brand-badge">CASINO<span>.</span></div>
            <div class="inv-badge">{{ $invoice->inv_no }}</div>
        </div>
        <div class="nav-actions">
            <a href="{{ route('sales_invoices.public_invoice_pdf', $invoice->id) }}" target="_blank" class="btn-download">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Tax Invoice (Original)
            </a>
            <a href="{{ route('sales_invoices.public_do_pdf', $invoice->id) }}" target="_blank" class="btn-download secondary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Delivery Order (Challan)
            </a>
        </div>
    </div>
</header>

<div class="main-wrapper">
    <!-- 2. Shipment Summary Overview Card -->
    <div class="overview-card">
        <div class="overview-header">
            <div>
                <span class="status-pill">
                    <span class="status-dot"></span>
                    Verified Dispatch Document
                </span>
            </div>
            <div style="font-size: 12.5px; font-weight: 700; color: #475569;">
                Packed On: {{ $invoice->inv_date ? $invoice->inv_date->format('d-m-Y') : date('d-m-Y') }}
            </div>
        </div>

        <div class="overview-grid">
            <div class="info-box">
                <div class="info-box-label">CUSTOMER</div>
                <div class="info-box-val">{{ $invoice->customer->name ?? 'Customer' }}</div>
            </div>
            <div class="info-box">
                <div class="info-box-label">TOTAL QUANTITY</div>
                <div class="info-box-val" style="color: #0052cc;">{{ (int)$totalPcs }} PCS</div>
            </div>
            <div class="info-box">
                <div class="info-box-label">CARTONS / BOX</div>
                <div class="info-box-val">{{ $booking->box_number ?? ($invoice->no_of_box ?: '1 Box') }}</div>
            </div>
            <div class="info-box">
                <div class="info-box-label">LR NUMBER</div>
                <div class="info-box-val" style="color: #0052cc;">{{ $booking->lr_no ?? 'PENDING' }}</div>
            </div>
            <div class="info-box">
                <div class="info-box-label">TRANSPORTER</div>
                <div class="info-box-val">{{ $booking->preferred_transport ?? 'Direct' }}</div>
            </div>
        </div>
    </div>

    <!-- 3. Document Tab Switcher -->
    <div class="tab-switcher">
        <button type="button" class="tab-btn active" onclick="switchDocTab('tax-invoice', this)">
            📄 Tax Invoice (Original Copy)
        </button>
        <button type="button" class="tab-btn" onclick="switchDocTab('delivery-order', this)">
            📦 Delivery Order (Challan)
        </button>
    </div>

    <!-- 4. Section 1: TAX INVOICE (ORIGINAL FOR RECIPIENT) -->
    <div id="tax-invoice" class="doc-paper doc-section active">
        <table class="doc-header-table">
            <tr>
                <td style="vertical-align: top;">
                    <div class="doc-company-title">{{ strtoupper($setting->company_name ?? 'NACHIAS FASHION PRIVATE LIMITED') }}</div>
                    <div class="doc-company-sub">
                        {{ $setting->address ?? '272/2, SOMU NAGAR, MADURAI - 625016' }}<br>
                        Phone: {{ $setting->phone ?? '8489938071' }} | Email: {{ $setting->email ?? 'sales@nachias.com' }}<br>
                        <strong>GSTIN:</strong> {{ $setting->gst_no ?? '33AADCN9342A1ZU' }} | <strong>State:</strong> Tamil Nadu (33)
                    </div>
                </td>
                <td style="vertical-align: top; text-align: right;">
                    <div class="doc-title-badge">TAX INVOICE</div>
                    <div style="font-size: 11px; font-weight: 700; color: #475569; margin-top: 4px;">ORIGINAL FOR RECIPIENT</div>
                </td>
            </tr>
        </table>

        <!-- Billed to & Shipped to -->
        <div class="address-grid">
            <div class="address-box">
                <div class="address-box-title">Details of Receiver | Billed To:</div>
                <strong>{{ $invoice->customer->name ?? '' }}</strong><br>
                {{ $invoice->customer->address_line_1 ?? '' }} {{ $invoice->customer->address_line_2 ?? '' }}<br>
                {{ $invoice->customer->city->city_name ?? '' }} - {{ $invoice->customer->zip_code ?? '' }}<br>
                State: {{ $invoice->customer->state->state_name ?? '' }} | Code: {{ $invoice->customer->state->state_code ?? '' }}<br>
                <strong>GSTIN:</strong> {{ $invoice->customer->gst_no ?? 'URP' }}
            </div>
            <div class="address-box">
                <div class="address-box-title">Invoice Details:</div>
                <strong>Invoice No:</strong> {{ $invoice->inv_no }}<br>
                <strong>Invoice Date:</strong> {{ $invoice->inv_date ? $invoice->inv_date->format('d-m-Y') : date('d-m-Y') }}<br>
                <strong>Order No:</strong> {{ $invoice->salesOrder->so_no ?? $invoice->salesOrder->order_no ?? '-' }}<br>
                <strong>Reverse Charge:</strong> No<br>
                <strong>Place of Supply:</strong> {{ $invoice->customer->state->state_name ?? 'Tamil Nadu' }}
            </div>
        </div>

        <!-- Items Table -->
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 35px;">#</th>
                        <th>Art No</th>
                        <th>Description of Goods</th>
                        <th class="text-center">HSN/SAC</th>
                        <th class="text-right">Qty</th>
                        <th class="text-right">Rate</th>
                        <th class="text-right">Taxable Val</th>
                        <th class="text-right">CGST</th>
                        <th class="text-right">SGST</th>
                        <th class="text-right">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sl = 1; @endphp
                    @foreach($invoice->items as $item)
                        @php
                            $taxRate = $invoice->cgst_percent ?? 2.5;
                            $taxAmt = ($item->amount * $taxRate) / 100;
                            $lineTotal = $item->amount + ($taxAmt * 2);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $sl++ }}</td>
                            <td style="font-weight: 700;">{{ $item->art_no ?: '-' }}</td>
                            <td>{{ $item->item ? $item->item->name : ($item->description ?: 'CASINO SHIRT') }}</td>
                            <td class="text-center">{{ $item->hsn_sac ?: '6205' }}</td>
                            <td class="text-right" style="font-weight: 700;">{{ (int)$item->quantity }}</td>
                            <td class="text-right">{{ number_format($item->rate, 2) }}</td>
                            <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                            <td class="text-right">{{ number_format($taxAmt, 2) }}</td>
                            <td class="text-right">{{ number_format($taxAmt, 2) }}</td>
                            <td class="text-right" style="font-weight: 700;">{{ number_format($lineTotal, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="4" class="text-right" style="padding-right: 12px;">Grand Total :</td>
                        <td class="text-right">{{ (int)$totalPcs }} PCS</td>
                        <td></td>
                        <td class="text-right">&#8377;{{ number_format($invoice->sub_total, 2) }}</td>
                        <td class="text-right">&#8377;{{ number_format($invoice->cgst, 2) }}</td>
                        <td class="text-right">&#8377;{{ number_format($invoice->sgst, 2) }}</td>
                        <td class="text-right" style="font-size: 13px;">&#8377;{{ number_format($invoice->grand_total, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="text-align: right; margin-top: 14px;">
            <a href="{{ route('sales_invoices.public_invoice_pdf', $invoice->id) }}" target="_blank" class="btn-download">
                📥 Download Official Tax Invoice PDF (Original)
            </a>
        </div>
    </div>

    <!-- 5. Section 2: DELIVERY ORDER (CHALLAN) -->
    <div id="delivery-order" class="doc-paper doc-section">
        <table class="doc-header-table">
            <tr>
                <td style="vertical-align: top;">
                    <div class="doc-company-title">{{ strtoupper($setting->company_name ?? 'NACHIAS FASHION PRIVATE LIMITED') }}</div>
                    <div class="doc-company-sub">
                        {{ $setting->address ?? '272/2, SOMU NAGAR, MADURAI - 625016' }}<br>
                        Phone: {{ $setting->phone ?? '8489938071' }} | Email: {{ $setting->email ?? 'sales@nachias.com' }}
                    </div>
                </td>
                <td style="vertical-align: top; text-align: right;">
                    <div class="doc-title-badge" style="background-color: #0284c7;">DELIVERY ORDER</div>
                    <div style="font-size: 11px; font-weight: 700; color: #475569; margin-top: 4px;">PACKING & CHALLAN BREAKDOWN</div>
                </td>
            </tr>
        </table>

        <!-- Meta info -->
        <div class="address-grid">
            <div class="address-box">
                <div class="address-box-title">Customer:</div>
                <strong>{{ $invoice->customer->name ?? '' }}</strong><br>
                {{ $invoice->customer->address_line_1 ?? '' }} {{ $invoice->customer->address_line_2 ?? '' }}<br>
                {{ $invoice->customer->city->city_name ?? '' }} - {{ $invoice->customer->zip_code ?? '' }}<br>
                <strong>GSTIN:</strong> {{ $invoice->customer->gst_no ?? 'URP' }}
            </div>
            <div class="address-box">
                <div class="address-box-title">Order & Box Details:</div>
                <strong>Invoice No:</strong> {{ $invoice->inv_no }}<br>
                <strong>Packed On:</strong> {{ $invoice->inv_date ? $invoice->inv_date->format('d/m/Y') : date('d/m/Y') }}<br>
                <strong>Order No:</strong> {{ $invoice->salesOrder->so_no ?? $invoice->salesOrder->order_no ?? '-' }}<br>
                <strong>Total Box:</strong> {{ $invoice->no_of_box ?: '1' }} Box<br>
                <strong>Total Quantity:</strong> <span style="color: #0052cc; font-weight: 800;">{{ (int)$totalPcs }} PCS</span>
            </div>
        </div>

        <!-- Size Matrix Table -->
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 35px;">#</th>
                        <th>Description</th>
                        <th>Art No.</th>
                        <th class="text-right">MRP</th>
                        <th class="text-right">WSP</th>
                        <th class="text-center">Sleeve</th>
                        @foreach($allSizes as $size)
                            <th class="text-center">{{ $size }}</th>
                        @endforeach
                        <th class="text-center">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $slNo = 1; @endphp
                    @foreach($groupedDeliveryRows as $row)
                        <tr>
                            <td class="text-center">{{ $slNo++ }}</td>
                            <td>{{ $row['desc'] }}</td>
                            <td style="font-weight: 700;">{{ $row['art'] }}</td>
                            <td class="text-right">{{ number_format($row['mrp'], 2) }}</td>
                            <td class="text-right">{{ number_format($row['rate'], 2) }}</td>
                            <td class="text-center" style="font-weight: 700;">{{ $row['sleeve'] }}</td>
                            @foreach($allSizes as $size)
                                <td class="text-center">
                                    {{ isset($row['sizes'][$size]) && $row['sizes'][$size] > 0 ? (int)$row['sizes'][$size] : '' }}
                                </td>
                            @endforeach
                            <td class="text-center" style="font-weight: 800;">{{ (int)$row['total_qty'] }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="6" class="text-right" style="padding-right: 12px;">Grand Total :</td>
                        @foreach($allSizes as $size)
                            @php
                                $sizeSum = 0;
                                foreach($groupedDeliveryRows as $r) {
                                    $sizeSum += ($r['sizes'][$size] ?? 0);
                                }
                            @endphp
                            <td class="text-center">{{ $sizeSum > 0 ? (int)$sizeSum : '-' }}</td>
                        @endforeach
                        <td class="text-center" style="font-size: 13px;">{{ (int)$totalPcs }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- MRP-Wise Quantity Summary -->
        <div class="mrp-table-box">
            <div class="mrp-header-bar">MRP-WISE QUANTITY SUMMARY</div>
            <div class="table-responsive" style="margin-bottom: 0; border: none; border-radius: 0;">
                <table class="data-table" style="background-color: #ffffff;">
                    <tr>
                        <th style="width: 100px; background-color: #f0f7fd; color: #01316f;">MRP (&#8377;)</th>
                        @foreach($mrpSummary as $mrpVal => $mrpQty)
                            <th class="text-center">&#8377;{{ $mrpVal }}</th>
                        @endforeach
                        <th class="text-center" style="background-color: #01316f; color: #ffffff;">TOTAL</th>
                    </tr>
                    <tr>
                        <td style="font-weight: 800; background-color: #f0f7fd; color: #01316f;">Qty (Pcs)</td>
                        @foreach($mrpSummary as $mrpVal => $mrpQty)
                            <td class="text-center" style="font-weight: 800;">{{ (int)$mrpQty }}</td>
                        @endforeach
                        <td class="text-center" style="font-weight: 800; background-color: #dbeafe; color: #01316f;">{{ (int)$totalPcs }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div style="text-align: right; margin-top: 14px;">
            <a href="{{ route('sales_invoices.public_do_pdf', $invoice->id) }}" target="_blank" class="btn-download secondary">
                📥 Download Delivery Order PDF (Landscape)
            </a>
        </div>
    </div>
</div>

<!-- 6. Mobile Bottom Sticky Action Bar -->
<div class="mobile-bottom-bar">
    <a href="{{ route('sales_invoices.public_invoice_pdf', $invoice->id) }}" target="_blank" class="btn-download">
        📄 Tax Invoice PDF
    </a>
    <a href="{{ route('sales_invoices.public_do_pdf', $invoice->id) }}" target="_blank" class="btn-download secondary">
        📦 Delivery Order PDF
    </a>
</div>

<script>
    function switchDocTab(sectionId, btn) {
        document.querySelectorAll('.doc-section').forEach(function(el) {
            el.classList.remove('active');
        });
        document.querySelectorAll('.tab-btn').forEach(function(b) {
            b.classList.remove('active');
        });
        var target = document.getElementById(sectionId);
        if (target) {
            target.classList.add('active');
        }
        btn.classList.add('active');
    }
</script>

</body>
</html>


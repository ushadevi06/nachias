<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carton Box Sticker - {{ $invoice->inv_no }} (Box {{ $currentBoxNo }}/{{ $totalBoxes }})</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800;900&family=Inter:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        @page {
            size: 150mm 100mm;
            margin: 0 !important;
        }
        @font-face {
            font-family: 'Cinzel';
            src: url('{{ asset('assets/fonts/Cinzel-Bold.ttf') }}') format('truetype');
            font-weight: 700;
            font-style: normal;
        }
        @font-face {
            font-family: 'Cinzel';
            src: url('{{ asset('assets/fonts/Cinzel-Black.ttf') }}') format('truetype');
            font-weight: 900;
            font-style: normal;
        }
        @font-face {
            font-family: 'Montserrat';
            src: url('{{ asset('assets/fonts/Montserrat-Regular.ttf') }}') format('truetype');
            font-weight: 400;
            font-style: normal;
        }
        @font-face {
            font-family: 'Montserrat';
            src: url('{{ asset('assets/fonts/Montserrat-SemiBold.ttf') }}') format('truetype');
            font-weight: 600;
            font-style: normal;
        }
        @font-face {
            font-family: 'Montserrat';
            src: url('{{ asset('assets/fonts/Montserrat-Bold.ttf') }}') format('truetype');
            font-weight: 700;
            font-style: normal;
        }
        @font-face {
            font-family: 'Montserrat';
            src: url('{{ asset('assets/fonts/Montserrat-ExtraBold.ttf') }}') format('truetype');
            font-weight: 800;
            font-style: normal;
        }
        @font-face {
            font-family: 'Montserrat';
            src: url('{{ asset('assets/fonts/Montserrat-Black.ttf') }}') format('truetype');
            font-weight: 900;
            font-style: normal;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        html, body {
            width: 150mm;
            height: 100mm;
            margin: 0;
            padding: 0;
            background: #ffffff;
            font-family: 'Montserrat', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #004b87;
            -webkit-font-smoothing: antialiased;
        }
        .sticker-page {
            width: 150mm;
            height: 100mm;
            padding: 3mm 4mm;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
        }
        .outer-card {
            width: 100%;
            height: 100%;
            border: 2.5px solid #004b87;
            border-radius: 12px;
            padding: 6px 9px 5px 9px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: #ffffff;
        }

        /* Top Header */
        .top-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 5px;
            min-height: 44px;
        }
        .header-brand-col {
            flex: 1.35;
            display: flex;
            align-items: center;
            padding-left: 2px;
        }
        .brand-name {
            font-size: 32px;
            font-weight: 900;
            letter-spacing: 2px;
            color: #004b87;
            text-transform: uppercase;
            line-height: 1;
            font-family: 'Montserrat', 'Inter', sans-serif;
        }
        .header-divider {
            width: 1.5px;
            height: 38px;
            background: #004b87;
            flex-shrink: 0;
        }
        .header-info-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0 10px;
        }
        .header-info-col.invoice-col {
            flex: 1.45;
        }
        .header-info-col.box-col {
            flex: 1.05;
            padding-right: 2px;
        }
        .pill-label {
            font-size: 11px;
            font-weight: 700;
            color: #004b87;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            margin-bottom: 2px;
            line-height: 1.1;
        }
        .pill-badge {
            background: #dceefb;
            border-radius: 7px;
            padding: 3px 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }
        .inv-badge {
            font-size: 21px;
            font-weight: 900;
            color: #004b87;
            letter-spacing: 0.5px;
            line-height: 1.15;
            white-space: nowrap;
        }
        .box-badge {
            font-size: 24px;
            font-weight: 900;
            color: #004b87;
            letter-spacing: 1px;
            line-height: 1.15;
            white-space: nowrap;
        }

        /* Blue Header Banner */
        .banner {
            background: #004b87;
            border-radius: 6px;
            padding: 7px 12px;
            min-height: 42px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #ffffff;
            margin-bottom: 5px;
        }
        .banner-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .banner-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .banner-title {
            font-size: 20px;
            font-weight: 900;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            color: #ffffff;
            white-space: nowrap;
            line-height: 1;
        }
        .banner-right {
            display: flex;
            align-items: center;
            gap: 9px;
            border-left: 1.5px solid rgba(255, 255, 255, 0.65);
            padding-left: 12px;
            height: 28px;
        }
        .shirt-text {
            display: flex;
            flex-direction: column;
            font-size: 10px;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            color: #ffffff;
        }

        /* Matrix Table */
        .matrix-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #004b87;
            table-layout: fixed;
        }
        .matrix-table th, 
        .matrix-table td {
            border: 1.5px solid #004b87;
            text-align: center;
            vertical-align: middle;
            padding: 5px 3px;
        }
        .matrix-table thead th {
            background: #dceefb;
            color: #004b87;
            font-size: 14.5px;
            font-weight: 800;
            letter-spacing: 0.4px;
            padding: 6px 3px;
        }
        .matrix-table th.col-variety {
            width: 30%;
            text-align: left;
            padding-left: 12px;
            font-size: 14px;
            letter-spacing: 0.6px;
        }
        .matrix-table th.col-total,
        .matrix-table td.col-total {
            width: 14%;
            font-weight: 900;
            font-size: 16.5px;
        }
        .matrix-table tbody tr {
            background: #ffffff;
        }
        .matrix-table tbody td.cell-variety {
            text-align: left;
            padding-left: 10px;
            font-weight: 800;
            font-size: 14.5px;
            color: #004b87;
            letter-spacing: 0.4px;
            white-space: nowrap;
        }
        .matrix-table tbody td.cell-qty {
            font-size: 16px;
            font-weight: 700;
            color: #004b87;
        }
        .matrix-table tbody td.col-total {
            font-size: 17px;
            font-weight: 900;
            color: #004b87;
        }

        /* Pattern Swatches */
        .swatch-wrap {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .swatch-box {
            width: 32px;
            height: 22px;
            border: 1.5px solid #004b87;
            border-radius: 4px;
            display: inline-block;
            vertical-align: middle;
            flex-shrink: 0;
            box-sizing: border-box;
        }
        .swatch-plains {
            background: #8fc7f4;
        }

        /* Table Footer / Summary Row */
        .matrix-table tfoot tr {
            background: #004b87;
            color: #ffffff;
        }
        .matrix-table tfoot td {
            border: 1.5px solid #004b87;
            padding: 6px 3px;
            color: #ffffff;
        }
        .matrix-table tfoot td.footer-label {
            text-align: center;
            font-weight: 900;
            font-size: 14.5px;
            letter-spacing: 0.8px;
            color: #ffffff;
        }
        .matrix-table tfoot td.footer-qty {
            font-size: 16.5px;
            font-weight: 900;
            color: #ffffff;
        }
        .matrix-table tfoot td.footer-grand-total {
            font-size: 20px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: 0.5px;
        }

        /* Bottom Feature Icons */
        .bottom-features {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 5px;
            margin-top: 2px;
        }
        .feature-item {
            display: flex;
            align-items: center;
            gap: 9px;
            flex: 1;
            padding: 0 6px;
        }
        .feature-item:not(:last-child) {
            border-right: 1.5px solid #c8d9e6;
        }
        .feature-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .feature-title {
            font-size: 11.5px;
            font-weight: 800;
            color: #004b87;
            line-height: 1.15;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }

        @media print {
            body {
                background: #ffffff;
            }
            .sticker-page {
                padding: 3mm 4mm;
            }
        }
    </style>
</head>
<body onload="{{ isset($is_print) && $is_print ? 'window.print()' : '' }}">

<div class="sticker-page">
    <div class="outer-card">

        <!-- Top Header -->
        <div class="top-header">
            <div class="header-brand-col">
                <div class="brand-name">{{ $brandName }}</div>
            </div>
            <div class="header-divider"></div>
            <div class="header-info-col invoice-col">
                <span class="pill-label">INVOICE No.</span>
                <div class="pill-badge">
                    <span class="inv-badge">{{ strtoupper($invoice->inv_no) }}</span>
                </div>
            </div>
            <div class="header-divider"></div>
            <div class="header-info-col box-col">
                <span class="pill-label">BOX No.</span>
                <div class="pill-badge">
                    <span class="box-badge">{{ $currentBoxNo }} / {{ $totalBoxes }}</span>
                </div>
            </div>
        </div>

        <!-- Blue Header Banner -->
        <div class="banner">
            <div class="banner-left">
                <div class="banner-icon">
                    <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                        <!-- Orange sparkles / rays -->
                        <path d="M4 6L1.5 4.5M3 10L0.5 10M7 2.5L5.5 0.5" stroke="#f36b21" stroke-width="2.2" stroke-linecap="round"/>
                        <!-- 3D Box in white -->
                        <path d="M16 4L28 10.5V22.5L16 29L4 22.5V10.5L16 4Z" stroke="#ffffff" stroke-width="2.2" stroke-linejoin="round"/>
                        <path d="M16 4V16.5M28 10.5L16 16.5M4 10.5L16 16.5" stroke="#ffffff" stroke-width="2.2"/>
                    </svg>
                </div>
                <div class="banner-title">What's Inside This Box?</div>
            </div>
            <div class="banner-right">
                <svg width="24" height="26" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.38 3.46L16 2a4 4 0 01-8 0L3.62 3.46a2 2 0 00-1.34 2.23l.58 3.47a1 1 0 00.99.84H6v10a2 2 0 002 2h8a2 2 0 002-2V10h2.15a1 1 0 00.99-.84l.58-3.47a2 2 0 00-1.34-2.23z"/>
                    <path d="M12 2v5.5"/>
                    <circle cx="12" cy="10.5" r="0.8" fill="#ffffff"/>
                    <circle cx="12" cy="14" r="0.8" fill="#ffffff"/>
                </svg>
                <div class="shirt-text">
                    <span>{{ $brandName }}</span>
                    <span>Shirts</span>
                </div>
            </div>
        </div>

        <!-- Table Matrix -->
        <table class="matrix-table">
            <thead>
                <tr>
                    <th class="col-variety">VARIETY</th>
                    @foreach($sizes as $sz)
                        <th>{{ $sz }}</th>
                    @endforeach
                    <th class="col-total">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($matrix as $varKey => $sizeCounts)
                    <tr>
                        <td class="cell-variety">
                            <div class="swatch-wrap">
                                @if($varKey === 'PLAINS')
                                    <span class="swatch-box swatch-plains"></span>
                                @elseif($varKey === 'CHECKS')
                                    <svg class="swatch-box" viewBox="0 0 32 22">
                                        <defs>
                                            <pattern id="pat-chk-{{ $loop->index }}" width="8" height="8" patternUnits="userSpaceOnUse">
                                                <rect width="4" height="4" fill="#004b87"/>
                                                <rect x="4" width="4" height="4" fill="#6ba4d9"/>
                                                <rect y="4" width="4" height="4" fill="#6ba4d9"/>
                                                <rect x="4" y="4" width="4" height="4" fill="#ffffff"/>
                                            </pattern>
                                        </defs>
                                        <rect width="32" height="22" fill="url(#pat-chk-{{ $loop->index }})"/>
                                    </svg>
                                @elseif($varKey === 'STRIPES')
                                    <svg class="swatch-box" viewBox="0 0 32 22">
                                        <defs>
                                            <pattern id="pat-strp-{{ $loop->index }}" width="6" height="22" patternUnits="userSpaceOnUse">
                                                <rect width="3" height="22" fill="#004b87"/>
                                                <rect x="3" width="3" height="22" fill="#ffffff"/>
                                            </pattern>
                                        </defs>
                                        <rect width="32" height="22" fill="url(#pat-strp-{{ $loop->index }})"/>
                                    </svg>
                                @elseif($varKey === 'PRINTS')
                                    <svg class="swatch-box" viewBox="0 0 32 22" style="background: #ffffff;">
                                        <defs>
                                            <pattern id="pat-prnt-{{ $loop->index }}" width="10" height="11" patternUnits="userSpaceOnUse">
                                                <circle cx="5" cy="5" r="1.3" fill="#004b87"/>
                                                <circle cx="5" cy="2.8" r="1" fill="#004b87"/>
                                                <circle cx="5" cy="7.2" r="1" fill="#004b87"/>
                                                <circle cx="2.8" cy="5" r="1" fill="#004b87"/>
                                                <circle cx="7.2" cy="5" r="1" fill="#004b87"/>
                                                <circle cx="1" cy="1" r="0.8" fill="#508ec9"/>
                                                <circle cx="9" cy="10" r="0.8" fill="#508ec9"/>
                                            </pattern>
                                        </defs>
                                        <rect width="32" height="22" fill="url(#pat-prnt-{{ $loop->index }})"/>
                                    </svg>
                                @else
                                    <span class="swatch-box" style="background: #b0bec5;"></span>
                                @endif
                                <span>{{ $varKey }}</span>
                            </div>
                        </td>
                        @foreach($sizes as $sz)
                            @php $q = $sizeCounts[$sz] ?? 0; @endphp
                            <td class="cell-qty">
                                {{ $q }}
                            </td>
                        @endforeach
                        <td class="col-total">
                            {{ $rowTotals[$varKey] ?? 0 }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="footer-label">TOTAL QTY</td>
                    @foreach($sizes as $sz)
                        <td class="footer-qty">{{ $colTotals[$sz] ?? 0 }}</td>
                    @endforeach
                    <td class="footer-grand-total">{{ $grandTotal ?? 0 }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Bottom Feature Icons -->
        <div class="bottom-features">
            <!-- 1. Sort Easy -->
            <div class="feature-item">
                <div class="feature-icon">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#f36b21" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.38 3.46L16 2a4 4 0 01-8 0L3.62 3.46a2 2 0 00-1.34 2.23l.58 3.47a1 1 0 00.99.84H6v10a2 2 0 002 2h8a2 2 0 002-2V10h2.15a1 1 0 00.99-.84l.58-3.47a2 2 0 00-1.34-2.23z"/>
                    </svg>
                </div>
                <div class="feature-title">
                    <div>SORT</div>
                    <div>EASY</div>
                </div>
            </div>

            <!-- 2. Find Faster -->
            <div class="feature-item">
                <div class="feature-icon">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#f36b21" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        <text x="12" y="7.5" font-size="5" fill="#f36b21" text-anchor="middle" font-weight="900" font-family="'Montserrat', sans-serif">A</text>
                    </svg>
                </div>
                <div class="feature-title">
                    <div>FIND</div>
                    <div>FASTER</div>
                </div>
            </div>

            <!-- 3. Serve Quicker -->
            <div class="feature-item">
                <div class="feature-icon">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#f36b21" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/>
                        <path d="M3.27 6.96L12 12.01l8.73-5.05M12 22.08V12"/>
                    </svg>
                </div>
                <div class="feature-title">
                    <div>SERVE</div>
                    <div>QUICKER</div>
                </div>
            </div>

            <!-- 4. Happier Customers -->
            <div class="feature-item">
                <div class="feature-icon">
                    <svg width="34" height="30" viewBox="0 0 24 24" fill="none" stroke="#f36b21" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                    </svg>
                </div>
                <div class="feature-title">
                    <div>HAPPIER</div>
                    <div>CUSTOMERS</div>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>

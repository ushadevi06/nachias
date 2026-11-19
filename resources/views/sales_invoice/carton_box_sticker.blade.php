<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carton Box Sticker - {{ $invoice->inv_no }} (Box {{ $currentBoxNo }}/{{ $totalBoxes }})</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&family=Roboto+Condensed:wght@700;800&family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style>
        @page {
            size: 150mm 100mm;
            margin: 0 !important;
        }
        @font-face {
            font-family: 'Copperplate Gothic';
            src: url('{{ asset('assets/fonts/CopperplateGothic-Bold.ttf') }}') format('truetype');
            font-weight: 700;
            font-style: normal;
        }
        @font-face {
            font-family: 'Copperplate Gothic';
            src: url('{{ asset('assets/fonts/CopperplateGothic-Bold.ttf') }}') format('truetype');
            font-weight: 800;
            font-style: normal;
        }
        @font-face {
            font-family: 'Copperplate Gothic';
            src: url('{{ asset('assets/fonts/CopperplateGothic-Bold.ttf') }}') format('truetype');
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
        @font-face {
            font-family: 'Roboto Condensed';
            src: url('{{ asset('assets/fonts/RobotoCondensed-Bold.ttf') }}') format('truetype');
            font-weight: 700;
            font-style: normal;
        }
        @font-face {
            font-family: 'Roboto';
            src: url('{{ asset('assets/fonts/Roboto-Regular.ttf') }}') format('truetype');
            font-weight: 400;
            font-style: normal;
        }
        @font-face {
            font-family: 'Roboto';
            src: url('{{ asset('assets/fonts/Roboto-Bold.ttf') }}') format('truetype');
            font-weight: 700;
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
            color: #003b70;
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
            border: 2px solid #004b87;
            border-radius: 12px;
            padding: 6px 10px 5px 10px;
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
            flex: 1.45;
            display: flex;
            align-items: center;
            padding-left: 2px;
        }
        .brand-name {
            font-size: 33px;
            font-weight: 700;
            letter-spacing: 2px;
            color: #003b70;
            text-transform: uppercase;
            line-height: 1;
            font-family: 'Copperplate Gothic', 'Copperplate Gothic Bold', 'Copperplate', serif;
        }
        .header-divider {
            width: 1px;
            height: 38px;
            background: #c5d7e5;
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
            font-family: 'Montserrat', sans-serif;
            font-size: 11px;
            font-weight: 700;
            color: #003b70;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            margin-bottom: 2px;
            line-height: 1.1;
        }
        .pill-badge {
            background: #eaf3fa;
            border-radius: 6px;
            padding: 3px 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }
        .inv-badge {
            font-family: 'Roboto', sans-serif;
            font-size: 21px;
            font-weight: 800;
            color: #003b70;
            letter-spacing: 0.5px;
            line-height: 1.15;
            white-space: nowrap;
        }
        .box-badge {
            font-family: 'Roboto', sans-serif;
            font-size: 23px;
            font-weight: 800;
            color: #003b70;
            letter-spacing: 1px;
            line-height: 1.15;
            white-space: nowrap;
        }

        /* Blue Header Banner */
        .banner {
            background: #004b87;
            border-radius: 6px;
            padding: 8px 14px;
            min-height: 46px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #ffffff;
            margin-bottom: 4px;
        }
        .banner-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .banner-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .banner-title {
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: #ffffff;
            white-space: nowrap;
            line-height: 1;
        }
        .banner-right {
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 1.5px solid rgba(255, 255, 255, 0.45);
            padding-left: 12px;
            height: 30px;
        }
        .shirt-text {
            display: flex;
            flex-direction: column;
            font-family: 'Montserrat', sans-serif;
            font-size: 10px;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            color: #ffffff;
        }

        .card-top {
            display: flex;
            flex-direction: column;
            width: 100%;
        }
        .table-wrap {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 3px 0;
        }

        /* Matrix Table */
        .matrix-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #d2dbe3;
            table-layout: fixed;
        }
        .matrix-table th, 
        .matrix-table td {
            border: 1px solid #d2dbe3;
            text-align: center;
            vertical-align: middle;
            padding: {{ count($matrix) <= 2 ? '8px 4px' : '5px 3px' }};
        }
        .matrix-table thead th {
            background: #dceefb;
            color: #003b70;
            font-family: 'Montserrat', sans-serif;
            font-size: 14.5px;
            font-weight: 700;
            letter-spacing: 0.4px;
            padding: 7px 3px;
            border: 1px solid #c8d8e6;
        }
        .matrix-table th.col-variety {
            width: 28%;
            text-align: left;
            padding-left: 14px;
            font-size: 14px;
            letter-spacing: 0.6px;
        }
        .matrix-table th.col-total {
            width: 15%;
            background: #0051a0;
            font-weight: 800;
            font-size: 16px;
        }
        .matrix-table tbody tr {
            background: #ffffff;
        }
        .matrix-table tbody td.cell-variety {
            text-align: left;
            padding-left: 12px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 14px;
            color: #003b70;
            letter-spacing: 0.4px;
            white-space: nowrap;
        }
        .matrix-table tbody td.cell-qty {
            font-family: 'Roboto', sans-serif;
            font-size: 16.5px;
            font-weight: 700;
            color: #003b70;
        }
        .matrix-table tbody td.col-total {
            background: #eaf3fa;
            font-family: 'Roboto', sans-serif;
            font-size: 17px;
            font-weight: 800;
            color: #003b70;
        }

        /* Pattern Swatches */
        .swatch-wrap {
            display: inline-flex;
            align-items: center;
            gap: 9px;
        }
        .swatch-box {
            width: 34px;
            height: 23px;
            border: 1.5px solid #004b87;
            border-radius: 4px;
            display: inline-block;
            vertical-align: middle;
            flex-shrink: 0;
            box-sizing: border-box;
        }
        .swatch-plains {
            background: #90caf9;
        }

        /* Table Footer / Summary Row */
        .matrix-table tfoot tr {
            background: #004b87;
            color: #ffffff;
        }
        .matrix-table tfoot td {
            border: 1px solid #004b87;
            padding: 7px 3px;
            color: #ffffff;
        }
        .matrix-table tfoot td.footer-label {
            text-align: center;
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: 14.5px;
            letter-spacing: 0.8px;
            color: #ffffff;
        }
        .matrix-table tfoot td.footer-qty {
            font-family: 'Roboto', sans-serif;
            font-size: 16.5px;
            font-weight: 700;
            color: #ffffff;
        }
        .matrix-table tfoot td.footer-grand-total {
            font-family: 'Roboto', sans-serif;
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
            padding-top: 6px;
            margin-top: 2px;
        }
        .feature-item {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            flex: 1;
            padding: 0 8px;
        }
        .feature-item:not(:last-child) {
            border-right: 1px solid #d2dbe3;
        }
        .feature-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .feature-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 11px;
            font-weight: 800;
            color: #003b70;
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

        <div class="card-top">
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
                        <svg width="30" height="30" viewBox="0 0 32 32" fill="none">
                            <!-- Orange sparkles / rays -->
                            <path d="M4 6L1.5 4.5M3 10L0.5 10M7 2.5L5.5 0.5" stroke="#f36b21" stroke-width="2.4" stroke-linecap="round"/>
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
        </div>

        <!-- Table Matrix Wrap -->
        <div class="table-wrap">
            <table class="matrix-table">
            <thead>
                <tr>
                    <th class="col-variety">VARIETY</th>
                    @foreach($sizes as $sz)
                        <th>{{ $sz }}</th>
                    @endforeach
                    <th class="col-total" style="color: #ffffff;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($matrix as $varKey => $sizeCounts)
                    <tr>
                        <td class="cell-variety">
                            <div class="swatch-wrap">
                                @if(str_contains($varKey, 'PLAIN'))
                                    <span class="swatch-box swatch-plains"></span>
                                @elseif(str_contains($varKey, 'CHECK'))
                                    <svg class="swatch-box" viewBox="0 0 34 23">
                                        <defs>
                                            <pattern id="pat-chk-{{ $loop->index }}" width="8" height="8" patternUnits="userSpaceOnUse">
                                                <rect width="4" height="4" fill="#003b70"/>
                                                <rect x="4" width="4" height="4" fill="#7cb3e2"/>
                                                <rect y="4" width="4" height="4" fill="#7cb3e2"/>
                                                <rect x="4" y="4" width="4" height="4" fill="#ffffff"/>
                                            </pattern>
                                        </defs>
                                        <rect width="34" height="23" fill="url(#pat-chk-{{ $loop->index }})"/>
                                    </svg>
                                @elseif(str_contains($varKey, 'STRIPE'))
                                    <svg class="swatch-box" viewBox="0 0 34 23">
                                        <defs>
                                            <pattern id="pat-strp-{{ $loop->index }}" width="6" height="23" patternUnits="userSpaceOnUse">
                                                <rect width="3" height="23" fill="#003b70"/>
                                                <rect x="3" width="3" height="23" fill="#ffffff"/>
                                            </pattern>
                                        </defs>
                                        <rect width="34" height="23" fill="url(#pat-strp-{{ $loop->index }})"/>
                                    </svg>
                                @elseif(str_contains($varKey, 'PRINT'))
                                    <svg class="swatch-box" viewBox="0 0 34 23" style="background: #ffffff;">
                                        <defs>
                                            <pattern id="pat-prnt-{{ $loop->index }}" width="12" height="12" patternUnits="userSpaceOnUse">
                                                <circle cx="6" cy="6" r="1.2" fill="#003b70"/>
                                                <ellipse cx="6" cy="3.8" rx="0.9" ry="1.2" fill="#003b70"/>
                                                <ellipse cx="6" cy="8.2" rx="0.9" ry="1.2" fill="#003b70"/>
                                                <ellipse cx="3.8" cy="6" rx="1.2" ry="0.9" fill="#003b70"/>
                                                <ellipse cx="8.2" cy="6" rx="1.2" ry="0.9" fill="#003b70"/>
                                                <circle cx="0.5" cy="0.5" r="0.9" fill="#4a8bc2"/>
                                                <circle cx="11.5" cy="11.5" r="0.9" fill="#4a8bc2"/>
                                                <circle cx="11.5" cy="0.5" r="0.6" fill="#003b70"/>
                                                <circle cx="0.5" cy="11.5" r="0.6" fill="#003b70"/>
                                            </pattern>
                                        </defs>
                                        <rect width="34" height="23" fill="url(#pat-prnt-{{ $loop->index }})"/>
                                    </svg>
                                @elseif(str_contains($varKey, 'WHITE'))
                                    <span class="swatch-box" style="background: #ffffff; border: 1.5px solid #004b87;"></span>
                                @else
                                    <span class="swatch-box" style="background: #90caf9;"></span>
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
        </div>

        <!-- Bottom Feature Icons -->
        <div class="bottom-features">
            <!-- 1. Sort Easy -->
            <div class="feature-item">
                <div class="feature-icon">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#f36b21" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
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
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#f36b21" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
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
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#f36b21" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
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
                    <svg width="36" height="34" viewBox="0 0 24 24" fill="none" stroke="#f36b21" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
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

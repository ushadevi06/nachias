<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Delivery Order - {{ $invoice->inv_no }}</title>
    @php
        $isDomPdf = false;
        foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $trace) {
            if (isset($trace['class']) && (str_contains($trace['class'], 'Dompdf') || str_contains($trace['class'], 'Barryvdh\DomPDF'))) {
                $isDomPdf = true;
                break;
            }
        }

        $arialRegularPath = $isDomPdf ? str_replace('\\', '/', public_path('assets/fonts/Arial Regular.ttf')) : asset('assets/fonts/Arial Regular.ttf');
        $arialBoldPath = $isDomPdf ? str_replace('\\', '/', public_path('assets/fonts/Arial Bold.ttf')) : asset('assets/fonts/Arial Bold.ttf');
        $robotoRegularPath = $isDomPdf ? str_replace('\\', '/', public_path('assets/fonts/RobotoCondensed-Regular.ttf')) : asset('assets/fonts/RobotoCondensed-Regular.ttf');
        $robotoBoldPath = $isDomPdf ? str_replace('\\', '/', public_path('assets/fonts/RobotoCondensed-Bold.ttf')) : asset('assets/fonts/RobotoCondensed-Bold.ttf');
        $robotoExtraBoldPath = $isDomPdf ? str_replace('\\', '/', public_path('assets/fonts/RobotoCondensed-ExtraBold.ttf')) : asset('assets/fonts/RobotoCondensed-ExtraBold.ttf');
    @endphp
    <style>
        /* 1. Actual TrueType Font Configurations for DomPDF */
        @font-face {
            font-family: 'Arial';
            src: url('{{ $arialRegularPath }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'Arial';
            src: url('{{ $arialBoldPath }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }
        @font-face {
            font-family: 'Roboto Condensed';
            src: url('{{ $robotoRegularPath }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'Roboto Condensed';
            src: url('{{ $robotoBoldPath }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }
        @font-face {
            font-family: 'Roboto Condensed ExtraBold';
            src: url('{{ $robotoExtraBoldPath }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        @page {
            size: A4 landscape;
            margin: 4mm 7mm 4mm 7mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 8.5px;
            color: #000000;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }
        .page-container {
            width: 100%;
        }

        /* Top Header */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1px;
        }
        .header-table td {
            padding: 0;
            vertical-align: top;
        }
        /* Roboto Condensed Bold for company heading */
        .company-name {
            font-family: 'Roboto Condensed', Arial, sans-serif;
            font-size: 19px;
            font-weight: bold;
            margin: 0;
            letter-spacing: 0.5px;
            color: #0b2c64;
            line-height: 1.15;
        }
        /* Arial for address, contact info and general text */
        .company-address {
            font-family: 'Arial', sans-serif;
            font-size: 10.5px;
            margin-top: 1px;
            color: #222;
            line-height: 1.25;
        }
        .complaints-badge-td {
            background-color: #dbf1f4;
            color: #0269a4;
            padding: 3.5px 25px;
            border-radius: 14px;
            font-family: 'Arial', sans-serif;
            font-size: 11.5px;
            font-weight: bold;
            letter-spacing: 0.2px;
        }
        /* Roboto Condensed Bold for Delivery Order title */
        .doc-title-pill {
            background-color: #054cae;
            color: #ffffff;
            font-family: 'Roboto Condensed', Arial, sans-serif;
            font-size: 14.5px;
            font-weight: bold;
            padding: 4px 22px;
            letter-spacing: 0.8px;
            border-radius: 4px;
            display: inline-block;
            text-transform: uppercase;
        }

        /* Metadata Details (Arial) */
        .meta-container {
            width: 100%;
            margin-top: 2px;
            margin-bottom: 3px;
            border-collapse: collapse;
            font-family: 'Arial', sans-serif;
        }
        .meta-container td {
            vertical-align: top;
            padding: 0px 0;
        }
        .meta-left {
            width: 37%;
            font-size: 12.5px;
            line-height: 1.3;
        }
        .meta-left .cust-name {
            font-family: 'Arial', sans-serif;
            font-size: 14.5px;
            font-weight: bold;
            margin-bottom: 1.5px;
            color: #000;
        }
        .meta-center {
            width: 26%;
            text-align: center;
            vertical-align: middle !important;
        }
        .meta-right {
            width: 37%;
            text-align: right;
            font-family: 'Arial', sans-serif;
            font-size: 12.5px;
            line-height: 1.3;
        }
        .meta-right div {
            margin-bottom: 1.5px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #808080;
            margin-bottom: 0px;
            font-family: 'Arial', sans-serif;
        }
        .items-table th, .items-table td {
            border: 1px solid #808080;
            padding: 3.5px 3px;
            font-size: 12px;
            vertical-align: middle;
        }
        /* Arial Bold for table headers */
        .items-table th {
            background-color: #ffffff;
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            font-size: 12px;
            text-align: center;
            color: #000000;
            padding: 3.5px 3px;
        }
        /* Roboto Condensed Bold for section headings */
        .section-bar {
            background-color: #026bb4;
            color: #ffffff;
            font-family: 'Roboto Condensed', Arial, sans-serif;
            font-weight: bold;
            font-size: 10px;
            padding: 3px 6px !important;
            letter-spacing: 0.5px;
        }
        /* Arial Bold for totals and subtotals */
        .subtotal-row {
            background-color: #f0f7fd;
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            color: #0b2545;
        }
        .subtotal-row td {
            padding: 3px 3px;
            font-size: 10.5px;
        }
        /* Blue double horizontal separator between Full Sleeve and Half Sleeve groups */
        .subtotal-f-row td {
            border-bottom: 3px double #026bb4 !important;
        }
        .grand-total-row {
            background-color: #ffffff;
            font-family: 'Arial', sans-serif;
            font-weight: bold;
            border-top: 1.5px solid #026bb4;
            height: 25px;
        }
        .grand-total-row td {
            padding: 5px 3px;
            font-size: 11px;
            vertical-align: middle;
            height: 25px;
        }

        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .text-left { text-align: left !important; }
        .bold { font-family: 'Arial', sans-serif; font-weight: bold; }

        /* Footer Table */
        .footer-summary-table {
            width: 100%;
            border: 1px solid #7ea5c7;
            border-top: none;
            border-collapse: collapse;
            font-family: 'Arial', sans-serif;
            font-size: 9.5px;
            page-break-inside: avoid;
        }
        .footer-summary-table td {
            border: none;
            vertical-align: top;
        }
        .footer-left {
            width: 75.5%;
            border-right: 1px solid #7ea5c7 !important;
            padding: 4px 8px;
        }
        .footer-right {
            width: 24.5%;
            padding: 0;
        }

        /* MRP-Wise Summary Abstract & Marketing Pillars */
        .abstract-banner-box {
            width: 100%;
            margin-top: 3px;
            border: 1.5px solid #01316f;
            border-radius: 4px;
            background-color: #ffffff;
            page-break-inside: avoid;
            font-family: 'Arial', sans-serif;
            overflow: hidden;
        }
        .abstract-main-table {
            width: 100%;
            border-collapse: collapse;
        }
        .abstract-main-table th,
        .abstract-main-table td {
            vertical-align: middle;
        }
        .mrp-title-badge {
            background-color: #01316f;
            color: #ffffff;
            text-align: center;
            vertical-align: middle;
            padding: 4px 6px;
            width: 95px;
            border-right: 1.5px solid #01316f;
        }
        .mrp-title-text {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            font-weight: bold;
            color: #ffffff;
            line-height: 1.25;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .mrp-matrix-label {
            font-weight: bold;
            color: #01316f;
            background-color: #f0f7fd;
            text-align: center;
            width: 65px;
            border: 1px solid #7ea5c7;
            padding: 3.5px 5px;
            font-size: 11.5px;
        }
        .mrp-matrix-val {
            font-weight: bold;
            color: #000000;
            border: 1px solid #7ea5c7;
            text-align: center;
            padding: 3.5px 5px;
            font-size: 11.5px;
        }
        .mrp-matrix-total-hdr {
            background-color: #01316f !important;
            color: #ffffff !important;
            font-weight: bold;
            font-size: 10.5px;
            border: 1px solid #01316f !important;
            width: 55px;
            text-align: center;
            padding: 3.5px 5px;
        }
        .mrp-matrix-total-val {
            background-color: #01316f !important;
            color: #ffffff !important;
            font-weight: bold;
            font-size: 11.5px;
            border: 1px solid #01316f !important;
            width: 55px;
            text-align: center;
            padding: 3.5px 5px;
        }
        .pillar-col {
            text-align: center;
            vertical-align: middle;
            padding: 2px 4px;
            width: 15%;
        }
        .pillar-border-left {
            border-left: 1.5px solid #01316f !important;
        }
        .pillar-border-dashed {
            border-left: 1px dashed #cbd5e1;
        }
        .pillar-col img {
            display: block;
            margin: 0 auto 2px auto;
        }
        .pillar-col div {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            line-height: 1.2;
        }
        .checklist-item {
            font-family: 'Arial', sans-serif;
            font-size: 8px;
            color: #1e3a6c;
            line-height: 1.35;
        }
        .refill-pill {
            background-color: #0b4d8c;
            color: #ffffff;
            padding: 4.5px 36px;
            border-radius: 14px;
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    @php
        $toBase64 = function($relativePath) {
            $fullPath = public_path($relativePath);
            if (file_exists($fullPath)) {
                $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
                return 'data:image/' . ($ext === 'jpg' ? 'jpeg' : ($ext === 'svg' ? 'svg+xml' : $ext)) . ';base64,' . base64_encode(file_get_contents($fullPath));
            }
            return '';
        };

        $casinoLogoBase64  = $toBase64('assets/images/delivery_order/casino_header_logo.png');
        $nachiasLogoBase64 = $toBase64('assets/images/logo_2_blue.png');
        $bannerBoxBase64   = $toBase64('assets/images/delivery_order/banner_box.png');
        $bannerCurveBase64 = $toBase64('assets/images/delivery_order/banner_curve.png');
        $tagIconBase64     = $toBase64('assets/images/delivery_order/icon_tag.png');
        $hangerIconBase64  = $toBase64('assets/images/delivery_order/icon_hanger.png');
        $teamIconBase64    = $toBase64('assets/images/delivery_order/icon_team.png');
        $shirtIconBase64   = $toBase64('assets/images/delivery_order/icon_shirt.png');
        $whiteTagSvg       = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><circle cx="7" cy="7" r="1.5" fill="#ffffff"></circle></svg>');
        $qrCodeBase64      = $toBase64('assets/images/qr_code.png');
        $phoneIconBase64   = $toBase64('assets/images/delivery_order/icon_phone.png');
        $phonePillBase64   = $toBase64('assets/images/delivery_order/icon_phone_pill.png');
        $checkIconBase64   = $toBase64('assets/images/delivery_order/icon_check.svg');

        $getSleeveCode = function($sleeveType) {
            if (empty($sleeveType)) return 'F';
            $s = strtoupper(trim($sleeveType));
            if (in_array($s, ['FULL', 'FULL SLEEVE', 'F/S', 'FS'])) return 'F';
            if (in_array($s, ['HALF', 'HALF SLEEVE', 'H/S', 'HS'])) return 'H';
            return substr($s, 0, 1);
        };

        $standardSizes = ['36', '38', '40', '42', '44', '46', '48', '50'];
        $invSizes = $invoice->items->map(function($i) {
            $s = $i->sizeRatio ? $i->sizeRatio->size : ($i->size ?? '');
            return trim(ltrim($s, 'Ss'));
        })->filter()->unique()->all();

        $allSizes = collect($standardSizes)->merge($invSizes)->unique()->filter(function($s) {
            return $s !== '' && $s !== '-';
        })->sortBy(function($s) {
            return is_numeric($s) ? (int)$s : $s;
        })->values()->all();

        $rows = [];
        foreach ($invoice->items as $item) {
            $seItem = $item->stockEntryItem;
            if (!$seItem && $item->stock_entry_item_id) {
                $seItem = \App\Models\StockEntryItem::with(['rawMaterial', 'item', 'style', 'brand'])->find($item->stock_entry_item_id);
            }
            if (!$seItem && $invoice->so_id) {
                $soItem = \App\Models\SalesOrderItem::where('sale_order_id', $invoice->so_id)->where('sku', $item->sku)->first();
                if ($soItem && $soItem->stock_entry_item_id) {
                    $seItem = \App\Models\StockEntryItem::find($soItem->stock_entry_item_id);
                }
            }

            $art = $seItem ? ($seItem->art_no ?: $item->art_no) : ($item->art_no ?: '-');
            $art = trim($art);

            $sleeveRaw = $item->sleeve_type ?: ($seItem ? $seItem->sleeve_type : '');
            $sleeve = $getSleeveCode($sleeveRaw);

            $rate = (float)($item->rate ?? 0);
            $mrp = (float)($item->mrp ?? 0);
            $rawSize = $item->sizeRatio ? $item->sizeRatio->size : ($item->size ?? '');
            $cleanSize = trim(ltrim($rawSize, 'Ss'));
            $qty = (float)($item->quantity ?? 0);

            $brandName = '';
            $styleName = '';
            if ($seItem && $seItem->brand) {
                $brandName = $seItem->brand->brand_name;
            }
            if ($seItem && $seItem->style) {
                $styleName = $seItem->style->style_name;
            }
            if (empty($brandName) && $item->item && $item->item->brand) {
                $brandName = $item->item->brand->brand_name;
            }
            if (empty($styleName) && $item->item && $item->item->style) {
                $styleName = $item->item->style->style_name;
            }
            $desc = trim($brandName . ' ' . $styleName);

            $key = $art . '_' . $desc . '_' . $sleeve . '_' . number_format($rate, 2, '.', '') . '_' . number_format($mrp, 2, '.', '');

            if (!isset($rows[$key])) {
                $rows[$key] = [
                    'art' => $art,
                    'desc' => $desc,
                    'sleeve' => $sleeve,
                    'rate' => $rate,
                    'mrp' => $mrp,
                    'sizes' => [],
                    'total_qty' => 0,
                ];
            }

            $rows[$key]['sizes'][$cleanSize] = ($rows[$key]['sizes'][$cleanSize] ?? 0) + $qty;
            $rows[$key]['total_qty'] += $qty;
        }

        $fullRows = [];
        $halfRows = [];

        foreach ($rows as $r) {
            if ($r['sleeve'] === 'F') {
                $fullRows[] = $r;
            } else {
                $halfRows[] = $r;
            }
        }

        // Calculate Totals for Full Sleeves
        $fSizeTotals = array_fill_keys($allSizes, 0);
        $fTotalQty = 0;
        foreach ($fullRows as $r) {
            foreach ($allSizes as $s) {
                $q = $r['sizes'][$s] ?? 0;
                $fSizeTotals[$s] += $q;
            }
            $fTotalQty += $r['total_qty'];
        }

        // Calculate Totals for Half Sleeves
        $hSizeTotals = array_fill_keys($allSizes, 0);
        $hTotalQty = 0;
        foreach ($halfRows as $r) {
            foreach ($allSizes as $s) {
                $q = $r['sizes'][$s] ?? 0;
                $hSizeTotals[$s] += $q;
            }
            $hTotalQty += $r['total_qty'];
        }

        // Grand Totals
        $grandSizeTotals = array_fill_keys($allSizes, 0);
        foreach ($allSizes as $s) {
            $grandSizeTotals[$s] = $fSizeTotals[$s] + $hSizeTotals[$s];
        }
        $grandTotalQty = $fTotalQty + $hTotalQty;

        // Calculate MRP-wise Quantity Summary for Abstract
        $mrpSummary = [];
        $totalMrpQty = 0;
        foreach ($invoice->items as $item) {
            $m = (float)($item->mrp ?? 0);
            $q = (float)($item->quantity ?? 0);
            if ($q > 0) {
                $mrpKey = (floor($m) == $m) ? (string)(int)$m : number_format($m, 2, '.', '');
                $mrpSummary[$mrpKey] = ($mrpSummary[$mrpKey] ?? 0) + $q;
                $totalMrpQty += $q;
            }
        }
        uksort($mrpSummary, function($a, $b) {
            return floatval($a) <=> floatval($b);
        });

        // Build display list without section header bars (as requested)
        $displayItems = [];
        if (!empty($fullRows)) {
            foreach ($fullRows as $fr) {
                $displayItems[] = ['type' => 'item', 'data' => $fr];
            }
        }

        if (!empty($halfRows)) {
            foreach ($halfRows as $hr) {
                $displayItems[] = ['type' => 'item', 'data' => $hr];
            }
        }

        // Single page limit with full promotional banner & footer: max 12 items
        // Intermediate pages without footer/banner: max 18 items
        $totalItemsCount = count($fullRows) + count($halfRows);
        $totalDisplayRows = count($displayItems);
        $chunks = [];
        if ($totalDisplayRows <= 12) {
            $chunks = [$displayItems];
        } elseif ($totalDisplayRows <= 30) {
            // Fits cleanly across exactly 2 pages (Page 1 up to 18 items, Page 2 up to 12 items with banner)
            $page1Take = ($totalDisplayRows <= 24) ? (int)ceil($totalDisplayRows / 2) : ($totalDisplayRows - 12);
            $chunks = [
                array_slice($displayItems, 0, $page1Take),
                array_slice($displayItems, $page1Take)
            ];
        } else {
            $offset = 0;
            while ($offset < $totalDisplayRows) {
                $remaining = $totalDisplayRows - $offset;
                if ($remaining <= 12) {
                    $take = $remaining;
                } elseif ($remaining <= 30) {
                    $take = ($remaining <= 24) ? (int)ceil($remaining / 2) : ($remaining - 12);
                } else {
                    $take = 18;
                }
                $chunks[] = array_slice($displayItems, $offset, $take);
                $offset += $take;
            }
        }
        $rowCounter = 1;
        $totalCols = 6 + count($allSizes) + 1;
    @endphp

    @foreach($chunks as $chunkIndex => $chunkItems)
        @php
            $isFirstChunk = ($chunkIndex === 0);
            $isLastChunk = ($chunkIndex === count($chunks) - 1);
        @endphp
        <div class="page-container" style="{{ !$isLastChunk ? 'page-break-after: always;' : '' }}">
            <!-- Header Table -->
            <table class="header-table">
                <tr>
                    <!-- Left: Casino Brand Logo -->
                    <td style="width: 23%; text-align: left;">
                        @if(!empty($casinoLogoBase64))
                            <img src="{{ $casinoLogoBase64 }}" style="width: 195px; height: auto; margin-top: 2px;" alt="Casino">
                        @else
                            <div style="font-family: Arial, sans-serif; font-size: 28px; font-weight: bold; color: #0b2c64; letter-spacing: 1px; line-height: 1;">CASINO</div>
                            <div style="font-family: Arial, sans-serif; font-size: 10.5px; font-weight: bold; color: #475569; letter-spacing: 0.5px; margin-top: 2px;">no compromise</div>
                        @endif
                    </td>
                    <!-- Center: Company Name, Address, Contact details & Complaints badge -->
                    <td style="width: 54%; text-align: center;">
                        <div class="company-name">
                            @if(!empty($nachiasLogoBase64))
                                <img src="{{ $nachiasLogoBase64 }}" style="height: 25px; width: auto; max-width: 20px; vertical-align: middle; display: inline; margin-right: 6px; position: relative; top: -1px;" alt="Logo">
                            @endif
                            <span style="vertical-align: middle;">{{ strtoupper($setting->company_name ?? 'NACHIAS FASHION PRIVATE LIMITED') }}</span>
                        </div>
                        <div class="company-address">
                            {{ preg_replace('/,\s*/', ', ', trim(preg_replace('/\s+/', ' ', str_replace(["\r", "\n"], ' ', $setting->address ?? '272/2, SOMU NAGAR, SIRINGERI NAGAR (SARATHAMBAL KOVIL BACKSIDE), BYEPASS ROAD, MADURAI - 625016')))) }}<br>
                            Phone: {{ !empty($setting->phone) ? $setting->phone : '8489938071, 8489938073' }} | Email: {{ !empty($setting->email) ? $setting->email : 'srinachias@yahoo.in, sales@nachias.com' }} | GSTIN: {{ !empty($setting->gst_no) ? $setting->gst_no : '33AADCN9342A1ZU' }}
                        </div>
                        <table align="center" style="margin: 2px auto 0 auto; border-collapse: collapse;">
                            <tr>
                                <td class="complaints-badge-td">
                                    @if(!empty($phoneIconBase64))
                                        <img src="{{ $phoneIconBase64 }}" style="width: 12px; height: 12px; vertical-align: middle; margin-right: 4px;" alt="">
                                    @endif
                                    For complaints or doubts, call @ {{ !empty($setting->phone) ? explode(',', $setting->phone)[0] : '8489938072' }}
                                </td>
                            </tr>
                        </table>
                    </td>
                    <!-- Right Spacer -->
                    <td style="width: 23%; text-align: right;">
                        &nbsp;
                    </td>
                </tr>
            </table>

            <!-- Customer & Order Meta Table (with Delivery Order badge centered) -->
            <table class="meta-container">
                <tr>
                    <td class="meta-left">
                        <div class="cust-name">M/S. {{ $invoice->customer->name ?? '' }}</div>
                        <div>{{ strtoupper($invoice->customer->address_line_1 ?? '') }} {{ strtoupper($invoice->customer->address_line_2 ?? '') }}</div>
                        <div>{{ strtoupper($invoice->customer->city->city_name ?? '') }} {{ $invoice->customer->zip_code ? '- ' . $invoice->customer->zip_code : '' }} ({{ $invoice->customer->state->state_name ?? '' }})</div>
                        <div><strong class="bold">GSTIN :</strong> {{ $invoice->customer->gst_no ?? 'N/A' }}</div>
                    </td>
                    <td class="meta-center">
                        <span class="doc-title-pill">DELIVERY ORDER</span>
                    </td>
                    <td class="meta-right">
                        <div><strong class="bold">Invoice No :</strong> <span style="font-weight: bold; font-size:17px">{{ $invoice->inv_no }}</span></div>
                        <div><strong class="bold">Packed On :</strong> {{ $invoice->inv_date ? $invoice->inv_date->format('d/m/Y') : date('d/m/Y') }}</div>
                        <div><strong class="bold">Order No :</strong> {{ $invoice->salesOrder->so_no ?? $invoice->salesOrder->order_no ?? '-' }}</div>
                        <div><strong class="bold">Total Qty :</strong> {{ (int)$grandTotalQty }} PCS</div>
                        @if(count($chunks) > 1)
                            <div style="font-weight: bold; margin-top: 1px;">Page {{ $chunkIndex + 1 }} of {{ count($chunks) }}</div>
                        @endif
                    </td>
                </tr>
            </table>

            <!-- Main Items Table -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 3.5%; vertical-align: middle;">Sl.No.</th>
                        <th rowspan="2" style="width: 21%; vertical-align: middle;">Description</th>
                        <th rowspan="2" style="width: 13.5%; vertical-align: middle;">Art No.</th>
                        <th rowspan="2" style="width: 9.25%; vertical-align: middle;">MRP</th>
                        <th rowspan="2" style="width: 9.25%; vertical-align: middle;">WSP</th>
                        <th rowspan="2" style="width: 4.5%; vertical-align: middle;">Sleeve</th>
                        <th colspan="{{ count($allSizes) }}" style="text-align: center; vertical-align: middle; padding: 2px 2px;">Size</th>
                        <th style="width: 6%; vertical-align: middle; border-bottom: none;">Total</th>
                    </tr>
                    <tr>
                        @foreach($allSizes as $size)
                            <th style="width: {{ round(33 / count($allSizes), 2) }}%; vertical-align: middle; padding: 2px 1px;">{{ $size }}</th>
                        @endforeach
                        <th style="width: 6%; border-top: none;">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($chunkItems as $item)
                        @if($item['type'] === 'header')
                            <tr>
                                <td colspan="{{ $totalCols }}" class="section-bar">
                                    @if(!empty($shirtIconBase64))
                                        <img src="{{ $shirtIconBase64 }}" style="width: 12px; height: auto; vertical-align: middle; margin-right: 4px;" alt="">
                                    @endif
                                    {{ $item['title'] }}
                                </td>
                            </tr>
                        @elseif($item['type'] === 'item')
                            @php $r = $item['data']; @endphp
                            <tr>
                                <td class="text-center">{{ $rowCounter++ }}</td>
                                <td style="padding-left: 6px;">{{ $r['desc'] }}</td>
                                <td class="bold" style="padding-left: 6px;">{{ $r['art'] }}</td>
                                <td class="text-right bold" style="padding-right: 6px;">{{ number_format($r['mrp'], 2) }}</td>
                                <td class="text-right bold" style="padding-right: 6px;">{{ number_format($r['rate'], 2) }}</td>
                                <td class="text-center bold">{{ $r['sleeve'] }}</td>
                                @foreach($allSizes as $size)
                                    <td class="text-center">
                                        {{ isset($r['sizes'][$size]) && $r['sizes'][$size] > 0 ? (int)$r['sizes'][$size] : '' }}
                                    </td>
                                @endforeach
                                <td class="text-center bold">{{ (int)$r['total_qty'] }}</td>
                            </tr>
                        @elseif($item['type'] === 'subtotal')
                            <tr class="subtotal-row {{ ($item['sleeve'] ?? '') === 'F' ? 'subtotal-f-row' : '' }}">
                                <td colspan="6" class="text-right bold" style="padding-right: 8px;">{{ $item['label'] }}</td>
                                @foreach($allSizes as $size)
                                    <td class="text-center bold">
                                        {{ isset($item['sizes'][$size]) && $item['sizes'][$size] > 0 ? (int)$item['sizes'][$size] : ($item['total'] == 0 ? '-' : '') }}
                                    </td>
                                @endforeach
                                <td class="text-center bold" style="background-color: #dbeafe;">{{ (int)$item['total'] }}</td>
                            </tr>
                        @endif
                    @endforeach

                    @if($isLastChunk)
                        @php
                            $emptyRowsNeeded = max(0, 12 - count($chunkItems));
                        @endphp
                        @for($e = 0; $e < $emptyRowsNeeded; $e++)
                            <tr class="item-row">
                                <td class="text-center">&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td class="text-center">&nbsp;</td>
                                @foreach($allSizes as $s)
                                    <td>&nbsp;</td>
                                @endforeach
                                <td>&nbsp;</td>
                            </tr>
                        @endfor

                        <!-- Grand Total Row -->
                        <tr class="grand-total-row" style="height: 25px;">
                            <td colspan="6" class="text-right bold" style="padding-right: 8px; font-size: 14px; vertical-align: middle; height: 30px;">Grand Total :</td>
                            @foreach($allSizes as $size)
                                <td class="text-center bold" style="font-size: 13.5px; vertical-align: middle; height: 25px;">
                                    {{ isset($grandSizeTotals[$size]) && $grandSizeTotals[$size] > 0 ? (int)$grandSizeTotals[$size] : ($grandTotalQty == 0 ? '-' : '') }}
                                </td>
                            @endforeach
                            <td class="text-center bold" style="background-color: #dbeafe; font-size: 14px; vertical-align: middle; height: 25px;">{{ (int)$grandTotalQty }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            @if($isLastChunk)
                <!-- Footer Summary Box -->
                <table class="footer-summary-table">
                    <tr>
                        <td class="footer-left">
                            <div style="font-size: 11.5px; margin-bottom: 5px;">
                                <strong class="bold">Remarks :</strong> {{ $invoice->remarks ?: '' }}
                            </div>
                            <table style="width: 100%; border-collapse: collapse; font-size: 11.5px; margin-top: 5px;">
                                <tr>
                                    <td style="width: 50%; padding: 0; vertical-align: top;">
                                        <strong class="bold">Prepared By :</strong> {{ auth()->user()->name ?? 'Admin' }}
                                    </td>
                                    <td style="width: 50%; padding: 0; vertical-align: top;">
                                        <strong class="bold">Checked by :</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td class="footer-right">
                            <table style="width: 100%; border-collapse: collapse; font-family: 'Arial', sans-serif;">
                                <tr>
                                    <td class="bold" style="font-size: 11.5px; padding: 3px 6px;">Total Box :</td>
                                    <td class="bold" style="font-size: 11.5px; text-align: right; padding: 3px 8px;">{{ $invoice->no_of_box ?: '1' }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <!-- Bottom MRP-Wise Summary Abstract & Action Pillars -->
                <div class="abstract-banner-box">
                    <table class="abstract-main-table">
                        <tr>
                            <!-- Navy Blue Title Block -->
                            <td rowspan="2" class="mrp-title-badge">
                                @if(!empty($whiteTagSvg))
                                    <img src="{{ $whiteTagSvg }}" style="width: 17px; height: 17px; display: block; margin: 0 auto 2px auto;" alt="">
                                @endif
                                <div class="mrp-title-text">
                                    MRP-WISE<br>QUANTITY<br>SUMMARY
                                </div>
                            </td>

                            <!-- Matrix Grid Row 1: MRP Headers -->
                            <th class="mrp-matrix-label">MRP</th>
                            @foreach($mrpSummary as $mrpVal => $mrpQty)
                                <th class="mrp-matrix-val">{{ $mrpVal }}</th>
                            @endforeach
                            <th class="mrp-matrix-total-hdr">TOTAL</th>

                            <!-- 3 Action Pillars (Spanning both rows) -->
                            <td rowspan="2" class="pillar-col pillar-border-left">
                                @if(!empty($tagIconBase64))
                                    <img src="{{ $tagIconBase64 }}" style="width: 25px; height: auto;" alt="">
                                @endif
                                <div>Price the<br>products quickly</div>
                            </td>
                            <td rowspan="2" class="pillar-col pillar-border-dashed">
                                @if(!empty($hangerIconBase64))
                                    <img src="{{ $hangerIconBase64 }}" style="width: 25px; height: auto;" alt="">
                                @endif
                                <div>Display at<br>prominent counters</div>
                            </td>
                            <td rowspan="2" class="pillar-col pillar-border-dashed">
                                @if(!empty($teamIconBase64))
                                    <img src="{{ $teamIconBase64 }}" style="width: 29px; height: auto;" alt="">
                                @endif
                                <div>Share new arrivals<br>with your team</div>
                            </td>
                        </tr>
                        <tr>
                            <!-- Matrix Grid Row 2: Qty Values -->
                            <td class="mrp-matrix-label">Qty (Pcs)</td>
                            @foreach($mrpSummary as $mrpVal => $mrpQty)
                                <td class="mrp-matrix-val bold">{{ (int)$mrpQty }}</td>
                            @endforeach
                            <td class="mrp-matrix-total-val">{{ (int)$totalMrpQty }}</td>
                        </tr>
                    </table>
                </div>
            @else
                <div style="text-align: right; font-family: 'Arial', sans-serif; font-weight: bold; font-size: 8.5px; margin-top: 3px;">
                    Continue to Page No. {{ $chunkIndex + 2 }}
                </div>
            @endif
        </div>
    @endforeach
</body>
</html>

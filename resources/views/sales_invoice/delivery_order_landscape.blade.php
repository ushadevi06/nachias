<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Delivery Order (Landscape) - {{ $invoice->inv_no }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 10mm 8mm 10mm;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 11px;
            color: #000000;
            line-height: 1.25;
            margin: 0;
            padding: 0;
        }
        .header-box {
            text-align: center;
            margin-bottom: 4px;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .company-address {
            font-size: 9px;
            margin-top: 1px;
            color: #222;
            line-height: 1.2;
        }
        .meta-container {
            width: 100%;
            margin-top: 6px;
            margin-bottom: 8px;
            border-collapse: collapse;
        }
        .meta-container td {
            vertical-align: top;
            padding: 4px 0;
        }
        .meta-left {
            width: 60%;
            font-size: 10px;
            line-height: 1.45;
        }
        .meta-left div {
            margin-bottom: 2.5px;
        }
        .meta-left div:last-child {
            margin-bottom: 0;
        }
        .meta-right {
            width: 40%;
            text-align: right;
            font-size: 10px;
            line-height: 1.45;
        }
        .meta-right div {
            margin-bottom: 2.5px;
        }
        .meta-right div:last-child {
            margin-bottom: 0;
        }
        .meta-title {
            font-size: 12px;
            font-weight: bold;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin-bottom: 0px;
        }
        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 2px 3px;
            font-size: 9.5px;
            vertical-align: middle;
        }
        .items-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .text-left { text-align: left !important; }
        .bold { font-weight: bold; }
        
        .footer-table {
            width: 100%;
            border: 1px solid #000;
            border-top: none;
            border-collapse: collapse;
            font-size: 9.5px;
            page-break-inside: avoid;
        }
        .footer-table td {
            border: none;
            padding: 4px 6px;
            vertical-align: top;
        }
        .footer-left {
            width: 55%;
            border-right: 1px solid #000 !important;
        }
        .footer-right {
            width: 45%;
        }
        .box-line {
            display: inline-block;
            border-bottom: 1px solid #000;
            width: 65px;
            height: 10px;
        }
    </style>
</head>
<body>
    @php
        $getSleeveCode = function($sleeveType) {
            if (empty($sleeveType)) return '-';
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
        $orderDate = $invoice->salesOrder && $invoice->salesOrder->so_date ? $invoice->salesOrder->so_date->format('d/m/Y') : ($invoice->inv_date ? $invoice->inv_date->format('d/m/Y') : '');

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

            $key = $art . '_' . $sleeve . '_' . number_format($rate, 2, '.', '') . '_' . number_format($mrp, 2, '.', '');

            if (!isset($rows[$key])) {
                $rows[$key] = [
                    'date' => $orderDate,
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

        $sizeTotals = array_fill_keys($allSizes, 0);
        $grandTotalQty = 0;
        foreach ($rows as $r) {
            foreach ($allSizes as $s) {
                $q = $r['sizes'][$s] ?? 0;
                $sizeTotals[$s] += $q;
            }
            $grandTotalQty += $r['total_qty'];
        }

        $rowsList = array_values($rows);
        $totalRows = count($rowsList);
        $chunks = [];

        if ($totalRows <= 15) {
            $chunks = [$rowsList];
        } else {
            $offset = 0;
            while ($offset < $totalRows) {
                $remaining = $totalRows - $offset;
                if ($offset === 0) {
                    $take = min(15, $remaining - 2);
                } elseif ($remaining <= 14) {
                    $take = $remaining;
                } else {
                    $take = min(18, $remaining - 2);
                }
                $chunks[] = array_slice($rowsList, $offset, $take);
                $offset += $take;
            }
        }
    @endphp

    @foreach($chunks as $chunkIndex => $chunkRows)
        @php
            $isFirstChunk = ($chunkIndex === 0);
            $isLastChunk = ($chunkIndex === count($chunks) - 1);
        @endphp
        <div style="{{ !$isLastChunk ? 'page-break-after: always;' : '' }}">
            <!-- Company Header -->
            <div class="header-box">
                <div class="company-name">{{ strtoupper($setting->company_name ?? 'NACHIAS FASHION PRIVATE LIMITED') }}</div>
                <div class="company-address">
                    {{ $setting->address ?? '272/2, SOMU NAGAR, SIRINGERI NAGAR (SARATHAMBAL KOVIL BACKSIDE), BYEPASS ROAD, MADURAI - 625016' }}<br>
                    Phone: {{ $setting->phone ?? '8489938071, 8489938073' }} | Email: {{ $setting->email ?? 'srinachias@yahoo.in, sales@nachias.com' }} | GSTIN: {{ $setting->gst_no ?? '33AADCN9342A1ZU' }}
                </div>
            </div>

            <!-- Metadata Table -->
            <table class="meta-container">
                <tr>
                    <td class="meta-left">
                        <div><strong>Party Name :</strong> {{ $invoice->customer->code ?? '' }}</div>
                        <div class="meta-title">M/S. {{ $invoice->customer->name ?? '' }}</div>
                        <div>{{ strtoupper($invoice->customer->address_line_1 ?? '') }} {{ strtoupper($invoice->customer->address_line_2 ?? '') }}</div>
                        <div>{{ strtoupper($invoice->customer->city->city_name ?? '') }} {{ $invoice->customer->zip_code ? '- ' . $invoice->customer->zip_code : '' }} ({{ $invoice->customer->state->state_name ?? '' }})</div>
                        <div><strong>GSTIN :</strong> {{ $invoice->customer->gst_no ?? 'N/A' }}</div>
                    </td>
                    <td class="meta-right">
                        <div style="font-size: 13px; font-weight: bold; margin-bottom: 2px;">DELIVERY ORDER</div>
                        <div><strong>Packing Slip No :</strong> {{ $invoice->inv_no }}</div>
                        <div><strong>Date :</strong> {{ $invoice->inv_date ? $invoice->inv_date->format('d/m/Y') : date('d/m/Y') }}</div>
                        <div><strong>Order No :</strong> {{ $invoice->salesOrder->so_no ?? $invoice->salesOrder->order_no ?? '-' }}</div>
                        <div><strong>Total Qty :</strong> {{ (int)$grandTotalQty }} PCS</div>
                        @if(count($chunks) > 1)
                            <div style="font-weight: bold; margin-top: 2px;">Page {{ $chunkIndex + 1 }} of {{ count($chunks) }}</div>
                        @endif
                    </td>
                </tr>
            </table>

            <!-- Main Grid Table without Order No column -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 8%;">Date</th>
                        <th style="width: 14%;">Item No.</th>
                        <th style="width: 20%;">Description</th>
                        <th style="width: 4%;">Slv</th>
                        @foreach($allSizes as $size)
                            <th style="width: {{ round(32 / count($allSizes), 2) }}%;">S{{ $size }}</th>
                        @endforeach
                        <th style="width: 6%;">Total</th>
                        <th style="width: 8%;">Rate</th>
                        <th style="width: 8%;">MRP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($chunkRows as $r)
                        <tr>
                            <td class="text-center">{{ $r['date'] }}</td>
                            <td class="bold">{{ $r['art'] }}</td>
                            <td>{{ $r['desc'] }}</td>
                            <td class="text-center bold">{{ $r['sleeve'] }}</td>
                            @foreach($allSizes as $size)
                                <td class="text-center">
                                    {{ isset($r['sizes'][$size]) && $r['sizes'][$size] > 0 ? (int)$r['sizes'][$size] : '' }}
                                </td>
                            @endforeach
                            <td class="text-center bold">{{ (int)$r['total_qty'] }}</td>
                            <td class="text-right">{{ number_format($r['rate'], 2) }}</td>
                            <td class="text-right">{{ number_format($r['mrp'], 2) }}</td>
                        </tr>
                    @endforeach

                    @if($isLastChunk)
                        <!-- Total Row -->
                        <tr style="background-color: #f9f9f9; font-weight: bold;">
                            <td colspan="4" class="text-right bold" style="padding-right: 8px;">Total :</td>
                            @foreach($allSizes as $size)
                                <td class="text-center bold">
                                    {{ $sizeTotals[$size] > 0 ? (int)$sizeTotals[$size] : '' }}
                                </td>
                            @endforeach
                            <td class="text-center bold" style="background-color: #e5e7eb;">{{ (int)$grandTotalQty }}</td>
                            <td colspan="2"></td>
                        </tr>
                    @endif
                </tbody>
            </table>

            @if($isLastChunk)
                <!-- Bottom Footer Box -->
                <table class="footer-table">
                    <tr>
                        <td class="footer-left">
                            <div style="font-weight: bold; font-size: 10px; margin-bottom: 4px;">
                                CASE : {{ $invoice->no_of_box ?: '1' }}
                            </div>
                            <div style="margin-bottom: 4px;">
                                <strong>Special Instructions :</strong> {{ $invoice->remarks ?: 'None' }}
                            </div>
                            <div style="margin-top: 10px;">
                                <strong>Packed By :</strong> _________________________________
                            </div>
                        </td>
                        <td class="footer-right">
                            <table style="width: 100%; border-collapse: collapse; font-size: 9.5px;">
                                <tr>
                                    <td style="font-weight: bold; font-size: 11px; padding: 1px 0;">Total :</td>
                                    <td style="font-weight: bold; font-size: 12px; text-align: right; padding: 1px 0;">{{ (int)$grandTotalQty }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 2px 0; font-weight: bold; font-size: 10.5px;">Total Box :</td>
                                    <td style="padding: 2px 0; font-weight: bold; font-size: 10.5px; text-align: right;">{{ $invoice->no_of_box ?: '1' }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            @else
                <div style="text-align: right; font-weight: bold; font-size: 9.5px; margin-top: 4px;">
                    Continue to Page No. {{ $chunkIndex + 2 }}
                </div>
            @endif
        </div>
    @endforeach
</body>
</html>

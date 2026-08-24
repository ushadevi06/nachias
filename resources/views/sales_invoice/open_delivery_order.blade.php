<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Open Delivery Order - {{ $invoice->inv_no }}</title>
    <style>
        @page {
            margin: 320px 20px 20px 20px;
        }
        header {
            position: fixed;
            top: -295px;
            left: 0;
            right: 0;
            height: 280px;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 12px;
            color: #000000;
            line-height: 1.35;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-bottom: 10px;
        }
        .header-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
        .company-name {
            font-size: 17px;
            margin: 0;
            font-family: 'Helvetica', Arial, sans-serif;
        }
        .company-details {
            margin: 3px 0 0 0;
            font-size: 12px;
            font-family: 'Helvetica', Arial, sans-serif;
        }
        .doc-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
            font-family: 'Helvetica', Arial, sans-serif;
            color: #000000;
        }
        .page-num:after {
            content: counter(page);
        }
        .page-total:after {
            content: counter(pages);
        }
        .meta-table {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .meta-table > tr > td, .meta-table > tbody > tr > td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
            width: 50%;
        }
        .group-header-bar {
            background-color: #60a5fa;
            color: #000;
            padding: 0px;
            border: 1px solid #60a5fa;
            border-bottom: none;
            margin: 15px 0 5px;
            text-transform: uppercase;
            font-size: 14px;
            margin-bottom: 5px;
            line-height: 1.4;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }
        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 5px 4px;
            font-size: 11px;
            vertical-align: middle;
            color: #000000;
        }
        .items-table th {
            background-color: #cccccc;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center !important;
        }
        .text-right {
            text-align: right !important;
        }
        .bold {
            font-weight: bold;
        }
        .bg-light-blue {
            background-color: #bfdbfe !important;
        }
    </style>
</head>
<body>
    @php
        $groupedItems = $invoice->items->groupBy(function($item) use ($invoice) {
            $brandName = '';
            
            $seItem = $item->stockEntryItem;
            $soItem = null;
            if ($invoice->so_id) {
                $soItem = \App\Models\SalesOrderItem::where('sale_order_id', $invoice->so_id)
                    ->where('sku', $item->sku)
                    ->first();
                if (!$seItem && $soItem && $soItem->stock_entry_item_id) {
                    $seItem = \App\Models\StockEntryItem::find($soItem->stock_entry_item_id);
                }
            }

            if ($seItem && $seItem->brand) {
                $brandName = $seItem->brand->brand_name;
            }
            
            if (empty($brandName) && $seItem && !empty($seItem->finished_item_code)) {
                $parts = explode('-', $seItem->finished_item_code);
                if (count($parts) > 0) {
                    $dbBrand = \App\Models\Brand::where('code', trim($parts[0]))->first();
                    if ($dbBrand) {
                        $brandName = $dbBrand->brand_name;
                    }
                }
            }
            
            if (empty($brandName)) {
                $codeToParse = '';
                if ($soItem) {
                    $codeToParse = $soItem->getAttributes()['item_name'] ?? '';
                    if (empty($codeToParse) || $codeToParse === '-') {
                        $codeToParse = $soItem->finished_item_code;
                    }
                }
                if (empty($codeToParse) || $codeToParse === '-') {
                    if ($seItem) {
                        $codeToParse = $seItem->finished_item_code;
                    } elseif ($item->item) {
                        $codeToParse = $item->item->code;
                    }
                }
                if (empty($codeToParse) || $codeToParse === '-') {
                    $codeToParse = $item->sku ?? '';
                }
                
                if (!empty($codeToParse) && $codeToParse !== '-') {
                    $parts = explode('-', $codeToParse);
                    $mainCode = trim($parts[0]);
                    $allBrands = \App\Models\Brand::all();
                    foreach ($allBrands as $brand) {
                        $bCode = trim($brand->code);
                        if (!empty($bCode) && stripos($mainCode, $bCode) === 0) {
                            $brandName = $brand->brand_name;
                            break;
                        }
                    }
                }
            }

            if (empty(trim($brandName)) && $invoice->brand) {
                $brandName = $invoice->brand->brand_name;
            }

            return trim($brandName);
        });

        // Get all unique sizes sorted
        $allSizes = $invoice->items->map(function($item) {
            return $item->sizeRatio ? $item->sizeRatio->size : ($item->size ?? '-');
        })->unique()->sortBy(function($size) {
            return is_numeric($size) ? (int)$size : $size;
        })->values()->all();

        $totalPcs = $invoice->items->sum('quantity');
    @endphp

    <header>
    <table class="header-table" style="padding-bottom: 8px; margin-bottom: 8px;">
        <tr>
            <td style="width: 70px; text-align: left; padding-right: 15px; vertical-align: top;">
                @php
                    $logoPath = null;
                    if (!empty($setting->logo_file) && file_exists(public_path($setting->logo_file))) {
                        $logoPath = public_path($setting->logo_file);
                    } elseif (file_exists(public_path('assets/images/jc_logo.png'))) {
                        $logoPath = public_path('assets/images/jc_logo.png');
                    }
                @endphp
                @if($logoPath)
                    <img src="{{ $logoPath }}" style="width: 65px; height: auto;" alt="Logo">
                @endif
            </td>
            <td style="text-align: left; vertical-align: top;">
                <div style="font-size: 16px; font-weight: normal; margin: 0; line-height: 1.2;">{{ $setting->company_name ?? 'Nachias Fashion Private Limited' }}</div>
                <div style="font-size: 12px; margin: 2px 0 0 0; line-height: 1.2; color: #000;">
                    {!! nl2br(e($setting->address ?? "272/2, Somu Nagar, Siringeri Nagar\n(Sarathambal Kovil Backside),\nByepass Road, Madurai - 625016")) !!}
                </div>
                <div style="font-size: 12px; margin: 2px 0 0 0; line-height: 1.2; color: #000;">
                    GSTIN: {{ $setting->gst_no ?? '33AADCN9342A1ZU' }}
                </div>
            </td>
        </tr>
    </table>

    <div class="doc-title">Delivery Order</div>
    <div class="page-info" style="color: transparent;">Page: &nbsp; 1 / 1</div>

    <table class="meta-table">
        <tr>
            <td style="padding: 6px 8px;">
                <table style="width: 100%; border: none; border-collapse: collapse; margin: 0; padding: 0;">
                    <tr>
                        <td style="width: 50px; border: none; padding: 2px 0; vertical-align: top; font-size: 13px;">M/S</td>
                        <td style="width: 15px; border: none; padding: 2px 0; vertical-align: top; font-size: 13px;">:</td>
                        <td style="border: none; padding: 2px 0; vertical-align: top; font-size: 13px;">
                            {{ $invoice->customer->name ?? '-' }}<br>
                            {!! nl2br(e(strtoupper(\App\Models\SalesInvoice::cleanAddress($invoice->delivery_address)))) !!}<br>
                            @php
                                $locParts = [];
                                if($invoice->customer->city->city_name ?? false) $locParts[] = strtoupper($invoice->customer->city->city_name);
                                if($invoice->customer->state->state_name ?? false) $locParts[] = strtoupper($invoice->customer->state->state_name);
                                $locStr = implode(', ', $locParts);
                                if($invoice->customer->zip_code ?? false) $locStr .= ($locStr ? ' - ' : '') . $invoice->customer->zip_code;
                            @endphp
                            @if($locStr)
                                {{ $locStr }}<br>
                            @endif
                            @if($invoice->customer && $invoice->customer->mobile_no)
                                {{ $invoice->customer->mobile_no }}
                            @endif
                        </td>
                    </tr>
                </table>
                <div style="margin-top: 15px; font-size: 13px;">
                    GSTIN: {{ $invoice->customer->gst_no ?? '-' }}
                </div>
            </td>
            <td style="padding: 6px 8px;">
                <table style="width: 100%; border: none; border-collapse: collapse; margin: 0; padding: 0;">
                    <tr>
                        <td style="width: 75px; border: none; padding: 2px 0; vertical-align: top; font-size: 13px;">PL No.</td>
                        <td style="width: 15px; border: none; padding: 2px 0; vertical-align: top; font-size: 13px;">:</td>
                        <td style="border: none; padding: 2px 0; vertical-align: top; font-size: 13px;">{{ $invoice->inv_no }}</td>
                    </tr>
                    <tr>
                        <td style="border: none; padding: 2px 0; vertical-align: top; font-size: 13px;">Packed On</td>
                        <td style="border: none; padding: 2px 0; vertical-align: top; font-size: 13px;">:</td>
                        <td style="border: none; padding: 2px 0; vertical-align: top; font-size: 13px;">{{ $invoice->inv_date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td style="border: none; padding: 2px 0; vertical-align: top; font-size: 13px;">Order No.</td>
                        <td style="border: none; padding: 2px 0; vertical-align: top; font-size: 13px;">:</td>
                        <td style="border: none; padding: 2px 0; vertical-align: top; font-size: 13px;">{{ $invoice->salesOrder->order_no ?? ($invoice->salesOrder->so_no ?? '-') }}</td>
                    </tr>
                    <tr>
                        <td style="border: none; padding: 2px 0; vertical-align: top; font-size: 13px;">Total Qty</td>
                        <td style="border: none; padding: 2px 0; vertical-align: top; font-size: 13px;">:</td>
                        <td style="border: none; padding: 2px 0; vertical-align: top; font-size: 13px;">{{ number_format($totalPcs, 0) }}</td>
                    </tr>
                    <tr>
                        <td style="border: none; padding: 2px 0; vertical-align: top; font-size: 13px;">No.of Units</td>
                        <td style="border: none; padding: 2px 0; vertical-align: top; font-size: 13px;">:</td>
                        <td style="border: none; padding: 2px 0; vertical-align: top; font-size: 13px;">{{ $invoice->no_of_box ?: 1 }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    </header>

    <main>
    @foreach($groupedItems as $groupName => $items)
        @php
            $rows = [];
            foreach ($items as $item) {
                $soItem = \App\Models\SalesOrderItem::where('sale_order_id', $invoice->so_id)
                    ->where('sku', $item->sku)
                    ->first();
                $codeToParse = '';
                if ($soItem) {
                    $codeToParse = $soItem->getAttributes()['item_name'] ?? '';
                    if (empty($codeToParse) || $codeToParse === '-') {
                        $codeToParse = $soItem->finished_item_code;
                    }
                }
                
                if (empty($codeToParse) || $codeToParse === '-') {
                    if ($item->stockEntryItem) {
                        $codeToParse = $item->stockEntryItem->finished_item_code;
                    } elseif ($item->item) {
                        $codeToParse = $item->item->code;
                    }
                }
                
                if (empty($codeToParse) || $codeToParse === '-') {
                    $codeToParse = $item->sku ?? '';
                }

                $brandName = '';
                $styleName = '';
                $sleeveType = $item->sleeve_type;

                $seItem = $item->stockEntryItem;
                if (!$seItem && $invoice->so_id) {
                    $soItem = \App\Models\SalesOrderItem::where('sale_order_id', $invoice->so_id)
                        ->where('sku', $item->sku)
                        ->first();
                    if ($soItem && $soItem->stock_entry_item_id) {
                        $seItem = \App\Models\StockEntryItem::find($soItem->stock_entry_item_id);
                    }
                }

                // 1. Get Brand Name (priority: from finished_item_code, e.g. CF => Casino Formal)
                if ($seItem && !empty($seItem->finished_item_code)) {
                    $parts = explode('-', $seItem->finished_item_code);
                    if (count($parts) > 0) {
                        $dbBrand = \App\Models\Brand::where('code', trim($parts[0]))->first();
                        if ($dbBrand) {
                            $brandName = $dbBrand->brand_name;
                        }
                    }
                }
                if (empty($brandName)) {
                    if ($item->item && $item->item->brand) {
                        $brandName = $item->item->brand->brand_name;
                    } elseif ($seItem && $seItem->brand) {
                        $brandName = $seItem->brand->brand_name;
                    } elseif ($item->brandCategory) {
                        $brandName = $item->brandCategory->name;
                    } elseif ($invoice->brand) {
                        $brandName = $invoice->brand->brand_name;
                    }
                }

                // 2. Get Style Name (Authoritative priority: finished_item_code e.g. CF-CKD-FS => CKD => CHECKED)
                if ($seItem && !empty($seItem->finished_item_code)) {
                    $parts = explode('-', $seItem->finished_item_code);
                    if (count($parts) >= 2) {
                        $styleCode = trim($parts[1]);
                        $dbStyle = \App\Models\Style::where('code', $styleCode)->first();
                        if ($dbStyle) {
                            $styleName = $dbStyle->style_name;
                        } elseif (strcasecmp($styleCode, 'CKD') === 0) {
                            $styleName = 'CHECKED';
                        } elseif (strcasecmp($styleCode, 'PLN') === 0 || strcasecmp($styleCode, 'PL') === 0) {
                            $styleName = 'PLAIN';
                        } elseif (strcasecmp($styleCode, 'PRT') === 0) {
                            $styleName = 'PRINT';
                        } elseif (strcasecmp($styleCode, 'STR') === 0 || strcasecmp($styleCode, 'STP') === 0) {
                            $styleName = 'STRIPES';
                        }
                    }
                }

                // Fallback to relations
                if (empty($styleName)) {
                    if ($item->item && $item->item->style) {
                        $styleName = $item->item->style->style_name;
                    } elseif ($seItem && $seItem->style) {
                        $styleName = $seItem->style->style_name;
                    } elseif ($seItem && $seItem->item && $seItem->item->style) {
                        $styleName = $seItem->item->style->style_name;
                    }
                }

                // 3. Sleeve
                if (empty($sleeveType) && $seItem) {
                    $sleeveType = $seItem->sleeve_type;
                }

                $sleeveDisplay = '';
                if (!empty($sleeveType)) {
                    $sUpper = strtoupper(trim($sleeveType));
                    if ($sUpper === 'FULL' || $sUpper === 'FULL SLEEVE' || $sUpper === 'F/S' || $sUpper === 'FS') {
                        $sleeveDisplay = 'F/S';
                    } elseif ($sUpper === 'HALF' || $sUpper === 'HALF SLEEVE' || $sUpper === 'H/S' || $sUpper === 'HS') {
                        $sleeveDisplay = 'H/S';
                    } else {
                        $sleeveDisplay = $sleeveType;
                    }
                }

                // Format: CASINO FORMAL CHECKED F/S
                $desc = trim(($brandName ? $brandName . ' ' : '') . ($styleName ? $styleName . ' ' : '') . $sleeveDisplay);
                if (empty($desc)) {
                    $desc = $item->sku ?? '-';
                }
                
                $uom = ($item->uom && $item->uom->uom_code) ? $item->uom->uom_code : 'PCS';
                $mrp = $item->mrp ?? 0;
                $rate = $item->rate ?? 0;
                $art = $item->stockEntryItem ? ($item->stockEntryItem->art_no ?: $item->art_no) : ($item->art_no ?? '-');
                $size = $item->sizeRatio ? $item->sizeRatio->size : ($item->size ?? '-');
                $qty = $item->quantity;

                if (!isset($rows[$desc])) {
                    $rows[$desc] = [
                        'uom' => $uom,
                        'prices' => []
                    ];
                }

                $priceKey = number_format($mrp, 2, '.', '') . '_' . number_format($rate, 2, '.', '');
                if (!isset($rows[$desc]['prices'][$priceKey])) {
                    $rows[$desc]['prices'][$priceKey] = [
                        'mrp' => $mrp,
                        'rate' => $rate,
                        'arts' => [],
                        'quantities' => [],
                        'total' => 0
                    ];
                }

                if ($art && $art !== '-') {
                    $rows[$desc]['prices'][$priceKey]['arts'][] = $art;
                }

                if (!isset($rows[$desc]['prices'][$priceKey]['quantities'][$size])) {
                    $rows[$desc]['prices'][$priceKey]['quantities'][$size] = 0;
                }
                $rows[$desc]['prices'][$priceKey]['quantities'][$size] += $qty;
                $rows[$desc]['prices'][$priceKey]['total'] += $qty;
            }

            $groupSizeTotals = array_fill_keys($allSizes, 0);
            $groupGrandTotal = 0;
        @endphp

        <div style="page-break-inside: avoid;">
        <div class="group-header-bar">{{ $groupName }}</div>
        <table class="items-table" style="margin-bottom: 15px;">
            <thead>
                <tr>
                    <th rowspan="2" style="text-align: center; padding-left: 6px;">Description</th>
                    <th rowspan="2" style="width: 6%;">UOM</th>
                    @if(empty($invoice->delivery_show_fields) || in_array('mrp', $invoice->delivery_show_fields))
                    <th rowspan="2" style="width: 10%;">Retail Price</th>
                    @endif
                    @if(empty($invoice->delivery_show_fields) || in_array('price', $invoice->delivery_show_fields))
                    <th rowspan="2" style="width: 10%;">Unit Price</th>
                    @endif
                    <th colspan="{{ count($allSizes) }}">Size</th>
                    <th rowspan="2" style="width: 4%;">Total</th>
                    @if(empty($invoice->delivery_show_fields) || in_array('art_no', $invoice->delivery_show_fields))
                    <th rowspan="2" style="width: 19%;">Art</th>
                    @endif
                </tr>
                <tr>
                    @foreach($allSizes as $size)
                        <th style="font-weight: normal; padding: 2px;">{{ $size }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $desc => $descData)
                    @php
                        $priceCount = count($descData['prices']);
                        $isFirstPrice = true;
                    @endphp
                    @foreach($descData['prices'] as $priceKey => $priceData)
                        @php
                            $uniqueArts = array_unique($priceData['arts']);
                            $artDisplay = !empty($uniqueArts) ? implode(', ', $uniqueArts) : '-';
                        @endphp
                        <tr>
                            @if($isFirstPrice)
                                <td rowspan="{{ $priceCount }}" style="padding-left: 6px; vertical-align: middle;">{{ $desc }}</td>
                                <td rowspan="{{ $priceCount }}" class="text-center" style="vertical-align: middle;">{{ $descData['uom'] }}</td>
                                @php $isFirstPrice = false; @endphp
                            @endif
                            @if(empty($invoice->delivery_show_fields) || in_array('mrp', $invoice->delivery_show_fields))
                            <td class="text-right" style="padding-right: 6px;">{{ number_format($priceData['mrp'], 2) }}</td>
                            @endif
                            @if(empty($invoice->delivery_show_fields) || in_array('price', $invoice->delivery_show_fields))
                            <td class="text-right" style="padding-right: 6px;">{{ number_format($priceData['rate'], 2) }}</td>
                            @endif
                            @foreach($allSizes as $size)
                                @php
                                    $qty = $priceData['quantities'][$size] ?? 0;
                                    $groupSizeTotals[$size] += $qty;
                                @endphp
                                <td class="text-center">{{ $qty > 0 ? number_format($qty, 0) : '' }}</td>
                            @endforeach
                            <td class="text-center bold bg-light-blue">{{ number_format($priceData['total'], 0) }}</td>
                            @if(empty($invoice->delivery_show_fields) || in_array('art_no', $invoice->delivery_show_fields))
                            <td class="text-center" style="font-size: 10px; word-break: break-all;">{{ $artDisplay }}</td>
                            @endif
                            @php
                                $groupGrandTotal += $priceData['total'];
                            @endphp
                        </tr>
                    @endforeach
                @endforeach
                <tr class="bold bg-light-blue">
                    @php
                        $leftColspan = 2;
                        if(empty($invoice->delivery_show_fields) || in_array('mrp', $invoice->delivery_show_fields)) $leftColspan++;
                        if(empty($invoice->delivery_show_fields) || in_array('price', $invoice->delivery_show_fields)) $leftColspan++;
                    @endphp
                    <td colspan="{{ $leftColspan }}" class="text-right" style="padding-right: 8px;">Total</td>
                    @foreach($allSizes as $size)
                        <td class="text-center">{{ $groupSizeTotals[$size] > 0 ? number_format($groupSizeTotals[$size], 0) : '' }}</td>
                    @endforeach
                    <td class="text-center">{{ number_format($groupGrandTotal, 0) }}</td>
                    @if(empty($invoice->delivery_show_fields) || in_array('art_no', $invoice->delivery_show_fields))
                    <td>&nbsp;</td>
                    @endif
                </tr>
            </tbody>
        </table>
        </div>
    @endforeach

    <div style="height: 160px; width: 100%; color: transparent; display: block;">.</div>
    <div style="position: absolute; bottom: 0; left: 0; width: 100%;">
        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <tr>
                <td colspan="2" style="border: 1px solid #000000; height: 60px; padding: 6px; vertical-align: top;">
                    <strong>Remarks : </strong> {{ $invoice->remarks ?? '' }}
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #000000; width: 50%; height: 80px; text-align: center; vertical-align: middle; font-size: 13px;">
                    Prepared By<br><br>
                    <strong>{{ auth()->user()->name ?? '' }}</strong>
                </td>
                <td style="border: 1px solid #000000; width: 50%; height: 80px; text-align: center; vertical-align: middle; font-size: 13px;">
                    Checked By
                </td>
            </tr>
        </table>
    </div>
    </main>
    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script('
                $text = "Page: " . $PAGE_NUM . " / " . $PAGE_COUNT;
                $size = 11;
                $font = $fontMetrics->getFont("Helvetica");
                $width = $fontMetrics->get_text_width($text, $font, $size);
                $x = ($pdf->get_width() - $width - 20);
                
                // Position exactly where the hardcoded text is on all pages
                $y = 110;
                
                $pdf->text($x, $y, $text, $font, $size);
            ');
        }
    </script>
</body>
</html>
    </script>
</body>
</html>

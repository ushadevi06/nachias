<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Delivery Order - {{ $invoice->inv_no }}</title>
    <style>
        @page {
            margin: 25px 20px 20px 20px;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 13px;
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
            vertical-align: middle;
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
            margin: 15px 0 5px 0;
            font-family: 'Helvetica', Arial, sans-serif;
            color: #000000;
        }
        .page-info {
            text-align: right;
            font-size: 11px;
            margin-bottom: 5px;
            font-family: 'Helvetica', Arial, sans-serif;
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
        .footer-sig {
            margin-top: 40px;
            width: 100%;
            border: none;
        }
        .footer-sig td {
            border: none;
            width: 50%;
            vertical-align: top;
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

            return trim($brandName);
        });

        $allSizes = $invoice->items->map(function($item) {
            return $item->sizeRatio ? $item->sizeRatio->size : ($item->size ?? '-');
        })->unique()->sortBy(function($size) {
            return is_numeric($size) ? (int)$size : $size;
        })->values()->all();

        $totalPcs = $invoice->items->sum('quantity');
    @endphp

    <table class="header-table">
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
    <div class="page-info">Page: &nbsp; 1 / 1</div>

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
                $sleeve = '';
                $resolved = false;

                $seItem = $item->stockEntryItem;
                if (!$seItem && $soItem && $soItem->stock_entry_item_id) {
                    $seItem = \App\Models\StockEntryItem::find($soItem->stock_entry_item_id);
                }

                if ($seItem) {
                    if ($seItem->brand) {
                        $brandName = $seItem->brand->brand_name;
                    }
                    if ($seItem->style) {
                        $styleName = $seItem->style->style_name;
                    }
                    
                    if (empty($brandName) && !empty($seItem->finished_item_code)) {
                        $seParts = explode('-', $seItem->finished_item_code);
                        if (count($seParts) > 0) {
                            $dbBrand = \App\Models\Brand::where('code', trim($seParts[0]))->first();
                            if ($dbBrand) {
                                $brandName = $dbBrand->brand_name;
                            }
                        }
                    }
                    
                    if (!empty($brandName) || !empty($styleName)) {
                        $resolved = true;
                    }
                    if (empty($sleeve) && !empty($seItem->sleeve_type)) {
                        $sleeve = $seItem->sleeve_type;
                    }
                }
                
                if (!empty($item->sleeve_type)) {
                    $sleeveValUpper = strtoupper(trim($item->sleeve_type));
                    if ($sleeveValUpper === 'FULL' || $sleeveValUpper === 'F/S' || $sleeveValUpper === 'FS') {
                        $sleeve = 'F/S';
                    } elseif ($sleeveValUpper === 'HALF' || $sleeveValUpper === 'H/S' || $sleeveValUpper === 'HS') {
                        $sleeve = 'H/S';
                    } else {
                        $sleeve = $item->sleeve_type;
                    }
                } elseif (!empty($sleeve)) {
                    $sleeveValUpper = strtoupper(trim($sleeve));
                    if ($sleeveValUpper === 'FULL' || $sleeveValUpper === 'F/S' || $sleeveValUpper === 'FS') {
                        $sleeve = 'F/S';
                    } elseif ($sleeveValUpper === 'HALF' || $sleeveValUpper === 'H/S' || $sleeveValUpper === 'HS') {
                        $sleeve = 'H/S';
                    }
                }

                if ((empty($brandName) || empty($styleName)) && !empty($codeToParse) && $codeToParse !== '-') {
                    $parts = explode('-', $codeToParse);
                    
                    if (count($parts) >= 2) {
                        $brandCode = trim($parts[0]);
                        $styleCode = trim($parts[1]);
                        
                        $dbBrand = \App\Models\Brand::where('code', $brandCode)->first();
                        $dbStyle = \App\Models\Style::where('code', $styleCode)->first();
                        
                        if ($dbBrand && empty($brandName)) {
                            $brandName = $dbBrand->brand_name;
                        }
                        if ($dbStyle && empty($styleName)) {
                            $styleName = $dbStyle->style_name;
                        }
                        if (!empty($brandName) || !empty($styleName)) {
                            $resolved = true;
                        }
                        
                        if (empty($sleeve) && isset($parts[2])) {
                            $sleeveVal = trim($parts[2]);
                            $sleeveValUpper = strtoupper($sleeveVal);
                            if ($sleeveValUpper === 'FULL' || $sleeveValUpper === 'F/S' || $sleeveValUpper === 'FS') {
                                $sleeve = 'F/S';
                            } elseif ($sleeveValUpper === 'HALF' || $sleeveValUpper === 'H/S' || $sleeveValUpper === 'HS') {
                                $sleeve = 'H/S';
                            } else {
                                $sleeve = $sleeveVal;
                            }
                        }
                    }
                    
                    if ((empty($brandName) || empty($styleName)) && count($parts) >= 1) {
                        $mainCode = trim($parts[0]);
                        $dbBrand = null;
                        $brandCode = '';
                        $allBrands = \App\Models\Brand::all();
                        foreach ($allBrands as $brand) {
                            $bCode = trim($brand->code);
                            if (!empty($bCode) && stripos($mainCode, $bCode) === 0) {
                                $dbBrand = $brand;
                                $brandCode = $bCode;
                                break;
                            }
                        }
                        
                        if ($dbBrand) {
                            if (empty($brandName)) {
                                $brandName = $dbBrand->brand_name;
                            }
                            $styleCode = substr($mainCode, strlen($brandCode));
                            $dbStyle = \App\Models\Style::where('code', $styleCode)->first();
                            if ($dbStyle && empty($styleName)) {
                                $styleName = $dbStyle->style_name;
                            }
                            $resolved = true;
                            
                            if (empty($sleeve) && isset($parts[1])) {
                                $sleeveVal = trim($parts[1]);
                                $sleeveValUpper = strtoupper($sleeveVal);
                                if ($sleeveValUpper === 'FULL' || $sleeveValUpper === 'F/S' || $sleeveValUpper === 'FS') {
                                    $sleeve = 'F/S';
                                } elseif ($sleeveValUpper === 'HALF' || $sleeveValUpper === 'H/S' || $sleeveValUpper === 'HS') {
                                    $sleeve = 'H/S';
                                } else {
                                    $sleeve = $sleeveVal;
                                }
                            }
                        }
                    }
                }

                if ($resolved) {
                    $itemName = trim($brandName . ' ' . $styleName . ' ' . $sleeve);
                } else {
                    $itemName = $codeToParse;
                }

                $sleeveDisplay = '';
                if ($item->sleeve_type) {
                    $sleeveLower = strtolower(trim($item->sleeve_type));
                    if ($sleeveLower === 'half') {
                        $sleeveDisplay = 'H/S';
                    } elseif ($sleeveLower === 'full') {
                        $sleeveDisplay = 'F/S';
                    } else {
                        $sleeveDisplay = $item->sleeve_type;
                    }
                }
                $desc = $itemName;
                $uom = $item->uom_id ?? '-';
                $art = $item->art_no ?? '-';
                $mrp = $item->mrp ?? 0;
                $rate = $item->rate ?? 0;
                $size = $item->sizeRatio ? $item->sizeRatio->size : ($item->size ?? '-');
                $qty = $item->quantity;

                $key = $desc . '_' . $uom . '_' . $art . '_' . $mrp . '_' . $rate;

                if (!isset($rows[$key])) {
                    $rows[$key] = [
                        'description' => $desc,
                        'uom' => $uom,
                        'art' => $art,
                        'mrp' => $mrp,
                        'rate' => $rate,
                        'quantities' => [],
                        'total' => 0
                    ];
                }

                if (!isset($rows[$key]['quantities'][$size])) {
                    $rows[$key]['quantities'][$size] = 0;
                }
                $rows[$key]['quantities'][$size] += $qty;
                $rows[$key]['total'] += $qty;
            }

            $groupSizeTotals = array_fill_keys($allSizes, 0);
            $groupGrandTotal = 0;
        @endphp

        <div class="group-header-bar">{{ $groupName }}</div>
        <table class="items-table" style="margin-bottom: 15px;">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 28%; text-align: left; padding-left: 6px;">Description</th>
                    <th rowspan="2" style="width: 8%;">UOM</th>
                    @if(is_null($invoice->delivery_show_fields) || in_array('art_no', $invoice->delivery_show_fields))
                    <th rowspan="2" style="width: 16%;">Art</th>
                    @endif
                    @if(is_null($invoice->delivery_show_fields) || in_array('mrp', $invoice->delivery_show_fields))
                    <th rowspan="2" style="width: 10%; text-align: right; padding-right: 6px;">Retail Price</th>
                    @endif
                    @if(is_null($invoice->delivery_show_fields) || in_array('price', $invoice->delivery_show_fields))
                    <th rowspan="2" style="width: 10%; text-align: right; padding-right: 6px;">Unit Price</th>
                    @endif
                    <th colspan="{{ count($allSizes) }}">Size</th>
                    <th rowspan="2" style="width: 6%;">Total</th>
                </tr>
                <tr>
                    @foreach($allSizes as $size)
                        <th style="font-weight: normal; padding: 2px;">{{ $size }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                    <tr>
                        <td style="padding-left: 6px;">{{ $row['description'] }}</td>
                        <td class="text-center">{{ $row['uom'] }}</td>
                        @if(is_null($invoice->delivery_show_fields) || in_array('art_no', $invoice->delivery_show_fields))
                        <td class="text-center">{{ $row['art'] }}</td>
                        @endif
                        @if(is_null($invoice->delivery_show_fields) || in_array('mrp', $invoice->delivery_show_fields))
                        <td class="text-right" style="padding-right: 6px;">{{ number_format($row['mrp'], 2) }}</td>
                        @endif
                        @if(is_null($invoice->delivery_show_fields) || in_array('price', $invoice->delivery_show_fields))
                        <td class="text-right" style="padding-right: 6px;">{{ number_format($row['rate'], 2) }}</td>
                        @endif
                        @foreach($allSizes as $size)
                            @php
                                $qty = $row['quantities'][$size] ?? 0;
                                $groupSizeTotals[$size] += $qty;
                            @endphp
                            <td class="text-center">{{ $qty > 0 ? number_format($qty, 0) : '' }}</td>
                        @endforeach
                        <td class="text-center bold bg-light-blue">{{ number_format($row['total'], 0) }}</td>
                        @php
                            $groupGrandTotal += $row['total'];
                        @endphp
                    </tr>
                @endforeach
                <tr class="bold bg-light-blue">
                    @php
                        $leftColspan = 2;
                        if(is_null($invoice->delivery_show_fields) || in_array('art_no', $invoice->delivery_show_fields)) $leftColspan++;
                        if(is_null($invoice->delivery_show_fields) || in_array('mrp', $invoice->delivery_show_fields)) $leftColspan++;
                        if(is_null($invoice->delivery_show_fields) || in_array('price', $invoice->delivery_show_fields)) $leftColspan++;
                    @endphp
                    <td colspan="{{ $leftColspan }}" class="text-right" style="padding-right: 8px;">Total</td>
                    @foreach($allSizes as $size)
                        <td class="text-center">{{ $groupSizeTotals[$size] > 0 ? number_format($groupSizeTotals[$size], 0) : '0' }}</td>
                    @endforeach
                    <td class="text-center">{{ number_format($groupGrandTotal, 0) }}</td>
                </tr>
            </tbody>
        </table>
    @endforeach
    <div style="position: absolute; bottom: 0; left: 0; width: 100%;">
        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <tr>
                <td colspan="2" style="border: 1px solid #000000; height: 60px; padding: 6px; vertical-align: top;">
                    <strong>Remarks :</strong>
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
</body>
</html>

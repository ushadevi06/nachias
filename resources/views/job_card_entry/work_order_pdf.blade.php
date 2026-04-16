<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Work Order - {{ $jobCard->job_card_no }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            width: 100%;
            margin-bottom: 5px;
        }
        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin: 0;
        }
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 5px 0;
        }
        .metadata-table {
            width: 100%;
            margin-bottom: 5px;
        }
        .metadata-table td {
            padding: 2px 0;
        }
        .label {
            font-weight: bold;
            width: 80px;
        }
        .value {
            border-bottom: none;
        }
        .section-header {
            background-color: #72a1e3;
            color: #fff;
            padding: 5px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 10px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .data-table th {
            background-color: #f2f2f2;
            border: 1px solid #333;
            padding: 4px;
            text-align: center;
            font-weight: bold;
        }
        .data-table td {
            border: 1px solid #333;
            padding: 4px;
            text-align: center;
        }
        .text-end {
            text-align: right !important;
        }
        .footer-row {
            font-weight: bold;
            background-color: #f2f2f2;
        }
        .bg-matrix {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <table class="metadata-table">
        <tr>
            <td class="label">Printed On:</td>
            <td>{{ now()->format('d/m/Y H:i') }}</td>
        </tr>
    </table>

    <div class="title">Job Card</div>

    <table class="metadata-table">
        <tr>
            <td class="label">Job Card No #</td>
            <td class="value">: {{ $jobCard->job_card_no }}</td>
            <td class="label" style="text-align: right;">Plant</td>
            <td class="value" style="text-align: left;">: {{ $jobCard->serviceProvider->name ?? 'Nachias Fashion Private Limited' }}</td>
        </tr>
        <tr>
            <td class="label">Job Card Date</td>
            <td class="value">: {{ $jobCard->job_card_date ? date('d/m/Y', strtotime($jobCard->job_card_date)) : '-' }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <div class="section-header">
        {{ ($jobCard->item && $jobCard->item->name) ? $jobCard->item->name : ($jobCard->brand->brand_name ?? 'JOB CARD DETAILS') }}
    </div>

    @php
        $defaultSizes = [];
        $sizes = $defaultSizes;
        if ($jobCard->sizeRatio && $jobCard->sizeRatio->size) {
            $sizes = array_values(array_filter(array_map('trim', explode(',', $jobCard->sizeRatio->size))));
        } else {
            $sizes = $jobCard->fabricDetails->pluck('quantities')->flatten()->pluck('size')->unique()->values()->toArray();
        }
    @endphp

    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2">Item</th>
                <th rowspan="2">Description</th>
                <th rowspan="2">Model</th>
                <th rowspan="2">UOM</th>
                <th rowspan="2">Art</th>
                <th colspan="{{ count($sizes) }}">Size</th>
                <th rowspan="2">Total</th>
            </tr>
            <tr>
                @foreach($sizes as $size)
                    <th>{{ $size }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotal = 0;
                $sizeTotals = array_fill_keys($sizes, 0);

                $fullSleeveRows = [];
                $halfSleeveRows = [];

                foreach($jobCard->fabricDetails as $detail) {
                    $trimmedArt = trim($detail->art_no ?? '');
                    if (($artCategoryMap[$trimmedArt] ?? 1) != 1) {
                        continue;
                    }

                    $allPOItems = $jobCard->purchaseOrder?->items;
                    
                    if (!$allPOItems && $detail->art_no) {
                        $grnItem = \App\Models\GrnEntryItem::where('art_no', $detail->art_no)->whereHas('purchaseInvoiceItem.purchaseOrderItem')->first();
                        $allPOItems = $grnItem?->purchaseInvoiceItem?->purchaseOrderItem?->purchaseOrder?->items;
                    }

                    $matchingPOItem = $allPOItems ? (
                        $allPOItems->where('store_category_id', 1)->whereNotNull('style_id')->first() 
                        ?: $allPOItems->whereNotNull('style_id')->first() 
                        ?: $allPOItems->first()
                    ) : null;

                    $uom = ($matchingPOItem && $matchingPOItem->uom) ? $matchingPOItem->uom->uom_code : (($matchingPOItem && $matchingPOItem->rawMaterial && $matchingPOItem->rawMaterial->uom) ? $matchingPOItem->rawMaterial->uom->uom_code : ($artUomMap[$detail->art_no] ?? '-'));
                    $style = $matchingPOItem?->style?->style_name ?? $allPOItems?->whereNotNull('style_id')->first()?->style?->style_name ?? '';
                    $brandCode = $jobCard->brand->code ?? '';
                    $brandName = $jobCard->brand->brand_name ?? '';
                    $artNo = $detail->art_no;
                    $displayStyle = $style ?: $artNo;

                    $hasValue = function($prefix) use ($detail) {
                        return $detail->quantities->where('qty_' . $prefix, '>', 0)->count() > 0;
                    };

                    $getSizesArray = function($prefix) use ($detail, $sizes) {
                        $arr = [];
                        foreach($sizes as $s) {
                            $q = $detail->quantities->where('size', $s)->first();
                            $arr[$s] = $q ? ($q->{'qty_' . $prefix} ?? 0) : 0;
                        }
                        return $arr;
                    };

                    if ($hasValue('fs')) {
                        $fullSleeveRows[] = [
                            'item_no' => trim($brandCode . '-' . $displayStyle . '-F/S', '-'),
                            'description' => trim($brandName . ' ' . $style . ' F/S'),
                            'uom' => $uom,
                            'art' => $artNo,
                            'sizes' => $getSizesArray('fs')
                        ];
                    }

                    if ($hasValue('hs')) {
                        $halfSleeveRows[] = [
                            'item_no' => trim($brandCode . '-' . $displayStyle . '-H/S', '-'),
                            'description' => trim($brandName . ' ' . $style . ' H/S'),
                            'uom' => $uom,
                            'art' => $artNo,
                            'sizes' => $getSizesArray('hs')
                        ];
                    }
                }

                $allRows = array_merge($fullSleeveRows, $halfSleeveRows);
            @endphp
            @foreach($allRows as $row)
                @php
                    $rowTotal = array_sum($row['sizes']);
                    $grandTotal += $rowTotal;
                    foreach($sizes as $sizeKey) {
                        $sizeTotals[$sizeKey] += $row['sizes'][$sizeKey];
                    }
                @endphp
                <tr>
                    <td>{{ $row['item_no'] }}</td>
                    <td>{{ $row['description'] }}</td>
                    <td></td>
                    <td>{{ $row['uom'] }}</td>
                    <td>{{ $row['art'] }}</td>
                    @foreach($sizes as $sizeKey)
                        <td>{{ $row['sizes'][$sizeKey] > 0 ? (int)$row['sizes'][$sizeKey] : '' }}</td>
                    @endforeach
                    <td style="background-color: #d9e9ff; font-weight: bold;">{{ $rowTotal > 0 ? (int)$rowTotal : '' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="footer-row" style="background-color: #d9e9ff;">
                <td colspan="5" style="text-align: center;">TOTAL</td>
                @foreach($sizes as $sizeKey)
                    <td>{{ $sizeTotals[$sizeKey] > 0 ? (int)$sizeTotals[$sizeKey] : '' }}</td>
                @endforeach
                <td style="font-weight: bold; font-size: 11px;">{{ $grandTotal > 0 ? (int)$grandTotal : '' }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>

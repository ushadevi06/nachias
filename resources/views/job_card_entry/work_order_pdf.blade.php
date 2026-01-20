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
            <td style="text-align: right;" class="label">Page:</td>
            <td style="text-align: left; width: 40px;">1/1</td>
        </tr>
    </table>

    <div class="title">Work Order</div>

    <table class="metadata-table">
        <tr>
            <td class="label">WO#</td>
            <td class="value">: {{ $jobCard->job_card_no }}</td>
            <td class="label" style="text-align: right;">Plant</td>
            <td class="value" style="text-align: left;">: Nachias Fashion Private Limited</td>
        </tr>
        <tr>
            <td class="label">WO Date</td>
            <td class="value">: {{ $jobCard->job_card_date ? date('d/m/Y', strtotime($jobCard->job_card_date)) : '-' }}</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <div class="section-header">
        {{ $jobCard->brand->brand_name ?? 'JOB CARD DETAILS' }}
    </div>

    @php
        $defaultSizes = ['36', '38', '40', '42', '44', '46', '48'];
        $sizes = $defaultSizes;
        if ($jobCard->sizeRatio && $jobCard->sizeRatio->size) {
            $sizes = array_values(array_filter(array_map('trim', explode(',', $jobCard->sizeRatio->size))));
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
                    $allPOItems = $jobCard->purchaseOrder->items;
                    $matchingPOItem = $allPOItems->where('art_no', $detail->art_no)->whereNotNull('style_id')->first() ?: $allPOItems->where('art_no', $detail->art_no)->first();
                    
                    $uom = ($matchingPOItem && $matchingPOItem->uom) ? $matchingPOItem->uom->uom_code : (($matchingPOItem && $matchingPOItem->rawMaterial && $matchingPOItem->rawMaterial->uom) ? $matchingPOItem->rawMaterial->uom->uom_code : ($artUomMap[$detail->art_no] ?? '-'));
                    
                    $style = $matchingPOItem->style->style_name ?? $allPOItems->whereNotNull('style_id')->first()?->style?->style_name ?? '';
                    $description = trim(($jobCard->brand->brand_name ?? '') . ' ' . $style);
                    $description = $description ?: '-';
                    $artNo = $detail->art_no;

                    // Helper to check if any size has value
                    $hasValue = function($prefix) use ($detail) {
                        return $detail->quantities->where('qty_' . $prefix, '>', 0)->count() > 0;
                    };
                    
                    // Helper to get sizes array
                    $getSizesArray = function($prefix) use ($detail, $sizes) {
                        $arr = [];
                        foreach($sizes as $s) {
                            $q = $detail->quantities->where('size', $s)->first();
                            $arr[$s] = $q ? ($q->{'qty_' . $prefix} ?? 0) : 0;
                        }
                        return $arr;
                    };

                    // Full Sleeve
                    if ($hasValue('fs')) {
                        $fullSleeveRows[] = [
                            'item_no' => $artNo . '-F/S',
                            'description' => $description . ' (F/S)',
                            'uom' => $uom,
                            'art' => $artNo,
                            'sizes' => $getSizesArray('fs')
                        ];
                    }

                    // Half Sleeve
                    if ($hasValue('hs')) {
                        $halfSleeveRows[] = [
                            'item_no' => $artNo . '-H/S',
                            'description' => $description . ' (H/S)',
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
                    foreach($row['sizes'] as $size => $qty) {
                        $sizeTotals[$size] += $qty;
                    }
                @endphp
                <tr>
                    <td>{{ $row['item_no'] }}</td>
                    <td>{{ $row['description'] }}</td>
                    <td></td>
                    <td>{{ $row['uom'] }}</td>
                    <td>{{ $row['art'] }}</td>
                    @foreach($sizes as $sizeKey)
                        <td>{{ $row['sizes'][$sizeKey] > 0 ? $row['sizes'][$sizeKey] : '' }}</td>
                    @endforeach
                    <td class="bg-matrix">{{ $rowTotal }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="footer-row">
                <td colspan="5" class="text-end">Total</td>
                @foreach($sizes as $sizeKey)
                    <td>{{ $sizeTotals[$sizeKey] > 0 ? $sizeTotals[$sizeKey] : '' }}</td>
                @endforeach

                <td class="bg-matrix">{{ $grandTotal }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>

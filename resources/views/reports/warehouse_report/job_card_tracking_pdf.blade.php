<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Job Card Tracking - {{ $jobCard->job_card_no }}</title>
    <style>
        @page {
            margin: 15px 20px;
        }
        * {
            font-family: 'Helvetica', sans-serif !important;
        }
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.2;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .metadata-table {
            margin-bottom: 8px;
        }
        .metadata-table td {
            padding: 2px 4px;
            vertical-align: top;
        }
        .metadata-table .label {
            font-weight: bold;
            width: 110px;
            color: #555;
        }
        .metadata-table .value {
            font-weight: bold;
        }
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-banner {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 10px;
            display: block;
        }
        .status-fully {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .status-partial {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
        }
        .status-pending {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .section-header {
            background-color: #6c9bd2;
            color: white;
            padding: 5px 8px;
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 0px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .data-table {
            border: 1px solid #000;
            margin-bottom: 8px;
        }
        .data-table th, .data-table td {
            border: 1px solid #777;
            padding: 5px 3px;
            text-align: center;
            font-size: 11px;
        }
        .data-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #111;
            font-size: 11px;
        }
        .data-table td.text-left {
            text-align: left;
            padding-left: 6px;
            font-size: 11px;
        }
        .footer-row {
            font-weight: bold;
            background-color: #d9e9ff !important;
        }
        .footer-row td {
            border-top: 2px solid #333 !important;
            font-size: 11px;
        }
        
        /* Cell Status Styles */
        .cell-full {
            color: #111;
            font-weight: bold;
            font-size: 11px;
        }
        .cell-zero {
            background-color: #f8d7da;
            color: #721c24;
            font-weight: bold;
            padding: 3px 2px;
            border-radius: 2px;
            font-size: 10px;
        }
        .cell-partial {
            line-height: 1.15;
        }
        .txt-inward {
            color: #111;
            font-weight: bold;
            font-size: 11px;
        }
        .txt-pend {
            color: #c82333;
            font-weight: bold;
            font-size: 10px;
        }
        .badge-pend {
            background-color: #f8d7da;
            color: #721c24;
            font-weight: bold;
            font-size: 9.5px;
            padding: 1px 3px;
            border-radius: 2px;
            display: inline-block;
        }
        .badge-reason {
            background-color: #ffeeba;
            color: #856404;
            border: 1px solid #ffdf7e;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 8.5px;
            margin-right: 4px;
            display: inline-block;
        }
    </style>
</head>
<body>

    <table class="metadata-table">
        <tr>
            <td class="label" style="width: 70px;">Printed On:</td>
            <td class="value" style="width: 250px;">{{ now()->format('d/m/Y H:i') }}</td>
            <td style="text-align: right; color: #777;">Tracking Report &bull; Inward vs Pending</td>
        </tr>
    </table>

    <div class="title">Job Card Tracking Report</div>

    <table class="metadata-table">
        <tr>
            <td class="label">Job Card No #</td>
            <td class="value">: {{ $jobCard->job_card_no }}</td>
            <td class="label" style="text-align: right;">Plant (Unit)</td>
            <td class="value">: {{ $jobCard->serviceProvider->name ?? ($jobCard->unit_name ?? 'Nachias Fashion Private Limited') }}</td>
        </tr>
        <tr>
            <td class="label">Job Card Date</td>
            <td class="value">: {{ $jobCard->job_card_date ? date('d/m/Y', strtotime($jobCard->job_card_date)) : '-' }}</td>
            <td class="label" style="text-align: right;">Store Location(s)</td>
            <td class="value">: {{ !empty($uniqueLocations) ? implode(', ', $uniqueLocations) : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Warehouse (To)</td>
            <td class="value">: {{ !empty($uniqueWarehouses) ? implode(', ', $uniqueWarehouses) : 'Head Office' }}</td>
            <td class="label" style="text-align: right;">Receipts Count</td>
            <td class="value">: {{ count($receipts) }} Receipts @if(!empty($uniqueDates)) (Last: {{ end($uniqueDates) }}) @endif</td>
        </tr>
    </table>

    @php
        $plannedGrandTotal = (float)($jobCard->grand_total_qty ?? 0);
        $totalInwarded = (float)($totalInwardedQty ?? 0);
        $totalPending = max(0, $plannedGrandTotal - $totalInwarded);

        $statusClass = 'status-fully';
        $statusText = 'FULLY INWARDED';
        if ($totalPending > 0 && $totalInwarded > 0) {
            $statusClass = 'status-partial';
            $statusText = 'PARTIALLY RECEIVED';
        } elseif ($totalPending > 0 && $totalInwarded == 0) {
            $statusClass = 'status-pending';
            $statusText = 'PENDING INWARD';
        }
    @endphp

    <div class="status-banner {{ $statusClass }}">
        <span style="font-size: 12px;">STATUS: {{ $statusText }}</span>
        &nbsp;&bull;&nbsp;
        Planned Qty: <strong>{{ number_format($plannedGrandTotal, 0) }} pcs</strong>
        &nbsp;|&nbsp;
        Inwarded Qty: <strong style="color: #155724;">{{ number_format($totalInwarded, 0) }} pcs</strong>
        &nbsp;|&nbsp;
        Pending Qty: <strong style="color: {{ $totalPending > 0 ? '#721c24' : '#155724' }};">{{ number_format($totalPending, 0) }} pcs</strong>
        @if($pendingReasons && $pendingReasons->count() > 0)
            <div style="margin-top: 4px; font-weight: normal;">
                <strong>Pending Reasons:</strong>
                @foreach($pendingReasons as $pr)
                    <span class="badge-reason">{{ $pr->reason }} - {{ (int)$pr->qty }} pcs @if($pr->remarks) ({{ $pr->remarks }}) @endif</span>
                @endforeach
            </div>
        @endif
    </div>

    <div class="section-header">
        {{ ($jobCard->item && $jobCard->item->name) ? $jobCard->item->name : ($jobCard->brand->brand_name ?? 'JOB CARD DETAILS') }}
    </div>

    @php
        $isCanvas = false;
        if ($jobCard->brand && stripos($jobCard->brand->brand_name ?? '', 'CANVAS') !== false) {
            $isCanvas = true;
        }

        $sizes = [];
        if ($jobCard->sizeRatio && $jobCard->sizeRatio->size) {
            $sizes = array_values(array_filter(array_map('trim', explode(',', $jobCard->sizeRatio->size))));
        } else {
            $sizes = $jobCard->fabricDetails->pluck('quantities')->flatten()->pluck('size')->unique()->values()->toArray();
        }

        if (!empty($sizes)) {
            usort($sizes, function($a, $b) {
                if (is_numeric($a) && is_numeric($b)) {
                    return (float)$a <=> (float)$b;
                }
                return strnatcasecmp($a, $b);
            });
        }

        $fullSleeveRows = [];
        $halfSleeveRows = [];

        $receiptStoreName = strtolower($jobCard->receiptStore->store_type_name ?? '');
        $isFabricStore = str_contains($receiptStoreName, 'fabric');
        $isAccessoriesStore = str_contains($receiptStoreName, 'accessori');
        if ($isCanvas || $isAccessoriesStore) {
            $storeUom = 'NOS';
        } elseif ($isFabricStore) {
            $storeUom = 'MTR';
        } else {
            $storeUom = 'PCS';
        }

        foreach($jobCard->fabricDetails as $detail) {
            $trimmedArt = trim($detail->art_no ?? '');
            if (!$isCanvas && ($artCategoryMap[$trimmedArt] ?? 1) != 1) {
                // skip non-fabric if mapped
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

            $uom = $storeUom;
            $styleObj = null;
            if ($detail->art_no) {
                $stockEntryItem = \App\Models\StockEntryItem::with('style')->where('art_no', $detail->art_no)->whereNotNull('style_id')->first();
                $styleObj = $stockEntryItem?->style; 
            }
            if (!$styleObj) {
                $styleObj = $matchingPOItem?->style ?? $allPOItems?->whereNotNull('style_id')->first()?->style;
            }

            $styleCode = $styleObj?->code ?? '';
            $styleName = $styleObj?->style_name ?? ($styleCode ?: '');

            $brandCode = $jobCard->brand->code ?? '';
            $brandName = $jobCard->brand->brand_name ?? '';
            $artNo = $detail->art_no;
            $displayStyle = $styleCode ?: $artNo;

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
                $itemNoFs = trim($brandCode . '-' . $displayStyle . '-FS', '-');
                $rowKeyFs = $itemNoFs . '|' . $artNo;
                $fsSizes = $getSizesArray('fs');
                if (isset($fullSleeveRows[$rowKeyFs])) {
                    foreach ($sizes as $s) {
                        $fullSleeveRows[$rowKeyFs]['sizes'][$s] = ($fullSleeveRows[$rowKeyFs]['sizes'][$s] ?? 0) + ($fsSizes[$s] ?? 0);
                    }
                } else {
                    $fullSleeveRows[$rowKeyFs] = [
                        'item_no' => $itemNoFs,
                        'description' => trim($brandName . ' ' . ($styleName ?: $styleCode) . ' FS'),
                        'uom' => $uom,
                        'art' => $artNo,
                        'fg_art_no' => $detail->fg_art_no,
                        'sleeve' => 'FS',
                        'sizes' => $fsSizes
                    ];
                }
            }

            if ($hasValue('hs')) {
                $itemNoHs = trim($brandCode . '-' . $displayStyle . '-HS', '-');
                $rowKeyHs = $itemNoHs . '|' . $artNo;
                $hsSizes = $getSizesArray('hs');
                if (isset($halfSleeveRows[$rowKeyHs])) {
                    foreach ($sizes as $s) {
                        $halfSleeveRows[$rowKeyHs]['sizes'][$s] = ($halfSleeveRows[$rowKeyHs]['sizes'][$s] ?? 0) + ($hsSizes[$s] ?? 0);
                    }
                } else {
                    $halfSleeveRows[$rowKeyHs] = [
                        'item_no' => $itemNoHs,
                        'description' => trim($brandName . ' ' . ($styleName ?: $styleCode) . ' HS'),
                        'uom' => $uom,
                        'art' => $artNo,
                        'fg_art_no' => $detail->fg_art_no,
                        'sleeve' => 'HS',
                        'sizes' => $hsSizes
                    ];
                }
            }
        }

        $allRows = array_merge(array_values($fullSleeveRows), array_values($halfSleeveRows));

        // Fallback if empty
        if (empty($allRows)) {
            $fallbackMap = [];
            foreach($jobCard->fabricDetails as $detail) {
                $itemNo = $detail->item_name ?: ($jobCard->item->name ?? $jobCard->job_card_no);
                $artKey = $itemNo . '|' . ($detail->art_no ?? '');
                $sizesArr = [];
                foreach($sizes as $s) {
                    $q = $detail->quantities->where('size', $s)->first();
                    $sizesArr[$s] = $q ? ($q->total_qty ?? 0) : 0;
                }
                if (isset($fallbackMap[$artKey])) {
                    foreach($sizes as $s) {
                        $fallbackMap[$artKey]['sizes'][$s] = ($fallbackMap[$artKey]['sizes'][$s] ?? 0) + ($sizesArr[$s] ?? 0);
                    }
                } else {
                    $fallbackMap[$artKey] = [
                        'item_no' => $itemNo,
                        'description' => $detail->item_name ?: $detail->art_no,
                        'uom' => $storeUom,
                        'art' => $detail->art_no,
                        'fg_art_no' => $detail->fg_art_no,
                        'sleeve' => 'FS',
                        'sizes' => $sizesArr
                    ];
                }
            }
            $allRows = array_values($fallbackMap);
        }

        // Build canonical map between fabric art_no and fg_art_no
        $canonicalMap = [];
        foreach ($jobCard->fabricDetails as $fd) {
            $canonical = strtolower(trim((string)$fd->art_no));
            $fabClean = strtolower(trim((string)$fd->art_no));
            $fgClean = strtolower(trim((string)$fd->fg_art_no));

            if ($fabClean !== '') $canonicalMap[$fabClean] = $canonical;
            if ($fgClean !== '') $canonicalMap[$fgClean] = $canonical;

            $fabNorm = preg_replace('/[^a-z0-9]/', '', $fabClean);
            $fgNorm = preg_replace('/[^a-z0-9]/', '', $fgClean);
            if ($fabNorm !== '') $canonicalMap[$fabNorm] = $canonical;
            if ($fgNorm !== '') $canonicalMap[$fgNorm] = $canonical;
        }

        $resolveArt = function($str) use ($canonicalMap) {
            $clean = strtolower(trim((string)$str));
            $norm = preg_replace('/[^a-z0-9]/', '', $clean);
            return $canonicalMap[$clean] ?? ($canonicalMap[$norm] ?? $clean);
        };

        $grandPlanTotal = 0;
        $grandInwardTotal = 0;
        $grandPendTotal = 0;
        $sizePlanTotals = array_fill_keys($sizes, 0);
        $sizeInwardTotals = array_fill_keys($sizes, 0);
        $sizePendTotals = array_fill_keys($sizes, 0);

        $remainingPool = $inwardMap;
    @endphp

    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 18%;">Item</th>
                <th rowspan="2" style="width: 15%;">Art</th>
                <th rowspan="2" style="width: 16%;">Store Location</th>
                <th colspan="{{ count($sizes) }}">Size (Inward / Pending)</th>
                <th rowspan="2" style="width: 7%; background-color: #e2e8f0;">Plan Qty</th>
                <th rowspan="2" style="width: 7%; background-color: #d4edda; color: #155724;">Inwarded</th>
                <th rowspan="2" style="width: 7%; background-color: #f8d7da; color: #721c24;">Pending</th>
            </tr>
            <tr>
                @foreach($sizes as $size)
                    <th style="font-size: 11px; min-width: 28px;">{{ $size }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($allRows as $row)
                @php
                    $rawArtClean = strtolower(trim((string)$row['art']));
                    $artClean = $resolveArt($row['art']);
                    $sleeve = (($row['sleeve'] ?? 'FS') === 'HS' || ($row['sleeve'] ?? '') === 'H/S') ? 'HS' : 'FS';
                    $cleanItemNo = preg_replace('/-F\/S/i', '-FS', $row['item_no']);
                    $cleanItemNo = preg_replace('/-H\/S/i', '-HS', $cleanItemNo);

                    $locKey = $artClean . '|' . $sleeve;
                    $rowLocations = $artLocationMap[$locKey] ?? ($artLocationMap[$rawArtClean . '|' . $sleeve] ?? ($artLocationMap[$artClean] ?? ($artLocationMap[$rawArtClean] ?? [])));

                    $rowPlanTotal = 0;
                    $rowInwardTotal = 0;
                    $rowPendTotal = 0;
                    $rowCells = [];

                    foreach($sizes as $sizeKey) {
                        $pQty = (float)($row['sizes'][$sizeKey] ?? 0);
                        $szClean = strtolower(trim((string)$sizeKey));
                        $keyWithSleeve = $artClean . '|' . $sleeve . '|' . $szClean;
                        $keySlash = $artClean . '|' . ($sleeve === 'FS' ? 'F/S' : 'H/S') . '|' . $szClean;
                        $rawKeyWithSleeve = $rawArtClean . '|' . $sleeve . '|' . $szClean;
                        $rawKeySlash = $rawArtClean . '|' . ($sleeve === 'FS' ? 'F/S' : 'H/S') . '|' . $szClean;
                        $keyFallback = $artClean . '|' . $szClean;
                        $rawKeyFallback = $rawArtClean . '|' . $szClean;

                        $availKey = null;
                        foreach ([$keyWithSleeve, $keySlash, $rawKeyWithSleeve, $rawKeySlash, $keyFallback, $rawKeyFallback] as $candidateKey) {
                            if (isset($remainingPool[$candidateKey]) && $remainingPool[$candidateKey] > 0) {
                                $availKey = $candidateKey;
                                break;
                            }
                        }

                        $availQty = $availKey ? (float)$remainingPool[$availKey] : 0;
                        $allocInw = min($pQty, $availQty);
                        if ($allocInw > 0) {
                            foreach ([$keyWithSleeve, $keySlash, $rawKeyWithSleeve, $rawKeySlash, $keyFallback, $rawKeyFallback] as $candidateKey) {
                                if (isset($remainingPool[$candidateKey])) {
                                    $remainingPool[$candidateKey] = max(0, $remainingPool[$candidateKey] - $allocInw);
                                }
                            }
                        }

                        $pndQty = max(0, $pQty - $allocInw);

                        $rowPlanTotal += $pQty;
                        $rowInwardTotal += $allocInw;
                        $rowPendTotal += $pndQty;

                        $sizePlanTotals[$sizeKey] += $pQty;
                        $sizeInwardTotals[$sizeKey] += $allocInw;
                        $sizePendTotals[$sizeKey] += $pndQty;

                        $rowCells[$sizeKey] = [
                            'pQty' => $pQty,
                            'inwQty' => $allocInw,
                            'pndQty' => $pndQty,
                        ];
                    }

                    $grandPlanTotal += $rowPlanTotal;
                    $grandInwardTotal += $rowInwardTotal;
                    $grandPendTotal += $rowPendTotal;
                @endphp
                <tr>
                    <td class="text-left" style="font-weight: bold;">{{ $cleanItemNo }}</td>
                    <td style="font-weight: bold;">{{ $row['art'] }}</td>
                    <td style="font-weight: bold; font-size: 10px; color: #1e3a8a;">
                        {{ !empty($rowLocations) ? implode(', ', $rowLocations) : '-' }}
                    </td>

                    @foreach($sizes as $sizeKey)
                        @php
                            $cell = $rowCells[$sizeKey];
                            $pQty = $cell['pQty'];
                            $inwQty = $cell['inwQty'];
                            $pndQty = $cell['pndQty'];
                        @endphp
                        <td>
                            @if($pQty == 0)
                                <span style="color: #bbb;">-</span>
                            @elseif($pndQty == 0 && $inwQty >= $pQty)
                                <div class="cell-full">{{ (int)$inwQty }}</div>
                            @elseif($inwQty > 0 && $pndQty > 0)
                                <div class="cell-partial">
                                    <span class="txt-inward">{{ (int)$inwQty }}</span><br>
                                    <span class="badge-pend">P: {{ (int)$pndQty }}</span>
                                </div>
                            @else
                                <div class="cell-zero">P: {{ (int)$pQty }}</div>
                            @endif
                        </td>
                    @endforeach

                    <td style="background-color: #f8fafc; font-weight: bold; font-size: 11.5px;">{{ (int)$rowPlanTotal }}</td>
                    <td style="background-color: #f0fdf4; font-weight: bold; font-size: 11.5px; color: #155724;">{{ (int)$rowInwardTotal }}</td>
                    <td style="background-color: #fef2f2; font-weight: bold; font-size: 11.5px; color: {{ $rowPendTotal > 0 ? '#b91c1c' : '#6b7280' }};">
                        {{ (int)$rowPendTotal }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="footer-row">
                <td colspan="3" style="text-align: center; font-weight: bold; font-size: 11.5px;">TOTAL</td>
                @foreach($sizes as $sizeKey)
                    @php
                        $sInw = $sizeInwardTotals[$sizeKey];
                        $sPnd = $sizePendTotals[$sizeKey];
                    @endphp
                    <td style="font-size: 10.5px; line-height: 1.15;">
                        @if($sInw > 0)
                            <span class="txt-inward">{{ (int)$sInw }}</span>
                        @endif
                        @if($sPnd > 0)
                            @if($sInw > 0)<br>@endif
                            <span class="badge-pend">P: {{ (int)$sPnd }}</span>
                        @elseif($sInw == 0)
                            <span style="color: #bbb;">0</span>
                        @endif
                    </td>
                @endforeach
                <td style="font-weight: bold; font-size: 12px;">{{ (int)$grandPlanTotal }}</td>
                <td style="font-weight: bold; font-size: 12px; color: #155724; background-color: #d4edda !important;">{{ (int)$grandInwardTotal }}</td>
                <td style="font-weight: bold; font-size: 12px; color: {{ $grandPendTotal > 0 ? '#721c24' : '#155724' }}; background-color: {{ $grandPendTotal > 0 ? '#f8d7da' : '#d4edda' }} !important;">
                    {{ (int)$grandPendTotal }}
                </td>
            </tr>
        </tfoot>
    </table>

</body>
</html>

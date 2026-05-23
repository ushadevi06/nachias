<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Job Card - {{ $jobCard->job_card_no }}</title>
    <style>
        @page {
            margin: 10px;
            size: landscape;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 7pt;
            margin: 0;
            padding: 0;
            color: #000;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .table-bordered th, .table-bordered td {
            border: 0.2pt solid #eee;
            padding: 4px 6px;
            vertical-align: middle;
        }
        .fw-bold {
            font-weight: bold;
            padding: 5px 5px;
        }
        .text-center {
            text-align: center;
        }
        .text-end {
            text-align: right;
        }
        .bg-light {
            background-color: #f8f9fa;
        }
        .bg-blue {
            background-color: #2f6fae;
            color: #ffffff;
        }
        .header-cell {
            font-weight: bold;
            font-size: 7pt;
        }
        .style-title {
            font-size: 9pt;
            font-weight: bold;
        }
        .urgent-text {
            font-size: 8pt;
            font-weight: bold;
        }
        .small-text {
            font-size: 5pt;
        }
        .art-img {
            max-width: 45pt;
            max-height: 35pt;
            display: block;
            margin: 2px auto;
        }
        .signature-section td {
            height: 12pt;
            font-size: 6pt;
            padding: 2px 4px !important;
        }
        .page-break-avoid {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    @php
        $allSizes = [];
        if ($jobCard->sizeRatio && $jobCard->sizeRatio->size) {
            $allSizes = array_values(array_filter(array_map('trim', explode(',', $jobCard->sizeRatio->size))));
        }

        if (empty($allSizes)) {
            $allSizes = $jobCard->cuttingSizeRatios->pluck('size')->unique()->toArray();
            sort($allSizes, SORT_NUMERIC);
        }

        $activeFs = [];
        $activeHs = [];
        foreach ($jobCard->cuttingSizeRatios as $ratio) {
            if ($ratio->qty_fs > 0)
                $activeFs[] = $ratio->size;
            if ($ratio->qty_hs > 0)
                $activeHs[] = $ratio->size;
        }
        $activeFs = array_values(array_unique($activeFs));
        sort($activeFs, SORT_NUMERIC);
        $activeHs = array_values(array_unique($activeHs));
        sort($activeHs, SORT_NUMERIC);

        if (empty($activeFs) && empty($activeHs)) {
            $activeFs = $allSizes;
        }

        $sleeveInstances = $jobCard->sleeve_instances;
        if (is_string($sleeveInstances)) {
            $sleeveInstances = json_decode($sleeveInstances, true);
        }

        $fsRows = [];
        $hsRows = [];

        if ($sleeveInstances && is_array($sleeveInstances)) {
            if (isset($sleeveInstances['instances']) && is_array($sleeveInstances['instances'])) {
                $valArr = $sleeveInstances['values'] ?? [];
                foreach ($sleeveInstances['instances'] as $inst) {
                    $id = $inst['id'] ?? null;
                    $type = $inst['type'] ?? '';

                    if ($id === null)
                        continue;

                    $vals = null;
                    foreach ($valArr as $vk => $vv) {
                        if (abs((float) $vk - (float) $id) < 0.01) {
                            $vals = $vv;
                            break;
                        }
                    }

                    if ($vals) {
                        $hasValue = false;
                        foreach ($vals as $v)
                            if ($v != '' && $v != '-')
                                $hasValue = true;
                        if ($hasValue) {
                            if ($type === 'fs')
                                $fsRows[] = ['id' => $id, 'values' => $vals];
                            elseif ($type === 'hs')
                                $hsRows[] = ['id' => $id, 'values' => $vals];
                        }
                    }
                }
            } elseif (isset($sleeveInstances['configs']) && is_array($sleeveInstances['configs'])) {
                $valArr = $sleeveInstances['values'] ?? [];
                foreach ($sleeveInstances['configs'] as $id => $config) {
                    $type = $config['type'] ?? '';
                    $vals = $valArr[$id] ?? [];

                    $hasValue = false;
                    foreach ($vals as $v)
                        if ($v != '' && $v != '-')
                            $hasValue = true;
                    if ($hasValue) {
                        if ($type === 'fs')
                            $fsRows[] = ['id' => $id, 'values' => $vals];
                        elseif ($type === 'hs')
                            $hsRows[] = ['id' => $id, 'values' => $vals];
                    }
                }
            } elseif (isset($sleeveInstances['fs']) || isset($sleeveInstances['hs'])) {
                $rawFs = $sleeveInstances['fs'] ?? [];
                $rawHs = $sleeveInstances['hs'] ?? [];
                foreach ($rawFs as $id => $vals) {
                    $hasValue = false;
                    foreach ($vals as $v)
                        if ($v != '' && $v != '-')
                            $hasValue = true;
                    if ($hasValue)
                        $fsRows[] = ['id' => $id, 'values' => $vals];
                }
                foreach ($rawHs as $id => $vals) {
                    $hasValue = false;
                    foreach ($vals as $v)
                        if ($v != '' && $v != '-')
                            $hasValue = true;
                    if ($hasValue)
                        $hsRows[] = ['id' => $id, 'values' => $vals];
                }
            }
        }

        $fabricDetails = $jobCard->fabricDetails->filter(function ($detail) use ($artCategoryMap) {
            $trimmedArt = trim($detail->art_no);
            return ($artCategoryMap[$trimmedArt] ?? 1) == 1;
        });

        $fabricImageSrcMap = [];
        foreach ($fabricDetails as $detail) {
            $imageFile = trim((string) ($detail->grn_image ?? ''));
            $imageSrc = '';

            if ($imageFile !== '') {
                $imagePath = public_path('uploads/grn_items/' . $imageFile);
                if (file_exists($imagePath)) {
                    $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
                    $mimeType = match ($extension) {
                        'jpg', 'jpeg' => 'image/jpeg',
                        'png' => 'image/png',
                        default => null,
                    };

                    if ($mimeType) {
                        $imageSrc = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($imagePath));
                    }
                }
            }

            $fabricImageSrcMap[$detail->id] = $imageSrc;
        }
        $allLayMarks = [];
        $firstFabric = $fabricDetails->first();
        if ($firstFabric) {
            $allLayMarks = $firstFabric->layMarks;
        }

        $fabricCount = $fabricDetails->count();
        $swatchWidth = $fabricCount > 0 ? (100 / $fabricCount) : 25;
        if ($swatchWidth > 33.3)
            $swatchWidth = 33.3;
        if ($swatchWidth < 14.2)
            $swatchWidth = 14.2;

        $logoPath = public_path('assets/images/jc_logo.png');
        $logoSrc = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';

        $favPath = public_path('assets/images/fav.jpeg');
        $favSrc = file_exists($favPath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($favPath)) : '';

        $issueDate = $jobCard->job_card_date ? \Carbon\Carbon::parse($jobCard->job_card_date) : null;
        $deliveryDate = $jobCard->delivery_date ? \Carbon\Carbon::parse($jobCard->delivery_date) : null;

        $pDates = ['d1' => '', 'd2' => '', 'd3' => '', 'd4' => '', 'd5' => '', 'd6' => ''];

        if ($issueDate && $deliveryDate) {
            $totalDays = max(1, $issueDate->diffInDays($deliveryDate));

            $pDates['d1'] = $issueDate->format('d-m-Y');
            $pDates['d2'] = $issueDate->copy()->addDays(round($totalDays * 0.28))->format('d-m-Y');
            $pDates['d3'] = $issueDate->copy()->addDays(round($totalDays * 0.50))->format('d-m-Y');
            $pDates['d4'] = $issueDate->copy()->addDays(round($totalDays * 0.71))->format('d-m-Y');
            $pDates['d5'] = $issueDate->copy()->addDays(round($totalDays * 0.85))->format('d-m-Y');
            $pDates['d6'] = $deliveryDate->format('d-m-Y');
        }
    @endphp

    <table class="table table-bordered">
        <tr>
            <td rowspan="2" style="width: 15%;" class="text-center">
                @if($logoSrc)
                    <img src="{{ $logoSrc }}" style="max-width: 100%; max-height: 30pt;">
                @endif
            </td>
            <td class="text-center fw-bold bg-light" style="width: 10%;">SEASON CODE</td>
            <td colspan="4" class="text-center fw-bold" style="font-size: 10pt;">JOB CARD</td>
            <td colspan="2" class="text-center fw-bold bg-light" style="width: 25%;">JOB CARD TYPE</td>
            <td class="text-center bg-blue urgent-text" style="width: 10%;">{{ strtoupper($jobCard->job_card_type ?? 'Regular') }}</td>
        </tr>
        <tr>
            <td class="text-center fw-bold" style="font-size: 8pt;">{{ $jobCard->season->name ?? '-' }}</td>
            <td colspan="4" class="text-center style-title">
                {{ strtoupper($jobCard->brand->brand_name ?? '') }} 
            </td>
            <td class="text-center fw-bold" style="width: 10%;">LOGO</td>
            <td class="text-center fw-bold" style="width: 15%;">
                POCKET @if($favSrc) <img src="{{ $favSrc }}" style="height: 10pt; vertical-align: middle; margin-top: 6px;"> @endif CENTER
            </td>
            <td class="text-center small-text">MARK CHECKER'S<br>SIGN</td>
        </tr>
    </table>

    <table class="table table-bordered" style="margin-top: -0.5pt;">
        <tr>
            <td class="fw-bold" style="width: 8%; font-size: 7px;">CUTTING NO</td>
            <td style="width: 12%; font-size: 7px;">{{ $jobCard->job_card_no }}</td>
            <td class="bg-light fw-bold" style="width: 5%; font-size: 7px;">FIT</td>
            <td class="text-center fw-bold" style="width: 15%; font-size: 7px;">{{ strtoupper($jobCard->fit->fit_name ?? 'CROSS') }}</td>
            <td class="bg-light fw-bold" style="width: 6%; font-size: 7px;">CUFF</td>
            <td class="text-center fw-bold" style="width: 10%; font-size: 7px;">{{ strtoupper($jobCard->cuffType->cuff_type_name ?? 'CROSS') }}</td>
            <td class="fw-bold" style="width: 12%; font-size: 7px;">CUTTING MASTER</td>
            <td style="width: 10%; font-size: 7px;">{{ $jobCard->cuttingMaster->name ?? '' }}</td>
        </tr>
        <tr>
            <td class="fw-bold" style="font-size: 7px;">ISSUE DATE</td>
            <td style="font-size: 7px;">{{ $jobCard->job_card_date ? date('d-m-Y', strtotime($jobCard->job_card_date)) : '' }}</td>
            <td class="bg-light fw-bold" style="font-size: 7px;">N.PATTI</td>
            <td class="text-center fw-bold" style="font-size: 7px;">{{ strtoupper($jobCard->pattiType->patti_type_name ?? 'CROSS') }}</td>
            <td class="bg-light fw-bold" style="font-size: 7px;">POCKET</td>
            <td class="text-center fw-bold" style="font-size: 7px;">{{ strtoupper($jobCard->pocketType->pocket_type_name ?? 'CROSS') }}</td>
            <td class="fw-bold" style="font-size: 7px;">CUTTING DATE</td>
            <td class="fw-bold text-end" style="font-size: 7px;">H.O / D.C /NO</td>
        </tr>
        <tr>
            <td class="fw-bold" style="font-size: 7px;">DELIVERY DATE</td>
            <td style="font-size: 7px;">{{ $jobCard->delivery_date ? date('d-m-Y', strtotime($jobCard->delivery_date)) : '' }}</td>
            <td class="bg-light fw-bold" style="font-size: 7px;">COLLAR</td>
            <td class="text-center fw-bold" style="font-size: 7px;">{{ strtoupper($jobCard->collarType->collar_type_name ?? 'CROSS') }}</td>
            <td class="bg-light fw-bold" style="font-size: 7px;">BOT.CUT</td>
            <td class="text-center fw-bold" style="font-size: 7px;">{{ strtoupper($jobCard->bottomCut->bottom_cut_name ?? 'CROSS') }}</td>
            <td class="fw-bold" style="font-size: 7px;">CUTTING ISSUE UNIT</td>
            <td style="font-size: 7px;">{{ $jobCard->cuttingIssueUnitMapping->name ?? $jobCard->cutting_issue_unit }}</td>
        </tr>
    </table>

    {{-- Matrix Header --}}
    <table class="table table-bordered" style="margin-top: 1pt;">
        <colgroup>
            <col style="width: 8%;">
            <col style="width: 12%;">
            <col style="width: 8%;">
            @foreach($allSizes as $s)
                <col style="width: {{ 32 / (count($allSizes) ?: 1) }}%;">
            @endforeach
            <col style="width: 10%;">
            <col style="width: 8%;">
            <col style="width: 10%;">
            <col style="width: 12%;">
        </colgroup>
        <tr>
            <td class="fw-bold" style="width: 8%; font-size: 7px;">WITHIN DAYS</td>
            <td style="width: 12%; font-size: 7px;">{{ $jobCard->no_of_days ?? '' }}</td>
            <td colspan="{{ count($allSizes) + 1 }}" class="text-center fw-bold bg-light" style="width: 40%;">CUTTING SIZE RATIO
            </td>
            <td colspan="3" class="text-center fw-bold bg-light" style="width: 28%;">CUTTING MARK</td>
            <td class="fw-bold text-end" style="width: 12%;">H.O / D.C /DATE</td>
        </tr>
        @php 
            $totalRatioRows = max(4, (count($fsRows) ?: 1) + (count($hsRows) ?: 1)) + 1;
            $sidebarRowspan = $totalRatioRows + 2;
            $currentRow = 0;
        @endphp
        <tr>
            <td class="fw-bold"></td>
            <td class="text-center">{{ $jobCard->width ?: '-' }}</td>
            <td class="text-center fw-bold bg-light" style="width: 8%;">SIZE</td>
            @foreach($allSizes as $s)
                <td class="text-center fw-bold" style="width: {{ 32 / (count($allSizes) ?: 1) }}%;">{{ $s }}</td>
            @endforeach
            <td class="text-center fw-bold bg-light" style="width: 10%;">SIZE</td>
            <td class="text-center fw-bold bg-light" style="width: 8%;">S.TYPE</td>
            <td class="text-center fw-bold bg-light" style="width: 10%;">MARK</td>
            @php $currentRow++; @endphp
            <td class="text-center">{{ $currentRow === 3 ? 'UNIT D.C NO' : '' }}</td>
        </tr>

        @if(count($fsRows) > 0)
            @foreach($fsRows as $index => $row)
            <tr>
                @if($index === 0)
                    <td class="fw-bold" rowspan="{{ count($fsRows) }}"></td>
                    <td rowspan="{{ count($fsRows) }}" class="text-center">&nbsp;</td>
                @endif
                <td class="text-center fw-bold bg-light">QTY - F/S</td>
                @foreach($allSizes as $s)
                    <td class="text-center">
                        {{ (isset($row['values'][$s]) && $row['values'][$s] != '' && $row['values'][$s] != '-') ? (int) $row['values'][$s] : '-' }}
                    </td>
                @endforeach
                @if($index === 0)
                    <td rowspan="{{ $totalRatioRows }}" colspan="3" style="padding: 0; vertical-align:top;">
                        <table class="table table-bordered mb-0" style="border: none;">
                            @for($i = 0; $i < count($allLayMarks); $i++)
                                @php $lm = $allLayMarks[$i] ?? null; @endphp
                                @if($lm)
                                    <tr>
                                        <td class="text-center" style="width: 35%; font-size: 7px; border: none; border-bottom: 0.5px solid #ddd; border-right: 0.5px solid #ddd;">
                                            {{ is_array($lm->sizes) ? implode(',', $lm->sizes) : $lm->sizes }}
                                        </td>
                                        <td class="text-center" style="width: 30%; font-size: 7px; border: none; border-bottom: 0.5px solid #ddd; border-right: 0.5px solid #ddd;">
                                            {{ $lm->sleeve_type ?? $lm->sleeve ?? 'F/S' }}
                                        </td>
                                        <td class="text-center" style="width: 35%; font-size: 7px; border: none; border-bottom: 0.5px solid #ddd;">
                                            {{ $lm->lay_mark_meter }}
                                        </td>
                                    </tr>
                                @endif
                            @endfor
                        </table>
                    </td>
                @endif
                @php $currentRow++; @endphp
                <td class="text-center fw-bold" style="font-size: 7px;">{{ $currentRow === 3 ? 'UNIT D.C NO' : '' }}</td>
            </tr>
            @endforeach
        @else
            <tr>
                <td class="fw-bold"></td>
                <td class="text-center">&nbsp;</td>
                <td class="text-center fw-bold bg-light">QTY - F/S</td>
                @foreach($allSizes as $s)
                    @php $ratio = $jobCard->cuttingSizeRatios->where('size', $s)->first(); @endphp
                    <td class="text-center">{{ $ratio ? (int) $ratio->qty_fs : '-' }}</td>
                @endforeach
                <td rowspan="{{ $totalRatioRows }}" colspan="3" style="padding: 0; vertical-align:top;">
                    <table class="table table-bordered mb-0 w-100" style="border:none;height:100%;">
                        @for($i = 0; $i < count($allLayMarks); $i++)
                            @php $lm = $allLayMarks[$i] ?? null; @endphp
                            @if($lm)
                                <tr style="height:18px;">
                                    <td class="text-center" style="width: 35%; font-size: 7px; border: none; border-bottom: 0.5px solid #ddd; border-right: 0.5px solid #ddd;">
                                        {{ is_array($lm->sizes) ? implode(',', $lm->sizes) : $lm->sizes }}
                                    </td>
                                    <td class="text-center" style="width: 30%; font-size: 7px; border: none; border-bottom: 0.5px solid #ddd; border-right: 0.5px solid #ddd;">
                                        {{ $lm->sleeve_type ?? $lm->sleeve ?? 'F/S' }}
                                    </td>
                                    <td class="text-center" style="width: 35%; font-size: 7px; border: none; border-bottom: 0.5px solid #ddd;">
                                        {{ $lm->lay_mark_meter }}
                                    </td>
                                </tr>
                            @endif
                        @endfor
                    </table>
                </td>
                @php $currentRow++; @endphp
                <td class="text-center fw-bold" style="font-size: 7px;">{{ $currentRow === 3 ? 'UNIT D.C NO' : '' }}</td>
            </tr>
        @endif

        @if(count($hsRows) > 0)
            @foreach($hsRows as $index => $row)
            <tr>
                @if($index === 0)
                    <td class="fw-bold" rowspan="{{ count($hsRows) }}"></td>
                    <td rowspan="{{ count($hsRows) }}" class="text-center">&nbsp;</td>
                @endif
                <td class="text-center fw-bold bg-light">QTY - H/S</td>
                @foreach($allSizes as $s)
                    <td class="text-center">
                        {{ (isset($row['values'][$s]) && $row['values'][$s] != '' && $row['values'][$s] != '-') ? (int) $row['values'][$s] : '-' }}
                    </td>
                @endforeach
                @php $currentRow++; @endphp
                <td class="text-center fw-bold" style="font-size: 7px;">{{ $currentRow === 3 ? 'UNIT D.C NO' : '' }}</td>
            </tr>
            @endforeach
        @else
            <tr>
                <td class="fw-bold"></td>
                <td class="text-center">&nbsp;</td>
                <td class="text-center fw-bold bg-light">QTY - H/S</td>
                @foreach($allSizes as $s)
                    @php $ratio = $jobCard->cuttingSizeRatios->where('size', $s)->first(); @endphp
                    <td class="text-center">{{ $ratio ? (int) $ratio->qty_hs : '-' }}</td>
                @endforeach
                @php $currentRow++; @endphp
                <td class="text-center fw-bold" style="font-size: 7px;">{{ $currentRow === 3 ? 'UNIT D.C NO' : '' }}</td>
            </tr>
        @endif

        @php 
            $rowsRendered = (count($fsRows) ?: 1) + (count($hsRows) ?: 1);
        @endphp
        @for($i = $rowsRendered; $i < 4; $i++)
            <tr style="height: 18pt;">
                <td class="fw-bold">&nbsp;</td>
                <td class="text-center">&nbsp;</td>
                <td class="text-center fw-bold bg-light">&nbsp;</td>
                @foreach($allSizes as $s)
                    <td>&nbsp;</td>
                @endforeach
                @php $currentRow++; @endphp
                <td class="text-center fw-bold" style="font-size: 7px;">{{ $currentRow === 3 ? 'UNIT D.C NO' : '' }}</td>
            </tr>
        @endfor

        <tr>
            <td class="fw-bold"></td>
            <td class="text-center">&nbsp;</td>
            <td colspan="{{ count($allSizes) + 1 }}"></td>
            @php $currentRow++; @endphp
            <td class="text-center fw-bold" style="font-size: 7px;">{{ $currentRow === 3 ? 'UNIT D.C NO' : '' }}</td>
        </tr>
    </table>

    {{-- Fabric Details (Horizontal Format) --}}
    <table class="table table-bordered" style="margin-top: 3pt;">
        {{--  <tr>
            <td class="bg-light fw-bold" style="width: 15%;">FABRIC TYPE</td> 
            <td colspan="{{ count($fabricDetails) }}" class="text-center fw-bold" style="color: #2f6fae; font-size: 8pt;"> 
                {{ $jobCard->fabricType->fabric_type ?? 'N/A' }}
            </td>
        </tr> --}}
        <tr>
            <td class="bg-light fw-bold" style="width: 15%;">IMAGE</td>
            @foreach($fabricDetails as $detail)
                @php $imageSrc = $fabricImageSrcMap[$detail->id] ?? ''; @endphp
                <td class="text-center">
                    @if($imageSrc)
                        <img src="{{ $imageSrc }}" alt="GRN Image" class="art-img">
                    @else
                        -
                    @endif
                </td>
            @endforeach
        </tr>
        <tr>
            <td class="bg-light fw-bold" style="width: 15%;">ART NO</td>
            @foreach($fabricDetails as $detail)
                <td class="text-center fw-bold">
                    {{ $detail->art_no }}
                    @if($detail->item_name)
                        <br><span style="font-size: 5pt; font-weight: normal;">{{ $detail->item_name }}</span>
                    @endif
                </td>
            @endforeach
        </tr>
        <tr>
            <td class="bg-light fw-bold">WIDTH</td>
            @foreach($fabricDetails as $detail)
                <td class="text-center">{{ $detail->width ?: '-' }}</td>
            @endforeach
        </tr>
        <tr>
            <td class="bg-light fw-bold">Issue Meter</td>
            @foreach($fabricDetails as $detail)
                <td class="text-center">{{ (int) $detail->mtr ?: '-' }}</td>
            @endforeach
        </tr>
        <tr>
            <td class="bg-light fw-bold">IN/OUT</td>
            @foreach($fabricDetails as $detail)
                <td class="text-center">{{ $detail->in_out ?: '-' }}</td>
            @endforeach
        </tr>
        <tr>
            <td class="bg-light fw-bold">N.PATTI</td>
            @foreach($fabricDetails as $detail)
                <td class="text-center">{{ $detail->n_patti ?: '-' }}</td>
            @endforeach
        </tr>
    </table>

    {{-- Quantity Summary --}}
    <table class="table table-bordered" style="margin-top: 3pt;">
        <thead>
            <tr class="bg-light text-center">
                <th rowspan="2" style="width: 10%;">ART NO</th>
                <th colspan="{{ count($allSizes) }}">F/S</th>
                <th colspan="{{ count($allSizes) }}">H/S</th>
                <th rowspan="2" style="width: 8%;">TOTAL</th>
            </tr>
            <tr class="bg-light text-center">
                @foreach($allSizes as $s) <th style="width: {{ 40 / count($allSizes) }}%;">{{ $s }}</th> @endforeach
                @foreach($allSizes as $s) <th style="width: {{ 40 / count($allSizes) }}%;">{{ $s }}</th> @endforeach
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($fabricDetails as $detail)
                @php 
                    $rowTotal = $detail->quantities->sum('total_qty');
    $grandTotal += $rowTotal;
                @endphp
                <tr class="text-center">
                    <td class="fw-bold">{{ $detail->art_no }}</td>
                    @foreach($allSizes as $s)
                        @php $q = $detail->quantities->where('size', $s)->first(); @endphp
                        <td>{{ $q ? (int) $q->qty_fs : '-' }}</td>
                    @endforeach
                    @foreach($allSizes as $s)
                        @php $q = $detail->quantities->where('size', $s)->first(); @endphp
                        <td>{{ $q ? (int) $q->qty_hs : '-' }}</td>
                    @endforeach
                    <td class="fw-bold">{{ (int) $rowTotal }}</td>
                </tr>
            @endforeach
            <tr class="bg-light text-center fw-bold">
                <td>TOTAL</td>
                @foreach($allSizes as $s)
                    <td>
                        @php
    $sumFs = 0;
    foreach ($fabricDetails as $detail) {
        if (($artCategoryMap[$detail->art_no] ?? 1) == 1) {
            $sumFs += $detail->quantities->where('size', $s)->sum('qty_fs');
        }
    }
                        @endphp
                        {{ $sumFs ?: '-' }}
                    </td>
                @endforeach
                @foreach($allSizes as $s)
                    <td>
                        @php
    $sumHs = 0;
    foreach ($fabricDetails as $detail) {
        if (($artCategoryMap[$detail->art_no] ?? 1) == 1) {
            $sumHs += $detail->quantities->where('size', $s)->sum('qty_hs');
        }
    }
                        @endphp
                        {{ $sumHs ?: '-' }}
                    </td>
                @endforeach
                <td>{{ (int) $grandTotal }}</td>
            </tr>
        </tbody>
    </table>


    {{-- Production Stages @if($jobCard->operations && $jobCard->operations->count() > 0)
    <table class="table table-bordered" style="margin-top: 10pt;">
        <thead>
            <tr class="bg-light text-center">
                <th colspan="5" class="fw-bold" style="font-size: 8pt;">PRODUCTION STAGES</th>
            </tr>
            <tr class="bg-light text-center fw-bold">
                <th style="width: 20%;">Stage</th>
                <th style="width: 25%;">Issue Unit (Plant)</th>
                <th style="width: 15%;">Issue Date</th>
                <th style="width: 15%;">Deadline Date</th>
                <th style="width: 25%;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jobCard->operations as $operation)
                <tr class="text-center">
                    <td>{{ $operation->stage->operation_stage_name ?? '-' }}</td>
                    <td>{{ $operation->serviceProvider->name ?? '-' }}</td>
                    <td>{{ $operation->assigned_date ? date('d-m-Y', strtotime($operation->assigned_date)) : '-' }}</td>
                    <td>{{ $operation->deadline_date ? date('d-m-Y', strtotime($operation->deadline_date)) : '-' }}</td>
                    <td>{{ $operation->remarks ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif --}}

    {{-- Redesigned Signatures Section --}}
    <table class="table table-bordered page-break-avoid" style="margin-top: 3pt; font-size: 6pt; table-layout: fixed;">
        <thead>
            <tr class="bg-light fw-bold text-center">
                <td colspan="7" style="vertical-align: middle;" class="text-start">AUTHORISED SIGNATURES</td>
                <td style="width: 10%; vertical-align: middle;">TOTAL MTRS</td>
                <td style="width: 8%; vertical-align: middle;">{{ (int) $grandTotal }}</td>
                <td style="width: 16%; vertical-align: middle;" class="text-start">REMARKS:</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="bg-light fw-bold" style="width: 10%;">SECTION</td>
                <td class="bg-light fw-bold" style="width: 10%;">INCHARGE SIGN</td>
                <td class="bg-light fw-bold" style="width: 8%;">PLANING DATE</td>
                <td class="bg-light fw-bold" style="width: 10%;">SECTION</td>
                <td class="bg-light fw-bold" style="width: 10%;">INCHARGE SIGN</td>
                <td class="bg-light fw-bold" style="width: 8%;">DATE</td>
                <td class="bg-light fw-bold" style="width: 10%;">SECTION</td>
                <td class="bg-light fw-bold" style="width: 10%;">INCHARGE SIGN</td>
                <td class="bg-light fw-bold" style="width: 8%;">DATE</td>
                <!-- Remarks Grid Cell -->
                <td rowspan="8" class="p-0" style="vertical-align: top; width: 16%;">
                    
                </td>
            </tr>
            <tr class="text-center">
                <td class="text-start">PURCHASE</td><td></td><td rowspan="2" class="fw-bold">{{ $pDates['d1'] }}</td>
                <td class="text-start">READY</td><td></td><td rowspan="2" class="fw-bold">{{ $pDates['d3'] }}</td>
                <td class="text-start">FINAL FINISH RECD</td><td rowspan="4"></td><td rowspan="4" class="fw-bold">{{ $pDates['d6'] }}</td>
            </tr>
            <tr class="text-center">
                <td class="text-start">FABRIC STORE</td><td></td>
                <td class="text-start">READY STORE</td><td></td>
                <td class="text-start">IRONING</td>
            </tr>
            <tr class="text-center">
                <td class="text-start">CUTTING</td><td></td><td rowspan="2" class="fw-bold">{{ $pDates['d2'] }}</td>
                <td class="text-start">ASSEMBLE</td><td></td><td rowspan="2" class="fw-bold">{{ $pDates['d4'] }}</td>
                <td class="text-start">PACKING</td>
            </tr>
            <tr class="text-center">
                <td class="text-start">FUSING & LOGO</td><td></td>
                <td class="text-start">ASSEMBLE STORE</td><td></td>
                <td class="text-start">DELIVERY</td>
            </tr>
            <tr class="text-center">
                <td class="text-start">CUTTING SEND BY</td><td></td><td></td>
                <td class="text-start">KAJA & BUTTON</td><td></td><td rowspan="3" class="fw-bold">{{ $pDates['d5'] }}</td>
                <td class="text-start">F.G STORE</td><td></td><td></td>
            </tr>
            <tr class="text-center">
                <td class="text-start">CUTTING RECD BY</td><td></td><td></td>
                <td class="text-start">TRIM & CHECK</td><td></td>
                <td></td><td></td><td></td>
            </tr>
            <tr class="text-center">
                <td class="text-start">UNIT INCHARGE</td><td></td><td></td>
                <td class="text-start">PRO SEND</td><td></td>
                <td></td><td></td><td></td>
            </tr>
        </tbody>
    </table>
    <div style="margin-top: 3pt; font-weight: bold; font-size: 7pt;">
        MANDATORY : Please put your signature once you have completed your SYSTEM ENTRY
    </div>
     @if(isset($is_print) && $is_print)
        <script>
            window.onload = function() {
                window.print();
            }
        </script>
    @endif
</body>
</html>

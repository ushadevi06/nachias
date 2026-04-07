<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Job Card - {{ $jobCard->job_card_no }}</title>
    <style>
        @page {
            margin: 10px;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 6pt;
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
            border: 0.5pt solid #eee;
            padding: 3px 4px;
            vertical-align: middle;
        }
        .fw-bold {
            font-weight: bold;
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
            max-width: 60pt;
            max-height: 50pt;
            display: block;
            margin: 2px auto;
        }
        .signature-section td {
            height: 14pt;
            font-size: 6pt;
        }
    </style>
</head>
<body>
    @php
        $allSizes = [];
        if ($jobCard->sizeRatio && $jobCard->sizeRatio->size) {
            $allSizes = array_values(array_filter(array_map('trim', explode(',', $jobCard->sizeRatio->size))));
        }

        $activeFs = [];
        $activeHs = [];
        foreach($jobCard->cuttingSizeRatios as $ratio) {
            if ($ratio->qty_fs > 0) $activeFs[] = $ratio->size;
            if ($ratio->qty_hs > 0) $activeHs[] = $ratio->size;
        }
        $activeFs = array_values(array_unique($activeFs));
        sort($activeFs, SORT_NUMERIC);
        $activeHs = array_values(array_unique($activeHs));
        sort($activeHs, SORT_NUMERIC);

        if (empty($activeFs) && empty($activeHs)) {
            $activeFs = $allSizes;
        }
        
        $fabricCount = $jobCard->fabricDetails->count();
        $swatchWidth = $fabricCount > 0 ? (100 / $fabricCount) : 25;
        if($swatchWidth > 33.3) $swatchWidth = 33.3; 
        if($swatchWidth < 14.2) $swatchWidth = 14.2;

        $logoPath = public_path('assets/images/jc_logo.png');
        $logoSrc = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';

        $favPath = public_path('assets/images/fav.jpeg');
        $favSrc = file_exists($favPath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($favPath)) : '';
    @endphp

    {{-- Header Table --}}
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
            <td class="text-center bg-blue urgent-text" style="width: 10%;">URGENT</td>
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

    {{-- Info Grid --}}
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
            <td class="fw-bold text-end" style="font-size: 7px;">H.O/D.C/NO</td>
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
    <table class="table table-bordered" style="margin-top: -0.5pt;">
        <tr>
            <td class="fw-bold" style="width: 8%;">WITHIN DAYS</td>
            <td style="width: 12%;">14</td>
            <td colspan="4" class="text-center fw-bold bg-light" style="width: 40%;">CUTTING SIZE RATIO</td>
            <td colspan="2" class="text-center fw-bold bg-light" style="width: 28%;">CUTTING MARK</td>
            <td class="fw-bold" style="width: 12%;">H.O.D.C DATE</td>
        </tr>
        <tr>
            <td class="fw-bold">CAD MARK WITDTH</td>
            <td>{{ $jobCard->width ?: '-' }}</td>
            <td class="text-center fw-bold bg-light" style="width: 8%;">SIZE</td>
            @foreach($allSizes as $s)
                <td class="text-center fw-bold" style="width: {{ 40 / count($allSizes) }}%;">{{ $s }}</td>
            @endforeach
            <td class="text-center fw-bold bg-light" style="width: 14%;">SIZE</td>
            <td class="text-center fw-bold bg-light" style="width: 14%;">MARK</td>
            <td rowspan="4"></td>
        </tr>
        <tr>
            <td class="fw-bold" rowspan="2">MRP</td>
            <td rowspan="2"></td>
            <td class="text-center fw-bold bg-light">QTY - F/S</td>
            @foreach($allSizes as $s)
                @php $ratio = $jobCard->cuttingSizeRatios->where('size', $s)->first(); @endphp
                <td class="text-center">{{ $ratio ? (int)$ratio->qty_fs : '-' }}</td>
            @endforeach
            <td rowspan="3" colspan="2" style="padding: 0;"></td>
        </tr>
        <tr>
            <td class="text-center fw-bold bg-light">QTY - H/S</td>
            @foreach($allSizes as $s)
                @php $ratio = $jobCard->cuttingSizeRatios->where('size', $s)->first(); @endphp
                <td class="text-center">{{ $ratio ? (int)$ratio->qty_hs : '-' }}</td>
            @endforeach
        </tr>
        <tr>
            <td class="fw-bold">F/S</td>
            <td>{{ number_format($jobCard->price_fs, 2) }}</td>
            <td colspan="{{ count($allSizes) + 1 }}"></td>
        </tr>
        <tr>
            <td class="fw-bold">H/S</td>
            <td>{{ number_format($jobCard->price_hs, 2) }}</td>
            <td colspan="{{ count($allSizes) + 3 }}"></td>
        </tr>
    </table>

    {{-- Fabric Details (Horizontal Format) --}}
    <table class="table table-bordered" style="margin-top: 5px;">
        <tr>
            <td class="bg-light fw-bold" style="width: 15%;">FABRIC TYPE</td>
            <td colspan="{{ count($jobCard->fabricDetails) }}" class="text-center fw-bold" style="color: #2f6fae; font-size: 8pt;">
                {{ $jobCard->fabricType->fabric_type ?? 'N/A' }}
            </td>
            @for($i = $jobCard->fabricDetails->count(); $i < 3; $i++)
                <td></td>
            @endfor
        </tr>
        <tr>
            <td class="bg-light fw-bold" style="width: 15%;">ART NO</td>
            @foreach($jobCard->fabricDetails as $detail)
                <td class="text-center fw-bold">
                    {{ $detail->art_no }}
                    @if($detail->item_name)
                        <br><span style="font-size: 5pt; font-weight: normal;">{{ $detail->item_name }}</span>
                    @endif
                </td>
            @endforeach
            @for($i = $jobCard->fabricDetails->count(); $i < 3; $i++)
                <td></td>
            @endfor
        </tr>
        <tr>
            <td class="bg-light fw-bold">WIDTH</td>
            @foreach($jobCard->fabricDetails as $detail)
                <td class="text-center">{{ $detail->width ?: '-' }}</td>
            @endforeach
            @for($i = $jobCard->fabricDetails->count(); $i < 3; $i++)
                <td></td>
            @endfor
        </tr>
        <tr>
            <td class="bg-light fw-bold">Issue Meter</td>
            @foreach($jobCard->fabricDetails as $detail)
                <td class="text-center">{{ (int)$detail->mtr ?: '-' }}</td>
            @endforeach
            @for($i = $jobCard->fabricDetails->count(); $i < 3; $i++)
                <td></td>
            @endfor
        </tr>
        <tr>
            <td class="bg-light fw-bold">IN/OUT</td>
            @foreach($jobCard->fabricDetails as $detail)
                <td class="text-center">{{ $detail->in_out ?: '-' }}</td>
            @endforeach
            @for($i = $jobCard->fabricDetails->count(); $i < 3; $i++)
                <td></td>
            @endfor
        </tr>
        <tr>
            <td class="bg-light fw-bold">N.PATTI</td>
            @foreach($jobCard->fabricDetails as $detail)
                <td class="text-center">{{ $detail->n_patti ?: '-' }}</td>
            @endforeach
            @for($i = $jobCard->fabricDetails->count(); $i < 3; $i++)
                <td></td>
            @endfor
        </tr>
    </table>

    {{-- Quantity Summary --}}
    <table class="table table-bordered" style="margin-top: 5px;">
        <thead>
            <tr class="bg-light text-center">
                <th rowspan="2" style="width: 10%;">ART NO</th>
                <th colspan="4">F/S</th>
                <th colspan="4">H/S</th>
                <th rowspan="2" style="width: 8%;">TOTAL</th>
            </tr>
            <tr class="bg-light text-center">
                @foreach($allSizes as $s) <th style="width: {{ 40 / count($allSizes) }}%;">{{ $s }}</th> @endforeach
                @foreach($allSizes as $s) <th style="width: {{ 40 / count($allSizes) }}%;">{{ $s }}</th> @endforeach
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($jobCard->fabricDetails as $detail)
                @php 
                    $rowTotal = $detail->quantities->sum('total_qty');
                    $grandTotal += $rowTotal;
                @endphp
                <tr class="text-center">
                    <td class="fw-bold">{{ $detail->art_no }}</td>
                    @foreach($allSizes as $s)
                        @php $q = $detail->quantities->where('size', $s)->first(); @endphp
                        <td>{{ $q ? (int)$q->qty_fs : '-' }}</td>
                    @endforeach
                    @foreach($allSizes as $s)
                        @php $q = $detail->quantities->where('size', $s)->first(); @endphp
                        <td>{{ $q ? (int)$q->qty_hs : '-' }}</td>
                    @endforeach
                    <td class="fw-bold">{{ (int)$rowTotal }}</td>
                </tr>
            @endforeach
            <tr class="bg-light text-center fw-bold">
                <td>TOTAL</td>
                @foreach($allSizes as $s)
                    <td>{{ $jobCard->fabricDetails->map->quantities->flatten()->where('size', $s)->sum('qty_fs') ?: '-' }}</td>
                @endforeach
                @foreach($allSizes as $s)
                    <td>{{ $jobCard->fabricDetails->map->quantities->flatten()->where('size', $s)->sum('qty_hs') ?: '-' }}</td>
                @endforeach
                <td>{{ (int)$grandTotal }}</td>
            </tr>
        </tbody>
    </table>

    </table>

    @if($jobCard->operations && $jobCard->operations->count() > 0)
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
    @endif

    {{-- Signatures Section --}}
    <table class="table table-bordered signature-section" style="margin-top: 10pt;">
        <tr class="bg-light fw-bold">
            <td colspan="13">AUTHORISED SIGNATURES</td>
        </tr>
        <tr class="bg-light text-center fw-bold">
            <td style="width: 10%;">SECTION</td>
            <td style="width: 12%;">INCHARGE SIGN</td>
            <td style="width: 10%;">PLANING DATE</td>
            <td style="width: 10%;">SECTION</td>
            <td style="width: 12%;">INCHARGE SIGN</td>
            <td style="width: 10%;">DATE</td>
            <td style="width: 10%;">SECTION</td>
            <td style="width: 12%;">INCHARGE SIGN</td>
            <td style="width: 8%;">DATE</td>
            <td style="width: 6%;">TOTAL MTRS</td>
            <td style="width: 5%;">{{ (int)$grandTotal }}</td>
            <td colspan="2">REMARKS:</td>
        </tr>
        <tr>
            <td class="fw-bold bg-light">PURCHASE</td>
            <td></td>
            <td class="text-center">{{ date('d-m-Y', strtotime($jobCard->job_card_date)) }}</td>
            <td class="fw-bold bg-light">READY</td>
            <td></td>
            <td class="text-center" rowspan="2" style="vertical-align: middle;">{{ date('d-m-Y', strtotime($jobCard->job_card_date . ' + 7 days')) }}</td>
            <td class="fw-bold bg-light">FINAL FINISH RECD</td>
            <td rowspan="2"></td>
            <td rowspan="7" class="text-center" style="vertical-align: middle;">{{ date('d-m-Y', strtotime($jobCard->delivery_date)) }}</td>
            <td colspan="4" rowspan="7"></td>
        </tr>
        <tr>
            <td class="fw-bold bg-light">FABRIC STORE</td>
            <td></td>
            <td></td>
            <td class="fw-bold bg-light">READY STORE</td>
            <td></td>
            <td class="fw-bold bg-light">IRONING</td>
        </tr>
        <tr>
            <td class="fw-bold bg-light">CUTTING</td>
            <td rowspan="5"></td>
            <td rowspan="5" class="text-center" style="vertical-align: middle;">{{ date('d-m-Y', strtotime($jobCard->job_card_date . ' + 3 days')) }}</td>
            <td class="fw-bold bg-light">ASSEMBLE</td>
            <td rowspan="2"></td>
            <td rowspan="2" class="text-center" style="vertical-align: middle;">{{ date('d-m-Y', strtotime($jobCard->job_card_date . ' + 10 days')) }}</td>
            <td class="fw-bold bg-light">PACKING</td>
            <td rowspan="2"></td>
        </tr>
        <tr>
            <td class="fw-bold bg-light">FUSING & LOGO</td>
            <td class="fw-bold bg-light">ASSEMBLE STORE</td>
            <td class="fw-bold bg-light">DELIVERY</td>
        </tr>
        <tr>
            <td class="fw-bold bg-light">CUTTING SEND BY</td>
            <td class="fw-bold bg-light">KAJA & BUTTON</td>
            <td rowspan="3"></td>
            <td rowspan="3" class="text-center" style="vertical-align: middle;">{{ date('d-m-Y', strtotime($jobCard->job_card_date . ' + 12 days')) }}</td>
            <td class="fw-bold bg-light">F.G STORE</td>
            <td rowspan="3"></td>
        </tr>
        <tr>
            <td class="fw-bold bg-light">CUTTING RECD BY</td>
            <td class="fw-bold bg-light">TRIM & CHECK</td>
            <td></td>
        </tr>
        <tr>
            <td class="fw-bold bg-light">UNIT INCHARGE</td>
            <td class="fw-bold bg-light">PRO SEND</td>
            <td></td>
        </tr>
    </table>
    <div style="margin-top: 5pt; font-weight: bold; font-size: 7pt;">
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

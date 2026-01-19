<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Job Card - {{ $jobCard->job_card_no }}</title>
    <style>
        @page {
            margin: 0.3cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8px;
            margin: 0;
            padding: 0;
            color: #000;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #000;
            padding: 2px 3px;
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
            background-color: #f2f2f2;
        }
        .header-title {
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1px;
            text-align: center;
        }
        .brand-title {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
        }
        .swatch-img {
            max-width: 60px;
            max-height: 45px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }
        .label {
            font-size: 7px;
            font-weight: bold;
        }
        .footer-grid td {
            height: 35px;
            vertical-align: top;
            width: 12.5%;
            font-size: 7px;
        }
    </style>
</head>
<body>
    @php
        $allSizes = ['36', '38', '40', '42', '44'];
        if ($jobCard->sizeRatio && $jobCard->sizeRatio->size) {
            $allSizes = array_values(array_filter(array_map('trim', explode(',', $jobCard->sizeRatio->size))));
        }

        $activeFs = [];
        $activeHs = [];
        foreach($jobCard->cuttingSizeRatios as $ratio) {
            if ($ratio->qty_fs > 0) $activeFs[] = $ratio->size;
            if ($ratio->qty_hs > 0) $activeHs[] = $ratio->size;
        }
        $activeFs = array_values(array_unique($activeFs ?: $allSizes));
        sort($activeFs, SORT_NUMERIC);
        $activeHs = array_values(array_unique($activeHs ?: $allSizes));
        sort($activeHs, SORT_NUMERIC);
    @endphp

    {{-- Main Header Table (10 Columns + Right Side Bar) --}}
    <table class="table table-bordered" style="table-layout: fixed;">
        <tr>
            <td colspan="10" class="text-center py-1">
                <div class="header-title">NACHIAS FASHION PVT LTD</div>
            </td>
        </tr>
        <tr>
            <td colspan="10" class="text-center py-1">
                <div class="brand-title">{{ strtoupper($jobCard->brand->brand_name ?? '') }}</div>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="fw-bold">JOB CARD</td>
            <td colspan="6"></td>
            <td colspan="2" class="text-center fw-bold">MARK CHECKER</td>
        </tr>
        {{-- Row 1 --}}
        <tr>
            <td class="fw-bold" style="width: 10%;">CUTTING NO</td>
            <td style="width: 12%;">{{ $jobCard->job_card_no }}</td>
            <td class="fw-bold" style="width: 5%;">FIT</td>
            <td class="text-center" style="width: 25%;">{{ strtoupper($jobCard->fit) ?: '-' }}</td>
            <td class="fw-bold" style="width: 5%;">CUFF</td>
            <td class="text-center" style="width: 10%;">{{ strtoupper($jobCard->cuff_type) ?: '-' }}</td>
            <td class="fw-bold" style="width: 5%;">CROSS</td>
            <td class="text-center" style="width: 10%;"></td>
            <td class="fw-bold" style="width: 10%;">CUTTING MASTER</td>
            <td style="width: 10%;">{{ $jobCard->cuttingMaster->name ?? '' }}</td>
        </tr>
        {{-- Row 2 --}}
        <tr>
            <td class="fw-bold">ISSUE DATE</td>
            <td>{{ $jobCard->job_card_date ? date('d-m-Y', strtotime($jobCard->job_card_date)) : '' }}</td>
            <td class="fw-bold">N.PATTI</td>
            <td class="text-center">{{ strtoupper($jobCard->patti_type) ?: '-' }}</td>
            <td class="fw-bold">POCKET</td>
            <td class="text-center">{{ strtoupper($jobCard->pocket_type) ?: '-' }}</td>
            <td class="fw-bold">CROSS</td>
            <td class="text-center"></td>
            <td class="fw-bold">CUTTING DATE</td>
            <td>
                @if($jobCard->cutting_date)
                    {{ date('d-m-Y', strtotime($jobCard->cutting_date)) }}
                @endif
                <div style="float: right; border-left: 1px solid #000; padding-left: 3px; font-weight: bold; font-size: 7px;">H.O.D.C NO</div>
            </td>
        </tr>
        {{-- Row 3 --}}
        <tr>
            <td class="fw-bold">DELIVERY DATE</td>
            <td>{{ $jobCard->delivery_date ? date('d-m-Y', strtotime($jobCard->delivery_date)) : '' }}</td>
            <td class="fw-bold">COLLAR</td>
            <td class="text-center">{{ strtoupper($jobCard->collar_type) ?: '-' }}</td>
            <td class="fw-bold">BOT.CUT</td>
            <td class="text-center">{{ strtoupper($jobCard->bottom_cut) ?: '-' }}</td>
            <td class="fw-bold">AERO CUT</td>
            <td class="text-center"></td>
            <td class="fw-bold">CUTTING ISSUE UNIT</td>
            <td>{{ $jobCard->cutting_issue_unit ?: '' }}</td>
        </tr>
        {{-- Row 4 --}}
        <tr>
            <td class="fw-bold">WASHING</td>
            <td>{{ $jobCard->washing ?: 'No' }}</td>
            <td colspan="7" class="text-center fw-bold bg-light">CUTTING SIZE RATIO</td>
            <td class="fw-bold" style="font-size: 7px;">H.O.D.C DATE</td>
        </tr>
        {{-- Row 5 --}}
        <tr>
            <td class="fw-bold">WIDTH</td>
            <td>{{ $jobCard->width ?: '-' }}</td>
            <td class="fw-bold text-center">SIZE</td>
            @foreach($allSizes as $size)
                <td class="text-center fw-bold">{{ $size }}</td>
            @endforeach
            @for($i = count($allSizes); $i < 5; $i++)
                <td></td>
            @endfor
            <td class="text-center fw-bold bg-light">CUTTING MARK AND LAY</td>
        </tr>
        {{-- Row 6 --}}
        <tr>
            <td class="fw-bold">MRP</td>
            <td>{{ number_format($jobCard->mrp, 2) }}</td>
            <td class="fw-bold text-center">QTY - F/S</td>
            @foreach($allSizes as $size)
                @php $ratio = $jobCard->cuttingSizeRatios->where('size', $size)->first(); @endphp
                <td class="text-center">
                    {{ ($ratio && $ratio->qty_fs > 0) ? (int)$ratio->qty_fs : '-' }}
                </td>
            @endforeach
            @for($i = count($allSizes); $i < 5; $i++)
                <td></td>
            @endfor
            <td style="padding: 0;">
                <table class="table" style="border: none;">
                    <tr>
                        <td class="text-center fw-bold" style="width: 50%; border-right: 1px solid #000; border-bottom: none; border-top: none;">SIZE</td>
                        <td class="text-center fw-bold" style="border: none;">MARK</td>
                    </tr>
                </table>
            </td>
        </tr>
        {{-- Row 7 --}}
        <tr>
            <td class="fw-bold">F/S</td>
            <td>{{ number_format($jobCard->price_fs, 2) }}</td>
            <td class="fw-bold text-center">QTY - F/S</td>
            @foreach($allSizes as $size)
                <td class="text-center">-</td>
            @endforeach
            @for($i = count($allSizes); $i < 5; $i++)
                <td></td>
            @endfor
            <td style="border-top: none; height: 12px;"></td>
        </tr>
        {{-- Row 8 --}}
        <tr>
            <td class="fw-bold">H/S</td>
            <td>{{ number_format($jobCard->price_hs, 2) }}</td>
            <td class="fw-bold text-center">QTY - H/S</td>
            @foreach($allSizes as $size)
                @php $ratio = $jobCard->cuttingSizeRatios->where('size', $size)->first(); @endphp
                <td class="text-center">
                    {{ ($ratio && $ratio->qty_hs > 0) ? (int)$ratio->qty_hs : '-' }}
                </td>
            @endforeach
            @for($i = count($allSizes); $i < 5; $i++)
                <td></td>
            @endfor
            <td class="fw-bold" style="font-size: 7px;">UNIT B/O NO</td>
        </tr>
    </table>

    {{-- Material Details (Vertical Columns) --}}
    <table class="table" style="margin-top: 5px;">
        <tr>
            @foreach($jobCard->fabricDetails as $index => $detail)
                @php $image = $jobCard->images->where('art_no', $detail->art_no)->first(); @endphp
                <td style="vertical-align: top; padding: 2px; width: 14.2%;">
                    <table class="table table-bordered">
                        <tr>
                            <td class="fw-bold bg-light" style="width: 40%;">ART NO</td>
                            <td class="text-center fw-bold" style="font-size: 7px;">{{ $detail->art_no }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-center" style="height: 50px; padding: 1px;">
                                @if($image)
                                    <img src="{{ public_path($image->image) }}" class="swatch-img">
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="bg-light fw-bold" style="font-size: 7px;">WIDTH</td>
                            <td class="text-center">{{ $detail->width ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="bg-light fw-bold" style="font-size: 7px;">Mtr/B.M</td>
                            <td class="text-center">{{ $detail->mtr ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="bg-light fw-bold" style="font-size: 7px;">IN / OUT</td>
                            <td class="text-center">{{ $detail->in_out ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="bg-light fw-bold" style="font-size: 7px;">N.PATTI</td>
                            <td class="text-center">{{ $detail->n_patti ?: '-' }}</td>
                        </tr>
                    </table>
                </td>
            @endforeach
            @for($i = count($jobCard->fabricDetails); $i < 7; $i++)
                <td style="width: 14.2%;"></td>
            @endfor
        </tr>
    </table>

    {{-- Quantity Matrix --}}
    <table class="table table-bordered" style="margin-top: 5px;">
        <thead>
            <tr class="text-center bg-light">
                <th rowspan="2" style="width: 15%;">ART NO</th>
                @if(count($activeFs) > 0)
                    <th colspan="{{ count($activeFs) }}">F/S</th>
                @endif
                @if(count($activeHs) > 0)
                    <th colspan="{{ count($activeHs) }}">H/S</th>
                @endif
                <th colspan="2">EX</th>
                <th rowspan="2" style="width: 8%;">TOTAL</th>
            </tr>
            <tr class="text-center bg-light">
                @foreach($activeFs as $s) <th>{{ $s }}</th> @endforeach
                @foreach($activeHs as $s) <th>{{ $s }}</th> @endforeach
                <th style="width: 5%;">40 H/S</th>
                <th style="width: 5%;">38 F/S</th>
            </tr>
        </thead>
        <tbody>
            @php $grand_total = 0; @endphp
            @foreach($jobCard->fabricDetails as $detail)
                @php
                    $row_total = 0;
                    foreach($activeFs as $s) { $row_total += (int)($detail->{'fs_' . $s} ?? 0); }
                    foreach($activeHs as $s) { $row_total += (int)($detail->{'hs_' . $s} ?? 0); }
                    $grand_total += $row_total;
                @endphp
                <tr class="text-center">
                    <td class="fw-bold">{{ $detail->art_no }}</td>
                    @foreach($activeFs as $s) <td>{{ $detail->{'fs_' . $s} ?: '-' }}</td> @endforeach
                    @foreach($activeHs as $s) <td>{{ $detail->{'hs_' . $s} ?: '-' }}</td> @endforeach
                    <td>-</td>
                    <td>-</td>
                    <td class="fw-bold">{{ $row_total ?: '-' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="text-center fw-bold bg-light">
                <td>TOTAL</td>
                @foreach($activeFs as $s) <td>{{ $jobCard->fabricDetails->sum('fs_'.$s) ?: '-' }}</td> @endforeach
                @foreach($activeHs as $s) <td>{{ $jobCard->fabricDetails->sum('hs_'.$s) ?: '-' }}</td> @endforeach
                <td>-</td>
                <td>-</td>
                <td>{{ $grand_total ?: '-' }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Signature Grid --}}
    <table class="table table-bordered footer-grid" style="margin-top: 5px;">
        <tr class="text-center fw-bold bg-light">
            <td colspan="2">AUTHORISED SIGNATURES</td>
            <td colspan="2">CUTTING RECEIVED BY</td>
            <td colspan="2">ASSAMBILE</td>
            <td colspan="2">TRIMMING & CHECKING</td>
            <td colspan="2">REMARKS</td>
        </tr>
        <tr>
            <td style="width: 5%;">DATE</td><td class="text-center">FABRIC INCHARGE</td>
            <td style="width: 5%;">DATE</td><td class="text-center">UNIT INCHARGE</td>
            <td style="width: 5%;">DATE</td><td class="text-center">PRODUCTION UNIT SEND BY</td>
            <td style="width: 5%;">DATE</td><td class="text-center">IRONING</td>
            <td colspan="2" rowspan="3" class="text-start" style="font-size: 7px; vertical-align: top;">
                {{ $jobCard->remarks }}
            </td>
        </tr>
        <tr>
            <td>DATE</td><td class="text-center">FABRIC ISSUED BY</td>
            <td>DATE</td><td class="text-center">READY SECTION</td>
            <td>DATE</td><td class="text-center">H.O RECEIVED BY</td>
            <td>DATE</td><td class="text-center">PACKING & DELIVERY</td>
        </tr>
        <tr>
            <td>DATE</td><td class="text-center">CUTTING SUPERVISOR</td>
            <td>DATE</td><td class="text-center">READY STORE</td>
            <td>DATE</td><td class="text-center">KAJA & BUTTON</td>
            <td>DATE</td><td class="text-center">F.G STORE</td>
        </tr>
        <tr>
            <td>DATE</td><td class="text-center">CUTTING SEND BY</td>
            <td colspan="6"></td>
            <td colspan="2" class="text-end fw-bold" style="vertical-align: bottom; font-size: 9px;">
                MRP: Rs.{{ number_format($jobCard->mrp, 2) }}
            </td>
        </tr>
    </table>
</body>
</html>

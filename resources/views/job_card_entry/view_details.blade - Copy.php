@extends('layouts.common')
@section('title', 'View Job Card Entry - ' . env('WEBSITE_NAME'))
@section('content')
        <div class="container-xxl section-padding">
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

                $fabricDetails = $jobCard->fabricDetails->filter(function ($detail) use ($artCategoryMap) {
                    $trimmedArt = trim($detail->art_no);
                    return ($artCategoryMap[$trimmedArt] ?? 1) == 1;
                });

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
            <div class="row">
                <div class="col-lg-12 text-end">
                    <a href="{{ route('job_card_entries.download', $jobCard->id) }}" class="btn btn-primary"  target="_blank"><i class="ri ri-download-line me-1"></i> Download</a>
                    <a href="{{ route('job_card_entries.print', $jobCard->id) }}" class="btn btn-primary" target="_blank"><i class="ri ri-printer-line me-1"></i> Print</a>
                    <a href="{{ url('job_card_entries') }}" class="btn btn-secondary"><i class="ri ri-arrow-left-line me-1"></i> Back to List</a>
                </div>
                <div class="col-lg-12 mt-4">
                    <style>
                        .job-card-table {
                            table-layout: fixed !important;
                            width: 100% !important;
                        }
                        .job-card-table td {
                            overflow: hidden;
                            text-overflow: ellipsis;
                            padding: 8px 10px !important;
                        }
                        .fw-bold {
                            padding: 10px !important;
                        }
                        @media print {
                            @page { 
                                size: landscape; 
                                margin: 5mm; 
                            }
                            body {
                                padding: 0 !important;
                                margin: 0 !important;
                                background: #fff !important;
                            }
                            .container-xxl { 
                                max-width: 100% !important; 
                                width: 100% !important; 
                                padding: 0 !important; 
                                margin: 0 !important;
                            }
                            .card { 
                                border: none !important; 
                                box-shadow: none !important; 
                                margin: 0 !important;
                            }
                            .btn, .text-end { 
                                display: none !important; 
                            }
                            .section-padding {
                                padding: 0 !important;
                            }
                            .mt-4 {
                                margin-top: 0 !important;
                            }
                        }
                        @media (max-width: 991px) {
                            .job-card-table {
                                font-size: 0.55rem !important; 
                            }
                            .job-card-table .fw-bold {
                                font-size: 0.55rem !important;
                            }
                            .job-card-table td {
                                padding: 1px 2px !important; 
                            }
                        }
                        @media (max-width: 575px) {
                            .job-card-table {
                                font-size: 0.45rem !important; 
                            }
                            .job-card-table .fw-bold {
                                font-size: 0.45rem !important;
                            }
                            .job-card-table td {
                                padding: 0.5px 1px !important;
                            }
                        }
                    </style>
                    <div class="card shadow-sm">
                        <div class="card-body p-0">
                            <div class="text-center">
                                <table class="table table-bordered table-sm mb-0 job-card-table text-center">
                                    <tr>
                                        <!-- LEFT LOGO -->
                                        <td rowspan="2" style="width:15%; vertical-align:middle; background:#fff;">
                                            <img src="{{ url('assets/images/jc_logo.png') }}" style="max-width:100%; height:auto;">
                                        </td>

                                        <!-- SEASON CODE -->
                                        <td style="width:8%; font-weight:bold; font-size:13px;">SEASON CODE</td>

                                        <!-- JOB CARD -->
                                        <td colspan="4" style="width:40%; font-weight:bold; font-size:20px;">
                                            JOB CARD
                                        </td>

                                        <!-- JOB CARD TYPE -->
                                        <td colspan="2" style="width:25%; font-weight:bold; font-size:16px;">
                                            JOB CARD TYPE
                                        </td>

                                        <!-- JOB CARD TYPE (Dynamic) -->
                                        <td style="width:12%; font-weight:bold; background:#2f6fae; color:#fff; font-size:16px;">
                                            {{ strtoupper($jobCard->job_card_type ?? 'Regular') }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <!-- SEASON VALUE -->
                                        <td style="font-weight:bold; font-size:16px;">{{ $jobCard->season->season_code ?? '' }}</td>

                                        <!-- STYLE NAME -->
                                        <td colspan="4" style="font-weight:bold; font-size:18px;">
                                            {{ strtoupper($jobCard->brand->brand_name ?? '') }} 
                                        </td>

                                        <!-- LOGO -->
                                        <td style="font-weight:bold; vertical-align:middle; font-size:18px;">LOGO</td>

                                        <!-- POCKET CENTER -->
                                        <td style="font-weight:bold; vertical-align:middle; font-size:15px;">
                                            POCKET 
                                            <img src="{{ url('assets/images/fav.jpeg') }}" style="height:30px; vertical-align:middle; margin:0 2px;">
                                            CENTER
                                        </td>

                                        <!-- MARK CHECKER -->
                                        <td style="font-size:11px;">
                                            MARK CHECKER'S<br>SIGN
                                        </td>
                                    </tr>
                                </table>

                            </div>

                            <table class="table table-bordered table-sm mb-0 job-card-table" style="border-color: #eeeeee !important;">
                                <tbody>
                                    {{-- Row 1 --}}
                                    <tr>
                                        <td class="fw-bold p-3" style="width: 9%; vertical-align:middle;">CUTTING NO</td>
                                        <td class=" p-3" style="width: 11%; vertical-align:middle;">{{ $jobCard->job_card_no }}</td>
                                        <td class="p-3" style="width: 6%; vertical-align:middle;">FIT</td>
                                        <td class="fw-bold p-3 text-center" style="width: 18%; vertical-align:middle;">{{ strtoupper($jobCard->fit->fit_name ?? '-') }}</td>
                                        <td class="p-3" style="width: 6%; vertical-align:middle;">CUFF</td>
                                        <td class="fw-bold p-3 text-center" style="width: 10%; vertical-align:middle;">{{ strtoupper($jobCard->cuffType->cuff_type_name ?? '-') }}</td>
                                        <td class="fw-bold p-3" style="width: 12%;">CUTTING MASTER</td>
                                        <td class="p-3" style="width: 18%;">{{ $jobCard->cuttingMaster->name ?? '' }}</td>
                                        <td class="fw-bold p-3" style="width: 10%;"></td>
                                    </tr>

                                    {{-- Row 2 --}}
                                    <tr>
                                        <td class="fw-bold p-3">ISSUE DATE</td>
                                        <td class="p-3">{{ $jobCard->job_card_date ? date('d-m-Y', strtotime($jobCard->job_card_date)) : '' }}</td>
                                        <td class="p-3">N.PATTI</td>
                                        <td class="fw-bold p-3 text-center">{{ strtoupper($jobCard->pattiType->patti_type_name ?? '-') }}</td>
                                        <td class="p-3">POCKET</td>
                                        <td class="fw-bold p-3 text-center">{{ strtoupper($jobCard->pocketType->pocket_type_name ?? '-') }}</td>
                                        <td class="fw-bold p-3">CUTTING DATE</td>
                                        <td class="p-3">{{ $jobCard->cutting_date ? date('d-m-Y', strtotime($jobCard->cutting_date)) : '' }}</td>
                                        <td class="text-center fw-bold p-3">H.O / D.C /NO</td>
                                    </tr>

                                    {{-- Row 3 --}}
                                    <tr>
                                        <td class="fw-bold p-3">DELIVERY DATE</td>
                                        <td class="p-3">{{ $jobCard->delivery_date ? date('d-m-Y', strtotime($jobCard->delivery_date)) : '' }}</td>
                                        <td class="p-3">COLLAR</td>
                                        <td class="fw-bold p-3 text-center">{{ strtoupper($jobCard->collarType->collar_type_name ?? '-') }}</td>
                                        <td class="p-3">BOT.CUT</td>
                                        <td class="fw-bold p-3 text-center">{{ strtoupper($jobCard->bottomCut->bottom_cut_name ?? '-') }}</td>
                                        <td class="fw-bold p-3">CUTTING ISSUE UNIT</td>
                                        <td class="p-3">{{ $jobCard->cuttingIssueUnitMapping->name ?? $jobCard->cutting_issue_unit }}</td>
                                        <td class="fw-bold p-3"></td>
                                    </tr>

                                    {{-- Row 4 --}}
                                    <tr>
                                        <td class="fw-bold p-3">WITHIN DAYS</td>
                                        <td class="p-3">14</td>
                                        <td colspan="4" class="text-center fw-bold p-3" style="font-size: 0.9rem; border-bottom: 2px solid #eeeeee;">CUTTING SIZE RATIO</td>
                                        <td colspan="2" class="text-center fw-bold p-3" style="font-size: 0.9rem; border-bottom: 2px solid #eeeeee;">CUTTING MARK</td>  
                                        <td class="text-center fw-bold p-3" style="font-size: 0.9rem; border-bottom: 2px solid #eeeeee;">H.O / D.C /DATE</td>
                                    </tr>

                                    {{-- Row 5 --}}
                                    <tr>
                                        <td class="fw-bold p-3" style="font-size: 12px;"></td>
                                        <td class="p-3">{{ $jobCard->width ?: '-' }}</td>
                                        <td class="fw-bold p-3 text-center">SIZE</td>
                                        <td colspan="3" class="p-0">
                                            <table class="table mb-0 job-card-table">
                                                <tr class="text-center fw-bold" style="font-size: 0.8rem;">
                                                    @php $sizes = $allSizes; @endphp
                                                    @foreach($sizes as $size)
                                                        <td class="p-3" style="width: 14.28%; border-right:1px solid #eeeeee;border-bottom:none !important;">{{ $size }}</td>
                                                    @endforeach
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="p-0" colspan="2">
                                            <div class="row g-0">
                                                <div class="col-5 fw-bold text-center p-3 border-end" style="font-size: 0.7rem;">SIZE</div>
                                                <div class="col-3 fw-bold text-center p-3 border-end" style="font-size: 0.7rem;">S.TYPE</div>
                                                <div class="col-4 fw-bold text-center p-3" style="font-size: 0.7rem;">MARK</div>
                                            </div>
                                        </td>
                                        @php 
                                            $totalRatioRows = max(4, (count($fsRows) ?: 1) + (count($hsRows) ?: 1)) + 1;
                                            $currentRow = 1; 
                                        @endphp
                                        <td class="text-center">{{ $currentRow === 3 ? 'UNIT D.C NO' : '' }}</td>
                                    </tr>

                                    {{-- Dynamic Ratio Rows --}}
                                    @php
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

                                        $allLayMarks = [];
                                        $firstFabric = $fabricDetails->first();
                                        if ($firstFabric) {
                                            $allLayMarks = $firstFabric->layMarks;
                                        }
                                        @endphp

                                    {{-- Row 6 - QTY F/S --}}
                                    @if(count($fsRows) > 0)
                                        @foreach($fsRows as $index => $row)
                                            <tr>
                                                @if($index === 0)
                                                    <td class="fw-bold py-1"></td>
                                                    <td class="py-1"></td>
                                                @else
                                                    <td colspan="2" style="border: none;"></td>
                                                @endif
                                                <td class="fw-bold py-1 text-center">QTY - F/S</td>
                                                <td colspan="3" class="p-0">
                                                    <table class="table table-bordered mb-0 job-card-table" style="border: none;">
                                                        <tr class="text-center" style="font-size: 0.8rem;">
                                                            @foreach($sizes as $size)
                                                                @php 
                                                                    $val = $row['values'][$size] ?? '-';
                                                                @endphp
                                                                <td class="py-1" style="width: 14.28%; border: 1px solid #fff; border-right: 1px solid #eeeeee;">
                                                                    {{ $val != '' && $val != '-' ? (int) $val : '-' }}
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    </table>
                                                </td>
                                                @if($index === 0)
                                                <td class="p-0 border-bottom text-center" rowspan="{{ $totalRatioRows }}">
                                                    @foreach($allLayMarks as $lm)
                                                        <div class="row g-0 border-bottom">
                                                            <div class="col-5 text-center border-end" style="font-size: 0.75rem; padding: 4px;">
                                                                {{ is_array($lm->sizes) ? implode(',', $lm->sizes) : $lm->sizes }}
                                                            </div>
                                                            <div class="col-3 text-center border-end" style="font-size: 0.75rem; padding: 4px;">
                                                                {{ $lm->sleeve_type ?? $lm->sleeve ?? 'F/S' }}
                                                            </div>
                                                            <div class="col-4 text-center" style="font-size: 0.75rem; padding: 4px;">
                                                                {{ $lm->lay_mark_meter }}
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </td>
                                                @endif
                                                @php $currentRow++; @endphp
                                                <td class="py-1 text-center fw-bold">{{ $currentRow === 3 ? 'UNIT D.C NO' : '' }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="fw-bold py-1"></td>
                                            <td class="py-1"></td>
                                            <td class="fw-bold py-1 text-center">QTY - F/S</td>
                                            <td colspan="3" class="p-0">
                                                <table class="table table-bordered mb-0 job-card-table" style="border: none;">
                                                    <tr class="text-center" style="font-size: 0.8rem;">
                                                        @foreach($sizes as $size)
                                                            @php $ratio = $jobCard->cuttingSizeRatios->where('size', $size)->first(); @endphp
                                                            <td class="py-1" style="width: 14.28%; border: 1px solid #fff; border-right: 1px solid #eeeeee;">
                                                                {{ ($ratio && $ratio->qty_fs > 0) ? (int) $ratio->qty_fs : '-' }}
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                </table>
                                            </td>
                                            <td class="p-0 border-bottom text-center" rowspan="{{ $totalRatioRows }}">
                                                @foreach($allLayMarks as $lm)
                                                    <div class="row g-0 border-bottom">
                                                        <div class="col-5 text-center border-end" style="font-size: 0.75rem; padding: 4px;">
                                                            {{ is_array($lm->sizes) ? implode(',', $lm->sizes) : $lm->sizes }}
                                                        </div>
                                                        <div class="col-3 text-center border-end" style="font-size: 0.75rem; padding: 4px;">
                                                            {{ $lm->sleeve_type ?? $lm->sleeve ?? 'F/S' }}
                                                        </div>
                                                        <div class="col-4 text-center" style="font-size: 0.75rem; padding: 4px;">
                                                            {{ $lm->lay_mark_meter }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </td>
                                            @php $currentRow++; @endphp
                                            <td class="py-1 text-center fw-bold">{{ $currentRow === 3 ? 'UNIT D.C NO' : '' }}</td>
                                        </tr>
                                    @endif

                                    {{-- Row 7 - QTY H/S --}}
                                    @if(count($hsRows) > 0)
                                        @foreach($hsRows as $index => $row)
                                            <tr>
                                                @if($index === 0 && count($fsRows) === 0)
                                                    <td class="fw-bold py-1"></td>
                                                    <td class="py-1"></td>
                                                @elseif(count($fsRows) > 0)
                                                    <td colspan="2" style="border: none;"></td>
                                                @endif
                                                <td class="fw-bold py-1 text-center">QTY - H/S</td>
                                                <td colspan="3" class="p-0">
                                                    <table class="table table-bordered mb-0 job-card-table" style="border: none;">
                                                        <tr class="text-center" style="font-size: 0.8rem;">
                                                            @foreach($sizes as $size)
                                                                @php 
                                                                    $val = $row['values'][$size] ?? '-';
                                                                @endphp
                                                                <td class="py-1" style="width: 14.28%; border: 1px solid #fff; border-right: 1px solid #eeeeee;">
                                                                    {{ $val != '' && $val != '-' ? (int) $val : '-' }}
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    </table>
                                                </td>

                                                @php $currentRow++; @endphp
                                                <td class="py-1 text-center fw-bold">{{ $currentRow === 3 ? 'UNIT D.C NO' : '' }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            @if(count($fsRows) > 0)
                                                <td colspan="2" style="border: none;"></td>
                                            @else
                                                <td class="fw-bold py-1"></td>
                                                <td class="py-1"></td>
                                            @endif
                                            <td class="fw-bold py-1 text-center">QTY - H/S</td>
                                            <td colspan="3" class="p-0">
                                                <table class="table table-bordered mb-0 job-card-table" style="border: none;">
                                                    <tr class="text-center" style="font-size: 0.8rem;">
                                                        @foreach($sizes as $size)
                                                            @php $ratio = $jobCard->cuttingSizeRatios->where('size', $size)->first(); @endphp
                                                            <td class="py-1" style="width: 14.28%; border: 1px solid #fff; border-right: 1px solid #eeeeee;">
                                                                {{ ($ratio && $ratio->qty_hs > 0) ? (int) $ratio->qty_hs : '-' }}
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                </table>
                                            </td>

                                            @php $currentRow++; @endphp
                                            <td class="py-1 text-center fw-bold">{{ $currentRow === 3 ? 'UNIT D.C NO' : '' }}</td>
                                        </tr>
                                    @endif

                                    @php 
                                        $rowsRendered = (count($fsRows) ?: 1) + (count($hsRows) ?: 1);
                                    @endphp
                                    @for($i = $rowsRendered; $i < 4; $i++)
                                        @php $currentRow++; @endphp
                                        <tr style="height: 40px;">
                                            <td class="fw-bold py-1">&nbsp;</td>
                                            <td class="py-1">&nbsp;</td>
                                            <td class="fw-bold py-1 text-center">&nbsp;</td>
                                            <td colspan="3">&nbsp;</td>
                                            <td class="py-1 text-center fw-bold">{{ $currentRow === 3 ? 'UNIT D.C NO' : '' }}</td>
                                        </tr>
                                    @endfor

                                    {{-- Row 8 --}}
                                    <tr>
                                        <td class="fw-bold py-1"></td>
                                        <td class="py-1"></td>
                                        <td class="p-0" colspan="2"></td>
                                        @php $currentRow++; @endphp
                                        <td class="py-1 text-center fw-bold">{{ $currentRow === 3 ? 'UNIT D.C NO' : '' }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            @if($jobCard->images->count() > 0)
                            <div class="row g-0 border-bottom" style="border-color: #eeeeee !important;">
                                @foreach($jobCard->images as $image)
                                    <div class="col text-center p-3 border-end">
                                        <div class="border rounded p-2" style="min-height: 120px; display: flex; align-items: center; justify-content: center;">
                                            <img src="{{ url($image->image) }}" alt="Swatch" style="max-width: 100%; max-height: 100px; object-fit: contain;">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @endif

                            <table class="table table-bordered table-sm mb-0 job-card-table" style="border-color: #eeeeee !important;">
                                <tbody>
                                    <tr class="text-center">
                                        <td class="fw-bold" style="width: 15%;">ART NO</td>
                                        @foreach($fabricDetails as $detail)
                                            @php
                                                $materialName = $artMaterialMap[$detail->art_no] ?? '';
                                            @endphp
                                            <td>
                                                {{ $detail->art_no }}
                                                @if($materialName)
                                                    <br><small class="text-muted" style="font-size: 0.7rem;">{{ $materialName }}</small>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr class="text-center">
                                        <td class="fw-bold">WIDTH</td>
                                        @foreach($fabricDetails as $detail)
                                            <td>{{ $detail->width ?: '-' }}</td>
                                        @endforeach
                                    </tr>
                                    <tr class="text-center">
                                        <td class="fw-bold">ISSUE METER</td>
                                        @foreach($fabricDetails as $detail)
                                            <td>{{ $detail->mtr ?: '-' }}</td>
                                        @endforeach
                                    </tr>
                                    <tr class="text-center">
                                        <td class="fw-bold">IN/OUT</td>
                                        @foreach($fabricDetails as $detail)
                                            <td>{{ $detail->in_out ?: '-' }}</td>
                                        @endforeach
                                    </tr>
                                    <tr class="text-center">
                                        <td class="fw-bold">N.PATTI</td>
                                        @foreach($fabricDetails as $detail)
                                            <td>{{ $detail->n_patti ?: '-' }}</td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table table-bordered table-sm mb-0 job-card-table" style="border-color: #eeeeee !important;">
                                <thead>
                                    <tr class="text-center">
                                        <th rowspan="2">ART NO</th>
                                        @if(count($activeFs) > 0)
                                            @php
                                                $fsMeter = $jobCard->sleeveMeters->where('sleeve_type', 'Full Sleeve')->first()->meter ?? null;
                                            @endphp
                                            <th colspan="{{ count($activeFs) }}">
                                                F/S @if($fsMeter) <br><small>({{ $fsMeter }} Mtr)</small> @endif
                                            </th>
                                        @endif
                                        @if(count($activeHs) > 0)
                                            @php
                                             $hsMeter = $jobCard->sleeveMeters->where('sleeve_type', 'Half Sleeve')->first()->meter ?? null;
                                            @endphp
                                            <th colspan="{{ count($activeHs) }}">
                                                H/S @if($hsMeter) <br><small>({{ $hsMeter }} Mtr)</small> @endif
                                            </th>
                                        @endif
                                        <th rowspan="2">TOTAL</th>
                                    </tr>
                                    <tr class="text-center">
                                        @foreach($activeFs as $s)
                                            <th>{{ $s }}</th>
                                        @endforeach
                                        @foreach($activeHs as $s)
                                            <th>{{ $s }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $fs_summary = array_fill_keys($activeFs, 0);
                                        $hs_summary = array_fill_keys($activeHs, 0);
                                        $grand_total = 0;
                                    @endphp
                                    @foreach($fabricDetails as $detail)
                                        @php
                                            $row_total = $detail->row_total ?: $detail->quantities->sum('total_qty');
                                            $grand_total += $row_total;

                                            foreach ($activeFs as $s) {
                                                $q = $detail->quantities->where('size', $s)->first();
                                                $fs_summary[$s] += $q ? $q->qty_fs : 0;
                                            }
                                            foreach ($activeHs as $s) {
                                                $q = $detail->quantities->where('size', $s)->first();
                                                $hs_summary[$s] += $q ? $q->qty_hs : 0;
                                            }
                                        @endphp
                                        <tr class="text-center">
                                            <td>
                                                @php
                                                    $materialName = $artMaterialMap[$detail->art_no] ?? '';
                                                @endphp
                                                {{ $detail->art_no }}
                                                @if($materialName)
                                                    <br><small class="text-muted" style="font-size: 0.65rem;">{{ $materialName }}</small>
                                                @endif
                                            </td>
                                            @foreach($activeFs as $s)
                                                @php 
                                                    $q = $detail->quantities->where('size', $s)->first(); 
                                                @endphp
                                                <td>
                                                    {{ ($q && $q->qty_fs > 0) ? (int) $q->qty_fs : '-' }}
                                                </td>
                                            @endforeach
                                            @foreach($activeHs as $s)
                                                @php 
                                                    $q = $detail->quantities->where('size', $s)->first(); 
                                                @endphp
                                                <td>
                                                    {{ ($q && $q->qty_hs > 0) ? (int) $q->qty_hs : '-' }}
                                                </td>
                                            @endforeach
                                            <td class="fw-bold">{{ $row_total ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="text-center fw-bold">
                                        <td>TOTAL</td>
                                        @foreach($activeFs as $s)
                                            <td>{{ $fs_summary[$s] ?: '-' }}</td>
                                        @endforeach
                                        @foreach($activeHs as $s)
                                            <td>{{ $hs_summary[$s] ?: '-' }}</td>
                                        @endforeach
                                        <td class="fw-bold">{{ $grand_total ?: '-' }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                            {{-- Authorised Signatures Section --}}
                            <div class="mt-4">
                                <style>
                                    .signature-table {
                                        width: 100%;
                                        border-collapse: collapse;
                                        border: 1px solid #000;
                                    }
                                    .signature-table th, .signature-table td {
                                        border: 1px solid #000;
                                        padding: 4px;
                                        font-size: 0.75rem;
                                        text-transform: uppercase;
                                    }
                                    .signature-table .bg-light {
                                        background-color: #f8f9fa;
                                    }
                                </style>
                                <table class="signature-table mb-0 text-center">
                                    <thead>
                                        <tr>
                                            <th colspan="7" class="fw-bold py-1 text-start">AUTHORISED SIGNATURES</th>
                                            <th style="width: 10%;" class="fw-bold py-1">TOTAL MTRS</th>
                                            <th style="width: 8%;" class="fw-bold py-1">{{ (int) $grand_total }}</th>
                                            <th style="width: 16%;" class="fw-bold py-1 text-start">REMARKS:</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <!-- Column 1 Headers -->
                                            <td class="bg-light fw-bold" style="width: 10%;">SECTION</td>
                                            <td class="bg-light fw-bold" style="width: 10%;">INCHARGE SIGN</td>
                                            <td class="bg-light fw-bold" style="width: 8%;">PLANING DATE</td>

                                            <!-- Column 2 Headers -->
                                            <td class="bg-light fw-bold" style="width: 10%;">SECTION</td>
                                            <td class="bg-light fw-bold" style="width: 10%;">INCHARGE SIGN</td>
                                            <td class="bg-light fw-bold" style="width: 8%;">DATE</td>

                                            <!-- Column 3 Headers -->
                                            <td class="bg-light fw-bold" style="width: 10%;">SECTION</td>
                                            <td class="bg-light fw-bold" style="width: 10%;">INCHARGE SIGN</td>
                                            <td class="bg-light fw-bold" style="width: 8%;">DATE</td>

                                            <!-- Remarks Grid Cell -->
                                            <td rowspan="8" class="p-0" style="vertical-align: top; width: 16%;">

                                            </td>
                                        </tr>
                                        <!-- Row 1 -->
                                        <tr>
                                            <td class="text-start">PURCHASE</td><td></td><td rowspan="2" class="fw-bold text-center">{{ $pDates['d1'] }}</td>
                                            <td class="text-start">READY</td><td></td><td rowspan="2" class="fw-bold text-center">{{ $pDates['d3'] }}</td>
                                            <td class="text-start">FINAL FINISH RECD</td><td rowspan="4"></td><td rowspan="4" class="fw-bold text-center">{{ $pDates['d6'] }}</td>
                                        </tr>
                                        <!-- Row 2 -->
                                        <tr>
                                            <td class="text-start">FABRIC STORE</td><td></td>
                                            <td class="text-start">READY STORE</td><td></td>
                                            <td class="text-start">IRONING</td>
                                        </tr>
                                        <!-- Row 3 -->
                                        <tr>
                                            <td class="text-start">CUTTING</td><td></td><td rowspan="2" class="fw-bold text-center">{{ $pDates['d2'] }}</td>
                                            <td class="text-start">ASSEMBLE</td><td></td><td rowspan="2" class="fw-bold text-center">{{ $pDates['d4'] }}</td>
                                            <td class="text-start">PACKING</td>
                                        </tr>
                                        <!-- Row 4 -->
                                        <tr>
                                            <td class="text-start">FUSING & LOGO</td><td></td>
                                            <td class="text-start">ASSEMBLE STORE</td><td></td>
                                            <td class="text-start">DELIVERY</td>
                                        </tr>
                                        <!-- Row 5 -->
                                        <tr>
                                            <td class="text-start">CUTTING SEND BY</td><td></td><td></td>
                                            <td class="text-start">KAJA & BUTTON</td><td></td><td rowspan="3" class="fw-bold text-center">{{ $pDates['d5'] }}</td>
                                            <td class="text-start">F.G STORE</td><td></td><td></td>
                                        </tr>
                                        <!-- Row 6 -->
                                        <tr>
                                            <td class="text-start">CUTTING RECD BY</td><td></td><td></td>
                                            <td class="text-start">TRIM & CHECK</td><td></td>
                                            <td></td><td></td><td></td>
                                        </tr>
                                        <!-- Row 7 -->
                                        <tr>
                                            <td class="text-start">UNIT INCHARGE</td><td></td><td></td>
                                            <td class="text-start">PRO SEND</td><td></td>
                                            <td></td><td></td><td></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="mt-2" style="font-size: 0.8rem; font-weight: bold;">
                                    MANDATORY : Please put your signature once you have completed your SYSTEM ENTRY
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

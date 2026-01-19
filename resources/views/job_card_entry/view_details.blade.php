@extends('layouts.common')
@section('title', 'View Job Card Entry - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
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
        $activeFs = array_values(array_unique($activeFs));
        sort($activeFs, SORT_NUMERIC);
        $activeHs = array_values(array_unique($activeHs));
        sort($activeHs, SORT_NUMERIC);

        if (empty($activeFs) && empty($activeHs)) {
             $activeFs = $allSizes;
        }
    @endphp
    <div class="row">
        <div class="col-lg-12 text-end">
            <a href="{{ route('job_card_entries.view_details_pdf', $jobCard->id) }}" class="btn btn-primary" target="_blank"><i class="ri ri-file-pdf-line me-1"></i> PDF</a>
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
                }
                @media (max-width: 991px) {
                    .job-card-table {
                        font-size: 0.55rem !important; /* Scaled down further */
                    }
                    .job-card-table .fw-bold {
                        font-size: 0.55rem !important;
                    }
                    .job-card-table td {
                        padding: 1px 2px !important; /* Minimal padding */
                    }
                }
                @media (max-width: 575px) {
                    .job-card-table {
                        font-size: 0.45rem !important; /* Ultra-compact for small phones */
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
                    <div class="text-center py-2 border-bottom" style="border-color: #eeeeee !important;">
                        <table class="table table-bordered table-sm mb-0 job-card-table" style="border-color: #eeeeee !important;">
                            <tr>
                                <td class="fw-bold" style="font-size: 0.9rem; letter-spacing: 1px;">NACHIAS FASHION PVT LTD</td>
                            </tr>
                        </table>
                        <table class="table table-bordered table-sm mb-0 job-card-table" style="border-color: #eeeeee !important;">
                            <tr>
                                <td style="width:20%;" class="fw-bold">JOB CARD</td>
                                <td style="width:60%;" class="fw-bold text-center">{{ strtoupper($jobCard->brand->brand_name ?? '') }}</td>
                                <td style="width:10%;"></td>
                                <td style="width:10%;">MARK CHECKER</td>
                            </tr>
                        </table>
                    </div>

                    <table class="table table-bordered table-sm mb-0 job-card-table" style="border-color: #eeeeee !important;">
                        <tbody>
                            {{-- Row 1 --}}
                            <tr>
                                <td class="fw-bold py-1" style="width: 9%;">CUTTING NO</td>
                                <td class="py-1" style="width: 11%;">{{ $jobCard->job_card_no }}</td>
                                <td class="fw-bold py-1" style="width: 6%;">FIT</td>
                                <td class="py-1 text-center" style="width: 18%;">{{ strtoupper($jobCard->fit) ?: '-' }}</td>
                                <td class="fw-bold py-1" style="width: 6%;">CUFF</td>
                                <td class="py-1 text-center" style="width: 10%;">{{ strtoupper($jobCard->cuff_type) ?: '-' }}</td>
                                <td class="fw-bold py-1" style="width: 12%;">CUTTING MASTER</td>
                                <td class="py-1" style="width: 18%;">{{ $jobCard->cuttingMaster->name ?? '' }}</td>
                                <td class="fw-bold py-1" style="width: 10%;"></td>
                            </tr>

                            {{-- Row 2 --}}
                            <tr>
                                <td class="fw-bold py-1">F.ISSUE DATE</td>
                                <td class="py-1">{{ $jobCard->job_card_date ? date('d-m-Y', strtotime($jobCard->job_card_date)) : '' }}</td>
                                <td class="fw-bold py-1">N.PATTI</td>
                                <td class="py-1 text-center">{{ strtoupper($jobCard->patti_type) ?: '-' }}</td>
                                <td class="fw-bold py-1">POCKET</td>
                                <td class="py-1 text-center">{{ strtoupper($jobCard->pocket_type) ?: '-' }}</td>
                                <td class="fw-bold py-1">CUTTING DATE</td>
                                <td class="py-1">{{ $jobCard->cutting_date ? date('d-m-Y', strtotime($jobCard->cutting_date)) : '' }}</td>
                                <td class="">H.O.D.C NO</td>
                            </tr>

                            {{-- Row 3 --}}
                            <tr>
                                <td class="fw-bold py-1">DELIVERY DATE</td>
                                <td class="py-1">{{ $jobCard->delivery_date ? date('d-m-Y', strtotime($jobCard->delivery_date)) : '' }}</td>
                                <td class="fw-bold py-1">COLLAR</td>
                                <td class="py-1 text-center">{{ strtoupper($jobCard->collar_type) ?: '-' }}</td>
                                <td class="fw-bold py-1">BOT.CUT</td>
                                <td class="py-1 text-center">{{ strtoupper($jobCard->bottom_cut) ?: '-' }}</td>
                                <td class="fw-bold py-1">CUTTING ISSUE UNIT</td>
                                <td class="py-1">{{ $jobCard->cutting_issue_unit ?: '' }}</td>
                                <td class="fw-bold py-1"></td>
                            </tr>

                            {{-- Row 4 --}}
                            <tr>
                                <td class="fw-bold py-1">WASHING</td>
                                <td class="py-1">{{ $jobCard->washing ?: 'NO' }}</td>
                                <td colspan="4" class="text-center fw-bold py-1" style="font-size: 0.75rem; border-bottom: 2px solid #eeeeee;">CUTTING SIZE RATIO</td>
                                <td colspan="2" class="text-center fw-bold py-1" style="font-size: 0.75rem; border-bottom: 2px solid #eeeeee;">CUTTING MARK AND LAY</td>  
                                <td colspan="2" class="text-center fw-bold py-1" style="font-size: 0.75rem; border-bottom: 2px solid #eeeeee;">H.O.D.C DATE</td>
                            </tr>

                            {{-- Row 5 --}}
                            <tr>
                                <td class="fw-bold py-1">WIDTH</td>
                                <td class="py-1">{{ $jobCard->width ?: '-' }}</td>
                                <td class="fw-bold py-1 text-center">SIZE</td>
                                <td colspan="3" class="p-0">
                                    <table class="table table-bordered mb-0 job-card-table" style="border: 1px solid #eeeeee;">
                                        <tr class="text-center fw-bold" style="font-size: 0.8rem;">
                                            @php $sizes = $allSizes; @endphp
                                            @foreach($sizes as $size)
                                                <td class="py-1" style="width: 14.28%; border: 1px solid #eeeeee;">{{ $size }}</td>
                                            @endforeach
                                        </tr>
                                    </table>
                                </td>
                                <td class="p-0" colspan="2">
                                    <div class="row g-0">
                                        <div class="col-6 fw-bold text-center py-1 border-end" style="font-size: 0.7rem;">SIZE</div>
                                        <div class="col-6 fw-bold text-center py-1" style="font-size: 0.7rem;">MARK</div>
                                    </div>
                                </td>
                            </tr>

                            {{-- Row 6 --}}
                            <tr>
                                <td class="fw-bold py-1">MRP</td>
                                <td class="py-1">{{ $jobCard->mrp ?: '' }}</td>
                                <td class="fw-bold py-1 text-center">QTY - F/S</td>
                                <td colspan="3" class="p-0">
                                    <table class="table table-bordered mb-0 job-card-table" style="border: none;">
                                        <tr class="text-center" style="font-size: 0.8rem;">
                                            @foreach($sizes as $size)
                                                @php $ratio = $jobCard->cuttingSizeRatios->where('size', $size)->first(); @endphp
                                                <td class="py-1" style="width: 14.28%; border: 1px solid #eeeeee;">
                                                    {{ ($ratio && $ratio->qty_fs > 0) ? (int)$ratio->qty_fs : '-' }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    </table>
                                </td>
                                <td class="py-1" colspan="2"></td>
                                <td class="py-1"></td>
                            </tr>

                            {{-- Row 7 --}}
                            <tr>
                                <td class="fw-bold py-1">F/S</td>
                                <td class="py-1">{{ $jobCard->price_fs ?: '' }}</td>
                                <td class="fw-bold py-1 text-center">QTY - H/S</td>
                                <td colspan="3" class="p-0">
                                    <table class="table table-bordered mb-0 job-card-table" style="border: none;">
                                        <tr class="text-center" style="font-size: 0.8rem;">
                                            @foreach($sizes as $size)
                                                @php $ratio = $jobCard->cuttingSizeRatios->where('size', $size)->first(); @endphp
                                                <td class="py-1" style="width: 14.28%; border: 1px solid #eeeeee;">
                                                    {{ ($ratio && $ratio->qty_hs > 0) ? (int)$ratio->qty_hs : '-' }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    </table>
                                </td>
                                <td class="py-1" colspan="2"></td>
                                <td class="py-1"></td>
                            </tr>

                            {{-- Row 8 --}}
                            <tr>
                                <td class="fw-bold py-1">H/S</td>
                                <td class="py-1">{{ $jobCard->price_hs ?: '' }}</td>
                                <td class="p-0" colspan="2"></td>
                                <td class="py-1"></td>
                            </tr>
                        </tbody>
                    </table>

                    @if($jobCard->images->count() > 0)
                    <div class="row g-0 border-bottom" style="border-color: #eeeeee !important;">
                        @foreach($jobCard->images as $image)
                            <div class="col text-center p-3 border-end">
                                <div class="border rounded p-2" style="min-height: 120px; display: flex; align-items: center; justify-content: center;">
                                    <img src="{{ asset($image->image) }}" alt="Swatch" style="max-width: 100%; max-height: 100px; object-fit: contain;">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif

                    <table class="table table-bordered table-sm mb-0 job-card-table" style="border-color: #eeeeee !important;">
                        <thead>
                            <tr class="text-center">
                                @foreach($jobCard->fabricDetails as $detail)
                                    <th class="fw-bold">ART NO</th>
                                    <th>{{ $detail->art_no }}</th>
                                @endforeach
                                @for($i = count($jobCard->fabricDetails); $i < 5; $i++)
                                    <th class="fw-bold">ART NO</th>
                                    <th></th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-center">
                                @foreach($jobCard->fabricDetails as $detail)
                                    <td class="fw-bold">WIDTH</td>
                                    <td>{{ $detail->width ?: '-' }}</td>
                                @endforeach
                                @for($i = count($jobCard->fabricDetails); $i < 5; $i++)
                                    <td class="fw-bold">WIDTH</td>
                                    <td></td>
                                @endfor
                            </tr>
                            <tr class="text-center">
                                @foreach($jobCard->fabricDetails as $detail)
                                    <td class="fw-bold">M/B.M</td>
                                    <td>{{ $detail->mtr ?: '-' }}</td>
                                @endforeach
                                @for($i = count($jobCard->fabricDetails); $i < 5; $i++)
                                    <td class="fw-bold">M/B.M</td>
                                    <td></td>
                                @endfor
                            </tr>
                            <tr class="text-center">
                                @foreach($jobCard->fabricDetails as $detail)
                                    <td class="fw-bold">IN/OUT</td>
                                    <td>{{ $detail->in_out ?: '-' }}</td>
                                @endforeach
                                @for($i = count($jobCard->fabricDetails); $i < 5; $i++)
                                    <td class="fw-bold">IN/OUT</td>
                                    <td></td>
                                @endfor
                            <tr class="text-center">
                                @foreach($jobCard->fabricDetails as $detail)
                                    <td class="fw-bold">N.PATTI</td>
                                    <td>{{ $detail->n_patti ?: '-' }}</td>
                                @endforeach
                                @for($i = count($jobCard->fabricDetails); $i < 5; $i++)
                                    <td class="fw-bold">N.PATTI</td>
                                    <td></td>
                                @endfor
                            </tr>
                        </tbody>
                    </table>

                    <table class="table table-bordered table-sm mb-0 job-card-table" style="border-color: #eeeeee !important;">
                        <thead>
                            <tr class="text-center">
                                <th rowspan="2">ART NO</th>
                                @if(count($activeFs) > 0)
                                    <th colspan="{{ count($activeFs) }}">F/S</th>
                                @endif
                                @if(count($activeHs) > 0)
                                    <th colspan="{{ count($activeHs) }}">H/S</th>
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
                            @foreach($jobCard->fabricDetails as $detail)
                                @php
                                    $fs_row_total = 0;
                                    foreach($activeFs as $s) {
                                        $val = (int)($detail->{'fs_' . $s} ?? 0);
                                        $fs_row_total += $val;
                                        $fs_summary[$s] += $val;
                                    }
                                    
                                    $hs_row_total = 0;
                                    foreach($activeHs as $s) {
                                        $val = (int)($detail->{'hs_' . $s} ?? 0);
                                        $hs_row_total += $val;
                                        $hs_summary[$s] += $val;
                                    }

                                    $row_total = $fs_row_total + $hs_row_total;
                                    $grand_total += $row_total;
                                @endphp
                                <tr class="text-center">
                                    <td>{{ $detail->art_no }}</td>
                                    @foreach($activeFs as $s)
                                        <td>{{ $detail->{'fs_' . $s} ?: '-' }}</td>
                                    @endforeach
                                    @foreach($activeHs as $s)
                                        <td>{{ $detail->{'hs_' . $s} ?: '-' }}</td>
                                    @endforeach
                                    <td class="fw-bold">{{ $row_total ?: '-' }}</td>
                                </tr>
                            @endforeach
                            {{-- Fill empty rows if needed to maintain layout --}}
                            @for($i = count($jobCard->fabricDetails); $i < 6; $i++)
                                <tr class="text-center">
                                    <td>&nbsp;</td>
                                    @foreach($activeFs as $s) <td>-</td> @endforeach
                                    @foreach($activeHs as $s) <td>-</td> @endforeach
                                    <td>-</td>
                                </tr>
                            @endfor
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

                    <div class="row g-0 border border-top-0" style="border-color: #eeeeee !important;">
                        <div class="col-10">
                            <div class="bg-light text-center fw-bold py-1 border-bottom" style="font-size: 0.8rem; border-color: #eeeeee !important;">AUTHORISED SIGNATURES</div>
                            <table class="table mb-0 job-card-table" style="border: none;">
                                <tbody>
                                    @php
                                        $allOps = $jobCard->operations->values();
                                        $totalRows = 4;
                                        $colsPerRow = 3;
                                    @endphp
                                    @for($i = 0; $i < $totalRows; $i++)
                                        <tr style="border-bottom: 1px solid #eeeeee;">
                                            @for($j = 0; $j < $colsPerRow; $j++)
                                                @php 
                                                    $idx = ($i * $colsPerRow) + $j;
                                                    $op = $allOps[$idx] ?? null;
                                                @endphp
                                                <td class="fw-bold py-1" style="width: 6%; font-size: 0.65rem; border-right: 1px solid #eeeeee; @if($j == 0) border-left: none; @endif">
                                                    @if($op)
                                                        DATE
                                                        <div class="fw-normal" style="font-size: 0.65rem;">
                                                            {{ $op->assigned_date ? date('d-m-Y', strtotime($op->assigned_date)) : '' }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="py-1" style="width: 19%; font-size: 0.75rem; vertical-align: top; height: 50px; border-right: 1px solid #eeeeee; @if($j == $colsPerRow - 1) border-right: none; @endif">
                                                    @if($op)
                                                        <div class="fw-bold text-center mb-1" style="font-size: 0.7rem; border-bottom: 1px solid #eeeeee;">
                                                            {{ $op->operationStage->operation_stage_name ?? '' }}
                                                        </div>
                                                        <div class="text-center" style="font-size: 0.65rem;">
                                                            {{ $op->employee->name ?? '' }}
                                                        </div>
                                                    @endif
                                                </td>
                                            @endfor
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                        <div class="col-2 border-start" style="border-color: #eeeeee !important;">
                            <div class="bg-light text-center fw-bold py-1 border-bottom" style="font-size: 0.8rem; border-color: #eeeeee !important;">REMARKS</div>
                            <div class="p-2" style="font-size: 0.75rem; min-height: 200px; white-space: pre-wrap;">{{ $jobCard->remarks }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

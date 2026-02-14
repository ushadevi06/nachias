@extends('layouts.common')
@section('title', 'View Job Card Entry - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    @php
        $allSizes = ['36', '38', '40', '42', '44','46'];
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

                                <!-- URGENT (Full Proper Cell) -->
                                <td style="width:12%; font-weight:bold; background:#2f6fae; color:#fff; font-size:16px;">
                                    URGENT
                                </td>
                            </tr>

                            <tr>
                                <!-- SEASON VALUE -->
                                <td style="font-weight:bold; font-size:16px;">B28</td>

                                <!-- STYLE NAME -->
                                <td colspan="4" style="font-weight:bold; font-size:18px;">
                                    CASINO FORMAL
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
                                <td class="fw-bold p-3 vertical-align:middle;">H.O/D.C/NO</td>
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
                                <td colspan="2" class="text-center fw-bold p-3" style="font-size: 0.9rem; border-bottom: 2px solid #eeeeee;">H.O.D.C DATE</td>
                            </tr>

                            {{-- Row 5 --}}
                            <tr>
                                <td class="fw-bold p-3" style="font-size: 12px;">CAD MARK WITDTH</td>
                                <td class="p-3">{{ $jobCard->width ?: '-' }}</td>
                                <td class="fw-bold p-3 text-center">SIZE</td>
                                <td colspan="3" class="p-0">
                                    <table class="table mb-0 job-card-table">
                                        <tr class="text-center fw-bold" style="font-size: 0.8rem;">
                                            @php $sizes = $allSizes; @endphp
                                            @foreach($sizes as $size)
                                                <td class="p-3" style="width: 14.28%; border-right:1px solid #eeeeee;">{{ $size }}</td>
                                            @endforeach
                                        </tr>
                                    </table>
                                </td>
                                <td class="p-0" colspan="2">
                                    <div class="row g-0">
                                        <div class="col-6 fw-bold text-center p-3 border-end" style="font-size: 0.7rem;">SIZE</div>
                                        <div class="col-6 fw-bold text-center p-3" style="font-size: 0.7rem;">MARK</div>
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
                                    <img src="{{ url($image->image) }}" alt="Swatch" style="max-width: 100%; max-height: 100px; object-fit: contain;">
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
                            @foreach($jobCard->fabricDetails as $detail)
                                @php
                                    $row_total = $detail->row_total ?: $detail->quantities->sum('total_qty');
                                    $grand_total += $row_total;
                                    
                                    foreach($activeFs as $s) {
                                        $q = $detail->quantities->where('size', $s)->first();
                                        $fs_summary[$s] += $q ? $q->qty_fs : 0;
                                    }
                                    foreach($activeHs as $s) {
                                        $q = $detail->quantities->where('size', $s)->first();
                                        $hs_summary[$s] += $q ? $q->qty_hs : 0;
                                    }
                                @endphp
                                <tr class="text-center">
                                    <td>{{ $detail->art_no }}</td>
                                    @foreach($activeFs as $s)
                                        @php 
                                            $q = $detail->quantities->where('size', $s)->first(); 
                                            $cons = $detail->consumptions->where('size', $s)->first();
                                        @endphp
                                        <td>
                                            {{ ($q && $q->qty_fs > 0) ? (int)$q->qty_fs : '-' }}
                                            @if($cons && $cons->fs_cons > 0)
                                                <br><small class="text-muted" style="font-size: 0.65rem;">{{ $cons->fs_cons }}m</small>
                                            @endif
                                        </td>
                                    @endforeach
                                    @foreach($activeHs as $s)
                                        @php 
                                            $q = $detail->quantities->where('size', $s)->first(); 
                                            $cons = $detail->consumptions->where('size', $s)->first();
                                        @endphp
                                        <td>
                                            {{ ($q && $q->qty_hs > 0) ? (int)$q->qty_hs : '-' }}
                                            @if($cons && $cons->hs_cons > 0)
                                                <br><small class="text-muted" style="font-size: 0.65rem;">{{ $cons->hs_cons }}m</small>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="fw-bold">{{ $row_total ?: '-' }}</td>
                                </tr>
                            @endforeach
                            {{-- Fill empty rows if needed to maintain layout --}}
                            @for($i = count($jobCard->fabricDetails); $i < 6; $i++)
                                <tr class="text-center">
                                    <td>&nbsp;</td>
                                    @foreach($activeFs as $s) <td></td> @endforeach
                                    @foreach($activeHs as $s) <td></td> @endforeach
                                    <td></td>
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
                                <thead>
                                    <tr class="text-center fw-bold" style="font-size: 0.7rem; border-bottom: 1px solid #eeeeee;">
                                        <td colspan="2" style="border-right: 1px solid #eeeeee;">AUTHORISED SIGNATURES</td>
                                        <td colspan="2" style="border-right: 1px solid #eeeeee;">CUTTING RECEIVED BY</td>
                                        <td colspan="2" style="border-right: 1px solid #eeeeee;">ASSAMBILE</td>
                                        <td colspan="2">TRIMMING & CHECKING</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="border-bottom: 1px solid #eeeeee;">
                                        <td class="fw-bold py-1" style="width: 5%; font-size: 0.65rem; border-right: 1px solid #eeeeee;">DATE</td>
                                        <td class="py-1 text-center fw-bold" style="width: 20%; font-size: 0.65rem; border-right: 1px solid #eeeeee;">FABRIC INCHARGE</td>
                                        <td class="fw-bold py-1" style="width: 5%; font-size: 0.65rem; border-right: 1px solid #eeeeee;">DATE</td>
                                        <td class="py-1 text-center fw-bold" style="width: 20%; font-size: 0.65rem; border-right: 1px solid #eeeeee;">UNIT INCHARGE</td>
                                        <td class="fw-bold py-1" style="width: 5%; font-size: 0.65rem; border-right: 1px solid #eeeeee;">DATE</td>
                                        <td class="py-1 text-center fw-bold" style="width: 20%; font-size: 0.65rem; border-right: 1px solid #eeeeee;">PRODUCTION UNIT SEND BY</td>
                                        <td class="fw-bold py-1" style="width: 5%; font-size: 0.65rem; border-right: 1px solid #eeeeee;">DATE</td>
                                        <td class="py-1 text-center fw-bold" style="width: 20%; font-size: 0.65rem;">IRONING</td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #eeeeee;">
                                        <td class="fw-bold py-1" style="width: 5%; font-size: 0.65rem; border-right: 1px solid #eeeeee;">DATE</td>
                                        <td class="py-1 text-center fw-bold" style="width: 20%; font-size: 0.65rem; border-right: 1px solid #eeeeee;">FABRIC ISSUED BY</td>
                                        <td class="fw-bold py-1" style="width: 5%; font-size: 0.65rem; border-right: 1px solid #eeeeee;">DATE</td>
                                        <td class="py-1 text-center fw-bold" style="width: 20%; font-size: 0.65rem; border-right: 1px solid #eeeeee;">READY SECTION</td>
                                        <td class="fw-bold py-1" style="width: 5%; font-size: 0.65rem; border-right: 1px solid #eeeeee;">DATE</td>
                                        <td class="py-1 text-center fw-bold" style="width: 20%; font-size: 0.65rem; border-right: 1px solid #eeeeee;">H.O RECEIVED BY</td>
                                        <td class="fw-bold py-1" style="width: 5%; font-size: 0.65rem; border-right: 1px solid #eeeeee;">DATE</td>
                                        <td class="py-1 text-center fw-bold" style="width: 20%; font-size: 0.65rem;">PACKING & DELIVERY</td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #eeeeee;">
                                        <td class="fw-bold py-1" style="width: 5%; font-size: 0.65rem; border-right: 1px solid #eeeeee;">DATE</td>
                                        <td class="py-1 text-center fw-bold" style="width: 20%; font-size: 0.65rem; border-right: 1px solid #eeeeee;">CUTTING SUPERVISOR</td>
                                        <td class="fw-bold py-1" style="width: 5%; font-size: 0.65rem; border-right: 1px solid #eeeeee;">DATE</td>
                                        <td class="py-1 text-center fw-bold" style="width: 20%; font-size: 0.65rem; border-right: 1px solid #eeeeee;">READY STORE</td>
                                        <td class="fw-bold py-1" style="width: 5%; font-size: 0.65rem; border-right: 1px solid #eeeeee;">DATE</td>
                                        <td class="py-1 text-center fw-bold" style="width: 20%; font-size: 0.65rem; border-right: 1px solid #eeeeee;">KAJA & BUTTON</td>
                                        <td class="fw-bold py-1" style="width: 5%; font-size: 0.65rem; border-right: 1px solid #eeeeee;">DATE</td>
                                        <td class="py-1 text-center fw-bold" style="width: 20%; font-size: 0.65rem;">F.G STORE</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold py-1" style="width: 5%; font-size: 0.65rem; border-right: 1px solid #eeeeee;">DATE</td>
                                        <td class="py-1 text-center fw-bold" style="width: 20%; font-size: 0.65rem; border-right: 1px solid #eeeeee;">CUTTING SEND BY</td>
                                        <td colspan="6"></td>
                                    </tr>
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

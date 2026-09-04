@extends('layouts.common')
@section('title', 'Additional Batch View - Job Card ' . $jobCard->job_card_no . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    @php
        $isCanvas = false;
        if ($jobCard->brand && in_array(strtoupper(trim($jobCard->brand->brand_name)), ['CANVAS ACCESSORIES', 'CANVAS ACCESSORIES (CAS)'])) {
            $isCanvas = true;
        }

        $allSizes = $sizes ?? ['36', '38', '40', '42', '44', '46'];
        $currentAdditional = intval($jobCard->additional_qty ?? 0);
        $initialPlanned = max(0, intval($jobCard->grand_total_qty) - $currentAdditional);
    @endphp

    <!-- Page Header & Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="mb-1 text-primary d-flex align-items-center gap-2">
                <i class="ri ri-file-list-3-line"></i> Additional Batch Details - Batch #{{ $batchIndex ?? 1 }}
            </h4>
            <div class="text-muted small">
                Job Card Number: <strong class="text-dark">{{ $jobCard->job_card_no }}</strong> &nbsp;|&nbsp; 
                Date: {{ $jobCard->job_card_date ? date('d-m-Y', strtotime($jobCard->job_card_date)) : '-' }} &nbsp;|&nbsp; 
                Plant: {{ $jobCard->serviceProvider->name ?? '-' }}
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('job_card_entries.additional_qty_history', $jobCard->id) }}" class="btn btn-secondary d-flex align-items-center gap-1 shadow-sm">
                <i class="ri ri-arrow-left-line"></i> Back to History
            </a>
            <a href="{{ route('job_card_entries.view-item', $jobCard->id) }}" class="btn btn-outline-primary d-flex align-items-center gap-1">
                <i class="ri ri-list-check-2"></i> Issue Item
            </a>
            <a href="{{ url('job_card_entries/view/' . $jobCard->id) }}" class="btn btn-outline-secondary d-flex align-items-center gap-1">
                <i class="ri ri-eye-line"></i> View Job Card
            </a>
        </div>
    </div>

    @php
        $batchTotalQty = $batchGroup->sum('total_qty');
        $batchFsQty = $batchGroup->sum('fs_qty');
        $batchHsQty = $batchGroup->sum('hs_qty');
        $batchMtr = $batchGroup->sum('mtr');
        $totalBatchesCount = $jobCard->fabricDetails->where('is_additional', 1)->groupBy(function($item) {
            return $item->additional_batch_no ?? ($item->created_at ? $item->created_at->format('Y-m-d H:i') : $item->id);
        })->count();
    @endphp

    @include('flash_messages')

    <!-- Batch Details Header Card -->
    <div class="card mb-4 shadow-sm border-0 bg-white">
        <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div class="badge bg-label-primary p-2 rounded-circle">
                    <i class="ri ri-file-list-3-line fs-3 text-primary"></i>
                </div>
                <div>
                    <h5 class="mb-0 text-dark fw-bold">
                        Batch #{{ $batchIndex ?? 1 }} Details
                    </h5>
                    <span class="small text-muted">
                        Added on: <strong>{{ $batch->created_at ? $batch->created_at->format('d-m-Y h:i A') : '-' }}</strong> &nbsp;|&nbsp;
                        @if($batchGroup->count() == 1)
                            Art No: <strong>{{ $batch->art_no ?? '-' }}</strong> &nbsp;|&nbsp;
                        @else
                            Art Nos: <strong>{{ $batchGroup->pluck('art_no')->implode(', ') }}</strong> ({{ $batchGroup->count() }} Fabrics) &nbsp;|&nbsp;
                        @endif
                        Extra Pieces: <strong class="text-success">+{{ number_format($batchTotalQty, 0) }} pcs</strong>
                    </span>
                </div>
            </div>
            <div>
                @if($isPosted)
                    <span class="badge bg-label-primary fs-6 px-3 py-2 border">
                        <i class="ri ri-checkbox-circle-line me-1"></i> Warehouse Posted
                    </span>
                @else
                    <a href="{{ url('job_card_entries/additional-qty/' . $jobCard->id . '?batch_id=' . $batch->id) }}" class="btn btn-outline-warning btn-sm d-flex align-items-center gap-1">
                        <i class="ri ri-edit-line"></i> Edit Batch
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Summary Statistics Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 bg-white">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small fw-bold text-uppercase mb-1">Base Planned Qty</div>
                    <div class="fs-4 fw-bold text-dark">{{ number_format($initialPlanned, 0) }} <small class="text-muted fs-6">pcs</small></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 bg-white">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small fw-bold text-uppercase mb-1">This Batch Qty</div>
                    <div class="fs-4 fw-bold text-primary">+{{ number_format($batchTotalQty, 0) }} <small class="text-muted fs-6">pcs</small></div>
                    @if(!$isCanvas)
                        <div class="small text-muted mt-1">(F/S: {{ $batchFsQty }}, H/S: {{ $batchHsQty }})</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 bg-white">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small fw-bold text-uppercase mb-1">Batch Issued Fabric</div>
                    <div class="fs-4 fw-bold text-primary">{{ number_format($batchMtr, 2) }} <small class="text-muted fs-6">Mtr</small></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100 bg-white">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small fw-bold text-uppercase mb-1">Total Additional Batches</div>
                    <div class="fs-4 fw-bold text-dark">{{ $totalBatchesCount }} <small class="text-muted fs-6">batches</small></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 1. Fabric Details Section -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <div class="card-header-box mb-3 border-bottom pb-2 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 text-dark">1. Fabric Specification & Lay Marks</h4>
                @if($batchGroup->count() > 1)
                    <span class="badge bg-label-primary px-3 py-2 fs-6">
                        <i class="ri ri-layout-column-line me-1"></i> {{ $batchGroup->count() }} Fabrics (Side-by-Side)
                    </span>
                @endif
            </div>

            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle" id="fabric-details-table" style="min-width: {{ count($batchGroup) * 1100 }}px; width: max-content; table-layout: fixed;">
                    <thead>
                        <tr id="fabric-details-head">
                            @foreach($batchGroup as $idx => $bf)
                                <th colspan="2" class="bg-light p-3" style="width: 1100px; min-width: 1100px; max-width: 1100px;">
                                    <label class="small text-primary fw-bold text-uppercase d-block mb-2">IMAGE (Fabric #{{ $idx + 1 }}: {{ $bf->art_no }})</label>
                                    @if($bf->grn_image && file_exists(public_path($bf->grn_image)))
                                        <a href="{{ asset($bf->grn_image) }}" target="_blank">
                                            <img src="{{ asset($bf->grn_image) }}" alt="Fabric Image" class="img-thumbnail rounded shadow-sm" style="max-height: 90px;">
                                        </a>
                                    @else
                                        <span class="badge bg-label-secondary px-3 py-2"><i class="ri ri-image-line me-1"></i> No Image</span>
                                    @endif
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody id="fabric-details-body">
                        <!-- ART NO Row -->
                        <tr>
                            @foreach($batchGroup as $idx => $bf)
                                <td class="fw-bold bg-light" style="width: 160px; min-width: 160px; max-width: 160px;">ART NO</td>
                                <td class="p-2" style="width: 940px; min-width: 940px; max-width: 940px;">
                                    <div class="form-control form-control-sm bg-light fw-bold text-center text-primary fs-6">{{ $bf->art_no ?? '-' }}</div>
                                </td>
                            @endforeach
                        </tr>

                        <!-- WIDTH Row -->
                        <tr>
                            @foreach($batchGroup as $idx => $bf)
                                <td class="fw-bold bg-light" style="width: 160px; min-width: 160px; max-width: 160px;">WIDTH</td>
                                <td class="p-2" style="width: 940px; min-width: 940px; max-width: 940px;">
                                    <div class="form-control form-control-sm bg-light text-center">{{ $bf->fabricSize->width ?? ($bf->width ?? '-') }}</div>
                                </td>
                            @endforeach
                        </tr>

                        <!-- ISSUED METERS Row -->
                        <tr>
                            @foreach($batchGroup as $idx => $bf)
                                <td class="fw-bold bg-light" style="width: 160px; min-width: 160px; max-width: 160px;">ISSUED METERS</td>
                                <td class="p-2" style="width: 940px; min-width: 940px; max-width: 940px;">
                                    <div class="form-control form-control-sm bg-light text-center fw-bold text-primary">{{ number_format($bf->mtr, 2) }} Mtr</div>
                                </td>
                            @endforeach
                        </tr>

                        <!-- IN/OUT Row -->
                        <tr>
                            @foreach($batchGroup as $idx => $bf)
                                <td class="fw-bold bg-light" style="width: 160px; min-width: 160px; max-width: 160px;">IN/OUT</td>
                                <td class="p-2" style="width: 940px; min-width: 940px; max-width: 940px;">
                                    <div class="form-control form-control-sm bg-light text-center">{{ $bf->in_out ?? 'NO' }}</div>
                                </td>
                            @endforeach
                        </tr>

                        <!-- N.PATTI Row -->
                        <tr>
                            @foreach($batchGroup as $idx => $bf)
                                <td class="fw-bold bg-light" style="width: 160px; min-width: 160px; max-width: 160px;">N.PATTI</td>
                                <td class="p-2" style="width: 940px; min-width: 940px; max-width: 940px;">
                                    <div class="form-control form-control-sm bg-light text-center">{{ $bf->n_patti ?? 'WHITE' }}</div>
                                </td>
                            @endforeach
                        </tr>

                        <!-- CONSUMPTION MTR Row -->
                        <tr>
                            @foreach($batchGroup as $idx => $bf)
                                <td class="fw-bold bg-light" style="width: 160px; min-width: 160px; max-width: 160px; vertical-align: middle;">
                                    CONSUMPTION<br>
                                    <span class="badge bg-secondary">MTR</span>
                                </td>
                                <td class="p-0" style="width: 940px; min-width: 940px; max-width: 940px;">
                                    <table class="table table-bordered table-sm mb-0 align-middle text-center">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 70px; min-width: 70px;">MARK</th>
                                                <th style="width: 390px; min-width: 390px;">SIZES</th>
                                                <th style="width: 120px; min-width: 120px;">SLEEVE</th>
                                                <th style="width: 180px; min-width: 180px;">LAY MARK METER</th>
                                                <th style="width: 180px; min-width: 180px;">NO. OF LAY</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($bf->layMarks && $bf->layMarks->count() > 0)
                                                @foreach($bf->layMarks as $lmIdx => $lm)
                                                    @php
                                                        $lmSizes = is_array($lm->sizes) ? $lm->sizes : (is_string($lm->sizes) ? json_decode($lm->sizes, true) ?? explode(',', $lm->sizes) : []);
                                                    @endphp
                                                    <tr>
                                                        <td class="fw-bold">{{ $lm->mark_no ?? ($lmIdx + 1) }}</td>
                                                        <td>
                                                            <div class="d-flex flex-wrap gap-1 justify-content-center">
                                                                @foreach($lmSizes as $sz)
                                                                    <span class="badge bg-light text-dark border px-2 py-1">{{ trim($sz) }}</span>
                                                                @endforeach
                                                            </div>
                                                        </td>
                                                        <td><span class="badge bg-label-info">{{ $lm->sleeve_type ?? '-' }}</span></td>
                                                        <td class="fw-bold">{{ $lm->lay_mark_meter ?? '0.00' }}</td>
                                                        <td class="fw-bold">{{ $lm->no_of_lay ?? '0' }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-muted py-2 small">No lay marks configured</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 2. Production Stages Section -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <div class="card-header-box mb-3 border-bottom pb-2">
                <h4 class="mb-0 text-dark">2. Production Stages & Operations</h4>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle text-center mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 20%;">STAGE</th>
                            <th style="width: 22%;">ISSUE UNIT (PLANT)</th>
                            <th style="width: 10%;">RATE</th>
                            <th style="width: 12%;">ISSUE DATE</th>
                            <th style="width: 12%;">DEADLINE DATE</th>
                            <th style="width: 14%;">REMARKS</th>
                            <th style="width: 10%;">TASK STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batchOps as $stIdx => $op)
                            @php
                                $stageId = $op->operation_stage_id ?? null;
                                $taskInfo = $stageTaskStatus[$stageId] ?? null;
                            @endphp
                            <tr>
                                <td class="fw-bold text-dark text-start ps-3">{{ $op->operationStage->operation_stage_name ?? '-' }}</td>
                                <td>{{ $op->serviceProvider->name ?? ($op->employee->name ?? '-') }}</td>
                                <td class="fw-bold text-end pe-3">₹{{ number_format($op->rate ?? 0, 2) }}</td>
                                <td>{{ $op->assigned_date ? date('d-m-Y', strtotime($op->assigned_date)) : '-' }}</td>
                                <td>{{ $op->deadline_date ? date('d-m-Y', strtotime($op->deadline_date)) : '-' }}</td>
                                <td class="text-muted small">{{ $op->remarks ?: '-' }}</td>
                                <td>
                                    @if($taskInfo)
                                        <span class="badge bg-primary" title="Task No: {{ $taskInfo['task_no'] }}">
                                            <i class="ri ri-task-line me-1"></i> #{{ $taskInfo['task_no'] }}
                                        </span>
                                    @else
                                        <span class="badge bg-label-secondary">Not Assigned</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-muted py-3">No production stages configured.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 3. Article Quantity Matrix -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <div class="card-header-box d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                <h4 class="mb-0 text-dark">3. Article Quantity Matrix (Extra Pieces)</h4>
                <span class="badge bg-primary fs-6 px-3 py-2">Batch Total: {{ number_format($batchTotalQty, 0) }} pcs</span>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle mb-0">
                    <thead class="table-light">
                        @if(!$isCanvas)
                            <tr>
                                <th rowspan="2" class="align-middle bg-light text-center" style="min-width: 170px; width: 170px;">ART NO / MATERIAL</th>
                                <th colspan="{{ count($allSizes) }}" class="text-center bg-light fw-bold">F/S</th>
                                <th colspan="{{ count($allSizes) }}" class="text-center bg-light fw-bold">H/S</th>
                                <th rowspan="2" class="align-middle bg-light text-center" style="min-width: 110px; width: 110px;">TOTAL</th>
                            </tr>
                            <tr>
                                @foreach($allSizes as $sz)
                                    <th class="text-center bg-light" style="min-width: 75px;">{{ $sz }}</th>
                                @endforeach
                                @foreach($allSizes as $sz)
                                    <th class="text-center bg-light" style="min-width: 75px;">{{ $sz }}</th>
                                @endforeach
                            </tr>
                        @else
                            <tr>
                                <th class="align-middle bg-light text-center" style="min-width: 170px; width: 170px;">ART NO / MATERIAL</th>
                                @foreach($allSizes as $sz)
                                    <th class="text-center bg-light" style="min-width: 75px;">{{ $sz }}</th>
                                @endforeach
                                <th class="align-middle bg-light text-center" style="min-width: 110px; width: 110px;">TOTAL</th>
                            </tr>
                        @endif
                    </thead>
                    <tbody>
                        @foreach($batchGroup as $idx => $bf)
                            @php
                                $matName = $jobCard->item->item_name ?? 'COTTON LINEN';
                                $rowTotal = 0;
                            @endphp
                            <tr>
                                <td>
                                    <div class="border rounded p-1 mb-1 text-center fw-bold small bg-white text-primary">{{ $bf->art_no }}</div>
                                    <div class="small text-muted text-center text-uppercase" style="font-size: 10px; line-height: 1.1;">{{ $matName }}</div>
                                </td>
                                @if(!$isCanvas)
                                    @foreach($allSizes as $sz)
                                        @php
                                            $qObj = $bf->quantities->firstWhere('size', (string)$sz);
                                            $fsQty = ($qObj && $qObj->qty_fs > 0) ? $qObj->qty_fs : 0;
                                            $rowTotal += $fsQty;
                                        @endphp
                                        <td class="fw-semibold {{ $fsQty > 0 ? 'text-primary' : 'text-muted' }}">{{ $fsQty }}</td>
                                    @endforeach
                                    @foreach($allSizes as $sz)
                                        @php
                                            $qObj = $bf->quantities->firstWhere('size', (string)$sz);
                                            $hsQty = ($qObj && $qObj->qty_hs > 0) ? $qObj->qty_hs : 0;
                                            $rowTotal += $hsQty;
                                        @endphp
                                        <td class="fw-semibold {{ $hsQty > 0 ? 'text-dark' : 'text-muted' }}">{{ $hsQty }}</td>
                                    @endforeach
                                @else
                                    @foreach($allSizes as $sz)
                                        @php
                                            $qObj = $bf->quantities->firstWhere('size', (string)$sz);
                                            $fsQty = ($qObj && $qObj->total_qty > 0) ? $qObj->total_qty : 0;
                                            $rowTotal += $fsQty;
                                        @endphp
                                        <td class="fw-semibold {{ $fsQty > 0 ? 'text-primary' : 'text-muted' }}">{{ $fsQty }}</td>
                                    @endforeach
                                @endif
                                <td class="fw-bold text-success fs-6">{{ $rowTotal }} pcs</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td class="fw-bold text-center small">CUTTING TOTAL (PCS)</td>
                            @if(!$isCanvas)
                                @foreach($allSizes as $sz)
                                    @php
                                        $colFsTot = $batchGroup->flatMap->quantities->where('size', (string)$sz)->sum('qty_fs');
                                    @endphp
                                    <td><div class="text-center fw-bold small py-1 border rounded bg-white text-primary" style="min-height: 30px; display: flex; align-items: center; justify-content: center;">{{ $colFsTot }}</div></td>
                                @endforeach
                                @foreach($allSizes as $sz)
                                    @php
                                        $colHsTot = $batchGroup->flatMap->quantities->where('size', (string)$sz)->sum('qty_hs');
                                    @endphp
                                    <td><div class="text-center fw-bold small py-1 border rounded bg-white text-dark" style="min-height: 30px; display: flex; align-items: center; justify-content: center;">{{ $colHsTot }}</div></td>
                                @endforeach
                            @else
                                @foreach($allSizes as $sz)
                                    @php
                                        $colTot = $batchGroup->flatMap->quantities->where('size', (string)$sz)->sum('total_qty');
                                    @endphp
                                    <td><div class="text-center fw-bold small py-1 border rounded bg-white text-primary" style="min-height: 30px; display: flex; align-items: center; justify-content: center;">{{ $colTot }}</div></td>
                                @endforeach
                            @endif
                            <td><div class="text-center fw-bold py-1 border rounded small text-success bg-white fs-6" style="min-height: 30px; display: flex; align-items: center; justify-content: center;">{{ $batchTotalQty }} pcs</div></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- 4. Warehouse & Production Receipts (If Posted/Recorded) -->
    @if($productionReceipts && $productionReceipts->count() > 0)
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">
                <div class="card-header-box d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <h4 class="mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="ri ri-building-line"></i> 4. Warehouse Production Receipts
                    </h4>
                    <span class="badge bg-label-primary fs-6 px-3 py-2">Total Receipts: {{ $productionReceipts->count() }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered align-middle text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Receipt #</th>
                                <th>Receipt Date</th>
                                <th>Warehouse</th>
                                <th>Store Location</th>
                                <th>Received By</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productionReceipts as $rcpt)
                                <tr>
                                    <td class="fw-bold text-primary">{{ $rcpt->receipt_no }}</td>
                                    <td>{{ $rcpt->receipt_date ? date('d-m-Y', strtotime($rcpt->receipt_date)) : '-' }}</td>
                                    <td><span class="badge bg-label-info">{{ $rcpt->warehouse->warehouse_name ?? '-' }}</span></td>
                                    <td>{{ $rcpt->storeLocation->store_location_name ?? '-' }}</td>
                                    <td>{{ $rcpt->employee->name ?? '-' }}</td>
                                    <td>
                                        @if($rcpt->status == 'Posted')
                                            <span class="badge bg-label-primary"><i class="ri ri-check-line me-1"></i> Posted</span>
                                        @else
                                            <span class="badge bg-label-warning">{{ $rcpt->status ?? 'Draft' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ url('production_receipts/view/' . $rcpt->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="ri ri-eye-line"></i> View Receipt
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

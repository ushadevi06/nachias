@extends('layouts.common')
@section('title', 'Addition History - Job Card ' . $jobCard->job_card_no . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    @php
        $isCanvas = false;
        if ($jobCard->brand && in_array(strtoupper(trim($jobCard->brand->brand_name)), ['CANVAS ACCESSORIES', 'CANVAS ACCESSORIES (CAS)'])) {
            $isCanvas = true;
        }
        $totalBatchesCount = count($additionalBatches);
        $totalAdditionalPieces = $jobCard->additional_qty ?? $jobCard->fabricDetails->where('is_additional', 1)->sum('total_qty');
        $totalAdditionalMeters = $jobCard->fabricDetails->where('is_additional', 1)->sum('mtr');
    @endphp

    <!-- Page Header & Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-1 text-primary d-flex align-items-center gap-2">
                <i class="ri ri-history-line"></i> Job Card Addition History & Logs
            </h4>
            <div class="text-muted small">
                Job Card Number: <strong class="text-dark">{{ $jobCard->job_card_no }}</strong> &nbsp;|&nbsp; 
                Date: {{ $jobCard->job_card_date ? date('d-m-Y', strtotime($jobCard->job_card_date)) : '-' }} &nbsp;|&nbsp; 
                Plant: {{ $jobCard->serviceProvider->name ?? '-' }}
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('job_card_entries.additional_qty', $jobCard->id) }}" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
                <i class="ri ri-add-line"></i> Add New Batch
            </a>
            <a href="{{ route('job_card_entries.view-item', $jobCard->id) }}" class="btn btn-outline-primary d-flex align-items-center gap-1">
                <i class="ri ri-list-check-2"></i> Issue Item
            </a>
            <a href="{{ url('job_card_entries/view/' . $jobCard->id) }}" class="btn btn-outline-secondary d-flex align-items-center gap-1">
                <i class="ri ri-eye-line"></i> View Job Card
            </a>
            <a href="{{ url('job_card_entries') }}" class="btn btn-secondary d-flex align-items-center gap-1">
                <i class="ri ri-arrow-left-line"></i> Back to List
            </a>
        </div>
    </div>

    @include('flash_messages')

    <!-- Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm text-center p-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #edf2f7 100%);">
                <span class="text-muted small fw-bold text-uppercase">Planned Quantity</span>
                <h3 class="mb-0 fw-bold text-dark mt-1">{{ number_format($jobCard->grand_total_qty - $totalAdditionalPieces, 0) }} <small class="fs-6 text-muted">pcs</small></h3>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm text-center p-3" style="background: linear-gradient(135deg, #fff9db 0%, #fff3bf 100%);">
                <span class="text-warning-emphasis small fw-bold text-uppercase">Total Additional Batches</span>
                <h3 class="mb-0 fw-bold text-warning mt-1">{{ $totalBatchesCount }} <small class="fs-6 text-muted">batches</small></h3>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm text-center p-3" style="background: linear-gradient(135deg, #e7f5ff 0%, #d0ebff 100%);">
                <span class="text-info-emphasis small fw-bold text-uppercase">Total Extra Pieces</span>
                <h3 class="mb-0 fw-bold text-info mt-1">+{{ number_format($totalAdditionalPieces, 0) }} <small class="fs-6 text-muted">pcs</small></h3>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm text-center p-3" style="background: linear-gradient(135deg, #ebfbee 0%, #d3f9d8 100%);">
                <span class="text-success small fw-bold text-uppercase">Current Grand Total</span>
                <h3 class="mb-0 fw-bold text-success mt-1">{{ number_format($jobCard->grand_total_qty, 0) }} <small class="fs-6 text-muted">pcs</small></h3>
            </div>
        </div>
    </div>

    <!-- History Table Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="ri ri-list-unordered text-primary"></i> Supplementary Batches List
            </h5>
            <span class="badge bg-label-primary px-3 py-2 fs-6">Total: {{ $totalBatchesCount }} Batches</span>
        </div>
        <div class="card-body p-0">
            @if(empty($additionalBatches) || count($additionalBatches) == 0)
                <div class="text-center py-5">
                    <i class="ri ri-inbox-line fs-1 d-block mb-3 text-secondary" style="font-size: 3rem;"></i>
                    <h5 class="fw-bold text-dark">No Additional Quantity Batches Added</h5>
                    <p class="text-muted small mb-3">No supplementary cutting or fabric has been added for this job card yet.</p>
                    <a href="{{ route('job_card_entries.additional_qty', $jobCard->id) }}" class="btn btn-primary px-4">
                        <i class="ri ri-add-line me-1"></i> Add First Additional Quantity Batch
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 8%;">Batch #</th>
                                <th style="width: 13%;">Added Date & Time</th>
                                <th style="width: 20%;">Fabric / Art No</th>
                                <th style="width: 12%;">Issued Meters</th>
                                <th style="width: 14%;">Extra Quantity</th>
                                <th style="width: 21%;">Size Ratio Breakdown</th>
                                <th style="width: 12%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($additionalBatches as $idx => $batchGroup)
                                @php
                                    $firstFabric = $batchGroup->first();
                                    $batchNo = $firstFabric->additional_batch_no ?? ($idx + 1);
                                    $batchTotalQty = $batchGroup->sum('total_qty');
                                    $batchFsQty = $batchGroup->sum('fs_qty');
                                    $batchHsQty = $batchGroup->sum('hs_qty');
                                    $batchMtr = $batchGroup->sum('mtr');
                                    $isPosted = $batchGroup->contains(fn($f) => $f->isPostedToWarehouse());
                                @endphp
                                <tr>
                                    <td>
                                        <span class="badge bg-primary fs-6 px-3 py-1">Batch #{{ $batchNo }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $firstFabric->created_at ? $firstFabric->created_at->format('d-m-Y') : '-' }}</div>
                                        <div class="text-muted small">{{ $firstFabric->created_at ? $firstFabric->created_at->format('h:i A') : '' }}</div>
                                    </td>
                                    <td>
                                        @if($batchGroup->count() == 1)
                                            <div class="fw-bold text-dark fs-6">{{ $firstFabric->art_no ?? '-' }}</div>
                                            <div class="text-muted small mt-1">
                                                Width: <strong>{{ $firstFabric->width ?? '-' }}</strong> &nbsp;|&nbsp; 
                                                In/Out: <strong>{{ $firstFabric->in_out ?? 'NO' }}</strong> &nbsp;|&nbsp; 
                                                N.Patti: <strong>{{ $firstFabric->n_patti ?? 'WHITE' }}</strong>
                                            </div>
                                        @else
                                            <div class="d-flex flex-wrap gap-1 justify-content-center">
                                                @foreach($batchGroup as $bf)
                                                    <span class="badge bg-label-primary px-2 py-1 fs-6 fw-bold border">
                                                        {{ $bf->art_no }} <small class="text-muted">({{ number_format($bf->mtr, 2) }}m / +{{ $bf->total_qty }}pcs)</small>
                                                    </span>
                                                @endforeach
                                            </div>
                                            <div class="text-muted small mt-1">
                                                In/Out: <strong>{{ $firstFabric->in_out ?? 'NO' }}</strong> &nbsp;|&nbsp; 
                                                N.Patti: <strong>{{ $firstFabric->n_patti ?? 'WHITE' }}</strong>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark fs-6">{{ number_format($batchMtr, 2) }}</span> <small class="text-muted">Mtr</small>
                                        @if($batchGroup->count() > 1)
                                            <div class="small text-muted mt-1">({{ $batchGroup->count() }} Fabrics)</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-success fs-6 px-3 py-2">+{{ number_format($batchTotalQty, 0) }} pcs</span>
                                        @if(!$isCanvas)
                                            <div class="small text-muted mt-1">
                                                (F/S: <strong>{{ $batchFsQty }}</strong>, H/S: <strong>{{ $batchHsQty }}</strong>)
                                            </div>
                                        @endif
                                        @if($isPosted)
                                            <div class="mt-2">
                                                <span class="badge bg-label-success border border-success" style="font-size: 10px;">
                                                    <i class="ri ri-checkbox-circle-fill me-1"></i> Warehouse Posted
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1 text-start">
                                            @php
                                                $allBatchQuantities = $batchGroup->flatMap->quantities;
                                                $fsSizes = $allBatchQuantities->where('qty_fs', '>', 0)->groupBy('size')->map->sum('qty_fs');
                                                $hsSizes = $allBatchQuantities->where('qty_hs', '>', 0)->groupBy('size')->map->sum('qty_hs');
                                            @endphp

                                            @if($fsSizes->count() > 0)
                                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                                    <span class="badge bg-primary text-white" style="font-size: 11px; min-width: 32px;">F/S</span>
                                                    @foreach($fsSizes as $sName => $sQty)
                                                        <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 11px;">
                                                            <strong>{{ $sName }}</strong>: {{ $sQty }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif

                                            @if($hsSizes->count() > 0)
                                                <div class="d-flex align-items-center gap-1 flex-wrap {{ $fsSizes->count() > 0 ? 'mt-1' : '' }}">
                                                    <span class="badge bg-success text-white" style="font-size: 11px; min-width: 32px;">H/S</span>
                                                    @foreach($hsSizes as $sName => $sQty)
                                                        <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 11px;">
                                                            <strong>{{ $sName }}</strong>: {{ $sQty }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif

                                            @if($fsSizes->count() == 0 && $hsSizes->count() == 0)
                                                <div class="d-flex flex-wrap gap-1 justify-content-center">
                                                    @php
                                                        $totSizes = $allBatchQuantities->where('total_qty', '>', 0)->groupBy('size')->map->sum('total_qty');
                                                    @endphp
                                                    @forelse($totSizes as $sName => $sQty)
                                                        <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 11px;">
                                                            <strong>{{ $sName }}</strong>: {{ $sQty }}
                                                        </span>
                                                    @empty
                                                        <span class="text-muted small">-</span>
                                                    @endforelse
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            @if(!$isPosted)
                                                <a href="{{ url('job_card_entries/additional-qty/' . $jobCard->id . '?batch_id=' . $firstFabric->id) }}" class="btn btn-sm btn-outline-warning d-flex align-items-center gap-1" title="Edit this Batch">
                                                    <i class="ri ri-edit-line"></i> Edit
                                                </a>
                                            @endif
                                            <a href="{{ url('job_card_entries/additional-qty-view/' . $jobCard->id . '?batch_id=' . $firstFabric->id) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1" title="View Batch Details">
                                                <i class="ri ri-eye-line"></i> View Details
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

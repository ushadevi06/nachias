@extends('layouts.common')
@section('title', 'Costing Analysis - ' . $jobCard->job_card_no)
@section('content')
<div class="container-xxl section-padding">
    <div class="row g-4">
        <div class="col-lg-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Costing Analysis: <span class="text-primary">{{ $jobCard->job_card_no }}</span></h4>
            <a href="{{ route('job_card_entries.view-item', $jobCard->id) }}" class="btn btn-secondary">
                <i class="ri-arrow-left-line me-1"></i> Back to Job Card
            </a>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-start border-primary border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm flex-shrink-0 me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="ri-t-shirt-line"></i></span>
                        </div>
                        <h6 class="mb-0">Production Volume</h6>
                    </div>
                    <h4 class="mb-0">{{ number_format($analysis['total_produced'], 0) }} <small class="text-muted">Pcs</small></h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-start border-success border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm flex-shrink-0 me-2">
                            <span class="avatar-initial rounded bg-label-success"><i class="ri-money-dollar-circle-line"></i></span>
                        </div>
                        <h6 class="mb-0">Total Job Card Cost</h6>
                    </div>
                    <h4 class="mb-0">₹{{ number_format($analysis['grand_total']['total'], 2) }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-start border-warning border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm flex-shrink-0 me-2">
                            <span class="avatar-initial rounded bg-label-warning"><i class="ri-price-tag-3-line"></i></span>
                        </div>
                        <h6 class="mb-0">Cost Per Piece (AVG)</h6>
                    </div>
                    <h4 class="mb-0 text-warning">₹{{ number_format($analysis['grand_total']['avg'], 2) }}</h4>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="card-title mb-0">Cost Breakdown</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th class="text-end">Total Amount</th>
                                <th class="text-end">Cost per Piece</th>
                                <th class="text-center">Contribution</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="ri-scissors-cut-line text-primary me-2 fs-4"></i>
                                        <div>
                                            <div class="fw-bold">Fabric Consumption</div>
                                            <div class="small text-muted">Body & Contrast Materials</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end fw-semibold">₹{{ number_format($analysis['fabric']['total'], 2) }}</td>
                                <td class="text-end">₹{{ number_format($analysis['fabric']['avg'], 2) }}</td>
                                <td class="text-center" style="width: 150px;">
                                    @php $percent = $analysis['grand_total']['total'] > 0 ? ($analysis['fabric']['total'] / $analysis['grand_total']['total']) * 100 : 0; @endphp
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percent }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ number_format($percent, 1) }}%</small>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="ri-ink-bottle-line text-info me-2 fs-4"></i>
                                        <div>
                                            <div class="fw-bold">Accessories</div>
                                            <div class="small text-muted">Buttons, Labels, Thread, etc.</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end fw-semibold">₹{{ number_format($analysis['accessories']['total'], 2) }}</td>
                                <td class="text-end">₹{{ number_format($analysis['accessories']['avg'], 2) }}</td>
                                <td class="text-center">
                                    @php $percent = $analysis['grand_total']['total'] > 0 ? ($analysis['accessories']['total'] / $analysis['grand_total']['total']) * 100 : 0; @endphp
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $percent }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ number_format($percent, 1) }}%</small>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="ri-settings-4-line text-warning me-2 fs-4"></i>
                                        <div>
                                            <div class="fw-bold">WIP Process Costs</div>
                                            <div class="small text-muted">Operational Stages (Labor)</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end fw-semibold">₹{{ number_format($analysis['process']['total'], 2) }}</td>
                                <td class="text-end">₹{{ number_format($analysis['process']['avg'], 2) }}</td>
                                <td class="text-center">
                                    @php $percent = $analysis['grand_total']['total'] > 0 ? ($analysis['process']['total'] / $analysis['grand_total']['total']) * 100 : 0; @endphp
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percent }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ number_format($percent, 1) }}%</small>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td class="fw-bold fs-5">Grand Total Cost</td>
                                <td class="text-end fw-bold fs-5">₹{{ number_format($analysis['grand_total']['total'], 2) }}</td>
                                <td class="text-end fw-bold fs-5 text-warning">₹{{ number_format($analysis['grand_total']['avg'], 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="card-title mb-0">Stage Breakdown</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($jobCard->operations as $op)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div>
                                <div class="fw-bold">{{ $op->operationStage->operation_stage_name ?? 'N/A' }}</div>
                                <div class="small text-muted">Rate: ₹{{ number_format($op->rate, 2) }} / pc</div>
                            </div>
                            <span class="badge bg-label-secondary rounded-pill">₹{{ number_format($op->total_cost, 2) }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-label-primary { background-color: #e7e7ff !important; color: #696cff !important; }
    .bg-label-success { background-color: #e8fadf !important; color: #71dd37 !important; }
    .bg-label-warning { background-color: #fff2d6 !important; color: #ffab00 !important; }
    .bg-label-secondary { background-color: #ebedef !important; color: #8592a3 !important; }
    .avatar { position: relative; width: 2.375rem; height: 2.375rem; cursor: pointer; }
    .avatar-sm { width: 2rem; height: 2rem; }
    .avatar-initial { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-weight: 500; }
</style>
@endsection

@extends('layouts.common')
@section('title', 'Job Card Details - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="card-header-box d-flex justify-content-between align-items-center">
                        <h4>Job Card Details: {{ $jobCard->job_card_no }}</h4>
                        <div>
                            <a href="{{ url('job_card_entries/add/' . $jobCard->id) }}" class="btn btn-primary btn-sm">Edit</a>
                            <a href="{{ url('job_card_entries/view-item/' . $jobCard->id) }}" class="btn btn-info btn-sm">Issue Item</a>
                            <a href="{{ url('job_card_entries') }}" class="btn btn-secondary btn-sm">Back</a>
                        </div>
                    </div>
                    <div class="row g-4 mt-2">
                        <div class="col-md-3"><strong>Date:</strong> {{ date('d-m-Y', strtotime($jobCard->job_card_date)) }}</div>
                        <div class="col-md-3"><strong>PO Reference:</strong> {{ $jobCard->purchaseOrder->po_number ?? '-' }}</div>
                        <div class="col-md-3"><strong>MRP:</strong> {{ $jobCard->mrp ?: '-' }}</div>
                        <div class="col-md-3"><strong>Price F/S:</strong> {{ $jobCard->price_fs ?: '-' }}</div>
                        <div class="col-md-3"><strong>Price H/S:</strong> {{ $jobCard->price_hs ?: '-' }}</div>
                        <div class="col-md-3"><strong>Brand:</strong> {{ $jobCard->brand->brand_name ?? '-' }}</div>
                        <div class="col-md-3"><strong>Season:</strong> {{ $jobCard->season->name ?? '-' }}</div>
                        <div class="col-md-3"><strong>Process Group:</strong> {{ $jobCard->processGroup->name ?? '-' }}</div>
                        <div class="col-md-3"><strong>Status:</strong> {{ $jobCard->status }}</div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>Quantity Matrix</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    @php $sizes = $jobCard->items->pluck('size')->unique(); @endphp
                                    @foreach($sizes as $size)
                                        <th>{{ $size }}</th>
                                    @endforeach
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($jobCard->items->sum('qty_fs') > 0)
                                    <tr>
                                        <td><strong>QTY - F/S</strong></td>
                                        @foreach($sizes as $size)
                                            <td>{{ $jobCard->items->where('size', $size)->sum('qty_fs') ?: '-' }}</td>
                                        @endforeach
                                        <td>{{ $jobCard->total_qty_fs }}</td>
                                    </tr>
                                @endif
                                @if($jobCard->items->sum('qty_hs') > 0)
                                    <tr>
                                        <td><strong>QTY - H/S</strong></td>
                                        @foreach($sizes as $size)
                                            <td>{{ $jobCard->items->where('size', $size)->sum('qty_hs') ?: '-' }}</td>
                                        @endforeach
                                        <td>{{ $jobCard->total_qty_hs }}</td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <th>Total</th>
                                    @foreach($sizes as $size)
                                        <th>{{ $jobCard->items->where('size', $size)->sum('total_qty') }}</th>
                                    @endforeach
                                    <th>{{ $jobCard->grand_total_qty }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>Fabric Details</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-bold">ART NO</th>
                                    @foreach($jobCard->articleMatrices as $matrix)
                                        <th>{{ $matrix->art_no }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-bold">WIDTH</td>
                                    @foreach($jobCard->articleMatrices as $matrix)
                                        <td>{{ $matrix->width ?: '-' }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="fw-bold">Mtr/B.M</td>
                                    @foreach($jobCard->articleMatrices as $matrix)
                                        <td>{{ $matrix->mtr ?: '-' }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="fw-bold">IN/OUT</td>
                                    @foreach($jobCard->articleMatrices as $matrix)
                                        <td>{{ $matrix->in_out ?: '-' }}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td class="fw-bold">N.PATTI</td>
                                    @foreach($jobCard->articleMatrices as $matrix)
                                        <td>{{ $matrix->n_patti ?: '-' }}</td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>Article Quantity Matrix</h4>
                    </div>
                    @php
                        $sr = $jobCard->sizeRatio;
                        $srSizes = $sr ? explode(',', $sr->size) : [];
                    @endphp
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2" class="align-middle">ART NO</th>
                                    <th colspan="5">F/S</th>
                                    <th colspan="5">H/S</th>
                                    <th colspan="2">EX</th>
                                    <th rowspan="2" class="align-middle">TOTAL</th>
                                </tr>
                                <tr>
                                    @for($i=0; $i<5; $i++) <th>{{ $srSizes[$i] ?? '' }}</th> @endfor
                                    @for($i=0; $i<5; $i++) <th>{{ $srSizes[$i] ?? '' }}</th> @endfor
                                    <th>40 H/S</th><th>38 F/S</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jobCard->articleMatrices as $matrix)
                                    <tr>
                                        <td>{{ $matrix->art_no }}</td>
                                        <td>{{ $matrix->fs_36 ?: '-' }}</td>
                                        <td>{{ $matrix->fs_38 ?: '-' }}</td>
                                        <td>{{ $matrix->fs_40 ?: '-' }}</td>
                                        <td>{{ $matrix->fs_42 ?: '-' }}</td>
                                        <td>{{ $matrix->fs_44 ?: '-' }}</td>
                                        <td>{{ $matrix->hs_38 ?: '-' }}</td>
                                        <td>{{ $matrix->hs_40 ?: '-' }}</td>
                                        <td>{{ $matrix->hs_42 ?: '-' }}</td>
                                        <td>{{ $matrix->hs_44 ?: '-' }}</td>
                                        <td>{{ $matrix->hs_46 ?: '-' }}</td>
                                        <td>{{ $matrix->ex_1 ?: '-' }}</td>
                                        <td>{{ $matrix->ex_2 ?: '-' }}</td>
                                        <td class="fw-bold">{{ $matrix->row_total }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="card-header-box">
                                <h4>Production Stages</h4>
                            </div>
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Stage</th>
                                        <th>Employee</th>
                                        <th>Assigned Date</th>
                                        <th>Received By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jobCard->operations as $op)
                                        <tr>
                                            <td>{{ $op->operationStage->operation_stage_name }}</td>
                                            <td>{{ $op->employee->name ?? '-' }}</td>
                                            <td>{{ $op->assigned_date ? date('d-m-Y', strtotime($op->assigned_date)) : '-' }}</td>
                                            <td>{{ $op->received_by ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="card-header-box">
                                <h4>Uploaded Images</h4>
                            </div>
                            <div class="row g-2">
                                @foreach($jobCard->images as $img)
                                    <div class="col-4">
                                        <div class="border p-1 text-center">
                                            <img src="{{ asset($img->image) }}" class="img-fluid mb-1" style="max-height: 100px;">
                                            <div class="small text-muted">{{ $img->art_no }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($jobCard->remarks)
                <div class="card">
                    <div class="card-body">
                        <strong>Remarks:</strong>
                        <p>{{ $jobCard->remarks }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

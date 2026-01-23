@extends('layouts.common')
@section('title', 'View Production - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Production</h4>
                <a href="{{ url('productions') }}" class="btn btn-primary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="detail-title fw-bold text-primary">Job Card Number: </label>
                            <div class="text-dark">{{ $production->job_card_no }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title fw-bold text-primary">Purchase Order No:</label>
                            <div class="text-dark">{{ $production->purchase_order_no ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title fw-bold text-primary">Plant:</label>
                            <div class="text-dark">{{ $production->serviceProvider->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title fw-bold text-primary">Process Group:</label>
                            <div class="text-dark">{{ $production->processGroup->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title fw-bold text-primary">Planned Quantity (FS/HS/Total):</label>
                            <div class="text-dark">{{ $production->full_sleeve_qty }} / {{ $production->half_sleeve_qty }} / <span class="fw-bold">{{ $production->total_planned_qty }}</span></div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title fw-bold text-primary">Planned Dates:</label>
                            <div class="text-dark">{{ date('d-m-Y', strtotime($production->planned_start_date)) }} to {{ date('d-m-Y', strtotime($production->planned_end_date)) }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title fw-bold text-primary">Exp. Completion Date:</label>
                            <div class="text-dark">{{ $production->expected_completion_date ? date('d-m-Y', strtotime($production->expected_completion_date)) : '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title fw-bold text-primary">Status:</label>
                            <div class="text-muted"><span class="badge bg-label-{{ $production->status == 'Confirmed' ? 'success' : ($production->status == 'Draft' ? 'warning' : 'secondary') }}">{{ $production->status }}</span></div>
                        </div>
                        <div class="col-md-12">
                            <label class="detail-title fw-bold text-primary">Remarks:</label>
                            <div class="text-dark">{{ $production->remarks ?? 'No remarks' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Process Schedules --}}
            @foreach($production->processSchedules as $schedule)
            <div class="card mt-4 border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">{{ $schedule->stage }} Schedule</h5>
                    <span class="badge bg-primary">{{ $schedule->status }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4 mt-4">
                        <div class="col-md-3">
                            <label class="fw-bold small text-muted">Planned Qty</label>
                            <div>{{ $schedule->planned_qty }} {{ $schedule->uom }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold small text-muted">Scheduled To</label>
                            <div>{{ $schedule->scheduled_to ?? '-' }} ({{ $schedule->serviceProvider->code ?? '-' }})</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold small text-muted">Timeline</label>
                            <div>{{ $schedule->start_date ? date('d-m-Y', strtotime($schedule->start_date)) : '-' }} to {{ $schedule->end_date ? date('d-m-Y', strtotime($schedule->end_date)) : '-' }} (Due: {{ $schedule->due_date ? date('d-m-Y', strtotime($schedule->due_date)) : '-' }})</div>
                        </div>
                    </div>
                    
                    <h6 class="fw-bold border-bottom pb-2">Associated Services</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th>Service Name</th>
                                    <th>Applies To</th>
                                    <th class="text-end">Qty</th>
                                    <th>UOM</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($schedule->services as $service)
                                <tr>
                                    <td>{{ $service->productionService->service_name ?? '-' }}</td>
                                    <td><span class="badge bg-label-info">{{ $service->applies_to }}</span></td>
                                    <td class="text-end fw-bold">{{ $service->calculated_qty }}</td>
                                    <td>{{ $service->uom }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
@extends('layouts.common')
@section('title', 'View Log - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Log Details - #{{ $log->id }}</h4>
                <a href="{{ url('logs') }}" class="btn btn-primary">
                    <i class="ri ri-arrow-left-line back-arrow"></i>Back
                </a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="detail-title">Action Type:</label>
                            <div>
                                @php
                                    $badgeClass = match ($log->action_type) {
                                        'create' => 'bg-success',
                                        'update' => 'bg-info',
                                        'delete' => 'bg-danger',
                                        'update_status' => 'bg-warning text-dark',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucwords(str_replace('_', ' ', $log->action_type)) }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Module:</label>
                            <div class="text-muted">{{ ucwords(str_replace(['_', '-'], ' ', $log->module)) }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Database Table:</label>
                            <div class="text-muted">{{ $log->table_name ?: '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Record ID:</label>
                            <div class="text-muted">{{ $log->record_id ? '#' . $log->record_id : '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Performed By:</label>
                            <div class="text-muted">{{ $log->user->name ?? 'System' }} {{ $log->user && $log->user->email ? '(' . $log->user->email . ')' : '' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Date & Time:</label>
                            <div class="text-muted">
                                {{ $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('d-m-Y h:i:s A') : '-' }}
                                @if($log->created_at)
                                    ({{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }})
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">IP Address:</label>
                            <div class="text-muted">{{ $log->ip_address ?: '-' }}</div>
                        </div>
                        <div class="col-md-8">
                            <label class="detail-title">Device & Browser:</label>
                            <div class="text-muted">{{ $deviceInfo }}</div>
                        </div>
                        <div class="col-md-12">
                            <label class="detail-title">Description:</label>
                            <div class="text-muted">
                                {{ $log->description ?: ucwords(str_replace(['_', '-'], ' ', $log->module)) . ' ' . ucwords(str_replace('_', ' ', $log->action_type)) . ' by ' . ($log->user->name ?? 'System') }}
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Changed Fields:</h6>
                        </div>
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 30%;">Field</th>
                                            <th style="width: 35%;">Old Value</th>
                                            <th style="width: 35%;">New Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($changedFields) > 0)
                                            @foreach($changedFields as $change)
                                                <tr>
                                                    <td>
                                                        <span class="fw-bold">{{ $change['field'] }}</span>
                                                    </td>
                                                    <td>
                                                        @if($change['old'] === '-')
                                                            <span class="text-muted">-</span>
                                                        @else
                                                            <span class="text-danger"><del>{{ $change['old'] }}</del></span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($change['new'] === '-')
                                                            <span class="text-muted">-</span>
                                                        @else
                                                            <span class="text-success fw-bold">{{ $change['new'] }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-3">No changes recorded</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

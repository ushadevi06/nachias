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
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="fw-bold mb-0">Changed Fields:</h6>
                                @if(!empty($prettyOldJson) || !empty($prettyNewJson))
                                    <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1" type="button" data-bs-toggle="collapse" data-bs-target="#rawJsonCollapse">
                                        <i class="ri ri-code-s-slash-line me-1"></i> Toggle Raw JSON
                                    </button>
                                @endif
                            </div>
                        </div>

                        @if(!empty($prettyOldJson) || !empty($prettyNewJson))
                            <div class="col-lg-12 collapse" id="rawJsonCollapse">
                                <div class="card bg-light border p-3 mb-3">
                                    <div class="row">
                                        @if(!empty($prettyOldJson))
                                            <div class="col-md-6">
                                                <h6 class="small fw-bold text-danger">Raw Old Values (JSON):</h6>
                                                <pre class="bg-white p-2 rounded border" style="max-height: 250px; font-size: 11px;"><code>{{ $prettyOldJson }}</code></pre>
                                            </div>
                                        @endif
                                        @if(!empty($prettyNewJson))
                                            <div class="col-md-6">
                                                <h6 class="small fw-bold text-success">Raw New Values (JSON):</h6>
                                                <pre class="bg-white p-2 rounded border" style="max-height: 250px; font-size: 11px;"><code>{{ $prettyNewJson }}</code></pre>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        @php
                            $renderValue = function($val, $isOld = false, $uniqueId = '') {
                                $type = $val['type'] ?? 'string';
                                if ($type === 'empty') {
                                    return '<span class="text-muted">-</span>';
                                }

                                if ($type === 'items_table') {
                                    $count = $val['count'] ?? 0;
                                    $headers = $val['headers'] ?? [];
                                    $rows = $val['rows'] ?? [];
                                    $btnClass = $isOld ? 'btn-outline-danger' : 'btn-outline-success';

                                    $html = '<div class="items-view-container">';
                                    $html .= '<div class="d-flex align-items-center gap-2 mb-2">';
                                    $html .= '<button class="btn btn-sm ' . $btnClass . ' rounded-pill px-3 py-1 fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_' . $uniqueId . '">';
                                    $html .= '<i class="ri ri-list-check me-1"></i> ' . $count . ' Items (Click to View)';
                                    $html .= '</button>';
                                    $html .= '</div>';

                                    $html .= '<div class="collapse" id="collapse_' . $uniqueId . '">';
                                    $html .= '<div class="table-responsive border rounded bg-white shadow-sm" style="max-height: 300px; overflow-y: auto;">';
                                    $html .= '<table class="table table-sm table-hover table-striped mb-0 align-middle" style="font-size: 12px;">';
                                    $html .= '<thead class="table-light sticky-top"><tr><th style="width: 40px;">#</th>';
                                    foreach ($headers as $h) {
                                        $html .= '<th>' . ucwords(str_replace('_', ' ', $h)) . '</th>';
                                    }
                                    $html .= '</tr></thead><tbody>';
                                    foreach ($rows as $rIdx => $row) {
                                        $html .= '<tr><td>' . ($rIdx + 1) . '</td>';
                                        foreach ($headers as $h) {
                                            $cell = $row[$h] ?? '-';
                                            if (is_array($cell)) {
                                                $cell = json_encode($cell);
                                            }
                                            $html .= '<td>' . htmlspecialchars((string)$cell) . '</td>';
                                        }
                                        $html .= '</tr>';
                                    }
                                    $html .= '</tbody></table></div></div></div>';
                                    return $html;
                                }

                                if ($type === 'key_value') {
                                    $data = $val['data'] ?? [];
                                    $badgeBorder = $isOld ? 'border-danger-subtle bg-danger-subtle text-danger' : 'border-success-subtle bg-light text-dark';
                                    $html = '<div class="d-flex flex-wrap gap-1">';
                                    foreach ($data as $k => $v) {
                                        $dispV = is_array($v) ? json_encode($v) : $v;
                                        $html .= '<span class="badge border ' . $badgeBorder . ' px-2 py-1 text-wrap text-start" style="font-size: 11.5px;">';
                                        $html .= '<strong class="text-muted">' . ucwords(str_replace('_', ' ', $k)) . ':</strong> ' . htmlspecialchars((string)$dispV);
                                        $html .= '</span>';
                                    }
                                    $html .= '</div>';
                                    return $html;
                                }

                                if ($type === 'tags') {
                                    $tags = $val['tags'] ?? [];
                                    $html = '<div class="d-flex flex-wrap gap-1">';
                                    foreach ($tags as $tag) {
                                        $html .= '<span class="badge bg-light text-dark border px-2 py-1">' . htmlspecialchars((string)$tag) . '</span>';
                                    }
                                    $html .= '</div>';
                                    return $html;
                                }

                                $disp = htmlspecialchars($val['display'] ?? '-');
                                if ($isOld) {
                                    return '<span class="text-danger"><del>' . $disp . '</del></span>';
                                }
                                return '<span class="text-success fw-semibold">' . $disp . '</span>';
                            };
                        @endphp

                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 25%;">Field</th>
                                            <th style="width: 37.5%;">Old Value</th>
                                            <th style="width: 37.5%;">New Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($changedFields) > 0)
                                            @foreach($changedFields as $idx => $change)
                                                <tr>
                                                    <td>
                                                        <span class="fw-bold text-dark">{{ $change['field'] }}</span>
                                                    </td>
                                                    <td>
                                                        {!! $renderValue($change['old'], true, 'old_' . $idx) !!}
                                                    </td>
                                                    <td>
                                                        {!! $renderValue($change['new'], false, 'new_' . $idx) !!}
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

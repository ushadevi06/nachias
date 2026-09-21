<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = \App\Models\Log::with('user')->latest();

            if (!empty($request->date_range)) {
                try {
                    $dates = explode(' to ', $request->date_range);
                    if (count($dates) == 2) {
                        $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                        $endDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                        $query->whereBetween('created_at', [$startDate, $endDate]);
                    } elseif (count($dates) == 1 && !empty(trim($dates[0]))) {
                        $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                        $query->whereDate('created_at', $startDate);
                    }
                } catch (\Exception $e) {
                    // Ignore date parse errors gracefully
                }
            }

            $totalRecords = $query->count();

            if ($request->has('search') && !empty($request->input('search')['value'])) {
                $search = $request->input('search')['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('module', 'like', "%{$search}%")
                        ->orWhere('action_type', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('created_at', 'like', "%{$search}%")
                        ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y') LIKE ?", ["%{$search}%"])
                        ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y %h:%i %p') LIKE ?", ["%{$search}%"])
                        ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') LIKE ?", ["%{$search}%"])
                        ->orWhereHas('user', function ($q2) use ($search) {
                            $q2->where('name', 'like', "%{$search}%");
                        });
                });
            }

            $filteredRecords = $query->count();

            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            if ($length != -1) {
                $query->skip($start)->take($length);
            }

            $logs = $query->get();
            $data = [];
            $count = $start + 1;

            foreach ($logs as $log) {
                $badgeClass = match ($log->action_type) {
                    'create' => 'bg-success',
                    'update' => 'bg-info',
                    'delete' => 'bg-danger',
                    'update_status' => 'bg-warning text-dark',
                    default => 'bg-secondary',
                };
                $actionDisplay = '<span class="badge ' . $badgeClass . '">' . ucwords(str_replace('_', ' ', $log->action_type)) . '</span>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'created_at' => $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('d-m-Y h:i A') : '-',
                    'user_name' => $log->user->name ?? 'System',
                    'module' => ucwords(str_replace(['_', '-'], ' ', $log->module)),
                    'action_type' => $actionDisplay,
                    'description' => $log->description ?: ucwords(str_replace(['_', '-'], ' ', $log->module)) . ' ' . ucwords(str_replace('_', ' ', $log->action_type)) . ' by ' . ($log->user->name ?? 'System'),
                    'action' => '<div class="d-flex align-items-center"><a href="' . url('logs/view/' . $log->id) . '" class="btn btn-view" title="View Details"><i class="icon-base ri ri-eye-line"></i></a></div>',
                ];
            }

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        }
        return view('logs.view');
    }

    public function show($id)
    {
        $log = \App\Models\Log::with('user')->findOrFail($id);

        $oldValues = $log->old_values ? json_decode($log->old_values, true) : [];
        $newValues = $log->new_values ? json_decode($log->new_values, true) : [];

        $excludeFields = ['id', 'created_by', 'updated_by', 'deleted_at'];
        $changedFields = [];

        $formatValue = function ($value) {
            if (is_null($value) || $value === '') {
                return '-';
            }
            if (is_bool($value)) {
                return $value ? 'Yes' : 'No';
            }
            if (is_array($value)) {
                return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            }
            if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
                try {
                    return \Carbon\Carbon::parse($value)->format('d-m-Y h:i A');
                } catch (\Exception $e) {
                    return $value;
                }
            }
            return (string) $value;
        };

        if ($log->action_type == 'create') {
            foreach (($newValues ?: []) as $key => $value) {
                if (in_array($key, $excludeFields)) continue;
                $isArray = is_array($value);
                $changedFields[] = [
                    'field' => ucwords(str_replace('_', ' ', $key)),
                    'raw_field' => $key,
                    'old' => '-',
                    'new' => $formatValue($value),
                    'is_array' => $isArray,
                    'status' => 'added'
                ];
            }
        } elseif ($log->action_type == 'delete') {
            foreach (($oldValues ?: []) as $key => $value) {
                if (in_array($key, $excludeFields)) continue;
                $isArray = is_array($value);
                $changedFields[] = [
                    'field' => ucwords(str_replace('_', ' ', $key)),
                    'raw_field' => $key,
                    'old' => $formatValue($value),
                    'new' => '-',
                    'is_array' => $isArray,
                    'status' => 'removed'
                ];
            }
        } else {
            $allKeys = array_unique(array_merge(array_keys($oldValues ?: []), array_keys($newValues ?: [])));
            foreach ($allKeys as $key) {
                if (in_array($key, $excludeFields)) continue;
                $oldVal = $oldValues[$key] ?? null;
                $newVal = $newValues[$key] ?? null;

                if ($oldVal != $newVal) {
                    $isArray = is_array($oldVal) || is_array($newVal);
                    $changedFields[] = [
                        'field' => ucwords(str_replace('_', ' ', $key)),
                        'raw_field' => $key,
                        'old' => $formatValue($oldVal),
                        'new' => $formatValue($newVal),
                        'is_array' => $isArray,
                        'status' => 'modified'
                    ];
                }
            }
        }

        // Parse user agent for simple human readable display
        $deviceInfo = 'Unknown Device';
        if (!empty($log->user_agent)) {
            $ua = $log->user_agent;
            $platform = 'Unknown OS';
            if (preg_match('/windows|win32/i', $ua)) $platform = 'Windows';
            elseif (preg_match('/macintosh|mac os x/i', $ua)) $platform = 'macOS';
            elseif (preg_match('/android/i', $ua)) $platform = 'Android';
            elseif (preg_match('/iphone|ipad|ipod/i', $ua)) $platform = 'iOS';
            elseif (preg_match('/linux/i', $ua)) $platform = 'Linux';

            $browser = 'Unknown Browser';
            if (preg_match('/edg/i', $ua)) $browser = 'Microsoft Edge';
            elseif (preg_match('/chrome|crios/i', $ua)) $browser = 'Google Chrome';
            elseif (preg_match('/firefox|fxios/i', $ua)) $browser = 'Mozilla Firefox';
            elseif (preg_match('/safari/i', $ua) && !preg_match('/chrome/i', $ua)) $browser = 'Apple Safari';
            elseif (preg_match('/opera|opr/i', $ua)) $browser = 'Opera';

            $deviceInfo = $browser . ' on ' . $platform;
        }

        $prettyOldJson = !empty($oldValues) ? json_encode($oldValues, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : null;
        $prettyNewJson = !empty($newValues) ? json_encode($newValues, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : null;

        return view('logs.view_details', compact('log', 'changedFields', 'oldValues', 'newValues', 'deviceInfo', 'prettyOldJson', 'prettyNewJson'));
    }

    public function getLogDetails($id)
    {
        $log = \App\Models\Log::with('user')->findOrFail($id);
        
        $oldValues = $log->old_values ? json_decode($log->old_values, true) : [];
        $newValues = $log->new_values ? json_decode($log->new_values, true) : [];
        
        // Fields to exclude from display
        // $excludeFields = ['id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'deleted_at'];
        $excludeFields = ['id', 'created_by', 'updated_by', 'deleted_at'];
        $changedFields = [];
        
        // Helper function to format values
        $formatValue = function($value) {
            if (is_null($value) || $value === '') {
                return '-';
            }
            // Handle arrays and objects
            if (is_array($value)) {
                $count = count($value);
                return $count > 0 ? $count . ' item(s)' : '-';
            }
            if (is_object($value)) {
                return '-';
            }
            // Check if it's a date/datetime string
            if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
                try {
                    return \Carbon\Carbon::parse($value)->format('d-m-Y');
                } catch (\Exception $e) {
                    return $value;
                }
            }
            return $value;
        };
        
        if ($log->action_type == 'create') {
            foreach ($newValues as $key => $value) {
                if (in_array($key, $excludeFields)) continue;
                $changedFields[] = [
                    'field' => ucwords(str_replace('_', ' ', $key)),
                    'old' => '-',
                    'new' => $formatValue($value)
                ];
            }
        } elseif ($log->action_type == 'delete') {
            foreach ($oldValues as $key => $value) {
                if (in_array($key, $excludeFields)) continue;
                $changedFields[] = [
                    'field' => ucwords(str_replace('_', ' ', $key)),
                    'old' => $formatValue($value),
                    'new' => '-'
                ];
            }
        } else {
            $allKeys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));
            foreach ($allKeys as $key) {
                if (in_array($key, $excludeFields)) continue;
                
                $oldVal = $oldValues[$key] ?? null;
                $newVal = $newValues[$key] ?? null;
                
                if ($oldVal != $newVal) {
                    $changedFields[] = [
                        'field' => ucwords(str_replace('_', ' ', $key)),
                        'old' => $formatValue($oldVal),
                        'new' => $formatValue($newVal)
                    ];
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'changed_fields' => $changedFields,
            'module' => ucwords(str_replace(['_', '-'], ' ', $log->module)),
            'action_type' => ucwords(str_replace('_', ' ', $log->action_type)),
            'user_name' => $log->user->name ?? 'System',
            'created_at' => $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('d-m-Y h:i A') : '-',
        ]);
    } 
}

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
                $dates = explode(' to ', $request->date_range);
                if (count($dates) == 2) {
                    $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $endDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                } elseif (count($dates) == 1) {
                    $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $query->whereDate('created_at', $startDate);
                }
            }

            $logs = $query->get();
            $data = [];
            $count = 1;

            foreach ($logs as $log) {
                $badgeClass = match ($log->action_type) {
                    'create' => 'bg-success',
                    'update' => 'bg-info',
                    'delete' => 'bg-danger',
                    default => 'bg-secondary',
                };
                $actionDisplay = '<span class="badge ' . $badgeClass . '">' . ucfirst($log->action_type) . '</span>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'created_at' => $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('d-m-Y h:i A') : '-',
                    'user_name' => $log->user->name ?? 'System',
                    'module' => ucwords(str_replace(['_', '-'], ' ', $log->module)),
                    'action_type' => $actionDisplay,
                    'record' => $log->description ?? '-',
                    'action' => '<div class="d-flex align-items-center"><button class="btn btn-view" onclick="viewLogDetails(' . $log->id . ')"><i class="icon-base ri ri-eye-line"></i></button></div>',
                ];
            }

            return response()->json(['data' => $data]);
        }
        return view('logs.view');
    }

    public function getLogDetails($id)
    {
        $log = \App\Models\Log::with('user')->findOrFail($id);
        
        $oldValues = $log->old_values ? json_decode($log->old_values, true) : [];
        $newValues = $log->new_values ? json_decode($log->new_values, true) : [];
        
        // Fields to exclude from display
        $excludeFields = ['id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'deleted_at'];
        
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
            'action_type' => ucfirst($log->action_type),
            'user_name' => $log->user->name ?? 'System',
            'created_at' => $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('d-m-Y h:i A') : '-',
            'record' => $log->description ?? '-'
        ]);
    } 
}

<?php

use App\Models\User;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

if (!function_exists('createdByName')) {

    function createdByName($userId)
    {
        if (!$userId) {
            return 'System';
        }

        $user = User::find($userId);

        return $user?->name ?? 'Unknown';
    }
}
if (!function_exists('addLog')) {
    function addLog($action, $module, $table, $recordId, $oldData = null, $newData = null)
    {
        \App\Models\Log::create([
            'user_id'     => auth()->id(),
            'action_type' => $action,
            'module'      => $module,
            'table_name'  => $table,
            'record_id'   => $recordId,
            'old_values'  => $oldData ? json_encode($oldData) : null,
            'new_values'  => $newData ? json_encode($newData) : null,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
            'created_at'  => now(),
        ]);
    }
}

if (!function_exists('unauthorizedRedirect')) {
    function unauthorizedRedirect($message = 'Unauthorized action.')
    {
        return redirect(url(''))->with('danger', $message);
    }
}

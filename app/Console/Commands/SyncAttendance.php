<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\DB;

class SyncAttendance extends Command
{
    protected $signature = 'attendance:sync';

    protected $description = 'Auto sync attendance from eSSL device';

    public function handle()
    {
        try {
            $controller = new AttendanceController();
            $date = now()->format('Y-m-d');
            $response = $controller->getLogs($date, 'BJ2C180660790');
            $logs = $controller->parseLogs($response);
            $grouped = $controller->processAttendance($logs);
            $controller->formatAttendance($grouped, $date);
            /*  Fetch devices from DB
            $devices = DB::table('devices')->pluck('serial_number');
            foreach ($devices as $device) {
                $this->info("Syncing Device: ".$device);
                // Fetch logs
                $response = $controller->getLogs($date, $device);
                // Parse logs
                $logs = $controller->parseLogs($response);
                // Process attendance
                $grouped = $controller->processAttendance($logs);
                // Save attendance
                $controller->formatAttendance($grouped, $date);
            } */
            $this->info('Attendance synced successfully.');
        } catch (\Exception $e) {
            \Log::error('Attendance Sync Failed: '.$e->getMessage());
            $this->error($e->getMessage());
        }
    }
}

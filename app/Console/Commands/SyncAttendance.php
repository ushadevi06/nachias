<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\DB;

class SyncAttendance extends Command
{
    protected $signature = 'attendance:sync';

    protected $description = 'Auto sync attendance from eSSL device for today';

    public function handle()
    {
        $date = now()->format('Y-m-d');
        $this->info("Syncing attendance for: {$date}");

        try {
            $controller = new AttendanceController();

            $devices = DB::table('devices')->pluck('serial_number');

            if ($devices->isEmpty()) {
                $this->warn('No devices found in database.');
                return;
            }

            foreach ($devices as $serial_no) {
                $this->info("Syncing device: {$serial_no}");

                try {
                    $response = $controller->getLogs($date, $serial_no);
                    $logs     = $controller->parseLogs($response);
                    $grouped  = $controller->processAttendance($logs);
                    $controller->formatAttendance($grouped, $date, $serial_no);

                    $this->info("Device {$serial_no} synced successfully.");
                } catch (\Exception $e) {
                    \Log::error("Sync failed for device {$serial_no}: " . $e->getMessage());
                    $this->error("Device {$serial_no} failed: " . $e->getMessage());
                }
            }

            $this->info('All devices synced for today.');
        } catch (\Exception $e) {
            \Log::error('Attendance Sync Failed: ' . $e->getMessage());
            $this->error('Sync failed: ' . $e->getMessage());
        }
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\DB;

class SyncAttendance extends Command
{
    protected $signature = 'attendance:sync {--from= : Start date (YYYY-MM-DD)} {--to= : End date (YYYY-MM-DD)}';

    protected $description = 'Auto sync attendance from eSSL device for today or a specific date range';

    public function handle()
    {
        $fromDate = $this->option('from');
        $toDate = $this->option('to');

        $dates = [];
        if ($fromDate) {
            $start = new \DateTime($fromDate);
            $end = new \DateTime($toDate ?: $fromDate);
            $end->modify('+1 day'); // Include the end date

            $interval = new \DateInterval('P1D');
            $period = new \DatePeriod($start, $interval, $end);

            foreach ($period as $dt) {
                $dates[] = $dt->format('Y-m-d');
            }
        } else {
            $dates[] = now()->format('Y-m-d');
        }

        try {
            $controller = new AttendanceController();

            $devices = DB::table('devices')->pluck('serial_number');

            if ($devices->isEmpty()) {
                $this->warn('No devices found in database.');
                return;
            }

            foreach ($dates as $date) {
                $this->info("Syncing attendance for: {$date}");
                foreach ($devices as $serial_no) {
                    $this->info("Syncing device: {$serial_no} for date {$date}");

                    try {
                        $response = $controller->getLogs($date, $serial_no);
                        $logs     = $controller->parseLogs($response);
                        $grouped  = $controller->processAttendance($logs);
                        $controller->formatAttendance($grouped, $date, $serial_no);

                        $this->info("Device {$serial_no} synced successfully for {$date}.");
                    } catch (\Exception $e) {
                        \Log::error("Sync failed for device {$serial_no} on {$date}: " . $e->getMessage());
                        $this->error("Device {$serial_no} failed for {$date}: " . $e->getMessage());
                    }
                }
            }

            $this->info('Attendance sync completed.');
        } catch (\Exception $e) {
            \Log::error('Attendance Sync Failed: ' . $e->getMessage());
            $this->error('Sync failed: ' . $e->getMessage());
        }
    }
}
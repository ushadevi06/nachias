<?php

namespace App\Jobs;

use App\Http\Controllers\AttendanceController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncAttendanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $date;
    public $device;

    public function __construct($date, $device)
    {
        $this->date = $date;
        $this->device = $device;
    }

    public function handle()
    {
        $controller = new AttendanceController();

        $response = $controller->getLogs(
            $this->date,
            $this->device
        );

        $logs = $controller->parseLogs($response);

        $grouped = $controller->processAttendance($logs);

        $controller->formatAttendance(
            $grouped,
            $this->date,
            $this->device
        );
    }
}

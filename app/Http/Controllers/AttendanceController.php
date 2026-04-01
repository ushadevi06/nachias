<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $device = $request->get('device');
        $date = $request->get('date', date('Y-m-d'));
        $attendances = [];
        $lastSynced = null;
        $rows = [];

        if ($device === '192.168.203') {
            $rows = [
                ['name' => 'Ramesh Kumar', 'code' => 'EMP001', 'inTime' => '09:05 AM', 'outTime' => '06:10 PM', 'hours' => '9.1', 'status' => 'Present'],
                ['name' => 'Karthick', 'code' => 'EMP002', 'inTime' => '09:35 AM', 'outTime' => '05:55 PM', 'hours' => '8.3', 'status' => 'Late'],
                ['name' => 'Akash Mehta', 'code' => 'EMP003', 'inTime' => '08:50 AM', 'outTime' => '07:15 PM', 'hours' => '10.4', 'status' => 'Overtime'],
            ];
        } elseif ($device === '192.168.204') {
            $rows = [
                ['name' => 'Krithian', 'code' => 'EMP001', 'inTime' => '09:10 AM', 'outTime' => '06:05 PM', 'hours' => '8.9', 'status' => 'Present'],
                ['name' => 'Vinoth', 'code' => 'EMP002', 'inTime' => '09:20 AM', 'outTime' => '06:00 PM', 'hours' => '8.7', 'status' => 'Late'],
                ['name' => 'Nisha', 'code' => 'EMP003', 'inTime' => '08:40 AM', 'outTime' => '07:00 PM', 'hours' => '10.3', 'status' => 'Overtime'],
            ];
        }

        if (!empty($rows)) {
            foreach ($rows as $row) {
                $row['date'] = date('d-m-Y', strtotime($date));
                $attendances[] = $row;
            }
            $lastSynced = now()->format('d-m-Y H:i:s');
        }

        return view('attendances/view', compact('attendances', 'device', 'date', 'lastSynced'));
    }

    public function edit() {
        return view('attendances/edit');
    }
    public function view(){
        return view('attendances/view_details');
    }
}

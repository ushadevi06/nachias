<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Jobs\SyncAttendanceJob;
class AttendanceController extends Controller
{
    public function getLogs($date, $device)
    {
        $from = date('Y-m-d 00:00:00', strtotime($date));
        $to   = date('Y-m-d 23:59:59', strtotime($date));
        $xml = '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                <GetTransactionsLog xmlns="http://tempuri.org/">
                    <FromDateTime>2026-04-01 00:00:00</FromDateTime>
                    <ToDateTime>2026-04-30 23:59:59</ToDateTime>
                    <SerialNumber>'.$device.'</SerialNumber>
                    <UserName>test</UserName>
                    <UserPassword>Test@123</UserPassword>
                    <strDataList>123</strDataList>
                </GetTransactionsLog>
            </soap:Body>
        </soap:Envelope>';
        $response = Http::withOptions([
            'verify' => false,
            'connect_timeout' => 30,
        ])
        ->timeout(120)
        ->retry(3, 5000)
        ->withHeaders([
            'Content-Type' => 'text/xml; charset=utf-8',
            'SOAPAction' => '"http://tempuri.org/GetTransactionsLog"',
        ])
        ->withBody($xml, 'text/xml;')
        ->post(
            'http://106.51.22.181:85/iclock/webAPIservice.asmx'
        );
        \Log::info('eBio Response', [
            'status' => $response->status(),
            'successful' => $response->successful(),
            'body' => $response->body(),
        ]);
        if (!$response->successful()) {
            dd('API Error', $response->body());
        }
        return $response->body();
    }
    public function parseLogs($xmlResponse)
    {
        $logs = [];
        try {
            preg_match(
                '/<strDataList>(.*?)<\/strDataList>/s',
                $xmlResponse,
                $matches
            );
            if (!isset($matches[1])) {
                \Log::error('strDataList not found');
                return [];
            }
            $rawLogs = trim($matches[1]);
            if (empty($rawLogs)) {
                \Log::warning('No logs found');
                return [];
            }
            // Split rows
            $rows = preg_split('/\r\n|\r|\n/', $rawLogs);
            foreach ($rows as $row) {
                $row = trim($row);
                if (empty($row)) {
                    continue;
                }
                $parts = preg_split('/\s+/', $row);
                if (count($parts) < 3) {
                    \Log::warning('Invalid row: ' . $row);
                    continue;
                }
                $empCode = trim($parts[0]);
                $datetime = trim($parts[1] . ' ' . $parts[2]);
                $logs[] = [
                    'emp_code' => $empCode,
                    'datetime' => $datetime,
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Parse Error: ' . $e->getMessage());
        }
        return $logs;
    }
    public function processAttendance($logs)
    {
        $grouped = [];
        foreach ($logs as $log) {
            $date = date('Y-m-d', strtotime($log['datetime']));
            $emp  = $log['emp_code'];
            $grouped[$emp][$date][] = $log['datetime'];
        }
        return $grouped;
    }
    public function formatAttendance($grouped, $selectedDate, $serial_no)
    {
        $final = [];
        foreach ($grouped as $emp => $dates) {
            foreach ($dates as $date => $times) {
                sort($times);
                $in  = $times[0] ?? null;
                $out = count($times) > 1 ? end($times) : null;
                $hours = ($in && $out)
                    ? (strtotime($out) - strtotime($in)) / 3600
                    : 0;
                $status = $this->getStatus($date, $in, $out, $hours);
                $permissionMinutes = 0;
                if ($in) {
                    $allowedTime = strtotime($date . ' 09:05:00');
                    // FIXED
                    $inTimestamp = strtotime($in);
                    if ($inTimestamp > $allowedTime) {
                        $lateMinutes = ceil(
                            ($inTimestamp - $allowedTime) / 60
                        );
                        $permissionMinutes = ceil($lateMinutes / 15) * 15;
                    }
                }
                $existingAttendance = DB::table('attendances')
                    ->where('emp_code', $emp)
                    ->where('date', $date)
                    ->first();
                if ($existingAttendance && $existingAttendance->is_manual) {
                    $attendanceId = $existingAttendance->id;
                    $name = DB::table('users')
                        ->where('emp_id', $emp)
                        ->value('name');
                    $department_id = DB::table('users')
                    ->where('emp_id', $emp)
                    ->value('department_id');
                    $department = DB::table('departments')
                        ->where('id', $department_id)
                        ->value('department');
                    $final[] = [
                        'id' => $attendanceId,
                        'name' => $name ?? 'Unknown',
                        'code' => $emp,
                        'date' => date('d-m-Y', strtotime($date)),
                        'inTime' => $existingAttendance->in_time
                            ? date('h:i A', strtotime($existingAttendance->in_time))
                            : '-',
                        'outTime' => $existingAttendance->out_time
                            ? date('h:i A', strtotime($existingAttendance->out_time))
                            : '-',
                        'hours' => $existingAttendance->work_hours ?: '-',
                        'permission_hours' => $existingAttendance->permission_hours ?: '-',
                        'status' => $existingAttendance->status,
                        'department' => $department ?? '-'
                    ];
                    continue;
                }
                DB::table('attendances')->updateOrInsert(
                    [
                        'emp_code' => $emp,
                        'date' => $date,
                        'device_serial_number' => $serial_no
                    ],
                    [
                        'in_time' => $in,
                        'out_time' => $out,
                        'work_hours' => round($hours, 2),
                        'permission_hours' => $permissionMinutes,
                        'status' => $status,
                        'is_manual' => 0
                    ]
                );
                $name = DB::table('users')
                    ->where('emp_id', $emp)
                    ->value('name');
                $department_id = DB::table('users')
                    ->where('emp_id', $emp)
                    ->value('department_id');
                $department = DB::table('departments')
                    ->where('id', $department_id)
                    ->value('department');
                $attendanceId = DB::table('attendances')
                    ->where('emp_code', $emp)
                    ->where('date', $date)
                    ->value('id');
                $final[] = [
                    'id' => $attendanceId,
                    'name' => $name ?? 'Unknown',
                    'code' => $emp,
                    'date' => date('d-m-Y', strtotime($date)),
                    'inTime' => $in
                        ? date('h:i A', strtotime($in))
                        : '-',
                    'outTime' => $out
                        ? date('h:i A', strtotime($out))
                        : '-',
                    'hours' => $hours
                        ? round($hours, 2)
                        : '-',
                    'permission_hours' => $permissionMinutes ?: '-',
                    'status' => $status,
                    'department' => $department ?? '-'
                ];
            }
        }
        $allEmployees = DB::table('users')
            ->where('id', '!=', 1)
            ->pluck('emp_id')
            ->toArray();
        foreach ($allEmployees as $emp) {
            if (!isset($grouped[$emp][$selectedDate])) {
                $existingAttendance = DB::table('attendances')
                    ->where('emp_code', $emp)
                    ->where('date', $selectedDate)
                    ->first();
                if ($existingAttendance && $existingAttendance->is_manual) {
                    $name = DB::table('users')
                        ->where('emp_id', $emp)
                        ->value('name');
                    $department_id = DB::table('users')
                        ->where('emp_id', $emp)
                        ->value('department_id');
                    $department = DB::table('departments')
                        ->where('id', $department_id)
                        ->value('department');
                    $final[] = [
                        'id' => $existingAttendance->id,
                        'name' => $name ?? 'Unknown',
                        'code' => $emp,
                        'date' => date('d-m-Y', strtotime($selectedDate)),
                        'inTime' => $existingAttendance->in_time
                            ? date('h:i A', strtotime($existingAttendance->in_time))
                            : '-',
                        'outTime' => $existingAttendance->out_time
                            ? date('h:i A', strtotime($existingAttendance->out_time))
                            : '-',
                        'hours' => $existingAttendance->work_hours ?: '-',
                        'permission_hours' => $existingAttendance->permission_hours ?: '-',
                        'status' => $existingAttendance->status,
                        'department' => $department ?? '-'
                    ];
                    continue;
                }
                $status = $this->getStatus(
                    $selectedDate,
                    null,
                    null,
                    0
                );
                DB::table('attendances')->updateOrInsert(
                    [
                        'emp_code' => $emp,
                        'date' => $selectedDate,
                        'device_serial_number' => $serial_no
                    ],
                    [
                        'in_time' => null,
                        'out_time' => null,
                        'work_hours' => 0,
                        'permission_hours' => 0,
                        'status' => $status,
                        'is_manual' => 0
                    ]
                );
                $name = DB::table('users')
                    ->where('emp_id', $emp)
                    ->value('name');
                $department_id = DB::table('users')
                    ->where('emp_id', $emp)
                    ->value('department_id');
                $department = DB::table('departments')
                    ->where('id', $department_id)
                    ->value('department');
                $attendanceId = DB::table('attendances')
                    ->where('emp_code', $emp)
                    ->where('date', $selectedDate)
                    ->value('id');
                $final[] = [
                    'id' => $attendanceId,
                    'name' => $name ?? 'Unknown',
                    'code' => $emp,
                    'date' => date('d-m-Y', strtotime($selectedDate)),
                    'inTime' => '-',
                    'outTime' => '-',
                    'hours' => '-',
                    'status' => $status,
                    'department' => $department ?? '-'
                ];
            }
        }
        return $final;
    }
    private function getStatus($date, $inTime, $outTime, $hours)
    {
        $isSunday = date('w', strtotime($date)) == 0;
        $isHoliday = DB::table('declared_holidays')
            ->whereDate('date', $date)
            ->exists();
        if (!$inTime && !$outTime) {
            if ($isHoliday) {
                return 'Holiday';
            }
            if ($isSunday) {
                return 'Week Off';
            }
            return 'Absent';
        }
        if (!$inTime) {
            return 'Missing Time Card';
        }
        if (($isSunday || $isHoliday) && ($inTime || $outTime)) {
            return 'Overtime';
        }
        if ($hours > 9) {
            return 'Overtime';
        }
        if ($inTime) {
            $in = strtotime($inTime);
            $late = strtotime(
                date('Y-m-d', strtotime($inTime)) . ' 09:05:00'
            );
            if ($in > $late) {
                return 'Late';
            }
        }
        return 'Present';
    }
    public function sync(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'device' => 'required'
        ]);

        SyncAttendanceJob::dispatch(
            $request->date,
            $request->device
        );

        return response()->json([
            'success' => true,
            'message' => 'Attendance sync started in background'
        ]);
    }
    public function getAttendanceRecords(Request $request)
    {
        $date = $request->date;
        $device = $request->device;

        $records = DB::table('attendances')
            ->leftJoin('users', function ($join) {
                $join->on(
                    DB::raw('users.emp_id COLLATE utf8mb4_unicode_ci'),
                    '=',
                    DB::raw('attendances.emp_code COLLATE utf8mb4_unicode_ci')
                );
            })
            ->leftJoin(
                'departments',
                'users.department_id',
                '=',
                'departments.id'
            )
            ->select(
                'attendances.id',
                DB::raw('COALESCE(users.name, "Unknown") as name'),
                'attendances.emp_code as code',
                'attendances.date',
                'attendances.in_time',
                'attendances.out_time',
                'attendances.work_hours as hours',
                'attendances.status',
                DB::raw('COALESCE(departments.department, "-") as department')
            )
            ->whereDate('attendances.date', $date)
            ->when($device, function ($query) use ($device) {
                $query->where('attendances.device_serial_number', $device);
            })
            ->orderBy('departments.department')
            ->orderBy('users.name')
            ->get();

        return response()->json([
            'data' => $records
        ]);
    }
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
    public function saveHolidays(Request $request)
    {
        $holidays = $request->holidays ?? [];
        if (empty($holidays)) {
            return response()->json(['success' => true]);
        }
        $month = date('Y-m', strtotime($holidays[0]['date']));
        $dates = collect($holidays)->pluck('date')->toArray();
        DB::table('declared_holidays')
            ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$month])
            ->whereNotIn('date', $dates)
            ->delete();
        foreach ($holidays as $holiday) {
            DB::table('declared_holidays')->updateOrInsert(
                ['date' => $holiday['date']],
                [
                    'name' => $holiday['name'],
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );
        }
        return response()->json(['success' => true]);
    }
    public function getHolidays(Request $request)
    {
        $month = $request->month;
        $holidays = DB::table('declared_holidays')
            ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$month])
            ->get();
        return response()->json($holidays);
    }
    public function getStaffReport(Request $request)
    {
        $empCode = $request->emp_code;
        $month   = $request->month;
        $query = DB::table('attendances')
            ->join('users', function ($join) {
                $join->on(
                    DB::raw('users.emp_id COLLATE utf8mb4_unicode_ci'),
                    '=',
                    DB::raw('attendances.emp_code COLLATE utf8mb4_unicode_ci')
                );
            })
            ->select(
                'attendances.date',
                'users.name as employee',
                'attendances.emp_code as code',
                'attendances.in_time',
                'attendances.out_time',
                'attendances.work_hours as hours',
                'attendances.status'
            );
        if ($empCode) {
            $query->where('attendances.emp_code', $empCode);
        }
        if ($month) {
            $query->where('attendances.date', 'like', $month . '%');
        }
        $records = $query->orderBy('attendances.date','desc') ->orderBy('users.emp_id', 'desc')->get();
        return response()->json($records);
    }
    public function getEmployees()
    {
        $employees = DB::table('users')
            ->select('emp_id as code', 'name')
            ->where('id', '!=', 1)
            ->orderBy('id','desc')
            ->get();
        return response()->json($employees);
    }
    public function edit() {
        return view('attendances/edit');
    }
    public function view($id)
    {
        $attendance = DB::table('attendances')
            ->join('users', function ($join) {
                $join->on(
                    DB::raw('users.emp_id COLLATE utf8mb4_unicode_ci'),
                    '=',
                    DB::raw('attendances.emp_code COLLATE utf8mb4_unicode_ci')
                );
            })
            ->select(
                'attendances.*',
                'users.id as user_id',
                'users.name',
                'users.emp_id',
                'users.profile_image',
            )
            ->where('attendances.id', $id)
            ->first();
        if (!$attendance) {
            abort(404, 'Attendance not found');
        }
        // dd($attendance);
        return view('attendances.view_details', compact('attendance'));
    }
    public function updateAttendance(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'in_time' => 'required',
            'out_time' => 'required',
            'status' => 'required'
        ]);
        $attendance = DB::table('attendances')
            ->where('id', $request->id)
            ->first();
        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance not found'
            ]);
        }
        $date = $attendance->date;
        $in = $request->in_time
            ? $date . ' ' . date('H:i:s', strtotime($request->in_time))
            : null;
        $out = $request->out_time
            ? $date . ' ' . date('H:i:s', strtotime($request->out_time))
            : null;
        $hours = 0;
        if ($in && $out) {
            $hours = round(
                (strtotime($out) - strtotime($in)) / 3600,
                2
            );
            if ($hours < 0) {
                $hours = 0;
            }
        }
        $status = 'Present';
        if (!$in && !$out) {
            $status = 'Absent';
        } else {
            $isSunday = date('w', strtotime($date)) == 0;
            $isHoliday = DB::table('declared_holidays')
                ->whereDate('date', $date)
                ->exists();
            if ($isSunday || $isHoliday) {
                $status = 'Overtime';
            } else if ($hours > 9) {
                $status = 'Overtime';
            } else if ($in) {
                $inTimestamp = strtotime($in);
                $lateTime = strtotime($date . ' 09:05:00');
                if ($inTimestamp > $lateTime) {
                    $status = 'Late';
                } else {
                    $status = 'Present';
                }
            }
        }
        DB::table('attendances')
            ->where('id', $request->id)
            ->update([
                'in_time' => $in,
                'out_time' => $out,
                'work_hours' => $hours,
                'status' => $status,
                'is_manual' => 1,
                'updated_by' => auth()->id(),
                'manual_updated_at' => now(),
                'updated_at' => now()
            ]);
        return response()->json([
            'success' => true,
            'message' => 'Attendance updated successfully',
            'status' => $status
        ]);
    }
}

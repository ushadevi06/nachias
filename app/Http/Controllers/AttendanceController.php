<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Jobs\SyncAttendanceJob;
use App\Models\Device;
class AttendanceController extends Controller
{
    public function getLogs($date, $device, $toDate = null)
    {
        $from = date('Y-m-d 00:00:00', strtotime($date));
        $to   = date('Y-m-d 23:59:59', strtotime($toDate ?: $date));
        $xml = '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                <GetTransactionsLog xmlns="http://tempuri.org/">
                    <FromDateTime>'.$from.'</FromDateTime>
                    <ToDateTime>'.$to.'</ToDateTime>
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
            $empStr = (string)$emp;
            $employeeExistsAndActive = DB::table('users')->where('emp_id', $empStr)->where('id', '!=', 1)->where('status', 'Active')->whereNull('deleted_at')->exists();
            if (!$employeeExistsAndActive) {
                continue;
            }
            foreach ($dates as $date => $times) {
                sort($times);
                $in  = $times[0] ?? null;
                $out = count($times) > 1 ? end($times) : null;
                $existingAttendance = DB::table('attendances')->where('emp_code', $empStr)->where('date', $date)->first();
                if ($existingAttendance && $existingAttendance->is_manual) {
                    $attendanceId = $existingAttendance->id;
                    $name = DB::table('users')->where('emp_id', $empStr)->value('name');
                    $department_id = DB::table('users')->where('emp_id', $empStr)->value('department_id');
                    $department = DB::table('departments')->where('id', $department_id)->value('department');
                    $final[] = [
                        'id' => $attendanceId,
                        'name' => $name ?? 'Unknown',
                        'code' => $empStr,
                        'date' => date('d-m-Y', strtotime($date)),
                        'inTime' => $existingAttendance->in_time ? date('h:i A', strtotime($existingAttendance->in_time)) : '-',
                        'outTime' => $existingAttendance->out_time ? date('h:i A', strtotime($existingAttendance->out_time)) : '-',
                        'hours' => $existingAttendance->work_hours ?: '-',
                        'permission_hours' => $existingAttendance->permission_hours ?: '-',
                        'status' => $existingAttendance->status,
                        'department' => $department ?? '-'
                    ];
                    continue;
                }
                $dbIn = $existingAttendance ? $existingAttendance->in_time : null;
                $dbOut = $existingAttendance ? $existingAttendance->out_time : null;
                $allTimes = array_values(array_unique(array_filter([$in, $out, $dbIn, $dbOut])));
                if (!empty($allTimes)) {
                    sort($allTimes);
                    if (count($allTimes) == 1) {
                        $cutoffTime = strtotime($date . ' 13:00:00');
                        if (strtotime($allTimes[0]) >= $cutoffTime) {
                            $in = null;
                            $out = $allTimes[0];
                        } else {
                            $in = $allTimes[0];
                            $out = null;
                        }
                    } else {
                        $in = $allTimes[0];
                        $out = end($allTimes);
                        if ((strtotime($out) - strtotime($in)) < 600) {
                            $out = null;
                        }
                    }
                }
                $hours = ($in && $out) ? (strtotime($out) - strtotime($in)) / 3600 : 0;
                $status = $this->getStatus($date, $in, $out, $hours);
                $permissionMinutes = 0;
                if ($in) {
                    $allowedTime = strtotime($date . ' 09:05:00');
                    $inTimestamp = strtotime($in);
                    if ($inTimestamp > $allowedTime) {
                        $lateMinutes = ceil(
                            ($inTimestamp - $allowedTime) / 60
                        );
                        $permissionMinutes = ceil($lateMinutes / 15) * 15;
                    }
                }
                $deviceSerial = $serial_no;
                if (!$deviceSerial && $existingAttendance) {
                    $deviceSerial = $existingAttendance->device_serial_number;
                }
                if (!$deviceSerial) {
                    $deviceSerial = DB::table('users')->where('emp_id', $empStr)->value('device');
                }
                DB::table('attendances')->updateOrInsert(
                    [
                        'emp_code' => $empStr,
                        'date' => $date,
                    ],
                    [
                        'device_serial_number' => $deviceSerial,
                        'in_time' => $in,
                        'out_time' => $out,
                        'work_hours' => round($hours, 2),
                        'permission_hours' => $permissionMinutes,
                        'status' => $status,
                        'is_manual' => 0
                    ]
                );
                $name = DB::table('users')->where('emp_id', $empStr)->value('name');
                $department_id = DB::table('users')->where('emp_id', $empStr)->value('department_id');
                $department = DB::table('departments')
                    ->where('id', $department_id)
                    ->value('department');
                $attendanceId = DB::table('attendances')
                    ->where('emp_code', $empStr)
                    ->where('date', $date)
                    ->value('id');
                $final[] = [
                    'id' => $attendanceId,
                    'name' => $name ?? 'Unknown',
                    'code' => $empStr,
                    'date' => date('d-m-Y', strtotime($date)),
                    'inTime' => $in ? date('h:i A', strtotime($in)) : '-',
                    'outTime' => $out ? date('h:i A', strtotime($out)) : '-',
                    'hours' => $hours ? round($hours, 2) : '-',
                    'permission_hours' => $permissionMinutes ?: '-',
                    'status' => $status,
                    'department' => $department ?? '-'
                ];
            }
        }
        $allEmployees = DB::table('users')->where('id', '!=', 1)->where('status', 'Active')->whereNull('deleted_at')->pluck('emp_id')->toArray();
        $datesToMark = [];
        for ($i = 6; $i >= 0; $i--) {
            $datesToMark[] = date('Y-m-d', strtotime("-$i days"));
        }
        $selectedDates = is_array($selectedDate) ? $selectedDate : [$selectedDate];
        $datesToMark = array_unique(array_merge($datesToMark, $selectedDates));
        
        foreach ($datesToMark as $dateKey) {
            foreach ($allEmployees as $emp) {
                $empStr = (string)$emp;
                if (!isset($grouped[$empStr][$dateKey])) {
                    $existingAttendance = DB::table('attendances')->where('emp_code', $empStr)->where('date', $dateKey)->first();
                    if ($existingAttendance) {
                        if ($existingAttendance->is_manual || $existingAttendance->in_time || $existingAttendance->out_time) {
                            $name = DB::table('users')->where('emp_id', $empStr)->value('name');
                            $department_id = DB::table('users')->where('emp_id', $empStr)->value('department_id');
                            $department = DB::table('departments')->where('id', $department_id)->value('department');
                            $final[] = [
                                'id' => $existingAttendance->id,
                                'name' => $name ?? 'Unknown',
                                'code' => $empStr,
                                'date' => date('d-m-Y', strtotime($dateKey)),
                                'inTime' => $existingAttendance->in_time ? date('h:i A', strtotime($existingAttendance->in_time)) : '-',
                                'outTime' => $existingAttendance->out_time ? date('h:i A', strtotime($existingAttendance->out_time)) : '-',
                                'hours' => $existingAttendance->work_hours ?: '-',
                                'permission_hours' => $existingAttendance->permission_hours ?: '-',
                                'status' => $existingAttendance->status,
                                'department' => $department ?? '-'
                            ];
                            continue;
                        }
                    }
                    $status = $this->getStatus(
                        $dateKey,
                        null,
                        null,
                        0
                    );
                    $deviceSerial = $serial_no;
                    if (!$deviceSerial && $existingAttendance) {
                        $deviceSerial = $existingAttendance->device_serial_number;
                    }
                    if (!$deviceSerial) {
                        $deviceSerial = DB::table('users')->where('emp_id', $empStr)->value('device');
                    }
                    DB::table('attendances')->updateOrInsert(
                        [
                            'emp_code' => $empStr,
                            'date' => $dateKey,
                        ],
                        [
                            'device_serial_number' => $deviceSerial,
                            'in_time' => null,
                            'out_time' => null,
                            'work_hours' => 0,
                            'permission_hours' => 0,
                            'status' => $status,
                            'is_manual' => 0
                        ]
                    );
                    $name = DB::table('users')->where('emp_id', $empStr)->value('name');
                    $department_id = DB::table('users')->where('emp_id', $empStr)->value('department_id');
                    $department = DB::table('departments')->where('id', $department_id)->value('department');
                    $attendanceId = DB::table('attendances')->where('emp_code', $empStr)->where('date', $dateKey)->value('id');
                    $final[] = [
                        'id' => $attendanceId,
                        'name' => $name ?? 'Unknown',
                        'code' => $empStr,
                        'date' => date('d-m-Y', strtotime($dateKey)),
                        'inTime' => '-',
                        'outTime' => '-',
                        'hours' => '-',
                        'status' => $status,
                        'department' => $department ?? '-'
                    ];
                }
            }
        }
        return $final;
    }
    private function getStatus($date, $inTime, $outTime, $hours)
    {
        $isSunday = date('w', strtotime($date)) == 0;
        $isHoliday = DB::table('declared_holidays')->whereDate('date', $date)->exists();
        if (!$inTime && !$outTime) {
            if ($isHoliday) {
                return 'Holiday';
            }
            if ($isSunday) {
                return 'Week Off';
            }
            return 'Absent';
        }
        $isToday = ($date === date('Y-m-d'));
        if (empty($inTime) && !empty($outTime)) {
            return 'Missing Time Card';
        }

        if (!empty($inTime) && empty($outTime)) {
            if (!$isToday) {
                return 'Missing Time Card';
            }
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

        $attendances = DB::table('attendances')
            ->select(
                'id',
                'emp_code as code',
                'date',
                'in_time',
                'out_time',
                'work_hours as hours',
                'status'
            )
            ->whereDate('date', $date)
            ->when($device, function ($query) use ($device) {
                $query->where('device_serial_number', $device);
            })
            ->get();

        if ($attendances->isNotEmpty()) {
            $empCodes = $attendances->pluck('code')->unique()->toArray();

            $users = DB::table('users')->leftJoin('departments', 'users.department_id', '=', 'departments.id')->whereIn('users.emp_id', $empCodes)->select('users.emp_id', 'users.name', 'departments.department')->get()->keyBy('emp_id');

            $attendances->transform(function ($item) use ($users) {
                $user = $users[$item->code] ?? null;
                $item->name = $user && $user->name ? $user->name : 'Unknown';
                $item->department = $user && $user->department ? $user->department : '-';
                return $item;
            });

            $attendances = $attendances->sortBy([
                ['department', 'asc'],
                ['name', 'asc']
            ])->values();
        }

        return response()->json([
            'data' => $attendances
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
        $devices = Device::get();
        return view('attendances/view', compact('attendances', 'device', 'date', 'lastSynced', 'devices'));
    }
    public function saveHolidays(Request $request)
    {
        $holidays = $request->holidays ?? [];
        if (empty($holidays)) {
            return response()->json(['success' => true]);
        }
        $month = date('Y-m', strtotime($holidays[0]['date']));
        $dates = collect($holidays)->pluck('date')->toArray();
        DB::table('declared_holidays')->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$month])->whereNotIn('date', $dates)->delete();
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
        $holidays = DB::table('declared_holidays')->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$month])->get();
        return response()->json($holidays);
    }
    public function getStaffReport(Request $request)
    {

        $empCode = $request->emp_code;
        $month   = $request->month;
        $fromDate = $request->from_date;
        $toDate   = $request->to_date;

        $query = DB::table('attendances')
            ->select(
                'date',
                'emp_code as code',
                'in_time',
                'out_time',
                'work_hours as hours',
                'status'
            );

        if ($empCode && $empCode !== 'all') {
            $query->where('emp_code', $empCode);
        }
        if ($month) {
            $query->where('date', 'like', $month . '%');
        } elseif ($fromDate && $toDate) {
            $query->whereBetween('date', [$fromDate, $toDate]);
        }

        $attendances = $query->orderBy('date', 'desc')->orderBy('emp_code', 'desc')->get();

        if ($attendances->isNotEmpty()) {
            $empCodes = $attendances->pluck('code')->unique()->toArray();

            $users = DB::table('users')
                ->whereIn('emp_id', $empCodes)
                ->pluck('name', 'emp_id');

            $attendances->transform(function ($item) use ($users) {
                $item->employee = $users[$item->code] ?? 'Unknown';
                return $item;
            });
        }

        return response()->json($attendances);
    }
    public function getEmployees()
    {
        $employees = DB::table('users')->select('emp_id as code', 'name')->where('id', '!=', 1)->orderBy('id','desc')->get();
        return response()->json($employees);
    }

    public function exportStaffReport(Request $request)
    {
        $empCode = $request->emp_code;
        $month   = $request->month;
        $fromDate = $request->from_date;
        $toDate   = $request->to_date;

        $fileName = 'Staff_Wise_Report';
        if ($empCode && $empCode !== 'all') {
            $empName = DB::table('users')->where('emp_id', $empCode)->value('name');
            $fileName .= '_' . str_replace(' ', '_', $empName);
        }
        if ($fromDate && $toDate) {
            $fileName .= '_' . $fromDate . '_to_' . $toDate;
        } elseif ($month) {
            $fileName .= '_' . $month;
        }

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\StaffReportExport($empCode, $month, $fromDate, $toDate),
            $fileName . '.xlsx'
        );
    }
    public function edit() {
        return view('attendances/edit');
    }
    public function view($id)
    {
        $attendance = DB::table('attendances')
            ->where('id', $id)
            ->first();

        if (!$attendance) {
            abort(404, 'Attendance not found');
        }

        $user = DB::table('users')
            ->where('emp_id', $attendance->emp_code)
            ->select('id as user_id', 'name', 'emp_id', 'profile_image')
            ->first();

        if ($user) {
            $attendance->user_id = $user->user_id;
            $attendance->name = $user->name;
            $attendance->emp_id = $user->emp_id;
            $attendance->profile_image = $user->profile_image;
        } else {
            $attendance->user_id = null;
            $attendance->name = 'Unknown';
            $attendance->emp_id = $attendance->emp_code;
            $attendance->profile_image = null;
        }

        return view('attendances.view_details', compact('attendance'));
    }
    public function updateAttendance(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'in_time' => 'required',
            'out_time' => 'nullable',
            'status' => 'required'
        ]);
        $attendance = DB::table('attendances')->where('id', $request->id)->first();
        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance not found'
            ]);
        }
        $date = $attendance->date;
        $in = $request->in_time ? $date . ' ' . date('H:i:s', strtotime($request->in_time)) : null;
        $out = $request->out_time ? $date . ' ' . date('H:i:s', strtotime($request->out_time)) : null;
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
            $isHoliday = DB::table('declared_holidays')->whereDate('date', $date)->exists();
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

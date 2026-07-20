<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmployeeAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('login');
        }

        $emp_id = $user->emp_id;
        $today = date('Y-m-d');
        $startOfWeek = date('Y-m-d', strtotime('monday this week'));
        $startOfMonth = date('Y-m-01');

        $todayRecord = DB::table('attendances')->where('emp_code', $emp_id)->where('date', $today)->first();
        
        $todayHours = $todayRecord ? (float) ($todayRecord->work_hours ?? 0) : 0;

        $monthlyRecords = DB::table('attendances')->where('emp_code', $emp_id)->whereBetween('date', [$startOfMonth, $today])->get();

        $monthlyOvertimeDays = 0;
        $monthlyLateCount = 0;
        $monthlyAbsentCount = 0;

        foreach ($monthlyRecords as $rec) {
            if ($rec->status === 'Overtime') {
                $monthlyOvertimeDays++;
            }
            if ($rec->status === 'Late') {
                $monthlyLateCount++;
            }
            if ($rec->status === 'Absent') {
                $monthlyAbsentCount++;
            }
        }

        return view('employee_attendance.index', compact(
            'user',
            'todayRecord',
            'todayHours',
            'monthlyLateCount',
            'monthlyAbsentCount',
            'monthlyOvertimeDays'
        ));
    }

    public function getAttendanceData(Request $request)
    {
        $user = Auth::user();
        $emp_id = $user->emp_id;

        $query = DB::table('attendances')->where('emp_code', $emp_id);

        if ($request->filled('month_year')) {
            $m = date('m', strtotime($request->month_year));
            $y = date('Y', strtotime($request->month_year));
            $query->whereYear('date', $y)->whereMonth('date', $m);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->orderBy('date', 'desc')->get();

        $data = [];
        foreach ($records as $r) {
            $data[] = [
                'date' => date('d-m-Y', strtotime($r->date)),
                'in_time' => $r->in_time ? date('h:i A', strtotime($r->in_time)) : '-',
                'status' => $r->status,
                'out_time' => $r->out_time ? date('h:i A', strtotime($r->out_time)) : '-',
                'late' => isset($r->late_time) && $r->late_time ? $r->late_time : '-',
                'overtime' => isset($r->ot_hours) && $r->ot_hours ? $r->ot_hours : '-',
                'working_hours' => isset($r->work_hours) && $r->work_hours ? $r->work_hours : '-',
            ];
        }

        return response()->json(['data' => $data]);
    }

    public function getKpiCounts(Request $request)
    {
        $user = Auth::user();
        $emp_id = $user->emp_id;

        $query = DB::table('attendances')->where('emp_code', $emp_id);

        if ($request->filled('month_year')) {
            $m = date('m', strtotime($request->month_year));
            $y = date('Y', strtotime($request->month_year));
            $query->whereYear('date', $y)->whereMonth('date', $m);
        } else {
            $query->where('date', '>=', date('Y-m-01'))->where('date', '<=', date('Y-m-d'));
        }

        $records = $query->get();

        $lateCount = 0;
        $absentCount = 0;
        $overtimeDays = 0;

        foreach ($records as $rec) {
            if ($rec->status === 'Late') $lateCount++;
            if ($rec->status === 'Absent') $absentCount++;
            if ($rec->status === 'Overtime') $overtimeDays++;
        }

        return response()->json([
            'lateCount' => $lateCount,
            'absentCount' => $absentCount,
            'overtimeDays' => $overtimeDays,
        ]);
    }

    private function timeToDecimal($time)
    {
        if (empty($time) || strpos($time, ':') === false) return 0;
        list($h, $m) = explode(':', $time);
        return (int)$h + ((int)$m / 60);
    }
}

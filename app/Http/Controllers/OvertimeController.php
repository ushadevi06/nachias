<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use Carbon\Carbon;

class OvertimeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Attendance::leftJoin(
                    'users',
                    DB::raw('users.emp_id COLLATE utf8mb4_unicode_ci'),
                    '=',
                    DB::raw('attendances.emp_code COLLATE utf8mb4_unicode_ci')
                )
                ->select(
                    'attendances.*',
                    'users.name as employee_name'
                );
            if (auth()->user()->id != 1) {
                $query->where(
                    'attendances.emp_code',
                    auth()->user()->emp_id
                );
            }
            if (!empty($request->month)) {
                $monthYear = Carbon::createFromFormat(
                    'Y-m',
                    $request->month
                );
                $query->whereMonth(
                        'attendances.date',
                        $monthYear->month
                    )
                    ->whereYear(
                        'attendances.date',
                        $monthYear->year
                    );
            }
            if (
                $request->has('search') &&
                !empty($request->input('search')['value'])
            ) {
                $search = $request->input('search')['value'];
                $query->where(function ($q) use ($search) {
                    $q->where(
                            'attendances.emp_code',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'users.name',
                            'like',
                            "%{$search}%"
                        );
                });
            }
            $attendanceRows = $query
                ->orderBy('attendances.date', 'desc')
                ->get();
            $finalData = [];
            $i = 1;
            foreach ($attendanceRows as $index => $att) {
                $date = Carbon::parse($att->date);
                $hours = 0;
                if ($att->in_time && $att->out_time) {
                    $in = Carbon::parse($att->in_time);
                    $out = Carbon::parse($att->out_time);
                    $hours = $out->diffInMinutes($in) / 60;
                }
                $isExtraHours = $hours > 9;
                $isSundayWorked =
                    $date->isSunday() &&
                    $att->in_time &&
                    $att->out_time;
                $isHolidayWorked =
                    DB::table('declared_holidays')
                        ->whereDate('date', $date->format('Y-m-d'))
                        ->exists()
                    &&
                    $att->in_time &&
                    $att->out_time;
                if (
                    !$isExtraHours &&
                    !$isSundayWorked &&
                    !$isHolidayWorked
                ) {
                    continue;
                }
                $otHours = '-';
                if ($att->in_time && $att->out_time) {
                    $totalMinutes = (
                        strtotime($att->out_time) -
                        strtotime($att->in_time)
                    ) / 60;
                    $extraMinutes = $totalMinutes - (9 * 60);
                    if ($extraMinutes > 0) {
                        $hours = floor($extraMinutes / 60);
                        $minutes = $extraMinutes % 60;
                        if ($hours > 0 && $minutes > 0) {
                            $otHours = $hours . ' hr ' . $minutes . ' mins';
                        } elseif ($hours > 0) {
                            $otHours = $hours . ' hr';
                        } else {
                            $otHours = $minutes . ' mins';
                        }
                    } else {
                        $otHours = '0 mins';
                    }
                }
                $action = '<div class="button-box d-flex gap-1">';
                if(auth()->id()== 1 || auth()->user()->can('view_details overtime')){
                    $action .= '
                        <a href="' . route(
                            'view_overtime',
                            [
                                'date' => $date->format('Y-m-d'),
                                'emp_code' => $att->emp_code
                            ]
                        ) . '" class="btn btn-view">
                            <i class="icon-base ri ri-eye-line"></i>
                        </a>';
                }
                if(auth()->id()== 1 || auth()->user()->can('edit overtime')){
                    $action .= '
                        <button type="button" class="btn btn-edit editOtBtn" data-id="' . $att->id . '" data-emp="' . $att->emp_code . '" data-date="' . $date->format('Y-m-d') . '" data-in="' . ($att->in_time ? Carbon::parse($att->in_time)->format('H:i') : '' ) . '" data-out="' . ($att->out_time ? Carbon::parse($att->out_time)->format('H:i') : '' ) . '" data-status="' . $att->status . '" >
                            <i class="icon-base ri ri-pencil-line"></i>
                        </button>';
                }
                $action .=  '</div>';
                $finalData[] = [
                    'DT_RowIndex' => $i++,
                    'date' => $date->format('d-m-Y'),
                    'employee' =>
                        $att->employee_name.
                        '(' .
                        $att->emp_code .
                        ')',
                    'in_time' => $att->in_time
                        ? Carbon::parse($att->in_time)
                            ->format('h:i A')
                        : '-',
                    'out_time' => $att->out_time
                        ? Carbon::parse($att->out_time)
                            ->format('h:i A')
                        : '-',
                    'ot_hours' => $otHours,
                    'action' => $action
                ];
            }

            $start = intval($request->input('start', 0));
            $length = intval($request->input('length', 10));
            $totalFiltered = count($finalData);

            if ($length > 0) {
                $finalData = array_slice($finalData, $start, $length);
            }

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalFiltered,
                'recordsFiltered' => $totalFiltered,
                'data' => $finalData
            ]);
        }
        return view('overtime.view');
    }
    public function edit($date)
    {
        $records = Attendance::whereDate('date', $date)
            // ->where('department', $department)
            ->get();
        return view('overtime.add', compact('records', 'date'));
    }
    public function view($date, $emp_code)
    {
        $record = Attendance::leftJoin(
                'users',
                DB::raw('CONVERT(users.emp_id USING utf8mb4)'),
                '=',
                DB::raw('CONVERT(attendances.emp_code USING utf8mb4)')
            )
            ->whereDate('attendances.date', $date)
            ->where('attendances.emp_code', $emp_code)
            ->select(
                'attendances.*',
                'users.name as employee_name'
            )
            ->first();
        if (!$record) {
            abort(404);
        }
        $hours = 0;
        if ($record->in_time && $record->out_time) {
            $in = \Carbon\Carbon::parse($record->in_time);
            $out = \Carbon\Carbon::parse($record->out_time);
            $hours = $out->diffInMinutes($in) / 60;
        }
        $isSunday = \Carbon\Carbon::parse($record->date)
            ->isSunday();
        $isHoliday = DB::table('declared_holidays')
            ->whereDate('date', $record->date)
            ->exists();
        $totalMinutes = 0;
        if ($record->in_time && $record->out_time) {
            $totalMinutes = $out->diffInMinutes($in);
        }
        if ($isSunday || $isHoliday) {
            $extraMinutes = $totalMinutes;
        } else {
            $extraMinutes = $totalMinutes - (9 * 60);
        }
        if ($extraMinutes < 0) {
            $extraMinutes = 0;
        }
        $hoursPart = floor($extraMinutes / 60);
        $minutesPart = $extraMinutes % 60;
        if ($hoursPart > 0 && $minutesPart > 0) {
            $otHours = $hoursPart . ' hr ' . $minutesPart . ' mins';
        } elseif ($hoursPart > 0) {
            $otHours = $hoursPart . ' hr';
        } elseif ($minutesPart > 0) {
            $otHours = $minutesPart . ' mins';
        } else {
            $otHours = '0 mins';
        }
        $lateHours = $record->status == 'Late'
            ? 1
            : 0;
        return view(
            'overtime.view_details',
            compact(
                'record',
                'otHours',
                'lateHours'
            )
        );
    }
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'in_time' => 'required',
            'out_time' => 'required'
        ]);
        $attendance = Attendance::find($request->id);
        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance not found'
            ]);
        }
        $date = $attendance->date;
        $in = $request->in_time
            ? $date . ' ' . $request->in_time . ':00'
            : null;
        $out = $request->out_time
            ? $date . ' ' . $request->out_time . ':00'
            : null;
        $workHours = 0;
        if ($in && $out) {
            $workHours = round(
                (
                    strtotime($out) -
                    strtotime($in)
                ) / 3600,
                2
            );
            if ($workHours < 0) {
                $workHours = 0;
            }
        }
        $oldData = $attendance->only([
            'in_time',
            'out_time',
            'work_hours',
            'is_manual',
            'updated_by',
            'manual_updated_at'
        ]);
        $newData = [
            'in_time' => $in,
            'out_time' => $out,
            'work_hours' => $workHours,
            'is_manual' => 1,
            'updated_by' => auth()->id(),
            'manual_updated_at' => now()
        ];
        $attendance->update($newData);
        addLog('update', 'Overtime', 'attendances', $request->id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'message' => 'Overtime updated successfully'
        ]);
    }
}

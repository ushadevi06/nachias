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
                    'users.emp_id',
                    '=',
                    'attendances.emp_code'
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

            $startDate = null;
            $endDate = null;
            if (!empty($request->month)) {
                $monthYear = Carbon::createFromFormat(
                    'Y-m',
                    $request->month
                );
                $startDate = $monthYear->copy()->startOfMonth()->toDateString();
                $endDate = $monthYear->copy()->endOfMonth()->toDateString();
                $query->whereBetween('attendances.date', [$startDate, $endDate]);
            }

            $query->whereNotNull('attendances.in_time')
                  ->whereNotNull('attendances.out_time');

            if (!empty($request->status)) {
                $query->where('attendances.ot_status', $request->status);
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

            // Preload declared holidays in one single query to eliminate N+1 loop queries
            $holidayQuery = DB::table('declared_holidays');
            if ($startDate && $endDate) {
                $holidayQuery->whereBetween('date', [$startDate, $endDate]);
            }
            $holidayMap = $holidayQuery->pluck('date')
                ->map(fn($d) => substr($d, 0, 10))
                ->flip()
                ->toArray();

            $attendanceRows = $query
                ->orderBy('attendances.date', 'desc')
                ->get();

            // Fast candidate filtering without building HTML for thousands of unrendered rows
            $validRows = [];
            foreach ($attendanceRows as $att) {
                $dateStr = substr($att->date, 0, 10);
                $inTs = strtotime($att->in_time);
                $outTs = strtotime($att->out_time);
                $totalMinutes = ($outTs - $inTs) / 60;

                $isExtraHours = $totalMinutes > 540;
                $dayOfWeek = (int)date('w', strtotime($dateStr));
                $isSundayWorked = ($dayOfWeek === 0);
                $isHolidayWorked = isset($holidayMap[$dateStr]);

                if (!$isExtraHours && !$isSundayWorked && !$isHolidayWorked) {
                    continue;
                }

                $validRows[] = [$att, $inTs, $outTs, $totalMinutes, $dateStr];
            }

            $start = intval($request->input('start', 0));
            $length = intval($request->input('length', 10));
            $totalFiltered = count($validRows);

            $pageRows = ($length > 0) ? array_slice($validRows, $start, $length) : $validRows;

            $finalData = [];
            $i = $start + 1;
            $canView = auth()->id() == 1 || auth()->user()->can('view_details overtime');
            $canEdit = auth()->id() == 1 || auth()->user()->can('edit overtime');

            foreach ($pageRows as $item) {
                [$att, $inTs, $outTs, $totalMinutes, $dateStr] = $item;

                $extraMinutes = $totalMinutes - (9 * 60);
                if ($extraMinutes > 0) {
                    $hours = (int)floor($extraMinutes / 60);
                    $minutes = (int)($extraMinutes % 60);
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

                $action = '<div class="button-box d-flex gap-1">';
                if ($canView) {
                    $action .= '
                        <a href="' . route(
                            'view_overtime',
                            [
                                'date' => $dateStr,
                                'emp_code' => $att->emp_code
                            ]
                        ) . '" class="btn btn-view">
                            <i class="icon-base ri ri-eye-line"></i>
                        </a>';
                }
                if ($canEdit) {
                    $action .= '
                        <button type="button" class="btn btn-edit editOtBtn" data-id="' . $att->id . '" data-emp="' . $att->emp_code . '" data-date="' . $dateStr . '" data-in="' . date('H:i', $inTs) . '" data-out="' . date('H:i', $outTs) . '" data-status="' . $att->status . '" data-ot-status="' . ($att->ot_status ?: 'Pending') . '" >
                            <i class="icon-base ri ri-pencil-line"></i>
                        </button>';
                }
                $action .=  '</div>';

                $otStatus = $att->ot_status ?: 'Pending';

                if ($canEdit) {
                    $options = '';
                    if ($otStatus !== 'Approved') {
                        $options .= '<option value="Pending" ' . ($otStatus === 'Pending' ? 'selected' : '') . '>Pending</option>';
                    }
                    $options .= '<option value="Approved" ' . ($otStatus === 'Approved' ? 'selected' : '') . '>Approved</option>';
                    $options .= '<option value="Not Approved" ' . ($otStatus === 'Not Approved' ? 'selected' : '') . '>Not Approved</option>';

                    $statusHtml = '
                        <select class="select2 form-select form-select-sm ot-status-select" 
                                data-id="' . $att->id . '" 
                                data-prev="' . $otStatus . '" 
                                style="min-width: 140px; cursor: pointer;">
                            ' . $options . '
                        </select>
                    ';
                } else {
                    $statusHtml = '<span>' . $otStatus . '</span>';
                }

                $finalData[] = [
                    'DT_RowIndex' => $i++,
                    'date' => date('d-m-Y', strtotime($dateStr)),
                    'employee' =>
                        $att->employee_name .
                        '(' .
                        $att->emp_code .
                        ')',
                    'in_time' => $att->in_time
                        ? date('h:i A', $inTs)
                        : '-',
                    'out_time' => $att->out_time
                        ? date('h:i A', $outTs)
                        : '-',
                    'ot_hours' => $otHours,
                    'status' => $statusHtml,
                    'action' => $action
                ];
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
            'out_time' => 'nullable',
            'ot_status' => 'nullable|in:Pending,Approved,Not Approved'
        ]);
        $attendance = Attendance::find($request->id);
        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance not found'
            ]);
        }
        if ($attendance->ot_status === 'Approved' && $request->ot_status === 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'Once approved, overtime cannot be reverted back to Pending.'
            ], 422);
        }
        $date = $attendance->date;
        $in = $request->in_time
            ? $date . ' ' . $request->in_time . (strlen($request->in_time) <= 5 ? ':00' : '')
            : $attendance->in_time;
        $out = $request->out_time
            ? $date . ' ' . $request->out_time . (strlen($request->out_time) <= 5 ? ':00' : '')
            : $attendance->out_time;
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
            'ot_status',
            'is_manual',
            'updated_by',
            'manual_updated_at'
        ]);
        $newData = [
            'in_time' => $in,
            'out_time' => $out,
            'work_hours' => $workHours,
            'ot_status' => $request->ot_status ?? $attendance->ot_status ?? 'Pending',
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

    public function updateStatus(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('edit overtime')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $request->validate([
            'id' => 'required|exists:attendances,id',
            'ot_status' => 'required|in:Pending,Approved,Not Approved'
        ]);

        $attendance = Attendance::findOrFail($request->id);
        if ($attendance->ot_status === 'Approved' && $request->ot_status === 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'Once approved, overtime cannot be reverted back to Pending.'
            ], 422);
        }
        $oldData = ['ot_status' => $attendance->ot_status];
        $newData = [
            'ot_status' => $request->ot_status,
            'updated_by' => auth()->id()
        ];
        $attendance->update($newData);
        addLog('update', 'Overtime Status', 'attendances', $request->id, $oldData, $newData);

        return response()->json([
            'success' => true,
            'message' => 'Overtime status updated to ' . $request->ot_status
        ]);
    }
}

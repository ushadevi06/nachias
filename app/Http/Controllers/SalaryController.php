<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MonthlyPayrollExport;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('salary_generations')
                ->join('users', function ($join) {
                    $join->on(
                        DB::raw('salary_generations.employee_id COLLATE utf8mb4_unicode_ci'),
                        '=',
                        DB::raw('users.emp_id COLLATE utf8mb4_unicode_ci')
                    );
                })
                ->select(
                    'salary_generations.*',
                    'users.name',
                    'users.emp_id'
                )
                ->orderBy('salary_generations.id', 'desc');
            if (
                $request->has('search') &&
                !empty($request->input('search')['value'])
            ) {
                $search = $request->input('search')['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.emp_id', 'like', "%{$search}%")
                    ->orWhere(function ($q) use ($search) {
                        $q->where(
                            DB::raw("
                                CONCAT(
                                    salary_generations.salary_month,
                                    ' ',
                                    salary_generations.salary_year
                                )
                            "),
                            'like',
                            "%{$search}%"
                        );
                    })
                    ->orWhere('salary_generations.status', 'like', "%{$search}%");
                });
            }
            if (!empty($request->status)) {
                $query->where(
                    'salary_generations.status',
                    $request->status
                );
            }
            if (!empty($request->month)) {
                $date = Carbon::parse($request->month);
                $query->where('salary_generations.salary_month', $date->month)->where('salary_generations.salary_year', $date->year);
            }
            $totalRecords = $query->count();
            $filteredRecords = $totalRecords;
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            if ($length != -1) {
                $query->skip($start)->take($length);
            }
            $salaries = $query->get();
            $data = [];
            $count = 1;
            foreach ($salaries as $salary) {
                $checkbox = '';
                if($salary->status == 'Draft') {
                    $checkbox = '<input type="checkbox" class="salary-checkbox" value="'.$salary->id.'">';
                }
                if($salary->status == 'Paid') {
                    $statusBadge = '
                        <span class="badge bg-success">
                            Paid
                        </span>
                    ';
                } else {
                    $statusBadge = '
                        <span class="badge bg-warning">
                            Draft
                        </span>
                    ';
                }
                $action = '<div class="d-inline-block text-nowrap">';
                if ((auth()->id() == 1 || auth()->user()->can('edit monthly-payroll')) && $salary->status == 'Draft') {
                    $action .= '<a href="'.url('add_monthly_payroll/'.$salary->id).'"
                            class="btn btn-edit">
                            <i class="icon-base ri ri-edit-box-line"></i>
                        </a>';
                }
                if (auth()->id() == 1 || auth()->user()->can('view_details monthly-payroll')) {
                    $action .= '
                        <a href="'.url('view-payslip/'.$salary->id).'"
                            target="_blank"
                            class="btn btn-view">
                            <i class="icon-base ri ri-eye-line"></i>
                        </a>';
                    $action .= '
                        <a href="'.url('print-payslip/'.$salary->id).'"
                            target="_blank"
                            class="btn btn-cancel">
                            <i class="icon-base ri ri-printer-line"></i>
                        </a>';
                    $action .= '
                        <a href="'.url('download-payslip/'.$salary->id).'"
                            target="_blank"
                            class="btn btn-cancel">
                            <i class="ri ri-download-line me-1"></i>
                        </a>';
                }
                $action .= '</div>';
                $data[] = [
                    'checkbox'      => $checkbox,
                    'DT_RowIndex'   => $count++,
                    'month_year'    => \Carbon\Carbon::create()->month($salary->salary_month)->format('F') . ' ' . $salary->salary_year,
                    'employee_name' => $salary->name,
                    'emp_code'      => $salary->emp_id,
                    'gross'         => '₹' . number_format($salary->gross_salary, 2),
                    'net_salary'    => '₹' . number_format($salary->net_salary, 2),
                    'status'        => $statusBadge,
                    'action'        => $action,
                ];
            }
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        }
        return view('salary_calculations.view');
    }
    public function add($id = null)
    {
        $salary = null;
        if($id) {
            $salary = DB::table('salary_generations')
                ->join('users', function ($join) {
                    $join->on(
                        DB::raw('salary_generations.employee_id COLLATE utf8mb4_unicode_ci'),
                        '=',
                        DB::raw('users.emp_id COLLATE utf8mb4_unicode_ci')
                    );
                })
                ->select(
                    'salary_generations.*',
                    'users.name',
                    'users.emp_id'
                )
                ->where('salary_generations.id', $id)
                ->first();
        }
        return view('salary_calculations.add', compact('salary'));
    }
    public function view() {
        return view('salary_calculations/view_details');
    }
    public function generatePayroll(Request $request)
    {
        $month = $request->month;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $type = $request->type ?: 'monthly';

        if ($type === 'range' && $fromDate && $toDate) {
            $startDate = Carbon::parse($fromDate)->toDateString();
            $endDate = Carbon::parse($toDate)->toDateString();
            $carbonStart = Carbon::parse($fromDate);
            $carbonEnd = Carbon::parse($toDate);
            $totalDays = $carbonStart->diffInDays($carbonEnd) + 1;
            $monthTotalDays = $carbonStart->daysInMonth;
            $monthNumber = $carbonStart->month;
            $year = $carbonStart->year;
        } else {
            $date = Carbon::parse($month);
            $year = $date->year;
            $monthNumber = $date->month;
            $startDate = $date->copy()->startOfMonth()->toDateString();
            $endDate = $date->copy()->endOfMonth()->toDateString();
            $totalDays = $date->daysInMonth;
            $monthTotalDays = $totalDays;
        }

        $holidayDates = DB::table('declared_holidays')->whereBetween('date', [$startDate, $endDate])->pluck('date')->toArray();

        $totHolidays = 0;
        for ($day = 0; $day < $totalDays; $day++) {
            $currentDate = Carbon::parse($startDate)->addDays($day)->toDateString();
            $isSunday = Carbon::parse($currentDate)->isSunday();
            $isDeclaredHoliday = in_array($currentDate, $holidayDates);
            
            if ($isSunday || $isDeclaredHoliday) {
                $totHolidays++;
            }
        }

        $employees = User::where('id', '!=', 1)->whereNotIn(DB::raw('emp_id COLLATE utf8mb4_unicode_ci'), function ($query) use ($monthNumber, $year) {
            $query->select(
                DB::raw('employee_id COLLATE utf8mb4_unicode_ci')
            )
            ->from('salary_generations')
            ->where('salary_month', $monthNumber)
            ->where('salary_year', $year);
        })
        ->get();
        $payroll = [];
        foreach ($employees as $employee) {
            $attendance = DB::table('attendances')->where('emp_code', $employee->emp_id)->whereBetween('date', [$startDate, $endDate])->get();
            
            $presentDays = 0;
            $absentDays = 0;
            $totalPresentDaysAny = $attendance->whereIn('status', ['Present', 'Late', 'Overtime'])->count();
            
            for ($day = 0; $day < $totalDays; $day++) {
                $currentDate = Carbon::parse($startDate)->addDays($day)->toDateString();
                $isSunday = Carbon::parse($currentDate)->isSunday();
                $isDeclaredHoliday = in_array($currentDate, $holidayDates);
                
                if (!$isSunday && !$isDeclaredHoliday) {
                    $att = $attendance->where('date', $currentDate)->first();
                    if ($att && in_array($att->status, ['Present', 'Late', 'Overtime'])) {
                        $presentDays++;
                    } else {
                        $absentDays++;
                    }
                }
            }

            if ($totalPresentDaysAny == 0) {
                $payroll[] = [
                    'employee_id'      => $employee->emp_id,
                    'employee_name'    => $employee->name,
                    'emp_code'         => $employee->emp_id,
                    'total_days'     => $totalDays,
                    'present_days'     => 0,
                    'absent_days'      => $absentDays,
                    'holidays'         => $totHolidays,
                    'fixed_gross'      => 0,
                    'basic_salary'     => 0,
                    'hra'              => 0,
                    'da'               => 0,
                    'oa'               => 0,
                    'ot_hours'         => 0,
                    'overtime_amount'  => 0,
                    'lop_amount'       => 0,
                    'incentive'        => 0,
                    'misc'             => 0,
                    'bus_fare'         => 0,
                    'pf'               => 0,
                    'esi'              => 0,
                    'other_deduction'  => 0,
                    'salary_advance'   => 0,
                    'late_hours'       => 0,
                    'late_fine'        => 0,
                    'gross_salary'     => 0,
                    'total_deduction'  => 0,
                    'net_salary'       => 0,
                    'can_generate'     => false,
                    'is_selected'      => false,
                ];
                continue;
            }
            $otHours = 0;
            $otDays = 0;
            foreach ($attendance as $att) {
                if ($att->in_time && $att->out_time) {
                    $inTime = Carbon::parse($att->in_time);
                    $outTime = Carbon::parse($att->out_time);
                    $workedHours = floor(
                        $inTime->diffInMinutes($outTime) / 60
                    );
                    $isSunday = Carbon::parse($att->date)->isSunday();
                    $isHoliday = in_array($att->date, $holidayDates);
                    if ($isSunday || $isHoliday) {
                        $otHours += $workedHours;
                    } else {
                        if ($workedHours > 9) {
                            $otHours += ($workedHours - 9);
                        }
                    }
                }
            }
            $otDays = $otHours / 8;
            $fixedGross = $employee->fixed_gross ?? 0;
            $incentive = DB::table('task_assign_employees')->where('issued_to', $employee->id)->whereBetween('issue_date', [$startDate, $endDate])->sum('total_cost') ?? 0;
            $salaryAdvance = 0;
            $otherDeduction = 0;
            $workingDays = $presentDays - $otDays;
            $busFare = $employee->bus_fare ? $workingDays * $employee->bus_fare : 0;
            $misc = $employee->bus_fare ? $otDays * $employee->bus_fare : 0;
            $perDaySalary = $fixedGross > 0 ? $fixedGross / $monthTotalDays : 0;
            $perHourSalary = $perDaySalary / 8;
            $otAmount = $perHourSalary * $otHours;
            $lopAmount = $perDaySalary * $absentDays;

            $payable = $fixedGross - $lopAmount;

            $basic = ($payable * 50) / 100;
            $da    = ($payable * 20) / 100;
            $hra   = ($payable * 20) / 100;
            $oa    = ($payable * 10) / 100;

            $wage = $basic + $da;
            $pf   = ($wage * 12) / 100;
            $esi  = ($wage <= 21000) ? ($wage * 0.75) / 100 : (21000 * 0.75) / 100;
            $totalPermissionHours = 0;
            foreach ($attendance as $att) {
                if (!empty($att->permission_hours)) {
                    $totalPermissionHours += (float) $att->permission_hours;
                }
            }
            $freePermissionHours = 2;
            $lateFine = 0;
            if ($totalPermissionHours > $freePermissionHours) {
                $lateHours = $totalPermissionHours - $freePermissionHours;
            } else {
                $lateHours = $totalPermissionHours;
            }
            $lateFine = $lateHours * $perHourSalary;
            $totalDeduction = $pf + $esi + $otherDeduction + $salaryAdvance + $lateFine;
            $totalEarnings = $payable + $otAmount + $incentive + $misc + $busFare;
            $netSalary = $totalEarnings - $totalDeduction;
            $payroll[] = [
                'employee_id'      => $employee->emp_id,
                'employee_name'    => $employee->name,
                'emp_code'         => $employee->emp_id,
                'total_days'       => $totalDays,
                'present_days'     => $presentDays,
                'absent_days'      => $absentDays,
                'holidays'         => $totHolidays,
                'fixed_gross'      => round($fixedGross, 2),
                'basic_salary'     => round($basic, 2),
                'hra'              => round($hra, 2),
                'da'               => round($da, 2),
                'oa'               => round($oa, 2),
                'ot_hours'         => round($otHours, 2),
                'overtime_amount'  => round($otAmount, 2),
                'lop_amount'       => round($lopAmount, 2),
                'incentive'        => round($incentive, 2),
                'misc'             => round($misc, 2),
                'bus_fare'         => round($busFare, 2),
                'pf'               => round($pf, 2),
                'esi'              => round($esi, 2),
                'other_deduction'  => round($otherDeduction, 2),
                'salary_advance'   => round($salaryAdvance, 2),
                'late_hours'       => round($lateHours, 2),
                'late_fine'        => round($lateFine, 2),
                'gross_salary'     => round($totalEarnings, 2),
                'total_deduction'  => round($totalDeduction, 2),
                'net_salary'       => round($netSalary, 2),
                'can_generate'     => true,
                'is_selected'      => false,
            ];
        }
        return response()->json([
            'success' => true,
            'payroll' => $payroll
        ]);
    }
    public function savePayroll(Request $request)
    {
        $month = $request->month;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $type = $request->type ?: 'monthly';

        if ($type === 'range' && $fromDate && $toDate) {
            $date = Carbon::parse($fromDate);
            $startDate = Carbon::parse($fromDate)->toDateString();
            $endDate = Carbon::parse($toDate)->toDateString();
        } else {
            $date = Carbon::parse($month);
            $startDate = $date->copy()->startOfMonth()->toDateString();
            $endDate = $date->copy()->endOfMonth()->toDateString();
        }
        $salaryMonth = $date->month;
        $salaryYear = $date->year;
        $isEdit = false;
        foreach ($request->payroll as $row) {
            if(!empty($row['salary_id'])) {
                $isEdit = true;
                break;
            }
        }
        foreach ($request->payroll as $row) {
            if($row['basic_salary'] <= 0 || $row['hra'] <= 0 || $row['da'] <= 0 || $row['gross_salary'] <= 0 || $row['net_salary'] <= 0) {
                return response()->json(['success' => false, 'message' => 'Invalid payroll values detected'], 422);
            }
            if($row['salary_advance'] > $row['gross_salary']) {
                return response()->json(['success' => false, 'message' => 'Salary advance cannot exceed gross salary'], 422);
            }
            if ($row['total_deduction'] > $row['gross_salary']) {
                return response()->json(['success' => false, 'message' => 'Total deduction cannot exceed gross salary'], 422);
            }
            if(!empty($row['salary_id'])) {
                DB::table('salary_generations')->where('id', $row['salary_id'])->update([
                        'basic_salary'    => $row['basic_salary'],
                        'hra'             => $row['hra'],
                        'da'              => $row['da'],
                        'oa'              => $row['oa'],
                        'incentive'       => $row['incentive'],
                        'misc_amount'     => $row['misc'],
                        'present_days'    => $row['present_days'],
                        'absent_days'     => $row['absent_days'],
                        'holidays'        => $row['holidays'],
                        'ot_hours'        => $row['ot_hours'],
                        'late_hours'        => $row['late_hours'],
                        'overtime_amount' => $row['overtime_amount'],
                        'bus_fare'        => $row['bus_fare'],
                        'pf'              => $row['pf'],
                        'esi'             => $row['esi'],
                        'other_deduction' => $row['other_deduction'],
                        'salary_advance'  => $row['salary_advance'],
                        'late_fine'       => $row['late_fine'],
                        'lop_amount'      => $row['lop_amount'],
                        'gross_salary'    => $row['gross_salary'],
                        'total_deduction' => $row['total_deduction'],
                        'net_salary'      => $row['net_salary'],
                        'updated_at'      => now()
                    ]);
            } else {
                $alreadyGenerated = DB::table('salary_generations')->where('employee_id', $row['employee_id'])->where('salary_month', $salaryMonth)->where('salary_year', $salaryYear)->exists();
                if($alreadyGenerated) {
                    continue;
                }
                DB::table('salary_generations')->insert([
                    'employee_id'      => $row['employee_id'],
                    'salary_month'     => $salaryMonth,
                    'salary_year'      => $salaryYear,
                    'from_date'        => $startDate,
                    'to_date'          => $endDate,
                    'fixed_gross'      => $row['fixed_gross'],
                    'basic_salary'     => $row['basic_salary'],
                    'hra'              => $row['hra'],
                    'da'               => $row['da'],
                    'oa'               => $row['oa'],
                    'incentive'        => $row['incentive'],
                    'misc_amount'      => $row['misc'],
                    'total_days'       => $row['total_days'],
                    'present_days'     => $row['present_days'],
                    'absent_days'      => $row['absent_days'],
                    'holidays'         => $row['holidays'],
                    'ot_hours'         => $row['ot_hours'],
                    'late_hours'       => $row['late_hours'],
                    'overtime_amount'  => $row['overtime_amount'],
                    'bus_fare'         => $row['bus_fare'],
                    'pf'               => $row['pf'],
                    'esi'              => $row['esi'],
                    'other_deduction'  => $row['other_deduction'],
                    'salary_advance'   => $row['salary_advance'],
                    'late_fine'        => $row['late_fine'],
                    'gross_salary'     => $row['gross_salary'],
                    'total_deduction'  => $row['total_deduction'],
                    'lop_amount'      => $row['lop_amount'],
                    'net_salary'       => $row['net_salary'],
                    'status'           => 'Draft',
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }
        }
        return response()->json([
            'success' => true
        ]);
    }
    public function updatePayrollStatus(Request $request)
    {
        $salary = DB::table('salary_generations')->where('id', $request->id)->first();
        if(!$salary) {
            return response()->json([
                'success' => false,
                'message' => 'Payroll record not found'
            ], 404);
        }
        if($salary->status == 'Paid') {
            return response()->json([
                'success' => false,
                'message' => 'Paid payroll cannot be modified'
            ], 422);
        }
        $updateData = [
            'status' => $request->status,
            'updated_at' => now()
        ];
        if($request->status == 'Approved') {
            $updateData['approved_at'] = now();
        }
        if($request->status == 'Paid') {
            $updateData['paid_at'] = now();
        }
        DB::table('salary_generations')->where('id', $request->id)->update($updateData);
        return response()->json(['success' => true, 'message' => 'Payroll status updated successfully']);
    }
    public function generatePayslipPdf(Request $request)
    {
        $ids = $request->ids;
        foreach($ids as $id) {
            $salary = DB::table('salary_generations')
                ->join('users', function ($join) {
                    $join->on(
                        DB::raw('salary_generations.employee_id COLLATE utf8mb4_unicode_ci'),
                        '=',
                        DB::raw('users.emp_id COLLATE utf8mb4_unicode_ci')
                    );
                })
                ->leftJoin(
                    'departments',
                    'users.department_id',
                    '=',
                    'departments.id'
                )
                ->leftJoin(
                    'roles',
                    'users.role_id',
                    '=',
                    'roles.id'
                )
                ->select(
                    'salary_generations.*',
                    'users.name',
                    'users.emp_id',
                    'users.esi_no',
                    'users.pf_no',
                    'departments.department as department_name',
                    'roles.name as role_name'
                )
                ->where('salary_generations.id', $id)
                ->first();
            $month = $salary->salary_month;
            $year  = $salary->salary_year;
            $totalDays = \Carbon\Carbon::create($year, $month, 1)->daysInMonth;
            $onTimeDays = DB::table('attendances')->where('emp_code', $salary->employee_id)->whereMonth('date', $month)->whereYear('date', $year)->where('status', 'Present')->count();
            $lateDays = DB::table('attendances')->where('emp_code', $salary->employee_id)->whereMonth('date', $month)->whereYear('date', $year)->where('status', 'Late')->count();
            $setting = Setting::with(['state', 'city'])->first();
            DB::table('salary_generations')
                ->where('id', $id)
                ->update([
                    'status' => 'Paid',
                    'updated_at' => now()
                ]);
        }
        return response()->json([
            'success' => true
        ]);
    }
    public function viewPayslip($id)
    {
        $salary = DB::table('salary_generations')
            ->join('users', function ($join) {
                $join->on(
                    DB::raw('salary_generations.employee_id COLLATE utf8mb4_unicode_ci'),
                    '=',
                    DB::raw('users.emp_id COLLATE utf8mb4_unicode_ci')
                );
            })
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->select(
                'salary_generations.*',
                'users.name',
                'users.emp_id',
                'users.esi_no',
                'users.pf_no',
                'departments.department as department_name',
                'roles.name as role_name'
            )
            ->where('salary_generations.id', $id)
            ->first();
        $month = $salary->salary_month;
        $year  = $salary->salary_year;
        $totalDays = \Carbon\Carbon::create($year, $month, 1)->daysInMonth;
        $onTimeDays = DB::table('attendances')->where('emp_code', $salary->employee_id)->whereMonth('date', $month)->whereYear('date', $year)->where('status', 'Present')->count();
        $lateDays = DB::table('attendances')->where('emp_code', $salary->employee_id)->whereMonth('date', $month)->whereYear('date', $year)->where('status', 'Late')->count();
        $setting = Setting::with(['state', 'city'])->first();
        $pdf = Pdf::loadView('salary_calculations.payslip_pdf', compact('salary', 'setting', 'totalDays', 'onTimeDays', 'lateDays'));
        return $pdf->stream('Payslip_'.$salary->salary_month.'_'.$salary->salary_year.'_'.$salary->emp_id.'.pdf');
    }
    public function printPayslip($id)
    {
        $salary = DB::table('salary_generations')
            ->join('users', function ($join) {
                $join->on(
                    DB::raw('salary_generations.employee_id COLLATE utf8mb4_unicode_ci'),
                    '=',
                    DB::raw('users.emp_id COLLATE utf8mb4_unicode_ci')
                );
            })
            ->leftJoin(
                'departments',
                'users.department_id',
                '=',
                'departments.id'
            )
            ->leftJoin(
                'roles',
                'users.role_id',
                '=',
                'roles.id'
            )
            ->select(
                'salary_generations.*',
                'users.name',
                'users.emp_id',
                'users.esi_no',
                'users.pf_no',
                'departments.department as department_name',
                'roles.name as role_name'
            )
            ->where('salary_generations.id', $id)
            ->first();
        $setting = Setting::with(['state', 'city'])->first();
        $is_print = true;
        return view('salary_calculations.payslip_pdf',compact('salary','is_print','setting'));
    }
    public function downloadPayslip($id)
    {
        $salary = DB::table('salary_generations')
            ->join('users', function ($join) {
                $join->on(
                    DB::raw('salary_generations.employee_id COLLATE utf8mb4_unicode_ci'),
                    '=',
                    DB::raw('users.emp_id COLLATE utf8mb4_unicode_ci')
                );
            })
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->select(
                'salary_generations.*',
                'users.name',
                'users.emp_id',
                'users.esi_no',
                'users.pf_no',
                'departments.department as department_name',
                'roles.name as role_name'
            )
            ->where('salary_generations.id', $id)
            ->first();
        $month = $salary->salary_month;
        $year  = $salary->salary_year;
        $totalDays = \Carbon\Carbon::create($year, $month, 1)->daysInMonth;
        $onTimeDays = DB::table('attendances')->where('emp_code', $salary->employee_id)->whereMonth('date', $month)->whereYear('date', $year)->where('status', 'Present')->count();
        $lateDays = DB::table('attendances')->where('emp_code', $salary->employee_id)->whereMonth('date', $month)->whereYear('date', $year)->where('status', 'Late')->count();
        $setting = Setting::with(['state', 'city'])->first();
        $filename = 'Payslip_'.$salary->salary_month.'_'.$salary->salary_year.'_'.Str::slug($salary->name).'_'.$salary->emp_id.'.pdf';
        $pdf = Pdf::loadView('salary_calculations.payslip_pdf', compact('salary', 'setting', 'totalDays', 'onTimeDays', 'lateDays'));
        return $pdf->download($filename);
    }
    public function searchSalaryGeneration(Request $request)
    {
        $search = trim($request->search);
        $month = $request->month;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $type = $request->type ?: 'monthly';

        if ($type === 'range' && $fromDate && $toDate) {
            $startDate = Carbon::parse($fromDate)->toDateString();
            $endDate = Carbon::parse($toDate)->toDateString();
            $carbonStart = Carbon::parse($fromDate);
            $carbonEnd = Carbon::parse($toDate);
            $totalDays = $carbonStart->diffInDays($carbonEnd) + 1;
            $monthTotalDays = $carbonStart->daysInMonth;
            $monthNumber = $carbonStart->month;
            $year = $carbonStart->year;
        } else {
            $date = Carbon::parse($month);
            $year = $date->year;
            $monthNumber = $date->month;
            $startDate = $date->copy()->startOfMonth()->toDateString();
            $endDate = $date->copy()->endOfMonth()->toDateString();
            $totalDays = $date->daysInMonth;
            $monthTotalDays = $totalDays;
        }

        $holidayDates = DB::table('declared_holidays')->whereBetween('date', [$startDate, $endDate])->pluck('date')->toArray();

        $totHolidays = 0;
        for ($day = 0; $day < $totalDays; $day++) {
            $currentDate = Carbon::parse($startDate)->addDays($day)->toDateString();
            $isSunday = Carbon::parse($currentDate)->isSunday();
            $isDeclaredHoliday = in_array($currentDate, $holidayDates);
            
            if ($isSunday || $isDeclaredHoliday) {
                $totHolidays++;
            }
        }

        $employees = DB::table('users')
            ->where('id', '!=', 1)
            ->whereNotIn(DB::raw('emp_id COLLATE utf8mb4_unicode_ci'), function ($query) use ($monthNumber, $year) {
                $query->select(
                    DB::raw('employee_id COLLATE utf8mb4_unicode_ci')
                )
                ->from('salary_generations')
                ->where('salary_month', $monthNumber)
                ->where('salary_year', $year);
            })
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('emp_id', 'like', "%{$search}%");
            })
            ->get();
        $payroll = [];
        foreach ($employees as $employee) {
            $attendance = DB::table('attendances')->where('emp_code', $employee->emp_id)->whereBetween('date', [$startDate, $endDate])->get();
            
            $presentDays = 0;
            $absentDays = 0;
            $totalPresentDaysAny = $attendance->whereIn('status', ['Present', 'Late', 'Overtime'])->count();
            
            for ($day = 0; $day < $totalDays; $day++) {
                $currentDate = Carbon::parse($startDate)->addDays($day)->toDateString();
                $isSunday = Carbon::parse($currentDate)->isSunday();
                $isDeclaredHoliday = in_array($currentDate, $holidayDates);
                
                if (!$isSunday && !$isDeclaredHoliday) {
                    $att = $attendance->where('date', $currentDate)->first();
                    if ($att && in_array($att->status, ['Present', 'Late', 'Overtime'])) {
                        $presentDays++;
                    } else {
                        $absentDays++;
                    }
                }
            }
            if ($totalPresentDaysAny == 0) {
                $payroll[] = [
                    'employee_id'      => $employee->emp_id,
                    'employee_name'    => $employee->name,
                    'emp_code'         => $employee->emp_id,
                    'total_days'     => $totalDays,
                    'present_days'     => 0,
                    'absent_days'      => $absentDays,
                    'holidays'         => $totHolidays,
                    'fixed_gross'      => 0,
                    'basic_salary'     => 0,
                    'hra'              => 0,
                    'da'               => 0,
                    'oa'               => 0,
                    'ot_hours'         => 0,
                    'overtime_amount'  => 0,
                    'lop_amount'       => 0,
                    'incentive'        => 0,
                    'misc'             => 0,
                    'bus_fare'         => 0,
                    'pf'               => 0,
                    'esi'              => 0,
                    'other_deduction'  => 0,
                    'salary_advance'   => 0,
                    'late_hours'       => 0,
                    'late_fine'        => 0,
                    'gross_salary'     => 0,
                    'total_deduction'  => 0,
                    'net_salary'       => 0,
                    'can_generate'     => false,
                    'is_selected'      => false,
                ];
                continue;
            }
            $otHours = 0;
            $otDays = 0;
            foreach ($attendance as $att) {
                if ($att->in_time && $att->out_time) {
                    $inTime = Carbon::parse($att->in_time);
                    $outTime = Carbon::parse($att->out_time);
                    $workedHours = floor($inTime->diffInMinutes($outTime) / 60);
                    $isSunday = Carbon::parse($att->date)->isSunday();
                    $isHoliday = DB::table('declared_holidays')->whereDate('date', $att->date)->exists();
                    if ($isSunday || $isHoliday) {
                        $otHours += $workedHours;
                    } else {
                        if ($workedHours > 9) {
                            $otHours += ($workedHours - 9);
                        }
                    }
                }
            }
            $otDays = $otHours / 8; 
            $fixedGross = $employee->fixed_gross ?? 0;
            $basic = ($fixedGross * 50) / 100;
            $hra   = ($fixedGross * 20) / 100;
            $da    = ($fixedGross * 20) / 100;
            $oa    = ($fixedGross * 10) / 100;
            $perDaySalary = $fixedGross > 0 ? $fixedGross / $monthTotalDays : 0;
            $perHourSalary = $perDaySalary / 8;
            $otAmount = $perHourSalary * $otHours;
            $lopAmount = $perDaySalary * $absentDays;
            $grossSalary = $fixedGross - $lopAmount;
            $incentive = DB::table('task_assign_employees')->where('issued_to', $employee->id)->whereBetween('issue_date', [$startDate, $endDate])->sum('total_cost') ?? 0; 
            $misc = $employee->bus_fare ? $otDays * $employee->bus_fare : 0;
            $otherDeduction = 0;
            $salaryAdvance = 0;
            $workingDays = $presentDays - $otDays;
            $busFare = $employee->bus_fare ? $workingDays * $employee->bus_fare : 0;
            $totalEarnings = $grossSalary + $otAmount + $incentive + $misc + $busFare;
            $wage = $basic + $da;
            $pf = ($wage * 12) / 100;
            $esi = ($wage <= 21000) ? ($wage * 0.75) / 100 : (21000 * 0.75) / 100;
            $totalPermissionHours = 0;
            foreach ($attendance as $att) {
                if (!empty($att->permission_hours)) {
                    $totalPermissionHours += (float) $att->permission_hours;
                }
            }
            $freePermissionHours = 2;
            $lateFine = 0;
            if ($totalPermissionHours > $freePermissionHours) {
                $lateHours = $totalPermissionHours - $freePermissionHours;
            } else {
                $lateHours = $totalPermissionHours;
            }
            $lateFine = $lateHours * $perHourSalary;
            $totalDeduction = $pf + $esi + $otherDeduction + $salaryAdvance + $lateFine;
            $totalEarnings = $grossSalary + $otAmount + $incentive + $misc + $busFare;
            $netSalary = $totalEarnings - $totalDeduction;
            $payroll[] = [
                'employee_id'      => $employee->emp_id,
                'employee_name'    => $employee->name,
                'emp_code'         => $employee->emp_id,
                'total_days'     => $totalDays,
                'present_days'     => $presentDays,
                'absent_days'      => $absentDays,
                'holidays'         => $totHolidays,
                'fixed_gross'      => round($fixedGross, 2),
                'basic_salary'     => round($basic, 2),
                'hra'              => round($hra, 2),
                'da'               => round($da, 2),
                'oa'               => round($oa, 2),
                'ot_hours'         => round($otHours, 2),
                'overtime_amount'  => round($otAmount, 2),
                'lop_amount'       => round($lopAmount, 2),
                'incentive'        => 0,
                'misc'             => round($misc, 2),
                'bus_fare'         => round($busFare, 2),
                'pf'               => round($pf, 2),
                'esi'              => round($esi, 2),
                'other_deduction'  => 0,
                'salary_advance'   => 0,
                'late_hours'       => round($lateHours, 2),
                'late_fine'        => round($lateFine, 2),
                'gross_salary'     => round($totalEarnings, 2),
                'total_deduction'  => round($totalDeduction, 2),
                'net_salary'       => round($netSalary, 2),
                'can_generate'     => true,
                'is_selected'      => false,
            ];
        }
        return response()->json([
            'status' => true,
            'payroll' => $payroll
        ]);
    }
    public function exportExcel(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view monthly-payroll')) {
            return unauthorizedRedirect();
        }
        $month = $request->month;
        if (!$month) {
            return back()->with('error', 'Please select a month.');
        }

        return Excel::download(
            new MonthlyPayrollExport($month),
            'monthly_payroll_' . $month . '.xlsx'
        );
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ServiceProvider;

class PayrollReportController extends Controller
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
                ->leftJoin('service_providers', 'users.service_provider_id', '=', 'service_providers.id')
                ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
                ->whereNull('users.deleted_at')
                ->select(
                    'salary_generations.*',
                    'users.name',
                    'users.emp_id',
                    'users.phone',
                    'users.service_provider_id',
                    'service_providers.name as unit_name',
                    'departments.department as department_name'
                )
                ->orderBy('salary_generations.id', 'desc');

            if (auth()->user()->id != 1) {
                $query->where(
                    'salary_generations.employee_id',
                    auth()->user()->emp_id
                );
            }

            // Unit Filter
            if (!empty($request->unit)) {
                $query->where('users.service_provider_id', $request->unit);
            }

            // Month Filter (supports "YYYY-MM" or separate month/year)
            if (!empty($request->month)) {
                if (strpos($request->month, '-') !== false) {
                    $date = Carbon::parse($request->month);
                    $query->where('salary_generations.salary_month', $date->month)
                          ->where('salary_generations.salary_year', $date->year);
                } else {
                    $query->where('salary_generations.salary_month', intval($request->month));
                    if (!empty($request->year)) {
                        $query->where('salary_generations.salary_year', intval($request->year));
                    }
                }
            } elseif (!empty($request->year)) {
                $query->where('salary_generations.salary_year', intval($request->year));
            }

            if ($request->has('search') && !empty($request->input('search')['value'])) {
                $search = $request->input('search')['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.emp_id', 'like', "%{$search}%")
                    ->orWhere('service_providers.name', 'like', "%{$search}%")
                    ->orWhere('departments.department', 'like', "%{$search}%")
                    ->orWhere('salary_generations.salary_month', 'like', "%{$search}%")
                    ->orWhere('salary_generations.salary_year', 'like', "%{$search}%");
                });
            }

            $totalRecords = DB::table('salary_generations')->count();
            $filteredRecords = $query->count();
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            if ($length != -1) {
                $query->skip($start)->take($length);
            }
            $reports = $query->get();
            $data = [];
            $count = $start + 1;
            foreach ($reports as $report) {
                $statusBadge = ($report->status == 'Paid')
                    ? '<span class="badge bg-success">Paid</span>'
                    : (($report->status == 'Draft')
                        ? '<span class="badge bg-warning">Draft</span>'
                        : '<span class="badge bg-success">Processed</span>');

                $action = '
                    <div class="d-inline-block text-nowrap">
                        <a href="'.url('view-payslip/'.$report->id).'" target="_blank" class="btn btn-view" title="View Payslip">
                            <i class="icon-base ri ri-eye-line"></i>
                        </a>
                        <a href="'.url('add_monthly_payroll/'.$report->id).'" class="btn btn-edit" title="Edit Payroll">
                            <i class="icon-base ri ri-edit-box-line"></i>
                        </a>
                    </div>
                ';

                $presentDays = (isset($report->present_days) ? $report->present_days : 0);
                $totalDays = (isset($report->total_days) && $report->total_days > 0 ? $report->total_days : Carbon::createFromDate($report->salary_year, $report->salary_month, 1)->daysInMonth);
                $daysDisplay = $presentDays . ' / ' . $totalDays;

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'month_year' => Carbon::createFromDate(
                        $report->salary_year,
                        $report->salary_month,
                        1
                    )->format('M Y'),
                    'employee' => '<strong>' . e($report->name) . '</strong> <span class="badge bg-light text-secondary border ms-1">#' . e($report->emp_id) . '</span>',
                    'unit' => $report->unit_name ?? '-',
                    'department' => $report->department_name ?? '-',
                    'days' => $daysDisplay,
                    'gross_salary' => '₹' . number_format($report->gross_salary ?? 0, 2),
                    'deductions' => '₹' . number_format($report->total_deduction ?? 0, 2),
                    'net_salary' => '<strong class="text-dark">₹' . number_format($report->net_salary ?? 0, 2) . '</strong>',
                    'status' => $statusBadge,
                    'action' => $action,
                ];
            }
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        }

        $units = ServiceProvider::active()->orderBy('id', 'asc')->get();
        return view('payroll_reports.view', compact('units'));
    }
    public function add(){
        return view('payroll_reports/add');
    } 
}

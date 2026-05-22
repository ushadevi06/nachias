<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
                ->select(
                    'salary_generations.*',
                    'users.name',
                    'users.emp_id'
                )
                ->orderBy('salary_generations.id', 'desc');
            if (auth()->user()->id != 1) {
                $query->where(
                    'salary_generations.employee_id',
                    auth()->user()->emp_id
                );
            }
            if ($request->has('search') && !empty($request->input('search')['value'])) {
                $search = $request->input('search')['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.emp_id', 'like', "%{$search}%")
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
                $statusBadge = '<span class="badge bg-success">Processed</span>';
                $action = '
                    <div class="button-btn">
                        <a href="'.url('download_salary_slip/'.$report->id).'" class="btn btn-cancel">
                            <i class="icon-base ri ri-file-download-line"></i>
                        </a>
                    </div>
                ';
                $data[] = [
                    'DT_RowIndex' => $count++,
                    'report_name' => 'Payroll Report',
                    'report_type' => 'Monthly',
                    'month_year' => Carbon::createFromFormat(
                        'F Y',
                        $report->salary_month . ' ' . $report->salary_year
                    )->format('M Y'),
                    'employee' => $report->name .
                        ' <span class="mini-title">(' .
                        $report->emp_id .
                        ')</span>',
                    'department' => $report->department ?? '-',
                    'gross_salary' => '₹' .
                        number_format($report->gross_salary ?? 0, 2),
                    'net_salary' => '₹' .
                        number_format($report->net_salary ?? 0, 2),
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
        return view('payroll_reports.view');
    }
    public function add(){
        return view('payroll_reports/add');
    } 
}

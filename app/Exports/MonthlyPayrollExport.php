<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MonthlyPayrollExport implements FromCollection, WithHeadings, WithMapping
{
    protected $month;
    private $sno = 0;

    public function __construct($month)
    {
        $this->month = $month;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        [$year, $month] = explode('-', $this->month);
        return DB::table('salary_generations')
            ->join('users', function ($join) {
                $join->on(
                    DB::raw('salary_generations.employee_id COLLATE utf8mb4_unicode_ci'),
                    '=',
                    DB::raw('users.emp_id COLLATE utf8mb4_unicode_ci')
                );
            })
            ->leftJoin('departments', 'departments.id', '=', 'users.department_id')
            ->leftJoin('devices', function ($join) {
                $join->on(
                    DB::raw('devices.serial_number COLLATE utf8mb4_unicode_ci'),
                    '=',
                    DB::raw('users.device COLLATE utf8mb4_unicode_ci')
                );
            })
            ->where('salary_generations.salary_year', $year)
            ->where('salary_generations.salary_month', (int)$month)
            ->select(
                'devices.device_name',
                'users.emp_id',
                'users.name',
                'departments.department',
                'users.account_number',
                'users.bank_name',
                'users.ifsc_code',
                'salary_generations.*'
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'Unit',
            'Sno.',
            'Emp Code',
            'Staff Name',
            'Department',
            'Fixed Gross',
            'Wrk Days',
            'Leave Days',
            'Holidays',
            'OT Hrs',
            'Late Hrs',
            'LOP',
            'Basic Pay',
            'DA',
            'HRA',
            'OA',
            'OT Amt',
            'Incentive',
            'Misc',
            'Gross Amt',
            'PF',
            'ESI',
            'Salary Advance',
            'Late Amt',
            'Net Pay',
            'Account No',
            'Bank Name',
            'IFSC',
        ];
    }

    public function map($row): array
    {

        return [
            $row->device_name,
            ++$this->sno,
            $row->emp_id,
            $row->name,
            $row->department,
            $row->fixed_gross ?? 0,
            $row->present_days ?? 0,
            $row->absent_days ?? 0,
            $row->holidays ?? 0,
            $row->ot_hours ?? 0,
            $row->late_hours ?? 0,
            $row->lop_amount ?? 0,
            $row->basic_salary ?? 0,
            $row->da ?? 0,
            $row->hra ?? 0,
            $row->oa ?? 0,
            $row->overtime_amount ?? 0,
            $row->incentive ?? 0,
            $row->misc_amount ?? 0,
            $row->gross_salary ?? 0,
            $row->pf ?? 0,
            $row->esi ?? 0,
            $row->salary_advance ?? 0,
            $row->late_fine ?? 0,
            $row->net_salary ?? 0,
            $row->account_number,
            $row->bank_name,
            $row->ifsc_code,
        ];
    }
}

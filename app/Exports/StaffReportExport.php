<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class StaffReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $empCode;
    protected $month;
    protected $fromDate;
    protected $toDate;

    public function __construct($empCode, $month, $fromDate, $toDate)
    {
        $this->empCode = $empCode;
        $this->month = $month;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function collection()
    {
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

        if ($this->empCode && $this->empCode !== 'all') {
            $query->where('attendances.emp_code', $this->empCode);
        }

        if ($this->month) {
            $query->where('attendances.date', 'like', $this->month . '%');
        } elseif ($this->fromDate && $this->toDate) {
            $query->whereBetween('attendances.date', [$this->fromDate, $this->toDate]);
        }

        return $query->orderBy('attendances.date', 'desc')->orderBy('users.emp_id', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Date',
            'Employee Name',
            'Employee Code',
            'In Time',
            'Out Time',
            'Working Hours',
            'Status',
        ];
    }

    public function map($record): array
    {
        return [
            $record->date ? Carbon::parse($record->date)->format('d-m-Y') : '-',
            $record->employee ?? '-',
            $record->code ?? '-',
            $record->in_time ?? '-',
            $record->out_time ?? '-',
            $record->hours ?? '-',
            $record->status ?? '-',
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class EmployeeExport extends StringValueBinder implements FromCollection, WithHeadings, WithMapping, WithCustomValueBinder, ShouldAutoSize
{
    protected $count = 0;

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::with(['department', 'role', 'devices', 'serviceProvider'])->orderBy('id', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Emp Code',
            'Name',
            'Date of Joining',
            'Phone',
            'Email',
            'Department',
            'Role',
            'Device',
            'ESI No',
            'PF No',
            'Fixed Gross',
            'Bus Fare',
            'Service Provider',
            'Operation Stage',
        ];
    }

    public function map($employee): array
    {

        return [
            $employee->emp_id ?? '-',
            $employee->name ?? '-',
            $employee->date_of_joining ?? '-',
            $employee->phone ?? '-',
            $employee->email ?? '-',
            $employee->department->department ?? '-',
            $employee->role->name ?? '-',
            $employee->devices->device_name ?? '-',
            $employee->esi_no ?? '-',
            $employee->pf_no ?? '-',
            $employee->fixed_gross ?? '-',
            $employee->bus_fare ?? '-',
            $employee->serviceProvider->name ?? '-',
            $employee->operation_stages_names,
        ];
    }
}

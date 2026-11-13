<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StoreLocationSampleExport implements WithHeadings, FromArray, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'Store Location *',
            'Status'
        ];
    }

    public function array(): array
    {
        return [];
    }
}


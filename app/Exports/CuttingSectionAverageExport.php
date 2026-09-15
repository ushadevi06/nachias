<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CuttingSectionAverageExport implements FromView, ShouldAutoSize
{
    protected $cuttingEmployees;
    protected $cuttingPlants;
    protected $rows;
    protected $totalRow;
    protected $avgRow;
    protected $meta;

    public function __construct($cuttingEmployees, $cuttingPlants, $rows, $totalRow, $avgRow, $meta)
    {
        $this->cuttingEmployees = $cuttingEmployees;
        $this->cuttingPlants = $cuttingPlants;
        $this->rows = $rows;
        $this->totalRow = $totalRow;
        $this->avgRow = $avgRow;
        $this->meta = $meta;
    }

    public function view(): View
    {
        return view('reports.production_report._cutting_section_average_excel', [
            'cuttingEmployees' => $this->cuttingEmployees,
            'cuttingPlants' => $this->cuttingPlants,
            'rows' => $this->rows,
            'totalRow' => $this->totalRow,
            'avgRow' => $this->avgRow,
            'meta' => $this->meta
        ]);
    }
}

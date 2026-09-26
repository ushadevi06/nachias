<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CasinoPoMergedExport implements FromView, ShouldAutoSize
{
    protected $summaryData;
    protected $breakdownData;
    protected $dateRange;
    protected $title;
    protected $summaryTotals;

    public function __construct($summaryData, $breakdownData, $dateRange, $title, $summaryTotals)
    {
        $this->summaryData = $summaryData;
        $this->breakdownData = $breakdownData;
        $this->dateRange = $dateRange;
        $this->title = $title;
        $this->summaryTotals = $summaryTotals;
    }

    public function view(): View
    {
        return view('reports.purchase_reports._casino_po_merged_excel', [
            'summaryData' => $this->summaryData,
            'breakdownData' => $this->breakdownData,
            'dateRange' => $this->dateRange,
            'title' => $this->title,
            'summaryTotals' => $this->summaryTotals,
        ]);
    }
}

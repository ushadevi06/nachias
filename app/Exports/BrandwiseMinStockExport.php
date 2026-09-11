<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BrandwiseMinStockExport implements FromView, ShouldAutoSize
{
    protected $data;
    protected $brandName;
    protected $date;
    protected $title;
    protected $isDhotiBrand;

    public function __construct($data, $brandName, $date, $title, $isDhotiBrand = true)
    {
        $this->data = $data;
        $this->brandName = $brandName;
        $this->date = $date;
        $this->title = $title;
        $this->isDhotiBrand = $isDhotiBrand;
    }

    public function view(): View
    {
        return view('reports.purchase_reports._brandwise_minstock_excel', [
            'data' => $this->data,
            'brandName' => $this->brandName,
            'date' => $this->date,
            'title' => $this->title,
            'isDhotiBrand' => $this->isDhotiBrand
        ]);
    }
}

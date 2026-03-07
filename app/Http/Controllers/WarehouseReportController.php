<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WarehouseReportController extends Controller
{
    public function index()
    {
        return view('reports/warehouse_report');
    }
}

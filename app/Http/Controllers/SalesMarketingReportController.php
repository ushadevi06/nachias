<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SalesMarketingReportController extends Controller
{
    public function index()
    {
        return view('reports/sales_marketing_report');
    }
}

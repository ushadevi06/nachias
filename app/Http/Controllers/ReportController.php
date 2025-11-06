<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function customer_reports(){
        return view('reports/customer_report');
    }
    public function sale_reports(){
        return view('reports/sale_report');
    }
    public function stock_reports(){
        return view('reports/stock_report');
    }
    public function daily_production_reports(){
        return view('reports/daily_production_report');
    }
    public function order_reports(){
        return view('reports/order_report');
    }
    public function employee_reports(){
        return view('reports/employee_report');
    }
}

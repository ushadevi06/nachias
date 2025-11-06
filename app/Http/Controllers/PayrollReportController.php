<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PayrollReportController extends Controller
{
    public function index(){
        return view('payroll_reports/view');
    }
    public function add(){
        return view('payroll_reports/add');
    } 
}

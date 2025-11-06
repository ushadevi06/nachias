<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PayslipController extends Controller
{
    public function index(){
        return view('payslip/view');
    }
    public function add(){
        return view('payslip/add');
    } 
    public function view(){
        return view('payslip/view_details');
    } 
}

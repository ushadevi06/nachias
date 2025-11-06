<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SalesAgentController extends Controller
{
    public function index(){
        return view('sales_agent/view');
    }
    public function add(){
        return view('sales_agent/add');
    }
    public function view(){
        return view('sales_agent/view_details');
    }
}

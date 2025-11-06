<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SaleOrderController extends Controller
{
    public function index(){
        return view('sales_order/view');
    }
    public function add(){
        return view('sales_order/add');
    }
    public function view(){
        return view('sales_order/view_details');
    }
}

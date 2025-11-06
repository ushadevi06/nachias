<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StockEntryController extends Controller
{
    public function index(){
        return view('stock_entry/view');
    }
    public function add(){
        return view('stock_entry/add');
    }
    public function view(){
        return view('stock_entry/view_details');
    }
}

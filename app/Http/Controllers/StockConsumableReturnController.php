<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StockConsumableReturnController extends Controller
{
    public function index(){
        return view('stock_consumable_returns/view');
    }
    public function add(){
        return view('stock_consumable_returns/add');
    }
    public function view(){
        return view('stock_consumable_returns/view_details');
    }
}

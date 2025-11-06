<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index(){
        return view('purchase_orders/view');
    }
    public function add() {
        return view('purchase_orders/add');
    }
    public function view() {
        return view('purchase_orders/view_details');
    }
}

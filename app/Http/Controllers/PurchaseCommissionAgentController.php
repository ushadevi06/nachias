<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchasecommissionAgentController extends Controller
{
    public function index(){
        return view('purchase_commission_agent/view');
    }
    public function add(){
        return view('purchase_commission_agent/add');
    }
    public function view(){
        return view('purchase_commission_agent/view_details');
    }

}

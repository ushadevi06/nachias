<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index(){
        return view('billings/view');
    }
    public function add(){
        return view('billings/add');
    }
    public function view(){
        return view('billings/view_details');
    }
     public function billing_invoice(){
        return view('billings/billing_invoice');
    }
}

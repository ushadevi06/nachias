<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchaseInvoiceController extends Controller
{
    public function index(){
        return view('purchase_invoice/view');
    }
    public function add() {
        return view('purchase_invoice/add');
    }
    public function view(){
        return view('purchase_invoice/view_details');
    }
}

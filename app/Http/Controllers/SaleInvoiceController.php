<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SaleInvoiceController extends Controller
{
    public function index(){
        return view('sales_invoice/view');
    }
    public function add(){
        return view('sales_invoice/add');
    }
    public function view(){
        return view('sales_invoice/view_details');
    }
}

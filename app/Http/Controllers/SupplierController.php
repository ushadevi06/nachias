<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(){
        return view('suppliers/view');
    }
    public function add(){
        return view('suppliers/add');
    }
    public function view(){
        return view('suppliers/view_details');
    }
}

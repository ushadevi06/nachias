<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(){
        return view('customers/view');
    }
    public function add(){
        return view('customers/add');
    }
    public function view(){
        return view('customers/view_details');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductionController extends Controller
{
    public function index(){
        return view('productions/view');
    }
    public function add() {
        return view('productions/add');
    }
    public function view(){
        return view('productions/view_details');
    }
}

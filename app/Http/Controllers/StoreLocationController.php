<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StorelocationController extends Controller
{
    public function index(){
        return view('store_location/view');
    }

    public function add(){
        return view('store_location/add');
    }
}

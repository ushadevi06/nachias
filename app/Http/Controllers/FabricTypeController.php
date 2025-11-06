<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FabricTypeController extends Controller
{
    public function index(){
        return view('fabric_type/view');
    }

    public function add(){
        return view('fabric_type/add');
    }
}

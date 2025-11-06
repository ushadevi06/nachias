<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RMaterialController extends Controller
{
    public function index(){
        return view('rmaterials/view');
    }
    public function add() {
        return view('rmaterials/add');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OperationStageController extends Controller
{
    public function index(){
        return view('operation_stages/view');
    }
    public function add(){
        return view('operation_stages/add');
    }
}

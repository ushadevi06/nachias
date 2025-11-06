<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SizeratioController extends Controller
{
    public function index(){
        return view('size_ratio/view');
    }

    public function add(){
        return view('size_ratio/add');
    }
}

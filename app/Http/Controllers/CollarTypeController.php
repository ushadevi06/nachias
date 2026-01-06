<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CollarTypeController extends Controller
{
    public function index()
    {
        return view('collar_type.view');
    }

    public function add()
    {
        return view('collar_type.add');
    }
}

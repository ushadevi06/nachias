<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CuffTypeController extends Controller
{
    public function index()
    {
        return view('cuff_type.view');
    }

    public function add()
    {
        return view('cuff_type.add');
    }
}

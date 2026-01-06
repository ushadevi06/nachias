<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BottomCutController extends Controller
{
    public function index()
    {
        return view('bottom_cut.view');
    }

    public function add()
    {
        return view('bottom_cut.add');
    }
}

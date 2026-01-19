<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        return view('shifts.view');
    }

    public function add()
    {
        return view('shifts.add');
    }
}

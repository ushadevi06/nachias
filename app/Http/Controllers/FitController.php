<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FitController extends Controller
{
    public function index()
    {
        return view('fit.view');
    }

    public function add()
    {
        return view('fit.add');
    }
}

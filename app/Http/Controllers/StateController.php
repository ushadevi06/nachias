<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StateController extends Controller
{
    public function index()
    {
        return view('states/view');
    }
    public function add()
    {
        return view('states/add');
    }
}

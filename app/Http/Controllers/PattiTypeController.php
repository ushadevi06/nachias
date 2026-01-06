<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PattiTypeController extends Controller
{
    public function index()
    {
        return view('patti_type.view');
    }

    public function add()
    {
        return view('patti_type.add');
    }
}

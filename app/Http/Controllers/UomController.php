<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UomController extends Controller
{
    public function index()
    {
        return view('uom/view');
    }
    public function add()
    {
        return view('uom/add');
    }
}

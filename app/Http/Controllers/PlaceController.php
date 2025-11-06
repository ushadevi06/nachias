<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlaceController extends Controller
{
    public function index()
    {
        return view('places/view');
    }
    public function add()
    {
        return view('places/add');
    }
}

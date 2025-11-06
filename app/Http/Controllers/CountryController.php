<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        return view('countries/view');
    }
    public function add()
    {
        return view('countries/add');
    }
}

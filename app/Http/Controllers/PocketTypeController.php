<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PocketTypeController extends Controller
{
    public function index()
    {
        return view('pocket_type.view');
    }

    public function add()
    {
        return view('pocket_type.add');
    }
}

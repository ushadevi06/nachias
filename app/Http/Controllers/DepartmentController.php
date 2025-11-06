<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        return view('departments/view');
    }

    public function add()
    {
        return view('departments/add');
    }
}

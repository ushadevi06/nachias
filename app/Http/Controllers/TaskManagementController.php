<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskManagementController extends Controller
{
    public function index()
    {
        return view('task_management/view');
    }

    public function add()
    {
        return view('task_management/add');
    }
    public function view1()
    {
        return view('task_management/view1');
    }

    public function view_details()
    {
        return view('task_management/view_details');
    }
}

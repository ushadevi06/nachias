<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskCreationController extends Controller
{
    public function index(){
        return view('task_creation/view');
    }
    public function add(){
        return view('task_creation/add');
    }
    public function view(){
        return view('task_creation/view_details');
    }
}

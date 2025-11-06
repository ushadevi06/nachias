<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskAssignmentController extends Controller
{
    public function index(){
        return view('task_assignment/view');
    }
    public function add(){
        return view('task_assignment/add');
    }
    public function view(){
        return view('task_assignment/view_details');
    }
}

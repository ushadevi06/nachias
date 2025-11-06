<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskStatusUpdateController extends Controller
{
    public function index(){
        return view('task_status_update/view');
    }
    public function add(){
        return view('task_status_update/add');
    }
    public function view(){
        return view('task_status_update/view_details');
    }
}

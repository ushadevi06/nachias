<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskTrackingMonitoringController extends Controller
{
    public function index(){
        return view('task_tracking_monitoring/view');
    }
    public function add(){
        return view('task_tracking_monitoring/add');
    }
    public function view(){
        return view('task_tracking_monitoring/view_details');
    }
}

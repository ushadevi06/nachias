<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(){
        return view('attendances/view');
    }
    public function edit() {
        return view('attendances/edit');
    }
    public function view(){
        return view('attendances/view_details');
    }
}

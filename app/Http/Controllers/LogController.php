<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(){
        return view('logs/view');
    }
    public function view(){
        return view('logs/view_details');
    } 
}

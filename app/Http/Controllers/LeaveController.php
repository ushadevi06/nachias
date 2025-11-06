<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(){
        return view('leaves/view');
    }
    public function add(){
        return view('leaves/add');
    } 
    public function view(){
        return view('leaves/view_details');
    } 
} 

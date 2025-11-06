<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OvertimeController extends Controller
{
    public function index(){
        return view('overtime/view');
    }
    public function add(){
        return view('overtime/add');
    } 
    public function view(){
        return view('overtime/view_details');
    } 
}

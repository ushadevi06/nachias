<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SalaryController extends Controller
{
    public function index(){
        return view('salary_calculations/view');
    }
    public function add(){
        return view('salary_calculations/add');
    } 
    public function view(){
        return view('salary_calculations/view_details');
    } 
}

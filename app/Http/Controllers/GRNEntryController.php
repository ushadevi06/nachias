<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GRNEntryController extends Controller
{
    public function index(){
        return view('grn_entry/view');
    }
    public function add(){
        return view('grn_entry/add');
    }
    public function view(){
        return view('grn_entry/view_details');
    }
}

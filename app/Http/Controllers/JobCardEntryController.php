<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JobCardEntryController extends Controller
{
    public function index(){
        return view('job_card_entry/view');
    }
    public function add() {
        return view('job_card_entry/add');
    }
    public function view(){
        return view('job_card_entry/view_details');
    } 
    public function view_jc_item(){
        return view('job_card_entry/view_jc_item');
    } 
}

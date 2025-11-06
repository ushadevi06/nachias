<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(){
        return view('items/view');
    }
    public function add() {
        return view('items/add');
    }
    public function view() {
        return view('items/view_details');
    }
}

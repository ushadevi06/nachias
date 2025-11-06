<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ItemCategoryController extends Controller
{
    public function index(){
        return view('item_categories/view');
    }
    public function add() {
        return view('item_categories/add');
    }
}

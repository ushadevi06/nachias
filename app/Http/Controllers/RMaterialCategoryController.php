<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RMaterialCategoryController extends Controller
{
    public function index(){
        return view('rmaterial_categories/view');
    }
    public function add() {
        return view('rmaterial_categories/add');
    }
}

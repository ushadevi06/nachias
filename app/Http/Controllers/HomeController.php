<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->id() != 1 && !auth()->user()->can('view dashboard')) {
            return unauthorizedRedirect();
        }
        return view('dashboard');
    }
}

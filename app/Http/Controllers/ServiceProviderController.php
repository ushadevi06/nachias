<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServiceProviderController extends Controller
{
    public function index(){
        return view('service_providers/view');
    }
    public function add(){
        return view('service_providers/add');
    }
    public function view(){
        return view('service_providers/view_details');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentRepositoryController extends Controller
{
    public function index()
    {
        return view('document_repository/view');
    }
    public function add()
    {
        return view('document_repository/add');
    }
    public function view()
    {
        return view('document_repository/view_details');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DebitNoteController extends Controller
{
    public function index()
    {
        return view('debit_notes/view');
    }

    public function add()
    {
        return view('debit_notes/add');
    }
    public function view()
    {
        return view('debit_notes/view_details');
    }
}

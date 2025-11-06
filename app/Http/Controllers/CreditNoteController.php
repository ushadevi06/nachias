<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CreditNoteController extends Controller
{
    public function index(){
        return view('credit_notes/view');
    }
    public function add() {
        return view('credit_notes/add');
    }
    public function view() {
        return view('credit_notes/view_details');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;


class PurchaseInvoiceController extends Controller
{
    public function index(){
        return view('purchase_invoice/view');
    }
    public function add() {
        return view('purchase_invoice/add');
    }
    public function view(){
        return view('purchase_invoice/view_details');
    }
    public function downloadPdf()
    {
        $pdf = Pdf::loadView('purchase_invoice.purchase_invoice_pdf');
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('Purchase_Invoice.pdf');
    }
}

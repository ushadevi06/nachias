<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use App\Models\DebitNote;
use App\Models\CreditNote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Setting;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view manage-payments')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $query = Payment::latest();

            if ($request->payment_type) {
                $query->where('payment_type', $request->payment_type);
            }

            $totalRecords = $query->count();

            if ($request->has('search') && !empty($request->input('search')['value'])) {
                $search = $request->input('search')['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('payment_no', 'like', "%{$search}%")
                        ->orWhere('payment_type', 'like', "%{$search}%")
                        ->orWhere('reference_type', 'like', "%{$search}%")
                        ->orWhere('reference_no', 'like', "%{$search}%")
                        ->orWhere('payment_mode', 'like', "%{$search}%");
                });
            }

            $filteredRecords = $query->count();

            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            if ($length != -1) {
                $query->skip($start)->take($length);
            }

            $payments = $query->get();
            $data = [];
            $i = $start + 1;

            foreach ($payments as $row) {
                $payment_no = '';
                if(auth()->id() == 1 || auth()->user()->can('view_details manage-payments')){
                    $payment_no = '<a href="' . url('payments/view/' . $row->id) . '">' . $row->payment_no . '</a>';
                } else {
                    $payment_no = $row->payment_no;
                }
                $action = '<div class="button-box">';
                if(auth()->id() == 1 || auth()->user()->can('view_details manage-payments')){
                    $action .= '<a href="' . url('payments/view/' . $row->id) . '" class="btn btn-view" title="View"><i class="icon-base ri ri-eye-line"></i></a> ';
                }
                if (auth()->id() == 1 || auth()->user()->can('delete manage-payments')) {
                    $action .= '<button type="button" class="btn btn-delete" onclick="delete_data(\'' . url('payments/delete/' . $row->id) . '\')" title="Delete"><i class="icon-base ri ri-delete-bin-line"></i></button>';
                }
                $action .= '</div>';

                $reference = $row->reference_no ?: ($row->reference_type);

                $data[] = [
                    'DT_RowIndex' => $i++,
                    'payment_no' => $payment_no,
                    'payment_type' => $row->payment_type,
                    'reference' => $reference,
                    'payment_mode' => $row->payment_mode,
                    'amount' => '₹' . number_format($row->amount, 2),
                    'payment_date' => date('d-m-Y', strtotime($row->payment_date)),
                    'action' => $action,
                ];
            }

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        }

        return view('payments.view');
    }

    public function add(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('create manage-payments')) {
            return unauthorizedRedirect();
        }
        if ($request->isMethod('post')) {
            $rules = [
                'payment_type' => 'required',
                'reference_document' => 'required',
                'reference_no' => 'required',
                'amount_paid' => 'required|numeric|min:0.01',
                'payment_date' => 'required',
                'payment_mode' => 'required',
                'bank_name' => 'required_if:payment_mode,Bank (Cheque),Online (UPI),NEFT/RTGS',
                'cheque_no' => 'required_if:payment_mode,Bank (Cheque)',
                'cheque_date' => 'required_if:payment_mode,Bank (Cheque)',
                'transaction_no' => 'required_if:payment_mode,Online (UPI),NEFT/RTGS',
            ];

            $messages = [
                '*.required' => 'This field is required.',
                'amount_paid.numeric' => 'Amount paid must be a number.',
                'amount_paid.min' => 'Amount paid must be at least 0.01.',
                'bank_name.required_if' => 'This field is required.',
                'cheque_no.required_if' => 'This field is required.',
                'cheque_date.required_if' => 'This field is required.',
                'transaction_no.required_if' => 'This field is required.',
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            if ($request->reference_document == 'po-invoice' && is_numeric($request->reference_no)) {
                $invoice = PurchaseInvoice::with('purchaseOrder')->find($request->reference_no);
                if ($invoice) {
                    $totalPaid = Payment::where('payment_type', $request->payment_type)
                        ->where('reference_type', 'Purchase Invoice')
                        ->where('reference_id', $invoice->id)
                        ->whereNull('deleted_at')
                        ->sum('amount');

                    $limit = $invoice->grand_total;
                    if ($request->payment_type == 'Agent Commission') {
                        $commPercent = $invoice->purchaseOrder->commission ?? 0;
                        $limit = ($invoice->sub_total * $commPercent) / 100;
                    }

                    $currentOutstanding = round($limit - $totalPaid, 2);
                    if ($request->amount_paid > $currentOutstanding) {
                        return redirect()->back()->with('danger', 'Amount Paid (₹' . $request->amount_paid . ') exceeds Outstanding Balance (₹' . $currentOutstanding . ')')->withInput();
                    }
                }
            }

            try {
                DB::beginTransaction();

                $paymentNo = 'PAY-' . date('Ymd') . '-' . strtoupper(uniqid());

                $payment = new Payment();
                $payment->payment_no = $paymentNo;
                $payment->payment_type = $request->payment_type;
                $payment->reference_type = $this->mapDocType($request->reference_document);

                if (is_numeric($request->reference_no)) {
                    $payment->reference_id = $request->reference_no;
                }
                else {
                    $payment->reference_no = $request->reference_no;
                }

                $payment->payment_mode = $request->payment_mode;
                $payment->amount = $request->amount_paid;
                $payment->payment_date = date('Y-m-d', strtotime($request->payment_date));
                $payment->transaction_no = $request->transaction_no;
                $payment->bank_name = $request->bank_name;
                $payment->cheque_no = $request->cheque_no;
                $payment->cheque_date = $request->cheque_date ? date('Y-m-d', strtotime($request->cheque_date)) : null;
                $payment->remarks = $request->remarks;

                if ($request->hasFile('attachment')) {
                    $file = $request->file('attachment');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $uploadPath = public_path('uploads/payments/');
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    $file->move($uploadPath, $filename);
                    $payment->attachment = 'uploads/payments/' . $filename;
                }

                $payment->created_by = auth()->id();

                if ($payment->reference_id) {
                    if ($payment->reference_type == 'Purchase Invoice') {
                        $invoice = PurchaseInvoice::find($payment->reference_id);
                        $payment->reference_no = $invoice ? $invoice->invoice_no : null;
                    }
                    elseif ($payment->reference_type == 'Sales Invoice') {
                        $invoice = SalesInvoice::find($payment->reference_id);
                        $payment->reference_no = $invoice ? $invoice->inv_no : null;
                    }
                    elseif ($payment->reference_type == 'Debit Note') {
                        $note = DebitNote::find($payment->reference_id);
                        $payment->reference_no = $note ? $note->debit_note_no : null;
                    }
                    elseif ($payment->reference_type == 'Credit Note') {
                        $note = CreditNote::find($payment->reference_id);
                        $payment->reference_no = $note ? $note->note_no : null;
                    }
                }

                $payment->save();
                if ($payment->reference_id) {
                    if ($payment->reference_type == 'Purchase Invoice' && $payment->payment_type == 'Supplier Payment') {
                        $invoice = PurchaseInvoice::find($payment->reference_id);
                        if ($invoice) {
                            $invoice->received_amount += $payment->amount;
                            $invoice->due_amount = $invoice->grand_total - $invoice->received_amount;

                            if ($invoice->due_amount <= 0) {
                                $invoice->invoice_status = 'Paid';
                            }
                            else {
                                $invoice->invoice_status = 'Partially Paid';
                            }
                            $invoice->save();
                        }
                    }
                    elseif ($payment->reference_type == 'Sales Invoice' && $payment->payment_type == 'Customer Collection') {
                        $invoice = SalesInvoice::find($payment->reference_id);
                        if ($invoice) {
                            $invoice->received_amount += $payment->amount;
                            $invoice->due_amount = $invoice->grand_total - $invoice->received_amount;

                            if ($invoice->due_amount <= 0) {
                                $invoice->invoice_status = 'Paid';
                            }
                            else {
                                $invoice->invoice_status = 'Partially Paid';
                            }
                            $invoice->save();
                        }
                    }
                }

                DB::commit();
                addLog('create', 'Payment', 'payments', $payment->id, null, $payment->toArray());
                return redirect('payments')->with('success', 'Payment recorded successfully. Reference No: ' . $paymentNo);
            }
            catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('danger', 'Error: ' . $e->getMessage())->withInput();
            }

        }
        return view('payments/add');
    }

    public function view($id = null)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view_details manage-payments')) {
            return unauthorizedRedirect();
        }
        $payment = $id ?Payment::findOrFail($id) : null;
        return view('payments/view_details', compact('payment'));
    }

    public function getReferences(Request $request)
    {
        $paymentType = $request->payment_type;
        $docType = $request->doc_type;
        $references = [];

        if ($paymentType == 'Supplier Payment') {
            if ($docType == 'po-invoice') {
                $references = PurchaseInvoice::where('invoice_status', '!=', 'Paid')->whereNull('deleted_at')->select('id', 'invoice_no as text')->get();
            }
            elseif ($docType == 'debit-note') {
                $references = DebitNote::where('status', 'Approved')->whereNull('deleted_at')->select('id', 'debit_note_no as text')->get();
            }
        }
        elseif ($paymentType == 'Customer Collection') {
            if ($docType == 'so-invoice') {
                $references = SalesInvoice::where('invoice_status', '!=', 'Paid')->whereNull('deleted_at')->select('id', 'inv_no as text')->get();
            }
            elseif ($docType == 'credit-note') {
                $references = CreditNote::where('status', 'Approved')->whereNull('deleted_at')->select('id', 'note_no as text')->get();
            }
        }
        elseif ($paymentType == 'Agent Commission') {
            if ($docType == 'po-invoice') {
                $references = PurchaseInvoice::join('purchase_orders', 'purchase_invoices.purchase_order_id', '=', 'purchase_orders.id')
                    ->whereNotNull('purchase_orders.purchase_commission_agent_id')
                    ->where('purchase_orders.commission', '>', 0)
                    ->whereNull('purchase_invoices.deleted_at')
                    ->whereNull('purchase_orders.deleted_at')
                    ->whereRaw('(purchase_invoices.sub_total * purchase_orders.commission / 100) > (
                        SELECT COALESCE(SUM(p.amount), 0) 
                        FROM payments p 
                        WHERE p.payment_type = "Agent Commission" 
                        AND p.reference_type = "Purchase Invoice" 
                        AND p.reference_id = purchase_invoices.id 
                        AND p.deleted_at IS NULL
                    )')->select('purchase_invoices.id', 'purchase_invoices.invoice_no as text')->get();
            }
            elseif ($docType == 'so-invoice') {
                $references = SalesInvoice::join('sales_orders', 'sales_invoices.so_id', '=', 'sales_orders.id')
                    ->whereNotNull('sales_orders.agent_id')
                    ->where('sales_orders.commission_percent', '>', 0)
                    ->whereNull('sales_invoices.deleted_at')
                    ->whereNull('sales_orders.deleted_at')
                    ->whereRaw('(sales_invoices.sub_total * sales_orders.commission_percent / 100) > (
                        SELECT COALESCE(SUM(p.amount), 0) 
                        FROM payments p 
                        WHERE p.payment_type = "Agent Commission" 
                        AND p.reference_type = "Sales Invoice" 
                        AND p.reference_id = sales_invoices.id 
                        AND p.deleted_at IS NULL
                    )')->select('sales_invoices.id', 'sales_invoices.inv_no as text')->get();
            }
        }

        return response()->json($references);
    }

    public function getReferenceDetails(Request $request)
    {
        $docType = $request->doc_type;
        $paymentType = $request->payment_type;
        $id = $request->id;
        $details = ['total' => 0, 'paid' => 0, 'outstanding' => 0];

        if ($docType == 'po-invoice') {
            $invoice = PurchaseInvoice::with('purchaseOrder.purchaseCommissionAgent')->find($id);
            if ($invoice) {
                if ($paymentType == 'Agent Commission') {
                    $commPercent = $invoice->purchaseOrder->commission ?? 0;
                    $details['total'] = ($invoice->sub_total * $commPercent) / 100;
                    $details['paid'] = Payment::where('payment_type', 'Agent Commission')->where('reference_type', 'Purchase Invoice')->where('reference_id', $id)->whereNull('deleted_at')->sum('amount');
                    $details['outstanding'] = $details['total'] - $details['paid'];
                    $details['agent_name'] = $invoice->purchaseOrder->purchaseCommissionAgent->name ?? 'N/A';
                    $details['commission_percent'] = $commPercent;
                    $details['commission_amount'] = $details['total'];
                }
                else {
                    $details['total'] = $invoice->grand_total;
                    $details['paid'] = Payment::where('payment_type', 'Supplier Payment')->where('reference_type', 'Purchase Invoice')->where('reference_id', $id)->whereNull('deleted_at')->sum('amount');
                    $details['outstanding'] = $invoice->grand_total - $details['paid'];
                }
            }
        }
        elseif ($docType == 'so-invoice') {
            $invoice = SalesInvoice::with('salesOrder.salesAgent')->find($id);
            if ($invoice) {
                if ($paymentType == 'Agent Commission') {
                    $commPercent = $invoice->salesOrder->commission_percent ?? 0;
                    $details['total'] = ($invoice->sub_total * $commPercent) / 100;
                    $details['paid'] = Payment::where('payment_type', 'Agent Commission')->where('reference_type', 'Sales Invoice')->where('reference_id', $id)->whereNull('deleted_at')->sum('amount');
                    $details['outstanding'] = $details['total'] - $details['paid'];
                    $details['agent_name'] = $invoice->salesOrder->salesAgent->name ?? 'N/A';
                    $details['commission_percent'] = $commPercent;
                    $details['commission_amount'] = $details['total'];
                }
                else {
                    $details['total'] = $invoice->grand_total;
                    $details['paid'] = Payment::where('payment_type', 'Customer Collection')->where('reference_type', 'Sales Invoice')->where('reference_id', $id)->whereNull('deleted_at')->sum('amount');
                    $details['outstanding'] = $invoice->grand_total - $details['paid'];
                }
            }
        }
        elseif ($docType == 'debit-note') {
            $note = DebitNote::find($id);
            if ($note) {
                $details['total'] = $note->grand_total;
                $details['paid'] = Payment::where('reference_type', 'Debit Note')->where('reference_id', $id)->whereNull('deleted_at')->sum('amount');
                $details['outstanding'] = $note->grand_total - $details['paid'];
            }
        }
        elseif ($docType == 'credit-note') {
            $note = CreditNote::find($id);
            if ($note) {
                $details['total'] = $note->grand_total;
                $details['paid'] = Payment::where('payment_type', 'Customer Collection')->where('reference_type', 'Credit Note')->where('reference_id', $id)->whereNull('deleted_at')->sum('amount');
                $details['outstanding'] = $note->grand_total - $details['paid'];
            }
        }

        return response()->json($details);
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete manage-payments')) {
            return unauthorizedRedirect();
        }

        try {
            DB::beginTransaction();
            $payment = Payment::findOrFail($id);

            if ($payment->reference_id) {
                if ($payment->reference_type == 'Purchase Invoice' && $payment->payment_type == 'Supplier Payment') {
                    $invoice = PurchaseInvoice::find($payment->reference_id);
                    if ($invoice) {
                        $invoice->received_amount -= $payment->amount;
                        $invoice->due_amount = $invoice->grand_total - $invoice->received_amount;

                        if ($invoice->received_amount <= 0) {
                            $invoice->invoice_status = 'Unpaid/Credit';
                        }
                        elseif ($invoice->due_amount > 0) {
                            $invoice->invoice_status = 'Partially Paid';
                        }
                        $invoice->save();
                    }
                }
                elseif ($payment->reference_type == 'Sales Invoice' && $payment->payment_type == 'Customer Collection') {
                    $invoice = SalesInvoice::find($payment->reference_id);
                    if ($invoice) {
                        $invoice->received_amount -= $payment->amount;
                        $invoice->due_amount = $invoice->grand_total - $invoice->received_amount;

                        if ($invoice->received_amount <= 0) {
                            $invoice->invoice_status = 'Unpaid/Credit';
                        }
                        elseif ($invoice->due_amount > 0) {
                            $invoice->invoice_status = 'Partially Paid';
                        }
                        $invoice->save();
                    }
                }
            }

            $oldData = $payment->toArray();
            $payment->delete();
            addLog('delete', 'Payment', 'payments', $id, $oldData, null);

            DB::commit();
            return redirect('payments')->with('success', 'Payment deleted successfully and balances reverted.');

        }
        catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('danger', 'Error: ' . $e->getMessage());
        }
    }

    public function print($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view_details manage-payments')) {
            return unauthorizedRedirect();
        }
        $payment = Payment::findOrFail($id);
        $setting = Setting::first();
        $totalInWords = numberToWords($payment->amount);
        $is_print = true;

        return view('payments.payment_pdf', compact('payment', 'setting', 'totalInWords', 'is_print'));
    }

    public function download($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view_details manage-payments')) {
            return unauthorizedRedirect();
        }
        $payment = Payment::findOrFail($id);
        $setting = Setting::first();
        $totalInWords = numberToWords($payment->amount);

        $pdf = Pdf::loadView('payments.payment_pdf', compact('payment', 'setting', 'totalInWords'));
        return $pdf->stream('payment_' . $payment->payment_no . '.pdf');
    }

    private function mapDocType($type)
    {
        $map = [
            'po-invoice' => 'Purchase Invoice',
            'so-invoice' => 'Sales Invoice',
            'debit-note' => 'Debit Note',
            'credit-note' => 'Credit Note',
        ];
        return $map[$type] ?? $type;
    }
}

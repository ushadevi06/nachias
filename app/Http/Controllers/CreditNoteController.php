<?php

namespace App\Http\Controllers;

use App\Models\CreditNote;
use App\Models\CreditNoteItem;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\Customer;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class CreditNoteController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = CreditNote::with(['customer', 'salesInvoice'])->orderBy('id', 'desc');

            if (!empty($request->customer_id)) {
                $query->where('customer_id', $request->customer_id);
            }

            $creditNotes = $query->get();
            $data = [];
            $count = 1;

            foreach ($creditNotes as $note) {
                $status_options = ['Draft', 'Approved', 'Cancelled'];
                $status = '<select class="form-select status-dropdown" data-id="' . $note->id . '">';
                foreach ($status_options as $option) {
                    $selected = ($note->status == $option) ? 'selected' : '';
                    $status .= '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
                }
                $status .= '</select>';
                $status .= '<div class="status_msg_' . $note->id . '"></div>';

                $action = '<div class="button-box">
                    <a href="' . url('credit_notes/view/' . $note->id) . '" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>
                    <a href="' . url('credit_notes/add/' . $note->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                    <a href="javascript:;" class="btn btn-delete delete-btn" onclick="delete_data(\'' . url('credit_notes/delete/' . $note->id) . '\')"><i class="icon-base ri ri-delete-bin-line"></i></a>
                </div>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'note_no' => $note->note_no,
                    'note_date' => $note->note_date->format('d-m-Y'),
                    'sales_invoice_no' => $note->salesInvoice ? $note->salesInvoice->inv_no : '-',
                    'customer_name' => $note->customer ? $note->customer->name : '-',
                    'grand_total' => '₹' . number_format($note->grand_total, 2),
                    'status' => $status,
                    'action' => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        $customers = Customer::all();
        return view('credit_notes.view', compact('customers'));
    }

    public function add(Request $request, $id = null)
    {
        $creditNote = $id ?CreditNote::with('items.item', 'items.uom', 'items.brandCategory')->findOrFail($id) : null;

        $nextNoteNo = '';
        if (!$id) {
            $lastNote = CreditNote::withTrashed()->orderBy('id', 'desc')->first();
            if ($lastNote && preg_match('/CN-(\d+)/', $lastNote->note_no, $matches)) {
                $number = intval($matches[1]) + 1;
                $nextNoteNo = 'CN-' . str_pad($number, 3, '0', STR_PAD_LEFT);
            }
            else {
                $nextNoteNo = 'CN-001';
            }
        }

        if ($request->isMethod('POST')) {
            $request->validate([
                'note_no' => 'required|string|max:50|unique:credit_notes,note_no,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'note_date' => 'required|date',
                'sales_invoice_id' => 'required|exists:sales_invoices,id',
                'customer_id' => 'required|exists:customers,id',
                'reason' => 'nullable|string',
                'items' => 'required|array|min:1',
                'sub_total' => 'required|numeric',
                'round_off' => 'nullable|numeric',
                'round_off_type' => 'nullable|string|in:Add,Less',
                'grand_total' => 'required|numeric',
                'reference_document' => 'nullable|mimes:pdf,jpg,jpeg,png,webp,doc,docx|max:2048',
                'remarks' => 'nullable|string|min:5|max:255|regex:/^[^<>]*$/',
            ], [
                '*.required' => 'This field is required.',
                '*.exists' => 'Selected value is invalid.',
                '*.unique' => 'This field already exists.',
                '*.date' => 'Please enter a valid date.',
                '*.after_or_equal' => 'Due date must be after or equal to PO date.',
                '*.numeric' => 'This field must be a number.',
                '*.min' => 'This field must be at least :min characters.',
                '*.max' => 'This field should not be more than :max characters.',
            ]);

            DB::beginTransaction();
            try {
                $referenceDoc = $id ? $creditNote->reference_doc : null;
                if ($request->hasFile('reference_document')) {
                    $file = $request->file('reference_document');
                    $filename = 'credit_note_' . time() . '.' . $file->getClientOriginalExtension();
                    $uploadPath = public_path('uploads/credit_notes');
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    $file->move($uploadPath, $filename);
                    if ($id && $creditNote->reference_doc && file_exists(public_path('uploads/credit_notes/' . $creditNote->reference_doc))) {
                        unlink(public_path('uploads/credit_notes/' . $creditNote->reference_doc));
                    }

                    $referenceDoc = $filename;
                }

                $creditNoteData = [
                    'note_no' => $request->note_no,
                    'note_date' => Carbon::parse($request->note_date)->format('Y-m-d'),
                    'sales_invoice_id' => $request->sales_invoice_id,
                    'customer_id' => $request->customer_id,
                    'reason' => $request->reason,
                    'other_state' => $request->is_other_state == 'yes',
                    'igst_percent' => $request->igst_percent ?? 0,
                    'igst' => $request->igst_amt ?? 0,
                    'cgst_percent' => $request->cgst_percent ?? 0,
                    'cgst' => $request->cgst_amt ?? 0,
                    'sgst_percent' => $request->sgst_percent ?? 0,
                    'sgst' => $request->sgst_amt ?? 0,
                    'sub_total' => $request->sub_total,
                    'tax_amount' => $request->tax_amount ?? 0,
                    'round_off' => $request->round_off ?? 0,
                    'round_off_type' => $request->round_off_type ?? 'Add',
                    'grand_total' => $request->grand_total,
                    'remarks' => $request->remarks,
                    'reference_doc' => $referenceDoc,
                    'status' => $request->status ?? 'Draft',
                ];

                if ($id) {
                    $oldData = $creditNote->toArray();
                    $creditNote->update($creditNoteData);
                    CreditNoteItem::where('credit_note_id', $id)->delete();
                    addLog('update', 'Credit Note', 'credit_notes', $id, $oldData, $creditNote->fresh()->toArray());
                    $msg = 'Credit Note updated successfully';
                }
                else {
                    $creditNoteData['created_by'] = auth()->id();
                    $creditNote = CreditNote::create($creditNoteData);
                    addLog('create', 'Credit Note', 'credit_notes', $creditNote->id, null, $creditNote->toArray());
                    $msg = 'Credit Note added successfully';
                }

                foreach ($request->items as $item) {
                    if (isset($item['selected']) && $item['selected'] == '1') {
                        CreditNoteItem::create([
                            'credit_note_id' => $creditNote->id,
                            'sales_invoice_item_id' => $item['sales_invoice_item_id'],
                            'item_id' => $item['item_id'],
                            'brand_category_id' => $item['brand_category_id'] ?? null,
                            'size' => $item['size'] ?? null,
                            'quantity' => $item['quantity'],
                            'sleeve_type' => $item['sleeve_type'] ?? null,
                            'mrp' => $item['mrp'] ?? 0,
                            'uom_id' => $item['uom_id'],
                            'rate' => $item['rate'],
                            'amount' => $item['amount'],
                        ]);
                    }
                }

                DB::commit();
                return redirect(url('credit_notes'))->with('success', $msg);
            }
            catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->withErrors(['error' => 'Failed to save: ' . $e->getMessage()]);
            }
        }

        $salesInvoices = SalesInvoice::with('customer')->latest()->get();

        $nextNoteNo = '';
        if (!$id) {
            $lastNote = CreditNote::withTrashed()->orderBy('id', 'desc')->first();
            if ($lastNote && preg_match('/CN-(\d+)/', $lastNote->note_no, $matches)) {
                $number = intval($matches[1]) + 1;
                $nextNoteNo = 'CN-' . str_pad($number, 3, '0', STR_PAD_LEFT);
            }
            else {
                $nextNoteNo = 'CN-001';
            }
        }

        return view('credit_notes.add', compact('creditNote', 'salesInvoices', 'nextNoteNo'));
    }

    public function getInvoiceDetails($id)
    {
        $invoice = SalesInvoice::with(['customer', 'items.item', 'items.uom', 'items.brandCategory'])->findOrFail($id);

        $items = $invoice->items->map(function ($item) {
            return [
            'id' => $item->id,
            'item_id' => $item->item_id,
            'item_name' => $item->item ? $item->item->name : '-',
            'item_code' => $item->item ? $item->item->code : '-',
            'brand_category_id' => $item->brand_id,
            'brand_category_name' => $item->brandCategory ? $item->brandCategory->name : '-',
            'size' => $item->size,
            'uom_id' => $item->uom_id,
            'uom_code' => $item->uom ? $item->uom->uom_code : '-',
            'quantity' => $item->quantity,
            'sleeve_type' => $item->sleeve_type,
            'mrp' => $item->mrp,
            'rate' => $item->rate,
            'amount' => $item->amount,
            ];
        });

        return response()->json([
            'success' => true,
            'customer_id' => $invoice->customer_id,
            'customer_name' => $invoice->customer ? $invoice->customer->name : '-',
            'items' => $items,
            'other_state' => $invoice->other_state ? 'yes' : 'no',
            'igst_percent' => $invoice->igst_percent,
            'cgst_percent' => $invoice->cgst_percent,
            'sgst_percent' => $invoice->sgst_percent,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $note = CreditNote::findOrFail($id);
        $oldData = $note->toArray();
        $note->status = $request->status;
        $note->save();
        addLog('update_status', 'Credit Note Status', 'credit_notes', $id, $oldData, $note->fresh()->toArray());

        return response()->json([
            'success' => true,
            'status' => $note->status
        ]);
    }

    public function view($id)
    {
        $creditNote = CreditNote::with(['customer', 'salesInvoice', 'items.item', 'items.uom'])->findOrFail($id);
        return view('credit_notes.view_details', compact('creditNote'));
    }

    public function destroy($id)
    {
        $note = CreditNote::findOrFail($id);
        $oldData = $note->toArray();
        $note->delete();
        addLog('delete', 'Credit Note', 'credit_notes', $id, $oldData, null);
        return redirect(url('credit_notes'))->with('success', 'Credit Note deleted successfully');
    }

    public function print($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view credit-note')) {
            return unauthorizedRedirect();
        }

        $creditNote = CreditNote::with(['customer', 'salesInvoice', 'items.item', 'items.uom'])->findOrFail($id);
        $setting = Setting::first();

        $totalInWords = numberToWords($creditNote->grand_total);
        $is_print = true;

        $data = [
            'creditNote' => $creditNote,
            'setting' => $setting,
            'totalInWords' => $totalInWords
        ];

        $pdf = Pdf::loadView('credit_notes.credit_note_pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('CreditNote_' . $creditNote->note_no . '.pdf');
    }

    public function download($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view credit-note')) {
            return unauthorizedRedirect();
        }

        $creditNote = CreditNote::with(['customer', 'salesInvoice', 'items.item', 'items.uom'])->findOrFail($id);
        $setting = Setting::first();

        $totalInWords = numberToWords($creditNote->grand_total);

        $data = [
            'creditNote' => $creditNote,
            'setting' => $setting,
            'totalInWords' => $totalInWords
        ];

        $pdf = Pdf::loadView('credit_notes.credit_note_pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('CreditNote_' . $creditNote->note_no . '.pdf');
    }
}

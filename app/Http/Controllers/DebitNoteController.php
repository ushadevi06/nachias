<?php

namespace App\Http\Controllers;

use App\Models\DebitNote;
use App\Models\DebitNoteItem;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DebitNoteController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view debit-note')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $query = DebitNote::with(['supplier', 'purchaseInvoice'])
                ->orderBy('id', 'desc');

            if (!empty($request->supplier_id)) {
                $query->where('supplier_id', $request->supplier_id);
            }

            $debitNotes = $query->get();
            $data = [];
            $count = 1;

            foreach ($debitNotes as $note) {
                $action = '<div class="button-box">';
                if (auth()->id() == 1 || auth()->user()->can('view debit-note')) {
                    $action .= '<a href="' . url('debit_notes/view/' . $note->id) . '" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>';
                }
                if (auth()->id() == 1 || auth()->user()->can('edit debit-note')) {
                    $action .= '<a href="' . url('debit_notes/add/' . $note->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }
                /* if (auth()->id() == 1 || auth()->user()->can('delete debit-note')) {
                    $action .= '<a href="javascript:;" class="btn btn-delete" onclick="delete_data(\'' . url('debit_notes/delete/' . $note->id) . '\')"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                } */
                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'debit_note_no' => $note->debit_note_no,
                    'debit_note_date' => $note->debit_note_date->format('d-m-Y'),
                    'purchase_invoice_no' => $note->purchaseInvoice ? $note->purchaseInvoice->invoice_no : '-',
                    'supplier_name' => $note->supplier ? $note->supplier->name : '-',
                    'grand_total' => '₹' . number_format($note->grand_total, 2),
                    'status' => $note->status,
                    'action' => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        $suppliers = Supplier::where('status', 'Active')->get();
        return view('debit_notes.view', compact('suppliers'));
    }

    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit debit-note')) {
                return unauthorizedRedirect();
            }
            $debitNote = DebitNote::with(['items.rawMaterial', 'items.uom'])->findOrFail($id);
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create debit-note')) {
                return unauthorizedRedirect();
            }
        }

        $debitNote = $debitNote ?? null;

        if (request()->isMethod('post')) {
            $request = request();

            $rules = [
                'debit_note_no' => ($id ? 'nullable' : 'required') . '|string|max:50|unique:debit_notes,debit_note_no,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'debit_note_date' => 'required|date',
                'purchase_invoice_id' => 'required|exists:purchase_invoices,id',
                'supplier_id' => 'required|exists:suppliers,id',
                'items' => 'required|array|min:1',
                'sub_total' => 'required|numeric|min:0',
                'grand_total' => 'required|numeric|min:0',
                'reference_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            ];

            $validated = $request->validate($rules);

            DB::beginTransaction();
            try {
                $referenceDocument = $id ? $debitNote->reference_document : null;
                if ($request->hasFile('reference_document')) {
                    $file = $request->file('reference_document');
                    $filename = 'debit_note_' . time() . '.' . $file->getClientOriginalExtension();
                    $uploadPath = public_path('uploads/debit_notes');
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    $file->move($uploadPath, $filename);
                    $referenceDocument = $filename;
                }

                $debitNoteData = [
                    'debit_note_no' => $request->debit_note_no,
                    'debit_note_date' => Carbon::parse($request->debit_note_date)->format('Y-m-d'),
                    'purchase_invoice_id' => $request->purchase_invoice_id,
                    'supplier_id' => $request->supplier_id,
                    'reason' => $request->reason,
                    'other_state' => $request->other_state ?? 'N',
                    'igst_percent' => $request->igst_percent ?? 0,
                    'cgst_percent' => $request->cgst_percent ?? 0,
                    'sgst_percent' => $request->sgst_percent ?? 0,
                    'sub_total' => $request->sub_total,
                    'tax_amount' => $request->tax_amount ?? 0,
                    'other_charges' => $request->other_charges ?? 0,
                    'round_off_type' => $request->round_off_type ?? 'Add',
                    'round_off' => $request->round_off ?? 0,
                    'grand_total' => $request->grand_total,
                    'remarks' => $request->remarks,
                    'reference_document' => $referenceDocument,
                    'status' => 'Active',
                ];

                if ($id) {
                    $debitNote->update($debitNoteData);
                    DebitNoteItem::where('debit_note_id', $id)->delete();
                    $message = 'Debit Note updated successfully';
                    addLog('update', 'Debit Note', 'debit_notes', $id, null, $debitNoteData);
                } else {
                    $debitNoteData['created_by'] = auth()->id();
                    $debitNote = DebitNote::create($debitNoteData);
                    $message = 'Debit Note created successfully';
                    addLog('create', 'Debit Note', 'debit_notes', $debitNote->id, null, $debitNoteData);
                }

                foreach ($request->items as $item) {
                    if (isset($item['selected']) && $item['selected'] == '1') {
                        DebitNoteItem::create([
                            'debit_note_id' => $debitNote->id,
                            'purchase_invoice_item_id' => $item['purchase_invoice_item_id'],
                            'raw_material_id' => $item['raw_material_id'],
                            'quantity' => $item['quantity'],
                            'uom_id' => $item['uom_id'],
                            'rate' => $item['rate'],
                            'amount' => $item['amount'],
                        ]);
                    }
                }

                DB::commit();
                return redirect('debit_notes')->with('success', $message);
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->withErrors(['error' => 'Failed to save: ' . $e->getMessage()]);
            }
        }

        $purchaseInvoices = PurchaseInvoice::with('supplier')->orderBy('id', 'desc')->get();
        
        $nextDebitNoteNo = '';
        if (!$id) {
            $setting = \App\Models\Setting::first();
            $prefix = ($setting && $setting->debit_note_prefix) ? $setting->debit_note_prefix : 'DN-';
            $lastDebitNote = DebitNote::where('debit_note_no', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
            if ($lastDebitNote) {
                $lastNumber = intval(substr($lastDebitNote->debit_note_no, strlen($prefix)));
                $nextDebitNoteNo = $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextDebitNoteNo = $prefix . '0001';
            }
        }

        return view('debit_notes.add', compact('debitNote', 'purchaseInvoices', 'nextDebitNoteNo'));
    }

    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view debit-note')) {
            return unauthorizedRedirect();
        }
        $debitNote = DebitNote::with(['supplier', 'purchaseInvoice', 'items.rawMaterial', 'items.uom'])->findOrFail($id);
        return view('debit_notes.view_details', compact('debitNote'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete debit-note')) {
            return unauthorizedRedirect();
        }
        $debitNote = DebitNote::findOrFail($id);
        $debitNote->delete();
        return redirect('debit_notes')->with('success', 'Debit Note deleted successfully');
    }

    public function getInvoiceDetails($id)
    {
        $invoice = PurchaseInvoice::with(['supplier', 'items.rawMaterial', 'items.uom'])->findOrFail($id);
        
        $items = $invoice->items->map(function($item) {
            return [
                'id' => $item->id,
                'raw_material_id' => $item->raw_material_id,
                'raw_material_name' => $item->rawMaterial ? $item->rawMaterial->name : '-',
                'uom_id' => $item->uom_id,
                'uom_code' => $item->uom ? $item->uom->uom_code : '-',
                'hsn_code' => $item->hsn_code,
                'quantity' => $item->quantity,
                'rate' => $item->rate,
                'amount' => $item->amount,
            ];
        });

        return response()->json([
            'success' => true,
            'supplier_id' => $invoice->supplier_id,
            'supplier_name' => $invoice->supplier ? $invoice->supplier->name : '-',
            'invoice_date' => $invoice->invoice_date->format('Y-m-d'),
            'items' => $items,
            'other_state' => $invoice->other_state ? 'Y' : 'N',
            'igst_percent' => $invoice->igst_percent,
            'cgst_percent' => $invoice->cgst_percent,
            'sgst_percent' => $invoice->sgst_percent,
        ]);
    }
}

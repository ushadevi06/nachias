<?php

namespace App\Http\Controllers;

use App\Models\DebitNote;
use App\Models\DebitNoteItem;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\Supplier;
use App\Models\Setting;
use App\Models\Charge;
use App\Models\DebitNoteCharge;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DebitNoteController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view debit-notes')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $query = DebitNote::with(['supplier', 'purchaseInvoice'])
                ->orderBy('id', 'desc');

            if (!empty($request->supplier_id)) {
                $query->where('supplier_id', $request->supplier_id);
            }

            $totalRecords = $query->count();

            if ($request->has('search') && !empty($request->input('search')['value'])) {
                $search = $request->input('search')['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('debit_note_no', 'like', "%{$search}%")
                        ->orWhereRaw("DATE_FORMAT(debit_note_date, '%d-%m-%Y') LIKE ?", ["%{$search}%"])
                        ->orWhere('grand_total', 'like', "%{$search}%")
                        ->orWhereHas('purchaseInvoice', function ($q2) use ($search) {
                            $q2->where('invoice_no', 'like', "%{$search}%");
                        })
                        ->orWhereHas('supplier', function ($q4) use ($search) {
                            $q4->where('name', 'like', "%{$search}%")
                               ->orWhere('code', 'like', "%{$search}%");
                        });

                    try {
                        $parsedDate = \Carbon\Carbon::createFromFormat('d-m-Y', $search);
                        if ($parsedDate) {
                            $q->orWhere('debit_note_date', $parsedDate->format('Y-m-d'));
                        }
                    } catch (\Exception $e) {}
                });
            }

            $filteredRecords = $query->count();

            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            if ($length != -1) {
                $query->skip($start)->take($length);
            }

            $debitNotes = $query->get();
            $data = [];
            $count = $start + 1;

            foreach ($debitNotes as $note) {
                $status_options = ['Draft', 'Approved', 'Cancelled'];
                $status = '<select class="form-select status-dropdown" data-id="' . $note->id . '">';
                foreach ($status_options as $option) {
                    $selected = ($note->status == $option) ? 'selected' : '';
                    $disabled = ($option == 'Draft' && $note->status != 'Draft') ? 'disabled' : '';
                    $status .= '<option value="' . $option . '" ' . $selected . ' ' . $disabled . '>' . $option . '</option>';
                }
                $status .= '</select>';
                $status .= '<div class="status_msg_' . $note->id . '"></div>';

                $action = '<div class="button-box">';
                if (auth()->id() == 1 || auth()->user()->can('view_details debit-notes')) {
                    $action .= '<a href="' . url('debit_notes/view/' . $note->id) . '" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>';
                }
                if ((auth()->id() == 1 || auth()->user()->can('edit debit-notes')) && $note->status == 'Draft') {
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
                    'status' => $status,
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

        $suppliers = Supplier::where('status', 'Active')->orderBy('id','desc')->get();
        return view('debit_notes.view', compact('suppliers'));
    }

    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit debit-notes')) {
                return unauthorizedRedirect();
            }
            $debitNote = DebitNote::with(['purchaseInvoice', 'items.rawMaterial', 'items.uom', 'charges'])->findOrFail($id);
            if ($debitNote->status == 'Approved') {
                return redirect('debit_notes')->with('error', 'Approved Debit Notes cannot be edited.');
            }
            $charges = $debitNote->charges;
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create debit-notes')) {
                return unauthorizedRedirect();
            }
        }

        $debitNote = $debitNote ?? null;
        $charges = $charges ?? collect();

        if (request()->isMethod('post')) {
            $request = request();

            $rules = [
                'debit_note_no' => [($id ? 'nullable' : 'required'), 'string', 'max:50', 'not_regex:/^0+$/', 'unique:debit_notes,debit_note_no,' . ($id ?? 'NULL') . ',id,deleted_at,NULL'],
                'debit_note_date' => 'required',
                'purchase_invoice_id' => 'required|exists:purchase_invoices,id',
                'supplier_id' => 'required|exists:suppliers,id',
                'reason' => 'nullable|string|min:5|max:255',
                'items' => 'required|array|min:1',
                'items.*.quantity' => 'required_if:items.*.selected,1|numeric|gt:0',
                'sub_total' => 'required|numeric|min:0',
                'discount_percent' => 'nullable|numeric|min:0|max:100',
                'discount_amount' => 'nullable|numeric|min:0',
                'igst_percent' => 'nullable|numeric|min:0|max:100',
                'cgst_percent' => 'nullable|numeric|min:0|max:100',
                'sgst_percent' => 'nullable|numeric|min:0|max:100',
                'round_off' => 'nullable|numeric|min:0|max:99.99',
                'round_off_type' => 'required|in:Add,Less',
                'taxable_amount' => 'required|numeric|min:0',
                'other_charges' => 'nullable|numeric|min:0',
                'charges_select' => 'nullable',
                'charge_amount' => 'nullable|numeric|min:0',
                'charges.amount.*' => 'nullable|numeric|min:0',
                'grand_total' => 'required|numeric|min:0',
                'status' => 'required|in:Draft,Approved,Cancelled',
                'reference_document' => 'nullable|mimes:pdf,jpg,jpeg,png,webp,doc,docx|max:2048',
                'remarks' => 'nullable|string|min:5|max:255|regex:/^[^<>]*$/',
            ];

            $messages = [
                '*.required' => 'This field is required.',
                '*.unique' => 'This field already exists.',
                '*.not_regex' => 'This field is an invalid format.',
                '*.regex' => 'This field is an invalid format',
                'items.*.quantity.gt' => 'Quantity must be greater than 0.',
                'items.*.quantity.min' => 'Quantity must be greater than 0.',
                'items.*.quantity.required_if' => 'Quantity must be greater than 0.',
                'reference_document.mimes' => 'Upload a valid file (e.g., .pdf, .doc, .docx, .jpg, .png, .jpeg, .webp).',
                'reference_document.max' => 'Uploaded file cannot exceed 2MB.',
                '*.attached_file.mimes' => 'Upload a valid file (e.g., .pdf, .doc, .docx, .jpg, .png, .jpeg, .webp).',
                '*.attached_file.max' => 'Uploaded file cannot exceed 2MB.',
                'discount_percent.min' => 'Discount cannot be negative. Please enter 0 or a positive value.',
                'discount_percent.max' => 'Discount percentage cannot exceed 100%.',
                'igst_percent.numeric' => 'IGST % must be a number.',
                'igst_percent.min' => 'IGST cannot be negative. Please enter 0 or a positive value.',
                'igst_percent.max' => 'IGST percentage cannot exceed 100%.',
                'cgst_percent.numeric' => 'CGST % must be a number.',
                'cgst_percent.min' => 'CGST cannot be negative. Please enter 0 or a positive value.',
                'cgst_percent.max' => 'CGST percentage cannot exceed 100%.',
                'sgst_percent.numeric' => 'SGST % must be a number.',
                'sgst_percent.min' => 'SGST cannot be negative. Please enter 0 or a positive value.',
                'sgst_percent.max' => 'SGST percentage cannot exceed 100%.',
                'round_off.min' => 'Round off cannot be negative. Please enter 0 or a positive value.',
                'round_off.max' => 'Round off amount cannot exceed 99.99.',
                '*.min' => 'This field must be at least :min characters.',
                '*.max' => 'This field should not be more than :max characters.',
            ];

            $validated = $request->validate($rules, $messages);

            // Calculate pre-GST charges total from request
            $preGstTotal = 0;
            if ($request->has('charges') && isset($request->charges['amount'])) {
                foreach ($request->charges['amount'] as $idx => $amt) {
                    $taxType = $request->charges['tax_type'][$idx] ?? 'Post-GST';
                    if ($taxType === 'Pre-GST') {
                        $preGstTotal += floatval($amt);
                    }
                }
            }

            $discountPercent = floatval($request->discount_percent ?? 0);
            $discountAmountCalculated = ($request->sub_total + $preGstTotal) * ($discountPercent / 100);
            $taxableAmountCalculated = ($request->sub_total + $preGstTotal) - $discountAmountCalculated;

            if ($taxableAmountCalculated < 0) {
                return back()->withInput()->withErrors(['discount_percent' => 'Discount cannot exceed the subtotal amount.']);
            }

            $itemErrors = [];
            $hasSelectedItem = false;
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $index => $item) {
                    if (isset($item['selected']) && $item['selected'] == '1') {
                        $hasSelectedItem = true;
                        $qty = floatval($item['quantity'] ?? 0);
                        if ($qty <= 0) {
                            $itemErrors["items.$index.quantity"] = "Quantity must be greater than 0.";
                        }

                        $rejectedQty = \App\Models\GrnEntryItem::where('purchase_invoice_item_id', $item['purchase_invoice_item_id'])->sum('qty_rejected');

                        $alreadyDebitedQuery = \App\Models\DebitNoteItem::where('purchase_invoice_item_id', $item['purchase_invoice_item_id']);
                        if ($id) {
                            $alreadyDebitedQuery->where('debit_note_id', '!=', $id);
                        }
                        $alreadyDebited = $alreadyDebitedQuery->sum('quantity');

                        $availableQty = $rejectedQty - $alreadyDebited;
                        if ($qty > $availableQty) {
                            $itemErrors["items.$index.quantity"] = "Quantity exceeds available rejected quantity ($availableQty).";
                        }
                    }
                }
            }

            if (!$hasSelectedItem) {
                $itemErrors['items'] = "Please select at least one item.";
            }

            if (!empty($itemErrors)) {
                return back()->withInput()->withErrors($itemErrors);
            }

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

                    if ($id && $debitNote->reference_document && file_exists(public_path('uploads/debit_notes/' . $debitNote->reference_document))) {
                        unlink(public_path('uploads/debit_notes/' . $debitNote->reference_document));
                    }

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
                    'discount_percent' => $request->discount_percent ?? 0,
                    'discount_amount' => $request->discount_amount ?? 0,
                    'taxable_amount' => $request->taxable_amount ?? 0,
                    'other_charges' => $request->other_charges ?? 0,
                    'tax_amount' => $request->tax_amount ?? 0,
                    'round_off_type' => $request->round_off_type ?? 'Add',
                    'round_off' => $request->round_off ?? 0,
                    'grand_total' => $request->grand_total,
                    'remarks' => $request->remarks,
                    'reference_document' => $referenceDocument,
                    'status' => $request->status ?? 'Draft',
                ];

                if ($id) {
                    $debitNote->update($debitNoteData);
                    DebitNoteItem::where('debit_note_id', $id)->delete();
                    DebitNoteCharge::where('debit_note_id', $id)->delete();
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

                if ($request->has('charges') && isset($request->charges['charge_id'])) {
                    $chargeIds = $request->charges['charge_id'];
                    $chargeNames = $request->charges['name'];
                    $chargeAmounts = $request->charges['amount'];
                    $taxTypes = $request->charges['tax_type'] ?? [];

                    foreach ($chargeIds as $index => $chargeId) {
                        DebitNoteCharge::create([
                            'debit_note_id' => $debitNote->id,
                            'charge_id' => $chargeId,
                            'charge_name' => $chargeNames[$index],
                            'charge_amount' => $chargeAmounts[$index],
                            'tax_type' => $taxTypes[$index] ?? 'Post-GST',
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

        $purchaseInvoices = PurchaseInvoice::with(['supplier', 'items'])
            ->whereHas('grnEntries.grnEntryItems', function ($q) {
                $q->where('qty_rejected', '>', 0);
            })
            ->orderBy('id', 'desc')->get();

        $purchaseInvoices = $purchaseInvoices->filter(function ($invoice) use ($id) {
            foreach ($invoice->items as $item) {
                $rejectedQty = \App\Models\GrnEntryItem::where('purchase_invoice_item_id', $item->id)->sum('qty_rejected');
                
                $alreadyDebitedQuery = \App\Models\DebitNoteItem::where('purchase_invoice_item_id', $item->id);
                if ($id) {
                    $alreadyDebitedQuery->where('debit_note_id', '!=', $id);
                }
                $alreadyDebited = $alreadyDebitedQuery->sum('quantity');

                if ($rejectedQty > $alreadyDebited) {
                    return true;
                }
            }
            return false;
        })->values();

        $nextDebitNoteNo = '';
        if (!$id) {
            $setting = Setting::first();
            $prefix = ($setting && $setting->debit_note_prefix) ? $setting->debit_note_prefix : 'DN-';
            $lastDebitNote = DebitNote::where('debit_note_no', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
            if ($lastDebitNote) {
                $lastNumber = intval(substr($lastDebitNote->debit_note_no, strlen($prefix)));
                $nextDebitNoteNo = $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextDebitNoteNo = $prefix . '0001';
            }
        }

        return view('debit_notes.add', compact('debitNote', 'purchaseInvoices', 'nextDebitNoteNo', 'charges'));
    }

    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view_details debit-notes')) {
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
        $invoice = PurchaseInvoice::with(['supplier', 'items.rawMaterial.storeCategory', 'items.uom', 'items.purchaseOrderItem'])->findOrFail($id);

        $items = collect($invoice->items)->map(function ($item) {
            $rejectedQty = \App\Models\GrnEntryItem::where('purchase_invoice_item_id', $item->id)->sum('qty_rejected');
            $alreadyDebited = \App\Models\DebitNoteItem::where('purchase_invoice_item_id', $item->id)->sum('quantity');
            $availableQty = $rejectedQty - $alreadyDebited;
            
            $grnItem = \App\Models\GrnEntryItem::where('purchase_invoice_item_id', $item->id)->first();
            $categoryName = $item->rawMaterial->storeCategory->store_category_name ?? '-';
            $artNo = $grnItem->art_no ?? '-';
            $poItem = $item->purchaseOrderItem ?? ($invoice->purchase_order_id ? \App\Models\PurchaseOrderItem::where('purchase_order_id', $invoice->purchase_order_id)->where('raw_material_id', $item->raw_material_id)->first() : null);
            $supplierDesignName = $poItem->supplier_design_name ?? '-';

            return [
                'id' => $item->id,
                'raw_material_id' => $item->raw_material_id,
                'raw_material_name' => $item->rawMaterial ? $item->rawMaterial->name : '-',
                'category_name' => $categoryName,
                'art_no' => $artNo,
                'supplier_design_name' => $supplierDesignName,
                'uom_id' => $item->uom_id,
                'uom_code' => $item->uom ? $item->uom->uom_code : '-',
                'hsn_code' => $item->hsn_code,
                'quantity' => $availableQty,
                'max_quantity' => $availableQty,
                'rate' => $item->rate,
                'amount' => round($availableQty * $item->rate, 2),
            ];
        })->filter(function ($item) {
            return $item['quantity'] > 0;
        })->values();

        return response()->json([
            'success' => true,
            'supplier_id' => $invoice->supplier_id,
            'supplier_name' => $invoice->supplier ? $invoice->supplier->name . ($invoice->supplier->code ? ' - ' . $invoice->supplier->code : '') : '-',
            'invoice_date' => $invoice->invoice_date->format('Y-m-d'),
            'items' => $items,
            'other_state' => $invoice->other_state ? 'Y' : 'N',
            'igst_percent' => $invoice->igst_percent,
            'cgst_percent' => $invoice->cgst_percent,
            'sgst_percent' => $invoice->sgst_percent,
            'discount_percent' => $invoice->discount_percent ?? 0,
        ]);
    }
    public function getSupplierInvoices($supplierId)
    {
        $invoices = PurchaseInvoice::with('items')->where('supplier_id', $supplierId)
            ->whereHas('grnEntries.grnEntryItems', function ($q) {
                $q->where('qty_rejected', '>', 0);
            })
            ->orderBy('id', 'desc')->get();

        $invoices = $invoices->filter(function ($invoice) {
            foreach ($invoice->items as $item) {
                $rejectedQty = \App\Models\GrnEntryItem::where('purchase_invoice_item_id', $item->id)->sum('qty_rejected');
                $alreadyDebited = \App\Models\DebitNoteItem::where('purchase_invoice_item_id', $item->id)->sum('quantity');
                if ($rejectedQty > $alreadyDebited) {
                    return true;
                }
            }
            return false;
        })->values();
        return response()->json([
            'success' => true,
            'invoices' => $invoices
        ]);
    }
    public function updateStatus(Request $request, $id)
    {
        $debitNote = DebitNote::findOrFail($id);
        $oldData = $debitNote->toArray();
        $debitNote->status = $request->status;
        $debitNote->save();
        $newData = $debitNote->toArray();
        addLog('update_status', 'Debit Note Status', 'debit_notes', $id, $oldData, $newData);
        return response()->json([
            'success' => true,
            'status' => $debitNote->status
        ]);
    }
    public function print($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view debit-notes')) {
            return unauthorizedRedirect();
        }

        $debitNote = DebitNote::with(['supplier', 'purchaseInvoice', 'items.rawMaterial', 'items.uom'])->findOrFail($id);
        $setting = Setting::first();

        $totalInWords = numberToWords($debitNote->grand_total);

        $is_print = true;
        return view('debit_notes.debit_note_pdf', compact('debitNote', 'setting', 'totalInWords', 'is_print'));
    }
    public function download($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view debit-notes')) {
            return unauthorizedRedirect();
        }

        $debitNote = DebitNote::with(['supplier', 'purchaseInvoice', 'items.rawMaterial', 'items.uom'])->findOrFail($id);
        $setting = Setting::first();

        $totalInWords = numberToWords($debitNote->grand_total);

        $data = [
            'debitNote' => $debitNote,
            'setting' => $setting,
            'totalInWords' => $totalInWords
        ];

        $pdf = Pdf::loadView('debit_notes.debit_note_pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('DebitNote_' . $debitNote->debit_note_no . '.pdf');
    }
}

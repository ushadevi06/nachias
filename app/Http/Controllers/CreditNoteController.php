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

            if (!empty($request->status)) {
                $query->where('status', $request->status);
            }

            if (!empty($request->note_date_range)) {
                $dates = explode(' to ', $request->note_date_range);
                if (count($dates) == 2) {
                    $startDate = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                    $query->whereBetween('note_date', [$startDate, $endDate]);
                } elseif (count($dates) == 1) {
                    $startDate = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $query->whereDate('note_date', $startDate);
                }
            }

            $totalRecords = $query->count();

            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $numericSearch = str_replace([',', '₹', 'Rs.', ' '], '', $search);

                $matchingInvoiceIds = \App\Models\SalesInvoice::where('inv_no', 'like', "%{$search}%")->pluck('id')->toArray();

                $query->where(function ($q) use ($search, $numericSearch, $matchingInvoiceIds) {
                    $q->where('note_no', 'like', "%{$search}%")
                      ->orWhereRaw("DATE_FORMAT(note_date, '%d-%m-%Y') LIKE ?", ["%{$search}%"])
                      ->orWhere('grand_total', 'like', "%{$numericSearch}%")
                      ->orWhere('status', 'like', "%{$search}%")
                      ->orWhereHas('customer', function($q2) use ($search) {
                          $q2->where('name', 'like', "%{$search}%")
                             ->orWhere('code', 'like', "%{$search}%");
                      })
                      ->orWhereHas('salesInvoice', function($q3) use ($search) {
                          $q3->where('inv_no', 'like', "%{$search}%");
                      });

                    if (!empty($matchingInvoiceIds)) {
                        $q->orWhere(function ($q4) use ($matchingInvoiceIds) {
                            foreach ($matchingInvoiceIds as $invId) {
                                $q4->orWhereJsonContains('sales_invoice_ids', (string)$invId)
                                   ->orWhereJsonContains('sales_invoice_ids', (int)$invId);
                            }
                        });
                    }
                });
            }

            $filteredRecords = $query->count();

            if ($request->has('start') && $request->has('length') && $request->length != '-1') {
                $query->skip($request->start)->take($request->length);
            }

            $creditNotes = $query->get();
            $data = [];
            $count = $request->has('start') ? intval($request->start) + 1 : 1;

            foreach ($creditNotes as $note) {
                $status_options = ['Draft', 'Approved', 'Cancelled'];
                $status = '<select class="form-select status-dropdown" data-id="' . $note->id . '">';
                foreach ($status_options as $option) {
                    $selected = ($note->status == $option) ? 'selected' : '';
                    $disabled = '';
                    if ($note->status == 'Approved') {
                        if ($option == 'Draft' || $option == 'Cancelled') {
                            $disabled = 'disabled';
                        }
                    } elseif ($note->status != 'Draft' && $option == 'Draft') {
                        $disabled = 'disabled';
                    }
                    $status .= '<option value="' . $option . '" ' . $selected . ' ' . $disabled . '>' . $option . '</option>';
                }
                $status .= '</select>';
                $status .= '<div class="status_msg_' . $note->id . '"></div>';

                $action = '<div class="button-box">';
                if(auth()->id() == 1 || auth()->user()->can('view_details credit-notes')) {
                    $action .= '<a href="' . url('credit_notes/view/' . $note->id) . '" class="btn btn-view" title="View Details"><i class="icon-base ri ri-eye-line"></i></a>';
                }
                if(auth()->id() == 1 || auth()->user()->can('edit credit-notes')) {
                    $action .= '<a href="' . url('credit_notes/add/' . $note->id) . '" class="btn btn-edit" title="Edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }
                // if(auth()->id() == 1 || auth()->user()->can('delete credit-notes')) {
                //     $action .= '<a href="javascript:;" class="btn btn-delete delete-btn" title="Delete" onclick="delete_data(\'' . url('credit_notes/delete/' . $note->id) . '\')"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                // }
                
                $multiSalesInvoices = !empty($note->sales_invoice_ids) && count($note->sales_invoice_ids) > 1;
                $eInvoiceBtn = '';

                if ($note->einvoice_status == 'generated') {
                    $eInvoiceBtn = '<button type="button" class="btn btn-danger einvoice-cancel-btn" data-id="' . $note->id . '" title="Cancel E-Invoice" style="padding: 0.25rem 0.5rem; font-size: 0.875rem; border-radius: 4px; margin-left: 5px;"><i class="ri ri-close-circle-line"></i></button>';
                } else {
                    if ($multiSalesInvoices) {
                        $eInvoiceBtn = '<button type="button" class="btn btn-info text-white" disabled  title="E-Invoice generation is allowed only for Credit Notes linked to a single Sales Invoice." style="padding: 0.25rem 0.5rem; font-size: 0.875rem; border-radius: 4px; margin-left: 5px;"><i class="ri ri-receipt-line"></i></button>';
                    } else {
                        $eInvoiceBtn = '<button type="button" class="btn btn-info text-white einvoice-generate-btn" data-id="' . $note->id . '" title="Generate E-Invoice" style="padding: 0.25rem 0.5rem; font-size: 0.875rem; border-radius: 4px; margin-left: 5px;"><i class="ri ri-receipt-line"></i></button>';
                    }
                }

                $action .= $eInvoiceBtn;
                $action .= '</div>';

                $invoiceNos = '-';
                if (!empty($note->sales_invoice_ids) && is_array($note->sales_invoice_ids)) {
                    $invoiceNos = SalesInvoice::whereIn('id', $note->sales_invoice_ids)->pluck('inv_no')->implode(', ');
                } elseif ($note->salesInvoice) {
                    $invoiceNos = $note->salesInvoice->inv_no;
                }

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'note_no' => $note->note_no,
                    'note_date' => $note->note_date->format('d-m-Y'),
                    'sales_invoice_no' => $invoiceNos,
                    'customer_name' => $note->customer ? $note->customer->name : '-',
                    'grand_total' => '₹' . number_format($note->grand_total, 2),
                    'status' => $status,
                    'action' => $action,
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        }

        $customers = Customer::whereHas('salesInvoices', function($q) {
            $q->whereNull('einvoice_status')->orWhere('einvoice_status', '!=', 'cancelled');
        })->orderBy('id', 'desc')->get();
        return view('credit_notes.view', compact('customers'));
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit credit-notes')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create credit-notes')) {
                return unauthorizedRedirect();
            }
        }
        
        $creditNote = null;
        if ($id) {
            $creditNote = CreditNote::with('items.item', 'items.uom', 'items.brandCategory', 'charges')->findOrFail($id);
        }
        if ($request->isMethod('POST')) {
            if ($creditNote && $creditNote->einvoice_status === 'generated') {
                $request->validate([
                    'show_fields' => 'nullable|array',
                ]);
                $creditNote->show_fields = $request->show_fields ?? [];
                $creditNote->save();
                return redirect('credit_notes')->with('success', 'Credit Note PDF settings updated successfully.');
            }

            $request->validate([
                'note_no' => 'required|string|max:50|unique:credit_notes,note_no,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'note_date' => 'required|date_format:d-m-Y',
                'sales_invoice_ids' => 'required|array|min:1',
                'sales_invoice_ids.*' => 'exists:sales_invoices,id',
                'customer_id' => 'required|exists:customers,id',
                'reason' => 'required|string',
                'reason_detail' => 'nullable|string|max:1000',
                'zone_id' => 'nullable|exists:zones,id',
                'agent_id' => 'nullable|exists:sales_agents,id',
                'items' => 'required|array|min:1',
                'sub_total' => 'required|numeric',
                'discount_percent' => 'nullable|numeric|min:0|max:100',
                'discount' => 'nullable|numeric|min:0',
                'tax_amount' => 'required|numeric',
                'other_charges' => 'nullable|numeric|min:0',
                'round_off' => 'nullable|numeric|min:0|max:99.99',
                'round_off_type' => 'nullable|string|in:Add,Less',
                'grand_total' => 'required|numeric',
                'reference_document' => 'nullable|mimes:pdf,jpg,jpeg,png,webp,doc,docx|max:2048',
                'remarks' => 'nullable|string|min:5|max:255|regex:/^[^<>]*$/',
                'show_fields' => 'nullable|array',
            ], [
                '*.required' => 'This field is required.',
                '*.exists' => 'Selected value is invalid.',
                '*.unique' => 'This field already exists.',
                '*.date' => 'Please enter a valid date.',
                '*.numeric' => 'This field must be a number.',
                'round_off.min' => 'Round off cannot be negative. Please enter 0 or a positive value.',
                'round_off.max' => 'Round off amount cannot exceed 99.99.',
                '*.min' => 'This field must be at least :min characters.',
                '*.max' => 'This field should not be more than :max characters.',
            ]);

            $hasSelected = false;
            foreach ($request->items as $item) {
                if (($item['quantity'] ?? 0) > 0) {
                    $hasSelected = true;
                    
                    $alreadyReturned = DB::table('credit_note_items')
                        ->join('credit_notes', 'credit_notes.id', '=', 'credit_note_items.credit_note_id')
                        ->where('credit_note_items.sales_invoice_item_id', $item['sales_invoice_item_id'])
                        ->whereNull('credit_notes.deleted_at')
                        ->whereNull('credit_note_items.deleted_at')
                        ->whereIn('credit_notes.status', ['Draft', 'Approved'])
                        ->when($id, function ($q) use ($id) {
                            return $q->where('credit_notes.id', '!=', $id);
                        })
                        ->sum('credit_note_items.quantity');
                        
                    $invoiceItem = SalesInvoiceItem::findOrFail($item['sales_invoice_item_id']);
                    $balanceQty = max(0, $invoiceItem->quantity - $alreadyReturned);
                    
                    if ($item['quantity'] > $balanceQty) {
                        return back()->withInput()->withErrors(['error' => 'Return quantity for item ' . ($invoiceItem->item ? $invoiceItem->item->name : '') . ' cannot exceed the remaining balance quantity of ' . $balanceQty]);
                    }
                }
            }

            if (!$hasSelected) {
                return back()->withInput()->withErrors(['error' => 'At least one item must have a quantity greater than zero.']);
            }

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
                $noteDate = null;
                if ($request->note_date) {
                    try {
                        $noteDate = \Carbon\Carbon::createFromFormat('d-m-Y', $request->note_date)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $noteDate = \Carbon\Carbon::parse($request->note_date)->format('Y-m-d');
                    }
                }

                $creditNoteData = [
                    'note_no' => $request->note_no,
                    'note_date' => $noteDate ?? \Carbon\Carbon::now()->format('Y-m-d'),
                    'sales_invoice_ids' => $request->sales_invoice_ids,
                    'customer_id' => $request->customer_id,
                    'reason' => $request->reason,
                    'fault' => $request->fault,
                    'reason_detail' => $request->reason_detail,
                    'zone_id' => $request->zone_id,
                    'agent_id' => $request->agent_id,
                    'other_state' => $request->is_other_state == 'yes',
                    'igst_percent' => $request->igst_percent ?? 0,
                    'igst' => $request->igst_amt ?? 0,
                    'cgst_percent' => $request->cgst_percent ?? 0,
                    'cgst' => $request->cgst_amt ?? 0,
                    'sgst_percent' => $request->sgst_percent ?? 0,
                    'sgst' => $request->sgst_amt ?? 0,
                    'sub_total' => $request->sub_total,
                    'discount_percent' => $request->discount_percent ?? 0,
                    'discount' => $request->discount ?? 0,
                    'tax_amount' => $request->tax_amount ?? 0,
                    'other_charges' => $request->other_charges ?? 0,
                    'round_off' => $request->round_off ?? 0,
                    'round_off_type' => $request->round_off_type ?? 'Add',
                    'grand_total' => $request->grand_total,
                    'remarks' => $request->remarks,
                    'reference_doc' => $referenceDoc,
                    'status' => $request->status ?? 'Draft',
                    'show_fields' => $request->show_fields ?? [],
                ];
                if ($id) {
                    $oldData = $creditNote->toArray();
                    $creditNote->update($creditNoteData);
                    CreditNoteItem::where('credit_note_id', $id)->delete();
                    \App\Models\CreditNoteCharge::where('credit_note_id', $id)->delete();
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
                    if (($item['quantity'] ?? 0) > 0) {
                        CreditNoteItem::create([
                            'credit_note_id' => $creditNote->id,
                            'sales_invoice_item_id' => $item['sales_invoice_item_id'],
                            'quantity' => $item['quantity'],
                            'mrp' => $item['mrp'] ?? 0,
                            'rate' => $item['rate'] ?? 0,
                            'amount' => $item['amount'] ?? 0,
                            'add_to_inventory' => isset($item['add_to_inventory']) ? 1 : 0,
                        ]);
                    }
                }

                if ($request->has('charges') && isset($request->charges['charge_id'])) {
                    $chargeIds = $request->charges['charge_id'];
                    $chargeNames = $request->charges['name'];
                    $chargeAmounts = $request->charges['amount'];
                    $taxTypes = $request->charges['tax_type'] ?? [];

                    foreach ($chargeIds as $idx => $chargeId) {
                        \App\Models\CreditNoteCharge::create([
                            'credit_note_id' => $creditNote->id,
                            'charge_id' => $chargeId,
                            'charge_name' => $chargeNames[$idx] ?? '',
                            'charge_amount' => $chargeAmounts[$idx] ?? 0,
                            'tax_type' => $taxTypes[$idx] ?? null,
                        ]);
                    }
                }

                $this->handleStockReversal($creditNote, $id ? $oldData['status'] : null, $creditNote->status);

                DB::commit();
                return redirect(url('credit_notes'))->with('success', $msg);
            }
            catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->withErrors(['error' => 'Failed to save: ' . $e->getMessage()]);
            }
        }

        $customers = Customer::whereHas('salesInvoices', function($q) {
            $q->whereNull('einvoice_status')->orWhere('einvoice_status', '!=', 'cancelled');
        })->get();
        $zones = \App\Models\Zone::active()->get();
        $sales_agent = \App\Models\SalesAgent::active()->get();
        $charges = \App\Models\Charge::where('status', 'Active')->orderBy('charge_name')->get();
        
        $salesInvoices = [];
        $creditNoteCharges = collect();
        if ($creditNote) {
            $salesInvoices = SalesInvoice::where('customer_id', $creditNote->customer_id)
                ->where(function($q) {
                    $q->whereNull('einvoice_status')->orWhere('einvoice_status', '!=', 'cancelled');
                })
                ->latest()->get();
            $creditNoteCharges = $creditNote->charges;
        } elseif (old('customer_id') && old('sales_invoice_ids')) {
            $salesInvoices = SalesInvoice::where('customer_id', old('customer_id'))
                ->where(function($q) {
                    $q->whereNull('einvoice_status')->orWhere('einvoice_status', '!=', 'cancelled');
                })
                ->latest()->get();
        }

        $nextNoteNo = '';
        if (!$id) {
            $currentMonth = date('n');
            $currentYear = date('Y');
            if ($currentMonth >= 4) {
                $fyYear = $currentYear;
            } else {
                $fyYear = $currentYear - 1;
            }
            $fySuffix = substr($fyYear, -2);
            $prefix = 'RCN' . $fySuffix . '-';

            $lastNote = CreditNote::withTrashed()
                ->where('note_no', 'like', $prefix . '%')
                ->orderBy('id', 'desc')
                ->first();

            if ($lastNote && preg_match('/' . $prefix . '(\d+)/', $lastNote->note_no, $matches)) {
                $number = intval($matches[1]) + 1;
                $nextNoteNo = $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
            }
            else {
                $nextNoteNo = $prefix . '001';
            }
        }

        return view('credit_notes.add', compact('creditNote', 'customers', 'salesInvoices', 'nextNoteNo', 'zones', 'sales_agent', 'charges', 'creditNoteCharges'));
    }

    public function getInvoiceDetails(Request $request, $ids)
    {
        $invoiceIds = explode(',', $ids);
        $currentCreditNoteId = $request->query('credit_note_id');

        $invoices = SalesInvoice::with(['customer', 'items.item', 'items.uom', 'items.color', 'items.sizeRatio', 'items.stockEntryItem'])
            ->whereIn('id', $invoiceIds)
            ->get();

        if ($invoices->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No invoices found.']);
        }

        $firstInvoice = $invoices->first();
        $items = [];

        $salesInvoiceItemIds = $invoices->flatMap->items->pluck('id')->toArray();
        $stockEntryItemIds = $invoices->flatMap->items->pluck('stock_entry_item_id')->filter()->unique()->toArray();

        // 1. Bulk query for Already Returned quantities
        $returnedQuantities = DB::table('credit_note_items')
            ->join('credit_notes', 'credit_notes.id', '=', 'credit_note_items.credit_note_id')
            ->whereIn('credit_note_items.sales_invoice_item_id', $salesInvoiceItemIds)
            ->whereNull('credit_notes.deleted_at')
            ->whereNull('credit_note_items.deleted_at')
            ->whereIn('credit_notes.status', ['Draft', 'Approved'])
            ->when($currentCreditNoteId, function ($q) use ($currentCreditNoteId) {
                return $q->where('credit_notes.id', '!=', $currentCreditNoteId);
            })
            ->groupBy('credit_note_items.sales_invoice_item_id')
            ->select('credit_note_items.sales_invoice_item_id', DB::raw('SUM(credit_note_items.quantity) as total_returned'))
            ->pluck('total_returned', 'sales_invoice_item_id')
            ->toArray();

        // 2. Bulk query for Sales Order UOM
        $soItemsUom = [];
        if (!empty($stockEntryItemIds)) {
            $soItemsUom = \App\Models\SalesOrderItem::whereIn('stock_entry_item_id', $stockEntryItemIds)
                ->whereNotNull('uom_id')
                ->pluck('uom_id', 'stock_entry_item_id')
                ->toArray();
        }

        foreach ($invoices as $invoice) {
            foreach ($invoice->items as $item) {
                $alreadyReturned = $returnedQuantities[$item->id] ?? 0;
                $balanceQty = max(0, $item->quantity - $alreadyReturned);
                
                $uomCode = 'PCS';
                if ($item->stock_entry_item_id && isset($soItemsUom[$item->stock_entry_item_id])) {
                    $uomCode = $soItemsUom[$item->stock_entry_item_id];
                }

                $items[] = [
                    'id' => $item->id,
                    'invoice_id' => $invoice->id,
                    'invoice_no' => $invoice->inv_no,
                    'item_id' => $item->item_id,
                    'item_name' => ($item->stockEntryItem && $item->stockEntryItem->finished_item_code) ? $item->stockEntryItem->finished_item_code : ($item->item ? $item->item->name : '-'),
                    'item_code' => $item->item ? $item->item->code : '-',
                    'product_barcode' => $item->sku ?? '-',
                    'brand_category_id' => $item->brand_id,
                    'brand_category_name' => $item->brandCategory ? $item->brandCategory->name : '-',
                    'color_id' => $item->color_id,
                    'color_name' => !empty($item->api_color) ? $item->api_color : ($item->color ? $item->color->color_name : '-'),
                    'art_no' => $item->art_no ?? '-',
                    'size' => $item->size,
                    'size_name' => $item->sizeRatio ? $item->sizeRatio->size : $item->size,
                    'uom_id'              => $uomCode,  
                    'uom_code'            => $uomCode,  
                    'sleeve_type' => $item->sleeve_type,
                    'invoice_qty' => $item->quantity,
                    'returned_qty' => $alreadyReturned,
                    'balance_qty' => $balanceQty,
                    'mrp' => $item->mrp,
                    'rate' => $item->rate,
                    'amount' => $item->amount,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'customer_id' => $firstInvoice->customer_id,
            'customer_name' => $firstInvoice->customer ? $firstInvoice->customer->name : '-',
            'agent_id' => $firstInvoice->agent_id,
            'items' => $items,
            'other_state' => $firstInvoice->other_state ? 'yes' : 'no',
            'igst_percent' => $firstInvoice->igst_percent,
            'cgst_percent' => $firstInvoice->cgst_percent,
            'sgst_percent' => $firstInvoice->sgst_percent,
            'discount_percent' => $firstInvoice->sales_discount > 0 ? $firstInvoice->sales_discount : $firstInvoice->discount_percent,
        ]);
    }

    public function getCustomerInvoices($customerId)
    {
        $invoices = SalesInvoice::where('customer_id', $customerId)
            ->where(function($q) {
                $q->whereNull('einvoice_status')->orWhere('einvoice_status', '!=', 'cancelled');
            })
            ->orderBy('id', 'desc')
            ->get(['id', 'inv_no', 'inv_date']);

        return response()->json([
            'success' => true,
            'invoices' => $invoices
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $note = CreditNote::with('items')->findOrFail($id);
        $oldData = $note->toArray();
        $oldStatus = $note->status;
        $note->status = $request->status;
        $note->save();
        
        $this->handleStockReversal($note, $oldStatus, $note->status);
        
        addLog('update_status', 'Credit Note Status', 'credit_notes', $id, $oldData, $note->fresh()->toArray());

        return response()->json([
            'success' => true,
            'status' => $note->status
        ]);
    }

    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view_details credit-notes')) {
            return unauthorizedRedirect();
        }

        $creditNote = CreditNote::with([
            'customer.state',
            'customer.city',
            'customer.zone',
            'salesAgent',
            'zone',
            'charges',
            'items.salesInvoiceItem.item',
            'items.salesInvoiceItem.stockEntryItem',
            'items.salesInvoiceItem.brandCategory',
            'items.salesInvoiceItem.color',
            'items.salesInvoiceItem.uom',
            'items.salesInvoiceItem.sizeRatio',
        ])->findOrFail($id);

        $salesInvoices = [];
        if (!empty($creditNote->sales_invoice_ids) && is_array($creditNote->sales_invoice_ids)) {
            $salesInvoices = SalesInvoice::whereIn('id', $creditNote->sales_invoice_ids)->get();
        } elseif ($creditNote->sales_invoice_id) {
            $salesInvoices = SalesInvoice::where('id', $creditNote->sales_invoice_id)->get();
        }

        return view('credit_notes.view_details', compact('creditNote', 'salesInvoices'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete credit-notes')) {
            return unauthorizedRedirect();
        }
        $note = CreditNote::findOrFail($id);
        $oldData = $note->toArray();
        
        CreditNoteItem::where('credit_note_id', $id)->delete();
        \App\Models\CreditNoteCharge::where('credit_note_id', $id)->delete();
        
        $note->delete();
        addLog('delete', 'Credit Note', 'credit_notes', $id, $oldData, null);
        return redirect(url('credit_notes'))->with('success', 'Credit Note deleted successfully');
    }

    public function print($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view credit-notes')) {
            return unauthorizedRedirect();
        }

        $creditNote = CreditNote::with([
            'customer.state',
            'customer.city',
            'customer.zone',
            'salesAgent',
            'zone',
            'charges',
            'items.salesInvoiceItem.item',
            'items.salesInvoiceItem.color',
            'items.salesInvoiceItem.uom',
            'items.salesInvoiceItem.sizeRatio',
            'items.salesInvoiceItem.stockEntryItem',
            'items.salesInvoiceItem.brandCategory',
        ])->findOrFail($id);
        
        $salesInvoices = [];
        if (!empty($creditNote->sales_invoice_ids) && is_array($creditNote->sales_invoice_ids)) {
            $salesInvoices = SalesInvoice::whereIn('id', $creditNote->sales_invoice_ids)->get();
        } elseif ($creditNote->sales_invoice_id) {
            $salesInvoices = SalesInvoice::where('id', $creditNote->sales_invoice_id)->get();
        }

        $setting = Setting::with(['state', 'city'])->first();
        $totalInWords = numberToWords($creditNote->grand_total);
        $is_print = true;

        return view('credit_notes.credit_note_pdf', compact('creditNote', 'setting', 'totalInWords', 'is_print', 'salesInvoices'));
    }

    public function download($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view credit-notes')) {
            return unauthorizedRedirect();
        }

        $creditNote = CreditNote::with([
            'customer.state',
            'customer.city',
            'customer.zone',
            'salesAgent',
            'zone',
            'charges',
            'items.salesInvoiceItem.item',
            'items.salesInvoiceItem.color',
            'items.salesInvoiceItem.uom',
            'items.salesInvoiceItem.sizeRatio',
            'items.salesInvoiceItem.stockEntryItem',
            'items.salesInvoiceItem.brandCategory',
        ])->findOrFail($id);
        
        $salesInvoices = [];
        if (!empty($creditNote->sales_invoice_ids) && is_array($creditNote->sales_invoice_ids)) {
            $salesInvoices = SalesInvoice::whereIn('id', $creditNote->sales_invoice_ids)->get();
        } elseif ($creditNote->sales_invoice_id) {
            $salesInvoices = SalesInvoice::where('id', $creditNote->sales_invoice_id)->get();
        }

        $setting = Setting::with(['state', 'city'])->first();
        $totalInWords = numberToWords($creditNote->grand_total);

        $data = [
            'creditNote' => $creditNote,
            'setting' => $setting,
            'totalInWords' => $totalInWords,
            'salesInvoices' => $salesInvoices
        ];
        $pdf = Pdf::loadView('credit_notes.credit_note_pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('CreditNote_' . $creditNote->note_no . '.pdf');
    }

    public function generateEInvoice(Request $request, $id, \App\Services\EInvoiceService $eInvoiceService)
    {
        $creditNote = CreditNote::findOrFail($id);

        if (!empty($creditNote->sales_invoice_ids) && count($creditNote->sales_invoice_ids) > 1) {
            return response()->json([
                'success' => false,
                'message' => 'E-Invoice generation is allowed only for Credit Notes linked to a single Sales Invoice. Multiple Sales Invoices are not supported.'
            ], 400);
        }

        if ($creditNote->einvoice_status === 'generated') {
            return response()->json([
                'success' => false,
                'message' => 'E-Invoice is already generated for this Credit Note.'
            ], 400);
        }

        $result = $eInvoiceService->generateCreditNoteEInvoice($creditNote);

        if ($result['success']) {
            if (function_exists('addLog')) {
                $creditNote->refresh();
                addLog('generate_einvoice', 'Credit Note E-Invoice Generated', 'credit_notes', $creditNote->id, null, $creditNote->toArray());
            }
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => $result['data'] ?? [],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 400);
    }

    public function cancelEInvoice(Request $request, $id, \App\Services\EInvoiceService $eInvoiceService)
    {
        $creditNote = CreditNote::findOrFail($id);
        $oldData = $creditNote->toArray();
        
        $cancelReason = $request->input('cancel_reason', '2');
        $cancelRemarks = $request->input('cancel_remarks', 'Data Entry Mistake');

        $result = $eInvoiceService->cancelCreditNoteEInvoice($creditNote, $cancelReason, $cancelRemarks);

        if ($result['success']) {
            if (function_exists('addLog')) {
                $creditNote->refresh();
                addLog('cancel_einvoice', 'Credit Note E-Invoice Cancelled', 'credit_notes', $creditNote->id, $oldData, $creditNote->toArray());
            }
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => $result['data'] ?? [],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 400);
    }

    private function handleStockReversal($creditNote, $oldStatus, $newStatus)
    {
        if ($oldStatus === $newStatus) {
            return;
        }

        if ($newStatus === 'Approved' && !$creditNote->is_stock_updated) {
            foreach ($creditNote->items as $item) {
                if ($item->add_to_inventory) {
                    $salesInvoiceItem = \App\Models\SalesInvoiceItem::find($item->sales_invoice_item_id);
                    if ($salesInvoiceItem && $salesInvoiceItem->stock_entry_item_id) {
                        $oldQtyOut = \App\Models\StockEntryItem::where('id', $salesInvoiceItem->stock_entry_item_id)->value('qty_out');
                        \App\Models\StockEntryItem::where('id', $salesInvoiceItem->stock_entry_item_id)->decrement('qty_out', $item->quantity);
                        $newQtyOut = \App\Models\StockEntryItem::where('id', $salesInvoiceItem->stock_entry_item_id)->value('qty_out');
                    }
                }
            }
            $creditNote->is_stock_updated = 1;
            $creditNote->save();
        } elseif ($oldStatus === 'Approved' && $newStatus !== 'Approved' && $creditNote->is_stock_updated) {
            foreach ($creditNote->items as $item) {
                if ($item->add_to_inventory) {
                    $salesInvoiceItem = \App\Models\SalesInvoiceItem::find($item->sales_invoice_item_id);
                    if ($salesInvoiceItem && $salesInvoiceItem->stock_entry_item_id) {
                        $oldQtyOut = \App\Models\StockEntryItem::where('id', $salesInvoiceItem->stock_entry_item_id)->value('qty_out');
                        \App\Models\StockEntryItem::where('id', $salesInvoiceItem->stock_entry_item_id)->increment('qty_out', $item->quantity);
                        $newQtyOut = \App\Models\StockEntryItem::where('id', $salesInvoiceItem->stock_entry_item_id)->value('qty_out');
                        \Log::info("CreditNote {$creditNote->id} Un-Approved - Stock incremented for StockEntryItem ID: {$salesInvoiceItem->stock_entry_item_id}. Old qty_out: {$oldQtyOut}, Increment: {$item->quantity}, New qty_out: {$newQtyOut}");
                    }
                }
            }
            $creditNote->is_stock_updated = 0;
            $creditNote->save();
        }
    }
}

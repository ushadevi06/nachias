<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\SalesOrder;
use App\Models\Customer;
use App\Models\BrandCategory;
use App\Models\Item;
use App\Models\Uom;
use App\Models\Color;
use App\Models\Setting;
use App\Models\StockEntry;
use App\Models\StockEntryItem;
use App\Models\StoreType;
use App\Models\SalesAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Support\Facades\Log;
use App\Models\Brand;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesInvoiceReportExport;
use App\Exports\SalesInvoiceItemsExport;

class SalesInvoiceController extends Controller
{
    public static function getActiveItemPrice($finishedItemCode, $artNo, $sizeName = null)
    {
        if (!$finishedItemCode || !$artNo) {
            return null;
        }

        $baseCode = preg_replace('/-(FS|HS)$/i', '', $finishedItemCode);
        $candidateCodes = [
            $finishedItemCode,
            $baseCode . '-FS',
            $baseCode . '-HS',
            $baseCode
        ];
        $candidateCodes = array_values(array_unique(array_filter($candidateCodes)));

        $cleanedArtNo = str_replace(' ', '', $artNo);

        $query = \DB::table('item_prices')
            ->whereIn('finished_item_code', $candidateCodes)
            ->whereRaw("REPLACE(art_no, ' ', '') = ?", [$cleanedArtNo])
            ->where('status', 'Active')
            ->whereNull('deleted_at')
            ->whereDate('effective_from', '<=', now());

        if ($sizeName) {
            $pricesList = (clone $query)
                ->where(function($q) use ($sizeName) {
                    $q->where('size', $sizeName)
                      ->orWhereNull('size');
                })
                ->get();
        } else {
            $pricesList = $query->get();
        }

        if ($pricesList->isEmpty()) {
            return null;
        }

        $sorted = $pricesList->sort(function($a, $b) use ($finishedItemCode, $candidateCodes, $sizeName) {
            if ($sizeName) {
                $aSizeMatch = ($a->size === $sizeName) ? 1 : 0;
                $bSizeMatch = ($b->size === $sizeName) ? 1 : 0;
                if ($aSizeMatch !== $bSizeMatch) {
                    return $bSizeMatch - $aSizeMatch;
                }
            }

            $aIndex = array_search($a->finished_item_code, $candidateCodes);
            $bIndex = array_search($b->finished_item_code, $candidateCodes);
            if ($aIndex !== $bIndex) {
                return $aIndex - $bIndex;
            }

            $aEff = strtotime($a->effective_from);
            $bEff = strtotime($b->effective_from);
            if ($aEff !== $bEff) {
                return $bEff - $aEff;
            }

            return $b->id - $a->id;
        });

        return $sorted->first();
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = SalesInvoice::with(['customer', 'salesOrder'])->orderBy('id', 'desc');

            if ($request->customer_id) {
                $query->where('customer_id', $request->customer_id);
            }

            if ($request->status) {
                $query->where('invoice_status', $request->status);
            }

            if ($request->inv_date_range) {
                $dates = explode(' to ', $request->inv_date_range);
                if (count($dates) == 2) {
                    $startDate = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                    $query->whereBetween('inv_date', [$startDate, $endDate]);
                } elseif (count($dates) == 1) {
                    $startDate = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $query->whereDate('inv_date', $startDate);
                }
            }

            $invoices = $query->get();
            $data = [];
            $count = 1;

            foreach ($invoices as $inv) {
                $statusOptions = '';
                $allStatuses = ['Draft', 'Unpaid/Credit', 'Partially Paid', 'Paid'];
                $currentStatus = $inv->invoice_status;

                foreach ($allStatuses as $status) {
                    $selected = ($currentStatus === $status) ? 'selected' : '';
                    $disabled = '';
                    if ($currentStatus === 'Draft') {
                        $disabled = '';
                    } elseif ($currentStatus === 'Unpaid/Credit' && $status === 'Draft') {
                        $disabled = 'disabled';
                    } elseif ($currentStatus === 'Partially Paid' && $status !== 'Paid') {
                        $disabled = 'disabled';
                    } elseif ($currentStatus === 'Paid' && $status !== 'Paid') {
                        $disabled = 'disabled';
                    }
                    $statusOptions .= "<option value=\"{$status}\" {$selected} {$disabled}>{$status}</option>";
                }

                $statusDropdown = '
                <div class="form-floating form-floating-outline">
                    <select class="form-select inv-status-change" data-id="' . $inv->id . '">
                        ' . $statusOptions . '
                    </select>
                </div>
                <div class="status_msg_' . $inv->id . ' mt-1" style="font-size:10px;"></div>';

                $eInvoiceBtn = '';
                if ($inv->einvoice_status === 'cancelled') {
                    $eInvoiceBtn = '<button type="button" class="btn btn-warning" title="E-Invoice Cancelled" style="padding: 0.25rem 0.5rem; font-size: 0.875rem; border-radius: 4px; margin-left: 5px;" disabled><i class="ri ri-close-circle-line"></i> Cancelled</button>';
                    
                    $recreatedInvoice = \App\Models\SalesInvoice::where('customer_id', $inv->customer_id)
                        ->where('so_ids', $inv->so_ids)
                        ->where('id', '!=', $inv->id)
                        ->where(function($q) {
                            $q->whereNull('einvoice_status')->orWhere('einvoice_status', '!=', 'cancelled');
                        })
                        ->exists();
                    
                    if (!$recreatedInvoice) {
                        $eInvoiceBtn .= '<a href="' . url('sales_invoices/recreate/' . $inv->id) . '" class="btn btn-outline-primary" title="Recreate / Copy to New Invoice" style="padding: 0.25rem 0.5rem; font-size: 0.875rem; border-radius: 4px; margin-left: 5px;"><i class="ri ri-file-copy-line"></i></a>';
                    }
                } elseif ($inv->irn) {
                    $ackDateTime = $inv->ack_date ? \Carbon\Carbon::parse($inv->ack_date) : null;
                    $isExpired = $ackDateTime ? $ackDateTime->diffInHours(now()) >= 24 : false;
                    if ($isExpired) {
                        $eInvoiceBtn = '<button type="button" class="btn btn-secondary einvoice-expired-btn" data-id="' . $inv->id . '" title="E-Invoice Cancellation Window Expired" style="padding: 0.25rem 0.5rem; font-size: 0.875rem; border-radius: 4px; margin-left: 5px;"><i class="ri ri-close-circle-line"></i></button>';
                    } else {
                        $eInvoiceBtn = '<button type="button" class="btn btn-danger einvoice-cancel-btn" data-id="' . $inv->id . '" title="Cancel E-Invoice" style="padding: 0.25rem 0.5rem; font-size: 0.875rem; border-radius: 4px; margin-left: 5px;"><i class="ri ri-close-circle-line"></i></button>';
                    }
                } else {
                    $eInvoiceBtn = '<button type="button" class="btn btn-info einvoice-generate-btn" data-id="' . $inv->id . '" title="Generate E-Invoice" style="padding: 0.25rem 0.5rem; font-size: 0.875rem; border-radius: 4px; margin-left: 5px;"><i class="ri ri-receipt-line"></i></button>';
                }

                $editBtn = '';
                if ($inv->einvoice_status !== 'cancelled') {
                    $editBtn = '<a href="' . url('sales_invoices/add/' . $inv->id) . '" class="btn btn-edit" title="Edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                }

                $action = '<div class="button-box d-flex align-items-center">
                    <a href="' . url('sales_invoices/view/' . $inv->id) . '" class="btn btn-view" title="View"><i class="icon-base ri ri-eye-line"></i></a>
                    ' . $editBtn . '
                    ' . $eInvoiceBtn . '
                </div>';

                $data[] = [
                    'id' => $inv->id,
                    'DT_RowIndex' => $count++,
                    'inv_no' => $inv->inv_no,
                    'inv_date' => $inv->inv_date->format('d-m-Y'),
                    'customer_name' => ($inv->customer ? $inv->customer->name : 'N/A') . ($inv->customer ? ' <span  class="mini-title">(' . $inv->customer->code . ')</span>' : ''),
                    'so_no' => ($inv->salesOrder ? $inv->salesOrder->so_no : 'N/A') . ($inv->salesOrder && $inv->salesOrder->order_no ? '<br><span class="badge bg-label-info mt-1" style="font-size:10px;">' . $inv->salesOrder->order_no . '</span>' : ''),
                    'total_items' => $inv->items->count(),
                    'total_qty' => $inv->items->sum('quantity'),
                    'sub_total' => '₹' . number_format($inv->sub_total, 2),
                    'raw_sub_total' => $inv->sub_total,
                    'discount' => '₹' . number_format($inv->discount ?? 0, 2),
                    'raw_discount' => $inv->discount ?? 0,
                    'taxable_value' => '₹' . number_format($inv->sub_total - ($inv->discount ?? 0), 2),
                    'raw_taxable_value' => $inv->sub_total - ($inv->discount ?? 0),
                    'grand_total' => '₹' . number_format($inv->grand_total, 2),
                    'raw_grand_total' => $inv->grand_total,
                    'status' => $statusDropdown,
                    'status_text' => $inv->invoice_status,
                    'delivery_status' => $inv->delivery_status,
                    'action' => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        $customers = Customer::all();
        return view('sales_invoice.view', compact('customers'));
    }

    public function getNextInvoiceNo(Request $request)
    {
        $brandId = $request->brand_id;
        $invDateStr = $request->inv_date;
        
        if (!$brandId || !$invDateStr) {
            return response()->json(['success' => false, 'message' => 'Brand ID and Invoice Date are required.']);
        }

        try {
            $date = Carbon::createFromFormat('d-m-Y', $invDateStr);
        } catch (\Exception $e) {
            try {
                $date = Carbon::createFromFormat('Y-m-d', $invDateStr);
            } catch (\Exception $ex) {
                return response()->json(['success' => false, 'message' => 'Invalid date format.']);
            }
        }

        $generatedInvNo = $this->generateInvoiceNumber($brandId, $date);

        return response()->json([
            'success' => true,
            'inv_no' => $generatedInvNo
        ]);
    }

    public function add(Request $request, $id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit sales-invoice')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create sales-invoice')) {
                return unauthorizedRedirect();
            }
        }

        if ($id) {
            $existingInvoice = SalesInvoice::findOrFail($id);
            if ($existingInvoice->einvoice_status === 'cancelled' || $existingInvoice->invoice_status === 'Cancelled') {
                return redirect()->route('sales_invoices.index')->with('error', 'Cannot edit a cancelled invoice.');
            }
        }

        if ($request->isMethod('post')) {
            $selectedBrandId = $request->brand_id;
            if ($selectedBrandId && $request->inv_date) {
                try {
                    $selectedDate = null;
                    try {
                        $selectedDate = Carbon::createFromFormat('d-m-Y', $request->inv_date);
                    } catch (\Exception $e) {
                        $selectedDate = Carbon::createFromFormat('Y-m-d', $request->inv_date);
                    }

                    $regenerate = false;
                    if ($request->is_manual_inv_no == '1') {
                        $regenerate = false;
                    } else if (!$id) {
                        $regenerate = true;
                    } else {
                        $invoice = SalesInvoice::findOrFail($id);
                        $origDate = Carbon::parse($invoice->inv_date);
                        $origYear = (int)$origDate->format('Y');
                        $origMonth = (int)$origDate->format('m');
                        $origFYStart = ($origMonth >= 4) ? $origYear : ($origYear - 1);
                        
                        $selYear = (int)$selectedDate->format('Y');
                        $selMonth = (int)$selectedDate->format('m');
                        $selFYStart = ($selMonth >= 4) ? $selYear : ($selYear - 1);

                        if ($invoice->brand_id != $selectedBrandId || $origFYStart != $selFYStart) {
                            $regenerate = true;
                        } else {
                            $request->merge(['inv_no' => $invoice->inv_no]);
                        }
                    }

                    if ($regenerate) {
                        $generatedInvNo = $this->generateInvoiceNumber($selectedBrandId, $selectedDate, $id);
                        $request->merge(['inv_no' => $generatedInvNo]);
                    }
                } catch (\Exception $e) {
                }
            }

            $request->validate([
                'brand_id' => 'required|exists:brands,id',
                'inv_no' => ['required', 'string', 'min:1', 'max:16', 'regex:/^[a-zA-Z1-9][a-zA-Z0-9\/\-]*$/', 'unique:sales_invoices,inv_no,' . ($id ?? 'NULL') . ',id,deleted_at,NULL'],
                'inv_date' => 'required|date_format:d-m-Y',
                'so_ids' => 'required|array',
                'so_ids.*' => 'exists:sales_orders,id',
                'customer_id' => 'required|exists:customers,id',
                'delivery_address' => 'required|min:3|max:255|regex:/^[^<>]*$/',
                'remarks' => 'nullable|min:3|max:255|regex:/^[^<>]*$/',
                'invoice_status' => 'required|in:Draft,Unpaid/Credit,Paid,Partially Paid',
                'payment_mode' => 'nullable',
                'items' => 'required|array|min:1',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.rate' => 'required|numeric|min:0.01',
                'items.*.mrp' => 'required|numeric|min:0',
                'no_of_box' => 'nullable|integer|min:1',
                'hsn_sac' => 'required|string|max:50',
                'sales_discount' => 'nullable|numeric|min:0|max:100',
                'discount_percent' => 'nullable|numeric|min:0|max:100',
                'box_discount_amount' => 'nullable|numeric|min:0',
                'signature_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'attachment_file' => 'nullable|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
                'tran_doc_date' => 'nullable|date_format:d-m-Y',
                'due_date' => 'nullable|date_format:d-m-Y',
                'transporter_id' => ['nullable', 'string', 'max:100', 'not_regex:/^0+$/'],
                'vehicle_no' => ['nullable', 'string', 'max:100', 'not_regex:/^0+$/'],
                'tran_doc_no' => ['nullable', 'string', 'max:100', 'not_regex:/^0+$/'],
                'lr_no' => ['nullable', 'string', 'max:100', 'not_regex:/^0+$/'],
                'tran_doc_date' => 'nullable|date_format:d-m-Y',
            ], [
                '*.required'      => 'This field is required.',
                '*.unique'        => 'This field already exists.',
                '*.exists'        => 'Selected value is invalid.',
                '*.date_format'   => 'Please enter a valid date in DD-MM-YYYY format.',
                '*.numeric'       => 'This field must be a number.',
                '*.regex'         => 'This field is an invalid format.',
                '*.not_regex'     => 'This field is an invalid format.',
                'sales_discount.max'   => 'Sales discount percentage cannot exceed 100%.',
                'sales_discount.min'   => 'Sales discount percentage cannot be negative.',
                'discount_percent.max' => 'Discount percentage cannot exceed 100%.',
                'discount_percent.min' => 'Discount percentage cannot be negative.',
                'box_discount_amount.min' => 'Box discount cannot be negative.',
                'items.*.rate.min' => 'Price must be greater than 0.00.',
                '*.min'           => 'This field must be at least :min characters.',
                '*.max'           => 'This field must be at most :max characters.',
                'items.*.quantity.required' => 'This field is required.',
                'items.*.rate.required' => 'Price is required.',
                'items.*.quantity.min' => 'Please enter a valid numeric value greater than or equal to 0.01.',
                'items.*.rate.min' => 'Please enter a valid numeric value greater than or equal to 0.',
                'items.*.mrp.min' => 'Please enter a valid numeric value greater than or equal to 0.',
                'extra_input' => 'nullable|min:3|max:100',
            ]);

            try {
                $this->validateStockAvailability($request->items, $id);
            } catch (\Exception $e) {
                return back()->withInput()->withErrors(['error' => $e->getMessage()]);
            }

            DB::beginTransaction();
            try {
                $invoiceData = $request->only([
                    'inv_no', 'brand_id', 'customer_id', 'store_id', 'agent_id', 'delivery_address', 'remarks',
                    'invoice_status', 'payment_mode', 'extra_input', 'due_date',
                    'notes', 'transporter_name', 'transporter_id', 'transport_mode',
                    'vehicle_no', 'veh_type', 'transport_distance', 'tran_doc_no', 'tran_doc_date',
                    'lr_no', 'no_of_box', 'hsn_sac', 'sub_total', 'sales_discount', 'box_discount_amount', 'discount_percent', 'discount', 
                    'commission_percent', 'commission_amount', 'total', 'other_state',
                    'tax_amount', 'igst_percent', 'igst', 'cgst_percent', 'cgst', 'sgst_percent', 'sgst',
                    'other_charges','round_off_type', 'round_off',
                    'grand_total', 'due_amount'
                ]);
                if ($request->tran_doc_date) {
                    $invoiceData['tran_doc_date'] = Carbon::createFromFormat('d-m-Y', $request->tran_doc_date)->format('Y-m-d');
                }
                $invoiceData['lr_no'] = $request->tran_doc_no ?? $request->lr_no;

                $invoiceData['inv_date'] = Carbon::createFromFormat('d-m-Y', $request->inv_date)->format('Y-m-d');
                if ($request->due_date) {
                    $invoiceData['due_date'] = Carbon::createFromFormat('d-m-Y', $request->due_date)->format('Y-m-d');
                }
                $invoiceData['show_fields'] = $request->show_fields ?? [];
                $invoiceData['delivery_show_fields'] = $request->delivery_show_fields ?? [];
                $invoiceData['other_state'] = $request->other_state == 'yes';
                $invoiceData['so_ids'] = json_encode($request->so_ids);
                $invoiceData['so_id'] = $request->so_ids[0] ?? null;

                if ($request->hasFile('signature_file')) {
                    $dir = public_path('uploads/sales_invoices/signatures');
                    if (!file_exists($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    $file = $request->file('signature_file');
                    $fileName = time() . '_sig_' . $file->getClientOriginalName();
                    $file->move($dir, $fileName);
                    $invoiceData['signature_file'] = 'uploads/sales_invoices/signatures/' . $fileName;
                }

                if ($request->hasFile('attachment_file')) {
                    $dir = public_path('uploads/sales_invoices/attachments');
                    if (!file_exists($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    $file = $request->file('attachment_file');
                    $fileName = time() . '_att_' . $file->getClientOriginalName();
                    $file->move($dir, $fileName);
                    $invoiceData['attachment_file'] = 'uploads/sales_invoices/attachments/' . $fileName;
                }
                //new 
                $calculatedSubTotal = 0;
                $calculatedTotalQty = 0;
                foreach ($request->items as $item) {
                    $qty = (float)($item['quantity'] ?? 0);
                    $mrp = (float)($item['mrp'] ?? 0);
                    $rate = (float)($item['rate'] ?? 0);
                    $price = $rate > 0 ? $rate : $mrp;
                    $calculatedSubTotal += $qty * $price;
                    $calculatedTotalQty += $qty;
                }

                $salesDiscPercent = (float)($request->sales_discount ?? 0);
                if ($salesDiscPercent <= 0 && $request->customer_id) {
                    $customer = \App\Models\Customer::find($request->customer_id);
                    if ($customer && $customer->sales_discount) {
                        $salesDiscPercent = (float)$customer->sales_discount;
                    }
                }
                $boxDiscountPerPc = (float)($request->box_discount_amount ?? 0);
                $salesDiscountValue = ($calculatedSubTotal * $salesDiscPercent) / 100;
                $boxDiscountValue = $calculatedTotalQty * $boxDiscountPerPc;
                $calculatedDiscount = $salesDiscountValue + $boxDiscountValue;

                // $preGstCharges = (float)($request->pre_gst_charges ?? 0);
                // $calculatedTotal = $calculatedSubTotal - $calculatedDiscount + $preGstCharges;
                $calculatedTotal = $calculatedSubTotal - $calculatedDiscount;
                $cgst = 0; $sgst = 0; $igst = 0;
                if ($request->other_state == 'yes') {
                    $igstPercent = (float)($request->igst_percent ?? 0);
                    $igst = ($calculatedTotal * $igstPercent) / 100;
                } else {
                    $cgstPercent = (float)($request->cgst_percent ?? 0);
                    $sgstPercent = (float)($request->sgst_percent ?? 0);
                    $cgst = ($calculatedTotal * $cgstPercent) / 100;
                    $sgst = ($calculatedTotal * $sgstPercent) / 100;
                }
                $calculatedTaxAmount = $cgst + $sgst + $igst;

                $otherCharges = (float)($request->other_charges ?? 0);
                // $postGstCharges = (float)($request->post_gst_charges ?? 0);
                
                // $totalBeforeRoundOff = $calculatedTotal + $calculatedTaxAmount + $otherCharges + $postGstCharges;
                $totalBeforeRoundOff = $calculatedTotal + $calculatedTaxAmount + $otherCharges ;
                $calculatedGrandTotal = round($totalBeforeRoundOff);
                $calculatedRoundOff = abs($calculatedGrandTotal - $totalBeforeRoundOff);
                $calculatedRoundOffType = ($calculatedGrandTotal >= $totalBeforeRoundOff) ? 'Add' : 'Less';

                // Override request data with recalculated values
                $invoiceData['sub_total'] = $calculatedSubTotal;
                $invoiceData['sales_discount'] = $salesDiscPercent;
                $invoiceData['discount_percent'] = $salesDiscPercent;
                $invoiceData['discount'] = $calculatedDiscount;
                $invoiceData['total'] = $calculatedTotal;
                $invoiceData['cgst'] = $cgst;
                $invoiceData['sgst'] = $sgst;
                $invoiceData['igst'] = $igst;
                $invoiceData['tax_amount'] = $calculatedTaxAmount;
                $invoiceData['round_off'] = $calculatedRoundOff;
                $invoiceData['round_off_type'] = $calculatedRoundOffType;
                $invoiceData['grand_total'] = $calculatedGrandTotal;
               
                //new
                $oldQuantities = [];
                if ($id) {
                    $invoice = SalesInvoice::with('items')->findOrFail($id);
                    foreach($invoice->items as $oItem) {
                        $oldQuantities[$oItem->id] = ['quantity' => $oItem->quantity, 'inv_no' => $invoice->inv_no, 'stock_entry_item_id' => $oItem->stock_entry_item_id];
                    }
                    
                    $activeStatuses = ['Paid', 'Partially Paid', 'Unpaid/Credit'];
                    // new
                    $invoiceData['received_amount'] = 0.00;
                    $invoiceData['due_amount'] = $calculatedGrandTotal; //new

                    $invoice->update($invoiceData);             
                          
                    
                    $itemIds = collect($request->items)->pluck('id')->filter()->toArray();
                    $deletedItems = $invoice->items()->whereNotIn('id', $itemIds)->get();
                    foreach ($deletedItems as $dItem) {
                        $this->sequentialStockRevert($dItem, $dItem->quantity);
                    }
                    
                    $invoice->items()->whereNotIn('id', $itemIds)->forceDelete();
                } else {
                    //new
                    $invoiceData['received_amount'] = 0.00;
                    $invoiceData['due_amount'] = $calculatedGrandTotal;//new
                    $invoice = SalesInvoice::create($invoiceData);
                }
                $invoiceId = $invoice->id;
                foreach ($request->items as $item) {
                    $isExtra = !empty($item['is_extra']);

                    $apiColor = $item['api_color'] ?? null;
                    if (empty($apiColor)) {
                        $artNo = $item['art_no'] ?? '';
                        if (!empty($artNo) && strpos($artNo, '-') !== false) {
                            $parts = explode('-', $artNo);
                            $lastPart = trim(end($parts));
                            if (is_numeric($lastPart)) {
                                $apiColor = $lastPart;
                            }
                        }
                    }
                    if (empty($apiColor)) {
                        $apiColor = 'A';
                    }

                    $stockEntryItemId = !empty($item['stock_entry_item_id']) ? $item['stock_entry_item_id'] : null;
                    if (empty($stockEntryItemId) && !empty($item['sku'])) {
                        $seItem = \App\Models\StockEntryItem::where('sku', $item['sku'])->first();
                        if ($seItem) {
                            $stockEntryItemId = $seItem->id;
                        }
                    }

                    if (!$isExtra && !empty($invoiceData['so_ids'])) {
                        $soIdsArr = is_array($invoiceData['so_ids']) ? $invoiceData['so_ids'] : json_decode($invoiceData['so_ids'], true);
                        if (!empty($soIdsArr)) {
                            $existsInSO = \App\Models\SalesOrderItem::whereIn('sale_order_id', $soIdsArr)
                                ->where(function($q) use ($item, $apiColor, $stockEntryItemId) {
                                    if ($stockEntryItemId) {
                                        $q->where('stock_entry_item_id', $stockEntryItemId);
                                    } else {
                                        $q->where('sku', $item['sku'] ?? '')
                                          ->where('size_id', $item['size_id'] ?? $item['size'] ?? '');
                                    }
                                })->exists();
                            
                            if (!$existsInSO) {
                                $isExtra = true;
                            }
                        }
                    }

                    SalesInvoiceItem::updateOrCreate(
                        ['id' => $item['id'] ?? null],
                        [
                            'sales_invoice_id' => $invoiceId,
                            'sku' => $item['sku'] ?? null,
                            'uom_id' => $item['uom_id'] ?? null,
                            'quantity' => $item['quantity'] ?? 0,
                            'rate' => $item['rate'] ?? 0,
                            'mrp' => $item['mrp'] ?? 0,
                            'amount' => $item['amount'] ?? 0,
                            'hsn_sac' => $item['hsn_sac'] ?? null,
                            'art_no' => $item['art_no'] ?? null,
                            'size' => $item['size'] ?? null,
                            'color_id' => $item['color_id'] ?? null,
                            'api_color' => $apiColor,
                            'sleeve_type' => $item['sleeve_type'] ?? null,
                            'stock_entry_item_id' => $stockEntryItemId,
                            'is_extra' => $isExtra,
                        ]
                    );
                }
                
                $this->adjustStock($invoiceId, false, $oldQuantities ?? []);

                $invoice->load(['items', 'customer.city', 'customer.place']);
                $totalPcs = (int) $invoice->items->sum('quantity');
                $boxCount = (int) ($invoice->no_of_box ?: 1);
                
                $customerName = $invoice->customer->name ?? 'N/A';
                $customerAddress = implode(', ', array_filter([$invoice->customer->address_line_1 ?? '', $invoice->customer->address_line_2 ?? '', $invoice->customer->address_line_3 ?? '']));
                $location = $invoice->customer->city->city_name ?? ($invoice->customer->place->place_name ?? 'N/A');
                
                $qrDetails = "Customer: {$customerName}\nAddress: {$customerAddress}\nLocation: {$location}\nInvoice No: {$invoice->inv_no}\nPieces: {$totalPcs}\nBoxes: {$boxCount}";
                $invoice->update(['qr_details' => $qrDetails]);

                $activeStatuses = ['Paid', 'Partially Paid', 'Unpaid/Credit'];
                if (in_array($invoice->invoice_status, $activeStatuses)) {
                    $invoice->load('items');
                }

                DB::commit();
                
                $msg = $id ? 'Sale Invoice updated successfully' : 'Sale Invoice created successfully';
                return redirect('sales_invoices')->with('success', $msg);
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->withErrors(['error' => 'Failed to save invoice: ' . $e->getMessage()]);
            }
        }

        $invoice = $id ? SalesInvoice::with(['items.brandCategory', 'items.item.brand', 'items.item.style', 'items.color', 'items.uom', 'items.sizeRatio', 'salesOrder'])->findOrFail($id) : null;
        if ($invoice && $invoice->einvoice_status === 'cancelled') {
            return redirect('sales_invoices')->with('error', 'Cancelled invoices cannot be edited.');
        }
        $customers = Customer::active()->orderBy('id', 'desc')->get();
        $brandCategories = BrandCategory::active()->get();
        $uoms = Uom::active()->get();
        $stores = StoreType::where('status', 'Active')->orderBy('id', 'desc')->get();
        $sales_agent = SalesAgent::where('status', 'Active')->orderBy('id', 'desc')->get();

        $brands = \App\Models\Brand::active()->orderBy('id', 'desc')->get();
        $transportModes = \App\Models\TransportMode::where('status', 'Active')->orderBy('id', 'desc')->get();

        if ($invoice) {
            $soIds = $invoice->so_ids ? json_decode($invoice->so_ids, true) : ($invoice->so_id ? [$invoice->so_id] : []);
            $saleOrders = SalesOrder::whereIn('id', $soIds)->orderBy('id', 'desc')->get();
        } else {
            $saleOrders = collect(); 
        }

        return view('sales_invoice.add', compact('invoice', 'customers', 'saleOrders', 'brandCategories', 'uoms', 'stores', 'sales_agent', 'brands', 'transportModes'));
    }

    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view_details sales-invoice')) {
            return unauthorizedRedirect();
        }
        $invoice = SalesInvoice::with(['customer', 'salesOrder', 'items.brandCategory', 'items.item', 'items.color', 'items.uom'])->findOrFail($id);
        return view('sales_invoice.view_details', compact('invoice'));
    }
    
    private function getCustomerPendingItems($customerId, $currentInvoiceId = null)
    {
        $allCustomerSOs = SalesOrder::with(['items'])->where('customer_id', $customerId)->whereIn('status', ['Approved', 'Dispatched'])->orderBy('id', 'asc')->get();
        
        $soItemOrdered = [];
        $soItemInvoiced = [];

        foreach($allCustomerSOs as $so) {
            foreach($so->items as $item) {
                $itemId = $item->stock_entry_item_id ?? '';
                if (!isset($soItemOrdered[$so->id][$itemId])) {
                    $soItemOrdered[$so->id][$itemId] = 0;
                }
                $soItemOrdered[$so->id][$itemId] += $item->qty;
            }
        }

        $invoices = DB::table('sales_invoices')
            ->where('customer_id', $customerId)
            ->where(function($q) {
                $q->whereNull('einvoice_status')
                  ->orWhere('einvoice_status', '!=', 'cancelled');
            })
            ->when($currentInvoiceId, function($q) use ($currentInvoiceId) {
                $q->where('id', '!=', $currentInvoiceId);
            })
            ->get(['id', 'so_ids', 'so_id']);

        foreach($invoices as $inv) {
            $so_ids = json_decode($inv->so_ids, true);
            if (!$so_ids && $inv->so_id) {
                $so_ids = [(string)$inv->so_id];
            }
            if (!$so_ids) continue;

            $invItems = DB::table('sales_invoice_items')->where('sales_invoice_id', $inv->id)->get();

            foreach($invItems as $invItem) {
                $itemId = $invItem->stock_entry_item_id ?? '';
                $qtyToAllocate = $invItem->quantity;

                foreach($so_ids as $so_id) {
                    if ($qtyToAllocate <= 0) break;

                    $ordered = $soItemOrdered[$so_id][$itemId] ?? 0;
                    $alreadyInvoiced = $soItemInvoiced[$so_id][$itemId] ?? 0;
                    $pending = $ordered - $alreadyInvoiced;

                    if ($pending > 0) {
                        $allocate = min($pending, $qtyToAllocate);
                        $soItemInvoiced[$so_id][$itemId] = $alreadyInvoiced + $allocate;
                        $qtyToAllocate -= $allocate;
                    }
                }
                
                if ($qtyToAllocate > 0 && count($so_ids) > 0) {
                    $first_so = $so_ids[0];
                    $soItemInvoiced[$first_so][$itemId] = ($soItemInvoiced[$first_so][$itemId] ?? 0) + $qtyToAllocate;
                }
            }
        }

        return $soItemInvoiced;
    }

    public function getCustomerSalesOrders(Request $request)
    {
        $customerId = $request->customer_id;
        $currentInvoiceId = $request->invoice_id ?? null;
        if (!$customerId) {
            return response()->json(['success' => false, 'message' => 'Customer ID required']);
        }

        $saleOrders = SalesOrder::with(['items'])->where('customer_id', $customerId)->whereIn('status', ['Approved', 'Dispatched'])->orderBy('id', 'desc')->get();
        $soItemInvoiced = $this->getCustomerPendingItems($customerId, $currentInvoiceId);

        $data = $saleOrders->map(function($so) use ($soItemInvoiced) {
            $totalQty = 0;
            $invoicedQty = 0;

            foreach ($so->items as $item) {
                $totalQty += $item->qty;
            }
            
            if (isset($soItemInvoiced[$so->id])) {
                foreach ($soItemInvoiced[$so->id] as $qty) {
                    $invoicedQty += $qty;
                }
            }

            $pendingQty = max(0, $totalQty - $invoicedQty);
            if ($pendingQty <= 0) {
                return null;
            }

            return [
                'id'          => $so->id,
                'so_no'       => $so->so_no,
                'order_no'    => $so->order_no,
                'so_date'     => $so->so_date ? $so->so_date->format('d-m-Y') : '',
                'total_qty'   => $totalQty,
                'pending_qty' => $pendingQty,
            ];
        })->filter(fn($so) => $so !== null)->values();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getMultipleSaleOrdersDetails(Request $request)
    {
        $soIds = $request->so_ids;
        if (empty($soIds) || !is_array($soIds)) {
            return response()->json(['success' => false, 'message' => 'No Sales Orders selected']);
        }

        $saleOrders = SalesOrder::with([
            'items.brandCategory', 
            'items.item.brand', 
            'items.item.style', 
            'items.stockEntryItem.item.brand', 
            'items.stockEntryItem.item.style', 
            'items.uom', 
            'items.size', 
            'items.color', 
            'customer',
            'charges'
        ])->whereIn('id', $soIds)->get();

        if ($saleOrders->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Sale Orders not found']);
        }

        $customerId = $saleOrders->first()->customer_id;
        $currentInvoiceId = $request->invoice_id ?? null;
        $soItemInvoiced = $this->getCustomerPendingItems($customerId, $currentInvoiceId);

        $allSkus = [];
        foreach ($saleOrders as $so) {
            foreach ($so->items as $item) {
                if ($item->sku) {
                    $allSkus[] = $item->sku;
                }
            }
        }
        $allSkus = array_unique(array_filter($allSkus));

        $prefetchedStockEntryItems = collect();
        if (!empty($allSkus)) {
            $prefetchedStockEntryItems = \App\Models\StockEntryItem::where(function ($q) use ($allSkus) {
                    $q->whereIn('sku', $allSkus)
                      ->orWhereIn('barcode', $allSkus);
                })
                ->where('stock_type', 'finished_goods')
                ->whereNull('deleted_at')
                ->get();
        }

        $allItems = collect();
        $billingAddress = '';
        $shippingAddress = '';
        $transporterName = '';
        $transportModeId = null;

        $transportModes = [];
        foreach ($saleOrders as $so) {
            if (!empty($so->transport_mode_id)) {
                $transportModes[] = $so->transport_mode_id;
            }
            if (empty($billingAddress) && $so->customer) {
                $c = $so->customer;
                $billingAddress = implode(', ', array_filter([$c->address_line_1, $c->address_line_2, $c->address_line_3, $c->city, $c->state, $c->pincode]));
                $shippingAddress = $so->shipping_address;
                $transporterName = $so->transporter_name;
            }

            foreach ($so->items as $item) {
                $invoicedQty = $soItemInvoiced[$so->id][$item->stock_entry_item_id] ?? 0;
                $pendingQty = max(0, $item->qty - $invoicedQty);
                
                if ($pendingQty > 0) {
                    $brandName = '';
                    $itemName = '';
                    $sleeveType = is_array($item->sleeve) ? ($item->sleeve[0] ?? '') : $item->sleeve;

                    // Resolve Stock Entry Item dynamically if not set
                    $stockEntryItem = $item->stockEntryItem;
                    if (!$stockEntryItem && $item->sku) {
                        $stockEntryItem = $prefetchedStockEntryItems->first(function ($se) use ($item) {
                            return $se->sku === $item->sku || $se->barcode === $item->sku;
                        });
                    }

                    if ($item->item) {
                        if ($item->item->brand) $brandName = $item->item->brand->brand_name;
                        elseif ($item->brandCategory) $brandName = $item->brandCategory->name;
                        
                        if ($item->item->style) $itemName = $item->item->style->style_name;
                        else $itemName = $item->item->name;
                    } elseif ($stockEntryItem) {
                        if ($stockEntryItem->item) {
                            $seItem = $stockEntryItem->item;
                            if ($seItem->brand) $brandName = $seItem->brand->brand_name;
                            elseif ($seItem->brandCategory) $brandName = $seItem->brandCategory->name;

                            if ($seItem->style) $itemName = $seItem->style->style_name;
                            else $itemName = $seItem->name;
                        } else {
                            $brandName = $stockEntryItem->finished_item_code;
                        }
                        if (empty($sleeveType)) $sleeveType = $stockEntryItem->sleeve_type;
                    } elseif ($item->brandCategory) {
                        $brandName = $item->brandCategory->name;
                    } 

                    $stockQty = 0;
                    if ($item->sku) {
                        $stockQtyQuery = DB::table('stock_entry_items')
                            ->where(function($q) use($item) {
                                $q->where('sku', $item->sku)
                                  ->orWhere('barcode', $item->sku);
                            })
                            ->where('stock_type', 'finished_goods')
                            ->whereNull('deleted_at');
                        if ($item->size_id) {
                            $stockQtyQuery->where('size', $item->size_id);
                        }
                        if ($item->item && $item->item->code) {
                            $stockQtyQuery->where('finished_item_code', $item->item->code);
                        } elseif ($stockEntryItem && $stockEntryItem->finished_item_code) {
                            $stockQtyQuery->where('finished_item_code', $stockEntryItem->finished_item_code);
                        }
                        $stockQty = $stockQtyQuery->sum(DB::raw('qty_in - COALESCE(qty_out, 0)')) ?? 0;
                    } elseif ($item->stock_entry_item_id) {
                        $stockQty = DB::table('stock_entry_items')->where('id', $item->stock_entry_item_id)->whereNull('deleted_at')->value(DB::raw('qty_in - COALESCE(qty_out, 0)')) ?? 0;
                    } elseif ($stockEntryItem) {
                        $stockQty = DB::table('stock_entry_items')->where('id', $stockEntryItem->id)->whereNull('deleted_at')->value(DB::raw('qty_in - COALESCE(qty_out, 0)')) ?? 0;
                    }

                    if (empty($brandName)) {
                        $brandName = $item->item_name;
                        $itemName = '';
                    }
                   
                    $finishedItemCode = '';
                    if ($item->item && $item->item->code) {
                        $finishedItemCode = $item->item->code;
                    } elseif ($stockEntryItem && $stockEntryItem->finished_item_code) {
                        $finishedItemCode = $stockEntryItem->finished_item_code;
                    }
                    $finalMrp = $item->mrp ?? 0;
                    $finalRate = $item->rate ?? 0;

                    $artNo = $item->art_no;
                    if ($stockEntryItem && !empty($stockEntryItem->art_no)) {
                        $artNo = $stockEntryItem->art_no;
                    }

                    if ($finishedItemCode && $artNo) {
                        $sizeName = $item->size ? $item->size->size : ($item->size_id ?: null);
                        $itemPrice = self::getActiveItemPrice($finishedItemCode, $artNo, $sizeName);
                        if ($itemPrice) {
                            $finalMrp = $itemPrice->selling_price;
                            $finalRate = $itemPrice->unit_price;
                        }
                    }

                    $itemData = [
                        'brand_id' => $item->brand_cat_id,
                        'brand_name' => $brandName ?: '',
                        'item_id' => $item->item_id,
                        'item_name' => $itemName ?: ($item->item_name ?? ''),
                        'item_code' => $item->item ? $item->item->code : ($stockEntryItem ? $stockEntryItem->finished_item_code : ''),
                        'uom_id' => $item->uom_id,
                        'uom_code' => $item->uom_id ?: '',
                        'qty' => $pendingQty, 
                        'stock_qty' => (float)$stockQty,
                        'rate' => $finalRate,
                        'mrp' => $finalMrp,
                        'amount' => (float)$finalRate * $pendingQty,
                        'art_no' => $artNo,
                        'hsn_sac' => $item->hsn_sac ?? null,
                        'sku' => $item->sku,
                        'size_id' => $item->size_id,
                        'size_name' => $item->size ? $item->size->size : '',
                        'color_id' => $item->color_id,
                        'color_name' => $item->color ? $item->color->color_name : '',
                        'api_color' => $item->api_color,
                        'sleeve' => $sleeveType ?: '',
                        'stock_entry_item_id' => $item->stock_entry_item_id ?: ($stockEntryItem ? $stockEntryItem->id : null),
                    ];

                    $existingItemKey = $allItems->search(function($i) use ($itemData) {
                        if (!empty($itemData['stock_entry_item_id']) && !empty($i['stock_entry_item_id'])) {
                            return $i['stock_entry_item_id'] == $itemData['stock_entry_item_id'] && $i['rate'] == $itemData['rate'];
                        } else {
                            return $i['sku'] == $itemData['sku'] && 
                                   $i['rate'] == $itemData['rate'] && 
                                   $i['size_id'] == $itemData['size_id'] && 
                                   $i['color_id'] == $itemData['color_id'] && 
                                   $i['art_no'] == $itemData['art_no'] && 
                                   $i['sleeve'] == $itemData['sleeve'] &&
                                   $i['item_code'] == $itemData['item_code'];
                        }
                    });
                    
                    if ($existingItemKey !== false) {
                        $existingItem = $allItems[$existingItemKey];
                        $existingItem['qty'] += $pendingQty;
                        $allItems[$existingItemKey] = $existingItem;
                    } else {
                        $allItems->push($itemData);
                    }
                }
            }
        }
        
        $transportModes = array_unique($transportModes);
        $transportModeId = (count($transportModes) === 1) ? reset($transportModes) : null;

        $firstSo = $saleOrders->first();

        $totalSubTotal = 0;
        $totalDiscountAmount = 0;
        // $totalPreGstCharges = 0;
        $totalCourierCharges = 0;
        // $totalOtherPostGstCharges = 0;
        foreach ($saleOrders as $so) {
            $totalSubTotal += $so->sub_total ?? 0;
            $totalDiscountAmount += $so->discount_amount ?? 0;
            
            // if ($so->charges) {
            //     // $totalPreGstCharges += $so->charges->where('tax_type', 'Pre-GST')->sum('charge_amount');
            //     
            //     // $totalCourierCharges += $so->charges->where('tax_type', 'Post-GST')
            //     //     ->filter(function($charge) {
            //     //         return stripos($charge->charge_name, 'COURIER') !== false;
            //     //     })->sum('charge_amount');
            //     
            //     // $totalOtherPostGstCharges += $so->charges->where('tax_type', 'Post-GST')
            //     //     ->filter(function($charge) {
            //     //         return stripos($charge->charge_name, 'COURIER') === false;
            //     //     })->sum('charge_amount');
            // }
        }
        $weightedDiscountPercent = $totalSubTotal > 0 ? round(($totalDiscountAmount / $totalSubTotal) * 100, 2) : ($firstSo->discount_percent ?? 0);
        return response()->json([
            'success' => true,
            'customer_id' => $firstSo->customer_id,
            'store_id' => $firstSo->store_id,
            'agent_id' => $firstSo->agent_id,
            'commission_percent' => $firstSo->commission_percent,
            'billing_address' => $billingAddress,
            'shipping_address' => $shippingAddress,
            'other_state' => $firstSo->other_state ? 'yes' : 'no',
            'discount_percent' => $weightedDiscountPercent,
            'igst_percent' => $firstSo->igst_percent,
            'cgst_percent' => $firstSo->cgst_percent,
            'sgst_percent' => $firstSo->sgst_percent,
            'transporter_name' => $transporterName,
            'transport_gst_no' => $firstSo->transport_gst_no,
            'transport_mode_id' => $transportModeId,
            'sales_discount' => (!empty($firstSo->sales_discount_percent) && (float)$firstSo->sales_discount_percent > 0) ? $firstSo->sales_discount_percent : ($firstSo->customer->sales_discount ?? 0),
            'box_discount_amount' => (!empty($firstSo->box_discount_amount) && (float)$firstSo->box_discount_amount > 0) ? $firstSo->box_discount_amount : ($firstSo->customer->box_discount_amount ?? 0),
            // 'pre_gst_charges' => $totalPreGstCharges,
            'courier_charge' => $totalCourierCharges,
            // 'post_gst_charges' => $totalOtherPostGstCharges,
            'items' => $allItems->values()
        ]);
    }

    public function getSaleOrderDetails($id)
    {
        $so = SalesOrder::with([
            'items.brandCategory', 
            'items.item.brand', 
            'items.item.style', 
            'items.stockEntryItem.item.brand', 
            'items.stockEntryItem.item.style', 
            'items.uom', 
            'items.size', 
            'items.color', 
            'customer',
            'charges'
        ])->find($id);
        if (!$so) {
            return response()->json(['success' => false, 'message' => 'Sale Order not found']);
        }

        $allSkus = [];
        foreach ($so->items as $item) {
            if ($item->sku) {
                $allSkus[] = $item->sku;
            }
        }
        $allSkus = array_unique(array_filter($allSkus));

        $prefetchedStockEntryItems = collect();
        if (!empty($allSkus)) {
            $prefetchedStockEntryItems = \App\Models\StockEntryItem::where(function ($q) use ($allSkus) {
                    $q->whereIn('sku', $allSkus)
                      ->orWhereIn('barcode', $allSkus);
                })
                ->where('stock_type', 'finished_goods')
                ->whereNull('deleted_at')
                ->get();
        }

        $items = $so->items->map(function($item) use ($prefetchedStockEntryItems) {
            $brandName = '';
            $itemName = '';
            $sleeveType = is_array($item->sleeve) ? ($item->sleeve[0] ?? '') : $item->sleeve;

            // Resolve Stock Entry Item dynamically if not set
            $stockEntryItem = $item->stockEntryItem;
            if (!$stockEntryItem && $item->sku) {
                $stockEntryItem = $prefetchedStockEntryItems->first(function ($se) use ($item) {
                    return $se->sku === $item->sku || $se->barcode === $item->sku;
                });
            }

            if ($item->item) {
                if ($item->item->brand) {
                    $brandName = $item->item->brand->brand_name;
                } elseif ($item->brandCategory) {
                    $brandName = $item->brandCategory->name;
                }

                if ($item->item->style) {
                    $itemName = $item->item->style->style_name;
                } else {
                    $itemName = $item->item->name;
                }
            } 
            elseif ($stockEntryItem) {
                if ($stockEntryItem->item) {
                    $seItem = $stockEntryItem->item;
                    if ($seItem->brand) {
                        $brandName = $seItem->brand->brand_name;
                    } elseif ($seItem->brandCategory) {
                        $brandName = $seItem->brandCategory->name;
                    }

                    if ($seItem->style) {
                        $itemName = $seItem->style->style_name;
                    } else {
                        $itemName = $seItem->name;
                    }
                } else {
                    $brandName = $stockEntryItem->finished_item_code;
                }
                
                if (empty($sleeveType)) {
                    $sleeveType = $stockEntryItem->sleeve_type;
                }
            }
            elseif ($item->brandCategory) {
                $brandName = $item->brandCategory->name;
            }

            $stockQty = 0;
            if ($item->sku) {
                $stockQtyQuery = DB::table('stock_entry_items')
                    ->where(function($q) use($item) {
                        $q->where('sku', $item->sku)
                          ->orWhere('barcode', $item->sku);
                    })
                    ->where('stock_type', 'finished_goods')
                    ->whereNull('deleted_at');
                if ($item->size_id) {
                    $stockQtyQuery->where('size', $item->size_id);
                }
                if ($item->item && $item->item->code) {
                    $stockQtyQuery->where('finished_item_code', $item->item->code);
                } elseif ($stockEntryItem && $stockEntryItem->finished_item_code) {
                    $stockQtyQuery->where('finished_item_code', $stockEntryItem->finished_item_code);
                }
                $stockQty = $stockQtyQuery->sum(DB::raw('qty_in - COALESCE(qty_out, 0)')) ?? 0;
            } elseif ($item->stock_entry_item_id) {
                $stockQty = DB::table('stock_entry_items')->where('id', $item->stock_entry_item_id)->whereNull('deleted_at')->value(DB::raw('qty_in - COALESCE(qty_out, 0)')) ?? 0;
            } elseif ($stockEntryItem) {
                $stockQty = DB::table('stock_entry_items')->where('id', $stockEntryItem->id)->whereNull('deleted_at')->value(DB::raw('qty_in - COALESCE(qty_out, 0)')) ?? 0;
            }

            if (empty($brandName)) {
                $brandName = $item->item_name;
                $itemName = '';
            }

            $finishedItemCode = '';
            if ($item->item && $item->item->code) {
                $finishedItemCode = $item->item->code;
            } elseif ($stockEntryItem && $stockEntryItem->finished_item_code) {
                $finishedItemCode = $stockEntryItem->finished_item_code;
            }

            $finalMrp = $item->mrp ?? 0;
            $finalRate = $item->rate ?? 0;

            $artNo = $item->art_no;
            if ($stockEntryItem && !empty($stockEntryItem->art_no)) {
                $artNo = $stockEntryItem->art_no;
            }

            if ($finishedItemCode && $artNo) {
                $sizeName = $item->size ? $item->size->size : ($item->size_id ?: null);
                $itemPrice = self::getActiveItemPrice($finishedItemCode, $artNo, $sizeName);
                if ($itemPrice) {
                    $finalMrp = $itemPrice->selling_price;
                    $finalRate = $itemPrice->unit_price;
                }
            }

            return [
                'brand_id' => $item->brand_cat_id,
                'brand_name' => $brandName ?: '',
                'item_id' => $item->item_id,
                'item_name' => $itemName ?: ($item->item_name ?? ''),
                'item_code' => $item->item ? $item->item->code : ($stockEntryItem ? $stockEntryItem->finished_item_code : ''),
                'uom_id' => $item->uom_id,
                'uom_code' => $item->uom_id ?: '',
                'qty' => $item->qty,
                'stock_qty' => (float)$stockQty,
                'rate' => $finalRate,
                'mrp' => $finalMrp,
                'amount' => (float)$finalRate * $item->qty,
                'art_no' => $artNo,
                'hsn_sac' => $item->hsn_sac ?? null,
                'sku' => $item->sku,
                'size_id' => $item->size_id,
                'size_name' => $item->size ? $item->size->size : '',
                'color_id' => $item->color_id,
                'color_name' => $item->color ? $item->color->color_name : '',
                'api_color' => $item->api_color,
                'sleeve' => $sleeveType ?: '',
                'stock_entry_item_id' => $item->stock_entry_item_id ?: ($stockEntryItem ? $stockEntryItem->id : null),
            ];
        });

        $billingAddress = '';
        if ($so->customer) {
            $c = $so->customer;
            $billingAddress = implode(', ', array_filter([$c->address_line_1, $c->address_line_2, $c->address_line_3, $c->city, $c->state, $c->pincode]));
        }
        // not in live
        // $preGstCharges = 0;
        $courierCharge = 0;
        // $postGstCharges = 0;
        // if ($so->charges) {
        //     // $preGstCharges = $so->charges->where('tax_type', 'Pre-GST')->sum('charge_amount');
        //     // $courierCharge = $so->charges->where('tax_type', 'Post-GST')
        //     //     ->filter(function($charge) {
        //     //         return stripos($charge->charge_name, 'COURIER') !== false;
        //     //     })->sum('charge_amount');
        //     // $postGstCharges = $so->charges->where('tax_type', 'Post-GST')
        //     //     ->filter(function($charge) {
        //     //         return stripos($charge->charge_name, 'COURIER') === false;
        //     //     })->sum('charge_amount');
        // }

        return response()->json([
            'success' => true,
            'customer_id' => $so->customer_id,
            'store_id' => $so->store_id,
            'agent_id' => $so->agent_id,
            'commission_percent' => $so->commission_percent,
            'billing_address' => $billingAddress,
            'shipping_address' => $so->shipping_address,
            'other_state' => $so->other_state ? 'yes' : 'no',
            'discount_percent' => $so->discount_percent,
            'igst_percent' => $so->igst_percent,
            'cgst_percent' => $so->cgst_percent,
            'sgst_percent' => $so->sgst_percent,
            'transporter_name' => $so->transporter_name,
            'transport_gst_no' => $so->transport_gst_no,
            'transport_mode_id' => $so->transport_mode_id,
            'sales_discount' => $so->sales_discount_percent ?? ($so->customer->sales_discount ?? 0),
            'box_discount_amount' => $so->box_discount_amount ?? ($so->customer->box_discount_amount ?? 0),
            // 'pre_gst_charges' => $preGstCharges,
            'courier_charge' => $courierCharge,
            // 'post_gst_charges' => $postGstCharges,
            'items' => $items
        ]);
    }
    
    public function updateStatus(Request $request, $id)
    {
        $invoice = SalesInvoice::with('items')->findOrFail($id);
        $oldStatus = $invoice->invoice_status;
        $newStatus = $request->status;
        $activeStatuses = ['Paid', 'Partially Paid', 'Unpaid/Credit'];

        if ($newStatus === 'Paid') {
            $totalPaid = DB::table('payments')->where('reference_id', $id)->where('reference_type', 'Customer Collection')->whereNull('deleted_at')->sum('amount');

            $balance = $invoice->grand_total - $totalPaid;

            if ($balance > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot mark as Paid. Outstanding balance of ' . number_format($balance, 2) . ' still remaining. Total Invoice: ' . number_format($invoice->grand_total, 2) . ', Total Paid: ' . number_format($totalPaid, 2),
                ], 422);
            }
        }
        $oldData = $invoice->toArray();
        $invoice->invoice_status = $newStatus;
        $invoice->save();
        $newData = $invoice->fresh()->toArray();
        addLog('update_status', 'Sales Invoice Status', 'sales_invoices', $id, $oldData, $newData);

        $wasActive = in_array($oldStatus, $activeStatuses);
        $isNowActive = in_array($newStatus, $activeStatuses);

        if (!$wasActive && $isNowActive) {
        } elseif ($wasActive && !$isNowActive) {
        }

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    public function destroy($id)
    {
        $invoice = SalesInvoice::with('items')->findOrFail($id);
        
        DB::beginTransaction();
        try {
            $this->adjustStock($id, true);
            addLog('delete', 'Sales Invoice', 'sales_invoices', $id, $invoice->toArray(), null);
            $invoice->delete();
            
            DB::commit();
            return redirect('sales_invoices')->with('success', 'Sales Invoice deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete invoice: ' . $e->getMessage());
        }
    }
    public function downloadPdf($id)
    {
        $invoice = SalesInvoice::with([
            'brand',
            'customer.state', 
            'customer.city', 
            'salesOrder.salesAgent', 
            'items' => function($q) {
                $q->orderBy('art_no');
            },
            'items.brandCategory', 
            'items.item.uom', 
            'items.uom'
        ])->findOrFail($id);
        
        $setting = Setting::with(['state', 'city'])->first();
        
        $discountRatio = $invoice->sub_total > 0 ? (($invoice->discount ?? 0) / $invoice->sub_total) : 0;
        $taxSummary = [];
        foreach ($invoice->items as $item) {
            $hsn = $item->hsn_sac ?: 'N/A';
            if (!isset($taxSummary[$hsn])) {
                $taxSummary[$hsn] = [
                    'hsn' => $hsn,
                    'taxable_value' => 0,
                    'cgst_rate' => $invoice->cgst_percent,
                    'cgst_amount' => 0,
                    'sgst_rate' => $invoice->sgst_percent,
                    'sgst_amount' => 0,
                    'igst_rate' => $invoice->igst_percent,
                    'igst_amount' => 0,
                ];
            }
            $taxSummary[$hsn]['taxable_value'] += $item->amount - ($item->amount * $discountRatio);
        }

        foreach ($taxSummary as &$summary) {
            $summary['cgst_amount'] = ($summary['taxable_value'] * $summary['cgst_rate']) / 100;
            $summary['sgst_amount'] = ($summary['taxable_value'] * $summary['sgst_rate']) / 100;
            $summary['igst_amount'] = ($summary['taxable_value'] * $summary['igst_rate']) / 100;
        }

        $totalInWords = numberToWords($invoice->grand_total);
        $totalTaxInWords = numberToWords($invoice->tax_amount);

        $pdf = Pdf::loadView('sales_invoice.pdf', compact('invoice', 'setting', 'taxSummary', 'totalInWords', 'totalTaxInWords'));
        $pdf->setPaper('A4', 'portrait');
        
        $safeInvoiceNo = str_replace(['/', '\\'], '_', $invoice->inv_no);
        return $pdf->stream('Sales_Invoice_' . $safeInvoiceNo . '.pdf');
    }

    public function print($id)
    {
        $invoice = SalesInvoice::with([
            'brand',
            'customer.state', 
            'customer.city', 
            'salesOrder.salesAgent', 
            'items' => function($q) {
                $q->orderBy('art_no');
            },
            'items.brandCategory', 
            'items.item.uom', 
            'items.uom'
        ])->findOrFail($id);
        
        $setting = Setting::with(['state', 'city'])->first();
        
        $discountRatio = $invoice->sub_total > 0 ? (($invoice->discount ?? 0) / $invoice->sub_total) : 0;
        $taxSummary = [];
        foreach ($invoice->items as $item) {
            $hsn = $item->hsn_sac ?: 'N/A';
            if (!isset($taxSummary[$hsn])) {
                $taxSummary[$hsn] = [
                    'hsn' => $hsn,
                    'taxable_value' => 0,
                    'cgst_rate' => $invoice->cgst_percent,
                    'cgst_amount' => 0,
                    'sgst_rate' => $invoice->sgst_percent,
                    'sgst_amount' => 0,
                    'igst_rate' => $invoice->igst_percent,
                    'igst_amount' => 0,
                ];
            }
            $taxSummary[$hsn]['taxable_value'] += $item->amount - ($item->amount * $discountRatio);
        }

        foreach ($taxSummary as &$summary) {
            $summary['cgst_amount'] = ($summary['taxable_value'] * $summary['cgst_rate']) / 100;
            $summary['sgst_amount'] = ($summary['taxable_value'] * $summary['sgst_rate']) / 100;
            $summary['igst_amount'] = ($summary['taxable_value'] * $summary['igst_rate']) / 100;
        }

        $totalInWords = numberToWords($invoice->grand_total);
        $totalTaxInWords = numberToWords($invoice->tax_amount);
        $is_print = true;

        return view('sales_invoice.pdf', compact('invoice', 'setting', 'taxSummary', 'totalInWords', 'totalTaxInWords', 'is_print'));
    }
    public function printSticker($id)
    {
        $invoice = SalesInvoice::with(['customer.state', 'customer.city', 'customer.place'])->findOrFail($id);
        $totalPcs = $invoice->items->sum('quantity');
        $boxCount = (int) ($invoice->no_of_box ?: 1);
        $setting = Setting::with(['state', 'city'])->first();
        $is_print = true;
        return view('sales_invoice.sticker', compact('invoice', 'totalPcs', 'setting', 'is_print', 'boxCount'));
    }

    public function printTransportSticker($id)
    {
        $invoice = SalesInvoice::with(['customer.state', 'customer.city', 'customer.place'])->findOrFail($id);
        $totalPcs = $invoice->items->sum('quantity');
        $boxCount = (int) ($invoice->no_of_box ?: 1);
        $setting = Setting::with(['state', 'city'])->first();
        $is_print = true;
        return view('sales_invoice.transport_sticker', compact('invoice', 'totalPcs', 'setting', 'is_print', 'boxCount'));
    }

    public function printCourierSticker($id)
    {
        $invoice = SalesInvoice::with(['customer.state', 'customer.city', 'customer.place'])->findOrFail($id);
        $totalPcs = $invoice->items->sum('quantity');
        $boxCount = (int) ($invoice->no_of_box ?: 1);
        $setting = Setting::with(['state', 'city'])->first();
        $is_print = true;
        return view('sales_invoice.courier_sticker', compact('invoice', 'totalPcs', 'setting', 'is_print', 'boxCount'));
    }

    public function downloadDeliveryOrder($id)
    {
        $invoice = SalesInvoice::with([
            'customer.state', 
            'customer.city', 
            'salesOrder.salesAgent', 
            'items.brandCategory', 
            'items.item.uom', 
            'items.uom'
        ])->findOrFail($id);
        
        $setting = Setting::with(['state', 'city'])->first();
        $totalPcs = $invoice->items->sum('quantity');

        $pdf = Pdf::loadView('sales_invoice.delivery_order', compact('invoice', 'setting', 'totalPcs'));
        $pdf->setPaper('A4', 'portrait');
        
        $safeInvoiceNo = str_replace(['/', '\\'], '_', $invoice->inv_no);
        return $pdf->stream('Delivery_Order_' . $safeInvoiceNo . '.pdf');
    }

    public function downloadOpenDeliveryOrder($id)
    {
        $invoice = SalesInvoice::with([
            'customer.state', 
            'customer.city', 
            'salesOrder.salesAgent', 
            'items.brandCategory', 
            'items.item.uom', 
            'items.uom'
        ])->findOrFail($id);
        
        $setting = Setting::with(['state', 'city'])->first();
        $totalPcs = $invoice->items->sum('quantity');

        $pdf = Pdf::loadView('sales_invoice.open_delivery_order', compact('invoice', 'setting', 'totalPcs'));
        $pdf->setPaper('A4', 'portrait');
        
        $safeInvoiceNo = str_replace(['/', '\\'], '_', $invoice->inv_no);
        return $pdf->stream('Open_Delivery_Order_' . $safeInvoiceNo . '.pdf');
    }

    public function generateEInvoice(Request $request, $id, \App\Services\EInvoiceService $eInvoiceService)
    {
        $invoice = SalesInvoice::findOrFail($id);
        if ($invoice->einvoice_status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'This E-Invoice has been cancelled on IRP. According to GST guidelines, you cannot reuse this invoice number to generate a new E-Invoice. You must issue a new sales invoice with a new invoice number.'
            ]);
        }
        if ($invoice->irn) {
            return response()->json(['success' => false, 'message' => 'E-Invoice already generated']);
        }
        
        if (strlen($invoice->inv_no) < 1 || strlen($invoice->inv_no) > 16 || !preg_match('/^[a-zA-Z1-9][a-zA-Z0-9\/\-]*$/', $invoice->inv_no)) {
            return response()->json([
                'success' => false,
                'message' => 'The Sales Invoice Number is invalid for e-Invoice API. It must be 1-16 characters long, start with a letter or 1-9, and contain only letters, numbers, hyphens, or forward slashes.'
            ]);
        }

        $transporterData = [];
        if (!empty($invoice->vehicle_no)) {
            $transporterData = [
                'transport_distance' => $invoice->transport_distance,
                'transport_mode' => $invoice->transport_mode,
                'transporter_id' => $invoice->transporter_id,
                'transporter_name' => $invoice->transporter_name,
                'tran_doc_date' => $invoice->tran_doc_date ? Carbon::parse($invoice->tran_doc_date)->format('d/m/Y') : null,
                'tran_doc_no' => $invoice->tran_doc_no,
                'vehicle_no' => $invoice->vehicle_no,
                'veh_type' => $invoice->veh_type,
            ];
        }

        $result = $eInvoiceService->generateEInvoice($invoice, $transporterData);
        if (!$result['success'] && isset($result['message'])) {
            if (strpos($result['message'], 'is cancelled and document date') !== false) {
                preg_match('/GSTIN - ([A-Z0-9]+)/', $result['message'], $matches);
                $gstin = $matches[1] ?? 'Unknown';
                $invoiceDate = \Carbon\Carbon::parse($invoice->inv_date)->format('d-m-Y');
                
                $result['message'] = "<strong>e-Invoice Generation Failed</strong><br><br>"
                                   . "<strong>Reason:</strong> The customer's GSTIN has been cancelled before the invoice date. Therefore, an e-Invoice cannot be generated for this invoice.<br><br>"
                                   . "<strong>Customer GSTIN:</strong> " . $gstin . "<br>"
                                   . "<strong>Invoice Date:</strong> " . $invoiceDate . "<br><br>"
                                   . "Please verify the customer's GST registration status or update the GSTIN before generating the e-Invoice.";
            } elseif (strpos($result['message'], 'field POS must match') !== false || strpos($result['message'], 'field State must') !== false) {
                $result['message'] = "<strong>e-Invoice Generation Failed</strong><br><br>"
                                   . "<strong>Reason:</strong> The customer's State or Place of Supply (POS) State Code is invalid or missing.<br><br>"
                                   . "<strong>Please verify the following before generating the e-Invoice:</strong><br>"
                                   . "<ul style='text-align: left; margin-bottom: 0; padding-left: 20px;'>"
                                   . "<li>Ensure the State is selected correctly.</li>"
                                   . "<li>Ensure the State Code (POS) is a valid 2-digit GST State Code (e.g., 33 for Tamil Nadu, 29 for Karnataka).</li>"
                                   . "<li>Update the customer master if the State or State Code is incorrect.</li>"
                                   . "</ul>";
            }
        }
        return response()->json($result);
    }

    public function generateEWayBill(Request $request, $id, \App\Services\EInvoiceService $eInvoiceService)
    {
        $invoice = SalesInvoice::findOrFail($id);

        if (empty($invoice->irn)) {
            return response()->json([
                'success' => false,
                'message' => 'Generate E-Invoice first before E-Way Bill.'
            ]);
        }

        $result = $eInvoiceService->generateEWayBill($invoice, $request->all());

        return response()->json($result);
    }

    public function cancelEInvoice(Request $request, $id, \App\Services\EInvoiceService $eInvoiceService)
    {
        $invoice = SalesInvoice::findOrFail($id);

        if (empty($invoice->irn)) {
            return response()->json([
                'success' => false,
                'message' => 'No active E-Invoice found for this sales invoice.'
            ]);
        }

        if ($invoice->ack_date) {
            $ackDateTime = \Carbon\Carbon::parse($invoice->ack_date);
            if ($ackDateTime->diffInHours(now()) >= 24) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cancellation time window expired. According to GST guidelines, an E-Invoice cannot be cancelled after 24 hours of generation. Please issue a Credit Note instead.'
                ]);
            }
        }

        $cancelReason = $request->input('cancel_reason', '2');
        $cancelRemarks = $request->input('cancel_remarks', 'Data Entry Mistake');

        $oldData = $invoice->toArray();

        if (!empty($invoice->eway_bill_no)) {
            if ($invoice->eway_bill_date) {
                $ewbDateTime = \Carbon\Carbon::parse($invoice->eway_bill_date);
                if ($ewbDateTime->diffInHours(now()) >= 24) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot cancel. E-Way Bill cancellation time window (24 hours) has expired.'
                    ]);
                }
            }

            $ewbResult = $eInvoiceService->cancelEWayBill($invoice, $cancelReason, $cancelRemarks);
            if (!$ewbResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to cancel linked E-Way Bill first: ' . $ewbResult['message']
                ]);
            }
            $invoice->refresh();
        }

        $result = $eInvoiceService->cancelEInvoice($invoice, $cancelReason, $cancelRemarks);

        if ($result['success']) {
            try {
                \DB::beginTransaction();
                
                $invoice->refresh();

                if (!$invoice->stock_reverted) {
                    $this->adjustStock($invoice->id, true);
                    
                    $invoice->update([
                        'invoice_status' => 'Cancelled',
                        'cancellation_date' => now(),
                        'cancel_reason' => $cancelReason,
                        'cancel_remarks' => $cancelRemarks,
                        'cancelled_by' => auth()->id() ?? 1,
                        'stock_reverted' => true,
                    ]);
                } else {
                    $invoice->update([
                        'invoice_status' => 'Cancelled',
                        'cancellation_date' => now(),
                        'cancel_reason' => $cancelReason,
                        'cancel_remarks' => $cancelRemarks,
                        'cancelled_by' => auth()->id() ?? 1,
                    ]);
                }

                $newData = $invoice->toArray();
                addLog('cancel_einvoice', 'Sales Invoice E-Invoice Cancelled', 'sales_invoices', $id, $oldData, $newData);

                \DB::commit();

                if (!empty($oldData['eway_bill_no'])) {
                    $result['message'] = 'E-Way Bill and E-Invoice cancelled successfully, and stock reverted.';
                } else {
                    $result['message'] = 'E-Invoice cancelled successfully, and stock reverted.';
                }
            } catch (\Exception $e) {
                \DB::rollBack();
                \Log::error('Failed to update local records after E-Invoice cancellation: ' . $e->getMessage());
                $result['message'] = 'E-Invoice cancelled on portal, but local stock reversion failed. Please contact admin.';
            }
        }

        return response()->json($result);
    }

    public function cancelEWayBill(Request $request, $id, \App\Services\EInvoiceService $eInvoiceService)
    {
        $invoice = SalesInvoice::findOrFail($id);

        if (empty($invoice->eway_bill_no)) {
            return response()->json([
                'success' => false,
                'message' => 'No active E-Way Bill found for this invoice.'
            ]);
        }

        if ($invoice->eway_bill_date) {
            $ewbDateTime = \Carbon\Carbon::parse($invoice->eway_bill_date);
            if ($ewbDateTime->diffInHours(now()) >= 24) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cancellation time window expired. According to GST guidelines, an E-Way Bill cannot be cancelled after 24 hours of generation.'
                ]);
            }
        }

        $cancelReason = $request->input('cancel_reason', '2');
        $cancelRemarks = $request->input('cancel_remarks', 'Data Entry Mistake');

        $oldData = $invoice->toArray();
        $result = $eInvoiceService->cancelEWayBill($invoice, $cancelReason, $cancelRemarks);

        if ($result['success']) {
            $invoice->refresh();
            $newData = $invoice->toArray();
            addLog('cancel_ewaybill', 'Sales Invoice E-Way Bill Cancelled', 'sales_invoices', $id, $oldData, $newData);
        }

        return response()->json($result);
    }

    public function recreate($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('create sales-invoice')) {
            return unauthorizedRedirect();
        }

        $original = SalesInvoice::with('items')->findOrFail($id);

        DB::beginTransaction();
        try {
            $nextInvNumber = $this->generateInvoiceNumber($original->brand_id, now());

            $newInvoice = $original->replicate();
            $newInvoice->inv_no = $nextInvNumber;
            $newInvoice->inv_date = now();
            $newInvoice->invoice_status = 'Draft';
            $newInvoice->einvoice_status = null;
            $newInvoice->irn = null;
            $newInvoice->ack_no = null;
            $newInvoice->ack_date = null;
            $newInvoice->signed_qr_code = null;
            $newInvoice->eway_bill_no = null;
            $newInvoice->eway_bill_date = null;
            $newInvoice->eway_bill_valid_till = null;
            $newInvoice->received_amount = 0.00;
            $newInvoice->save();

            foreach ($original->items as $item) {
                $newItem = $item->replicate();
                $newItem->sales_invoice_id = $newInvoice->id;
                $newItem->save();
            }

            DB::commit();

            return redirect('sales_invoices/add/' . $newInvoice->id)->with('success', 'Invoice details copied successfully. New Invoice ' . $newInvoice->inv_no . ' created as Draft.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to copy invoice: ' . $e->getMessage());
        }
    }

    public function calculateDistance(Request $request)
    {
        $fromPincode = trim($request->query('from_pincode'));
        $toPincode = trim($request->query('to_pincode'));

        if (empty($fromPincode) || empty($toPincode)) {
            return response()->json(['success' => false, 'message' => 'Both pincodes are required.']);
        }

        try {
            $fromCoords = $this->getGeocode($fromPincode);
            $toCoords = $this->getGeocode($toPincode);

            if (!$fromCoords || !$toCoords) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to resolve coordinates. Please enter distance manually.'
                ]);
            }

            $straightDistance = $this->getDistance(
                $fromCoords['lat'], $fromCoords['lng'],
                $toCoords['lat'], $toCoords['lng']
            );

            $roadDistance = $straightDistance * 1.1576;

            return response()->json([
                'success' => true,
                'from_pincode' => $fromPincode,
                'to_pincode' => $toPincode,
                'from_coords' => $fromCoords,
                'to_coords' => $toCoords,
                'straight_line_km' => round($straightDistance, 2),
                'distance' => round($roadDistance),
                'note' => 'Estimated road distance (Haversine distance multiplied by a 1.1576 correction factor).'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function getGeocode($pincode)
    {
        $cleanPincode = preg_replace('/[^0-9]/', '', $pincode);
        if (strlen($cleanPincode) !== 6) {
            return null;
        }

        return \Illuminate\Support\Facades\Cache::remember("geocode_in_{$cleanPincode}", now()->addDays(30), function () use ($cleanPincode) {
            try {
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'User-Agent' => 'NachiasERP/1.0 (admin@nachias.com)'
                ])->timeout(5)->get('https://nominatim.openstreetmap.org/search', [
                    'postalcode' => $cleanPincode,
                    'countrycodes' => 'in',
                    'format' => 'json',
                    'limit' => 1
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                        return [
                            'lat' => (float)$data[0]['lat'],
                            'lng' => (float)$data[0]['lon'],
                            'source' => 'nominatim_postal'
                        ];
                    }
                }

                $postalResponse = \Illuminate\Support\Facades\Http::timeout(5)->get("https://api.postalpincode.in/pincode/{$cleanPincode}");
                if ($postalResponse->successful()) {
                    $postalData = $postalResponse->json();
                    if (!empty($postalData) && isset($postalData[0]['Status']) && $postalData[0]['Status'] === 'Success' && !empty($postalData[0]['PostOffice'])) {
                        $postOffice = $postalData[0]['PostOffice'][0];
                        $locality = $postOffice['Name'] ?? '';
                        $district = $postOffice['District'] ?? '';
                        $state = $postOffice['State'] ?? '';

                        $queries = [];
                        if ($locality && $district && $state) {
                            $queries[] = "{$locality}, {$district}, {$state}, India";
                        }
                        if ($district && $state) {
                            $queries[] = "{$district}, {$state}, India";
                        }

                        foreach ($queries as $q) {
                            $geoResponse = \Illuminate\Support\Facades\Http::withHeaders([
                                'User-Agent' => 'NachiasERP/1.0 (admin@nachias.com)'
                            ])->timeout(5)->get('https://nominatim.openstreetmap.org/search', [
                                'q' => $q,
                                'countrycodes' => 'in',
                                'format' => 'json',
                                'limit' => 1
                            ]);

                            if ($geoResponse->successful()) {
                                $geoData = $geoResponse->json();
                                if (!empty($geoData) && isset($geoData[0]['lat']) && isset($geoData[0]['lon'])) {
                                    return [
                                        'lat' => (float)$geoData[0]['lat'],
                                        'lng' => (float)$geoData[0]['lon'],
                                        'source' => 'postal_in_fallback'
                                    ];
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Geocoding failed for pincode {$cleanPincode}: " . $e->getMessage());
            }
            return null;
        });
    }

    public function getDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    public function scanItems($id)
    {
        $invoice = SalesInvoice::with(['customer', 'items.item', 'items.stockEntryItem'])->findOrFail($id);
        $setting = Setting::first();
        return view('sales_invoice.scan_items', compact('invoice', 'setting'));
    }

    public function saveScanProgress(Request $request, $id)
    {
        $invoice = SalesInvoice::findOrFail($id);
        if ($invoice->delivery_status === 'Dispatched') {
            return response()->json(['success' => false, 'message' => 'Invoice is already dispatched and locked.'], 400);
        }
        
        if (strtolower($invoice->einvoice_status) === 'cancelled') {
            return response()->json(['success' => false, 'message' => 'E-Invoice is cancelled. Scanning is not allowed.'], 400);
        }

        $items = $request->input('items', []);
        foreach ($items as $itemData) {
            $itemId = $itemData['id'];
            $scannedQty = $itemData['scanned_qty'];
            
            $invoiceItem = SalesInvoiceItem::where('sales_invoice_id', $id)->where('id', $itemId)->first();
            if ($invoiceItem) {
                if ($scannedQty <= $invoiceItem->quantity) {
                    $invoiceItem->update(['scanned_qty' => $scannedQty]);
                }
            }
        }
        
        return response()->json(['success' => true, 'message' => 'Progress saved']);
    }

    public function completeDispatch($id)
    {
        $invoice = SalesInvoice::findOrFail($id);
        if (strtolower($invoice->einvoice_status) === 'cancelled') {
            return response()->json(['success' => false, 'message' => 'Cannot dispatch a cancelled e-invoice.'], 400);
        }
        $totalScanned = SalesInvoiceItem::where('sales_invoice_id', $id)->sum('scanned_qty');
        if ($totalScanned <= 0) {
            return response()->json(['success' => false, 'message' => 'Cannot dispatch! No items have been scanned yet. Please scan items first.'], 400);
        }
        $invoice->update([
            'delivery_status' => 'Dispatched',
            'dispatch_completed_at' => now()
        ]);
        return response()->json(['success' => true, 'message' => 'Dispatch completed successfully']);
    }

    public function updateDeliveryStatus(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('edit sales-invoice')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $request->validate([
            'invoice_id' => 'required|exists:sales_invoices,id',
            'delivery_status' => 'required|in:Pending,Dispatched,Partially Delivered,Delivered,Cancel'
        ]);
        $invoice = SalesInvoice::findOrFail($request->invoice_id);
        $currentStatus = $invoice->delivery_status;
        $newStatus = $request->delivery_status;
        if ($currentStatus === 'Delivered' && in_array($newStatus, ['Pending', 'Dispatched'])) {
            return response()->json(['success' => false, 'message' => 'Cannot revert a Delivered invoice back to Pending or Dispatched.'], 400);
        }
        if ($currentStatus === 'Cancel' && $newStatus !== 'Cancel') {
            return response()->json(['success' => false, 'message' => 'Cannot change status of a Cancelled delivery.'], 400);
        }
        if ($currentStatus === 'Pending' && $newStatus === 'Delivered') {
            return response()->json(['success' => false, 'message' => 'Invoice must be Dispatched before it can be Delivered.'], 400);
        }
        if ($newStatus === 'Dispatched') {
            $totalScanned = SalesInvoiceItem::where('sales_invoice_id', $request->invoice_id)->sum('scanned_qty');
            if ($totalScanned <= 0) {
                return response()->json(['success' => false, 'message' => 'Cannot change status to Dispatched! No items have been scanned yet.'], 400);
            }
        }
        $invoice->update([
            'delivery_status' => $newStatus
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Delivery Status updated successfully'
        ]);
    }
    public function getFinishedGoodsStock(Request $request)
    {
        $search = $request->get('q');
        
        $storeCategory = \App\Models\StoreCategory::where('name', 'like', '%Finished Goods%')->first();
        $storeCategoryId = $storeCategory ? $storeCategory->id : 4;
        
        $query = \App\Models\StockEntryItem::select(
            'stock_entry_items.id', 
            'stock_entry_items.art_no', 
            'stock_entry_items.size', 
            'stock_entry_items.color_id',
            'stock_entry_items.sleeve_type',
            'stock_entry_items.price',
            'stock_entry_items.mrp',
            'stock_entry_items.sku',
            'stock_entry_items.finished_item_code',
            'colors.name as color_name',
            \DB::raw('(stock_entry_items.qty_in - COALESCE(stock_entry_items.qty_out, 0)) as available_qty')
        )
        ->join('stock_entries', 'stock_entry_items.stock_entry_id', '=', 'stock_entries.id')
        ->leftJoin('colors', 'stock_entry_items.color_id', '=', 'colors.id')
        ->where('stock_entries.store_category_id', $storeCategoryId)
        ->having('available_qty', '>', 0);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('stock_entry_items.art_no', 'like', "%{$search}%")
                  ->orWhere('stock_entry_items.sku', 'like', "%{$search}%")
                  ->orWhere('stock_entry_items.size', 'like', "%{$search}%")
                  ->orWhere('colors.name', 'like', "%{$search}%");
            });
        }
        
        $items = $query->limit(50)->get();
        
        $results = [];
        foreach ($items as $item) {
            $displayName = $item->art_no;
            if ($item->color_name) $displayName .= " - " . $item->color_name;
            if ($item->size) $displayName .= " - " . $item->size;
            if ($item->sleeve_type) $displayName .= " - " . $item->sleeve_type;
            
            $itemPrice = \DB::table('item_prices')
                ->where('finished_item_code', $item->finished_item_code)
                ->where('art_no', $item->art_no)
                ->where('size', $item->size)
                ->where('status', 'Active')
                ->whereNull('deleted_at')
                ->whereDate('effective_from', '<=', now())
                ->orderBy('effective_from', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            if (!$itemPrice) {
                $itemPrice = \DB::table('item_prices')
                    ->where('finished_item_code', $item->finished_item_code)
                    ->where('art_no', $item->art_no)
                    ->whereNull('size')
                    ->where('status', 'Active')
                    ->whereNull('deleted_at')
                    ->whereDate('effective_from', '<=', now())
                    ->orderBy('effective_from', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();
            }

            $mrp = $itemPrice ? $itemPrice->selling_price : 0;
            $price = $itemPrice ? $itemPrice->unit_price : 0;

            $results[] = [
                'id' => $item->id,
                'text' => $displayName . " (Stock: {$item->available_qty})",
                'art_no' => $item->art_no,
                'size' => $item->size,
                'color_id' => $item->color_id,
                'color_name' => $item->color_name,
                'sleeve_type' => $item->sleeve_type,
                'price' => $price,
                'mrp' => $mrp,
                'sku' => $item->sku,
                'available_qty' => $item->available_qty,
            ];
        }
        
        return response()->json(['results' => $results]);
    }
    private function validateStockAvailability($items, $invoiceId = null)
    {
        $existingInvoiceItems = [];
        if ($invoiceId) {
            $existingInvoiceItems = SalesInvoiceItem::where('sales_invoice_id', $invoiceId)->get();
            
            $stockFieldsChanged = false;
            $reqItemsMap = [];
            foreach ($items as $reqItem) {
                if (!empty($reqItem['id'])) {
                    $reqItemsMap[$reqItem['id']] = $reqItem;
                } else {
                    $stockFieldsChanged = true;
                    break;
                }
            }
            
            if (!$stockFieldsChanged) {
                if (count($items) !== $existingInvoiceItems->count()) {
                    $stockFieldsChanged = true;
                } else {
                    foreach ($existingInvoiceItems as $ei) {
                        if (!isset($reqItemsMap[$ei->id])) {
                            $stockFieldsChanged = true;
                            break;
                        }
                        
                        $reqItem = $reqItemsMap[$ei->id];
                        if (
                            $ei->quantity != ($reqItem['quantity'] ?? 0) ||
                            $ei->sku != ($reqItem['sku'] ?? null) ||
                            $ei->size != ($reqItem['size'] ?? null) ||
                            $ei->art_no != ($reqItem['art_no'] ?? null) ||
                            $ei->color_id != ($reqItem['color_id'] ?? null) ||
                            $ei->sleeve_type != ($reqItem['sleeve_type'] ?? null) ||
                            $ei->stock_entry_item_id != ($reqItem['stock_entry_item_id'] ?? null)
                        ) {
                            $stockFieldsChanged = true;
                            break;
                        }
                    }
                }
            }
            
            if (!$stockFieldsChanged) {
                return;
            }
        }

        $requiredQuantities = [];
        foreach ($items as $item) {
            $sku = $item['sku'] ?? null;
            $size = $item['size'] ?? null;
            $sleeve_type = $item['sleeve_type'] ?? null;
            $stockEntryItemId = $item['stock_entry_item_id'] ?? null;
            
            $finishedItemCode = null;
            $artNo = $item['art_no'] ?? null;
            if ($stockEntryItemId) {
                $stItem = \App\Models\StockEntryItem::find($stockEntryItemId);
                if ($stItem) {
                    $finishedItemCode = $stItem->finished_item_code;
                    $artNo = $stItem->art_no;
                }
            }
            
            $identifier = $sku ? $sku . '_' . $size . '_' . $sleeve_type . '_' . $finishedItemCode . '_' . $artNo : ($item['item_name'] ?? $item['art_no'] ?? 'unknown');
            
            if (!isset($requiredQuantities[$identifier])) {
                $requiredQuantities[$identifier] = [
                    'sku' => $sku,
                    'size' => $size,
                    'sleeve_type' => $sleeve_type,
                    'finished_item_code' => $finishedItemCode,
                    'art_no' => $artNo,
                    'qty' => 0,
                    'display' => $sku ?: $identifier
                ];
            }
            $requiredQuantities[$identifier]['qty'] += (float)($item['quantity'] ?? 0);
            
            if (!$stockEntryItemId) {
                $stockQuery = clone StockEntryItem::where('stock_type', 'finished_goods')->whereNull('deleted_at');
                if (!empty($item['sku'])) {
                    $stockQuery->where(function ($q) use ($item) {
                        $q->where('sku', $item['sku'])->orWhere('barcode', $item['sku']);
                    });
                } else {
                    $stockQuery->where(function ($q) use ($item) {
                        $fic = $item['item_name'] ?? ($item['art_no'] ?? '');
                        $q->where('finished_item_code', $fic)
                          ->orWhere('art_no', $fic);
                    });
                }
                if (!empty($item['size'])) {
                    $stockQuery->where('size', $item['size']);
                }
                if (!empty($item['art_no'])) {
                    $stockQuery->where('art_no', $item['art_no']);
                }
                if (!empty($item['sleeve_type'])) {
                    $sleeveUpper = strtoupper(trim($item['sleeve_type']));
                    if ($sleeveUpper === 'FS' || $sleeveUpper === 'F/S' || $sleeveUpper === 'FULL') {
                        $sleeveDbValues = ['FULL', 'Full', 'F/S', 'Fs', 'Full Sleeve', 'F/S Sleeve', 'FS'];
                    } elseif ($sleeveUpper === 'HS' || $sleeveUpper === 'H/S' || $sleeveUpper === 'HALF') {
                        $sleeveDbValues = ['HALF', 'Half', 'H/S', 'Hs', 'Half Sleeve', 'H/S Sleeve', 'HS'];
                    } else {
                        $sleeveDbValues = [$item['sleeve_type']];
                    }
                    $stockQuery->whereIn('sleeve_type', $sleeveDbValues);
                }

                $matchedStockItem = $stockQuery->orderByRaw('(qty_in - qty_out) > 0 DESC')->orderBy('id', 'desc')->first();
                if ($matchedStockItem) {
                    $stockEntryItemId = $matchedStockItem->id;
                    $requiredQuantities[$identifier]['finished_item_code'] = $matchedStockItem->finished_item_code;
                    $requiredQuantities[$identifier]['art_no'] = $matchedStockItem->art_no;
                }
            }

            if (!$stockEntryItemId && $sku) {
                throw new \Exception("Could not find matching stock for item: " . ($sku ?: ($item['item_name'] ?? 'Unknown Item')));
            }
        }

        foreach ($requiredQuantities as $req) {
            if (!$req['sku']) continue;

            $stockQuery = StockEntryItem::where('stock_type', 'finished_goods')
                ->whereNull('deleted_at')
                ->where(function ($q) use ($req) {
                    $q->where('sku', $req['sku'])->orWhere('barcode', $req['sku']);
                });
            if ($req['size']) {
                $stockQuery->where('size', $req['size']);
            }
            if ($req['art_no']) {
                $stockQuery->where('art_no', $req['art_no']);
            }
            if ($req['finished_item_code']) {
                $stockQuery->where('finished_item_code', $req['finished_item_code']);
            }
            if ($req['art_no']) {
                $stockQuery->where('art_no', $req['art_no']);
            }
            if ($req['sleeve_type']) {
                $sleeveUpper = strtoupper(trim($req['sleeve_type']));
                if ($sleeveUpper === 'FS' || $sleeveUpper === 'F/S' || $sleeveUpper === 'FULL') {
                    $sleeveDbValues = ['FULL', 'Full', 'F/S', 'Fs', 'Full Sleeve', 'F/S Sleeve', 'FS'];
                } elseif ($sleeveUpper === 'HS' || $sleeveUpper === 'H/S' || $sleeveUpper === 'HALF') {
                    $sleeveDbValues = ['HALF', 'Half', 'H/S', 'Hs', 'Half Sleeve', 'H/S Sleeve', 'HS'];
                } else {
                    $sleeveDbValues = [$req['sleeve_type']];
                }
                $stockQuery->whereIn('sleeve_type', $sleeveDbValues);
            }
            
            $totalIn = (clone $stockQuery)->sum('qty_in');
            $totalOut = (clone $stockQuery)->sum('qty_out');
            
            $alreadyDeducted = 0;
            if ($invoiceId) {
                foreach ($existingInvoiceItems as $ei) {
                    if ($ei->sku === $req['sku'] && $ei->size == $req['size']) {
                        $alreadyDeducted += $ei->quantity;
                    }
                }
            }
            $effectiveAvailable = ($totalIn - $totalOut) + $alreadyDeducted;

            if ($effectiveAvailable < $req['qty']) {
                \Illuminate\Support\Facades\Log::error("Validation Failed:", [
                    'sku' => $req['sku'],
                    'req_qty' => $req['qty'],
                    'totalIn' => $totalIn,
                    'totalOut' => $totalOut,
                    'alreadyDeducted' => $alreadyDeducted,
                    'existing_skus' => collect($existingInvoiceItems)->pluck('sku')->toArray()
                ]);
                throw new \Exception("Insufficient stock for " . $req['display'] . " (Available: " . $effectiveAvailable . ", Required: " . $req['qty'] . ")");
            }
        }
    }

    private function createReturnStockEntry($item, $returnQty, $invNo)
    {
        if ($returnQty <= 0 || !$item->stock_entry_item_id) return;

        $originalStockItem = \App\Models\StockEntryItem::find($item->stock_entry_item_id);
        if (!$originalStockItem) return;

        $stockEntry = \App\Models\StockEntry::create([
            'stock_entry_no' => 'SR-' . time() . '-' . rand(10, 99),
            'stock_date' => date('Y-m-d'),
            'entry_type' => 'Finished Goods',
            'remarks' => 'Sales Return for Invoice ' . $invNo,
            'status' => 'Posted',
            'created_by' => auth()->id() ?? 1,
        ]);

        $newItemData = $originalStockItem->toArray();
        unset($newItemData['id']);
        unset($newItemData['created_at']);
        unset($newItemData['updated_at']);
        unset($newItemData['deleted_at']);
        
        $newItemData['stock_entry_id'] = $stockEntry->id;
        $newItemData['qty_in'] = $returnQty;
        $newItemData['qty_out'] = 0;

        \App\Models\StockEntryItem::create($newItemData);
    }

    private function adjustStock($salesInvoiceId, $revert = false, $oldQuantities = [])
    {
        $items = SalesInvoiceItem::where('sales_invoice_id', $salesInvoiceId)->get();
        foreach ($items as $item) {
            $stockEntryItemId = $item->stock_entry_item_id;

            if (!$stockEntryItemId && !$revert) {
                $matchedStockItem = null;
                if (!empty($item->sku)) {
                    $stockQuery = StockEntryItem::where('stock_type', 'finished_goods')
                        ->whereNull('deleted_at')
                        ->where(function ($q) use ($item) {
                            $q->where('sku', $item->sku)
                              ->orWhere('barcode', $item->sku);
                        });

                    if (!empty($item->size)) {
                        $stockQuery->where('size', $item->size);
                    }
                    if (!empty($item->art_no)) {
                        $stockQuery->where('art_no', $item->art_no);
                    }

                    if (!empty($item->sleeve_type)) {
                        $sleeveUpper = strtoupper(trim($item->sleeve_type));
                        if ($sleeveUpper === 'FS' || $sleeveUpper === 'F/S' || $sleeveUpper === 'FULL') {
                            $sleeveDbValues = ['FULL', 'Full', 'F/S', 'Fs', 'Full Sleeve', 'F/S Sleeve', 'FS'];
                        } elseif ($sleeveUpper === 'HS' || $sleeveUpper === 'H/S' || $sleeveUpper === 'HALF') {
                            $sleeveDbValues = ['HALF', 'Half', 'H/S', 'Hs', 'Half Sleeve', 'H/S Sleeve', 'HS'];
                        } else {
                            $sleeveDbValues = [$item->sleeve_type];
                        }
                        $stockQuery->whereIn('sleeve_type', $sleeveDbValues);
                    }

                    $matchedStockItem = $stockQuery->orderByRaw('(qty_in - qty_out) > 0 DESC')->orderBy('id', 'desc')->first();
                }

                if (!$matchedStockItem) {
                    $finishedItemCode = $item->item_name ?? $item->art_no;
                    if ($finishedItemCode) {
                        $stockQuery3 = StockEntryItem::where('stock_type', 'finished_goods')
                            ->whereNull('deleted_at')
                            ->where(function ($q) use ($finishedItemCode) {
                                $q->where('finished_item_code', $finishedItemCode)
                                  ->orWhere('art_no', $finishedItemCode);
                            });

                        if (!empty($item->color_id)) {
                            $stockQuery3->where(function ($q) use ($item) {
                                $q->where('color_id', $item->color_id)->orWhereNull('color_id');
                            });
                        }
                        if (!empty($item->size)) {
                            $stockQuery3->where('size', $item->size);
                        }
                        if (!empty($item->sleeve_type)) {
                            $sleeveUpper = strtoupper(trim($item->sleeve_type));
                            if ($sleeveUpper === 'FS' || $sleeveUpper === 'F/S' || $sleeveUpper === 'FULL') {
                                $sleeveDbValues = ['FULL', 'Full', 'F/S', 'Fs', 'Full Sleeve', 'F/S Sleeve', 'FS'];
                            } elseif ($sleeveUpper === 'HS' || $sleeveUpper === 'H/S' || $sleeveUpper === 'HALF') {
                                $sleeveDbValues = ['HALF', 'Half', 'H/S', 'Hs', 'Half Sleeve', 'H/S Sleeve', 'HS'];
                            } else {
                                $sleeveDbValues = [$item->sleeve_type];
                            }
                            $stockQuery3->whereIn('sleeve_type', $sleeveDbValues);
                        }

                        $matchedStockItem = $stockQuery3->orderByRaw('(qty_in - qty_out) > 0 DESC')->orderBy('id', 'desc')->first();
                    }
                }

                if ($matchedStockItem) {
                    $stockEntryItemId = $matchedStockItem->id;
                    $item->update([
                        'stock_entry_item_id' => $stockEntryItemId,
                        'art_no' => $matchedStockItem->art_no ?? $item->art_no
                    ]);
                }
            }

            if ($stockEntryItemId) {
                if ($revert) {
                    $this->sequentialStockRevert($item, $item->quantity);
                } else {
                    if (!empty($oldQuantities) && isset($oldQuantities[$item->id])) {
                        $oldQty = $oldQuantities[$item->id]['quantity'];
                        $delta = $item->quantity - $oldQty;
                        
                        if ($delta > 0) {
                            $this->sequentialStockDeduct($item, $delta);
                        } elseif ($delta < 0) {
                            $returnQty = abs($delta);
                            $this->sequentialStockRevert($item, $returnQty);
                        }
                    } else {
                        $this->sequentialStockDeduct($item, $item->quantity);
                    }
                }
            }
        }
    }

    private function sequentialStockDeduct($item, $quantityToDeduct)
    {
        if ($quantityToDeduct <= 0) return;

        $stockQuery = StockEntryItem::where('stock_type', 'finished_goods')
            ->whereNull('deleted_at')
            ->where(function ($q) use ($item) {
                if (!empty($item->sku)) {
                    $q->where('sku', $item->sku)->orWhere('barcode', $item->sku);
                } else {
                    $code = $item->item_name ?? $item->art_no;
                    $q->where('finished_item_code', $code)->orWhere('art_no', $code);
                }
            });

        if ($item->stock_entry_item_id) {
            $finishedItemCode = \App\Models\StockEntryItem::where('id', $item->stock_entry_item_id)->value('finished_item_code');
            if ($finishedItemCode) {
                $stockQuery->where('finished_item_code', $finishedItemCode);
            }
        }

        if (!empty($item->size)) {
            $stockQuery->where('size', $item->size);
        }

        if (!empty($item->sleeve_type)) {
            $sleeveUpper = strtoupper(trim($item->sleeve_type));
            if ($sleeveUpper === 'FS' || $sleeveUpper === 'F/S' || $sleeveUpper === 'FULL') {
                $sleeveDbValues = ['FULL', 'Full', 'F/S', 'Fs', 'Full Sleeve', 'F/S Sleeve', 'FS'];
            } elseif ($sleeveUpper === 'HS' || $sleeveUpper === 'H/S' || $sleeveUpper === 'HALF') {
                $sleeveDbValues = ['HALF', 'Half', 'H/S', 'Hs', 'Half Sleeve', 'H/S Sleeve', 'HS'];
            } else {
                $sleeveDbValues = [$item->sleeve_type];
            }
            $stockQuery->whereIn('sleeve_type', $sleeveDbValues);
        }

        $availableItems = $stockQuery->orderByRaw('(qty_in - qty_out) DESC')->orderBy('id', 'asc')->get();
        $remaining = (float)$quantityToDeduct;

        foreach ($availableItems as $stItem) {
            if ($remaining <= 0) break;
            
            $balance = (float)$stItem->qty_in - (float)$stItem->qty_out;
            
            if ($balance > 0) {
                $deduct = min($balance, $remaining);
                $stItem->increment('qty_out', $deduct);
                $remaining -= $deduct;
            }
        }

        if ($remaining > 0) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'stock' => 'Insufficient stock. You are trying to invoice ' . $quantityToDeduct . ' items, but there is not enough available in the warehouse.'
            ]);
        }
    }

    private function sequentialStockRevert($item, $quantityToRevert)
    {
        if ($quantityToRevert <= 0) return;

        $stockQuery = StockEntryItem::where('stock_type', 'finished_goods')
            ->whereNull('deleted_at')
            ->where(function ($q) use ($item) {
                if (!empty($item->sku)) {
                    $q->where('sku', $item->sku)->orWhere('barcode', $item->sku);
                } else {
                    $code = $item->item_name ?? $item->art_no;
                    $q->where('finished_item_code', $code)->orWhere('art_no', $code);
                }
            });

        if ($item->stock_entry_item_id) {
            $finishedItemCode = \App\Models\StockEntryItem::where('id', $item->stock_entry_item_id)->value('finished_item_code');
            if ($finishedItemCode) {
                $stockQuery->where('finished_item_code', $finishedItemCode);
            }
        }

        if (!empty($item->size)) {
            $stockQuery->where('size', $item->size);
        }

        if (!empty($item->sleeve_type)) {
            $sleeveUpper = strtoupper(trim($item->sleeve_type));
            if ($sleeveUpper === 'FS' || $sleeveUpper === 'F/S' || $sleeveUpper === 'FULL') {
                $sleeveDbValues = ['FULL', 'Full', 'F/S', 'Fs', 'Full Sleeve', 'F/S Sleeve', 'FS'];
            } elseif ($sleeveUpper === 'HS' || $sleeveUpper === 'H/S' || $sleeveUpper === 'HALF') {
                $sleeveDbValues = ['HALF', 'Half', 'H/S', 'Hs', 'Half Sleeve', 'H/S Sleeve', 'HS'];
            } else {
                $sleeveDbValues = [$item->sleeve_type];
            }
            $stockQuery->whereIn('sleeve_type', $sleeveDbValues);
        }

        $deductedItems = $stockQuery->where('qty_out', '>', 0)->orderBy('id', 'desc')->get();
        $remaining = (float)$quantityToRevert;

        foreach ($deductedItems as $stItem) {
            if ($remaining <= 0) break;
            
            $out = (float)$stItem->qty_out;
            $revert = min($out, $remaining);
            $stItem->decrement('qty_out', $revert);
            $remaining -= $revert;
        }

        if ($remaining > 0 && $item->stock_entry_item_id) {
            StockEntryItem::where('id', $item->stock_entry_item_id)->decrement('qty_out', $remaining);
        }
    }

    public function report(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view sales-invoice-report')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $query = SalesInvoice::with(['customer', 'brand'])
                ->where(function($q) {
                    $q->whereNotNull('irn')
                      ->orWhere(function($q2) {
                          $q2->whereNull('irn')
                             ->whereHas('customer', function($q3) {
                                 $q3->where('name', 'like', '%CASH%');
                             });
                      });
                })->orderBy('id', 'desc');

            if ($request->customer_id) {
                $query->where('customer_id', $request->customer_id);
            }
            if ($request->brand_id) {
                $query->where('brand_id', $request->brand_id);
            }
            if ($request->inv_no) {
                $query->where('inv_no', $request->inv_no); 
            }
            if ($request->inv_date_range) {
                $dates = explode(' to ', $request->inv_date_range);
                if (count($dates) == 2) {
                    $startDate = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                    $query->whereBetween('inv_date', [$startDate, $endDate]);
                } elseif (count($dates) == 1) {
                    $startDate = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $query->whereDate('inv_date', $startDate);
                }
            }

            $invoices = $query->get();
            $data = [];
            $count = 1;

            foreach ($invoices as $inv) {
                $data[] = [
                    'id' => $inv->id,
                    'DT_RowIndex' => $count++,
                    'inv_no' => $inv->inv_no,
                    'inv_date' => $inv->inv_date ? $inv->inv_date->format('d-m-Y') : 'N/A',
                    'customer_name' => $inv->customer ? $inv->customer->name : 'N/A',
                    'brand_name' => $inv->brand ? $inv->brand->brand_name : 'N/A',
                    'total_qty' => $inv->items()->sum('quantity'),
                    'grand_total' => '₹' . number_format($inv->grand_total, 2),
                ];
            }

            return response()->json(['data' => $data]);
        }

        $customers = Customer::orderBy('name')->get();
        $brands = Brand::orderBy('brand_name')->get();
        return view('sales_invoice.report', compact('customers', 'brands'));
    }

    public function exportReport(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('export sales-invoice-report')) {
            return unauthorizedRedirect();
        }
        $filters = $request->only(['customer_id', 'brand_id', 'inv_date_range', 'inv_no']);
        return Excel::download(new SalesInvoiceReportExport($filters), 'Sales_Invoice_Report.xlsx');
    }

    public function exportItemsReport(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('export sales-invoice-report')) {
            return unauthorizedRedirect();
        }
        $filters = $request->only(['customer_id', 'brand_id', 'inv_date_range', 'inv_no']);
        return Excel::download(new SalesInvoiceItemsExport($filters), 'Sales_Invoice_Items.xlsx');
    }
    private function generateInvoiceNumber($brandId, $date, $ignoreId = null)
    {
        $brand = \App\Models\Brand::find($brandId);
        if (!$brand) {
            return '';
        }

        $brandCode = trim($brand->code);
        if (empty($brandCode)) {
            $brandCode = strtoupper(substr($brand->brand_name, 0, 2));
        }

        // Map core brands to main brand prefixes to share the same running sequence
        if ($brandCode === 'CDC') {
            $brandCode = 'CDS';
        } elseif ($brandCode === 'CBC') {
            $brandCode = 'CB';
        } elseif ($brandCode === 'CFC') {
            $brandCode = 'CF';
        }

        $year = (int)$date->format('Y');
        $month = (int)$date->format('m');
        $startYear = ($month >= 4) ? $year : ($year - 1);
        $endYear = $startYear + 1;
        $financialYear = substr($startYear, -2) . '-' . substr($endYear, -2);

        $maxRunningNo = 0;
        if ($financialYear === '26-27') {
            if ($brandCode === 'CW') {
                $maxRunningNo = 1157;
            } elseif ($brandCode === 'CDS') {
                $maxRunningNo = 735;
            } elseif ($brandCode === 'CB') {
                $maxRunningNo = 506;
            } elseif ($brandCode === 'CD') {
                $maxRunningNo = 179;
            } elseif ($brandCode === 'CF') {
                $maxRunningNo = 1493;
            }
        }

        // Search by the mapped common prefix instead of brand_id to ensure sequences are shared
        $invoices = SalesInvoice::where('inv_no', 'like', "{$brandCode}/%/{$financialYear}")
            ->when($ignoreId, function ($q) use ($ignoreId) {
                $q->where('id', '!=', $ignoreId);
            })
            ->get(['inv_no']);

        foreach ($invoices as $inv) {
            $parts = explode('/', $inv->inv_no);
            if (count($parts) === 3) {
                $runningNo = (int)$parts[1];
                if ($runningNo > $maxRunningNo) {
                    $maxRunningNo = $runningNo;
                }
            }
        }

        $nextRunningNo = $maxRunningNo + 1;
        return "{$brandCode}/{$nextRunningNo}/{$financialYear}";
    }
}

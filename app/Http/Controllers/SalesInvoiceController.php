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


class SalesInvoiceController extends Controller
{
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
                    'grand_total' => '₹' . number_format($inv->grand_total, 2),
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

        if ($request->isMethod('post')) {
            $request->validate([
                'inv_no' => 'required|min:3|max:50|unique:sales_invoices,inv_no,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'inv_date' => 'required|date',
                'so_ids' => 'required|array',
                'so_ids.*' => 'exists:sales_orders,id',
                'customer_id' => 'required|exists:customers,id',
                'delivery_address' => 'required|min:3|max:255|regex:/^[^<>]*$/',
                'remarks' => 'nullable|min:3|max:255|regex:/^[^<>]*$/',
                'invoice_status' => 'required|in:Draft,Unpaid/Credit,Paid,Partially Paid',
                'payment_mode' => 'nullable',
                'items' => 'required|array|min:1',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.rate' => 'nullable|numeric|min:0',
                'items.*.mrp' => 'required|numeric|min:0',
                'no_of_box' => 'nullable|integer|min:1',
                'hsn_sac' => 'required|string|max:50',
                'signature_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'attachment_file' => 'nullable|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            ], [
                '*.required'      => 'This field is required.',
                '*.unique'        => 'This field already exists.',
                '*.exists'        => 'Selected value is invalid.',
                '*.date'          => 'Please enter a valid date.',
                '*.numeric'       => 'This field must be a number.',
                '*.regex'         => 'This field is an invalid format.',
                '*.min'           => 'This field must be at least :min characters.',
                '*.max'           => 'This field must be at most :max characters.',
                'items.*.quantity.required' => 'This field is required.',
                'items.*.rate.nullable' => 'This field is optional.',
                'extra_input' => 'nullable|min:3|max:100',
            ]);
            if ($request->invoice_status === 'Paid') {
                $referenceId = $id ?? null;

                if ($referenceId) {
                    $totalPaid = DB::table('payments')->where('reference_id', $referenceId)->where('reference_type', 'Customer Collection')->whereNull('deleted_at')->sum('amount');
                } else {
                    $totalPaid = 0;
                }

                $grandTotal = (float) $request->grand_total;
                $balance = $grandTotal - (float) $totalPaid;

                if ($totalPaid <= 0) {
                    return back()->withInput()->withErrors([
                        'invoice_status' => 'Cannot set status to Paid. No payments found for this invoice.',
                    ]);
                }

                if ($balance > 0) {
                    return back()->withInput()->withErrors([
                        'invoice_status' => 'Cannot set status to Paid. Outstanding balance of ' . number_format($balance, 2) . ' still remaining. Total Invoice: ' . number_format($grandTotal, 2) . ', Total Paid: ' . number_format($totalPaid, 2),
                    ]);
                }
            }
            DB::beginTransaction();
            try {
                $invoiceData = $request->only([
                    'inv_no', 'customer_id', 'store_id', 'agent_id', 'delivery_address', 'remarks',
                    'invoice_status', 'payment_mode', 'extra_input', 'due_date',
                    'notes', 'transporter_name', 'transporter_id', 'transport_mode',
                    'vehicle_no', 'veh_type', 'transport_distance', 'tran_doc_no', 'tran_doc_date',
                    'lr_no', 'no_of_box', 'hsn_sac', 'sub_total', 'discount_percent', 'discount', 
                    'commission_percent', 'commission_amount', 'total', 'other_state',
                    'tax_amount', 'igst_percent', 'igst', 'cgst_percent', 'cgst', 'sgst_percent', 'sgst',
                    'other_charges', 'round_off_type', 'round_off',
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

                if ($id) {
                    $invoice = SalesInvoice::with('items')->findOrFail($id);
                    $activeStatuses = ['Paid', 'Partially Paid', 'Unpaid/Credit'];
                    $invoiceData['received_amount'] = (float)$invoice->received_amount + (float)$request->received_amount;
                    $invoice->update($invoiceData);                     
                    $itemIds = collect($request->items)->pluck('id')->filter()->toArray();
                    $invoice->items()->whereNotIn('id', $itemIds)->forceDelete();
                } else {
                    $invoiceData['received_amount'] = (float)$request->received_amount;
                    $invoice = SalesInvoice::create($invoiceData);
                }
                $invoiceId = $invoice->id;
                foreach ($request->items as $item) {
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
                            'api_color' => $item['api_color'] ?? null,
                            'sleeve_type' => $item['sleeve_type'] ?? null,
                            'stock_entry_item_id' => !empty($item['stock_entry_item_id']) ? $item['stock_entry_item_id'] : null,
                        ]
                    );
                }

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

        $nextInvNumber = '';
        if (!$id) {
            $lastInv = SalesInvoice::orderBy('id', 'desc')->first();
            $nextInvNumber = 'SV-' . str_pad(($lastInv ? $lastInv->id + 1 : 1), 4, '0', STR_PAD_LEFT);
        }

        if ($invoice) {
            $soIds = $invoice->so_ids ? json_decode($invoice->so_ids, true) : ($invoice->so_id ? [$invoice->so_id] : []);
            $saleOrders = SalesOrder::whereIn('id', $soIds)->orderBy('id', 'desc')->get();
        } else {
            $saleOrders = collect(); 
        }

        return view('sales_invoice.add', compact('invoice', 'customers', 'saleOrders', 'brandCategories', 'uoms', 'nextInvNumber', 'stores', 'sales_agent'));
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
                $soItemOrdered[$so->id][$item->stock_entry_item_id] = $item->qty;
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
                $itemId = $invItem->stock_entry_item_id;
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
                $invoicedQty += ($soItemInvoiced[$so->id][$item->stock_entry_item_id] ?? 0);
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
            'customer'
        ])->whereIn('id', $soIds)->get();

        if ($saleOrders->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Sale Orders not found']);
        }

        $customerId = $saleOrders->first()->customer_id;
        $currentInvoiceId = $request->invoice_id ?? null;
        $soItemInvoiced = $this->getCustomerPendingItems($customerId, $currentInvoiceId);

        $allItems = collect();
        $billingAddress = '';
        $shippingAddress = '';
        $transporterName = '';
        $transportModeId = null;

        foreach ($saleOrders as $so) {
            if (empty($billingAddress) && $so->customer) {
                $c = $so->customer;
                $billingAddress = implode(', ', array_filter([$c->address_line_1, $c->address_line_2, $c->address_line_3, $c->city, $c->state, $c->pincode]));
                $shippingAddress = $so->shipping_address;
                $transporterName = $so->transporter_name;
                $transportModeId = $so->transport_mode_id;
            }

            foreach ($so->items as $item) {
                $invoicedQty = $soItemInvoiced[$so->id][$item->stock_entry_item_id] ?? 0;
                $pendingQty = max(0, $item->qty - $invoicedQty);
                
                if ($pendingQty > 0) {
                    $brandName = '';
                    $itemName = '';
                    $sleeveType = is_array($item->sleeve) ? ($item->sleeve[0] ?? '') : $item->sleeve;

                    if ($item->item) {
                        if ($item->item->brand) $brandName = $item->item->brand->brand_name;
                        elseif ($item->brandCategory) $brandName = $item->brandCategory->name;
                        
                        if ($item->item->style) $itemName = $item->item->style->style_name;
                        else $itemName = $item->item->name;
                    } elseif ($item->stockEntryItem) {
                        if ($item->stockEntryItem->item) {
                            $seItem = $item->stockEntryItem->item;
                            if ($seItem->brand) $brandName = $seItem->brand->brand_name;
                            elseif ($seItem->brandCategory) $brandName = $seItem->brandCategory->name;

                            if ($seItem->style) $itemName = $seItem->style->style_name;
                            else $itemName = $seItem->name;
                        } else {
                            $brandName = $item->stockEntryItem->finished_item_code;
                        }
                        if (empty($sleeveType)) $sleeveType = $item->stockEntryItem->sleeve_type;
                    } elseif ($item->brandCategory) {
                        $brandName = $item->brandCategory->name;
                    }

                    $stockQty = 0;
                    if ($item->stock_entry_item_id) {
                        $stockQty = DB::table('stock_entry_items')
                            ->where('id', $item->stock_entry_item_id)
                            ->whereNull('deleted_at')
                            ->value(DB::raw('qty_in - COALESCE(qty_out, 0)')) ?? 0;
                    }

                    $itemData = [
                        'brand_id' => $item->brand_cat_id,
                        'brand_name' => $brandName ?: '',
                        'item_id' => $item->item_id,
                        'item_name' => $itemName ?: ($item->item_name ?? ''),
                        'item_code' => $item->item ? $item->item->code : '',
                        'uom_id' => $item->uom_id,
                        'uom_code' => $item->uom_id ?: '',
                        'qty' => $pendingQty, 
                        'stock_qty' => (float)$stockQty,
                        'rate' => $item->rate,
                        'mrp' => $item->mrp ?? 0,
                        'amount' => $item->amount,
                        'art_no' => $item->art_no,
                        'hsn_sac' => $item->hsn_sac ?? null,
                        'sku' => $item->sku,
                        'size_id' => $item->size_id,
                        'size_name' => $item->size ? $item->size->size : '',
                        'color_id' => $item->color_id,
                        'color_name' => $item->color ? $item->color->color_name : '',
                        'api_color' => $item->api_color,
                        'sleeve' => $sleeveType ?: '',
                        'stock_entry_item_id' => $item->stock_entry_item_id,
                    ];

                    $existingItemKey = $allItems->search(function($i) use ($item) {
                        if (!empty($item->stock_entry_item_id) && !empty($i['stock_entry_item_id'])) {
                            return $i['stock_entry_item_id'] == $item->stock_entry_item_id && $i['rate'] == $item->rate;
                        } else {
                            return $i['sku'] == $item->sku && $i['rate'] == $item->rate && $i['size_id'] == $item->size_id && $i['color_id'] == $item->color_id && $i['art_no'] == $item->art_no;
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

        $firstSo = $saleOrders->first();

        $totalSubTotal = 0;
        $totalDiscountAmount = 0;
        foreach ($saleOrders as $so) {
            $totalSubTotal += $so->sub_total ?? 0;
            $totalDiscountAmount += $so->discount_amount ?? 0;
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
            'customer'
        ])->find($id);
        if (!$so) {
            return response()->json(['success' => false, 'message' => 'Sale Order not found']);
        }

        $items = $so->items->map(function($item) {
            $brandName = '';
            $itemName = '';
            $sleeveType = is_array($item->sleeve) ? ($item->sleeve[0] ?? '') : $item->sleeve;

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
            elseif ($item->stockEntryItem) {
                if ($item->stockEntryItem->item) {
                    $seItem = $item->stockEntryItem->item;
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
                    $brandName = $item->stockEntryItem->finished_item_code;
                }
                
                if (empty($sleeveType)) {
                    $sleeveType = $item->stockEntryItem->sleeve_type;
                }
            }
            elseif ($item->brandCategory) {
                $brandName = $item->brandCategory->name;
            }

            $stockQty = 0;
            if ($item->stock_entry_item_id) {
                $stockQty = DB::table('stock_entry_items')
                    ->where('id', $item->stock_entry_item_id)
                    ->whereNull('deleted_at')
                    ->value(DB::raw('qty_in - COALESCE(qty_out, 0)')) ?? 0;
            }

            return [
                'brand_id' => $item->brand_cat_id,
                'brand_name' => $brandName ?: '',
                'item_id' => $item->item_id,
                'item_name' => $itemName ?: ($item->item_name ?? ''),
                'item_code' => $item->item ? $item->item->code : '',
                'uom_id' => $item->uom_id,
                'uom_code' => $item->uom_id ?: '',
                'qty' => $item->qty,
                'stock_qty' => (float)$stockQty,
                'rate' => $item->rate,
                'mrp' => $item->mrp ?? 0,
                'amount' => $item->amount,
                'art_no' => $item->art_no,
                'hsn_sac' => $item->hsn_sac ?? null,
                'sku' => $item->sku,
                'size_id' => $item->size_id,
                'size_name' => $item->size ? $item->size->size : '',
                'color_id' => $item->color_id,
                'color_name' => $item->color ? $item->color->color_name : '',
                'api_color' => $item->api_color,
                'sleeve' => $sleeveType ?: '',
                'stock_entry_item_id' => $item->stock_entry_item_id,
            ];
        });

        $billingAddress = '';
        if ($so->customer) {
            $c = $so->customer;
            $billingAddress = implode(', ', array_filter([$c->address_line_1, $c->address_line_2, $c->address_line_3, $c->city, $c->state, $c->pincode]));
        }

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
        if (in_array($newStatus, ['Paid', 'Partially Paid', 'Unpaid/Credit'])) {
            $totalPaid = DB::table('payments')->where('reference_id', $id)->where('reference_type', 'Customer Collection')->whereNull('deleted_at')->sum('amount');
            $balance = $invoice->grand_total - $totalPaid;

            if ($totalPaid <= 0) {
                $newStatus = 'Unpaid/Credit';
            } elseif ($balance <= 0) {
                $newStatus = 'Paid';
            } else {
                $newStatus = 'Partially Paid';
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
            $taxSummary[$hsn]['taxable_value'] += $item->amount;
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
            $taxSummary[$hsn]['taxable_value'] += $item->amount;
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
            $invoice->refresh();
            $newData = $invoice->toArray();
            addLog('cancel_einvoice', 'Sales Invoice E-Invoice Cancelled', 'sales_invoices', $id, $oldData, $newData);

            if (!empty($oldData['eway_bill_no'])) {
                $result['message'] = 'E-Way Bill and E-Invoice cancelled successfully.';
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
            $lastInv = SalesInvoice::orderBy('id', 'desc')->first();
            $nextInvNumber = 'SV-' . str_pad(($lastInv ? $lastInv->id + 1 : 1), 4, '0', STR_PAD_LEFT);

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
}

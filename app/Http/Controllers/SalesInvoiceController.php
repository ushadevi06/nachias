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

                $action = '<div class="button-box">
                    <a href="' . url('sales_invoices/view/' . $inv->id) . '" class="btn btn-view" title="View"><i class="icon-base ri ri-eye-line"></i></a>
                    <a href="' . url('sales_invoices/add/' . $inv->id) . '" class="btn btn-edit" title="Edit"><i class="icon-base ri ri-edit-box-line"></i></a>
                </div>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'inv_no' => $inv->inv_no,
                    'inv_date' => $inv->inv_date->format('d-m-Y'),
                    'customer_name' => ($inv->customer ? $inv->customer->name : 'N/A') . ($inv->customer ? ' <span class="mini-title">(' . $inv->customer->code . ')</span>' : ''),
                    'so_no' => $inv->salesOrder ? $inv->salesOrder->so_no : 'N/A',
                    'total_items' => $inv->items->count(),
                    'grand_total' => '₹' . number_format($inv->grand_total, 2),
                    'status' => $statusDropdown,
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
                'so_id' => 'required|exists:sales_orders,id',
                'customer_id' => 'required|exists:customers,id',
                'delivery_address' => 'required|min:3|max:255|regex:/^[^<>]*$/',
                'remarks' => 'nullable|min:3|max:255|regex:/^[^<>]*$/',
                'invoice_status' => 'required|in:Draft,Unpaid/Credit,Paid,Partially Paid',
                'payment_mode' => 'nullable',
                'items' => 'required|array|min:1',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.rate' => 'nullable|numeric|min:0',
                'items.*.mrp' => 'required|numeric|min:0',
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

            DB::beginTransaction();
            try {
                $invoiceData = $request->only([
                    'inv_no', 'so_id', 'customer_id', 'store_id', 'agent_id', 'delivery_address', 'remarks',
                    'invoice_status', 'payment_mode', 'extra_input', 'due_date',
                    'notes', 'transporter_name', 'lr_no', 'sub_total', 'discount_percent', 'discount', 
                    'commission_percent', 'commission_amount', 'total', 'other_state',
                    'tax_amount', 'igst_percent', 'igst', 'cgst_percent', 'cgst', 'sgst_percent', 'sgst',
                    'other_charges', 'round_off_type', 'round_off',
                    'grand_total', 'due_amount'
                ]);

                $invoiceData['inv_date'] = Carbon::createFromFormat('d-m-Y', $request->inv_date)->format('Y-m-d');
                if ($request->due_date) {
                    $invoiceData['due_date'] = Carbon::createFromFormat('d-m-Y', $request->due_date)->format('Y-m-d');
                }
                $invoiceData['show_fields'] = $request->show_fields ?? [];
                $invoiceData['other_state'] = $request->other_state == 'yes';


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
                    if (in_array($invoice->invoice_status, $activeStatuses)) {
                        $this->revertStockDeduction($invoice);
                    }
                    $invoiceData['received_amount'] = (float)$invoice->received_amount + (float)$request->received_amount;
                    $invoice->update($invoiceData);
                    
                    $itemIds = collect($request->items)->pluck('id')->filter()->toArray();
                    $invoice->items()->whereNotIn('id', $itemIds)->forceDelete();
                } else {
                    $invoiceData['received_amount'] = (float)$request->received_amount;
                    $invoice = SalesInvoice::create($invoiceData);
                }

                foreach ($request->items as $item) {
                    SalesInvoiceItem::updateOrCreate(
                        ['id' => $item['id'] ?? null],
                        [
                            'sales_invoice_id' => $invoice->id,
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
                            'sleeve_type' => $item['sleeve_type'] ?? null,
                            'stock_entry_item_id' => !empty($item['stock_entry_item_id']) ? $item['stock_entry_item_id'] : null,
                        ]
                    );
                }

                $activeStatuses = ['Paid', 'Partially Paid', 'Unpaid/Credit'];
                if (in_array($invoice->invoice_status, $activeStatuses)) {
                    $invoice->load('items');
                    $this->applyStockDeduction($invoice);
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
        $customers = Customer::active()->orderBy('id','desc')->get();

        $usedSoIds = SalesInvoice::whereNotNull('so_id')
            ->when($id, function($query) use ($id) {
                return $query->where('id', '!=', $id);
            })->pluck('so_id')->toArray();

        $saleOrders = SalesOrder::where('status', 'Approved')->whereNotIn('id', $usedSoIds)->orderBy('id', 'desc')->get();
        $brandCategories = BrandCategory::active()->get();
        $uoms = Uom::active()->get();
        $stores = StoreType::where('status', 'Active')->orderBy('id', 'desc')->get();
        $sales_agent = SalesAgent::where('status', 'Active')->orderBy('id', 'desc')->get();
        
        $nextInvNumber = '';
        if (!$id) {
            $lastInv = SalesInvoice::orderBy('id', 'desc')->first();
            $nextInvNumber = 'SINV-' . str_pad(($lastInv ? $lastInv->id + 1 : 1), 4, '0', STR_PAD_LEFT);
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

            // Try to get from direct item relation
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
            // Fallback to stockEntryItem if direct item is missing
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
                    // Ultimate fallback: finished_item_code
                    $brandName = $item->stockEntryItem->finished_item_code;
                }
                
                if (empty($sleeveType)) {
                    $sleeveType = $item->stockEntryItem->sleeve_type;
                }
            }
            elseif ($item->brandCategory) {
                $brandName = $item->brandCategory->name;
            }

            return [
                'brand_id' => $item->brand_cat_id,
                'brand_name' => $brandName ?: '',
                'item_id' => $item->item_id,
                'item_name' => $itemName ?: '',
                'item_code' => $item->item ? $item->item->code : '',
                'uom_id' => $item->uom_id,
                'uom_code' => $item->uom_id ?: '',
                'qty' => $item->qty,
                'rate' => $item->rate,
                'mrp' => $item->mrp ?? 0,
                'amount' => $item->amount,
                'art_no' => $item->art_no,
                'sku' => $item->sku,
                'size_id' => $item->size_id,
                'size_name' => $item->size ? $item->size->size : '',
                'color_id' => $item->color_id,
                'color_name' => $item->color ? $item->color->color_name : '',
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
            'items' => $items
        ]);
    }
    
    public function updateStatus(Request $request, $id)
    {
        $invoice = SalesInvoice::with('items')->findOrFail($id);
        $oldStatus = $invoice->invoice_status;
        $newStatus = $request->status;
        $activeStatuses = ['Paid', 'Partially Paid', 'Unpaid/Credit'];

        $oldData = $invoice->toArray();
        $invoice->invoice_status = $newStatus;
        $invoice->save();
        $newData = $invoice->fresh()->toArray();
        addLog('update_status', 'Sales Invoice Status', 'sales_invoices', $id, $oldData, $newData);

        $wasActive = in_array($oldStatus, $activeStatuses);
        $isNowActive = in_array($newStatus, $activeStatuses);

        if (!$wasActive && $isNowActive) {
            $this->applyStockDeduction($invoice);
        } elseif ($wasActive && !$isNowActive) {
            $this->revertStockDeduction($invoice);
        }

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    public function destroy($id)
    {
        $invoice = SalesInvoice::with('items')->findOrFail($id);
        
        DB::beginTransaction();
        try {
            $activeStatuses = ['Paid', 'Partially Paid', 'Unpaid/Credit'];
            if (in_array($invoice->invoice_status, $activeStatuses)) {
                $this->revertStockDeduction($invoice);
            }

            addLog('delete', 'Sales Invoice', 'sales_invoices', $id, $invoice->toArray(), null);
            $invoice->delete();
            
            DB::commit();
            return redirect('sales_invoices')->with('success', 'Sales Invoice deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete invoice: ' . $e->getMessage());
        }
    }

    private function applyStockDeduction(SalesInvoice $invoice): void
    {
        foreach ($invoice->items as $item) {
            if (empty($item->stock_entry_item_id)) continue;
            StockEntryItem::where('id', $item->stock_entry_item_id)->increment('qty_out', $item->quantity);
        }
    }

    private function revertStockDeduction(SalesInvoice $invoice): void
    {
        foreach ($invoice->items as $item) {
            if (empty($item->stock_entry_item_id)) continue;
            StockEntryItem::where('id', $item->stock_entry_item_id)->where('qty_out', '>=', $item->quantity)->decrement('qty_out', $item->quantity);
        }
    }

    public function downloadPdf($id)
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
        $setting = Setting::with(['state', 'city'])->first();
        $is_print = true;
        return view('sales_invoice.sticker', compact('invoice', 'totalPcs', 'setting', 'is_print'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Season;
use App\Models\Customer;
use App\Models\StoreType;
use App\Models\SalesAgent;
use App\Models\ShippingMethod;
use App\Models\TransportMode;
use App\Models\ServiceProvider;
use App\Models\BrandCategory;
use App\Models\Item;
use App\Models\Color;
use App\Models\Uom;
use App\Models\SizeRatio;
use App\Models\Setting;
use App\Models\Zone;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesOrderController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view sales-order')) {
            return unauthorizedRedirect();
        }

        if ($request->ajax()) {
            $query = SalesOrder::with(['customer', 'salesAgent'])->orderBy('id', 'desc');

            if (!empty($request->customer_id)) {
                $query->where('customer_id', $request->customer_id);
            }

            if (!empty($request->status)) {
                $query->where('status', $request->status);
            }

            if (!empty($request->so_date_range)) {
                $dates = explode(' to ', $request->so_date_range);
                if (count($dates) == 2) {
                    $startDate = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                    $query->whereBetween('so_date', [$startDate, $endDate]);
                }
                elseif (count($dates) == 1 && trim($dates[0]) !== '') {
                    $startDate = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $query->whereDate('so_date', $startDate);
                }
            }

            $salesOrders = $query->get();
            $data = [];
            $count = 1;

            foreach ($salesOrders as $so) {
                $statusOptions = '';
                foreach (['Draft', 'Approved', 'Pending', 'In Production', 'Dispatched', 'Cancelled'] as $status) {
                    $selected = $so->status === $status ? 'selected' : '';
                    $disabled = '';
                    if ($so->status === 'Approved' && $status === 'Draft')
                        $disabled = 'disabled';
                    if ($so->status === 'Dispatched' && in_array($status, ['Draft', 'Approved']))
                        $disabled = 'disabled';
                    if ($so->status === 'Cancelled' && $status !== 'Cancelled')
                        $disabled = 'disabled';
                    $statusOptions .= "<option value=\"{$status}\" {$selected} {$disabled}>{$status}</option>";
                }

                $statusDropdown = '
                <div class="form-floating form-floating-outline">
                    <select class="form-select so-status-change" data-id="' . $so->id . '" ' . ($so->status === 'Cancelled' ? 'disabled' : '') . '>
                        ' . $statusOptions . '
                    </select>
                </div>
                <div class="status_msg_' . $so->id . ' mt-1"></div>';

                $action = '<div class="button-box">';
                if (auth()->id() == 1 || auth()->user()->can('view sales-order')) {
                    $action .= '<a href="' . url('sales_orders/view/' . $so->id) . '" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>';
                }
                if ($so->status == 'Draft') {
                    if (auth()->id() == 1 || auth()->user()->can('edit sales-order')) {
                        $action .= '<a href="' . url('sales_orders/add/' . $so->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                    }
                }
                if (auth()->id() == 1 || auth()->user()->can('delete sales-order')) {
                    $action .= '<a href="' . url('sales_orders/delete/' . $so->id) . '" class="btn btn-delete delete-btn"><i class="icon-base ri ri-delete-bin-line"></i></a>';
                }
                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'so_no' => $so->so_no,
                    'so_date' => $so->so_date->format('d-m-Y'),
                    'customer_name' => $so->customer ? $so->customer->name . ' <span class="mini-title">(' . $so->customer->code . ')</span>' : '-',
                    'customer_po_ref' => $so->customer_po_ref ?? '-',
                    'total_qty' => number_format($so->total_qty, 2),
                    'sales_agent' => $so->salesAgent ? $so->salesAgent->name : '-',
                    'status' => $statusDropdown,
                    'total_amount' => '₹' . number_format($so->total_amount, 2),
                    'action' => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('sales_order.view');
    }

    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit sales-order')) {
                return unauthorizedRedirect();
            }
        }
        else {
            if (auth()->id() != 1 && !auth()->user()->can('create sales-order')) {
                return unauthorizedRedirect();
            }
        }

        $salesOrder = null;
        if ($id) {
            $salesOrder = SalesOrder::with('items')->findOrFail($id);
        }

        if (request()->isMethod('post')) {
            $request = request();

            $rules = [
                'so_no' => 'required|string|min:3|max:50|unique:sales_orders,so_no,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'so_date' => 'required|date',
                'request_date' => 'nullable|date',
                'order_type' => 'required|string|max:50',
                'season_id' => 'nullable|exists:seasons,id',
                'customer_id' => 'required|exists:customers,id',
                'customer_po_ref' => 'nullable|string|min:3|max:50',
                'store_id' => 'required|exists:store_types,id',
                'agent_id' => 'nullable|exists:sales_agents,id',
                'zone_id' => 'nullable|exists:zones,id',
                'shipping_method_id' => 'nullable|exists:shipping_methods,id',
                'transport_mode_id' => 'nullable|exists:transport_modes,id',
                'dispatch_from_id' => 'nullable|exists:service_providers,id',
                'status' => 'required|in:Draft,Approved,Rejected,Pending,In Production,Dispatched,Cancelled',
                'items' => 'required|array|min:1',
                'items.*.brand_cat_id' => 'required|exists:brand_categories,id',
                'items.*.item_id' => 'required|exists:items,id',
                'items.*.color_id' => 'nullable|exists:colors,id',
                'items.*.art_no' => 'required|string|min:5|max:50',
                'items.*.uom_id' => 'required|exists:uoms,id',
                'items.*.size_id' => 'required',
                'items.*.qty' => 'required|numeric|min:0.01',
                'items.*.rate' => 'required|numeric|min:0',
                'items.*.mrp' => 'required|numeric|min:0',
                'commission_percent' => 'nullable|numeric|min:0',
                'commission_amount' => 'nullable|numeric|min:0',
                'discount_percent' => 'nullable|numeric|min:0|max:100',
                'round_off_type' => 'nullable|in:Add,Less',
                'round_off' => 'nullable|numeric|min:0',
                'apply_box_discount' => 'nullable|boolean',
                'attachment' => 'nullable|array',
                'attachment.*' => 'nullable|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:2048',
                'existing_attachments' => 'nullable|array',
                'billing_address' => 'nullable|string|regex:/^[^<>]*$/',
                'shipping_address' => 'nullable|string|regex:/^[^<>]*$/',
                'payment_terms' => 'nullable|string|max:255|regex:/^[^<>]*$/',
                'transporter_name' => 'nullable|string|max:50',
                'freight_type' => 'nullable|in:Paid,To Pay',
                'freight_amount' => 'nullable|numeric|min:0',
                'transport_gst_no' => 'nullable|string|max:50',
                'terms_conditions' => 'nullable|string|regex:/^[^<>]*$/',
            ];

            $messages = [
                '*.required' => 'This field is required.',
                '*.exists' => 'Selected value is invalid.',
                '*.unique' => 'SO Number already exists.',
                '*.date' => 'Please enter a valid date.',
                '*.numeric' => 'This field must be a number.',
                '*.regex' => 'This field is an invalid format.',
                'items.required' => 'At least one item is required.',
                'items.*.brand_cat_id.required' => 'This field is required.',
                'items.*.item_id.required' => 'This field is required.',
                'items.*.uom_id.required' => 'This field is required.',
                'items.*.size_id.required' => 'This field is required.',
                'items.*.qty.required' => 'This field is required.',
                'items.*.rate.required' => 'This field is required.',
                'items.*.art_no.required' => 'This field is required.',
                'items.*.mrp.required' => 'This field is required.',
                'attachment.*.mimes' => 'Upload a valid file (e.g.,.pdf,.doc,.docx,.jpg, .png, .jpeg, .webp).',
                'attachment.*.max' => 'Uploaded file cannot exceed 2MB.',
            ];

            $request->validate($rules, $messages);

            DB::beginTransaction();
            try {
                $taxableAmount = $request->taxable_amount ?? 0;
                $taxAmount = $request->tax_amount ?? 0;
                $roundOffAmount = $request->round_off ?? 0;
                $roundOffType = $request->round_off_type ?? 'Add';
                $finalTotal = $taxableAmount + $taxAmount;
                if ($roundOffType === 'Add') {
                    $finalTotal += $roundOffAmount;
                }
                else {
                    $finalTotal -= $roundOffAmount;
                }

                $soData = [
                    'so_no' => $request->so_no,
                    'so_date' => Carbon::createFromFormat('d-m-Y', $request->so_date)->format('Y-m-d'),
                    'request_date' => $request->request_date ?Carbon::createFromFormat('d-m-Y', $request->request_date)->format('Y-m-d') : null,
                    'order_type' => $request->order_type,
                    'season_id' => $request->season_id,
                    'customer_id' => $request->customer_id,
                    'customer_po_ref' => $request->customer_po_ref,
                    'store_id' => $request->store_id,
                    'agent_id' => $request->agent_id,
                    'zone_id' => $request->zone_id,
                    'shipping_method_id' => $request->shipping_method_id,
                    'transport_mode_id' => $request->transport_mode_id,
                    'dispatch_from_id' => $request->dispatch_from_id,
                    'status' => $request->status,
                    'total_qty' => $request->total_qty ?? 0,
                    'sub_total_qty' => $request->sub_total_qty ?? 0,
                    'commission_percent' => $request->commission_percent ?? 0,
                    'commission_amount' => $request->commission_amount ?? 0,
                    'discount_percent' => $request->discount_percent ?? 0,
                    'discount_amount' => $request->discount_amount ?? 0,
                    'taxable_amount' => $taxableAmount,
                    'other_state' => $request->other_state === 'yes',
                    'igst_percent' => $request->igst_percent ?? 0,
                    'cgst_percent' => $request->cgst_percent ?? 0,
                    'sgst_percent' => $request->sgst_percent ?? 0,
                    'tax_amount' => $taxAmount,
                    'round_off_type' => $roundOffType,
                    'round_off' => $roundOffAmount,
                    'total_amount' => $finalTotal,
                    'internal_remarks' => $request->internal_remarks,
                    'billing_address' => $request->billing_address,
                    'shipping_address' => $request->shipping_address,
                    'payment_terms' => $request->payment_terms,
                    'transporter_name' => $request->transporter_name,
                    'freight_type' => $request->freight_type,
                    'freight_amount' => $request->freight_amount ?? 0,
                    'transport_gst_no' => $request->transport_gst_no,
                    'terms_conditions' => $request->terms_conditions,
                    'apply_box_discount' => $request->apply_box_discount == '1',
                ];

                if ($request->status === 'Approved') {
                    if (!$id || SalesOrder::find($id)->status !== 'Approved') {
                        $soData['approved_by'] = auth()->id();
                        $soData['approved_date'] = now();
                    }
                }
                else {
                    $soData['approved_by'] = null;
                    $soData['approved_date'] = null;
                }

                if ($id) {
                    $salesOrder = SalesOrder::findOrFail($id);
                    $soData['updated_by'] = auth()->id();
                    $salesOrder->update($soData);
                    SalesOrderItem::where('sale_order_id', $id)->forceDelete();
                    $message = 'Sale Order updated successfully';
                }
                else {
                    $soData['created_by'] = auth()->id();
                    $salesOrder = SalesOrder::create($soData);
                    $message = 'Sale Order created successfully';
                }

                $attachments = $request->existing_attachments ?? [];

                if ($request->hasFile('attachment')) {
                    $uploadPath = public_path('uploads/so/' . $salesOrder->id);
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }

                    foreach ($request->file('attachment') as $file) {
                        $fileName = 'attach_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move($uploadPath, $fileName);
                        $attachments[] = $fileName;
                    }
                }

                if ($id) {
                    $oldAttachments = !empty($salesOrder->attachment) ? explode(',', $salesOrder->attachment) : [];
                    foreach ($oldAttachments as $oldFile) {
                        if (!in_array($oldFile, $attachments)) {
                            $filePath = public_path('uploads/so/' . $salesOrder->id . '/' . $oldFile);
                            if (file_exists($filePath)) {
                                @unlink($filePath);
                            }
                        }
                    }
                }

                $salesOrder->update(['attachment' => !empty($attachments) ? implode(',', $attachments) : null]);

                foreach ($request->items as $idx => $item) {
                    $itemModel = Item::find($item['item_id']);
                    if (!$itemModel)
                        continue;

                    $reqSize = $item['size_id'] ?? null;
                    $reqSleeve = is_array($item['sleeve'] ?? null) ? $item['sleeve'][0] : ($item['sleeve'] ?? 'Full');
                    $reqQty = floatval($item['qty'] ?? 1);

                    $stockQuery = DB::table('stock_entry_items')->where('finished_item_code', 'like', $itemModel->code . '%')->where('size', $reqSize)->whereNull('deleted_at');

                    if ($reqSleeve == 'Half') {
                        $stockQuery->where(function ($q) {
                            $q->where('finished_item_code', 'like', '%Half%')->orWhere('finished_item_code', 'like', '%H/S%');
                        });
                    }
                    elseif ($reqSleeve == 'Full') {
                        $stockQuery->where(function ($q) {
                            $q->where('finished_item_code', 'like', '%Full%')->orWhere('finished_item_code', 'like', '%F/S%');
                        });
                    }
                    else {
                        $stockQuery->where('finished_item_code', 'not like', '%Half%')->where('finished_item_code', 'not like', '%H/S%');
                    }

                    $availableStock = $stockQuery->sum('qty_in') - $stockQuery->sum('qty_out');

                    if ($availableStock < $reqQty) {
                        return back()->withInput()->withErrors([
                            "items.$idx.qty" => "Requested qty ({$reqQty}) exceeds available stock ({$availableStock})."
                        ]);
                    }
                }

                foreach ($request->items as $item) {
                    SalesOrderItem::create([
                        'sale_order_id' => $salesOrder->id,
                        'brand_cat_id' => $item['brand_cat_id'] ?? null,
                        'item_id' => $item['item_id'],
                        'color_id' => $item['color_id'] ?? null,
                        'art_no' => $item['art_no'] ?? null,
                        'uom_id' => $item['uom_id'],
                        'size_id' => $item['size_id'] ?? null,
                        'qty' => $item['qty'],
                        'rate' => $item['rate'],
                        'mrp' => $item['mrp'] ?? 0,
                        'amount' => $item['qty'] * $item['rate'],
                        'sleeve' => isset($item['sleeve']) ? (array)$item['sleeve'] : null,
                        'stock_entry_item_id' => !empty($item['stock_entry_item_id']) ? (int)$item['stock_entry_item_id'] : null,
                    ]);
                }

                DB::commit();
                addLog($id ? 'update' : 'create', 'Sale Order', 'sales_orders', $salesOrder->id, null, $salesOrder->toArray());
                return redirect('sales_orders')->with('success', $message);

            }
            catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->withErrors(['error' => 'Failed to save sale order: ' . $e->getMessage()]);
            }
        }

        $seasons = Season::where('status', 'Active')->orderBy('id', 'desc')->get();
        $customers = Customer::where('status', 'Active')->orderBy('id', 'desc')->get();
        $stores = StoreType::where('status', 'Active')->orderBy('id', 'desc')->get();
        $sales_agent = SalesAgent::where('status', 'Active')->orderBy('id', 'desc')->get();
        $brandCategories = BrandCategory::where('status', 'Active')
            ->whereHas('items', function ($q) {
            $q->where('status', 'Active')->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('stock_entry_items')
                        ->whereColumn('stock_entry_items.finished_item_code', 'like', DB::raw("CONCAT(items.code, '%')"))
                        ->whereNull('deleted_at')
                        ->groupBy('finished_item_code')
                        ->havingRaw('SUM(qty_in) - SUM(qty_out) > 0');
                }
                );
            })
            ->orderBy('id', 'desc')->get();
        $itemsQuery = Item::where('status', 'Active')
            ->whereExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('stock_entry_items')
                ->whereColumn('stock_entry_items.finished_item_code', 'like', DB::raw("CONCAT(items.code, '%')"))
                ->whereNull('deleted_at')
                ->groupBy('finished_item_code')
                ->havingRaw('SUM(qty_in) - SUM(qty_out) > 0');
        });
        if ($id && $salesOrder) {
            $existingItemIds = collect($salesOrder->items)->pluck('item_id')->toArray();
            $itemsQuery->orWhereIn('id', $existingItemIds);
        }
        $items = $itemsQuery->orderBy('id', 'desc')->get();
        $colors = Color::where('status', 'Active')->orderBy('id', 'desc')->get();
        $uoms = Uom::where('status', 'Active')->orderBy('id', 'desc')->get();
        $sizes = SizeRatio::where('status', 'Active')->orderBy('id', 'desc')->get();
        $dynamicSizes = DB::table('stock_entry_items')->join('stock_entries', 'stock_entry_items.stock_entry_id', '=', 'stock_entries.id')->where('stock_entries.entry_type', 'Finished Goods')->whereNotNull('stock_entry_items.size')->where('stock_entry_items.size', '!=', '')->whereNull('stock_entry_items.deleted_at')->distinct()->pluck('stock_entry_items.size')->toArray();

        $nextSoNumber = '';
        if (!$id) {
            $setting = Setting::first();
            if ($setting && $setting->so_prefix) {
                $prefix = $setting->so_prefix;
                $lastSo = SalesOrder::where('so_no', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
                if ($lastSo) {
                    $lastNum = intval(substr($lastSo->so_no, strlen($prefix)));
                    $nextSoNumber = $prefix . str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
                }
                else {
                    $nextSoNumber = $prefix . '0001';
                }
            }
        }

        $zones = Zone::where('status', 'Active')->get();
        $shippingMethods = ShippingMethod::where('status', 'Active')->get();
        $transportModes = TransportMode::where('status', 'Active')->get();
        $serviceProviders = ServiceProvider::where('status', 'Active')->get();

        return view('sales_order.add', compact('salesOrder', 'seasons', 'customers', 'stores', 'sales_agent', 'brandCategories', 'items', 'colors', 'uoms', 'sizes', 'dynamicSizes', 'nextSoNumber', 'zones', 'shippingMethods', 'transportModes', 'serviceProviders'));
    }

    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view sales-order')) {
            return unauthorizedRedirect();
        }
        $salesOrder = SalesOrder::with(['customer', 'salesAgent', 'store', 'season', 'items.brandCategory', 'items.item', 'items.color', 'items.uom', 'shippingMethod', 'transportMode', 'dispatchFrom'])->findOrFail($id);
        return view('sales_order.view_details', compact('salesOrder'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete sales-order')) {
            return unauthorizedRedirect();
        }
        $salesOrder = SalesOrder::findOrFail($id);
        if ($salesOrder->attachment) {
            $filePath = public_path('uploads/so/' . $id . '/' . $salesOrder->attachment);
            if (file_exists($filePath))
                unlink($filePath);
        }
        $salesOrder->delete();
        addLog('delete', 'Sale Order', 'sales_orders', $id, $salesOrder->toArray(), null);
        return redirect('sales_orders')->with('success', 'Sale Order deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $salesOrder = SalesOrder::findOrFail($id);
        $salesOrder->status = $request->status;
        $salesOrder->save();
        addLog('update_status', 'Sale Order Status', 'sales_orders', $id, null, $salesOrder->toArray());
        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }
    public function downloadPdf($id)
    {
        $salesOrder = SalesOrder::with([
            'customer.state',
            'customer.city',
            'salesAgent',
            'items.brandCategory',
            'items.item',
            'items.uom',
            'items.size',
            'items.color'
        ])->findOrFail($id);

        $setting = Setting::with(['state', 'city'])->first();

        $taxSummary = [];
        foreach ($salesOrder->items as $item) {
            $hsn = $item->hsn_sac ?: 'N/A';
            if (!isset($taxSummary[$hsn])) {
                $taxSummary[$hsn] = [
                    'hsn' => $hsn,
                    'taxable_value' => 0,
                    'cgst_rate' => $salesOrder->cgst_percent,
                    'cgst_amount' => 0,
                    'sgst_rate' => $salesOrder->sgst_percent,
                    'sgst_amount' => 0,
                    'igst_rate' => $salesOrder->igst_percent,
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

        $totalInWords = numberToWords($salesOrder->total_amount);
        $totalTaxInWords = numberToWords($salesOrder->tax_amount);

        $pdf = Pdf::loadView('sales_order.pdf', compact('salesOrder', 'setting', 'taxSummary', 'totalInWords', 'totalTaxInWords'));
        $pdf->setPaper('A4', 'portrait');

        $safeSoNo = str_replace(['/', '\\'], '_', $salesOrder->so_no);
        return $pdf->stream('Sales_Order_' . $safeSoNo . '.pdf');
    }

    public function print($id)
    {
        $salesOrder = SalesOrder::with([
            'customer.state',
            'customer.city',
            'salesAgent',
            'items.brandCategory',
            'items.item',
            'items.uom',
            'items.size',
            'items.color'
        ])->findOrFail($id);

        $setting = Setting::with(['state', 'city'])->first();

        $taxSummary = [];
        foreach ($salesOrder->items as $item) {
            $hsn = $item->hsn_sac ?: 'N/A';
            if (!isset($taxSummary[$hsn])) {
                $taxSummary[$hsn] = [
                    'hsn' => $hsn,
                    'taxable_value' => 0,
                    'cgst_rate' => $salesOrder->cgst_percent,
                    'cgst_amount' => 0,
                    'sgst_rate' => $salesOrder->sgst_percent,
                    'sgst_amount' => 0,
                    'igst_rate' => $salesOrder->igst_percent,
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

        $totalInWords = numberToWords($salesOrder->total_amount);
        $totalTaxInWords = numberToWords($salesOrder->tax_amount);

        $is_print = true;
        return view('sales_order.pdf', compact('salesOrder', 'setting', 'taxSummary', 'totalInWords', 'totalTaxInWords', 'is_print'));
    }
}

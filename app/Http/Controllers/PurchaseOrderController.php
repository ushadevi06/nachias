<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\SalesAgent;
use App\Models\Supplier;
use App\Models\StoreType;
use App\Models\StoreCategory;
use App\Models\RawMaterial;
use App\Models\Uom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view purchase-order')) {
            return unauthorizedRedirect();
        }
        if ($request->ajax()) {
            $query = PurchaseOrder::with(['salesAgent', 'supplier', 'storeType'])
                ->orderBy('id', 'desc');

            if (!empty($request->status)) {
                $query->where('status', $request->status);
            }

            if (!empty($request->po_date_range)) {
                $dates = explode(' to ', $request->po_date_range);
                if (count($dates) == 2) {
                    $startDate = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('d-m-Y', trim($dates[1]))->endOfDay();
                    $query->whereBetween('po_date', [$startDate, $endDate]);
                } elseif (count($dates) == 1) {
                    $startDate = Carbon::createFromFormat('d-m-Y', trim($dates[0]))->startOfDay();
                    $query->whereDate('po_date', $startDate);
                }
            }

            $purchaseOrders = $query->get();
            $data = [];
            $count = 1;

            foreach ($purchaseOrders as $po) {
                $statusOptions = '';
                foreach (['Draft', 'Approved', 'Dispatched', 'Received'] as $status) {

                    $disabled = '';

                    if ($po->status === 'Approved' && $status === 'Draft') {
                        $disabled = 'disabled';
                    }

                    if ($po->status === 'Dispatched' && in_array($status, ['Draft', 'Approved'])) {
                        $disabled = 'disabled';
                    }

                    if ($po->status === 'Received') {
                        $disabled = 'disabled';
                    }

                    $selected = $po->status === $status ? 'selected' : '';

                    $statusOptions .= "<option value=\"{$status}\" {$selected} {$disabled}>{$status}</option>";
                }


                $statusDropdown = '
                <div class="form-floating form-floating-outline">
                    <select class="form-select po-status-change" data-id="' . $po->id . '" ' . ($po->status === 'Received' ? 'disabled' : '') . '>
                        ' . $statusOptions . '
                    </select>
                </div>
                <div class="status_msg_' . $po->id . ' mt-1"></div>';

                $action = '<div class="button-box">';
                if (auth()->id() == 1 || auth()->user()->can('view purchase-order')) {
                    $action .= '<a href="' . url('purchase_orders/view/' . $po->id) . '" class="btn btn-view"><i class="icon-base ri ri-eye-line"></i></a>';
                }
                if ($po->status == 'Draft') {
                    if (auth()->id() == 1 || auth()->user()->can('edit purchase-order')) {
                        $action .= '<a href="' . url('purchase_orders/add/' . $po->id) . '" class="btn btn-edit">
                        <i class="icon-base ri ri-edit-box-line"></i>
                    </a>';
                    }
                }

                // if (auth()->id() == 1 || auth()->user()->can('delete purchase-order')) {
                //     $action .= '<button class="btn btn-delete" onclick="delete_data(\'' . url('purchase_orders/delete/' . $po->id) . '\')"><i class="icon-base ri ri-delete-bin-line"></i></button>';
                // }
                $action .= '</div>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'po_number' => $po->po_number,
                    'po_date' => $po->po_date->format('d-m-Y'),
                    'supplier_name' => $po->supplier->name . ' <a href="' . url('view_supplier/' . $po->supplier->id) . '" target="_blank"><span class="mini-title">(' . $po->supplier->code . ')</span></a>',
                    'reference_no' => $po->reference_no ?? '-',
                    'due_date' => $po->due_date->format('d-m-Y'),
                    'delivery_location' => $po->storeType->store_type_name ?? '-',
                    'total_qty' => number_format($po->total_qty, 2) . ' ' . ($po->items->first()->uom->uom_code ?? ''),
                    'order_date' => $po->order_date->format('d-m-Y'),
                    'status' => $statusDropdown,
                    'total_amount' => '₹' . number_format($po->total_amount, 2),
                    'action' => $action,
                ];
            }

            return response()->json(['data' => $data]);
        }

        return view('purchase_orders.view');
    }

    public function add($id = null)
    {
        if ($id) {
            if (auth()->id() != 1 && !auth()->user()->can('edit purchase-order')) {
                return unauthorizedRedirect();
            }
        } else {
            if (auth()->id() != 1 && !auth()->user()->can('create purchase-order')) {
                return unauthorizedRedirect();
            }
        }
        $purchaseOrder = null;
        if ($id) {
            $purchaseOrder = PurchaseOrder::with(['items.storeCategory', 'items.rawMaterial', 'items.uom'])->findOrFail($id);
        }
        $purchaseOrder = $purchaseOrder ?? null;

        if (request()->isMethod('post')) {
            $request = request();

            $rules = [
                'po_number' => 'required|string|max:50|unique:purchase_orders,po_number,' . ($id ?? 'NULL') . ',id,deleted_at,NULL',
                'po_date' => 'required|date',
                'sales_agent_id' => 'nullable|exists:sales_agents,id',
                'commission' => 'nullable|numeric|min:0|max:100',
                'supplier_id' => 'required|exists:suppliers,id',
                'reference_no' => 'required|string|max:100', 
                'reference_date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:po_date',
                'store_type_id' => 'required|exists:store_types,id',
                'order_date' => 'required|date',
                'payment_terms' => 'nullable|string|max:1000',
                'status' => 'required|in:Draft,Approved,Dispatched,Received',
                'items' => 'required|array|min:1',
                'items.*.store_category_id' => 'required|exists:store_categories,id',
                'items.*.raw_material_id' => 'required|exists:raw_materials,id',
                'items.*.uom_id' => 'required|exists:uoms,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.art_no' => 'nullable|string|max:100',
                'items.*.rate' => 'required|numeric|min:0',
                'items.*.remarks' => 'nullable|string|max:500',
                'items.*.attached_file' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
                'discount_percent' => 'nullable'
            ];

            $messages = [
                '*.required' => 'This field is required.',
                '*.exists' => 'Selected value is invalid.',
                '*.unique' => 'This field already exists.',
                '*.date' => 'Please enter a valid date.',
                '*.after_or_equal' => 'Due date must be after or equal to PO date.',
                '*.numeric' => 'This field must be a number.',
                '*.min' => 'Value must be at least :min.',
                '*.max' => 'Value must not exceed :max.',
                'items.required' => 'At least one item is required.',
                'items.*.*' => 'This field is required.',
                'items.*.attached_file.image' => 'File must be an image.',
                'items.*.attached_file.mimes' => 'Image must be: jpeg, jpg, png, or webp.',
                'items.*.attached_file.max' => 'Image size must not exceed 2MB.',
            ];

            $validated = $request->validate($rules, $messages);

            DB::beginTransaction();
            try {
                $poData = [
                    'po_number' => $request->po_number,
                    'po_date'        => Carbon::createFromFormat('d-m-Y', $request->po_date)->format('Y-m-d'),
                    'sales_agent_id' => $request->sales_agent_id,
                    'commission' => $request->commission ?? 0,
                    'supplier_id' => $request->supplier_id,
                    'reference_no' => $request->reference_no,
                    'reference_date' => Carbon::createFromFormat('d-m-Y', $request->reference_date)->format('Y-m-d'),
                    'due_date'       => Carbon::createFromFormat('d-m-Y', $request->due_date)->format('Y-m-d'),
                    'store_type_id' => $request->store_type_id,
                    'order_date'     => Carbon::createFromFormat('d-m-Y', $request->order_date)->format('Y-m-d'),
                    'payment_terms' => $request->payment_terms,
                    'status' => $request->status,
                    'total_qty' => $request->total_qty ?? 0,
                    'sub_total' => $request->sub_total ?? 0,
                    'discount_percent' => $request->discount_percent ?? 0,
                    'discount_amount' => $request->discount_amount ?? 0,
                    'taxable_amount' => $request->taxable_amount ?? 0,
                    'other_state' => $request->other_state === 'yes',
                    'igst_percent' => $request->igst_percent ?? 0,
                    'cgst_percent' => $request->cgst_percent ?? 0,
                    'sgst_percent' => $request->sgst_percent ?? 0,
                    'tax_amount' => $request->tax_amount ?? 0,
                    'total_amount' => $request->total_amount ?? 0,
                ];

                if ($id) {
                    $oldData = PurchaseOrder::find($id)->toArray();
                    $purchaseOrder = PurchaseOrder::findOrFail($id);
                    $poData['updated_by'] = auth()->id();
                    $purchaseOrder->update($poData);

                    PurchaseOrderItem::where('purchase_order_id', $id)->forceDelete();

                    $newData = $purchaseOrder->fresh()->toArray();
                    addLog('update', 'Purchase Order', 'purchase_orders', $id, $oldData, $newData);
                    $message = 'Purchase Order updated successfully';
                } else {
                    $poData['created_by'] = auth()->id();
                    $purchaseOrder = PurchaseOrder::create($poData);
                    $newData = $purchaseOrder->toArray();
                    addLog('create', 'Purchase Order', 'purchase_orders', $purchaseOrder->id, null, $newData);
                    $message = 'Purchase Order created successfully';
                }

                foreach ($request->items as $index => $item) {
                    $itemData = [
                        'purchase_order_id' => $purchaseOrder->id,
                        'store_category_id' => $item['store_category_id'],
                        'raw_material_id' => $item['raw_material_id'],
                        'uom_id' => $item['uom_id'],
                        'quantity' => $item['quantity'],
                        'art_no' => $item['art_no'],
                        'rate' => $item['rate'],
                        'amount' => $item['quantity'] * $item['rate'],
                        'remarks' => $item['remarks'],
                    ];

                    if (isset($item['attached_file']) && $request->hasFile("items.{$index}.attached_file")) {
                        $file = $request->file("items.{$index}.attached_file");
                        $fileName = time() . '_' . $index . '_' . $file->getClientOriginalName();

                        $uploadPath = public_path('uploads/purchase_orders');
                        if (!file_exists($uploadPath)) {
                            mkdir($uploadPath, 0755, true);
                        }

                        $file->move($uploadPath, $fileName);
                        $itemData['attached_file'] = $fileName;
                    }

                    PurchaseOrderItem::create($itemData);
                }

                DB::commit();
                return redirect('purchase_orders')->with('success', $message);
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withInput()->withErrors(['error' => 'Failed to save purchase order: ' . $e->getMessage()]);
            }
        }

        $salesAgents = SalesAgent::active()->get();
        $suppliers = Supplier::active()->get();
        $storeTypes = StoreType::get();
        $storeCategories = StoreCategory::active()->get();
        $uoms = Uom::active()->get();

        $nextPoNumber = '';
        if (!$id) {
            $setting = \App\Models\Setting::first();
            if ($setting && $setting->po_prefix) {
                $prefix = $setting->po_prefix;
                $lastPo = PurchaseOrder::where('po_number', 'like', $prefix . '%')
                    ->orderBy('id', 'desc')
                    ->first();
                
                if ($lastPo) {
                    // Extract the numeric part after the prefix
                    $lastNumberStr = substr($lastPo->po_number, strlen($prefix));
                    $lastNumber = intval($lastNumberStr);
                    $nextNumber = str_pad($lastNumber + 1, max(strlen($lastNumberStr), 4), '0', STR_PAD_LEFT);
                } else {
                    $nextNumber = '0001';
                }
                $nextPoNumber = $prefix . $nextNumber;
            }
        }
        return view('purchase_orders.add', compact('purchaseOrder', 'salesAgents', 'suppliers', 'storeTypes', 'storeCategories', 'uoms', 'nextPoNumber'));
    }

    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view purchase-order')) {
            return unauthorizedRedirect();
        }
        $purchaseOrder = PurchaseOrder::with(['salesAgent', 'supplier', 'storeType', 'items.storeCategory', 'items.rawMaterial', 'items.uom'])->findOrFail($id);
        return view('purchase_orders.view_details', compact('purchaseOrder'));
    }

    public function destroy($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('delete purchase-order')) {
            return unauthorizedRedirect();
        }
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $oldData = $purchaseOrder->toArray();

        foreach ($purchaseOrder->items as $item) {
            if ($item->attached_file) {
                $filePath = public_path('uploads/purchase_orders/' . $item->attached_file);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }

        $purchaseOrder->delete();
        addLog('delete', 'Purchase Order', 'purchase_orders', $id, $oldData, null);
        return redirect('purchase_orders')->with('success', 'Purchase Order deleted successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $oldData = $purchaseOrder->toArray();
        $purchaseOrder->status = $request->status;
        $purchaseOrder->save();
        $newData = $purchaseOrder->toArray();
        addLog('update_status', 'Purchase Order Status', 'purchase_orders', $id, $oldData, $newData);
        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

}

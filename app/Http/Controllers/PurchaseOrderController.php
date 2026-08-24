<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseCommissionAgent;
use App\Models\Supplier;
use App\Models\StoreType;
use App\Models\StoreCategory;
use App\Models\RawMaterial;
use App\Models\Uom;
use App\Models\Color;
use App\Models\Style;
use App\Models\Brand;
use App\Models\FabricSize;
use App\Models\FabricType;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
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
            $query = PurchaseOrder::with(['purchaseCommissionAgent', 'supplier', 'storeType'])->orderBy('id', 'desc');

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

            $totalRecords = $query->count();

            if ($request->has('search') && !empty($request->input('search')['value'])) {
                $search = $request->input('search')['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('po_number', 'like', "%{$search}%")
                        ->orWhere('reference_no', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('total_qty', 'like', "%{$search}%")
                        ->orWhere('total_amount', 'like', "%{$search}%")
                        ->orWhere(DB::raw("DATE_FORMAT(po_date, '%d-%m-%Y')"), 'like', "%{$search}%")
                        ->orWhere(DB::raw("DATE_FORMAT(due_date, '%d-%m-%Y')"), 'like', "%{$search}%")
                        ->orWhereHas('supplier', function ($q2) use ($search) {
                            $q2->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        })
                        ->orWhereHas('storeType', function ($q2) use ($search) {
                            $q2->where('store_type_name', 'like', "%{$search}%");
                        });
                });
            }

            $filteredRecords = $query->count();

            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            if ($length != -1) {
                $query->skip($start)->take($length);
            }

            $purchaseOrders = $query->get();
            $data = [];
            $count = $start + 1;

            foreach ($purchaseOrders as $po) {
                $statusOptions = '';
                foreach (['Draft', 'Approved', 'Dispatched', 'Received'] as $status) {

                    $disabled = '';

                    if ($po->status === 'Draft' && in_array($status, ['Dispatched', 'Received'])) {
                        $disabled = 'disabled';
                    }

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
                    <div style="max-width: 145px; white-space: normal !important;">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select po-status-change" data-id="' . $po->id . '" data-previous-status="' . $po->status . '" ' . ($po->status === 'Received' ? 'disabled' : '') . '>
                                ' . $statusOptions . '
                            </select>
                        </div>
                        <div class="status_msg_' . $po->id . '" style="white-space: normal !important;"></div>
                    </div>';


                $action = '<div class="d-inline-block text-nowrap">';

                if ($po->status == 'Draft') {
                    if (auth()->id() == 1 || auth()->user()->can('edit purchase-order')) {
                        $action .= '<a href="' . url('purchase_orders/add/' . $po->id) . '" class="btn btn-edit"><i class="icon-base ri ri-edit-box-line"></i></a>';
                    }
                }

                $action .= '<button class="btn dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="icon-base ri ri-more-2-fill"></i></button>';

                $action .= '<div class="dropdown-menu dropdown-menu-end m-0">';
                if (auth()->id() == 1 || auth()->user()->can('view_details purchase-order')) {
                    $action .= '<a href="' . url('purchase_orders/view/' . $po->id) . '" class="dropdown-item"><i class="icon-base ri ri-eye-line me-2"></i>View</a>';
                }

                $action .= '<div class="dropdown-divider"></div>';
                $action .= '<div class="px-4 py-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input po-self-close-toggle" type="checkbox" data-id="' . $po->id . '" ' . ($po->is_self_closed ? 'checked' : '') . '>
                                    <label class="form-check-label small ms-1">Self Close</label>
                                </div>
                                <div class="self_close_msg_' . $po->id . ' mt-1" style="height: 15px;"></div>
                            </div>';

                $action .= '</div></div>';

                $data[] = [
                    'DT_RowIndex' => $count++,
                    'po_number' => $po->po_number,
                    'po_date' => $po->po_date->format('d-m-Y'),
                    'supplier_name' => $po->supplier->name . ' <a href="' . url('suppliers/view_details/' . $po->supplier->id) . '" target="_blank"><span class="mini-title">(' . $po->supplier->code . ')</span></a>',
                    'reference_no' => $po->reference_no ?? '-',
                    'due_date' => $po->due_date->format('d-m-Y'),
                    'delivery_location' => $po->storeType->store_type_name ?? '-',
                    'total_qty' => number_format($po->total_qty, 2),
                    'status' => $statusDropdown,
                    'total_amount' => '₹' . number_format($po->total_amount, 2),
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
            if ($purchaseOrder->status !== 'Draft') {
                return redirect('purchase_orders')->with('error', 'Only Draft Purchase Orders can be edited.');
            }
        }

        $purchaseOrder = $purchaseOrder ?? null;

        if (request()->isMethod('post')) {
            $request = request();

            $rules = [
                'po_number' => ['required', 'string', 'min:3', 'max:50', 'not_regex:/^0+$/', 'unique:purchase_orders,po_number,' . ($id ?? 'NULL') . ',id,deleted_at,NULL'],
                'po_date' => 'required|date_format:d-m-Y',
                'purchase_commission_agent_id' => [
                    'nullable',
                    'exists:purchase_commission_agents,id',
                    \Illuminate\Validation\Rule::requiredIf(function () use ($request) {
                        return (float) $request->commission > 0;
                    })
                ],
                'commission' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'max:100',
                    \Illuminate\Validation\Rule::requiredIf(function () use ($request) {
                        return !empty($request->purchase_commission_agent_id);
                    })
                ],
                'supplier_id' => 'required|exists:suppliers,id',
                'reference_no' => 'required|string|min:3|max:100',
                'reference_date' => 'required|date_format:d-m-Y',
                'due_date' => 'required|date_format:d-m-Y',
                'store_type_id' => 'required|exists:store_types,id',
                'payment_terms' => 'nullable|string|max:255|regex:/^[^<>]*$/',
                'order_type' => 'required|string|in:Core Order,Repeat Order,New Order,Sample Order,Urgent Order',
                'status' => 'required|in:Draft,Approved,Dispatched,Received',
                'items' => 'required|array|min:1',
                'items.*.store_category_id' => 'required|exists:store_categories,id',
                'items.*.raw_material_id' => 'required|exists:raw_materials,id',
                'items.*.uom_id' => 'required|exists:uoms,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.supplier_design_name' => 'nullable|string|max:50',
                'items.*.rate' => [
                    $request->status === 'Draft' ? 'nullable' : 'required',
                    'numeric',
                    'min:0'
                ],
                'items.*.remarks' => 'nullable|string|max:255',
                'items.*.attached_file' => 'nullable|mimes:jpg,jpeg,png,webp',
                'items.*.color_id' => 'nullable|exists:colors,id',
                'items.*.brand_id' => 'required|exists:brands,id',
                'items.*.fabric_width_id' => 'nullable|exists:fabric_sizes,id',
                'items.*.fabric_type_id' => 'nullable|exists:fabric_types,id',
                'items.*.style_id' => 'required_if:items.*.store_category_id,1|nullable|exists:styles,id',
                'items.*.cgst_percent' => 'nullable|numeric|min:0|max:100',
                'items.*.sgst_percent' => 'nullable|numeric|min:0|max:100',
                'items.*.igst_percent' => 'nullable|numeric|min:0|max:100',
                'cgst_percent' => 'nullable|numeric|min:0|max:100',
                'cgst_amount' => 'nullable|numeric|min:0',
                'sgst_percent' => 'nullable|numeric|min:0|max:100',
                'sgst_amount' => 'nullable|numeric|min:0',
                'igst_percent' => 'nullable|numeric|min:0|max:100',
                'igst_amount' => 'nullable|numeric|min:0',
                'discount_percent' => 'nullable|numeric|min:0|max:100',
                'additional_attachments' => 'nullable|array|max:5',
                'additional_attachments.*' => 'nullable|mimes:pdf,doc,docx,jpg,jpeg,png,webp',
                'existing_additional_attachments' => 'nullable|array',
                'round_off_type' => 'nullable|in:Add,Less',
                'round_off' => 'nullable|numeric|min:0|max:99.99',
            ];

            $messages = [
                '*.required' => 'This field is required.',
                '*.exists' => 'Selected value is invalid.',
                '*.unique' => 'This field already exists.',
                '*.date_format' => 'Please enter a valid date in DD-MM-YYYY format.',
                '*.after_or_equal' => 'Due date must be after or equal to PO date.',
                '*.numeric' => 'This field must be a valid number.',
                'commission.min' => 'Commission cannot be negative. Please enter 0 to 100.',
                'commission.max' => 'Commission percentage cannot exceed 100%.',
                'commission.required_if' => 'Commission is required when Purchase Commission Agent is selected.',
                'purchase_commission_agent_id.required_if' => 'Purchase Commission Agent is required when Commission is greater than 0.',
                'igst_percent.min' => 'IGST cannot be negative. Please enter 0 to 100.',
                'igst_percent.max' => 'IGST percentage cannot exceed 100%.',
                'cgst_percent.min' => 'CGST cannot be negative. Please enter 0 to 100.',
                'cgst_percent.max' => 'CGST percentage cannot exceed 100%.',
                'sgst_percent.min' => 'SGST cannot be negative. Please enter 0 to 100.',
                'sgst_percent.max' => 'SGST percentage cannot exceed 100%.',
                'items.*.igst_percent.min' => 'Item IGST cannot be negative. Please enter 0 to 100.',
                'items.*.igst_percent.max' => 'Item IGST percentage cannot exceed 100%.',
                'items.*.cgst_percent.min' => 'Item CGST cannot be negative. Please enter 0 to 100.',
                'items.*.cgst_percent.max' => 'Item CGST percentage cannot exceed 100%.',
                'items.*.sgst_percent.min' => 'Item SGST cannot be negative. Please enter 0 to 100.',
                'items.*.sgst_percent.max' => 'Item SGST percentage cannot exceed 100%.',
                'discount_percent.min' => 'Discount cannot be negative. Please enter 0 to 100.',
                'discount_percent.max' => 'Discount percentage cannot exceed 100%.',
                'round_off.min' => 'Round off cannot be negative. Please enter 0 or a positive value.',
                'round_off.max' => 'Round off amount cannot exceed 99.99.',
                '*.min' => 'This field must be at least :min.',
                '*.max' => 'This field should not be more than :max.',
                'items.*.style_id.required_if' => 'Please select a Style for the Fabric store category.',
                'items.required' => 'At least one item is required.',
                'items.*.quantity.min' => 'Please enter a valid numeric value greater than or equal to 0.01.',
                'items.*.rate.min' => 'Please enter a valid numeric value greater than or equal to 0.',
                'items.*.store_category_id.required' => 'This field is required.',
                'items.*.brand_id.required' => 'This field is required.',
                'items.*.raw_material_id.required' => 'This field is required.',
                'items.*.uom_id.required' => 'This field is required.',
                'items.*.quantity.required' => 'This field is required.',
                'items.*.rate.required' => 'This field is required.',
                'items.*.attached_file.image' => 'File must be an image.',
                'items.*.attached_file.mimes' => 'Upload a valid file (e.g., .jpg, .png, .jpeg, .webp).',
                'items.*.attached_file.max' => 'Uploaded file cannot exceed 2MB.',
                'regex' => 'This field is an invalid format',
                'not_regex' => 'This field is an invalid format',
                'additional_attachments.max' => 'You can upload a maximum of 5 files.',
                'additional_attachments.*.mimes' => 'Upload a valid file (e.g., .pdf, .doc, .docx, .jpg, .png, .jpeg, .webp).',
                'additional_attachments.*.max' => 'Uploaded file cannot exceed 2MB.',
            ];

            $validated = $request->validate($rules, $messages);

            $subTotal = 0;
            if (is_array($request->items)) {
                foreach ($request->items as $item) {
                    $qty = floatval($item['quantity'] ?? 0);
                    $rate = floatval($item['rate'] ?? 0);
                    $subTotal += ($qty * $rate);
                }
            }

            $discountPercent = floatval($request->discount_percent ?? 0);
            $commissionPercent = floatval($request->commission ?? 0);

            $discountAmount = ($subTotal * $discountPercent) / 100;
            $commissionAmount = ($subTotal * $commissionPercent) / 100;

            $taxableAmountCalculated = $subTotal - $discountAmount - $commissionAmount;

            $taxableAmount = $request->taxable_amount ?? 0;
            $taxAmount = $request->tax_amount ?? 0;
            $totalBeforeRoundOff = round($taxableAmount + $taxAmount, 2);
            $roundOffAmount = $request->round_off ?? 0;
            $roundOffType = $request->round_off_type ?? 'Add';

            $finalTotal = $totalBeforeRoundOff;
            if ($roundOffType === 'Add') {
                $finalTotal += $roundOffAmount;
            } elseif ($roundOffType === 'Less') {
                $finalTotal -= $roundOffAmount;
            }

            if ($request->status === 'Approved' && $finalTotal <= 0) {
                return back()->withInput()->withErrors(['status' => 'Grand total must be greater than 0 to approve this purchase order.']);
            }

            DB::beginTransaction();
            try {
                $taxableAmount = $request->taxable_amount ?? 0;
                $taxAmount = $request->tax_amount ?? 0;

                $totalBeforeRoundOff = round($taxableAmount + $taxAmount, 2);
                $roundOffAmount = $request->round_off ?? 0;
                $roundOffType = $request->round_off_type ?? 'Add';

                $finalTotal = $totalBeforeRoundOff;
                if ($roundOffType === 'Add') {
                    $finalTotal += $roundOffAmount;
                } elseif ($roundOffType === 'Less') {
                    $finalTotal -= $roundOffAmount;
                }

                $poData = [
                    'po_number' => $request->po_number,
                    'po_date' => Carbon::createFromFormat('d-m-Y', $request->po_date)->format('Y-m-d'),
                    'purchase_commission_agent_id' => $request->purchase_commission_agent_id,
                    'commission' => $request->commission ?? 0,
                    'supplier_id' => $request->supplier_id,
                    'reference_no' => $request->reference_no,
                    'reference_date' => Carbon::createFromFormat('d-m-Y', $request->reference_date)->format('Y-m-d'),
                    'due_date' => Carbon::createFromFormat('d-m-Y', $request->due_date)->format('Y-m-d'),
                    'store_type_id' => $request->store_type_id,
                    'payment_terms' => $request->payment_terms,
                    'order_type' => $request->order_type,
                    'status' => $request->status,
                    'total_qty' => $request->total_qty ?? 0,
                    'sub_total' => $request->sub_total ?? 0,
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
                    'is_self_closed' => $request->has('is_self_closed'),
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

                $attachments = $request->existing_additional_attachments ?? [];

                if ($request->hasFile('additional_attachments')) {
                    $uploadPath = public_path('uploads/purchase_orders');
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }

                    foreach ($request->file('additional_attachments') as $file) {
                        if (count($attachments) >= 5)
                            break;

                        $fileName = 'additional_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move($uploadPath, $fileName);
                        $attachments[] = $fileName;
                    }
                }

                if ($id) {
                    $oldAttachments = is_array($purchaseOrder->additional_attachments) ? $purchaseOrder->additional_attachments : ($purchaseOrder->additional_attachments ? [$purchaseOrder->additional_attachments] : []);
                    foreach ($oldAttachments as $oldFile) {
                        if (!in_array($oldFile, $attachments)) {
                            $filePath = public_path('uploads/purchase_orders/' . $oldFile);
                            if (file_exists($filePath)) {
                                @unlink($filePath);
                            }
                        }
                    }
                }

                $purchaseOrder->update(['additional_attachments' => $attachments]);

                foreach ($request->items as $index => $item) {
                    $itemData = [
                        'purchase_order_id' => $purchaseOrder->id,
                        'store_category_id' => $item['store_category_id'],
                        'raw_material_id' => $item['raw_material_id'],
                        'uom_id' => $item['uom_id'],
                        'quantity' => $item['quantity'],
                        'supplier_design_name' => $item['supplier_design_name'] ?? null,
                        'rate' => $item['rate'],
                        'amount' => $item['quantity'] * $item['rate'],
                        'remarks' => $item['remarks'],
                        'color_id' => $item['color_id'] ?? null,
                        'style_id' => $item['style_id'] ?? null,
                        'brand_id' => $item['brand_id'] ?? null,
                        'fabric_width_id' => $item['fabric_width_id'] ?? null,
                        'fabric_type_id' => $item['fabric_type_id'] ?? null,
                        'attached_file' => $item['existing_file'] ?? null,
                        'cgst_percent' => $item['cgst_percent'] ?? 0,
                        'cgst_amount' => $item['cgst_amount'] ?? 0,
                        'sgst_percent' => $item['sgst_percent'] ?? 0,
                        'sgst_amount' => $item['sgst_amount'] ?? 0,
                        'igst_percent' => $item['igst_percent'] ?? 0,
                        'igst_amount' => $item['igst_amount'] ?? 0,
                    ];

                    if ($request->hasFile("items.{$index}.attached_file")) {
                        if ($id && !empty($item->attached_file)) {
                            $oldFilePath = public_path('uploads/purchase_orders/' . $item->attached_file);
                            if (file_exists($oldFilePath)) {
                                @unlink($oldFilePath);
                            }
                        }

                        $file = $request->file("items.{$index}.attached_file");
                        $fileName = time() . '_' . $index . '_' . $file->getClientOriginalName();

                        $uploadPath = public_path('uploads/purchase_orders');
                        if (!file_exists($uploadPath)) {
                            mkdir($uploadPath, 0755, true);
                        }

                        $file->move($uploadPath, $fileName);
                        $filePath = $uploadPath . '/' . $fileName;
                        $this->compressImage($filePath, $filePath, 60);
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

        $purchaseCommissionAgents = PurchaseCommissionAgent::active()->orderBy('id','desc')->get();
        $suppliers = Supplier::active()->orderBy('id','desc')->get();
        $storeTypes = StoreType::active()->orderBy('id','desc')->get();
        $storeCategories = StoreCategory::active()->orderBy('id','desc')->get();
        $uoms = Uom::active()->orderBy('id','desc')->get();
        $colors = Color::active()->orderBy('id','desc')->get();
        $styles = Style::active()->orderBy('id','desc')->get();
        $brands = Brand::active()->orderBy('id','desc')->get();
        $fabricSizes = FabricSize::active()->orderBy('id','desc')->get();
        $fabricTypes = FabricType::active()->orderBy('id','desc')->get();

        $nextPoNumber = '';
        if (!$id) {
            $setting = Setting::first();
            if ($setting && $setting->po_prefix) {
                $prefix = $setting->po_prefix;
                $lastPo = PurchaseOrder::where('po_number', 'like', $prefix . '%')->orderBy('id', 'desc')->first();

                if ($lastPo) {
                    $lastNumberStr = substr($lastPo->po_number, strlen($prefix));
                    $lastNumber = intval($lastNumberStr);
                    $nextNumber = str_pad($lastNumber + 1, max(strlen($lastNumberStr), 4), '0', STR_PAD_LEFT);
                } else {
                    $nextNumber = '0001';
                }
                $nextPoNumber = $prefix . $nextNumber;
            }
        }
        return view('purchase_orders.add', compact('purchaseOrder', 'purchaseCommissionAgents', 'suppliers', 'storeTypes', 'storeCategories', 'uoms', 'colors', 'styles', 'brands', 'fabricSizes', 'fabricTypes', 'nextPoNumber'));
    }

    public function view($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view_details purchase-order')) {
            return unauthorizedRedirect();
        }
        $purchaseOrder = PurchaseOrder::with([
            'purchaseCommissionAgent',
            'supplier',
            'storeType',
            'items.storeCategory',
            'items.rawMaterial',
            'items.uom',
            'items.style',
            'items.color',
            'items.brand',
            'items.fabricWidth',
            'items.fabricType'
        ])->findOrFail($id);
        return view('purchase_orders.view_details', compact('purchaseOrder'));
    }

    public function downloadPdf($id)
    {
        ini_set('memory_limit', '512M');
        if (auth()->id() != 1 && !auth()->user()->can('view purchase-order')) {
            return unauthorizedRedirect();
        }

        $purchaseOrder = PurchaseOrder::with([
            'purchaseCommissionAgent',
            'supplier',
            'storeType',
            'items.storeCategory',
            'items.rawMaterial',
            'items.uom',
            'items.style',
            'items.color',
            'items.brand',
            'items.fabricWidth',
            'items.fabricType'
        ])->findOrFail($id);

        $setting = Setting::first();
        $totalInWords = numberToWords($purchaseOrder->total_amount);

        $pdf = Pdf::loadView('purchase_orders.purchase_order_pdf', compact('purchaseOrder', 'setting', 'totalInWords'));
        return $pdf->stream('PO-' . $purchaseOrder->po_number . '.pdf');
    }

    public function print($id)
    {
        if (auth()->id() != 1 && !auth()->user()->can('view purchase-order')) {
            return unauthorizedRedirect();
        }

        $purchaseOrder = PurchaseOrder::with([
            'purchaseCommissionAgent',
            'supplier',
            'storeType',
            'items.storeCategory',
            'items.rawMaterial',
            'items.uom',
            'items.style',
            'items.color',
            'items.brand',
            'items.fabricWidth',
            'items.fabricType'
        ])->findOrFail($id);

        $setting = Setting::first();
        $totalInWords = numberToWords($purchaseOrder->total_amount);
        $is_print = true;

        return view('purchase_orders.purchase_order_pdf', compact('purchaseOrder', 'setting', 'totalInWords', 'is_print'));
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

        if ($purchaseOrder->additional_attachments) {
            $attachments = is_array($purchaseOrder->additional_attachments) ? $purchaseOrder->additional_attachments : [$purchaseOrder->additional_attachments];
            foreach ($attachments as $attachment) {
                $filePath = public_path('uploads/purchase_orders/' . $attachment);
                if (file_exists($filePath)) {
                    @unlink($filePath);
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
        if ($purchaseOrder->status === 'Draft') {
            $missingRateItems = $purchaseOrder->items()
                ->where(function ($q) {
                    $q->whereNull('rate')
                    ->orWhere('rate', '<=', 0);
                })
                ->get();

            if ($missingRateItems->isNotEmpty()) {
                return response()->json([
                    'success'      => false,
                    'rate_missing' => true,
                    'message'      => 'Please update rate for all items before changing status.',
                ]);
            }

            if ($request->status === 'Approved' && $purchaseOrder->total_amount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Grand total must be greater than 0 to approve this purchase order.'
                ]);
            }

            if (in_array($request->status, ['Dispatched', 'Received'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'A Draft Purchase Order must be Approved before it can be Dispatched or Received.'
                ]);
            }
        }

        $oldData = $purchaseOrder->toArray();
        $purchaseOrder->status = $request->status;
        $purchaseOrder->save();
        $newData = $purchaseOrder->toArray();
        addLog('update_status', 'Purchase Order Status', 'purchase_orders', $id, $oldData, $newData);
        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    public function toggleSelfClose(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $oldData = $purchaseOrder->toArray();
        $purchaseOrder->is_self_closed = $request->is_self_closed == 'true' ? 1 : 0;
        $purchaseOrder->save();
        $newData = $purchaseOrder->toArray();
        addLog('update_self_close', 'Purchase Order Self Close', 'purchase_orders', $id, $oldData, $newData);
        return response()->json(['success' => true, 'message' => 'Self-close status updated successfully']);
    }   
     private function compressImage($sourcePath, $destinationPath, $quality = 60)
    {
        if (!file_exists($sourcePath)) {
            return false;
        }

        $info = getimagesize($sourcePath);

        if (!$info || !isset($info['mime'])) {
            return false;
        }

        $mime = $info['mime'];
        $width = $info[0];
        $height = $info[1];
        $max_width = 1000;
        $max_height = 1000;

        if ($width > $max_width || $height > $max_height) {
            $ratio = $width / $height;
            if ($ratio > 1) {
                $new_width = $max_width;
                $new_height = round($max_width / $ratio);
            } else {
                $new_height = $max_height;
                $new_width = round($max_height * $ratio);
            }
        } else {
            $new_width = $width;
            $new_height = $height;
        }

        $resize = function($src) use ($new_width, $new_height, $width, $height) {
            $dst = imagecreatetruecolor($new_width, $new_height);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            return $dst;
        };

        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = imagecreatefromjpeg($sourcePath);
                if (!$image) {
                    return false;
                }
                if ($width > $max_width || $height > $max_height) {
                    $resized = $resize($image);
                    imagedestroy($image);
                    $image = $resized;
                }
                imagejpeg($image, $destinationPath, $quality);
                imagedestroy($image);
                return true;

            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                if (!$image) {
                    return false;
                }
                if ($width > $max_width || $height > $max_height) {
                    $resized = imagecreatetruecolor($new_width, $new_height);
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                    imagecopyresampled($resized, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                    imagedestroy($image);
                    $image = $resized;
                } else {
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                }
                imagepng($image, $destinationPath, 6);
                imagedestroy($image);
                return true;

            case 'image/webp':
                if (!function_exists('imagecreatefromwebp')) {
                    return false;
                }
                $image = imagecreatefromwebp($sourcePath);
                if (!$image) {
                    return false;
                }
                if ($width > $max_width || $height > $max_height) {
                    $resized = $resize($image);
                    imagedestroy($image);
                    $image = $resized;
                }
                imagewebp($image, $destinationPath, $quality);
                imagedestroy($image);
                return true;
        }

        return false;
    }
}

@extends('layouts.common')
@section('title', ($purchaseOrder ? 'Edit' : 'Add') . ' Purchase Order - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="{{ $purchaseOrder ? url('purchase_orders/add/' . $purchaseOrder->id) : url('purchase_orders/add') }}" method="POST" enctype="multipart/form-data" class="common-form">
                @csrf
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>{{ $purchaseOrder ? 'Edit' : 'Add' }} Purchase Order</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('po_number') is-invalid @enderror" id="po_number" name="po_number" placeholder="Enter PO Number" value="{{ old('po_number', $purchaseOrder->po_number ?? '') }}">
                                    <label for="po_number">PO Number <span class="text-danger">*</span></label>
                                </div>
                                @error('po_number')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control po_date @error('po_date') is-invalid @enderror" id="po_date" name="po_date" autocomplete="off" placeholder="Enter PO Date" value="{{ old('po_date', $purchaseOrder ? $purchaseOrder->po_date->format('d-m-Y') : '') }}" />
                                    <label for="po_date">PO Date <span class="text-danger">*</span></label>
                                </div>
                                @error('po_date')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="sales_agent_id" name="sales_agent_id" class="select2 form-select @error('sales_agent_id') is-invalid @enderror" data-placeholder="Select Broker/Sales Agent">
                                        <option value="">Select Broker/Sales Agent</option>
                                        @foreach($salesAgents as $agent)
                                        <option value="{{ $agent->id }}" {{ old('sales_agent_id', $purchaseOrder->sales_agent_id ?? '') == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->name }} ({{ $agent->code }})
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="sales_agent_id">Broker/Sales Agent</label>
                                </div>
                                @error('sales_agent_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number"
                                        class="form-control @error('commission') is-invalid @enderror"
                                        id="commission"
                                        name="commission"
                                        step="0.01"
                                        min="0"
                                        max="100"
                                        placeholder="Enter Commission (%)"
                                        value="{{ old('commission', $purchaseOrder->commission ?? '') }}">
                                    <label for="commission">Commission (%)</label>
                                </div>
                                @error('commission')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="supplier_id"
                                        name="supplier_id"
                                        class="select2 form-select @error('supplier_id') is-invalid @enderror"
                                        data-placeholder="Select Supplier">
                                        <option value="">Select Supplier</option>
                                        @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}"
                                            {{ old('supplier_id', $purchaseOrder->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }} ({{ $supplier->code }})
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="supplier_id">Supplier <span class="text-danger">*</span></label>
                                </div>
                                @error('supplier_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text"
                                        class="form-control @error('reference_no') is-invalid @enderror"
                                        id="reference_no"
                                        name="reference_no"
                                        placeholder="Enter Reference No"
                                        value="{{ old('reference_no', $purchaseOrder->reference_no ?? '') }}">
                                    <label for="reference_no">Reference No <span class="text-danger">*</span></label>
                                </div>
                                @error('reference_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text"
                                        class="form-control reference_date @error('reference_date') is-invalid @enderror"
                                        id="reference_date"
                                        name="reference_date"
                                        autocomplete="off"
                                        placeholder="Enter Reference Date" value="{{ old('reference_date', $purchaseOrder ? optional($purchaseOrder->reference_date)->format('d-m-Y') : '') }}" />
                                    <label for="reference_date">Reference Date <span class="text-danger">*</span></label>
                                </div>
                                @error('reference_date')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text"
                                        class="form-control due_date @error('due_date') is-invalid @enderror"
                                        id="due_date"
                                        name="due_date"
                                        autocomplete="off"
                                        placeholder="Enter Due Date" value="{{ old('due_date', $purchaseOrder ? $purchaseOrder->due_date->format('d-m-Y') : '') }}" />
                                    <label for="due_date">Due Date <span class="text-danger">*</span></label>
                                </div>
                                @error('due_date')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="store_type_id" name="store_type_id" class="select2 form-select @error('store_type_id') is-invalid @enderror" data-placeholder="Select Store Type">
                                        <option value="">Select Store Type</option>
                                        @foreach($storeTypes as $storeType)
                                        <option value="{{ $storeType->id }}"
                                            {{ old('store_type_id', $purchaseOrder->store_type_id ?? '') == $storeType->id ? 'selected' : '' }}>
                                            {{ $storeType->store_type_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="store_type_id">Store Type <span class="text-danger">*</span></label>
                                </div>
                                @error('store_type_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text"
                                        class="form-control order_date @error('order_date') is-invalid @enderror"
                                        id="order_date"
                                        name="order_date"
                                        autocomplete="off"
                                        placeholder="Enter Order Date" value="{{ old('order_date', $purchaseOrder ? $purchaseOrder->order_date->format('d-m-Y') : '') }}" />
                                    <label for="order_date">Order Date <span class="text-danger">*</span></label>
                                </div>
                                @error('order_date')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100 @error('payment_terms') is-invalid @enderror"
                                        id="payment_terms"
                                        name="payment_terms"
                                        placeholder="Enter Payment Terms">{{ old('payment_terms', $purchaseOrder->payment_terms ?? '') }}</textarea>
                                    <label for="payment_terms">Payment Terms</label>
                                </div>
                                @error('payment_terms')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            @php
                            $currentStatus = old('status', $purchaseOrder->status ?? 'Draft');

                            $disabledStatuses = match ($currentStatus) {
                            'Approved' => ['Draft'],
                            'Dispatched' => ['Draft', 'Approved'],
                            'Received' => ['Draft', 'Approved', 'Dispatched'],
                            default => [],
                            };
                            @endphp

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="status" name="status" class="select2 form-select @error('status') is-invalid @enderror" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        @foreach(['Draft', 'Approved', 'Dispatched', 'Received'] as $status)
                                        <option value="{{ $status }}" {{ $currentStatus === $status ? 'selected' : '' }} {{ in_array($status, $disabledStatuses) ? 'disabled' : '' }}>
                                            {{ $status }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                </div>
                                @error('status')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Item Details</h4>
                        </div>
                        @error('items')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror

                        <div id="item-rows">
                            <input type="hidden" id="itemIndex" value="{{ $purchaseOrder?->items?->count() ?? 1 }}">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Category *</th>
                                        <th>Material *</th>
                                        <th>UOM *</th>
                                        <th>Qty *</th>
                                        <th>Art No</th>
                                        <th>Rate *</th>
                                        <th>Amount</th>
                                        <th>Remarks</th>
                                        <th>File</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(old('items'))
                                    @foreach(old('items') as $index => $item)
                                    <tr class="item-row">
                                        <td>
                                            <select class="select2 form-select store_category @error('items.'.$index.'.store_category_id') is-invalid @enderror"
                                                name="items[{{ $index }}][store_category_id]"
                                                data-placeholder="Select Category">
                                                <option value="">Select Category</option>
                                                @foreach($storeCategories as $category)
                                                <option value="{{ $category->id }}" {{ $item['store_category_id'] == $category->id ? 'selected' : '' }}>
                                                    {{ $category->category_name }}({{ $category->code }})
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('items.'.$index.'.store_category_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <select class="select2 form-select material @error('items.'.$index.'.raw_material_id') is-invalid @enderror"
                                                name="items[{{ $index }}][raw_material_id]"
                                                data-placeholder="Select Material">
                                                @if(isset($item['raw_material_id']) && $item['raw_material_id'])
                                                @php
                                                $selectedMaterial = \App\Models\RawMaterial::find($item['raw_material_id']);
                                                @endphp
                                                @if($selectedMaterial)
                                                <option value="{{ $selectedMaterial->id }}" data-uom-id="{{ $selectedMaterial->uom_id }}" selected>
                                                    {{ $selectedMaterial->name }} ({{ $selectedMaterial->code }})
                                                </option>
                                                @endif
                                                @endif
                                            </select>
                                            @error('items.'.$index.'.raw_material_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <select class="select2 form-select uom @error('items.'.$index.'.uom_id') is-invalid @enderror"
                                                disabled
                                                data-placeholder="Select UOM">
                                                <option value="">Select UOM</option>
                                                @foreach($uoms as $uom)
                                                <option value="{{ $uom->id }}" {{ ($item['uom_id'] ?? '') == $uom->id ? 'selected' : '' }}>
                                                    {{ $uom->uom_code }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="items[{{ $index }}][uom_id]" value="{{ $item['uom_id'] ?? '' }}" class="uom_hidden">
                                            @error('items.'.$index.'.uom_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" class="form-control quantity @error('items.'.$index.'.quantity') is-invalid @enderror"
                                                name="items[{{ $index }}][quantity]"
                                                step="0.01" min="0.01"
                                                value="{{ $item['quantity'] ?? '' }}">
                                            @error('items.'.$index.'.quantity')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="text" class="form-control art_no @error('items.'.$index.'.art_no') is-invalid @enderror"
                                                name="items[{{ $index }}][art_no]"
                                                value="{{ $item['art_no'] ?? '' }}">
                                            @error('items.'.$index.'.art_no')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" class="form-control rate @error('items.'.$index.'.rate') is-invalid @enderror"
                                                name="items[{{ $index }}][rate]"
                                                step="0.01" min="0"
                                                value="{{ $item['rate'] ?? '' }}">
                                            @error('items.'.$index.'.rate')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="text" class="form-control amount"
                                                value="{{ ($item['quantity'] ?? 0) * ($item['rate'] ?? 0) }}" readonly>
                                        </td>
                                        <td>
                                            <textarea class="form-control remarks @error('items.'.$index.'.remarks') is-invalid @enderror"
                                                name="items[{{ $index }}][remarks]"
                                                style="height: 58px;">{{ $item['remarks'] ?? '' }}</textarea>
                                            @error('items.'.$index.'.remarks')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="file" class="form-control file-input @error('items.'.$index.'.attached_file') is-invalid @enderror"
                                                name="items[{{ $index }}][attached_file]"
                                                accept="image/jpeg,image/jpg,image/png,image/webp">
                                            @error('items.'.$index.'.attached_file')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            @if($loop->first)
                                            <button type="button" class="btn btn-primary add_item">
                                                <i class="ri ri-add-line"></i>
                                            </button>
                                            @else
                                            <button type="button" class="btn btn-danger delete_item">
                                                <i class="ri ri-delete-bin-line"></i>
                                            </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                    @elseif($purchaseOrder && $purchaseOrder->items->count() > 0)
                                    @foreach($purchaseOrder->items as $index => $item)
                                    <tr class="item-row">
                                        <td>
                                            <select class="select2 form-select store_category @error('items.'.$index.'.store_category_id') is-invalid @enderror"
                                                name="items[{{ $index }}][store_category_id]"
                                                data-placeholder="Select Category">
                                                <option value="">Select Category</option>
                                                @foreach($storeCategories as $category)
                                                <option value="{{ $category->id }}" {{ $item->store_category_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->category_name }}({{ $category->code }})
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('items.'.$index.'.store_category_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <select class="select2 form-select material @error('items.'.$index.'.raw_material_id') is-invalid @enderror"
                                                name="items[{{ $index }}][raw_material_id]"
                                                data-placeholder="Select Material">
                                                <option value="{{ $item->raw_material_id }}" data-uom-id="{{ $item->rawMaterial->uom_id }}">
                                                    {{ $item->rawMaterial->name }} ({{ $item->rawMaterial->code }})
                                                </option>
                                            </select>
                                            @error('items.'.$index.'.raw_material_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <select class="select2 form-select uom @error('items.'.$index.'.uom_id') is-invalid @enderror"
                                                disabled
                                                data-placeholder="Select UOM">
                                                @foreach($uoms as $uom)
                                                <option value="{{ $uom->id }}" {{ $item->uom_id == $uom->id ? 'selected' : '' }}>
                                                    {{ $uom->uom_code }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="items[{{ $index }}][uom_id]" value="{{ $item->uom_id }}" class="uom_hidden">
                                            @error('items.'.$index.'.uom_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" class="form-control quantity @error('items.'.$index.'.quantity') is-invalid @enderror"
                                                name="items[{{ $index }}][quantity]"
                                                step="0.01" min="0.01"
                                                value="{{ $item->quantity }}">
                                            @error('items.'.$index.'.quantity')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="text" class="form-control art_no @error('items.'.$index.'.art_no') is-invalid @enderror"
                                                name="items[{{ $index }}][art_no]"
                                                value="{{ $item->art_no }}">
                                            @error('items.'.$index.'.art_no')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" class="form-control rate @error('items.'.$index.'.rate') is-invalid @enderror"
                                                name="items[{{ $index }}][rate]"
                                                step="0.01" min="0"
                                                value="{{ $item->rate }}">
                                            @error('items.'.$index.'.rate')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="text" class="form-control amount"
                                                value="{{ $item->amount }}" readonly>
                                        </td>
                                        <td>
                                            <textarea class="form-control remarks @error('items.'.$index.'.remarks') is-invalid @enderror"
                                                name="items[{{ $index }}][remarks]">{{ $item->remarks }}</textarea>
                                            @error('items.'.$index.'.remarks')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="file" class="form-control file-input @error('items.'.$index.'.attached_file') is-invalid @enderror"
                                                name="items[{{ $index }}][attached_file]"
                                                accept="image/jpeg,image/jpg,image/png,image/webp">
                                            @if($item->attached_file)
                                            <a href="javascript:void(0)" class="view-image mt-1 d-block"
                                                data-image="{{ asset('uploads/purchase_orders/' . $item->attached_file) }}">
                                                <i class="ri ri-image-line"></i> View
                                            </a>
                                            @endif
                                            @error('items.'.$index.'.attached_file')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            @if($loop->first)
                                            <button type="button" class="btn btn-primary add_item">
                                                <i class="ri ri-add-line"></i>
                                            </button>
                                            @else
                                            <button type="button" class="btn btn-danger delete_item">
                                                <i class="ri ri-delete-bin-line"></i>
                                            </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr class="item-row">
                                        <td>
                                            <select class="select2 form-select store_category" name="items[0][store_category_id]"
                                                data-placeholder="Select Category">
                                                <option value="">Select Category</option>
                                                @foreach($storeCategories as $category)
                                                <option value="{{ $category->id }}">{{ $category->category_name }}({{ $category->code }})</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="select2 form-select material"
                                                name="items[0][raw_material_id]"
                                                data-placeholder="Select Material">
                                                <option value="">Select Material</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="select2 form-select uom"
                                                disabled
                                                data-placeholder="Select UOM">
                                                <option value="">Select UOM</option>
                                                @foreach($uoms as $uom)
                                                <option value="{{ $uom->id }}">{{ $uom->uom_code }}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="items[0][uom_id]" value="" class="uom_hidden">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control quantity"
                                                name="items[0][quantity]"
                                                step="0.01" min="0.01">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control art_no"
                                                name="items[0][art_no]">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control rate"
                                                name="items[0][rate]"
                                                step="0.01" min="0">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control amount" readonly>
                                        </td>
                                        <td>
                                            <textarea class="form-control remarks"
                                                name="items[0][remarks]"
                                                style="height: 58px;"></textarea>
                                        </td>
                                        <td>
                                            <input type="file" class="form-control file-input"
                                                name="items[0][attached_file]"
                                                accept="image/jpeg,image/jpg,image/png,image/webp">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-primary add_item">
                                                <i class="ri ri-add-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-3 col-xl-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('total_qty') is-invalid @enderror" id="total_qty" name="total_qty" placeholder="Enter Quantity"
                                        value="{{ old('total_qty', $purchaseOrder->total_qty ?? '') }}" readonly>
                                    <label for="total_qty">Total Qty</label>
                                </div>
                                @error('total_qty')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 col-xl-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('sub_total') is-invalid @enderror" id="sub_total" name="sub_total"
                                        value="{{ old('sub_total', $purchaseOrder->sub_total ?? '') }}" readonly>
                                    <label for="sub_total">Sub Total</label>
                                </div>
                                @error('sub_total')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <label class="form-label d-block mb-1">Discount:</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control text-end @error('discount_percent') is-invalid @enderror" id="discount_percent" name="discount_percent"
                                            step="0.01" min="0" max="100" value="{{ old('discount_percent', $purchaseOrder->discount_percent ?? 0) }}">
                                        <span class="input-group-text">%</span>
                                        <input type="text" class="form-control text-end @error('discount_amount') is-invalid @enderror" id="discount_amount" name="discount_amount"
                                            value="{{ old('discount_amount', $purchaseOrder->discount_amount ?? '') }}" readonly>
                                    </div>
                                    @error('discount_percent')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                    @error('discount_amount')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="taxable_amount" name="taxable_amount" class="form-control @error('taxable_amount') is-invalid @enderror"
                                        value="{{ old('taxable_amount', $purchaseOrder->taxable_amount ?? '') }}" readonly>
                                    <label>Net Amount (Before Tax)</label>
                                </div>
                                @error('taxable_amount')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <label class="">Other State</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('other_state') is-invalid @enderror" type="radio" name="other_state" id="other_state_yes" value="yes"
                                        {{ old('other_state', $purchaseOrder && $purchaseOrder->other_state ? 'yes' : 'no') == 'yes' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="other_state_yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('other_state') is-invalid @enderror" type="radio" name="other_state" id="other_state_no" value="no"
                                        {{ old('other_state', $purchaseOrder && $purchaseOrder->other_state ? 'yes' : 'no') == 'no' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="other_state_no">No</label>
                                </div>
                                @error('other_state')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 col-xl-2 igst-field {{ old('other_state', $purchaseOrder && $purchaseOrder->other_state ? 'yes' : 'no') == 'yes' ? '' : 'd-none' }}">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control @error('igst_percent') is-invalid @enderror" id="igst_percent" name="igst_percent" step="0.01" min="0" max="100"
                                        value="{{ old('igst_percent', $purchaseOrder->igst_percent ?? $web_settings->igst) }}">
                                    <label for="igst_percent">IGST %</label>
                                </div>
                                @error('igst_percent')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 col-xl-2 cgst-field {{ old('other_state', $purchaseOrder && $purchaseOrder->other_state ? 'yes' : 'no') == 'no' ? '' : 'd-none' }}">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control @error('cgst_percent') is-invalid @enderror" id="cgst_percent" name="cgst_percent" step="0.01" min="0" max="100"
                                        value="{{ old('cgst_percent', $purchaseOrder->cgst_percent ?? $web_settings->cgst) }}">
                                    <label for="cgst_percent">CGST %</label>
                                </div>
                                @error('cgst_percent')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 col-xl-2 sgst-field {{ old('other_state', $purchaseOrder && $purchaseOrder->other_state ? 'yes' : 'no') == 'no' ? '' : 'd-none' }}">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control @error('sgst_percent') is-invalid @enderror" id="sgst_percent" name="sgst_percent" step="0.01" min="0" max="100"
                                        value="{{ old('sgst_percent', $purchaseOrder->sgst_percent ?? $web_settings->sgst) }}">
                                    <label for="sgst_percent">SGST %</label>
                                </div>
                                @error('sgst_percent')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 col-xl-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('tax_amount') is-invalid @enderror" id="tax_amount" name="tax_amount"
                                        value="{{ old('tax_amount', $purchaseOrder->tax_amount ?? '') }}" readonly>
                                    <label for="tax_amount">Tax Amount</label>
                                </div>
                                @error('tax_amount')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 col-xl-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('total_amount') is-invalid @enderror" id="total_amount" name="total_amount"
                                        value="{{ old('total_amount', $purchaseOrder->total_amount ?? '') }}" readonly>
                                    <label for="total_amount">Total Amount</label>
                                </div>
                                @error('total_amount')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-12 text-end mt-5">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ url('purchase_orders') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="Preview">
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let itemIndex = Number($('#itemIndex').val());

        // Add new item row
        $(document).on('click', '.add_item', function() {
            let rowHtml = `
            <tr class="item-row">
                <td>
                    <select class="select2 form-select store_category" 
                            name="items[${itemIndex}][store_category_id]" 
                            data-placeholder="Select Category">
                        <option value="">Select Category</option>
                        @foreach($storeCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->category_name }}({{ $category->code }})</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select class="select2 form-select material" 
                            name="items[${itemIndex}][raw_material_id]" 
                            data-placeholder="Select Material">
                        <option value="">Select Material</option>
                    </select>
                </td>
                <td>
                    <select class="select2 form-select uom" 
                            disabled
                            data-placeholder="Select UOM">
                        <option value="">Select UOM</option>
                        @foreach($uoms as $uom)
                        <option value="{{ $uom->id }}">{{ $uom->uom_code }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="items[${itemIndex}][uom_id]" value="" class="uom_hidden">
                </td>
                <td>
                    <input type="number" class="form-control quantity" 
                        name="items[${itemIndex}][quantity]" 
                        step="0.01" min="0.01">
                </td>
                <td>
                    <input type="text" class="form-control art_no" 
                        name="items[${itemIndex}][art_no]">
                </td>
                <td>
                    <input type="number" class="form-control rate" 
                        name="items[${itemIndex}][rate]" 
                        step="0.01" min="0">
                </td>
                <td>
                    <input type="text" class="form-control amount" readonly>
                </td>
                <td>
                    <textarea class="form-control remarks" 
                            name="items[${itemIndex}][remarks]" 
                            style="height: 58px;"></textarea>
                </td>
                <td>
                    <input type="file" class="form-control file-input" 
                        name="items[${itemIndex}][attached_file]" 
                        accept="image/jpeg,image/jpg,image/png,image/webp">
                </td>
                <td>
                    <button type="button" class="btn btn-danger delete_item">
                        <i class="ri ri-delete-bin-line"></i>
                    </button>
                </td>
            </tr>`;

            $('#item-rows tbody').append(rowHtml);
            $('.select2').each(function() {
                $(this).select2({
                    dropdownParent: $(this).parent(),
                    placeholder: $(this).data('placeholder'),
                    width: '100%'
                });
            });
            itemIndex++;
        });

        function updateDisabledMaterials() {
            let selectedMaterialIds = [];
            $('.material').each(function() {
                let val = $(this).val();
                if (val) {
                    selectedMaterialIds.push(val.toString());
                }
            });

            $('.material').each(function() {
                let currentVal = $(this).val();
                let $select = $(this);
                $select.find('option').each(function() {
                    let optionId = $(this).val();
                    if (optionId && selectedMaterialIds.includes(optionId.toString()) && optionId.toString() !== currentVal) {
                        $(this).attr('disabled', 'disabled');
                    } else {
                        $(this).removeAttr('disabled');
                    }
                });
                
                $select.select2({
                    dropdownParent: $select.parent(),
                    placeholder: $select.data('placeholder'),
                    width: '100%'
                });
            });
        }

        // Call on page load
        updateDisabledMaterials();

        // Load materials based on category
        $(document).on('change', '.store_category', function () {
    let category_id = $(this).val();
    let row = $(this).closest('tr');
    let materialSelect = row.find('.material');

    // Fully destroy Select2 safely
    if (materialSelect.hasClass("select2-hidden-accessible")) {
        materialSelect.select2('destroy');
    }

    materialSelect.empty().append('<option value="">Select Material</option>');

    if (!category_id) {
        materialSelect.select2({
            dropdownParent: materialSelect.parent(),
            placeholder: materialSelect.data('placeholder'),
            width: '100%'
        });
        return;
    }

    $.ajax({
        url: APP_URL + '/get-materials-by-category/' + category_id,
        type: 'GET',
        success: function (response) {

            let materialsHtml = '<option value="">Select Material</option>';

            if (response.materials?.length) {
                response.materials.forEach(material => {
                    materialsHtml += `
                        <option value="${material.id}" data-uom-id="${material.uom_id}">
                            ${material.name} (${material.code})
                        </option>`;
                });
            } else {
                materialsHtml += '<option value="">No materials found</option>';
            }

            materialSelect.html(materialsHtml);

            // Re-init Select2 AFTER HTML update
            materialSelect.select2({
                dropdownParent: materialSelect.parent(),
                placeholder: materialSelect.data('placeholder'),
                width: '100%'
            });

            updateDisabledMaterials();
        },
        error: function () {
            materialSelect.html('<option value="">Select Material</option>').select2({
                dropdownParent: materialSelect.parent(),
                width: '100%'
            });
        }
    });
});


        // Set UOM based on material
        $(document).on('change', '.material', function() {
            let uom_id = $(this).find(':selected').data('uom-id');
            if (uom_id) {
                let row = $(this).closest('tr');
                row.find('.uom').val(uom_id).trigger('change');
                row.find('.uom_hidden').val(uom_id);
            } else {
                let row = $(this).closest('tr');
                row.find('.uom').val('').trigger('change');
                row.find('.uom_hidden').val('');
            }
            updateDisabledMaterials(); // Update after selection
        });

        // Delete item row
        $(document).on('click', '.delete_item', function() {
            if ($('#item-rows tbody tr').length > 1) {
                $(this).closest('tr').remove();
                calculateTotals();
                updateDisabledMaterials(); // Update after removal
            } else {
                alert('At least one item is required');
            }
        });

        // Calculate amount on quantity/rate change
        $(document).on('input', '.quantity, .rate', function() {
            let row = $(this).closest('tr');
            let qty = parseFloat(row.find('.quantity').val()) || 0;
            let rate = parseFloat(row.find('.rate').val()) || 0;
            row.find('.amount').val((qty * rate).toFixed(2));
            calculateTotals();
        });

        // Calculate discount
        $('#discount_percent').on('input', function() {
            calculateTotals();
        });

        // Toggle tax fields based on other_state
        $('input[name="other_state"]').on('change', function() {
            if ($(this).val() === 'yes') {
                $('.igst-field').removeClass('d-none');
                $('.cgst-field, .sgst-field').addClass('d-none');
            } else {
                $('.igst-field').addClass('d-none');
                $('.cgst-field, .sgst-field').removeClass('d-none');
            }
            calculateTotals();
        });

        // Recalculate on tax percentage change
        $('#igst_percent, #cgst_percent, #sgst_percent').on('input', function() {
            calculateTotals();
        });

        // Calculate totals
        function calculateTotals() {
            let totalQty = 0;
            let subTotal = 0;

            $('#item-rows tbody tr').each(function() {
                let qty = parseFloat($(this).find('.quantity').val()) || 0;
                let amount = parseFloat($(this).find('.amount').val()) || 0;
                totalQty += qty;
                subTotal += amount;
            });

            $('#total_qty').val(totalQty.toFixed(2));
            $('#sub_total').val(subTotal.toFixed(2));

            // Calculate discount
            let discountPercent = parseFloat($('#discount_percent').val()) || 0;
            let discountAmount = (subTotal * discountPercent) / 100;
            $('#discount_amount').val(discountAmount.toFixed(2));

            // Calculate taxable amount
            let taxableAmount = subTotal - discountAmount;
            $('#taxable_amount').val(taxableAmount.toFixed(2));

            // Calculate tax
            let taxAmount = 0;
            if ($('input[name="other_state"]:checked').val() === 'yes') {
                let igstPercent = parseFloat($('#igst_percent').val()) || 0;
                taxAmount = (taxableAmount * igstPercent) / 100;
            } else {
                let cgstPercent = parseFloat($('#cgst_percent').val()) || 0;
                let sgstPercent = parseFloat($('#sgst_percent').val()) || 0;
                taxAmount = (taxableAmount * (cgstPercent + sgstPercent)) / 100;
            }
            $('#tax_amount').val(taxAmount.toFixed(2));

            // Calculate total amount
            let totalAmount = taxableAmount + taxAmount;
            $('#total_amount').val(totalAmount.toFixed(2));
        }

        // Image preview
        $(document).on('click', '.view-image', function() {
            let imageSrc = $(this).data('image');
            $('#modalImage').attr('src', imageSrc);
            $('#imageModal').modal('show');
        });

        // File input change - show thumbnail
        $(document).on('change', '.file-input', function() {
            let file = this.files[0];
            if (file) {
                let reader = new FileReader();
                let $td = $(this).closest('td');

                reader.onload = function(e) {
                    $td.find('.view-image').remove();
                    $td.append(`<a href="javascript:void(0)" class="view-image mt-1 d-block" data-image="${e.target.result}">
                    <i class="ri ri-image-line"></i> View
                </a>`);
                }
                reader.readAsDataURL(file);
            }
        });

        // Initial calculation
        calculateTotals();
    });
</script>
@endsection
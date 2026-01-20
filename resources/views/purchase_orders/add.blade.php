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
                                    <input type="text" class="form-control @error('po_number') is-invalid @enderror" id="po_number" name="po_number" placeholder="Enter PO Number" value="{{ old('po_number', $purchaseOrder->po_number ?? $nextPoNumber ?? '') }}">
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
                                    <select id="purchase_commission_agent_id" name="purchase_commission_agent_id" class="select2 form-select @error('purchase_commission_agent_id') is-invalid @enderror" data-placeholder="Select Purchase Commission Agent">
                                        <option value="">Select Purchase Commission Agent</option>
                                        @foreach($salesAgents as $agent)
                                        <option value="{{ $agent->id }}" {{ old('purchase_commission_agent_id', $purchaseOrder->purchase_commission_agent_id ?? '') == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->name }} ({{ $agent->code }})
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="purchase_commission_agent_id">Purchase Commission Agent</label>
                                </div>
                                @error('purchase_commission_agent_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control @error('commission') is-invalid @enderror" id="commission" name="commission" step="0.01" min="0" max="100" placeholder="Enter Commission (%)" value="{{ old('commission', $purchaseOrder->commission ?? '') }}">
                                    <label for="commission">Commission (%)</label>
                                </div>
                                @error('commission')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="supplier_id" name="supplier_id" class="select2 form-select @error('supplier_id') is-invalid @enderror" data-placeholder="Select Supplier">
                                        <option value="">Select Supplier</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchaseOrder->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }} ({{ $supplier->code }})
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
                                    <input type="text" class="form-control @error('reference_no') is-invalid @enderror" id="reference_no" name="reference_no" placeholder="Enter Reference No" value="{{ old('reference_no', $purchaseOrder->reference_no ?? '') }}">
                                    <label for="reference_no">Reference No <span class="text-danger">*</span></label>
                                </div>
                                @error('reference_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control reference_date @error('reference_date') is-invalid @enderror" id="reference_date" name="reference_date" autocomplete="off" placeholder="Enter Reference Date" value="{{ old('reference_date', $purchaseOrder ? optional($purchaseOrder->reference_date)->format('d-m-Y') : '') }}" />
                                    <label for="reference_date">Reference / Order Date <span class="text-danger">*</span></label>
                                </div>
                                @error('reference_date')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control due_date @error('due_date') is-invalid @enderror" id="due_date" name="due_date" autocomplete="off" placeholder="Enter Due Date" value="{{ old('due_date', $purchaseOrder ? $purchaseOrder->due_date->format('d-m-Y') : '') }}" />
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

                        <div id="item-rows" class="table-responsive text-nowrap">
                            <input type="hidden" id="itemIndex" value="{{ $purchaseOrder?->items?->count() ?? 1 }}">
                            <table class="table align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th style="min-width: 200px;">Store Category *</th>
                                        <th style="min-width: 150px;">Brand</th>
                                        <th style="min-width: 200px;">Raw Material *</th>
                                        <th style="min-width: 150px;">Style</th>
                                        <th style="min-width: 150px;">Fabric Width</th>
                                        <th style="min-width: 100px;">UOM *</th>
                                        <th style="min-width: 120px;">Qty *</th>
                                        <th style="min-width: 130px;">Supplier Design Name</th>
                                        <th style="min-width: 150px;">Color</th>
                                        <th style="min-width: 120px;">Rate *</th>
                                        <th style="min-width: 120px;">Amount</th>
                                        <th style="min-width: 150px;">Remarks</th>
                                        <th style="min-width: 100px;">File</th>
                                        <th style="min-width: 50px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(old('items'))
                                    @foreach(old('items') as $index => $item)
                                    <tr class="item-row">
                                        <td>
                                            <select class="select2 form-select po_store_category @error('items.'.$index.'.store_category_id') is-invalid @enderror" name="items[{{ $index }}][store_category_id]" data-placeholder="Select Store Category">
                                                <option value="">Select Store Category</option>
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
                                            <select class="select2 form-select brand @error('items.'.$index.'.brand_id') is-invalid @enderror" name="items[{{ $index }}][brand_id]" data-placeholder="Select Brand">
                                                <option value="">Select Brand</option>
                                                @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}" {{ ($item['brand_id'] ?? '') == $brand->id ? 'selected' : '' }}>{{ $brand->brand_name }}</option>
                                                @endforeach
                                            </select>
                                            @error('items.'.$index.'.brand_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <select class="select2 form-select material @error('items.'.$index.'.raw_material_id') is-invalid @enderror" name="items[{{ $index }}][raw_material_id]" data-placeholder="Select Raw Material">
                                                @if(isset($item['raw_material_id']) && $item['raw_material_id'])
                                                    @php
                                                        $selectedMaterial = \App\Models\RawMaterial::find($item['raw_material_id']);
                                                    @endphp
                                                    @if($selectedMaterial)
                                                    <option value="{{ $selectedMaterial->id }}" data-uom-id="{{ $selectedMaterial->uom_id }}" selected>{{ $selectedMaterial->name }} ({{ $selectedMaterial->code }})</option>
                                                    @endif
                                                @endif
                                            </select>
                                            @error('items.'.$index.'.raw_material_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <select class="select2 form-select style @error('items.'.$index.'.style_id') is-invalid @enderror" name="items[{{ $index }}][style_id]" data-placeholder="Select Style">
                                                <option value="">Select Style</option>
                                                @foreach($styles as $style)
                                                <option value="{{ $style->id }}" {{ ($item['style_id'] ?? '') == $style->id ? 'selected' : '' }}>{{ $style->style_name }}</option>
                                                @endforeach
                                            </select>
                                            @error('items.'.$index.'.style_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <select class="select2 form-select fabric_width @error('items.'.$index.'.fabric_width_id') is-invalid @enderror" name="items[{{ $index }}][fabric_width_id]" data-placeholder="Select Width">
                                                <option value="">Select Width</option>
                                                @foreach($sizeRatios as $ratio)
                                                <option value="{{ $ratio->id }}" {{ ($item['fabric_width_id'] ?? '') == $ratio->id ? 'selected' : '' }}>{{ $ratio->size }} - ({{ $ratio->ratio }})</option>
                                                @endforeach
                                            </select>
                                            @error('items.'.$index.'.fabric_width_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <select class="select2 form-select uom @error('items.'.$index.'.uom_id') is-invalid @enderror" name="items[{{ $index }}][uom_id]" disabled data-placeholder="Select UOM">
                                                <option value="">Select UOM</option>
                                                @foreach($uoms as $uom)
                                                <option value="{{ $uom->id }}" {{ ($item['uom_id'] ?? '') == $uom->id ? 'selected' : '' }}>{{ $uom->uom_code }}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="items[{{ $index }}][uom_id]" value="{{ $item['uom_id'] ?? '' }}" class="uom_hidden">
                                            @error('items.'.$index.'.uom_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" class="form-control quantity @error('items.'.$index.'.quantity') is-invalid @enderror" name="items[{{ $index }}][quantity]" step="0.01" min="0.01" value="{{ $item['quantity'] ?? '' }}">
                                            @error('items.'.$index.'.quantity')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="text" class="form-control art_no @error('items.'.$index.'.art_no') is-invalid @enderror" name="items[{{ $index }}][art_no]" value="{{ $item['art_no'] ?? '' }}">
                                            @error('items.'.$index.'.art_no')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <select class="select2 form-select color @error('items.'.$index.'.color_id') is-invalid @enderror" name="items[{{ $index }}][color_id]" data-placeholder="Select Color">
                                                <option value="">Select Color</option>
                                                @foreach($colors as $color)
                                                <option value="{{ $color->id }}" {{ ($item['color_id'] ?? '') == $color->id ? 'selected' : '' }}>{{ $color->color_name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('items.'.$index.'.color_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" class="form-control rate @error('items.'.$index.'.rate') is-invalid @enderror" name="items[{{ $index }}][rate]" step="0.01" min="0" value="{{ $item['rate'] ?? '' }}">
                                            @error('items.'.$index.'.rate')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="text" class="form-control amount" value="{{ ($item['quantity'] ?? 0) * ($item['rate'] ?? 0) }}" readonly>
                                        </td>
                                        <td>
                                            <textarea class="form-control remarks @error('items.'.$index.'.remarks') is-invalid @enderror" name="items[{{ $index }}][remarks]" style="height: 58px;">{{ $item['remarks'] ?? '' }}</textarea>
                                            @error('items.'.$index.'.remarks')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="file" class="form-control file-input @error('items.'.$index.'.attached_file') is-invalid @enderror" name="items[{{ $index }}][attached_file]" accept="image/jpeg,image/jpg,image/png,image/webp">
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
                                            <select class="select2 form-select po_store_category @error('items.'.$index.'.store_category_id') is-invalid @enderror" name="items[{{ $index }}][store_category_id]" data-placeholder="Select Category">
                                                <option value="">Select Category</option>
                                                @foreach($storeCategories as $category)
                                                <option value="{{ $category->id }}" {{ $item->store_category_id == $category->id ? 'selected' : '' }}>{{ $category->category_name }}({{ $category->code }})
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('items.'.$index.'.store_category_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <select class="select2 form-select brand @error('items.'.$index.'.brand_id') is-invalid @enderror" name="items[{{ $index }}][brand_id]" data-placeholder="Select Brand">
                                                <option value="">Select Brand</option>
                                                @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}" {{ ($item->brand_id ?? '') == $brand->id ? 'selected' : '' }}>{{ $brand->brand_name }}</option>
                                                @endforeach
                                            </select>
                                            @error('items.'.$index.'.brand_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <select class="select2 form-select material @error('items.'.$index.'.raw_material_id') is-invalid @enderror" name="items[{{ $index }}][raw_material_id]" data-placeholder="Select Material">
                                                <option value="{{ $item->raw_material_id }}" data-uom-id="{{ $item->rawMaterial->uom_id }}">{{ $item->rawMaterial->name }} ({{ $item->rawMaterial->code }})</option>
                                            </select>
                                            @error('items.'.$index.'.raw_material_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <select class="select2 form-select style @error('items.'.$index.'.style_id') is-invalid @enderror" name="items[{{ $index }}][style_id]" data-placeholder="Select Style">
                                                <option value="">Select Style</option>
                                                @foreach($styles as $style)
                                                <option value="{{ $style->id }}" {{ $item->style_id == $style->id ? 'selected' : '' }}>{{ $style->style_name }}</option>
                                                @endforeach
                                            </select>
                                            @error('items.'.$index.'.style_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <select class="select2 form-select fabric_width @error('items.'.$index.'.fabric_width_id') is-invalid @enderror" name="items[{{ $index }}][fabric_width_id]" data-placeholder="Select Width">
                                                <option value="">Select Width</option>
                                                @foreach($sizeRatios as $ratio)
                                                <option value="{{ $ratio->id }}" {{ ($item->fabric_width_id ?? '') == $ratio->id ? 'selected' : '' }}>{{ $ratio->size }} - ({{ $ratio->ratio }})</option>
                                                @endforeach
                                            </select>
                                            @error('items.'.$index.'.fabric_width_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <select class="select2 form-select uom @error('items.'.$index.'.uom_id') is-invalid @enderror" disabled data-placeholder="Select UOM">
                                                @foreach($uoms as $uom)
                                                <option value="{{ $uom->id }}" {{ $item->uom_id == $uom->id ? 'selected' : '' }}>{{ $uom->uom_code }}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="items[{{ $index }}][uom_id]" value="{{ $item->uom_id }}" class="uom_hidden">
                                            @error('items.'.$index.'.uom_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" class="form-control quantity @error('items.'.$index.'.quantity') is-invalid @enderror" name="items[{{ $index }}][quantity]" step="0.01" min="0.01" value="{{ $item->quantity }}">
                                            @error('items.'.$index.'.quantity')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="text" class="form-control art_no @error('items.'.$index.'.art_no') is-invalid @enderror" name="items[{{ $index }}][art_no]" value="{{ $item->art_no }}">
                                            @error('items.'.$index.'.art_no')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <select class="select2 form-select color @error('items.'.$index.'.color_id') is-invalid @enderror" name="items[{{ $index }}][color_id]" data-placeholder="Select Color">
                                                <option value="">Select Color</option>
                                                @foreach($colors as $color)
                                                <option value="{{ $color->id }}" {{ $item->color_id == $color->id ? 'selected' : '' }}>{{ $color->color_name }}</option>
                                                @endforeach
                                            </select>
                                            @error('items.'.$index.'.color_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" class="form-control rate @error('items.'.$index.'.rate') is-invalid @enderror" name="items[{{ $index }}][rate]" step="0.01" min="0" value="{{ $item->rate }}">
                                            @error('items.'.$index.'.rate')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="text" class="form-control amount" value="{{ $item->amount }}" readonly>
                                        </td>
                                        <td>
                                            <textarea class="form-control remarks @error('items.'.$index.'.remarks') is-invalid @enderror" name="items[{{ $index }}][remarks]">{{ $item->remarks }}</textarea>
                                            @error('items.'.$index.'.remarks')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="file" class="form-control file-input @error('items.'.$index.'.attached_file') is-invalid @enderror" name="items[{{ $index }}][attached_file]" accept="image/jpeg,image/jpg,image/png,image/webp">
                                            @if($item->attached_file)
                                            <a href="javascript:void(0)" class="view-image mt-1 d-block" data-image="{{ asset('uploads/purchase_orders/' . $item->attached_file) }}">
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
                                            <select class="select2 form-select po_store_category" name="items[0][store_category_id]" data-placeholder="Select Category">
                                                <option value="">Select Category</option>
                                                @foreach($storeCategories as $category)
                                                <option value="{{ $category->id }}">{{ $category->category_name }}({{ $category->code }})</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="select2 form-select brand" name="items[0][brand_id]" data-placeholder="Select Brand">
                                                <option value="">Select Brand</option>
                                                @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="select2 form-select material" name="items[0][raw_material_id]" data-placeholder="Select Material">
                                                <option value="">Select Material</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="select2 form-select style" name="items[0][style_id]" data-placeholder="Select Style">
                                                <option value="">Select Style</option>
                                                @foreach($styles as $style)
                                                <option value="{{ $style->id }}">{{ $style->style_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="select2 form-select fabric_width" name="items[0][fabric_width_id]" data-placeholder="Select Width">
                                                <option value="">Select Width</option>
                                                @foreach($sizeRatios as $ratio)
                                                <option value="{{ $ratio->id }}">{{ $ratio->size }} - ({{ $ratio->ratio }})</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="select2 form-select uom" name="items[0][uom_id]" disabled data-placeholder="Select UOM">
                                                <option value="">Select UOM</option>
                                                @foreach($uoms as $uom)
                                                <option value="{{ $uom->id }}">{{ $uom->uom_code }}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="items[0][uom_id]" value="" class="uom_hidden">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control quantity" name="items[0][quantity]" step="0.01" min="0.01">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control art_no" name="items[0][art_no]">
                                        </td>
                                        <td>
                                            <select class="select2 form-select color" name="items[0][color_id]" data-placeholder="Select Color">
                                                <option value="">Select Color</option>
                                                @foreach($colors as $color)
                                                <option value="{{ $color->id }}">{{ $color->color_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control rate" name="items[0][rate]" step="0.01" min="0">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control amount" readonly>
                                        </td>
                                        <td>
                                            <textarea class="form-control remarks" name="items[0][remarks]" style="height: 58px;"></textarea>
                                        </td>
                                        <td>
                                            <input type="file" class="form-control file-input" name="items[0][attached_file]" accept="image/jpeg,image/jpg,image/png,image/webp">
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
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-header-box">
                                    <h4>Additional Information</h4>
                                </div>
                                <div class="row g-4">
                                    <div class="col-12">
                                        @php
                                        $currentStatus = old('status', $purchaseOrder->status ?? 'Draft');
                                        $disabledStatuses = match ($currentStatus) {
                                            'Approved' => ['Draft'],
                                            'Dispatched' => ['Draft', 'Approved'],
                                            'Received' => ['Draft', 'Approved', 'Dispatched'],
                                            default => [],
                                        };
                                        @endphp
                                        <div class="form-floating form-floating-outline">
                                            <select id="status" name="status" class="select2 form-select @error('status') is-invalid @enderror" data-placeholder="Select Status">
                                                <option value="">Select Status</option>
                                                @foreach(['Draft', 'Approved', 'Dispatched', 'Received'] as $status)
                                                <option value="{{ $status }}" {{ $currentStatus === $status ? 'selected' : '' }} {{ in_array($status, $disabledStatuses) ? 'disabled' : '' }}>{{ $status }}</option>
                                                @endforeach
                                            </select>
                                            <label for="status">Status <span class="text-danger">*</span></label>
                                        </div>
                                        @error('status')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating form-floating-outline">
                                            <textarea class="form-control h-px-100 @error('payment_terms') is-invalid @enderror" id="payment_terms" name="payment_terms" placeholder="Enter Payment Terms">{{ old('payment_terms', $purchaseOrder->payment_terms ?? '') }}</textarea>
                                            <label for="payment_terms">Payment Terms</label>
                                        </div>
                                        @error('payment_terms')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 file-container">
                                        <div class="form-floating form-floating-outline text-black">
                                            <input type="file" class="form-control file-input @error('additional_attachments') is-invalid @enderror" id="additional_attachments" name="additional_attachments">
                                            <label for="additional_attachments">Additional Attachments</label>
                                            @if($purchaseOrder && $purchaseOrder->additional_attachments)
                                            <div class="mt-2">
                                                @php
                                                    $attachment = $purchaseOrder->additional_attachments;
                                                    $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                                    $url = asset('uploads/po/' . $purchaseOrder->id . '/' . $attachment);
                                                @endphp

                                                @if($isImage)
                                                    <a href="javascript:void(0)" class="view-image mt-1 d-block" data-image="{{ $url }}">
                                                        <i class="ri ri-image-line"></i> View
                                                    </a>
                                                @else
                                                    <a href="{{ $url }}" class="mt-1 d-block" target="_blank">
                                                        <i class="ri ri-file-text-line"></i> View
                                                    </a>
                                                @endif
                                            </div>
                                            @endif
                                        </div>
                                        @error('additional_attachments')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-header-box">
                                    <h4>Tax Summary</h4>
                                </div>
                                <div class="row g-4">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="total_qty" class="fw-medium">Total Qty:</label>
                                            <input type="text" class="form-control-plaintext text-end w-50 fw-bold" id="total_qty" name="total_qty" value="{{ old('total_qty', $purchaseOrder->total_qty ?? '') }}" readonly>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="sub_total" class="fw-medium">Sub Total:</label>
                                            <input type="text" class="form-control-plaintext text-end w-50 fw-bold" id="sub_total" name="sub_total" value="{{ old('sub_total', $purchaseOrder->sub_total ?? '') }}" readonly>
                                        </div>

                                        <div class="mb-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label class="fw-medium">Discount:</label>
                                                <div class="input-group input-group-sm" style="width:120px;">
                                                    <input type="number" class="form-control form-control-sm text-end @error('discount_percent') is-invalid @enderror" id="discount_percent" name="discount_percent" step="0.01" min="0" max="100" value="{{ old('discount_percent', $purchaseOrder->discount_percent ?? 0) }}">
                                                    <span class="input-group-text px-1">%</span>
                                                </div>
                                            </div>
                                            <div class="text-end mt-1">
                                                <input type="text" class="form-control-plaintext form-control-sm text-end py-0" id="discount_amount" name="discount_amount" value="{{ old('discount_amount', $purchaseOrder->discount_amount ?? '') }}" readonly>
                                            </div>
                                        </div>

                                        <div class="mb-2 d-none" id="commission_row">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label class="fw-medium">Commission Amount:</label>
                                                <div class="text-end">
                                                    <span id="commission_amount_display" class="fw-bold">0.00</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center border-top pt-2">
                                            <label for="taxable_amount" class="fw-medium">Net Amount (Before Tax):</label>
                                            <input type="text" id="taxable_amount" name="taxable_amount" class="form-control-plaintext text-end w-50 fw-bold" value="{{ old('taxable_amount', $purchaseOrder->taxable_amount ?? '') }}" readonly>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="fw-medium">Other State:</label>
                                            <div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input @error('other_state') is-invalid @enderror" type="radio" name="other_state" id="other_state_yes" value="yes" {{ old('other_state', $purchaseOrder && $purchaseOrder->other_state ? 'yes' : 'no') == 'yes' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="other_state_yes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input @error('other_state') is-invalid @enderror" type="radio" name="other_state" id="other_state_no" value="no" {{ old('other_state', $purchaseOrder && $purchaseOrder->other_state ? 'yes' : 'no') == 'no' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="other_state_no">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="round_off_type" id="round_off_type" value="{{ old('round_off_type', $purchaseOrder->round_off_type ?? 'Add') }}">

                                        <!-- Tax Fields -->
                                        <div class="igst-field {{ old('other_state', $purchaseOrder && $purchaseOrder->other_state ? 'yes' : 'no') == 'yes' ? '' : 'd-none' }}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label for="igst_percent" class="fw-medium">IGST :</label>
                                                <div class="input-group input-group-sm" style="width:120px;">
                                                    <input type="number" class="form-control form-control-sm text-end @error('igst_percent') is-invalid @enderror" id="igst_percent" name="igst_percent" step="0.01" min="0" max="100" value="{{ old('igst_percent', $purchaseOrder->igst_percent ?? $web_settings->igst) }}">
                                                    <span class="input-group-text px-1">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="cgst-field {{ old('other_state', $purchaseOrder && $purchaseOrder->other_state ? 'yes' : 'no') == 'no' ? '' : 'd-none' }}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label for="cgst_percent" class="fw-medium">CGST :</label>
                                                <div class="input-group input-group-sm" style="width:120px;">
                                                    <input type="number" class="form-control form-control-sm text-end @error('cgst_percent') is-invalid @enderror" id="cgst_percent" name="cgst_percent" step="0.01" min="0" max="100"
                                                        value="{{ old('cgst_percent', $purchaseOrder->cgst_percent ?? $web_settings->cgst) }}">
                                                    <span class="input-group-text px-1">%</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="sgst-field {{ old('other_state', $purchaseOrder && $purchaseOrder->other_state ? 'yes' : 'no') == 'no' ? '' : 'd-none' }} mt-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label for="sgst_percent" class="fw-medium">SGST :</label>
                                                <div class="input-group input-group-sm" style="width:120px;">
                                                    <input type="number" class="form-control form-control-sm text-end @error('sgst_percent') is-invalid @enderror" id="sgst_percent" name="sgst_percent" step="0.01" min="0" max="100"  value="{{ old('sgst_percent', $purchaseOrder->sgst_percent ?? $web_settings->sgst) }}">
                                                    <span class="input-group-text px-1">%</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="tax_amount" class="fw-medium">Tax Amount:</label>
                                            <input type="text" class="form-control-plaintext text-end w-50" id="tax_amount" name="tax_amount" value="{{ old('tax_amount',$purchaseOrder->tax_amount ?? '') }}" readonly>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="fw-medium">Round Off:</label>
                                            <div class="d-flex align-items-center">
                                                <div class="form-check form-check-inline me-2">
                                                    <input class="form-check-input" type="radio" name="round_off_type" id="round_off_add" value="Add" {{ old('round_off_type', $purchaseOrder->round_off_type ?? 'Add') == 'Add' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="round_off_add">Add</label>
                                                </div>
                                                <div class="form-check form-check-inline me-2">
                                                    <input class="form-check-input" type="radio" name="round_off_type" id="round_off_less" value="Less" {{ old('round_off_type', $purchaseOrder->round_off_type ?? 'Add') == 'Less' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="round_off_less">Less</label>
                                                </div>
                                                <input type="number" class="form-control form-control-sm text-end" style="width: 100px;" id="round_off" name="round_off" step="0.01" min="0" value="{{ old('round_off', $purchaseOrder->round_off ?? '') }}">
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                                            <label for="total_amount" class="fw-bold fs-5">Total Amount:</label>
                                            <input type="text" class="form-control-plaintext text-end w-50 fw-bold fs-5 text-primary" id="total_amount" name="total_amount" value="{{ old('total_amount', $purchaseOrder->total_amount ?? '') }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 text-end mt-4">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <a href="{{ url('purchase_orders') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
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
        $(document).on('click', '.add_item', function() {
            let rowHtml = `
            <tr class="item-row">
                <td>
                    <select class="select2 form-select po_store_category" name="items[${itemIndex}][store_category_id]" data-placeholder="Select Category">
                        <option value="">Select Category</option>
                        @foreach($storeCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->category_name }}({{ $category->code }})</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select class="select2 form-select brand" name="items[${itemIndex}][brand_id]" data-placeholder="Select Brand">
                        <option value="">Select Brand</option>
                        @foreach($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select class="select2 form-select material" name="items[${itemIndex}][raw_material_id]" data-placeholder="Select Material">
                        <option value="">Select Material</option>
                    </select>
                </td>
                <td>
                    <select class="select2 form-select style" name="items[${itemIndex}][style_id]" data-placeholder="Select Style">
                        <option value="">Select Style</option>
                        @foreach($styles as $style)
                        <option value="{{ $style->id }}">{{ $style->style_name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select class="select2 form-select fabric_width" name="items[${itemIndex}][fabric_width_id]" data-placeholder="Select Width">
                        <option value="">Select Width</option>
                        @foreach($sizeRatios as $ratio)
                        <option value="{{ $ratio->id }}">{{ $ratio->size }} - ({{ $ratio->ratio }})</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select class="select2 form-select uom" name="items[${itemIndex}][uom_id]" disabled data-placeholder="Select UOM">
                        <option value="">Select UOM</option>
                        @foreach($uoms as $uom)
                        <option value="{{ $uom->id }}">{{ $uom->uom_code }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="items[${itemIndex}][uom_id]" value="" class="uom_hidden">
                </td>
                <td>
                    <input type="number" class="form-control quantity" name="items[${itemIndex}][quantity]" step="0.01" min="0.01">
                </td>
                <td>
                    <input type="text" class="form-control art_no" name="items[${itemIndex}][art_no]">
                </td>
                <td>
                    <select class="select2 form-select color" name="items[${itemIndex}][color_id]" data-placeholder="Select Color">
                        <option value="">Select Color</option>
                        @foreach($colors as $color)
                        <option value="{{ $color->id }}">{{ $color->color_name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control rate" name="items[${itemIndex}][rate]" step="0.01" min="0">
                </td>
                <td>
                    <input type="text" class="form-control amount" readonly>
                </td>
                <td>
                    <textarea class="form-control remarks" name="items[${itemIndex}][remarks]" style="height: 58px;"></textarea>
                </td>
                <td>
                    <input type="file" class="form-control file-input" name="items[${itemIndex}][attached_file]" accept="image/jpeg,image/jpg,image/png,image/webp">
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

        $(document).on('change', '.po_store_category', function () {
            let category_id = $(this).val();
            let row = $(this).closest('tr');
            let materialSelect = row.find('.material');

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
                            let materialName = material.name;
                            if (typeof materialName === 'object' && materialName !== null) {
                                materialName = materialName.en || materialName.value || Object.values(materialName)[0] || JSON.stringify(materialName);
                            }
                            materialsHtml += `
                                <option value="${material.id}" data-uom-id="${material.uom_id}">
                                    ${materialName} (${material.code})
                                </option>`;
                        });
                    } else {
                        materialsHtml += '<option value="">No materials found</option>';
                    }

                    materialSelect.html(materialsHtml);
                    materialSelect.select2({
                        dropdownParent: materialSelect.parent(),
                        placeholder: materialSelect.data('placeholder'),
                        width: '100%'
                    });


                },
                error: function () {
                    materialSelect.html('<option value="">Select Material</option>').select2({
                        dropdownParent: materialSelect.parent(),
                        width: '100%'
                    });
                }
            });
        });

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

        });

        $(document).on('click', '.delete_item', function() {
            if ($('#item-rows tbody tr').length > 1) {
                $(this).closest('tr').remove();
                calculateTotals();

            } else {
                alert('At least one item is required');
            }
        });

        $(document).on('input', '.quantity, .rate', function() {
            let row = $(this).closest('tr');
            let qty = parseFloat(row.find('.quantity').val()) || 0;
            let rate = parseFloat(row.find('.rate').val()) || 0;
            row.find('.amount').val((qty * rate).toFixed(2));
            calculateTotals();
        });

        $('#discount_percent').on('input', function() {
            calculateTotals();
        });


        $(document).on('input', '#discount_percent, #igst_percent, #cgst_percent, #sgst_percent, #round_off, input[name="commission"]', function() {
            calculateTotals();
        });

        $(document).on('change', 'input[name="round_off_type"]', function() {
            calculateTotals();
        });

        $(document).on('change', 'input[name="other_state"]', function() {
            toggleTaxDivs();
            calculateTotals();
        });

        function calculateTotals() {
            let subTotal = 0;
            let totalQty = 0;

            $('.item-row').each(function() {
                let qty = parseFloat($(this).find('.quantity').val()) || 0;
                let rate = parseFloat($(this).find('.rate').val()) || 0;
                let amount = qty * rate;
                $(this).find('.amount').val(amount.toFixed(2));
                subTotal += amount;
                totalQty += qty;
            });

            $('#total_qty').val(totalQty.toFixed(2));
            $('#sub_total').val(subTotal.toFixed(2));

            let commissionPercent = parseFloat($('input[name="commission"]').val()) || 0;
            if (commissionPercent > 0) {
                let commissionAmount = (subTotal * commissionPercent) / 100;
                $('#commission_amount_display').text(commissionAmount.toFixed(2));
                $('#commission_row').removeClass('d-none');
            } else {
                $('#commission_row').addClass('d-none');
                $('#commission_amount_display').text('0.00');
            }

            let discountPercent = parseFloat($('#discount_percent').val()) || 0;
            let discountAmount = (subTotal * discountPercent) / 100;
            $('#discount_amount').val(discountAmount.toFixed(2));

            let taxableAmount = subTotal - discountAmount;
            
            $('#taxable_amount').val(taxableAmount.toFixed(2));

            let otherState = $('input[name="other_state"]:checked').val();
            let taxAmount = 0;

            if (otherState === 'yes') {
                let igstPercent = parseFloat($('#igst_percent').val()) || 0;
                taxAmount = (taxableAmount * igstPercent) / 100;
            } else {
                let cgstPercent = parseFloat($('#cgst_percent').val()) || 0;
                let sgstPercent = parseFloat($('#sgst_percent').val()) || 0;
                taxAmount = (taxableAmount * (cgstPercent + sgstPercent)) / 100;
            }

            $('#tax_amount').val(taxAmount.toFixed(2));

            let totalBeforeRoundOff = parseFloat((taxableAmount + taxAmount).toFixed(2));
            
            let roundOffAmount = parseFloat($('#round_off').val()) || 0;
            let roundOffType = $('input[name="round_off_type"]:checked').val();
            let finalTotal = 0;

            if (roundOffType === 'Add') {
                finalTotal = totalBeforeRoundOff + roundOffAmount;
            } else {
                finalTotal = totalBeforeRoundOff - roundOffAmount;
            }

            $('#total_amount').val(finalTotal.toFixed(2));
        }

        function toggleTaxDivs() {
            let otherState = $('input[name="other_state"]:checked').val();
            if (otherState === 'yes') {
                $('.igst-field').removeClass('d-none');
                $('.cgst-field, .sgst-field').addClass('d-none');
            } else {
                $('.igst-field').addClass('d-none');
                $('.cgst-field, .sgst-field').removeClass('d-none');
            }
        }

       $(document).on('click', '.view-image', function () {
        let imagePath = $(this).data('image');

        let imageSrc = imagePath.startsWith('http') || imagePath.startsWith('data:') || imagePath.startsWith('blob:')
            ? imagePath
            : APP_URL + imagePath;

        $('#modalImage').attr('src', imageSrc);
        $('#imageModal').modal('show');
    });

    $(document).on('change', '.file-input', function () {
        let file = this.files[0];
        let $container = $(this).closest('td, .file-container');
        
        // Remove ONLY dynamic previews (added by JS), keeping the server-rendered "View" link
        $container.find('.js-preview').remove();

        if (file) {
            let fileUrl = URL.createObjectURL(file);
            let fileType = file.type;

            if (fileType.startsWith('image/')) {
                 $container.append(`
                    <a href="javascript:void(0)" class="view-image mt-1 d-block js-preview" data-image="${fileUrl}">
                        <i class="ri ri-image-line"></i> View Selected Image
                    </a>
                `);
            } else if (fileType === 'application/pdf') {
                 $container.append(`
                    <a href="${fileUrl}" class="view-file mt-1 d-block js-preview" target="_blank">
                        <i class="ri ri-file-pdf-line"></i> View Selected PDF
                    </a>
                `);
            } else {
                 $container.append(`
                    <span class="text-muted small mt-1 d-block text-truncate js-preview">Selected: ${file.name}</span>
                `);
            }
        }
    });
    calculateTotals();
});
</script>
@endsection
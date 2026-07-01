@extends('layouts.common')
@section('title', ($salesOrder ? 'Edit' : 'Add') . ' Sales Order - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <form action="{{ $salesOrder ? url('sales_orders/add/' . $salesOrder->id) : url('sales_orders/add') }}" method="POST" enctype="multipart/form-data" class="common-form" autocomplete="off">
                @csrf
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ $salesOrder ? 'Edit' : 'Add' }} Sale Order</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('so_no') is-invalid @enderror" id="so_no" name="so_no" placeholder="SO Number" value="{{ old('so_no', $salesOrder->so_no ?? $nextSoNumber ?? '') }}">
                                    <label for="so_no">SO Number <span class="text-danger">*</span></label>
                                </div>
                                @error('so_no')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control so_date @error('so_date') is-invalid @enderror" id="so_date" name="so_date" placeholder="SO Date" value="{{ old('so_date', $salesOrder ? $salesOrder->so_date->format('d-m-Y') : date('d-m-Y')) }}">
                                    <label for="so_date">SO Date <span class="text-danger">*</span></label>
                                </div>
                                @error('so_date')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="order_type" name="order_type" class="select2 form-select">
                                        @foreach(['Regular'=>'Regular Order','Urgent'=>'Urgent Order','Special'=>'Special Order','Discount'=>'Discount Order', 'Phone' => 'Phone Order', 'Open' => 'Open Order'] as $val=>$label)
                                        <option value="{{ $val }}" {{ old('order_type', $salesOrder->order_type ?? 'Regular') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <label for="order_type">Order Type <span class="text-danger">*</span></label>
                                </div>
                                @error('order_type')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control request_date" id="request_date" name="request_date" placeholder="Request Date" value="{{ old('request_date', $salesOrder ? optional($salesOrder->request_date)->format('d-m-Y') : date('d-m-Y')) }}">
                                    <label for="request_date">Request Date</label>
                                </div>
                                @error('request_date')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control delivery_date" id="delivery_date" name="delivery_date" placeholder="Delivery Date" value="{{ old('delivery_date', $salesOrder && $salesOrder->delivery_date ? $salesOrder->delivery_date->format('d-m-Y') : '') }}">
                                    <label for="delivery_date">Delivery Date</label>
                                </div>
                                @error('delivery_date')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="season_id" name="season_id" class="select2 form-select" data-placeholder="Select Season">
                                        <option value="">Select Season</option>
                                        @foreach($seasons as $season)
                                        <option value="{{ $season->id }}" {{ old('season_id', $salesOrder->season_id ?? '') == $season->id ? 'selected' : '' }}>{{ $season->name }}({{ $season->season_code }})</option>
                                        @endforeach
                                    </select>
                                    <label for="season_id">Season</label>
                                </div>
                                @error('season_id')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="customer_id" name="customer_id" class="select2 form-select @error('customer_id') is-invalid @enderror" data-placeholder="Select Customer">
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" data-state-id="{{ $customer->state_id }}" data-zone-id="{{ $customer->zone_id }}" {{ old('customer_id', $salesOrder->customer_id ?? '') == $customer->id ? 'selected' : '' }}>{{ $customer->name }} ({{ $customer->code }})</option>
                                        @endforeach
                                    </select>
                                    <label for="customer_id">Customer <span class="text-danger">*</span></label>
                                </div>
                                @error('customer_id')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('customer_po_ref') is-invalid @enderror" id="customer_po_ref" name="customer_po_ref" placeholder="Customer PO Reference" value="{{ old('customer_po_ref', $salesOrder->customer_po_ref ?? '') }}">
                                    <label for="customer_po_ref">Customer PO Ref No</label>
                                </div>
                                @error('customer_po_ref')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="store_id" name="store_id" class="select2 form-select @error('store_id') is-invalid @enderror" data-placeholder="Select Store">
                                        <option value="">Select Store</option>
                                        @foreach($stores as $store)
                                        <option value="{{ $store->id }}" {{ old('store_id', $salesOrder->store_id ?? '') == $store->id ? 'selected' : '' }}>{{ $store->store_type_name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="store_id">Store <span class="text-danger">*</span></label>
                                </div>
                                @error('store_id')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="zone_id" name="zone_id" class="select2 form-select" data-placeholder="Select Zone">
                                        <option value="">Select Zone</option>
                                        @foreach($zones as $zone)
                                        <option value="{{ $zone->id }}" {{ old('zone_id', $salesOrder->zone_id ?? '') == $zone->id ? 'selected' : '' }}>{{ $zone->zone_name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="zone_id">Select Zone</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="agent_id" name="agent_id" class="select2 form-select" data-placeholder="Select Sales Executive">
                                        <option value="">Select Sales Executive</option>
                                        @foreach($sales_agent as $agent)
                                        <option value="{{ $agent->id }}" {{ old('agent_id', $salesOrder->agent_id ?? '') == $agent->id ? 'selected' : '' }}>{{ $agent->name }}({{ $agent->code }})</option>
                                        @endforeach
                                    </select>
                                    <label for="agent_id">Sales Executive</label>
                                </div> 
                            </div>

                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Billing &amp; Shipping Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control" id="billing_address" name="billing_address" placeholder="Billing Address" style="height: 100px;">{{ old('billing_address', $salesOrder->billing_address ?? '') }}</textarea>
                                    <label for="billing_address">Billing Address</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control" id="shipping_address" name="shipping_address" placeholder="Shipping Address" style="height: 100px;">{{ old('shipping_address', $salesOrder->shipping_address ?? '') }}</textarea>
                                    <label for="shipping_address">Shipping Address</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control" id="payment_terms" name="payment_terms" placeholder="Payment Terms" style="height: 100px;">{{ old('payment_terms', $salesOrder->payment_terms ?? '') }}</textarea>
                                    <label for="payment_terms">Payment Terms</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Logistics &amp; Destination</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="shipping_method_id" name="shipping_method_id" class="select2 form-select" data-placeholder="Select Shipping Method">
                                        <option value="">Select Shipping Method</option>
                                        @foreach($shippingMethods as $sm)
                                        <option value="{{ $sm->id }}" {{ old('shipping_method_id', $salesOrder->shipping_method_id ?? '') == $sm->id ? 'selected' : '' }}>{{ $sm->name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="shipping_method">Shipping Method</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="transport_mode_id" name="transport_mode_id" class="select2 form-select" data-placeholder="Select Transport Mode">
                                        <option value="">Select Transport Mode</option>
                                        @foreach($transportModes as $tm)
                                        <option value="{{ $tm->id }}" {{ old('transport_mode_id', $salesOrder->transport_mode_id ?? '') == $tm->id ? 'selected' : '' }}>{{ $tm->name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="transport_mode">Transport Mode</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="dispatch_from_id" name="dispatch_from_id" class="select2 form-select" data-placeholder="Select Dispatch From">
                                        <option value="">Select Dispatch From</option>
                                        @foreach($serviceProviders as $sp)
                                            <option value="{{ $sp->id }}" {{ old('dispatch_from_id', $salesOrder->dispatch_from_id ?? '') == $sp->id ? 'selected' : '' }}>{{ $sp->name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="dispatch_from_id">Dispatch From</label>
                                </div>
                            </div>
                            {{-- <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="freight_type" name="freight_type" class="select2 form-select" data-placeholder="Freight Type">
                                        <option value="">Select Freight Type</option>
                                        @foreach(['Paid','To Pay'] as $ft)
                                        <option value="{{ $ft }}" {{ old('freight_type', $salesOrder->freight_type ?? '') == $ft ? 'selected' : '' }}>{{ $ft }}</option>
                                        @endforeach
                                    </select>
                                    <label for="freight_type">Freight Type</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control" id="freight_amount" name="freight_amount" placeholder="Freight Amount" value="{{ old('freight_amount', $salesOrder->freight_amount ?? '0') }}" min="0" step="0.01" {{ old('freight_type', $salesOrder->freight_type ?? '') === 'Paid' ? '' : 'readonly' }}>
                                    <label for="freight_amount">Freight Amount</label>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Item Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6 text-center">
                                <div class="input-group mb-2 mx-auto">
                                    <div class="form-floating form-floating-outline flex-grow-1">
                                        <input type="text" id="global_item_search" class="form-control border-primary" placeholder="Scan Barcode" autocomplete="off" style="border-width: 2px; border-right: none; border-top-right-radius: 0; border-bottom-right-radius: 0;">
                                        <label for="global_item_search" class="text-primary fw-bold">SCAN BARCODE</label>
                                    </div>
                                    <button class="btn btn-outline-primary px-4" type="button" id="btn_camera_scan" style="border-width: 2px; border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                        <i class="ri-camera-line me-1"></i> CAMERA
                                    </button>
                                </div>
                                <div id="reader" class="rounded overflow-hidden mb-3 mx-auto" style="display: none; width: 100%; border: 1px solid #00bcd4;"></div>
                                <div id="scan_alert" class="alert alert-danger mt-3 mx-auto" style="display: none; max-width: 500px; text-align: left;">
                                    <span id="scan_msg"></span>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                                <small class="text-muted">Tip: Scan a barcode or type item code to quickly add it to the order.</small>
                            </div>
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered align-middle" id="item-rows">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width: 250px;">Stock Item *</th>
                                        <th style="min-width: 150px;">SKU</th>
                                        <th style="min-width: 150px;">Color</th>
                                        <th style="min-width: 100px;">Sleeve</th>
                                        <th style="min-width: 150px;">Art No *</th>
                                        <th style="min-width: 100px;">UOM *</th>
                                        <th style="min-width: 120px;">Size *</th>
                                        <th style="min-width: 120px;">Quantity *</th>
                                        <th style="min-width: 120px;">MRP *</th>
                                        <th style="min-width: 120px;">Selling Price *</th>

                                        <th style="min-width: 120px;">Amount</th>
                                        <th style="min-width: 50px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(old('items'))
                                        @foreach(old('items') as $index => $item)
                                            <tr class="item-row">
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" class="form-control stock-item-autocomplete" placeholder="Stock Item" value="{{ $item['stock_item_key'] ?? '' }}" autocomplete="off">
                                                        <input type="hidden" name="items[{{ $index }}][stock_item_key]" class="stock-item-select" value="{{ $item['stock_item_key'] ?? '' }}">
                                                        <label>Stock Item*</label>
                                                    </div>
                                                    @error("items.$index.stock_item_key")<div class="text-danger mt-1" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                                    <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item['item_id'] ?? '' }}">
                                                    <input type="hidden" name="items[{{ $index }}][brand_cat_id]" value="{{ $item['brand_cat_id'] ?? '' }}">
                                                    <input type="hidden" name="items[{{ $index }}][stock_entry_item_id]" class="stock-entry-item-id" value="{{ $item['stock_entry_item_id'] ?? '' }}">
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" name="items[{{ $index }}][sku]" class="form-control sku-input" placeholder="SKU" value="{{ $item['sku'] ?? '' }}" readonly tabindex="-1">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <select name="items[{{ $index }}][color_id]" class="select2 form-select @error("items.$index.color_id") is-invalid @enderror" data-placeholder="Color">
                                                            <option value="">Select Color</option>
                                                            @foreach($colors as $col)
                                                                <option value="{{ $col->id }}" {{ ($item['color_id'] ?? '') == $col->id ? 'selected' : '' }}>{{ $col->color_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    @error("items.$index.color_id")<div class="text-danger mt-1" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" name="items[{{ $index }}][sleeve]" class="form-control sleeve-input" placeholder="Sleeve" value="{{ $item['sleeve'] ?? '' }}" readonly>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" name="items[{{ $index }}][art_no]" class="form-control art-no-input @error("items.$index.art_no") is-invalid @enderror" placeholder="Art No" value="{{ $item['art_no'] ?? '' }}">
                                                    </div>
                                                    @error("items.$index.art_no")<div class="text-danger mt-1" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" class="form-control" value="PCS" readonly tabindex="-1">
                                                        <input type="hidden" name="items[{{ $index }}][uom_id]" value="PCS">
                                                        <label>UOM</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <select name="items[{{ $index }}][size_id]" class="form-select select2 size-select @error("items.$index.size_id") is-invalid @enderror" data-selected="{{ $item['size_id'] ?? '' }}">
                                                            <option value="">Select Size</option>
                                                            @foreach(['36','38','40','42','44'] as $s)
                                                                <option value="{{ $s }}" {{ ($item['size_id'] ?? '') == $s ? 'selected' : '' }}>{{ $s }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    @error("items.$index.size_id")<div class="text-danger mt-1" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="number" name="items[{{ $index }}][qty]" class="form-control qty-input @error("items.$index.qty") is-invalid @enderror" value="{{ $item['qty'] ?? 1 }}" min="0.01" step="0.01">
                                                        <div class="stock-info-wrapper mt-1">
                                                            <small class="stock-label text-muted">Stock: <span class="available-stock-display">0.00</span></small>
                                                            <div class="invalid-feedback stock-error-msg" style="display: none;">Exceeds!</div>
                                                        </div>
                                                    </div>
                                                    @error("items.$index.qty")<div class="text-danger mt-1" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="number" name="items[{{ $index }}][mrp]" class="form-control mrp-input @error("items.$index.mrp") is-invalid @enderror" placeholder="0.00" value="{{ $item['mrp'] ?? '' }}" step="0.01">
                                                    </div>
                                                    @error("items.$index.mrp")<div class="text-danger mt-1" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="number" name="items[{{ $index }}][rate]" class="form-control rate-input @error("items.$index.rate") is-invalid @enderror" placeholder="0.00" value="{{ $item['rate'] ?? '' }}" step="0.01">
                                                    </div>
                                                    @error("items.$index.rate")<div class="text-danger mt-1" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                                </td>

                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" name="items[{{ $index }}][amount]" class="form-control amount-input" value="{{ number_format(($item['qty'] ?? 0) * ($item['rate'] ?? 0), 2, '.', '') }}" readonly>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-danger btn-sm delete_row"><i class="ri ri-delete-bin-line"></i></button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @elseif(isset($salesOrder) && $salesOrder->items->count() > 0)
                                        @foreach($salesOrder->items as $index => $item) 
                                            <tr class="item-row">
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        @php 
                                                            $itemFinishedCode = $item->stockEntryItem ? $item->stockEntryItem->finished_item_code : ($item->item->code ?? '');
                                                            $stockItemKey = $item->sku ?: $itemFinishedCode;
                                                        @endphp
                                                        <input type="text" class="form-control stock-item-autocomplete" placeholder="Stock Item" value="{{ $itemFinishedCode }}" autocomplete="off">
                                                        <input type="hidden" name="items[{{ $index }}][stock_item_key]" class="stock-item-select" value="{{ $stockItemKey }}">
                                                        <label>Stock Item*</label>
                                                    </div>
                                                    <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item->item_id }}">
                                                    <input type="hidden" name="items[{{ $index }}][brand_cat_id]" value="{{ $item->brand_cat_id }}">
                                                    <input type="hidden" name="items[{{ $index }}][stock_entry_item_id]" class="stock-entry-item-id" value="{{ $item->stock_entry_item_id }}">
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" name="items[{{ $index }}][sku]" class="form-control sku-input" placeholder="SKU" value="{{ $item->sku }}" readonly tabindex="-1">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <select name="items[{{ $index }}][color_id]" class="select2 form-select" data-placeholder="Color">
                                                            <option value="">Select Color</option>
                                                            @foreach($colors as $col)
                                                                <option value="{{ $col->id }}" {{ $item->color_id == $col->id ? 'selected' : '' }}>{{ $col->color_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" name="items[{{ $index }}][sleeve]" class="form-control sleeve-input" placeholder="Sleeve" value="{{ is_array($item->sleeve) ? implode(', ', $item->sleeve) : $item->sleeve }}" readonly>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" name="items[{{ $index }}][art_no]" class="form-control art-no-input" placeholder="Art No" value="{{ $item->art_no }}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" class="form-control" value="PCS" readonly tabindex="-1">
                                                        <input type="hidden" name="items[{{ $index }}][uom_id]" value="PCS">
                                                        <label>UOM</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <select name="items[{{ $index }}][size_id]" class="form-select select2 size-select" data-selected="{{ $item->size_id }}">
                                                            <option value="">Select Size</option>
                                                            @foreach(['36','38','40','42','44'] as $s)
                                                                <option value="{{ $s }}" {{ $item->size_id == $s ? 'selected' : '' }}>{{ $s }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="number" name="items[{{ $index }}][qty]" class="form-control qty-input" value="{{ $item->qty }}" min="0.01" step="0.01">
                                                        <div class="stock-info-wrapper mt-1">
                                                            <small class="stock-label text-muted">Stock: <span class="available-stock-display">0.00</span></small>
                                                            <div class="invalid-feedback stock-error-msg" style="display: none;">Exceeds!</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="number" name="items[{{ $index }}][mrp]" class="form-control mrp-input" value="{{ $item->mrp }}" step="0.01">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="number" name="items[{{ $index }}][rate]" class="form-control rate-input" value="{{ $item->rate }}" step="0.01">
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" name="items[{{ $index }}][amount]" class="form-control amount-input" value="{{ number_format($item->qty * ($item->rate ?: 0), 2, '.', '') }}" readonly>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-danger btn-sm delete_row"><i class="ri ri-delete-bin-line"></i></button>
                                                        <input type="hidden" name="items[{{ $index }}][stock_entry_item_id]" class="stock-entry-item-id" value="{{ $item->stock_entry_item_id ?? '' }}">
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                    <tr class="item-row">
                                        <td>
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" class="form-control stock-item-autocomplete" placeholder="Stock Item" value="" autocomplete="off">
                                                <input type="hidden" name="items[0][stock_item_key]" class="stock-item-select" value="">
                                                <label>Stock Item*</label>
                                            </div>
                                            <input type="hidden" name="items[0][item_id]" value="">
                                            <input type="hidden" name="items[0][brand_cat_id]" value="">
                                            <input type="hidden" name="items[0][stock_entry_item_id]" class="stock-entry-item-id" value="">
                                        </td>
                                        <td>
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" name="items[0][sku]" class="form-control sku-input" placeholder="SKU" readonly>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-floating form-floating-outline">
                                                <select name="items[0][color_id]" class="select2 form-select" data-placeholder="Color">
                                                    <option value="">Select Color</option>
                                                    @foreach($colors as $col)
                                                        <option value="{{ $col->id }}">{{ $col->color_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" name="items[0][sleeve]" class="form-control sleeve-input" placeholder="Sleeve" readonly>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" name="items[0][art_no]" class="form-control art-no-input" placeholder="Art No">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" class="form-control" value="PCS" readonly tabindex="-1">
                                                <input type="hidden" name="items[0][uom_id]" value="PCS">
                                                <label>UOM</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-floating form-floating-outline">
                                                <select name="items[0][size_id]" class="form-select select2 size-select">
                                                    <option value="">Select Size</option>
                                                    @foreach(['36','38','40','42','44'] as $s)
                                                        <option value="{{ $s }}">{{ $s }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-floating form-floating-outline">
                                                <input type="number" name="items[0][qty]" class="form-control qty-input" value="1" min="0.01" step="0.01">
                                                <div class="stock-info-wrapper mt-1">
                                                    <small class="stock-label text-muted">Stock: <span class="available-stock-display">0.00</span></small>
                                                    <div class="invalid-feedback stock-error-msg" style="display: none;">Exceeds available stock!</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-floating form-floating-outline">
                                                <input type="number" name="items[0][mrp]" class="form-control mrp-input" placeholder="0.00" step="0.01">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-floating form-floating-outline">
                                                <input type="number" name="items[0][rate]" class="form-control rate-input" placeholder="0.00" step="0.01">
                                            </div>
                                        </td>

                                        <td>
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" name="items[0][amount]" class="form-control amount-input" value="0.00" readonly>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-danger btn-sm delete_row">
                                                    <i class="ri ri-delete-bin-line"></i>
                                                </button>
                                                <input type="hidden" name="items[0][stock_entry_item_id]" class="stock-entry-item-id" value="">
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                            <input type="hidden" id="itemIndex" value="{{ (old('items') ? count(old('items')) : (isset($salesOrder) ? $salesOrder->items->count() : 1)) }}">
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Tax &amp; Charges</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <select id="charges_select" class="select2 form-select @error('charges_select') is-invalid @enderror" data-placeholder="Select Charge">
                                        <option value="">Loading charges...</option>
                                    </select>
                                    <label>Charges </label>
                                </div>
                                @error('charges_select')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" min="0" step="0.01" class="form-control @error('charge_amount') is-invalid @enderror" id="charge_amount" placeholder="Charge Amount">
                                    <label>Amount</label>
                                </div>
                                @error('charge_amount')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <select id="charge_tax_type" class="form-select select2">
                                        <option value="Pre-GST">Pre-GST (Taxable)</option>
                                        <option value="Post-GST" selected>Post-GST (Non-Taxable)</option>
                                    </select>
                                    <label>Tax Type</label>
                                </div>
                            </div>

                            <div class="col-md-6 col-xl-2 d-flex align-items-center">
                                <button type="button" id="add_charge_btn" class="btn btn-primary w-100">Add Charge</button>
                            </div>

                        </div>
                        <div class="table-responsive mt-4 {{ (isset($charges) && $charges->count() || (old('charges') && isset(old('charges')['charge_id']))) ? '' : 'd-none' }}" id="charges_table">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Charge Name</th>
                                        <th>Tax Type</th>
                                        <th>Amount</th>
                                        <th width="120px">Action</th>
                                    </tr>
                                </thead>

                                <tbody id="added_charges_list">
                                    @php
                                        $oldCharges = old('charges');
                                        $chargesToLoop = [];

                                        if ($oldCharges && isset($oldCharges['charge_id'])) {
                                            foreach ($oldCharges['charge_id'] as $index => $id) {
                                                $chargesToLoop[] = (object) [
                                                    'charge_id' => $id,
                                                    'charge_name' => $oldCharges['name'][$index] ?? '',
                                                    'charge_amount' => $oldCharges['amount'][$index] ?? 0,
                                                    'id' => null
                                                ];
                                            }
                                        } else {
                                            $chargesToLoop = isset($charges) ? $charges : collect();
                                        }
                                    @endphp

                                    @foreach($chargesToLoop as $charge)
                                        @php
                                            $chargeId = is_array($charge) ? ($charge['charge_id'] ?? '') : $charge->charge_id;
                                            $chargeName = is_array($charge) ? ($charge['name'] ?? '') : ($charge->charge_name ?? $charge->name ?? '');
                                            $chargeAmount = is_array($charge) ? ($charge['amount'] ?? 0) : ($charge->charge_amount ?? $charge->amount ?? 0);
                                            $taxType = is_array($charge) ? ($charge['tax_type'] ?? 'Post-GST') : ($charge->tax_type ?? 'Post-GST');
                                            $invoiceChargeId = is_array($charge) ? ($charge['id'] ?? null) : ($charge->id ?? null);
                                        @endphp

                                        <tr class="charge-row" data-charge-id="{{ $chargeId }}" data-invoice-charge-id="{{ $invoiceChargeId }}" data-tax-type="{{ $taxType }}">
                                            <td>
                                                {{ $chargeName }}
                                                <input type="hidden" name="charges[charge_id][]" value="{{ $chargeId }}">
                                                <input type="hidden" name="charges[name][]" value="{{ $chargeName }}">
                                            </td>
                                            <td>
                                                {{ $taxType }}
                                                <input type="hidden" name="charges[tax_type][]" value="{{ $taxType }}">
                                            </td>
                                            <td>
                                                {{ number_format($chargeAmount, 2) }}
                                                <input type="hidden" name="charges[amount][]" value="{{ $chargeAmount }}">
                                            </td>
                                            <td class="d-flex align-items-center">
                                                <button type="button" class="btn btn-outline-success btn-sm edit-charge me-1" title="Edit Charge">
                                                    <i class="ri ri-pencil-line"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-charge" title="Delete Charge" {{ isset($salesOrder) ? 'disabled' : '' }}>
                                                    <i class="ri ri-delete-bin-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Additional Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <select id="status" name="status" class="select2 form-select">
                                                @php
                                                    $currentStatus = $salesOrder->status ?? 'Draft';
                                                    $allStatuses = ['Draft', 'Pending', 'Approved', 'Rejected'];
                                                @endphp
                                                @foreach($allStatuses as $st)
                                                    @php
                                                        $disabled = '';
                                                        if ($currentStatus === 'Draft') {
                                                            if (!in_array($st, ['Draft', 'Pending', 'Approved', 'Rejected'])) $disabled = 'disabled';
                                                        } elseif ($currentStatus === 'Pending') {
                                                            if (!in_array($st, ['Draft', 'Pending', 'Approved', 'Rejected'])) $disabled = 'disabled';
                                                        } elseif ($currentStatus === 'Approved' || $currentStatus === 'Rejected') {
                                                            if ($st !== $currentStatus) $disabled = 'disabled';
                                                        }
                                                    @endphp
                                                    <option value="{{ $st }}" {{ old('status', $currentStatus) == $st ? 'selected' : '' }} {{ $disabled }}>{{ $st }}</option>
                                                @endforeach
                                             </select>
                                            <label for="status">Order Status <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <textarea class="form-control h-px-100" id="terms_conditions" name="terms_conditions" rows="3" placeholder="Terms & Conditions">{{ old('terms_conditions', $salesOrder->terms_conditions ?? $web_settings->terms_and_conditions ?? '') }}</textarea>
                                            <label for="terms_conditions">Terms & Conditions</label>
                                        </div>
                                        @error('terms_conditions')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <textarea class="form-control" id="internal_remarks" name="internal_remarks" rows="2" placeholder="Internal Notes">{{ old('internal_remarks', $salesOrder->internal_remarks ?? '') }}</textarea>
                                            <label for="internal_remarks">Internal Notes</label>
                                        </div>
                                        @error('internal_remarks')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <input type="file" class="form-control @error('attachment.*') is-invalid @enderror" id="attachment" name="attachment[]" multiple>
                                            <label for="attachment">Attachments</label>
                                        </div>
                                        <small class="text-muted d-block mt-1">Max file size: 2MB. Supported formats: JPG, PNG, JPEG, WEBP, PDF, DOC, DOCX</small>
                                        @if($salesOrder && $salesOrder->attachment)
                                        <div class="mt-2 d-flex flex-wrap gap-3">
                                            @foreach(explode(',', $salesOrder->attachment) as $file)
                                                @php
                                                    $ext = pathinfo($file, PATHINFO_EXTENSION);
                                                    $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                    $fileUrl = url('uploads/so/' . $salesOrder->id . '/' . $file);
                                                @endphp
                                                <div class="p-1 border rounded bg-light shadow-sm d-flex align-items-center">
                                                    @if($isImage)
                                                        <img src="{{ $fileUrl }}" class="rounded cursor-pointer view-image" data-image="{{ $fileUrl }}" width="45" height="45" style="object-fit: cover;" alt="Attachment">
                                                    @else
                                                        <a href="{{ $fileUrl }}" target="_blank" class="text-decoration-none d-flex align-items-center px-2">
                                                            @if(strtolower($ext) == 'pdf')
                                                                <i class="ri ri-file-pdf-2-line text-danger fs-3"></i>
                                                            @else
                                                                <i class="ri ri-file-text-fill text-primary fs-3"></i>
                                                            @endif
                                                        </a>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                        @endif
                                        @error('attachment.*')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="card-header-box d-flex justify-content-between align-items-center mb-4">
                                    <h4>Tax Summary</h4>
                                </div>
                                <div class="row g-4">
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <label class="fw-bold">Sales Discount:</label>
                                            <div class="input-group input-group-sm" style="width:100px;">
                                                <input type="number" class="form-control form-control-sm text-end" id="sales_discount_percent" name="sales_discount_percent" step="0.01" min="0" max="100" value="{{ old('sales_discount_percent', $salesOrder->sales_discount_percent ?? '0') }}">
                                                <span class="input-group-text px-1">%</span>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <label class="fw-bold">Box Discount:</label>
                                            <div class="input-group input-group-sm" style="width:100px;">
                                                <span class="input-group-text px-1">₹</span>
                                                <input type="number" class="form-control form-control-sm text-end" id="box_discount_amount" name="box_discount_amount" step="0.01" min="0" value="{{ old('box_discount_amount', $salesOrder->box_discount_amount ?? '0') }}">
                                            </div>
                                        </div>
                                        <input type="hidden" name="apply_box_discount" id="apply_box_discount_hidden" value="1">
                                        <input type="hidden" id="customer_sales_discount" value="0">
                                        <input type="hidden" id="customer_box_discount" value="0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="fw-medium">Total Qty:</label>
                                            <input type="text" class="form-control-plaintext text-end w-50 fw-bold" id="total_qty" name="total_qty" value="{{ old('total_qty', $salesOrder->total_qty ?? '0.00') }}" readonly>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="fw-medium">Sub Total:</label>
                                            <input type="text" class="form-control-plaintext text-end w-50 fw-bold" id="sub_total_qty" name="sub_total_qty" value="{{ old('sub_total_qty', $salesOrder->sub_total_qty ?? '0.00') }}" readonly>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="fw-medium">Discount Amount:</label>
                                            <input type="text" class="form-control-plaintext text-end w-50 fw-bold" id="discount_amount" name="discount_amount" value="{{ old('discount_amount', $salesOrder->discount_amount ?? '0.00') }}" readonly>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="fw-medium">Pre-GST Charges:</label>
                                            <input type="text" id="pre_gst_total_input" name="pre_gst_total" class="form-control-plaintext text-end w-50 fw-bold" value="0.00" readonly>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center border-top mt-2 pt-2">
                                            <label class="fw-medium">Net Amount (Before Tax):</label>
                                            <input type="text" id="taxable_amount" name="taxable_amount" class="form-control-plaintext text-end w-50 fw-bold" value="{{ old('taxable_amount', $salesOrder->taxable_amount ?? '0.00') }}" readonly>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="fw-medium">Other State:</label>
                                            <div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="other_state" id="other_state_yes" value="yes" {{ old('other_state', $salesOrder && $salesOrder->other_state ? 'yes' : 'no') == 'yes' ? 'checked' : '' }} onclick="return false;">
                                                    <label class="form-check-label" for="other_state_yes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="other_state" id="other_state_no" value="no" {{ old('other_state', $salesOrder && $salesOrder->other_state ? 'yes' : 'no') == 'no' ? 'checked' : '' }} onclick="return false;">
                                                    <label class="form-check-label" for="other_state_no">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="igst-field {{ old('other_state', $salesOrder && $salesOrder->other_state ? 'yes' : 'no') == 'yes' ? '' : 'd-none' }}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label class="fw-medium">IGST:</label>
                                                <div class="input-group input-group-sm" style="width:120px;">
                                                    <input type="number" class="form-control form-control-sm text-end" id="igst_percent" name="igst_percent" step="0.01" min="0" max="100" value="{{ old('igst_percent', $salesOrder->igst_percent ?? (!empty($web_settings->igst) ? $web_settings->igst : '')) }}">
                                                    <span class="input-group-text px-1">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cgst-field {{ old('other_state', $salesOrder && $salesOrder->other_state ? 'yes' : 'no') == 'no' ? '' : 'd-none' }}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label class="fw-medium">CGST:</label>
                                                <div class="input-group input-group-sm" style="width:120px;">
                                                    <input type="number" class="form-control form-control-sm text-end" id="cgst_percent" name="cgst_percent" step="0.01" min="0" max="100" value="{{ old('cgst_percent', $salesOrder->cgst_percent ?? (!empty($web_settings->cgst) ? $web_settings->cgst : '')) }}">
                                                    <span class="input-group-text px-1">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="sgst-field {{ old('other_state', $salesOrder && $salesOrder->other_state ? 'yes' : 'no') == 'no' ? '' : 'd-none' }} mt-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label class="fw-medium">SGST:</label>
                                                <div class="input-group input-group-sm" style="width:120px;">
                                                    <input type="number" class="form-control form-control-sm text-end" id="sgst_percent" name="sgst_percent" step="0.01" min="0" max="100" value="{{ old('sgst_percent', $salesOrder->sgst_percent ?? (!empty($web_settings->sgst) ? $web_settings->sgst : '')) }}">
                                                    <span class="input-group-text px-1">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="fw-medium">Tax Amount:</label>
                                            <input type="text" class="form-control-plaintext text-end w-50" id="tax_amount" name="tax_amount" value="{{ old('tax_amount', $salesOrder->tax_amount ?? '0.00') }}" readonly>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <label class="fw-medium">Post-GST Charges:</label>
                                            <input type="text" id="other_charges_input" name="other_charges" class="form-control-plaintext text-end w-50 fw-bold" value="{{ old('other_charges', $salesOrder->other_charges ?? '0.00') }}" readonly>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <label class="fw-medium">Total Before Round Off:</label>
                                            <input type="text" id="total_before_round_off" class="form-control-plaintext text-end w-50 fw-bold" value="0.00" readonly>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="fw-medium">Round Off:</label>
                                            <div class="d-flex align-items-center">
                                                <input type="hidden" name="round_off_type" id="round_off_type" value="{{ old('round_off_type', $salesOrder->round_off_type ?? 'Add') }}">
                                                <input type="text" class="form-control-plaintext form-control-sm text-end fw-bold" style="width:100px;" id="round_off_display" value="0.00" readonly>
                                                <input type="hidden" id="round_off" name="round_off" value="{{ old('round_off', $salesOrder->round_off ?? '0.00') }}">
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                                            <label class="fw-bold fs-5">Total Amount:</label>
                                            <input type="text" class="form-control-plaintext text-end w-50 fw-bold fs-5 text-primary" id="total_amount" name="total_amount" value="{{ old('total_amount', $salesOrder->total_amount ?? '0.00') }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ url('sales_orders') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
    .segmented-control {
        display: inline-flex;
        background: #f4f5fb;
        border-radius: 10px;
        padding: 4px;
        border: 1px solid #e0e2ef;
    }
    .segmented-control input[type="radio"] {
        display: none;
    }
    .segmented-control label {
        padding: 5px 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        margin-bottom: 0;
        font-weight: 600;
        font-size: 13px;
        color: #6d6f89;
        text-align: center;
    }
    .segmented-control input[type="radio"]:checked + label {
        background: var(--bs-primary);
        color: var(--bs-white);
    }
    .segmented-control label:hover:not(.segmented-control input[type="radio"]:checked + label) {
        background: #eaeaef;
    }
    .stock-info-wrapper {
        line-height: 1;
        min-height: 15px;
    }
    .qty-input.is-invalid {
        border-color: #ff4d49 !important;
    }
    .stock-error-msg {
        font-size: 0.7rem;
        font-weight: 600;
    }
    .ui-autocomplete {
        z-index: 10000 !important;
        max-height: 300px;
        overflow-y: auto;
        overflow-x: hidden;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        border: 1px solid #e0e2ef;
    }
    .ui-menu-item {
        border-bottom: 1px solid #f0f1f8;
    }
    .ui-menu-item:last-child {
        border-bottom: none;
    }
    .ui-menu-item .ui-menu-item-wrapper {
        padding: 10px 15px !important;
        transition: all 0.2s;
    }
    .ui-menu-item .ui-menu-item-wrapper.ui-state-active {
        background-color: #f4f5fb !important;
        color: var(--bs-primary) !important;
        border: none !important;
        margin: 0 !important;
    }
    .search-item-title {
        font-weight: 700;
        color: #323452;
        margin-bottom: 2px;
        display: block;
    }
    .search-item-info {
        font-size: 12px;
        color: #6d6f89;
    }
    .search-item-balance {
        float: right;
        font-weight: 600;
        color: var(--bs-success);
    }

</style>
@endsection
@section('scripts')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
$(document).ready(function () {
    let itemIndex = Number($('#itemIndex').val()) || 0;

    $('.so_date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });
    $('.request_date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });

    $('.select2').each(function() {
        $(this).select2({ dropdownParent: $(this).closest('.card-body').length ? $(this).closest('.card-body') : $('body') });
    });

    $('#customer_id').on('change', function() {
        let zoneId = $(this).find(':selected').data('zone-id');
        if (zoneId) {
            $('#zone_id').val(zoneId).trigger('change');
        } else {
            $('#zone_id').val('').trigger('change');
        }
        
        let customerId = $(this).val();
        if (customerId) {
            $.ajax({
                url: `{{ url('get-customer-details') }}/${customerId}`,
                type: 'GET',
                success: function(res) {
                    if (res.success) {
                        $('#customer_box_discount').val(res.box_discount || 0);
                        $('#customer_sales_discount').val(res.sales_discount || 0);
                        $('#sales_discount_percent').val(res.sales_discount || 0);
                        $('#box_discount_amount').val(res.box_discount_amount || 0);
                        calculateTotals();
                    }
                }
            });
        }
    });

    function createRow() {
        let colorOpts = `<option value="">Select Color</option>`;
        @foreach($colors as $col)
        colorOpts += `<option value="{{ $col->id }}">{{ addslashes($col->color_name) }}</option>`;
        @endforeach


        let rowHtml = `
            <tr class="item-row">
                <td>
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control stock-item-autocomplete" placeholder="Stock Item" value="" autocomplete="off">
                        <input type="hidden" name="items[${itemIndex}][stock_item_key]" class="stock-item-select" value="">
                        <label>Search Stock Item (Code/SKU) *</label>
                    </div>
                    <input type="hidden" name="items[${itemIndex}][item_id]" value="">
                    <input type="hidden" name="items[${itemIndex}][brand_cat_id]" value="">
                    <input type="hidden" name="items[${itemIndex}][stock_entry_item_id]" class="stock-entry-item-id" value="">
                </td>
                <td>
                    <div class="form-floating form-floating-outline">
                        <input type="text" name="items[${itemIndex}][sku]" class="form-control sku-input" placeholder="SKU" readonly>
                    </div>
                </td>
                <td>
                    <div class="form-floating form-floating-outline">
                        <select name="items[${itemIndex}][color_id]" class="select2 form-select color-select" data-placeholder="Color">${colorOpts}</select>
                    </div>
                </td>
                <td>
                    <div class="form-floating form-floating-outline">
                        <input type="text" name="items[${itemIndex}][sleeve]" class="form-control sleeve-input" placeholder="Sleeve" readonly>
                    </div>
                </td>
                <td>
                    <div class="form-floating form-floating-outline">
                        <input type="text" name="items[${itemIndex}][art_no]" class="form-control art-no-input" placeholder="Art No">
                    </div>
                </td>
                <td>
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control" value="PCS" readonly tabindex="-1">
                        <input type="hidden" name="items[${itemIndex}][uom_id]" value="PCS">
                        <label>UOM</label>
                    </div>
                </td>
                <td>
                    <div class="form-floating form-floating-outline">
                        <select name="items[${itemIndex}][size_id]" class="select2 form-select size-select" data-placeholder="Size">
                            <option value="">Select Size</option>
                            <option value="36">36</option>
                            <option value="38">38</option>
                            <option value="40">40</option>
                            <option value="42">42</option>
                            <option value="44">44</option>
                        </select>
                    </div>
                </td>
                <td>
                    <div class="form-floating form-floating-outline">
                        <input type="number" name="items[${itemIndex}][qty]" class="form-control qty-input" value="1" min="0.01" step="0.01">
                        <div class="stock-info-wrapper mt-1">
                            <small class="stock-label text-muted">Stock: <span class="available-stock-display">0.00</span></small>
                            <div class="invalid-feedback stock-error-msg" style="display: none;">Exceeds!</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="form-floating form-floating-outline">
                        <input type="number" name="items[${itemIndex}][mrp]" class="form-control mrp-input" min="0" step="0.01" placeholder="0.00">
                    </div>
                </td>
                <td>
                    <div class="form-floating form-floating-outline">
                        <input type="number" name="items[${itemIndex}][rate]" class="form-control rate-input" min="0" step="0.01" placeholder="0.00">
                    </div>
                </td>
                <td>
                    <div class="form-floating form-floating-outline">
                        <input type="text" name="items[${itemIndex}][amount]" class="form-control amount-input" value="0.00" readonly>
                    </div>
                </td>
                <td>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-danger btn-sm delete_row"><i class="ri ri-delete-bin-line"></i></button>
                        <input type="hidden" name="items[${itemIndex}][stock_entry_item_id]" class="stock-entry-item-id" value="">
                    </div>
                </td>
            </tr>`;

        $('#item-rows tbody').append(rowHtml);

        $('#item-rows tr:last .select2').each(function() {
            if ($(this).hasClass("select2-hidden-accessible")) {
                $(this).select2('destroy');
            }
            $(this).select2({ dropdownParent: $(this).closest('.card-body').length ? $(this).closest('.card-body') : $('body') });
        });

        itemIndex++;
    }

    function initStockItemAutocomplete($el) {
        const isGlobalSearch = $el.attr('id') === 'global_item_search';

        $el.autocomplete({
            source: function(request, response) {
                let codeToSearch = request.term;
                if (codeToSearch) {
                    codeToSearch = codeToSearch.split('|')[0].trim();
                }
                $.getJSON("{{ url('sales_orders/search-stock-items') }}", {
                    term: codeToSearch
                }, function(data) {
                    const results = Array.isArray(data) ? data : [];

                    if (isGlobalSearch && request.term && results.length === 0) {
                        response([{
                            label: 'Barcode not found',
                            value: '',
                            noResult: true
                        }]);
                        return;
                    }

                    response(results);
                });
            },
            minLength: 1,
            select: function(event, ui) {
                let $this = $(this);
                let $row = $this.closest('.item-row');

                if (ui.item && ui.item.noResult) {
                    event.preventDefault();
                    return false;
                }
                
                if (ui.item) {
                    if ($row.length) {
                        $this.val(ui.item.label);
                        $row.find('.stock-item-select').val(ui.item.value).trigger('change');
                    } else if ($this.attr('id') === 'global_item_search') {
                        $.ajax({
                            url: `{{ url('get-finished-item-stock') }}?code=${encodeURIComponent(ui.item.value)}&so_id={{ $salesOrder->id ?? '' }}`,
                            type: 'GET',
                            success: function(res) {
                                if (res.success) {
                                    handleGlobalItemSelection(res);
                                    $this.val('').focus();
                                } else {
                                    alert("Failed to fetch item details. Please try again.");
                                    $this.val('').focus();
                                }
                            },
                            error: function() {
                                alert("Error fetching item stock. Please check your connection.");
                            }
                        });
                    }
                }
                return false;
            }
        }).autocomplete("instance")._renderItem = function(ul, item) {
            if (item.noResult) {
                return $("<li>")
                    .append(`<div class="ui-menu-item-wrapper text-danger fw-bold">Barcode not found</div>`)
                    .appendTo(ul);
            }

            let skuInfo = item.sku ? ` | SKU: ${item.sku}` : '';
            return $("<li>")
                .append(`<div class="ui-menu-item-wrapper">
                    <span class="search-item-title">${item.label}</span>
                    <span class="search-item-balance">Stock: ${parseFloat(item.balance).toFixed(2)}</span>
                    <div class="search-item-info">
                        Art No: ${item.art_no || '-'} ${skuInfo} | Price: ₹${parseFloat(item.price).toFixed(2)}
                    </div>
                </div>`)
                .appendTo(ul);
        };

        $el.on('keydown', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                let $this = $(this);
                let val = $this.val();
                if (val && $this.attr('id') !== 'global_item_search') {
                    let codeToSearch = val.split('|')[0].trim();
                    $.ajax({
                        url: `{{ url('get-finished-item-stock') }}?code=${encodeURIComponent(codeToSearch)}&so_id={{ $salesOrder->id ?? '' }}`,
                        type: 'GET',
                        success: function(res) {
                            if (res.success) {
                                populateRowData($this.closest('.item-row'), res);
                            }
                        }
                    });
                }
            }
        });
    }


    initStockItemAutocomplete($('#global_item_search'));
    
    $('#global_item_search').on('keydown', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            let val = $(this).val();
            if (val) {
                let codeToSearch = val.split('|')[0].trim();
                $.ajax({
                    url: `{{ url('get-finished-item-stock') }}?code=${encodeURIComponent(codeToSearch)}&so_id={{ $salesOrder->id ?? '' }}`,
                    type: 'GET',
                    success: function(res) {
                        if (res.success) {
                            handleGlobalItemSelection(res);
                            $('#global_item_search').val('').focus();
                        } else {
                            alert("Item not found or out of stock.");
                        }
                    }
                });
            }
        }
    });

    function handleGlobalItemSelection(res) {
        let $existing = $('.item-row').filter(function() {
            return $(this).find('.sku-input').val() === res.sku &&
                $(this).find('.size-select').val() == res.size &&
                $(this).find('select[name*="[color_id]"]').val() == res.color_id;
        }).first();

        if ($existing.length) {
            let $qty = $existing.find('.qty-input');
            let newQty = parseFloat($qty.val()) + 1;
            let available = parseFloat($existing.find('.available-stock-display').text()) || 0;

            if (newQty > available) {
                Swal.fire({ icon: 'warning', title: 'Stock Limit', text: 'Only ' + available + ' in stock.', timer: 2000, showConfirmButton: false });
            } else {
                $qty.val(newQty).trigger('input');
            }
            $('#global_item_search').val('').focus();
            return;
        }

        let $target = $('.item-row').filter(function() {
            return !$(this).find('.stock-item-select').val();
        }).first();

        if (!$target.length) { createRow(); $target = $('.item-row').last(); }
        populateRowData($target, res);
        $('#global_item_search').focus();
    }

    function populateRowData($row, res) {
        if (!$row || !$row.length) return;
        
        let displayName = res.finished_item_code;
        if (res.item_name) {
            displayName += ' - ' + res.item_name;
        }
        $row.find('.stock-item-autocomplete').val(displayName);
        $row.find('.stock-item-select').val(res.sku || res.finished_item_code);
        $row.find('input[name*="[item_id]"]').val(res.item_id);
        $row.find('input[name*="[brand_cat_id]"]').val(res.brand_cat_id);
        $row.find('.stock-entry-item-id').val(res.stock_entry_item_id);
        $row.find('.sku-input').val(res.sku);
        
        if ($row.find('select[name*="[color_id]"]').val() != res.color_id) {
            $row.find('select[name*="[color_id]"]').val(res.color_id).trigger('change');
        }
        
        $row.find('.sleeve-input').val(res.sleeve_type);
        $row.find('.art-no-input').val(res.art_no);
        
        let mrp = parseFloat(res.mrp || 0);
        let price = parseFloat(res.price || 0);
        
        $row.data('size-prices', res.size_prices);
        $row.data('default-mrp', mrp);
        $row.data('default-price', price);
        
        if (!$row.find('.mrp-input').val() || parseFloat($row.find('.mrp-input').val()) == 0) {
            $row.find('.mrp-input').val(mrp.toFixed(2));
        }
        if (!$row.find('.rate-input').val() || parseFloat($row.find('.rate-input').val()) == 0) {
            $row.find('.rate-input').val(price.toFixed(2));
        }
        
        const sizeList = ['36', '38', '40', '42', '44'];
        let sizeOpts = `<option value="">Select Size</option>`;
        sizeList.forEach(s => {
            sizeOpts += `<option value="${s}">${s}</option>`;
        });
        let $sizeSelect = $row.find('.size-select');
        $sizeSelect.html(sizeOpts);
        
        $row.data('size-stock', res.size_stock);
        if (res.size && $sizeSelect.val() != res.size) {
            $sizeSelect.val(res.size).trigger('change');
        } else {
            updateStockAndRate($row);
        }
        
        $row.find('.qty-input').trigger('input');
        if (typeof calculateTotals === 'function') {
            calculateTotals();
        }
    }

    $(document).on('focus', '.stock-item-autocomplete', function() {
        if (!$(this).hasClass('ui-autocomplete-input')) {
            initStockItemAutocomplete($(this));
        }
    });

    $('.add_item').on('click', createRow);
    
    $('#customer_id').on('change', function() {
        let customerId = $(this).val();
        if (customerId) {
            $.ajax({
                url: `{{ url('get-customer-details') }}/${customerId}`,
                type: 'GET',
                success: function(res) {
                    if (res.success && res.customer) {
                        let c = res.customer;
                        let parts = [c.address_line_1, c.address_line_2, c.address_line_3, c.city, c.state, c.pincode].filter(Boolean).map(s => s.trim());
                        let uniqueParts = [];
                        for (let part of parts) {
                            let lowerPart = part.toLowerCase();
                            let alreadyExists = false;
                            for (let existing of uniqueParts) {
                                let lowerExisting = existing.toLowerCase();
                                if (lowerExisting === lowerPart) {
                                    alreadyExists = true;
                                    break;
                                }
                                if (/^[a-zA-Z0-9]+$/.test(part) && new RegExp('\\b' + lowerPart + '\\b').test(lowerExisting)) {
                                    alreadyExists = true;
                                    break;
                                }
                            }
                            if (!alreadyExists) {
                                uniqueParts.push(part);
                            }
                        }
                        let billingAddress = uniqueParts.join(', ');
                        $('#billing_address').val(billingAddress);
                        $('#shipping_address').val(billingAddress);
                        if (c.payment_terms) $('#payment_terms').val(c.payment_terms);
                        if (c.transport_name) $('#transporter_name').val(c.transport_name);
                        
                        $('#customer_box_discount').val(c.box_discount || 0);
                        $('#customer_sales_discount').val(c.sales_discount || 0);
                        $('#sales_discount_percent').val(c.sales_discount || 0);
                        $('#box_discount_amount').val(c.box_discount_amount || 0);

                        let customerStateId = c.state_id;
                        let companyStateId = "{{ $web_settings->state_id ?? '' }}";

                        if (customerStateId && companyStateId) {
                            if (customerStateId == companyStateId) {
                                $('#other_state_no').prop('checked', true).trigger('change');
                                $('#cgst_percent').val("{{ $web_settings->cgst ?? 0 }}");
                                $('#sgst_percent').val("{{ $web_settings->sgst ?? 0 }}");
                                $('#igst_percent').val(0);
                            } else {
                                $('#other_state_yes').prop('checked', true).trigger('change');
                                $('#igst_percent').val("{{ $web_settings->igst ?? 0 }}");
                                $('#cgst_percent').val(0);
                                $('#sgst_percent').val(0);
                            }
                        }
                        calculateTotals();
                    }
                }
            });
        }
    });

    $('#freight_amount').on('input', function() {
        calculateTotals();
    });

    $(document).on('click', '.delete_row', function() {
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
            calculateTotals();
        } else {
            alert("At least one item is required.");
        }
    });

    $(document).on('change', '.stock-item-select', function() {
        let code = $(this).val();
        let $row = $(this).closest('.item-row');
        
        if (code) {
            $.ajax({
                url: `{{ url('get-finished-item-stock') }}?code=${encodeURIComponent(code)}&so_id={{ $salesOrder->id ?? '' }}`,
                type: 'GET',
                success: function(res) {
                    if (res.success) {
                        populateRowData($row, res);
                    }
                }
            });
        } else {
            $row.find('input[name*="[item_id]"]').val('');
            $row.find('input[name*="[brand_cat_id]"]').val('');
            $row.find('select[name*="[color_id]"]').val('').trigger('change');
            $row.find('.sku-input').val('');
            $row.find('.sleeve-input').val('');
            $row.find('.art-no-input').val('');
            $row.find('.available-stock-display').text('0.00');
            $row.find('.mrp-input').val('');
            $row.find('.rate-input').val('');
            $row.find('.size-select').html('<option value="">Select Size</option>').trigger('change');
            $row.data('size-stock', {});
            calculateTotals();
        }
    });

    function updateStockAndRate($row) {
        let size = $row.find('.size-select').val();
        let sizeStock = $row.data('size-stock') || {};
        let balance = sizeStock[size] || 0;
        
        $row.find('.available-stock-display').text(parseFloat(balance).toFixed(2));

        let sizePrices = $row.data('size-prices') || {};
        if (sizePrices && sizePrices[size] !== undefined) {
            $row.find('.mrp-input').val(parseFloat(sizePrices[size].mrp).toFixed(2));
            $row.find('.rate-input').val(parseFloat(sizePrices[size].price).toFixed(2));
        } else {
            let defaultMrp = parseFloat($row.data('default-mrp') || 0);
            let defaultPrice = parseFloat($row.data('default-price') || 0);
            if (defaultMrp > 0) $row.find('.mrp-input').val(defaultMrp.toFixed(2));
            if (defaultPrice > 0) $row.find('.rate-input').val(defaultPrice.toFixed(2));
        }
        
        $row.find('.qty-input').trigger('input');
    }

    $(document).on('change', '.size-select, select[name*="[color_id]"]', function() {
        updateStockAndRate($(this).closest('.item-row'));
    });

    setTimeout(function() {
        $('.item-row').each(function() {
            let $row = $(this);
            let code = $row.find('.stock-item-select').val();
            if (code) {
                $row.find('.stock-item-select').trigger('change');
            }
        });
    }, 500);

    $(document).on('change', 'input[type="radio"][name*="[sleeve]"]', function() {
        if (this.checked) {
            updateStockAndRate($(this).closest('.item-row'));
        }
    });

    $(document).on('input', '.qty-input, .mrp-input, .rate-input', function() {
        let $row = $(this).closest('.item-row');
        let qtyInput = $row.find('.qty-input');
        let qty = parseFloat(qtyInput.val()) || 0;
        let rate = parseFloat($row.find('.rate-input').val()) || 0;
        let available = parseFloat($row.find('.available-stock-display').text()) || 0;

        let hasStockItem = !!$row.find('.stock-item-select').val();

        if (hasStockItem && qty > available) {
            qtyInput.addClass('is-invalid');
            $row.find('.stock-error-msg').show();
        } else {
            qtyInput.removeClass('is-invalid');
            $row.find('.stock-error-msg').hide();
        }

        $row.find('.amount-input').val((qty * rate).toFixed(2));
        calculateTotals();
        validateSubmit();
    });

    function validateSubmit() {
        let hasError = $('.qty-input.is-invalid').length > 0;
        $('button[type="submit"]').prop('disabled', hasError);
    }

    function calculateTotals() {
        let totalQty = 0, subTotal = 0;
        $('.item-row').each(function() {
            totalQty += parseFloat($(this).find('.qty-input').val()) || 0;
            subTotal += parseFloat($(this).find('.amount-input').val()) || 0;
        });

        $('#total_qty').val(totalQty.toFixed(2));
        $('#sub_total_qty').val(subTotal.toFixed(2));

        const boxDiscountAmountPerPiece = parseFloat($('#box_discount_amount').val()) || 0;
        const salesDiscPercent = parseFloat($('#sales_discount_percent').val()) || 0;
        
        const boxDiscountValue = totalQty * boxDiscountAmountPerPiece;
        const salesDiscountValue = (subTotal * salesDiscPercent) / 100;
        const discountAmount = boxDiscountValue + salesDiscountValue;
        
        $('#discount_amount').val(discountAmount.toFixed(2));

        let preGstCharges = 0;
        let postGstCharges = 0;
        $('.charge-row').each(function () {
            let amount = parseFloat($(this).find('input[name="charges[amount][]"]').val()) || 0;
            let taxType = $(this).attr('data-tax-type') || $(this).data('tax-type') || 'Post-GST';
            if (taxType === 'Pre-GST') {
                preGstCharges += amount;
            } else {
                postGstCharges += amount;
            }
        });
        
        $('#pre_gst_total_input').val(preGstCharges.toFixed(2));
        $('#other_charges_input').val(postGstCharges.toFixed(2));

        const taxableAmount = subTotal - discountAmount + preGstCharges;
        $('#taxable_amount').val(taxableAmount.toFixed(2));

        let taxPercent = 0;
        const isOtherState = $('input[name="other_state"]:checked').val() === 'yes';
        
        if (isOtherState) {
            $('.igst-field').removeClass('d-none');
            $('.cgst-field, .sgst-field').addClass('d-none');
            taxPercent = parseFloat($('#igst_percent').val()) || 0;
        } else {
            $('.igst-field').addClass('d-none');
            $('.cgst-field, .sgst-field').removeClass('d-none');
            taxPercent = (parseFloat($('#cgst_percent').val()) || 0) + (parseFloat($('#sgst_percent').val()) || 0);
        }

        const taxAmount = (taxableAmount * taxPercent) / 100;
        $('#tax_amount').val(taxAmount.toFixed(2));
        
        let finalTotal = taxableAmount + taxAmount + postGstCharges;
        
        // if ($('#freight_type').val() === 'Paid') {
        //     finalTotal += parseFloat($('#freight_amount').val()) || 0;
        // }

        let roundedTotal = Math.round(finalTotal);
        $('#total_before_round_off').val(finalTotal.toFixed(2));
        
        let roundOffVal = Math.abs(roundedTotal - finalTotal);
        let roundOffType = (roundedTotal >= finalTotal) ? 'Add' : 'Less';
        
        roundOffVal = parseFloat(roundOffVal.toFixed(2));
        
        $('#round_off_type').val(roundOffVal > 0 ? roundOffType : 'Add');
        $('#round_off').val(roundOffVal.toFixed(2));
        
        let displayStr = (roundOffVal > 0) ? (roundOffType === 'Add' ? '+' : '-') + roundOffVal.toFixed(2) : '0.00';
        $('#round_off_display').val(displayStr);
        
        finalTotal = roundedTotal;

        $('#total_amount').val(finalTotal.toFixed(2));
    }

    $(document).on('input', '#box_discount_amount, #sales_discount_percent, #igst_percent, #cgst_percent, #sgst_percent', calculateTotals);
    $(document).on('change', 'input[name="other_state"]', calculateTotals);
    function initDiscountState() {
        $('#box_discount_amount').prop('disabled', false);
    }
    initDiscountState();

    $(".select2").each(function() {
        if ($(this).hasClass("select2-hidden-accessible")) {
            $(this).select2('destroy');
        }
        $(this).select2({ dropdownParent: $(this).closest('.card-body').length ? $(this).closest('.card-body') : $('body') });
    });
    
    $('.table-responsive').on('scroll', function() {
        $('.select2').each(function() {
            if ($(this).data('select2') && $(this).data('select2').isOpen()) {
                $(this).select2('close');
            }
        });
    });

    calculateTotals();

    $('#zone_id').on('change', function() {
        const zoneId = $(this).val();
        const agentSelect = $('#agent_id');
        
        agentSelect.html('<option value="">Loading...</option>').trigger('change');
        
        if (zoneId) {
            $.ajax({
                url: `{{ url('get-agents-by-zone') }}/${zoneId}`,
                type: 'GET',
                success: function(data) {
                    let opts = '<option value="">Select Sales Executive</option>';
                    data.forEach(agent => {
                        opts += `<option value="${agent.id}">${agent.name}(${agent.code})</option>`;
                    });
                    agentSelect.html(opts).trigger('change');
                }
            });
        } else {
             $.ajax({
                url: `{{ url('get-agents-by-zone') }}/0`, 
                type: 'GET',
                success: function(data) {
                    let opts = '<option value="">Select Sales Executive</option>';
                    data.forEach(agent => {
                        opts += `<option value="${agent.id}">${agent.name}</option>`;
                    });
                    agentSelect.html(opts).trigger('change');
                }
            });
        }
    });

    function refreshChargeDropdownState() {
        let selectedChargeIds = [];
        $('#added_charges_list tr').each(function () {
            let id = $(this).data('charge-id');
            if (id) selectedChargeIds.push(id.toString());
        });

        $('#charges_select option').each(function () {
            let optionId = $(this).val();
            if (optionId) {
                if (selectedChargeIds.includes(optionId.toString())) {
                    $(this).prop('disabled', true);
                } else {
                    $(this).prop('disabled', false);
                }
            }
        });

        $('#charges_select').select2('destroy').select2({
            dropdownParent: $('#charges_select').closest('.card-body')
        });
    }

    function loadCharges() {
        $.ajax({
            url: "{{ url('get_charges') }}",
            type: "GET",
            success: function (data) {
                let select = $('#charges_select');
                select.empty();
                select.append('<option value="">Select Charge</option>');

                data.forEach(function (charge) {
                    select.append(`<option value="${charge.id}">${charge.charge_name}</option>`);
                });
                refreshChargeDropdownState();
            }
        });
    }
    loadCharges();

    $('#add_charge_btn').click(function () {
        let chargeId = $('#charges_select').val();
        let chargeText = $('#charges_select option:selected').text();
        let amount = parseFloat($('#charge_amount').val());
        let taxType = $('#charge_tax_type').val();

        if (!chargeId) {
            alert("Please select a charge");
            return;
        }

        if (!amount || amount <= 0) {
            alert("Please enter a valid amount");
            return;
        }

        $('#charges_table').removeClass('d-none');

        let row = `
            <tr class="charge-row" data-charge-id="${chargeId}" data-tax-type="${taxType}">
                <td>
                    ${chargeText}
                    <input type="hidden" name="charges[charge_id][]" value="${chargeId}">
                    <input type="hidden" name="charges[name][]" value="${chargeText}">
                </td>
                <td>
                    ${taxType}
                    <input type="hidden" name="charges[tax_type][]" value="${taxType}">
                </td>
                <td>
                    ${amount.toFixed(2)}
                    <input type="hidden" name="charges[amount][]" value="${amount.toFixed(2)}">
                </td>
                <td class="d-flex align-items-center">
                    <button type="button" class="btn btn-outline-success btn-sm edit-charge me-1" title="Edit Charge">
                        <i class="ri ri-pencil-line"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-charge" title="Delete Charge">
                        <i class="ri ri-delete-bin-line"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#added_charges_list').append(row);

        $('#charges_select').val('').trigger('change');
        $('#charge_amount').val('');

        calculateTotals();
        refreshChargeDropdownState();
    });

    $(document).on('click', '.edit-charge', function () {
        let $row = $(this).closest('tr');
        let chargeId = $row.data('charge-id');
        let amount = parseFloat($row.find('input[name="charges[amount][]"]').val()) || 0;
        let taxType = $row.attr('data-tax-type') || $row.data('tax-type') || 'Post-GST';

        $('#charges_select').val(chargeId).trigger('change');
        $('#charge_amount').val(amount.toFixed(2));
        $('#charge_tax_type').val(taxType).trigger('change');

        $row.remove();

        if ($('#added_charges_list tr').length === 0) {
            $('#charges_table').addClass('d-none');
        }
        calculateTotals();
        refreshChargeDropdownState();
    });

    $(document).on("click", ".remove-charge", function () {
        let $row = $(this).closest('tr');
        let invoiceChargeId = $row.data('invoice-charge-id');

        if (invoiceChargeId) {
            Swal.fire({
                title: "Are you sure?",
                text: "Do you really want to delete this charge?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#8c57ff",
                cancelButtonColor: "#ff4c51",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Deleting...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: "{{ url('sales_orders/delete-charge') }}/" + invoiceChargeId,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Charge has been deleted.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                $row.remove();

                                if ($('#added_charges_list tr').length === 0) {
                                    $('#charges_table').addClass('d-none');
                                }

                                calculateTotals();
                                refreshChargeDropdownState();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: response.message || 'Failed to delete charge'
                                });
                            }
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to delete charge'
                            });
                        }
                    });
                }
            });
        } else {
            $row.remove();

            if ($('#added_charges_list tr').length === 0) {
                $('#charges_table').addClass('d-none');
            }

            calculateTotals();
            refreshChargeDropdownState();
        }
    });

    // Camera scanning logic
    let html5QrCode = null;
    let isCameraOpen = false;

    $('#btn_camera_scan').click(function() {
        if (isCameraOpen && html5QrCode) {
            html5QrCode.stop().then((ignore) => {
                $('#reader').hide();
                isCameraOpen = false;
                $(this).html('<i class="ri-camera-line me-1"></i> CAMERA');
                $('#global_item_search').focus();
            }).catch((err) => {
                console.error("Failed to stop camera:", err);
            });
        } else {
            $('#reader').show();
            html5QrCode = new Html5Qrcode("reader");
            
            html5QrCode.start(
                { facingMode: "environment" }, 
                {
                    fps: 10,
                    qrbox: { width: 250, height: 100 },
                    aspectRatio: 1.0
                },
                (decodedText, decodedResult) => {
                    $('#global_item_search').val(decodedText);
                    
                    html5QrCode.stop().then((ignore) => {
                        $('#reader').hide();
                        isCameraOpen = false;
                        $('#btn_camera_scan').html('<i class="ri-camera-line me-1"></i> CAMERA');
                        
                        var e = $.Event("keydown");
                        e.which = 13;
                        $('#global_item_search').trigger(e);
                    }).catch((err) => {
                        console.error("Failed to stop camera after scan:", err);
                    });
                },
                (errorMessage) => {
                    // Parse error, ignore it
                }
            ).then(() => {
                isCameraOpen = true;
                $(this).html('<i class="ri-close-line me-1"></i> CLOSE CAMERA');
            }).catch((err) => {
                console.error("Error starting camera", err);
                $('#scan_alert').removeClass('alert-success').addClass('alert-danger').show();
                $('#scan_msg').html('<strong>Error:</strong> Could not start camera. Please ensure camera permissions are granted.');
                $('#reader').hide();
            });
        }
    });

});
</script>
@endsection

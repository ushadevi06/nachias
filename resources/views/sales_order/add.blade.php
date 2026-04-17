@extends('layouts.common')
@section('title', ($salesOrder ? 'Edit' : 'Add') . ' Sales Order - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
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
                                        @foreach(['Regular'=>'Regular Order','Sample'=>'Sample Order','Bulk'=>'Bulk/Export'] as $val=>$label)
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
                                        <option value="{{ $customer->id }}" data-state-id="{{ $customer->state_id }}" {{ old('customer_id', $salesOrder->customer_id ?? '') == $customer->id ? 'selected' : '' }}>{{ $customer->name }} ({{ $customer->code }})</option>
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
                                <div class="row gx-2">
                                    <div class="col-6">
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
                                    <div class="col-6">
                                        <div class="form-floating form-floating-outline">
                                            <select id="agent_id" name="agent_id" class="select2 form-select" data-placeholder="Select Sales Agent/Executive">
                                                <option value="">Select Sales Agent/Executive</option>
                                                @foreach($sales_agent as $agent)
                                                <option value="{{ $agent->id }}" {{ old('agent_id', $salesOrder->agent_id ?? '') == $agent->id ? 'selected' : '' }}>{{ $agent->name }}({{ $agent->code }})</option>
                                                @endforeach
                                            </select>
                                            <label for="agent_id">Sales Agent/Executive</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                        <input type="number" class="form-control" id="commission_percent" name="commission_percent" step="0.01" min="0" placeholder="0.00" value="{{ old('commission_percent', $salesOrder->commission_percent ?? '') }}">
                                        <label for="commission_percent">Commission</label>
                                    </div>
                                    <span class="input-group-text">%</span>
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

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="transporter_name" name="transporter_name" placeholder="Transporter Name" value="{{ old('transporter_name', $salesOrder->transporter_name ?? '') }}">
                                    <label for="transporter_name">Transporter Name</label>
                                </div>
                            </div>

                            <div class="col-md-4">
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
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="transport_gst_no" name="transport_gst_no" placeholder="Transport GST No" value="{{ old('transport_gst_no', $salesOrder->transport_gst_no ?? '') }}">
                                    <label for="transport_gst_no">Transport GST No</label>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Item Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered align-middle" id="item-rows">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width: 250px;">Stock Item *</th>
                                        <th style="min-width: 150px;">Color</th>
                                        <th style="min-width: 150px;">Art No *</th>
                                        <th style="min-width: 100px;">UOM *</th>
                                        <th style="min-width: 120px;">Size *</th>
                                        <th style="min-width: 120px;">Quantity *</th>
                                        {{-- <th style="min-width: 120px;">Rate *</th> --}}
                                        <th style="min-width: 120px;">MRP *</th>
                                        <th style="min-width: 120px;">Amount</th>
                                        <th style="min-width: 140px;">Sleeve Type</th>
                                        <th style="min-width: 50px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                            @if(old('items'))
                                @foreach(old('items') as $index => $item)
                                    <tr class="item-row">
                                        <td>
                                            <div class="form-floating form-floating-outline">
                                                <select name="items[{{ $index }}][stock_item_key]" class="select2 form-select stock-item-select @error("items.$index.stock_item_key") is-invalid @enderror" data-placeholder="Select Stock Item">
                                                    <option value="">Select</option>
                                                    @foreach($stockItems as $si)
                                                        @php $key = $si['finished_item_code'] . '|' . $si['color_id']; @endphp
                                                        <option value="{{ $key }}" {{ ($item['stock_item_key'] ?? ($si['finished_item_code'] . '|' . $si['color_id'])) == $key ? 'selected' : '' }}>{{ $si['finished_item_code'] }} ({{ $si['color_name'] }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error("items.$index.stock_item_key")<div class="text-danger mt-1" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                            <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item['item_id'] ?? '' }}">
                                            <input type="hidden" name="items[{{ $index }}][brand_cat_id]" value="{{ $item['brand_cat_id'] ?? '' }}">
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
                                                <input type="text" name="items[{{ $index }}][art_no]" class="form-control @error("items.$index.art_no") is-invalid @enderror" placeholder="Art No" value="{{ $item['art_no'] ?? '' }}">
                                            </div>
                                            @error("items.$index.art_no")<div class="text-danger mt-1" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                        </td>
                                        <td>
                                            <div class="form-floating form-floating-outline">
                                                <select name="items[{{ $index }}][uom_id]" class="select2 form-select @error("items.$index.uom_id") is-invalid @enderror" data-placeholder="UOM">
                                                    <option value="">UOM</option>
                                                    @foreach($uoms as $u)
                                                        <option value="{{ $u->id }}" {{ ($item['uom_id'] ?? '') == $u->id ? 'selected' : '' }}>{{ $u->uom_code }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error("items.$index.uom_id")<div class="text-danger mt-1" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                        </td>
                                        <td>
                                            <div class="form-floating form-floating-outline">
                                                <select name="items[{{ $index }}][size_id]" class="form-select select2 size-select @error("items.$index.size_id") is-invalid @enderror" data-selected="{{ $item['size_id'] ?? '' }}">
                                                    <option value="">Select Size</option>
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
                                        {{-- <td>
                                            <div class="form-floating form-floating-outline">
                                                <input type="number" name="items[{{ $index }}][rate]" class="form-control rate-input @error("items.$index.rate") is-invalid @enderror" placeholder="0.00" value="{{ $item['rate'] ?? '' }}" step="0.01">
                                            </div>
                                            @error("items.$index.rate")<div class="text-danger mt-1" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                        </td> --}}
                                        <td>
                                            <div class="form-floating form-floating-outline">
                                                <input type="number" name="items[{{ $index }}][mrp]" class="form-control mrp-input @error("items.$index.mrp") is-invalid @enderror" placeholder="0.00" value="{{ $item['mrp'] ?? '' }}" step="0.01">
                                            </div>
                                            @error("items.$index.mrp")<div class="text-danger mt-1" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                        </td>
                                        <td>
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" name="items[{{ $index }}][amount]" class="form-control amount-input" value="{{ number_format(($item['qty'] ?? 0) * ($item['mrp'] ?? 0), 2, '.', '') }}" readonly>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="segmented-control">
                                                <input type="radio" name="items[{{ $index }}][sleeve]" id="sleeve_full_{{ $index }}" value="Full" {{ (is_array($item['sleeve'] ?? 'Full') ? in_array('Full', (array)$item['sleeve']) : ($item['sleeve'] ?? 'Full') == 'Full') ? 'checked' : '' }}>
                                                <label for="sleeve_full_{{ $index }}">Full</label>
                                                <input type="radio" name="items[{{ $index }}][sleeve]" id="sleeve_half_{{ $index }}" value="Half" {{ (is_array($item['sleeve'] ?? '') ? in_array('Half', (array)$item['sleeve']) : ($item['sleeve'] ?? '') == 'Half') ? 'checked' : '' }}>
                                                <label for="sleeve_half_{{ $index }}">Half</label>
                                            </div>
                                            @error("items.$index.sleeve")<div class="text-danger mt-1" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-danger btn-sm delete_row"><i class="ri ri-delete-bin-line"></i></button>
                                                <input type="hidden" name="items[{{ $index }}][stock_entry_item_id]" class="stock-entry-item-id" value="{{ $item['stock_entry_item_id'] ?? '' }}">
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @elseif(isset($salesOrder) && $salesOrder->items->count() > 0)
                                @foreach($salesOrder->items as $index => $item) 
                                    <tr class="item-row">
                                        <td>
                                            <div class="form-floating form-floating-outline">
                                                <select name="items[{{ $index }}][stock_item_key]" class="select2 form-select stock-item-select" data-placeholder="Select Stock Item">
                                                    <option value="">Select</option>
                                                    @foreach($stockItems as $si)
                                                        @php 
                                                            $key = $si['finished_item_code'] . '|' . $si['color_id']; 
                                                            $itemFinishedCode = $item->stockEntryItem ? $item->stockEntryItem->finished_item_code : ($item->item->code ?? '');
                                                        @endphp
                                                        <option value="{{ $key }}" {{ ($itemFinishedCode == $si['finished_item_code'] && $item->color_id == $si['color_id']) ? 'selected' : '' }}>{{ $si['finished_item_code'] }} ({{ $si['color_name'] }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item->item_id }}">
                                            <input type="hidden" name="items[{{ $index }}][brand_cat_id]" value="{{ $item->brand_cat_id }}">
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
                                                <input type="text" name="items[{{ $index }}][art_no]" class="form-control" placeholder="Art No" value="{{ $item->art_no }}">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-floating form-floating-outline">
                                                <select name="items[{{ $index }}][uom_id]" class="select2 form-select" data-placeholder="UOM">
                                                    <option value="">UOM</option>
                                                    @foreach($uoms as $u)
                                                        <option value="{{ $u->id }}" {{ $item->uom_id == $u->id ? 'selected' : '' }}>{{ $u->uom_code }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-floating form-floating-outline">
                                                <select name="items[{{ $index }}][size_id]" class="form-select select2 size-select" data-selected="{{ $item->size_id }}">
                                                    <option value="">Select Size</option>
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
                                        {{-- <td>
                                            <div class="form-floating form-floating-outline">
                                                <input type="number" name="items[{{ $index }}][rate]" class="form-control rate-input" value="{{ $item->rate }}" step="0.01">
                                            </div>
                                        </td> --}}
                                        <td>
                                            <div class="form-floating form-floating-outline">
                                                <input type="number" name="items[{{ $index }}][mrp]" class="form-control mrp-input" value="{{ $item->mrp }}" step="0.01">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" name="items[{{ $index }}][amount]" class="form-control amount-input" value="{{ number_format($item->qty * $item->mrp, 2, '.', '') }}" readonly>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="segmented-control">
                                                <input type="radio" name="items[{{ $index }}][sleeve]" id="sleeve_full_{{ $index }}" value="Full" {{ (is_array($item->sleeve) ? in_array('Full', $item->sleeve) : $item->sleeve == 'Full') ? 'checked' : '' }}>
                                                <label for="sleeve_full_{{ $index }}">Full</label>
                                                <input type="radio" name="items[{{ $index }}][sleeve]" id="sleeve_half_{{ $index }}" value="Half" {{ (is_array($item->sleeve) ? in_array('Half', $item->sleeve) : $item->sleeve == 'Half') ? 'checked' : '' }}>
                                                <label for="sleeve_half_{{ $index }}">Half</label>
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
                                            <select name="items[0][stock_item_key]" class="select2 form-select stock-item-select" data-placeholder="Select Stock Item">
                                                <option value="">Select</option>
                                                @foreach($stockItems as $si)
                                                    @php $key = $si['finished_item_code'] . '|' . $si['color_id']; @endphp
                                                    <option value="{{ $key }}">{{ $si['finished_item_code'] }} ({{ $si['color_name'] }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <input type="hidden" name="items[0][item_id]" value="">
                                        <input type="hidden" name="items[0][brand_cat_id]" value="">
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
                                            <input type="text" name="items[0][art_no]" class="form-control" placeholder="Art No">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <select name="items[0][uom_id]" class="select2 form-select" data-placeholder="UOM">
                                                <option value="">UOM</option>
                                                @foreach($uoms as $u)
                                                    <option value="{{ $u->id }}" {{ $u->uom_code == 'PCS' ? 'selected' : '' }}>{{ $u->uom_code }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <select name="items[0][size_id]" class="form-select select2 size-select">
                                                <option value="">Select Size</option>
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
                                    {{-- <td>
                                        <div class="form-floating form-floating-outline">
                                            <input type="number" name="items[0][rate]" class="form-control rate-input" placeholder="0.00" step="0.01">
                                        </div>
                                    </td> --}}
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <input type="number" name="items[0][mrp]" class="form-control mrp-input" placeholder="0.00" step="0.01">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" name="items[0][amount]" class="form-control amount-input" value="0.00" readonly>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="segmented-control">
                                            <input type="radio" name="items[0][sleeve]" id="sleeve_full_0" value="Full" checked>
                                            <label for="sleeve_full_0">Full</label>
                                            <input type="radio" name="items[0][sleeve]" id="sleeve_half_0" value="Half">
                                            <label for="sleeve_half_0">Half</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-primary btn-sm add_item">
                                                <i class="ri ri-add-line"></i>
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
                                                @foreach(['Draft','Approved','Rejected','Pending','In Production','Dispatched','Cancelled'] as $st)
                                                <option value="{{ $st }}" {{ old('status', $salesOrder->status ?? 'Draft') == $st ? 'selected' : '' }}>{{ $st }}</option>
                                                @endforeach
                                            </select>
                                            <label for="status">Order Status <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <textarea class="form-control" id="terms_conditions" name="terms_conditions" rows="3" placeholder="Terms & Conditions">{{ old('terms_conditions', $salesOrder->terms_conditions ?? '') }}</textarea>
                                            <label for="terms_conditions">Terms & Conditions</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <textarea class="form-control" id="internal_remarks" name="internal_remarks" rows="2" placeholder="Internal Notes">{{ old('internal_remarks', $salesOrder->internal_remarks ?? '') }}</textarea>
                                            <label for="internal_remarks">Internal Notes</label>
                                        </div>
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
                                                                <i class="ri-file-pdf-fill text-danger fs-3"></i>
                                                            @else
                                                                <i class="ri-file-text-fill text-primary fs-3"></i>
                                                            @endif
                                                            <span class="ms-1 small text-dark fw-bold text-uppercase" style="font-size: 10px;">{{ $ext }}</span>
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
                                        <div class="mb-4">
                                            <label class="fw-bold mb-2">Box Discount Option:</label>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex flex-wrap gap-3 align-items-center">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="discount_type" id="disc_none" value="none" {{ (old('discount_type', $salesOrder ? ($salesOrder->apply_box_discount ? 'box' : 'none') : 'none')) == 'none' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="disc_none">Without Box Discount</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="discount_type" id="disc_box" value="box" {{ (old('discount_type', $salesOrder ? ($salesOrder->apply_box_discount ? 'box' : '') : '')) == 'box' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="disc_box">With Box Discount</label>
                                                    </div>
                                                </div>
                                                <div class="input-group input-group-sm" style="width:100px;">
                                                    <input type="number" class="form-control form-control-sm text-end" id="discount_percent" name="discount_percent" step="0.01" min="0" max="100" value="{{ old('discount_percent', $salesOrder->discount_percent ?? '0') }}">
                                                    <span class="input-group-text px-1">%</span>
                                                </div>
                                            </div>
                                            <input type="hidden" name="apply_box_discount" id="apply_box_discount_hidden" value="{{ old('apply_box_discount', $salesOrder->apply_box_discount ?? false) ? '1' : '0' }}">
                                            <input type="hidden" id="customer_sales_discount" value="0">
                                            <input type="hidden" id="customer_box_discount" value="0">
                                        </div>
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
                                        <div class="mb-2 d-none" id="commission_row">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label class="fw-medium">Commission Amount:</label>
                                                <div class="text-end">
                                                    <span id="commission_amount_display" class="fw-bold">0.00</span>
                                                    <input type="hidden" id="commission_amount" name="commission_amount" value="{{ old('commission_amount', $salesOrder->commission_amount ?? '0.00') }}">
                                                </div>
                                            </div>
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
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="fw-medium">Round Off:</label>
                                            <div class="d-flex align-items-center">
                                                <div class="form-check form-check-inline me-2">
                                                    <input class="form-check-input" type="radio" name="round_off_type" id="round_off_add" value="Add" {{ old('round_off_type', $salesOrder->round_off_type ?? 'Add') == 'Add' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="round_off_add">Add</label>
                                                </div>
                                                <div class="form-check form-check-inline me-2">
                                                    <input class="form-check-input" type="radio" name="round_off_type" id="round_off_less" value="Less" {{ old('round_off_type', $salesOrder->round_off_type ?? 'Add') == 'Less' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="round_off_less">Less</label>
                                                </div>
                                                <input type="number" class="form-control form-control-sm text-end" style="width:100px;" id="round_off" name="round_off" step="0.01" min="0" value="{{ old('round_off', $salesOrder->round_off ?? '0.00') }}" autocomplete="off">
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
</style>
@endsection
@section('scripts')
<script>
$(document).ready(function () {
    let itemIndex = Number($('#itemIndex').val()) || 0;

    $('.so_date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });
    $('.request_date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });

    function createRow() {
        let stockOpts = `<option value="">Select</option>`;
        @foreach($stockItems as $si)
        stockOpts += `<option value="{{ $si['finished_item_code'] }}|{{ $si['color_id'] }}">{{ addslashes($si['finished_item_code']) }} ({{ addslashes($si['color_name']) }})</option>`;
        @endforeach

        let colorOpts = `<option value="">Select Color</option>`;
        @foreach($colors as $col)
        colorOpts += `<option value="{{ $col->id }}">{{ addslashes($col->color_name) }}</option>`;
        @endforeach

        let uomOpts = `<option value="">UOM</option>`;
        @foreach($uoms as $u)
        uomOpts += `<option value="{{ $u->id }}" {{ $u->uom_code == 'PCS' ? 'selected' : '' }}>{{ $u->uom_code }}</option>`;
        @endforeach

        let rowHtml = `
            <tr class="item-row">
                <td>
                    <div class="form-floating form-floating-outline">
                        <select name="items[${itemIndex}][stock_item_key]" class="select2 form-select stock-item-select" data-placeholder="Select Stock Item">${stockOpts}</select>
                    </div>
                    <input type="hidden" name="items[${itemIndex}][item_id]" value="">
                    <input type="hidden" name="items[${itemIndex}][brand_cat_id]" value="">
                </td>
                <td>
                    <div class="form-floating form-floating-outline">
                        <select name="items[${itemIndex}][color_id]" class="select2 form-select color-select" data-placeholder="Color">${colorOpts}</select>
                    </div>
                </td>
                <td>
                    <div class="form-floating form-floating-outline">
                        <input type="text" name="items[${itemIndex}][art_no]" class="form-control" placeholder="Art No">
                    </div>
                </td>
                <td>
                    <div class="form-floating form-floating-outline">
                        <select name="items[${itemIndex}][uom_id]" class="select2 form-select" data-placeholder="UOM">${uomOpts}</select>
                    </div>
                </td>
                <td>
                    <div class="form-floating form-floating-outline">
                        <select name="items[${itemIndex}][size_id]" class="select2 form-select size-select" data-placeholder="Size">
                            <option value="">Select Size</option>
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
                        <input type="text" name="items[${itemIndex}][amount]" class="form-control amount-input" value="0.00" readonly>
                    </div>
                </td>
                <td>
                    <div class="segmented-control">
                        <input type="radio" name="items[${itemIndex}][sleeve]" id="sleeve_full_${itemIndex}" value="Full" checked>
                        <label for="sleeve_full_${itemIndex}">Full</label>
                        <input type="radio" name="items[${itemIndex}][sleeve]" id="sleeve_half_${itemIndex}" value="Half">
                        <label for="sleeve_half_${itemIndex}">Half</label>
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
                        let billingAddress = [c.address_line_1, c.address_line_2, c.address_line_3, c.city, c.state, c.pincode].filter(Boolean).join(', ');
                        $('#billing_address').val(billingAddress);
                        $('#shipping_address').val(billingAddress);
                        if (c.payment_terms) $('#payment_terms').val(c.payment_terms);
                        if (c.transport_name) $('#transporter_name').val(c.transport_name);
                        
                        $('#discount_percent').val('');

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

    $('#freight_type').on('change', function() {
        if ($(this).val() === 'Paid') {
            $('#freight_amount').prop('readonly', false);
        } else {
            $('#freight_amount').val('0').trigger('input').prop('readonly', true);
        }
        calculateTotals();
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

    function reindexItems() {
        // Not strictly needed for table display but good to keep if logic depends on counting
    }

    $(document).on('change', '.stock-item-select', function() {
        let key = $(this).val();
        let $row = $(this).closest('.item-row');
        
        if (key) {
            let parts = key.split('|');
            let code = parts[0];
            let colorId = parts[1];
            
            $.ajax({
                url: `{{ url('get-finished-item-details') }}/${encodeURIComponent(code)}/${colorId}`,
                type: 'GET',
                success: function(res) {
                    if (res.success) {
                        let d = res.data;
                        $row.find('input[name*="[item_id]"]').val(d.item_id);
                        $row.find('input[name*="[brand_cat_id]"]').val(d.brand_cat_id);
                        $row.find('select[name*="[color_id]"]').val(colorId).trigger('change');
                        $row.find('input[name*="[art_no]"]').val(d.art_no);
                        $row.find('select[name*="[uom_id]"]').val(d.uom_id).trigger('change');
                        $row.find('.available-stock-display').text(parseFloat(d.balance).toFixed(2));
                        $row.find('input[name*="[mrp]"]').val(parseFloat(d.mrp).toFixed(2));
                        
                        if (d.sleeve) {
                            $row.find(`input[name*="[sleeve]"][value="${d.sleeve}"]`).prop('checked', true).trigger('change');
                        }
                        
                        let sizeOpts = `<option value="">Select Size</option><option value="${d.size}" selected>${d.size}</option>`;
                        $row.find('.size-select').html(sizeOpts).trigger('change');
                        
                        $row.find('.qty-input').trigger('input');
                    }
                }
            });
        } else {
            $row.find('input[name*="[item_id]"]').val('');
            $row.find('input[name*="[brand_cat_id]"]').val('');
            $row.find('select[name*="[color_id]"]').val('').trigger('change');
            $row.find('input[name*="[art_no]"]').val('');
            $row.find('.available-stock-display').text('0.00');
            $row.find('input[name*="[mrp]"]').val('');
            $row.find('.size-select').html('<option value="">Select Size</option>').trigger('change');
            calculateTotals();
        }
    });

    function updateStockAndRate($row) {
        // This function might need to be adjusted or kept if it handles other things like sleeve change
        // But for now, the stock item selection already fetches the balance.
    }

    $(document).on('change', '.size-select, select[name*="[color_id]"]', function() {
        updateStockAndRate($(this).closest('.item-row'));
    });

    $('.item-row').each(function() {
        let $row = $(this);
        let itemId = $row.find('.item-select').val();
        if (itemId) {
            $row.find('.item-select').trigger('change');
        }
    });

    $(document).on('change', 'input[type="radio"][name*="[sleeve]"]', function() {
        if (this.checked) {
            updateStockAndRate($(this).closest('.item-row'));
        }
    });

    $(document).on('input', '.qty-input, .mrp-input', function() {
        let $row = $(this).closest('.item-row');
        let qtyInput = $row.find('.qty-input');
        let qty = parseFloat(qtyInput.val()) || 0;
        let mrp = parseFloat($row.find('.mrp-input').val()) || 0;
        let available = parseFloat($row.find('.available-stock-display').text()) || 0;

        if (qty > available && available > 0) {
            qtyInput.addClass('is-invalid');
            $row.find('.stock-error-msg').show();
        } else {
            qtyInput.removeClass('is-invalid');
            $row.find('.stock-error-msg').hide();
        }

        $row.find('.amount-input').val((qty * mrp).toFixed(2));
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

        const discPercent = parseFloat($('#discount_percent').val()) || 0;
        const discountAmount = (subTotal * discPercent) / 100;
        $('#discount_amount').val(discountAmount.toFixed(2));

        const taxableAmount = subTotal - discountAmount;
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

        let commissionPercent = parseFloat($('#commission_percent').val()) || 0;
        let commissionAmount = 0;
        if (commissionPercent > 0) {
            commissionAmount = (subTotal * commissionPercent) / 100;
            $('#commission_amount_display').text(commissionAmount.toFixed(2));
            $('#commission_amount').val(commissionAmount.toFixed(2));
            $('#commission_row').removeClass('d-none');
        } else {
            $('#commission_amount_display').text('0.00');
            $('#commission_amount').val('0.00');
            $('#commission_row').addClass('d-none');
        }

        let finalTotal = taxableAmount + taxAmount;
        
        if ($('#freight_type').val() === 'Paid') {
            finalTotal += parseFloat($('#freight_amount').val()) || 0;
        }

        const roundOffVal = parseFloat($('#round_off').val()) || 0;
        const roundOffType = $('input[name="round_off_type"]:checked').val();
        
        if (roundOffType === 'Add') {
            finalTotal += roundOffVal;
        } else {
            finalTotal -= roundOffVal;
        }

        $('#total_amount').val(finalTotal.toFixed(2));
    }

    $(document).on('input', '#discount_percent, #igst_percent, #cgst_percent, #sgst_percent, #round_off, #commission_percent', calculateTotals);
    $(document).on('change', 'input[name="other_state"], input[name="round_off_type"]', calculateTotals);

    $(document).on('change', 'input[name="discount_type"]', function() {
        let type = $(this).val();
        $('#apply_box_discount_hidden').val(type === 'box' ? 1 : 0);
        $('#discount_percent').val('');
        calculateTotals();
    });

    function initDiscountState() {
        $('#discount_percent').prop('disabled', false);
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
                    let opts = '<option value="">Select Sales Agent/Executive</option>';
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
                    let opts = '<option value="">Select Sales Agent/Executive</option>';
                    data.forEach(agent => {
                        opts += `<option value="${agent.id}">${agent.name}</option>`;
                    });
                    agentSelect.html(opts).trigger('change');
                }
            });
        }
    });
});
</script>
@endsection
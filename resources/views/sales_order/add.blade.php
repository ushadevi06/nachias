@extends('layouts.common')
@section('title', ($salesOrder ? 'Edit' : 'Add') . ' Sales Order - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="{{ $salesOrder ? url('sales_orders/add/' . $salesOrder->id) : url('sales_orders/add') }}" method="POST" enctype="multipart/form-data" class="common-form" autocomplete="off">
                @csrf
                {{-- @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif --}}
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
                                        <option value="{{ $season->id }}" {{ old('season_id', $salesOrder->season_id ?? '') == $season->id ? 'selected' : '' }}>{{ $season->name }}</option>
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
                                    <div class="col-8">
                                        <div class="form-floating form-floating-outline">
                                            <select id="agent_id" name="agent_id" class="select2 form-select" data-placeholder="Select Sales Agent/Executive">
                                                <option value="">Select Sales Agent/Executive</option>
                                                @foreach($sales_agent as $agent)
                                                <option value="{{ $agent->id }}" {{ old('agent_id', $salesOrder->agent_id ?? '') == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
                                                @endforeach
                                            </select>
                                            <label for="agent_id">Sales Agent/Executive</label>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="input-group input-group-merge">
                                            <div class="form-floating form-floating-outline">
                                                <input type="number" class="form-control" id="commission_percent" name="commission_percent" step="0.01" min="0" placeholder="0.00" value="{{ old('commission_percent', $salesOrder->commission_percent ?? '') }}">
                                                <label for="commission_percent">Comm.</label>
                                            </div>
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
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
                                    <input type="text" class="form-control delivery_date @error('delivery_date') is-invalid @enderror" id="delivery_date" name="delivery_date" placeholder="Delivery Date" value="{{ old('delivery_date', $salesOrder ? $salesOrder->delivery_date?->format('d-m-Y') : date('d-m-Y', strtotime('+7 days'))) }}">
                                    <label for="delivery_date">Expected Delivery Date <span class="text-danger">*</span></label>
                                </div>
                                @error('delivery_date')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="shipping_method" name="shipping_method" class="select2 form-select" data-placeholder="Select Shipping Method">
                                        <option value="">Select Shipping Method</option>
                                        @foreach(['DTDC','BlueDart','Self Pickup','Local Courier'] as $sm)
                                        <option value="{{ $sm }}" {{ old('shipping_method', $salesOrder->shipping_method ?? '') == $sm ? 'selected' : '' }}>{{ $sm }}</option>
                                        @endforeach
                                    </select>
                                    <label for="shipping_method">Shipping Method</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="transport_mode" name="transport_mode" class="select2 form-select" data-placeholder="Select Transport Mode">
                                        <option value="">Select Transport Mode</option>
                                        @foreach(['Truck','Tempo','Courier','By Hand','Rail','Air'] as $tm)
                                        <option value="{{ $tm }}" {{ old('transport_mode', $salesOrder->transport_mode ?? '') == $tm ? 'selected' : '' }}>{{ $tm }}</option>
                                        @endforeach
                                    </select>
                                    <label for="transport_mode">Transport Mode</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control" id="dispatch_from" name="dispatch_from" placeholder="Dispatch From" style="height: 52px;">{{ old('dispatch_from', $salesOrder->dispatch_from ?? '') }}</textarea>
                                    <label for="dispatch_from">Dispatch From</label>
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
                                    <input type="text" class="form-control" id="eway_bill_no" name="eway_bill_no" placeholder="E-Way Bill No" value="{{ old('eway_bill_no', $salesOrder->eway_bill_no ?? '') }}">
                                    <label for="eway_bill_no">E-Way Bill No</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="lr_no" name="lr_no" placeholder="LR No" value="{{ old('lr_no', $salesOrder->lr_no ?? '') }}">
                                    <label for="lr_no">LR No</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="dispatch_through" name="dispatch_through" class="select2 form-select" data-placeholder="Dispatch Through">
                                        <option value="">Select Dispatch Through</option>
                                        @foreach(['Road','Air','Courier','Hand Delivery'] as $dt)
                                        <option value="{{ $dt }}" {{ old('dispatch_through', $salesOrder->dispatch_through ?? '') == $dt ? 'selected' : '' }}>{{ $dt }}</option>
                                        @endforeach
                                    </select>
                                    <label for="dispatch_through">Dispatch Through</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box d-flex justify-content-between align-items-center mb-4">
                            <h4>Item Details</h4>
                            <button type="button" class="btn btn-sm btn-outline-primary add_item"><i class="ri ri-add-line me-1"></i>Add Item</button>
                        </div>
                        @error('items')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror

                        <div id="item-rows">
                            @if(old('items'))
                                @foreach(old('items') as $index => $item)
                                    <div class="item-block" id="row-{{ $index }}">
                                        <div class="item-block-header">
                                            <span class="item-number">Item #{{ $index + 1 }}</span>
                                            <button type="button" class="remove_item_btn" onclick="removeRow({{ $index }})"><i class="ri ri-delete-bin-line"></i></button>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-2">
                                                <div class="form-floating form-floating-outline">
                                                    <select name="items[{{ $index }}][brand_cat_id]" class="select2 form-select brand-select @error("items.$index.brand_cat_id") is-invalid @enderror" data-placeholder="Brand Category">
                                                        <option value="">Select</option>
                                                        @foreach($brandCategories as $bc)
                                                            <option value="{{ $bc->id }}" {{ ($item['brand_cat_id'] ?? '') == $bc->id ? 'selected' : '' }}>{{ $bc->name }} - {{ $bc->code }}</option>
                                                        @endforeach
                                                    </select>
                                                    <label>Brand Category *</label>
                                                </div>
                                                @error("items.$index.brand_cat_id")<div class="text-danger mt-1" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-floating form-floating-outline">
                                                    <select name="items[{{ $index }}][item_id]" class="select2 form-select item-select @error("items.$index.item_id") is-invalid @enderror" data-placeholder="Item">
                                                        <option value="">Select Item</option>
                                                        @if(isset($item['brand_cat_id']) && $item['brand_cat_id'])
                                                            @foreach($items->where('brand_category_id', $item['brand_cat_id']) as $it)
                                                                <option value="{{ $it->id }}" {{ ($item['item_id'] ?? '') == $it->id ? 'selected' : '' }}>{{ $it->name }} - {{ $it->code }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    <label>Item *</label>
                                                </div>
                                                @error("items.$index.item_id")<div class="text-danger mt-1" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-floating form-floating-outline">
                                                    <select name="items[{{ $index }}][color_id]" class="select2 form-select @error("items.$index.color_id") is-invalid @enderror" data-placeholder="Color">
                                                        <option value="">Select Color</option>
                                                        @foreach($colors as $col)
                                                            <option value="{{ $col->id }}" {{ ($item['color_id'] ?? '') == $col->id ? 'selected' : '' }}>{{ $col->color_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <label>Color</label>
                                                </div>
                                                @error("items.$index.color_id")<div class="text-danger mt-1" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" name="items[{{ $index }}][art_no]" class="form-control @error("items.$index.art_no") is-invalid @enderror" placeholder="Art No" value="{{ $item['art_no'] ?? '' }}">
                                                    <label>Art No *</label>
                                                </div>
                                                @error("items.$index.art_no")<div class="text-danger mt-1" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-floating form-floating-outline">
                                                    <select name="items[{{ $index }}][uom_id]" class="select2 form-select @error("items.$index.uom_id") is-invalid @enderror" data-placeholder="UOM">
                                                        <option value="">UOM</option>
                                                        @foreach($uoms as $u)
                                                            <option value="{{ $u->id }}" {{ ($item['uom_id'] ?? '') == $u->id ? 'selected' : '' }}>{{ $u->uom_code }}</option>
                                                        @endforeach
                                                    </select>
                                                    <label>UOM *</label>
                                                </div>
                                                @error("items.$index.uom_id")<div class="text-danger mt-1" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-floating form-floating-outline">
                                                    <select name="items[{{ $index }}][size_id]" class="form-select select2 size-select @error("items.$index.size_id") is-invalid @enderror" data-selected="{{ $item['size_id'] ?? '' }}">
                                                        <option value="">Select Size</option>
                                                    </select>
                                                    <label>Size *</label>
                                                </div>
                                                @error("items.$index.size_id")<div class="text-danger mt-1" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="number" name="items[{{ $index }}][qty]" class="form-control qty-input @error("items.$index.qty") is-invalid @enderror" value="{{ $item['qty'] ?? 1 }}" min="0.01" step="0.01">
                                                    <label>Quantity *</label>
                                                    <div class="stock-info-wrapper mt-1">
                                                        <small class="stock-label text-muted">Stock: <span class="available-stock-display">0.00</span></small>
                                                        <div class="invalid-feedback stock-error-msg" style="display: none;">Exceeds available stock!</div>
                                                    </div>
                                                </div>
                                                @error("items.$index.qty")<div class="text-danger mt-1" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="number" name="items[{{ $index }}][rate]" class="form-control rate-input @error("items.$index.rate") is-invalid @enderror" placeholder="0.00" value="{{ $item['rate'] ?? '' }}" step="0.01">
                                                    <label>Rate *</label>
                                                </div>
                                                 @error("items.$index.rate")<div class="text-danger mt-1" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="number" name="items[{{ $index }}][mrp]" class="form-control mrp-input @error("items.$index.mrp") is-invalid @enderror" placeholder="0.00" value="{{ $item['mrp'] ?? '' }}" step="0.01">
                                                    <label>MRP *</label>
                                                </div>
                                                @error("items.$index.mrp")<div class="text-danger mt-1" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" name="items[{{ $index }}][amount]" class="form-control amount-input" value="{{ number_format(($item['qty'] ?? 0) * ($item['rate'] ?? 0), 2, '.', '') }}" readonly>
                                                    <label>Amount</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="sleeve-container">
                                                    <span class="sleeve-label">Sleeve Type</span>
                                                    <div class="segmented-control mt-1">
                                                        <input type="radio" name="items[{{ $index }}][sleeve]" id="sleeve_full_{{ $index }}" value="Full" {{ (is_array($item['sleeve'] ?? 'Full') ? in_array('Full', (array)$item['sleeve']) : ($item['sleeve'] ?? 'Full') == 'Full') ? 'checked' : '' }}>
                                                        <label for="sleeve_full_{{ $index }}">Full</label>
                                                        <input type="radio" name="items[{{ $index }}][sleeve]" id="sleeve_half_{{ $index }}" value="Half" {{ (is_array($item['sleeve'] ?? '') ? in_array('Half', (array)$item['sleeve']) : ($item['sleeve'] ?? '') == 'Half') ? 'checked' : '' }}>
                                                        <label for="sleeve_half_{{ $index }}">Half</label>
                                                    </div>
                                                </div>
                                                @error("items.$index.sleeve")<div class="text-danger mt-1" style="font-size: 0.75rem;">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <input type="hidden" name="items[{{ $index }}][stock_entry_item_id]" class="stock-entry-item-id" value="{{ $item['stock_entry_item_id'] ?? '' }}">
                                    </div>
                                @endforeach
                            @elseif(isset($salesOrder) && $salesOrder->items->count() > 0)
                                @foreach($salesOrder->items as $index => $item) 
                                    <div class="item-block" id="row-{{ $index }}">
                                        <div class="item-block-header">
                                            <span class="item-number">Item #{{ $index + 1 }}</span>
                                            <button type="button" class="remove_item_btn" onclick="removeRow({{ $index }})"><i class="ri ri-delete-bin-line"></i></button>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-2">
                                                <div class="form-floating form-floating-outline">
                                                    <select name="items[{{ $index }}][brand_cat_id]" class="select2 form-select brand-select" data-placeholder="Brand Category">
                                                        <option value="">Select</option>
                                                        @foreach($brandCategories as $bc)
                                                            <option value="{{ $bc->id }}" {{ $item->brand_cat_id == $bc->id ? 'selected' : '' }}>{{ $bc->name }} - {{ $bc->code }}</option>
                                                        @endforeach
                                                    </select>
                                                    <label>Brand Category *</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-floating form-floating-outline">
                                                    <select name="items[{{ $index }}][item_id]" class="select2 form-select item-select" data-placeholder="Item">
                                                        <option value="">Select Item</option>
                                                        @foreach($items->where('brand_category_id', $item->brand_cat_id) as $it)
                                                            <option value="{{ $it->id }}" {{ $item->item_id == $it->id ? 'selected' : '' }}>{{ $it->name }} - {{ $it->code }}</option>
                                                        @endforeach
                                                    </select>
                                                    <label>Item *</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-floating form-floating-outline">
                                                    <select name="items[{{ $index }}][color_id]" class="select2 form-select" data-placeholder="Color">
                                                        <option value="">Select Color</option>
                                                        @foreach($colors as $col)
                                                            <option value="{{ $col->id }}" {{ $item->color_id == $col->id ? 'selected' : '' }}>{{ $col->color_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <label>Color</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" name="items[{{ $index }}][art_no]" class="form-control" placeholder="Art No" value="{{ $item->art_no }}">
                                                    <label>Art No *</label>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-floating form-floating-outline">
                                                    <select name="items[{{ $index }}][uom_id]" class="select2 form-select" data-placeholder="UOM">
                                                        <option value="">UOM</option>
                                                        @foreach($uoms as $u)
                                                            <option value="{{ $u->id }}" {{ $item->uom_id == $u->id ? 'selected' : '' }}>{{ $u->uom_code }}</option>
                                                        @endforeach
                                                    </select>
                                                    <label>UOM *</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-floating form-floating-outline">
                                                    <select name="items[{{ $index }}][size_id]" class="form-select select2 size-select" data-selected="{{ $item->size_id }}">
                                                        <option value="">Select Size</option>
                                                    </select>
                                                    <label>Size *</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="number" name="items[{{ $index }}][qty]" class="form-control qty-input" value="{{ $item->qty }}" min="0.01" step="0.01">
                                                    <label>Quantity *</label>
                                                    <div class="stock-info-wrapper mt-1">
                                                        <small class="stock-label text-muted">Stock: <span class="available-stock-display">0.00</span></small>
                                                        <div class="invalid-feedback stock-error-msg" style="display: none;">Exceeds available stock!</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="number" name="items[{{ $index }}][rate]" class="form-control rate-input" value="{{ $item->rate }}" step="0.01">
                                                    <label>Rate *</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="number" name="items[{{ $index }}][mrp]" class="form-control mrp-input" value="{{ $item->mrp }}" step="0.01">
                                                    <label>MRP *</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" name="items[{{ $index }}][amount]" class="form-control amount-input" value="{{ number_format($item->amount, 2, '.', '') }}" readonly>
                                                    <label>Amount</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="sleeve-container">
                                                    <span class="sleeve-label">Sleeve Type</span>
                                                    <div class="segmented-control mt-1">
                                                        <input type="radio" name="items[{{ $index }}][sleeve]" id="sleeve_full_{{ $index }}" value="Full" {{ (is_array($item->sleeve) ? in_array('Full', $item->sleeve) : $item->sleeve == 'Full') ? 'checked' : '' }}>
                                                        <label for="sleeve_full_{{ $index }}">Full</label>
                                                        <input type="radio" name="items[{{ $index }}][sleeve]" id="sleeve_half_{{ $index }}" value="Half" {{ (is_array($item->sleeve) ? in_array('Half', $item->sleeve) : $item->sleeve == 'Half') ? 'checked' : '' }}>
                                                        <label for="sleeve_half_{{ $index }}">Half</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="items[{{ $index }}][stock_entry_item_id]" class="stock-entry-item-id" value="{{ $item->stock_entry_item_id ?? '' }}">
                                    </div>
                                @endforeach
                            @else
                                <div class="item-block" id="row-0">
                                    <div class="item-block-header">
                                        <span class="item-number">Item #1</span>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-2">
                                            <div class="form-floating form-floating-outline">
                                                <select name="items[0][brand_cat_id]" class="select2 form-select brand-select" data-placeholder="Brand Category">
                                                    <option value="">Select</option>
                                                    @foreach($brandCategories as $bc)
                                                        <option value="{{ $bc->id }}">{{ $bc->name }} - {{ $bc->code }}</option>
                                                    @endforeach
                                                </select>
                                                <label>Brand Category *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating form-floating-outline">
                                                <select name="items[0][item_id]" class="select2 form-select item-select" data-placeholder="Item">
                                                    <option value="">Select Item</option>
                                                </select>
                                                <label>Item *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-floating form-floating-outline">
                                                <select name="items[0][color_id]" class="select2 form-select" data-placeholder="Color">
                                                    <option value="">Select Color</option>
                                                    @foreach($colors as $col)
                                                        <option value="{{ $col->id }}">{{ $col->color_name }}</option>
                                                    @endforeach
                                                </select>
                                                <label>Color</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" name="items[0][art_no]" class="form-control" placeholder="Art No">
                                                <label>Art No *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-floating form-floating-outline">
                                                <select name="items[0][uom_id]" class="select2 form-select" data-placeholder="UOM">
                                                    <option value="">UOM</option>
                                                    @foreach($uoms as $u)
                                                        <option value="{{ $u->id }}" {{ $u->uom_code == 'PCS' ? 'selected' : '' }}>{{ $u->uom_code }}</option>
                                                    @endforeach
                                                </select>
                                                <label>UOM *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-floating form-floating-outline">
                                                <select name="items[0][size_id]" class="form-select select2 size-select">
                                                    <option value="">Select Size</option>
                                                </select>
                                                <label>Size *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-floating form-floating-outline">
                                                <input type="number" name="items[0][qty]" class="form-control qty-input" value="1" min="0.01" step="0.01">
                                                <label>Quantity *</label>
                                                <div class="stock-info-wrapper mt-1">
                                                    <small class="stock-label text-muted">Stock: <span class="available-stock-display">0.00</span></small>
                                                    <div class="invalid-feedback stock-error-msg" style="display: none;">Exceeds available stock!</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-floating form-floating-outline">
                                                <input type="number" name="items[0][rate]" class="form-control rate-input" placeholder="0.00" step="0.01">
                                                <label>Rate *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-floating form-floating-outline">
                                                <input type="number" name="items[0][mrp]" class="form-control mrp-input" placeholder="0.00" step="0.01">
                                                <label>MRP *</label>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" name="items[0][amount]" class="form-control amount-input" value="0.00" readonly>
                                                <label>Amount</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="sleeve-container">
                                                <span class="sleeve-label">Sleeve Type</span>
                                                <div class="segmented-control mt-1">
                                                    <input type="radio" name="items[0][sleeve]" id="sleeve_full_0" value="Full" checked>
                                                    <label for="sleeve_full_0">Full</label>
                                                    <input type="radio" name="items[0][sleeve]" id="sleeve_half_0" value="Half">
                                                    <label for="sleeve_half_0">Half</label>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="items[0][stock_entry_item_id]" class="stock-entry-item-id" value="">
                                    </div>
                                </div>
                            @endif
                            <input type="hidden" id="itemIndex" value="{{ (old('items') ? count(old('items')) : (isset($salesOrder) ? $salesOrder->items->count() : 1)) }}">
                        </div>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="card-header-box d-flex justify-content-between align-items-center mb-4">
                                    <h4>Additional Information</h4>
                                </div>
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
                                        <div class="mt-2 d-flex flex-wrap gap-2">
                                            @foreach(explode(',', $salesOrder->attachment) as $file)
                                            <a href="{{ url('uploads/so/' . $salesOrder->id . '/' . $file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="ri ri-file-line me-1"></i> View
                                            </a>
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
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="fw-medium">Total Qty:</label>
                                            <input type="text" class="form-control-plaintext text-end w-50 fw-bold" id="total_qty" name="total_qty" value="{{ old('total_qty', $salesOrder->total_qty ?? '0.00') }}" readonly>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="fw-medium">Sub Total:</label>
                                            <input type="text" class="form-control-plaintext text-end w-50 fw-bold" id="sub_total_qty" name="sub_total_qty" value="{{ old('sub_total_qty', $salesOrder->sub_total_qty ?? '0.00') }}" readonly>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="fw-medium">Discount:</label>
                                            <div class="input-group input-group-sm" style="width:120px;">
                                                <input type="number" class="form-control form-control-sm text-end" id="discount_percent" name="discount_percent" step="0.01" min="0" max="100" value="{{ old('discount_percent', $salesOrder->discount_percent ?? '0') }}">
                                                <span class="input-group-text px-1">%</span>
                                            </div>
                                        </div>
                                        <div class="text-end mt-1">
                                            <input type="text" class="form-control-plaintext form-control-sm text-end py-0" id="discount_amount" name="discount_amount" value="{{ old('discount_amount', $salesOrder->discount_amount ?? '0.00') }}" readonly>
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
                                                <input type="number" class="form-control form-control-sm text-end" style="width:100px;" id="round_off" name="round_off" step="0.01" min="0" value="{{ old('round_off', $salesOrder->round_off ?? '0.00') }}" autocomplete="off" readonly>
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
    .item-block {
        background: #fdfdfd;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        position: relative;
        transition: all 0.3s ease;
    }
    .item-block-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 10px;
    }
    .item-number {
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #666;
    }
    .remove_item_btn {
        background: #fff0f0;
        border: 1px solid #ffe0e0;
        border-radius: 8px;
        padding: 5px 10px;
        color: #ff4d4d;
        cursor: pointer;
        transition: all 0.2s;
    }
    .remove_item_btn:hover {
        background: #ff4d4d;
        color: #fff !important;
    }
    .sleeve-container {
        padding: 10px;
        border: 1px solid #eee;
        border-radius: 8px;
        background: #fff;
    }
    .sleeve-label {
        font-size: 11px;
        color: #999;
        margin-bottom: 2px;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
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
        padding: 8px 24px;
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

<script>
$(document).ready(function () {
    let itemIndex = Number($('#itemIndex').val()) || 0;

    $('.so_date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });
    $('.request_date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });
    $('.delivery_date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });

    function createRow() {
    let sleeveCbs = `
        <div class="segmented-control mt-1">
            <input type="radio" name="items[${itemIndex}][sleeve]" id="sleeve_full_${itemIndex}" value="Full" checked>
            <label for="sleeve_full_${itemIndex}">Full</label>
            <input type="radio" name="items[${itemIndex}][sleeve]" id="sleeve_half_${itemIndex}" value="Half">
            <label for="sleeve_half_${itemIndex}">Half</label>
        </div>`;

    let brandOpts = `<option value="">Select</option>`;
    @foreach($brandCategories as $bc)
    brandOpts += `<option value="{{ $bc->id }}">{{ addslashes($bc->name) }} - {{ addslashes($bc->code) }}</option>`;
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
        <div class="item-block" id="row-${itemIndex}">
            <div class="item-block-header">
                <span class="item-number">Item #</span>
                <button type="button" class="remove_item_btn" onclick="removeRow(${itemIndex})"><i class="ri ri-delete-bin-line"></i></button>
            </div>
            <div class="row g-3">
                <div class="col-md-2">
                    <div class="form-floating form-floating-outline">
                        <select name="items[${itemIndex}][brand_cat_id]" class="select2 form-select brand-select" data-placeholder="Brand Category">${brandOpts}</select>
                        <label>Brand Category *</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating form-floating-outline">
                        <select name="items[${itemIndex}][item_id]" class="select2 form-select item-select" data-placeholder="Item"><option value="">Select Item</option></select>
                        <label>Item *</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating form-floating-outline">
                        <select name="items[${itemIndex}][color_id]" class="select2 form-select" data-placeholder="Color">${colorOpts}</select>
                        <label>Color</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating form-floating-outline">
                        <input type="text" name="items[${itemIndex}][art_no]" class="form-control" placeholder="Art No">
                        <label>Art No *</label>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-floating form-floating-outline">
                        <select name="items[${itemIndex}][uom_id]" class="select2 form-select" data-placeholder="UOM">${uomOpts}</select>
                        <label>UOM *</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating form-floating-outline">
                        <select name="items[${itemIndex}][size_id]" class="select2 form-select size-select" data-placeholder="Size">
                            <option value="">Select Size</option>
                        </select>
                        <label>Size *</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating form-floating-outline">
                        <input type="number" name="items[${itemIndex}][qty]" class="form-control qty-input" value="1" min="0.01" step="0.01">
                        <label>Quantity *</label>
                        <div class="stock-info-wrapper mt-1">
                            <small class="stock-label text-muted">Stock: <span class="available-stock-display">0.00</span></small>
                            <div class="invalid-feedback stock-error-msg" style="display: none;">Exceeds available stock!</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating form-floating-outline">
                        <input type="number" name="items[${itemIndex}][rate]" class="form-control rate-input" min="0" step="0.01" placeholder="0.00">
                        <label>Rate *</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating form-floating-outline">
                        <input type="number" name="items[${itemIndex}][mrp]" class="form-control mrp-input" min="0" step="0.01" placeholder="0.00">
                        <label>MRP *</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-floating form-floating-outline">
                        <input type="text" name="items[${itemIndex}][amount]" class="form-control amount-input" value="0.00" readonly>
                        <label>Amount</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="sleeve-container">
                        <span class="sleeve-label">Sleeve Type</span>
                        ${sleeveCbs}
                    </div>
                </div>
                <input type="hidden" name="items[${itemIndex}][stock_entry_item_id]" class="stock-entry-item-id" value="">
            </div>
        </div>`;

    $('#item-rows').append(rowHtml);

    $('#item-rows .item-block:last .select2').each(function() {
        $(this).select2({ dropdownParent: $(this).closest('.item-block') });
    });

    itemIndex++;

    // Renumber all items after appending
    reindexItems();
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

    window.removeRow = function(id) {
        if ($('.item-block').length > 1) {
            $(`#row-${id}`).remove();
            reindexItems();
            calculateTotals();
        } else {
            alert("At least one item is required.");
        }
    }

    function reindexItems() {
        $('.item-block').each(function(index) {
            $(this).find('.item-number').text('Item #' + (index + 1));
        });
    }

    $(document).on('change', '.brand-select', function() {
        let brandId = $(this).val();
        let $row = $(this).closest('.item-block');
        let $itemSelect = $row.find('.item-select');
        
        $itemSelect.html('<option value="">Loading...</option>').trigger('change');
        
        if (brandId) {
            $.ajax({
                url: `{{ url('get-items-by-brand-category') }}/${brandId}`,
                type: 'GET',
                success: function(res) {
                    if (res.success) {
                        let opts = '<option value="">Select Item</option>';
                        res.items.forEach(it => {
                            opts += `<option value="${it.id}">${it.name} - ${it.code}</option>`;
                        });
                        $itemSelect.html(opts).trigger('change');
                    }
                }
            });
        } else {
            $itemSelect.html('<option value="">Select Item</option>').trigger('change');
        }
    });

    $(document).on('change', '.item-select', function() {
        let itemId = $(this).val();
        let $row = $(this).closest('.item-block');
        
        if (itemId) {
            $.ajax({
                url: `{{ url('get-item-details') }}/${itemId}`,
                type: 'GET',
                success: function(res) {
                    if (res.success) {
                        $row.data('stock-breakdown', res.stock_breakdown);
                        
                        let $uomSelect = $row.find('select[name*="[uom_id]"]');
                        if (!$uomSelect.val()) {
                            $uomSelect.val(res.uom_id).trigger('change');
                        }
                        
                        let $colorSelect = $row.find('select[name*="[color_id]"]');
                        if (!$colorSelect.val()) {
                            $colorSelect.val(res.color_id).trigger('change');
                        }

                        if (!$row.find('input[name*="[mrp]"]').val()) {
                             $row.find('input[name*="[mrp]"]').val(res.mrp);
                        }
                        
                        let $sizeSelect = $row.find('.size-select');
                        let selectedSize = $sizeSelect.data('selected') || $sizeSelect.val();
                        let sizes = [...new Set(res.stock_breakdown.map(s => s.size))];
                        let sizeOpts = '<option value="">Select Size</option>';
                        sizes.forEach(sz => {
                            sizeOpts += `<option value="${sz}" ${sz == selectedSize ? 'selected' : ''}>${sz}</option>`;
                        });
                        $sizeSelect.html(sizeOpts).trigger('change');
                        $sizeSelect.data('selected', ''); 

                        updateStockAndRate($row);
                    }
                }
            });
        } else {
            $row.data('stock-breakdown', null);
            $row.find('.available-stock-display').text('0.00');
            $row.find('.rate-input').val('0.00');
            $row.find('.qty-input').val('1');
            $row.find('.amount-input').val('0.00');
            $row.find('.size-select').html('<option value="">Select Size</option>').trigger('change');
            calculateTotals();
        }
    });

    function updateStockAndRate($row) {
        let size = $row.find('.size-select').val();
        let sleeve = $row.find('input[type="radio"][name*="[sleeve]"]:checked').val();
        let breakdown = $row.data('stock-breakdown') || [];
        
        let available = 0;
        let rate = 0;
        let stockEntryItemId = '';
        
        if (size && sleeve) {
            let match = breakdown.find(s => s.size == size && s.sleeve == sleeve);
            if (match) {
                available = match.balance || 0;
                rate = match.rate || 0;
                stockEntryItemId = match.stock_entry_item_id || '';
            } else {
                let sizeMatch = breakdown.find(s => s.size == size);
                if (sizeMatch) {
                    rate = sizeMatch.rate || 0;
                    stockEntryItemId = sizeMatch.stock_entry_item_id || '';
                }
            }
        } else if (size) {
            let sizeMatch = breakdown.find(s => s.size == size);
            if (sizeMatch) {
                rate = sizeMatch.rate || 0;
                stockEntryItemId = sizeMatch.stock_entry_item_id || '';
            }
        }

        $row.find('.available-stock-display').text(available.toFixed(2));
        $row.find('.stock-entry-item-id').val(stockEntryItemId);
        
        let currentRate = parseFloat($row.find('.rate-input').val()) || 0;
        if (currentRate === 0 && rate > 0) {
            $row.find('.rate-input').val(rate.toFixed(2)).trigger('input');
        } else {
            $row.find('.qty-input').trigger('input');
        }
    }

    $(document).on('change', '.size-select', function() {
        updateStockAndRate($(this).closest('.item-block'));
    });

    $('.item-block').each(function() {
        let $row = $(this);
        let itemId = $row.find('.item-select').val();
        if (itemId) {
            $row.find('.item-select').trigger('change');
        }
    });

    $(document).on('change', 'input[type="radio"][name*="[sleeve]"]', function() {
        if (this.checked) {
            updateStockAndRate($(this).closest('.item-block'));
        }
    });

    $(document).on('input', '.qty-input, .rate-input', function() {
        let $row = $(this).closest('.item-block');
        let qtyInput = $row.find('.qty-input');
        let qty = parseFloat(qtyInput.val()) || 0;
        let rate = parseFloat($row.find('.rate-input').val()) || 0;
        let available = parseFloat($row.find('.available-stock-display').text()) || 0;

        if (qty > available && available > 0) {
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
        $('.item-block').each(function() {
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

    $(".select2").each(function() {
        $(this).select2({ dropdownParent: $(this).closest('.card-body').length ? $(this).closest('.card-body') : $('body') });
    });

    calculateTotals();
});
</script>
@endsection
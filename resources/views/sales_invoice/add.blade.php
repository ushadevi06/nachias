@extends('layouts.common')
@section('title', ($invoice ? 'Edit' : 'Add') . ' Sales Invoice - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <form action="{{ isset($invoice) ? url('sales_invoices/add/' . $invoice->id) : url('sales_invoices/add') }}" method="POST" class="common-form" enctype="multipart/form-data">
                @csrf
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>{{ $invoice ? 'Edit' : 'Add' }} Sales Invoice</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('inv_no') is-invalid @enderror" id="inv_no" placeholder="Enter Invoice No" name="inv_no" value="{{ old('inv_no', isset($invoice) ? $invoice->inv_no : '') }}">
                                    <input type="hidden" name="is_manual_inv_no" id="is_manual_inv_no" value="{{ old('is_manual_inv_no', '0') }}">
                                    <label for="inv_no">Invoice No. <span class="text-danger">*</span> </label>
                                    @error('inv_no')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control form-control inv_date @error('inv_date') is-invalid @enderror" name="inv_date" placeholder="Enter Invoice Date" value="{{ old('inv_date', isset($invoice) ? $invoice->inv_date->format('d-m-Y') : date('d-m-Y')) }}" />
                                    <label for="inv_date">Invoice Date <span class="text-danger">*</span> </label>
                                    @error('inv_date')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="brand_id" name="brand_id" class="select2 form-select @error('brand_id') is-invalid @enderror" data-placeholder="Select Brand" {{ (isset($invoice) && ($invoice->einvoice_status === 'generated' || $invoice->delivery_status === 'Dispatched')) ? 'disabled' : '' }}>
                                        <option value="">Select Brand</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}" {{ (old('brand_id', isset($invoice) ? $invoice->brand_id : '') == $brand->id) ? 'selected' : '' }}>{{ $brand->brand_name }} ({{ $brand->code }})</option>
                                        @endforeach
                                    </select>
                                    @if(isset($invoice) && ($invoice->einvoice_status === 'generated' || $invoice->delivery_status === 'Dispatched'))
                                        <input type="hidden" name="brand_id" value="{{ $invoice->brand_id }}">
                                    @endif
                                    <label for="brand_id">Brand <span class="text-danger">*</span></label>
                                    @error('brand_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="customer_id" name="customer_id" class="select2 form-select @error('customer_id') is-invalid @enderror" data-placeholder="Select Customer/Buyer" {{ (isset($invoice) && ($invoice->einvoice_status === 'generated' || $invoice->delivery_status === 'Dispatched')) ? 'disabled' : '' }}>
                                        <option value="">Select Customer/Buyer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" data-state-id="{{ $customer->state_id }}" data-pincode="{{ $customer->zip_code }}" data-sales-discount="{{ $customer->sales_discount ?? 0 }}" {{ (old('customer_id', isset($invoice) ? $invoice->customer_id : '') == $customer->id) ? 'selected' : '' }}>{{ $customer->name }} ({{ $customer->code }})</option>
                                        @endforeach
                                    </select>
                                    @if(isset($invoice) && ($invoice->einvoice_status === 'generated' || $invoice->delivery_status === 'Dispatched'))
                                        <input type="hidden" name="customer_id" value="{{ $invoice->customer_id }}">
                                    @endif
                                    <label for="customer_id">Customer <span class="text-danger">*</span></label>
                                    @error('customer_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="so_ids" name="so_ids[]" class="select2 form-select @error('so_ids') is-invalid @enderror" multiple data-placeholder="Select Sales Orders" {{ (isset($invoice) && ($invoice->einvoice_status === 'generated' || $invoice->delivery_status === 'Dispatched')) ? 'disabled' : '' }}>
                                        @foreach($saleOrders as $so)
                                            <option value="{{ $so->id }}" {{ (is_array(old('so_ids', isset($invoice) && $invoice->so_ids ? json_decode($invoice->so_ids, true) : [])) && in_array($so->id, old('so_ids', isset($invoice) && $invoice->so_ids ? json_decode($invoice->so_ids, true) : []))) ? 'selected' : '' }}>{{ $so->so_no }}</option>
                                        @endforeach
                                    </select>
                                    @if(isset($invoice) && ($invoice->einvoice_status === 'generated' || $invoice->delivery_status === 'Dispatched'))
                                        @foreach(old('so_ids', isset($invoice) && $invoice->so_ids ? json_decode($invoice->so_ids, true) : []) as $soId)
                                            <input type="hidden" name="so_ids[]" value="{{ $soId }}">
                                        @endforeach
                                    @endif
                                    <label for="so_ids">Sales Order <span class="text-danger">*</span></label>
                                    @error('so_ids')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control @error('delivery_address') is-invalid @enderror" id="address" name="delivery_address" placeholder="Enter Delivery Address">{{ old('delivery_address', isset($invoice) ? $invoice->delivery_address : '') }}</textarea>
                                    <label for="address">Delivery Address <span class="text-danger">*</span></label>
                                    @error('delivery_address')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control" id="remarks" name="remarks" placeholder="Enter Remarks">{{ old('remarks', isset($invoice) ? $invoice->remarks : '') }}</textarea>
                                    <label for="remarks">Remarks</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="store_id" name="store_id" class="select2 form-select" data-placeholder="Select Store">
                                        <option value="">Select Store</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}" {{ (old('store_id', isset($invoice) ? $invoice->store_id : '') == $store->id) ? 'selected' : '' }}>{{ $store->store_type_name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="store_id">Store</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="agent_id" name="agent_id" class="select2 form-select" data-placeholder="Select Sales Executive">
                                        <option value="">Select Sales Executive</option>
                                        @foreach($sales_agent as $agent)
                                            <option value="{{ $agent->id }}" {{ (old('agent_id', isset($invoice) ? $invoice->agent_id : '') == $agent->id) ? 'selected' : '' }}>{{ $agent->name }}({{ $agent->code }})</option>
                                        @endforeach
                                    </select>
                                    <label for="agent_id">Sales Executive</label>
                                </div>
                            </div>
                            {{-- <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" class="form-control" id="commission_percent" name="commission_percent" placeholder="Commission %" value="{{ old('commission_percent', isset($invoice) ? $invoice->commission_percent : '') }}">
                                    <label for="commission_percent">Commission %</label>
                                </div>
                            </div> --}}
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" min="1" step="1" class="form-control @error('no_of_box') is-invalid @enderror"
                                        id="no_of_box" name="no_of_box" placeholder="No Of Box"
                                        value="{{ old('no_of_box', isset($invoice) ? $invoice->no_of_box : '') }}">
                                    <label for="no_of_box">No of Box</label>
                                    @error('no_of_box')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('hsn_sac') is-invalid @enderror"
                                        id="hsn_sac" name="hsn_sac" placeholder="HSN Code"
                                        value="{{ old('hsn_sac', isset($invoice) ? $invoice->hsn_sac : '62053000') }}">
                                    <label for="hsn_sac">HSN Code <span class="text-danger">*</span></label>
                                    @error('hsn_sac')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box d-flex justify-content-between align-items-center">
                            <div>
                                <h5>Item Details *</h5>
                                @error('items')
                                    <div class="text-danger small mb-2">{{ $message }}</div>
                                @enderror
                            </div>
                            @if(!(isset($invoice) && ($invoice->einvoice_status === 'generated' || $invoice->delivery_status === 'Dispatched')))
                            <button type="button" class="btn btn-primary btn-sm" id="btn_toggle_open_order">
                                <i class="ri ri-add-line"></i> Add Open Order Items
                            </button>
                            @endif
                        </div>
                        @if(!(isset($invoice) && ($invoice->einvoice_status === 'generated' || $invoice->delivery_status === 'Dispatched')))
                        <div class="row mb-4">
                            <div class="col-md-6 text-center">
                                <div class="input-group mb-2 mx-auto">
                                    <div class="form-floating form-floating-outline flex-grow-1">
                                        <input type="text" id="barcode_scanner" class="form-control border-primary" placeholder="Scan Barcode" autocomplete="off" style="border-width: 2px; border-right: none; border-top-right-radius: 0; border-bottom-right-radius: 0;" autofocus>
                                        <label for="barcode_scanner" class="text-primary fw-bold">SCAN BARCODE</label>
                                    </div>
                                    <button class="btn btn-outline-primary px-4" type="button" id="btn_camera_scan" style="border-width: 2px; border-left: none; border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                        <i class="ri ri-camera-line me-1"></i> CAMERA
                                    </button>
                                </div>
                                <div id="reader" class="rounded overflow-hidden mb-3 mx-auto" style="display: none; width: 100%; border: 1px solid #00bcd4;"></div>
                                <div id="scan_alert" class="alert alert-danger mt-3 mx-auto" style="display: none; max-width: 500px; text-align: left;">
                                    <span id="scan_msg"></span>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">S.No.</th>
                                        <th style="width: 20%;">Stock Item</th>
                                        <th style="width: 10%;">Color</th>
                                        <th style="width: 10%;">Art No</th>
                                        <th style="width: 8%;">UOM</th>
                                        <th style="width: 8%;">Size</th>
                                        <th style="width: 8%;">Quantity *</th>
                                        <th style="width: 10%;">MRP</th>
                                        <th style="width: 10%;">Price *</th>
                                        <th style="width: 12%;">Amount *</th>
                                        <th style="width: 5%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="item-rows">
                                    @php
                                        $items = old('items');
                                        if (!$items && isset($invoice)) {
                                            $items = $invoice->items->map(function ($item) use ($invoice) {
                                                $brandName = '';
                                                $itemName = '';
                                                $sleeveType = $item->sleeve_type;

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
                                                } elseif ($item->stockEntryItem) {
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

                                                if (empty($brandName) && !empty($item->sku)) {
                                                    $seItemBySku = \App\Models\StockEntryItem::where('sku', $item->sku)->first();
                                                    if ($seItemBySku) {
                                                        if ($seItemBySku->item && $seItemBySku->item->brand) {
                                                            $brandName = $seItemBySku->item->brand->brand_name;
                                                        } elseif ($seItemBySku->item && $seItemBySku->item->brandCategory) {
                                                            $brandName = $seItemBySku->item->brandCategory->name;
                                                        } else {
                                                            $brandName = $seItemBySku->finished_item_code;
                                                        }
                                                        if (empty($sleeveType)) {
                                                            $sleeveType = $seItemBySku->sleeve_type;
                                                        }
                                                    }
                                                }
                                                if (empty($brandName) && $item->brandCategory) {
                                                    $brandName = $item->brandCategory->name;
                                                }
                                                if (empty($itemName) && !empty($item->art_no)) {
                                                    $itemName = $item->art_no;
                                                }

                                                $maxQty = null;
                                                $soIds = [];
                                                if (!empty($invoice->so_ids)) {
                                                    $soIds = is_array($invoice->so_ids) ? $invoice->so_ids : json_decode($invoice->so_ids, true);
                                                }
                                                if (empty($soIds) && isset($invoice->so_id)) {
                                                    $soIds = [$invoice->so_id];
                                                }
                                                if (!empty($soIds)) {
                                                    $maxQty = \App\Models\SalesOrderItem::whereIn('sale_order_id', $soIds)
                                                        ->where(function ($q) use ($item) {
                                                            if ($item->stock_entry_item_id) {
                                                                $q->where('stock_entry_item_id', $item->stock_entry_item_id);
                                                            }
                                                            if ($item->sku) {
                                                                $q->orWhere('sku', $item->sku);
                                                            }
                                                            if (empty($item->stock_entry_item_id) && empty($item->sku)) {
                                                                $q->where('art_no', $item->art_no);
                                                            }
                                                        })
                                                        ->sum('qty');
                                                }

                                                $stockQty = 0;
                                                if (isset($item->sku) && $item->sku) {
                                                    $stockQtyQuery = \Illuminate\Support\Facades\DB::table('stock_entry_items')
                                                        ->where(function($q) use($item) {
                                                            $q->where('sku', $item->sku)
                                                              ->orWhere('barcode', $item->sku);
                                                        })
                                                        ->where('stock_type', 'finished_goods')
                                                        ->whereNull('deleted_at');
                                                    if (isset($item->size) && $item->size) {
                                                        $stockQtyQuery->where('size', $item->size);
                                                    }
                                                    $stockQty = $stockQtyQuery->sum(\Illuminate\Support\Facades\DB::raw('qty_in - COALESCE(qty_out, 0)')) ?? 0;
                                                } elseif (isset($item->stock_entry_item_id) && $item->stock_entry_item_id) {
                                                    $stockQty = \Illuminate\Support\Facades\DB::table('stock_entry_items')
                                                        ->where('id', $item->stock_entry_item_id)
                                                        ->whereNull('deleted_at')
                                                        ->value(\Illuminate\Support\Facades\DB::raw('qty_in - COALESCE(qty_out, 0)')) ?? 0;
                                                }

                                                $stockQty += (float)$item->quantity;

                                                return [
                                                    'brand_id' => $item->brand_id,
                                                    'brand_name' => $brandName ?: '',
                                                    'item_id' => $item->item_id,
                                                    'item_name' => $itemName ?: '',
                                                    'sleeve_type' => $sleeveType ?: '',
                                                    'color_id' => $item->color_id,
                                                    'color_name' => $item->api_color ?: ($item->color ? $item->color->color_name : ''),
                                                    'api_color' => $item->api_color,
                                                    'size' => $item->size,
                                                    'size_name' => $item->sizeRatio ? $item->sizeRatio->size : $item->size,
                                                    'art_no' => $item->art_no,
                                                    'hsn_sac' => $item->hsn_sac,
                                                    'uom_id' => $item->uom_id,
                                                    'uom_code' => $item->uom_id ?: '',
                                                    'quantity' => $item->quantity,
                                                    'max_qty' => $maxQty,
                                                    'stock_qty' => (float)$stockQty,
                                                    'rate' => $item->rate,
                                                    'mrp' => $item->mrp,
                                                    'amount' => $item->amount,
                                                    'sku' => $item->sku,
                                                    'stock_entry_item_id' => $item->stock_entry_item_id,
                                                    'is_extra' => $item->is_extra,
                                                    'is_open_order' => (isset($invoice) && $invoice->salesOrder && $invoice->salesOrder->order_type == 'Open') ? 1 : 0,
                                                    'id' => $item->id,
                                                ];
                                            })->toArray();
                                        }
                                    @endphp
                                    @php
                                        $mainItems = [];
                                        $openOrderItems = [];
                                        if ($items) {
                                            foreach ($items as $index => $row) {
                                                $rowObj = (object) $row;
                                                if (isset($rowObj->is_extra) && $rowObj->is_extra == 1) {
                                                    if (empty($rowObj->brand_name) && !empty($rowObj->stock_entry_item_id)) {
                                                        $stockItem = \App\Models\StockEntryItem::with(['brand', 'item', 'color'])->find($rowObj->stock_entry_item_id);
                                                        if ($stockItem) {
                                                            $rowObj->finished_item_code = $stockItem->finished_item_code ?? '';
                                                            $rowObj->brand_name = $stockItem->brand->brand_name ?? ($stockItem->item->brand->brand_name ?? '');
                                                            $rowObj->item_name = $stockItem->item->name ?? '';
                                                            $rowObj->sleeve_type = $stockItem->sleeve_type ?? '';
                                                            $rowObj->sku = $stockItem->sku ?? '';
                                                            $rowObj->api_color = $stockItem->api_color ?? '';
                                                            $rowObj->color_name = $stockItem->color->color_name ?? '';
                                                            $rowObj->art_no = $stockItem->art_no ?? '';
                                                            $rowObj->size_name = $stockItem->size;
                                                            $rowObj->stock_qty = ($stockItem->qty_in - ($stockItem->qty_out ?? 0)) + (float)$rowObj->quantity;
                                                        }
                                                    }
                                                    $openOrderItems[$index] = $rowObj;
                                                } else {
                                                    $mainItems[$index] = $rowObj;
                                                }
                                            }
                                        }
                                    @endphp
                                    @if($mainItems)
                                        @foreach($mainItems as $index => $row)
                                        @php $row = (object) $row; @endphp
                                        <tr class="item-row">
                                            <td class="s-no text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $row->brand_name ?? '' }}</div>
                                                @if(!empty($row->item_name) && $row->item_name !== ($row->brand_name ?? ''))
                                                    <div class="small text-muted">{{ $row->item_name }} ({{ $row->sleeve_type ?? '' }})</div>
                                                @endif
                                                @if(!empty($row->sku))
                                                    <div class="small text-primary">Barcode: {{ $row->sku }}</div>
                                                @endif
                                                <input type="hidden" class="invoice-item-db-id" name="items[{{ $index }}][id]" value="{{ $row->id ?? '' }}">
                                                <input type="hidden" name="items[{{ $index }}][brand_id]" class="brand-id" value="{{ $row->brand_id ?? '' }}">
                                                <input type="hidden" name="items[{{ $index }}][brand_name]" class="brand-name" value="{{ $row->brand_name ?? '' }}">
                                                <input type="hidden" name="items[{{ $index }}][item_id]" class="item-id" value="{{ $row->item_id ?? '' }}">
                                                <input type="hidden" name="items[{{ $index }}][item_name]" class="item-name" value="{{ $row->item_name ?? '' }}">
                                                <input type="hidden" name="items[{{ $index }}][sleeve_type]" class="sleeve-type" value="{{ $row->sleeve_type ?? '' }}">
                                                <input type="hidden" name="items[{{ $index }}][sku]" class="sku" value="{{ $row->sku ?? '' }}">
                                                <input type="hidden" name="items[{{ $index }}][stock_entry_item_id]" class="stock-entry-item-id" value="{{ $row->stock_entry_item_id ?? '' }}">
                                                <input type="hidden" name="items[{{ $index }}][max_qty]" class="max-qty" value="{{ $row->max_qty ?? '' }}">
                                                <input type="hidden" name="items[{{ $index }}][stock_qty]" class="stock-qty" value="{{ $row->stock_qty ?? '' }}">
                                                <input type="hidden" name="items[{{ $index }}][is_extra]" class="is-extra" value="{{ $row->is_extra ?? '' }}">
                                            </td>
                                            <td>
                                                <span class="color-text">{{ !empty($row->api_color) ? $row->api_color : ($row->color_name ?? '-') }}</span>
                                                <input type="hidden" name="items[{{ $index }}][color_id]" class="color-id" value="{{ $row->color_id ?? '' }}">
                                                <input type="hidden" name="items[{{ $index }}][api_color]" class="api-color" value="{{ $row->api_color ?? '' }}">
                                                <input type="hidden" name="items[{{ $index }}][color_name]" class="color-name" value="{{ $row->color_name ?? '' }}">
                                            </td>
                                            <td>
                                                <span class="art-no-text">{{ $row->art_no ?? '' }}</span>
                                                <input type="hidden" name="items[{{ $index }}][art_no]" class="art-no" value="{{ $row->art_no ?? '' }}">
                                            </td>
                                            <td>
                                                <span class="uom-text">{{ $row->uom_code ?? '' }}</span>
                                                <input type="hidden" name="items[{{ $index }}][uom_id]" class="uom-id" value="{{ $row->uom_id ?? '' }}">
                                                <input type="hidden" name="items[{{ $index }}][uom_code]" class="uom-code" value="{{ $row->uom_code ?? '' }}">
                                            </td>
                                            <td>
                                                <span class="size-text">{{ $row->size_name ?? '' }}</span>
                                                <input type="hidden" name="items[{{ $index }}][size]" class="size-id" value="{{ $row->size ?? '' }}">
                                                <input type="hidden" name="items[{{ $index }}][size_name]" class="size-name" value="{{ $row->size_name ?? '' }}">
                                            </td>
                                            <td>
                                                <div class="form-floating form-floating-outline">
                                                    <input type="number" step="any" class="form-control qty" name="items[{{ $index }}][quantity]" value="{{ $row->quantity ?? '' }}" data-original-qty="{{ $row->quantity ?? 0 }}" data-max="{{ (isset($row->is_open_order) && $row->is_open_order == 1) ? '' : ($row->max_qty ?? '') }}" data-stock="{{ $row->stock_qty ?? '' }}" max="{{ (isset($row->is_open_order) && $row->is_open_order == 1) ? '' : ($row->max_qty ?? '') }}" placeholder="Qty" {{ (isset($invoice) && ($invoice->einvoice_status === 'generated' || $invoice->delivery_status === 'Dispatched')) ? 'readonly' : '' }}>
                                                    <label>Qty *</label>
                                                </div>
                                                <div class="qty-error text-danger small" style="display:none;"></div>
                                                @if(isset($row->max_qty) && $row->max_qty !== '' && (!isset($row->is_open_order) || $row->is_open_order != 1))
                                                    <small class="text-info d-block">Ordered: {{ $row->max_qty }}</small>
                                                @endif
                                                @if(isset($row->stock_qty) && $row->stock_qty !== '')
                                                    <small class="{{ max(0, $row->stock_qty) < ($row->max_qty ?? 0) ? 'text-danger' : 'text-muted' }} d-block" style="font-weight: 500;">Stock: {{ max(0, $row->stock_qty) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="form-floating form-floating-outline">
                                                    <input type="number" step="any" class="form-control mrp" name="items[{{ $index }}][mrp]" value="{{ $row->mrp ?? '' }}" placeholder="MRP">
                                                    <label>MRP</label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-floating form-floating-outline">
                                                    <input type="number" step="any" class="form-control rate" name="items[{{ $index }}][rate]" value="{{ $row->rate ?? '' }}" placeholder="Price">
                                                    <label>Price *</label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" class="form-control amount" name="items[{{ $index }}][amount]" value="{{ $row->amount ?? '' }}" placeholder="Amount" readonly>
                                                    <label>Amount *</label>
                                                </div>
                                            </td>
                                            <td>
                                                @if(isset($invoice) && ($invoice->einvoice_status === 'generated' || $invoice->delivery_status === 'Dispatched'))
                                                    <span class="text-muted">-</span>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-danger remove-item"><i class="ri ri-delete-bin-line"></i></button>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr class="item-row">
                                            <td colspan="10" class="text-center">No items found</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Open Order Items Section -->
                <div class="card mb-4" id="open_order_section" style="display: {{ !empty($openOrderItems) ? 'block' : 'none' }}; border-left: 3px solid #00bcd4;">
                    <div class="card-header">
                        <h5 class="mb-0 text-primary">Open Order Item Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6 text-center">
                                <div class="input-group mb-2 mx-auto">
                                    <div class="form-floating form-floating-outline flex-grow-1">
                                        <input type="text" id="open_order_barcode_scanner" class="form-control border-primary" placeholder="Scan Barcode" autocomplete="off" style="border-width: 2px; border-right: none; border-top-right-radius: 0; border-bottom-right-radius: 0;">
                                        <label for="open_order_barcode_scanner" class="text-primary fw-bold">SCAN BARCODE</label>
                                    </div>
                                    <button class="btn btn-outline-primary px-4" type="button" id="btn_open_order_camera_scan" style="border-width: 2px; border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                        <i class="ri ri-camera-line me-1"></i> CAMERA
                                    </button>
                                </div>
                                <div id="open_order_reader" class="rounded overflow-hidden mb-3 mx-auto" style="display: none; width: 100%; border: 1px solid #00bcd4;"></div>
                                <div id="open_order_scan_alert" class="alert alert-danger mt-3 mx-auto" style="display: none; max-width: 500px; text-align: left;">
                                    <span id="open_order_scan_msg"></span>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                                <small class="text-muted">Tip: Scan a barcode or type item code to quickly add it to the open order.</small>
                            </div>
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered align-middle" id="open-order-item-rows">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width: 250px;">Stock Item *</th>
                                        <th style="min-width: 200px;">SKU</th>
                                        <th style="min-width: 150px;">Color</th>
                                        <th style="min-width: 100px;">Sleeve</th>
                                        <th style="min-width: 150px;">Art No *</th>
                                        <th style="min-width: 100px;">UOM *</th>
                                        <th style="min-width: 120px;">Size *</th>
                                        <th style="min-width: 120px;">Quantity *</th>
                                        <th style="min-width: 160px;">MRP</th>
                                        <th style="min-width: 120px;">Selling Price *</th>
                                        <th style="min-width: 120px;">Amount</th>
                                        <th style="min-width: 50px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($openOrderItems))
                                        @foreach($openOrderItems as $index => $row)
                                            @php
                                                /* $displayName = !empty($row->finished_item_code) ? $row->finished_item_code : ($row->sku ?? '');
                                                if (!empty($row->item_name)) {
                                                    $displayName .= ' - ' . $row->item_name;
                                                } */
                                               $displayName = !empty($row->brand_name) ? $row->brand_name.' ' : (!empty($row->finished_item_code) ? $row->finished_item_code : ($row->sku ?? ''));
                                            @endphp
                                            <tr class="item-row">
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" class="form-control" value="{{ $displayName }}" readonly tabindex="-1">
                                                        <label>Stock Item*</label>
                                                    </div>
                                                    <input type="hidden" class="invoice-item-db-id" name="items[{{ $index }}][id]" value="{{ $row->id ?? '' }}">
                                                    <input type="hidden" name="items[{{ $index }}][stock_entry_item_id]" class="stock-entry-item-id" value="{{ $row->stock_entry_item_id ?? '' }}">
                                                    <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $row->item_id ?? '' }}">
                                                    <input type="hidden" name="items[{{ $index }}][brand_cat_id]" value="{{ $row->brand_cat_id ?? '' }}">
                                                    <input type="hidden" name="items[{{ $index }}][is_extra]" value="1">
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" name="items[{{ $index }}][sku]" class="form-control sku-input" value="{{ $row->sku ?? '' }}" readonly tabindex="-1">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" name="items[{{ $index }}][api_color]" class="form-control" value="{{ !empty($row->api_color) ? $row->api_color : ($row->color_name ?? '') }}">
                                                        <input type="hidden" name="items[{{ $index }}][color_id]" class="color-id-input" value="{{ $row->color_id ?? '' }}">
                                                        <input type="hidden" name="items[{{ $index }}][color_name]" value="{{ $row->color_name ?? '' }}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" name="items[{{ $index }}][sleeve_type]" class="form-control" value="{{ $row->sleeve_type ?? '' }}" readonly tabindex="-1">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" name="items[{{ $index }}][art_no]" class="form-control art-no-input" value="{{ $row->art_no ?? '' }}" readonly tabindex="-1">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" class="form-control" value="{{ !empty($row->uom_code) ? $row->uom_code : 'PCS' }}" readonly tabindex="-1">
                                                        <input type="hidden" name="items[{{ $index }}][uom_id]" value="{{ !empty($row->uom_id) ? $row->uom_id : 'PCS' }}">
                                                        <input type="hidden" name="items[{{ $index }}][uom_code]" value="{{ !empty($row->uom_code) ? $row->uom_code : 'PCS' }}">
                                                        <label>UOM</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" name="items[{{ $index }}][size_name]" class="form-control size-input" value="{{ $row->size_name ?? '' }}" readonly tabindex="-1">
                                                        <input type="hidden" name="items[{{ $index }}][size]" value="{{ $row->size_name ?? '' }}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="number" name="items[{{ $index }}][quantity]" class="form-control qty-input open-qty" value="{{ $row->quantity ?? '' }}" data-original-qty="{{ $row->quantity ?? 0 }}" min="0.01" step="0.01">
                                                    </div>
                                                    <div class="stock-info-wrapper mt-1">
                                                        <small class="stock-label text-muted">Stock: <span class="available-stock-display">{{ number_format(max(0, $row->stock_qty ?? 0), 2) }}</span></small>
                                                        <div class="text-danger small stock-error-msg" style="display: none; font-weight: 500;">Exceeds stock!</div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="number" name="items[{{ $index }}][mrp]" class="form-control mrp-input" value="{{ $row->mrp ?? '' }}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="number" name="items[{{ $index }}][rate]" class="form-control rate-input open-rate" value="{{ $row->rate ?? '' }}" step="0.01">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" name="items[{{ $index }}][amount]" class="form-control amount-input amount open-amount" value="{{ $row->amount ?? '' }}" readonly tabindex="-1">
                                                    </div>
                                                </td>
                                                <td>
                                                    @if(isset($invoice) && ($invoice->einvoice_status === 'generated' || $invoice->delivery_status === 'Dispatched'))
                                                        <span class="text-muted">-</span>
                                                    @else
                                                        <button type="button" class="btn btn-danger btn-sm open_delete_row"><i class="ri ri-delete-bin-line"></i></button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            <input type="hidden" id="openOrderItemIndex" value="0">
                        </div>
                    </div>
                </div>
                <!-- End Open Order Items Section -->
                <div class="row g-4">
                    <!-- Invoice Details Card -->
                    <div class="col-md-12 col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-header-box">
                                    <h5>Invoice Details</h5>
                                </div>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <select name="invoice_status" id="invoice_status" class="form-select select2 @error('invoice_status') is-invalid @enderror" data-placeholder="Select Invoice Status">
                                                <option value="">Select Invoice Status</option>
                                                <option value="Draft" {{ old('invoice_status', isset($invoice) ? $invoice->invoice_status : '') == 'Draft' ? 'selected' : '' }}>Draft</option>
                                                <option value="Unpaid/Credit" {{ old('invoice_status', isset($invoice) ? $invoice->invoice_status : '') == 'Unpaid/Credit' ? 'selected' : '' }}>Unpaid/Credit</option>
                                                <option value="Paid" {{ old('invoice_status', isset($invoice) ? $invoice->invoice_status : '') == 'Paid' ? 'selected' : '' }}>Paid</option>
                                                <option value="Partially Paid" {{ old('invoice_status', isset($invoice) ? $invoice->invoice_status : '') == 'Partially Paid' ? 'selected' : '' }}>Partially Paid</option>
                                            </select>
                                            <label for="invoice_status">Invoice Status <span class="text-danger">*</span></label>
                                            @error('invoice_status')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <select name="payment_mode" id="payment_mode" class="form-select select2 @error('payment_mode') is-invalid @enderror" data-placeholder="Select Payment Mode">
                                                <option value="">Select Payment Mode</option>
                                                <option value="Cash" {{ old('payment_mode', isset($invoice) ? $invoice->payment_mode : '') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                                <option value="Bank (Cheque)" {{ old('payment_mode', isset($invoice) ? $invoice->payment_mode : '') == 'Bank (Cheque)' ? 'selected' : '' }}>Bank (Cheque)</option>
                                                <option value="Online (UPI)" {{ old('payment_mode', isset($invoice) ? $invoice->payment_mode : '') == 'Online (UPI)' ? 'selected' : '' }}>Online (UPI)</option>
                                            </select>
                                            <label for="payment_mode">Payment Mode</label>
                                            @error('payment_mode')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Cheque / UPI field -->
                                    <div class="col-md-12" id="extra_field" style="display:none;">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" name="extra_input" id="extra_input" placeholder="Enter Cheque / UPI No" value="{{ old('extra_input', isset($invoice) ? $invoice->extra_input : '') }}">
                                            <label id="extra_label">Cheque / UPI No</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control due_date" id="due_date" name="due_date" value="{{ old('due_date', isset($invoice) ? ($invoice->due_date ? $invoice->due_date->format('d-m-Y') : '') : date('d-m-Y')) }}">
                                            <label for="due_date">Due Date</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline mb-4">
                                            <input type="number" step="any" class="form-control" id="other_charges" name="other_charges" placeholder="Courier Charge" value="{{ old('other_charges', isset($invoice) ? number_format($invoice->other_charges, 2, '.', '') : '0.00') }}">
                                            <label for="other_charges">Courier Charge</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <textarea class="form-control" id="notes" name="notes" placeholder="Additional Notes">{{ old('notes', isset($invoice) ? $invoice->notes : '') }}</textarea>
                                            <label for="notes">Additional Notes</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <input type="file" class="form-control" id="signature_file" name="signature_file" accept="image/*">
                                            <label for="signature_file">Authorized Signature / Stamp Upload</label>
                                            @if(isset($invoice) && $invoice->signature_file)
                                                @php
                                                    $sigExt = pathinfo($invoice->signature_file, PATHINFO_EXTENSION);
                                                    $isSigImage = in_array(strtolower($sigExt), ['jpg', 'jpeg', 'png']);
                                                    $sigUrl = asset($invoice->signature_file);
                                                @endphp
                                                <div class="mt-2 p-1 border rounded d-inline-flex align-items-center bg-light shadow-sm">
                                                    @if($isSigImage)
                                                        <img src="{{ $sigUrl }}" class="rounded cursor-pointer view-image" data-image="{{ $sigUrl }}" width="45" height="45" style="object-fit: cover;" alt="Signature">
                                                    @else
                                                        <a href="{{ $sigUrl }}" target="_blank" class="text-decoration-none d-flex align-items-center px-2">
                                                            @if(strtolower($sigExt) == 'pdf')
                                                                <i class="ri ri-file-pdf-2-line text-danger fs-3"></i>
                                                            @else
                                                                <i class="ri ri-file-text-line text-primary fs-3"></i>
                                                            @endif
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif
                                            <small class="text-muted d-block mt-1">Max file size: 2MB. Supported formats: JPG, PNG, JPEG</small>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline">
                                            <input type="file" class="form-control" id="attachment_file" name="attachment_file">
                                            <label for="attachment_file">Attachments</label>
                                            @if(isset($invoice) && $invoice->attachment_file)
                                                @php
                                                    $attExt = pathinfo($invoice->attachment_file, PATHINFO_EXTENSION);
                                                    $isAttImage = in_array(strtolower($attExt), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                    $attUrl = asset($invoice->attachment_file);
                                                @endphp
                                                <div class="mt-2 p-1 border rounded d-inline-flex align-items-center bg-light shadow-sm">
                                                    @if($isAttImage)
                                                        <img src="{{ $attUrl }}" class="rounded cursor-pointer view-image" data-image="{{ $attUrl }}" width="45" height="45" style="object-fit: cover;" alt="Attachment">
                                                    @else
                                                        <a href="{{ $attUrl }}" target="_blank" class="text-decoration-none d-flex align-items-center px-2">
                                                            @if(strtolower($attExt) == 'pdf')
                                                                <i class="ri ri-file-pdf-2-line text-danger fs-3"></i>
                                                            @else
                                                                <i class="ri ri-file-text-line text-primary fs-3"></i>
                                                            @endif
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif
                                            <small class="text-muted d-block mt-1">Max file size: 2MB. Supported formats: JPG, PNG, JPEG, WEBP, PDF, DOC, DOCX</small>
                                        </div>
                                    </div>
                                    <!-- Show Fields in Customer Invoice PDF -->
                                    <div class="border-top pt-5 mt-5">
                                        <h6 class="fw-bold mb-2">Show in Customer Invoice PDF</h6>
                                        <div class="row">
                                            @php
                                                $selected_fields = old('show_fields', isset($invoice->show_fields) ? $invoice->show_fields : ['amount', 'discount', 'tax', 'subtotal', 'grandtotal','mrp','price']);
                                            @endphp
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="show_amount" name="show_fields[]" value="amount" {{ in_array('amount', $selected_fields) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="show_amount">Show Amount</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="show_discount" name="show_fields[]" value="discount" {{ in_array('discount', $selected_fields) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="show_discount">Show Discount</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="show_tax" name="show_fields[]" value="tax" {{ in_array('tax', $selected_fields) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="show_tax">Show Tax (GST/IGST)</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4 mt-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="show_subtotal" name="show_fields[]" value="subtotal" {{ in_array('subtotal', $selected_fields) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="show_subtotal">Show Sub Total</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4 mt-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="show_grandtotal" name="show_fields[]" value="grandtotal" {{ in_array('grandtotal', $selected_fields) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="show_grandtotal">Show Grand Total</label>
                                                </div>
                                            </div>
            
                                            <div class="col-md-6 col-lg-4 mt-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="show_mrp" name="show_fields[]" value="mrp" {{ in_array('mrp', $selected_fields) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="show_mrp">Show MRP</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4 mt-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="show_price" name="show_fields[]" value="price" {{ in_array('price', $selected_fields) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="show_price">Show Price</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Show Fields in Delivery Order PDF -->
                                    <div class="border-top pt-5 mt-5">
                                        <h6 class="fw-bold mb-2">Show in Delivery Order PDF</h6>
                                        <div class="row">
                                            @php
                                                $selected_delivery_fields = old('delivery_show_fields', isset($invoice->delivery_show_fields) ? $invoice->delivery_show_fields : ['mrp', 'price', 'art_no']);
                                            @endphp
                                            <div class="col-md-6 col-lg-4 mt-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="delivery_show_mrp" name="delivery_show_fields[]" value="mrp" {{ in_array('mrp', $selected_delivery_fields) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="delivery_show_mrp">Show Retail Price (MRP)</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4 mt-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="delivery_show_price" name="delivery_show_fields[]" value="price" {{ in_array('price', $selected_delivery_fields) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="delivery_show_price">Show Unit Price</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-lg-4 mt-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="delivery_show_art_no" name="delivery_show_fields[]" value="art_no" {{ in_array('art_no', $selected_delivery_fields) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="delivery_show_art_no">Show Art No</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="card-header-box mb-4">
                                    <h5 class="mb-0">Invoice Summary</h5>
                                </div>
                                <div class="summary-box px-2">
                                    <div class="d-flex justify-content-between align-items-center mb-5">
                                        <span class="text-secondary fw-medium">Total Quantity:</span>
                                        <span class="fw-bold mb-0" id="total_qty_val">{{ old('total_qty', isset($invoice) && isset($invoice->items) ? number_format($invoice->items->sum('quantity'), 2, '.', '') : '0.00') }}</span>
                                        <input type="hidden" name="total_qty" id="total_qty" value="{{ old('total_qty', isset($invoice) && isset($invoice->items) ? number_format($invoice->items->sum('quantity'), 2, '.', '') : '0.00') }}">
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-5">
                                        <span class="text-secondary fw-medium">Sub total:</span>
                                        <span class="fw-bold mb-0" id="sub_total_val">{{ old('sub_total', isset($invoice) ? number_format($invoice->sub_total, 2, '.', '') : '0.00') }}</span>
                                        <input type="hidden" name="sub_total" id="sub_total" value="{{ old('sub_total', isset($invoice) ? number_format($invoice->sub_total, 2, '.', '') : '0.00') }}">
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="d-flex align-items-center">
                                            <span class="text-secondary fw-medium me-2">Sales Discount (%):</span>
                                            <input type="number" step="any" min="0" class="form-control form-control-sm text-end" style="width: 80px;" name="sales_discount" id="sales_discount" value="{{ old('sales_discount', isset($invoice) ? number_format($invoice->sales_discount > 0 ? $invoice->sales_discount : $invoice->discount_percent, 2, '.', '') : '0.00') }}">
                                        </div>
                                        <span class="fw-bold mb-0" id="sales_discount_amount_val">₹0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <span class="text-secondary fw-medium me-2">Box Discount (₹/pc):</span>
                                            <input type="number" step="any" min="0" class="form-control form-control-sm text-end" style="width: 80px;" name="box_discount_amount" id="box_discount_amount" value="{{ old('box_discount_amount', isset($invoice) ? number_format($invoice->box_discount_amount, 2, '.', '') : '0.00') }}">
                                        </div>
                                        <span class="fw-bold mb-0" id="box_discount_amount_val">₹0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="text-secondary fw-medium">Total Discount Amount:</span>
                                        <span class="fw-bold mb-0 text-danger" id="discount_val">₹0.00</span>
                                        <input type="hidden" name="discount" id="discount" value="{{ old('discount', isset($invoice) ? number_format($invoice->discount, 2, '.', '') : '0.00') }}">
                                        <input type="hidden" name="discount_percent" id="discount_percent" value="{{ old('discount_percent', isset($invoice) ? number_format($invoice->discount_percent, 2, '.', '') : '0.00') }}">
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3" style="display: none !important;">
                                        <span class="text-secondary fw-medium">Pre-GST Charges:</span>
                                        <span class="fw-bold mb-0" id="pre_gst_charges_val">{{ old('pre_gst_charges', isset($invoice) ? number_format($invoice->pre_gst_charges, 2, '.', '') : '0.00') }}</span>
                                        <input type="hidden" name="pre_gst_charges" id="pre_gst_charges" value="{{ old('pre_gst_charges', isset($invoice) ? number_format($invoice->pre_gst_charges, 2, '.', '') : '0.00') }}">
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <span class="text-secondary fw-medium">Net Amount (Before Tax):</span>
                                        <span class="fw-bold h5 mb-0" id="total_val">{{ old('total', isset($invoice) ? number_format($invoice->total, 2, '.', '') : '0.00') }}</span>
                                        <input type="hidden" name="total" id="total" value="{{ old('total', isset($invoice) ? number_format($invoice->total, 2, '.', '') : '0.00') }}">
                                    </div>
                                    {{-- <div class="d-flex justify-content-between align-items-center mb-5">
                                        <span class="text-secondary fw-medium">Commission Amount:</span>
                                        <span class="fw-bold" id="commission_amount_val">{{ old('commission_amount', isset($invoice) ? number_format($invoice->commission_amount, 2, '.', '') : '0.00') }}</span>
                                        <input type="hidden" name="commission_amount" id="commission_amount" value="{{ old('commission_amount', isset($invoice) ? number_format($invoice->commission_amount, 2, '.', '') : '0.00') }}">
                                    </div> --}}
                                    <div class="mb-4 pt-2 border-top">
                                        <label class="text-secondary fw-medium mb-2 d-block">Other State?</label>
                                        <div class="d-flex gap-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="other_state" id="other_state_yes" value="yes" {{ old('other_state', isset($invoice) && $invoice->other_state ? 'yes' : 'no') == 'yes' ? 'checked' : '' }} onclick="return false;">
                                                <label class="form-check-label" for="other_state_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="other_state" id="other_state_no" value="no" {{ old('other_state', isset($invoice) && $invoice->other_state ? 'yes' : 'no') == 'no' ? 'checked' : '' }} onclick="return false;">
                                                <label class="form-check-label" for="other_state_no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="igst_section" style="display:none;">
                                        <div class="row g-2 align-items-center mb-3">
                                            <div class="col-4"><span class="text-secondary fw-medium">IGST</span></div>
                                            <div class="col-8 d-flex align-items-center">
                                                <div class="input-group input-group-sm ms-auto" style="width: 140px;">
                                                    <input type="number" step="any" name="igst_percent" id="igst_percent" class="form-control text-end" value="{{ old('igst_percent', isset($invoice) ? number_format($invoice->igst_percent, 2, '.', '') : '18.00') }}" {{ (isset($invoice) && $invoice->einvoice_status === 'generated') ? 'readonly' : '' }}>
                                                    <span class="input-group-text bg-white">%</span>
                                                </div>
                                                <span class="fw-bold ms-3" style="text-align: right;" id="igst_val">{{ old('igst', isset($invoice) ? number_format($invoice->igst, 2, '.', '') : '0.00') }}</span>
                                                <input type="hidden" name="igst" id="igst" value="{{ old('igst', isset($invoice) ? number_format($invoice->igst, 2, '.', '') : '0.00') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div id="cgst_sgst_section">
                                        <div class="row g-2 align-items-center mb-2">
                                            <div class="col-4"><span class="text-secondary fw-medium">CGST</span></div>
                                            <div class="col-8 d-flex align-items-center">
                                                <div class="input-group input-group-sm ms-auto" style="width: 140px;">
                                                    <input type="number" step="any" name="cgst_percent" id="cgst_percent" class="form-control text-end" value="{{ old('cgst_percent', isset($invoice) ? number_format($invoice->cgst_percent, 2, '.', '') : '9.00') }}" {{ (isset($invoice) && $invoice->einvoice_status === 'generated') ? 'readonly' : '' }}>
                                                    <span class="input-group-text bg-white">%</span>
                                                </div>
                                                <span class="fw-bold ms-3" style="text-align: right;" id="cgst_val">{{ old('cgst', isset($invoice) ? number_format($invoice->cgst, 2, '.', '') : '0.00') }}</span>
                                                <input type="hidden" name="cgst" id="cgst" value="{{ old('cgst', isset($invoice) ? number_format($invoice->cgst, 2, '.', '') : '0.00') }}">
                                            </div>
                                        </div>
                                        <div class="row g-2 align-items-center mb-3">
                                            <div class="col-4"><span class="text-secondary fw-medium">SGST</span></div>
                                            <div class="col-8 d-flex align-items-center">
                                                <div class="input-group input-group-sm ms-auto" style="width: 140px;">
                                                    <input type="number" step="any" name="sgst_percent" id="sgst_percent" class="form-control text-end" value="{{ old('sgst_percent', isset($invoice) ? number_format($invoice->sgst_percent, 2, '.', '') : '9.00') }}" {{ (isset($invoice) && $invoice->einvoice_status === 'generated') ? 'readonly' : '' }}>
                                                    <span class="input-group-text bg-white">%</span>
                                                </div>
                                                <span class="fw-bold ms-3" style="text-align: right;" id="sgst_val">{{ old('sgst', isset($invoice) ? number_format($invoice->sgst, 2, '.', '') : '0.00') }}</span>
                                                <input type="hidden" name="sgst" id="sgst" value="{{ old('sgst', isset($invoice) ? number_format($invoice->sgst, 2, '.', '') : '0.00') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-2 border-top border-bottom mb-3">
                                        <span class="text-secondary fw-medium">Tax Amount:</span>
                                        <span class="fw-bold" id="tax_amount_val">{{ old('tax_amount', isset($invoice) ? number_format($invoice->tax_amount, 2, '.', '') : '0.00') }}</span>
                                        <input type="hidden" name="tax_amount" id="tax_amount" value="{{ old('tax_amount', isset($invoice) ? number_format($invoice->tax_amount, 2, '.', '') : '0.00') }}">
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="text-secondary fw-medium">Courier Charge:</span>
                                        <span class="fw-bold mb-0" id="other_charges_val">{{ old('other_charges', isset($invoice) ? number_format($invoice->other_charges, 2, '.', '') : '0.00') }}</span>
                                        <input type="hidden" name="other_charges" id="other_charges" value="{{ old('other_charges', isset($invoice) ? number_format($invoice->other_charges, 2, '.', '') : '0.00') }}">
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3" style="display: none !important;">
                                        <span class="text-secondary fw-medium">Post GST charges:</span>
                                        <span class="fw-bold mb-0" id="post_gst_charges_val">{{ old('post_gst_charges', isset($invoice) ? number_format($invoice->post_gst_charges, 2, '.', '') : '0.00') }}</span>
                                        <input type="hidden" name="post_gst_charges" id="post_gst_charges" value="{{ old('post_gst_charges', isset($invoice) ? number_format($invoice->post_gst_charges, 2, '.', '') : '0.00') }}">
                                    </div>
                                    <div class="row g-2 align-items-center mb-1 pb-1">
                                        <div class="col-4"><span class="text-secondary fw-medium">Total Before Round Off:</span></div>
                                        <div class="col-8 d-flex align-items-center justify-content-end">
                                            <input type="text" id="total_before_round_off" class="form-control-plaintext form-control-sm text-end fw-bold" style="width: 100px;" value="0.00" readonly>
                                        </div>
                                    </div>
                                    <div class="row g-2 align-items-center mb-3 pb-3 border-bottom">
                                        <div class="col-4"><span class="text-secondary fw-medium">Round Off:</span></div>
                                        <div class="col-8 d-flex align-items-center justify-content-end">
                                            <div class="d-flex gap-3 me-3">
                                                <input type="hidden" name="round_off_type" id="round_off_type" value="{{ old('round_off_type', isset($invoice) ? $invoice->round_off_type : 'Add') }}">
                                            </div>
                                            <input type="text" id="round_off_display" class="form-control-plaintext form-control-sm text-end fw-bold" style="width: 100px;" value="0.00" readonly>
                                            <input type="hidden" name="round_off" id="round_off" value="{{ old('round_off', isset($invoice) ? number_format($invoice->round_off, 2, '.', '') : '0.00') }}">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <span class="fw-bold mb-0">Grand Total:</span>
                                        <span class="fw-bold mb-0 text-primary" id="grand_total_val">{{ old('grand_total', isset($invoice) ? number_format($invoice->grand_total, 2, '.', '') : '0.00') }}</span>
                                        <input type="hidden" name="grand_total" id="grand_total" value="{{ old('grand_total', isset($invoice) ? number_format($invoice->grand_total, 2, '.', '') : '0.00') }}">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                <div class="card mb-4 mt-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h5>Transport & E-Way Bill Details</h5>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <select name="transport_mode" id="transport_mode" class="form-select select2" data-placeholder="Select Transport Mode">
                                        <option value="">Select Transport Mode</option>
                                        @foreach($transportModes as $mode)
                                            <option value="{{ $mode->id }}" {{ old('transport_mode', isset($invoice) ? $invoice->transport_mode : '') == $mode->id ? 'selected' : '' }}>{{ $mode->name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="transport_mode">Transport Mode</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="transporter_name" name="transporter_name" placeholder="Transporter Name" value="{{ old('transporter_name', isset($invoice) ? $invoice->transporter_name : '') }}">
                                    <label for="transporter_name">Transporter Name</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="transporter_id" name="transporter_id" placeholder="Transporter GSTIN" value="{{ old('transporter_id', isset($invoice) ? $invoice->transporter_id : '') }}">
                                    <label for="transporter_id">Transporter GSTIN / ID</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="vehicle_no" name="vehicle_no" placeholder="Vehicle No" value="{{ old('vehicle_no', isset($invoice) ? $invoice->vehicle_no : '') }}">
                                    <label for="vehicle_no">Vehicle No</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <select name="veh_type" id="veh_type" class="form-select select2">
                                        <option value="R" {{ old('veh_type', isset($invoice) ? $invoice->veh_type : 'R') == 'R' ? 'selected' : '' }}>Regular</option>
                                        <option value="O" {{ old('veh_type', isset($invoice) ? $invoice->veh_type : '') == 'O' ? 'selected' : '' }}>ODC (Over Dimensional Cargo)</option>
                                    </select>
                                    <label for="veh_type">Vehicle Type</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <style>
                                        /* Hide spinners for number input */
                                        input[type=number].no-spinners::-webkit-inner-spin-button, 
                                        input[type=number].no-spinners::-webkit-outer-spin-button { 
                                            -webkit-appearance: none; 
                                            margin: 0; 
                                        }
                                        input[type=number].no-spinners {
                                            -moz-appearance: textfield;
                                        }
                                    </style>
                                    <input type="number" step="any" class="form-control no-spinners" id="transport_distance" name="transport_distance" placeholder="Distance (in km)" value="{{ old('transport_distance', isset($invoice) ? $invoice->transport_distance : '') }}" onkeydown="if(event.key==='ArrowUp' || event.key==='ArrowDown') event.preventDefault();">
                                    <label for="transport_distance">Distance (in km)</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="tran_doc_no" name="tran_doc_no" placeholder="Transport Doc No / LR No" value="{{ old('tran_doc_no', isset($invoice) ? $invoice->tran_doc_no : (isset($invoice) ? $invoice->lr_no : '')) }}">
                                    <label for="tran_doc_no">Transport Doc No / LR No</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control due_date" id="tran_doc_date" name="tran_doc_date" placeholder="Transport Doc Date" value="{{ old('tran_doc_date', isset($invoice) ? ($invoice->tran_doc_date ? \Carbon\Carbon::parse($invoice->tran_doc_date)->format('d-m-Y') : '') : '') }}">
                                    <label for="tran_doc_date">Transport Doc Date</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ url('sales_invoices') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    function updateColorFromArtNo($row) {
        let artNo = $row.find('input[name$="[art_no]"]').val();
        if (!artNo) {
            artNo = $row.find('.art-no-text').text();
        }
        if (!artNo) {
            artNo = $row.find('.art-no').val();
        }
        artNo = String(artNo || '').trim();
        
        let color = 'A';
        if (artNo) {
            let parts = artNo.split('-');
            if (parts.length > 1) {
                color = parts[parts.length - 1];
            }
        }
        
        let $colorSpan = $row.find('.color-text');
        if ($colorSpan.length) $colorSpan.text(color);
        
        let $colorInput = $row.find('input[name$="[api_color]"], .api-color');
        if ($colorInput.length) {
            $colorInput.val(color);
            $colorInput.prop('readonly', true).attr('tabindex', '-1');
        }
        
        let $colorName = $row.find('input[name$="[color_name]"], .color-name');
        if ($colorName.length) $colorName.val(color);
    }

    $(document).ready(function() {
        $('.item-row').each(function() {
            updateColorFromArtNo($(this));
        });

        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                    $(mutation.addedNodes).each(function() {
                        if (typeof $(this).hasClass === 'function' && $(this).hasClass('item-row')) {
                            updateColorFromArtNo($(this));
                        }
                    });
                }
            });
        });
        
        if (document.getElementById('item-rows')) {
            observer.observe(document.getElementById('item-rows'), { childList: true, subtree: true });
        }
        if (document.getElementById('open-order-item-rows')) {
            observer.observe(document.getElementById('open-order-item-rows'), { childList: true, subtree: true });
        }
        $('.select2').select2({
            width: '100%',
            dropdownParent: $('body')
        });
        var preselectedCustomer = $('#customer_id').val();
        var preselectedSoIds = @json(old('so_ids', isset($invoice) && $invoice->so_ids ? json_decode($invoice->so_ids, true) : []));

        if (preselectedCustomer) {
            if ($('#so_ids option').length === 0) {
                $.ajax({
                    url: "{{ url('sales_invoices/get-customer-sales-orders') }}",
                    type: "GET",
                    data: { customer_id: preselectedCustomer, invoice_id: "{{ isset($invoice) ? $invoice->id : '' }}" },
                    success: function(response) {
                        if (response.success) {
                            var soSelect = $('#so_ids');
                            soSelect.empty();
                            $.each(response.data, function(index, so) {
                                var selected = preselectedSoIds.map(String).includes(so.id.toString());
                                soSelect.append(new Option(
                                    so.so_no + ' (Pending: ' + so.pending_qty + ')',
                                    so.id,
                                    selected,
                                    selected
                                ));
                            });
                            soSelect.trigger('change.select2');
                        }
                    }
                });
            }

            if (preselectedSoIds.length > 0) {
                $.ajax({
                    url: "{{ url('sales_invoices/get-multiple-sale-orders-details') }}",
                    type: "POST",
                    data: { so_ids: preselectedSoIds, _token: "{{ csrf_token() }}" },
                    success: function(data) {
                        if (data.success) {
                            window.availableSOItems = data.items;
                        }
                    }
                });
            }
        }
        if ($('input[name="other_state"]:checked').val() == 'yes') {
            $('#igst_section').show();
            $('#cgst_sgst_section').hide();
        } else {
            $('#igst_section').hide();
            $('#cgst_sgst_section').show();
        }

        if ({{ isset($invoice) || old('items') ? 'true' : 'false' }}) {
            calculateTotals();
        }

        
        $('#customer_id').on('change', function() {
            var customerId = $(this).val();
            if (customerId) {
                $.ajax({
                    url: "{{ url('sales_invoices/get-customer-sales-orders') }}",
                    type: "GET",
                    data: { customer_id: customerId, invoice_id: "{{ isset($invoice) ? $invoice->id : '' }}" },
                    success: function(response) {
                        if (response.success) {
                            var soSelect = $('#so_ids');
                            var currentValue = soSelect.val() || [];
                            soSelect.empty();
                            $.each(response.data, function(index, so) {
                                var selected = currentValue.includes(so.id.toString());
                                soSelect.append(new Option(so.so_no + ' (Pending: ' + so.pending_qty + ')', so.id, false, selected));
                            });
                            soSelect.trigger('change.select2');
                        }
                    }
                });
            } else {
                $('#so_ids').empty().trigger('change.select2');
            }
        });

        $('#so_ids').on('change', function() {
            var soIds = $(this).val();
            if (soIds && soIds.length > 0) {
                $.ajax({
                    url: "{{ url('sales_invoices/get-multiple-sale-orders-details') }}",
                    type: "POST",
                    data: { so_ids: soIds, _token: "{{ csrf_token() }}" },
                    success: function(data) {
                        if (data.success) {
                            if (!$('#store_id').val()) $('#store_id').val(data.store_id).trigger('change');
                            if (!$('#agent_id').val()) $('#agent_id').val(data.agent_id).trigger('change');
                            if (!$('#commission_percent').val()) $('#commission_percent').val(data.commission_percent);
                            if (!$('#address').val()) $('#address').val(data.shipping_address);
                            if (!$('#transporter_name').val()) $('#transporter_name').val(data.transporter_name);
                            if(data.transport_gst_no && !$('#transporter_id').val()) {
                                $('#transporter_id').val(data.transport_gst_no);
                            }
                            if(data.transport_mode_id) {
                                $('#transport_mode').val(data.transport_mode_id).trigger('change');
                            } else {
                                $('#transport_mode').val('').trigger('change');
                            }
                            if (data.other_state == 'yes') {
                                $('#other_state_yes').prop('checked', true);
                                $('#igst_section').show();
                                $('#cgst_sgst_section').hide();
                            } else {
                                $('#other_state_no').prop('checked', true);
                                $('#igst_section').hide();
                                $('#cgst_sgst_section').show();
                            }

                            if (!$('#discount_percent').val() || $('#discount_percent').val() == '0.00') {
                                $('#discount_percent').val(data.discount_percent || 0);
                            }
                            if (data.sales_discount !== undefined) {
                                $('#sales_discount_val').text(parseFloat(data.sales_discount).toFixed(2) + '%');
                                $('#sales_discount').val(parseFloat(data.sales_discount).toFixed(2));
                            }
                             if (data.pre_gst_charges !== undefined) {
                                $('#pre_gst_charges_val').text(parseFloat(data.pre_gst_charges).toFixed(2));
                                $('#pre_gst_charges').val(parseFloat(data.pre_gst_charges).toFixed(2));
                            }
                            if (data.courier_charge !== undefined) {
                                $('#other_charges_val').text(parseFloat(data.courier_charge).toFixed(2));
                                $('#other_charges').val(parseFloat(data.courier_charge).toFixed(2));
                            }
                            if (data.post_gst_charges !== undefined) {
                                $('#post_gst_charges_val').text(parseFloat(data.post_gst_charges).toFixed(2));
                                $('#post_gst_charges').val(parseFloat(data.post_gst_charges).toFixed(2));
                            }
                            if (data.box_discount_amount !== undefined) {
                                $('#box_discount_amount_val').text('₹' + parseFloat(data.box_discount_amount).toFixed(2));
                                $('#box_discount_amount').val(parseFloat(data.box_discount_amount).toFixed(2));
                            }
                            if (!$('#igst_percent').val() || $('#igst_percent').val() == '18.00') $('#igst_percent').val(data.igst_percent || 18);
                            if (!$('#cgst_percent').val() || $('#cgst_percent').val() == '9.00') $('#cgst_percent').val(data.cgst_percent || 9);
                            if (!$('#sgst_percent').val() || $('#sgst_percent').val() == '9.00') $('#sgst_percent').val(data.sgst_percent || 9);

                            window.availableSOItems = data.items;
                            if (window.isEditMode !== true) {
                            }
                            $('#barcode_scanner').focus();
                            calculateTotals();
                        }
                    }
                });
            } else {
                window.availableSOItems = [];
            }
        });

        window.isEditMode = {{ isset($invoice) ? 'true' : 'false' }};
        window.einvoiceStatus = "{{ isset($invoice) ? $invoice->einvoice_status : '' }}";
        window.isDispatched = "{{ isset($invoice) && $invoice->delivery_status === 'Dispatched' ? 'true' : 'false' }}";
        window.availableSOItems = [];

        function addInvoiceItem(matchedItem, qty = null, maxQty = null) {
            if (window.einvoiceStatus === 'generated' || window.isDispatched === 'true') {
                return;
            }
            if (maxQty === null && matchedItem.qty) {
                maxQty = matchedItem.qty;
            }
            if (qty === null) {
                qty = matchedItem.qty ? matchedItem.qty : 1;
            }
            var existingRow = null;
            $('#item-rows .item-row').each(function() {
                if ($(this).find('.sku').val() == (matchedItem.sku || '') && 
                    $(this).find('.art-no').val() == (matchedItem.art_no || '') && 
                    $(this).find('.size-id').val() == (matchedItem.size_id || '') && 
                    $(this).find('.sleeve-type').val() == (matchedItem.sleeve || matchedItem.sleeve_type || '')) {
                    existingRow = $(this);
                }
            });

            if (existingRow) {
                var qtyInput = existingRow.find('.qty');
                var currentQty = parseFloat(qtyInput.val()) || 0;
                var maxVal = parseFloat(qtyInput.attr('data-max')) || 999999;

                if (currentQty + 1 > maxVal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Limit Exceeded',
                        text: 'Cannot add more. Quantity already matches the pending ordered quantity.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }

                var newQty = currentQty + 1;
                qtyInput.val(newQty).trigger('input');
            } else {
                var index = $('#item-rows .item-row').length;
                if (index === 1 && $('#item-rows .item-row td').attr('colspan')) {
                    $('#item-rows').empty();
                    index = 0;
                }

                var html = `
                    <tr class="item-row">
                    <td class="s-no text-center">${index + 1}</td>
                    <td>
                        <div class="fw-bold text-dark">${matchedItem.brand_name || ''}</div>
                        ${(matchedItem.item_name && matchedItem.item_name !== matchedItem.brand_name) ? `<div class="small text-muted">${matchedItem.item_name} (${matchedItem.sleeve || ''})</div>` : ''}
                        ${matchedItem.sku ? `<div class="small text-primary">Barcode: ${matchedItem.sku}</div>` : ''}
                        <input type="hidden" name="items[${index}][brand_id]" class="brand-id" value="${matchedItem.brand_id}">
                        <input type="hidden" name="items[${index}][brand_name]" class="brand-name" value="${matchedItem.brand_name || ''}">
                        <input type="hidden" name="items[${index}][item_id]" class="item-id" value="${matchedItem.item_id || ''}">
                        <input type="hidden" name="items[${index}][item_name]" class="item-name" value="${matchedItem.item_name || ''}">
                        <input type="hidden" name="items[${index}][sleeve_type]" class="sleeve-type" value="${matchedItem.sleeve || ''}">
                        <input type="hidden" name="items[${index}][sku]" class="sku" value="${matchedItem.sku || ''}">
                        <input type="hidden" name="items[${index}][stock_entry_item_id]" class="stock-entry-item-id" value="${matchedItem.stock_entry_item_id || ''}">
                        <input type="hidden" name="items[${index}][is_extra]" class="is-extra" value="${matchedItem.is_extra || ''}">
                    </td>
                    <td>
                        <span class="color-text">${matchedItem.api_color || matchedItem.color_name || '-'}</span>
                        <input type="hidden" name="items[${index}][color_id]" class="color-id" value="${matchedItem.color_id || ''}">
                        <input type="hidden" name="items[${index}][api_color]" class="api-color" value="${matchedItem.api_color || ''}">
                        <input type="hidden" name="items[${index}][color_name]" class="color-name" value="${matchedItem.color_name || ''}">
                    </td>
                    <td>
                        <span class="art-no-text">${matchedItem.art_no || ''}</span>
                        <input type="hidden" name="items[${index}][art_no]" class="art-no" value="${matchedItem.art_no || ''}">
                    </td>
                    <td>
                        <span class="uom-text">${matchedItem.uom_code}</span>
                        <input type="hidden" name="items[${index}][uom_id]" class="uom-id" value="${matchedItem.uom_id}">
                        <input type="hidden" name="items[${index}][uom_code]" class="uom-code" value="${matchedItem.uom_code}">
                    </td>
                    <td>
                        <span class="size-text">${matchedItem.size_name || matchedItem.size_id || ''}</span>
                        <input type="hidden" name="items[${index}][size]" class="size-id" value="${matchedItem.size_id}">
                        <input type="hidden" name="items[${index}][size_name]" class="size-name" value="${matchedItem.size_name || matchedItem.size_id || ''}">
                    </td>
                    <td>
                        <div class="form-floating form-floating-outline">
                            <input type="number" step="any" class="form-control qty" name="items[${index}][quantity]" value="${qty}" data-max="${matchedItem.is_open_order == 1 ? '' : (maxQty || '')}" data-stock="${matchedItem.stock_qty !== undefined ? matchedItem.stock_qty : ''}" max="${matchedItem.is_open_order == 1 ? '' : (maxQty || '')}">
                            <label>Qty *</label>
                        </div>
                        <div class="qty-error text-danger small" style="display:none;"></div>
                        ${(maxQty && matchedItem.is_open_order != 1) ? `<small class="text-info d-block">Ordered: ${maxQty}</small>` : ''}
                        ${matchedItem.stock_qty !== undefined ? `<small class="${matchedItem.stock_qty < (maxQty || Infinity) ? 'text-danger' : 'text-muted'} d-block" style="font-weight: 500;">Stock: ${matchedItem.stock_qty}</small>` : ''}
                    </td>
                    <td>
                        <div class="form-floating form-floating-outline">
                            <input type="number" step="any" class="form-control mrp" name="items[${index}][mrp]" value="${matchedItem.mrp || 0}">
                            <label>MRP</label>
                        </div>
                    </td>
                    <td>
                        <div class="form-floating form-floating-outline">
                            <input type="number" step="any" class="form-control rate" name="items[${index}][rate]" value="${matchedItem.rate || 0}">
                            <label>Price *</label>
                        </div>
                    </td>
                    <td>
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control amount" name="items[${index}][amount]" value="${(qty * (matchedItem.rate || matchedItem.mrp || 0)).toFixed(2)}" readonly>
                            <label>Amount *</label>
                        </div>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-item"><i class="ri ri-delete-bin-line"></i></button>
                    </td>
                </tr>`;
                $('#item-rows').append(html);
                updateSerialNumbers();
                calculateTotals();
            }
        }

        $('#barcode_scanner').autocomplete({
            source: function (request, response) {
                if (!window.availableSOItems || window.availableSOItems.length === 0) {
                    response([]);
                    return;
                }
                var term = request.term;
                if (term.includes('|')) {
                    term = term.split('|')[0].trim();
                }
                term = term.toLowerCase();
                var matches = window.availableSOItems.filter(function (item) {
                    return (item.sku && String(item.sku).toLowerCase().includes(term)) ||
                        (item.art_no && String(item.art_no).toLowerCase().includes(term)) ||
                        (item.item_code && String(item.item_code).toLowerCase().includes(term)) ||
                        (item.item_name && String(item.item_name).toLowerCase().includes(term)) ||
                        (item.brand_name && String(item.brand_name).toLowerCase().includes(term));
                });

                var formatted = matches.map(function (item) {
                    var label = '';
                    if (item.brand_name && item.item_name && item.brand_name !== item.item_name) {
                        label = item.brand_name + ' - ' + item.item_name;
                    } else {
                        label = item.brand_name || item.item_name || '';
                    }
                    if (item.sleeve) label += ' (' + item.sleeve + ')';
                    if (item.sku) label += ' | SKU: ' + item.sku;
                    if (item.size_name || item.size_id) label += ' | Size: ' + (item.size_name || item.size_id);

                    return {
                        label: label,
                        value: item.sku || item.art_no || '',
                        itemData: item
                    };
                });
                formatted = formatted.slice(0, 20);

                if (request.term && formatted.length === 0) {
                    response([{
                        label: 'Barcode not found',
                        value: '',
                        noResult: true
                    }]);
                    return;
                }

                response(formatted);
            },
            minLength: 1,
            focus: function(event, ui) {
                event.preventDefault();
                return false;
            },
            select: function (event, ui) {
                event.preventDefault();
                if (ui.item && ui.item.noResult) {
                    return false;
                }
                if (ui.item && ui.item.disabled) {
                    return false;
                }

                addInvoiceItem(ui.item.itemData);
                $('#barcode_scanner').val('');
                if ($('#barcode_scanner').data('ui-autocomplete')) {
                    $('#barcode_scanner').autocomplete('close');
                }
                setTimeout(function() { $('#barcode_scanner').val('').focus(); }, 300);
                event.preventDefault();
                return false;
            }
        }).autocomplete("instance")._renderItem = function (ul, item) {
            if (item.noResult) {
                return $("<li>")
                    .append(`<div class="ui-menu-item-wrapper text-danger fw-bold">Barcode not found</div>`)
                    .appendTo(ul);
            }

            var it = item.itemData;
            var skuInfo = it.sku ? ` | SKU: ${it.sku}` : '';
            var sizeInfo = it.size_name ? ` | Size: ${it.size_name}` : (it.size_id ? ` | Size: ${it.size_id}` : '');
            return $("<li>")
                .append(`<div class="ui-menu-item-wrapper">
                    <span class="search-item-title">${item.label}</span>
                    <span class="search-item-balance">SO Qty: ${parseFloat(it.qty).toFixed(2)}</span>
                    <div class="search-item-info">
                        Art No: ${it.art_no || '-'} ${skuInfo} | Price: ₹${parseFloat(it.rate || it.mrp || 0).toFixed(2)}
                    </div>
                </div>`)
                .appendTo(ul);
        };

        $('#barcode_scanner').on('keypress', function(e) {
            if (e.which == 13) {
                e.preventDefault();
                var barcode = $(this).val().trim();
                if (!barcode) return;
                
                if (barcode.includes('|')) {
                    barcode = barcode.split('|')[0].trim();
                }

                if (!window.availableSOItems || window.availableSOItems.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Items',
                        text: 'Please select a Sales Order first, or no items exist in the selected Sales Order.',
                    });
                    $(this).focus();
                    return;
                }

                var matchedItem = window.availableSOItems.find(function(item) {
                    return (item.sku && String(item.sku).toLowerCase() === barcode.toLowerCase()) || 
                            (item.art_no && String(item.art_no).toLowerCase() === barcode.toLowerCase()) ||
                            (item.item_code && String(item.item_code).toLowerCase() === barcode.toLowerCase());
                });

                let $this = $(this);
                if (matchedItem) {
                    addInvoiceItem(matchedItem);
                    $this.val('');
                    if ($this.data('ui-autocomplete')) {
                        $this.autocomplete('close');
                    }
                    setTimeout(function() { $this.val('').focus(); }, 300);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Item Not Found',
                        text: 'Item not found in selected Sales Orders',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(function() { $this.focus(); }, 100);
                }

                if ($(this).data('ui-autocomplete')) {
                    $(this).autocomplete('close');
                }
            }
        });

        function updateSerialNumbers() {
            $('#item-rows .item-row').each(function(index) {
                $(this).find('.s-no').text(index + 1);
                $(this).find('input, select, textarea').each(function() {
                    let name = $(this).attr('name');
                    if (name) {
                        let newName = name.replace(/items\[\d+\]/, 'items[' + index + ']');
                        $(this).attr('name', newName);
                    }
                });
            });
        }

        function updateOpenOrderIndices() {
            $('#open-order-item-rows tbody .item-row').each(function(index) {
                $(this).find('input, select, textarea').each(function() {
                    let name = $(this).attr('name');
                    if (name) {
                        let newName = name.replace(/items\[(open_\d+|\d+)\]/, 'items[open_' + index + ']');
                        $(this).attr('name', newName);
                    }
                });
            });
            $('#openOrderItemIndex').val($('#open-order-item-rows tbody .item-row').length);
        }

        window.updateSerialNumbers = updateSerialNumbers;
        window.updateOpenOrderIndices = updateOpenOrderIndices;
        window.calculateTotals = calculateTotals;

        // Initialize open order indices on page load
        updateOpenOrderIndices();
        calculateTotals();

        $('#item-rows').on('click', '.remove-item', function() {
            if (window.einvoiceStatus === 'generated' || window.isDispatched === 'true') {
                return;
            }
            if ($('#item-rows .item-row').length > 0) {
                $(this).closest('tr').remove();
                updateSerialNumbers();
                if ($('#item-rows .item-row').length === 0) {
                    $('#item-rows').html('<tr class="item-row"><td colspan="10" class="text-center">No items found</td></tr>');
                }
                calculateTotals();
            }
        });

        $('#customer_id').on('change', function() {
            let customerId = $(this).val();
            if (customerId) {
                let salesDiscount = $(this).find(':selected').data('sales-discount') || 0;
                $('#sales_discount').val(parseFloat(salesDiscount).toFixed(2));

                let customerStateId = $(this).find(':selected').data('state-id');
                let companyStateId = "{{ $web_settings->state_id ?? '' }}";

                if (customerStateId && companyStateId) {
                    if (customerStateId == companyStateId) {
                        $('#other_state_no').prop('checked', true).trigger('change');
                        $('#cgst_percent').val("{{ $web_settings->cgst ?? 9 }}");
                        $('#sgst_percent').val("{{ $web_settings->sgst ?? 9 }}");
                        $('#igst_percent').val(0);
                    } else {
                        $('#other_state_yes').prop('checked', true).trigger('change');
                        $('#igst_percent').val("{{ $web_settings->igst ?? 18 }}");
                        $('#cgst_percent').val(0);
                        $('#sgst_percent').val(0);
                    }
                }
                let customerPincode = $(this).find(':selected').data('pincode');
                let companyPincode = "{{ $web_settings->zip_code ?? '' }}";
                
                if (!customerPincode) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Danger',
                        text: 'Customer zipcode is empty.'
                    });
                }
                
                if (customerPincode && companyPincode) {
                    $.ajax({
                        url: "{{ url('sales_invoices/calculate-distance') }}",
                        type: "GET",
                        data: {
                            from_pincode: companyPincode,
                            to_pincode: customerPincode
                        },
                        beforeSend: function() {
                            $('#transport_distance').attr('placeholder', 'Calculating...');
                        },
                        success: function(res) {
                            if (res.success && res.distance) {
                                $('#transport_distance').val(Math.round(res.distance)).prop('readonly',true);
                            } else {
                                $('#transport_distance').prop('readonly',false).attr('placeholder', 'Distance (in km)');
                            }
                        },
                        error: function() {
                            $('#transport_distance').removeAttr('readonly').attr('placeholder', 'Distance (in km)');
                        }
                    });
                } else {
                    $('#transport_distance').removeAttr('readonly').val('');
                }
                calculateTotals();
            }
        });

        function calculateTotals() {
            var subTotal = 0;
            $('.amount, .open-amount').each(function() {
                subTotal += parseFloat($(this).val()) || 0;
            });
            $('#sub_total_val').text(subTotal.toFixed(2));
            $('#sub_total').val(subTotal.toFixed(2));

            var totalQty = 0;
            $('.qty, .open-qty').each(function() {
                totalQty += parseFloat($(this).val()) || 0;
            });
            $('#total_qty_val').text(totalQty.toFixed(2));
            $('#total_qty').val(totalQty.toFixed(2));

            var salesDiscPercent = parseFloat($('#sales_discount').val()) || 0;
            var boxDiscountPerPc = parseFloat($('#box_discount_amount').val()) || 0;

            var salesDiscountValue = (subTotal * salesDiscPercent) / 100;
            var boxDiscountValue = totalQty * boxDiscountPerPc;
            var discount = salesDiscountValue + boxDiscountValue;
            
            $('#sales_discount_amount_val').text('₹' + salesDiscountValue.toFixed(2));
            $('#box_discount_amount_val').text('₹' + boxDiscountValue.toFixed(2));
            $('#discount_val').text('- ₹' + discount.toFixed(2));
            $('#discount').val(discount.toFixed(2));

            // var preGstCharges = parseFloat($('#pre_gst_charges').val()) || 0;
            var total = subTotal - discount; // + preGstCharges;
            $('#total_val').text(total.toFixed(2));
            $('#total').val(total.toFixed(2));

            var commPercent = parseFloat($('#commission_percent').val()) || 0;
            var commissionAmount = (subTotal * commPercent) / 100;
            $('#commission_amount_val').text(commissionAmount.toFixed(2));
            $('#commission_amount').val(commissionAmount.toFixed(2));

            var otherState = $('input[name="other_state"]:checked').val();
            var taxAmount = 0;
            var igst = 0, cgst = 0, sgst = 0;

            if (otherState == 'yes') {
                var igstPercent = parseFloat($('#igst_percent').val()) || 0;
                igst = (total * igstPercent) / 100;
                taxAmount = igst;
                $('#igst_val').text(igst.toFixed(2));
                $('#igst').val(igst.toFixed(2));
                $('#igst_section').show();
                $('#cgst_sgst_section').hide();
            } else {
                var cgstP = parseFloat($('#cgst_percent').val()) || 0;
                var sgstP = parseFloat($('#sgst_percent').val()) || 0;
                cgst = (total * cgstP) / 100;
                sgst = (total * sgstP) / 100;
                taxAmount = cgst + sgst;
                $('#cgst_val').text(cgst.toFixed(2));
                $('#cgst').val(cgst.toFixed(2));
                $('#sgst_val').text(sgst.toFixed(2));
                $('#sgst').val(sgst.toFixed(2));
                $('#igst_section').hide();
                $('#cgst_sgst_section').show();
            }

            $('#tax_amount_val').text(taxAmount.toFixed(2));
            $('#tax_amount').val(taxAmount.toFixed(2));

            var otherCharges = parseFloat($('#other_charges').val()) || 0;
            // var postGstCharges = parseFloat($('#post_gst_charges').val()) || 0;

           var totalBeforeRoundOff = total + taxAmount + otherCharges; // + postGstCharges;
            $('#total_before_round_off').val(totalBeforeRoundOff.toFixed(2));
            
            var nearestWhole = Math.round(totalBeforeRoundOff);
            var roundOffAmount = Math.abs(nearestWhole - totalBeforeRoundOff);
            var roundOffType = (nearestWhole >= totalBeforeRoundOff) ? 'Add' : 'Less';
            
            roundOffAmount = parseFloat(roundOffAmount.toFixed(2));
            
            $('#round_off_type').val(roundOffAmount > 0 ? roundOffType : 'Add');
            $('#round_off').val(roundOffAmount.toFixed(2));
            
            var displayStr = (roundOffAmount > 0) ? (roundOffType === 'Add' ? '+' : '-') + roundOffAmount.toFixed(2) : '0.00';
            $('#round_off_display').val(displayStr);

            var grandTotal = nearestWhole;

            $('#grand_total_val').text(grandTotal.toFixed(2));
            $('#grand_total').val(grandTotal.toFixed(2));
        }

        window.calculateTotals = calculateTotals;

        $(document).on('input', '#sales_discount, #box_discount_amount, #discount_percent, #commission_percent, #igst_percent, #cgst_percent, #sgst_percent, #other_charges, #pre_gst_charges, #post_gst_charges, #received_amount', function() {
            calculateTotals();
        });

        $(document).on('change', 'input[name="other_state"]', function() {
            calculateTotals();
        });

        $('#payment_mode').on('change', function() {
            var mode = $(this).val();
            if (mode == 'Bank (Cheque)') {
                $('#extra_field').show();
                $('#extra_label').text('Cheque Number');
                $('#extra_input').attr('placeholder', 'Enter Cheque Number');
            } else if (mode == 'Online (UPI)') {
                $('#extra_field').show();
                $('#extra_label').text('UPI ID');
                $('#extra_input').attr('placeholder', 'Enter UPI ID');
            } else {
                $('#extra_field').hide();
                $('#extra_input').val('');
            }
        });

        if ($('#payment_mode').val()) {
            $('#payment_mode').trigger('change');
        }

        $('input[name="other_state"]').on('change', function() {
            if ($(this).val() == 'yes') {
                $('#igst_section').show();
                $('#cgst_sgst_section').hide();
                igstPercent = 18; 
                cgstPercent = 0;
                sgstPercent = 0;
            } else {
                $('#igst_section').hide();
                $('#cgst_sgst_section').show();
                igstPercent = 0;
                cgstPercent = 9;
                sgstPercent = 9;
            }
            calculateTotals();
        });

        $('#item-rows').on('input', '.qty, .mrp, .rate', function() {
            var row = $(this).closest('.item-row');
            var qtyInput = row.find('.qty');
            var qty = parseFloat(qtyInput.val()) || 0;
            var maxAttr = qtyInput.attr('data-max');
            var max = (maxAttr !== undefined && maxAttr !== '') ? parseFloat(maxAttr) : NaN;
            var stockAttr = qtyInput.attr('data-stock');
            var stock = (stockAttr !== undefined && stockAttr !== '') ? parseFloat(stockAttr) : NaN;
            var originalQty = parseFloat(qtyInput.attr('data-original-qty')) || 0; //new line
            var maxAllowedStock = !isNaN(stock) ? Math.max(stock, originalQty) : NaN; //new line
            var errorDiv = row.find('.qty-error');
            
            var isExistingItem = row.find('.invoice-item-db-id').length > 0 && row.find('.invoice-item-db-id').val() !== ''; //new line

            if (!isNaN(max) && qty > max) {
                errorDiv.text('Exceeds ordered qty (' + max + ')').show();
                qtyInput.addClass('is-invalid');
            // } else if (!isNaN(stock) && qty > stock && (!window.isEditMode || !isExistingItem)) { // old line
            } else if (!isNaN(maxAllowedStock) && qty > maxAllowedStock) { // new line
                errorDiv.text('Exceeds stock!').show();
                qtyInput.addClass('is-invalid');
            } else {
                errorDiv.hide();
                qtyInput.removeClass('is-invalid');
            }

            var mrp = parseFloat(row.find('.mrp').val()) || 0;
            var rate = parseFloat(row.find('.rate').val()) || 0;
            var price = rate > 0 ? rate : mrp;
            row.find('.amount').val((qty * price).toFixed(2));
            calculateTotals();
        });

        $('.common-form').on('submit', function(e) {
            let hasError = false;

            // Validate regular items
            $('#item-rows .item-row').each(function() {
                var row = $(this);
                var qtyInput = row.find('.qty');
                if (qtyInput.length === 0) return;
                var qty = parseFloat(qtyInput.val()) || 0;
                var maxAttr = qtyInput.attr('data-max');
                var max = (maxAttr !== undefined && maxAttr !== '') ? parseFloat(maxAttr) : NaN;
                var stockAttr = qtyInput.attr('data-stock');
                var stock = (stockAttr !== undefined && stockAttr !== '') ? parseFloat(stockAttr) : NaN;
                var originalQty = parseFloat(qtyInput.attr('data-original-qty')) || 0;
                var maxAllowedStock = !isNaN(stock) ? Math.max(stock, originalQty) : NaN;
                var errorDiv = row.find('.qty-error');
                
                var isExistingItem = row.find('.invoice-item-db-id').length > 0 && row.find('.invoice-item-db-id').val() !== '';

                if (!isNaN(max) && qty > max) {
                    errorDiv.text('Exceeds ordered qty (' + max + ')').show();
                    qtyInput.addClass('is-invalid');
                    hasError = true;
                // } else if (!isNaN(stock) && qty > stock && (!window.isEditMode || !isExistingItem)) { // old line
                } else if (!isNaN(maxAllowedStock) && qty > maxAllowedStock) { // new line
                    errorDiv.text('Exceeds stock!').show();
                    qtyInput.addClass('is-invalid');
                    hasError = true;
                } else {
                    errorDiv.hide();
                    qtyInput.removeClass('is-invalid');
                }
            });

            // Validate open order items
            $('#open-order-item-rows .item-row').each(function() {
                var row = $(this);
                var qtyInput = row.find('.open-qty');
                if (qtyInput.length === 0) return;
                var qty = parseFloat(qtyInput.val()) || 0;
                var originalQty = parseFloat(qtyInput.attr('data-original-qty')) || 0;
                var available = parseFloat(row.find('.available-stock-display').text()) || 0;
                var maxAllowedStock = Math.max(available, originalQty);

                // if (qty > available) { // old line
                if (qty > maxAllowedStock) { // new line
                    qtyInput.addClass('is-invalid');
                    row.find('.stock-error-msg').show();
                    hasError = true;
                } else {
                    qtyInput.removeClass('is-invalid');
                    row.find('.stock-error-msg').hide();
                }
            });

            if (hasError) {
                e.preventDefault();
                return false;
            }
            if (!$(this).data('ready-to-submit')) {
                e.preventDefault();
                if ($('#invoice-preloader').length === 0) {
                    $('body').append(`
                        <div id="invoice-preloader" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(255,255,255,0.7);z-index:9999;display:flex;flex-direction:column;justify-content:center;align-items:center;">
                            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <h4 class="mt-3 text-primary">Calculating & Saving...</h4>
                        </div>
                    `);
                } else {
                    $('#invoice-preloader').show();
                }
                if (typeof calculateTotals === 'function') {
                    calculateTotals();
                }
                var form = this;
                setTimeout(function() {
                    $(form).data('ready-to-submit', true);
                    form.submit();
                }, 500);
            }
        });

        $('#einvoice-generate').on('click', function() {
            var invoiceId = "{{ isset($invoice) ? $invoice->id : '' }}";
            if (!invoiceId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Wait!',
                    text: 'Please save the invoice first to generate an E-Invoice.'
                });
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to generate an E-Invoice for this Sales Invoice?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, generate it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    var btn = $(this);
                    btn.prop('disabled', true).text('Generating...');
                    
                    Swal.fire({
                        title: 'Generating...',
                        text: 'Please wait while we generate the E-Invoice.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    $.ajax({
                        url: "{{ url('sales_invoices/generate-einvoice') }}/" + invoiceId,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                                btn.prop('disabled', false).html('<i class="ri ri-receipt-line"></i> Generate E-Invoice');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
                            btn.prop('disabled', false).html('<i class="ri ri-receipt-line"></i> Generate E-Invoice');
                        }
                    });
                }
            });
        });
    });

    let html5QrCode = null;
    let isCameraOpen = false;

    $('#btn_camera_scan').click(function() {
        if (isCameraOpen && html5QrCode) {
            html5QrCode.stop().then((ignore) => {
                $('#reader').hide();
                isCameraOpen = false;
                $(this).html('<i class="ri ri-camera-line me-1"></i> CAMERA');
                $('#barcode_scanner').focus();
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
                    let cleanedText = decodedText;
                    if (cleanedText && cleanedText.includes('|')) {
                        cleanedText = cleanedText.split('|')[0].trim();
                    }
                    $('#barcode_scanner').val(cleanedText);
                    
                    html5QrCode.stop().then((ignore) => {
                        $('#reader').hide();
                        isCameraOpen = false;
                        $('#btn_camera_scan').html('<i class="ri ri-camera-line me-1"></i> CAMERA');
                        
                        var e = $.Event("keypress");
                        e.which = 13;
                        $('#barcode_scanner').trigger(e);
                    }).catch((err) => {
                        console.error("Failed to stop camera after scan:", err);
                    });
                },
                (errorMessage) => {
                }
            ).then(() => {
                isCameraOpen = true;
                $(this).html('<i class="ri ri-close-line me-1"></i> CLOSE CAMERA');
            }).catch((err) => {
                console.error("Error starting camera", err);
                $('#scan_alert').removeClass('alert-success').addClass('alert-danger').show();
                $('#scan_msg').html('<strong>Error:</strong> Could not start camera. Please ensure camera permissions are granted.');
                $('#reader').hide();
            });
        }

        $(function() {
            $('#btn_add_extra_item').click(function() {
                if (window.einvoiceStatus === 'generated' || window.isDispatched === 'true') {
                    Swal.fire('Error', 'Cannot modify items. Invoice is either dispatched or e-invoice generated.', 'error');
                    return;
                }
                $('#extra_item_select').val(null).trigger('change');
                $('#extra_item_qty').val('');
                $('#extra_item_available_qty_text').text('');
                $('#extraItemsModal').modal('show');
            });

            $('#extra_item_select').select2({
                dropdownParent: $('#extraItemsModal'),
                placeholder: 'Search by Art No, SKU, Color...',
                allowClear: true,
                ajax: {
                    url: "{{ url('sales_invoices/finished-goods-stock') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                }
            }).on('select2:select', function (e) {
                var data = e.params.data;
                $('#extra_item_qty').attr('max', data.available_qty);
                $('#extra_item_available_qty_text').text('Available Stock: ' + data.available_qty);
            });

            $('#btn_confirm_extra_item').click(function() {
                var data = $('#extra_item_select').select2('data')[0];
                if (!data) {
                    Swal.fire('Error', 'Please select a finished good.', 'error');
                    return;
                }
                var qty = parseFloat($('#extra_item_qty').val());
                if (!qty || qty <= 0) {
                    Swal.fire('Error', 'Please enter a valid quantity.', 'error');
                    return;
                }
                if (qty > data.available_qty) {
                    Swal.fire('Error', 'Quantity exceeds available stock (' + data.available_qty + ').', 'error');
                    return;
                }

                var matchedItem = {
                    brand_name: 'Extra Finished Good',
                    item_name: data.art_no,
                    sleeve: data.sleeve_type,
                    sku: data.sku,
                    stock_entry_item_id: data.id,
                    api_color: data.color_name,
                    color_name: data.color_name,
                    color_id: data.color_id,
                    art_no: data.art_no,
                    uom_code: 'PCS',
                    uom_id: 1, 
                    size_id: data.size,
                    size_name: data.size,
                    stock_qty: data.available_qty,
                    mrp: data.mrp,
                    rate: data.price,
                    is_extra: 1
                };

                addInvoiceItem(matchedItem, qty, null);
                $('#extraItemsModal').modal('hide');
            });
        });
    });
        // Open Order Logic
        $('#btn_toggle_open_order').on('click', function() {
            $('#open_order_section').toggle();
        });

        $('#open_order_barcode_scanner').autocomplete({
            source: function(request, response) {
                let codeToSearch = request.term;
                if (codeToSearch) {
                    codeToSearch = codeToSearch.split('|')[0].trim();
                }
                $.getJSON("{{ url('sales_orders/search-stock-items') }}", {
                    term: codeToSearch
                }, function(data) {
                    const results = Array.isArray(data) ? data : [];
                    if (request.term && results.length === 0) {
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
            focus: function(event, ui) {
                event.preventDefault();
                return false;
            },
            select: function(event, ui) {
                let $this = $(this);
                if (ui.item && ui.item.noResult) {
                    event.preventDefault();
                    return false;
                }
                if (ui.item && ui.item.balance <= 0) {
                    Swal.fire({ icon: 'warning', title: 'Out of Stock', text: 'This item is out of stock and cannot be selected.', timer: 2000, showConfirmButton: false });
                    event.preventDefault();
                    return false;
                }
                
                if (ui.item) {
                    // Clear the input synchronously to prevent text from lingering
                    $('#open_order_barcode_scanner').val('');
                    if ($('#open_order_barcode_scanner').data('ui-autocomplete')) {
                        $('#open_order_barcode_scanner').autocomplete('close');
                    }

                    let sleeveParam = ui.item.sleeve_type ? `&sleeve_type=${encodeURIComponent(ui.item.sleeve_type)}` : '';
                    let sizeParam = ui.item.size ? `&size=${encodeURIComponent(ui.item.size)}` : '';
                    let itemCodeParam = ui.item.id ? `&item_code=${encodeURIComponent(ui.item.id)}` : '';
                    let artNoParam = ui.item.art_no ? `&art_no=${encodeURIComponent(ui.item.art_no)}` : '';
                    $.ajax({
                        url: `{{ url('get-finished-item-stock') }}?code=${encodeURIComponent(ui.item.value)}${sleeveParam}${sizeParam}${itemCodeParam}${artNoParam}`,
                        type: 'GET',
                        success: function(res) {
                            if (res.success) {
                                handleOpenOrderScan(res);
                                $('#open_order_barcode_scanner').val('');
                                setTimeout(function() { $('#open_order_barcode_scanner').val('').focus(); }, 300);
                            } else {
                                Swal.fire({ icon: 'error', title: 'Item Not Found', text: 'Item not found or out of stock.', timer: 2000 });
                                setTimeout(function() { $('#open_order_barcode_scanner').focus(); }, 150);
                            }
                        },
                        error: function() {
                            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to fetch item details.', timer: 2000 });
                            setTimeout(function() { $('#open_order_barcode_scanner').focus(); }, 150);
                        }
                    });
                }
                event.preventDefault();
                return false;
            }
        }).autocomplete("instance")._renderItem = function(ul, item) {
            if (item.noResult) {
                return $("<li>")
                    .append(`<div class="ui-menu-item-wrapper text-danger fw-bold">Barcode not found</div>`)
                    .appendTo(ul);
            }

            // Calculate stock status
            let balance = parseFloat(item.balance) || 0;
            let outOfStock = balance <= 0;

            let skuInfo = item.sku ? ` | SKU: ${item.sku}` : '';
            let stockHtml = outOfStock 
                ? `<span class="search-item-balance text-danger fw-bold">Stock: ${balance.toFixed(2)}<br>OUT OF STOCK</span>`
                : `<span class="search-item-balance">Stock: ${balance.toFixed(2)}</span>`;

            let $li = $("<li>").append(`<div class="ui-menu-item-wrapper">
                    <span class="search-item-title">${item.label}</span>
                    ${stockHtml}
                    <div class="search-item-info">
                        Art No: ${item.art_no || '-'} ${skuInfo} | Price: ₹${parseFloat(item.price).toFixed(2)}
                    </div>
                </div>`).appendTo(ul);
                
            if (outOfStock) {
                $li.addClass('ui-state-disabled');
                $li.css('opacity', '0.6');
                item.disabled = true;
            }
            return $li;
        };

        $('#open_order_barcode_scanner').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                let val = $(this).val();
                if (val) {
                    // Clear the input synchronously to prevent text from lingering
                    $('#open_order_barcode_scanner').val('');
                    if ($('#open_order_barcode_scanner').data('ui-autocomplete')) {
                        $('#open_order_barcode_scanner').autocomplete('close');
                    }

                    let codeToSearch = val.split('|')[0].trim();
                    $.ajax({
                        url: `{{ url('get-finished-item-stock') }}?code=${encodeURIComponent(codeToSearch)}`,
                        type: 'GET',
                        success: function(res) {
                            if (res.success) {
                                handleOpenOrderScan(res);
                                $('#open_order_barcode_scanner').val('');
                                if ($('#open_order_barcode_scanner').data('ui-autocomplete')) {
                                    $('#open_order_barcode_scanner').autocomplete('close');
                                }
                                setTimeout(function() { $('#open_order_barcode_scanner').val('').focus(); }, 300);
                            } else {
                                Swal.fire({ icon: 'error', title: 'Item Not Found', text: 'Item not found or out of stock.', timer: 2000 });
                                $('#open_order_barcode_scanner').focus();
                            }
                        },
                        error: function() {
                            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to fetch item details.', timer: 2000 });
                            $('#open_order_barcode_scanner').focus();
                        }
                    });
                }
            }
        });

        function handleOpenOrderScan(res) {
            let $existing = $('#open-order-item-rows .item-row').filter(function() {
                return $(this).find('.sku-input').val() == (res.sku || '') &&
                    $(this).find('.size-input').val() == (res.size || '') &&
                    $(this).find('.color-id-input').val() == (res.color_id || '') &&
                    $(this).find('input[name*="[sleeve_type]"]').val() == (res.sleeve_type || '') &&
                    $(this).find('.art-no-input').val() == (res.art_no || '') &&
                    $(this).find('.stock-entry-item-id').val() == (res.stock_entry_item_id || '');
            }).first();

            if ($existing.length) {
                let $qty = $existing.find('.qty-input');
                let newQty = parseFloat($qty.val()) + 1;
                $qty.val(newQty).trigger('input');
                return;
            }

            let index = parseInt($('#openOrderItemIndex').val()) || 0;
            /* let displayName = res.finished_item_code || res.sku || '';
            if (res.item_name) displayName += ' - ' + res.item_name; */
            let displayName = res.brand_name ? res.brand_name + ' ' : (res.finished_item_code || res.sku || '');

            let mrp = parseFloat(res.mrp || 0).toFixed(2);
            let price = parseFloat(res.price || 0).toFixed(2);
            let qty = 1;
            let amount = (qty * price).toFixed(2);
            
            let size = res.size === null ? "" : res.size;
            let sizeStock = res.size_stock || {};
            let balance = sizeStock[size] !== undefined ? sizeStock[size] : (res.balance || 0);

            let rowHtml = `
            <tr class="item-row">
                <td>
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control" value="${displayName}" readonly tabindex="-1">
                        <label>Stock Item*</label>
                    </div>
                    <input type="hidden" name="items[open_${index}][stock_entry_item_id]" class="stock-entry-item-id" value="${res.stock_entry_item_id || ''}">
                    <input type="hidden" name="items[open_${index}][item_id]" value="${res.item_id || ''}">
                    <input type="hidden" name="items[open_${index}][brand_cat_id]" value="${res.brand_cat_id || ''}">
                    <input type="hidden" name="items[open_${index}][is_extra]" value="1">
                </td>
                <td>
                    <div class="form-floating form-floating-outline">
                        <input type="text" name="items[open_${index}][sku]" class="form-control sku-input" value="${res.sku || ''}" readonly tabindex="-1">
                    </div>
                </td>
                <td>
                    <div class="form-floating form-floating-outline">
                        <input type="text" name="items[open_${index}][api_color]" class="form-control" value="${res.color_name || ''}">
                        <input type="hidden" name="items[open_${index}][color_id]" class="color-id-input" value="${res.color_id || ''}">
                        <input type="hidden" name="items[open_${index}][color_name]" value="${res.color_name || ''}">
                    </div>
                </td>
                <td>
                    <div class="form-floating form-floating-outline">
                        <input type="text" name="items[open_${index}][sleeve_type]" class="form-control" value="${res.sleeve_type || ''}" readonly tabindex="-1">
                    </div>
                </td>
                <td>
                    <div class="form-floating form-floating-outline">
                        <input type="text" name="items[open_${index}][art_no]" class="form-control art-no-input" value="${res.art_no || ''}" readonly tabindex="-1">
                    </div>
                </td>
                <td>
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control" value="PCS" readonly tabindex="-1">
                        <input type="hidden" name="items[open_${index}][uom_id]" value="PCS">
                        <input type="hidden" name="items[open_${index}][uom_code]" value="PCS">
                        <label>UOM</label>
                    </div>
                </td>
                <td>
                    <div class="form-floating form-floating-outline">
                        <input type="text" name="items[open_${index}][size_name]" class="form-control size-input" value="${res.size || ''}" readonly tabindex="-1">
                        <input type="hidden" name="items[open_${index}][size]" value="${res.size || ''}">
                    </div>
                </td>
                <td>
                    <div class="form-floating form-floating-outline">
                        <input type="number" name="items[open_${index}][quantity]" class="form-control qty-input open-qty" value="${qty}" data-original-qty="${qty}" min="0.01" step="0.01">
                    </div>
                    <div class="stock-info-wrapper mt-1">
                        <small class="stock-label text-muted">Stock: <span class="available-stock-display">${Math.max(0, parseFloat(balance)).toFixed(2)}</span></small>
                        <div class="text-danger small stock-error-msg" style="display: none; font-weight: 500;">Exceeds stock!</div>
                    </div>
                </td>
                <td>
                    <div class="form-floating form-floating-outline">
                        <input type="number" name="items[open_${index}][mrp]" class="form-control mrp-input" value="${mrp}" readonly tabindex="-1">
                    </div>
                </td>
                <td>
                    <div class="form-floating form-floating-outline">
                        <input type="number" name="items[open_${index}][rate]" class="form-control rate-input open-rate" value="${price}" step="0.01">
                    </div>
                </td>
                <td>
                    <div class="form-floating form-floating-outline">
                        <input type="text" name="items[open_${index}][amount]" class="form-control amount-input amount open-amount" value="${amount}" readonly tabindex="-1">
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm open_delete_row"><i class="ri ri-delete-bin-line"></i></button>
                </td>
            </tr>`;

            $('#open-order-item-rows tbody').append(rowHtml);
            if (typeof window.updateOpenOrderIndices === 'function') {
                window.updateOpenOrderIndices();
            } else if (typeof updateOpenOrderIndices === 'function') {
                updateOpenOrderIndices();
            }
            if (typeof window.calculateTotals === 'function') {
                window.calculateTotals();
            } else if (typeof calculateTotals === 'function') {
                calculateTotals();
            }
        }

        $(document).on('input', '.open-qty, .open-rate', function() {
            let $row = $(this).closest('.item-row');
            let qtyInput = $row.find('.open-qty');
            let qty = parseFloat(qtyInput.val()) || 0;
            let originalQty = parseFloat(qtyInput.attr('data-original-qty')) || 0;
            let rate = parseFloat($row.find('.open-rate').val()) || 0;
            let available = parseFloat($row.find('.available-stock-display').text()) || 0;
            let maxAllowedStock = Math.max(available, originalQty);

            // if (qty > available) { // old line
            if (qty > maxAllowedStock) { // new line
                qtyInput.addClass('is-invalid');
                $row.find('.stock-error-msg').show();
            } else {
                qtyInput.removeClass('is-invalid');
                $row.find('.stock-error-msg').hide();
            }

            $row.find('.open-amount').val((qty * rate).toFixed(2));
            if (typeof window.calculateTotals === 'function') window.calculateTotals();
        });

        $(document).on('click', '.open_delete_row', function() {
            $(this).closest('.item-row').remove();
            if (typeof window.updateOpenOrderIndices === 'function') {
                window.updateOpenOrderIndices();
            } else if (typeof updateOpenOrderIndices === 'function') {
                updateOpenOrderIndices();
            }
            if (typeof window.calculateTotals === 'function') {
                window.calculateTotals();
            } else if (typeof calculateTotals === 'function') {
                calculateTotals();
            }
        });

        let openOrderHtml5QrCode = null;
        let isOpenOrderCameraOpen = false;

        $('#btn_open_order_camera_scan').click(function() {
            if (isOpenOrderCameraOpen && openOrderHtml5QrCode) {
                openOrderHtml5QrCode.stop().then((ignore) => {
                    $('#open_order_reader').hide();
                    isOpenOrderCameraOpen = false;
                    $(this).html('<i class="ri ri-camera-line me-1"></i> CAMERA');
                    $('#open_order_barcode_scanner').focus();
                }).catch((err) => {
                    console.error("Failed to stop camera:", err);
                });
            } else {
                $('#open_order_reader').show();
                openOrderHtml5QrCode = new Html5Qrcode("open_order_reader");
                
                openOrderHtml5QrCode.start(
                    { facingMode: "environment" }, 
                    {
                        fps: 10,
                        qrbox: { width: 250, height: 100 },
                        aspectRatio: 1.0
                    },
                    (decodedText, decodedResult) => {
                        let cleanedText = decodedText;
                        if (cleanedText && cleanedText.includes('|')) {
                            cleanedText = cleanedText.split('|')[0].trim();
                        }
                        $('#open_order_barcode_scanner').val(cleanedText);
                        
                        openOrderHtml5QrCode.stop().then((ignore) => {
                            $('#open_order_reader').hide();
                            isOpenOrderCameraOpen = false;
                            $('#btn_open_order_camera_scan').html('<i class="ri ri-camera-line me-1"></i> CAMERA');
                            
                            var e = $.Event("keypress");
                            e.which = 13;
                            $('#open_order_barcode_scanner').trigger(e);
                        }).catch((err) => {
                            console.error("Failed to stop camera after scan:", err);
                        });
                    },
                    (errorMessage) => {}
                ).then(() => {
                    isOpenOrderCameraOpen = true;
                    $(this).html('<i class="ri ri-close-line me-1"></i> CLOSE CAMERA');
                }).catch((err) => {
                    console.error("Error starting camera", err);
                    $('#open_order_scan_alert').removeClass('alert-success').addClass('alert-danger').show();
                    $('#open_order_scan_msg').html('<strong>Error:</strong> Could not start camera. Please ensure camera permissions are granted.');
                    $('#open_order_reader').hide();
                });
            }
        });

        let originalBrandId = "{{ isset($invoice) ? $invoice->brand_id : '' }}";
        let originalInvNo = "{{ isset($invoice) ? $invoice->inv_no : '' }}";
        let originalInvDate = "{{ isset($invoice) ? $invoice->inv_date->format('d-m-Y') : '' }}";

        function getFinancialYearStart(dateStr) {
            if (!dateStr) return null;
            let parts = dateStr.split('-');
            let year, month;
            if (parts[0].length === 4) {
                year = parseInt(parts[0]);
                month = parseInt(parts[1]);
            } else {
                year = parseInt(parts[2]);
                month = parseInt(parts[1]);
            }
            return (month >= 4) ? year : (year - 1);
        }

        $('#inv_no').on('input', function() {
            $('#is_manual_inv_no').val('1');
        });

        function updateInvoiceNo() {
            let brandId = $('#brand_id').val();
            let invDate = $('input[name="inv_date"]').val();

            if (!brandId || !invDate) {
                $('#inv_no').val('');
                return;
            }

            if (originalInvNo && brandId == originalBrandId && getFinancialYearStart(invDate) === getFinancialYearStart(originalInvDate)) {
                $('#inv_no').val(originalInvNo);
                return;
            }

            $.ajax({
                url: '{{ url("sales_invoices/get_next_invoice_no") }}',
                method: 'GET',
                data: {
                    brand_id: brandId,
                    inv_date: invDate
                },
                success: function(response) {
                    if (response.success) {
                        $('#inv_no').val(response.inv_no);
                        $('#is_manual_inv_no').val('0');
                    } else {
                        console.error(response.message);
                    }
                },
                error: function(xhr) {
                    console.error('AJAX error fetching invoice number');
                }
            });
        }

        $('#brand_id').on('change', function() {
            updateInvoiceNo();
        });

        $('input[name="inv_date"]').on('change input', function() {
            updateInvoiceNo();
        });

        setTimeout(function() {
            let fp = document.querySelector('input[name="inv_date"]');
            if (fp && fp._flatpickr) {
                fp._flatpickr.config.onChange.push(function(selectedDates, dateStr, instance) {
                    updateInvoiceNo();
                });
            }
        }, 1000);

        if ($('#brand_id').val()) {
            updateInvoiceNo();
        }

        // Lock fields once e-invoice is generated
        $(function() {
            if (window.einvoiceStatus === 'generated') {
                $('form.common-form input, form.common-form select, form.common-form textarea').each(function() {
                    var $el = $(this);
                    var nameAttr = $el.attr('name');
                    
                    // Keep PDF display checkboxes editable
                    if (nameAttr === 'show_fields[]' || nameAttr === 'delivery_show_fields[]') {
                        return;
                    }
                    
                    // Keep token, method, submit buttons enabled
                    if ($el.attr('type') === 'hidden' && (nameAttr === '_token' || nameAttr === '_method')) {
                        return;
                    }
                    if ($el.attr('type') === 'submit' || $el.is('button') || $el.hasClass('btn')) {
                        return;
                    }
                    
                    // Disable select elements and checkboxes/radios/files/dates
                    if ($el.is('select')) {
                        $el.prop('disabled', true);
                        if ($el.hasClass('select2-hidden-accessible')) {
                            $el.select2({
                                width: '100%',
                                dropdownParent: $('body')
                            });
                        }
                    } else if ($el.attr('type') === 'checkbox' || $el.attr('type') === 'radio' || $el.attr('type') === 'file' || $el.hasClass('flatpickr-input') || $el.hasClass('date-picker') || nameAttr === 'inv_date' || nameAttr === 'due_date' || nameAttr === 'tran_doc_date') {
                        $el.prop('disabled', true);
                        if ($el.hasClass('flatpickr-input') || $el.hasClass('date-picker') || nameAttr === 'inv_date' || nameAttr === 'due_date' || nameAttr === 'tran_doc_date') {
                            $el.css('pointer-events', 'none');
                        }
                    } else {
                        $el.prop('readonly', true);
                    }
                });

                // Destroy flatpickr instances so they don't open on click
                $('input').each(function() {
                    if (this._flatpickr) {
                        this._flatpickr.destroy();
                    }
                });

                // Hide item deletion and addition buttons
                $('#btn_add_extra_item, #open_camera, #open_order_camera, .remove-item').hide();

                // Re-enable disabled elements right before form submit so Laravel receives all values and validation passes
                $('form.common-form').on('submit', function() {
                    $(this).find('select, input[type="checkbox"], input[type="radio"], input[type="file"], .flatpickr-input, .date-picker, input[name="inv_date"], input[name="due_date"], input[name="tran_doc_date"]').prop('disabled', false);
                });
            }
        });
</script>
@endsection
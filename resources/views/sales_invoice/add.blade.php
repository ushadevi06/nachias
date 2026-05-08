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
                                            <input type="text" class="form-control @error('inv_no') is-invalid @enderror" id="inv_no" placeholder="Enter Invoice No" name="inv_no" value="{{ old('inv_no', isset($invoice) ? $invoice->inv_no : $nextInvNumber) }}">
                                            <label for="inv_no">Invoice No. * </label>
                                            @error('inv_no')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control form-control inv_date @error('inv_date') is-invalid @enderror" name="inv_date" placeholder="Enter Invoice Date" value="{{ old('inv_date', isset($invoice) ? $invoice->inv_date->format('d-m-Y') : date('d-m-Y')) }}" />
                                            <label for="inv_date">Invoice Date * </label>
                                            @error('inv_date')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="form-floating form-floating-outline">
                                            <select id="so_id" name="so_id" class="select2 form-select @error('so_id') is-invalid @enderror" data-placeholder="Select Sales Order">
                                                <option value="">Select Sales Order</option>
                                                @foreach($saleOrders as $so)
                                                    <option value="{{ $so->id }}" {{ (old('so_id', isset($invoice) ? $invoice->so_id : '') == $so->id) ? 'selected' : '' }}>{{ $so->so_no }}</option>
                                                @endforeach
                                            </select>
                                            <label for="so_id">Sales Order *</label>
                                            @error('so_id')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="form-floating form-floating-outline">
                                            <select id="customer_id" name="customer_id" class="select2 form-select @error('customer_id') is-invalid @enderror" data-placeholder="Select Customer/Buyer">
                                                <option value="">Select Customer/Buyer</option>
                                                @foreach($customers as $customer)
                                                    <option value="{{ $customer->id }}" data-state-id="{{ $customer->state_id }}" {{ (old('customer_id', isset($invoice) ? $invoice->customer_id : '') == $customer->id) ? 'selected' : '' }}>{{ $customer->name }} ({{ $customer->code }})</option>
                                                @endforeach
                                            </select>
                                            <label for="customer_id">Customer / Buyer *</label>
                                            @error('customer_id')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="form-floating form-floating-outline">
                                            <textarea class="form-control @error('delivery_address') is-invalid @enderror" id="address" name="delivery_address" placeholder="Enter Delivery Address">{{ old('delivery_address', isset($invoice) ? $invoice->delivery_address : '') }}</textarea>
                                            <label for="address">Delivery Address *</label>
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
                                            <select id="agent_id" name="agent_id" class="select2 form-select" data-placeholder="Select Sales Agent/Executive">
                                                <option value="">Select Sales Agent/Executive</option>
                                                @foreach($sales_agent as $agent)
                                                    <option value="{{ $agent->id }}" {{ (old('agent_id', isset($invoice) ? $invoice->agent_id : '') == $agent->id) ? 'selected' : '' }}>{{ $agent->name }}({{ $agent->code }})</option>
                                                @endforeach
                                            </select>
                                            <label for="agent_id">Sales Agent/Executive</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="number" step="0.01" class="form-control" id="commission_percent" name="commission_percent" placeholder="Commission %" value="{{ old('commission_percent', isset($invoice) ? $invoice->commission_percent : '') }}">
                                            <label for="commission_percent">Commission %</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="transporter_name" name="transporter_name" placeholder="Transporter Name" value="{{ old('transporter_name', isset($invoice) ? $invoice->transporter_name : '') }}">
                                            <label for="transporter_name">Transporter Name</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="lr_no" name="lr_no" placeholder="LR No" value="{{ old('lr_no', isset($invoice) ? $invoice->lr_no : '') }}">
                                            <label for="lr_no">LR No</label>
                                        </div>
                                    </div>
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
                                </div>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="card-header-box">
                                    <h5>Item Details *</h5>
                                    @error('items')
                                        <div class="text-danger small mb-2">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" id="barcode_scanner" class="form-control border-primary" placeholder="Scan Barcode" autocomplete="off" style="border-width: 2px;" autofocus>
                                            <label for="barcode_scanner" class="text-primary fw-bold">SCAN BARCODE</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-center">
                                        <small class="text-muted"><i class="ri-information-line me-1"></i> Tip: Scan a barcode or type item code to quickly add it to the order.</small>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">S.No.</th>
                                                <th style="width: 15%;">Stock Item</th>
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
            $maxQty = null;
            if (isset($invoice->so_id)) {
                $soItem = \App\Models\SalesOrderItem::where('sale_order_id', $invoice->so_id)
                    ->where('stock_entry_item_id', $item->stock_entry_item_id)
                    ->first();
                if ($soItem) {
                    $maxQty = $soItem->qty;
                }
            }

            return [
                'brand_id' => $item->brand_id,
                'brand_name' => $brandName ?: '',
                'item_id' => $item->item_id,
                'item_name' => $itemName ?: '',
                'sleeve_type' => $sleeveType ?: '',
                'color_id' => $item->color_id,
                'color_name' => $item->color ? $item->color->color_name : '',
                'size' => $item->size,
                'size_name' => $item->sizeRatio ? $item->sizeRatio->size : $item->size,
                'art_no' => $item->art_no,
                'hsn_sac' => $item->hsn_sac,
                'uom_id' => $item->uom_id,
                'uom_code' => $item->uom_id ?: '',
                'quantity' => $item->quantity,
                'max_qty' => $maxQty,
                'rate' => $item->rate,
                'mrp' => $item->mrp,
                'amount' => $item->amount,
                'sku' => $item->sku,
                'stock_entry_item_id' => $item->stock_entry_item_id,
                'id' => $item->id,
            ];
        })->toArray();
    }
                                            @endphp
                                            @if($items)
                                                @foreach($items as $index => $row)
                                                @php $row = (object) $row; @endphp
                                                <tr class="item-row">
                                                    <td class="s-no text-center">{{ $index + 1 }}</td>
                                                    <td>
                                                        <div class="fw-bold text-dark">{{ $row->brand_name ?? '' }}</div>
                                                        <div class="small text-muted">{{ $row->item_name ?? '' }} ({{ $row->sleeve_type ?? '' }})</div>
                                                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $row->id ?? '' }}">
                                                        <input type="hidden" name="items[{{ $index }}][brand_id]" class="brand-id" value="{{ $row->brand_id }}">
                                                        <input type="hidden" name="items[{{ $index }}][brand_name]" class="brand-name" value="{{ $row->brand_name ?? '' }}">
                                                        <input type="hidden" name="items[{{ $index }}][item_id]" class="item-id" value="{{ $row->item_id }}">
                                                        <input type="hidden" name="items[{{ $index }}][item_name]" class="item-name" value="{{ $row->item_name ?? '' }}">
                                                        <input type="hidden" name="items[{{ $index }}][sleeve_type]" class="sleeve-type" value="{{ $row->sleeve_type ?? '' }}">
                                                        <input type="hidden" name="items[{{ $index }}][sku]" class="sku" value="{{ $row->sku ?? '' }}">
                                                        <input type="hidden" name="items[{{ $index }}][stock_entry_item_id]" class="stock-entry-item-id" value="{{ $row->stock_entry_item_id ?? '' }}">
                                                    </td>
                                                    <td>
                                                        <span class="color-text">{{ $row->color_name ?? '-' }}</span>
                                                        <input type="hidden" name="items[{{ $index }}][color_id]" class="color-id" value="{{ $row->color_id }}">
                                                        <input type="hidden" name="items[{{ $index }}][color_name]" class="color-name" value="{{ $row->color_name ?? '' }}">
                                                    </td>
                                                    <td>
                                                        <span class="art-no-text">{{ $row->art_no ?? '' }}</span>
                                                        <input type="hidden" name="items[{{ $index }}][art_no]" class="art-no" value="{{ $row->art_no ?? '' }}">
                                                    </td>
                                                    <td>
                                                        <span class="uom-text">{{ $row->uom_code ?? '' }}</span>
                                                        <input type="hidden" name="items[{{ $index }}][uom_id]" class="uom-id" value="{{ $row->uom_id }}">
                                                        <input type="hidden" name="items[{{ $index }}][uom_code]" class="uom-code" value="{{ $row->uom_code ?? '' }}">
                                                    </td>
                                                    <td>
                                                        <span class="size-text">{{ $row->size_name ?? '' }}</span>
                                                        <input type="hidden" name="items[{{ $index }}][size]" class="size-id" value="{{ $row->size }}">
                                                        <input type="hidden" name="items[{{ $index }}][size_name]" class="size-name" value="{{ $row->size_name ?? '' }}">
                                                    </td>
                                                    <td>
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="number" step="any" class="form-control qty" name="items[{{ $index }}][quantity]" value="{{ $row->quantity ?? '' }}" data-max="{{ $row->max_qty ?? '' }}" max="{{ $row->max_qty ?? '' }}" placeholder="Qty">
                                                            <label>Qty *</label>
                                                        </div>
                                                        <div class="qty-error text-danger small" style="display:none;"></div>
                                                        @if(isset($row->max_qty))
                                                            <small class="text-info ordered-qty-label">Ordered: {{ $row->max_qty }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="number" step="any" class="form-control mrp" name="items[{{ $index }}][mrp]" value="{{ $row->mrp ?? '' }}" placeholder="MRP" readonly>
                                                            <label>MRP</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="number" step="any" class="form-control rate" name="items[{{ $index }}][rate]" value="{{ $row->rate ?? '' }}" placeholder="Price" readonly>
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
                                                        <button type="button" class="btn btn-sm btn-danger remove-item"><i class="ri ri-delete-bin-line"></i></button>
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
                                                    <label for="invoice_status">Invoice Status *</label>
                                                    @error('invoice_status')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-floating form-floating-outline">
                                                    <select name="payment_mode" id="payment_mode" class="form-select select2 @error('payment_mode') is-invalid @enderror">
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

                                            <div class="col-md-12">
                                                <div class="form-floating form-floating-outline">
                                                    <textarea class="form-control" id="notes" name="notes" placeholder="Additional Notes">{{ old('notes', isset($invoice) ? $invoice->notes : '') }}</textarea>
                                                    <label for="notes">Additional Notes</label>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="file" class="form-control" id="signature_file" name="signature_file">
                                                    <label for="signature_file">Authorized Signature / Stamp Upload</label>
                                                    @if(isset($invoice) && $invoice->signature_file)
                                                        @php
        $sigExt = pathinfo($invoice->signature_file, PATHINFO_EXTENSION);
        $isSigImage = in_array(strtolower($sigExt), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        $sigUrl = asset($invoice->signature_file);
                                                        @endphp
                                                        <div class="mt-2 p-1 border rounded d-inline-flex align-items-center bg-light shadow-sm">
                                                            @if($isSigImage)
                                                                <img src="{{ $sigUrl }}" class="rounded cursor-pointer view-image" data-image="{{ $sigUrl }}" width="45" height="45" style="object-fit: cover;" alt="Signature">
                                                            @else
                                                                <a href="{{ $sigUrl }}" target="_blank" class="text-decoration-none d-flex align-items-center px-2">
                                                                    @if(strtolower($sigExt) == 'pdf')
                                                                        <i class="ri-file-pdf-fill text-danger fs-3"></i>
                                                                    @else
                                                                        <i class="ri-file-text-fill text-primary fs-3"></i>
                                                                    @endif
                                                                    <span class="ms-1 small text-dark fw-bold text-uppercase" style="font-size: 10px;">{{ $sigExt }}</span>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @endif
                                                    <small class="text-muted d-block mt-1">Max file size: 2MB. Supported formats: JPG, PNG, JPEG, WEBP, PDF, DOC, DOCX</small>
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
                                                                        <i class="ri-file-pdf-fill text-danger fs-3"></i>
                                                                    @else
                                                                        <i class="ri-file-text-fill text-primary fs-3"></i>
                                                                    @endif
                                                                    <span class="ms-1 small text-dark fw-bold text-uppercase" style="font-size: 10px;">{{ $attExt }}</span>
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
    $selected_fields = old('show_fields', isset($invoice->show_fields) ? $invoice->show_fields : ['amount', 'discount', 'tax', 'subtotal', 'grandtotal']);
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
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="text-secondary fw-medium">Sub total:</span>
                                                <span class="fw-bold h5 mb-0" id="sub_total_val">{{ old('sub_total', isset($invoice) ? number_format($invoice->sub_total, 2, '.', '') : '0.00') }}</span>
                                                <input type="hidden" name="sub_total" id="sub_total" value="{{ old('sub_total', isset($invoice) ? number_format($invoice->sub_total, 2, '.', '') : '0.00') }}">
                                            </div>
                                            <div class="row g-2 align-items-center mb-3 pb-3 border-bottom">
                                                <div class="col-4">
                                                    <span class="text-secondary fw-medium">Discount:</span>
                                                </div>
                                                <div class="col-8 d-flex align-items-center">
                                                    <div class="input-group input-group-sm ms-auto" style="width: 140px;">
                                                        <input type="number" step="any" name="discount_percent" id="discount_percent" class="form-control text-end" value="{{ old('discount_percent', isset($invoice) ? number_format($invoice->discount_percent, 2, '.', '') : '0.00') }}">
                                                        <span class="input-group-text bg-white">%</span>
                                                    </div>
                                                    <span class="fw-bold ms-3" style="text-align: right;" id="discount_val">{{ old('discount', isset($invoice) ? number_format($invoice->discount, 2, '.', '') : '0.00') }}</span>
                                                    <input type="hidden" name="discount" id="discount" value="{{ old('discount', isset($invoice) ? number_format($invoice->discount, 2, '.', '') : '0.00') }}">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <span class="text-secondary fw-medium">Total:</span>
                                                <span class="fw-bold h5 mb-0" id="total_val">{{ old('total', isset($invoice) ? number_format($invoice->total, 2, '.', '') : '0.00') }}</span>
                                                <input type="hidden" name="total" id="total" value="{{ old('total', isset($invoice) ? number_format($invoice->total, 2, '.', '') : '0.00') }}">
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="text-secondary fw-medium">Commission Amount:</span>
                                                <span class="fw-bold" id="commission_amount_val">{{ old('commission_amount', isset($invoice) ? number_format($invoice->commission_amount, 2, '.', '') : '0.00') }}</span>
                                                <input type="hidden" name="commission_amount" id="commission_amount" value="{{ old('commission_amount', isset($invoice) ? number_format($invoice->commission_amount, 2, '.', '') : '0.00') }}">
                                            </div>
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
                                                            <input type="number" step="any" name="igst_percent" id="igst_percent" class="form-control text-end" value="{{ old('igst_percent', isset($invoice) ? number_format($invoice->igst_percent, 2, '.', '') : '18.00') }}">
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
                                                            <input type="number" step="any" name="cgst_percent" id="cgst_percent" class="form-control text-end" value="{{ old('cgst_percent', isset($invoice) ? number_format($invoice->cgst_percent, 2, '.', '') : '9.00') }}">
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
                                                            <input type="number" step="any" name="sgst_percent" id="sgst_percent" class="form-control text-end" value="{{ old('sgst_percent', isset($invoice) ? number_format($invoice->sgst_percent, 2, '.', '') : '9.00') }}">
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
                                            {{-- <div class="row g-2 align-items-center mb-3">
                                                <div class="col-4"><span class="text-secondary fw-medium">Other Charges:</span></div>
                                                <div class="col-8">
                                                    <input type="number" step="any" name="other_charges" id="other_charges" class="form-control form-control-sm text-end ms-auto" style="width: 140px;" value="{{ old('other_charges', '0.00') }}">
                                                </div>
                                            </div> --}}
                                            <div class="row g-2 align-items-center mb-3 pb-3 border-bottom">
                                                <div class="col-4"><span class="text-secondary fw-medium">Round Off:</span></div>
                                                <div class="col-8 d-flex align-items-center justify-content-end">
                                                    <div class="d-flex gap-3 me-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="round_off_type" id="round_off_add" value="Add" {{ old('round_off_type', isset($invoice) ? $invoice->round_off_type : 'Add') == 'Add' ? 'checked' : '' }}>
                                                            <label class="form-check-label small fw-medium" for="round_off_add">Add</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="round_off_type" id="round_off_less" value="Less" {{ old('round_off_type', isset($invoice) ? $invoice->round_off_type : 'Add') == 'Less' ? 'checked' : '' }}>
                                                            <label class="form-check-label small fw-medium" for="round_off_less">Less</label>
                                                        </div>
                                                    </div>
                                                    <input type="number" step="any" name="round_off" id="round_off" class="form-control form-control-sm text-end" style="width: 100px;" value="{{ old('round_off', isset($invoice) ? number_format($invoice->round_off, 2, '.', '') : '0.00') }}">
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
        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    width: '100%',
                    dropdownParent: $('body')
                });

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

                $('#so_id').on('change', function() {
                    var soId = $(this).val();
                    if (soId) {
                        $.ajax({
                            url: "{{ url('sales_invoices/get-sale-order-details') }}/" + soId,
                            type: "GET",
                            success: function(data) {
                                if (data.success) {
                                    $('#customer_id').val(data.customer_id).trigger('change');
                                    $('#store_id').val(data.store_id).trigger('change');
                                    $('#agent_id').val(data.agent_id).trigger('change');
                                    $('#commission_percent').val(data.commission_percent);
                                    $('#address').val(data.shipping_address);
                                    if (data.other_state == 'yes') {
                                        $('#other_state_yes').prop('checked', true);
                                        $('#igst_section').show();
                                        $('#cgst_sgst_section').hide();
                                    } else {
                                        $('#other_state_no').prop('checked', true);
                                        $('#igst_section').hide();
                                        $('#cgst_sgst_section').show();
                                    }

                                    $('#discount_percent').val(data.discount_percent || 0);
                                    $('#igst_percent').val(data.igst_percent || 18);
                                    $('#cgst_percent').val(data.cgst_percent || 9);
                                    $('#sgst_percent').val(data.sgst_percent || 9);

                                    window.availableSOItems = data.items;
                                    if (window.isEditMode !== true) {
                                        $('#item-rows').empty();
                                        if (data.items && data.items.length > 0) {
                                            data.items.forEach(function(item) {
                                                addInvoiceItem(item, item.qty, item.qty);
                                            });
                                        }
                                    }
                                    $('#barcode_scanner').focus();
                                    calculateTotals();
                                }
                            }
                        });
                    }
                });

                window.isEditMode = {{ isset($invoice) ? 'true' : 'false' }};
                window.availableSOItems = [];

                function addInvoiceItem(matchedItem, qty = 1, maxQty = null) {
                    if (maxQty === null && matchedItem.qty) {
                        maxQty = matchedItem.qty;
                    }
                    var existingRow = null;
                    $('#item-rows .item-row').each(function() {
                        var rowArtNo = $(this).find('.art-no').val();
                        if (rowArtNo == matchedItem.art_no && $(this).find('.size-id').val() == matchedItem.size_id && $(this).find('.color-id').val() == matchedItem.color_id) {
                            existingRow = $(this);
                        }
                    });

                    if (existingRow) {
                        var qtyInput = existingRow.find('.qty');
                        var currentQty = parseFloat(qtyInput.val()) || 0;
                        var maxVal = parseFloat(qtyInput.attr('data-max')) || 999999;

                        if (currentQty + 1 > maxVal) {
                            var errorMsg = existingRow.find('.qty-error');
                            errorMsg.text('Max allowed: ' + maxVal).show();
                            qtyInput.addClass('is-invalid');
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
                                <div class="small text-muted">${matchedItem.item_name || ''} (${matchedItem.sleeve || ''})</div>
                                <input type="hidden" name="items[${index}][brand_id]" class="brand-id" value="${matchedItem.brand_id}">
                                <input type="hidden" name="items[${index}][brand_name]" class="brand-name" value="${matchedItem.brand_name || ''}">
                                <input type="hidden" name="items[${index}][item_id]" class="item-id" value="${matchedItem.item_id || ''}">
                                <input type="hidden" name="items[${index}][item_name]" class="item-name" value="${matchedItem.item_name || ''}">
                                <input type="hidden" name="items[${index}][sleeve_type]" class="sleeve-type" value="${matchedItem.sleeve || ''}">
                                <input type="hidden" name="items[${index}][sku]" class="sku" value="${matchedItem.sku || ''}">
                                <input type="hidden" name="items[${index}][stock_entry_item_id]" class="stock-entry-item-id" value="${matchedItem.stock_entry_item_id || ''}">
                            </td>
                            <td>
                                <span class="color-text">${matchedItem.color_name || '-'}</span>
                                <input type="hidden" name="items[${index}][color_id]" class="color-id" value="${matchedItem.color_id || ''}">
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
                                    <input type="number" step="any" class="form-control qty" name="items[${index}][quantity]" value="${qty}" data-max="${maxQty || ''}" max="${maxQty || ''}">
                                    <label>Qty *</label>
                                </div>
                                <div class="qty-error text-danger small" style="display:none;"></div>
                                ${maxQty ? `<small class="text-info ordered-qty-label">Ordered: ${maxQty}</small>` : ''}
                            </td>
                            <td>
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="any" class="form-control mrp" name="items[${index}][mrp]" value="${matchedItem.mrp || 0}" readonly>
                                    <label>MRP</label>
                                </div>
                            </td>
                            <td>
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="any" class="form-control rate" name="items[${index}][rate]" value="${matchedItem.rate || 0}" readonly>
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
                        var term = request.term.toLowerCase();
                        var matches = window.availableSOItems.filter(function (item) {
                            return (item.sku && String(item.sku).toLowerCase().includes(term)) ||
                                (item.art_no && String(item.art_no).toLowerCase().includes(term)) ||
                                (item.item_code && String(item.item_code).toLowerCase().includes(term)) ||
                                (item.item_name && String(item.item_name).toLowerCase().includes(term)) ||
                                (item.brand_name && String(item.brand_name).toLowerCase().includes(term));
                        });

                        var formatted = matches.map(function (item) {
                            var label = (item.brand_name || '') + ' - ' + (item.item_name || '');
                            if (item.sleeve) label += ' (' + item.sleeve + ')';
                            if (item.sku) label += ' | SKU: ' + item.sku;

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
                    select: function (event, ui) {
                        if (ui.item && ui.item.noResult) {
                            event.preventDefault();
                            return false;
                        }

                        addInvoiceItem(ui.item.itemData);
                        setTimeout(() => { $(this).val(''); }, 10);
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

                        if (!window.availableSOItems || window.availableSOItems.length === 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'No Items',
                                text: 'Please select a Sales Order first, or no items exist in the selected Sales Order.',
                            });
                            $(this).val('');
                            return;
                        }

                        var matchedItem = window.availableSOItems.find(function(item) {
                            return (item.sku && String(item.sku).toLowerCase() === barcode.toLowerCase()) || 
                                   (item.art_no && String(item.art_no).toLowerCase() === barcode.toLowerCase()) ||
                                   (item.item_code && String(item.item_code).toLowerCase() === barcode.toLowerCase());
                        });

                        if (matchedItem) {
                            addInvoiceItem(matchedItem);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Item Not Found',
                                text: 'Barcode does not match any item in the selected Sales Order.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }

                        $(this).val('');
                        $(this).focus();
                        if ($(this).data('ui-autocomplete')) {
                            $(this).autocomplete('close');
                        }
                    }
                });

                function updateSerialNumbers() {
                    $('#item-rows .item-row').each(function(index) {
                        $(this).find('.s-no').text(index + 1);
                    });
                }

                $('#item-rows').on('click', '.remove-item', function() {
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
                        calculateTotals();
                    }
                });

                function calculateTotals() {
                    var subTotal = 0;
                    $('.amount').each(function() {
                        subTotal += parseFloat($(this).val()) || 0;
                    });
                    $('#sub_total_val').text(subTotal.toFixed(2));
                    $('#sub_total').val(subTotal.toFixed(2));

                    var discPercent = parseFloat($('#discount_percent').val()) || 0;
                    var discount = (subTotal * discPercent) / 100;
                    $('#discount_val').text(discount.toFixed(2));
                    $('#discount').val(discount.toFixed(2));

                    var total = subTotal - discount;
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

                    var totalBeforeRoundOff = total + taxAmount + otherCharges;
                    var nearestWhole = Math.round(totalBeforeRoundOff);
                    var roundOff = nearestWhole - totalBeforeRoundOff;

                    var roundOffType = $('input[name="round_off_type"]:checked').val();
                    var roundOffAmount = parseFloat($('#round_off').val()) || 0;

                    var grandTotal = totalBeforeRoundOff;
                    if (roundOffType == 'Add') {
                        grandTotal += roundOffAmount;
                    } else {
                        grandTotal -= roundOffAmount;
                    }

                    $('#grand_total_val').text(grandTotal.toFixed(2));
                    $('#grand_total').val(grandTotal.toFixed(2));


                }



                $(document).on('input', '#discount_percent, #commission_percent, #igst_percent, #cgst_percent, #sgst_percent, #other_charges, #round_off, #received_amount', function() {
                    calculateTotals();
                });

                $(document).on('change', 'input[name="other_state"], input[name="round_off_type"]', function() {
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
                    var errorDiv = row.find('.qty-error');

                    if (!isNaN(max) && qty > max) {
                        errorDiv.text('Cannot exceed ' + max).show();
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
                    if ($('.qty.is-invalid').length > 0) {
                        e.preventDefault();
                        alert('Please correct the quantities before saving. Some items exceed the ordered quantity.');
                        return false;
                    }
                });
            });
        </script>
@endsection
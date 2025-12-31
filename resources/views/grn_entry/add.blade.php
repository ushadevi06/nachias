@extends('layouts.common')
@section('title', ($grn ? 'Edit' : 'Add') . ' GRN Entry - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $grn ? 'Edit' : 'Add' }} GRN Entry</h4>
                    </div>
                    <form action="{{ url('grn_entries/add' . ($grn ? '/' . $grn->id : '')) }}" method="POST" id="grn-form" class="common-form" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" value="{{ $grn->grn_number ?? $nextGrnNo }}" readonly />
                                    <label>GRN No * </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="grn_date" autocomplete="off" class="form-control grn_date @error('grn_date') is-invalid @enderror" value="{{ old('grn_date', $grn ? $grn->grn_date->format('d-m-Y') : date('d-m-Y')) }}" />
                                    <label>GRN Date * </label>
                                </div>
                                @error('grn_date')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="purchase_invoice_id" id="po_no" class="select2 form-select @error('purchase_invoice_id') is-invalid @enderror" data-placeholder="Select PO Invoice Number">
                                        <option value="">Select PO Invoice Number</option>
                                        @foreach($purchaseInvoices as $inv)
                                            <option value="{{ $inv->id }}" {{ old('purchase_invoice_id', $grn->purchase_invoice_id ?? '') == $inv->id ? 'selected' : '' }}>{{ $inv->invoice_no }}</option>
                                        @endforeach
                                    </select>
                                    <label>PO Invoice Number *</label>
                                </div>
                                @error('purchase_invoice_id')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div id="show_item_det" class="{{ ($grn || old('purchase_invoice_id')) ? '' : 'd-none' }} col-lg-12">
                                <div class="row g-4">
                                    <div class="col-md-6 col-xl-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" id="supplier_display" value="{{ $grn->supplier->name ?? '' }}" readonly>
                                            <label>Supplier</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" name="supplier_invoice_date" id="sup_inv_date" autocomplete="off" class="form-control sup_inv_date @error('supplier_invoice_date') is-invalid @enderror" value="{{ old('supplier_invoice_date', $grn ? $grn->supplier_invoice_date->format('d-m-Y') : '') }}" />
                                            <label>Supplier Invoice Date * </label>
                                        </div>
                                        @error('supplier_invoice_date')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="form-floating form-floating-outline">
                                            <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror" data-placeholder="Select Status">
                                                <option value="">Select Status</option>
                                                <option value="Draft" {{ old('status', $grn->status ?? 'Received') == 'Draft' ? 'selected' : '' }}>Draft</option>
                                                <option value="Received" {{ old('status', $grn->status ?? 'Received') == 'Received' ? 'selected' : '' }}>Received</option>
                                                <option value="Partially Received" {{ old('status', $grn->status ?? 'Received') == 'Partially Received' ? 'selected' : '' }}>Partially Received</option>
                                                <option value="Invoiced" {{ old('status', $grn->status ?? 'Received') == 'Invoiced' ? 'selected' : '' }}>Invoiced</option>
                                                <option value="Cancelled" {{ old('status', $grn->status ?? 'Received') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                            <label>Status *</label>
                                        </div>
                                        @error('status')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-lg-12 mt-5">
                                        <div class="table-responsive grn_table" style="overflow-x: auto; white-space: nowrap;">
                                            <table class="table table-bordered align-middle text-center" id="grn-items-table">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th rowspan="2">Select</th>
                                                        <th rowspan="2">S.No.</th>
                                                        <th rowspan="2">Supplier Design Name(Code)</th>
                                                        <th rowspan="2">Item Image</th>
                                                        <th rowspan="2">Art No. *</th>
                                                        <th rowspan="2">UOM</th>
                                                        <th rowspan="2">Fabric Type</th>
                                                        <th colspan="2">QUANTITY</th>
                                                        <th rowspan="2">RATE & AMOUNT</th>
                                                        <th rowspan="2">STATUS & LOCATION *</th>
                                                    </tr>
                                                    <tr>
                                                        <th>Ordered / Inv / Rec</th>
                                                        <th>Acc / Rej / Bal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php 
                                                        $oldItems = old('items');
                                                        $itemsToLoop = $oldItems ?: ($grn ? $grn->grnEntryItems : []);
                                                        $itCount = 1; 
                                                    @endphp
                                                    @foreach($itemsToLoop as $idx => $item)
                                                        @php 
                                                            $itemObj = is_array($item) ? (object)$item : $item; 
                                                            if (is_array($item)) {
                                                                 $dbItem = \App\Models\PurchaseInvoiceItem::with('uom')->find($item['purchase_invoice_item_id'] ?? 0);
                                                                $designName = ($dbItem && $dbItem->rawMaterial) ? ($dbItem->rawMaterial->name . ' (' . $dbItem->rawMaterial->code . ')') : 'Item ' . ($idx + 1);
                                                                $uomName = ($dbItem && $dbItem->uom) ? $dbItem->uom->uom_code : 'MTR';
                                                                $alreadyReceived = \App\Models\GrnEntryItem::where('purchase_invoice_item_id', $item['purchase_invoice_item_id'] ?? 0)->sum('qty_received');
                                                                if ($grn) {
                                                                    // This is slightly complex for old input, but let's keep it simple for now
                                                                }
                                                                $qtyOrdered = ($dbItem->quantity ?? 0) - $alreadyReceived;
                                                            } else {
                                                                $designName = ($item->purchaseInvoiceItem && $item->purchaseInvoiceItem->rawMaterial) ? ($item->purchaseInvoiceItem->rawMaterial->name . ' (' . $item->purchaseInvoiceItem->rawMaterial->code . ')') : 'Unknown';
                                                                $uomName = ($item->purchaseInvoiceItem && $item->purchaseInvoiceItem->uom) ? $item->purchaseInvoiceItem->uom->uom_code : 'MTR';
                                                                $alreadyReceived = \App\Models\GrnEntryItem::where('purchase_invoice_item_id', $item->purchase_invoice_item_id)->where('grn_entry_id', '!=', $grn->id ?? 0)->sum('qty_received');
                                                                $qtyOrdered = ($item->purchaseInvoiceItem->quantity ?? 0) - $alreadyReceived;
                                                            }
                                                        @endphp
                                                        @php 
                                                            if ($qtyOrdered <= 0) continue;
                                                        @endphp
                                                        <tr class="item-row" data-index="{{ $idx }}">
                                                            <td>
                                                                <input type="checkbox" class="row-select form-check-input" {{ (is_array($item) ? ($item['row_selected'] ?? false) : true) ? 'checked' : '' }}>
                                                                <input type="hidden" name="items[{{$idx}}][row_selected]" value="{{ (is_array($item) ? ($item['row_selected'] ?? 0) : 1) }}" class="row-selected-input">
                                                            </td>
                                                            <td>{{ $itCount++ }}</td>
                                                            <td>
                                                                {{ $designName }}
                                                                <button type="button" class="btn btn-warning btn-sm btn-variants" data-index="{{ $idx }}" data-ordered="{{ $itemObj->qty_ordered }}" {{ ((is_array($item) ? ($item['row_selected'] ?? false) : true) && ($itemObj->qty_received ?? 0) > 0) ? '' : 'disabled' }}>Add Variants</button>
                                                                <div class="variants-data-container">
                                                                    @php $variants = is_array($item) ? ($item['variants'] ?? []) : $item->variants; @endphp
                                                                    @foreach($variants as $vIdx => $v)
                                                                        @php $vObj = is_array($v) ? (object)$v : $v; @endphp
                                                                        <input type="hidden" name="items[{{$idx}}][variants][{{$vIdx}}][color_id]" value="{{ $vObj->color_id }}">
                                                                        <input type="hidden" name="items[{{$idx}}][variants][{{$vIdx}}][qty]" value="{{ $vObj->qty ?? ($vObj->qty_received ?? 0) }}">
                                                                    @endforeach
                                                                </div>
                                                                @error("items.$idx.variants") <div class="text-danger small">{{ $message }}</div> @enderror
                                                            </td>
                                                            <td>
                                                                <input type="file" name="items[{{$idx}}][item_image]" class="form-control" {{ (is_array($item) ? ($item['row_selected'] ?? false) : true) ? '' : 'disabled' }}>
                                                                @if(isset($itemObj->image) && $itemObj->image)
                                                                    <input type="hidden" name="items[{{$idx}}][old_image]" value="{{ $itemObj->image }}">
                                                                    <a href="{{ asset($itemObj->image) }}" target="_blank">
                                                                        <img src="{{ asset($itemObj->image) }}" width="40" class="mt-1 border rounded">
                                                                    </a>
                                                                @endif
                                                                @error("items.$idx.item_image") <div class="text-danger small">{{ $message }}</div> @enderror
                                                            </td>
                                                            <td>
                                                                <input type="hidden" name="items[{{$idx}}][purchase_invoice_item_id]" value="{{ $itemObj->purchase_invoice_item_id }}">
                                                                <input type="text" name="items[{{$idx}}][art_no]" value="{{ $itemObj->art_no }}" class="form-control art-no-input @error("items.$idx.art_no") is-invalid @enderror">
                                                                @error("items.$idx.art_no") <div class="text-danger small">{{ $message }}</div> @enderror
                                                            </td>
                                                            <td>{{ $uomName }}</td>
                                                            <td> 
                                                                <select class="form-control select2 @error("items.$idx.fabric_type_id") is-invalid @enderror" name="items[{{$idx}}][fabric_type_id]" data-placeholder="Select Fabric Type" {{ (is_array($item) ? ($item['row_selected'] ?? false) : true) ? '' : 'disabled' }}>
                                                                    <option value="">Select Fabric Type</option>
                                                                    @foreach($fabricTypes as $ft)
                                                                        <option value="{{ $ft->id }}" {{ ($itemObj->fabric_type_id ?? '') == $ft->id ? 'selected' : '' }}>{{ $ft->fabric_type }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error("items.$idx.fabric_type_id") <div class="text-danger small">{{ $message }}</div> @enderror
                                                            </td>
                                                            <td>
                                                                <div class="mb-2">
                                                                    <label class="small d-block fw-bold">Ordered:</label>
                                                                    <input type="number" name="items[{{$idx}}][qty_ordered]" value="{{ $qtyOrdered }}" class="qty-ordered form-control" readonly>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label class="small d-block fw-bold">Invoiced:</label>
                                                                    <input type="number" value="{{ $alreadyReceived }}" class="form-control" readonly disabled>
                                                                </div>
                                                                <div>
                                                                    <label class="small d-block fw-bold">Received *:</label>
                                                                    <input type="number" name="items[{{$idx}}][qty_received]" value="{{ $itemObj->qty_received }}" class="qty-received form-control @error("items.$idx.qty_received") is-invalid @enderror" {{ ((is_array($item) ? ($item['row_selected'] ?? false) : true) && count($variants) == 0) ? '' : 'readonly' }}>
                                                                    <div class="qty-error text-danger small" style="display:none;">Cannot exceed ordered qty</div>
                                                                    @error("items.$idx.qty_received") <div class="text-danger small">{{ $message }}</div> @enderror
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="mb-2">
                                                                    <label class="small d-block fw-bold">Accepted *:</label>
                                                                    <input type="number" name="items[{{$idx}}][qty_accepted]" value="{{ $itemObj->qty_accepted }}" class="qty-accepted form-control @error("items.$idx.qty_accepted") is-invalid @enderror" {{ (is_array($item) ? ($item['row_selected'] ?? false) : true) ? '' : 'readonly' }}>
                                                                    <div class="qty-acc-error text-danger small" style="display:none;">Cannot exceed received qty</div>
                                                                    @error("items.$idx.qty_accepted") <div class="text-danger small">{{ $message }}</div> @enderror
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label class="small d-block fw-bold">Rejected:</label>
                                                                    <input type="number" name="items[{{$idx}}][qty_rejected]" value="{{ $itemObj->qty_rejected }}" class="qty-rejected form-control" readonly>
                                                                </div>
                                                                <div>
                                                                    <label class="small d-block fw-bold">Balanced:</label>
                                                                    <input type="number" name="items[{{$idx}}][qty_balanced]" value="{{ $itemObj->qty_balanced }}" class="qty-balanced form-control" readonly>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="mb-2">
                                                                    <label class="small d-block fw-bold">Rate:</label>
                                                                    <input type="number" name="items[{{$idx}}][rate]" value="{{ $itemObj->rate }}" class="rate-input form-control @error("items.$idx.rate") is-invalid @enderror" readonly>
                                                                </div>
                                                                <div>
                                                                    <label class="small d-block fw-bold">Amount:</label>
                                                                    <input type="number" name="items[{{$idx}}][amount]" value="{{ $itemObj->amount }}" class="amount-input form-control" readonly>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="mb-3 text-start">
                                                                    <label class="form-label small fw-bold d-block mb-1">QC Status:</label>
                                                                    <select class="form-control select2 @error("items.$idx.quality_check_status") is-invalid @enderror" name="items[{{$idx}}][quality_check_status]" {{ (is_array($item) ? ($item['row_selected'] ?? false) : true) ? '' : 'disabled' }}>
                                                                        <option value="">Select Status</option>
                                                                        <option value="Pass" {{ ($itemObj->quality_check_status ?? '') == 'Pass' ? 'selected' : '' }}>Pass</option>
                                                                        <option value="Fail" {{ ($itemObj->quality_check_status ?? '') == 'Fail' ? 'selected' : '' }}>Fail</option>
                                                                        <option value="Hold" {{ ($itemObj->quality_check_status ?? '') == 'Hold' ? 'selected' : '' }}>Hold</option>
                                                                    </select>
                                                                    @error("items.$idx.quality_check_status") <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                                                </div>
                                                                <div class="text-start">
                                                                    <label class="form-label small fw-bold d-block mb-1">Store Location:</label>
                                                                    <select class="form-control select2 @error("items.$idx.store_location_id") is-invalid @enderror" name="items[{{$idx}}][store_location_id]" {{ (is_array($item) ? ($item['row_selected'] ?? false) : true) ? '' : 'disabled' }}>
                                                                        <option value="">Select Store Location</option>
                                                                        @foreach($storeLocations as $loc)
                                                                            <option value="{{ $loc->id }}" {{ ($itemObj->store_location_id ?? '') == $loc->id ? 'selected' : '' }}>{{ $loc->store_location }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error("items.$idx.store_location_id") <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end col-lg-12">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('grn_entries') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="variantModal" tabindex="-1" aria-labelledby="variantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary d-flex justify-content-between align-items-center">
                <h5 class="modal-title mb-0 text-white" id="variantModalLabel">
                    Add Variants (Specify Quantity per Color)
                </h5>
                <h5 class="mb-0 text-white" id="modal-qty-summary">Ordered: 0.00 | Received: 0.00</h5>
                <button type="button" class="btn-close ms-2 btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <label class="form-label fw-semibold">Select Colors</label>
                    <select id="variantColors" class="form-control select2" multiple="multiple" data-placeholder="Select Colors">
                        @foreach($colors as $col)
                            <option value="{{ $col->id }}" data-name="{{ $col->color_name }}">{{ $col->color_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle" id="variantQtyTable">
                    <thead class="table-light">
                        <tr>
                        <th>Color</th>
                        <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    </table>
                </div>
                <div id="variant-error" class="text-danger mt-2 fw-bold" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save-variants">Save Variants</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let activeRowIndex = null;
        const fabrics_options = `@foreach($fabricTypes as $ft)<option value="{{$ft->id}}">{{$ft->fabric_type}}</option>@endforeach`;
        const locations_options = `@foreach($storeLocations as $loc)<option value="{{$loc->id}}">{{$loc->store_location}}</option>@endforeach`;

        $('.grn_date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });
        $('.sup_inv_date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });

        function initSelect2() {
            $('.select2').each(function() {
                let parent = $(this).closest('.modal-content').length ? $(this).closest('.modal-content') : null;
                $(this).select2({ dropdownParent: parent });
            });
        }
        initSelect2();

        $('#po_no').on('change', function() {
            let po_id = $(this).val();
            if (po_id) {
                $('#show_item_det').removeClass('d-none');
                $('#supplier_display').val('Loading...');
                $('#grn-items-table tbody').empty().append('<tr><td colspan="17" class="text-center">Loading items...</td></tr>');

                $.get("{{ url('grn_entries/get-invoice-details') }}/" + po_id, function(res) {
                    $('#supplier_display').val(res.supplier_name);
                    $('#sup_inv_date').val(res.invoice_date);
                    
                    let tbody = $('#grn-items-table tbody').empty();
                    res.items.forEach((item, idx) => {
                        tbody.append(`
                            <tr class="item-row" data-index="${idx}">
                                <td>
                                    <input type="checkbox" class="row-select form-check-input" checked>
                                    <input type="hidden" name="items[${idx}][row_selected]" value="1" class="row-selected-input">
                                </td>
                                <td>${idx + 1}</td>
                                <td>
                                    ${item.design_name}
                                    <button type="button" class="btn btn-warning btn-sm btn-variants" data-index="${idx}" data-ordered="${item.qty_ordered}" disabled>Add Variants</button>
                                    <div class="variants-data-container"></div>
                                </td>
                                <td>
                                    <input type="file" name="items[${idx}][item_image]" class="form-control">
                                </td>
                                <td>
                                    <input type="hidden" name="items[${idx}][purchase_invoice_item_id]" value="${item.id}">
                                    <input type="text" name="items[${idx}][art_no]" value="${item.art_no}" class="form-control">
                                </td>
                                <td>${item.uom}</td>
                                <td><select class="form-control select2" name="items[${idx}][fabric_type_id]"><option value="">Select Fabric</option>${fabrics_options}</select></td>
                                <td>
                                    <div class="mb-2">
                                        <label class="small d-block fw-bold">Ordered:</label>
                                        <input type="number" name="items[${idx}][qty_ordered]" value="${item.qty_ordered}" class="qty-ordered form-control" readonly>
                                    </div>
                                    <div class="mb-2">
                                        <label class="small d-block fw-bold">Invoiced:</label>
                                        <input type="number" value="${item.qty_already_received}" class="form-control" readonly disabled>
                                    </div>
                                    <div>
                                        <label class="small d-block fw-bold">Received *:</label>
                                        <input type="number" name="items[${idx}][qty_received]" value="0" class="qty-received form-control">
                                        <div class="qty-error text-danger small" style="display:none;">Cannot exceed ordered qty</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="mb-2">
                                        <label class="small d-block fw-bold">Accepted *:</label>
                                        <input type="number" name="items[${idx}][qty_accepted]" value="0" class="qty-accepted form-control">
                                        <div class="qty-acc-error text-danger small" style="display:none;">Cannot exceed received qty</div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="small d-block fw-bold">Rejected:</label>
                                        <input type="number" name="items[${idx}][qty_rejected]" value="0" class="qty-rejected form-control" readonly>
                                    </div>
                                    <div>
                                        <label class="small d-block fw-bold">Balanced:</label>
                                        <input type="number" name="items[${idx}][qty_balanced]" value="${item.qty_ordered}" class="qty-balanced form-control" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="mb-2">
                                        <label class="small d-block fw-bold">Rate:</label>
                                        <input type="number" name="items[${idx}][rate]" value="${item.rate}" class="rate-input form-control" readonly>
                                    </div>
                                    <div>
                                        <label class="small d-block fw-bold">Amount:</label>
                                        <input type="number" name="items[${idx}][amount]" value="0" class="amount-input form-control" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="mb-3 text-start">
                                        <label class="form-label small fw-bold d-block mb-1">QC Status:</label>
                                        <select class="form-control select2" name="items[${idx}][quality_check_status]"><option value="">Select</option><option value="Pass">Pass</option><option value="Fail">Fail</option><option value="Hold">Hold</option></select>
                                    </div>
                                    <div class="text-start">
                                        <label class="form-label small fw-bold d-block mb-1">Store Location:</label>
                                        <select class="form-control select2" name="items[${idx}][store_location_id]"><option value="">Select Location</option>${locations_options}</select>
                                    </div>
                                </td>
                            </tr>
                        `);
                    });
                    initSelect2();
                });
            } else {
                $('#show_item_det').addClass('d-none');
            }
        });

        $(document).on('click', '.btn-variants', function() {
            let row = $(this).closest('.item-row');
            activeRowIndex = row.data('index');
            let ordered = parseFloat($(this).data('ordered')) || 0;
            let currentReceived = parseFloat(row.find('.qty-received').val()) || 0;
            
            $('#modal-qty-summary').data('ordered', ordered);
            updateModalSummary(currentReceived, ordered);
            $('#variant-error').hide();
            
            // Reload existing variants into modal
            let container = $(this).closest('td').find('.variants-data-container');
            let selectedColors = [];
            let tbody = $('#variantQtyTable tbody').empty();
            
            container.find('input[name$="[color_id]"]').each(function() {
                let color_id = $(this).val();
                let qty = $(this).next().val();
                selectedColors.push(color_id);
                // Get name from option directly using ID
                let colorName = $(`#variantColors option[value="${color_id}"]`).data('name') || 'Unknown';
                tbody.append(`<tr data-color-id="${color_id}"><td>${colorName}</td><td><input type="number" class="form-control var-qty" value="${qty}" min="0"></td></tr>`);
            });
            
            $('#variantColors').val(selectedColors).trigger('change');
            $('#variantModal').modal('show');
        });

        $('#variantColors').on('change', function () {
            let data = $(this).select2('data') || [];
            let tbody = $('#variantQtyTable tbody');
            let existingData = {};

            tbody.find('tr').each(function () {
                existingData[$(this).data('color-id')] = $(this).find('input').val();
            });

            tbody.empty();
            data.forEach(function (item) {
                let color_id = item.id;
                let colorName = item.text;
                let existingQty = existingData[color_id] || '';
                tbody.append(`<tr data-color-id="${color_id}"><td>${colorName}</td><td><input type="number" class="form-control var-qty" value="${existingQty}" min="0"></td></tr>`);
            });
        });

        $(document).on('input', '.var-qty', function() {
            validateVariantTotal();
        });

        function validateVariantTotal() {
            let total = 0;
            $('.var-qty').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            let row = $(`.item-row[data-index="${activeRowIndex}"]`);
            let received = parseFloat(row.find('.qty-received').val()) || 0;
            let ordered = parseFloat($('#modal-qty-summary').data('ordered')) || 0;
            
            updateModalSummary(total, received); // Use received as the label in summary
            
            if (total > received) {
                $('#variant-error').text(`Total variant quantity (${total.toFixed(2)}) cannot exceed received quantity (${received.toFixed(2)})`).show();
                $('#save-variants').prop('disabled', true);
            } else {
                $('#variant-error').hide();
                $('#save-variants').prop('disabled', false);
            }
            return total;
        }

        function updateModalSummary(totalVariants = 0, received = null) {
            let ordered = parseFloat($('#modal-qty-summary').data('ordered')) || 0;
            if (received === null && activeRowIndex !== null) {
                received = parseFloat($(`.item-row[data-index="${activeRowIndex}"]`).find('.qty-received').val()) || 0;
            }
            $('#modal-qty-summary').text(`Ordered: ${ordered.toFixed(2)} | Received Total: ${received.toFixed(2)} | Variants Sum: ${totalVariants.toFixed(2)}`);
        }

        $('#save-variants').on('click', function() {
            let row = $(`.item-row[data-index="${activeRowIndex}"]`);
            let receivedLimit = parseFloat(row.find('.qty-received').val()) || 0;
            let totalVariants = validateVariantTotal();
            
            if (totalVariants > receivedLimit) return;

            let container = row.find('.variants-data-container').empty();
            
            $('#variantQtyTable tbody tr').each(function(i) {
                let color_id = $(this).data('color-id');
                let qty = $(this).find('.var-qty').val() || 0;
                if (parseFloat(qty) > 0) {
                    container.append(`<input type="hidden" name="items[${activeRowIndex}][variants][${i}][color_id]" value="${color_id}">`);
                    container.append(`<input type="hidden" name="items[${activeRowIndex}][variants][${i}][qty]" value="${qty}">`);
                }
            });
            
            row.find('.qty-received').prop('readonly', totalVariants > 0);
            updateRowCalculations(row);
            $('#variantModal').modal('hide');
        });

        $(document).on('input', '.qty-received, .qty-accepted, .qty-rejected, .rate-input', function() {
            let row = $(this).closest('.item-row');
            
            // If received is changed, default accepted to the same value
            if ($(this).hasClass('qty-received')) {
                let received = parseFloat($(this).val()) || 0;
                row.find('.qty-accepted').val(received);
            }

            updateRowCalculations(row);
            validateForm();

            // Enable/Disable variant button based on received quantity
            let received = parseFloat(row.find('.qty-received').val()) || 0;
            row.find('.btn-variants').prop('disabled', received <= 0);
        });

        function validateForm() {
            let hasError = false;
            $('.item-row').each(function() {
                let isChecked = $(this).find('.row-select').is(':checked');
                if (!isChecked) return; // Only validate selected rows

                let ordered = parseFloat($(this).find('.qty-ordered').val()) || 0;
                let received = parseFloat($(this).find('.qty-received').val()) || 0;
                
                let accepted = parseFloat($(this).find('.qty-accepted').val()) || 0;
                
                if (received > ordered) {
                    $(this).find('.qty-received').addClass('is-invalid');
                    $(this).find('.qty-error').show();
                    hasError = true;
                } else {
                    $(this).find('.qty-received').removeClass('is-invalid');
                    $(this).find('.qty-error').hide();
                }

                if (accepted > received) {
                    $(this).find('.qty-accepted').addClass('is-invalid');
                    $(this).find('.qty-acc-error').show();
                    hasError = true;
                } else {
                    $(this).find('.qty-accepted').removeClass('is-invalid');
                    $(this).find('.qty-acc-error').hide();
                }
            });
            $('button[type="submit"]').prop('disabled', hasError);
        }

        function updateRowCalculations(row) {
            let ordered = parseFloat(row.find('.qty-ordered').val()) || 0;
            let received = parseFloat(row.find('.qty-received').val()) || 0;
            let accepted = parseFloat(row.find('.qty-accepted').val()) || 0;
            let rejected = received - accepted;
            let rate = parseFloat(row.find('.rate-input').val()) || 0;
            
            row.find('.qty-rejected').val(rejected.toFixed(2));
            row.find('.qty-balanced').val((ordered - received).toFixed(2));
            row.find('.amount-input').val((accepted * rate).toFixed(2));
        }

        // Run calculations and button status for rows already present (from old() or edit mode)
        $('.item-row').each(function() {
            let row = $(this);
            updateRowCalculations(row);
            let received = parseFloat(row.find('.qty-received').val()) || 0;
            row.find('.btn-variants').prop('disabled', received <= 0);
        });

        $(document).on('change', '.row-select', function() {
            let row = $(this).closest('.item-row');
            let isChecked = $(this).is(':checked');
            row.find('.row-selected-input').val(isChecked ? 1 : 0);
            
            row.find('.qty-received').prop('readonly', !isChecked || row.find('.variants-data-container input').length > 0);
            row.find('.qty-accepted').prop('readonly', !isChecked);
            row.find('select').prop('disabled', !isChecked);
            row.find('input[type="file"]').prop('disabled', !isChecked);
            
            let received = parseFloat(row.find('.qty-received').val()) || 0;
            row.find('.btn-variants').prop('disabled', !isChecked || received <= 0);
            
            if (isChecked) {
                row.find('input, select').removeClass('is-invalid');
                row.find('.text-danger').hide();
            } else {
                row.find('.qty-received').val(0).prop('readonly', true);
                row.find('.qty-accepted').val(0).prop('readonly', true);
                row.find('.qty-rejected').val(0);
                row.find('.qty-balanced').val(parseFloat(row.find('.qty-ordered').val()) || 0);
                row.find('.amount-input').val(0);
                row.find('select').val('').trigger('change').prop('disabled', true); 
                row.find('input[type="file"]').val('').prop('disabled', true); 
                row.find('.variants-data-container').empty(); 
                row.find('.btn-variants').prop('disabled', true);
                row.find('input, select').removeClass('is-invalid');
                row.find('.text-danger').hide();
            }
            updateRowCalculations(row);
            validateForm(); // Re-validate form after changes
        });

        // Initialize state for existing rows
        $('.row-select').each(function() {
            let row = $(this).closest('.item-row');
            let isChecked = $(this).is(':checked');
            
            row.find('.qty-received').prop('readonly', !isChecked || row.find('.variants-data-container input').length > 0);
            row.find('.qty-accepted').prop('readonly', !isChecked);
            row.find('select').prop('disabled', !isChecked);
            row.find('input[type="file"]').prop('disabled', !isChecked);
            
            let received = parseFloat(row.find('.qty-received').val()) || 0;
            row.find('.btn-variants').prop('disabled', !isChecked || received <= 0);
        });

        // Auto-scroll to errors in items table if QC or Store Location are invalid
        let targetedErrors = $('.item-row').find('select[name*="quality_check_status"].is-invalid, select[name*="store_location_id"].is-invalid');
        if (targetedErrors.length > 0) {
            setTimeout(function() {
                let tableContainer = $('.table-responsive');
                if (tableContainer.length > 0) {
                    tableContainer.animate({
                        scrollLeft: tableContainer[0].scrollWidth
                    }, 800);
                }
            }, 500);
        }
    });
</script>
@endsection
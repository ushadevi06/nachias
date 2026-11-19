@extends('layouts.common')
@section('title', ($receipt ? 'Edit' : 'Add') . ' Production Receipt - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            @include('flash_messages')
        </div>
        <div class="col-lg-12">
            <form action="{{ url('production_receipts/add' . ($receipt ? '/' . $receipt->id : '')) }}" method="POST" class="common-form">
                @csrf
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>{{ $receipt ? 'Edit' : 'Add' }} Production Receipt</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    @php
                                        $oldRawVal = old('job_card_id_raw', old('job_card_id'));
                                        $oldFabId = old('job_card_fabric_detail_id');
                                        if ($oldRawVal && !str_contains((string)$oldRawVal, '_add_') && $oldFabId) {
                                            $oldRawVal = $oldRawVal . '_add_' . $oldFabId;
                                        }
                                        $initialJcId = $receipt ? $receipt->job_card_id : null;
                                        if ($oldRawVal) {
                                            $initialJcId = explode('_add_', (string)$oldRawVal)[0];
                                        }
                                        $initialJc = $initialJcId ? $jobCards->firstWhere('id', (int)$initialJcId) : null;
                                        $isCanvasReceipt = false;
                                        if ($initialJc && $initialJc->brand && (in_array(strtoupper(trim($initialJc->brand->brand_name ?? '')), ['CANVAS ACCESSORIES', 'CANVAS ACCESSORIES (CAS)']) || stripos($initialJc->brand->brand_name ?? '', 'CANVAS') !== false)) {
                                            $isCanvasReceipt = true;
                                        }
                                    @endphp
                                    <input type="hidden" name="job_card_id_raw" id="job_card_id_raw" value="{{ $oldRawVal }}">
                                    <select name="job_card_id" id="job_card_id" class="form-select select2" data-placeholder="Select Job Card No">
                                        <option value="">Select Job Card No</option>
                                        @foreach($jobCards as $jobCard)
                                            @php
                                                $baseOrdered = max(0, floatval($jobCard->grand_total_qty) - floatval($jobCard->additional_qty ?? 0));
                                                if ($baseOrdered <= 0) {
                                                    $baseFabrics = $jobCard->fabricDetails ? $jobCard->fabricDetails->where('is_additional', 0) : collect();
                                                    if ($baseFabrics->isNotEmpty() && $baseFabrics->first()->quantities) {
                                                        $baseOrdered = $baseFabrics->first()->quantities->sum('total_qty');
                                                    }
                                                }
                                                if ($baseOrdered <= 0) {
                                                    $baseOrdered = ($jobCard->total_qty_fs ?? 0) + ($jobCard->total_qty_hs ?? 0);
                                                }
                                                if ($baseOrdered <= 0) {
                                                    $baseOrdered = floatval($jobCard->grand_total_qty ?? 0);
                                                }

                                                $baseReceived = $receiptsSummary[$jobCard->id]['base'] ?? 0;
                                                $isBaseDone = ($baseOrdered > 0 && $baseReceived >= ($baseOrdered - 0.001));

                                                $baseTasks = $jobCard->tasks ? $jobCard->tasks->filter(function($t) {
                                                    return empty($t->is_additional) && empty($t->job_card_fabric_detail_id);
                                                }) : collect();
                                                $isBaseTasksCompleted = $baseTasks->isNotEmpty() && $baseTasks->every(fn($t) => $t->status === 'Completed');

                                                $isCurrentBaseSelected = ($receipt && $receipt->job_card_id == $jobCard->id && !$receipt->job_card_fabric_detail_id);
                                                $isBaseOptionSelected = ($oldRawVal == (string)$jobCard->id && empty($oldFabId) && !str_contains((string)$oldRawVal, '_add_')) || (empty($oldRawVal) && $isCurrentBaseSelected);

                                                $additionalBatches = $jobCard->fabricDetails
                                                    ? $jobCard->fabricDetails->where('is_additional', 1)->groupBy(function($item) {
                                                        return $item->additional_batch_no ?? ($item->created_at ? $item->created_at->format('Y-m-d H:i') : $item->id);
                                                    })
                                                    : collect();
                                            @endphp

                                            @if((!$isBaseDone && $isBaseTasksCompleted) || $isCurrentBaseSelected || $isBaseOptionSelected)
                                                <option value="{{ $jobCard->id }}" {{ $isBaseOptionSelected ? 'selected' : '' }}>
                                                    {{ $jobCard->job_card_no }}
                                                </option>
                                            @endif

                                            @foreach($additionalBatches as $batchGroup)
                                                @php
                                                    $firstBatchFab = $batchGroup->first();
                                                    $batchTotalQty = ($firstBatchFab && $firstBatchFab->quantities) ? $firstBatchFab->quantities->sum('total_qty') : 0;
                                                    if ($batchTotalQty <= 0) {
                                                        $batchTotalQty = $batchGroup->sum('total_qty');
                                                    }
                                                    $addVal = $jobCard->id . '_add_' . $firstBatchFab->id;
                                                    $addQty = (floatval($batchTotalQty) == intval($batchTotalQty)) ? intval($batchTotalQty) : $batchTotalQty;

                                                    $batchIds = $batchGroup->pluck('id')->toArray();
                                                    $batchReceived = 0;
                                                    foreach ($batchIds as $bId) {
                                                        $batchReceived += ($receiptsSummary[$jobCard->id]['additional'][$bId] ?? 0);
                                                    }
                                                    $isBatchDone = ($batchTotalQty > 0 && $batchReceived >= ($batchTotalQty - 0.001));

                                                    $batchTasks = $jobCard->tasks ? $jobCard->tasks->filter(function($t) use ($batchIds) {
                                                        return in_array($t->job_card_fabric_detail_id, $batchIds) || ($t->is_additional == 1 && in_array($t->job_card_fabric_detail_id, $batchIds));
                                                    }) : collect();
                                                    $isBatchTasksCompleted = $batchTasks->isNotEmpty() && $batchTasks->every(fn($t) => $t->status === 'Completed');

                                                    $isBatchSelected = false;
                                                    if ($receipt && $receipt->job_card_id == $jobCard->id && $receipt->job_card_fabric_detail_id) {
                                                        $isBatchSelected = in_array($receipt->job_card_fabric_detail_id, $batchIds);
                                                    }

                                                    $isOptionSelected = ($oldRawVal == $addVal) ||
                                                        ($oldRawVal == (string)$jobCard->id && $oldFabId && in_array($oldFabId, $batchIds)) ||
                                                        (empty($oldRawVal) && $isBatchSelected);
                                                @endphp

                                                @if((!$isBatchDone && $isBatchTasksCompleted) || $isBatchSelected || $isOptionSelected)
                                                    <option value="{{ $addVal }}" {{ $isOptionSelected ? 'selected' : '' }}>
                                                        {{ $jobCard->job_card_no }} - Additional {{ $addQty }} qty
                                                    </option>
                                                @endif
                                            @endforeach
                                        @endforeach
                                    </select>
                                    <label for="job_card_id">Job Card No <span class="text-danger">*</span></label>
                                </div>
                                @error('job_card_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="receipt_date" id="receipt_date" class="form-control receipt-date" placeholder="Select Receipt Date" value="{{ old('receipt_date', $receipt && $receipt->receipt_date ? date('d-m-Y', strtotime($receipt->receipt_date)) : date('d-m-Y')) }}">
                                    <label for="receipt_date">Receipt Date <span class="text-danger">*</span></label>
                                </div>
                                @error('receipt_date') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="doc_no" id="doc_no" class="form-control" placeholder="Enter Doc No" value="{{ old('doc_no', $receipt->doc_no ?? '') }}" readonly>
                                    <label for="doc_no">Doc No</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="doc_date" id="doc_date" class="form-control doc-date" placeholder="Select Doc Date" value="{{ old('doc_date', $receipt && $receipt->doc_date ? date('d-m-Y', strtotime($receipt->doc_date)) : '') }}">
                                    <label for="doc_date">Doc Date <span class="text-danger">*</span></label>
                                </div>
                                @error('doc_date') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4" id="employee_id_wrapper">
                                <div class="form-floating form-floating-outline">
                                    <select name="employee_id" id="employee_id" class="form-select select2 @error('employee_id') is-invalid @enderror" data-placeholder="Select Responsible Person">
                                        <option value="">Select Responsible Person</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('employee_id', $receipt->employee_id ?? '') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }}{{ $employee->emp_id ? ' [' . $employee->emp_id . ']' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="employee_id">Responsible Person (Employee) <span class="text-danger">*</span></label>
                                </div>
                                @error('employee_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="plant" id="plant" class="form-control" placeholder="Plant" value="{{ old('plant', ($receipt && $receipt->jobCard && $receipt->jobCard->serviceProvider) ? $receipt->jobCard->serviceProvider->name : '') }}" readonly>
                                    <label for="plant">Plant</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="warehouse_id" id="warehouse_id" class="form-select select2" data-placeholder="Select Warehouse">
                                        <option value="">Select Warehouse</option>
                                        @foreach($warehouses as $wh)
                                            <option value="{{ $wh->id }}" {{ old('warehouse_id', $receipt->warehouse_id ?? '') == $wh->id ? 'selected' : '' }}>{{ $wh->warehouse_name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="warehouse_id">Warehouse Name <span class="text-danger" id="warehouse_required_star" style="{{ $isCanvasReceipt ? 'display: none;' : '' }}">*</span></label>
                                </div>
                                <div id="warehouse_capacity_info"></div>
                                @error('warehouse_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="order_due_date" id="order_due_date" class="form-control" placeholder="Order Due Date" value="{{ old('order_due_date', ($receipt && $receipt->order_due_date) ? date('d-m-Y', strtotime($receipt->order_due_date)) : (($receipt && $receipt->jobCard && $receipt->jobCard->delivery_date) ? date('d-m-Y', strtotime($receipt->jobCard->delivery_date)) : '')) }}" readonly>
                                    <label for="order_due_date">Order Due Date</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="store_type_id" id="store_type_id" class="form-select select2" data-placeholder="Select Store">
                                        <option value="">Select Store</option>
                                        @foreach($storeTypes as $storeType)
                                            <option value="{{ $storeType->id }}" {{ old('store_type_id', $receipt->store_type_id ?? '') == $storeType->id ? 'selected' : '' }}>{{ $storeType->store_type_name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="store_type_id">Store <span class="text-danger">*</span></label>
                                </div>
                                @error('store_type_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="form-select select2" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Draft" {{ old('status', $receipt->status ?? '') == 'Draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="Posted" {{ old('status', $receipt->status ?? '') == 'Posted' ? 'selected' : '' }}>Posted</option>
                                    </select>
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                </div>
                                @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-12 col-xl-8">
                                <div class="form-floating form-floating-outline">
                                    <textarea name="remarks" id="remarks" class="form-control" placeholder="Enter Remarks" style="height: 48px;">{{ old('remarks', $receipt->remarks ?? '') }}</textarea>
                                    <label for="remarks">Remarks</label>
                                </div>
                                @error('remarks') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card" id="items-section" style="display: none;">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Items</h4>
                        </div>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th class="col-art-no" style="{{ $isCanvasReceipt ? 'display: none;' : '' }}">Art No</th>
                                        <th class="col-description" style="{{ $isCanvasReceipt ? 'display: none;' : '' }}">Description</th>
                                        <th>Color</th>
                                        <th>Size</th>
                                        <th>Unit Price</th>
                                        <th>Qty Ordered</th>
                                        <th>Qty Received</th>
                                        <th>Qty Balance</th>
                                        <th>Qty To Receive <span class="text-danger">*</span></th>
                                        <th style="min-width: 170px;">Store Location <span class="text-danger">*</span></th>
                                        <th style="width: 70px;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="items-tbody">
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary me-2">Submit</button>
                            <a href="{{ url('production_receipts') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="consumptionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Consumption Breakdown</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="consumption-info" class="mb-3 p-2 bg-light rounded border"></div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light text-uppercase text-xs fw-bold text-muted">
                            <tr>
                                <th class="border-0">Material (Art No)</th>
                                <th class="border-0">UOM</th>
                                <th class="text-end border-0">Rate</th>
                                <th class="text-end border-0">Unit Price</th>
                                <th class="text-end pe-3 border-0">Per Shirt Price</th>
                            </tr>
                        </thead>
                        <tbody id="consumption-tbody" class="border-top-0">
                        </tbody>
                        <tfoot class="border-top bg-light">
                            <tr>
                                <th colspan="4" class="text-end pe-3 py-3 fs-6">Grand Total (Per Shirt) <br>Estimate Cost </th>
                                <th class="text-end pe-3 py-3 fs-6 text-primary" id="consumption-footer-total">₹0.00</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('.select2').select2();
        $('.receipt-date, .doc-date').flatpickr({ dateFormat: 'd-m-Y', allowInput: true });

        var itemDataStore = [];
        var storeLocationsList = @json($storeLocations);
        var itemRowCounter = 0;

        function buildRowHtml(item, index, isCanvas) {
            var scanQty = parseFloat(item.scan_qty || 0);
            var alreadyRec = parseFloat(item.qty_already_received || 0);
            var orderedQty = parseFloat(item.ordered_qty || 0);
            var maxAvailable = parseFloat(item.balance_qty || (orderedQty - alreadyRec) || 0);
            var currentBalance = Math.max(0, maxAvailable - scanQty);
            var selectedLocId = item.store_location_id || '';
            var isSplit = !!item.is_split;
            var groupKey = (item.art_no || item.item_code || '') + '_' + (item.size_variant || item.size || '') + '_' + (item.color_id || '');

            var locSelectHtml = '<select name="items[' + index + '][store_location_id]" class="form-select form-select-sm store-location-select select2" data-placeholder="Select Location">' +
                '<option value="">Select Location</option>';
            storeLocationsList.forEach(function(loc) {
                var sel = (selectedLocId && selectedLocId == loc.id) ? ' selected' : '';
                locSelectHtml += '<option value="' + loc.id + '"' + sel + '>' + loc.store_location + '</option>';
            });
            locSelectHtml += '</select>';

            var showAddBtn = (!isSplit && currentBalance > 0.0001);
            var actionBtnHtml = isSplit
                ? '<button type="button" class="btn btn-sm btn-icon btn-danger btn-remove-split-row" title="Remove split row"><i class="ri ri-delete-bin-line" style="pointer-events: none;"></i></button>'
                : '<button type="button" class="btn btn-sm btn-icon btn-success btn-add-split-row" data-index="' + index + '" title="Split to another store location"' + (!showAddBtn ? ' style="display:none;"' : '') + '><i class="ri ri-add-line" style="pointer-events: none;"></i></button>';

            var itemNameDisplay = (item.item_code || item.item_name || '');
            if (isSplit) {
                itemNameDisplay += (itemNameDisplay ? ' ' : '') + '<span class="badge bg-label-info" style="font-size: 10px;">Split</span>';
            }

            var row = '<tr class="item-row ' + (isSplit ? 'split-row' : '') + '" data-index="' + index + '" data-group-key="' + groupKey + '" data-is-split="' + (isSplit ? '1' : '0') + '">' +
                '<td>' + itemNameDisplay + '<input type="hidden" name="items[' + index + '][item_name]" value="' + (item.item_name || item.item_code || '') + '"></td>' +
                '<td class="col-art-no"' + (isCanvas ? ' style="display:none;"' : '') + '>' + (item.art_no || '-') + '</td>' +
                '<td class="col-description"' + (isCanvas ? ' style="display:none;"' : '') + '>' + 
                    (item.description || '') + 
                    (!isSplit ? '<button type="button" class="btn btn-sm btn-link p-0 ms-1 btn-show-consumption" data-index="' + index + '" title="View Consumption Details"><i class="icon-base ri ri-information-line fs-5"></i></button>' : '') +
                '</td>' +
                '<td>' + (item.color || '-') + '<input type="hidden" name="items[' + index + '][color_id]" value="' + (item.color_id || '') + '"></td>' +
                '<td>' + (item.size || '') + '<input type="hidden" name="items[' + index + '][size]" value="' + (item.size || '') + '"></td>' +
                '<td class="text-end">' + 
                    (!isSplit ? '<button type="button" class="btn btn-sm btn-label-primary p-1 btn-show-consumption" data-index="' + index + '">' + parseFloat(item.unit_price || 0).toFixed(2) + '</button>' : parseFloat(item.unit_price || 0).toFixed(2)) +
                    '<input type="hidden" class="unit-price" name="items[' + index + '][unit_price]" value="' + parseFloat(item.unit_price || 0).toFixed(2) + '">' +
                    '<input type="hidden" class="mrp" name="items[' + index + '][mrp]" value="' + parseFloat(item.mrp || 0).toFixed(2) + '">' +
                '</td>' +
                '<td class="text-end">' + (isSplit ? '-' : orderedQty.toFixed(2)) + '<input type="hidden" class="ordered-qty" name="items[' + index + '][ordered_qty]" value="' + orderedQty.toFixed(2) + '"></td>' +
                '<td class="text-end">' + (isSplit ? '-' : '<span class="qty-received-text">' + (alreadyRec + scanQty).toFixed(2) + '</span>') + '<input type="hidden" class="qty-already-received" name="items[' + index + '][qty_already_received]" value="' + alreadyRec.toFixed(2) + '"></td>' +
                '<td class="text-end">' + (isSplit ? '-' : '<span class="qty-balance-text">' + currentBalance.toFixed(2) + '</span>') + '<input type="hidden" class="balance-qty" name="items[' + index + '][balance_qty]" value="' + currentBalance.toFixed(2) + '"></td>' +
                '<td><input type="number" step="0.01" class="form-control form-control-sm text-end scan-qty" name="items[' + index + '][scan_qty]" value="' + scanQty.toFixed(2) + '" min="0" data-max="' + maxAvailable.toFixed(2) + '"></td>' +
                '<td style="min-width: 170px;">' + locSelectHtml + '</td>' +
                '<td class="text-center align-middle">' + actionBtnHtml + '</td>' +
                '<input type="hidden" name="items[' + index + '][item_id]" value="' + (item.item_id || '') + '">' +
                '<input type="hidden" name="items[' + index + '][item_code]" value="' + (item.item_code || '') + '">' +
                '<input type="hidden" name="items[' + index + '][art_no]" value="' + (item.art_no || '') + '">' +
                '<input type="hidden" name="items[' + index + '][description]" value="' + (item.description || '') + '">' +
                '<input type="hidden" name="items[' + index + '][size_variant]" value="' + (item.size_variant || '') + '">' +
                '<input type="hidden" name="items[' + index + '][uom_id]" value="' + (item.uom_id || '') + '">' +
                '<input type="hidden" name="items[' + index + '][uom_code]" value="' + (item.uom_code || '') + '">' +
                '<input type="hidden" name="items[' + index + '][completed_qty]" value="' + (item.completed_qty || 0) + '">' +
                '<input type="hidden" name="items[' + index + '][is_split]" value="' + (isSplit ? '1' : '0') + '">' +
                '</tr>';

            return row;
        }

        function populateItemsGrid(items, isCanvas) {
            itemDataStore = items || [];
            var tbody = $('#items-tbody');
            tbody.empty();
            
            var colSpan = isCanvas ? 10 : 12;
            if (!items || items.length === 0) {
                tbody.append('<tr><td colspan="' + colSpan + '" class="text-center">No items found</td></tr>');
                $('#items-section').hide();
                return;
            }

            if (isCanvas) {
                $('.col-art-no, .col-description').hide();
            } else {
                $('.col-art-no, .col-description').show();
            }

            itemRowCounter = 0;
            items.forEach(function(item) {
                var rowHtml = buildRowHtml(item, itemRowCounter++, isCanvas);
                tbody.append(rowHtml);
            });

            tbody.find('.select2').select2({
                width: '100%',
                placeholder: 'Select Location'
            });

            var handledGroups = new Set();
            items.forEach(function(item) {
                var groupKey = (item.art_no || item.item_code || '') + '_' + (item.size_variant || item.size || '') + '_' + (item.color_id || '');
                if (!handledGroups.has(groupKey)) {
                    handledGroups.add(groupKey);
                    recalculateGroupValues(groupKey);
                }
            });

            $('#items-section').show();
        }

        $(document).on('click', '.btn-show-consumption', function() {
            var index = $(this).data('index');
            var item = itemDataStore[index];
            if (!item || !item.consumption_details) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No consumption details found for this item.',
                    confirmButtonColor: '#8c57ff',
                });
                return;
            }

            var infoHtml = '<div class="card bg-label-secondary border-0 mb-3">' +
                           '<div class="card-body p-3 d-flex justify-content-between align-items-center">' +
                           '<div><small class="text-uppercase text-muted fw-bold d-block mb-1">Item Details</small>' + 
                           '<span class="fw-bold text-dark fs-6">' + item.item_code + '</span> <span class="badge bg-white text-dark shadow-sm ms-2">' + item.size_variant + '</span>' +
                           '</div>'  +
                           '</div></div>';
            $('#consumption-info').html(infoHtml);

            var tbody = $('#consumption-tbody');
            tbody.empty();
            var grandTotalCost = 0;
            var batchQty = parseFloat(item.scan_qty || 0);

            item.consumption_details.forEach(function(detail) {
                var rate = parseFloat(detail.rate);
                var materialPrice = parseFloat(detail.price || 0);
                var perShirtCost = rate * materialPrice;
                var batchUsage = rate * batchQty;
                
                if (rate <= 0) return;
                grandTotalCost += perShirtCost;

                var formulaHtml = detail.formula ? 
                    '<div class="text-muted small mt-1" style="font-size: 0.75rem;"><i class="ri-calculator-line me-1"></i>' + detail.formula + '</div>' : 
                    '<div class="text-muted small mt-1" style="font-size: 0.75rem;">' + (detail.source || 'Standard') + '</div>';

                tbody.append('<tr>' +
                    '<td>' +
                        '<div class="d-flex align-items-center">' +
                            '<span class="badge bg-label-primary p-2 me-2 rounded"><i class="icon-base ri ri-shirt-line"></i></span>' +
                            '<div>' +
                                '<div class="fw-bold text-dark">' + detail.material_name + '</div>' +
                                (detail.material_name != detail.art_no ? '<small class="text-muted">' + detail.art_no + '</small>' : '') +
                            '</div>' +
                        '</div>' +
                    '</td>' +
                    '<td><span class="badge bg-label-secondary">' + (detail.uom || 'MTR') + '</span></td>' +
                    '<td class="text-end">' +
                        '<div class="fw-bold">' + rate.toFixed(4) + '</div>' + 
                        formulaHtml +
                    '</td>' +
                    '<td class="text-end">₹' + materialPrice.toFixed(2) + '</td>' +
                    '<td class="text-end pe-3 fw-bold text-dark">₹' + perShirtCost.toFixed(2) + '</td>' +
                    '</tr>');
            });

            $('#consumption-footer-total').text('₹' + grandTotalCost.toFixed(2));

            $('#consumptionModal').modal('show');
        });
        
        function recalculateGroupValues(groupKey) {
            var $groupRows = $('#items-tbody tr[data-group-key="' + groupKey + '"]');
            if ($groupRows.length === 0) return;

            var $firstRow = $groupRows.first();
            var maxAvailable = parseFloat($firstRow.find('.scan-qty').data('max')) || 0;
            var orderedQty = parseFloat($firstRow.find('.ordered-qty').val()) || 0;
            var alreadyRec = parseFloat($firstRow.find('.qty-already-received').val()) || 0;

            var totalScan = 0;
            $groupRows.each(function() {
                totalScan += parseFloat($(this).find('.scan-qty').val()) || 0;
            });

            var remainingBalance = Math.max(0, maxAvailable - totalScan);
            $firstRow.find('.qty-balance-text').text(remainingBalance.toFixed(2));
            $firstRow.find('.balance-qty').val(remainingBalance.toFixed(2));
            $firstRow.find('.qty-received-text').text((alreadyRec + totalScan).toFixed(2));

            var $addBtn = $firstRow.find('.btn-add-split-row');
            if (remainingBalance > 0.0001) {
                $addBtn.show();
            } else {
                $addBtn.hide();
            }
        }

        $(document).on('input change', '.scan-qty', function() {
            var $row = $(this).closest('tr');
            var groupKey = $row.data('group-key');
            var maxAvailable = parseFloat($(this).data('max')) || 0;

            var otherRowsTotal = 0;
            $('#items-tbody tr[data-group-key="' + groupKey + '"]').not($row).each(function() {
                otherRowsTotal += parseFloat($(this).find('.scan-qty').val()) || 0;
            });

            var allowedForThisRow = Math.max(0, maxAvailable - otherRowsTotal);
            var enteredVal = parseFloat($(this).val()) || 0;

            if (enteredVal > (allowedForThisRow + 0.0001)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Limit Exceeded',
                    text: 'Qty To Receive cannot exceed remaining available balance (' + allowedForThisRow.toFixed(2) + ')',
                    confirmButtonColor: '#8c57ff',
                });
                enteredVal = allowedForThisRow;
                $(this).val(enteredVal.toFixed(2));
            }

            if (enteredVal < 0) {
                enteredVal = 0;
                $(this).val('0.00');
            }

            recalculateGroupValues(groupKey);
        });

        $(document).on('click', '.btn-add-split-row', function(e) {
            e.preventDefault();
            var $parentRow = $(this).closest('tr');
            var parentIndex = $parentRow.data('index');
            var groupKey = $parentRow.data('group-key');
            var isCanvas = $('.col-art-no').is(':hidden');

            var parentItem = itemDataStore[parentIndex];
            if (!parentItem) {
                parentItem = {
                    item_id: $parentRow.find('input[name*="[item_id]"]').val(),
                    item_code: $parentRow.find('input[name*="[item_code]"]').val(),
                    item_name: $parentRow.find('input[name*="[item_name]"]').val(),
                    art_no: $parentRow.find('input[name*="[art_no]"]').val(),
                    description: $parentRow.find('input[name*="[description]"]').val(),
                    color: $parentRow.find('input[name*="[color_id]"]').closest('td').text().trim(),
                    color_id: $parentRow.find('input[name*="[color_id]"]').val(),
                    size: $parentRow.find('input[name*="[size]"]').val(),
                    size_variant: $parentRow.find('input[name*="[size_variant]"]').val(),
                    unit_price: $parentRow.find('.unit-price').val(),
                    mrp: $parentRow.find('.mrp').val(),
                    ordered_qty: $parentRow.find('.ordered-qty').val(),
                    qty_already_received: $parentRow.find('.qty-already-received').val(),
                    completed_qty: $parentRow.find('input[name*="[completed_qty]"]').val(),
                    balance_qty: $parentRow.find('.scan-qty').data('max'),
                    uom_id: $parentRow.find('input[name*="[uom_id]"]').val(),
                    uom_code: $parentRow.find('input[name*="[uom_code]"]').val(),
                };
            }

            var maxAvailable = parseFloat($parentRow.find('.scan-qty').data('max')) || 0;
            var currentGroupScan = 0;
            $('#items-tbody tr[data-group-key="' + groupKey + '"]').each(function() {
                currentGroupScan += parseFloat($(this).find('.scan-qty').val()) || 0;
            });

            var remainingForSplit = Math.max(0, maxAvailable - currentGroupScan);

            var splitItem = $.extend({}, parentItem);
            splitItem.is_split = true;
            splitItem.scan_qty = remainingForSplit;
            splitItem.store_location_id = '';

            var newIndex = itemRowCounter++;
            var splitRowHtml = buildRowHtml(splitItem, newIndex, isCanvas);

            var $lastInGroup = $('#items-tbody tr[data-group-key="' + groupKey + '"]').last();
            $lastInGroup.after(splitRowHtml);

            var $newRow = $lastInGroup.next();
            $newRow.find('.select2').select2({
                width: '100%',
                placeholder: 'Select Location'
            });

            recalculateGroupValues(groupKey);
        });

        function deleteSplitRow($btn) {
            var $row = $btn.closest('tr');
            if ($row.length === 0) return;
            var groupKey = $row.data('group-key');

            if ($row.find('.select2').data('select2')) {
                try {
                    $row.find('.select2').select2('close');
                    $row.find('.select2').select2('destroy');
                } catch(err) {}
            }
            $row.remove();

            recalculateGroupValues(groupKey);
        }

        $(document).on('mousedown', '.btn-remove-split-row', function(e) {
            e.preventDefault();
            deleteSplitRow($(this));
        });

        $(document).on('click', '.btn-remove-split-row', function(e) {
            e.preventDefault();
            deleteSplitRow($(this));
        });

        $('#job_card_id').on('change', function() {
            var rawVal = $(this).val();
            $('#job_card_id_raw').val(rawVal || '');
            if (!rawVal) {
                $('#plant').val('');
                $('#doc_no').val('');
                $('#doc_date').val('');
                $('#items-section').hide();
                $('#items-tbody').empty();
                $('#warehouse_required_star').show();
                $('#warehouse_capacity_info').html('');
                return;
            }

            var jobCardId = rawVal;
            var fabricDetailId = '';
            var isAdditional = '0';

            if (rawVal.indexOf('_add_') !== -1) {
                var parts = rawVal.split('_add_');
                jobCardId = parts[0];
                fabricDetailId = parts[1];
                isAdditional = '1';
            }

            var excludeReceiptId = {{ $receipt ? $receipt->id : 'null' }};
            var url = '{{ url("production_receipts/get-job-card-details") }}/' + jobCardId;
            var params = [];
            if (excludeReceiptId) {
                params.push('exclude_receipt_id=' + excludeReceiptId);
            }
            if (fabricDetailId) {
                params.push('job_card_fabric_detail_id=' + fabricDetailId);
            } else {
                params.push('is_additional=' + isAdditional);
            }
            if (params.length > 0) {
                url += '?' + params.join('&');
            }

            var oldItems = @json(old('items', []));
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    if (response.success && response.data) {
                        $('#plant').val(response.data.plant_name || '');
                        $('#order_due_date').val(response.data.order_due_date || '');
                        $('#doc_no').val(response.data.job_card_no || '');
                        $('#doc_date').val(response.data.job_card_date || '');
                        
                        if (response.data.is_canvas) {
                            $('#warehouse_required_star').hide();
                            $('#warehouse_capacity_info').html('');
                        } else {
                            $('#warehouse_required_star').show();
                        }

                        if (response.data.store_type_id) {
                            $('#store_type_id').val(response.data.store_type_id).trigger('change.select2');
                        }
                        
                        if (response.data.items && response.data.items.length > 0) {
                            var responseItems = response.data.items;
                            
                            if (oldItems && Object.keys(oldItems).length > 0) {
                                var expandedItems = [];
                                var matchedOldIndices = new Set();

                                function getStrictItemKey(it) {
                                    var art = (it.art_no || '').trim().toLowerCase();
                                    var size = (it.size_variant || it.size || '').trim().toLowerCase();
                                    var item = (it.item_code || it.item_name || it.item_id || '').trim().toLowerCase();
                                    var color = (it.color_id || it.color || '').trim().toLowerCase();
                                    return art + '||' + size + '||' + item + '||' + color;
                                }

                                responseItems.forEach(function(baseItem) {
                                    var baseKey = getStrictItemKey(baseItem);
                                    var matchingOldEntries = [];

                                    Object.keys(oldItems).forEach(function(k) {
                                        if (matchedOldIndices.has(k)) return;
                                        var o = oldItems[k];
                                        var oKey = getStrictItemKey(o);

                                        var isMatch = (oKey === baseKey);
                                        // Fallback for canvas which doesn't have art_no
                                        if (!isMatch && response.data.is_canvas) {
                                            var canvasBaseKey = (baseItem.size_variant || baseItem.size || '').trim().toLowerCase() + '||' + (baseItem.item_code || baseItem.item_name || '').trim().toLowerCase();
                                            var canvasOKey = (o.size_variant || o.size || '').trim().toLowerCase() + '||' + (o.item_code || o.item_name || '').trim().toLowerCase();
                                            isMatch = (canvasBaseKey === canvasOKey);
                                        }

                                        if (isMatch) {
                                            matchingOldEntries.push(o);
                                            matchedOldIndices.add(k);
                                        }
                                    });

                                    if (matchingOldEntries.length > 0) {
                                        matchingOldEntries.forEach(function(entry, idx) {
                                            var clone = $.extend({}, baseItem);
                                            clone.scan_qty = (typeof entry.scan_qty !== 'undefined' && entry.scan_qty !== '') ? entry.scan_qty : 0;
                                            clone.store_location_id = entry.store_location_id || '';
                                            clone.is_split = (entry.is_split === '1' || idx > 0);
                                            expandedItems.push(clone);
                                        });
                                    } else {
                                        expandedItems.push(baseItem);
                                    }
                                });
                                responseItems = expandedItems;
                            }
                            populateItemsGrid(responseItems, response.data.is_canvas);
                        } else {
                            $('#items-section').hide();
                        }
                    } else if (response.success === false && response.message) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: response.message,
                            confirmButtonColor: '#8c57ff',
                        });
                        $('#job_card_id').val('').trigger('change.select2');
                    }
                },
                error: function(xhr) {
                    console.error('Error fetching job card details:', xhr);
                    let errorMessage = 'Error loading job card details';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage,
                        confirmButtonColor: '#8c57ff',
                    });
                    $('#job_card_id').val('').trigger('change.select2');
                }
            });
        });

        $('form').on('submit', function(e) {
            let totalScanQty = 0;
            let missingLocation = false;
            let missingLocationRow = null;

            $('#items-tbody tr').each(function() {
                let scanQty = parseFloat($(this).find('.scan-qty').val()) || 0;
                totalScanQty += scanQty;
                if (scanQty > 0) {
                    let locVal = $(this).find('.store-location-select').val();
                    if (!locVal) {
                        missingLocation = true;
                        if (!missingLocationRow) missingLocationRow = $(this);
                    }
                }
            });
            
            if (totalScanQty <= 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please enter Qty To Receive for at least one item.',
                    confirmButtonColor: '#8c57ff',
                });
                return false;
            }

            if (missingLocation) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please select Store Location for all items with Qty To Receive.',
                    confirmButtonColor: '#8c57ff',
                });
                if (missingLocationRow) {
                    missingLocationRow.find('.store-location-select').focus();
                }
                return false;
            }
        });
        $(document).on('keypress', '.scan-qty', function (e) {
            if (e.which === 45 || e.which === 43) {
                e.preventDefault();
            }
        });
        
        function checkLiveWarehouseCapacity() {
            let warehouseId = $('#warehouse_id').val();
            let jobCardId = $('#job_card_id').val();
            
            if (!warehouseId || !jobCardId) {
                $('#warehouse_capacity_info').html('');
                return;
            }
            
            $.ajax({
                url: "{{ url('production_receipts/get-warehouse-capacity') }}",
                type: 'GET',
                data: { warehouse_id: warehouseId, job_card_id: jobCardId },
                success: function(res) {
                    if (res.has_capacity) {
                        if (res.is_over_capacity) {
                            let html = '<span class="text-danger d-block mt-1">' + res.message + '</span>';
                            $('#warehouse_capacity_info').html(html);
                        } else {
                            let html = '<span class="text-primary d-block mt-1 small">' + res.message + '</span>';
                            $('#warehouse_capacity_info').html(html);
                        }
                    } else {
                        let html = '<span class="text-danger d-block mt-1">' + (res.message || 'Selected warehouse has NO storage capacity configured for this brand.') + '</span>';
                        $('#warehouse_capacity_info').html(html);
                    }
                }
            });
        }

        $(document).on('change', '#warehouse_id, #job_card_id', function() {
            checkLiveWarehouseCapacity();
        });

        if ($('#job_card_id').val()) {
            $('#job_card_id').trigger('change');
        }

        if ($('#warehouse_id').val() && $('#job_card_id').val()) {
            checkLiveWarehouseCapacity();
        }
    });
</script>
@endsection


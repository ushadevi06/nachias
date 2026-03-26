@extends('layouts.common')
@section('title', 'View Job Card Item - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row g-4">
        <div class="col-lg-12 text-end">
            @if(auth()->id() == 1 || auth()->user()->can('fabric-consumption-pdf job-card'))
            <a href="{{ route('job_card_entries.fabric_consumption_pdf', $jobCard->id) }}" target="_blank" class="btn btn-primary me-2">
                <i class="ri ri-scissors-line me-1"></i> Fabric Consumption
            </a>
            @endif
            
            @if(auth()->id() == 1 || auth()->user()->can('work-order-pdf job-card'))
            <a href="{{ route('job_card_entries.work_order_pdf', $jobCard->id) }}" target="_blank" class="btn btn-primary">
                <i class="ri ri-file-list-3-line me-1"></i> Work Order
            </a>
            @endif
            
            <a href="{{ url('job_card_entries') }}" class="btn btn-secondary"><i class="ri ri-arrow-left-line me-1"></i> Back to List</a>
        </div>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box mb-3">
                        <h4>Job Card Issue Item</h4>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="job_card_no" value="{{ $jobCard->job_card_no }}" readonly>
                                <label for="job_card_no">Job Card Number *</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control jc_date" id="jc_date" value="{{ $jobCard->job_card_date ? date('d-m-Y', strtotime($jobCard->job_card_date)) : '' }}" readonly>
                                <label for="jc_date">Job Card Date *</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    @php
                        $totalIssueItems = 0;
                        foreach($jobCard->fabricDetails as $item) {
                            $fs = array_sum([$item->fs_36 ?? 0, $item->fs_38 ?? 0, $item->fs_40 ?? 0, $item->fs_42 ?? 0, $item->fs_44 ?? 0]);
                            $hs = array_sum([$item->hs_36 ?? 0, $item->hs_38 ?? 0, $item->hs_40 ?? 0, $item->hs_42 ?? 0, $item->hs_44 ?? 0, $item->hs_46 ?? 0]);
                            if($fs > 0) $totalIssueItems++;
                            if($hs > 0) $totalIssueItems++;
                            if($fs == 0 && $hs == 0) $totalIssueItems++;
                        }
                    @endphp
                    <div class="card-header-box mb-3 d-flex justify-content-between align-items-center bg-primary text-white p-2 rounded">
                        <h4 class="mb-0 text-white"><i class="ri-list-check-2 mr-2"></i> Issue Items</h4>
                        {{-- <div class="d-flex align-items-center gap-3">
                            <div class="badge bg-white text-primary px-3 py-2 rounded-pill">
                                <strong>F/S Shirt Price: ₹<span id="summary-price-fs">{{ number_format($jobCard->price_fs, 2) }}</span></strong>
                            </div>
                            <div class="badge bg-white text-primary px-3 py-2 rounded-pill">
                                <strong>H/S Shirt Price: ₹<span id="summary-price-hs">{{ number_format($jobCard->price_hs, 2) }}</span></strong>
                            </div>
                            <div class="small text-white">Records: {{ $totalIssueItems }} <i class="ri-search-line"></i></div>
                        </div> --}}
                    </div>

                    {{-- <ul class="nav nav-tabs mb-3" id="issueTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="issue-fs-tab" data-bs-toggle="tab" data-bs-target="#issue-fs-content" type="button" role="tab">Full Sleeve (F/S)</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="issue-hs-tab" data-bs-toggle="tab" data-bs-target="#issue-hs-content" type="button" role="tab">Half Sleeve (H/S)</button>
                        </li>
                    </ul> --}}

                    <div class="tab-content" id="issueTabsContent">
                        {{-- @php
                            $fsMeterValue = $jobCard->sleeveMeters->where('sleeve_type', 'Full Sleeve')->first()->meter ?? 0;
                            $hsMeterValue = $jobCard->sleeveMeters->where('sleeve_type', 'Half Sleeve')->first()->meter ?? 0;
                            $fsDefaultIssueTotal = $fsMeterValue * $jobCard->total_qty_fs;
                            $hsDefaultIssueTotal = $hsMeterValue * $jobCard->total_qty_hs;
                        @endphp --}}
                        {{-- F/S Tab --}}
                        <div class="tab-pane fade show active" id="issue-fs-content" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered table-sm align-middle text-nowrap issue-items-table" id="issue-items-table-fs">
                                    <thead class="bg-primary">
                                        <tr>
                                            <th>Action</th><th>Line#</th><th>Store</th><th>Location</th><th>Item</th><th>Description</th><th>Art</th><th>Qty/UOM</th><th>UOM</th><th>Qty To Issue</th><th>Qty Wastage</th><th>Qty Used</th><th>Qty Adjusted</th><th>Produced Qty</th><th>Unit Price</th>{{-- <th>Total Cost</th><th style="min-width: 100px;">Cost/Pc</th> --}}<th>Status</th><th>Modified By</th><th>Modified On</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $lineNum = 1; @endphp
                                        @foreach($jobCard->fabricDetails as $index => $item)
                                            @php
                                                $materialName = $artMaterialMap[$item->art_no] ?? $item->art_no;
                                                $locationName = $artLocationMap[$item->art_no] ?? '-';
                                                $poItem = $jobCard->purchaseOrder?->items?->where('art_no', $item->art_no)->first();
                                                $uomName = ($poItem && $poItem->uom) ? $poItem->uom->uom_code : (($poItem && $poItem->rawMaterial && $poItem->rawMaterial->uom) ? $poItem->rawMaterial->uom->uom_code : ($artUomMap[$item->art_no] ?? '-'));
                                                
                                                $total_qty = $item->quantities->sum('total_qty'); 
                                                $produced_qty = $total_qty; 
                                                $savedItem = $issueItemMap[$item->id] ?? null;
                                                $itemDisplayName = $jobCard->item->code ?: $jobCard->item->name;
                                                $itemDescription = $jobCard->item->name;
                                            @endphp
                                            
                                            <tr data-line="{{ $lineNum }}">
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-icon edit-item-btn text-primary" data-bs-toggle="modal" data-bs-target="#editItemModal"
                                                        data-store="{{ $jobCard->issueStore->store_type_name ?? '-' }}" 
                                                        data-item="{{ $itemDisplayName }}" 
                                                        data-art="{{ $item->art_no }}" 
                                                        data-uom="{{ $uomName }}" 
                                                        data-qty-issue="{{ $savedItem->qty_issue ?? $item->mtr }}" 
                                                        data-matrix-id="{{ $item->id }}" 
                                                        data-qty-adjusted="{{ $savedItem->qty_adjusted ?? '0.00' }}" 
                                                        data-qty-wastage="{{ $savedItem->qty_wastage ?? '0.00' }}" 
                                                        data-qty-used="{{ $savedItem->qty_used ?? '0.00' }}" 
                                                        data-bit="{{ $savedItem->bit ?? '0.00' }}" 
                                                        data-balance="{{ $savedItem->balance ?? '0.00' }}" 
                                                        data-average="{{ $savedItem->average ?? '0.00' }}" 
                                                        data-produced-qty="{{ $produced_qty }}" 
                                                        data-row-qty="{{ $total_qty }}" 
                                                        title="Edit">
                                                        <i class="ri ri-edit-line"></i>
                                                    </button>
                                                </td>
                                                <td>{{ $lineNum++ }}</td>
                                                <td class="col-store">{{ $jobCard->issueStore->store_type_name ?? '-' }}</td>
                                                <td>{{ $locationName }}</td>
                                                <td class="col-item">{{ $itemDisplayName }}</td>
                                                <td class="col-description">{{ $itemDescription }}</td>
                                                <td class="fw-bold col-art">{{ $item->art_no }}</td>
                                                <td>1</td><td>{{ $uomName }}</td>
                                                <td>
                                                    <p class="mb-0 col-qty-issue text-end">{{ $savedItem->qty_issue ?? $item->mtr }}</p>
                                                    <input type="hidden" name="items[{{ $item->id }}][bit]" class="col-bit" value="{{ $savedItem->bit ?? '0.00' }}">
                                                    <input type="hidden" name="items[{{ $item->id }}][balance]" class="col-balance" value="{{ $savedItem->balance ?? '0.00' }}">
                                                    <input type="hidden" name="items[{{ $item->id }}][average]" class="col-average" value="{{ $savedItem->average ?? '0.00' }}">
                                                </td>
                                                <td><p class="mb-0 col-qty-wastage text-end">{{ $savedItem->qty_wastage ?? '0.00' }}</p></td>
                                                <td><p class="mb-0 col-qty-used text-end">{{ $savedItem->qty_used ?? '0.00' }}</p></td>
                                                <td><p class="mb-0 col-qty-adjusted text-end">{{ $savedItem->qty_adjusted ?? '0.00' }}</p></td>
                                                <td><p class="mb-0 col-produced-qty text-end">{{ $produced_qty }}</p></td>
                                                <td><p class="mb-0 col-unit-price text-end">{{ (isset($savedItem->unit_price) && $savedItem->unit_price > 0) ? number_format($savedItem->unit_price, 2, '.', '') : (isset($artPriceMap[$item->art_no]) ? number_format($artPriceMap[$item->art_no], 2, '.', '') : '0.00') }}</p></td>
                                                {{-- <td><input type="hidden" name="items[{{ $item->id }}][total_cost]" class="col-total-cost" value="{{ (isset($savedItem->total_cost) && $savedItem->total_cost > 0) ? number_format($savedItem->total_cost, 2, '.', '') : '0.00' }}"></td>
                                                <td><input type="hidden" class="col-cost-per-pc" value="{{ (isset($savedItem->cost_per_pc) && $savedItem->cost_per_pc > 0) ? number_format($savedItem->cost_per_pc, 2, '.', '') : '0.00' }}"></td> --}}
                                                <td><span class="badge {{ ($savedItem && $savedItem->qty_used > 0) ? 'bg-label-success' : 'bg-label-info' }} status-badge">{{ ($savedItem && $savedItem->qty_used > 0) ? 'COMPLETED' : 'OPEN' }}</span></td>
                                                <td>{{ $jobCard->creator->name ?? 'N/A' }}</td><td>{{ $jobCard->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Edit Item Modal -->
<div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title text-white d-flex align-items-center">
                    <i class="ri-edit-circle-line me-2 fs-4"></i> Edit Item Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editItemForm">
                    <input type="hidden" id="modal_row_index">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="modal_store" class="form-control" readonly placeholder="Store">
                                <label for="modal_store">Store</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="modal_item" class="form-control" readonly placeholder="Brand">
                                <label for="modal_item">Brand</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="modal_art" class="form-control" readonly placeholder="Art No">
                                <label for="modal_art">Art No</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="modal_uom" class="form-control" readonly placeholder="UOM">
                                <label for="modal_uom">UOM</label>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 rounded-3 border">
                        <h6 class="mb-4 text-primary fw-bold d-flex align-items-center">
                            <i class="ri-calculator-line me-2 fs-5"></i> Issue Quantities & Wastage Calculation
                        </h6>
                        <div class="row g-4">
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="number" step="0.01" id="modal_qty_issue" class="form-control bg-white fw-bold" readonly>
                                    <label>Issued</label>
                                </div>
                                {{-- <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" id="modal_bit" class="form-control border-primary" placeholder="Bit">
                                    <label>Bit</label>
                                </div> --}}
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="number" step="0.01" id="modal_qty_used" class="form-control border-primary" placeholder="Used">
                                    <label>Used</label>
                                </div>
                                {{-- <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" id="modal_avg" class="form-control bg-white fw-bold text-primary" readonly>
                                    <label>Qty/PC</label>
                                </div> --}}
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" id="modal_qty_wastage" class="form-control bg-white fw-bold text-danger" readonly>
                                    <label>Wastage</label>
                                </div>
                            </div>
                            {{-- <div class="col-md-3">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="number" step="0.0001" id="modal_consumption" class="form-control border-primary bg-light-warning" placeholder="Qty/Pc">
                                    <label class="text-primary fw-bold font-weight-bold">
                                        <i class="ri-ruler-2-line"></i> Qty/Pc
                                    </label>
                                </div>
                            </div> --}}
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="number" step="0.01" id="modal_qty_adjusted" class="form-control border-primary" placeholder="Adjusted">
                                    <label>Adjusted</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" id="modal_produced_qty" class="form-control bg-light fw-bold" readonly>
                                    <label>Produced Qty</label>
                                </div>
                                <input type="hidden" id="modal_unit_price">
                                <input type="hidden" id="modal_total_cost">
                                <input type="hidden" id="modal_cost_per_pc">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top p-3">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i> Close
                </button>
                <button type="button" class="btn btn-primary px-4" id="updateItemData">
                    <i class="ri-checkbox-circle-line me-1"></i> Update Data
                </button>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    let currentRow;
    const fsMeter = {{ $jobCard->sleeveMeters->where('sleeve_type', 'Full Sleeve')->first()->meter ?? 0 }};
    const hsMeter = {{ $jobCard->sleeveMeters->where('sleeve_type', 'Half Sleeve')->first()->meter ?? 0 }};

    $(document).on('click', '.edit-item-btn', function() {
        const button = $(this);
        currentRow = button.closest('tr');

        const store = button.attr('data-store') || '';
        const item = button.attr('data-item') || '';
        const art = button.attr('data-art') || '';
        const uom = button.attr('data-uom') || '';
        const qtyIssue = parseFloat(button.attr('data-qty-issue')) || 0;
        const rowQty = parseFloat(button.attr('data-row-qty')) || 1;
        const matrixId = button.attr('data-matrix-id');

        $('#modal_row_index').val(matrixId); 
        
        const otherUsage = 0;
        $('#modal_other_usage').val(otherUsage.toFixed(2));
        
        const qtyAdjusted = button.attr('data-qty-adjusted') || currentRow.find('.col-qty-adjusted').text() || '0.00';
        const qtyWastage = button.attr('data-qty-wastage') || currentRow.find('.col-qty-wastage').text() || '0.00';
        const qtyUsed = button.attr('data-qty-used') || currentRow.find('.col-qty-used').text() || '0.00';
        const bit = button.attr('data-bit') || currentRow.find('.col-bit').val() || '0.00';
        const balance = button.attr('data-balance') || currentRow.find('.col-balance').val() || '0.00';
        const average = button.attr('data-average') || currentRow.find('.col-average').val() || '0.00';
        const producedQty = button.attr('data-produced-qty') || currentRow.find('.col-produced-qty').text() || '0.00';

        $('#store').val(store);
        $('#modal_store').val(store);
        $('#modal_item').val(item);
        $('#modal_art').val(art);
        $('#modal_uom').val(uom);
        $('#modal_qty_issue').val(qtyIssue.toFixed(2));
        
        $('#modal_qty_adjusted').val(qtyAdjusted);
        $('#modal_qty_wastage').val(qtyWastage);
        $('#modal_qty_used').val(qtyUsed);
        $('#modal_bit').val(bit);
        $('#modal_balance').val(balance);
        $('#modal_avg').val(average);
        $('#modal_produced_qty').val(producedQty);
        
        const unitPrice = button.attr('data-unit-price') || currentRow.find('.col-unit-price').text() || '0.00';
        const totalCost = button.attr('data-total-cost') || currentRow.find('.col-total-cost').val() || '0.00';
        $('#modal_unit_price').val(unitPrice);
        $('#modal_total_cost').val(totalCost);
        
        calculateAll();
        $('#editItemModal').modal('show');
    });

    function calculateAll(source = 'all') {
        const qtyIssue = parseFloat($('#modal_qty_issue').val()) || 0;
        const qtyAdjusted = parseFloat($('#modal_qty_adjusted').val()) || 0;
        const otherUsage = parseFloat($('#modal_other_usage').val()) || 0;
        const totalIssuedGlobal = qtyIssue + qtyAdjusted;
        
        const producedQty = parseFloat($('#modal_produced_qty').val()) || 1;
        let qtyUsed = parseFloat($('#modal_qty_used').val()) || 0;
        let consumption = parseFloat($('#modal_consumption').val()) || 0;
        const bit = parseFloat($('#modal_bit').val()) || 0;

        if (source === 'consumption') {
            qtyUsed = consumption * producedQty;
            $('#modal_qty_used').val(qtyUsed.toFixed(2));
        } else if (source === 'used') {
            if (producedQty > 0) {
                consumption = qtyUsed / producedQty;
                $('#modal_consumption').val(consumption.toFixed(4));
            }
        } else {
            if (producedQty > 0) {
                consumption = qtyUsed / producedQty;
                $('#modal_consumption').val(consumption.toFixed(4));
            }
        }

        const netBalance = totalIssuedGlobal - qtyUsed - otherUsage;
        $('#modal_balance').val(netBalance.toFixed(2));
        const wastage = qtyIssue - qtyUsed; 
        $('#modal_qty_wastage').val(wastage.toFixed(2));
        const artAvg = producedQty > 0 ? (qtyUsed + wastage) / producedQty : 0;
        $('#modal_avg').val(artAvg.toFixed(4));

        const unitPrice = parseFloat($('#modal_unit_price').val()) || 0;
        const totalCost = qtyIssue * unitPrice;
        $('#modal_total_cost').val(totalCost.toFixed(2));

        const costPerPc = producedQty > 0 ? (totalCost / producedQty) : 0;
        $('#modal_cost_per_pc').val(costPerPc.toFixed(2));
    }

    /* $('#modal_qty_issue').css('cursor', 'pointer').attr('title', 'Click to calculate based on sleeve meter');
    $('#modal_qty_issue').on('click', function() {
        const sleeveType = $('#modal_sleeve_type').val();
        const producedQty = parseFloat($('#modal_produced_qty').val()) || 0;
        
        if (sleeveType === 'Full Sleeve' && fsMeter > 0) {
            const calculatedQty = fsMeter * producedQty;
            $(this).val(calculatedQty.toFixed(2));
            calculateAll('all');
            
            Swal.fire({
                icon: 'info',
                title: 'Calculated',
                text: 'Issue Qty calculated: ' + fsMeter + ' (Meter) x ' + producedQty + ' (Qty) = ' + calculatedQty.toFixed(2),
                timer: 2000,
                showConfirmButton: false
            });
        } else if (sleeveType === 'Half Sleeve' && hsMeter > 0) {
            const calculatedQty = hsMeter * producedQty;
            $(this).val(calculatedQty.toFixed(2));
            calculateAll('all');

            Swal.fire({
                icon: 'info',
                title: 'Calculated',
                text: 'Issue Qty calculated: ' + hsMeter + ' (Meter) x ' + producedQty + ' (Qty) = ' + calculatedQty.toFixed(2),
                timer: 2000,
                showConfirmButton: false
            });
        }
    }); */

    $('#modal_consumption').on('input', function() { calculateAll('consumption'); });

    $('#modal_balance').on('input', function() { calculateAll('balance'); });
    $('#modal_qty_used').on('input', function() { calculateAll('used'); });
    $('#modal_bit, #modal_qty_adjusted, #modal_unit_price').on('input', function() { calculateAll('all'); });

    $('#updateItemData').on('click', function() {
        if (currentRow) {
            const matrixId = $('#modal_row_index').val();
            const adj = $('#modal_qty_adjusted').val();
            const was = $('#modal_qty_wastage').val();
            const use = $('#modal_qty_used').val();
            const bit = $('#modal_bit').val();
            const bal = $('#modal_balance').val();
            const avg = $('#modal_avg').val();
            const pro = $('#modal_produced_qty').val();
            
            let formData = {};
            formData['_token'] = '{{ csrf_token() }}';
            formData['items'] = {};
            formData['items'][matrixId] = {
                'qty_issue': $('#modal_qty_issue').val(),
                'qty_adjusted': adj,
                'qty_wastage': was,
                'qty_used': use,
                'bit': bit,
                'balance': bal,
                'average': avg,
                'produced_qty': pro,
                'unit_price': $('#modal_unit_price').val(),
                'total_cost': $('#modal_total_cost').val(),
                'is_manual_price': 0
            };

            const btn = $(this);
            const originalText = btn.html();
            btn.prop('disabled', true).html('<i class="ri-loader-4-line ri-spin me-1"></i> Saving...');

            $.ajax({
                url: "{{ route('job_card_entries.issue_items', $jobCard->id) }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    if(response.success) {
                        currentRow.find('.col-qty-adjusted').text(adj);
                        currentRow.find('.col-qty-wastage').text(was);
                        currentRow.find('.col-qty-used').text(use);
                        currentRow.find('.col-bit').val(bit);
                        currentRow.find('.col-balance').val(bal);
                        currentRow.find('.col-average').val(avg);
                        currentRow.find('.col-produced-qty').text(pro);

                        if (response.updated_items && response.updated_items[matrixId]) {
                            var itemData = response.updated_items[matrixId];
                            currentRow.find('.col-unit-price').text(parseFloat(itemData.unit_price).toFixed(2));
                            // currentRow.find('.col-total-cost').text(parseFloat(itemData.total_cost).toFixed(2));
                            // currentRow.find('.col-cost-per-pc').text(parseFloat(itemData.cost_per_pc).toFixed(2));
                            currentRow.find('.status-badge').removeClass('bg-label-info').addClass('bg-label-success').text('COMPLETED');
                            
                            if (response.total_price !== undefined) {
                                $('#summary-price-fs').text(parseFloat(response.total_price).toLocaleString(undefined, {minimumFractionDigits: 2}));
                            }
                        }

                        const editBtn = currentRow.find('.edit-item-btn');
                        editBtn.attr('data-qty-adjusted', adj);
                        editBtn.attr('data-qty-wastage', was);
                        editBtn.attr('data-qty-used', use);
                        editBtn.attr('data-bit', bit);
                        editBtn.attr('data-balance', bal);
                        editBtn.attr('data-average', avg);
                        editBtn.attr('data-produced-qty', pro);

                        $('.edit-item-btn').each(function() {
                            const btn = $(this);
                            if (btn.attr('data-matrix-id') == matrixId) {
                                btn.attr('data-other-usage', use);
                            }
                        });

                        currentRow.addClass('table-success');
                        setTimeout(() => currentRow.removeClass('table-success'), 1000);

                        $('#editItemModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message || 'Item updated successfully',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to update item',
                            confirmButtonColor: '#d33'
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An unexpected error occurred.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.statusText) {
                        errorMessage = xhr.statusText;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Updating Item',
                        text: errorMessage,
                        confirmButtonColor: '#d33',
                        footer: xhr.status ? `Error Code: ${xhr.status}` : ''
                    });
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalText);
                }
            });
        }
    });
});
</script>
@endsection

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
            <a href="{{ route('job_card_entries.accessories_consumption_pdf', $jobCard->id) }}" target="_blank" class="btn btn-primary me-2">
                <i class="ri ri-ink-bottle-line me-1"></i> Accessories Consumption
            </a>
            @endif
            
            @if(auth()->id() == 1 || auth()->user()->can('work-order-pdf job-card'))
            <a href="{{ route('job_card_entries.work_order_pdf', $jobCard->id) }}" target="_blank" class="btn btn-primary me-2">
                <i class="ri ri-file-list-3-line me-1"></i> Work Order
            </a>
            @endif

            <a href="{{ route('job_card_entries.costing_analysis', $jobCard->id) }}" class="btn btn-success me-2">
                <i class="ri ri-funds-line me-1"></i> Costing Analysis
            </a>
            
            <a href="{{ url('job_card_entries') }}" class="btn btn-secondary"><i class="ri ri-arrow-left-line me-1"></i> Back to List</a>

            <div class="mt-3 d-flex justify-content-end gap-3 flex-wrap">
                @php
                    $wipCost = $jobCard->operations->sum('total_cost');
                    $wipAvg = $jobCard->grand_total_qty > 0 ? $wipCost / $jobCard->grand_total_qty : 0;
                @endphp
                @if($wipCost > 0)
                <div class="badge bg-label-warning px-3 py-2 border border-warning">
                    <i class="ri-settings-4-line me-1"></i> <span class="fw-semibold">WIP Process Cost:</span> ₹{{ number_format($wipCost, 2) }} <span class="ms-1 small">(Avg: ₹{{ number_format($wipAvg, 2) }}/pc)</span>
                </div>
                @endif
                <div class="badge bg-label-info px-3 py-2 border border-info">
                    <i class="ri-t-shirt-line me-1"></i> <span class="fw-semibold">Total Qty:</span> {{ number_format($jobCard->grand_total_qty, 0) }}
                </div>
                <div class="badge bg-label-primary px-3 py-2 border border-primary">
                    <i class="ri-t-shirt-2-line me-1"></i> <span class="fw-semibold">F/S Qty:</span> {{ number_format($jobCard->total_qty_fs, 0) }}
                </div>
                <div class="badge bg-label-success px-3 py-2 border border-success">
                    <i class="ri-shirt-line me-1"></i> <span class="fw-semibold">H/S Qty:</span> {{ number_format($jobCard->total_qty_hs, 0) }}
                </div>
            </div>
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

                    <ul class="nav nav-tabs mb-3" id="issueTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="main-consumption-tab" data-bs-toggle="tab" data-bs-target="#main-consumption-content" type="button" role="tab">
                                <i class="ri-scissors-2-line me-1"></i> Fabric
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="accessories-tab" data-bs-toggle="tab" data-bs-target="#accessories-content" type="button" role="tab">
                                <i class="ri-ink-bottle-line me-1"></i> Accessories
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="issueTabsContent">
                        {{-- @php
                            $fsMeterValue = $jobCard->sleeveMeters->where('sleeve_type', 'Full Sleeve')->first()->meter ?? 0;
                            $hsMeterValue = $jobCard->sleeveMeters->where('sleeve_type', 'Half Sleeve')->first()->meter ?? 0;
                            $fsDefaultIssueTotal = $fsMeterValue * $jobCard->total_qty_fs;
                            $hsDefaultIssueTotal = $hsMeterValue * $jobCard->total_qty_hs;
                        @endphp --}}
                        {{-- F/S Tab --}}
                        @php
                            $mainItems = [];
                            $accItems = [];
                            foreach($jobCard->fabricDetails as $item) {
                                $catId = $artCategoryMap[$item->art_no] ?? 1;
                                if($catId == 1) $mainItems[] = $item;
                                else $accItems[] = $item;
                            }
                        @endphp
                        <div class="tab-pane fade show active" id="main-consumption-content" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered table-sm align-middle text-nowrap" id="main-items-table">
                                    <thead class="bg-primary">
                                        <tr>
                                            <th>Action</th><th>Line#</th><th>Store</th><th>Location</th><th>Item</th><th>Description</th><th>Art</th><th>Qty/UOM</th><th>UOM</th><th>Qty To Issue</th><th>Qty Wastage</th><th>Qty Used</th><th>Qty Adjusted</th><th>Produced Qty</th><th>Unit Price</th><th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $lineNum = 1; @endphp
                                        @foreach($mainItems as $item)
                                            @php
                                                $materialName = $artMaterialMap[$item->art_no] ?? $item->art_no;
                                                $locationName = $artLocationMap[$item->art_no] ?? '-';
                                                $poItem = $jobCard->purchaseOrder?->items?->where('art_no', $item->art_no)->first();
                                                $uomName = ($poItem && $poItem->uom) ? $poItem->uom->uom_code : (($poItem && $poItem->rawMaterial && $poItem->rawMaterial->uom) ? $poItem->rawMaterial->uom->uom_code : ($artUomMap[$item->art_no] ?? '-'));
                                                $total_qty = $item->quantities->sum('total_qty'); 
                                                $produced_qty = $total_qty; 
                                                $savedItem = $issueItemMap[$item->id] ?? null;
                                                $itemDisplayName = $jobCard->brand->brand_name ?? '-';
                                                $itemDescription = $jobCard->brand->code ?? '-';
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
                                                <td>{{ $jobCard->issueStore->store_type_name ?? '-' }}</td>
                                                <td>{{ $locationName }}</td>
                                                <td>{{ $itemDisplayName }}</td>
                                                <td>{{ $itemDescription }}</td>
                                                <td class="fw-bold">{{ $item->art_no }}</td>
                                                <td>1</td><td>{{ $uomName }}</td>
                                                <td><p class="mb-0 col-qty-issue text-end">{{ $savedItem->qty_issue ?? $item->mtr }}</p></td>
                                                <td><p class="mb-0 col-qty-wastage text-end">{{ $savedItem->qty_wastage ?? '0.00' }}</p></td>
                                                <td><p class="mb-0 col-qty-used text-end">{{ $savedItem->qty_used ?? '0.00' }}</p></td>
                                                <td><p class="mb-0 col-qty-adjusted text-end">{{ $savedItem->qty_adjusted ?? '0.00' }}</p></td>
                                                <td><p class="mb-0 col-produced-qty text-end">{{ $produced_qty }}</p></td>
                                                <td><p class="mb-0 col-unit-price text-end">{{ (isset($savedItem->unit_price) && $savedItem->unit_price > 0) ? number_format($savedItem->unit_price, 2, '.', '') : (isset($artPriceMap[$item->art_no]) ? number_format($artPriceMap[$item->art_no], 2, '.', '') : '0.00') }}</p></td>
                                                <td><span class="badge {{ ($savedItem && $savedItem->qty_used > 0) ? 'bg-label-success' : 'bg-label-info' }} status-badge">{{ ($savedItem && $savedItem->qty_used > 0) ? 'COMPLETED' : 'OPEN' }}</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="accessories-content" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered table-sm align-middle text-nowrap" id="acc-items-table">
                                    <thead class="bg-primary">
                                        <tr>
                                            <th>Action</th>
                                            <th>Line#</th>
                                            <th>Store</th>
                                            <th>Location</th>
                                            <th>Item</th>
                                            <th>Art</th>
                                            <th>Qty/UOM</th>
                                            <th>UOM</th>
                                            <th>Qty to Issue</th>
                                            <th>Qty Wastage</th>
                                            <th>Qty Used</th>
                                            <th>Qty Adjusted</th>
                                            <th>Produced Qty</th>
                                            <th>Unit Price</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $lineNumAcc = 1; @endphp
                                        @foreach($accItems as $item)
                                            @php
                                                $rmId = $artRawMaterialIdMap[$item->art_no] ?? null;
                                                $std = $rmId ? ($standardConsumptions[$rmId] ?? null) : null;
                                                $fsCons = $std ? (float)$std->fs_qty : 0;
                                                $hsCons = $std ? (float)$std->hs_qty : 0;
                                                $calcQty = ($fsCons * $jobCard->total_qty_fs) + ($hsCons * $jobCard->total_qty_hs);
                                                
                                                $materialName = $artMaterialMap[$item->art_no] ?? $item->art_no;
                                                $locationName = $artLocationMap[$item->art_no] ?? '-';
                                                $poItem = $jobCard->purchaseOrder?->items?->where('art_no', $item->art_no)->first();
                                                $uomName = ($poItem && $poItem->uom) ? $poItem->uom->uom_code : (($poItem && $poItem->rawMaterial && $poItem->rawMaterial->uom) ? $poItem->rawMaterial->uom->uom_code : ($artUomMap[$item->art_no] ?? '-'));
                                                $savedItem = $issueItemMap[$item->id] ?? null;
                                                $unitPrice = (isset($savedItem->unit_price) && $savedItem->unit_price > 0) ? $savedItem->unit_price : ($artPriceMap[$item->art_no] ?? 0);
                                            @endphp
                                            <tr data-line="{{ $lineNumAcc }}">
                                                @php
                                                    $defaultQty = $savedItem->qty_issue ?? ($savedItem ? $savedItem->qty_used : (($item->mtr > 0) ? $item->mtr : $calcQty));
                                                @endphp
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-icon edit-item-btn text-primary" data-bs-toggle="modal" data-bs-target="#editItemModal"
                                                        data-store="{{ $jobCard->issueStore->store_type_name ?? '-' }}" 
                                                        data-item="{{ $materialName }}" 
                                                        data-art="{{ $item->art_no }}" 
                                                        data-uom="{{ $uomName }}" 
                                                        data-qty-issue="{{ $defaultQty }}" 
                                                        data-matrix-id="{{ $item->id }}" 
                                                        data-qty-adjusted="{{ $savedItem->qty_adjusted ?? '0.00' }}" 
                                                        data-qty-wastage="{{ $savedItem->qty_wastage ?? '0.00' }}" 
                                                        data-qty-used="{{ $savedItem->qty_used ?? $defaultQty }}" 
                                                        data-produced-qty="{{ $jobCard->grand_total_qty }}" 
                                                        data-unit-price="{{ number_format($unitPrice, 2, '.', '') }}"
                                                        data-std-fs="{{ $fsCons }}"
                                                        data-std-hs="{{ $hsCons }}"
                                                        data-calc-qty="{{ $calcQty }}"
                                                        title="Edit">
                                                        <i class="ri ri-edit-line"></i>
                                                    </button>
                                                </td>
                                                <td>{{ $lineNumAcc++ }}</td>
                                                <td>{{ $jobCard->issueStore->store_type_name ?? '-' }}</td>
                                                <td>{{ $locationName }}</td>
                                                <td>
                                                    <div class="fw-bold text-dark">{{ $materialName }}</div>
                                                    @if($fsCons > 0 || $hsCons > 0)
                                                        <div class="small text-muted mt-1" style="font-size: 10px;">
                                                            <span class="badge bg-primary-subtle text-primary border-primary border px-1" style="font-size: 9px;">FS: {{ number_format($fsCons, 2) }}</span>
                                                            <span class="badge bg-success-subtle text-success border-success border px-1" style="font-size: 9px;">HS: {{ number_format($hsCons, 2) }}</span>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="fw-bold">{{ $item->art_no }}</td>
                                                <td class="text-center text-primary fw-bold">{{ $item->mtr > 0 ? number_format($item->mtr, 2) : ($calcQty > 0 ? number_format($calcQty, 2) : '-') }}</td>
                                                <td>{{ $uomName }}</td>
                                                <td class="text-end col-qty-issue">{{ number_format($defaultQty, 2) }}</td>
                                                <td class="text-end col-qty-wastage">{{ number_format($savedItem->qty_wastage ?? 0, 2) }}</td>
                                                <td class="text-center fw-bold col-qty-used">{{ $savedItem->qty_used ?? $defaultQty }}</td>
                                                <td class="text-end col-qty-adjusted">{{ number_format($savedItem->qty_adjusted ?? 0, 2) }}</td>
                                                <td class="text-end col-produced-qty">{{ number_format($jobCard->grand_total_qty, 2) }}</td>
                                                <td class="text-end col-unit-price">{{ number_format($unitPrice, 2) }}</td>
                                                <td><span class="badge {{ ($savedItem && $savedItem->qty_used > 0) ? 'bg-label-success' : 'bg-label-info' }} status-badge">{{ ($savedItem && $savedItem->qty_used > 0) ? 'COMPLETED' : 'OPEN' }}</span></td>
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
                                    <input type="number" step="0.01" id="modal_qty_issue" class="form-control bg-white fw-bold">
                                    <label>Issued</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="number" step="0.01" id="modal_qty_used" class="form-control border-primary" placeholder="Used">
                                    <label>Used</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" id="modal_qty_wastage" class="form-control bg-white fw-bold text-danger" readonly>
                                    <label>Wastage</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="number" step="0.01" id="modal_qty_adjusted" class="form-control border-primary" placeholder="Adjusted">
                                    <label>Adjusted</label>
                                </div>
                            </div>
                            <div class="col-md-3" id="modal_std_info_wrapper" style="display: none;">
                                <div class="p-2 bg-light rounded border">
                                    <small class="text-muted d-block">Standard Cons.</small>
                                    <span class="fw-bold" id="modal_std_display">-</span>
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
        
        const fsCons = parseFloat(button.attr('data-std-fs')) || 0;
        const hsCons = parseFloat(button.attr('data-std-hs')) || 0;
        const calcQty = parseFloat(button.attr('data-calc-qty')) || 0;

        $('#modal_row_index').val(matrixId); 
        
        if (calcQty > 0) {
            $('#modal_std_info_wrapper').show();
            $('#modal_std_display').html(`FS: ${fsCons.toFixed(4)} | HS: ${hsCons.toFixed(4)} <br> Calc: ${calcQty.toFixed(2)}`);
        } else {
            $('#modal_std_info_wrapper').hide();
        }
        
        const qtyAdjusted = button.attr('data-qty-adjusted') || currentRow.find('.col-qty-adjusted').text() || '0.00';
        const qtyWastage = button.attr('data-qty-wastage') || currentRow.find('.col-qty-wastage').text() || '0.00';
        const qtyUsed = button.attr('data-qty-used') || currentRow.find('.col-qty-used').text() || '0.00';
        const producedQty = button.attr('data-produced-qty') || currentRow.find('.col-produced-qty').text() || '0.00';

        $('#modal_store').val(store);
        $('#modal_item').val(item);
        $('#modal_art').val(art);
        $('#modal_uom').val(uom);
        $('#modal_qty_issue').val(parseFloat(qtyIssue).toFixed(2));
        
        $('#modal_qty_adjusted').val(parseFloat(qtyAdjusted).toFixed(2));
        $('#modal_qty_wastage').val(parseFloat(qtyWastage).toFixed(2));
        $('#modal_qty_used').val(parseFloat(qtyUsed).toFixed(2));
        $('#modal_produced_qty').val(parseFloat(producedQty).toFixed(2));
        
        const unitPrice = button.attr('data-unit-price') || currentRow.find('.col-unit-price').text() || '0.00';
        $('#modal_unit_price').val(unitPrice);
        
        calculateAll();
        $('#editItemModal').modal('show');
    });

    function calculateAll(source = 'all') {
        const qtyIssue = parseFloat($('#modal_qty_issue').val()) || 0;
        const qtyAdjusted = parseFloat($('#modal_qty_adjusted').val()) || 0;
        const totalIssuedGlobal = qtyIssue + qtyAdjusted;
        const producedQty = parseFloat($('#modal_produced_qty').val()) || 1;
        let qtyUsed = parseFloat($('#modal_qty_used').val()) || 0;

        const wastage = qtyIssue - qtyUsed; 
        $('#modal_qty_wastage').val(wastage.toFixed(2));

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

    $('#modal_qty_used, #modal_qty_issue, #modal_qty_adjusted, #modal_unit_price').on('input', function() { calculateAll(); });

    $('#updateItemData').on('click', function() {
        if (currentRow) {
            const matrixId = $('#modal_row_index').val();
            const adj = $('#modal_qty_adjusted').val();
            const was = $('#modal_qty_wastage').val();
            const use = $('#modal_qty_used').val();
            const pro = $('#modal_produced_qty').val();
            
            let formData = {};
            formData['_token'] = '{{ csrf_token() }}';
            formData['items'] = {};
            formData['items'][matrixId] = {
                'qty_issue': $('#modal_qty_issue').val(),
                'qty_adjusted': adj,
                'qty_wastage': was,
                'qty_used': use,
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
                        currentRow.find('.col-qty-issue').text(parseFloat($('#modal_qty_issue').val()).toFixed(2));
                        currentRow.find('.col-qty-adjusted').text(adj);
                        currentRow.find('.col-qty-wastage').text(was);
                        currentRow.find('.col-qty-used').text(use);
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

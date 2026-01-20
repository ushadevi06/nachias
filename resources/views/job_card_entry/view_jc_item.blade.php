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
                        <div class="d-flex align-items-center gap-2">
                            <div class="small text-white">Records: {{ $totalIssueItems }} <i class="ri-search-line"></i></div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-sm align-middle text-nowrap" id="issue-items-table">
                            <thead class="bg-primary">
                                <tr>
                                    <th>Action</th>
                                    <th>Line#</th>
                                    <th>Store</th>
                                    <th>Location</th>
                                    <th>Item</th>
                                    <th>Description</th>
                                    <th>Art</th>
                                    <th>Supplier</th>
                                    <th>Qty/UOM</th>
                                    <th>UOM</th>
                                    <th>Qty To Issue</th>
                                    <th>Qty Adjusted</th>
                                    <th>Qty Wastage</th>
                                    <th>Qty Used</th>
                                    <th>Unit Price</th>
                                    <th>Total Cost</th>
                                    <th>Status</th>
                                    <th>Modified By</th>
                                    <th>Modified On</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $lineNum = 1; @endphp
                                @foreach($jobCard->fabricDetails as $index => $item)
                                @php
                                    $materialName = $artMaterialMap[$item->art_no] ?? $item->art_no;
                                    $locationName = $artLocationMap[$item->art_no] ?? '-';
                                    $poItem = $jobCard->purchaseOrder->items->where('art_no', $item->art_no)->first();
                                    $uomName = ($poItem && $poItem->uom) ? $poItem->uom->uom_code : (($poItem && $poItem->rawMaterial && $poItem->rawMaterial->uom) ? $poItem->rawMaterial->uom->uom_code : ($artUomMap[$item->art_no] ?? '-'));
                                    $brandCode = $poItem->brand->code ?? ($jobCard->brand->code ?? ($jobCard->purchaseOrder->items->first()->brand->code ?? '-'));
                                    $styleName = $poItem->style->style_name ?? ($jobCard->purchaseOrder->items->whereNotNull('style_id')->first()->style->style_name ?? '-');
                                    $styleCode = $poItem->style->code ?? ($jobCard->purchaseOrder->items->whereNotNull('style_id')->first()->style->code ?? '-');
                                    $brandName = $poItem->brand->brand_name ?? ($jobCard->brand->brand_name ?? ($jobCard->purchaseOrder->items->first()->brand->brand_name ?? '-'));
                                    $fs_total = $item->quantities->sum('qty_fs');
                                    $hs_total = $item->quantities->sum('qty_hs');
                                    $produced_qty_for_issue = $item->quantities->sum('total_qty') ?: ($fs_total + $hs_total);
                                    $sleeveTypes = [];
                                    if($fs_total > 0) $sleeveTypes['Full Sleeve'] = $produced_qty_for_issue;
                                    if($hs_total > 0) $sleeveTypes['Half Sleeve'] = $produced_qty_for_issue;
                                    if(empty($sleeveTypes)) $sleeveTypes['Full Sleeve'] = $produced_qty_for_issue;
                                @endphp
                                
                                @foreach($sleeveTypes as $sleeveType => $pieces)
                                @php
                                    $savedItem = $issueItemMap[$item->id . '_' . $sleeveType] ?? ($issueItemMap[$item->id . '_'] ?? null);
                                    $sleeveSuffix = ($sleeveType == 'Full Sleeve') ? 'FS' : 'HS';
                                    $sleeveShort = ($sleeveType == 'Full Sleeve') ? 'F/S' : 'H/S';
                                    $itemDisplayName = $brandCode . '-' . $styleCode . '-' . $sleeveSuffix;
                                    $itemDescription = $brandName . ' ' . $styleName . ' ' . $sleeveType . ' (' . $sleeveShort . ')';
                                @endphp
                                <tr data-line="{{ $lineNum }}">
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-icon edit-item-btn text-primary" 
                                                data-bs-toggle="modal" data-bs-target="#editItemModal"
                                                data-store="{{ $jobCard->receipt_store }}"
                                                data-item="{{ $itemDisplayName }}"
                                                data-sleeve-type="{{ $sleeveType }}"
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
                                                data-produced-qty="{{ $pieces }}"
                                                data-row-qty="{{ $pieces }}"
                                                title="Edit">
                                                <i class="ri ri-edit-line"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>{{ $lineNum++ }}</td>
                                    <td class="col-store">{{ $jobCard->receipt_store }}</td>
                                    <td>{{ $locationName }}</td>
                                    <td class="col-item">{{ $itemDisplayName }}</td>
                                    <td class="col-description">{{ $itemDescription }}</td>
                                    <td class="fw-bold col-art">{{ $item->art_no }}</td>
                                    <td>{{ $jobCard->purchaseOrder->supplier->name ?? '-' }}</td>
                                    <td>1</td>
                                    <td>{{ $uomName }}</td>
                                    <td>
                                        <input type="number" step="0.01" name="items[{{ $item->id }}][{{ $sleeveType }}][qty_issue]" class="form-control form-control-sm text-end col-qty-issue" value="{{ $savedItem->qty_issue ?? $item->mtr }}" readonly>
                                        <input type="hidden" name="items[{{ $item->id }}][{{ $sleeveType }}][bit]" class="col-bit" value="{{ $savedItem->bit ?? '0.00' }}">
                                        <input type="hidden" name="items[{{ $item->id }}][{{ $sleeveType }}][balance]" class="col-balance" value="{{ $savedItem->balance ?? '0.00' }}">
                                        <input type="hidden" name="items[{{ $item->id }}][{{ $sleeveType }}][average]" class="col-average" value="{{ $savedItem->average ?? '0.00' }}">
                                        <input type="hidden" name="items[{{ $item->id }}][{{ $sleeveType }}][produced_qty]" class="col-produced-qty" value="{{ $pieces }}">
                                        <input type="hidden" name="items[{{ $item->id }}][{{ $sleeveType }}][sleeve_type]" class="col-sleeve-type" value="{{ $sleeveType }}">
                                    </td>
                                    <td><input type="number" step="0.01" name="items[{{ $item->id }}][{{ $sleeveType }}][qty_adjusted]" class="form-control form-control-sm text-end col-qty-adjusted" value="{{ $savedItem->qty_adjusted ?? '0.00' }}"></td>
                                    <td><input type="number" step="0.01" name="items[{{ $item->id }}][{{ $sleeveType }}][qty_wastage]" class="form-control form-control-sm text-end col-qty-wastage" value="{{ $savedItem->qty_wastage ?? '0.00' }}" readonly></td>
                                    <td><input type="number" step="0.01" name="items[{{ $item->id }}][{{ $sleeveType }}][qty_used]" class="form-control form-control-sm text-end col-qty-used" value="{{ $savedItem->qty_used ?? '0.00' }}"></td>
                                    <td><input type="number" step="0.01" name="items[{{ $item->id }}][{{ $sleeveType }}][unit_price]" class="form-control form-control-sm text-end col-unit-price" value="{{ (isset($savedItem->unit_price) && $savedItem->unit_price > 0) ? number_format($savedItem->unit_price, 2, '.', '') : '0.00' }}"></td>
                                    <td><input type="number" step="0.01" name="items[{{ $item->id }}][{{ $sleeveType }}][total_cost]" class="form-control form-control-sm text-end col-total-cost" value="{{ (isset($savedItem->total_cost) && $savedItem->total_cost > 0) ? number_format($savedItem->total_cost, 2, '.', '') : '0.00' }}"></td>
                                    <td><span class="badge {{ ($savedItem && $savedItem->qty_used > 0) ? 'bg-label-success' : 'bg-label-info' }} status-badge">{{ ($savedItem && $savedItem->qty_used > 0) ? 'COMPLETED' : 'OPEN' }}</span></td>
                                    <td>{{ $jobCard->creator->name ?? 'N/A' }}</td>
                                    <td>{{ $jobCard->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endforeach
                                @endforeach
                            </tbody>
                        </table>
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
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="modal_sleeve_type" class="form-control" readonly placeholder="Sleeve Type">
                                <label for="modal_sleeve_type">Sleeve Type</label>
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
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" id="modal_bit" class="form-control border-primary" placeholder="Bit">
                                    <label>Bit</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="number" step="0.01" id="modal_qty_adjusted" class="form-control border-primary" placeholder="Adjusted">
                                    <label>Adjusted</label>
                                </div>
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" id="modal_qty_wastage" class="form-control bg-white fw-bold text-danger" readonly>
                                    <label>Wastage</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="number" step="0.01" id="modal_qty_used" class="form-control border-primary" placeholder="Used">
                                    <label>Used</label>
                                </div>
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" id="modal_avg" class="form-control bg-white fw-bold text-primary" readonly>
                                    <label>Average</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="number" step="0.01" id="modal_balance" class="form-control bg-white fw-bold" readonly>
                                    <label>Balance</label>
                                </div>
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="number" id="modal_pieces" class="form-control bg-white" readonly>
                                    <label>Pieces for Item</label>
                                    <input type="hidden" id="modal_produced_qty">
                                </div>
                            </div>
                            {{-- <div class="col-md-3">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="number" step="0.01" id="modal_unit_price" class="form-control border-primary" placeholder="Unit Price">
                                    <label>Unit Price</label>
                                </div>
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" id="modal_total_cost" class="form-control bg-white fw-bold text-success" readonly>
                                    <label>Total Cost</label>
                                </div>
                            </div> --}}
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

    $(document).on('click', '.edit-item-btn', function() {
        const button = $(this);
        currentRow = button.closest('tr');

        const store = button.attr('data-store') || '';
        const item = button.attr('data-item') || '';
        const sleeveType = button.attr('data-sleeve-type') || '';
        const art = button.attr('data-art') || '';
        const uom = button.attr('data-uom') || '';
        const qtyIssue = parseFloat(button.attr('data-qty-issue')) || 0;
        const rowQty = parseFloat(button.attr('data-row-qty')) || 1;
        const matrixId = button.attr('data-matrix-id');

        $('#modal_row_index').val(matrixId); 
        $('#modal_sleeve_type').val(sleeveType); 
        
        const qtyAdjusted = button.attr('data-qty-adjusted') || currentRow.find('.col-qty-adjusted').val() || '0.00';
        const qtyWastage = button.attr('data-qty-wastage') || currentRow.find('.col-qty-wastage').val() || '0.00';
        const qtyUsed = button.attr('data-qty-used') || currentRow.find('.col-qty-used').val() || '0.00';
        const bit = button.attr('data-bit') || currentRow.find('.col-bit').val() || '0.00';
        const balance = button.attr('data-balance') || currentRow.find('.col-balance').val() || '0.00';
        const average = button.attr('data-average') || currentRow.find('.col-average').val() || '0.00';
        const producedQty = button.attr('data-produced-qty') || currentRow.find('.col-produced-qty').val() || '0.00';

        $('#modal_store').val(store);
        $('#modal_item').val(item);
        $('#modal_sleeve_type').val(sleeveType);
        $('#modal_art').val(art);
        $('#modal_uom').val(uom);
        $('#modal_qty_issue').val(qtyIssue.toFixed(2));
        $('#modal_pieces').val(rowQty);
        
        $('#modal_qty_adjusted').val(qtyAdjusted);
        $('#modal_qty_wastage').val(qtyWastage);
        $('#modal_qty_used').val(qtyUsed);
        $('#modal_bit').val(bit);
        $('#modal_balance').val(balance);
        $('#modal_avg').val(average);
        $('#modal_produced_qty').val(producedQty);
        
        const unitPrice = button.attr('data-unit-price') || currentRow.find('.col-unit-price').val() || '0.00';
        const totalCost = button.attr('data-total-cost') || currentRow.find('.col-total-cost').val() || '0.00';
        $('#modal_unit_price').val(unitPrice);
        $('#modal_total_cost').val(totalCost);
        
        calculateAll();
        $('#editItemModal').modal('show');
    });

    function calculateAll(source = 'all') {
        const overallAvg = parseFloat("{{ $jobCard->average }}") || 1;
        const qtyIssue = parseFloat($('#modal_qty_issue').val()) || 0;
        const qtyAdjusted = parseFloat($('#modal_qty_adjusted').val()) || 0;
        const totalAvailable = qtyIssue + qtyAdjusted;
        
        let qtyUsed = parseFloat($('#modal_qty_used').val()) || 0;
        let balance = parseFloat($('#modal_balance').val()) || 0;
        const bit = parseFloat($('#modal_bit').val()) || 0;

        if (source === 'balance') {
            qtyUsed = totalAvailable - balance;
            $('#modal_qty_used').val(qtyUsed.toFixed(2));
        } else {
            balance = totalAvailable - qtyUsed;
            $('#modal_balance').val(balance.toFixed(2));
        }

        const wastage = balance - bit;
        $('#modal_qty_wastage').val(wastage.toFixed(2));

        const producedQty = parseFloat($('#modal_pieces').val()) || 0;
        $('#modal_produced_qty').val(producedQty);

        const qtyConsumed = qtyUsed + wastage + qtyAdjusted;

        let artAvg = 0;
        if (producedQty > 0) {
            artAvg = qtyConsumed / producedQty;
        }
        $('#modal_avg').val(artAvg.toFixed(2));

        const unitPrice = parseFloat($('#modal_unit_price').val()) || 0;
        const totalCost = qtyUsed * unitPrice;
        $('#modal_total_cost').val(totalCost.toFixed(2));
    }

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
            const sleeveType = $('#modal_sleeve_type').val();
            
            let formData = {};
            formData['_token'] = '{{ csrf_token() }}';
            formData['items'] = {};
            formData['items'][matrixId] = {};
            formData['items'][matrixId][sleeveType] = {
                'qty_issue': $('#modal_qty_issue').val(),
                'qty_adjusted': adj,
                'qty_wastage': was,
                'qty_used': use,
                'bit': bit,
                'balance': bal,
                'average': avg,
                'produced_qty': pro,
                'unit_price': $('#modal_unit_price').val(),
                'sleeve_type': sleeveType
            };

            const btn = $(this);
            const originalText = btn.html();
            btn.prop('disabled', true).html('<i class="ri-loader-4-line ri-spin me-1"></i> Saving...');

            $.ajax({
                url: "{{ route('job_card_entries.issue_items', $jobCard->id) }}",
                method: "POST",
                data: formData,
                success: function(response) {
                    if(response.success) {
                        currentRow.find('.col-qty-adjusted').val(adj);
                        currentRow.find('.col-qty-wastage').val(was);
                        currentRow.find('.col-qty-used').val(use);
                        currentRow.find('.col-bit').val(bit);
                        currentRow.find('.col-balance').val(bal);
                        currentRow.find('.col-average').val(avg);
                        currentRow.find('.col-produced-qty').val(pro);

                        if (response.updated_items && response.updated_items[matrixId] && response.updated_items[matrixId][sleeveType]) {
                            var itemData = response.updated_items[matrixId][sleeveType];
                            currentRow.find('.col-unit-price').val(parseFloat(itemData.unit_price).toFixed(2));
                            currentRow.find('.col-total-cost').val(parseFloat(itemData.total_cost).toFixed(2));
                            currentRow.find('.status-badge').removeClass('bg-label-info').addClass('bg-label-success').text('COMPLETED');
                        }

                        const editBtn = currentRow.find('.edit-item-btn');
                        editBtn.attr('data-qty-adjusted', adj);
                        editBtn.attr('data-qty-wastage', was);
                        editBtn.attr('data-qty-used', use);
                        editBtn.attr('data-bit', bit);
                        editBtn.attr('data-balance', bal);
                        editBtn.attr('data-average', avg);
                        editBtn.attr('data-produced-qty', pro);

                        currentRow.addClass('table-success');
                        setTimeout(() => currentRow.removeClass('table-success'), 1000);

                        $('#editItemModal').modal('hide');
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Error updating item: ' + xhr.statusText);
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

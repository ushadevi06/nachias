@extends('layouts.common')
@section('title', 'View Job Card Item - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row g-4">
        <form action="{{ url('job_card_entries/issue-items/'.$jobCard->id) }}" method="POST">
            @csrf
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
                    <div class="card-header-box mb-3 d-flex justify-content-between align-items-center bg-primary text-white p-2 rounded">
                        <h4 class="mb-0 text-white"><i class="ri-list-check-2 mr-2"></i> Issue Items</h4>
                        <div class="small text-white">Records: {{ $jobCard->articleMatrices->count() }} <i class="ri-search-line"></i></div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle text-nowrap" id="issue-items-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Edit</th>
                                    <th>Line#</th>
                                    <th>Store</th>
                                    <th>Location</th>
                                    <th>Item</th>
                                    <th>Size</th>
                                    <th>Art</th>
                                    <th>Supplier</th>
                                    <th>Qty/UOM</th>
                                    <th>UOM</th>
                                    <th>Qty To Issue</th>
                                    <th>Qty Adjusted</th>
                                    <th>Qty Wastage</th>
                                    <th>Qty Used</th>
                                    <th>Status</th>
                                    <th>Modified By</th>
                                    <th>Modified On</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jobCard->articleMatrices as $index => $item)
                                @php
                                    $materialName = $artMaterialMap[$item->art_no] ?? $item->art_no;
                                    $locationName = $artLocationMap[$item->art_no] ?? '-';
                                    $poItem = $jobCard->purchaseOrder->items->where('art_no', $item->art_no)->first();
                                    $uomName = $poItem->uom->name ?? 'PCS';
                                @endphp
                                <tr data-line="{{ $index + 1 }}">
                                    <td>
                                        <button type="button" class="btn btn-sm btn-icon edit-item-btn text-primary" 
                                            data-bs-toggle="modal" data-bs-target="#editItemModal"
                                            data-store="{{ $jobCard->receipt_store }}"
                                            data-item="{{ $materialName }}"
                                            data-art="{{ $item->art_no }}"
                                            data-uom="{{ $uomName }}"
                                            data-qty-issue="{{ $item->mtr }}">
                                            <i class="ri ri-edit-line"></i>
                                        </button>
                                    </td>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="col-store">{{ $jobCard->receipt_store }}</td>
                                    <td>{{ $locationName }}</td>
                                    <td class="col-item">{{ $materialName }}</td>
                                    <td>58IN</td>
                                    <td class="fw-bold col-art">{{ $item->art_no }}</td>
                                    <td>{{ $jobCard->purchaseOrder->supplier->name ?? '-' }}</td>
                                    <td>1</td>
                                    <td class="col-uom">{{ $uomName }}</td>
                                    <td>
                                        <input type="number" step="0.01" name="items[{{ $item->id }}][qty_issue]" class="form-control form-control-sm text-end col-qty-issue" value="{{ $item->mtr }}" readonly>
                                    </td>
                                    <td><input type="number" step="0.01" name="items[{{ $item->id }}][qty_adjusted]" class="form-control form-control-sm text-end col-qty-adjusted" value="0.00"></td>
                                    <td><input type="number" step="0.01" name="items[{{ $item->id }}][qty_wastage]" class="form-control form-control-sm text-end col-qty-wastage" value="0.00"></td>
                                    <td><input type="number" step="0.01" name="items[{{ $item->id }}][qty_used]" class="form-control form-control-sm text-end col-qty-used" value="0.00"></td>
                                    <td><span class="badge bg-label-info">OPEN</span></td>
                                    <td>{{ $jobCard->creator->name ?? 'N/A' }}</td>
                                    <td>{{ $jobCard->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ url('job_card_entries') }}" class="btn btn-secondary">Cancel</a>
        </div>
        </form>
    </div>
</div>
<!-- Edit Item Modal -->
<div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
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
                                <input type="text" id="modal_store" class="form-control bg-light" readonly placeholder="Store">
                                <label for="modal_store">Store</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="modal_item" class="form-control bg-light" readonly placeholder="Item Name">
                                <label for="modal_item">Item Name</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="modal_art" class="form-control bg-light" readonly placeholder="Art No">
                                <label for="modal_art">Art No</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="modal_uom" class="form-control bg-light" readonly placeholder="UOM">
                                <label for="modal_uom">UOM</label>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-label-primary rounded-3">
                        <h6 class="mb-3 text-primary fw-bold"><i class="ri-edit-box-line me-1"></i> Issue Quantities</h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" id="modal_qty_issue" class="form-control bg-light" readonly placeholder="Qty to Issue">
                                    <label for="modal_qty_issue">Qty to Issue</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" id="modal_qty_adjusted" class="form-control border-primary" placeholder="Qty Adjusted">
                                    <label for="modal_qty_adjusted">Qty Adjusted</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" id="modal_qty_wastage" class="form-control border-primary" placeholder="Qty Wastage">
                                    <label for="modal_qty_wastage">Qty Wastage</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" id="modal_qty_used" class="form-control border-primary" placeholder="Qty Used">
                                    <label for="modal_qty_used">Qty Used</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" id="modal_bit" class="form-control border-primary" placeholder="Bit">
                                    <label for="modal_bit">Bit</label>
                                </div>
                            </div> 
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" id="modal_balance" class="form-control border-primary" placeholder="Balance">
                                    <label for="modal_balance">Balance</label>
                                </div>
                            </div>
                             <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" id="modal_avg" class="form-control border-primary" placeholder="Balance">
                                    <label for="modal_avg">Average</label>
                                </div>
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

    // Handle Edit Button Click
    $(document).on('click', '.edit-item-btn', function() {
        const button = $(this);
        currentRow = button.closest('tr');

        // Extract data using .attr() to be safe
        const store = button.attr('data-store') || '';
        const item = button.attr('data-item') || '';
        const art = button.attr('data-art') || '';
        const uom = button.attr('data-uom') || '';
        const qtyIssue = button.attr('data-qty-issue') || '0.00';
        
        // Get dynamic values from table row inputs
        const qtyAdjusted = currentRow.find('.col-qty-adjusted').val() || '0.00';
        const qtyWastage = currentRow.find('.col-qty-wastage').val() || '0.00';
        const qtyUsed = currentRow.find('.col-qty-used').val() || '0.00';

        // Set values in modal
        $('#modal_store').val(store);
        $('#modal_item').val(item);
        $('#modal_art').val(art);
        $('#modal_uom').val(uom);
        $('#modal_qty_issue').val(qtyIssue);
        
        $('#modal_qty_adjusted').val(qtyAdjusted);
        $('#modal_qty_wastage').val(qtyWastage);
        $('#modal_qty_used').val(qtyUsed);

        // Manually show the modal (or rely on data-bs attributes)
        // If data-bs-toggle is on the button, this might be redundant but safe
        $('#editItemModal').modal('show');
    });

    $('#updateItemData').on('click', function() {
        if (currentRow) {
            // Update row values from modal back to table
            currentRow.find('.col-qty-adjusted').val($('#modal_qty_adjusted').val());
            currentRow.find('.col-qty-wastage').val($('#modal_qty_wastage').val());
            currentRow.find('.col-qty-used').val($('#modal_qty_used').val());

            // Visual feedback
            currentRow.addClass('table-success');
            setTimeout(() => currentRow.removeClass('table-success'), 1000);

            // Close modal
            $('#editItemModal').modal('hide');
        }
    });
});
</script>
@endsection

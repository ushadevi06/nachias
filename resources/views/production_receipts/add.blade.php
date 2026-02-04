@extends('layouts.common')
@section('title', ($receipt ? 'Edit' : 'Add') . ' Production Receipt - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="{{ url('production_receipts/add' . ($receipt ? '/' . $receipt->id : '')) }}" method="POST" class="common-form">
                @csrf
                {{-- @if ($errors->any())
                    <div class="alert alert-danger shadow-sm border-0 mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="ri-error-warning-line fs-4 me-2"></i>
                            <h6 class="mb-0 fw-bold">Please correct the following errors:</h6>
                        </div>
                        <ul class="mb-0 ps-3 text-danger">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success shadow-sm border-0 mb-4 d-flex align-items-center">
                        <i class="ri-checkbox-circle-line fs-4 me-2 text-success"></i>
                        <h6 class="mb-0 fw-bold text-success">{{ session('success') }}</h6>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger shadow-sm border-0 mb-4 d-flex align-items-center">
                        <i class="ri-error-warning-line fs-4 me-2 text-danger"></i>
                        <h6 class="mb-0 fw-bold text-danger">{{ session('error') }}</h6>
                    </div>
                @endif --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>{{ $receipt ? 'Edit' : 'Add' }} Production Receipt</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="job_card_id" id="job_card_id" class="form-select select2" data-placeholder="Select Job Card No">
                                        <option value="">Select Job Card No</option>
                                        @foreach($jobCards as $jobCard)
                                            <option value="{{ $jobCard->id }}" {{ old('job_card_id', $receipt->job_card_id ?? '') == $jobCard->id ? 'selected' : '' }}>{{ $jobCard->job_card_no }}</option>
                                        @endforeach
                                    </select>
                                    <label for="job_card_id">Job Card No *</label>
                                </div>
                                @error('job_card_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="receipt_no" id="receipt_no" class="form-control" placeholder="Enter Receipt No" value="{{ old('receipt_no', $receipt->receipt_no ?? '') }}">
                                    <label for="receipt_no">Receipt No *</label>
                                </div>
                                @error('receipt_no') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="receipt_date" id="receipt_date" class="form-control receipt-date" placeholder="Select Receipt Date" value="{{ old('receipt_date', $receipt && $receipt->receipt_date ? date('d-m-Y', strtotime($receipt->receipt_date)) : date('d-m-Y')) }}">
                                    <label for="receipt_date">Receipt Date *</label>
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
                                    <label for="doc_date">Doc Date *</label>
                                </div>
                                @error('doc_date') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4" id="customer_name_wrapper">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="customer_name" id="customer_name" class="form-control" placeholder="Customer Name" value="{{ old('customer_name', $receipt->customer_name ?? '') }}" readonly>
                                    <label for="customer_name">Customer Name</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="plant" id="plant" class="form-control" placeholder="Plant" value="{{ old('plant', ($receipt && $receipt->production && $receipt->production->plant) ? $receipt->production->plant->name : '') }}" readonly>
                                    <label for="plant">Plant</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="order_due_date" id="order_due_date" class="form-control" placeholder="Order Due Date" value="{{ old('order_due_date', ($receipt && $receipt->order_due_date) ? date('d-m-Y', strtotime($receipt->order_due_date)) : '') }}" readonly>
                                    <label for="order_due_date">Order Due Date</label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="store_type_id" id="store_type_id" class="form-select select2" data-placeholder="Select Store">
                                        <option value="">Select Store</option>
                                        @foreach($storeTypes as $storeType)
                                            <option value="{{ $storeType->id }}" {{ old('store_type_id', $receipt->store_type_id ?? '') == $storeType->id ? 'selected' : '' }}>
                                                {{ $storeType->store_type_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="store_type_id">Store *</label>
                                </div>
                                @error('store_type_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="store_location_id" id="store_location_id" class="form-select select2" data-placeholder="Select Location">
                                        <option value="">Select Location</option>
                                        @foreach($storeLocations as $location)
                                            <option value="{{ $location->id }}" {{ old('store_location_id', $receipt->store_location_id ?? '') == $location->id ? 'selected' : '' }}>
                                                {{ $location->store_location }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="store_location_id">Store Location *</label>
                                </div>
                                @error('store_location_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="form-select select2" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Draft" {{ old('status', $receipt->status ?? 'Draft') == 'Draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="Posted" {{ old('status', $receipt->status ?? '') == 'Posted' ? 'selected' : '' }}>Posted</option>
                                    </select>
                                    <label for="status">Status *</label>
                                </div>
                                @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea name="remarks" id="remarks" class="form-control" placeholder="Enter Remarks" >{{ old('remarks', $receipt->remarks ?? '') }}</textarea>
                                    <label for="remarks">Remarks</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-4" id="items-section" style="display: none;">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Items</h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-nowrap" id="items-table">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>Item</th>
                                        <th>Description</th>
                                        {{-- <th>Color</th> --}}
                                        <th>Size</th>
                                        <th>Unit Price</th>
                                        <th>Qty Ordered</th>
                                        <th>Qty Received</th>
                                        <th>Qty Balance</th>
                                        <th>Qty To Receive *</th>
                                    </tr>
                                </thead>
                                <tbody id="items-tbody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 text-end mt-3">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ url('production_receipts') }}" class="btn btn-secondary">Cancel</a>
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
                                <th class="ps-3 border-0">Batch Usage</th>
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
                                <th colspan="5" class="text-end pe-3 py-3 fs-6">Grand Total (Per Shirt)</th>
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

        function populateItemsGrid(items) {
            itemDataStore = items || [];
            var tbody = $('#items-tbody');
            tbody.empty();
            
            if (!items || items.length === 0) {
                tbody.append('<tr><td colspan="9" class="text-center">No items found</td></tr>');
                $('#items-section').hide();
                return;
            }

            items.forEach(function(item, index) {
                var scanQty = parseFloat(item.scan_qty || 0);
                var alreadyRec = parseFloat(item.qty_already_received || 0);
                var orderedQty = parseFloat(item.ordered_qty || 0);
                var currentBalance = orderedQty - (alreadyRec + scanQty);

                var row = '<tr data-index="' + index + '">' +
                    '<td>' + (item.item_code || '') + '<input type="hidden" name="items[' + index + '][item_name]" value="' + (item.item_name || '') + '"></td>' +
                    '<td>' + 
                        (item.description || '') + ' (' + (item.sleeve || '') + ') ' +
                        '<button type="button" class="btn btn-sm btn-link p-0 ms-1 btn-show-consumption" data-index="' + index + '" title="View Consumption Details">' +
                        '<i class="icon-base ri ri-information-line fs-5"></i></button>' +
                    '</td>' +
                    // '<td>' + (item.color || '-') + '</td>' +
                    '<td>' + (item.size || '') + '<input type="hidden" name="items[' + index + '][size]" value="' + (item.size || '') + '"></td>' +
                    '<td class="text-end">' + parseFloat(item.unit_price || 0).toFixed(2) + '<input type="hidden" class="unit-price" name="items[' + index + '][unit_price]" value="' + parseFloat(item.unit_price || 0).toFixed(2) + '"></td>' +
                    '<td class="text-end">' + orderedQty.toFixed(2) + '<input type="hidden" class="ordered-qty" name="items[' + index + '][ordered_qty]" value="' + orderedQty.toFixed(2) + '"></td>' +
                    '<td class="text-end"><span class="qty-received-text">' + (alreadyRec + scanQty).toFixed(2) + '</span><input type="hidden" class="qty-already-received" name="items[' + index + '][qty_already_received]" value="' + alreadyRec.toFixed(2) + '"></td>' +
                    '<td class="text-end"><span class="qty-balance-text">' + currentBalance.toFixed(2) + '</span><input type="hidden" class="balance-qty" name="items[' + index + '][balance_qty]" value="' + currentBalance.toFixed(2) + '"></td>' +
                    '<td><input type="number" step="0.01" class="form-control form-control-sm text-end scan-qty" name="items[' + index + '][scan_qty]" value="' + scanQty.toFixed(2) + '" min="0" data-max="' + (item.balance_qty || 0).toFixed(2) + '"></td>' +
                    '<input type="hidden" name="items[' + index + '][item_id]" value="' + (item.item_id || '') + '">' +
                    '<input type="hidden" name="items[' + index + '][item_code]" value="' + (item.item_code || '') + '">' +
                    '<input type="hidden" name="items[' + index + '][size_variant]" value="' + (item.size_variant || '') + '">' +
                    '<input type="hidden" name="items[' + index + '][uom_id]" value="' + (item.uom_id || '') + '">' +
                    '<input type="hidden" name="items[' + index + '][uom_code]" value="' + (item.uom_code || '') + '">' +
                    '<input type="hidden" name="items[' + index + '][completed_qty]" value="' + (item.completed_qty || 0) + '">' +
                    '</tr>';
                tbody.append(row);
            });
            $('#items-section').show();
        }

        $(document).on('click', '.btn-show-consumption', function() {
            var index = $(this).data('index');
            var item = itemDataStore[index];
            if (!item || !item.consumption_details) {
                alert('No consumption details found for this item.');
                return;
            }

            var infoHtml = '<div class="card bg-label-secondary border-0 mb-3">' +
                           '<div class="card-body p-3 d-flex justify-content-between align-items-center">' +
                           '<div><small class="text-uppercase text-muted fw-bold d-block mb-1">Item Details</small>' + 
                           '<span class="fw-bold text-dark fs-6">' + item.item_code + '</span> <span class="badge bg-white text-dark shadow-sm ms-2">' + item.size_variant + '</span>' +
                           '</div>' +
                           '<div class="text-end">' +
                           '<small class="text-uppercase text-muted fw-bold d-block mb-1">Batch Qty</small>' +
                           '<span class="fs-4 fw-bolder text-primary">' + parseFloat(item.scan_qty || 0).toFixed(2) + '</span>' +
                           '</div>' +
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

                tbody.append('<tr>' +
                    '<td class="ps-3"><span class="badge bg-label-success">' + batchUsage.toFixed(2) + '</span></td>' +
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
                    '<td class="text-end"><span class="fw-bold">' + rate.toFixed(2) + '</span></td>' +
                    '<td class="text-end">₹' + materialPrice.toFixed(2) + '</td>' +
                    '<td class="text-end pe-3 fw-bold text-dark">₹' + perShirtCost.toFixed(2) + '</td>' +
                    '</tr>');
            });

            $('#consumption-footer-total').text('₹' + grandTotalCost.toFixed(2));

            $('#consumptionModal').modal('show');
        });

        function calculateRowValues(row) {
            var scanQty = parseFloat(row.find('.scan-qty').val()) || 0;
            var orderedQty = parseFloat(row.find('.ordered-qty').val()) || 0;
            var alreadyReceived = parseFloat(row.find('.qty-already-received').val()) || 0;
            var availableBalance = parseFloat(row.find('.scan-qty').data('max')) || 0;
            
            if (scanQty > availableBalance) {
                alert('Qty To Receive cannot exceed Available Balance (' + availableBalance.toFixed(2) + ')');
                scanQty = availableBalance;
                row.find('.scan-qty').val(scanQty.toFixed(2));
            }
            
            if (scanQty < 0) {
                scanQty = 0;
                row.find('.scan-qty').val('0.00');
            }

            var totalReceived = alreadyReceived + scanQty;
            row.find('.qty-received-text').text(totalReceived.toFixed(2));

            var remainingBalance = orderedQty - totalReceived;
            row.find('.qty-balance-text').text(remainingBalance.toFixed(2));
        }

        $(document).on('input', '.scan-qty', function() {
            var row = $(this).closest('tr');
            calculateRowValues(row);
        });

        $('#job_card_id').on('change', function() {
            var jobCardId = $(this).val();
            if (!jobCardId) {
                $('#plant').val('');
                $('#doc_no').val('');
                $('#doc_date').val('');
                $('#items-section').hide();
                $('#items-tbody').empty();
                return;
            }

            var excludeReceiptId = {{ $receipt ? $receipt->id : 'null' }};
            var url = '{{ url("production_receipts/get-job-card-details") }}/' + jobCardId;
            if (excludeReceiptId) {
                url += '?exclude_receipt_id=' + excludeReceiptId;
            }

            var oldItems = @json(old('items', []));
            $.ajax({
                url: url,
                type: 'GET',
                data: { exclude_receipt_id: excludeReceiptId },
                success: function(response) {
                    if (response.success && response.data) {
                        $('#plant').val(response.data.plant_name || '');
                        $('#customer_name').val(response.data.customer_name || '');
                        $('#order_due_date').val(response.data.order_due_date || '');
                        $('#doc_no').val(response.data.job_card_no || '');
                        $('#doc_date').val(response.data.job_card_date || '');
                        
                        if (response.data.items && response.data.items.length > 0) {
                            var responseItems = response.data.items;
                            
                            if (oldItems && Object.keys(oldItems).length > 0) {
                                responseItems.forEach(function(item) {
                                    var match = Object.values(oldItems).find(function(oi) {
                                        return oi.item_id == item.item_id && oi.size_variant == item.size_variant;
                                    });
                                    if (match) {
                                        item.scan_qty = match.scan_qty;
                                    }
                                });
                            }
                            populateItemsGrid(responseItems);
                        } else {
                            $('#items-section').hide();
                        }
                    }
                },
                error: function(xhr) {
                    console.error('Error fetching job card details:', xhr);
                    alert('Error loading job card details');
                }
            });
        });

        if ($('#job_card_id').val()) {
            $('#job_card_id').trigger('change');
        }
    });
</script>
@endsection


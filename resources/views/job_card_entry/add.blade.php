@extends('layouts.common')
@section('title', ($jobCard ? 'Edit Job Card' : 'Add Job Card') . ' - ' . env('WEBSITE_NAME'))
@use('App\Models\StockEntry')
@section('content')

    @php
        $matrixRows = old('article_matrix', $jobCard ? $jobCard->fabricDetails->toArray() : []);
        $matrixItems = old('matrix_items', $jobCard ? $jobCard->cuttingSizeRatios->toArray() : []);

        $dynamicSizes = [];
        foreach ($matrixItems as $item) {
            if (!empty($item['size'])) {
                $dynamicSizes[] = $item['size'];
            }
        }

        $sizes = !empty($dynamicSizes) ? array_values(array_unique($dynamicSizes)) : ['36', '38', '40', '42', '44'];
        sort($sizes, SORT_NUMERIC);
        $ratios = [];
        foreach ($sizes as $s) {
            $found = false;
            foreach ($matrixItems as $item) {
                if (($item['size'] ?? '') == $s) {
                    $ratios[] = $item['ratio'] ?? '';
                    $found = true;
                    break;
                }
            }
            if (!$found)
                $ratios[] = '';
        }

        $fabrics = old('fabrics', $jobCard ? $jobCard->fabricDetails->toArray() : []);

        $activeFs = [];
        $activeHs = [];
        foreach ($matrixItems as $item) {
            $s = $item['size'] ?? '';
            if ($s) {
                if ((float) ($item['qty_fs'] ?? 0) > 0)
                    $activeFs[] = $s;
                if ((float) ($item['qty_hs'] ?? 0) > 0)
                    $activeHs[] = $s;
            }
        }

        $activeFs = array_values(array_unique($activeFs));
        sort($activeFs, SORT_NUMERIC);
        $activeHs = array_values(array_unique($activeHs));
        sort($activeHs, SORT_NUMERIC);

        $processGroupName = strtoupper(old('process_group_display', $jobCard && $jobCard->processGroup ? $jobCard->processGroup->name : ''));
        $hasFS = empty($processGroupName) || str_contains($processGroupName, 'F/S') || str_contains($processGroupName, 'FULL');
        $hasHS = empty($processGroupName) || str_contains($processGroupName, 'H/S') || str_contains($processGroupName, 'HALF');

        $showMatrix = $jobCard || !empty(old('article_matrix')) || !empty($activeFs) || !empty($activeHs);
        $hasPo = $jobCard || !empty(old('purchase_order_id'));

        $existingImages = $jobCard ? $jobCard->images : collect();
        $grnImageMap = $grnImageMap ?? [];
    @endphp
    <div class="container-xxl section-padding">
        <div class="row">
            <div class="col-lg-12">
                <form action="{{ url('job_card_entries/add/' . ($jobCard ? $jobCard->id : '')) }}" method="POST" class="common-form" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="col-lg-12">
                        @include('flash_messages')
                    </div>
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="card-header-box">
                                <h4>{{ $jobCard ? 'Edit' : 'Add' }} Job Card Entry</h4>
                            </div>
                            <div class="row g-4">   
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <select id="brand" name="brand_id" class="form-select select2" data-placeholder="Select Brand">
                                            <option value="">Select Brand</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}" {{ (old('brand_id', $jobCard ? $jobCard->brand_id : '') == $brand->id) ? 'selected' : '' }}>{{ $brand->brand_name }} ({{ $brand->code }})</option>
                                            @endforeach
                                        </select>
                                        <label for="brand">Brand * </label>
                                    </div>
                                    @error('brand_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" id="job_card_no" placeholder="Enter Job Card Number" name="job_card_no" value="{{ old('job_card_no', $jobCard ? $jobCard->job_card_no : '') }}">
                                        <label for="job_card_no">Job Card Number * </label>
                                    </div>
                                    @error('job_card_no') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <div class="input-group">
                                        <div class="form-floating form-floating-outline" style="position: relative;">
                                            <input type="text" id="stock_entry_search" class="form-control" placeholder="Type Stock Entry No or Material Name" autocomplete="off" {{ $hasTasks ? 'readonly' : '' }}>
                                            <label for="stock_entry_search">Type Stock Entry No or Material Name</label>
                                        </div>
                                    </div>
                                    <div id="stock-entry-tags" class="mt-2 d-flex flex-wrap gap-1"></div>
                                    <div id="fabric-validation-error" class="text-danger small fw-bold mt-2" style="display: none;"></div>
                                    <div id="stock-entry-hidden-inputs">
                                        @php
                                            $rawOldStockEntryIds = old('stock_entry_ids', $jobCard ? $jobCard->stock_entry_ids : []);

                                            if (is_string($rawOldStockEntryIds)) {
                                                $decoded = json_decode($rawOldStockEntryIds, true);
                                                $oldStockEntryIds = is_array($decoded) ? $decoded : [];
                                            } else {
                                                $oldStockEntryIds = is_array($rawOldStockEntryIds) ? $rawOldStockEntryIds : [];
                                            }
                                        @endphp
                                        @if(!empty($oldStockEntryIds))
                                            @foreach($oldStockEntryIds as $seId)
                                                <input type="hidden" name="stock_entry_ids[]" value="{{ $seId }}">
                                            @endforeach
                                        @endif
                                    </div>
                                    @error('stock_entry_ids') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                @php
                                    $initialEntries = [];
                                    if (!empty($oldStockEntryIds)) {
                                        $rawIds = $oldStockEntryIds;
                                        $seIds = [];
                                        $filters = [];
                                        foreach ($rawIds as $combinedId) {
                                            if (is_string($combinedId) && strpos($combinedId, '::') !== false) {
                                                list($seId, $target) = explode('::', $combinedId, 2);
                                                $seIds[] = $seId;
                                                if (strpos($target, '|') !== false) {
                                                    list($type, $val) = explode('|', $target, 2);
                                                    $filters[$seId][] = ['type' => $type, 'val' => $val, 'combined' => $combinedId];
                                                }
                                            } else {
                                                $seIds[] = $combinedId;
                                            }
                                        }

                                        if (!empty($seIds)) {
                                            $issuedQtys = [];
                                            if ($jobCard) {
                                                $issuedQtys = \App\Models\JobCardIssueItem::where('job_card_entry_id', $jobCard->id)->with('fabricDetail')->get()
                                                    ->groupBy(function ($item) {
                                                        return $item->fabricDetail->art_no ?? '';
                                                    })->map(function ($items) {
                                                        return $items->sum('qty_issue');
                                                    })->toArray();
                                            }

                                            $stockEntries = StockEntry::with('stockEntryItems.rawMaterial', 'stockEntryItems.uom')->whereIn('id', array_unique($seIds))->get();

                                            foreach ($stockEntries as $se) {
                                                if (!isset($filters[$se->id])) {
                                                    $names = [];
                                                    $qtys = [];
                                                    $artNos = [];
                                                    foreach ($se->stockEntryItems as $item) {
                                                        if ($item->art_no && !in_array($item->art_no, $artNos)) {
                                                            $artNos[] = $item->art_no;
                                                        }
                                                        $name = 'Unknown';
                                                        if ($item->raw_material_id && $item->rawMaterial) {
                                                            $name = $item->rawMaterial->name;
                                                        } elseif ($item->item_id && $item->item) {
                                                            $name = $item->item->name;
                                                        } elseif ($item->art_no) {
                                                            $name = $item->art_no;
                                                        }

                                                        if (!in_array($name, $names)) {
                                                            $names[] = $name;
                                                        }
                                                        $uom = $item->uom->uom_code ?? '';
                                                        $alreadyIssued = (float) ($issuedQtys[$item->art_no ?? ''] ?? 0);
                                                        $netQty = ($item->qty_in - ($item->qty_out ?? 0) + $alreadyIssued);

                                                        if (!isset($qtys[$uom])) {
                                                            $qtys[$uom] = 0;
                                                        }
                                                        $qtys[$uom] += $netQty;
                                                    }
                                                    $nameStr = implode(', ', $names);
                                                    $artNoStr = implode(', ', $artNos);
                                                    $qtyStrs = [];
                                                    foreach ($qtys as $uom => $qty) {
                                                        $qtyStrs[] = round($qty, 3) . ' ' . $uom;
                                                    }
                                                    $initialEntries[] = [
                                                        'id' => $se->id,
                                                        'text' => $se->stock_entry_no . ($artNoStr ? ' | ' . $artNoStr : '') . ' | ' . $nameStr . ' | Qty: ' . implode(', ', $qtyStrs),
                                                    ];
                                                } else {
                                                    $addedCombos = [];
                                                    foreach ($filters[$se->id] as $f) {
                                                        if (in_array($f['combined'], $addedCombos))
                                                            continue;

                                                        $name = 'Unknown';
                                                        $artNo = '';
                                                        $uom = '';
                                                        $netQty = 0;
                                                        foreach ($se->stockEntryItems as $item) {
                                                            $match = false;
                                                            if ($f['type'] === 'rm' && $item->raw_material_id == $f['val']) {
                                                                $name = $item->rawMaterial->name ?? 'Unknown';
                                                                $artNo = $item->art_no;
                                                                $match = true;
                                                            }
                                                            if ($f['type'] === 'item' && $item->item_id == $f['val']) {
                                                                $name = $item->item->name ?? 'Unknown';
                                                                $artNo = $item->art_no;
                                                                $match = true;
                                                            }
                                                            if ($f['type'] === 'art' && $item->art_no == $f['val']) {
                                                                $name = $item->rawMaterial->name ?? ($item->item->name ?? $item->art_no);
                                                                $artNo = $item->art_no;
                                                                $match = true;
                                                            }

                                                            if ($match) {
                                                                $uom = $item->uom->uom_code ?? '';
                                                                $alreadyIssued = (float) ($issuedQtys[$item->art_no ?? ''] ?? 0);
                                                                $netQty += ($item->qty_in - ($item->qty_out ?? 0) + $alreadyIssued);
                                                            }
                                                        }
                                                        $initialEntries[] = [
                                                            'id' => $f['combined'],
                                                            'text' => $se->stock_entry_no . ($artNo ? ' | ' . $artNo : '') . ' | ' . $name . ' | Qty: ' . round($netQty, 3) . ' ' . $uom
                                                        ];
                                                        $addedCombos[] = $f['combined'];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                @endphp
                                <script>
                                    window._initialStockEntries = @json($initialEntries);
                                </script>
                                <input type="hidden" name="purchase_order_id" value="{{ old('purchase_order_id', $jobCard ? $jobCard->purchase_order_id : '') }}">
                                <input type="hidden" name="fabric_type_id" id="fabric_type_id" value="{{ old('fabric_type_id', $jobCard ? $jobCard->fabric_type_id : '') }}">
                                <input type="hidden" name="sleeve_instances" id="sleeve_instances_json" value="{{ old('sleeve_instances', ($jobCard && $jobCard->sleeve_instances) ? json_encode($jobCard->sleeve_instances) : '[]') }}">
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <select id="plant" name="service_provider_id" class="form-select select2" data-placeholder="Select Plant">
                                            <option value="">Select Plant</option>
                                            @foreach($plants as $plant)
                                                <option value="{{ $plant->id }}" {{ (old('service_provider_id', $jobCard ? $jobCard->service_provider_id : '') == $plant->id) ? 'selected' : '' }}>
                                                    {{ $plant->name }} ({{ $plant->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="plant">Plant *</label>
                                    </div>
                                    @error('service_provider_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <select id="issue_store" name="issue_store_id" class="form-select select2" data-placeholder="Select Issue Store">
                                            <option value="">Select Issue Store</option>
                                            @foreach($storeTypes as $st)
                                                <option value="{{ $st->id }}" {{ (old('issue_store_id', $jobCard ? $jobCard->issue_store_id : '') == $st->id) ? 'selected' : '' }}>
                                                    {{ $st->store_type_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="issue_store">Issue Store *</label>
                                    </div>
                                    @error('issue_store_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <label class="mb-2">Washing</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="washing" id="washing_yes" value="Yes" {{ (old('washing', $jobCard ? $jobCard->washing : '') == 'Yes') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="washing_yes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="washing" id="washing_no" value="No" {{ (old('washing', $jobCard ? $jobCard->washing : 'No') == 'No') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="washing_no">No</label>
                                        </div>
                                    </div>
                                    @error('washing') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control form-control issue_date" placeholder="Enter Issue Date" name="issue_date" value="{{ old('issue_date', $jobCard ? date('d-m-Y', strtotime($jobCard->job_card_date)) : '') }}" />
                                        <label for="issue_date">Issue Date * </label>
                                    </div>
                                    @error('issue_date') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control form-control delivery_date" placeholder="Enter Delivery Date" name="delivery_date" value="{{ old('delivery_date', $jobCard ? date('d-m-Y', strtotime($jobCard->delivery_date)) : '') }}" />
                                        <label for="delivery_date">Delivery Date * </label>
                                    </div>
                                    @error('delivery_date') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" id="no_of_days_display" placeholder="No of Days" readonly value="{{ old('no_of_days', $jobCard ? $jobCard->no_of_days : '') }}">
                                        <input type="hidden" name="no_of_days" id="no_of_days" value="{{ old('no_of_days', $jobCard ? $jobCard->no_of_days : '') }}">
                                        <label for="no_of_days_display">No of Days (Auto)</label>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" id="width" placeholder="Enter Width" name="width" value="{{ old('width', $jobCard ? $jobCard->width : '') }}">
                                        <label for="width">Width</label>
                                    </div>
                                    @error('width') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <input type="hidden" id="total_qty_fs" name="total_qty_fs" value="{{ old('total_qty_fs', $jobCard ? $jobCard->total_qty_fs : '') }}">
                                <input type="hidden" id="total_qty_hs" name="total_qty_hs" value="{{ old('total_qty_hs', $jobCard ? $jobCard->total_qty_hs : '') }}">
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <select id="season" name="season_id" class="form-select select2" data-placeholder="Select Season Code">
                                            <option value="">Select Season Code</option>
                                            @foreach($seasons as $season)
                                                <option value="{{ $season->id }}" {{ (old('season_id', $jobCard ? $jobCard->season_id : '') == $season->id) ? 'selected' : '' }}>{{ $season->name }}({{ $season->season_code }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="season">Season Code</label>
                                    </div>
                                    @error('season_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <select id="receipt_store_id" name="receipt_store_id" class="form-select select2" data-placeholder="Select Receipt Store">
                                            <option value="">Select Receipt Store</option>
                                            @foreach($storeTypes as $st)
                                                <option value="{{ $st->id }}" {{ (old('receipt_store_id', $jobCard ? $jobCard->receipt_store_id : '') == $st->id) ? 'selected' : '' }}>{{ $st->store_type_name }}</option>
                                            @endforeach
                                        </select>
                                        <label for="receipt_store_id">Receipt Store *</label>
                                    </div>
                                    @error('receipt_store_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <div class="input-group">
                                        <div class="form-floating form-floating-outline" data-bs-toggle="modal" data-bs-target="#processGroupModal" style="cursor: pointer;">
                                            <input type="text" id="process_group_display" name="process_group_display" class="form-control" placeholder="Select Process Group" readonly value="{{ old('process_group_display', $jobCard && $jobCard->processGroup ? $jobCard->processGroup->name : '') }}" style="cursor: pointer;">
                                            <input type="hidden" id="process_group_id" name="process_group_id" value="{{ old('process_group_id', $jobCard ? $jobCard->process_group_id : '') }}">
                                            <label for="process_group_display">Process Group *</label>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#processGroupModal" id="processGroupBtn">
                                            <i class="ri ri-search-line"></i>
                                        </button>
                                    </div>
                                    @error('process_group_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" id="reference_no" placeholder="Enter Reference No" name="reference_no" value="{{ old('reference_no', $jobCard ? $jobCard->reference_no : '') }}">
                                        <label for="reference_no">Reference No * </label>
                                    </div>
                                    @error('reference_no') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <select id="job_card_type" name="job_card_type" class="form-select select2"
                                            data-placeholder="Select Job Card Type">
                                            <option value="">Select Job Card Type</option>
                                            @foreach(['Regular', 'Urgent', 'Sample', 'Special Order'] as $type)
                                                <option value="{{ $type }}" {{ (old('job_card_type', $jobCard ? $jobCard->job_card_type : 'Regular') == $type) ? 'selected' : '' }}>{{ $type }}</option>
                                            @endforeach
                                        </select>
                                        <label for="job_card_type">Job Card Type *</label>
                                    </div>
                                    @error('job_card_type') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <select id="status" name="status" class="form-select select2" data-placeholder="Select Status">
                                            <option value="">Select Status</option>
                                            <option value="Production Hold" {{ (old('status', $jobCard ? $jobCard->status : '') == 'Production Hold') ? 'selected' : '' }}>Production Hold</option>
                                            <option value="Production In Progress" {{ (old('status', $jobCard ? $jobCard->status : '') == 'Production In Progress') ? 'selected' : '' }}>Production In Progress</option>
                                            <option value="Production Completed" {{ (old('status', $jobCard ? $jobCard->status : '') == 'Production Completed') ? 'selected' : '' }}>Production Completed</option>
                                        </select>
                                        <label for="status">Status *</label>
                                    </div>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            setTimeout(() => {
                                                if ($('#status').hasClass('select2-hidden-accessible')) {
                                                    $('#status').val('{{ old('status', $jobCard ? $jobCard->status : '') }}').trigger('change');
                                                }
                                            }, 500);
                                        });
                                    </script>
                                    @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <textarea id="remarks" name="remarks" class="form-control" placeholder="Enter Remarks">{{ old('remarks', $jobCard ? $jobCard->remarks : '') }}</textarea>
                                        <label for="remarks">Remarks</label>
                                    </div>
                                    @error('remarks') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline text-black">
                                        <input type="file" class="form-control" id="attachment" name="attachment" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx,.csv">
                                        @if($jobCard && $jobCard->attachment)
                                            <div class="mt-2 d-flex align-items-center gap-2">
                                                <a href="{{ url($jobCard->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="ri ri-attachment-line me-1"></i> View Attachment
                                                </a>
                                            </div>
                                        @endif
                                        <label for="formFile" class="form-label">Reference Document</label>
                                        <small class="text-muted d-block mt-1">Max file size: 5MB. Supported formats: JPG, PNG, JPEG, WEBP, PDF, DOC, DOCX, XLS, XLSX, CSV</small>
                                        @error('attachment') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4" id="item-details-card">
                        <div class="card-body">
                            <div class="card-header-box mb-3 d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">Item Details</h4>
                            </div>
                            <div id="item-details-table-wrapper" class="table-responsive d-none">
                                <h6 class="text-primary mt-2">Fabric</h6>
                                <table class="table table-bordered table-sm align-middle mb-4" id="item-details-fabric-table">
                                    <thead class="bg-primary">
                                        <tr>
                                            <th class="text-center" style="width: 50px;">S.No</th>
                                            <th>Raw Material Name</th>
                                            <th class="text-center" style="width: 150px;">Total Quantity</th>
                                            <th class="text-center" style="width: 180px;">Quantity Issued</th>
                                            <th class="text-center" style="width: 150px;">Quantity Remaining</th>
                                        </tr>
                                    </thead>
                                    <tbody id="item-details-fabric-tbody">
                                    </tbody>
                                </table>

                                <h6 class="text-primary mt-2">Accessories</h6>
                                <table class="table table-bordered table-sm align-middle" id="item-details-accessories-table">
                                    <thead class="bg-primary">
                                        <tr>
                                            <th class="text-center" style="width: 50px;">S.No</th>
                                            <th>Raw Material Name</th>
                                            <th class="text-center" style="width: 150px;">Total Quantity</th>
                                            <th class="text-center" style="width: 180px;">Quantity Issued</th>
                                            <th class="text-center" style="width: 150px;">Quantity Remaining</th>
                                        </tr>
                                    </thead>
                                    <tbody id="item-details-accessories-tbody">
                                    </tbody>
                                </table>
                            </div>
                            <div id="no-materials-msg" class="text-center py-4 bg-light rounded text-muted">
                                <i class="ri-information-line fs-3 d-block mb-2 text-primary"></i>
                                <span id="no-material-text">Please select a Stock Entry Number to fetch material details.</span>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="card-header-box">
                                <h4>Tailoring Specification</h4>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <select id="fit" name="fit_id" class="form-select select2" data-placeholder="Select Fit">
                                            <option value="">Select Fit</option>
                                            @foreach($fits as $fit)
                                                <option value="{{ $fit->id }}" {{ (old('fit_id', $jobCard ? $jobCard->fit_id : '') == $fit->id) ? 'selected' : '' }}>{{ $fit->fit_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="fit">Fit</label>
                                    </div>
                                    @error('fit_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <select id="patti_type" name="patti_type_id" class="form-select select2" data-placeholder="Select Patti Type">
                                            <option value="">Select Patti Type</option>
                                            @foreach($pattiTypes as $type)
                                                <option value="{{ $type->id }}" {{ (old('patti_type_id', $jobCard ? $jobCard->patti_type_id : '') == $type->id) ? 'selected' : '' }}>{{ $type->patti_type_name }}</option>
                                            @endforeach
                                        </select>
                                        <label for="patti_type">Patti Type</label>
                                    </div>
                                    @error('patti_type_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <select id="collar_type" name="collar_type_id" class="form-select select2" data-placeholder="Select Collar Type">
                                            <option value="">Select Collar Type</option>
                                            @foreach($collarTypes as $type)
                                                <option value="{{ $type->id }}" {{ (old('collar_type_id', $jobCard ? $jobCard->collar_type_id : '') == $type->id) ? 'selected' : '' }}>{{ $type->collar_type_name }}</option>
                                            @endforeach
                                        </select>
                                        <label for="collar_type">Collar Type</label>
                                    </div>
                                    @error('collar_type_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <select id="cuff_type" name="cuff_type_id" class="form-select select2" data-placeholder="Select Cuff Type">
                                            <option value="">Select Cuff Type</option>
                                            @foreach($cuffTypes as $type)
                                                <option value="{{ $type->id }}" {{ (old('cuff_type_id', $jobCard ? $jobCard->cuff_type_id : '') == $type->id) ? 'selected' : '' }}>{{ $type->cuff_type_name }}</option>
                                            @endforeach
                                        </select>
                                        <label for="cuff_type">Cuff Type</label>
                                    </div>
                                    @error('cuff_type_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <select id="pocket_type" name="pocket_type_id" class="form-select select2" data-placeholder="Select Pocket Type">
                                            <option value="">Select Pocket Type</option>
                                            @foreach($pocketTypes as $type)
                                                <option value="{{ $type->id }}" {{ (old('pocket_type_id', $jobCard ? $jobCard->pocket_type_id : '') == $type->id) ? 'selected' : '' }}>{{ $type->pocket_type_name }}</option>
                                            @endforeach
                                        </select>
                                        <label for="pocket_type">Pocket Type</label>
                                    </div>
                                    @error('pocket_type_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <select id="bottom_cut" name="bottom_cut_id" class="form-select select2" data-placeholder="Select Bottom Cut">
                                            <option value="">Select Bottom Cut</option>
                                            @foreach($bottomCuts as $type)
                                                <option value="{{ $type->id }}" {{ (old('bottom_cut_id', $jobCard ? $jobCard->bottom_cut_id : '') == $type->id) ? 'selected' : '' }}>{{ $type->bottom_cut_name }}</option>
                                            @endforeach
                                        </select>
                                        <label for="bottom_cut">Bottom Cut</label>
                                    </div>
                                    @error('bottom_cut_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="card-header-box d-flex justify-content-between align-items-center">
                                <h4>Production Stages</h4>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="add-stage-row">
                                    <i class="ri ri-add-line"></i> Add Stage
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table" id="production-stages-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Stage *</th>
                                            <th>Issue Unit (Plant) *</th>
                                            <th>Rate *</th>
                                            <th>Issue Date *</th>
                                            <th>Deadline Date *</th>
                                            <th>Remarks</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $existingStages = old('production_stages', $jobCard ? $jobCard->operations->toArray() : []);
                                        @endphp
                                        @if(!empty($existingStages))
                                            @foreach($existingStages as $index => $stage)
                                                <tr class="stage-row">
                                                    <td>
                                                        <select name="production_stages[{{ $index }}][stage_id]" class="form-select select2 stage-select" data-placeholder="Select Stage">
                                                            <option value="">Select Stage</option>
                                                            @foreach($operationStages as $os)
                                                                <option value="{{ $os->id }}" data-cost="{{ $os->cost }}" {{ ($stage['stage_id'] ?? $stage['operation_stage_id'] ?? '') == $os->id ? 'selected' : '' }}>{{ $os->operation_stage_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('production_stages.' . $index . '.stage_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                                    </td>
                                                    <td>
                                                        <select name="production_stages[{{ $index }}][service_provider_id]" class="form-select select2 provider-select" data-placeholder="Select Unit">
                                                            <option value="">Select Unit</option>
                                                            @foreach($plants as $p)
                                                                <option value="{{ $p->id }}" {{ ($stage['service_provider_id'] ?? '') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('production_stages.' . $index . '.service_provider_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                                    </td>
                                                    <td>
                                                        <input type="number" name="production_stages[{{ $index }}][rate]" class="form-control stage-rate" value="{{ $stage['rate'] ?? '' }}" step="0.01" placeholder="0.00">
                                                        @error('production_stages.' . $index . '.rate') <span class="text-danger small">{{ $message }}</span> @enderror
                                                    </td>
                                                    <td>
                                                        <input type="text" name="production_stages[{{ $index }}][issue_date]" class="form-control issue-date" value="{{ !empty($stage['issue_date']) ? $stage['issue_date'] : (!empty($stage['assigned_date']) ? date('d-m-Y', strtotime($stage['assigned_date'])) : '') }}" placeholder="Enter Issue Date">
                                                        @error('production_stages.' . $index . '.issue_date') <span class="text-danger small">{{ $message }}</span> @enderror
                                                    </td>
                                                    <td>
                                                        <input type="text" name="production_stages[{{ $index }}][deadline_date]" class="form-control deadline-date" value="{{ !empty($stage['deadline_date']) ? date('d-m-Y', strtotime($stage['deadline_date'])) : '' }}" placeholder="Enter Deadline Date">
                                                        @error('production_stages.' . $index . '.deadline_date') <span class="text-danger small">{{ $message }}</span> @enderror
                                                    </td>
                                                    <td>
                                                        <textarea name="production_stages[{{ $index }}][remarks]" class="form-control" placeholder="Enter Remarks">{{ $stage['remarks'] ?? '' }}</textarea>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-danger remove-stage-row"><i class="ri ri-delete-bin-line"></i></button>
                                                        @if($jobCard && (auth()->id() == 1 || auth()->user()->can('assign-task job-card')))
                                                        @php
                                                            $currentStageId = $stage['stage_id'] ?? $stage['operation_stage_id'] ?? null;
                                                            $taskData = $stageTaskStatus[$currentStageId] ?? null;
                                                            $hasTask = !empty($taskData);

                                                            $taskStatus = $taskData['status'] ?? null;
                                                            $taskNo = $taskData['task_no'] ?? null;
                                                            $previousStage = $existingStages[$index - 1] ?? null;
                                                            $previousStageId = $previousStage['stage_id'] ?? $previousStage['operation_stage_id'] ?? null;
                                                            if ($index > 0) {
                                                                $previousTaskAssigned = !empty($stageTaskStatus[$previousStageId]);
                                                            } else {
                                                                $previousTaskAssigned = true;
                                                            }
                                                            $canAssignCurrentStage = $previousTaskAssigned && !$hasTask;
                                                            $buttonText = $hasTask ? 'Assigned Task (Task: ' . $taskNo . ')' : 'Assign Task';

                                                            if (!$previousTaskAssigned) {
                                                                $buttonTitle = 'Previous stage task not assigned';
                                                            } elseif ($hasTask) {
                                                                $buttonTitle = "Task already assigned (Status: $taskStatus)";
                                                            } else {
                                                                $buttonTitle = 'Assign Task';
                                                            }
                                                        @endphp
                                                        <button type="button" class="btn btn-sm btn-outline-primary assign-task-btn ms-1" title="{{ $buttonTitle }}" {{ !$canAssignCurrentStage ? 'disabled' : '' }}><i class="ri ri-task-line"></i> {{ $buttonText }}</button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr class="stage-row">
                                                <td>
                                                    <select name="production_stages[0][stage_id]" class="form-select select2 stage-select" data-placeholder="Select Stage">
                                                        <option value="">Select Stage</option>
                                                        @foreach($operationStages as $os)
                                                            <option value="{{ $os->id }}" data-cost="{{ $os->cost }}">{{ $os->operation_stage_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('production_stages.0.stage_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                                </td>
                                                <td>
                                                    <select name="production_stages[0][service_provider_id]" class="form-select select2 provider-select" data-placeholder="Select Unit">
                                                        <option value="">Select Unit</option>
                                                        @foreach($plants as $p)
                                                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('production_stages.0.service_provider_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                                </td>
                                                <td>
                                                    <input type="number" name="production_stages[0][rate]" class="form-control stage-rate" value="" step="0.01" placeholder="0.00">
                                                    @error('production_stages.0.rate') <span class="text-danger small">{{ $message }}</span> @enderror
                                                </td>
                                                <td>
                                                    <input type="text" name="production_stages[0][issue_date]" class="form-control issue-date" value="" placeholder="Enter Issue Date">
                                                    @error('production_stages.0.issue_date') <span class="text-danger small">{{ $message }}</span> @enderror
                                                </td>
                                                <td>
                                                    <input type="text" name="production_stages[0][deadline_date]" class="form-control deadline-date" value="" placeholder="Enter Deadline Date">
                                                    @error('production_stages.0.deadline_date') <span class="text-danger small">{{ $message }}</span> @enderror
                                                </td>
                                                <td>
                                                    <textarea name="production_stages[0][remarks]" class="form-control" placeholder="Enter Remarks"></textarea>
                                                    @error('production_stages.0.remarks') <span class="text-danger small">{{ $message }}</span> @enderror
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger remove-stage-row"><i class="ri ri-delete-bin-line"></i></button>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="card-header-box">
                                <h4>Cutting Size Ratio</h4>
                            </div>
                            <div class="row g-4 mb-3">
                                <div class="col-md-6 col-xl-4">
                                    <div id="sleeve-instance-manager" class="p-3 border rounded shadow-sm">
                                        <label class="d-block mb-2 fw-bold text-primary">Sleeve Configuration</label>
                                        <div class="d-flex gap-2 mb-3">
                                            <button type="button" class="btn btn-sm btn-outline-primary shadow-sm hover-lift" id="add-fs-instance">
                                                <i class="ri ri-add-line me-1"></i>  ADD F/S
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-info shadow-sm hover-lift" id="add-hs-instance">
                                                <i class="ri ri-add-line me-1"></i>  ADD H/S
                                            </button>
                                        </div>

                                        <div id="sleeve-instance-list" class="d-flex flex-wrap gap-2"></div>

                                        <div id="no-sleeve-msg" class="text-muted small mt-2">
                                            <i class="ri ri-information-line me-1"></i> No sleeves added yet.
                                        </div>
                                    </div>
                                    @error('sleeve_types') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 col-xl-8">
                                    <div id="size-selector-container" class="p-3 border rounded shadow-sm">
                                        <label class="d-block mb-2 fw-bold text-primary">Select Sizes</label>
                                        <div class="d-flex flex-wrap gap-2" id="size-selector">
                                            @foreach(['36','38','40','42','44','46','48','50'] as $sz)
                                                <label class="btn btn-sm btn-outline-primary size-toggle-btn {{ in_array($sz, $sizes) ? 'active' : '' }}" style="cursor: pointer;">
                                                    <input type="checkbox" class="size-checkbox d-none" value="{{ $sz }}" {{ in_array($sz, $sizes) ? 'checked' : '' }}> {{ $sz }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive" id="cutting-size-table-wrapper" style="{{ ($jobCard && $jobCard->size_ratio_id) ? '' : 'display:none;' }}">
                                <table class="table table-bordered text-center align-middle" id="cutting-size-table">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="align-middle">SIZE</th>
                                            <th colspan="{{ count($sizes) }}" class="ratio-header">CUTTING SIZE RATIO</th>
                                        </tr>
                                        <tr class="size-header-row">
                                            @foreach($sizes as $s)
                                                <th class="dynamic-size-head">{{ $s }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $fsInfoLabel = str_contains($processGroupName, 'OTHERS') ? 'QTY - F/S' : 'SIZE';
                                            $sizeRatioDisplay = old('matrix_items_info.fs', $jobCard ? $jobCard->size_ratio_display : '');
                                            $sizeStr = $sizeRatioDisplay ? explode(' - ', $sizeRatioDisplay)[0] : '';
                                        @endphp

                                        {{-- QTY - F/S ROW --}}
                                        <tr class="qty-fs-row">
                                            <td><strong>QTY - F/S</strong></td>
                                            @foreach($sizes as $idx => $s)
                                                @php
                                                    $val = '';
                                                    foreach ($matrixItems as $item) {
                                                        if (($item['size'] ?? '') == $s) {
                                                            $val = $item['qty_fs'] ?? '';
                                                            break;
                                                        }
                                                    }
                                                @endphp
                                                <td>
                                                    <input type="number" name="matrix_items[{{ $idx }}][qty_fs]" class="form-control form-control-sm text-center fw-bold qty-direct-input fs-summary-{{ $s }}" data-type="fs" data-size="{{ $s }}" value="{{ $val ? (int) $val : '' }}" {{ $hasTasks ? 'readonly' : '' }}>
                                                    <input type="hidden" name="matrix_items[{{ $idx }}][size]" value="{{ $s }}">
                                                    <input type="hidden" name="matrix_items[{{ $idx }}][article_no]" value="{{ old("matrix_items.$idx.article_no", $jobCard ? $jobCard->article_no : '') }}">
                                                </td>
                                            @endforeach
                                            <td class=""></td><td class=""></td>
                                            <td><input type="text" name="mark_lay[fs][size]" class="form-control form-control-sm text-center" value="{{ old('mark_lay.fs.size', $jobCard ? $jobCard->mark_lay_fs_size : '') }}"></td>
                                            <td><input type="text" name="mark_lay[fs][mark]" class="form-control form-control-sm text-center" value="{{ old('mark_lay.fs.mark', $jobCard ? $jobCard->mark_lay_fs_mark : '') }}"></td>
                                        </tr>

                                        {{-- INFO ROW (F/S) --}}
                                        <tr class="qty-fs-info-row">
                                            <td><strong>{{ $fsInfoLabel }}</strong></td>
                                            <td colspan="{{ count($sizes) }}">
                                                <input type="text" id="size_ratio_display" name="matrix_items_info[fs]" class="form-control form-control-sm text-center text-muted" value="{{ $sizeStr }}">
                                            </td>
                                            <td class=""></td><td class=""></td><td></td><td></td>
                                        </tr>

                                        {{-- QTY - H/S ROW --}}
                                        <tr class="qty-hs-row">
                                            <td><strong>QTY - H/S</strong></td>
                                            @foreach($sizes as $idx => $s)
                                                @php
                                                $val = '';
                                                foreach ($matrixItems as $item) {
                                                    if (($item['size'] ?? '') == $s) {
                                                        $val = $item['qty_hs'] ?? '';
                                                        break;
                                                    }
                                                }
                                                @endphp
                                                <td>
                                                    <input type="number" name="matrix_items[{{ $idx }}][qty_hs]" class="form-control form-control-sm text-center fw-bold qty-direct-input hs-summary-{{ $s }}" data-type="hs" data-size="{{ $s }}" value="{{ $val ? (int) $val : '' }}" {{ $hasTasks ? 'readonly' : '' }}>
                                                </td>
                                            @endforeach
                                            <td class=""></td><td class=""></td>
                                            <td><input type="text" name="mark_lay[hs][size]" class="form-control form-control-sm text-center" value="{{ old('mark_lay.hs.size', $jobCard ? $jobCard->mark_lay_hs_size : '') }}"></td>
                                            <td><input type="text" name="mark_lay[hs][mark]" class="form-control form-control-sm text-center" value="{{ old('mark_lay.hs.mark', $jobCard ? $jobCard->mark_lay_hs_mark : '') }}"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            {{-- <div class="text-end mt-3" id="trigger-sync-wrapper" style="{{ ($jobCard && $jobCard->size_ratio_id) ? '' : 'display:none;' }}">
                                <button type="button" class="btn btn-primary" id="trigger-sync">
                                    <i class="ri ri-play-circle-line me-1"></i> GO
                                </button>
                            </div> --}} 
                        </div>
                    </div>

                    <div class="card mb-4 {{ $hasPo ? '' : 'd-none' }}" id="fabric-details-card">
                        <div class="card-body">
                            <div class="card-header-box">
                                <h4>Fabric Details</h4>
                                <div id="fabric-details-error" class="text-danger small fw-bold mb-2" style="display: none;"></div>
                                @error('fabric_details') <div class="text-danger small fw-bold mb-2 backend-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered text-center align-middle" id="fabric-details-table">
                                    <thead id="fabric-details-head">
                                        @if(session('error'))
                                            <div class="alert alert-warning alert-dismissible fade show">
                                                {{ session('error') }}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                            </div>
                                        @endif
                                        @if(!empty($fabrics))
                                            <tr>
                                                @foreach($fabrics as $index => $fabric)
                                                    <th colspan="2" class="bg-light">
                                                        <div class="p-2">
                                                            <label class="small text-primary fw-bold">Image</label>
                                                            <div class="d-flex flex-wrap gap-2 mb-2">
                                                                @foreach($existingImages as $img)
                                                                    @if($img->art_no == ($fabric['art_no'] ?? ''))
                                                                        <div class="position-relative" style="width: 80px; height: 80px;">
                                                                            <img src="{{ url('/') }}/{{ $img->image }}" class="img-thumbnail" style="width: 100%; height: 100%; object-fit: cover;">
                                                                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" style="padding: 2px 6px; font-size: 10px;" onclick="deleteImage({{ $img->id }})">
                                                                                <i class="ri ri-close-line"></i>
                                                                            </button>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                                @php
                                                                    $trimmedArtNo = trim((string) ($fabric['art_no'] ?? ''));
                                                                    $grnImage = $grnImageMap[$trimmedArtNo] ?? null;
                                                                @endphp
                                                                @if($grnImage)
                                                                    <div class="d-flex flex-column align-items-center justify-content-center mx-auto" style="width: 80px;">
                                                                        <img src="{{ $grnImage['url'] }}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <input type="file" class="form-control form-control-sm" name="fabric_images[{{ $index }}][]" multiple accept="image/*">
                                                            <small class="text-muted d-block mt-1">Max file size: 2MB. Supported formats: JPG, PNG, JPEG, WEBP, PDF, DOC, DOCX</small>
                                                        </div>
                                                    </th>
                                                @endforeach
                                            </tr>
                                        @endif
                                    </thead>
                                    <tbody id="fabric-details-body">
                                        @if(!empty($fabrics))
                                            <tr>
                                                @foreach($fabrics as $index => $fabric)
                                                    <td class="fw-bold">ART NO</td>
                                                    <td><input type="text" name="fabrics[{{ $index }}][art_no]" class="form-control form-control-sm text-center" value="{{ $fabric['art_no'] ?? '' }}" readonly></td>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                @foreach($fabrics as $index => $fabric)
                                                    <td class="fw-bold">WIDTH</td>
                                                    <td><input type="text" name="fabrics[{{ $index }}][width]" class="form-control form-control-sm text-center" value="{{ $fabric['width'] ?? '' }}"></td>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                @foreach($fabrics as $index => $fabric)
                                                    <td class="fw-bold">ISSUED METERS</td>
                                                    <td><input type="text" name="fabrics[{{ $index }}][mtr]" class="form-control form-control-sm text-center mtr-input" data-art="{{ $fabric['art_no'] ?? '' }}" value="{{ $fabric['mtr'] ?? '' }}" readonly></td>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                @foreach($fabrics as $index => $fabric)
                                                    <td class="fw-bold">IN/OUT</td>
                                                    <td><input type="text" name="fabrics[{{ $index }}][in_out]" class="form-control form-control-sm text-center" value="{{ $fabric['in_out'] ?? 'NO' }}"></td>
                                                @endforeach
                                            </tr>

                                            <tr>
                                                @foreach($fabrics as $index => $fabric)
                                                    <td class="fw-bold">N.PATTI</td>
                                                    <td><input type="text" name="fabrics[{{ $index }}][n_patti]" class="form-control form-control-sm text-center" value="{{ $fabric['n_patti'] ?? 'WHITE' }}"></td>
                                                @endforeach
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4 d-none" id="article-matrix-card">
                        <div class="card-body">
                            <div class="card-header-box mb-3 border-bottom pb-2 align-items-center">
                                <h4 class="mb-0">Article Quantity Matrix</h4>
                                <div id="article-matrix-error" class="text-danger small fw-bold mb-2" style="display: none;"></div>
                                @error('article_matrix') <div class="text-danger small fw-bold mb-2 backend-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered text-center align-middle mb-0 shadow-sm" id="article-qty-matrix">
                                    <thead class="table-light text-uppercase small fw-bold"></thead>
                                    <tbody id="article-qty-matrix-body"></tbody>
                                    <tfoot class="table-light"></tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 text-end mt-5">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="{{ url('job_card_entries') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="processGroupModal" tabindex="-1" aria-labelledby="processGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="processGroupModalLabel">Select Process Group</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered align-middle text-center" id="processGroupTable">
                        <thead class="table-light">
                            <tr>
                                <th>Select</th>
                                <th>Code</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($processGroups as $pg)
                            <tr>
                                <td><input type="radio" name="process_option" value="{{ $pg->id }}" data-name="{{ $pg->name }}" {{ ($jobCard && $jobCard->process_group_id == $pg->id) ? 'checked' : '' }}></td>
                                <td>{{ explode(' - ', $pg->name)[0] }}</td>
                                <td>{{ count(explode(' - ', $pg->name)) > 1 ? explode(' - ', $pg->name)[1] : $pg->name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmProcessGroup">Select</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="stockValidationErrorModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="stockValidationErrorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-2xl" style="border-radius: 24px; overflow: hidden; background: #ffffff;">
                <div class="modal-header border-0 py-4 px-5" style="background: linear-gradient(135deg, #1a1a1a 0%, #3e1a1a 100%); position: relative;">
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at 20% 50%, rgba(255, 82, 82, 0.15), transparent); pointer-events: none;"></div>
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: rgba(255, 82, 82, 0.2); border: 1px solid rgba(255, 82, 82, 0.4); box-shadow: 0 0 15px rgba(255, 82, 82, 0.3);">
                            <i class="ri ri-error-warning-fill text-danger fs-3"></i>
                        </div>
                        <div>
                            <h4 class="modal-title text-white fw-bolder mb-0" id="stockValidationErrorModalLabel" style="letter-spacing: -0.5px;">Stock Discrepancy Detected</h4>
                            <p class="text-white-50 mb-0 small fw-medium">Some materials in your matrix exceed current warehouse stock.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-0">
                    <div class="px-5 pt-4 pb-2">
                        <div class="table-responsive rounded-4" style="border: 1px solid #f0f0f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                            <table class="table table-hover align-middle mb-0" id="stockErrorTable">
                                <thead style="background: #fafafa; border-bottom: 2px solid #f0f0f0;">
                                    <tr>
                                        <th class="py-3 px-4 text-secondary small fw-bold text-uppercase" style="letter-spacing: 1px;">Article & Description</th>
                                        <th class="py-3 text-center text-secondary small fw-bold text-uppercase" style="letter-spacing: 1px;">Required</th>
                                        <th class="py-3 text-center text-secondary small fw-bold text-uppercase" style="letter-spacing: 1px;">Available</th>
                                        <th class="py-3 text-center text-secondary small fw-bold text-uppercase" style="letter-spacing: 1px;">Shortage</th>
                                        <th class="py-3 px-4 text-start text-secondary small fw-bold text-uppercase" style="letter-spacing: 1px;">Stock Analysis</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 px-5 pb-5 pt-0 d-flex justify-content-end gap-3">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none fw-bold px-4 hover-lift" data-bs-dismiss="modal" style="font-size: 0.9rem;">
                        CANCEL
                    </button>

                    <button type="button" class="btn px-4 py-2 fw-bolder d-flex align-items-center justify-content-center gap-2" id="btn-recheck-stock" style="background: #fff; color: #6200ee; border: 1px solid #6200ee; border-radius: 12px; transition: all 0.2s; min-width: 150px;">
                        <i class="ri ri-refresh-line fs-5"></i>
                        RE-CHECK STOCK
                    </button>
                </div>
            </div>
        </div>
    </div>

<style>
    .size-toggle-btn {
        border: 1px solid #dee2e6 !important;
        background-color: #f8f9fa !important;
        color: #495057 !important;
        transition: all 0.2s ease-in-out;
        font-weight: 500;
    }
    .size-toggle-btn.active {
        background-color: #6a1b9a !important;
        border-color: #6a1b9a !important;
        color: #ffffff !important;
        box-shadow: 0 4px 6px rgba(98, 0, 238, 0.2) !important;
    }
    .size-toggle-btn:hover {
        background-color: #e9ecef !important;
        border-color: #ced4da !important;
        color: #212529 !important;
    }
    .size-toggle-btn.active:hover {
        background-color: #5000d6 !important;
        border-color: #5000d6 !important;
        color: #ffffff !important;
    }

    .extra-small { font-size: 0.65rem; }
    .hover-lift:hover { transform: translateY(-2px); color: #333 !important; }
    .hover-glow:hover { box-shadow: 0 8px 25px rgba(98, 0, 238, 0.4) !important; transform: translateY(-1px); opacity: 0.95; }
    #stockErrorTable tbody tr { transition: all 0.2s; border-bottom: 1px solid #f8f9fa; }
    #stockErrorTable tbody tr:hover { background-color: #fbfbfb; }
    .stock-badge { padding: 4px 10px; border-radius: 8px; font-size: 0.85rem; }

    #fabric-details-table td {
        white-space: nowrap;
    }
    #fabric-details-table td.fw-bold {
        min-width: 100px;
        background-color: #f8f9fa;
    }
    .assign-task-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background-color: #e9ecef;
        border-color: #dee2e6;
        color: #6c757d;
    }
    #stock_entry_search[readonly], #generate-matrix-btn:disabled {
        background-color: #ccc !important;
    }
    #fabric-details-table td:not(.fw-bold) {
        min-width: 200px;
    }
    #cutting-size-table td:not(.fw-bold),
    #article-qty-matrix-1 td:not(.fw-bold),
    #article-qty-matrix-2 td:not(.fw-bold) {
        min-width: 100px;
    }
    #cutting-size-table input,
    #article-qty-matrix-1 input,
    #article-qty-matrix-2 input {
        width: 100% !important;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
    $(document).ready(function() {
        const rawOldMatrix = @json(array_values(old('article_matrix', [])));
        const oldMatrix = rawOldMatrix;
        const rawExistingMatrix = @json($jobCard && $jobCard->fabricDetails ? $jobCard->fabricDetails->values() : []);
        const existingMatrix = rawExistingMatrix;
        const validationErrors = @json($errors->toArray());

        const rawOldFabrics = @json(array_values(old('fabrics', [])));
        const oldFabrics = rawOldFabrics;

        const hasTasks = @json($hasTasks);
        const existingImages = @json($jobCard && $jobCard->images ? $jobCard->images : []);
        const grnImageMap = @json($grnImageMap ?? []);
        const matrixItems = @json(array_values(old('matrix_items', $jobCard && $jobCard->cuttingSizeRatios ? $jobCard->cuttingSizeRatios->toArray() : [])));
        const isEditMode = {{ $jobCard ? 'true' : 'false' }};
        let globalActiveSizes = { fs: @json($activeFs), hs: @json($activeHs) };
        let isSyncing = false;
        let currentArtNumbers = [];
        const phpFabrics = @json(array_values($fabrics));
        if (oldFabrics && Object.keys(oldFabrics).length > 0) {
            const uniqueArts = [...new Set(Object.values(oldFabrics).map(f => f.art_no))];
            currentArtNumbers = uniqueArts.filter(Boolean);
        } else if (phpFabrics && phpFabrics.length > 0) {
            currentArtNumbers = [...new Set(phpFabrics.map(f => f.art_no))];
        }
        const articleUoms = @json(collect($fabrics)->pluck('uom_code', 'art_no')) || {};
        let currentArtData = (@json(array_values($fabrics)) || []).map(f => ({
            art_no: f.art_no,
            mtr: parseFloat(f.stock_total_qty) || parseFloat(f.mtr) || 0,
            already_issued: parseFloat(f.mtr) || 0,
            saved_stock_total_qty: f.stock_total_qty || null,
            saved_mtr: f.mtr || null,
            art_name: f.art_no,
            grn_image: f.grn_image || (grnImageMap[String(f.art_no || '').trim()] ? grnImageMap[String(f.art_no || '').trim()].image : null),
            grn_image_url: f.grn_image ? `{{ url('uploads/grn_items') }}/${f.grn_image}` : (grnImageMap[String(f.art_no || '').trim()] ? grnImageMap[String(f.art_no || '').trim()].url : null)
        }));

        let currentSizes = @json($sizes);
        let currentRatios = @json($ratios);
        let currentProcessGroupId = '{{ old("process_group_id", $jobCard ? $jobCard->process_group_id : "") }}';
        let currentProcessGroup = '{{ old("process_group_display", $jobCard && $jobCard->processGroup ? $jobCard->processGroup->name : "") }}';
        let addedStages = [];
        let fsMeterValue = '{{ old("fs_meter", $jobCard ? ($jobCard->sleeveMeters->where("sleeve_type", "Full Sleeve")->first()->meter ?? "") : "") }}';
        let hsMeterValue = '{{ old("hs_meter", $jobCard ? ($jobCard->sleeveMeters->where("sleeve_type", "Half Sleeve")->first()->meter ?? "") : "") }}';


        let sleeveValues = {};
        function captureSleeveValues() {
            $('.qty-direct-input').each(function() {
                const instId = $(this).data('instance');
                const size = $(this).data('size');
                const val = $(this).val();
                if (instId && size) {
                    if (!sleeveValues[instId]) sleeveValues[instId] = {};
                    sleeveValues[instId][size] = val;
                }
            });
        }

        let sleeveInstances = []; 
        let rawSleeveJson = $('#sleeve_instances_json').val();
        try {
            if (rawSleeveJson && rawSleeveJson !== 'null') {
                let parsedJson = JSON.parse(rawSleeveJson);
                if (parsedJson && parsedJson.instances) {
                    sleeveInstances = parsedJson.instances;
                    sleeveValues = parsedJson.values || {};
                }
            }
        } catch (e) {
            console.error('Error parsing sleeve instances', e);
        }

        if (sleeveInstances.length === 0 && matrixItems && matrixItems.length > 0) {
            let hasFs = matrixItems.some(i => (parseFloat(i.qty_fs) || 0) > 0);
            let hasHs = matrixItems.some(i => (parseFloat(i.qty_hs) || 0) > 0);
            if (hasFs) {
                const id = Date.now() + Math.random();
                sleeveInstances.push({ id: id, type: 'fs' });
                matrixItems.forEach(i => {
                    if (!sleeveValues[id]) sleeveValues[id] = {};
                    sleeveValues[id][i.size] = i.qty_fs;
                });
            }
            if (hasHs) {
                const id = Date.now() + Math.random();
                sleeveInstances.push({ id: id, type: 'hs' });
                matrixItems.forEach(i => {
                    if (!sleeveValues[id]) sleeveValues[id] = {};
                    sleeveValues[id][i.size] = i.qty_hs;
                });
            }
        }

        function syncReferenceNo() {
            const jobCardNo = $('#job_card_no').val();
            if(jobCardNo) {
                $('#reference_no').val(jobCardNo);
            }
        }
        $('#job_card_no').on('input', function() {
            $('#reference_no').val($(this).val());
        });
        if(!$('#reference_no').val()) {
            syncReferenceNo();
        }

        function calculateNoOfDays() {
            const issueVal = $('input[name="issue_date"]').val();
            const deliveryVal = $('input[name="delivery_date"]').val();

            const resetNoOfDays = () => {
                $('#no_of_days_display').val('');
                $('#no_of_days').val('');
            };

            if (!issueVal || !deliveryVal) {
                resetNoOfDays();
                return;
            }

            const parseDMY = (str) => {
                const parts = str.split('-');
                if (parts.length !== 3) {
                    return null;
                }

                const date = new Date(parts[2], parts[1] - 1, parts[0]);
                return Number.isNaN(date.getTime()) ? null : date;
            };

            const issueDate = parseDMY(issueVal);
            const deliveryDate = parseDMY(deliveryVal);

            if (!issueDate || !deliveryDate || deliveryDate < issueDate) {
                resetNoOfDays();
                return;
            }

            const diff = Math.round((deliveryDate - issueDate) / (1000 * 60 * 60 * 24));
            $('#no_of_days_display').val(diff);
            $('#no_of_days').val(diff);
        }

        $('input[name="issue_date"]').on('change input', calculateNoOfDays);
        $('input[name="delivery_date"]').on('change input', calculateNoOfDays);
        calculateNoOfDays();

        function performStockCheck(isRecheck = false) {
            const form = $('form.common-form');
            if (form.attr('data-skip-validation') === 'true') return true;

            let isValid = true;
            let errors = [];
            let discrepancies = [];

            const artDataMap = {};

            $('tr.cat1-row, tr.cat2-row').each(function() {
                const artRaw = $(this).data('art');
                if (!artRaw) return;
                const art = String(artRaw).trim();

                if (!artDataMap[art]) {
                    artDataMap[art] = { 
                        default_fs_cons: 0, default_hs_cons: 0, size_wise_cons: {}, 
                        issued: 0, is_mtr: false,
                        cat_id: $(this).hasClass('cat2-row') ? 2 : 1
                    };

                    const $mtrInput = $(`.mtr-input[data-art="${art}"], .mtr-input[data-art="${artRaw}"]`);
                    if ($mtrInput.length) artDataMap[art].issued = parseFloat($mtrInput.val()) || 0;
                }
            });

            $('.pcs-cons-input, .mtr-input, .size-cons-input, .sleeve-qty-input').each(function() {
                const artRaw = $(this).data('art');
                if (!artRaw) return;
                const art = String(artRaw).trim();
                const val = parseFloat($(this).val()) || 0;
                const name = $(this).attr('name');
                const isFs = (name || "").includes('fs_qty') || (name || "").includes('fs_cons');
                const isSizeWise = $(this).hasClass('size-cons-input');
                const size = $(this).data('size'); 

                if (!artDataMap[art]) {
                    artDataMap[art] = { 
                        default_fs_cons: 0, default_hs_cons: 0, size_wise_cons: {}, 
                        issued: 0, is_mtr: false
                    };
                }

                if (isSizeWise) {
                    if (!artDataMap[art].size_wise_cons[size]) artDataMap[art].size_wise_cons[size] = { fs: 0, hs: 0 };
                    if (isFs) artDataMap[art].size_wise_cons[size].fs = val;
                    else artDataMap[art].size_wise_cons[size].hs = val;
                } else {
                    if (isFs) artDataMap[art].default_fs_cons = val;
                    else artDataMap[art].default_hs_cons = val;
                }
            });

            if (currentArtData && currentArtData.length) {                
                const clean = (s) => String(s || "").replace(/[^a-z0-9]/gi, '').toLowerCase();

                for (const art in artDataMap) {
                    const matchArt = String(art).trim().toLowerCase();
                    const fuzzyArt = clean(art);

                    let d = currentArtData.find(item => clean(item.art_no) === fuzzyArt);

                    if (d) {
                        const newStock = parseFloat(d.mtr) || 0;
                        artDataMap[art].issued = newStock;
                        artDataMap[art].total_available = parseFloat(d.total_available) || (newStock + (parseFloat(d.already_issued) || 0));
                        if (d.store_category_id) artDataMap[art].cat_id = d.store_category_id;
                        if (d.raw_material_id) artDataMap[art].mat_id = d.raw_material_id;
                        if (d.grn_no) artDataMap[art].grn_no = d.grn_no;
                        if (d.uom_code) artDataMap[art].is_mtr = (String(d.uom_code).trim().toUpperCase() === 'MTR');
                        artDataMap[art].already_issued = parseFloat(d.already_issued) || 0;

                        $('.mtr-input[data-art]').filter(function() {
                            return clean($(this).data('art')) === fuzzyArt;
                        }).val(newStock);

                        const $itemUsed = $(`.item-used-input`).filter(function() {
                            return clean($(this).data('art')) === fuzzyArt;
                        });

                        if ($itemUsed.length) {
                            const $row = $itemUsed.closest('tr');
                            $row.find('.item-total-qty').text(newStock.toFixed(2));
                            $row.find('.total-qty-hidden').val(newStock.toFixed(2));
                            $itemUsed.trigger('input'); 
                        }
                    }
                }
            }

            for (const art in artDataMap) {
                const data = artDataMap[art];
                let matrixTotal = 0;
                let enteredUsed = 0;
                let calcDetails = "";
                const $matrixRow = $('tr.cat1-row, tr.cat2-row').filter(function() { 
                    const rowArt = String($(this).data('art') || "").trim();
                    return rowArt === art;
                });

                if ($matrixRow.length > 0) {
                    enteredUsed = parseFloat($(`.used-qty-hidden[data-art="${art}"]`).val()) || 0;
                    matrixTotal = parseFloat($matrixRow.find('.qty-input').val()) || parseFloat($matrixRow.find('.row-total').val()) || 0;
                    const catId = data.cat_id;

                    if (catId == 1) { 
                        let lmRequired = 0;
                        const $lmTable = $(`.lay-mark-table[data-art="${art}"]`);
                        if ($lmTable.length) {
                            $lmTable.find('tbody tr.lay-mark-row').each(function() {
                                const mkMeter = parseFloat($(this).find('input[name$="[meter]"]').val()) || 0;
                                const mkLay = parseFloat($(this).find('input[name$="[no_of_lay]"]').val()) || 0;
                                lmRequired += (mkMeter * mkLay);
                            });
                        }

                        if (lmRequired > 0) {
                            matrixTotal = lmRequired;
                            calcDetails = `Calculated from Lay Marks: ${lmRequired.toFixed(2)}`;
                        } else {
                            matrixTotal = enteredUsed; 
                            calcDetails = `Manual Entry (Quantity Issued - Fabric): ${enteredUsed}`;
                        }
                    }
                } else {
                    enteredUsed = parseFloat($(`.item-used-input[data-art="${art}"]`).val()) || 0;
                    const artInfo = currentArtData ? currentArtData.find(d => String(d.art_no).trim() == String(art).trim()) : null;

                    if (artInfo) {
                        let fsCons = parseFloat($(`.pcs-cons-input[data-art="${art}"][name*="[fs_cons]"]`).val());
                        let hsCons = parseFloat($(`.pcs-cons-input[data-art="${art}"][name*="[hs_cons]"]`).val());

                        if (isNaN(fsCons)) fsCons = parseFloat(artInfo.fs_cons) || 0;
                        if (isNaN(hsCons)) hsCons = parseFloat(artInfo.hs_cons) || 0;

                        const tFS = parseFloat($('#total_qty_fs').val()) || 0;
                        const tHS = parseFloat($('#total_qty_hs').val()) || 0;

                        matrixTotal = (tFS * fsCons) + (tHS * hsCons);
                        calcDetails = `Auto-Calculated (${tFS} F/S × ${fsCons} + ${tHS} H/S × ${hsCons}): ${matrixTotal.toFixed(2)}`;
                    } else {
                        matrixTotal = enteredUsed;
                        calcDetails = `Manual Entry: ${enteredUsed}`;
                    }
                }

                const finalMatrixTotal = Math.round(matrixTotal * 1000) / 1000;
                const finalEnteredUsed = Math.round(enteredUsed * 1000) / 1000;
                const finalIssued = Math.round(data.issued * 1000) / 1000;

                if (finalMatrixTotal > finalEnteredUsed) {
                    isValid = false;
                    const $itemUsedInput = $(`.item-used-input[data-art="${art}"]`);
                    const diff = Math.round((finalMatrixTotal - finalEnteredUsed)*100)/100;
                    $itemUsedInput.parent().find('.dynamic-stock-error-container').html(`<i class="ri ri-alert-line me-1"></i>Shortage: ${diff} (Need: ${finalMatrixTotal})`);

                    const $matrixRow = $('tr.cat1-row, tr.cat2-row').filter(function() { 
                        return String($(this).data('art') || "").trim() === String(art).trim();
                    });
                    if ($matrixRow.length) {
                        $matrixRow.find('.row-total').parent().find('.dynamic-stock-error-container').html(`<i class="ri ri-alert-line me-1"></i>Shortage: ${diff}`);
                    }
                }

                if ((finalMatrixTotal - (data.total_available || finalIssued)) >= 0.01) {
                    errors.push({
                        art: art, required: finalMatrixTotal, issued: finalIssued, 
                        total_available: data.total_available || finalIssued,
                        calc: calcDetails,
                        cat: data.cat_id || '', mat_id: data.mat_id || '', grn_no: data.grn_no || '',
                        already_issued: data.already_issued || 0
                    });
                }
            }

            if (errors.length > 0) {
                isValid = false; 
                console.log("Stock Validation Failed (Modal Required)", errors);
                const $tbody = $('#stockErrorTable tbody').empty();
                errors.forEach(err => {
                    const artInfo = currentArtData.find(item => String(item.art_no).trim() === String(err.art).trim());
                    discrepancies.push({
                        art: err.art,
                        name: artInfo ? artInfo.art_name : 'Raw Material/Fabric',
                        needed: err.required,
                        issued: err.issued,
                        total_available: err.total_available,
                        gap: err.required - err.total_available,
                        calc: err.calc,
                        cat: err.cat_id || '',
                        mat: err.mat_id || '',
                        grn: err.grn_no || '',
                        already_issued: err.already_issued || 0
                    });
                });
            }

            if (discrepancies.length > 0) {
                console.group("Stock Discrepancies Detected");
                console.table(discrepancies);
                console.groupEnd();

                const $tbody = $('#stockErrorTable tbody').empty();

                discrepancies.forEach(err => {
                    const searchUrl = `{{ url('stock_entries') }}?material_category=${err.cat}&material=${err.mat}&grn_no=${err.grn}&art_no=${encodeURIComponent(err.art)}`;
                    const shortageFormatted = parseFloat(err.gap).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                    $(`.mtr-input[data-art="${err.art}"]`).val(err.issued);

                    $tbody.append(`
                        <tr class="align-middle border-bottom">
                            <td class="py-4 px-4 text-start">
                                <div class="d-flex flex-column text-start">
                                    <a href="${searchUrl}" target="_blank" class="text-primary fw-bolder fs-5 text-decoration-underline d-inline-flex align-items-center gap-1">
                                        ${err.art}
                                        <i class="ri ri-external-link-line small"></i>
                                    </a>
                                    <span class="extra-small text-secondary fw-bold text-uppercase">${err.name}</span>
                                    ${err.already_issued > 0 ? `<span class="extra-small text-success mt-1" style="font-size: 10px;"><i class="ri ri-check-double-line me-1"></i>Already Issued: ${err.already_issued}</span>` : ''}
                                    <span class="extra-small text-muted mt-1" style="font-size: 10px;">
                                        <i class="ri ri-information-line me-1"></i>Click Art No to view stock entries
                                    </span>
                                </div>
                            </td>
                            <td class="text-center py-4">
                                <div class="d-inline-flex flex-column">
                                    <span class="text-dark fw-bolder fs-5">${parseFloat(err.needed).toLocaleString()}</span>
                                    <span class="extra-small text-secondary fw-bold opacity-50 text-uppercase">Needed</span>
                                </div>
                            </td>
                            <td class="text-center py-4">
                                <div class="d-inline-flex flex-column">
                                    <span class="text-success fw-bolder fs-5">${parseFloat(err.issued).toLocaleString()}</span>
                                    <span class="extra-small text-success fw-bold opacity-50 text-uppercase">In Stock</span>
                                </div>
                            </td>
                            <td class="text-center py-4">
                                <div class="d-inline-flex flex-column">
                                    <span class="text-danger fw-bolder fs-5 shadow-sm px-2 rounded" style="background: rgba(255, 82, 82, 0.1);">${shortageFormatted}</span>
                                    <span class="extra-small text-danger fw-bold opacity-75 text-uppercase">Gap</span>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="small text-secondary text-start" style="max-width: 250px;">${err.calc}</div>
                            </td>
                        </tr>
                    `);
                });

                if (!isRecheck) {
                    new bootstrap.Modal(document.getElementById('stockValidationErrorModal')).show();
                }
                return false;
            } else {
                if (isRecheck) {
                    const modalEl = document.getElementById('stockValidationErrorModal');
                    const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modalInstance.hide();

                    Swal.fire({
                        icon: 'success',
                        title: 'Stock Updated!',
                        text: 'Everything matches! Saving your Job Card automatically...',
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true
                    }).then(() => {
                        $('form.common-form').attr('data-skip-validation', 'true');
                        $('form.common-form')[0].submit();
                    });
                }
                return true;
            }
        }

        function fetchFreshStockData() {
            return new Promise((resolve, reject) => {
                const poId = $('#purchase_order').val() || $('#purchase_order_id').val();
                const jobCardId = '{{ $jobCard ? $jobCard->id : "" }}';

                let selectedEntries = [];
                $('#stock-entry-hidden-inputs input[name="stock_entry_ids[]"]').each(function() {
                    selectedEntries.push($(this).val());
                });

                let url = '';
                let ajaxData = {};

                if (selectedEntries.length > 0) {
                    url = '{{ url("job_card_entries/get-stock-entry-details") }}';
                    ajaxData = { ids: selectedEntries, job_card_id: jobCardId };
                } else if (poId) {
                    url = `{{ url('job_card_entries/check-stock') }}/${poId}`;
                    ajaxData = { job_card_id: jobCardId };
                } else {
                    resolve({ art_data: currentArtData || [] });
                    return;
                }

                $.ajax({
                    url: url,
                    type: 'GET',
                    data: ajaxData,
                    cache: false, 
                    success: function(data) {
                        if (!data || !data.art_data) {
                            reject('Invalid data structure: ' + JSON.stringify(data));
                        } else {
                            resolve(data);
                        }
                    },
                    error: function(xhr) {
                        reject(`AJAX Error: ${xhr.status} - ${xhr.responseText}`);
                    }
                });
            });
        }

        $('form.common-form').on('submit', function(e) {
            if ($(this).attr('data-skip-validation') === 'true') return;

            e.preventDefault(); 
            $('.text-danger.small.fw-bold, .backend-error, .lay-mark-error').hide();
            $('.lay-mark-row .is-invalid').removeClass('is-invalid');

            let hasLayMarkError = false;
            $('.lay-mark-row').each(function() {
                const $row = $(this);
                
                // Validate Sizes
                const $sizesInput = $row.find('.sizes-input');
                if ($sizesInput.length) {
                    const sizesVal = $sizesInput.val();
                    if (!sizesVal || sizesVal.length === 0) {
                        $sizesInput.siblings('.lay-mark-error').show();
                        hasLayMarkError = true;
                    }
                }
                
                // Validate Sleeve
                const $sleeveInput = $row.find('.sleeve-input');
                if ($sleeveInput.length) {
                    if (!$sleeveInput.val()) {
                        $sleeveInput.addClass('is-invalid');
                        $sleeveInput.siblings('.lay-mark-error').show();
                        hasLayMarkError = true;
                    }
                }
                
                // Validate Meter
                const $meterInput = $row.find('.meter-input');
                if ($meterInput.length) {
                    const meterVal = parseFloat($meterInput.val());
                    if (isNaN(meterVal) || meterVal <= 0) {
                        $meterInput.addClass('is-invalid');
                        $meterInput.siblings('.lay-mark-error').show();
                        hasLayMarkError = true;
                    }
                }
                
                // Validate No Of Lay
                const $noOfLayInput = $row.find('.no-of-lay-input');
                if ($noOfLayInput.length) {
                    const layVal = parseFloat($noOfLayInput.val());
                    if (isNaN(layVal) || layVal <= 0) {
                        $noOfLayInput.addClass('is-invalid');
                        $noOfLayInput.siblings('.lay-mark-error').show();
                        hasLayMarkError = true;
                    }
                }
            });

            if (hasLayMarkError) {
                // Focus on the first error element
                $('.lay-mark-error:visible').first().closest('td').find('input, select').focus();
                return false;
            }

            const grandTotal = $('#article-qty-matrix-grand-total').text().trim();
            const total = parseFloat(grandTotal) || 0;

            let missingFabricArtNos = [];
            $('.art-no-input').each(function() {
                const artNo = $(this).val();
                let rowQty = 0;
                $(`.sleeve-qty-input[data-art="${artNo}"]:not(.size-cons-input)`).each(function() {
                    rowQty += parseFloat($(this).val()) || 0;
                });
                if (rowQty <= 0) {
                    missingFabricArtNos.push(artNo);
                }
            });

            const $form = $(this);
            const $btn = $form.find('[type="submit"]');
            const originalHtml = $btn.html();

            const poId = $('#purchase_order').val() || $('#purchase_order_id').val();

            $btn.prop('disabled', true).html('<i class="ri ri-loader-4-line ri-spin"></i> Checking Stock...');

            fetchFreshStockData()
                .then(data => {
                    currentArtData = data.art_data;

                    const isValid = performStockCheck(false); 

                    if (isValid) {
                        $form.attr('data-skip-validation', 'true');
                        $form[0].submit(); 
                    } else {
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                })
                .catch(err => {
                    console.warn('Stock fetch skipped or failed, submitting without validation:', err);
                    $form.attr('data-skip-validation', 'true');
                    $form[0].submit();
                });
        });

        $('#btn-recheck-stock').on('click', function() {
            const $btn = $(this);
            const originalHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="ri ri-loader-4-line ri-spin fs-5"></i> SYNCING...');
            fetchFreshStockData()
                .then(data => {
                    currentArtData = data.art_data;
                    const allClear = performStockCheck(true);
                    if (!allClear) {
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                })
                .catch(err => {
                    alert('Failed to sync: ' + err);
                    $btn.prop('disabled', false).html(originalHtml);
                });
        });

        (function () {
            const $input       = $('#stock_entry_search');
            const $suggestions = $('#stock-entry-suggestions');
            const $tags        = $('#stock-entry-tags');
            const $hiddenWrap  = $('#stock-entry-hidden-inputs');
            const searchUrl    = '{{ url("job_card_entries/search-stock-entries") }}';
            const detailsUrl   = '{{ url("job_card_entries/get-stock-entry-details") }}';

            let selectedIds = [];
            let searchTimer = null;

            function syncSelectedIdsFromDom() {
                selectedIds = [];
                $hiddenWrap.find('input[name="stock_entry_ids[]"]').each(function () {
                    const v = String($(this).val());
                    if (v && !selectedIds.includes(v)) selectedIds.push(v);
                });
            }

            function addTag(id, text) {
                id = String(id);
                if (selectedIds.includes(id)) return;
                selectedIds.push(id);
                $hiddenWrap.append(`<input type="hidden" name="stock_entry_ids[]" value="${id}">`);

                const removeButton = hasTasks ? '' : `<button type="button" class="btn-close btn-close-white ms-1" style="font-size:8px;" data-remove="${id}" title="Remove"></button>`;
                const $tag = $(`
                    <span class="badge bg-primary d-inline-flex align-items-center gap-1 px-2 py-1 fs-6" data-id="${id}" style="cursor:default; max-width:300px; white-space:normal; text-align:left;">
                        <span style="font-size:11px; line-height:1.3;">${text}</span>
                        ${removeButton}
                    </span>`);
                $tags.append($tag);
                refreshMatrixFromIds();
            }

            function removeTag(id) {
                id = String(id);
                selectedIds = selectedIds.filter(x => x !== id);
                $hiddenWrap.find(`input[value="${id}"]`).remove();
                $tags.find(`[data-id="${id}"]`).remove();
                refreshMatrixFromIds();
            }


            function refreshMatrixFromIds() {
                if (selectedIds.length === 0) {
                    $('#fabric-validation-error').hide();
                    $('#fabric-details-card').addClass('d-none');
                    $('#item-details-table-wrapper').addClass('d-none');
                    $('#no-materials-msg').removeClass('d-none');
                    $('#article-matrix-card').addClass('d-none');
                    return;
                }

                const jobCardId = '{{ $jobCard ? $jobCard->id : "" }}';
                $.get(detailsUrl, { ids: selectedIds, job_card_id: jobCardId }, function (data) {
                    currentArtNumbers = data.art_numbers;
                    currentArtData    = data.art_data;

                    let hasFabric = false;
                    if (data.art_data) {
                        data.art_data.forEach(d => { 
                            articleUoms[d.art_no] = d.uom_code; 
                            if (d.store_category_id == 1) {
                                hasFabric = true;
                                if (d.fabric_type_id && !$('#fabric_type_id').val()) {
                                    $('#fabric_type_id').val(d.fabric_type_id);
                                }
                            }
                        });
                    }

                    let selectedBrandText = $('#brand option:selected').text().toUpperCase().trim();
                    let isCanvas = selectedBrandText === 'CANVAS ACCESSORIES' || selectedBrandText === 'CANVAS ACCESSORIES (CAS)';

                    if (!hasFabric && !isCanvas) {
                        $('#fabric-validation-error').hide();
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: 'Please select at least one Fabric (Store Category: Fabric) to proceed.',
                            confirmButtonColor: '#6200ee'
                        });
                        $('#fabric-details-card').addClass('d-none');
                        $('#article-matrix-card').addClass('d-none');
                        return;
                    }

                    $('#fabric-validation-error').hide();

                    $('#fabric-details-card').removeClass('d-none');
                    renderFabricDetails();
                    if (typeof renderItemDetailsTable === "function") {
                        renderItemDetailsTable(currentArtData);
                    }

                    if (typeof globalActiveSizes !== 'undefined') {
                        renderArticleQtyMatrix(data.art_numbers, globalActiveSizes.fs || [], globalActiveSizes.hs || []);
                    } else {
                        renderArticleQtyMatrix(data.art_numbers);
                    }

                    renderCuttingSizeTable(currentSizes, currentRatios);
                    if (typeof syncMatrixWithMasterTable === 'function') {
                        syncMatrixWithMasterTable(false);
                    }
                    updateQuantityRowVisibility();

                });
            }

            var $ac = $input.autocomplete({
                disabled: hasTasks,
                source: function (request, response) {
                    $.get(searchUrl, { q: request.term, brand_id: $('#brand').val() }, function (data) {
                        const mappedResults = data.results.map(function(item) {
                            return {
                                label: item.text,
                                value: item.text, 
                                id: item.id,      
                                se_no: item.se_no,
                                art_no: item.art_no,
                                name: item.name,
                                qty: item.qty
                            };
                        });
                        response(mappedResults);
                    });
                },
                minLength: 1, 
                select: function (event, ui) {
                    event.preventDefault(); 
                    addTag(ui.item.id, ui.item.label);
                    $input.val(''); 
                    return false;
                },
                focus: function(event, ui) {
                    event.preventDefault(); 
                    return false;
                }
            });

            var acInstance = $ac.data("ui-autocomplete") || $ac.data("autocomplete");
            if (acInstance) {
                acInstance._renderItem = function(ul, item) {
                    return $("<li>")
                        .append(`
                            <div class="d-flex justify-content-between align-items-center p-2 border autocomplete-custom-item mb-2 mx-2 rounded" style="cursor: pointer; transition: background-color 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.02); background: #fff;">
                                <div>
                                    <div class="fw-bold text-primary mb-1" style="font-size: 13px;">${item.name} <span class="text-secondary ml-1">[${item.art_no}]</span></div>
                                    <div class="small text-muted" style="font-size: 11px;"><i class="ri-hashtag"></i> ${item.se_no}</div>
                                </div>
                                <div class="badge bg-success-subtle text-success px-2 py-1" style="font-size: 11px;">
                                    ${item.qty}
                                </div>
                            </div>
                        `).appendTo(ul);
                };
            }

            $tags.on('click', '[data-remove]', function () {
                removeTag($(this).data('remove'));
            });

            syncSelectedIdsFromDom();
            if (selectedIds.length > 0) {
                $('#generate-matrix-btn').prop('disabled', false);
            }
            if (window._initialStockEntries && window._initialStockEntries.length > 0) {
                window._initialStockEntries.forEach(function (entry) {
                    if (!selectedIds.includes(String(entry.id))) {
                        selectedIds.push(String(entry.id));
                    }
                    if ($tags.find(`[data-id="${entry.id}"]`).length === 0) {
                        const removeButton = hasTasks ? '' : `<button type="button" class="btn-close btn-close-white ms-1" style="font-size:8px;" data-remove="${entry.id}" title="Remove"></button>`;
                        const $tag = $(`
                            <span class="badge bg-primary d-inline-flex align-items-center gap-1 px-2 py-1 fs-6"
                                    data-id="${entry.id}" style="cursor:default; max-width:300px; white-space:normal; text-align:left;">
                                <span style="font-size:11px; line-height:1.3;">${entry.text}</span>
                                ${removeButton}
                            </span>`);
                        $tags.append($tag);
                    }
                });
            }

            if (selectedIds.length > 0) {
                const jobCardId = '{{ $jobCard ? $jobCard->id : "" }}';
                $.get(detailsUrl, { ids: selectedIds, job_card_id: jobCardId }, function (data) {
                    currentArtNumbers = data.art_numbers;
                    currentArtData    = data.art_data;

                    if (typeof renderItemDetailsTable === "function") {
                        renderItemDetailsTable(currentArtData);
                    }

                    const isRestoredFromOld = (oldFabrics && Object.keys(oldFabrics).length > 0);

                    if (isRestoredFromOld) {
                        renderFabricDetails();
                        renderArticleQtyMatrix(currentArtNumbers, globalActiveSizes.fs, globalActiveSizes.hs);
                        calculateMatrixFromLayMarks();
                        updateQuantityRowVisibility();
                        calculateMatrixTotals(true);
                        if (typeof renderItemDetailsTable === "function") {
                            renderItemDetailsTable(currentArtData);
                        }
                        renderCuttingSizeTable(currentSizes, currentRatios);
                    } else if (window._initialStockEntries && window._initialStockEntries.length > 0) {
                        renderFabricDetails();
                        renderArticleQtyMatrix(currentArtNumbers, globalActiveSizes.fs, globalActiveSizes.hs);
                        calculateMatrixFromLayMarks();
                        updateQuantityRowVisibility();
                        calculateMatrixTotals(true);
                        renderCuttingSizeTable(currentSizes, currentRatios);
                    } else {
                        if (!isEditMode && $('#fabric-details-body tr').length === 0) {
                            $('#fabric-details-card').removeClass('d-none');
                            renderFabricDetails();
                            renderCuttingSizeTable(currentSizes, currentRatios);
                            updateQuantityRowVisibility();
                        } else {
                            renderFabricDetails(); 
                            renderCuttingSizeTable(currentSizes, currentRatios); 
                            syncMatrixWithMasterTable(false);
                            renderArticleQtyMatrix(currentArtNumbers, globalActiveSizes.fs, globalActiveSizes.hs);
                            calculateMatrixFromLayMarks();
                            updateQuantityRowVisibility();
                        }
                    }
                });
            }
        })();

        const initialPoId = $('#purchase_order').val();
        if (initialPoId) {
            $.get(`{{ url('job_card_entries/get-po-details') }}/${initialPoId}`, function(data) {
                currentArtNumbers = data.art_numbers;
                currentArtData = data.art_data;

                if (!isEditMode && $('#fabric-details-body tr').length === 0) {
                    $('#fabric-details-card').removeClass('d-none');
                    renderFabricDetails();
                    if (typeof renderItemDetailsTable === "function") {
                        renderItemDetailsTable(currentArtData);
                    }
                    renderCuttingSizeTable(currentSizes, currentRatios);
                    updateQuantityRowVisibility();
                } else {
                    renderFabricDetails(); 
                    if (typeof renderItemDetailsTable === "function") {
                        renderItemDetailsTable(currentArtData);
                    }
                    renderCuttingSizeTable(currentSizes, currentRatios); 
                    syncMatrixWithMasterTable(true);
                    renderArticleQtyMatrix(currentArtNumbers, globalActiveSizes.fs, globalActiveSizes.hs);
                    calculateMatrixFromLayMarks();
                    updateQuantityRowVisibility();
                }
            });
        }

        function renderArticleQtyMatrix(artNumbers, activeFsSizes = [], activeHsSizes = []) {
            activeFsSizes = [...new Set(activeFsSizes)];
            activeHsSizes = [...new Set(activeHsSizes)];
            const $table = $('#article-qty-matrix');
            const $thead = $table.find('thead');
            const $tbody = $('#article-qty-matrix-body');
            const $tfoot = $table.find('tfoot');

            const capturedMatrix = {};
            $tbody.find('tr').each(function() {
                const art = String($(this).data('art') || "").trim();
                if (art) {
                    capturedMatrix[art] = {};
                    $(this).find('.qty-input').each(function() {
                        const col = $(this).data('col');
                        if (col) {
                            capturedMatrix[art][col.replace('-', '_')] = $(this).val();
                        }
                    });
                }
            });

            $thead.empty();
            $tbody.empty();
            $tfoot.empty();

            if (!artNumbers || artNumbers.length === 0) return;

            let selectedBrandText = $('#brand option:selected').text().toUpperCase().trim();
            let isCanvas = selectedBrandText === 'CANVAS ACCESSORIES' || selectedBrandText === 'CANVAS ACCESSORIES (CAS)';

            const headHtml = isCanvas ? `
                <tr class="size-headers">
                    <th class="align-middle" style="min-width: 150px;">ART NO / MATERIAL</th>
                    ${activeFsSizes.map(s => `<th class="mat-fs-head">${s}</th>`).join('')}
                    <th class="align-middle">TOTAL</th>
                </tr>` : `
                <tr>
                    <th rowspan="2" class="align-middle" style="min-width: 150px;">ART NO / MATERIAL</th>
                    ${activeFsSizes.length > 0 ? `<th colspan="${activeFsSizes.length}">F/S</th>` : ''}
                    ${activeHsSizes.length > 0 ? `<th colspan="${activeHsSizes.length}">H/S</th>` : ''}
                    <th rowspan="2" class="align-middle">TOTAL</th>
                </tr>
                <tr class="size-headers">
                    ${activeFsSizes.map(s => `<th class="mat-fs-head">${s}</th>`).join('')}
                    ${activeHsSizes.map(s => `<th class="mat-hs-head">${s}</th>`).join('')}
                </tr>`;
            $thead.append(headHtml);

            artNumbers.forEach((art, index) => {
                art = String(art).trim();
                const existingRow = isEditMode && existingMatrix.length > 0 ? existingMatrix.find(r => String(r.art_no).split('|')[0].trim() == String(art).split('|')[0].trim()) : null;

                let oldRow = null;
                if (oldMatrix && oldMatrix.length > 0) {
                    oldRow = Object.values(oldMatrix).find(r => String(r.art_no).split('|')[0].trim() == String(art).split('|')[0].trim());
                }

                let uom = (articleUoms[art] || 'PCS').toUpperCase();
                let artName = '';
                let catId = 1;
                let actualArt = art;

                if (currentArtData && currentArtData.length > 0) {
                    const d = currentArtData.find(d => String(d.art_no).trim() == String(art).trim());
                    if (d) {
                        artName = d.art_name || '';
                        uom = (d.uom_code || uom).toUpperCase();
                        catId = d.store_category_id || 1;
                        if (d.actual_art_no) actualArt = d.actual_art_no;
                    }
                }

                let artWidth = '';
                const $liveWidthInput = $(`input[name="fabrics[${index}][width]"]`);
                const oldF = (oldFabrics && Object.keys(oldFabrics).length > 0) 
                    ? Object.values(oldFabrics).find(f => String(f.art_no).split('|')[0].trim() == String(art).split('|')[0].trim()) 
                    : null;

                if ($liveWidthInput.length && $liveWidthInput.val()) {
                    artWidth = $liveWidthInput.val();
                } else if (oldF && oldF.width) {
                    artWidth = oldF.width;
                } else if (existingRow && existingRow.width) {
                    artWidth = existingRow.width;
                }
                const widthDisplay = artWidth ? artWidth : '-';

                const isFabric = (catId == 1);
                if (!isFabric && !isCanvas) return;

                let hasAutoCons = false;
                if (currentArtData && currentArtData.length > 0) {
                    const d = currentArtData.find(d => String(d.art_no).trim() == String(art).trim());
                    if (d && !isFabric && (d.fs_cons != null || d.hs_cons != null)) {
                        hasAutoCons = true;
                    }
                }

                const isTaskReadOnly = hasTasks ? 'readonly tabindex="-1"' : '';
                const readonlyAttr = (!isFabric && !hasAutoCons && !isCanvas) ? '' : ((isFabric || isCanvas) ? isTaskReadOnly : 'readonly tabindex="-1"');
                const rowClass = (isFabric || isCanvas) ? 'cat1-row' : (hasAutoCons ? 'cat2-row-auto' : 'cat2-row-manual');
                const styleAttr = (isFabric || hasAutoCons || isCanvas) ? '' : 'style="display: none;"';

                let rowHtml = `<tr class="${rowClass}" data-uom="${uom}" data-art="${art}" data-category="${catId}" data-index="${index}" ${styleAttr}>
                                <td>
                                    <div class="border rounded p-1 mb-1 text-center fw-bold small" style="background: #f8f9fa;">${actualArt}</div>
                                    <input type="hidden" name="article_matrix[${index}][art_no]" value="${art}">
                                    <div class="small text-muted text-center" style="font-size: 10px; line-height: 1.1;">${artName}</div>
                                </td>`;

                activeFsSizes.forEach(s => {
                    let fsVal = '';
                    const key = `fs_${s}`;
                    if (capturedMatrix[art] && capturedMatrix[art][key] !== undefined) {
                        fsVal = capturedMatrix[art][key];
                    } else if (oldRow && oldRow[key] !== undefined) {
                        fsVal = oldRow[key];
                    } else if (existingRow && existingRow.quantities) {
                        const q = existingRow.quantities.find(q => String(q.size) === String(s));
                        fsVal = (q && q.qty_fs != null) ? parseFloat(q.qty_fs) : '';
                    }
                    rowHtml += `<td><input type="number" name="article_matrix[${index}][fs_${s}]" class="form-control form-control-sm qty-input text-center" data-col="fs-${s}" data-art="${art}" value="${fsVal}" ${readonlyAttr} placeholder="0"></td>`;
                });

                activeHsSizes.forEach(s => {
                    let hsVal = '';
                    const key = `hs_${s}`;
                    if (capturedMatrix[art] && capturedMatrix[art][key] !== undefined) {
                        hsVal = capturedMatrix[art][key];
                    } else if (oldRow && oldRow[key] !== undefined) {
                        hsVal = oldRow[key];
                    } else if (existingRow && existingRow.quantities) {
                        const q = existingRow.quantities.find(q => String(q.size) === String(s));
                        hsVal = (q && q.qty_hs != null) ? parseFloat(q.qty_hs) : '';
                    }
                    rowHtml += `<td><input type="number" name="article_matrix[${index}][hs_${s}]" class="form-control form-control-sm qty-input text-center" data-col="hs-${s}" data-art="${art}" value="${hsVal}" ${readonlyAttr} placeholder="0"></td>`;
;
                });

                rowHtml += `<td><input type="text" class="form-control form-control-sm row-total text-center fw-bold" readonly tabindex="-1"></td></tr>`;
                $tbody.append(rowHtml);
            });

            const footHtml = `
                <tr>
                    <td class="fw-bold text-center small">CUTTING TOTAL (PCS)</td>
                    ${activeFsSizes.map(s => `<td><div class="col-total text-center fw-bold small py-1 border rounded" data-col="fs-${s}" style="min-height: 30px;"></div></td>`).join('')}
                    ${activeHsSizes.map(s => `<td><div class="col-total text-center fw-bold small py-1 border rounded" data-col="hs-${s}" style="min-height: 30px;"></div></td>`).join('')}
                    <td><div id="article-qty-matrix-grand-total" class="grand-total text-center fw-bold py-1 border rounded small" style="min-height: 30px;"></div></td>
                </tr>`;
            $tfoot.append(footHtml);

            calculateMatrixTotals(true);
        }

        $(document).on('input', '.qty-input', function() {
            if (isSyncing) return;
            const $el = $(this);
            const $row = $el.closest('tr');
            if ($row.closest('table').is('#article-qty-matrix')) {
                const isCat1 = ($row.attr('data-category') == 1);

                if (isCat1 && $('#article-qty-matrix-body tr.cat1-row').first().is($row)) {
                    const col = $el.data('col');
                    const parts = (col || "").split('-');
                    if (parts.length >= 2) {
                        const type = parts[0];
                        const size = parts[1];
                        const val = parseFloat($el.val()) || 0;
                        const pieces = val; 

                        const $masterInput = $('.qty-direct-input').filter(function() {
                            return $(this).data('size') == size && $(this).data('type') == type;
                        });
                        if ($masterInput.length) {
                            const wasSyncing = isSyncing;
                            isSyncing = true;
                            $masterInput.val(pieces);
                            isSyncing = wasSyncing;
                            syncMatrixWithMasterTable(true, false, type, size);
                            return;
                        }
                    }
                }
                calculateMatrixTotals();
            }
        });

        function calculateMatrixTotals(force = false) {
            if (isSyncing && !force) return;
            let wasSyncing = isSyncing;
            isSyncing = true;

            try {
                const cat1ColSums = {};
                $('#article-qty-matrix-body tr.cat1-row').each(function() {
                    const $row = $(this);
                    let rowTotal = 0;
                    let rowFS = 0;
                    let rowHS = 0;
                    const index = $row.index();

                    $row.find('.qty-input').each(function() {
                        const col = $(this).data('col');
                        const val = parseFloat($(this).val()) || 0;
                        const rowCatId = $row.data('category');

                        let selectedBrandText = $('#brand option:selected').text().toUpperCase().trim();
                        let isCanvas = selectedBrandText === 'CANVAS ACCESSORIES' || selectedBrandText === 'CANVAS ACCESSORIES (CAS)';
                        
                        if (rowCatId == 1 || isCanvas) {
                            cat1ColSums[col] = (cat1ColSums[col] || 0) + val;
                        }

                        if (col.startsWith('fs')) rowFS += val;
                        else if (col.startsWith('hs')) rowHS += val;
                        rowTotal += val;
                    });
                    $row.find('.row-total').val(rowTotal > 0 ? (rowTotal % 1 === 0 ? rowTotal : rowTotal.toFixed(2)) : '-');

                    $(`input[name="fabrics[${index}][fs_qty]"]`).val(rowFS > 0 ? rowFS : '');
                    $(`input[name="fabrics[${index}][hs_qty]"]`).val(rowHS > 0 ? rowHS : '');
                });

                $('#article-qty-matrix-body tr.cat2-row-auto').each(function() {
                    const $row = $(this);
                    const art = $row.data('art');
                    let rowTotal = 0;

                    $row.find('.qty-input').each(function() {
                        const col = $(this).data('col') || '';
                        const type = col.startsWith('fs') ? 'fs' : 'hs';
                        const size = col.replace(`${type}-`, '');

                        const fabricQty = cat1ColSums[col] || 0;
                        const cons = getConsumptionValue(art, type, size);
                        const calculatedVal = fabricQty * cons;

                        $(this).val(calculatedVal > 0 ? (calculatedVal % 1 === 0 ? calculatedVal : calculatedVal.toFixed(2)) : '');
                        rowTotal += calculatedVal;
                    });
                    $row.find('.row-total').val(rowTotal > 0 ? (rowTotal % 1 === 0 ? rowTotal : rowTotal.toFixed(2)) : '-');
                });

                if (currentArtData && currentArtData.length > 0) {
                    renderItemDetailsTable(currentArtData);
                }

                let globalFS = 0;
                let globalHS = 0;
                $('#article-qty-matrix-body tr.cat1-row').each(function() {
                    $(this).find('.qty-input').each(function() {
                        const col = $(this).data('col') || '';
                        const val = parseFloat($(this).val()) || 0;
                        if (col.startsWith('fs')) globalFS += val;
                        else if (col.startsWith('hs')) globalHS += val;
                    });
                });
                $('#total_qty_fs').val(globalFS);
                $('#total_qty_hs').val(globalHS);

                const $table = $('#article-qty-matrix');
                let totalFS = 0;
                let totalHS = 0;
                let grandTotal = 0;

                $table.find('.col-total').each(function() {
                    const col = $(this).data('col');
                    let sum = 0;
                    sum = cat1ColSums[col] || 0;

                    $(this).text(sum > 0 ? (sum % 1 === 0 ? sum : sum.toFixed(2)) : '0'); 

                    if (col.startsWith('fs')) totalFS += sum;
                    else if (col.startsWith('hs')) totalHS += sum;
                    grandTotal += sum;
                });

                $('#article-qty-matrix-grand-total').text(grandTotal > 0 ? (grandTotal % 1 === 0 ? grandTotal : grandTotal.toFixed(2)) : '0');

                $('#total_qty_fs').val(totalFS > 0 ? Math.round(totalFS) : '');
                $('#total_qty_hs').val(totalHS > 0 ? Math.round(totalHS) : '');
                $('.total-summary-fs').text(totalFS > 0 ? Math.round(totalFS) : '0');
                $('.total-summary-hs').text(totalHS > 0 ? Math.round(totalHS) : '0');

                $('.requirement-display').each(function() {
                    const art = $(this).data('art');
                    const artInfo = currentArtData ? currentArtData.find(d => String(d.art_no).trim() == String(art).trim()) : null;
                    const catId = artInfo ? (artInfo.store_category_id || 0) : 0;

                    let lmRequired = 0;
                    const $lmTable = $(`.lay-mark-table[data-art="${art}"]`);
                    if ($lmTable.length) {
                        $lmTable.find('tbody tr.lay-mark-row').each(function() {
                            const mkMeter = parseFloat($(this).find('input[name$="[meter]"]').val()) || 0;
                            const mkLay = parseFloat($(this).find('input[name$="[no_of_lay]"]').val()) || 0;
                            lmRequired += (mkMeter * mkLay);
                        });
                    }
                    let requirementText = '-';
                    if (catId == 1) { 
                        requirementText = lmRequired > 0 ? lmRequired.toFixed(2) : '-';
                    } else {
                        const fsCons = parseFloat($(`.pcs-cons-input[data-art="${art}"][name*="[fs_cons]"]`).val()) || 0;
                        const hsCons = parseFloat($(`.pcs-cons-input[data-art="${art}"][name*="[hs_cons]"]`).val()) || 0;
                        const tFS = parseFloat($('#total_qty_fs').val()) || 0;
                        const tHS = parseFloat($('#total_qty_hs').val()) || 0;
                        const accReq = (tFS * fsCons) + (tHS * hsCons);
                        requirementText = accReq > 0 ? accReq.toFixed(2) : '-';
                    }

                    $(this).find('.req-val').text(requirementText);
                });

            } finally {
                if (!wasSyncing) isSyncing = false;
            }
        }

        function getArtNumbers() {
            const artNos = [];
            $('#fabric-details-body input[name*="[art_no]"]').each(function() {
                const val = $(this).val();
                if (val && !artNos.includes(val)) {
                    artNos.push(val);
                }
            });
            return artNos;
        }

        if ($('#article-qty-matrix-body tr').length > 0) {
            calculateMatrixTotals();
        }

        if (currentArtNumbers && currentArtNumbers.length > 0) {
            $('#fabric-details-card').removeClass('d-none');
            renderFabricDetails();
            if (typeof renderItemDetailsTable === "function") {
                renderItemDetailsTable(currentArtData);
            }
        }

        renderSleeveInstanceList();
        renderCuttingSizeTable(currentSizes, currentRatios);
        syncMatrixWithMasterTable(true); 
        updateQuantityRowVisibility();

        if (sleeveInstances && sleeveInstances.length > 0) {
            $('#article-matrix-card').removeClass('d-none');
        }

        $('#processGroupTable tbody tr').on('click', function() {
            $(this).find('input[type="radio"]').prop('checked', true);
        });

        $('#confirmProcessGroup').click(function() {
            const selected = $('input[name="process_option"]:checked');
            if (selected.length) {
                currentProcessGroupId = selected.val();
                currentProcessGroup = selected.data('name');
                $('#process_group_display').val(currentProcessGroup);
                $('#process_group_id').val(currentProcessGroupId);
                renderCuttingSizeTable(currentSizes, currentRatios);
                updateQuantityRowVisibility();
                syncMatrixWithMasterTable(false);
                $('.qty-input').first().trigger('input'); 
                bootstrap.Modal.getInstance(document.getElementById('processGroupModal')).hide();
            }
        });

        $('#add-fs-instance').on('click', function() { addSleeveInstance('fs'); });
        $('#add-hs-instance').on('click', function() { addSleeveInstance('hs'); });

        $(document).on('change', '.size-checkbox', function() {
            if (sleeveInstances.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sleeve Configuration Required',
                    text: 'Please add a sleeve configuration (F/S or H/S) first.',
                    confirmButtonColor: '#6200ee'
                });
                $(this).prop('checked', false);
                $(this).closest('label').removeClass('active');
                return;
            }

            const size = $(this).val();
            const checked = $(this).is(':checked');
            
            $(this).closest('label').toggleClass('active', checked);

            if (checked) {
                if (!currentSizes.includes(size)) {
                    currentSizes.push(size);
                }
            } else {
                currentSizes = currentSizes.filter(s => s !== size);
            }

            currentSizes.sort((a, b) => {
                const numA = parseFloat(a);
                const numB = parseFloat(b);
                if (!isNaN(numA) && !isNaN(numB)) return numA - numB;
                return String(a).localeCompare(String(b));
            });

            renderCuttingSizeTable(currentSizes, currentRatios);
            syncMatrixWithMasterTable(true);
        });

        function addSleeveInstance(type) {
            captureSleeveValues();
            const id = Date.now() + Math.random();

            sleeveValues[id] = {};
            currentSizes.forEach(size => {
                sleeveValues[id][size] = '';
            });

            sleeveInstances.push({ id, type });
            updateSleeveJson();
            renderSleeveInstanceList();
            renderCuttingSizeTable(currentSizes, currentRatios);
            syncMatrixWithMasterTable(true);
            updateQuantityRowVisibility();
        }

        function updateSleeveJson() {
            captureSleeveValues();
            const payload = {
                instances: sleeveInstances,
                values: sleeveValues
            };
            $('#sleeve_instances_json').val(JSON.stringify(payload));
        }

        $(document).on('click', '.remove-sleeve-instance', function() {
            captureSleeveValues();
            const id = $(this).data('instance-id');
            sleeveInstances = sleeveInstances.filter(i => i.id != id);
            if (sleeveValues[id]) delete sleeveValues[id];
            updateSleeveJson();
            renderSleeveInstanceList();
            renderCuttingSizeTable(currentSizes, currentRatios);
            syncMatrixWithMasterTable(true);
            updateQuantityRowVisibility();
        });

        function renderSleeveInstanceList() {
            const $list = $('#sleeve-instance-list');
            const $msg = $('#no-sleeve-msg');
            $list.empty();

            if (sleeveInstances.length === 0) {
                $msg.show();
                $('#cutting-size-table-wrapper').hide();
                $('#trigger-sync-wrapper').hide();
                $('#article-matrix-card').addClass('d-none');
                return;
            }

            $msg.hide();
            $('#cutting-size-table-wrapper').show();
            $('#trigger-sync-wrapper').show();
            $('#article-matrix-card').removeClass('d-none');

            sleeveInstances.forEach((inst, idx) => {
                const label = inst.type === 'fs' ? 'F/S row' : 'H/S row';
                const colorClass = inst.type === 'fs' ? 'bg-primary' : 'bg-info';
                const badge = $(`
                    <span class="badge ${colorClass} d-inline-flex align-items-center gap-1 px-2 py-1 fs-6">
                        ${label} ${idx + 1}
                        <i class="ri ri-close-line ms-1 cursor-pointer remove-sleeve-instance" data-instance-id="${inst.id}" title="Remove"></i>
                    </span>
                `);
                $list.append(badge);
            });
        }

        renderCuttingSizeTable(currentSizes, currentRatios);
        function renderArtImagesHtml(art) {
            let html = '<div class="d-flex flex-wrap gap-2 mb-2">';
            let hasImage = false;
            const trimmedArt = String(art || '').trim();

            if (existingImages.length > 0) {
                existingImages.forEach(img => {
                    if (String(img.art_no || '').trim() === trimmedArt) {
                        hasImage = true;
                        html += `
                            <div class="position-relative" style="width: 80px; height: 80px;">
                                <img src="{{ url('/') }}/${img.image}" class="img-thumbnail" style="width: 100%; height: 100%; object-fit: cover;">
                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" style="padding: 2px 6px; font-size: 10px;" onclick="deleteImage(${img.id})">
                                    <i class="ri ri-close-line"></i>
                                </button>
                            </div>`;
                    }
                });
            }

            let grnImageUrl = null;
            let grnImageName = null;
            if (currentArtData && currentArtData.length > 0) {
                const artInfo = currentArtData.find(d => String(d.art_no || '').trim() === trimmedArt);
                if (artInfo && artInfo.grn_image_url) {
                    grnImageUrl = artInfo.grn_image_url;
                }
                if (artInfo && artInfo.grn_image) {
                    grnImageName = artInfo.grn_image;
                }
            }

            if (!grnImageUrl && grnImageMap[trimmedArt] && grnImageMap[trimmedArt].url) {
                grnImageUrl = grnImageMap[trimmedArt].url;
            }

            if (!grnImageName && grnImageMap[trimmedArt] && grnImageMap[trimmedArt].image) {
                grnImageName = grnImageMap[trimmedArt].image;
            }

            if (grnImageUrl) {
                hasImage = true;
                html += `
                    <div class="d-flex flex-column align-items-center justify-content-center mx-auto" style="width: 80px;">
                        <img src="${grnImageUrl}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                        <small class="d-block text-muted mt-1">GRN Image</small>
                    </div>`;
            }

            html += '</div>';
            return hasImage ? html : '';
        }

        function renderFabricDetails() {
            const $tbody = $('#fabric-details-body');
            const $thead = $('#fabric-details-head');

            const captured = { mtr: {}, width: {}, in_out: {}, n_patti: {}, consumptions: {}, layMarks: {} };
            $tbody.find('.mtr-input').each(function() {
                const art = $(this).data('art');
                if (art) captured.mtr[art] = $(this).val();
            });
            $tbody.find('.width-input').each(function() {
                const art = $(this).data('art');
                if (art) captured.width[art] = $(this).val();
            });
            $tbody.find('.in-out-input').each(function() {
                const art = $(this).data('art');
                if (art) captured.in_out[art] = $(this).val();
            });
            $tbody.find('.n-patti-input').each(function() {
                const art = $(this).data('art');
                if (art) captured.n_patti[art] = $(this).val();
            });

            $tbody.find('.size-cons-input').each(function() {
                const art = $(this).data('art');
                const size = String($(this).data('size'));
                const type = $(this).data('type'); 
                if (art && size) {
                    if (!captured.consumptions[art]) captured.consumptions[art] = {};
                    if (!captured.consumptions[art][size]) captured.consumptions[art][size] = {};
                    captured.consumptions[art][size][type] = $(this).val();
                }
            });

            $tbody.find('.lay-mark-table').each(function() {
                const art = $(this).data('art');
                if (art) {
                    const marks = [];
                    $(this).find('tbody tr.lay-mark-row').each(function() {
                        marks.push({
                            sizes: $(this).find('.select2-size-multi').val() || [],
                            sleeve_type: $(this).find('select[name*="[sleeve]"]').val(),
                            lay_mark_meter: $(this).find('input[name*="[meter]"]').val(),
                            no_of_lay: $(this).find('input[name*="[no_of_lay]"]').val()
                        });
                    });
                    if (marks.length > 0) captured.layMarks[art] = marks;
                }
            });

            $thead.empty();
            $tbody.empty();

            if (!currentArtNumbers.length) return;
            let headHtml = '<tr>';
            let artRow = '<tr>';
            let widthRow = '<tr>';
            let mtrRow = '<tr>';
            let inOutRow = '<tr>';
            let nPattiRow = '<tr>';
            let sleeveQtyRow = '<tr>';

            let selectedBrandText = $('#brand option:selected').text().toUpperCase().trim();
            let isCanvas = selectedBrandText === 'CANVAS ACCESSORIES' || selectedBrandText === 'CANVAS ACCESSORIES (CAS)';

            currentArtNumbers.forEach((art, index) => {
                let uom = '';
                let catId = 0;
                let artName = '';
                let actualArt = art;

                let sizes = [];
                if (typeof globalActiveSizes !== 'undefined') {
                    sizes = [...new Set([...(globalActiveSizes.fs || []), ...(globalActiveSizes.hs || [])])].sort((a,b) => parseFloat(a)-parseFloat(b) || String(a).localeCompare(String(b)));
                } else if (typeof currentSizes !== 'undefined') {
                    sizes = currentSizes;
                }
                if (currentArtData && currentArtData.length > 0) {
                    const d = currentArtData.find(d => d.art_no == art);
                    if (d) {
                        uom = d.uom_code || '';
                        catId = d.store_category_id || 0;
                        artName = d.art_name || '';
                        if (d.actual_art_no) actualArt = d.actual_art_no;
                    }
                }

                const isAllowed = (catId == 1) || (artName && /BUTTONS|LABEL/i.test(artName)) || isCanvas;
                const currentArtInfo = currentArtData.find(d => String(d.art_no || '').trim() === String(art || '').trim());
                const grnImageName = currentArtInfo?.grn_image || (grnImageMap[String(art || '').trim()] ? grnImageMap[String(art || '').trim()].image : '');
                if (!isAllowed) {
                    let extraHtml = `
                        <input type="hidden" name="fabrics[${index}][store_category_id]" value="${catId}">
                        <input type="hidden" name="fabrics[${index}][art_no]" value="${art}">
                        <input type="hidden" name="fabrics[${index}][stock_entry_id]" value="${currentArtInfo?.stock_entry_id || ''}">
                        <input type="hidden" name="fabrics[${index}][mtr]" class="mtr-input" data-art="${art}" value="${captured.mtr[art] || ''}">
                        <input type="hidden" name="fabrics[${index}][total_qty]" class="total-qty-hidden" data-art="${art}" value="">
                        <input type="hidden" name="fabrics[${index}][grn_image]" value="${grnImageName || ''}">
                    `;
                    $tbody.append(`<tr style="display:none;"><td>${extraHtml}</td></tr>`);
                    return; 
                }

                // let existingImagesHtml = '';
                // if (isEditMode && existingImages.length > 0) {
                //     existingImagesHtml = '<div class="d-flex flex-wrap gap-2 mb-2">';
                //     existingImages.forEach(img => {
                //         if (img.art_no == art) {
                //             existingImagesHtml += `
                //                 <div class="position-relative" style="width: 80px; height: 80px;">
                //                     <img src="{{ url('/') }}/${img.image}" class="img-thumbnail" style="width: 100%; height: 100%; object-fit: cover;">
                //                     <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" style="padding: 2px 6px; font-size: 10px;" onclick="deleteImage(${img.id})">
                //                         <i class="ri ri-close-line"></i>
                //                     </button>
                //                 </div>`;
                //         }
                //     });
                //     existingImagesHtml += '</div>';
                // }
                const existingImagesHtml = renderArtImagesHtml(art);
                headHtml += `<th colspan="2" class="bg-light">
                                <div class="p-2">
                                    <label class="small text-primary fw-bold">Image</label>
                                    ${existingImagesHtml}
                                    <input type="file" class="form-control form-control-sm" name="fabric_images[${index}][]" multiple accept="image/*">
                                    <input type="hidden" name="fabrics[${index}][store_category_id]" value="${catId}">
                                    <input type="hidden" name="fabrics[${index}][fs_qty]" value="">
                                    <input type="hidden" name="fabrics[${index}][hs_qty]" value="">
                                    <input type="hidden" name="fabrics[${index}][total_qty]" class="total-qty-hidden" data-art="${art}" value="">
                                    <input type="hidden" name="fabrics[${index}][used_qty]" class="used-qty-hidden" data-art="${art}" value="">
                                    <input type="hidden" name="fabrics[${index}][remaining_qty]" class="remaining-qty-hidden" data-art="${art}" value="">
                                    <input type="hidden" name="fabrics[${index}][grn_image]" value="${grnImageName || ''}">
                                </div>
                            </th>`;
                            artRow += `<td class="fw-bold">ART NO</td><td>
                                <input type="hidden" name="fabrics[${index}][art_no]" value="${art}">
                                <input type="hidden" name="fabrics[${index}][stock_entry_id]" value="${currentArtInfo?.stock_entry_id || ''}">
                                <input type="text" class="form-control form-control-sm text-center art-no-input" value="${actualArt}" readonly>
                            </td>`;

                let oldF = null;
                if (oldFabrics && Object.keys(oldFabrics).length > 0) {
                    oldF = Object.values(oldFabrics).find(f => String(f.art_no).split('|')[0].trim() == String(art).split('|')[0].trim());
                }

                let vWidth = captured.width[art] || (oldF ? oldF.width : '') || '';
                let vMtr = captured.mtr[art] || (oldF ? (oldF.used_qty !== undefined ? oldF.used_qty : oldF.mtr) : '') || '';
                let vInOut = captured.in_out[art] || (oldF ? oldF.in_out : '') || '';
                let vNPatti = captured.n_patti[art] || (oldF ? oldF.n_patti : '') || '';

                if (!vWidth && currentArtData && currentArtData.length > 0) {
                    const d = currentArtData.find(d => d.art_no == art);
                    if (d && d.width) vWidth = d.width;
                }

                if (!vWidth && existingMatrix.length > 0) {
                    const m = existingMatrix.find(m => String(m.art_no).split('|')[0].trim() == String(art).split('|')[0].trim());
                    if (m) {
                        vWidth = m.width || '';
                        vMtr = m.used_qty !== undefined && m.used_qty !== null ? m.used_qty : (m.mtr || '');
                        vInOut = m.in_out || '';
                        vNPatti = m.n_patti || '';
                    }
                }

                if (!vInOut) vInOut = 'NO';
                if (!vNPatti) vNPatti = 'WHITE';

                if (vMtr !== '' && vMtr !== undefined && vMtr !== null) {
                } else if (currentArtData && currentArtData.length > 0) {
                    const d = currentArtData.find(d => d.art_no == art);
                    if (d) {
                        let totalQty;
                        if (isEditMode && d.saved_stock_total_qty) {
                            totalQty = parseFloat(d.saved_stock_total_qty);
                        } else {
                            totalQty = parseFloat(d.total_available) || 0;
                        }
                        let fallbackMtr = '';
                        if (isEditMode && d.saved_mtr !== null && d.saved_mtr !== undefined) {
                            fallbackMtr = parseFloat(d.saved_mtr).toFixed(2);
                        }
                        if (!fallbackMtr) {
                            fallbackMtr = totalQty.toFixed(2);
                        }
                        vMtr = (d.already_issued !== undefined && d.already_issued !== null && parseFloat(d.already_issued) > 0) 
                            ? d.already_issued 
                            : fallbackMtr;
                    }
                }

                const isTaskReadOnly = hasTasks ? 'readonly' : '';
                widthRow += `<td class="fw-bold">WIDTH</td><td><input type="text" name="fabrics[${index}][width]" class="form-control form-control-sm text-center width-input" data-art="${art}" value="${vWidth}" ${isTaskReadOnly}></td>`;

                /*if (sizes.length > 0) {
                    setTimeout(() => renderConsumptionTable(art, globalActiveSizes, []), 100);
                } */

                mtrRow += `<td class="fw-bold">ISSUED METERS</td>
                    <td>
                        <div class="input-group input-group-sm">
                            <input type="text" name="fabrics[${index}][mtr]" class="form-control form-control-sm text-center mtr-input" data-art="${art}" value="${vMtr}" readonly>
                            <button type="button" class="btn btn-outline-primary sync-mtr-btn" data-art="${art}" title="Sync from Matrix"><i class="ri ri-refresh-line"></i></button>
                        </div>
                        <div class="mt-1 small text-muted requirement-display" data-art="${art}" style="font-size: 10px;">
                            Matrix Need: <span class="req-val">-</span> MTR
                        </div>
                        ${validationErrors[`fabrics.${index}.mtr`] ? `<div class="text-danger small mt-1" style="font-size: 11px;">${validationErrors[`fabrics.${index}.mtr`][0]}</div>` : ''}
                    </td>`;
                inOutRow += `<td class="fw-bold">IN/OUT</td><td><input type="text" name="fabrics[${index}][in_out]" class="form-control form-control-sm text-center in-out-input" data-art="${art}" value="${vInOut}" ${isTaskReadOnly}></td>`;
                nPattiRow += `<td class="fw-bold">N.PATTI</td><td><input type="text" name="fabrics[${index}][n_patti]" class="form-control form-control-sm text-center n-patti-input" data-art="${art}" value="${vNPatti}" ${isTaskReadOnly}></td>`;



                let sizeTableHtml = '';
                if (sizes.length > 0) {
                    if (catId == 1) {
                        sizeTableHtml = `<table class="table table-bordered table-sm mb-0 mt-1 lay-mark-table" id="lay-mark-table-art-${index}" data-art="${art}" style="font-size: 11px;">
                            <thead class="bg-light">
                                <tr class="text-center">
                                    <th style="width: 8%;">MARK</th>
                                    <th style="width: 32%;">SIZE</th>
                                    ${isCanvas ? '' : '<th style="width: 15%;">SLEEVE</th>'}
                                    <th style="width: 20%;">LAY MARK METER</th>
                                    <th style="width: 15%;">No.of Lay</th>
                                    <th style="width: 10%;"><i class="ri ri-settings-4-line"></i></th>
                                </tr>
                            </thead>
                            <tbody>`;
                        let savedLayMarks = captured.layMarks[art] || [];

                        if (savedLayMarks.length === 0 && oldF && oldF.lay_marks) {
                            const rawOld = oldF.lay_marks;
                            const oldEntries = Array.isArray(rawOld) ? rawOld : Object.values(rawOld);
                            savedLayMarks = oldEntries.map(e => ({
                                sizes: e.sizes || [],
                                sleeve_type: e.sleeve || e.sleeve_type || 'F/S',
                                lay_mark_meter: e.meter || e.lay_mark_meter || null,
                                no_of_lay: e.no_of_lay || null
                            }));
                        } else if (isEditMode && existingMatrix.length > 0) {
                            const m = existingMatrix.find(m => String(m.art_no).split('|')[0].trim() == String(art).split('|')[0].trim());
                            if (m && m.lay_marks && m.lay_marks.length > 0) {
                                savedLayMarks = m.lay_marks;
                            } else if (m && m.consumptions && m.consumptions.length > 0) {
                                const fsRows = m.consumptions.filter(c => parseFloat(c.fs_cons) > 0);
                                const hsRows = m.consumptions.filter(c => parseFloat(c.hs_cons) > 0);

                                if (fsRows.length > 0) {
                                    savedLayMarks.push({
                                        sizes: fsRows.map(c => String(c.size)),
                                        sleeve_type: 'F/S',
                                        lay_mark_meter: null,
                                        no_of_lay: null
                                    });
                                }
                                if (hsRows.length > 0) {
                                    savedLayMarks.push({
                                        sizes: hsRows.map(c => String(c.size)),
                                        sleeve_type: 'H/S',
                                        lay_mark_meter: null,
                                        no_of_lay: null
                                    });
                                }
                            }
                        }

                        const rowsToRender = savedLayMarks.length > 0 ? savedLayMarks : [{ sizes: [], sleeve_type: 'F/S', lay_mark_meter: null, no_of_lay: null }];

                        rowsToRender.forEach((lm, lmIndex) => {
                            const savedSizes = lm.sizes ? (Array.isArray(lm.sizes) ? lm.sizes : JSON.parse(lm.sizes)) : [];
                            const savedSleeve = lm.sleeve_type || 'F/S';
                            const savedMeter = (lm.lay_mark_meter !== null && lm.lay_mark_meter !== undefined) ? lm.lay_mark_meter : '';
                    const savedNoOfLay = (lm.no_of_lay !== null && lm.no_of_lay !== undefined) ? lm.no_of_lay : '';

                            sizeTableHtml += `
                                <tr class="lay-mark-row">
                                    <td class="text-center align-middle fw-bold mark-no">${lmIndex + 1}</td>
                                    <td>
                                        <select class="form-select form-select-sm select2-size-multi sizes-input" multiple="multiple" name="fabrics[${index}][lay_marks][${lmIndex}][sizes][]" style="width: 100%;">
                                            ${sizes.map(sz => `<option value="${sz}" ${savedSizes.includes(String(sz)) ? 'selected' : ''}>${sz}</option>`).join('')}
                                        </select>
                                        <div class="text-danger small mt-1 lay-mark-error" style="display:none; text-align: left; font-weight: 500;">This field is required</div>
                                    </td>
                                    ${isCanvas ? `<input type="hidden" name="fabrics[${index}][lay_marks][${lmIndex}][sleeve]" value="F/S">` : `
                                    <td>
                                        <select class="form-select form-select-sm sleeve-input" name="fabrics[${index}][lay_marks][${lmIndex}][sleeve]">
                                            <option value="F/S" ${savedSleeve === 'F/S' ? 'selected' : ''}>F/S</option>
                                            <option value="H/S" ${savedSleeve === 'H/S' ? 'selected' : ''}>H/S</option>
                                        </select>
                                        <div class="text-danger small mt-1 lay-mark-error" style="display:none; text-align: left; font-weight: 500;">This field is required</div>
                                    </td>`}
                                    <td style="padding: 10px; vertical-align: top;">
                                        <div style="display:flex; flex-direction:column; justify-content:center; align-items:center; height:100%;">
                                            <input type="number" step="0.01" min="0.01" class="form-control form-control-sm text-center meter-input" name="fabrics[${index}][lay_marks][${lmIndex}][meter]" placeholder="0.00" value="${savedMeter}" ${isTaskReadOnly}>
                                            <div class="text-danger small mt-1 lay-mark-error" style="display:none; text-align: left; width: 100%; font-weight: 500;">This field is required</div>
                                        </div>
                                    </td>
                                    <td style="padding: 10px; vertical-align: top;">
                                        <div style="display:flex; flex-direction:column; justify-content:center; align-items:center; height:100%;">
                                            <input type="number" step="0.01" min="0.01" class="form-control form-control-sm text-center no-of-lay-input" name="fabrics[${index}][lay_marks][${lmIndex}][no_of_lay]" placeholder="0" value="${savedNoOfLay}" ${isTaskReadOnly}>
                                            <div class="text-danger small mt-1 lay-mark-error" style="display:none; text-align: left; width: 100%; font-weight: 500;">This field is required</div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <button type="button" class="btn btn-sm btn-icon btn-danger remove-lay-mark" ${isTaskReadOnly ? 'disabled' : ''}><i class="ri ri-delete-bin-line"></i></button>
                                    </td>
                                </tr>
                            `;
                        });

                        sizeTableHtml += `</tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-center p-1">
                                        <button type="button" class="btn btn-sm btn-primary add-lay-mark-btn" data-index="${index}" data-sizes='${JSON.stringify(sizes)}' ${isTaskReadOnly ? 'disabled' : ''}><i class="ri ri-add-line"></i> Add Row</button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>`;
                    } else {
                            sizeTableHtml = `<table class="table table-bordered table-sm mb-0 mt-1 accessory-cons-table" data-art="${art}" style="font-size: 11px;">
                            <thead class="bg-light"><tr><th>Size</th><th>F/S Cons</th><th>H/S Cons</th></tr></thead>
                            <tbody>`;

                        sizes.forEach(sz => {
                            let vSzFs = '';
                            let vSzHs = '';

                            if (captured.consumptions[art] && captured.consumptions[art][sz]) {
                                vSzFs = captured.consumptions[art][sz]['fs'] || '';
                                vSzHs = captured.consumptions[art][sz]['hs'] || '';
                            } else if (oldF && oldF['consumptions'] && oldF['consumptions'][sz]) {
                                vSzFs = oldF['consumptions'][sz]['fs_cons'] || '';
                                vSzHs = oldF['consumptions'][sz]['hs_cons'] || '';
                            }

                            if (!vSzFs && isEditMode && existingMatrix && existingMatrix.length > 0) {
                                const m = existingMatrix.find(m => String(m.art_no).split('|')[0].trim() == String(art).split('|')[0].trim());
                                if (m && m.consumptions) {
                                    const c = m.consumptions.find(c => String(c.size) === String(sz));
                                    if (c) {
                                        vSzFs = c.fs_cons || '';
                                        vSzHs = c.hs_cons || '';
                                    }
                                }
                            }

                            if (vSzFs !== '' && !isNaN(vSzFs)) vSzFs = parseFloat(vSzFs).toFixed(2);
                            if (vSzHs !== '' && !isNaN(vSzHs)) vSzHs = parseFloat(vSzHs).toFixed(2);

                            sizeTableHtml += `<tr>
                                <td>${sz}</td>
                                <td><input type="number" step="0.01" name="fabrics[${index}][consumptions][${sz}][fs_cons]" class="form-control form-control-sm text-center p-0 sleeve-qty-input size-cons-input" data-art="${art}" data-size="${sz}" data-type="fs" data-uom="${uom}" data-category="${catId}" value="${vSzFs}" ${isTaskReadOnly}></td>
                                <td><input type="number" step="0.01" name="fabrics[${index}][consumptions][${sz}][hs_cons]" class="form-control form-control-sm text-center p-0 sleeve-qty-input size-cons-input" data-art="${art}" data-size="${sz}" data-type="hs" data-uom="${uom}" data-category="${catId}" value="${vSzHs}" ${isTaskReadOnly}></td>
                            </tr>`;
                        });
                        sizeTableHtml += `</tbody></table>`;
                    }
                } else {
                    sizeTableHtml = `<div class="small text-muted p-1">Generate matrix first to see sizes</div>`;
                }

                sleeveQtyRow += `<td class="fw-bold bg-light" style="vertical-align: middle;">CONSUMPTION<br><span class="badge bg-secondary uom-label" data-art="${art}">${uom}</span></td>
                    <td class="p-0">${sizeTableHtml}</td>`;


            });

            $thead.append(headHtml + '</tr>');
            $tbody.append(artRow + '</tr>');
            $tbody.append(widthRow + '</tr>');
            $tbody.append(mtrRow + '</tr>');
            $tbody.append(inOutRow + '</tr>');
            $tbody.append(nPattiRow + '</tr>');
            if (!isCanvas) {
                $tbody.append(sleeveQtyRow + '</tr>');
            }
            $('.select2-size-multi').select2({ placeholder: 'Select sizes', allowClear: true });
        }

        $(document).on('click', '.add-lay-mark-btn', function() {
            const index = $(this).data('index');

            if (index === 0) {
                $('.add-lay-mark-btn').each(function() {
                    if ($(this).data('index') !== 0) {
                        addLayMarkRow($(this).data('index'));
                    }
                });
            }

            addLayMarkRow(index);
        });

        function addLayMarkRow(index) {
            const $btn = $(`.add-lay-mark-btn[data-index="${index}"]`);
            const sizes = $btn.data('sizes');
            const $table = $('#lay-mark-table-art-' + index + ' tbody');
            if (!$table.length) return;
            const rowCount = $table.find('tr.lay-mark-row').length;
            const newIndex = rowCount;
            
            let selectedBrandText = $('#brand option:selected').text().toUpperCase().trim();
            let isCanvas = selectedBrandText === 'CANVAS ACCESSORIES' || selectedBrandText === 'CANVAS ACCESSORIES (CAS)';


            let rowHtml = `
                <tr class="lay-mark-row">
                    <td class="text-center align-middle fw-bold mark-no">${newIndex + 1}</td>
                    <td>
                        <select class="form-select form-select-sm select2-size-multi sizes-input" multiple="multiple" name="fabrics[${index}][lay_marks][${newIndex}][sizes][]" style="width: 100%;">
                            ${sizes.map(sz => `<option value="${sz}">${sz}</option>`).join('')}
                        </select>
                        <div class="text-danger small mt-1 lay-mark-error" style="display:none; text-align: left; font-weight: 500;">This field is required</div>
                    </td>
                    ${isCanvas ? `<input type="hidden" name="fabrics[${index}][lay_marks][${newIndex}][sleeve]" value="F/S">` : `
                    <td>
                        <select class="form-select form-select-sm sleeve-input" name="fabrics[${index}][lay_marks][${newIndex}][sleeve]">
                            <option value="F/S">F/S</option>
                            <option value="H/S">H/S</option>
                        </select>
                        <div class="text-danger small mt-1 lay-mark-error" style="display:none; text-align: left; font-weight: 500;">This field is required</div>
                    </td>`}
                    <td style="padding: 10px; vertical-align: top;">
                        <div style="display:flex; flex-direction:column; justify-content:center; align-items:center; height:100%;">
                            <input type="number" step="0.01" min="0.01" class="form-control form-control-sm text-center meter-input" name="fabrics[${index}][lay_marks][${newIndex}][meter]" placeholder="0.00">
                            <div class="text-danger small mt-1 lay-mark-error" style="display:none; text-align: left; width: 100%; font-weight: 500;">This field is required</div>
                        </div>
                    </td>
                    <td style="padding: 10px; vertical-align: top;">
                        <div style="display:flex; flex-direction:column; justify-content:center; align-items:center; height:100%;">
                            <input type="number" step="0.01" min="0.01" class="form-control form-control-sm text-center no-of-lay-input" name="fabrics[${index}][lay_marks][${newIndex}][no_of_lay]" placeholder="0">
                            <div class="text-danger small mt-1 lay-mark-error" style="display:none; text-align: left; width: 100%; font-weight: 500;">This field is required</div>
                        </div>
                    </td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-sm btn-icon btn-danger remove-lay-mark"><i class="ri ri-delete-bin-line"></i></button>
                    </td>
                </tr>
            `;
            $table.append(rowHtml);
            $table.find('tr:last-child .select2-size-multi').select2({ placeholder: 'Select sizes', allowClear: true });
            updateLayMarkRowNumbers($table);
            calculateMatrixFromLayMarks();
        }

        $(document).on('click', '.remove-lay-mark', function() {
            const $row = $(this).closest('tr');
            const $table = $row.closest('tbody');
            const rowIndex = $row.index();
            const $containerTable = $(this).closest('.lay-mark-table');
            const fabricIndex = $containerTable.find('.add-lay-mark-btn').data('index');

            if (fabricIndex === 0) {
                $('.lay-mark-table').each(function() {
                    const thisTableIndex = $(this).find('.add-lay-mark-btn').data('index');
                    if (thisTableIndex !== 0) {
                        $(this).find('tbody tr').eq(rowIndex).remove();
                        updateLayMarkRowNumbers($(this).find('tbody'));
                    }
                });
            }

            if ($table.find('tr.lay-mark-row').length > 1) {
                $row.remove();
                updateLayMarkRowNumbers($table);
                calculateMatrixFromLayMarks();
            }
        });

        $(document).on('input change', '.lay-mark-table[id$="-art-0"] input, .lay-mark-table[id$="-art-0"] select', function() {
            if (isSyncing) return;
            isSyncing = true;
            try {
                const $el = $(this);
                const $row = $el.closest('tr');
                const rowIndex = $row.index();
                const name = $el.attr('name');
                if (!name) return;

                const suffix = name.replace(/^fabrics\[0\]\[lay_marks\]\[\d+\]/, '');

                $('.lay-mark-table').each(function() {
                    const fabricIndex = $(this).find('.add-lay-mark-btn').data('index');
                    if (fabricIndex === 0) return;

                    const targetName = `fabrics[${fabricIndex}][lay_marks][${rowIndex}]${suffix}`;
                    const $target = $(`[name="${targetName}"]`);

                    if ($target.length) {
                        if ($el.hasClass('select2-size-multi') && $el.is('select')) {
                            const val = $el.val();
                            $target.val(val).trigger('change.select2');
                        } else {
                            $target.val($el.val());
                        }
                    }
                });
            } finally {
                isSyncing = false;
            }
        });

        function updateLayMarkRowNumbers($table) {
            $table.find('tr.lay-mark-row').each(function(i) {
                $(this).find('.mark-no').text(i + 1);

                const selectAttr = $(this).find('.select2-size-multi').attr('name');
                if (selectAttr) {
                    $(this).find('.select2-size-multi').attr('name', selectAttr.replace(/\[lay_marks\]\[\d+\]/, `[lay_marks][${i}]`));
                }

                const sleeveAttr = $(this).find('select[name$="[sleeve]"]').attr('name');
                if (sleeveAttr) {
                    $(this).find('select[name$="[sleeve]"]').attr('name', sleeveAttr.replace(/\[lay_marks\]\[\d+\]/, `[lay_marks][${i}]`));
                }

                const meterAttr = $(this).find('input[name$="[meter]"]').attr('name');
                if (meterAttr) {
                    $(this).find('input[name$="[meter]"]').attr('name', meterAttr.replace(/\[lay_marks\]\[\d+\]/, `[lay_marks][${i}]`));
                }

                const layAttr = $(this).find('input[name$="[no_of_lay]"]').attr('name');
                if (layAttr) {
                    $(this).find('input[name$="[no_of_lay]"]').attr('name', layAttr.replace(/\[lay_marks\]\[\d+\]/, `[lay_marks][${i}]`));
                }
            });
        }

        if (currentArtNumbers.length > 0) {
            $('#fabric-details-card').removeClass('d-none');
            renderFabricDetails();
            if (typeof renderItemDetailsTable === "function") {
                renderItemDetailsTable(currentArtData);
            }
        }

        if (oldMatrix && Object.keys(oldMatrix).length > 0) {
            syncMatrixWithMasterTable(false);
        }

        function renderCuttingSizeTable(sizes, ratios) {
            const $table = $('#cutting-size-table');
            const $sizeHeaderRow = $table.find('.size-header-row');

            $table.find('.ratio-header').attr('colspan', sizes.length);
            $sizeHeaderRow.find('th').not('.extra-col-1, .extra-col-2').remove();

            let sizeHeadersHtml = '';
            sizes.forEach(s => sizeHeadersHtml += `<th class="dynamic-size-head">${s}</th>`);
            $sizeHeaderRow.prepend(sizeHeadersHtml);

            const $tbody = $table.find('tbody');
            $tbody.empty();

            const selectedRatioDisplay = $('#size_ratio_display').val() || '';
            const sizeStr = selectedRatioDisplay ? selectedRatioDisplay.split(' - ')[0] : '';

            const addTypeRows = (type, label, isVisible, showInfo = true, infoLabel = 'SIZE', instanceId = null) => {
                const style = isVisible ? '' : 'display:none;';

                let vRow = `<tr class="qty-${type}-row" style="${style}"><td><strong>${label}</strong></td>`;
                sizes.forEach((s, idx) => {
                    let savedVal = '';
                    if (matrixItems.length > 0) {
                        const savedRecord = matrixItems.find(r => String(r.size) === String(s));
                        if (savedRecord) {
                            const fsVal = savedRecord.qty_fs != null ? savedRecord.qty_fs : '';
                            const hsVal = savedRecord.qty_hs != null ? savedRecord.qty_hs : '';
                            savedVal = type === 'fs' ? fsVal : hsVal;
                        }
                    }
                    const ratioVal = (ratios[idx] && !savedVal) ? ratios[idx] : '';
                    let finalVal = savedVal || ratioVal;

                    if (sleeveValues[instanceId] && sleeveValues[instanceId][s] !== undefined) {
                        finalVal = sleeveValues[instanceId][s];
                    }

                    vRow += `<td>
                        <input type="number" class="form-control form-control-sm text-center fw-bold qty-direct-input dummy-input-${type}-summary-${s}" data-type="${type}" data-size="${s}" data-instance="${instanceId}" value="${finalVal}" placeholder="-">
                    </td>`;
                });

                vRow += `</tr>`;
                $tbody.append(vRow);

                if (showInfo) {
                    let iRow = `<tr class="qty-${type}-info-row" style="${style}"><td><strong>${infoLabel}</strong></td>`;
                    iRow += `<td colspan="${sizes.length}"><input type="text" class="form-control form-control-sm text-center text-muted" value="${infoLabel === 'SIZE' ? sizeStr : ''}"></td>`;
                    iRow += `</tr>`;
                    $tbody.append(iRow);
                }
            };

            sleeveInstances.forEach((inst, instIdx) => {
                const label = inst.type === 'fs' ? 'QTY - F/S' : 'QTY - H/S';
                addTypeRows(inst.type, label, true, false, 'SIZE', inst.id);
            });

            syncSummaryToHeader(); 
        }

        function updateQuantityRowVisibility() {
            const hasFS = sleeveInstances.some(i => i.type === 'fs');
            const hasHS = sleeveInstances.some(i => i.type === 'hs');
            $('#total_qty_fs').closest('.col-md-6').toggle(hasFS);
            $('#total_qty_hs').closest('.col-md-6').toggle(hasHS);
        }


        $('#production_stage_select').on('change', function() {
            const stageId = $(this).val();
            const stageName = $(this).find(':selected').data('name');
            if (!stageId) return;

            if (addedStages.includes(stageId)) {
                $(this).val('').trigger('change');
                return;
            }

            addedStages.push(stageId);
            const index = addedStages.length - 1;
            const empOptions = `@foreach($employees as $emp)<option value="{{ $emp->id }}">{{ $emp->name }}( {{ $emp->emp_id }} )</option>@endforeach`;

            const rowHtml = `
                <tr data-stage="${stageId}">
                    <td><input type="text" class="form-control form-control-sm" value="${stageName}" readonly><input type="hidden" name="stages[${index}][stage_id]" value="${stageId}"></td>
                    <td><input type="text" class="form-control form-control-sm dynamic-stage-date" name="stages[${index}][issue_date]" placeholder="Date"></td>
                    <td><select class="form-select form-select-sm select2-dynamic" name="stages[${index}][employee_id]"><option value="">Select Employee</option>${empOptions}</select></td>
                    <td><input type="text" class="form-control form-control-sm" name="stages[${index}][received_by]" placeholder="Received By"></td>
                    <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-stage"><i class="ri ri-delete-bin-line"></i></button></td>
                </tr>`;

            $('#production_stages_body').append(rowHtml);
            $('.select2-dynamic').last().select2();
            $('.dynamic-stage-date').last().flatpickr(flatpickrConfig);
            $(this).val('').trigger('change');
        });

        $('#production_stages_body').on('click', '.remove-stage', function() {
            const row = $(this).closest('tr');
            addedStages = addedStages.filter(id => id != row.data('stage'));
            row.remove();
        });

        function syncSummaryToHeader() {
            calculateMatrixTotals();
        }

        $(document).on('input change', 'input, select, textarea', function() {
            const $el = $(this);
            $el.removeClass('is-invalid');
            $el.closest('.col-md-6, .col-xl-4, .col-lg-4, .form-group').find('.text-danger.small').fadeOut(function() {
                $(this).remove();
            });
        });

        function showFieldError(selector, message) {
            const $el = $(selector);
            if (!$el.length) return;
            $el.addClass('is-invalid');
            const $container = $el.closest('.col-md-6, .col-xl-4, .col-lg-4, .form-group');
            $container.find('.text-danger.small').remove();
            $container.append(`<div class="text-danger small mt-1">${message}</div>`);
            $('html, body').animate({
                scrollTop: $el.offset().top - 150
            }, 500);
        }

        $(document).on('input', '.qty-direct-input', function() {
            if (isSyncing) return;
            updateSleeveJson();
            syncMatrixWithMasterTable(true);
        });

        function syncMatrixWithMasterTable(populateValues = true, reRenderFabric = true, targetType = null, targetSize = null) {
            if (isSyncing) return { fs: [], hs: [] };
            isSyncing = true;
            let activeFsSizes = [];
            let activeHsSizes = [];
            try {
                let hasFS = sleeveInstances.some(i => i.type === 'fs');
                let hasHS = sleeveInstances.some(i => i.type === 'hs');

                $('.lay-mark-table').each(function() {
                    $(this).find('tbody tr.lay-mark-row').each(function() {
                        const sleeve = $(this).find('select[name$="[sleeve]"]').val();
                        const noOfLay = parseFloat($(this).find('input[name*="[no_of_lay]"]').val()) || 0;
                        if (noOfLay > 0) {
                            if (sleeve === 'F/S') hasFS = true;
                            if (sleeve === 'H/S') hasHS = true;
                        }
                    });
                });

                if (hasFS) activeFsSizes = [...currentSizes];
                if (hasHS) activeHsSizes = [...currentSizes];

                $('#hidden-matrix-items-container').remove();
                let hiddenInputsHtml = `<div id="hidden-matrix-items-container" style="display:none;">`;

                currentSizes.forEach((size, idx) => {
                    let totalFs = 0;
                    let totalHs = 0;
                    $(`.qty-direct-input[data-type="fs"][data-size="${size}"]`).each(function() {
                        totalFs += parseFloat($(this).val()) || 0;
                    });
                    $(`.qty-direct-input[data-type="hs"][data-size="${size}"]`).each(function() {
                        totalHs += parseFloat($(this).val()) || 0;
                    });

                    let ratioVal = currentRatios[idx] || '';

                    hiddenInputsHtml += `<input type="hidden" name="matrix_items[${idx}][size]" value="${size}">`;
                    hiddenInputsHtml += `<input type="hidden" name="matrix_items[${idx}][qty_fs]" value="${totalFs > 0 ? totalFs : ''}">`;
                    hiddenInputsHtml += `<input type="hidden" name="matrix_items[${idx}][qty_hs]" value="${totalHs > 0 ? totalHs : ''}">`;
                    hiddenInputsHtml += `<input type="hidden" name="matrix_items[${idx}][ratio]" value="${ratioVal}">`;
                });

                hiddenInputsHtml += `</div>`;
                $('#cutting-size-table').after(hiddenInputsHtml);

                const sizesChanged = JSON.stringify(globalActiveSizes.fs) !== JSON.stringify(activeFsSizes) || JSON.stringify(globalActiveSizes.hs) !== JSON.stringify(activeHsSizes);

                const artNumbers = getArtNumbers();

                if (artNumbers.length > 0 && (activeFsSizes.length > 0 || activeHsSizes.length > 0)) {
                    if (sizesChanged) {
                        renderArticleQtyMatrix(artNumbers, activeFsSizes, activeHsSizes);
                    }
                    $('#article-matrix-card').removeClass('d-none');

                    if (populateValues) {
                        currentSizes.forEach(size => {
                            ['fs', 'hs'].forEach(type => {
                                let totalVal = 0;
                                $(`.qty-direct-input[data-type="${type}"][data-size="${size}"]`).each(function() {
                                    totalVal += parseFloat($(this).val()) || 0;
                                });

                                $('table[id^="article-qty-matrix"] tbody tr .qty-input').filter(function() {
                                    const $row = $(this).closest('tr');
                                    const catId = $row.data('category');
                                    return $(this).data('col') === `${type}-${size}` && catId != 1;
                                }).each(function() {
                                    const uom = ($(this).closest('tr').attr('data-uom') || '').toUpperCase();
                                    let finalCalc = totalVal > 0 ? (uom === 'PCS' ? Math.round(totalVal).toString() : totalVal.toString()) : '';

                                    if ($(this).val() != finalCalc) {
                                        $(this).val(finalCalc);
                                    }
                                });
                            });
                        });
                        calculateMatrixFromLayMarks();
                    }
                    calculateMatrixTotals(true);
                } else {
                    if (artNumbers.length > 0 && sizesChanged) {
                        renderArticleQtyMatrix(artNumbers, [], []);
                    }
                }

                if (reRenderFabric && sizesChanged) {
                    globalActiveSizes = { fs: activeFsSizes, hs: activeHsSizes };
                    renderFabricDetails();
                    if (typeof renderItemDetailsTable === "function") {
                        renderItemDetailsTable(currentArtData);
                    }
                } else {
                    globalActiveSizes = { fs: activeFsSizes, hs: activeHsSizes };
                }
            } finally {
                if (typeof applyCanvasLogic === 'function') {
                    applyCanvasLogic();
                }
                isSyncing = false;
            }

            return { fs: activeFsSizes, hs: activeHsSizes };
        }

        function calculateMatrixFromLayMarks() {
            const matrixData = {};
            let needsSync = false;
            $('.lay-mark-table').each(function() {
                $(this).find('tbody tr.lay-mark-row').each(function() {
                    const sleeve = $(this).find('select[name$="[sleeve]"]').val();
                    const noOfLay = parseFloat($(this).find('input[name*="[no_of_lay]"]').val()) || 0;
                    if (noOfLay > 0) {
                        const type = sleeve.toLowerCase().replace('/', '');
                        if (type === 'fs' && (!globalActiveSizes.fs || !globalActiveSizes.fs.length)) needsSync = true;
                        if (type === 'hs' && (!globalActiveSizes.hs || !globalActiveSizes.hs.length)) needsSync = true;
                    }
                });
            });

            if (needsSync) {
                syncMatrixWithMasterTable(true);
                return; 
            }

            $('.lay-mark-table').each(function() {
                const fabricIndex = $(this).find('.add-lay-mark-btn').data('index');
                if (fabricIndex === undefined) return;

                let hasValidMarks = false;
                $(this).find('tbody tr.lay-mark-row').each(function() {
                    const sizes = $(this).find('.select2-size-multi').val() || [];
                    const noOfLay = parseFloat($(this).find('input[name*="[no_of_lay]"]').val()) || 0;
                    if (sizes.length > 0 && noOfLay > 0) hasValidMarks = true;
                });

                if (hasValidMarks) {
                    const fabricArt = String($(this).data('art') || "").split('|')[0].trim();
                    if (fabricArt) {
                        $(`tr.cat1-row`).filter(function() {
                            return String($(this).data('art') || "").split('|')[0].trim() === fabricArt;
                        }).find('.qty-input').val('');
                    }

                    if (!matrixData[fabricIndex]) matrixData[fabricIndex] = { fs: {}, hs: {} };
                    $(this).find('tbody tr.lay-mark-row').each(function() {
                        const sizes = $(this).find('.select2-size-multi').val() || [];
                        const sleeve = $(this).find('select[name$="[sleeve]"]').val();
                        const noOfLay = parseFloat($(this).find('input[name*="[no_of_lay]"]').val()) || 0;

                        if (sleeve && noOfLay > 0) {
                            const type = sleeve.toLowerCase().replace('/', '');
                            sizes.forEach(sz => {
                                matrixData[fabricIndex][type][sz] = (matrixData[fabricIndex][type][sz] || 0) + noOfLay;
                            });
                        }
                    });
                }
            });

            for (const fIndex in matrixData) {
                const $btn = $(`.add-lay-mark-btn[data-index="${fIndex}"]`);
                if (!$btn.length) continue;

                const fabricArt = String($btn.closest('.lay-mark-table').data('art') || "").split('|')[0].trim();
                if (!fabricArt) continue;

                for (const type in matrixData[fIndex]) {
                    for (const size in matrixData[fIndex][type]) {
                        const val = matrixData[fIndex][type][size];
                        const col = `${type}-${size}`;
                        const $input = $(`.qty-input`).filter(function() {
                            return String($(this).data('art') || "").split('|')[0].trim() === fabricArt && $(this).data('col') === col;
                        });

                        if ($input.length) {
                            $input.val(val > 0 ? val : '');
                        }
                    }
                }
            }

            if (matrixData[0]) {
                for (const type in matrixData[0]) {
                    for (const size in matrixData[0][type]) {
                        const val = matrixData[0][type][size];
                        const $masterInput = $(`.qty-direct-input[data-type="${type}"][data-size="${size}"]`);
                        if ($masterInput.length && !$masterInput.val()) {
                            $masterInput.val(val || '');
                        }
                    }
                }
                updateSleeveJson();
            }

            calculateMatrixTotals();
        }

        $(document).on('input', 'input[name$="[no_of_lay]"]', function() {
            calculateMatrixFromLayMarks();
        });

        $(document).on('change', '.select2-size-multi, select[name$="[sleeve]"]', function() {
            calculateMatrixFromLayMarks();
        });

        function getConsumptionValue(art, type, size) {
            const $sizeWise = $(`.size-cons-input[data-art="${art}"][data-size="${size}"][data-type="${type}"]`);
            if ($sizeWise.length) {
                const val = parseFloat($sizeWise.val());
                if (!isNaN(val)) return val;
                return 0;
            }
            return 0; 
        }

        $(document).on('input', '.size-cons-input', function() {
            calculateMatrixTotals();
        });

        if ($('.size-cons-input').length > 0) {
            calculateMatrixTotals();
        }


        $(document).on('input', '.sleeve-qty-input', function() {
            syncMatrixWithMasterTable(false, false);
        });


        $('#trigger-sync').on('click', function() {
            const result = syncMatrixWithMasterTable(false);

            if (result.fs.length === 0 && result.hs.length === 0) {
                alert('Please enter at least one quantity in the Master Table (Cutting Size Ratio).');
                return;
            }

            $('html, body').animate({
                scrollTop: $("#article-qty-matrix").offset().top - 100
            }, 500);
        });

        const oldStages = @json(old('stages', []));
        if (oldStages.length) {
            const $stageBody = $('#production_stages_body');
            const empOptions = `@foreach($employees as $emp)<option value="{{ $emp->id }}">{{ $emp->name }}</option>@endforeach`;

            oldStages.forEach((stage, index) => {
                const stageId = stage.stage_id;
                let stageName = '';
                $('#production_stage_select option').each(function() {
                    if ($(this).val() == stageId) {
                        stageName = $(this).data('name');
                    }
                });

                if (stageId && !addedStages.includes(stageId)) {
                    addedStages.push(stageId);
                    const rowHtml = `
                        <tr data-stage="${stageId}">
                            <td><input type="text" class="form-control form-control-sm" value="${stageName}" readonly><input type="hidden" name="stages[${index}][stage_id]" value="${stageId}"></td>
                            <td><input type="text" class="form-control form-control-sm dynamic-stage-date" name="stages[${index}][issue_date]" placeholder="Date" value="${stage.issue_date || ''}"></td>
                            <td><select class="form-select form-select-sm select2-dynamic" name="stages[${index}][employee_id]"><option value="">Select Employee</option>${empOptions}</select></td>
                            <td><input type="text" class="form-control form-control-sm" name="stages[${index}][received_by]" placeholder="Received By" value="${stage.received_by || ''}"></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-stage"><i class="ri ri-delete-bin-line"></i></button></td>
                        </tr>`;
                    $stageBody.append(rowHtml);

                    const $lastRow = $stageBody.find('tr').last();
                    if (stage.employee_id) {
                        $lastRow.find('select').val(stage.employee_id);
                    }
                }
            });
            $('.select2-dynamic').select2();
            $('.dynamic-stage-date').flatpickr(flatpickrConfig);
        }

        if (oldMatrix && Object.keys(oldMatrix).length > 0) {
            renderCuttingSizeTable(currentSizes, currentRatios);
        }

        window.deleteImage = function(imageId) {
            if (!confirm('Are you sure you want to delete this image?')) return;
            $.ajax({
                url: `{{ url('job_card_entries/delete-image') }}/${imageId}`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        const index = existingImages.findIndex(img => img.id === imageId);
                        if (index > -1) {
                            existingImages.splice(index, 1);
                        }
                        renderFabricDetails();
                        if (typeof renderItemDetailsTable === "function") {
                            renderItemDetailsTable(currentArtData);
                        }
                    }
                },
                error: function(xhr) {
                    alert('Error deleting image');
                }
            });
        };

        $('#cutting_issue_unit').on('change', function() {
            const plantId = $(this).val();
            const $masterSelect = $('#cutting_master');
            $masterSelect.empty().append('<option value="">Select Cutting Master</option>');
            if (plantId) {
                $.ajax({
                    url: `{{ url('get-employees-by-plant') }}/${plantId}?department_id=1`,
                    type: 'GET',
                    success: function(response) {
                        if (response.success && response.employees) {
                            response.employees.forEach(function(emp) {
                                $masterSelect.append(`<option value="${emp.id}">${emp.name}</option>`);
                            });
                            $masterSelect.trigger('change');
                        }
                    },
                    error: function() {
                        console.error('Error fetching employees');
                    }
                });
            }
        });

        let stageRowIndex = {{ !empty($existingStages) ? count($existingStages) : 1 }};
        $('#add-stage-row').on('click', function() {
            let newRow = `
            <tr class="stage-row">
                <td>
                    <select name="production_stages[${stageRowIndex}][stage_id]" class="form-select select2 stage-select" data-placeholder="Select Stage">
                        <option value="">Select Stage</option>
                        @foreach($operationStages as $os)
                            <option value="{{ $os->id }}" data-cost="{{ $os->cost }}">{{ $os->operation_stage_name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="production_stages[${stageRowIndex}][service_provider_id]" class="form-select select2 provider-select" data-placeholder="Select Unit">
                        <option value="">Select Unit</option>
                        @foreach($plants as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="production_stages[${stageRowIndex}][rate]" class="form-control stage-rate" value="" step="0.01" placeholder="0.00">
                </td>
                <td>
                    <input type="text" name="production_stages[${stageRowIndex}][issue_date]" class="form-control issue-date" value="" placeholder="Enter Issue Date">
                </td>
                <td>
                    <input type="text" name="production_stages[${stageRowIndex}][deadline_date]" class="form-control deadline-date" value="" placeholder="Enter Deadline Date">
                </td>
                <td>
                    <input type="text" name="production_stages[${stageRowIndex}][remarks]" class="form-control" placeholder="Enter Remarks">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger remove-stage-row"><i class="ri ri-delete-bin-line"></i></button>
                </td>
            </tr>`;
            $('#production-stages-table tbody').append(newRow);

            $('#production-stages-table tbody tr:last .select2').each(function() {
                $(this).select2({
                    dropdownParent: $(this).parent(),
                    placeholder: $(this).data('placeholder'),
                    width: '100%'
                });
            });

            const config = typeof flatpickrConfig !== 'undefined' ? flatpickrConfig : { dateFormat: 'd-m-Y', allowInput: true };
            $('#production-stages-table tbody tr:last .issue-date, #production-stages-table tbody tr:last .deadline-date').flatpickr(config);

            stageRowIndex++;
        });

        $(document).on('change', '.stage-select', function() {
            const $row = $(this).closest('tr');
            const selectedOption = $(this).find('option:selected');
            const cost = selectedOption.data('cost');
            if (cost !== undefined && cost !== '') {
                $row.find('.stage-rate').val(parseFloat(cost).toFixed(2));
            }

            $row.find('.issue-date').trigger('change');
        });

        $(document).on('click', '.remove-stage-row', function() {
            if ($('#production-stages-table tbody tr').length > 1) {
                $(this).closest('tr').remove();
            } else {
                alert('At least one row is required.');
            }
        });

        const operationStagesData = @json($operationStages->keyBy('id'));

        $(document).on('change', '.issue-date', function(e) {
            let $row = $(this).closest('tr');
            let stageId = $row.find('.stage-select').val();
            let issueDateStr = $(this).val();
            if (stageId && issueDateStr) {
                let stageData = operationStagesData[stageId];
                if (stageData && stageData.working_days) {
                    let parts = issueDateStr.split('-');
                    if (parts.length === 3) {
                        let issueDateObj = new Date(parts[2], parts[1] - 1, parts[0]);
                        issueDateObj.setDate(issueDateObj.getDate() + parseInt(stageData.working_days));

                        let d = String(issueDateObj.getDate()).padStart(2, '0');
                        let m = String(issueDateObj.getMonth() + 1).padStart(2, '0');
                        let y = issueDateObj.getFullYear();

                        let deadlineDateStr = d + '-' + m + '-' + y;
                        let $deadlineInput = $row.find('.deadline-date');
                        $deadlineInput.val(deadlineDateStr);
                        if ($deadlineInput[0]._flatpickr) {
                            $deadlineInput[0]._flatpickr.setDate(deadlineDateStr, true);
                        }
                    }
                }
            }
        });

        $('.stage-select, .issue-date').each(function() {
            if ($(this).val() && !$(this).hasClass('issue-date')) {
                $(this).trigger('change');
            }
        });


        $(document).on('click', '.assign-task-btn', function() {
            let $row = $(this).closest('tr');
            let stageId = $row.find('.stage-select').val();
            let jobCardId = '{{ $jobCard ? $jobCard->id : "" }}';

            if (!stageId) {
                alert('Please select a Stage before assigning.');
                return;
            }

            let baseUrl = '{{ route("task_management.add") }}';
            let params = new URLSearchParams({
                job_card_id: jobCardId,
                stage_id: stageId
            });
            window.open(baseUrl + '?' + params.toString(), '_blank');
        });

        function renderItemDetailsTable(artData) {
            const $wrapper = $('#item-details-table-wrapper');
            const $fabricTbody = $('#item-details-fabric-tbody');
            const $accTbody = $('#item-details-accessories-tbody');
            const $msg = $('#no-materials-msg');
            const currentManualUsed = {};
            $wrapper.find('.item-used-input').each(function() {
                const art = $(this).data('art');
                if (art) currentManualUsed[art] = $(this).val();
            });

            $fabricTbody.empty();
            $accTbody.empty();

            if (!artData || artData.length === 0) {
                $wrapper.addClass('d-none');
                $msg.removeClass('d-none');
                $('#no-material-text').text('No materials found for selected Stock Entry Number.');
                return;
            }

            $wrapper.removeClass('d-none');
            $msg.addClass('d-none');

            let fabricIndex = 0;
            let accIndex = 0;

            artData.forEach((item, index) => {
                let totalQty;
                if (isEditMode && item.saved_stock_total_qty) {
                    totalQty = parseFloat(item.saved_stock_total_qty);
                } else {
                    totalQty = parseFloat(item.total_available) || 0;
                }
                const artNo = item.art_no;
                const categoryId = item.store_category_id || 2; 

                let oldVal = '';
                if (oldFabrics && oldFabrics.length > 0) {
                    const oldF = Object.values(oldFabrics).find(f => String(f.art_no).split('|')[0].trim() == String(artNo).split('|')[0].trim());
                    if (oldF && oldF.mtr !== undefined && oldF.mtr !== '') {
                        oldVal = oldF.mtr;
                    }
                }

                let usedStr = '';

                if (currentManualUsed[artNo] !== undefined &&
                    currentManualUsed[artNo] !== null &&
                    currentManualUsed[artNo] !== '') {
                    usedStr = currentManualUsed[artNo];
                }

                if (!usedStr && oldVal !== '') {
                    usedStr = String(oldVal);
                }

                if (!usedStr && isEditMode &&
                    item.saved_mtr !== null && item.saved_mtr !== undefined) {
                    usedStr = parseFloat(item.saved_mtr).toFixed(2);
                }

                if (!usedStr) {
                    usedStr = totalQty.toFixed(2);
                }

                const used = parseFloat(usedStr) || 0;
                let remaining = totalQty - used;
                if (Math.abs(remaining) < 0.001) remaining = 0;

                $(`.total-qty-hidden[data-art="${artNo}"]`).val(totalQty.toFixed(2));
                $(`.used-qty-hidden[data-art="${artNo}"]`).val(used.toFixed(2));
                $(`.remaining-qty-hidden[data-art="${artNo}"]`).val(remaining.toFixed(2));

                const isFabric = (categoryId == 1);

                if (item.fs_cons != null || item.hs_cons != null) {
                    const fsCons = parseFloat(item.fs_cons) || 0;
                    const hsCons = parseFloat(item.hs_cons) || 0;
                    const tFS = parseFloat($('#total_qty_fs').val()) || 0;
                    const tHS = parseFloat($('#total_qty_hs').val()) || 0;

                    const calculated = (tFS * fsCons) + (tHS * hsCons);

                    if (currentManualUsed[artNo] === undefined && oldVal === '') {
                        if (isEditMode && item.saved_mtr !== null && item.saved_mtr !== undefined) {
                        } else {
                            if (calculated > 0) {
                                usedStr = calculated.toFixed(2);
                            }
                        }
                    }
                }
                const sNo = (isFabric) ? ++fabricIndex : ++accIndex;

                const row = `
                    <tr data-art="${artNo}">
                        <td class="text-center">${sNo}</td>
                        <td class="fw-bold">
                            ${item.art_name || ''} <br> 
                            <small class="text-muted">${item.actual_art_no || artNo}</small>
                            ${item.stock_entry_nos ? `<br><small class="text-info">${item.stock_entry_nos}</small>` : ''}
                        </td>
                        <td class="text-center item-total-qty">${totalQty.toFixed(2)}</td>
                        <td>
                            <input type="hidden" name="fabrics[${index}][art_no]" value="${artNo}">
                            <input type="hidden" name="fabrics[${index}][stock_entry_id]" value="${item.stock_entry_id || ''}">
                            <input type="hidden" name="fabrics[${index}][store_category_id]" value="${categoryId}">
                            <input type="hidden" name="fabrics[${index}][stock_total_qty]" class="stock-total-qty-hidden" data-art="${artNo}" value="${totalQty.toFixed(2)}">
                            <div class="input-group input-group-sm">
                                <input type="number" step="0.01" class="form-control text-center fw-bold item-used-input" name="fabrics[${index}][mtr]" data-art="${artNo}" value="${usedStr}">
                                <span class="input-group-text">${item.uom_code || 'MTR'}</span>
                            </div>
                            ${validationErrors[`fabrics.${index}.mtr`] ? `<div class="text-danger small mt-1" style="font-size: 11px;">${validationErrors[`fabrics.${index}.mtr`][0]}</div>` : ''}
                        </td>
                        <td class="text-center item-remaining-qty fw-bold ${remaining < -0.001 ? 'text-danger' : 'text-success'}">${remaining.toFixed(2)}</td>
                    </tr>
                `;

                if (isFabric) {
                    $fabricTbody.append(row);
                } else {
                    $accTbody.append(row);
                }
            });

            if (fabricIndex === 0) {
                $fabricTbody.append(`<tr><td colspan="5" class="text-center text-muted">No Fabric Items</td></tr>`);
            }
            if (accIndex === 0) {
                $accTbody.append(`<tr><td colspan="5" class="text-center text-muted">No Accessories Items</td></tr>`);
            }
        }

        $(document).on('input', '.item-used-input', function() {
            const $input = $(this);
            const $row = $input.closest('tr');
            const artNo = $input.data('art');
            const total = parseFloat($row.find('.item-total-qty').text()) || 0;
            const used = parseFloat($input.val()) || 0;
            let remaining = total - used;
            if (Math.abs(remaining) < 0.001) remaining = 0;

            const $remainingCell = $row.find('.item-remaining-qty');
            $remainingCell.text(remaining.toFixed(2));

            if (remaining < -0.001) {
                $remainingCell.removeClass('text-success').addClass('text-danger');
                $input.addClass('border-danger');
                if (!$row.find('.qty-error-msg').length) {
                    $input.parent().after('<small class="text-danger qty-error-msg d-block mt-1" style="font-size: 10px;">Used Qty cannot exceed Total Qty</small>');
                }
            } else {
                $remainingCell.removeClass('text-danger').addClass('text-success');
                $input.removeClass('border-danger');
                $row.find('.qty-error-msg').remove();
            }

            $(`.mtr-input[data-art="${artNo}"]`).val(used.toFixed(2)).trigger('change');
            $(`.used-qty-hidden[data-art="${artNo}"]`).val(used.toFixed(2));
            $(`.remaining-qty-hidden[data-art="${artNo}"]`).val(remaining.toFixed(2));
        });

        $(document).on('click', '.sync-mtr-btn', function() {
            const art = $(this).data('art');
            const reqVal = parseFloat($(`.requirement-display[data-art="${art}"] .req-val`).text());
            if (!isNaN(reqVal) && reqVal > 0) {
                $(`.mtr-input[data-art="${art}"]`).val(reqVal.toFixed(2)).trigger('input');
            }
        });

        $(document).on('input', '.mtr-input', function() {
            const $input = $(this);
            const artNo = $input.data('art');
            const val = parseFloat($input.val()) || 0;

            const $itemInput = $(`.item-used-input[data-art="${artNo}"]`);
            if ($itemInput.length && parseFloat($itemInput.val()) !== val) {
                $itemInput.val(val.toFixed(2)).trigger('input');
            }
        });

        function applyCanvasLogic() {
            let selectedBrandText = $('#brand option:selected').text().toUpperCase().trim();
            let isCanvas = selectedBrandText === 'CANVAS ACCESSORIES' || selectedBrandText === 'CANVAS ACCESSORIES (CAS)';
            
            if (isCanvas) {
                // Production Stages
                $('#production-stages-table tbody tr').each(function() {
                    let $row = $(this);
                    let stageText = $row.find('.stage-select option:selected').text().toUpperCase();
                    if(stageText && stageText.trim() !== '' && !stageText.includes('CUTTING')) {
                        $row.remove();
                    }
                });
                
                if($('#production-stages-table tbody tr').length === 0 && typeof window.addStageRow === 'function') {
                    window.addStageRow();
                    setTimeout(() => {
                        let $lastRow = $('#production-stages-table tbody tr').last();
                        let $stageSelect = $lastRow.find('.stage-select');
                        $stageSelect.find('option').filter(function() {
                            return $(this).text().toUpperCase().includes('CUTTING');
                        }).prop('selected', true).trigger('change');
                    }, 200);
                } else if ($('#production-stages-table tbody tr').length === 0) {
                     $('#add-stage-row').click();
                     setTimeout(() => {
                        let $lastRow = $('#production-stages-table tbody tr').last();
                        let $stageSelect = $lastRow.find('.stage-select');
                        $stageSelect.find('option').filter(function() {
                            return $(this).text().toUpperCase().includes('CUTTING');
                        }).prop('selected', true).trigger('change');
                    }, 200);
                }
                
                $('#add-stage-row').hide();
                
                $('#cutting-size-table tr.qty-hs-row').hide();
                $('#cutting-size-table tr.qty-fs-row td:first').html('<strong>QUANTITY</strong>');
                $('select[name="sleeve_types[]"]').closest('.col-md-6').hide();
                
                $('#sleeve-instance-manager').hide();
                if (typeof sleeveInstances !== 'undefined' && sleeveInstances.length === 0) {
                     sleeveInstances.push({ id: 1, type: 'fs' });
                     if (typeof renderSleeveInstanceList === 'function') renderSleeveInstanceList();
                }
                $('#cutting-size-table-wrapper').show();
                
                $('#production-stages-table thead th').each(function() {
                    $(this).html($(this).html().replace(' *', ''));
                });
            } else {
                $('#add-stage-row').show();
                $('#cutting-size-table tr.qty-hs-row').show();
                $('#cutting-size-table tr.qty-fs-row td:first').html('<strong>QTY - F/S</strong>');
                $('#process_group_display').closest('.col-md-6').show();
                $('select[name="sleeve_types[]"]').closest('.col-md-6').show();
                $('#sleeve-instance-manager').show();
                
                $('#production-stages-table thead th').each(function() {
                    let text = $(this).text().trim();
                    if (['STAGE', 'ISSUE UNIT (PLANT)', 'RATE', 'ISSUE DATE', 'DEADLINE DATE'].includes(text) && !text.includes('*')) {
                        $(this).html(text + ' *');
                    }
                });
            }
        }
        
        $('#brand').on('change', function() {
            applyCanvasLogic();
        });
        
        setTimeout(applyCanvasLogic, 500);

    });
</script>
<style>
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance: textfield;
    }

    .ui-autocomplete {
        max-height: 250px;
        overflow-y: auto;
        overflow-x: hidden;
        z-index: 1050;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 6px !important;
        background: white !important;
        padding: 8px 0 !important;
    }

    .ui-menu-item {
        margin: 0 !important;
        padding: 0 !important;
    }

    .ui-menu-item .ui-menu-item-wrapper {
        padding: 0 !important;
        border: none !important;
    }

    .ui-menu-item .ui-menu-item-wrapper.ui-state-active {
        background: transparent !important;
        color: inherit !important;
        border: none !important;
    }

    .autocomplete-custom-item {
        background: #ffffff !important;
        border: 1px solid #e0e0e0 !important;
    }

    .ui-state-active .autocomplete-custom-item, .autocomplete-custom-item:hover {
        background-color: #f0f2f5 !important;
        border-color: #c0c0c0 !important;
    }
</style>
@endsection
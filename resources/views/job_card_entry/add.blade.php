@extends('layouts.common')
@section('title', ($jobCard ? 'Edit Job Card' : 'Add Job Card') . ' - ' . env('WEBSITE_NAME'))
@use('App\Models\StockEntry')
@section('content')

@php
    $matrixRows = old('article_matrix', $jobCard ? $jobCard->fabricDetails->toArray() : []);
    $matrixItems = old('matrix_items', $jobCard ? $jobCard->cuttingSizeRatios->toArray() : []);
    
    $dynamicSizes = [];
    foreach($matrixItems as $item) {
        if (!empty($item['size'])) {
            $dynamicSizes[] = $item['size'];
        }
    }
    
    $sizes = !empty($dynamicSizes) ? array_values(array_unique($dynamicSizes)) : ['36', '38', '40', '42', '44'];
    $ratios = [];
    foreach($sizes as $s) {
        $found = false;
        foreach($matrixItems as $item) {
            if (($item['size'] ?? '') == $s) {
                $ratios[] = $item['ratio'] ?? '';
                $found = true;
                break;
            }
        }
        if (!$found) $ratios[] = '';
    }
    
    $fabrics = old('fabrics', $jobCard ? $jobCard->fabricDetails->toArray() : []);
    
    $activeFs = [];
    $activeHs = [];
    foreach($matrixItems as $item) {
        $s = $item['size'] ?? '';
        if ($s) {
            if ((float)($item['qty_fs'] ?? 0) > 0) $activeFs[] = $s;
            if ((float)($item['qty_hs'] ?? 0) > 0) $activeHs[] = $s;
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
@endphp
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="{{ url('job_card_entries/add/'. ($jobCard ?  $jobCard->id : '')) }}" method="POST" class="common-form" enctype="multipart/form-data" autocomplete="off">
                @csrf

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
                                        $oldStockEntryIds = is_string($rawOldStockEntryIds) ? json_decode($rawOldStockEntryIds, true) : $rawOldStockEntryIds;
                                        if(!is_array($oldStockEntryIds)) $oldStockEntryIds = [];
                                    @endphp
                                    @if(!empty($oldStockEntryIds))
                                        @foreach($oldStockEntryIds as $seId)
                                            <input type="hidden" name="stock_entry_ids[]" value="{{ $seId }}">
                                        @endforeach
                                    @endif
                                </div>
                                @error('stock_entry_ids') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            @if(!empty($oldStockEntryIds))
                            @php
                                $issuedQtys = [];
                                if ($jobCard) {
                                    $issuedQtys = \App\Models\JobCardIssueItem::where('job_card_entry_id', $jobCard->id)->with('fabricDetail')->get()
                                        ->groupBy(function($item) {
                                            return $item->fabricDetail->art_no ?? '';
                                        })->map(function($items) {
                                            return $items->sum('qty_issue');
                                        });
                                }

                                $rawIds = $oldStockEntryIds;
                                $seIds = [];
                                $filters = [];
                                foreach ($rawIds as $combinedId) {
                                    if (strpos($combinedId, '::') !== false) {
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
                                
                                $initialEntries = [];
                                if (!empty($seIds)) {
                                    $stockEntries = StockEntry::with('stockEntryItems.rawMaterial','stockEntryItems.uom')->whereIn('id', array_unique($seIds))->get();
                                        
                                    foreach ($stockEntries as $se) {
                                        if (!isset($filters[$se->id])) {
                                            $names = [];
                                            $qtys = [];
                                            $artNos = [];
                                            foreach ($se->stockEntryItems as $item) {
                                                if ($item->art_no && !in_array($item->art_no, $artNos)) { $artNos[] = $item->art_no; }
                                                $name = 'Unknown';
                                                if ($item->raw_material_id && $item->rawMaterial) { $name = $item->rawMaterial->name; }
                                                elseif ($item->item_id && $item->item) { $name = $item->item->name; }
                                                elseif ($item->art_no) { $name = $item->art_no; }
                                                
                                                if (!in_array($name, $names)) { $names[] = $name; }
                                                $uom = $item->uom->uom_code ?? '';
                                                $alreadyIssued = (float)($issuedQtys[$item->art_no ?? ''] ?? 0);
                                                $netQty = ($item->qty_in - ($item->qty_out ?? 0)) + $alreadyIssued;
                                                
                                                if (!isset($qtys[$uom])) { $qtys[$uom] = 0; }
                                                $qtys[$uom] += $netQty;
                                            }
                                            $nameStr = implode(', ', $names);
                                            $artNoStr = implode(', ', $artNos);
                                            $qtyStrs = [];
                                            foreach ($qtys as $uom => $qty) { $qtyStrs[] = round($qty, 3) . ' ' . $uom; }
                                            $initialEntries[] = [
                                                'id'   => $se->id,
                                                'text' => $se->stock_entry_no . ($artNoStr ? ' | ' . $artNoStr : '') . ' | ' . $nameStr . ' | Qty: ' . implode(', ', $qtyStrs),
                                            ];
                                        } else {
                                            $addedCombos = [];
                                            foreach ($filters[$se->id] as $f) {
                                                if (in_array($f['combined'], $addedCombos)) continue;
                                                
                                                $name = 'Unknown';
                                                $artNo = '';
                                                $uom = '';
                                                $netQty = 0;
                                                foreach ($se->stockEntryItems as $item) {
                                                    $match = false;
                                                    if ($f['type'] === 'rm' && $item->raw_material_id == $f['val']) { $name = $item->rawMaterial->name ?? 'Unknown'; $artNo = $item->art_no; $match = true; }
                                                    if ($f['type'] === 'item' && $item->item_id == $f['val']) { $name = $item->item->name ?? 'Unknown'; $artNo = $item->art_no; $match = true; }
                                                    if ($f['type'] === 'art' && $item->art_no == $f['val']) { $name = $item->rawMaterial->name ?? ($item->item->name ?? $item->art_no); $artNo = $item->art_no; $match = true; }
                                                    
                                                    if ($match) {
                                                        $uom = $item->uom->uom_code ?? '';
                                                        $alreadyIssued = (float)($issuedQtys[$item->art_no ?? ''] ?? 0);
                                                        $netQty += ($item->qty_in - ($item->qty_out ?? 0)) + $alreadyIssued;
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
                            @endphp
                            <script>
                            window._initialStockEntries = @json($initialEntries);
                            </script>
                            @endif
                            <input type="hidden" name="purchase_order_id" value="{{ old('purchase_order_id', $jobCard ? $jobCard->purchase_order_id : '') }}">
                            <input type="hidden" name="fabric_type_id" id="fabric_type_id" value="{{ old('fabric_type_id', $jobCard ? $jobCard->fabric_type_id : '') }}">
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
                                @if($jobCard)
                                <small class="text-muted"><i class="ri ri-information-line"></i> Process Group is read-only when editing</small>
                                @endif
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
                                    <select id="status" name="status" class="form-select select2" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Production Hold" {{ (old('status', $jobCard ? $jobCard->status : '') == 'Production Hold') ? 'selected' : '' }}>Production Hold</option>
                                        <option value="Production In Progress" {{ (old('status', $jobCard ? $jobCard->status : '') == 'Production In Progress') ? 'selected' : '' }}>Production In Progress</option>
                                        <option value="Production Completed" {{ (old('status', $jobCard ? $jobCard->status : '') == 'Production Completed') ? 'selected' : '' }}>Production Completed</option>
                                    </select>
                                    <label for="status">Status *</label>
                                </div>
                                @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea id="remarks" name="remarks" class="form-control" placeholder="Enter Remarks">{{ old('remarks', $jobCard ? $jobCard->remarks : '') }}</textarea>
                                    <label for="remarks">Remarks</label>
                                </div>
                                @error('remarks') <span class="text-danger">{{ $message }}</span> @enderror
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
                            <table class="table table-bordered table-sm align-middle" id="item-details-table">
                                <thead class="bg-primary">
                                    <tr>
                                        <th class="text-center" style="width: 50px;">S.No</th>
                                        <th>Raw Material Name</th>
                                        <th class="text-center" style="width: 150px;">Total Quantity</th>
                                        <th class="text-center" style="width: 180px;">Quantity Used</th>
                                        <th class="text-center" style="width: 150px;">Quantity Remaining</th>
                                    </tr>
                                </thead>
                                <tbody id="item-details-tbody">
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
                                                            <option value="{{ $os->id }}" {{ ($stage['stage_id'] ?? $stage['operation_stage_id'] ?? '') == $os->id ? 'selected' : '' }}>{{ $os->operation_stage_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('production_stages.' . $index . '.stage_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                                </td>
                                                <td>
                                                    <select name="production_stages[{{ $index }}][service_provider_id]" class="form-select select2 provider-select" data-placeholder="Select Unit" data-selected="{{ $stage['service_provider_id'] ?? '' }}">
                                                        <option value="">Select Unit</option>
                                                    </select>
                                                    @error('production_stages.' . $index . '.service_provider_id') <span class="text-danger small">{{ $message }}</span> @enderror
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
                                                    @if($jobCard)
                                                    @php
                                                        $currentStageId = $stage['stage_id'] ?? $stage['operation_stage_id'] ?? null;
                                                        $taskData = $stageTaskStatus[$currentStageId] ?? null;
                                                        $hasTask = !empty($taskData);
                                                        
                                                        $taskStatus = $taskData['status'] ?? null;
                                                        $taskNo = $taskData['task_no'] ?? null;

                                                        $buttonText = $hasTask ? 'Assigned Task (Task: ' . $taskNo . ')' : 'Assign Task';

                                                        $buttonTitle = $hasTask ? "Task already assigned (Status: $taskStatus)" : 'Assign Task';
                                                    @endphp
                                                    <button type="button" class="btn btn-sm btn-outline-primary assign-task-btn ms-1" title="{{ $buttonTitle }}" {{ $hasTask ? 'disabled' : '' }}><i class="ri ri-task-line"></i> {{ $buttonText }}</button>
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
                                                        <option value="{{ $os->id }}">{{ $os->operation_stage_name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('production_stages.0.stage_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                            </td>
                                            <td>
                                                <select name="production_stages[0][service_provider_id]" class="form-select select2 provider-select" data-placeholder="Select Unit">
                                                    <option value="">Select Unit</option>
                                                </select>
                                                @error('production_stages.0.service_provider_id') <span class="text-danger small">{{ $message }}</span> @enderror
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
                                    
                                    {{-- 
                                    <hr class="my-3">
                                    <label class="d-block mb-2 fw-bold text-secondary small">Add Extra Size (Optional)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" id="extra_size_input" class="form-control" placeholder="e.g. 36 or 48">
                                        <button class="btn btn-dark" type="button" id="add-extra-size-btn">
                                            <i class="ri ri-add-line me-1"></i> Add Size
                                        </button>
                                    </div>
                                    --}}
                                </div>
                                @error('sleeve_types') <span class="text-danger">{{ $message }}</span> @enderror
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
                                                foreach($matrixItems as $item) {
                                                    if (($item['size'] ?? '') == $s) {
                                                        $val = $item['qty_fs'] ?? '';
                                                        break;
                                                    }
                                                }
                                            @endphp
                                            <td>
                                                <input type="number" name="matrix_items[{{ $idx }}][qty_fs]" class="form-control form-control-sm text-center fw-bold qty-direct-input fs-summary-{{ $s }}" data-type="fs" data-size="{{ $s }}" value="{{ $val ? (int)$val : '' }}" {{ $hasTasks ? 'readonly' : '' }}>
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
                                            <input type="text" name="matrix_items_info[fs]" class="form-control form-control-sm text-center text-muted" value="{{ $sizeStr }}">
                                        </td>
                                        <td class=""></td><td class=""></td><td></td><td></td>
                                    </tr>

                                    {{-- QTY - H/S ROW --}}
                                    <tr class="qty-hs-row">
                                        <td><strong>QTY - H/S</strong></td>
                                        @foreach($sizes as $idx => $s)
                                            @php
                                                $val = '';
                                                foreach($matrixItems as $item) {
                                                    if (($item['size'] ?? '') == $s) {
                                                        $val = $item['qty_hs'] ?? '';
                                                        break;
                                                    }
                                                }
                                            @endphp
                                            <td>
                                                <input type="number" name="matrix_items[{{ $idx }}][qty_hs]" class="form-control form-control-sm text-center fw-bold qty-direct-input hs-summary-{{ $s }}" data-type="hs" data-size="{{ $s }}" value="{{ $val ? (int)$val : '' }}" {{ $hasTasks ? 'readonly' : '' }}>
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
                                                <td><input type="text" name="fabrics[{{ $index }}][mtr]" class="form-control form-control-sm text-center" value="{{ $fabric['mtr'] ?? '' }}"></td>
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
                <h5 class="modal-title text-white" id="processGroupModalLabel">{{ $jobCard ? 'view process-groups (Read-Only)' : 'Select Process Group' }}</h5>
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
                            <td><input type="radio" name="process_option" value="{{ $pg->id }}" data-name="{{ $pg->name }}" {{ $jobCard ? 'disabled' : '' }} {{ ($jobCard && $jobCard->process_group_id == $pg->id) ? 'checked' : '' }}></td>
                            <td>{{ explode(' - ', $pg->name)[0] }}</td>
                            <td>{{ count(explode(' - ', $pg->name)) > 1 ? explode(' - ', $pg->name)[1] : $pg->name }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $jobCard ? 'Close' : 'Cancel' }}</button>
                @if(!$jobCard)
                    <button type="button" class="btn btn-primary" id="confirmProcessGroup">Select</button>
                @endif
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
                    <div class="table-responsive rounded-4 overflow-hidden" style="border: 1px solid #f0f0f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
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
        const rawOldMatrix = @json(old('article_matrix', []));
        const oldMatrix = Array.isArray(rawOldMatrix) ? rawOldMatrix : Object.values(rawOldMatrix);
        const rawExistingMatrix = @json($jobCard && $jobCard->fabricDetails ? $jobCard->fabricDetails : []);
        const existingMatrix = Array.isArray(rawExistingMatrix) ? rawExistingMatrix : Object.values(rawExistingMatrix);
        const validationErrors = @json($errors->toArray());
        
        const rawOldFabrics = @json(old('fabrics', []));
        const oldFabrics = Array.isArray(rawOldFabrics) ? rawOldFabrics : Object.values(rawOldFabrics);

        const hasTasks = @json($hasTasks);
        const existingImages = @json($jobCard && $jobCard->images ? $jobCard->images : []);
        const matrixItems = @json(old('matrix_items', $jobCard && $jobCard->cuttingSizeRatios ? $jobCard->cuttingSizeRatios : []));
        const isEditMode = {{ $jobCard ? 'true' : 'false' }};
        let globalActiveSizes = { fs: [], hs: [] };
        let isSyncing = false;
        let currentArtNumbers = [];
        const phpFabrics = @json($fabrics);
        if (oldFabrics && Object.keys(oldFabrics).length > 0) {
            const uniqueArts = [...new Set(Object.values(oldFabrics).map(f => f.art_no))];
            currentArtNumbers = uniqueArts.filter(Boolean);
        } else if (phpFabrics && phpFabrics.length > 0) {
            currentArtNumbers = [...new Set(phpFabrics.map(f => f.art_no))];
        }
        const articleUoms = @json(collect($fabrics)->pluck('uom_code', 'art_no')) || {};
        let currentArtData = (@json($fabrics) || []).map(f => ({
            art_no: f.art_no,
            mtr: f.total_qty || f.mtr,
            already_issued: f.used_qty || f.mtr,
            art_name: f.art_no
        }));
        let currentSizes = ['36', '38', '40', '42', '44'];
        let currentRatios = ['', '', '', '', ''];
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
        if (matrixItems && matrixItems.length > 0) {
            let hasFs = matrixItems.some(i => (parseFloat(i.qty_fs) || 0) > 0);
            let hasHs = matrixItems.some(i => (parseFloat(i.qty_hs) || 0) > 0);
            if (hasFs) {
                const id = Date.now() + Math.random();
                sleeveInstances.push({ id: id, type: 'fs' });
            }
            if (hasHs) {
                const id = Date.now() + Math.random();
                sleeveInstances.push({ id: id, type: 'hs' });
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
                    matrixTotal = parseFloat($matrixRow.find('.row-total').val()) || 0;
                    
                    if (data.cat_id == 1) { 
                        matrixTotal = enteredUsed; 
                        calcDetails = `Manual Entry (Quantity Used - Fabric): ${enteredUsed}`;
                    } else { 
                        calcDetails = `Matrix Auto-Calculated Total (Accessories): ${matrixTotal}`;
                    }
                }

                const finalMatrixTotal = Math.round(matrixTotal * 1000) / 1000;
                const finalEnteredUsed = Math.round(enteredUsed * 1000) / 1000;
                const finalIssued = Math.round(data.issued * 1000) / 1000;

                if (data.cat_id != 1 && finalMatrixTotal > finalEnteredUsed && finalEnteredUsed < finalIssued) {
                    isValid = false;
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: `For Article ${art}, the Article Quantity Matrix Total (${finalMatrixTotal}) is greater than the Quantity Used (${finalEnteredUsed}). Quantity Used must be greater than or equal to the Matrix Total.`,
                    });
                    discrepancies = []; 
                    errors = [];
                    return false; 
                }

                if (finalMatrixTotal > finalIssued) {
                    isValid = false;
                    errors.push({
                        art: art, required: finalMatrixTotal, issued: finalIssued, calc: calcDetails,
                        cat: data.cat_id || '', mat_id: data.mat_id || '', grn_no: data.grn_no || '',
                        already_issued: data.already_issued || 0
                    });
                }
            }

            if (!isValid || errors.length > 0) {
                console.log("Validation Failed", errors);
                const $tbody = $('#stockErrorTable tbody').empty();
                errors.forEach(err => {
                    const artInfo = currentArtData.find(item => String(item.art_no).trim() === String(err.art).trim());
                    discrepancies.push({
                        art: err.art,
                        name: artInfo ? artInfo.art_name : 'Raw Material/Fabric',
                        needed: err.required,
                        issued: err.issued,
                        gap: err.required - err.issued,
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
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Stock Updated!',
                        text: 'Everything matches! Saving your Job Card automatically...',
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true
                    }).then(() => {
                        $('form.common-form').attr('data-skip-validation', 'true').submit();
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
            $('.text-danger.small.fw-bold, .backend-error').hide();

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
                        $form.off('submit').submit(); 
                    } else {
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                })
                .catch(err => {
                    alert('Stock validation failed. Please check connection or console.');
                    $btn.prop('disabled', false).html(originalHtml);
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

                    if (!hasFabric) {
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
                    $.get(searchUrl, { q: request.term }, function (data) {
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
                        `)
                        .appendTo(ul);
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
                    if (!selectedIds.includes(entry.id)) {
                        selectedIds.push(entry.id);
                    }
                    if ($tags.find(`[data-id="${entry.id}"]`).length === 0) {
                        const removeButton = hasTasks ? '' : `<button type="button" class="btn-close btn-close-white ms-1" style="font-size:8px;" data-remove="${entry.id}" title="Remove"></button>`;
                        const $tag = $(`
                            <span class="badge bg-primary d-inline-flex align-items-center gap-1 px-2 py-1 fs-6"
                                  data-id="${entry.id}" style="cursor:default; max-width:300px; white-space:normal;">
                                <span style="font-size:11px; line-height:1.3;">${entry.text}</span>
                                ${removeButton}
                            </span>`);
                        $tags.append($tag);
                    }
                });
                if (selectedIds.length > 0) {
                    const jobCardId = '{{ $jobCard ? $jobCard->id : "" }}';
                    $.get(detailsUrl, { ids: selectedIds, job_card_id: jobCardId }, function (data) {
                        currentArtNumbers = data.art_numbers;
                        currentArtData    = data.art_data;
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
                            syncMatrixWithMasterTable(false);
                            renderArticleQtyMatrix(currentArtNumbers, globalActiveSizes.fs, globalActiveSizes.hs);
                            updateQuantityRowVisibility();
                        }
                    });
                }
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
                    syncMatrixWithMasterTable(false);
                    renderArticleQtyMatrix(currentArtNumbers, globalActiveSizes.fs, globalActiveSizes.hs);
                    updateQuantityRowVisibility();
                }
            });
        }

        function renderArticleQtyMatrix(artNumbers, activeFsSizes = [], activeHsSizes = []) {
            const $table = $('#article-qty-matrix');
            const $thead = $table.find('thead');
            const $tbody = $('#article-qty-matrix-body');
            const $tfoot = $table.find('tfoot');

            $thead.empty();
            $tbody.empty();
            $tfoot.empty();

            if (!artNumbers || artNumbers.length === 0) return;

            const headHtml = `
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
                const existingRow = isEditMode && existingMatrix.length > 0 ? existingMatrix.find(r => String(r.art_no).trim() == String(art).trim()) : null;
                const oldRow = oldMatrix && oldMatrix.length > 0 ? (oldMatrix.find(r => String(r.art_no).trim() == String(art).trim()) || oldMatrix[index]) : null;
                
                let uom = (articleUoms[art] || 'PCS').toUpperCase();
                let artName = '';
                let catId = 1;

                if (currentArtData && currentArtData.length > 0) {
                    const d = currentArtData.find(d => String(d.art_no).trim() == String(art).trim());
                    if (d) {
                        artName = d.art_name || '';
                        uom = (d.uom_code || uom).toUpperCase();
                        catId = d.store_category_id || 1;
                    }
                }
                
                const isTaskReadOnly = hasTasks ? 'readonly tabindex="-1"' : '';
                const readonlyAttr = (catId != 1) ? 'readonly tabindex="-1"' : isTaskReadOnly;
                const rowClass = (catId != 1) ? 'cat2-row' : 'cat1-row';

                let rowHtml = `<tr class="${rowClass}" data-uom="${uom}" data-art="${art}" data-category="${catId}">
                                <td>
                                    <div class="border rounded p-1 mb-1 text-center fw-bold small" style="background: #f8f9fa;">${art}</div>
                                    <input type="hidden" name="article_matrix[${index}][art_no]" value="${art}">
                                    <div class="small text-muted text-center" style="font-size: 10px; line-height: 1.1;">${artName}</div>
                                </td>`;
                
                activeFsSizes.forEach(s => {
                    let fsVal = '';
                    if (oldRow && oldRow[`fs_${s}`] !== undefined) fsVal = oldRow[`fs_${s}`];
                    else if (existingRow && existingRow.quantities) {
                        const q = existingRow.quantities.find(q => String(q.size) === String(s));
                        fsVal = (q && q.qty_fs != null) ? parseFloat(q.qty_fs) : '';
                    }
                    rowHtml += `<td><input type="number" name="article_matrix[${index}][fs_${s}]" class="form-control form-control-sm qty-input text-center" data-col="fs-${s}" data-art="${art}" value="${fsVal}" ${readonlyAttr}></td>`;
                });

                activeHsSizes.forEach(s => {
                    let hsVal = '';
                    if (oldRow && oldRow[`hs_${s}`] !== undefined) hsVal = oldRow[`hs_${s}`];
                    else if (existingRow && existingRow.quantities) {
                        const q = existingRow.quantities.find(q => String(q.size) === String(s));
                        hsVal = (q && q.qty_hs != null) ? parseFloat(q.qty_hs) : '';
                    }
                    rowHtml += `<td><input type="number" name="article_matrix[${index}][hs_${s}]" class="form-control form-control-sm qty-input text-center" data-col="hs-${s}" data-art="${art}" value="${hsVal}" ${readonlyAttr}></td>`;
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

            calculateMatrixTotals();
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
                        cat1ColSums[col] = (cat1ColSums[col] || 0) + val;
                        
                        if (col.startsWith('fs')) rowFS += val;
                        else if (col.startsWith('hs')) rowHS += val;
                        rowTotal += val;
                    });
                    $row.find('.row-total').val(rowTotal > 0 ? (rowTotal % 1 === 0 ? rowTotal : rowTotal.toFixed(2)) : '');
                    
                    $(`input[name="fabrics[${index}][fs_qty]"]`).val(rowFS > 0 ? rowFS : '');
                    $(`input[name="fabrics[${index}][hs_qty]"]`).val(rowHS > 0 ? rowHS : '');
                });

                $('#article-qty-matrix-body tr').each(function() {
                    const $row = $(this);
                    if ($row.hasClass('cat1-row')) return; 

                    const art = $row.data('art');
                    let rowTotal = 0;

                    $row.find('.qty-input').each(function() {
                        const col = $(this).data('col');
                        const parts = col.split('-');
                        if (parts.length >= 2) {
                            const type = parts[0];
                            const size = parts[1];

                            const pieces = cat1ColSums[col] || 0;
                            const cons = getConsumptionValue(art, type, size);
                            const calcVal = pieces * cons;

                            $(this).val(calcVal > 0 ? (calcVal % 1 === 0 ? calcVal : calcVal.toFixed(2)) : '');
                            rowTotal += (parseFloat($(this).val()) || 0);
                        }
                    }); 
                    
                    const finalRowTotal = rowTotal > 0 ? (rowTotal % 1 === 0 ? rowTotal : rowTotal.toFixed(2)) : '';
                    $row.find('.row-total').val(finalRowTotal);
                    
                    const $itemUsed = $(`.item-used-input[data-art="${art}"]`);
                    if ($itemUsed.length && (!$itemUsed.val() || parseFloat($itemUsed.val()) == 0)) {
                        $itemUsed.val(finalRowTotal).trigger('input');
                    }
                });

                const $table = $('#article-qty-matrix');
                let totalFS = 0;
                let totalHS = 0;
                let grandTotal = 0;

                $table.find('.col-total').each(function() {
                    const col = $(this).data('col');
                    let sum = 0;
                    sum = cat1ColSums[col] || 0;
                    
                    $(this).text(sum > 0 ? (sum % 1 === 0 ? sum : sum.toFixed(2)) : ''); 
                    
                    if (col.startsWith('fs')) totalFS += sum;
                    else if (col.startsWith('hs')) totalHS += sum;
                    grandTotal += sum;
                });

                $('#article-qty-matrix-grand-total').text(grandTotal > 0 ? (grandTotal % 1 === 0 ? grandTotal : grandTotal.toFixed(2)) : '');

                $('#total_qty_fs').val(totalFS > 0 ? Math.round(totalFS) : '');
                $('#total_qty_hs').val(totalHS > 0 ? Math.round(totalHS) : '');
                $('.total-summary-fs').text(totalFS > 0 ? Math.round(totalFS) : '0');
                $('.total-summary-hs').text(totalHS > 0 ? Math.round(totalHS) : '0');
                
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

        renderSleeveInstanceList();
        renderCuttingSizeTable(currentSizes, currentRatios);
        syncMatrixWithMasterTable(false); 
        updateQuantityRowVisibility();

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

        $('#add-extra-size-btn').on('click', function() {
            const val = $('#extra_size_input').val().trim();
            if (val && !currentSizes.includes(val)) {
                currentSizes.push(val);
                currentSizes.sort((a, b) => {
                    const numA = parseFloat(a);
                    const numB = parseFloat(b);
                    if (!isNaN(numA) && !isNaN(numB)) return numA - numB;
                    return String(a).localeCompare(String(b));
                });
                $('#extra_size_input').val('');
                renderCuttingSizeTable(currentSizes, currentRatios);
                syncMatrixWithMasterTable(true);
            } else if (currentSizes.includes(val)) {
                $('#extra_size_input').val('');
            }
        });

        $('#extra_size_input').on('keypress', function(e) {
            if (e.which == 13) {
                e.preventDefault();
                $('#add-extra-size-btn').click();
            }
        });

        function addSleeveInstance(type) {
            captureSleeveValues();
            const id = Date.now() + Math.random();
            sleeveInstances.push({ id, type });
            renderSleeveInstanceList();
            renderCuttingSizeTable(currentSizes, currentRatios);
            syncMatrixWithMasterTable(true);
            updateQuantityRowVisibility();
        }

        $(document).on('click', '.remove-sleeve-instance', function() {
            captureSleeveValues();
            const id = $(this).data('instance-id');
            sleeveInstances = sleeveInstances.filter(i => i.id != id);
            if (sleeveValues[id]) delete sleeveValues[id];
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

        function renderFabricDetails() {
            const $tbody = $('#fabric-details-body');
            const $thead = $('#fabric-details-head');
            
            const currentManualMtr = {};
            $tbody.find('.mtr-input').each(function() {
                const art = $(this).data('art');
                if (art) currentManualMtr[art] = $(this).val();
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

            currentArtNumbers.forEach((art, index) => {
                let existingImagesHtml = '';
                if (isEditMode && existingImages.length > 0) {
                    existingImagesHtml = '<div class="d-flex flex-wrap gap-2 mb-2">';
                    existingImages.forEach(img => {
                        if (img.art_no == art) {
                            existingImagesHtml += `
                                <div class="position-relative" style="width: 80px; height: 80px;">
                                    <img src="{{ url('/') }}/${img.image}" class="img-thumbnail" style="width: 100%; height: 100%; object-fit: cover;">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" style="padding: 2px 6px; font-size: 10px;" onclick="deleteImage(${img.id})">
                                        <i class="ri ri-close-line"></i>
                                    </button>
                                </div>`;
                        }
                    });
                    existingImagesHtml += '</div>';
                }
                
                headHtml += `<th colspan="2" class="bg-light">
                    <div class="p-2">
                        <label class="small text-primary fw-bold">Image</label>
                        ${existingImagesHtml}
                        <input type="file" class="form-control form-control-sm" name="fabric_images[${index}][]" multiple accept="image/*">
                        <input type="hidden" name="fabrics[${index}][fs_qty]" value="">
                        <input type="hidden" name="fabrics[${index}][hs_qty]" value="">
                        <input type="hidden" name="fabrics[${index}][total_qty]" class="total-qty-hidden" data-art="${art}" value="">
                        <input type="hidden" name="fabrics[${index}][used_qty]" class="used-qty-hidden" data-art="${art}" value="">
                        <input type="hidden" name="fabrics[${index}][remaining_qty]" class="remaining-qty-hidden" data-art="${art}" value="">
                    </div>
                </th>`;
                artRow += `<td class="fw-bold">ART NO</td><td><input type="text" name="fabrics[${index}][art_no]" class="form-control form-control-sm text-center art-no-input" value="${art}" readonly></td>`;
                
                let vWidth = (oldFabrics && oldFabrics[index] && oldFabrics[index]['width']) ? oldFabrics[index]['width'] : '';
                let vMtr = (oldFabrics && oldFabrics[index] && oldFabrics[index]['mtr']) ? oldFabrics[index]['mtr'] : '';
                let vInOut = (oldFabrics && oldFabrics[index] && oldFabrics[index]['in_out']) ? oldFabrics[index]['in_out'] : '';
                let vNPatti = (oldFabrics && oldFabrics[index] && oldFabrics[index]['n_patti']) ? oldFabrics[index]['n_patti'] : '';

                if (!vWidth && existingMatrix.length > 0) {
                    const m = existingMatrix.find(m => m.art_no == art);
                    if (m) {
                        vWidth = m.width || '';
                        vMtr = m.mtr || '';
                        vInOut = m.in_out || '';
                        vNPatti = m.n_patti || '';
                    }
                }

                if (!vInOut) vInOut = 'NO';
                if (!vNPatti) vNPatti = 'WHITE';

                if (currentManualMtr[art] !== undefined && currentManualMtr[art] !== '') {
                    vMtr = currentManualMtr[art];
                } else if (!vMtr && currentArtData && currentArtData.length > 0) {
                    const d = currentArtData.find(d => d.art_no == art);
                    if (d) {
                        vMtr = d.mtr || '';
                    }
                }

                const isTaskReadOnly = hasTasks ? 'readonly' : '';
                widthRow += `<td class="fw-bold">WIDTH</td><td><input type="text" name="fabrics[${index}][width]" class="form-control form-control-sm text-center" value="${vWidth}" ${isTaskReadOnly}></td>`;
                mtrRow += `<td class="fw-bold">ISSUED METERS</td><td><input type="text" name="fabrics[${index}][mtr]" class="form-control form-control-sm text-center mtr-input" data-art="${art}" value="${vMtr}" ${isTaskReadOnly}>${validationErrors[`fabrics.${index}.mtr`] ? `<div class="text-danger small mt-1" style="font-size: 11px;">${validationErrors[`fabrics.${index}.mtr`][0]}</div>` : ''}</td>`;
                inOutRow += `<td class="fw-bold">IN/OUT</td><td><input type="text" name="fabrics[${index}][in_out]" class="form-control form-control-sm text-center" value="${vInOut}" ${isTaskReadOnly}></td>`;
                nPattiRow += `<td class="fw-bold">N.PATTI</td><td><input type="text" name="fabrics[${index}][n_patti]" class="form-control form-control-sm text-center" value="${vNPatti}" ${isTaskReadOnly}></td>`;
                
                let uom = '';
                let catId = 0;
                if (currentArtData && currentArtData.length > 0) {
                    const d = currentArtData.find(d => d.art_no == art);
                    if (d) {
                        uom = d.uom_code || '';
                        catId = d.store_category_id || 0;
                    }
                }

                let sizes = [];
                if (typeof globalActiveSizes !== 'undefined') {
                    sizes = [...new Set([...(globalActiveSizes.fs || []), ...(globalActiveSizes.hs || [])])].sort((a,b) => parseFloat(a)-parseFloat(b) || String(a).localeCompare(String(b)));
                } else if (typeof currentSizes !== 'undefined') {
                    sizes = currentSizes;
                }
                
                let sizeTableHtml = '';
                if (sizes.length > 0) {
                    sizeTableHtml = `<table class="table table-bordered table-sm mb-0 mt-1" style="font-size: 11px;">
                        <thead class="bg-light"><tr><th>Size</th><th>F/S Cons</th><th>H/S Cons</th></tr></thead>
                        <tbody>`;
                    
                    sizes.forEach(sz => {
                        let vSzFs = '';
                        let vSzHs = '';
                        
                        if (oldFabrics && oldFabrics[index] && oldFabrics[index]['consumptions'] && oldFabrics[index]['consumptions'][sz]) {
                            vSzFs = oldFabrics[index]['consumptions'][sz]['fs_cons'] || '';
                            vSzHs = oldFabrics[index]['consumptions'][sz]['hs_cons'] || '';
                        }
                        
                        if (!vSzFs && isEditMode && existingMatrix && existingMatrix.length > 0) {
                            const m = existingMatrix.find(m => m.art_no == art);
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
            $tbody.append(sleeveQtyRow + '</tr>');
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
                        <input type="number" name="matrix_items[${idx}][qty_${type}]" class="form-control form-control-sm text-center fw-bold qty-direct-input ${type}-summary-${s}" data-type="${type}" data-size="${s}" data-instance="${instanceId}" value="${finalVal}">
                        <input type="hidden" name="matrix_items[${idx}][size]" value="${s}">
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
            syncMatrixWithMasterTable(true);
        });

        function syncMatrixWithMasterTable(populateValues = true, reRenderFabric = true, targetType = null, targetSize = null) {
            if (isSyncing) return { fs: [], hs: [] };
            isSyncing = true;
            let activeFsSizes = [];
            let activeHsSizes = [];
            try {
                const hasFS = sleeveInstances.some(i => i.type === 'fs');
                const hasHS = sleeveInstances.some(i => i.type === 'hs');

                if (hasFS) activeFsSizes = [...currentSizes];
                if (hasHS) activeHsSizes = [...currentSizes];

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

                                $('table[id^="article-qty-matrix"] tbody tr.cat1-row .qty-input').filter(function() {
                                    return $(this).data('col') === `${type}-${size}`;
                                }).each(function() {
                                    const uom = ($(this).closest('tr').attr('data-uom') || '').toUpperCase();
                                    let finalCalc = totalVal > 0 ? (uom === 'PCS' ? Math.round(totalVal).toString() : totalVal.toString()) : '';
                                    
                                    if ($(this).val() != finalCalc) {
                                        $(this).val(finalCalc);
                                    }
                                });
                            });
                        });
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
                isSyncing = false;
            }

            return { fs: activeFsSizes, hs: activeHsSizes };
        }

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
                            <option value="{{ $os->id }}">{{ $os->operation_stage_name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="production_stages[${stageRowIndex}][service_provider_id]" class="form-select select2 provider-select" data-placeholder="Select Unit">
                        <option value="">Select Unit</option>
                    </select>
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

        $(document).on('click', '.remove-stage-row', function() {
            if ($('#production-stages-table tbody tr').length > 1) {
                $(this).closest('tr').remove();
            } else {
                alert('At least one row is required.');
            }
        });

        $(document).on('change', '.stage-select', function() {
            let $row = $(this).closest('tr');
            let stageId = $(this).val();
            let $providerSelect = $row.find('.provider-select');
            let $issueDate = $row.find('.issue-date');

            $providerSelect.prop('disabled', true).html('<option value="">Loading...</option>').trigger('change.select2');

            if (stageId) {
                $.ajax({
                    url: `{{ url('get-service-providers-by-stage') }}/${stageId}`,
                    type: 'GET',
                    success: function(response) {
                        $providerSelect.prop('disabled', false).html('<option value="">Select Unit</option>');
                        if (response.success && response.providers) {
                            response.providers.forEach(p => {
                                let selected = ($providerSelect.data('selected') == p.id) ? 'selected' : '';
                                $providerSelect.append(`<option value="${p.id}" ${selected}>${p.name}</option>`);
                            });
                        }
                        $providerSelect.trigger('change.select2');
                    },
                    error: function() {
                        $providerSelect.prop('disabled', false).html('<option value="">Error loading units</option>').trigger('change.select2');
                    }
                });
                if ($issueDate.val()) {
                    $issueDate.trigger('change');
                }
            } else {
                $providerSelect.prop('disabled', false).html('<option value="">Select Unit</option>').trigger('change.select2');
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
            let issueDate = $row.find('.issue-date').val();
            let deadlineDate = $row.find('.deadline-date').val();
            let remarks = $row.find('textarea').val();
            let jobCardId = '{{ $jobCard ? $jobCard->id : "" }}';

            if (!stageId) {
                alert('Please select a Stage before assigning.');
                return;
            }

            let baseUrl = '{{ route("task_management.add") }}';
            let params = new URLSearchParams({
                job_card_id: jobCardId,
                stage_id: stageId,
                issue_date: issueDate,
                due_date: deadlineDate,
                remarks: remarks
            });
            window.open(baseUrl + '?' + params.toString(), '_blank');
        });

        function renderItemDetailsTable(artData) {
            const $wrapper = $('#item-details-table-wrapper');
            const $tbody = $('#item-details-tbody');
            const $msg = $('#no-materials-msg');
            const currentManualUsed = {};
            $tbody.find('.item-used-input').each(function() {
                const art = $(this).data('art');
                if (art) currentManualUsed[art] = $(this).val();
            });

            $tbody.empty();
            
            if (!artData || artData.length === 0) {
                $wrapper.addClass('d-none');
                $msg.removeClass('d-none');
                $('#no-material-text').text('No materials found for selected Stock Entry Number.');
                return;
            }
            
            $wrapper.removeClass('d-none');
            $msg.addClass('d-none');
            
            artData.forEach((item, index) => {
                const total = parseFloat(item.mtr) || 0;
                const artNo = item.art_no;
                
                let usedStr = (currentManualUsed[artNo] !== undefined && currentManualUsed[artNo] !== '') 
                    ? currentManualUsed[artNo] 
                    : (isEditMode && parseFloat(item.already_issued) > 0 ? parseFloat(item.already_issued).toFixed(2) : total.toFixed(2));
                
                const used = parseFloat(usedStr) || 0;
                const remaining = total - used;
                
                $(`.total-qty-hidden[data-art="${artNo}"]`).val(total.toFixed(2));
                $(`.used-qty-hidden[data-art="${artNo}"]`).val(used.toFixed(2));
                $(`.remaining-qty-hidden[data-art="${artNo}"]`).val(remaining.toFixed(2));

                const row = `
                    <tr data-art="${artNo}">
                        <td class="text-center">${index + 1}</td>
                        <td class="fw-bold">${item.art_name || ''} <br> <small class="text-muted">${artNo}</small></td>
                        <td class="text-center item-total-qty">${total.toFixed(2)}</td>
                        <td>
                            <div class="input-group input-group-sm">
                                <input type="number" step="0.01" class="form-control text-center fw-bold item-used-input" 
                                       data-art="${artNo}" value="${usedStr}">
                                <span class="input-group-text">${item.uom_code || 'MTR'}</span>
                            </div>
                            ${validationErrors[`fabrics.${index}.mtr`] ? `<div class="text-danger small mt-1" style="font-size: 11px;">${validationErrors[`fabrics.${index}.mtr`][0]}</div>` : ''}
                        </td>
                        <td class="text-center item-remaining-qty fw-bold ${remaining < 0 ? 'text-danger' : 'text-success'}">${remaining.toFixed(2)}</td>
                    </tr>
                `;
                $tbody.append(row);
            });
        }

        $(document).on('input', '.item-used-input', function() {
            const $input = $(this);
            const $row = $input.closest('tr');
            const artNo = $input.data('art');
            const total = parseFloat($row.find('.item-total-qty').text()) || 0;
            const used = parseFloat($input.val()) || 0;
            const remaining = total - used;
            
            const $remainingCell = $row.find('.item-remaining-qty');
            $remainingCell.text(remaining.toFixed(2));
    
            if (remaining < 0) {
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

        $(document).on('input', '.mtr-input', function() {
            const $input = $(this);
            const artNo = $input.data('art');
            const val = parseFloat($input.val()) || 0;
            
            const $itemInput = $(`.item-used-input[data-art="${artNo}"]`);
            if ($itemInput.length && parseFloat($itemInput.val()) !== val) {
                $itemInput.val(val.toFixed(2)).trigger('input');
            }
        });
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
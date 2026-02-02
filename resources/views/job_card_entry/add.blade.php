@extends('layouts.common')
@section('title', ($jobCard ? 'Edit Job Card' : 'Add Job Card') . ' - ' . env('WEBSITE_NAME'))
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
            <form action="{{ url('job_card_entries/add/'. ($jobCard ?  $jobCard->id : '')) }}" method="POST" class="common-form" enctype="multipart/form-data">
                @csrf
                {{-- @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>{{ $jobCard ? 'Edit' : 'Add' }} Job Card Entry</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="job_card_no" placeholder="Enter Job Card Number" name="job_card_no" value="{{ old('job_card_no', $jobCard ? $jobCard->job_card_no : '') }}">
                                    <label for="job_card_no">Job Card Number * </label>
                                </div>
                                @error('job_card_no') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="purchase_order" name="purchase_order_id" class="form-select select2" data-placeholder="Select Purchase Order">
                                        <option value="">Select Purchase Order</option>
                                        @foreach($purchaseOrders as $po)
                                            <option value="{{ $po->id }}" {{ (old('purchase_order_id', $jobCard ? $jobCard->purchase_order_id : '') == $po->id) ? 'selected' : '' }}>{{ $po->po_number }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="purchase_order">Purchase Order *</label>
                                </div>
                                @error('purchase_order_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="plant" name="service_provider_id" class="form-select select2" data-placeholder="Select Plant">
                                        <option value="">Select Plant</option>
                                        @foreach($plants as $plant)
                                            <option value="{{ $plant->id }}" {{ (old('service_provider_id', $jobCard ? $jobCard->service_provider_id : '') == $plant->id) ? 'selected' : '' }}>
                                                {{ $plant->name }}
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
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="mrp" placeholder="Enter MRP" name="mrp" value="{{ old('mrp', $jobCard ? $jobCard->mrp : '') }}">
                                    <label for="mrp">MRP</label>
                                </div>
                                @error('mrp') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" class="form-control" id="price_fs" placeholder="Enter Price of F/S" name="price_fs" value="{{ old('price_fs', $jobCard ? $jobCard->price_fs : '') }}">
                                    <label for="price_fs">Price of F/S</label>
                                </div>
                                @error('price_fs') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" class="form-control" id="price_hs" placeholder="Enter Price of H/S" name="price_hs" value="{{ old('price_hs', $jobCard ? $jobCard->price_hs : '') }}">
                                    <label for="price_hs">Price of H/S</label>
                                </div>
                                @error('price_hs') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <input type="hidden" id="total_qty_fs" name="total_qty_fs" value="{{ old('total_qty_fs', $jobCard ? $jobCard->total_qty_fs : '') }}">
                            <input type="hidden" id="total_qty_hs" name="total_qty_hs" value="{{ old('total_qty_hs', $jobCard ? $jobCard->total_qty_hs : '') }}">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="season" name="season_id" class="form-select select2" data-placeholder="Select Season">
                                        <option value="">Select Season</option>
                                        @foreach($seasons as $season)
                                            <option value="{{ $season->id }}" {{ (old('season_id', $jobCard ? $jobCard->season_id : '') == $season->id) ? 'selected' : '' }}>
                                                {{ $season->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="season">Season </label>
                                </div>
                                @error('season_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="brand" name="brand_id" class="form-select select2" data-placeholder="Select Brand">
                                        <option value="">Select Brand</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}" {{ (old('brand_id', $jobCard ? $jobCard->brand_id : '') == $brand->id) ? 'selected' : '' }}>{{ $brand->brand_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="brand">Brand * </label>
                                </div>
                                @error('brand_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="receipt_store_id" name="receipt_store_id" class="form-select select2" data-placeholder="Select Receipt Store">
                                        <option value="">Select Receipt Store</option>
                                        @foreach($storeTypes as $st)
                                            <option value="{{ $st->id }}" {{ (old('receipt_store_id', $jobCard ? $jobCard->receipt_store_id : '') == $st->id) ? 'selected' : '' }}>
                                                {{ $st->store_type_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="receipt_store_id">Receipt Store *</label>
                                </div>
                                @error('receipt_store_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="input-group">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="process_group_display" name="process_group_display" class="form-control" placeholder="Select Process Group" readonly value="{{ old('process_group_display', $jobCard && $jobCard->processGroup ? $jobCard->processGroup->name : '') }}">
                                        <input type="hidden" id="process_group_id" name="process_group_id" value="{{ old('process_group_id', $jobCard ? $jobCard->process_group_id : '') }}">
                                        <label for="process_group_display">Process Group *</label>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#processGroupModal" id="processGroupBtn">
                                        <i class="ri ri-search-line"></i>
                                    </button>
                                </div>
                                @error('process_group_id') <span class="text-danger">{{ $message }}</span> @enderror
                                @if($jobCard)
                                    <small class="text-muted"><i class="ri ri-information-line"></i> Process Group is read-only when editing</small>
                                @endif
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
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Item Details</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="brand_category_id" name="brand_category_id" class="form-select select2" data-placeholder="Select Brand Category">
                                        <option value="">Select Brand Category</option>
                                        @foreach($brandCategories as $cat)
                                            <option value="{{ $cat->id }}" {{ (old('brand_category_id', $jobCard ? $jobCard->brand_category_id : '') == $cat->id) ? 'selected' : '' }}>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="brand_category_id">Brand Category *</label>
                                </div>
                                @error('brand_category_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="item_id" name="item_id" class="form-select select2" data-placeholder="Select Item">
                                        <option value="">Select Item</option>
                                    </select>
                                    <label for="item_id">Item *</label>
                                </div>
                                @error('item_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
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
                                    <select id="fit" name="fit" class="form-select select2" data-placeholder="Select Fit">
                                        <option value="">Select Fit</option>
                                        @foreach($fits as $fit)
                                            <option value="{{ $fit->fit_name }}" {{ (old('fit', $jobCard ? $jobCard->fit : '') == $fit->fit_name) ? 'selected' : '' }}>{{ $fit->fit_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="fit">Fit</label>
                                </div>
                                @error('fit') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="patti_type" name="patti_type" class="form-select select2" data-placeholder="Select Patti Type">
                                        <option value="">Select Patti Type</option>
                                        @foreach($pattiTypes as $type)
                                            <option value="{{ $type->patti_type_name }}" {{ (old('patti_type', $jobCard ? $jobCard->patti_type : '') == $type->patti_type_name) ? 'selected' : '' }}>
                                                {{ $type->patti_type_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="patti_type">Patti Type</label>
                                </div>
                                @error('patti_type') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="collar_type" name="collar_type" class="form-select select2" data-placeholder="Select Collar Type">
                                        <option value="">Select Collar Type</option>
                                        @foreach($collarTypes as $type)
                                            <option value="{{ $type->collar_type_name }}" {{ (old('collar_type', $jobCard ? $jobCard->collar_type : '') == $type->collar_type_name) ? 'selected' : '' }}>
                                                {{ $type->collar_type_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="collar_type">Collar Type</label>
                                </div>
                                @error('collar_type') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="cuff_type" name="cuff_type" class="form-select select2" data-placeholder="Select Cuff Type">
                                        <option value="">Select Cuff Type</option>
                                        @foreach($cuffTypes as $type)
                                            <option value="{{ $type->cuff_type_name }}" {{ (old('cuff_type', $jobCard ? $jobCard->cuff_type : '') == $type->cuff_type_name) ? 'selected' : '' }}>
                                                {{ $type->cuff_type_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="cuff_type">Cuff Type</label>
                                </div>
                                @error('cuff_type') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="pocket_type" name="pocket_type" class="form-select select2" data-placeholder="Select Pocket Type">
                                        <option value="">Select Pocket Type</option>
                                        @foreach($pocketTypes as $type)
                                            <option value="{{ $type->pocket_type_name }}" {{ (old('pocket_type', $jobCard ? $jobCard->pocket_type : '') == $type->pocket_type_name) ? 'selected' : '' }}>
                                                {{ $type->pocket_type_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="pocket_type">Pocket Type</label>
                                </div>
                                @error('pocket_type') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="bottom_cut" name="bottom_cut" class="form-select select2" data-placeholder="Select Bottom Cut">
                                        <option value="">Select Bottom Cut</option>
                                        @foreach($bottomCuts as $type)
                                            <option value="{{ $type->bottom_cut_name }}" {{ (old('bottom_cut', $jobCard ? $jobCard->bottom_cut : '') == $type->bottom_cut_name) ? 'selected' : '' }}>
                                                {{ $type->bottom_cut_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="bottom_cut">Bottom Cut</label>
                                </div>
                                @error('bottom_cut') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="cutting_issue_unit" name="cutting_issue_unit" class="form-select select2" data-placeholder="Select Cutting Issue Unit">
                                        <option value="">Select Cutting Issue Unit</option>
                                        <option value="Nachias Fashion Private Limited" {{ (old('cutting_issue_unit', $jobCard ? $jobCard->cutting_issue_unit : '') == 'Nachias Fashion Private Limited') ? 'selected' : '' }}>Nachias Fashion Private Limited</option>
                                        <option value="Samayanallur" {{ (old('cutting_issue_unit', $jobCard ? $jobCard->cutting_issue_unit : '') == 'Samayanallur') ? 'selected' : '' }}>Samayanallur</option>
                                        <option value="Kalavasal" {{ (old('cutting_issue_unit', $jobCard ? $jobCard->cutting_issue_unit : '') == 'Kalavasal') ? 'selected' : '' }}>Kalavasal</option>
                                    </select>
                                    <label for="cutting_issue_unit">Cutting Issue Unit *</label>
                                </div>
                                @error('cutting_issue_unit') <span class="text-danger">{{ $message }}</span> @enderror
                            </div> 
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="cutting_master" name="cutting_master_id" class="form-select select2" data-placeholder="Select Cutting Master">
                                        <option value="">Select Cutting Master</option>
                                        @foreach($cuttingMasters as $master)
                                            <option value="{{ $master->id }}" {{ (old('cutting_master_id', $jobCard ? $jobCard->cutting_master_id : '') == $master->id) ? 'selected' : '' }}>
                                                {{ $master->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="cutting_master">Cutting Master *</label>
                                </div>
                                @error('cutting_master_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control cutting_date" id="cutting_date" name="cutting_date" placeholder="Select Cutting Date" value="{{ old('cutting_date', $jobCard ? date('d-m-Y', strtotime($jobCard->cutting_date)) : '') }}">
                                    <label for="cutting_date">Cutting Date *</label>
                                </div>
                                @error('cutting_date') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
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
                                <div class="form-floating form-floating-outline">
                                    <select id="size_ratio_select" name="size_ratio_id" class="form-select select2" data-placeholder="Select Size Ratio">
                                        <option value="">Select Size Ratio</option>
                                        @foreach($sizeRatios as $sr)
                                            <option value="{{ $sr->id }}" data-sizes="{{ $sr->size }}" data-ratios="{{ $sr->ratio }}" {{ (old('size_ratio_id', $jobCard ? $jobCard->size_ratio_id : '') == $sr->id) ? 'selected' : '' }}>({{ $sr->size }}) - ({{ $sr->ratio }})</option>
                                        @endforeach
                                    </select>
                                    <label for="size_ratio_select">Select Size Ratio</label>
                                </div>
                                <small class="text-muted">
                                    Note: Size ratio will be used to calculate production quantity for each size.
                                </small><br>
                                @error('size_ratio_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="table-responsive" id="cutting-size-table-wrapper" style="{{ ($jobCard && $jobCard->size_ratio_id) ? '' : 'display:none;' }}">
                            <table class="table table-bordered text-center align-middle" id="cutting-size-table">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="align-middle">SIZE</th>
                                        <th colspan="{{ count($sizes) }}" class="ratio-header">CUTTING SIZE RATIO</th>
                                        <th colspan="2" class=""></th>
                                        <th colspan="2">CUTTING MARK AND LAY</th>
                                    </tr>
                                    <tr class="size-header-row">
                                        @foreach($sizes as $s)
                                            <th class="dynamic-size-head">{{ $s }}</th>
                                        @endforeach
                                        <th class="extra-col-1"></th>
                                        <th class="extra-col-2"></th>
                                        <th>SIZE</th>
                                        <th>MARK</th>
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
                                                <input type="number" name="matrix_items[{{ $idx }}][qty_fs]" class="form-control form-control-sm text-center fw-bold qty-direct-input fs-summary-{{ $s }}" data-type="fs" data-size="{{ $s }}" value="{{ $val ? (int)$val : '' }}">
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
                                                <input type="number" name="matrix_items[{{ $idx }}][qty_hs]" class="form-control form-control-sm text-center fw-bold qty-direct-input hs-summary-{{ $s }}" data-type="hs" data-size="{{ $s }}" value="{{ $val ? (int)$val : '' }}">
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
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle">
                                <thead id="fabric-details-head">
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
                                                <td class="fw-bold">Mtr/B.M</td>
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
                                                <td class="fw-bold">SLEEVE WISE QTY</td>
                                                <td>
                                                    <div class="input-group input-group-sm flex-nowrap">
                                                        <span class="input-group-text px-1">F/S</span>
                                                        <input type="text" name="fabrics[{{ $index }}][fs_qty]" class="form-control text-center px-1 sleeve-qty-input" data-art="{{ $fabric['art_no'] ?? '' }}" value="{{ $fabric['fs_qty'] ?? '' }}">
                                                        <span class="input-group-text px-1">H/S</span>
                                                        <input type="text" name="fabrics[{{ $index }}][hs_qty]" class="form-control text-center px-1 sleeve-qty-input" data-art="{{ $fabric['art_no'] ?? '' }}" value="{{ $fabric['hs_qty'] ?? '' }}">
                                                        <span class="input-group-text px-1 uom-label">{{ $fabric['uom_code'] ?? 'PCS' }}</span>
                                                    </div>
                                                </td>
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

                <div class="card mb-4 {{ $showMatrix ? '' : 'd-none' }}" id="article-matrix-card">
                    <div class="card-body">
                        <div class="card-header-box mb-3">
                            <h4>Article Quantity Matrix</h4>
                        </div>
                        
                        <!-- Section 1: Fabric -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-uppercase border-bottom pb-2 mb-3">1. Fabric Pieces (Source)</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered text-center align-middle mb-0" id="article-qty-matrix-1">
                                    <thead></thead>
                                    <tbody id="article-qty-matrix-1-body"></tbody>
                                    <tfoot></tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Section 2: Consumables -->
                        <div>
                            <h6 class="fw-bold text-uppercase border-bottom pb-2 mb-3">2. Consumables (Derived)</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered text-center align-middle mb-0" id="article-qty-matrix-2">
                                    <thead></thead>
                                    <tbody id="article-qty-matrix-2-body"></tbody>
                                    <tfoot></tfoot>
                                </table>
                            </div>
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
                <h5 class="modal-title text-white" id="processGroupModalLabel">{{ $jobCard ? 'View Process Group (Read-Only)' : 'Select Process Group' }}</h5>
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

<div class="modal fade" id="stockValidationErrorModal" tabindex="-1" aria-labelledby="stockValidationErrorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="stockValidationErrorModalLabel">Stock Validation Error</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center" id="stockErrorTable">
                        <thead class="table-light">
                            <tr>
                                <th>Art No</th>
                                <th>Required (Mtr)</th>
                                <th>Issued (Mtr)</th>
                                <th>Calculation Details</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <label for="stock_error_notes" class="form-label fw-bold">Validation Notes</label>
                    <textarea class="form-control" id="stock_error_notes" name="stock_error_notes" rows="3" placeholder="Enter notes regarding this stock discrepancy"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="$('form.common-form').attr('data-skip-validation', 'true').submit();">Ignore & Submit Anyway</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        const oldFabrics = @json(old('fabrics', []));
        const oldMatrix = @json(old('article_matrix', []));
        const existingImages = @json($jobCard && $jobCard->images ? $jobCard->images : []);
        const existingMatrix = @json($jobCard && $jobCard->fabricDetails ? $jobCard->fabricDetails : []);
        const matrixItems = @json(old('matrix_items', $jobCard && $jobCard->cuttingSizeRatios ? $jobCard->cuttingSizeRatios : []));
        const isEditMode = {{ $jobCard ? 'true' : 'false' }};
        let globalActiveSizes = { fs: [], hs: [] };
        let isSyncing = false;
        let currentArtNumbers = @json(array_values(array_unique(array_column($fabrics, 'art_no'))));
        const articleUoms = @json(collect($fabrics)->pluck('uom_code', 'art_no')) || {};
        let currentArtData = []; 
        let currentSizes = @json($sizes);
        let currentRatios = @json($ratios);
        let currentProcessGroupId = '{{ old("process_group_id", $jobCard ? $jobCard->process_group_id : "") }}';
        let currentProcessGroup = '{{ old("process_group_display", $jobCard && $jobCard->processGroup ? $jobCard->processGroup->name : "") }}';
        let addedStages = [];
        let fsMeterValue = '{{ old("fs_meter", $jobCard ? ($jobCard->sleeveMeters->where("sleeve_type", "Full Sleeve")->first()->meter ?? "") : "") }}';
        let hsMeterValue = '{{ old("hs_meter", $jobCard ? ($jobCard->sleeveMeters->where("sleeve_type", "Half Sleeve")->first()->meter ?? "") : "") }}';

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

        const flatpickrConfig = { dateFormat: 'd-m-Y', allowInput: true };
        $('.issue_date, .delivery_date, .cutting_date, .dynamic-stage-date').flatpickr(flatpickrConfig);

        $('form').on('submit', function(e) {
            if ($(this).attr('data-skip-validation') === 'true') return;

            let isValid = true;
            let errors = [];
            
            const artDataMap = {};
                                
            $('.sleeve-qty-input').each(function() {
                const art = $(this).data('art');
                const val = parseFloat($(this).val()) || 0;
                const name = $(this).attr('name');
                const isFs = name.includes('fs_qty') || name.includes('fs_cons');
                const isSizeWise = $(this).hasClass('size-cons-input');
                const size = $(this).data('size'); 

                if (!artDataMap[art]) {
                    artDataMap[art] = { 
                        default_fs_cons: 0, 
                        default_hs_cons: 0,
                        size_wise_cons: {}, 
                        issued: 0,
                        is_mtr: false
                    };

                    if (currentArtData) {
                        const d = currentArtData.find(d => d.art_no == art);
                        if (d) {
                            artDataMap[art].issued = parseFloat(d.mtr) || 0;
                            artDataMap[art].is_mtr = (d.uom_code === 'MTR');
                        }
                    } else {
                    const $row = $('tr.cat1-row, tr.cat2-row').filter(function() {
                        return $(this).data('art') == art;
                    });
                    if ($row.length) {
                        artDataMap[art].is_mtr = ($row.data('uom') === 'MTR');
                        artDataMap[art].issued = parseFloat($('#fabric-details-card').find(`.uom-label[data-art="${art}"]`).closest('tr').siblings().find('input[name*="[mtr]"]').val()) || 0;
                    }
                    }
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

            for (const art in artDataMap) {
                const data = artDataMap[art];

                let required = 0;
                let calcDetails = "";

                const $matrixRow = $('tr.cat1-row, tr.cat2-row').filter(function() {
                    return $(this).data('art') == art;
                });

                if ($matrixRow.length > 0) {
                    if (data.is_mtr) {
                        for (const sz in data.size_wise_cons) {
                            const cons = data.size_wise_cons[sz];
                            const piecesFs = parseFloat($matrixRow.find('input').filter(function() { return (this.name || "").includes(`[fs_${sz}]`); }).val()) || 0;
                            const piecesHs = parseFloat($matrixRow.find('input').filter(function() { return (this.name || "").includes(`[hs_${sz}]`); }).val()) || 0;
                            
                            required += (cons.fs * piecesFs) + (cons.hs * piecesHs);
                            if (cons.fs > 0 || cons.hs > 0) {
                                if (piecesFs > 0 || piecesHs > 0) {
                                    calcDetails += (calcDetails ? " + " : "") + `${sz}: (${cons.fs}*${piecesFs} F/S + ${cons.hs}*${piecesHs} H/S)`;
                                }
                            }
                        }
                    } else {
                        let totalPieces = 0;
                        $matrixRow.find('.qty-input').each(function() {
                            totalPieces += parseFloat($(this).val()) || 0;
                        });
                        
                        required = totalPieces;
                        calcDetails = `Total from matrix: ${totalPieces} pieces`;
                    }
                }

                required = Math.round(required * 1000) / 1000;
                const issued = Math.round(data.issued * 1000) / 1000;

                if (required > issued) {
                    if (!data.is_mtr) {
                        isValid = false;
                    }
                    errors.push({
                        art: art,
                        required: required,
                        issued: issued,
                        calc: calcDetails,
                        is_mtr: data.is_mtr || false
                    });
                }
            }

            if (!isValid || errors.length > 0) {
                e.preventDefault();
                const $tbody = $('#stockErrorTable tbody').empty();
                errors.forEach(err => {
                    const rowClass = err.is_mtr ? 'table-info' : '';
                    const badge = err.is_mtr ? '<span class="badge bg-info ms-2">Info Only</span>' : '';
                    $tbody.append(`
                        <tr class="${rowClass}">
                            <td class="fw-bold">${err.art}${badge}</td>
                            <td class="text-danger fw-bold">${err.required}</td>
                            <td class="text-success fw-bold">${err.issued}</td>
                            <td class="small text-start">${err.calc}</td>
                        </tr>
                    `);
                });
                new bootstrap.Modal(document.getElementById('stockValidationErrorModal')).show();
            }
        });

        $('#purchase_order').on('change', function() {
            const poId = $(this).val();
            if (!poId) return;

            $.get(`{{ url('job_card_entries/get-po-details') }}/${poId}`, function(data) {
                currentArtNumbers = data.art_numbers;
                currentArtData = data.art_data; 
                
                if (data.art_data) {
                    data.art_data.forEach(d => {
                        articleUoms[d.art_no] = d.uom_code;
                    });
                }

                $('#fabric-details-card').removeClass('d-none');
                
                renderFabricDetails();
                renderArticleQtyMatrix(data.art_numbers);
                renderCuttingSizeTable(currentSizes, currentRatios);
                updateQuantityRowVisibility();
            });
        });

        function resetItemSelect(selectedId = null) {
            const $item = $('#item_id');
            $item.empty().append('<option value="">Select Item</option>');
            if (selectedId) {
                $item.val(String(selectedId)).trigger('change');
            } else {
                $item.val('').trigger('change');
            }
        }

        function loadItemsByBrandCategory(brandCategoryId, selectedItemId = null) {
            if (!brandCategoryId) {
                resetItemSelect();
                return;
            }

            $.get(`{{ url('job_card_entries/get_items_by_brand_category') }}`, { brand_category_id: brandCategoryId }, function(res) {
                const items = res && res.items ? res.items : [];
                const $item = $('#item_id');
                $item.empty().append('<option value="">Select Item</option>');
                items.forEach(it => {
                    const text = it.code ? `${it.name} (${it.code})` : it.name;
                    $item.append(`<option value="${it.id}">${text}</option>`);
                });
                if (selectedItemId) {
                    $item.val(String(selectedItemId)).trigger('change');
                } else {
                    $item.val('').trigger('change');
                }
            }).fail(function() {
                resetItemSelect();
            });
        }

        $('#brand_category_id').on('change', function() {
            loadItemsByBrandCategory($(this).val(), null);
        });

        const initialBrandCategoryId = $('#brand_category_id').val();
        const initialItemId = @json(old('item_id', $jobCard ? $jobCard->item_id : null));
        if (initialBrandCategoryId) {
            loadItemsByBrandCategory(initialBrandCategoryId, initialItemId);
        } else {
            resetItemSelect(initialItemId);
        }

        const initialPoId = $('#purchase_order').val();
        if (initialPoId) {
            $.get(`{{ url('job_card_entries/get-po-details') }}/${initialPoId}`, function(data) {
                currentArtNumbers = data.art_numbers;
                currentArtData = data.art_data;
                
                if (!isEditMode && $('#fabric-details-body tr').length === 0) {
                    $('#fabric-details-card').removeClass('d-none');
                    renderFabricDetails();
                    renderCuttingSizeTable(currentSizes, currentRatios);
                    updateQuantityRowVisibility();
                } else {
                    renderFabricDetails(); 
                    renderCuttingSizeTable(currentSizes, currentRatios); 
                    syncMatrixWithMasterTable(false);
                    updateQuantityRowVisibility();
                }
            });
        }

        function renderArticleQtyMatrix(artNumbers, activeFsSizes = [], activeHsSizes = []) {
            const tableIds = ['#article-qty-matrix-1', '#article-qty-matrix-2'];
            
            tableIds.forEach(id => {
                const $table = $(id);
                $table.find('thead').empty();
                $table.find('tbody').empty();
                $table.find('tfoot').empty();

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
                $table.find('thead').append(headHtml);
                
                const footHtml = `
                    <tr class="${id === '#article-qty-matrix-2' ? 'd-none' : ''}">
                        <td class="fw-bold text-center small">TOTAL</td>
                        ${activeFsSizes.map(s => `<td><div class="col-total text-center fw-bold small py-1 bg-secondary-subtle border rounded" data-col="fs-${s}" style="min-height: 30px;"></div></td>`).join('')}
                        ${activeHsSizes.map(s => `<td><div class="col-total text-center fw-bold small py-1 bg-secondary-subtle border rounded" data-col="hs-${s}" style="min-height: 30px;"></div></td>`).join('')}
                        <td><div id="${id.replace('#','')}-grand-total" class="grand-total text-center fw-bold py-1 bg-secondary-subtle border rounded small" style="min-height: 30px;"></div></td>
                    </tr>`;
                $table.find('tfoot').append(footHtml);
            });

            if (!artNumbers || artNumbers.length === 0) return;

            artNumbers.forEach((art, index) => {
                const existingRow = isEditMode && existingMatrix.length > 0  ? existingMatrix.find(r => String(r.art_no).trim() == String(art).trim()) : null;
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
                    
                const readonlyAttr = (catId != 1) ? 'readonly tabindex="-1"' : '';
                const rowClass = (catId != 1) ? 'cat2-row' : 'cat1-row';
                const sectionId = (catId == 1) ? 1 : 2;
                const $targetTbody = $(`#article-qty-matrix-${sectionId}-body`);

                let rowHtml = `<tr class="${rowClass}" data-uom="${uom}" data-art="${art}" data-category="${catId}">
                                <td>
                                    <div class="border rounded p-1 mb-1 text-center fw-bold small" style="background: #f8f9fa;">${art}</div>
                                    <input type="hidden" name="article_matrix[${index}][art_no]" value="${art}">
                                    <div class="small text-muted text-center" style="font-size: 10px; line-height: 1.1;">${artName}</div>
                                </td>`;
                
                activeFsSizes.forEach(s => {
                    let fsVal = '';
                    if (oldRow && oldRow[`fs_${s}`] !== undefined) {
                        fsVal = oldRow[`fs_${s}`];
                    } else if (existingRow && existingRow.quantities) {
                        const q = existingRow.quantities.find(q => String(q.size) === String(s));
                        fsVal = (q && q.qty_fs != null) ? parseFloat(q.qty_fs) : '';
                    }
                    rowHtml += `<td><input type="number" name="article_matrix[${index}][fs_${s}]" class="form-control form-control-sm qty-input text-center" data-col="fs-${s}" data-art="${art}" value="${fsVal}" ${readonlyAttr}></td>`;
                });

                activeHsSizes.forEach(s => {
                    let hsVal = '';
                    if (oldRow && oldRow[`hs_${s}`] !== undefined) {
                        hsVal = oldRow[`hs_${s}`];
                    } else if (existingRow && existingRow.quantities) {
                        const q = existingRow.quantities.find(q => String(q.size) === String(s));
                        hsVal = (q && q.qty_hs != null) ? parseFloat(q.qty_hs) : '';
                    }
                    rowHtml += `<td><input type="number" name="article_matrix[${index}][hs_${s}]" class="form-control form-control-sm qty-input text-center" data-col="hs-${s}" data-art="${art}" value="${hsVal}" ${readonlyAttr}></td>`;
                });

                rowHtml += `<td><input type="text" class="form-control form-control-sm row-total text-center fw-bold" readonly tabindex="-1"></td></tr>`;
                $targetTbody.append(rowHtml);
            });

            calculateMatrixTotals();
        }

        $(document).on('input', '.qty-input', function() {
            if (isSyncing) return;
            
            const $el = $(this);
            const $row = $el.closest('tr');
            if ($row.closest('table').is('#article-qty-matrix-1, #article-qty-matrix-2')) {
                const isCat1 = ($row.attr('data-category') == 1);

                // Sync with master table ONLY IF it's Category 1 and first row (usually Fabric)
                if (isCat1 && $row.is(':first-child')) {
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
                // Step 1: Sum Category 1 pieces per column (Source for Consumables)
                const cat1ColSums = {};
                $('#article-qty-matrix-1-body tr').each(function() {
                    let rowTotal = 0;
                    $(this).find('.qty-input').each(function() {
                        const col = $(this).data('col');
                        const val = parseFloat($(this).val()) || 0;
                        cat1ColSums[col] = (cat1ColSums[col] || 0) + val;
                        rowTotal += val;
                    });
                    $(this).find('.row-total').val(rowTotal > 0 ? (rowTotal % 1 === 0 ? rowTotal : rowTotal.toFixed(3)) : '');
                });

                // Step 2: Auto-calculate Category 2 based on Cat 1 sums
                $('#article-qty-matrix-2-body tr').each(function() {
                    const $row = $(this);
                    const art = $row.data('art');
                    let rowTotal = 0;
                    $row.find('.qty-input').each(function() {
                        const col = $(this).data('col');
                        const parts = col.split('-');
                        const type = parts[0];
                        const size = parts[1];
                        
                        const pieces = cat1ColSums[col] || 0;
                        const cons = getConsumptionValue(art, type, size);
                        const calcVal = pieces * cons;
                        
                        $(this).val(calcVal > 0 ? (calcVal % 1 === 0 ? calcVal : calcVal.toFixed(3)) : '');
                        rowTotal += (parseFloat($(this).val()) || 0);
                    });
                    $(this).find('.row-total').val(rowTotal > 0 ? (rowTotal % 1 === 0 ? rowTotal : rowTotal.toFixed(3)) : '');
                });

                // Step 3: Column Totals and Grand Totals for both tables
                const tableIds = ['#article-qty-matrix-1', '#article-qty-matrix-2'];
                let totalFS = 0;
                let totalHS = 0;

                tableIds.forEach(id => {
                    const colSums = {};
                    let tableGrandTotal = 0;
                    
                    $(`${id}-body tr`).each(function() {
                        const uom = ($(this).attr('data-uom') || '').toUpperCase();
                        const isMtr = (uom === 'MTR');

                        $(this).find('.qty-input').each(function() {
                            const val = parseFloat($(this).val()) || 0;
                            const col = $(this).data('col');
                            colSums[col] = (colSums[col] || 0) + val;
                            tableGrandTotal += val;
                            
                            if (isMtr) {
                                if (col.startsWith('fs')) totalFS += val;
                                else if (col.startsWith('hs')) totalHS += val;
                            }
                        });
                    });

                    // Update footer
                    $(`${id} .col-total`).each(function() {
                        const col = $(this).data('col');
                        const sum = colSums[col] || 0;
                        $(this).text(sum > 0 ? (sum % 1 === 0 ? sum : sum.toFixed(3)) : ''); 
                    });
                    $(`${id}-grand-total`).text(tableGrandTotal > 0 ? (tableGrandTotal % 1 === 0 ? tableGrandTotal : tableGrandTotal.toFixed(3)) : '');
                });

                // Summary updates
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

        $('#size_ratio_select').on('change', function() {
            const $selected = $(this).find(':selected');
            const sizesStr = $selected.data('sizes') || '';
            const ratiosStr = $selected.data('ratios') || '';
            
            if (sizesStr) {
                currentSizes = sizesStr.toString().split(',').map(s => s.trim());
                currentRatios = ratiosStr.toString().split(',').map(r => r.trim());
                $('#cutting-size-table-wrapper').show();
                $('#trigger-sync-wrapper').show();
                $('#article-matrix-card').removeClass('d-none');
            } else {
                currentSizes = ['36', '38', '40', '42', '44'];
                currentRatios = ['', '', '', '', ''];
                $('#cutting-size-table-wrapper').hide();
                $('#trigger-sync-wrapper').hide();
            }
            renderCuttingSizeTable(currentSizes, currentRatios);
            syncMatrixWithMasterTable(false);
            updateQuantityRowVisibility();
        });
        const $initialSizeRatio = $('#size_ratio_select').find(':selected');
        if ($initialSizeRatio.val()) {
            const sizesStr = $initialSizeRatio.data('sizes') || '';
            const ratiosStr = $initialSizeRatio.data('ratios') || '';
            if (sizesStr) {
                currentSizes = sizesStr.toString().split(',').map(s => s.trim());
                currentRatios = ratiosStr.toString().split(',').map(r => r.trim());
                renderCuttingSizeTable(currentSizes, currentRatios);
                syncMatrixWithMasterTable(false);
            }
        }

        function renderFabricDetails() {
            const $tbody = $('#fabric-details-body');
            const $thead = $('#fabric-details-head');
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
                    </div>
                </th>`;
                artRow += `<td class="fw-bold">ART NO</td><td><input type="text" name="fabrics[${index}][art_no]" class="form-control form-control-sm text-center" value="${art}" readonly></td>`;

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

                if (!vMtr && currentArtData && currentArtData.length > 0) {
                    const d = currentArtData.find(d => d.art_no == art);
                    if (d) {
                        vMtr = d.mtr || '';
                    }
                }

                widthRow += `<td class="fw-bold">WIDTH</td><td><input type="text" name="fabrics[${index}][width]" class="form-control form-control-sm text-center" value="${vWidth}"></td>`;
                mtrRow += `<td class="fw-bold">Mtr/B.M</td><td><input type="text" name="fabrics[${index}][mtr]" class="form-control form-control-sm text-center" value="${vMtr}"></td>`;
                inOutRow += `<td class="fw-bold">IN/OUT</td><td><input type="text" name="fabrics[${index}][in_out]" class="form-control form-control-sm text-center" value="${vInOut}"></td>`;
                nPattiRow += `<td class="fw-bold">N.PATTI</td><td><input type="text" name="fabrics[${index}][n_patti]" class="form-control form-control-sm text-center" value="${vNPatti}"></td>`;
                
                let uom = '';
                if (currentArtData && currentArtData.length > 0) {
                    const d = currentArtData.find(d => d.art_no == art);
                    if (d) {
                        uom = d.uom_code || '';
                    }
                }

                if (uom === 'MTR') {
                    let sizes = [...new Set([...globalActiveSizes.fs, ...globalActiveSizes.hs])].sort((a,b) => parseFloat(a)-parseFloat(b) || String(a).localeCompare(String(b)));
                    
                    let sizeTableHtml = '';
                    if (sizes.length > 0) {
                        sizeTableHtml = `<table class="table table-bordered table-sm mb-0 mt-1" style="font-size: 11px;">
                            <thead class="bg-light"><tr><th>Size</th><th>F/S</th><th>H/S</th></tr></thead>
                            <tbody>`;
                        
                        sizes.forEach(sz => {
                            let vSzFs = '';
                            let vSzHs = '';
                            
                            if (oldFabrics && oldFabrics[index] && oldFabrics[index]['consumptions'] && oldFabrics[index]['consumptions'][sz]) {
                                vSzFs = oldFabrics[index]['consumptions'][sz]['fs_cons'] || '';
                                vSzHs = oldFabrics[index]['consumptions'][sz]['hs_cons'] || '';
                            }
                            
                            if (!vSzFs && isEditMode && existingMatrix.length > 0) {
                                const m = existingMatrix.find(m => m.art_no == art);
                                if (m && m.consumptions) {
                                    const c = m.consumptions.find(c => String(c.size) === String(sz));
                                    if (c) {
                                        vSzFs = c.fs_cons || '';
                                        vSzHs = c.hs_cons || '';
                                    }
                                }
                            }

                            sizeTableHtml += `<tr>
                                <td>${sz}</td>
                                <td><input type="text" name="fabrics[${index}][consumptions][${sz}][fs_cons]" class="form-control form-control-sm text-center p-0 sleeve-qty-input size-cons-input" data-art="${art}" data-size="${sz}" data-type="fs" data-uom="${uom}" value="${vSzFs}"></td>
                                <td><input type="text" name="fabrics[${index}][consumptions][${sz}][hs_cons]" class="form-control form-control-sm text-center p-0 sleeve-qty-input size-cons-input" data-art="${art}" data-size="${sz}" data-type="hs" data-uom="${uom}" value="${vSzHs}"></td>
                            </tr>`;
                        });
                        sizeTableHtml += `</tbody></table>`;
                    } else {
                        sizeTableHtml = `<div class="small text-muted p-1">Enter Matrix Qty first to see sizes</div>`;
                    }

                    sleeveQtyRow += `<td class="fw-bold">SLEEVE WISE QTY<br><span class="badge bg-info uom-label" data-art="${art}">${uom}</span></td>
                        <td>${sizeTableHtml}</td>`;
                } else {
                    let vFsQty = (oldFabrics && oldFabrics[index] && oldFabrics[index]['fs_qty']) ? oldFabrics[index]['fs_qty'] : '';
                    let vHsQty = (oldFabrics && oldFabrics[index] && oldFabrics[index]['hs_qty']) ? oldFabrics[index]['hs_qty'] : '';

                    if (!vFsQty && existingMatrix.length > 0) {
                        const m = existingMatrix.find(m => m.art_no == art);
                        if (m) {
                            vFsQty = m.fs_qty || '';
                            vHsQty = m.hs_qty || '';
                        }
                    }

                    sleeveQtyRow += `<td class="fw-bold">SLEEVE WISE QTY</td>
                            <td>
                                <div class="input-group input-group-sm flex-nowrap">
                                    <span class="input-group-text px-1">F/S</span>
                                    <input type="text" name="fabrics[${index}][fs_qty]" class="form-control text-center px-1 sleeve-qty-input" data-art="${art}" data-type="fs" data-uom="${uom}" value="${vFsQty}">
                                    <span class="input-group-text px-1">H/S</span>
                                    <input type="text" name="fabrics[${index}][hs_qty]" class="form-control text-center px-1 sleeve-qty-input" data-art="${art}" data-type="hs" data-uom="${uom}" value="${vHsQty}">
                                    <span class="input-group-text px-1 uom-label">${uom}</span>
                                </div>
                            </td>`;
                }
            });

            $thead.append(headHtml + '</tr>');
            $tbody.append(artRow + '</tr>');
            $tbody.append(widthRow + '</tr>');
            $tbody.append(mtrRow + '</tr>');
            $tbody.append(inOutRow + '</tr>');
            $tbody.append(sleeveQtyRow + '</tr>');
            $tbody.append(nPattiRow + '</tr>');
        }

        if (currentArtNumbers.length > 0) {
            renderFabricDetails();
        }

        function renderCuttingSizeTable(sizes, ratios) {
            const $table = $('#cutting-size-table');
            const $sizeHeaderRow = $table.find('.size-header-row');
            
            $table.find('.ratio-header').attr('colspan', sizes.length);
            $sizeHeaderRow.find('th').not('.extra-col-1, .extra-col-2, :nth-last-child(1), :nth-last-child(2)').remove();
            
            let sizeHeadersHtml = '';
            sizes.forEach(s => sizeHeadersHtml += `<th class="dynamic-size-head">${s}</th>`);
            $sizeHeaderRow.prepend(sizeHeadersHtml);

            const $tbody = $table.find('tbody');
            $tbody.empty();

            const name = (currentProcessGroup || '').toUpperCase();
            const hasFS = !currentProcessGroup || name.includes('F/S') || name.includes('FULL');
            const hasHS = !currentProcessGroup || name.includes('H/S') || name.includes('HALF');
            const hasSpecial = name.includes('SPECIAL');


            const selectedRatioDisplay = $('#size_ratio_display').val() || '';
            const sizeStr = selectedRatioDisplay ? selectedRatioDisplay.split(' - ')[0] : '';

            const addTypeRows = (type, label, isVisible, showInfo = true, infoLabel = 'SIZE') => {
                const style = isVisible ? '' : 'display:none;';
                
                let vRow = `<tr class="qty-${type}-row" style="${style}"><td><strong>${label}</strong></td>`;
                sizes.forEach((s, idx) => {
                    let savedVal = '';
                    if (isEditMode && matrixItems.length > 0) {
                        const savedRecord = matrixItems.find(r => String(r.size) === String(s));
                        if (savedRecord) {
                            const fsVal = savedRecord.qty_fs != null ? savedRecord.qty_fs : '';
                            const hsVal = savedRecord.qty_hs != null ? savedRecord.qty_hs : '';
                            savedVal = type === 'fs' ? fsVal : hsVal;
                        }
                    }
                    const ratioVal = (ratios[idx] && !savedVal) ? ratios[idx] : '';
                    const finalVal = savedVal || ratioVal;
                    
                    vRow += `<td>
                        <input type="number" name="matrix_items[${idx}][qty_${type}]" class="form-control form-control-sm text-center fw-bold qty-direct-input ${type}-summary-${s}" data-type="${type}" data-size="${s}" value="${finalVal}">
                        ${type === 'fs' ? `<input type="hidden" name="matrix_items[${idx}][size]" value="${s}">` : ''} 
                    </td>`;
                });
                
                vRow += `<td class=""></td><td class=""></td>
                <td><input type="text" name="mark_lay[${type}][size]" class="form-control form-control-sm text-center"></td>
                <td><input type="text" name="mark_lay[${type}][mark]" class="form-control form-control-sm text-center"></td></tr>`;
                $tbody.append(vRow);

                if (showInfo) {
                    let iRow = `<tr class="qty-${type}-info-row" style="${style}"><td><strong>${infoLabel}</strong></td>`;
                    iRow += `<td colspan="${sizes.length}"><input type="text" name="matrix_items_info[${type}]" class="form-control form-control-sm text-center text-muted" value="${infoLabel === 'SIZE' ? sizeStr : ''}"></td>`;
                    iRow += `<td class=""></td><td class=""></td><td></td><td></td></tr>`;
                    $tbody.append(iRow);
                }
            };

            const fsInfoLabel = name.includes('OTHERS') ? 'QTY - F/S' : 'SIZE';
            addTypeRows('fs', 'QTY - F/S', true, true, fsInfoLabel);
            addTypeRows('hs', 'QTY - H/S', true, false);

            syncSummaryToHeader(); 
        }

        function updateQuantityRowVisibility() {
            const name = (currentProcessGroup || '').toUpperCase();
            const hasFS = !currentProcessGroup || name.includes('F/S') || name.includes('FULL');
            const hasHS = !currentProcessGroup || name.includes('H/S') || name.includes('HALF');
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
            const empOptions = `@foreach($employees as $emp)<option value="{{ $emp->id }}">{{ $emp->name }}</option>@endforeach`;

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
                $('.qty-direct-input[data-type="fs"]').each(function() {
                    const val = parseFloat($(this).val()) || 0;
                    const size = String($(this).data('size'));
                    if (val > 0 && !activeFsSizes.includes(size)) activeFsSizes.push(size);
                });
                $('.qty-direct-input[data-type="hs"]').each(function() {
                    const val = parseFloat($(this).val()) || 0;
                    const size = String($(this).data('size'));
                    if (val > 0 && !activeHsSizes.includes(size)) activeHsSizes.push(size);
                });

                $('.qty-input').each(function() {
                    const val = parseFloat($(this).val()) || 0;
                    if (val > 0) {
                        const col = $(this).data('col') || '';
                        const parts = col.split('-');
                        if (parts.length >= 2) {
                            const type = parts[0];
                            const size = String(parts[1]);
                            if (type === 'fs' && !activeFsSizes.includes(size)) activeFsSizes.push(size);
                            if (type === 'hs' && !activeHsSizes.includes(size)) activeHsSizes.push(size);
                        }
                    }
                });

                const sizeSort = (a, b) => {
                    const numA = parseFloat(a);
                    const numB = parseFloat(b);
                    if (!isNaN(numA) && !isNaN(numB)) return numA - numB;
                    return String(a).localeCompare(String(b));
                };
                activeFsSizes.sort(sizeSort);
                activeHsSizes.sort(sizeSort);

                const sizesChanged = JSON.stringify(globalActiveSizes.fs) !== JSON.stringify(activeFsSizes) || JSON.stringify(globalActiveSizes.hs) !== JSON.stringify(activeHsSizes);

                const artNumbers = getArtNumbers();

                if (artNumbers.length > 0 && (activeFsSizes.length > 0 || activeHsSizes.length > 0)) {
                    if (sizesChanged) {
                        renderArticleQtyMatrix(artNumbers, activeFsSizes, activeHsSizes);
                    }
                    $('#article-matrix-card').removeClass('d-none');
                    
                    if (populateValues) {
                        $('.qty-direct-input').each(function() {
                            const type = $(this).data('type');
                            const size = String($(this).data('size'));
                            
                            if (targetType && targetSize && (type !== targetType || size !== String(targetSize))) return;

                            const val = parseFloat($(this).val()); 
                            const pieces = isNaN(val) ? 0 : val;

                            $('#article-qty-matrix tbody tr.cat1-row .qty-input').filter(function() {
                                return $(this).data('col') === `${type}-${size}`;
                            }).each(function() {
                                const uom = ($(this).closest('tr').attr('data-uom') || '').toUpperCase();
                                let finalCalc;
                                
                                if (uom === 'PCS') {
                                    // Normally Cat 1 should be MTR, but if it's PCS, we use direct pieces
                                    finalCalc = pieces > 0 ? Math.round(pieces).toString() : '';
                                } else {
                                    finalCalc = pieces > 0 ? pieces.toString() : '';
                                }
                                
                                if ($(this).val() != finalCalc) {
                                    $(this).val(finalCalc);
                                }
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
            const $stdInput = $(`.sleeve-qty-input[data-art="${art}"][data-type="${type}"]:not(.size-cons-input)`);
            if ($stdInput.length) {
                const val = parseFloat($stdInput.val());
                if (!isNaN(val)) return val;
                
                return 0;
            }
            
            return 0; 
        }

        $(document).on('input', '.sleeve-qty-input', function() {
            syncMatrixWithMasterTable(false, false);
        });


        $('#trigger-sync').on('click', function() {
            if (!$('#purchase_order').val()) {
                alert('Please select a Purchase Order first.');
                return;
            }
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

        if ($('#size_ratio_select').val()) {
            $('#size_ratio_select').trigger('change');
        } else if (matrixItems.length > 0 || @json(old('matrix_items') ? true : false)) {
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
                    }
                },
                error: function(xhr) {
                    alert('Error deleting image');
                }
            });
        };
    });
</script>
<style>
    /* Hide spinners in Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Hide spinners in Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>
@endsection
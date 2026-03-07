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
                            <input type="hidden" id="total_qty_fs" name="total_qty_fs" value="{{ old('total_qty_fs', $jobCard ? $jobCard->total_qty_fs : '') }}">
                            <input type="hidden" id="total_qty_hs" name="total_qty_hs" value="{{ old('total_qty_hs', $jobCard ? $jobCard->total_qty_hs : '') }}">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="season" name="season_id" class="form-select select2" data-placeholder="Select Season Code">
                                        <option value="">Select Season Code</option>
                                        @foreach($seasons as $season)
                                            <option value="{{ $season->id }}" {{ (old('season_id', $jobCard ? $jobCard->season_id : '') == $season->id) ? 'selected' : '' }}>{{ $season->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="season">Season Code</label>
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
                                            <option value="{{ $st->id }}" {{ (old('receipt_store_id', $jobCard ? $jobCard->receipt_store_id : '') == $st->id) ? 'selected' : '' }}>{{ $st->store_type_name }}</option>
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
                                            <option value="{{ $cat->id }}" {{ (old('brand_category_id', $jobCard ? $jobCard->brand_category_id : '') == $cat->id) ? 'selected' : '' }}>{{ $cat->name }}</option>
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
                                        <th>Employee *</th>
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
                                                    <select name="production_stages[{{ $index }}][employee_id]" class="form-select select2 employee-select" data-placeholder="Select Employee" data-selected="{{ $stage['employee_id'] ?? '' }}">
                                                        <option value="">Select Employee</option>
                                                    </select>
                                                    @error('production_stages.' . $index . '.employee_id') <span class="text-danger small">{{ $message }}</span> @enderror
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
                                                <select name="production_stages[0][employee_id]" class="form-select select2 employee-select" data-placeholder="Select Employee">
                                                    <option value="">Select Employee</option>
                                                </select>
                                                @error('production_stages.0.employee_id') <span class="text-danger small">{{ $message }}</span> @enderror
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

                <div class="card mb-4 d-none" id="article-matrix-card">
                    <div class="card-body">
                        <div class="card-header-box mb-3">
                            <h4>Article Quantity Matrix</h4>
                            <div id="article-matrix-error" class="text-danger small fw-bold mb-2" style="display: none;"></div>
                            @error('article_matrix') <div class="text-danger small fw-bold mb-2 backend-error">{{ $message }}</div> @enderror
                        </div>
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
                    }
                }
            }

            for (const art in artDataMap) {
                const data = artDataMap[art];
                let required = 0;
                let calcDetails = "";
                const $matrixRow = $('tr.cat1-row, tr.cat2-row').filter(function() { 
                    const rowArt = String($(this).data('art') || "").trim();
                    return rowArt === art;
                });

                if ($matrixRow.length > 0) {
                    if (data.is_mtr) {
                        let hasProcessedSizeWise = false;
                        for (const sz in data.size_wise_cons) {
                            const cons = data.size_wise_cons[sz];
                            const piecesFs = parseFloat($matrixRow.find('input').filter(function() { return (this.name || "").includes(`[fs_${sz}]`); }).val()) || 0;
                            const piecesHs = parseFloat($matrixRow.find('input').filter(function() { return (this.name || "").includes(`[hs_${sz}]`); }).val()) || 0;
                            if (piecesFs > 0 || piecesHs > 0) {
                                required += (cons.fs * piecesFs) + (cons.hs * piecesHs);
                                if (cons.fs > 0 || cons.hs > 0) {
                                    calcDetails += (calcDetails ? " + " : "") + `${sz}: (${cons.fs}*${piecesFs} F/S + ${cons.hs}*${piecesHs} H/S)`;
                                    hasProcessedSizeWise = true;
                                }
                            }
                        }

                        if (!hasProcessedSizeWise && (data.default_fs_cons > 0 || data.default_hs_cons > 0)) {
                            let totalFs = 0;
                            let totalHs = 0;
                            const $masterRow = $('tr.cat1-row').first();
                            $masterRow.find('input').each(function() {
                                const name = $(this).attr('name') || "";
                                if (name.includes('[fs_')) totalFs += parseFloat($(this).val()) || 0;
                                else if (name.includes('[hs_')) totalHs += parseFloat($(this).val()) || 0;
                            });
                            required = (data.default_fs_cons * totalFs) + (data.default_hs_cons * totalHs);
                            calcDetails = `Global: (${data.default_fs_cons} * ${totalFs} F/S) + (${data.default_hs_cons} * ${totalHs} H/S)`;
                        }
                    } else {
                        let totalPieces = 0;
                        $matrixRow.find('.qty-input').each(function() { totalPieces += parseFloat($(this).val()) || 0; });
                        required = totalPieces;
                        calcDetails = `Total from matrix: ${totalPieces} pieces`;
                    }
                }

                const finalRequired = Math.round(required * 1000) / 1000;
                const finalIssued = Math.round(data.issued * 1000) / 1000;

                if (finalRequired > finalIssued) {
                    isValid = false;
                    errors.push({
                        art: art, required: finalRequired, issued: finalIssued, calc: calcDetails,
                        cat: data.cat_id || '', mat_id: data.mat_id || '', grn_no: data.grn_no || '',
                        already_issued: data.already_issued || 0
                    });
                }
            }

            if (!isValid || errors.length > 0) {
                console.log("Validation Failed");
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

        function fetchFreshStockData(poId) {
            return new Promise((resolve, reject) => {
                if (!poId) {
                    reject('No Purchase Order ID found');
                    return;
                }
                const jobCardId = '{{ $jobCard ? $jobCard->id : "" }}';
                $.ajax({
                    url: `{{ url('job_card_entries/check-stock') }}/${poId}?job_card_id=${jobCardId}`,
                    type: 'GET',
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

            const grandTotal1 = $('#article-qty-matrix-1-grand-total').text().trim();
            const grandTotal2 = $('#article-qty-matrix-2-grand-total').text().trim();
            const total1 = parseFloat(grandTotal1) || 0;
            const total2 = parseFloat(grandTotal2) || 0;

            const $matrixError = $('#article-matrix-error');
            if (total1 <= 0 && total2 <= 0) {
                $matrixError.text('Please enter at least one quantity in the Article Quantity Matrix.').show();
                $('html, body').animate({
                    scrollTop: $("#article-matrix-card").offset().top - 100
                }, 500);
                return;
            } else {
                $matrixError.hide();
            }

            let missingFabricArtNos = [];
            $('.art-no-input').each(function() {
                const $row = $(this).closest('tr'); 
                const artNo = $(this).val();
                
                let rowQty = 0;
                $(`.sleeve-qty-input[data-art="${artNo}"]`).each(function() {
                    rowQty += parseFloat($(this).val()) || 0;
                });

                if (rowQty <= 0) {
                    missingFabricArtNos.push(artNo);
                }
            });

            const $fabricError = $('#fabric-details-error');
            if (missingFabricArtNos.length > 0) {
                $fabricError.text('Please enter Sleeve Wise Qty for Art No: ' + missingFabricArtNos.join(', ')).show();
                $('html, body').animate({
                    scrollTop: $("#fabric-details-card").offset().top - 100
                }, 500);
                return;
            } else {
                $fabricError.hide();
            }

            const $form = $(this);
            const $btn = $form.find('[type="submit"]');
            const originalHtml = $btn.html();
            
            const poId = $('#purchase_order').val() || $('#purchase_order_id').val();
            
            if (!poId) {
                alert('Please select a Purchase Order first.');
                return;
            }

            $btn.prop('disabled', true).html('<i class="ri ri-loader-4-line ri-spin"></i> Checking Stock...');

            fetchFreshStockData(poId)
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
            const poId = $('#purchase_order').val() || $('#purchase_order_id').val();
            fetchFreshStockData(poId)
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
                    renderArticleQtyMatrix(currentArtNumbers, globalActiveSizes.fs, globalActiveSizes.hs);
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
                            
                            if (id === '#article-qty-matrix-1') {
                                if (col.startsWith('fs')) totalFS += val;
                                else if (col.startsWith('hs')) totalHS += val;
                            }
                        });
                    });

                    $(`${id} .col-total`).each(function() {
                        const col = $(this).data('col');
                        const sum = colSums[col] || 0;
                        $(this).text(sum > 0 ? (sum % 1 === 0 ? sum : sum.toFixed(3)) : ''); 
                    });
                    $(`${id}-grand-total`).text(tableGrandTotal > 0 ? (tableGrandTotal % 1 === 0 ? tableGrandTotal : tableGrandTotal.toFixed(3)) : '');
                });

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
            const $this = $(this);
            const $selected = $this.find(':selected');
            const selectedValue = $this.val();
            
            if (selectedValue) {
                const poId = $('#purchase_order').val();
                if (!poId) {
                    showFieldError('#purchase_order', 'Please select Purchase Order first');
                    $this.val('').trigger('change.select2');
                    return;
                }
                
                const processGroupId = $('#process_group_id').val();
                if (!processGroupId) {
                    showFieldError('#process_group_display', 'Please select Process Group first');
                    $this.val('').trigger('change.select2');
                    return;
                }
            }
            
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

                if (!vMtr && currentArtData && currentArtData.length > 0) {
                    const d = currentArtData.find(d => d.art_no == art);
                    if (d) {
                        vMtr = d.mtr || '';
                    }
                }

                widthRow += `<td class="fw-bold">WIDTH</td><td><input type="text" name="fabrics[${index}][width]" class="form-control form-control-sm text-center" value="${vWidth}"></td>`;
                mtrRow += `<td class="fw-bold">Mtr/B.M</td><td><input type="text" name="fabrics[${index}][mtr]" class="form-control form-control-sm text-center mtr-input" data-art="${art}" value="${vMtr}"></td>`;
                inOutRow += `<td class="fw-bold">IN/OUT</td><td><input type="text" name="fabrics[${index}][in_out]" class="form-control form-control-sm text-center" value="${vInOut}"></td>`;
                nPattiRow += `<td class="fw-bold">N.PATTI</td><td><input type="text" name="fabrics[${index}][n_patti]" class="form-control form-control-sm text-center" value="${vNPatti}"></td>`;
                
                let uom = '';
                let catId = 0;
                if (currentArtData && currentArtData.length > 0) {
                    const d = currentArtData.find(d => d.art_no == art);
                    if (d) {
                        uom = d.uom_code || '';
                        catId = d.store_category_id || 0;
                    }
                }

                if (catId === 1) {
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
                                <td><input type="text" name="fabrics[${index}][consumptions][${sz}][fs_cons]" class="form-control form-control-sm text-center p-0 sleeve-qty-input size-cons-input" data-art="${art}" data-size="${sz}" data-type="fs" data-uom="${uom}" data-category="${catId}" value="${vSzFs}"></td>
                                <td><input type="text" name="fabrics[${index}][consumptions][${sz}][hs_cons]" class="form-control form-control-sm text-center p-0 sleeve-qty-input size-cons-input" data-art="${art}" data-size="${sz}" data-type="hs" data-uom="${uom}" data-category="${catId}" value="${vSzHs}"></td>
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
                            <input type="text" name="fabrics[${index}][fs_qty]" class="form-control text-center px-1 sleeve-qty-input" data-art="${art}" data-type="fs" data-uom="${uom}" data-category="${catId}" value="${vFsQty}">
                            <span class="input-group-text px-1">H/S</span>
                            <input type="text" name="fabrics[${index}][hs_qty]" class="form-control text-center px-1 sleeve-qty-input" data-art="${art}" data-type="hs" data-uom="${uom}" data-category="${catId}" value="${vHsQty}">
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
                    if (matrixItems.length > 0) {
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
            $el.closest('.col-md-6, .col-xl-4, .col-lg-4, .input-group, .form-group').find('.text-danger.small').fadeOut(function() {
                $(this).remove();
            });
        });

        function showFieldError(selector, message) {
            const $el = $(selector);
            $el.addClass('is-invalid');
            const $container = $el.closest('.col-md-6, .col-xl-4, .col-lg-4, .input-group, .form-group');
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
                $('.qty-direct-input[data-type="fs"]').each(function() {
                    const val = parseFloat($(this).val()) || 0;
                    const size = String($(this).data('size'));
                    if (!activeFsSizes.includes(size)) activeFsSizes.push(size);
                });
                $('.qty-direct-input[data-type="hs"]').each(function() {
                    const val = parseFloat($(this).val()) || 0;
                    const size = String($(this).data('size'));
                    if (!activeHsSizes.includes(size)) activeHsSizes.push(size);
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
                    <select name="production_stages[${stageRowIndex}][employee_id]" class="form-select select2 employee-select" data-placeholder="Select Employee">
                        <option value="">Select Employee</option>
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
            let $employeeSelect = $row.find('.employee-select');

            $providerSelect.html('<option value="">Select Unit</option>').trigger('change');
            $employeeSelect.html('<option value="">Select Employee</option>').trigger('change');

            if (stageId) {
                $.ajax({
                    url: `{{ url('get-service-providers-by-stage') }}/${stageId}`,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            response.providers.forEach(p => {
                                let selected = ($providerSelect.data('selected') == p.id) ? 'selected' : '';
                                $providerSelect.append(`<option value="${p.id}" ${selected}>${p.name}</option>`);
                            });
                            $providerSelect.trigger('change');
                        }
                    }
                });
            }
        });
        $(document).on('change', '.provider-select', function() {
            let $row = $(this).closest('tr');
            let providerId = $(this).val();
            let stageId = $row.find('.stage-select').val();
            let $employeeSelect = $row.find('.employee-select');
            $employeeSelect.html('<option value="">Select Employee</option>').trigger('change');

            if (providerId) {
                let url = `{{ url('get-employees-by-plant') }}/${providerId}`;
                if (stageId) {
                    url += `/${stageId}`;
                }
                url += '?role_id=5';
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            response.employees.forEach(e => {
                                let selected = ($employeeSelect.data('selected') == e.id) ? 'selected' : '';
                                let label = e.emp_id ? `${e.name} (${e.emp_id})` : e.name;
                                $employeeSelect.append(`<option value="${e.id}" ${selected}>${label}</option>`);
                            });
                            $employeeSelect.trigger('change');
                        }
                    }
                });
            }
        });

        $('.stage-select').each(function() {
            if ($(this).val()) {
                $(this).trigger('change');
            }
        });

        $(document).on('click', '.assign-task-btn', function() {
            let $row = $(this).closest('tr');
            let stageId = $row.find('.stage-select').val();
            let employeeId = $row.find('.employee-select').val();
            let issueDate = $row.find('.issue-date').val();
            let deadlineDate = $row.find('.deadline-date').val();
            let remarks = $row.find('textarea').val();
            let jobCardId = '{{ $jobCard ? $jobCard->id : "" }}';

            if (!stageId || !employeeId) {
                alert('Please select BOTH Stage and Employee before assigning.');
                return;
            }

            let employeeName = $row.find('.employee-select option:selected').text();
            let baseUrl = '{{ route("task_management.add") }}';
            let params = new URLSearchParams({
                job_card_id: jobCardId,
                stage_id: stageId,
                issued_to: employeeId,
                employee_name: employeeName,
                issue_date: issueDate,
                due_date: deadlineDate,
                remarks: remarks
            });
            window.open(baseUrl + '?' + params.toString(), '_blank');
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
</style>
@endsection
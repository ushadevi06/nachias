@extends('layouts.common')
@section('title', ($jobCard ? 'Edit Job Card' : 'Add Job Card') . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <form action="{{ url('job_card_entries/add/'. ($jobCard ?  $jobCard->id : '')) }}" method="POST" class="common-form" enctype="multipart/form-data">
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
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
                                            <option value="{{ $po->id }}" {{ (old('purchase_order_id', $jobCard ? $jobCard->purchase_order_id : '') == $po->id) ? 'selected' : '' }}>
                                                {{ $po->po_number }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="purchase_order">Purchase Order *</label>
                                </div>
                                @error('purchase_order_id') <span class="text-danger">{{ $message }}</span> @enderror
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
                                            <option value="{{ $brand->id }}" {{ (old('brand_id', $jobCard ? $jobCard->brand_id : '') == $brand->id) ? 'selected' : '' }}>
                                                {{ $brand->brand_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="brand">Brand * </label>
                                </div>
                                @error('brand_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="receipt_store" name="receipt_store" class="form-select select2" data-placeholder="Select Receipt Store">
                                        <option value="">Select Receipt Store</option>
                                        <option value="Finished Goods" {{ (old('receipt_store', $jobCard ? $jobCard->receipt_store : '') == 'Finished Goods') ? 'selected' : '' }}>Finished Goods</option>
                                    </select>
                                    <label for="receipt_store">Receipt Store </label>
                                </div>
                                @error('receipt_store') <span class="text-danger">{{ $message }}</span> @enderror
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
                                        <option value="Urgent" {{ (old('status', $jobCard ? $jobCard->status : '') == 'Urgent') ? 'selected' : '' }}>Urgent</option>
                                        <option value="Normal" {{ (old('status', $jobCard ? $jobCard->status : '') == 'Normal') ? 'selected' : '' }}>Normal</option>
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
                            <h4>Tailoring Specification</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select id="fit" name="fit" class="form-select select2" data-placeholder="Select Fit">
                                        <option value="">Select Fit</option>
                                        @foreach($fits as $fit)
                                            <option value="{{ $fit->fit_name }}" {{ (old('fit', $jobCard ? $jobCard->fit : '') == $fit->fit_name) ? 'selected' : '' }}>
                                                {{ $fit->fit_name }}
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
                                <div class="input-group">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="size_ratio_display" name="size_ratio_display" class="form-control" placeholder="Select Size Ratio" readonly value="{{ old('size_ratio_display', $jobCard && $jobCard->sizeRatio ? $jobCard->sizeRatio->display : '') }}">
                                        <input type="hidden" id="size_ratio" name="size_ratio_id" value="{{ old('size_ratio_id', $jobCard ? $jobCard->size_ratio_id : '') }}">
                                        <label for="size_ratio_display">Size Ratio *</label>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#sizeRatioModal">
                                        <i class="ri ri-search-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle" id="cutting-size-table">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="align-middle">SIZE</th>
                                        <th colspan="1" class="ratio-header">CUTTING SIZE RATIO</th>
                                        <th colspan="2" class=""></th>
                                        <th colspan="2">CUTTING MARK AND LAY</th>
                                    </tr>
                                    <tr class="size-header-row">
                                        {{-- Dynamically populated --}}
                                        <th class="extra-col-1"></th>
                                        <th class="extra-col-2"></th>
                                        <th>SIZE</th>
                                        <th>MARK</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Dynamically populated rows: RATIO, QTY-F/S, QTY-H/S --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Fabric Details</h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle">
                                <thead id="fabric-details-head">
                                </thead>
                                <tbody id="fabric-details-body">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Article Quantity Matrix</h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center align-middle" id="article-qty-matrix">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="align-middle" style="min-width: 120px;">ART NO</th>
                                        <th colspan="5">F/S</th>
                                        <th colspan="5">H/S</th>
                                        <th colspan="2">EX</th>
                                        <th rowspan="2" class="align-middle">TOTAL</th>
                                    </tr>
                                    <tr class="size-headers">
                                        <!-- F/S Sizes -->
                                        <th class="mat-fs-head" data-idx="0">36</th>
                                        <th class="mat-fs-head" data-idx="1">38</th>
                                        <th class="mat-fs-head" data-idx="2">40</th>
                                        <th class="mat-fs-head" data-idx="3">42</th>
                                        <th class="mat-fs-head" data-idx="4"></th>
                                        <!-- H/S Sizes -->
                                        <th class="mat-hs-head" data-idx="0">38</th>
                                        <th class="mat-hs-head" data-idx="1">40</th>
                                        <th class="mat-hs-head" data-idx="2">42</th>
                                        <th class="mat-hs-head" data-idx="3">44</th>
                                        <th class="mat-hs-head" data-idx="4">46</th>
                                        <!-- EX -->
                                        <th>40 H/S</th>
                                        <th>38 F/S</th>
                                    </tr>
                                </thead>
                                <tbody id="article-qty-matrix-body">
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="fw-bold text-center">TOTAL</td>
                                        <!-- F/S Totals -->
                                        <td><input type="text" class="form-control form-control-sm col-total text-center fw-bold" data-col="fs-36" readonly tabindex="-1"></td>
                                        <td><input type="text" class="form-control form-control-sm col-total text-center fw-bold" data-col="fs-38" readonly tabindex="-1"></td>
                                        <td><input type="text" class="form-control form-control-sm col-total text-center fw-bold" data-col="fs-40" readonly tabindex="-1"></td>
                                        <td><input type="text" class="form-control form-control-sm col-total text-center fw-bold" data-col="fs-42" readonly tabindex="-1"></td>
                                        <td><input type="text" class="form-control form-control-sm col-total text-center fw-bold" data-col="fs-44" readonly tabindex="-1"></td>
                                        <!-- H/S Totals -->
                                        <td><input type="text" class="form-control form-control-sm col-total text-center fw-bold" data-col="hs-38" readonly tabindex="-1"></td>
                                        <td><input type="text" class="form-control form-control-sm col-total text-center fw-bold" data-col="hs-40" readonly tabindex="-1"></td>
                                        <td><input type="text" class="form-control form-control-sm col-total text-center fw-bold" data-col="hs-42" readonly tabindex="-1"></td>
                                        <td><input type="text" class="form-control form-control-sm col-total text-center fw-bold" data-col="hs-44" readonly tabindex="-1"></td>
                                        <td><input type="text" class="form-control form-control-sm col-total text-center fw-bold" data-col="hs-46" readonly tabindex="-1"></td>
                                        <!-- EX Totals -->
                                        <td><input type="text" class="form-control form-control-sm col-total text-center fw-bold" data-col="ex-1" readonly tabindex="-1"></td>
                                        <td><input type="text" class="form-control form-control-sm col-total text-center fw-bold" data-col="ex-2" readonly tabindex="-1"></td>
                                        <!-- Grand Total -->
                                        <td><input type="text" id="grand-total" class="form-control form-control-sm text-center fw-bold bg-light" readonly tabindex="-1"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>Production Stages</h4>
                        </div>
                        
                        <!-- Stage Selection -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <div class="form-floating form-floating-outline">
                                    <select id="production_stage_select" class="form-select select2" data-placeholder="Select Production Stage">
                                        <option value="">Select Production Stage</option>
                                        @foreach($operationStages as $stage)
                                            <option value="{{ $stage->id }}" data-name="{{ $stage->operation_stage_name }}">
                                                {{ $stage->operation_stage_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="production_stage_select">Select Production Stage *</label>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" id="production_stages_table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 20%;">Stage Name</th>
                                        <th style="width: 20%;">Issue Date</th>
                                        <th style="width: 25%;">Employee</th>
                                        <th style="width: 25%;">Received By</th>
                                        <th style="width: 10%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="production_stages_body">
                                    @php
                                        $stagesToDisplay = old('stages', []);
                                        if (empty($stagesToDisplay) && $jobCard && $jobCard->operations && $jobCard->operations->count() > 0) {
                                            $stagesToDisplay = $jobCard->operations->map(function($op) {
                                                return [
                                                    'stage_id' => $op->operation_stage_id,
                                                    'stage_name' => $op->operationStage->operation_stage_name ?? '',
                                                    'issue_date' => $op->assigned_date ? date('d-m-Y', strtotime($op->assigned_date)) : '',
                                                    'employee_id' => $op->employee_id,
                                                    'received_by' => $op->received_by
                                                ];
                                            })->toArray();
                                        }
                                    @endphp
                                    
                                    @if(!empty($stagesToDisplay))
                                        @foreach($stagesToDisplay as $index => $stage)
                                        <tr data-stage="{{ $stage['stage_id'] ?? '' }}">
                                            <td>
                                                @php
                                                    $stageName = $stage['stage_name'] ?? '';
                                                    if (empty($stageName) && isset($stage['stage_id'])) {
                                                        $stageObj = $operationStages->firstWhere('id', $stage['stage_id']);
                                                        $stageName = $stageObj->operation_stage_name ?? '';
                                                    }
                                                @endphp
                                                <input type="text" class="form-control form-control-sm" value="{{ $stageName }}" readonly>
                                                <input type="hidden" name="stages[{{ $index }}][stage_id]" value="{{ $stage['stage_id'] ?? '' }}">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm dynamic-stage-date" name="stages[{{ $index }}][issue_date]" placeholder="Date" value="{{ $stage['issue_date'] ?? '' }}">
                                            </td>
                                            <td>
                                                <select class="form-select form-select-sm select2-dynamic" name="stages[{{ $index }}][employee_id]">
                                                    <option value="">Select Employee</option>
                                                    @foreach($employees as $emp)
                                                        <option value="{{ $emp->id }}" {{ ($stage['employee_id'] ?? '') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm" name="stages[{{ $index }}][received_by]" placeholder="Received By" value="{{ $stage['received_by'] ?? '' }}">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger remove-stage"><i class="ri ri-delete-bin-line"></i></button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        @error('stages') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        <div class="col-lg-12 text-end mt-5">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ url('job_card_entries') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
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

<div class="modal fade" id="sizeRatioModal" tabindex="-1" aria-labelledby="sizeRatioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="sizeRatioModalLabel">Select Size Ratio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered align-middle text-center" id="sizeRatioTable">
                    <thead class="table-light">
                        <tr>
                            <th>Select</th>
                            <th>Size</th>
                            <th>Ratio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sizeRatios as $ratio)
                        <tr>
                            <td><input type="radio" name="size_ratio_option" value="{{ $ratio->id }}" data-size="{{ $ratio->size }}" data-ratio="{{ $ratio->ratio }}" data-display="({{ $ratio->size }}) - ({{ $ratio->ratio }})"></td>
                            <td>{{ $ratio->size }}</td>
                            <td>{{ $ratio->ratio }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmSizeRatio">Select</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        const oldFabrics = @json(old('fabrics', []));
        const existingImages = @json($jobCard && $jobCard->images ? $jobCard->images : []);
        const existingMatrix = @json($jobCard && $jobCard->articleMatrices ? $jobCard->articleMatrices : []);
        const isEditMode = {{ $jobCard ? 'true' : 'false' }};
        let isSyncing = false;
        let currentArtNumbers = existingMatrix.length > 0 ? existingMatrix.map(m => m.art_no) : [];
        let currentArtData = []; 
        let currentSizes = [];
        let currentRatios = [];
        let currentProcessGroupId = '';
        let currentProcessGroup = '';
        let addedStages = [];

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

        $('#purchase_order').on('change', function() {
            const poId = $(this).val();
            if (!poId) return;

            $.get(`{{ url('job_card_entries/get-po-details') }}/${poId}`, function(data) {
                currentArtNumbers = data.art_numbers;
                currentArtData = data.art_data; 
                renderFabricDetails();
                renderArticleQtyMatrix(data.art_numbers);
            });
        });

        // Trigger initial fetch if PO is already selected (Edit Mode)
        const initialPoId = $('#purchase_order').val();
        if (initialPoId) {
            $.get(`{{ url('job_card_entries/get-po-details') }}/${initialPoId}`, function(data) {
                currentArtNumbers = data.art_numbers;
                currentArtData = data.art_data;
                // Note: renderFabricDetails will pick up mtr from currentArtData if existingMatrix mtr is empty
            });
        }

        function updateMatrixHeaders() {
            const fsCols = ['fs-36', 'fs-38', 'fs-40', 'fs-42', 'fs-44'];
            const hsCols = ['hs-38', 'hs-40', 'hs-42', 'hs-44', 'hs-46'];

            $('.mat-fs-head').each(function(i) {
                const s = (currentSizes && currentSizes[i]) ? currentSizes[i] : '';
                $(this).text(s);
                const $foot = $(`.col-total[data-col="${fsCols[i]}"]`);
                if (!s) $foot.css('background', '#f1f1f1');
                else $foot.css('background', '');
            });
            $('.mat-hs-head').each(function(i) {
                const s = (currentSizes && currentSizes[i]) ? currentSizes[i] : '';
                $(this).text(s);
                const $foot = $(`.col-total[data-col="${hsCols[i]}"]`);
                if (!s) $foot.css('background', '#f1f1f1');
                else $foot.css('background', '');
            });
        }

        function renderArticleQtyMatrix(artNumbers) {
            const currentUIData = [];
            $('#article-qty-matrix-body tr').each(function() {
                const row = {};
                $(this).find('input').each(function() {
                    const name = $(this).attr('name');
                    if (name) {
                        const match = name.match(/\[(\w+)\]$/);
                        if (match) {
                            row[match[1]] = $(this).val();
                        }
                    }
                });
                if (row.art_no) currentUIData.push(row);
            });

            const $tbody = $('#article-qty-matrix-body');
            $tbody.empty();
            
            if (!artNumbers || artNumbers.length === 0) return;

            updateMatrixHeaders();

            artNumbers.forEach((art, index) => {
                const matrixRow = currentUIData.find(m => m.art_no === art) || (existingMatrix ? existingMatrix.find(m => m.art_no === art) : null);
                const getVal = (col) => {
                    if (!matrixRow) return '';
                    return matrixRow[col] || '';
                };

                const isFsDisabled = (idx) => !(currentSizes && currentSizes[idx]) ? 'readonly tabindex="-1" style="background:#f1f1f1"' : '';
                const isHsDisabled = (idx) => !(currentSizes && currentSizes[idx]) ? 'readonly tabindex="-1" style="background:#f1f1f1"' : '';

                const rowHtml = `
                    <tr>
                        <td><input type="text" name="article_matrix[${index}][art_no]" class="form-control form-control-sm text-center" value="${art}" readonly></td>
                        <!-- F/S Inputs -->
                        <td><input type="number" name="article_matrix[${index}][fs_36]" class="form-control form-control-sm qty-input text-center" data-col="fs-36" value="${getVal('fs_36')}" ${isFsDisabled(0)}></td>
                        <td><input type="number" name="article_matrix[${index}][fs_38]" class="form-control form-control-sm qty-input text-center" data-col="fs-38" value="${getVal('fs_38')}" ${isFsDisabled(1)}></td>
                        <td><input type="number" name="article_matrix[${index}][fs_40]" class="form-control form-control-sm qty-input text-center" data-col="fs-40" value="${getVal('fs_40')}" ${isFsDisabled(2)}></td>
                        <td><input type="number" name="article_matrix[${index}][fs_42]" class="form-control form-control-sm qty-input text-center" data-col="fs-42" value="${getVal('fs_42')}" ${isFsDisabled(3)}></td>
                        <td><input type="number" name="article_matrix[${index}][fs_44]" class="form-control form-control-sm qty-input text-center" data-col="fs-44" value="${getVal('fs_44')}" ${isFsDisabled(4)}></td>
                        <!-- H/S Inputs -->
                        <td><input type="number" name="article_matrix[${index}][hs_38]" class="form-control form-control-sm qty-input text-center" data-col="hs-38" value="${getVal('hs_38')}" ${isHsDisabled(0)}></td>
                        <td><input type="number" name="article_matrix[${index}][hs_40]" class="form-control form-control-sm qty-input text-center" data-col="hs-40" value="${getVal('hs_40')}" ${isHsDisabled(1)}></td>
                        <td><input type="number" name="article_matrix[${index}][hs_42]" class="form-control form-control-sm qty-input text-center" data-col="hs-42" value="${getVal('hs_42')}" ${isHsDisabled(2)}></td>
                        <td><input type="number" name="article_matrix[${index}][hs_44]" class="form-control form-control-sm qty-input text-center" data-col="hs-44" value="${getVal('hs_44')}" ${isHsDisabled(3)}></td>
                        <td><input type="number" name="article_matrix[${index}][hs_46]" class="form-control form-control-sm qty-input text-center" data-col="hs-46" value="${getVal('hs_46')}" ${isHsDisabled(4)}></td>
                        <!-- EX Inputs -->
                        <td><input type="number" name="article_matrix[${index}][ex_1]" class="form-control form-control-sm qty-input text-center" data-col="ex-1" value="${getVal('ex_1')}"></td>
                        <td><input type="number" name="article_matrix[${index}][ex_2]" class="form-control form-control-sm qty-input text-center" data-col="ex-2" value="${getVal('ex_2')}"></td>
                        <!-- Row Total -->
                        <td><input type="text" class="form-control form-control-sm row-total text-center fw-bold" readonly tabindex="-1"></td>
                    </tr>`;
                $tbody.append(rowHtml);
            });
            calculateMatrixTotals();
        }

        $(document).on('input', '#article-qty-matrix .qty-input', function() {
            calculateMatrixTotals();
        });

        function calculateMatrixTotals() {
            if (isSyncing) return;
            isSyncing = true;

            try {
                $('#article-qty-matrix tbody tr').each(function() {
                    let rowTotal = 0;
                    $(this).find('.qty-input').each(function() {
                        rowTotal += parseFloat($(this).val()) || 0;
                    });
                    $(this).find('.row-total').val(rowTotal || '');
                });

                const colSums = {};
                let totalFS = 0;
                let totalHS = 0;

                $('#article-qty-matrix .qty-input').each(function() {
                    const col = $(this).data('col');
                    const val = parseFloat($(this).val()) || 0;
                    colSums[col] = (colSums[col] || 0) + val;
                    
                    if (col.startsWith('fs')) {
                        totalFS += val;
                    } else if (col.startsWith('hs')) {
                        totalHS += val;
                    } else if (col === 'ex-1') { 
                        totalHS += val;
                    } else if (col === 'ex-2') {
                        totalFS += val;
                    }
                });

                const hasMatrixRows = $('#article-qty-matrix-body tr').length > 0;

                let grandTotal = 0;
                $('.col-total').each(function() {
                    const col = $(this).data('col');
                    const sum = colSums[col] || 0;
                    $(this).val(sum || '');
                    grandTotal += sum;

                    const fsCols = ['fs-36', 'fs-38', 'fs-40', 'fs-42', 'fs-44'];
                    const hsCols = ['hs-38', 'hs-40', 'hs-42', 'hs-44', 'hs-46'];

                    if (hasMatrixRows) {
                        if (fsCols.includes(col)) {
                            const idx = fsCols.indexOf(col);
                            const s = currentSizes[idx];
                            if (s) $(`.fs-summary-${s}`).val(sum || '');
                        } else if (hsCols.includes(col)) {
                            const idx = hsCols.indexOf(col);
                            const s = currentSizes[idx];
                            if (s) $(`.hs-summary-${s}`).val(sum || '');
                        }
                    }
                });

                $('#grand-total').val(grandTotal || '');

                $('#total_qty_fs').val(totalFS || '');
                $('#total_qty_hs').val(totalHS || '');
            } finally {
                isSyncing = false;
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

        // --- Size Ratio Handling --
        $('#sizeRatioTable tbody tr').on('click', function() {
            $(this).find('input[type="radio"]').prop('checked', true);
        });

        function triggerSizeRatioUpdate() {
             const selected = $('input[name="size_ratio_option"]:checked');
            if (selected.length) {
                const ratioId = selected.val();
                const size = selected.data('size');
                const ratio = selected.data('ratio');
                const display = selected.data('display');
                currentSizes = size.split(',').map(s => s.trim());
                currentRatios = ratio.split(',').map(r => r.trim());
                
                const ratioSum = currentRatios.reduce((a, b) => a + (parseFloat(b) || 0), 0);
                if (ratioSum > 0) {
                    if (!$('#total_qty_fs').val() || $('#total_qty_fs').val() == 0) $('#total_qty_fs').val(ratioSum);
                    if (!$('#total_qty_hs').val() || $('#total_qty_hs').val() == 0) $('#total_qty_hs').val(ratioSum);
                }

                $('#size_ratio_display').val(display);
                $('#size_ratio').val(ratioId);
                
                renderCuttingSizeTable(currentSizes, currentRatios);
                updateMatrixHeaders();
                renderArticleQtyMatrix(getArtNumbers());
                distributeQuantitiesByRatio();
                
                const modalEl = document.getElementById('sizeRatioModal');
                if(modalEl && bootstrap.Modal.getInstance(modalEl)) {
                    bootstrap.Modal.getInstance(modalEl).hide();
                }
            }
        }

        $('#confirmSizeRatio').click(function() {
            triggerSizeRatioUpdate();
        });

        // Initialize Size Ratio from old value
        const initialSizeRatioId = $('#size_ratio').val();
        if(initialSizeRatioId) {
            const $option = $(`input[name="size_ratio_option"][value="${initialSizeRatioId}"]`);
            if ($option.length) {
                $option.prop('checked', true);
                const size = $option.data('size');
                const ratio = $option.data('ratio');
                currentSizes = size.split(',').map(s => s.trim());
                currentRatios = ratio.split(',').map(r => r.trim());

                const ratioSum = currentRatios.reduce((a, b) => a + (parseFloat(b) || 0), 0);
                if (ratioSum > 0) {
                    if (!$('#total_qty_fs').val() || $('#total_qty_fs').val() == 0) $('#total_qty_fs').val(ratioSum);
                    if (!$('#total_qty_hs').val() || $('#total_qty_hs').val() == 0) $('#total_qty_hs').val(ratioSum);
                }

                renderCuttingSizeTable(currentSizes, currentRatios);
                updateMatrixHeaders();
                renderArticleQtyMatrix(getArtNumbers());
                distributeQuantitiesByRatio();
            }
        }

        // --- Process Group Handling ---
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
                $('.qty-input').first().trigger('input'); 
                bootstrap.Modal.getInstance(document.getElementById('processGroupModal')).hide();
            }
        });

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
            });

            $thead.append(headHtml + '</tr>');
            $tbody.append(artRow + '</tr>');
            $tbody.append(widthRow + '</tr>');
            $tbody.append(mtrRow + '</tr>');
            $tbody.append(inOutRow + '</tr>');
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

            // --- RATIO Val Row (Editable 1, 2, 3...) ---

            const selectedRatioDisplay = $('#size_ratio_display').val() || '';
            const sizeStr = selectedRatioDisplay ? selectedRatioDisplay.split(' - ')[0] : '';

            // Helper to add value + info rows
            const addTypeRows = (type, label, isVisible, showInfo = true) => {
                const style = isVisible ? '' : 'display:none;';
                
                let vRow = `<tr class="qty-${type}-row" style="${style}"><td><strong>${label}</strong></td>`;
                sizes.forEach((s, idx) => {
                    const defaultVal = ratios[idx] || '';
                    vRow += `<td>
                        <input type="number" name="matrix_items[${idx}][qty_${type}]" class="form-control form-control-sm text-center fw-bold qty-direct-input ${type}-summary-${s}" data-type="${type}" data-size="${s}" value="${defaultVal}">
                        ${type === 'fs' ? `<input type="hidden" name="matrix_items[${idx}][size]" value="${s}">
                        <input type="hidden" name="matrix_items[${idx}][ratio]" class="ratio-val-input" value="${defaultVal}">
                        <input type="hidden" name="matrix_items[${idx}][article_no]" value="${currentArtNumbers[0] || ''}">` : ''} 
                    </td>`;
                });
                
                vRow += `<td class=""></td><td class=""></td>
                <td><input type="text" name="mark_lay[${type}][size]" class="form-control form-control-sm text-center"></td>
                <td><input type="text" name="mark_lay[${type}][mark]" class="form-control form-control-sm text-center"></td></tr>`;
                $tbody.append(vRow);

                if (showInfo) {
                    let iRow = `<tr class="qty-${type}-info-row" style="${style}"><td><strong>SIZE</strong></td>`;
                    iRow += `<td colspan="${sizes.length}"><input type="text" class="form-control form-control-sm text-center text-muted" value="${sizeStr}" readonly></td>`;
                    iRow += `<td class=""></td><td class=""></td><td></td><td></td></tr>`;
                    $tbody.append(iRow);
                }
            };

            addTypeRows('fs', 'QTY - F/S', hasFS);
            addTypeRows('hs', 'QTY - H/S', hasHS, false);

            syncSummaryToHeader(); 
        }

        function updateQuantityRowVisibility() {
            const name = (currentProcessGroup || '').toUpperCase();
            const hasFS = !currentProcessGroup || name.includes('F/S') || name.includes('FULL');
            const hasHS = !currentProcessGroup || name.includes('H/S') || name.includes('HALF');
            $('#total_qty_fs').closest('.col-md-6').toggle(hasFS);
            $('#total_qty_hs').closest('.col-md-6').toggle(hasHS);
            $('.qty-fs-row, .qty-fs-info-row').toggle(hasFS);
            $('.qty-hs-row').toggle(hasHS);
        }


        // --- Production Stage Handling ---
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
            if (isSyncing) return;
            isSyncing = true;
            try {
                let totalFS = 0, totalHS = 0;
                $('.qty-direct-input[data-type="fs"]').each(function() {
                    totalFS += parseFloat($(this).val()) || 0;
                });
                $('.qty-direct-input[data-type="hs"]').each(function() {
                    totalHS += parseFloat($(this).val()) || 0;
                });
                $('#total_qty_fs').val(totalFS || '');
                $('#total_qty_hs').val(totalHS || '');
            } finally {
                isSyncing = false;
            }
        }


        $(document).on('input', '.qty-direct-input', function() {
            syncSummaryToHeader();
        });

        $(document).on('input', '#total_qty_fs, #total_qty_hs', function() {
            distributeQuantitiesByRatio();
        });

        function distributeQuantitiesByRatio() {
            if (isSyncing) return;
            isSyncing = true;

            try {
                const totalFS = parseFloat($('#total_qty_fs').val()) || 0;
                const totalHS = parseFloat($('#total_qty_hs').val()) || 0;
                
                const ratios = [];
                $('.ratio-val-input').each(function() {
                    ratios.push(parseFloat($(this).val()) || 0);
                });
                
                const ratioSum = ratios.reduce((a, b) => a + b, 0);
                if (ratioSum <= 0) return;

                const fsCols = ['fs-36', 'fs-38', 'fs-40', 'fs-42', 'fs-44'];
                const hsCols = ['hs-38', 'hs-40', 'hs-42', 'hs-44', 'hs-46'];

                currentSizes.forEach((size, idx) => {
                    const ratio = ratios[idx] || 0;
                    const distributedFS = totalFS > 0 ? Math.round((totalFS / ratioSum) * ratio) : 0;
                    const distributedHS = totalHS > 0 ? Math.round((totalHS / ratioSum) * ratio) : 0;

                    $(`.fs-summary-${size}`).val(distributedFS || '');
                    $(`.hs-summary-${size}`).val(distributedHS || '');

                    if (distributedFS > 0) {
                        $('#article-qty-matrix-body tr').each(function() {
                            $(this).find(`input[data-col="${fsCols[idx]}"]`).val(distributedFS);
                        });
                    }
                    if (distributedHS > 0) {
                        $('#article-qty-matrix-body tr').each(function() {
                            $(this).find(`input[data-col="${hsCols[idx]}"]`).val(distributedHS);
                        });
                    }
                });
            } finally {
                isSyncing = false;
                calculateMatrixTotals();
            }
        }

        if ($('#purchase_order').val()) $('#purchase_order').trigger('change');

        // --- Persist Production Stages ---
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

        // Delete image function
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
@endsection
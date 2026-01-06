@extends('layouts.common')
@section('title', ($rawMaterial ? 'Edit' : 'Add') . ' Raw Material - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $rawMaterial ? 'Edit' : 'Add' }} Raw Material</h4>
                    </div>
                    <form action="{{ url('raw_materials/add' . ($rawMaterial ? '/' . $rawMaterial->id : '')) }}"
                        method="POST" class="common-form" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-4">
                            <!-- Store Category -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="store_category_id" id="store_category_id" class="select2 form-select @error('store_category_id') is-invalid @enderror"
                                        data-placeholder="Select Store Category">
                                        <option value="">Select Store Category</option>
                                        @foreach($storeCategories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('store_category_id', $rawMaterial->store_category_id ?? '') == $category->id ? 'selected' : '' }}>
                                            {{ $category->category_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="store_category_id">Store Category <span
                                            class="text-danger">*</span></label>
                                </div>
                                @error('store_category_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Name -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter Name"
                                        name="name" value="{{ old('name', $rawMaterial->name ?? '') }}">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                </div>
                                @error('name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Raw Material Code -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" placeholder="Enter Raw Material Code" name="code" value="{{ old('code', $rawMaterial->code ?? '') }}">
                                    <label for="code">Raw Material Code <span class="text-danger">*</span></label>
                                </div>
                                @error('code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Supplier Design Name -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('supplier_design_name') is-invalid @enderror" id="supplier_design_name"
                                        placeholder="Enter Supplier Design Name" name="supplier_design_name"
                                        value="{{ old('supplier_design_name', $rawMaterial->supplier_design_name ?? '') }}">
                                    <label for="supplier_design_name">Supplier Design Name <span class="text-danger">*</span></label>
                                </div>
                                @error('supplier_design_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>


                            <!-- Size / Width -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('size_width') is-invalid @enderror" id="size_width"
                                        placeholder="Enter Width" name="size_width"
                                        value="{{ old('size_width', $rawMaterial->size_width ?? '') }}">
                                    <label for="size_width">Width</label>
                                </div>
                                @error('size_width')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- UOM -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="uom_id" id="uom_id" class="select2 form-select @error('uom_id') is-invalid @enderror"
                                        data-placeholder="Select UOM">
                                        <option value="">Select UOM</option>
                                        @foreach($uoms as $uom)
                                        <option value="{{ $uom->id }}"
                                            {{ old('uom_id', $rawMaterial->uom_id ?? '') == $uom->id ? 'selected' : '' }}>
                                            {{ $uom->uom_code }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="uom_id">UOM <span class="text-danger">*</span></label>
                                </div>
                                @error('uom_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fabric Type -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="fabric_type_id" id="fabric_type_id" class="select2 form-select @error('fabric_type_id') is-invalid @enderror"
                                        data-placeholder="Select Fabric Type">
                                        <option value="">Select Fabric Type</option>
                                        @foreach($fabricTypes as $fabricType)
                                        <option value="{{ $fabricType->id }}"
                                            {{ old('fabric_type_id', $rawMaterial->fabric_type_id ?? '') == $fabricType->id ? 'selected' : '' }}>
                                            {{ $fabricType->fabric_type }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="fabric_type_id">Fabric Type <span class="text-danger">*</span></label>
                                </div>
                                @error('fabric_type_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Reference Image -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control @error('reference_image') is-invalid @enderror" type="file" id="reference_image" name="reference_image"
                                        accept="image/*">
                                    <label for="reference_image">Reference Images</label>
                                </div>
                                @if($rawMaterial && $rawMaterial->reference_image)
                                <div class="mt-2">
                                    <img src="{{ asset($rawMaterial->reference_image) }}" alt="Reference Image"
                                        style="max-width: 100px; max-height: 100px;">
                                </div>
                                @endif
                                @error('reference_image')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Specifications -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100 @error('specification') is-invalid @enderror" id="specification"
                                        placeholder="Enter Specifications / Quality Notes"
                                        name="specification">{{ old('specification', $rawMaterial->specification ?? '') }}</textarea>
                                    <label for="specification">Specifications / Quality Notes</label>
                                </div>
                                @error('specification')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Minimum Stock Level -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control @error('min_stock') is-invalid @enderror" id="min_stock"
                                        placeholder="Enter Minimum Stock Level" name="min_stock" min="0"
                                        value="{{ old('min_stock', $rawMaterial->min_stock ?? 0) }}">
                                    <label for="min_stock">Minimum Stock Level</label>
                                </div>
                                @error('min_stock')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror"
                                        data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active"
                                            {{ old('status', $rawMaterial->status ?? '') == 'Active' ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="Inactive"
                                            {{ old('status', $rawMaterial->status ?? '') == 'Inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                </div>
                                @error('status')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Buttons -->
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('raw_materials') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
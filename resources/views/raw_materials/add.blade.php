@extends('layouts.common')
@section('title', ($rawMaterial ? 'Edit' : 'Add') . ' Raw Material - ' . env('WEBSITE_NAME'))
@section('content')
    <div class="container-xxl section-padding">
        <div class="row">
            <div class="col-lg-12">
                <div class="col-lg-12">
                    @include('flash_messages')
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="card-header-box">
                            <h4>{{ $rawMaterial ? 'Edit' : 'Add' }} Raw Material</h4>
                        </div>
                        <form action="{{ url('raw_materials/add' . ($rawMaterial ? '/' . $rawMaterial->id : '')) }}" method="POST" class="common-form" enctype="multipart/form-data" autocomplete="off">
                            @csrf
                            <div class="row g-4">
                                <!-- Store Category -->
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <select name="store_category_id" id="store_category_id" class="select2 form-select @error('store_category_id') is-invalid @enderror" data-placeholder="Select Store Category">
                                            <option value="">Select Store Category</option>
                                            @foreach($storeCategories as $category)
                                            <option value="{{ $category->id }}" {{ old('store_category_id', $rawMaterial->store_category_id ?? '') == $category->id ? 'selected' : '' }}>{{ $category->category_name }}</option>
                                            @endforeach
                                        </select>
                                        <label for="store_category_id">Store Category <span class="text-danger">*</span></label>
                                    </div>
                                    @error('store_category_id')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!-- Name -->
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter Name" name="name" value="{{ old('name', $rawMaterial->name ?? '') }}">
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
                                <!-- UOM -->
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <select name="uom_id" id="uom_id" class="select2 form-select @error('uom_id') is-invalid @enderror" data-placeholder="Select UOM">
                                            <option value="">Select UOM</option>
                                            @foreach($uoms as $uom)
                                            <option value="{{ $uom->id }}" {{ old('uom_id', $rawMaterial->uom_id ?? '') == $uom->id ? 'selected' : '' }}>{{ $uom->uom_code }}</option>
                                            @endforeach
                                        </select>
                                        <label for="uom_id">UOM <span class="text-danger">*</span></label>
                                    </div>
                                    @error('uom_id')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!-- Material Type -->
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <select name="material_type" id="material_type" class="select2 form-select @error('material_type') is-invalid @enderror" data-placeholder="Select Material Type">
                                            <option value="">Select Material Type</option>
                                            <option value="Plastic" {{ old('material_type', $rawMaterial->material_type ?? '') == 'Plastic' ? 'selected' : '' }}>Plastic</option>
                                            <option value="Fabric" {{ old('material_type', $rawMaterial->material_type ?? '') == 'Fabric' ? 'selected' : '' }}>Fabric</option>
                                            <option value="Thread" {{ old('material_type', $rawMaterial->material_type ?? '') == 'Thread' ? 'selected' : '' }}>Thread</option>
                                            <option value="Zip" {{ old('material_type', $rawMaterial->material_type ?? '') == 'Zip' ? 'selected' : '' }}>Zip</option>
                                            <option value="Button" {{ old('material_type', $rawMaterial->material_type ?? '') == 'Button' ? 'selected' : '' }}>Button</option>
                                            <option value="Elastic" {{ old('material_type', $rawMaterial->material_type ?? '') == 'Elastic' ? 'selected' : '' }}>Elastic</option>
                                            <option value="Velcro" {{ old('material_type', $rawMaterial->material_type ?? '') == 'Velcro' ? 'selected' : '' }}>Velcro</option>
                                            <option value="Label" {{ old('material_type', $rawMaterial->material_type ?? '') == 'Label' ? 'selected' : '' }}>Label</option>
                                            <option value="Tag" {{ old('material_type', $rawMaterial->material_type ?? '') == 'Tag' ? 'selected' : '' }}>Tag</option>
                                            <option value="Padding" {{ old('material_type', $rawMaterial->material_type ?? '') == 'Padding' ? 'selected' : '' }}>Padding</option>
                                            <option value="Lining" {{ old('material_type', $rawMaterial->material_type ?? '') == 'Lining' ? 'selected' : '' }}>Lining</option>
                                        </select>
                                        <label for="material_type">Material Type</label>
                                    </div>
                                    @error('material_type')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!-- Art Nos -->
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <select name="art_nos[]" id="art_nos" class="select2 form-select @error('art_nos') is-invalid @enderror" multiple data-placeholder="Select Art Nos">
                                            @foreach($artNos as $artNo)
                                                <option value="{{ $artNo }}" {{ (is_array(old('art_nos', $selectedArtNos)) && in_array($artNo, old('art_nos', $selectedArtNos))) ? 'selected' : '' }}>{{ $artNo }}</option>
                                            @endforeach
                                        </select>
                                        <label for="art_nos">Art Nos</label>
                                    </div>
                                    @error('art_nos')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!-- Reference Image -->
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control @error('reference_image') is-invalid @enderror" type="file" id="reference_image" name="reference_image" accept="image/*">
                                        <label for="reference_image">Reference Images</label>
                                    </div>
                                    @if($rawMaterial && $rawMaterial->reference_image)
                                    <div class="mt-2">
                                        <img src="{{ url($rawMaterial->reference_image) }}" alt="Reference Image" style="max-width: 100px; max-height: 100px;">
                                    </div>
                                    @endif
                                    @error('reference_image')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!-- Specifications -->
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <textarea class="form-control h-px-100 @error('specification') is-invalid @enderror" id="specification" placeholder="Enter Specifications / Quality Notes" name="specification">{{ old('specification', $rawMaterial->specification ?? '') }}</textarea>
                                        <label for="specification">Specifications / Quality Notes</label>
                                    </div>
                                    @error('specification')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Minimum Stock -->
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <input type="number" class="form-control @error('min_stock') is-invalid @enderror" id="min_stock" placeholder="Enter Minimum Stock" name="min_stock" min="0" step="0.01" value="{{ old('min_stock', $rawMaterial->min_stock ?? '') }}">
                                        <label for="min_stock">Minimum Stock</label>
                                    </div>
                                    @error('min_stock')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating form-floating-outline">
                                        <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror" data-placeholder="Select Status">
                                            <option value="">Select Status</option>
                                            <option value="Active" {{ old('status', $rawMaterial->status ?? '') == 'Active' ? 'selected' : '' }}>Active</option>
                                            <option value="Inactive" {{ old('status', $rawMaterial->status ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                    </div>
                                    @error('status')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
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


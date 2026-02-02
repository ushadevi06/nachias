@extends('layouts.common')
@section('title', ($consumable ? 'Edit' : 'Add') . ' Configuration - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ $consumable ? 'Edit' : 'Add' }} Configuration</h4>
                </div>
                <div class="card-body">
                    <form action="{{ url('production_stage_consumables/add/' . ($consumable->id ?? '')) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Production Stage *</label>
                                <select name="stage" class="form-select select2" required>
                                    <option value="">Select Stage</option>
                                    @foreach($stages as $stage)
                                        <option value="{{ $stage->name }}" {{ ($consumable->stage ?? '') == $stage->name ? 'selected' : '' }}>{{ $stage->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Raw Material *</label>
                                <select name="raw_material_id" class="form-select select2" required>
                                    <option value="">Select Material</option>
                                    @foreach($rawMaterials as $rm)
                                        <option value="{{ $rm->id }}" {{ ($consumable->raw_material_id ?? '') == $rm->id ? 'selected' : '' }}>{{ $rm->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quantity Per Unit *</label>
                                <input type="number" step="0.0001" name="quantity_per_unit" class="form-control" value="{{ $consumable->quantity_per_unit ?? '' }}" required>
                                <small class="text-muted">How much material is used for 1 piece of production?</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">UOM</label>
                                <select name="uom_id" class="form-select select2">
                                    <option value="">Select UOM</option>
                                    @foreach($uoms as $uom)
                                        <option value="{{ $uom->id }}" {{ ($consumable->uom_id ?? '') == $uom->id ? 'selected' : '' }}>{{ $uom->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="Active" {{ ($consumable->status ?? '') == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Inactive" {{ ($consumable->status ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-12 mt-3 text-end">
                                <a href="{{ url('production_stage_consumables') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save Configuration</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.common')
@section('title', 'Standard Consumption - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $standardConsumption ? 'Edit' : 'Add' }} Standard Consumption</h4>
                    </div>
                    <form action="{{ url('standard_consumptions/add' . ($standardConsumption ? '/' . $standardConsumption->id : '')) }}" method="POST" class="common-form" autocomplete="off">
                        @csrf
                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('raw_material_id') is-invalid @enderror" id="raw_material_search" name="raw_material_search" placeholder="Search Raw Material" autocomplete="off" value="{{ old('raw_material_search', $standardConsumption && $standardConsumption->rawMaterial ? $standardConsumption->rawMaterial->name . ' (' . $standardConsumption->rawMaterial->code . ')' : '') }}">
                                    <input type="hidden" name="raw_material_id" id="raw_material_id" value="{{ old('raw_material_id', $standardConsumption ? $standardConsumption->raw_material_id : '') }}">
                                    <label for="raw_material_search">Raw Material <span class="text-danger">*</span></label>
                                </div>
                                @error('raw_material_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="any" class="form-control @error('fs_qty') is-invalid @enderror" id="fs_qty" placeholder="Enter F/S Qty" name="fs_qty" value="{{ old('fs_qty', isset($standardConsumption->fs_qty) ? (float) $standardConsumption->fs_qty : '') }}" min="0">
                                    <label for="fs_qty">F/S Qty <span class="text-danger">*</span></label>
                                </div>
                                @error('fs_qty')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="any" class="form-control @error('hs_qty') is-invalid @enderror" id="hs_qty" placeholder="Enter H/S Qty" name="hs_qty" value="{{ old('hs_qty', isset($standardConsumption->hs_qty) ? (float) $standardConsumption->hs_qty : '') }}" min="0">
                                    <label for="hs_qty">H/S Qty <span class="text-danger">*</span></label>
                                </div>
                                @error('hs_qty')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active" {{ old('status', $standardConsumption->status ?? '') == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status', $standardConsumption->status ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                </div>
                                @error('status')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('standard_consumptions') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
    $(document).ready(function() {
        $('#raw_material_search').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "{{ url('ajax/search_raw_materials') }}",
                    dataType: "json",
                    data: {
                        q: request.term
                    },
                    success: function(data) {
                        response($.map(data.results, function(item) {
                            return {
                                label: item.text,
                                value: item.id
                            }
                        }));
                    }
                });
            },
            minLength: 1,
            select: function(event, ui) {
                $('#raw_material_search').val(ui.item.label);
                $('#raw_material_id').val(ui.item.value);
                return false;
            },
            change: function(event, ui) {
                if (!ui.item && !$('#raw_material_id').val()) {
                    $('#raw_material_search').val('');
                    $('#raw_material_id').val('');
                }
            }
        });
        
        $('#raw_material_search').on('input', function() {
            $('#raw_material_id').val('');
        });
    });
</script>
<style>
    .ui-autocomplete {
        z-index: 10000 !important;
        max-height: 300px;
        overflow-y: auto;
        overflow-x: hidden;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        border: 1px solid #e0e2ef;
    }
    .ui-menu-item {
        border-bottom: 1px solid #f0f1f8;
    }
    .ui-menu-item:last-child {
        border-bottom: none;
    }
    .ui-menu-item .ui-menu-item-wrapper {
        padding: 10px 15px !important;
        transition: all 0.2s;
    }
    .ui-menu-item .ui-menu-item-wrapper.ui-state-active {
        background-color: #f4f5fb !important;
        color: var(--bs-primary) !important;
        border: none !important;
        margin: 0 !important;
    }
</style>
@endsection

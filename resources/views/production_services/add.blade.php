@extends('layouts.common')
@section('title', ($service ? 'Edit Service' : 'Add Service') . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-12">
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $service ? 'Edit' : 'Add' }} Production Service</h4>
                    </div>
                    <form action="{{ url('production_services/add' . ($service ? '/' . $service->id : '')) }}" method="POST" class="common-form" autocomplete="off">
                        @csrf
                        <div class="row g-4">
                            <div class="col-12">
                                <h5 class="mb-2 text-primary border-bottom pb-1">Basic Identification</h5>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('service_code') is-invalid @enderror" id="service_code" placeholder="Enter Service Code" name="service_code" value="{{ old('service_code', $service->service_code ?? '') }}">
                                    <label for="service_code">Service Code <span class="text-danger">*</span></label>
                                </div>
                                @error('service_code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('service_name') is-invalid @enderror" id="service_name" placeholder="Enter Service Name" name="service_name" value="{{ old('service_name', $service->service_name ?? '') }}">
                                    <label for="service_name">Service Name <span class="text-danger">*</span></label>
                                </div>
                                @error('service_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <select name="operation_stage_id" id="operation_stage_id" class="select2 form-select @error('operation_stage_id') is-invalid @enderror" data-placeholder="Select Production Stage">
                                        <option value="">Select Production Stage</option>
                                        @foreach($operationStages as $stage)
                                        <option value="{{ $stage->id }}" {{ old('operation_stage_id', $service->operation_stage_id ?? '') == $stage->id ? 'selected' : '' }}>{{ $stage->operation_stage_name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="operation_stage_id">Production Stage <span class="text-danger">*</span></label>
                                </div>
                                @error('operation_stage_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control @error('sequence') is-invalid @enderror" id="sequence" name="sequence" placeholder="Enter Sequence" value="{{ old('sequence', $service->sequence ?? '') }}">
                                    <label for="sequence">Sequence <span class="text-danger">*</span></label>
                                </div>
                                @error('sequence')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror">
                                        <option value="Active" {{ old('status', $service->status ?? 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status', $service->status ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                </div>
                                @error('status')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="number"
                                        class="form-control @error('cost') is-invalid @enderror"
                                        id="cost" name="cost"
                                        placeholder="Enter Cost"
                                        min="0" step="0.01"
                                        value="{{ old('cost', $service->cost ?? '') }}">
                                    <label for="cost">Cost </label>
                                </div>
                                @error('cost')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <select name="process_group_ids[]" id="process_group_ids" class="select2 form-select @error('process_group_ids') is-invalid @enderror" data-placeholder="Select Process Groups" multiple>
                                        @foreach($processGroups as $group)
                                        @php
                                            $selected = false;
                                            if (is_array(old('process_group_ids'))) {
                                                $selected = in_array($group->id, old('process_group_ids'));
                                            } elseif ($service && $service->processGroups) {
                                                $selected = $service->processGroups->contains($group->id);
                                            }
                                        @endphp
                                        <option value="{{ $group->id }}" {{ $selected ? 'selected' : '' }}>{{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="process_group_ids">Process Groups <span class="text-danger">*</span></label>
                                </div>
                                @error('process_group_ids')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Quantity Calculation Logic -->
                            <div class="col-12 mt-4">
                                <h5 class="mb-2 text-primary border-bottom pb-1">Quantity Calculation Logic</h5>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <select name="applies_to" id="applies_to" class="select2 form-select @error('applies_to') is-invalid @enderror">
                                        @foreach(['Full Sleeve', 'Half Sleeve', 'Both'] as $opt)
                                        <option value="{{ $opt }}" {{ old('applies_to', $service->applies_to ?? 'ALL') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                    <label for="applies_to">Applies To <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating form-floating-outline">
                                    <select name="base_quantity_source" id="base_quantity_source" class="select2 form-select @error('base_quantity_source') is-invalid @enderror">
                                        @foreach(['Total Qty', 'FS Qty', 'HS Qty'] as $opt)
                                        <option value="{{ $opt }}" {{ old('base_quantity_source', $service->base_quantity_source ?? 'Total Qty') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                    <label for="base_quantity_source">Base Quantity Source <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-lg-12 text-end mt-4">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('production_services') }}" class="btn btn-secondary">Cancel</a>
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
<script>
$(document).ready(function() {
    $('#operation_stage_id').on('change', function() {
        let stageId = $(this).val();
        if (!stageId) {
            $('#sequence').val('');
            return;
        }
        $.ajax({
            url: "{{ url('production-services/get-next-sequence') }}/" + stageId,
            type: "GET",
            success: function(response) {
                $('#sequence').val(response.sequence);
            }
        });
    });
});
</script>
@endsection
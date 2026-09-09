@extends('layouts.common')
@section('title', ($setting && $setting->id ? 'Edit Core Material Planning Master' : 'Add Core Material Planning Master') . ' - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="col-lg-12">
                @include('flash_messages')
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-header-box">
                        <h4>{{ $setting && $setting->id ? 'Edit' : 'Add' }} Core Material Planning Master</h4>
                    </div>
                    <form action="{{ url('core-material-settings/add' . ($setting && $setting->id ? '/' . $setting->id : '')) }}" method="POST" class="common-form" autocomplete="off">
                        @csrf
                        <div class="row g-4 justify-content-center">
                            <!-- Art No Field with Autocomplete -->
                            <div class="col-md-6 col-xl-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control @error('art_no') is-invalid @enderror" id="art_no_input" name="art_no" placeholder="Type or Select Art No" value="{{ old('art_no', $setting->art_no ?? '') }}" autocomplete="off" {{ ($setting && $setting->id) ? 'readonly' : '' }}>
                                    <label for="art_no_input">Art No <span class="text-danger">*</span></label>
                                </div>
                                @error('art_no')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Brand Selection -->
                            <div class="col-md-6 col-xl-6">
                                <div class="form-floating form-floating-outline">
                                    <select name="brand_id" id="brand_id_select" class="select2 form-select @error('brand_id') is-invalid @enderror" data-placeholder="Select Brand">
                                        <option value="">Select Brand</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}" {{ old('brand_id', $setting->brand_id ?? '') == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->brand_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="brand_id_select">Brand</label>
                                </div>
                                @error('brand_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Daily Consumption -->
                            <div class="col-md-4 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" min="0" class="form-control @error('daily_consumption') is-invalid @enderror" id="daily_consumption" name="daily_consumption" placeholder="Enter Daily Consumption" value="{{ old('daily_consumption', $setting->daily_consumption ?? '') }}">
                                    <label for="daily_consumption">Daily Consumption (M/Day) <span class="text-danger">*</span></label>
                                </div>
                                @error('daily_consumption')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Supplier Lead Time -->
                            <div class="col-md-4 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" min="0" class="form-control @error('supplier_lead_time') is-invalid @enderror" id="supplier_lead_time" name="supplier_lead_time" placeholder="Enter Supplier Lead Time" value="{{ old('supplier_lead_time', $setting->supplier_lead_time ?? '') }}">
                                    <label for="supplier_lead_time">Supplier Lead Time (Days) <span class="text-danger">*</span></label>
                                </div>
                                @error('supplier_lead_time')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Safety Stock -->
                            <div class="col-md-4 col-xl-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" min="0" class="form-control @error('safety_stock') is-invalid @enderror" id="safety_stock" name="safety_stock" placeholder="Enter Safety Stock" value="{{ old('safety_stock', $setting->safety_stock ?? '') }}">
                                    <label for="safety_stock">Safety Stock (M) <span class="text-danger">*</span></label>
                                </div>
                                @error('safety_stock')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-md-12 col-xl-12">
                                <div class="form-floating form-floating-outline">
                                    <select name="status" id="status" class="select2 form-select @error('status') is-invalid @enderror" data-placeholder="Select Status">
                                        <option value="">Select Status</option>
                                        <option value="Active" {{ old('status', $setting->status ?? 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status', $setting->status ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                </div>
                                @error('status')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-lg-12 text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ url('core-material-settings') }}" class="btn btn-secondary">Cancel</a>
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
        let artList = [
            @foreach($artNumbers as $art)
            {
                label: "{!! addslashes($art->art_no) !!}{!! $art->brand_name ? ' ('.addslashes($art->brand_name).')' : '' !!}",
                value: "{!! addslashes($art->art_no) !!}",
                brand_id: "{{ $art->brand_id }}"
            },
            @endforeach
        ];

        $('#art_no_input').autocomplete({
            source: function(request, response) {
                let term = request.term.toLowerCase();
                let matches = $.grep(artList, function(item) {
                    return item.label.toLowerCase().indexOf(term) !== -1 || item.value.toLowerCase().indexOf(term) !== -1;
                });
                response(matches);
            },
            minLength: 1,
            select: function(event, ui) {
                $('#art_no_input').val(ui.item.value);
                if (ui.item.brand_id) {
                    $('#brand_id_select').val(ui.item.brand_id).trigger('change');
                } else {
                    fetchArtBrand(ui.item.value);
                }
                return false;
            }
        }).on('blur', function() {
            let val = $(this).val().trim();
            if (val) {
                fetchArtBrand(val);
            }
        });
    });

    function fetchArtBrand(artNo) {
        if (!artNo) return;
        $.ajax({
            url: "{{ url('core-material-settings/get-art-brand') }}/" + encodeURIComponent(artNo),
            type: 'GET',
            success: function(res) {
                if (res.brand_id) {
                    $('#brand_id_select').val(res.brand_id).trigger('change');
                }
            }
        });
    }
</script>
<style>
    .ui-autocomplete {
        z-index: 10000 !important;
        max-height: 250px;
        overflow-y: auto;
        overflow-x: hidden;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        border: 1px solid #e0e2ef;
        background-color: #ffffff;
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
        font-size: 14px;
    }
    .ui-menu-item .ui-menu-item-wrapper.ui-state-active {
        background-color: #f4f5fb !important;
        color: #7367f0 !important;
        border: none !important;
        margin: 0 !important;
    }
</style>
@endsection

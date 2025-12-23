@extends('layouts.common')
@section('title', 'View Item - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Item</h4>
                <a href="{{ url('items') }}" class="btn btn-primary"><i
                        class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="detail-title">Brand:</label>
                            <div class="text-muted">{{ $item->brand->brand_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Brand Category:</label>
                            <div class="text-muted">
                                @if($item->brandCategory)
                                {{ $item->brandCategory->name }}({{ $item->brandCategory->code }})
                                @else
                                -
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Item Name:</label>
                            <div class="text-muted">{{ $item->name }}({{ $item->code }})</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Entry Type:</label>
                            <div class="text-muted">{{ ucfirst(str_replace('_', ' ', $item->entry_type ?? '-')) }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Style:</label>
                            <div class="text-muted">{{ $item->style ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Fabric Type:</label>
                            <div class="text-muted">{{ $item->fabricType->fabric_type ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Design Art Number:</label>
                            <div class="text-muted">{{ $item->design_art_no ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">UOM:</label>
                            <div class="text-muted">{{ $item->uom->uom_code ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Size / Ratio:</label>
                            <div class="text-muted">
                                @if($item->sizeRatio)
                                {{ $item->sizeRatio->size }}({{ $item->sizeRatio->ratio }})
                                @else
                                -
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Color:</label>
                            <div class="text-muted">
                                @if($colors->count() > 0)
                                {{ $colors->pluck('color_name')->implode(', ') }}
                                @else
                                -
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Product Barcode:</label>
                            <div class="text-muted">{{ $item->product_barcode ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Standard Costing:</label>
                            <div class="text-muted">
                                {{ $item->standard_costing ? '₹' . number_format($item->standard_costing, 2) : '-' }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Status:</label>
                            <div class="{{ $item->status === 'Active' ? 'text-success' : 'text-danger' }}">
                                {{ $item->status }}
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Item Details (BOM):</h6>
                        </div>
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Store Category</th>
                                            <th>Material</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($item->related_materials && count($item->related_materials) > 0)
                                        @foreach($item->related_materials as $index => $material)
                                        <tr>
                                            <td>{{ $index }}</td>
                                            <td>{{ $material['category_name'] ?? '-' }}</td>
                                            <td>{{ $material['material_name'] ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="3" class="text-center">No materials added</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <h6>Operation Stages:</h6>
                        </div>
                        @php
                        $itemStages = is_array($item->operation_stages) ? $item->operation_stages : [];
                        $itemProviders = is_array($item->service_providers) ? $item->service_providers : [];
                        @endphp

                        @foreach($operationStages as $stage)
                        @php
                        $stageName = $stage->operation_stage_name;
                        $stageKey = strtolower($stageName);
                        $isEnabled = in_array($stageName, $itemStages);
                        @endphp

                        @if($isEnabled)
                        <div class="col-md-3">
                            <label class="detail-title">{{ $stageName }}:</label>
                            <div class="text-muted">
                                @php
                                $providerId = $itemProviders[$stageKey] ?? null;
                                $provider = $providerId ? \App\Models\ServiceProvider::find($providerId) : null;
                                @endphp
                                {{ $provider ? $provider->name . ' (' . $provider->code . ')' : '-' }}
                            </div>
                        </div>
                        @endif
                        @endforeach
                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Pricing:</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Wholesale Price:</label>
                            <div>{{ $item->wholesale_price ? '₹' . number_format($item->wholesale_price, 2) : '-' }}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Retail Price:</label>
                            <div>{{ $item->retail_price ? '₹' . number_format($item->retail_price, 2) : '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="detail-title">Export Price:</label>
                            <div>{{ $item->export_price ? '₹' . number_format($item->export_price, 2) : '-' }}</div>
                        </div>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                        <div class="col-lg-12">
                            <h6>Other Information:</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Created By:</label>
                            <div class="text-muted">{{ createdByName($item->created_by) }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Created At:</label>
                            <div class="text-muted">{{ $item->created_at->format('d M Y, h:i A') }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Updated At:</label>
                            <div class="text-muted">{{ $item->updated_at->format('d M Y, h:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
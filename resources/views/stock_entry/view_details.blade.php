@extends('layouts.common')
@section('title', 'View Stock Entry - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Stock Entry</h4>
                <a href="{{ url('stock_entries') }}" class="btn btn-primary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="detail-title">Stock Entry No: </label>
                            <div class="text-muted">{{ $stockEntry->stock_entry_no }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Stock Date:</label>
                            <div class="text-muted">{{ $stockEntry->stock_date->format('d-m-Y') }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Entry Type:</label>
                            <div class="text-muted">{{ $stockEntry->entry_type }}</div>
                        </div>

                        @if($stockEntry->entry_type == 'Purchase Receipt' && $stockEntry->grnEntry)
                        <div class="col-md-4">
                            <label class="detail-title">GRN Number:</label>
                            <div class="text-muted">{{ $stockEntry->grnEntry->grn_number }}</div>
                        </div>
                        @endif

                        @foreach($stockEntry->stockEntryItems as $item)
                        <div class="col-md-4">
                            <label class="detail-title">Type:</label>
                            <div class="text-muted">{{ ucwords(str_replace('_', ' ', $item->stock_type)) }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Store Category:</label>
                            <div class="text-muted">{{ $item->storeCategory->category_name ?? '-' }}({{ $item->storeCategory->code ?? '-' }})</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Material:</label>
                            <div class="text-muted">
                                @if($item->stock_type == 'raw_material')
                                    {{ $item->rawMaterial->name ?? '-' }}({{ $item->rawMaterial->code ?? '-' }})
                                @else
                                    {{ $item->finished_item_code ?? '-' }}
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">UOM:</label>
                            <div class="text-muted">{{ $item->uom->uom_code ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Quantity In:</label>
                            <div class="text-muted">{{ $item->qty_in + 0 }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Quantity Out:</label>
                            <div class="text-muted">{{ $item->qty_out > 0 ? $item->qty_out + 0 : '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Location / Store:</label>
                            <div class="text-muted">{{ $item->storeLocation->store_location ?? '-' }}</div>
                        </div>

                        @if($item->grnEntryItem)
                        <div class="col-md-4">
                            <label class="detail-title">GRN Item (Art No):</label>
                            <div class="text-muted">{{ $item->grnEntryItem->art_no ?? '-' }}</div>
                        </div>
                        @endif
                        @endforeach

                        <div class="col-md-4">
                            <label class="detail-title">Remarks:</label>
                            <div class="text-muted">{{ $stockEntry->remarks ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Reference Document:</label>
                            <div class="text-muted">
                                @if($stockEntry->reference_document)
                                    <a href="{{ asset($stockEntry->reference_document) }}" target="_blank">View</a>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
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

                        @php
                            $firstItem = $stockEntry->stockEntryItems->first();
                            $totalQtyIn = $stockEntry->stockEntryItems->sum('qty_in');
                            $totalQtyOut = $stockEntry->stockEntryItems->sum('qty_out');
                        @endphp

                        @if($stockEntry->grnEntry)
                        <div class="col-md-4">
                            <label class="detail-title">GRN Number:</label>
                            <div class="text-muted">{{ $stockEntry->grnEntry->grn_number }}</div>
                        </div>
                        @endif

                        <div class="col-md-4">
                            <label class="detail-title">Entry Type:</label>
                            <div class="text-muted">{{ $stockEntry->entry_type ?? '-' }}</div>
                        </div>


                        @if($firstItem)
                        <div class="col-md-4">
                            <label class="detail-title">Category:</label>
                            <div class="text-muted">{{ $firstItem->storeCategory->category_name ?? '-' }} ({{ $firstItem->storeCategory->code ?? '-' }})</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Material / Item:</label>
                            <div class="text-muted">
                                @if($firstItem->stock_type == 'raw_material')
                                    {{ $firstItem->rawMaterial->name ?? '-' }} ({{ $firstItem->rawMaterial->code ?? '-' }})
                                @else
                                    {{ $firstItem->finished_item_code ?? '-' }}
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Art No:</label>
                            <div class="text-muted">{{ $firstItem->grnEntryItem->art_no ?? '-' }}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">UOM:</label>
                            <div class="text-muted">{{ $firstItem->uom->uom_code ?? '-' }}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Unit Price:</label>
                            <div class="text-muted">{{ $firstItem->price > 0 ? '₹' . number_format($firstItem->price, 2) : '-' }}</div>
                        </div>
                        @endif

                        <div class="col-md-4">
                            <label class="detail-title">Total Quantity In:</label>
                            <div class="text-muted text-success fw-bold">+{{ $totalQtyIn + 0 }}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Remarks:</label>
                            <div class="text-muted">{{ $stockEntry->remarks ?? '-' }}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title">Reference Document:</label>
                            <div class="text-muted">
                                @if($stockEntry->reference_document)
                                    <a href="{{ url('uploads/stock_entries/' . $stockEntry->reference_document) }}" target="_blank">View Document</a>
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
@extends('layouts.common')
@section('title', 'View Stock Entry - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box">
                <h4>View Stock Entry</h4>
                <a href="{{ url('stock_entries') }}" class="btn btn-outline-secondary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
            </div>
            <div class="card detail-card">
                <div class="card-body">
                    <div class="row g-4">
                        @php
                            $firstItem = $stockEntry->stockEntryItems->first();
                            $totalQtyIn = $stockEntry->stockEntryItems->sum('qty_in');
                            $totalQtyOut = $stockEntry->stockEntryItems->sum('qty_out');
                            $isRawMaterial = ($stockEntry->entry_type === 'Raw Material');
                            $isFinishedGoods = ($stockEntry->entry_type === 'Finished Goods');
                        @endphp

                        @if($isRawMaterial)
                        <div class="col-md-4">
                            <label class="detail-title">Stock Entry No: </label>
                            <div class="text-muted">{{ $stockEntry->stock_entry_no }}</div>
                        </div>
                        @endif

                        <div class="col-md-4">
                            <label class="detail-title">Stock Date:</label>
                            <div class="text-muted">{{ $stockEntry->stock_date->format('d-m-Y') }}</div>
                        </div>

                        @if($isRawMaterial && $stockEntry->grnEntry)
                        <div class="col-md-4">
                            <label class="detail-title">GRN Number:</label>
                            <div class="text-muted">{{ $stockEntry->grnEntry->grn_number }}</div>
                        </div>
                        @elseif($isFinishedGoods)
                        <div class="col-md-4">
                            <label class="detail-title">Job Card Number:</label>
                            <div class="text-muted">
                                {{ ($stockEntry->productionReceipt && $stockEntry->productionReceipt->jobCard) 
                                    ? $stockEntry->productionReceipt->jobCard->job_card_no 
                                    : '-' }}
                            </div>
                        </div>
                        @endif

                        <div class="col-md-4">
                            <label class="detail-title">Entry Type:</label>
                            <div class="text-muted">{{ $stockEntry->entry_type ?? '-' }}</div>
                        </div>

                        @if($isRawMaterial)
                        <div class="col-md-12 mt-4">
                            <h5>Stock Entry Items</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Material / Category</th>
                                            <th>Art No</th>
                                            <th>UOM</th>
                                            <th>Qty In</th>
                                            <th>Unit Price</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stockEntry->stockEntryItems as $item)
                                        <tr>
                                            <td>
                                                {{ $item->rawMaterial->name ?? '-' }} <br>
                                                <small class="text-muted">{{ $item->storeCategory->category_name ?? '-' }}</small>
                                            </td>
                                            <td>{{ $item->grnEntryItem->art_no ?? '-' }}</td>
                                            <td>{{ $item->uom->uom_code ?? '-' }}</td>
                                            <td class="text-success fw-bold">+{{ floatval($item->qty_in) }}</td>
                                            <td>{{ $item->price > 0 ? '₹' . number_format($item->price, 2) : '-' }}</td>
                                            <td>{{ $item->price > 0 && $item->qty_in > 0 ? '₹' . number_format($item->price * $item->qty_in, 2) : '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-end">Total:</th>
                                            <th class="text-success fw-bold">+{{ floatval($totalQtyIn) }}</th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        @else
                        @if($firstItem)
                        <div class="col-md-4">
                            <label class="detail-title">Item:</label>
                            <div class="text-muted">
                                {{ $firstItem->finished_item_code ?? '-' }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">UOM:</label>
                            <div class="text-muted">{{ $firstItem->uom->uom_code ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="detail-title">Quantity In:</label>
                            <div class="text-muted text-success fw-bold">+{{ $totalQtyIn + 0 }}</div>
                        </div>
                        @endif
                        @endif

                        <div class="col-md-4">
                            <label class="detail-title">Remarks:</label>
                            <div class="text-muted">{{ $stockEntry->remarks ?? '-' }}</div>
                        </div>

                        @if($isRawMaterial)
                        <div class="col-md-4">
                            <label class="detail-title">Reference Document:</label>
                            <div class="text-muted">
                                @if($stockEntry->reference_document)
                                    @php
                                        $ext = pathinfo($stockEntry->reference_document, PATHINFO_EXTENSION);
                                        $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        $fileUrl = url('uploads/stock_entries/' . $stockEntry->reference_document);
                                    @endphp
                                    <div class="d-inline-flex align-items-center border rounded shadow-sm">
                                        @if($isImage)
                                            <img src="{{ $fileUrl }}" class="rounded cursor-pointer shadow-sm view-image border" data-image="{{ $fileUrl }}" width="50" height="50" style="object-fit: cover;" alt="Reference">
                                        @else
                                            <a href="{{ $fileUrl }}" target="_blank" class="text-decoration-none d-flex align-items-center px-2">
                                                @if(strtolower($ext) == 'pdf')
                                                    <i class="ri-file-pdf-fill text-danger fs-1"></i>
                                                @else
                                                    <i class="ri-file-text-fill text-primary fs-1"></i>
                                                @endif
                                                <div class="ms-2">
                                                    <div class="fw-bold text-dark small text-uppercase">{{ $ext }} Document</div>
                                                    <div class="text-muted small" style="font-size: 10px;">Click to view</div>
                                                </div>
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted small">No document attached</span>
                                @endif
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
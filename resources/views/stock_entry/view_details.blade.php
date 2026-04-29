@extends('layouts.common')
@section('title', 'View Stock Entry - ' . env('WEBSITE_NAME'))
@section('content')
    <div class="container-xxl section-padding">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-header-box">
                    <h4>View Stock Entry - {{ $stockEntry->stock_entry_no }}</h4>
                    <a href="{{ url('stock_entries') }}" class="btn btn-outline-secondary"><i class="ri ri-arrow-left-line back-arrow"></i>Back</a>
                </div>
                <div class="card detail-card">
                    <div class="card-body">
                        <div class="row g-4">
                            @php
    $itemId = request('item_id');
    $firstItem = null;
    if ($itemId) {
        $firstItem = $stockEntry->stockEntryItems->where('id', $itemId)->first();
    }
    if (!$firstItem) {
        $firstItem = $stockEntry->stockEntryItems->first();
    }

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
                                    {{ ($stockEntry->productionReceipt && $stockEntry->productionReceipt->jobCard) ? $stockEntry->productionReceipt->jobCard->job_card_no : '-' }}
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
                                                    @php
                                    $fabricType = $firstItem->fabricType ? $firstItem->fabricType->fabric_type : null;
                                    if (!$fabricType && $stockEntry->productionReceipt && $stockEntry->productionReceipt->jobCard) {
                                        $fabricType = $stockEntry->productionReceipt->jobCard->fabricType->fabric_type ?? null;
                                    }
                                    $fabricType = $fabricType ?? '-';
                                    $colorName = $firstItem->color ? $firstItem->color->color_name : '-';
                                    $qrString = ($firstItem->sku ?? '-') . " | " .
                                        ($firstItem->item->name ?? '-') . " | " .
                                        $fabricType . " | " .
                                        ($firstItem->size ?? '-') . " | " .
                                        $colorName . " | " .
                                        ($firstItem->sleeve_type ?? '-') . " | Qty: " .
                                        floatval($firstItem->qty_in);
                                        @endphp
                                        <div class="col-md-12 mt-4">
                                            <div class="card shadow-none border bg-light bg-opacity-25 rounded-3">
                                                <div class="card-body p-4">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-3 text-center border-end pe-md-5">
                                                            @if($firstItem->sku)
                                                                <div class="p-3 border rounded bg-white shadow-sm d-inline-block mb-2">
                                                                    {!! \QrCode::size(120)->generate($qrString) !!}
                                                                </div>
                                                                <div class="fw-bold text-primary fs-5">{{ $firstItem->sku }}</div>
                                                                <div class="text-muted small mb-3">Sequential SKU</div>
                                                                {{-- @if($firstItem->sku)
                                                                <a href="{{ url('labels/print/' . $firstItem->id) }}" target="_blank"
                                                                    class="btn btn-primary btn-sm px-3 py-1 shadow-sm d-inline-flex align-items-center gap-1">
                                                                    <i class="ri ri-printer-line"></i> Print Label
                                                                </a>
                                                                @endif --}}
                                                            @else
                                                                <div class="text-muted italic">No SKU Assigned</div>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-6 ps-md-5 py-3">
                                                            <h4 class="text-primary fw-bold mb-1">
                                                                @if($isFinishedGoods)
                                                                    {{ $firstItem->finished_item_code ?? '-' }}
                                                                @else
                                                                    {{ $firstItem->item->name ?? ($firstItem->rawMaterial->name ?? '-') }}
                                                                @endif
                                                            </h4>
                                                            @if(!$isFinishedGoods)
                                                                <div class="text-muted small mb-3">{{ $firstItem->item->code ?? ($firstItem->rawMaterial->code ?? '-') }}</div>
                                                            @endif
                                                            <div class="row g-3">
                                                                <div class="col-sm-6">
                                                                    <label class="detail-title d-block mb-1 text-uppercase ls-1 small fw-bold">Product SKU</label>
                                                                    <span class="text-dark fw-medium fs-6">{{ $firstItem->sku ?? '-' }}</span>
                                                                </div>
                                                                {{-- <div class="col-sm-6">
                                                                    <label class="detail-title d-block mb-1 text-uppercase ls-1 small fw-bold">Art No</label>
                                                                    <span class="text-dark fw-medium fs-6">{{ $firstItem->art_no ?? '-' }}</span>
                                                                </div> --}}
                                                                <div class="col-sm-6">
                                                                    <label class="detail-title d-block mb-1 text-uppercase ls-1 small fw-bold">Fabric / Sleeve</label>
                                                                    <span class="text-dark fw-medium fs-6">
                                                                        {{ $fabricType }} / 
                                                                        {{ $firstItem->sleeve_type ?? '-' }}
                                                                    </span>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <label class="detail-title d-block mb-1 text-uppercase ls-1 small fw-bold">Quantity</label>
                                                                    <span class="text-success fw-bold fs-5">+{{ floatval($firstItem->qty_in) }} <small class="text-muted fs-6 fw-normal">{{ $firstItem->uom->uom_code ?? 'PCS' }}</small></span>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <label class="detail-title d-block mb-1 text-uppercase ls-1 small fw-bold">Size / Color</label>
                                                                    <span class="text-dark fw-bold fs-5">{{ $firstItem->size ?? '-' }}</span>
                                                                    <span class="text-muted ms-1">({{ $firstItem->color->color_name ?? '-' }})</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- <div class="col-md-3 text-md-end pt-3 pt-md-0">
                                                            @if($firstItem->sku)
                                                                <a href="{{ url('labels/print/' . $firstItem->id) }}" target="_blank" class="btn btn-primary px-4 py-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
                                                                    <i class="ri-printer-line fs-5"></i> Print Label
                                                                </a>
                                                            @endif
                                                        </div> --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                @else
                                <div class="col-md-12 text-center py-5">
                                    <div class="text-muted">No finished goods items found in this entry.</div>
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
@extends('layouts.common')
@section('title', 'Credit Note Details - ' . env('WEBSITE_NAME'))
@section('content')

<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-primary mb-1">Credit Note Details</h3>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ url('credit_notes/download/' . $creditNote->id) }}" target="_blank" class="btn btn-primary d-flex align-items-center">
                        <i class="ri ri-download-line me-1"></i> Download
                    </a>
                    <a href="{{ url('credit_notes/print/' . $creditNote->id) }}" target="_blank" class="btn btn-primary d-flex align-items-center">
                        <i class="ri ri-printer-line me-1"></i> Print
                    </a>
                    <a href="{{ url('credit_notes') }}" class="btn btn-outline-secondary d-flex align-items-center">
                        <i class="ri ri-arrow-left-line me-1"></i> Back
                    </a>
                </div>
            </div>

            <!-- General Information -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-light py-3" style="border-radius: 12px 12px 0 0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">General Information</h5>
                        <span class="badge bg-white text-primary px-3 py-2">#{{ $creditNote->note_no }}</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Customer</div>
                            <div class="fw-bold text-dark">
                                {{ $creditNote->customer ? $creditNote->customer->name : 'N/A' }}
                                @if($creditNote->customer && $creditNote->customer->code)
                                    <span class="text-primary small">({{ $creditNote->customer->code }})</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Sales Invoices</div>
                            <div class="fw-bold text-dark">
                                @if(!empty($salesInvoices) && count($salesInvoices) > 0)
                                    {{ implode(', ', $salesInvoices->pluck('inv_no')->toArray()) }}
                                @else
                                    {{ $creditNote->salesInvoice ? $creditNote->salesInvoice->inv_no : '-' }}
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Issue Date</div>
                            <div class="fw-bold text-dark">{{ $creditNote->note_date->format('d M, Y') }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Reason</div>
                            <div class="fw-bold text-dark">{{ $creditNote->reason ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Zone</div>
                            <div class="fw-bold text-dark">{{ $creditNote->zone ? $creditNote->zone->zone_name : '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Sales Executive</div>
                            <div class="fw-bold text-dark">
                                {{ $creditNote->salesAgent ? $creditNote->salesAgent->name : '-' }}
                                @if($creditNote->salesAgent && $creditNote->salesAgent->code)
                                    <span class="text-primary small">({{ $creditNote->salesAgent->code }})</span>
                                @endif
                            </div>
                        </div>
                        @if($creditNote->reason_detail)
                        <div class="col-md-6">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Reason Detail</div>
                            <div class="fw-bold text-dark small" style="white-space: pre-line;">{{ $creditNote->reason_detail }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Item Details Table -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-light py-3" style="border-radius: 12px 12px 0 0; border-bottom: 1px solid #f0f0f0;">
                    <h5 class="mb-0 fw-bold text-dark">Item Details</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-muted text-uppercase small fw-bold" width="80">S.No</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold">Item Description</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold text-center">Quantity</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold text-end pe-4">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($creditNote->items->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="ri-information-line fs-3 d-block mb-2"></i>
                                            No items found for this credit note
                                        </td>
                                    </tr>
                                @else
                                    @foreach($creditNote->items as $index => $item)
                                        @php
                                            $invoiceItem = $item->salesInvoiceItem;

                                            // Item name
                                            $itemName = 'N/A';
                                            if ($invoiceItem) {
                                                if ($invoiceItem->stockEntryItem && $invoiceItem->stockEntryItem->finished_item_code) {
                                                    $itemName = $invoiceItem->stockEntryItem->finished_item_code;
                                                } elseif ($invoiceItem->item) {
                                                    $itemName = $invoiceItem->item->name;
                                                }
                                            }

                                            // Brand/Category
                                            $brandName = '';
                                            if ($invoiceItem && $invoiceItem->brandCategory) {
                                                $brandName = $invoiceItem->brandCategory->name;
                                            }

                                            // Item code
                                            $itemCode = $invoiceItem && $invoiceItem->sku ? $invoiceItem->sku : '';

                                            // Sleeve
                                            $sleeveShort = '';
                                            if ($invoiceItem && $invoiceItem->sleeve_type) {
                                                $sleeveShort = ' - ' . (strtolower($invoiceItem->sleeve_type) == 'full' ? 'F/S' : 'H/S');
                                            }

                                            // UOM
                                            $uomCode = $invoiceItem && $invoiceItem->uom ? $invoiceItem->uom->uom_code : 'PCS';

                                            // Color
                                            $colorName = $invoiceItem && $invoiceItem->color ? $invoiceItem->color->color_name : '';

                                            // Size
                                            $sizeName = '';
                                            if ($invoiceItem) {
                                                $sizeName = $invoiceItem->sizeRatio ? $invoiceItem->sizeRatio->size : $invoiceItem->size;
                                            }

                                            // Art No
                                            $artNo = $invoiceItem ? $invoiceItem->art_no : '';
                                        @endphp
                                        <tr>
                                            <td class="ps-4 fw-bold">{{ sprintf('%02d', $index + 1) }}</td>
                                            <td>
                                                <div class="fw-bold text-dark">
                                                    {{ $brandName }} {{ $itemName }}
                                                </div>
                                                @if($itemCode)
                                                    <small class="text-primary fw-medium">{{ $itemCode }}</small>
                                                @endif
                                                @if($colorName)
                                                    <small class="text-muted"> | Color: {{ $colorName }}</small>
                                                @endif
                                                @if($artNo && $artNo != '-')
                                                    <small class="text-muted"> | Art No: {{ $artNo }}</small>
                                                @endif
                                                @if($sizeName)
                                                    <small class="text-muted"> | Size: {{ $sizeName }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark px-3 py-2 fw-medium">
                                                    {{ number_format($item->quantity, 2) }} {{ $uomCode }}
                                                </span>
                                            </td>
                                            <td class="text-end fw-bold text-dark pe-4">₹{{ number_format($item->amount, 2) }}</td>
                                        </tr>
                                        @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="row g-4 justify-content-between">
                <div class="col-lg-6" style="max-height:200px;">
                    @if($creditNote->remarks || $creditNote->reference_doc)
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background-color: #fcfcfc;">
                        <div class="card-body p-4">
                            @if($creditNote->remarks)
                                <h6 class="text-uppercase small fw-bold text-muted mb-3 border-bottom pb-2">Internal Remarks</h6>
                                <p class="mb-3 text-dark small" style="white-space: pre-line; line-height: 1.6;">{{ $creditNote->remarks }}</p>
                            @endif

                            @if($creditNote->reference_doc)
                                <h6 class="text-uppercase small fw-bold text-muted mb-3 {{ $creditNote->remarks ? 'mt-4 border-top pt-3' : '' }} border-bottom pb-2">Reference Document</h6>
                                @php
                                    $attachment = $creditNote->reference_doc;
                                    $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                    $url = url('uploads/credit_notes/' . $attachment);
                                @endphp

                                <div class="attachment-thumb border rounded p-1 bg-white shadow-sm position-relative" style="width: 100px; height: 100px;" title="{{ $attachment }}">
                                    @if($isImage)
                                        <img src="{{ $url }}" class="w-100 h-100 object-fit-cover rounded cursor-pointer view-image" data-image="{{ $url }}" alt="Reference">
                                    @else
                                        <a href="{{ $url }}" target="_blank" class="w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-light rounded text-decoration-none shadow-none text-primary">
                                            <i class="ri ri-file-text-line fs-2"></i>
                                            <span class="badge bg-primary text-white mt-1" style="font-size: 10px;">{{ strtoupper($extension) }}</span>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-header bg-light py-3" style="border-radius: 12px 12px 0 0; border-bottom: 1px solid #f0f0f0;">
                            <h5 class="mb-0 fw-bold text-dark">Billing Summary</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted fw-medium">Total Quantity</span>
                                <span class="fw-bold">{{ number_format($creditNote->items->sum('quantity'), 2) }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted fw-medium">Sub Total</span>
                                <span class="fw-bold">₹{{ number_format($creditNote->sub_total, 2) }}</span>
                            </div>

                            @if($creditNote->discount > 0)
                            <div class="d-flex justify-content-between mb-3 text-muted small">
                                <span>Discount ({{ number_format($creditNote->discount_percent, 2) }}%)</span>
                                <span>-₹{{ number_format($creditNote->discount, 2) }}</span>
                            </div>
                            @endif

                            @php
                                $preGstCharges = $creditNote->charges->where('tax_type', 'Pre-GST');
                                $postGstCharges = $creditNote->charges->where('tax_type', 'Post-GST');
                            @endphp

                            @if($preGstCharges->count() > 0)
                                @foreach($preGstCharges as $charge)
                                <div class="d-flex justify-content-between mb-3 text-muted small">
                                    <span>{{ $charge->charge_name }} (Pre-GST)</span>
                                    <span>+₹{{ number_format($charge->charge_amount, 2) }}</span>
                                </div>
                                @endforeach
                            @endif

                            @if($creditNote->other_state)
                                <div class="d-flex justify-content-between mb-3 text-muted small">
                                    <span>IGST ({{ $creditNote->igst_percent }}%)</span>
                                    <span>₹{{ number_format($creditNote->igst, 2) }}</span>
                                </div>
                            @else
                                <div class="d-flex justify-content-between mb-3 text-muted small">
                                    <span>CGST ({{ $creditNote->cgst_percent }}%)</span>
                                    <span>₹{{ number_format($creditNote->cgst, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 text-muted small">
                                    <span>SGST ({{ $creditNote->sgst_percent }}%)</span>
                                    <span>₹{{ number_format($creditNote->sgst, 2) }}</span>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between mb-3 pt-3 border-top">
                                <span class="text-dark fw-bold">Total Tax</span>
                                <span class="fw-bold">₹{{ number_format($creditNote->tax_amount, 2) }}</span>
                            </div>

                            @if($postGstCharges->count() > 0)
                                @foreach($postGstCharges as $charge)
                                <div class="d-flex justify-content-between mb-3 text-muted small">
                                    <span>{{ $charge->charge_name }} (Post-GST)</span>
                                    <span>+₹{{ number_format($charge->charge_amount, 2) }}</span>
                                </div>
                                @endforeach
                            @endif

                            @if($creditNote->round_off > 0)
                            <div class="d-flex justify-content-between mb-3 text-muted italic">
                                <span>Round Off ({{ $creditNote->round_off_type }})</span>
                                <span>{{ $creditNote->round_off_type == 'Less' ? '-' : '+' }}₹{{ number_format($creditNote->round_off, 2) }}</span>
                            </div>
                            @endif

                            <div class="bg-primary-soft p-3 rounded-3 mt-4 border-start border-primary border-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-primary small uppercase">Grand Total</span>
                                    <span class="fs-5 fw-bold text-primary">₹{{ number_format($creditNote->grand_total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

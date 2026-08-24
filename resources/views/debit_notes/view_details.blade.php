@extends('layouts.common')
@section('title', 'View Debit Note - ' . env('WEBSITE_NAME'))
@section('content')

<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-primary mb-1">Debit Note Details</h3>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ url('debit_notes/download/' . $debitNote->id) }}" target="_blank" class="btn btn-primary d-flex align-items-center">
                        <i class="ri ri-download-line me-1"></i> Download
                    </a>
                    <a href="{{ url('debit_notes/print/' . $debitNote->id) }}" target="_blank" class="btn btn-primary d-flex align-items-center">
                        <i class="ri ri-printer-line me-1"></i> Print
                    </a>
                    <a href="{{ url('debit_notes') }}" class="btn btn-outline-secondary d-flex align-items-center">
                        <i class="ri ri-arrow-left-line me-1"></i> Back
                    </a>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-light py-3" style="border-radius: 12px 12px 0 0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">General Information</h5>
                        <span class="badge bg-white text-primary px-3 py-2">#{{ $debitNote->debit_note_no }}</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Debit Note No</div>
                            <div class="fw-bold text-dark">{{ $debitNote->debit_note_no ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Debit Note Date</div>
                            <div class="fw-bold text-dark">{{ $debitNote->debit_note_date->format('d M, Y') }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Purchase Invoice</div>
                            <div class="fw-bold text-dark">{{ $debitNote->purchaseInvoice->invoice_no ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Supplier</div>
                            <div class="fw-bold text-dark">
                                {{ $debitNote->supplier->name ?? '-' }}
                                @if($debitNote->supplier && $debitNote->supplier->supplier_code)
                                    <span class="text-primary small">({{ $debitNote->supplier->supplier_code }})</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Reason</div>
                            <div class="fw-bold text-dark">{{ $debitNote->reason ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Status</div>
                            <div>
                                @php
                                    $statusColors = [
                                        'Draft' => 'bg-secondary',
                                        'Approved' => 'bg-success',
                                        'Cancelled' => 'bg-danger',
                                    ];
                                @endphp
                                <span class="badge {{ $statusColors[$debitNote->status] ?? 'bg-secondary' }} px-3 py-2 text-uppercase small">{{ $debitNote->status }}</span>
                            </div>
                        </div>
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
                                    <th class="py-3 text-muted text-uppercase small fw-bold">Raw Material</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold">Art No</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold">Supplier Design Name</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold text-center">Quantity / UOM</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold text-end">Rate</th>
                                    <th class="py-3 text-muted text-uppercase small fw-bold text-end pe-4">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($debitNote->items->isEmpty())
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="ri ri-information-line fs-3 d-block mb-2"></i>
                                            No items found for this debit note
                                        </td>
                                    </tr>
                                @else
                                    @foreach($debitNote->items as $index => $item)
                                    @php
                                         $dbInvItem = \App\Models\PurchaseInvoiceItem::with(['purchaseOrderItem', 'purchaseInvoice'])->find($item->purchase_invoice_item_id);
                                         $poItem = $dbInvItem->purchaseOrderItem ?? ($dbInvItem?->purchaseInvoice?->purchase_order_id ? \App\Models\PurchaseOrderItem::where('purchase_order_id', $dbInvItem->purchaseInvoice->purchase_order_id)->where('raw_material_id', $dbInvItem->raw_material_id)->first() : null);
                                         $supplierDesignName = $poItem->supplier_design_name ?? '-';
                                         $grnItem = \App\Models\GrnEntryItem::where('purchase_invoice_item_id', $item->purchase_invoice_item_id)->first();
                                         $artNo = $grnItem->art_no ?? '-';
                                    @endphp
                                    <tr>
                                        <td class="ps-4 fw-bold">{{ sprintf('%02d', $index + 1) }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $item->rawMaterial->name ?? '-' }}</div>
                                            @if($item->rawMaterial && $item->rawMaterial->material_code)
                                                <small class="text-primary fw-medium">{{ $item->rawMaterial->material_code }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $artNo }}
                                        </td>
                                        <td>
                                            {{ $supplierDesignName }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark px-3 py-2 fw-medium">
                                                {{ number_format($item->quantity, 2) }} {{ $item->uom->uom_code ?? '' }}
                                            </span>
                                        </td>
                                        <td class="text-end">₹{{ number_format($item->rate, 2) }}</td>
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
            <div class="row g-4">
                <div class="col-lg-7 text-break">
                    <!-- Pre-GST Charges & Info -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-header bg-light py-3" style="border-radius: 12px 12px 0 0; border-bottom: 1px solid #f0f0f0;">
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing: 0.5px;">Pre-GST Charges & Info</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                @php $preGstChargesList = $debitNote->charges ? $debitNote->charges->where('tax_type', 'Pre-GST') : collect(); @endphp
                                @if($preGstChargesList->count() > 0)
                                    @foreach($preGstChargesList as $charge)
                                        <div class="col-12 d-flex justify-content-between align-items-center border-bottom pb-3 mb-1">
                                            <span class="text-muted small fw-bold text-uppercase">{{ $charge->charge_name }}</span>
                                            <span class="fw-bold text-dark fs-6">₹{{ number_format($charge->charge_amount, 2) }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12 text-muted small fst-italic py-2">No pre-GST charges applied.</div>
                                @endif

                                @if(($debitNote->discount_percent ?? 0) > 0 || ($debitNote->discount_amount ?? 0) > 0)
                                    @php
                                        $preGstSum = $preGstChargesList->sum('charge_amount');
                                        $discAmt = $debitNote->discount_amount ?? (($debitNote->sub_total + $preGstSum) * ($debitNote->discount_percent ?? 0) / 100);
                                    @endphp
                                    <div class="col-12 mt-3">
                                        <div class="d-flex justify-content-between align-items-center p-3 rounded-3" style="background-color: #eceef1; border: 1px solid #dee2e6;">
                                            <span class="fw-bold text-dark fs-6">Discount ({{ number_format($debitNote->discount_percent ?? 0, 2) }}%)</span>
                                            <span class="fw-bold fs-6">-₹{{ number_format($discAmt, 2) }}</span>
                                        </div>
                                    </div>
                                @endif

                                @if($debitNote->remarks)
                                    <div class="col-12 mt-2">
                                        <div class="text-muted text-uppercase small fw-bold mb-2">Internal Remarks</div>
                                        <p class="small text-dark border p-3 rounded-3 bg-white shadow-sm mb-0" style="white-space: pre-line; border-color: #f0f0f0 !important;">
                                            {{ $debitNote->remarks }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Post-GST Charges & Attachments -->
                    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-header bg-light py-3" style="border-radius: 12px 12px 0 0; border-bottom: 1px solid #f0f0f0;">
                            <h6 class="mb-0 fw-bold text-dark text-uppercase small" style="letter-spacing: 0.5px;">Post-GST Charges & Attachments</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                @php $postGstChargesList = $debitNote->charges ? $debitNote->charges->where('tax_type', 'Post-GST') : collect(); @endphp
                                @if($postGstChargesList->count() > 0)
                                    @foreach($postGstChargesList as $charge)
                                        <div class="col-12 d-flex justify-content-between align-items-center border-bottom pb-3 mb-1">
                                            <span class="text-muted small fw-bold text-uppercase">{{ $charge->charge_name }}</span>
                                            <span class="fw-bold text-dark fs-6">₹{{ number_format($charge->charge_amount, 2) }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12 text-muted small fst-italic py-2 border-bottom pb-3 mb-1">No post-GST charges applied.</div>
                                @endif
                            </div>

                            <div class="row g-4 mt-1">
                                <div class="col-md-12">
                                    <div class="text-muted text-uppercase small fw-bold mb-2">Reference Document</div>
                                    @if($debitNote->reference_document)
                                        @php
                                            $attachment = $debitNote->reference_document;
                                            $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                            $url = url('uploads/debit_notes/' . $attachment);
                                        @endphp
                                        <div class="mt-2 text-start">
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
                                        </div>
                                    @else
                                        <span class="text-muted small fst-italic">No attachment</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-header bg-light py-3" style="border-radius: 12px 12px 0 0; border-bottom: 1px solid #f0f0f0;">
                            <h5 class="mb-0 fw-bold text-dark">Billing Summary</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted fw-medium">Total Quantity</span>
                                <span class="fw-bold">{{ number_format($debitNote->items->sum('quantity'), 2) }}</span>
                            </div>
                            
                            @php
                                $preGstCharges = $debitNote->charges ? $debitNote->charges->where('tax_type', 'Pre-GST')->sum('charge_amount') : 0;
                                $postGstCharges = $debitNote->charges ? $debitNote->charges->where('tax_type', 'Post-GST')->sum('charge_amount') : 0;
                                $discountAmt = $debitNote->discount_amount ?? (($debitNote->sub_total + $preGstCharges) * ($debitNote->discount_percent ?? 0) / 100);
                                $taxableAmt = $debitNote->taxable_amount ?? (($debitNote->sub_total + $preGstCharges) - $discountAmt);
                            @endphp

                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted fw-medium">Sub Total</span>
                                <span class="fw-bold">₹{{ number_format($debitNote->sub_total, 2) }}</span>
                            </div>

                            @if($preGstCharges > 0)
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted fw-medium">Pre-GST Charges</span>
                                <span class="fw-bold">₹{{ number_format($preGstCharges, 2) }}</span>
                            </div>
                            @endif

                            @if(($debitNote->discount_percent ?? 0) > 0)
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted fw-medium text-danger">Discount ({{ number_format($debitNote->discount_percent, 2) }}%)</span>
                                <span class="fw-bold text-danger">-₹{{ number_format($discountAmt, 2) }}</span>
                            </div>
                            @endif

                            @if($preGstCharges > 0 || ($debitNote->discount_percent ?? 0) > 0)
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted fw-medium">Taxable Total</span>
                                <span class="fw-bold">₹{{ number_format($taxableAmt, 2) }}</span>
                            </div>
                            @endif

                            @if($debitNote->other_state == 'Y')
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted fw-medium">IGST ({{ $debitNote->igst_percent }}%)</span>
                                    <span class="fw-bold">₹{{ number_format($taxableAmt * ($debitNote->igst_percent / 100), 2) }}</span>
                                </div>
                            @else
                                <div class="d-flex justify-content-between mb-3 text-muted small">
                                    <span>CGST ({{ $debitNote->cgst_percent }}%)</span>
                                    <span>₹{{ number_format($taxableAmt * ($debitNote->cgst_percent / 100), 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 text-muted small">
                                    <span>SGST ({{ $debitNote->sgst_percent }}%)</span>
                                    <span>₹{{ number_format($taxableAmt * ($debitNote->sgst_percent / 100), 2) }}</span>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between mb-3 pt-3 border-top">
                                <span class="text-dark fw-bold">Total Tax</span>
                                <span class="fw-bold">₹{{ number_format($debitNote->tax_amount, 2) }}</span>
                            </div>

                            @if($postGstCharges > 0)
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted fw-medium">Post-GST Charges</span>
                                <span class="fw-bold">₹{{ number_format($postGstCharges, 2) }}</span>
                            </div>
                            @endif

                            @if($debitNote->round_off != 0)
                            <div class="d-flex justify-content-between mb-3 text-muted italic">
                                <span>Round Off ({{ $debitNote->round_off_type }})</span>
                                <span>{{ $debitNote->round_off_type == 'Less' ? '-' : '+' }}₹{{ number_format($debitNote->round_off, 2) }}</span>
                            </div>
                            @endif

                            <div class="bg-primary-soft p-3 rounded-3 mt-4 border-start border-primary border-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-primary small uppercase">Grand Total</span>
                                    <span class="fs-5 fw-bold text-primary">₹{{ number_format($debitNote->grand_total, 2) }}</span>
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

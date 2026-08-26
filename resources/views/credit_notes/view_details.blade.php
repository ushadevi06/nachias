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
                    @php
                        $multiSalesInvoices = !empty($creditNote->sales_invoice_ids) && count($creditNote->sales_invoice_ids) > 1;
                    @endphp
                    @if($creditNote->einvoice_status == 'generated')
                        <button type="button" class="btn btn-danger d-flex align-items-center" id="einvoice-cancel">
                            <i class="ri ri-close-circle-line me-1"></i> Cancel E-Invoice
                        </button>
                    @else
                        @if($multiSalesInvoices)
                            <button type="button" class="btn btn-info d-flex align-items-center text-white" disabled title="E-Invoice generation is allowed only for Credit Notes linked to a single Sales Invoice. Multiple Sales Invoices are not supported.">
                                <i class="ri ri-receipt-line me-1"></i> Generate E-Invoice
                            </button>
                        @else
                            <button type="button" class="btn btn-info d-flex align-items-center text-white" id="einvoice-generate">
                                <i class="ri ri-receipt-line me-1"></i> Generate E-Invoice
                            </button>
                        @endif
                    @endif
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
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Credit Note No</div>
                            <div class="fw-bold text-primary">{{ $creditNote->note_no }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Credit Note Date</div>
                            <div class="fw-bold text-dark">{{ $creditNote->note_date->format('d M, Y') }}</div>
                        </div>
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
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Reason</div>
                            <div class="fw-bold text-dark">{{ $creditNote->reason ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Fault</div>
                            <div class="fw-bold text-dark">{{ $creditNote->fault ?? '-' }}</div>
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

            <!-- E-Invoice Segment -->
            @if($creditNote->einvoice_status == 'generated' || $creditNote->irn)
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-light py-3" style="border-radius: 12px 12px 0 0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="ri ri-receipt-line me-2"></i> E-Invoice Details</h5>
                        <span class="badge {{ $creditNote->einvoice_status == 'generated' ? 'bg-success' : 'bg-danger' }} px-3 py-2">
                            {{ ucfirst($creditNote->einvoice_status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">IRN Number</div>
                            <div class="fw-bold text-dark text-break" style="font-family: monospace;">{{ $creditNote->irn ?: '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Ack No</div>
                            <div class="fw-bold text-dark">{{ $creditNote->ack_no ?: '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-1 text-muted text-uppercase small fw-bold">Ack Date</div>
                            <div class="fw-bold text-dark">
                                {{ $creditNote->ack_date ? \Carbon\Carbon::parse($creditNote->ack_date)->format('d M, Y h:i A') : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Item Details Table -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-light py-3" style="border-radius: 12px 12px 0 0; border-bottom: 1px solid #f0f0f0;">
                    <h5 class="mb-0 fw-bold text-dark">Item Details</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive p-1">
                        <table class="table table-bordered table-hover align-middle text-nowrap mb-0" id="itemTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;" class="text-center">S.No</th>
                                    <th style="width: 160px;">INVOICE NO</th>
                                    <th style="width: 220px;">ITEM NAME</th>
                                    <th style="width: 100px;">COLOR</th>
                                    <th style="width: 130px;">ART NO</th>
                                    <th style="width: 70px;">UOM</th>
                                    <th style="width: 90px;" class="text-center">SIZE</th>
                                    <th style="width: 110px;" class="text-end">INV QTY</th>
                                    <th style="width: 110px;" class="text-end">RET QTY</th>
                                    <th style="width: 110px;" class="text-end">BAL QTY</th>
                                    <th style="width: 110px;" class="text-center">RETURN QTY</th>
                                    <th style="width: 110px;" class="text-end">MRP</th>
                                    <th style="width: 110px;" class="text-end">PRICE</th>
                                    <th style="width: 150px;" class="text-end">AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($creditNote->items->isEmpty())
                                    <tr>
                                        <td colspan="14" class="text-center py-5 text-muted">
                                            <i class="ri-information-line fs-3 d-block mb-2"></i>
                                            No items found for this credit note
                                        </td>
                                    </tr>
                                @else
                                    @foreach($creditNote->items as $index => $item)
                                        @php
                                            $invoiceItem = $item->salesInvoiceItem;
                                            $invoiceNo = $invoiceItem && $invoiceItem->salesInvoice ? $invoiceItem->salesInvoice->inv_no : '-';

                                            $itemName = '-';
                                            if ($invoiceItem) {
                                                if ($invoiceItem->stockEntryItem && $invoiceItem->stockEntryItem->finished_item_code) {
                                                    $itemName = $invoiceItem->stockEntryItem->finished_item_code;
                                                } elseif ($invoiceItem->item) {
                                                    $itemName = $invoiceItem->item->name;
                                                }
                                            }

                                            $productBarcode = $invoiceItem ? $invoiceItem->sku : '';
                                            $colorName = $invoiceItem ? ($invoiceItem->api_color ?: ($invoiceItem->color ? $invoiceItem->color->color_name : '-')) : '-';

                                            $displayArtNo = '-';
                                            if ($invoiceItem) {
                                                $displayArtNo = $invoiceItem->stockEntryItem ? ($invoiceItem->stockEntryItem->art_no ?: $invoiceItem->art_no) : ($invoiceItem->art_no ?: '-');
                                            }

                                            $uomCode = 'PCS';
                                            if ($invoiceItem) {
                                                if ($invoiceItem->stockEntryItem) {
                                                    $soItem = \App\Models\SalesOrderItem::where('stock_entry_item_id', $invoiceItem->stock_entry_item_id)->first();
                                                    if ($soItem && $soItem->uom_id) {
                                                        $uomCode = $soItem->uom_id;
                                                    }
                                                } elseif ($invoiceItem->uom_id) {
                                                    $uomCode = $invoiceItem->uom_id;
                                                } elseif ($invoiceItem->uom) {
                                                    $uomCode = $invoiceItem->uom->uom_code;
                                                }
                                            }

                                            $sizeName = '-';
                                            if ($invoiceItem) {
                                                $sizeName = $invoiceItem->sizeRatio ? $invoiceItem->sizeRatio->size : ($invoiceItem->size ?: '-');
                                            }

                                            $invQty = $invoiceItem ? $invoiceItem->quantity : 0;

                                            $alreadyReturned = 0;
                                            if ($invoiceItem) {
                                                $alreadyReturned = \DB::table('credit_note_items')
                                                    ->join('credit_notes', 'credit_notes.id', '=', 'credit_note_items.credit_note_id')
                                                    ->where('credit_note_items.sales_invoice_item_id', $invoiceItem->id)
                                                    ->whereNull('credit_notes.deleted_at')
                                                    ->whereNull('credit_note_items.deleted_at')
                                                    ->whereIn('credit_notes.status', ['Draft', 'Approved'])
                                                    ->where('credit_notes.id', '!=', $creditNote->id)
                                                    ->sum('credit_note_items.quantity');
                                            }

                                            $balQty = max(0, $invQty - $alreadyReturned);
                                            $mrp = $item->mrp ?? ($invoiceItem->mrp ?? 0);
                                            $rate = $item->rate ?? ($invoiceItem->rate ?? 0);
                                            $amount = $item->amount ?? ($item->quantity * $rate);
                                        @endphp
                                        <tr>
                                            <td class="text-center fw-semibold">{{ $index + 1 }}</td>
                                            <td>
                                                <span class="fw-semibold text-primary">{{ $invoiceNo }}</span>
                                            </td>
                                            <td>
                                                <span class="d-block fw-bold" style="font-size: 13px;">{{ $itemName }}</span>
                                                @if($productBarcode)
                                                    <small class="text-muted"><i class="ri-barcode-line"></i> {{ $productBarcode }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $colorName }}</td>
                                            <td>{{ $displayArtNo }}</td>
                                            <td>{{ $uomCode }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark px-2 py-1 border">{{ $sizeName }}</span>
                                            </td>
                                            <td class="text-end fw-semibold">{{ number_format($invQty, 2) }}</td>
                                            <td class="text-end text-warning fw-semibold">{{ number_format($alreadyReturned, 2) }}</td>
                                            <td class="text-end text-success fw-bold">{{ number_format($balQty, 2) }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-primary-subtle text-primary px-3 py-2 fw-bold fs-6">
                                                    {{ number_format($item->quantity, 2) }}
                                                </span>
                                            </td>
                                            <td class="text-end">₹{{ number_format($mrp, 2) }}</td>
                                            <td class="text-end">₹{{ number_format($rate, 2) }}</td>
                                            <td class="text-end fw-bold text-dark">₹{{ number_format($amount, 2) }}</td>
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
                <div class="col-lg-6">
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
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        var creditNoteId = "{{ $creditNote->id }}";
        
        $('#einvoice-generate').on('click', function() {
            var btn = $(this);
            Swal.fire({
                title: 'Generate E-Invoice?',
                text: "Do you want to generate an E-Invoice for this Credit Note?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, generate it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i> Generating...');
                    
                    Swal.fire({
                        title: 'Generating...',
                        text: 'Please wait while we generate the E-Invoice.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: "{{ url('credit_notes/generate-einvoice') }}/" + creditNoteId,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Success', response.message, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error');
                                btn.prop('disabled', false).html('<i class="ri ri-receipt-line"></i> Generate E-Invoice');
                            }
                        },
                        error: function(xhr) {
                            var errMsg = 'An error occurred';
                            if(xhr.responseJSON && xhr.responseJSON.message) {
                                errMsg = xhr.responseJSON.message;
                            }
                            Swal.fire('Error', errMsg, 'error');
                            btn.prop('disabled', false).html('<i class="ri ri-receipt-line"></i> Generate E-Invoice');
                        }
                    });
                }
            });
        });

        $('#einvoice-cancel').on('click', function() {
            var btn = $(this);
            Swal.fire({
                title: 'Cancel E-Invoice',
                html: `
                    <div class="text-start">
                        <p class="text-danger fw-bold mb-2">Warning: This action cannot be undone.</p>
                        <p class="mb-2">Are you sure you want to cancel the E-Invoice?</p>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i> Cancelling...');
                    
                    Swal.fire({
                        title: 'Cancelling...',
                        text: 'Please wait while we cancel the E-Invoice.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: "{{ url('credit_notes/cancel-einvoice') }}/" + creditNoteId,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Success', response.message, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error', response.message, 'error');
                                btn.prop('disabled', false).html('<i class="ri ri-close-circle-line"></i> Cancel E-Invoice');
                            }
                        },
                        error: function(xhr) {
                            var errMsg = 'An error occurred';
                            if(xhr.responseJSON && xhr.responseJSON.message) {
                                errMsg = xhr.responseJSON.message;
                            }
                            Swal.fire('Error', errMsg, 'error');
                            btn.prop('disabled', false).html('<i class="ri ri-close-circle-line"></i> Cancel E-Invoice');
                        }
                    });
                }
            });
        });
    });
</script>
@endsection

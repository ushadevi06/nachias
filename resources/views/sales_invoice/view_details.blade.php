@extends('layouts.common')
@section('title', 'View Sale Invoice - ' . env('WEBSITE_NAME'))
@section('content')
    <div class="container-xxl section-padding">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-header-box d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
                    <h4 class="mb-0">View Sales Invoice</h4>
                    <div class="d-flex flex-wrap gap-2">
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="pdfOptionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ri ri-file-pdf-line back-arrow"></i>PDF Options
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="pdfOptionsDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ url('sales_invoices/download/' . $invoice->id) }}" target="_blank">
                                        <i class="ri ri-download-line me-2"></i>Download PDF
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ url('sales_invoices/print/' . $invoice->id) }}" target="_blank">
                                        <i class="ri ri-printer-line me-2"></i>Print PDF
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="deliveryOrderDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ri ri-truck-line back-arrow"></i>Delivery Order
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="deliveryOrderDropdown">
                                <li><a class="dropdown-item" href="{{ url('sales_invoices/delivery-order/' . $invoice->id) }}" target="_blank">Delivery Order</a></li>
                                <li><a class="dropdown-item" href="{{ url('sales_invoices/open-delivery-order/' . $invoice->id) }}" target="_blank">Open Delivery Order</a></li>
                                <li><a class="dropdown-item" href="{{ url('sales_invoices/scan-items/' . $invoice->id) }}">Scan Items</a></li>
                            </ul>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-info dropdown-toggle text-white" type="button" id="stickerPrintDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: #00bcd4; border-color: #00bcd4;">
                                <i class="ri ri-printer-line back-arrow"></i>Sticker Print
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="stickerPrintDropdown">
                                <li><a class="dropdown-item" href="{{ url('sales_invoices/transport-sticker/' . $invoice->id) }}" target="_blank">Transport Sticker</a></li>
                                <li><a class="dropdown-item" href="{{ url('sales_invoices/courier-sticker/' . $invoice->id) }}" target="_blank">Courier Sticker</a></li>
                            </ul>
                        </div>
                        @if($invoice->irn || $invoice->eway_bill_no)
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#einvoiceDetailsModal">
                            <i class="ri ri-receipt-line"></i> E-INVOICE Details
                        </button>
                        @endif
                        @if($invoice->einvoice_status === 'cancelled')
                        <button type="button" class="btn btn-warning text-white" disabled>
                            <i class="ri ri-close-circle-line"></i> E-Invoice Cancelled
                        </button>
                        @php
                            $recreatedInvoice = \App\Models\SalesInvoice::where('customer_id', $invoice->customer_id)
                                ->where('so_ids', $invoice->so_ids)
                                ->where('id', '!=', $invoice->id)
                                ->where(function($q) {
                                    $q->whereNull('einvoice_status')->orWhere('einvoice_status', '!=', 'cancelled');
                                })
                                ->orderBy('id', 'desc')
                                ->first();
                        @endphp
                        @if($recreatedInvoice)
                        <a href="{{ url('sales_invoices/view/' . $recreatedInvoice->id) }}" class="btn btn-outline-success" title="View Recreated Invoice">
                            <i class="ri ri-check-double-line"></i> Recreated as {{ $recreatedInvoice->inv_no }}
                        </a>
                        @else
                        <a href="{{ url('sales_invoices/recreate/' . $invoice->id) }}" class="btn btn-outline-primary" title="Recreate / Copy to New Invoice">
                            <i class="ri ri-file-copy-line"></i> Recreate Invoice
                        </a>
                        @endif
                        @elseif($invoice->irn)
                            @php
                                $ackDateTime = $invoice->ack_date ? \Carbon\Carbon::parse($invoice->ack_date) : null;
                                $isExpired = $ackDateTime ? $ackDateTime->diffInHours(now()) >= 24 : false;
                            @endphp
                            @if($isExpired)
                            <button type="button" class="btn btn-secondary" id="einvoice-expired">
                                <i class="ri ri-close-circle-line"></i> Cancel E-Invoice
                            </button>
                            @else
                            <button type="button" class="btn btn-danger" id="einvoice-cancel">
                                <i class="ri ri-close-circle-line"></i> Cancel E-Invoice
                            </button>
                            @endif
                        @else
                        @if($invoice->customer && $invoice->customer->gst_no)
                        <button type="button" class="btn btn-info" id="einvoice-generate">
                            <i class="ri ri-receipt-line"></i> Generate E-Invoice
                        </button>
                        @endif
                        @endif
                        <a href="{{ url('sales_invoices') }}" class="btn btn-secondary">
                            <i class="ri ri-arrow-left-line back-arrow"></i>Back
                        </a>
                    </div>
                </div>

                <div class="card detail-card">
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <h6>Order Details:</h6>
                            </div>

                            <div class="col-md-4">
                                <label class="detail-title text-muted">Invoice No:</label>
                                <div class="fw-bold text-primary fs-5">{{ $invoice->inv_no }}</div>
                            </div>

                            <div class="col-md-4">
                                <label class="detail-title text-muted">Invoice Date:</label>
                                <div class="text-dark fw-semibold">{{ $invoice->inv_date->format('d-M-Y') }}</div>
                            </div>

                            <div class="col-md-4">
                                <label class="detail-title text-muted">Customer / Buyer Name:</label>
                                <div class="fw-bold text-primary fs-6">{{ $invoice->customer ? $invoice->customer->name : '-' }}
                                    <span class="text-muted fw-normal">({{ $invoice->customer ? $invoice->customer->code : '' }})</span></div>
                            </div>

                            <div class="col-md-4">
                                <label class="detail-title">Linked SO No:</label>
                                <div class="text-muted">{{ $invoice->salesOrder ? $invoice->salesOrder->so_no : '-' }}</div>
                            </div>

                            <div class="col-md-4">
                                <label class="detail-title">IRN No:</label>
                                <div class="text-muted" style="word-break: break-all;">{{ $invoice->irn ?? '-' }}</div>
                            </div>

                            <div class="col-md-4">
                                <label class="detail-title">E-Way Bill No:</label>
                                <div class="text-muted">{{ $invoice->eway_bill_no ?? '-' }}</div>
                            </div>

                            <div class="col-md-4">
                                <label class="detail-title">Delivery Address:</label>
                                <div class="text-muted">{!! nl2br(e(\App\Models\SalesInvoice::cleanAddress($invoice->delivery_address ?? '-'))) !!}</div>
                            </div>

                            <div class="col-md-4">
                                <label class="detail-title">Remarks:</label>
                                <div class="text-muted">{{ $invoice->remarks ?? '-' }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="detail-title">No of Box:</label>
                                <div class="text-muted">{{ $invoice->no_of_box ?: 1 }}</div>
                            </div>

                            <div class="col-md-4">
                                <label class="detail-title">HSN Code:</label>
                                <div class="text-muted">{{ $invoice->hsn_sac ?? '-' }}</div>
                            </div>

                            <div class="col-lg-12">
                                <hr>
                            </div>

                            <div class="col-lg-12">
                                <h6>Item Details:</h6>
                            </div>
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover text-nowrap align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>S.No</th>
                                                <th>Stock Item</th>
                                                <th>Color</th>
                                                <th>Art No</th>
                                                <th>UOM</th>
                                                <th>Size</th>
                                                <th class="text-end">Quantity</th>
                                                <th class="text-end">MRP</th>
                                                <th class="text-end">Price</th>
                                                <th class="text-end">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($invoice->items as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                     <td>
                                                        @php
                                                            $brandName = '';
                                                            $itemName = '';
                                                            $sleeveType = $item->sleeve_type;

                                                            if ($item->item) {
                                                                if ($item->item->brand) {
                                                                    $brandName = $item->item->brand->brand_name;
                                                                } elseif ($item->brandCategory) {
                                                                    $brandName = $item->brandCategory->name;
                                                                }

                                                                if ($item->item->style) {
                                                                    $itemName = $item->item->style->style_name;
                                                                } else {
                                                                    $itemName = $item->item->name;
                                                                }
                                                            } elseif ($item->stockEntryItem) {
                                                                if ($item->stockEntryItem->item) {
                                                                    $seItem = $item->stockEntryItem->item;
                                                                    if ($seItem->brand) {
                                                                        $brandName = $seItem->brand->brand_name;
                                                                    } elseif ($seItem->brandCategory) {
                                                                        $brandName = $seItem->brandCategory->name;
                                                                    }

                                                                    if ($seItem->style) {
                                                                        $itemName = $seItem->style->style_name;
                                                                    } else {
                                                                        $itemName = $seItem->name;
                                                                    }
                                                                } else {
                                                                    $brandName = $item->stockEntryItem->finished_item_code;
                                                                }

                                                                if (empty($sleeveType)) {
                                                                    $sleeveType = $item->stockEntryItem->sleeve_type;
                                                                }
                                                            }

                                                            if (empty($brandName) && $item->brandCategory) {
                                                                $brandName = $item->brandCategory->name;
                                                            }
                                                            if (empty($itemName) && !empty($item->art_no)) {
                                                                $itemName = $item->art_no;
                                                            }
                                                        @endphp
                                                        <div class="fw-bold">{{ $brandName ?: '-' }}</div>
                                                        <div class="small text-muted">{{ $itemName ?: '' }} {{ $sleeveType ? '(' . $sleeveType . ')' : '' }}</div>
                                                        @if(!empty($item->sku))
                                                            <div class="small text-primary" style="font-size: 11px;">Barcode: {{ $item->sku }}</div>
                                                        @endif
                                                    </td>
                                                    <td>{{ $item->api_color ?: ($item->color ? $item->color->color_name : '-') }}</td>
                                                    <td>
                                                        @php
                                                            $displayArtNo = $item->stockEntryItem ? ($item->stockEntryItem->art_no ?: $item->art_no) : $item->art_no;
                                                        @endphp
                                                        {{ $displayArtNo ?? '-' }}
                                                    </td>
                                                    <td>{{ $item->uom_id ?? '-' }}</td>
                                                    <td>{{ $item->sizeRatio ? $item->sizeRatio->size : ($item->size ?? '-') }}</td>
                                                    <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                                                    <td class="text-end">₹{{ number_format($item->mrp ?? 0, 2) }}</td>
                                                    <td class="text-end">₹{{ number_format($item->rate ?? 0, 2) }}</td>
                                                    <td class="text-end fw-bold">₹{{ number_format($item->amount, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <hr>
                            </div>
                            <div class="col-lg-12 mt-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="mb-3 fw-bold">Show in Customer Invoice PDF</h6>
                                        <div class="row g-3">
                                            <div class="col-md-4 col-lg-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" id="showAmount" {{ is_array($invoice->show_fields) && in_array('amount', $invoice->show_fields) ? 'checked' : '' }} disabled>
                                                    <label class="form-check-label" for="showAmount">Show Amount</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-lg-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" id="showSubTotal" {{ is_array($invoice->show_fields) && in_array('subtotal', $invoice->show_fields) ? 'checked' : '' }} disabled>
                                                    <label class="form-check-label" for="showSubTotal">Show Sub
                                                        Total</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-lg-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" id="showDiscount" {{ is_array($invoice->show_fields) && in_array('discount', $invoice->show_fields) ? 'checked' : '' }} disabled>
                                                    <label class="form-check-label" for="showDiscount">Show Discount</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-lg-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" id="showGrandTotal" {{ is_array($invoice->show_fields) && in_array('grandtotal', $invoice->show_fields) ? 'checked' : '' }} disabled>
                                                    <label class="form-check-label" for="showGrandTotal">Show Grand
                                                        Total</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-lg-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" id="showTax" {{ is_array($invoice->show_fields) && in_array('tax', $invoice->show_fields) ? 'checked' : '' }} disabled>
                                                    <label class="form-check-label" for="showTax">Show Tax
                                                        (GST/IGST)</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-lg-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" id="showMrp" {{ is_array($invoice->show_fields) && in_array('mrp', $invoice->show_fields) ? 'checked' : '' }} disabled>
                                                    <label class="form-check-label" for="showMrp">Show MRP</label>
                                                </div>
                                            </div>

                                            <div class="col-md-4 col-lg-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" id="showPrice" {{ is_array($invoice->show_fields) && in_array('price', $invoice->show_fields) ? 'checked' : '' }} disabled>
                                                    <label class="form-check-label" for="showPrice">Show Price</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 mt-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="mb-3 fw-bold">Show in Delivery Order PDF</h6>
                                        <div class="row g-3">
                                            <div class="col-md-4 col-lg-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" id="showDeliveryMrp" {{ is_array($invoice->delivery_show_fields) && in_array('mrp', $invoice->delivery_show_fields) ? 'checked' : '' }} disabled>
                                                    <label class="form-check-label" for="showDeliveryMrp">Show Retail Price (MRP)</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-lg-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" id="showDeliveryPrice" {{ is_array($invoice->delivery_show_fields) && in_array('price', $invoice->delivery_show_fields) ? 'checked' : '' }} disabled>
                                                    <label class="form-check-label" for="showDeliveryPrice">Show Unit Price</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-lg-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" id="showDeliveryArtNo" {{ is_array($invoice->delivery_show_fields) && in_array('art_no', $invoice->delivery_show_fields) ? 'checked' : '' }} disabled>
                                                    <label class="form-check-label" for="showDeliveryArtNo">Show Art No</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <h6>Invoice Summary:</h6>
                            </div>
                            <div class="col-lg-12">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="summary-left border rounded p-3 h-100">
                                            <div class="d-flex justify-content-between mb-2">
                                                <label class="detail-title">Sub Total:</label>
                                                <div class="text-muted fw-bold">₹{{ number_format($invoice->sub_total, 2) }}
                                                </div>
                                            </div>
                                            @php
                                                $salesDiscountAmount = ($invoice->sub_total * (float)$invoice->sales_discount) / 100;
                                                $boxDiscountTotal = (float)$invoice->discount - $salesDiscountAmount;
                                            @endphp
                                            <div class="d-flex justify-content-between mb-2">
                                                <label class="detail-title">Sales Discount ({{ number_format((float)$invoice->sales_discount, 2) }}%):</label>
                                                <div class="text-muted">₹{{ number_format($salesDiscountAmount, 2) }}</div>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <label class="detail-title">Box Discount:</label>
                                                <div class="text-muted">₹{{ number_format($boxDiscountTotal, 2) }}</div>
                                            </div>
                                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                                <label class="detail-title">Total:</label>
                                                <div class="text-muted fw-bold">₹{{ number_format($invoice->total, 2) }}
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between mb-3">
                                                <label class="detail-title">Other State:</label>
                                                <div class="text-muted">{{ $invoice->other_state ? 'Yes' : 'No' }}</div>
                                            </div>
                                            @if($invoice->other_state)
                                                <div class="d-flex justify-content-between mb-2">
                                                    <label class="detail-title">IGST
                                                        ({{ number_format($invoice->igst_percent, 2) }}%):</label>
                                                    <div class="text-muted">₹{{ number_format($invoice->igst, 2) }}</div>
                                                </div>
                                            @else
                                                <div class="d-flex justify-content-between mb-2">
                                                    <label class="detail-title">CGST
                                                        ({{ number_format($invoice->cgst_percent, 2) }}%):</label>
                                                    <div class="text-muted">₹{{ number_format($invoice->cgst, 2) }}</div>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <label class="detail-title">SGST
                                                        ({{ number_format($invoice->sgst_percent, 2) }}%):</label>
                                                    <div class="text-muted">₹{{ number_format($invoice->sgst, 2) }}</div>
                                                </div>
                                            @endif
                                            <div class="d-flex justify-content-between mb-2 pt-2 border-top">
                                                <label class="detail-title">Tax Amount:</label>
                                                <div class="text-muted fw-bold">
                                                    ₹{{ number_format($invoice->tax_amount, 2) }}</div>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <label class="detail-title">Other Charges:</label>
                                                <div class="text-muted">₹{{ number_format($invoice->other_charges, 2) }}
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <label class="detail-title">Round Off
                                                    ({{ $invoice->round_off_type }}):</label>
                                                <div class="text-muted">₹{{ number_format($invoice->round_off, 2) }}</div>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2 pt-3 border-top">
                                                <label class="detail-title h5 mb-0">Grand Total:</label>
                                                <div class="fw-bold h5 mb-0 text-success">
                                                    ₹{{ number_format($invoice->grand_total, 2) }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="summary-right border rounded p-3 h-100">
                                            <div class="d-flex justify-content-between mb-2">
                                                <label class="detail-title">Invoice Status:</label>
                                                <div class="text-right"><span class="badge bg-primary">{{ $invoice->invoice_status }}</span></div>
                                            </div>
                                            <div class="payment-details-box p-3 bg-light border rounded mb-3">
                                                <h6 class="mb-3 border-bottom pb-2 text-primary"><i class="ri-money-dollar-circle-line me-1"></i>Payment Information</h6>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <label class="detail-title text-muted">Payment Mode:</label>
                                                    <div class="fw-semibold text-dark">{{ $invoice->payment_mode ?? '-' }}</div>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <label class="detail-title text-muted">Cheque / UPI Ref:</label>
                                                    <div class="fw-semibold text-dark">{{ $invoice->extra_input ?? '-' }}</div>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <label class="detail-title text-muted">Due Date:</label>
                                                    <div class="fw-semibold text-dark">
                                                        {{ $invoice->due_date ? $invoice->due_date->format('d-M-Y') : '-' }}
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <label class="detail-title text-muted">Additional Notes:</label>
                                                    <div class="fw-semibold text-dark text-end" style="max-width: 60%; word-wrap: break-word; white-space: normal;">{{ $invoice->notes ?? '-' }}</div>
                                                </div>
                                            </div>

                                            @if($invoice->signature_file)
                                            <div class="d-flex justify-content-between mb-3 border-top pt-3">
                                                <label class="detail-title text-muted">Authorized Signature:</label>
                                                <div class="text-muted">
                                                    @php
                                                        $sigExt = pathinfo($invoice->signature_file, PATHINFO_EXTENSION);
                                                        $isSigImage = in_array(strtolower($sigExt), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                        $sigUrl = asset($invoice->signature_file);
                                                    @endphp
                                                    <div class="p-1 border rounded d-inline-flex align-items-center bg-white shadow-sm">
                                                        @if($isSigImage)
                                                            <img src="{{ $sigUrl }}" class="rounded cursor-pointer view-image" data-image="{{ $sigUrl }}" width="45" height="45" style="object-fit: cover;" alt="Signature">
                                                        @else
                                                            <a href="{{ $sigUrl }}" target="_blank" class="text-decoration-none d-flex align-items-center px-2">
                                                                @if(strtolower($sigExt) == 'pdf')
                                                                    <i class="ri ri-file-pdf-2-line text-danger fs-3"></i>
                                                                @else
                                                                    <i class="ri ri-file-text-fill text-primary fs-3"></i>
                                                                @endif
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            
                                            @if($invoice->attachment_file)
                                            <div class="d-flex justify-content-between mb-2">
                                                <label class="detail-title text-muted">Attachments:</label>
                                                <div class="text-muted">
                                                    @php
                                                        $attExt = pathinfo($invoice->attachment_file, PATHINFO_EXTENSION);
                                                        $isAttImage = in_array(strtolower($attExt), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                        $attUrl = asset($invoice->attachment_file);
                                                    @endphp
                                                    <div class="p-1 border rounded d-inline-flex align-items-center bg-white shadow-sm">
                                                        @if($isAttImage)
                                                            <img src="{{ $attUrl }}" class="rounded cursor-pointer view-image"
                                                                data-image="{{ $attUrl }}" width="45" height="45"
                                                                style="object-fit: cover;" alt="Attachment">
                                                        @else
                                                            <a href="{{ $attUrl }}" target="_blank"
                                                                class="text-decoration-none d-flex align-items-center px-2">
                                                                @if(strtolower($attExt) == 'pdf')
                                                                    <i class="ri ri-file-pdf-2-line text-danger fs-3"></i>
                                                                @else
                                                                    <i class="ri ri-file-text-fill text-primary fs-3"></i>
                                                                @endif
                                                                <span class="ms-1 small text-dark fw-bold text-uppercase"
                                                                    style="font-size: 10px;">{{ $attExt }}</span>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
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
        var invoiceId = "{{ $invoice->id }}";
        
        $('#einvoice-generate').on('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to generate an E-Invoice for this Sales Invoice?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, generate it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    var btn = $(this);
                    btn.prop('disabled', true).text('Generating...');
                    
                    Swal.fire({
                        title: 'Generating...',
                        text: 'Please wait while we generate the E-Invoice.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    $.ajax({
                        url: "{{ url('sales_invoices/generate-einvoice') }}/" + invoiceId,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                                btn.prop('disabled', false).html('<i class="ri ri ri-receipt-line"></i> Generate E-Invoice');
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 419) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Session Expired',
                                    text: 'Your session has expired or the page was idle. Please reload the page to refresh your CSRF token.',
                                    confirmButtonText: 'Reload Page'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
                            }
                            btn.prop('disabled', false).html('<i class="ri ri-receipt-line"></i> Generate E-Invoice');
                        }
                    });
                }
            });
        });

        $('#einvoice-cancel').on('click', function() {
            Swal.fire({
                title: 'Cancel E-Invoice',
                html: `
                    <div class="alert alert-warning text-start p-2 mb-3" style="font-size: 13.5px; border: 1px solid #ffd8a8; background-color: #fff9db; color: #d9480f; border-radius: 4px; line-height: 1.4;">
                        <div class="fw-semibold mb-1" style="font-size: 14.5px; color: #d9480f;"><i class="ri-information-line me-1"></i> GST e-Invoicing Guidelines:</div>
                        <ul class="mb-0 ps-3">
                            <li><strong>24-Hour Window:</strong> IRN can only be cancelled within 24 hours of generation.</li>
                            <li><strong>E-Way Bill:</strong> If linked to an active E-Way Bill, the E-Way Bill will be automatically cancelled first.</li>
                            <li><strong>No Re-Use:</strong> Once cancelled, this invoice number cannot be reused to generate another IRN.</li>
                            <li><strong>Full Cancellation:</strong> Partial cancellations are not allowed.</li>
                        </ul>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="cancel_reason" class="form-label font-semibold text-dark">Cancellation Reason <span class="text-danger">*</span></label>
                        <select id="cancel_reason" class="form-select">
                            <option value="">Select Reason</option>
                            <option value="1">1 - Duplicate</option>
                            <option value="2">2 - Data Entry Mistake</option>
                            <option value="3">3 - Order Cancelled</option>
                            <option value="4">4 - Others</option>
                        </select>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="cancel_remarks" class="form-label font-semibold text-dark">Remarks / Explanation <span class="text-danger">*</span></label>
                        <textarea id="cancel_remarks" class="form-control" rows="2" placeholder="Explain the reason for cancellation (min 3 chars)"></textarea>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, cancel it!',
                preConfirm: () => {
                    const reason = Swal.getPopup().querySelector('#cancel_reason').value;
                    const remarks = Swal.getPopup().querySelector('#cancel_remarks').value.trim();
                    if (!reason) {
                        Swal.showValidationMessage(`Please select a reason`);
                    } else if (remarks.length < 3) {
                        Swal.showValidationMessage(`Remarks must be at least 3 characters`);
                    }
                    return { cancel_reason: reason, cancel_remarks: remarks }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    var btn = $(this);
                    btn.prop('disabled', true).text('Canceling...');

                    Swal.fire({
                        title: 'Canceling...',
                        text: 'Please wait while we cancel the E-Invoice.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    $.ajax({
                        url: "{{ url('sales_invoices/cancel-einvoice') }}/" + invoiceId,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            cancel_reason: result.value.cancel_reason,
                            cancel_remarks: result.value.cancel_remarks
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Canceled!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                                btn.prop('disabled', false).html('<i class="ri ri-close-circle-line"></i> Cancel E-Invoice');
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 419) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Session Expired',
                                    text: 'Your session has expired or the page was idle. Please reload the page to refresh your CSRF token.',
                                    confirmButtonText: 'Reload Page'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
                            }
                            btn.prop('disabled', false).html('<i class="ri ri-close-circle-line"></i> Cancel E-Invoice');
                        }
                    });
                }
            });
        });

        $('#einvoice-expired').on('click', function() {
            Swal.fire({
                title: 'Cancellation Expired',
                text: 'According to GST guidelines, an E-Invoice cannot be cancelled after 24 hours of generation. Please issue a Credit Note instead.',
                icon: 'error',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        });

        $('#ewaybill-cancel').on('click', function() {
            $('#einvoiceDetailsModal').modal('hide');
            Swal.fire({
                title: 'Cancel E-Way Bill',
                html: `
                    <div class="alert alert-warning text-start p-2 mb-3" style="font-size: 13.5px; border: 1px solid #ffd8a8; background-color: #fff9db; color: #d9480f; border-radius: 4px; line-height: 1.4;">
                        <div class="fw-semibold mb-1" style="font-size: 14.5px; color: #d9480f;"><i class="ri-information-line me-1"></i> GST E-Way Bill Cancellation Guidelines:</div>
                        <ul class="mb-0 ps-3">
                            <li><strong>24-Hour Window:</strong> E-Way Bill can only be cancelled within 24 hours of generation.</li>
                            <li><strong>Transit Check:</strong> Cannot cancel if the goods are already in transit or verified by a GST officer.</li>
                        </ul>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="ewb_cancel_reason" class="form-label font-semibold text-dark">Cancellation Reason <span class="text-danger">*</span></label>
                        <select id="ewb_cancel_reason" class="form-select">
                            <option value="">Select Reason</option>
                            <option value="1">1 - Duplicate</option>
                            <option value="2">2 - Data Entry Mistake</option>
                            <option value="3">3 - Order Cancelled</option>
                            <option value="4">4 - Others</option>
                        </select>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="ewb_cancel_remarks" class="form-label font-semibold text-dark">Remarks / Explanation <span class="text-danger">*</span></label>
                        <textarea id="ewb_cancel_remarks" class="form-control" rows="2" placeholder="Explain the reason for cancellation (min 3 chars)"></textarea>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, cancel it!',
                preConfirm: () => {
                    const reason = Swal.getPopup().querySelector('#ewb_cancel_reason').value;
                    const remarks = Swal.getPopup().querySelector('#ewb_cancel_remarks').value.trim();
                    if (!reason) {
                        Swal.showValidationMessage(`Please select a reason`);
                    } else if (remarks.length < 3) {
                        Swal.showValidationMessage(`Remarks must be at least 3 characters`);
                    }
                    return { cancel_reason: reason, cancel_remarks: remarks }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Canceling...',
                        text: 'Please wait while we cancel the E-Way Bill.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    $.ajax({
                        url: "{{ url('sales_invoices/cancel-ewaybill') }}/" + invoiceId,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            cancel_reason: result.value.cancel_reason,
                            cancel_remarks: result.value.cancel_remarks
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Canceled!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', response.message, 'error').then(() => {
                                    $('#einvoiceDetailsModal').modal('show');
                                });
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'An error occurred. Please try again.', 'error').then(() => {
                                $('#einvoiceDetailsModal').modal('show');
                            });
                        }
                    });
                } else {
                    $('#einvoiceDetailsModal').modal('show');
                }
            });
        });

        $('#ewaybill-expired').on('click', function() {
            $('#einvoiceDetailsModal').modal('hide');
            Swal.fire({
                title: 'Cancellation Expired',
                text: 'According to GST guidelines, an E-Way Bill cannot be cancelled after 24 hours of generation.',
                icon: 'error',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            }).then(() => {
                $('#einvoiceDetailsModal').modal('show');
            });
        });

        $(document).on('click', '.copy-btn', function() {
            let targetId = $(this).data('clipboard-target');
            let text = $(targetId).text();
            let btn = $(this);
            
            navigator.clipboard.writeText(text).then(() => {
                let originalHtml = btn.html();
                btn.html('<i class="ri ri-check-line text-success"></i>');
                setTimeout(() => {
                    btn.html(originalHtml);
                }, 1500);
            });
        });
    });
</script>

<!-- E-Invoice Details Modal -->
<div class="modal fade" id="einvoiceDetailsModal" tabindex="-1" aria-labelledby="einvoiceDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title text-white" id="einvoiceDetailsModalLabel">
                    <i class="ri ri-receipt-line me-1"></i> E-Invoice & E-Way Bill Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- E-Invoice Segment -->
                <div class="mb-4">
                    <h6 class="text-primary border-bottom pb-2 mb-3">E-Invoice Info</h6>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted fw-semibold small mb-1 d-block">Invoice Reference Number (IRN)</label>
                        <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded border">
                            <span class="text-break font-monospace small" id="irnVal">{{ $invoice->irn ?? '-' }}</span>
                            @if($invoice->irn)
                            <button class="btn btn-sm btn-outline-secondary copy-btn py-0 px-2" data-clipboard-target="#irnVal" title="Copy IRN">
                                <i class="ri ri-file-copy-line"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row text-start">
                        <div class="col-6 mb-3">
                            <label class="form-label text-muted fw-semibold small mb-1">Acknowledgement No.</label>
                            <p class="fw-bold mb-0 text-dark">{{ $invoice->ack_no ?? '-' }}</p>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label text-muted fw-semibold small mb-1">Acknowledgement Date</label>
                            <p class="fw-bold mb-0 text-dark">
                                {{ $invoice->ack_date ? \Carbon\Carbon::parse($invoice->ack_date)->format('d-m-Y h:i A') : '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- E-Way Bill Segment -->
                <div>
                    <h6 class="text-info border-bottom pb-2 mb-3">E-Way Bill Info</h6>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted fw-semibold small mb-1 d-block">E-Way Bill Number</label>
                        <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded border">
                            <span class="font-monospace fw-bold text-dark" id="ewbVal">{{ $invoice->eway_bill_no ?? '-' }}</span>
                            @if($invoice->eway_bill_no)
                            <button class="btn btn-sm btn-outline-secondary copy-btn py-0 px-2" data-clipboard-target="#ewbVal" title="Copy E-Way Bill No">
                                <i class="ri ri-file-copy-line"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                    
                    @if($invoice->eway_bill_no)
                        @php
                            $ewbDateTime = $invoice->eway_bill_date ? \Carbon\Carbon::parse($invoice->eway_bill_date) : null;
                            $isEwbExpired = $ewbDateTime ? $ewbDateTime->diffInHours(now()) >= 24 : false;
                        @endphp
                        <div class="mb-3">
                            @if($isEwbExpired)
                            <button type="button" class="btn btn-secondary btn-sm w-100" id="ewaybill-expired">
                                <i class="ri ri-close-circle-line me-1"></i> Cancel E-Way Bill (Window Expired)
                            </button>
                            @else
                            <button type="button" class="btn btn-danger btn-sm w-100" id="ewaybill-cancel">
                                <i class="ri ri-close-circle-line me-1"></i> Cancel E-Way Bill
                            </button>
                            @endif
                        </div>
                    @endif
                    
                    <div class="row text-start">
                        <div class="col-6">
                            <label class="form-label text-muted fw-semibold small mb-1">E-Way Bill Date</label>
                            <p class="fw-bold mb-0 text-dark">
                                {{ $invoice->eway_bill_date ? \Carbon\Carbon::parse($invoice->eway_bill_date)->format('d-m-Y h:i A') : '-' }}
                            </p>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted fw-semibold small mb-1">Valid Till</label>
                            <p class="fw-bold mb-0 text-success">
                                {{ $invoice->eway_bill_valid_till ? \Carbon\Carbon::parse($invoice->eway_bill_valid_till)->format('d-m-Y h:i A') : '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
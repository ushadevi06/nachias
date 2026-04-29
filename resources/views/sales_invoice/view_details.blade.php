@extends('layouts.common')
@section('title', 'View Sale Invoice - ' . env('WEBSITE_NAME'))
@section('content')
    <div class="container-xxl section-padding">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-header-box">
                    <h4>View Sales Invoice</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ url('sales_invoices/download/' . $invoice->id) }}" class="btn btn-primary"
                            target="_blank">
                            <i class="ri ri-download-line back-arrow"></i>Download
                        </a>
                        <a href="{{ url('sales_invoices/print/' . $invoice->id) }}" class="btn btn-primary" target="_blank">
                            <i class="ri ri-printer-line back-arrow"></i>Print
                        </a>
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
                                <label class="detail-title">Invoice No:</label>
                                <div class="text-muted">{{ $invoice->inv_no }}</div>
                            </div>

                            <div class="col-md-4">
                                <label class="detail-title">Invoice Date:</label>
                                <div class="text-muted">{{ $invoice->inv_date->format('d-M-Y') }}</div>
                            </div>

                            <div class="col-md-4">
                                <label class="detail-title">Customer / Buyer Name:</label>
                                <div class="text-muted">{{ $invoice->customer ? $invoice->customer->name : 'N/A' }}
                                    ({{ $invoice->customer ? $invoice->customer->code : '' }})</div>
                            </div>

                            <div class="col-md-4">
                                <label class="detail-title">Linked SO No:</label>
                                <div class="text-muted">{{ $invoice->saleOrder ? $invoice->saleOrder->so_no : 'N/A' }}</div>
                            </div>

                            <div class="col-md-4">
                                <label class="detail-title">Delivery Address:</label>
                                <div class="text-muted">{{ $invoice->delivery_address ?? 'N/A' }}</div>
                            </div>

                            <div class="col-md-4">
                                <label class="detail-title">Remarks:</label>
                                <div class="text-muted">{{ $invoice->remarks ?? 'N/A' }}</div>
                            </div>

                            <div class="col-lg-12">
                                <hr>
                            </div>

                            <div class="col-lg-12">
                                <h6>Item Details:</h6>
                            </div>
                            <div class="col-lg-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
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
                                                            if ($item->item) {
                                                                $brandName = ($item->item->brand ? $item->item->brand->brand_name : ($item->brandCategory ? $item->brandCategory->name : 'N/A'));
                                                                $itemName = ($item->item->style ? $item->item->style->style_name : $item->item->name);
                                                            } elseif ($item->stockEntryItem) {
                                                                if ($item->stockEntryItem->item) {
                                                                    $seItem = $item->stockEntryItem->item;
                                                                    $brandName = ($seItem->brand ? $seItem->brand->brand_name : ($seItem->brandCategory ? $seItem->brandCategory->name : 'N/A'));
                                                                    $itemName = ($seItem->style ? $seItem->style->style_name : $seItem->name);
                                                                } else {
                                                                    $brandName = $item->stockEntryItem->finished_item_code;
                                                                }
                                                            } else {
                                                                $brandName = $item->brandCategory ? $item->brandCategory->name : 'N/A';
                                                                $itemName = $item->item ? $item->item->name : 'N/A';
                                                            }
                                                        @endphp
                                                        <div class="fw-bold">{{ $brandName }}</div>
                                                        <small class="text-muted">{{ $itemName }} ({{ $item->sleeve_type ?? '-' }})</small>
                                                    </td>
                                                    <td>{{ $item->color ? $item->color->color_name : '-' }}</td>
                                                    <td>{{ $item->art_no ?? '-' }}</td>
                                                    <td>{{ $item->uom ? $item->uom->uom_code : '-' }}</td>
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
                                            <div class="d-flex justify-content-between mb-2">
                                                <label class="detail-title">Discount
                                                    ({{ number_format($invoice->discount_percent, 2) }}%):</label>
                                                <div class="text-muted">₹{{ number_format($invoice->discount, 2) }}</div>
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
                                                <div class="text-right"><span
                                                        class="badge bg-primary">{{ $invoice->invoice_status }}</span></div>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <label class="detail-title">Payment Mode:</label>
                                                <div class="text-muted">{{ $invoice->payment_mode ?? 'N/A' }}</div>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <label class="detail-title">Cheque / UPI Ref:</label>
                                                <div class="text-muted">{{ $invoice->extra_input ?? 'N/A' }}</div>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <label class="detail-title">Due Date:</label>
                                                <div class="text-muted">
                                                    {{ $invoice->due_date ? $invoice->due_date->format('d-M-Y') : 'N/A' }}
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <label class="detail-title">Additional Notes:</label>
                                                <div class="text-muted">{{ $invoice->notes ?? 'N/A' }}</div>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2 border-top pt-2">
                                                <label class="detail-title">Authorized Signature:</label>
                                                <div class="text-muted">
                                                    @if($invoice->signature_file)
                                                        @php
                                                            $sigExt = pathinfo($invoice->signature_file, PATHINFO_EXTENSION);
                                                            $isSigImage = in_array(strtolower($sigExt), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                            $sigUrl = asset($invoice->signature_file);
                                                        @endphp
                                                        <div
                                                            class="p-1 border rounded d-inline-flex align-items-center bg-white shadow-sm">
                                                            @if($isSigImage)
                                                                <img src="{{ $sigUrl }}" class="rounded cursor-pointer view-image"
                                                                    data-image="{{ $sigUrl }}" width="45" height="45"
                                                                    style="object-fit: cover;" alt="Signature">
                                                            @else
                                                                <a href="{{ $sigUrl }}" target="_blank"
                                                                    class="text-decoration-none d-flex align-items-center px-2">
                                                                    @if(strtolower($sigExt) == 'pdf')
                                                                        <i class="ri-file-pdf-fill text-danger fs-3"></i>
                                                                    @else
                                                                        <i class="ri-file-text-fill text-primary fs-3"></i>
                                                                    @endif
                                                                    <span class="ms-1 small text-dark fw-bold text-uppercase"
                                                                        style="font-size: 10px;">{{ $sigExt }}</span>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @else
                                                        N/A
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <label class="detail-title">Attachments:</label>
                                                <div class="text-muted">
                                                    @if($invoice->attachment_file)
                                                        @php
                                                            $attExt = pathinfo($invoice->attachment_file, PATHINFO_EXTENSION);
                                                            $isAttImage = in_array(strtolower($attExt), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                            $attUrl = asset($invoice->attachment_file);
                                                        @endphp
                                                        <div
                                                            class="p-1 border rounded d-inline-flex align-items-center bg-white shadow-sm">
                                                            @if($isAttImage)
                                                                <img src="{{ $attUrl }}" class="rounded cursor-pointer view-image"
                                                                    data-image="{{ $attUrl }}" width="45" height="45"
                                                                    style="object-fit: cover;" alt="Attachment">
                                                            @else
                                                                <a href="{{ $attUrl }}" target="_blank"
                                                                    class="text-decoration-none d-flex align-items-center px-2">
                                                                    @if(strtolower($attExt) == 'pdf')
                                                                        <i class="ri-file-pdf-fill text-danger fs-3"></i>
                                                                    @else
                                                                        <i class="ri-file-text-fill text-primary fs-3"></i>
                                                                    @endif
                                                                    <span class="ms-1 small text-dark fw-bold text-uppercase"
                                                                        style="font-size: 10px;">{{ $attExt }}</span>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @else
                                                        N/A
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
        </div>
    </div>
    </div>
@endsection
@extends('layouts.common')
@section('title', 'View Old Sales Invoice - ' . env('WEBSITE_NAME'))
@section('content')
<div class="container-xxl section-padding">
    <div class="row">
        <div class="col-lg-12">
            <div class="table-header-box d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
                <h4 class="mb-0">View Old Sales Invoice</h4>
                <div class="d-flex flex-wrap gap-2">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="pdfOptionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ri ri-file-pdf-line back-arrow me-1"></i> PDF Options
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="pdfOptionsDropdown">
                            <li>
                                <a class="dropdown-item btn-custom-pdf-trigger" href="javascript:void(0);">
                                    <i class="ri ri-download-line me-2"></i> Download PDF
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item btn-custom-pdf-trigger" href="javascript:void(0);">
                                    <i class="ri ri-printer-line me-2"></i> Print PDF
                                </a>
                            </li>
                        </ul>
                    </div>
                    <a href="{{ url('old_sales_invoices') }}" class="btn btn-secondary">
                        <i class="ri ri-arrow-left-line back-arrow me-1"></i> Back
                    </a>
                </div>
            </div>

            <div class="card detail-card mb-4">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <h6>Order Details:</h6>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="detail-title text-muted">Invoice No:</label>
                            <div class="fw-bold text-primary fs-5">{{ $invoice->doc_no }}</div>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="detail-title text-muted">Invoice Date:</label>
                            <div class="text-dark fw-semibold">{{ $invoice->doc_date ? $invoice->doc_date->format('d-M-Y') : '-' }}</div>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="detail-title text-muted">Brand:</label>
                            <div class="text-dark fw-semibold">
                                {{ $invoice->brand ? $invoice->brand->brand_name : ($invoice->items->first()?->brand?->brand_name ?? '-') }}
                                @if($invoice->brand && $invoice->brand->code)
                                    <span class="text-muted fw-normal">({{ $invoice->brand->code }})</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title text-muted">Customer / Buyer Name:</label>
                            <div class="fw-bold text-primary fs-6">{{ $invoice->customer_name ?: ($invoice->customer?->name ?? '-') }}
                                @if($invoice->customer && $invoice->customer->code)
                                    <span class="text-muted fw-normal">({{ $invoice->customer->code }})</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title text-muted">GSTIN / Reg No:</label>
                            <div class="text-dark fw-semibold">{{ $invoice->gstin_reg_no ?: ($invoice->customer?->gst_no ?? '-') }}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title text-muted">Destination / Pincode:</label>
                            <div class="text-muted">{{ $invoice->customer?->city?->city_name ?? ($invoice->pincode ? 'PIN: ' . $invoice->pincode : '-') }}</div>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="detail-title text-muted">Billing Address:</label>
                            <div class="text-muted">{!! nl2br(e($invoice->bill_to ?: ($invoice->customer?->address_line_1 ?? '-'))) !!}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title text-muted">Delivery Address:</label>
                            <div class="text-muted">{!! nl2br(e($invoice->ship_to ?: ($invoice->bill_to ?: ($invoice->customer?->address_line_1 ?? '-')))) !!}</div>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="detail-title text-muted">Vehicle No / Transport:</label>
                            <div class="text-muted">{{ $invoice->vehicle_no ?: '-' }}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title text-muted">Total Quantity:</label>
                            <div class="fw-bold text-success">{{ number_format($invoice->total_qty, 0) }} Pcs</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title text-muted">HSN Code:</label>
                            <div class="text-muted">{{ $invoice->items->first()?->hsn_sac ?: '62052000' }}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="detail-title text-muted">Ack No / Date:</label>
                            <div class="text-muted">{{ $invoice->ack_no ? $invoice->ack_no . ' (' . ($invoice->ack_date ?: '-') . ')' : '-' }}</div>
                        </div>

                        @if($invoice->irn_no)
                        <div class="col-lg-12">
                            <label class="detail-title text-muted">IRN (E-Invoice):</label>
                            <div class="text-break font-monospace bg-light p-2 rounded small">{{ $invoice->irn_no }}</div>
                        </div>
                        @endif

                        <div class="col-lg-12">
                            <hr>
                        </div>

                        <!-- Item Details Section -->
                        <div class="col-lg-12">
                            <h6>Item Details:</h6>
                        </div>
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover text-nowrap align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px;">S.No</th>
                                            <th>Stock Item</th>
                                            <th class="text-center">Color</th>
                                            <th>Art No</th>
                                            <th class="text-center">UOM</th>
                                            <th class="text-center">Size</th>
                                            <th class="text-end">Quantity</th>
                                            <th class="text-end">MRP</th>
                                            <th class="text-end">Price</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($invoice->items as $item)
                                        @php
                                            // Brand Code (e.g. CF, CB, CDW, CW, etc.)
                                            $brandCode = $item->brand?->code ?: ($invoice->brand?->code ?? ($item->brand?->brand_name ?? 'CF'));

                                            // Style Code (PRT, CKD, PLN)
                                            $styleName = strtoupper($item->style?->style_name ?: ($item->style?->code ?? 'PLAIN'));
                                            $styleCode = 'PLN';
                                            if (str_contains($styleName, 'PRINT') || str_contains($styleName, 'PRT')) {
                                                $styleCode = 'PRT';
                                            } elseif (str_contains($styleName, 'CHECK') || str_contains($styleName, 'CKD')) {
                                                $styleCode = 'CKD';
                                            }

                                            // Sleeve Code (FS, HS)
                                            $sleeveRaw = strtoupper(trim($item->class2_sleeve ?? ''));
                                            $sleeveCode = 'FS';
                                            $sleeveLabel = 'Full';
                                            if (str_contains($sleeveRaw, 'HALF') || str_contains($sleeveRaw, 'HS') || $sleeveRaw === 'H') {
                                                $sleeveCode = 'HS';
                                                $sleeveLabel = 'Half';
                                            } elseif (str_contains($sleeveRaw, 'FULL') || str_contains($sleeveRaw, 'FS') || $sleeveRaw === 'F') {
                                                $sleeveCode = 'FS';
                                                $sleeveLabel = 'Full';
                                            }

                                            $stockItemCode = "{$brandCode}-{$styleCode}-{$sleeveCode}";

                                            // Extract clean Art No (e.g. "CB1122-4 40 L F/S" -> "CB1122-4")
                                            $rawDesc = trim($item->description ?? '');
                                            $tokens = preg_split('/\s+/', $rawDesc);
                                            $cleanArtNo = $tokens[0] ?? $rawDesc;

                                            if (isset($tokens[1]) && strlen($tokens[1]) == 1 && !is_numeric($tokens[1]) && !in_array(strtoupper($tokens[1]), ['S', 'M', 'L', 'XL', 'XXL', 'FS', 'HS'])) {
                                                $cleanArtNo = $tokens[0] . '-' . $tokens[1];
                                            }

                                            // Extract Color from Art No:
                                            // CB1122-4 => 4, CF20797-2 => 2, ARTCOTTON-A => A
                                            $color = '-';
                                            if (preg_match('/-([A-Za-z0-9]+)$/', $cleanArtNo, $colorMatch)) {
                                                $color = $colorMatch[1];
                                            } elseif (preg_match('/([A-Za-z0-9])$/', $cleanArtNo, $colorMatch)) {
                                                $color = $colorMatch[1];
                                            }

                                            // Clean Size: Extract ONLY numeric digits (e.g., "40 L" -> "40", "42 XL" -> "42")
                                            $rawSize = trim($item->size ?? '');
                                            $cleanSize = preg_replace('/[^0-9]/', '', $rawSize);
                                            if (empty($cleanSize)) {
                                                $cleanSize = $rawSize ?: '-';
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="fw-bold">{{ $stockItemCode }}</div>
                                                <div class="small text-muted">{{ $cleanArtNo }} ({{ $sleeveLabel }})</div>
                                            </td>
                                            <td class="text-center">{{ $color }}</td>
                                            <td>{{ $cleanArtNo }}</td>
                                            <td class="text-center">PCS</td>
                                            <td class="text-center">{{ $cleanSize }}</td>
                                            <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                                            <td class="text-end">₹{{ number_format($item->mrp, 2) }}</td>
                                            <td class="text-end">₹{{ number_format($item->price, 2) }}</td>
                                            <td class="text-end fw-bold">₹{{ number_format($item->gross_amount, 2) }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="10" class="text-center">No items found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <hr>
                        </div>

                        <!-- Show in Customer Invoice PDF Section -->
                        <div class="col-lg-12">
                            <div class="card border">
                                <div class="card-body">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">
                                        <h6 class="mb-0 fw-bold" style="color: #7367f0;">Show in Customer Invoice PDF</h6>
                                        <button type="button" class="btn btn-sm btn-primary" id="btnDownloadCustomPdf">
                                            <i class="ri ri-file-pdf-line me-1"></i> Download / Print PDF with Selected Fields
                                        </button>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input show-field-cb" type="checkbox" id="showAmount" value="amount" checked>
                                                <label class="form-check-label text-dark" for="showAmount">Show Amount</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input show-field-cb" type="checkbox" id="showSubTotal" value="subtotal" checked>
                                                <label class="form-check-label text-dark" for="showSubTotal">Show Sub Total</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input show-field-cb" type="checkbox" id="showDiscount" value="discount" checked>
                                                <label class="form-check-label text-dark" for="showDiscount">Show Discount</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input show-field-cb" type="checkbox" id="showGrandTotal" value="grandtotal" checked>
                                                <label class="form-check-label text-dark" for="showGrandTotal">Show Grand Total</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input show-field-cb" type="checkbox" id="showTax" value="tax" checked>
                                                <label class="form-check-label text-dark" for="showTax">Show Tax (GST/IGST)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input show-field-cb" type="checkbox" id="showMrp" value="mrp" checked>
                                                <label class="form-check-label text-dark" for="showMrp">Show MRP</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-lg-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input show-field-cb" type="checkbox" id="showPrice" value="price" checked>
                                                <label class="form-check-label text-dark" for="showPrice">Show Price</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <hr>
                        </div>

                        <!-- Invoice Summary & Address Section -->
                        <div class="col-lg-12">
                            <h6>Invoice Summary:</h6>
                        </div>
                        <div class="col-lg-12">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="summary-left border rounded p-3 h-100">
                                        <div class="d-flex justify-content-between mb-2">
                                            <label class="detail-title text-dark fw-bold">Sub Total:</label>
                                            <div class="text-muted fw-bold">₹{{ number_format($invoice->sub_total, 2) }}</div>
                                        </div>

                                        @php
                                            $discountPercent = $invoice->sub_total > 0 ? round(($invoice->discount_amount / $invoice->sub_total) * 100, 2) : 0;
                                        @endphp
                                        <div class="d-flex justify-content-between mb-2">
                                            <label class="detail-title">Sales Discount ({{ number_format($discountPercent, 2) }}%):</label>
                                            <div class="text-muted">₹{{ number_format($invoice->discount_amount, 2) }}</div>
                                        </div>

                                        <div class="d-flex justify-content-between mb-2">
                                            <label class="detail-title">Box Discount:</label>
                                            <div class="text-muted">₹0.00</div>
                                        </div>

                                        <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                            <label class="detail-title text-dark fw-bold">Total:</label>
                                            <div class="text-muted fw-bold">₹{{ number_format($invoice->taxable_amount, 2) }}</div>
                                        </div>

                                        @php
                                            $isOtherState = ($invoice->igst_amount > 0);
                                        @endphp
                                        <div class="d-flex justify-content-between mb-3">
                                            <label class="detail-title">Other State:</label>
                                            <div class="text-muted">{{ $isOtherState ? 'Yes' : 'No' }}</div>
                                        </div>

                                        @if($isOtherState)
                                            <div class="d-flex justify-content-between mb-2">
                                                <label class="detail-title">IGST (5.00%):</label>
                                                <div class="text-muted">₹{{ number_format($invoice->igst_amount, 2) }}</div>
                                            </div>
                                        @else
                                            <div class="d-flex justify-content-between mb-2">
                                                <label class="detail-title">CGST (2.50%):</label>
                                                <div class="text-muted">₹{{ number_format($invoice->cgst_amount, 2) }}</div>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <label class="detail-title">SGST (2.50%):</label>
                                                <div class="text-muted">₹{{ number_format($invoice->sgst_amount, 2) }}</div>
                                            </div>
                                        @endif

                                        @php
                                            $taxAmount = $invoice->cgst_amount + $invoice->sgst_amount + $invoice->igst_amount;
                                            $roundOffType = $invoice->round_off >= 0 ? 'Add' : 'Less';
                                            $absRoundOff = abs($invoice->round_off);
                                        @endphp
                                        <div class="d-flex justify-content-between mb-2 pt-2 border-top">
                                            <label class="detail-title text-dark fw-bold">Tax Amount:</label>
                                            <div class="text-muted fw-bold">₹{{ number_format($taxAmount, 2) }}</div>
                                        </div>

                                        <div class="d-flex justify-content-between mb-2">
                                            <label class="detail-title">Courier Charge:</label>
                                            <div class="text-muted">₹0.00</div>
                                        </div>

                                        <div class="d-flex justify-content-between mb-2">
                                            <label class="detail-title">Round Off ({{ $roundOffType }}):</label>
                                            <div class="text-muted">₹{{ number_format($absRoundOff, 2) }}</div>
                                        </div>

                                        <div class="d-flex justify-content-between mb-2 pt-3 border-top">
                                            <label class="detail-title h5 mb-0 text-dark">Grand Total:</label>
                                            <div class="fw-bold h5 mb-0 text-success" style="color: #28c76f !important;">₹{{ number_format($invoice->total_amount, 2) }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="summary-right border rounded p-3 h-100">
                                        <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                            <label class="detail-title">Invoice Status:</label>
                                            <div class="text-right"><span class="badge bg-primary">Generated</span></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="detail-title text-muted small">Bill To Address:</label>
                                            <div class="text-dark bg-light p-2 rounded small mt-1">{!! nl2br(e($invoice->bill_to ?: ($invoice->customer?->address_line_1 ?? '-'))) !!}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="detail-title text-muted small">Ship To Address:</label>
                                            <div class="text-dark bg-light p-2 rounded small mt-1">{!! nl2br(e($invoice->ship_to ?: ($invoice->bill_to ?: ($invoice->customer?->address_line_1 ?? '-')))) !!}</div>
                                        </div>
                                        @if($invoice->ack_no)
                                        <div class="row g-2 pt-2 border-top">
                                            <div class="col-6">
                                                <label class="detail-title text-muted small">Ack No:</label>
                                                <div class="fw-semibold">{{ $invoice->ack_no }}</div>
                                            </div>
                                            <div class="col-6">
                                                <label class="detail-title text-muted small">Ack Date:</label>
                                                <div class="fw-semibold">{{ $invoice->ack_date ?: '-' }}</div>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    function getSelectedPdfUrl() {
        var selectedFields = [];
        document.querySelectorAll('.show-field-cb:checked').forEach(function (cb) {
            selectedFields.push(cb.value);
        });
        
        var baseUrl = "{{ url('old_sales_invoices/pdf/' . $invoice->id) }}";
        if (selectedFields.length > 0) {
            baseUrl += '?show_fields=' + encodeURIComponent(selectedFields.join(','));
        } else {
            baseUrl += '?show_fields=none';
        }
        return baseUrl;
    }

    var btnCustom = document.getElementById('btnDownloadCustomPdf');
    if (btnCustom) {
        btnCustom.addEventListener('click', function () {
            window.open(getSelectedPdfUrl(), '_blank');
        });
    }

    document.querySelectorAll('.btn-custom-pdf-trigger').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            window.open(getSelectedPdfUrl(), '_blank');
        });
    });
});
</script>
@endsection

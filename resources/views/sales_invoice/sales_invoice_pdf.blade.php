<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Invoice - {{ $invoice->inv_no }}</title>
    <style>
        @page {
            margin: 20px 10px 15px 10px;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 11px;
            color: #000000;
            margin: 0;
            padding: 0;
        }
        .footer-page {
            position: fixed;
            bottom: -8px;
            left: 0;
            right: 0;
            height: 15px;
            font-size: 9px;
            font-family: 'Helvetica', Arial, sans-serif;
            color: #000000;
            border-top: 1px solid #000000;
            padding-top: 3px;
        }
        .footer-page-left {
            position: absolute;
            left: 0;
        }
        .footer-page-right {
            position: absolute;
            right: 0;
        }
        .page-num:after {
            content: counter(page);
        }
        .page-total:after {
            content: counter(pages);
        }
        .container {
            padding: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        th, td {
            border: 1px solid #000000;
            padding: 4px;
            text-align: left;
            vertical-align: top;
            color: #000000;
        }
        .item-table th, .item-table td {
            border-left: 1px solid #000000;
            border-right: 1px solid #000000;
            border-top: none;
            border-bottom: none;
            padding: 4px;
            text-align: left;
            vertical-align: top;
        }
        .no-border th, .no-border td {
            border: none;
        }
        .compact-details th, .compact-details td {
            padding: 1px 4px;
        }
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .bold { font-weight: bold; }
        .header-table td {
            border: none;
            padding: 0px 4px;
        }
        .header-title {
            font-size: 17px;
            font-weight: bold;
            margin-top: 0px;
            margin-bottom: 2px;
            text-align: center;
            color: #000000;
        }
        .company-logo {
            max-width: 120px;
        }
        .qr-code {
            max-width: 100px;
        }
        .original-stamp {
            border: 2px solid #000000;
            padding: 5px;
            font-weight: bold;
            text-align: center;
            transform: rotate(-15deg);
            position: absolute;
            top: 0px;
            right: 20px;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        .section-title {
            background-color: #a3a3a3;
            font-weight: bold;
            padding: 2px 5px;
            border: 1px solid #000000;
            color: #000000;
        }
        .item-table {
            border-bottom: 1px solid #000000;
            table-layout: fixed;
            width: 100%;
        }
        .item-table th {
            background-color: #a3a3a3;
            text-align: center;
            font-weight: bold;
            color: #000000;
        }
        .item-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .item-table tbody tr:last-child td {
            border-bottom: none;
        }
        .summary-table td {
            border: none;
            padding: 2px 5px;
        }
        .summary-table .label { width: 60%; }
        .summary-table .value { width: 40%; text-align: right; }
        .footer-note {
            font-size: 9px;
            margin-top: 10px;
            color: #000000;
        }
        .bank-details {
            margin-top: 10px;
            border: 1px solid #000000;
            padding: 5px;
            color: #000000;
        }
        .signature-box {
            margin-top: 20px;
            text-align: right;
        }
        .signature-area {
            border: 1px solid #000000;
            width: 200px;
            height: 80px;
            display: inline-block;
            margin-top: 5px;
            position: relative;
        }
        .signature-label {
            position: absolute;
            bottom: 5px;
            width: 100%;
            text-align: center;
        }
        .bottom-section {
            border: 1px solid #000000;
            border-top: none;
            width: 100%;
        }

        .bottom-table {
            width: 100%;
            border-collapse: collapse;
        }

        .bottom-table td {
            border-right: 1px solid #000000;
            vertical-align: top;
            padding: 6px;
            font-size: 10px;
            color: #000000;
        }

        .bottom-table td:last-child {
            border-right: none;
        }

        .summary-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-box td {
            padding: 3px 5px;
            border: none;
            color: #000000;
        }

        .summary-box .label {
            text-align: left;
        }

        .summary-box .value {
            text-align: right;
        }

        .summary-box .total-row {
            border-top: 1px solid #000000;
            font-weight: bold;
            color: #000000;
        }

        .amount-words {
            border-top: 1px solid #000000;
            padding: 5px;
            font-weight: bold;
            color: #000000;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                padding: 0;
            }
        }
        .item-table tbody table tr, .item-table tbody table td {
            background-color: transparent !important;
        }
    </style>
</head>
<body>
@php
$showFields = is_array($invoice->show_fields) ? $invoice->show_fields : [];
$showAmount = in_array('amount', $showFields);
$showSubTotal = in_array('subtotal', $showFields);
$showDiscount = in_array('discount', $showFields);
$showTax = in_array('tax', $showFields);
$showGrandTotal = in_array('grandtotal', $showFields);
$showMrp       = in_array('mrp', $showFields);
$showPrice     = in_array('price', $showFields);

$copies = ['ORIGINAL', 'DUPLICATE', 'TRIPLICATE'];

$colWidths = [
    'sno' => 4,
    'desc' => $showAmount ? 30 : 42,
    'art' => 18,
    'uom' => 6,
    'size' => 6,
    'qty' => 8,
];
if ($showMrp) {
    $colWidths['mrp'] = 8;
}
if ($showPrice) {
    $colWidths['price'] = 8;
}
if ($showAmount) {
    $colWidths['amount'] = 12;
}

$totalW = array_sum($colWidths);
foreach ($colWidths as $key => $w) {
    $colWidths[$key] = round(($w / $totalW) * 100, 4);
}

$w_1_5 = $colWidths['sno'] + $colWidths['desc'] + $colWidths['art'] + $colWidths['uom'] + $colWidths['size'];
$w1_6 = $w_1_5 + $colWidths['qty'];

$w_1_4 = $colWidths['sno'] + $colWidths['desc'] + $colWidths['art'] + $colWidths['uom'];
$w_5_6 = $colWidths['size'] + $colWidths['qty'];

$w_colsAfterQty = 0;
if ($showMrp) $w_colsAfterQty += $colWidths['mrp'];
if ($showPrice) $w_colsAfterQty += $colWidths['price'];

$colsAfterQty = 0;
if ($showMrp) $colsAfterQty++;
if ($showPrice) $colsAfterQty++;
@endphp

<div class="footer-page">
    <span class="footer-page-left">E.&O.E.</span>
    <span class="footer-page-right">Page: <span class="page-num"></span> / <span class="page-total"></span></span>
</div>

@foreach($copies as $index => $copyLabel)
    
    @php
    $logoPath = public_path('assets/images/jc_logo.png');
    $logoBase64 = '';
    if (file_exists($logoPath)) {
        $logoData = file_get_contents($logoPath);
        $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
    }

    $qrBase64 = '';
    if (!empty($setting->qr_code)) {
        $uploadedQrPath = public_path('uploads/qr_code/' . $setting->qr_code);
        if (file_exists($uploadedQrPath)) {
            $qrData = file_get_contents($uploadedQrPath);
            $qrBase64 = 'data:image/' . pathinfo($uploadedQrPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode($qrData);
        }
    }
    if (!$qrBase64) {
        $qrPath = public_path('assets/images/qr_code.png');
        if (file_exists($qrPath)) {
            $qrData = file_get_contents($qrPath);
            $qrBase64 = 'data:image/png;base64,' . base64_encode($qrData);
        }
    }

    $einvoiceQr = '';
    if (!empty($invoice->signed_qr_code)) {
        try {
            $einvoiceQr = 'data:image/svg+xml;base64,' . base64_encode(SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate($invoice->signed_qr_code));
        } catch (\Exception $e) {
            \Log::error('E-Invoice QR Generation failed: ' . $e->getMessage());
        }
    }
    @endphp
    <div class="container" style="{{ ($index < count($copies) - 1) ? 'page-break-after: always;' : '' }}">
        <table class="item-table" style="margin-top: 0;  font-size: 11px;">
            <thead style="border-bottom: 1px solid #000; border-top: 1px solid #000;">
                <tr>
                    <th width="4%">S.No</th>
                    <th width="{{ $showAmount ? '30%' : '42%' }}">Description</th>
                    <th width="18%">Art</th>
                    <th width="6%">UOM</th>
                    <th width="6%">Size</th>
                    <th width="8%">Qty</th>
                    @if($showMrp)
                    <th width="8%">MRP</th>
                    @endif
                    @if($showPrice)
                    <th width="8%">Price</th>
                    @endif
                    @if($showAmount)
                    <th width="12%">Amount</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $itemIndex => $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            @php
                                $soItem = \App\Models\SalesOrderItem::where('sale_order_id', $invoice->so_id)
                                    ->where('sku', $item->sku)
                                    ->first();
                                $codeToParse = '';
                                if ($soItem) {
                                    $codeToParse = $soItem->getAttributes()['item_name'] ?? '';
                                    if (empty($codeToParse) || $codeToParse === '-') {
                                        $codeToParse = $soItem->finished_item_code;
                                    }
                                }
                                
                                if (empty($codeToParse) || $codeToParse === '-') {
                                    if ($item->stockEntryItem) {
                                        $codeToParse = $item->stockEntryItem->finished_item_code;
                                    } elseif ($item->item) {
                                        $codeToParse = $item->item->code;
                                    }
                                }
                                
                                if (empty($codeToParse) || $codeToParse === '-') {
                                    $codeToParse = $item->sku ?? '';
                                }

                                $brandName = '';
                                $styleName = '';
                                $sleeve = '';
                                $resolved = false;

                                $seItem = $item->stockEntryItem;
                                if (!$seItem && $soItem && $soItem->stock_entry_item_id) {
                                    $seItem = \App\Models\StockEntryItem::find($soItem->stock_entry_item_id);
                                }

                                if ($seItem) {
                                    if ($seItem->brand) {
                                        $brandName = $seItem->brand->brand_name;
                                    }
                                    if ($seItem->style) {
                                        $styleName = $seItem->style->style_name;
                                    }
                                    
                                    if (empty($brandName) && !empty($seItem->finished_item_code)) {
                                        $seParts = explode('-', $seItem->finished_item_code);
                                        if (count($seParts) > 0) {
                                            $dbBrand = \App\Models\Brand::where('code', trim($seParts[0]))->first();
                                            if ($dbBrand) {
                                                $brandName = $dbBrand->brand_name;
                                            }
                                        }
                                    }
                                    
                                    if (!empty($brandName) || !empty($styleName)) {
                                        $resolved = true;
                                    }
                                    if (empty($sleeve) && !empty($seItem->sleeve_type)) {
                                        $sleeve = $seItem->sleeve_type;
                                    }
                                }
                                
                                if (!empty($item->sleeve_type)) {
                                    $sleeveValUpper = strtoupper(trim($item->sleeve_type));
                                    if ($sleeveValUpper === 'FULL' || $sleeveValUpper === 'F/S' || $sleeveValUpper === 'FS') {
                                        $sleeve = 'F/S';
                                    } elseif ($sleeveValUpper === 'HALF' || $sleeveValUpper === 'H/S' || $sleeveValUpper === 'HS') {
                                        $sleeve = 'H/S';
                                    } else {
                                        $sleeve = $item->sleeve_type;
                                    }
                                } elseif (!empty($sleeve)) {
                                    $sleeveValUpper = strtoupper(trim($sleeve));
                                    if ($sleeveValUpper === 'FULL' || $sleeveValUpper === 'F/S' || $sleeveValUpper === 'FS') {
                                        $sleeve = 'F/S';
                                    } elseif ($sleeveValUpper === 'HALF' || $sleeveValUpper === 'H/S' || $sleeveValUpper === 'HS') {
                                        $sleeve = 'H/S';
                                    }
                                }

                                if ((empty($brandName) || empty($styleName)) && !empty($codeToParse) && $codeToParse !== '-') {
                                    $parts = explode('-', $codeToParse);
                                    
                                    if (count($parts) >= 2) {
                                        $brandCode = trim($parts[0]);
                                        $styleCode = trim($parts[1]);
                                        
                                        $dbBrand = \App\Models\Brand::where('code', $brandCode)->first();
                                        $dbStyle = \App\Models\Style::where('code', $styleCode)->first();
                                        
                                        if ($dbBrand && empty($brandName)) {
                                            $brandName = $dbBrand->brand_name;
                                        }
                                        if ($dbStyle && empty($styleName)) {
                                            $styleName = $dbStyle->style_name;
                                        }
                                        if (!empty($brandName) || !empty($styleName)) {
                                            $resolved = true;
                                        }
                                        
                                        if (empty($sleeve) && isset($parts[2])) {
                                            $sleeveVal = trim($parts[2]);
                                            $sleeveValUpper = strtoupper($sleeveVal);
                                            if ($sleeveValUpper === 'FULL' || $sleeveValUpper === 'F/S' || $sleeveValUpper === 'FS') {
                                                $sleeve = 'F/S';
                                            } elseif ($sleeveValUpper === 'HALF' || $sleeveValUpper === 'H/S' || $sleeveValUpper === 'HS') {
                                                $sleeve = 'H/S';
                                            } else {
                                                $sleeve = $sleeveVal;
                                            }
                                        }
                                    }
                                    
                                    if ((empty($brandName) || empty($styleName)) && count($parts) >= 1) {
                                        $mainCode = trim($parts[0]);
                                        $dbBrand = null;
                                        $brandCode = '';
                                        $allBrands = \App\Models\Brand::all();
                                        foreach ($allBrands as $brand) {
                                            $bCode = trim($brand->code);
                                            if (!empty($bCode) && stripos($mainCode, $bCode) === 0) {
                                                $dbBrand = $brand;
                                                $brandCode = $bCode;
                                                break;
                                            }
                                        }
                                        
                                        if ($dbBrand) {
                                            if (empty($brandName)) {
                                                $brandName = $dbBrand->brand_name;
                                            }
                                            $styleCode = substr($mainCode, strlen($brandCode));
                                            $dbStyle = \App\Models\Style::where('code', $styleCode)->first();
                                            if ($dbStyle && empty($styleName)) {
                                                $styleName = $dbStyle->style_name;
                                            }
                                            $resolved = true;
                                            
                                            if (empty($sleeve) && isset($parts[1])) {
                                                $sleeveVal = trim($parts[1]);
                                                $sleeveValUpper = strtoupper($sleeveVal);
                                                if ($sleeveValUpper === 'FULL' || $sleeveValUpper === 'F/S' || $sleeveValUpper === 'FS') {
                                                    $sleeve = 'F/S';
                                                } elseif ($sleeveValUpper === 'HALF' || $sleeveValUpper === 'H/S' || $sleeveValUpper === 'HS') {
                                                    $sleeve = 'H/S';
                                                } else {
                                                    $sleeve = $sleeveVal;
                                                }
                                            }
                                        }
                                    }
                                }

                                if ($resolved) {
                                    $itemName = trim($brandName . ' ' . $styleName . ' ' . $sleeve);
                                } else {
                                    $itemName = $codeToParse;
                                }
                            @endphp
                            <div class="bold">{{ $itemName }}</div>
                        </td>
                        <td class="text-center">
                            @php
                                $displayArtNo = $item->stockEntryItem ? ($item->stockEntryItem->art_no ?: $item->art_no) : $item->art_no;
                            @endphp
                            {{ $displayArtNo ?? '-' }}
                        </td>
                        <td class="text-center">{{ $item->uom->uom_code ?? 'PCS' }}</td>
                        <td class="text-center">{{ $item->sizeRatio ? $item->sizeRatio->size : ($item->size ?? '-') }}</td>
                        <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                        @if($showMrp)
                        <td class="text-right">{{ number_format($item->mrp, 2) }}</td>
                        @endif
                        @if($showPrice)
                        <td class="text-right">{{ number_format($item->rate, 2) }}</td>
                        @endif
                        @if($showAmount)
                        <td class="text-right bold">{{ number_format($item->amount, 2) }}</td>
                        @endif
                    </tr>
                @endforeach
                
            </tbody>
            
            <tbody>
                <tr>
                    <td style="border-top: none;"></td>
                    <td style="border-top: none;"></td> 
                    <td style="border-top: none;"></td> 
                    <td style="border-top: none;"></td>
                    <td style="border-top: none;"></td> 
                    <td class="text-center bold" style="border-top: 1px solid #000000;">{{ number_format($invoice->items->sum('quantity'), 2) }}</td>
                    
                    @if($showMrp && $showPrice)
                        <td style="border-top: none;"></td>
                        <td class="text-right bold" style="padding-right: 8px; vertical-align: middle; border-top: 1px solid #000000;">Gross</td>
                    @elseif($showMrp || $showPrice)
                        <td class="text-right bold" style="padding-right: 8px; vertical-align: middle; border-top: 1px solid #000000;">Gross</td>
                    @endif
                    
                    @if($showAmount)
                        <td class="text-right bold" style="padding-right: 4px; vertical-align: middle; border-top: 1px solid #000000;">{{ number_format($invoice->sub_total, 2) }}</td>
                    @endif
                </tr>
            </tbody>
            <tbody>
                @php
                    $totalColsCount = 6 + $colsAfterQty + ($showAmount ? 1 : 0);
                    $leftW = $showAmount ? $w1_6 : max(0, 100 - ($w_colsAfterQty + 15));
                    $midW = $w_colsAfterQty;
                    $rightW = $showAmount ? ($colWidths['amount'] ?? 15) : 15;
                @endphp
                <tr style="border-top: 1px solid #000000;">
                    <td colspan="{{ $totalColsCount }}" style="padding: 0; border: none;">
                        <table style="width: 100%; border-collapse: collapse; margin: 0; border: none;">
                            <tr>
                                <!-- Left Side (IRN + Bank + UPI) -->
                                <td style="width: {{ $leftW }}%; padding: 0; vertical-align: top; border-right: 1px solid #000000; border-bottom: 1px solid #000000;">
                                    <table style="width: 100%; border-collapse: collapse; margin: 0; border: none;">
                                        <tr>
                                            <td colspan="2" style="padding: 4px; vertical-align: top; border: none; border-bottom: 1px solid #000000;">
                                                <div style="font-size: 11px;">
                                                    IRN: {{ $invoice->irn ?? '' }}<br>
                                                    Ack No.: {{ $invoice->ack_no ?? '' }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Eway Bill No. : {{ $invoice->eway_bill_no ?? '' }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: {{ ($w_1_4 / $w1_6) * 100 }}%; padding: 4px; vertical-align: top; border: none; border-right: 1px solid #000000;">
                                                <div style="font-size: 12px;">
                                                    <b>Company's Bank Details :</b><br>
                                                    Bank Name : {{ $setting->bank_name ?? '' }}, {{ $setting->branch_location ? $setting->branch_location . ', ' : '' }}<br>
                                                    A/C No. : {{ $setting->account_no ?? '' }}<br>
                                                    Branch & IFS Code : {{ $setting->ifsc_code ?? '' }}<br><br>
                                                    <strong>CASH DISCOUNT IS VALID ONLY ON PAYMENTS RECEIVED WITHIN 30 DAYS AND ONLY ON THE TAXABLE VALUE</strong><br>
                                                    {!! $invoice->notes ?? '' !!}
                                                </div>
                                            </td>
                                            <td style="width: {{ ($w_5_6 / $w1_6) * 100 }}%; padding: 4px; vertical-align: top; text-align: center; border: none;">
                                                <div style="font-size: 11px; min-height: 85px;">
                                                    <span style="font-weight: bold;">For UPI Payment</span><br>
                                                    @if($qrBase64)
                                                        <img src="{{ $qrBase64 }}" style="width: 85px; height: 85px; margin-top: 4px;">
                                                    @else
                                                    <img src="{{ isset($is_print) && $is_print ? asset('assets/images/qr_code.png') : public_path('assets/images/qr_code.png') }}" style="width: 85px; height: 85px; margin-top: 4px;">
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <!-- Right Side Labels -->
                                <td style="width: {{ $midW }}%; padding: 0; vertical-align: top; border-right: 1px solid #000000; border-bottom: 1px solid #000000;">
                                    <table style="width: 100%; border-collapse: collapse; margin: 0; border: none;">
                                        @if($showDiscount && isset($invoice->discount) && $invoice->discount > 0)
                                        <tr><td style="border: none; padding: 2px 4px; text-align: right; white-space: nowrap;">Discount</td></tr>
                                        @endif
                                        @if($showSubTotal)
                                        <tr><td style="border: none; padding: 2px 4px; text-align: right; white-space: nowrap;">Taxable Value</td></tr>
                                        @endif
                                        @if($showTax)
                                            @if(!$invoice->other_state)
                                            <tr><td style="border: none; padding: 2px 4px; text-align: right; white-space: nowrap;">OUTPUT CGST</td></tr>
                                            <tr><td style="border: none; padding: 2px 4px; text-align: right; white-space: nowrap;">OUTPUT SGST</td></tr>
                                            @else
                                            <tr><td style="border: none; padding: 2px 4px; text-align: right; white-space: nowrap;">OUTPUT IGST</td></tr>
                                            @endif
                                        @endif
                                        @if(isset($invoice->other_charges) && $invoice->other_charges > 0)
                                        <tr><td style="border: none; padding: 2px 4px; text-align: right; white-space: nowrap;">Other Charges</td></tr>
                                        @endif
                                        <tr><td style="border: none; padding: 2px 4px; text-align: right; white-space: nowrap;">Round Off</td></tr>
                                    </table>
                                </td>
                                <!-- Right Side Amounts -->
                                <td style="width: {{ $rightW }}%; padding: 0; vertical-align: top; border-bottom: 1px solid #000000;">
                                    <table style="width: 100%; border-collapse: collapse; margin: 0; border: none;">
                                        @if($showDiscount && isset($invoice->discount) && $invoice->discount > 0)
                                        <tr><td style="border: none; padding: 2px 4px; text-align: right;">{{ number_format($invoice->discount, 2) }}</td></tr>
                                        @endif
                                        @if($showSubTotal)
                                        <tr><td style="border: none; padding: 2px 4px; text-align: right;">{{ number_format($invoice->sub_total - ($invoice->discount ?? 0), 2) }}</td></tr>
                                        @endif
                                        @if($showTax)
                                            @if(!$invoice->other_state)
                                            <tr><td style="border: none; padding: 2px 4px; text-align: right;">{{ number_format($invoice->cgst, 2) }}</td></tr>
                                            <tr><td style="border: none; padding: 2px 4px; text-align: right;">{{ number_format($invoice->sgst, 2) }}</td></tr>
                                            @else
                                            <tr><td style="border: none; padding: 2px 4px; text-align: right;">{{ number_format($invoice->igst, 2) }}</td></tr>
                                            @endif
                                        @endif
                                        @if(isset($invoice->other_charges) && $invoice->other_charges > 0)
                                        <tr><td style="border: none; padding: 2px 4px; text-align: right;">{{ number_format($invoice->other_charges, 2) }}</td></tr>
                                        @endif
                                        <tr><td style="border: none; padding: 2px 4px; text-align: right;">{{ (in_array(strtolower($invoice->round_off_type ?? ''), ['less', 'minus']) ? ' - ' : '') . number_format($invoice->round_off ?? 0, 2) }}</td></tr>
                                    </table>
                                </td>
                            </tr>
                            @if($showGrandTotal)
                            <tr>
                                <td style="padding: 8px; border-right: none; border-bottom: 1px solid #000000;">
                                    Rupees &nbsp;&nbsp;&nbsp;: {{ strtoupper($totalInWords) }}
                                </td>
                                <td style="padding: 4px; font-weight: bold; text-align: right; border-right: 1px solid #000000; border-bottom: 1px solid #000000;">
                                    Total
                                </td>
                                <td style="padding: 4px 6px; font-weight: bold; text-align: right; border-bottom: 1px solid #000000;">
                                    {{ number_format($invoice->grand_total, 2) }}
                                </td>
                            </tr>
                            @endif
                        </table>
                    </td>
                </tr>
            </tbody>
            @endif
        </table>
        
        <div class="bold" style="margin-top: 5px; display: none;">Amount of Tax(in words) : {{ $totalTaxInWords }}</div>
        @if($showTax)
        <table class="item-table" style="margin-top: 5px; border-bottom: none; border-top: 1px solid #000;">
            <thead>
                <tr>
                    <th rowspan="2" width="5%" style="border-bottom: 1px solid #000;">S.No</th>
                    <th rowspan="2" width="15%" style="border-bottom: 1px solid #000;">HSN/SAC</th>
                    <th rowspan="2" width="15%" style="border-bottom: 1px solid #000;">Taxable Value</th>
                    <th colspan="2" width="20%" style="border-bottom: 1px solid #000;">OUTPUT CGST</th>
                    <th colspan="2" width="20%" style="border-bottom: 1px solid #000;">OUTPUT SGST</th>
                    <th colspan="2" width="20%" style="border-bottom: 1px solid #000;">IGST</th>
                </tr>
                <tr>
                    <th style="border-bottom:1px solid #000;">(%)</th>
                    <th style="border-bottom:1px solid #000;">Amount</th>
                    <th style="border-bottom:1px solid #000;">(%)</th>
                    <th style="border-bottom:1px solid #000;">Amount</th>
                    <th style="border-bottom:1px solid #000;">(%)</th>
                    <th style="border-bottom:1px solid #000;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @php $i = 1; @endphp
                @foreach($taxSummary as $hsn => $summary)
                    <tr>
                        <td class="text-center">{{ $i++ }}</td>
                        <td class="text-left">{{ $invoice->hsn_sac }}</td>
                        <td class="text-right">{{ number_format($summary['taxable_value'], 2) }}</td>
                        @if(!$invoice->other_state)
                        <td class="text-right">{{ $summary['cgst_rate'] }}</td>
                        <td class="text-right">{{ number_format($summary['cgst_amount'], 2) }}</td>
                        <td class="text-right">{{ $summary['sgst_rate'] }}</td>
                        <td class="text-right">{{ number_format($summary['sgst_amount'], 2) }}</td>
                        <td class="text-center"></td>
                        <td class="text-right"></td>
                        @else
                        <td class="text-center"></td>
                        <td class="text-right"></td>
                        <td class="text-center"></td>
                        <td class="text-right"></td>
                        <td class="text-right">{{ $summary['igst_rate'] }}</td>
                        <td class="text-right">{{ number_format($summary['igst_amount'], 2) }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="border-top: 1px solid #000000;">
                    <td class="text-center">&nbsp;</td>
                    <td class="text-right bold" style="padding-right: 8px;">Total</td>
                    <td class="text-right bold">{{ number_format($invoice->sub_total - ($invoice->discount ?? 0), 2) }}</td>
                    @if(!$invoice->other_state)
                    <td>&nbsp;</td>
                    <td class="text-right bold">{{ number_format($invoice->cgst, 2) }}</td>
                    <td>&nbsp;</td>
                    <td class="text-right bold">{{ number_format($invoice->sgst, 2) }}</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    @else
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="text-right bold">{{ number_format($invoice->igst, 2) }}</td>
                    @endif
                </tr>
                <tr>
                    <td colspan="9" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; padding: 6px; text-align: left;">
                        Amount of Tax(in words) &nbsp;&nbsp;&nbsp;: {{ strtoupper($totalTaxInWords) }}
                    </td>
                </tr>
            </tfoot>
        </table>
        @endif
        @endif
        
        
        <table class="no-border" style="margin-top: 15px; width: 100%;">
            <tr>
                <td width="60%" style="vertical-align: top; padding-left: 4px;">
                    <div style="margin-bottom: 8px; font-size: 10px;">
                        <span>Remarks :</span> {{ $invoice->remarks ?? '' }}
                    </div>
                    <div style="font-weight: bold; font-size: 10px;">Terms & Conditions :</div>
                    <div style="font-size: 9px; line-height: 1.4;">
                        1. Goods once sold will not be taken back or exchange<br>
                        2. Our responsibility ceases on delivery of goods to carriers.<br>
                        3. Cheque or DD only in favour of NACHIAS FASHION PRIVATE LIMITED<br>
                        4. Discount should be deducted from Gross Amount only<br>
                        5. Payment with in 45 days, Subject to Madurai Jurisdiction
                    </div>
                </td>
                <td width="40%" class="text-right" style="vertical-align: bottom;">
                    <br>
                    <div style="border: 2px solid #000; border-radius: 2px; text-align: center; height: 90px; position: relative;">
                        <div style="padding-top: 5px; font-size: 11px;">For Nachias Fashion Private Limited</div>
                        
                        <div style="position: absolute; bottom: 0; width: 100%; border-top: 1px dotted #000; padding: 4px 0; font-size: 10px;">
                            Authorised Signatory
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        @endif
    </div>
    @endforeach
    @if(isset($is_print) && $is_print)
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
    @endif
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Invoice - {{ $invoice->inv_no }}</title>
    <style>
        @page {
            margin: 5px 10px 15px 10px;
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
        $chunks = collect($invoice->items)->chunk(10);
        $totalChunks = $chunks->count();
    @endphp
    @foreach($chunks as $chunkIndex => $chunk)
        @php
            $isLastChunk = ($chunkIndex == $totalChunks - 1);
        @endphp
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
    <div class="container" style="{{ ($index < count($copies) - 1) || !$isLastChunk ? 'page-break-after: always;' : '' }}">
        <div style="position: relative;">
            <div style="position: absolute; right: 0; top: 0; text-align: right; width: 100px; z-index: 10;">
                <div style="font-weight: bold; font-size: 14px;">{{ $copyLabel }}</div>
                @if($einvoiceQr)
                    <img src="{{ $einvoiceQr }}" class="qr-code" style="max-width: 100px; margin-top: 5px;">
                @endif
            </div>
            <table class="header-table" style="width: 100%;">
                <tr>
                    <td width="70%">
                        <table style="border: none;">
                            <tr>
                                <td style="border: none; vertical-align: top; width:30%;">
                                    @if($logoBase64)
                                        <img src="{{ $logoBase64 }}" style="width: 220px;">
                                    @else
                                        <img src="{{ isset($is_print) && $is_print ? asset('assets/images/jc_logo.png') : public_path('assets/images/jc_logo.png') }}" style="width: 220px;">
                                    @endif
                                 </td>
                                 <td style="border: none; vertical-align: top; padding-left: 15px; width:75%;" >
                                     <div style="font-size: 12px; line-height: 1.3;">
                                         {{ $setting->address }} 
                                         <table style="width: 100%; border-collapse: collapse; margin-top: 2px; font-size: 12px;">
                                            <tr>
                                                <td style="border: none; padding: 0; width: 45px;">Mobile</td>
                                                <td style="border: none; padding: 0; width: 10px;">:</td>
                                                <td style="border: none; padding: 0;">{!! implode(', ', array_map('trim', explode(',', $setting->toll_free_no ?? ''))) !!}</td>
                                            </tr>
                                            <tr>
                                                <td style="border: none; padding: 0;">Email</td>
                                                <td style="border: none; padding: 0;">:</td>
                                                <td style="border: none; padding: 0; white-space: nowrap;">{!! implode(', ', array_map('trim', explode(',', $setting->email ?? ''))) !!}</td>
                                            </tr>
                                            <tr>
                                                <td style="border: none; padding: 0;">GSTIN</td>
                                                <td style="border: none; padding: 0;">:</td>
                                                <td style="border: none; padding: 0;">{{ $setting->gst_no ?? '' }}</td>
                                            </tr>
                                         </table>
                                     </div>
                                 </td>
                             </tr>
                         </table>
                     </td>
                     <td width="30%">&nbsp;</td>
                </tr>
            </table>
        </div>
        <div class="header-title">Tax Invoice</div>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td width="50%" style="padding: 0; vertical-align: top; border-right: 1px solid #000; border-bottom: 1px solid #000;">
                    <table class="no-border compact-details" style="margin: 0; width: 100%; font-size:12px;">
                        <tr>
                            <td width="10%">Bill To</td>
                            <td width="5%">:</td>
                            <td>
                                <span>{{ $invoice->customer->name ?? 'N/A' }}</span><br>
                                {{ strtoupper($invoice->customer->address_line_1 ?? '') }}<br>
                                {{ strtoupper($invoice->customer->city->city_name ?? '') }}{{ $invoice->customer->zip_code ? '-' . $invoice->customer->zip_code : '' }}<br>
                                {{ $invoice->customer->mobile_no ?? ''}}
                            </td>
                        </tr>   
                        <tr>
                            <td width="20%">State</td>
                            <td width="5%">:</td>
                            <td width="75%">{{ $invoice->customer->state->state_name ?? 'N/A' }}({{ $invoice->customer->state->state_code ?? '' }})</td>
                        </tr>
                        <tr>
                            <td width="20%">GSTIN</td>
                            <td width="5%">:</td>
                            <td width="75%">{{ $invoice->customer->gst_no ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </td>
                <td width="50%" style="padding: 0; vertical-align: top; border-bottom: 1px solid #000;">
                    <table class="no-border" style="margin: 0; width: 100%; font-size:12px;">
                        <tr>
                            <td width="30%">Invoice No.</td>
                            <td width="5%">:</td>
                            <td width="65%">{{ $invoice->inv_no }}</td>
                        </tr>
                        <tr>
                            <td>Invoice Date</td>
                            <td>:</td>
                            <td>{{ $invoice->inv_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td>Order No.</td>
                            <td>:</td>
                            <td>{{ $invoice->salesOrder->so_no ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td>Destination</td>
                            <td>:</td>
                            <td>{{ $invoice->customer->city->city_name ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding: 0; vertical-align: top; border-right: 1px solid #000; border-bottom: 1px solid #000;">
                    <table class="no-border compact-details" style="margin: 0; width: 100%; font-size:12px;">
                        <tr>
                            <td width="20%">Ship To</td>
                            <td width="5%">:</td>
                            <td>
                                <span>{{ $invoice->customer->name ?? 'N/A' }}</span><br>
                                @if($invoice->delivery_address)
                                    {!! nl2br(e(mb_strtoupper(\App\Models\SalesInvoice::cleanAddress($invoice->delivery_address ?? ''), 'UTF-8'))) !!}
                                @else
                                   {!! nl2br(e(mb_strtoupper(\App\Models\SalesInvoice::cleanAddress($invoice->customer->address ?? ''), 'UTF-8'))) !!}
                                    {{ $invoice->customer->city->city_name ?? '' }}{{ $invoice->customer->zip_code ? '-' . $invoice->customer->zip_code : '' }}
                                @endif <br>
                                {{ $invoice->customer->mobile_no ?? ''}}
                            </td>
                        </tr>
                        <tr>
                            <td width="20%">State</td>
                            <td width="5%">:</td>
                            <td width="75%">{{ $invoice->customer->state->state_name ?? 'N/A' }}({{ $invoice->customer->state->state_code ?? '' }})</td>
                        </tr>
                        <tr>
                            <td width="20%">GSTIN/UIN</td>
                            <td width="5%">:</td>
                            <td width="75%">{{ $invoice->customer->gst_no ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </td>
                <td width="50%" style="padding: 0; vertical-align: top;">
                    <table class="no-border" style="margin: 0; width: 100%; font-size: 12px;">
                        <tr>
                            <td width="30%">Transport</td>
                            <td width="5%">:</td>
                            <td width="65%">{{ $invoice->salesOrder->transporter_name ?? '' }}</td>
                        </tr>
                        <tr>
                            <td>Doc No.</td>
                            <td>:</td>
                            <td>{{ $invoice->inv_no }}</td>
                        </tr>
                        <tr>
                            <td>Sales Group</td>
                            <td>:</td>
                            <td>{{ is_object($invoice->customer->zone) || is_array($invoice->customer->zone) ? ($invoice->customer->zone->zone_name ?? $invoice->customer->zone['zone_name'] ?? 'N/A') : (is_string($invoice->customer->zone) ? json_decode($invoice->customer->zone)->zone_name ?? $invoice->customer->zone : 'N/A') }}</td>
                        </tr>
                        <tr>
                            <td>Sales Executive</td>
                            <td>:</td>
                            <td>{{ $invoice->salesOrder->salesAgent->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td>Total Pkgs</td>
                            <td>:</td>
                            <td>{{ count($invoice->items) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
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
                @foreach($chunk as $itemIndex => $item)
                    <tr>
                        <td class="text-center">{{ $chunkIndex * 10 + $loop->iteration }}</td>
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
                        <td class="text-center">{{ $item->art_no }}</td>
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
                @for($i = $chunk->count(); $i < 10; $i++)
                <tr>
                    <td style="height: 15px;">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    @if($showMrp)<td>&nbsp;</td>@endif
                    @if($showPrice)<td>&nbsp;</td>@endif
                    @if($showAmount)<td>&nbsp;</td>@endif
                </tr>
                @endfor
            </tbody>
            @if($isLastChunk)
            <tfoot>
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
            </tfoot>
            <tbody>
                <tr style="border-top: 1px solid #000000;">
                    <!-- Left Side (IRN + Bank + UPI) in a nested table to ensure borders join edge-to-edge -->
                    <td colspan="6" style="width: {{ $w1_6 }}%; padding: 0; vertical-align: top; border-right: 1px solid #000000; border-bottom: 1px solid #000000; border-top: 1px solid #000000;">
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
                                            <img src="{{ $qrBase64 }}" style="max-width: 100px; margin-top: 4px;">
                                        @else
                                        <img src="{{ isset($is_print) && $is_print ? asset('assets/images/qr_code.png') : public_path('assets/images/qr_code.png') }}" style="max-width: 100px; margin-top: 4px;">
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <!-- Right Side Labels -->
                    <td colspan="{{ $colsAfterQty }}" style="width: {{ $w_colsAfterQty }}%; padding: 0; vertical-align: top; border-bottom: 1px solid #000000; border-top: 1px solid #000000; border-right: 1px solid #000000;">
                        <table style="width: 100%; border-collapse: collapse; margin: 0; border: none;">
                            @if($showDiscount && isset($invoice->discount) && $invoice->discount > 0)
                            @php
                                $effectiveDiscountPercent = $invoice->discount_percent > 0 ? (float)$invoice->discount_percent : ($invoice->sub_total > 0 ? round(($invoice->discount / $invoice->sub_total) * 100, 2) : 0);
                            @endphp
                            <tr><td style="border: none; padding: 2px 4px; text-align: right;">Discount({{ rtrim(rtrim(number_format($effectiveDiscountPercent, 2), '0'), '.') }}%)</td></tr>
                            @endif
                            @if($showSubTotal)
                            <tr><td style="border: none; padding: 2px 4px; text-align: right;">Taxable Value</td></tr>
                            @endif
                            @if($showTax)
                                @if(!$invoice->other_state)
                                <tr><td style="border: none; padding: 2px 4px; text-align: right;">OUTPUT CGST</td></tr>
                                <tr><td style="border: none; padding: 2px 4px; text-align: right;">OUTPUT SGST</td></tr>
                                @else
                                <tr><td style="border: none; padding: 2px 4px; text-align: right;">OUTPUT IGST</td></tr>
                                @endif
                            @endif
                            <tr><td style="border: none; padding: 2px 4px; text-align: right;">Round Off</td></tr>
                        </table>
                    </td>
                    <!-- Right Side Amounts -->
                    <td colspan="1" style="width: {{ $colWidths['amount'] }}%; padding: 0; vertical-align: top; border-bottom: 1px solid #000000; border-top: 1px solid #000000;">
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
                            <tr><td style="border: none; padding: 2px 4px; text-align: right;">{{ (in_array(strtolower($invoice->round_off_type ?? ''), ['less', 'minus']) ? ' - ' : '') . number_format($invoice->round_off ?? 0, 2) }}</td></tr>
                        </table>
                    </td>
                </tr>
                @if($showGrandTotal)
                <tr>
                    <td colspan="6" style="padding: 8px; border-right: none; border-bottom: 1px solid #000000; border-top: 1px solid #000000;">
                        Rupees &nbsp;&nbsp;&nbsp;: {{ strtoupper($totalInWords) }}
                    </td>
                    <td colspan="{{ $colsAfterQty }}" style="padding: 4px; font-weight: bold; text-align: right; border-right: 1px solid #000000; border-top: 1px solid #000000; border-bottom: 1px solid #000000;">
                        Total
                    </td>
                    <td colspan="1" style="padding: 4px; font-weight: bold; text-align: right; border-top: 1px solid #000000; border-bottom: 1px solid #000000;">
                        {{ number_format($invoice->grand_total, 2) }}
                    </td>
                </tr>
                @endif
            </tbody>
            @endif
        </table>
        @if($isLastChunk)
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
        @if(!$isLastChunk)
        <div style="text-align: right; padding: 10px; font-weight: bold; font-size: 13px;">
            Continue to Page No. {{ $chunkIndex + 2 }}
        </div>
        @endif
        @if($isLastChunk)
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

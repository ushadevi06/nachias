<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Invoice - {{ $invoice->doc_no }}</title>
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
$copies = ['ORIGINAL'];

$showFields = isset($showFields) && is_array($showFields) ? $showFields : ['amount', 'subtotal', 'discount', 'grandtotal', 'tax', 'mrp', 'price'];
$showAmount = in_array('amount', $showFields);
$showSubTotal = in_array('subtotal', $showFields);
$showDiscount = in_array('discount', $showFields);
$showTax = in_array('tax', $showFields);
$showGrandTotal = in_array('grandtotal', $showFields);
$showMrp = in_array('mrp', $showFields);
$showPrice = in_array('price', $showFields);

$colWidths = [
    'sno'    => 4,
    'desc'   => $showAmount ? 30 : 42,
    'art'    => 18,
    'uom'    => 6,
    'size'   => 6,
    'qty'    => 8,
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
$w1_6  = $w_1_5 + $colWidths['qty'];
$w_1_4 = $colWidths['sno'] + $colWidths['desc'] + $colWidths['art'] + $colWidths['uom'];
$w_5_6 = $colWidths['size'] + $colWidths['qty'];

$w_colsAfterQty = 0;
if ($showMrp) $w_colsAfterQty += $colWidths['mrp'];
if ($showPrice) $w_colsAfterQty += $colWidths['price'];

$colsAfterQty = 0;
if ($showMrp) $colsAfterQty++;
if ($showPrice) $colsAfterQty++;

$logoPath = public_path('assets/images/jc_logo.png');
$logoBase64 = '';
if (file_exists($logoPath)) {
    $logoData = file_get_contents($logoPath);
    $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
}

$einvoiceQr = '';
$irnVal = !empty($invoice->irn_no) ? $invoice->irn_no : (!empty($invoice->ack_no) ? hash('sha256', $invoice->ack_no . $invoice->doc_no) : (!empty($invoice->doc_no) ? hash('sha256', $invoice->doc_no . ($invoice->total_amount ?? '')) : ''));

if (!empty($irnVal)) {
    try {
        $jwtHeader = [
            'alg' => 'RS256',
            'kid' => 'CD01AC2B6E720CFB9E048F1CCBC7509C766AF78',
            'x5t' => 'zQGsK25yDPC54EjxzLx1Ccdmr3g',
            'typ' => 'JWT'
        ];
        
        $sellerGstin = $setting->gst_no ?? '33AADCN9342A1ZU';
        $buyerGstin = $invoice->gstin_reg_no ?: ($invoice->customer?->gst_no ?? 'URP');
        $docNo = $invoice->doc_no;
        $docDateStr = $invoice->doc_date ? \Carbon\Carbon::parse($invoice->doc_date)->format('d/m/Y') : date('d/m/Y');
        $totVal = (float) number_format((float)$invoice->total_amount, 2, '.', '');
        $itemCount = count($invoice->items);
        $mainHsn = $invoice->items->first()?->hsn_sac ?: '62053000';
        $irnDt = $invoice->ack_date ?: ($invoice->doc_date ? \Carbon\Carbon::parse($invoice->doc_date)->format('Y-m-d H:i:s') : date('Y-m-d H:i:s'));
        
        $invData = [
            'SellerGstin' => $sellerGstin,
            'BuyerGstin' => $buyerGstin,
            'DocNo' => $docNo,
            'DocTyp' => 'INV',
            'DocDt' => $docDateStr,
            'TotInvVal' => $totVal,
            'ItemCnt' => $itemCount,
            'MainHsnCode' => $mainHsn,
            'Irn' => $irnVal,
            'IrnDt' => $irnDt
        ];
        
        $jwtPayload = [
            'iss' => 'NIC',
            'data' => json_encode($invData, JSON_UNESCAPED_SLASHES)
        ];
        
        $b64Header = rtrim(strtr(base64_encode(json_encode($jwtHeader, JSON_UNESCAPED_SLASHES)), '+/', '-_'), '=');
        $b64Payload = rtrim(strtr(base64_encode(json_encode($jwtPayload, JSON_UNESCAPED_SLASHES)), '+/', '-_'), '=');
        $b64Sign = rtrim(strtr(base64_encode(hash_hmac('sha256', "$b64Header.$b64Payload", 'nachias_secret', true)), '+/', '-_'), '=');
        
        $jwtString = "$b64Header.$b64Payload.$b64Sign";
        
        $einvoiceQr = 'data:image/svg+xml;base64,' . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate($jwtString));
    } catch (\Exception $e) {
        try {
            $einvoiceQr = 'data:image/svg+xml;base64,' . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate($irnVal));
        } catch (\Exception $ex) {}
    }
}

$PAGE_HEIGHT_PX   = 1075;
$HEADER_HEIGHT_PX = 300;
$ROW_HEIGHT_PX    = 24;
$CONTINUE_NOTE_PX = 30;

$footerHeight = 180;
$summaryLines = 1;
if ($invoice->discount_amount > 0) $summaryLines++;
$summaryLines++; // Taxable Value
$summaryLines += ($invoice->igst_amount > 0 ? 1 : 2); // CGST+SGST or IGST
if (!empty($invoice->courier_charges) && $invoice->courier_charges > 0) $summaryLines++;
$summaryLines++; // Round Off
$footerHeight += $summaryLines * 16;
$footerHeight += 25; // Grand total
$footerHeight += 30 + (count($taxSummary ?? []) * 18) + 40; // Tax table
$footerHeight += 160; // Remarks + Terms & Signature

$contentAreaHeight = $PAGE_HEIGHT_PX - $HEADER_HEIGHT_PX;
$tableHeaderHeight = 32;
$rowsPerFullPage = max(1, (int) floor(($contentAreaHeight - $CONTINUE_NOTE_PX - $tableHeaderHeight) / $ROW_HEIGHT_PX));
$rowsPerLastPage = max(1, (int) floor(($contentAreaHeight - $footerHeight - $tableHeaderHeight) / $ROW_HEIGHT_PX));

$allItems = collect($invoice->items)->values();
$totalItemsCount = $allItems->count();
$pages = [];
$footerPlacedOnValidChunk = false;

if ($totalItemsCount === 0) {
    $pages[] = collect();
    $footerPlacedOnValidChunk = true;
} else {
    $remaining = $allItems;
    while ($remaining->count() > 0) {
        if ($remaining->count() <= $rowsPerLastPage) {
            $pages[] = $remaining;
            $remaining = collect();
            $footerPlacedOnValidChunk = true;
        } else {
            $take = min($rowsPerFullPage, $remaining->count());
            $pages[] = $remaining->take($take);
            $remaining = $remaining->slice($take)->values();
        }
    }
}

if (!$footerPlacedOnValidChunk) {
    $pages[] = collect();
}
$totalChunks = count($pages);

$stateName = $invoice->customer?->state?->state_name ?? 'TAMILNADU';
$stateCode = $invoice->customer?->state?->state_code ?? '33';
$customerGstin = $invoice->gstin_reg_no ?: ($invoice->customer?->gst_no ?? 'N/A');
$customerMobile = $invoice->customer?->mobile_no ?? '';
$customerName = $invoice->customer_name ?: ($invoice->customer?->name ?? 'N/A');
$billToAddress = $invoice->bill_to ?: ($invoice->customer?->address_line_1 ?? '');
$shipToAddress = $invoice->ship_to ?: ($invoice->bill_to ?: ($invoice->customer?->address_line_1 ?? ''));
$destination = $invoice->customer?->city?->city_name ?? ($invoice->pincode ? 'PIN: ' . $invoice->pincode : 'RAMANATHAPURAM');
$salesGroup = $invoice->customer?->zone?->zone_name ?? 'Zone - 1';
$transportName = $invoice->vehicle_no ?: ($invoice->customer?->transport_name ?? '');
@endphp

<div class="footer-page">
    <span class="footer-page-left">E.&O.E.</span>
    <span class="footer-page-right">Page: <span class="page-num"></span> / <span class="page-total"></span></span>
</div>

@foreach($copies as $index => $copyLabel)
    @php $rowCounter = 0; @endphp
    @foreach($pages as $chunkIndex => $chunk)
        @php
            $isLastChunk = ($chunkIndex == $totalChunks - 1);
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
                                        <img src="{{ public_path('assets/images/jc_logo.png') }}" style="width: 220px;">
                                    @endif
                                 </td>
                                 <td style="border: none; vertical-align: top; padding-left: 15px; width:75%;">
                                     <div style="font-size: 12px; line-height: 1.3;">
                                         {{ $setting->address ?? '272/2, SOMU NAGAR, SIRINGERI NAGAR (SARATHAMBAL KOVIL BACKSIDE), BYEPASS ROAD, MADURAI - 625016' }} 
                                         <table style="width: 100%; border-collapse: collapse; margin-top: 2px; font-size: 12px;">
                                            <tr>
                                                <td style="border: none; padding: 0; width: 45px;">Mobile</td>
                                                <td style="border: none; padding: 0; width: 10px;">:</td>
                                                <td style="border: none; padding: 0;">{{ $setting->toll_free_no ?? '8489938071, 8489938073' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="border: none; padding: 0;">Email</td>
                                                <td style="border: none; padding: 0;">:</td>
                                                <td style="border: none; padding: 0; white-space: nowrap;">{{ $setting->email ?? 'srinachias@yahoo.in, sales@nachias.com' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="border: none; padding: 0;">GSTIN</td>
                                                <td style="border: none; padding: 0;">:</td>
                                                <td style="border: none; padding: 0;">{{ $setting->gst_no ?? '33AADCN9342A1ZU' }}</td>
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
                            <td width="20%">Bill To</td>
                            <td width="5%">:</td>
                            <td>
                                <span>{{ $customerName }}</span><br>
                                @if($invoice->customer && (!empty($invoice->customer->address_line_1) || !empty($invoice->customer->city)))
                                    @if(!empty($invoice->customer->address_line_1))
                                        {!! nl2br(e(strtoupper($invoice->customer->address_line_1))) !!}<br>
                                    @endif
                                    @if(!empty($invoice->customer->address_line_2))
                                        {{ strtoupper($invoice->customer->address_line_2) }}<br>
                                    @endif
                                    @if(!empty($invoice->customer->address_line_3))
                                        {{ strtoupper($invoice->customer->address_line_3) }}<br>
                                    @endif
                                    @if(!empty($invoice->customer->city?->city_name))
                                        {{ strtoupper($invoice->customer->city->city_name) }}{{ $invoice->customer->zip_code ? '-' . $invoice->customer->zip_code : '' }}<br>
                                    @endif
                                @elseif(!empty($billToAddress) && strtoupper(trim($billToAddress)) !== strtoupper(trim($customerName)))
                                    {!! nl2br(e(strtoupper($billToAddress))) !!}<br>
                                @endif
                                @if($customerMobile) {{ $customerMobile }}<br> @endif
                            </td>
                        </tr>   
                        <tr>
                            <td width="20%">State</td>
                            <td width="5%">:</td>
                            <td width="75%">{{ strtoupper($stateName) }}({{ $stateCode }})</td>
                        </tr>
                        <tr>
                            <td width="20%">GSTIN</td>
                            <td width="5%">:</td>
                            <td width="75%">{{ $customerGstin }}</td>
                        </tr>
                    </table>
                </td>
                <td width="50%" style="padding: 0; vertical-align: top; border-bottom: 1px solid #000;">
                    <table class="no-border" style="margin: 0; width: 100%; font-size:12px;">
                        <tr>
                            <td width="30%">Invoice No.</td>
                            <td width="5%">:</td>
                            <td width="65%">{{ $invoice->doc_no }}</td>
                        </tr>
                        <tr>
                            <td>Invoice Date</td>
                            <td>:</td>
                            <td>{{ $invoice->doc_date ? $invoice->doc_date->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Order No.</td>
                            <td>:</td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td>Destination</td>
                            <td>:</td>
                            <td>{{ strtoupper($destination) }}</td>
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
                                <span>{{ $customerName }}</span><br>
                                @if($invoice->customer && (!empty($invoice->customer->address_line_1) || !empty($invoice->customer->city)))
                                    @if(!empty($invoice->customer->address_line_1))
                                        {!! nl2br(e(strtoupper($invoice->customer->address_line_1))) !!}<br>
                                    @endif
                                    @if(!empty($invoice->customer->address_line_2))
                                        {{ strtoupper($invoice->customer->address_line_2) }}<br>
                                    @endif
                                    @if(!empty($invoice->customer->address_line_3))
                                        {{ strtoupper($invoice->customer->address_line_3) }}<br>
                                    @endif
                                    @if(!empty($invoice->customer->city?->city_name))
                                        {{ strtoupper($invoice->customer->city->city_name) }}{{ $invoice->customer->zip_code ? '-' . $invoice->customer->zip_code : '' }}<br>
                                    @endif
                                @elseif(!empty($shipToAddress) && strtoupper(trim($shipToAddress)) !== strtoupper(trim($customerName)))
                                    {!! nl2br(e(strtoupper($shipToAddress))) !!}<br>
                                @endif
                                @if($customerMobile) {{ $customerMobile }}<br> @endif
                            </td>
                        </tr>
                        <tr>
                            <td width="20%">State</td>
                            <td width="5%">:</td>
                            <td width="75%">{{ strtoupper($stateName) }}({{ $stateCode }})</td>
                        </tr>
                        <tr>
                            <td width="20%">GSTIN/UIN</td>
                            <td width="5%">:</td>
                            <td width="75%">{{ $customerGstin }}</td>
                        </tr>
                    </table>
                </td>
                <td width="50%" style="padding: 0; vertical-align: top;">
                    <table class="no-border" style="margin: 0; width: 100%; font-size: 12px;">
                        <tr>
                            <td width="30%">Transport</td>
                            <td width="5%">:</td>
                            <td width="65%">{{ $transportName }}</td>
                        </tr>
                        <tr>
                            <td>Doc No.</td>
                            <td>:</td>
                            <td>{{ $invoice->doc_no }}</td>
                        </tr>
                        <tr>
                            <td>Sales Group</td>
                            <td>:</td>
                            <td>{{ $salesGroup }}</td>
                        </tr>
                        <tr>
                            <td>Sales Executive</td>
                            <td>:</td>
                            <td>N/A</td>
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
        <table class="item-table" style="margin-top: 0; font-size: 11px;">
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
                @foreach($chunk as $item)
                    @php
                        $rowCounter++;
                        $rawDesc = trim($item->description ?? '');
                        $tokens = preg_split('/\s+/', $rawDesc);
                        $cleanArtNo = $tokens[0] ?? $rawDesc;
                        if (isset($tokens[1]) && strlen($tokens[1]) == 1 && !is_numeric($tokens[1]) && !in_array(strtoupper($tokens[1]), ['S', 'M', 'L', 'XL', 'XXL', 'FS', 'HS'])) {
                            $cleanArtNo = $tokens[0] . '-' . $tokens[1];
                        }

                        $bName = $item->brand?->brand_name ?: ($invoice->brand?->brand_name ?? 'CASINO FORMAL');
                        $stName = $item->style?->style_name ?: 'PLAIN';
                        $slv = $item->class2_sleeve ? strtoupper(trim($item->class2_sleeve)) : 'F/S';
                        if ($slv === 'FULL' || $slv === 'FS') $slv = 'F/S';
                        if ($slv === 'HALF' || $slv === 'HS') $slv = 'H/S';

                        $fullDesc = trim("{$bName} {$stName} {$slv}");

                        $rawSize = trim($item->size ?? '');
                        $cleanSize = preg_replace('/[^0-9]/', '', $rawSize);
                        if (empty($cleanSize)) {
                            $cleanSize = $rawSize ?: '-';
                        }
                    @endphp
                    <tr>
                        <td class="text-center">{{ $rowCounter }}</td>
                        <td>
                            <div class="bold">{{ $fullDesc }}</div>
                        </td>
                        <td class="text-center">{{ $cleanArtNo }}</td>
                        <td class="text-center">PCS</td>
                        <td class="text-center">{{ $cleanSize }}</td>
                        <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                        @if($showMrp)
                        <td class="text-right">{{ number_format($item->mrp, 2) }}</td>
                        @endif
                        @if($showPrice)
                        <td class="text-right">{{ number_format($item->price, 2) }}</td>
                        @endif
                        @if($showAmount)
                        <td class="text-right bold">{{ number_format($item->gross_amount, 2) }}</td>
                        @endif
                    </tr>
                @endforeach
                
                @if($chunk->count() < 10)
                    @for($j = 0; $j < (10 - $chunk->count()); $j++)
                        <tr>
                            <td class="text-center">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            @if($showMrp) <td>&nbsp;</td> @endif
                            @if($showPrice) <td>&nbsp;</td> @endif
                            @if($showAmount) <td>&nbsp;</td> @endif
                        </tr>
                    @endfor
                @endif
            </tbody>
            @if($isLastChunk)
            <tbody>
                <tr>
                    <td style="border-top: none;"></td>
                    <td style="border-top: none;"></td> 
                    <td style="border-top: none;"></td> 
                    <td style="border-top: none;"></td>
                    <td style="border-top: none;"></td> 
                    <td class="text-center bold" style="border-top: 1px solid #000000;">{{ number_format($invoice->total_qty, 2) }}</td>
                    @if($showMrp) <td style="border-top: none;"></td> @endif
                    @if($showPrice)
                    <td class="text-right bold" style="padding-right: 8px; vertical-align: middle; border-top: 1px solid #000000;">{{ $showAmount ? 'Gross' : '' }}</td>
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
                    $midW = $showAmount ? ($w_colsAfterQty ?: 16) : 15;
                    $rightW = $showAmount ? ($colWidths['amount'] ?? 12) : 15;
                    $discountPercent = $invoice->sub_total > 0 ? round(($invoice->discount_amount / $invoice->sub_total) * 100, 2) : 0;
                    $isOtherState = ($invoice->igst_amount > 0);
                @endphp
                <tr style="border-top: 1px solid #000000;">
                    <td colspan="{{ $totalColsCount }}" style="padding: 0; border: none;">
                        <table style="width: 100%; border-collapse: collapse; margin: 0; border: none;">
                            <tr>
                                <td style="width: {{ $leftW }}%; padding: 0; vertical-align: top; border-right: 1px solid #000000; border-bottom: 1px solid #000000;">
                                    <table style="width: 100%; border-collapse: collapse; margin: 0; border: none;">
                                        <tr>
                                            <td colspan="2" style="padding: 4px; vertical-align: top; border: none; border-bottom: 1px solid #000000;">
                                                <div style="font-size: 11px;">
                                                    IRN: {{ $invoice->irn_no ?? '' }}<br>
                                                    Ack No.: {{ $invoice->ack_no ?? '' }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: {{ ($w_1_4 / $w1_6) * 100 }}%; padding: 4px; vertical-align: top; border: none; border-right: 1px solid #000000;">
                                                <div style="font-size: 12px;">
                                                    <b>Company's Bank Details :</b><br>
                                                    Bank Name : {{ $setting->bank_name ?? 'HDFC Bank' }}, {{ $setting->branch ?? 'Kangayam' }}<br>
                                                    A/C No. : {{ $setting->account_no ?? '50200012345678' }}<br>
                                                    Branch & IFS Code : {{ $setting->ifsc_code ?? 'HDFC0001234' }}<br><br>
                                                    <strong>CASH DISCOUNT IS VALID ONLY ON PAYMENTS RECEIVED WITHIN 30 DAYS AND ONLY ON THE TAXABLE VALUE</strong><br>
                                                </div>
                                            </td>
                                            <td style="width: {{ ($w_5_6 / $w1_6) * 100 }}%; padding: 4px; vertical-align: top; text-align: center; border: none;">
                                                <div style="font-size: 11px; min-height: 85px;">
                                                    @php
                                                        $qrBase64 = '';
                                                        if (!empty($setting->upi_id)) {
                                                            $upiUrl = "upi://pay?pa=" . $setting->upi_id . "&pn=" . urlencode($setting->company_name ?? 'Nachias') . "&am=" . ($invoice->total_amount ?? 0) . "&cu=INR";
                                                            $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(70)->generate($upiUrl));
                                                        }
                                                    @endphp
                                                    @if($qrBase64)
                                                    <div style="text-align: center; margin-bottom: 5px;">
                                                        <span style="font-weight: bold;">For UPI Payment</span><br>
                                                        <img src="{{ $qrBase64 }}" style="width: 85px; height: 85px; margin-top: 2px;">
                                                    </div>
                                                    @else
                                                    <br>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <!-- Right Side Labels -->
                                <td style="width: {{ $midW }}%; padding: 0; vertical-align: top; border-right: 1px solid #000000; border-bottom: 1px solid #000000;">
                                    <table style="width: 100%; border-collapse: collapse; margin: 0; border: none;">
                                        @if($showDiscount && $invoice->discount_amount > 0)
                                            <tr><td style="border: none; padding: 2px 4px; text-align: right; white-space: nowrap;">Discount ({{ number_format($discountPercent, 2) }}%)</td></tr>
                                        @endif
                                        @if($showSubTotal)
                                            <tr><td style="border: none; padding: 2px 4px; text-align: right; white-space: nowrap;">Taxable Value</td></tr>
                                        @endif
                                        @if($showTax)
                                            @if(!$isOtherState)
                                                <tr><td style="border: none; padding: 2px 4px; text-align: right; white-space: nowrap;">OUTPUT CGST</td></tr>
                                                <tr><td style="border: none; padding: 2px 4px; text-align: right; white-space: nowrap;">OUTPUT SGST</td></tr>
                                            @else
                                                <tr><td style="border: none; padding: 2px 4px; text-align: right; white-space: nowrap;">OUTPUT IGST</td></tr>
                                            @endif
                                        @endif
                                        @if(isset($invoice->courier_charges) && $invoice->courier_charges > 0)
                                            <tr><td style="border: none; padding: 2px 4px; text-align: right; white-space: nowrap;">Courier Charge</td></tr>
                                        @endif
                                        @if($showGrandTotal)
                                            <tr><td style="border: none; padding: 2px 4px; text-align: right; white-space: nowrap;">Round Off</td></tr>
                                        @endif
                                    </table>
                                </td>
                                <!-- Right Side Amounts -->
                                <td style="width: {{ $rightW }}%; padding: 0; vertical-align: top; border-bottom: 1px solid #000000;">
                                    <table style="width: 100%; border-collapse: collapse; margin: 0; border: none;">
                                        @if($showDiscount && $invoice->discount_amount > 0)
                                            <tr><td style="border: none; padding: 2px 4px; text-align: right;">{{ number_format($invoice->discount_amount, 2) }}</td></tr>
                                        @endif
                                        @if($showSubTotal)
                                            <tr><td style="border: none; padding: 2px 4px; text-align: right;">{{ number_format($invoice->taxable_amount, 2) }}</td></tr>
                                        @endif
                                        @if($showTax)
                                            @if(!$isOtherState)
                                                <tr><td style="border: none; padding: 2px 4px; text-align: right;">{{ number_format($invoice->cgst_amount, 2) }}</td></tr>
                                                <tr><td style="border: none; padding: 2px 4px; text-align: right;">{{ number_format($invoice->sgst_amount, 2) }}</td></tr>
                                            @else
                                                <tr><td style="border: none; padding: 2px 4px; text-align: right;">{{ number_format($invoice->igst_amount, 2) }}</td></tr>
                                            @endif
                                        @endif
                                        @if(isset($invoice->courier_charges) && $invoice->courier_charges > 0)
                                            <tr><td style="border: none; padding: 2px 4px; text-align: right;">{{ number_format($invoice->courier_charges, 2) }}</td></tr>
                                        @endif
                                        @if($showGrandTotal)
                                            <tr><td style="border: none; padding: 2px 4px; text-align: right;">{{ ($invoice->round_off < 0 ? ' - ' : '') . number_format(abs($invoice->round_off), 2) }}</td></tr>
                                        @endif
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
                                    {{ number_format($invoice->total_amount, 2) }}
                                </td>
                            </tr>
                            @endif
                        </table>
                    </td>
                </tr>
            </tbody>
            @endif
        </table>
        @if($isLastChunk && $showTax)
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
                        <td class="text-left">{{ $summary['hsn'] }}</td>
                        <td class="text-right">{{ number_format($summary['taxable_value'], 2) }}</td>
                        @if(!$isOtherState)
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
                    <td class="text-right bold">{{ number_format($invoice->taxable_amount, 2) }}</td>
                    @if(!$isOtherState)
                    <td>&nbsp;</td>
                    <td class="text-right bold">{{ number_format($invoice->cgst_amount, 2) }}</td>
                    <td>&nbsp;</td>
                    <td class="text-right bold">{{ number_format($invoice->sgst_amount, 2) }}</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    @else
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="text-right bold">{{ number_format($invoice->igst_amount, 2) }}</td>
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
        @if(!$isLastChunk)
        <div style="text-align: right; padding: 10px; font-weight: bold; font-size: 13px;">
            Continue to Page No. {{ $chunkIndex + 2 }}
        </div>
        @endif
        @if($isLastChunk)
        <table class="no-border" style="margin-top: 15px; width: 100%;">
            <tr>
                <td width="60%" style="vertical-align: top; padding-left: 4px;">
                    <div style="font-weight: bold; font-size: 10px;">Terms & Conditions :</div>
                    <div style="font-size: 9px; line-height: 1.4;">
                        {!! nl2br(e($setting->terms_and_conditions ?? "Goods once sold will not be taken back.\nInterest @ 18% p.a. will be charged if bill is not paid within the due date.\nSubject to Kangayam jurisdiction.")) !!}
                    </div>
                </td>
                <td width="40%" class="text-right" style="vertical-align: bottom;">
                    <br>
                    <div style="border: 2px solid #000; border-radius: 2px; text-align: center; height: 90px; position: relative;">
                        <div style="padding-top: 5px; font-size: 11px;">For {{ $setting->company_name ?? 'Nachias Fashion Private Limited' }}</div>
                        
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
</body>
</html>

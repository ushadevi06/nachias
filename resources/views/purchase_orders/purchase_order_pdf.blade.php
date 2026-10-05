<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Purchase Order - {{ $purchaseOrder->po_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20px 10px 15px 10px;
        }

        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 11px;
            color: #000000;
            margin: 0;
            padding: 0;
            line-height: 1.2;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        strong, .bold, th {
            font-weight: bold;
        }

        .container {
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

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

        .details-box {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
            margin-bottom: 3px;
        }

        .details-box td {
            vertical-align: top;
            font-size: 12px;
            line-height: 1.25;
            border: none;
            padding: 0;
        }

        .details-inner-table {
            width: 100%;
            border: none;
            border-collapse: collapse;
            margin: 0;
        }

        .details-inner-table td {
            border: none !important;
            padding: 1px 2px !important;
            vertical-align: top;
            font-size: 12px;
            line-height: 1.25;
            color: #000000;
        }

        .item-table {
            border-bottom: 1px solid #000;
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
            word-wrap: break-word;
        }

        .item-table th {
            background-color: #a3a3a3;
            text-align: center;
            font-weight: bold;
            border: 1px solid #000;
            padding: 3px 2px;
            font-size: 8.5px;
            color: #000000;
        }

        .item-table tbody td {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-top: none;
            border-bottom: none;
            padding: 1px 2px;
            vertical-align: middle;
            font-size: 8.5px;
            height: 46px;
            color: #000000;
        }

        .item-row {
            height: 46px;
        }

        .item-row td {
            height: 46px;
            vertical-align: middle;
        }

        .filler-row {
            height: 46px;
        }

        .filler-row td {
            height: 46px;
            padding: 0 2.5px;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-top: none;
            border-bottom: none;
        }

        .item-table .total-row td {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            height: auto;
            padding: 3px 2px;
            font-size: 8.5px;
            color: #000000;
        }

        .summary-box-table {
            width: 100%;
            border: 1px solid #000;
            border-top: none;
            border-collapse: collapse;
            margin-top: 0;
        }

        .summary-inner-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin: 0;
        }

        .summary-inner-table tr {
            height: auto;
        }

        .summary-inner-table td {
            border: none !important;
            padding: 2px 4px !important;
            font-size: 11.5px !important;
            line-height: 1.35 !important;
            height: auto !important;
            vertical-align: middle;
            color: #000000;
        }

        .no-border th,
        .no-border td {
            border: none !important;
            padding: 1px 1px;
            margin: 0;
        }

        .text-center {
            text-align: center !important;
        }

        .text-right {
            text-align: right !important;
        }

        .bold {
            font-weight: bold;
        }

        .no-wrap {
            white-space: nowrap !important;
        }

        .img-container {
            width: 44px;
            height: 44px;
            line-height: 44px;
            text-align: center;
            vertical-align: middle;
            margin: 0 auto;
            overflow: hidden;
        }

        .img-container img {
            max-width: 44px;
            max-height: 44px;
            display: inline-block;
            vertical-align: middle;
            border: 1px solid #ccc;
        }

        .amount-words {
            border-top: 1px solid #000;
            padding: 0;
        }
    </style>
</head>

<body>
    @php
        $allItems = collect($purchaseOrder->items);
        $totalItemCount = $allItems->count();

        $isAccessoriesStore = $purchaseOrder->storeType && (
            stripos($purchaseOrder->storeType->store_type_name, 'ACCESSORIES') !== false
            || $purchaseOrder->store_type_id == 2
        );
        $isFabricStore = !$isAccessoriesStore;
        $isOtherState = (bool)($purchaseOrder->other_state ?? false)
            || in_array(strtolower((string)($purchaseOrder->other_state ?? '')), ['yes', 'y', '1', 'true'], true)
            || ($purchaseOrder->items->sum('igst_amount') > 0)
            || ($purchaseOrder->items->sum('igst_percent') > 0);
        $totalCols = $isFabricStore ? 14 : ($isOtherState ? 11 : 13);

        $rowHeight = 46;

        // Header height calculation (compact)
        $s = $purchaseOrder->supplier;
        $addrParts = $s ? array_filter([$s->address_line_1 ?? null, $s->address_line_2 ?? null, $s->address_line_3 ?? null]) : [];
        $addrLineCount = count($addrParts);
        $addrText = implode(' ', $addrParts);
        $extraAddrLines = max(0, $addrLineCount - 1) + (strlen($addrText) > 45 ? (int)floor(strlen($addrText) / 45) : 0);
        
        $thHeight = ($isAccessoriesStore && !$isOtherState) ? 28 : 22;
        $headerHeight = 175 + ($extraAddrLines * 12) + $thHeight;

        // Summary lines count
        $summaryLines = 3; // Total Qty, Sub Total, Taxable Amount
        if (($purchaseOrder->discount_amount ?? 0) > 0) $summaryLines++;
        if (($purchaseOrder->commission ?? 0) > 0) $summaryLines++;
        if ($isAccessoriesStore) {
            if ($isOtherState) {
                $summaryLines += 1;
            } else {
                $summaryLines += 3;
            }
        } else {
            if ($isOtherState) {
                $summaryLines += 1;
            } else {
                $summaryLines += 2;
            }
        }
        if (($purchaseOrder->round_off ?? 0) != 0 || !empty($purchaseOrder->round_off_type)) $summaryLines++;
        $summaryLines++; // Grand Total

        $remarksLen = strlen($purchaseOrder->remarks ?? '');
        $remarksLines = !empty($purchaseOrder->remarks) ? max(1, (int)ceil($remarksLen / 40)) : 0;
        
        $paymentTermsLen = strlen($purchaseOrder->payment_terms ?? '');
        $paymentLines = !empty($purchaseOrder->payment_terms) ? max(1, (int)ceil($paymentTermsLen / 35)) : 0;

        $summaryBoxHeight = max(24 * $summaryLines * 0.85 + 15, 50 + ($remarksLines * 15));
        $signatoryHeight = 75 + (max(0, $paymentLines - 1) * 14);
        $footerHeight = 20 + $summaryBoxHeight + $signatoryHeight + 15;

        $continueNoteHeight = 22;

        $PAGE_HEIGHT = 1040;

        $rowsPerFullPage = max(1, (int) floor(($PAGE_HEIGHT - $headerHeight - $continueNoteHeight) / $rowHeight));
        $rowsPerLastPage = max(1, (int) floor(($PAGE_HEIGHT - $headerHeight - $footerHeight) / $rowHeight));

        $rowsPerFullPage = min(14, $rowsPerFullPage);
        $rowsPerLastPage = min(($isAccessoriesStore && !$isOtherState ? 10 : 11), $rowsPerLastPage);

        $pages = [];
        $remaining = collect($allItems);

        if ($totalItemCount === 0) {
            $pages[] = [
                'items' => collect(),
                'fillerRows' => $rowsPerLastPage
            ];
        } elseif ($totalItemCount <= $rowsPerLastPage) {
            $pages[] = [
                'items' => $remaining,
                'fillerRows' => max(0, $rowsPerLastPage - $totalItemCount)
            ];
        } else {
            while ($remaining->count() > 0) {
                $remCount = $remaining->count();

                if ($remCount <= $rowsPerLastPage) {
                    $pages[] = [
                        'items' => $remaining,
                        'fillerRows' => max(0, $rowsPerLastPage - $remCount)
                    ];
                    break;
                }

                if ($remCount <= $rowsPerFullPage + $rowsPerLastPage) {
                    $take = min($rowsPerFullPage, $remCount - 1);
                    if ($remCount - $take > $rowsPerLastPage) {
                        $take = $remCount - $rowsPerLastPage;
                    }
                } else {
                    $take = $rowsPerFullPage;
                }

                $pages[] = [
                    'items' => $remaining->take($take),
                    'fillerRows' => 0
                ];
                $remaining = $remaining->slice($take)->values();
            }
        }

        $totalPages = count($pages);
        $globalIndex = 0;

        $logoBase64 = '';
        if (isset($setting) && !empty($setting->logo)) {
            $uploadedLogoPath = public_path('uploads/logo/' . $setting->logo);
            if (file_exists($uploadedLogoPath)) {
                $logoData = file_get_contents($uploadedLogoPath);
                $logoBase64 = 'data:image/' . pathinfo($uploadedLogoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode($logoData);
            }
        }
        if (!$logoBase64) {
            $defaultLogo = public_path('assets/images/jc_logo.png');
            if (file_exists($defaultLogo)) {
                $logoData = file_get_contents($defaultLogo);
                $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
            }
        }
    @endphp

    @foreach($pages as $pageIndex => $pageData)
    @php
        $chunk = $pageData['items'];
        $fillerRows = $pageData['fillerRows'];
        $isLastPage = ($pageIndex === $totalPages - 1);
    @endphp

    <div class="container" style="{{ !$isLastPage ? 'page-break-after: always;' : '' }}">
        <!-- 1. Company Header Table -->
        <table class="header-table" style="width: 100%; border: none; margin-bottom: 2px;">
            <tr>
                <td width="70%">
                    <table style="border: none;">
                        <tr>
                            <td style="border: none; vertical-align: top; width: 30%;">
                                @if($logoBase64)
                                    <img src="{{ $logoBase64 }}" style="width: 220px;">
                                @else
                                    <img src="{{ isset($is_print) && $is_print ? asset('assets/images/jc_logo.png') : public_path('assets/images/jc_logo.png') }}" style="width: 220px;">
                                @endif
                            </td>
                            <td style="border: none; vertical-align: top; padding-left: 15px; width: 75%;">
                                <div style="font-size: 12px; line-height: 1.3; color: #000;">
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

        <!-- 2. Purchase Order Title -->
        <div class="header-title">Purchase Order</div>

        <!-- 3. Supplier & PO Details Box -->
        <table class="details-box">
            <tr>
                <td style="width: 55%; border-right: 1px solid #000; padding: 2px 4px; vertical-align: top;">
                    <table class="details-inner-table">
                        <tr>
                            <td style="width: 22%;">Supplier</td>
                            <td style="width: 4%;">:</td>
                            <td><strong>{{ $purchaseOrder->supplier->name ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td>:</td>
                            <td>
                                @php
                                $s = $purchaseOrder->supplier;
                                if ($s) {
                                    $addr = array_filter([$s->address_line_1, $s->address_line_2, $s->address_line_3]);
                                    echo implode(', ', $addr);
                                    if ($s->city) echo ', ' . ($s->city->city_name ?? '');
                                    if ($s->state) echo ' - ' . ($s->state->state_name ?? '');
                                    if ($s->zip_code) echo ' (' . $s->zip_code . ')';
                                } else {
                                    echo '-';
                                }
                                @endphp
                            </td>
                        </tr>
                        <tr>
                            <td>State</td>
                            <td>:</td>
                            <td>{{ $purchaseOrder->supplier->state->state_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>GSTIN</td>
                            <td>:</td>
                            <td>{{ $purchaseOrder->supplier->gst_no ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Order Type</td>
                            <td>:</td>
                            <td>{{ strtoupper($purchaseOrder->order_type ?? '') }}</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 45%; padding: 2px 4px; vertical-align: top;">
                    <table class="details-inner-table">
                        <tr>
                            <td style="width: 30%;">PO No.</td>
                            <td style="width: 4%;">:</td>
                            <td style="width: 66%;"><strong>{{ $purchaseOrder->po_number }}</strong></td>
                        </tr>
                        <tr>
                            <td>PO Date</td>
                            <td>:</td>
                            <td>{{ $purchaseOrder->po_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td>Due Date</td>
                            <td>:</td>
                            <td>{{ $purchaseOrder->due_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td>Ref No.</td>
                            <td>:</td>
                            <td>{{ $purchaseOrder->reference_no ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Ref Date</td>
                            <td>:</td>
                            <td>{{ $purchaseOrder->reference_date ? $purchaseOrder->reference_date->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Agent</td>
                            <td>:</td>
                            <td>{{ $purchaseOrder->agent->name ?? ($purchaseOrder->purchaseCommissionAgent->name ?? '-') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- 4. Items Table -->
        <table class="item-table">
            <thead>
                <tr>
                    @if($isFabricStore)
                        <th width="3.5%">S.No</th>
                        <th width="7.5%">Store Category</th>
                        <th width="7.5%">Brand</th>
                        <th width="10%">Raw Material</th>
                        <th width="7.5%">Style</th>
                        <th width="5%">Fabric Width</th>
                        <th width="6.5%">Fabric Type</th>
                        <th width="7.5%">Supplier Design</th>
                        <th width="6%">Color</th>
                        <th width="3.5%">UOM</th>
                        <th width="7.5%">Quantity</th>
                        <th width="6%">Rate</th>
                        <th width="11%">Amount</th>
                        <th width="10.5%">Image</th>
                    @elseif($isOtherState)
                        <th width="4%">S.No</th>
                        <th width="12%">Store Category</th>
                        <th width="13%">Brand</th>
                        <th width="19%">Raw Material</th>
                        <th width="4.5%">UOM</th>
                        <th width="9%">Quantity</th>
                        <th width="6.5%">Rate</th>
                        <th width="5%">IGST %</th>
                        <th width="9%">IGST Amt</th>
                        <th width="13%">Amount</th>
                        <th width="5%">Image</th>
                    @else
                        <th width="4%">S.No</th>
                        <th width="11%">Store Category</th>
                        <th width="12%">Brand</th>
                        <th width="17%">Raw Material</th>
                        <th width="4%">UOM</th>
                        <th width="8.5%">Quantity</th>
                        <th width="5.5%">Rate</th>
                        <th width="4.5%">CGST %</th>
                        <th width="8%">CGST Amt</th>
                        <th width="4.5%">SGST %</th>
                        <th width="8%">SGST Amt</th>
                        <th width="12%">Amount</th>
                        <th width="5.5%">Image</th>
                    @endif
                </tr>
            </thead>

            <tbody>
                @foreach($chunk as $item)
                @php
                $globalIndex++;
                $imageSrc = '';
                $imgW = null;
                $imgH = null;

                if (!empty($item->attached_file)) {
                    $imagePath = public_path('uploads/purchase_orders/' . $item->attached_file);
                    if (file_exists($imagePath)) {
                        if (isset($is_print) && $is_print) {
                            $imageSrc = url('uploads/purchase_orders/' . $item->attached_file);
                        } else {
                            $imageSrc = $imagePath;
                        }
                        $imgInfo = @getimagesize($imagePath);
                        if ($imgInfo && $imgInfo[0] > 0 && $imgInfo[1] > 0) {
                            $maxDim = 44;
                            $origW = $imgInfo[0];
                            $origH = $imgInfo[1];
                            $scale = min($maxDim / $origW, $maxDim / $origH);
                            $imgW = (int)round($origW * $scale);
                            $imgH = (int)round($origH * $scale);
                        }
                    }
                }
                @endphp

                <tr class="item-row">
                    <td class="text-center no-wrap">{{ $globalIndex }}</td>
                    <td class="text-center">{{ $item->storeCategory->category_name ?? '-' }}</td>
                    <td class="text-center">{{ $item->brand->brand_name ?? '-' }}</td>
                    <td><strong>{{ $item->rawMaterial->name ?? '' }}</strong></td>

                    @if($isFabricStore)
                        <td class="text-center no-wrap">{{ $item->style->style_name ?? '-' }}</td>
                        <td class="text-center no-wrap">{{ $item->fabricWidth->width ?? '-' }}</td>
                        <td class="text-center">{{ $item->fabricType->fabric_type ?? '-' }}</td>
                        <td class="text-center bold">{{ $item->supplier_design_name ?? '-' }}</td>
                        <td class="text-center">{{ $item->color->color_name ?? '-' }}</td>
                    @endif

                    <td class="text-center no-wrap">{{ $item->uom->uom_code ?? '-' }}</td>
                    <td class="text-center no-wrap">{{ number_format($item->quantity, 2) }}</td>
                    <td class="text-right no-wrap">{{ number_format($item->rate, 2) }}</td>

                    @if(!$isFabricStore)
                        @if($isOtherState)
                            <td class="text-center no-wrap">{{ number_format($item->igst_percent ?? 0, 2) }}%</td>
                            <td class="text-right no-wrap">{{ number_format($item->igst_amount ?? 0, 2) }}</td>
                        @else
                            <td class="text-center no-wrap">{{ number_format($item->cgst_percent ?? 0, 2) }}%</td>
                            <td class="text-right no-wrap">{{ number_format($item->cgst_amount ?? 0, 2) }}</td>
                            <td class="text-center no-wrap">{{ number_format($item->sgst_percent ?? 0, 2) }}%</td>
                            <td class="text-right no-wrap">{{ number_format($item->sgst_amount ?? 0, 2) }}</td>
                        @endif
                    @endif

                    <td class="text-right no-wrap">{{ number_format($item->amount, 2) }}</td>

                    <td class="text-center" style="padding: 1px;">
                        <div class="img-container">
                            @if($imageSrc)
                                <img src="{{ $imageSrc }}" alt="Item" @if($imgW && $imgH) style="width:{{ $imgW }}px; height:{{ $imgH }}px;" @endif>
                            @else
                                -
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach

                @for($i = 0; $i < $fillerRows; $i++)
                    <tr class="filler-row">
                        <td class="text-center">&nbsp;</td>
                        @for($c = 1; $c < $totalCols; $c++)
                            <td>&nbsp;</td>
                        @endfor
                    </tr>
                @endfor

                @if(!$isLastPage)
                <tr>
                    <td colspan="{{ $totalCols }}" class="text-right bold" style="padding: 3px 6px; border-top: 1px solid #000; font-style: italic;">
                        Continue to Page {{ $pageIndex + 2 }}...
                    </td>
                </tr>
                @endif

                @if($isLastPage)
                <tr class="total-row">
                    @if($isFabricStore)
                        <td colspan="10" class="text-right bold">Total</td>
                        <td class="text-center bold no-wrap">
                            {{ number_format($purchaseOrder->total_qty, 2) }}
                        </td>
                        <td></td>
                        <td class="text-right bold no-wrap">
                            {{ number_format($purchaseOrder->sub_total, 2) }}
                        </td>
                        <td></td>
                    @elseif($isOtherState)
                        <td colspan="5" class="text-right bold">Total</td>
                        <td class="text-center bold no-wrap">
                            {{ number_format($purchaseOrder->total_qty, 2) }}
                        </td>
                        <td></td>
                        <td></td>
                        <td class="text-right bold no-wrap">
                            {{ number_format($purchaseOrder->items->sum('igst_amount'), 2) }}
                        </td>
                        <td class="text-right bold no-wrap">
                            {{ number_format($purchaseOrder->sub_total, 2) }}
                        </td>
                        <td></td>
                    @else
                        <td colspan="5" class="text-right bold">Total</td>
                        <td class="text-center bold no-wrap">
                            {{ number_format($purchaseOrder->total_qty, 2) }}
                        </td>
                        <td></td>
                        <td></td>
                        <td class="text-right bold no-wrap">
                            {{ number_format($purchaseOrder->items->sum('cgst_amount'), 2) }}
                        </td>
                        <td></td>
                        <td class="text-right bold no-wrap">
                            {{ number_format($purchaseOrder->items->sum('sgst_amount'), 2) }}
                        </td>
                        <td class="text-right bold no-wrap">
                            {{ number_format($purchaseOrder->sub_total, 2) }}
                        </td>
                        <td></td>
                    @endif
                </tr>
                @endif
            </tbody>
        </table>

        <!-- 5. Standalone Summary Box Table (Last Page) -->
        @if($isLastPage)
        <table class="summary-box-table">
            <tr>
                <td style="width: 55%; vertical-align: top; border-right: 1px solid #000; padding: 4px 6px; font-size: 11.5px; line-height: 1.35; color: #000; border-top: none; border-bottom: none; border-left: none;">
                    <div>Amount in Words: <strong>{{ strtoupper($totalInWords) }}</strong></div>
                    @if(isset($purchaseOrder->remarks) && $purchaseOrder->remarks != '')
                    <div style="margin-top: 6px;">
                        <strong>Remarks:</strong> {{ $purchaseOrder->remarks }}
                    </div>
                    @endif
                </td>
                <td style="width: 45%; vertical-align: top; padding: 0; border: none;">
                    <table class="summary-inner-table">
                        <tr>
                            <td class="text-left">Total Qty:</td>
                            <td class="text-right">{{ number_format($purchaseOrder->total_qty, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-left">Sub Total:</td>
                            <td class="text-right">{{ number_format($purchaseOrder->sub_total, 2) }}</td>
                        </tr>
                        @if($purchaseOrder->discount_amount > 0)
                        <tr>
                            <td class="text-left">Discount ({{ number_format($purchaseOrder->discount_percent, 2) }}%):</td>
                            <td class="text-right">-{{ number_format($purchaseOrder->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        @if($purchaseOrder->commission > 0)
                        <tr>
                            <td class="text-left">Commission ({{ number_format($purchaseOrder->commission, 2) }}%):</td>
                            <td class="text-right">-{{ number_format((($purchaseOrder->sub_total * $purchaseOrder->commission) / 100), 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-left">Taxable Amount:</td>
                            <td class="text-right">{{ number_format($purchaseOrder->taxable_amount, 2) }}</td>
                        </tr>
                        @if($isAccessoriesStore)
                            @if($purchaseOrder->other_state)
                            <tr>
                                <td class="text-left">IGST:</td>
                                <td class="text-right">{{ number_format($purchaseOrder->tax_amount, 2) }}</td>
                            </tr>
                            @else
                            <tr>
                                <td class="text-left">CGST:</td>
                                <td class="text-right">{{ number_format($purchaseOrder->items->sum('cgst_amount'), 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-left">SGST:</td>
                                <td class="text-right">{{ number_format($purchaseOrder->items->sum('sgst_amount'), 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-left">Total GST Amount:</td>
                                <td class="text-right">{{ number_format($purchaseOrder->tax_amount, 2) }}</td>
                            </tr>
                            @endif
                        @else
                            @if($purchaseOrder->other_state)
                            <tr>
                                <td class="text-left">IGST ({{ $purchaseOrder->igst_percent }}%):</td>
                                <td class="text-right">{{ number_format($purchaseOrder->tax_amount, 2) }}</td>
                            </tr>
                            @else
                            <tr>
                                <td class="text-left">CGST ({{ $purchaseOrder->cgst_percent }}%):</td>
                                <td class="text-right">{{ number_format($purchaseOrder->tax_amount / 2, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-left">SGST ({{ $purchaseOrder->sgst_percent }}%):</td>
                                <td class="text-right">{{ number_format($purchaseOrder->tax_amount / 2, 2) }}</td>
                            </tr>
                            @endif
                        @endif
                        <tr>
                            <td class="text-left">Round Off ({{ $purchaseOrder->round_off_type }}):</td>
                            <td class="text-right">{{ $purchaseOrder->round_off_type == 'Less' ? '-' : '+' }}{{ number_format($purchaseOrder->round_off, 2) }}</td>
                        </tr>
                        <tr style="font-weight: bold; font-size: 12px;">
                            <td class="text-left" style="border-top: 1px solid #000 !important;">Grand Total:</td>
                            <td class="text-right" style="border-top: 1px solid #000 !important;">{{ number_format($purchaseOrder->total_amount, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        @endif

        <!-- 6. Payment Terms & Signatory (Last Page) -->
        @if($isLastPage)
        <table class="no-border" style="margin-top: 6px; width: 100%;">
            <tr>
                <td width="55%" style="vertical-align: top;">
                    @if(isset($purchaseOrder->payment_terms) && $purchaseOrder->payment_terms != '')
                    <div style="font-size: 11px; line-height: 1.3; color: #000;">
                        <strong>Payment Terms:</strong><br>
                        {{ $purchaseOrder->payment_terms }}
                    </div>
                    @endif
                </td>
                <td width="45%" class="text-right" style="vertical-align: top;">
                    <div style="border: 1px solid #000; border-radius: 2px; text-align: center; height: 65px; width: 190px; display: inline-block;">
                        <div style="padding-top: 4px; font-size: 10.5px; font-weight: bold;">For NACHIAS FASHION PVT.LTD.</div>
                        <div style="margin-top: 32px; font-size: 10px; border-top: 1px dotted #000;">
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
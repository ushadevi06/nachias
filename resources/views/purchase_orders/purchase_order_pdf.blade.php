<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Purchase Order - {{ $purchaseOrder->po_number }}</title>
    <style>
        @font-face {
            font-family: "dejavusans-regular";
            src: url("{{ asset('assets/fonts/DejaVuSans.ttf') }}");
        }
        @font-face {
            font-family: "dejavusans-bold";
            src: url("{{ asset('assets/fonts/DejaVuSans-Bold.ttf') }}");
        }

        @page {
            size: A4 portrait;
            margin: 10mm 8mm 6mm 8mm;
        }

        body {
            font-family: 'dejavusans-regular', sans-serif;
            font-size: 8px;
            color: #292929ff;
            margin: 0;
            padding: 0;
            line-height: 1.15;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        strong, .bold, th {
            font-family: 'dejavusans-bold', sans-serif;
        }

        .container {
            padding: 10px;
        }

        @media print {
            .container {
                padding: 0;
            }
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px 4px;
            text-align: left;
            vertical-align: top;
        }

        tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        .item-table {
            border-bottom: 1px solid #000;
        }

        .item-table th, .item-table td {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-top: none;
            border-bottom: none;
            padding: 4px;
            text-align: left;
            vertical-align: top;
        }

        .item-table th {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        .item-table tbody td {
            padding: 2.5px 3px;
            vertical-align: middle;
        }

        .item-table tfoot td {
            border-top: 1px solid #000 !important;
            border-bottom: none !important;
            border-left: none !important;
            border-right: none !important;
            padding: 0 !important;
            height: 0px !important;
            line-height: 0px !important;
            font-size: 0px !important;
        }

        .item-table .total-row td {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        .item-table thead td {
            border: none;
            padding: 0;
            background: transparent;
        }

        .no-border th,
        .no-border td {
            border: none !important;
            padding: 3px 2px;
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

        .header-table td {
            border: none;
        }

        .header-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            text-align: center;
        }

        .section-title {
            background-color: #f2f2f2;
            font-weight: bold;
            padding: 2px 5px;
            border: 1px solid #000;
        }

        .item-table {
            border-bottom: 1px solid #000;
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
            word-wrap: break-word;
        }

        .item-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        .item-table tbody tr:nth-child(even) {
            background-color: transparent;
        }

        .item-table tfoot td {
            background-color: #f2f2f2;
            font-weight: bold;
            border: 1px solid #000;
        }

        .bottom-section {
            border: 1px solid #000;
            border-top: none;
            width: 100%;
        }

        .summary-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-box td {
            padding: 3px 5px;
            border: none;
        }

        .summary-box .label {
            text-align: left;
        }

        .summary-box .value {
            text-align: right;
        }

        .amount-words {
            border-top: 1px solid #000;
            padding: 5px;
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
        $isOtherState = ($purchaseOrder->other_state ?? '') === 'yes';
        $totalCols = $isFabricStore ? 14 : ($isOtherState ? 12 : 14);

        $hasImages = $allItems->contains(function($it) {
            return !empty($it->attached_file) && file_exists(public_path('uploads/purchase_orders/' . $it->attached_file));
        });

        $PAGE_HEIGHT_PX = 915; 
        
        // Dynamic Header Height
        $s = $purchaseOrder->supplier;
        $addrParts = array_filter([$s->address_line_1 ?? null, $s->address_line_2 ?? null, $s->address_line_3 ?? null]);
        $addrLineCount = count($addrParts);
        $addrText = implode(' ', $addrParts);
        $extraAddrLines = max(0, $addrLineCount - 1) + (strlen($addrText) > 60 ? (int)floor(strlen($addrText) / 60) : 0);
        
        $HEADER_HEIGHT_PX = 235 + ($extraAddrLines * 14);
        $ROW_HEIGHT_PX = $hasImages ? 38 : 22;
        $CONTINUE_NOTE_PX = 28;

        // Dynamic Footer/Summary Height calculation (Summary + Remarks + 90px Signature Box)
        $summaryLines = 3; // Total Qty, Sub Total, Taxable Amount
        if (($purchaseOrder->discount_amount ?? 0) > 0) $summaryLines++;
        if (($purchaseOrder->commission ?? 0) > 0) $summaryLines++;
        if (($purchaseOrder->cgst_amount ?? 0) > 0 || ($purchaseOrder->cgst_percent ?? 0) > 0) $summaryLines++;
        if (($purchaseOrder->sgst_amount ?? 0) > 0 || ($purchaseOrder->sgst_percent ?? 0) > 0) $summaryLines++;
        if (($purchaseOrder->igst_amount ?? 0) > 0 || ($purchaseOrder->igst_percent ?? 0) > 0) $summaryLines++;
        if (($purchaseOrder->round_off ?? 0) != 0) $summaryLines++;
        $summaryLines++; // Grand Total

        $remarksLen = strlen($purchaseOrder->remarks ?? '');
        $remarksLines = !empty($purchaseOrder->remarks) ? max(1, (int)ceil($remarksLen / 50)) : 0;

        $footerHeight = 240 + (max(0, $summaryLines - 5) * 14) + ($remarksLines * 14);

        $contentAreaHeight = $PAGE_HEIGHT_PX - $HEADER_HEIGHT_PX;
        
        $rowsPerFullPage = min($hasImages ? 16 : 26, max(1, (int) floor(($contentAreaHeight - $CONTINUE_NOTE_PX) / $ROW_HEIGHT_PX)));
        $rowsPerLastPage = min($hasImages ? 12 : 22, max(1, (int) floor(($PAGE_HEIGHT_PX - $HEADER_HEIGHT_PX - $footerHeight) / $ROW_HEIGHT_PX)));

        // Intelligent Page Chunking
        $pages = [];
        $remaining = collect($allItems);

        if ($totalItemCount === 0) {
            $pages[] = collect();
        } elseif ($totalItemCount <= $rowsPerLastPage) {
            $pages[] = $remaining;
        } else {
            while ($remaining->count() > 0) {
                $remCount = $remaining->count();

                if ($remCount <= $rowsPerLastPage) {
                    $pages[] = $remaining;
                    break;
                }

                if ($remCount <= $rowsPerFullPage + $rowsPerLastPage) {
                    $leftForLast = min($rowsPerLastPage, max(1, $remCount - $rowsPerFullPage));
                    $take = $remCount - $leftForLast;
                    if ($take > $rowsPerFullPage) {
                        $take = $rowsPerFullPage;
                    }
                } else {
                    $take = $rowsPerFullPage;
                }

                $pages[] = $remaining->take($take);
                $remaining = $remaining->slice($take)->values();
            }
        }

        $itemChunks = collect($pages);
        $totalChunks = count($itemChunks);
        $globalIndex = 0;
    @endphp

    @foreach($itemChunks as $chunkIndex => $chunk)
    <div class="container" style="{{ $chunkIndex < $totalChunks - 1 ? 'page-break-after: always;' : '' }}">
        <table class="item-table">
            <thead>
                <tr>
                    <td colspan="{{ $totalCols }}" style="border: none; padding: 4px 0 0 0; background: #fff;">
                        <div style="position: relative;">
                            <table class="header-table" style="width: 100%; border: none;">
                                <tr>
                                    <td width="60%" style="border: none; padding: 0;">
                                        <table style="border: none; width: 100%;">
                                            <tr>
                                                <td style="border: none; vertical-align: top; width:25%; padding: 0;">
                                                    @php
                                                        $logoPath = '';
                                                        if (isset($setting) && !empty($setting->logo)) {
                                                            if (isset($is_print) && $is_print) {
                                                                $logoPath = url('uploads/logo/' . $setting->logo);
                                                            } else {
                                                                $logoPath = public_path('uploads/logo/' . $setting->logo);
                                                            }
                                                        }
                                                        
                                                        if (empty($logoPath) || (!isset($is_print) && !file_exists($logoPath))) {
                                                            $logoPath = isset($is_print) && $is_print ? url('assets/images/jc_logo.png') : public_path('assets/images/jc_logo.png');
                                                        }
                                                    @endphp
                                                    <img src="{{ $logoPath }}" style="width: 140px;">
                                                </td>
                                                <td style="border: none; vertical-align: top; padding-left: 10px; width:75%;">
                                                    <div style="font-size: 9.5px; line-height: 1.25;">
                                                        {{ $setting->address }}
                                                        <table style="width: 100%; border-collapse: collapse; margin-top: 2px;">
                                                            <tr>
                                                                <td style="border: none; padding: 0; width: 45px;">Mobile</td>
                                                                <td style="border: none; padding: 0; width: 10px;">:</td>
                                                                <td style="border: none; padding: 0;">
                                                                    {!! implode(', ', array_map('trim', explode(',', $setting->toll_free_no ?? ''))) !!}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="border: none; padding: 0;">Email</td>
                                                                <td style="border: none; padding: 0;">:</td>
                                                                <td style="border: none; padding: 0; white-space: nowrap;">
                                                                    {!! implode(', ', array_map('trim', explode(',', $setting->email ?? ''))) !!}
                                                                </td>
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
                                </tr>
                            </table>
                        </div>
                        <div class="header-title" style="margin-top: 6px; margin-bottom: 6px;">Purchase Order</div>
                        <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; margin-bottom: 8px; font-size: 9.5px;">
                            <tr>
                                <td width="55%" style="padding: 4px 6px; vertical-align: top; border-right: 1px solid #000; border-bottom: none; border-top: none; border-left: none;">
                                    <table class="no-border" style="margin: 0; width: 100%;">
                                        <tr>
                                            <td width="22%">Supplier</td>
                                            <td width="5%">:</td>
                                            <td><strong>{{ $purchaseOrder->supplier->name }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>Address</td>
                                            <td>:</td>
                                            <td>
                                                @php
                                                $s = $purchaseOrder->supplier;
                                                $addr = array_filter([$s->address_line_1, $s->address_line_2, $s->address_line_3]);
                                                echo implode(', ', $addr);
                                                if ($s->city) echo '<br>' . $s->city->city_name;
                                                if ($s->state) echo ' - ' . $s->state->state_name;
                                                if ($s->zip_code) echo ' (' . $s->zip_code . ')';
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
                                <td width="45%" style="padding: 4px 6px; vertical-align: top; border: none;">
                                    <table class="no-border" style="margin: 0; width: 100%;">
                                        <tr>
                                            <td width="30%">PO No.</td>
                                            <td width="5%">:</td>
                                            <td width="65%"><strong>{{ $purchaseOrder->po_number }}</strong></td>
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
                                            <td>{{ $purchaseOrder->purchaseCommissionAgent->name ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    @if($isFabricStore)
                        <th width="3.5%">S.No</th>
                        <th width="7.5%">Store Category</th>
                        <th width="7.5%">Brand</th>
                        <th width="10%">Raw Material</th>
                        <th width="7%">Style</th>
                        <th width="5.5%">Fabric Width</th>
                        <th width="6.5%">Fabric Type</th>
                        <th width="7.5%">Supplier Design</th>
                        <th width="6.5%">Color</th>
                        <th width="4.5%">UOM</th>
                        <th width="7.5%">Quantity</th>
                        <th width="6.5%">Rate</th>
                        <th width="10%">Amount</th>
                        <th width="10%">Image</th>
                    @elseif($isOtherState)
                        <th width="3.5%">S.No</th>
                        <th width="8.5%">Store Category</th>
                        <th width="8.5%">Brand</th>
                        <th width="14%">Raw Material</th>
                        <th width="10%">Supplier Design</th>
                        <th width="5.5%">UOM</th>
                        <th width="8%">Quantity</th>
                        <th width="7.5%">Rate</th>
                        <th width="6.5%">IGST %</th>
                        <th width="9%">IGST Amt</th>
                        <th width="10%">Amount</th>
                        <th width="9%">Image</th>
                    @else
                        <th width="3.5%">S.No</th>
                        <th width="7.5%">Store Category</th>
                        <th width="7.5%">Brand</th>
                        <th width="11%">Raw Material</th>
                        <th width="8.5%">Supplier Design</th>
                        <th width="4.5%">UOM</th>
                        <th width="7%">Quantity</th>
                        <th width="6.5%">Rate</th>
                        <th width="5.5%">CGST %</th>
                        <th width="7.5%">CGST Amt</th>
                        <th width="5.5%">SGST %</th>
                        <th width="7.5%">SGST Amt</th>
                        <th width="9%">Amount</th>
                        <th width="9%">Image</th>
                    @endif
                </tr>
            </thead>

            <tbody>
                @foreach($chunk as $item)
                @php
                $globalIndex++;
                $imageSrc = '';

                if (!empty($item->attached_file)) {
                    $imagePath = public_path('uploads/purchase_orders/' . $item->attached_file);
                    if (file_exists($imagePath)) {
                        if (isset($is_print) && $is_print) {
                            $imageSrc = url('uploads/purchase_orders/' . $item->attached_file);
                        } else {
                            $imageSrc = $imagePath;
                        }
                    }
                }
                @endphp

                <tr>
                    <td class="text-center no-wrap">{{ $globalIndex }}</td>
                    <td class="text-center">{{ $item->storeCategory->category_name ?? '-' }}</td>
                    <td class="text-center">{{ $item->brand->brand_name ?? '-' }}</td>
                    <td><strong>{{ $item->rawMaterial->name ?? '' }}</strong></td>

                    @if($isFabricStore)
                        <td class="text-center">{{ $item->style->style_name ?? '-' }}</td>
                        <td class="text-center no-wrap">{{ $item->fabricWidth->width ?? '-' }}</td>
                        <td class="text-center">{{ $item->fabricType->fabric_type ?? '-' }}</td>
                        <td class="text-center bold">{{ $item->supplier_design_name ?? '-' }}</td>
                        <td class="text-center">{{ $item->color->color_name ?? '-' }}</td>
                    @else
                        <td class="text-center bold">{{ $item->supplier_design_name ?? '-' }}</td>
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

                    <td class="text-center">
                        @if($imageSrc)
                        <img src="{{ $imageSrc }}"
                            alt="Item Image"
                            style="width:32px; height:32px; object-fit:cover; border:1px solid #ccc; display:block; margin: 0 auto; max-width:100%;">
                        @else
                        -
                        @endif
                    </td>
                </tr>
                @endforeach

                @if($chunkIndex < $totalChunks - 1)
                @php
                    $intermediateCount = count($chunk);
                    $dummyRowsIntermediate = max(0, $rowsPerFullPage - $intermediateCount);
                @endphp
                @for($i = 0; $i < $dummyRowsIntermediate; $i++)
                    <tr style="page-break-inside: avoid;">
                        <td style="height: 20px; line-height: 18px;">&nbsp;</td>
                        @for($c = 1; $c < $totalCols; $c++)
                            <td>&nbsp;</td>
                        @endfor
                    </tr>
                @endfor
                <tr style="page-break-inside: avoid;">
                    <td colspan="{{ $totalCols }}" class="text-right bold" style="padding: 6px 12px; border-top: 1px solid #000; font-style: italic;">
                        Continue to Page {{ $chunkIndex + 2 }}...
                    </td>
                </tr>
                @endif

                @if($chunkIndex == $totalChunks - 1)
                @php
                    $lastChunkCount = count($chunk);
                    $dummyRowsNeeded = max(0, $rowsPerLastPage - $lastChunkCount);
                @endphp

                @for($i = 0; $i < $dummyRowsNeeded; $i++)
                    <tr style="page-break-inside: avoid;">
                        <td style="height: 20px; line-height: 18px;">&nbsp;</td>
                        @for($c = 1; $c < $totalCols; $c++)
                            <td>&nbsp;</td>
                        @endfor
                    </tr>
                @endfor

                <tr class="total-row" style="page-break-inside: avoid;">
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
                        <td colspan="6" class="text-right bold">Total</td>
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
                        <td colspan="6" class="text-right bold">Total</td>
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

                <tr style="page-break-inside: avoid;">
                    <td colspan="{{ $totalCols }}" style="padding: 0; border: 1px solid #000;">
                        <table style="width: 100%; border-collapse: collapse; border: none;">
                            <tr>
                                <td style="width: 60%; padding: 8px; vertical-align: top; border-right: 1px solid #000; border-top: none; border-bottom: none; border-left: none;">
                                    <div class="amount-words" style="border-top: none; padding: 0;">
                                        Amount in Words: <strong>{{ strtoupper($totalInWords) }}</strong>
                                    </div>
                                    @if(isset($purchaseOrder->remarks) && $purchaseOrder->remarks != '')
                                    <div style="margin-top: 10px;">
                                        <strong>Remarks:</strong> {{ $purchaseOrder->remarks }}
                                    </div>
                                    @endif
                                </td>
                                <td style="width: 40%; padding: 0; vertical-align: top; border: none;">
                                    <table class="no-border" style="width: 100%;">
                                        <tr>
                                            <td class="text-left" style="padding: 4px;">Total Qty:</td>
                                            <td class="text-right" style="padding: 4px;">
                                                {{ number_format($purchaseOrder->total_qty, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-left" style="padding: 4px;">Sub Total:</td>
                                            <td class="text-right" style="padding: 4px;">
                                                {{ number_format($purchaseOrder->sub_total, 2) }}
                                            </td>
                                        </tr>
                                        @if($purchaseOrder->discount_amount > 0)
                                        <tr>
                                            <td class="text-left" style="padding: 4px;">Discount
                                                ({{ number_format($purchaseOrder->discount_percent, 2) }}%):</td>
                                            <td class="text-right" style="padding: 4px;">
                                                -{{ number_format($purchaseOrder->discount_amount, 2) }}</td>
                                        </tr>
                                        @endif
                                        @if($purchaseOrder->commission > 0)
                                        <tr>
                                            <td class="text-left" style="padding: 4px;">Commission
                                                ({{ number_format($purchaseOrder->commission, 2) }}%):</td>
                                            <td class="text-right" style="padding: 4px;">
                                                -{{ number_format((($purchaseOrder->sub_total * $purchaseOrder->commission) / 100), 2) }}
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td class="text-left" style="padding: 4px;">Taxable Amount:</td>
                                            <td class="text-right" style="padding: 4px;">
                                                {{ number_format($purchaseOrder->taxable_amount, 2) }}
                                            </td>
                                        </tr>
                                        
                                        @php
                                            $isAccessories = $purchaseOrder->storeType && strtolower($purchaseOrder->storeType->store_type_name) == 'accessories';
                                        @endphp
                                        @if($isAccessories)
                                            @if($purchaseOrder->other_state)
                                            <tr>
                                                <td class="text-left" style="padding: 4px;">IGST:</td>
                                                <td class="text-right" style="padding: 4px;">
                                                    {{ number_format($purchaseOrder->tax_amount, 2) }}
                                                </td>
                                            </tr>
                                            @else
                                            <tr>
                                                <td class="text-left" style="padding: 4px;">CGST:</td>
                                                <td class="text-right" style="padding: 4px;">
                                                    {{ number_format($purchaseOrder->items->sum('cgst_amount'), 2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="padding: 4px;">SGST:</td>
                                                <td class="text-right" style="padding: 4px;">
                                                    {{ number_format($purchaseOrder->items->sum('sgst_amount'), 2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="padding: 4px;">Total GST Amount:</td>
                                                <td class="text-right" style="padding: 4px;">
                                                    {{ number_format($purchaseOrder->tax_amount, 2) }}
                                                </td>
                                            </tr>
                                            @endif
                                        @else
                                            @if($purchaseOrder->other_state)
                                            <tr>
                                                <td class="text-left" style="padding: 4px;">IGST ({{ $purchaseOrder->igst_percent }}%):</td>
                                                <td class="text-right" style="padding: 4px;">
                                                    {{ number_format($purchaseOrder->tax_amount, 2) }}
                                                </td>
                                            </tr>
                                            @else
                                            <tr>
                                                <td class="text-left" style="padding: 4px;">CGST ({{ $purchaseOrder->cgst_percent }}%):</td>
                                                <td class="text-right" style="padding: 4px;">
                                                    {{ number_format($purchaseOrder->tax_amount / 2, 2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="padding: 4px;">SGST ({{ $purchaseOrder->sgst_percent }}%):</td>
                                                <td class="text-right" style="padding: 4px;">
                                                    {{ number_format($purchaseOrder->tax_amount / 2, 2) }}
                                                </td>
                                            </tr>
                                            @endif
                                        @endif
                                        <tr>
                                            <td class="text-left" style="padding: 4px;">Round Off
                                                ({{ $purchaseOrder->round_off_type }}):</td>
                                            <td class="text-right" style="padding: 4px;">
                                                {{ $purchaseOrder->round_off_type == 'Less' ? '-' : '+' }}{{ number_format($purchaseOrder->round_off, 2) }}
                                            </td>
                                        </tr>
                                        <tr style="font-weight: bold;">
                                            <td class="text-left" style="padding: 4px; border-top: 1px solid #000 !important;">Grand Total:</td>
                                            <td class="text-right" style="padding: 4px; border-top: 1px solid #000 !important;">
                                                {{ number_format($purchaseOrder->total_amount, 2) }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>

        @if($chunkIndex == $totalChunks - 1)
        <table class="no-border" style="margin-top: 10px; width: 100%; page-break-inside: avoid;">
            <tr>
                <td width="50%">
                    @if(isset($purchaseOrder->payment_terms) && $purchaseOrder->payment_terms != '')
                    <div style="font-size: 10px;">
                        <strong>Payment Terms:</strong><br>
                        {{ $purchaseOrder->payment_terms }}
                    </div>
                    @endif
                </td>
                <td width="50%" class="text-right">
                    <div
                        style="border: 1px solid #000; border-radius: 2px; text-align: center; height: 80px; width: 200px; display: inline-block;">
                        <div style="padding-top: 5px; font-size: 10px;">For NACHIAS FASHION PVT.LTD.</div>
                        <div style="margin-top: 40px; font-size: 10px; border-top: 1px dotted #000;">
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
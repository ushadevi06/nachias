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
            margin: 6mm 7mm 5mm 7mm;
        }

        body {
            font-family: 'dejavusans-regular', sans-serif;
            font-size: 8.5px;
            color: #222;
            margin: 0;
            padding: 0;
            line-height: 1.15;
        }

        strong, .bold, th {
            font-family: 'dejavusans-bold', sans-serif;
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
            padding: 0;
        }

        .details-box {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
            margin-bottom: 3px;
        }

        .details-box td {
            vertical-align: top;
            font-size: 8.5px;
            line-height: 1.2;
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
            padding: 1px 0 !important;
            vertical-align: top;
            font-size: 8.5px;
            line-height: 1.2;
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
            border: 1px solid #000;
            padding: 3px 2px;
            font-size: 8px;
        }

        .item-table tbody td {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-top: none;
            border-bottom: none;
            padding: 1px 2.5px;
            vertical-align: middle;
            font-size: 8.5px;
            height: {{ $rowHeight }}px;
        }

        .item-row {
            height: {{ $rowHeight }}px;
        }

        .item-row td {
            height: {{ $rowHeight }}px;
            vertical-align: middle;
        }

        .filler-row {
            height: {{ $rowHeight }}px;
        }

        .filler-row td {
            height: {{ $rowHeight }}px;
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
            padding: 3px 3px;
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
            padding: 1.5px 3px !important;
            font-size: 8.5px !important;
            line-height: 1.2 !important;
            height: auto !important;
            vertical-align: middle;
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

        .header-title {
            font-size: 12px;
            font-weight: bold;
            margin: 2px 0 3px 0;
            text-align: center;
        }

        .img-container {
            width: {{ $imgDim }}px;
            height: {{ $imgDim }}px;
            line-height: {{ $imgDim }}px;
            text-align: center;
            vertical-align: middle;
            margin: 0 auto;
            overflow: hidden;
        }

        .img-container img {
            max-width: {{ $imgDim }}px;
            max-height: {{ $imgDim }}px;
            display: inline-block;
            vertical-align: middle;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
    @php
        $isAccessoriesStore = $purchaseOrder->storeType && (
            stripos($purchaseOrder->storeType->store_type_name, 'ACCESSORIES') !== false
            || $purchaseOrder->store_type_id == 2
        );
        $isFabricStore = !$isAccessoriesStore;
        $isOtherState = ($purchaseOrder->other_state ?? '') === 'yes';
        $totalCols = $isFabricStore ? 14 : ($isOtherState ? 12 : 14);
        $globalIndex = 0;
    @endphp

    <div class="container">
        <!-- 1. Company Header Table -->
        <table class="header-table" style="width: 100%; border: none; margin-bottom: 2px;">
            <tr>
                <td style="width: 20%; vertical-align: middle; padding: 0;">
                    @php
                        $logoPath = public_path('assets/images/jc_logo.png');
                    @endphp
                    <img src="{{ $logoPath }}" style="width: 120px;">
                </td>
                <td style="width: 80%; vertical-align: middle; padding-left: 8px; font-size: 8.5px; line-height: 1.25;">
                    <div>{{ $setting->address }}</div>
                    <div style="margin-top: 1px;">
                        <strong>Mobile:</strong> {!! implode(', ', array_map('trim', explode(',', $setting->toll_free_no ?? ''))) !!} &nbsp;|&nbsp; 
                        <strong>Email:</strong> {!! implode(', ', array_map('trim', explode(',', $setting->email ?? ''))) !!} &nbsp;|&nbsp; 
                        <strong>GSTIN:</strong> {{ $setting->gst_no ?? '' }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- 2. Purchase Order Title -->
        <div class="header-title">Purchase Order</div>

        <!-- 3. Supplier & PO Details Box -->
        <table class="details-box">
            <tr>
                <td style="width: 55%; border-right: 1px solid #000; padding: 3px 5px; vertical-align: top;">
                    <table class="details-inner-table">
                        <tr>
                            <td style="width: 20%;">Supplier</td>
                            <td style="width: 3%;">:</td>
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
                <td style="width: 45%; padding: 3px 5px; vertical-align: top;">
                    <table class="details-inner-table">
                        <tr>
                            <td style="width: 28%;">PO No.</td>
                            <td style="width: 3%;">:</td>
                            <td style="width: 69%;"><strong>{{ $purchaseOrder->po_number }}</strong></td>
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

        <!-- 4. Items Table -->
        <table class="item-table">
            <thead>
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
                        <th width="7%">Quantity</th>
                        <th width="6.5%">Rate</th>
                        <th width="9%">Amount</th>
                        <th width="11.5%">Image</th>
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
                        <th width="9.5%">Amount</th>
                        <th width="10%">Image</th>
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
                        <th width="8%">Amount</th>
                        <th width="10%">Image</th>
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
                        $imageSrc = $imagePath;
                        $imgInfo = @getimagesize($imagePath);
                        if ($imgInfo && $imgInfo[0] > 0 && $imgInfo[1] > 0) {
                            $maxDim = $imgDim;
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
                        Continue to Page 2...
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
                @endif
            </tbody>
        </table>

        <!-- 5. Standalone Summary Box Table (Last Page) -->
        @if($isLastPage)
        <table class="summary-box-table">
            <tr>
                <td style="width: 60%; vertical-align: top; border-right: 1px solid #000; padding: 4px 5px; font-size: 8.5px; border-top: none; border-bottom: none; border-left: none;">
                    <div>Amount in Words: <strong>{{ strtoupper($totalInWords) }}</strong></div>
                    @if(isset($purchaseOrder->remarks) && $purchaseOrder->remarks != '')
                    <div style="margin-top: 4px;">
                        <strong>Remarks:</strong> {{ $purchaseOrder->remarks }}
                    </div>
                    @endif
                </td>
                <td style="width: 40%; vertical-align: top; padding: 0; border: none;">
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
                        <tr style="font-weight: bold;">
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
        <table class="no-border" style="margin-top: 4px; width: 100%;">
            <tr>
                <td width="50%">
                    @if(isset($purchaseOrder->payment_terms) && $purchaseOrder->payment_terms != '')
                    <div style="font-size: 8px;">
                        <strong>Payment Terms:</strong><br>
                        {{ $purchaseOrder->payment_terms }}
                    </div>
                    @endif
                </td>
                <td width="50%" class="text-right">
                    <div style="border: 1px solid #000; border-radius: 2px; text-align: center; height: 55px; width: 170px; display: inline-block;">
                        <div style="padding-top: 3px; font-size: 8px;">For NACHIAS FASHION PVT.LTD.</div>
                        <div style="margin-top: 26px; font-size: 8px; border-top: 1px dotted #000;">
                            Authorised Signatory
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        @endif
    </div>
</body>
</html>

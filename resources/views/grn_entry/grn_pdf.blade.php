<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Goods Receipt Note - {{ $grn->grn_number }}</title>
<style>
    @page {
        size: A4;
        margin: 15px;
    }
    body {
        font-family: 'Arial', sans-serif;
        font-size: 9pt;
        color: #000;
        line-height: 1.2;
        margin: 0;
        padding: 0;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .header-section {
        padding-bottom: 5px;
        margin-bottom: 5px;
    }
    .company-name {
        font-size: 14pt;
        font-weight: normal;
        margin-bottom: 2px;
        border-bottom: 1px solid #000;
    }
    .company-address {
        font-size: 10pt;
        line-height: 1.3;
    }
    .title {
        text-align: center;
        font-size: 12pt;
        margin: 5px 0;
    }
    .meta-container {
        border: 1px solid #333;
        border-radius: 5px;
        overflow: hidden;
        margin-bottom: 5px;
        width: 100%;
        box-sizing: border-box;
    }
    .meta-table {
        width: 100%;
        border-collapse: collapse;
    }
    .meta-table td {
        padding: 4px 8px;
        vertical-align: top;
    }
    .meta-left {
        width: 55%;
        border-right: 1px solid #333;
    }
    .meta-right {
        width: 45%;
        padding: 0 !important;
    }
    .inner-meta-table {
        width: 100%;
        border-collapse: collapse;
    }
    .inner-meta-table td {
        padding: 4px 8px;
    }
    .inner-meta-table tr:last-child td {
        border-bottom: none;
    }
    .meta-label {
        width: 40%;
    }
    .meta-value {
        width: 60%;
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
        border: 1px solid #000;
        border-bottom: none;
    }
    .items-table th {
        background-color: #999;
        color: #000;
        border: 1px solid #000;
        padding: 6px 4px;
        font-weight: bold;
        font-size: 9pt;
    }
    .items-table td {
        border-left: 1px solid #000;
        border-right: 1px solid #000;
        border-top: none;
        border-bottom: none;
        padding: 4px;
        font-size: 9pt;
        vertical-align: top;
    }
    .alt-row {
        background-color: #f2f2f2;
    }

    .text-center { text-align: center; }
    .text-left { text-align: left; }
    .text-right { text-align: right; }

    .footer-section {
        width: 100%;
        margin-top: 10px;
    }
    .signature-box {
        float: right;
        width: 100%;
        max-width: 500px;
        border: 1px solid #000;
        border-radius: 5px;
        overflow: hidden;
    }
    .signature-header {
        background-color: #f2f2f2;
        text-align: center;
        font-weight: bold;
        padding: 4px;
        border-bottom: 1px solid #000;
    }
    .signature-content {
        /* padding: 5px; */
    }
    .signature-details {
        width: 100%;
        border-collapse: collapse;
    }
    .signature-details td {
        padding: 2px 5px;
    }
</style>
</head>
<body>
    <div class="header-section">
        <div class="company-name">{{ $setting->company_name ?? '' }}</div>
        <div class="company-address">
            {{ $setting->address ?? '' }}<br>
            @if(isset($setting))
                GSTIN: <strong>{{ $setting->gst_no ?? '' }}</strong> &nbsp;&nbsp; MOB: {{ $setting->toll_free_no ?? '' }}
            @endif
        </div>
    </div>
    <div class="title">Goods Receipt</div>
    <div class="meta-container">
        <table class="meta-table">
            <tr>
                <td class="meta-left">
                    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                        <tr>
                            <td width="35%" style="border:none; padding:0;">Received From</td>
                            <td width="65%" style="border:none; padding:0;">: <strong>{{ $grn->supplier->name ?? 'N/A' }}</strong></td>
                        </tr>
                        <tr>
                            <td style="border:none;"></td>
                            <td style="border:none; font-size: 8pt; line-height: 1.2; padding-top: 2px;">
                                @if($grn->supplier)
                                    @php
                                        $addressLines = array_filter([
                                            $grn->supplier->address_line_1,
                                            $grn->supplier->address_line_2,
                                            $grn->supplier->address_line_3
                                        ]);
                                        $address = implode(', ', $addressLines);
                                        $city = $grn->supplier->city->city_name ?? '';
                                        $zip = $grn->supplier->zip_code ?? '';
                                        
                                        $mainAddress = implode(', ', array_filter([$address, $city]));
                                        echo $mainAddress;
                                        if ($zip) {
                                            echo ' - ' . $zip;
                                        }
                                    @endphp
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="meta-right">
                    <table class="inner-meta-table">
                        <tr>
                            <td class="meta-label">Receipt No.</td>
                            <td class="meta-value">: {{ $grn->grn_number }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Receipt Date</td>
                            <td class="meta-value">: {{ $grn->grn_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Doc. No</td>
                            <td class="meta-value">: {{ $grn->purchaseInvoice->invoice_no ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Reference#</td>
                            <td class="meta-value">: {{ $grn->purchaseInvoice->purchaseOrder->po_number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Store</td>
                            <td class="meta-value">: {{ $grn->grnEntryItems->first()->storeLocation->store_location ?? '-' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th width="20">S.No</th>
                <th width="130">Raw Material</th>
                <th width="40">Style</th>
                <th width="40">Color</th>
                <th width="40">Width</th>
                <th width="50">Supplier Design</th>
                <th width="50">Art No</th>
                <th width="40">Location</th>
                <th width="30">UOM</th>
                <th width="40">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @php $totalQty = 0; @endphp
            @foreach($grn->grnEntryItems as $index => $item)
                @php 
                    $totalQty += $item->qty_received;
                    $catId = $item->purchaseInvoiceItem?->rawMaterial?->store_category_id ?? 0;
                    if ($catId == 1 && $item->purchaseInvoiceItem?->purchaseOrderItem?->supplier_design_name) {
                        $rawMaterialName = $item->purchaseInvoiceItem->purchaseOrderItem->supplier_design_name;
                    } else {
                        $rawMaterialName = $item->purchaseInvoiceItem->rawMaterial->name ?? '-';
                    }
                    $brandName = $item->purchaseInvoiceItem->purchaseOrderItem->brand->brand_name ?? '-';
                    $styleName = $item->purchaseInvoiceItem->purchaseOrderItem->style->style_name ?? '-';
                    $colorName = $item->purchaseInvoiceItem->purchaseOrderItem->color->color_name ?? '-';
                    $widthVal = $item->purchaseInvoiceItem->purchaseOrderItem->fabricWidth->width ?? '-';
                    $supplierArtNo = $item->purchaseInvoiceItem->purchaseOrderItem->supplier_design_name ?? '-';
                @endphp
                <tr class="{{ ($index % 2 != 0) ? 'alt-row' : '' }}">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-left">{{ $rawMaterialName }} <br> [{{ $brandName }}]</td>
                    <td class="text-center">{{ $styleName }}</td>
                    <td class="text-center">{{ $colorName }}</td>
                    <td class="text-center">{{ $widthVal }}</td>
                    <td class="text-center">{{ $supplierArtNo }}</td>
                    <td class="text-center">{{ $item->art_no ?? '-' }}</td>
                    <td class="text-center">{{ $item->storeLocation->store_location ?? '-' }}</td>
                    <td class="text-center">{{ $item->purchaseInvoiceItem->uom->uom_code ?? 'MTR' }}</td>
                    <td class="text-right">{{ number_format($item->qty_received, 2) }}</td>
                </tr>
            @endforeach
            
            @php 
                $currentCount = count($grn->grnEntryItems);
                $fillerCount = max(0, 15 - $currentCount);
            @endphp
            @for ($i = 0; $i < $fillerCount; $i++)
                @php $rowIndex = $currentCount + $i; @endphp
                <tr class="{{ ($rowIndex % 2 != 0) ? 'alt-row' : '' }}">
                    <td style="height: 18px;">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            @endfor
            <tr style="font-weight: bold;">
                <td colspan="9" class="text-right" style="border: none; border-top: 1px solid #000; border-left-style: hidden;">Total Quantity: &nbsp;</td>
                <td class="text-right" style="border: 1px solid #000; border-top: 1px solid #000; padding: 6px 4px;">{{ number_format($totalQty, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div style="clear: both;"></div>

    <div class="footer-section">
        <div style="font-size: 8pt; margin-bottom: 10px;">
            <strong>Remarks :</strong> {{ $grn->remarks ?? '' }}
        </div>

        <div class="signature-box">
            <div class="signature-header">Received & Checked By</div>
            <div class="signature-content">
                <table class="signature-details" width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                    <tr>
                        <td rowspan="2" width="60%" style="vertical-align: top; padding: 8px 8px; border-right: 1px solid #333;">
                            <div style="font-size: 8pt; margin-bottom: 25px;">This is to acknowledge that the goods are received in good condition.</div>
                            
                            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                                <tr>
                                    <td width="45" style="border:none; padding: 4px 0; font-weight: bold; font-size: 8.5pt;">Signed</td>
                                    <td width="10" style="border:none; padding: 4px 0; font-size: 8.5pt;">:</td>
                                    <td style="border:none; padding: 4px 0; font-size: 8.5pt;">...................................................</td>
                                </tr>
                                <tr>
                                    <td style="border:none; padding: 2px 0; font-weight: bold; font-size: 8.5pt;">Name</td>
                                    <td style="border:none; padding: 2px 0; font-size: 8.5pt;">:</td>
                                    <td style="border:none; padding: 2px 0; font-size: 8.5pt;">...................................................</td>
                                </tr>
                            </table>
                        </td>
                        <td width="40%" style="padding: 10px; border-bottom: 1px solid #333;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                                <tr>
                                    <td width="40" style="border:none; padding: 5px 0; font-weight: bold;">Date</td>
                                    <td width="10" style="border:none; padding: 5px 0;">:</td>
                                    <td style="border:none; padding: 5px 0;">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
                                <tr>
                                    <td width="40" style="border:none; padding: 5px 0; font-weight: bold;">Time</td>
                                    <td width="10" style="border:none; padding: 5px 0;">:</td>
                                    <td style="border:none; padding: 5px 0;">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    @if(isset($is_print) && $is_print)
        <script>
            window.onload = function() {
                window.print();
            }
        </script>
    @endif
</body>
</html>

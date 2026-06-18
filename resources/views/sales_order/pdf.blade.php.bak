<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Sales Order - {{ $salesOrder->so_no }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
            vertical-align: top;
        }

        .item-table th,
        .item-table td {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-top: none;
            border-bottom: none;
            padding: 4px;
            text-align: left;
            vertical-align: top;
        }

        .no-border th,
        .no-border td {
            border: none;
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

        .header-table td {
            border: none;
        }

        .header-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            text-align: center;
        }

        .company-logo {
            max-width: 150px;
        }

        .qr-code {
            max-width: 100px;
        }

        .section-title {
            background-color: #f2f2f2;
            font-weight: bold;
            padding: 2px 5px;
            border: 1px solid #000;
        }

        .item-table {
            border-bottom: 1px solid #000;
        }

        .item-table th {
            background-color: #f2f2f2;
            text-align: center;
        }

        .item-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .item-table tbody tr:last-child td {
            border-bottom: 1px solid #000;
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
    <div class="container">
        <div style="position: relative;">
            <table class="header-table">
                <tr>
                    <td width="60%">
                        <table style="border: none;">
                            <tr>
                                <td style="border: none; vertical-align: top; width:25%;">
                                    <img src="{{ isset($is_print) && $is_print ? url('assets/images/jc_logo.png') : public_path('assets/images/jc_logo.png') }}"
                                        style="width: 150px;">
                                </td>
                                <td style="border: none; vertical-align: top; padding-left: 15px; width:75%;">
                                    <div style="font-size: 10px; line-height: 1.2;">
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
        <div class="header-title">Sales Order</div>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td width="50%"
                    style="padding: 0; vertical-align: top; border-right: 1px solid #000; border-bottom: 1px solid #000;">
                    <table class="no-border" style="margin: 0; width: 100%;">
                        <tr>
                            <td width="20%">Customer</td>
                            <td width="5%">:</td>
                            <td>
                                <strong>{{ $salesOrder->customer->name }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td>State</td>
                            <td>:</td>
                            <td>{{ $salesOrder->customer->state->state_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td>GSTIN</td>
                            <td>:</td>
                            <td>{{ $salesOrder->customer->gst_no ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </td>
                <td width="50%" style="padding: 0; vertical-align: top; border-bottom: 1px solid #000;">
                    <table class="no-border" style="margin: 0; width: 100%;">
                        <tr>
                            <td width="30%">Order No.</td>
                            <td width="5%">:</td>
                            <td width="65%">{{ $salesOrder->so_no }}</td>
                        </tr>
                        <tr>
                            <td>Order Date</td>
                            <td>:</td>
                            <td>{{ $salesOrder->so_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td>PO Ref.</td>
                            <td>:</td>
                            <td>{{ $salesOrder->customer_po_ref ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td>Sales Executive</td>
                            <td>:</td>
                            <td>{{ $salesOrder->salesAgent->name ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; border-top: none;">
            <tr>
                <td width="50%" style="border-right: 1px solid #000; padding: 5px; vertical-align: top;">
                    <div class="bold">Billing Address:</div>
                    <div style="margin-top: 2px;">{{ $salesOrder->billing_address }}</div>
                </td>
                <td width="50%" style="padding: 5px; vertical-align: top;">
                    <div class="bold">Shipping Address:</div>
                    <div style="margin-top: 2px;">{{ $salesOrder->shipping_address }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border-top: 1px solid #000; padding: 5px;">
                    <span class="bold">Payment Terms:</span> {{ $salesOrder->payment_terms ?? 'N/A' }}
                </td>
            </tr>
        </table>
        <table class="item-table" style="margin-top: 0;">
            <thead style="border-bottom: 1px solid #000;">
                <tr>
                    <th width="5%">S.No</th>
                    <th width="25%">Item Name</th>
                    <th width="12%">Color</th>
                    <th width="8%">Size</th>
                    <th width="12%">Art No</th>
                    <th width="8%">UOM</th>
                    <th width="10%">Quantity</th>
                    <th width="10%">MRP</th>
                    <th width="10%">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesOrder->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            @php
                                $codeToParse = $item->getAttributes()['item_name'] ?? $item->item_name ?? $item->finished_item_code ?? '';
                                
                                $brandName = '';
                                $styleName = '';
                                $sleeve = '';
                                $resolved = false;
                                
                                if (!empty($item->sleeve)) {
                                    $sleeveVal = is_array($item->sleeve) ? ($item->sleeve[0] ?? '') : $item->sleeve;
                                    $sleeveValUpper = strtoupper(trim($sleeveVal));
                                    if ($sleeveValUpper === 'FULL' || $sleeveValUpper === 'F/S' || $sleeveValUpper === 'FS') {
                                        $sleeve = 'F/S';
                                    } elseif ($sleeveValUpper === 'HALF' || $sleeveValUpper === 'H/S' || $sleeveValUpper === 'HS') {
                                        $sleeve = 'H/S';
                                    } else {
                                        $sleeve = $sleeveVal;
                                    }
                                }

                                if (!empty($codeToParse) && $codeToParse !== '-') {
                                    $parts = explode('-', $codeToParse);
                                    
                                    if (count($parts) >= 2) {
                                        $brandCode = trim($parts[0]);
                                        $styleCode = trim($parts[1]);
                                        
                                        $dbBrand = \App\Models\Brand::where('code', $brandCode)->first();
                                        $dbStyle = \App\Models\Style::where('code', $styleCode)->first();
                                        
                                        if ($dbBrand && $dbStyle) {
                                            $brandName = $dbBrand->brand_name;
                                            $styleName = $dbStyle->style_name;
                                            $resolved = true;
                                            
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
                                    }
                                    
                                    if (!$resolved && count($parts) >= 1) {
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
                                            $styleCode = substr($mainCode, strlen($brandCode));
                                            $dbStyle = \App\Models\Style::where('code', $styleCode)->first();
                                            if ($dbStyle) {
                                                $brandName = $dbBrand->brand_name;
                                                $styleName = $dbStyle->style_name;
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
                                }

                                if ($resolved) {
                                    $displayDescription = trim($brandName . ' ' . $styleName . ' ' . $sleeve);
                                } else {
                                    $displayDescription = $codeToParse;
                                }
                            @endphp
                            {{ $displayDescription }}
                        </td>
                        <td class="text-center">{{ $item->color->color_name ?? '-' }}</td>
                        <td class="text-center">{{ $item->size->size ?? $item->size_id ?? '-' }}</td>
                        <td class="text-center">{{ $item->art_no }}</td>
                        <td class="text-center">{{ $item->uom->uom_code ?? $item->uom_id ?? 'PCS' }}</td>
                        <td class="text-center">{{ number_format($item->qty, 2) }}</td>
                        <td class="text-right">{{ number_format($item->mrp, 2) }}</td>
                        <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach
                @for($i = count($salesOrder->items); $i < 10; $i++)
                    <tr>
                        <td style="height: 15px;">&nbsp;</td>
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
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="text-right bold">Total</td>
                    <td class="text-center bold">{{ number_format($salesOrder->total_qty, 2) }}</td>
                    <td></td>
                    <td class="text-right bold">{{ number_format($salesOrder->taxable_amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; border-top: none;">
            <tr>
                <td style="width: 60%; padding: 8px; vertical-align: top; border-right: 1px solid #000;">
                    <div class="amount-words" style="border-top: none; padding: 0;">
                        Amount in Words: <strong>{{ strtoupper($totalInWords) }}</strong>
                    </div>
                    <div style="margin-top: 10px;">
                        <strong>Remarks:</strong> {{ $salesOrder->internal_remarks ?? 'N/A' }}
                    </div>
                </td>
                <td style="width: 40%; padding: 0; vertical-align: top;">
                    <table class="no-border" style="width: 100%;">
                        <tr>
                            <td class="text-right" style="padding: 4px;">Taxable Amount:</td>
                            <td class="text-right" style="padding: 4px;">
                                {{ number_format($salesOrder->taxable_amount, 2) }}
                            </td>
                        </tr>
                        @if($salesOrder->igst_percent > 0)
                            <tr>
                                <td class="text-right" style="padding: 4px;">IGST ({{ $salesOrder->igst_percent }}%):</td>
                                <td class="text-right" style="padding: 4px;">{{ number_format($salesOrder->tax_amount, 2) }}
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td class="text-right" style="padding: 4px;">CGST ({{ $salesOrder->cgst_percent }}%):</td>
                                <td class="text-right" style="padding: 4px;">
                                    {{ number_format($salesOrder->tax_amount / 2, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right" style="padding: 4px;">SGST ({{ $salesOrder->sgst_percent }}%):</td>
                                <td class="text-right" style="padding: 4px;">
                                    {{ number_format($salesOrder->tax_amount / 2, 2) }}
                                </td>
                            </tr>
                        @endif
                        @if($salesOrder->discount_amount > 0)
                            <tr>
                                <td class="text-right" style="padding: 4px;">
                                    {{ $salesOrder->apply_box_discount ? 'Box Discount' : 'Discount' }}
                                    @if($salesOrder->discount_percent > 0)
                                        ({{ $salesOrder->discount_percent }}%)
                                    @endif:
                                </td>
                                <td class="text-right" style="padding: 4px;">
                                    -{{ number_format($salesOrder->discount_amount, 2) }}</td>
                            </tr>
                        @elseif($salesOrder->apply_box_discount)
                            <tr>
                                <td class="text-right" style="padding: 4px;">Box Discount:</td>
                                <td class="text-right" style="padding: 4px;">Without Implement</td>
                            </tr>
                        @endif
                        @if($salesOrder->commission_amount > 0)
                            <tr>
                                <td class="text-right" style="padding: 4px;">Commission
                                    ({{ $salesOrder->commission_percent }}%):</td>
                                <td class="text-right" style="padding: 4px;">
                                    {{ number_format($salesOrder->commission_amount, 2) }}
                                </td>
                            </tr>
                        @endif
                        @if($salesOrder->round_off != 0)
                            <tr>
                                <td class="text-right" style="padding: 4px;">Round Off ({{ $salesOrder->round_off_type }}):
                                </td>
                                <td class="text-right" style="padding: 4px;">
                                    {{ $salesOrder->round_off_type == 'Less' ? '-' : '+' }}{{ number_format($salesOrder->round_off, 2) }}
                                </td>
                            </tr>
                        @endif
                        <tr style="border-top: 1px solid #000; font-weight: bold;">
                            <td class="text-right" style="padding: 4px;">Grand Total:</td>
                            <td class="text-right" style="padding: 4px;">
                                {{ number_format($salesOrder->total_amount, 2) }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="no-border" style="margin-top: 30px; width: 100%;">
            <tr>
                <td width="50%">
                    <div style="font-size: 9px;">
                        <strong>Terms & Conditions:</strong><br>
                        {!! nl2br(e($salesOrder->terms_conditions ?? '1. Goods once sold will not be taken back.')) !!}
                    </div>
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
    </div>

    @if(isset($is_print) && $is_print)
        <script>
            window.onload = function () {
                window.print();
            }
        </script>
    @endif
</body>

</html>
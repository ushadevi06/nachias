<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order - {{ $purchaseOrder->po_number }}</title>
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
        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
            vertical-align: top;
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
        .no-border th, .no-border td {
            border: none;
        }
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .bold { font-weight: bold; }
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
            border-bottom:none;
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
                                    <img src="{{ isset($is_print) && $is_print ? asset('assets/images/jc_logo.png') : public_path('assets/images/jc_logo.png') }}" style="width: 150px;">
                                </td>
                                <td style="border: none; vertical-align: top; padding-left: 15px; width:75%;">
                                    <div style="font-size: 10px; line-height: 1.2;">
                                        {{ $setting->address }}
                                        <table style="width: 100%; border-collapse: collapse; margin-top: 2px;">
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
                </tr>
            </table>
        </div>
        <div class="header-title">Purchase Order</div>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td width="50%" style="padding: 0; vertical-align: top; border-right: 1px solid #000; border-bottom: none;">
                    <table class="no-border" style="margin: 0; width: 100%;">
                        <tr>
                            <td width="20%">Supplier</td>
                            <td width="5%">:</td>
                            <td>
                                <strong>{{ $purchaseOrder->supplier->name }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td>:</td>
                            <td>
                                @php
                                    $s = $purchaseOrder->supplier;
                                    $addr = array_filter([$s->address_line_1, $s->address_line_2, $s->address_line_3]);
                                    echo implode(', ', $addr);
                                    if($s->city) echo '<br>'.$s->city->city_name;
                                    if($s->state) echo ' - '.$s->state->state_name;
                                    if($s->zip_code) echo ' ('.$s->zip_code.')';
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
                    </table>
                </td>
                <td width="50%" style="padding: 0; vertical-align: top; border-bottom: none;">
                    <table class="no-border" style="margin: 0; width: 100%;">
                        <tr>
                            <td width="30%">PO No.</td>
                            <td width="5%">:</td>
                            <td width="65%">{{ $purchaseOrder->po_number }}</td>
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

        <table class="item-table" style="margin-top: 0;">
            <thead style="border-bottom: 1px solid #000; border-top:1px solid #000;">
                <tr>
                    <th width="3%">S.No</th>
                    <th width="12%">Store Category</th>
                    <th width="10%">Brand</th>
                    <th width="18%">Raw Material</th>
                    <th width="8%">Style</th>
                    <th width="8%">Color</th>
                    <th width="7%">Width</th>
                    <th width="6%">UOM</th>
                    <th width="8%">Quantity</th>
                    <th width="9%">Rate</th>
                    <th width="11%">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseOrder->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $item->storeCategory->category_name ?? '-' }}</td>
                        <td class="text-center">{{ $item->brand->brand_name ?? '-' }}</td>
                        <td><strong>{{ $item->rawMaterial->name ?? '' }}</strong></td>
                        <td class="text-center">{{ $item->style->style_name ?? '-' }}</td>
                        <td class="text-center">{{ $item->color->color_name ?? '-' }}</td>
                        <td class="text-center">{{ $item->fabricWidth->size ?? '-' }}</td>
                        <td class="text-center">{{ $item->uom->uom_code ?? '-' }}</td>
                        <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                        <td class="text-right">{{ number_format($item->rate, 2) }}</td>
                        <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach
                @for($i = count($purchaseOrder->items); $i < 10; $i++)
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
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                @endfor
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8" class="text-right bold">Total</td>
                    <td class="text-center bold">{{ number_format($purchaseOrder->total_qty, 2) }}</td>
                    <td></td>
                    <td class="text-right bold">{{ number_format($purchaseOrder->taxable_amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; border-top: none;">
            <tr>
                <td style="width: 60%; padding: 8px; vertical-align: top; border-right: 1px solid #000;">
                    <div class="amount-words" style="border-top: none; padding: 0;">
                        Amount in Words: <strong>{{ strtoupper($totalInWords) }}</strong>
                    </div>
                    @if(isset($purchaseOrder->remarks) && $purchaseOrder->remarks != '')
                    <div style="margin-top: 10px;">
                        <strong>Remarks:</strong> {{ $purchaseOrder->remarks }}
                    </div>
                    @endif
                </td>
                <td style="width: 40%; padding: 0; vertical-align: top;">
                    <table class="no-border" style="width: 100%;">
                        <tr>
                            <td class="text-left" style="padding: 4px;">Total Qty:</td>
                            <td class="text-right" style="padding: 4px;">{{ number_format($purchaseOrder->total_qty, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-left" style="padding: 4px;">Sub Total:</td>
                            <td class="text-right" style="padding: 4px;">{{ number_format($purchaseOrder->sub_total, 2) }}</td>
                        </tr>
                        @if($purchaseOrder->discount_amount > 0)
                        <tr>
                            <td class="text-left" style="padding: 4px;">Discount ({{ number_format($purchaseOrder->discount_percent, 2) }}%):</td>
                            <td class="text-right" style="padding: 4px;">-{{ number_format($purchaseOrder->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-left" style="padding: 4px;">Taxable Amount:</td>
                            <td class="text-right" style="padding: 4px;">{{ number_format($purchaseOrder->taxable_amount, 2) }}</td>
                        </tr>
                        @if($purchaseOrder->other_state)
                        <tr>
                            <td class="text-left" style="padding: 4px;">IGST ({{ $purchaseOrder->igst_percent }}%):</td>
                            <td class="text-right" style="padding: 4px;">{{ number_format($purchaseOrder->tax_amount, 2) }}</td>
                        </tr>
                        @else
                        <tr>
                            <td class="text-left" style="padding: 4px;">CGST ({{ $purchaseOrder->cgst_percent }}%):</td>
                            <td class="text-right" style="padding: 4px;">{{ number_format($purchaseOrder->tax_amount/2, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-left" style="padding: 4px;">SGST ({{ $purchaseOrder->sgst_percent }}%):</td>
                            <td class="text-right" style="padding: 4px;">{{ number_format($purchaseOrder->tax_amount/2, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-left" style="padding: 4px;">Round Off ({{ $purchaseOrder->round_off_type }}):</td>
                            <td class="text-right" style="padding: 4px;">{{ $purchaseOrder->round_off_type == 'Less' ? '-' : '+' }}{{ number_format($purchaseOrder->round_off, 2) }}</td>
                        </tr>
                        <tr style="border-top: 1px solid #000; font-weight: bold;">
                            <td class="text-left" style="padding: 4px;">Grand Total:</td>
                            <td class="text-right" style="padding: 4px;">{{ number_format($purchaseOrder->total_amount, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="no-border" style="margin-top: 30px; width: 100%;">
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
                    <div style="border: 1px solid #000; border-radius: 2px; text-align: center; height: 80px; width: 200px; display: inline-block;">
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
        window.onload = function() {
            window.print();
        }
    </script>
    @endif
</body>
</html>

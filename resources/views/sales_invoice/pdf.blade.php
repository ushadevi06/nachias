<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Invoice - {{ $invoice->inv_no }}</title>
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
        .company-logo {
            max-width: 150px;
        }
        .qr-code {
            max-width: 100px;
        }
        .original-stamp {
            border: 2px solid #000;
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
        .summary-table td {
            border: none;
            padding: 2px 5px;
        }
        .summary-table .label { width: 60%; }
        .summary-table .value { width: 40%; text-align: right; }
        .footer-note {
            font-size: 9px;
            margin-top: 10px;
        }
        .bank-details {
            margin-top: 10px;
            border: 1px solid #000;
            padding: 5px;
        }
        .signature-box {
            margin-top: 20px;
            text-align: right;
        }
        .signature-area {
            border: 1px solid #000;
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
            border: 1px solid #000;
            border-top: none;
            width: 100%;
        }

        .bottom-table {
            width: 100%;
            border-collapse: collapse;
        }

        .bottom-table td {
            border-right: 1px solid #000;
            vertical-align: top;
            padding: 6px;
            font-size: 10px;
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
        }

        .summary-box .label {
            text-align: left;
        }

        .summary-box .value {
            text-align: right;
        }

        .summary-box .total-row {
            border-top: 1px solid #000;
            font-weight: bold;
        }

        .amount-words {
            border-top: 1px solid #000;
            padding: 5px;
            font-weight: bold;
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
                                <td style="border: none; vertical-align: top; width:30%;">
                                    @php
                                        $logoPath = public_path('assets/images/jc_logo.png');
                                        $logoBase64 = '';
                                        if (file_exists($logoPath)) {
                                            $logoData = file_get_contents($logoPath);
                                            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
                                        }
                                    @endphp
                                    @if($logoBase64)
                                        <img src="{{ $logoBase64 }}" style="width: 140px;">
                                    @else
                                        <img src="{{ isset($is_print) && $is_print ? asset('assets/images/jc_logo.png') : public_path('assets/images/jc_logo.png') }}" style="width: 140px;">
                                    @endif
                                </td>
                                <td style="border: none; vertical-align: top; padding-left: 15px; width:75%;">
                                    <div style="font-size: 11px; line-height: 1.3;">
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
                    <td width="40%" class="text-right" style="vertical-align: top;">
                        <div style="margin-right: 10px;">ORIGINAL</div>
                        <img src="{{ isset($is_print) && $is_print ? asset('assets/images/qr_code.png') : public_path('assets/images/qr_code.png') }}" class="qr-code">
                    </td>
                </tr>
            </table>
        </div>
        <div class="header-title">Tax Invoice</div>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td width="50%" style="padding: 0; vertical-align: top; border-right: 1px solid #000; border-bottom: 1px solid #000;">
                    <table class="no-border" style="margin: 0; width: 100%;">
                        <tr>
                            <td width="10%">Bill To</td>
                            <td width="5%">:</td>
                            <td>
                                <span>ANANTHAM RETAIL PVT.LTD KUMBAKONAM</span><br>
                                8/329, AYEKULAM ROAD,<br>
                                KUMBAKONAM-612001
                            </td>
                        </tr>
                        <tr>
                            <td width="20%">State</td>
                            <td width="5%">:</td>
                            <td width="75%">Tamil Nadu(33)</td>
                        </tr>
                        <tr>
                            <td width="20%">33AAQCA6068B1ZR</td>
                            <td width="5%"></td>
                            <td width="75%"></td>
                        </tr>
                    </table>
                </td>
                <td width="50%" style="padding: 0; vertical-align: top; border-bottom: 1px solid #000;">
                    <table class="no-border" style="margin: 0; width: 100%;">
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
                    <table class="no-border" style="margin: 0; width: 100%;">
                        <tr>
                            <td width="20%">Ship To</td>
                            <td width="5%">:</td>
                            <td>
                                <span>ANANTHAM RETAIL PVT.LTD KUMBAKONAM</span><br>
                                8/329, AYEKULAM ROAD,<br>
                                KUMBAKONAM-612001
                            </td>
                        </tr>
                        <tr>
                            <td width="20%">State</td>
                            <td width="5%">:</td>
                            <td width="75%">Tamil Nadu(33)</td>
                        </tr>
                        <tr>
                            <td width="20%">33AAQCA6068B1ZR</td>
                            <td width="5%"></td>
                            <td width="75%"></td>
                        </tr>
                    </table>
                </td>
                <td width="50%" style="padding: 0; vertical-align: top;">
                    <table class="no-border" style="margin: 0; width: 100%;">
                        <tr>
                            <td width="30%">Transport</td>
                            <td width="5%">:</td>
                            <td width="65%">{{ $invoice->salesOrder->transporter_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td>Doc No.</td>
                            <td>:</td>
                            <td>{{ $invoice->inv_no }}</td>
                        </tr>
                        <tr>
                            <td>Sales Group</td>
                            <td>:</td>
                            <td>ZONE-2</td>
                        </tr>
                        <tr>
                            <td>Sales Person</td>
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
        <table class="item-table" style="margin-top: 0;">
            <thead style="border-bottom: 1px solid #000;">
                <tr>
                    <th width="5%">S.No</th>
                    <th width="35%">Description</th>
                    <th width="8%">Size</th>
                    <th width="10%">Art</th>
                    <th width="8%">Tax (%)</th>
                    <th width="6%">UOM</th>
                    <th width="8%">Quantity</th>
                    <th width="8%">MRP</th>
                    <th width="14%">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            {{ $item->brandCategory->category_name ?? '' }} 
                            {{ $item->item->name ?? '' }}
                            @if($item->sleeve_type == 'Full') (F/S) @endif
                            @if($item->sleeve_type == 'Half') (H/S) @endif
                        </td>
                        <td class="text-center">{{ $item->size }}</td>
                        <td class="text-center">{{ $item->art_no }}</td>
                        <td class="text-center">{{ $invoice->other_state ? $invoice->igst_percent : ($invoice->cgst_percent + $invoice->sgst_percent) }}</td>
                        <td class="text-center">{{ $item->uom->uom_code ?? 'PCS' }}</td>
                        <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                        <td class="text-right">{{ number_format($item->mrp, 2) }}</td>
                        <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach
                @for($i = count($invoice->items); $i < 10; $i++)
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
                    <td colspan="6" class="text-right bold"></td>
                    <td class="text-center bold">{{ number_format($invoice->items->sum('quantity'), 2) }}</td>
                    <td class="text-right">Gross</td>
                    <td class="text-right bold">{{ number_format($invoice->sub_total, 2) }}</td>
                </tr>
            </tfoot>
        </table>
        <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; border-top: none;">
            <tr>
                <td style="width: 70%; padding: 0; vertical-align: top; border-right: 1px solid #000; border-bottom: none; border-top:none;">
                    <div style="padding: 4px;">
                        IRN: 72223e1c6b46388af6b4fe64ff31c510238217d57fc92b60e9332d7979706dd1<br>
                        Ack No.: 152523983271642 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Eway Bill No. : 
                    </div>
                    <table style="width: 100%; border-collapse: collapse; border-top: 1px solid #000;">
                        <tr>
                            <td style="width: 75%; padding: 4px; border-right: 1px solid #000; vertical-align: top; border-bottom: none; border-left: none;">
                                Company's Bank Details :<br>
                                Bank Name : HDFC BANK<br>
                                A/C No. : XXXXXXXX<br>
                                Branch & IFS Code : HDFC0000002
                            </td>
                            <td style="width: 25%; padding: 4px; vertical-align: top; text-align: center; border-bottom: none; border-right: none;">
                                <span style="font-weight: bold;">For UPI Payment</span><br>
                                <img src="{{ isset($is_print) && $is_print ? asset('assets/images/qr_code.png') : public_path('assets/images/qr_code.png') }}" style="max-width: 85px; margin-top: 0px;">
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 18%; padding: 0; vertical-align: top; border-bottom: none; border-top:none; border-right: 1px solid #000;">
                    <table style="width: 100%; border-collapse: collapse;">
                        @if(isset($invoice->discount) && $invoice->discount > 0)
                        <tr><td style="border: none; padding: 2px 4px; text-align: right;">Discount({{ $invoice->discount_percent }}%)</td></tr>
                        @endif
                        <tr><td style="border: none; padding: 2px 4px; text-align: right;">Taxable Value</td></tr>
                        @if(!$invoice->other_state)
                        <tr><td style="border: none; padding: 2px 4px; text-align: right;">OUTPUT CGST</td></tr>
                        <tr><td style="border: none; padding: 2px 4px; text-align: right;">OUTPUT SGST</td></tr>
                        @else
                        <tr><td style="border: none; padding: 2px 4px; text-align: right;">OUTPUT IGST</td></tr>
                        @endif
                        <tr><td style="border: none; padding: 2px 4px; text-align: right;">Round Off</td></tr>
                    </table>
                </td>
                <td style="width: 12%; padding: 0; vertical-align: top; border-bottom: none; border-top:none;">
                    <table style="width: 100%; border-collapse: collapse;">
                        @if(isset($invoice->discount) && $invoice->discount > 0)
                        <tr><td style="border: none; padding: 2px 4px; text-align: right;">{{ number_format($invoice->discount, 2) }}</td></tr>
                        @endif
                        <tr><td style="border: none; padding: 2px 4px; text-align: right;">{{ number_format($invoice->taxable_amount ?? $invoice->sub_total, 2) }}</td></tr>
                        @if(!$invoice->other_state)
                        <tr><td style="border: none; padding: 2px 4px; text-align: right;">{{ number_format($invoice->cgst, 2) }}</td></tr>
                        <tr><td style="border: none; padding: 2px 4px; text-align: right;">{{ number_format($invoice->sgst, 2) }}</td></tr>
                        @else
                        <tr><td style="border: none; padding: 2px 4px; text-align: right;">{{ number_format($invoice->igst, 2) }}</td></tr>
                        @endif
                        <tr><td style="border: none; padding: 2px 4px; text-align: right;">{{ (in_array(strtolower($invoice->round_off_type ?? ''), ['less', 'minus']) ? ' - ' : '') . number_format($invoice->round_off ?? 0, 2) }}</td></tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="width: 70%; padding: 8px; border-right: none;">
                    Rupees &nbsp;&nbsp;&nbsp;: {{ strtoupper($totalInWords) }}
                </td>
                <td style="width: 18%; padding: 4px; font-weight: bold; text-align: right; border-right: 1px solid #000; border-top:none;">
                    Total
                </td>
                <td style="width: 12%; padding: 4px; font-weight: bold; text-align: right; border-left: none; border-top:none;">
                    {{ number_format($invoice->total, 2) }}
                </td>
            </tr>
        </table>
        <div class="bold" style="margin-top: 5px; display: none;">Amount of Tax(in words) : {{ $totalTaxInWords }}</div>
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
                        <td class="text-center">{{ $hsn }}</td>
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
                <tr>
                    <td colspan="2" class="text-right">Total</td>
                    <td class="text-right">{{ number_format($invoice->taxable_amount ?? $invoice->sub_total, 2) }}</td>
                    @if(!$invoice->other_state)
                    <td></td>
                    <td class="text-right">{{ number_format($invoice->cgst, 2) }}</td>
                    <td></td>
                    <td class="text-right">{{ number_format($invoice->sgst, 2) }}</td>
                    <td></td>
                    <td></td>
                    @else
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-right bold">{{ number_format($invoice->igst, 2) }}</td>
                    @endif
                </tr>
                <tr>
                    <td colspan="9" style="border-top: 1px solid #000; padding: 4px; text-align: left; border-bottom: 1px solid #000;">
                        Amount of Tax(in words) &nbsp;&nbsp;&nbsp;: {{ strtoupper($totalTaxInWords) }}
                    </td>
                </tr>
            </tfoot>
        </table>
        <div style="margin-top: 4px; padding-left: 4px;">
            <span style="font-size: 11px;">Additional Notes &nbsp;&nbsp;: {{ $invoice->notes ?? 'order axe no 100004896' }}</span>
        </div>
        <table class="no-border" style="margin-top: 15px; width: 100%;">
            <tr>
                <td width="60%" style="vertical-align: top; padding-left: 4px;">
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
                        <div style="padding-top: 5px; font-size: 10px;">For Nachias Fashion Private Limited</div>
                        
                        <div style="position: absolute; bottom: 0; width: 100%; border-top: 1px dotted #000; padding: 4px 0; font-size: 10px;">
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

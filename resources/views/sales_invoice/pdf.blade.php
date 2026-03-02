<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tax Invoice - {{ $invoice->inv_no }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 20px;
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
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .header-table td {
            border: none;
        }
        .header-title {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 5px;
            text-align: center;
        }
        .company-logo {
            max-width: 150px;
        }
        .qr-code {
            max-width: 80px;
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
                                    <img src="{{ public_path('assets/images/jc_logo.png') }}" style="width: 180px;">
                                </td>
                                <td style="border: none; vertical-align: top; padding-left: 15px; width:70%;">
                                    <div style="font-size: 12px;">
                                        {{ $setting->address }}<br>
                                        Mobile: {!! implode(',', array_map('trim', explode(',', $setting->toll_free_no ?? ''))) !!}<br>
                                        Email: {!! implode(',', array_map('trim', explode(',', $setting->email ?? ''))) !!}<br>
                                        <span>GSTIN: {{ $setting->gst_no ?? '' }}</span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="40%" class="text-right" style="vertical-align: top;">
                        <div style="margin-right: 20px;">ORIGINAL</div>
                        <img src="{{ public_path('assets/images/qr_code.png') }}" class="qr-code">
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
                            <td width="20%">Bill To</td>
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
                            <td width="30%" class="bold">Invoice No.</td>
                            <td width="5%">:</td>
                            <td width="65%">{{ $invoice->inv_no }}</td>
                        </tr>
                        <tr>
                            <td class="bold">Invoice Date</td>
                            <td>:</td>
                            <td>{{ $invoice->inv_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="bold">Order No.</td>
                            <td>:</td>
                            <td>{{ $invoice->salesOrder->so_no ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="bold">Destination</td>
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
                            <td width="30%" class="bold">Transport</td>
                            <td width="5%">:</td>
                            <td width="65%">{{ $invoice->salesOrder->transporter_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="bold">Doc No.</td>
                            <td>:</td>
                            <td>{{ $invoice->inv_no }}</td>
                        </tr>
                        <tr>
                            <td class="bold">Sales Group</td>
                            <td>:</td>
                            <td>ZONE-2</td>
                        </tr>
                        <tr>
                            <td class="bold">Sales Person</td>
                            <td>:</td>
                            <td>{{ $invoice->salesOrder->salesAgent->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="bold">Total Pkgs</td>
                            <td>:</td>
                            <td>{{ count($invoice->items) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Item Table -->
        <table class="item-table" style="margin-top: 0;">
            <thead>
                <tr>
                    <th width="5%">S.No</th>
                    <th width="30%">Description</th>
                    <th width="8%">Size</th>
                    <th width="10%">Art</th>
                    <th width="10%">HSN/SAC</th>
                    <th width="7%">Tax (%)</th>
                    <th width="5%">UOM</th>
                    <th width="8%">Quantity</th>
                    <th width="8%">MRP</th>
                    <th width="8%">Price</th>
                    <th width="12%">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            {{ $item->brandCategory->category_name ?? '' }} 
                            {{ $item->item->name ?? '' }}
                            @if($item->sleeve_type) ({{ $item->sleeve_type }}) @endif
                        </td>
                        <td class="text-center">{{ $item->size }}</td>
                        <td class="text-center">{{ $item->art_no }}</td>
                        <td class="text-center">{{ $item->hsn_sac }}</td>
                        <td class="text-center">{{ $invoice->other_state ? $invoice->igst_percent : ($invoice->cgst_percent + $invoice->sgst_percent) }}%</td>
                        <td class="text-center">{{ $item->uom->uom_code ?? 'PCS' }}</td>
                        <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                        <td class="text-right">{{ number_format($item->mrp, 2) }}</td>
                        <td class="text-right">{{ number_format($item->rate, 2) }}</td>
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
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                @endfor
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7" class="text-right bold">Total</td>
                    <td class="text-center bold">{{ number_format($invoice->items->sum('quantity'), 2) }}</td>
                    <td colspan="2"></td>
                    <td class="text-right bold">{{ number_format($invoice->sub_total, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    
        <!-- Calculation Summary -->
        <div class="bottom-section">
            <table class="bottom-table">
                <tr>
                    <!-- LEFT MAIN COLUMN -->
                    <td width="65%" style="padding:0;">

                        <table style="width:100%; border-collapse:collapse;">
                            <tr>
                                <!-- LEFT TEXT AREA -->
                                <td width="65%" style="vertical-align:top; padding:8px;">

                                    <div><strong>IRN:</strong> 72223e1c6b46388af6b4fe64ff31c510238217d57fc92b60e9332d7979706dd1</div>
                                    <div style="margin-top:4px;">
                                        <strong>Ack No.:</strong> 152523983271642
                                    </div>
                                    <div style="margin-top:4px;">
                                        <strong>Eway Bill No.:</strong>
                                    </div>

                                    <hr style="margin:8px 0; border:0; border-top:1px solid #000;">

                                    <div>
                                        <strong>Company's Bank Details :</strong><br>
                                        Bank Name : HDFC BANK<br>
                                        A/C No. : XXXXXXXX<br>
                                        Branch & IFSC Code : HDFC0000002
                                    </div>

                                </td>

                                <!-- QR COLUMN -->
                                <td width="35%" style="vertical-align:top; text-align:center; border-left:1px solid #000; padding:8px;">
                                    <div><strong>For UPI Payment</strong></div>
                                    <img src="{{ public_path('assets/images/qr_code.png') }}" width="100">
                                </td>
                            </tr>
                        </table>

                    </td>

                    <!-- SUMMARY COLUMN -->
                    <td width="35%" style="vertical-align:top; padding:8px; border-left:1px solid #000;">
                        <div class="summary-box">
                            <table>
                                <tr>
                                    <td class="label">Gross</td>
                                    <td class="value">8490.00</td>
                                </tr>
                                <tr>
                                    <td class="label">Discount (7%)</td>
                                    <td class="value">594.30</td>
                                </tr>
                                <tr>
                                    <td class="label">Taxable Value</td>
                                    <td class="value">7895.70</td>
                                </tr>
                                <tr>
                                    <td class="label">OUTPUT CGST</td>
                                    <td class="value">197.39</td>
                                </tr>
                                <tr>
                                    <td class="label">OUTPUT SGST</td>
                                    <td class="value">197.39</td>
                                </tr>
                                <tr>
                                    <td class="label">Round Off</td>
                                    <td class="value">-0.48</td>
                                </tr>
                                <tr class="total-row">
                                    <td class="label">Total</td>
                                    <td class="value">8290.00</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>

            <!-- FULL WIDTH AMOUNT IN WORDS -->
            <div class="amount-words">
                Rupees : EIGHT THOUSAND TWO HUNDRED NINETY RUPEES ONLY
            </div>
        </div>

        <div class="bold" style="margin-top: 5px;">Amount in words : {{ $totalInWords }}</div>
        <div class="bold" style="margin-top: 5px;">Amount of Tax(in words) : {{ $totalTaxInWords }}</div>

        <!-- IRN and Acknowledgement -->
        <div style="margin-top: 10px;">
            <div class="bold">IRN: {{ str_repeat('x', 64) }}</div>
            <div class="bold">Ack No.: 152523983271642 &nbsp;&nbsp; Eway Bill No. : </div>
        </div>

        <!-- Tax Summary Table -->
        <table class="item-table" style="margin-top: 10px;">
            <thead>
                <tr>
                    <th rowspan="2" width="5%">S.No</th>
                    <th rowspan="2" width="15%">HSN/SAC</th>
                    <th rowspan="2" width="15%">Taxable Value</th>
                    <th colspan="2" width="20%">OUTPUT CGST</th>
                    <th colspan="2" width="20%">OUTPUT SGST</th>
                    <th colspan="2" width="20%">IGST</th>
                </tr>
                <tr>
                    <th>(%)</th>
                    <th>Amount</th>
                    <th>(%)</th>
                    <th>Amount</th>
                    <th>(%)</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @php $i = 1; @endphp
                @foreach($taxSummary as $hsn => $summary)
                    <tr>
                        <td class="text-center">{{ $i++ }}</td>
                        <td class="text-center">{{ $hsn }}</td>
                        <td class="text-right">{{ number_format($summary['taxable_value'], 2) }}</td>
                        <td class="text-center">{{ $summary['cgst_rate'] }}</td>
                        <td class="text-right">{{ number_format($summary['cgst_amount'], 2) }}</td>
                        <td class="text-center">{{ $summary['sgst_rate'] }}</td>
                        <td class="text-right">{{ number_format($summary['sgst_amount'], 2) }}</td>
                        <td class="text-center">{{ $summary['igst_rate'] }}</td>
                        <td class="text-right">{{ number_format($summary['igst_amount'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-right bold">Total</td>
                    <td class="text-right bold">{{ number_format($invoice->total, 2) }}</td>
                    <td></td>
                    <td class="text-right bold">{{ number_format($invoice->cgst, 2) }}</td>
                    <td></td>
                    <td class="text-right bold">{{ number_format($invoice->sgst, 2) }}</td>
                    <td></td>
                    <td class="text-right bold">{{ number_format($invoice->igst, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        
        <div style="margin-top: 10px;">
            <span class="bold">Remarks :</span> {{ $invoice->remarks ?? 'N/A' }}
        </div>

        <!-- Footer -->
        <table class="no-border" style="margin-top: 10px;">
            <tr>
                <td width="60%">
                    <!-- Terms & Conditions already moved above -->
                </td>
                <td width="40%" class="text-right">
                    <div class="signature-box">
                        <div class="bold">For {{ $setting->company_name ?? 'Nachias Fashion Private Limited' }}</div>
                        <div class="signature-area">
                            <div class="signature-label">Authorised Signatory</div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

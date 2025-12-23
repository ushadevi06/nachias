<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Purchase Invoice</title>
    <style>
        @page { margin: 20px 25px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #6a1b9a;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        
        .header .logo {
            flex: 0 0 35%;
        }
        
        .header .company-details {
            flex: 0 0 60%;
            text-align: right;
        }
        
        .header img {
            width: 90px;
            height: auto;
        }

        .company-details h2 {
            margin: 0;
            color: #6a1b9a;
            font-size: 16px;
        }
        
        .company-details p {
            margin: 1px 0;
            font-size: 10px;
        }
        
        .invoice-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            color: #6a1b9a;
            margin: 12px 0;
            padding: 4px 0;
            border-top: 1px solid #6a1b9a;
            border-bottom: 1px solid #6a1b9a;
        }
        
        .info-section {
            width: 100%;
            margin: 8px 0;
        }
        
        .info-section td {
            padding: 1px 4px;
            vertical-align: top;
            font-size: 10px;
        }
        
        .info-section strong {
            color: #000;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }
        
        table.items th {
            background: #6a1b9a;
            color: #fff;
            text-align: center;
            padding: 6px 4px;
            font-size: 10px;
            font-weight: bold;
        }
        
        table.items td {
            border: 1px solid #ccc;
            padding: 5px 4px;
            text-align: center;
            font-size: 10px;
        }
        
        table.items tr:nth-child(even) {
            background: #f8f8f8;
        }
        
        .totals {
            margin-top: 12px;
            width: 100%;
            font-size: 10px;
        }
        
        .totals td {
            padding: 3px;
        }
        
        .totals .label {
            text-align: right;
            font-weight: bold;
            width: 70%;
        }
        
        .totals .value {
            text-align: right;
            color: #6a1b9a;
            width: 30%;
        }
        
        .details-box {
            width: 100%;
            border: 1px solid #ccc;
            margin-top: 12px;
            border-radius: 4px;
            padding: 8px;
            font-size: 10px;
        }
        
        .details-box h4 {
            margin: 0 0 6px 0;
            color: #6a1b9a;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
            font-size: 11px;
        }
        
        .details-box p {
            margin: 2px 0;
        }
        
        .signature {
            margin-top: 25px;
            text-align: right;
        }
        
        .signature img {
            width: 80px;
            height: auto;
        }
        
        .signature p {
            margin-top: 3px;
            font-weight: bold;
            font-size: 10px;
        }
        
        .terms {
            margin-top: 15px;
            font-size: 9px;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
        
        .terms ol {
            margin: 3px 0;
            padding-left: 15px;
        }
        
        .terms li {
            margin-bottom: 2px;
        }
        
        .footer {
            position: fixed;
            bottom: 10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 3px;
        }
        
        .compact-row {
            margin: 0;
            padding: 0;
        }
        
        .section-spacing {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <!-- Header - Compact -->
    <table width="100%" style="border-bottom:2px solid #6a1b9a;">
        <tr>
            <td style="width:35%; vertical-align:top;">
                <img src="{{ public_path('assets/images/logo.jpg') }}" alt="Company Logo" style="width: 110px;">
            </td>
            <td style="width:65%; text-align: right; vertical-align: top;">
                <h2 style="margin: 0; color: #6a1b9a; font-size: 18px;">
                    {{ env('WEBSITE_NAME', 'Your Company Name') }}
                </h2>
                <p style="margin: 2px 0; font-size: 11px;">No.157, Muniyandi Kovil Street, Madurai - 625016.</p>
                <p style="margin: 2px 0; font-size: 11px;">Email: casinoshirts2000@gmail.com</p>
                <p style="margin: 2px 0; font-size: 11px;">Phone: +91 98765 43210</p>
                <p style="margin: 2px 0; font-size: 11px;">GSTIN: 33AADCN9342A1ZU</p>
            </td>
        </tr>
    </table>

    <!-- Invoice Title -->
    <div class="invoice-title">Purchase Invoice</div>

    <!-- Invoice Info - Compact -->
    <table class="info-section">
        <tr>
            <!-- Left Column -->
            <td style="width:48%; vertical-align:top;">
                <p class="compact-row"><strong>Invoice No:</strong> PO-2025-001</p>
                <p class="compact-row"><strong>Date:</strong> 19-Sep-2025</p>
                <p class="compact-row"><strong>Transport:</strong> Uttam Road Ways Pvt</p>
                <p class="compact-row"><strong>LR No:</strong> 23062138</p>
            </td>

            <!-- Right Column -->
            <td style="width:52%; vertical-align:top;">
                <table width="100%" style="border-collapse: collapse; font-size: 10px;">
                    <tr>
                        <td colspan="2" style="color:#6a1b9a; font-weight:bold; padding-bottom:2px;">
                            Supplier Details
                        </td>
                    </tr>
                    <tr>
                        <td style="width:35%; vertical-align:top; color:#000;"><strong>Name:</strong></td>
                        <td style="vertical-align:top;">Krishna Fabrics (SUP001)</td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top; color:#000;"><strong>Address:</strong></td>
                        <td style="vertical-align:top;">
                            No. 10, Textile Market Road,<br>
                            Tirupur – 641602,<br>
                            TamilNadu.
                        </td>
                    </tr>
                    <tr>
                        <td style="color:#000;"><strong>GSTIN:</strong></td>
                        <td>33ABCDE1234F1Z5</td>
                    </tr>
                    <tr>
                        <td style="color:#000;"><strong>Broker:</strong></td>
                        <td>Bhagwan Textiles Agency</td>
                    </tr>
                    <tr>
                        <td style="color:#000;"><strong>Destination:</strong></td>
                        <td>Chennai</td>
                    </tr>
                    <tr>
                        <td style="color:#000;"><strong>Payment Mode:</strong></td>
                        <td>Bank Transfer</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Items Table - Compact -->
    <table class="items">
        <thead>
            <tr>
                <th style="width:5%">S.No</th>
                <th style="width:45%">Item</th>
                <th style="width:15%">HSN Code</th>
                <th style="width:10%">Qty</th>
                <th style="width:12%">Rate</th>
                <th style="width:13%">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Men's Casual Denim Shirt</td>
                <td>D45SD/72-23</td>
                <td>3</td>
                <td>₹50.00</td>
                <td>₹150.00</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Men's Formal Cotton Shirt</td>
                <td>Q9KD34-23</td>
                <td>1</td>
                <td>₹25.00</td>
                <td>₹25.00</td>
            </tr>
        </tbody>
    </table>

    <!-- Financial Details in Compact Layout -->
    <div style="display: flex; gap: 10px; margin-top: 10px;">

        <!-- Totals -->
        <div style="flex: 1;">
            <table class="totals">
                <tr>
                    <td class="label">Sub Total:</td>
                    <td class="value">₹175.00</td>
                </tr>
                <tr>
                    <td class="label">Discount (2%):</td>
                    <td class="value">₹3.50</td>
                </tr>
                <tr>
                    <td class="label">Taxable Amount:</td>
                    <td class="value">₹171.50</td>
                </tr>
                <tr>
                    <td class="label">CGST (9%):</td>
                    <td class="value">₹15.44</td>
                </tr>
                <tr>
                    <td class="label">SGST (9%):</td>
                    <td class="value">₹15.44</td>
                </tr>
                <tr>
                    <td class="label">Total GST:</td>
                    <td class="value">₹30.88</td>
                </tr>
                <tr>
                    <td class="label">Freight Charges:</td>
                    <td class="value">₹100.00</td>
                </tr>
                <tr>
                    <td class="label" style="border-top:2px solid #6a1b9a;">Grand Total:</td>
                    <td class="value" style="border-top:2px solid #6a1b9a;"><strong>₹302.38</strong></td>
                </tr>
            </table>
            <p style="font-size:10px; margin-top:4px; font-weight:bold; color:#000;">
                Amount in Words: 
                <span style="color:#6a1b9a;">
                    Rupees Three Hundred Two and Thirty-Eight Paise Only
                </span>
            </p>

        </div>
    </div>
    <!-- Bank Details - Table Format -->
    <table width="100%" style="border: 1px solid #ccc; border-radius: 4px; margin-top: 12px; font-size: 10px; border-collapse: collapse;">
        <tr>
            <td colspan="4" style="background: #6a1b9a; color: #fff; font-weight: bold; padding: 5px; text-align: left;">
                Bank Details
            </td>
        </tr>
        <tr>
            <td style="width: 25%; padding: 5px;"><strong>Bank Name:</strong></td>
            <td style="width: 25%; padding: 5px;">HDFC Bank</td>
            <td style="width: 25%; padding: 5px;"><strong>Account Name:</strong></td>
            <td style="width: 25%; padding: 5px;">Krishna Fabrics</td>
        </tr>
        <tr>
            <td style="padding: 5px;"><strong>Account Number:</strong></td>
            <td style="padding: 5px;">123456789012</td>
            <td style="padding: 5px;"><strong>IFSC Code:</strong></td>
            <td style="padding: 5px;">HDFC0001234</td>
        </tr>
        <tr>
            <td style="padding: 5px;"><strong>Branch:</strong></td>
            <td style="padding: 5px;">T. Nagar, Chennai</td>
            <td style="padding: 5px;"><strong>Payment Mode:</strong></td>
            <td style="padding: 5px;">Bank Transfer</td>
        </tr>
    </table>

    <!-- Bank Details - Compact -->
    <table width="100%" style="margin-top: 20px; border-top: 1px solid #ccc; font-size: 9px;">
        <tr>
            <!-- Left: Terms & Conditions -->
            <td style="width: 65%; vertical-align: top; padding: 5px;">
                <strong style="color:#6a1b9a;">Terms & Conditions:</strong>
                <ol style="margin: 3px 0; padding-left: 15px;">
                    <li>Goods once sold will not be taken back.</li>
                    <li>Payment due within 15 days of invoice date.</li>
                    <li>Interest @18% p.a. will be charged on overdue accounts.</li>
                    <li>All disputes subject to Chennai jurisdiction only.</li>
                </ol>
            </td>

            <!-- Right: Authorized Signature -->
            <td style="width: 35%; text-align: right; vertical-align: bottom; padding: 5px;">
                <img src="{{ public_path('assets/images/signature_images.jpg') }}" 
                    alt="Authorized Signature" 
                    style="width: 90px; height: auto; margin-bottom: 4px;">
                <p style="margin: 0; font-weight: bold; font-size: 10px;">Authorized Signatory</p>
            </td>
        </tr>
    </table>
    <!-- Footer -->
    <div class="footer">
        <p>Nachias.</p>
    </div>
</body>
</html>
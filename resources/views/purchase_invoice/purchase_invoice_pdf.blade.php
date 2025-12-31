<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Tax Invoice</title>
    <style>
        @page { 
            margin: 15px 20px; 
            size: A4;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            color: #000;
            line-height: 1.2;
        }
        
        /* Main Wrapper Margin */
        .pdf-section {
            margin: 10px;
        }
        
        /* Consistent spacing between sections */
        .pdf-section > div, 
        .pdf-section > table {
            margin: 0;
            width: 100%; /* Ensure full width */
        }
        
        /* Header Section */
        .header-container {
            border: 1px solid #000;
        }
        .header-row {
            display: table;
            width: 100%;
            border-bottom: 1px solid #000;
        }
        .header-left {
            display: table-cell;
            width: 70%;
            padding: 5px;
            vertical-align: top;
            border-right: 1px solid #000;
        }
        .header-right {
            display: table-cell;
            width: 30%;
            padding: 5px;
            vertical-align: top;
            text-align: center;
        }
        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .company-details {
            font-size: 8px;
            line-height: 1.3;
        }
        .tax-invoice-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .qr-placeholder {
            width: 80px;
            height: 80px;
            border: 1px solid #000;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7px;
        }
        
        /* Invoice Details Section */
        .details-section {
            border: 1px solid #000;
            border-top: none;
        }
        .details-row {
            display: table;
            width: 100%;
        }
        .details-left {
            display: table-cell;
            width: 50%;
            padding: 4px;
            vertical-align: top;
            border-right: 1px solid #000;
        }
        .details-right {
            display: table-cell;
            width: 50%;
            padding: 4px;
            vertical-align: top;
        }
        .detail-line {
            margin-bottom: 2px;
            font-size: 8px;
        }
        .detail-label {
            font-weight: bold;
            display: inline-block;
            width: 80px;
        }
        
        /* Supplier/Delivery Section */
        .party-section {
            border: 1px solid #000;
            border-top: none;
        }
        .party-row {
            display: table;
            width: 100%;
            border-bottom: 1px solid #000;
        }
        .party-row:last-child {
            border-bottom: none;
        }
        .party-cell {
            display: table-cell;
            width: 50%;
            padding: 4px;
            vertical-align: top;
        }
        .party-cell:first-child {
            border-right: 1px solid #000;
        }
        .party-title {
            font-weight: bold;
            font-size: 9px;
            margin-bottom: 3px;
            text-decoration: underline;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }
        .items-table th {
            background: #f0f0f0;
            border: 1px solid #000;
            padding: 3px 2px;
            font-weight: bold;
            text-align: center;
            font-size: 8px;
        }
        .items-table td {
            border: 1px solid #000;
            padding: 3px 2px;
            text-align: center;
        }
        .items-table td.left {
            text-align: left;
        }
        .items-table td.right {
            text-align: right;
        }
        
        /* Summary Section */
        .summary-section {
            border: 1px solid #000;
            border-top: none;
        }
        .summary-row {
            display: table;
            width: 100%;
        }
        .summary-left {
            display: table-cell;
            width: 60%;
            padding: 4px;
            vertical-align: top;
            border-right: 1px solid #000;
        }
        .summary-right {
            display: table-cell;
            width: 40%;
            padding: 4px;
            vertical-align: top;
        }
        .summary-line {
            display: table;
            width: 100%;
            margin-bottom: 1px;
            font-size: 8px;
        }
        .summary-label {
            display: table-cell;
            text-align: right;
            padding-right: 10px;
            width: 70%;
        }
        .summary-value {
            display: table-cell;
            text-align: right;
            width: 30%;
        }
        .total-words {
            font-size: 8px;
            font-weight: bold;
            margin-top: 3px;
        }
        
        /* Bank Details */
        .bank-section {
            border: 1px solid #000;
            border-top: none;
            padding: 4px;
        }
        .bank-title {
            font-weight: bold;
            font-size: 9px;
            margin-bottom: 3px;
        }
        .bank-details {
            font-size: 8px;
            line-height: 1.4;
        }
        
        /* Footer */
        .footer-section {
            border: 1px solid #000;
            border-top: none;
        }
        .footer-row {
            display: table;
            width: 100%;
        }
        .footer-left {
            display: table-cell;
            width: 60%;
            padding: 4px;
            vertical-align: top;
            border-right: 1px solid #000;
        }
        .footer-right {
            display: table-cell;
            width: 40%;
            padding: 4px;
            vertical-align: bottom;
            text-align: center;
        }
        .terms-title {
            font-weight: bold;
            font-size: 8px;
            margin-bottom: 2px;
        }
        .terms-list {
            font-size: 7px;
            padding-left: 12px;
            line-height: 1.3;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 30px;
            padding-top: 2px;
            font-size: 8px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <section class="pdf-section">
        <!-- Header Section -->
        <div class="header-container">
            <div class="header-row">
                <div class="header-left">
                    <div class="company-name">INDRAPUJA POLYCOT (INDIA)</div>
                    <div class="company-details">
                        <strong>Sales-Off :</strong> 1, Postal Addr.:Shop No. 6,7, Ground Floor, Ring Rd, 150, Dr. Viegas Street,Saudathi Khatari Bldg.,<br>
                        Opp.Bata Showroom, Kalbadevi Road, Mumbai - 400 002 | <strong>Tel: 022-22015066/67</strong><br>
                        <strong>Email:</strong> indrapujapolycot@gmail.com | <strong>Website:</strong> www.indrapujapolycot.com<br>
                        <strong>GSTIN:</strong> 27AACFI6271F1ZT | <strong>PAN:</strong> AACFI6271F | <strong>State:</strong> Maharashtra (27)
                    </div>
                </div>
                <div class="header-right">
                    <div class="tax-invoice-title">TAX INVOICE</div>
                    <div class="qr-placeholder">
                        QR CODE
                    </div>
                    <div style="font-size: 7px; margin-top: 3px;">IRN: 24 hrs Valid</div>
                </div>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="details-section">
            <div class="details-row">
                <div class="details-left">
                    <div class="detail-line"><span class="detail-label">Invoice No:</span> FSI/23/24/002211</div>
                    <div class="detail-line"><span class="detail-label">Invoice Date:</span> 02/12/2023</div>
                    <div class="detail-line"><span class="detail-label">Transport:</span> Uttam Road Ways Pvt Ltd</div>
                    <div class="detail-line"><span class="detail-label">LR No:</span> 23062138</div>
                    <div class="detail-line"><span class="detail-label">Broker:</span> BHAGWAN TEXTILES AGENCY</div>
                </div>
                <div class="details-right">
                    <div class="detail-line"><span class="detail-label">Destination:</span> MADURAI</div>
                    <div class="detail-line"><span class="detail-label">LR Date:</span> 02/12/2023</div>
                    <div class="detail-line"><span class="detail-label">Vehicle No:</span> MH12AB1234</div>
                    <div class="detail-line"><span class="detail-label">E-Way Bill No:</span> 123456789012</div>
                </div>
            </div>
        </div>

        <!-- Supplier and Delivery Details -->
        <div class="party-section">
            <div class="party-row">
                <div class="party-cell">
                    <div class="party-title">BILLED TO</div>
                    <div style="font-size: 8px;">
                        <strong>NACHIAS FASHION PVT LTD.</strong><br>
                        155, MUNIYANDI KOVIL STREET<br>
                        MADURAI - 625016<br>
                        State: <strong>TAMIL NADU (33)</strong><br>
                        GSTIN: <strong>33AADCN9342A1ZU</strong>
                    </div>
                </div>
                <div class="party-cell">
                    <div class="party-title">DELIVERY AT</div>
                    <div style="font-size: 8px;">
                        <strong>NACHIAS FASHION PVT LTD.</strong><br>
                        155, MUNIYANDI KOVIL STREET<br>
                        MADURAI (PERIYAR)<br>
                        State: <strong>TAMIL NADU (33)</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 4%;">S.N</th>
                    <th style="width: 30%;">DESCRIPTION OF GOODS</th>
                    <th style="width: 10%;">BALE No.</th>
                    <th style="width: 6%;">PCS</th>
                    <th style="width: 10%;">METERS</th>
                    <th style="width: 6%;">UOM</th>
                    <th style="width: 10%;">RATE</th>
                    <th style="width: 12%;">AMOUNT(Rs.)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td class="left">60/60 X 90/130 [58/60 21021101]</td>
                    <td>002211</td>
                    <td>5</td>
                    <td>227.00</td>
                    <td>Mtr</td>
                    <td class="right">160.00</td>
                    <td class="right">36320.62</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td class="left">NOVA COTTON 58" [HSN 52081901]</td>
                    <td>002211</td>
                    <td>6</td>
                    <td>303.15</td>
                    <td>Mtr</td>
                    <td class="right">115.00</td>
                    <td class="right">34856.55</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td class="left">CHENILLE 58" [HSN 52082100]</td>
                    <td>002211</td>
                    <td>4</td>
                    <td>219.08</td>
                    <td>Mtr</td>
                    <td class="right">130.65</td>
                    <td class="right">28350.00</td>
                </tr>
                <!-- Empty rows for spacing -->
                <tr style="height: 30px;">
                    <td colspan="8"></td>
                </tr>
                <tr style="height: 30px;">
                    <td colspan="8"></td>
                </tr>
            </tbody>
        </table>

        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-row">
                <div class="summary-left">
                    <div class="total-words">
                        <strong>Total Amount (in Words):</strong><br>
                        RUPEES ONE LAKH TWO THOUSAND FOUR HUNDRED THIRTEEN AND EIGHT PAISE ONLY
                    </div>
                </div>
                <div class="summary-right">
                    <div class="summary-line">
                        <div class="summary-label">Gross Total</div>
                        <div class="summary-value">{{ number_format($invoice->sub_total, 2) }}</div>
                    </div>
                    <div class="summary-line">
                        <div class="summary-label"><strong>Discount ({{ $invoice->discount_percent }}%)</strong></div>
                        <div class="summary-value">{{ number_format($invoice->discount_amount, 2) }}</div>
                    </div>
                    <div class="summary-line">
                        <div class="summary-label">Round Off ({{ $invoice->round_off_type }})</div>
                        <div class="summary-value">{{ number_format($invoice->round_off, 2) }}</div>
                    </div>
                    <div class="summary-line">
                        <div class="summary-label">CGST ({{ $invoice->cgst_percent }}%)</div>
                        <div class="summary-value">{{ number_format($invoice->cgst_amount, 2) }}</div>
                    </div>
                    <div class="summary-line">
                        <div class="summary-label">SGST ({{ $invoice->sgst_percent }}%)</div>
                        <div class="summary-value">{{ number_format($invoice->sgst_amount, 2) }}</div>
                    </div>
                    <div class="summary-line">
                        <div class="summary-label">IGST ({{ $invoice->igst_percent }}%)</div>
                        <div class="summary-value">{{ number_format($invoice->igst_amount, 2) }}</div>
                    </div>
                    <div class="summary-line">
                        <div class="summary-label">Other Charges</div>
                        <div class="summary-value">{{ number_format($invoice->other_charges, 2) }}</div>
                    </div>
                    <div class="summary-line">
                        <div class="summary-label"><strong>Rate</strong></div>
                        <div class="summary-value"><strong>Amount</strong></div>
                    </div>
                    <div class="summary-line">
                        <div class="summary-label">9.00 %</div>
                        <div class="summary-value">4870.87</div>
                    </div>
                    <div class="summary-line">
                        <div class="summary-label">9.00 %</div>
                        <div class="summary-value">4870.87</div>
                    </div>
                    <div class="summary-line">
                        <div class="summary-label">0.00 %</div>
                        <div class="summary-value">0.00</div>
                    </div>
                    <div class="summary-line" style="border-top: 1px solid #000; padding-top: 2px; margin-top: 2px;">
                        <div class="summary-label" style="font-size: 14px;"><strong>Total Invoice Amount</strong></div>
                        <div class="summary-value" style="font-size: 14px;"><strong>{{ number_format($invoice->grand_total, 2) }}</strong></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bank Details -->
        <div class="bank-section">
            <div class="bank-title">Bank Details:</div>
            <div class="bank-details">
                <strong>Bank Name: HDFC BANK</strong> &nbsp;&nbsp;&nbsp; 
                <strong>BRANCH: KHAR</strong> &nbsp;&nbsp;&nbsp; 
                <strong>IFSC NO: HDFC0000502</strong> &nbsp;&nbsp;&nbsp; 
                <strong>RTGS NO: 5 0 2 0 1 0 0 1 2 4 1 3 7 1</strong><br>
                <strong>A/C NO: 50200001241371</strong>
            </div>
        </div>

        <!-- Terms and Signature -->
        <div class="footer-section">
            <div class="footer-row">
                <div class="footer-left">
                    <div class="terms-title">TERMS AND CONDITIONS:</div>
                    <ol class="terms-list">
                        <li>Interest @24% p.a. after 3 days of sale of goods or services.</li>
                        <li>Shortage claim, if any, should be made within 3 days of receipt of goods & services.</li>
                        <li>Our risk & responsibility ceases as soon as the goods leave our premises.</li>
                        <li>Goods once sold will not be taken back or exchanged.</li>
                        <li>All disputes are subject to Mumbai Jurisdiction only.</li>
                        <li>E. & O.E.</li>
                        <li>Subject to realization of Cheque or Draft or any mode of payment.</li>
                    </ol>
                </div>
                <div class="footer-right">
                    <div style="font-size: 8px; margin-bottom: 5px;">For <strong>INDRAPUJA POLYCOT (INDIA)</strong></div>
                    <div class="signature-line">Authorised Signatory</div>
                </div>
            </div>
        </div>
    </section>

</body>
</html>
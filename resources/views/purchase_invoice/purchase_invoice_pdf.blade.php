<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Tax Invoice</title>

<style>
@page {
    size: A4;
    margin: 15px 20px;
}

body {
    font-family: DejaVu Sans, Arial, sans-serif;
    font-size: 9px;
    color: #000;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

td {
    vertical-align: top;
}

/* Header */
.company-name {
    font-size: 18px;
    font-weight: bold;
}

.company-details {
    font-size: 8.8px;
    line-height: 1.4;
}

.double-bottom {
    border-bottom: 4px double #000;
}

.table-wrapper {
    width: 100%;
    max-width: 900px;
    border: 1px solid #000;
    background-color: #fff;
    font-family: 'Inter', sans-serif;
  }

  .invoice-items {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
  }

  .invoice-items th {
    background-color: #f2f2f2;
    border-bottom: 1px solid #000;
    border-right: 1px solid #000;
    padding: 6px 4px;
    font-size: 12px;
    font-weight: bold;
    text-align: center;
  }

  .invoice-items td {
    border-right: 1px solid #000;
    padding: 4px 8px;
    font-size: 12px;
    vertical-align: top;
    height: 20px;
  }

  .invoice-items th:last-child,
  .invoice-items td:last-child {
    border-right: none;
  }

  /* Column Widths */
  .col-sn { width: 3%; padding-left: 0; padding-right: 0; }
  .col-desc { width: 44%; }
  .col-bale { width: 10%; }
  .col-pcs { width: 7%; }
  .col-meters { width: 7%; }
  .col-uom { width: 5%; }
  .col-rate { width: 10%; }
  .col-amount { width: 14%; }

  /* Alignment Helpers */
  .center { text-align: center; }
  .left { text-align: left; }
  .right { text-align: right; }
  .bold { font-weight: bold; }

  /* Filler row to create the long vertical lines effect */
  .filler-row td {
    height: 300px; 
  }

  .gross-total-row td {
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
    background-color: #fff;
    padding: 6px 8px;
  }
</style>
</head>

<body>
<table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #000; border-collapse: collapse;">
    <tr style="border-bottom:4px double #000;">
        <!-- LEFT -->
        <td width="75%" style="border-right:1px solid #000; vertical-align:top;">

            <!-- Supplier -->
            <table width="100%" cellpadding="6" cellspacing="0" style="border-bottom:4px double #000; line-height:1.4;">
                <tr>
                    <td>
                        <strong style="font-size:20px;">
                        {{ strtoupper($invoice->supplier->name)}} (INDIA)
                        </strong><br>
                        <strong>Sales Off / Postal Add:</strong> Shop No. C2, Ground Floor, Bldg No. 156,
                        Dr. Viegas Street, Swadeshi Market Bldg., Kalbadevi Road, Mumbai - 400 002.<br>
                        <strong>GST No:27AAHFI4075L1Z2 </strong> State : <strong>Maharashtra (27)</strong>
                        Tel : <strong>(022)22010101 / 08433992250</strong><br>
                        Email : <strong>indrapujapolycot@gmail.com</strong> | Website : <strong>www.indrapujapolycot.com</strong>
                    </td>
                </tr>
            </table>

            <!-- Invoice Meta -->
            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; line-height:1.4;">
                <tr>
                    <td width="50%">
                        Invoice No : <strong>{{ $invoice->invoice_no }}</strong><br>
                        Challan No : <strong>002211 x 3</strong><br>
                        Transport : <strong>Uttam Road Ways Pvt Ltd</strong><br>
                        LR No : <strong>23062138</strong><br>
                        Indent No : <strong>6737</strong><br>
                        Broker: <strong>BHAGWAN TEXTILES AGENCY</strong>
                    </td>
                    <td width="50%" style="padding:6px; line-height:1.4;">
                        Date : <strong>02/12/2023</strong><br><br>
                        Destination : <strong>MADURAI</strong><br>
                        LR Dt : <strong>04-12-2023</strong><br>
                        Indent Dt : 01/12/2023<br>
                      
                    </td>
                </tr>
            </table>
        </td>

        <!-- RIGHT EWAY -->
        <td width="25%" valign="top" align="center">
            <table width="100%" cellpadding="4" cellspacing="0" style="border-collapse: collapse;">
                <tr>
                    <td align="center" style="font-size:12px;">
                        <strong>TAX INVOICE</strong>
                    </td>
                </tr>

                <tr>
                    <td align="center" style="padding-bottom:10px;">
                        <img src="{{ public_path('assets/images/qr_code.png') }}" width="100" height="100">
                    </td>
                </tr>

                <tr>
                    <td align="center" style="border-top:1px solid #000;">
                        <strong>EWAY BILL NO</strong><br>
                        <strong style="font-size:14px;">271684527828</strong><br>
                        <span style="font-size:10px;">2023-12-02 20:10:00</span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #000; border-collapse:collapse;">
    <tr>
        <!-- INVOICE TO -->
        <td width="50%" style="border-right:1px solid #000; vertical-align:top;">
            <table width="100%" cellpadding="6" cellspacing="0">
                <tr style="background-color:#cccccc">
                    <td style="text-align:center; font-weight:bold; border-bottom:1px solid #000;">
                        INVOICE TO
                    </td>
                </tr>
                <tr>
                    <td style="line-height:1.5; letter-spacing:0px;">
                        <strong style="font-size:12px;">NACHIAS FASHION PVT.LTD.</strong><br>
                        <span style="font-size:10px;">157, MUNIYANDI KVL STREET</span><br>
                        <span style="font-size:10px;">MADURAI - 625016</span><br>
                        <span style="font-size:10px;">GSTIN: <strong>33AADCN9342A1ZU</strong> &nbsp;&nbsp; State: <strong>TAMIL NADU (33)</strong></span><br>
                        <span style="font-size:10px;">MOB: 919443330774</span>
                    </td>
                </tr>
            </table>
        </td>
        <td width="50%" style="vertical-align:top;">
            <table width="100%" cellpadding="6" cellspacing="0">
                <tr style="background-color:#cccccc">
                    <td style="text-align:center; font-weight:bold; border-bottom:1px solid #000;">
                        DELIVERY AT
                    </td>
                </tr>
                <tr>
                    <td style="line-height:1.5; letter-spacing:0px;">
                        <strong style="font-size:12px;">NACHIAS FASHION PVT.LTD.</strong><br>
                        <span style="font-size:10px;">157, MUNIYANDI KVL STREET</span><br>
                        <span style="font-size:10px;">MADURAI - 625016</span><br>
                        <span style="font-size:10px;">GSTIN: <strong>33AADCN9342A1ZU</strong> &nbsp;&nbsp; State: <strong>TAMIL NADU (33)</strong></span><br>
                        <span style="font-size:10px;">MOB: 919443330774</span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<div class="table-wrapper" style="border-top: 2px solid #000;">
  <table class="invoice-items" width="100%" cellpadding="6" cellspacing="0">
    <thead>
      <tr>
        <th class="col-sn">S.N</th>
        <th class="col-desc">DESCRIPTION OF GOODS</th>
        <th class="col-bale">BALE No.</th>
        <th class="col-pcs">PCS</th>
        <th class="col-meters">METERS</th>
        <th class="col-uom">UOM</th>
        <th class="col-rate">RATE</th>
        <th class="col-amount">AMOUNT(Rs.)</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="left">1</td>
        <td>90/10/10 X 90/10/10 [HSN:52102110]</td>
        <td class="center">002211</td>
        <td class="center">2</td>
        <td class="right">227.00</td>
        <td class="center">Mtr</td>
        <td class="right">160.00</td>
        <td class="right">36320.00</td>
      </tr>
      <tr>
        <td class="left">2</td>
        <td>NOVA COTTON 58" [HSN:52081990]</td>
        <td class="center">002211</td>
        <td class="center">5</td>
        <td class="right">303.10</td>
        <td class="center">Mtr</td>
        <td class="right">115.00</td>
        <td class="right">34856.50</td>
      </tr>
      <tr>
        <td class="left">3</td>
        <td>ORIENTAL 58" [HSN:52083310]</td>
        <td class="center">002211</td>
        <td class="center">4</td>
        <td class="right">210.00</td>
        <td class="center">Mtr</td>
        <td class="right">135.00</td>
        <td class="right">28350.00</td>
      </tr>
      <tr class="filler-row">
        <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
      </tr>
    </tbody>
    <tfoot>
      <tr class="gross-total-row">
        <td colspan="2" class="right bold">Gross Total</td>
        <td></td>
        <td class="center bold">11</td>
        <td class="right bold">740.10</td>
        <td></td>
        <td></td>
        <td class="right bold">99526.50</td>
      </tr>
    </tfoot>
  </table>
</div>
<table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #000; border-collapse: collapse; margin-top: -1px; border-top: none;">
    <tr>
        <td width="25%" style="border-right: 1px solid #000; padding: 6px;">
            Payment Due Date : <strong style="background-color: #cccccc;">01-03-2024</strong><br>
            Remark: 
        </td>
        <td width="75%">
            <table width="100%" cellpadding="4" cellspacing="0" style="border-collapse: collapse; ">
                <tr style="background-color: #f2f2f2; font-weight: bold; text-align: center; ">
                    <td width="31%" style="border-bottom: 1px solid #000; border-right: 1px solid #000;">Discount / Tax</td>
                    <td width="15%" style="border-bottom: 1px solid #000; border-right: 1px solid #000;">Add / Less</td>
                    <td width="25%" style="border-bottom: 1px solid #000; border-right: 1px solid #000;">On Amount</td>
                    <td width="15%" style="border-bottom: 1px solid #000; border-right: 1px solid #000;">Rate</td>
                    <td width="20%" style="border-bottom: 1px solid #000;">Amount (Rs.)</td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #000; border-right: 1px solid #000;">Cash Discount (Agn)</td>
                    <td align="center" style="border-bottom: 1px solid #000; border-right: 1px solid #000;">LESS</td>
                    <td align="right" style="border-bottom: 1px solid #000; border-right: 1px solid #000;">99526.50</td>
                    <td align="right" style="border-bottom: 1px solid #000; border-right: 1px solid #000;">2.00 %</td>
                    <td align="right" style="border-bottom: 1px solid #000;">-1990.53</td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #000; border-right: 1px solid #000;">IGST</td>
                    <td align="center" style="border-bottom: 1px solid #000; border-right: 1px solid #000;">ADD</td>
                    <td align="right" style="border-bottom: 1px solid #000; border-right: 1px solid #000;">97535.97</td>
                    <td align="right" style="border-bottom: 1px solid #000; border-right: 1px solid #000;">5.00 %</td>
                    <td align="right" style="border-bottom: 1px solid #000;">4876.80</td>
                </tr>
                <tr>
                    <td style="border-right: 1px solid #000;">Round Off</td>
                    <td align="center" style="border-right: 1px solid #000;">ADD</td>
                    <td align="right" style="border-right: 1px solid #000;">0.00</td>
                    <td align="right" style="border-right: 1px solid #000;">0.00 %</td>
                    <td align="right;">0.23</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" style="border: 1px solid #000; border-collapse: collapse;">
    <tr>
        <td width="44%" style="border-right: 1px solid #000; padding: 6px;">
            <strong>Total Amount (In Words) :</strong> RUPEES ONE LAKHS TWO THOUSAND FOUR HUNDRED THIRTEEN ONLY
        </td>
        <td width="13%" align="center" style="border-right: 1px solid #000; padding: 6px;">
            <strong>Total Amount (In Figure)</strong>
        </td>
        <td width="11%" align="right" style="padding: 6px; font-size: 14px; font-weight: bold;">
            102413.00
        </td>
    </tr>
</table>

<table width="100%" cellpadding="4" cellspacing="0" style="border: 1px solid #000; border-collapse: collapse; margin-top: -1px; border-top: none;">
    <tr>
        <td style="font-size: 10px;">
            <strong>Bank Name: HDFC BANK</strong> &nbsp;&nbsp;&nbsp;&nbsp; 
            <strong>BRANCH: Khar</strong> &nbsp;&nbsp;&nbsp;&nbsp; 
            <strong>IFSC NO.: HDFC0000002</strong> &nbsp;&nbsp;&nbsp;&nbsp; 
            <strong style="letter-spacing: 1px;">RTGS NO. : 50200059463252</strong><br>
            <strong>IRN :</strong> d4fed8264d1bbad1f2ccb4535d8b71a3a78fc031d603946549b6c384d201ada9 &nbsp;&nbsp;&nbsp;&nbsp; <strong>Ack:</strong> 122319209010901
        </td>
    </tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" style="border: 1px solid #000; border-collapse: collapse; margin-top: -1px; border-top: none;">
    <tr>
        <td width="70%" style="border-right: 1px solid #000; padding: 4px; vertical-align: top;">
            <div style="font-size: 8px; font-weight: bold; margin-bottom: 2px;">TERMS AND CONDITION:</div>
            <div style="font-size: 7.5px; line-height: 1.3;">
                1. Interest 24% p.a. after allowing 5 days of grace. 2. We take no responsibility for damages & losses on routes.<br>
                3. Any discount etc. will not be allowed in this Invoice. 4. Any Taxes levied upon by Provincial Government shall be borne by you. 5. Any dispute pertaining to this transaction will be disposed of according to the arbitration rules of Hindustan Chamber of Commerce, Mumbai. 6. SUBJECT TO MUMBAI JURISDICTION.
            </div>
            <table width="100%" style="margin-top: 15px; font-size: 8px; font-weight: bold;">
                <tr>
                    <td width="30%">PREPARED BY:</td>
                    <td width="40%" align="center">CHECKED BY:</td>
                    <td width="30%" align="right" style="font-style: italic;">E. & O. E</td>
                </tr>
            </table>
        </td>
        <td width="30%" align="center" style="vertical-align: top; padding: 4px;">
            <div style="font-size: 10px; font-weight: bold;">For INDRAPUJA POLYCOT</div>
            <div style="font-size: 10px; font-weight: bold;">(INDIA)</div>
            <br><br>
            <div style="margin-top: 20px; font-size: 9px; font-weight: bold;">Authorised Signature</div>
        </td>
    </tr>
</table>
</body>
</html>
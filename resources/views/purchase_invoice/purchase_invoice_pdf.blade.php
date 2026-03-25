<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Purchase Invoice - {{ $invoice->invoice_no }}</title>

  <style>
    @page {
      size: A4;
      margin: 15px 20px;
    }

    body {
      font-family: DejaVu Sans, Arial, sans-serif;
      font-size: 9px;
      color: #000;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    .table {
      width: 100%;
      border-collapse: collapse;
    }

    td {
      vertical-align: top;
    }

    * {
      box-sizing: border-box;
    }

    tr,
    td,
    table {
      box-sizing: border-box;
    }

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
      border: 1px solid #000;
      background-color: #fff;
      font-family: 'Inter', Arial, sans-serif;
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

    .center {
      text-align: center;
    }

    .left {
      text-align: left;
    }

    .right {
      text-align: right;
    }

    .bold {
      font-weight: bold;
    }

    .content-table th {
      border-right: 1px solid #000;
    }

    .content-table th:last-child {
      border-right: none;
    }

    .gross-total-row td {
      border-top: 1px solid #000;
      border-bottom: 1px solid #000;
      background-color: #fff;
      padding: 6px 8px;
    }

    tr {
      page-break-inside: avoid;
    }

    tbody {
      page-break-inside: avoid;
    }

    @media print {
      body {
        font-size: 8px;
        margin: 0;
        padding: 0;
      }

      .table-wrapper {
        width: 100% !important;
        overflow: hidden;
      }

      .content-table td,
      .content-table th {
        font-size: 9px !important;
        padding: 3px 4px !important;
      }

      .no-wrap {
        white-space: nowrap;
        overflow: hidden;
      }
    }
  </style>
</head>

<body>
  <!-- HEADER SECTION -->
  <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #000; border-collapse:collapse;">
    <tr style="border-bottom:4px double #000;">
      <td width="75%" style="border-right:1px solid #000; vertical-align:top;">
        <table width="100%" cellpadding="6" cellspacing="0" style="border-bottom:4px double #000; line-height:1.4;">
          <tr>
            <td>
              <strong style="font-size:20px;">{{ $invoice->supplier->name }}</strong><br>
              <strong>Sales Off / Postal Add:</strong> {{ $invoice->supplier->address_line_1 }}
              {{ $invoice->supplier->address_line_2 ? ', '.$invoice->supplier->address_line_2 : '' }}
              {{ $invoice->supplier->address_line_3 ? ', '.$invoice->supplier->address_line_3 : '' }}
              {{ $invoice->supplier->city ? ', '.$invoice->supplier->city->city_name : '' }}
              {{ $invoice->supplier->state ? ', '.$invoice->supplier->state->state_name : '' }}
              {{ $invoice->supplier->zip_code ? ' - '.$invoice->supplier->zip_code : '' }}.<br>
              <strong>GST No: {{ $invoice->supplier->gst_no ?? '-' }}</strong> &nbsp;
              State: <strong>{{ $invoice->supplier->state->state_name ?? '-' }} ({{ $invoice->supplier->state->state_code ?? '' }})</strong> &nbsp;
              Tel: <strong>{{ $invoice->supplier->mobile_no ?? '-' }}</strong><br>
              Email: <strong>{{ $invoice->supplier->email ?? '-' }}</strong> | Website: <strong>{{ $invoice->supplier->website_url ?? '-' }}</strong>
            </td>
          </tr>
        </table>
        <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse; line-height:1.4;">
          <tr>
            <td width="50%">
              Invoice No: <strong>{{ $invoice->invoice_no }}</strong><br>
              PO Ref: <strong>{{ $invoice->po_reference ?? '-' }}</strong><br>
              Transport: <strong>{{ $invoice->transport ?? '-' }}</strong><br>
              LR No: <strong>{{ $invoice->lr_no ?? '-' }}</strong><br>
              Indent No: <strong>{{ $invoice->indent_no ?? '-' }}</strong><br>
              Broker: <strong>-</strong>
            </td>
            <td width="50%" style="padding:6px; line-height:1.4;">
              Date: <strong>{{ $invoice->invoice_date->format('d/m/Y') }}</strong><br><br>
              Destination: <strong>{{ $invoice->destination ?? '-' }}</strong><br>
              LR Dt: <strong>{{ $invoice->lr_date ? $invoice->lr_date->format('d/m/Y') : '-' }}</strong><br>
              Indent Dt: <strong>{{ $invoice->indent_date ? $invoice->indent_date->format('d/m/Y') : '-' }}</strong>
            </td>
          </tr>
        </table>
      </td>

      <td width="25%" valign="top" align="center">
        <table width="100%" cellpadding="4" cellspacing="0" style="border-collapse:collapse;">
          <tr>
            <td align="center" style="font-size:12px;"><strong>TAX INVOICE</strong></td>
          </tr>
          <tr>
            <td align="center" style="padding-bottom:10px;">
              <div style="width:100px; height:100px; border:1px solid #ccc; margin:auto; display:flex; align-items:center; justify-content:center; font-size:8px; color:#999;">
                QR CODE
              </div>
            </td>
          </tr>
          <tr>
            <td align="center" style="border-top:1px solid #000;">
              <strong>EWAY BILL NO</strong><br>
              <strong style="font-size:14px;">{{ $invoice->eway_billno ?? '-' }}</strong><br>
              @if($invoice->eway_billno)
              <span style="font-size:10px;">{{ $invoice->invoice_date->format('Y-m-d H:i:s') }}</span>
              @endif
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <!-- INVOICE TO / DELIVERY AT SECTION -->
  <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #000; border-collapse:collapse; border-top:none;">
    <tr>
      <!-- Invoice To -->
      <td width="50%" style="border-right:1px solid #000; vertical-align:top;">
        <table width="100%" cellpadding="6" cellspacing="0">
          <tr style="background-color:#cccccc;">
            <td style="text-align:center; font-weight:bold; border-bottom:1px solid #000;">
              INVOICE TO
            </td>
          </tr>
          <tr>
            <td style="line-height:1.5;">
              <strong style="font-size:12px;">{{ $setting->company_name }}</strong><br>
              <span style="font-size:10px;">{{ $setting->address }}</span><br>
              <span style="font-size:10px;">GSTIN: <strong>{{ $setting->gst_no }}</strong> &nbsp;&nbsp; State: <strong>{{ $setting->state->state_name ?? $setting->state }}</strong></span><br>
              <span style="font-size:10px;">MOB: {{ $setting->toll_free_no }}</span>
            </td>
          </tr>
        </table>
      </td>

      <!-- Delivery At -->
      <td width="50%" style="vertical-align:top;">
        <table width="100%" cellpadding="6" cellspacing="0">
          <tr style="background-color:#cccccc;">
            <td style="text-align:center; font-weight:bold; border-bottom:1px solid #000;">
              DELIVERY AT
            </td>
          </tr>
          <tr>
            <td style="line-height:1.5;">
              <strong style="font-size:12px;">{{ $setting->company_name }}</strong><br>
              <span style="font-size:10px;">{{ $setting->address }}</span><br>
              <span style="font-size:10px;">{{ $setting->city->city_name ?? $setting->city }} - {{ $setting->zip_code }}</span><br>
              <span style="font-size:10px;">GSTIN: <strong>{{ $setting->gst_no }}</strong> &nbsp;&nbsp; State: <strong>{{ $setting->state->state_name ?? $setting->state }}</strong></span><br>
              <span style="font-size:10px;">MOB: {{ $setting->toll_free_no }}</span>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <!-- ITEMS TABLE SECTION -->
  <div class="table-wrapper" style="border-top:1px solid #000;">
    <table width="100%" cellpadding="0" cellspacing="0" class="content-table" style="border-collapse:collapse; table-layout:fixed; width:100%;">
      <tr style="height:0; visibility:hidden; line-height:0;">
        <td style="width:5%;"></td>
        <td style="width:22%;"></td>
        <td style="width:23%;"></td>
        <td style="width:10%;"></td>
        <td style="width:6%;"></td>
        <td style="width:6%;"></td>
        <td style="width:7%;"></td>
        <td style="width:10%;"></td>
        <td style="width:11%;"></td>
      </tr>
      <tbody>
        <tr style="background-color:#f2f2f2;">
          <th style="border-bottom:1px solid #000; border-right:1px solid #000; font-size:10px; padding:6px 2px; text-align:center;">S.No</th>
          <th colspan="2" style="border-bottom:1px solid #000; border-right:1px solid #000; font-size:10px; padding:6px 2px; text-align:center;">DESCRIPTION OF GOODS</th>
          <th style="border-bottom:1px solid #000; border-right:1px solid #000; font-size:10px; padding:6px 2px; text-align:center;">BALE No.</th>
          <th colspan="2" style="border-bottom:1px solid #000; border-right:1px solid #000; font-size:10px; padding:6px 2px; text-align:center;">QUANTITY</th>
          <th style="border-bottom:1px solid #000; border-right:1px solid #000; font-size:10px; padding:6px 2px; text-align:center;">UOM</th>
          <th style="border-bottom:1px solid #000; border-right:1px solid #000; font-size:10px; padding:6px 2px; text-align:center;">RATE</th>
          <th style="border-bottom:1px solid #000; font-size:10px; padding:6px 2px; text-align:center;">AMOUNT(Rs.)</th>
        </tr>

        @foreach($invoice->items as $index => $item)
        <tr>
          <td style="padding:5px; text-align:center; font-size:11px; border-right:1px solid #000;">{{ $index + 1 }}</td>
          <td colspan="2" style="padding:5px; font-size:11px; border-right:1px solid #000;">{{ $item->rawMaterial->name ?? '' }} [HSN: {{ $item->hsn_code ?? '-' }}]</td>
          <td class="no-wrap" style="padding:5px; text-align:center; font-size:11px; border-right:1px solid #000;">{{ $item->purchaseOrderItem->supplier_design_name ?? '-' }}</td>
          <td colspan="2" class="no-wrap" style="padding:5px; text-align:right; padding-right:15px; font-size:11px; border-right:1px solid #000;">
            {{ trim(strtoupper($item->uom->uom_code ?? '')) == 'PCS' ? number_format($item->quantity, 0) : number_format($item->quantity, 2) }}
          </td>
          <td class="no-wrap" style="padding:5px; text-align:center; font-size:11px; border-right:1px solid #000;">{{ $item->uom->uom_code ?? '-' }}</td>
          <td class="no-wrap" style="padding:5px; text-align:right; font-size:11px; border-right:1px solid #000;">{{ number_format($item->rate, 2) }}</td>
          <td class="no-wrap" style="padding:5px; text-align:right; font-size:11px;">{{ number_format($item->amount, 2) }}</td>
        </tr>
        @endforeach

        @for($i = count($invoice->items); $i < 12; $i++)
        <tr>
          <td style="height:30px; border-right:1px solid #000;"></td>
          <td colspan="2" style="border-right:1px solid #000;"></td>
          <td style="border-right:1px solid #000;"></td>
          <td colspan="2" style="border-right:1px solid #000;"></td>
          <td style="border-right:1px solid #000;"></td>
          <td style="border-right:1px solid #000;"></td>
          <td></td>
        </tr>
        @endfor

        <!-- GROSS TOTAL ROW -->
        <tr style="border-top:1px solid #000; border-bottom:1px solid #000; font-weight:bold; background-color:#f2f2f2; vertical-align:middle;">
          <td colspan="3" style="border-right:1px solid #000; border-left:1px solid #000;padding:6px; text-align:right; vertical-align:middle;">Gross Total</td>
          <td style="border-right:1px solid #000; text-align:center; vertical-align:middle;"></td>
          <td colspan="2" style="border-right:1px solid #000; text-align:right; padding-right:15px; vertical-align:middle;">{{ number_format($invoice->items->sum('quantity'), 2) }}</td>
          <td style="border-right:1px solid #000; vertical-align:middle;"></td>
          <td style="border-right:1px solid #000; vertical-align:middle;"></td>
          <td style="text-align:right; padding-right:5px; vertical-align:middle; border-right: none;">{{ number_format($invoice->sub_total, 2) }}</td>
        </tr>

        <!-- FOOTER TABLE STRUCTURE -->
        <tr style="background-color:#f2f2f2; font-weight:bold; text-align:center; border-bottom:1px solid #000; vertical-align:middle;">
          <td colspan="2" rowspan="4" style="border-right:1px solid #000; vertical-align:top; text-align:left; padding:8px; font-weight:normal; background-color:#fff;">
            Payment Due Date: <strong style="background-color:#cccccc; color:#000; padding:4px 6px;">{{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : '-' }}</strong><br>
            Remark: {{ $invoice->notes }}
          </td>
          <td colspan="1" style="border-right:1px solid #000; padding:4px; font-size:10px; vertical-align:middle;">Discount / Tax</td>
          <td colspan="1" style="border-right:1px solid #000; padding:4px; font-size:10px; vertical-align:middle;">Add / Less</td>
          <td colspan="2" style="border-right:1px solid #000; padding:4px; font-size:10px; vertical-align:middle;">On Amount</td>
          <td colspan="2" style="border-right:1px solid #000; padding:4px; font-size:10px; vertical-align:middle;">Rate</td>
          <td colspan="1" style="padding:4px; font-size:10px; vertical-align:middle;">Amount (Rs.)</td>
        </tr>

        <!-- Discount Row -->
        <tr style="vertical-align:middle;">
          <td colspan="1" style="border-right:1px solid #000; padding:6px 6px 0; font-size:11px; vertical-align:middle;">Discount</td>
          <td colspan="1" style="border-right:1px solid #000; text-align:center; padding:6px 6px 0; font-size:11px; vertical-align:middle;">LESS</td>
          <td colspan="2" style="text-align:right; padding:6px 6px 0; font-size:11px; vertical-align:middle; border-right:1px solid #000;">{{ number_format($invoice->sub_total, 2) }}</td>
          <td colspan="2" style="border-right:1px solid #000; text-align:center; padding:6px 6px 0; font-size:11px; vertical-align:middle;">{{ number_format($invoice->discount_percent, 2) }} %</td>
          <td colspan="1" style="text-align:right; padding:6px 6px 0; font-size:11px; font-weight:bold; vertical-align:middle;">-{{ number_format($invoice->discount_amount, 2) }}</td>
        </tr>

        <!-- Tax Row -->
        <tr>
          <td colspan="1" style="border-right:1px solid #000; padding:2px 6px; font-size:11px;">{{ $invoice->other_state ? 'IGST' : 'CGST+SGST' }}</td>
          <td colspan="1" style="border-right:1px solid #000; text-align:center; padding:2px 6px; font-size:11px;">ADD</td>
          <td colspan="2" style="border-right:1px solid #000; text-align:right; padding:2px 6px; font-size:11px;">{{ number_format($invoice->taxable_amount, 2) }}</td>
          <td colspan="2" style="border-right:1px solid #000; text-align:center; padding:2px 6px; font-size:11px;">{{ $invoice->igst_percent ?? ($invoice->cgst_percent + $invoice->sgst_percent) }} %</td>
          <td colspan="1" style="text-align:right; padding:2px 6px; font-size:11px; font-weight:bold;">{{ number_format($invoice->tax_amount, 2) }}</td>
        </tr>

        <!-- Round Off Row -->
        <tr style="border-bottom:1px solid #000;">
          <td colspan="1" style="border-right:1px solid #000; padding:2px 6px; font-size:11px;">Round Off</td>
          <td colspan="1" style="border-right:1px solid #000; text-align:center; padding:2px 6px; font-size:11px;">{{ strtoupper($invoice->round_off_type ?? 'ADD') }}</td>
          <td colspan="2" style="border-right:1px solid #000; text-align:right; padding:2px 6px; font-size:11px;">0.00</td>
          <td colspan="2" style="border-right:1px solid #000; text-align:center; padding:2px 6px; font-size:11px;">-</td>
          <td colspan="1" style="text-align:right; padding:2px 6px; font-size:11px; font-weight:bold;">
            {{ $invoice->round_off_type == 'Less' ? '-' : '' }}{{ number_format($invoice->round_off, 2) }}
          </td>
        </tr>

        <!-- FINAL TOTAL ROW -->
        <tr style="border-bottom:1px solid #000; border-top:none;">
          <td colspan="6" style="border-right:1px solid #000; padding:10px; font-size:11px;">
            <strong>Total Amount (In Words):</strong> {{ strtoupper($totalInWords) }}
          </td>
          <td colspan="2" style="border-right:1px solid #000; padding:6px; text-align:center; font-size:10px; font-weight:bold;">
            Total Amount<br>(In Figure)
          </td>
          <td style="padding:6px; text-align:right; font-size:12px; font-weight:bold;">{{ number_format($invoice->grand_total, 2) }}</td>
        </tr>

        <!-- BANK DETAILS SECTION -->
        <tr style="border-bottom:1px solid #000; border-top: none;">
          <td colspan="9" style="padding:6px 8px; font-size:10px;">
            <div style="display:table; width:100%; margin-bottom:2px;">
              <div style="display:table-row;">
                <div style="display:table-cell; width:25%; padding-right:5px;">Bank Name: <strong>{{ $invoice->supplier->bank_name ?? '-' }}</strong></div>
                <div style="display:table-cell; width:25%; padding-right:5px;">BRANCH: <strong>{{ $invoice->supplier->branch ?? '-' }}</strong></div>
                <div style="display:table-cell; width:25%; padding-right:5px;">IFSC NO.: <strong>{{ $invoice->supplier->ifsc_code ?? '-' }}</strong></div>
                <div style="display:table-cell; width:25%; padding-right:5px;">RTGS NO.: <strong>{{ $invoice->supplier->account_number ?? '-' }}</strong></div>
              </div>
            </div>
            <div style="display:table; width:100%;">
              <div style="display:table-row;">
                <div style="display:table-cell; width:70%;">IRN: <strong>-</strong></div>
                <div style="display:table-cell; width:30%;">Ack: <strong>-</strong></div>
              </div>
            </div>
          </td>
        </tr>

        <!-- FOOTER / TERMS SECTION -->
        <tr>
          <td colspan="6" style="border-right:1px solid #000; border-bottom:1px solid #000; padding:8px; vertical-align:top;">
            <div style="font-size:8px; font-weight:bold; margin-bottom:4px;">TERMS AND CONDITION:</div>
            <div style="font-size:10px; line-height:1.4;">
              1. Interest 24% p.a. after allowing 5 days of grace.
              2. We take no responsibility for damages &amp; losses on routes.
              3. Any discount etc. will not be allowed in this Invoice.
              4. Any Taxes levied upon by Provincial Government shall be borne by you.
              5. Any dispute pertaining to this transaction will be disposed of according to the arbitration rules of Hindustan Chamber of Commerce, Mumbai.
              6. SUBJECT TO MUMBAI JURISDICTION.
            </div>
            <table width="100%" style="margin-top:15px; font-size:10px; font-weight:bold;">
              <tr>
                <td width="33%">PREPARED BY:</td>
                <td width="34%" align="center">CHECKED BY:</td>
                <td width="33%" align="right" style="font-style:italic;">E. &amp; O. E</td>
              </tr>
            </table>
          </td>
          <td colspan="3" style="border-bottom:1px solid #000; vertical-align:top; padding:10px; text-align:center;">
            <div style="font-size:11px; font-weight:bold;">For {{ $invoice->supplier->name }}</div>
            <br><br><br>
            <div style="margin-top:40px; font-size:10px; font-weight:bold;">Authorised Signature</div>
          </td>
        </tr>

      </tbody>
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
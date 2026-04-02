<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Debit Note - {{ $debitNote->debit_note_no }}</title>

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

    .company-name {
      font-size: 18px;
      font-weight: bold;
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

    tr {
      page-break-inside: avoid;
    }

    tbody {
      page-break-inside: avoid;
    }

    .table-wrapper {
      width: 100%;
      border: 1px solid #000;
      background-color: #fff;
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
    }
  </style>
</head>

<body>
  <!-- HEADER SECTION -->
  <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #000; border-collapse:collapse;">
    <tr>
      <td width="25%" style="padding: 10px; vertical-align: middle;">
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
          <img
            src="{{ isset($is_print) && $is_print ? url('assets/images/jc_logo.png') : public_path('assets/images/jc_logo.png') }}"
            style="width: 140px;">
        @endif
      </td>
      <td width="50%" align="center" style="vertical-align: middle;">
        <div style="font-size: 20px; font-weight: bold;">DEBIT NOTE</div>
      </td>
      <td width="25%" align="right" style="vertical-align: middle;">
      </td>
    </tr>
    <tr style="border-top: 1px solid #000; border-bottom:4px double #000;">
      <td colspan="3" style="padding: 0;">
        <table width="100%" cellpadding="6" cellspacing="0" style="border-bottom:4px double #000; line-height:1.4;">
          <tr>
            <td>
              <strong style="font-size:16px;">{{ $debitNote->supplier->name }}</strong><br>
              <strong>Sales Off / Postal Add:</strong> {{ $debitNote->supplier->address_line_1 }}
              {{ $debitNote->supplier->address_line_2 ? ', ' . $debitNote->supplier->address_line_2 : '' }}
              {{ $debitNote->supplier->address_line_3 ? ', ' . $debitNote->supplier->address_line_3 : '' }}
              {{ $debitNote->supplier->city ? ', ' . $debitNote->supplier->city->city_name : '' }}
              {{ $debitNote->supplier->state ? ', ' . $debitNote->supplier->state->state_name : '' }}
              {{ $debitNote->supplier->zip_code ? ' - ' . $debitNote->supplier->zip_code : '' }}.<br>
              <strong>GST No: {{ $debitNote->supplier->gst_no ?? '-' }}</strong> &nbsp;
              State: <strong>{{ $debitNote->supplier->state->state_name ?? '-' }}
                ({{ $debitNote->supplier->state->state_code ?? '' }})</strong> &nbsp;
              Tel: <strong>{{ $debitNote->supplier->mobile_no ?? '-' }}</strong><br>
              Email: <strong>{{ $debitNote->supplier->email ?? '-' }}</strong> | Website:
              <strong>{{ $debitNote->supplier->website_url ?? '-' }}</strong>
            </td>
          </tr>
        </table>
        <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse; line-height:1.4;">
          <tr>
            <td width="50%" style="border-right: 1px solid #000;">
              Debit Note No: <strong>{{ $debitNote->debit_note_no }}</strong><br>
              Reference Invoice: <strong>{{ $debitNote->purchaseInvoice->invoice_no ?? '-' }}</strong><br>
              Reason: <strong>{{ $debitNote->reason ?? '-' }}</strong>
            </td>
            <td width="50%" style="padding:6px; line-height:1.4;">
              Date: <strong>{{ $debitNote->debit_note_date->format('d/m/Y') }}</strong><br>
              Destination: <strong>{{ $debitNote->supplier->city->city_name ?? '-' }}</strong><br>
              Place of Supply: <strong>{{ $debitNote->supplier->state->state_name ?? '-' }}</strong>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <!-- INVOICE TO SECTION -->
  <table width="100%" cellpadding="0" cellspacing="0"
    style="border:1px solid #000; border-collapse:collapse; border-top:none;">
    <tr>
      <td width="100%" style="vertical-align:top;">
        <table width="100%" cellpadding="6" cellspacing="0">
          <tr style="background-color:#cccccc;">
            <td style="text-align:center; font-weight:bold; border-bottom:1px solid #000;">
              BILL TO (PURCHASER)
            </td>
          </tr>
          <tr>
            <td style="line-height:1.5;">
              <strong style="font-size:12px;">{{ $setting->company_name }}</strong><br>
              <span style="font-size:10px;">{{ $setting->address }}</span><br>
              <span style="font-size:10px;">{{ $setting->city->city_name ?? $setting->city }} -
                {{ $setting->zip_code }}</span><br>
              <span style="font-size:10px;">GSTIN: <strong>{{ $setting->gst_no }}</strong> &nbsp;&nbsp; State:
                <strong>{{ $setting->state->state_name ?? $setting->state }}</strong></span><br>
              <span style="font-size:10px;">MOB: {{ $setting->toll_free_no }}</span>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <!-- ITEMS TABLE SECTION -->
  <div class="table-wrapper" style="border-top:1px solid #000;">
    <table width="100%" cellpadding="0" cellspacing="0" class="content-table"
      style="border-collapse:collapse; table-layout:fixed; width:100%;">
      <tr style="height:0; visibility:hidden; line-height:0;">
        <td style="width:5%;"></td>
        <td style="width:49%;"></td>
        <td style="width:8%;"></td>
        <td style="width:8%;"></td>
        <td style="width:15%;"></td>
        <td style="width:15%;"></td>
      </tr>
      <tbody>
        <tr style="background-color:#f2f2f2;">
          <th
            style="border-bottom:1px solid #000; border-right:1px solid #000; font-size:10px; padding:6px 2px; text-align:center;">
            S.No</th>
          <th
            style="border-bottom:1px solid #000; border-right:1px solid #000; font-size:10px; padding:6px 2px; text-align:center;">
            ITEM DESCRIPTION</th>
          <th
            style="border-bottom:1px solid #000; border-right:1px solid #000; font-size:10px; padding:6px 2px; text-align:center;">
            QUANTITY</th>
          <th
            style="border-bottom:1px solid #000; border-right:1px solid #000; font-size:10px; padding:6px 2px; text-align:center;">
            UOM</th>
          <th
            style="border-bottom:1px solid #000; border-right:1px solid #000; font-size:10px; padding:6px 2px; text-align:center;">
            RATE (Rs.)</th>
          <th style="border-bottom:1px solid #000; font-size:10px; padding:6px 2px; text-align:center;">AMOUNT (Rs.)
          </th>
        </tr>

        @foreach($debitNote->items as $index => $item)
          <tr>
            <td style="padding:5px; text-align:center; font-size:11px; border-right:1px solid #000;">{{ $index + 1 }}</td>
            <td style="padding:5px; font-size:11px; border-right:1px solid #000;">{{ $item->rawMaterial->name ?? '' }}
            </td>
            <td style="padding:5px; text-align:right; font-size:11px; border-right:1px solid #000;">
              {{ number_format($item->quantity, 2) }}
            </td>
            <td style="padding:5px; text-align:center; font-size:11px; border-right:1px solid #000;">
              {{ $item->uom->uom_code ?? '-' }}
            </td>
            <td style="padding:5px; text-align:right; font-size:11px; border-right:1px solid #000;">
              {{ number_format($item->rate, 2) }}
            </td>
            <td style="padding:5px; text-align:right; font-size:11px;">{{ number_format($item->amount, 2) }}</td>
          </tr>
        @endforeach

        @for($i = count($debitNote->items); $i < 10; $i++)
          <tr>
            <td style="height:25px; border-right:1px solid #000;"></td>
            <td style="border-right:1px solid #000;"></td>
            <td style="border-right:1px solid #000;"></td>
            <td style="border-right:1px solid #000;"></td>
            <td style="border-right:1px solid #000;"></td>
            <td></td>
          </tr>
        @endfor

        @php
          $preGstCharges = $debitNote->charges ? $debitNote->charges->where('tax_type', 'Pre-GST')->sum('charge_amount') : 0;
          $postGstCharges = $debitNote->charges ? $debitNote->charges->where('tax_type', 'Post-GST')->sum('charge_amount') : 0;
          $taxableAmt = $debitNote->sub_total + $preGstCharges;
        @endphp
        <tr style="border-top:1px solid #000; font-weight:bold;">
          <td colspan="4" style="border-right:1px solid #000; padding:6px; text-align:right;">Sub Total</td>
          <td colspan="2" style="text-align:right; padding:6px; border-top:1px solid #000;">
            {{ number_format($debitNote->sub_total, 2) }}
          </td>
        </tr>

        @if($preGstCharges > 0)
          <tr>
            <td colspan="4" style="border-right:1px solid #000; padding:6px; text-align:right;">Pre-GST Charges</td>
            <td colspan="2" style="text-align:right; padding:6px;">{{ number_format($preGstCharges, 2) }}</td>
          </tr>
          <tr style="font-weight:bold;">
            <td colspan="4" style="border-right:1px solid #000; padding:6px; text-align:right;">Taxable Total</td>
            <td colspan="2" style="text-align:right; padding:6px;">{{ number_format($taxableAmt, 2) }}</td>
          </tr>
        @endif

        @if($debitNote->other_state == 'Y')
          <tr>
            <td colspan="4" style="border-right:1px solid #000; padding:4px; text-align:right;">IGST
              ({{ $debitNote->igst_percent }}%)</td>
            <td colspan="2" style="text-align:right; padding:4px;">
              {{ number_format($taxableAmt * ($debitNote->igst_percent / 100), 2) }}
            </td>
          </tr>
        @else
          <tr>
            <td colspan="4" style="border-right:1px solid #000; padding:4px; text-align:right;">CGST
              ({{ $debitNote->cgst_percent }}%)</td>
            <td colspan="2" style="text-align:right; padding:4px;">
              {{ number_format($taxableAmt * ($debitNote->cgst_percent / 100), 2) }}
            </td>
          </tr>
          <tr>
            <td colspan="4" style="border-right:1px solid #000; padding:4px; text-align:right;">SGST
              ({{ $debitNote->sgst_percent }}%)</td>
            <td colspan="2" style="text-align:right; padding:4px;">
              {{ number_format($taxableAmt * ($debitNote->sgst_percent / 100), 2) }}
            </td>
          </tr>
        @endif

        @if($postGstCharges > 0)
          <tr>
            <td colspan="4" style="border-right:1px solid #000; padding:6px; text-align:right;">Post-GST Charges</td>
            <td colspan="2" style="text-align:right; padding:6px;">{{ number_format($postGstCharges, 2) }}</td>
          </tr>
        @endif

        @if($debitNote->round_off != 0)
          <tr>
            <td colspan="4" style="border-right:1px solid #000; padding:4px; text-align:right;">Round Off
              ({{ $debitNote->round_off_type }})</td>
            <td colspan="2" style="text-align:right; padding:4px;">
              {{ $debitNote->round_off_type == 'Less' ? '-' : '' }}{{ number_format($debitNote->round_off, 2) }}
            </td>
          </tr>
        @endif

        <tr
          style="background-color:#f2f2f2; font-weight:bold; border-top:1px solid #000; border-bottom:1px solid #000;">
          <td colspan="4" style="border-right:1px solid #000; padding:8px; text-align:right; font-size:12px;">Grand
            Total (Rs.)</td>
          <td colspan="2" style="text-align:right; padding:8px; font-size:12px;">
            {{ number_format($debitNote->grand_total, 2) }}
          </td>
        </tr>

        <tr>
          <td colspan="6" style="padding:8px; font-size:10px; border-bottom:1px solid #000;">
            <strong>Amount in Words:</strong> {{ strtoupper($totalInWords) }}
          </td>
        </tr>

        @if($debitNote->remarks)
          <tr>
            <td colspan="6" style="padding:8px; font-size:10px; border-bottom:1px solid #000;">
              <strong>Remarks:</strong> {{ $debitNote->remarks }}
            </td>
          </tr>
        @endif

        <!-- SIGNATURE SECTION -->
        <tr>
          <td colspan="3"
            style="border-right:1px solid #000; border-bottom:1px solid #000; padding:30px 10px 10px; vertical-align:bottom;">
            <div style="border-top:1px solid #000; width:150px; text-align:center; font-weight:bold; font-size:10px;">
              Receiver's Signature</div>
          </td>
          <td colspan="3" style="padding:10px; text-align:center; vertical-align:top; border-bottom:1px solid #000;">
            <div style="font-weight:bold; font-size:11px;">For {{ $setting->company_name }}</div>
            <br><br><br><br>
            <div style="font-weight:bold; font-size:10px;">Authorised Signature</div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div style="margin-top:20px; font-size:8px; text-align:center; color:#666;">
    This is a computer generated document.
  </div>
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
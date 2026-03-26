<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Credit Note - {{ $creditNote->note_no }}</title>

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
            <img src="{{ isset($is_print) && $is_print ? asset('assets/images/jc_logo.png') : public_path('assets/images/jc_logo.png') }}" style="width: 140px;">
        @endif
      </td>
      <td width="50%" align="center" style="vertical-align: middle;">
        <div style="font-size: 20px; font-weight: bold;">CREDIT NOTE</div>
      </td>
      <td width="25%" align="right" style="vertical-align: middle;">
      </td>
    </tr>
    <tr style="border-top: 1px solid #000; border-bottom:4px double #000;">
      <td colspan="3" style="padding: 0;">
        <table width="100%" cellpadding="6" cellspacing="0" style="border-bottom:4px double #000; line-height:1.4;">
          <tr>
            <td>
              <strong style="font-size:16px;">{{ $creditNote->customer->name }}</strong><br>
              <strong>Address:</strong> {{ $creditNote->customer->address }}
              {{ $creditNote->customer->city ? ', '.$creditNote->customer->city->city_name : '' }}
              {{ $creditNote->customer->state ? ', '.$creditNote->customer->state->state_name : '' }}
              {{ $creditNote->customer->zip_code ? ' - '.$creditNote->customer->zip_code : '' }}.<br>
              <strong>GST No: {{ $creditNote->customer->gst_no ?? '-' }}</strong> &nbsp;
              State: <strong>{{ $creditNote->customer->state->state_name ?? '-' }} ({{ $creditNote->customer->state->state_code ?? '' }})</strong> &nbsp;
              Tel: <strong>{{ $creditNote->customer->mobile_no ?? '-' }}</strong><br>
              Email: <strong>{{ $creditNote->customer->email ?? '-' }}</strong>
            </td>
          </tr>
        </table>
        <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse; line-height:1.4;">
          <tr>
            <td width="50%" style="border-right: 1px solid #000;">
              Credit Note No: <strong>{{ $creditNote->note_no }}</strong><br>
              Reference Invoice: <strong>{{ $creditNote->salesInvoice->inv_no ?? '-' }}</strong><br>
              Reason: <strong>{{ $creditNote->reason ?? '-' }}</strong>
            </td>
            <td width="50%" style="padding:6px; line-height:1.4;">
              Date: <strong>{{ $creditNote->note_date->format('d/m/Y') }}</strong><br>
              Destination: <strong>{{ $creditNote->customer->city->city_name ?? '-' }}</strong><br>
              Place of Supply: <strong>{{ $creditNote->customer->state->state_name ?? '-' }}</strong>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <!-- ISSUER SECTION -->
  <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #000; border-collapse:collapse; border-top:none;">
    <tr>
      <td width="100%" style="vertical-align:top;">
        <table width="100%" cellpadding="6" cellspacing="0">
          <tr style="background-color:#cccccc;">
            <td style="text-align:center; font-weight:bold; border-bottom:1px solid #000;">
              BILL FROM (ISSUER)
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
  <div class="table-wrapper">
    <table width="100%" cellpadding="0" cellspacing="0" class="content-table" style="border-collapse:collapse; table-layout:fixed; width:100%;">
      <tr style="height:0; visibility:hidden; line-height:0;">
        <td style="width:5%;"></td>
        <td style="width:45%;"></td>
        <td style="width:15%;"></td>
        <td style="width:10%;"></td>
        <td style="width:10%;"></td>
        <td style="width:15%;"></td>
      </tr>
      <tbody>
        <tr style="background-color:#f2f2f2;">
          <th style="border-bottom:1px solid #000; border-right:1px solid #000; font-size:10px; padding:6px 2px; text-align:center;">S.No</th>
          <th style="border-bottom:1px solid #000; border-right:1px solid #000; font-size:10px; padding:6px 2px; text-align:center;">ITEM DESCRIPTION</th>
          <th style="border-bottom:1px solid #000; border-right:1px solid #000; font-size:10px; padding:6px 2px; text-align:center;">BRAND</th>
          <th style="border-bottom:1px solid #000; border-right:1px solid #000; font-size:10px; padding:6px 2px; text-align:center;">SIZE</th>
          <th style="border-bottom:1px solid #000; border-right:1px solid #000; font-size:10px; padding:6px 2px; text-align:center;">QTY</th>
          <th style="border-bottom:1px solid #000; font-size:10px; padding:6px 2px; text-align:center;">AMOUNT</th>
        </tr>

        @foreach($creditNote->items as $index => $item)
        <tr>
          <td style="padding:5px; text-align:center; font-size:11px; border-right:1px solid #000;">{{ $index + 1 }}</td>
          <td style="padding:5px; font-size:11px; border-right:1px solid #000;">
            {{ $item->item->name ?? '' }}
            @if($item->sleeve_type)
                <br><small>Sleeve: {{ $item->sleeve_type }}</small>
            @endif
          </td>
          <td style="padding:5px; font-size:11px; border-right:1px solid #000; text-align:center;">{{ $item->brandCategory->name ?? '-' }}</td>
          <td style="padding:5px; text-align:center; font-size:11px; border-right:1px solid #000;">{{ $item->size ?? '-' }}</td>
          <td style="padding:5px; text-align:right; font-size:11px; border-right:1px solid #000;">{{ number_format($item->quantity, 2) }}</td>
          <td style="padding:5px; text-align:right; font-size:11px;">{{ number_format($item->amount, 2) }}</td>
        </tr>
        @endforeach

        @for($i = count($creditNote->items); $i < 8; $i++)
        <tr>
          <td style="height:25px; border-right:1px solid #000;"></td>
          <td style="border-right:1px solid #000;"></td>
          <td style="border-right:1px solid #000;"></td>
          <td style="border-right:1px solid #000;"></td>
          <td style="border-right:1px solid #000;"></td>
          <td></td>
        </tr>
        @endfor

        <!-- SUMMARY SECTION -->
        <tr style="border-top:1px solid #000; font-weight:bold;">
          <td colspan="5" style="border-right:1px solid #000; padding:6px; text-align:right;">Sub Total</td>
          <td style="text-align:right; padding:6px; border-top:1px solid #000;">{{ number_format($creditNote->sub_total, 2) }}</td>
        </tr>

        @if($creditNote->other_state)
        <tr>
          <td colspan="5" style="border-right:1px solid #000; padding:4px; text-align:right;">IGST ({{ $creditNote->igst_percent }}%)</td>
          <td style="text-align:right; padding:4px;">{{ number_format($creditNote->igst, 2) }}</td>
        </tr>
        @else
        <tr>
          <td colspan="5" style="border-right:1px solid #000; padding:4px; text-align:right;">CGST ({{ $creditNote->cgst_percent }}%)</td>
          <td style="text-align:right; padding:4px;">{{ number_format($creditNote->cgst, 2) }}</td>
        </tr>
        <tr>
          <td colspan="5" style="border-right:1px solid #000; padding:4px; text-align:right;">SGST ({{ $creditNote->sgst_percent }}%)</td>
          <td style="text-align:right; padding:4px;">{{ number_format($creditNote->sgst, 2) }}</td>
        </tr>
        @endif

        @if($creditNote->round_off > 0)
        <tr>
          <td colspan="5" style="border-right:1px solid #000; padding:4px; text-align:right;">Round Off ({{ $creditNote->round_off_type }})</td>
          <td style="text-align:right; padding:4px;">{{ $creditNote->round_off_type == 'Less' ? '-' : '+' }}{{ number_format($creditNote->round_off, 2) }}</td>
        </tr>
        @endif

        <tr style="background-color:#f2f2f2; font-weight:bold; border-top:1px solid #000; border-bottom:1px solid #000;">
          <td colspan="5" style="border-right:1px solid #000; padding:8px; text-align:right; font-size:12px;">Grand Total (Rs.)</td>
          <td style="text-align:right; padding:8px; font-size:12px;">{{ number_format($creditNote->grand_total, 2) }}</td>
        </tr>

        <tr>
          <td colspan="6" style="padding:8px; font-size:10px; border-bottom:1px solid #000;">
            <strong>Amount in Words:</strong> {{ strtoupper($totalInWords) }}
          </td>
        </tr>

        @if($creditNote->remarks)
        <tr>
          <td colspan="6" style="padding:8px; font-size:10px; border-bottom:1px solid #000;">
            <strong>Remarks:</strong> {{ $creditNote->remarks }}
          </td>
        </tr>
        @endif

        <!-- SIGNATURE SECTION -->
        <tr>
          <td colspan="4" style="border-right:1px solid #000; border-bottom:1px solid #000; padding:30px 10px 10px; vertical-align:bottom;">
            <div style="border-top:1px solid #000; width:150px; text-align:center; font-weight:bold; font-size:10px;">Receiver's Signature</div>
          </td>
          <td colspan="2" style="padding:10px; text-align:center; vertical-align:top; border-bottom:1px solid #000;">
            <div style="font-weight:bold; font-size:11px;">For {{ $setting->company_name }}</div>
            <br><br><br><br>
            <div style="font-weight:bold; font-size:10px;">Authorised Signature</div>
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

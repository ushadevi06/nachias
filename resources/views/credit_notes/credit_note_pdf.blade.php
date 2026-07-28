<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Credit Note - {{ $creditNote->note_no }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

  <style>
    @page {
      size: A4;
      margin: 15px 20px;
    }

    body {
      font-family: 'Roboto', Arial, Helvetica, sans-serif;
      font-size: 10px;
      color: #000;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
      margin: 0;
      padding: 0;
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

    /* Outer Wrapper for entire page border */
    .page-wrapper {
      width: 100%;
      border: 1px solid #000;
    }

    .header-section {
      width: 100%;
    }
    
    .company-details {
      padding: 10px;
    }

    .company-name {
      font-family: 'Roboto', sans-serif;
      font-size: 19px;
    }

    .info-box {
      width: 100%;
      border-collapse: collapse;
      border-bottom: 1px solid #000;
    }

    .info-box td {
      padding: 3px 5px;
      vertical-align: top;
      line-height: 1.3;
      font-size: 13px;
    }

    .info-box .left-col {
      width: 58%;
      border-right: 1px solid #000;
    }

    .info-box .right-col {
      width: 42%;
    }

    .title-row {
      text-align: center;
      font-size: 18px;
      padding: 5px 0;
      border-bottom: 1px solid #000;
      position: relative;
    }

    .page-info {
      position: absolute;
      right: 10px;
      top: 5px;
      font-size: 10px;
      font-weight: normal;
    }

    /* Items Table */
    .invoice-items {
      width: 100%;
      border-collapse: collapse;
      table-layout: fixed;
    }

    .invoice-items th {
      background-color: #a6a6a6;
      border-bottom: 1px solid #000;
      border-right: 1px solid #000;
      border-top: 1px solid #000;
      padding: 7px 2px;
      font-size: 11px;
      font-weight: normal;
      text-align: center;
    }

    .invoice-items th:last-child {
      border-right: none;
    }

    .invoice-items td {
      border-right: 1px solid #000;
      padding: 4px;
      font-size: 12px;
      vertical-align: top;
      word-wrap: break-word;
    }

    .invoice-items td:last-child {
      border-right: none;
    }

    /* Alternate row background */
    .invoice-items tr:nth-child(even) td {
      background-color: #f9f9f9;
    }

    /* Footer Section */
    .footer-section {
      width: 100%;
      border-collapse: collapse;
      border-top: 1px solid #000;
    }

    .footer-left {
      width: 65%;
      border-right: 1px solid #000;
      padding: 0;
    }

    .footer-right {
      width: 35%;
      padding: 0;
    }

    .footer-row {
      border-bottom: 1px solid #000;
      padding: 4px;
    }

    .footer-row:last-child {
      border-bottom: none;
    }

    .bank-details {
      font-size: 12px;
      line-height: 1.4;
    }

    .totals-table {
      width: 100%;
      border-collapse: collapse;
    }

    .totals-table td {
      padding: 4px 5px;
      text-align: right;
      font-size:13px;
    }
    
    .totals-table .label-col {
      width: 60%;
    }
    
    .totals-table .value-col {
      width: 40%;
    }

    .signature-box {
      border: 4px solid #000;
      margin: 10px;
      padding: 5px;
      text-align: center;
      height: 80px;
      position: relative;
    }

    .signature-line {
      position: absolute;
      bottom: 25px;
      left: 5px;
      right: 5px;
      border-top: 1px dotted #000;
    }

    .signature-text {
      position: absolute;
      bottom: 5px;
      left: 0;
      width: 100%;
      text-align: center;
      font-size: 11px;
    }

    @media print {
      body {
        font-size: 9px;
        margin: 0;
        padding: 0;
      }
      .page-wrapper {
        min-height: auto;
        height: 100%;
      }
      .footer-section {
        position: relative;
      }
    }
  </style>
</head>

<body>
@php
    $showFields = $creditNote->show_fields ?? [];
    $showAmount = in_array('amount', $showFields);
    $showSubTotal = in_array('subtotal', $showFields);
    $showDiscount = in_array('discount', $showFields);
    $showTax = in_array('tax', $showFields);
    $showGrandTotal = in_array('grandtotal', $showFields);
    $showMrp = in_array('mrp', $showFields);
    $showPrice = in_array('price', $showFields);
    $showSalesAgent = in_array('sales_agent', $showFields);
@endphp

<table class="table header-section">
    <tr>
      <td width="15%" style="padding: 10px; text-align: center; vertical-align: middle;">
        @php
            $logoPath = public_path('assets/images/jc_logo.png');
            $logoBase64 = '';
            if (file_exists($logoPath)) {
              $logoData = file_get_contents($logoPath);
              $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
            }
        @endphp
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" style="max-width: 100px;">
        @else
            <img src="{{ isset($is_print) && $is_print ? asset('assets/images/jc_logo.png') : public_path('assets/images/jc_logo.png') }}" style="max-width: 100px;">
        @endif
      </td>
      <td width="65%" class="company-details">
        <div class="company-name">{{ ucwords(strtolower($setting->company_name), " \t\r\n\f\v,.-()") }}</div>
        <div style="font-size: 16px;">
          {!! nl2br(e(ucwords(strtolower($setting->address), " \t\r\n\f\v,.-()"))) !!} <br> 
          GSTIN: <span>{{ strtoupper($setting->gst_no) }}</span>
        </div>
      </td>
      <td width="20%" style="padding: 10px; text-align: right; vertical-align: middle;">
        @php
            $qrBase64 = '';
            if (!empty($creditNote->signed_qr_code)) {
                try {
                    $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->margin(0)->generate($creditNote->signed_qr_code));
                } catch (\Exception $e) {
                    \Log::error('Credit Note E-Invoice QR Generation failed: ' . $e->getMessage());
                }
            } else {
                $qrCodePath = public_path('uploads/qr_codes/' . $setting->qr_code);
                if ($setting->qr_code && file_exists($qrCodePath)) {
                  $qrData = file_get_contents($qrCodePath);
                  $qrBase64 = 'data:image/png;base64,' . base64_encode($qrData);
                }
            }
        @endphp
        @if($qrBase64)
            <img src="{{ $qrBase64 }}" style="max-width: 80px; max-height: 80px;">
        @endif
      </td>
    </tr>
  </table>

<!-- TITLE -->
<div class="title-row">
  Credit Note
  <div class="page-info">Page: 1/1</div>
</div>

<div class="page-wrapper" style="border-top: none;">
  
  <!-- DOC INFO -->
  <table class="info-box">
    <tr>
      <td class="left-col">
        <table width="100%" cellpadding="0" cellspacing="0" style="line-height:1.3;">
          <tr>
            <td width="15%">M/S</td>
            <td width="5%">:</td>
            <td width="80%">
              <span>{{ strtoupper($creditNote->customer->name) }}</span><br>
              {{ strtoupper($creditNote->customer->address_line_1) }}<br>
              {{ strtoupper($creditNote->customer->city ? $creditNote->customer->city->city_name : '') }}{{ $creditNote->customer->zip_code ? '-'.$creditNote->customer->zip_code : '' }}<br>
              {{ $creditNote->customer->mobile_no ?? '' }}
            </td>
          </tr>
          <tr>
            <td>State</td>
            <td>:</td>
            <td>{{ $creditNote->customer->state->state_name ?? '-' }}({{ $creditNote->customer->state->state_code ?? '' }})</td>
          </tr>
          <tr>
            <td colspan="3">{{ $creditNote->customer->gst_no ?? '' }}</td>
          </tr>
        </table>
      </td>
      <td class="right-col">
        <table width="100%" cellpadding="0" cellspacing="0" style="line-height:1.4;">
          <tr>
            <td width="25%">No.</td>
            <td width="5%">:</td>
            <td width="70%"><span>{{ $creditNote->note_no }}</span></td>
          </tr>
          <tr>
            <td>Date</td>
            <td>:</td>
            <td>{{ $creditNote->note_date->format('d/m/Y') }}</td>
          </tr>
          <tr>
            <td>Doc No.</td>
            <td>:</td>
            <td>
              @if(!empty($salesInvoices) && count($salesInvoices) > 0)
                  {{ implode(', ', $salesInvoices->pluck('inv_no')->toArray()) }}
              @else
                  {{ $creditNote->salesInvoice->inv_no ?? '-' }}
              @endif
            </td>
          </tr>
          <tr>
            <td>Doc Date</td>
            <td>:</td>
            <td>
              @if(!empty($salesInvoices) && count($salesInvoices) > 0)
                {{ $salesInvoices->first()->inv_date ? \Carbon\Carbon::parse($salesInvoices->first()->inv_date)->format('d/m/Y') : '-' }}
              @else
                {{ $creditNote->salesInvoice->inv_date ? \Carbon\Carbon::parse($creditNote->salesInvoice->inv_date)->format('d/m/Y') : '-' }}
              @endif
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <!-- ITEMS TABLE -->
  <table class="invoice-items">
    <thead>
      <tr>
        <th style="width: 5%;">S.No</th>
        <th style="width: 35%;">Description</th>
        <th style="width: 8%;">Size</th>
        <th style="width: 10%;">Art</th>
        <th style="width: 10%;">HSN/SAC</th>
        <th style="width: 6%;">UOM</th>
        <th style="width: 8%;">Quantity</th>
        <th style="width: 8%;">Unit Price</th>
        <th style="width: 10%;">Amount</th>
      </tr>
    </thead>
    <tbody>
      @php
          $totalQty = 0;
      @endphp
      @foreach($creditNote->items as $index => $item)
        @php
            $invoiceItem = $item->salesInvoiceItem;

            $itemName = '-';
            $hsnSac = '';
            
            if ($invoiceItem) {
                $hsnSac = $invoiceItem->hsn_sac ?: ($invoiceItem->salesInvoice->hsn_sac ?? '');
                
                if ($invoiceItem->stockEntryItem) {
                    $brandName = '';
                    if ($invoiceItem->stockEntryItem->brand) {
                      $brandName = $invoiceItem->stockEntryItem->brand->brand_name;
                    } else if ($invoiceItem->stockEntryItem->finished_item_code) {
                        $parts = explode('-', $invoiceItem->stockEntryItem->finished_item_code);
                        if (count($parts) > 0) {
                          $brand = \App\Models\Brand::where('code', $parts[0])->first();
                          if ($brand) {
                            $brandName = $brand->brand_name;
                          }
                        }
                    }
                    $styleName = $invoiceItem->stockEntryItem->style ? $invoiceItem->stockEntryItem->style->style_name : '';
                    $itemName = trim(trim($brandName) . ' ' . trim($styleName));
                    if (!$itemName) {
                      $itemName = $invoiceItem->stockEntryItem->finished_item_code;
                    }
                } elseif ($invoiceItem->item) {
                    $brandName = $invoiceItem->item->brand ? $invoiceItem->item->brand->brand_name : '';
                    $styleName = $invoiceItem->item->style ? $invoiceItem->item->style->style_name : '';
                    $itemName = trim(trim($brandName) . ' ' . trim($styleName));
                    if (!$itemName) {
                      $itemName = $invoiceItem->item->name;
                    }
                }
            }

            $sleeveFull = '';
            if ($invoiceItem && $invoiceItem->sleeve_type) {
                $st = strtolower(trim($invoiceItem->sleeve_type));
                if ($st == 'full' || $st == 'f/s' || $st == 'fs') {
                  $sleeveFull = ' Full Sleeve';
                } elseif ($st == 'half' || $st == 'h/s' || $st == 'hs') {
                  $sleeveFull = ' Half Sleeve';
                } else {
                  $sleeveFull = ' ' . trim($invoiceItem->sleeve_type) . ' Sleeve';
                }
            }

            if ($itemName != '-' && $itemName != '') {
              $itemName = trim($itemName . $sleeveFull);
            } else {
              $itemName = trim($sleeveFull) ?: '-';
            }

            $artNo = $invoiceItem ? ($invoiceItem->art_no ?? '-') : '-';
            $uomCode = $invoiceItem && $invoiceItem->uom ? $invoiceItem->uom->uom_code : 'PCS';
            
            $sizeName = '-';
            if ($invoiceItem) {
              $sizeName = $invoiceItem->sizeRatio ? $invoiceItem->sizeRatio->size : ($invoiceItem->size ?? '-');
            }
            
            $totalQty += $item->quantity;
        @endphp
        <tr>
            <td class="center">{{ $index + 1 }}</td>
            <td>{{ strtoupper($itemName) }}</td>
            <td>{{ $sizeName }}</td>
            <td>{{ $artNo }}</td>
            <td>{{ $hsnSac }}</td>
            <td>{{ $uomCode }}</td>
            <td class="right">{{ number_format($item->quantity, 2) }}</td>
            <td class="right">{{ number_format($item->rate, 2) }}</td>
            <td class="right">{{ number_format($item->amount, 2) }}</td>
        </tr>
      @endforeach

      <!-- Empty rows to push footer down -->
      @for($i = count($creditNote->items); $i < 15; $i++)
        <tr>
            <td style="height:25px;"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
      @endfor
      <tr style="border-bottom:1px solid #000;">
        <td style="text-align:center; border-right:1px solid #000;"></td>
        <td style="text-align:center; border-right:1px solid #000;"></td>
        <td style="text-align:center; border-right:1px solid #000;"></td>
        <td style="text-align:center; border-right:1px solid #000;"></td>
        <td style="text-align:center; border-right:1px solid #000;"></td>
        <td style="text-align:center; border-right:1px solid #000;"></td>
        <td class="center" style="border-right:1px solid #000; border-top:1px solid #000;">{{ number_format($totalQty, 2) }}</td>
        <td class="center" style="border-right:1px solid #000;">Gross</td>
        <td class="right" style="border-top:1px solid #000;">{{ number_format($creditNote->sub_total, 2) }}</td>
      </tr>
    </tbody>
  </table>

  <!-- FOOTER SECTION -->
  <table class="footer-section">
    <tr>
      <td class="footer-left">
        <div class="footer-row" style="font-size:12px; border-bottom:1px solid #000;">
          IRN &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $creditNote->irn ?? '' }} <br>
          Ack No.: {{ $creditNote->ack_no ?? '' }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Ack Date: {{ $creditNote->ack_date ? \Carbon\Carbon::parse($creditNote->ack_date)->format('d/m/Y') : '' }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Eway Bill No.: {{ $creditNote->eway_bill_no ?? '' }}
        </div>
        <div class="footer-row bank-details">
          <strong>Company's Bank Details :</strong><br>
          Bank Name : {{ $setting->bank_name ?? '-' }}, {{ $setting->branch_location ?? '-' }}<br>
          A/c No. : {{ $setting->account_no ?? '-' }}<br>
          Branch & IFS Code : {{ $setting->ifsc_code ?? '-' }}
        </div>
        <div class="footer-row" style="border-bottom:none; font-size:12px;">
          Rupees &nbsp;&nbsp;&nbsp;: &nbsp;&nbsp;{{ strtoupper($totalInWords) }} 
        </div>
      </td>
      
      <!-- Middle Column (Labels) -->
      <td style="width: 21%; padding: 0; vertical-align: top; border-right: 1px solid #000;">
        <table style="width: 100%; border-collapse: collapse;">
          @php
              $taxableValue = $creditNote->sub_total - $creditNote->discount;
          @endphp
          <tr>
            <td style="padding: 4px 5px; text-align: right; font-size:13px;">Taxable Value</td>
          </tr>
          @if($creditNote->other_state)
            <tr>
              <td style="padding: 4px 5px; text-align: right; font-size:13px;">IGST @ {{ $creditNote->igst_percent }}%</td>
            </tr>
          @else
            <tr>
              <td style="padding: 4px 5px; text-align: right; font-size:13px;">CGST @ {{ $creditNote->cgst_percent }}%</td>
            </tr>
            <tr>
              <td style="padding: 4px 5px; text-align: right; font-size:13px;">SGST @ {{ $creditNote->sgst_percent }}%</td>
            </tr>
          @endif
          @if($creditNote->round_off != 0)
          <tr>
            <td style="padding: 4px 5px; text-align: right; font-size:13px;">Round Off</td>
          </tr>
          @endif
          <tr>
            <td style="padding: 4px 5px; text-align: right; font-weight:bold; font-size:12px;">Total</td>
          </tr>
        </table>
      </td>

      <!-- Right Column (Values) -->
      <td style="width: 14%; padding: 0; vertical-align: top;">
        <table style="width: 100%; border-collapse: collapse;">
          <tr>
            <td style="padding: 4px 5px; text-align: right; font-size:13px;">{{ number_format($taxableValue, 2) }}</td>
          </tr>
          @if($creditNote->other_state)
            <tr>
              <td style="padding: 4px 5px; text-align: right; font-size:13px;">{{ number_format($creditNote->igst, 2) }}</td>
            </tr>
          @else
            <tr>
              <td style="padding: 4px 5px; text-align: right; font-size:13px;">{{ number_format($creditNote->cgst, 2) }}</td>
            </tr>
            <tr>
              <td style="padding: 4px 5px; text-align: right; font-size:13px;">{{ number_format($creditNote->sgst, 2) }}</td>
            </tr>
          @endif
          @if($creditNote->round_off != 0)
          <tr>
            <td style="padding: 4px 5px; text-align: right; font-size:13px;">{{ $creditNote->round_off_type == 'Less' ? '-' : '+' }}{{ number_format($creditNote->round_off, 2) }}</td>
          </tr>
          @endif
          <tr>
            <td style="padding: 4px 5px; text-align: right; font-weight:bold; font-size:12px;">{{ number_format($creditNote->grand_total, 2) }}</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</div> <!-- End page wrapper -->

<table width="100%" cellpadding="0" cellspacing="0" style="border-bottom: 1px solid #000;">
  <tr>
      <td width="65%" style="padding: 10px; vertical-align:top; font-size:13px;">
        Remarks : <br>
        {{ $creditNote->remarks }}
      </td>
      <td width="35%" style="text-align:center; vertical-align:middle; padding:5px;">
        <div class="signature-box">
          <div style="font-size:10px;">For {{ $setting->company_name }}</div>
          <div class="signature-line"></div>
          <div class="signature-text" style="font-size:13px;">Authorised Signatory</div>
        </div>
      </td>
  </tr>
</table>
  @if(isset($is_print) && $is_print)
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
  @endif
</body>
</html>

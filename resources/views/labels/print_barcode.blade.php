<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Barcode Label</title>
    <style>
        @page {
            size: {{ $width }}mm {{ $height }}mm;
            margin: 0;
        }
        html, body {
            width: {{ $width }}mm;
            height: {{ $height }}mm;
            margin: 0;
            padding: 0;
            font-family: 'Arial Narrow', Arial, sans-serif;
            background-color: white;
            -webkit-print-color-adjust: exact;
        }
        .label-container {
            width: {{ $width }}mm;
            height: {{ $height }}mm;
            padding: {{ $margin }}mm;
            background-color: {{ $bg_color }};
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
        }

        /* Header */
        .header-section {
            padding-bottom: 1mm;
            margin-bottom: 0mm;
        }
        .mkt-by { font-size: 7.5pt; font-weight: bold; margin-bottom: 0.5mm; }
        .comp-name { font-size: 9.5pt; font-weight: bold; margin-bottom: 1mm; }
        .address-text { font-size: 7pt; line-height: 1.2; margin-bottom: 1mm; color: #333; text-transform: uppercase; }
        .contact-row { font-size: 7pt; margin-bottom: 0.5mm; font-weight: normal; }

        /* Content Area */
        .content-area { width: 100%; position: relative; }
        .data-table { width: 100%; border-collapse: collapse; }
        .label-col { width: 18mm; font-size: 8.5pt; font-weight: bold; vertical-align: top; padding: 0.8mm 0; }
        .colon-col { width: 3mm; font-size: 8.5pt; font-weight: bold; vertical-align: top; padding: 0.8mm 0; }
        .value-col { font-size: 8.5pt; font-weight: 800; vertical-align: top; padding: 0.8mm 0; text-transform: uppercase; padding-right: 2mm; white-space: nowrap; }
        
        .logo-box {
            position: absolute;
            right: 0;
            top: 2mm;
            width: 10mm;
            height: 10mm;
            padding: 1mm;
            display: flex;
            justify-content: center;
            align-items: center;
            background: white;
            z-index: 10;
        }

        /* MRP & QR Section */
        .mrp-qr-section {
            padding-top: 0mm;
            margin-top: 1mm;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .mrp-left { flex: 1; }
        .mrp-row { display: flex; align-items: baseline; margin-bottom: 0mm; }
        .mrp-label { width: 18mm; font-size: 8.5pt; font-weight: bold; }
        .mrp-colon { width: 3mm; font-size: 8.5pt; font-weight: bold; }
        .mrp-symbol { font-size: 8.5pt; font-weight: 800; margin-right: 1.5mm; }
        .mrp-val { font-size: 9.5pt; font-weight: 800; letter-spacing: 0.1px; }
        .mrp-sub { font-size: 6.2pt; font-weight: normal; display: block; margin-top: 0mm; margin-left: 18mm; }

        .mfg-lot { font-size: 8.5pt; font-weight: bold; line-height: 1.4; margin-top: 1mm; }

        .qr-section { width: 18mm; text-align: center; }
        .sku-text { font-size: 9pt; font-weight: bold; margin-top: 1mm; letter-spacing: 0.2px; }

        /* Footer */
        .footer-section {
            margin-top: 0mm;
            font-size: 7.5pt;
            font-weight: normal;
        }
        .footer-row { margin-bottom: 0.5mm; }
        .flex-footer { display: flex; justify-content: space-between; align-items: flex-end; }
        .made-in { font-size: 8pt; font-weight: normal; }
    </style>
</head>
<body onload="window.print()">
    <div class="label-container">
        <div class="header-section">
            <div class="mkt-by">MANUFACTURED & MARKETED BY</div>
            <div class="comp-name">{{ strtoupper($labelData['company_name']) }}</div>
            <div class="address-text">
                {!! nl2br(e($labelData['company_address'])) !!}
            </div>
                <div class="contact-row">TOLL-FREE : {{ $labelData['toll_free'] }}</div>
                <div class="contact-row">EMAIL : {{ $labelData['company_email'] }}</div>
                <div class="contact-row">GSTIN : {{ $labelData['company_gstin'] }}</div>
        </div>

        @php $fields = explode(',', $order); @endphp
        
        @foreach($fields as $field)

            @if(in_array($field, ['product', 'brand', 'art', 'color', 'fabric', 'size']))
                @if($loop->first || $fields[$loop->index - 1] == 'header' || $fields[$loop->index - 1] == 'mfg' || $fields[$loop->index - 1] == 'footer' || $fields[$loop->index - 1] == 'mrp')
                <div class="content-area">
                    <div class="logo-box">
                        <img src="{{ url('assets/images/fav.jpeg') }}" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                    <table class="data-table">
                @endif

                    @if($field == 'product')
                    <tr>
                        <td class="label-col">PRODUCT</td>
                        <td class="colon-col">:</td>
                        <td class="value-col">{{ $labelData['product_name'] }}</td>
                    </tr>
                    @elseif($field == 'brand')
                    <tr>
                        <td class="label-col">BRAND NAME</td>
                        <td class="colon-col">:</td>
                        <td class="value-col">{{ $labelData['brand_name'] }}</td>
                    </tr>
                    @elseif($field == 'art')
                    <tr>
                        <td class="label-col">ART NO</td>
                        <td class="colon-col">:</td>
                        <td class="value-col">{{ $labelData['design'] }}</td>
                    </tr>
                    @elseif($field == 'color')
                    <tr>
                        <td class="label-col">COLOUR</td>
                        <td class="colon-col">:</td>
                        <td class="value-col">{{ $labelData['color'] }}</td>
                    </tr>
                    @elseif($field == 'fabric')
                    <tr>
                        <td class="label-col">FABRIC</td>
                        <td class="colon-col">:</td>
                        <td class="value-col">{{ $labelData['fabric'] }}</td>
                    </tr>
                    @elseif($field == 'size')
                    <tr>
                        <td class="label-col">SIZE</td>
                        <td class="colon-col">:</td>
                        <td class="value-col">
                            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; padding-right: 2mm;">
                                <span>{{ $labelData['size'] }} CM</span>
                                <div style="font-size: 8.5pt;">
                                    <span style="font-weight: 800;">SLEEVE</span>
                                    <span style="margin-left: 1mm;">
                                        @php
                                            $sText = $labelData['sleeve'];
                                            $sText = str_replace(['F/S', 'H/S'], ['FULL', 'HALF'], $sText);
                                        @endphp
                                        {{ $sText }}
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endif

                @if($loop->last || !in_array($fields[$loop->index + 1], ['product', 'brand', 'art', 'color', 'fabric', 'size']))
                    </table>
                </div>
                @endif

            @elseif($field == 'mrp')
                <div class="mrp-qr-section">
                    <div class="mrp-left">
                        <div class="mrp-row">
                            <span class="mrp-label">MRP</span>
                            <span class="mrp-colon">:</span>
                            <span class="mrp-symbol">₹</span>
                            <span class="mrp-val">{{ number_format((float) $labelData['price'], 2) }}</span>
                        </div>
                        <span class="mrp-sub">(Inclusive of All Taxes)</span>

                        @if(isset($fields[$loop->index + 1]) && $fields[$loop->index + 1] == 'mfg')
                            <div class="mfg-lot">
                                <div>DATE OF MFG : {{ strtoupper($labelData['mfg_date']) }}</div>
                                <div style="margin-top: 1mm;">LOT #{{ $labelData['lot_no'] }}</div>
                            </div>
                        @endif
                    </div>
                    <div class="qr-section">
                        @php
                            $qrString = ($labelData['sku'] ?? '-') . " | " . ($labelData['product_name'] ?? '-') . " | " . ($labelData['size'] ?? '-');
                        @endphp
                        {!! QrCode::size(65)->generate($qrString) !!}
                        <div class="sku-text">{{ $labelData['sku'] }}</div>
                    </div>
                </div>

            @elseif($field == 'mfg')
                @if(!isset($fields[$loop->index - 1]) || $fields[$loop->index - 1] != 'mrp')
                <div class="mfg-lot" style="margin: 2mm 0;">
                    <div>DATE OF MFG : {{ strtoupper($labelData['mfg_date']) }}</div>
                    <div style="margin-top: 1mm;">LOT #{{ $labelData['lot_no'] }}</div>
                </div>
                @endif
            @endif
        @endforeach

        <div class="footer-section">
            <div class="footer-row">FOR COMPLAINTS PLEASE CONTACT</div>
            <div class="footer-row">CUSTOMERCARE EXECUTIVE</div>

            <div class="flex-footer" style="margin-top: 1mm;">
                <div class="footer-row">ADDRESS AS ABOVE</div>
                <div class="made-in">MADE IN INDIA</div>
            </div>

            <div class="footer-row">{{ strtoupper($labelData['company_name']) }}</div>

            <div class="footer-row" style="margin-top: 0.5mm;">
                MOBILE: {{ $labelData['phone_number'] }} &nbsp;({{ $labelData['opening_time'] }} TO {{ $labelData['closing_time'] }})
            </div>
            <div class="footer-row" style="font-size: 6.5pt; margin-left: 23mm;">
                {{ strtoupper($labelData['working_days']) }}
            </div>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Sticker - {{ $invoice->inv_no }}</title>
    <style>
        @page {
            size: 100mm 150mm;
            margin: 0 !important;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        html {
            width: 100mm;
            height: 150mm;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Arial Narrow', Arial, sans-serif;
            width: 100mm;
            height: 150mm;
            padding: 2mm;
            background: #fff;
            margin: 0;
        }
        .sticker {
            border: 3px solid #000;
            border-radius: 0px; /* Square/sharp corners to match original */
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .content {
            padding: 10px 12px 6px 12px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .to {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 4px 0;
            vertical-align: top;
            font-weight: bold;
        }
        .lbl {
            font-size: 20px;
            width: 120px;
            white-space: nowrap;
        }
        .eq {
            font-size: 20px;
            width: 25px;
            text-align: center;
        }
        .val {
            font-size: 20px;
            text-transform: uppercase;
        }
        .val-party {
            font-size: 22px;
            text-transform: uppercase;
        }
        .val-place {
            font-size: 40px;
            font-family: 'Arial Black', Impact, sans-serif;
            text-transform: uppercase;
            line-height: 1;
        }
        .val-inv {
            font-size: 30px;
            font-family: 'Arial Black', Impact, sans-serif;
            text-transform: uppercase;
            line-height: 1;
        }
        .val-pcs {
            font-size: 50px;
            font-family: 'Arial Black', Impact, sans-serif;
            line-height: 1;
            text-align: center;
            width: 100%;
            display: inline-block;
        }
        .val-box {
            font-size: 42px;
            font-family: 'Arial Black', Impact, sans-serif;
            line-height: 1;
            text-align: center;
            width: 100%;
            display: inline-block;
        }
        .disclaimer {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.3;
            margin-top: auto;
            padding-top: 6px;
            text-align: left;
        }
        .footer {
            flex-shrink: 0;
            border-top: 2px solid #000;
            padding: 6px 10px 8px;
            text-align: center;
        }
        .footer .f1 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
            text-align: left;
        }
        .footer .f2 {
            font-size: 18px;
            font-weight: bold;
            font-family: 'Arial Black', sans-serif;
            margin-bottom: 2px;
        }
        .footer .f3 {
            font-size: 12px;
            font-weight: bold;
            line-height: 1.3;
        }
        @media print {
            html, body {
                width: 100mm;
                height: 150mm;
                margin: 0 !important;
                padding: 0 !important;
            }
            .sticker {
                width: 100%;
                height: 100%;
                padding: 2mm;
                box-sizing: border-box;
            }
        }
    </style>
</head>
<body onload="{{ isset($is_print) && $is_print ? 'window.print()' : '' }}">
    <div class="sticker">
        <div class="content">
            <div class="to">TO :-</div>

            <table>
                <tr>
                    <td class="lbl">PARTY</td>
                    <td class="eq">=</td>
                    <td class="val-party">{{ strtoupper($invoice->customer->name ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td class="lbl">ADDRESS</td>
                    <td class="eq">=</td>
                    <td class="val">{{ strtoupper(implode(', ', array_filter([$invoice->customer->address_line_1 ?? '', $invoice->customer->address_line_2 ?? '', $invoice->customer->address_line_3 ?? '']))) }}</td>
                </tr>
                <tr>
                    <td class="lbl">PLACE</td>
                    <td class="eq">=</td>
                    <td class="val-place">{{ strtoupper($invoice->customer->city->city_name ?? ($invoice->customer->place->place_name ?? 'N/A')) }}</td>
                </tr>
                <tr>
                    <td class="lbl">INV NO</td>
                    <td class="eq">=</td>
                    <td class="val-inv">{{ strtoupper($invoice->inv_no) }}</td>
                </tr>
                <tr>
                    <td class="lbl" style="vertical-align: middle;">NO OF PCS</td>
                    <td class="eq" style="vertical-align: middle;">=</td>
                    <td style="vertical-align: middle;"><span class="val-pcs">{{ intval($totalPcs) }}</span></td>
                </tr>
                <tr>
                    <td class="lbl" style="vertical-align: middle;">NO OF BOX</td>
                    <td class="eq" style="vertical-align: middle;">=</td>
                    <td style="vertical-align: middle;"><span class="val-box">{{ $boxCount }}</span></td>
                </tr>
            </table>

            <div class="disclaimer">*THE ORIGINAL INVOICE FOR YOUR PURCHASE WILL BE SECURALY PLACED INSIDE THE CARTON IN A CASINO ENVELOPE</div>
        </div>

        <div class="footer">
            <div class="f1">MANUFEDTURED &amp; MARKETED BY:-</div>
            <div class="f2">NACHIAS FASHION PVT LTD</div>
            <div class="f3">272/2, SOMUNAGAR, SRINGERI NAGAR,<br>BY-PASS ROAD, MADURAI - 625 016.<br>PNO:9443330774,8489938214</div>
        </div>
    </div>
</body>
</html>

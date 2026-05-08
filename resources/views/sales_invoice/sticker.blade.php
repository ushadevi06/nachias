<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Sticker - {{ $invoice->inv_no }}</title>
    <style>
        @page {
            size: 100mm 150mm;
            margin: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial Narrow', Arial, sans-serif;
            width: 100mm;
            height: 150mm;
            padding: 2mm;
            background: #fff;
        }
        .sticker {
            border: 3px solid #000;
            border-radius: 14px;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .content {
            padding: 12px 14px 6px 14px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .to {
            font-size: 22px;
            font-weight: 900;
            margin-bottom: 6px;
        }
        .row {
            display: flex;
            align-items: baseline;
            margin-bottom: 6px;
        }
        .lbl {
            font-size: 16px;
            font-weight: 900;
            min-width: 80px;
            flex-shrink: 0;
        }
        .eq {
            font-size: 16px;
            font-weight: 900;
            margin: 0 4px;
        }
        .val {
            font-size: 16px;
            font-weight: 900;
            text-transform: uppercase;
        }
        .val-party {
            font-size: 20px;
            font-weight: 900;
            text-transform: uppercase;
        }
        .val-place {
            font-size: 44px;
            font-weight: 900;
            font-family: 'Arial Black', Impact, sans-serif;
            text-transform: uppercase;
            line-height: 1;
        }
        .val-inv {
            font-size: 30px;
            font-weight: 900;
            font-family: 'Arial Black', Impact, sans-serif;
            text-transform: uppercase;
            line-height: 1;
        }
        .val-pcs {
            font-size: 64px;
            font-weight: 900;
            font-family: 'Arial Black', Impact, sans-serif;
            text-align: center;
            flex: 1;
            line-height: 1;
        }
        .val-box {
            font-size: 50px;
            font-weight: 900;
            font-family: 'Arial Black', Impact, sans-serif;
            text-align: center;
            flex: 1;
            line-height: 1;
        }
        .disclaimer {
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            line-height: 1.2;
            margin-top: auto;
            padding-top: 4px;
        }
        .footer {
            border-top: 2px solid #000;
            padding: 8px 14px 10px;
            border-radius: 0 0 11px 11px;
        }
        .footer .f1 {
            font-size: 13px;
            font-weight: 900;
            margin-bottom: 1px;
        }
        .footer .f2 {
            font-size: 14px;
            font-weight: 900;
            font-family: 'Arial Black', sans-serif;
            margin-bottom: 1px;
            text-align: center;
            letter-spacing: 1px;
        }
        .footer .f3 {
            letter-spacing: 0.7px;
            font-size: 12px;
            font-weight: 900;
            line-height: 1.2;
            text-align: center;
        }
        @media print {
            body { width: 100mm; height: 150mm; padding: 3mm; }
            .footer { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="{{ isset($is_print) && $is_print ? 'window.print()' : '' }}">
    <div class="sticker">
        <div class="content">
            <div class="to">TO :-</div>

            <div class="row">
                <span class="lbl">PARTY</span>
                <span class="eq">=</span>
                <span class="val-party">{{ strtoupper($invoice->customer->name ?? 'N/A') }}</span>
            </div>

            <div class="row">
                <span class="lbl">ADDRESS</span>
                <span class="eq">=</span>
                <span class="val">{{ strtoupper(implode(', ', array_filter([$invoice->customer->address_line_1 ?? '', $invoice->customer->address_line_2 ?? '', $invoice->customer->address_line_3 ?? '']))) }}</span>
            </div>

            <div class="row">
                <span class="lbl">PLACE</span>
                <span class="eq">=</span>
                <span class="val-place">{{ strtoupper($invoice->customer->city->city_name ?? ($invoice->customer->place->place_name ?? 'N/A')) }}</span>
            </div>

            <div class="row">
                <span class="lbl">INV NO</span>
                <span class="eq">=</span>
                <span class="val-inv">{{ strtoupper($invoice->inv_no) }}</span>
            </div>

            <div class="row">
                <span class="lbl">NO OF PCS</span>
                <span class="eq">=</span>
                <span class="val-pcs">{{ intval($totalPcs) }}</span>
            </div>

            <div class="row">
                <span class="lbl">NO OF BOX</span>
                <span class="eq">=</span>
                <span class="val-box">{{ $boxCount }}</span>
            </div>
            <div class="disclaimer">*THE ORIGINAL INVOICE FOR YOUR PURCHASE WILL BE SECURELY PLACED INSIDE THE CARTON IN A CASINO ENVELOPE</div>
        </div>

        <div class="footer">
            <div class="f1">MANUFACTURED &amp; MARKETED BY:-</div>
            <div class="f2">NACHIAS FASHION PVT LTD</div>
            <div class="f3">272/2, SOMUNAGAR, SRINGERI NAGAR,<br>BY-PASS ROAD, MADURAI - 625 016.<br>PNO:9443330774,8489938214</div>
        </div>
    </div>
</body>
</html>

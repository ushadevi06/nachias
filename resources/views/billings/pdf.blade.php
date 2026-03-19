<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Billing Invoice - {{ $billing->bill_no }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .header-table td {
            border: none;
        }
        .no-border th, .no-border td {
            border: none;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .header-title {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
            text-transform: uppercase;
        }
        .company-info {
            line-height: 1.6;
        }
        .billing-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            color: white;
            font-size: 10px;
            font-weight: bold;
        }
        .bg-success { background-color: #28a745; }
        .bg-warning { background-color: #ffc107; color: #000; }
        .bg-danger { background-color: #dc3545; }
        .bg-info { background-color: #17a2b8; }
    </style>
</head>
<body>
    <div class="container">
        <table class="header-table">
            <tr>
                <td width="60%">
                    @php
                        $logoPath = public_path('assets/images/jc_logo.png');
                        $logoBase64 = '';
                        if (file_exists($logoPath)) {
                            $logoData = file_get_contents($logoPath);
                            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
                        }
                    @endphp
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" style="max-width: 200px;">
                    @else
                        <h2 style="margin: 0; color: #2c3e50;">{{ env('WEBSITE_NAME') }}</h2>
                    @endif
                </td>
                <td width="50%" class="text-right company-info">
                    <div class="bold">{{ $setting->company_name ?? env('WEBSITE_NAME') }}</div>
                    <div>{{ $setting->address ?? '' }}</div>
                    <div>Email: {{ $setting->email ?? '' }}</div>
                    <div>Mobile: {{ $setting->toll_free_no ?? '' }}</div>
                    @if($setting->gst_no)
                        <div>GSTIN: {{ $setting->gst_no }}</div>
                    @endif
                </td>
            </tr>
        </table>

        <div class="header-title">Billing Invoice</div>

        <div class="billing-info">
            <table class="no-border" style="margin-bottom: 0;">
                <tr>
                    <td width="20%" class="bold">Bill Number:</td>
                    <td width="30%">{{ $billing->bill_no }}</td>
                    <td width="20%" class="bold">Bill Date:</td>
                    <td width="30%">{{ $billing->bill_date->format('d-M-Y') }}</td>
                </tr>
                <tr>
                    <td class="bold">Bill Type:</td>
                    <td>{{ $billing->billing_type }}</td>
                    <td class="bold">Status:</td>
                    <td>
                        <span class="badge {{ 
                            $billing->status == 'Paid' ? 'bg-success' : 
                            ($billing->status == 'Pending' ? 'bg-warning' : 
                            ($billing->status == 'Cancelled' ? 'bg-danger' : 'bg-info')) 
                        }}">
                            {{ $billing->status }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <table>
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th>Description</th>
                    <th class="text-right" width="20%">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="height: 100px; vertical-align: top;">
                        {{ $billing->reason ?? 'Billing payment for ' . $billing->billing_type }}
                    </td>
                    <td class="text-right bold" style="vertical-align: top;">
                        &#8377;{{ number_format($billing->amount, 2) }}
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td class="text-right bold">Total Amount:</td>
                    <td class="text-right bold" style="font-size: 16px;">
                        &#8377;{{ number_format($billing->amount, 2) }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="bold">
                        Amount in Words: <span style="font-weight: normal; text-transform: capitalize;">{{ strtolower($totalInWords) }}</span>
                    </td>
                </tr>
            </tfoot>
        </table>

        <table class="no-border" style="margin-top: 50px;">
            <tr>
                <td width="60%">
                    <div class="bold">Notes:</div>
                    <div style="font-size: 10px; color: #666;">
                        1. This is a computer generated invoice.<br>
                        2. Detailed breakdown of services provided for the billing type mentioned above.
                    </div>
                </td>
                <td width="40%" class="text-center">
                    <br><br>
                    <div style="border-top: 1px solid #000; display: inline-block; width: 200px;">
                        Authorised Signatory
                    </div>
                </td>
            </tr>
        </table>

        <div class="footer">
            Thank you for your business!
        </div>
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

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Voucher</title>
    <style>
        @page { size: A4; margin: 20px; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #333; line-height: 1.5; margin: 0; padding: 0; }
        .container { width: 100%; border: 1px solid #000; padding: 0; box-sizing: border-box; }
        .header { border-bottom: 2px solid #000; padding: 15px; background: #f8f9fa; }
        .company-name { font-size: 18px; font-weight: bold; margin-bottom: 5px; color: #000; }
        .voucher-title { font-size: 16px; font-weight: bold; text-align: center; background: #333; color: #fff; padding: 5px; margin: 10px 0; }
        .section-title { font-weight: bold; text-decoration: underline; margin-bottom: 10px; font-size: 12px; }
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; table-layout: fixed; }
        .details-table td { padding: 8px; vertical-align: top; border: 1px solid #eee; word-wrap: break-word; }
        .details-table td:nth-child(1),
        .details-table td:nth-child(3) { font-weight: bold; color: #666; width: 15%; }
        .details-table td:nth-child(2),
        .details-table td:nth-child(4) { color: #000; width: 35%; }
        .amount-section { background: #f8f9fa; padding: 15px; border-top: 1px solid #000; border-bottom: 1px solid #000; margin: 20px 0; }
        .amount-row { font-size: 14px; font-weight: bold; margin-bottom: 10px; }
        .words { font-style: italic; color: #444; }
        .signature-table { width: 100%; margin-top: 50px; }
        .signature-box { text-align: center; width: 33.33%; padding-top: 40px; }
        .sig-line { border-top: 1px solid #000; width: 150px; margin: 0 auto 5px auto; }
        .footer { text-align: center; font-size: 9px; color: #888; margin-top: 20px; border-top: 1px dashed #ccc; padding-top: 10px; }

        /* Browser print: constrain to A4 width */
        @media screen {
            body { display: flex; justify-content: center; padding: 20px; background: #e0e0e0; }
            .container { max-width: 780px; background: #fff; }
        }
        @media print {
            body { background: #fff; padding: 0; display: block; }
            .container { max-width: 100%; border: 1px solid #000; }
            .voucher-title { background: #333 !important; color: #fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .header { background: #f8f9fa !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .amount-section { background: #f8f9fa !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <table width="100%">
                <tr>
                    <td width="20%">
                        <img src="{{ isset($is_print) && $is_print ? asset('assets/images/jc_logo.png') : public_path('assets/images/jc_logo.png') }}" style="width: 100px;">
                    </td>
                    <td width="80%" align="right">
                        <div class="company-name">{{ $setting->company_name }}</div>
                        <div>{{ $setting->address }}</div>
                        <div>{{ $setting->city->city_name ?? $setting->city }} - {{ $setting->zip_code }}</div>
                        <div>GSTIN: <strong>{{ $setting->gst_no }}</strong> | MOB: {{ $setting->toll_free_no }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="voucher-title">PAYMENT VOUCHER</div>

        <div style="padding: 20px;">
            <table class="details-table">
                <tr>
                    <td class="label">Voucher No:</td>
                    <td class="value"><strong>{{ $payment->payment_no }}</strong></td>
                    <td class="label">Date:</td>
                    <td class="value">{{ date('d-m-Y', strtotime($payment->payment_date)) }}</td>
                </tr>
                <tr>
                    <td class="label">Payment Type:</td>
                    <td class="value">{{ $payment->payment_type }}</td>
                    <td class="label">Payment Mode:</td>
                    <td class="value"><strong>{{ $payment->payment_mode }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Reference Type:</td>
                    <td class="value">{{ $payment->reference_type }}</td>
                    <td class="label">Reference No:</td>
                    <td class="value text-primary"><strong>{{ $payment->reference_no ?: 'N/A' }}</strong></td>
                </tr>
                @if($payment->bank_name)
                <tr>
                    <td class="label">Bank Name:</td>
                    <td class="value" colspan="3">{{ $payment->bank_name }}</td>
                </tr>
                @endif
                @if($payment->transaction_no || $payment->cheque_no)
                <tr>
                    <td class="label">{{ $payment->cheque_no ? 'Cheque No' : 'Transaction No' }}:</td>
                    <td class="value">{{ $payment->cheque_no ?: $payment->transaction_no }}</td>
                    <td class="label">{{ $payment->cheque_date ? 'Cheque Date :' : '' }}</td>
                    <td class="value">{{ $payment->cheque_date ? date('d-m-Y', strtotime($payment->cheque_date)) : '' }}</td>
                </tr>
                @endif
            </table>

            <div class="amount-section">
                <div class="amount-row">Amount Paid: ₹{{ number_format($payment->amount, 2) }}</div>
                <div class="words text-uppercase"><strong>Amount in words:</strong> {{ $totalInWords }} Only</div>
            </div>

            @if($payment->remarks)
            <div style="margin-top: 20px;">
                <div class="section-title">Remarks / Particulars:</div>
                <div style="padding: 10px; border: 1px dashed #ccc; background: #fff;">
                    {{ $payment->remarks }}
                </div>
            </div>
            @endif

            <table class="signature-table">
                <tr>
                    <td class="signature-box">
                        <div class="sig-line"></div>
                        <div>Receiver's Signature</div>
                    </td>
                    <td class="signature-box">
                    </td>
                    <td class="signature-box">
                        <div style="font-weight: bold; margin-bottom: 40px;">For {{ $setting->company_name }}</div>
                        <div class="sig-line"></div>
                        <div>Authorised Signature</div>
                    </td>
                </tr>
            </table>
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

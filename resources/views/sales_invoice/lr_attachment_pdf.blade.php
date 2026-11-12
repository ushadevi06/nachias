<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attached LR Document - {{ $invoice->inv_no ?? '' }}</title>
    <style>
        @page { margin: 18px 20px; }
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #1f2937; font-size: 11px; margin: 0; padding: 0; }
        .header-box { border-bottom: 2px solid #4c1d95; padding-bottom: 8px; margin-bottom: 12px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .company-name { font-size: 15px; font-weight: bold; color: #4c1d95; }
        .doc-title { font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.4px; color: #111827; margin-top: 2px; }
        .meta-grid { width: 100%; border-collapse: collapse; margin-bottom: 12px; background: #f8fafc; border: 1px solid #cbd5e1; }
        .meta-grid td { padding: 6px 10px; border: 1px solid #e2e8f0; vertical-align: top; }
        .meta-label { font-size: 9px; text-transform: uppercase; color: #64748b; font-weight: bold; display: block; margin-bottom: 2px; }
        .meta-value { font-size: 11px; font-weight: bold; color: #0f172a; }
        .lr-badge { color: #047857; font-family: DejaVu Sans Mono, monospace; font-size: 12px; }
        .image-frame { border: 1.5px solid #cbd5e1; border-radius: 4px; background: #ffffff; padding: 10px; text-align: center; }
        .image-frame img { max-width: 100%; max-height: 780px; width: auto; height: auto; }
        .footer-note { margin-top: 8px; font-size: 9px; color: #64748b; text-align: center; }
    </style>
</head>
<body>
    <div class="header-box">
        <table class="header-table">
            <tr>
                <td style="width: 65%; vertical-align: middle;">
                    <div class="company-name">{{ $setting->company_name ?? 'NACHIAS FASHION PRIVATE LIMITED' }}</div>
                    <div class="doc-title">Attached Lorry Receipt (LR) / Dispatch Copy</div>
                </td>
                <td style="width: 35%; text-align: right; vertical-align: middle;">
                    <div><strong>Invoice No:</strong> {{ $invoice->inv_no ?? '-' }}</div>
                    <div><strong>Invoice Date:</strong> {{ !empty($invoice->inv_date) ? \Carbon\Carbon::parse($invoice->inv_date)->format('d-m-Y') : '-' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="meta-grid">
        <tr>
            <td style="width: 38%;">
                <span class="meta-label">Customer</span>
                <span class="meta-value">{{ $invoice->customer->name ?? '-' }}</span>
            </td>
            <td style="width: 22%;">
                <span class="meta-label">LR Number</span>
                <span class="meta-value lr-badge">{{ $attachment['lr_no'] ?? '-' }}</span>
            </td>
            <td style="width: 22%;">
                <span class="meta-label">Transport Name</span>
                <span class="meta-value">{{ $attachment['transport_name'] ?? '-' }}</span>
            </td>
            <td style="width: 18%;">
                <span class="meta-label">Box No(s)</span>
                <span class="meta-value">{{ $attachment['box_numbers'] ?? '-' }}</span>
            </td>
        </tr>
    </table>

    <div class="image-frame">
        <img src="{{ $attachment['image_data_uri'] }}" alt="LR Receipt Image">
    </div>

    <div class="footer-note">
        Verified digital attachment linked to Invoice {{ $invoice->inv_no ?? '' }} (Dispatched: {{ $attachment['booking_date'] ?? '-' }})
    </div>
</body>
</html>


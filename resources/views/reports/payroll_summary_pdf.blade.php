<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $filename ?? 'Nachias_Payroll_Report' }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15px 20px;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #1e293b;
            background: #ffffff;
            margin: 0;
            padding: 0;
        }
        * {
            box-sizing: border-box;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 8px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .company-title {
            font-size: 16px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .company-sub {
            font-size: 8.5px;
            color: #475569;
            margin-top: 3px;
            line-height: 1.4;
        }
        .report-title-box {
            text-align: right;
        }
        .report-title {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
        }
        .report-period {
            font-size: 11px;
            font-weight: bold;
            color: #2563eb;
            margin-top: 2px;
        }
        .report-meta {
            font-size: 8px;
            color: #94a3b8;
            margin-top: 2px;
        }
        
        /* KPI Summary Bar */
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .kpi-table td {
            width: 16.66%;
            padding: 6px 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            text-align: center;
        }
        .kpi-label {
            font-size: 8px;
            text-transform: uppercase;
            font-weight: bold;
            color: #64748b;
            margin-bottom: 3px;
        }
        .kpi-value {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
        }
        .kpi-value.net {
            color: #166534;
        }

        /* Section Titles */
        .section-header {
            font-size: 11px;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 6px;
            text-transform: uppercase;
            border-left: 3px solid #1e3a8a;
            padding-left: 6px;
        }

        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #000000;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            padding: 6px 8px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        .data-table th.text-center, .data-table td.text-center {
            text-align: center;
        }
        .data-table th.text-end, .data-table td.text-end {
            text-align: right;
        }
        .data-table td {
            padding: 5px 8px;
            border: 1px solid #e2e8f0;
            font-size: 9px;
            color: #334155;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .data-table tr.total-row td {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #000000;
            border-top: 2px solid #cbd5e1;
            border-bottom: 2px solid #cbd5e1;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 4px;
        }
        .badge-success {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }
        .badge-warning {
            background: #fef9c3;
            color: #a16207;
            border: 1px solid #fde047;
        }
        .badge-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .footer {
            margin-top: 15px;
            font-size: 8px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    <!-- Header Table -->
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <div class="company-title">{{ $setting->company_name ?? 'NACHIAS FASHION PRIVATE LIMITED' }}</div>
                <div class="company-sub">
                    {{ preg_replace('/\s+/', ' ', trim($setting->address ?? '')) }}
                    @if(!empty($setting->phone_number))
                        <br><b>Phone:</b> +91 {{ $setting->phone_number }}
                    @endif
                    @if(!empty($setting->email))
                        &bull; <b>Email:</b> {{ $setting->email }}
                    @endif
                    @if(!empty($setting->gst_no))
                        &bull; <b>GSTIN:</b> {{ $setting->gst_no }}
                    @endif
                </div>
            </td>
            <td style="width: 40%;" class="report-title-box">
                <div class="report-title">PAYROLL & WORKFORCE SUMMARY</div>
                <div class="report-period">{{ $data['period_label'] ?? date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</div>
                <div class="report-meta">Generated: {{ date('d-m-Y H:i') }} | Nachias ERP</div>
            </td>
        </tr>
    </table>

@php
    $c = function($val) {
        if ($val === null || $val === '') return 'Rs. 0.00';
        return str_replace('₹', 'Rs. ', $val);
    };
@endphp

    <!-- KPI Summary Bar -->
    <table class="kpi-table">
        <tr>
            <td>
                <div class="kpi-label">Total Employees</div>
                <div class="kpi-value">{{ $data['total_employees'] ?? 0 }}</div>
            </td>
            <td>
                <div class="kpi-label">Gross Payroll</div>
                <div class="kpi-value">{{ $c($data['formatted_total_gross'] ?? '0.00') }}</div>
            </td>
            <td>
                <div class="kpi-label">Total PF</div>
                <div class="kpi-value">{{ $c($data['formatted_total_pf'] ?? '0.00') }}</div>
            </td>
            <td>
                <div class="kpi-label">Total ESI</div>
                <div class="kpi-value">{{ $c($data['formatted_total_esi'] ?? '0.00') }}</div>
            </td>
            <td>
                <div class="kpi-label">Total Deductions</div>
                <div class="kpi-value">{{ $c($data['formatted_total_deductions'] ?? '0.00') }}</div>
            </td>
            <td>
                <div class="kpi-label">Net Payable</div>
                <div class="kpi-value net">{{ $c($data['formatted_total_net'] ?? '0.00') }}</div>
            </td>
        </tr>
    </table>

    <!-- 1. Unit-wise Summary Table -->
    <div class="section-header">1. Unit-wise Summary — {{ $data['period_label'] ?? '' }}</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25%;">Unit</th>
                <th class="text-center" style="width: 10%;">Employees</th>
                <th class="text-end" style="width: 13%;">Gross Payroll</th>
                <th class="text-end" style="width: 11%;">PF</th>
                <th class="text-end" style="width: 11%;">ESI</th>
                <th class="text-end" style="width: 13%;">Deductions</th>
                <th class="text-end" style="width: 17%;">Net Payroll</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($data['units']) && count($data['units']) > 0)
                @foreach($data['units'] as $u)
                    <tr>
                        <td style="font-weight: bold; color: #0f172a;">{{ $u['name'] }}</td>
                        <td class="text-center">{{ $u['employees'] }}</td>
                        <td class="text-end" style="font-weight: 500;">{{ $c($u['formatted_gross']) }}</td>
                        <td class="text-end">{{ $c($u['formatted_pf']) }}</td>
                        <td class="text-end">{{ $c($u['formatted_esi']) }}</td>
                        <td class="text-end">{{ $c($u['formatted_deductions']) }}</td>
                        <td class="text-end" style="font-weight: bold; color: #0f172a;">{{ $c($u['formatted_net']) }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td>TOTAL</td>
                <td class="text-center">{{ $data['total_employees'] ?? 0 }}</td>
                <td class="text-end">{{ $c($data['formatted_total_gross'] ?? '0.00') }}</td>
                <td class="text-end">{{ $c($data['formatted_total_pf'] ?? '0.00') }}</td>
                <td class="text-end">{{ $c($data['formatted_total_esi'] ?? '0.00') }}</td>
                <td class="text-end">{{ $c($data['formatted_total_deductions'] ?? '0.00') }}</td>
                <td class="text-end" style="color: #166534;">{{ $c($data['formatted_total_net'] ?? '0.00') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- 2. Monthly Comparison — Last 3 Months -->
    @if(isset($data['comparison']) && isset($data['comparison']['months']))
        <div class="section-header" style="margin-top: 10px;">2. Monthly Comparison — Last 3 Months</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 25%;">Unit</th>
                    <th class="text-center" style="width: 25%;">{{ $data['comparison']['months'][0]['label'] ?? 'Month 1' }}</th>
                    <th class="text-center" style="width: 25%;">{{ $data['comparison']['months'][1]['label'] ?? 'Month 2' }}</th>
                    <th class="text-center" style="width: 25%;">{{ $data['comparison']['months'][2]['label'] ?? 'Month 3' }}</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($data['comparison']['rows']) && count($data['comparison']['rows']) > 0)
                    @foreach($data['comparison']['rows'] as $r)
                        <tr>
                            <td style="font-weight: bold; color: #0f172a;">{{ $r['name'] ?? '' }}</td>
                            <td class="text-center">{{ $c($r['m1_net']) }}</td>
                            <td class="text-center">{{ $c($r['m2_net']) }}</td>
                            <td class="text-center">{{ $c($r['m3_net']) }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td>TOTAL NET PAYABLE</td>
                    <td class="text-center">{{ $c($data['comparison']['totals']['m1_net'] ?? '0.00') }}</td>
                    <td class="text-center">{{ $c($data['comparison']['totals']['m2_net'] ?? '0.00') }}</td>
                    <td class="text-center" style="color: #166534;">{{ $c($data['comparison']['totals']['m3_net'] ?? '0.00') }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    <div class="footer">
        Confidential Document &bull; Generated from Nachias Fashion ERP &bull; Strictly for internal management & HR use
    </div>

</body>
</html>

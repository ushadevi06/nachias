<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bulk-Payslips-{{ now()->format('Y-m-d') }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 3.5mm 8mm;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9.5px;
            color: #0f172a;
            background: #ffffff;
        }
        .slip-wrapper {
            width: 100%;
            border: 1.5px solid #475569;
            border-radius: 4px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #1e3a8a;
            color: #ffffff;
        }
        .header-table td {
            color: #ffffff;
            vertical-align: middle;
            padding: 7px 10px;
        }
        .company-name {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .company-sub {
            font-size: 8.5px;
            color: #cbd5e1;
            line-height: 1.35;
            margin-top: 2px;
        }
        .payslip-title {
            text-align: right;
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .payslip-period {
            font-size: 9.5px;
            color: #cbd5e1;
            font-weight: normal;
            margin-top: 2px;
        }

        /* Employee Info */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #f8fafc;
            border-bottom: 1px solid #94a3b8;
        }
        .info-table td {
            padding: 5px 8px;
            font-size: 9.5px;
            color: #1e293b;
            vertical-align: middle;
        }
        .info-label {
            color: #475569;
            font-weight: normal;
        }
        .info-val {
            font-weight: bold;
            color: #0f172a;
        }

        /* Salary Breakdown Tables */
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
        }
        .col-header {
            background-color: #e2e8f0;
            color: #0f172a;
            font-size: 9.5px;
            font-weight: bold;
            padding: 6px 8px;
            border-top: 1px solid #94a3b8;
            border-bottom: 1px solid #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table td {
            padding: 7px 8px;
            font-size: 9.5px;
            color: #1e293b;
            border-bottom: 1px solid #f1f5f9;
        }
        .data-table tr:nth-child(even) td {
            background-color: #fcfcfc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .fw-bold {
            font-weight: bold;
        }

        /* Total Rows */
        .total-row td {
            background-color: #cbd5e1 !important;
            color: #0f172a;
            font-weight: bold;
            font-size: 10px;
            padding: 6px 8px;
            border-top: 1px solid #94a3b8;
            border-bottom: 1px solid #94a3b8;
        }

        /* Bottom Summary & Signatures */
        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
        }
        .bottom-table td {
            padding: 6px 8px;
        }
        .net-pay-box {
            background-color: #1e3a8a;
            color: #ffffff;
            padding: 7px 14px;
            border-radius: 4px;
            display: inline-block;
        }
        .net-pay-title {
            font-size: 8.5px;
            color: #cbd5e1;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .net-pay-amt {
            font-size: 15px;
            font-weight: bold;
            color: #ffffff;
        }
        .bank-details {
            font-size: 9px;
            color: #1e293b;
            line-height: 1.45;
        }
        .sign-spacer {
            height: 35px;
            line-height: 35px;
        }
        .sign-cell {
            text-align: center;
            border-top: 1.5px solid #334155;
            padding-top: 5px;
            font-size: 9.5px;
            font-weight: bold;
            color: #1e293b;
            white-space: nowrap;
        }

        /* Cut line between 2 slips on same page */
        .cut-line { width: 100%; text-align: center; height: 12px; line-height: 12px; font-size: 8px; color: #64748b; margin: 2px 0; overflow: hidden; }
        .page-break {
            page-break-after: always;
            height: 0;
            line-height: 0;
        }
    </style>
</head>
<body>

@foreach($salaries as $index => $salary)
    <div class="slip-wrapper">
        {{-- HEADER --}}
        <table class="header-table">
            <tr>
                <td width="60%">
                    <div class="company-name">{{ $setting->company_name ?? env('WEBSITE_NAME', 'NACHIAS') }}</div>
                    <div class="company-sub">
                        {{ $setting->address ?? '-' }} | Ph: +91 {{ $setting->phone_number ?? '-' }}
                    </div>
                </td>
                <td width="40%" class="text-right">
                    <div class="payslip-title">PAYSLIP</div>
                    <div class="payslip-period">
                        @if(!empty($salary->from_date) && !empty($salary->to_date))
                            {{ \Carbon\Carbon::parse($salary->from_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($salary->to_date)->format('d M Y') }}
                        @else
                            {{ \Carbon\Carbon::create()->month($salary->salary_month)->format('F') }} {{ $salary->salary_year }}
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        {{-- EMPLOYEE & ATTENDANCE INFO --}}
        <table class="info-table">
            <tr>
                <td width="28%">
                    <span class="info-label">Emp Name:</span> <span class="info-val">{{ $salary->name }}</span>
                </td>
                <td width="22%">
                    <span class="info-label">Emp Code:</span> <span class="info-val">{{ $salary->emp_id }}</span>
                </td>
                <td width="25%">
                    <span class="info-label">Department:</span> <span class="info-val">{{ $salary->department_name ?? '-' }}</span>
                </td>
                <td width="25%">
                    <span class="info-label">Unit:</span> <span class="info-val">{{ $salary->unit_name ?? '-' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="info-label">Designation:</span> <span class="info-val">{{ $salary->role_name ?? '-' }}</span>
                </td>
                <td>
                    <span class="info-label">Roll Type:</span> <span class="info-val">{{ $salary->roll_type ?? 'On Roll' }}</span>
                </td>
                <td>
                    <span class="info-label">PF No:</span> <span class="info-val">{{ $salary->pf_no ?? '-' }}</span>
                </td>
                <td>
                    <span class="info-label">ESI No:</span> <span class="info-val">{{ $salary->esi_no ?? '-' }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="4" style="background-color: #f1f5f9; border-top: 1px dotted #cbd5e1;">
                    <span class="info-label">Total Days:</span> <span class="info-val">{{ $salary->total_days ?? ($salary->monthDays ?? '-') }}</span>
                    &nbsp;|&nbsp;
                    <span class="info-label">Working:</span> <span class="info-val">{{ $salary->present_days ?? 0 }}</span>
                    &nbsp;|&nbsp;
                    <span class="info-label">LOP/Absent:</span> <span class="info-val">{{ $salary->absent_days ?? 0 }}</span>
                    &nbsp;|&nbsp;
                    <span class="info-label">Holidays:</span> <span class="info-val">{{ $salary->holidays ?? 0 }}</span>
                    &nbsp;|&nbsp;
                    <span class="info-label">OT Hrs:</span> <span class="info-val">{{ number_format($salary->ot_hours ?? 0, 1) }}</span>
                    &nbsp;|&nbsp;
                    <span class="info-label">Late Hrs:</span> <span class="info-val">{{ number_format($salary->late_hours ?? 0, 1) }}</span>
                </td>
            </tr>
        </table>

        {{-- EARNINGS & DEDUCTIONS BREAKDOWN --}}
        <table class="breakdown-table">
            <tr>
                {{-- EARNINGS (LEFT 50%) --}}
                <td width="50%" valign="top" style="border-right: 1px solid #cbd5e1;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="col-header" style="text-align: left;">Earnings Component</th>
                                <th class="col-header text-right">Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Basic Salary</td>
                                <td class="text-right">{{ number_format($salary->basic_salary ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>House Rent Allowance (HRA)</td>
                                <td class="text-right">{{ number_format($salary->hra ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Dearness Allowance (DA)</td>
                                <td class="text-right">{{ number_format($salary->da ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Other Allowance (OA)</td>
                                <td class="text-right">{{ number_format($salary->oa ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Overtime Amount (OT)</td>
                                <td class="text-right">{{ number_format($salary->overtime_amount ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Incentive</td>
                                <td class="text-right">{{ number_format($salary->incentive ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Miscellaneous Allowance</td>
                                <td class="text-right">{{ number_format($salary->misc_amount ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Bus Fare Allowance</td>
                                <td class="text-right">{{ number_format($salary->bus_fare ?? 0, 2) }}</td>
                            </tr>
                            <tr class="total-row">
                                <td>GROSS EARNINGS</td>
                                <td class="text-right">₹ {{ number_format($salary->gross_salary ?? 0, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>

                {{-- DEDUCTIONS (RIGHT 50%) --}}
                <td width="50%" valign="top">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="col-header" style="text-align: left;">Deduction Component</th>
                                <th class="col-header text-right">Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Provident Fund (PF)</td>
                                <td class="text-right">{{ number_format($salary->pf ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Employee State Insurance (ESI)</td>
                                <td class="text-right">{{ number_format($salary->esi ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Loss of Pay (LOP Amount)</td>
                                <td class="text-right">{{ number_format($salary->lop_amount ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Salary Advance Deduction</td>
                                <td class="text-right">{{ number_format($salary->salary_advance ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Late Hours Fine</td>
                                <td class="text-right">{{ number_format($salary->late_fine ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Other Deductions</td>
                                <td class="text-right">{{ number_format($salary->other_deduction ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td style="color: transparent;">-</td>
                                <td class="text-right" style="color: transparent;">0.00</td>
                            </tr>
                            <tr>
                                <td style="color: transparent;">-</td>
                                <td class="text-right" style="color: transparent;">0.00</td>
                            </tr>
                            <tr class="total-row">
                                <td>TOTAL DEDUCTIONS</td>
                                <td class="text-right">₹ {{ number_format($salary->total_deduction ?? 0, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        {{-- NET SALARY & SIGNATURES --}}
        <table class="bottom-table">
            <tr>
                <td width="33%" style="vertical-align: bottom; padding: 6px 8px 10px 8px;">
                    <div class="net-pay-box">
                        <div class="net-pay-title">Net Salary Payable</div>
                        <div class="net-pay-amt">₹ {{ number_format($salary->net_salary ?? 0, 2) }}</div>
                    </div>
                </td>
                <td width="27%" style="vertical-align: bottom; padding: 6px 8px 10px 8px;">
                    @if(!empty($salary->bank_name) || !empty($salary->account_number))
                        <div class="bank-details">
                            <b>Bank:</b> {{ $salary->bank_name ?? '-' }}<br>
                            <b>A/c:</b> {{ $salary->account_number ?? '-' }}<br>
                            <b>IFSC:</b> {{ $salary->ifsc_code ?? '-' }}
                        </div>
                    @endif
                </td>
                <td width="40%" style="vertical-align: bottom; padding: 6px 8px 10px 8px;">
                    <table width="100%" style="border-collapse: collapse;">
                        <tr>
                            <td class="sign-spacer" style="width: 46%;">&nbsp;</td>
                            <td class="sign-spacer" style="width: 8%;">&nbsp;</td>
                            <td class="sign-spacer" style="width: 46%;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="sign-cell" style="width: 46%;">
                                Employee Signature
                            </td>
                            <td style="width: 8%;">&nbsp;</td>
                            <td class="sign-cell" style="width: 46%;">
                                Authorised Signatory
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- Between Slip 1 and Slip 2 on the same page --}}
    @if($loop->iteration % 2 == 1 && !$loop->last)
        <div class="cut-line">
            ✂ - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ✂
        </div>
    @elseif($loop->iteration % 2 == 0 && !$loop->last)
        <div class="page-break"></div>
    @endif
@endforeach

</body>
</html>

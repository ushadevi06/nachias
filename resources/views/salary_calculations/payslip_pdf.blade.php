<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip-{{ $salary->salary_month }}-{{ $salary->salary_year }}-{{ Str::slug($salary->name) }}-{{ $salary->emp_id }}</title>
    <style>
        @page {
            size: A4;
            margin: 18px;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #2d3748;
            background: #f4f6f9;
        }
        * {
            box-sizing: border-box;
        }
        .table-wrapper {
            width: 100%;
            border: 1px solid #d6dce5;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
        }
        .header-section {
            background-color: #84a1db;
            color: #ffffff;
            padding: 18px 20px;
            border-bottom: 4px solid #84a1db;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            background-color: #84a1db;
            color: #ffffff;
            vertical-align: top;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .company-details {
            font-size: 11px;
            line-height: 1.5;
        }

        .payslip-title {
            text-align: right;
            font-size: 20px;
            font-weight: bold;
            line-height: 1.6;
        }
        .employee-section {
            padding: 15px 20px;
            background: #f8fafc;
            /* border-bottom: 1px solid #e2e8f0; */
        }
        .contribution-section {
            padding: 15px 20px;
            background: #ffffff;
            /* border-bottom: 1px solid #e2e8f0; */
        }
        .employee-table {
            width: 100%;
            border-collapse: collapse;
        }
        .employee-table td {
            padding: 8px 4px;
            font-size: 11px;
            color: #808080;
        }
        .employee-table b {
            color: #808080;
        }
        .salary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .salary-table th {
            background: #84a1db;
            color: #ffffff;
            padding: 10px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .salary-table td {
            padding: 9px 10px;
            border-bottom: 1px solid #edf2f7;
            font-size: 11px;
            color: #808080;
        }
        .salary-table tr:nth-child(even) {
            background: #f8fafc;
        }
        .deduction-table {
            width: 100%;
            border-collapse: collapse;
        }
        .deduction-table th {
            background: #84a1db;
            color: #ffffff;
            padding: 10px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .deduction-table td {
            padding: 9px 10px;
            border-bottom: 1px solid #edf2f7;
            font-size: 11px;
            color: #808080;
        }
        .deduction-table tr:nth-child(even) {
            background: #f8fafc;
        }
        .right {
            text-align: right;
        }
        .center {
            text-align: center;
        }
        .bold {
            font-weight: bold;
        }
        .gross-row {
            background: #84a1db !important;
            font-weight: bold;
        }

        .gross-row td {
            color: #ffffff !important;
        }
        .netpay-section {
            margin: 20px;
            background-color: #84a1db;
            color: #ffffff;
            border-radius: 10px;
            padding: 16px;
            text-align: center;
        }
        .netpay-label {
            font-size: 14px;
            margin-bottom: 6px;
        }
        .netpay-amount {
            font-size: 28px;
            font-weight: bold;
        }
        .footer {
            padding: 20px;
            font-size: 10px;
            color: #64748b;
        }
        .signature-table {
            width: 100%;
            margin-top: 35px;
        }
        .signature-table td {
            font-size: 11px;
        }
    </style>
</head>
<body>
<div class="table-wrapper">
    {{-- HEADER --}}
    <div class="header-section">
        <table class="header-table">
            <tr>
                <td width="60%">
                    <div class="company-name">
                        {{ $setting->company_name ?? env('WEBSITE_NAME') }}
                    </div>
                    <div class="company-details">
                        {{ $setting->address ?? '-' }}
                        <br>
                        <b>Phone :</b> +91 {{ $setting->phone_number ?? '-' }}
                        <br>
                        <b>Email :</b> {{ $setting->email ?? '-' }}
                    </div>
                </td>
                <td width="40%">
                    <div class="payslip-title">
                        Payslip
                        <br>
                        {{ \Carbon\Carbon::create()->month($salary->salary_month)->format('F') }}
                        {{ $salary->salary_year }}
                    </div>
                </td>
            </tr>
        </table>
    </div>
    {{-- EMPLOYEE DETAILS --}}
    <div class="employee-section">
        <table class="employee-table">
            <tr>
                <td>
                    <b>Emp Name: </b> {{ $salary->name }}
                </td>
                <td>
                    <b>Department: </b> {{ $salary->department_name }}
                </td>
                <td>
                    <b>ESI No: </b> {{ $salary->esi_no }}
                </td>
            </tr>
            <tr>
                <td>
                    <b>Emp Code: </b> {{ $salary->emp_id }}
                </td>
                <td>
                    <b>Designation: </b> {{ $salary->role_name }}
                </td>
                <td>
                    <b>PF No: </b> {{ $salary->pf_no }}
                </td>
            </tr>
            <tr>
                <td>
                    <b>Total Days: </b> {{ $totalDays ?? '-' }}
                </td>
                <td>
                    <b>Working Days: {{ $salary->present_days }}</b> 
                </td>
                <td>
                    <b>Holidays: </b> {{ $salary->holidays }}
                </td>
            </tr>
            <tr>
                <td>
                    <b>LOP Days: {{ $salary->absent_days }}</b> 
                </td>
                <td>
                    <b>On time: </b> {{ $onTimeDays ?? '-' }}
                </td>
                <td>
                    <b>Late Days: </b> {{ $lateDays ?? '-' }}
                </td>
            </tr>
        </table>
    </div>
    {{-- SALARY TABLE --}}
    <table width="100%" style="border-collapse: collapse;">
        <tr>
            {{-- EARNINGS --}}
            <td width="50%" valign="top">
                <table class="salary-table">
                    <thead>
                        <tr>
                            <th>Earnings</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Basic Salary</td>
                            <td class="right">
                                ₹ {{ number_format($salary->basic_salary, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td>HRA</td>
                            <td class="right">
                                ₹ {{ number_format($salary->hra, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td>DA</td>
                            <td class="right">
                                ₹ {{ number_format($salary->da, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td>Other Allowance</td>
                            <td class="right">
                                ₹ {{ number_format($salary->oa, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td>OT Amount</td>
                            <td class="right">
                                ₹ {{ number_format($salary->overtime_amount, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td>Incentive</td>
                            <td class="right">
                                ₹ {{ number_format($salary->incentive, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td>Misc</td>
                            <td class="right">
                                ₹ {{ number_format($salary->misc_amount, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td>Bus Fare</td>
                            <td class="right">
                                ₹ {{ number_format($salary->bus_fare, 2) }}
                            </td>
                        </tr>
                        <tr class="gross-row">
                            <td>
                                Total
                            </td>
                            <td class="right">
                                ₹ {{ number_format($salary->gross_salary, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
            {{-- SPACE --}}
            <td width="2%"></td>
            {{-- DEDUCTIONS --}}
            <td width="48%" valign="top">
                <table class="deduction-table">
                    <thead>
                        <tr>
                            <th>Deductions</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>PF</td>
                            <td class="right">
                                ₹ {{ number_format($salary->pf, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td>ESI</td>
                            <td class="right">
                                ₹ {{ number_format($salary->esi, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td>Other Deduction</td>
                            <td class="right">
                                ₹ 0.00
                            </td>
                        </tr>
                        <tr>
                            <td>Sal Advance</td>
                            <td class="right">
                                ₹ {{ number_format($salary->salary_advance, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td>Late Fine</td>
                            <td class="right">
                                ₹ {{ number_format($salary->late_fine, 2) }}
                            </td>
                        </tr>
                        <tr class="gross-row">
                            <td>
                                Total Deductions
                            </td>
                            <td class="right">
                                ₹
                                {{
                                    number_format(
                                        $salary->pf +
                                        $salary->esi +
                                        $salary->salary_advance + $salary->late_fine,
                                        2
                                    )
                                }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
    <div class="contribution-section">
        <table class="employee-table" width="100%">
            <tr>
                {{-- LEFT --}}
                <td width="50%" valign="top">
                    <table width="100%">
                        <tr>
                            <td>
                                <b>Gross Pay:</b>
                            </td>
                            <td class="right">
                                ₹ {{ number_format($salary->gross_salary, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Total Deductions:</b>
                            </td>
                            <td class="right">
                                ₹
                                {{
                                    number_format(
                                        $salary->pf +
                                        $salary->esi +
                                        $salary->salary_advance,
                                        2
                                    )
                                }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-top:10px;border-top:1px dotted #808080;">
                                <b>Net Pay: </b>
                            </td>
                            <td class="right" style="padding-top:10px;border-top:1px dotted #808080;">
                                <b>₹ {{ number_format($salary->net_salary, 2) }}</b>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="2%"></td>
                {{-- RIGHT --}}
                <td width="48%" valign="top">
                    <table width="100%">
                        {{-- TITLE --}}
                        <tr>
                            <td colspan="2"
                                style="
                                    padding-bottom:10px;
                                    font-weight:bold;
                                    border-bottom:1px solid #dbe3f1;
                                ">
                                Company Contribution
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-top:10px;">
                                <b>PF:</b>
                            </td>
                            <td class="right" style="padding-top:10px;">
                                @php $company_pf = ($salary->pf / 12) * 12; @endphp
                                ₹ {{ number_format($company_pf, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>ESI:</b>
                            </td>
                            <td class="right">
                                @php $company_esi = ($salary->esi / 0.75) * 3.25; @endphp
                                ₹ {{ number_format($company_esi, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-top:8px;">
                                <b>Total:</b>
                            </td>
                            <td class="right" style="padding-top:8px;">
                                <b>
                                    ₹ {{ number_format($salary->pf + $salary->esi, 2) }}
                                </b>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    {{-- FOOTER --}}
    <div class="footer">
        <table class="signature-table">
            <tr>
                <td class="left">                    
                </td>
                <td class="right">
                    Authorized Signature
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
@if(isset($is_print) && $is_print)
<script>
    window.onload = function() {
        window.print();
    }
</script>
@endif

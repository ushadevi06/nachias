<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $meta['report_title'] ?? 'HO Cutting Section Average Report' }}</title>
    <style>
        @page {
            size: landscape;
            margin: 6mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            font-size: 8px;
            color: #1e293b;
            margin: 0;
            padding: 8px;
            background: #fff;
        }

        .report-header-banner {
            background-color: #eef2ff !important;
            border: 1px solid #c7d2fe !important;
            padding: 8px 12px;
            border-radius: 6px;
            text-align: center;
            margin-bottom: 8px;
        }

        .report-header-banner h2 {
            margin: 0;
            font-size: 13px;
            font-weight: 800;
            color: #1e1b4b;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .meta-bar {
            display: flex;
            justify-content: space-between;
            font-size: 8px;
            color: #64748b;
            margin-bottom: 6px;
            font-weight: 600;
        }

        table.cutting-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5px;
            margin-bottom: 12px;
            page-break-inside: auto;
        }

        table.cutting-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        table.cutting-table th,
        table.cutting-table td {
            border: 1px solid #475569 !important;
            padding: 3px 2px;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }

        .bg-peach {
            background-color: #fce4d6 !important;
            color: #000 !important;
        }

        .bg-yellow {
            background-color: #ffc000 !important;
            color: #000 !important;
        }

        .bg-light-yellow {
            background-color: #fff2cc !important;
            color: #000 !important;
        }

        .bg-dark-yellow {
            background-color: #e6ac00 !important;
            color: #000 !important;
        }

        .bg-total-row {
            background-color: #fef08a !important;
            font-weight: bold !important;
        }

        .bg-avg-row {
            background-color: #e2e8f0 !important;
            font-weight: bold !important;
        }

        .bg-highlight {
            background-color: #f8fafc !important;
            font-weight: bold !important;
        }

        .kpi-container {
            width: 480px;
            max-width: 100%;
            margin-top: 10px;
            page-break-inside: avoid;
        }

        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            border: 1px solid #475569;
        }

        .kpi-table th,
        .kpi-table td {
            border: 1px solid #475569 !important;
            padding: 3px 6px;
        }

        .kpi-header {
            background-color: #1e40af !important;
            color: #ffffff !important;
            font-weight: bold;
            font-size: 9px;
            text-align: left;
        }

        .text-start {
            text-align: left !important;
        }

        .text-center {
            text-align: center !important;
        }

        .text-end {
            text-align: right !important;
        }

        .fw-bold {
            font-weight: bold !important;
        }

        .text-success {
            color: #16a34a !important;
        }

        .text-danger {
            color: #dc2626 !important;
        }

        .text-primary {
            color: #1d4ed8 !important;
        }

        .badge-ot {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 3px;
            font-weight: bold;
            background-color: #dcfce7 !important;
            color: #15803d !important;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <!-- <div class="no-print" style="margin-bottom: 8px; display: flex; justify-content: flex-end; gap: 6px;">
        <button onclick="window.print()" style="background: #2563eb; color: #fff; border: none; padding: 5px 12px; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 11px;">
            🖨️ Print / Save as PDF
        </button>
        <button onclick="window.close()" style="background: #e2e8f0; color: #334155; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 11px;">
            Close
        </button>
    </div> -->

    <!-- Header Banner -->
    <div class="report-header-banner">
        <h2>{{ $meta['report_title'] ?? 'HO CUTTING SECTION AVERAGE REPORT' }}</h2>
    </div>

    <div class="meta-bar">
        <span><strong>Period:</strong> {{ $meta['report_period'] ?? 'Current' }} @if(!empty($unitName)) |
        <strong>Unit:</strong> {{ $unitName }} @endif</span>
        <span><strong>Generated:</strong> {{ date('d-m-Y H:i:s') }}</span>
    </div>

    <!-- Main Cutting Section Average Table -->
    <table class="cutting-table">
        <thead>
            <!-- Header Row 1 -->
            <tr>
                <th rowspan="2" class="bg-peach fw-bold" style="min-width: 55px;">DATE</th>
                <th colspan="{{ count($cuttingEmployees) + 1 }}" class="bg-yellow fw-bold">CUTTING MASTER</th>
                <th rowspan="2" class="bg-light-yellow fw-bold" style="min-width: 50px;">CUTTING BUNDLEING QTY</th>
                <th rowspan="2" class="bg-light-yellow fw-bold" style="min-width: 45px;">FUSING QTY</th>
                <th rowspan="2" class="bg-light-yellow fw-bold" style="min-width: 40px;">LOGO QTY</th>
                <th colspan="{{ count($cuttingPlants) + 1 }}" class="bg-yellow fw-bold">CUTTING ISSUE</th>
                <th rowspan="2" class="bg-peach fw-bold" style="min-width: 50px;">EFFICIENCY</th>
                <th rowspan="2" class="bg-peach fw-bold" style="min-width: 40px;">1hr OT</th>
                <th rowspan="2" class="bg-peach fw-bold" style="min-width: 50px;">TARGET PER DAY</th>
            </tr>
            <!-- Header Row 2 -->
            <tr>
                @foreach($cuttingEmployees as $emp)
                    <th class="bg-yellow fw-bold" title="{{ $emp->name }}">{{ strtoupper($emp->name) }}</th>
                @endforeach
                <th class="bg-dark-yellow fw-bold">TOTAL QTY</th>
                @foreach($cuttingPlants as $plant)
                    <th class="bg-yellow fw-bold" title="{{ $plant->name }}">{{ strtoupper($plant->code ?: $plant->name) }}
                    </th>
                @endforeach
                <th class="bg-dark-yellow fw-bold">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    <td class="fw-bold">{{ $row['date_clean'] ?? strip_tags($row['date']) }}</td>
                    @foreach($cuttingEmployees as $emp)
                        <td>{{ $row['emp_' . $emp->id] ?? '-' }}</td>
                    @endforeach
                    <td class="bg-highlight">{{ $row['master_total_clean'] ?? strip_tags($row['master_total']) }}</td>
                    <td>{{ $row['cutting_bundleing_qty'] ?? '-' }}</td>
                    <td>{{ $row['fusing_qty'] ?? '-' }}</td>
                    <td>{{ $row['logo_qty'] ?? '-' }}</td>
                    @foreach($cuttingPlants as $plant)
                        <td>{{ $row['plant_' . $plant->id] ?? '-' }}</td>
                    @endforeach
                    <td class="bg-highlight">{{ $row['issue_total_clean'] ?? strip_tags($row['issue_total']) }}</td>
                    <td
                        class="fw-bold {{ is_numeric($row['efficiency_val']) && $row['efficiency_val'] >= 100 ? 'text-success' : (is_numeric($row['efficiency_val']) && $row['efficiency_val'] >= 75 ? 'text-primary' : '') }}">
                        {{ $row['efficiency_clean'] ?? (is_numeric($row['efficiency_val']) && $row['efficiency_val'] > 0 ? $row['efficiency_val'] . '%' : '-') }}
                    </td>
                    <td>
                        @if(($row['ot_val'] ?? '') === 'Yes' || strpos(($row['ot_clean'] ?? strip_tags($row['ot'])), 'Yes') !== false)
                            <span class="badge-ot">Yes</span>
                        @else
                            <span style="color: #94a3b8;">No</span>
                        @endif
                    </td>
                    <td>{{ $row['target_per_day'] ?? '2,000' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <!-- TOTAL Row -->
            <tr class="bg-total-row">
                <td class="fw-bold">TOTAL</td>
                @foreach($cuttingEmployees as $emp)
                    <td>{{ $totalRow['emp_' . $emp->id] ?? '-' }}</td>
                @endforeach
                <td class="text-primary">{{ $totalRow['master_total'] ?? '-' }}</td>
                <td>{{ $totalRow['cutting_bundleing_qty'] ?? '-' }}</td>
                <td>{{ $totalRow['fusing_qty'] ?? '-' }}</td>
                <td>{{ $totalRow['logo_qty'] ?? '-' }}</td>
                @foreach($cuttingPlants as $plant)
                    <td>{{ $totalRow['plant_' . $plant->id] ?? '-' }}</td>
                @endforeach
                <td class="text-primary">{{ $totalRow['issue_total'] ?? '-' }}</td>
                <td class="text-success">{{ $totalRow['efficiency'] ?? '-' }}</td>
                <td>{{ $totalRow['ot'] ?? '-' }}</td>
                <td>{{ $totalRow['target_per_day'] ?? '-' }}</td>
            </tr>
            <!-- AVERAGE Row -->
            <tr class="bg-avg-row">
                <td class="fw-bold">AVERAGE</td>
                @foreach($cuttingEmployees as $emp)
                    <td>{{ $avgRow['emp_' . $emp->id] ?? '-' }}</td>
                @endforeach
                <td>{{ $avgRow['master_total'] ?? '-' }}</td>
                <td>{{ $avgRow['cutting_bundleing_qty'] ?? '-' }}</td>
                <td>{{ $avgRow['fusing_qty'] ?? '-' }}</td>
                <td>{{ $avgRow['logo_qty'] ?? '-' }}</td>
                @foreach($cuttingPlants as $plant)
                    <td>{{ $avgRow['plant_' . $plant->id] ?? '-' }}</td>
                @endforeach
                <td>{{ $avgRow['issue_total'] ?? '-' }}</td>
                <td class="text-success">{{ $avgRow['efficiency'] ?? '-' }}</td>
                <td>{{ $avgRow['ot'] ?? '-' }}</td>
                <td>{{ $avgRow['target_per_day'] ?? '-' }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Executive KPI Summary Card Table -->
    <div class="kpi-container">
        <table class="kpi-table">
            <thead>
                <tr>
                    <th colspan="4" class="kpi-header">
                        Performance &amp; Target Summary ({{ $meta['report_period'] ?? 'Current Period' }})
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th class="bg-light text-start fw-bold" style="width: 45%;">Monthly Target</th>
                    <td colspan="3" class="text-end fw-bold">{{ $meta['monthly_target'] ?? '0' }}</td>
                </tr>
                <tr class="text-center fw-bold" style="background-color: #f1f5f9;">
                    <td class="text-start">Timing</td>
                    <td>9 to 6</td>
                    <td>9 to 7</td>
                    <td>Total</td>
                </tr>
                <tr>
                    <th class="bg-light text-start fw-bold">Days Worked</th>
                    <td class="text-center">{{ $meta['timing_reg_days'] ?? 0 }}</td>
                    <td class="text-center">{{ $meta['timing_ot_days'] ?? 0 }}</td>
                    <td class="text-center fw-bold">{{ $meta['timing_total_days'] ?? 0 }}</td>
                </tr>
                <tr>
                    <th class="bg-light text-start fw-bold">Target for Days worked</th>
                    <td class="text-center">{{ $meta['target_reg_days'] ?? '0' }}</td>
                    <td class="text-center">{{ $meta['target_ot_days'] ?? '0' }}</td>
                    <td class="text-center fw-bold text-primary">{{ $meta['target_total_days'] ?? '0' }}</td>
                </tr>
                <tr>
                    <th class="bg-light text-start fw-bold">Actual</th>
                    <td colspan="3" class="text-end fw-bold text-success">{{ $meta['actual_issue'] ?? '0' }}</td>
                </tr>
                <tr>
                    <th class="bg-light text-start fw-bold">Loss</th>
                    <td colspan="3"
                        class="text-end fw-bold {{ !empty($meta['loss_is_negative']) ? 'text-danger' : 'text-success' }}">
                        {{ $meta['loss'] ?? '0' }}
                    </td>
                </tr>
                <tr>
                    <th class="bg-light text-start fw-bold">Average</th>
                    <td colspan="3" class="text-end fw-bold">{{ $meta['average'] ?? '0' }}</td>
                </tr>
                <tr>
                    <th class="bg-light text-start fw-bold">Efficiency</th>
                    <td colspan="3" class="text-end fw-bold" style="color: #0284c7;">{{ $meta['efficiency'] ?? '0%' }}
                    </td>
                </tr>
                <tr>
                    <th class="bg-light text-start fw-bold">Days Backward</th>
                    <td colspan="3" class="text-end fw-bold" style="color: #d97706;">{{ $meta['days_backward'] ?? '0' }}
                    </td>
                </tr>
                <tr>
                    <th class="bg-light text-start fw-bold">Target Per day to achieve Monthly Target</th>
                    <td colspan="3" class="text-end fw-bold text-primary">
                        {{ $meta['target_per_day_to_achieve'] ?? '0' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if(!empty($isPrint))
        <script>
            window.addEventListener('DOMContentLoaded', function () {
                setTimeout(function () {
                    window.print();
                }, 300);
            });
        </script>
    @endif
</body>

</html>
@php
    $totalCols = 1 + count($cuttingEmployees) + 1 + 3 + count($cuttingPlants) + 1 + 3;
@endphp
<table>
    <thead>
        <!-- Report Title Banner -->
        <tr>
            <th colspan="{{ $totalCols }}" style="font-size: 13px; font-weight: bold; text-align: center; vertical-align: middle; background-color: #e0e7ff; color: #1e1b4b; height: 35px; border: 1px solid #000000;">
                {{ $meta['report_title'] ?? 'HO CUTTING SECTION AVERAGE REPORT' }}
            </th>
        </tr>
        <!-- Main Table Multi-Tier Headers Row 1 -->
        <tr>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #fce4d6;">DATE</th>
            <th colspan="{{ count($cuttingEmployees) + 1 }}" style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #ffc000;">CUTTING MASTER</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #fff2cc;">CUTTING BUNDLEING QTY</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #fff2cc;">FUSING QTY</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #fff2cc;">LOGO QTY</th>
            <th colspan="{{ count($cuttingPlants) + 1 }}" style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #ffc000;">CUTTING ISSUE</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #fce4d6;">EFFICIENCY</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #fce4d6;">1hr OT</th>
            <th rowspan="2" style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #fce4d6;">TARGET PER DAY</th>
        </tr>
        <!-- Main Table Multi-Tier Headers Row 2 -->
        <tr>
            @foreach($cuttingEmployees as $emp)
                <th style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #ffc000;">
                    {{ strtoupper($emp->name) }}
                </th>
            @endforeach
            <th style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #e6ac00;">TOTAL QTY</th>
            @foreach($cuttingPlants as $plant)
                <th style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #ffc000;">
                    {{ strtoupper($plant->code ?: $plant->name) }}
                </th>
            @endforeach
            <th style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #e6ac00;">TOTAL</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
            <tr>
                <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000;">
                    {{ $row['date_clean'] ?? strip_tags($row['date']) }}
                </td>
                @foreach($cuttingEmployees as $emp)
                    <td style="text-align: center; vertical-align: middle; border: 1px solid #000000;">
                        {{ $row['emp_' . $emp->id] ?? '-' }}
                    </td>
                @endforeach
                <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #f8fafc;">
                    {{ $row['master_total_clean'] ?? strip_tags($row['master_total']) }}
                </td>
                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000;">
                    {{ $row['cutting_bundleing_qty'] ?? '-' }}
                </td>
                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000;">
                    {{ $row['fusing_qty'] ?? '-' }}
                </td>
                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000;">
                    {{ $row['logo_qty'] ?? '-' }}
                </td>
                @foreach($cuttingPlants as $plant)
                    <td style="text-align: center; vertical-align: middle; border: 1px solid #000000;">
                        {{ $row['plant_' . $plant->id] ?? '-' }}
                    </td>
                @endforeach
                <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #f8fafc;">
                    {{ $row['issue_total_clean'] ?? strip_tags($row['issue_total']) }}
                </td>
                <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000;">
                    {{ $row['efficiency_clean'] ?? (is_numeric($row['efficiency_val']) && $row['efficiency_val'] > 0 ? $row['efficiency_val'] . '%' : '-') }}
                </td>
                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000;">
                    {{ $row['ot_val'] ?? ($row['ot_clean'] ?? strip_tags($row['ot'])) }}
                </td>
                <td style="text-align: center; vertical-align: middle; border: 1px solid #000000;">
                    {{ $row['target_per_day'] ?? '2,000' }}
                </td>
            </tr>
        @endforeach

        <!-- TOTAL Row -->
        <tr style="background-color: #fef08a; font-weight: bold;">
            <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #fef08a;">TOTAL</td>
            @foreach($cuttingEmployees as $emp)
                <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #fef08a;">
                    {{ $totalRow['emp_' . $emp->id] ?? '-' }}
                </td>
            @endforeach
            <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #fef08a;">
                {{ $totalRow['master_total'] ?? '-' }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #fef08a;">
                {{ $totalRow['cutting_bundleing_qty'] ?? '-' }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #fef08a;">
                {{ $totalRow['fusing_qty'] ?? '-' }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #fef08a;">
                {{ $totalRow['logo_qty'] ?? '-' }}
            </td>
            @foreach($cuttingPlants as $plant)
                <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #fef08a;">
                    {{ $totalRow['plant_' . $plant->id] ?? '-' }}
                </td>
            @endforeach
            <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #fef08a;">
                {{ $totalRow['issue_total'] ?? '-' }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #fef08a;">
                {{ $totalRow['efficiency'] ?? '-' }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #fef08a;">
                {{ $totalRow['ot'] ?? '-' }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #fef08a;">
                {{ $totalRow['target_per_day'] ?? '-' }}
            </td>
        </tr>

        <!-- AVERAGE Row -->
        <tr style="background-color: #e2e8f0; font-weight: bold;">
            <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #e2e8f0;">AVERAGE</td>
            @foreach($cuttingEmployees as $emp)
                <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #e2e8f0;">
                    {{ $avgRow['emp_' . $emp->id] ?? '-' }}
                </td>
            @endforeach
            <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #e2e8f0;">
                {{ $avgRow['master_total'] ?? '-' }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #e2e8f0;">
                {{ $avgRow['cutting_bundleing_qty'] ?? '-' }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #e2e8f0;">
                {{ $avgRow['fusing_qty'] ?? '-' }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #e2e8f0;">
                {{ $avgRow['logo_qty'] ?? '-' }}
            </td>
            @foreach($cuttingPlants as $plant)
                <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #e2e8f0;">
                    {{ $avgRow['plant_' . $plant->id] ?? '-' }}
                </td>
            @endforeach
            <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #e2e8f0;">
                {{ $avgRow['issue_total'] ?? '-' }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #e2e8f0;">
                {{ $avgRow['efficiency'] ?? '-' }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #e2e8f0;">
                {{ $avgRow['ot'] ?? '-' }}
            </td>
            <td style="font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000000; background-color: #e2e8f0;">
                {{ $avgRow['target_per_day'] ?? '-' }}
            </td>
        </tr>

        <!-- Spacing Rows -->
        <tr><td colspan="{{ $totalCols }}"></td></tr>
        <tr><td colspan="{{ $totalCols }}"></td></tr>

        <!-- Executive KPI Performance & Target Summary Table -->
        <tr>
            <th colspan="4" style="background-color: #1e40af; color: #ffffff; font-weight: bold; text-align: left; vertical-align: middle; border: 1px solid #000000; font-size: 11px; height: 26px;">
                Performance &amp; Target Summary ({{ $meta['report_period'] ?? 'Current Period' }})
            </th>
        </tr>
        <tr>
            <th style="background-color: #f1f5f9; font-weight: bold; border: 1px solid #000000; text-align: left;">Monthly Target</th>
            <td colspan="3" style="text-align: right; font-weight: bold; border: 1px solid #000000;">{{ $meta['monthly_target'] ?? '0' }}</td>
        </tr>
        <tr style="background-color: #f8fafc; text-align: center; font-weight: bold;">
            <th style="background-color: #f1f5f9; border: 1px solid #000000; text-align: left;">Timing</th>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">9 to 6</td>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">9 to 7</td>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">Total</td>
        </tr>
        <tr>
            <th style="background-color: #f1f5f9; font-weight: bold; border: 1px solid #000000; text-align: left;">Days Worked</th>
            <td style="border: 1px solid #000000; text-align: center;">{{ $meta['timing_reg_days'] ?? 0 }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $meta['timing_ot_days'] ?? 0 }}</td>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">{{ $meta['timing_total_days'] ?? 0 }}</td>
        </tr>
        <tr>
            <th style="background-color: #f1f5f9; font-weight: bold; border: 1px solid #000000; text-align: left;">Target for Days worked</th>
            <td style="border: 1px solid #000000; text-align: center;">{{ $meta['target_reg_days'] ?? '0' }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $meta['target_ot_days'] ?? '0' }}</td>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold; color: #1d4ed8;">{{ $meta['target_total_days'] ?? '0' }}</td>
        </tr>
        <tr>
            <th style="background-color: #f1f5f9; font-weight: bold; border: 1px solid #000000; text-align: left;">Actual</th>
            <td colspan="3" style="text-align: right; font-weight: bold; color: #16a34a; border: 1px solid #000000;">{{ $meta['actual_issue'] ?? '0' }}</td>
        </tr>
        <tr>
            <th style="background-color: #f1f5f9; font-weight: bold; border: 1px solid #000000; text-align: left;">Loss</th>
            <td colspan="3" style="text-align: right; font-weight: bold; border: 1px solid #000000; {{ !empty($meta['loss_is_negative']) ? 'color: #dc2626;' : 'color: #16a34a;' }}">
                {{ $meta['loss'] ?? '0' }}
            </td>
        </tr>
        <tr>
            <th style="background-color: #f1f5f9; font-weight: bold; border: 1px solid #000000; text-align: left;">Average</th>
            <td colspan="3" style="text-align: right; font-weight: bold; border: 1px solid #000000;">{{ $meta['average'] ?? '0' }}</td>
        </tr>
        <tr>
            <th style="background-color: #f1f5f9; font-weight: bold; border: 1px solid #000000; text-align: left;">Efficiency</th>
            <td colspan="3" style="text-align: right; font-weight: bold; border: 1px solid #000000; color: #0284c7;">{{ $meta['efficiency'] ?? '0%' }}</td>
        </tr>
        <tr>
            <th style="background-color: #f1f5f9; font-weight: bold; border: 1px solid #000000; text-align: left;">Days Backward</th>
            <td colspan="3" style="text-align: right; font-weight: bold; border: 1px solid #000000; color: #d97706;">{{ $meta['days_backward'] ?? '0' }}</td>
        </tr>
        <tr>
            <th style="background-color: #f1f5f9; font-weight: bold; border: 1px solid #000000; text-align: left;">Target Per day to achieve Monthly Target</th>
            <td colspan="3" style="text-align: right; font-weight: bold; border: 1px solid #000000; color: #1d4ed8;">{{ $meta['target_per_day_to_achieve'] ?? '0' }}</td>
        </tr>
    </tbody>
</table>

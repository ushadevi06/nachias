<table>
    <thead>
        <!-- Title Row -->
        <tr>
            <th colspan="8" style="font-weight: bold; font-size: 14pt; text-align: center; background-color: #591a75; color: #ffffff; height: 35px; vertical-align: middle;">
                {{ $title ?? 'CASINO PURCHASE ORDER CONSOLIDATED REPORT' }}
            </th>
        </tr>
        @if(!empty($dateRange))
        <tr>
            <th colspan="8" style="font-style: italic; text-align: center; background-color: #f3e8f7; color: #333333; height: 22px; vertical-align: middle;">
                Period: {{ $dateRange }}
            </th>
        </tr>
        @endif
        <tr>
            <th colspan="8" style="height: 10px;"></th>
        </tr>

        <!-- Section 1 Header -->
        <tr>
            <th colspan="8" style="font-weight: bold; font-size: 11pt; text-align: left; background-color: #ede9fe; color: #591a75; height: 26px; vertical-align: middle;">
                SECTION 1: PRODUCT GROUP &amp; STYLE SUMMARY
            </th>
        </tr>
        <tr>
            <th style="font-weight: bold; text-align: center; background-color: #e2e8f0; border: 1px solid #cbd5e1; width: 45px;">#</th>
            <th style="font-weight: bold; text-align: left; background-color: #e2e8f0; border: 1px solid #cbd5e1; width: 220px;">PRODUCT GROUP</th>
            <th style="font-weight: bold; text-align: center; background-color: #e2e8f0; border: 1px solid #cbd5e1; width: 75px;">WIDTH</th>
            <th style="font-weight: bold; text-align: right; background-color: #e2e8f0; border: 1px solid #cbd5e1; width: 160px;">PLAIN METERS</th>
            <th style="font-weight: bold; text-align: right; background-color: #e2e8f0; border: 1px solid #cbd5e1; width: 160px;">PRINT METERS</th>
            <th style="font-weight: bold; text-align: right; background-color: #e2e8f0; border: 1px solid #cbd5e1; width: 160px;">CHECKED METERS</th>
            <th style="font-weight: bold; text-align: right; background-color: #e2e8f0; border: 1px solid #cbd5e1; width: 160px;">STRIPED METERS</th>
            <th style="font-weight: bold; text-align: right; background-color: #e2e8f0; border: 1px solid #cbd5e1; width: 170px;">TOTAL METERS</th>
        </tr>
    </thead>
    <tbody>
        @php $sno = 1; @endphp
        @foreach($summaryData as $row)
        @php
            $plainM = floatval(str_replace(',', '', (string)($row['plain'] ?? 0)));
            $printM = floatval(str_replace(',', '', (string)($row['print'] ?? 0)));
            $checkedM = floatval(str_replace(',', '', (string)($row['checked'] ?? 0)));
            $stripedM = floatval(str_replace(',', '', (string)($row['striped'] ?? 0)));
            $totalM = floatval(str_replace(',', '', (string)($row['total'] ?? 0)));
        @endphp
        <tr>
            <td style="text-align: center; border: 1px solid #e2e8f0;">{{ $sno++ }}</td>
            <td style="font-weight: 600; text-align: left; border: 1px solid #e2e8f0;">{{ $row['brand_name'] ?? '-' }}</td>
            <td style="text-align: center; border: 1px solid #e2e8f0;">{{ !empty($row['width']) ? $row['width'] . '"' : '-' }}</td>
            <td style="text-align: right; border: 1px solid #e2e8f0;">
                {{ number_format($plainM, 2) }}
                @if(!empty($row['plain_count'])) ({{ $row['plain_count'] }}) @endif
            </td>
            <td style="text-align: right; border: 1px solid #e2e8f0;">
                {{ number_format($printM, 2) }}
                @if(!empty($row['print_count'])) ({{ $row['print_count'] }}) @endif
            </td>
            <td style="text-align: right; border: 1px solid #e2e8f0;">
                {{ number_format($checkedM, 2) }}
                @if(!empty($row['checked_count'])) ({{ $row['checked_count'] }}) @endif
            </td>
            <td style="text-align: right; border: 1px solid #e2e8f0;">
                {{ number_format($stripedM, 2) }}
                @if(!empty($row['striped_count'])) ({{ $row['striped_count'] }}) @endif
            </td>
            <td style="font-weight: bold; text-align: right; border: 1px solid #e2e8f0; background-color: #faf5ff;">
                {{ number_format($totalM, 2) }}
                @if(!empty($row['total_count'])) ({{ $row['total_count'] }}) @endif
            </td>
        </tr>
        @endforeach

        <!-- Summary Total Row -->
        @php
            $totPlainM = floatval(str_replace(',', '', (string)($summaryTotals['plain'] ?? 0)));
            $totPrintM = floatval(str_replace(',', '', (string)($summaryTotals['print'] ?? 0)));
            $totCheckedM = floatval(str_replace(',', '', (string)($summaryTotals['checked'] ?? 0)));
            $totStripedM = floatval(str_replace(',', '', (string)($summaryTotals['striped'] ?? 0)));
            $totTotalM = floatval(str_replace(',', '', (string)($summaryTotals['total'] ?? 0)));
        @endphp
        <tr style="font-weight: bold; background-color: #f1f5f9;">
            <td colspan="3" style="text-align: right; border: 1px solid #cbd5e1; font-weight: bold;">SUMMARY TOTAL</td>
            <td style="text-align: right; border: 1px solid #cbd5e1; font-weight: bold;">
                {{ number_format($totPlainM, 2) }}
                @if(!empty($summaryTotals['plain_count'])) ({{ $summaryTotals['plain_count'] }}) @endif
            </td>
            <td style="text-align: right; border: 1px solid #cbd5e1; font-weight: bold;">
                {{ number_format($totPrintM, 2) }}
                @if(!empty($summaryTotals['print_count'])) ({{ $summaryTotals['print_count'] }}) @endif
            </td>
            <td style="text-align: right; border: 1px solid #cbd5e1; font-weight: bold;">
                {{ number_format($totCheckedM, 2) }}
                @if(!empty($summaryTotals['checked_count'])) ({{ $summaryTotals['checked_count'] }}) @endif
            </td>
            <td style="text-align: right; border: 1px solid #cbd5e1; font-weight: bold;">
                {{ number_format($totStripedM, 2) }}
                @if(!empty($summaryTotals['striped_count'])) ({{ $summaryTotals['striped_count'] }}) @endif
            </td>
            <td style="font-weight: bold; text-align: right; background-color: #e2e8f0; border: 1px solid #cbd5e1;">
                {{ number_format($totTotalM, 2) }}
                @if(!empty($summaryTotals['total_count'])) ({{ $summaryTotals['total_count'] }}) @endif
            </td>
        </tr>

        <!-- Spacing Rows -->
        <tr><td colspan="8" style="height: 20px;"></td></tr>
        <tr>
            <td colspan="8" style="font-weight: bold; font-size: 11pt; text-align: left; background-color: #ede9fe; color: #591a75; height: 26px; vertical-align: middle;">
                SECTION 2: STYLE &amp; PR RANGE DETAILED BREAKDOWN
            </td>
        </tr>
        <tr><td colspan="8" style="height: 10px;"></td></tr>

        <!-- Section 2 PR Range Breakdowns -->
        @if(empty($breakdownData))
        <tr>
            <td colspan="8" style="text-align: center; color: #64748b; font-style: italic;">No PR range breakdown records found.</td>
        </tr>
        @else
            @foreach($breakdownData as $section)
            @php
                $secMtr = floatval(str_replace(',', '', (string)($section['total_meters'] ?? 0)));
                $secWidth = !empty($section['width']) ? '(' . $section['width'] . '")' : '';
            @endphp
            <tr>
                <td colspan="8" style="font-weight: bold; font-size: 10pt; text-align: left; background-color: #f8fafc; color: #1e293b; border-bottom: 2px solid #591a75; height: 24px; vertical-align: middle;">
                    &gt; {{ $section['brand_name'] }} {{ $secWidth }} - {{ $section['style_name'] }} METERS BREAKDOWN (Total: {{ number_format($secMtr, 2) }} MTR | {{ $section['total_designs'] ?? 0 }} Designs)
                </td>
            </tr>
            <tr>
                <th style="font-weight: bold; text-align: center; background-color: #f1f5f9; border: 1px solid #cbd5e1;">#</th>
                <th colspan="3" style="font-weight: bold; text-align: left; background-color: #f1f5f9; border: 1px solid #cbd5e1;">PR RANGE (Rate Slab)</th>
                <th colspan="2" style="font-weight: bold; text-align: center; background-color: #f1f5f9; border: 1px solid #cbd5e1;">DESIGN COUNT</th>
                <th colspan="2" style="font-weight: bold; text-align: right; background-color: #f1f5f9; border: 1px solid #cbd5e1;">METERS</th>
            </tr>
            @php $subSno = 1; @endphp
            @foreach($section['rows'] as $slabRow)
            @php
                $slabMtr = floatval(str_replace(',', '', (string)($slabRow['meters'] ?? ($slabRow['raw_meters'] ?? 0))));
            @endphp
            <tr>
                <td style="text-align: center; border: 1px solid #e2e8f0;">{{ $subSno++ }}</td>
                <td colspan="3" style="font-weight: 500; text-align: left; border: 1px solid #e2e8f0;">{{ $slabRow['pr_range'] }}</td>
                <td colspan="2" style="text-align: center; border: 1px solid #e2e8f0;">{{ $slabRow['design_count'] ?? 0 }}</td>
                <td colspan="2" style="text-align: right; border: 1px solid #e2e8f0; font-weight: {{ $slabMtr > 0 ? 'bold' : 'normal' }};">
                    {{ number_format($slabMtr, 2) }}
                </td>
            </tr>
            @endforeach
            <tr style="font-weight: bold; background-color: #f8fafc;">
                <td colspan="4" style="text-align: right; border: 1px solid #cbd5e1;">SUBTOTAL</td>
                <td colspan="2" style="text-align: center; border: 1px solid #cbd5e1;">{{ $section['total_designs'] ?? 0 }}</td>
                <td colspan="2" style="text-align: right; border: 1px solid #cbd5e1; background-color: #f1f5f9;">{{ number_format($secMtr, 2) }}</td>
            </tr>
            <tr><td colspan="8" style="height: 12px;"></td></tr>
            @endforeach
        @endif
    </tbody>
</table>

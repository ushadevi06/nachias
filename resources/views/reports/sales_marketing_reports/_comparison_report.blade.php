<div class="card-datatable table-responsive">
    <table class="datatables-products table table-hover" id="comparisonReportTable">
        <thead class="bg-light">
            <tr>
                <th>Month</th>
                <th class="text-end">Sales Year {{ $comparisonReport[0]['prev_year'] ?? '2025' }}</th>
                <th class="text-end">Sales Year {{ $comparisonReport[0]['curr_year'] ?? '2026' }}</th>
                <th class="text-center">Growth %</th>
            </tr>
        </thead>
        <tbody>
            @if($comparisonReport && count($comparisonReport) > 0)
            @foreach($comparisonReport as $data)
            <tr>
                <td class="fw-bold">{{ $data['month_name'] }}</td>
                <td class="text-end">₹{{ number_format($data['prev_year_sales'], 2) }}</td>
                <td class="text-end fw-bold text-primary">₹{{ number_format($data['curr_year_sales'], 2) }}</td>
                <td class="text-center">
                    @if($data['growth_pc'] > 0)
                        <span class="text-success fw-bold"><i class="ri ri-arrow-up"></i> {{ number_format($data['growth_pc'], 1) }}%</span>
                    @elseif($data['growth_pc'] < 0)
                        <span class="text-danger fw-bold"><i class="ri ri-arrow-down"></i> {{ number_format(abs($data['growth_pc']), 1) }}%</span>
                    @else
                        <span class="text-muted small">0%</span>
                    @endif
                </td>
            </tr>
            @endforeach
            @endif
        </tbody>
        <tfoot class="bg-light fw-bold">
            <tr>
                <td>Annual Total:</td>
                <td class="text-end">₹{{ number_format(array_sum(array_column($comparisonReport, 'prev_year_sales')), 2) }}</td>
                <td class="text-end text-primary">₹{{ number_format(array_sum(array_column($comparisonReport, 'curr_year_sales')), 2) }}</td>
                <td class="text-center">
                    @php
                        $totalPrev = array_sum(array_column($comparisonReport, 'prev_year_sales'));
                        $totalCurr = array_sum(array_column($comparisonReport, 'curr_year_sales'));
                        $totalGrowth = $totalPrev > 0 ? (($totalCurr - $totalPrev) / $totalPrev) * 100 : ($totalCurr > 0 ? 100 : 0);
                    @endphp
                    @if($totalGrowth > 0)
                        <span class="badge bg-label-success rounded-pill">+{{ number_format($totalGrowth, 1) }}%</span>
                    @elseif($totalGrowth < 0)
                        <span class="badge bg-label-danger rounded-pill">{{ number_format($totalGrowth, 1) }}%</span>
                    @else
                        -
                    @endif
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="card-datatable">
    <table class="datatables-products table table-hover">
        <thead class="bg-light">
            <tr>
                <th>Zone</th>
                <th>Sales Executive</th>
                <th class="text-end">Total Sales</th>
                <th class="text-center">Incentive %</th>
                <th class="text-end">Incentive Amount</th>
            </tr>
        </thead>
        <tbody>
            @if($incentiveReport && count($incentiveReport) > 0)
                @foreach($incentiveReport as $data) 
                <tr>
                    <td><span class="fw-bold text-primary">{{ $data['zone'] }}</span></td>
                    <td>{{ $data['agent_name'] }} <small class="text-muted">({{ $data['agent_code'] }})</small></td>
                    <td class="text-end fw-bold">₹{{ number_format($data['total_sales'], 2) }}</td>
                    <td class="text-center">
                        <span class="badge bg-label-info rounded-pill">{{ number_format($data['incentive_pc'], 2) }}%</span>
                    </td>
                    <td class="text-end text-success fw-bold">₹{{ number_format($data['incentive_amt'], 2) }}</td>
                </tr>
                @endforeach
            @endif
        </tbody>
        @if(count($incentiveReport) > 0)
        <tfoot class="bg-light fw-bold">
            <tr>
                <td></td>
                <td class="text-end">Grand Total:</td>
                <td class="text-end text-primary">₹{{ number_format(array_sum(array_column($incentiveReport, 'total_sales')), 2) }}</td>
                <td></td>
                <td class="text-end text-success">₹{{ number_format(array_sum(array_column($incentiveReport, 'incentive_amt')), 2) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

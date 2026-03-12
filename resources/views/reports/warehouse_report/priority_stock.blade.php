<div class="card-datatable">
    <table class="datatables-products table table-hover">
        <thead>
            <tr>
                <th>Article No</th>
                <th>Ageing (Days)</th>
                <th class="text-center">Current Stock</th>
                <th class="text-end">Value</th>
                <th class="text-center">Priority</th>
            </tr>
        </thead>
        <tbody>
            @foreach($priorityStock as $data)
            <tr>
                <td><strong>{{ $data->art_no }}</strong></td>
                <td class="{{ $data->ageing_days > 120 ? 'text-danger fw-bold' : '' }}">
                    {{ $data->ageing_days }} Days
                </td>
                <td class="text-center">{{ number_format($data->current_stock, 2) }}</td>
                <td class="text-end">₹{{ number_format($data->stock_value, 2) }}</td>
                <td class="text-center">
                    @if($data->ageing_days > 120)
                        <span class="badge bg-danger rounded-pill shadow-sm">Critical</span>
                    @else
                        <span class="badge bg-warning rounded-pill shadow-sm">High</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="card-datatable">
    <table class="datatables-products table table-hover">
        <thead>
            <tr>
                <th>Brand</th>
                <th>Category</th>
                <th class="text-center">Sold Qty</th>
                <th class="text-end">Sales Value</th>
                <th class="text-center">Trend</th>
            </tr>
        </thead>
        <tbody>
            @if($brandwiseSales->count() > 0)
                @foreach($brandwiseSales as $sale)
                    <tr>
                        <td><strong>{{ $sale->brand ?? '-' }}</strong></td>
                        <td>{{ $sale->category ?? '-' }}</td>
                        <td class="text-center">{{ number_format($sale->sold_qty, 0) }}</td>
                        <td class="text-end fw-bold">₹{{ number_format($sale->sales_value, 2) }}</td>
                        <td class="text-center {{ $sale->trend >= 0 ? 'text-success' : 'text-danger' }}">
                            <i class="ri {{ $sale->trend >= 0 ? 'ri-arrow-up-line' : 'ri-arrow-down-line' }} me-1"></i>
                            {{ number_format(abs($sale->trend), 1) }}%
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

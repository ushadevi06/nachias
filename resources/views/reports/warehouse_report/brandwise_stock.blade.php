<div class="card-datatable">
    <table id="brandwiseStockTable" class="datatables-products table table-hover">
        <thead>
            <tr>
                <th>Brand</th>
                <th>Article No</th>
                <th class="text-center">Sets Available</th>
                <th class="text-center">Items per Set</th>
                <th class="text-center">Total Qty</th>
            </tr>
        </thead>
        <tbody>
            @if($brandwiseStock && $brandwiseStock->count() > 0)
                @foreach($brandwiseStock as $stock)
                <tr>
                    <td><strong>{{ $stock->brand }}</strong></td>
                    <td>{{ $stock->article_no }}</td>
                    <td class="text-center">{{ number_format($stock->sets_available, 0) }}</td>
                    <td class="text-center">{{ $stock->items_per_set }}</td>
                    <td class="text-center fw-bold">{{ number_format($stock->total_qty, 0) }}</td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

<div class="card-datatable">
    <table class="datatables-products table table-hover">
        <thead>
            <tr>
                <th>Store</th>
                <th>Item Name</th>
                <th>Size</th>
                <th class="text-center">Stock Qty</th>
            </tr>
        </thead>
        <tbody>
            @if($assortedStock && $assortedStock->count() > 0)
                @foreach($assortedStock as $stock)
                <tr>
                    <td>{{ $stock->store }}</td>
                    <td><strong>{{ $stock->item_name }}</strong></td>
                    <td>{{ $stock->size ?: '-' }}</td>
                    <td class="text-center fw-bold text-primary">{{ number_format($stock->stock_qty, 0) }}</td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

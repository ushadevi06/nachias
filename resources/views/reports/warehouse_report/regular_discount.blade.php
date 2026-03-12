<div class="card-datatable">
    <table class="datatables-products table table-hover">
        <thead>
            <tr>
                <th>Period</th>
                <th class="text-end">Regular Sales</th>
                <th class="text-end">Discount Sales</th>
                <th class="text-center">Discount %</th>
                <th class="text-end">Net Revenue</th>
            </tr>
        </thead>
        <tbody>
            @foreach($regularDiscount as $data)
            <tr>
                <td>{{ $data->period }}</td>
                <td class="text-end">₹{{ number_format($data->regular_sales, 2) }}</td>
                <td class="text-end text-danger">₹{{ number_format($data->discount_sales, 2) }}</td>
                <td class="text-center">{{ number_format($data->discount_pc, 1) }}%</td>
                <td class="text-end fw-bold">₹{{ number_format($data->net_revenue, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

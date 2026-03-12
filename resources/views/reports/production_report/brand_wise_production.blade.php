<div class="card-datatable table-responsive">
    <table class="datatables-products table table-hover">
        <thead>
            <tr>
                <th>Brand</th>
                <th>Style Name</th>
                <th>Sleeve Type</th>
                <th class="text-center">Produced Qty</th>
                <th>Unit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($brandWiseProduction as $row)
            <tr>
                <td><strong>{{ $row['brand'] }}</strong></td>
                <td>{{ $row['style'] }}</td>
                <td>{{ $row['sleeve'] }}</td>
                <td class="text-center fw-bold">{{ number_format($row['qty']) }}</td>
                <td>{{ $row['unit'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

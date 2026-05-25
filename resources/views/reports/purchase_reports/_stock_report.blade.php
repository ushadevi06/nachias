<div class="table-responsive">
    <table class="table premium-table mb-0 datatables-stock">
        <thead>
            @if($isFabric)
            <tr>
                <th>MATERIAL NAME</th>
                <th>BRAND</th>
                <th>STYLE</th>
                <th>COLOR</th>
                <th>FABRIC TYPE</th>
                <th>WIDTH</th>
                <th>OPENING METERS</th>
                <th>INWARD METERS</th>
                <th>OUTWARD METERS</th>
                <th>CLOSING METERS</th>
                <th>CLOSING COST (₹)</th>
            </tr>
            @else
            <tr>
                <th>ITEM NAME</th>
                <th>OPENING QTY</th>
                <th>INWARD QTY</th>
                <th>OUTWARD QTY</th>
                <th>CLOSING QTY</th>
                <th>CLOSING COST (₹)</th>
            </tr>
            @endif
        </thead>
        <tbody>
            @foreach($stockData as $stock)
            <tr>
                @if($isFabric)
                    <td>{{ $stock['item_name'] }}</td>
                    <td>{{ $stock['brand'] }}</td>
                    <td>{{ $stock['style'] }}</td>
                    <td>{{ $stock['color'] }}</td>
                    <td>{{ $stock['fabric_type'] }}</td>
                    <td>{{ $stock['width'] }}</td>
                    <td class="text-end">{{ number_format($stock['opening'], 2) }}</td>
                    <td class="text-end text-success fw-semibold">{{ number_format($stock['inward'], 2) }}</td>
                    <td class="text-end text-danger fw-semibold">{{ number_format($stock['outward'], 2) }}</td>
                    <td class="text-end fw-bold">{{ number_format($stock['closing'], 2) }}</td>
                    <td class="text-end fw-bold text-primary">₹ {{ number_format($stock['closing_cost'], 2) }}</td>
                @else
                    <td>{{ $stock['item_name'] }}</td>
                    <td class="text-end">{{ number_format($stock['opening'], 2) }}</td>
                    <td class="text-end text-success fw-semibold">{{ number_format($stock['inward'], 2) }}</td>
                    <td class="text-end text-danger fw-semibold">{{ number_format($stock['outward'], 2) }}</td>
                    <td class="text-end fw-bold">{{ number_format($stock['closing'], 2) }}</td>
                    <td class="text-end fw-bold text-primary">₹ {{ number_format($stock['closing_cost'], 2) }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            @if(count($stockData) > 0)
            <tr class="fw-bold" style="background: #f1f5f9;">
                @if($isFabric)
                    <td colspan="6" class="text-end">TOTAL</td>
                    <td class="text-end">{{ number_format(collect($stockData)->sum('opening'), 2) }}</td>
                    <td class="text-end text-success">{{ number_format(collect($stockData)->sum('inward'), 2) }}</td>
                    <td class="text-end text-danger">{{ number_format(collect($stockData)->sum('outward'), 2) }}</td>
                    <td class="text-end">{{ number_format(collect($stockData)->sum('closing'), 2) }}</td>
                    <td class="text-end text-primary">₹ {{ number_format(collect($stockData)->sum('closing_cost'), 2) }}</td>
                @else
                    <td colspan="1" class="text-end">TOTAL</td>
                    <td class="text-end">{{ number_format(collect($stockData)->sum('opening'), 2) }}</td>
                    <td class="text-end text-success">{{ number_format(collect($stockData)->sum('inward'), 2) }}</td>
                    <td class="text-end text-danger">{{ number_format(collect($stockData)->sum('outward'), 2) }}</td>
                    <td class="text-end">{{ number_format(collect($stockData)->sum('closing'), 2) }}</td>
                    <td class="text-end text-primary">₹ {{ number_format(collect($stockData)->sum('closing_cost'), 2) }}</td>
                @endif
            </tr>
            @endif
        </tfoot>
    </table>
</div>
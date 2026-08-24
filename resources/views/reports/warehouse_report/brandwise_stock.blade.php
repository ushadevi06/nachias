<div class="card-datatable table-responsive">
    <table id="brandwiseStockTable" class="datatables-products table table-hover">
        <thead class="table-light">
            <tr>
                <th class="fw-bold">BRAND</th>
                <th class="text-center fw-bold">STOCK</th>
                <th class="text-end fw-bold">STOCK VALUE</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($brandwiseStock) && $brandwiseStock->count() > 0)
                @foreach($brandwiseStock as $stock)
                {{-- Clickable row to navigate to the details page --}}
                <tr style="cursor: pointer;" onclick="window.location.href='{{ url('warehouse_reports/brandwise_stock_details') }}/{{ $stock->brand_id ?? 0 }}'">
                    <td class="text-uppercase"><strong>{{ $stock->brand ?? '-' }}</strong></td>
                    <td class="text-center">{{ number_format($stock->total_qty ?? 0, 0) }}</td>
                    <td class="text-end fw-bold">₹{{ number_format($stock->stock_value ?? 0, 2) }}</td>
                </tr>
                @endforeach
            @else
                {{-- Dummy Data for UI Preview --}}
                <tr style="cursor: pointer;" onclick="window.location.href='#'" class="align-middle">
                    <td class="text-uppercase"><strong>CASINO BRAVO</strong></td>
                    <td class="text-center">6,047</td>
                    <td class="text-end fw-bold">₹3,003,452.00</td>
                </tr>
                <tr style="cursor: pointer;" onclick="window.location.href='#'" class="align-middle">
                    <td class="text-uppercase"><strong>CASINO BRAVO CORE</strong></td>
                    <td class="text-center">1,212</td>
                    <td class="text-end fw-bold">₹481,395.01</td>
                </tr>
                <tr style="cursor: pointer;" onclick="window.location.href='#'" class="align-middle bg-light">
                    <td class="text-uppercase"><strong>CASINO DEAL</strong></td>
                    <td class="text-center">1,714</td>
                    <td class="text-end fw-bold">₹928,607.00</td>
                </tr>
                <tr style="cursor: pointer;" onclick="window.location.href='#'" class="align-middle">
                    <td class="text-uppercase"><strong>CASINO DHOTI SHIRT CORE</strong></td>
                    <td class="text-center">2,492</td>
                    <td class="text-end fw-bold">₹1,080,023.00</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

<style>
    /* Styling for hover effect to indicate it's clickable */
    #brandwiseStockTable tbody tr:hover td {
        background-color: rgba(105, 108, 255, 0.08) !important;
        transition: all 0.2s ease-in-out;
    }
</style>

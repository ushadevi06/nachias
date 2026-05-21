<div class="table-responsive">
    <table class="table premium-table mb-0 datatable">
        <thead>
            <tr>
                @if($isFabric)
                    <th>MATERIAL NAME</th>
                    <th>BRAND</th>
                    <th>STYLE</th>
                    <th>COLOR</th>
                    <th>FABRIC TYPE</th>
                    <th>WIDTH</th>
                @else
                    <th>ITEM NAME</th>
                @endif
                <th>0-30 DAYS</th>
                <th>31-60 DAYS</th>
                <th>61-90 DAYS</th>
                <th>91+ DAYS</th>
                <th>TOTAL STOCK</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $total_0_30 = 0;
                $total_31_60 = 0;
                $total_61_90 = 0;
                $total_91_plus = 0;
                $grand_total = 0;
            @endphp
            @forelse($ageingData as $row)
                @php 
                    $total_0_30 += $row['0_30'];
                    $total_31_60 += $row['31_60'];
                    $total_61_90 += $row['61_90'];
                    $total_91_plus += $row['91_plus'];
                    $grand_total += $row['total'];
                @endphp
                <tr>
                    @if($isFabric)
                        <td>{{ $row['item_name'] }}</td>
                        <td>{{ $row['brand'] }}</td>
                        <td>{{ $row['style'] }}</td>
                        <td>{{ $row['color'] }}</td>
                        <td>{{ $row['fabric_type'] }}</td>
                        <td>{{ $row['width'] }}</td>
                    @else
                        <td>{{ $row['item_name'] }}</td>
                    @endif
                    <td class="text-center font-weight-bold">{{ number_format($row['0_30'], 2) }}</td>
                    <td class="text-center font-weight-bold">{{ number_format($row['31_60'], 2) }}</td>
                    <td class="text-center font-weight-bold">{{ number_format($row['61_90'], 2) }}</td>
                    <td class="text-center font-weight-bold">{{ number_format($row['91_plus'], 2) }}</td>
                    <td class="text-center font-weight-bold text-primary">{{ number_format($row['total'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        @if(count($ageingData) > 0)
        <tfoot>
            <tr class="bg-light font-weight-bold">
                <td colspan="{{ $isFabric ? 6 : 1 }}" class="text-right">TOTAL</td>
                <td class="text-center">{{ number_format($total_0_30, 2) }}</td>
                <td class="text-center">{{ number_format($total_31_60, 2) }}</td>
                <td class="text-center">{{ number_format($total_61_90, 2) }}</td>
                <td class="text-center">{{ number_format($total_91_plus, 2) }}</td>
                <td class="text-center text-primary">{{ number_format($grand_total, 2) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

<script>
    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#ageing-report .datatable')) {
            $('#ageing-report .datatable').DataTable({
                "pageLength": 25,
                "ordering": true,
                "info": true,
                "searching": true,
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                "language": {
                    "search": "",
                    "searchPlaceholder": "Search records..."
                },
                "buttons": [
                    { extend: 'excel', title: 'Stock Ageing Report', className: 'd-none' },
                    { extend: 'pdf', title: 'Stock Ageing Report', className: 'd-none' },
                    { extend: 'print', title: 'Stock Ageing Report', className: 'd-none' }
                ]
            });
        }
    });
</script>

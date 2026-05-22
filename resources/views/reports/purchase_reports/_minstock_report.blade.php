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
                <th class="text-center">MIN STOCK REQ.</th>
                <th class="text-center">CURRENT STOCK</th>
                <th class="text-center">SHORTAGE</th>
                <th class="text-center">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($minStockData as $row)
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
                    <td class="text-center font-weight-bold text-dark">{{ number_format($row['min_stock'], 2) }}</td>
                    <td class="text-center font-weight-bold {{ $row['closing'] <= 0 ? 'text-danger' : 'text-warning' }}">{{ number_format($row['closing'], 2) }}</td>
                    <td class="text-center font-weight-bold text-danger">{{ number_format($row['shortage'], 2) }}</td>
                    <td class="text-center">
                        @if($row['closing'] <= 0)
                            <span class="badge bg-label-danger">Out of Stock</span>
                        @else
                            <span class="badge bg-label-warning">Low Stock</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#minstock-report .datatable')) {
            $('#minstock-report .datatable').DataTable().destroy();
        }
        $('#minstock-report .datatable').DataTable({
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
                    { extend: 'excel', title: 'Minimum Stock Report', className: 'd-none' },
                    { extend: 'pdf', title: 'Minimum Stock Report', className: 'd-none' },
                    { extend: 'print', title: 'Minimum Stock Report', className: 'd-none' }
                ]
            });
    });
</script>

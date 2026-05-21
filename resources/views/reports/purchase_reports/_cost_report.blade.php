<div class="table-responsive">
    <table class="table premium-table mb-0 datatable">
        <thead>
            <tr>
                <th>ITEM NAME</th>
                <th class="text-center">TOTAL PURCHASED QTY</th>
                <th class="text-end">TOTAL AMOUNT (₹)</th>
                <th class="text-end text-primary">AVERAGE COST (₹)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($costData as $row)
                <tr>
                    <td>{{ $row['item_name'] }}</td>
                    <td class="text-center">{{ number_format($row['total_qty'], 2) }}</td>
                    <td class="text-end">{{ number_format($row['total_amount'], 2) }}</td>
                    <td class="text-end text-primary font-weight-bold">{{ number_format($row['average_cost'], 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center py-4 text-muted">No data available in table</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#cost-report .datatable')) {
            $('#cost-report .datatable').DataTable({
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
                    { extend: 'excel', title: 'Average Cost Report', className: 'd-none' },
                    { extend: 'pdf', title: 'Average Cost Report', className: 'd-none' },
                    { extend: 'print', title: 'Average Cost Report', className: 'd-none' }
                ]
            });
        }
    });
</script>

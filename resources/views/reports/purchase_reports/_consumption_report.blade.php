<div class="table-responsive">
    <table class="table premium-table mb-0 datatable">
        <thead>
            <tr>
                <th>DATE</th>
                <th>JOB CARD NO</th>
                <th>BRAND</th>
                <th class="text-center">TOTAL GARMENTS (QTY)</th>
                <th class="text-center">FABRIC USED (MTR)</th>
                <th class="text-center">AVG CONSUMPTION (MTR/PC)</th>
                <th class="text-center">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $sum_garments = 0;
                $sum_fabric = 0;
            @endphp
            @forelse($consumptionData as $row)
                @php 
                    $sum_garments += $row['total_garments'];
                    $sum_fabric += $row['total_fabric'];
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row['date'])->format('d-m-Y') }}</td>
                    <td class="font-weight-bold text-primary">{{ $row['job_card_no'] }}</td>
                    <td>{{ $row['brand'] }}</td>
                    <td class="text-center">{{ number_format($row['total_garments'], 2) }}</td>
                    <td class="text-center">{{ number_format($row['total_fabric'], 2) }}</td>
                    <td class="text-center font-weight-bold">{{ number_format($row['average'], 3) }}</td>
                    <td class="text-center">
                        <span class="badge bg-label-{{ strtolower($row['status']) == 'completed' ? 'success' : 'primary' }}">
                            {{ ucfirst($row['status']) }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
        @if(count($consumptionData) > 0)
        <tfoot>
            <tr class="bg-light font-weight-bold">
                <td colspan="3" class="text-right">TOTAL</td>
                <td class="text-center text-primary">{{ number_format($sum_garments, 2) }}</td>
                <td class="text-center text-primary">{{ number_format($sum_fabric, 2) }}</td>
                <td class="text-center text-primary">
                    {{ $sum_garments > 0 ? number_format($sum_fabric / $sum_garments, 3) : '0.000' }}
                </td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

<script>
    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#consumption-report .datatable')) {
            $('#consumption-report .datatable').DataTable({
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
                    { extend: 'excel', title: 'Job Card Consumption Report', className: 'd-none' },
                    { extend: 'pdf', title: 'Job Card Consumption Report', className: 'd-none' },
                    { extend: 'print', title: 'Job Card Consumption Report', className: 'd-none' }
                ]
            });
        }
    });
</script>

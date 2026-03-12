<div class="card-datatable table-responsive">
    <table class="datatables-products table table-hover">
        <thead>
            <tr>
                <th>Job Card No</th>
                <th>Service Name</th>
                <th>Employee</th>
                <th>Stage</th>
                <th class="text-center">Assigned Qty</th>
                <th class="text-center">Completed Qty</th>
                <th class="text-center">Pending Qty</th>
                <th class="text-center">Efficiency</th>
            </tr>
        </thead>
        <tbody>
            @foreach($performanceIndividual as $perf)
            <tr>
                <td><strong>{{ $perf['job_card_no'] }}</strong></td>
                <td>{{ $perf['service'] }}</td>
                <td>{{ $perf['employee'] }}</td>
                <td>{{ $perf['stage'] }}</td>
                <td class="text-center">{{ $perf['assigned_qty'] }}</td>
                <td class="text-center text-success">{{ $perf['completed_qty'] }}</td>
                <td class="text-center text-danger">{{ $perf['pending_qty'] }}</td>
                <td class="text-center">
                    @php
                        $efficiency = $perf['efficiency'];
                        $badgeClass = 'bg-label-danger';
                        if ($efficiency >= 90) $badgeClass = 'bg-label-success';
                        elseif ($efficiency >= 70) $badgeClass = 'bg-label-warning';
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $efficiency }}%</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

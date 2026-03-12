<div class="card-datatable">
    <table class="datatables-products table table-hover">
        <thead>
            <tr>
                <th>Dispatch Date</th>
                <th>Order No</th>
                <th>Party Name</th>
                <th>Place</th>
                <th>Transporter/Vehicle</th>
                <th>LR No</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dispatchReport as $data)
            <tr>
                <td>{{ $data->so_date->format('d-m-Y') }}</td>
                <td><strong>{{ $data->so_no }}</strong></td>
                <td>{{ $data->customer->name ?? 'N/A' }}</td>
                <td>{{ $data->customer->city->city_name ?? 'N/A' }}</td>
                <td>{{ $data->transporter_name ?? ($data->dispatch_through ?? 'N/A') }}</td>
                <td>{{ $data->lr_no ?? 'N/A' }}</td>
                <td class="text-center">
                    <span class="badge bg-label-success">{{ $data->status }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

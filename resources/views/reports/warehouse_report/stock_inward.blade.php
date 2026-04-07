<div class="card-datatable">
    <table class="datatables-products table table-hover">
        <thead>
            <tr>
                <th>GRN No</th>
                <th>Supplier</th>
                <th>Inward Date</th>
                <th class="text-center">Total Items</th>
                <th class="text-center">QC Status</th>
            </tr>
        </thead>
        <tbody>
            @if($stockInward && $stockInward->count() > 0)
                @foreach($stockInward as $data)
                <tr>
                    <td><strong>{{ $data->grn_number }}</strong></td>
                    <td>{{ $data->supplier->name ?? 'N/A' }}</td>
                    <td>{{ $data->grn_date->format('d-M-Y') }}</td>
                    <td class="text-center">{{ number_format($data->total_qty, 2) }}</td>
                    <td class="text-center">
                        @php
                            $badgeClass = 'bg-label-info';
                            if ($data->status == 'Completed') $badgeClass = 'bg-label-success';
                            elseif ($data->status == 'Draft') $badgeClass = 'bg-label-warning';
                        @endphp
                        <span class="badge {{ $badgeClass }} rounded-pill">{{ $data->status }}</span>
                    </td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

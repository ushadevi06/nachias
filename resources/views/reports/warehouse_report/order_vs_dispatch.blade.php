<div class="card-datatable">
    <table class="datatables-products table table-hover">
        <thead>
            <tr>
                <th>Order No</th>
                <th>Customer</th>
                <th class="text-center">Ordered Qty</th>
                <th class="text-center">Dispatched Qty</th>
                <th class="text-center">Pending Qty</th>
                <th class="text-center">Progress</th>
            </tr>
        </thead>
        <tbody>
            @if($orderVsDispatch && $orderVsDispatch->count() > 0)
                @foreach($orderVsDispatch as $data)
                <tr>
                    <td><strong>{{ $data->so_no }}</strong></td>
                    <td>{{ $data->customer->name ?? '-' }}</td>
                    <td class="text-center">{{ number_format($data->ordered_qty, 0) }}</td>
                    <td class="text-center">{{ number_format($data->dispatched_qty, 0) }}</td>
                    <td class="text-center {{ $data->pending_qty > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($data->pending_qty, 0) }}</td>
                    <td>
                        <div class="progress" style="height: 8px;" title="{{ number_format($data->fulfillment_pc, 1) }}%">
                            <div class="progress-bar bg-success" style="width: {{ $data->fulfillment_pc }}%"></div>
                        </div>
                    </td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

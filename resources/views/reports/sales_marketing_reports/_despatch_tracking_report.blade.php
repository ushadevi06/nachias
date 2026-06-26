<div class="card-datatable">
    <div class="table-responsive">
        <table class="datatables-products table premium-table table-hover text-nowrap">
            <thead class="bg-light">
                <tr>
                    <th class="text-center">S.No</th>
                    <th>Order Type</th>
                    <th>Sale Order Date</th>
                    <th>Sales Order No</th>
                    <th>OrderAxe Order No</th>
                    <th>Sales Executive</th>
                    <th>Customer Name</th>
                    <th>Place</th>
                    <th>Zone</th>
                    <th class="text-center">Dhoti Shirts</th>
                    <th class="text-center">White</th>
                    <th class="text-center">Core</th>
                    <th class="text-center">Bravo</th>
                    <th class="text-center">Deal</th>
                    <th class="text-center">Formal</th>
                    <th class="text-center fw-bold text-dark">Total Qty</th>
                    <th>Requested Delivery Date</th>
                    <th>Status</th>
                    <th class="text-center text-success fw-bold">Despatch Qty</th>
                    <th class="text-center text-danger fw-bold">Pending Qty</th>
                    <th>Partial D.Date</th>
                    <th>Despatch Complete Date</th>
                    <th>Reason for Delayed Delivery</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $index => $order)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $order->order_type ?? 'Regular' }}</td>
                    <td>{{ $order->so_date ? $order->so_date->format('d-m-Y') : '-' }}</td>
                    <td class="fw-bold text-primary">{{ $order->so_no }}</td>
                    <td>{{ $order->order_no ?? '-' }}</td>
                    <td>{{ $order->salesAgent->name ?? '-' }}</td>
                    <td>
                        <div class="fw-bold text-dark">{{ $order->customer->name ?? '-' }}</div>
                        <small class="text-muted">{{ $order->customer->code ?? '' }}</small>
                    </td>
                    <td>{{ optional(optional($order->customer)->city)->city_name ?? '-' }}</td>
                    <td>{{ $order->zone->zone_name ?? '-' }}</td>
                    <td class="text-center">{{ $order->dhoti_qty > 0 ? $order->dhoti_qty : '' }}</td>
                    <td class="text-center">{{ $order->white_qty > 0 ? $order->white_qty : '' }}</td>
                    <td class="text-center">{{ $order->core_qty > 0 ? $order->core_qty : '' }}</td>
                    <td class="text-center">{{ $order->bravo_qty > 0 ? $order->bravo_qty : '' }}</td>
                    <td class="text-center">{{ $order->deal_qty > 0 ? $order->deal_qty : '' }}</td>
                    <td class="text-center">{{ $order->formal_qty > 0 ? $order->formal_qty : '' }}</td>
                    <td class="text-center fw-bold">{{ floatval($order->total_qty) }}</td>
                    <td>
                        {{ $order->delivery_date ? $order->delivery_date->format('d-m-Y') : ($order->request_date ? $order->request_date->format('d-m-Y') : '-') }}
                    </td>
                    <td>
                        @if($order->fulfillment_status == 'Delivered')
                            <span class="badge bg-label-success">Delivered</span>
                        @elseif($order->fulfillment_status == 'Partial Delivery')
                            <span class="badge bg-label-warning">Partial Delivery</span>
                        @else
                            <span class="badge bg-label-primary">Planned</span>
                        @endif
                    </td>
                    <td class="text-center fw-bold text-success">{{ floatval($order->delivered_qty) }}</td>
                    <td class="text-center fw-bold text-danger">{{ floatval($order->pending_qty) }}</td>
                    <td>{{ $order->partial_d_date ?? '-' }}</td>
                    <td>{{ $order->despatch_complete_date ?? '-' }}</td>
                    <td>
                        <span class="text-wrap" style="max-width: 250px; display: inline-block;">
                            {{ $order->delay_reason ?? '-' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
